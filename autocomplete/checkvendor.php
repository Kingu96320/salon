<?php
include "../includes/db_include.php";
$branch_id = $_SESSION['branch_id'];
if(!empty($_GET["p"])) {
	$num = $_GET["p"];
	$val = str_replace("%20", " ", $num);
  	$sql2="SELECT * from vendor where cont='$val' and branch_id='".$branch_id."'";
	$result2=query($sql2,[],$conn);
	if($result2) {
		 echo "Already Exist";
	} 
}
?>
