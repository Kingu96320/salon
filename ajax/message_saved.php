<?php
include "../includes/db_include.php";
$branch_id = $_SESSION['branch_id'];
if(isset($_GET['update_message']) && $_GET['update_message'] > 0){
		$eid = $_GET['update_message'];
		$message = $_POST['message'];
		query("UPDATE `messages` set `message`=:message where id=:id and branch_id='".$branch_id."'",['message'=>$message,'id'=>$eid],$conn);
		 
		echo '{"data-inserted":"1"}';
}else if($_GET['did'] > 0 && isset($_GET['did'])){
		$did = $_GET['did'];
		 
		query("UPDATE `messages` set `status`=0 where id=:id and branch_id='".$branch_id."'",['id'=>$did],$conn);
		 
		echo '{"data-deleted":"1"}';
}else if(isset($_POST['message']) && $_POST['message'] !='' && !isset($_GET['update_message']) ){
	$message = $_POST['message'];
	query("INSERT INTO `messages` set `message`=:message, `branch_id`='".$branch_id."'",['message'=>$message],$conn);
	echo '{"data-inserted":"1"}';
}else if(($_POST['eid']) && $_POST['eid'] > 0){

	$eid = $_POST['eid'];
	$edit = query_by_id("SELECT * from `messages` where id='$eid' and status='1' and branch_id='".$branch_id."'",[],$conn);
	echo JSON_ENCODE($edit);

}
