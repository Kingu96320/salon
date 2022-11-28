<?php 
	include "./includes/db_include.php";
	$branch_id = $_SESSION['branch_id'];
	if(!isset($_SESSION['user_type'])){
		echo '<meta http-equiv="refresh" content="0; url=dashboard.php" />';die();
	}
	if(isset($_GET['eid']) && $_GET['eid']>0)
	{
		$eid = $_GET['eid'];
		$edit = query_by_id("SELECT * FROM `membership_discount` where id=:eid",["eid"=>$eid],$conn)[0];
	}
	
	if(isset($_POST['edit-submit'])){
		$eid = $_GET['eid'];
		$membership_name 			= $_POST['membership_name'];
		$min_reward_points_earned 	= $_POST['min_reward_points_earned'];
		$min_bill_amount 			= $_POST['min_bill_amount'];
		$discount_on_service 		= $_POST['discount_on_service'].",".$_POST['discount_on_service_disc_type'];
		$discount_on_product 		= $_POST['discount_on_product'].",".$_POST['discount_on_product_disc_type'];
		$discount_on_package 		= $_POST['discount_on_package'].",".$_POST['discount_on_package_disc_type'];
		$reward_points_boost 		= $_POST['reward_points_boost'];
		$membership_price 			= $_POST['membership_price'];
		$reward_points_on_purchase 	= $_POST['reward_points_on_purchase'];
		$validity     				= $_POST['validity'];
		$condition     				= $_POST['mem_condition'];
		
		query("UPDATE `membership_discount` set membership_name=:membership_name,min_reward_points_earned=:min_reward_points_earned,min_bill_amount=:min_bill_amount,discount_on_service=:discount_on_service,discount_on_product=:discount_on_product,discount_on_package=:discount_on_package,reward_points_boost=:reward_points_boost,membership_price=:membership_price,reward_points_on_purchase=:reward_points_on_purchase,validity=:validity,mem_condition=:condition where id=:eid",
												[
					 'membership_name'			=>$membership_name,
					 'min_reward_points_earned'	=>$min_reward_points_earned,	
					 'min_bill_amount'			=>$min_bill_amount,	
					 'discount_on_service'		=>$discount_on_service,	
					 'discount_on_product'		=>$discount_on_product,	
					 'discount_on_package'		=>$discount_on_package,	
					 'reward_points_boost'		=>$reward_points_boost,
					 'membership_price'			=>$membership_price,
					 'reward_points_on_purchase'=>$reward_points_on_purchase,
					 'validity'				    =>$validity,
					 'condition'                =>$condition,
					 'eid'                      =>$eid, 
												
												],$conn);
		$_SESSION['t']  = 1;
		$_SESSION['tmsg']  = "Membership Updated Successfully";
		echo '<meta http-equiv="refresh" content="0; url=memberships.php?eid='.$eid.'" />';die();
	}
	if(isset($_POST['submit'])){
		$membership_name 			= $_POST['membership_name'];
		$min_reward_points_earned 	= $_POST['min_reward_points_earned'];
		$min_bill_amount 			= $_POST['min_bill_amount'];
		$discount_on_service 		= $_POST['discount_on_service'].",".$_POST['discount_on_service_disc_type'];
		$discount_on_product 		= $_POST['discount_on_product'].",".$_POST['discount_on_product_disc_type'];
		$discount_on_package 		= $_POST['discount_on_package'].",".$_POST['discount_on_package_disc_type'];
		$reward_points_boost 		= $_POST['reward_points_boost'];
		$membership_price 			= $_POST['membership_price'];
		$reward_points_on_purchase 	= $_POST['reward_points_on_purchase'];
		$validity     				= $_POST['validity'];
		$condition     				= $_POST['mem_condition'];
		
		query("INSERT INTO `membership_discount` set membership_name=:membership_name,min_reward_points_earned=:min_reward_points_earned,min_bill_amount=:min_bill_amount,discount_on_service=:discount_on_service,discount_on_product=:discount_on_product,discount_on_package=:discount_on_package,reward_points_boost=:reward_points_boost,membership_price=:membership_price,reward_points_on_purchase=:reward_points_on_purchase,validity=:validity,mem_condition=:condition, branch_id='0'",
					[
					 'membership_name'			=>$membership_name,
					 'min_reward_points_earned'	=>$min_reward_points_earned,	
					 'min_bill_amount'			=>$min_bill_amount,	
					 'discount_on_service'		=>$discount_on_service,	
					 'discount_on_product'		=>$discount_on_product,	
					 'discount_on_package'		=>$discount_on_package,	
					 'reward_points_boost'		=>$reward_points_boost,
					 'membership_price'			=>$membership_price,
					 'reward_points_on_purchase'=>$reward_points_on_purchase,
					 'validity'				    =>$validity,
					 'condition'                =>$condition,
					],$conn);
		$_SESSION['t']  = 1;
		$_SESSION['tmsg']  = "Membership Added Successfully";
		echo '<meta http-equiv="refresh" content="0; url=memberships.php" />';die();
	}
	
	if(isset($_GET['del'])){
	    if(DELETE_BUTTON_INACTIVE != 'true'){
    		$del=$_GET['del'];
    		query("UPDATE `membership_discount` SET `status`='0' WHERE id=$del",[],$conn);
    		// query("UPDATE `membership_discount_history` SET `status`='0' WHERE md_id=$del",[],$conn);
    		$_SESSION['t']  = 1;
    		$_SESSION['tmsg']  = "Data Removed Successfully";
    		echo '<meta http-equiv="refresh" content="0; url=memberships.php" />';die();
	    }
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
			
			<!--<div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">-->
			<!--	<div class="panel">-->
			<!--		<div class="panel-heading">-->
			<!--			<h4>Membership stats</h4>-->
			<!--		</div>-->
			<!--		<div class="panel-body">-->
			<!--			<div id="pieChart" class="chart-height c3">-->
			<!--			</div>-->
			<!--		</div>-->
			<!--	</div>-->
			<!--</div>-->
			<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
				<div class="panel">
					<div class="panel-heading">
						<h4><?= isset($_GET['eid'])?'Update':'Create'?> membership</h4>
					</div>
					<div class="panel-body">
						<div class="row">
							<form action="" method="post" id="main-form">
								<div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
									<div class="form-group">
										<label for="userName">Membership type name <span class="text-danger">*</span></label>
										<input type="text" class="form-control" name="membership_name" placeholder="Membership name" value="<?= isset($eid)?$edit['membership_name']:''?>"   required>
									</div>
								</div>
								<div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
									<div class="form-group">
										<label for="userName">Membership price <span class="text-danger">*</span></label>
										<input type="text" class="form-control" name="membership_price" placeholder="Membership price" value="<?= isset($eid)?$edit['membership_price']:''?>"   required>
									</div>
								</div>
								<div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
									<div class="form-group">
										<label for="userName" >Duration (in days start from day of purchase) <span class="text-danger">*</span></label>
										<input type="text" class="form-control"  name="validity" maxlength="4" placeholder="0" value="<?= isset($eid)?$edit['validity']:''?>" required>
									</div>
								</div>
								<div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
									<div class="form-group">
										<label for="userName">Reward points on purchase <span class="text-danger">*</span></label>
										<input type="text" class="form-control"  name="reward_points_on_purchase" placeholder="0" value="<?= isset($eid)?$edit['reward_points_on_purchase']:''?>" required>
									</div>
								</div>
								<div class="clearfix"></div>
								<div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
									<div class="form-group">
										<label for="userName" >Discount on services <span class="text-danger">*</span></label>
										<div class="row">
										    <div class="col-md-7">
										        <input type="text" class="form-control" value="<?= isset($eid)?explode(',',$edit['discount_on_service'])[0]:'0'?>" name="discount_on_service" placeholder="" required>
										    </div>
										    <div class="col-md-5">
									        	<select class="form-control" name="discount_on_service_disc_type" id="disc_row_type">
													<option <?=(explode(",",$edit['discount_on_service'])[1] == 'pr')?'value="pr" Selected':'value="pr"'?>>%</option>
													<option <?=(explode(",",$edit['discount_on_service'])[1] == CURRENCY)?'value="'.CURRENCY.'" Selected':'value="'.CURRENCY.'"'?>><?=CURRENCY?></option>
											    </select>
										    </div>
										</div>
									</div>
								</div>
								<div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
									<div class="form-group">
										<label for="userName">Discount on products <span class="text-danger">*</span></label>
										<div class="row">
										    <div class="col-md-7">
										        <input type="text" class="form-control" value="<?=isset($eid)?explode(',',$edit['discount_on_product'])[0]:'0'?>" name="discount_on_product" placeholder="" required>
										    </div>
										    <div class="col-md-5">
									        	<select class="form-control" name="discount_on_product_disc_type" id="disc_row_type">
													<option <?=(explode(",",$edit['discount_on_product'])[1] == 'pr')?'value="pr" Selected':'value="pr"'?>>%</option>
													<option <?=(explode(",",$edit['discount_on_product'])[1] == CURRENCY)?'value="'.CURRENCY.'" Selected':'value="'.CURRENCY.'"'?>><?=CURRENCY?></option>
												</select>
										    </div>
										</div>
									</div>
								</div>
								<div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
									<div class="form-group">
										<label for="userName">Discount on packages <span class="text-danger">*</span></label>
										<div class="row">
										    <div class="col-md-7">
										        <input type="text" class="form-control" value="<?=isset($eid)?explode(',',$edit['discount_on_package'])[0]:'0'?>" name="discount_on_package" placeholder="" required>
										    </div>
										    <div class="col-md-5">
									        	<select class="form-control" name="discount_on_package_disc_type">
													<option <?=(explode(",",$edit['discount_on_package'])[1] == 'pr')?'value="pr" Selected':'value="pr"'?>>%</option>
													<option <?=(explode(",",$edit['discount_on_package'])[1] == CURRENCY)?'value="'.CURRENCY.'" Selected':'value="'.CURRENCY.'"'?>><?=CURRENCY?></option>
												</select>
										    </div>
										</div>
									</div>
								</div>
								<div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
									<div class="form-group">
										<label for="userName">Reward points boost <span class="text-danger">*</span></label>
										<div class="row">
										    <div class="col-md-12">
										        <select class="form-control" name="reward_points_boost">
        											<option <?=($edit['reward_points_boost'] == '1')?'value="1" Selected':'value="1"'?>>1X</option>
        											<option <?=($edit['reward_points_boost'] == '2')?'value="2" Selected':'value="2"'?> >2X</option>
        											<option <?=($edit['reward_points_boost'] =='3')?'value="3" Selected':'value="3"'?>>3X</option>
        											<option <?=($edit['reward_points_boost'] == '4')?'value="4" Selected':'value="4"'?> >4X</option>
        										</select>
										    </div>
										</div>
									</div>
								</div>
								<div class="clearfix"></div>
								<div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
									<div class="form-group">
										<label for="userName">Min. reward points earned <span class="text-danger">*</span> <i class="icon-info2" data-toggle="tooltip"  title="Membership will be automaticaly granted to client when min. reward points earned."></i> </label>
										<input type="text" class="form-control"  name="min_reward_points_earned" placeholder="" value="<?= isset($eid)?$edit['min_reward_points_earned']:'0'?>" required>
									</div>
								</div>
								
								<div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
									<div class="form-group">
										<label for="userName">Condition </label>
										<select class="form-control" name="mem_condition">
										    <option <?= isset($eid)?$edit['mem_condition']==1?'selected':'':''?> value="1">AND</option>
										    <option <?= isset($eid)?$edit['mem_condition']==2?'selected':'':''?> value="2">OR</option>
										</select>
									</div>
								</div>
								<div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
									<div class="form-group">
										<label for="userName" >Min. billed amount <span class="text-danger">*</span> <i class="icon-info2" data-toggle="tooltip"  title="Membership will be automaticaly granted to client when min. total amount of bills are generated."></i></label>
										<input type="text" class="form-control"  name="min_bill_amount" placeholder="" value="<?= isset($eid)?$edit['min_bill_amount']:'0'?>" required>
									</div>
								</div>
								
								<div class="clearfix"></div>

								<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
									<div class="form-group">
										<?php if(isset($eid))
											{	
											?>
											<button type="submit" name="edit-submit" class="btn btn-info pull-right" onClick="loader()"><i class="fa fa-pencil-square-o" aria-hidden="true"></i>Update Membership</button>
											<?php } else
											{ ?>	
											<button type="submit" name="submit" class="btn btn-success pull-right" onClick="loader()"><i class="fa fa-plus" aria-hidden="true"></i>Add Membership</button>
										<?php }?>		
									</div>
								</div>
							</form>
						</div>
					</div>
				</div>
			
			</div>
	</div>		
			<!-- Row starts -->
				<div class="row gutter">
					<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
						<div class="panel">
							<div class="panel-heading">
								<h4>Manage membership</h4>
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
														<th>Min. billed amount </th>
														<th>Discount on services</th>
														<th>Discount on Products</th>
														<th>Discount on Packages</th>
														<th>Reward points boost</th>
														<th>Reward points on purchase</th>
														<th>Validity</th>
														<th>Action</th>
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
																<td><?=$row_mem['min_bill_amount']; ?></td>
																<td><?=explode(',',$row_mem['discount_on_service'])[0]." ".(explode(",",$row_mem['discount_on_service'])[1] === 'pr'?'%':CURRENCY)?></td>
																<td><?=explode(',',$row_mem['discount_on_product'])[0]." ".(explode(",",$row_mem['discount_on_product'])[1] === 'pr'?'%':CURRENCY)?></td>
																<td><?=explode(',',$row_mem['discount_on_package'])[0]." ".(explode(",",$row_mem['discount_on_package'])[1] === 'pr'?'%':CURRENCY)?></td>
																<td><?=$row_mem['reward_points_boost'];?>X</td>
																<td><?=$row_mem['reward_points_on_purchase'];?></td>
																<td><?=$row_mem['validity']; ?> Days</td>
															    <td><a href="memberships.php?eid=<?=$row_mem['id']; ?>"><button class="btn btn-warning btn-xs"  type="button"><i class="fa fa-pencil-square-o" aria-hidden="true"></i>Edit</button></a> 
															    <?php if(DELETE_BUTTON_INACTIVE == 'true'){ ?>
															        <a href="#" onclick="return deleteDisabled();"><button class="btn btn-danger btn-xs"  type="button"><i class="icon-delete"></i>Delete</button></a></td>
															    <?php } else { ?>
															        <a href="memberships.php?del=<?=$row_mem['id']; ?>" onClick="return confirm('Are you sure');"><button class="btn btn-danger btn-xs"  type="button"><i class="icon-delete"></i>Delete</button></a></td>
														        <?php } ?>
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
		
		
													
		