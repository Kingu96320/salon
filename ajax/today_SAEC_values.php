<?php
	include "../includes/db_include.php";
	$branch_id = $_SESSION['branch_id'];
	$jsonArr =[];
	$startDate = $_GET['startDate'];
	$endDate   = $_GET['endDate'];
	if((isset($_GET['type']) && $_GET['type'] == 'sales')){
		$total = 0;
		$date = "and doa BETWEEN '$startDate' AND '$endDate'";  
		$sql="SELECT sum(total) as total from `invoice_".$branch_id."` where  active=0 and branch_id='".$branch_id."' ".$date;
		$result=query_by_id($sql,[],$conn)[0]; 
		$total += floatval($result['total']);
		
		$app_advance = query_by_id("SELECT sum(paid) as total FROM `app_invoice_".$branch_id."` WHERE active='0' and branch_id='".$branch_id."' AND appdate BETWEEN '".$startDate."' AND '".$endDate."'",[],$conn)[0];
		
		if($app_advance['total'] > 0){
		    $total += floatval($app_advance['total']);
		}
		
		$common_bill = query_by_id("SELECT sum(paid) as adv FROM `app_invoice_".$branch_id."` WHERE bill_created_status='1' AND active='0' and branch_id='".$branch_id."' AND appdate BETWEEN '".$startDate."' AND '".$endDate."' ",[],$conn)[0];
		
		if($common_bill['adv'] > 0){
		    $total -= floatval($common_bill['adv']);
		}
		
		if($total>0){	
			$jsonArr['salesTotal']= number_format($total,2);
		}else{
			$jsonArr['salesTotal']=0;
		}
	}
	
	if((isset($_GET['type']) && $_GET['type'] == 'appointment')){
		$date = "and doa BETWEEN '$startDate' AND '$endDate'"; 
		$sql="SELECT count(*) as total from `app_invoice_".$branch_id."` where active=0 and branch_id='".$branch_id."' ".$date;
		$result=query_by_id($sql,[],$conn)[0];
		if($result){   
			$jsonArr['appTotal']=($result['total'] > 0) ? $result['total'] : '0';
		} 
	}
	
	if((isset($_GET['type']) && $_GET['type'] == 'enquiry')){
	    $date = "and regon ='".date('Y-m-d')."'";
		$sql="SELECT count(*) as total from `enquiry` where active=0 and branch_id='".$branch_id."' ".$date;
		$result=query_by_id($sql,[],$conn)[0];
		if($result){   
			$jsonArr['enquiryTotal']=($result['total'] > 0) ? $result['total'] : '0';
		} 
	}
	
	if((isset($_GET['type']) && $_GET['type'] == 'expenses')){
		$date = "and date BETWEEN '$startDate' AND '$endDate'"; 
		$sql="SELECT sum(amount) as total from `expense` where active=0 and branch_id='".$branch_id."' ".$date;
		$result=query_by_id($sql,[],$conn)[0];
		if($result){   
			$jsonArr['enquiryTotal']=($result['total'] > 0) ? $result['total'] : '0';
		} 
	}
	
	if((isset($_GET['type']) && $_GET['type'] == 'clients')){
		$date = "and doa BETWEEN '$startDate' AND '$endDate'"; 
		$sql="SELECT count(DISTINCT client) as total from `invoice_".$branch_id."` where active=0 ".$date." GROUP BY client" ;
		$result=query_by_id($sql,[],$conn);
		if($result){   
			$jsonArr['clintsvisitTotal']=(count($result) > 0) ? count($result) : '0';
		} else {
			$jsonArr['clintsvisitTotal'] = 0;
		} 
	}
	
	echo json_encode($jsonArr);
?>