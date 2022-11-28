<?php 
	include "../includes/db_include.php";
	$branch_id = $_SESSION['branch_id'];
	if(isset($_GET['cid'])){
		
		$cid = $_GET['cid'];
		$sid = $_GET['sid'];
		$index_value = $_GET['index_value'];
		$select_quantity = 0;
		
		$array=json_decode($_GET['count_qt'],true);
		$result = array();
		foreach($array as $k => $v) {
			$id = $v['pck_id'];
			$result[$id][] = $v['qt'];
		}
		
		$new = array();
		foreach($result as $key => $value) {
			$new[] = array('pck_id' => $key, 'qt' => array_sum($value));
		}
		
		$sql="SELECT  GROUP_CONCAT(cpsu.inv) as inv,p.name as package_name,p.valid,s.name as service_name, s.price as price, cpsu.c_service_id,cpsu.client_id,cpsu.c_pack_id,sum(cpsu.quantity) - sum(cpsu.quantity_used) as qt,cpsu.quantity_used FROM `client_package_services_used` cpsu "
		." LEFT JOIN `packages` p on p.id=cpsu.c_pack_id "
		." LEFT JOIN `service` s on s.id=SUBSTRING_INDEX(cpsu.c_service_id,',',-1)"
		." where cpsu.client_id='$cid' and cpsu.c_service_id='$sid' and cpsu.active='1' and cpsu.branch_id='".$branch_id."' GROUP BY cpsu.c_pack_id";
 		//echo $sql;
		$result  =query_by_id($sql,[],$conn);
		
		$table='<div class="table-responsive">
		<table id="table" class="table table-bordered no-margin">
		<thead>
		<tr>
		<th width="20%">Package validity </th>
		<th width="20%">Package</th>
		<th width="30%">Service</th>
		<th width="5%">Quantities Left</th>
		<th width="10%">Select quantity</th>
		<th width="5%">Action</th>
		</tr>
		</thead>
		<tbody>';
		$pack_count_qt=[];
		$i=0;
		//$valid = 0;
		foreach($result as $row) {
			$su_sql="SELECT sum(ii.quantity) as used_quantity FROM `invoice_items` ii where ii.client='$cid' and service = '$sid' and active='0' and package_id='".$row['c_pack_id']."' and ii.branch_id='".$branch_id."'  GROUP BY  ii.package_id";
			//echo $su_sql;
			$used_quantity=get_row_count($su_sql,[],$conn);
			$used_quantity1=query_by_id($su_sql,[],$conn);
			
			$used_quantity_count=0;
			if($used_quantity > 0){
				foreach($used_quantity1 as $row_used_quantity){
					$used_quantity_count +=$row_used_quantity['used_quantity'];
				}
			}
			foreach ($new as $row1) {
				if($row['c_pack_id'] == $row1['pck_id']){
					$pack_count_qt[$i]['pkq'] = $row1['qt'];
				}
			}
			
			if(count($array) == 0){

				if(check_package_expiry($row['client_id'], $row['c_pack_id']) == 1){
				    $valid = 1;
    				if($row['qt'] - $used_quantity_count - ($pack_count_qt[$i]['pkq']) > 0) {
    				$table .='<tr><td>'.$row['valid'].'<input type="hidden" class="valid" value='.$row['valid'].'></td>';
    				$table .='<td>'.$row['package_name'].'<input type="hidden" class="pack_name" value='.$row['c_pack_id'].'></td>';
    				$table .='<td>'.$row['service_name'].'<input type="hidden" class="ser_name" value='.$row['c_service_id'].'></td>';
    				$table .='<td>'.($row['qt'] - $used_quantity_count - ($select_quantity)).'<input type="hidden" class="act_qt" value='.($row['qt'] - ($select_quantity)).'><input type="hidden" class="inv" value='.($row['inv']).'><input type="hidden" class="package_id_ck" value='.($row['c_pack_id']).'></td>';
    				$table .='<td><input style="width:50px" type="number" name="qt" id="package_service_qt" class="positivenumber form-control center" value="1" min="1" max="'.($row['qt']- $used_quantity_count -($select_quantity)).'"></td>';
    				$table .='<td><button style="background-color: #4cbe71;
    				border-color: #4cbe71;
    				color: #ffffff;"class="btn btn-success btn-xs use" type="button" onClick="use_service($(this),'.$index_value.', '.$row["price"].','.packageDiscount($row['c_pack_id']).')" >Use</button><input type="hidden" class="index_value" value='.$index_value.'></td>
    				</tr>';
    				}
				}
			    
			}else{
			    if(check_package_expiry($row['client_id'], $row['c_pack_id']) == 1){
			    $valid = 1;
    				if($row['qt'] - $used_quantity_count - ($pack_count_qt[$i]['pkq']) > 0) {
    				$table .='<tr><td>'.$row['valid'].'<input type="hidden" class="valid" value='.$row['valid'].'></td>';
    				$table .='<td>'.$row['package_name'].'<input type="hidden" class="pack_name" value='.$row['c_pack_id'].'></td>';
    				$table .='<td>'.$row['service_name'].'<input type="hidden" class="ser_name" value='.$row['c_service_id'].'></td>';
    				$table .='<td>'.($row['qt'] - $used_quantity_count - ($pack_count_qt[$i]['pkq'])).'<input type="hidden" class="act_qt" value='.($row['qt'] - ($pack_count_qt[$i]['pkq'])).'><input type="hidden" class="inv" value='.($row['inv']).'><input type="hidden" class="package_id_ck" value='.($row['c_pack_id']).'></td>';
    				$table .='<td><input style="width:50px" type="number" name="qt" id="package_service_qt" class="positivenumber form-control center" value="1" min="1" max="'.($row['qt']- $used_quantity_count -($pack_count_qt[$i]['pkq'])).'"></td>';
    				$table .='<td><button style="background-color: #4cbe71;
    				border-color: #4cbe71;
    				color: #ffffff;"class="btn btn-success btn-xs use" type="button" onClick="use_service($(this),'.$index_value.', '.$row["price"].','.packageDiscount($row['c_pack_id']).')" >Use</button><input type="hidden" class="index_value" value='.$index_value.'></td>
    				</tr>';
    				}
				}}
			$i++;
		}
		//if($valid == 1){
		    echo $table .='</tbody></table> </div>';
		//}
		
		}else if(isset($_POST['check_cid']) && isset($_POST['check_sid'])){
		$available_services=[];
		$cid = $_POST['check_cid'];
		$sid = $_POST['check_sid'];
		$package_id_count_qt = $_POST['package_id_count_qt'];
		
		$select_quantity = 0;
		
		$array=json_decode($_POST['count_qt'],true);
		$result = array();
		foreach($array as $k => $v) {
			$id = $v['pck_id'];
			$result[$id][] = $v['qt'];
		}
		
		$new = array();
		foreach($result as $key => $value) {
			$new[] = array('pck_id' => $key, 'qt' => array_sum($value));
		}
		
		$sql_avail="SELECT cpsu.c_pack_id,sum(cpsu.quantity) - sum(cpsu.quantity_used) as available_services,cpsu.c_service_id, cpsu.client_id,cpsu.c_pack_id, p.valid FROM `client_package_services_used` cpsu LEFT JOIN `packages` p on p.id=cpsu.c_pack_id LEFT JOIN `service` s on s.id=SUBSTRING_INDEX(cpsu.c_service_id,',',-1) where cpsu.client_id='$cid' and cpsu.c_service_id='$sid' and cpsu.active='1' and cpsu.branch_id='".$branch_id."' GROUP BY cpsu.c_pack_id";
		$result =query_by_id($sql_avail,[],$conn);
		$i=0;
		foreach($result as $row) {
			
			$su_sql="SELECT sum(ii.quantity) as used_quantity FROM `invoice_items` ii where ii.client='$cid' and service = '$sid' and active='0' and package_id='".$row['c_pack_id']."' and ii.branch_id='".$branch_id."' GROUP BY ii.package_id";
			
			$used_quantity =get_row_count($su_sql,[],$conn);
			$used_quantity1=query_by_id($su_sql,[],$conn);
			$used_quantity_count=0;
			
			if($used_quantity > 0){
				foreach($used_quantity1 as $row_used_quantity){
					$used_quantity_count +=$row_used_quantity['used_quantity'];
				}
			}
			
			foreach ($new as $row1) {
				if($row['c_pack_id'] == $row1['pck_id']){
					$pack_count_qt[$i]['pkq'] = $row1['qt'];
				}
			}
			if(check_package_expiry($row['client_id'], $row['c_pack_id']) == 1){
    			if(count($array) == 0){
    				$available_services[$i]['c_pack_id'] 		  = $row['c_pack_id'];
    				$available_services[$i]['available_services'] = $row['available_services'] - $used_quantity_count ;
    				$available_services[$i]['service_id'] 		  = $row['c_service_id'];
    				$available_services[$i]['valid'] = 1;
    				} else{
    				$available_services[$i]['c_pack_id'] 		  = $row['c_pack_id'];
    				$available_services[$i]['available_services'] = $row['available_services'] - $used_quantity_count - ($pack_count_qt[$i]['pkq']);
    				$available_services[$i]['service_id'] 		  = $row['c_service_id'];
    				$available_services[$i]['valid'] = 1;
    				
    			}
			} else {
			    $available_services[$i]['valid'] = 0;
			}
			$i++;	
		}
		echo JSON_ENCODE($available_services);
	}	


	if(isset($_POST['action']) && $_POST['action'] == 'package_history'){
		$package_id = $_POST['package_id'];
		$service_id = $_POST['service_id'];
		$result = query_by_id("SELECT * FROM package_service_history WHERE package_id = '".$package_id."' AND service_id='".$service_id."' AND status='1'",[],$conn);
		$data2 = [];
		if($result){
			$res['status'] = 1;
			foreach($result as $data){
				$date = query_by_id("SELECT doa FROM invoice_".$data['used_in_branch']." WHERE id='".$data['invoice_id']."'",[],$conn)[0]['doa'];
				$sub['used_in_branch'] = ucfirst(branch_by_id($data['used_in_branch']));
				$sub['quantity'] = $data['quantity'];
				$sub['invoice_id'] = $data['invoice_id'];
				$sub['used_on'] = my_date_format($date);
				array_push($data2, $sub);
			}
			$res['data'] = $data2;
		} else {
			$res['status'] = 0;
		}
		echo json_encode($res);
	}
?>