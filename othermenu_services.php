<?php 
	include_once './includes/db_include.php';
	$branch_id = $_SESSION['branch_id'];
	include "topbar.php";
	include "header.php";
	include "menu.php";
	include "reportMenu.php";
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
						<h4 id='payment_mode'>Services Report</h4>
					</div>
					<div class="panel-body">
						<div class="row"><br />
							<div class="col-lg-4">
								<input type="text" class="form-control" id="daterange" range-attr="daterange" name="dates" onchange="fetch_data();">
							</div>
							<div class="col-lg-2">
							    <SELECT class="form-control" name="rp_type" id="rp_type" onchange="fetch_data();">
							        <OPTION value='1'>Services</OPTION>
							        <OPTION value='2'>Products</OPTION>
							        <OPTION value='3'>Packages</OPTION>
							        <OPTION value='4'>Memberships</OPTION>
							    </SELECT>
							</div>
						</div>					
						<div class="row">
                            <br/>
							<div class="col-lg-12">
								<div id="fetch_services_report" class="table-responsive">
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
     	var text = "Services Report";
     	var daterange = $("#daterange").val();
     	var rp_type = $("#rp_type").val();
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
    		 url:"ajax/fetch_services_report.php?from="+from_date+"&to="+to_date+"&type="+rp_type,
    		 method:"GET",
    		 success:function(data){
    			// if(data){
    				$('#fetch_services_report').html(data);
    			// }
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
 		var rp_type = $("#rp_type").val();
    	var date = daterange.split("-");
    	if(daterange == ''){
    		var from_date = '<?= date('Y-m-d') ?>';
    		var to_date = '<?= date('Y-m-d') ?>';
    	} else {
    		var from_date = isoDate(date[0]);
    		var to_date = isoDate(date[1]);
    	}
    	$.ajax({
    		 url:"ajax/fetch_services_report.php?from_date="+from_date+"&to_date="+to_date+"&type="+rp_type,
    		 method:"GET",
    		 success:function(data){
					$('#fetch_services_report').html(data);
				
			 },
		 });
	 });
	 $.ytLoad();
	// jquery function to change daterange date in iso date standard  Y-m-d (2019-11-01)
	});
	
	$('document').ready(function(){
		fetch_data();
	});

 </script>
 <?php  include "footer.php";?>