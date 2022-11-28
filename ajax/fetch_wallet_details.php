<?php
	include "../includes/db_include.php";
	$branch_id = $_SESSION['branch_id'];
	if(!isset($_POST['client_id'])){
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
		
		$sql="SELECT count(*) as allcount from wallet w LEFT JOIN `client` c on c.id = w.client_id where w.status='1' and c.branch_id='".$branch_id."' ".$searchQuery;
		## Total number of records without filtering
		$records = query_by_id($sql,[],$conn)[0];
		//$records = mysqli_fetch_assoc($sel);
		$totalRecords = $records['allcount'];
		
		## Total number of record with filtering
		$records = query_by_id("SELECT count(*) as allcount from wallet w LEFT JOIN `client` c on c.id = w.client_id where w.status='1' and c.branch_id='".$branch_id."' ".$searchQuery,[],$conn)[0];
		// echo "SELECT count(*) as allcount from wallet where status=1 ".$searchQuery." group by client_id";
		//$records = mysqli_fetch_assoc($sel);
		$totalRecordwithFilter = $records['allcount'];
		
		## Fetch records
		$empQuery = "SELECT group_concat(pm.name) as payment_method,w.id,w.client_id,sum(w.wallet_amount) as wallet_amount,max(w.time_update) as time_update,c.name as client_name,c.cont from wallet w LEFT JOIN client c on c.id=w.client_id LEFT JOIN payment_method pm on pm.id=w.payment_method  where w.status=1 and c.branch_id='".$branch_id."' ".$searchQuery." GROUP BY w.client_id order by ".$columnName." ".$columnSortOrder." limit ".$row.",".$rowperpage;

		$empRecords = query_by_id($empQuery,[],$conn);
		$totalRecords = $records['allcount'];
		$data = array();
		
		if($empRecords){
			foreach($empRecords as $row) {
			    if(DELETE_BUTTON_INACTIVE == 'true'){ 
        		    $buttons = '<a href="wallet_report.php?cid='.$row['client_id'].'" target="_blank"><button class="btn btn-info btn-xs" type="button"><i class="fa fa-file-text-o" aria-hidden="true"></i>View history</button></a> <a href="#" onclick="return deleteDisabled();"><button class="btn btn-danger btn-xs"><i class="icon-delete"></i>Delete</button></a>';
        		} else {
        		    $buttons = '<a href="wallet_report.php?cid='.$row['client_id'].'" target="_blank"><button class="btn btn-info btn-xs" type="button"><i class="fa fa-file-text-o" aria-hidden="true"></i>View history</button></a> <a href="wallet.php?del_id='.$row['client_id'].'" onclick="return confirmDelete();"><button class="btn btn-danger btn-xs"><i class="icon-delete"></i>Delete</button></a>';
        		}
				if(($row['wallet_amount'] - wallet_money_used($row['client_id'])) != 0){
					$data[] = array( 
					"time_update"=> my_date_format(explode(" ",$row['time_update'])[0]),
					"name"=> $row['client_name'],
					"cont"=> $row['cont'],
					"remaning_amount" => number_format(($row['wallet_amount']),2),
					"action"=> $buttons
					);
				}
			}
		}
		
		
		## Response
		$response = array(
		"draw" => intval($draw),
		"iTotalRecords" => $totalRecordwithFilter,
		"iTotalDisplayRecords" => $totalRecords,
		"aaData" => $data
		);
		
		echo json_encode($response);
	}else{
		$data = [];
		$client_id = $_POST['client_id'];
		$invoice_id = $_POST['invoice_id'];
		$wallet_amount_paid = $_POST['wallet_amount_paid'];
		$sql_invoice_exists = query_by_id("SELECT 1 from `wallet` where iid='$invoice_id' and status='1' and branch_id='".$branch_id."'",[],$conn);	
		if(!$sql_invoice_exists || $invoice_id == 0){
		
		$sql = "SELECT w.client_id,sum(w.wallet_amount) as wallet_amount,max(w.time_update) as time_update,c.name as client_name,c.cont from wallet w LEFT JOIN client c on c.id=w.client_id  where w.status=1 and w.client_id='$client_id' and c.branch_id='".$branch_id."' GROUP BY w.client_id";
		$result = query_by_id($sql,[],$conn);
		if($result){
			foreach($result as $row){
				if($row['wallet_amount'] > 0){
					$data['client_id'] = $row['client_id'];
					$data['wallet_amount'] = number_format(($row['wallet_amount'] - wallet_money_used($row['client_id']) - $wallet_amount_paid),2);
					$data['client_name'] = $row['client_name'];
					$data['cont'] = $row['cont'];
				}
			}
			
		}
		}
		echo json_encode($data);
	}
 
	
	
