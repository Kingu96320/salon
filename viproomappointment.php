<?php
	include "./includes/db_include.php";
	
	$branch_id = $_SESSION['branch_id'];
	$uid = $_SESSION['uid'];
	if(isset($_GET['id']) && $_GET['id']>0)
	{
		$eid = $_GET['id'];
		$edit = query_by_id("SELECT DISTINCT c.gender,ai.status as status, ai.bill_created_status,b.name as provider,ai.notes,ai.dis,ai.doa,ai.appdate,ai.itime,ai.id,ai.role,ai.total,ai.paid,ai.pay_method,ai.subtotal,CONCAT(ai.tax,',',ai.taxtype) as tax_type,ai.status,ai.dis as total_discount,c.name,c.id as client_id,c.cont,c.dob,c.aniversary from `app_invoice_".$branch_id."` ai LEFT JOIN `client` c on c.id=ai.client LEFT join `app_invoice_items_".$branch_id."` aii on aii.iid=ai.id LEFT JOIN `beauticians` b on b.id=aii.staffid where ai.active=0  and ai.id='$eid' and ai.branch_id='".$branch_id."' order by ai.id desc",[],$conn)[0];
	} else if(isset($_GET['enqid']) && $_GET['enqid']>0){
		$enqid = $_GET['enqid'];
		$edit = query_by_id("SELECT customer as name,cont,1 as enquiry,client_id FROM `enquiry` where id='$enqid' and active='0' and branch_id='".$branch_id."'",[],$conn)[0];
	}  
	
	
		
	if(isset($_POST['submit'])){
	    $client         = addslashes(trim($_POST['clientid'])); // client id
		$name           = addslashes(trim($_POST['client'])); // client name
		$cont           = addslashes(trim($_POST['cont'])); // client contact number
		$gender         = addslashes(trim($_POST['gender']));
		$gst            = addslashes(trim(($_POST['gst']) ? $_POST['gst'] : ''));
		$dob            = addslashes(trim($_POST['dob']));
		$aniv           = addslashes(trim($_POST['aniv']));  // anniversary date 
		$time 	        = addslashes(trim(date('H:i',strtotime($_POST['start'])))); // appointment time
// 		$time 	        = addslashes(trim(date('H:i',strtotime($_POST['time'])))); // appointment time
		$leadsource     = addslashes(trim($_POST['leadsource'])); //source of client
		if($client == ''){
    		$client=get_insert_id("INSERT INTO `client` set `name`=:name,`cont`=:cont,`gst`=:gst,`gender`=:gender,`dob`=:dob,`aniversary`=:aniversary,`leadsource`=:leadsource, `active`=:active, `branch_id`='".$branch_id."'",['name'=>$name,'cont'  =>$cont,'gst'=>$gst,'gender'=>$gender,'dob'=>$dob,'aniversary'=>$aniv,'leadsource'=>$leadsource,'active'=>0],$conn);
    		query("INSERT INTO wallet SET iid='0', date = '".date('Y-m-d')."', client_id = '$client', wallet_amount='0', get_wallet_from='', status='1', branch_id='".$branch_id."'",[],$conn);
		} else {
			query("UPDATE `client` set `gender`=:gender,`dob`=:dob,`aniversary`=:aniversary, `leadsource`=:leadsource where id=:id and branch_id='".$branch_id."'",['gender'=>$gender,'dob'=>$dob,'aniversary'=>$aniv,'leadsource'=>$leadsource, 'id'=>$client],$conn);
		} 

		if($_POST['total_disc_row_type'] == '0'){   // discount type (INR or %)
			$dis = 'pr,'.$_POST['dis'];             // if %
		} else if($_POST['total_disc_row_type'] == '1'){
			$dis 	= CURRENCY.','.$_POST['dis'];   // if INR
		}
		
		$doa    = $_POST['doa'];
		// $disper = $_POST['discount2'];
		$role   = $_POST['role'];  //(source of appointment)
		$appdate = date('Y-m-d');
		$tax	 = 0;  // type of tax
		$taxx	 = 0;  // 
		$tax	 = 0;
		$typ 	 = 0;
		
		$adv 	= 0;   // advance payment
		$total 	= $_POST['total'];
		$sub_total=$_POST['total'];;
		$due    = 0;
		
		$method = 0;   // payment method
		$stat 	= 0;   // appointment status
            $detail = $_POST['detail'];   // missing field
            $starttime = date('H:i',strtotime($_POST['start']));   // missing field
            $endtime = date('H:i',strtotime($_POST['end']));   // missing field
            $roomid = $_POST['vip_room'];   // missing field
            $serpro = $_POST['ser_provider'];   // missing field
		
		$gtime  = $_POST['time'];   // appointment time
		$drr 	= 0;
		$notes  = addslashes(trim($_POST['notes']));  // extra notes for appointment

		$client_branch_id = $_POST['client_branch_id'];

		$aid = get_insert_id("INSERT INTO `app_invoice_".$branch_id."`(`client`,`doa`,`room_id`,`itime`,`role`,`dis`,`disper`,`tax`,`taxtype`,`pay_method`,`total`,`subtotal`,`bmethod`,`paid`,`due`,`notes`,`type`, `status`,`details`,`appdate`,`active`,`appuid`,`branch_id`,`client_branch_id`) VALUES ('$client','$doa','$roomid','$time','$role','$dis','$disper','$tax','$typ','$method','$total','$sub_total','$method','$adv','$due','$notes','1','$stat','$detail','$appdate',0,'$uid','$branch_id','$client_branch_id')",[],$conn);
		
                $vipcreate = get_insert_id("INSERT INTO `vip_appointment`(`inv_id`,`app_date`,`room_id`,`app_start_time`,`app_end_time`,`allocated_by`,`allocated_for`,`allocated`,`total`) VALUES ('$aid','$doa','$roomid','$starttime','$endtime','$serpro','$client','1','$total')",[],$conn);

		$gtime = $time;
		$advance = 0;
		

	
		$sms_data = array(
	        'name' => $name,
	        'date' => date('d-m-Y',strtotime($doa)),
	        'time' => date('h:i a',strtotime($_POST['start'])),
	        'salon_name' => systemname()
	    );
		
		send_sms($cont,$sms_data,'appointment_booking_software');
	 
		$_SESSION['t']  = 1;
		$_SESSION['tmsg']  = "Appointment Saved Successfully";
		echo '<meta http-equiv="refresh" content="0; url=viproomappointment.php" />';die(); 
	}
	
	
	
	
	include "topbar.php";
	include "header.php";
	include "menu.php";
?>
<style>
	.btn-add{
	padding: 6px 6px;
	margin-left :100px;
	position:relative;
	
	}
	.btn-remove{
	padding: 6px 6px;
	margin-left :100px;
	position:relative;
	}
</style>
<div class="dashboard-wrapper">
	
	<!-- Main container starts -->
	<div class="main-container">
		<form action="" method="post" id="main-form">
		<!-- Row starts -->
		<div class="row gutter">
			
			<div class="col-lg-9 col-md-12 col-sm-12 col-xs-12">
				
					<div class="panel">
						<div class="panel-heading">
							<h4><?= isset($eid)&&$eid!=''?'Update':'Create'; ?> appointment</h4>
						</div>
						<div class="panel-body">
						    <div id="member_ship_message"></div>
							<div class="row">
							<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
								<div class="form-group">
									<label for="client">Client name <span class="text-danger">*</span></label>
									<input type="text" class="client form-control client_name" id="client" name="client" placeholder="Autocomplete (Phone)" value="<?=$edit['name']?>" required>
									<input type="hidden" name="clientid" id="clientid" value="<?=$edit['client_id']?>" class="clt"> 
									<input type="hidden" name="client_branch_id" id="client_branch_id" value="<?=$edit['client_branch_id']?>" class="clt"> 
									
								</div>
							</div>
							<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
								<div class="form-group">
									<label for="cont">Contact number <span class="text-danger">*</span></label>
									<input type="text" class="form-control client" value="<?=$edit['cont']?>"  onBlur="check();contact_no_length($(this), this.value);" id="cont" name="cont" placeholder="Client contact" onkeyup="this.value = this.value.replace(/[^0-9\.]/g,'');" maxLength="<?= PHONE_NUMBER ?>" required>
									<span style="color:red" id="client-status"></span>
									<span style="color:red" id="digit_error"></span>
								</div>
							</div>	
							<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
								<div class="form-group">
									<?php $date = date('Y-m-d'); ?>
									<label for="doa">Appointment is on <span class="text-danger">*</span></label>
									<input type="text" class="form-control dat <?= isset($edit['id'])?'date':'min_present_date' ?>" id="date" onblur="$('.staff').val('')" onchange="dateAvailability(this.value)" value="<?=($edit['doa'])?$edit['doa']:$date ?>" name="doa" required readonly />
									<span class="text-danger" id="dateerror"></span>
								</div>
							</div>
							
							<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
								<div class="form-group">
									<?php $time = date('h:i A'); ?>
									<label for="time">Time of appointment <span class="text-danger">*</span></label>
									<input type='text' onchange="checkappTime('time',this.value,'apptime')" class="form-control slot dat <?= extratimeStatus()=='1'?'maintime':'time'?>" name="time"  value="<?=($edit['itime'])?date('h:i A',strtotime($edit['itime'])):$time; ?>" id="time" required readonly>
									<input type="text" class="hidden" id="close_time" value="<?= shopclosetime(); ?>">
									<span id="apptime" class="text-danger"></span>
								</div>
							</div>
							<div class="clearfix"></div>
							<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
								<div class="form-group">
									<label for="role">Source of appointment</label>
									<select class="form-control" name="role">
										<?php 
											$app_source = query_by_id("SELECT * from app_source where status='1' order by name asc",[],$conn);
											if($app_source){
												foreach ($app_source as $val){ ?>
													<option <?= $edit['role']==$val['id']?'selected':''?> value="<?=$val['id']?>"><?= $val['name'] ?></option>
												<?php }
											}								
										?>
									</select>
								</div>
							</div>
							<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
								<div class="form-group">
									<label for="">&nbsp;</label>
									<button type="button" onclick="todaySchedule($('#date').val())" class="btn btn-warning btn-block"><i class="fa fa-calendar" aria-hidden="true"></i>Check schedule</button>
								</div>
							</div>
							<div class="clearfix"></div>
							<div class="col-lg-12">
								<div class="table-responsive">
									<table id="catTable" class="table table-bordered">
										<thead>
											<tr>
												
												<th></th>
												<th></th>
												
											</tr>
										</thead>
										<tbody>
										
                                                                                    
											<tr>
												<td class="total" ></td>
												<td >
												<input type="number" step="0.01" class="key1 form-control" name="dis" id="total_disc"  value="<?=($edit['total_discount'])?explode(",",$edit['total_discount'])[1]:'0'?>" placeholder="Discount Amount" min="0">
                                                                                                </td>
												<!--<td >-->
<!--													<select class="form-control total_disc_row_type" name="total_disc_row_type" id="disc_row_type">
														<option <?=(substr($edit['total_discount'],0,3) == 'pr,')?'value="0" Selected':'value="0"'?>>%</option>
														<option <?=(explode(",",$edit['total_discount'])[0] == CURRENCY)?'value="1" Selected':'value="1"'?>><?=CURRENCY?></option>
													</select>-->
												<!--</td>-->
											</tr>
											
											<tr>
												<td class="total" >Start Time</td>
												<td >
												<input type="text"  name="start"   class=" form-control time start_time" value="<?php echo date('h:i A');?>" id="start" ></td>
											</tr>
											<tr>
												<td class="total" >End Time</td>
												<td >
												<input type="text"  name="end"  class=" form-control time end_time" value="<?php echo date('h:i A');?>" id="end" ></td>
											</tr>
                                                                                        <tr>
												<td class="total" id="tot" >Select Vip Room</td>
												<td><select name="vip_room" id="vip_room" onchange="check_app(this.value)" data-validation="required" class="form-control">
													<option value="0">Select Vip room</option>
													
														<?php 
														
															$vipsql="SELECT * FROM `vip_rooms` where active=0";
															$vipresult=query_by_id($vipsql,[],$conn);
															
														
															foreach ($vipresult as $viprow) {
															?>
                                                                                                                        
															<option value="<?php echo $viprow['id'];?>"><?php echo $viprow['room_name']; ?></option>
                                                               
															<!--<option <?=((($edit['tax_type']) == ($row2['id'].',0'))?'value="'.$row2['id'].','.$row2['tax'].',0"Selected':'value="'.$row2['id'].','.$row2['tax'].',0"')?>><?php echo $row2['title']; ?></option>-->
														<?php } ?>
													
													              
												</select>
                                                                                                </td>
											</tr>
                                                                                        <tr>
												<td class="total" >Service Provider</td>
												<td >
												<select name="ser_provider" id="ser_provider" onchange="check_app_staff(this.value)" data-validation="required" class="form-control ">
													<option>Select Service Provider</option>
													
														<?php 
															$buesql="SELECT * FROM `beauticians` where active=0";
															$bueresult=query_by_id($buesql,[],$conn);
															foreach ($bueresult as $buerow) {
															?>
															<option value="<?php echo $buerow['id'];?>"><?php echo $buerow['name']; ?></option>
														<?php } ?>
													
													              
												</select>
                                                                                                </td>
											</tr>
<!--											<tr>
												<td class="total" colspan="4">Taxes</td>
												<td colspan="2"><select name="tax" id="tax" data-validation="required" class="form-control">
													<option value="0,0,3">Select Taxes</option>
													<optgroup label="Inclusive Taxes">
														<?php 
															$sql2="SELECT * FROM `tax` where active=0 order by title asc";
															$result2=query_by_id($sql2,[],$conn);
															foreach ($result2 as $row2) {
															?>
															<option <?=((($edit['tax_type']) == ($row2['id'].',0'))?'value="'.$row2['id'].','.$row2['tax'].',0"Selected':'value="'.$row2['id'].','.$row2['tax'].',0"')?>><?php echo $row2['title']; ?></option>
														<?php } ?>
													</optgroup>
													<optgroup label="Exclusive Taxes">
														<?php 
															$sql2="SELECT * FROM `tax` where active=0 order by title asc";
															$result2=query_by_id($sql2,[],$conn);
															foreach ($result2 as $row2) {
															?>
															<option <?=((($edit['tax_type']) == ($row2['id'].',1'))?'value="'.$row2['id'].','.$row2['tax'].',1" Selected':'value="'.$row2['id'].','.$row2['tax'].',1"')?>><?php echo $row2['title']; ?></option>
														<?php } ?>
													</optgroup>              
												</select></td>
											</tr>-->
											<tr>
												<td class="total" id="tot">Total</td>
												<td><input type="text" id="total" class="form-control" name="total" placeholder="Total Amount" value="" ></td>
											</tr>
											
											
											
											
											<tr>
                                                                                            <td colspan="2"><textarea name="notes" class="form-control no-resize" rows="5" placeholder="Write Notes About Appointment here..." id="textArea"><?= $edit['notes']; ?></textarea></td>
											
											</tr>
										</tbody>
									</table>
								</div>
							</div>
                                                        
                                                        
                                                        
							<div class="clearfix"></div>
							<div class="col-sm-12">
							    <input type="hidden" id="mem_service" value="" />
    						    <input type="hidden" id="mem_product" value="" />
    						    <input type="hidden" id="mem_package" value="" />
    						    <input type="hidden" id="has_membership" value="0" />
    						    <input type="hidden" id="mem_condition" value="0" />
    						    <input type="hidden" id="mem_reward_point" value="0" />
    						    <input type="hidden" id="mem_bill_amount" value="0" />
    						    <input type="hidden" name="membership_appilied" id="membership_appilied" value="0" />
    						    <input type="hidden" name="membership_id" id="membership_id" value="0" />
    						    <input type="hidden" name="membership_reward_boost" id="membership_reward_boost" value="1" />
								<?php if(isset($_GET['id']) && $_GET['id']>0)
								if(isset($_GET['type']) && $_GET['type'] == 1){
								    if($edit['bill_created_status'] == 0 && $edit['status'] != 'Cancelled'){ ?>
								    <a href="appointment.php?cancel_appointment=1&id=<?= $eid ?>"><button type="button" name="cancel_appointment" class="btn mr-left-5 btn-danger pull-right">Cancel</button></a>
								    <input type="hidden" name="web_appointment" value="1" />
								    <button type="submit" name="edit-submit" class="btn btn-success pull-right"><i class="fa fa-calendar-check-o" aria-hidden="true"></i>Approve</button>
								<?php } } else
								{ 
								$offerstring = implode(',', $predata); ?>
								<input type="hidden" name="package_service" value="<?= $offerstring ?>">
								<?php if($edit['bill_created_status'] == 0 && $edit['status'] != 'Cancelled'){ ?>	
								<button type="submit" name="edit-submit" class="btn btn-info pull-right" ><i class="fa fa-calendar-plus-o" aria-hidden="true"></i>Update Appointment</button>
							<?php } ?>
								<?php }else{?>
								<button type="button" name="" class="btn mr-left-5 btn-danger pull-right" onClick="location.reload();">Reset</button>
								<button type="submit" name="submit" id="subapp" class="btn btn-success pull-right"><i class="fa fa-calendar-check-o" aria-hidden="true"></i>Create appointments</button>
							<?php } ?>	
							</div>
							</div>
						</div>
					</div>
				
			</div>
			<div class="col-lg-3 col-xs-12 grey-box">
				<div class="row">
					<div class="col-lg-12">
						<div class="panel">
						<div class="panel-heading">
							<h4><i class="fa fa-repeat mr-left-0 text-warning fa-spin" aria-hidden="true"></i>Client 360&#176; view</h4>
						</div>
						<div class="client-view">
							<div class="client-view-content">
								<div id="customer_type" >
									
								</div>
								
								<table width="100%" class="table table-striped">
									<tr>
										<td>Branch:</td>
										<td id="branch_name">----<br></td>
									</tr>
									<tr>
										<td>Last visit on:</td>
										<td id="last_visit">----<br></td>
									</tr>
									<tr>
										<td>Total visits:</td>
										<td id="total_visit">0</td>
									</tr>
									<tr>
										<td>Total spendings:</td>
										<td id="total_spending">0</td>
									</tr>
									<tr>
										<td>Membership:</td>
										<td id="membership">----</td>
									</tr>
									<tr>
										<td>Active packages:</td>
										<td id="active_package">----<br></td>
									</tr>
									
									<tr>
										<td>Last feedback:</td>
										<td id="last_feedback"><a href="#"><u>----</u></a></td>
									</tr>
									<tr>
										<td>My wallet	:</td>
										<td id="wallet">0</td>
										<input type="hidden" id="wallet_money" value="0">
									</tr>
									<tr>
										<td>Reward points:</td>
										<td id="earned_points">0</td>
										<input type="hidden" id="reward_point" value="0">
									</tr>
									
									
									<tr>
										<td>Gender:</td>
										<td id="gender">
											<select class="form-control" name="gender" id="gender">
												<option value="">--Select--</option>
												<option id="gn-1" value="1" selected>Male</option>
												<option id="gn-2" value="2">Female</option>
											</select>
										</td>
									</tr>
									<tr>
										<td>Date of birth	:</td>
										<td id="dob"><input type="text" class="form-control dob_annv_date" name="dob" id="clientdob" value="" readonly></td>
									</tr>
									<tr>
										<td>Anniversary	:</td>
										<td><input type="text" class="form-control dob_annv_date" name="aniv" id="anniversary" value="" readonly></td>
									</tr>
									
									<tr>
										<td>Source of client:</td>
										<td>       
											<select class="form-control" name="leadsource" id="leadsource">
												<option value="">--Select--</option>
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
										</td>
									</tr>												
								</table>
							</div>
						</div>
					</div>
					</div>
				</div>
			</div>
		
		</div>
		<!-- Row ends -->
		</form>
		
		<div class="clearfix"></div>
		
	</div>
	
</div>

</div>
<!-- Container fluid ends -->


<!-- Modal -->
<div class="modal fade disableOutsideClick" id="appointment" role="dialog">
	<div class="modal-dialog modal-lg">
		
		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Appointment Details</h4>
			</div>
			<div class="modal-body">
				<table class="table table-bordered">
					<tr>
						<thead>
							<th>Service provider</th>
							<th>Client name</th>
							<th>Service name</th>
							<th>Start time</th>
							<th>End time</th>
							<th>Duration</th>
						</thead>
					</tr>
					<tbody id="appoint">
					</tbody>
				</table>		
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times" aria-hidden="true"></i> Close</button>
			</div>
		</div>
	</div>
</div>
<!-- Modal End -->


<!-- Modal -->
<div class="modal fade disableOutsideClick" id="spschedule" role="dialog">
	<div class="modal-dialog modal-lg">
		
		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Service provider's schedule on <span id="spdate"></span></h4>
			</div>
			<div class="modal-body">
				<table class="table table-bordered">
					<tr>
						<thead>
							<th>Service provider</th>
							<th>Client name</th>
							<th>Service name</th>
							<th>Start time</th>
							<th>End time</th>
							<th>Duration</th>
						</thead>
					</tr>
					<tbody id="todaySchedule">
					</tbody>
				</table>		
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times" aria-hidden="true"></i> Close</button>
			</div>
		</div>
	</div>
</div>
<!-- Modal End -->


<?php include "footer.php"; ?>

<script>
 function check_app(room){
     var startt = $("#start").val();
     var endt = $("#end").val();
     var apdate = $("#date").val();
     
     $.ajax({
			url: "ajax/checkappoinment.php?startt="+startt+"&endt="+endt+"&room="+room+"&apdate="+apdate,
			type: "POST",
			success:function(data){
                            console.log(data);
				        var ds = JSON.parse(data.trim());
					if(ds['success']=='1'){
						toastr.success('Room Available for this time slot!!');
                           $('#subapp').prop('disabled', false);
						}else if(ds['success']=='0'){
						toastr.error('Room Unavailable for this time slot!!');
				// 		$("#vip_room").prop('selected', false); 
					document.getElementById("vip_room").value = "0";
                        $('#subapp').prop('disabled', true);
						}
			},
			error:function (){}
		});

    }


function check_app_staff(staffno){
     var startt = $("#start").val();
     var endt = $("#end").val();
     var apdate = $("#date").val();
     var room = $("#vip_room").val();
     
     $.ajax({
			url: "ajax/checkappoinmentstaff.php?startt="+startt+"&endt="+endt+"&apdate="+apdate+"&staffno="+staffno,
			type: "POST",
			success:function(data){
                            console.log(data);
				        var ds = JSON.parse(data.trim());
					if(ds['success']=='1'){
						toastr.success('Staff Available for this time slot!!');
                                                $('#subapp').prop('disabled', false);
						}else if(ds['success']=='0'){
						toastr.error('Staff Unavailable for this time slot!!');
                                                $('#subapp').prop('disabled', true);
						}
			},
			error:function (){}
		});

    }





// special discount division start
document.getElementById('total_disc').addEventListener('keyup', function(event) { 
    const key = event.key; 
    if(parseInt($("#has_membership").val()) == 0){
        if (key === "Backspace" || key === "Delete") {
            if(parseInt($("#total_disc").val())==0 || $("#total_disc").val()==''){
                $("#total_disc").val(0);
                $(".singleservicediscount").val(0);

                $(".TextBoxContainer").each(function(index, value) {
                var row = $(this);
                var qnt = row.find(".qt").val();
                var actual = row.find(".prr").val();
                var totalprice = parseInt(qnt) * parseInt(actual);
                row.find(".pr").val(totalprice);   
                price_calculate(row);     
                });
            }  
        }
    } 
}); 
$("#total_disc").keypress(function() {
    //debugger;
    if(parseInt($("#has_membership").val()) == 0){
        $(".singleservicediscount").val(0);
        var count = document.getElementsByClassName("singleservicediscount").length;
        $(".TextBoxContainer").each(function(index, value) {
            var row = $(this);
            var qnt = row.find(".qt").val();
            var actual = row.find(".prr").val();
            var totalprice = parseInt(qnt) * parseInt(actual);
            row.find(".pr").val(totalprice);
            
        });
    }
});

$("#total_disc").change(function() {
    //debugger;
    if(parseInt($("#has_membership").val()) == 0){
    var dis_type = $(".total_disc_row_type").val();
    dis_type = parseInt(dis_type);
    if (dis_type == 1) {
        $(".TextBoxContainer").each(function(index, value) {
            var row = $(this);
            row.find("#disc_row_type").val('1');
        });
        $(".singleservicediscount").val(0);
        var value = $(this).val();
        var count = document.getElementsByClassName("singleservicediscount").length;
        var total_per = parseInt(value) / parseInt(count);
        total_per = parseFloat(total_per.toFixed(2));
        var orignalprice = 0;
        var prev;
        
        for (var i = 0; i < count; i++) {
            prev = document.getElementsByClassName("servicepriceafterdiscount")[i].value;
            orignalprice = orignalprice + parseFloat(prev);
        }
        orignalprice = Math.round(orignalprice);
        var sum = 0;
        for (var i = 0; i < count; i++) {
            var price = document.getElementsByClassName("servicepriceafterdiscount")[i].value;
            discountper = (parseFloat(price) / orignalprice) * 100;
            discount = (discountper / 100) * parseFloat(value);
            document.getElementsByClassName("singleservicediscount")[i].value = parseFloat(discount.toFixed(2));
            var exactprice = parseInt(price) - discount;
            document.getElementsByClassName("servicepriceafterdiscount")[i].value = parseFloat(exactprice
                .toFixed(2));
            price_calculate($("#TextBoxContainer"));
            sum += exactprice;
        }
        $("#sum").html("<?= CURRENCY ?> " + sum.toFixed(2));

            $("#sum2").val(sum);
         
        $("#total_disc").val(0);
    } else {
        $(".TextBoxContainer").each(function(index, value) {
            var row = $(this);
            row.find("#disc_row_type").val('0');
        });
        $(".singleservicediscount").val(0);
        var value = $(this).val();
        var count = document.getElementsByClassName("singleservicediscount").length;

        var orignalprice = 0;
        var prev;
        for (var i = 0; i < count; i++) {
            prev = document.getElementsByClassName("servicepriceafterdiscount")[i].value;
            orignalprice = orignalprice + parseFloat(prev);
        }
        orignalprice = Math.round(orignalprice);
        var total_dis = (parseInt(value) / 100) * orignalprice;
        var total_per = parseInt(total_dis) / parseInt(count);
        total_per = parseFloat(total_per.toFixed(2));
        var sum=0;
        for (var i = 0; i < count; i++) {
            var price = document.getElementsByClassName("servicepriceafterdiscount")[i].value;
            discountper = (parseFloat(price) / orignalprice) * 100;
            discount = (discountper / 100) * parseFloat(total_dis);
            var exactprice = parseInt(price) - discount;
            document.getElementsByClassName("servicepriceafterdiscount")[i].value = parseFloat(exactprice
                .toFixed(2));
                
            if(document.getElementsByClassName("pa_ser")[i].value==''){
            document.getElementsByClassName("singleservicediscount")[i].value = value;}
            price_calculate($("#TextBoxContainer"));
                //sumup();
            sum += exactprice;
        }
        
        $("#sum").html("<?= CURRENCY ?> " + sum.toFixed(2));

            $("#sum2").val(sum);
        $("#total_disc").val(0);
    }
    }
});
// special discount  end

	/*******Server_side_datatable*********/
		$(document).ready(function(){
			var bill_status = <?= $edit['bill_created_status']!='1'?'0':$edit['bill_created_status'] ?>;
			if(bill_status == 1){
				$('input').prop('disabled',true);
				$('#btnAdd').hide();
			}
		});
		/*******End********/
	$(document).on("click", '#date', function() {
		// $('.staff option[value=""]').prop('selected',true);	
		// $('.ser').val("");
		// $('.start_time').val("");
		// $('.end_time').val("");
		// $('.ser_stime').val("");
		// $('.ser_etime').val("");
		// $('.prr').val("");
		// $('.pr ').val("");
		// $('.serr').val("");
		// $('.disc_row').val("");
		
	});
	
	
	$(document).on("change", '.staff', function() {
		
		<?php if(isset($_GET['id']) && $_GET['id'] > 0){  ?>
			var app_eid = <?=$_GET['id']?>
			<?php }else { ?>
			var app_eid  = 0;
		<?php } ?>

		
		staff = $(this).val();
		
		findDuplicate($(this)); //call diplicate element function
		
		var durr  		= $(this).parent().parent().find('.durr').val();
		var starttime   = $(this).parents('tr').find('.ser_stime').val();
		var endtime     = $(this).parents('tr').find('.ser_etime').val();
		var select_staff= $(this).parents('tr').find('.staff option[value=""]');	
		
		date = $('#date').val();
		time = $('#time').val();
		var durr_plus = 0;
		var prev_rows = $(this).parent().parent().prevAll('tr');
        $(this).parent().parent().prevAll('tr').each(function(){
            durr_plus += parseInt($(this).find('.durr').val());
		});
		if(starttime !=''){
			$.ajax({
				url: "ajax/appointment_stafftime.php?id="+staff+"&date="+date+"&time="+time+"&starttime="+starttime+"&endtime="+endtime+"&app_eid="+app_eid,
				type: "POST",
				success:function(data){
		
					var durr_count = 0;
					var ds = JSON.parse(data.trim());
					starttime = ds['start'];
					endtime = ds['end'];
					var ds = JSON.parse(data.trim());
					if(ds['success']=='0'){
						toastr.success(ds['data']['spcat']+' Available.');
						}else if(ds['success']=='1'){
						toastr.error(ds['data']['spcat']+' Unavailable.');
						select_staff.prop("selected",true);
						showmodal(date,staff);
						}else if(ds['success']=='2'){
						toastr.error(ds['data']['spcat']+' Unavailable.');
						select_staff.prop("selected",true);
					}
				},
				error:function (){}
			});
			}else{
			select_staff.prop('selected',true);
		}
	});
	
	function showmodal(d,s){
		$.ajax({
			url: "ajax/timeslot.php?date="+d+"&staff="+s,
			type: "POST",
			success:function(data){
				$("#appoint").html(data);
				$("#appointment").modal("show");
			},
			error:function (){}
		});
	}
	

	function todaySchedule(date){
		$.ajax({
			url: "ajax/timeslot.php",
			type: "POST",
			data: {date : date, 'action':'todaySchedule'},
			success:function(data){
				date = date.split('-');
				date = date[2]+'-'+date[1]+'-'+date[0];
				$('#spdate').text(date);
				$("#todaySchedule").html(data);
				$("#spschedule").modal("show");
			},
			error:function (){}
		});
	}

	// function to find duplicate service_provider
	function findDuplicate(e){
		duplicate_arr=[];
		var row=e.parents(".TextBoxContainer").find('.spr_row').children('table');
		var row1=$(".TextBoxContainer").parents('table.add_row').find('.spr_row').children('table.add_row');
		var val=row.find('.staff');
		val.each(function(){
			duplicate_arr.push($(this).val());
			
		});
		
		for(var i=0;i<duplicate_arr.length;i++){
			for(var j=i+1;j<duplicate_arr.length;j++){
				if(duplicate_arr[i]==duplicate_arr[j]){
					e.parents('table.add_row').find('td#minus_button').remove()
					e.parents('table.add_row').find('td#select_row').remove();
				}  
			}
		}
	}
	
	$(window).on('load', function(){
		<?php if(isset($_GET['id']) || isset($_GET['enqid'])){ ?>
			var clientID=$('#clientid').val();
			clientView(clientID);
			client_check_membership_availability(clientID);
		<?php } ?>
		
		var contact=$('#cont').val();
		$.ajax({
			url:"autocomplete/client.php",
			type:"POST",
			data:{cont:contact},
			success:function(data){
				if(data){
					var da=JSON.parse(data.trim());
					if(da[0]['id'] > 0){
						$('#clientid').val(da[0]['id']);
						$("#client-status").html("");
					}
				}
			}
		});
		
		<?php if(isset($_GET['enqid'])){ ?>
			$('.TextBoxContainer').each(function(){
				var e =$(this);
				$.ajax({
					url:"ajax/bill.php?term="+$('.ser').val()+"&ser_stime="+$('#date').val()+' '+$('#time').val()+"&page_info=app",
					type: "GET",
					success:function(data){
						var ui = JSON.parse(data.trim());
						e.find('.serr').val(ui[0]['id']);
						e.find('.prr').val(ui[0]['price']);
						e.find('.qt').val('1');
						e.find('.disc_row ').val('0');
						e.find('.duration').val(ui[0]['duration']);
						e.find('.ser_stime').val(ui[0]['ser_stime']);
						e.find('.ser_etime').val(ui[0]['ser_etime']);
						e.find('.start_time').val((ui[0]['ser_stime']).substring(11));
						e.find('.end_time').val((ui[0]['ser_etime']).substring(11));
						price_calculate(row);
						sumup();
					},
				});
				
			});
		<?php } ?>
		
		
		var e=$(this);
		var row=$(".TextBoxContainer");
		var row_len=row.length;
		var j=1;
		var k=0;
		var l=1;
		row.each(function(){			
			var table_row=row.eq(k).find('.spr_row').children('table');
			table_row.each(function(){
				table_row.eq(j).removeAttr('id');
				table_row.eq(j).addClass('add_row');
				table_row.eq(j).find('td#plus_button').remove();
				table_row.eq(j).find('tr').append('<td><span class="input-group-btn"><button onclick="$(this).parent().parent().parent().parent().parent().remove();"   class="btn btn-danger btn-remove btn_remove" type="button"><span class="glyphicon-minus" "></span></button></span></td>');
				$(".TextBoxContainer").eq(l).find('.sno').html('<span class="remm icon-trash2 " style="color:red;" onclick="change_timing($(this));sumup();increment_ids();membershipDiscounts();"></span>');
				j++;
			});
			j=1;
			k++;
			l++;
		});
	    increment_ids();
		sumup();	
		check();
		formValidaiorns();
	});
	
	<!-- Add service_Provider row -->
	$(document).on("click",".add_spr_row", function(){
		var td_clone=$("#TextBoxContainer").find('.spr_row').children('table#add_row').clone().addClass('add_row');
		td_clone.removeAttr('id');
		td_clone.find('td#plus_button').remove();
		td_clone.find('tr').append('<td id="minus_button"><span class="input-group-btn"><button onclick="$(this).parent().parent().parent().parent().parent().remove();" class="btn btn-danger btn-remove btn_remove" type="button"><span class="glyphicon-minus" "></span></button></span></td>');
		var l_row=$(this).parents(".TextBoxContainer").find('.spr_row');
		l_row.children('table:last').after(td_clone);
		
		// l_row.next().children('table.add_row').find('.staff option[value=""]').prop('selected',true); 	
		td_clone.find('.staff option[value=""]').attr('selected','selected'); 
		
		var staff_name=$(this).parents(".TextBoxContainer").find('.staff').attr('name');
		$(this).parents(".TextBoxContainer").find('.staff').attr('name',staff_name); 
	});


	// multiple payment method

	$(document).on("click",".add_spr_row_payment", function(){
		var empty = 0;

		if($('#total').val() == 0){
			empty += 1;
		} else {
			$('.adv').each(function(){
				if($(this).val() == 0 || $(this).val() == ''){
					empty += 1;
					$(this).addClass('invalid');
				} else {
					$(this).removeClass('invalid');
				}
			});

			$('.act').each(function(){
				if($(this).val() == 0 || $(this).val() == ''){
					empty += 1;
					$(this).addClass('invalid');
				} else {
					$(this).removeClass('invalid');
				}
			});
		}

		if(empty <= 0){
			var amount_div = $(this).parent().parent().parent();
			var td_clone=$("#TextBoxContainerPayment").find('.spr_row_payment').children('table#pay_methods').clone().addClass('pay_methods');
			td_clone.removeAttr('id');
			td_clone.find('td#plus_button_payment').remove();
			td_clone.find('.adv').val(0);
			td_clone.find('tr').append('<td id="minus_button"><span class="input-group-btn"><button onclick="$(this).parent().parent().parent().parent().parent().remove();sumup();" class="btn btn-danger btn-remove btn_remove" type="button"><span class="glyphicon-minus" "></span></button></span></td>');
			var l_row=$(this).parents(".TextBoxContainerPayment").find('.spr_row_payment');
			l_row.children('table:last').after(td_clone);	
			l_row.children('table.pay_methods').first().find('.act option[value="1"]').attr('selected','selected');  
			var staff_name=$(this).parents(".TextBoxContainerPayment").find('.act').attr('name');
			$(this).parents(".TextBoxContainerPayment").find('.act').attr('name',staff_name);
			price_change();
		}
	});
	
    $(document).ready(function(){
        autocomplete_serr();
        autocomplete_serr_cat();
        price_change();
		formValidaiorns();
        change_event();
        $(".client").autocomplete({
			source: "autocomplete/client.php",
			minLength: 1,
            select: function(event, ui) {
          
                event.preventDefault();
                $('#client').val(ui.item.name);
                $('#clientid').val(ui.item.id); 
                $('#client_branch_id').val(ui.item.client_branch_id); 
                $('#cont').val(ui.item.cont); 
                $('#gender').val(ui.item.gender);
				$('#anniversary').val(ui.item.anniversary);
				$("#client-status").html("");
				$('#clientdob').val(ui.item.dob);
				$('#gst').val(ui.item.gst);
				clientView(ui.item.id);
				
				/*******Client_REWARD_POINTS********/
					$.ajax({
						url : "ajax/client_reward_points.php?client_id="+ui.item.id+"&referral_code="+ui.item.referral_code,
						type: "POST",
						success:function(data){
							
							var obj = $.parseJSON(data);
							if(parseInt(obj['reward_points']) > 0){
								$('#earned_points').html(obj['reward_points']); 
							}
						}	
					});
					
					/*******END******/
					
				    // check client pending payments
				        
				    $.ajax({
				        url : "ajax/get_pending_payments.php",
				        type : "post",
				        data : {action : 'check_pending_payments', client_id : ui.item.id},
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
				    var client_id = $('#clientid').val();
				    client_check_membership_availability(client_id);
					}				
				});	
				 
				
				$("#btnAdd").bind("click", function() {
					var empty_fields = [];
					$('.ser').each(function(){
						if($(this).val() == ''){
							empty_fields.push('empty_field');
						}
					});
					$('.start_time').each(function(){
						if($(this).val() == ''){
							empty_fields.push('empty_field');
						}
					});
					$('.end_time').each(function(){
						if($(this).val() == ''){
							empty_fields.push('empty_field');
						}
					});
					$('.price').each(function(){
						if($(this).val() == ''){
							empty_fields.push('empty_field');
						}
					});
					if(empty_fields && empty_fields.length == 0){
						var clonetr = $("#TextBoxContainer").clone().addClass('TextBoxContainer');
						clonetr.removeAttr('id');
						clonetr.find("table.add_row").remove();
						clonetr.find('.sno').html('<span class="remm icon-trash2 " style="color:red;" onclick="change_timing($(this));sumup();increment_ids();membershipDiscounts();"></span>');
						//clonetr.find('input[text]').val('');
						clonetr.find('input').val('');
						$("#addBefore").before(clonetr);
						clonetr.find('.staff option[value=""]').prop('selected',true);
						autocomplete_serr_cat();
						// autocomplete_serr();
						price_change();
						change_event();
						increment_ids();
						formValidaiorns();
						$(".time").datetimepicker({
					        format: "HH:ii P",
					        showMeridian: true,
					        autoclose: true,
					        pickDate: false,
					        startView: 1,
				    		maxView: 1
					    });
					    $(".datetimepicker").find('thead th').remove();
				  		$(".datetimepicker").find('thead').append($('<th class="switch text-warning">').html('Pick Time'));
				  		$(".datetimepicker").find('tbody').addClass('alltimes');
				  		$('.switch').css('width','190px');
						// funcClass();
						$('.ser').on('click',function(){
							$(this).keydown();
							autocomplete_serr_top();
						});

						$('.ser').on('keyup',function(){
							if($(this).val().length > 0){
								autocomplete_serr();
							} else {
								autocomplete_serr_top();
							}
						});
					}
				});
				$(".ser_cat").on('keyup keydown keypress change',function(){
					if($(this).val()==''){
						$(this).parent().find('.ser_cat_id').val('');
					}
				});
			});
				function clientView(id){
						jQuery.ajax({
							url: "ajax/client_view.php",
							type: "POST",
							data:{client_id:id},
							beforeSend: function () {
								$('.client-view-content').fadeOut();
								$('.client-view').append('<div class="divloader"><div class="divloader_ajax_small"></div></div>');
							},
							success:function(data){
								if(data !=''){
						
									var ds = JSON.parse(data);
									// $('#last_visit').html('----');
									if(ds['lastvisit'] != ''){
										$('#last_visit').html(ds['lastvisit']);
									} else {
										$('#last_visit').html('');
									}

									if(ds['branch_name'] != ''){
										$('#branch_name').html(ds['branch_name']);
									} else {
										$('#branch_name').html('');
									}

									$('#total_visit').html(ds['total_visit']);
									$('#total_spending').html(ds['total_spending']+ ' <?= CURRENCY ?>');
									$('#membership').html(ds['membership']);
									if(ds['packages'] != ''){
										$('#active_package').html(ds['packages']+'<br><a href="javascript:void(0)" onClick="viewPackageModal('+id+')"><i class="icon-eye3"></i> View details</a>');
									} else {
										$('#active_package').html('----');
									}
									$('#earned_points').html(ds['reward_points']);
									$('#reward_point').val(ds['reward_points']);
									$('#last_feedback').html(ds['last_feedback']);
									$('#wallet').html(ds['wallet']+" <?=CURRENCY?>");
									$('#wallet_money').val(ds['wallet']);
									$('#gender option').attr('selected', false);
									if(ds['gender'] !=''){
									    $('#gender #gn-'+ds['gender']).attr('selected', true);
									} else {
									    $('#gender #gn-1').attr('selected', true);
									}
									
									if(ds['dob'] != '0000-00-00'){
										$('#clientdob').val(ds['dob']);
									} else {
										$('#clientdob').val('');
									}
									if(ds['Anniversary'] != '0000-00-00'){
										$('#anniversary').val(ds['Anniversary']);
									} else {
										$('#anniversary').val('');
									}
									$('#leadsource option[value="'+ds['leadsource']+'"]').prop('selected',true);
									$('.divloader').remove();
									
									if(ds['customer_type'] == 'active'){
										$('#customer_type').html("<div style='background:#00a400; margin:10px 0; padding:10px; border-radius:3px;'><h4 style='margin-bottom:0; color:#fff;'>Customer type: Active</h4><small class='text-white'>Active Customers - Customers who visit your outlet at regular intervals.  </small></div>");
										}else if(ds['customer_type'] == 'inactive'){
										$('#customer_type').html("<div style='background:#fa383e; margin:10px 0; padding:10px; border-radius:3px;'><h4 style='margin-bottom:0; color:#fff;'>Customer type: Defected customer</h4><small class='text-white'>Defected Customers - Customers who haven't visited your outlet and become inactive. </small></div>");
										} else if(ds['customer_type'] == 'newcustomer'){
											$('#customer_type').html("<div style='background:#622bfb; margin:10px 0; padding:10px; border-radius:3px;'><h4 style='margin-bottom:0; color:#fff;'>Customer type: New Customer</h4><small class='text-white'>Customer who haven't visited your outlet. </small></div>");
										} else{
										$('#customer_type').html("<div  style='background:#fff200; margin:10px 0; padding:10px; border-radius:3px;'><h4 style='margin-bottom:0;'>Customer type: Churn prediction</h4><small >Churn Prediction - Customers who haven't visited your outlet and who are likely to leave. </small> </div>");
									}
									$('.client-view-content').fadeIn();
									
								} 
							},
							error:function (){
								$('.client-view-content').css('display','none');
								$('.client-view').append('<div class="divloader"><div class="divloader_ajax_small"></div></div>');
							},
							
						});
					}
			// <!----increment_id's function -->
			function increment_ids(){
				var row_len=$('.TextBoxContainer');
				var i=0;
				row_len.each(function(){
					var a=$(this).find('.staff').attr('name','staffid'+i+'[]');
					i++; 
				});
			}
			<!--End code-->
			
			$(document).on("click input", '.start_time:eq(0)', function() {
				var s_time=$('.start_time:eq(0)').val();
				if(s_time !=""){
					// checkappTime('time',$('#time').val(),'apptime');
					servicestarttime(this.value, $(this));
				}
			});
			
			function addZero(i) {
				if (i < 10) {
					i = "0" + i;
				}
				return i;
			}
			
			
			//$(document).on("click input keydown ", '#time', function() {

				function appointmenttime(){
				
				var start_arr=[];	
				var end_arr  =[];
				var e=$('.TextBoxContainer');
				// e.find('.staff option[value=""]').prop('selected',true); 
				
				var date=$('#date').val();
				var duration = parseInt(e.find('.duration').val()); //duration
				
				var ser_stime=$('#time').val();
	
				e.find('.ser_stime').val(date+" "+time12to24(ser_stime));
				e.find('.start_time').val(ser_stime);
				
				var ser_stime1=e.find('.ser_stime').val();
				var da = new Date(ser_stime1.replace(/-/g, '/'));
				
				var new_endtime= new Date(da.getTime() + (duration * 60 * 1000));
				
				var final_atime=new_endtime.getFullYear() + '-' +('0' + (new_endtime.getMonth()+1)).slice(-2)+ '-' +  addZero(new_endtime.getDate()) + ' '+addZero(new_endtime.getHours())+ ':'+('00' + (new_endtime.getMinutes())).slice(-2)+ ':'+new_endtime.getSeconds()+'0';
				
				e.find('.ser_etime').val(final_atime);
				e.find('.end_time').val(onTimeChange(final_atime.substr(11)));
				
				var start = e.nextAll('tr').find('.ser_etime').length;
				var index_value=$('.TextBoxContainer').index();
				
				for(var i=0;i<start;i++){ 
					
					var prev_start_time = $(".ser_stime:eq("+(i+index_value)+")").val();
					
					var prev_stime = new Date(prev_start_time.replace(/-/g, '/'));
					var prev_duration = $('.duration:eq('+(i+index_value)+')').val(); //prev_duration
					
					var prev_starttime= new Date(prev_stime.getTime() + (prev_duration * 60 * 1000));
					
					var final_atime=prev_starttime.getFullYear() + '-' +('0' + (prev_starttime.getMonth()+1)).slice(-2)+ '-' +  addZero(prev_starttime.getDate()) + ' '+addZero(prev_starttime.getHours())+ ':'+('00' + (prev_starttime.getMinutes())).slice(-2)+ ':'+prev_starttime.getSeconds()+'0';
					
					var next_start_time = $(".ser_etime:eq("+(i+index_value)+")").val(); 
					
					var next_stime = new Date(next_start_time.replace(/-/g, '/'));
					var next_duration = $('.duration:eq('+(i+index_value+1)+')').val(); //next_duration
					var next_starttime= new Date(next_stime.getTime() + (next_duration * 60 * 1000));
					
					var final_etime=next_starttime.getFullYear() + '-' +('0' + (next_starttime.getMonth()+1)).slice(-2)+ '-' +  addZero(next_starttime.getDate()) + ' '+addZero(next_starttime.getHours())+ ':'+('00' + (next_starttime.getMinutes())).slice(-2)+ ':'+next_starttime.getSeconds()+'0';
					
					start_arr.push(final_atime);
					end_arr.push(final_etime);  
					
					$(".end_time:eq("+(i+index_value+1)+")").val(onTimeChange((end_arr[i]).substring(11))); 
					$(".ser_etime:eq("+(i+index_value+1)+")").val(end_arr[i]); 
					$(".start_time:eq("+(i+index_value+1)+")").val(onTimeChange((start_arr[i]).substring(11))); 
					$(".ser_stime:eq("+(i+index_value+1)+")").val(start_arr[i]); 
					// $(".start_time:eq("+(i+index_value+1)+")").attr('min',(start_arr[i]).substring(11));
					// $(".start_time:eq("+(i+index_value+1)+")").attr('max',(end_arr[i]).substring(11));
				}
			}
			// );
			
			
			// $(document).on("click input keydown", '.start_time', function() {
				function servicestarttime(time, current){
		
				var e = current;
				var date = $('#date').val();
				if(date != ''){
					$('#date').removeClass('invalid');
					$.ajax({  
			        url:"ajax/system_details.php",
			        method:"POST",
			        dataType: "json",
			        data: {date : date, 'action':'checkapptime'},
			        success:function(response){
			        	
			            	if(response.status == '1'){
			            		sstime = response.starttime;
			            		eetime = response.endtime;
			               		var stime = Date.parse('20 Aug 2000 '+sstime);
								var etime = Date.parse('20 Aug 2000 '+eetime);
								var cpmtime = Date.parse('20 Aug 2000 '+time+':00');
								if(stime == '' || etime == ''){
									
								} else {
									if(cpmtime < stime || cpmtime > etime){
										e.parents('tr').find('.start_time').val('');
										e.parents('tr').find('.ser_etime').val('');
										e.parents('tr').find('.end_time').val('');
									} else {
										
									var start_arr=[];	
									var end_arr  =[];
									
									e.parents('tr').find('.staff option[value=""]').prop('selected',true); 
									
									var date=$('#date').val();
									var duration = parseInt(e.parents('tr').find('.duration').val()); //duration
									var ser_stime=e.parents('tr').find('.start_time').val();
									
									e.parents('tr').find('.ser_stime').val(date+" "+time12to24(ser_stime));
									
									var ser_stime1=e.parents('tr').find('.ser_stime').val();
									var da = new Date(ser_stime1.replace(/-/g, '/'));
									
									var new_endtime= new Date(da.getTime() + (duration * 60 * 1000));
									if(!isNaN(duration)){
										var final_atime=new_endtime.getFullYear() + '-' +('0' + (new_endtime.getMonth()+1)).slice(-2)+ '-' +  addZero(new_endtime.getDate()) + ' '+(new_endtime.getHours()<10?'0':'')+new_endtime.getHours()+ ':'+(new_endtime.getMinutes()<10?'0':'')+new_endtime.getMinutes()+ ':'+new_endtime.getSeconds()+'0';
									} else {
										var final_atime = '2000-08-20'+' '+time12to24(ser_stime);
									}
									
									
									e.parents('tr').find('.ser_etime').val(final_atime);
									e.parents('tr').find('.end_time').val(onTimeChange(final_atime.substr(11)));
									
									var start = e.parents('tr').nextAll('tr').find('.ser_etime').length;
									
									var index_value=e.parents('.TextBoxContainer').index();
									
									
									for(var i=0;i<start;i++){ 
										$(".staff option[value='']:eq("+(i+index_value+1)+")").prop('selected',true);
										
										var prev_start_time = $(".ser_stime:eq("+(i+index_value)+")").val();
										var prev_stime = new Date(prev_start_time.replace(/-/g, '/'));
										var prev_duration = $('.duration:eq('+(i+index_value)+')').val(); //prev_duration
										
										
										var prev_starttime= new Date(prev_stime.getTime() + (prev_duration * 60 * 1000));
										var final_atime=prev_starttime.getFullYear() + '-' +('0' + (prev_starttime.getMonth()+1)).slice(-2)+ '-' +  addZero(prev_starttime.getDate()) + ' '+addZero(prev_starttime.getHours())+ ':'+('00' + (prev_starttime.getMinutes())).slice(-2)+ ':'+prev_starttime.getSeconds()+'0';
										
										var next_start_time = $(".ser_stime:eq("+(i+index_value+1)+")").val(); 
										var next_stime = new Date(next_start_time.replace(/-/g, '/'));
										var next_duration = $('.duration:eq('+(i+index_value+1)+')').val(); //next_duration
										var next_starttime= new Date(next_stime.getTime() + (next_duration * 60 * 1000));
										var final_etime=next_starttime.getFullYear() + '-' +('0' + (next_starttime.getMonth()+1)).slice(-2)+ '-' +  addZero(next_starttime.getDate()) + ' '+time12to24(addZero(next_starttime.getHours())+ ':'+('00' + (next_starttime.getMinutes())).slice(-2)+ ':'+next_starttime.getSeconds());
										
										start_arr.push(final_atime);
										end_arr.push(final_etime);  
										
										$(".end_time:eq("+(i+index_value+1)+")").val((end_arr[i]).substring(11)); 
										$(".ser_etime:eq("+(i+index_value+1)+")").val(end_arr[i]); 
										$(".start_time:eq("+(i+index_value+1)+")").val((start_arr[i]).substring(11)); 
										$(".ser_stime:eq("+(i+index_value+1)+")").val(start_arr[i]); 
										// $(".start_time:eq("+(i+index_value+1)+")").attr('min',(start_arr[i]).substring(11));
										// $(".start_time:eq("+(i+index_value+1)+")").attr('max',(end_arr[i]).substring(11));
									}

									}
								}
			               	} else {
			               		current.val('');
			               	}
			            }
			       	});
				} else {
					current.val('');
					$('#date').addClass('invalid');
				}
				
			}
			// ); 
			
			
			/* time changing function*/
			function change_timing(e){
				var start_arr=[];	
				var end_arr  =[];
				update_temp_qty(e);
				var ser_stime = e.parents('tr').find('.ser_stime').val(); //start time
				var ser_etime = e.parents('tr').find('.ser_etime').val(); //end time
				var d2 = new Date(ser_stime.replace(/-/g, '/'));
				var d1 = new Date(ser_etime.replace(/-/g, '/'));
				var diff_minutes =  (d1- d2);
				
				var start = e.parents('tr').prevAll('tr').find('.ser_etime').length;
				var count = e.parents('tr').nextAll('tr').find('.ser_etime').length;
				var p_service = e.parents('tr').find('.pa_ser').val();
				if(p_service != ''){
					e.parents('tr').hide();
				} else {
					e.parents('tr').remove();
				}
				 
				
				var add_in_start  = $(".ser_etime:eq("+(start-1)+")").val();
				start_arr.push(add_in_start);
				
				
				for(var i=0;i<count;i++){	
					var add_in_end = $(".ser_etime:eq("+(i+start)+")").val();
					var add_in_start1  = $(".ser_stime:eq("+(i+start)+")").val();
					var d2 = new Date(add_in_end.replace(/-/g, '/'));
					var new_endtime= (new Date(d2 - diff_minutes));
					
					var final_etime=new_endtime.getFullYear() + '-' +('0' + (new_endtime.getMonth()+1)).slice(-2)+ '-' +  addZero(new_endtime.getDate()) + ' '+addZero(new_endtime.getHours())+ ':'+('00' + (new_endtime.getMinutes())).slice(-2)+ ':'+new_endtime.getSeconds()+'0';
					
					end_arr.push(final_etime);
					start_arr.push(final_etime); 
					$(".end_time:eq("+(i+start)+")").val((end_arr[i]).substring(11)); 
					$(".ser_etime:eq("+(i+start)+")").val(end_arr[i]); 
					$(".start_time:eq("+(i+start)+")").val((start_arr[i]).substring(11)); 
					$(".ser_stime:eq("+(i+start)+")").val(start_arr[i]);  
					
				}
				
			}
			
			function autocomplete_serr_cat(){
				$(".ser_cat").autocomplete({
					source: "ajax/bill_cat.php",
					minLength: 1,
					select:function (event, ui) {  
						var row = $(this).parent().parent();
						var row1= $(this).parents('tr');
						$(this).val(ui.item.value);
						row.find('.ser_cat_id').val(ui.item.id);
						row1.find('.ser').val("");
						row1.find('.start_time').val("");
						row1.find('.end_time').val("");
						row1.find('.ser_stime').val("");
						row1.find('.ser_etime').val("");
						row1.find('.prr').val("");
						row1.find('.pr ').val("");
						row1.find('.serr').val("");
						row1.find('.disc_row').val("");
						row1.find('.staff option[value=""]').prop('selected',true);
						
					}
				});	
			}


			$('.ser').on('click',function(){
				$(this).keydown();
				autocomplete_serr_top();
			});

			$('.ser').on('keyup',function(){
				if($(this).val().length > 0){
					autocomplete_serr();
				} else {
					autocomplete_serr_top();
				}
			});

			function autocomplete_serr_top(){
				$(".ser").autocomplete({ 
					source: function(request, response) {
						var ser_stime = '';
						if($(this.element).parent().parent().parent().parent().parent().parent().attr('id')=='TextBoxContainer'){
							ser_stime = $('#date').val()+' '+$('#time').val();
							}else{
							ser_stime = $(this.element).parent().parent().parent().parent().parent().parent().prev('tr').find('.ser_etime').val();
						}
						$.getJSON("ajax/bill.php", { term: 'topservices',ser_cat_id: $(this.element).parent().parent().find('.ser_cat_id').val(),ser_stime:ser_stime,page_info:'topservices' }, response);
					},
					minLength: 0,
					select:function (event, ui) { 
						var appo_time = $('#time').val();
						var appo_date = $('#date').val();
						if(appo_time == ''){
							$('#time').addClass('invalid');
						} else if(appo_date == ''){
							$('#date').addClass('invalid');
							$('#time').removeClass('invalid');
						} else{
						
							$('#date').removeClass('invalid');
							var etime = Date.parse('20 Aug 2000 '+$('#close_time').val());
							var setime = Date.parse('20 Aug 2000 '+ui.item.ser_etime.split(' ')[1]+':00');
							var et_status = '<?= extratimeStatus(); ?>'; // Extra time status
							if(setime > etime && et_status == '0'){	
								var row = $(this).parent().parent();
								// var row = $(this).parent().parent().parent().parent().parent().parent();
								row.find('input[type="text"].ser').val('');
								row.find('.serr').val('');
								// row.find('.pa_ser').val('');
								row.find('.prr').val('');
								row.find('.qt').val('0');
								row.find('.disc_row ').val('0');
								row.find('.duration').val('');
								row.find('.ser_stime').val('');
								row.find('.ser_etime').val('');
								row.find('.start_time').val(('').substring(11));
								row.find('.end_time').val(('').substring(11));
					
								toastr.error('Appointment can\'t book for this service. salon will close at '+onTimeChange($('#close_time').val()));
							} else {
								var row = $(this).parent().parent();
								// var row = $(this).parent().parent().parent().parent().parent().parent();
								if(row.find('.pa_ser').val() != ''){
									$.ajax({
										url : 'ajax/bill.php',
										method : 'post',
										data : {id : row.find('.pa_ser').val(), action : 'removeTemp' },
										success : function(response){
								
										}
									});
									row.find('.pa_ser').val('');
								}
								row.find('.serr').val(ui.item.id);
								row.find('.prr').val(ui.item.price);
								row.find('.qt').val('1');
								row.find('.disc_row ').val('0');
								row.find('.duration').val(ui.item.duration);
								row.find('.ser_stime').val(ui.item.ser_stime);
								row.find('.ser_etime').val(ui.item.ser_etime);
								row.find('.start_time').val(onTimeChange((ui.item.ser_stime).substring(11)));
								row.find('.end_time').val(onTimeChange((ui.item.ser_etime).substring(11)));
								var appointment_date = $('#date').val();
								$.ajax({
								    url : "ajax/service.php",
								    method : "POST",
								    data : { action : 'provider_list_by_service', service_id : ui.item.id, appointment_date : appointment_date},
								    dataType : "JSON",
								    success : function(response){
								        var options = '<option value="">Service provider</option>';
								        if(response.length != 0){
								            $.each(response, function(i, item) {
                                                options += '<option value="'+item.id+'">'+item.name+'</option>';
                                            });
								        }
								        row.find('.staff').html(options);
								    }
								});
								appointmenttime();
								var clientId = $('#clientid').val();
								if(clientId != ''){
						
									$.ajax({
										url : 'ajax/bill.php',
										method : 'post',
										data : { sid : ui.item.id, cid : clientId, 'action':'packages' },
										success : function(response){
								
											if(response != '0'){
									
												$('#avpackageModal').remove();
												row.append(response);
												if($('#avpackageModal table tbody').children().length != 0) {
													$('#avpackageModal').modal('show');
												}
											} else {
												$.ajax({
													url : 'ajax/bill.php',
													method : 'post',
													data : {id : row.find('.pa_ser').val(), action : 'removeTemp' },
													success : function(response){
											
													}
												});
												row.find('.pa_ser').val('');

											}
										}
									});
								}
					
								price_calculate(row);
								sumup();
								// paymode($('.act').val());
							}
						}
					}
				});	
			}

			function autocomplete_serr(){
				$(".ser").autocomplete({ 
					source: function(request, response) {
						var ser_stime = '';
						if($(this.element).parent().parent().parent().parent().parent().parent().attr('id')=='TextBoxContainer'){
							ser_stime = $('#date').val()+' '+$('#time').val();
							}else{
							ser_stime = $(this.element).parent().parent().parent().parent().parent().parent().prev('tr').find('.ser_etime').val();
						}
						$.getJSON("ajax/bill.php", { term: request.term,ser_cat_id: $(this.element).parent().parent().find('.ser_cat_id').val(),ser_stime:ser_stime,page_info:'app' }, response);
					},
					minLength: 1,
					select:function (event, ui) { 
						var appo_time = $('#time').val();
						var appo_date = $('#date').val();
						if(appo_time == ''){
							$('#time').addClass('invalid');
						} else if(appo_date == ''){
							$('#date').addClass('invalid');
							$('#time').removeClass('invalid');
						} else{
						
							$('#date').removeClass('invalid');
							var etime = Date.parse('20 Aug 2000 '+$('#close_time').val());
							var setime = Date.parse('20 Aug 2000 '+ui.item.ser_etime.split(' ')[1]+':00');
							var et_status = '<?= extratimeStatus(); ?>'; // Extra time status
							if(setime > etime && et_status == '0'){	
								// var row = $(this).parent().parent().parent().parent().parent().parent();
								var row = $(this).parent().parent();
								row.find('input[type="text"].ser').val('');
								row.find('.serr').val('');
								// row.find('.pa_ser').val('');
								row.find('.prr').val('');
								row.find('.qt').val('0');
								row.find('.disc_row ').val('0');
								row.find('.duration').val('');
								row.find('.ser_stime').val('');
								row.find('.ser_etime').val('');
								row.find('.start_time').val(('').substring(11));
								row.find('.end_time').val(('').substring(11));
					
								toastr.error('Appointment can\'t book for this service. salon will close at '+onTimeChange($('#close_time').val()));
							} else {
								// var row = $(this).parent().parent().parent().parent().parent().parent();
								var row = $(this).parent().parent();
								if(row.find('.pa_ser').val() != ''){
									$.ajax({
										url : 'ajax/bill.php',
										method : 'post',
										data : {id : row.find('.pa_ser').val(), action : 'removeTemp' },
										success : function(response){
								
										}
									});
									row.find('.pa_ser').val('');
								}
								row.find('.serr').val(ui.item.id);
								row.find('.prr').val(ui.item.price);
								row.find('.qt').val('1');
								row.find('.disc_row ').val('0');
								row.find('.duration').val(ui.item.duration);
								row.find('.ser_stime').val(ui.item.ser_stime);
								row.find('.ser_etime').val(ui.item.ser_etime);
								row.find('.start_time').val(onTimeChange((ui.item.ser_stime).substring(11)));
								row.find('.end_time').val(onTimeChange((ui.item.ser_etime).substring(11)));
								var appointment_date = $('#date').val();
								$.ajax({
								    url : "ajax/service.php",
								    method : "POST",
								    data : { action : 'provider_list_by_service', service_id : ui.item.id, appointment_date : appointment_date},
								    dataType : "JSON",
								    success : function(response){
								        var options = '<option value="">Service provider</option>';
								        if(response.length != 0){
								            $.each(response, function(i, item) {
                                                options += '<option value="'+item.id+'">'+item.name+'</option>';
                                            });
								        }
								        row.find('.staff').html(options);
								    }
								});
								appointmenttime();
								var clientId = $('#clientid').val();
								if(clientId != ''){
						
									$.ajax({
										url : 'ajax/bill.php',
										method : 'post',
										data : { sid : ui.item.id, cid : clientId, 'action':'packages' },
										success : function(response){
								
											if(response != '0'){
									
												$('#avpackageModal').remove();
												row.append(response);
												if($('#avpackageModal table tbody').children().length != 0) {
													$('#avpackageModal').modal('show');
												}
											} else {
												$.ajax({
													url : 'ajax/bill.php',
													method : 'post',
													data : {id : row.find('.pa_ser').val(), action : 'removeTemp' },
													success : function(response){
											
													}
												});
												row.find('.pa_ser').val('');

											}
										}
									});
								}
					
								price_calculate(row);
								sumup();
								// paymode($('.act').val());
							}
						}
					}
				});	
			}
			
			function update_temp_qty(e){
	
				var parent = e.parents('tr');
				var val = parent.find('.pa_ser').val();
				if(val != ''){
					$.ajax({
						url : 'ajax/bill.php',
						method : 'post',
						data : {id : val, action : 'removeTemp' },
						success : function(response){
				
						}
					});
				}
			}
			
			function price_calculate(row){
				var pr = row.find('.prr').val();
				var qt = row.find('.qt').val();
				var sum = pr * 1;
				var disc_row_val = row.find('.disc_row').val();
				disc_row_val = disc_row_val>0?disc_row_val:0;
				var disc_row_type = row.find('.disc_row_type').val();
				if(disc_row_type=='0'){
				    if(parseFloat(disc_row_val) <= 100){
					    var disc_row = parseFloat((sum * disc_row_val)/100);
				    } else {
				        row.find('.disc_row').val('0');
				        var disc_row = 0;
				        toastr.warning("In % max discount should be 100%");
				    }
				} else {
					var disc_row = parseFloat(disc_row_val);
				}
	
				sum = sum - disc_row;
				row.find('.price').val(sum);
				var pric = 0;
				var sums = 0;
				var sump = 0;
				var sumt = 0;
				var sum  = 0;
				var ids  = $(".serr");
				
				
				var inputs = $(".price");
				for(var i = 0; i < inputs.length; i++){
					var service = $(ids[i]).val().split(',');
					if(service[0]=="sr"){
						sums = sums + parseFloat($(inputs[i]).val());
					}
					else if(service[0]=="pr"){
						sump = sump + parseFloat($(inputs[i]).val());
					}
					sum = parseFloat(sum) + parseFloat($(inputs[i]).val());
					$("#sum").html("Rs. "+sum.toFixed(2));
				}	
			}

			function sumup(){
				var pric = 0;
				var sums = 0;
				var sump = 0;
				var sumt = 0;
				var sum = 0;
				var ids = $(".serr");
				membershipDiscounts();
				var inputs = $(".price");
				for(var i = 0; i < inputs.length; i++){
					var service = $(ids[i]).val().split(',');
					if(service[0]=="sr"){
						sums = sums + parseFloat($(inputs[i]).val());
					}
					else if(service[0]=="pr"){
						sump = sump + parseFloat($(inputs[i]).val());
					}
					sum = parseFloat(sum) + parseFloat($(inputs[i]).val());
					sum = sum || 0;
					$("#sum").html("<?= CURRENCY ?> "+sum.toFixed(2));
					$("#sum2").val(sum);
					$("#total").val(sum);
				}
				
				
				var paid = $('#paid').val();
				$("#sums").val(sums);
				$("#sump").val(sump);
				$("#sum").val(sum);
				var dis = parseFloat($("#disc").val());
				dis = dis || 0;
				var cval = parseFloat($("#cval").val());
				cval = cval || 0;
				var cmax = parseFloat($("#cmax").val());
				cmax = cmax || 0;
				var paid = parseFloat($("#paid").val());
				paid = paid || 0;
				var csum = sum * cval / 100;
				if(csum > cmax){
					csum = cmax;
				}
				csum = csum || 0;
				sum = sum - csum;
				
				var total = sum - dis ;
				
				
				if($('.total_disc_row_type').val() == 1){
					var tot_disc =$("#sum2").val();
					var tot_dis=$('#total_disc').val();
					total = tot_disc - tot_dis;
				} else { 
					var tot_disc = $("#sum2").val();
					var tot_dis = $('#total_disc').val();
					if(tot_dis <= 100){
					    total = total - (tot_disc * tot_dis / 100);
					} else {
					    $('#total_disc').val('0');
					    total = total;
					    toastr.warning("In % max discount should be 100%");
					}
				}
				
				var tax = $('#tax').val();
				var taxx = tax.split(',');
				
				if(taxx[2]!=0){
					var tsum = total * parseFloat(taxx[1]) / 100;
					tsum = tsum || 0;
					total = total + tsum;
					}else{
					tsum = 0;
				}
					
				$("#total").val(total.toFixed(2));
				$("#paid").val(total.toFixed(2));
	
				var adv = 0;
				$('.adv').each(function(){
					adv += parseFloat($(this).val()||0);
				});
				// var pend = 0;
	
				if(adv <= total){
					var pend = total - parseFloat(adv);
				}else{
					$(".adv").val();
					$("#pend").val(0);
					var pend = parseFloat(adv);
				}
				
				$("#pend").html(pend.toFixed(2));
				var paid = $("#paid").val();
				var fin = total - paid - parseFloat(adv);
				
				$("#due").html(fin.toFixed(2));
				$("#chng").val(fin.toFixed(2));

				if($('#total').val() == 0){
					$('.adv').prop('readonly',true);
				} else {
					if(pend == 0){
						$('select[name=status] option:nth-child(2)').prop('selected',true);
						$('.add_spr_row_payment').css('pointer-events','none');
					} else {
				// 		$('select[name=status] option:nth-child(1)').prop('selected',true);
						$('.add_spr_row_payment').css('pointer-events','initial');
					}
					$('.adv').prop('readonly',false);
				}
				advKeyup();
			}
			function price_change(){
				$(".pr, #tax, .adv").on("keyup change", function () {
					sumup();
				});
			}

			function change_event(){
				
				$(".qt").on("keyup keypress change click", function () {
					var row = $(this).parent().parent(); 
					price_calculate(row);
					sumup();
					
				});
				$(".disc_row,.disc_row_type").on("blur keypress keyup keydown change", function () {
					var row = $(this).parent().parent().parent().parent().parent().parent(); 
					if(row.find('.prr').val() > 0){
						if ($(".total_disc_row_type").val() == 1) {
						    if(parseFloat($(this).val()) <= row.find('.prr').val()){
						        var row = $(this).parent().parent(); 
						        price_calculate(row);
						        sumup();
						    } else {
						        $('#toast-container .toast-warning').remove();
						        toastr.warning("Discount should be less then price");
						        $(this).val(0);
						        price_calculate(row);
						        sumup();
						    }
						} else {
							
						}
					} else {
					   if($(this).val() > 0){
					        $('#toast-container .toast-warning').remove();
					        toastr.warning("Price should be greater then 0 to apply discount");
					   }
					   $(this).val(0);
					}
				});
				
				$(".disc_row,.disc_row_type").on("keyup keypress change keydown", function () {
				    var row = $(this).parent().parent().parent().parent().parent().parent(); 
				    price_calculate(row);
					sumup();
				});
				$("#total_disc,.total_disc_row_type").on("keyup keypress change keydown", function () {
				    sumup();
				});
				$("#total_disc,.total_disc_row_type").on("blur", function () {
				    var total_amount = parseFloat($('#sum2').val());
				    if($(this).val() > 0){
    				    if(total_amount > 0){
    				    	if ($(".total_disc_row_type").val() == 1) {
	    				        if(parseFloat($(this).val()) > total_amount){
	    				            $(this).val(0);
	    				            $('#toast-container .toast-warning').remove();
	    				            toastr.warning("Discount should be less then "+total_amount);
	    				            sumup();
	    				        } else {
	    				            sumup();
	    				        }
	    				    } else {
    				            sumup();
    				        }
    				    } else {
    				        $(this).val(0);
    				        sumup();
    				        $('#toast-container .toast-warning').remove();
    				        toastr.warning("Total amount should be greater then 0 to apply discount");
    				    }
				    }
				// 	sumup(); 
				});
			}
			
			
			function check() {
				$client_id = $('#clientid').val();				
				jQuery.ajax({
					url: "checkccont.php?p="+$("#cont").val(),
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
							$('#dob').val("");
							$('#aniv').val("");
						}
					},
					error:function (){}
				});
			}

	// function to use package service

	function usePackageService(service_id, row_id, e){
		var ser = e.parents('.TextBoxContainer').find('.pa_ser').val();
		if(ser != ''){
			$('#avpackageModal').modal('hide');
		} else {
			e.parents('.TextBoxContainer').find('.pa_ser').val(service_id+'-'+row_id);
			e.parents('.TextBoxContainer').find('.pr').val('0');
			e.parents('.TextBoxContainer').find('.prr').val('0');
			e.parents('.TextBoxContainer').find('.disc_row').val('0');
			$.ajax({
				url : 'ajax/bill.php',
				method : 'post',
				data : {row_id : row_id, action : 'tmpqty'},
				success : function(response){
					if(response == 1){
						sumup();
						$('#avpackageModal').modal('hide');
						setTimeout(function(){
							$('#avpackageModal').remove();
						}, 1000);
					} else {

					}
				}
			});
		}
	}

	function removeModal(modalid){
		setTimeout(function(){
			$('#'+modalid).remove();
		},1000);
	}

	function paymode(mode_id, modeDiv){
		var options = 0;
		var totalVal = parseInt($('#total').val());
		if(totalVal == 0){
			modeDiv.val('1');
		} else {
			$('.act').each(function(){
				var selected_options = $(this).val();
				if(mode_id == selected_options){
					options += 1;
				}
			});
			if(options > 1){
				toastr.warning('Payment option is already selected.');
				modeDiv.parent().parent().parent().parent().remove();
				$('#TextBoxContainerPayment table:first-child .input-group-btn').html('<button style="" class="btn btn-add btn-plus btn-success btn-add add_spr_row_payment" type="button"><span class="glyphicon-plus"></span></button>');
				$('#TextBoxContainerPayment table:first-child').attr('id','pay_methods');
				$('#TextBoxContainerPayment table:first-child .input-group-btn').parent().attr('id','plus_button_payment');
				$('#TextBoxContainerPayment table:first-child').removeClass('pay_methods');
				sumup();
				return false;
			}
		}
		var modeDiv = modeDiv.parent().parent();
		var wallet_money = parseInt($('#wallet_money').val());
		var reward_point = parseInt($('#reward_point').val());
		if(mode_id == '7'){	
			if(totalVal != 0){
				if(wallet_money == '0' || wallet_money == ''){
					toastr.warning('Wallet is empty.');
					modeDiv.find('.act').val('1');
					modeDiv.find('.adv').val('0');
				} 
				else {
					
					var price_cal = parseInt(wallet_money);
					if(totalVal < wallet_money){
						modeDiv.find('.adv').val(parseFloat($('#pend').text()));
					} else {
						modeDiv.find('.adv').val(price_cal);
					} 
					sumup();
					if(reward_point == '' || reward_point == '0'){

					} else {
						$('#reward_point').val(reward_point);
						$('#earned_points').text(reward_point);
					}
				}
			}
		} else if(mode_id == '9'){
			if(totalVal != 0){
				if(reward_point == '' || reward_point == '0'){
					modeDiv.find('.adv').val('0');
					toastr.warning('Don\'t have any reward point.');
					modeDiv.find('.act').val('1');
					sumup();
				} else {
					
					var point;
					var point_price = <?= redeemprice() ?>;
					var redeem_point = <?= redeempoint() ?>;
					var pprice = parseFloat($('#pend').text());
					if(reward_point > <?= maxredeempoint() ?>){
						point = <?= maxredeempoint() ?>;
					} else {
						point = reward_point;
					}
					var price_cal = (parseInt(point)/parseInt(redeem_point))*parseInt(point_price);
					
					if(pprice > price_cal){
						modeDiv.find('.adv').val(price_cal); 
					} else {
						modeDiv.find('.adv').val(pprice);
					}
					sumup();
				}
			}
		} else {
			modeDiv.find('.adv').val('0');
			sumup();
		}
	}
	
	$(document).on("blur", '#date', function() {
	    if($('#clientid').val() != ''){
		    client_check_membership_availability($('#clientid').val());
		}
	});

	function advKeyup(){	
		$('.adv').on('keyup',function(){
			var adv = 0;
			var redeem_point = <?= redeempoint() ?>;
			var max_points = <?= maxredeempoint() ?>;
			var mainDiv = $(this).parent().parent();
			var currentDiv = $(this);
			var rewardPoint = $('#reward_point').val();
			var walletMoney = $('#wallet_money').val();
			
			$('.adv').each(function(){
				adv += parseFloat($(this).val()||0);
			});
			if(parseFloat(adv) > parseFloat($('#total').val()) ){
				toastr.warning('Advance amount exceeded total amount.');
				currentDiv.val(0);
				sumup();
			}
			if(mainDiv.find('.act').val() == '9'){
				
				if(rewardPoint == '0'){
					toastr.warning('Don\'t have any reward point.');
				} else if(rewardPoint > 0){
				    if(parseFloat((currentDiv.val()*redeem_point)) > parseFloat(max_points)){
				        toastr.warning('You can redeem max '+max_points+' points ( '+(max_points/redeem_point) +' <?= CURRENCY ?> ) at a time.');
						currentDiv.val(0);
				    }
					else if(parseFloat((currentDiv.val()*redeem_point)) > parseFloat(rewardPoint)){
						toastr.warning('You have only '+rewardPoint+' reward points');
						currentDiv.val(0);
					}
				}
			} else if(mainDiv.find('.act').val() == '7'){
				if(walletMoney == '0'){
					toastr.warning('Wallet is empty.');
				} else if(walletMoney > 0){
					
					if(parseFloat(currentDiv.val()) > parseFloat(walletMoney)){
						toastr.warning('You have only '+walletMoney+' <?= CURRENCY ?> in wallet.');
						currentDiv.val(0);
					}
				}
			}
		});
	}
	
	
	function client_check_membership_availability(client_id){
    		$.ajax({
    			url : "ajax/check_membership_availability.php",
    			type: "POST",
    			data: {client_id:client_id},
    			success:function(data){
        			if(data){
            			var dt = $.parseJSON(data);
            			if(dt[0]['total'] == '1'){
        				var str = dt[0]['result']['time_update'];
        			 	dt[0]['result']['time_update'] = str.replace(" ", "T");//for mac and ios
            			 if(new Date(dt[0]['result']['time_update']) < new Date($('#date').val()+'T'+time12to24(($('#time').val())))){
            			    dt = dt[0]['result'];
            			    $('#has_membership').val(1);
            			    $('#mem_condition').val(dt['mem_condition']);
            			    $('#mem_reward_point').val(dt['min_reward_points_earned']);
            			    $('#mem_bill_amount').val(dt['min_bill_amount']);
            			    $('#membership_id').val(dt['md_id']);
		                    $('#membership_reward_boost').val(dt['reward_points_boost']);
            			    
            			    var s_dis = dt['discount_on_service'];
            			    var p_dis = dt['discount_on_product'];
            			    var pack_dis = dt['discount_on_package'];
            			    
            			    if(dt['mem_condition'] == 1){
            			        var condition = '<strong>AND</strong>';
            			    } else if(dt['mem_condition'] == 2){
            			        var condition = '<strong>OR</strong>';
            			    }
            			    
            			    $('#mem_service').val(s_dis);
            			    $('#mem_product').val(p_dis);
            			    $('#mem_package').val(pack_dis);
            			    
            			    s_dis = s_dis.split(",");
            			    p_dis = p_dis.split(",");
            			    pack_dis = pack_dis.split(",");
            			    
            			    if(s_dis[1] == 'pr'){
            			        s_dis = s_dis[0]+'%';
            			    } else {
            			        s_dis = s_dis[0]+' <?= CURRENCY ?>';
            			    }
            			    
            			    if(p_dis[1] == 'pr'){
            			        p_dis = p_dis[0]+'%';
            			    } else {
            			        p_dis = p_dis[0]+' <?= CURRENCY ?>';
            			    }
            			    
            			    if(pack_dis[1] == 'pr'){
            			        pack_dis = pack_dis[0]+'%';
            			    } else {
            			        pack_dis = pack_dis[0]+' <?= CURRENCY ?>';
            			    }
            			    
            			    var html = '<br /><div class="alert alert-success light"><i class="icon-check_circle"></i><strong>Wow! </strong>';
            			                html += 'This client has a membership with ';
            			                html += 'Discount on <strong>Service '+s_dis+'</strong> , ';
            			                html += 'Discount on <strong>Products '+p_dis+'</strong> , ';
            			                html += 'Discount on <strong>Packages '+pack_dis+'</strong>';
            			                html += '<div class="text-danger"><i style="margin-left:0px;" class="fa fa-exclamation-circle" aria-hidden="true"></i> <strong>Note:</strong> ';
            			                html += 'Minimum <strong>Reward point</strong> should be <strong>'+dt['min_reward_points_earned']+'</strong> '+condition+' minimum <strong>Bill amount</strong> should be <strong><?= CURRENCY ?> '+dt['min_bill_amount']+'</strong> to apply membership discount.';
            			                html += '</div>';
            			    $('#member_ship_message').html(html);
            			 
            			 }else{
            			     $('#member_ship_message').html('');
            			     emptyMembershipData();
            			 }
        			    }else{
        			        $('#member_ship_message').html('');
            				emptyMembershipData();
        			    }
        			} else {
        			    $('#member_ship_message').html('');
        			    emptyMembershipData();
        			}
        			membershipDiscounts();
        			sumup();
    			}
    		});
    	}
    		
		function emptyMembershipData(){
		    $('#member_ship_message').html("");
			$('#mem_service').val('');
		    $('#mem_product').val('');
		    $('#mem_package').val('');
		    $('#mem_condition').val('0');
		    $('#has_membership').val(0);
		    $('#mem_reward_point').val(0);
		    $('#mem_bill_amount').val(0);
		    $('#membership_id').val(0);
		}
    		
    		
	function membershipDiscounts(){
	    // membership discount calculation code		
									
		// check membership status
		var membership = parseInt($('#has_membership').val());
        // subtotal
        var subtotal = parseFloat($('#sum2').val());
		if(membership > 0 && membership == 1){
            // membership discount uploading code
	        var mem_service = $('#mem_service').val();
	        var mem_product = $('#mem_product').val();
	        var mem_package = $('#mem_package').val();
	        var reward_point = parseInt($('#reward_point').val());
	        var condition = parseInt($('#mem_condition').val());
	        var min_rpoint = parseInt($('#mem_reward_point').val());
	        var min_amount = parseFloat($('#mem_bill_amount').val());
	        
	        // spliting service types
	        mem_service = mem_service.split(',');
            mem_product = mem_product.split(',');
            mem_package = mem_package.split(',');
	                        
	        if(condition > 0 && condition == 1){ // AND condition
	            if(reward_point >= min_rpoint && subtotal >= min_amount){
	                $('.serr').each(function(){
	                   var parent = $(this).parent().parent();
	                   var type = parent.find('.serr').val();
	                   var pack = parent.find('.pa_ser').val();
	                   if(pack == ''){
	                        type = type.split(',')[0];
		                    if(type == 'sr'){
		                       if(parseFloat(parent.find('.prr').val()) < parseFloat(mem_service[0])){
		                           parent.find('.disc_row').val(parent.find('.prr').val());
		                       } else {
		                           parent.find('.disc_row').val(mem_service[0]);
		                       }
	                           parent.find('.disc_row').prop('readonly',true);
	                           if(mem_service[1] == 'pr'){
	                               parent.find('.disc_row_type option[value = 0]').prop('selected',true);
	                           } else {
	                               parent.find('.disc_row_type option[value = 1]').prop('selected',true);
	                           }
	                           parent.find('.disc_row_type option:not(:selected)').attr('disabled', true);
	                        } else if(type == 'pa'){
	                           parent.find('.disc_row').val(mem_package[0]);
	                           parent.find('.disc_row').prop('readonly',true);
	                           if(mem_package[1] == 'pr'){
	                               parent.find('.disc_row_type option[value = 0]').prop('selected',true);
	                           } else {
	                               parent.find('.disc_row_type option[value = 1]').prop('selected',true);
	                           }
	                           parent.find('.disc_row_type option:not(:selected)').attr('disabled', true);
	                        } else if(type == 'pr'){
	                           parent.find('.disc_row').val(mem_product[0]);
	                           parent.find('.disc_row').prop('readonly',true);
	                           if(mem_product[1] == 'pr'){
	                               parent.find('.disc_row_type option[value = 0]').prop('selected',true);
	                           } else {
	                               parent.find('.disc_row_type option[value = 1]').prop('selected',true);
	                           }
	                           parent.find('.disc_row_type option:not(:selected)').attr('disabled',true);
	                        }
	                        price_calculate(parent);
	                        $('#membership_appilied').val(1);
						   if(parent.find('.serr').val().split(',')[0] == 'mem'){
							    if($('#has_membership').val() == 1){
							        parent.find('.start_time').val('');
							        parent.find('.end_time').val('');
							        parent.find('.pr').val('0');
							        parent.find('.prr').val('');
							        parent.find('.rpoint').val('');
							        setTimeout(function(){
							            parent.find('.ser').val('');
							        },100);
							        toastr.warning("One membership is already activated.");
							    }
							} 
	                   }
	                });
	            } else {
	                $('.serr').each(function(){
	                     var parent = $(this).parent().parent();
	                     parent.find('.disc_row').val(0);
	                     parent.find('.disc_row').prop('readonly',false);
	                     parent.find('.disc_row_type option').prop('disabled',false);
	                     price_calculate(parent);
	                     $('#membership_appilied').val(0);
	                });
	            }
	        } else if(condition > 0 && condition == 2){ // OR condition
	            if(reward_point >= min_rpoint || subtotal >= min_amount){
	                $('.serr').each(function(){
	                   var parent = $(this).parent().parent();
	                   var type = parent.find('.serr').val();
	                   var pack = parent.find('.pa_ser').val();
	                   if(pack == ''){
	                        type = type.split(',')[0];
		                    if(type == 'sr'){
	                           if(parseFloat(parent.find('.prr').val()) < parseFloat(mem_service[0])){
		                           parent.find('.disc_row').val(parent.find('.prr').val());
		                       } else {
		                           parent.find('.disc_row').val(mem_service[0]);
		                       }
	                           parent.find('.disc_row').prop('readonly',true);
	                           if(mem_service[1] == 'pr'){
	                               parent.find('.disc_row_type option[value = 0]').prop('selected',true);
	                           } else {
	                               parent.find('.disc_row_type option[value = 1]').prop('selected',true);
	                           }
	                           parent.find('.disc_row_type option:not(:selected)').attr('disabled', true);
	                        } else if(type == 'pa'){
	                           parent.find('.disc_row').val(mem_package[0]);
	                           parent.find('.disc_row').prop('readonly',true);
	                           if(mem_product[1] == 'pr'){
	                               parent.find('.disc_row_type option[value = 0]').prop('selected',true);
	                           } else {
	                               parent.find('.disc_row_type option[value = 1]').prop('selected',true);
	                           }
	                           parent.find('.disc_row_type option:not(:selected)').attr('disabled', true);
	                        } else if(type == 'pr'){
	                           parent.find('.disc_row').val(mem_product[0]);
	                           parent.find('.disc_row').prop('readonly',true);
	                           if(mem_package[1] == 'pr'){
	                               parent.find('.disc_row_type option[value = 0]').prop('selected',true);
	                           } else {
	                               parent.find('.disc_row_type option[value = 1]').prop('selected',true);
	                           }
	                           parent.find('.disc_row_type option:not(:selected)').attr('disabled',true);
	                        }
	                        price_calculate(parent);
	                        $('#membership_appilied').val(1);
	                        <?php if(!isset($_GET['beid'])){ ?>
	                        if(parent.find('.serr').val().split(',')[0] == 'mem'){
							    if($('#has_membership').val() == 1){
							        parent.find('.start_time').val('');
							        parent.find('.end_time').val('');
							        parent.find('.pr').val('0');
							        parent.find('.serr').val('');
							        parent.find('.remm').click();
							        parent.find('.prr').val('');
							        parent.find('.rpoint').val('');
							        setTimeout(function(){
							            parent.find('.ser').val('');
							        },100);
							        toastr.warning("One membership is already activated.");
							    }
							}
							<?php } ?>
	                   }
	                });
	            } else {
	                $('.serr').each(function(){
	                     var parent = $(this).parent().parent();
	                     var editid = parseInt('<?= isset($_GET['id'])?$_GET['id']:0 ?>');
	                     if(editid == 0){
	                         parent.find('.disc_row').val(0);
	                     }
	                     parent.find('.disc_row').prop('readonly',false);
	                     parent.find('.disc_row_type option').prop('disabled',false);
	                     price_calculate(parent);
	                     $('#membership_appilied').val(0);
	                });
	            }
	        }
        } else {
            $('.serr').each(function(){
                 var parent = $(this).parent().parent();
                 var editid = parseInt('<?= isset($_GET['id'])?$_GET['id']:0 ?>');
                 if(editid == 0){
	               // parent.find('.disc_row').val(0);
	             }
                 parent.find('.disc_row').prop('readonly',false);
                 parent.find('.disc_row_type option').prop('disabled',false);
                 price_calculate(parent);
                 $('#membership_appilied').val(0);
                //  parent.find('.disc_row_type option[value="1"]').prop('selected', true);
            });
        }
	}
		
</script>											