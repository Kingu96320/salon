<?php
include_once '../includes/db_include.php';
$branch_id = $_SESSION['branch_id'];
if (isset($_GET['term'])){
	$return_arr = array();
	$term = $_GET['term'];
	$date = date('Y-m-d');
	$val = str_replace("%20", " ", $term);
		$sql="SELECT * FROM `purchase_items` WHERE name LIKE '%$val%' and exp_date > '".$date."' active=0 and branch_id='".$branch_id."'";
		$result=mysqli_query($con,$sql);
		while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
			$return_arr[] =  $row['name'];
		}
		
    echo json_encode($return_arr);
}
?>