<?php
	include_once './includes/db_include.php';
	$branch_id = $_SESSION['branch_id'];
	if (isset($_GET['term'])){
		$return_arr = array();
		$term = $_GET['term'];
		$i=0;
		$sql="SELECT id,name,cont,gender,dob FROM `client` WHERE active='0' AND (cont LIKE '%$term%' or name LIKE '%$term%') and branch_id='".$branch_id."'";
		$result=query_by_id($sql,[],$conn);
		
		foreach($result as $row) 
		{
			$return_arr[$i]['value'] =  $row['name']."(".$row['cont'].")";
			$return_arr[$i]['name'] =  $row['name'];
			$return_arr[$i]['id'] =  $row['id'];
			$return_arr[$i]['cont'] =  $row['cont'];
			$return_arr[$i]['gender'] =  $row['gender'];
			$return_arr[$i]['dob'] =  $row['dob'];
			$i++;
		}
		
		echo json_encode($return_arr);
		}else if(isset($_POST['cont'])){
		$return_arr = array();
		$term = $_POST['cont'];
		$i=0;
		$sql="SELECT id,name,cont,gender,dob FROM `client` WHERE active='0' AND cont='$term' and branch_id='".$branch_id."'";
		$result=query_by_id($sql,[],$conn);
		
		foreach($result as $row) 
		{
			$return_arr[$i]['value'] =  $row['name']."(".$row['cont'].")";
			$return_arr[$i]['name'] =  $row['name'];
			$return_arr[$i]['id'] =  $row['id'];
			$return_arr[$i]['cont'] =  $row['cont'];
			$return_arr[$i]['gender'] =  $row['gender'];
			$return_arr[$i]['dob'] =  $row['dob'];
			$i++;
		}
		
		echo json_encode($return_arr);
	}
?>