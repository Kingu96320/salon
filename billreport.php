<?php 
	include_once './includes/db_include.php';
	$branch_id = $_SESSION['branch_id'];
	//$uid = $_SESSION['uid'];
	$submit = "";
	$sdate = "";
	$edate = "";
	$clt = "";
	$dqry = "";
	$prod = "";
	$userquery = "";
	$uid = "";
	$clnt = "";
	$stfid = "";
	
	$start_date = date('m/d/Y');
    $end_date = date('m/d/Y');

	if(isset($_POST['reset'])){
		echo '<meta http-equiv="refresh" content="0; url=billreport.php" />';die();
	}
	
	if(isset($_POST['submit'])){
		if($_POST['products']!=""){
			$prod = $_POST['pid'];
			$prr = explode(',',$prod);
			//echo $prr[0]." ".$prr[1];
			//echo checkproduct($prr[0],$prr[1]);
		}
		
		if($_POST['date']!=""){
    	    $date = $_POST['date'];
    	    $date = explode("-",$date);
    	    $start_date = $date[0];
    	    $end_date = $date[1];
    	    $sdate = isoDate($date[0]);
    	    $edate = isoDate($date[1]);
    		$dqry = "and i.doa BETWEEN '$sdate' AND '$edate'";
    	}
	
		if($_POST['client']!=""){
			$clnt = $_POST['clientid'];
			$clt = "and i.client=".$_POST['clientid']." ";
		}
		if($_POST['userid']!=""){
			$uid = $_POST['userid'];
			$userquery = "and i.uid=".$_POST['userid']." ";
		}
		if($_POST['staffid']!=""){
			$sp_id = " and imsp.service_provider=".$_POST['staffid']." and imsp.branch_id='".$branch_id."'";
			$sp_query = "LEFT JOIN `invoice_multi_service_provider` imsp on imsp.inv=i.id";
		}
		if($_POST['serviceid']!=""){
			$serid = $_POST['serviceid'];
			$ser_id = " and ii.service='$serid'";
			$ser_query = "LEFT JOIN `invoice_items_".$branch_id."` ii on ii.iid=i.id";
		}
		
		$submit = $clt.$userquery.$dqry.$sp_id.$ser_id ;
	}
	
	include "topbar.php";
	include "header.php";
	include "menu.php";
?>
<script type='text/javascript'>//<![CDATA[
	$(document).on("keyup blur change", '.ser', function() {
		
		var ser = $(".ser").val();
		$.ajax({
			url: "getlist.php",
			type: "POST",
			data: {'ser' : ser},
			success:function(data){
				var ds = JSON.parse(data.trim());
				$('.serr').val(ds[0]['id']);
			},
			error:function (){}
		});
		
	});
</script>

<script>

	function checkcont() {
		var cat = $('#cont').val();
		jQuery.ajax({
			url: "checkenq.php?con="+$("#cont").val(),
			//data:'cat='+$("#bcont").val(),
			type: "POST",
			success:function(data){
				$("#cont-status").html(data);
				//alert(data);
				if ( data.indexOf("Already Exist") > -1 ) {
					$('#cont').val("");
				}
			},
			error:function (){}
		});
	}
	$(document).ready(function(){
	    setTimeout(function(){
		$(".staff").autocomplete({
			source: "autocomplete/beautician.php",
			minLength: 1,	
			//autoFocus: true,
			select: function(event, ui) {
				//event.preventDefault();
				$('#staffid').val(ui.item.id); 
				$('#staff').val(ui.item.cont); 
			}				
		});	
		
		$(".service").autocomplete({
			source: function(request, response) {
				var ser_stime = '';
				if($(this.element).parent().parent().attr('class')=='TextBoxContainer'){
					ser_stime = $('#date').val()+' '+$('#time').val();
					}else{
					ser_stime = $(this.element).parent().parent().prev('tr').find('.ser_etime').val();
				}
				
				$.getJSON("ajax/bill.php", { term: request.term,ser_cat_id: $(this.element).parent().parent().find('.ser_cat_id').val(),ser_stime:ser_stime}, response);
			},
			minLength: 1,
			select:function (event, ui) {  
				var row = $(this).parent().parent();
				row.find('#serviceid').val(ui.item.id);
			}
		});
	    },1000);
	});
</script>

<!-- Dashboard wrapper starts -->
<div class="dashboard-wrapper">
	
	<!-- Main container starts -->
	<div class="main-container">
		
		<!-- Row starts -->
		<div class="row gutter">
			
			<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
				<div class="panel">
					<div class="panel-heading heading-with-btn">
						<h4 class="pull-left">Billing Reports</h4>
    					<span id="download-btn"></span>					
    					<div class="clearfix"></div>
					</div>
					<div class="panel-body">
						<div class="row">
							<form action="" method="POST">
								<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
									<div class="form-group">
										<label class=" control-label">Select date</label>
										<input type="text" class="form-control" range-attr="daterange" id="daterange" name="date" value="<?= $start_date.' - '.$end_date ?>"  placeholder="01/01/1990 - 12/05/2000" required readonly>	
									</div>
								</div>
								<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
									<div class="form-group">
										<?php 
											$staffqry = "";
											if($stfid!=""){
												$staffqry = getstaff($stfid);
											}
										?>
										<label class=" control-label">Service provider</label>
										<?php if($stfid!=0){ ?>
											<input type="text" class="form-control staff" id="staff" value="<?php echo $staffqry; ?>" name="staff" placeholder="Autocomplete(Name & Phone)">
											<?php }else{ ?>
											<input type="text" class="form-control staff" id="staff" name="staff" placeholder="Autocomplete(Service provider name)">
										<?php } ?>
										<input type="hidden" name="staffid" value="<?php echo $stfid; ?>" id="staffid" class="stf"> 
									</div>
								</div>
								<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
									<div >
										<label class=" control-label">Service</label>
										<?php if($stfid!=0){ ?>
											<input type="text" class="form-control service" id="service" value="<?php echo $staffqry; ?>" name="service" placeholder="Autocomplete(Name & Phone)">
											<?php }else{ ?>
											<input type="text" class="form-control service" id="service" name="service" placeholder="Autocomplete(Service name)">
										<?php } ?>
										<input type="hidden" name="serviceid" value="<?php echo $stfid; ?>" id="serviceid" class="stf"> 
									</div>
								</div>
								
								<?php 
									$service = "";
									if($prr[0]=="sr"){
									    $service="Service";
									}
									if($prr[0]=="pr"){
									    $service="Product";
									}
									if($prr[0]=="pa"){
									    $service="Package";
									}
								?>
								
								
								<script type="text/javascript">
									$(function() {
										autocomplete_usr();										
									});
									function autocomplete_usr(){
										$(".user").autocomplete({
											source: "ajax/user.php",
											minLength: 1,	
											//autoFocus: true,
											select: function(event, ui) {
												//event.preventDefault();
												$('#userid').val(ui.item.id); 
												$('#user').val(ui.item.cont); 
											}				
										});	
									}
								</script>
								
								<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
									<div class="form-group">
									    <label>&nbsp;</label><div class="clearfix"></div>
										<button type="submit" name="submit" class="btn btn-filter"><i class="fa fa-filter" aria-hidden="true"></i>Filter</button>
										<button type="submit" name="reset" class="btn btn-danger"><i class="fa fa-times" aria-hidden="true"></i>Clear</button>
									</div>
								</div>
								
							</form>
						</div>
						<div class="row">
							<div class="col-lg-12">
								<div class="panel-body">
									<div class="table-responsive">
									    <?php if(isset($_POST['submit'])) { ?>
									            
									    <table id="table" class="table grid table-bordered no-margin">
											<thead>
												<tr>
													<th style="white-space: nowrap">Date of Bill</th>
													<th>Bill id</th>
													<th>Client</th>
													<th>Contact</th>
													<th>Total</th>
													<th>Paid</th>
													<th>Payment detail</th>
													<th>Pending</th>
													<th>Type</th>
													<th>Products/Services - Service Provider</th>
													<th>Remarks</th>
													<th>User</th>
													<th>Manage</th>
												</tr>
											</thead>
											<tbody>
											<?php		
										        $sql1="SELECT DISTINCT(i.id) as invid, i.*,c.name,c.cont from `invoice_".$branch_id."` i LEFT JOIN `client` c on c.id =i.client ".$sp_query." ".$ser_query." where i.active=0 and i.branch_id='".$branch_id."' ".$submit." order by i.id desc";
													$result1=query_by_id($sql1,[],$conn);
													foreach($result1 as $row1) {
														if($prr[0]!=""){
															$tr = checkproduct($prr[0],$prr[1],$row1['id']);
															if($tr==0)
															continue;
														}
														if($stfid!=""){
															$stfchk = checkstaff($stfid,$row1['id']);
															if($stfchk==0)
															continue;
														}

														$sep_bill = query_by_id("SELECT mpm.amount_paid, pm.name FROM multiple_payment_method mpm LEFT JOIN payment_method pm ON pm.id = mpm.payment_method WHERE mpm.invoice_id='bill,".$row1['id']."' AND mpm.status='1' and mpm.branch_id='".$branch_id."'",[],$conn);
														$sbill = '';
														if($sep_bill){
														    foreach($sep_bill as $bill){
														        $sbill .= $bill['name'].' - '.number_format($bill['amount_paid'],2).'<br />';
														    }
														}
													?>
													<tr>
														<td><?= my_date_format($row1['doa']); ?></td>
														<td><?= $row1['invid'] ?></td>
														<td><?= $row1['name']; ?></td>
														<td><?= $row1['cont']; ?></td>
														<td><?= number_format($row1['total'],2); ?></td>
														<td><?= number_format($row1['paid'],2); ?></td>
														<td><?= $sbill ?></td>
														<td><?= number_format($row1['due'],2); ?></td>
														<td><?php 
															$ty = $row1['invoice'];
															if($ty==1)
															echo "Bill";
															else 
															echo "Converted";
														?></td>
														<td><ul>
															<?php
																$staf = "";
																$sql2="SELECT * from `invoice_multi_service_provider` where inv=".$row1['id']." and status=1 and branch_id='".$branch_id."' order by id desc";
																$result2=query($sql2,[],$conn);
																foreach($result2 as $row2) 
																{
																	if($row2['service_provider']!="0")
																	$staf = " - <u>".getstaff($row2['service_provider'])."</u>";
																	else
																	$staf = "";
																	echo "<li>".getitem($row2['service_name']).$staf."</li>";
																}
															?></ul>
														</td>
														<td><?php echo $row1['notes']; ?></td>
														<td><?php echo get_user($row1['uid']); ?></td>
														<td>
														    <a href="billing.php?beid=<?php echo $row1['id']; ?>" onclick="return confirm('Are you sure?');">
														        <button class="btn btn-warning btn-xs" type="button"><i class="fa fa-pencil-square-o" aria-hidden="true"></i>Edit</button>
														    </a> 
														    <a href="invoice.php?inv=<?php echo $row1['id']; ?>" target="_blank">
														        <button class="btn btn-info btn-xs"  type="button"><i class="fa fa-eye" aria-hidden="true"></i>View</button>
														    </a>
														    <a href="" onclick="invoice_delete(<?php echo $row1['id']; ?>)">
														        <button class="btn btn-danger btn-xs"  type="button"><i class="fa fa-trash" aria-hidden="true"></i>Delete</button>
														    </a>
														</td>
													</tr>
												<?php } ?>
												</tbody>
											</table>
									    <?php } else { ?>
    										<table id="empTable" class="table table-bordered no-margin">
    											<thead>
    												<tr>
    													<th style="white-space: nowrap">Date of Bill</th>
    													<th>Bill id</th>
    													<th>Client</th>
    													<th>Contact</th>
    													<th>Total</th>
    													<th>Paid</th>
    													<th>Payment detail</th>
    													<th>Pending</th>
    													<th>Type</th>
    													<th>Products/Services - Service Provider</th>
    													<th>Remarks</th>
    													<th>User</th>
    													<th>Manage</th>
    												</tr>
    											</thead>
    											<tbody>
    												
    											</tbody>
    										</table>
										<?php } ?>
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



<?php 
	include "footer.php";
	
	function get_user($user){
		global $conn;
		global $branch_id;
		$sql="SELECT * from user where id=$user and branch_id='".$branch_id."' order by id desc";
		$result=query_by_id($sql,[],$conn);
		foreach($result as $row) {
			return $row['name'];
		}
	}
	
	function getclient($cid){
		global $conn;
		global $branch_id;
		$sql="SELECT * from client where id=$cid and branch_id='".$branch_id."'";
		$result=query_by_id($sql,[],$conn);
		foreach($result as $row) 
		{
			return $row['name'];
		}
	}
	
	function getstaff($sid){
		global $conn;
		global $branch_id;
		$sql="SELECT * FROM `beauticians` where id=$sid and branch_id='".$branch_id."'";
		$result=query_by_id($sql,[],$conn);
		foreach($result as $row){
			return $row['name'];
		}
	}
	
	function getcont($cid){
		global $conn;
		global $branch_id;
		$sql="SELECT * from client where id=$cid and branch_id='".$branch_id."'";
		$result=mysqli_query($con,$sql);
		if($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
			return $row['cont'];
		}
	}
	
		function getitem($id)
	{
		global $conn;
		global $branch_id;
		$str = "";
		$item_id=explode(",",$id)[1];
		if($item_id == ''){
		    $item_id = 0;
		}
		$chk_type = explode(",",$id)[0];
		if($chk_type == 'sr'){
		    $type = 'Service';
		} else if($chk_type == 'pa'){
		    $type = 'Package';
		} else if($chk_type == 'pr'){
		    $type = 'Product';
		} else if($chk_type == 'mem'){
		    $type = 'Membership';
		} else {
		        $type = '';
		}
		
		switch ($type) 
		{
			case "Service":
			$sql="SELECT * from `service` where id=$item_id";
			$result=query_by_id($sql,[],$conn);
			foreach($result as $row) 
			{
				$str = $row['name']."(Service)";
			}
			break;
			case "Product":
			$sql="SELECT * from `products` where id=$item_id";
			$result=query_by_id($sql,[],$conn);
			foreach($result as $row) 
			{
				$str = $row['name']."(Product)";
			}
			break;
			case "Package":
			$sql="SELECT * from `packages` where id=$item_id and branch_id='".$branch_id."'";
			$result=query_by_id($sql,[],$conn);
			foreach($result as $row) {
				$str = $row['name']."(Package)";
			}
			case "Membership":
			$sql="SELECT * from `membership_discount` where id=$item_id";
			$result=query_by_id($sql,[],$conn);
			foreach($result as $row) {
				$str = $row['membership_name']."(Membership)";
			}
			break;
			default:
			$str = "";
			break;
		}
		return $str;
	}
	
	function checkstaff($sid,$inv){
		global $conn;
		global $branch_id;
		$sql = "SELECT * from invoice_items_".$branch_id." where staffid=$sid and iid=$inv and active=0 and branch_id='".$branch_id."'";
		$result=query($sql,[],$conn);
		if($result)
		{
			foreach($result as $row) {
				return 1;
			}}else{
			return 0;
		}
	}
	
	function checkproduct($pr,$pid,$inv){
		global $conn;
		global $branch_id;
		if($pr=="pr")
		$pr="Product";
		if($pr=="sr")
		$pr="Service";
		if($pr=="pa")
		$pr="Package";
		if($pr=="mem")
		$pr="Membership";
		$sql = "SELECT * from invoice_items_".$branch_id." where type='$pr' and service=$pid and iid=$inv and active=0 and branch_id='".$branch_id."'";
		$result=query_by_id($sql,[],$conn);
		if($result) 
		{
			return 1;
			}else{
			return 0;
		}
	}
	
	?>	

    <script>
        $(document).ready(function(){
			var empTbl = $('#empTable').DataTable({
				'processing': true,
				'serverSide': true,
				'serverMethod': 'post',
				'ajax': {
					'url':'ajax/billreport.php'
				},
				'columns': [
				{ data: 'doa'},
				{ data: 'billid'},
				{ data: 'name'},
				{ data: 'cont'},
				{ data: 'total'},
				{ data: 'paid'},
				{ data: 'payment_detail'},
				{ data: 'due'},
				{ data: 'invoice'},
				{ data: 'service_provider'},
				{ data: 'notes'},
				{ data: 'user'},
				{ data: 'action'},
				],
				'columnDefs': [ {
					'targets': [0,2,3,4,5], // column index (start from 0)
					'orderable': false, // set orderable false for selected columns
				}]
			});
		
			var buttons = new $.fn.dataTable.Buttons(empTbl, {
		     buttons: [{
					extend: 'excelHtml5',
					text: '<i class="fa fa-file-excel-o" aria-hidden="true"></i> Export',
					titleAttr: 'Export to Excel',
					title: '<?php echo systemname($conn); ?>',
					exportOptions: {
						columns: ':not(:last-child):not(.notexport)',
					}
				}
		    ]
		}).container().appendTo($('#download-btn'));

		buttons[0].classList.add('d-block');
		buttons[0].classList.add('custom-download-btn');
		buttons[0].classList.add('pull-right');
		buttons[0].classList.remove('dt-buttons');
		$('.custom-download-btn a').removeClass('btn-default');
		// $('.custom-download-btn a').attr({"data-toggle":"tooltip","data-placement":"top","data-html":"true"});
		$('.custom-download-btn a').addClass('btn-warning pull-right download-btn mr-left-5');
	
			
			
		});
		
		// jquery function to change daterange date in iso date standard  Y-m-d (2019-11-01)

    	function isoDate(date){	
    		var datespit = date.split('/');
    		var day = datespit[1].replace(' ','');
    		var month = datespit[0].replace(' ','');
    		var year = datespit[2].replace(' ','');
    		return year+'-'+month+'-'+day;
    	}
    		function invoice_delete(inv){
    	   
    	    $.ajax({
          type: "POST",
          url: 'invoice_delete.php',
          data:{inv:inv},
          success:function(html) {
            
              if(html == 1){
             alert("Record Deleted Successfully!!");
              }
              else{
                  alert("Error In Deleting Record!!");
              }
          }

      });
    	    
    	}
    </script>

