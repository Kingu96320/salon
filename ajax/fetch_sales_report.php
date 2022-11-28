<?php 
    include_once '../includes/db_include.php'; 
    $branch_id = $_SESSION['branch_id'];
    if(isset($_GET['from_date']) && isset($_GET['to_date'])){
        $from_date=$_GET['from_date'];
        $to_date=$_GET['to_date'];
        $pageinfo=$_GET['pageinfo'];
        if($pageinfo !=''){
            $sql="SELECT id, name from `payment_method` where id='$pageinfo' and status='1'";
            $result = query_by_id($sql,[],$conn)[0];
            $payment_method = $result['id'];
        }
    }
    // Billing start date
    $sql_opening_start_date="SELECT i.doa from invoice_".$branch_id." i where i.active=0 and i.branch_id='".$branch_id."' order by doa asc limit 1";
    $result_opening_start_date = query_by_id($sql_opening_start_date,[],$conn)[0];
    $sql_start_date_invoice = $result_opening_start_date['doa'];
    
    // Appointment start date
    $sql_opening_start_date_app="SELECT updatetime,ai.id,ai.doa,c.name as client_name,c.cont,ai.paid,2 as type from app_invoice_".$branch_id." ai"
                    . " LEFT JOIN `client` c on c.id=ai.client "
                    . " where ai.active=0 and ai.branch_id='".$branch_id."' order by doa asc limit 1";
    $result_opening_start_date_app = query_by_id($sql_opening_start_date_app,[],$conn)[0];
    $sql_start_date_appointment = $result_opening_start_date_app['doa'];
    
    // Expense start date
    $sql_opening_start_date_expense="SELECT date from `expense` e"
                    . " where e.active=0 and e.branch_id='".$branch_id."' order by date asc limit 1";
    $result_opening_start_date_expense = query_by_id($sql_opening_start_date_expense,[],$conn)[0];
    $sql_start_date_expense = $result_opening_start_date_expense['date'];
    
?>  
    <style>
        #tr{
            border-top:hidden !important;
        }
        .text-right{
            color: #0f9e02 !important;
        }
    </style>
    <div class="row">
        <div class="col-lg-12 col-md-12-col-xs-12 col-sm-12"><div class="row">
            <div class="col-lg-4 col-md-4 col-xs-12 col-sm-12">
                <div class="card sales-card">
                    <div class="card-header" style="background:#f8f4f4 !important;padding: 10px;"><center><strong>Daily Sales</strong></center></div>
                    <div class="card-body" style="background:#fff !important;">
                        <table class="table tbl responsive" >
                            <?php
                                $modes = query("SELECT * FROM payment_method where status='1'", [], $conn);
                                $totalDaily = 0;
                                foreach($modes as $mode){
                                    ?>
                                        <tr id="tr">
                                            <td class="text-left"><?= $mode['name']?> </td>
                                            <td class="text-right">
                                                <span style="font-size:13px;margin-right:0px;"><?= CURRENCY_ICON ?></span>
                                                <?php 
                                                $payment_method = $mode['id'];
                                                $branch_id = $_SESSION['branch_id'];
                                                    $receivedBill = 0;
                                                    $sqlBill="SELECT i.branch_id as branch, updatetime,i.due,i.id,i.doa,c.name as client_name,c.cont,i.paid,1 as type,i.advance from `invoice_".$branch_id."` i "
                                                    . " LEFT JOIN `client` c on c.id=i.client"
                                                    . " LEFT JOIN `multiple_payment_method` mpm on mpm.invoice_id=CONCAT('bill,', i.id)"
                                                    . " where i.active=0 and i.doa = '".date('Y-m-d')."' and mpm.payment_method='$payment_method' and i.branch_id='".$branch_id."'"
                                                    . " UNION SELECT ai.branch_id as branch, updatetime,ai.due,ai.id,ai.doa,c.name as client_name,c.cont,ai.paid,2 as type,0 as advance from `app_invoice_".$branch_id."` ai"
                                                    . " LEFT JOIN `client` c on c.id=ai.client "
                                                    . " LEFT JOIN `multiple_payment_method` mpm on mpm.invoice_id=CONCAT('app,', ai.id)"
                                                    . " where ai.active=0 and ai.appdate  = '".date('Y-m-d')."' and mpm.payment_method='".$payment_method."' and ai.branch_id='".$branch_id."'"
                                                    . " UNION SELECT ipp.branch_id as branch, ipp.update_date as updatetime,0 as due,ipp.id,ipp.pending_paid_date as doa,c.name as client_name,c.cont,ipp.pending_payment_received as paid,3 as type,0 as advance from `invoice_pending_payment` ipp "
                                                    . " LEFT JOIN `client` c on c.id=ipp.client_id"
                                                    . " where ipp.status=1 and ipp.paid_at_branch='".$branch_id."' and ipp.pending_paid_date = '".date('Y-m-d')."' and ipp.payment_method='".$payment_method."'"
                                                    . " UNION SELECT 0 as branch, date,0 as due,w.id,w.date doa,c.name as client_name,c.cont,w.wallet_amount as paid,4 as type,0 as advance from `wallet` w "
                                                    . " LEFT JOIN `client` c on c.id=w.client_id "
                                                    . " where w.status=1 and w.date = '".date('Y-m-d')."' and w.payment_method='$payment_method' order by updatetime asc";
                                                    
                                                    $result1=query($sqlBill,[],$conn);
                                                    if($result1){
                                                        $receivedBill=0;
                                                        foreach($result1 as $row1) {
                                                            $paid =($row1['type']=='1')?get_cash($row1['id']):$row1['paid'];
                                                                $receivedBill+=$paid;
                                                            $totalDaily +=$paid;
                                                        }
                                                        echo number_format($receivedBill,2);
                                                        }else {
                                                            echo "0.00";
                                                        }  
                                                    
                                                  
                                                    
                                                    
                                                ?></td>
                                        </tr>
                                    <?php
                                }
                            ?>
                            <tr>
                                <td class="text-left"><strong>Total</strong></td>
                                <td class="text-right">
                                    <strong><span style="font-size:15px;margin-right:0px;"><?= CURRENCY_ICON ?></span> <?= number_format($totalDaily,2) ?></strong>
                                </td>
                            </tr>
                        </table>
                    </div>
                    
                </div>
            </div>
            <div class="col-lg-4 col-md-4 col-xs-12 col-sm-12">
                <div class="card sales-card">
                    <div class="card-header" style="background:#f8f4f4 !important;padding: 10px;"><center><strong>Monthly Sales</strong></center></div>
                    <div class="card-body" style="background:#fff !important;">
                        <table class="table tbl responsive" >
                            <?php
                                $totalMonthly = 0;
                                foreach($modes as $mode){
                                    ?>
                                        <tr id="tr">
                                            <td class="text-left"><?= $mode['name']?> </td>
            
                                            <td class="text-right">
                                                <span style="font-size:13px;margin-right:0px;"><?= CURRENCY_ICON ?></span>
                                                <?php 
                                                $payment_method = $mode['id'];
                                                $branch_id = $_SESSION['branch_id'];
                                                    $receivedBill = 0;
                                                    $sqlBill="SELECT i.branch_id as branch, updatetime,i.due,i.id,i.doa,c.name as client_name,c.cont,i.paid,1 as type,i.advance from `invoice_".$branch_id."` i "
                                                    . " LEFT JOIN `client` c on c.id=i.client"
                                                    . " LEFT JOIN `multiple_payment_method` mpm on mpm.invoice_id=CONCAT('bill,', i.id)"
                                                    . " where i.active=0 and month(i.doa) = '".date('m')."' and mpm.payment_method='$payment_method' and i.branch_id='".$branch_id."'"
                                                    . " UNION SELECT ai.branch_id as branch, updatetime,ai.due,ai.id,ai.doa,c.name as client_name,c.cont,ai.paid,2 as type,0 as advance from `app_invoice_".$branch_id."` ai"
                                                    . " LEFT JOIN `client` c on c.id=ai.client "
                                                    . " LEFT JOIN `multiple_payment_method` mpm on mpm.invoice_id=CONCAT('app,', ai.id)"
                                                    . " where ai.active=0 and month(ai.appdate) = '".date('m')."' and mpm.payment_method='".$payment_method."' and ai.branch_id='".$branch_id."'"
                                                    . " UNION SELECT ipp.branch_id as branch, ipp.update_date as updatetime,0 as due,ipp.id,ipp.pending_paid_date as doa,c.name as client_name,c.cont,ipp.pending_payment_received as paid,3 as type,0 as advance from `invoice_pending_payment` ipp "
                                                    . " LEFT JOIN `client` c on c.id=ipp.client_id"
                                                    . " where ipp.status=1 and ipp.paid_at_branch='".$branch_id."' and month(ipp.pending_paid_date) = '".date('m')."' and ipp.payment_method='".$payment_method."'"
                                                    . " UNION SELECT 0 as branch, date,0 as due,w.id,w.date doa,c.name as client_name,c.cont,w.wallet_amount as paid,4 as type,0 as advance from `wallet` w "
                                                    . " LEFT JOIN `client` c on c.id=w.client_id "
                                                    . " where w.status=1 and month(w.date) = '".date('m')."' and w.payment_method='$payment_method' and w.branch_id='".$branch_id."' order by updatetime asc";
                                                    
                                                    $result1=query($sqlBill,[],$conn);
                                                    if($result1){
                                                        $receivedBill=0;
                                                        foreach($result1 as $row1) {
                                                            $paid =($row1['type']=='1')?get_cash($row1['id']):$row1['paid'];
                                                                $receivedBill+=$paid;
                                                            $totalMonthly +=$paid;
                                                        }
                                                        echo number_format($receivedBill,2);
                                                        }else {
                                                            echo "0.00";
                                                        }  
                                                    

                                                ?></td>
                                        </tr>
                                    <?php
                                }
                            ?>
                            <tr>
                                <td class="text-left"><strong>Total</strong></td>
                                <td class="text-right">
                                    <strong><span style="font-size:15px;margin-right:0px;"><?= CURRENCY_ICON ?></span> <?= number_format($totalMonthly,2) ?></strong>
                                </td>
                            </tr>
                        </table>
                    </div>
                    
                </div>
            </div>           
            <div class="col-lg-4 col-md-4 col-xs-12 col-sm-12">
                <div class="card sales-card">
                    <div class="card-header" style="background:#f8f4f4 !important;padding: 10px;"><center><strong>Yearly Sales</strong></center></div>
                    <div class="card-body" style="background:#fff !important;">
                        <table class="table tbl responsive" >
                            <?php
                                $totalYearly = 0;
                                foreach($modes as $mode){
                                    ?>
                                        <tr id="tr">
                                            <td class="text-left"><?= $mode['name']?> </td>
            
                                            <td class="text-right">
                                                <span style="font-size:13px;margin-right:0px;"><?= CURRENCY_ICON ?></span>
                                                <?php 
                                                $payment_method = $mode['id'];
                                                $branch_id = $_SESSION['branch_id'];
                                                    $receivedBill = 0;
                                                    $sqlBill="SELECT i.branch_id as branch, updatetime,i.due,i.id,i.doa,c.name as client_name,c.cont,i.paid,1 as type,i.advance from `invoice_".$branch_id."` i "
                                                    . " LEFT JOIN `client` c on c.id=i.client"
                                                    . " LEFT JOIN `multiple_payment_method` mpm on mpm.invoice_id=CONCAT('bill,', i.id)"
                                                    . " where i.active=0 and year(i.doa) = '".date('Y')."' and mpm.payment_method='$payment_method' and i.branch_id='".$branch_id."'"
                                                    . " UNION SELECT ai.branch_id as branch, updatetime,ai.due,ai.id,ai.doa,c.name as client_name,c.cont,ai.paid,2 as type,0 as advance from `app_invoice_".$branch_id."` ai"
                                                    . " LEFT JOIN `client` c on c.id=ai.client "
                                                    . " LEFT JOIN `multiple_payment_method` mpm on mpm.invoice_id=CONCAT('app,', ai.id)"
                                                    . " where ai.active=0 and year(ai.appdate)  = '".date('Y')."' and mpm.payment_method='".$payment_method."' and ai.branch_id='".$branch_id."'"
                                                    . " UNION SELECT ipp.branch_id as branch, ipp.update_date as updatetime,0 as due,ipp.id,ipp.pending_paid_date as doa,c.name as client_name,c.cont,ipp.pending_payment_received as paid,3 as type,0 as advance from `invoice_pending_payment` ipp "
                                                    . " LEFT JOIN `client` c on c.id=ipp.client_id"
                                                    . " where ipp.status=1 and ipp.paid_at_branch='".$branch_id."' and year(ipp.pending_paid_date) = '".date('Y')."' and ipp.payment_method='".$payment_method."'"
                                                    . " UNION SELECT 0 as branch, date,0 as due,w.id,w.date doa,c.name as client_name,c.cont,w.wallet_amount as paid,4 as type,0 as advance from `wallet` w "
                                                    . " LEFT JOIN `client` c on c.id=w.client_id "
                                                    . " where w.status=1 and year(w.date) = '".date('Y')."' and w.payment_method='$payment_method' and w.branch_id='".$branch_id."' order by updatetime asc";
                                                    
                                                    $result1=query($sqlBill,[],$conn);
                                                    if($result1){
                                                        $receivedBill=0;
                                                        foreach($result1 as $row1) {
                                                            $paid =($row1['type']=='1')?get_cash($row1['id']):$row1['paid'];
                                                                $receivedBill+=$paid;
                                                            $totalYearly +=$paid;
                                                        }
                                                        echo number_format($receivedBill,2);
                                                        }else {
                                                            echo "0.00";
                                                        }  
                                                    

                                                ?></td>
                                        </tr>
                                    <?php
                                }
                            ?>
                            <tr>
                                <td class="text-left"><strong>Total</strong></td>
                                <td class="text-right">
                                   <strong><span style="font-size:15px;margin-right:0px;"><?= CURRENCY_ICON ?></span> <?= number_format($totalYearly,2) ?></strong>
                                </td>
                            </tr>
                        </table>
                    </div>
                    
                </div>
            </div></div>
        </div>
    </div>
    <br/><br/>

    <div class="row">
        <div class="col-sm-12 col-lg-12 col-md-4 col-xs-12"><div class="row">
            <div class="col-md-4 col-lg-4 col-sm-12 col-xs-12">
                <div class="card sales-card">
                    <div class="card-header" style="background:#f8f4f4 !important;padding: 10px;"><center><strong>Daily Wallet Recharge</strong></center></div>
                    <div class="card-body" style="background:#fff !important;">
                        <table class="table tbl responsive" >
                            <?php
                            $total = 0;
                                foreach($modes as $mode){
                                    $amtCredit = query("SELECT SUM(paid_amount) as tot from wallet_history where payment_method='".$mode['id']."' and transaction_type=1 and date(time_update) = '".date('Y-m-d')."' and branch_id='".$branch_id."'", [], $conn)[0]['tot'];
                                    if($amtCredit == "" || $amtCredit == NULL){
                                        $amtCredit = 0;
                                    }

                                    $amtDebit = query("SELECT SUM(paid_amount) as tot from wallet_history where payment_method='".$mode['id']."' and (transaction_type = '0' or transaction_type=2) and date(time_update) = '".date('Y-m-d')."' and branch_id='".$branch_id."'", [], $conn)[0]['tot'];
                                    if($amtDebit == "" || $amtDebit == NULL){
                                        $amtDebit = 0;
                                    }
                                    $amt = $amtCredit - $amtDebit;
                                    $total += $amt;
                                    echo "<tr id = 'tr'><td class='text-left'>".$mode['name']."</td><td class='text-right'>
                                    <span style='font-size:13px;margin-right:0px;'>".CURRENCY_ICON."</span> ".number_format($amt,2)."</td></tr>";
                                }
                            ?>
                            <tr><td class="text-left"><strong>Total</strong></td><td class="text-right"><strong><span style='font-size:15px;margin-right:0px;'><?= CURRENCY_ICON ?></span> <?= number_format($total,2) ?></strong></td></tr>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-md-4 col-lg-4 col-sm-12 col-xs-12">
                <div class="card sales-card">
                    <div class="card-header" style="background:#f8f4f4 !important;padding: 10px;"><center><strong>Monthly Wallet Recharge</strong></center></div>
                    <div class="card-body" style="background:#fff !important;">
                        <table class="table tbl responsive" >
                            <?php
                            $total = 0;
                                foreach($modes as $mode){
                                    $amtCredit = query("SELECT SUM(paid_amount) as tot from wallet_history where payment_method='".$mode['id']."' and transaction_type=1 and MONTH(date(time_update)) = '".date('m')."' and branch_id='".$branch_id."' ", [], $conn)[0]['tot'];
                                    if($amtCredit == "" || $amtCredit == NULL){
                                        $amtCredit = 0;
                                    }

                                    $amtDebit = query("SELECT SUM(paid_amount) as tot from wallet_history where payment_method='".$mode['id']."' and (transaction_type = '0' or transaction_type=2) and MONTH(date(time_update)) = '".date('m')."' and branch_id='".$branch_id."' ", [], $conn)[0]['tot'];
                                    if($amtDebit == "" || $amtDebit == NULL){
                                        $amtDebit = 0;
                                    }
                                    $amt = $amtCredit - $amtDebit;
                                    $total += $amt;
                                    echo "<tr id = 'tr'><td class='text-left'>".$mode['name']."</td><td class='text-right'>
                                    <span style='font-size:13px;margin-right:0px;'>".CURRENCY_ICON."</span> ".number_format($amt,2)."</td></tr>";
                                }
                            ?>
                            <tr><td class="text-left"><strong>Total</strong></td><td class="text-right"><strong><span style='font-size:15px;margin-right:0px;'><?= CURRENCY_ICON ?></span> <?= number_format($total,2) ?></strong></td></tr>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-md-4 col-lg-4 col-sm-12 col-xs-12">
                <div class="card sales-card">
                    <div class="card-header" style="background:#f8f4f4 !important;padding: 10px;"><center><strong>Yearly Wallet Recharge</strong></center></div>
                    <div class="card-body" style="background:#fff !important;">
                        <table class="table tbl responsive" >
                            <?php
                            $total = 0;
                                foreach($modes as $mode){
                                    $amtCredit = query("SELECT SUM(paid_amount) as tot from wallet_history where payment_method='".$mode['id']."' and transaction_type=1 and Year(date(time_update)) = '".date('Y')."' and branch_id='".$branch_id."' ", [], $conn)[0]['tot'];
                                    if($amtCredit == "" || $amtCredit == NULL){
                                        $amtCredit = 0;
                                    }

                                    $amtDebit = query("SELECT SUM(paid_amount) as tot from wallet_history where payment_method='".$mode['id']."' and (transaction_type = '0' or transaction_type=2) and Year(date(time_update)) = '".date('Y')."' and branch_id='".$branch_id."' ", [], $conn)[0]['tot'];
                                    if($amtDebit == "" || $amtDebit == NULL){
                                        $amtDebit = 0;
                                    }
                                    $amt = $amtCredit - $amtDebit;
                                    $total += $amt;
                                    echo "<tr id = 'tr'><td class='text-left'>".$mode['name']."</td><td class='text-right'>
                                    <span style='font-size:13px;margin-right:0px'>".CURRENCY_ICON."</span>".number_format($amt,2)."</td></tr>";
                                }
                            ?>
                            <tr><td class="text-left"><strong>Total</strong></td><td class="text-right"><strong><span style='font-size:15px;margin-right:0px;'><?= CURRENCY_ICON ?></span> <?= number_format($total,2) ?></strong></td></tr>
                        </table>
                    </div>
                </div>
            </div>
            </div>
        </div>
    </div><br /><br />
    <div class="">
    <div class="row">
        <div class="col-lg-12">
                <?php
                $from = date('Y-m-d', strtotime('-1 month', strtotime(date('Y-m-d'))));
                $to = date('Y-m-d');
                $totalTotal = 0;
                $totalSubTotal = 0;
                $totalTaxinc = 0;
                $totalTaxexc = 0;
                $totalDiscount = 0;
                $totalTax = 0;
                $totalDue = 0;
                $totalTxn = 0;
                for($i=strtotime($from); $i<=strtotime($to); $i+=86400){
                        $d = date("Y-m-d", $i);
                        $sqlBill="SELECT i.branch_id as branch, updatetime,i.due,i.id,i.doa,c.name as client_name,c.cont,i.paid,1 as type, i.subtotal as subtotal ,i.advance,i.tax, i.taxtype, i.dis from `invoice_".$branch_id."` i "
                            . " LEFT JOIN `client` c on c.id=i.client"
                            . " LEFT JOIN `multiple_payment_method` mpm on mpm.invoice_id=CONCAT('bill,', i.id)"
                            . " where i.active=0 and i.doa = '".$d."' and i.branch_id='".$branch_id."'"
                            . " UNION SELECT ai.branch_id as branch, updatetime,ai.due,ai.id,ai.doa,c.name as client_name,c.cont,ai.paid,2 as type, ai.subtotal as subtotal,0 as advance,ai.tax, ai.taxtype, ai.dis from `app_invoice_".$branch_id."` ai"
                            . " LEFT JOIN `client` c on c.id=ai.client "
                            . " LEFT JOIN `multiple_payment_method` mpm on mpm.invoice_id=CONCAT('app,', ai.id)"
                            . " where ai.active=0 and ai.appdate  = '".$d."' and ai.branch_id='".$branch_id."' and ai.bill_created_status='1'";
                        $result1=query($sqlBill,[],$conn);
                        if($result1){
                            $receivedBill=0;
                            $total = 0;
                            $subtotal = 0;
                            $tax = 0;
                            $discount_amount = 0;
                            $due = 0;
                            if(isset($_GET['from']) && isset($_GET['to'])){
                                $sdate = $_GET['from'];
                                $edate = $_GET['to'];
                                $sql="SELECT paid, dis, subtotal, total, id from `invoice_".$branch_id."` where doa BETWEEN '$sdate' AND '$edate' and active=0 and branch_id='".$branch_id."'";
                                $sql2="SELECT coupons.* FROM coupons LEFT JOIN invoice_".$branch_id." invoice ON coupons.id = invoice.coupon WHERE invoice.doa BETWEEN '$sdate' AND '$edate' and invoice.active=0 and invoice.branch_id='".$branch_id."'";
                                
                            } else {
                                $date = date('Y-m-d');    
                                $sql="SELECT paid, dis, subtotal, total, id from `invoice_".$branch_id."` where doa='$date' and active=0 and branch_id='".$branch_id."'";
                                $sql2="SELECT coupons.* FROM coupons LEFT JOIN invoice_".$branch_id." invoice ON coupons.id = invoice.coupon WHERE invoice.doa='$date' and invoice.active=0 and invoice.branch_id='".$branch_id."'";
                            }
                            global $conn;
                            $result=query_by_id($sql,[],$conn);
                            $result2=query_by_id($sql2,[],$conn);
                            if($result){
                                foreach($result as $res){
                                    $items = query_by_id("SELECT actual_price, price, quantity FROM `invoice_items_".$branch_id."` WHERE iid='".$res['id']."'",[],$conn);
                                    foreach($items as $item){
                                        $discount_amount = $discount_amount+(($item['quantity']*$item['actual_price'])-$item['price']);
                                    }
                                    
                                    $discount = explode(",",$res['dis']);
                                    if($discount['0'] == CURRENCY){
                                        $discount_amount = $discount_amount + $discount['1'];
                                    } else if($discount['0'] == 'pr'){
                                        if($discount['1'] != 0){
                                            $dis_price = ($res['subtotal']/100)*$discount['1'];
                                            $discount_amount = $discount_amount+$dis_price;
                                        } else {
                                            $discount_amount = $discount_amount;
                                        }
                                    }
                                }
                            }
                            
                            if($result2){
                                foreach($result2 as $res){
                                    $discount_amount = $discount_amount + $res['discount'];
                                }
                            }
        
                            $txn = 0;
                            $discount = 0;
                            $inc_tax = 0;
                            $exl_tax = 0;
                            
                            foreach($result1 as $row1) {
                                $txn += 1;
                                $total += ($row1['paid']+$row1['due']);
                                $due += $row1['due'];
                                $subtotal += $row1['subtotal'];
                                if($row1['subtotal'] == 0){
                                    $subtotal += $row1['paid'];
                                }
                                $discount_val = explode(",", $row1["dis"]);
                                
                                $items = query_by_id("SELECT actual_price, price, quantity FROM `invoice_items_".$branch_id."` WHERE iid='".$row1['id']."'",[],$conn);
                                foreach($items as $item){
                                    $discount = $discount+(($item['quantity']*$item['actual_price'])-$item['price']);
                                    $subtotal += (($item['quantity']*$item['actual_price'])-$item['price']);
                                }
                                
                                
                                if($discount_val['0'] == CURRENCY){
                                        $discount = $discount + $discount_val['1'];
                                } else if($discount_val['0'] == 'pr'){
                                    if($discount_val['1'] != 0){
                                        $dis_price = ($row1['subtotal']/100)*$discount_val['1'];
                                        $discount = $discount+$dis_price;
                                    } else {
                                        $discount = $discount;
                                    }
                                }

                                $check_tax = query_by_id("SELECT id, tax, taxtype, dis, subtotal  FROM invoice_$branch_id WHERE id='".$row1['id']."' AND branch_id='".$branch_id."'",[],$conn);
                                foreach($check_tax as $tax){
                                   $gst_amount = get_discount_invoice($tax['id']);
                                   if($tax['taxtype'] == '0' && ($tax['tax'] == '1' || $tax['tax'] == '2')){
                                       $tx = query_by_id("SELECT t.tax FROM tax t WHERE id='".$tax['tax']."' and active='0'",[],$conn)[0];
                                       $inc_tax += ($gst_amount-($gst_amount/($tx['tax']+100))*100);
                                   } else if($tax['taxtype'] == '1' && ($tax['tax'] == '1' || $tax['tax'] == '2')){
                                       $tx = query_by_id("SELECT t.tax FROM tax t WHERE id='".$tax['tax']."' and active='0'",[],$conn)[0];
                                       $exl_tax += ($gst_amount*$tx['tax'])/100;
                                       
                                   }
                                }

                            }
                            $totalDiscount += $discount;
                            $totalTotal += $total;
                            $totalSubTotal += $subtotal;
                            $totalTaxinc += $inc_tax;
                            $totalTaxexc += $exl_tax;
                            $totalTax += ($inc_tax+$exl_tax);
                            $totalDue += $due;
                            $totalTxn += $txn;
                        } else {

                        }  
                    }   
                ?>
            </table>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-8">
            <div id="chartContainer" style="height: 300px; width: 100%;"></div>
        </div>
        <div class="col-lg-4">
                <div class="card sales-card">
                    <div class="card-header" style="background:#f8f4f4 !important;padding:10px;"><center><strong>Customers</strong></center></div>
                    <div class="card-body" style="background:#fff !important;">
                        <br/><br/><div align="center"><button class="button btn btn-success" onclick="window.open('clients.php', '_blank')">Total Customers :  <?= query("SELECT count(*) as t from client where branch_id='".$_SESSION['branch_id']."' and active='0' ", [], $conn)[0]['t'] ?></button></div>
                        <br/><br/>
                        <div align="center"><button class="button btn btn-success" onclick = "cust(0);">New Customers : 
                        <?php 
                            $cl = 0;
                            $result = query("SELECT distinct(client), i.doa from `invoice_".$branch_id."` i left join client c on i.client = c.id where i.active=0 and i.doa = '".date('Y-m-d')."' and i.branch_id='".$branch_id."'", [], $conn); 
                            if($result){
                                foreach($result as $row) {
                                    if(strtotime(date('Y-m-d')) <= strtotime(firstvisit($row['client']))){
                                        $cl++;
                                    }
                                }
                            }
                            echo $cl;
                            ?></button></div>
                        <br/><br/>
                        <div align="center"><button class="button btn btn-success">Repeated Customers :
                         <?php
                            $ct = 0;
                            $query = "SELECT i.client, i.doa from `invoice_".$branch_id."` i left join client c on i.client = c.id  where i.active=0 and i.doa ='".date('Y-m-d')."' and i.branch_id='".$branch_id."'";
                            $data_ct = query($query,[],$conn);
                            if($data_ct){
                                foreach($data_ct as $data){
                                    if(strtotime(date('Y-m-d')) > strtotime(firstvisit($data['client']))){
                                        $ct++;
                                    } else {
                                        
                                    }
                                }
                            }
                            echo $ct;
                        ?></button></div>
                        <!--<div align="center"><button class="button btn btn-success" onclick = "cust(1);">Repeated Customers : <?= $ct ?>-->
                        <br/><br/>
                    </div>
                </div>
        </div>      
    </div>
</div>
<script type="text/javascript">
    $('document').ready(function(){
var chart = new CanvasJS.Chart("chartContainer", {
    animationEnabled: true,
    exportEnabled: true,
    theme: "light1", // "light1", "light2", "dark1", "dark2"
    title:{
        text: "Sales Record"
    },
    data: [{
        type: "column", //change type to bar, line, area, pie, etc
        //indexLabel: "{y}", //Shows y value on all Data Points
        indexLabelFontColor: "#5A5757",
        indexLabelFontSize: 16,
        indexLabelPlacement: "outside",
        dataPoints: [
            { label:"Sub Total", y: <?= $totalSubTotal ?> },
            { label:"Tax", y: <?= $totalTax ?> },
            { label:"Net Discount", y: <?= $totalDiscount ?> },
            { label:"Grand Total", y: <?= $totalTotal ?> }
        ]
    }]
});
chart.render();
    });
</script>
<?php
    function firstvisit($uid){
        global $conn;
        global $branch_id;
        $sql="SELECT * from invoice_".$branch_id." where client='$uid' and branch_id='".$branch_id."' order by doa asc limit 1";
        $result=query_by_id($sql,[],$conn);
        if ($result) 
        {
            foreach($result as $row)
            {
                return $row['doa'];
            }
            }else{
            return "NA";
        }
    }
    
    function get_discount_invoice($id){
        global $branch_id;
        $total = 0;
        $sdate = date('Y-m-d', strtotime('-1 month', strtotime(date('Y-m-d'))));
        $edate = date('Y-m-d');
        $sql="SELECT paid, dis, subtotal, total, id from `invoice_".$branch_id."` where doa BETWEEN '$sdate' AND '$edate' and active=0 and branch_id='".$branch_id."' and id='".$id."'";
        $sql2="SELECT coupons.* FROM coupons LEFT JOIN invoice_".$branch_id." invoice ON coupons.id = invoice.coupon WHERE invoice.doa BETWEEN '$sdate' AND '$edate' and invoice.active=0 and invoice.branch_id='".$branch_id."' and invoice.id='".$id."'";
        global $conn;
        $result=query_by_id($sql,[],$conn);
        $result2=query_by_id($sql2,[],$conn);
        if($result){
            foreach($result as $res){
                $discount = explode(",",$res['dis']);
                if($discount['0'] == CURRENCY){
                    $total = $res['subtotal'] - $discount['1'];
                } else if($discount['0'] == 'pr'){
                    if($discount['1'] != '0'){
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
                $total = $total - $res['discount'];
            }
        }
        return $total;
    }
    
    function get_cash($id){
        global $conn,$payment_method;
        global $branch_id;
        $paid=0;
        $sql="SELECT amount_paid From`multiple_payment_method` where invoice_id=CONCAT('bill,',$id) and status='1' and payment_method='$payment_method' and branch_id='".$branch_id."'";
        
        $result = query_by_id($sql,[],$conn);
        if($result){
            foreach($result as $row){
                $paid+=$row['amount_paid'];
            }
        }
        return $paid;
    }   
        function getcat($cid) {
        global $conn;
        global $branch_id;
        $sql="SELECT * from expensecat where id='$cid' and branch_id='".$branch_id."'";
        $result=query_by_id($sql,[],$conn);
        foreach($result as $row) 
        {
            return $row['title'];
        }
    }
    function get_user($user){
        global $conn;
        global $branch_id;
        $sql="SELECT * from user where id=$user and branch_id='".$branch_id."' order by id desc";
        $result=query_by_id($sql,[],$conn);
        foreach($result as $row) 
        {
            return $row['name'];
        }
    }
    //sleep(5);
?>  
