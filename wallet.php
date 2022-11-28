<?php 
    include_once 'includes/db_include.php';
	if(isset($_GET['del_id'])){
		$del = $_GET['del_id'];
		query("UPDATE `wallet` SET `status`=0 WHERE client_id='".$del."'",[],$conn);
		query("UPDATE `wallet_history` SET `status`=0 WHERE client_id='".$del."'",[],$conn);
		//query("UPDATE `invoice_".$branch_id."` SET `wallet_status`=0 WHERE client='".$del."' and pay_method='7'",[],$conn);
		$_SESSION['t']  = 1;
		$_SESSION['tmsg']  = "Wallet removed successfully";
		header('location:billing.php');
		exit();
	}
?>

<!-- Dashboard wrapper starts -->
		<!-- Row starts -->
		<div class="row gutter">
			
			<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
				<div class="panel">
					<div class="panel-heading">
						<h4>Add wallet amount</h4>
					</div>
					<div class="panel-body">
						<div class="row">
							<form action="" method="post">
    							 <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
    								<div class="form-group">
    									<label for="date">Date <span class="text-danger">*</span></label>
    									<?php $date = date('Y-m-d'); ?>
    									<input type="text" class="form-control date" value="<?=($edit['doa'])?$edit['doa']:$date?>" name="date_wallet" readonly required>
    								</div>
    							</div>
								<div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
									<div class="form-group">
										<label for="name">Client name <span class="text-danger">*</span></label>
										<input type="text" class="form-control" id="client_name_wallet" name="name_wallet" placeholder="Client Name" value="<?= isset($id)?$edit['client_name']:''?>"   required>
										<input type="hidden" id="client_id_wallet" name="client_id_wallet" value="<?= isset($id)?$edit['client_id']:''?>"> 
										<span style="color:red" id="client-status-wallet"></span>
									</div>
								</div>
								<div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
									<div class="form-group">
										<label for="cont">Contact number<span class="text-danger">*</span></label>
										<input type="text"  onkeyup="this.value = this.value.replace(/[^0-9\.]/g,'');" maxLength="<?= PHONE_NUMBER ?>" class="form-control" onBlur="check()" name="cont_wallet" id="cont_wallet" placeholder="Contact" value="<?= isset($id)?$edit['cont']:''?>" readonly required>
									</div>
								</div>
								
								<div class="clearfix"></div>
								
								<div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
									<div class="form-group">
										<label for="paid_amount">Amount paid <span class="text-danger">*</span></label>
										<input type="number"  onkeyup="this.value = this.value.replace(/[^0-9\.]/g,'');" maxLength="<?= PHONE_NUMBER ?>" class="form-control" onBlur="check()" name="paid_amount_wallet" placeholder="Amount" value="<?= isset($id)?$edit['paid_amount']:''?>" required>
										
									</div>
								</div>
								<div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
									<div class="form-group">
										<label for="payment_method">Payment mode <span class="text-danger">*</span></label>
										<select name="payment_method_wallet" data-validation="required" required class="form-control act">
													<?php $sql_pay_mode="Select * FROM `payment_method` where status='1'";
														$result_pay_mode =query_by_id($sql_pay_mode,[],$conn);
														foreach($result_pay_mode as $row_pay_mode){
														if($row_pay_mode['id'] != '7'){ ?>
														    <option <?=(($edit['payment_method'] === $row_pay_mode['id'])?'value="'.$row_pay_mode['id'].'" Selected':'')?> value="<?=$row_pay_mode['id']?>"><?=$row_pay_mode['name']?></option> 
														<?php } } ?>  
												</select>
									</div>
								</div>
								<div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
									<div class="form-group">
										<label for="wallet_amount">Amount to be credit <span class="text-danger">*</span></label>
										<input type="number" onkeyup="this.value = this.value.replace(/[^0-9\.]/g,'');" maxLength="<?= PHONE_NUMBER ?>" class="form-control" onBlur="check()" name="wallet_amount" placeholder="Amount" value="<?= isset($id)?$edit['wallet_amount']:''?>" required>
									</div>
								</div>
								<div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
									<div class="form-group">
										<label for="send_receipt">Send receipt:</label>
										<label class="checkbox-inline"><input type="checkbox" name="send_receipt">Send the deposit receipt to customer?</label>
									</div>
								</div>	
									<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
										<div class="form-group">
											
											<?php if(isset($id)) { ?>
												<button type="submit" name="edit-submit-wallet" class="btn btn-info pull-right"><i class="fa fa-pencil-square-o" aria-hidden="true"></i>Update wallet</button>
											<?php } else { ?>	
												<button type="submit" name="wallet_submit" class="btn btn-success pull-right"><i class="fa fa-plus" aria-hidden="true"></i>Add wallet</button>
											<?php } ?>		
										</div>
									</div>
								</form>
							</div>
							
						</div>
						
					</div>
					
					
				</div>
			</div>
			<div class="clearfix"></div>
			<!-- Row ends -->
			<!-- Row starts -->
					<div class="row ">
						
						<div class="col-lg-12">
							
							<div class="panel">
								<div class="panel-heading">
									<h4>Client's Wallet</h4>
								</div>
								<div class="panel-body">
									
									<div class="">
										<div class="">
											<div class="table-responsive">
												<table id='empTable' class="table table-bordered">
													<thead>
														<tr>
															<th>Date/Time</th>
															<th>Client name</th>
															<th>Contact number</th>
															 <!--<th>Total amount</th> -->
															<th>Wallet amount</th>
															 <!--<th>Payment mode</th> -->
															<th width="200">Action</th>
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
				// { data: 'wallet_amount' },
				{ data: 'remaning_amount' },
				// { data: 'payment_method' },
				{ data: 'action' },
				],
				'columnDefs': [ {
					// 'targets': [0,2,3,4,5], // column index (start from 0)
					'orderable': false, // set orderable false for selected columns
				}]
			});
		});
		/*******End********/
			$('#client_name_wallet').on('keypress',function(){
				$('#client_id_wallet').val("");
			});
			$("#client_name_wallet").autocomplete({
				source: "autocomplete/client.php",
				minLength: 1,
				select: function(event, ui) {
					event.preventDefault();
					$('#client_name_wallet').val(ui.item.name);
					$('#client_id_wallet').val(ui.item.id); 
					$('#cont_wallet').val(ui.item.cont); 
					$('#client-status-wallet').html("");
				}				
			});	
			
			$('#client_name_wallet').on('blur',function(){
				var client_id = $('#client_id_wallet').val();	
				if(client_id ==''){
					$('#client-status-wallet').html('Please select client name from list');
					$('#client_name_wallet').val("");
					$('#cont_wallet').val(""); 
				}
			});
			
			function checkcat() {
				var cat_id=$('#client_id_wallet').val();
				$.ajax({
					url: "ajax/checkservice.php",
					data:{category: cat,cat_id:cat_id},
					type: "POST",
					success:function(data){
						if(data == '1'){
							$("#check-status-wallet").html("Duplicate category . Please select category from list").css("color","red");
							$('#scat_wallet').val("");
							}else{
							$("#check-status-wallet").html("");
						}
					},
					error:function (){}
				});
			} 
			
			
		</script>