<?php
	include_once '../includes/db_include.php';
	$branch_id = $_SESSION['branch_id'];
	if (isset($_GET['term'])){
		$return_arr = array();
		$term = $_GET['term'];
		$val = str_replace("%20", " ", $term);
        
        if(isset($_GET['ser_cat_id']) && $_GET['ser_cat_id']!=''){
            $sql = "select concat('sr,',id) as id,name as value,price,duration from service WHERE name LIKE '%$val%' and cat=:cat and active='0' and branch_id='".$branch_id."'";
            $result = query_by_id($sql,["cat"=>$_GET['ser_cat_id']],$conn);
            foreach($result as $row) 
            {
                $row['ser_stime'] = date('Y-m-d H:i:s',strtotime($_GET['ser_stime']));
                $row['ser_etime'] = date('Y-m-d H:i:s',strtotime($row['ser_stime'].' +'.$row['duration'].' minutes'));
                $return_arr[] =  $row;
			}
		}else if(isset($_GET['page_info']) && $_GET['page_info'] =='pu'){
		    $date = date('Y-m-d');
			$sql = "select concat('pr,',ps.product_id) as id, concat(concat(ps.product,' - ',ps.sale_price),' - ',DATE_FORMAT(ps.exp_date,'%d-%M-%Y')) as value,ps.sale_price as sale_price,'0' as duration,p.reward as points, ps.exp_date, ps.id as stock_id, p.* from purchase_items ps LEFT JOIN products p ON p.id = ps.product_id WHERE (ps.product LIKE '%$val%' OR barcode='".$val."') and ps.exp_date >= '".$date."' and p.active='0' and ps.branch_id='".$branch_id."'";
			$result = query_by_id($sql,[],$conn);
			foreach($result as $row) 
			{
				$row['ser_stime'] = date('Y-m-d H:i:s',strtotime($_GET['ser_stime']));
				$row['ser_etime'] = date('Y-m-d H:i:s',strtotime($row['ser_stime'].' +'.$row['duration'].' minutes'));
				$row['actual_stock'] = ((explode(",",$row['id'])[0]) == 'pr')?getstock(explode(",",$row['id'])[1],0,$row['stock_id']):'0';
				$return_arr[] =  $row;
			}
		}else if(isset($_GET['page_info']) && $_GET['page_info'] =='app'){
			$sql = "select concat('sr,',id) as id,name as value,price,duration from service WHERE name LIKE '%$val%' and active='0'";
			$result = query_by_id($sql,[],$conn);
			foreach($result as $row) 
			{
				$row['ser_stime'] = date('Y-m-d H:i:s',strtotime($_GET['ser_stime']));
				$row['ser_etime'] = date('Y-m-d H:i:s',strtotime($row['ser_stime'].' +'.$row['duration'].' minutes'));
				$row['actual_stock'] = ((explode(",",$row['id'])[0]) == 'pr')?getstock(explode(",",$row['id'])[1],0):'0';
				$return_arr[] =  $row;
			}
		}
		else if(isset($_GET['page_info']) && $_GET['page_info'] =='topservices'){
			$sql = "select bill.service as id, sum(bill.quantity) as quantity, s.name as value, s.price as price , s.duration as duration, s.points, 0 as stock_id from service s join invoice_items_".$branch_id." bill on SUBSTRING_INDEX(bill.service,',',-1) = s.id WHERE bill.type='Service' and s.id = SUBSTRING_INDEX(bill.service,',',-1) GROUP BY s.id ORDER by sum(bill.quantity) DESC limit 5";
			$result = query_by_id($sql,[],$conn);
			foreach($result as $row) 
			{
				$row['ser_stime'] = date('Y-m-d H:i:s',strtotime($_GET['ser_stime']));
				$row['ser_etime'] = date('Y-m-d H:i:s',strtotime($row['ser_stime'].' +'.$row['duration'].' minutes'));
				$row['actual_stock'] = ((explode(",",$row['id'])[0]) == 'pr')?getstock(explode(",",$row['id'])[1], 0):'0';
				$return_arr[] =  $row;
			}
		}
		else if(isset($_GET['page_info']) && $_GET['page_info'] =='service-slip'){
			$sql = "select concat('sr,',id) as id,name as value,price,duration from service WHERE name LIKE '%$val%' and active='0' and branch_id='".$branch_id."' UNION select concat('pr,',id) as id,name as value,price,'0' as duration from products WHERE name LIKE '%$val%' and active='0' and branch_id='".$branch_id."'";
			$result = query_by_id($sql,[],$conn);
			foreach($result as $row) 
			{
				$row['ser_stime'] = date('Y-m-d H:i:s',strtotime($_GET['ser_stime']));
				$row['ser_etime'] = date('Y-m-d H:i:s',strtotime($row['ser_stime'].' +'.$row['duration'].' minutes'));
				$row['actual_stock'] = ((explode(",",$row['id'])[0]) == 'pr')?getstock(explode(",",$row['id'])[1], 0):'0';
				$return_arr[] =  $row;
			}
		}
		else if(isset($_GET['type']) && $_GET['type']=='package_services'){
		    $sql = "select concat('sr,',s.id) as id, s.cat, s.name as value, s.price, s.duration, s.points, sc.cat as cat_name from service s LEFT JOIN servicecat sc ON sc.id = s.cat WHERE s.name LIKE '%$val%' and s.active='0'";
			$result = query_by_id($sql,[],$conn);
			foreach($result as $row) 
			{
				$row['ser_stime'] = date('Y-m-d H:i:s',strtotime($_GET['ser_stime']));
				$row['ser_etime'] = date('Y-m-d H:i:s',strtotime($row['ser_stime'].' +'.$row['duration'].' minutes'));
				$row['actual_stock'] = ((explode(",",$row['id'])[0]) == 'pr')?getstock(explode(",",$row['id'])[1], 0):'0';
				$return_arr[] =  $row;
			}
		} else if(isset($_GET['page_info']) && $_GET['page_info'] =='service_reminder'){
    		$sql = "select id,name as value from service WHERE name LIKE '%$val%' and active='0'";
    		$result = query_by_id($sql,[],$conn);
    		foreach($result as $row) 
    		{
    			$row['id'] = $row['id'];
    			$row['value'] = html_entity_decode(html_entity_decode($row['value']));
    			$return_arr[] =  $row;
    		}
    	} else {
    	    $date = date('Y-m-d');
			$sql = "select concat('sr,',id) as id, name as value,price,duration, points, '0000-00-00' as exp_date, 0 as stock_id, 'service' as type from service WHERE name LIKE '%$val%' and active='0'"
			." UNION select concat('pr,',ps.product_id) as id, concat(concat(ps.product,' - ',ps.sale_price),' - ',DATE_FORMAT(ps.exp_date,'%d-%M-%Y')) as value,ps.sale_price as price,'0' as duration,p.reward as points, ps.exp_date, ps.id as stock_id, 'product' as type from purchase_items ps LEFT JOIN products p ON p.id = ps.product_id WHERE (ps.product LIKE '%$val%' OR p.barcode LIKE '%$val%') and ps.exp_date >= '".$date."' and p.active='0' and ps.branch_id='".$branch_id."'"
			." UNION select concat('pa,',id) as id,name as value,price,'0' as duration,points, '0000-00-00' as exp_date, 0 as stock_id, 'package' as type from packages WHERE name LIKE '%$val%' and DATE_ADD(valid,INTERVAL duration DAY) >= curdate() and active='0' and branch_id='".$branch_id."'"
			." UNION select concat('mem,',id) as id,membership_name as value,membership_price, '0' as duration, reward_points_on_purchase as points, '0000-00-00' as exp_date, 0 as stock_id, 'membership' as type from membership_discount WHERE membership_name LIKE '%$val%' and status='1' order by exp_date ASC";
			
			$result = query_by_id($sql,[],$conn);
			
			
			
			
			foreach($result as $row)
			{
				$row['ser_stime'] = date('Y-m-d H:i:s',strtotime($_GET['ser_stime']));
				$row['ser_etime'] = date('Y-m-d H:i:s',strtotime($row['ser_stime'].' +'.$row['duration'].' minutes'));
				$row['actual_stock'] = ((explode(",",$row['id'])[0]) == 'pr')?getstock(explode(",",$row['id'])[1], 0):'0';
				$product_stock = ((explode(",",$row['id'])[0]) == 'pr')?getstock(explode(",",$row['id'])[1], 0,$row['stock_id'], 'product_to_use'):'0';
				if($row['type'] == 'product' && $product_stock <= 0){
				    continue;
				}
				$return_arr[] =  $row;
			}
		}
		
		
		
		echo json_encode($return_arr);
	}

	// function to show list of service that is available in purchased package of client.

	if(isset($_POST['action']) && $_POST['action'] == 'packages'){
		$sid = $_POST['sid']; // service id
		$cid = $_POST['cid']; // client id
		$return_arr = array();
		$arr = array();
		// $sql = "SELECT cp.inv as id, cp.package as package_id, cp.client as client_id, p.name as package_name, p.valid as validupto, ps.service as service_id, s.name as service_name FROM clientpackages cp LEFT JOIN packages as p on p.id = cp.package LEFT JOIN packageservice ps on p.id = ps.pid LEFT JOIN service s on s.id = SUBSTRING_INDEX(ps.service,',',-1) WHERE cp.active = '0' AND ps.active='0' AND p.active='0' AND cp.client = '".$cid."' AND s.id = SUBSTRING_INDEX('".$sid."',',',-1) 	 ORDER BY p.valid ASC";
		$sql = '';
		$total_branches = query_by_id("SELECT id FROM salon_branches WHERE status='1'",[],$conn);
		$branch_count = 1;
		foreach($total_branches as $branch){
			$sql .= "SELECT cpsu.id as id, i.id as inv_id, i.doa as purchase_date, cpsu.c_pack_id as package_id, cpsu.inv, cpsu.client_id as client_id, p.name as package_name, p.valid as validupto, cpsu.c_service_id as service_id, s.name as service_name FROM client_package_services_used cpsu LEFT JOIN packages p ON p.id = cpsu.c_pack_id LEFT JOIN service s ON s.id = SUBSTRING_INDEX('".$sid."',',',-1) LEFT JOIN invoice_".$branch['id']." i ON i.id = cpsu.inv WHERE cpsu.active = 1 AND cpsu.client_id = '".$cid."' AND cpsu.c_service_id = '".$sid."' and cpsu.branch_id='".$branch['id']."'";
			if($branch_count < count($total_branches)){
				$sql .= ' UNION ';
			}
			$branch_count += 1;
		}

		// $sql .=" ORDER BY packages.valid ASC ";

		$result = query_by_id($sql,[],$conn); 
		if(!empty($result)){
			?>
			<!-- available package -->
			<div id="avpackageModal" class="modal disableOutsideClick fade" role="dialog">
				<div class="modal-dialog modal-lg">		
					<div class="modal-content">
						<div class="modal-header">
							<h4 class="modal-title">Available package services</h4>
						</div>
						<div class="modal-body">
							<div class="table-responsive">
								<table class="table table-bordered table-striped">
									<thead>
										<th>Branch</th>
										<th>Service name</th>
										<th>Unit</th>
										<th>Package name</th>
										<th>Purchased on</th>
										<th>Expired on</th>
										<th width="100" class="text-center">Action</th>
									</thead>
									<tbody id="pckresult">
									<?php
									$total_branches = query_by_id("SELECT id FROM salon_branches WHERE status='1'",[],$conn);
									foreach($total_branches as $branch){
										foreach ($result as $res) {
											$stock = 0;
											if(strtotime(package_validity_date($res['client_id'], $res['package_id'], $res['inv'], $branch['id'])) > strtotime(date('d-m-Y'))){
												$stock = sum_total_quantity($res['package_id'],$res['client_id'],$res['service_id'], $res['id'], $branch['id']);
												if($stock > 0){
													?>
													<tr>
														<td><?= ucfirst(branch_by_id($branch['id'])); ?></td>
														<td><?= $res['service_name'] ?></td>
														<td><?= $stock ?></td>
														<td><?= $res['package_name'] ?></td>
														<td><?= my_date_format($res['purchase_date']) ?></td>
														<td><?= package_validity_date($res['client_id'], $res['package_id'], $res['inv'], $branch['id']); ?></td>
														<td class="text-center">
															<button type="button" onclick="usePackageService('<?= $res['service_id'] ?>','<?= $res['id'] ?>', $(this))" class="btn btn-sm btn-warning"><i class="fa fa-plus-circle" aria-hidden="true"></i>Use</button>
														</td>
													</tr>
													<?php
												} else {
													echo "";
													//return false;
												}
											}
										}
									}
									?>
									</tbody>
								</table>
							</div>
						</div>
						<div class="modal-footer">
							<button type="button" onclick="removeModal('avpackageModal')" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times" aria-hidden="true"></i>Close</button>
						</div>
					</div>		
				</div>
			</div>
			<?php
		}
		else 
		{
			echo "";
		}
	}


	// function to change tmp qty of the purchased package service

	if(isset($_POST['action']) && $_POST['action'] == 'tmpqty'){
		$id = $_POST['row_id'];
		$sql = "update client_package_services_used set tmp_qty = tmp_qty+1 where id='".$id."'";
		$result = query($sql,[],$conn); 
		echo "1";
	}

	// remove tmp qty of package service

	if(isset($_POST['action']) && $_POST['action'] == 'removeTemp'){
		$id = $_POST['id'];
		$temp_value = query("SELECT tmp_qty FROM client_package_services_used WHERE id = SUBSTRING_INDEX('".$id."','-',-1) and branch_id='".$branch_id."'",[],$conn)[0]['tmp_qty'];
		if($temp_value > 0){
			$sql = "update client_package_services_used set tmp_qty = tmp_qty-1 where id=SUBSTRING_INDEX('".$id."','-',-1) and branch_id='".$branch_id."' ";
			$result = query($sql,[],$conn); 
			echo "1";
		}
	}
	
	// 	autoservice reminder

	if(isset($_POST['page_info']) && $_POST['page_info'] != ''){
	    $return_arr = array();
	    $service_id = $_POST['page_info'];
	    $result = query_by_id("SELECT status FROM service_reminder WHERE service_id =  $service_id and branch_id='".$branch_id."'",[],$conn)[0];
	    if($result){
	        if($result['status'] == '0'){
	            $return_arr['status'] = 0;
	            $return_arr['msg'] = "Service already added, please make it active from the inactive service table.";
	        } else if($result['status'] == '1'){
	            $return_arr['status'] = 1;
	            $return_arr['msg'] = "Service already added.";
	        } else {
	            $return_arr['status'] = 2;
	            $return_arr['msg'] = "";
	        }
	    } else {
	        $return_arr['status'] = 2;
	        $return_arr['msg'] = "";
	    }
	    echo json_encode($return_arr);
	}
	
?>