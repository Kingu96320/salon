<?php 
	include "./includes/db_include.php";
	$branch_id = $_SESSION['branch_id'];
	include "topbar.php";
	include "header.php";
	include "menu.php";
	
	if(isset($_GET['cid']) && $_GET['cid']>0)
	{
		$cid = $_GET['cid'];
		$edit = query_by_id("SELECT sum(w.wallet_amount) as wallet_amount ,c.name as client_name,c.cont from wallet w LEFT JOIN client c on c.id=w.client_id where w.client_id=:cid and status='1' GROUP by w.client_id",["cid"=>$cid],$conn)[0];
	}
	
?>

<!-- Dashboard wrapper starts -->
<div class="dashboard-wrapper">
	<!-- Main container starts -->
	<div class="main-container">
		<!-- Row starts -->
		<div class="row gutter">
			<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
				<!-- Row starts -->
				<div class="row gutter">
					<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
						<div class="panel">
							<div class="panel-heading">
								<h4><?=ucwords($edit['client_name'])?>'s Wallet Credit/ Debit History</h4>
							</div><br>
							<div class="alert alert-success">
							<?php $wallet = $edit['wallet_amount'] ?>
						    <i class=" icon-wallet"></i><strong>Your Wallet Balance </strong><?= CURRENCY ." ". number_format($wallet,2); ?> /-  
							</div><br>
							<div class="panel-body">
								<div class="row">
									<div class="col-lg-12">
										<div class="table-responsive">
											<table id='empTable' class="table table-bordered">
												<thead>
													<tr>
														<th>Date/Time</th>
														<th>Transaction type</th>
														<th>Amount paid</th>
														<th>Wallet amount</th>
														<th>Payment method</th>
														<th>Amount received from</th>
														<th>Bill id</th>
													</tr>
												</thead>
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
</div>
<!-- Dashboard Wrapper End -->


<!-- Container fluid ends -->


<script>
	
	function confirmDelete()
	{
		return confirm('Are you sure?')
	}
	/*******Server_side_datatable*********/
		$(document).ready(function(){
			var client_id=<?=$_GET['cid']?>;
			$('#empTable').DataTable({
				'processing': true,
				'serverSide': true,
				'serverMethod': 'post',
				'ajax': {
					'type':'POST',
					'data':{client_id},
					'url':'ajax/fetch_wallet_reports.php'
				},
				'columns': [
				{ data: 'time_update' },
				{ data: 'transaction_type' },
				{ data: 'paid_amount' },
				{ data: 'wallet_amount' },
				{ data: 'payment_method' },
				{ data: 'amount_received_from' },
				{ data: 'bill_id' },
				],
				'columnDefs': [{
					'targets': [0,1,2,3], // column index (start from 0)
					'orderable': false, // set orderable false for selected columns
				}],
				"order": [[ 0, "desc" ]]
			});
		});
		/*******End********/
			$('#client_name').on('keypress',function(){
				$('#client_id').val("");
			});
			$("#client_name").autocomplete({
				source: "autocomplete/client.php",
				minLength: 1,
				select: function(event, ui) {
					event.preventDefault();
					$('#client_name').val(ui.item.name);
					$('#client_id').val(ui.item.id); 
					$('#cont').val(ui.item.cont); 
					$('#client-status').html("");
				}				
			});	
			
			$('#client_name').on('blur',function(){
				var client_id = $('#client_id').val();	
				if(client_id ==''){
					$('#client-status').html('Please select client name from list');
					$('#client_name').val("");
					$('#cont').val(""); 
				}
			});
			
			function checkcat() {
				var cat_id=$('#client_id').val();
				$.ajax({
					url: "ajax/checkservice.php",
					data:{category: cat,cat_id:cat_id},
					type: "POST",
					success:function(data){
						if(data == '1'){
							$("#check-status").html("Duplicate category . Please select category from list").css("color","red");
							$('#scat').val("");
							}else{
							$("#check-status").html("");
						}
					},
					error:function (){}
				});
			} 
			
			
		</script>
		
		
		<?php 
			include "footer.php";
		?>																		