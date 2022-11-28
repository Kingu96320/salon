<?php
	include_once '../includes/db_include.php';
	$branch_id = $_SESSION['branch_id'];
	if(isset($_GET['sdate']) && isset($_GET['edate'])){
		$start_date = $_GET['sdate'];
		$end_date   = $_GET['edate'];
		
		$date = "and doa BETWEEN '$start_date' AND '$end_date'";
		$date1 = "and appdate BETWEEN '$start_date' AND '$end_date'";
		
		
		$total = 0;
		
		
		$sql="SELECT sum(bpaid) as total from `invoice_".$branch_id."` where  active=0 and branch_id='".$branch_id."' ".$date;
		$result1=query_by_id($sql,[],$conn)[0];
		 
		$total += intval($result1['total']);
		
		$sql="SELECT sum(paid) as total from `invoice_".$branch_id."` where  active=0 and branch_id='".$branch_id."' ".$date1;
		$result2=query_by_id($sql,[],$conn)[0];
	 
		$total += intval($result2['total']);
		if($total>0){	
			echo  $total;
			}else{
			echo 0;
		}
		
	}		