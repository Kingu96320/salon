<?php 
	include_once './includes/db_include.php';
	$branch_id = $_SESSION['branch_id'];
	include "topbar.php";
	include "header.php";
	include "menu.php";
	include "reportMenu.php";

	if(isset($_GET['from'])){
	    $start_date = $_GET['from'];
	    $start_date = explode("-",$start_date);
	    $start_date = $start_date['1'].'/'.$start_date['2'].'/'.$start_date['0'];
	} else {
	    $start_date = date('m/d/Y');
	}
	
	if(isset($_GET['to'])){
	    $end_date = $_GET['to'];
	    $end_date = explode("-",$end_date);
	    $end_date = $end_date['1'].'/'.$end_date['2'].'/'.$end_date['0'];
	} else {
	    $end_date = date('m/d/Y');
	}

	$service_provider = query_by_id("SELECT id, name FROM beauticians WHERE branch_id='".$branch_id."' AND active='0'",[],$conn);

?>
<style>
    @media print {
        #fetch_balace_report, #dates{
            display:block!important;
        }
        header, nav, footer, .heading-with-btn, .col-lg-3.col-md-4.col-sm-4.col-xs-12, #date_filter, #paymentModal, .pr-hide {
            display:none;
        }
        table, tr, td, th{
        	border: 1px solid #000;
        	border-collapse: collapse;
        }
        th, td{
        	padding: 3px;
        }
        table {
        	width: 300px;
        }  
        #payment_mode{
        	float: none!important;
        	text-transform: uppercase;
        	text-align: center;
        	width: 300px;
        	font-size: 24px;
        }
        h4{
        	margin: 0px;	
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
						<h4 id='payment_mode' class="pull-left">Job card report</h4>
						<a href="javascript:void(0)" onclick="export_data()" class="pr-hide" target="_blank">
						    <button type="button" class="btn btn-warning pull-right">
						        <i class="fa fa-file-excel-o" aria-hidden="true"></i>Export
						    </button>
						</a>
					    <div class="clearfix"></div>
					</div>
					<?php
						if(isset($_GET['sp_id'])){
                    		$sp_id = $_GET['sp_id'];
                    	} else {
                    		$sp_id = $service_provider[0]['id'];
                    	}

                    	if(isset($_GET['from']) && isset($_GET['to'])){
                    		$df = " AND i.doa BETWEEN  '".$_GET['from']."' AND '".$_GET['to']."' ";
                    	} else {
                    		$df = " AND i.doa BETWEEN  '".date('Y-m-d')."' AND '".date('Y-m-d')."' ";
                    	}
					?>
					<div class="panel-body">
						<div class="row"><br class="pr-hide" />
							<div class="col-lg-4 col-md-4 col-sm-6 pr-hide">
								<label>Select date :</label>
							    <input type="text" class="form-control" range-attr="daterange" id="daterange" name="date" value="<?= $start_date.' - '.$end_date ?>"  placeholder="01/01/1990 - 12/05/2000" required readonly>	
							</div>
							<div class="col-lg-4 col-md-4 col-sm-6 pr-hide">
								<label>Service provider :</label>
							    <select class="form-control" id="service_provider">
							    	<?php
							    		foreach($service_provider as $sp){ 
							    		    if(isset($_GET['sp_id']) && $_GET['sp_id'] > 0){
							    		        if($sp['id'] == $_GET['sp_id']){
							    		            $selected = 'selected';
							    		        } else {
							    		            $selected = '';
							    		        }
							    		    } else {
							    		        $selected = '';
							    		    }
							    		?>
							    			<option <?= $selected ?> value="<?= $sp['id'] ?>"><?= $sp['name'] ?></option>
							    	<?php	}
							    	?>
							    </select>
							</div>
							<div class="col-md-3">
							    <lable>&nbsp;</lable>
							    <div class="form-group">
							         <button class="btn pr-hide btn-filter btn-sm" onclick="filterDailyreport()"><i class="fa fa-filter" aria-hidden="true"></i>Filter</button>&nbsp;&nbsp;&nbsp;&nbsp;<button class="btn pr-hide btn-danger btn-sm" onclick="window.location.href='job-card.php'"><i class="fa fa-times" aria-hidden="true"></i>Clear</button>&nbsp;&nbsp;&nbsp;&nbsp;<button class="btn pr-hide btn-warning btn-sm" onclick="window.print()"><i class="fa fa-print" aria-hidden="true"></i>Print</button>
							    </div>
							</div>
						</div>
						<div class="row" id="dates" style="display: none;">
							<div class="col-md-12">
								<b>Date : <?= my_date_format($start_date).'</b> to <b>'.my_date_format($end_date) ?></b><br />
								<b>Service provider : <?php
							                	echo query_by_id("SELECT name FROM beauticians WHERE id='".$sp_id."'",[],$conn)[0]['name'];
							                ?></b>
							</div>
						</div>					
						<div class="row">
                            <br/>
                            <?php              
                                $jb = query_by_id("SELECT ii.service, ii.quantity, ii.price, ii.id, c.name FROM invoice_items_".$branch_id." ii LEFT JOIN invoice_multi_service_provider imps ON imps.ii_id = ii.id LEFT JOIN client c ON c.id = ii.client LEFT JOIN invoice_".$branch_id." i ON i.id = ii.iid WHERE imps.service_provider = '".$sp_id."' AND ii.branch_id='".$branch_id."' $df ",[],$conn);
                                $service_amount = 0;
                                $service_qty = 0;
                                $product_amount = 0;
                                $product_qty = 0;
                                $package_amount = 0;
                                $package_qty = 0;
                                $membership_amount = 0;
                                $membership_qty = 0;
                                $total_revenue = 0;

                                if($jb){
                                	foreach($jb as $data){
                                		$type = explode(',',$data['service'])[0];
                                		if($type == 'sr'){
                                			$service_amount += $data['price'];
                                			$service_qty += $data['quantity']; 
                                		} else if($type == 'pr'){
                                			$product_amount += $data['price'];
                                			$product_qty += $data['quantity']; 
                                		} else if($type == 'pa'){
                                			$package_amount += $data['price'];
                                			$package_qty += $data['quantity']; 
                                		} else if($type == 'mem'){
                                			$membership_amount += $data['price'];
                                			$membership_qty += $data['quantity']; 
                                		} else {

                                		}
                                	}
                                	$total_revenue = $service_amount + $product_amount + $package_amount + $membership_amount;
                                }
                            ?>
							<div class="col-lg-12">
							    <table class="table table-stripped table-bordered" style="max-width: 500px;">
							        <thead>
							            <tr>
							                <th colspan="3" class="text-center">Total Revenue Collected : <span><?= number_format($total_revenue, 2) ?></span></th>
							            </tr>
							            <tr>
							                <th><strong>Type</strong></th>
							                <th>Quantity</th>
							                <th>Price</th>
							            </tr>
							        </thead>
							        <tbody>
							            <tr>
							                <td><strong>Services</strong></td>
							                <td><?= $service_qty; ?></td>
							                <td><?= number_format($service_amount,2) ?></td>
							            </tr>
							            <tr>
							                <td><strong>Products</strong></td>
							                <td><?= $product_qty ?></td>
							                <td><?= number_format($product_amount,2) ?></td>
							            </tr>
							            <tr>
							                <td><strong>Packages</strong></td>
							                <td><?= $package_qty ?></td>
							                <td><?= number_format($package_amount,2) ?></td> 
							            </tr>
							            <tr>
							                <td><strong>Memberships</strong></td>
							                <td><?= $membership_qty ?></td>
							                <td><?= number_format($membership_amount,2) ?></td>                
							            </tr>
							        </tbody>
							    </table>
							</div> <br />
							<div class="col-lg-12">
							 	<table class="table table-stripped table-bordered" style="max-width: 500px;">
							        <thead>
							            <tr>
							                <th colspan="3" class="text-center">Service List</span></th>
							            </tr>
							            <tr>
							                <th><strong>Client</strong></th>
							                <th>Item</th>
							                <th>Price</th>
							            </tr>
							        </thead>
							        <tbody>
							        <?php
							        	if($jb){
							        		foreach($jb as $data){
							        			?>
							        			<tr>
							        				<td><?= $data['name'] ?></td>
							        				<td><?= getservice($data['service']) ?></td>
							        				<td><?= $data['price'] ?></td>
							        			</tr>
							        			<?php
							        		}
							        	} else {
							        		echo "<tr><td colspan='3' class='text-center'>No record found.</td></tr>";
							        	}
							        ?>
							        </tbody>
							    </table>
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


	function filterDailyreport(){
		var daterange = $('#daterange').val();
		var date = daterange.split("-");
		var service_provider = $('#service_provider').val();
		if(daterange == ''){
			var from = '';
			var to = '';
		} else {
			var from = isoDate(date[0]);
			var to = isoDate(date[1]);
		}
		window.location.href = '?from='+from+'&to='+to+'&sp_id='+service_provider;
	}

	function export_data(){
		var daterange = $('#daterange').val();
		var date = daterange.split("-");
		var service_provider = $('#service_provider').val();
		if(daterange == ''){
			var from = '';
			var to = '';
		} else {
			var from = isoDate(date[0]);
			var to = isoDate(date[1]);
		}
		window.location.href = '<?= BASE_URL ?>exportdata/job-card.php?from='+from+'&to='+to+'&sp_id='+service_provider;
	}
		
	$(function(){
		var setTimer
		var StartDispalyingTimer = function (){var start = 1;
	        setTimer = setInterval(function () {start++;}, 1000);
	    }
		// jquery function to change daterange date in iso date standard  Y-m-d (2019-11-01)
	});

 </script>
 <?php  include "footer.php";

 function getservice($get_id){
		global $conn;
		global $branch_id;
		$id = EXPLODE(",",$get_id)[1];
		if(EXPLODE(",",$get_id)[0] == 'sr'){
			$sql ="SELECT CONCAT('(Service)',' ',name) as name FROM `service` where active='0' and id='$id'";	
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
	    	return html_entity_decode($row['name']);
	    }
	}
?>