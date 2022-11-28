<?php
	include "../includes/db_include.php";
	$branch_id = $_SESSION['branch_id'];
	if (isset($_GET['client_id']) && $_GET['client_id'] >0 ){
		$referral_code = $_GET['referral_code'];
		$cid = $_GET['client_id'];
		echo '{"success":"1","reward_points":'.json_encode(get_reward_points($cid,$referral_code,$inv=0)).'}';
	}else{
		echo '{"success":"0"}';
	}
?>