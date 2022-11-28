<?php
	include "./includes/db_include.php";
	$branch_id = $_SESSION['branch_id'];
	if(isset($_GET['sid'])){
		$pid = $_GET['pid'];
		$sid = $_GET['sid'];
		$edit_manage_salary=query_by_id("SELECT type,title,salary FROM `salary` where `id`='$sid' and `eid`='$pid' and active=0 and branch_id='".$branch_id."'",[],$conn)[0];
		
	}
		
	if(isset($_GET['payid'])){
		$payid = $_GET['payid'];
		$edit_payroll_slip = query_by_id("SELECT startdate,enddate,bonus,deduction,total,subtotal,paid,notes,paymode,details from payroll where id=$payid and active=0 and branch_id='".$branch_id."'",[],$conn)[0];
	}
	
	if(isset($_GET['pid'])){
		$pid = $_GET['pid'];
		
		if(isset($_POST['submit'])){
			$start 	= $_POST['start'];
			$end 	= $_POST['end'];
			$bonus  = $_POST['bonus'];
			$ded    = $_POST['ded'];
			$total  = $_POST['total'];
			$subtotal  = $_POST['subtotal'];
			$notes     = $_POST['notes'];
			$pay       = $_POST['pay'];
			$modeofpay = $_POST['modeofpay'];
			$detail    = $_POST['detail'];
			
			query("INSERT INTO `payroll`(`eid`,`total`,`bonus`,`deduction`,`subtotal`,`startdate`,`enddate`,`paid`,`notes`,`paymode`, `details`,`type`,`active`,`branch_id`) VALUES ('$pid','$total','$bonus','$ded','$subtotal','$start','$end','$pay','$notes','$modeofpay','$detail',1,0,'$branch_id')",[],$conn);
			
			$_SESSION['t']  = 1;
			$_SESSION['tmsg']  = "Payroll Slip Generated Successfully";
			echo '<meta http-equiv="refresh" content="0; url=payroll.php?pid='.$pid.'" />';die();
		}
		
		if(isset($_POST['salary'])){
			$title = $_POST['title'];
			$title = ucfirst($title);
			$amount = $_POST['amount'];
			$type = $_POST['type'];
			query("INSERT INTO `salary`(`eid`,`salary`,`title`,`type`,`active`,`branch_id`) VALUES ('$pid','$amount','$title','$type',0,'$branch_id')",[],$conn);
			$_SESSION['t']  = 1;
			$_SESSION['tmsg']  = "Salary Added Successfully";
			echo '<meta http-equiv="refresh" content="0; url=payroll.php?pid='.$pid.'" />';die();
		}
		if(isset($_GET['del'])){
			$del=$_GET['del'];
			query("UPDATE `salary` SET `active`=1 WHERE id=$del and branch_id='".$branch_id."'",[],$conn);
			$_SESSION['t']  = 1;
			$_SESSION['tmsg']  = "Bill Removed Successfully";
			echo '<meta http-equiv="refresh" content="0; url=payroll.php?pid='.$pid.'" />';die();
		}
		
		if(isset($_GET['de'])){
			
			$del = $_GET['de'];
			query("UPDATE `payroll` SET `active`=1 WHERE id=$del and branch_id='".$branch_id."'",[],$conn);
			$_SESSION['t']  = 1;
			$_SESSION['tmsg']  = "Bill Removed Successfully";
			echo '<meta http-equiv="refresh" content="0; url=payroll.php?pid='.$pid.'" />';die();
		}	
		
		
		if(isset($_POST['update_payroll'])){
			$payid = $_GET['payid'];
			$start = $_POST['start'];
			$end   = $_POST['end'];
			$bonus = $_POST['bonus'];
			$ded   = $_POST['ded'];
			$total = $_POST['total'];
			$subtotal = $_POST['subtotal'];
			$notes = $_POST['notes'];
			$pay   = $_POST['pay'];
			$modeofpay = $_POST['modeofpay'];
			$detail = $_POST['detail'];
			
			query("UPDATE `payroll` SET `eid`='$pid',`total`='$total',`bonus`='$bonus', `deduction`='$ded',`subtotal`='$subtotal',`startdate`='$start',`enddate`='$end',`notes`='$notes',`paid`='$pay',`paymode`='$modeofpay'  WHERE id=$payid and active='0' and branch_id='".$branch_id."'",[],$conn);
			
			$_SESSION['t']  = 1;
			$_SESSION['tmsg']  = "Payroll Updated Successfully";
			echo '<meta http-equiv="refresh" content="0; url=payroll.php?pid='.$pid.'" />';die();
		}
		
		if(isset($_POST['update_salary'])){
			$title = $_POST['title'];
			$title = ucfirst($title);
			$amount = $_POST['amount'];
			$type = $_POST['type'];
			query("UPDATE `salary` SET `eid`=$pid,`salary`=$amount,`title`='$title',`type`='$type',`active`=0 WHERE id=$sid and branch_id='".$branch_id."'",[],$conn);
			
			$_SESSION['t']  = 1;
			$_SESSION['tmsg']  = "Salary Updated Successfully";
			echo '<meta http-equiv="refresh" content="0; url=payroll.php?pid='.$pid.'" />';die();
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
				
				<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
					<div class="panel">
						<div class="panel-heading">
							<h4>Manage Salary</h4>
						</div>
						<div class="panel-body">
							<div class="row">
								<form id="formcat" action="" method="post">
									<!--<form id="formcat">-->
									<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
										<div class="form-group">
											<label for="userName">Enter Title</label>
											<input type="text" class="form-control" name="title" id="cat" placeholder="Salary Title" value="<?=($edit_manage_salary['title'])?$edit_manage_salary['title']:''?>" required>
										</div>
									</div>
									<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
										<div class="form-group">
											<label for="userName">Enter Amount</label>
											<input type="number" class="form-control" name="amount" placeholder="Amount" value="<?=($edit_manage_salary['salary'])?$edit_manage_salary['salary']:''?>" required>
											<span id="cat-status"></span>
										</div>
									</div>
									
									<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
										<div class="form-group">
											<label for="userName">Salary Type</label>
											<select name="type" class="form-control">
												<option <?=($edit_manage_salary['type'] == '1')?'SELECTED':''?> value="1">Earnings</option>
												<option <?=($edit_manage_salary['salary'] == '2')?'SELECTED':''?> value="2">Deduction</option>
											</select>
										</div>
									</div>
									<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
										<div class="form-group">
											<label for="userName">  </label><br>
											<?php if(isset($_GET['sid'])) {?>
												<button type="submit" name="update_salary" class="btn btn-info">Update Salary</button>
												<?php }else{ ?>
												<button type="submit" name="salary" class="btn btn-info">Add Salary</button>
											<?php } ?>
										</div>
									</div>
								</form>
							</div>
							
							<div class="row">
								<div class="col-lg-12">
									<div class="table-responsive">
										<table id="grid" class="table table-bordered no-margin">
											<thead>
												<tr>
													<th>Title</th>
													<th>Amount</th>
													<th>Type</th>
													<th>Action</th>
												</tr>
											</thead>
											<tbody>
												<?php
													$sql="SELECT * from salary where eid=$pid and active=0 and branch_id='".$branch_id."' order by title asc";
													$result=query_by_id($sql,[],$conn);
													foreach($result as $row) {
													?>
													<tr>
														<td><?php echo $row['title']; ?></td>
														<td><?php echo $row['salary']; ?></td>
														<?php 
															$type = $row['type'];
															$str = "";
															if($type==1)
															$str = "Earnings";
															else
															$str = "Deduction";
														?>
														<td><?php echo $str; ?></td>
														<td><a href="payroll.php?pid=<?php echo $pid; ?>&sid=<?php echo $row['id']; ?>"><button class="btn btn-info btn-xs" type="button">Edit</button></a> <a href="payroll.php?del=<?php echo $row['id']; ?>&pid=<?php echo $pid; ?>" onclick="return confirm('Are you sure?');"><button class="btn btn-danger btn-xs" type="button">Delete</button></a></td>
													</tr>
												<?php } ?>
											</tbody>
										</table>
									</div>
									
								</div>
							</div>
						</div>
					</div>
				</div>
				
				
				<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
					<div class="panel">
						<div class="panel-heading">
							<h4>Generate payroll slip</h4>
						</div>
						<div class="panel-body">
							<div class="row">
								<form action="" method="post">
									<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
										<div class="form-group">
											<label for="userName">Start Date</label>
											<input type="text" class="form-control date" name="start" value="<?=($edit_payroll_slip['startdate'])?$edit_payroll_slip['startdate']:''?>"  placeholder="service name" required readonly>
											
										</div>
									</div>
									<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
										<div class="form-group">
											<label for="userName">End Date</label>
											<input type="text" class="form-control date" name="end"  value="<?=($edit_payroll_slip['enddate'])?$edit_payroll_slip['enddate']:''?>" placeholder="service name" required readonly>
											
										</div>
									</div>
									<div class="clearfix"></div>
									<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
										<div class="form-group">
											<label for="userName">Bonus</label>
											<input type="number" class="form-control sum" name="bonus"  value="<?=($edit_payroll_slip['bonus'])?$edit_payroll_slip['bonus']:''?>" id="bon" placeholder="0" >
										</div>
									</div>
									<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
										<div class="form-group">
											<label for="userName">Deduction(Any) :</label>
											<input type="number" class="form-control sum" name="ded" value="<?=($edit_payroll_slip['deduction'])?$edit_payroll_slip['deduction']:''?>" id="ded" placeholder="0">
										</div>
									</div>
									<div class="clearfix"></div>
									<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
										<div class="form-group">
											<?php $val = getsum($pid); ?>
											<label for="userName">Total :</label>
											<input type="number" value="<?=($edit_payroll_slip['total'])?$edit_payroll_slip['total']:$val?>" class="form-control" name="total" id="tot" placeholder="0" readonly>
										</div>
									</div>
									<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
										<div class="form-group">
											<label for="userName">Sub-Total :</label>
											<input type="number" value="<?=($edit_payroll_slip['subtotal'])?$edit_payroll_slip['subtotal']:$val?>" class="form-control" name="subtotal" id="sub" placeholder="0" readonly>
										</div>
									</div>
									<div class="clearfix"></div>
									<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
										<div class="form-group">
											<label for="userName">Payment :</label>
											<input type="number" class="form-control" name="pay" value="<?=($edit_payroll_slip['paid'])?$edit_payroll_slip['paid']:''?>" id="userName" placeholder="Payment">
										</div>
									</div>
									
									
									<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
										<div class="form-group">
											<label for="userName">Payment Mode :</label>
											<select class="form-control act" name="modeofpay">
												<option <?=($edit_payroll_slip['paymode'] == 'Cash')?'SELECTED':'Cash'?> >Cash</option>
												<option <?=($edit_payroll_slip['paymode'] == 'Mobile Wallet')?'SELECTED':'Mobile Wallet'?> >Mobile Wallet</option>
												<option <?=($edit_payroll_slip['paymode'] == 'Credit/Debit Card')?'SELECTED':'Credit/Debit Card'?> >Credit/Debit Card</option>
												<option <?=($edit_payroll_slip['paymode'] == 'Cheque')?'SELECTED':'Cheque'?> >Cheque</option>
												<option <?=($edit_payroll_slip['paymode'] == 'Online payment')?'SELECTED':'Online payment'?> >Online payment</option>
											</select>
											<input type="text" class="form-control" style="margin-top : 5px;" name="detail" id="detail" value="<?=($edit_payroll_slip['details'])?$edit_payroll_slip['details']:''?>" placeholder="Enter Card 4 digits or Cheque No.">
										</div>
									</div>
									
									<script type='text/javascript'>//<![CDATA[
										$(document).on("change", '.act', function() {
											var str = $('.act').val();
											if(str=="Credit/Debit Card" || str=="Cheque")
											$('#detail').show();
											else
											$('#detail').hide();
										});
										$( document ).ready(function() {
											$('#detail').hide();
											//$('#detail').val('');
										});
										
									</script>
									<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
										<div class="form-group">
											<label for="userName">Notes :</label>
											<input type="text" class="form-control" name="notes" id="userName" placeholder="Description Any">
										</div>
									</div>
									<div class="clearfix"></div>
									<div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
										<div class="form-group">
											<label for="userName">  </label><br>
											<?php if(isset($_GET['payid'])){ ?>
												<button type="submit" name="update_payroll" class="btn btn-info">Update PaySlip</button>
											<?php }else{ ?>
											<button type="submit" name="submit" class="btn btn-info">Generate PaySlip</button>
											<?php } ?>
										</div>
									</div>
								</form>
							</div>
							
							<div class="row">
								<div class="col-lg-12">
									<div class="table-responsive">
										<table id="table" class="table table-bordered no-margin">
											<thead>
												<tr>
													<th>Start-End Date</th>
													<th>Bonus</th>
													<th>Deduction</th>
													<th>Total</th>
													<th>Sub-Total</th>
													<th>Paid</th>
													<th>Manage</th>
												</tr>
											</thead>
											<tbody>
												<?php
													$sql1="SELECT * from payroll where eid=$pid and active=0 and type=1 and branch_id='".$branch_id."' order by enddate asc";
													$result1=query_by_id($sql1,[],$conn);
													foreach($result1 as $row1) {
													?>
													<tr>
														<td><?php echo $row1['startdate']; ?>-<?php echo $row1['enddate']; ?></td>
														<td><?php echo $row1['bonus']; ?></td>
														<td><?php echo $row1['deduction']; ?></td>
														<td><?php echo $row1['total']; ?></td>
														<td><?php echo $row1['subtotal']; ?></td>
														<td><?php echo $row1['paid']; ?></td>
														<td><a href="payroll.php?pid=<?php echo $pid; ?>&payid=<?php echo $row1['id'] ?>"><button class="btn btn-info btn-xs" type="button">Edit</button></a> <a href="payroll.php?de=<?php echo $row1['id']; ?>&pid=<?php echo $pid; ?>" onclick="return confirm('Are you sure?');"><button class="btn btn-danger btn-xs" type="button">Delete</button></a></td>
													</tr>
												<?php } ?>
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
			
		</div>
		<!-- Main container ends -->
		
	</div>
	<!-- Dashboard Wrapper End -->
	
</div>
<!-- Container fluid ends -->

<script>
	$(document).ready(function(){
		$(".sum").keyup(function(){
			
			var total =$('#tot').val(); 
			var sub = $('#sub').val(); 
			var bon = $('#bon').val(); 
			var ded = $('#ded').val(); 
			bon = bon || 0 ;
			ded = ded || 0 ;
			var sum = parseInt(total) + parseInt(bon) - parseInt(ded);
			$('#sub').val(sum);
			
		});
	});
</script>


<?php 
	include "footer.php";
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
	$sql2="SELECT sum(salary) as sum from salary where eid='$pid' and type=1 and active='0' and branch_id='".$branch_id."'";
	$result2=query_by_id($sql2,[],$conn)[0];
	if($result2) {
		return $result2['sum'];
	}
}

function getdeductions($cid) {
	global $conn;
	global $branch_id;
	$sql2="SELECT sum(salary) as sum from salary where eid='$cid' and type=2 and active='0' and branch_id='".$branch_id."'";
	$result2=query_by_id($sql2,[],$conn)[0];
	if($result2) {
		return $result2['sum'];
	}
}

?>