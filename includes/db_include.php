<?php 
//if (substr($_SERVER['HTTP_HOST'], 0, 4) === 'www.') {
//    header('Location: http'.(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS']=='on' ? 's':'').'://' . substr($_SERVER['HTTP_HOST'], 4).$_SERVER['REQUEST_URI']);
//    die();
//}
require 'functions.php';
require 'db.php';

session_start();
ob_start();

$dt = array();// this will house all the variables that we require in views
$conn = connect($config);
if( !$conn ) die('could not connect');
date_default_timezone_set("Kuwait/Riyadh");
query('SET @@session.time_zone = "+03:00"', [], $conn);
//code to store current time to variable
//$dateandtime = date('d-m-Y h:i:s a');
$save_sms = 1;

require_once 'constants.php';

function send_sms($contact,$content,$template_type){
    $ch = curl_init();
    $url = SMS_API_URL."transactional_sms.php";
    $postData = array(
      'action' => 'transactional_sms',
      'contact' => $contact, 
      'content' => $content, 
      'template_type' => $template_type, 
      'sender_id' => SENDER_ID, 
      'software_link' => BASE_URL
    );
    
    curl_setopt_array($ch,
      array(
        CURLOPT_URL => $url,
        CURLOPT_POST       => true,
        CURLOPT_POSTFIELDS => json_encode($postData),
        CURLOPT_HTTPHEADER => array('Content-Type:application/json'),
        CURLOPT_RETURNTRANSFER     => true,
      )
    );
    
    $data = curl_exec($ch);
    curl_close($ch);
    return $data;
}
