<?php
	include "./includes/db_include.php";
	$branch_id = $_SESSION['branch_id'];
	if(isset($_GET['pid'])){
		$pid = $_GET['pid'];
		if($pid == 0 || $pid == ''){
			header('LOCATION:beauticians.php');
			exit();
		}		
		$dat = "";	
		if(!isset($_SESSION['dt'])){
			$_SESSION['dt']  = "";
			}else{
			$dat = $_SESSION['dt'];
		}
		
		if(isset($_GET['payid'])){
		$payid = $_GET['payid'];
		$edit_payroll_slip = query_by_id("SELECT ser_commission,prod_commission,startdate,enddate,bonus,deduction,total,subtotal,paid,notes,paymode,details from payroll where id='$payid' and active=0 and type='2' and branch_id='".$branch_id."'",[],$conn)[0];
		}
		
		
		if(isset($_POST['submit'])){
			
			$start = $_POST['start'];
			$end   = $_POST['end'];
			$scom  = $_POST['scomm'];
			$pcom  = $_POST['pcomm'];
			$tot   = $_POST['tot'];
			$pay   = $_POST['pay'];
			$notes = $_POST['notes'];
			$modeofpay = $_POST['modeofpay'];
			$detail = $_POST['detail'];
			
			query("INSERT INTO `payroll`(`eid`,`total`,`startdate`,`enddate`,`notes`,`paid`,`paymode`,`details`,`ser_commission`,`prod_commission`, `type`, `active`,`branch_id`) VALUES ('$pid','$tot','$start','$end','$notes','$pay','$modeofpay','$detail','$scom','$pcom',2,0,'$branch_id')",[],$conn);
			
			$_SESSION['t']  = 1;
			$_SESSION['tmsg']  = "Commission Slip Generated Successfully";
			echo '<meta http-equiv="refresh" content="0; url=commision.php?pid='.$pid.'" />';die();
		}
			if(isset($_POST['update_payroll'])){
			$payid = $_GET['payid'];
			$start = $_POST['start'];
			$end   = $_POST['end'];
			$scom  = $_POST['scomm'];
			$pcom  = $_POST['pcomm'];
			$tot   = $_POST['tot'];
			$pay   = $_POST['pay'];
			$notes = $_POST['notes'];
			$modeofpay = $_POST['modeofpay'];
			$detail = $_POST['detail'];
			
			query("UPDATE  `payroll` set `eid`='$pid',`total`='$tot',`startdate`='$start',`enddate`='$end',`notes`='$notes',`paid`='$pay',`paymode`='$modeofpay',`details`='$detail',`ser_commission`='$scom',`prod_commission`='$pcom', `type`='2', `active`='0' where id='$payid' and active='0' and type='2' and branch_id='".$branch_id."'",[],$conn);
			
			$_SESSION['t']  = 1;
			$_SESSION['tmsg']  = "Commission slip Updated Successfully";
			echo '<meta http-equiv="refresh" content="0; url=commision.php?pid='.$pid.'" />';die();
		}
		
		if(isset($_POST['filter'])){
			$st = $_POST['fstart'];
			echo $st;
			$te = $_POST['tend'];
			$dat = "and i.doa BETWEEN '".$st."' AND '".$te."'";
			$_SESSION['dt'] = $dat;
			echo '<meta http-equiv="refresh" content="0; url=commision.php?pid='.$pid.'" />';die();
		}
		
		
		if(isset($_GET['del'])){
			
			$del = $_GET['del'];
			query("UPDATE `payroll` SET `active`=1 WHERE id='$del' and type='2' and active='0' and branch_id='".$branch_id."'" ,[],$conn);
			$_SESSION['t']  = 1;
			$_SESSION['tmsg']  = "Commission slip Removed Successfully";
			echo '<meta http-equiv="refresh" content="0; url=commision.php?pid='.$pid.'" />';die();
		}	
		
		include "topbar.php";
		include "header.php";
		include "menu.php";
	}
	
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
						<h4> Commissions</h4>
					</div>
					<div class="panel-body">
						<div class="row">
							<form id="formcat" action="" method="post">
								<div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
									<div class="form-group">
										<label for="date">Select dates</label>
										<input type="text" class="form-control" range-attr="daterange" id="daterange" name="date"  placeholder="01/01/1990 - 12/05/2000" required readonly>		
									</div>
								</div>
								<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
									<div class="form-group">
										<label for="service_type">Service type</label>
										<select name="service_type" id="service_type" class="form-control">
											<option value="">--All--</option>
										</select>
									</div>
								</div>
								<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
									<div class="form-group">
										<label for="commission_for">Commission for</label>
										<select name="commission_for" id="commission_for" class="form-control">
											<option value="">--All--</option>
											<option value="Service">Service</option>
											<option value="Product">Product</option>
										</select>
									</div>
								</div>
								<div class="col-lg-1 col-md-1 col-sm-1 col-xs-12">
									<div class="form-group">
										<label>&nbsp;</label>
										<input type="hidden" name="pro_id" id="pro_id" value="<?= $pid; ?>">	
										<button type="button" id="filter" onclick="commissionFilter();" name="filter" class="btn btn-filter btn-block"><i class="fa fa-filter" aria-hidden="true"></i>Filter</button>
									</div>
								</div>
								<div class="col-lg-1 col-md-1 col-sm-1 col-xs-12">
									<div class="form-group">
										<label>&nbsp;</label>	
										<button type="reset" onclick="allcommissionAjax(<?php echo $pid; ?>)" class="btn btn-danger btn-block"><i class="fa fa-times" aria-hidden="true"></i>Clear</button>
									</div>
								</div>
							</form>
						</div>
						<div class="clearfix"></div><br>
						<div class="row">
							<div class="col-lg-12">
								<div class="table-responsive">
									<table id="grid" class="table table-bordered no-margin" >
										<thead>
											<tr>
												<th>Date</th>
												<th>Price</th>
												<th>Service/Product</th>
												<!--<th>Quantity</th>-->
												<th>Commission</th>
												<th class="text-center">Bill id</th>
											</tr>
										</thead>
										<tbody id="filterData">
											
										</tbody>
									</table>
								</div>
								
							</div>
						</div>
					</div>
				</div>
			</div>
			
			
			<!-- <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
				<div class="panel">
					<div class="panel-heading">
						<h4>Generate commission slip</h4>
					</div>
					<div class="panel-body">
						<div class="row">
							<form action="" method="post">
								<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
									<div class="form-group">
										<?php $date = date('Y-m-d'); ?>
										<label for="userName">Start Date :</label>
										<input type="text" class="form-control date dt sum" id="start" name="start" value="<?=($edit_payroll_slip['startdate'])?$edit_payroll_slip['startdate']:$date?>" placeholder="Start Date" required readonly>
									</div>
								</div>
								<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
									<div class="form-group">
										<label for="userName">End Date :</label>
										<input type="text" class="form-control date dt sum" id="end" name="end" value="<?=($edit_payroll_slip['enddate'])?$edit_payroll_slip['enddate']:$date?>" placeholder="End Date" required readonly>
									</div>
								</div>
								
								<div class="clearfix"></div>
								<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
									<div class="form-group">
										<label for="userName">Total Service Commission :</label>
										<input type="number" class="form-control" name="scomm" id="scom" placeholder="0" readonly value="<?=($edit_payroll_slip['ser_commission'])?$edit_payroll_slip['ser_commission']:''?>">
										
									</div>
								</div>
								<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
									<div class="form-group">
										<label for="userName">Total Product Commission :</label>
										<input type="number" class="form-control" name="pcomm" id="pcom" placeholder="0" readonly value="<?=($edit_payroll_slip['prod_commission'])?$edit_payroll_slip['prod_commission']:''?>">
									</div>
								</div>
								
								<div class="clearfix"></div>
								<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
									<div class="form-group">
										<label for="userName">Total Commission:</label>
										<input type="number" value="<?=($edit_payroll_slip['total'])?$edit_payroll_slip['total']:$val?>" class="form-control" name="tot" id="tot" placeholder="Payment">
									</div>
								</div>
								<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
									<div class="form-group">
										<label for="userName">Payment :</label>
										<input type="number" value="<?=($edit_payroll_slip['paid'])?$edit_payroll_slip['paid']:'0'?>" class="form-control sum" name="pay" id="pay" placeholder="Payment">
									</div>
								</div>
								<div class="clearfix"></div>
								<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
									<div class="form-group">
										<label for="userName">Pending :</label>
										<input type="number" class="form-control sum" name="pend" id="pend" placeholder="Payment">
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
										<input type="text" class="form-control sum" name="notes" id="userName" placeholder="Description Any">
									</div>
								</div>
								<div class="clearfix"></div>
								<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
									<div class="form-group">
										<?php if(isset($_GET['payid'])){ ?>
												<button type="submit" name="update_payroll" class="btn btn-info pull-right">Update PaySlip</button>
											<?php }else{ ?>
										<button type="submit" name="submit" class="btn btn-info pull-right">Generate commission slip</button>
										<?php } ?>
									</div>
								</div>
							</form>
						</div>
						
						<div class="clearfix"></div><br>
						
						<div class="row">
							<div class="col-lg-12">
								<div class="table-responsive">
									<table id="table" class="table table-bordered no-margin">
										<thead>
											<tr>
												<th>Start-End Date</th>
												<th>Service Comm</th>
												<th>Product Comm</th>
												<th>Total</th>
												<th>Paid</th>
												<th>Manage</th>
											</tr>
										</thead>
										<tbody>
											<?php
												$sql1="SELECT * from payroll where eid=$pid and active=0 and type=2 and branch_id='".$branch_id."' order by enddate desc";
												$result1=query($sql1,[],$conn);
												foreach($result1 as $row1) {
												?>
												<tr>
													<td><?php echo $row1['startdate']; ?>-<?php echo $row1['enddate']; ?></td>
													<td><?php echo $row1['ser_commission']; ?></td>
													<td><?php echo $row1['prod_commission']; ?></td>
													<td><?php echo $row1['total']; ?></td>
													<td><?php echo $row1['paid']; ?></td>
													<td><a href="commision.php?pid=<?php echo $pid; ?>&payid=<?php echo $row1['id']; ?>"><button class="btn btn-info btn-xs" type="button">Edit</button></a> <a href="commision.php?del=<?php echo $row1['id']; ?>&pid=<?php echo $pid; ?>" onclick="return confirm('Are you sure?');"><button class="btn btn-danger btn-xs" type="button">Delete</button></a></td>
												</tr>
											<?php } ?>
										</tbody>
									</table>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div> -->
			<div id="rrr"></div>
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
		servicetypelist();
		$(".sum").keyup(function(){		
			sumup();		
		});
	});
</script>
<script>
	$(document).ready(function(){
		$(".dt").on('change click ',function(){
			sumup();
		});

		// calling function to load all commission of service provider

		allcommissionAjax(<?php echo $pid; ?>);

	});
	$(window).on('load', function(){
		
		sumup();
		});
	function sumup(){
		
		var sd = $("#start").val();
		var ed = $("#end").val();
		var id = <?php echo $pid; ?>;
		jQuery.ajax({
			url: "ajax/getcomm.php?s="+sd+"&e="+ed+"&i="+id,
			
			type: "POST",
			success:function(data){
				var ds = JSON.parse(data.trim());
				$('#scom').val(ds['service']);
				$('#pcom').val(ds['product']);
				var scom = $('#scom').val();
				var pcom = $('#pcom').val();
				var tot = parseInt(scom) + parseInt(pcom);
				$('#tot').val(tot);
				var pay = $('#pay').val();
				pay = pay || 0;
				var pend = tot - parseInt(pay);
				$('#pend').val(pend);
				
			},
			error:function (){}
		});
	}


	// function to get list of services provided by single service provider

	function servicetypelist(){		
		var id = <?php echo $pid; ?>;
		jQuery.ajax({
			url: "ajax/checkservice.php",
			data: {'sp_id':id},
			dataType : 'json',
			type: "POST",
			success:function(data){
				$.each(data,function(i){
					$('#service_type').append('<option value="sr,'+data[i].id+'">'+data[i].name+'</option>');
				});	
			},
			error:function (){}
		});
	}

	// function to filter services provider commission based on start date, enddate, servicec type and  commission type.

	function commissionFilter(){
		var daterange = $('#daterange').val();
		var date = daterange.split("-");
		if(daterange == ''){
			var startdate = '';
			var enddate = '';
		} else {
			var startdate = isoDate(date[0]);
			var enddate = isoDate(date[1]);
		}
		var pid = $('#pro_id').val();
		var service_type = $('#service_type').val();
		var commission_for = $('#commission_for').val();
		commissionAjax(startdate, enddate, service_type, commission_for, pid);
	}

	// ajax method for commission query

	function commissionAjax(startdate, enddate, service_type = '', commission_for = '', provider_id){
		jQuery.ajax({
			url: "ajax/commission_filter.php",
			data: {'startdate':startdate, 'enddate': enddate, 'service_type': service_type, 'commission_for' : commission_for, 'pid' : provider_id, 'filter_type':'commission'},
			type: "POST",
			beforeSend: function() {
            	$('#filter i').removeClass('fa-filter');
            	$('#filter i').addClass('fa-spinner fa-spin');
            	$('#filter').prop('disabled',true);
			},
			success:function(data){
				$('#filter i').addClass('fa-filter');
            	$('#filter i').removeClass('fa-spinner fa-spin');
            	$('#filter').prop('disabled',false);
				$('#filterData').html(data);
			},
			error:function (){}
		});
	}

	// function to load all commission data of service provider

	function allcommissionAjax(provider_id){
		jQuery.ajax({
			url: "ajax/commission_filter.php",
			data: {'pid' : provider_id, 'filter_type':'allcommission'},
			type: "POST",
			success:function(data){
				$('#filterData').html(data);
			},
			error:function (){}
		});
	}


  	// jquery function to change daterange date in iso date standard  Y-m-d (2019-11-01)

	function isoDate(date){	
		var datespit = date.split('/');
		var day = datespit[1].replace(' ','');
		var month = datespit[0].replace(' ','');
		var year = datespit[2].replace(' ','');
		return year+'-'+month+'-'+day;
	}


</script>

<?php 
	include "footer.php"; 
	
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
	
	
	function getservicecom($price,$bid){
		$sum = 0;
		$com = getcoms($bid);
		$val = $price * $com / 100;
		$sum = $sum + $val;
		return $sum;
	}
	
	function getprodcom($price,$bid){
		$sum = 0;
		$com = getcomp($bid);
		$val = $price * $com / 100;
		$sum = $sum + $val;
		return $sum;
	}
	
	function getcoms($bid){
		global $conn;
		global $branch_id;
		$sum = 0;
		$sql="SELECT * from beauticians where id='$bid' and active='0' and branch_id='".$branch_id."'";
		$result=query_by_id($sql,[],$conn);
		foreach($result as $row) {
			return $row['serivce_commision'];
		}
	}
	
	function getcomp($bid){
		global $conn;
		global $branch_id;
		$sum = 0;
		$sql="SELECT * from beauticians where id='$bid' and active='0' and branch_id='".$branch_id."'";
		$result=query_by_id($sql,[],$conn);
		foreach($result as $row) {
			return $row['prod_commision'];
		}
	}
	
	function getproduct($bid){
		global $conn;
		global $branch_id;
		$sum = 0;
		$sql="SELECT * from products where id=$bid and branch_id='".$branch_id."'";
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
			$sql ="SELECT CONCAT('(Service)',' ',name) as name FROM `service` where active='0' and id='$id' and branch_id='".$branch_id."'";	
		}else if(EXPLODE(",",$get_id)[0] == 'pr'){
			$sql ="SELECT CONCAT('(Product)',' ',name) as name FROM `products` where active='0' and id='$id'";	
		}else if(EXPLODE(",",$get_id)[0] == 'pa'){
			$sql ="SELECT CONCAT('(Package)',' ',name) as name FROM `packages` where active='0' and id='$id' and branch_id='".$branch_id."'";	
		}else if(EXPLODE(",",$get_id)[0] == 'prepaid'){
			$sql ="SELECT CONCAT('(Prepaid)',' ',pack_name) as name FROM `prepaid` where status='1' and id='$id' and branch_id='".$branch_id."'";	
		}else if(EXPLODE(",",$get_id)[0] == 'mem'){
			$sql ="SELECT CONCAT('(Membership)',' ',membership_name) as name FROM `membership_discount` where status='1' and id='$id'";
		}
	    $result=query_by_id($sql,[],$conn);
	    foreach($result as $row) {
	    	return $row['name'];
	    }
	}
	
	?>	