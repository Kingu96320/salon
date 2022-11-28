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

//$booking_dates=query("select * from app_bookings ab inner join app_bookings_clients abc on(ab.cid=abc.cid) where abc.contact_no='".$_GET['mobile']."' group by booking_date order by booking_date desc",array(),$conn);
 
			$aboutus_data=query("select * from app_aboutus where status=1",array(),$conn);
            if($aboutus_data){
                foreach($aboutus_data as $data){
                $arr['cnt'].=$data['about_us'];
                }				
            }
		

   echo json_encode($arr);
?>