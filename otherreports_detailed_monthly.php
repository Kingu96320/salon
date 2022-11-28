<?php 
	include_once './includes/db_include.php';
	$branch_id = $_SESSION['branch_id'];
	include "topbar.php";
	include "header.php";
	include "menu.php";
	include "reportMenu.php";
	
	$months = array(1=>"January", 2=>"February", 3=>"March", 4=>"April", 5=>"May", 6=>"June", 7=>"July", 8=>"August", 9=>"September", 10=>"October", 11=>"November", 12=>"December");
	$yr = date(Y);
	$feb = 28;
	if($yr%4 == 0)
	    $feb = 29;
	$months_days = array(1=>31, 2=>$feb, 3=>31, 4=>30, 5=>31, 6=>30, 7=>31, 8=>31, 9=>30, 10=>31, 11=>30, 12=>31);
	$week = array(1=>"Sunday", 2=>"Monday", 3=>"Tuesday", 4=>"Wednesday", 5=>"Thursday", 6=>"Friday", 7=>"Saturday")

	
?>
<style>
    @media print {
        #fetch_balace_report{
            display:block;
        }
        header, nav, footer, .heading-with-btn, .col-lg-3.col-md-4.col-sm-4.col-xs-12, #date_filter, #paymentModal {
            display:none;
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
						<h4 id='payment_mode'>Weekly Report for <?= $months[$_GET['f']] ?></h4>
					</div>
					<div class="panel-body">
						<div class="row"><br />
							<div class="col-lg-12 table-responsive">
                                <table class="table table-striped table-bordered">
                                    <tr>
                                        <th>S.No</th>
                                        <th>Week</th>
                                        <th width="100">Total Customers</th>
                                        <th width="100">Total Services</th>
                                        <th>Avg Services</th>
                                        <th>Total Revenue</th>
                                        <th>Avg Billing</th>
                        				<?php
                        					$paymethods = query("SELECT * FROM payment_method where status = 1", [], $conn);
                        					if($paymethods){
                        						foreach ($paymethods as $pm) {
                        							echo "<th>".$pm['name']."</th>";
                        						}
                        					}
                        				?>
                                    </tr>
                                    <?php
                                        $total_weeks = (int)($months_days[$_GET['f']] / 7);
                                        if($months_days[$_GET['f']] % 7 != 0)
                                            $total_weeks += 1;
                                            $start = 0;
                                        for($wk=1; $wk <=$total_weeks; $wk++){
                                            $start +=1;
                                            echo "<tr><td>".$wk."</td>";
                                            echo "<td>".$start;
                                            if($wk == 5){
                                                $start += $months_days[$_GET['f']] % 7 -1;
                                            }else{
                                                $start += 6;
                                            }
                                            echo " - ".($start)."</td>";
                                            $durS = 0;
                                            $durE = 0;
                                            if($wk==1){
                                                $durS = 1;
                                                $durE = 7;
                                            } else if($wk==2){
                                                $durS = 8;
                                                $durE = 14;
                                            } else if($wk==3){
                                                $durS = 15;
                                                $durE = 21;
                                            } else if($wk==4){
                                                $durS = 22;
                                                $durE = 28;
                                            } else if($wk==5){
                                                $durS = 29;
                                                $durE = 29 + $months_days[$_GET['f']] % 7 -1 ;
                                            }
                				    	    $totalCustomers = query("SELECT count(client) as ct from invoice_".$branch_id." where MONTH(date(doa)) = '".$_GET['f']."' and day(date(doa)) >= '".$durS."' and day(date(doa)) <= '".$durE."' ", [], $conn)[0]['ct'];
                    					    if($totalCustomers == "" || $totalCustomers == NULL)
                    					    	$totalCustomers = 0;
                    					    echo "<td>".$totalCustomers."</td>";
                    					    
                    					    $totalServices = query("SELECT count(*) as ct from invoice_items_".$branch_id." where MONTH(date(start_time)) = '".$_GET['f']."' and type='Service' and day(date(start_time)) >= '".$durS."' and day(date(start_time)) <= '".$durE."' ", [], $conn)[0]['ct'];
                    					    if($totalServices == "" || $totalServices == NULL)
                    					    	$totalServices = 0;
                    					    echo "<td>".$totalServices."</td>";
                    					    
                    					    $avgServices = 0;
                    					    if($totalCustomers > 0)
                    					    	$avgServices = $totalServices/$totalCustomers;
                    					    echo "<td>".number_format($avgServices, 2)."</td>";
                    					    
                    					    $totalRevenue = query("SELECT sum(paid) as ct from invoice_".$branch_id." where MONTH(date(doa)) = '".$_GET['f']."' and day(date(doa)) >= '".$durS."' and day(date(doa)) <= '".$durE."' ", [], $conn)[0]['ct'];
                    					    if($totalRevenue == "" || $totalRevenue == NULL)
                    					    	$totalRevenue = 0;
                    					    echo "<td>".number_format($totalRevenue,2)."</td>";
                    					    
                    					    $avgBilling = 0;
                    					    if($totalCustomers > 0)
                    					    	$avgBilling = $totalRevenue/$totalCustomers;
                    					    echo "<td>".number_format($avgBilling, 2)."</td>";
                    					    
                    					    foreach($paymethods as $pm){
                    					        
                    					        $wallet_amount = 0;
                                    			$sql="SELECT sum(mpm.amount_paid) as total  from `invoice_".$branch_id."` i LEFT JOIN `multiple_payment_method` mpm on mpm.invoice_id = CONCAT('bill',',',i.id) where mpm.payment_method='".$pm['id']."' and i.active=0 AND i.branch_id='".$branch_id."' AND mpm.branch_id='".$branch_id."' and month(i.doa) = '".$_GET['f']."' and day(date(i.doa)) >= '".$durS."' and day(date(i.doa)) <= '".$durE."'";
                                    			$result=query_by_id($sql,[],$conn)[0];
                                    			$total_inv = ($result['total'] > 0)?$result['total']:0;
                                    			
                                    			$sql="SELECT sum(mpm.amount_paid) as total  from `app_invoice_".$branch_id."` ai LEFT JOIN `multiple_payment_method` mpm on mpm.invoice_id = CONCAT('app',',',ai.id) where mpm.payment_method='".$pm['id']."' and ai.active=0 AND ai.branch_id='".$branch_id."' AND mpm.branch_id='".$branch_id."' and month(ai.appdate) = '".$_GET['f']."' and day(date(ai.appdate)) >= '".$durS."' and day(date(ai.appdate)) <= '".$durE."'";
                                    			$result=query_by_id($sql,[],$conn)[0];
                                    			$total_app = ($result['total'] > 0)?$result['total']:0;
                                    			
                                    			$amount1 = query_by_id("SELECT paid_amount from `wallet_history` where status=1  and branch_id='".$branch_id."' and iid='0' and transaction_type='1' and payment_method='".$pm['id']."' and month(time_update) = '".$_GET['f']."' and day(date(time_update)) >= '".$durS."' and day(date(time_update)) <= '".$durE."'",[],$conn);
                                    	        $amount2 = query_by_id("SELECT wallet_amount from `wallet_history` where status=1 and branch_id='".$branch_id."' and iid !='0' and transaction_type='1' and payment_method='".$pm['id']."' and month(time_update) = '".$_GET['f']."' and day(date(time_update)) >= '".$durS."' and day(date(time_update)) <= '".$durE."'",[],$conn);
                                    	        foreach($amount1 as $a1){
                                    	            $wallet_amount += $a1['paid_amount'];
                                    	        }
                                    	        foreach($amount2 as $a2){
                                    	            $wallet_amount += $a2['wallet_amount'];
                                    	        }
                                    			
                                    			$total = $total_inv+$total_app+$wallet_amount;
                			
                    					    	$bill = query("SELECT i.paid as total from `invoice_".$branch_id."` i LEFT JOIN `client` c on c.id=i.client LEFT JOIN `multiple_payment_method` mpm on mpm.invoice_id=CONCAT('bill,', i.id) where i.active=0 and month(i.doa) = '".$_GET['f']."' and mpm.payment_method='".$pm['id']."' and i.branch_id='".$branch_id."'  and day(date(i.doa)) >= '".$durS."' and day(date(i.doa)) <= '".$durE."'  UNION SELECT i.pending_payment_received as total from invoice_pending_payment i LEFT JOIN `client` c on c.id=i.client_id LEFT JOIN `invoice_".$branch_id."` inv on inv.id= i.iid where i.status=1 and month(i.update_date) = '".$_GET['f']."' and i.payment_method='".$pm['id']."' and i.branch_id='".$branch_id."'  and day(date(i.update_date)) >= '".$durS."' and day(date(i.update_date)) <= '".$durE."' and i.status='1' ", [], $conn);
                    					    	$paid = 0;
                    					    	foreach($bill as $b)
                    					    		$paid += $b['total'];
                    					    	$percentage = 0;
                    					    	if($totalRevenue > 0)
                    					    		$percentage = (($total/$totalRevenue)*100);
                    						    echo "<td>".number_format($total,2)." (".number_format($percentage, 2)."%)</td>";
                    					    }
                                            echo "</tr>";
                                        }
                                    ?>
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



 <?php  include "footer.php";?>