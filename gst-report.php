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

	if(isset($_GET['tax_type']) && ($_GET['tax_type'] == '0' || $_GET['tax_type'] == '1')){
		$tax_type = " AND taxtype='".$_GET['tax_type']."' "; 
		$ttype = $_GET['tax_type'];
	} else {
		$tax_type = " AND taxtype!='3' ";
		$ttype = '';
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
						<h4 class="pull-left">GST report</h4>
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
				        ?>
						    <a href="exportdata/gst-report.php?sdate=<?= $from ?>&edate=<?= $to ?>&tax_type=<?= $ttype ?>" target="_blank">
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
    								<div class="col-md-2">
    								    <lable>Tax type</lable>
    								    <select class="form-control" id="tax_type">
    								    	<option value="">--Select--</option>
    								    	<option <?= isset($_GET['tax_type'])&&$_GET['tax_type']=='0'?'selected':''?> value="0">Inclusive</option>
    								    	<option <?= isset($_GET['tax_type'])&&$_GET['tax_type']=='1'?'selected':''?> value="1">Exclusive</option>
    								    </select>
    								</div>
    								<div class="col-md-3 col-md-3 col-sm-3 col-xs-12">
    								    <lable>&nbsp;</lable>
    								    <div class="form-group">
    								        <button class="btn btn-filter btn-sm" onclick="filterDailyreport()"><i class="fa fa-filter" aria-hidden="true"></i>Filter</button>&nbsp;&nbsp;&nbsp;&nbsp;<button class="btn btn-danger btn-sm" onclick="window.location.href='gst-report.php'"><i class="fa fa-times" aria-hidden="true"></i>Clear</button>
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
							    				<th>Bill date</th>	
							    				<th>Invoice id</th>	
							    				<th>Client name</th>
							    				<th>Client number</th>
							    				<th>Bill amount</th>
							    				<th>Payment mode</th>
							    				<th>Inclusive Tax</th>
							    				<th>Exclusive Tax</th>
							    			</tr>
							    		</thead>
							    		<tbody>
							    			<?php
							    				$tax_inc = 0;
							    				$tax_exc = 0;
							    				$total_amount = 0;
							    				for($i=$sdate; $i<=$edate; $i+=86400){
													$date = date("Y-m-d", $i);
$sql = "SELECT id as billid, 'invoice' as billtype, client as cid, doa as invoice_date from invoice_".$branch_id." where doa='$date' and active=0 and branch_id='".$branch_id."' $tax_type ORDER BY invoice_date DESC";

$inv = query_by_id($sql,[],$conn);
if($inv){
	foreach($inv as $data){
		$client = query_by_id("SELECT * FROM client WHERE id='".$data['cid']."'",[],$conn)[0];
		?>
		<tr>
			<td><?= my_date_format($date) ?></td>
			<td><a href="invoice.php?inv=<?= $data['billid'] ?>" target="_blank"><u><?= $data['billid'] ?></u></a></td>
			<td><?= $client['name'] ?></td>
			<td><?= $client['cont'] ?></td>     
			<td><?php 
					echo getsales($date, $data['billid'], $data['billtype']);
					$total_amount += getsales($date, $data['billid'], $data['billtype']);
			?></td>                         	
			<td>
				<?php
					$paystatus = array();
			    	$paymethod = "SELECT pm.name as name FROM multiple_payment_method mpm LEFT JOIN payment_method pm ON mpm.payment_method = pm.id WHERE invoice_id='bill,".$data['billid']."' and mpm.branch_id='".$branch_id."'";
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
			<td>
				<?php
					echo number_format(taxcalculation($date, 'inclusive', $data['billid'], $data['billtype']),2); 
					$tax_inc += taxcalculation($date, 'inclusive', $data['billid'], $data['billtype']); 
				?>
			</td>
			<td>
				<?php
					echo number_format(taxcalculation($date, 'exclusive', $data['billid'], $data['billtype']),2); 
					$tax_exc += taxcalculation($date, 'exclusive', $data['billid'], $data['billtype']);
				?>
			</td>
		</tr>
		<?php
	}
}
}
?><tr>
	<td class="text-right" colspan="4"><b>Total</b></td>
	<td><b><?php echo number_format($total_amount, 2); ?>
	<td></td>
	<td><b><?php echo number_format($tax_inc, 2); ?></b></td>
	<td><b><?php echo number_format($tax_exc, 2); ?></b></td>
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
		<!-- Row ends -->
		
	</div>
	<!-- Main container ends -->
	
</div>
<!-- Dashboard Wrapper End -->

</div>
<!-- Container fluid ends -->

<?php 
	include "footer.php";
	
	function getsales($date, $id, $type){
		global $branch_id;
		if($type == 'invoice'){
			$sql="SELECT sum(total) as total from `invoice_".$branch_id."` where doa='$date' and active=0 and branch_id='".$branch_id."' and id='".$id."'";
			global $conn;
			
			$result=query_by_id($sql,[],$conn)[0];
			if($result){
				$total = ($result['total'] > 0)?$result['total']:0;
				return $total;
			}else{
				return 0;
			}
		} else if($type == 'appointment'){
			$sql="SELECT sum(total) as total from `app_invoice_".$branch_id."` where appdate='$date' and active=0 and branch_id='".$branch_id."' and id='".$id."'";
			global $conn;
			
			$result=query_by_id($sql,[],$conn)[0];
			if($result){
				$total = ($result['total'] > 0)?$result['total']:0;
				return $total;
			}else{
				return 0;
			}
		} else if($type == 'wallet'){
			return walletrecharged($date, $id);
		} else if($type == 'pending payment'){
			global $conn;
			$ppr = query_by_id("SELECT sum(pending_payment_received) as pr FROM invoice_pending_payment WHERE pending_paid_date = '".$date."' AND paid_at_branch='".$branch_id."' and id='".$id."' and status='1'",[],$conn)[0]['pr'];
			if($ppr){
				$total = ($ppr > 0)?$ppr:0;
				return $total;
			}else{
				return 0;
			}
		}   
	}
	
	
	function walletrecharged($date, $id){
	    global $branch_id;
	    global $conn;
	    $wallet_amount = 0;
        $amount1 = query_by_id("SELECT paid_amount from `wallet_history` where status=1 and DATE(time_update) = '".$date."' and branch_id='".$branch_id."' and iid='0' and transaction_type='1' and id = '".$id."'",[],$conn);
        $amount2 = query_by_id("SELECT wallet_amount from `wallet_history` where status=1 and DATE(time_update) ='".$date."' and branch_id='".$branch_id."' and iid!='0' and transaction_type='1' and id='".$id."'",[],$conn);
        if($amount1){
	        foreach($amount1 as $a1){
	            $wallet_amount += $a1['paid_amount'];
	        }
	    }
	    if($amount2){
	        foreach($amount2 as $a2){
	            $wallet_amount += $a2['wallet_amount'];
	        }
	    }

		return $wallet_amount;
	}
	
	function taxcalculation($date, $is_exce = null, $id, $type){
	    global $branch_id;
	    global $conn;
	    if($type == 'invoice'){
		    if($is_exce == 'exclusive'){
		    	$exl_tax = 0;
		    	$check_tax = query_by_id("SELECT id, tax, taxtype, dis, subtotal  FROM invoice_$branch_id WHERE doa='".$date."' AND branch_id='".$branch_id."' and id='".$id."'",[],$conn);
			    foreach($check_tax as $tax){
			       $gst_amount = get_discount_invoice($tax['id'], $date, $type);
			       if($tax['taxtype'] == '1'){
			           $tx = query_by_id("SELECT t.tax FROM tax t WHERE id='".$tax['tax']."' and active='0'",[],$conn)[0];
			           $exl_tax += ($gst_amount*$tx['tax']/100);
			       }
			    }
			    return $exl_tax;
		    } else if($is_exce == 'inclusive'){
		    	$inc_tax = 0;
		    	$check_tax = query_by_id("SELECT id, tax, taxtype, dis, subtotal  FROM invoice_$branch_id WHERE doa='".$date."' AND branch_id='".$branch_id."' and id='".$id."'",[],$conn);
			    foreach($check_tax as $tax){
			       $gst_amount = get_discount_invoice($tax['id'], $date, $type);
			       if($tax['taxtype'] == '0'){
			           $tx = query_by_id("SELECT t.tax FROM tax t WHERE id='".$tax['tax']."' and active='0'",[],$conn)[0];
			           $inc_tax += ($gst_amount-($gst_amount/($tx['tax']+100))*100);
			       }
			    }
			    return $inc_tax;
		    } else {

		    }
		}
	}

	function get_discount_invoice($id, $date, $type){
		global $branch_id;
	    $total = 0;
	    if($type == 'invoice'){
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
		} else if($type == 'appointment'){
			$sql="SELECT paid, dis, subtotal, total, id from `app_invoice_".$branch_id."` where appdate='$date' and active=0 and branch_id='".$branch_id."' and id='".$id."'";			
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
		}
		return $total;
	}

	
	?>	
<script>
	function filterDailyreport(){
		var daterange = $('#daterange').val();
		var date = daterange.split("-");
		var tax_type = $('#tax_type').val();
		if(daterange == ''){
			var from = '';
			var to = '';
		} else {
			var from = isoDate(date[0]);
			var to = isoDate(date[1]);
		}
		window.location.href = '?sdate='+from+'&edate='+to+'&tax_type='+tax_type;
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