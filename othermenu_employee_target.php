<?php 
	include_once './includes/db_include.php';
	$branch_id = $_SESSION['branch_id'];
	include "topbar.php";
	include "header.php";
	include "menu.php";
	include "reportMenu.php";
// 	$service_provider = query_by_id("SELECT id, name FROM beauticians WHERE branch_id='".$branch_id."' AND active='0'",[],$conn);

?>
<style>
    @media print {
        #fetch_balace_report{
            display:block;
        }
        header, nav, footer, .heading-with-btn, .col-lg-3.col-md-4.col-sm-4.col-xs-12, #date_filter, #paymentModal {
            display:none;
        }
    }
</style>
<!--<div id="displayAjaxContent"> </div>-->
<!-- Dashboard wrapper starts -->
<div class="dashboard-wrapper">
	
	<!-- Main container starts -->
	<div class="main-container">
		<!-- Row starts -->
		<div class="row gutter">
			<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
				<div class="panel">
					<div class="panel-heading">
						<h4 id='payment_mode' class="pull-left">Employee Report</h4>
						<a href="javascript:void(0)" onclick="export_data()" target="_blank">
						    <button type="button" class="btn btn-warning pull-right">
						        <i class="fa fa-file-excel-o" aria-hidden="true"></i>Export
						    </button>
						</a>
					    <div class="clearfix"></div>
					</div>
					<div class="panel-body">
						<div class="row"><br />
							<div class="col-lg-4 col-md-4 col-sm-6">
								<label>Select date :</label>
							    <input type="text" class="form-control" id="daterange" range-attr="daterange" name="dates">
							</div>
							<!--<div class="col-lg-4 col-md-4 col-sm-6">-->
							<!--	<label>Service provider :</label>-->
							<!--    <select class="form-control" id="service_provider">-->
							    	<?php
							    	// 	foreach($service_provider as $sp){
							    	// 		echo '<option value="'.$sp['id'].'">'.$sp['name'].'</option>';
							    	// 	}
							    	?>
							<!--    </select>-->
							<!--</div>-->
							<div class="col-md-3 col-md-3 col-sm-3 col-xs-12">
							    <lable>&nbsp;</lable>
							    <div class="form-group">
							        <button class="btn btn-filter btn-sm" onclick="fetch_data()"><i class="fa fa-filter" aria-hidden="true"></i>Filter</button>
							    </div>
							</div>
						</div>					
						<div class="row">
                            <br/>
							<div class="col-lg-12">
								<div id="fetch_employee_target_report" class="table-responsive">
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
	function isoDate(date){	
	    var datespit = date.split('/');
		var day = datespit[1].replace(' ','');
		var month = datespit[0].replace(' ','');
		var year = datespit[2].replace(' ','');
		return year+'-'+month+'-'+day;
	}
 	 function fetch_data(){
 		var text = " Employees Report";
 		var daterange = $("#daterange").val();
//  		var service_provider = $('#service_provider').val();
 		var date = daterange.split("-");
    	if(daterange == ''){
    		var from_date = '<?= date('Y-m-d') ?>';
    		var to_date = '<?= date('Y-m-d') ?>';
    	} else {
    		var from_date = isoDate(date[0]);
    		var to_date = isoDate(date[1]);
    	}
 		 document.getElementById("payment_mode").innerHTML = text;
 		 $.ajax({
    		 url:"ajax/fetch_employee_target_report.php?from="+from_date+"&to="+to_date,
    		 method:"GET",
    		 success:function(data){
				if(data){
					$('#fetch_employee_target_report').html(data);
				}
			 },
		 });
 	 }
		
	$(function(){
	var setTimer
	var StartDispalyingTimer = function (){var start = 1;
        setTimer = setInterval(function () {start++;}, 1000);
    }

	$('#date_filter').click(function(){
        var daterange = $('#daterange').val();
    	var date = daterange.split("-");
    	if(daterange == ''){
    		var from_date = '<?= date('Y-m-d') ?>';
    		var to_date = '<?= date('Y-m-d') ?>';
    	} else {
    		var from_date = isoDate(date[0]);
    		var to_date = isoDate(date[1]);
    	}

    	 $.ajax({
    		 url:"ajax/fetch_employee_target_report.php?from_date="+from_date+"&to_date="+to_date,
    		 method:"GET",
    		 success:function(data){
				if(data){
					$('#fetch_employee_target_report').html(data);
				}
			 },
		 });
	 });
	 $.ytLoad();
    
	// jquery function to change daterange date in iso date standard  Y-m-d (2019-11-01)
 
	});
	
	$('document').ready(function(){
		fetch_data();
	});

	function export_data(){
		var daterange = $("#daterange").val();
//  		var service_provider = $('#service_provider').val();
 		var date = daterange.split("-");
    	if(daterange == ''){
    		var from_date = '<?= date('Y-m-d') ?>';
    		var to_date = '<?= date('Y-m-d') ?>';
    	} else {
    		var from_date = isoDate(date[0]);
    		var to_date = isoDate(date[1]);
    	}
    	window.location.href="exportdata/service-provider-report.php?from="+from_date+"&to="+to_date;
	}

 </script>
 <?php  include "footer.php";?>