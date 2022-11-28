<?php 
	include_once './includes/db_include.php';
	$branch_id = $_SESSION['branch_id'];
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
						<h4 class="pull-left">Product sales report</h4>
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
				        ?>
						    <a href="exportdata/product-sale-report.php?<?= $url_append ?>" target="_blank">
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
    										<label for="date">Select product</label>
    										<select id="product" class="form-control">
    											<option value="">--Select--</option>
    											
    										</select>	
    									</div>
    								</div>
    								<div class="col-lg-2 col-md-2 col-sm-2 col-xs-12">
    									<div class="form-group">
    										<label for="date">Select client</label>
    										<select id="client" class="form-control">
    											<option value="">--Select--</option>
    											
    										</select>	
    									</div>
    								</div>
    								<div class="col-md-3 col-md-3 col-sm-3 col-xs-12">
    								    <lable>&nbsp;</lable>
    								    <div class="form-group">
    								        <button class="btn btn-filter btn-sm" onclick="filterDailyreport()"><i class="fa fa-filter" aria-hidden="true"></i>Filter</button>&nbsp;&nbsp;&nbsp;&nbsp;<button class="btn btn-danger btn-sm" onclick="window.location.href='product-sale-report.php'"><i class="fa fa-times" aria-hidden="true"></i>Clear</button>
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
					            $app_query = '';
					            if(isset($_GET['client'])){
					                $append_query = " AND client='".$_GET['client']."' " ;
					            }

					            if(isset($_GET['product'])){
					                $app_query = " AND service='".$_GET['product']."' " ;
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
							    				<th>Product name</th>
							    				<th>Unit</th>
							    				<th>Unit price</th>
							    				<th>Qty</th>
							    				<th>Total</th>							    				
							    				<th>Discount</th>
							    				<th>Subtotal</th>
							    				<?php if(!isset($_GET['client']) && !isset($_GET['product'])){ ?>
							    				<th>Tax</th>
							    				<th>Grand total</th>
							    				<th>Payment method</th>
							    				<th>Sold by</th>
							    				<?php } ?>
							    			</tr>
							    		</thead>
							    		<tbody>
							    			<?php
							    			$total_unit_price = 0;
							    			$total_qty = 0;
							    			$total_amount = 0;
							    			$total_discount = 0;
							    			$total_subtotal = 0;
							    			$in_tax = 0;
							    			$ex_tax = 0;
							    			$total_grand_total = 0;							    			
						    				for($i=$sdate; $i<=$edate; $i+=86400){
						    					$date = date("Y-m-d", $i);
						    					$data = query_by_id("SELECT iid as inv_id FROM invoice_items_$branch_id WHERE date(start_time) = '".$date."' AND active = '0' AND type='Product' $append_query GROUP BY iid ",[],$conn);
						    					if($data){
						    						foreach($data as $ser){
						    							$pdata = query_by_id("SELECT id, client, service, quantity, disc_row, actual_price, price FROM invoice_items_$branch_id WHERE iid='".$ser['inv_id']."' AND type='Product' $app_query ",[],$conn);
														$count = 1;
														$invoice_amount = 0;  
						    							if($pdata){
						    								$rowspan = count($pdata);	
						    								foreach($pdata as $product){
						    									$invoice_amount = $invoice_amount+($product['actual_price']*$product['quantity']);
						    								} 
						    								foreach($pdata as $product){ 
						    									$product_name = product_name($product['service']);
						    									$pid = explode(',', $product['service'])[1];
						    									$unit = query_by_id("SELECT p.volume, u.name FROM products p LEFT JOIN units u ON u.id = p.unit WHERE p.id='".$pid."'",[],$conn)[0];	
						    								?>
<tr>
<?php if($count == 1){ ?>
<td rowspan="<?php echo $rowspan ?>"><?php echo my_date_format($date) ?></td>
<td rowspan="<?php echo $rowspan ?>"><?php echo $ser['inv_id'] ?></td>
<td rowspan="<?php echo $rowspan ?>"><?php 
	$client = query_by_id("SELECT name, cont FROM client WHERE id='".$product['client']."'",[],$conn)[0];
	echo $client['name']." (".$client['cont'].")";
?></td>
<?php } ?>
<td><?php echo $product_name; ?></td>
<td><?php echo $unit['volume']." ".$unit['name'] ?></td>
<td><?php echo $product['actual_price']; $total_unit_price += $product['actual_price']; ?></td>
<td><?php echo $product['quantity']; $total_qty += $product['quantity']; ?></td>
<td><?php echo number_format($product['actual_price']*$product['quantity'],2); $total_amount += ($product['actual_price']*$product['quantity']); ?></td>
<?php if($count == 1){ ?>
<td rowspan="<?php echo $rowspan ?>"><?php echo number_format(invoice_discount($ser['inv_id'], $date),2); $total_discount += invoice_discount($ser['inv_id'], $date); ?></td>
<td rowspan="<?php echo $rowspan ?>"><?php echo number_format($invoice_amount-invoice_discount($ser['inv_id'], $date),2); $total_subtotal += ($invoice_amount-invoice_discount($ser['inv_id'], $date)); ?></td>		    
<?php if(!isset($_GET['client']) && !isset($_GET['product'])){ ?>    	
<td rowspan="<?php echo $rowspan ?>"><?php echo taxcalculation($ser['inv_id'], $date);  $in_tax += taxcalculation($ser['inv_id'], $date, 'inclusive'); $ex_tax += taxcalculation($ser['inv_id'], $date, 'exclusive'); ?></td>
<td rowspan="<?php echo $rowspan ?>"><?php echo number_format(($invoice_amount-invoice_discount($ser['inv_id'], $date))+taxcalculation($ser['inv_id'], $date, 'exclusive'),2); $total_grand_total += ($invoice_amount-invoice_discount($ser['inv_id'], $date))+taxcalculation($ser['inv_id'], $date, 'exclusive'); ?></td>
<td rowspan="<?php echo $rowspan ?>">
	<?php
		$paystatus = array();
    	$paymethod = "SELECT pm.name as name FROM multiple_payment_method mpm LEFT JOIN payment_method pm ON mpm.payment_method = pm.id WHERE invoice_id='bill,".$ser['inv_id']."' and mpm.branch_id='".$branch_id."'";
    	$methodres = query_by_id($paymethod,[],$conn);
    	if($methodres){
        	foreach ($methodres as $res) {
        		array_push($paystatus, $res['name']);
        	}
    	}
    	if(count($paystatus) > 0){
    		echo implode(', ', $paystatus);
    	} else {
    		echo '-';
    	}
	?>
</td>
<?php } } ?>
<?php if(!isset($_GET['client']) && !isset($_GET['product'])){ ?>
<td>
	<?php
		$sp = query_by_id("SELECT b.id, b.name, b.cont FROM invoice_multi_service_provider imps LEFT JOIN beauticians b on b.id = imps.service_provider WHERE imps.inv='".$ser['inv_id']."' AND imps.service_name = '".$product['service']."'",[],$conn)[0];
		echo $sp['name']." (".$sp['cont'].")";
	?>
</td>
<?php } ?>
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
							    				<td colspan="4" align="right"><b>Total</b></td>
							    				<td><b></b></td>
							    				<td><b><?php echo number_format($total_unit_price,2) ?></b></td>
							    				<td><b><?php echo $total_qty ?></b></td>
							    				<td><b><?php echo number_format($total_amount,2) ?></b></td>
							    				<td><b><?php echo number_format($total_discount,2) ?></b></td>
							    				<td><b><?php echo number_format($total_subtotal,2) ?></b></td>
							    				<?php if(!isset($_GET['client']) && !isset($_GET['product'])){ ?>
							    				<td><?php
							    					echo "<b>Inclusive : </b>".number_format($in_tax,2)."<br />";
							    					echo "<b>Exclusive : </b>".number_format($ex_tax,2);
							    				?></td>
							    				<td><b><?php echo number_format($total_grand_total,2) ?></b></td>
							    				<td><b></b></td>
							    				<td><b></b></td>
							    				<?php } ?>
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

	$product_array = array();
	$client_array = array();
	for($i=$sdate; $i<=$edate; $i+=86400){
		$date = date("Y-m-d", $i);
		$data = query_by_id("SELECT iid as inv_id FROM invoice_items_$branch_id WHERE date(start_time) = '".$date."' AND active = '0' AND type='Product' GROUP BY iid ",[],$conn);
		if($data){
			foreach($data as $ser){
				$pdata = query_by_id("SELECT id, client, service, quantity, disc_row, actual_price, price FROM invoice_items_$branch_id WHERE iid='".$ser['inv_id']."' and type='Product' ",[],$conn); 
				$count = 1;
				if($pdata){
					foreach($pdata as $product){ 
						if($count == 1){
							$client = query_by_id("SELECT name, cont FROM client WHERE id='".$product['client']."'",[],$conn)[0];
							array_push($client_array, [$client['name']." (".$client['cont'].")" => $product['client']]);
						}
						$product_name = product_name($product['service']);
						array_push($product_array, [$product_name." (".$unit['volume']." ".$unit['name'].")" => $product['service']]);
						$count++;	
					}
				}
			}
		}
	}

	$c = ''; $pd = '';
	$products = array_unique($product_array, SORT_REGULAR);

	if(isset($_GET['product']) && $_GET['product'] != ''){
		$pd = $_GET['product'];
	}

	if(isset($_GET['client']) && $_GET['client'] > 0){
		$c = $_GET['client'];
	}

	if(count($products) > 0){
		foreach($products as $pr){
			foreach ($pr as $key => $value) {
			?>
				<script>
					$('#product').append('<option <?= $pd==$value?"selected":"" ?> value="<?php echo $value ?>"><?php echo $key ?></option>');
				</script>
			<?php
			}
		}
	}

	$clients = array_unique($client_array, SORT_REGULAR);
	if(count($clients) > 0){
		foreach($clients as $cl){
			foreach ($cl as $key => $value) {
			?>
				<script>
					$('#client').append('<option <?= $c==$value?"selected":"" ?> value="<?php echo $value ?>"><?php echo $key ?></option>');
				</script>
			<?php
			}
		}
	}

	function product_name($get_id){
		global $conn;
		$id = EXPLODE(",",$get_id)[1];
		if(EXPLODE(",",$get_id)[0] == 'pr'){
			$sql ="SELECT name FROM `products` where id='$id'";	
		}
		$result=query_by_id($sql,[],$conn);
		foreach($result as $row) {
			return $row['name'];
		}
	}

	function taxcalculation($id, $date, $is_exce = null){
	    global $branch_id;
	    global $conn;
	    if($is_exce == 'exclusive'){
	    	$exl_tax = 0;
	    	$check_tax = query_by_id("SELECT id, tax, taxtype, dis, subtotal  FROM invoice_$branch_id WHERE doa='".$date."' AND branch_id='".$branch_id."' AND id='".$id."'",[],$conn);
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
	    	$check_tax = query_by_id("SELECT id, tax, taxtype, dis, subtotal  FROM invoice_$branch_id WHERE doa='".$date."' AND branch_id='".$branch_id."' AND id='".$id."'",[],$conn);
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
		    $check_tax = query_by_id("SELECT id, tax, taxtype, dis, subtotal  FROM invoice_$branch_id WHERE doa='".$date."' AND branch_id='".$branch_id."' AND id='".$id."'",[],$conn);
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
        $sql="SELECT paid, dis, subtotal, total, id from `invoice_".$branch_id."` where doa='$date' and active=0 and branch_id='".$branch_id."' and id='".$id."'";
        $sql2="SELECT coupons.* FROM coupons LEFT JOIN invoice_".$branch_id." invoice ON coupons.id = invoice.coupon WHERE invoice.doa='$date' and invoice.active=0 and invoice.branch_id='".$branch_id."' and invoice.id='".$id."'";
		global $conn;
		$result=query_by_id($sql,[],$conn);
		$result2=query_by_id($sql2,[],$conn);
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
		
		if($result2){
		    foreach($result2 as $res){
		        $total = $total - $res['discount'];
		    }
		}
		return $total;
	}

	function invoice_discount($id, $date){
		global $branch_id;
	    $total = 0;   
        $sql="SELECT paid, dis, subtotal, total, id from `invoice_".$branch_id."` where doa='$date' and id='".$id."' and active=0 and branch_id='".$branch_id."'";
        $sql2="SELECT coupons.* FROM coupons LEFT JOIN invoice_".$branch_id." invoice ON coupons.id = invoice.coupon WHERE invoice.doa='$date' and invoice.id='".$id."' and invoice.active=0 and invoice.branch_id='".$branch_id."'";
		global $conn;
		$result=query_by_id($sql,[],$conn);
		$result2=query_by_id($sql2,[],$conn);
		if($result){
		    foreach($result as $res){		
		    	$items = query_by_id("SELECT actual_price, price, quantity FROM `invoice_items_".$branch_id."` WHERE iid='".$res['id']."'",[],$conn);
		        foreach($items as $item){
		            $total = $total+(($item['actual_price']*$item['quantity'])-$item['price']);
		        }
			       
		        $discount = explode(",",$res['dis']);
		        if($discount['0'] == CURRENCY){
		            $total = $total + $discount['1'];
		        } else if($discount['0'] == 'pr'){
		            if($discount['1'] != 0){
			            $dis_price = ($res['subtotal']/100)*$discount['1'];
			            $total = $total+$dis_price;
		            }
		        }
	        } 
	    }
		
		if($result2){
		    foreach($result2 as $res){
		        $total = $total + $res['discount'];
		    }
		}
		return $total;
	}
?>
<script type="text/javascript">

	function filterDailyreport(){
		var daterange = $('#daterange').val();
		var date = daterange.split("-");
		var product = $('#product').val();
		var provider = $('#provider').val();
		var client = $('#client').val();
		var app = '';
		if(product != ''){
			app += '&product='+product;
		}
		if(client != ''){
			app += '&client='+client;
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