<?php 
	include_once '../includes/db_include.php';
	$branch_id = $_SESSION['branch_id'];
	if(isset($_GET['client_id']) && $_GET['client_id'] > 0){ 
		$client_id = $_GET['client_id'];
	?>
	<div class="modal-header">
		<!-- <button type="button" class="close" data-dismiss="modal" data-clinet-id="">&times;</button> -->
		<h4 class="modal-title">Client package</h4>
	</div>
	<div class="modal-body">
		<div class="table-responsive">
			<table id="table" class="table table-bordered no-margin">
				<thead>
					<tr>
						
						<th>Package name</th>
						<th>Branch</th>
						<th>Valid upto</th>
						<th>Package price</th>
						<th>Total services</th>
						<th>Services availed</th>
						<th>Action</th>
					</tr>
				</thead>
				<tbody>
					<?php 
						$total_branches = query_by_id("SELECT id FROM salon_branches WHERE status='1'",[],$conn);
						foreach($total_branches as $branch){
							$sql5 = "SELECT count(cpsu.c_pack_id) as pack_count,i.id as inv_id, p.name as package_name,p.valid,p.price,p.duration,s.name as service_name,cpsu.inv,cpsu.c_service_id,cpsu.client_id,cpsu.c_pack_id,sum(cpsu.quantity) as qt, sum(cpsu.quantity_used) as used_qty,cpsu.quantity_used FROM `client_package_services_used` cpsu "
							." LEFT JOIN `packages` p on p.id=cpsu.c_pack_id "
							." LEFT JOIN `service` s on s.id=SUBSTRING_INDEX(cpsu.c_service_id,',',-1)"
							." LEFT JOIN `invoice_".$branch['id']."` i on i.id = cpsu.inv"
							." where cpsu.active='1' and cpsu.client_id='".$client_id."' and cpsu.branch_id='".$branch['id']."' GROUP by cpsu.inv";
							$result5=query_by_id($sql5,[],$conn);
							foreach($result5 as $row5) {
								$days=$row5['duration'];
								$package_expiry_date=my_date_format(date('Y-m-d', strtotime($row5['valid']. ' + '.$days.' days')));
								if(strtotime(package_validity_date($row5['client_id'], $row5['c_pack_id'], $row5['inv_id'], $branch['id'])) > strtotime(date('d-m-Y'))) { 
							?>
							<tr>
								<td><?= $row5['package_name']; ?></td>
								<td><?= ucfirst(branch_by_id($branch['id'])) ?></td>
								<td><?= package_validity_date($row5['client_id'], $row5['c_pack_id'], $row5['inv_id'], $branch['id']); ?></td>
								<td><?= $row5['price']?></td>
								<td><?= $row5['qt']?></td>
								<td><?= $row5['used_qty']?></td>
								<?php if($branch_id == $branch['id']) { ?>
								<td><?php echo '<a href="clientpackage.php?cid='.$client_id.'&pid='.$row5['c_pack_id'].'&invid='.$row5['inv'].'" target="_blank"><button class="btn btn-info btn-xs" type="button"><i class="fa fa-eye" aria-hidden="true"></i>View</button></a>'; ?></td>
								<?php } else { echo "<td></td>"; }?>
							</tr>
							
						<?php } } } ?>
				</tbody>
			</table>
		</div>
		
	</div>	
	<div class="modal-footer">
		<button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times" aria-hidden="true"></i>Close</button>
	</div>
<?php  } ?>				