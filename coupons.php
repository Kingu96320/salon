<?php
	include "./includes/db_include.php";
	$branch_id = $_SESSION['branch_id'];
	if(isset($_GET['id']) && $_GET['id']>0){
		$id = $_GET['id'];
		$edit = query_by_id("SELECT * from coupons where id=:id and branch_id='".$branch_id."'",["id"=>$id],$conn)[0];
	}
	if(isset($_POST['submit'])){
		$code           = addslashes(trim(ucfirst($_POST['code'])));
		$discount       = addslashes(trim($_POST['discount']));
		$discount_type  = addslashes(trim($_POST['dis_type']));
		$max            = addslashes(trim($_POST['max']));
		$min            = addslashes(trim($_POST['min']));
		$c_per_user     = addslashes(trim($_POST['c_per_user']));
		$valid          = addslashes(trim($_POST['valid']));
		$reward_points  = addslashes(trim($_POST['reward_points']));
		query("INSERT INTO `coupons` set `ccode`=:ccode,`discount`=:discount,`discount_type`=:discount_type,`max_amount`=:max_amount,`min_amount`=:min_amount,`c_per_user`=:c_per_user,`valid`=:valid,`reward_points`=:reward_points,`active`=:active, `branch_id`='".$branch_id."'",[
																	'ccode'   	     =>$code,
																	'discount'		 =>$discount,
																	'discount_type'	 =>$discount_type,
																	'max_amount'	 =>$max,
																	'min_amount'     =>$min,
																	'c_per_user'     =>$c_per_user,
																	'valid'			 =>$valid,
																	'reward_points'	  =>$reward_points,
																	'active'		 =>0
																	],$conn);
		
		$_SESSION['t']  = 1;
		$_SESSION['tmsg']  = "Coupon Added Successfully";
		header('LOCATION:coupons.php');
		exit();
	}
	
	if(isset($_POST['edit-submit'])){
		$eid            = addslashes(trim($_POST['eid']));
		$code           = addslashes(trim(ucfirst($_POST['code'])));
		$discount       = addslashes(trim($_POST['discount']));
		$discount_type  = addslashes(trim($_POST['dis_type']));
		$max            = addslashes(trim($_POST['max']));
		$min            = addslashes(trim($_POST['min']));
	    $c_per_user     = addslashes(trim($_POST['c_per_user']));
		$valid          = addslashes(trim($_POST['valid']));
		$reward_points  = addslashes(trim($_POST['reward_points']));
		
		query("UPDATE `coupons` set `ccode`=:ccode,`discount`=:discount,`discount_type`=:discount_type,`max_amount`=:max_amount,`min_amount`=:min_amount,`c_per_user`=:c_per_user,`valid`=:valid,`active`=:active,`reward_points`=:reward_points where id=:eid and branch_id='".$branch_id."'",[
																  'ccode'=>$code,
																  'discount'=>$discount,
																  'discount_type'=>$discount_type,
																  'max_amount'=>$max,
																  'min_amount'=>$min,
																  'c_per_user'=>$c_per_user,
																  'valid'=>$valid,
																  'reward_points'=>$reward_points,
																  'active'=>0,
																  'eid'=>$eid
																  ],$conn);
		
		$_SESSION['t']  = 1;
		$_SESSION['tmsg']  = "Coupon Updated Successfully";
		header('LOCATION:coupons.php');
		exit();
	}
	
	if(isset($_GET['d'])){
	    if(DELETE_BUTTON_INACTIVE != 'true'){
    		$d = $_GET['d'];
    		query("update `coupons` set active=1 where id=$d and branch_id='".$branch_id."'",[],$conn);
    		$_SESSION['t']  = 1;
    		$_SESSION['tmsg']  = "Coupon Removed Successfully";
    		header('LOCATION:coupons.php');
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
			
			<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
				<div class="panel">
					<div class="panel-heading">
						<h4>Manage coupons</h4>
					</div>
					<div class="panel-body">
						<form action="" method="post" id="main-form">
							<div class="row">
								<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
									<div class="form-group">
										<label for="userName">Coupon code <span class="text-danger">*</span></label>
										<input type="text" class="form-control" name="code" value="<?= isset($id)?$edit['ccode']:old('ccode')?>" onBlur="checkcoupon()" id="coup" placeholder="Coupon Code" required>
										<span style="color:red" id="coupon-status"></span>
										
									</div>
									
								</div>
								
								<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
									<div class="form-group">
										<label for="userName">Discount <span class="text-danger">*</span></label>
										<input type="number" step="0.01" class="form-control" name="discount" value="<?= isset($id)?$edit['discount']:old('discount')?>" id="userName" placeholder="60" min="0" required>
									</div>
								</div>
								<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
									<div class="form-group">
										<label for="userName">Discount type</label>
										<select name="dis_type" class="form-control">
											<option value="0" <?= isset($id)?(($edit['discount_type']==0)?'Selected':''):'Selected'?>>%</option>
											<option value="1" <?= isset($id)?(($edit['discount_type']==1)?'Selected':''):''?>>FIXED AMOUNT</option>
										</select>
									</div>
								</div>
								<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
									<div class="form-group">
										<label for="userName">Minimum billing amount <span class="text-danger">*</span></label>
										<input type="number" step="0.01" class="form-control" name="min" value="<?= isset($id)?$edit['min_amount']:old('min_amount')?>" id="userName" placeholder="9800.00" min="0" required>
									</div>
								</div>
								<div class="clearfix"></div>
								<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
									<div class="form-group">
										<label for="userName">Maximum discount amount <span class="text-danger">*</span></label>
										<input type="number" step="0.01" class="form-control" name="max" value="<?= isset($id)?$edit['max_amount']:old('max_amount')?>" id="userName" placeholder="9800.00" min="0" required>
									</div>
								</div>
								<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
									<div class="form-group">
										<label for="userName">Coupons per user <span class="text-danger">*</span></label>
										<input type="text" step="0.01" class="form-control" name="c_per_user" value="<?= isset($id)?$edit['c_per_user']:old('c_per_user')?>" id="userName" placeholder="1" required>
									</div>
								</div>
								
								<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
									<div class="form-group">
										<label for="userName">Valid till <span class="text-danger">*</span></label>
										<input type="text" class="form-control date" value="<?=isset($edit)?$edit['valid']:''?>" name="valid" required readonly>
									</div>
								</div>
								
								<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
									<div class="form-group">
										<label for="userName">Reward points <span class="text-danger">*</span></label>
										<input type="text" class="form-control" value="<?= isset($id)?$edit['reward_points']:old('reward_points')?>" name="reward_points" placeholder="" required>
									</div>
								</div>
								
								
								
								<div class="col-lg-12">
									<?php if(isset($id)){ ?>
										<input type="hidden" name="eid" value="<?=$id?>">
										<button type="submit" name="edit-submit" class="btn btn-info pull-right"><i class="fa fa-pencil-square-o" aria-hidden="true"></i>Update coupon</button>
										<?php }else{ ?>
										<button type="submit" name="submit" class="btn btn-success pull-right"><i class="fa fa-plus" aria-hidden="true"></i>Create coupon</button>
									<?php } ?>
								</div>
							</div>
						</form>
					</div>
				</div>
			</div>
					
						<hr>
						
						<div class="clearfix"></div>
					
							<div class="col-lg-12">
							    
							    <div class="panel">
					<div class="panel-heading">
						<h4>Edit/ manage coupons</h4>
					</div>
					<div class="panel-body">
					    
								<div class="table-responsive">
									<table class="table table-bordered grid no-margin table_datatable">
										<thead>
											<tr>
												<th>Coupon Code</th>
												<th>Discount</th>
												<th>Minimun Discount Amt.</th>
												<th>Maximum Discount Amt.</th>
												<th>Valid Till</th>
												<th>Coupons per user</th>
												<th>Action</th>
											</tr>
										</thead>
										<tbody>
											<?php
												$sql="SELECT * from coupons where active=0 and branch_id='".$branch_id."' order by id desc";
												$result=query_by_id($sql,[],$conn);
												if($result){
													foreach ($result as $row) {
													?>
													<tr>
														<td><?=$row['ccode']; ?></td>
														<td><?=($row['discount']) .' '.(($row['discount_type']==0)?'%':'FIXED AMOUNT'); ?></td>
														<td><?= number_format($row['min_amount'],2); ?></td>
														<td><?= number_format($row['max_amount'],2); ?></td>
														<td><?=my_date_format($row['valid']); ?></td>
														<td><?=$row['c_per_user']?> </td>
														<td><a href="coupons.php?id=<?php echo $row['id']; ?>" ><button class="btn btn-warning btn-xs" type="button"><i class="fa fa-pencil-square-o" aria-hidden="true"></i>Edit</button></a>
														<?php if(DELETE_BUTTON_INACTIVE == 'true'){ ?>
														    <a href="#" onclick="return deleteDisabled();"><button class="btn btn-danger btn-xs" type="button"><i class="icon-delete"></i>Delete</button></a></td>
														<?php } else { ?>
														    <a href="coupons.php?d=<?php echo $row['id']; ?>" onclick="return confirm('Are you sure?');"><button class="btn btn-danger btn-xs" type="button"><i class="icon-delete"></i>Delete</button></a></td>
													    <?php } ?>
													</tr>
												<?php } } ?>
										</tbody>
									</table>
								</div>
							</div>
							
							</div>
							
							
							</div>
					
					</div>
					
				</div>
			</div>
		</div>
		<!-- Row ends -->
		
	</div>
	<!-- Main container ends -->
	
</div>
<!-- Dashboard Wrapper End -->

</div>
<!-- Container fluid ends -->
<script>
	function checkcoupon() {
		var cat = $('#coup').val().replace(/\s/g,'');
		jQuery.ajax({
			url: "ajax/checkcoupon.php",
			data:{'coup' : cat},
			type: "POST",
			success:function(data){
				if(data === "1"){
					$("#coupon-status").html('Coupon already Exists');
					$('#coup').val("");
				}},
				error:function (){}
		});
	}
</script>
<?php 
	include "footer.php";
?>
