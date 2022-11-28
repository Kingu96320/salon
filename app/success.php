<?php
	error_reporting(E_ALL);
	ini_set("display_errors", 1);
	require_once '../includes/db_include.php';

	$status=$_POST["status"];
	$firstname=$_POST["firstname"];
	$amount=$_POST["amount"];
	$txnid=$_POST["txnid"];
	$posted_hash=$_POST["hash"];
	$productinfo=$_POST["productinfo"];
	$email=$_POST["email"];
	$salt="0SiDlXnx";
	$phone = $_POST['phone'];
    $package_id = $_POST['package_id'];
///	echo '<pre>';
	///print_r($_POST);

	If (isset($_POST["additionalCharges"])) {
		$additionalCharges=$_POST["additionalCharges"];
        $retHashSeq = $additionalCharges.'|'.$salt.'|'.$status.'|||||||||||'.$email.'|'.$firstname.'|'.$productinfo.'|'.$amount.'|'.$txnid.'|'.$key;
	}
	else{	  
		$retHashSeq = $salt.'|'.$status.'|||||||||||'.$email.'|'.$firstname.'|'.$productinfo.'|'.$amount.'|'.$txnid.'|'.$key;
	}
	$hash = hash("sha512", $retHashSeq);
	
	if(empty($posted_hash)) {
		echo "Invalid Transaction. Please try again";
	}else{
        $package_info=explode(",",$postdata['productinfo']);
		$cid=query("select id from client where cont='".$phone."'",array(),$conn)[0]['id'];
		
		$aid=get_insert_id("INSERT INTO `invoice` set `client`='".$cid."',`doa`='".date("Y-m-d")."',`subtotal`='".$amount."',`total`='".$amount."',`dis`='INR,0',`paid`='".$amount."',`tax`='0','due='0',`bpaid`='".$amount."',`bmethod`='5',`pay_method`='5',`billdate`='".date('Y-m-d')."',`status`='Bill',`active`='0'",[],$conn);
		
		$app_inv_item_id = get_insert_id("INSERT INTO `invoice_items` set `iid`='$aid',`client`='".$cid."',`service`='pa".$package_id."',`quantity`='1',`staffid`='0',`disc_row`='pr,0',`price`='".$amount."',`type`='Package',`bill`=1,`active`=0",[],$conn); 
		
		get_insert_id("INSERT INTO  `invoice_multi_service_provider` set `ii_id`='$app_inv_item_id',`inv`='$aid',`service_name`='pa".$package_id."',`status`='1'",[],$conn);
		
        echo   $msg = ".<h4>Thank you for purchasing our package.</h4>";
	}         
?>	