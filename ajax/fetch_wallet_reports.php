<?php
	include "../includes/db_include.php";
	$branch_id = $_SESSION['branch_id'];
	if(isset($_POST['client_id'])){
		$client_id = $_POST['client_id'];
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
			//$searchQuery = " and (c.name like '%".$searchValue."%' or 
			//c.cont like '%".$searchValue."%' ) ";
		}
		
		$sql="SELECT count(*) as allcount from wallet_history where status=1 and wallet_amount !=0 and client_id='$client_id' ".$searchQuery;
		## Total number of records without filtering
		$records = query_by_id($sql,[],$conn)[0];
		//$records = mysqli_fetch_assoc($sel);
		$totalRecords = $records['allcount'];
		
		## Total number of record with filtering
		$records = query_by_id("SELECT count(*) as allcount from wallet_history where status=1 and wallet_amount != 0 and client_id='$client_id'".$searchQuery,[],$conn)[0];
		//$records = mysqli_fetch_assoc($sel);
		$totalRecordwithFilter = $records['allcount'];
		
		## Fetch records
		$empQuery = "SELECT get_wallet_from,transaction_type,wallet_amount, SUBSTRING_INDEX(wh.iid,',',-1) as bill_id, paid_amount, pm.name as payment_method, time_update, wh.branch_id from `wallet_history` wh LEFT JOIN payment_method pm on pm.id=wh.payment_method where wh.status=1 and wh.wallet_amount != 0 and wh.client_id='$client_id'".$searchQuery." order by ".$columnName." ".$columnSortOrder." limit ".$row.",".$rowperpage;
		// echo $empQuery;
		$empRecords = query_by_id($empQuery,[],$conn);
		$data = array();
		if($empRecords){
			foreach($empRecords as $row) {
				if($row['payment_method'] == null){
					$payment_method = '';
				} else {
					$payment_method = $row['payment_method'];
				}
				
				if($row['bill_id'] == '0'){
				    $bill_id = '--';
				} else {
				    if($row['get_wallet_from'] == 'Bill'){
				        if($row['transaction_type'] == 2){
				            $bill_id = $row['bill_id'];
				        } else {
				            $bill_id = '<u>'.$row['bill_id'].'</u>';
				        }
				    } else if($row['get_wallet_from'] == 'Appointment'){
				        if($row['transaction_type'] == 2){
				            $bill_id = $row['bill_id'];
				        } else {
				            $bill_id = '<u>'.$row['bill_id'].'</u>';
				        }
				    } else {
				        if($row['get_wallet_from'] == 'Bill (Advance amount)'){
				            $bill_id = '<u>'.$row['bill_id'].'</u>';
				        } else if($row['get_wallet_from'] == 'Bill (pending payment)'){
				            $bill_id = $row['bill_id'];
				        } else {
				            $bill_id = '--';
				        }
				    }
				}

				if($row['transaction_type'] == 0){
					$type = "<span class='text-danger'>Debit</span>";
				} else if($row['transaction_type'] == 1){
					$type = "<span class='text-success'>Credit</span>";
				} else if($row['transaction_type'] == 2){
					$type = "<span class='text-warning'>Refunded</span>";
				} else {
					$type = '';
				}
					$data[] = array( 
					"time_update"           => my_date_format(explode(" ",$row['time_update'])[0])." ".my_time_format($row['time_update']),
					"branch_name"			=> ucfirst(branch_by_id($row['branch_id'])),
					"transaction_type"      => $type,
					"paid_amount"           => number_format($row['paid_amount'],2),
					"wallet_amount"         => number_format($row['wallet_amount'],2),
					"payment_method"        => $payment_method,
					"amount_received_from"  => $row['get_wallet_from'],
					"bill_id"               => $bill_id,
					);
				// }
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
	} 
	
	
	
