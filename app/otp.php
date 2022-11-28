<?php 
error_reporting(E_ALL);
ini_set("display_errors", 1);
require_once '../includes/db_include.php';
require_once 'path.php';

require_once("../../salonSoftFiles_new/send_sms.php");
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$data = json_decode(file_get_contents("php://input"), true);

$otp = $data['otp'];
$mobile = $data['mobile'];

if($otp==''){
    $arr["success"] = '0';
    $arr["msg"] = "Invalid OTP.";
}else{
   
  $response = query_by_id("SELECT o.cid,o.mobile,o.name,c.address FROM otp_app o left join client c on c.id=o.cid where o.mobile='".$mobile."' AND o.otp='".$otp."' ",[],$conn)[0];

  if($response){
       // if($mobile!='9888629974'){
			query("UPDATE otp_app set status='1' where cid=:cid",["cid"=>$response['cid']],$conn);
        //}
        $arr["success"] = '1';
        $arr["msg"] = "Login Successfull!";
        $arr["cid"] = $response['cid'];
        $arr["name"] = $response['name'];
        $arr["mobile"] = $response['mobile'];
        $arr["address"] = $response['address'];
        $arr['siteroot']=$app_path;
    }else{
        $arr["success"] = '0';
        $arr['siteroot']='0';
        $arr["msg"] = "Invalid OTP.";
    }
}
print json_encode($arr);
exit;