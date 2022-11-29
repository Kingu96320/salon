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
session_Start();
$data = json_decode(file_get_contents("php://input"), true);
$mobile = $data['mobile'];
  
$client=query("select * from client where  cont='".$mobile."'",[],$conn)[0];
$client_temp=query("select * from app_bookings_clients where  contact_no='".$mobile."'",[],$conn)[0];

if(!empty($client)){
        $otp = mt_rand(100000, 999999);
        
        $sms_data = array(
    	    'otp' => $otp
    	);
        send_sms($client['cont'],$sms_data,'app_appointment_booking_otp');
    
        query("INSERT INTO otp_app set otp=:otp,cid=:cid,name=:name,mobile=:mobile,status=:sts",[
            "otp"=>$otp,
            "cid"=>$client['id'],
            "name"=>$client['name'],
            "mobile"=>$client['cont'],
            "sts"=>0,
            ],$conn);
            
        $arr["success"] = '1';
        $arr["msg"] = "Login Successfull!";
        $arr["cid"] = $client['id'];
        $arr["name"] = $client['name'];
        $arr["mobile"] = $client['cont'];
        $arr["address"] = $client['address'];
        $arr["old_client"]= $client['old_client'];
        $arr['siteroot']=$app_path;
		
}else if($client_temp){
        $client=$client_temp;
        $otp = mt_rand(100000, 999999);
        
        $sms_data = array(
    	    'otp' => $otp
    	);
        send_sms($client['contact_no'],$sms_data,'app_appointment_booking_otp');
        
        query("INSERT INTO otp_app set otp=:otp,cid=:cid,name=:name,mobile=:mobile,status=:sts",[
            "otp"=>$otp,
            "cid"=>$client['cid'],
            "name"=>$client['fname'],
            "mobile"=>$client['contact_no'],
            "sts"=>0,
            ],$conn);
            
        $arr["success"] = '1';
        $arr["msg"] = "Login Successfull!";
        $arr["cid"] = $client['cid'];
        $arr["name"] = $client['name'];
        $arr["mobile"] = $client['contact_no'];
        $arr["address"] = $client['address'];
        $arr["old_client"]=0;
        $arr['siteroot']=$app_path;
}else{
	
     $arr["success"] = '0';
     $arr["msg"] = "Login invalid!";
     $arr['siteroot']=0;
	 
}
echo json_encode($arr);
 
 
  