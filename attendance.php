<?php 
	include_once './includes/db_include.php';
	$branch_id = $_SESSION['branch_id'];
	include "topbar.php";
	include "header.php";
	include "menu.php";
	if($_SESSION['u_role']!=1){ 
	    header('LOCATION: dashboard.php');
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
						<h4>Attendance</h4>
					</div>
					<div class="panel-body">
						<div class="row">
							<div id="filter_input">
							<?php $current_date=date('Y-m-d')?>
								
									<div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
										<div class="form-group">
											<label for="from">Attendance type</label>
											<select class="form-control" id="attendance_type" onclick="alert_show();">
											    <option value="1">Service provider</option>
											    <option value="2">Staff</option>
											</select>
										</div>
									</div>
									
									<div class="col-lg-3 col-md-3 col-sm-6 col-xs-12" id="suid_div">
										<div class="form-group">
											<label for="from">Select Service providers</label>
											<select class="form-control" id="suid">
											    <?php 
											        $qry_data =  query_by_id("SELECT id,name from beauticians where active='0' and branch_id='".$branch_id."'",[],$conn);
											        if($qry_data){
											            foreach($qry_data as $key_qry=>$val_qry){
								                ?>
											                <option value="<?=$val_qry['id']?>"><?=$val_qry['name']?></option>
								                <?php
											                
											            }
											        }
											    ?>
											</select>
										</div>
									</div>
									
									<div class="col-lg-3 col-md-3 col-sm-6 col-xs-12" id="euid_div" style="display:none;">
										<div class="form-group">
											<label for="from">Select Staff</label>
											<select class="form-control" id="euid">
											    <?php 
											        $qry_data =  query_by_id("SELECT id,name from employee where active='0'",[],$conn);
											        if($qry_data){
											            foreach($qry_data as $key_qry=>$val_qry){
								                ?>
											                <option value="<?=$val_qry['id']?>"><?=$val_qry['name']?></option>
								                <?php
											                
											            }
											        }
											    ?>
											</select>
										</div>
									</div>
									<?php
									    $stdate = date("m/d/Y", strtotime(date("Y-m-d",strtotime(date("Y-m-d")))."-1 month"));
									    $edate = date("m/d/Y");
									    $final_default_date = $stdate.' - '.$edate;
									?>
									<div class="col-lg-2 col-md-2 col-sm-2 col-xs-12">
										<div class="form-group">
											<label>Month</label>
											<select id="month" class="form-control">
											    <option value="01" <?= date('m') == '01'?'selected':'' ?>>January</option>
											    <option value="02" <?= date('m') == '02'?'selected':'' ?>>February</option>
											    <option value="03" <?= date('m') == '03'?'selected':'' ?>>March</option>
											    <option value="04" <?= date('m') == '04'?'selected':'' ?>>April</option>
											    <option value="05" <?= date('m') == '05'?'selected':'' ?>>May</option>
											    <option value="06" <?= date('m') == '06'?'selected':'' ?>>June</option>
											    <option value="07" <?= date('m') == '07'?'selected':'' ?>>July</option>
											    <option value="08" <?= date('m') == '08'?'selected':'' ?>>August</option>
											    <option value="09" <?= date('m') == '09'?'selected':'' ?>>September</option>
											    <option value="10" <?= date('m') == '10'?'selected':'' ?>>October</option>
											    <option value="11" <?= date('m') == '11'?'selected':'' ?>>November</option>
											    <option value="12" <?= date('m') == '12'?'selected':'' ?>>December</option>
											</select>
										</div>
									</div>
									<div class="col-lg-2 col-md-2 col-sm-2 col-xs-12">
										<div class="form-group">
											<label>Year</label>
											<select id="year" class="form-control">
											    <?php
											        for($i = 2020; $i <= date('Y'); $i++){ ?>
											            <option value="<?= $i ?>" <?= $i==date('Y')?'selected':'' ?>><?= $i ?></option>
											        <?php }
											    ?>
											</select>
										</div>
									</div>
									<div class="col-lg-1 col-md-1 col-sm-6 col-xs-12"><br>
										<button type="submit" class="btn btn-primary" id="date_filter"><i class="fa fa-filter" style="margin-left:0px;" aria-hidden="true"></i>Filter</button>
									</div>
							</div>
						</div>
						<div id="fetch_balace_report" class="table-responsive">
						    
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
 
 <script src="../salonSoftFiles_new/js/jquery.table2excel.js"></script>

 <script>
 function alert_show(){
     var type = $("#attendance_type").val();
     if(type=="1"){
         $("#suid_div").show();
         $("#euid_div").hide();
     }else{
         $("#euid_div").show();
         $("#suid_div").hide();
     }
 }
    // jquery function to change daterange date in iso date standard  Y-m-d (2019-11-01)

	function isoDate(date){	
		var datespit = date.split('/');
		var day = datespit[1].replace(' ','');
		var month = datespit[0].replace(' ','');
		var year = datespit[2].replace(' ','');
		return year+'-'+month+'-'+day;
	}
	
	
	$(function(){
    	$('#date_filter').click(function(){
    	    var month = $('#month').val();
    	    var year = $('#year').val();
        	var type=$('#attendance_type').val();
        	var suid=$('#suid').val();
        	var euid=$('#euid').val();
        	$.ajax({
        		 url:"ajax/attendance.php?month="+month+"&year="+year+"&type="+type+"&suid="+suid+"&euid="+euid,
        		 method:"GET",
        		 success:function(data){
    				if(data){
    					$('#fetch_balace_report').html(data);
    				}
    			 },
    		 });
    	});
    	
    	var month = $('#month').val();
    	var year = $('#year').val();
    	var attendance_type=$('#attendance_type').val();
    	var suid=$('#suid').val();
    	var euid=$('#euid').val();
    	$.ajax({
    		 url:"ajax/attendance.php?month="+month+"&year="+year+"&type="+attendance_type+"&suid="+suid+"&euid="+euid,
    		 method:"GET",
    		 success:function(data){
				if(data){
					$('#fetch_balace_report').html(data);
				}
			 },
        });
	});
	

 </script>