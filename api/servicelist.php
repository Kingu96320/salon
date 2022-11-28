<?php
include_once '../includes/db_include.php';
include_once 'cors.php';

$category_id = $_POST['cat_id'];
$service_for = $_POST['service_for'];

if($service_for != 0){
    $append2 = " and service_for = '".$service_for."' ";
} else {
    $append2 = "";
}

if($category_id != 0){
    $append = " AND cat='".$category_id."'";
} else {
    $append = "";
}

$resultArr = array();
$services = query("select * from service where active = 0 ".$append." $append2 order by name asc",[],$conn);
foreach($services as $res){
    $serviceArr = array();
    $serviceArr['serId'] = $res['id'];
    $serviceArr['serName'] = ucfirst($res['name']);
    $serviceArr['serDuration'] = $res['duration'];
    $serviceArr['serPrice'] = $res['price'];
    array_push($resultArr,$serviceArr);
}

echo json_encode($resultArr);

?>