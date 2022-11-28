<?php 
include "../includes/db_include.php";
$branch_id = $_SESSION['branch_id'];
if(isset($_POST['data'])){
	$array=json_decode($_POST['data'],true);
	$date = date('y-m-d');
	$result = array();
	foreach($array as $v){
	    $sql=query("insert into `invoice_pending_payment` SET pending_payment_received='".$v['paid_amount']."',iid='".$v['invoice_id']."',payment_method='".$v['payment_method']."',update_date='$date',pending_paid_date='".$v['paid_date']."',client_id='".$v['client_id']."', branch_id='".$branch_id."'",[],$conn);
	}
	echo '1';
}
	
// check pending paymemnt on appointment and billing

if(isset($_POST['action']) && $_POST['action'] == 'check_pending_payments'){
    $client_id = $_POST['client_id'];
    $branch_count = 1;
    $sql = '';
    $total_branches = query_by_id("SELECT id FROM salon_branches WHERE status='1'",[],$conn);
	foreach($total_branches as $branch){
		$sql .= "SELECT c.id as client_id,c.name,c.cont,i.*,i.id as iid,i.due as pending, i.doa as bdate, 'Bill' as itype from `invoice_".$branch['id']."` i "
	      ."LEFT JOIN `client` c on c.id=i.client where i.active=0 AND i.client = '".$client_id."' AND i.due > 0 and i.branch_id='".$branch['id']."'";
	    if($branch_count < count($total_branches)){
	    	$sql .= " UNION ";
	    }
	 	$branch_count++;
	}
	$result = query_by_id($sql,[],$conn);
	if(count($result) > 0){ ?>
	    <div class="modal-header">
			<h4 class="modal-title">Pending payments</h4>
		</div>
		<div class="modal-body">
			<div class="row">
			    <div class="col-md-12 table-responsive">
			        <table class="table table-bordered">
			            <thead>
			                <tr>
			              		<td>Branch</td>
			                    <td style="text-align:center;"><strong>Id</strong></td>
			                    <td><strong>Date</strong></td>
			                    <td><strong>Type</strong></td>
			                    <td><strong>Pending amount</strong></td>
			                    <td><strong>Pay amount</strong></td>
			                    <td><strong>Pay mode</strong></td>
			                    <td><strong>Action</strong></td>
			                </tr>
			            </thead>
			            <tbody>
			                <?php foreach($result as $row) { 
                        		if($row['pending'] - get_pending_payment($row['client_id'], $row['id'], $row['branch_id']) > 0){	
                        		    $ppayment = $row['pending'] - get_pending_payment($row['client_id'], $row['id'], $row['branch_id']);
                        		?>
                            		<tr>
                            			<td><?= ucfirst(branch_by_id($row['branch_id'])); ?></td>
                            			<td style="text-align:center;vertical-align:middle;"><?= $row['id']; ?></td>
                            			<td style="vertical-align:middle;"><?= my_date_format($row['bdate']); ?></td>
                            			<td style="vertical-align:middle;"><?= $row['itype']; ?></td>
                            			<td style="vertical-align:middle;" class="pri"><?= number_format($ppayment,2); ?></td>
                            			<td>
                            			    <input type="number" class="form-control amtpay" onkeyup="maxpendpayment('<?= $ppayment ?>',this.value, $(this))" onblur="maxpendpayment('<?= $ppayment ?>',this.value, $(this))" min="0" value="0" />
                            			    <input type="hidden" class="form-control pendtotal" min="0" value="<?= $ppayment ?>" />
                            			    <input type="hidden" class="form-control clientid" min="0" value="<?= $row['client_id']; ?>" />
                            			    <input type="hidden" class="form-control inv_id" min="0" value="<?= $row['id']; ?>" />
                            			    <input type="hidden" class="form-control branch_id" min="0" value="<?= $row['branch_id']; ?>" />
                            			</td>
                            			<td><select class="form-control mthd" onchange="pendingpaymode(this.value,$(this))">
                            			    <?php
                                                $sql_pay_mode="Select * FROM `payment_method` where status='1'";
												$result_pay_mode =query_by_id($sql_pay_mode,[],$conn);
												foreach($result_pay_mode as $row_pay_mode){
												?>
												    <option value="<?=$row_pay_mode['id']?>"><?=$row_pay_mode['name']?></option> 
											<?php } ?>
                            			?></select></td>
                            			<td>
                            			    <button type="button" onclick="pendingPayment($(this))" class="btn btn-success"><i class="fa fa-money" aria-hidden="true"></i>Pay now</button>
                            			</td>
                            		</tr>
                            	<?php  }  
                        	} ?>
			            </tbody>
			        </table>
			    </div>
			</div>
		</div>
		<div class="modal-footer">
			<button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times" aria-hidden="true"></i>Close</button>
		</div>
	<?php } else {
	    echo 0;
	}
}


// check pending paymemnt on appointment and billing (follow up page)

if(isset($_POST['action']) && $_POST['action'] == 'check_pending_payments_fup'){
    $client_id = $_POST['client_id'];
    $bid = $_POST['branch_id'];
    $sql = "SELECT c.id as client_id,c.name,c.cont,i.*,i.id as iid,i.due as pending, i.doa as bdate, 'Bill' as itype from `invoice_".$bid."` i "
          ."LEFT JOIN `client` c on c.id=i.client where i.active=0 AND i.client = '".$client_id."' AND i.due > 0 and i.branch_id='".$bid."' order by i.id DESC";
	$result = query_by_id($sql,[],$conn);
	if(count($result) > 0){ ?>
	    <div class="modal-header">
			<h4 class="modal-title">Pending payments</h4>
		</div>
		<div class="modal-body">
			<div class="row">
			    <div class="col-md-12 table-responsive">
			        <table class="table table-bordered">
			            <thead>
			                <tr>
			                    <td style="text-align:center;"><strong>Id</strong></td>
			                    <td><strong>Date</strong></td>
			                    <td><strong>Type</strong></td>
			                    <td><strong>Pending amount</strong></td>
			                    <td><strong>Pay amount</strong></td>
			                    <td><strong>Pay mode</strong></td>
			                    <td><strong>Action</strong></td>
			                </tr>
			            </thead>
			            <tbody>
			                <?php foreach($result as $row) { 
                        		if($row['pending'] - get_pending_payment($row['client_id'], $row['id'], $bid) > 0){	
                        		    $ppayment = $row['pending'] - get_pending_payment($row['client_id'], $row['id'], $bid);
                        		?>
                            		<tr>
                            			<td style="text-align:center;vertical-align:middle;"><?= $row['id']; ?></td>
                            			<td style="vertical-align:middle;"><?= my_date_format($row['bdate']); ?></td>
                            			<td style="vertical-align:middle;"><?= $row['itype']; ?></td>
                            			<td style="vertical-align:middle;" class="pri"><?= number_format($ppayment,2); ?></td>
                            			<td>
                            			    <input type="number" class="form-control amtpay" onkeyup="maxpendpayment_fup('<?= $ppayment ?>',this.value, $(this))" onblur="maxpendpayment('<?= $ppayment ?>',this.value, $(this))" min="0" value="0" />
                            			    <input type="hidden" class="form-control pendtotal" min="0" value="<?= $ppayment ?>" />
                            			    <input type="hidden" class="form-control clientid" min="0" value="<?= $row['client_id']; ?>" />
                            			    <input type="hidden" class="form-control inv_id" min="0" value="<?= $row['id']; ?>" />
                            				<input type="hidden" class="form-control branch_id" min="0" value="<?= $row['branch_id']; ?>">
                            			</td>
                            			<td><select class="form-control mthd" onchange="pendingpaymode_fup(this.value,$(this))">
                            			    <?php
                                                $sql_pay_mode="Select * FROM `payment_method` where status='1'";
												$result_pay_mode =query_by_id($sql_pay_mode,[],$conn);
												foreach($result_pay_mode as $row_pay_mode){
												?>
												    <option value="<?=$row_pay_mode['id']?>"><?=$row_pay_mode['name']?></option> 
											<?php } ?>
                            			?></select></td>
                            			<td>
                            			    <button type="button" onclick="pendingPayment_fup($(this))" class="btn btn-success"><i class="fa fa-money" aria-hidden="true"></i>Pay now</button>
                            			</td>
                            		</tr>
                            	<?php  }  
                        	} ?>
			            </tbody>
			            <input type="hidden" class="wallet_money" value="<?= client_wallet($row['client_id']) ?>" />
        			    <input type="hidden" class="reward_point" value="<?= get_reward_points($row['client_id']) ?>" />
			        </table>
			    </div>
			</div>
		</div>
		<div class="modal-footer">
			<button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times" aria-hidden="true"></i>Close</button>
		</div>
	<?php } else {
	    echo 0;
	}
}

// apply pending payment

if(isset($_POST['action']) && $_POST['action'] == 'apply_pending_payment'){
    $client = $_POST['client_id'];
    $inv_id = $_POST['inv_id'];
    $method = $_POST['method'];
    $amount = $_POST['amount'];
    $bid = $_POST['branch_id'];
    $res = array();
    if($client != '' && $inv_id != '' && $method != '' && $amount != '' && $bid != ''){
        if($method == 7){
    		query("INSERT INTO `wallet_history` (`client_id`,`iid`,`transaction_type`,`wallet_amount`,`get_wallet_from`,`status`,`branch_id`, `paid_of_branch`) VALUES ('$client','bill,$inv_id',0,'$amount','Bill (pending payment)',1,'$branch_id','$bid')",[],$conn);
    		
    		query("UPDATE `wallet` set `wallet_amount`= (wallet_amount-'$amount') WHERE `client_id` = '$client'",[],$conn);
    	}
    	if($method == 9){
    		$rpoint = redeempoint(1);
    		$points = $amount*($rpoint);
    		query("INSERT INTO `customer_reward_points` SET `invoice_id`='bill,$inv_id',`client_id`='cust,$client',`points_on`='0',`point_type`='2',`points`='$points',`notes`='Points redeem at the time of invoice pending payment.',`status`='1', `branch_id`='".$branch_id."'",[],$conn);
    	}
    	$sql = get_insert_id("insert into `invoice_pending_payment` SET pending_payment_received='".$amount."',iid='".$inv_id."',payment_method='".$method."',update_date='".date('Y-m-d')."',pending_paid_date='".date('Y-m-d')."',client_id='".$client."', branch_id='".$bid."', paid_at_branch = '".$branch_id."'",[],$conn);
        if($sql > 0){
            $res['status'] = '1';
            $res['msg'] = 'success';
            $message = "Dear ".get_client_name($client, $conn,'fullname')."\nYou have paid ".CURRENCY." ".number_format($amount,2)." via ".pay_method_name($method)." to ".systemname()."\nThank you!";
            $phone = client_profile($client);
            send_sms($phone['cont'],$message);
        } else {
            $res['status'] = '2';
            $res['msg'] = 'failed';
        }
    } else {
        $res['status'] = '3';
        $res['msg'] = 'failed';
    }
	echo json_encode($res);
}


// Single invoice pending payment

if(isset($_POST['action']) && $_POST['action'] == 'invoice_pending_payment'){
    $invoice_id = $_POST['invoice_id'];
    $bid = $_POST['branch_id'];
    $sql = "SELECT c.id as client_id,c.name,c.cont,i.*,i.id as iid,i.due as pending, i.doa as bdate, 'Bill' as itype from `invoice_".$bid."` i "
          ."LEFT JOIN `client` c on c.id=i.client where i.active=0 AND i.id = '".$invoice_id."' AND i.due > 0 and i.branch_id='".$bid."' order by i.id DESC";
	$result = query_by_id($sql,[],$conn);
	if(count($result) > 0){ ?>
	    	    <div class="modal-header">
			<h4 class="modal-title">Pending payments</h4>
		</div>
		<div class="modal-body">
			<div class="row">
			    <div class="col-md-12">
			        <table class="table table-bordered table-reponsive">
			            <thead>
			                <tr>
			                    <td style="text-align:center;"><strong>Id</strong></td>
			                    <td><strong>Date</strong></td>
			                    <td><strong>Type</strong></td>
			                    <td><strong>Pending amount</strong></td>
			                    <td><strong>Pay amount</strong></td>
			                    <td><strong>Pay mode</strong></td>
			                    <td><strong>Action</strong></td>
			                </tr>
			            </thead>
			            <tbody>
			                <?php foreach($result as $row) { 
                        		if($row['pending'] - get_pending_payment($row['client_id'], $row['id'], $bid) > 0){	
                        		    $ppayment = $row['pending'] - get_pending_payment($row['client_id'], $row['id'], $bid);
                        		?>
                            		<tr>
                            			<td style="text-align:center;vertical-align:middle;"><?= $row['id']; ?></td>
                            			<td style="vertical-align:middle;"><?= my_date_format($row['bdate']); ?></td>
                            			<td style="vertical-align:middle;"><?= $row['itype']; ?></td>
                            			<td style="vertical-align:middle;" class="pri"><?= number_format($ppayment,2); ?></td>
                            			<td>
                            			    <input type="number" class="form-control amtpay" onkeyup="maxpendpayment('<?= $ppayment ?>',this.value, $(this))" min="0" value="0" />
                            			    <input type="hidden" class="form-control pendtotal" min="0" value="<?= $ppayment ?>" />
                            			    <input type="hidden" class="form-control clientid" min="0" value="<?= $row['client_id']; ?>" />
                            			    <input type="hidden" class="form-control inv_id" min="0" value="<?= $row['id']; ?>" />
                            			    <input type="hidden" class="form-control branch_id" min="0" value="<?= $row['branch_id']; ?>" />
                            			</td>
                            			<td><select class="form-control mthd" onchange="pendingpaymode(this.value,$(this))">
                            			    <?php
                                                $sql_pay_mode="Select * FROM `payment_method` where status='1'";
												$result_pay_mode =query_by_id($sql_pay_mode,[],$conn);
												foreach($result_pay_mode as $row_pay_mode){
												?>
												    <option value="<?=$row_pay_mode['id']?>"><?=$row_pay_mode['name']?></option> 
											<?php } ?>
                            			?></select></td>
                            			<td>
                            			    <input type="hidden" id="wallet_money" value="<?= client_wallet($row['client_id']) ?>" />
                            			    <input type="hidden" id="reward_point" value="<?= get_reward_points($row['client_id']) ?>" />
                            			    <button type="button" onclick="pendingPaymentSingleInvoice($(this))" class="btn btn-success"><i class="fa fa-money" aria-hidden="true"></i>Pay now</button>
                            			</td>
                            		</tr>
                            	<?php  }  
                        	} ?>
			            </tbody>
			        </table>
			    </div>
			</div>
		</div>
		<div class="modal-footer">
			<button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times" aria-hidden="true"></i>Close</button>
		</div>
	<?php
	} else {
	    echo 0;
	}
}

?>	