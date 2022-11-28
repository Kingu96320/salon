<?php
include_once '../includes/db_include.php';
$branch_id = $_SESSION['branch_id'];
if (isset($_GET['s'])){
	$arr = array();
	$start = $_GET['s'];
	$end = $_GET['e'];
	$id = $_GET['i'];	
	
	$dat = "and i.doa BETWEEN '".$start."' AND '".$end."'";
		
		$arr['service'] = getservicecom($id,$dat);
		$arr['product'] = getprodcom($id,$dat);
		
    echo json_encode($arr);
}

function getservicecom($bid,$dat){
	global $conn;
	global $branch_id;
	$sum   = 0;
	$price = 0;
	$sql="SELECT sum(ii.price) as total FROM `invoice_".$branch_id."` i "
			." LEFT JOIN `invoice_items_".$branch_id."` ii on ii.iid=i.id "
			." LEFT JOIN `invoice_multi_service_provider` imsp on imsp.inv=i.id "
			." WHERE imsp.service_provider='$bid' and i.active=0 and i.branch_id='".$branch_id."' and ii.type='Service'".$dat;
	//$sql="SELECT sum(tr.price) as total FROM `transactions` tr LEFT JOIN `invoice_multi_service_provider` imsp on imsp.ii_id=tr.inv WHERE imsp.service_provider='$bid' and tr.active=0 and tr.type='Service'".$dat;
		$result=query_by_id($sql,[],$conn)[0];
		if ($result) {
			$price =  $result['total'];
		}else{
			$price = 0;
		}
	$com = getcoms($bid);

		$sum =$sum + $price * $com / 100;
	return $sum;
}

function getprodcom($bid,$dat){
	global $conn;
	global $branch_id;
	$sum = 0;
	$price = 0;
	$sql="SELECT sum(ii.price) as total FROM `invoice_".$branch_id."` i "
			." LEFT JOIN `invoice_items_".$branch_id."` ii on ii.iid=i.id "
			." LEFT JOIN `invoice_multi_service_provider` imsp on imsp.inv=i.id "
			." WHERE imsp.service_provider='$bid' and i.active=0 and i.branch_id='".$branch_id."' and ii.type='Product'".$dat;
		$result=query_by_id($sql,[],$conn)[0];
		if ($result) {
			$price =  $result['total'];
		}else{
			$price = 0;
		}
	$com = getcomp($bid);

		$sum =$sum + $price * $com / 100;
	return $sum;
}

function getcoms($bid){
	global $conn;
	global $branch_id;
	$sum = 0;
	$sql="SELECT * from beauticians where id=$bid and branch_id='".$branch_id."'";
	$result=query_by_id($sql,[],$conn)[0];
	if ($result) {
		return $result['serivce_commision'];
	}
}

function getcomp($bid){
	global $conn;
	global $branch_id;
	$sum = 0;
	$sql="SELECT * from beauticians where id=$bid and branch_id='".$branch_id."'";
	$result=query_by_id($sql,[],$conn)[0];
	if ($result) {
		return $result['prod_commision'];
	}
}

?>
