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
						<h4 class="pull-left">Membership report</h4>
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

							if(isset($_GET['membership'])){
					            $url_append .= '&membership='.$_GET['membership'];
				            }

				            if(isset($_GET['mem_type'])){
					            $url_append .= '&mem_type='.$_GET['mem_type'];
				            }				            

				        ?>
					    <a href="exportdata/membership-report.php?<?= $url_append ?>" target="_blank">
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
    										<label>Membership</label>
    										<select id="membership" class="form-control">
    											<option value="">--Select--</option>
    											<?php
    												$mem = query_by_id("SELECT DISTINCT md_id FROM membership_discount_history",[],$conn);
    												if($mem){
    													foreach($mem as $membership){
    														$q = query_by_id("SELECT membership_name as name FROM membership_discount WHERE id='".$membership['md_id']."' order by name asc ",[],$conn)[0];

    														?>
    														<option <?= isset($_GET['membership'])&&$_GET['membership']==$membership['md_id']?'selected':'' ?> value='<?= $membership['md_id'] ?>'><?= $q['name'] ?></option>
    													<?php }
    												}	
    											?>
    										</select>
    									</div>
    								</div>
    								<div class="col-lg-2 col-md-2 col-sm-2 col-xs-12">
    									<div class="form-group">
    										<label>Client</label>
    										<select id="client" class="form-control">
    											<option value="">--Select--</option>
    											<?php
    												$mem_client = query_by_id("SELECT DISTINCT client_id FROM membership_discount_history",[],$conn);
    												if($mem_client){
    													foreach($mem_client as $client){
    														?>
    														<option <?= isset($_GET['client'])&&$_GET['client']==$client['client_id']?'selected':'' ?> value='<?= $client['client_id'] ?>'><?= 	get_client_name($client['client_id'],$conn,'fullname') ?></option>
    													<?php }
    												}
    											?>
    										</select>
    									</div>
    								</div>
    								<div class="col-lg-2 col-md-2 col-sm-2 col-xs-12">
    									<div class="form-group">
    										<label>Membership type</label>
    										<select id="mem_type" class="form-control">
    											<option value="">--Select--</option>
    											<option value="1" <?= isset($_GET['mem_type'])&&$_GET['mem_type']==1?'selected':'' ?>>Active</option>
    											<option value="2" <?= isset($_GET['mem_type'])&&$_GET['mem_type']==2?'selected':'' ?>>Expired</option>
    										</select>
    									</div>
    								</div>
    								<div class="col-md-2">
    								    <lable>&nbsp;</lable>
    								    <div class="form-group">
    								        <button class="btn btn-filter btn-sm" onclick="filterDailyreport()"><i class="fa fa-filter" aria-hidden="true"></i>Filter</button>&nbsp;&nbsp;&nbsp;&nbsp;<button class="btn btn-danger btn-sm" onclick="window.location.href='membership-report.php'"><i class="fa fa-times" aria-hidden="true"></i>Clear</button>
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
					        ?>
					        <div class="col-md-12">
						    	<div class="table-responsive">
							    	<table class="table table-stripped table-bordered" id="myTable">
							    		<thead>
							    			<tr>
							    				<th>Bill date</th>	
							    				<th>Invoice id</th>	
							    				<th>Client name</th>
							    				<th>Contact number</th>			    				
							    				<th>Membership name</th>							    				
							    				<th>Amount</th>
							    				<th>Expiry date</th>	
							    				<th>Status</th>						    				
							    			</tr>
							    		</thead>
							    		<tbody>
							    		<?php
							    			$total_rows = 0;

							    			$ap_query = '';
							    			$mem_types = 0;

							    			if(isset($_GET['membership']) && $_GET['membership'] != ''){
							    				$ap_query .= ' AND mdh.md_id="'.$_GET['membership'].'" ';
							    			}

							    			if(isset($_GET['client']) && $_GET['client'] != ''){
							    				$ap_query .= ' AND mdh.client_id="'.$_GET['client'].'" ';
							    			}

							    			if(isset($_GET['mem_type']) && $_GET['mem_type'] != ''){
							    				$mem_types = $_GET['mem_type'];
							    			}

							    			if($ap_query != '' || $mem_types != 0){
							    				$data = query_by_id("SELECT mdh.*, i.doa, i.paid, md.membership_name as name, md.validity, md.membership_price FROM membership_discount_history mdh"
						                                ." LEFT JOIN membership_discount md ON md.id = mdh.md_id"
						                                ." LEFT JOIN invoice_$branch_id i ON i.id=mdh.invoice_id "
						                                ." LEFT JOIN client c ON c.id = client_id"
						                                ." WHERE 1=1 $ap_query ORDER BY mdh.invoice_id DESC",[],$conn);
							    				if($data){							    					
							    					foreach($data as $memdata){
							    						$package_expiry_date = my_date_format(date('Y-m-d', strtotime(date('Y-m-d',strtotime($memdata['doa'])). ' + '.$memdata['validity'].' days')));
							    						if($mem_types == 2){
							    							if(strtotime($package_expiry_date) > strtotime(date('Y-m-d'))){
							    								continue;
								    						}
								    					}
							    						?>
							    						<tr>
							    							<td><?php echo my_date_format($memdata['time_update']); ?></td>
							    							<td><?php echo $memdata['invoice_id'] ?></td>
							    							<td><?php echo query_by_id("SELECT name FROM client WHERE id='".$memdata['client_id']."'",[],$conn)[0]['name']; ?></td>
							    							<td><?php echo query_by_id("SELECT cont FROM client WHERE id='".$memdata['client_id']."'",[],$conn)[0]['cont']; ?></td>
							    							<td><?php echo $memdata['name'] ?></td>
							    							<td><?php echo $memdata['membership_price'] ?></td>
							    							<td><?php 							    						
        														if(strtotime($package_expiry_date) > strtotime(date('Y-m-d'))){
		                											echo $package_expiry_date;
		                										}
							    							 ?></td>
							    							<td>
							    								<?php
							    									if(strtotime($package_expiry_date) > strtotime(date('Y-m-d'))){
							    										echo "<lable class='badge badge-success'>Active</label>";
							    									} else {
							    										echo "<lable class='badge badge-danger'>Expired</label>";
							    									}
							    								?>
							    							</td>
							    						</tr>
							    						<?php
							    						$total_rows++;
							    					}
							    				}
							    			}
							    			else{
								    			for($i=$sdate; $i<=$edate; $i+=86400){
							    					$date = date("Y-m-d", $i);
								    				$data = query_by_id("SELECT mdh.*, i.doa, i.paid, md.membership_name as name, md.validity, md.membership_price FROM membership_discount_history mdh"
						                                ." LEFT JOIN membership_discount md ON md.id = mdh.md_id"
						                                ." LEFT JOIN invoice_$branch_id i ON i.id=mdh.invoice_id "
						                                ." LEFT JOIN client c ON c.id = client_id"
						                                ." WHERE date(mdh.time_update)='".$date."' ORDER BY mdh.invoice_id DESC",[],$conn);
								    				if($data){							    					
								    					foreach($data as $memdata){
								    						?>
								    						<tr>
								    							<td><?php echo my_date_format($memdata['time_update']); ?></td>
								    							<td><?php echo $memdata['invoice_id'] ?></td>
								    							<td><?php echo query_by_id("SELECT name FROM client WHERE id='".$memdata['client_id']."'",[],$conn)[0]['name']; ?></td>
								    							<td><?php echo query_by_id("SELECT cont FROM client WHERE id='".$memdata['client_id']."'",[],$conn)[0]['cont']; ?></td>
								    							<td><?php echo $memdata['name'] ?></td>
								    							<td><?php echo $memdata['membership_price'] ?></td>
								    							<td><?php 
								    								$package_expiry_date = my_date_format(date('Y-m-d', strtotime(date('Y-m-d',strtotime($memdata['doa'])). ' + '.$memdata['validity'].' days')));
	        														if(strtotime($package_expiry_date) > strtotime(date('Y-m-d'))){
			                											echo $package_expiry_date;
			                										}
								    							 ?></td>
								    							<td>
								    								<?php
								    									if(strtotime($package_expiry_date) > strtotime(date('Y-m-d'))){
								    										echo "<lable class='badge badge-success'>Active</label>";
								    									} else {
								    										echo "<lable class='badge badge-danger'>Expired</label>";
								    									}
								    								?>
								    							</td>
								    						</tr>
								    						<?php
								    						$total_rows++;
								    					}
								    				}
								    			}
								    		}
							    			if($total_rows == 0){
							    				echo "<tr>";
							    					echo "<td colspan='8' class='text-center'>No record found!!</td>";
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
?>
<script type="text/javascript">

	function filterDailyreport(){
		var daterange = $('#daterange').val();
		var date = daterange.split("-");
		var membership = $('#membership').val();
		var client = $('#client').val();
		var mem_type = $('#mem_type').val();
		var app = '';
		if(membership != ''){
			app += '&membership='+membership;
		}
		if(client != ''){
			app += '&client='+client;
		}
		if(mem_type != ''){
			app += '&mem_type='+mem_type;
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