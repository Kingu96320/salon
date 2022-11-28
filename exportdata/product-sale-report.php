<?php
	include_once '../includes/db_include.php';
	if(isset($_GET['sdate'])){
        $sdate = strtotime($_GET['sdate']);
    }
    if(isset($_GET['edate'])){
        $edate = strtotime($_GET['edate']);
    }

    header("Content-Type: application/xls");    
    header("Content-Disposition: attachment; filename=ProductSaleReport_".$sdate.'-'.$edate.".xls"); 
    header("Pragma: no-cache"); 
    header("Expires: 0");

    $branch_id = $_SESSION['branch_id'];

    $append_query = ''; 
    $app_query = '';
    if(isset($_GET['client'])){
        $append_query = " AND client='".$_GET['client']."' " ;
    }

    if(isset($_GET['product'])){
        $app_query = " AND service='".$_GET['product']."' " ;
    }	
?>

<table border="1">
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
					$pdata = query_by_id("SELECT id, client, service, quantity, disc_row, actual_price, price FROM invoice_items_$branch_id WHERE iid='".$ser['inv_id']."' $app_query ",[],$conn);
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
<?php
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