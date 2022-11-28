<?php 
	include_once './includes/db_include.php';
	$branch_id = $_SESSION['branch_id'];
	
	if(isset($_SESSION['user_type']) &&  $_SESSION['user_type'] == 'superadmin'){
	    if(isset($_GET['id']) && $_GET['id'] > 0){
	        query_by_id("UPDATE invoice_pending_payment SET status = '0' WHERE id='".$_GET['id']."' AND branch_id='".$branch_id."'",[],$conn);
	        $_SESSION['t']  = 1;
		    $_SESSION['tmsg']  = "Record deleted successfully";
		    echo '<meta http-equiv="refresh" content="0; url=received-pending-payments.php" />';die();
	    }
	}
	
	include "topbar.php";
	include "header.php";
	include "menu.php";
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
						<h4 class="pull-left">Received Pending Payments</h4>
    					<span id="download-btn"></span>					
    					<div class="clearfix"></div>
					</div>
					<div class="panel-body"><br />
						<div class="table-responsive">
							<table id="empTable" class="table grid table-bordered no-margin">
								<thead>
									<tr>
										<th style="white-space: nowrap">Date</th>
										<th>Invoice id</th>
										<th>Client name</th>
										<th>Contact number</th>
										<th>Amount</th>
										<th>Payment method</th>
										<th>Action</th>
									</tr>
								</thead>
								<tbody>
									<?php
									    $pending_payment = query_by_id("SELECT ipp.iid, ipp.payment_method, ipp.update_date as updatetime,0 as due,ipp.id,ipp.pending_paid_date as doa,c.name as client_name,c.cont,ipp.pending_payment_received as paid,3 as type,0 as advance from `invoice_pending_payment` ipp "
                    					. " LEFT JOIN `client` c on c.id=ipp.client_id"
                    					. " where ipp.status=1 and ipp.paid_at_branch='".$branch_id."' order by ipp.id DESC",[],$conn);
                    					if($pending_payment){
                                        	foreach($pending_payment as $pp){ ?>
                                        	    <tr>
                                            	    <td><?= my_date_format($pp['updatetime']) ?></td>
                                            	    <td><?= $pp['iid'] ?></td>
                                            	    <td><?= ucfirst(strtolower($pp['client_name'])) ?></td>
                                            	    <td><?= $pp['cont'] ?></td>
                                    	            <td><?= number_format($pp['paid'],2) ?></td>
                                    	            <td><?= pay_method_name($pp['payment_method']) ?></td>
                                    	            <td width="50"><a href="received-pending-payments.php?id=<?= $pp['id'] ?>"><button class="btn btn-xs btn-danger"><i class="icon-delete"></i>Delete</button></a></td>
                                	            </tr>
                                        	<?php }
                                    	}
									?>
								</tbody>
							</table>
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

<?php include "footer.php"; ?>