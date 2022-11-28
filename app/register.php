<?php 
error_reporting(E_ALL);
ini_set("display_errors", 1);
require_once '../includes/db_include.php';
require_once("../../salonSoftFiles_new/send_sms.php");
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
session_Start();
$data = json_decode(file_get_contents("php://input"), true);
//query("insert into set fname='".$data['name']."',contact_no='".$data['mobile']."',email='".$data['email']."',address='".$data['address']."' ",array(),$conn);
$mobile = $data['mobile'];

//$client_temp=query("select * from app_bookings_clients where  contact_no='".$mobile."'",[],$conn)[0];

if(!empty($data['register'])){
            $client=$data;
            $check=query("select 1 from app_bookings_clients where  contact_no='".$mobile."'",[],$conn)[0];
            if(!$check){
                $otp = mt_rand(100000, 999999);
                send_sms($mobile,($otp."is your OTP for booking Appointment."));
                query("INSERT INTO otp_app set otp=:otp,cid=:cid,name=:name,mobile=:mobile,status=:sts",[
                    "otp"=>$otp,
                    "cid"=>'',
                    "name"=>$client['name'],
                    "mobile"=>$client['mobile'],
                    "sts"=>0,
                    ],$conn);
                    
                $arr["success"] = '1';
                $arr["msg"] = "Register Successfull!";
                $arr["cid"] = "0";
                $arr["name"] = $client['name'];
                $arr["mobile"] = $client['mobile'];
                $arr["address"] = $client['address'];
                $arr["email"] = $client['email'];
                $arr["old_client"]=0;
                $arr['siteroot']=$app_path;
            }else{
                 $arr["success"] = '0';
                 $arr["msg"] = "This client is already exists.";
                 $arr['siteroot']=0;
            }
}else if($data['register']==0){
    $otp=$data['otp'];
  //  echo "SELECT cid,mobile,name FROM otp_app  where mobile='".$mobile."' AND otp='".$otp."'";
	$response = query_by_id("SELECT cid,mobile,name FROM otp_app  where mobile='".$mobile."' AND otp='".$otp."' ",[],$conn)[0];

  if($response){
       // if($mobile!='9888629974'){
			query("UPDATE otp_app set status='1' where mobile=:mobile",["mobile"=>$response['mobile']],$conn);
        //}
        
        query("insert into app_bookings_clients set fname='".$data['name']."',contact_no='".$data['mobile']."',email='".$data['email']."',address='".$data['address']."' ",array(),$conn);
        $mobile = $data['mobile'];
        $client=query("select * from app_bookings_clients where  contact_no='".$mobile."'",[],$conn)[0];

        
        $arr["success"] = '1';
        $arr["msg"] = "Login Successfull!";
        $arr["cid"] = $client['cid'];
        $arr["name"] = $client['fname'];
        $arr["mobile"] = $client['contact_no'];
        $arr['email'] = $client['email'];
        $arr["address"] = $response['address'];
        $arr['siteroot']='http://localhost/upsalon/app/';
    }else{
         $arr["success"] = '0';
         $arr["msg"] = "Registeration failed!";
         $arr['siteroot']=0;
    }
    
	 
}else{
     $arr["success"] = '0';
     $arr["msg"] = "Registeration failed!";
     $arr['siteroot']=0;
    
}
echo json_encode($arr);
 