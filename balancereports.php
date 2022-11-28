<?php 
	include_once './includes/db_include.php';
	$branch_id = $_SESSION['branch_id'];
	include "topbar.php";
	include "header.php";
	include "menu.php";
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

			<div class="col-md-12">
				<div class="form-group" style="width: 15%;">
					<label >Payment method</label>
					<select name="method[]" id='modes' data-validation="required" class="form-control act" onchange = 'fetch_data(this.value);'>
						<!--<option value="">--Select--</option>-->
						<?php $sql_pay_mode="Select * FROM `payment_method` where status='1'";
							$result_pay_mode =query_by_id($sql_pay_mode,[],$conn); ?>
							<option value="All"><?php echo "All"; ?></option> 
						<?php	foreach($result_pay_mode as $row_pay_mode){
							?>
							    <option value="<?= $row_pay_mode['id'] ?>"><?=$row_pay_mode['name']?></option> 
						    <?php } ?>  
					</select>
				</div>
			</div>
			<div class="clearfix"></div>			
			<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
				<div class="panel">

					<div class="panel-heading heading-with-btn">
						<h4 id='payment_mode' class="pull-left">Cash Balance Report</h4>
						<span data-toggle="modal"><button onclick="window.print()" class="btn btn-warning pull-right"><i class="fa fa-print" aria-hidden="true"></i>Print</button></span>
					    <div class="clearfix"></div>
					</div>
					<div class="panel-body">					
						<div class="row">
							<?php $current_date=date('Y-m-d')?>
							<div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
								<div class="form-group">
									<label class=" control-label">Select dates</label>
									<input type="text" class="form-control" range-attr="daterange" id="daterange" name="date"  placeholder="01/01/1990 - 12/05/2000" required readonly>	
								</div>
							</div>
							<div class="col-lg-4 col-md-4 col-sm-6 col-xs-12"><br>
								<button type="submit" class="btn btn-filter" id="date_filter"><i class="fa fa-filter" aria-hidden="true"></i>Filter</button>
							</div>
							<div class="clearfix"></div>

							<br/>
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
 	 function fetch_data(method_id){
 	 	var method = '';
 	 	var from_date = '<?= date('Y-m-d') ?>';
    	var to_date = '<?= date('Y-m-d') ?>';
 	 	 if(method_id == 1){
 	 	 	method = "Cash";
 	 	 }else if(method_id == 3){
 	 	 	method = "Credit/Debit card";
 	 	 }else if(method_id == 4){
 	 	 	method = "Cheque";
 	 	 }else if(method_id == 5){
 	 	 	method = "Online payment";
 	 	 }else if(method_id == 6){
 	 	 	method = "Paytm";
 	 	 }else if(method_id == 7){
 	 	 	method = "My wallet";
 	 	 }else if(method_id == 9){
 	 	 	method = "Reward points";
 	 	 }else if(method_id == 10){
 	 	 	method = "PhonePe";
 	 	 }else if(method_id == 11){
 	 	 	method = "Gpay";
 	 	 }else if(method_id == "All"){
 	 	 	method = "All";
 	 	 }
 		 var text = method + " Balance Report";
 		 document.getElementById("payment_mode").innerHTML = text;
 		 $.ajax({
    		 url:"ajax/fetch_balance_report.php?from_date="+from_date+"&to_date="+to_date+"&pageinfo="+method_id,
    		 method:"GET",
    		 success:function(data){
				if(data){
					$('#fetch_balace_report').html(data);
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
        var pageinfo = $('#modes').val();
    	var date = daterange.split("-");
    	if(daterange == ''){
    		var from_date = '<?= date('Y-m-d') ?>';
    		var to_date = '<?= date('Y-m-d') ?>';
    	} else {
    		var from_date = isoDate(date[0]);
    		var to_date = isoDate(date[1]);
    	}

    	 $.ajax({
    		 url:"ajax/fetch_balance_report.php?from_date="+from_date+"&to_date="+to_date+"&pageinfo="+pageinfo,
    		 method:"GET",
    		 success:function(data){
				if(data){
					$('#fetch_balace_report').html(data);
				}
			 },
		 });
	 });
	 $.ytLoad();
    
	// jquery function to change daterange date in iso date standard  Y-m-d (2019-11-01)

	function isoDate(date){	
	    var datespit = date.split('/');
		var day = datespit[1].replace(' ','');
		var month = datespit[0].replace(' ','');
		var year = datespit[2].replace(' ','');
		return year+'-'+month+'-'+day;
	}
		 
	});
	
	$('document').ready(function(){
		fetch_data(1);
	});

 </script>
 <?php  include "footer.php";?>