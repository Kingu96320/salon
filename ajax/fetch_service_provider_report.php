<?php
	include "../includes/db_include.php";
	$branch_id = $_SESSION['branch_id'];
	## Read value
	$draw = $_POST['draw'];
	$row = $_POST['start'];
	$rowperpage = $_POST['length']; // Rows display per page
	$columnIndex = $_POST['order'][0]['column']; // Column index
	$columnName = $_POST['columns'][$columnIndex]['data']; // Column name
	$columnSortOrder = $_POST['order'][0]['dir']; // asc or desc
	$searchValue = $_POST['search']['value']; // Search value
	$from_Date = $_GET['from_date'];
	$to_Date   = $_GET['to_date'];
	$sp_id     = $_GET['sp_id'];
	
	## Search 
	$dateFilter ="";
	if($sp_id > 0){
	    $dateFilter="and (i.doa between '".$from_Date."' and '".$to_Date."') and imsp.service_provider='".$sp_id."'";
	} else {
	    $dateFilter="and (i.doa between '".$from_Date."' and '".$to_Date."') ";
	}
	
	$searchQuery = " ";
	if($searchValue != ''){
		$searchQuery = " and (b.name like '%".$searchValue."%' OR 
        b.cont like '%".$searchValue."%') OR (s.name like '%".$searchValue."%') OR (i.doa like '%".$searchValue."%')";
	}
	$sql="SELECT count(*) as allcount from `invoice_multi_service_provider` imsp "
				." LEFT JOIN `invoice_items_".$branch_id."` ii on ii.id=imsp.ii_id "
				." LEFT JOIN `invoice_".$branch_id."` i on i.id=ii.iid " 
				." LEFT JOIN `beauticians` b on b.id = imsp.service_provider"
				." LEFT JOIN `service` s on s.id = SUBSTRING_INDEX(ii.service,',',-1)"
				." where i.active=0 and imsp.branch_id='".$branch_id."' ".$searchQuery." ".$dateFilter;
	## Total number of records without filtering
	$records = query_by_id($sql,[],$conn)[0];
	//$records = mysqli_fetch_assoc($sel);
	$totalRecords = $records['allcount'];
	
	## Total number of record with filtering
	$records = query_by_id("SELECT count(*) as allcount from `invoice_multi_service_provider` imsp "
				." LEFT JOIN `invoice_items_".$branch_id."` ii on ii.id=imsp.ii_id "
				." LEFT JOIN `invoice_".$branch_id."` i on i.id=ii.iid " 
				." LEFT JOIN `beauticians` b on b.id = imsp.service_provider"
				." LEFT JOIN `service` s on s.id = SUBSTRING_INDEX(ii.service,',',-1)"
				." where i.active=0 and imsp.branch_id='".$branch_id."' ".$searchQuery." ".$dateFilter,[],$conn)[0];
	//$records = mysqli_fetch_assoc($sel);
	$totalRecordwithFilter = $records['allcount'];
	
	## Fetch records
	$spQuery = "SELECT i.doa,b.name as service_provider,b.id as bid, s.name as s_name, imsp.service_name, b.cont,ii.price as serice_price, ii.service as sid, i.id as bill_id from `invoice_multi_service_provider` imsp "
				." LEFT JOIN `invoice_items_".$branch_id."` ii on ii.id=imsp.ii_id "
				." LEFT JOIN `invoice_".$branch_id."` i on i.id=ii.iid " 
				." LEFT JOIN `beauticians` b on b.id = imsp.service_provider"
				." LEFT JOIN `service` s on s.id = SUBSTRING_INDEX(ii.service,',',-1)"
				." where i.active=0 and imsp.branch_id='".$branch_id."' ".$searchQuery." ".$dateFilter." order by ".$columnName." ".$columnSortOrder." limit ".$row.",".$rowperpage;
	$spRecords = query_by_id($spQuery,[],$conn);
	$data = array();
	$total_price = 0; 
	
// 	echo "<pre>";
// 	print_r($spRecords);
// 	echo "</pre>";
	foreach($spRecords as $row) {
	    
	        if(explode(",",$row['service_name'])[1] >0){
				 $ser_id = $row['service_name'];
				 $service_name=getservice($ser_id);
			}else{
				  $service_name = '';
			}
			
			if(explode(" ", $service_name)[0] == '(Service)'){
			    $amount = getservicecom($row['serice_price'],$row['bid'],$ser_id, $row['bill_id']);
			    $total_amount = $amount+$total_amount;
		        $commission = number_format(getservicecom($row['serice_price'],$row['bid'],$ser_id, $row['bill_id']),2); 
			} else { 
			    $amount = getprodcom($row['serice_price'],$row['bid'],$ser_id, $row['bill_id']);
			    $total_amount = $amount+$total_amount;
			    $commission = number_format(getprodcom($row['serice_price'],$row['bid'],$ser_id, $row['bill_id']),2); 
			}
			
			if(explode(",",$row['service_name'])[0] == 'pr'){
			    $sid = explode(',',$row['sid'])[1];
			    $name = query_by_id("SELECT name FROM products WHERE id = '".$sid."'",[],$conn)[0]['name'];
			} else {
			    $name = $row['s_name'];
			}
				
			$data[] = array( 
			"doa"=> my_date_format($row['doa']),
			"service_provider"=>$row['service_provider'],
			"cont"=>$row['cont'],
			"service"=>$service_name,
			"serice_price"=> number_format($row['serice_price'],2),
			"commission_amount"=>$commission,
			);
			$total_price +=$row['serice_price'];
		}
	$data[] = array( 
			"doa"=>"",
			"service_provider"=>"",
			"cont"=>"",
			"service"=>"<b>Total</b>",
			"serice_price"=> "<b>".number_format($total_price,2)."</b>",
			"commission_amount"=> "<b>".number_format($total_amount,2)."</b>",
			);
	## Response
	$response = array(
    	"draw" => intval($draw),
    	"iTotalRecords" => $totalRecordwithFilter,
    	"iTotalDisplayRecords" => $totalRecords,
    	"aaData" => $data
	);
	
	echo json_encode($response);
	
	function getsum($pid){
		$earn = getearnings($pid);
		$ded = getdeductions($pid);
		$sum = $earn - $ded;
		return $sum;
	}
	
	function getearnings($pid) {
		global $conn;
		global $branch_id;
		$sql2="SELECT sum(salary) as sum from salary where eid='$pid' and type=1 and branch_id='".$branch_id."'";
		$result2=query_by_id($sql2,[],$conn);
		foreach($result2 as $row2){
			return $row2['sum'];
		}
	}
	
	function getdeductions($cid) {
		global $conn;
		global $branch_id;
		$sql2="SELECT sum(salary) as sum from salary where eid='$cid' and type=2 and branch_id='".$branch_id."'";
		$result2=query_by_id($sql2,[],$conn);
		foreach($result2 as $row2) {
			return $row2['sum'];
		}
	}
	
	
	function getservicecom($price,$bid,$service,$id){
		$sum = 0;
		$provider_id = $bid;
		$type = 'Service';
		$total_sale = service_provider_total_sale($type, $provider_id, $id);
		$commission_per = service_provider_commission_saved($provider_id, $service, $id);
		$com = $commission_per;
		$val = $price * $com / 100;
		$sum = $sum + $val;
		$pcount = provider_count($service,$id);
		if($pcount > 0){
		    return $sum/$pcount;
		} else {
		    return $sum;   
		}
	}
	
	function getprodcom($price,$bid,$service,$id){
		$sum = 0;
		$provider_id = $bid;
		$type = 'Product';
		$total_sale = service_provider_total_sale($type, $provider_id, $id);
		$commission_per = service_provider_commission_saved($provider_id, $service, $id);
		$com = $commission_per;
		$val = $price * $com / 100;
		$sum = $sum + $val;
		$pcount = provider_count($service,$id);
		if($pcount > 0){
		    return $sum/$pcount;
		} else {
		    return $sum;   
		}
	}
	
	function getcoms($bid,$type,$id){
		global $conn;
		global $branch_id;
		$sum = 0;
		$sql="SELECT * from invoice_multi_service_provider where inv='$id' and service_name='$type' and service_provider = '$bid' and status='1' and branch_id='".$branch_id."'";
		$result=query_by_id($sql,[],$conn);
		foreach($result as $row) {
			return $row['commission_per'];
		}
	}
	
	function getcomp($bid,$type,$id){
		global $conn;
		global $branch_id;
		$sum = 0;
        $sql="SELECT * from invoice_multi_service_provider where inv='$id' and service_name='$type' and service_provider = '$bid' and status='1' and branch_id='".$branch_id."'";
		$result=query_by_id($sql,[],$conn);
		foreach($result as $row) {
			return $row['commission_per'];
		}
	}
	
	function getproduct($bid){
		global $conn;
		global $branch_id;
		$sum = 0;
		$sql="SELECT * from products where id=$bid";
		$result=query_by_id($sql,[],$conn);
		foreach($result as $row) {
			return $row['name'];
		}
	}
	
	function getservice($get_id){
		global $conn;
		global $branch_id;
		$id = EXPLODE(",",$get_id)[1];
		if(EXPLODE(",",$get_id)[0] == 'sr'){
			$sql ="SELECT CONCAT('(Service)',' ',name) as name FROM `service` where active='0' and id='$id'";	
		}else if(EXPLODE(",",$get_id)[0] == 'pr'){
			$sql ="SELECT CONCAT('(Product)',' ',name) as name FROM `products` where active='0' and id='$id'";	
		}else if(EXPLODE(",",$get_id)[0] == 'pa'){
			$sql ="SELECT CONCAT('(Package)',' ',name) as name FROM `packages` where active='0' and id='$id' and branch_id='".$branch_id."'";	
		}else if(EXPLODE(",",$get_id)[0] == 'prepaid'){
			$sql ="SELECT CONCAT('(Prepaid)',' ',pack_name) as name FROM `prepaid` where status='1' and id='$id' and branch_id='".$branch_id."'";	
		}else if(EXPLODE(",",$get_id)[0] == 'mem'){
			$sql ="SELECT CONCAT('(Membership)',' ',membership_name) as name FROM `membership_discount` where status='1' and id='$id'";
		}
	    $result=query_by_id($sql,[],$conn);
	    foreach($result as $row) {
	    	return $row['name'];
	    }
	}
	 