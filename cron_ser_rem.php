<?php
include_once './includes/db_include.php'; 
$date = date('Y-m-d');

$branches = query_by_id("SELECT * FROM salon_branches WHERE status = '1'",[],$conn);
if($branches){
	foreach($branches as $br){
	    $systemname = systemname($br['id']);
		$query = "SELECT DATE_ADD(i.doa, INTERVAL sr.days_interval DAY), i.id as invoice_id, i.doa, c.name as client_name, c.cont, c.id as client_id, s.name, sr.message, sr.days_interval, i.branch_id FROM `invoice_items_".$br['id']."` ii INNER JOIN service_reminder sr ON ii.service = CONCAT('sr',',',sr.service_id) LEFT JOIN `invoice_".$br['id']."` i ON i.id = ii.iid LEFT JOIN service s ON s.id = sr.service_id LEFT JOIN client c ON c.id = i.client WHERE DATE_ADD(i.doa, INTERVAL sr.days_interval DAY) = '".$date."' AND sr.status = 1";
		$result = query_by_id($query,[],$conn);
		if($result){
		    // echo "<pre>";
		    // print_r($result);
		    // echo "</pre>";
		    foreach($result as $res){
		        if(strlen($res['cont']) == 10){
		              $message = str_replace('{name}',$res['client_name'],$res['message']);
		              $message = str_replace('{salon_name}',$systemname,$message);
		              send_sms($res['cont'],$message);
		        } else {
		           
		        }
		    }
		}
	}
}


?>