<?php
	include "../includes/db_include.php";
// 	ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);
	$branch_id = $_SESSION['branch_id'];
	if(isset($_GET['clientID']) && $_GET['clientID'] > 0){
		$id = $_GET['clientID'];
		$edit = query_by_id("SELECT * from client where id=:id and branch_id='".$branch_id."'",["id"=>$id],$conn)[0];
		echo json_encode($edit);
	}
	else if(isset($_GET['page']) && $_GET['page'] === 'bulk_email'){
	## Read value
	$draw = $_POST['draw'];
	$row = $_POST['start'];
	$start = $_POST['start'];
	$rowperpage = $_POST['length']; // Rows display per page
	if($rowperpage == -1){
		$rowperpage = 9999999;
	} else {
		$rowperpage =$rowperpage;
	}
	$end = $rowperpage;
	$columnIndex = $_POST['order'][0]['column']; // Column index
	$columnName = $_POST['columns'][$columnIndex]['data']; // Column name
	$columnSortOrder = $_POST['order'][0]['dir']; // asc or desc
	$searchValue = $_POST['search']['value']; // Search value
	
	##Order
	$OrderQuery = "order by id DESC";
	$firstv = false;
	$lastv = false;
	if($columnName != "checkbox" && $columnName != "firstvisit" && $columnName != "lastvisit"){
		$OrderQuery = "ORDER BY ".$columnName." ".$columnSortOrder."";
	}
	elseif($columnName == "firstvisit"){
		$firstv = true;

	}elseif($columnName == "lastvisit"){
		$lastv = true;
	}

	## Search 
	$searchQuery = " ";
	if($searchValue != ''){
		$searchQuery = " and (name like '%".$searchValue."%' or 
        cont like '%".$searchValue."%' ) ";
	}
	$sql_in="";
	$in_value = "";
	if(isset($_GET['client_type'])){
	$client_type = $_GET['client_type'];	
	$client_type_sql = query_by_id("SELECT id from client where active='0' and branch_id='".$branch_id."'",[],$conn);
		if($client_type_sql){
			foreach($client_type_sql as $client_row){
					$customer_type=customer_type($client_row['id']);
					if($customer_type === $client_type){
						$in_value .=$client_row['id'].",";
					} 
					else if($customer_type === $client_type){
						$in_value .=$client_row['id'].",";
					}	 
					else if ($customer_type === $client_type){
						$in_value .=$client_row['id'].",";
					}	 
				}
		}
		$lastSpacePosition = strrpos($in_value, ',');
		$ids_IN_function = substr($in_value, 0, $lastSpacePosition);
		if($ids_IN_function !=''){
		$sql_in=" and id IN ($ids_IN_function)";
		}else{
		$sql_in=" and id IN (0)";
		}
	}
	
	$sql="SELECT count(*) as allcount from client where active=0 and branch_id='".$branch_id."' and email<>'' ".$sql_in.$searchQuery;
	## Total number of records without filtering
	$records = query_by_id($sql,[],$conn)[0];
	//$records = mysqli_fetch_assoc($sel);
	$totalRecords = $records['allcount'];
	
	## Total number of record with filtering
	$records = query_by_id("SELECT count(*) as allcount from client where active=0 and branch_id='".$branch_id."' and email<>'' ".$sql_in.$searchQuery,[],$conn)[0];
	//$records = mysqli_fetch_assoc($sel);
	$totalRecordwithFilter = $records['allcount'];
	
	
	 
	if($firstv == true){
		$empQuery = getfirstvisit_cid($columnSortOrder,$sql_in.$searchQuery.$add,$start,$end);
	}elseif($lastv == true){
		$empQuery = getlastvisit_cid($columnSortOrder,$sql_in.$searchQuery.$add,$start,$end);
	}else{
		$empQuery = "SELECT * from client where active=0 and branch_id='".$branch_id."' and email<>'' ".$searchQuery." ".$OrderQuery."";
	}
	
	$empRecords = query_by_id($empQuery,[],$conn);
	$data = array();
	foreach($empRecords as $row) {
			if($row['gender'] == '1'){
				$gender = 'Male';
			} else if($row['gender'] == '2'){
				$gender = 'Female';
			} else {
			    $gender = '--';
			}
			if(my_date_format(firstvisit($row['id'])) == '01-01-1970'){
			    $fvisit = '--';
			} else {
			    $fvisit = my_date_format(firstvisit($row['id']));
			}  if(my_date_format(firstvisit($row['id'])) == '01-01-1970'){
			    $lvisit = '--';
			} else {
			    $lvisit = my_date_format(lastvisit($row['id']));
			}
			if($row['referral_code'] != ''){
			     if(strlen($row['referral_code']) != 8 || strpos($row['referral_code'], ' ') !== false){
			        $code = sprintf('%08s',addslashes(trim(strtoupper(substr(str_replace(' ','',$row['name']),0,4)).strtoupper(substr(md5(sha1($row['cont'])),0,4)))));
			        $refcode = '<a href="javascript:void(0)" id="row_'.$row['id'].'" class="text-danger" onClick="generateRefcode('.$row['id'].',\''.$code.'\')">Generate code</a>';
			    } else {
			        $refcode = '<a href="javascript:void(0)" onClick="viewModal('.$row['id'].')"><i class="icon-eye3"></i><b> '.$row['referral_code'].'<b></a>';
			    }
			} else {
			   $code = sprintf('%08s',addslashes(trim(strtoupper(substr(str_replace(' ','',$row['name']),0,4)).strtoupper(substr(md5(sha1($row['cont'])),0,4)))));
			   $refcode = '<a href="javascript:void(0)" id="row_'.$row['id'].'" class="text-danger" onClick="generateRefcode('.$row['id'].',\''.$code.'\')">Generate code</a>';
			}
			
			if(DELETE_BUTTON_INACTIVE == 'true'){
			    $buttons = '<div class=""><a href="clientprofile.php?cid='.$row['id'].'"><button class="btn btn-info btn-xs" type="button"><i class="fa fa-user" aria-hidden="true"></i>View Profile</button></a> <a href="#" onclick="return deleteDisabled();"><button class="btn btn-danger btn-xs"><i class="icon-delete" style="margin-right:0px;"></i></button></a></a>';
			} else {
			    $buttons = '<div class=""><a href="clientprofile.php?cid='.$row['id'].'"><button class="btn btn-info btn-xs" type="button"><i class="fa fa-user" aria-hidden="true"></i>View Profile</button></a> <a href="clients.php?del_id='.$row['id'].'" onclick="return confirmDelete();"><button class="btn btn-danger btn-xs"><i class="icon-delete" style="margin-right:0px;"></i></button></a></a>';
			}
			
			$last_bill_amount = query_by_id("SELECT total FROM invoice_$branch_id WHERE client = '".$row['id']."' and active = 0 ORDER BY doa DESC LIMIT 1",[],$conn)[0]['total'];
			
			// $last_service = query_by_id("SELECT s.name FROM invoice_items_$branch_id ii LEFT JOIN service s ON s.id = SUBSTRING_INDEX(ii.service,',',-1) WHERE ii.client='".$row['id']."' AND ii.type='Service' AND ii.active='0' ORDER BY ii.id DESC LIMIT 1",[],$conn)[0]['name'];

			$last_invoice_id = query_by_id("SELECT id FROM invoice_$branch_id WHERE client = '".$row['id']."' and active = 0 ORDER BY doa DESC LIMIT 1 ",[],$conn)[0]['id'];

			$last_service = array();
			$services = query_by_id("SELECT DISTINCT service FROM invoice_items_$branch_id WHERE iid='".$last_invoice_id."'",[],$conn);
			if($services){
				foreach($services as $ser){
					array_push($last_service, getservice($ser['service']));
				}
			}

			$sp = array();
			$spr = query_by_id("SELECT DISTINCT(b.name) as name FROM beauticians b LEFT JOIN invoice_multi_service_provider imsp ON imsp.service_provider = b.id WHERE imsp.inv = '".$last_invoice_id."' and imsp.branch_id='".$branch_id."'",[],$conn);
			if($spr){
				foreach($spr as $s) {
					array_push($sp, $s['name']);
				}
			}
			
			$data[] = array( 
			"checkbox"          => '<input type="checkbox" id="check_'.$row['id'].'" value="'.$row['id'].'" class="chkk" data-name="'.$row['name'].'" data-contact="'.$row['cont'].'" data-ref="'.$row['referral_code'].'">',
			"id"               => $row['id'],
			"name"              =>$row['name'],
			"cont"              =>$row['cont'],
			"referral_code"     => $refcode,
			"firstvisit"        => $fvisit,
			"lastvisit"         => $lvisit,
			"last_service"      => implode(', ', $last_service),
			"last_service_provider"      => implode(', ', $sp),
			"last_bill_amount"  => $last_bill_amount,
			"gender"            =>$gender,
			"points"            =>get_reward_points($row['id'],$row['referral_code']),
			"action"            => $buttons,
			);
		}
	
// 	<button class="btn btn-warning btn-xs" type="button" onClick="editclients_showmodal('.$row['id'].')">Edit</button> (edit profile button)
	
	## Response
	$response = array(
	"draw" => intval($draw),
	"iTotalRecords" => $totalRecordwithFilter,
	"iTotalDisplayRecords" => $totalRecords,
	"aaData" => $data
	);
	
	echo json_encode($response);
	}else{	
	## Read value
	$draw = $_POST['draw'];
	$row = $_POST['start'];
	$start = $_POST['start'];
	$rowperpage = $_POST['length']; // Rows display per page
	if($rowperpage == -1){
		$rowperpage = 9999999;
	} else {
		$rowperpage =$rowperpage;
	}
	$end = $rowperpage;
	$columnIndex = $_POST['order'][0]['column']; // Column index
	$columnName = $_POST['columns'][$columnIndex]['data']; // Column name
	$columnSortOrder = $_POST['order'][0]['dir']; // asc or desc
	$searchValue = $_POST['search']['value']; // Search value
	
	##Order
	$OrderQuery = "order by id DESC";
	$firstv = false;
	$lastv = false;
	if($columnName != "checkbox" && $columnName != "firstvisit" && $columnName != "lastvisit"){
		$OrderQuery = "ORDER BY ".$columnName." ".$columnSortOrder."";
	}
	elseif($columnName == "firstvisit"){
		$firstv = true;

	}elseif($columnName == "lastvisit"){
		$lastv = true;
	}
	## Search 
	$searchQuery = " ";
	if($searchValue != ''){
		$searchQuery = " and (name like '%".$searchValue."%' or 
        cont like '%".$searchValue."%' ) ";
	}
	$sql_in="";
	$in_value = "";
	if(isset($_GET['client_type'])){
	$client_type = $_GET['client_type'];	
	$client_type_sql = query_by_id("SELECT id from client where active='0' and branch_id='".$branch_id."'",[],$conn);
		if($client_type_sql){
			foreach($client_type_sql as $client_row){
					$customer_type=customer_type($client_row['id']);
					if($customer_type === $client_type){
						$in_value .=$client_row['id'].",";
					} 
					else if($customer_type === $client_type){
						$in_value .=$client_row['id'].",";
					}	 
					else if ($customer_type === $client_type){
						$in_value .=$client_row['id'].",";
					}	 
				}
		}
		$lastSpacePosition = strrpos($in_value, ',');
		$ids_IN_function = substr($in_value, 0, $lastSpacePosition);
		if($ids_IN_function !=''){
		$sql_in=" and id IN ($ids_IN_function)";
		}else{
		$sql_in=" and id IN (0)";
		}
	}
	
	$add = '';
	$cids = '';
	$ids = array();
	if(isset($_GET['filter'])){
	    if(isset($_GET['uid']) && $_GET['uid'] != ''){
	        $add .= " and id='".$_GET['uid']."'";
	    }
	    if(isset($_GET['name']) && $_GET['name'] != ''){
	        $add .= " and name='".$_GET['name']."'";
	    }
	    if(isset($_GET['number']) && $_GET['number'] != ''){
	        $add .= " and cont='".$_GET['number']."'";
	    }
	    if(isset($_GET['email']) && $_GET['email'] != ''){
	        $add .= " and email='".$_GET['email']."'";
	    }
	    if(isset($_GET['source']) && $_GET['source'] != ''){
	        $add .= " and leadsource='".$_GET['source']."'";
		}
		if(isset($_GET['gender']) && $_GET['gender'] != ''){
	        $add .= " and gender='".$_GET['gender']."'";
	    }
	    if(isset($_GET['spname']) && $_GET['spname'] != ''){
	        $cids = query_by_id("SELECT ii.client as client_id FROM invoice_multi_service_provider imsp LEFT JOIN invoice_items_$branch_id ii ON ii.id=imsp.ii_id WHERE imsp.service_provider='".$_GET['spname']."'",[],$conn);
	       // echo "SELECT ii.client as client_id FROM invoice_multi_service_provider imsp LEFT JOIN invoice_items_$branch_id ii ON ii.id=imsp.ii_id WHERE imsp.service_provider='".$_GET['spname']."'";
	        if($cids){
	            foreach($cids as $id){
	                array_push($ids, $id['client_id']);
	            }
	            
	        }
	    }
	    if(isset($_GET['sid']) && $_GET['sid'] != ''){
	       $cids = query_by_id("SELECT ii.client as client_id FROM invoice_items_$branch_id ii  WHERE ii.service='".$_GET['sid']."' and ii.type='Service'",[],$conn);
	       //echo "SELECT ii.client as client_id FROM invoice_items_$branch_id ii  WHERE ii.service='".$_GET['sid']."' and ii.type='Service'"; 
	        if($cids){
	            foreach($cids as $id){
	                array_push($ids, $id['client_id']);
	            }
	        }
	    }
	}
	
	if(count($ids) > 0){
	    $cids = implode(', ', $ids);
	    $add .=" and id IN ($cids)";
	}
	
	
	$sql="SELECT count(*) as allcount from client where active=0 and branch_id='".$branch_id."' ".$sql_in.$searchQuery.$add;
	## Total number of records without filtering
	$records = query_by_id($sql,[],$conn)[0];
	//$records = mysqli_fetch_assoc($sel);
	$totalRecords = $records['allcount'];
	
	## Total number of record with filtering
	$records = query_by_id("SELECT count(*) as allcount from client where active=0 and branch_id='".$branch_id."' ".$sql_in.$searchQuery.$add,[],$conn)[0];
	//$records = mysqli_fetch_assoc($sel);
	$totalRecordwithFilter = $records['allcount'];
	
	
	if(isset($_GET['page']) && $_GET['page'] === 'bulk_sms'){
		$empQuery = "SELECT * from client where active=0 and branch_id='".$branch_id."' ".$searchQuery.$add." ".$OrderQuery;
	}else{
		## Fetch records
		if($firstv == true){
			$empQuery = getfirstvisit_cid($columnSortOrder,$sql_in.$searchQuery.$add,$start,$end);
		}elseif($lastv == true){
			$empQuery = getlastvisit_cid($columnSortOrder,$sql_in.$searchQuery.$add,$start,$end);
		}else{
			$empQuery = "SELECT * from client where active=0 and branch_id='".$branch_id."' ".$sql_in.$searchQuery.$add." ".$OrderQuery." limit ".$row.",".$rowperpage;
		} 
		//$empQuery = "SELECT * from client where active=0 and branch_id='".$branch_id."' ".$sql_in.$searchQuery.$add." ".$OrderQuery." limit ".$row.",".$rowperpage;
	}
	
// 	echo $empQuery;
	
			
	
	$empRecords = query_by_id($empQuery,[],$conn);
	//print_r($empRecords);
	$data = array();
	
	foreach($empRecords as $row) {
		if($row['gender'] == '1'){
			$gender = 'Male';
		} else if($row['gender'] == '2'){
			$gender = 'Female';
		} else {
		    $gender = '--';
		}
		if(my_date_format(firstvisit($row['id'])) == '01-01-1970'){
		    $fvisit = '--';
		} else {
		    $fvisit = my_date_format(firstvisit($row['id']));
		}  if(my_date_format(firstvisit($row['id'])) == '01-01-1970'){
		    $lvisit = '--';
		} else {
		    $lvisit = my_date_format(lastvisit($row['id']));
		}
		if($row['referral_code'] != ''){
		     if(strlen($row['referral_code']) != 8 || strpos($row['referral_code'], ' ') !== false){
		        $code = sprintf('%08s',addslashes(trim(strtoupper(substr(str_replace(' ','',$row['name']),0,4)).strtoupper(substr(md5(sha1($row['cont'])),0,4)))));
		        $refcode = '<a href="javascript:void(0)" id="row_'.$row['id'].'" class="text-danger" onClick="generateRefcode('.$row['id'].',\''.$code.'\')">Generate code</a>';
		    } else {
		        $refcode = '<a href="javascript:void(0)" onClick="viewModal('.$row['id'].')"><i class="icon-eye3"></i><b> '.$row['referral_code'].'<b></a>';
		    }
		} else {
		   $code = sprintf('%08s',addslashes(trim(strtoupper(substr(str_replace(' ','',$row['name']),0,4)).strtoupper(substr(md5(sha1($row['cont'])),0,4)))));
		   $refcode = '<a href="javascript:void(0)" id="row_'.$row['id'].'" class="text-danger" onClick="generateRefcode('.$row['id'].',\''.$code.'\')">Generate code</a>';
		}
		// <span data-toggle="modal" data-target="#add_follow_up" onclick="reset_followup_modal('.$row['id'].');"><button type="button" class="btn btn-success btn-xs"><span class="fa fa-plus mr-left-0" aria-hidden="true"></span>Add Follow up</button></span>
		if(DELETE_BUTTON_INACTIVE == 'true'){
    	    $buttons = '<div class=""><a href="clientprofile.php?cid='.$row['id'].'"><button class="btn btn-info btn-xs" type="button"><i class="fa fa-user" aria-hidden="true"></i>View Profile</button></a> <a href="#" onclick="return deleteDisabled();"><button class="btn btn-danger btn-xs"><i class="icon-delete" style="margin-right:0px;"></i></button></a></a>';
    	} else {
    	    $buttons = '<div class=""><a href="clientprofile.php?cid='.$row['id'].'"><button class="btn btn-info btn-xs" type="button"><i class="fa fa-user" aria-hidden="true"></i>View Profile</button></a> <a href="clients.php?del_id='.$row['id'].'" onclick="return confirmDelete();"><button class="btn btn-danger btn-xs"><i class="icon-delete" style="margin-right:0px;"></i></button></a></a>';
    	}
    	
    	$last_bill_amount = query_by_id("SELECT total FROM invoice_$branch_id WHERE client = '".$row['id']."' and active = 0 ORDER BY doa DESC LIMIT 1",[],$conn)[0]['total'];
    	
    	$last_invoice_id = query_by_id("SELECT id FROM invoice_$branch_id WHERE client = '".$row['id']."' and active = 0 ORDER BY doa DESC LIMIT 1 ",[],$conn)[0]['id'];

    	$last_service = array();
		$services = query_by_id("SELECT DISTINCT service FROM invoice_items_$branch_id WHERE iid='".$last_invoice_id."'",[],$conn);
		if($services){
			foreach($services as $ser){
				array_push($last_service, getservice($ser['service']));
			}
		}

		$sp = array();
		$spr = query_by_id("SELECT DISTINCT(b.name) as name FROM beauticians b LEFT JOIN invoice_multi_service_provider imsp ON imsp.service_provider = b.id WHERE imsp.inv = '".$last_invoice_id."' and imsp.branch_id='".$branch_id."'",[],$conn);
		if($spr){
			foreach($spr as $s) {
				array_push($sp, $s['name']);
			}
		}
    	
		$data[] = array( 
		"checkbox"          => '<input type="checkbox" id="check_'.$row['id'].'" value="'.$row['id'].'" class="chkk" data-name="'.$row['name'].'" data-contact="'.$row['cont'].'" data-ref="'.$row['referral_code'].'">',
		"id"               => $row['id'],
		"name"              =>$row['name'],
		"cont"              =>$row['cont'],
		"referral_code"     => $refcode,
		"firstvisit"        => $fvisit,
		"lastvisit"         => $lvisit,
		"last_service"      => implode(', ', $last_service),
		"last_service_provider"      => implode(', ', $sp),
		"last_bill_amount"  => $last_bill_amount,
		"gender"            =>$gender,
		"points"            =>get_reward_points($row['id'],$row['referral_code']),
		"action"            =>$buttons,
		);
	}
    
    // <a href="javascript:void(0)"><button class="btn btn-warning btn-xs" type="button" onClick="editclients_showmodal('.$row['id'].')"><i class="fa fa-pencil-square-o" aria-hidden="true"></i>Edit</button></a>
	
	## Response
	$response = array(
	"draw" => intval($draw),
	"iTotalRecords" => $totalRecordwithFilter,
	"iTotalDisplayRecords" => $totalRecords,
	"aaData" => $data
	);
	
	echo json_encode($response);
	}
	
	function getsum($client){
		global $conn;
		global $branch_id;
		$sum = 0;
		$sql = "SELECT * from invoice_".$branch_id." where client='$client' and active=0 and branch_id='".$branch_id."'";
		$result=query($sql,[],$conn);
		foreach($result as $row) 
		{
			$inv = $row['id'];
			$point = getpoint($inv);
			$sum = $sum + $point;
		}
		return $sum;
	}
	
	function firstvisit($uid){
		global $conn;
		global $branch_id;
		$sql="SELECT * from invoice_".$branch_id." where client='$uid' and branch_id='".$branch_id."' order by id ASC limit 1";
		$result=query_by_id($sql,[],$conn);
		if ($result) 
		{
			foreach($result as $row)
			{
				return $row['doa'];
				
			}
			}else{
			return "NA";
		}
	}
	
	function lastvisit($uid){
		global $conn;
		global $branch_id;
		$sql="SELECT * from invoice_".$branch_id." where client='$uid' and branch_id='".$branch_id."'  order by id DESC limit 1";
		$result=query_by_id($sql,[],$conn);
		if($result)
		{
			foreach($result as $row)
			{
				return $row['doa'];
			}
			}else{
			return "NA";
		}
	}
	
	function getfirstvisit_cid($order,$search,$start,$end){
		global $conn;
		global $branch_id;
		//$sql = "SELECT abc.* from (SELECT * from invoice_".$branch_id." ORDER BY doa ".$order.") as abc GROUP BY abc.client ORDER by abc.doa ".$order."";
		$sql = "SELECT c.*,abc.doa from (SELECT * from invoice_".$branch_id." ORDER BY doa ASC limit 99999999999) as abc LEFT JOIN client c on abc.client = c.id where c.active=0 and c.branch_id='".$branch_id."' ".$search." GROUP BY abc.client ORDER by abc.doa ".$order." LIMIT ".$start.",".$end."";
		//$result=query_by_id($sql,[],$conn);
		return $sql;
	}

	function getlastvisit_cid($order,$search,$start,$end){
		global $conn;
		global $branch_id;
		//$sql = "SELECT abc.* from (SELECT * from invoice_".$branch_id." ORDER BY doa ".$order.") as abc GROUP BY abc.client ORDER by abc.doa ".$order."";
		$sql = "SELECT c.*,abc.doa from (SELECT * from invoice_".$branch_id." ORDER BY doa DESC limit 99999999999) as abc LEFT JOIN client c on abc.client = c.id where c.active=0 and c.branch_id='".$branch_id."' ".$search." GROUP BY abc.client ORDER by abc.doa ".$order." LIMIT ".$start.",".$end."";
		//$result=query_by_id($sql,[],$conn);
		return $sql;
	}
	
	function getpoint($inv){
		global $conn;
		global $branch_id;
		$sql = "SELECT sum(price) as total from invoice_items_".$branch_id." where iid=$inv and type='Service' and active=0 and branch_id='".$branch_id."'";
		$result=query_by_id($sql,[],$conn);
		if ($result) 
		{  
			foreach($result as $row)
			{
				return $row['total'];
			}
		}
	}

	function getservice($get_id){
		global $conn;
		global $branch_id;
		$id = EXPLODE(",",$get_id)[1];
		if($id != ''){
			if(EXPLODE(",",$get_id)[0] == 'sr'){
				$sql ="SELECT name as name FROM `service` where active='0' and id='$id'";	
			}else if(EXPLODE(",",$get_id)[0] == 'pr'){
				$sql ="SELECT name as name FROM `products` where active='0' and id='$id'";	
			}else if(EXPLODE(",",$get_id)[0] == 'pa'){
				$sql ="SELECT name as name FROM `packages` where active='0' and id='$id' and branch_id='".$branch_id."'";	
			}else if(EXPLODE(",",$get_id)[0] == 'prepaid'){
				$sql ="SELECT pack_name as name FROM `prepaid` where status='1' and id='$id' and branch_id='".$branch_id."'";	
			}else if(EXPLODE(",",$get_id)[0] == 'mem'){
				$sql ="SELECT membership_name as name FROM `membership_discount` where status='1' and id='$id'";
			}
		    $result=query_by_id($sql,[],$conn);
		    foreach($result as $row) {
		    	return $row['name'];
		    }
		}
	}	