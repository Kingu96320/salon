<?php
include_once './includes/db_include.php';
$branch_id = $_SESSION['branch_id'];
if(!empty($_GET["pack"])) {
	$num = $_GET["pack"];
	$sql2="SELECT * from packages where name='$num' and branch_id='".$branch_id."'";
	$result2=query_by_id($sql2,[],$conn);
	if($result2)
	{
			 echo "Already Exist";
	}else{
		
	}
}
?>
