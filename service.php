<?php
include "./includes/db_include.php";
$branch_id = $_SESSION['branch_id'];
if (isset($_GET['term'])){
	$return_arr = array();
	$term = $_GET['term'];
	$val = str_replace("%20", " ", $term);
		$sql="SELECT * FROM `service` WHERE name LIKE '%$val%' and active=0 and branch_id='".$branch_id."'";
		$result=query_by_id($sql,[],$conn);
		foreach($result as $row) 
		{
			$return_arr[] =  $row['name'];
		}
		
    echo json_encode($return_arr);
}
?>