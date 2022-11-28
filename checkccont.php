<?php
	include_once './includes/db_include.php';
	$branch_id = $_SESSION['branch_id'];
	if(!empty($_GET["p"])) {
		$num = $_GET["p"];
		$val = str_replace("%20", " ", $num);
		$sql2="SELECT * from client where cont='$val' and active='0'";
		$result2=query_by_id($sql2,[],$conn);
		if($result2) {
			echo "1";
		}
	}
?>
