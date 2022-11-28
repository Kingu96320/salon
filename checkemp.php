<?php
include_once './includes/db_include.php';
$branch_id = $_SESSION['branch_id'];
if(!empty($_GET["con"])) 
{
	$num = $_GET["con"];
	if(!empty($_GET["uid"])){
		$uid = $_GET["uid"];
		$sql2="SELECT * from employee where cont='$num' and id='$uid' and active=0 and branch_id='".$branch_id."'";
		$result = query_by_id($sql2,[],$conn);
		if($result){
			echo "Same user";
		} else {
			echo "Already Exist";
		}
	} else {
		$sql2="SELECT * from employee where cont='$num' and active=0 and branch_id='".$branch_id."'";
		$result = query_by_id($sql2,[],$conn);
		if($result){
			echo "Already Exist";
		}
	}
}
?>
