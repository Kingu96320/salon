<?php
include_once './includes/db_include.php';
$branch_id = $_SESSION['branch_id'];
if (isset($_GET['ser'])){
	$res = array();
	$term = $_GET['ser'];
	$val = str_replace("%20", " ", $term);
		$sql="SELECT id,name,price FROM `service` WHERE name like '%$val%'";
		$result=query_by_id($sql,[],$conn);
		if ($result) 
		{
			foreach($result as $row)
			{
			//echo  $row['price'];
			$res[] = $row; 
			}
		}else{
			$res[] = "No Column Found";
		}
		echo json_encode($res);
}
?>