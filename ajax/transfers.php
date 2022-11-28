<?php
include_once '../includes/db_include.php';
$branch_id = $_SESSION['branch_id'];

if(!isset($_SESSION['user_type'])){
	die();
}

if(isset($_POST['action']) && $_POST['action']=='check_provider_schedule_migrate'){
	$date = $_POST['date'];
	$sp_id = $_POST['sp_id'];

	if(strlen($date) > 10){
		$date = explode("-",$date);
	    $start_date = $date[0];
	    $end_date = $date[1]; 
	    $sdate = isoDate($date[0]);
	    $edate = isoDate($date[1]);			
		$append = " AND ai.doa BETWEEN '".$sdate."' AND '".$edate."'";
	} else {
		$append = " AND ai.doa='".$date."'";
	}
	$result = query_by_id("SELECT ai.doa, ai.itime, ai.client FROM app_multi_service_provider amsp LEFT JOIN app_invoice_".$branch_id." ai on amsp.iid = ai.id WHERE amsp.service_provider = '".$sp_id."'".$append,[],$conn);
	if($result){
		$res['status'] = 0;
		$arr = array();
		foreach($result as $data){
			$sub['doa'] = my_date_format($data['doa']);
			$sub['itime'] = my_time_format($data['itime']);
			$sub['client'] = ucfirst(client_name($data['client']));
			array_push($arr, $sub);
		}
		$res['records'] = $arr;
	} else {
		$res['status'] = 1;
	}
	echo json_encode($res);
}


if(isset($_POST['action']) && $_POST['action'] == 'transfer_service_provider'){
	$sp_id = $_POST['sp_id'];
	$date = $_POST['date'];
	$moved_to_branch = $_POST['moved_to_branch'];
	$transfer_type = $_POST['transfer_type'];

	if(strlen($date) > 12){
		$date = explode("-",$date);
	    $start_date = $date[0];
	    $end_date = $date[1];
	    $sdate = isoDate($date[0]);
	    $edate = isoDate($date[1]);			
		$append = ", start_date='".$sdate."', end_date='".$edate."'";
	} else {
		$append = ", date='".$date."'";
	}

	$query = "INSERT INTO transfered_service_providers SET provider_id='".$sp_id."', transfer_type='".$transfer_type."', moved_from_branch='".$branch_id."', moved_to_branch='".$moved_to_branch."', transfer_date='".date('Y-m-d')."', status='1'".$append;
	$result = get_insert_id($query,[],$conn);
	if($result > 0){
		$res['status'] = 1;
	} else {
		$res['status'] = 0;
	}
	echo json_encode($res);
}

?>