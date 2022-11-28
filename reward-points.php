<?php 
	include "./includes/db_include.php";
	 
	if(isset($_GET['id']) && $_GET['id']>0)
	{
		$id = $_GET['id'];
		$edit = query_by_id("SELECT w.*,c.name as client_name,c.cont from wallet w LEFT JOIN client c on c.id=w.client_id where w.id=:id",["id"=>$id],$conn)[0];
	}
	
	if(isset($_POST['edit-submit'])){
		$client_id 	= $_POST['client_id'];
		$wallet_amount 	= $_POST['wallet_amount'];
		$date 	= $_POST['date'];
		$payment_method = $_POST['payment_method'];
		$eid = $_GET['id'];
		query("DELETE from `wallet_history` where iid='$beid' and status='1'",[],$conn);
		query("UPDATE `wallet` set date='$date',payment_method='$payment_method',client_id='$client_id',wallet_amount='$wallet_amount' where id=:id",['id'=>$id],$conn);
		query("INSERT INTO `wallet_history` set client_id='$client_id',transaction_type	='0',wallet_amount='$wallet_amount',get_wallet_from='Add_wallet'",[],$conn);
		$_SESSION['t']  = 1;
		$_SESSION['tmsg']  = "Wallet Updated Successfully";
		header('location:wallet.php');
		exit();
	}
	
	if(isset($_POST['submit'])){
		$client_id 	= $_POST['client_id'];
		$wallet_amount 	= $_POST['wallet_amount'];
		$date 	= $_POST['date'];
		$payment_method = $_POST['payment_method'];
		$aid=get_insert_id("INSERT INTO `wallet` set date='$date',payment_method='$payment_method',client_id='$client_id',wallet_amount='$wallet_amount',get_wallet_from='Add_wallet'",[],$conn);
		query("INSERT INTO `wallet_history` set client_id='$client_id',transaction_type='1',wallet_amount='$wallet_amount',get_wallet_from='Add_wallet'",[],$conn);
		
		
		
		if(isset($_POST['send_receipt'])){
			$current_date = date(MY_DATE_FORMAT);
			$current_time = date(TIME_FORMAT);
			$sql_client_name = query_by_id("SELECT c.name as client_name,c.cont from client c where c.id=:client_id",["client_id"=>$client_id],$conn)[0];
			$system = systemname();
			$client_name = $sql_client_name['client_name'];
			$contact     = $sql_client_name['cont'];
			$msg = "Dear ".$client_name.". your wallet credited with ".CURRENCY." ".$wallet_amount." on ".$current_date." ".$current_time.". Avl Wallet Balance ".CURRENCY." ".client_wallet($client_id)." ".$system;
			send_sms($contact,$msg);
		}
		
		
		$_SESSION['t']  = 1;
		$_SESSION['tmsg']  = "Wallet Added Successfully";
		header('location:wallet.php');
		exit();
	}
	
	if(isset($_GET['del_id'])){
		$del=$_GET['del_id'];
		query("UPDATE `wallet` SET `status`=0 WHERE client_id=$del",[],$conn);
		query("UPDATE `wallet_history` SET `status`=0 WHERE client_id=$del",[],$conn);
		
		query("UPDATE `invoice_".$branch_id."` SET `wallet_status`=0 WHERE client='$del' and pay_method='7'",[],$conn);
		$_SESSION['t']  = 1;
		$_SESSION['tmsg']  = "Wallet Removed Successfully";
		header('location:wallet.php');
		exit();
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
			
			<div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">
				<div class="panel">
					<div class="panel-heading">
						<h4>Reward points</h4>
					</div>
					<div class="panel-body">
						
					
						
					</div>
					
					
				</div>
			</div>
			
			
			<div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">
				<div class="panel">
					<div class="panel-heading">
						<h4>Settings</h4>
					</div>
					<div class="panel-body">
						
						<table width="100%" class="table table-striped">
						
							<tr>
								<td>Services:</td>
								<td>
								
								<table>
									<tr>
										<td>
											<input type="text" class="form-control" value="<?= isset($id)?$edit['valid']:old('valid')?>" name="valid" placeholder="" required>
										</td>
										<td>
										<select class="form-control total_disc_row_type" name="total_disc_row_type" id="disc_row_type">
														<option value="0">%</option>
														<option value="1" Selected>Points</option>
													</select>
										</td>
									</tr>
								</table>
								</td>
							</tr>
							
							<tr>
								<td>Products:</td>
								<td>
								
								<table>
									<tr>
										<td>
											<input type="text" class="form-control" value="<?= isset($id)?$edit['valid']:old('valid')?>" name="valid" placeholder="" required>
										</td>
										<td>
										<select class="form-control total_disc_row_type" name="total_disc_row_type" id="disc_row_type">
														<option value="0">%</option>
														<option value="1" Selected>Points</option>
													</select>
										</td>
									</tr>
								</table>
								</td>
							</tr>
							
							<tr>
								<td>Package:</td>
								<td>
								
								<table>
									<tr>
										<td>
											<input type="text" class="form-control" value="<?= isset($id)?$edit['valid']:old('valid')?>" name="valid" placeholder="" required>
										</td>
										<td>
										<select class="form-control total_disc_row_type" name="total_disc_row_type" id="disc_row_type">
														<option value="0">%</option>
														<option value="1" Selected>Points</option>
													</select>
										</td>
									</tr>
								</table>
								</td>
							</tr>
							
							<tr>
								<td>Gift card:</td>
								<td>
								
								<table>
									<tr>
										<td>
											<input type="text" class="form-control" value="<?= isset($id)?$edit['valid']:old('valid')?>" name="valid" placeholder="" required>
										</td>
										<td>
										<select class="form-control total_disc_row_type" name="total_disc_row_type" id="disc_row_type">
														<option value="0">%</option>
														<option value="1" Selected>Points</option>
													</select>
										</td>
									</tr>
								</table>
								</td>
							</tr>
							
							
							<tr>
								<td>Facebook appointment:</td>
								<td>
								
								<table>
									<tr>
										<td>
											<input type="text" class="form-control" value="<?= isset($id)?$edit['valid']:old('valid')?>" name="valid" placeholder="" required>
										</td>
										<td>
										<select class="form-control total_disc_row_type" name="total_disc_row_type" id="disc_row_type">
														<option value="0">%</option>
														<option value="1" Selected>Points</option>
													</select>
										</td>
									</tr>
								</table>
								</td>
							</tr>
							
							<tr>
								<td>Website appointment:</td>
								<td>
								
								<table>
									<tr>
										<td>
											<input type="text" class="form-control" value="<?= isset($id)?$edit['valid']:old('valid')?>" name="valid" placeholder="" required>
										</td>
										<td>
										<select class="form-control total_disc_row_type" name="total_disc_row_type" id="disc_row_type">
														<option value="0">%</option>
														<option value="1" Selected>Points</option>
													</select>
										</td>
									</tr>
								</table>
								</td>
							</tr>
							
							
							
							
							
							
							
							<tr>
								<td>1 Reward point=:</td>
								<td><input type="text" class="form-control" value="<?= isset($id)?$edit['valid']:old('valid')?>" name="valid" placeholder="Rs.10" required></td>
							</tr>
							<tr>
								<td>Feedback:</td>
								<td><input type="text" class="form-control" value="<?= isset($id)?$edit['valid']:old('valid')?>" name="valid" placeholder="" required></td>
							</tr>
							<tr>
								<td>Reference 1st billing:</td>
								<td>
								
								<table>
									<tr>
										<td>
											<input type="text" class="form-control" value="<?= isset($id)?$edit['valid']:old('valid')?>" name="valid" placeholder="" required>
										</td>
										<td>
										<select class="form-control total_disc_row_type" name="total_disc_row_type" id="disc_row_type">
														<option value="0">%</option>
														<option value="1" Selected>Points</option>
													</select>
										</td>
									</tr>
								</table>
								</td>
								
							</tr>
							<tr>
								<td>Reference regular billing:</td>
								<td>
								
								<table>
									<tr>
										<td>
											<input type="text" class="form-control" value="<?= isset($id)?$edit['valid']:old('valid')?>" name="valid" placeholder="" required>
										</td>
										<td>
										<select class="form-control total_disc_row_type" name="total_disc_row_type" id="disc_row_type">
														<option value="0">%</option>
														<option value="1" Selected>Points</option>
													</select>
										</td>
									</tr>
								</table>
								</td>
								
							</tr>
							
							
							<tr>
								<td>Payment mode:</td>
								<td>
								
								<table>
									<tr>
										<td>
											<input type="text" class="form-control" value="<?= isset($id)?$edit['valid']:old('valid')?>" name="valid" placeholder="" required>
										</td>
										<td>
										<select class="form-control total_disc_row_type" name="total_disc_row_type" id="disc_row_type">
														<option value="0">%</option>
														<option value="1" Selected>Points</option>
													</select>
										</td>
										
										<td>
										<select class="form-control total_disc_row_type" name="total_disc_row_type" id="disc_row_type">
														<option value="0">Cash</option>
														<option value="1" Selected>Mobile wallet</option>
														<option value="0">Credit / debit card</option>
														<option value="1" Selected>My wallet</option>
													</select>
										</td>
									</tr>
									<tr>
										<td>
											<input type="text" class="form-control" value="<?= isset($id)?$edit['valid']:old('valid')?>" name="valid" placeholder="" required>
										</td>
										<td>
										<select class="form-control total_disc_row_type" name="total_disc_row_type" id="disc_row_type">
														<option value="0">%</option>
														<option value="1" Selected>Points</option>
													</select>
										</td>
										
										<td>
										<select class="form-control total_disc_row_type" name="total_disc_row_type" id="disc_row_type">
														<option value="0">Cash</option>
														<option value="1" Selected>Mobile wallet</option>
														<option value="0">Credit / debit card</option>
														<option value="1" Selected>My wallet</option>
													</select>
										</td>
									</tr>
									<tr>
										<td>
											<input type="text" class="form-control" value="<?= isset($id)?$edit['valid']:old('valid')?>" name="valid" placeholder="" required>
										</td>
										<td>
										<select class="form-control total_disc_row_type" name="total_disc_row_type" id="disc_row_type">
														<option value="0">%</option>
														<option value="1" Selected>Points</option>
													</select>
										</td>
										
										<td>
										<select class="form-control total_disc_row_type" name="total_disc_row_type" id="disc_row_type">
														<option value="0">Cash</option>
														<option value="1" Selected>Mobile wallet</option>
														<option value="0">Credit / debit card</option>
														<option value="1" Selected>My wallet</option>
													</select>
										</td>
									</tr>
								</table>
								</td>
								
							</tr>
							
							
							
							
							
						</table>
					
						
					</div>
					
					
				</div>
			</div>
			
			
			<div class="clearfix"></div>
			<!-- Row ends -->
			<!-- Row starts -->
					
					
					
		</div>
		<!-- Main container ends -->
		
	</div>
	<!-- Dashboard Wrapper End -->
	
</div>
<!-- Container fluid ends -->
</div>

<script>
	
	function confirmDelete()
	{
		return confirm('Are you sure?')
	}
	/*******Server_side_datatable*********/
		$(document).ready(function(){
			
			$('#empTable').DataTable({
				'processing': true,
				'serverSide': true,
				'serverMethod': 'post',
				'ajax': {
					'url':'ajax/fetch_wallet_details.php'
				},
				'columns': [
				{ data: 'time_update' },
				{ data: 'name' },
				{ data: 'cont' },
				{ data: 'wallet_amount' },
				{ data: 'remaning_amount' },
				{ data: 'payment_method' },
				{ data: 'action' },
				],
				'columnDefs': [ {
					'targets': [0,2,3,4,5], // column index (start from 0)
					'orderable': false, // set orderable false for selected columns
				}]
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
						if(data == ﻿'1'){
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
																