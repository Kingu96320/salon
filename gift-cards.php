<?php 
	if(isset($_GET['gc_id']) && $_GET['gc_id']>0)
	{
		$id = $_GET['gc_id'];
		$edit = query_by_id("SELECT w.*,c.name as client_name,c.cont from wallet w LEFT JOIN client c on c.id=w.client_id where w.id=:id",["id"=>$id],$conn)[0];
	}
	
	if(isset($_POST['edit-submit-gc'])){
		$client_id 	= $_POST['client_id_gc'];
		$wallet_amount 	= $_POST['wallet_amount_gc'];
		$date 	= $_POST['date_gc'];
		$payment_method = $_POST['payment_method_gc'];
		$eid = $_GET['gc_id'];
		query("DELETE from `wallet_history` where iid='$beid' and status='1' and branch_id='".$branch_id."'",[],$conn);
		query("UPDATE `wallet` set date='$date',payment_method='$payment_method',client_id='$client_id',wallet_amount='$wallet_amount' where id=:id and branch_id='".$branch_id."'",['id'=>$id],$conn);
		query("INSERT INTO `wallet_history` set client_id='$client_id',transaction_type	='0',wallet_amount='$wallet_amount',get_wallet_from='Add_wallet', branch_id='".$branch_id."'",[],$conn);
		$_SESSION['t']  = 1;
		$_SESSION['tmsg']  = "Wallet Updated Successfully";
		header('location:wallet.php');
		exit();
	}
	
	if(isset($_POST['submit_gc'])){
		$client_id 	= $_POST['client_id_gc'];
		$wallet_amount 	= $_POST['wallet_amount_gc'];
		$date 	= $_POST['date_gc'];
		$payment_method = $_POST['payment_method_gc'];
		$aid=get_insert_id("INSERT INTO `wallet` set date='$date',payment_method='$payment_method',client_id='$client_id',wallet_amount='$wallet_amount',get_wallet_from='Add_wallet', branch_id='".$branch_id."'",[],$conn);
		query("INSERT INTO `wallet_history` set client_id='$client_id',transaction_type='1',wallet_amount='$wallet_amount',get_wallet_from='Add_wallet', branch_id='".$branch_id."'",[],$conn);
		
		
		
		if(isset($_POST['send_receipt'])){
			$current_date = date(MY_DATE_FORMAT);
			$current_time = date(TIME_FORMAT);
			$sql_client_name = query_by_id("SELECT c.name as client_name,c.cont from client c where c.id=:client_id and branch_id='".$branch_id."'",["client_id"=>$client_id],$conn)[0];
			$system = systemname();
			$client_name = $sql_client_name['client_name_gc'];
			$contact     = $sql_client_name['cont_gc'];
			$msg = "Dear ".$client_name.". your wallet credited with ".CURRENCY." ".$wallet_amount." on ".$current_date." ".$current_time.". Avl Wallet Balance ".CURRENCY." ".client_wallet($client_id)." ".$system;
			send_sms($contact,$msg);
		}
		
		
		$_SESSION['t']  = 1;
		$_SESSION['tmsg']  = "Gift card Added Successfully";
		header('location:wallet.php');
		exit();
	}
	
	if(isset($_GET['del_id_gc'])){
		$del=$_GET['del_id_gc'];
		query("UPDATE `wallet` SET `status`=0 WHERE client_id=$del and branch_id='".$branch_id."'",[],$conn);
		query("UPDATE `wallet_history` SET `status`=0 WHERE client_id=$del and branch_id='".$branch_id."'",[],$conn);
		
		query("UPDATE `invoice_".$branch_id."` SET `wallet_status`=0 WHERE client='$del' and pay_method='7' and branch_id='".$branch_id."'",[],$conn);
		$_SESSION['t']  = 1;
		$_SESSION['tmsg']  = "Gift card Removed Successfully";
		header('location:wallet.php');
		exit();
	}
?>

		<!-- Row starts -->
		<div class="row gutter">
			
			<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
				<div class="panel">
					<div class="panel-heading">
						<h4>Bill new gift card</h4>
					</div>
					<div class="panel-body">
						<div class="row">
							<form action="" method="post">
    							 <div class="col-lg-4 col-md-3 col-sm-3 col-xs-12">
    								<div class="form-group">
    									<label for="userName">Date</label>
    									<?php $date = date('Y-m-d'); ?>
    									<input type="text" class="form-control date" value="<?=($edit['doa'])?$edit['doa']:$date?>" name="date_gc" readonly>
    								</div>
    							</div>
								<div class="col-lg-4 col-md-3 col-sm-3 col-xs-12">
									<div class="form-group">
										<label for="userName">Client name :</label>
										<input type="text" class="form-control" id="client_name_gc" name="name_gc" placeholder="Client Name" value="<?= isset($id)?$edit['client_name']:''?>"   required>
										<input type="hidden" id="client_id_gc" name="client_id_gc" value="<?= isset($id)?$edit['client_id']:''?>"> 
										<span style="color:red" id="client-status"></span>
									</div>
								</div>
								<div class="col-lg-4 col-md-3 col-sm-3 col-xs-12">
									<div class="form-group">
										<label for="userName">Contact :</label>
										<input type="text"  onkeyup="this.value = this.value.replace(/[^0-9\.]/g,'');" maxLength="<?= PHONE_NUMBER ?>" class="form-control" onBlur="check()" name="cont_gc" id="cont_gc" placeholder="Contact" value="<?= isset($id)?$edit['cont']:''?>" readonly>
										
									</div>
								</div>
								
								<div class="clearfix"></div>
								
								<div class="col-lg-2 col-md-3 col-sm-3 col-xs-12">
									<div class="form-group">
										<label for="userName">Amount paid:</label>
										<input type="text"  onkeyup="this.value = this.value.replace(/[^0-9\.]/g,'');" maxLength="<?= PHONE_NUMBER ?>" class="form-control" onBlur="check()" name="wallet_amount_gc" placeholder="Amount" value="<?= isset($id)?$edit['wallet_amount']:''?>" required>
										
									</div>
								</div>
								<div class="col-lg-2 col-md-3 col-sm-3 col-xs-12">
									<div class="form-group">
										<label for="userName">Payment mode :</label>
										<select name="payment_method_gc" data-validation="required" class="form-control act">
													<?php $sql_pay_mode="Select * FROM `payment_method` where status='1'";
														$result_pay_mode =query_by_id($sql_pay_mode,[],$conn);
														foreach($result_pay_mode as $row_pay_mode){
														if($row_pay_mode['id'] !='7'){
														?>
														<option <?=(($edit['payment_method'] === $row_pay_mode['id'])?'value="'.$row_pay_mode['id'].'" Selected':'')?> value="<?=$row_pay_mode['id']?>"><?=$row_pay_mode['name']?></option> 
														<?php } }?>  
												</select>
									</div>
								</div>
								<div class="col-lg-2 col-md-3 col-sm-3 col-xs-12">
									<div class="form-group">
										<label for="userName">Amount to be credited :</label>
										<input type="text"  onkeyup="this.value = this.value.replace(/[^0-9\.]/g,'');" maxLength="<?= PHONE_NUMBER ?>" class="form-control" onBlur="check()" name="wallet_amount_gc" placeholder="Amount" value="<?= isset($id)?$edit['wallet_amount']:''?>" required>
									</div>
								</div>
								<div class="col-lg-2 col-md-3 col-sm-3 col-xs-12">
									<div class="form-group">
										<label for="userName">Valid till :</label>
										<input type="text" class="form-control date" name="" readonly required>
										<!-- <input type="date"  onkeyup="this.value = this.value.replace(/[^0-9\.]/g,'');" maxLength="<?= PHONE_NUMBER ?>" class="form-control" onBlur="check()" name="wallet_amount" id="cont" placeholder="Amount" value="<?= isset($id)?$edit['wallet_amount']:''?>" required> -->
									</div>
								</div>
								<div class="col-lg-12 col-md-3 col-sm-3 col-xs-12">
									<div class="form-group"><br>
										<label for="userName">Send receipt:</label>
										<label class="checkbox-inline"><input type="checkbox" name="send_receipt_gc">Send the deposit receipt to customer?</label>
									</div>
								</div>	
									<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
										<div class="form-group">
											
											<?php if(isset($id))
												{	
												?>
												<button type="submit" name="edit-submit-gc" class="btn btn-info pull-right">Update wallet</button>
												<?php 	} else
												{ ?>	
												<button type="submit" name="submit_gc" class="btn btn-info pull-right">Create</button>
											<?php }?>		
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
					


<script>
	
	function confirmDelete()
	{
		return confirm('Are you sure?')
	}
		/*******End********/
			$('#client_name_gc').on('keypress',function(){
				$('#client_id_gc').val("");
			});
			$("#client_name_gc").autocomplete({
				source: "autocomplete/client.php",
				minLength: 1,
				select: function(event, ui) {
					event.preventDefault();
					$('#client_name_gc').val(ui.item.name);
					$('#client_id_gc').val(ui.item.id); 
					$('#cont_gc').val(ui.item.cont); 
					$('#client-status-gc').html("");
				}				
			});	
			
			$('#client_name_gc').on('blur',function(){
				var client_id = $('#client_id_gc').val();	
				if(client_id ==''){
					$('#client-status-gc').html('Please select client name from list');
					$('#client_name_gc').val("");
					$('#cont_gc').val(""); 
				}
			});
			
			function checkcat() {
				var cat_id=$('#client_id_gc').val();
				$.ajax({
					url: "ajax/checkservice.php",
					data:{category: cat,cat_id:cat_id},
					type: "POST",
					success:function(data){
						if(data == '1'){
							$("#check-status-gc").html("Duplicate category . Please select category from list").css("color","red");
							$('#scat_gc').val("");
							}else{
							$("#check-status-gc").html("");
						}
					},
					error:function (){}
				});
			} 
			
			
		</script>