<?php
require_once '../includes/db_include.php';
require_once 'path.php';

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$data = json_decode(file_get_contents("php://input"), true);

$system=query("select * from system",array(),$conn)[0];  
$arr=[];
$arr['success']=1;
$arr['discount']=10;
$arr['tax']=18;
	
		

   echo json_encode($arr);
?>