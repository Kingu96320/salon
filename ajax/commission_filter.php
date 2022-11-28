<?php 
	include_once '../includes/db_include.php';
	$branch_id = $_SESSION['branch_id'];
	if(isset($_POST['filter_type']) && $_POST['filter_type'] == 'commission'){
		$start_date = $_POST['startdate'];
		$end_date = $_POST['enddate'];
		$service_type = $_POST['service_type'];
		$commission_for = $_POST['commission_for'];
		$pid = $_POST['pid'];

		// checking both start date and end date
		if($start_date != '' && $end_date !=''){
			$dat = "and i.doa BETWEEN '".$start_date."' AND '".$end_date."'";
		} else {
			$dat = "";
		}

		// checking commission type(service, product)
		if($commission_for != ''){
			$commission_for = "and ii.type='".$commission_for."'";
		} else {
			$commission_for = "";
		}

		// checking service type of service provider
		if($service_type != ''){
			$service_type = "and ii.service='".$service_type."'";
		} else {
			$service_type = "";
		}

		$sql ="SELECT ii.quantity,i.doa,b.name as service_provider,imsp.service_name,b.cont,ii.price as service_price, i.id as bill_id, ii.package_service_id from `invoice_multi_service_provider` imsp "
			." LEFT JOIN `invoice_items_".$branch_id."` ii on ii.id=imsp.ii_id "
			." LEFT JOIN `invoice_".$branch_id."` i on i.id=ii.iid " 
			." LEFT JOIN `beauticians` b on b.id = imsp.service_provider"
			." LEFT JOIN `service` s on s.id = SUBSTRING_INDEX(ii.service,',',-1)"
			." WHERE imsp.service_provider='$pid' and i.active=0 and imsp.branch_id='".$branch_id."' ".$dat." ".$commission_for." ".$service_type." order by i.doa desc";
			//echo $sql;
			$result=query_by_id($sql,[],$conn);
			if($result){
			    $total_amount = 0;
				foreach($result as $row){
				 
				 if(explode(",",$row['service_name'])[1] >0){
					 $ser_id = $row['service_name'];
					 $service_name = getservice($ser_id);
				}else{
					  $service_name = '';
				}
				
				if($row['package_service_id'] > 0){
					$row['service_price'] = query_by_id("SELECT price FROM service WHERE id='".explode(",",$row['service_name'])[1]."'",[],$conn)[0]['price'];
				}
				
				?>
				<tr>
						<td><?= my_date_format($row['doa']); ?></td>
						<td><?= number_format($row['service_price'],2); ?></td>
						<td><?= ucfirst($service_name) ?></td>
						<!--<td><?=$row['quantity']; ?></td>-->
						<td><?php 
				if(explode(" ", $service_name)[0] == '(Service)'){
				    $amount = getservicecom($row['service_price'],$pid,$ser_id, $row['bill_id']);
				    $total_amount = $amount+$total_amount;
				    echo number_format(getservicecom($row['service_price'],$pid,$ser_id, $row['bill_id']),2); 
				} else if(explode(" ", $service_name)[0] == '(Product)'){
				    $amount = getprodcom($row['service_price'],$pid,$ser_id, $row['bill_id']);
				    $total_amount = $amount+$total_amount;
				    echo number_format(getprodcom($row['service_price'],$pid,$ser_id, $row['bill_id']),2); 
				} else {
				    echo '0.00';
				} ?></td>
				<td class="text-center"><u><a target="_blank" href="invoice.php?inv=<?= $row['bill_id'] ?>"><?= $row['bill_id'] ?></a></u></td>
				 
				</tr>
			<?php 
				} ?>
				<tr>
				    <td></td><td></td>
				    <td><strong>Total</strong></td>
				    <td><strong><?= number_format($total_amount,2) ?></strong></td>
				    <td></td>
				</tr>
				<?php
			} else { ?>
				<tr><td colspan='5' class="text-center">No record found!!</td></tr>
				<?php 
			}
	}


	// function to get all commission data of service provider

	if(isset($_POST['filter_type']) && $_POST['filter_type'] == 'allcommission'){
		$pid = $_POST['pid'];
		$sql ="SELECT ii.quantity,i.doa,b.name as service_provider,imsp.service_name,b.cont,ii.price as service_price, i.id as bill_id, ii.package_service_id from `invoice_multi_service_provider` imsp "
			." LEFT JOIN `invoice_items_".$branch_id."` ii on ii.id=imsp.ii_id "
			." LEFT JOIN `invoice_".$branch_id."` i on i.id=ii.iid " 
			." LEFT JOIN `beauticians` b on b.id = imsp.service_provider"
			." LEFT JOIN `service` s on s.id = SUBSTRING_INDEX(ii.service,',',-1)"
			." WHERE imsp.service_provider='$pid' and i.active=0 and i.branch_id='".$branch_id."' order by i.doa desc";
			
			$result=query_by_id($sql,[],$conn);
// 			echo get_row_count_new($sql,[],$conn);
			if($result){
			    $total_amount = 0;
				foreach($result as $row){
				    
				print_r($row); 
				
				 if(explode(",",$row['service_name'])[1] >0){
					 $ser_id = $row['service_name'];
					 $service_name = getservice($ser_id);
				}else{
					  $service_name = '';
				}
				
				if($row['package_service_id'] > 0){
					$row['service_price'] = query_by_id("SELECT price FROM service WHERE id='".explode(",",$row['service_name'])[1]."'",[],$conn)[0]['price'];
				}
				
				?>
				<tr>
						<td><?= my_date_format($row['doa']); ?></td>
						<td><?= number_format($row['service_price'],2); ?></td>
						<td><?= ucfirst($service_name); ?></td>
						<!--<td><?=$row['quantity']; ?></td>-->
						<td><?php 
				if(explode(" ", $service_name)[0] == '(Service)'){ 
				    $amount = getservicecom($row['service_price'],$pid,$ser_id, $row['bill_id']);
				    $total_amount = $amount+$total_amount;
				    echo number_format(getservicecom($row['service_price'],$pid,$ser_id, $row['bill_id']),2); }
				else if(explode(" ", $service_name)[0] == '(Product)'){
				    $amount = getprodcom($row['service_price'],$pid,$ser_id, $row['bill_id']);
				    $total_amount = $amount+$total_amount;
				    echo number_format(getprodcom($row['service_price'],$pid,$ser_id, $row['bill_id']),2); 
				    } else {
				        echo '0.00';
				    } ?></td>
				<td class="text-center"><u><a target="_blank" href="invoice.php?inv=<?= $row['bill_id'] ?>"><?= $row['bill_id'] ?></a></u></td>
				 
				</tr>
			<?php 
				}
				?>
				<tr>
				    <td></td><td></td>
				    <td><strong>Total</strong></td>
				    <td><strong><?= number_format($total_amount,2) ?></strong></td>
				    <td></td>
				</tr>
				<?php
			} else { ?>
				<tr><td colspan='5' class="text-center">No record found!!</td></tr>
				<?php 
			}
	}

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
		$provider_id = $_POST['pid'];
		$type = 'Service';
		$total_sale = service_provider_total_sale($type, $provider_id, $id);
		$commission_per = service_provider_commission($type, $provider_id, $total_sale);
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
		$provider_id = $_POST['pid'];
		$type = 'Product';
		$total_sale = service_provider_total_sale($type, $provider_id, $id);
		$commission_per = service_provider_commission($type, $provider_id, $total_sale);
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
// 		$sql="SELECT * from beauticians where id='$bid' and active='0'";
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
			$sql ="SELECT CONCAT('(Service)',' ',name) as name FROM `service` where id='$id'";	
		}else if(EXPLODE(",",$get_id)[0] == 'pr'){
			$sql ="SELECT CONCAT('(Product)',' ',name) as name FROM `products` where id='$id'";	
		}else if(EXPLODE(",",$get_id)[0] == 'pa'){
			$sql ="SELECT CONCAT('(Package)',' ',name) as name FROM `packages` where id='$id' and branch_id='".$branch_id."'";	
		}else if(EXPLODE(",",$get_id)[0] == 'prepaid'){
			$sql ="SELECT CONCAT('(Prepaid)',' ',pack_name) as name FROM `prepaid` where id='$id' and branch_id='".$branch_id."'";	
		}else if(EXPLODE(",",$get_id)[0] == 'mem'){
			$sql ="SELECT CONCAT('(Membership)',' ',membership_name) as name FROM `membership_discount` where id='$id'";
		}
	    $result=query_by_id($sql,[],$conn);
	    foreach($result as $row) {
	    	return $row['name'];
	    }
	}
?>