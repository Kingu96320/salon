<?php
	include_once '../includes/db_include.php';
	$branch_id = $_SESSION['branch_id'];
	if (isset($_GET['term'])){
		$return_arr = array();
		$term = $_GET['term'];
		$val = str_replace("%20", " ", $term);
		
		$sql = "select p.id,p.name as value,p.volume,u.name as unit,p.unit as unit_id from `products` p LEFT JOIN `units` u on u.id=p.unit WHERE p.name LIKE '%$val%' and p.active='0'";
		$result = query_by_id($sql,[],$conn);
		foreach($result as $row){
			$return_arr[] =  $row;
		}
		
		echo json_encode($return_arr);
	}
?>