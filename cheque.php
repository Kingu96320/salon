<?php 
	include_once './includes/db_include.php';
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
						<h4>Cheque Report</h4>
					</div>
					<div class="panel-body">
						<div class="row">
							<div class="col-lg-12">
							<?php $current_date=date('Y-m-d')?>
								
									<div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
										<div class="form-group">
											<label for="from">Date From</label>
											<input type="text" class="form-control date" value="<?=$current_date?>" id="from" readonly>
										</div>
									</div>
									<div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
										<div class="form-group">
											<label for="to">Date To</label>
											<input type="text" class="form-control date" value="<?=$current_date?>" id="to" readonly>
										</div>
									</div>
									<div class="col-lg-4 col-md-4 col-sm-6 col-xs-12"><br>
										<button type="submit" class="btn btn-info" id="date_filter">Filter</button>
									</div>
							</div>
							<div class="col-lg-12">
								<button class="btn btn-default buttons-excel buttons-html5" id="excel">Excel</button>
								<div id="fetch_balace_report">
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
 <?php  include "footer.php";?>
 <script src="../salonSoftFiles/js/jquery.table2excel.js"></script>

 <script>
	$(function(){
	$('#date_filter').click(function(){
	var from_date=$('#from').val();
	var to_date=$('#to').val();
	 $.ajax({
		 url:"ajax/fetch_balance_report.php?from_date="+from_date+"&to_date="+to_date+"&pageinfo=Cheque",
		 method:"GET",
		 success:function(data){
				if(data){
					$('#fetch_balace_report').html(data);
				}
			 },
		 });
	 });
	var from_date=$('#from').val();
	var to_date=$('#to').val();
	 $.ajax({
		 url:"ajax/fetch_balance_report.php?from_date="+from_date+"&to_date="+to_date+"&pageinfo=Cheque",
		 method:"GET",
		 success:function(data){
				if(data){
					$('#fetch_balace_report').html(data);
					 
				}
			 },
		 });
	});
	
 
 </script>