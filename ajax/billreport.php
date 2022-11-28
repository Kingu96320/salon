<?php
	include "../includes/db_include.php";
	$branch_id = $_SESSION['branch_id'];
	if(!isset($_POST['customer_filter'])){
		## Read value
		$draw = $_POST['draw'];
		$row = $_POST['start'];
		$rowperpage = $_POST['length']; // Rows display per page
		$columnIndex = $_POST['order'][0]['column']; // Column index
		$columnName = $_POST['columns'][$columnIndex]['data']; // Column name
		$columnSortOrder = $_POST['order'][0]['dir']; // asc or desc
		$searchValue = $_POST['search']['value']; // Search value
		
		## Search 
		$searchQuery = " ";
		if($searchValue != ''){
			$searchQuery = " and (c.name like '%".$searchValue."%' or 
			c.cont like '%".$searchValue."%' ) ";
		}
		
		$sql="SELECT count(*) as allcount from `invoice_".$branch_id."` i LEFT JOIN `client` c on c.id =i.client where i.active=0 and i.branch_id='".$branch_id."' and i.doa='".date('Y-m-d')."' ".$searchQuery;
		## Total number of records without filtering
		$records = query_by_id($sql,[],$conn)[0];
		//$records = mysqli_fetch_assoc($sel);
		$totalRecords = $records['allcount'];
		
		## Total number of record with filtering
		$records = query_by_id("SELECT count(*) as allcount from `invoice_".$branch_id."` i LEFT JOIN `client` c on c.id =i.client where i.active=0 and i.doa='".date('Y-m-d')."' and i.branch_id='".$branch_id."'  ".$searchQuery,[],$conn)[0];
		//$records = mysqli_fetch_assoc($sel);
		$totalRecordwithFilter = $records['allcount'];
		
		$sql1="SELECT i.*,c.name,c.cont from `invoice_".$branch_id."` i LEFT JOIN `client` c on c.id =i.client where i.active=0 and i.doa='".date('Y-m-d')."' and i.branch_id='".$branch_id."' ".$searchQuery."   order by ".$columnName." ".$columnSortOrder." limit ".$row.",".$rowperpage;
		$result1=query_by_id($sql1,[],$conn);
		$arr = array();
		foreach($result1 as $row1) {
			if($prr[0]!=""){
				$tr = checkproduct($prr[0],$prr[1],$row1['id']);
				if($tr==0)
				continue;
			}
			if($stfid!=""){
				$stfchk = checkstaff($stfid,$row1['id']);
				if($stfchk==0)
				continue;
			}

			$sep_bill = query_by_id("SELECT mpm.amount_paid, pm.name FROM multiple_payment_method mpm LEFT JOIN payment_method pm ON pm.id = mpm.payment_method WHERE mpm.invoice_id='bill,".$row1['id']."' AND mpm.status='1' and mpm.branch_id='".$branch_id."'",[],$conn);
			$sbill = '';
			if($sep_bill){
			    foreach($sep_bill as $bill){
			        $sbill .= $bill['name'].' - '.number_format($bill['amount_paid'],2).'<br />';
			    }
			}

			$data = array(
			        "doa"   => my_date_format($row1['doa']),
			        "billid"   => $row1['id'],
			        "name"  => $row1['name'],
			        "cont"  => $row1['cont'],
			        "total" => number_format($row1['total'],2),
			        "paid"  => number_format($row1['paid'],2),
			        "payment_detail" => $sbill,
			        "due"   => number_format($row1['due'],2),
			    );
				$ty = $row1['invoice'];
				if($ty==1){
				   $data['invoice'] = "Converted";
				}
				else{
    				$data['invoice'] = "Converted";
				}
				$staf = "";
				$sql2="SELECT * from `invoice_multi_service_provider` where inv=".$row1['id']." and status=1 and branch_id='".$branch_id."' order by id desc";
				$result2=query($sql2,[],$conn);
				$staf = "<ul>";
				foreach($result2 as $row2) 
				{
					if($row2['service_provider']!="0"){
    					$staff = " - <u>".getstaff($row2['service_provider'])."</u>";
					} else {
					    $staff = '';
					}
					$staf .= "<li>".getitem($row2['service_name']).$staff."</li>";
					$data['service_provider'] = $staf;
				}
				$staf .= "</ul>";
			    $data['notes'] = $row1['notes'];
			    $data['user'] = get_user($row1['uid']);
			    $btn = '<a href="billing.php?beid='.$row1['id'].'" onclick="return confirm("Are you sure?");"><button class="btn btn-warning btn-xs" type="button"><i class="fa fa-pencil-square-o" aria-hidden="true"></i>Edit</button></a> <a href="invoice.php?inv='.$row1['id'].'" target="_blank"><button class="btn btn-info btn-xs"  type="button"><i class="fa fa-eye" aria-hidden="true"></i>View</button></a><a href="" onclick="invoice_delete('.$row1["id"].')"><button class="btn btn-danger btn-xs"  type="button"><i class="fa fa-trash" aria-hidden="true"></i>Delete</button></a>';
    			$data['action'] = $btn;
    			array_push($arr,$data);
        }
	
		## Response
		$response = array(
		"draw" => intval($draw),
		"iTotalRecords" => $totalRecordwithFilter,
		"iTotalDisplayRecords" => $totalRecords,
		"aaData" => $arr
		);
		
		echo json_encode($response);
		}
	
		function get_user($user){
		global $conn;
		global $branch_id;
		$sql="SELECT * from user where id=$user and branch_id='".$branch_id."' order by id desc";
		$result=query_by_id($sql,[],$conn);
		foreach($result as $row) {
			return $row['name'];
		}
	}
	
	function getclient($cid){
		global $conn;
		global $branch_id;
		$sql="SELECT * from client where id=$cid and branch_id='".$branch_id."'";
		$result=query_by_id($sql,[],$conn);
		foreach($result as $row) 
		{
			return $row['name'];
		}
	}
	
	function getstaff($sid){
		global $conn;
		global $branch_id;
		$sql="SELECT * FROM `beauticians` where id=$sid and branch_id='".$branch_id."'";
		$result=query_by_id($sql,[],$conn);
		foreach($result as $row){
			return $row['name'];
		}
	}
	
	function getcont($cid){
		global $conn;
		global $branch_id;
		$sql="SELECT * from client where id=$cid and branch_id='".$branch_id."'";
		$result=mysqli_query($con,$sql);
		if($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
			return $row['cont'];
		}
	}
	
		function getitem($id)
	{
		global $conn;
		global $branch_id;
		$str = "";
		$item_id=explode(",",$id)[1];
		if($item_id == ''){
		    $item_id = 0;
		}
		$chk_type = explode(",",$id)[0];
		if($chk_type == 'sr'){
		    $type = 'Service';
		} else if($chk_type == 'pa'){
		    $type = 'Package';
		} else if($chk_type == 'pr'){
		    $type = 'Product';
		} else if($chk_type == 'mem'){
		    $type = 'Membership';
		} else {
		        $type = '';
		}
		
		switch ($type) 
		{
			case "Service":
			$sql="SELECT * from `service` where id=$item_id";
			$result=query_by_id($sql,[],$conn);
			foreach($result as $row) 
			{
				$str = $row['name']."(Service)";
			}
			break;
			case "Product":
			$sql="SELECT * from `products` where id=$item_id";
			$result=query_by_id($sql,[],$conn);
			foreach($result as $row) 
			{
				$str = $row['name']."(Product)";
			}
			break;
			case "Package":
			$sql="SELECT * from `packages` where id=$item_id and branch_id='".$branch_id."'";
			$result=query_by_id($sql,[],$conn);
			foreach($result as $row) {
				$str = $row['name']."(Package)";
			}
			break;
			case "Membership":
			$sql="SELECT * from `membership_discount` where id=$item_id";
			$result=query_by_id($sql,[],$conn);
			foreach($result as $row) {
				$str = $row['membership_name']."(Membership)";
			}
			break;
			default:
			$str = "";
			break;
		}
		return $str;
	}
	
	function checkstaff($sid,$inv){
		global $conn;
		global $branch_id;
		$sql = "SELECT * from invoice_items_".$branch_id." where staffid=$sid and iid=$inv and active=0 and branch_id='".$branch_id."'";
		$result=query($sql,[],$conn);
		if($result)
		{
			foreach($result as $row) {
				return 1;
			}}else{
			return 0;
		}
	}
	
	function checkproduct($pr,$pid,$inv){
		global $conn;
		global $branch_id;
		if($pr=="pr")
		$pr="Product";
		if($pr=="sr")
		$pr="Service";
		if($pr=="pa")
		$pr="Package";
		if($pr=="mem")
		$pr="Membership";
		$sql = "SELECT * from invoice_items_".$branch_id." where type='$pr' and service=$pid and iid=$inv and active=0 and branch_id='".$branch_id."'";
		$result=query_by_id($sql,[],$conn);
		if($result) 
		{
			return 1;
			}else{
			return 0;
		}
	}
	
		function invoice_delete($inv){
		    global $conn;
	    	global $branch_id;
    	   // $result =  query("UPDATE $table set `active`= 1 where `id`='$inv' and `branch_id`='".$branch_id."'",[],$conn);
    	    
    	}
 
	
	
