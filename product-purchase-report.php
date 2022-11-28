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
	// ini_set('display_errors', 1);
	// ini_set('display_startup_errors', 1);
	// error_reporting(E_ALL);
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
						<h4 class="pull-left">Product purchase report</h4>
						<?php
							if(isset($_GET['sdate'])){
				                $from = $_GET['sdate'];
				            } else {
								$from = date('Y-m-d');
				            }
				            if(isset($_GET['edate'])){
				                $to = $_GET['edate'];
				            } else {
				            	$to = date('Y-m-d');
				            }

				            if(isset($_GET['vendor']) && $_GET['vendor'] > 0){
				            	$vid = ' AND vendor="'.$_GET['vendor'].'" ';
				            } else {
				            	$vid = '';
				            }
				        ?>
						    <a href="exportdata/product-purchase-report.php?sdate=<?= $from ?>&edate=<?= $to ?>&tax_type=<?= $ttype ?>" target="_blank">
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
    								<?php
    									$vendors = query_by_id("SELECT id, name, cont FROM vendor WHERE active='0' AND branch_id='".$branch_id."' ORDER BY name ASC",[],$conn);
    								?>
    								<div class="col-lg-2 col-md-2 col-sm-2 col-xs-12">
    									<div class="form-group">
    										<label for="date">Select vendor</label>
    										<select id="vendor" class="form-control">
    											<option value="">--Select--</option>
    											<?php
    												if($vendors){
    													foreach($vendors as $vendor){ ?>
    														<option <?= isset($_GET['vendor'])&&$_GET['vendor']==$vendor['id']?'selected':'' ?> value="<?= $vendor['id'] ?>"><?= $vendor['name'] ?> (<?= $vendor['cont'] ?>)</option>
    												<?php	}
    												}
    											?>
    										</select>    										
    									</div>
    								</div>
    								<div class="col-md-3 col-md-3 col-sm-3 col-xs-12">
    								    <lable>&nbsp;</lable>
    								    <div class="form-group">
    								        <button class="btn btn-filter btn-sm" onclick="filterDailyreport()"><i class="fa fa-filter" aria-hidden="true"></i>Filter</button>&nbsp;&nbsp;&nbsp;&nbsp;<button class="btn btn-danger btn-sm" onclick="window.location.href='product-purchase-report.php'"><i class="fa fa-times" aria-hidden="true"></i>Clear</button>
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
							    	<table class="table table-stripped table-bordered">
							    		<thead>
							    			<tr>
							    				<th class="text-center">Purchase date</th>
							    				<th class="text-center">Invoice</th>
							    				<th>Vendor name</th>
							    				<th>Product name</th>							    				
							    				<th>Unit</th>							    				
							    				<th class="text-center">Price</th>
							    				<th class="text-center">Quantity</th>
							    				<th class="text-center">Amount</th>			    				
							    				<th class="text-center">Discount</th>
							    				<th>Tax</th>
							    				<th class="text-center">Net amount</th>
							    				<th class="text-center">Shipping charges</th>	
							    				<th class="text-center">Total price</th>
							    				<th class="text-center">Paid</th>
							    				<th class="text-center">Due</th>
							    				<th>Payment method</th>
							    			</tr>
							    		</thead>
							    		<tbody>
							    		<?php
							    			$total_rows = 0;
							    			for($i=$sdate; $i<=$edate; $i+=86400){
												$date = date("Y-m-d", $i);
												$data = query_by_id("SELECT id, inv, vendor, dis, pay_method, tax, taxtype, total, subtotal, paid, due, notes, dop, ship FROM purchase WHERE dop='".$date."' AND branch_id='".$branch_id."' $vid ",[],$conn);
												if($data){
													$total_rows += count($data);
													foreach ($data as $data) {
														$products = query_by_id("SELECT product, product_id, quantity, volume, unit, price, price, sale_price FROM purchase_items WHERE iid='".$data['id']."'",[],$conn);
														if($products){
															$rowspan = count($products);
															$count = 1;
															foreach ($products as $list) {										
																echo '<tr>';
																	if($count == 1){
																		echo '<td align="center" style="vertical-align:middle;" rowspan="'.$rowspan.'">'.my_date_format($data['dop']).'</td>';
																		echo '<td align="center" style="vertical-align:middle;"  rowspan="'.$rowspan.'">'.$data['id'].'</td>';
																		echo '<td style="vertical-align:middle;"  rowspan="'.$rowspan.'">'.vendor_name($data['vendor']).'</td>';
																	}
																	echo '<td>'.product_name($list['product_id']).'</td>';
																	echo '<td>'.$list['volume']." ".unit_name($list['unit']).'</td>';
																	echo '<td align="center">'.number_format($list['price'],2).'</td>';	
																	echo '<td align="center">'.$list['quantity'].'</td>';	
																	echo '<td align="center">'.number_format(($list['price']*$list['quantity']),2).'</td>';		
																	if($count == 1){ ?>
																		
																		<?php
																		echo '<td style="vertical-align:middle;"  rowspan="'.$rowspan.'" align="center">'.number_format($data['subtotal']-get_discount_invoice($data['id'], $date),2).'</td>';
																		?>
																		<td style="vertical-align:middle;" rowspan="<?= $rowspan ?>"><?php echo taxcalculation($date,'',$data['id']) ?></td>
																		<?php
																		echo '<td style="vertical-align:middle;"  rowspan="'.$rowspan.'" align="center">'.number_format(get_discount_invoice($data['id'], $date),2).'</td>';	
																		echo '<td style="vertical-align:middle;"  rowspan="'.$rowspan.'" align="center">'.number_format($data['ship'],2).'</td>';		
																		echo '<td align="center" style="vertical-align:middle;"  rowspan="'.$rowspan.'">'.number_format($data['total'], 2).'</td>';
																		echo '<td align="center" style="vertical-align:middle;"  rowspan="'.$rowspan.'">'.number_format($data['paid'], 2).'</td>';
																		echo '<td align="center" style="vertical-align:middle;"  rowspan="'.$rowspan.'">'.number_format($data['due'], 2).'</td>';
																		echo '<td style="vertical-align:middle;"  rowspan="'.$rowspan.'">'.pay_method_name($data['pay_method']).'</td>';
																	}

																echo '</tr>';
																$count++;
															}
														}
													}
												}
											}
											if($total_rows == 0){
							    				echo "<tr>";
							    					echo "<td colspan='16' class='text-center'>No record found!!</td>";
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

	function vendor_name($id){
		global $conn;
		$name = query_by_id("SELECT name FROM vendor WHERE id='".$id."'",[],$conn)[0]['name'];
		return $name;
	}

	function product_name($id){
		global $conn;
		$product_name = query_by_id("SELECT name FROM products WHERE id='".$id."'",[],$conn)[0]['name'];
		return $product_name;
	}

	function unit_name($id){
		global $conn;
		$unit_name = query_by_id("SELECT name FROM units WHERE id='".$id."'",[],$conn)[0]['name'];
		return $unit_name;
	}

	function taxcalculation($date, $is_exce = null, $invid){
	    global $branch_id;
	    global $conn;
	    if($is_exce == 'exclusive'){
	    	$exl_tax = 0;
	    	$check_tax = query_by_id("SELECT id, tax, taxtype, dis, subtotal  FROM purchase WHERE dop='".$date."' AND id='".$invid."' AND branch_id='".$branch_id."'",[],$conn);
		    foreach($check_tax as $tax){
		       $gst_amount = get_discount_invoice($tax['id'], $date);
		       if($tax['taxtype'] == '1'){
		           $tx = query_by_id("SELECT t.tax FROM tax t WHERE id='".$tax['tax']."' and active='0'",[],$conn)[0];
		           $exl_tax += ($gst_amount*$tx['tax']/100);
		       }
		    }
		    return $exl_tax;
	    } else if($is_exce == 'inclusive'){
	    	$inc_tax = 0;
	    	$check_tax = query_by_id("SELECT id, tax, taxtype, dis, subtotal  FROM purchase WHERE dop='".$date."' AND id='".$invid."' AND branch_id='".$branch_id."'",[],$conn);
		    foreach($check_tax as $tax){
		       $gst_amount = get_discount_invoice($tax['id'], $date);
		       if($tax['taxtype'] == '0'){
		           $tx = query_by_id("SELECT t.tax FROM tax t WHERE id='".$tax['tax']."' and active='0'",[],$conn)[0];
		           $inc_tax += ($gst_amount-($gst_amount/($tx['tax']+100))*100);
		       }
		    }
		    return $inc_tax;
	    } else {
		    $inc_tax = 0;
		    $exl_tax = 0;
		    $check_tax = query_by_id("SELECT id, tax, taxtype, dis, subtotal  FROM purchase WHERE dop='".$date."' AND id='".$invid."' AND branch_id='".$branch_id."'",[],$conn);
		    foreach($check_tax as $tax){
		       $gst_amount = get_discount_invoice($tax['id'], $date);
		       if($tax['taxtype'] == '0'){
		           $tx = query_by_id("SELECT t.tax FROM tax t WHERE id='".$tax['tax']."' and active='0'",[],$conn)[0];
		           $inc_tax += ($gst_amount-($gst_amount/($tx['tax']+100))*100);
		       } else if($tax['taxtype'] == '1'){
		           $tx = query_by_id("SELECT t.tax FROM tax t WHERE id='".$tax['tax']."' and active='0'",[],$conn)[0];
		           $exl_tax += ($gst_amount*$tx['tax']/100);
		       }
		    }
		    
		    echo '<strong>Inclusive : </strong>'.number_format($inc_tax,2)."<br />";
		    echo '<strong>Exclusive : </strong>'.number_format($exl_tax,2);
		}
	}

	function get_discount_invoice($id, $date){
		global $branch_id;
	    $total = 0;
        $sql="SELECT paid, dis, subtotal, total, id from purchase where dop='$date' and active=0 and branch_id='".$branch_id."' and id='".$id."'";
		global $conn;
		$result=query_by_id($sql,[],$conn);
		if($result){
		    foreach($result as $res){
		        $discount = explode(",",$res['dis']);
		        if($discount['0'] == CURRENCY){
		            $total = $res['subtotal'] - $discount['1'];
		        } else if($discount['0'] == 'pr'){
		            if($discount['1'] != 0){
    		            $dis_price = ($res['subtotal']*$discount['1'])/100;
    		            $total = $res['subtotal']-$dis_price;
		            } else {
		                $total = $res['subtotal'];
		            }
		        }
		    }
		}
		return $total;
	}
?>
<script>
	function filterDailyreport(){
		var daterange = $('#daterange').val();
		var date = daterange.split("-");
		var vendor = $('#vendor').val();
		if(daterange == ''){
			var from = '';
			var to = '';
		} else {
			var from = isoDate(date[0]);
			var to = isoDate(date[1]);
		}
		window.location.href = '?sdate='+from+'&edate='+to+'&vendor='+vendor;
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