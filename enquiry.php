<?php 
	include "./includes/db_include.php";
	$branch_id = $_SESSION['branch_id'];
	$uid = $_SESSION['uid'];
	if(isset($_GET['id']) && $_GET['id']>0)
	{
		$eid= $_GET['id'];
		$edit = query_by_id("SELECT * from `enquiry` where id=:id and branch_id='".$branch_id."'",["id"=>$eid],$conn)[0];
		$sourceofclient = query_by_id("SELECT leadsource from `client` where id=:id and branch_id='".$branch_id."'",["id"=>$edit['client_id']],$conn)[0];
		if(!$edit){
			header('LOCATION:enquiry.php');
		}
	}
	
	if(isset($_POST['submit'])){
		$customer = addslashes(trim($_POST['customer']));
		$cont  	  = trim($_POST['cont']);
		$client_id = trim($_POST['clientid']);
		$email    = trim($_POST['email']);
		$addr     = addslashes(trim($_POST['addr']));
		$leadsource = trim($_POST['leadsource']);
		if($client_id == 0){
		$get_client = query_by_id("SELECT count(*) as total FROM client WHERE cont='".$cont."' AND active='0' AND branch_id='".$branch_id."'",[],$conn)[0]['total'];
		if($get_client <= 0){
			$client_id=get_insert_id("INSERT INTO `client` set `name`=:name,`cont`=:cont,`address`=:address,`email`=:email, `leadsource`=:leadsource, `gender`=:gender, `active`=:active, `branch_id`='".$branch_id."'",[
							'name'=>$customer,
							'cont'=>$cont,
							'address'=>$addr,
							'email'=>$email,
							'leadsource'=> $leadsource,
							'gender' => 1,
							'active'=>0
				],$conn);
			 query("INSERT INTO wallet SET iid='0', date = '".date('Y-m-d')."', client_id = '$client_id', wallet_amount='0', get_wallet_from='', status='1', branch_id='".$branch_id."'",[],$conn);
		    } 
		}
		$enquiry  = trim($_POST['enquiry']);
		$enquiry_type =explode(',',$enquiry)[0];
		$enqtype  = trim($_POST['enquirytype']);
		$response = addslashes(trim($_POST['response']));
		$followdate = trim($_POST['followdate']);
		$followdate = trim($_POST['followdate']);
		$leadstatus  = trim($_POST['leadstatus']);
		$user	    = $_SESSION['uid'];
		if($_SESSION['u_role']==1){ 
			$leaduser  = trim($_POST['leaduser']);
			}else{
			$leaduser = $user;
		}
		if($leadstatus=="Converted"){
			$lead_status  = 'Pending';
		}else{
			$lead_status  = trim($_POST['leadstatus']);
		}
		$date = date("Y-m-d");
		
		$eid=get_insert_id("INSERT INTO `enquiry` set `client_id`=:client_id,`customer`=:customer,`cont`=:cont,`email`=:email,`addr`=:addr,`enquiry`=:enquiry,`type`=:type,`regon`=:regon,`response`=:response,`datefollow`=:datefollow,`leaduser`=:leaduser,`leadstatus`=:leadstatus,`active`=:active, `branch_id`='".$branch_id."'",[
		                        'client_id'=>$client_id,
		                        'customer'=>$customer,
		                        'cont'=>$cont,
		                        'email'=>$email,
		                        'addr'=>$addr,
		                        'enquiry'=>$enquiry,
		                        'type'=>$enqtype,
		                        'regon'=>$date,
		                        'response'=>$response,
		                        'datefollow'=>$followdate,
		                        'leaduser'=>$leaduser,
		                        'leadstatus'=>$lead_status,
		                        'active'=>0
		                        ],$conn);

		$sms_data = array(
	        'name' => $customer
	    );
	    
		send_sms($cont,$sms_data,'new_enquiry_sms');
		
		$now = new DateTime();
		$time = $now->format('H:i:s');
		
		$enquiryresponse_id=get_insert_id("INSERT INTO `enquiryresponse`(`eid`,`response`,`date`,`time`,`enqtype`,`leadstatus`,`user`,`branch_id`) VALUES ('$eid','$response','$date','$time','$enqtype','$lead_status','$uid','$branch_id')",[],$conn);
		
		$_SESSION['t']  = 1;
		$_SESSION['tmsg']  = "Enquiry Added Successfully";
		if($leadstatus=="Converted" && $enquiry_type == 'sr'){
			echo '<meta http-equiv="refresh" content="0; url=appointment.php?enqid='.$eid.'&enquiryresponse_id='.$enquiryresponse_id.'" />';
		}else if($leadstatus=="Converted" && ($enquiry_type == 'pr' || $enquiry_type == 'pa')){
			echo '<meta http-equiv="refresh" content="0; url=billing.php?enqid='.$eid.'&enquiryresponse_id='.$enquiryresponse_id.'" />';
		}else{
			header('LOCATION:enquiry.php');
		}
	}
	
	if(isset($_POST['edit-submit']))
	{
		$customer = addslashes(trim($_POST['customer']));
		$client_id = trim($_POST['clientid']);
		$cont     = trim($_POST['cont']);
		$email    = trim($_POST['email']);
		$addr  	  = addslashes(trim($_POST['addr']));
		$enquiry  = addslashes(trim($_POST['enquiry']));
		$enquiry_type = explode(',',$enquiry)[0];
		$enqtype  = trim($_POST['enquirytype']);
		$response = addslashes(trim($_POST['response']));
		$followdate  = trim($_POST['followdate']);
		$leadsource  = trim($_POST['leadsource']);
		$leadstatus  = trim($_POST['leadstatus']);
		if($leadstatus=="Converted"){
			$lead_status  = 'Pending';
		}else{
			$lead_status  = trim($_POST['leadstatus']);
		}
		$user = $_SESSION['uid'];
		if($_SESSION['u_role']==1){ 
			$leaduser  = trim($_POST['leaduser']);
		}else{
			$leaduser = $user;
		}
		
		$date = date("Y-m-d");
		query("UPDATE `client` SET `leadsource`=:leadsource WHERE id=:id and branch_id='".$branch_id."'",['leadsource'=>$leadsource, 'id'=>$client_id],$conn);

		query("UPDATE `enquiry` SET `client_id`=:client_id,`customer`=:customer,`cont`=:cont,`email`=:email,`addr`=:addr,`enquiry`=:enquiry,`type`=:type,`regon`=:regon,`response`=:response,`datefollow`=:datefollow,`leaduser`=:leaduser,`leadstatus`=:leadstatus,`active`=:active WHERE id=:id and `branch_id`='".$branch_id."'",['client_id'=>$client_id,
		                        'customer'=>$customer,
		                        'cont'=>$cont,
		                        'email'=>$email,
		                        'addr'=>$addr,
		                        'enquiry'=>$enquiry,
		                        'type'=>$enqtype,
		                        'regon'=>$date,
		                        'response'=>$response,
		                        'datefollow'=>$followdate,
		                        'leaduser'=>$leaduser,
		                        'leadstatus'=>$lead_status,
		                        'active'=>0,
								'id'    =>$eid
								],$conn);
	
		
		$now = new DateTime();
		$time = $now->format('H:i:s');
		
		//query("DELETE FROM `enquiryresponse` where eid='$eid'",[],$conn);
		
		$enquiryresponse_id=get_insert_id("INSERT INTO `enquiryresponse`(`eid`,`response`,`date`,`time`,`enqtype`,`leadstatus`,`user`,`branch_id`) VALUES ('$eid','$response','$date','$time','$enqtype','$lead_status','$leaduser','$branch_id')",[],$conn);
		$_SESSION['t']  = 1;
		$_SESSION['tmsg']  = "Enquiry Updated Successfully";

		if($leadstatus=="Converted" && $enquiry_type == 'sr'){
			header('LOCATION:appointment.php?enqid='.$eid.'&enquiryresponse_id='.$enquiryresponse_id.'');
		}else if($leadstatus=="Converted" && ($enquiry_type == 'pr' || $enquiry_type == 'pa')){
			header('LOCATION:billing.php?enqid='.$eid.'&enquiryresponse_id='.$enquiryresponse_id.'');
		}else{
			header('LOCATION:enquiry.php?id='.$eid);
		}
	}
	
	
	$estat = "";
	$euser = "";
	$etype = "";
	$append = "";
	if($_SESSION['u_role']!=1)
	{ 
		$append .= " and leaduser='".$_SESSION['uid']."' ";
	}
	if(isset($_POST['filter']))
	{
		if($_POST['estatus']!==""){
			$estat = "and leadstatus = '".$_POST['estatus']."' ";
		}
		if($_POST['etype']!==""){
			$etype = "and type = '".$_POST['etype']."' ";
		}
		if($_POST['euser']!="0" && $_POST['euser']!=""){
			$euser = "and leaduser = ".$_POST['euser']." ";
		}
		$append .= " ".$estat." ".$etype." ".$euser;
	}
	if(isset($_GET['d'])){
		$d = $_GET['d'];
		query("update enquiry set active=1 where id = $d and branch_id='".$branch_id."'",[],$conn);
		$_SESSION['t']  = 1;
		$_SESSION['tmsg']  = "Enquiry Deleted Successfully";
		echo '<meta http-equiv="refresh" content="0; url=enquiry.php"/>';exit(); 
	}
	
	include "topbar.php";
	include "header.php";
	include "menu.php";
?>
<style>
    #empTable tr th:nth-child(2), #empTable tr td:nth-child(2){
        text-align:center;
        vertical-align:middle;
    }
    .tmps{
        border: 1px solid #ccc;
        border-radius: 3px;
        padding: 10px;
        cursor: pointer;
        margin-bottom: 10px;
    }
</style>
<!-- Dashboard wrapper starts -->
<div class="dashboard-wrapper">
	
	<!-- Main container starts -->
	<div class="main-container">
		
		<!-- Row starts -->
		<div class="row gutter">
			
			<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
				<div class="panel">
					<div class="panel-heading">
						<h4><?php echo $eid!=''?'Enquiry Details':'Add Enquiry'?> </h4>
					</div>
					<div class="panel-body">
						<div class="row">
							<form action="" method="post">
								<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
									<div class="form-group">
										<label for="userName">Contact number <span class="text-danger">*</span></label>
										<input type="text" class="form-control client" maxlength="<?= PHONE_NUMBER ?>" id="cont" name="cont" placeholder="Contact number" value="<?= isset($eid)?$edit['cont']:old('name')?>" onkeyup="this.value = this.value.replace(/[^0-9\.]/g,'');" onBlur="check();contact_no_length($(this), this.value);" required>
										<span style="color:red" id="client-status"></span>
										<span style="color:red" id="digit_error"></span>
									</div>
								</div>
								<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">	
									<div class="form-group">
										<label for="userName">Client name <span class="text-danger">*</span></label>
										<input type="text" class="client form-control" id="client_name" name="customer" placeholder="Client Name" value="<?= isset($eid)?$edit['customer']:old('name')?>" required>
										<input type="hidden" value="<?=$edit['client_id']?$edit['client_id']:'0'?>" name="clientid" id="clientid" class="clt"> 
									</div>
								</div>
								<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
									<div class="form-group">
										<label for="userName">Email</label>
										<input type="email" class="form-control" id="email" name="email" value="<?= isset($eid)?$edit['email']:old('name')?>" placeholder="Email">
									</div>
								</div>
								<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
									<div class="form-group">
										<label for="userName">Address</label>
										<input type="text" class="form-control" name="addr" id="address" value="<?= isset($eid)?$edit['addr']:old('name')?>" placeholder="Address">
									</div>
								</div>
								
								<div class="clearfix"></div>
								<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
									<div class="form-group">
										<label for="userName">Enquiry for <span class="text-danger">*</span></label>
										<input type="text" class="form-control" name="enquiry_for" id="enquiry" value="<?php 
											if(isset($edit['enquiry']) && !empty($edit['enquiry'])){
												$id = EXPLODE(",",$edit['enquiry'])[1];
												if(EXPLODE(",",$edit['enquiry'])[0] == 'sr'){
													$sql ="SELECT CONCAT('sr',',',id) as id,name FROM `service` where active='0' and id='$id'";	
													}else if(EXPLODE(",",$edit['enquiry'])[0] == 'pr'){
													$sql ="SELECT CONCAT('pr',',',id) as id,name FROM `products` where active='0' and id='$id'";	
													}else if(EXPLODE(",",$edit['enquiry'])[0] == 'pa'){
													$sql ="SELECT CONCAT('pa',',',id) as id,name FROM `packages` where active='0' and id='$id' and branch_id='".$branch_id."'";	
												}
												$result=query_by_id($sql,[],$conn);
												foreach($result as $row) {
													echo  $row['name'];
													$edit_enquiry_id = $row['id'];
												} } ?>" placeholder="Autocomplete services / products / packages" required>
												<input type="hidden" id="enquiry_service_id" value="<?=$edit_enquiry_id?>" name="enquiry">
									</div>
								</div>
								
								<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
									<label class="control-label">Enquiry type <span class="text-danger">*</span></label>
									<select class="form-control" name="enquirytype" required>
										<?php if(isset($eid))
											{ 
											$type=$edit['type'];?>
											
											<option value="Hot" <?php if($type=="Hot") echo"selected"; ?>>Hot</option>
											<option value="Cold" <?php if($type=="Cold") echo "selected"; ?>>Cold</option>
											<option value="Warm" <?php if($type=="Warm") echo"selected"?>>Warm</option>
											<?php } else 
											{ ?>	
											<option value="">-- Select a type --</option>
											<option value="Hot">Hot</option>
											<option value="Cold">Cold</option>
											<option value="Warm">Warm</option>
										<?php } ?> 
									</select>
								</div>
								
								
								<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">		
									<div class="form-group ">
										<label class=" control-label">Response</label>
										<input type="text-area" class="form-control" name="response" id="userName" value="<?= isset($eid)?$edit['response']:old('name')?>" placeholder="Response">
									</div>
								</div>
								
								<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
									<div class="form-group">
										<label class=" control-label">Date to follow <span class="text-danger">*</span></label>
										<input type="text" onblur="dateAvailability(this.value)" class="form-control date" name="followdate" id="date" value="<?= isset($eid)?$edit['datefollow']:date('Y-m-d', strtotime(date('Y-m-d') . ' +1 day'));?>" required readonly>
										<span class="text-danger" id="dateerror"></span>
									</div>
								</div>
								<div class="clearfix"></div>
								<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
									<div class="form-group ">
										<label class=" control-label">Source of enquiry <span class="text-danger">*</span></label>
										<select class="form-control" name="leadsource" required>
											<?php if(isset($eid))
												{
													$type=$sourceofclient['leadsource'];
												?>
												<option value="Client refrence" <?php if($type=="Client refrence") echo"selected";?>>Client refrence</option>
												<option value="Cold Calling" <?php if($type=="Cold Calling") echo"selected";?>>Cold Calling</option>
												<option value="Facebook" <?php if($type=="Facebook") echo"selected";?>>Facebook</option>
												<option value="Twitter" <?php if($type=="Twitter") echo"selected";?>>Twitter</option>
												<option value="Instagram" <?php if($type=="Instagram") echo"selected";?>>Instagram</option>
												<option value="Other Social Media" <?php if($type=="Other Social Media") echo"selected";?>>Other Social Media</option>
												<option value="Website" <?php if($type=="Website") echo"selected";?>>Website</option>
												<option value="Walk-In" <?php if($type=="Walk-In") echo"selected";?>>Walk-In</option>
												<option value="Flex" <?php if($type=="Flex") echo"selected";?>>Flex</option>
												<option value="Flyer" <?php if($type=="Flyer") echo"selected";?>>Flyer</option>
												<option value="Newspaper" <?php if($type=="Newspaper") echo"selected";?>>Newspaper</option>
												<option value="SMS" <?php if($type=="SMS") echo"selected";?>>SMS</option>
												<option value="Street Hoardings" <?php if($type=="Street Hoardings") echo"selected";?>>Street Hoardings</option>
												<option value="Event" <?php if($type=="Event") echo"selected";?>>Event</option>
												<option value="TV/Radio" <?php if($type=="TV/Radio") echo"selected";?>>TV/Radio</option>
												<?php 
												}
												else
												{
												?>
												<option value="">-- Select--</option>
												<option value="Client refrence">Client refrence</option>
												<option value="Cold Calling">Cold Calling</option>
												<option value="Facebook">Facebook</option>
												<option value="Twitter">Twitter</option>
												<option value="Instagram">Instagram</option>
												<option value="Other Social Media">Other Social Media</option>
												<option value="Website">Website</option>
												<option value="Walk-In">Walk-In</option>
												<option value="Flex">Flex</option>
												<option value="Flyer">Flyer</option>
												<option value="Newspaper">Newspaper</option>
												<option value="SMS">SMS</option>
												<option value="Street Hoardings">Street Hoardings</option>
												<option value="Event">Event</option>
												<option value="TV/Radio">TV/Radio</option>
											<?php } ?> 
											
										</select>
									</div>
								</div>
								
								<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
									<div class="form-group ">
										<label class=" control-label">Lead representative</label>
										<?php if(isset($eid))
											{
											$type=$edit['leaduser']; ?>
											<select class="form-control" name="leaduser" <?=($_SESSION['u_role']==1)?'required':'disabled'?>>
											    <option value="">-- Select User --</option>
												<?php
													$sql2="SELECT * from user where branch_id='".$branch_id."' order by name asc";
													$result2=query_by_id($sql2,[],$conn);
													foreach($result2 as $row2) {
													?>
													<option value="<?php echo $row2['id']; ?>" <?php if($type==$row2['id']) echo "selected"; ?>><?php echo $row2['name']; ?></option>
												<?php } ?>
												<!--<option value="1"  <?php if ($type=="1") echo "selected"; ?>>Admin</option>-->
												<!--<option value="2"  <?php if ($type=="2") echo "selected"; ?>>User</option>-->
												<!--<option value="3"  <?php if ($type=="3") echo "selected"; ?>>Service provider</option>-->
												
											</select>		
											<?php 	}
											else
											{
											?>
											
											<select class="form-control" name="leaduser" <?=($_SESSION['u_role']==1)?'required':'disabled'?>>
												<option value="">-- Select User --</option>
												<?php
													$sql2="SELECT * from user where branch_id='".$branch_id."' order by name asc";
													$result2=query_by_id($sql2,[],$conn);
													foreach($result2 as $row2) {
													?>
													<option value="<?php echo $row2['id']; ?>" <?php if($uid==$row2['id']) echo "selected"; ?>><?php echo $row2['name']; ?></option>
												<?php } } ?>
										</select>
									</div>
								</div>
								<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
									<div class="form-group ">
										<label class=" control-label">Lead status <span class="text-danger">*</span></label>
										<select class="form-control" name="leadstatus" required>
											<?php
												if(isset($eid))
												{ 
													$type=$edit['leadstatus'];
												?>
												<option value="Pending" <?php if($type=="Pending") echo"selected";?> selected>Pending</option>
												<option  value="Converted" <?php if($type=="Converted") echo"selected";?>>Converted</option>
												<option value="Close" <?php if($type=="Close") echo"selected";?> >Close</option>
												<?php	}
												else
												{
												?>
												
												<option value="">-- Select a type --</option>
												<option value="Pending" selected>Pending</option>
												<option value="Converted">Converted</option>
												<option value="Close">Close</option>
												<?php 
												}
											?>
										</select>
									</div>
								</div>
								<div class="clearfix"></div>
								<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 ">
									<div class="form-group ">
										<?php if(isset($eid))
											{
											if($edit['leadstatus'] == 'Converted' || $edit['leadstatus'] == 'Close'){
											    ?>
											        <a href="enquiry.php">
												        <button type="button" class="btn btn-danger pull-right mr-left-5"><i class="fa fa-arrow-left" aria-hidden="true"></i>Go back</button>
											        </a>
											    <?php
											} else {
											?>
											<a href="enquiry.php">
												<button type="button" class="btn btn-danger pull-right mr-left-5"><i class="fa fa-times" aria-hidden="true"></i>Cancel</button>
											</a>
											<button type="submit" name="edit-submit" class="btn btn-info pull-right"><i class="fa fa-pencil-square-o" aria-hidden="true"></i>Update enquiry</button>
											<?php } }
											else
											{?>
											<button type="submit" id="enquiry-submit" name="submit" class="btn btn-success topmargin-sm pull-right"><i class="fa fa-plus" aria-hidden="true"></i>Add enquiry</button>
											
										<?php } ?>
									</div>
								</div>
							</form>
						</div>
					</div>
				</div>
			</div>
		</div>
		<?php if(isset($eid))
			{?>
			
			<div class="row gutter">
				
				<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
					<div class="panel">
						<div class="panel-heading">
							<h4>Enquiry Update History</h4>
						</div>
						<div class="panel-body">
							<div class="table-responsive">
								<table class="table table-condensed table-striped table-hover table-bordered pull-left dataTable no-footer" id="table" role="grid" aria-describedby="data-table_info">
									
									<thead>
										<tr>
											<th>Date</th>
											<th>Response</th>
											<th>Time updated</th>
											<th>Enquiry type</th>
											<th>Lead status</th>
											<th>Representative</th>
										</tr>
									</thead>
									<tbody>    
										<?php
											$sql2="SELECT * from enquiryresponse where active='0' and eid=$eid and branch_id='".$branch_id."' order by id desc";
											//echo $sql2;
											$result2=query_by_id($sql2,[],$conn);
											foreach($result2 as $row2) {
											?>
											<tr>
												<td><?php echo my_date_format($row2['date']); ?></td>
												<td><?php echo $row2['response']; ?></td>
												<td><?php echo my_time_format($row2['time']); ?></td>
												<td><?php echo $row2['enqtype']; ?></td>
												<td><?php echo $row2['leadstatus']; ?></td>
												<td><?php echo get_user($row2['user']); ?></td>
											</tr>
										<?php } ?>
									</tbody>
								</table>
							</div>
						</div>
					</div>
				</div>
			</div>
			
			<!-- Row ends -->
			<?php } else { ?>
			<div class="panel">
				<div class="panel-heading heading-with-btn">
					<h4 class="pull-left">View &amp; Manage all enquiry</h4>
					<span id="download-btn"></span>					
					<div class="clearfix"></div>
				</div>
				<div class="panel-body">
					<div class="row">
						<form action="" method="post">
						<div class="col-lg-2 col-md-2 col-sm-4 col-xs-12">
							<div class="form-group">
								<label for="date">Date to follow</label>
								<input type="text" class="form-control" range-attr="daterange" id="daterange" name="date"  placeholder="01/01/1990 - 12/05/2000" required>
							</div>
						</div>
						<div class="col-lg-2 col-md-2 col-sm-4 col-xs-6">
							<div class="form-group">
								<label class=" control-label">Enquiry for</label>
								<input type="text" class="form-control" name="enquiry_for" id="filterenquiry" value="<?php 
										if(isset($edit['enquiry']) && !empty($edit['enquiry'])){
											$id = EXPLODE(",",$edit['enquiry'])[1];
											if(EXPLODE(",",$edit['enquiry'])[0] == 'sr'){
												$sql ="SELECT CONCAT('sr',',',id) as id,name FROM `service` where active='0' and id='$id' and branch_id='".$branch_id."'";	
												}else if(EXPLODE(",",$edit['enquiry'])[0] == 'pr'){
												$sql ="SELECT CONCAT('pr',',',id) as id,name FROM `products` where active='0' and id='$id' and branch_id='".$branch_id."'";	
												}else if(EXPLODE(",",$edit['enquiry'])[0] == 'pa'){
												$sql ="SELECT CONCAT('pa',',',id) as id,name FROM `packages` where active='0' and id='$id' and branch_id='".$branch_id."'";	
											}
											$result=query_by_id($sql,[],$conn);
											foreach($result as $row) {
												echo  $row['name'];
												$edit_enquiry_id = $row['id'];
											} } ?>" placeholder="Autocomplete services / products / packages">
											<input type="hidden" id="filter_enquiry_service_id" value="<?=$edit_enquiry_id?>" name="enquiry">
							</div>
						</div>
						<div class="col-lg-2 col-md-2 col-sm-4 col-xs-6">
							<div class="form-group">
								<label class=" control-label">Lead representative</label>
								<select class="form-control" name="leaduserfilter" id="leaduserfilter">
									<option value="" <?php if($leadr=="") echo "selected"; ?>>-- Select User --</option>
									<?php
									$sql2="SELECT * from user where branch_id='".$branch_id."' order by username asc";
									$result2=query_by_id($sql2,[],$conn);
									foreach($result2 as $row2) {
										?>
									<option value="<?php echo $row2['id']; ?>" ><?php echo $row2['username']; ?></option>
									<?php } ?>
								</select>
							</div>
						</div> 
						<div class="col-lg-2 col-md-2 col-sm-4 col-xs-6">
							<div class="form-group">
								<label class=" control-label">Enquiry type</label>
								<select class="form-control" name="enq_type" id="enq_type">
									<option value="">-- Select a type --</option>
										<option value="Hot">Hot</option>
										<option value="Cold">Cold</option>
										<option value="Warm">Warm</option>
								</select>
							</div>
						</div>
						<div class="col-lg-2 col-md-2 col-sm-4 col-xs-12">
							<label for="lead_source">Source of enquiry</label>
							<select class="form-control" id="lead_source" name="lead_source">
								<option value="">-- Select A Type --</option>
								<option value="Client refrence">Client refrence</option>
								<option value="Cold Calling">Cold Calling</option>
								<option value="Facebook">Facebook</option>
								<option value="Twitter">Twitter</option>
								<option value="Instagram">Instagram</option>
								<option value="Other Social Media">Other Social Media</option>
								<option value="Website">Website</option>
								<option value="Walk-In">Walk-In</option>
								<option value="Flex">Flex</option>
								<option value="Flyer">Flyer</option>
								<option value="Newspaper">Newspaper</option>
								<option value="SMS">SMS</option>
								<option value="Street Hoardings">Street Hoardings</option>
								<option value="Event">Event</option>
								<option value="TV/Radio">TV/Radio</option>
							</select>
						</div>
						<div class="col-lg-1 col-md-2 col-sm-4 col-xs-12">
							<div class="form-group">
								<label>&nbsp;</label>
								<button type="button" id="filter" onclick="enquiryFilter();" name="filter" class="btn btn-filter btn-block"><i class="fa fa-filter" aria-hidden="true"></i>Filter</button>
							</div>
						</div>
						<div class="col-lg-1 col-md-2 col-sm-4 col-xs-12">
							<div class="form-group">
								<label>&nbsp;</label>
								<button type="reset" onclick="allenquiryAjax()" class="btn btn-danger btn-block"><i class="fa fa-times" aria-hidden="true"></i>Clear</button>
							</div>
						</div>
					</form>
					</div>
					<div class="table-responsive">
						<table id="smstab" class="table tableprint_enquiry table-striped no-margin table-bordered">
							<thead>
								<tr>
									<th width="25" class="notexport"><input type="checkbox" id="enqchk" ></th>				
									<th>Name</th>
									<th>Email</th>
									<th>Phone</th>
									<th>Date to follow</th>
									<th width="100">Lead type</th>
									<th>Enquiry for</th>
									<th width="150">Action</th>
								</tr>
							</thead>
							<tbody id="filterData">
								
							</tbody>
						</table>	
					</div>
				</div>
			</div>
		<?php } ?>
		
		<!-- Modal -->
			<div class="modal fade" id="myModal" role="dialog">
				<div class="modal-dialog">
					<!-- Modal content-->
					<div class="modal-content">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal">&times;</button>
							<h4 class="modal-title">SEND SMS</h4>
						</div>
						<div class="modal-body">
						    <div class="row">
							    <div class="col-md-6">
							        <div class="form-group">
							            <label>Category</label>
							            <select class="form-control" id="template_category" onchange="getTemplateType(this.value)">
							                
							            </select>
							        </div>
							    </div>
							    <div class="col-md-6">
							        <div class="form-group">
							            <label>Template type</label>
							            <select class="form-control" id="template_type" onchange="getTemplate(this.value)">
							                <option>--Select--</option>
							                
							            </select>
							        </div>
							    </div>
							</div>
							<div id="msg_body"></div>
							<br>
							<div id="send_button"></div>
							<div class="clearfix"></div>
							<div class="notes">
                    			<p><i class="fa fa-hand-o-right" aria-hidden="true"></i>Important notes:</p>
                    			<ol>
                    				<li>* Don't replace <b>{#name#}</b>, <b>{#salon_name#}</b>, <b>{#booking_link#}</b>, <b>{#referral_point#}</b>, <b>{#enquired_service#}</b> variables.</li>
                    				<li>* One variable {#__#} text should be less then 30 chars.</li>
                    				<li>* No extra content is allowed in approved templates, you can only replace {#__#}.</li>
                    			</ol>
                    		</div>
							
						</div>
					</div>
					
				</div>
			</div>
			<!-- Modal End --> 
			
			<!-- modal to select templates to send messages -->
            <div class="modal fade disableOutsideClick" id="final_templates" role="dialog">
            	<div class="modal-dialog  modal-lg">
            		<!-- Modal content-->
            		<div class="modal-content">
            			<div class="modal-header">
            			    <button type="button" class="close" data-dismiss="modal">&times;</button> 
            				<h4 class="modal-title"><span id="mtitle">Choose template</h4>
            			</div>
            			<div class="modal-body">
            				<div class="panel-body row">
            				    
            			    </div>
            			</div>
            		</div>
            	</div>
            </div>
            <!-- modal end -->
		
		
	</div>
	<!-- Main container ends -->
	
</div>
<!-- Dashboard Wrapper End -->

</div>
<!-- Container fluid ends -->

<?php 
	include "footer.php";
	function get_user($cid) {
		global $conn;
		global $branch_id;
		$sql="SELECT * from user where id='$cid' and branch_id='".$branch_id."'";
		$result=query_by_id($sql,[],$conn);
		foreach($result as $row) {
			return $row['name'];
		}
	}
?>	

<script>
	$(document).ready(function(){
		allenquiryAjax();
		getTemplateCategory();
		$('#enquiry-submit').submit(function() {
          $(this).find("button[type='submit']").prop('disabled',true);
        });
	});
	
	function getTemplateCategory(){
        $.ajax({
           url : '<?= SMS_API_URL ?>sms_templates.php',
           type : 'post',
           data : {action : 'get_template_category'},
           success : function(res){
               if(res != ''){
                   $('#template_category').html(res);
               }
           }
        });
    }
    
    function getTemplateType(value){
        if(value == ''){
            $('#template_type').html('<option value="">--Select--</option>');
            $('#msg_body, #send_button').html('');
        } else {
            $.ajax({
                url : '<?= SMS_API_URL ?>sms_templates.php',
                type : 'post',
                data : {action : 'getTemplate', category : value},
                success : function(res){
                    if(res != ''){
                        $('#template_type').html('<option value="">--Select--</option>'+res);
                    } else {
                        $('#template_type').html('<option value="">--Select--</option>');
                    }
                }
            });
        }
    }
    
    function getTemplate(value){
        var template_category = $('#template_category').val();
        if(value == ''){
            $('#msg_body, #send_button').html('');
        } else {
            $.ajax({
                url : '<?= SMS_API_URL ?>sms_templates.php',
                type : 'post',
                data : {action : 'getMessages', group_name : value, template_category : template_category},
                success : function(res){
                    if(res != ''){
                        $('#final_templates .panel-body').html(res);
                        $('#final_templates').modal('show');
                    } else {
                        $('#msg_body, #send_button').html('');
                    }
                }
            });
        }
    }
    
    function useTemplate(tempid, sendas, element){
        var html = '<label for="userName">Message</label><br /><span class="text-danger">* Please check SMS content and update variable values before sending SMS.</span>';
        html += '<input type="hidden" id="tempid" value="'+tempid+'">';
        html += '<input type="hidden" id="sendas" value="'+sendas+'">';
        html += '<textarea id="message" style="height: 100px;" class="form-control">'+element.html()+'</textarea><p class="text-success" id="message_show"></p>';
        $('#msg_body').html(html);
        $('#send_button').html('<button type="submit" style="float : right;" onclick="sendmessage()" class="btn btn-success" id="final_submit">SEND SMS</button>');
        $('#final_templates').modal('hide');
    }
    
    
	$(function(){
		var refresh = window.localStorage.getItem('refresh');
		if (refresh==1){
			window.location.reload();
			window.localStorage.setItem('refresh','1');
			window.localStorage.removeItem("refresh");
			}else{
			window.localStorage.setItem('refresh','1');
		}	
		autocomplete_serr();
		EnqformValidaiorns();
	});
	
	function autocomplete_serr(){
        $("#enquiry").autocomplete({
			source: function(request, response) {
				$.getJSON("ajax/bill.php", { term: request.term}, response);
			},
			minLength: 1,
			select:function (event, ui) {  
				$('#enquiry_service_id').val(ui.item.id);
			}
		});
		$("#filterenquiry").autocomplete({
			source: function(request, response) {
				$.getJSON("ajax/bill.php", { term: request.term}, response);
			},
			minLength: 1,
			select:function (event, ui) {  
				$('#filter_enquiry_service_id').val(ui.item.id);
			}
		});	
		$(".client").autocomplete({
			source: "autocomplete/client.php",
			minLength: 1,
			select: function(event, ui) {
				event.preventDefault();
				$('#client_name').val(ui.item.name);
				$('#clientid').val(ui.item.id); 
				$('#cont').val(ui.item.cont); 
				$('#email').val(ui.item.email); 
				$('#address').val(ui.item.address); 
				$('#gender').val(ui.item.gender);
				$('#aniv').val(ui.item.anniversary);
				$('#dob').val(ui.item.dob);
				$('#gst').val(ui.item.gst);
				$('#cc').val('');
				$('#earned_points').val("");
			}
		});
	}
	function check() {
		$client_id = $('#clientid').val();
		jQuery.ajax({
			url: "checkccont.php?p="+$("#cont").val(),
			//data:'p='+$("#prod").val(),
			type: "POST",
			success:function(data){
				if(data == '1'){
					if($client_id == ''){
						$("#client-status").html("Contact number already exists");
						$('#cont').val("");
						$('#dob').val("");
						$('#aniv').val("");
					}
				}else{
					$("#client-status").html("");
					$("#clientid").val("");
					$(".client_name").val("");
					$('#dob').val("");
					$('#aniv').val("");
				}
			},
			error:function (){}
		});
	}
	$("#enqchk").click(function () {
		if($(this).prop("checked") == true){
			$('.chkk').prop("checked", true);
		}
		else if($(this).prop("checked") == false){
			$('.chkk').prop("checked", false);
		}
	});
	
	var json_values = [];
	function sendsms() {
		json_values = [];
		var count = $('input.chkk:checked').length;
		if(count>0){
			$(".chkk").each(function()
            {
				if($(this).is(':checked'))
                {
                    json_values.push({name: $(this).data("name"),contact: $(this).data("contact"), enq_service: $(this).data("service") });
				}
			});
			$('#myModal').modal('show');
			}else{
            alert('Please Select at least one enquiry'); 
		}
	}
	
	function sendmessage() {
	    if(confirm('Have you replaced appropriate variable {#__#} values in SMS content?')){
		var message = $('#message').val();
		var sendtype = $('#sendas').val();
		var tempid = $('#tempid').val();
		$.ajax({
			type: "POST",
			url: 'ajax/sendsms.php',
			data: {
				json: JSON.stringify(json_values,true),
				message: message,
				sendtype : sendtype,
				tempid : tempid
			},
			dataType: 'text',
			beforeSend : function(){
			  $('#final_submit').prop('disabled', true);
			  $('#message_show').text('SMS process started, please do not refresh or close page.');
			},
			success: function(result) {
				toastr.success(result);
				$('#msg_body').html('');
				$('#send_button').html('');
				$('#template_category option[value=""]').prop('selected', true);
				$('#template_type').html('<option value="">--Select--</option>');
				$('#final_submit').prop('disabled', false);
			    $('#message_show').text('');
			    $('input[type="checkbox"]').prop('checked', false);
			}
		});
	    }
	}



	// function to filter enquiry.

	function enquiryFilter(){
		debugger;
		var daterange = $('#daterange').val();
		var date = daterange.split("-");
		if(daterange == ''){
			var startdate = '';
			var enddate = '';
		} else {
			var startdate = isoDate(date[0]);
			var enddate = isoDate(date[1]);
		}
		var enquiry_for = $('#filter_enquiry_service_id').val();
		var enquiry_type = $('#enq_type').val();
		var lead_source = $('#lead_source').val();
		var lead_user = $('#leaduserfilter').val();
		enquiryAjax(startdate, enddate, enquiry_for, enquiry_type, lead_source,lead_user);
	}

	// ajax method for commission query

	function enquiryAjax(startdate, enddate, enquiry_for = '', enquiry_type = '', lead_source,lead_user){
		jQuery.ajax({
			url: "ajax/checkenq.php",
			data: {'startdate':startdate, 'enddate': enddate, 'enquiry_for': enquiry_for, 'enquiry_type' : enquiry_type, 'lead_source' : lead_source,'lead_user' : lead_user, 'filter_type':'enquiry'},
			type: "POST",
			beforeSend: function() {
            	$('#filter i').removeClass('fa-filter');
            	$('#filter i').addClass('fa-spinner fa-spin');
            	$('#filter').prop('disabled',true);
			},
			success:function(data){
				$(".tableprint_enquiry").dataTable().fnDestroy();
				$('#filter i').addClass('fa-filter');
            	$('#filter i').removeClass('fa-spinner fa-spin');
            	$('#filter').prop('disabled',false);
				$('#filterData').html(data);
				createDatatable();
			},
			error:function (){}
		});
	}

	// function to load all commission data of service provider

	function allenquiryAjax(){
		jQuery.ajax({
			url: "ajax/checkenq.php",
			data: {'filter_type':'allenquiry'},
			type: "POST",
			success:function(data){
			    $(".tableprint_enquiry").dataTable().fnDestroy();
				$('#filterData').html(data);
				createDatatable();
			},
			error:function (){}
		});
	}
	
	function createDatatable(){
	    var table = $('.tableprint_enquiry').DataTable({
			dom: 'lBfrtip',
			"bProcessing": true,
			"aaSorting":[],
			'lengthMenu': [[10, 25, 50, 100, 99999999], [10, 25, 50, 100, 'All']],
			"aoColumnDefs": [
                { "bSortable": false, "aTargets": [ 0, 7 ] }, 
            ],
			buttons: [	{
				extend: 'excelHtml5',
				text: '<i class="fa fa-file-excel-o"></i> Excel',
				titleAttr: 'Export to Excel',
				title: '<?php echo systemname($conn); ?>',
				exportOptions: {
					columns: ':not(.notexport)',
				}
			},
			{
				extend: 'csvHtml5',
				text: '<i class="fa fa-file-text-o"></i> CSV',
				titleAttr: 'CSV',
				title: '<?php echo systemname($conn); ?>',
				exportOptions: {
					columns: ':not(.notexport)',
				}
			},
			{
				extend: 'print',
				exportOptions: {
					columns: ':visible'
				},
				customize: function(win) {
					$(win.document.body).find( 'table' ).find('td:last-child, th:last-child').remove();
				}
			},
			],
			"fnDrawCallback": function (oSettings) {
				$("#smstab_wrapper").find('.pagination').append('<li class="paginate_button"><a href="javascript:void(0)" style="border-radius:0px;background-color:#2877aa;border-color:#2877aa;color:#fff;" class="btn btn-info" onclick="sendsms()"><i style="margin-left:0px;" class="fa fa-paper-plane" aria-hidden="true"></i>Send SMS</a></li>');
			},
		});
		var buttons = new $.fn.dataTable.Buttons(table, {
		     buttons: [{
					extend: 'excelHtml5',
					text: '<i class="fa fa-file-excel-o" aria-hidden="true"></i> Export',
					titleAttr: 'Export to Excel',
					title: '<?php echo systemname($conn); ?>',
					exportOptions: {
						columns: ':not(:last-child):not(.notexport)',
					}
				}
		    ]
		}).container().appendTo($('#download-btn'));

		buttons[0].classList.add('d-block');
		buttons[0].classList.add('custom-download-btn');
		buttons[0].classList.add('pull-right');
		buttons[0].classList.remove('dt-buttons');
		$('.custom-download-btn a').removeClass('btn-default');
		// $('.custom-download-btn a').attr({"data-toggle":"tooltip","data-placement":"top","data-html":"true"});
		$('.custom-download-btn a').addClass('btn-warning pull-right download-btn mr-left-5');
	}


  	// jquery function to change daterange date in iso date standard  Y-m-d (2019-11-01)

	function isoDate(date){	
		var datespit = date.split('/');
		var day = datespit[1].replace(' ','');
		var month = datespit[0].replace(' ','');
		var year = datespit[2].replace(' ','');
		return year+'-'+month+'-'+day;
	}

</script>
							