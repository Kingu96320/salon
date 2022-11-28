<?php 
	include_once './includes/db_include.php';
	$branch_id = $_SESSION['branch_id'];
	$date = date('Y-m-d');

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

	// $app_services = query_by_id("SELECT ai.id, ai.client, ai.doa, ai.total, ai.subtotal, aii.service, aii.price as item_price FROM app_invoice_".$branch_id." ai LEFT JOIN app_invoice_items_".$branch_id." aii ON aii.iid = ai.id WHERE ai.id = '".$ap['id']."'",[],$conn);

	// $inv = query_by_id("SELECT i.id, i.client, i.doa, i.total, i.subtotal, i.appointment_id, ii.service, ii.price as item_price FROM invoice_".$branch_id." i LEFT JOIN invoice_items_".$branch_id." ii ON ii.iid = i.id WHERE i.doa = '".date('Y-m-d')."' and i.appointment_id > 0 ",[],$conn);

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
						<h4 class="pull-left">Upsell Report</h4>
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

				            if(isset($_GET['client'])){
					            $url_append .= '&client='.$_GET['client'];
				            }
				       
				            if(isset($_GET['sp'])){
					            $url_append .= '&sp='.$_GET['sp'];
				            }
			       
				        ?>
						<a href="exportdata/upsell-report.php?<?= $url_append ?>" target="_blank">
						    <button type="button" class="btn btn-warning pull-right">
						        <i class="fa fa-file-excel-o" aria-hidden="true"></i>Export
						    </button>
						</a>
						<div class="clearfix"></div>
					</div>
					<div class="panel-body"><br />
						<div class="row">
							<div class="col-md-12">
						        <div class="row">
    						        <div class="col-lg-2 col-md-2 col-sm-2 col-xs-12">
    									<div class="form-group">
    										<label for="date">Select dates</label>
    										<input type="text" class="form-control" range-attr="daterange" id="daterange" name="date" value="<?= $start_date.' - '.$end_date ?>"  placeholder="01/01/1990 - 12/05/2000" required readonly>		
    									</div>
    								</div>
    								<div class="col-lg-2 col-md-2 col-sm-2 col-xs-12">
    									<div class="form-group">
    										<label for="date">Client</label>
    										<select id="client" class="form-control">
    											<option value="">--Select--</option>
    										</select>
    									</div>
    								</div>
    								<div class="col-lg-2 col-md-2 col-sm-2 col-xs-12">
    									<div class="form-group">
    										<label for="date">Service provider</label>
    										<select id="service_provider" class="form-control">
    											<option value="">--Select--</option>
    										</select>
    									</div>
    								</div>
    								<div class="col-md-2">
    								    <lable>&nbsp;</lable>
    								    <div class="form-group">
    								        <button class="btn btn-filter btn-sm" onclick="filterDailyreport()"><i class="fa fa-filter" aria-hidden="true"></i>Filter</button>&nbsp;&nbsp;&nbsp;&nbsp;<button class="btn btn-danger btn-sm" onclick="window.location.href='upsell-report.php'"><i class="fa fa-times" aria-hidden="true"></i>Clear</button>
    								    </div>
    								</div>
    							</div>
						    </div>
							<div class="col-md-12">
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
						            if(isset($_GET['client'])){
						                $append_query .= " AND i.client='".$_GET['client']."' " ;
						            }
						            if(isset($_GET['sp'])){
						                $append_query .= " AND imsp.service_provider='".$_GET['sp']."' " ;
						            }
								?>
								<div class="table-responsive">
									<table id="empTable" class="table grid table-bordered no-margin">
										<thead>
											<tr>
												<th style="white-space: nowrap">Date</th>
												<th>Invoice id</th>
												<th>Client name</th>
												<th>Contact number</th>
												<th>Service name</th>
												<th>Service provider</th>
												<th>Amount</th>
												<th>Action</th>
											</tr>
										</thead>
										<tbody>
											<?php
												$total_price = 0;
												for($i=$sdate; $i<=$edate; $i+=86400){
													$date = date("Y-m-d", $i);
													$app = query_by_id("SELECT appointment_id FROM invoice_".$branch_id." WHERE doa='".$date."' and active = '0' and appointment_id > '0'",[],$conn);
													if($app){
														foreach($app as $ap){
															$app_service_list = array();
															$app_services = query_by_id("SELECT ai.id, ai.client, ai.doa, ai.total, ai.subtotal, aii.service, aii.price as item_price FROM app_invoice_".$branch_id." ai LEFT JOIN app_invoice_items_".$branch_id." aii ON aii.iid = ai.id WHERE ai.id = '".$ap['appointment_id']."'",[],$conn);
															if($app_services){
																foreach($app_services as $app_ser){
																	array_push($app_service_list, str_replace(',', '-', $app_ser['service']));
																}
															}

															$inv = query_by_id("SELECT i.id, c.name, c.cont, i.doa, i.appointment_id, ii.service, ii.price as item_price, imsp.service_provider, GROUP_CONCAT(b.name,' ') as spname FROM invoice_".$branch_id." i LEFT JOIN invoice_items_".$branch_id." ii ON ii.iid = i.id LEFT JOIN invoice_multi_service_provider imsp ON imsp.ii_id = ii.id LEFT JOIN client c ON c.id = i.client LEFT JOIN `beauticians` b on b.id=imsp.service_provider WHERE REPLACE(ii.service,',','-') NOT IN ('".implode(',',$app_service_list)."') AND i.appointment_id = '".$ap['appointment_id']."' $append_query  GROUP BY ii_id ",[],$conn);
															$rows = count($inv);
															$count = 1;
															$sp_id = 0;
															if($inv){
																foreach($inv as $in){
																	?>
																	<tr>
																		<?php
																			if($count == 1){ ?>
																				<td style="vertical-align: middle;" rowspan="<?= $rows ?>"><?php echo my_date_format($in['doa']) ?></td>
																				<td style="vertical-align: middle;" rowspan="<?= $rows ?>"><?php echo $in['id'] ?></td>
																				<td style="vertical-align: middle;" rowspan="<?= $rows ?>"><?php echo $in['name'] ?></td>
																				<td style="vertical-align: middle;" rowspan="<?= $rows ?>"><?php echo $in['cont'] ?></td>
																			<?php }
																		?>															
																		<td style="vertical-align: middle;"><?php echo getservice($in['service']) ?></td>
																		<td>
																			<?php
												    					        $sprovider = explode(',',$in['spname']);
												    					        $slast = end($sprovider);
												    					        foreach($sprovider as $sp){
												    					            if(count($sprovider) > 1 && $sp != $slast){
												    					                $cat = ',';
												    					            } else { $cat = ''; }
												    					            echo ucfirst(strtolower($sp)).$cat."<br />";
												    					        }
												    					    ?>
																		</td>
																		<td><?php echo number_format($in['item_price'],2); $total_price += $in['item_price']; ?></td>
																		<td class="hide"></td>
																		<?php
																			if($count == 1){ ?>
																				<td style="vertical-align: middle;" rowspan="<?= $rows ?>"><a href="invoice.php?inv=<?= $in['id'] ?>" target="_blank"><button class="btn btn-info btn-xs" type="button"><i class="fa fa-eye" aria-hidden="true"></i>View</button></a></td>
																			<?php 
																		} ?>
																	</tr>
																	<?php
																	$count++;
																}
															}
														}
													}
												}
											?>
											<tr>
												<td colspan="6" class="text-right"><strong>Total</strong></td>
												<td colspan="2"><strong><?= number_format($total_price,2) ?></strong></td>
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
		<!-- Row ends -->
	</div>
	<!-- Main container ends -->
</div>
<!-- Dashboard Wrapper End -->
</div>
<!-- Container fluid ends -->

<?php 
	include "footer.php"; 

	$client_array = array();
	$provider_array = array();

	for($i=$sdate; $i<=$edate; $i+=86400){
		$date = date("Y-m-d", $i);
		$data = query_by_id("SELECT i.client as client_id, imsp.service_provider  FROM invoice_".$branch_id." i  LEFT JOIN invoice_multi_service_provider imsp ON imsp.inv = i.id LEFT JOIN client c ON c.id = i.client LEFT JOIN `beauticians` b on b.id=imsp.service_provider WHERE i.appointment_id > '0' AND i.doa = '".$date."'",[],$conn);
		if($data){
			foreach($data as $pu){
				array_push($client_array, [get_client_name($pu['client_id'],$conn,'withcontact') => $pu['client_id']]);
				$provider_name = query_by_id("SELECT CONCAT(name, ' ', ' (', cont,') ') as name FROM beauticians WHERE id='".$pu['service_provider']."'",[],$conn)[0]['name'];
				array_push($provider_array, [$provider_name => $pu['service_provider']]);
			}
		}
	}

	if(isset($_GET['client']) && $_GET['client'] != ''){
		$client = $_GET['client'];
	}

	$clients = array_unique($client_array, SORT_REGULAR);
	$clients = array_unique($clients, SORT_REGULAR);

	if(count($clients) > 0){
		foreach($clients as $cl){
			foreach ($cl as $key => $value) {
			?>
				<script>
					$('#client').append('<option <?= $client==$value?"selected":"" ?> value="<?php echo $value ?>"><?php echo $key ?></option>');
				</script>
			<?php
			}
		}
	}

	if(isset($_GET['sp']) && $_GET['sp'] > 0){
		$provider = $_GET['sp'];
	}

	$sp = array_unique($provider_array, SORT_REGULAR);
	if(count($sp) > 0){
		foreach($sp as $sdata){
			foreach ($sdata as $key => $value) {
			?>
				<script>
					$('#service_provider').append('<option <?= $provider==$value?"selected":"" ?> value="<?php echo $value ?>"><?php echo $key ?></option>');
				</script>
			<?php
			}
		}
	}

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
	    	return $row['name'];
	    }
	}
?>

<script type="text/javascript">
	function filterDailyreport(){
		var daterange = $('#daterange').val();
		var date = daterange.split("-");
		var provider = $('#service_provider').val();
		var client = $('#client').val();
		var app = '';
		
		if(client != ''){
			app += '&client='+client;
		}
		if(provider != ''){
			app += '&sp='+provider;
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