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
						<h4 class="pull-left">Product usage report</h4>
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

				            if(isset($_GET['product'])){
					            $url_append .= '&product='.$_GET['product'];
				            }

				            if(isset($_GET['sp'])){
					            $url_append .= '&sp='.$_GET['sp'];
				            }

				            if(isset($_GET['from'])){
					            $url_append .= '&from='.$_GET['from'];
				            }
				        ?>
					    <a href="exportdata/product-usage-report.php?<?= $url_append ?>" target="_blank">
						    <button type="button" class="btn btn-warning pull-right">
						        <i class="fa fa-file-excel-o" aria-hidden="true"></i>Export
						    </button>
						</a>
						<span id="download-btn"></span>					
						<div class="clearfix"></div>
					</div>
					<div class="panel-body">
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
    										<label for="date">Product</label>
    										<select id="product" class="form-control">
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
    								<div class="col-lg-2 col-md-2 col-sm-2 col-xs-12">
    									<div class="form-group">
    										<label for="date">Used from</label>
    										<select id="used_from" class="form-control">
    											<option value="">--Select--</option>
    											<option value="1" <?= isset($_GET['from'])&&$_GET['from']==1?'selected':'' ?>>Product stock</option>
    											<option value="2" <?= isset($_GET['from'])&&$_GET['from']==2?'selected':'' ?>>Assigned stock</option>
    										</select>
    									</div>
    								</div>
    								<div class="col-md-2 col-md-2 col-sm-2 col-xs-12">
    								    <lable>&nbsp;</lable>
    								    <div class="form-group">
    								        <button class="btn btn-filter btn-sm" onclick="filterDailyreport()"><i class="fa fa-filter" aria-hidden="true"></i>Filter</button>&nbsp;&nbsp;&nbsp;&nbsp;<button class="btn btn-danger btn-sm" onclick="window.location.href='product-usage-report.php'"><i class="fa fa-times" aria-hidden="true"></i>Clear</button>
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
					            if(isset($_GET['client'])){
					                $append_query .= " AND i.client='".$_GET['client']."' " ;
					            }
					            if(isset($_GET['product'])){
					                $append_query .= " AND pu.product_id='".$_GET['product']."' " ;
					            }
					            if(isset($_GET['sp'])){
					                $append_query .= " AND pu.service_provider='".$_GET['sp']."' " ;
					            }

					            if(isset($_GET['from'])){
					                $append_query .= " AND pu.type='".$_GET['from']."' " ;
					            }
					        ?>
					        <div class="col-md-12">
						    	<div class="table-responsive">
							    	<table class="table table-stripped table-bordered" id="myTable">
							    		<thead>
							    			<tr>
							    				<th>Bill date</th>	
							    				<th>Invoice id</th>	
							    				<th>Client name</th>
							    				<th>Service name</th>				    				
							    				<th>Product name</th>							    				
							    				<th>Quantity</th>
							    				<th>Unit</th>
							    				<th>Stock id</th>
							    				<th>Service provider</th>
							    				<th>Used From</th>
							    			</tr>
							    		</thead>
							    		<tbody>
							    			<?php
							    			$total_rows = 0;
							    			for($i=$sdate; $i<=$edate; $i+=86400){
						    					$date = date("Y-m-d", $i);
							    				$data = query_by_id("SELECT pu.*, i.doa as bill_date, i.client as client_id FROM product_usage pu LEFT JOIN invoice_".$branch_id." i ON i.id = pu.invoice_id WHERE pu.status='1' AND pu.branch_id='".$branch_id."' and i.doa='".$date."' $append_query GROUP BY pu.invoice_id ORDER BY i.id DESC",[],$conn);
							    				if($data){
							    					$total_rows += count($data);
							    					foreach($data as $pu){
							    						$count = 1;
							    						$pd = query_by_id("SELECT pu.*, i.doa as bill_date, i.client as client_id FROM product_usage pu LEFT JOIN invoice_".$branch_id." i ON i.id = pu.invoice_id WHERE pu.invoice_id='".$pu['invoice_id']."' $append_query ",[],$conn);
							    						if($pd){
							    							$rowspan = count($pd);
							    							foreach($pd as $pr){
							    								?>
									    						<tr>
									    							<?php if($count == 1){ ?>
									    							<td rowspan="<?php echo $rowspan ?>"><?php echo my_date_format($pr['bill_date']) ?></td>
									    							<td class="text-center" rowspan="<?php echo $rowspan ?>"><?php echo $pr['invoice_id'] ?></td>
									    							<td rowspan="<?php echo $rowspan ?>"><?php echo get_client_name($pr['client_id'],$conn,'withcontact') ?></td>
									    							<?php } ?>
									    							<td><?php echo $pr['service_name'] ?></td>
									    							<td><?php echo $pr['product_name'] ?></td>
									    							<td><?php echo $pr['quantity'] ?></td>
									    							<td><?php echo query_by_id("SELECT name FROM units WHERE id='".$pr['unit']."'",[],$conn)[0]['name']; ?></td>
									    							<td><?php echo $pr['stock_id'] ?></td>
									    							<td><?php echo query_by_id("SELECT CONCAT(name, ' ', ' (', cont,') ') as name FROM beauticians WHERE id='".$pr['service_provider']."'",[],$conn)[0]['name'] ?></td>
									    							<td>
									    								<?php 
									    									if($pr['type'] == '1'){
									    										echo "Product stock";
									    									} else if($pr['type'] == '2'){
									    										echo "Assigned stock";
									    									}
									    								?>
									    							</td>
									    						</tr>
							    						<?php
							    						$count++;
							    							}
							    						}					
							    					}
							    				}
							    			}
							    			if($total_rows == 0){
							    				echo "<tr>";
							    					echo "<td colspan='10' class='text-center'>No record found!!</td>";
							    				echo "<tr>";
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
		</div>
	</div>
</div>
</div>
<?php 
	include "footer.php";
	$client_array = array();
	$product_array = array();
	$provider_array = array();

	for($i=$sdate; $i<=$edate; $i+=86400){
		$date = date("Y-m-d", $i);
		$data = query_by_id("SELECT pu.*, i.doa as bill_date, i.client as client_id FROM product_usage pu LEFT JOIN invoice_".$branch_id." i ON i.id = pu.invoice_id WHERE pu.status='1' AND pu.branch_id='".$branch_id."' and i.doa='".$date."' ORDER BY i.id DESC",[],$conn);
		if($data){
			foreach($data as $pu){
				array_push($client_array, [get_client_name($pu['client_id'],$conn,'withcontact') => $pu['client_id']]);
				array_push($product_array, [$pu['product_name'] => $pu['product_id']]);
				$provider_name = query_by_id("SELECT CONCAT(name, ' ', ' (', cont,') ') as name FROM beauticians WHERE id='".$pu['service_provider']."'",[],$conn)[0]['name'];
				array_push($provider_array, [$provider_name => $pu['service_provider']]);
			}
		}
	}

	if(isset($_GET['client']) && $_GET['client'] != ''){
		$client = $_GET['client'];
	}

	$clients = array_unique($client_array, SORT_REGULAR);
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

	if(isset($_GET['product']) && $_GET['product'] > 0){
		$product = $_GET['product'];
	}

	$products = array_unique($product_array, SORT_REGULAR);
	if(count($products) > 0){
		foreach($products as $pr){
			foreach ($pr as $key => $value) {
			?>
				<script>
					$('#product').append('<option <?= $product==$value?"selected":"" ?> value="<?php echo $value ?>"><?php echo $key ?></option>');
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

?>
<script type="text/javascript">

	function filterDailyreport(){
		var daterange = $('#daterange').val();
		var date = daterange.split("-");
		var product = $('#product').val();
		var provider = $('#service_provider').val();
		var client = $('#client').val();
		var from = $('#used_from').val();
		var app = '';
		if(product != ''){
			app += '&product='+product;
		}
		if(client != ''){
			app += '&client='+client;
		}
		if(provider != ''){
			app += '&sp='+provider;
		}
		if(from != ''){
			app += '&from='+from;
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