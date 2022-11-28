<?php
include "../includes/db_include.php";
$branch_id = $_SESSION['branch_id'];
if (isset($_REQUEST['inv'])){
	 $inv = $_REQUEST['inv'];
	 $aid = $_REQUEST['aii'];

     $sql = "SELECT room_id from `app_invoice_".$branch_id."` where id = '".$inv."'";
     $result = query_by_id($sql,[],$conn)[0];
     
	$arr = array();
	$arr1 = array();
	if($result['room_id'] == ""){
	   
	$sql1="SELECT ai.notes,ai.bill_created_status, c.name as client,GROUP_CONCAT(s.name) as service,b.name as beautician,aii.start_time as start_time, aii.end_time as end_time  FROM `app_multi_service_provider` i
			LEFT JOIN `app_invoice_items_".$branch_id."` aii on aii.id=i.aii_staffid
			left join client c on c.id=aii.client
			left join service s on s.id=SUBSTRING_INDEX(i.service_name,',',-1)
			left join beauticians b on b.id=i.service_provider
			LEFT JOIN `app_invoice_".$branch_id."` ai on ai.id=aii.iid
			WHERE aii.id='".$aid."' AND i.iid='".$inv."' and i.branch_id='".$branch_id."' and aii.active='0'";
// 			group by i.iid,aii.active having i.iid='".$inv."' and aii.active='0'";
	$result1=query_by_id($sql1,[],$conn)[0];
	$arr['client'] = ucfirst($result1['client']);
	$arr['service'] = $result1['service'];
	$arr['beautician'] = $result1['beautician'];
	$arr['notes'] = $result1['notes'];
	$arr['date'] = my_date_format($result1['start_time']);
	$arr['start_time'] = my_time_format($result1['start_time']);
	$arr['end_time'] = my_time_format($result1['end_time']);
	$arr['bill_status'] = $result1['bill_created_status'];
    echo json_encode($arr);
    
	}else{
	   	$sql2="SELECT ai.*, va.*, c.name as client,va.room_id, vr.room_name as roomname, b.name as beautician, ai.notes as notes, va.app_start_time as start_time, va.app_end_time as end_time, ai.bill_created_status   FROM `vip_appointment` va
		    left join client c on c.id=va.allocated_for
			left join beauticians b on b.id=va.allocated_by
			LEFT JOIN `app_invoice_".$branch_id."` ai on ai.id=va.inv_id
			LEFT JOIN `vip_rooms` vr on vr.id= va.room_id
			WHERE va.inv_id='".$inv."' and ai.branch_id='".$branch_id."' and va.allocated='1'";
// 			group by i.iid,aii.active having i.iid='".$inv."' and aii.active='0'";
	$result2=query_by_id($sql2,[],$conn)[0];

	$arr['client'] = ucfirst($result2['client']);
	$arr['service'] = "-";
	$arr['beautician'] = $result2['beautician'];
	$arr['notes'] = $result2['notes'];
	$arr['date'] = my_date_format($result2['start_time']);
	$arr['start_time'] = my_time_format($result2['start_time']);
	$arr['end_time'] = my_time_format($result2['end_time']);
	$arr['bill_status'] = $result2['bill_created_status'];
	$arr['roomname'] = $result2['roomname'];
	
    echo json_encode($arr); 
	}
}
?>