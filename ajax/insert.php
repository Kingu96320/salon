<?php 
	include "../includes/db_include.php";
	$branch_id = $_SESSION['branch_id'];
	for($i=0;$i<50;$i++){
		query("INSERT INTO `client` set `name`='arsh',`cont`='8146772559',active='0', branch_id='".$branch_id."'",[],$conn);
	}