<?php
	include "./includes/db_include.php";
	$branch_id = $_SESSION["branch_id"];
	
	$edit = query_by_id("SELECT * from `system` where active='0' and branch_id='".$branch_id."'",["id"=>$id],$conn)[0];
	
	
	if(isset($_POST['submit'])){
		$name       = addslashes(trim($_POST['name']));
		$user       = addslashes(trim($_POST['user']));
		$sp_id      = addslashes(trim($_POST['sp_id']));
		$role       = addslashes(trim($_POST['role']));
		$pass1      = $_POST['pass1'];
		$pass2      = $_POST['pass2'];
		if($pass1==$pass2){
			
			$pass1 = md5($pass1);
			$salt="ea7b7a7372bceab4a64b3c2d380c8a72";
			$pass = $salt.$pass1;
			$pass1 = md5(sha1(md5($pass)));
			
			
			query("INSERT INTO `user`(`username`,`name`,`sp_id`,`pass`,`role`,`active`,`branch_id`) VALUES ('$user','$name','$sp_id','$pass1','$role',0,'$branch_id')",[],$conn);
			
			$_SESSION['t']  = 1;
			$_SESSION['tmsg']  = "User Added Successfully";
			echo '<meta http-equiv="refresh" content="0; url=users.php" />';die();
			}else{
			$_SESSION['t']  = 2;
			$_SESSION['tmsg']  = "Password does not match";
			echo '<meta http-equiv="refresh" content="0; url=users.php" />';die();
		}
	}
	
	
	if(isset($_GET['d'])){
		
		$del = $_GET['d'];
		query("UPDATE `user` SET `active`=1 WHERE id='$del' and active='0' and branch_id='".$branch_id."'" ,[],$conn);
		$_SESSION['t']  = 1;
		$_SESSION['tmsg']  = "User Removed Successfully";
		echo '<meta http-equiv="refresh" content="0; url=users.php" />';die();
	}
	if(isset($_POST['edit-submit']))
	{
		
		$name       = addslashes(trim($_POST['name']));
		$user       = addslashes(trim($_POST['user']));
		$sp_id      = addslashes(trim($_POST['sp_id']));
		$role       = addslashes(trim($_POST['role']));
		$pass1      = $_POST['pass1'];
		$pass2      = $_POST['pass2'];
		if($pass1==$pass2){
			$pass1 = md5($pass1);
			$salt="ea7b7a7372bceab4a64b3c2d380c8a72";
			$pass = $salt.$pass1;
			$pass1 = md5(sha1(md5($pass)));
			query("UPDATE `user` set `name`='$name',`username`='$user',`sp_id`='$sp_id',`role`='$role',`pass`='$pass1',`active`='0' where id=:id and branch_id='".$branch_id."'",["id"=>$id],$conn);
			$_SESSION['t']  = 1;
			$_SESSION['tmsg']  = "User Updated Successfully";
			echo '<meta http-equiv="refresh" content="0; url=users.php" />';die();
			}else{
			$_SESSION['t']  = 2;
			$_SESSION['tmsg']  = "Password does not match";
			echo '<meta http-equiv="refresh" content="0; url=users.php" />';die();
		}
	}
	
	include "topbar.php";
	include "header.php";
	include "menu.php";
?>
<style>
	input {
    box-sizing: border-box;
    border: 1px solid #ccc;
    height: 30px;
    padding: 10px;
	}
	input.loading {
    background: url(http://www.xiconeditor.com/image/icons/loading.gif) no-repeat right center;
	}
	input.loading_done {
    background: url(upload/loading_done.gif) no-repeat right center;
	}
</style>
<!-- Dashboard wrapper starts -->
<div class="dashboard-wrapper">
	
	<!-- Main container starts -->
	<div class="main-container">
		<!-- Row starts -->
		<?php if(!isset($_SESSION['user_type'])){ ?>
		<div class="row gutter">
			
			<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
				<div class="panel">
					<div class="panel-heading">
						<h4>System setting</h4>
					</div>
					<div class="panel-body system-setting">
						<div class="widget-body"><br />
							<form class="form-horizontal no-margin" action="" method="post" enctype="multipart/form-data">
								<div class="form-group">
									<label for="userName" class="col-sm-2 control-label">Salon Name</label>
									<div class="col-sm-10">
										<input type="text" class="form-control" id="salon" placeholder="Name" value="<?=$edit['salon']?>" >
									</div>
								</div>
								<div class="form-group">
									<label for="userName" class="col-sm-2 control-label">Address</label>
									<div class="col-sm-10">
										<input type="text" class="form-control" id="address" placeholder="Address" value="<?=$edit['address']?>">
									</div>
								</div>
								<div class="form-group">
									<label for="userName" class="col-sm-2 control-label">Phone</label>
									<div class="col-sm-10">
										<input type="text" class="form-control" id="phone" placeholder="Phone" value="<?=$edit['phone']?>">
									</div>
								</div>
								<div class="form-group">
									<label for="userName" class="col-sm-2 control-label">Email</label>
									<div class="col-sm-10">
										<input type="email" class="form-control" id="email" placeholder="Email" value="<?=$edit['email']?>">
									</div>
								</div>
								<div class="form-group">
									<label for="userName" class="col-sm-2 control-label">Website</label>
									<div class="col-sm-10">
										<input type="text" class="form-control" id="website" placeholder="Website" value="<?=$edit['website']?>">
									</div>
								</div>
								<div class="form-group">
									<label for="userName" class="col-sm-2 control-label">GST</label>
									<div class="col-sm-10">
										<input type="text" class="form-control" id="gst" placeholder="GST" value="<?=$edit['gst']?>">
									</div>
								</div>

								<div class="form-group">
									<label for="hours" class="col-sm-2 control-label">Working Hours</label>
									<div class="col-sm-2">
										<input type="text" class="form-control maintime" id="shpstarttime" placeholder="GST" name="shpstarttime" value="<?=$edit['shpstarttime']!='00:00:00'?date('h:i A',strtotime($edit['shpstarttime'])):''?>" readonly>
									</div>
									<div class="col-sm-2">
										<input type="text" class="form-control maintime" id="shpendtime" placeholder="GST" name="shpendtime" value="<?=$edit['shpendtime']!='00:00:00'?date('h:i A',strtotime($edit['shpendtime'])):''?>" readonly>
									</div>
								</div>
								
								<div class="form-group">
									<label for="userName" class="col-sm-2 control-label">Update Logo</label>
									<div class="col-sm-10">
										<input type="file" class="form-control" id="logo" placeholder="GST" name="logo" accept="image/*"><br>
										<img id="logo_showing" src="<?=$edit['logo']?>" alt="your image" class="img-responsive img-thumbnail" />
										<span id="invalidImage" style="color:red;"></span>
									</div>
								</div>
								<div class="form-group">
									<label for="userName" class="col-sm-2 control-label">Login page background</label>
									<div class="col-sm-10">
										<input type="file" class="form-control" id="loginbg" placeholder="GST" name="loginbg" accept="image/*"><br>
										<?php if($edit['loginbg']){ ?>
										<img id="logo_showing2" src="<?=$edit['loginbg']?>" alt="Login page background image" class="img-responsive img-thumbnail"/>
									<?php } ?>
										<span id="invalidImage2" style="color:red;"></span>
									</div>
								</div>
								
							</form>
						</div>
					</div>
				</div>
			</div>
			<div class="clearfix"></div>
			<div class="col-lg-9 col-md-9 col-sm-9 col-xs-12">
				<div class="panel">
					<div class="panel-heading">
						<h4>Working days &amp; hours</h4>
					</div>
					<?php
						$result = query_by_id("SELECT * from `working_days_time` WHERE branch_id='".$branch_id."'",[],$conn);
					?>
					<div class="panel-body">
						<div class="widget-body">
							<form class="form-horizontal no-margin working-days" id="working-days" action="" method="post" enctype="multipart/form-data"><br />
								<div class="form-group">
									<?php 
									if((isset($edit['shpstarttime']) && $edit['shpstarttime']!='00:00:00') || (isset($edit['shpendtime']) && $edit['shpendtime']!='00:00:00')){
									?>	
									<div class="col-sm-3 col-xs-4 hidden-xs"></div>
								<?php } ?>
									<div class="
										<?php if((isset($edit['shpstarttime']) && $edit['shpstarttime']!='00:00:00') && (isset($edit['shpendtime']) && $edit['shpendtime']!='00:00:00')){ echo 'col-sm-9 col-xs-8'; } else { echo 'col-sm-12 col-xs-12'; } ?>
									">
										<?php
											if((isset($edit['shpstarttime']) && $edit['shpstarttime']=='00:00:00') || (isset($edit['shpendtime']) && $edit['shpendtime']=='00:00:00')){
												?>
												<div class="alert alert-warning">Please add working hours in system setting tab</div>
												<?php
											} else {
												?>
												<div class="alert alert-warning">
													Opening time and Closing time should be between <b id="st"></b> to <b id="et"></b>
												</div>
												<script type="text/javascript">
													$(document).ready(function(){
														$('#st').text(onTimeChange('<?= $edit['shpstarttime'] ?>'));
														$('#et').text(onTimeChange('<?= $edit['shpendtime'] ?>'));
													});
												</script>
												<?php
											}
										?>
									</div>
								</div>
								<?php 
								if((isset($edit['shpstarttime']) && $edit['shpstarttime']!='00:00:00')&& (isset($edit['shpendtime']) && $edit['shpendtime']!='00:00:00')){
								?>								
								<div class="form-group">
									<div class="col-sm-3 col-xs-4 hidden-xs"></div>
									<div class="col-sm-3 col-xs-3">
										<p><strong><i class="fa fa-calendar-check-o text-warning" style="margin-left:0px;" aria-hidden="true"></i> Days</strong></p>
									</div>
									<div class="col-sm-3 col-xs-4">
										<p><strong><i class="fa fa-clock-o text-warning" style="margin-left:0px;" aria-hidden="true"></i> Opening time</strong></p>
									</div>
									<div class="col-sm-3 col-xs-4">
										<p><strong><i class="fa fa-clock-o text-warning" style="margin-left: 0px;" aria-hidden="true"></i> Closing time</strong></p>
									</div>
								</div>
								<!-- Monday -->
								<div class="form-group">
									<label for="working-hours" class="col-sm-3 control-label hidden-xs"><strong>Working day &amp; hours</strong></label>
									<div class="col-sm-3 col-xs-4">
										<label>
											<input type="checkbox" name="mon-status" id="mon-status" <?= $result[0]['working_status']==1?'checked':'' ?> /> <span><?= $result[0]['day_name'] ?></span>
										</label>
									</div>
									<div class="col-sm-3 col-xs-4">
										<input type="text" onchange="checkTime('mon-working-hour-from',this.value,'')" class="form-control time" id="mon-working-hour-from" placeholder="Working hours" name="mon-working-hour-from" value="<?= $result[0]['working_status']==1?$result[0]['open_time']!='00:00:00'?date('h:i A',strtotime($result[0]['open_time'])):'':'' ?>" readonly>
									</div>
									<div class="col-sm-3 col-xs-4">
										<input type="text" onchange="checkTime('mon-working-hour-to',this.value,'')" class="form-control time" id="mon-working-hour-to" placeholder="Working hours" name="mon-working-hour-to" value="<?= $result[0]['working_status']==1?$result[0]['close_time']!='00:00:00'?date('h:i A',strtotime($result[0]['close_time'])):'':'' ?>" readonly>
									</div>
								</div>
								<!-- Tuesday -->
								<div class="form-group">
									<label for="working-hours" class="col-sm-3 control-label hidden-xs"></label>
									<div class="col-sm-3 col-xs-4">
										<label>
											<input type="checkbox" name="tue-status" id="tue-status" <?= $result[1]['working_status']==1?'checked':'' ?>/> <span><?= $result[1]['day_name'] ?></span>
										</label>
									</div>
									<div class="col-sm-3 col-xs-4">
										<input type="text" onchange="checkTime('tue-working-hour-from',this.value,'')" class="form-control time" id="tue-working-hour-from" placeholder="Working hours" name="tue-working-hour-from" value="<?= $result[1]['working_status']==1?$result[1]['open_time']!='00:00:00'?date('h:i A',strtotime($result[1]['open_time'])):'':'' ?>" readonly>
									</div>
									<div class="col-sm-3 col-xs-4">
										<input type="text" onchange="checkTime('tue-working-hour-to',this.value,'')" class="form-control time" id="tue-working-hour-to" placeholder="Working hours" name="tue-working-hour-to" value="<?= $result[1]['working_status']==1?$result[1]['close_time']!='00:00:00'?date('h:i A',strtotime($result[1]['close_time'])):'':'' ?>" readonly>
									</div>
								</div>
								<!-- wednesday -->
								<div class="form-group">
									<label for="working-hours" class="col-sm-3 control-label hidden-xs"></label>
									<div class="col-sm-3 col-xs-4">
										<label>
											<input type="checkbox" name="wed-status" id="wed-status" <?= $result[2]['working_status']==1?'checked':'' ?> /> <span><?= $result[2]['day_name'] ?></span>
										</label>
									</div>
									<div class="col-sm-3 col-xs-4">
										<input type="text" onchange="checkTime('wed-working-hour-from',this.value,'')" class="form-control time" id="wed-working-hour-from" placeholder="Working hours" name="wed-working-hour-from" value="<?= $result[2]['working_status']==1?$result[2]['open_time']!='00:00:00'?date('h:i A',strtotime($result[2]['open_time'])):'':'' ?>" readonly>
									</div>
									<div class="col-sm-3 col-xs-4">
										<input type="textx" onchange="checkTime('wed-working-hour-to',this.value,'')" class="form-control time" id="wed-working-hour-to" placeholder="Working hours" name="wed-working-hour-to" value="<?= $result[2]['working_status']==1?$result[2]['close_time']!='00:00:00'?date('h:i A',strtotime($result[2]['close_time'])):'':'' ?>" readonly>
									</div>
								</div>
								<!-- thursday -->
								<div class="form-group">
									<label for="working-hours" class="col-sm-3 control-label hidden-xs"></label>
									<div class="col-sm-3 col-xs-4">
										<label>
											<input type="checkbox" name="thu-status" id="thu-status" <?= $result[3]['working_status']==1?'checked':'' ?>/> <span><?= $result[3]['day_name'] ?></span>
										</label>
									</div>
									<div class="col-sm-3 col-xs-4">
										<input type="text" onchange="checkTime('thu-working-hour-from',this.value,'')" class="form-control time" id="thu-working-hour-from" placeholder="Working hours" name="thu-working-hour-from" value="<?= $result[3]['working_status']==1?$result[3]['open_time']!='00:00:00'?date('h:i A',strtotime($result[3]['open_time'])):'':'' ?>" readonly>
									</div>
									<div class="col-sm-3 col-xs-4">
										<input type="textx" onchange="checkTime('thu-working-hour-to',this.value,'')" class="form-control time" id="thu-working-hour-to" placeholder="Working hours" name="thu-working-hour-to" value="<?= $result[3]['working_status']==1?$result[3]['close_time']!='00:00:00'?date('h:i A',strtotime($result[3]['close_time'])):'':'' ?>" readonly>
									</div>
								</div>
								<!-- friday -->
								<div class="form-group">
									<label for="working-hours" class="col-sm-3 control-label hidden-xs"></label>
									<div class="col-sm-3 col-xs-4">
										<label>
											<input type="checkbox" name="fri-status" id="fri-status" <?= $result[4]['working_status']==1?'checked':'' ?> /> <span><?= $result[4]['day_name'] ?></span>
										</label>
									</div>
									<div class="col-sm-3 col-xs-4">
										<input type="text" onchange="checkTime('fri-working-hour-from',this.value,'')" class="form-control time" id="fri-working-hour-from" placeholder="Working hours" name="fri-working-hour-from" value="<?= $result[4]['working_status']==1?$result[4]['open_time']!='00:00:00'?date('h:i A',strtotime($result[4]['open_time'])):'':'' ?>" readonly>
									</div>
									<div class="col-sm-3 col-xs-4">
										<input type="text" onchange="checkTime('fri-working-hour-to',this.value,'')" class="form-control time" id="fri-working-hour-to" placeholder="Working hours" name="fri-working-hour-to" value="<?= $result[4]['working_status']==1?$result[4]['close_time']!='00:00:00'?date('h:i A',strtotime($result[4]['close_time'])):'':'' ?>" readonly>
									</div>
								</div>
								<!-- Saturday -->
								<div class="form-group">
									<label for="working-hours" class="col-sm-3 control-label hidden-xs"></label>
									<div class="col-sm-3 col-xs-4">
										<label>
											<input type="checkbox" name="sat-status" id="sat-status" <?= $result[5]['working_status']==1?'checked':'' ?> /> <span><?= $result[5]['day_name'] ?></span>
										</label>
									</div>
									<div class="col-sm-3 col-xs-4">
										<input type="text" onchange="checkTime('sat-working-hour-from',this.value,'')" class="form-control time" id="sat-working-hour-from" placeholder="Working hours" name="sat-working-hour-from" value="<?= $result[5]['working_status']==1?$result[5]['open_time']!='00:00:00'?date('h:i A',strtotime($result[5]['open_time'])):'':'' ?>" readonly>
									</div>
									<div class="col-sm-3 col-xs-4">
										<input type="text" onchange="checkTime('sat-working-hour-to',this.value,'')" class="form-control time" id="sat-working-hour-to" placeholder="Working hours" name="sat-working-hour-to" value="<?= $result[5]['working_status']==1?$result[5]['close_time']!='00:00:00'?date('h:i A',strtotime($result[5]['close_time'])):'':'' ?>" readonly>
									</div>
								</div>
								<!-- sunday -->
								<div class="form-group">
									<label for="working-hours" class="col-sm-3 control-label hidden-xs"></label>
									<div class="col-sm-3 col-xs-4">
										<label>
											<input type="checkbox" name="sun-status" id="sun-status" <?= $result[6]['working_status']==1?'checked':'' ?> /> <span><?= $result[6]['day_name'] ?></span>
										</label>
									</div>
									<div class="col-sm-3 col-xs-4">
										<input type="text" onchange="checkTime('sun-working-hour-from',this.value,'')" class="form-control time" id="sun-working-hour-from" placeholder="Working hours" name="sun-working-hour-from" value="<?= $result[6]['working_status']==1?$result[6]['open_time']!='00:00:00'?date('h:i A',strtotime($result[6]['open_time'])):'':'' ?>" readonly>
									</div>
									<div class="col-sm-3 col-xs-4">
										<input type="text" onchange="checkTime('sun-working-hour-to',this.value,'')" class="form-control time" id="sun-working-hour-to" placeholder="Working hours" name="sun-working-hour-to" value="<?= $result[6]['working_status']==1?$result[6]['close_time']!='00:00:00'?date('h:i A',strtotime($result[6]['close_time'])):'':'' ?>" readonly>
									</div>
								</div>
								<!-- submit button -->
								<div class="form-group">
									<div class="col-sm-12">
										<input type="hidden" id="day1id" value="<?= $result[0]['id'] ?>">
										<input type="hidden" id="day2id" value="<?= $result[1]['id'] ?>">
										<input type="hidden" id="day3id" value="<?= $result[2]['id'] ?>">
										<input type="hidden" id="day4id" value="<?= $result[3]['id'] ?>">
										<input type="hidden" id="day5id" value="<?= $result[4]['id'] ?>">
										<input type="hidden" id="day6id" value="<?= $result[5]['id'] ?>">
										<input type="hidden" id="day7id" value="<?= $result[6]['id'] ?>">
										<button type="button" name="workday" onclick="saveWorkingday()" class="btn btn-success pull-right"><i class="fa fa-floppy-o" aria-hidden="true"></i>Save</button>
									</div>
								</div>
							<?php } ?>
							</form>
						</div>
					</div>
				</div>
			</div>
			<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
				<div class="panel">
					<div class="panel-heading">
						<h4>Extra hours</h4>
					</div><br />
					<div class="panel-body">
						<div class="widget-body">
							<div class="col-sm-12">
								<div class="form-group working-days">
									<label>
										<input type="radio" value="1" class="reminders" onclick="extraHours(this.value)" name="extra_hours" <?= $edit['extra_hours'] == '1'?'checked':''?> /> <span>Yes</span>
									</label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
									<label>
										<input type="radio" value="0" class="reminders" onclick="extraHours(this.value)" name="extra_hours" <?= $edit['extra_hours'] == '0'?'checked':''?> /> <span>No</span>
									</label>
								</div>
							</div>
						</div>
					</div>
				</div>

				<?php 
					if((isset($edit['shpstarttime']) && $edit['shpstarttime']!='00:00:00')&& (isset($edit['shpendtime']) && $edit['shpendtime']!='00:00:00')){
				?>	
				<div class="panel">
					<div class="panel-heading">
						<h4>Day end report</h4>
					</div><br />
					<?php
						$de_report = query_by_id("SELECT * from `day_end_report` where status='1' and branch_id='".$branch_id."'",[],$conn)[0];
					?>
					<div class="panel-body">
						<div class="widget-body">
							<form id="report-time" class="working-days">
								<div class="form-group">
									<div class="col-sm-5">
										<label><span class="mr-left-0">Report time <b class="text-danger">*</b></span></label>
									</div>
									<div class="col-sm-7">
										<input type="text" onchange="checkTime('endreporttime',this.value,'')" name="" value="<?= $de_report?$de_report['report_time']!='00:00:00'?date('h:i A',strtotime($de_report['report_time'])):'':''?>" id="endreporttime" class="form-control time" readonly>
									</div>
									<div class="clearfix"></div>
									<div class="col-sm-5">
										<label><span class="mr-left-0">Send in <b class="text-danger">*</b></span></label>
									</div>
									<div class="col-sm-7">
										<label>
											<input type="checkbox" class="reminders" name="" id="reportsms" <?= $de_report['sms_status'] == '1'?'checked':''?> /> <span>SMS</span>
										</label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
										<label>
											<input type="checkbox" class="reminders" onclick="" name="" id="reportemail" <?= $de_report['email_status'] == '1'?'checked':''?> /> <span>Email</span>
										</label>
									</div>
									<div class="clearfix"></div>
									<div class="col-sm-12">
										<input type="hidden" name="" value="<?= $de_report != ''?$de_report['id']: ''?>" id="dayreportid">
										<button style="margin-top:15px;" type="button" onclick="dayEndReport()" class="btn btn-success pull-right"><i class="fa fa-floppy-o" aria-hidden="true"></i>Save</button>
									</div>
								</div>
							</form>
						</div>
					</div>
				</div>
				<?php } ?>
			</div>
			<div class="clearfix"></div>
			<div class="col-lg-9 col-md-9 col-sm-9 col-xs-12">
				<div class="panel">
					<div class="panel-heading">
						<h4>SMS templates settings</h4>
						<?php $api = query_by_id("SELECT * from `sms` where id='1'",[],$conn)[0]; ?>
					</div>
					<div class="panel-body">
						<div class="widget-body"><br />
							<div class="">
								<form class="form-horizontal no-margin" action="" method="post" enctype="multipart/form-data">
									<div class="form-group">
										<label for="templatefor" class="col-sm-2 control-label">Template for <span class="text-danger">*</span></label>
										<?php $templates = query_by_id("SELECT * from `sms_templates_".$branch_id."` where status = '1' and branch_id='".$branch_id."'",[],$conn); ?>
										<div class="col-sm-10">
											<select class="form-control" name="templatefor" id="templatefor">
												<option value="">--select--</option>
												<?php foreach($templates as $template){ ?>
													<option value="<?= $template['id'] ?>"><?= $template['template_name'] ?></option>
												<?php } ?>
											</select>
										</div>
									</div>
									<div class="form-group">
										<label for="smstemplate" class="col-sm-2 control-label">Message <span class="text-danger">*</span></label>
										<div class="col-sm-10">
											<textarea style="resize: none;" class="form-control" rows="8" name="smstemplate" id="smstemplate" placeholder="" value="" ></textarea>
											<p><span class="pull-right"><span id="text_lenght">0</span>/<?= $api['text_limit'] ?>  </span><strong><span style="cursor: pointer;" id="clientname" onclick="addTofield('smstemplate','{name}','clientname')">{name}</span>&nbsp;<span style="cursor: pointer;" id="salonname" onclick="addTofield('smstemplate','{salon_name}','salonname')">{salon_name}</span><strong></p>
										</div>
									</div>
									<div class="form-group">
										<div class="col-sm-12">
											<input type="hidden" name="template_id" id="template_id" value="">
											<button type="button" onclick="savesmsTemplate()" class="btn btn-success pull-right"><i class="fa fa-floppy-o" aria-hidden="true"></i>Save</button>
										</div>
									</div>
								</form>
							</div>
						</div>
					</div>
				</div>
			</div>
			
		</div>
		<?php } ?>
	<!-- Row ends -->
	<div class="row gutter">
	<?php
		if(isset($_SESSION['user_type']) && $_SESSION['user_type'] == 'superadmin'){
			?>
				<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
					<div class="panel">
						<div class="panel-heading">
							<h4>Automatic reminders</h4>
						</div>
						<?php
							$reminder = query_by_id("SELECT * from `automatic_reminders` where status='1'",[],$conn);
						?>
						<div class="panel-body">
							<div class="widget-body">
								<form id="autometic-reminder" class="working-days">
									<div class="col-sm-12">
										<p><strong><i class="fa fa-check-square-o text-warning" style="margin-left:0px;" aria-hidden="true"></i> Enable</strong></p>
									</div>
									<?php foreach ($reminder as $reminder) { ?>
									<div class="form-group">
										<div class="col-sm-12">
											<label>
												<input type="checkbox" class="reminders" onclick="reminders(<?= $reminder['id'] ?>)" name="reminder[]" id="rem-<?= $reminder['id'] ?>" <?= $reminder['reminder_status']==1?'checked':'' ?> /> <span><?= $reminder['reminder_for'] ?></span>
											</label>
										</div>
									</div>
								<?php } ?>
								</form>
							</div>
						</div>
					</div>
				</div>
				<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
					<div class="panel">
						<div class="panel-heading">
							<h4>Redeem points setting</h4>
						</div><br />
						<?php
							$redeem_point = query_by_id("SELECT * from `redeem_point_setting` where status='1' and id='1'",[],$conn)[0];
						?>
						<div class="panel-body">
							<div class="widget-body">
								<form id="redeem-point" class="working-days">
									<div class="form-group">
										<div class="col-sm-5">
											<label><span class="mr-left-0">Redeem points <b class="text-danger">*</b></span></label>
										</div>
										<div class="col-sm-7">
											<input type="number" name="" id="redeempoints" class="form-control" value="<?= $redeem_point['redeem_point']; ?>">
										</div>
										<div class="clearfix"></div>
									</div>
									<div class="form-group">
										<div class="col-sm-5">
											<label><span class="mr-left-0">Price <b class="text-danger">*</b></span></label>
										</div>
										<div class="col-sm-7">
											<input type="number" name="" value="<?= $redeem_point['price']; ?>" id="redeempointsprice" class="form-control" >
										</div>
										<div class="clearfix"></div>
									</div>
									<div class="form-group">
										<div class="col-sm-5">
											<label><span class="mr-left-0">Max redeem points <b class="text-danger">*</b></span></label>
										</div>
										<div class="col-sm-7">
											<input type="number" name="" value="<?= $redeem_point['max_redeem_point']; ?>" id="maxredeempoints" class="form-control">
										</div>
									</div>
									<div class="form-group">
										<div class="col-sm-12">
											<input type="hidden" name="" value="<?= $redeem_point != ''?$redeem_point['id']: ''?>" id="redeem_point_id">
											<button style="margin-top:15px;" type="button" onclick="redeemSetting()" class="btn btn-success pull-right"><i class="fa fa-floppy-o" aria-hidden="true"></i>Save</button>
										</div>
									</div>
								</form>
							</div>
						</div>
					</div>
				</div>
				<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
					<div class="panel">
						<div class="panel-heading">
							<h4>API settings</h4>
						</div>
						<?php $api = query_by_id("SELECT * from `sms` where id='1'",[],$conn)[0]; ?>
						<div class="panel-body">
							<div class="widget-body"><br />
								<div class="col-sm-12">
									<form class="form-horizontal no-margin" action="" method="post" enctype="multipart/form-data">
										<div class="form-group">
											<label for="apiurl" class="col-sm-3 control-label">API url <span class="text-danger">*</span></label>
											<div class="col-sm-9">
												<input type="url" class="form-control" name="apiurl" id="apiurl" placeholder="url" value="<?= $api['api_url'] ?>">
											</div>
										</div>
										<div class="form-group">
											<label for="apiusername" class="col-sm-3 control-label">API username <span class="text-danger">*</span></label>
											<div class="col-sm-9">
												<input type="text" class="form-control" name="apiusername" id="apiusername" placeholder="username" value="<?= $api['api_username'] ?>" >
											</div>
										</div>
										<div class="form-group">
											<label for="apipassword" class="col-sm-3 control-label">API password <span class="text-danger">*</span></label>
											<div class="col-sm-9">
												<input type="password" class="form-control" name="apipassword" id="apipassword" placeholder="password" value="<?= $api['api_password'] ?>">
												<i class="fa fa-eye showpassword text-warning" id="showpassword" aria-hidden="true"></i>
											</div>
										</div>
										<div class="form-group">
											<label for="senderid" class="col-sm-3 control-label">Sender id <span class="text-danger">*</span></label>
											<div class="col-sm-9">
												<input type="text" class="form-control" id="senderid" name="senderid" placeholder="sender id" value="<?= $api['sender_id'] ?>">
											</div>
										</div>
										<div class="form-group">
											<div class="col-sm-12">
												<button type="button" onclick="saveApi()" class="btn btn-success pull-right"><i class="fa fa-floppy-o" aria-hidden="true"></i>Save</button>
											</div>
										</div>
									</form>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="clearfix"></div>
			<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
				<div class="panel">
					<div class="panel-heading">
						<h4>List of holidays</h4>
					</div>
					<div class="panel-body">
						<div class="widget-body">
							<div class="table-responsive"><br />
								<table id="myTable" class="table table-bordered adv-holiday-comm-table">
									<thead>
										<tr>
											<th style="width:3%"></th>
											<th width="250">Date</th>
											<th>Holiday description</th>
										</tr>
									</thead>
									<tbody>
										<?php $query = query_by_id("SELECT * from holidays_list where status=1 ",[],$conn); 
										if($query){
											$count = 1;
											foreach ($query as $key=>$row) {
												if($count == 1){ 
										?>

										<tr id="TextBoxContainer" class="TextBoxContainer holiday_row_1" data-row="holiday_row_1">
											<td class="sno" style="vertical-align: middle;">
												<span class="icon-dots-three-vertical"></span>
											</td>
											<td>
												<input type="text" class="form-control sal urdate holidaydate" name="holidaydate[]" value="<?= $row['date'] ?>" required readonly>
											</td>
											<td>
												<input type="text" placeholder="Description" class="form-control sal holidaydescription" name="holidaydescription[]" value="<?= $row['description'] ?>" 	required>
											</td>
											<input type="hidden" name="table_id" id="table_id" value="<?= $row['id'] ?>">
										</tr>
										<?php } else { ?>
											<tr id="TextBoxContainer" class="TextBoxContainer holiday_row_<?= $count ?>" data-row="holiday_row_<?= $count ?>">
											<td class="sno" style="vertical-align: middle;">
												<span class="remm icon-trash2 " style="color:red;" onclick="$(this).parent().parent().remove();"></span>
											</td>
											<td>
												<input type="text" class="form-control sal urdate holidaydate"  name="holidaydate[]" value="<?= $row['date'] ?>" required readonly>
											</td>
											<td>
												<input type="text" placeholder="Description" class="form-control sal holidaydescription" name="holidaydescription[]" value="<?= $row['description'] ?>" 	required>
											</td>
											<input type="hidden" name="table_id" id="table_id" value="<?= $row['id'] ?>">
										</tr>
										<?php }
											$count += 1;
										}
									} else { ?>
										<tr id="TextBoxContainer" class="TextBoxContainer holiday_row_1" data-row="holiday_row_1">
											<td class="sno" style="vertical-align: middle;">
												<span class="icon-dots-three-vertical"></span>
											</td>
											<td>
												<input type="text" class="form-control sal urdate holidaydate"  name="holidaydate[]" value="" required readonly>
											</td>
											<td>
												<input type="text" placeholder="Description" class="form-control sal holidaydescription" id="" name="holidaydescription[]" value="" 	required>
											</td>
											<input type="hidden" name="table_id" id="table_id" value="">
										</tr>
									<?php } ?>
										<tr id="addBefore">
											<td colspan="5">
												<button type="button" id="btnAdd" class="btn btn-warning btn-xs pull-right"><i class="fa fa-plus mr-right-0" aria-hidden="true"></i></button>
											</td>					
										</tr>
									</tbody>
								</table>
								<button class="btn btn-success pull-right" onclick="addHolidaylist()"><i class="fa fa-floppy-o" aria-hidden="true"></i>Save</button>
							</div>
						</div>
					</div>
				</div>
			</div>
			<?php
		}
	?>
	</div>
	
</div>
<!-- Main container ends -->

</div>
<!-- Dashboard Wrapper End -->

</div>
<?php include "footer.php"; ?>	

<script>
	$(function(){
		$('.system-setting input').css("background-color","#eaeaea"); 
		focus(); 
		function focus(){	 
			$('.system-setting input').on('focus',function(){
				$(this).css("background-color","#ffffff");
				$(this).removeClass('loading_done');
			});	
		}
		$('.system-setting input[type="file"]').change(function(e){
			$('.system-setting input').css("background-color","#eaeaea");
			// if($(this).val() !=''){
			let $t = $(e.currentTarget);
			let value=$(this).val();
			var id = $(this).attr('id');
			value = value.substring(value.lastIndexOf("\\") + 1, value.length);
			$t.addClass('loading');
			$('.system-setting input').not(this).attr('readonly',true);
			if(id == 'logo'){
				var url = 'ajax/system_details.php?column_name=logo&value='+value;
				var invalidimgdiv = $('#invalidImage');
				var logo_showing = $('#logo_showing');
			} else if(id == 'loginbg'){
				var url = 'ajax/system_details.php?column_name=loginbg&value='+value;
				var invalidimgdiv = $('#invalidImage2');
				var logo_showing = $('#logo_showing2');
			}
			var file_data = $(this).prop('files')[0];   
			var form_data = new FormData();                  
			form_data.append('file', file_data);
			$.ajax({
				url: url,
				dataType: 'text',  // what to expect back from the PHP script, if anything
				type: 'POST',
                cache: false,
                contentType: false,
                processData: false,
                data: form_data,
				success: function(data) {
				    if(data =='2'){
						$t.removeClass('loading');
						$t.addClass('loading_done');
						$('.system-setting input').attr('readonly',false);
						logo_showing.show();
						invalidimgdiv.hide();
						location.reload();
				    } else if( data == '0'){
				    	$t.removeClass('loading');
						$t.addClass('loading_done');
				        logo_showing.hide();
				        invalidimgdiv.show().text("ALLOWED IMAGE FORMAT ARE jpg, png, jpeg, gif");
				    } else if( data == '1'){
				    	$t.removeClass('loading');
						$t.addClass('loading_done');
				        logo_showing.hide();
				        invalidimgdiv.show().text("Filesize should be less then 2MB");
				    }
				}
			});
			// }
			// readURL(this);
		});
	});
	function readURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                $('#logo_showing').attr('src', e.target.result);
			}
            reader.readAsDataURL(input.files[0]);
		}
	}
    
    $("#imgInp").change(function(){
        readURL(this);
	});
	
	$(".system-setting input:not(:file)").on('change', function(e) {
		$('.system-setting input').css("background-color","#eaeaea");
		// if($(this).val() !=''){
		var $t = $(e.currentTarget);
		var columnName = $(this).attr('id');
		var value =$(this).val();
		$t.addClass('loading');
		$('.system-setting input').not(this).attr('readonly',true);
		$.ajax({
			url: 'ajax/system_details.php',
			type: 'POST',
			data: {column_name:columnName,value:value},
			success: function(data) {
			    if(data =='2'){
					$t.removeClass('loading');
					$t.addClass('loading_done');
					$('.system-setting input').attr('readonly',false);
					location.reload();
					//$('#logo_showing').show();
					//$('#invalidImage').hide();
			    }else{
			         //$('#logo_showing').hide();
			         //$('#invalidImage').show().text("ALLOWED IMAGE FORMAT ARE jpg, png, jpeg, gif");
			    }
			}
		});
		// }
		
	});

	// function to save working days & hours

	function saveWorkingday(){
		var day1_status, day2_status, day3_status, day4_status, day5_status, day6_status, day7_status;
		if($('#mon-status').is(":checked")){ day1_status = 1; } else { day1_status = 0 }
		if($('#tue-status').is(":checked")){ day2_status = 1; } else { day2_status = 0 }
		if($('#wed-status').is(":checked")){ day3_status = 1; } else { day3_status = 0 }
		if($('#thu-status').is(":checked")){ day4_status = 1; } else { day4_status = 0 }
		if($('#fri-status').is(":checked")){ day5_status = 1; } else { day5_status = 0 }
		if($('#sat-status').is(":checked")){ day6_status = 1; } else { day6_status = 0 }
		if($('#sun-status').is(":checked")){ day7_status = 1; } else { day7_status = 0 }
		// open time for all days
		var day1_work_from = $('#mon-working-hour-from').val();	
		var day2_work_from = $('#tue-working-hour-from').val();	
		var day3_work_from = $('#wed-working-hour-from').val();	
		var day4_work_from = $('#thu-working-hour-from').val();	
		var day5_work_from = $('#fri-working-hour-from').val();	
		var day6_work_from = $('#sat-working-hour-from').val();	
		var day7_work_from = $('#sun-working-hour-from').val();

		// close time for all days
		var day1_work_to = $('#mon-working-hour-to').val();	
		var day2_work_to = $('#tue-working-hour-to').val();	
		var day3_work_to = $('#wed-working-hour-to').val();	
		var day4_work_to = $('#thu-working-hour-to').val();	
		var day5_work_to = $('#fri-working-hour-to').val();	
		var day6_work_to = $('#sat-working-hour-to').val();	
		var day7_work_to = $('#sun-working-hour-to').val();

		// day id
		var day1id = $('#day1id').val();	
		var day2id = $('#day2id').val();	
		var day3id = $('#day3id').val();	
		var day4id = $('#day4id').val();	
		var day5id = $('#day5id').val();	
		var day6id = $('#day6id').val();	
		var day7id = $('#day7id').val();	
		var valid = [];
		if(day1_status == 1){
			if(day1_work_from == '' || day1_work_to == ''){
				$('#mon-working-hour-from').addClass('invalid');
				$('#mon-working-hour-to').addClass('invalid');
				valid.push('day1invalid');
			} else {
				valid = jQuery.grep(valid, function(value) {
				  return value != 'day1invalid';
				});
			}
		} else {
			$('#mon-working-hour-from').removeClass('invalid');
			$('#mon-working-hour-to').removeClass('invalid');
		}
		if(day2_status == 1){
			if(day2_work_from == '' || day2_work_to == ''){
				$('#tue-working-hour-from').addClass('invalid');
				$('#tue-working-hour-to').addClass('invalid');
				valid.push('day2invalid');
			} else {
				valid = jQuery.grep(valid, function(value) {
				  return value != 'day2invalid';
				});
			}
		} else {
			$('#tue-working-hour-from').removeClass('invalid');
			$('#tue-working-hour-to').removeClass('invalid');
		}
		if(day3_status == 1){
			if(day3_work_from == '' || day3_work_to == ''){
				$('#wed-working-hour-from').addClass('invalid');
				$('#wed-working-hour-to').addClass('invalid');
				valid.push('day3invalid');
			} else {
				valid = jQuery.grep(valid, function(value) {
				  return value != 'day3invalid';
				});
			}
		} else {
			$('#wed-working-hour-from').removeClass('invalid');
			$('#wed-working-hour-to').removeClass('invalid');
		}
		if(day4_status == 1){
			if(day4_work_from == '' || day4_work_to == ''){
				$('#thu-working-hour-from').addClass('invalid');
				$('#thu-working-hour-to').addClass('invalid');
				valid.push('day4invalid');
			} else {
				valid = jQuery.grep(valid, function(value) {
				  return value != 'day4invalid';
				});
			}
		} else {
			$('#thu-working-hour-from').removeClass('invalid');
			$('#thu-working-hour-to').removeClass('invalid');
		}
		if(day5_status == 1){
			if(day5_work_from == '' || day5_work_to == ''){
				$('#fri-working-hour-from').addClass('invalid');
				$('#fri-working-hour-to').addClass('invalid');
				valid.push('day5invalid');
			} else {
				valid = jQuery.grep(valid, function(value) {
				  return value != 'day5invalid';
				});
			}
		} else {
			$('#fri-working-hour-from').removeClass('invalid');
			$('#fri-working-hour-to').removeClass('invalid');
		}
		if(day6_status == 1){
			if(day6_work_from == '' || day6_work_to == ''){
				$('#sat-working-hour-from').addClass('invalid');
				$('#sat-working-hour-to').addClass('invalid');
				valid.push('day6invalid');
			} else {
				valid = jQuery.grep(valid, function(value) {
				  return value != 'day6invalid';
				});
			}
		} else {
			$('#sat-working-hour-from').removeClass('invalid');
			$('#sat-working-hour-to').removeClass('invalid');
		}
		if(day7_status == 1){
			if(day7_work_from == '' || day7_work_to == ''){
				$('#sun-working-hour-from').addClass('invalid');
				$('#sun-working-hour-to').addClass('invalid');
				valid.push('day7invalid');
			} else {
				valid = jQuery.grep(valid, function(value) {
				  return value != 'day7invalid';
				});
			}
		} else {
			$('#sun-working-hour-from').removeClass('invalid');
			$('#sun-working-hour-to').removeClass('invalid');
		}

		if(valid.length === 0){
			var data = {day1id:day1id, day1_status:day1_status, day1_work_from : day1_work_from, day1_work_to : day1_work_to, day2id:day2id, day2_status:day2_status, day2_work_from : day2_work_from, day2_work_to : day2_work_to, day3id:day3id, day3_status:day3_status, day3_work_from : day3_work_from, day3_work_to : day3_work_to, day4id:day4id, day4_status:day4_status, day4_work_from : day4_work_from, day4_work_to : day4_work_to, day5id:day5id, day5_status:day5_status, day5_work_from : day5_work_from, day5_work_to : day5_work_to, day6id:day6id, day6_status:day6_status, day6_work_from : day6_work_from, day6_work_to : day6_work_to, day7id:day7id, day7_status:day7_status, day7_work_from : day7_work_from, day7_work_to : day7_work_to , action : 'workingdays'}

			$.ajax({
				url: 'ajax/system_details.php',
				type: 'POST',
				data: data,
				success: function(data) {
				  	location.reload();
				}
			});
		}
	}

	// function for holiday row
	<?php if(isset($count)) { ?>
		var holiday_row = <?= $count-1 ?>;
	<?php } else { ?>
		var holiday_row = 1;
	<?php } ?>

	// add holidays rows

	$("#btnAdd").bind("click", function() {
		holiday_row += 1;
		var clonetr = $("#TextBoxContainer").clone().addClass('TextBoxContainer holiday_row_'+holiday_row);
		clonetr.removeAttr('id');
		clonetr.removeClass('holiday_row_1');
		clonetr.attr('data-row','holiday_row_'+holiday_row);
		clonetr.find("table.add_row").remove();
		clonetr.find('.sno').html('<span class="remm icon-trash2 " style="color:red;" onclick="$(this).parent().parent().remove();"></span>');
		clonetr.find('input').val('');
		// clonetr.find('.staff option[value=""]').prop('selected',true);
		$("#addBefore").before(clonetr);
		$('.holiday_row_'+holiday_row+' input').removeClass('invalid');
		dp();
	});

	// function to insert holiday list 
	function addHolidaylist(){
		var row_no;
		var comm_data = [];
		$('.adv-holiday-comm-table  tbody   tr.TextBoxContainer').each(function() {
			var row_no = $(this).attr('data-row');
			var holidaydate = $('.'+row_no+' .holidaydate').val();
			var table_id = $('.'+row_no+' #table_id').val();
			var holidaydescription = $('.'+row_no+' .holidaydescription').val();
			var row_data = [table_id, holidaydate, holidaydescription];
			if(holidaydate == '' || holidaydescription == ''){
				if(holidaydate == ''){
					$('.'+row_no+' .holidaydate').addClass('invalid');
				} else {
					$('.'+row_no+' .holidaydate').removeClass('invalid');
				}
				if(holidaydescription == ''){
					$('.'+row_no+' .holidaydescription').addClass('invalid');
				} else {
					$('.'+row_no+' .holidaydescription').removeClass('invalid');
				}			
			} else {
				$('.'+row_no+' .holidaydate').removeClass('invalid');
				$('.'+row_no+' .holidaydescription').removeClass('invalid');
				comm_data.push(row_data);
			}
		});

		if($('.adv-holiday-comm-table input').hasClass('invalid')){
			// alert('field error');
		} else {
			$.ajax({  
	            url:"ajax/system_details.php",
	            method:"POST",
	            data: JSON.stringify({data: comm_data, 'action':'addholidays'}),
	            contentType: false,
	            dataType: "json",
	            processData:false,
	            success:function(response){
	            	if(response.status == 1){
	               		location.reload();
	               	}
	            }
	       });
		}
		// console.log(comm_data);
	}

	// function to save api and template

	function saveApi(){
		var urlDiv = $('#apiurl');
		var usernameDiv = $('#apiusername');
		var passwordDiv = $('#apipassword');
		var senderidDiv = $('#senderid');
		var url = urlDiv.val();
		var username = usernameDiv.val();
		var password = passwordDiv.val();
		var sender_id = senderidDiv.val();
		if(url == '' || username == '' || password == '' || sender_id == ''){
			url == ''? urlDiv.addClass('invalid'): urlDiv.removeClass('invalid');
			username == ''? usernameDiv.addClass('invalid'): usernameDiv.removeClass('invalid');
			password == ''? passwordDiv.addClass('invalid'): passwordDiv.removeClass('invalid');
			sender_id == ''? senderidDiv.addClass('invalid'): senderidDiv.removeClass('invalid');
		} else {
			$.ajax({  
	            url:"ajax/system_details.php",
	            method:"POST",
	            data: {url : url, username : username, password : password, sender_id : sender_id, 'action':'addapisetting'},
	            success:function(response){
	            	if(response == '1'){
	               		location.reload();
	               	}
	            }
	       });
		}
	}

	$('.showpassword').click(function(){
		var id = $(this).attr('id');
		if(id == 'showpassword'){
			$(this).attr('id','hidepassword');
			$('#apipassword').attr('type','text');
			$(this).removeClass('fa-eye');
			$(this).addClass('fa-eye-slash');
		} else if(id == 'hidepassword'){
			$(this).attr('id','showpassword');
			$('#apipassword').attr('type','password');
			$(this).removeClass('fa-eye-slash');
			$(this).addClass('fa-eye');
		}
	});

	// function to check text lenght in textare and remove more the limit
	function textlength(textareaid, counterid,e){
		var textarea = $('#'+textareaid).val();
		var textlenght = textarea.length;
		var set = <?= $api['text_limit']!=''?$api['text_limit']:'160' ?>;
		var remain = parseInt(set - textlenght);
	    $('#'+counterid).text(textlenght);
	    if(e == undefined){
	    	if(parseInt(textlenght) > set){
				$('#'+textareaid).val((textarea).substring(0, textlenght - Math.abs(remain)));
			    $('#'+counterid).text(textlenght - Math.abs(remain));
			    return false;
			}
	    } else {
	    	if (remain <= 0 && e.which !== 0 && e.charCode === 0) {
		        $('#'+textareaid).val((textarea).substring(0, textlenght - Math.abs(remain)));
		        $('#'+counterid).text(textlenght - Math.abs(remain));
		        return false;
		    }
	    }
	}

	// function if shortcode is already used

	function chectShortCodeUsage(){
		if($('#smstemplate').val().indexOf('{name}') > -1){
			$('#clientname').css('color','#676767');
		} else {
			$('#clientname').css('color','#101010');
		}
		if($('#smstemplate').val().indexOf('{salon_name}') > -1){
			$('#salonname').css('color','#676767');
		} else {
			$('#salonname').css('color','#101010');
		}
	}

	// calling textlength() function on keyup for sms template field
	$('#smstemplate').keyup(function(e){
		textlength('smstemplate', 'text_lenght',e);
		chectShortCodeUsage();
	});

	// function to append shortcode in textarea

	function addTofield(fieldId,name,id){
		var shortcode = name;
		var oldval = $('#'+fieldId).val();
		$('#'+id).css('color','#676767');
		if(oldval.indexOf(name) > -1){
			$('#'+id).css('color','#676767');
		} else {
			$('#'+fieldId).val(oldval+shortcode);
			textlength('smstemplate', 'text_lenght');
		}
	}

	// code to get sms template lists

	$('#templatefor').change(function(){
		var template_id = $(this).val();
		$('#template_id').val(template_id);
		if(template_id > 0 || template_id != ''){
			$(this).removeClass('invalid');
			$.ajax({  
	            url:"ajax/system_details.php",
	            method:"POST",
	            data: {id : template_id, 'action':'getsmstemplate'},
	            success:function(response){
	            	$('#smstemplate').val(response);
	            	$('#smstemplate').removeClass('invalid');
	            	textlength('smstemplate', 'text_lenght');
	            	chectShortCodeUsage();
	            }
	       });
		} else {
			$('#smstemplate').val('');
			textlength('smstemplate', 'text_lenght');
			chectShortCodeUsage();
		}
	});

	// function to update extra hours setting

	function extraHours(value){
		$.ajax({  
            url:"ajax/system_details.php",
            method:"POST",
            data: {status : value, 'action':'upExtraHours'},
            success:function(response){
            	// code
            }
       });
	}

	//function to save sms templates
	function savesmsTemplate(){
		var dropdownDiv = $('#templatefor'); 
		var dropdownid = dropdownDiv.val();
		var template_detail = $('#smstemplate').val();
		var template_id = $('#template_id').val();
		if(dropdownid == '' || dropdownid == '0'){
			dropdownDiv.addClass('invalid');
		} else if(template_detail == ''){
			$('#smstemplate').addClass('invalid');
		} else {
			$('#smstemplate').removeClass('invalid');
			$.ajax({  
	            url:"ajax/system_details.php",
	            method:"POST",
	            data: {id : template_id, detail : template_detail, 'action':'savesmstemplate'},
	            success:function(response){
	            	if(response == '1'){
	               		location.reload();
	               	}
	            }
	       });
		}
	}

	// function to save autometic reminder settings

	function reminders(rowId){
		var status;
		if($('#rem-'+rowId).is(':checked')){ 
			status = 1; 
		} else { 
			status = 0 
		}
		$.ajax({  
            url:"ajax/system_details.php",
            method:"POST",
            data: {id : rowId, status : status, 'action':'saveremindersetting'},
            success:function(response){
            	if(response == '1'){
               		//location.reload();
               	}
            }
       });
	}

	// function to add day end report setting

	function dayEndReport(){
		var timeDiv = $('#endreporttime');
		var emailDiv = $('#reportemail');
		var smsDiv = $('#reportsms');
		var id = $('#dayreportid').val();
		var sms, email, time;
		if(timeDiv.val() == ''){
			timeDiv.addClass('invalid');
		} else {
			timeDiv.removeClass('invalid');
			time = timeDiv.val();
			if(smsDiv.is(':checked')){ sms = 1 } else { sms = 0; }
			if(emailDiv.is(':checked')){ email = 1 } else { email = 0; }
			$.ajax({  
            url:"ajax/system_details.php",
            method:"POST",
            data: {id : id, sms : sms, email : email, time : time, 'action':'saveEndDayReport'},
            success:function(response){
	            	if(response == '1'){
	               		location.reload();
	               	}
	            }
	       });
		}
	}


	// function to add and save reedem point settings

	function redeemSetting(){
		var pointDiv = $('#redeempoints');
		var priceDiv = $('#redeempointsprice');
		var maxpointDiv = $('#maxredeempoints');
		var id = $('#redeem_point_id').val();
		var point, price, max_point;
		if(pointDiv.val() == '' || priceDiv.val() == '' || maxpointDiv.val() == ''){
			if(pointDiv.val() == ''){ pointDiv.addClass('invalid'); } else { pointDiv.removeClass('invalid'); }
			if(priceDiv.val() == ''){ priceDiv.addClass('invalid'); } else { priceDiv.removeClass('invalid'); }
			if(maxpointDiv.val() == ''){ maxpointDiv.addClass('invalid'); } else { maxpointDiv.removeClass('invalid'); }
		} else {
			point = pointDiv.val();
			price = priceDiv.val();
			max_point = maxpointDiv.val();
			$.ajax({  
            url:"ajax/system_details.php",
            method:"POST",
            data: {id : id, point : point, price : price, max_point : max_point, 'action':'redeemPoint'},
            success:function(response){
	            	if(response == '1'){
	               		location.reload();
	               	}
	            }
	       });
		}
	}

	</script>		