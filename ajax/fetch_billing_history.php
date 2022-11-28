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
	
	## Search 
	$searchQuery = " ";
	if($searchValue != ''){
		$searchQuery = " and (c.name like '%".$searchValue."%' or 
        c.cont like '%".$searchValue."%' ) OR i.doa LIKE '%".$searchValue."%'";
	}
	
 
	$sql="SELECT  count(*) as allcount FROM `invoice_".$branch_id."` i LEFT JOIN `client` c on c.id=i.client LEFT JOIN `payment_method` pm on pm.id=i.pay_method where i.active=0 and i.branch_id='".$branch_id."'";
	## Total number of records without filtering
	$records = query_by_id($sql,[],$conn)[0];
	//$records = mysqli_fetch_assoc($sel);
	$totalRecords = $records['allcount'];
	
	## Total number of record with filtering
	$records = query_by_id("SELECT  count(*) as allcount FROM `invoice_".$branch_id."` i LEFT JOIN `client` c on c.id=i.client LEFT JOIN `payment_method` pm on pm.id=i.pay_method where i.active=0 and i.branch_id='".$branch_id."' ".$searchQuery,[],$conn)[0];
	//$records = mysqli_fetch_assoc($sel);
	$totalRecordwithFilter = $records['allcount'];
	
	## Fetch records
	$empQuery = "SELECT i.*,pm.name as payment_mode,c.name as c_name,c.cont FROM `invoice_".$branch_id."` i LEFT JOIN `client` c on c.id=i.client LEFT JOIN `payment_method` pm on pm.id=i.pay_method where i.active=0 and i.branch_id='".$branch_id."' ".$searchQuery." order by ".$columnName." ".$columnSortOrder.",id ".$columnSortOrder." limit ".$row.",".$rowperpage;
	$empRecords = query_by_id($empQuery,[],$conn);
		
	$data = array();
	foreach($empRecords as $row) {
    	$paystatus = '';
    	$paymethod = "SELECT pm.name as name FROM multiple_payment_method mpm LEFT JOIN payment_method pm ON mpm.payment_method = pm.id WHERE invoice_id='bill,".$row['id']."' and mpm.branch_id='".$branch_id."'";
    	$methodres = query_by_id($paymethod,[],$conn);
    	if($methodres){
        	foreach ($methodres as $res) {
        		$paystatus .= ucfirst($res['name'])."<br />";
        	}
    	} else {
    	    $paystatus = '--';
    	}
    	
        if(DELETE_BUTTON_INACTIVE == 'true'){
            $buttons = '<a href="billing.php?beid='.$row['id'].'"><button class="btn btn-warning btn-xs" type="button"><i class="fa fa-pencil-square-o" aria-hidden="true"></i>Edit</button></a> <a href="invoice.php?inv='.$row['id'].'" target="_blank"><button class="btn btn-info btn-xs"  type="button"><i class="fa fa-eye" aria-hidden="true"></i>View</button></a>';
        } else {
    	    $buttons = '<a href="billing.php?beid='.$row['id'].'"><button class="btn btn-warning btn-xs" type="button"><i class="fa fa-pencil-square-o" aria-hidden="true"></i>Edit</button></a> <a href="invoice.php?inv='.$row['id'].'" target="_blank"><button class="btn btn-info btn-xs"  type="button"><i class="fa fa-eye" aria-hidden="true"></i>View</button></a>';
    	    if($_SESSION['user_type'] == 'superadmin'){
            	$buttons .= ' <a href="billing.php?del='.$row['id'].'&c_id='.$row['client'].'" onclick="return confirm(\'Are you sure?\');"><button class="btn btn-danger btn-xs"  type="button"><i class="icon-delete"></i>Delete</button></a>';
            }
        }
		$data[] = array( 
		"doa"      => my_date_format($row['doa']),
		"c_name"   => $row['c_name'],
		"cont"     => $row['cont'],
		"total"    => number_format($row['total'],2),
		"advance"  => number_format(($row['advance'])?$row['advance']:'0',2),
		"paid"     => number_format($row['paid'],2),
		"due"      => number_format(($row['due']>0)?$row['due']:'0',2),
		"notes"    => $row['notes'],
		"pay_mode" => $paystatus,
		"action"   => $buttons,
		);
	}
	 
	
	## Response
	$response = array(
	"draw" => intval($draw),
	"iTotalRecords" => $totalRecordwithFilter,
	"iTotalDisplayRecords" => $totalRecords,
	"aaData" => $data
	);
	echo json_encode($response);
	
	function payment_mode($iid){
		global $conn;
		global $branch_id;
		$payment_method = '' ;
		$sql = "SELECT pm.name as payment_method FROM `multiple_payment_method` mpm LEFT JOIN payment_method pm on pm.id=mpm.payment_method WHERE mpm.invoice_id='$iid' and mpm.status=1 and mpm.branch_id='".$branch_id."'";
		$result = query_by_id($sql,[],$conn);
		if($result){
			foreach($result as $row){
				$payment_method .= $row['payment_method'].'<br>';
			}
			}else{
			
			$payment_method;
		}
		return $payment_method;
	}