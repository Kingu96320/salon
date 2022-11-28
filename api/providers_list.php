<?php
include_once '../includes/db_include.php';
include_once 'cors.php';

$resultArr = array();
$providers = query("select id, name from beauticians where active = 0 order by name asc",[],$conn);
foreach($providers as $res){
    $providerArr = array();
    $providerArr['providerId'] = $res['id'];
    $providerArr['providerName'] = ucfirst(strtolower($res['name']));
    array_push($resultArr,$providerArr);
}

echo json_encode($resultArr);

?>