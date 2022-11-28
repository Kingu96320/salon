<?php
include_once './includes/db_include.php';
$branch_id = $_SESSION['branch_id'];
if(!empty($_GET["cat"])) {
	$num = $_GET["cat"];
	$sql2="SELECT * from servivcecat where cat='$num' and branch_id='".$branch_id."'";
	$result2=query_by_id($sql2,[],$conn);
	if($result2) 
	{
			//echo "Category";
	}else{
		 echo "Invalid Category";
	}
}
?>
