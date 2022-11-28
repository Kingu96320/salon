<?php
include "./includes/db_include.php";
$branch_id = $_SESSION['branch_id'];
if(isset($_SESSION['uid'])){
	echo '<meta http-equiv="refresh" content="0; url=dashboard.php" />';
}
$scat = $_POST['scat'];
query_by_id("INSERT INTO `servivcecat`(`cat`, `active`,`branch_id`) VALUES ('$scat',0,'$branch_id')",[],$conn);
echo "Service Category Add Successfullt";
?>