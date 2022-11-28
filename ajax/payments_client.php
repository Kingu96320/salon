<?php
	include "../includes/db_include.php";
	$branch_id = $_SESSION['branch_id'];
	if(isset($_POST['client_id']) && $_POST['client_id'] > 0){
		$client_id = $_POST['client_id'];
		$date = date('Y-m-d');
	?>
	
	<table id="grid" class="table table-bordered no-margin">
		<thead>
			<tr>
				<th width="30%">Date of bill</th>
				<th width="20%">Invoice id</th>
				<th width="20%">Pending amount</th>
				<th width="20%">Paid</th>
				<th width="10%">Select Paid date:<input type="text" class="form-control date paid_date" name="paid_date"  readonly></th>
				<th width="10%"></th>
			</tr>
		</thead>
		<tbody>
			<?php 
				$total=0;
				$sql1="SELECT c.id as client_id,c.name,c.cont,i.*,i.id as iid,i.due as pending from `invoice_".$branch_id."` i LEFT JOIN `client` c on c.id=i.client where i.active=0 and i.client='$client_id' and i.branch_id='".$branch_id."'";
				$result1=query_by_id($sql1,[],$conn);
				foreach($result1 as $row1) { 
				get_invoice_pending_payment($row1['iid']);
					if($row1['pending'] - get_invoice_pending_payment($row1['iid']) > 0){
						$total+=$row1['pending'] - get_invoice_pending_payment($row1['iid']);
					?>
					<tr>
						<td ><?php echo $row1['doa']; ?></td>
						<td>INV<?=sprintf('%04d',$row1['id']); ?><input type="hidden" class="invoice_id" name="invoice_id" value="<?=$row1['id'];?>"></td>
						<td><?=$row1['pending'] - get_invoice_pending_payment($row1['iid']) ?></td>
						<td><input type="number" name="paid_modal" min="0" max="<?=round($row1['pending'],0); ?>" class="form-control positivenumber decimalnumber paid_modal" value="0"><input type="hidden" class="due_amount" name="due_amount" ></input></td>
						<td></td>
					</tr><?php  } } ?>
					<tr><td></td><td>Total</td><td><?=$total?></td><td><span class="pen_amount">0</span></td><td  id="payment_method_select_row"> 
						<select name="method" data-validation="required" class="form-control pay_method">
							<option value="">Select payment mode</option>
							<?php $sql_pay_mode="Select * FROM `payment_method` where status='1'";
								$result_pay_mode =query_by_id($sql_pay_mode,[],$conn);
								foreach($result_pay_mode as $row_pay_mode){
								if($row_pay_mode['id']!='7'){
								?>
								<option value="<?=$row_pay_mode['id']?>" <?=$row_pay_mode['id']=='1'?'Selected':''?>><?=$row_pay_mode['name']?></option> 
								<?php } } ?>  
						</select></td><td><button class="btn btn-info accept_payment">Accept payment</button></td>
					</tr>
		</tbody>
	</table>
	
	<?php 	}
?>	

<script>
	$('document').ready(function(){		
		$('.paid_modal').on('input',function(){
			var this_paid = 0;
			var inputs = $(".paid_modal");
			var total=<?=$total?>||0;
			inputs.each(function(){
				this_max_value=parseInt($(this).attr('max'));
				if($(this).val() > this_max_value ){
					$(this).val("0");	
				}
				this_paid +=parseInt($(this).val()||0);
				var paid_amount =$(this).parents('tr').find('.paid_modal').val();
				if(paid_amount !='0'){
					$(this).parent().find('.due_amount').val(this_max_value - paid_amount);
				}
			});
			$('.pen_amount').html(this_paid)||0;	
		});
		
		/*******Accept_payment********/
			$('.accept_payment').click(function(){
				var payment_method = $(this).parents('tr').find('.pay_method').val();
		    	var paid_date  = $(this).parents('table').find('.paid_date').val();
				var arr = [];
				$('.invoice_id').each(function(){
					var invoice_id = $(this).val();
					var paid_amount =$(this).parents('tr').find('.paid_modal').val();
					var due = $(this).parents('tr').find('.due_amount').val();
					if(paid_amount !='0')
					arr.push({invoice_id:invoice_id,paid_amount:paid_amount,due:due,payment_method:payment_method,client_id:<?=$client_id?>,paid_date:paid_date});
				});
				$.ajax({
					url : "ajax/get_pending_payments.php",
					type: "POST",
					async: false,
					data: {data:JSON.stringify(arr)},
					success:function(data){
						if(data.trim() === '1'){
							window.location = 'dashboard.php';
							<?php 	$_SESSION['t']  = 1;
								$_SESSION['tmsg']  = "Pending payments accepted successfully";
							?>
						}
					}
				});
			});
			
		});
	</script>
