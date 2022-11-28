<?php
require_once '../includes/db_include.php';
require_once 'path.php';
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
$data = json_decode(file_get_contents("php://input"), true);
$branches=query("SELECT * FROM salon_branches WHERE status='1'",[],$conn);
$response = array();
if($branches){
	$response['status'] = 1;
	$br = array();
	foreach($branches as $branch){
		$res['branch_id'] = $branch['id'];
		$res['branch_name'] = $branch['branch_name'];
		array_push($br, $res);
	}
	$response['branches'] = $br;
}
echo json_encode($response);
?>