<?php
require_once '../includes/db_include.php';
include("../../salonSoftFiles_new/send_sms.php");
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$data = json_decode(file_get_contents("php://input"), true);

$system=query("select * from system",array(),$conn)[0];  
$arr=[];
$arr['success']=1;
   
if(!empty($data['confirm_booking'])){

    //$cid=query("select cid from app_bookings_clients where fname='".$_GET['full_name']."' and contact_no='".$_GET['contact_no']."'",array(),$conn)[0]['cid'];

  $branch_id = $data['branch_id'];
  $doa = date("Y-m-d",strtotime($data['book_date']));
  $apptime = date("H:i:s",strtotime($data['book_time']));

  $get_client = query_by_id("SELECT id FROM client WHERE cont='".$data['contact_no']."' AND active='0'",[],$conn)[0]['id'];
  $get_client_name = query_by_id("SELECT name FROM client WHERE cont='".$data['contact_no']."' AND active='0'",[],$conn)[0]['name'];
  if($get_client_name != ''){
      $name = $get_client_name;
  }
  if($get_client == ''){
      $get_client = get_insert_id("INSERT INTO `client` set `name`=:name,`cont`=:cont,`gst`=:gst,`gender`=:gender,`dob`=:dob,`aniversary`=:aniversary,`active`=:active, `branch_id`='".$branch_id."'",[
        'name'  =>$data['full_name'],
        'cont'  =>$data['contact_no'],
        'gst'   =>'',
        'gender'=>'',
        'dob'   =>'',
        'aniversary'=>'',
        'active' =>0
    ],$conn);
  }
  $appdate = date('Y-m-d');
  $total_amount = 0;
  //$total_amount = $total_amount+$service_data['price'];
  $invoice_id = get_insert_id("INSERT INTO `app_invoice_".$branch_id."`(`client`,`doa`,`itime`,`role`,`dis`,`disper`,`tax`,`taxtype`,`pay_method`,`total`,`subtotal`,`bmethod`,`paid`,`due`,`notes`,`type`,`status`,`details`,`appdate`,`active`,`appuid`,`app_from`) VALUES ('$get_client','$doa','$apptime','3','".CURRENCY.",0','0','0','3','0','0','0','0','0','0','Mobile appointment','1','Pending','','$appdate',0,'0','2')",[],$conn);
  if($data['ids']){
      foreach($data['ids'] as $key=>$val){
          $res['iddd'] = $val;
          $service_data2 = query("SELECT name, price, cat, duration FROM service WHERE id='".$val."' AND active = '0'",[],$conn)[0];
          $total_amount = $total_amount+$service_data2['price'];
          $app_inv_item_id = get_insert_id("INSERT INTO `app_invoice_items_".$branch_id."` set `iid`='$invoice_id',`client`='$get_client',`service`='sr,".$val."',`quantity`='1',`staffid`='0',`disc_row`='".CURRENCY.",0',`price`='".$service_data2['price']."',`type`='Service',`start_time`='0000-00-00 00:00:00',`end_time`='0000-00-00 00:00:00',`app_date`='$doa',`active`=0",[],$conn);
          if($provider == ''){
              query("INSERT INTO `app_multi_service_provider` set `iid`='$invoice_id',`aii_staffid`='$app_inv_item_id',`service_cat`='".$service_data2['cat']."',`service_name`='sr,".$val."',`service_provider`='0',`status`='1'",[],$conn);
          } else {
              query("INSERT INTO `app_multi_service_provider` set `iid`='$invoice_id',`aii_staffid`='$app_inv_item_id',`service_cat`='".$service_data2['cat']."',`service_name`='sr,".$val."',`service_provider`='0',`status`='1'",[],$conn);
          }
      }
  }
  
  query("UPDATE app_invoice_$branch_id SET total = '".$total_amount."', subtotal = '".$total_amount."', due = '".$total_amount."' WHERE id = '".$invoice_id."'",[],$conn);
  query("DELETE FROM web_otp WHERE phone_number = '".$data['contact_no']."' AND otp='".$otp."' AND status = '1'",[],$conn);
  send_sms($data['contact_no'],"Thank You ".$data['full_name']."\nYour Appointment is booked on ".date('d-m-Y',strtotime($doa))." at ".date('h:i a',strtotime($apptime)).". \n".systemname());

   // Old code

  // $cid=get_insert_id("insert into app_bookings_clients set fname='".$data['full_name']."',contact_no='".$data['contact_no']."',address='".$data['address']."',date_time='".date("Y-m-d",strtotime($data['book_date']))." ".date("H:i:s",strtotime($data['book_time']))."'",array(),$conn);
    
  //   if($data){
  //       foreach($data['ids'] as $key=>$val){
  //           query("insert into app_bookings set service_id='".$val."',cid='".$cid."',booking_date='".$data['book_date']."',booking_time='".$data['book_time']."' ",array(),$conn);
  //       }
  //   }
  //  send_sms($data['contact_no'],"Thank you ".$data['full_name']." \nYour Appointment is booked on ".date('d-m-Y',strtotime($data['book_date']))." at ".date('h:i a',strtotime($data['book_time']))." \n".systemname().".");


   $arr['msg']="Booking successfull.";    
   $arr['cid']=$cid;    
   echo json_encode($arr);
   exit(0);
}

$servicecat=query("select * from servicecat where active=0",array(),$conn);

if($servicecat){
  foreach($servicecat as $data){
    $service=query("select * from service where cat='".$data['id']."' and active=0",array(),$conn);
		$arr['services'].='<div class="dropdown-menu">
				<a href="#" class="dropdown-item dropdown-toggle bg-salon"><em style="color:#fff;">'.$data['cat'].' </em><i class="ion-android-add"></i></a>
				<div class="dropdown-content bg-salon-silver">';
		if($service){
			foreach($service as $da){
			$arr['services'].='<a href="javascript:cart('."'".$da['name']."'".','."'".$da['duration']."'".','."'".$da['price']."'".','."'".$da['id']."'".')" class="dropdown-item"><em><strong>'.$da['name'].'</strong></em><br><em> 0.'.$da['duration'].' HRS  | INR '.round($da['price'],2).'</em><i class="ion-ios-cart"></i></a>';
					}
				}
		$arr['services'].='</div>
		</div>'; 
  }
}	

echo json_encode($arr);
?>