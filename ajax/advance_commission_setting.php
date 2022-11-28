<?php 
include_once '../includes/db_include.php';
$branch_id = $_SESSION['branch_id'];
// To get json objects as post data.

$json = file_get_contents('php://input');
$data = json_decode($json);

// function for advance service commission setting

if ($data->module && $data->module == "advsercomm") {
	$return_arr = array();
	$uid = trim($data->uid);
	$pr_id = trim($data->pr_id);
	$comm_data = $data->comm_data;
	query("UPDATE `service_provider_advance_commission_setting` set `status`='1' where `uid`='$uid' and sp_id='$pr_id' and `commission_type`='1' and branch_id='".$branch_id."'",[],$conn);
	for ($i=0; $i < count($comm_data); $i++) {

		$id = trim($comm_data[$i][0]);
		$from_price = trim($comm_data[$i][1]);
		$to_price = trim($comm_data[$i][2]);
		$commission_rate = trim($comm_data[$i][3]);
		$commission_type = trim($comm_data[$i][4]);

		if($id != ''){
			query("UPDATE `service_provider_advance_commission_setting` set `from_price`='$from_price', `to_price`='$to_price', `commission_rate`='$commission_rate', `commission_type`='$commission_type', `status`='0'  where `uid`='$uid' and `sp_id`='$pr_id' and `id`='$id' and branch_id='".$branch_id."'",[],$conn);
		} else {
			query("INSERT INTO `service_provider_advance_commission_setting` set `sp_id`='$pr_id', `uid`='$uid', `from_price`='$from_price', `to_price`='$to_price', `commission_rate`='$commission_rate', `commission_type`='$commission_type', `status`='0', `branch_id`='".$branch_id."'",[],$conn);
		}

	}

	$_SESSION['t']  = 1;
	$_SESSION['tmsg']  = "Service commission setting saved Successfully";
	$return_arr['status'] = 1;
	$return_arr['message'] = "success";
	echo json_encode($return_arr);
}


// function for advance product commision setting

if ($data->module && $data->module == "advprocomm") {
	$return_arr = array();
	$uid = trim($data->uid);
	$pr_id = trim($data->pr_id);
	$comm_data = $data->comm_data;
	query("UPDATE `service_provider_advance_commission_setting` set `status`='1' where `uid`='$uid' and sp_id='$pr_id' and `commission_type`='2' and branch_id='".$branch_id."'",[],$conn);
	for ($i=0; $i < count($comm_data); $i++) {

		$id = trim($comm_data[$i][0]);
		$from_price = trim($comm_data[$i][1]);
		$to_price = trim($comm_data[$i][2]);
		$commission_rate = trim($comm_data[$i][3]);
		$commission_type = trim($comm_data[$i][4]);

		if($id != ''){
			query("UPDATE `service_provider_advance_commission_setting` set `from_price`='$from_price', `to_price`='$to_price', `commission_rate`='$commission_rate', `commission_type`='$commission_type', `status`='0'  where `uid`='$uid' and `sp_id`='$pr_id' and `id`='$id' and branch_id='".$branch_id."'",[],$conn);
		} else {
			query("INSERT INTO `service_provider_advance_commission_setting` set `sp_id`='$pr_id', `uid`='$uid', `from_price`='$from_price', `to_price`='$to_price', `commission_rate`='$commission_rate', `commission_type`='$commission_type', `status`='0', `branch_id`='".$branch_id."'",[],$conn);
		}
	}

	$_SESSION['t']  = 1;
	$_SESSION['tmsg']  = "Product commission setting saved Successfully";
	$return_arr['status'] = 1;
	$return_arr['message'] = "success";
	echo json_encode($return_arr);
}

?>