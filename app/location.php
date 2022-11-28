<?php
require_once '../includes/db_include.php';
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
$path="https://easysalon.in/new_salon_demo/app/images/";

$arr=[];
$arr['success']=1;
$system=query("select * from system WHERE branch_id='".$_GET['branch_id']."'",array(),$conn)[0];
 
 if(!empty($_GET['contact'])){
    $to      = $system['email'];
    $subject = 'Our Saloon';
    $message = $_GET['message'];
    $headers = 'From: '.$_GET['email']. "\r\n" .
    'Name : '.$_GET['name'] . "\r\n" .
    'Contact number : '.$_GET['contact'] . "\r\n" .
    'Email id: '.$_GET['email'] . "\r\n" .
    'X-Mailer: PHP/' . phpversion();

     if(mail($to, $subject, $message, $headers)){
        $arr['msg'].="Email sent successfully";     
     }else{
         $arr['success']=0;
        $arr['msg'].="Invalid email.";
     }
     echo json_encode($arr);
     exit(0);
 }
 
 $arr['salon_name']=$system['salon'];
 $arr['phone']=$system['phone'];
 $arr['website']=$system['website'];
 $arr['facebook']=$system['facebook'];
 $arr['email']=$system['email'];
 $arr['address']=$system['address'];
 $arr['map_location']=$system['map_location'];
 
 
 



   echo json_encode($arr);
?>