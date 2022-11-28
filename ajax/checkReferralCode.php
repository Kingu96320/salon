<?php
	include_once '../includes/db_include.php';
	$branch_id = $_SESSION['branch_id'];
	if (isset($_GET['referral_code'])){ 
		$referral_code = $_GET['referral_code'];
		$client_id = $_GET['client_id'];
		$chk_referral_code=query("SELECT 1 FROM `client` WHERE active='0' and referral_code='$referral_code' and branch_id='".$branch_id."'",[],$conn);
		if($chk_referral_code){
			$result=query("SELECT 1 FROM `client` WHERE active='0' AND id='$client_id' AND referral_code='$referral_code' and branch_id='".$branch_id."'",[],$conn)[0]; 
			$sql_used_referral_code=query("SELECT count(referral_code) as urc FROM `invoice_".$branch_id."` where client='$client_id' and `referral_code`='$referral_code' and active=0 and branch_id='".$branch_id."'",[],$conn)[0]; 
			if($result){
				echo '{"status":"1"}';
			}else if($sql_used_referral_code['urc'] > 0){ 
				echo '{"status":"2"}';
			}else {
				echo '{"status":"0"}';
			}
		} else {
			echo '{"status":"1"}';
		}
	}
?>