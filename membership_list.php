<?php 
	include "./includes/db_include.php";
	$branch_id = $_SESSION['branch_id'];
	if(isset($_SESSION['user_type'])){
		echo '<meta http-equiv="refresh" content="0; url=dashboard.php" />';die();
	}
	if(isset($_GET['eid']) && $_GET['eid']>0)
	{
		$eid = $_GET['eid'];
		$edit = query_by_id("SELECT * FROM `membership_discount` where id=:eid",["eid"=>$eid],$conn)[0];
	}
	
	include "topbar.php";
	include "header.php";
	include "menu.php";
?>

<!-- Dashboard wrapper starts -->
<div class="dashboard-wrapper">
	
	<!-- Main container starts -->
	<div class="main-container">
			<!-- Row starts -->
				<div class="row gutter">
					<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
						<div class="panel">
							<div class="panel-heading">
								<h4>All memberships</h4>
							</div>
							<div class="panel-body">
								<div class="row">
									<div class="col-lg-12">
										<div class="table-responsive">
											<table class="table table-bordered grid">
												<thead>
													<tr>
														<th>Membership name</th>
														<th>Membership price</th>
														<th>Min. reward points earned</th>
														<th>Condition</th>
														<th>Min. billed amount </th>
														<th>Discount on services</th>
														<th>Discount on Products</th>
														<th>Discount on Packages</th>
														<th>Reward points boost</th>
														<th>Reward points on purchase</th>
														<th>Validity</th>
													</tr>
												</thead>
												<tbody>
													<?php $sql_mem=query_by_id("SELECT * FROM `membership_discount` where status=1",[],$conn);
														if($sql_mem){
															foreach($sql_mem as $row_mem){	
															?>
															<tr>
																<td><?=$row_mem['membership_name']; ?></td>
																<td><?=$row_mem['membership_price']; ?></td>
																<td><?=$row_mem['min_reward_points_earned']; ?></td>
																<td><?= $row_mem['mem_condition']==1?'AND':'OR'; ?></td>
																<td><?=$row_mem['min_bill_amount']; ?></td>
																<td><?=explode(',',$row_mem['discount_on_service'])[0]." ".(explode(",",$row_mem['discount_on_service'])[1] === 'pr'?'%':CURRENCY)?></td>
																<td><?=explode(',',$row_mem['discount_on_product'])[0]." ".(explode(",",$row_mem['discount_on_product'])[1] === 'pr'?'%':CURRENCY)?></td>
																<td><?=explode(',',$row_mem['discount_on_package'])[0]." ".(explode(",",$row_mem['discount_on_package'])[1] === 'pr'?'%':CURRENCY)?></td>
																<td><?=$row_mem['reward_points_boost'];?>X</td>
																<td><?=$row_mem['reward_points_on_purchase'];?></td>
																<td><?=$row_mem['validity']; ?> Days</td>
														</tr>
													<?php }} ?>
											</tbody>
										</table>
									</div>
								</div>
							</div>
						</div>
					</div>	
				</div>
			</div>
		 
			<!-- Row ends -->
			<!-- Row starts -->
		
		<!-- Main container ends -->
		
	</div>
	<!-- Dashboard Wrapper End -->
	
</div>
<!-- Container fluid ends -->
</div>
	<?php 
			include "footer.php";
		?>		
<script>
 
	var chart12 = c3.generate({
	bindto: '#pieChart',
	padding: {
		left: 40,
	},
	data: {
		 
		columns: [
		<?php $sql_mem_graph=query_by_id("SELECT * FROM `membership_discount` where status=1",[],$conn);
			  if($sql_mem_graph){
			  foreach($sql_mem_graph as $row_mem_graph){	
		?>
			['<?=$row_mem_graph['membership_name']?>', 60],
			  <?php } } ?>	
		],
		type : 'pie',
		colors: {
			Likes: '#55ACEE',
			Shares: '#4c6c7b',
			Sharedasds: '#4c6c7b',
		},
		onclick: function (d, i) { console.log("onclick", d, i); },
		onmouseover: function (d, i) { console.log("onmouseover", d, i); },
		onmouseout: function (d, i) { console.log("onmouseout", d, i); }
	}
});
 
 
	function confirmDelete()
	{
		return confirm('Are you sure?')
	}
	/*******Server_side_datatable*********/
		$(document).ready(function(){
			
			$('#empTable').DataTable({
				'processing': true,
				'serverSide': true,
				'serverMethod': 'post',
				'ajax': {
					'url':'ajax/fetch_wallet_details.php'
				},
				'columns': [
				{ data: 'time_update' },
				{ data: 'name' },
				{ data: 'cont' },
				{ data: 'wallet_amount' },
				{ data: 'remaning_amount' },
				{ data: 'payment_method' },
				{ data: 'action' },
				],
				'columnDefs': [ {
					'targets': [0,2,3,4,5], // column index (start from 0)
					'orderable': false, // set orderable false for selected columns
				}]
			});
		});
		/*******End********/
			$('#client_name').on('keypress',function(){
				$('#client_id').val("");
			});
			$("#client_name").autocomplete({
				source: "autocomplete/client.php",
				minLength: 1,
				select: function(event, ui) {
					event.preventDefault();
					$('#client_name').val(ui.item.name);
					$('#client_id').val(ui.item.id); 
					$('#cont').val(ui.item.cont); 
					$('#client-status').html("");
				}				
			});	
			
			$('#client_name').on('blur',function(){
				var client_id = $('#client_id').val();	
				if(client_id ==''){
					$('#client-status').html('Please select client name from list');
					$('#client_name').val("");
					$('#cont').val(""); 
				}
			});
			
			function checkcat() {
				var cat_id=$('#client_id').val();
				$.ajax({
					url: "ajax/checkservice.php",
					data:{category: cat,cat_id:cat_id},
					type: "POST",
					success:function(data){
						if(data == ﻿'1'){
							$("#check-status").html("Duplicate category . Please select category from list").css("color","red");
							$('#scat').val("");
							}else{
							$("#check-status").html("");
						}
					},
					error:function (){}
				});
			} 
			
			
		</script>
		
		
													
		