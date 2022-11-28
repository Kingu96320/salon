<?php
	include "../includes/db_include.php";
	// $query = "UPDATE app_invoice set active='0' where appdate < CURDATE() and active = '1'";
	$query = "select appdate from app_invoice where appdate < CURDATE() and active = '1'";
	$result = query_by_id($query,[],$conn);
	echo "<pre>";
	print_r($result);
?>