<?php
include_once '../includes/db_include.php';
$branch_id = $_SESSION['branch_id'];
if (isset($_GET['term'])){
	$return_arr = array();
	$term = $_GET['term'];
	 $inv = getinvstock($term);
	  $pur = getpurstock($term);
	  $productused=productused($term);
		$sum = $pur - $inv - $productused;
	  echo $sum;
}

function getinvstock($pid){
	global $conn;
	global $branch_id;
	$sql="SELECT sum(quantity) as total from `invoice_items` where service=$pid and type='Product' and active=0 and branch_id='".$branch_id."'";
	$result=query_by_id($sql,[],$conn);
	 	
		foreach($result as $row)
		{
		$total= $row['total'];
		}
	 return $total;
}

function getpurstock($pid){
		global $conn;
		global $branch_id;
		$sql="SELECT sum(quantity) as total from `purchase_items` where product_id='$pid' and active=0 and branch_id='".$branch_id."'";
		$result=query_by_id($sql,[],$conn);
	 
			foreach($result as $row)
			{			
			$total=$row['total'];
			}
			return $total;
		 
}
function productused($pid)
{
		global $conn;
		global $branch_id;
		$sql="SELECT sum(quantity) as total from `productused` where product='$pid' and active=0 and branch_id='".$branch_id."'";
		$result=query_by_id($sql,[],$conn);
	 
			foreach($result as $row)
			{			
			$total=$row['total'];
			}
			return $total;
		 
}
?>