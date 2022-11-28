<?php 
	include_once './includes/db_include.php';
	$branch_id = $_SESSION['branch_id'];
	$dr = 0;
	if($_GET['dr']){
		$dr = $_GET['dr'];
	}
	$date = date('Y-m-d');
	$pay = "";
	$pm = "";

	if(isset($_POST['paymode'])){
		$pm = $_POST['paymode'];
		$pay = " and bmethod='".$_POST['paymode']."' ";
	}
	
	if(isset($_GET['sdate'])){
	    $start_date = $_GET['sdate'];
	    $start_date = explode("-",$start_date);
	    $start_date = $start_date['1'].'/'.$start_date['2'].'/'.$start_date['0'];
	} else {
	    $start_date = date('m/d/Y');
	}
	
	if(isset($_GET['edate'])){
	    $end_date = $_GET['edate'];
	    $end_date = explode("-",$end_date);
	    $end_date = $end_date['1'].'/'.$end_date['2'].'/'.$end_date['0'];
	} else {
	    $end_date = date('m/d/Y');
	}
	
	include "topbar.php";
	include "header.php";
	include "menu.php";
	include "reportMenu.php";
?>
<!-- Dashboard wrapper starts -->
<div class="dashboard-wrapper">
	
	<!-- Main container starts -->
	<div class="main-container">
		
		<!-- Row starts -->
		<div class="row gutter">
			
			<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
				<div class="panel">
					<div class="panel-heading heading-with-btn">
						<h4 class="pull-left">Service sales report</h4>
						<?php
							$url_append = '';
							if(isset($_GET['sdate'])){
				                $from = $_GET['sdate'];
				                $url_append .= 'sdate='.$from;
				            } else {
								$from = date('Y-m-d');
								$url_append .= 'sdate='.$from;
				            }
				            if(isset($_GET['edate'])){
				                $to = $_GET['edate'];
				                $url_append .= '&edate='.$to;
				            } else {
				            	$to = date('Y-m-d');
				            	$url_append .= '&edate='.$to;
				            }
				            if(isset($_GET['service_type'])){
				            	$url_append .= '&service_type='.$_GET['service_type'];
				            }
				            if(isset($_GET['stype'])){
				            	$url_append .= '&stype='.$_GET['stype'];
				            }
				        ?>
						    <a href="exportdata/services-sale-report.php?<?= $url_append ?>" target="_blank">
    						    <button type="button" class="btn btn-warning pull-right">
    						        <i class="fa fa-file-excel-o" aria-hidden="true"></i>Export
    						    </button>
    						</a>
						<span id="download-btn"></span>					
						<div class="clearfix"></div>
							
					    <div class="clearfix"></div>
					</div>
					<div class="panel-body">
						<div class="row">
						</div>
						<div class="row">
						    <div class="col-md-12">
						        <div class="row">
    						        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
    									<div class="form-group">
    										<label for="date">Select dates</label>
    										<input type="text" class="form-control" range-attr="daterange" id="daterange" name="date" value="<?= $start_date.' - '.$end_date ?>"  placeholder="01/01/1990 - 12/05/2000" required readonly>		
    									</div>
    								</div>
    								<div class="col-lg-2 col-md-2 col-sm-2 col-xs-12">
    									<div class="form-group">
    										<label for="date">Service Type</label>
    										<select id="service_type" class="form-control" onchange="getList(this.value)">
    											<option <?= isset($_GET['service_type'])&&$_GET['service_type']==''?'selected':'' ?> value="">--Select--</option>
    											<option <?= isset($_GET['service_type'])&&$_GET['service_type']=='Service'?'selected':'' ?> value="Service">Service</option>
    											<option <?= isset($_GET['service_type'])&&$_GET['service_type']=='Product'?'selected':'' ?> value="Product">Product</option>
    											<option <?= isset($_GET['service_type'])&&$_GET['service_type']=='Package'?'selected':'' ?> value="Package">Package</option>
    											<option <?= isset($_GET['service_type'])&&$_GET['service_type']=='mem'?'selected':'' ?> value="mem">Membership</option>
    										</select>	
    									</div>
    								</div>
    								<div id="append_div">
    									<?php
    										if(isset($_GET['service_type']) && $_GET['service_type'] != ''){
    											$type = $_GET['service_type'];
    											?>
    											<div class="col-lg-2 col-md-2 col-sm-2 col-xs-12">
													<div class="form-group">
														<?php
														if($type == 'Service'){
															echo '<label>Select service</label>';
														} else if($type == 'Product'){
															echo '<label>Select product</label>';
														} else if($type == 'Package'){
															echo '<label>Select package</label>';
														} else if($type == 'mem'){
															echo '<label>Select membership</label>';
														}
														?>
														<select id="s_type" class="form-control">
															<option <?= isset($_GET['stype'])&&$_GET['stype']==''?'selected':'' ?> value="">--Select--</option>
														<?php
															if($type == 'Service'){
																$service_list = query_by_id("SELECT id, name FROM service ORDER BY name ASC",[],$conn);
																if($service_list){
																	foreach($service_list as $list){ ?>
																		<option <?= isset($_GET['stype'])&&$_GET['stype']=='sr,'.$list['id']?'selected':'' ?> value="sr,<?= $list['id'] ?>"><?= $list['name']?></option>
																	<?php }
																}
															} else if($type == 'Product'){
																$product_list = query_by_id("SELECT id, name FROM products ORDER BY name ASC",[],$conn);
																if($product_list){
																	foreach($product_list as $list){ ?>
																		<option <?= isset($_GET['stype'])&&$_GET['stype']=='pr,'.$list['id']?'selected':'' ?> value="pr,<?= $list['id'] ?>"><?= $list['name'] ?></option>
																	<?php }
																}												
															} else if($type == 'Package'){
																$package_list = query_by_id("SELECT id, name FROM packages WHERE branch_id='".$branch_id."'",[],$conn);
																if($package_list){
																	foreach($package_list as $list){ ?>
																		<option <?= isset($_GET['stype'])&&$_GET['stype']=='pa,'.$list['id']?'selected':'' ?> value="pa,<?= $list['id'] ?>"><?= $list['name'] ?></option>
																	<?php }
																}														
															} else if($type == 'mem'){
																$membership_list = query_by_id("SELECT id, membership_name FROM membership_discount ORDER BY membership_name ASC",[],$conn);
																if($membership_list){
																	foreach($membership_list as $list){ ?>
																		<option <?= isset($_GET['stype'])&&$_GET['stype']=='mem,'.$list['id']?'selected':'' ?> value="mem,<?= $list['id'] ?>"><?= $list['membership_name'] ?></option>
																	<?php }
																}
															} 
															?>
														</select>
													</div>
												</div>
    											<?php
    										}
    									?>
    								</div>
    								<div class="col-md-3 col-md-3 col-sm-3 col-xs-12">
    								    <lable>&nbsp;</lable>
    								    <div class="form-group">
    								        <button class="btn btn-filter btn-sm" onclick="filterDailyreport()"><i class="fa fa-filter" aria-hidden="true"></i>Filter</button>&nbsp;&nbsp;&nbsp;&nbsp;<button class="btn btn-danger btn-sm" onclick="window.location.href='services-sale-report.php'"><i class="fa fa-times" aria-hidden="true"></i>Clear</button>
    								    </div>
    								</div>
    							</div>
						    </div>
						    <?php
						    	$sdate = strtotime(date('Y-m-d'));
					            $edate = strtotime(date('Y-m-d'));
					            if(isset($_GET['sdate'])){
					                $sdate = strtotime($_GET['sdate']);
					            }
					            if(isset($_GET['edate'])){
					                $edate = strtotime($_GET['edate']);
					            }	

					            $append_query = '';

					            if(isset($_GET['service_type']) && $_GET['service_type'] != ''){
					            	$append_query .= " AND type='".$_GET['service_type']."'";
					            } 

					            if(isset($_GET['stype']) && $_GET['stype'] != ''){
					            	$append_query .= " AND service='".$_GET['stype']."'";
					            }
					        ?>
					        <div class="col-md-12">
						    	<div class="table-responsive">
							    	<table class="table table-stripped table-bordered" id="myTable">
							    		<thead>
							    			<tr>
							    				<th>Date</th>						    				
							    				<th>Service name</th>
							    				<th>Paid services</th>
							    				<th>Package services</th>
							    				<th>Total services</th>
							    				<th>Discount</th>
							    				<th>Total revenue</th>
							    			</tr>
							    		</thead>
							    		<tbody>
							    			<?php
							    			$total_paid_services = 0;
							    			$total_package_services = 0;
							    			$total_services_count = 0;
							    			$total_discount = 0;
							    			$total_revenue = 0;

						    				for($i=$sdate; $i<=$edate; $i+=86400){
						    					$date = date("Y-m-d", $i);
						    					$data = query_by_id("SELECT DISTINCT service FROM invoice_items_$branch_id WHERE date(start_time) = '".$date."' AND active = '0' $append_query ",[],$conn);
						    					if($data){
						    						foreach($data as $ser){	
														$dis = 0;
						    							$service_name = service_name($ser['service']);
						    							$total_amount = query_by_id("SELECT SUM(price) as total FROM invoice_items_$branch_id WHERE service='".$ser['service']."' AND date(start_time)='".$date."'",[],$conn)[0]['total'];
						    							$total_revenue = $total_revenue+$total_amount;

						    							$paid_services = query_by_id("SELECT count(*) as total FROM invoice_items_$branch_id WHERE service='".$ser['service']."' AND date(start_time)='".$date."' AND package_service_id='0'",[],$conn)[0]['total'];
						    							$total_paid_services = $total_paid_services+$paid_services;

						    							$package_services = query_by_id("SELECT count(*) as total FROM invoice_items_$branch_id WHERE service='".$ser['service']."' AND date(start_time)='".$date."' AND package_service_id='1'",[],$conn)[0]['total'];
						    							$total_package_services = $total_package_services+$package_services;

						    							$total_services = query_by_id("SELECT count(*) as total FROM invoice_items_$branch_id WHERE service='".$ser['service']."' AND date(start_time)='".$date."'",[],$conn)[0]['total'];
						    							$total_services_count = $total_services_count+$total_services;

						    							$items = query_by_id("SELECT actual_price, price, quantity FROM `invoice_items_".$branch_id."` WHERE service='".$ser['service']."' AND date(start_time)='".$date."' ",[],$conn);
												        foreach($items as $item){
												            $dis = $dis+(($item['actual_price']*$item['quantity'])-$item['price']);
												        }
												        $total_discount = $total_discount+$dis;
													       
												       
												        ?>
												        <tr>
												        	<td><?php echo my_date_format($date) ?></td>
												        	<td><?php echo $service_name ?></td>
												        	<td><?php echo $paid_services ?></td>
												        	<td><?php echo $package_services ?></td>
												        	<td><?php echo $total_services ?></td>
												        	<td><?php echo number_format($dis,2) ?></td>
												        	<td><?php echo number_format($total_amount,2) ?></td>
												        </tr>
												        <?php
						    						}
						    					}
						    				}
							    			?>
							    			<tr>
							    				<td colspan="2" align="right"><b>Total</b></td>
							    				<td><b><?php echo $total_paid_services; ?></b></td>
							    				<td><b><?php echo $total_package_services; ?></b></td>
							    				<td><b><?php echo $total_services_count; ?></b></td>
							    				<td><b><?php echo number_format($total_discount,2); ?></b></td>
							    				<td><b><?php echo number_format($total_revenue,2); ?></b></td>
							    			</tr>
							    		</tbody>
							    	</table>
							    </div>
							</div>
					    </div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
</div>
<?php 
	include "footer.php";
	function service_name($get_id){
		global $conn;
		$id = EXPLODE(",",$get_id)[1];
		if(EXPLODE(",",$get_id)[0] == 'sr'){
			$sql ="SELECT CONCAT('Service',' - ',name) as name FROM `service` where id='$id'";	
			}else if(EXPLODE(",",$get_id)[0] == 'pr'){
			$sql ="SELECT CONCAT('Product',' - ',name) as name FROM `products` where id='$id'";	
			}else if(EXPLODE(",",$get_id)[0] == 'pa'){
			$sql ="SELECT CONCAT('Package',' - ',name) as name FROM `packages` where id='$id' and branch_id='".$_SESSION['branch_id']."'";	
			}else if(EXPLODE(",",$get_id)[0] == 'mem'){
			$sql ="SELECT CONCAT('Membership',' - ',membership_name) as name FROM `membership_discount` where id='$id'";	
			}
		$result=query_by_id($sql,[],$conn);
		foreach($result as $row) {
			return $row['name'];
		}
	}
?>
<script type="text/javascript">
	function getList(type){
		if(type != ''){
			$.ajax({
				url : 'ajax/report-ajax.php',
				method : 'POST',
				data : {'action':'getlist', type : type },
				success : function(res){
					if(res != ''){
						var html = '<div class="col-lg-2 col-md-2 col-sm-2 col-xs-12">'+
    									'<div class="form-group">';
    									if(type == 'Service'){
    										html += '<label for="date">Select service</label>';
    									} else if(type == 'Product'){
    										html += '<label for="date">Select product</label>';
    									} else if(type == 'Package'){
    										html += '<label for="date">Select package</label>';
    									} else if(type == 'mem'){
    										html += '<label for="date">Select Membership</label>';
    									}

    								html += '<select id="s_type" class="form-control">'+
    										res
    										'</select>'+	
    									'</div>'+
    								'</div>';
    					$('#append_div').html(html);
					} else {
						$('#append_div').html('');
					}
				}
			});
		} else {
			$('#append_div').html('');
		}
	}

	function filterDailyreport(){
		var daterange = $('#daterange').val();
		var date = daterange.split("-");
		var service_type = $('#service_type').val();
		var app = '';
		if(service_type != ''){
			var stype = $('#s_type').val();
			if(stype != ''){
				app += '&service_type='+service_type+'&stype='+stype;
			} else {
				app += '&service_type='+service_type;
			}
		}
		if(daterange == ''){
			var from = '';
			var to = '';
		} else {
			var from = isoDate(date[0]);
			var to = isoDate(date[1]);
		}
		window.location.href = '?sdate='+from+'&edate='+to+app;
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