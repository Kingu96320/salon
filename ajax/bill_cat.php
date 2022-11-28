<?php
include_once '../includes/db_include.php';
$branch_id = $_SESSION['branch_id'];
if (isset($_GET['term'])){
	$return_arr = array();
	$term = $_GET['term'];
	$val = str_replace("%20", " ", $term);

		$sql = "select id,cat as value from servicecat WHERE cat LIKE '%$val%' and branch_id='".$branch_id."'";
		$result = query_by_id($sql,[],$conn);
		foreach($result as $row){
			$return_arr[] =  $row;
		}
		
    echo json_encode($return_arr);
}
?>