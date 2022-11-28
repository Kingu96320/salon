<?php 
require_once '../includes/gym.php';
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$data = json_decode(file_get_contents("php://input"), true);
$mobile = $data['mobile'];
$cid = $data['cid'];

if($mobile==''){
    $arr["success"] = '0';
    $arr["msg"] = "Please enter correct mobile.";
}else{
    $response = query_by_id("SELECT cid,fname,contact_no,photo FROM customer_info where contact_no=:contact_no AND cid=:cid",["contact_no"=>$mobile,"cid"=>$cid],$conn);
    if($response){
        $response = $response[0];
        $arr["success"] = '1';
        $arr["msg"] = "Login Successfull!";
        $arr["cid"] = $response['cid'];
        $arr["name"] = $response['fname'];
        $arr["mobile"] = $response['contact_no'];
        $arr["photo"] = 'http://localhost/upsalon/'.$response['photo'];
        $arr['siteroot']='http://localhost/upsalon/app/';
    }else{
        $arr["success"] = '0';
        $arr["msg"] = "Mobile number is not registered.";
    }
}
print json_encode($arr);
exit;