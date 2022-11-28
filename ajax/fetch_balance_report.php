<?php 
	include_once '../includes/db_include.php'; 
	$branch_id = $_SESSION['branch_id'];
	if(isset($_GET['from_date']) && isset($_GET['to_date'])){
		$from_date=$_GET['from_date'];
		$to_date=$_GET['to_date'];
		$pageinfo=$_GET['pageinfo'];
		if($pageinfo !=''){
		    if($pageinfo == "All"){
                    $payment_method = "All";
                }else{
			$sql="SELECT id, name from `payment_method` where id='$pageinfo' and status='1'";
			$result = query_by_id($sql,[],$conn)[0];
			$payment_method = $result['id'];
                }
		}
	}
    // Billing start date
	$sql_opening_start_date="SELECT i.doa from invoice_".$branch_id." i where i.active=0 and i.branch_id='".$branch_id."' order by doa asc limit 1";
	$result_opening_start_date = query_by_id($sql_opening_start_date,[],$conn)[0];
	$sql_start_date_invoice = $result_opening_start_date['doa'];
	
    // Appointment start date
	$sql_opening_start_date_app="SELECT updatetime,ai.id,ai.doa,c.name as client_name,c.cont,ai.paid,2 as type from app_invoice_".$branch_id." ai"
					. " LEFT JOIN `client` c on c.id=ai.client "
					. " where ai.active=0 and ai.branch_id='".$branch_id."' order by doa asc limit 1";
	$result_opening_start_date_app = query_by_id($sql_opening_start_date_app,[],$conn)[0];
	$sql_start_date_appointment = $result_opening_start_date_app['doa'];
	
    // Expense start date
	$sql_opening_start_date_expense="SELECT date from `expense` e"
					." where e.active=0 and e.branch_id='".$branch_id."' "
					." UNION SELECT dop as date FROM purchase WHERE active='0' AND branch_id='".$branch_id."'"
					." UNION SELECT p.date as date FROM payments p WHERE p.active='0' AND p.branch_id='".$branch_id."' order by date asc limit 1";
	$result_opening_start_date_expense = query_by_id($sql_opening_start_date_expense,[],$conn)[0];
	$sql_start_date_expense = $result_opening_start_date_expense['date'];
	
?>	
    <!--<button class="btn btn-warning buttons-excel buttons-html5" id="excel"><i class="fa fa-file-excel-o" aria-hidden="true"></i>Export</button>-->
<?php if($payment_method != "All"){ ?>
<div class="table-responsive">
		<table  class="table table-responsive" id="table">
			<tbody>
				<tr style="background-color: #ddd;">
					<td><strong>From:</strong> <?=date('d-M-Y',strtotime($from_date))?></td>
					<td><strong>To:</strong> <?=date('d-M-Y',strtotime($to_date))?></td>
					<td colspan="4"></td>
					<?php 
					    $date_to_previousday = date("Y-m-d", strtotime('-24 hours', strtotime($from_date)));
				        $opening_balnce=0; 
				        $Prevopening_balnce = 0;
				        $exp = 0;
                                        
					    $sql_exp="SELECT amount from expense where active=0 and mop='".$payment_method."' and date between '".$sql_start_date_expense."' and '".$date_to_previousday."' and branch_id='".$branch_id."'"
					    	." UNION SELECT paid as amount FROM purchase WHERE active='0' AND pay_method='".$payment_method."' AND branch_id='".$branch_id."' AND dop BETWEEN '".$sql_start_date_expense."' AND '".$date_to_previousday."'"
						." UNION SELECT p.paid as amount FROM payments p WHERE p.active='0' AND p.mode='".$payment_method."' AND p.branch_id='".$branch_id."' AND p.date BETWEEN '".$sql_start_date_expense."' AND '".$date_to_previousday."'";
						$result_exp=query_by_id($sql_exp,[],$conn);
						foreach($result_exp as $row_exp) {
						  $exp += (int)$row_exp['amount']; 
						}
						
					$sql_opening="SELECT updatetime,i.due,i.id,i.doa,c.name as client_name,c.cont,i.paid,1 as type,i.advance from `invoice_".$branch_id."` i "
					. " LEFT JOIN `client` c on c.id=i.client"
					. " LEFT JOIN `multiple_payment_method` mpm on mpm.invoice_id=concat('bill,', i.id)"
					. " where i.active=0 and i.doa between '".$sql_start_date_invoice."' and '".$date_to_previousday."' and mpm.payment_method='$payment_method' and i.branch_id='".$branch_id."'"   
					. " UNION SELECT updatetime,ai.due,ai.id,ai.doa,c.name as client_name,c.cont,ai.paid,2 as type,0 as advance from `app_invoice_".$branch_id."` ai"
					. " LEFT JOIN `client` c on c.id=ai.client "
					. " LEFT JOIN `multiple_payment_method` mpm on mpm.invoice_id=concat('app,', ai.id)"
					. " where ai.active=0 and ai.appdate between '".$sql_start_date_appointment."' and '".$date_to_previousday."' and mpm.payment_method = '$payment_method' and ai.branch_id='".$branch_id."' "
					. " UNION SELECT ipp.update_date as updatetime,0 as due,ipp.id,ipp.pending_paid_date as doa,c.name as client_name,c.cont,ipp.pending_payment_received as paid,3 as type,0 as advance from `invoice_pending_payment` ipp "
					. " LEFT JOIN `client` c on c.id=ipp.client_id"
					. " where ipp.status=1 and ipp.pending_paid_date between '".$sql_start_date_invoice."' and '".$date_to_previousday."' and ipp.payment_method='".$payment_method."' and ipp.paid_at_branch='".$branch_id."' "
					. " UNION SELECT date,0 as due,w.id,w.date doa,c.name as client_name,c.cont,w.wallet_amount as paid,4 as type,0 as advance from `wallet` w "
					. " LEFT JOIN `client` c on c.id=w.client_id "
					. " where w.status=1 and w.date between '".$sql_start_date_invoice."' and '".$date_to_previousday."' and w.payment_method='$payment_method' and w.branch_id='".$branch_id."' order by updatetime asc";
				// 	echo $sql_opening;
					$result_sql_opening = query_by_id($sql_opening,[],$conn);
					foreach($result_sql_opening as $row_opening)
					{ 
					    $paid =($row_opening['type']=='1')?get_cash($row_opening['id']):$row_opening['paid'];
					    if($paid>0){
							$Prevopening_balnce += $paid;
					    }
					} 
					$prevExpense = 0;
					$sql1="SELECT date, amount, 'expense' as type from expense where active=0 and mop='".$payment_method."' and date between '".$sql_start_date_invoice."' and '".$date_to_previousday."' and branch_id='".$branch_id."'"
					." UNION ALL SELECT dop as date, paid as amount, 'stock' as type FROM purchase WHERE active='0' AND pay_method='".$payment_method."' AND branch_id='".$branch_id."' AND dop BETWEEN '".$sql_start_date_invoice."' AND '".$date_to_previousday."'"
					." UNION ALL SELECT p.date as date, p.paid as amount, 'pending payment' as type FROM payments p WHERE p.active='0' AND p.mode='".$payment_method."' AND p.branch_id='".$branch_id."' AND p.date BETWEEN '".$sql_start_date_invoice."' AND '".$date_to_previousday."' order by date asc";
				// 	echo $sql1;
					$result1=query_by_id($sql1,[],$conn);
					foreach($result1 as $row1) {
						$prevExpense+=$row1['amount'];
					}
					$prevBalance = $Prevopening_balnce-$prevExpense;
                                        
					?>
					<td><strong>Opening Balance: </strong></td>
					<td><strong><?= number_format($prevBalance,2) ?></strong></td>
					<td>&nbsp;</td>
				</tr>
				<tr>
					<td colspan="6"><strong>Amount Received</strong></td>
				</tr>
				<tr>
					<td><strong>Date</strong></td>
					<td><strong>Branch</strong></td>
					<td><strong>Invoice id</strong></td>
					<td><strong>Client name</strong></td>
					<td><strong>Client contact</strong></td>
					<td><strong>Advance received</strong></td>
					<td><strong>Pending payment</strong></td>
					<td><strong>Amount received</strong></td>
					<td><strong>Type</strong></td>
				</tr>
				<?php
					$received = 0;
					$sql1="SELECT i.branch_id as branch, updatetime,i.due,i.id,i.doa,c.name as client_name,c.cont,mpm.amount_paid as paid,1 as type,i.advance from `invoice_".$branch_id."` i "
					. " LEFT JOIN `client` c on c.id=i.client"
					. " LEFT JOIN `multiple_payment_method` mpm on mpm.invoice_id=CONCAT('bill,', i.id)"
					. " where i.active=0 and i.doa between '".$from_date."' and '".$to_date."' and mpm.payment_method='$payment_method' and i.branch_id='".$branch_id."' and mpm.branch_id='".$branch_id."' "
					. " UNION SELECT ai.branch_id as branch, updatetime,ai.due,ai.id,ai.doa,c.name as client_name,c.cont,mpm.amount_paid as paid,2 as type,0 as advance from `app_invoice_".$branch_id."` ai"
					. " LEFT JOIN `client` c on c.id=ai.client "
					. " LEFT JOIN `multiple_payment_method` mpm on mpm.invoice_id=CONCAT('app,', ai.id)"
					. " where ai.active=0 and ai.appdate between '".$from_date."' and '".$to_date."' and mpm.payment_method='".$payment_method."' and ai.branch_id='".$branch_id."' and mpm.branch_id='".$branch_id."'"
					. " UNION SELECT ipp.branch_id as branch, ipp.update_date as updatetime,0 as due,ipp.id,ipp.pending_paid_date as doa,c.name as client_name,c.cont,ipp.pending_payment_received as paid,3 as type,0 as advance from `invoice_pending_payment` ipp "
					. " LEFT JOIN `client` c on c.id=ipp.client_id"
					. " where ipp.status=1 and ipp.paid_at_branch='".$branch_id."' and ipp.pending_paid_date between '".$from_date."' and '".$to_date."' and ipp.payment_method='".$payment_method."'"
					. " UNION SELECT w.branch_id as branch,0 as updatetime,0 as due,w.id, DATE(w.time_update) doa,c.name as client_name,c.cont,w.paid_amount as paid,4 as type,0 as advance from `wallet_history` w "
					. " LEFT JOIN `client` c on c.id=w.client_id "
					. " where w.status=1 and DATE(w.time_update) between '".$from_date."' and '".$to_date."' and w.payment_method='$payment_method' and c.branch_id='".$branch_id."' and w.transaction_type='1' order by updatetime asc";
                    // echo $sql1;
					$result1=query_by_id($sql1,[],$conn);
					if($result1){
						$received=0;
						foreach($result1 as $row1) {
							$paid =($row1['type']=='1')?get_cash($row1['id']):$row1['paid'];
								$received+=$paid;
							?>
							<tr>
								<td><?= my_date_format($row1['doa']); ?></td>
								<td><?= ucfirst(branch_by_id($row1['branch'])); ?></td>
								<td>INV <?=sprintf('%04d',$row1['id']); ?></td>
								<td><?= ucfirst($row1['client_name']); ?></td>
								<td><?= $row1['cont']; ?></td>
								<td><?= number_format($row1['advance'],2); ?></td>
								<td><?= number_format(($row1['due']>0)?$row1['due']:'0',2); ?></td>
								<td><?= number_format($paid,2) ?></td>
								<td><?php 
    								if($row1['type']=='1'){ echo 'Bill'; }
    								else if($row1['type']=='2'){ echo 'Appointment'; }
    								else if($row1['type']=='3'){ echo 'Pending payment'; }
    								else if($row1['type']=='4'){ echo 'Wallet'; }
    								else {} 
								?></td>
							</tr>
						<?php } } else {
						    echo "<tr><td class='text-center' colspan='9'>No result found!</td></tr>";
						}  ?>
						<tr style="background-color: #ddd;">
							<td colspan="6">&nbsp;</td>
							<td><strong>Total Received:</strong></td>
							<td><strong><?= number_format($received,2) ?></strong></td>
							<td>&nbsp;</td>
						</tr>
						<tr>
							<td colspan="9"><strong>Expenses</strong></td>
						</tr>
						<tr>
							<td><strong>Date</strong></td>
                            <td colspan="6"><strong>Category</strong></td>
							<td><strong>Amount paid</strong></td>
							<td><strong>Paid by</strong></td>
            				</tr>					
							<?php
								
								$expaid = 0;
								$sql1="SELECT e.date as date, e.cat as cat, e.amount as amount, e.user as user, 'Expense' as type from expense e where e.active=0 and e.mop='".$payment_method."' and e.date between '".$from_date."' and '".$to_date."' and e.branch_id='".$branch_id."'"
									." UNION ALL SELECT dop as date, 0 as cat, paid as amount, 0 as user, 'Stock purchase' as type  FROM purchase WHERE active='0' AND pay_method='".$payment_method."' AND branch_id='".$branch_id."' AND dop BETWEEN '".$from_date."' AND '".$to_date."'"
								    ." UNION ALL SELECT p.date as date, 0 as cat, p.paid as amount, 0 as user, 'Stock purchase (pending payment)' as type FROM payments p WHERE p.active='0' AND p.mode='".$payment_method."' AND p.branch_id='".$branch_id."' AND p.date BETWEEN '".$from_date."' AND '".$to_date."' order by date asc";
								$result1=query_by_id($sql1,[],$conn);
								if($result1){
    								foreach($result1 as $row1) {
    								?>
    								<tr>
    									<td><?php echo my_date_format($row1['date']); ?></td>
    									<td colspan="2"><?php 
    										if($row1['type'] == 'Expense'){
    											echo getcat($row1['cat']).' - '.$row1['type'];
    										} else {
    											echo $row1['type'];
    										} ?></td>
    									<td colspan="4">&nbsp;</td>
    									<td><?php $expaid += (int)$row1['amount']; echo number_format($row1['amount']); ?></td>
    									<td><?php if($row1['type'] == 'Expense'){
    										echo get_user($row1['user']); 
    									} else {
    										echo '-';
    									} ?></td>
    								</tr>
							    <?php } } else {
							        echo "<tr><td class='text-center' colspan='10'>No result found!</td></tr>";
							    } ?>
							<tr style="background-color: #ddd;">
    							<td colspan="6">&nbsp;</td>
								<td><strong>Total Paid:</strong></td>
								<td><strong><?= number_format($expaid,2) ?></strong></td>
								<td>&nbsp;</td>
							</tr>
						<tr style="background-color: #ddd;">
							<td colspan="6">&nbsp;</td>
							<td><strong>Closing Balance:</strong></td>
							<td><strong><?= number_format($prevBalance + $received -$expaid,2); ?> /-</strong></td>
							<td>&nbsp;</td>
						</tr>

			</tbody>
		</table>
		
	</div>

<?php }else{ ?>
<div class="table-responsive">
		<table  class="table table-responsive" id="table">
			<tbody>
				<tr style="background-color: #ddd;">
					<td><strong>From:</strong> <?=date('d-M-Y',strtotime($from_date))?></td>
					<td><strong>To:</strong> <?=date('d-M-Y',strtotime($to_date))?></td>
					<td colspan="4"></td>
					<?php 
					    $date_to_previousday = date("Y-m-d", strtotime('-24 hours', strtotime($from_date)));
				        $opening_balnce=0; 
				        $Prevopening_balnce = 0;
				        $exp = 0;
                                        
					    $sql_exp="SELECT amount from expense where active=0 and date between '".$sql_start_date_expense."' and '".$date_to_previousday."' and branch_id='".$branch_id."'"
					    	." UNION SELECT paid as amount FROM purchase WHERE active='0' AND branch_id='".$branch_id."' AND dop BETWEEN '".$sql_start_date_expense."' AND '".$date_to_previousday."'"
						." UNION SELECT p.paid as amount FROM payments p WHERE p.active='0' AND p.branch_id='".$branch_id."' AND p.date BETWEEN '".$sql_start_date_expense."' AND '".$date_to_previousday."'";
						$result_exp=query_by_id($sql_exp,[],$conn);
						foreach($result_exp as $row_exp) {
						  $exp += (int)$row_exp['amount']; 
						}
						
					$sql_opening="SELECT updatetime,i.due,i.id,i.doa,c.name as client_name,c.cont,i.paid,1 as type,i.advance from `invoice_".$branch_id."` i "
					. " LEFT JOIN `client` c on c.id=i.client"
					. " LEFT JOIN `multiple_payment_method` mpm on mpm.invoice_id=concat('bill,', i.id)"
					. " where i.active=0 and i.doa between '".$sql_start_date_invoice."' and '".$date_to_previousday."' and i.branch_id='".$branch_id."'"   
					. " UNION SELECT updatetime,ai.due,ai.id,ai.doa,c.name as client_name,c.cont,ai.paid,2 as type,0 as advance from `app_invoice_".$branch_id."` ai"
					. " LEFT JOIN `client` c on c.id=ai.client "
					. " LEFT JOIN `multiple_payment_method` mpm on mpm.invoice_id=concat('app,', ai.id)"
					. " where ai.active=0 and ai.appdate between '".$sql_start_date_appointment."' and '".$date_to_previousday."' and ai.branch_id='".$branch_id."' "
					. " UNION SELECT ipp.update_date as updatetime,0 as due,ipp.id,ipp.pending_paid_date as doa,c.name as client_name,c.cont,ipp.pending_payment_received as paid,3 as type,0 as advance from `invoice_pending_payment` ipp "
					. " LEFT JOIN `client` c on c.id=ipp.client_id"
					. " where ipp.status=1 and ipp.pending_paid_date between '".$sql_start_date_invoice."' and '".$date_to_previousday."' and ipp.paid_at_branch='".$branch_id."' "
					. " UNION SELECT date,0 as due,w.id,w.date doa,c.name as client_name,c.cont,w.wallet_amount as paid,4 as type,0 as advance from `wallet` w "
					. " LEFT JOIN `client` c on c.id=w.client_id "
					. " where w.status=1 and w.date between '".$sql_start_date_invoice."' and '".$date_to_previousday."' and w.branch_id='".$branch_id."' order by updatetime asc";
				// 	echo $sql_opening;
					$result_sql_opening = query_by_id($sql_opening,[],$conn);
					foreach($result_sql_opening as $row_opening)
					{ 
					    $paid =($row_opening['type']=='1')?get_cash($row_opening['id']):$row_opening['paid'];
					    if($paid>0){
							$Prevopening_balnce += $paid;
					    }
					} 
					$prevExpense = 0;
					$sql1="SELECT date, amount, 'expense' as type from expense where active=0 and date between '".$sql_start_date_invoice."' and '".$date_to_previousday."' and branch_id='".$branch_id."'"
					." UNION ALL SELECT dop as date, paid as amount, 'stock' as type FROM purchase WHERE active='0' AND branch_id='".$branch_id."' AND dop BETWEEN '".$sql_start_date_invoice."' AND '".$date_to_previousday."'"
					." UNION ALL SELECT p.date as date, p.paid as amount, 'pending payment' as type FROM payments p WHERE p.active='0' AND p.branch_id='".$branch_id."' AND p.date BETWEEN '".$sql_start_date_invoice."' AND '".$date_to_previousday."' order by date asc";
				// 	echo $sql1;
					$result1=query_by_id($sql1,[],$conn);
					foreach($result1 as $row1) {
						$prevExpense+=$row1['amount'];
					}
					$prevBalance = $Prevopening_balnce-$prevExpense;
                                        
					?>
					<td><strong>Opening Balance: </strong></td>
					<td><strong><?= number_format($prevBalance,2) ?></strong></td>
					<td>&nbsp;</td>
				</tr>
				<tr>
					<td colspan="6"><strong>Amount Received</strong></td>
				</tr>
				<tr>
					<td><strong>Date</strong></td>
					<td><strong>Branch</strong></td>
					<td><strong>Invoice id</strong></td>
					<td><strong>Client name</strong></td>
					<td><strong>Client contact</strong></td>
					<td><strong>Advance received</strong></td>
					<td><strong>Pending payment</strong></td>
					<td><strong>Amount received</strong></td>
					<td><strong>Type</strong></td>
				</tr>
				<?php
					$received = 0;
					$sql1="SELECT i.branch_id as branch, updatetime,i.due,i.id,i.doa,c.name as client_name,c.cont,mpm.amount_paid as paid,1 as type,i.advance from `invoice_".$branch_id."` i "
					. " LEFT JOIN `client` c on c.id=i.client"
					. " LEFT JOIN `multiple_payment_method` mpm on mpm.invoice_id=CONCAT('bill,', i.id)"
					. " where i.active=0 and i.doa between '".$from_date."' and '".$to_date."' and i.branch_id='".$branch_id."' and mpm.branch_id='".$branch_id."' "
					. " UNION SELECT ai.branch_id as branch, updatetime,ai.due,ai.id,ai.doa,c.name as client_name,c.cont,mpm.amount_paid as paid,2 as type,0 as advance from `app_invoice_".$branch_id."` ai"
					. " LEFT JOIN `client` c on c.id=ai.client "
					. " LEFT JOIN `multiple_payment_method` mpm on mpm.invoice_id=CONCAT('app,', ai.id)"
					. " where ai.active=0 and ai.appdate between '".$from_date."' and '".$to_date."' and ai.branch_id='".$branch_id."' and mpm.branch_id='".$branch_id."'"
					. " UNION SELECT ipp.branch_id as branch, ipp.update_date as updatetime,0 as due,ipp.id,ipp.pending_paid_date as doa,c.name as client_name,c.cont,ipp.pending_payment_received as paid,3 as type,0 as advance from `invoice_pending_payment` ipp "
					. " LEFT JOIN `client` c on c.id=ipp.client_id"
					. " where ipp.status=1 and ipp.paid_at_branch='".$branch_id."' and ipp.pending_paid_date between '".$from_date."' and '".$to_date."'"
					. " UNION SELECT w.branch_id as branch,0 as updatetime,0 as due,w.id, DATE(w.time_update) doa,c.name as client_name,c.cont,w.paid_amount as paid,4 as type,0 as advance from `wallet_history` w "
					. " LEFT JOIN `client` c on c.id=w.client_id "
					. " where w.status=1 and DATE(w.time_update) between '".$from_date."' and '".$to_date."' and c.branch_id='".$branch_id."' and w.transaction_type='1' order by updatetime asc";
                    // echo $sql1;
					$result1=query_by_id($sql1,[],$conn);
					if($result1){
						$received=0;
						foreach($result1 as $row1) {
							$paid =($row1['type']=='1')?get_cash($row1['id']):$row1['paid'];
								$received+=$paid;
							?>
							<tr>
								<td><?= my_date_format($row1['doa']); ?></td>
								<td><?= ucfirst(branch_by_id($row1['branch'])); ?></td>
								<td>INV <?=sprintf('%04d',$row1['id']); ?></td>
								<td><?= ucfirst($row1['client_name']); ?></td>
								<td><?= $row1['cont']; ?></td>
								<td><?= number_format($row1['advance'],2); ?></td>
								<td><?= number_format(($row1['due']>0)?$row1['due']:'0',2); ?></td>
								<td><?= number_format($paid,2) ?></td>
								<td><?php 
    								if($row1['type']=='1'){ echo 'Bill'; }
    								else if($row1['type']=='2'){ echo 'Appointment'; }
    								else if($row1['type']=='3'){ echo 'Pending payment'; }
    								else if($row1['type']=='4'){ echo 'Wallet'; }
    								else {} 
								?></td>
							</tr>
						<?php } } else {
						    echo "<tr><td class='text-center' colspan='9'>No result found!</td></tr>";
						}  ?>
						<tr style="background-color: #ddd;">
							<td colspan="6">&nbsp;</td>
							<td><strong>Total Received:</strong></td>
							<td><strong><?= number_format($received,2) ?></strong></td>
							<td>&nbsp;</td>
						</tr>
						<tr>
							<td colspan="9"><strong>Expenses</strong></td>
						</tr>
						<tr>
							<td><strong>Date</strong></td>
                            <td colspan="6"><strong>Category</strong></td>
							<td><strong>Amount paid</strong></td>
							<td><strong>Paid by</strong></td>
            				</tr>					
							<?php
								
								$expaid = 0;
								$sql1="SELECT e.date as date, e.cat as cat, e.amount as amount, e.user as user, 'Expense' as type from expense e where e.active=0 and e.date between '".$from_date."' and '".$to_date."' and e.branch_id='".$branch_id."'"
									." UNION ALL SELECT dop as date, 0 as cat, paid as amount, 0 as user, 'Stock purchase' as type  FROM purchase WHERE active='0' AND branch_id='".$branch_id."' AND dop BETWEEN '".$from_date."' AND '".$to_date."'"
								    ." UNION ALL SELECT p.date as date, 0 as cat, p.paid as amount, 0 as user, 'Stock purchase (pending payment)' as type FROM payments p WHERE p.active='0' AND p.branch_id='".$branch_id."' AND p.date BETWEEN '".$from_date."' AND '".$to_date."' order by date asc";
								$result1=query_by_id($sql1,[],$conn);
								if($result1){
    								foreach($result1 as $row1) {
    								?>
    								<tr>
    									<td><?php echo my_date_format($row1['date']); ?></td>
    									<td colspan="2"><?php 
    										if($row1['type'] == 'Expense'){
    											echo getcat($row1['cat']).' - '.$row1['type'];
    										} else {
    											echo $row1['type'];
    										} ?></td>
    									<td colspan="4">&nbsp;</td>
    									<td><?php $expaid += (int)$row1['amount']; echo number_format($row1['amount']); ?></td>
    									<td><?php if($row1['type'] == 'Expense'){
    										echo get_user($row1['user']); 
    									} else {
    										echo '-';
    									} ?></td>
    								</tr>
							    <?php } } else {
							        echo "<tr><td class='text-center' colspan='10'>No result found!</td></tr>";
							    } ?>
							<tr style="background-color: #ddd;">
    							<td colspan="6">&nbsp;</td>
								<td><strong>Total Paid:</strong></td>
								<td><strong><?= number_format($expaid,2) ?></strong></td>
								<td>&nbsp;</td>
							</tr>
						<tr style="background-color: #ddd;">
							<td colspan="6">&nbsp;</td>
							<td><strong>Closing Balance:</strong></td>
							<td><strong><?= number_format($prevBalance + $received -$expaid,2); ?> /-</strong></td>
							<td>&nbsp;</td>
						</tr>

			</tbody>
		</table>
		
	</div>

<?php } ?>





<?php 
	function get_cash($id){
		global $conn,$payment_method;
		global $branch_id;
		$paid=0;
                if($payment_method != "All"){
		$sql="SELECT amount_paid From`multiple_payment_method` where invoice_id=CONCAT('bill,',$id) and status='1' and payment_method='$payment_method' and branch_id='".$branch_id."'";
                }else{
		$sql="SELECT amount_paid From`multiple_payment_method` where invoice_id=CONCAT('bill,',$id) and status='1' and branch_id='".$branch_id."'";
                    
                }
		$result = query_by_id($sql,[],$conn);
		if($result){
			foreach($result as $row){
				$paid+=$row['amount_paid'];
			}
		}
		return $paid;
	}	
		function getcat($cid) {
		global $conn;
		global $branch_id;
		$sql="SELECT * from expensecat where id='$cid' and branch_id='".$branch_id."'";
		$result=query_by_id($sql,[],$conn);
		foreach($result as $row) 
		{
			return $row['title'];
		}
	}
	function get_user($user){
		global $conn;
		global $branch_id;
		$sql="SELECT * from user where id=$user and branch_id='".$branch_id."' order by id desc";
		$result=query_by_id($sql,[],$conn);
		foreach($result as $row) 
		{
			return $row['name'];
		}
	}
	//sleep(5);
?>	
