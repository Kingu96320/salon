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
    header("Content-Disposition: attachment; filename=ServiceSaleReport_".$sdate.'-'.$edate.".xls"); 
    header("Pragma: no-cache"); 
    header("Expires: 0");

    $branch_id = $_SESSION['branch_id'];
    $append_query = '';

    if(isset($_GET['service_type']) && $_GET['service_type'] != ''){
        $append_query .= " AND type='".$_GET['service_type']."'";
    } 

    if(isset($_GET['stype']) && $_GET['stype'] != ''){
        $append_query .= " AND service='".$_GET['stype']."'";
    }  
?>
<table border="1">
    <thead>
        <tr>
            <th>Date</th>                                           
            <th>Service name</th>
            <th>Paid services</th>
            <th>Package services</th>
            <th>Total services</th>
            <th>Discount</th>
            <th>Total revenue</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $total_paid_services = 0;
        $total_package_services = 0;
        $total_services_count = 0;
        $total_discount = 0;
        $total_revenue = 0;

        for($i=$sdate; $i<=$edate; $i+=86400){
            $date = date("Y-m-d", $i);
            $data = query_by_id("SELECT DISTINCT service FROM invoice_items_$branch_id WHERE date(start_time) = '".$date."' AND active = '0' $append_query ",[],$conn);
            if($data){
                foreach($data as $ser){
                    $dis = 0;
                    $service_name = service_name($ser['service']);
                    $total_amount = query_by_id("SELECT SUM(price) as total FROM invoice_items_$branch_id WHERE service='".$ser['service']."' AND date(start_time)='".$date."'",[],$conn)[0]['total'];
                    $total_revenue = $total_revenue+$total_amount;

                    $paid_services = query_by_id("SELECT count(*) as total FROM invoice_items_$branch_id WHERE service='".$ser['service']."' AND date(start_time)='".$date."' AND package_service_id='0'",[],$conn)[0]['total'];
                    $total_paid_services = $total_paid_services+$paid_services;

                    $package_services = query_by_id("SELECT count(*) as total FROM invoice_items_$branch_id WHERE service='".$ser['service']."' AND date(start_time)='".$date."' AND package_service_id='1'",[],$conn)[0]['total'];
                    $total_package_services = $total_package_services+$package_services;

                    $total_services = query_by_id("SELECT count(*) as total FROM invoice_items_$branch_id WHERE service='".$ser['service']."' AND date(start_time)='".$date."'",[],$conn)[0]['total'];
                    $total_services_count = $total_services_count+$total_services;

                    $items = query_by_id("SELECT actual_price, price, quantity FROM `invoice_items_".$branch_id."` WHERE service='".$ser['service']."' AND date(start_time)='".$date."' ",[],$conn);
                    foreach($items as $item){
                        $dis = $dis+(($item['actual_price']*$item['quantity'])-$item['price']);
                    }
                    $total_discount = $total_discount+$dis;
                       
                   
                    ?>
                    <tr>
                        <td><?php echo my_date_format($date) ?></td>
                        <td><?php echo $service_name ?></td>
                        <td><?php echo $paid_services ?></td>
                        <td><?php echo $package_services ?></td>
                        <td><?php echo $total_services ?></td>
                        <td><?php echo number_format($dis,2) ?></td>
                        <td><?php echo number_format($total_amount,2) ?></td>
                    </tr>
                    <?php
                }
            }
        }
        ?>
        <tr>
            <td colspan="2" align="right"><b>Total</b></td>
            <td><b><?php echo $total_paid_services; ?></b></td>
            <td><b><?php echo $total_package_services; ?></b></td>
            <td><b><?php echo $total_services_count; ?></b></td>
            <td><b><?php echo number_format($total_discount,2); ?></b></td>
            <td><b><?php echo number_format($total_revenue,2); ?></b></td>
        </tr>
    </tbody>
</table>
<?php 
    function service_name($get_id){
        global $conn;
        $id = EXPLODE(",",$get_id)[1];
        if(EXPLODE(",",$get_id)[0] == 'sr'){
            $sql ="SELECT CONCAT('Service',' - ',name) as name FROM `service` where id='$id'";  
            }else if(EXPLODE(",",$get_id)[0] == 'pr'){
            $sql ="SELECT CONCAT('Product',' - ',name) as name FROM `products` where id='$id'"; 
            }else if(EXPLODE(",",$get_id)[0] == 'pa'){
            $sql ="SELECT CONCAT('Package',' - ',name) as name FROM `packages` where id='$id' and branch_id='".$_SESSION['branch_id']."'";  
            }else if(EXPLODE(",",$get_id)[0] == 'mem'){
            $sql ="SELECT CONCAT('Membership',' - ',membership_name) as name FROM `membership_discount` where id='$id'";    
            }
        $result=query_by_id($sql,[],$conn);
        foreach($result as $row) {
            return $row['name'];
        }
    }
?>
