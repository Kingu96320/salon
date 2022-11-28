<?php 
	include_once './includes/db_include.php';
	$branch_id = $_SESSION['branch_id'];
	include "topbar.php";
	include "header.php";
	include "menu.php";
?>
 
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
						<h4>Cash Balance Report</h4>
					</div>
					<div class="panel-body">
						<div class="row">
							<?php $current_date=date('Y-m-d')?>
							<div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
								<div class="form-group">
									<label class=" control-label">Select dates</label>
									<input type="text" class="form-control" range-attr="daterange" id="daterange" name="date"  placeholder="01/01/1990 - 12/05/2000" required readonly>	
								</div>
							</div>
							<div class="col-lg-4 col-md-4 col-sm-6 col-xs-12"><br>
								<button type="submit" class="btn btn-filter" id="date_filter"><i class="fa fa-filter" aria-hidden="true"></i>Filter</button>
							</div>
							<div class="clearfix"></div>
							<div class="col-lg-12">
								
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


 <script>
 
		
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
    		 url:"ajax/fetch_balance_report.php?from_date="+from_date+"&to_date="+to_date+"&pageinfo=Cash",
    		 method:"GET",
    		 success:function(data){
				if(data){
					$('#fetch_balace_report').html(data);
				}
			 },
		 });
	 });
	 $.ytLoad();
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
		 url:"ajax/fetch_balance_report.php?from_date="+from_date+"&to_date="+to_date+"&pageinfo=Cash",
		 method:"GET",
		 success:function(data){
				if(data){
					$('#fetch_balace_report').html(data);
				}
			 },
		 });
		
		// jquery function to change daterange date in iso date standard  Y-m-d (2019-11-01)

    	function isoDate(date){	
    	    var datespit = date.split('/');
    		var day = datespit[1].replace(' ','');
    		var month = datespit[0].replace(' ','');
    		var year = datespit[2].replace(' ','');
    		return year+'-'+month+'-'+day;
    	}
		 
	});
	
	

 </script>
 <?php  include "footer.php";?>