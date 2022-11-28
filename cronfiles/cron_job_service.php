<?php
include_once './includes/db_include.php'; 
$branch_id = $_SESSION['branch_id'];
$date = date('m-d');
$sql1="SELECT * FROM `client` where active=0 and DATE_FORMAT(dob,'%m-%d')=DATE_FORMAT(CURDATE(),'%m-%d')";
$result1=query_by_id($sql1,[],$conn);
$systemname=systemname(); 
if($result1){
    foreach($result1 as $row1) {
    $client_name=$row1['name']; 
    $client_contact=$row1['cont'];
    $msg ="Happy Birthday ".$client_name."\n FROM ".$systemname;
    send_sms($client_contact,$msg);
    }
} 

$date=date('Y-m-d');
$current_time=strtotime(date("H:i"));
$query_app_sms=query_by_id("select ai.itime,ai.doa,c.cont from app_invoice_".$branch_id." ai inner join client c on ai.client=c.id where ai.active='0' and DATE(appdate)='$date'",[],$conn);
//echo $query_app_sms;
foreach($query_app_sms as $data)
{  
    //echo $data['itime']."<br>";
    $app_time=strtotime($data['itime']);
    $app_time1 = date("h:i A", strtotime($data['itime']));
    $app_date1 = date("d-M-Y", strtotime($data['doa']));
    $cont=$data['cont'];
    $min = floor((($app_time- $current_time)/60));
    if($min <= 60)
    {
		$sms_data = array(
	        'date' => $app_date1,
	        'time' => $app_time1,
	        'salon_name' => systemname()
	    );
		
		send_sms($cont,$sms_data,'appointment_reminder_sms');
		
	}
}

?>