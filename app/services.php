<?php
require_once '../includes/db_include.php';
require_once 'path.php';

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$arr=[];
$arr['success']=1;
$arr['current_time']=date("h:i A",strtotime("+2 hours"));
$arr['currentTime']=date("H:i:s",strtotime("+2 hours"));

$arr['today']=date("Y-m-d");
$arr['tomorow']=date("Y-m-d",strtotime("+1 days"));
$arr['day_after']=date("Y-m-d",strtotime("+2 days"));
$arr['day_after_name']=date("l",strtotime("+2 days"));


$system=query("select * from system",array(),$conn)[0]; 
if(!empty($_GET['search'])){
    $search=" and cat like '%".$_GET['search']."%'";
}else{
    $search="";
}
    $servicecat=query("select * from servicecat where active=0 $search",array(),$conn);
  
    if($servicecat){
        foreach($servicecat as $data){
          
       $service=query("select * from service where cat='".$data['id']."' and active=0",array(),$conn);
    
                     $arr['services'].='<div class="dropdown-menu interest-box bg-salon animate-zoom shadow-salon">
					<a href="#" class="dropdown-item dropdown-toggle "><em style="color:#fff;">'.$data['cat'].' </em><i class="ion-android-add"></i></a>
					<div class="dropdown-content">';
					if($service){
						foreach($service as $da){
					$arr['services'].='<a href="javascript:cart('."'".$da['name']."'".','."'".$da['duration']."'".','."'".$da['price']."'".','."'".$da['id']."'".')" class="dropdown-item"><em><strong>'.$da['name'].'</strong></em><em> 0.'.$da['duration'].' HRS  | INR '.round($da['price'],2).'</em><i class="ion-ios-cart"></i></a>';
						}
					}
	 $arr['services'].='</div></div>';
    }
} 
 
if(empty($arr['services'])){
    $arr['services']="No package available";
}	


   echo json_encode($arr);
?>