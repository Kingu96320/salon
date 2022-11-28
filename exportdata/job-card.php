<?php
    include_once '../includes/db_include.php';
    $sdate = strtotime(date('Y-m-d'));
    $edate = strtotime(date('Y-m-d'));
    if(isset($_GET['from'])){
        $sdate = strtotime($_GET['from']);
    }
    if(isset($_GET['to'])){
        $edate = strtotime($_GET['to']);
    }



    header("Content-Type: application/xls");
    header("Content-Disposition: attachment; filename=JobCard_".$sdate.'-'.$edate.".xls"); 
    header("Pragma: no-cache");
    header("Expires: 0");

    $branch_id = $_SESSION['branch_id'];
?>

<?php

    if(isset($_GET['sp_id'])){
        $sp_id = $_GET['sp_id'];
    } else {
        $sp_id = $service_provider[0]['id'];
    }

    if(isset($_GET['from']) && isset($_GET['to'])){
        $df = " AND i.doa BETWEEN  '".$_GET['from']."' AND '".$_GET['to']."' ";
    } else {
        $df = " AND i.doa BETWEEN  '".date('Y-m-d')."' AND '".date('Y-m-d')."' ";
    }

    $jb = query_by_id("SELECT ii.service, ii.quantity, ii.price, ii.id, c.name FROM invoice_items_".$branch_id." ii LEFT JOIN invoice_multi_service_provider imps ON imps.ii_id = ii.id LEFT JOIN client c ON c.id = ii.client LEFT JOIN invoice_".$branch_id." i ON i.id = ii.iid WHERE imps.service_provider = '".$sp_id."' AND ii.branch_id='".$branch_id."' $df ",[],$conn);
    $service_amount = 0;
    $service_qty = 0;
    $product_amount = 0;
    $product_qty = 0;
    $package_amount = 0;
    $package_qty = 0;
    $membership_amount = 0;
    $membership_qty = 0;
    $total_revenue = 0;

    if($jb){
        foreach($jb as $data){
            $type = explode(',',$data['service'])[0];
            if($type == 'sr'){
                $service_amount += $data['price'];
                $service_qty += $data['quantity']; 
            } else if($type == 'pr'){
                $product_amount += $data['price'];
                $product_qty += $data['quantity']; 
            } else if($type == 'pa'){
                $package_amount += $data['price'];
                $package_qty += $data['quantity']; 
            } else if($type == 'mem'){
                $membership_amount += $data['price'];
                $membership_qty += $data['quantity']; 
            } else {

            }
        }
        $total_revenue = $service_amount + $product_amount + $package_amount + $membership_amount;
    }
?>

<table border="1">
    <thead>
        <tr>
            <th><b>Date : <?= my_date_format($_GET['from']).'</b> to <b>'.my_date_format($_GET['to']) ?></b><br /></th>
            <th><b>Service provider : <?php
                echo query_by_id("SELECT name FROM beauticians WHERE id='".$sp_id."'",[],$conn)[0]['name'];
            ?></b></th>
            <th></th>
        </tr>
        <tr>
            <th colspan="3" class="text-center">Total Revenue Collected : <span><?= number_format($total_revenue, 2) ?></span></th>
        </tr>
        <tr>
            <th><strong>Type</strong></th>
            <th>Quantity</th>
            <th>Price</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td><strong>Services</strong></td>
            <td><?= $service_qty; ?></td>
            <td><?= number_format($service_amount,2) ?></td>
        </tr>
        <tr>
            <td><strong>Products</strong></td>
            <td><?= $product_qty ?></td>
            <td><?= number_format($product_amount,2) ?></td>
        </tr>
        <tr>
            <td><strong>Packages</strong></td>
            <td><?= $package_qty ?></td>
            <td><?= number_format($package_amount,2) ?></td> 
        </tr>
        <tr>
            <td><strong>Memberships</strong></td>
            <td><?= $membership_qty ?></td>
            <td><?= number_format($membership_amount,2) ?></td>                
        </tr>
    </tbody>
</table>
<br /><br />
<table border="1">
    <thead>
        <tr>
            <th colspan="3" class="text-center">Service List</span></th>
        </tr>
        <tr>
            <th><strong>Client</strong></th>
            <th>Item</th>
            <th>Price</th>
        </tr>
    </thead>
    <tbody>
    <?php
        if($jb){
            foreach($jb as $data){
                ?>
                <tr>
                    <td><?= $data['name'] ?></td>
                    <td><?= getservice($data['service']) ?></td>
                    <td><?= $data['price'] ?></td>
                </tr>
                <?php
            }
        } else {
            echo "<tr><td colspan='3' class='text-center'>No record found.</td></tr>";
        }
    ?>
    </tbody>
</table>
<?php  include "footer.php";
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
            return html_entity_decode($row['name']);
        }
    }
?>