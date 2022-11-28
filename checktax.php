<?php
include_once './includes/db_include.php';
$branch_id = $_SESSION['branch_id'];
if(!empty($_GET["t"])) {
	$num = $_GET["t"];
	$val = str_replace("%20", " ", $num);
	$sql2="SELECT * from tax where title='$val' and active=0 branch_id='".$branch_id."'";
	$result2=query_by_id($sql2,[],$conn);
	if($result2) 
	{
		 echo "Already Exist";
	}else{
		
	}
}
?>
