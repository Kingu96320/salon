<?php
    include_once '../includes/db_include.php';
    $sdate = strtotime(date('Y-m-d'));
    $edate = strtotime(date('Y-m-d'));
    if(isset($_GET['sdate'])){
        $sdate = strtotime($_GET['sdate']);
    }
    if(isset($_GET['edate'])){
        $edate = strtotime($_GET['edate']);
    }
    
    header("Content-Type: application/xls");    
    header("Content-Disposition: attachment; filename=DailyReport_".$sdate.'-'.$edate.".xls"); 
    header("Pragma: no-cache"); 
    header("Expires: 0");

	$branch_id = $_SESSION['branch_id'];
?>

<table border="1">
    <thead>
    <tr>
        <th>Sr. no</th>
        <th style="min-width: 100px!important;">Bill date</th>      
        <th>Service amount</th> 
        <th>Product amount</th>
        <th>Package amount</th>
        <th>Membership amount</th>
        <th>Wallet amount</th>
        <th>Pending amount received</th>
        <th>Appointment advance</th>
        <th>Pending payment</th>
        <th title="Discount included, Tax not included.">Total invoice amount</th>
        <th>Discount</th>
        <th title="(Total invoice amount + Wallet amount + Appointment advance) - Discount">Net sale</th>
        <th>Tax</th>
        <th title="Net sale + Exclusive tax">Grand sale</th>
        <th title="Grand sale - Pending payment - E-wallet - Reward points">Total Collection</th>
        <?php
            $pm = query_by_id("SELECT * FROM payment_method WHERE status = '1'",[],$conn);
            foreach($pm as $pam){
                echo '<th>'.$pam['name'].'</th>';
            }
        ?>
    </tr>
</thead>
<tbody>
    <?php
        $count = 1;
        $service_amount = 0;
        $product_amount = 0;
        $package_amount = 0;
        $membership_amount = 0;
        $wallet_amount = 0;
        $pending_amount_received = 0;
        $appointment_advance = 0;
        $pending_payment = 0;
        $tax_inc = 0;
        $tax_exc = 0;
        $discount_amount = 0;
        $net_sale_amount = 0;
        $grand_sale = 0;
        $total_collaction = 0;
        $total_inv_amount = 0; 
        $payment_1 = 0;
        $payment_3 = 0;
        $payment_4 = 0;
        $payment_5 = 0;
        $payment_6 = 0;
        $payment_7 = 0;
        $payment_9 = 0;
        $payment_10 = 0;
        $payment_11 = 0;

        for($i=$sdate; $i<=$edate; $i+=86400){
            $date = date("Y-m-d", $i);
            $app_total = 0;
            $app_advance = query_by_id("SELECT sum(paid) as total FROM `app_invoice_".$branch_id."` WHERE active='0' AND bill_created_status = '0' AND branch_id='".$branch_id."' AND appdate = '".$date."'",[],$conn)[0];
            if($app_advance['total'] > 0){
                $app_total += $app_advance['total'];
            }
            
            // pending payment received
            
            $pending_payment_in = 0;
            $ppr = query_by_id("SELECT sum(pending_payment_received) as pr FROM invoice_pending_payment WHERE pending_paid_date = '".$date."' AND paid_at_branch='".$branch_id."' and status='1'",[],$conn)[0][pr];
            $pending_payment_in = $ppr;
            ?>
            <tr>
                <td><?= $count ?></td>
                <td><?= my_date_format($date) ?></td>
                <td><?php echo number_format(getsalebytype($date,'Service'),2); $service_amount += getsalebytype($date,'Service'); ?></td>
                <td><?php echo number_format(getsalebytype($date,'Product'),2); $product_amount += getsalebytype($date,'Product') ?></td>
                <td><?php echo number_format(getsalebytype($date,'Package'),2); $package_amount += getsalebytype($date,'Package'); ?></td>
                <td><?php echo number_format(getsalebytype($date,'mem'),2); $membership_amount += getsalebytype($date,'mem'); ?></td>
                <td><?php echo number_format(walletrecharged($date),2); $wallet_amount += walletrecharged($date); ?></td>
                <td><?php echo number_format($pending_payment_in,2); $pending_amount_received += $pending_payment_in; ?></td>
                <td><?php echo number_format($app_total, 2); $appointment_advance += $app_total; ?></td>
                <td><?php $expp = getpendingamount($date); echo number_format($expp,2); $pending_payment += $expp; ?></td>      
                <td><?php 
                echo number_format(getsales($date)+get_discount($date)-taxcalculation($date, 'exclusive'),2); $total_inv_amount+= getsales($date)+get_discount($date)-taxcalculation($date, 'exclusive'); ?></td>              
                <td><?php echo number_format(get_discount($date),2); $discount_amount += get_discount($date); ?></td>
                <td><?php
                    $net = (getsales($date)+$app_total+walletrecharged($date))-taxcalculation($date, 'exclusive'); echo number_format($net,2); $net_sale_amount += $net; ?></td>

                <td><?php taxcalculation($date); $tax_inc += taxcalculation($date, 'inclusive'); $tax_exc += taxcalculation($date, 'exclusive'); ?></td>
                
                <td><?php 
                $grand = getsales($date)+$app_total+walletrecharged($date); echo number_format($grand,2); $grand_sale += $grand; ?></td>
                <td><?php
                 $collaction = (getsales($date))+$pending_payment_in+$app_total+walletrecharged($date)-get_reports('7', $date)-get_reports('9', $date)-getpendingamount($date); echo number_format($collaction,2); $total_collaction += $collaction; ?></td>
                <?php
                    $pm = query_by_id("SELECT * FROM payment_method WHERE status = '1'",[],$conn);
                    foreach($pm as $pam){
                        echo '<td>'.number_format(get_reports($pam['id'], $date),2).'</td>';
                        if($pam['id'] == 1){ $payment_1 += get_reports($pam['id'], $date); }
                        if($pam['id'] == 3){ $payment_3 += get_reports($pam['id'], $date); }
                        if($pam['id'] == 4){ $payment_4 += get_reports($pam['id'], $date); }
                        if($pam['id'] == 5){ $payment_5 += get_reports($pam['id'], $date); }
                        if($pam['id'] == 6){ $payment_6 += get_reports($pam['id'], $date); }
                        if($pam['id'] == 7){ $payment_7 += get_reports($pam['id'], $date); }
                        if($pam['id'] == 9){ $payment_9 += get_reports($pam['id'], $date); }
                        if($pam['id'] == 10){ $payment_10 += get_reports($pam['id'], $date); }
                        if($pam['id'] == 11){ $payment_11 += get_reports($pam['id'], $date); }
                    }
                ?>
            </tr>
            <?php
            $count++;
        }
    ?>
    <tr>
        <td colspan="2" class="text-right"><b>Total</b></td>
        <td><b><?= number_format($service_amount, 2) ?></b></td>
        <td><b><?= number_format($product_amount, 2) ?></b></td>
        <td><b><?= number_format($package_amount, 2) ?></b></td>
        <td><b><?= number_format($membership_amount, 2) ?></b></td>
        <td><b><?= number_format($wallet_amount, 2) ?></b></td>
        <td><b><?= number_format($pending_amount_received, 2) ?></b></td>
        <td><b><?= number_format($appointment_advance, 2) ?></b></td>
        <td><b><?= number_format($pending_payment, 2) ?></b></td>
        <td><b><?= number_format($total_inv_amount, 2) ?></b></td>
        <td><b><?= number_format($discount_amount, 2) ?></b></td>                           
        <td><b><?= number_format($net_sale_amount, 2) ?></b></td>
        <td><b><?php
            echo 'Inclusive : '.number_format($tax_inc, 2)."<br />";
            echo 'Exclusive : '.number_format($tax_exc, 2)."<br />";
        ?></b></td>
        <td><b><?= number_format($grand_sale, 2) ?></b></td>
        <td><b><?= number_format($total_collaction, 2) ?></b></td>
        <td><b><?= number_format($payment_1, 2) ?></b></td>
        <td><b><?= number_format($payment_3, 2) ?></b></td>
        <td><b><?= number_format($payment_4, 2) ?></b></td>
        <td><b><?= number_format($payment_5, 2) ?></b></td>
        <td><b><?= number_format($payment_6, 2) ?></b></td>
        <td><b><?= number_format($payment_7, 2) ?></b></td>
        <td><b><?= number_format($payment_9, 2) ?></b></td>
        <td><b><?= number_format($payment_10, 2) ?></b></td>
        <td><b><?= number_format($payment_11, 2) ?></b></td>
    </tr>
</tbody>
</table>

<?php
    
   function getclient($cid){
        global $conn;
        global $branch_id;
        $sql="SELECT * from client where id=$cid and branch_id='".$branch_id."'";
        $result = mysqli_query($sql,[],$conn)[0];
        if($result) {
            return $result['name'];
        }
    }
    
    function getstaff($sid){
        global $conn;
        global $branch_id;
        $sql="SELECT * FROM `beauticians` where id=$sid and branch_id='".$branch_id."'";
        $result=query_by_id($sql,[],$conn)[0];
        if($result) {
            return $result['name'];
        }
    }
    
    function getcont($cid){
        global $conn;
        global $branch_id;
        $sql="SELECT * from client where id=$cid and branch_id='".$branch_id."'";
        $result=query_by_id($sql,[],$conn)[0];
        if($result) {
            return $result['cont'];
        }
    }
    
    function getitem($id)
    {
        global $conn;
        global $branch_id;
        $str = "";
        $item_id=explode(",",$id)[1];
        $chk_type=explode(",",$id)[0];
        if($chk_type == 'sr'){
            $type = 'Service';
            }else if($chk_type == 'pa'){
            $type = 'Package';
            }else{
            $type = 'Product';
        }
        
        switch ($type) 
        {
            case "Service":
            $sql="SELECT * from `service` where id=$item_id";
            $result=query_by_id($sql,[],$conn);
            foreach($result as $row) 
            {
                $str = $row['name']."(Service)";
            }
            break;
            case "Product":
            $sql="SELECT * from `products` where id=$item_id";
            $result=query_by_id($sql,[],$conn);
            foreach($result as $row) 
            {
                $str = $row['name']."(Product)";
            }
            break;
            case "Package":
            $sql="SELECT * from `packages` where id=$item_id AND branch_id='".$branch_id."'";
            $result=query_by_id($sql,[],$conn);
            foreach($result as $row) {
                $str = $row['name']."(Package)";
            }
            break;
            default:
            $str = "";
            break;
        }
        return $str;
    }
    
    
    function get_reports($type, $date){

        $invquery = "i.doa='$date'";
        $appquery = "ai.appdate='$date'";
        $waquery = "DATE(time_update)='".$date."'";
        $pend_amount = 0;
        global $conn;
        global $branch_id;
        switch ($type) 
        {
            case "1":
            $wallet_amount = 0;
            $sql="SELECT sum(mpm.amount_paid) as total  from `invoice_".$branch_id."` i LEFT JOIN `multiple_payment_method` mpm on mpm.invoice_id = CONCAT('bill',',',i.id) where $invquery and mpm.payment_method='1' and i.active=0 AND i.branch_id='".$branch_id."' AND mpm.branch_id='".$branch_id."'";
            $result=query_by_id($sql,[],$conn)[0];
            $total_inv = ($result['total'] > 0)?$result['total']:0;
            
            $sql="SELECT sum(mpm.amount_paid) as total  from `app_invoice_".$branch_id."` ai LEFT JOIN `multiple_payment_method` mpm on mpm.invoice_id = CONCAT('app',',',ai.id) where $appquery and mpm.payment_method='1' and ai.active=0 AND ai.branch_id='".$branch_id."' AND mpm.branch_id='".$branch_id."'";
            $result=query_by_id($sql,[],$conn)[0];
            $total_app = ($result['total'] > 0)?$result['total']:0;
            
            $amount1 = query_by_id("SELECT paid_amount from `wallet_history` where status=1 and $waquery  and branch_id='".$branch_id."' and iid='0' and transaction_type='1' and payment_method='1'",[],$conn);
            $amount2 = query_by_id("SELECT wallet_amount from `wallet_history` where status=1 and $waquery and branch_id='".$branch_id."' and iid !='0' and transaction_type='1' and payment_method='1'",[],$conn);
            foreach($amount1 as $a1){
                $wallet_amount += $a1['paid_amount'];
            }
            foreach($amount2 as $a2){
                $wallet_amount += $a2['wallet_amount'];
            }

            $pend_amount = query_by_id("SELECT sum(pending_payment_received) as pr FROM invoice_pending_payment WHERE pending_paid_date = '".$date."' AND paid_at_branch='".$branch_id."' and payment_method = '1' and status='1'",[],$conn)[0]['pr'];
            
            $total = $total_inv+$total_app+$wallet_amount+$pend_amount;
            break;
            case "3":
            $wallet_amount = 0;
            $sql="SELECT sum(mpm.amount_paid) as total  from `invoice_".$branch_id."` i LEFT JOIN `multiple_payment_method` mpm on mpm.invoice_id = CONCAT('bill',',',i.id) where $invquery and mpm.payment_method='3' and i.active=0 AND i.branch_id='".$branch_id."' AND mpm.branch_id='".$branch_id."'";
            $result=query_by_id($sql,[],$conn)[0];
            $total_inv = ($result['total'] > 0)?$result['total']:0;
            
            $sql="SELECT sum(mpm.amount_paid) as total  from `app_invoice_".$branch_id."` ai LEFT JOIN `multiple_payment_method` mpm on mpm.invoice_id = CONCAT('app',',',ai.id) where $appquery and mpm.payment_method='3' and ai.active=0 AND ai.branch_id='".$branch_id."' AND mpm.branch_id='".$branch_id."'";
            $result=query_by_id($sql,[],$conn)[0];
            $total_app = ($result['total'] > 0)?$result['total']:0;
            
            $amount1 = query_by_id("SELECT paid_amount from `wallet_history` where status=1 and $waquery  and branch_id='".$branch_id."' and iid='0' and transaction_type='1' and payment_method='3'",[],$conn);
            $amount2 = query_by_id("SELECT wallet_amount from `wallet_history` where status=1 and $waquery and branch_id='".$branch_id."' and iid !='0' and transaction_type='1' and payment_method='3'",[],$conn);
            foreach($amount1 as $a1){
                $wallet_amount += $a1['paid_amount'];
            }
            foreach($amount2 as $a2){
                $wallet_amount += $a2['wallet_amount'];
            }

            $pend_amount = query_by_id("SELECT sum(pending_payment_received) as pr FROM invoice_pending_payment WHERE pending_paid_date = '".$date."' AND paid_at_branch='".$branch_id."' and payment_method = '3' and status='1'",[],$conn)[0]['pr'];
            
            $total = $total_inv+$total_app+$wallet_amount+$pend_amount;
            break;
            case "4":
            $wallet_amount = 0;
            $sql="SELECT sum(mpm.amount_paid) as total  from `invoice_".$branch_id."` i LEFT JOIN `multiple_payment_method` mpm on mpm.invoice_id = CONCAT('bill',',',i.id) where $invquery and mpm.payment_method='4' and i.active=0 AND i.branch_id='".$branch_id."' AND mpm.branch_id='".$branch_id."'";
            $result=query_by_id($sql,[],$conn)[0];
            $total_inv = ($result['total'] > 0)?$result['total']:0;
            
            $sql="SELECT sum(mpm.amount_paid) as total  from `app_invoice_".$branch_id."` ai LEFT JOIN `multiple_payment_method` mpm on mpm.invoice_id = CONCAT('app',',',ai.id) where $appquery and mpm.payment_method='4' and ai.active=0 AND ai.branch_id='".$branch_id."' AND mpm.branch_id='".$branch_id."'";
            $result=query_by_id($sql,[],$conn)[0];
            $total_app = ($result['total'] > 0)?$result['total']:0;
            
            $amount1 = query_by_id("SELECT paid_amount from `wallet_history` where status=1 and $waquery  and branch_id='".$branch_id."' and iid='0' and transaction_type='1' and payment_method='4'",[],$conn);
            $amount2 = query_by_id("SELECT wallet_amount from `wallet_history` where status=1 and $waquery and branch_id='".$branch_id."' and iid !='0' and transaction_type='1' and payment_method='4'",[],$conn);
            foreach($amount1 as $a1){
                $wallet_amount += $a1['paid_amount'];
            }
            foreach($amount2 as $a2){
                $wallet_amount += $a2['wallet_amount'];
            }

            $pend_amount = query_by_id("SELECT sum(pending_payment_received) as pr FROM invoice_pending_payment WHERE pending_paid_date = '".$date."' AND paid_at_branch='".$branch_id."' and payment_method = '4' and status='1'",[],$conn)[0]['pr'];
            
            $total = $total_inv+$total_app+$wallet_amount+$pend_amount;
            break;
            case "5":
            $wallet_amount = 0;
            $sql="SELECT sum(mpm.amount_paid) as total  from `invoice_".$branch_id."` i LEFT JOIN `multiple_payment_method` mpm on mpm.invoice_id = CONCAT('bill',',',i.id) where $invquery and mpm.payment_method='5' and i.active=0 AND i.branch_id='".$branch_id."' AND mpm.branch_id='".$branch_id."'";
            $result=query_by_id($sql,[],$conn)[0];
            $total_inv = ($result['total'] > 0)?$result['total']:0;
            
            $sql="SELECT sum(mpm.amount_paid) as total  from `app_invoice_".$branch_id."` ai LEFT JOIN `multiple_payment_method` mpm on mpm.invoice_id = CONCAT('app',',',ai.id) where $appquery and mpm.payment_method='5' and ai.active=0 AND ai.branch_id='".$branch_id."' AND mpm.branch_id='".$branch_id."'";
            $result=query_by_id($sql,[],$conn)[0];
            $total_app = ($result['total'] > 0)?$result['total']:0;
            
            $amount1 = query_by_id("SELECT paid_amount from `wallet_history` where status=1 and $waquery  and branch_id='".$branch_id."' and iid='0' and transaction_type='1' and payment_method='5'",[],$conn);
            $amount2 = query_by_id("SELECT wallet_amount from `wallet_history` where status=1 and $waquery and branch_id='".$branch_id."' and iid !='0' and transaction_type='1' and payment_method='5'",[],$conn);
            foreach($amount1 as $a1){
                $wallet_amount += $a1['paid_amount'];
            }
            foreach($amount2 as $a2){
                $wallet_amount += $a2['wallet_amount'];
            }

            $pend_amount = query_by_id("SELECT sum(pending_payment_received) as pr FROM invoice_pending_payment WHERE pending_paid_date = '".$date."' AND paid_at_branch='".$branch_id."' and payment_method = '5' and status='1'",[],$conn)[0]['pr'];
            
            $total = $total_inv+$total_app+$wallet_amount+$pend_amount;
            break;
            case "6":
            $wallet_amount = 0;
            $sql="SELECT sum(mpm.amount_paid) as total  from `invoice_".$branch_id."` i LEFT JOIN `multiple_payment_method` mpm on mpm.invoice_id = CONCAT('bill',',',i.id) where $invquery and mpm.payment_method='6' and i.active=0 AND i.branch_id='".$branch_id."' AND mpm.branch_id='".$branch_id."'";
            $result=query_by_id($sql,[],$conn)[0];
            $total_inv = ($result['total'] > 0)?$result['total']:0;
            
            $sql="SELECT sum(mpm.amount_paid) as total  from `app_invoice_".$branch_id."` ai LEFT JOIN `multiple_payment_method` mpm on mpm.invoice_id = CONCAT('app',',',ai.id) where $appquery and mpm.payment_method='6' and ai.active=0 AND ai.branch_id='".$branch_id."' AND mpm.branch_id='".$branch_id."'";
            $result=query_by_id($sql,[],$conn)[0];
            $total_app = ($result['total'] > 0)?$result['total']:0;
            
            $amount1 = query_by_id("SELECT paid_amount from `wallet_history` where status=1 and $waquery  and branch_id='".$branch_id."' and iid='0' and transaction_type='1' and payment_method='6'",[],$conn);
            $amount2 = query_by_id("SELECT wallet_amount from `wallet_history` where status=1 and $waquery and branch_id='".$branch_id."' and iid !='0' and transaction_type='1' and payment_method='6'",[],$conn);
            foreach($amount1 as $a1){
                $wallet_amount += $a1['paid_amount'];
            }
            foreach($amount2 as $a2){
                $wallet_amount += $a2['wallet_amount'];
            }

            $pend_amount = query_by_id("SELECT sum(pending_payment_received) as pr FROM invoice_pending_payment WHERE pending_paid_date = '".$date."' AND paid_at_branch='".$branch_id."' and payment_method = '6' and status='1'",[],$conn)[0]['pr'];
            
            $total = $total_inv+$total_app+$wallet_amount+$pend_amount;
            break;
            case "7":
            $wallet_amount = 0;
            $sql="SELECT sum(mpm.amount_paid) as total  from `invoice_".$branch_id."` i LEFT JOIN `multiple_payment_method` mpm on mpm.invoice_id = CONCAT('bill',',',i.id) where $invquery and mpm.payment_method='7' and i.active=0 AND i.branch_id='".$branch_id."' AND mpm.branch_id='".$branch_id."'";
            $result=query_by_id($sql,[],$conn)[0];
            $total_inv = ($result['total'] > 0)?$result['total']:0;
            
            $sql="SELECT sum(mpm.amount_paid) as total  from `app_invoice_".$branch_id."` ai LEFT JOIN `multiple_payment_method` mpm on mpm.invoice_id = CONCAT('app',',',ai.id) where $appquery and mpm.payment_method='7' and ai.active=0 AND ai.branch_id='".$branch_id."' AND mpm.branch_id='".$branch_id."'";
            $result=query_by_id($sql,[],$conn)[0];
            $total_app = ($result['total'] > 0)?$result['total']:0;
            
            $amount1 = query_by_id("SELECT paid_amount from `wallet_history` where status=1 and $waquery  and branch_id='".$branch_id."' and iid='0' and transaction_type='1' and payment_method='7'",[],$conn);
            $amount2 = query_by_id("SELECT wallet_amount from `wallet_history` where status=1 and $waquery and branch_id='".$branch_id."' and iid !='0' and transaction_type='1' and payment_method='7'",[],$conn);
            foreach($amount1 as $a1){
                $wallet_amount += $a1['paid_amount'];
            }
            foreach($amount2 as $a2){
                $wallet_amount += $a2['wallet_amount'];
            }
            
            $pend_amount = query_by_id("SELECT sum(pending_payment_received) as pr FROM invoice_pending_payment WHERE pending_paid_date = '".$date."' AND paid_at_branch='".$branch_id."' and payment_method = '7' and status='1'",[],$conn)[0]['pr'];

            $total = $total_inv+$total_app+$wallet_amount+$pend_amount;
            break;
            case "9":
            $wallet_amount = 0;
            $sql="SELECT sum(mpm.amount_paid) as total  from `invoice_".$branch_id."` i LEFT JOIN `multiple_payment_method` mpm on mpm.invoice_id = CONCAT('bill',',',i.id) where $invquery and mpm.payment_method='9' and i.active=0 AND i.branch_id='".$branch_id."' AND mpm.branch_id='".$branch_id."'";
            $result=query_by_id($sql,[],$conn)[0];
            $total_inv = ($result['total'] > 0)?$result['total']:0;
            
            $sql="SELECT sum(mpm.amount_paid) as total  from `app_invoice_".$branch_id."` ai LEFT JOIN `multiple_payment_method` mpm on mpm.invoice_id = CONCAT('app',',',ai.id) where $appquery and mpm.payment_method='9' and ai.active=0 AND ai.branch_id='".$branch_id."' AND mpm.branch_id='".$branch_id."'";
            $result=query_by_id($sql,[],$conn)[0];
            $total_app = ($result['total'] > 0)?$result['total']:0;
            
            $amount1 = query_by_id("SELECT paid_amount from `wallet_history` where status=1 and $waquery  and branch_id='".$branch_id."' and iid='0' and transaction_type='1' and payment_method='9'",[],$conn);
            $amount2 = query_by_id("SELECT wallet_amount from `wallet_history` where status=1 and $waquery and branch_id='".$branch_id."' and iid !='0' and transaction_type='1' and payment_method='9'",[],$conn);
            foreach($amount1 as $a1){
                $wallet_amount += $a1['paid_amount'];
            }
            foreach($amount2 as $a2){
                $wallet_amount += $a2['wallet_amount'];
            }

            $pend_amount = query_by_id("SELECT sum(pending_payment_received) as pr FROM invoice_pending_payment WHERE pending_paid_date = '".$date."' AND paid_at_branch='".$branch_id."' and payment_method = '9' and status='1'",[],$conn)[0]['pr'];
            
            $total = $total_inv+$total_app+$wallet_amount+$pend_amount;
            break;
            case "10":
            $wallet_amount = 0;
            $sql="SELECT sum(mpm.amount_paid) as total  from `invoice_".$branch_id."` i LEFT JOIN `multiple_payment_method` mpm on mpm.invoice_id = CONCAT('bill',',',i.id) where $invquery and mpm.payment_method='10' and i.active=0 AND i.branch_id='".$branch_id."' AND mpm.branch_id='".$branch_id."'";
            $result=query_by_id($sql,[],$conn)[0];
            $total_inv = ($result['total'] > 0)?$result['total']:0;
            
            $sql="SELECT sum(mpm.amount_paid) as total  from `app_invoice_".$branch_id."` ai LEFT JOIN `multiple_payment_method` mpm on mpm.invoice_id = CONCAT('app',',',ai.id) where $appquery and mpm.payment_method='10' and ai.active=0 AND ai.branch_id='".$branch_id."' AND mpm.branch_id='".$branch_id."'";
            $result=query_by_id($sql,[],$conn)[0];
            $total_app = ($result['total'] > 0)?$result['total']:0;
            
            $amount1 = query_by_id("SELECT paid_amount from `wallet_history` where status=1 and $waquery  and branch_id='".$branch_id."' and iid='0' and transaction_type='1' and payment_method='10'",[],$conn);
            $amount2 = query_by_id("SELECT wallet_amount from `wallet_history` where status=1 and $waquery and branch_id='".$branch_id."' and iid !='0' and transaction_type='1' and payment_method='10'",[],$conn);
            foreach($amount1 as $a1){
                $wallet_amount += $a1['paid_amount'];
            }
            foreach($amount2 as $a2){
                $wallet_amount += $a2['wallet_amount'];
            }

            $pend_amount = query_by_id("SELECT sum(pending_payment_received) as pr FROM invoice_pending_payment WHERE pending_paid_date = '".$date."' AND paid_at_branch='".$branch_id."' and payment_method = '10' and status='1'",[],$conn)[0]['pr'];
            
            $total = $total_inv+$total_app+$wallet_amount+$pend_amount;
            break;
            case "11":
            $wallet_amount = 0;
            $sql="SELECT sum(mpm.amount_paid) as total  from `invoice_".$branch_id."` i LEFT JOIN `multiple_payment_method` mpm on mpm.invoice_id = CONCAT('bill',',',i.id) where $invquery and mpm.payment_method='11' and i.active=0 AND i.branch_id='".$branch_id."' AND mpm.branch_id='".$branch_id."'";
            $result=query_by_id($sql,[],$conn)[0];
            $total_inv = ($result['total'] > 0)?$result['total']:0;
            
            $sql="SELECT sum(mpm.amount_paid) as total  from `app_invoice_".$branch_id."` ai LEFT JOIN `multiple_payment_method` mpm on mpm.invoice_id = CONCAT('app',',',ai.id) where $appquery and mpm.payment_method='11' and ai.active=0 AND ai.branch_id='".$branch_id."' AND mpm.branch_id='".$branch_id."'";
            $result=query_by_id($sql,[],$conn)[0];
            $total_app = ($result['total'] > 0)?$result['total']:0;
            
            $amount1 = query_by_id("SELECT paid_amount from `wallet_history` where status=1 and $waquery  and branch_id='".$branch_id."' and iid='0' and transaction_type='1' and payment_method='11'",[],$conn);
            $amount2 = query_by_id("SELECT wallet_amount from `wallet_history` where status=1 and $waquery and branch_id='".$branch_id."' and iid !='0' and transaction_type='1' and payment_method='11'",[],$conn);
            foreach($amount1 as $a1){
                $wallet_amount += $a1['paid_amount'];
            }
            foreach($amount2 as $a2){
                $wallet_amount += $a2['wallet_amount'];
            }

            $pend_amount = query_by_id("SELECT sum(pending_payment_received) as pr FROM invoice_pending_payment WHERE pending_paid_date = '".$date."' AND paid_at_branch='".$branch_id."' and payment_method = '11' and status='1'",[],$conn)[0]['pr'];
            
            $total = $total_inv+$total_app+$wallet_amount+$pend_amount;
            break;
            default:
            $total=0;
            break;
        }
        return $total;
    }
    
    
    function checkstaff($sid,$inv){
        global $conn;
        global $branch_id;
        $sql = "SELECT * from invoice_items_".$branch_id." where staffid=$sid and iid=$inv and active=0 and branch_id='".$branch_id."'";
        $result=query_by_id($sql,[],$conn)[0];
        if($result) {
            return 1;
            }else{
            return 0;
        }
    }
    
    function checkproduct($pr,$pid,$inv){
        global $conn;
        global $branch_id;
        if($pr=="pr")
        $pr="Product";
        if($pr=="sr")
        $pr="Service";
        if($pr=="pa")
        $pr="Package";
        $sql = "SELECT * from invoice_items_".$branch_id." where type='$pr' and service=$pid and iid=$inv and active=0 and branch_id='".$branch_id."'";
        $result=query_by_id($con,$sql)[0];
        if($result) {
            return 1;
            }else{
            return 0;
        }
    }
    
    function getcat($cid) {
        global $conn;
        global $branch_id;
        $sql="SELECT * from expensecat where id='$cid' and branch_id='".$branch_id."'";
        $result=query_by_id($sql,[],$conn)[0];
        if($result) {
            return $result['title'];
        }
    }
    
    function getsales($date){
        global $branch_id;
        $sql="SELECT sum(total) as total from `invoice_".$branch_id."` where doa='$date' and active=0 and branch_id='".$branch_id."'";
        global $conn;
        
        $result=query_by_id($sql,[],$conn)[0];
        if($result){
            $total = ($result['total'] > 0)?$result['total']:0;
            return $total;
        }else{
            return 0;
        }
    }
    
    function getpendingamount($date){
        global $branch_id;
        $sql="SELECT sum(due) as total from `invoice_".$branch_id."` where doa='$date' and active=0 and branch_id='".$branch_id."'";
        global $conn;
        $result=query_by_id($sql,[],$conn)[0];
        if($result){
            $total = ($result['total'] > 0)?$result['total']:0;
            return $total;
        }else{
            return 0;
        }
    }
    
    
    function get_discount($date){
		global $branch_id;
	    $total = 0;   
        $sql="SELECT paid, dis, subtotal, total, id from `invoice_".$branch_id."` where doa='$date' and active=0 and branch_id='".$branch_id."'";
        $sql2="SELECT coupons.*, invoice.coupon, invoice.subtotal FROM coupons LEFT JOIN invoice_".$branch_id." invoice ON coupons.id = invoice.coupon WHERE invoice.doa='$date' and invoice.active=0 and invoice.branch_id='".$branch_id."'";
		global $conn;
		$result=query_by_id($sql,[],$conn);
		$result2=query_by_id($sql2,[],$conn);
		if($result){
		    foreach($result as $res){
		        $items = query_by_id("SELECT actual_price, price, quantity FROM `invoice_items_".$branch_id."` WHERE iid='".$res['id']."'",[],$conn);
		        foreach($items as $item){
		            $total = $total+(($item['actual_price']*$item['quantity'])-$item['price']);
		        }
		        
		        $discount = explode(",",$res['dis']);
		        if($discount['0'] == CURRENCY){
		            $total = $total + $discount['1'];
		        } else if($discount['0'] == 'pr'){
		            if($discount['1'] != 0){
    		            $dis_price = ($res['subtotal']/100)*$discount['1'];
    		            $total = $total+$dis_price;
		            }
		        }
		    }
		}
		
		if($result2){
		    foreach($result2 as $res){
		        if($res['discount_type'] == 0){
		            if($res['coupon'] != 0){
		                $dis_price = ($res['subtotal']/100)*$res['discount'];
    		            $total = $total+$dis_price;
		            }
		        } else {
		            $total = $total + $res['discount'];
		        }
		    }
		}
		return $total;
	}
	
	function get_discount_invoice($id, $date){
		global $branch_id;
	    $total = 0;
        $sql="SELECT paid, dis, subtotal, total, id from `invoice_".$branch_id."` where doa='$date' and active=0 and branch_id='".$branch_id."' and id='".$id."'";
        $sql2="SELECT coupons.*, invoice.coupon, invoice.subtotal FROM coupons LEFT JOIN invoice_".$branch_id." invoice ON coupons.id = invoice.coupon WHERE invoice.doa='$date' and invoice.active=0 and invoice.branch_id='".$branch_id."' and invoice.id='".$id."'";
		global $conn;
		$result=query_by_id($sql,[],$conn);
		$result2=query_by_id($sql2,[],$conn);
		if($result){
		    foreach($result as $res){
		        $discount = explode(",",$res['dis']);
		        if($discount['0'] == CURRENCY){
		            $total = $res['subtotal'] - $discount['1'];
		        } else if($discount['0'] == 'pr'){
		            if($discount['1'] != 0){
    		            $dis_price = ($res['subtotal']*$discount['1'])/100;
    		            $total = $res['subtotal']-$dis_price;
		            } else {
		                $total = $res['subtotal'];
		            }
		        }
		    }
		}
		
		if($result2){
		    foreach($result2 as $res){
		        if($res['discount_type'] == 0){
		            if($res['coupon'] != 0){
		                $dis_price = ($res['subtotal']/100)*$res['discount'];
    		            $total = $total+$dis_price;
		            }
		        } else {
		            $total = $total + $res['discount'];
		        }
		    }
		}
		return $total;
	}
    
    function getsalebytype($date, $type){
		global $branch_id;
		$sql="SELECT ii.actual_price, ii.price, ii.quantity from `invoice_items_".$branch_id."` ii LEFT JOIN invoice_".$branch_id." i on i.id=ii.iid where i.doa='$date' and ii.type='".$type."' and i.active=0 and i.branch_id='".$branch_id."'";
		global $conn;
		$total = 0;
		$result=query_by_id($sql,[],$conn);
		if($result) {
			foreach($result as $item){
	            $total = $total+($item['actual_price']*$item['quantity']);
			}
	        return $total;
		}else{
			return 0;
		}
	}
    
    function getclients($date){
        global $branch_id;
        $sql="SELECT distinct(client) from `invoice_".$branch_id."` where active=0 and doa='$date' and branch_id='".$branch_id."'";
        $cl = 0;
        global $conn;
        $result=query_by_id($sql,[],$conn);
        foreach($result as $row) {
            $cl++;
        }
        return $cl;
    }
    
    
    function getnewclients($date){
        global $branch_id;
        global $conn;
        $result = query("SELECT distinct(client) from `invoice_".$branch_id."` i left join client c on i.client = c.id where i.active=0 and i.doa = '".$date."' and i.branch_id='".$branch_id."'", [], $conn);
        $cl = 0;
        foreach($result as $row) {
            $cl++;
        }
        return $cl;
    }
    
    function walletrecharged($date){
        global $branch_id;
        global $conn;
        $wallet_amount = 0;
        $amount1 = query_by_id("SELECT paid_amount from `wallet_history` where status=1 and DATE(time_update) = '".$date."' and branch_id='".$branch_id."' and iid='0' and transaction_type='1'",[],$conn);
        $amount2 = query_by_id("SELECT wallet_amount from `wallet_history` where status=1 and DATE(time_update) ='".$date."' and branch_id='".$branch_id."' and iid!='0' and transaction_type='1'",[],$conn);
        if($amount1){
            foreach($amount1 as $a1){
                $wallet_amount += $a1['paid_amount'];
            }
        }
        if($amount2){
            foreach($amount2 as $a2){
                $wallet_amount += $a2['wallet_amount'];
            }
        }

        return $wallet_amount;
    }
    
    function taxcalculation($date, $is_exce = null){
        global $branch_id;
        global $conn;
        if($is_exce == 'exclusive'){
            $exl_tax = 0;
            $check_tax = query_by_id("SELECT id, tax, taxtype, dis, subtotal  FROM invoice_$branch_id WHERE doa='".$date."' AND branch_id='".$branch_id."'",[],$conn);
            foreach($check_tax as $tax){
               $gst_amount = get_discount_invoice($tax['id'], $date);
               if($tax['taxtype'] == '1'){
                   $tx = query_by_id("SELECT t.tax FROM tax t WHERE id='".$tax['tax']."' and active='0'",[],$conn)[0];
                   $exl_tax += ($gst_amount*$tx['tax']/100);
               }
            }
            return $exl_tax;
        } else if($is_exce == 'inclusive'){
            $inc_tax = 0;
            $check_tax = query_by_id("SELECT id, tax, taxtype, dis, subtotal  FROM invoice_$branch_id WHERE doa='".$date."' AND branch_id='".$branch_id."'",[],$conn);
            foreach($check_tax as $tax){
               $gst_amount = get_discount_invoice($tax['id'], $date);
               if($tax['taxtype'] == '0'){
                   $tx = query_by_id("SELECT t.tax FROM tax t WHERE id='".$tax['tax']."' and active='0'",[],$conn)[0];
                   $inc_tax += ($gst_amount-($gst_amount/($tx['tax']+100))*100);
               }
            }
            return $inc_tax;
        } else {
            $inc_tax = 0;
            $exl_tax = 0;
            $check_tax = query_by_id("SELECT id, tax, taxtype, dis, subtotal  FROM invoice_$branch_id WHERE doa='".$date."' AND branch_id='".$branch_id."'",[],$conn);
            foreach($check_tax as $tax){
               $gst_amount = get_discount_invoice($tax['id'], $date);
               if($tax['taxtype'] == '0'){
                   $tx = query_by_id("SELECT t.tax FROM tax t WHERE id='".$tax['tax']."' and active='0'",[],$conn)[0];
                   $inc_tax += ($gst_amount-($gst_amount/($tx['tax']+100))*100);
               } else if($tax['taxtype'] == '1'){
                   $tx = query_by_id("SELECT t.tax FROM tax t WHERE id='".$tax['tax']."' and active='0'",[],$conn)[0];
                   $exl_tax += ($gst_amount*$tx['tax']/100);
               }
            }
            
            echo '<strong>Inclusive : </strong>'.number_format($inc_tax,2)."<br />";
            echo '<strong>Exclusive : </strong>'.number_format($exl_tax,2);
        }
    }
    
    function commission_amount($date){
        global $branch_id;
        global $conn;
        $dateFilter="and i.doa = '".$date."'";
        $spQuery = "SELECT i.doa,b.name as service_provider,b.id as bid, s.name as s_name, imsp.service_name, b.cont,ii.price as serice_price, i.id as bill_id from `invoice_multi_service_provider` imsp "
            ." LEFT JOIN `invoice_items_".$branch_id."` ii on ii.id=imsp.ii_id "
            ." LEFT JOIN `invoice_".$branch_id."` i on i.id=ii.iid " 
            ." LEFT JOIN `beauticians` b on b.id = imsp.service_provider"
            ." LEFT JOIN `service` s on s.id = SUBSTRING_INDEX(ii.service,',',-1)"
            ." where i.active=0 and imsp.branch_id='".$branch_id."' ".$dateFilter;
        $spRecords = query_by_id($spQuery,[],$conn);
        foreach($spRecords as $row) {
            if(explode(",",$row['service_name'])[1] >0){
                 $ser_id = $row['service_name'];
                 $service_name=getservice($ser_id);
            }else{
                  $service_name = '';
            }
            
            if(explode(" ", $service_name)[0] == '(Service)'){
                $amount = getservicecom($row['serice_price'],$row['bid'],$ser_id, $row['bill_id']);
                $total_commission = $amount+$total_commission;
            } else { 
                $amount = getprodcom($row['serice_price'],$row['bid'],$ser_id, $row['bill_id']);
                $total_commission = $amount+$total_commission;
            }
        }
        return $total_commission;
    }
    
    function getexpenses($date){
        global $branch_id;
        $sql="SELECT sum(amount) as total FROM `expense` where active=0 and date='$date' and branch_id='".$branch_id."'";
        global $conn;
        $result=query_by_id($sql,[],$conn)[0];
        if($result) {
            if($result['total']=="")
                return 0;
            else
                return $result['total'];
        }else{
            return 0;
        }
    }
    
    function getadvance($date){
        global $conn;
        global $branch_id;
        $sql="SELECT sum(advance) as total FROM `invoice_".$branch_id."` where active=0 and doa='$date' and branch_id='".$branch_id."'";
        $result=query_by_id($sql,[],$conn)[0];
        if($result) {
            return $result['total'];
        }else{
            return 0;
        }
    }
    
    function getpayments($date){
        global $conn;
        global $branch_id;
        $sql="SELECT sum(credit) as credit FROM `payments` WHERE date='$date' and active=0 and branch_id='".$branch_id."'";
        $result=query_by_id($sql,[],$conn)[0];
        if ($result) {
            return $result['credit'];
        }else{
            return "0";
        }
    }
    
    function vendorpays($date){
        global $conn;
        global $branch_id;
        $sql="SELECT sum(debit) as debit FROM `payments` WHERE date='$date' and active=0 and branch_id='".$branch_id."'";
        $result=query_by_id($sql,[],$conn)[0];
        if ($result) {
            return $result['debit'];
        }else{
            return "0";
        }
    }
    
    function netcollection($date){
        global $branch_id; 
        $sql="SELECT sum(subtotal) as total,sum(bpaid) as paid FROM `invoice_".$branch_id."` where active=0 and doa='$date' and branch_id='".$branch_id."'";
        global $conn;
        $result=query_by_id($sql,[],$conn)[0];
        if($result) {
            $payments = getpayments($date);
            $total = $result['total'] +$payments;
            if($total=="")
            return 0;
            else
            return $total;
            }else{
            return 0;
        }
    }
    
    // commission functions
    
    
        function getsum($pid){
        $earn = getearnings($pid);
        $ded = getdeductions($pid);
        $sum = $earn - $ded;
        return $sum;
    }
    
    function getearnings($pid) {
        global $conn;
        global $branch_id;
        $sql2="SELECT sum(salary) as sum from salary where eid='$pid' and type=1 and branch_id='".$branch_id."'";
        $result2=query_by_id($sql2,[],$conn);
        foreach($result2 as $row2){
            return $row2['sum'];
        }
    }
    
    function getdeductions($cid) {
        global $conn;
        global $branch_id;
        $sql2="SELECT sum(salary) as sum from salary where eid='$cid' and type=2 and branch_id='".$branch_id."'";
        $result2=query_by_id($sql2,[],$conn);
        foreach($result2 as $row2) {
            return $row2['sum'];
        }
    }
    
    
    function getservicecom($price,$bid,$service,$id){
        $sum = 0;
        $provider_id = $bid;
        $type = 'Service';
        $total_sale = service_provider_total_sale($type, $provider_id, $id);
        $commission_per = service_provider_commission_saved($provider_id, $service, $id);
        $com = $commission_per;
        $val = $price * $com / 100;
        $sum = $sum + $val;
        $pcount = provider_count($service,$id);
        if($pcount > 0){
            return $sum/$pcount;
        } else {
            return $sum;   
        }
    }
    
    function getprodcom($price,$bid,$service,$id){
        $sum = 0;
        $provider_id = $bid;
        $type = 'Product';
        $total_sale = service_provider_total_sale($type, $provider_id, $id);
        $commission_per = service_provider_commission_saved($provider_id, $service, $id);
        $com = $commission_per;
        $val = $price * $com / 100;
        $sum = $sum + $val;
        $pcount = provider_count($service,$id);
        if($pcount > 0){
            return $sum/$pcount;
        } else {
            return $sum;   
        }
    }
    
    function getcoms($bid,$type,$id){
        global $conn;
        global $branch_id;
        $sum = 0;
        $sql="SELECT * from invoice_multi_service_provider where inv='$id' and service_name='$type' and service_provider = '$bid' and status='1' and branch_id='".$branch_id."'";
        $result=query_by_id($sql,[],$conn);
        foreach($result as $row) {
            return $row['commission_per'];
        }
    }
    
    function getcomp($bid,$type,$id){
        global $conn;
        global $branch_id;
        $sum = 0;
        $sql="SELECT * from invoice_multi_service_provider where inv='$id' and service_name='$type' and service_provider = '$bid' and status='1' and branch_id='".$branch_id."'";
        $result=query_by_id($sql,[],$conn);
        foreach($result as $row) {
            return $row['commission_per'];
        }
    }
    
    function getproduct($bid){
        global $conn;
        global $branch_id;
        $sum = 0;
        $sql="SELECT * from products where id=$bid";
        $result=query_by_id($sql,[],$conn);
        foreach($result as $row) {
            return $row['name'];
        }
    }
    
    function getservice($get_id){
        global $conn;
        global $branch_id;
        $id = EXPLODE(",",$get_id)[1];
        if(EXPLODE(",",$get_id)[0] == 'sr'){
            $sql ="SELECT CONCAT('(Service)',' ',name) as name FROM `service` where active='0' and id='$id'";   
        }else if(EXPLODE(",",$get_id)[0] == 'pr'){
            $sql ="SELECT CONCAT('(Product)',' ',name) as name FROM `products` where active='0' and id='$id'";  
        }else if(EXPLODE(",",$get_id)[0] == 'pa'){
            $sql ="SELECT CONCAT('(Package)',' ',name) as name FROM `packages` where active='0' and id='$id' and branch_id='".$branch_id."'";   
        }else if(EXPLODE(",",$get_id)[0] == 'prepaid'){
            $sql ="SELECT CONCAT('(Prepaid)',' ',pack_name) as name FROM `prepaid` where status='1' and id='$id' and branch_id='".$branch_id."'";   
        }else if(EXPLODE(",",$get_id)[0] == 'mem'){
            $sql ="SELECT CONCAT('(Membership)',' ',membership_name) as name FROM `membership_discount` where status='1' and id='$id'";
        }
        $result=query_by_id($sql,[],$conn);
        foreach($result as $row) {
            return $row['name'];
        }
    }
    
    ?>  