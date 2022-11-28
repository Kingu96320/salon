<?php
include "./includes/db_include.php";
$branch_id = $_SESSION['branch_id'];
if (isset($_GET['ser'])){
	$res = array();
	$term = $_GET['ser'];
	$val = str_replace("%20", " ", $term);
	$sql="SELECT id,name,price,duration as durr FROM `service` WHERE name like '%$val%' and branch_id='".$branch_id."'";
	$result=query_by_id($sql,[],$conn);
	if ($result) 
	{
		//echo  $row['price'];
		foreach($result as $row)
		{
		$res[] = $row; 
		}
	}else{
		$res[] = "No Service Exist";
	}
	echo json_encode($res);
}
?>