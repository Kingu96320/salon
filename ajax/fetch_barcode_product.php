<?php
include_once '../includes/db_include.php';
$branch_id = $_SESSION['branch_id'];
if(isset($_POST['action']) &&  $_POST['action'] == 'checkbarcode'){
    $barcode = $_POST['barcode'];
    $date = date('Y-m-d');
    if($barcode != ''){
        $query = "select concat('pr,',ps.product_id) as id, concat(concat(ps.product,' - ',ps.sale_price),' - ',DATE_FORMAT(ps.exp_date,'%d-%M-%Y')) as value, p.volume as volume, u.id as unit, ps.sale_price as price,'0' as duration,p.reward as points, ps.exp_date, ps.id as stock_id from purchase_items ps LEFT JOIN products p ON p.id = ps.product_id LEFT JOIN `units` u on u.id=p.unit WHERE p.barcode LIKE '%$barcode%' and ps.exp_date >= '".$date."' and p.active='0' and ps.branch_id='".$branch_id."'";
        $result = query_by_id($query,[],$conn)[0];
        if(count($result) > 0){
            $res = json_encode($result);
        } else {
            $res = '';
        }
    } else {
        $res = '';
    }
    echo $res;
}

if(isset($_POST['action']) &&  $_POST['action'] == 'addstockbarcode'){
    $barcode = $_POST['barcode'];
    $date = date('Y-m-d');
    if($barcode != ''){
        $query = "select p.id as id, p.name as value, p.volume as volume, u.id as unit, p.price as price,'0' as duration,p.reward as points, '' as exp_date, 0 as stock_id from  products p LEFT JOIN `units` u on u.id=p.unit WHERE p.barcode LIKE '%$barcode%' and p.active='0'";
        $result = query_by_id($query,[],$conn)[0];
        if(count($result) > 0){
            $res = json_encode($result);
        } else {
            $res = '';
        }
    } else {
        $res = '';
    }
    echo $res;
}

?>