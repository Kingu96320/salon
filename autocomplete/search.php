<?php
include_once '../includes/db_include.php';
$branch_id = $_SESSION['branch_id'];
if (isset($_GET['term'])){
	$return_arr = array();
	$term = $_GET['term'];
		$i=0;
		$sql="SELECT * FROM `expensecat` WHERE title LIKE '%$term%' and branch_id='".$branch_id."'";
		$result=query_by_id($sql,[],$conn);
		foreach($result as $row) 
		{
			$return_arr[$i]['id'] 	 =  $row['id'];
			$return_arr[$i]['value'] =  $row['title'];
			$return_arr[$i]['cat'] 	 =  $row['title'];
			$i++;
		}	
    echo json_encode($return_arr);
}
?>