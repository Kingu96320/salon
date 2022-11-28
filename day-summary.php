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
?>
<script type='text/javascript'>//<![CDATA[
	$(document).on("keyup blur change", '.ser', function() {
		
		var ser = $(".ser").val();
		$.ajax({
			url: "getlist.php",
			type: "POST",
			data: {'ser' : ser},
			success:function(data){
				var ds = JSON.parse(data.trim());
				$('.serr').val(ds[0]['id']);
			},
			error:function (){}
		});
		
	});
</script>

<!-- Dashboard wrapper starts -->
<div class="dashboard-wrapper">
	
	<!-- Main container starts -->
	<div class="main-container">
		
		<!-- Row starts -->
		<div class="row gutter">
			
			<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
				<div class="panel">
					<div class="panel-heading heading-with-btn">
						<h4 class="pull-left">Daily reports</h4>
						    <!--<a href="email-report.php" target="_blank">-->
						    <!--<a href="#" target="_blank">-->
    						<!--    <button type="button" class="btn btn-warning pull-right">-->
    						<!--        <i class="fa fa-file-pdf-o" aria-hidden="true"></i>PDF report-->
    						<!--    </button>-->
    						<!--</a>-->
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
    						        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
    									<div class="form-group">
    										<label for="date">Select dates</label>
    										<input type="text" class="form-control" range-attr="daterange" id="daterange" name="date" value="<?= $start_date.' - '.$end_date ?>"  placeholder="01/01/1990 - 12/05/2000" required readonly>		
    									</div>
    								</div>
    								<div class="col-md-3 col-md-3 col-sm-3 col-xs-12">
    								    <lable>&nbsp;</lable>
    								    <div class="form-group">
    								        <button class="btn btn-filter btn-sm" onclick="filterDailyreport()"><i class="fa fa-filter" aria-hidden="true"></i>Filter</button>&nbsp;&nbsp;&nbsp;&nbsp;<button class="btn btn-danger btn-sm" onclick="window.location.href='dailyreport.php'"><i class="fa fa-times" aria-hidden="true"></i>Clear</button>
    								    </div>
    								</div>
    							</div>
						    </div>
						    <?php
						      //  appointment advance amount collection
					            $sdate = date('Y-m-d');
					            $edate = date('Y-m-d');
					            if(isset($_GET['sdate'])){
					                $sdate = $_GET['sdate'];
					            }
					            if(isset($_GET['edate'])){
					                $edate = $_GET['edate'];
					            }
					            $app_total = 0;
					            $app_advance = query_by_id("SELECT sum(paid) as total FROM `app_invoice_".$branch_id."` WHERE active='0' AND branch_id='".$branch_id."' AND appdate BETWEEN '".$sdate."' AND '".$edate."'",[],$conn)[0];
                        		if($app_advance['total'] > 0){
                        		    $app_total += intval($app_advance['total']);
                        		}
                        		
                        		$common_bill = query_by_id("SELECT sum(paid) as adv FROM `app_invoice_".$branch_id."` WHERE bill_created_status='1' AND active='0' AND branch_id='".$branch_id."' AND appdate BETWEEN '".$sdate."' AND '".$edate."' ",[],$conn)[0];
                        		
                        		if($common_bill['adv'] > 0){
                        		    $app_total -= intval($common_bill['adv']);
                        		}
                        		
                                // pending payment received
                                
                                $pending_payment = 0;
                                $ppr = query_by_id("SELECT sum(pending_payment_received) as pr FROM invoice_pending_payment WHERE pending_paid_date BETWEEN '".$sdate."' AND '".$edate."' AND paid_at_branch='".$branch_id."' and status='1'",[],$conn)[0][pr];
                                $pending_payment = $ppr;
                        		
					        ?>
							<div class="col-lg-12">
								<table class="table table-bordered no-margin" id="mytable">
											<thead>
												<tr>		
													<th width="400">Sales Type</th>
													<th>Amount</th>
												</tr>
												</thead><tbody>
												<tr>
													<td><strong><?php if(isset($_GET['sdate'])){ echo 'Total invoice amount'; } else { echo 'Total invoice amount'; }?></strong></td>
													<td><?php echo number_format(getsales(),2); ?></td>
												</tr>
												<tr>
													<td><strong>Pending payable by clients</strong></td>
													<td><?php 
														$expp = getpendingamount(); echo number_format($expp,2);
													?></td>
												</tr>
												<tr>
												    <td><strong>Total Collection</strong></td>
												    <td><?= number_format((getsales()-getpendingamount())+$pending_payment+$app_total+walletrecharged(),2) ?></td>
												</tr>
												<tr>
												    <td><strong>Product Sales</strong></td>
												    <td><?php echo number_format(getproductsale(),2); ?></td>
												</tr>
												<tr>
													<td><strong>Service Sales</strong></td>
													<td><?php echo number_format(getservicesale(),2); ?></td>
												</tr>
												<tr>
												    <td><strong>Pending payment received</strong></td>
												    <td>
												        <?php
												            echo number_format($pending_payment,2);
												        ?>
												    </td>
												</tr>
												<tr>
												    <td><strong>Appointment advance</strong></td>
												    <td>
												        <?php
												            echo number_format($app_total,2);
												        ?>
												    </td>
												</tr>
												<tr>
												    <td><strong>Wallet re-charged</strong></td>
												    <td><?= number_format(walletrecharged(),2) ?></td>
												</tr>
												<!-- Done -->
												<tr>
													<td><strong>Cash</strong></td>
													<td><?php echo number_format(get_reports('Cash'),2); ?></td>
												</tr>
												<tr>
													<td><strong>Online payment</strong></td>
													<td><?php echo number_format(get_reports('Online_Payment'),2); ?></td>
												</tr>
												<tr>
													<td><strong>Credit/Debit Card</strong></td>
													<td><?php echo number_format(get_reports('Credit/Debit'),2); ?></td>
												</tr>
												<tr>
													<td><strong>Cheque</strong></td>
													<td><?php echo number_format(get_reports('Cheque'),2); ?></td>
												</tr>
												<tr>
													<td><strong>Paid by wallet</strong></td>
													<td><?php echo number_format(get_reports('Mobile_Wallet'),2); ?></td>
												</tr>
												<tr>
													<td><strong>Paytm</strong></td>
													<td><?php echo number_format(get_reports('Paytm'),2); ?></td>
												</tr>
												<tr>
													<td><strong>Gpay</strong></td>
													<td><?php echo number_format(get_reports('Gpay'),2); ?></td>
												</tr>
												<tr>
													<td><strong>PhonePe</strong></td>
													<td><?php echo number_format(get_reports('PhonePe'),2); ?></td>
												</tr>
                                                <tr>
													<td><strong>Paid by Reward points</strong></td>
													<td><?php echo number_format(get_reports('RewardPoint'),2); ?></td>
												</tr>
												<tr>
													<td><strong>Total Discount given</strong></td>
													<td><?php echo number_format(get_discount(),2); ?></td>
												</tr>
												
												<tr>
												    <td><strong>Total TAX</strong></td>
												    <td><?php taxcalculation() ?></td>
												</tr>
											    <tr>
											        <td><strong>Total commisions payable</strong></td>
											        <td><?= number_format(commission_amount(),2) ?></td>
											    </tr>
												<tr>
													<td><strong><?php if(isset($_GET['sdate'])){ echo 'Total'; } else { echo 'Today\'s'; }?> Clients</strong></td>
													<td><?php echo getclients(); ?></td>
												</tr>
												<tr>
													<td><strong><?php if(isset($_GET['sdate'])){ echo 'Total'; } else { echo 'Today\'s'; }?> new clients</strong></td>
													<td><?php echo getnewclients(); ?></td>
												</tr>
												<tr>
													<td><strong>Expenses <?php if(isset($_GET['sdate'])){ echo 'Total'; } else { echo 'Today'; }?></strong></td>
													<td><?php 
														$expp = getexpenses(); echo number_format($expp,2);
														$expense = getexpenses();
													?></td>
												</tr>
												
												<!--<tr>
													<td><strong>Net Earnings</strong></td>
													<td title="Sub Total - (Pending Amonut + Discount + Expenses) + Pending payment">
													<?php 
														$netc = netcollection()+$app_total;
														$ven = vendorpays();
														$dis = get_discount();
														$expp = $expp + $ven;
														$sum = $netc - ($expp+$dis);
														echo $netc." - (".$expp." + ".$dis." + ".round($expense,0).") = ".number_format(($sum-$expense)+$pending_payment,2);
													?>
													</td>
												</tr>-->
											</tbody>
										</table>
							</div>
						</div>
						<script type="text/javascript">
							var table2;
							$(document).ready(function() {
								table2 = $('#mytable').DataTable({
									dom: 'Bfrtip',
									"aaSorting":[],
									"pageLength" : 40,
									buttons: [	{
										extend: 'excelHtml5',
										text: '<i class="fa fa-file-excel-o"></i> Excel',
										titleAttr: 'Export to Excel',
										title: 'Daily Reports',
										exportOptions: {
										// 	columns: ':not(:last-child)',
										}
									},
									{
										extend: 'csvHtml5',
										text: '<i class="fa fa-file-text-o"></i> CSV',
										titleAttr: 'CSV',
										title: 'Daily Reports',
										exportOptions: {
										// 	columns: ':not(:last-child)',
										}
									},
									{
										extend: 'print',
										exportOptions: {
										// 	columns: ':visible'
										},
										
									},
									],
									
								} );
								     var buttons = new $.fn.dataTable.Buttons(table2, {
                        		     buttons: [{
                        					extend: 'excelHtml5',
                        					text: '<i class="fa fa-file-excel-o" aria-hidden="true"></i> Export',
                        					titleAttr: 'Export to Excel',
                        					title: '<?php echo systemname($conn); ?>',
                        					exportOptions: {
                        				// 		columns: ':not(:last-child):not(.not-export-column)',
                        					}
                        				}
                        		    ]
                        		}).container().appendTo($('#download-btn'));
                        
                        		buttons[0].classList.add('d-block');
                        		buttons[0].classList.add('custom-download-btn');
                        		buttons[0].classList.add('pull-right');
                        		buttons[0].classList.remove('dt-buttons');
                        		$('.custom-download-btn a').removeClass('btn-default');
                        		// $('.custom-download-btn a').attr({"data-toggle":"tooltip","data-placement":"top","data-html":"true"});
                        		$('.custom-download-btn a').addClass('btn-warning pull-right download-btn mr-left-5');
							} );
						</script>
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
	function checkcont() {
		var cat = $('#cont').val();
		jQuery.ajax({
			url: "checkenq.php?con="+$("#cont").val(),
			//data:'cat='+$("#bcont").val(),
			type: "POST",
			success:function(data){
				$("#cont-status").html(data);
				//alert(data);
				if ( data.indexOf("Already Exist") > -1 ) {
					$('#cont').val("");
				}
			},
			error:function (){}
		});
	}
</script>

<?php 
	include "footer.php";
	
	// function getuser($user){
	// global $conn;
	// $sql="SELECT * from user where id=$user order by id desc";
	// $result=query_by_id($sql,[],$conn)[0];
	// if ($result) {
	// return $result;
	// }
	// }
	
	function getclient($cid){
		global $conn;
		global $branch_id;
		$sql="SELECT * from client where id=$cid and branch_id='".$branch_id."'";
		$result = mysqli_query($sql,[],$conn)[0];
		if($result) {
			return $result['name'];
		}
	}
	
	function getstaff($sid){
		global $conn;
		global $branch_id;
		$sql="SELECT * FROM `beauticians` where id=$sid and branch_id='".$branch_id."'";
		$result=query_by_id($sql,[],$conn)[0];
		if($result) {
			return $result['name'];
		}
	}
	
	function getcont($cid){
		global $conn;
		global $branch_id;
		$sql="SELECT * from client where id=$cid and branch_id='".$branch_id."'";
		$result=query_by_id($sql,[],$conn)[0];
		if($result) {
			return $result['cont'];
		}
	}
	
	function getitem($id)
	{
		global $conn;
		global $branch_id;
		$str = "";
		$item_id=explode(",",$id)[1];
		$chk_type=explode(",",$id)[0];
		if($chk_type == 'sr'){
			$type = 'Service';
			}else if($chk_type == 'pa'){
			$type = 'Package';
			}else{
			$type = 'Product';
		}
		
		switch ($type) 
		{
			case "Service":
			$sql="SELECT * from `service` where id=$item_id";
			$result=query_by_id($sql,[],$conn);
			foreach($result as $row) 
			{
				$str = $row['name']."(Service)";
			}
			break;
			case "Product":
			$sql="SELECT * from `products` where id=$item_id";
			$result=query_by_id($sql,[],$conn);
			foreach($result as $row) 
			{
				$str = $row['name']."(Product)";
			}
			break;
			case "Package":
			$sql="SELECT * from `packages` where id=$item_id AND branch_id='".$branch_id."'";
			$result=query_by_id($sql,[],$conn);
			foreach($result as $row) {
				$str = $row['name']."(Package)";
			}
			break;
			default:
			$str = "";
			break;
		}
		return $str;
	}
	
	
	function get_reports($type){
	    if(isset($_GET['sdate']) &&isset($_GET['edate'])){
	        $sdate = $_GET['sdate'];
	        $edate = $_GET['edate'];
	        $invquery = "i.doa BETWEEN '$sdate' AND '$edate'";
	        $appquery = "ai.appdate BETWEEN '$sdate' AND '$edate'";
	        $waquery = "DATE(time_update) BETWEEN '$sdate' AND '$edate'";
	    } else {
	        $date = date('Y-m-d');
	        $invquery = "i.doa='$date'";
	        $appquery = "ai.appdate='$date'";
	        $waquery = "DATE(time_update)='".$date."'";
	    }
		
		global $conn;
		global $branch_id;
		switch ($type) 
		{
			case "Cash":
			$wallet_amount = 0;
			$sql="SELECT sum(mpm.amount_paid) as total  from `invoice_".$branch_id."` i LEFT JOIN `multiple_payment_method` mpm on mpm.invoice_id = CONCAT('bill',',',i.id) where $invquery and mpm.payment_method='1' and i.active=0 AND i.branch_id='".$branch_id."' AND mpm.branch_id='".$branch_id."'";
			$result=query_by_id($sql,[],$conn)[0];
			$total_inv = ($result['total'] > 0)?$result['total']:0;
			
			$sql="SELECT sum(mpm.amount_paid) as total  from `app_invoice_".$branch_id."` ai LEFT JOIN `multiple_payment_method` mpm on mpm.invoice_id = CONCAT('app',',',ai.id) where $appquery and mpm.payment_method='1' and ai.active=0 AND ai.branch_id='".$branch_id."' AND mpm.branch_id='".$branch_id."'";
			$result=query_by_id($sql,[],$conn)[0];
			$total_app = ($result['total'] > 0)?$result['total']:0;
			
			$amount1 = query_by_id("SELECT paid_amount from `wallet_history` where status=1 and $waquery  and branch_id='".$branch_id."' and iid='0' and transaction_type='1' and payment_method='1'",[],$conn);
	        $amount2 = query_by_id("SELECT wallet_amount from `wallet_history` where status=1 and $waquery and branch_id='".$branch_id."' and iid !='0' and transaction_type='1' and payment_method='1'",[],$conn);
	        foreach($amount1 as $a1){
	            $wallet_amount += $a1['paid_amount'];
	        }
	        foreach($amount2 as $a2){
	            $wallet_amount += $a2['wallet_amount'];
	        }
			
			$total = $total_inv+$total_app+$wallet_amount;
			break;
			case "Mobile_Wallet":
			$wallet_amount = 0;
			$sql="SELECT sum(mpm.amount_paid) as total  from `invoice_".$branch_id."` i LEFT JOIN `multiple_payment_method` mpm on mpm.invoice_id = CONCAT('bill',',',i.id) where $invquery and mpm.payment_method='7' and i.active=0 AND i.branch_id='".$branch_id."' AND mpm.branch_id='".$branch_id."'";
			$result=query_by_id($sql,[],$conn)[0];
			$total_inv = ($result['total'] > 0)?$result['total']:0;
			
			$sql="SELECT sum(mpm.amount_paid) as total  from `app_invoice_".$branch_id."` ai LEFT JOIN `multiple_payment_method` mpm on mpm.invoice_id = CONCAT('app',',',ai.id) where $appquery and mpm.payment_method='7' and ai.active=0 AND ai.branch_id='".$branch_id."' AND mpm.branch_id='".$branch_id."'";
			$result=query_by_id($sql,[],$conn)[0];
			$total_app = ($result['total'] > 0)?$result['total']:0;
			
			$amount1 = query_by_id("SELECT paid_amount from `wallet_history` where status=1 and $waquery  and branch_id='".$branch_id."' and iid='0' and transaction_type='1' and payment_method='7'",[],$conn);
	        $amount2 = query_by_id("SELECT wallet_amount from `wallet_history` where status=1 and $waquery and branch_id='".$branch_id."' and iid !='0' and transaction_type='1' and payment_method='7'",[],$conn);
	        foreach($amount1 as $a1){
	            $wallet_amount += $a1['paid_amount'];
	        }
	        foreach($amount2 as $a2){
	            $wallet_amount += $a2['wallet_amount'];
	        }
			
			$total = $total_inv+$total_app+$wallet_amount;
			break;
			case "Credit/Debit":
			$wallet_amount = 0;
			$sql="SELECT sum(mpm.amount_paid) as total  from `invoice_".$branch_id."` i LEFT JOIN `multiple_payment_method` mpm on mpm.invoice_id = CONCAT('bill',',',i.id) where $invquery and mpm.payment_method='3' and i.active=0 AND i.branch_id='".$branch_id."' AND mpm.branch_id='".$branch_id."'";
			$result=query_by_id($sql,[],$conn)[0];
			$total_inv = ($result['total'] > 0)?$result['total']:0;
			
			$sql="SELECT sum(mpm.amount_paid) as total  from `app_invoice_".$branch_id."` ai LEFT JOIN `multiple_payment_method` mpm on mpm.invoice_id = CONCAT('app',',',ai.id) where $appquery and mpm.payment_method='3' and ai.active=0 AND ai.branch_id='".$branch_id."' AND mpm.branch_id='".$branch_id."'";
			$result=query_by_id($sql,[],$conn)[0];
			$total_app = ($result['total'] > 0)?$result['total']:0;
			
			$amount1 = query_by_id("SELECT paid_amount from `wallet_history` where status=1 and $waquery  and branch_id='".$branch_id."' and iid='0' and transaction_type='1' and payment_method='3'",[],$conn);
	        $amount2 = query_by_id("SELECT wallet_amount from `wallet_history` where status=1 and $waquery and branch_id='".$branch_id."' and iid !='0' and transaction_type='1' and payment_method='3'",[],$conn);
	        foreach($amount1 as $a1){
	            $wallet_amount += $a1['paid_amount'];
	        }
	        foreach($amount2 as $a2){
	            $wallet_amount += $a2['wallet_amount'];
	        }
			
			$total = $total_inv+$total_app+$wallet_amount;
			break;
			case "Cheque":
			$wallet_amount = 0;
			$sql="SELECT sum(mpm.amount_paid) as total  from `invoice_".$branch_id."` i LEFT JOIN `multiple_payment_method` mpm on mpm.invoice_id = CONCAT('bill',',',i.id) where $invquery and mpm.payment_method='4' and i.active=0 AND i.branch_id='".$branch_id."' AND mpm.branch_id='".$branch_id."'";
			$result=query_by_id($sql,[],$conn)[0];
			$total_inv = ($result['total'] > 0)?$result['total']:0;
			
			$sql="SELECT sum(mpm.amount_paid) as total  from `app_invoice_".$branch_id."` ai LEFT JOIN `multiple_payment_method` mpm on mpm.invoice_id = CONCAT('app',',',ai.id) where $appquery and mpm.payment_method='4' and ai.active=0 AND ai.branch_id='".$branch_id."' AND mpm.branch_id='".$branch_id."'";
			$result=query_by_id($sql,[],$conn)[0];
			$total_app = ($result['total'] > 0)?$result['total']:0;
			
			$amount1 = query_by_id("SELECT paid_amount from `wallet_history` where status=1 and $waquery  and branch_id='".$branch_id."' and iid='0' and transaction_type='1' and payment_method='4'",[],$conn);
	        $amount2 = query_by_id("SELECT wallet_amount from `wallet_history` where status=1 and $waquery and branch_id='".$branch_id."' and iid !='0' and transaction_type='1' and payment_method='4'",[],$conn);
	        foreach($amount1 as $a1){
	            $wallet_amount += $a1['paid_amount'];
	        }
	        foreach($amount2 as $a2){
	            $wallet_amount += $a2['wallet_amount'];
	        }
			
			$total = $total_inv+$total_app+$wallet_amount;
			break;
			case "Online_Payment":
			$wallet_amount = 0;
			$sql="SELECT sum(mpm.amount_paid) as total  from `invoice_".$branch_id."` i LEFT JOIN `multiple_payment_method` mpm on mpm.invoice_id = CONCAT('bill',',',i.id) where $invquery and mpm.payment_method='5' and i.active=0 AND i.branch_id='".$branch_id."' AND mpm.branch_id='".$branch_id."'";
			$result=query_by_id($sql,[],$conn)[0];
			$total_inv = ($result['total'] > 0)?$result['total']:0;
			
			$sql="SELECT sum(mpm.amount_paid) as total  from `app_invoice_".$branch_id."` ai LEFT JOIN `multiple_payment_method` mpm on mpm.invoice_id = CONCAT('app',',',ai.id) where $appquery and mpm.payment_method='5' and ai.active=0 AND ai.branch_id='".$branch_id."' AND mpm.branch_id='".$branch_id."'";
			$result=query_by_id($sql,[],$conn)[0];
			$total_app = ($result['total'] > 0)?$result['total']:0;
			
			$amount1 = query_by_id("SELECT paid_amount from `wallet_history` where status=1 and $waquery  and branch_id='".$branch_id."' and iid='0' and transaction_type='1' and payment_method='5'",[],$conn);
	        $amount2 = query_by_id("SELECT wallet_amount from `wallet_history` where status=1 and $waquery and branch_id='".$branch_id."' and iid !='0' and transaction_type='1' and payment_method='5'",[],$conn);
	        foreach($amount1 as $a1){
	            $wallet_amount += $a1['paid_amount'];
	        }
	        foreach($amount2 as $a2){
	            $wallet_amount += $a2['wallet_amount'];
	        }
			
			$total = $total_inv+$total_app+$wallet_amount;
			break;
			case "Paytm":
			$wallet_amount = 0;
			$sql="SELECT sum(mpm.amount_paid) as total  from `invoice_".$branch_id."` i LEFT JOIN `multiple_payment_method` mpm on mpm.invoice_id = CONCAT('bill',',',i.id) where $invquery and mpm.payment_method='6' and i.active=0 AND i.branch_id='".$branch_id."' AND mpm.branch_id='".$branch_id."'";
			$result=query_by_id($sql,[],$conn)[0];
			$total_inv = ($result['total'] > 0)?$result['total']:0;
			
			$sql="SELECT sum(mpm.amount_paid) as total  from `app_invoice_".$branch_id."` ai LEFT JOIN `multiple_payment_method` mpm on mpm.invoice_id = CONCAT('app',',',ai.id) where $appquery and mpm.payment_method='6' and ai.active=0 AND ai.branch_id='".$branch_id."' AND mpm.branch_id='".$branch_id."'";
			$result=query_by_id($sql,[],$conn)[0];
			$total_app = ($result['total'] > 0)?$result['total']:0;
			
			$amount1 = query_by_id("SELECT paid_amount from `wallet_history` where status=1 and $waquery  and branch_id='".$branch_id."' and iid='0' and transaction_type='1' and payment_method='6'",[],$conn);
	        $amount2 = query_by_id("SELECT wallet_amount from `wallet_history` where status=1 and $waquery and branch_id='".$branch_id."' and iid !='0' and transaction_type='1' and payment_method='6'",[],$conn);
	        foreach($amount1 as $a1){
	            $wallet_amount += $a1['paid_amount'];
	        }
	        foreach($amount2 as $a2){
	            $wallet_amount += $a2['wallet_amount'];
	        }
			
			$total = $total_inv+$total_app+$wallet_amount;
			break;
			case "Gpay":
		    $wallet_amount = 0;
			$sql="SELECT sum(mpm.amount_paid) as total  from `invoice_".$branch_id."` i LEFT JOIN `multiple_payment_method` mpm on mpm.invoice_id = CONCAT('bill',',',i.id) where $invquery and mpm.payment_method='11' and i.active=0 AND i.branch_id='".$branch_id."' AND mpm.branch_id='".$branch_id."'";
			$result=query_by_id($sql,[],$conn)[0];
			$total_inv = ($result['total'] > 0)?$result['total']:0;
			
			$sql="SELECT sum(mpm.amount_paid) as total  from `app_invoice_".$branch_id."` ai LEFT JOIN `multiple_payment_method` mpm on mpm.invoice_id = CONCAT('app',',',ai.id) where $appquery and mpm.payment_method='11' and ai.active=0 AND ai.branch_id='".$branch_id."' AND mpm.branch_id='".$branch_id."'";
			$result=query_by_id($sql,[],$conn)[0];
			$total_app = ($result['total'] > 0)?$result['total']:0;
			
			$amount1 = query_by_id("SELECT paid_amount from `wallet_history` where status=1 and $waquery  and branch_id='".$branch_id."' and iid='0' and transaction_type='1' and payment_method='11'",[],$conn);
	        $amount2 = query_by_id("SELECT wallet_amount from `wallet_history` where status=1 and $waquery and branch_id='".$branch_id."' and iid !='0' and transaction_type='1' and payment_method='11'",[],$conn);
	        foreach($amount1 as $a1){
	            $wallet_amount += $a1['paid_amount'];
	        }
	        foreach($amount2 as $a2){
	            $wallet_amount += $a2['wallet_amount'];
	        }
			
			$total = $total_inv+$total_app+$wallet_amount;
			break;
			case "RewardPoint":
			$wallet_amount = 0;
			$sql="SELECT sum(mpm.amount_paid) as total  from `invoice_".$branch_id."` i LEFT JOIN `multiple_payment_method` mpm on mpm.invoice_id = CONCAT('bill',',',i.id) where $invquery and mpm.payment_method='9' and i.active=0 AND i.branch_id='".$branch_id."' AND mpm.branch_id='".$branch_id."'";
			$result=query_by_id($sql,[],$conn)[0];
			$total_inv = ($result['total'] > 0)?$result['total']:0;
			
			$sql="SELECT sum(mpm.amount_paid) as total  from `app_invoice_".$branch_id."` ai LEFT JOIN `multiple_payment_method` mpm on mpm.invoice_id = CONCAT('app',',',ai.id) where $appquery and mpm.payment_method='9' and ai.active=0 AND ai.branch_id='".$branch_id."' AND mpm.branch_id='".$branch_id."'";
			$result=query_by_id($sql,[],$conn)[0];
			$total_app = ($result['total'] > 0)?$result['total']:0;
			
			$amount1 = query_by_id("SELECT paid_amount from `wallet_history` where status=1 and $waquery  and branch_id='".$branch_id."' and iid='0' and transaction_type='1' and payment_method='9'",[],$conn);
	        $amount2 = query_by_id("SELECT wallet_amount from `wallet_history` where status=1 and $waquery and branch_id='".$branch_id."' and iid !='0' and transaction_type='1' and payment_method='9'",[],$conn);
	        foreach($amount1 as $a1){
	            $wallet_amount += $a1['paid_amount'];
	        }
	        foreach($amount2 as $a2){
	            $wallet_amount += $a2['wallet_amount'];
	        }
			
			$total = $total_inv+$total_app+$wallet_amount;
			break;
			case "PhonePe":
			$wallet_amount = 0;
			$sql="SELECT sum(mpm.amount_paid) as total  from `invoice_".$branch_id."` i LEFT JOIN `multiple_payment_method` mpm on mpm.invoice_id = CONCAT('bill',',',i.id) where $invquery and mpm.payment_method='10' and i.active=0 AND i.branch_id='".$branch_id."' AND mpm.branch_id='".$branch_id."'";
			$result=query_by_id($sql,[],$conn)[0];
			$total_inv = ($result['total'] > 0)?$result['total']:0;
			
			$sql="SELECT sum(mpm.amount_paid) as total  from `app_invoice_".$branch_id."` ai LEFT JOIN `multiple_payment_method` mpm on mpm.invoice_id = CONCAT('app',',',ai.id) where $appquery and mpm.payment_method='10' and ai.active=0 AND ai.branch_id='".$branch_id."' AND mpm.branch_id='".$branch_id."'";
			$result=query_by_id($sql,[],$conn)[0];
			$total_app = ($result['total'] > 0)?$result['total']:0;
			
			$amount1 = query_by_id("SELECT paid_amount from `wallet_history` where status=1 and $waquery  and branch_id='".$branch_id."' and iid='0' and transaction_type='1' and payment_method='10'",[],$conn);
	        $amount2 = query_by_id("SELECT wallet_amount from `wallet_history` where status=1 and $waquery and branch_id='".$branch_id."' and iid !='0' and transaction_type='1' and payment_method='10'",[],$conn);
	        foreach($amount1 as $a1){
	            $wallet_amount += $a1['paid_amount'];
	        }
	        foreach($amount2 as $a2){
	            $wallet_amount += $a2['wallet_amount'];
	        }
			
			$total = $total_inv+$total_app+$wallet_amount;
			break;
			default:
			$total=0;
			break;
		}
		return $total;
	}
	
	
	function checkstaff($sid,$inv){
		global $conn;
		global $branch_id;
		$sql = "SELECT * from invoice_items_".$branch_id." where staffid=$sid and iid=$inv and active=0 and branch_id='".$branch_id."'";
		$result=query_by_id($sql,[],$conn)[0];
		if($result) {
			return 1;
			}else{
			return 0;
		}
	}
	
	function checkproduct($pr,$pid,$inv){
		global $conn;
		global $branch_id;
		if($pr=="pr")
		$pr="Product";
		if($pr=="sr")
		$pr="Service";
		if($pr=="pa")
		$pr="Package";
		$sql = "SELECT * from invoice_items_".$branch_id." where type='$pr' and service=$pid and iid=$inv and active=0 and branch_id='".$branch_id."'";
		$result=query_by_id($con,$sql)[0];
		if($result) {
			return 1;
			}else{
			return 0;
		}
	}
	
	function getcat($cid) {
		global $conn;
		global $branch_id;
		$sql="SELECT * from expensecat where id='$cid' and branch_id='".$branch_id."'";
		$result=query_by_id($sql,[],$conn)[0];
		if($result) {
			return $result['title'];
		}
	}
	
	/*function getsales2(){
		$date = date('Y-m-d');
		global $con;
		$sql="SELECT sum(subtotal) as total from `invoice` where doa='$date' and type=2 and active=0";
		$result=mysqli_query($con,$sql);
		if($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
		return $row['total'];
		}
	}*/
	
	function getsales(){
		global $branch_id;
	    if(isset($_GET['sdate']) &&isset($_GET['edate'])){
	        $sdate = $_GET['sdate'];
	        $edate = $_GET['edate'];
	        $sql="SELECT sum(total) as total from `invoice_".$branch_id."` where doa BETWEEN '$sdate' AND '$edate' and active=0 and branch_id='".$branch_id."'";
	    } else {
	        $date = date('Y-m-d');    
	        $sql="SELECT sum(total) as total from `invoice_".$branch_id."` where doa='$date' and active=0 and branch_id='".$branch_id."'";
	    }
		global $conn;
		
		$result=query_by_id($sql,[],$conn)[0];
		if($result){
			$total = ($result['total'] > 0)?$result['total']:0;
			return $total;
		}else{
			return 0;
		}
	}
	
	function getpendingamount(){
		global $branch_id;
	    if(isset($_GET['sdate']) &&isset($_GET['edate'])){
	        $sdate = $_GET['sdate'];
	        $edate = $_GET['edate'];
	        $sql="SELECT sum(due) as total from `invoice_".$branch_id."` where doa BETWEEN '$sdate' AND '$edate' and active=0 and branch_id='".$branch_id."'";
	    } else {
	        $date = date('Y-m-d');    
	        $sql="SELECT sum(due) as total from `invoice_".$branch_id."` where doa='$date' and active=0 and branch_id='".$branch_id."'";
	    }
		global $conn;
		$result=query_by_id($sql,[],$conn)[0];
		if($result){
			$total = ($result['total'] > 0)?$result['total']:0;
			return $total;
		}else{
			return 0;
		}
	}
	
	
	function get_discount(){
		global $branch_id;
	    $total = 0;
	    if(isset($_GET['sdate']) &&isset($_GET['edate'])){
	        $sdate = $_GET['sdate'];
	        $edate = $_GET['edate'];
	        $sql="SELECT paid, dis, subtotal, total, id from `invoice_".$branch_id."` where doa BETWEEN '$sdate' AND '$edate' and active=0 and branch_id='".$branch_id."'";
	        $sql2="SELECT coupons.* FROM coupons LEFT JOIN invoice_".$branch_id." invoice ON coupons.id = invoice.coupon WHERE invoice.doa BETWEEN '$sdate' AND '$edate' and invoice.active=0 and invoice.branch_id='".$branch_id."'";
	        
	    } else {
	        $date = date('Y-m-d');    
	        $sql="SELECT paid, dis, subtotal, total, id from `invoice_".$branch_id."` where doa='$date' and active=0 and branch_id='".$branch_id."'";
	        $sql2="SELECT coupons.*, invoice.coupon, invoice.subtotal FROM coupons LEFT JOIN invoice_".$branch_id." invoice ON coupons.id = invoice.coupon WHERE invoice.doa='$date' and invoice.active=0 and invoice.branch_id='".$branch_id."'";
	    }
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
		        if($res['discount_type'] == 0){
		            if($res['coupon'] != 0){
		                $dis_price = ($res['subtotal']/100)*$res['discount'];
    		            $total = $total+$dis_price;
		            }
		        } else {
		            $total = $total + $res['discount'];
		        }
		    }
		}
		return $total;
	}
	
	function get_discount_invoice($id){
		global $branch_id;
	    $total = 0;
	    if(isset($_GET['sdate']) &&isset($_GET['edate'])){
	        $sdate = $_GET['sdate'];
	        $edate = $_GET['edate'];
	        $sql="SELECT paid, dis, subtotal, total, id from `invoice_".$branch_id."` where doa BETWEEN '$sdate' AND '$edate' and active=0 and branch_id='".$branch_id."' and id='".$id."'";
	        $sql2="SELECT coupons.* FROM coupons LEFT JOIN invoice_".$branch_id." invoice ON coupons.id = invoice.coupon WHERE invoice.doa BETWEEN '$sdate' AND '$edate' and invoice.active=0 and invoice.branch_id='".$branch_id."' and invoice.id='".$id."'";
	        
	    } else {
	        $date = date('Y-m-d');    
	        $sql="SELECT paid, dis, subtotal, total, id from `invoice_".$branch_id."` where doa='$date' and active=0 and branch_id='".$branch_id."' and id='".$id."'";
	        $sql2="SELECT coupons.*,invoice.coupon, invoice.subtotal FROM coupons LEFT JOIN invoice_".$branch_id." invoice ON coupons.id = invoice.coupon WHERE invoice.doa='$date' and invoice.active=0 and invoice.branch_id='".$branch_id."' and invoice.id='".$id."'";
	    }
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
		        if($res['discount_type'] == 0){
		            if($res['coupon'] != 0){
		                $dis_price = ($res['subtotal']/100)*$res['discount'];
    		            $total = $total+$dis_price;
		            }
		        } else {
		            $total = $total + $res['discount'];
		        }
		    }
		}
		return $total;
	}
	
	function getproductsale(){
		global $branch_id;
	    if(isset($_GET['sdate']) &&isset($_GET['edate'])){
	        $sdate = $_GET['sdate'];
	        $edate = $_GET['edate'];
	        $sql="SELECT ii.actual_price, ii.price, ii.quantity from `invoice_items_".$branch_id."` ii LEFT JOIN invoice_".$branch_id." i on i.id=ii.iid where i.doa BETWEEN '$sdate' AND '$edate' and ii.type='Product' and i.active=0 and i.branch_id='".$branch_id."'";
	    } else {
		    $date = date('Y-m-d');
		    $sql="SELECT ii.actual_price, ii.price, ii.quantity from `invoice_items_".$branch_id."` ii LEFT JOIN invoice_".$branch_id." i on i.id=ii.iid where i.doa='$date' and ii.type='Product' and i.active=0 and i.branch_id='".$branch_id."'";
	    }
		global $conn;
		$total = 0;
		$result=query_by_id($sql,[],$conn);
		if($result) {
			foreach($result as $item){
	            $total = $total+($item['actual_price']*$item['quantity']);
			}
	        return $total;
		}else{
			return 0;
		}
	}
	
	function getservicesale(){
		global $branch_id;
	    if(isset($_GET['sdate']) &&isset($_GET['edate'])){
	        $sdate = $_GET['sdate'];
	        $edate = $_GET['edate'];
	        $sql="SELECT ii.actual_price, ii.price, ii.quantity from `invoice_items_".$branch_id."` ii LEFT JOIN invoice_".$branch_id." i on i.id=ii.iid where i.doa BETWEEN '$sdate' AND '$edate' and ii.type='Service' and i.active=0 and i.branch_id='".$branch_id."'";
	    } else {
		    $date = date('Y-m-d');
		    $sql="SELECT ii.actual_price, ii.price, ii.quantity from `invoice_items_".$branch_id."` ii LEFT JOIN invoice_".$branch_id." i on i.id=ii.iid where i.doa='$date' and ii.type='Service' and i.active=0 and i.branch_id='".$branch_id."'";
	    }
		global $conn;
		$total = 0;
		$result=query_by_id($sql,[],$conn);
		if($result) {
			foreach($result as $item){
	            $total = $total+($item['actual_price']*$item['quantity']);
			}
	        return $total;
		}else{
			return 0;
		}
	}
	
	function getcashsale(){
		$date = date('Y-m-d');
		global $conn;
		global $branch_id;
		$sql="SELECT sum(mpm.amount_paid + i.advance) as total  from `invoice_".$branch_id."` i LEFT JOIN `multiple_payment_method` mpm on mpm.invoice_id = CONCAT('bill',',',i.id) where i.doa='$date' and mpm.payment_method='1' and i.active=0 and i.branch_id='".$branch_id."' AND mpm.branch_id='".$branch_id."'";
		$result=query_by_id($sql,[],$conn)[0];
		if($result) {
			$total = $result['total'];
			return $total;
			}else{
			return "0";
		}
	}
	
	
	function getcardsale(){
		$date = date('Y-m-d');
		global $conn;
		global $branch_id;
		$sql="SELECT sum(mpm.amount_paid + i.advance) as total  from `invoice_".$branch_id."` i LEFT JOIN `multiple_payment_method` mpm on mpm.invoice_id = CONCAT('bill',',',i.id) where i.doa='$date' and mpm.payment_method='3' and i.active=0 and i.branch_id='".$branch_id."' AND mpm.branch_id='".$branch_id."'";
		$result=query_by_id($sql,[],$conn)[0];
		if($result['total'] > 0) {
			$total = $result['total'];
			return $total;
			}else{
			return "0";
		}
	}
	
	function getonline(){
		$date = date('Y-m-d');
		global $conn;
		global $branch_id;
		$sql="SELECT sum(total) as total from `invoice_".$branch_id."` where doa='$date' and pay_method='Online payment' and bmethod='Online payment' and active=0 and branch_id='".$branch_id."'";
		//echo $sql;
		$result=mysqli_query($sql,[],$conn)[0];
		if($result) {
			if($result['total']=="")
			return 0;
			else
			return $result['total'];
			}else{
			return "0";
		}
	}
	
	function getchequesale(){
		$date = date('Y-m-d');
		global $conn;
		global $branch_id;
		$sql="SELECT sum(total) as total from `invoice_".$branch_id."` where doa='$date' and pay_method='Cheque' or bmethod='Cheque' and active=0 and branch_id='".$branch_id."'";
		$result=query_by_id($sql,[],$conn)[0];
		if($result) {
			if($result['total']=="")
			return 0;
			else
			return $result['total'];
			}else{
			return "0";
		}
	}
	
	function getwalletsale(){
		$date = date('Y-m-d');
		global $conn;
		global $branch_id;
		$sql="SELECT sum(total) as total from `invoice_".$branch_id."` where doa='$date' and pay_method='Mobile Wallet' or bmethod='Mobile Wallet' and active=0 and branch_id='".$branch_id."'";
		$result=query_by_id($sql,[],$conn)[0];
		if($result) {
			if($result['total']=="")
			return 0;
			else
			return $result['total'];
			}else{
			return "0";
		}
	}
	//
	
	function getcredit(){
		$date = date('Y-m-d');
		global $conn;
		global $branch_id;
		$sql="SELECT sum(total) as total,sum(bpaid) as bpaid,sum(paid) as paid from `invoice_".$branch_id."` where doa='$date' and type=2 and active=0 and branch_id='".$branch_id."'";
		$result=query_by_id($sql,[],$conn)[0];
		if($result) {
			$total = $result['total'];
			$bpaid = $result['bpaid'];
			$paid  = $result['paid'];
			//echo $total."-".$bpaid."-".$paid;
			$tot = $total - $bpaid - $paid;
			if($tot=="")
			return 0;
			else
			return $tot;
			}else{
			return "0";
		}
	}
	
	function getclients(){
		global $branch_id;
	    if(isset($_GET['sdate']) &&isset($_GET['edate'])){
	        $sdate = $_GET['sdate'];
	        $edate = $_GET['edate'];
	        $sql="SELECT distinct(client) from `invoice_".$branch_id."` where active=0 and doa BETWEEN '$sdate' AND '$edate' and branch_id='".$branch_id."'";
	    } else {
	        $date = date('Y-m-d');
	        $sql="SELECT distinct(client) from `invoice_".$branch_id."` where active=0 and doa='$date' and branch_id='".$branch_id."'";
	    }
		$cl = 0;
		global $conn;
		$result=query_by_id($sql,[],$conn);
		foreach($result as $row) {
			$cl++;
		}
		return $cl;
	}
	
	
	function getnewclients(){
		global $branch_id;
		global $conn;
	    if(isset($_GET['sdate']) &&isset($_GET['edate'])){
	        $sdate = $_GET['sdate'];
	        $edate = $_GET['edate'];
	        $result = query("SELECT distinct(client) from `invoice_".$branch_id."` i left join client c on i.client = c.id where i.active=0 and i.doa BETWEEN '".$sdate."' AND '".$edate."' and i.branch_id='".$branch_id."'", [], $conn);
	    } else {
	        $date = date('Y-m-d');
	        $result = query("SELECT distinct(client) from `invoice_".$branch_id."` i left join client c on i.client = c.id where i.active=0 and i.doa = '".$date."' and i.branch_id='".$branch_id."'", [], $conn);
	    }
		$cl = 0;
        foreach($result as $row) {
    		$cl++;
    	}
    	return $cl;
	}
	
	function walletrecharged(){
	    global $branch_id;
	    global $conn;
	    $wallet_amount = 0;
	    if(isset($_GET['sdate']) &&isset($_GET['edate'])){
	        $sdate = $_GET['sdate'];
	        $edate = $_GET['edate'];
	        $amount1 = query_by_id("SELECT paid_amount from `wallet_history` where status=1 and DATE(time_update) BETWEEN '$sdate' AND '$edate' and branch_id='".$branch_id."' and iid='0' and transaction_type='1'",[],$conn);
	        $amount2 = query_by_id("SELECT wallet_amount from `wallet_history` where status=1 and DATE(time_update) BETWEEN '$sdate' AND '$edate' and branch_id='".$branch_id."' and iid !='0' and transaction_type='1'",[],$conn);
	        foreach($amount1 as $a1){
	            $wallet_amount += $a1['paid_amount'];
	        }
	        foreach($amount2 as $a2){
	            $wallet_amount += $a2['wallet_amount'];
	        }
	    } else {
	        $date = date('Y-m-d');
	        $amount1 = query_by_id("SELECT paid_amount from `wallet_history` where status=1 and DATE(time_update) = '".$date."' and branch_id='".$branch_id."' and iid='0' and transaction_type='1'",[],$conn);
	        $amount2 = query_by_id("SELECT wallet_amount from `wallet_history` where status=1 and DATE(time_update) ='".$date."' and branch_id='".$branch_id."' and iid!='0' and transaction_type='1'",[],$conn);
	        foreach($amount1 as $a1){
	            $wallet_amount += $a1['paid_amount'];
	        }
	        foreach($amount2 as $a2){
	            $wallet_amount += $a2['wallet_amount'];
	        }
	    }

		return $wallet_amount;
	}
	
	function taxcalculation(){
	    global $branch_id;
	    global $conn;
	    $inc_tax = 0;
	    $exl_tax = 0;
	    if(isset($_GET['sdate']) && isset($_GET['edate'])){
	        $sdate = $_GET['sdate'];
	        $edate = $_GET['edate'];
	        $check_tax = query_by_id("SELECT id, tax, taxtype, dis, subtotal  FROM invoice_$branch_id WHERE doa BETWEEN '$sdate' AND '$edate' AND branch_id='".$branch_id."'",[],$conn);
	        foreach($check_tax as $tax){
	           $gst_amount = get_discount_invoice($tax['id']);
	           if($tax['taxtype'] == '0' && $tax['tax'] == '1'){
	               $tx = query_by_id("SELECT tax FROM tax WHERE id='".$tax['tax']."' and active='0'",[],$conn)[0];
	               $inc_tax += $gst_amount-($gst_amount/($tx['tax']+100))*100;
	           } else if($tax['taxtype'] == '1' && $tax['tax'] == '1'){
	               $tx = query_by_id("SELECT tax FROM tax WHERE id='".$tax['tax']."' and active='0'",[],$conn)[0];
	               $exl_tax += ($gst_amount*$tx['tax'])/100;
	           }
	        }
	    } else {
	        $date = date('Y-m-d');
	        $check_tax = query_by_id("SELECT id, tax, taxtype, dis, subtotal  FROM invoice_$branch_id WHERE doa='".$date."' AND branch_id='".$branch_id."'",[],$conn);
	        foreach($check_tax as $tax){
	           $gst_amount = get_discount_invoice($tax['id']);
	           if($tax['taxtype'] == '0' && $tax['tax'] == '1'){
	               $tx = query_by_id("SELECT t.tax FROM tax t WHERE id='".$tax['tax']."' and active='0'",[],$conn)[0];
	               $inc_tax += ($gst_amount-($gst_amount/($tx['tax']+100))*100);
	           } else if($tax['taxtype'] == '1' && $tax['tax'] == '1'){
	               $tx = query_by_id("SELECT t.tax FROM tax t WHERE id='".$tax['tax']."' and active='0'",[],$conn)[0];
	               $exl_tax += ($gst_amount*$tx['tax']/100);
	           }
	        }
	    }
	    
	    echo '<strong>Inclusive tax : </strong>'.number_format($inc_tax,2)."<br />";
	    echo '<strong>Exclusive tax : </strong>'.number_format($exl_tax,2);
	}
	
	function commission_amount(){
	    global $branch_id;
	    global $conn;
	    if(isset($_GET['sdate']) && isset($_GET['edate'])){
	        $sdate = $_GET['sdate'];
	        $edate = $_GET['edate'];
	        $dateFilter="and (i.doa between '".$sdate."' and '".$edate."') ";
	        $spQuery = "SELECT i.doa,b.name as service_provider,b.id as bid, s.name as s_name, imsp.service_name, b.cont,ii.price as serice_price, i.id as bill_id from `invoice_multi_service_provider` imsp "
				." LEFT JOIN `invoice_items_".$branch_id."` ii on ii.id=imsp.ii_id "
				." LEFT JOIN `invoice_".$branch_id."` i on i.id=ii.iid " 
				." LEFT JOIN `beauticians` b on b.id = imsp.service_provider"
				." LEFT JOIN `service` s on s.id = SUBSTRING_INDEX(ii.service,',',-1)"
				." where i.active=0 and imsp.branch_id='".$branch_id."' ".$dateFilter;
	        $spRecords = query_by_id($spQuery,[],$conn);
	        foreach($spRecords as $row) {
    	        if(explode(",",$row['service_name'])[1] >0){
    				 $ser_id = $row['service_name'];
    				 $service_name=getservice($ser_id);
    			}else{
    				  $service_name = '';
    			}
    			
    			if(explode(" ", $service_name)[0] == '(Service)'){
    			    $amount = getservicecom($row['serice_price'],$row['bid'],$ser_id, $row['bill_id']);
    			    $total_commission = $amount+$total_commission;
    			} else { 
    			    $amount = getprodcom($row['serice_price'],$row['bid'],$ser_id, $row['bill_id']);
    			    $total_commission = $amount+$total_commission;
    			}
	        }
	    } else {
	        $date = date('Y-m-d');
	        $dateFilter="and i.doa = '".$date."'";
	        $spQuery = "SELECT i.doa,b.name as service_provider,b.id as bid, s.name as s_name, imsp.service_name, b.cont,ii.price as serice_price, i.id as bill_id from `invoice_multi_service_provider` imsp "
				." LEFT JOIN `invoice_items_".$branch_id."` ii on ii.id=imsp.ii_id "
				." LEFT JOIN `invoice_".$branch_id."` i on i.id=ii.iid " 
				." LEFT JOIN `beauticians` b on b.id = imsp.service_provider"
				." LEFT JOIN `service` s on s.id = SUBSTRING_INDEX(ii.service,',',-1)"
				." where i.active=0 and imsp.branch_id='".$branch_id."' ".$dateFilter;
	        $spRecords = query_by_id($spQuery,[],$conn);
	        foreach($spRecords as $row) {
    	        if(explode(",",$row['service_name'])[1] >0){
    				 $ser_id = $row['service_name'];
    				 $service_name=getservice($ser_id);
    			}else{
    				  $service_name = '';
    			}
    			
    			if(explode(" ", $service_name)[0] == '(Service)'){
    			    $amount = getservicecom($row['serice_price'],$row['bid'],$ser_id, $row['bill_id']);
    			    $total_commission = $amount+$total_commission;
    			} else { 
    			    $amount = getprodcom($row['serice_price'],$row['bid'],$ser_id, $row['bill_id']);
    			    $total_commission = $amount+$total_commission;
    			}
	        }
	    }
	    return $total_commission;
	}
	
	function getexpenses(){
		global $branch_id;
	    if(isset($_GET['sdate']) &&isset($_GET['edate'])){
	        $sdate = $_GET['sdate'];
	        $edate = $_GET['edate'];
	        $sql="SELECT sum(amount) as total FROM `expense` where active=0 and date BETWEEN '$sdate' AND '$edate' and branch_id='".$branch_id."'";
	    } else {
		    $date = date('Y-m-d');
		    $sql="SELECT sum(amount) as total FROM `expense` where active=0 and date='$date' and branch_id='".$branch_id."'";
	    }
		global $conn;
		$result=query_by_id($sql,[],$conn)[0];
		if($result) {
			if($result['total']=="")
			return 0;
			else
			return $result['total'];
			}else{
			return 0;
		}
	}
	
	function getadvance(){
		$date = date('Y-m-d');
		global $conn;
		global $branch_id;
		$sql="SELECT sum(advance) as total FROM `invoice_".$branch_id."` where active=0 and doa='$date' and branch_id='".$branch_id."'";
		$result=query_by_id($sql,[],$conn)[0];
		if($result) {
			return $result['total'];
			}else{
			return 0;
		}
	}
	
	function getpayments(){
		global $conn;
		global $branch_id;
		$date = date('Y-m-d');
		$sql="SELECT sum(credit) as credit FROM `payments` WHERE date='$date' and active=0 and branch_id='".$branch_id."'";
		$result=query_by_id($sql,[],$conn)[0];
		if ($result) {
			return $result['credit'];
			}else{
			return "0";
		}
	}
	
	function vendorpays(){
		global $conn;
		global $branch_id;
		if(isset($_GET['sdate']) &&isset($_GET['edate'])){
	        $sdate = $_GET['sdate'];
	        $edate = $_GET['edate'];
	        $sql="SELECT sum(debit) as debit FROM `payments` WHERE date BETWEEN '$sdate' AND '$edate' and active=0 and branch_id='".$branch_id."'";
		} else {
		    $date = date('Y-m-d');
		    $sql="SELECT sum(debit) as debit FROM `payments` WHERE date='$date' and active=0 and branch_id='".$branch_id."'";
		}
		$result=query_by_id($sql,[],$conn)[0];
		if ($result) {
			return $result['debit'];
			}else{
			return "0";
		}
	}
	
	function netcollection(){
		global $branch_id;
	    if(isset($_GET['sdate']) &&isset($_GET['edate'])){
	        $sdate = $_GET['sdate'];
	        $edate = $_GET['edate'];
	        $sql="SELECT sum(subtotal) as total,sum(bpaid) as paid FROM `invoice_".$branch_id."` where active=0 and branch_id='".$branch_id."' and doa BETWEEN '$sdate' AND '$edate'";  
	    } else {
		    $date = date('Y-m-d');
		    $sql="SELECT sum(subtotal) as total,sum(bpaid) as paid FROM `invoice_".$branch_id."` where active=0 and doa='$date' and branch_id='".$branch_id."'";
	    }
		global $conn;
		$result=query_by_id($sql,[],$conn)[0];
		if($result) {
			$payments = getpayments();
			$total = $result['total'] +$payments;
			if($total=="")
			return 0;
			else
			return $total;
			}else{
			return 0;
		}
	}
	
    // commission functions
    
    
    	function getsum($pid){
		$earn = getearnings($pid);
		$ded = getdeductions($pid);
		$sum = $earn - $ded;
		return $sum;
	}
	
	function getearnings($pid) {
		global $conn;
		global $branch_id;
		$sql2="SELECT sum(salary) as sum from salary where eid='$pid' and type=1 and branch_id='".$branch_id."'";
		$result2=query_by_id($sql2,[],$conn);
		foreach($result2 as $row2){
			return $row2['sum'];
		}
	}
	
	function getdeductions($cid) {
		global $conn;
		global $branch_id;
		$sql2="SELECT sum(salary) as sum from salary where eid='$cid' and type=2 and branch_id='".$branch_id."'";
		$result2=query_by_id($sql2,[],$conn);
		foreach($result2 as $row2) {
			return $row2['sum'];
		}
	}
	
	
	function getservicecom($price,$bid,$service,$id){
		$sum = 0;
		$provider_id = $bid;
		$type = 'Service';
		$total_sale = service_provider_total_sale($type, $provider_id, $id);
		$commission_per = service_provider_commission_saved($provider_id, $service, $id);
		$com = $commission_per;
		$val = $price * $com / 100;
		$sum = $sum + $val;
		$pcount = provider_count($service,$id);
		if($pcount > 0){
		    return $sum/$pcount;
		} else {
		    return $sum;   
		}
	}
	
	function getprodcom($price,$bid,$service,$id){
		$sum = 0;
		$provider_id = $bid;
		$type = 'Product';
		$total_sale = service_provider_total_sale($type, $provider_id, $id);
		$commission_per = service_provider_commission_saved($provider_id, $service, $id);
		$com = $commission_per;
		$val = $price * $com / 100;
		$sum = $sum + $val;
		$pcount = provider_count($service,$id);
		if($pcount > 0){
		    return $sum/$pcount;
		} else {
		    return $sum;   
		}
	}
	
	function getcoms($bid,$type,$id){
		global $conn;
		global $branch_id;
		$sum = 0;
		$sql="SELECT * from invoice_multi_service_provider where inv='$id' and service_name='$type' and service_provider = '$bid' and status='1' and branch_id='".$branch_id."'";
		$result=query_by_id($sql,[],$conn);
		foreach($result as $row) {
			return $row['commission_per'];
		}
	}
	
	function getcomp($bid,$type,$id){
		global $conn;
		global $branch_id;
		$sum = 0;
        $sql="SELECT * from invoice_multi_service_provider where inv='$id' and service_name='$type' and service_provider = '$bid' and status='1' and branch_id='".$branch_id."'";
		$result=query_by_id($sql,[],$conn);
		foreach($result as $row) {
			return $row['commission_per'];
		}
	}
	
	function getproduct($bid){
		global $conn;
		global $branch_id;
		$sum = 0;
		$sql="SELECT * from products where id=$bid";
		$result=query_by_id($sql,[],$conn);
		foreach($result as $row) {
			return $row['name'];
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
	
	<script>
	
	function filterDailyreport(){
		var daterange = $('#daterange').val();
		var date = daterange.split("-");
		if(daterange == ''){
			var from = '';
			var to = '';
		} else {
			var from = isoDate(date[0]);
			var to = isoDate(date[1]);
		}
		window.location.href = '?sdate='+from+'&edate='+to;
	}
	
	// jquery function to change daterange date in iso date standard  Y-m-d (2019-11-01)

	function isoDate(date){	
		var datespit = date.split('/');
		var day = datespit[1].replace(' ','');
		var month = datespit[0].replace(' ','');
		var year = datespit[2].replace(' ','');
		return year+'-'+month+'-'+day;
	}
	
	 function filerIRclient(){
	    var startdate = $('#irfrom-date').val();
	    var enddate = $('#irto-date').val();
	    if(startdate == ''){
	        $('#irfrom-date').css('border-color','#f00');
	    } else if(enddate == ''){
	        $('#irto-date').css('border-color','#f00');
	    } else {
	        if(new Date(startdate) <= new Date(enddate))
            {
    	        $('#irfrom-date').css('border-color','initial');
    	        $('#irto-date').css('border-color','initial');
    	        window.location.href = '?sdate='+startdate+'&edate='+enddate;
    	       // jQuery.ajax({
    	       //    url: "ajax/irregularclient.php",
    	       //    type: "POST",
    	       //    data : {from : startdate, to : enddate , action : 'filter_client'},
    	       //    success : function(response){
    	               //$('#irclient').html(response);
    	               //var table = $('#smstab').DataTable();
                       //table.ajax.reload();
    	       //    }
    	       // });
            } else {
                alert('To date shoule be greater or equal to from date');
            }
	    }
	}
	</script>