<?php 

require_once '../includes/db_include.php';
require_once 'path.php';

require_once("../../salonSoftFiles_new/send_sms.php");

error_reporting(E_ALL);
ini_set("display_errors", 1);
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
error_reporting(E_ALL);
ini_set("display_errors", 1);
$data = json_decode(file_get_contents("php://input"), true);
if(isset($data['set_otp'])){
        $otp = mt_rand(1000, 9999);
        $sms_data = array(
	        'otp' => $otp
	    );
        send_sms($data['cont'],$sms_data,'appointment_booking_otp');

        query("INSERT INTO otp_app set otp='".$otp."',name='".$data['name']."',mobile='".$data['cont']."',status=0",[],$conn);
        
        $arr['otp']=$otp;
        $arr['success']=1;
}

if(isset($data['check_otp'])){
        $check=query("select 1 from otp_app where otp='".$data['otp']."' and mobile='".$data['cont']."' and status=0",array(),$conn);
        
        if($check){
        $arr['success']=1;
            query("update otp_app set status=1 where otp='".$data['otp']."'",array(),$conn);
        }else{
        $arr['success']=0;
        }
        
}



print json_encode($arr);
exit;