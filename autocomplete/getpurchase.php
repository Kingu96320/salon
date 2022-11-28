<?php
include_once '../includes/db_include.php';
$branch_id = $_SESSION['branch_id'];
if (isset($_GET['term'])){
	$return_arr = array();
	$term = $_GET['term'];
	$i=0;
		$sql="SELECT sum(p.credit) as cre,sum(p.payment) as pay,sum(pa.debit) as debit FROM `purchase` p LEFT JOIN payments pa on pa.purchase_id=p.id WHERE p.vendor LIKE '$term%' and p.branch_id='".$branch_id."' ";
		$result=query_by_id($sql,[],$conn);
		foreach($result as $row) 
		{
			//$total=$row['pay'] - $row['total']-$row['debit'];
			 
			//$return_arr[$i]['credit']  = abs($row['cre']);
		 
			 
			//$return_arr[$i]['total']	=	$row['tot'];
			$i++;
		}
		
    echo json_encode($return_arr);
}
?>