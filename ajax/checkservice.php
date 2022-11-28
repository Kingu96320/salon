<?php
	include_once '../includes/db_include.php';
	$branch_id = $_SESSION['branch_id'];
	if(!empty($_POST["ser"])) 
	{
		$id = $_POST['id'];
		$num = $_POST["ser"];
		$sql2="SELECT * from `service` where replace(name,' ','')='$num' and active='0' and id <> '$id'";
		$result2=query_by_id($sql2,[],$conn);
		if($result2){
			echo "1";
		} 
	}
	else if(!empty($_POST["category"]) && $_POST['cat_id'] == ''){
		$num = $_POST["category"];
		$sql2="SELECT * from `service` where replace(cat,' ','')='$num' and active='0'";
		$result2=query_by_id($sql2,[],$conn);
		if($result2){
			echo "1";
		} 
	}else if(!empty($_POST["products"]) && $_POST["product_id"] == ''){
	
		$num = $_POST["products"];
		$id = $_POST['id'];
		$sql2="SELECT * from `products` where replace(name,' ','')='$num' and active='0' and id <>'$id'";
		$result2=query_by_id($sql2,[],$conn);
		if($result2){
			echo "1";
		} 
	}

	// total list of services of single service provider

	if(isset($_POST['sp_id']) && $_POST['sp_id'] != 0){
		$sp_id = $_POST['sp_id'];	
		$sql2="SELECT distinct s.id, s.name from `service` s left join `invoice_multi_service_provider` imsp on s.id = SUBSTRING_INDEX(imsp.service_name,',',-1) left join invoice_".$branch_id." i on imsp.inv = i.id where imsp.service_provider = '$sp_id' and i.active='0' order by s.id asc";
		$result2=query_by_id($sql2,[],$conn);
		if($result2){
			echo json_encode($result2);
		} 
	}

	if(isset($_POST['action']) && $_POST['action'] == 'checkinvoice'){
		$invoice_no = trim($_POST['invoice_no']);
		$vendor_id = trim($_POST['vendor_id']);
		if($vendor_id != '' || $vendor_id != 0){
			$append = " and id != '$vendor_id' ";
		} else {
			$append = '';
		}

		$sql = "SELECT inv from purchase where inv='$invoice_no' ".$append." and active=0 and branch_id='".$branch_id."'";
		$result = query_by_id($sql,[],$conn);
		if($result){
			echo '1'; // invoice number found
		} else {
			echo "0"; // invoice number not found
		}
	}


	// function to filter commission for single service provider based on start and end date, service types, and commission type(service, Product).

	// if(isset($_POST['filter']))
?>
