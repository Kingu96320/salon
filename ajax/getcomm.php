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
	$sql="SELECT sum(ii.price) as total from `invoice_multi_service_provider` imsp "
				." LEFT JOIN `invoice_items_".$branch_id."` ii on ii.id=imsp.ii_id "
				." LEFT JOIN `invoice_".$branch_id."` i on i.id=ii.iid " 
				." LEFT JOIN `beauticians` b on b.id = imsp.service_provider"
				." LEFT JOIN `service` s on s.id = SUBSTRING_INDEX(ii.service,',',-1)"
				." WHERE imsp.service_provider='$bid' and i.active=0 and ii.type='Service' and imsp.branch_id='".$branch_id."' ".$dat;
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
	$sql="SELECT sum(ii.price) as total from `invoice_multi_service_provider` imsp "
				." LEFT JOIN `invoice_items_".$branch_id."` ii on ii.id=imsp.ii_id "
				." LEFT JOIN `invoice_".$branch_id."` i on i.id=ii.iid " 
				." LEFT JOIN `beauticians` b on b.id = imsp.service_provider"
				." LEFT JOIN `service` s on s.id = SUBSTRING_INDEX(ii.service,',',-1)"
				." WHERE imsp.service_provider='$bid' and i.active=0 and ii.type='Product' and imsp.branch_id='".$branch_id."' ".$dat;	
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
