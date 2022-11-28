<?php 
    header('LOCATION: otherreports.php');
    die();
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
						<h4 id='payment_mode'>Weekly Report</h4>
					</div>
					<div class="panel-body">
						<div class="row"><br />
							<div class="col-lg-2">
								<select class="form-control" id="daterange" onchange="fetch_data()">
									<?php
										$curYear = date('Y');
										$start = 2019;
										for($i=$start; $i<=$curYear; $i++){ ?>
											<option value="<?= $i ?>" <?= $i==$curYear?'selected':'' ?>><?= $i ?></option>;
										    <?php
										}
									?>
								</select>
							</div>
						</div>					
						<div class="row">
                            <br/>
							<div class="col-lg-12">
								<div id="fetch_weekly_report" class="table-responsive">
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
 		var text = " Weekly Report";
 		var month = $("#daterange").val();
 		 document.getElementById("payment_mode").innerHTML = text;
 		 $.ajax({
    		 url:"ajax/fetch_weekly_report.php?month="+month,
    		 method:"GET",
    		 success:function(data){
				if(data){
					$('#fetch_weekly_report').html(data);
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
 		var month = $("#daterange").val();
    	 $.ajax({
    		 url:"ajax/fetch_weekly_report.php?month="+month,
    		 method:"GET",
    		 success:function(data){
				if(data){
					$('#fetch_weekly_report').html(data);
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

 </script>
 <?php  include "footer.php";?>