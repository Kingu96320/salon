<?php
include_once '../includes/db_include.php';
$branch_id = $_SESSION['branch_id'];
if (isset($_GET['term'])){
	$return_arr = array();
	$term = $_GET['term'];

		$sql="SELECT id,name,cont FROM `beauticians` WHERE cont LIKE '%$term%' or name LIKE '%$term%' and active=0 and branch_id='".$branch_id."'";
		$result=query_by_id($sql,[],$conn);
		$i=0;
		foreach($result as $row) {
			$return_arr[$i]['value'] =  $row['name']."(".$row['cont'].")";
			$return_arr[$i]['name'] =  $row['name'];
			$return_arr[$i]['id'] =  $row['id'];
			$i++;
		}
		
    echo json_encode($return_arr);
}
?>