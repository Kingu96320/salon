<?php
    include "../includes/db_include.php";
    $branch_id = $_SESSION['branch_id'];
    $from = strtotime($_GET['from']);
    $to = strtotime($_GET['to']);
?>
<table class="table table-bordered table-striped">
    <thead>
        <tr>
            <th>Sr. no</th>
            <th>Service provider</th>
            <th>Total clients</th>
            <th>Total services</th>
            <th>Service amount</th>
            <th>Service commission</th>
            <th>Total products</th>
            <th>Product amount</th>
            <th>Product commission</th>
            <th>Total membership</th>
            <th>Membership amount</th>
            <th>Total packages</th>
            <th>Package amount</th>
            <th>Actual amount</th>
            <th>Discount</th>
            <th>Total amount</th>
            <th>Average Bill Value (ABV)</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $count = 1;
        $total_client = 0;
        $total_service = 0;
        $service_amount = 0;
        $service_commission = 0;
        $total_products = 0;
        $product_amount = 0;
        $product_commission = 0;
        $total_membership = 0;
        $membership_amount = 0;
        $total_package = 0;
        $package_amount = 0;
        $total_amount = 0;
        $discount = 0;
        $actual_amount = 0;
        
        $service_provider = query_by_id("SELECT id, name, cont FROM beauticians WHERE branch_id='".$branch_id."' AND active='0'",[],$conn);
        foreach($service_provider as $sp){
            $t_service = 0;
            $ser_amount = 0;
            $ser_commission = 0;
            $t_products = 0;
            $pro_amount = 0;
            $pro_commission = 0;
            $t_membership = 0;
            $mem_amount = 0;
            $t_package = 0;
            $pack_amount = 0;
            $t_amount = 0;
            $dis = 0;
            $act_amount = 0;
            $c = 0;
            for($i=$from; $i<=$to; $i+=86400){
                $client_array = array();
                $date = date("Y-m-d", $i);  
                $data = query_by_id("SELECT i.client as client, ii.service, ii.quantity, ii.disc_row, ii.actual_price, ii.price, ii.type, imsp.commission_per FROM invoice_$branch_id i LEFT JOIN invoice_items_$branch_id ii ON ii.iid = i.id LEFT JOIN invoice_multi_service_provider imsp ON ii.id = imsp.ii_id WHERE service_provider = '".$sp['id']."' AND i.doa = '".$date."'",[],$conn);
                foreach($data as $d){
                    array_push($client_array, $d['client']);
                    if(explode(',',$d['service'])[0] == 'sr'){
                        $t_service += $d['quantity'];
                        $ser_amount += $d['actual_price']*$d['quantity'];
                        $ser_commission += $d['commission_per'];
                    }
                    if(explode(',',$d['service'])[0] == 'pr'){
                        $t_products += $d['quantity'];
                        $pro_amount += $d['actual_price']*$d['quantity'];
                        $pro_commission += $d['commission_per'];
                    }
                    if(explode(',',$d['service'])[0] == 'mem'){
                        $t_membership += $d['quantity'];
                        $mem_amount += $d['actual_price']*$d['quantity'];
                    }
                    if(explode(',',$d['service'])[0] == 'pa'){
                        $t_package += $d['quantity'];
                        $pack_amount += $d['actual_price']*$d['quantity'];
                    }
                    $t_amount += $d['price'];
                    $dis += (($d['actual_price']*$d['quantity'])-$d['price']);
                    $act_amount += $d['actual_price']*$d['quantity'];
                }
                if(!empty($client_array)){
                    $c = $c+count(array_unique($client_array));
                }
            }
            
            $total_service +=  $t_service;
            $service_amount += $ser_amount;
            $service_commission += $ser_commission;
            $total_products += $t_products;
            $product_amount += $pro_amount;
            $product_commission += $pro_commission;
            $total_membership += $t_membership;
            $membership_amount += $mem_amount;
            $total_package += $t_package;
            $package_amount += $pack_amount;
            $total_amount += $t_amount;
            $discount += $dis;
            $actual_amount += $act_amount;
            $total_client += $c;
            ?>
            <tr>
                <td><?php echo $count; ?></td>
                <td><?= $sp['name'].' ('.$sp['cont'].')' ?></td>
                <td><?php echo number_format($c); ?></td>
                <td><?php echo $t_service; ?></td>
                <td><?php echo number_format($ser_amount,2); ?></td>
                <td><?php echo number_format($ser_commission,2); ?></td>
                <td><?php echo $t_products; ?></td>
                <td><?php echo number_format($pro_amount,2); ?></td>
                <td><?php echo number_format($pro_commission,2); ?></td>
                <td><?php echo $t_membership; ?></td>
                <td><?php echo number_format($mem_amount,2); ?></td>
                <td><?php echo $t_package; ?></td>
                <td><?php echo number_format($pack_amount,2); ?></td>
                <td><?php echo number_format($act_amount, 2); ?></td>
                <td><?php echo number_format($dis, 2); ?></td>
                <td><?php echo number_format($t_amount, 2); ?></td>
                <td><?php echo number_format($t_amount/$c, 2); ?></td>
            </tr>
            <?php
            $count++;
        }
        
        ?>
        <tr>
            <td colspan="2" class="text-right"><b>Total</b></td>
            <td><b><?= $total_client ?></b></td>
            <td><b><?= $total_service ?></b></td>
            <td><b><?= number_format($service_amount, 2) ?></b></td>
            <td><b><?= number_format($service_commission, 2) ?></b></td>
            <td><b><?= $total_products ?></b></td>
            <td><b><?= number_format($product_amount, 2) ?></b></td>
            <td><b><?= number_format($product_commission, 2) ?></b></td>
            <td><b><?= $total_membership ?></b></td>
            <td><b><?= number_format($membership_amount, 2) ?></b></td>
            <td><b><?= $total_package ?></b></td>
            <td><b><?= number_format($package_amount, 2) ?></b></td>
            <td><b><?= number_format($actual_amount, 2) ?></b></td>
            <td><b><?= number_format($discount, 2) ?></b></td>
            <td><b><?= number_format($total_amount, 2) ?></b></td>
            <td><b><?= number_format($total_amount/$total_client, 2) ?></b></td>
        </tr>   
    </tbody>        
</table>