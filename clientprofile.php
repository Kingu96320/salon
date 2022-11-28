<?php
	include "./includes/db_include.php";
    $branch_id = $_SESSION['branch_id'];
	if(isset($_GET['cid'])){
		$cid = $_GET['cid'];
		$client_sql=query_by_id("SELECT * from client where id=$cid and branch_id='".$branch_id."'",[],$conn)[0];
	}

    if(isset($_GET['cid']) && isset($_GET['bid']) && $_GET['bid']!=''){
        $cid = $_GET['cid'];
        $bid = decrypt_url($_GET['bid']);
        $client_sql=query_by_id("SELECT * from client where id=$cid and branch_id='".$bid."'",[],$conn)[0];
    }
	
	if(isset($_POST['changeprofile'])){
	    $cid                = addslashes(trim($_POST['client_id']));
		$client             = addslashes(trim($_POST['name']));
		$cont             = addslashes(trim($_POST['usermobile']));
		$dob 	            = addslashes(trim($_POST['dob']));
		$aniv 	            = addslashes(trim($_POST['anniv']));
		$gender             = addslashes(trim($_POST['gender']));
		$email 	            = addslashes(trim($_POST['email']));
		$addr 	            = addslashes(trim($_POST['address']));
		$leadsource 	    = addslashes(trim($_POST['leadsource']));
    	query("UPDATE `client` SET `name`=:name,`cont` =:cont, `aniversary`=:anivesary,`email`=:email,`address`=:address, `gender`=:gender,`dob`=:dob,`leadsource`=:leadsource,`active`=:active WHERE id=:id and branch_id='".$branch_id."'",[
            'name'=>$client,
            'cont'=>$cont,
            'email'=>$email,
            'address'=>$addr,
            'gender'=>$gender,
            'dob'=>$dob,
            'anivesary'=>$aniv,
            'active'=>0,
            'leadsource' => $leadsource,
            'id'=>$cid],$conn);
		$_SESSION['t']  = 1;
		$_SESSION['tmsg']  = "Profile Updated Successfully";
		echo '<meta http-equiv="refresh" content="0; url=clientprofile.php?cid='.$cid.'" />';die();
	}
	$service_worth_purchased = 0;
	$current_date= my_date_format(date('Y-m-d'));
	
	include "topbar.php";
	include "header.php";
	include "menu.php";
?>

<!-- Dashboard wrapper starts -->
<div class="dashboard-wrapper" style="margin:0px;">
	<!-- Main container starts -->
	<div class="main-container">
		<ul class="nav nav-tabs">
            <li class="active"><a data-toggle="tab" href="#profile">Profile - <?= ucfirst($client_sql['name']) ?></a></li>
            <li><a data-toggle="tab" href="#appointment_history">Appointment</a></li>
            <li><a data-toggle="tab" href="#billing">Billing</a></li>
            <li><a data-toggle="tab" href="#reward_point_history">Reward point</a></li>
            <li><a data-toggle="tab" href="#payment_history">Payment</a></li>
            <li><a data-toggle="tab" href="#package_history">Package</a></li>
            <li><a data-toggle="tab" href="#membership_history">Membership</a></li>
            <li><a data-toggle="tab" href="#wallet_history">Wallet</a></li>
            <li><a data-toggle="tab" href="#feedback_rating">Feedback & rating</a></li>
        </ul>
        <div class="tab-content">
            <!-- Profile tab-->
            <div id="profile" class="tab-pane fade in active">
                <div class="row gutter">
        			<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        				<div class="panel">
        					<div class="panel-heading">
        						<h4><?php echo ucfirst($client_sql['name']); ?> - client since <?php echo my_date_format($client_sql['doj']); ?></h4>
        					</div>
        					<form action="" method="post">
        				        <div class="row">
        					        <div class="panel-body">
            						<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
            							<div class="form-group">
            								<label for="userName">Client name <span class="text-danger">*</span></label>
            								<input type="text" name="name" value="<?php echo $client_sql['name']; ?>" class="form-control" id="userName" placeholder="Client" required>
            							</div>
            						</div>
            						
            						<div class="col-lg-3 col-md-2 col-sm-2 col-xs-12">
            							<div class="form-group">
            								<label for="userName">Contact number</label>
            								<input type="text" value="<?php echo $client_sql['cont']; ?>" class="form-control" id="usermobile" name="usermobile" placeholder="Mobile Number">
            							</div>
            						</div>
            						<div class="col-lg-3 col-md-2 col-sm-2 col-xs-12">
            							<div class="form-group">
            								<label for="userName">Email ID</label>
            								<input type="text" name="email" value="<?php echo $client_sql['email']; ?>" class="form-control" id="userName" placeholder="Email">
            							</div>
            						</div>
            						
            						<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
            							<div class="form-group">
            								<label for="userName">Gender</label>
            								<select name="gender" data-validation="required" class="form-control">
            								    <option value="">--Select--</option>
            									<option value="1" <?php if($client_sql['gender']=="1") echo "selected"; ?>>Male</option>
            									<option value="2" <?php if($client_sql['gender']=="2") echo "selected"; ?>>Female</option>                                    
            									
            								</select>
            							</div>
            						</div>
            						
            						<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
            							<div class="form-group">
            								<label for="userName">Date of birth</label>
            								<input type="text" name="dob" value="<?= $client_sql['dob']!='0000-00-00'?$client_sql['dob']:'' ?>" class="form-control dob_annv_date" id="userName" readonly>
            							</div>
            						</div>
            						<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
            							<div class="form-group">
            								<label for="userName">Anniversary</label>
            								<input type="text" name="anniv" value="<?= $client_sql['aniversary']!='0000-00-00'?$client_sql['aniversary']:'' ?>" class="form-control dob_annv_date" id="userName" readonly>
            							</div>
            						</div>
            						
            						<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
            							<div class="form-group">
            								<label for="userName">Address</label>
            								<input type="text" name="address" value="<?php echo $client_sql['address']; ?>" class="form-control" id="userName" placeholder="Address">
            							</div>
            						</div>
            						
            						<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
            							<div class="form-group">
            								<label for="userName">Source of client</label>
            								<select class="form-control" name="leadsource" required>
            									<option value="">-- Select A Type --</option>
            									<option <?= $client_sql['leadsource']=='Client refrence'?'selected':''; ?> value="Client refrence">Client refrence</option>
            									<option <?= $client_sql['leadsource']=='Cold Calling'?'selected':''; ?> value="Cold Calling">Cold Calling</option>
            									<option <?= $client_sql['leadsource']=='Facebook'?'selected':''; ?> value="Facebook">Facebook</option>
            									<option <?= $client_sql['leadsource']=='Twitter'?'selected':''; ?> value="Twitter">Twitter</option>
            									<option <?= $client_sql['leadsource']=='Instagram'?'selected':''; ?> value="Instagram">Instagram</option>
            									<option <?= $client_sql['leadsource']=='Other Social Media'?'selected':''; ?> value="Other Social Media">Other Social Media</option>
            									<option <?= $client_sql['leadsource']=='Website'?'selected':''; ?> value="Website">Website</option>
            									<option <?= $client_sql['leadsource']=='Walk-In'?'selected':''; ?> value="Walk-In">Walk-In</option>
            									<option <?= $client_sql['leadsource']=='Flex'?'selected':''; ?> value="Flex">Flex</option>
            									<option <?= $client_sql['leadsource']=='Flyer'?'selected':''; ?> value="Flyer">Flyer</option>
            									<option <?= $client_sql['leadsource']=='Newspaper'?'selected':''; ?> value="Newspaper">Newspaper</option>
            									<option <?= $client_sql['leadsource']=='SMS'?'selected':''; ?> value="SMS">SMS</option>
            									<option <?= $client_sql['leadsource']=='Street Hoardings'?'selected':''; ?> value="Street Hoardings">Street Hoardings</option>
            									<option <?= $client_sql['leadsource']=='Event'?'selected':''; ?> value="Event">Event</option>
            									<option <?= $client_sql['leadsource']=='TV/Radio'?'selected':''; ?> value="TV/Radio">TV/Radio</option>
            								</select>
            							</div>
            						</div>
            						<div class="clearfix"></div>
                                    <?php if(!isset($_GET['bid'])){ ?>
            						<div class="col-md-12">
            						    <input type="hidden" value="<?= $_GET['cid']; ?>" name="client_id" />
            						    <button type="submit" name="changeprofile" class="btn btn-info pull-right">
            						        <i class="fa fa-pencil-square-o" aria-hidden="true"></i>Update profile</button>
            						</div>
                                    <?php } ?>
            					   </div>
        				        </div>
        				    </form>
        				</div>
        			</div>
        		</div>
            </div>
            
            <!--Appointment history-->
            <div id="appointment_history" class="tab-pane fade">
                <div class="row gutter">
        			<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        				<div class="panel">
        					<div class="panel-heading">
        						<h4>Appointment history</h4>
        					</div>
        					<div class="panel-body">
        						<div class="table-responsive">
        							<table class="table table-bordered grid order_date_desc no-margin">
        								<thead>
        									<tr>
        										<th>Date</th>
                                                <th>Branch</th>
        										<th>Appointment ID</th>
        										<th>Source</th>
        										<th>Amount payable</th>
        										<th>Advance paid</th>
        										<th>Status</th>
        										<th>Remarks</th>
                                                <?php if(!isset($_GET['bid'])){ ?>
        										<th>Action</th>
                                            <?php } ?>
        									</tr>
        								</thead>
        								<tbody>
        									<?php
                                                $total_branches = query_by_id("SELECT id FROM salon_branches WHERE status='1'",[],$conn);
                                                foreach($total_branches as $branch){ 
                										$sql1="SELECT pm.name as pay_method_name,ai.*,aso.name as app_source from `app_invoice_".$branch['id']."` ai"
                										." LEFT JOIN `app_source` aso on aso.id=ai.role "
                										." LEFT JOIN `payment_method` pm on pm.id = ai.pay_method "
                										." where ai.client='$cid' and ai.active=0 and ai.type=1 and ai.branch_id='".$branch['id']."' order by `id` desc";
                										$result1=query_by_id($sql1,[],$conn);
                										foreach($result1 as $row1) {
                										?>
                										<tr>
                											<td><?php echo my_date_format($row1['doa']); ?></td>
                                                            <td><?php echo ucfirst(branch_by_id($branch['id'])); ?></td>
                											<td><?php echo $row1['id']; ?></td>
                											<td><?php echo $row1['app_source']; ?></td>
                											<td><?php echo number_format($row1['total'],2); ?></td>
                											<td><?php echo number_format($row1['paid'],2); ?></td>
                											<td><?php echo $row1['pay_method_name']; ?></td>
                											<td><?php echo $row1['notes']; ?></td>
                                                            <?php if(!isset($_GET['bid'])){ ?>
                											<td>
                												<?php //if($row1['ss_created_status'] == 0){ ?>
                													<!--<a href="service-slip.php?sid=<?=$row1['id']?>" class="btn btn-xs btn-warning">Create service slip</a>-->
                													<?php //}
                													if($row1['bill_created_status'] == 0){ ?>
                													<a href="appointment.php?id=<?=$row1['id']?>"><button class="btn btn-xs btn-warning"><i class="fa fa-pencil-square-o" aria-hidden="true"></i> Edit</button></a>
                													<a href="billing.php?bid=<?=$row1['id']?>"><button class="btn btn-xs btn-primary"><i class="fa fa-money" aria-hidden="true"></i>Create bill</button></a>
                													<!--<a href="#"><button class="btn btn-info btn-xs" type="button"><i class="fa fa-envelope-o" aria-hidden="true"></i>SMS reminder</button></a></td>-->
                													<?php }else{ ?>
                													<a href="appointment.php?id=<?=$row1['id']?>"><button class="btn btn-xs btn-info"><i class="fa fa-eye" aria-hidden="true"></i> View</button></a>
                												    <button class="btn btn-xs btn-success"><i class="fa fa-money" aria-hidden="true"></i>Bill paid</button>
                                                                <?php } ?>
                											</td>
                                                        <?php } ?>
                										</tr>
        										    <?php
                                                }   
                							}
                							?>
                						</tbody>
                					</table>
                				</div>
                			</div>
                		</div>
                	</div>
                </div>
            </div>
            
            <!--Billing history-->
            <div id="billing" class="tab-pane fade">
                <div class="row gutter">
                	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                		<div class="panel">
                			<div class="panel-heading">
                				<h4>Billing history</h4>
                			</div>
                			<div class="panel-body">
                				<div class="table-responsive">
                					<table class="table order_date_desc grid table-bordered no-margin">
                						<thead>
                							<tr>
                                                <th>Date</th>
                								<th>Branch</th>
                								<th>Bill id</th>
                								<th>Amount</th>
                								<th>Advance</th>
                								<th>Paid</th>
                								<th>Pending</th>
                								<th>Installment paid</th>
                								<th>Earned points</th>
                                                <?php if(!isset($_GET['bid'])){ ?>
                								<th>Action</th>
                                                <?php } ?>
                							</tr>
                						</thead>
                						<tbody>
                							
                							<?php
                                                $total_branches = query_by_id("SELECT id FROM salon_branches WHERE status='1'",[],$conn);

                                                foreach($total_branches as $branch){

                    								$sql1="SELECT * from invoice_".$branch['id']." where client='$cid' and invoice = '1' and active='0'  and branch_id='".$branch['id']."' order by `id` desc";
                    								$result1=query_by_id($sql1,[],$conn);
                    								foreach($result1 as $row1) {
                    								?>
                    								<tr>
                                                        <td><?php echo my_date_format($row1['doa']); ?></td>
                    									<td><?php echo ucfirst(branch_by_id($branch['id'])); ?></td>
                    									<td><?php echo $row1['id']; ?></td>
                    									<td><?php echo number_format($row1['total'],2); ?></td>
                    									<td><?php echo number_format($row1['advance'],2); ?></td>
                    									<td><?php echo number_format($row1['paid'],2); ?></td>
                    									<td><?php echo number_format($row1['due'],2); ?></td>
                    									<td>
                    									    <?php
                    									        $ins_paid = query_by_id("SELECT SUM(pending_payment_received) as payment FROM invoice_pending_payment WHERE iid='".$row1['id']."' and branch_id='".$branch['id']."' and status='1'",[],$conn)[0];
                    									        echo number_format($ins_paid['payment'],2);
                    									    ?>
                    									</td>
                    									<td><?php echo get_reward_points($cid,'',$row1['id'], $branch['id']); ?></td>
                    									<?php if(!isset($_GET['bid'])){ ?>
                                                        <td>
                    									    <?php 
                    									        if($row1['due']-$ins_paid['payment'] == 0){
                    									            echo '<button type="button" class="btn btn-success btn-xs"><i class="fa fa-check" aria-hidden="true"></i>Paid</button>';
                    									        } else {
                    									            echo '<button onclick="invoice_pending_payment('.$row1['id'].','.$branch['id'].')" class="btn btn-danger btn-xs" type="button"><i class="fa fa-money" aria-hidden="true"></i>Pay now</button>';
                    									        }
                    									    ?>
                    									    <a href="billing.php?beid=<?php echo $row1['id']; ?>"><button class="btn btn-warning btn-xs" type="button"><i class="fa fa-pencil-square-o" aria-hidden="true"></i>Edit</button></a> 
                    									    <a href="invoice.php?inv=<?php echo $row1['id']; ?>"><button class="btn btn-info btn-xs" type="button"><i class="fa fa-eye" aria-hidden="true"></i>View</button></a> 
                    									    <!--<a href="invoice.php?inv=<?php echo $row1['id']; ?>"><button class="btn btn-primary btn-xs" type="button">SMS</button></a>-->
                    								    </td>
                                                        <?php } ?>
                    								</tr>
                							<?php } } ?>
                						</tbody>
                					</table>
                				</div>
                			</div>
                		</div>
                	</div>
                </div>
            </div>
            
            <!--Reward points-->
            <div id="reward_point_history" class="tab-pane fade">
                <div class="row gutter">
                	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                		<div class="panel">
                			<div class="panel-heading">
                				<h4>Reward point history
                				<strong  class="pull-right" style="color:red">Pending Points <?= get_reward_points($cid) ?> </strong></h4>
                			</div>
                			<div class="panel-body">
                				<div class="table-responsive">
                					<table class="table order_date_desc grid table-bordered no-margin">
                						<thead>
                							<tr>
                								<th>Date</th>
                                                <th>Branch</th>
                								<th>Bill / Appointment ID</th>
                								<th>Point on</th>
                								<th>Transaction type</th>
                								<th>Points</th>
                								<th>Notes</th>
                							</tr>
                						</thead>
                						<tbody>
                							
                							<?php
                                                $total_branches = query_by_id("SELECT id FROM salon_branches WHERE status='1'",[],$conn);
                                                foreach($total_branches as $branch){
                    								$sql1="SELECT SUM(crp.points) as point, crp.* FROM customer_reward_points crp WHERE crp.client_id='cust,$cid' AND crp.status = '1' and crp.branch_id='".$branch['id']."' GROUP BY crp.invoice_id, crp.point_type ORDER BY crp.id DESC";		
                    								$result1=query_by_id($sql1,[],$conn);
                    								foreach($result1 as $row1) {
                    			                        $res = query_by_id("SELECT s.name as name FROM invoice_items_".$branch['id']." as ii "
                    			                                         ." LEFT JOIN service as s ON CONCAT('sr',',',s.id) = ii.service"
                    			                                         ." WHERE ii.iid = SUBSTRING_INDEX('".$row1['invoice_id']."',',',-1) AND ii.type = 'Service' and ii.branch_id='".$branch['id']."'"
                    			                                         ." UNION"
                    			                                         ." SELECT p.name as name FROM invoice_items_".$branch['id']." as ii "
                    			                                         ." LEFT JOIN products as p ON CONCAT('pr',',',p.id) = ii.service"
                    			                                         ." WHERE ii.iid = SUBSTRING_INDEX('".$row1['invoice_id']."',',',-1) AND ii.type = 'Product' and ii.branch_id='".$branch['id']."'"
                    			                                         ." UNION"
                    			                                         ." SELECT md.membership_name as name FROM membership_discount_history mdh"
                    			                                         ." LEFT JOIN membership_discount md ON md.id = mdh.md_id"
                    			                                         ." LEFT JOIN invoice_".$branch['id']." i ON i.membership_id = md.id"
                    			                                         ." WHERE mdh.invoice_id = SUBSTRING_INDEX('".$row1['invoice_id']."',',',-1)"
                    			                                         ." UNION"
                    			                                         ." SELECT pa.name as name FROM invoice_items_".$branch['id']." as ii  "
                    			                                         ." LEFT JOIN packages as pa ON CONCAT('pa',',',pa.id) = ii.service"
                    			                                         ." WHERE ii.iid = SUBSTRING_INDEX('".$row1['invoice_id']."',',',-1) AND ii.type = 'Package' and ii.branch_id='".$branch['id']."'",[],$conn);
                    								?>
                    								<tr>
                    									<td><?php echo date('d-m-Y',strtotime($row1['datetime'])); ?> </td>
                                                        <td><?= ucfirst(branch_by_id($branch['id'])); ?></td>
                    									<td><?php echo $row1['invoice_id']; ?></td>
                    									<td><?php 
                    									    foreach($res as $name){
                    									        echo $name['name']."<br />";
                    									    } 
                    									   ?></td>
                    									<td>
                    										<?php 
                    											if($row1['point_type'] == 1) {
                    												echo "<span class='text-success'>Credit</span>";
                    											} else if($row1['point_type'] == 2) {
                    												echo "<span class='text-danger'>Debit</span>";
                    											} else if($row1['point_type'] == 3) {
                    												echo "<span class='text-warning'>Refunded</span>";
                    											}
                    										?>
                    									</td>
                    									<td><?php echo $row1['point']; ?></td>
                    									<td><?php echo $row1['notes']; ?></td>
                    								</tr>
                							<?php } } ?>
                						</tbody>
                					</table>
                				</div>
                			</div>
                		</div>
                	</div>
                </div>
            </div>
            
            <!--Payment history-->
            <div id="payment_history" class="tab-pane fade">
                <div class="row gutter">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <div class="panel">
                            <div class="panel-heading">
                                <h4>Payment history</h4>
                            </div>
                            <div class="panel-body">
                                <div class="table-responsive">
                                    <table class="table order_date_desc grid table-bordered no-margin">
                                        <thead>
                                            <tr>
                                                <th>Date</th>
                                                <th>Branch</th>
                                                <th>Bill / Appointment id</th>
                                                <th>Total amount</th>
                                                <th>Advance</th>
                                                <th>Paid</th>
                                                <th>Pending</th>
                                                <th>Appointment id</th>
                                                <th>Payment mode</th>
                                                <th>Bill type</th>
                                                <th>Paid at</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                                $total_branches = query_by_id("SELECT id FROM salon_branches WHERE status='1'",[],$conn);
                                                foreach($total_branches as $branch){
                                                    $query = "SELECT ai.appdate as date, ai.id as invoice_id, ai.total  as total, ai.paid as advance, 0 as paid, ai.due as pending, 'Appointment' as type, '--' as app_id, 0 as paid_at_branch FROM app_invoice_".$branch['id']." ai WHERE ai.client = '$cid' AND ai.bill_created_status = 0 and ai.branch_id='".$branch['id']."' AND active='0'"
                                                              ." UNION"
                                                              ." SELECT i.doa as date, i.id as invoice_id, i.total as total, i.advance as advance, i.paid as paid, i.due as pending, 'Bill' as type, i.appointment_id as app_id, 0 as paid_at_branch FROM invoice_".$branch['id']." i WHERE i.client = '$cid' and i.branch_id='".$branch['id']."' AND active='0'"
                                                              ." UNION"
                                                              ." SELECT ipp.pending_paid_date as date, ipp.iid as invoice_id, 0 as total, 0 as advance, ipp.pending_payment_received as paid, 0 as pending, 'Pending payment' as type, '--' as app_id, paid_at_branch FROM invoice_pending_payment ipp WHERE ipp.status = '1' AND ipp.client_id ='$cid' and ipp.branch_id='".$branch['id']."'"
                                                              ." UNION"
                                                              ." SELECT DATE(wh.time_update) as date, wh.id as invoice_id, 0 as total, 0 as advacne, wh.paid_amount as paid, 0 as pending, 'Wallet' as type, '--' as app_id, 0 as paid_at_branch FROM wallet_history wh WHERE wh.client_id = '$cid' AND wh.transaction_type = '1' AND wh.paid_amount > 0 and wh.branch_id='".$branch['id']."' and status='1'"
                                                              ." ORDER BY date DESC";
                                                    $result = query_by_id($query,[],$conn);
                                                    if($result){
                                                        foreach($result as $res){
                                                        ?>
                                                        <tr>
                                                            <td><?= my_date_format($res['date']); ?></td>
                                                            <td><?= ucfirst(branch_by_id($branch['id'])); ?></td>
                                                            <td><?= $res['invoice_id'] ?></td>
                                                            <td><?= number_format($res['total'],2) ?></td>
                                                            <td><?= number_format($res['advance'],2) ?></td>
                                                            <td><?= number_format($res['paid'],2) ?></td>
                                                            <td><?= number_format($res['pending'],2) ?></td>
                                                            <td><?= $res['app_id']!=0?$res['app_id']:'--'; ?></td>
                                                            <td>
                                                                <?php
                                                                    if($res['type'] == 'Pending payment'){
                                                                        $pay_modes = query_by_id("SELECT DISTINCT payment_method FROM invoice_pending_payment WHERE iid = '".$res['invoice_id']."' and status='1'",[],$conn);
                                                                        foreach($pay_modes as $methods){
                                                                            echo pay_method_name($methods['payment_method'])."<br />";
                                                                        }
                                                                    } else if($res['type'] == 'Bill'){
                                                                        $pay_modes = query_by_id("SELECT DISTINCT payment_method FROM multiple_payment_method WHERE invoice_id = 'bill,".$res['invoice_id']."'",[],$conn);
                                                                        foreach($pay_modes as $methods){
                                                                            echo pay_method_name($methods['payment_method'])."<br />";
                                                                        }
                                                                    } else if($res['type'] == 'Appointment'){
                                                                        $pay_modes = query_by_id("SELECT DISTINCT payment_method FROM multiple_payment_method WHERE invoice_id = 'app,".$res['invoice_id']."'",[],$conn);
                                                                        foreach($pay_modes as $methods){
                                                                            echo pay_method_name($methods['payment_method'])."<br />";
                                                                        }
                                                                    } else if($res['type'] == 'Wallet'){
                                                                        $pay_modes = query_by_id("SELECT payment_method FROM wallet_history WHERE id='".$res['invoice_id']."'",[],$conn);
                                                                        foreach($pay_modes as $methods){
                                                                            echo pay_method_name($methods['payment_method'])."<br />";
                                                                        }
                                                                    } else {
                                                                        echo '--';
                                                                    }
                                                                ?>
                                                            </td>
                                                            <td><?= $res['type'] ?></td>
                                                            <td><?php
                                                                if($res['type'] == 'Pending payment'){
                                                                    if($res['paid_at_branch'] != 0){
                                                                        echo ucfirst(branch_by_id($res['paid_at_branch']));
                                                                    } else {
                                                                        echo ucfirst(branch_by_id($branch['id']));
                                                                    }
                                                                } else {
                                                                    echo ucfirst(branch_by_id($branch['id']));
                                                                }
                                                            ?></td>
                                                        </tr>
                                                        <?php
                                                        }   
                                                    }
                                                }
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!--Package history-->
            <div id="package_history" class="tab-pane fade">
                <div class="row gutter">
                	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                		<div class="panel">
                			<div class="panel-heading">
                				<h4>Package history</h4>
                			</div>
                			<div class="panel-body">
                				<div class="table-responsive">
                					<table class="table grid table-bordered no-margin">
                						<thead>
                							<tr>
                								
                								<th>Package name</th>
                                                <th>Branch</th>
                								<th>Valid upto</th>
                								<th>Package price</th>
                								<th>Total services</th>
                								<th>Services availed</th>
                								<th>Action</th>
                							</tr>
                						</thead>
                						<tbody>
                							<?php
                                                $total_branches = query_by_id("SELECT id FROM salon_branches WHERE status='1'",[],$conn);
                                                foreach($total_branches as $branch){ 
                    								$sql5= "SELECT count(cpsu.c_pack_id) as pack_count,i.id as inv_id, p.name as package_name,p.valid,p.price,p.duration,s.name as service_name,cpsu.inv,cpsu.c_service_id,cpsu.client_id,cpsu.c_pack_id,sum(cpsu.quantity) as qt, sum(cpsu.quantity_used) as used_qty,cpsu.quantity_used FROM `client_package_services_used` cpsu "
    						." LEFT JOIN `packages` p on p.id=cpsu.c_pack_id "
    						." LEFT JOIN `service` s on s.id=SUBSTRING_INDEX(cpsu.c_service_id,',',-1)"
    						." LEFT JOIN `invoice_".$branch['id']."` i on i.id = cpsu.inv"
    						." where cpsu.active='1' and cpsu.client_id='".$_GET['cid']."' and cpsu.branch_id='".$branch['id']."' and i.active='0' GROUP by cpsu.inv";
                    								$result5=query_by_id($sql5,[],$conn);
                    								foreach($result5 as $row5) {
                    									$days = $row5['duration'];
                    									$package_expiry_date = my_date_format(date('Y-m-d', strtotime($row5['valid']. ' + '.$days.' days')));
                    								?>
                    								<tr>
                    									<td <?php if(strtotime(package_validity_date($row5['client_id'], $row5['c_pack_id'], $row5['inv'], $branch['id'])) < strtotime(date('d-m-Y'))){ echo 'style="color:red;"'; } ?>><?php  if(strtotime(package_validity_date($row5['client_id'], $row5['c_pack_id'], $row5['inv'], $branch['id'])) < strtotime(date('d-m-Y'))){ echo '(Expired) '.'<del>'; } echo $row5['package_name']; ?></td>
                    									<td><?= ucfirst(branch_by_id($branch['id'])); ?></td>
                    									<td><?= package_validity_date($row5['client_id'], $row5['c_pack_id'], $row5['inv'], $branch['id']); ?></td>
                    									<td><?= number_format($row5['price'],2) ?></td>
                    									<td><?= $row5['qt']?></td>
                    									<td><?= $row5['used_qty']?></td>
                                                        <?php //if($branch['id'] == $branch_id) { ?>
                    									<td><?php echo '<a href="clientpackage.php?cid='.$_GET['cid'].'&pid='.$row5['c_pack_id'].'&invid='.$row5['inv'].'" target="_blank"><button class="btn btn-info btn-xs" type="button"><i class="fa fa-eye" aria-hidden="true"></i>View</button></a>'; ?></td>
                                                        <?php //} else { echo "<td></td>"; } ?>
                    								</tr>
                    								
                    							<?php } } ?>
                						</tbody>
                					</table>
                				</div>
                			</div>
                		</div>
                	</div>
                </div>
            </div>
            
            
            <!--Membership history-->
            <div id="membership_history" class="tab-pane fade">
                <div class="row gutter">
                	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                		<div class="panel">
                			<div class="panel-heading">
                				<h4>Membership history</h4>
                			</div>
                			<div class="panel-body">
                				<div class="table-responsive">
                					<table class="table grid table-bordered no-margin">
                						<thead>
                							<tr>
                								<th>Membership name</th>
                								<th>Valid upto</th>
                								<th>Price</th>
                								<th>Service discount</th>
                								<th>Product discount</th>
                								<th>Package discount</th>
                								<th>Reward boost</th>
                								<th>Condition</th>
                							</tr>
                						</thead>
                						<tbody>
                							<?php 
                							    $sql = "SELECT mdh.*, md.*, i.doa FROM membership_discount_history mdh"
                							                       ." LEFT JOIN membership_discount md ON md.id = mdh.md_id"
                                                                   ." LEFT JOIN invoice_$branch_id i ON i.id=mdh.invoice_id "
                							                       ." WHERE mdh.client_id='".$cid."'";
                								$result = query_by_id($sql,[],$conn);
                								foreach($result as $row) {
                									$days = $row['validity'];
                									$package_expiry_date = my_date_format(date('Y-m-d', strtotime(date('Y-m-d',strtotime($row['doa'])). ' + '.$days.' days')));
                								?>
                								<tr>
                									<td <?php if(strtotime($package_expiry_date) < strtotime(date('d-m-Y'))){ echo 'style="color:red;"'; } ?>><?php  if(strtotime($package_expiry_date) < strtotime(date('d-m-Y'))){ echo '(Expired) '.'<del>'; } echo $row['membership_name']; ?></td>
                									<td><?= my_date_format($package_expiry_date); ?></td>
                									<td><?= number_format($row['membership_price'],2) ?></td>
                									<td><?php
                									    $ser_discount = explode(',',$row['discount_on_service']);
                									    if($ser_discount[1] == 'pr'){
                									        echo $ser_discount[0].'%';
                									    } else {
                									        echo $ser_discount[0].' '.CURRENCY;
                									    }
                									?></td>
                									<td><?php
                									    $pro_discount = explode(',',$row['discount_on_product']);
                									    if($pro_discount[1] == 'pr'){
                									        echo $pro_discount[0].'%';
                									    } else {
                									        echo $pro_discount[0].' '.CURRENCY;
                									    }
                									?>
                									</td>
                									<td><?php
                									    $pack_discount = explode(',',$row['discount_on_package']);
                									    if($pack_discount[1] == 'pr'){
                									        echo $pack_discount[0].'%';
                									    } else {
                									        echo $pack_discount[0].' '.CURRENCY;
                									    }
                									?></td>
                									<td><?php echo $row['reward_points_boost'].'X'?></td>
                									<td>
                									    <?php
                									        echo "Minimun reward point should be ".$row['min_reward_points_earned'];
                									        echo $row['condition']==1?'<div class="text-center"><b>AND</b></div>':'<div class="text-center"><b>OR</b></div>';
                									        echo "Minimun bill amount should be ".$row['min_bill_amount'];
                									    ?>
                									</td>
                								</tr>
                								
                							<?php } ?>
                						</tbody>
                					</table>
                				</div>
                			</div>
                		</div>
                	</div>
                </div>
            </div>
            
            <!--Wallet history-->
            
            <div id="wallet_history" class="tab-pane fade">
                <div class="row gutter">
                	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                		<div class="panel">
                			<div class="panel-heading">
                				<h4>Wallet history
                				    <strong  class="pull-right" style="color:red">Wallet Balance <?= CURRENCY ." ". number_format(client_wallet($cid),2); ?> /-  </strong>
                				</h4>
                			</div>
                			<div class="panel-body">
                				<div class="table-responsive">
                					<div class="table-responsive">
										<table id='empTable' class="table table-bordered">
											<thead>
												<tr>
													<th>Date/Time</th>
                                                    <th>Branch</th>
													<th>Transaction type</th>
													<th>Amount paid</th>
													<th>Wallet amount</th>
													<th>Payment method</th>
													<th>Amount received from</th>
													<th>Bill id</th>
												</tr>
											</thead>
											<tbody>
            						      <?php
            						        $wallethistory = query_by_id("SELECT * FROM wallet_history WHERE status = 1 AND client_id = '".$cid."' ORDER BY id DESC",[],$conn);
            						        if($wallethistory){
            						            foreach($wallethistory as $wh){
            						                ?>
            						                    <tr>
                                                            <td><?= ucfirst(branch_by_id($wh['branch_id'])); ?></td>
            						                        <td><?= $rev['review']; ?></td>
            						                        <td><?= $rev['overall_exp']; ?></td>
            						                        <td><?= $rev['timely_response']; ?></td>
            						                        <td><?= $rev['our_support']; ?></td>
            						                        <td><?= $rev['overall_satisfaction']; ?></td>
            						                        <td><?php
            						                            for($i=1;$i<=5;$i++){
                                                	                if($i <= $rev['customer_service_rating']){
                                                	                     echo '<i class="fa fa-star rating-color" style="margin:0px;" aria-hidden="true"></i>';
                                                	                } else {
                                                	                    echo '<i class="fa fa-star-o rating-color" style="margin:0px;" aria-hidden="true"></i>';
                                                	                }
                                                	            }
                						                    ?></td>
            						                        <td><?= $rev['suggestion']; ?></td>
                                                            <?php if(!isset($_GET['bid'])){ ?>
            						                        <td>
            						                            <?php
            						                                if($rev['approve_status'] == 1){
            						                                    echo '<button type="button" class="btn btn-sm btn-success"><i class="fa fa-thumbs-o-up" aria-hidden="true"></i>Approved</button>';
            						                                } else if($rev['approve_status'] == 2){
            						                                    echo '<button type="button" class="btn btn-sm btn-danger"><i class="fa fa-times-circle-o" aria-hidden="true"></i>Cancelled</button>';
            						                                } else {}
            						                            ?>
            						                        </td>
                                                        <?php } ?>
            						                    </tr>
            						                <?php
            						            }
            						        }
            						      ?>
            						  </tbody>
										</table>
									</div>
                				</div>
                			</div>
                		</div>
                	</div>
                </div>
            </div>
            
            <!--feedback & rating-->
            <div id="feedback_rating" class="tab-pane fade">
                <div class="row gutter">
                	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                		<div class="panel">
                			<div class="panel-heading">
                				<h4>Feedback & rating</h4>
                			</div>
                			<div class="panel-body">
                				<div class="table-responsive">
                					<table class="table grid table-bordered table-striped">
            						  <thead>
            							<tr>
                                          <th>Branch</th>
            							  <th>Review</th>
            							  <th>Overall experience</th>
            							  <th>Timely response</th>
            							  <th>Support</th>
            							  <th>Overall satisfaction</th>
            							  <th>Service rating</th>
            							  <th>Suggestion</th>
                                          <?php if(!isset($_GET['bid'])){ ?>
            							  <th>Action</th>
                                          <?php } ?>
            							</tr>
            						  </thead>
            						  <tbody>
            						      <?php
            						        $review = query_by_id("SELECT * FROM client_feedback WHERE status = 1 AND client_id = '".$cid."' AND approve_status !=0 ORDER BY id DESC",[],$conn);
            						        if($review){
            						            foreach($review as $rev){
            						                ?>
            						                    <tr>
                                                            <td><?= ucfirst(branch_by_id($rev['branch_id'])); ?></td>
            						                        <td><?= $rev['review']; ?></td>
            						                        <td><?= $rev['overall_exp']; ?></td>
            						                        <td><?= $rev['timely_response']; ?></td>
            						                        <td><?= $rev['our_support']; ?></td>
            						                        <td><?= $rev['overall_satisfaction']; ?></td>
            						                        <td><?php
            						                            for($i=1;$i<=5;$i++){
                                                	                if($i <= $rev['customer_service_rating']){
                                                	                     echo '<i class="fa fa-star rating-color" style="margin:0px;" aria-hidden="true"></i>';
                                                	                } else {
                                                	                    echo '<i class="fa fa-star-o rating-color" style="margin:0px;" aria-hidden="true"></i>';
                                                	                }
                                                	            }
                						                    ?></td>
            						                        <td><?= $rev['suggestion']; ?></td>
                                                            <?php if(!isset($_GET['bid'])){ ?>
            						                        <td>
            						                            <?php
            						                                if($rev['approve_status'] == 1){
            						                                    echo '<button type="button" class="btn btn-sm btn-success"><i class="fa fa-thumbs-o-up" aria-hidden="true"></i>Approved</button>';
            						                                } else if($rev['approve_status'] == 2){
            						                                    echo '<button type="button" class="btn btn-sm btn-danger"><i class="fa fa-times-circle-o" aria-hidden="true"></i>Cancelled</button>';
            						                                } else {}
            						                            ?>
            						                        </td>
                                                        <?php } ?>
            						                    </tr>
            						                <?php
            						            }
            						        }
            						      ?>
            						  </tbody>
            						</table>
                				</div>
                			</div>
                		</div>
                	</div>
                </div>
            </div>
        </div>
    </div>
<!-- Main container ends -->
</div>
</div>
<!-- Dashboard Wrapper End -->


<!-- Modal -->
<div class="modal fade" id="myModal" role="dialog">
	<div class="modal-dialog">
		
		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header">
				<button type="button"  class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">Payments</h4>
			</div>
			<div class="modal-body">
				<form id="">
					<label for="userName">Amount to be Paid:</label>
					<input type="text" value="<?php //echo suminvoice($row['id']); ?>" class="form-control" id="tobepaid" readonly>
					<br>
					<label for="userName">Payment:</label>
					<input type="text" class="form-control" onkeyup="checkpay();" id="amount">
					<br>
					<label for="userName">Pending:</label>
					<input type="text" class="form-control" id="pend" readonly>
					<br>
					<label for="userName">Payment Mode :</label>
					<select name="paymehod" id="mode" class="form-control act">
						<option>Cash</option>
						<option>Mobile Wallet</option>
						<option>Credit/Debit Card</option>
						<option>Cheque</option>
						<option>Online payment</option>
					</select>
					<br>
					<label for="userName">Notes Any :</label>
					<textarea name="notes" id="notes" class="form-control" ></textarea>
					<br>
					<button type="submit" style="float : right;" onclick="acceptpayment()" data-dismiss="modal" class="btn-info">Accept Payment</button> 
				</form>
			</div>
			<br>
			<div class="modal-footer">
				
			</div>
		</div>
		
	</div>
</div>
<!-- Modal End -->

<script>
	function acceptpayment(){
		var payment = $('#tobepaid').val();
		var amnt = $('#amount').val();
		var mode = $('#mode').val();
		var pend = $('#pend').val();
		var notes = $('#notes').val();
		var client = <?php echo $cid; ?>;
		if(payment==""||amnt==""||mode==""){
			alert("Please Fill all Fields");
			//$('#myModal').modal('show');
			}else{
			var dataString = 'pay='+ payment + '&amnt='+ amnt + '&mode='+ mode + '&pend='+ pend+'&notes='+notes+'&client='+client;
			$.ajax({
				url: "payments_client.php",
				type: "POST",
				data: dataString,
				success:function(data){
					//alert(data);
					toastr.success("Payment Accepted Successfully");
					$('#tobepaid').val(pend);
					$('#pamnt').val(pend);
					$('#amount').val('');
					$('#mode').val('');
					$('#pend').val('');
					$('#notes').val('');
				},
				error:function (){}
			});
		}
	}
	
	function checkpay(){
		var payment = $('#tobepaid').val();
		var amnt = $('#amount').val();
		var pend = $('#pend').val();
		var sum = parseInt(payment) - parseInt(amnt);
		$('#pend').val(sum);
	}
	
	$(document).ready(function(){
	    if (localStorage.getItem("invoice_paid") !== null) {
	        var check_pending_payment = localStorage.getItem("invoice_paid");
    	    if(check_pending_payment == 'paid_success'){
    	        $('.nav-tabs a').each(function(){
    	            if($(this).attr('href') == '#billing'){
    	                $(this).click();
    	                localStorage.clear();
    	            }
    	        });
    	    }
	    }
	    
		var client_id=<?=$cid?>;
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
			{ data: 'branch_name' },
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
	
	function invoice_pending_payment(invoice_id, branch_id){
	    // check client pending payments
	    $.ajax({
	        url : "ajax/get_pending_payments.php",
	        type : "post",
	        data : {action : 'invoice_pending_payment', invoice_id : invoice_id, branch_id : branch_id},
	        success : function(res){
	            if(res != 0){
	                $('#ppaymentModal .modal-content').html(res);
	                var size = $('#ppaymentModal table tbody tr').length;
	                if(size <= 0){
	                    $('#ppaymentModal').modal('hide');
	                } else {
	                    $('#ppaymentModal').modal('show');
	                }
	            }
	        }
	    });
	}
</script>
<?php 
	
	include "footer.php";

	function payment_mode($iid){
		global $conn;
        global $branch_id;
		$payment_method = '' ;
		$sql = "SELECT pm.name as payment_method FROM `multiple_payment_method` mpm LEFT JOIN payment_method pm on pm.id=mpm.payment_method WHERE mpm.invoice_id='$iid' and mpm.status=1 and mpm.branch_id='".$branch_id."'";
		$result = query_by_id($sql,[],$conn);
		if($result){
			foreach($result as $row){
				$payment_method .= $row['payment_method'].'<br>';
			}
			}else{
			
			$payment_method;
		}
		return $payment_method;
	}
	
	function get_cash($id){
		global $conn,$payment_method;
        global $branch_id;
		$paid=0;
		$sql="SELECT amount_paid From`multiple_payment_method` where invoice_id='$id' and status='1' and branch_id='".$branch_id."'";
		$result = query_by_id($sql,[],$conn);
		if($result){
			foreach($result as $row){
				$paid+=$row['amount_paid'];
			}
		}
		return $paid;
	}
	
	function firstvisit($uid){
		global $conn;
        global $branch_id;
		$sql="SELECT * from invoice_".$branch_id." where client=$uid and type=2 and branch_id='".$branch_id."' order by id asc limit 1";
		$result=query_by_id($sql,[],$conn)[0];
		if ($result) {
			return $result['doa'];
			}else{
			return "NA";
		}
	}
	
	function package($uid){
		global $conn;
        global $branch_id;
		$sql="SELECT * FROM `packages` where id=$uid and branch_id='".$branch_id."'";
		$result=query_by_id($sql,[],$conn)[0];
		if ($result) {
			return $result['name'];
			}else{
			return "NA";
		}
	}
	
	function packageprice($uid){
		global $conn;
        global $branch_id;
		$sql="SELECT * FROM `packages` where id=$uid and branch_id='".$branch_id."'";
		$result=query_by_id($sql,[],$conn)[0];
		if ($result) {
			return $result['price'];
			}else{
			return "NA";
		}
	}
	
	function servicsavail($pid,$inv){
		global $conn;
        global $branch_id;
		$sql="SELECT sum(quantity) as total FROM `clientservices` where branch_id='".$branch_id."' ";
		$result=query_by_id($sql,[],$conn)[0];
		if ($result) {
			return $result['total'];
			}else{
			return "0";
		}
	}
	
	function sumservice($pkg,$inv){
		global $conn;
        global $branch_id;
		$sql="SELECT sum(quantity) as total FROM `client_package_services_used` WHERE `c_pack_id`='$pkg' and `inv`='$inv' and active=1 and branch_id='".$branch_id."'";
		$result=query_by_id($sql,[],$conn)[0];
		if ($result) {
			return $result['total'];
			}else{
			return "0";
		}
	}
	
	function sumpack($uid,$cid,$sid){
		
		global $conn;
        global $branch_id;
		//$sql="SELECT sum(quantity) as total FROM `packageservice` WHERE pid=$uid and active=0";
		$sql="SELECT sum(ii.quantity) as used_quantity FROM `invoice_items_".$branch_id."` ii where ii.client='$cid' and active='0' and service='$sid' and package_id='$uid' and branch_id='".$branch_id."'";
		$result=query_by_id($sql,[],$conn)[0];
		if ($result) {
			return $result['used_quantity'];
			}else{
			return "0";
		}
	}
	
	function suminvoice($cid){
		global $conn;
        global $branch_id;
		$sql="SELECT sum(paid) as paid,sum(bpaid) as bpaid,sum(total) as total,sum(chnge) as rett FROM `invoice_".$branch_id."` WHERE client=$cid and status <> 'Cancelled' and active=0 and branch_id='".$branch_id."'";
		$result=query_by_id($sql,[],$conn)[0];
		if ($result) {
			$total = $result['total'];
			$bpaid = $result['bpaid'];
			$paid  = $result['paid'];
			$chnge = $result['rett'];
			$clnt = getpayments($cid);
			$pend = $total - $bpaid - $paid - $clnt;
			//$pend = $pend + $chnge;
			return $pend;
			}else{
            return "0";
		}
	}
	
	function getpayments($cid){
		global $conn;
        global $branch_id;
		$sql="SELECT sum(credit) as credit FROM `payments_client` WHERE client=$cid and active=0 and branch_id='".$branch_id."'";
		$result=query_by_id($sql,[],$conn)[0];
		if ($result) {
			return $result['credit'];
			}else{
			return "0";
		}
	}
	
	function suminv($id){
		global $conn;
        global $branch_id;
		$sql="SELECT paid,bpaid,total FROM `invoice_".$branch_id."` WHERE id=$id  and active=0 and branch_id='".$branch_id."'";
		$result=query_by_id($sql,[],$conn)[0];
		if ($result) {
			$total = $result['total'];
			$bpaid = $result['bpaid'];
			$paid  = $result['paid'];
			$pend  = $total - $bpaid - $paid;
			return $pend;
			}else{
			return "0";
		}
	}
	
	function getdat($uid){
		global $conn;
        global $branch_id;
		$sql="SELECT * from invoice_".$branch_id." where id=$uid and branch_id='".$branch_id."'";
		$result=query_by_id($sql,[],$conn)[0];
		if ($result) {
			return $result['doa']." ".$result['itime'];
			}else{
			return "NA";
		}
	}
	
	function get_packages_total_services($pid){
		global $conn;
        global $branch_id;
		$sql="SELECT count(id) as total_service from `packageservice` where pid=$pid and active='0' and branch_id='".$branch_id."'";
		$result=query_by_id($sql,[],$conn);
		foreach($result as $row){
			return $row['total_service'];
		}
		
	}
	
	// function get_reward_points($cid,$inv_id){
	// 	global $conn;
    // global $branch_id;
	// 	$sql_reward_point="SELECT ii.service,s.points,sum(ii.quantity) as sum_quantity FROM invoice_items ii LEFT JOIN `service` s on s.id=SUBSTRING_INDEX(ii.service,',',-1) where ii.client='$cid' and ii.iid='$inv_id' and ii.active='0'";
	// 	if(get_row_count($sql_reward_point,[],$conn) >0 ){
	// 		$result1  =	query_by_id($sql_reward_point,[],$conn);
	// 		foreach($result1 as $row1){
	// 				$reward_point += ((($row1['points'])?$row1['points']:'0')* $row1['sum_quantity']);
	// 			}
	// 		}
	 
	// 	 return  $reward_point;
	// }
	?>				