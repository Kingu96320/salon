<?php
	include "../includes/db_include.php";
	$branch_id = $_SESSION['branch_id'];
	$type = 0;
	if(isset($_GET['type'])){
	    $type = $_GET['type'];
	    $sp = " and ai.app_from='".$type."' ";
	}
	if($type == 0){
		$sp = " and (ai.app_from != '' and ai.app_status='1') OR (ai.app_from = '0' AND ai.active='0')";
	}
	## Read value
	$draw = $_POST['draw'];
	$row = $_POST['start'];
	$rowperpage = $_POST['length']; // Rows display per page
	if($rowperpage == -1){
		$rowperpage = 9999999;
	} else {
		$rowperpage =$rowperpage;
	}
	$columnIndex = $_POST['order'][0]['column']; // Column index
	$columnName = $_POST['columns'][$columnIndex]['data']; // Column name
	$columnSortOrder = $_POST['order'][0]['dir']; // asc or desc
	$searchValue = $_POST['search']['value']; // Search value
	
	## Search 
	$searchQuery = " ";
	if($searchValue != ''){
		$searchQuery = " and (c.name like '%".$searchValue."%' or 
        c.cont like '%".$searchValue."%' OR ai.doa LIKE '%".$searchValue."%'   ) ";
	}
	
 
	$sql="SELECT  count(*) as allcount from `app_invoice_".$branch_id."` ai "
		." LEFT JOIN `client` c on c.id=ai.client "
		." LEFT JOIN `payment_method` pm on pm.id = ai.pay_method "
		." where ai.active=0 ".$sp." and ai.branch_id='".$branch_id."' order by ai.id desc,ai.id desc";
		
	## Total number of records without filtering
	$records = query_by_id($sql,[],$conn)[0];
	//$records = mysqli_fetch_assoc($sel);
	$totalRecords = $records['allcount'];
	
	## Total number of record with filtering
	$records = query_by_id("SELECT  count(*) as allcount from `app_invoice_".$branch_id."` ai "
							." LEFT JOIN `client` c on c.id=ai.client "
							." LEFT JOIN `payment_method` pm on pm.id = ai.pay_method "
							." where ai.active=0 ".$sp." and ai.branch_id='".$branch_id."' ".$searchQuery."  order by ai.doa desc,ai.id desc ",[],$conn)[0];
	//$records = mysqli_fetch_assoc($sel);
	$totalRecordwithFilter = $records['allcount'];
	
	## Fetch records
	$empQuery = "SELECT  ai.notes,pm.name as pay_method_name, c.id as client, ai.status, ai.paid,ai.due,ai.bill_created_status,ai.ss_created_status,ai.doa,ai.appdate,ai.id,ai.total,ai.pay_method,c.name,c.cont, ai.app_from, ai.app_status from `app_invoice_".$branch_id."` ai "
						." LEFT JOIN `client` c on c.id=ai.client "
						." LEFT JOIN `payment_method` pm on pm.id = ai.pay_method "
						." where ai.active=0 ".$sp." and ai.branch_id='".$branch_id."' ".$searchQuery." order by ".$columnName." ".$columnSortOrder.",ai.id ".$columnSortOrder." limit ".$row.",".$rowperpage;
	// echo $empQuery;
	$empRecords = query_by_id($empQuery,[],$conn);
	$data = array();
	foreach($empRecords as $row) {
		if($row['ss_created_status'] == 1){
			$query = "SELECT id FROM service_slip WHERE appointment_id='".$row['id']."' and branch_id='".$branch_id."'";
			$result = query_by_id($query,[],$conn)[0];
			if($result['id'] != '0'){
				$qstring = "ssbid=".$result['id']."&ssappid=".$row['id'];
			}
		} else {
			$qstring = "bid=".$row['id'];
		}
        
        if($type == 1){
            if($row['app_status'] == '0'){
                $editview = '<a href="appointment.php?type=1&id='.$row['id'].'" class=""><button type="button" class="btn btn-xs btn-warning">Approve / Cancel</button></a>';
            } else if($row['app_status'] == '1'){
                $editview = '<a href="appointment.php?&id='.$row['id'].'" class=""><button type="button" class="btn btn-xs btn-info"><i class="fa fa-eye" aria-hidden="true"></i>View</button></a>';
                $editview .= '<a href="javascript:void(0)"><button type="button" class="btn btn-success btn-xs"><i class="fa fa-check" aria-hidden="true"></i>Approved</button></a>';
            } else if($row['app_status'] == '2'){
                $editview = '<button type="button" class="btn btn-danger btn-xs">Cancelled</button>';
            }
        } else if($type == 2){
            if($row['app_status'] == '0'){
                $editview = '<a href="appointment.php?type=2&id='.$row['id'].'" class=""><button type="button" class="btn btn-xs btn-warning">Approve / Cancel</button></a>';
            } else if($row['app_status'] == '1'){
                $editview = '<a href="appointment.php?&id='.$row['id'].'" class=""><button type="button" class="btn btn-xs btn-info"><i class="fa fa-eye" aria-hidden="true"></i>View</button></a>';
                $editview .= '<a href="javascript:void(0)"><button type="button" class="btn btn-success btn-xs"><i class="fa fa-check" aria-hidden="true"></i>Approved</button></a>';
            } else if($row['app_status'] == '2'){
                $editview = '<button type="button" class="btn btn-danger btn-xs">Cancelled</button>';
            }
        } else {
    		if($row['bill_created_status'] == 1){
    			$editview = '<a href="appointment.php?id='.$row['id'].'" class=""><button type="button" class="btn btn-xs btn-info"><i class="fa fa-eye" aria-hidden="true"></i>View</button></a>';
    		} else if($row['status'] == 'Cancelled'){
    			$editview = '<a href="appointment.php?id='.$row['id'].'" class=""><button type="button" class="btn btn-xs btn-info"><i class="fa fa-eye" aria-hidden="true"></i>View</button></a>';
    		} else {
    			$editview = '<a href="appointment.php?id='.$row['id'].'" class=""><button type="button" class="btn btn-xs btn-warning"><i class="fa fa-pencil-square-o" aria-hidden="true"></i>Edit</button></a>';
    		}
    
    		if($row['bill_created_status'] == 1){
    			$deletebtn = '';
    		} else if($row['status'] == 'Cancelled'){
    			$deletebtn = '<button type="button" class="btn btn-xs btn-danger"><i class="fa fa-times" aria-hidden="true"></i>Cancelled</button>';
    		} else{
    		    if(DELETE_BUTTON_INACTIVE == 'true'){
    		        $deletebtn = '<a href="#" onclick="return deleteDisabled();"><button type="button" class="btn btn-xs btn-danger"><i class="icon-delete"></i>Delete</button></a>';
    		    } else {
    		        $deletebtn = '<a href="appointment.php?del_id='.$row['id'].'&c_id='.$row['client'].'" class="" onClick="return confirm(/"Are you sure"/);deleteloader();"><button type="button" class="btn btn-xs btn-danger"><i class="icon-delete"></i>Delete</button></a>';
    		    }
    			
    		}
        }
        
        // $print = ' <a href="appointment-invoice.php?inv='.$row['id'].'" target="_blank"><button class="btn btn-default btn-xs"  type="button"><i class="fa fa-print" aria-hidden="true"></i>Print</button></a>';
        $print = '';
        
		$paystatus = '';
		$paymethod = "SELECT pm.name as name FROM multiple_payment_method mpm LEFT JOIN payment_method pm ON mpm.payment_method = pm.id WHERE invoice_id='app,".$row['id']."' and mpm.branch_id='".$branch_id."' ";
		$methodres = query_by_id($paymethod,[],$conn);
		foreach ($methodres as $res) {
			$paystatus .= ucfirst($res['name'])."<br />";
		}

		$data[] = array( 
		"doa"			=>my_date_format($row['doa']),
		"appdate"		=>my_date_format($row['appdate']),
		"name"			=>$row['name'],
		"cont"			=>$row['cont'],
		"total"			=>number_format(($row['total'])?$row['total']:'0',2),
		"paid"			=>number_format($row['paid'],2),
		"due"			=>number_format(round(($row['due']>0)?$row['due']:'0',0),2),
		"notes"			=>$row['notes'],
		"pay_method_name" =>($paystatus),
		"action"=>$editview
		         .$print
				 .$innerbtn
				 .$deletebtn,
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
	
 