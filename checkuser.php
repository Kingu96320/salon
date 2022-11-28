<?php
include_once './includes/db_include.php';
$branch_id = $_SESSION['branch_id'];
if(!empty($_GET["usr"])) {
	$num = $_GET["usr"];
	$sql="SELECT * from user where username='$num' and branch_id='".$branch_id."'";
	$result=query_by_id($sql,[],$conn);
	if($result) {
			 echo "Already Exist";
	}else{
		
	}
}
?>
