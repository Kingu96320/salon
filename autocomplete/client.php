<?php
include_once '../includes/db_include.php';
$branch_id = $_SESSION['branch_id'];
if (isset($_GET['term'])){
	$return_arr = array();
	$term = $_GET['term'];
	$i=0;
		$sql="SELECT referral_code,id,name,cont,gender,dob,gst,aniversary,branch_id FROM `client` WHERE active='0' AND (cont LIKE '%$term%' or name LIKE '%$term%')";
		$result=query_by_id($sql,[],$conn);
		if($result){
			foreach($result as $row) 
			{
				$return_arr[$i]['value']	 		=  $row['name']."(".$row['cont'].") - ".branch_by_id($row['branch_id']);
				$return_arr[$i]['name']		 		=  $row['name'];
				$return_arr[$i]['id'] 		 		=  $row['id'];
				$return_arr[$i]['client_branch_id'] =  $row['branch_id'];
				$return_arr[$i]['cont'] 	 		=  $row['cont'];
				$return_arr[$i]['gender'] 	 		=  $row['gender'];
				$return_arr[$i]['dob'] 	     		=  $row['dob'];
				$return_arr[$i]['gst']		 		=  $row['gst'];
				$return_arr[$i]['anniversary'] 		=  $row['aniversary'];
				$return_arr[$i]['referral_code'] 	=  $row['referral_code'];
				$i++;
			}
		}
		
    echo json_encode($return_arr);
}
?>