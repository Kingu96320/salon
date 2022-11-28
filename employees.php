<?php
include "./includes/db_include.php";
$branch_id = $_SESSION['branch_id'];
if(isset($_GET['id']) && $_GET['id']>0)
{
    $id = $_GET['id'];
    $edit = query_by_id("SELECT *, emp.id as emp_id, emp.name as emp_name from `employee` emp LEFT JOIN `user` usr on emp.id = SUBSTRING_INDEX(usr.sp_id,',',-1)  where emp.id=:id and emp.active = 0 and emp.branch_id='".$branch_id."'",["id"=>$id],$conn)[0];

    if(!$edit){
		header('LOCATION:employees.php');
		exit();
	}

	$uid = query_by_id("SELECT id, username from user where sp_id=:id and active=0 and branch_id='".$branch_id."'",["id"=>'emp,'.$edit['emp_id']],$conn)[0];
}


if(isset($_POST['submit'])){
	$name	            = addslashes(trim(htmlspecialchars($_POST['name'])));
	$name	            = ucfirst($name);
	$dob 	            = addslashes(trim(htmlspecialchars($_POST['dob'])));
	$doj 	            = addslashes(trim(htmlspecialchars($_POST['doj'])));
	$email 	            = addslashes(trim(htmlspecialchars($_POST['email'])));
	$cont 	            = addslashes(trim(htmlspecialchars($_POST['cont'])));
	$econt 	            = addslashes(trim(htmlspecialchars($_POST['econt'])));
	$eperson            = addslashes(trim(htmlspecialchars($_POST['eperson'])));
	$addr 	            = addslashes(trim(htmlspecialchars($_POST['addr'])));
	$start	            = addslashes(trim(htmlspecialchars(date('H:i',strtotime($_POST['start'])))));
	$end 	            = addslashes(trim(htmlspecialchars(date('H:i',strtotime($_POST['end'])))));
	$month_sal          = addslashes(trim(htmlspecialchars($_POST['month_sal'])));
	$role               = addslashes(trim(htmlspecialchars($_POST['role'])));
	$username           = addslashes(trim($_POST['userName']));
	$password           = addslashes(trim($_POST['password']));
	$confirm_password   = addslashes(trim($_POST['confirm_password']));
	$gender             = addslashes(trim($_POST['gender']));		
	$dept_id            = addslashes(trim($_POST['department']));		
    $allowed =  array('png' ,'jpg','jpeg');
    $profile = '';
    $proof = '';
    if($password == $confirm_password){

		$password = md5($password);
		$salt="ea7b7a7372bceab4a64b3c2d380c8a72";
		$pass = $salt.$password;
		$password = md5(sha1(md5($pass)));

		if($username == 'admin'){
			$_SESSION['t']  = 2;
            $_SESSION['tmsg']  = 'You can\'t create Employee with username `admin`';
            header('LOCATION:employees.php');
            exit();
		}

        if(isset($_FILES["photo"]) && $_FILES["photo"]["name"]!=''){
		$newfilename = round(microtime(true));
		$uploadfile=$newfilename.$_FILES["photo"]["name"];
		$folder="upload/profile/";
		if (!file_exists($folder) && !is_dir($folder)) {
			mkdir($folder);    
		}
		$profile = $folder.$uploadfile;
                $allowed =  array('png' ,'jpg','jpeg');
                $filename = $_FILES['photo']['name'];
                $ext = pathinfo($filename, PATHINFO_EXTENSION);
                if(!in_array($ext,$allowed) ) {
				$_SESSION['t']  = 2;
				$_SESSION['tmsg']  = "File is not a image";
				echo '<meta http-equiv="refresh" content="0; url=employees.php" />';die();
                }
				move_uploaded_file($_FILES["photo"]["tmp_name"],$profile);
        }
        if(isset($_FILES["proof"]) && $_FILES["proof"]["name"]!=''){
		
		$newfilename = round(microtime(true));
		$uploadfile=$newfilename.$_FILES["proof"]["name"];
		$folder="upload/proof/";
		if (!file_exists($folder) && !is_dir($folder)) {
			mkdir($folder);    
		}
		$proof = $folder.$uploadfile;
                $filename = $_FILES['proof']['name'];
                $ext = pathinfo($filename, PATHINFO_EXTENSION);
                if(!in_array($ext,$allowed) ) {
                    $_SESSION['t']  = 2;
                    $_SESSION['tmsg']  = "File is not a image";
                    echo '<meta http-equiv="refresh" content="0; url=employees.php" />';die();
                }
				move_uploaded_file($_FILES["proof"]["tmp_name"], $proof);
        }

        $col_inc = 0;
        $col_id_qry = query_by_id("SELECT cal_color from employee where type=2 and branch_id='".$branch_id."' order by id desc limit 1",[],$conn);
        if($col_id_qry){
            $col_inc = ((int)$col_id_qry[0]['cal_color']) + 1;
            if($col_inc>COLOR_TOTAL){
                $col_inc = 0;
			}
		}
	 
		$emp_id = get_insert_id("INSERT INTO `employee` (`name`,`dob`,`gender`,`cont`,`email`,`emergency_cont`,`emergency_person`,`address`,`month_sal`,`photo`,`idproof`,`cal_color`,`active`,`type`,`starttime`,`endtime`,`dept_id`,`doj`,`branch_id`) VALUES ('$name','$dob','$gender','$cont','$email','$econt','$eperson','$addr','$month_sal','$profile','$proof','$col_inc',0,2,'$start','$end','$dept_id','$doj','$branch_id')",[],$conn);
		
		if($username != '' && $password != ''){
			query("INSERT INTO `user`(`username`,`name`,`sp_id`,`pass`,`role`,`active`,`branch_id`) VALUES ('$username','$name','emp,$emp_id','$password','$role',0,'$branch_id')",[],$conn);
		}

		$_SESSION['t']  = 1;
		$_SESSION['tmsg']  = "Employee Added Successfully";
		echo '<meta http-equiv="refresh" content="0; url=employees.php" />';die();
	} else {
		$_SESSION['t']  = 2;
		$_SESSION['tmsg']  = "Password does not match";
		echo '<meta http-equiv="refresh" content="0; url=employees.php" />';die();
	}
}

// update password 
if (isset($_POST['update-password'])) {
	$uid        = addslashes(trim($_POST['sp_user_id']));
	$user_id    = addslashes(trim($_POST['user_id']));
	$role       = addslashes(trim($_POST['role']));
	$upass      = addslashes(trim($_POST['upassword']));
	$ucpass     = addslashes(trim($_POST['ucpassword']));
	if($upass == $ucpass){
		if($upass == '' || $ucpass == ''){
			$_SESSION['t']  = 2;
			$_SESSION['tmsg']  = "Empty password field";
			echo '<meta http-equiv="refresh" content="0; url=employees.php?id='.$uid.'" />';die();
		} else {
			$password = md5($upass);
			$salt="ea7b7a7372bceab4a64b3c2d380c8a72";
			$pass = $salt.$password;
			$password = md5(sha1(md5($pass)));
			query("UPDATE `user` set `pass`='$password' where id=:id and sp_id=:sp_id and branch_id='".$branch_id."'",["id"=>$user_id, "sp_id"=>'emp,'.$uid],$conn);	
			$_SESSION['t']  = 1;
			$_SESSION['tmsg']  = "Password Updated Successfully";
			echo '<meta http-equiv="refresh" content="0; url=employees.php?id='.$uid.'" />';die();
		}
	} else {
		$_SESSION['t']  = 2;
		$_SESSION['tmsg']  = "Password does not match";
		echo '<meta http-equiv="refresh" content="0; url=employees.php?id='.$uid.'" />';die();
	}
}

//  function to create new account for un-registered users

if(isset($_POST['add-account'])){
	$user_id        = addslashes(trim($_POST['sp_user_id']));
	$name           = addslashes(trim($_POST['accountname']));
	$username       = addslashes(trim($_POST['newuserName']));
	$role           = addslashes(trim($_POST['role']));
	$upass          = addslashes(trim($_POST['newpassword']));
	$ucpass         = addslashes(trim($_POST['newconfirm_password']));
	if($upass == $ucpass){
		if($upass == '' || $ucpass == ''){
			$_SESSION['t']  = 2;
			$_SESSION['tmsg']  = "Empty password field";
			echo '<meta http-equiv="refresh" content="0; url=employees.php?id='.$user_id.'" />';die();
		} else {
			$password = md5($upass);
			$salt="ea7b7a7372bceab4a64b3c2d380c8a72";
			$pass = $salt.$password;
			$password = md5(sha1(md5($pass)));
			query("INSERT INTO `user`(`username`,`name`,`sp_id`,`pass`,`role`,`active`,`branch_id`) VALUES ('$username','$name','emp,$user_id','$password','$role',0,'$branch_id')",[],$conn);
			$_SESSION['t']  = 1;
			$_SESSION['tmsg']  = "Account created Successfully";
			echo '<meta http-equiv="refresh" content="0; url=employees.php?id='.$user_id.'" />';die();
		}
	} else {
		$_SESSION['t']  = 2;
		$_SESSION['tmsg']  = "Password does not match";
		echo '<meta http-equiv="refresh" content="0; url=employees.php?id='.$user_id.'" />';die();
	}
}

if(isset($_GET['d'])){
    if(DELETE_BUTTON_INACTIVE != 'true'){
    	$d = $_GET['d'];
    	query("update `employee` set active=1 where id=$d and branch_id='".$branch_id."'",[],$conn);
    	query("update `user` set active=1 where sp_id=$d and branch_id='".$branch_id."'",[],$conn);
    	$_SESSION['t']  = 1;
    	$_SESSION['tmsg']  = "Employee In-activated";
    	echo '<meta http-equiv="refresh" content="0; url=employees.php" />';die();
    }
}

if(isset($_GET['activeid'])){
    if(DELETE_BUTTON_INACTIVE != 'true'){
    	$d = $_GET['activeid'];
    	query("update `employee` set active=0 where id=$d and branch_id='".$branch_id."'",[],$conn);
    	query("update `user` set active=0 where sp_id=$d and branch_id='".$branch_id."'",[],$conn);
    	$_SESSION['t']  = 1;
    	$_SESSION['tmsg']  = "Employee Activated";
    	echo '<meta http-equiv="refresh" content="0; url=employees.php" />';die();
    }
}

if(isset($_POST['edit-submit'])){
	$id 	        = addslashes(trim($_POST['eid']));
	$uid 	        = addslashes(trim($_POST['uid']));
	$name	        = addslashes(trim(htmlspecialchars($_POST['name'])));
	$name	        = ucfirst($name);
	$dob 	        = addslashes(trim(htmlspecialchars($_POST['dob'])));
	$doj 	        = addslashes(trim(htmlspecialchars($_POST['doj'])));
	$email 	        = addslashes(trim(htmlspecialchars($_POST['email'])));
	$cont 	        = addslashes(trim(htmlspecialchars($_POST['cont'])));
	$econt 	        = addslashes(trim(htmlspecialchars($_POST['econt'])));
	$eperson        = addslashes(trim(htmlspecialchars($_POST['eperson'])));
	$addr 	        = addslashes(trim(htmlspecialchars($_POST['addr'])));
	$start	        = addslashes(trim(htmlspecialchars(date('H:i',strtotime($_POST['start'])))));
	$end 	        = addslashes(trim(htmlspecialchars(date('H:i',strtotime($_POST['end'])))));
	$month_sal      = addslashes(trim(htmlspecialchars($_POST['month_sal'])));	
	$role           = addslashes(trim(htmlspecialchars($_POST['role'])));
	$gender         = addslashes(trim($_POST['gender']));
	$dept_id        = addslashes(trim($_POST['department']));
    $allowed = array('png' ,'jpg','jpeg');
	

	$col_inc = 0;
	$col_id_qry =  query_by_id("SELECT cal_color from employee where type=2 and branch_id='".$branch_id."' order by id desc limit 1",[],$conn);
	if($col_id_qry){
		$col_inc = ((int)$col_id_qry[0]['cal_color']) + 1;
		if($col_inc>COLOR_TOTAL){
			$col_inc = 0;
		}
	}
	 
    if(isset($_FILES["photo"]) && $_FILES['photo']['name']!='')
	{
		$newfilename = round(microtime(true));
		$uploadfile=$newfilename.$_FILES["photo"]["name"];
		$folder="upload/profile/";
		if (!file_exists($folder) && !is_dir($folder)) {
			mkdir($folder);    
		}
		$profile = $folder.$uploadfile;
                $filename = $_FILES['photo']['name'];
                $ext = pathinfo($filename, PATHINFO_EXTENSION);
                if(!in_array($ext,$allowed) ) {
				$_SESSION['t']  = 2;
				$_SESSION['tmsg']  = "File is not a image";
				echo '<meta http-equiv="refresh" content="0; url=employees.php?id='.$id.'" />';die();
                }
		move_uploaded_file($_FILES["photo"]["tmp_name"],$profile);

		query("UPDATE `employee` set `name`='$name',`dob`='$dob',`doj`='$doj',`gender`='$gender',`cont`='$cont',`email`='$email',`emergency_cont`='$econt',`emergency_person`='$eperson',`address`='$addr',`month_sal`='$month_sal',`photo`='$profile',`cal_color`='$col_inc',`type`='2',`starttime`='$start',`endtime`='$end',`dept_id`='$dept_id',`active`=0 where id=:id and branch_id='".$branch_id."'",["id"=>$id],$conn);	

		// check name in user table and update 
			if(isset($uid)){
				query("UPDATE `user` set `name`='$name', `role`='$role' where id=:id and sp_id=:sp_id and branch_id='".$branch_id."'",["id"=>$uid, "sp_id"=>'emp,'.$id],$conn);
			}	
	}
	
	if(isset($_FILES["photo"]) && $_FILES['proof']['name']!=''){
		$newfilename = round(microtime(true));
		$uploadfile=$newfilename.$_FILES["proof"]["name"];
		$folder="upload/proof/";
		if (!file_exists($folder) && !is_dir($folder)) {
			mkdir($folder);    
		}
		$proof = $folder.$uploadfile;
                $filename = $_FILES['proof']['name'];
                $ext = pathinfo($filename, PATHINFO_EXTENSION);
                if(!in_array($ext,$allowed) ) {
				$_SESSION['t']  = 2;
				$_SESSION['tmsg']  = "File is not a image";
				echo '<meta http-equiv="refresh" content="0; url=employees.php?id='.$id.'" />';die();
                }
		move_uploaded_file($_FILES["proof"]["tmp_name"], $proof);

		query("UPDATE `employee` set `name`='$name',`dob`='$dob',`doj`='$doj',`gender`='$gender',`cont`='$cont',`email`='$email',`emergency_cont`='$econt',`emergency_person`='$eperson',`address`='$addr',`month_sal`='$month_sal',`idproof`='$proof',`cal_color`='$col_inc',`type`='2',`starttime`='$start',`endtime`='$end',`dept_id`='$dept_id',`active`=0 where id=:id and branch_id='".$branch_id."'",["id"=>$id],$conn);	

		// check name in user table and update 
			if(isset($uid)){
				query("UPDATE `user` set `name`='$name', `role`='$role' where id=:id and sp_id=:sp_id and branch_id='".$branch_id."'",["id"=>$uid, "sp_id"=>'emp,'.$id],$conn);
			}	

				$_SESSION['t']  = 1;
				$_SESSION['tmsg']  = "Employee Updated Successfully";
				echo '<meta http-equiv="refresh" content="0; url=employees.php?id='.$id.'" />';die();
	}
	else{	 
		query("UPDATE `employee` set `name`='$name',`dob`='$dob',`doj`='$doj',`gender`='$gender',`cont`='$cont',`email`='$email',`emergency_cont`='$econt',`emergency_person`='$eperson',`address`='$addr',`month_sal`='$month_sal',`cal_color`='$col_inc',`type`='2',`starttime`='$start',`endtime`='$end',`dept_id`='$dept_id',`active`=0 where id=:id and branch_id='".$branch_id."'",["id"=>$id],$conn);	

		// check name in user table and update 
		if(isset($uid)){
			query("UPDATE `user` set `name`='$name', `role`='$role' where id=:id and sp_id=:sp_id and branch_id='".$branch_id."'",["id"=>$uid, "sp_id"=>'emp,'.$id],$conn);
		}					
		$_SESSION['t']  = 1;
		$_SESSION['tmsg']  = "Employee Updated Successfully";
		echo '<meta http-equiv="refresh" content="0; url=employees.php?id='.$id.'" />';die();
	}
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
						
						<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
							<div class="panel">
								<div class="panel-heading">
									<h4><?php if(!isset($id)){ echo 'Add'; } else { echo 'Edit'; } ?> staff</h4>
								</div>
								<div class="panel-body">
								
									<div class="row">
									<form action="" method="post" enctype="multipart/form-data">
										<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
											<div class="form-group">
												<label for="name">Enter name <span class="text-danger">*</span></label>
												<input type="text" class="form-control" id="name" name="name" placeholder="Employee name" value="<?= isset($id)?$edit['emp_name']:old('name')?>" required>
											</div>
										</div>
										
										<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
											<div class="form-group">
												<label for="dob">Date of birth</label>
												<input type="text" class="form-control dob_annv_date" id="dob" name="dob" value="<?= isset($id)?$edit['dob']:old('dob')?>" readonly>
											</div>
										</div>
										<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
											<div class="form-group">
												<label for="cont">Contact number </label>
												<input type="text" class="form-control" maxlength="<?= PHONE_NUMBER ?>" name="cont" onBlur="checkcont();contact_no_length($(this), this.value);" id="bcont" placeholder="Contact number" value="<?= isset($id)?$edit['cont']:old('cont')?>" >
												<span id="bcont-status" class="text-danger"></span>
												<span style="color:red" id="digit_error"></span>
											</div>
										</div>
										<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
											<div class="form-group">
												<label for="email">Email Address</label>
												<input type="email" class="form-control" id="email" name="email" placeholder="Email" value="<?= isset($id)?$edit['email']:old('email')?>" >
											</div>
										</div>
										<div class="clearfix"></div>
										<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
											<div class="form-group">
												<label for="">Working hours <span class="text-danger">*</span></label>
												<div class="clearfix"></div>
												<div class="row">
													<?php $start_time = shopopentime(); ?>
													<div class="col-lg-6 col-md-6">
	                                                    <input type="text" class="form-control time" name="start" placeholder="Start Time" value="<?= isset($id)?date('h:i A',strtotime($edit['starttime'])):date('h:i A',strtotime($start_time))?>" required>
	                                                </div>
	                                                <?php $end_time = shopclosetime(); ?>
													<div class="col-lg-6 col-md-6">
	                                                   <input type="text" class="form-control time" name="end" placeholder="End Time" value="<?= isset($id)?date('h:i A',strtotime($edit['endtime'])):date('h:i A',strtotime($end_time))?>" required>
	                                               </div>
	                                            </div>
											</div>
										</div>
										<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
											<div class="form-group">
												<label for="salary">Monthly salary <span class="text-danger">*</span></label>
												<input type="text" class="form-control" id="month_sal" name="month_sal" placeholder="Monthly salary"  value="<?= isset($id)?$edit['month_sal']:old('month_sal')?>" required>
											</div>
										</div>
										<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
											<div class="form-group">
												<label for="econt">Emergency contact number</label>
												<input type="text" class="form-control" onblur="othercontact($(this), this.value);" maxlength="<?= PHONE_NUMBER ?>" name="econt" id="econt" placeholder="Emergency contact number" value="<?= isset($id)?$edit['emergency_cont']:old('name')?>">
											    <span style="color:red" class="conterror"></span>
											</div>
										</div>
										<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
											<div class="form-group">
												<label for="eperson">Emergency contact person</label>
												<input type="text" class="form-control" onblur="othercontact($(this), this.value);" id="eperson" name="eperson" placeholder="Emergency contact person"   value="<?= isset($id)?$edit['emergency_person']:old('name')?>"  >
											    <span style="color:red" class="conterror"></span>
											</div>
										</div>
										<div class="clearfix"></div>
										<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
											<div class="form-group">
												<label for="addr">Address</label>
												<input type="text" class="form-control" id="addr" name="addr" placeholder="Address " value="<?= isset($id)?$edit['address']:old('name')?>" >
											</div>
										</div>
										<?php if(!isset($id)){ ?>
											<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
												<div class="form-group">
													<label for="userName">Username</label>
													<input type="text" class="form-control" onBlur="checkuser()" name="userName" id="userName" placeholder="Username" value="">
													<span id="user-status" class="text-danger"></span>				
												</div>
											</div>
											<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
												<div class="form-group">
													<label for="password">Password</label>
													<input type="password" onBlur="checklength()" class="form-control" name="password" id="password" placeholder="Password">
													<span id="pass-status" class="text-danger"></span>						
												</div>
											</div>
											<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
												<div class="form-group">
													<label for="confirm-password">Confirm password</label>
													<input type="password" class="form-control" name="confirm_password" id="confirm_password" placeholder="Confirm password">
												</div>
											</div>
											<div class="clearfix"></div>
										<?php } ?>	
										<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
											<div class="form-group">
												<label for="gender">Gender <span class="text-danger">*</span></label>
												<div class="mr-top-10">
													<input type="radio" <?php if(!isset($id)) { echo 'checked'; } ?> name="gender" value="1" <?php if(isset($id)){ echo $edit['gender'] == '1' ? 'checked' : ''; } ?>> Male &nbsp;&nbsp;&nbsp;&nbsp;
													<input type="radio" name="gender" value="2" <?php if(isset($id)){ echo $edit['gender'] == '2' ? 'checked' : ''; }?>> Female
												</div>
											</div>
										</div>	
										<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
											<div class="form-group">
												<label for="doj">Date of joining <span class="text-danger">*</span></label>
												<input type="text" class="form-control urdate" name="doj" value="<?= isset($id)?$edit['doj']:date('Y-m-d')?>" id="doj" required readonly>
											</div>
										</div>								
										
										<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
											<?php if((!isset($id) && !isset($uid)) || (isset($id) && isset($uid))){ ?>
											<div class="form-group">
												<label for="role">User type <span class="text-danger">*</span></label>
												<select class="form-control" name="role" id="role">
													<option value="2" <?= $edit['role'] == 2 ? 'selected' : ''; ?>>User</option>
													<option value="1" <?= $edit['role'] == 1 ? 'selected' : ''; ?>>Admin</option>
												</select>
											</div>
										<?php } ?>
										</div>

										<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
											<div class="form-group">
												<?php 
													$departments = query_by_id("SELECT * from staff_department where status='0'",[],$conn);
												?>
												<label for="role">Department <span class="text-danger">*</span></label>
												<select class="form-control" name="department" id="department" required>
													<option>--select--</option>
													<?php 
														foreach($departments as $st_dept){
															?>
															<option <?= $edit['dept_id']==$st_dept['id']?'selected':''; ?> value="<?= $st_dept['id']?>"><?= $st_dept['department_name']; ?></option>
															<?php
														}
													?>
												</select>
											</div>
										</div>

										<?php if(!isset($id)){ ?> <div class="clearfix"></div> <?php } ?>	
										<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
											<div class="form-group">
												<label for="photo">Upload Photo</label>
												<input type="file" class="form-control" id="photo" name="photo"   placeholder="profile">
												<?php if(strpos($edit['photo'], 'upload') !== false){ ?>
													<img src="<?= $edit['photo'] ?>" class="img-responsive edit-avt-img img-thumbnail" />
												<?php } else {
													if($edit['gender'] == '1'){ ?>
														<img src="img/avatar/male.png" class="img-responsive edit-avt-img img-thumbnail" />
													<?php } else if($edit['gender'] == '2'){ ?>
														<img src="img/avatar/female.png" class="img-responsive edit-avt-img img-thumbnail" />
													<?php }
												} ?>
											</div>
										</div>
										<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
											<div class="form-group">
												<label for="proof">Upload ID Proof</label>
												<input type="file" class="form-control" id="proof" name="proof"   placeholder="idproof" >
												<?php if(isset($id)){ 
													if(strpos($edit['idproof'], 'upload') !== false){ ?>
													<a href="<?= $edit['idproof'] ?>" data-lightbox="<?= $edit['emp_id'] ?>">
													<img src="<?= $edit['idproof'] ?>" class="img-responsive edit-avt-img img-thumbnail" />
													</a>
												<?php }
													} 
												?>											
											</div>
										</div>
										<div class="clearfix"></div>
										<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 pull-right">
											<div class="form-group">	
											<?php if(isset($id)) { ?>
												<input type="hidden" name="eid" id="eid" value="<?=$id?>">
												<input type="hidden" name="uid" value="<?=$uid['id']?>">
												<a href="employees.php">
													<button type="button" class="btn btn-danger pull-right mr-left-5"><i class="fa fa-times" aria-hidden="true"></i>Cancel</button>
												</a>
												<button type="submit" name="edit-submit" class="btn btn-info pull-right"><i class="fa fa-pencil-square-o" aria-hidden="true"></i>Update staff</button>
											<?php } else { ?>
												<button type="submit" name="submit" class="btn btn-success pull-right"><i class="fa fa-plus" aria-hidden="true"></i>Add staff</button>
											<?php } ?>
											</div>
										</div>
									</form>
								</div>
								</div>
							</div>
						<?php if(isset($id) && isset($uid)){ ?>
					<div class="panel">
						<div class="panel-heading">
							<h4>Update password</h4>
						</div>
						<div class="panel-body">
							<div class="row">
								<form action="" method="post" enctype="multipart/form-data">
									<div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
										<div class="form-group">
											<label for="username">Username</label>
											<input type="text" class="form-control" readonly disabled name="username" value="<?= isset($id)?$uid['username']:old('name')?>" id="username" placeholder="Service Provider name">
										</div>
									</div>
									<div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
										<div class="form-group">
											<label for="upassword">New password <span class="text-danger">*</span></label>
											<input required type="password" onBlur="checklength()" class="form-control" name="upassword" id="password" placeholder="New password">
											<span id="pass-status" class="text-danger"></span>
										</div>
									</div>
									<div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
										<div class="form-group">
											<label for="ucpassword">Confirm password <span class="text-danger">*</span></label>
											<input required type="password" class="form-control" name="ucpassword" id="ucpassword" placeholder="Confirm password">
										</div>
									</div>
									<div class="clearfix"></div>
									<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
										<input type="hidden" name="user_id" value="<?= $uid['id'] ?>">
										<input type="hidden" name="sp_user_id" value="<?= $edit['emp_id'] ?>">
										<button type="submit" name="update-password" class="btn btn-info pull-right "><i class="fa fa-pencil-square-o" aria-hidden="true"></i> Update password</button>
									</div>
								</form>
							</div>
						</div>
					</div>
					<?php } else if(isset($id) && !isset($uid)){ ?>
						<div class="panel" id="newaccount">
							<div class="panel-heading">
								<h4>Create account</h4>
							</div>
							<div class="panel-body">
								<div class="row">
									<form action="" method="post" enctype="multipart/form-data">
										<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
											<div class="form-group">
												<label for="newuserName">Username <span class="text-danger">*</span></label>
												<input type="text" class="form-control" onBlur="checkuser()" name="newuserName" id="userName" placeholder="User Name" value="" required>
												<span id="user-status" class="text-danger"></span>				
											</div>
										</div>
										<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
											<div class="form-group">
												<label for="newpassword">Password <span class="text-danger">*</span></label>
												<input type="password" onBlur="checklength()" class="form-control" name="newpassword" id="password" placeholder="Password" required>
												<span id="pass-status" class="text-danger"></span>		
											</div>
										</div>
										<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
											<div class="form-group">
												<label for="newconfirm-password">Confirm password <span class="text-danger">*</span></label>
												<input type="password" class="form-control" name="newconfirm_password" id="confirm_password" placeholder="Confirm password" required>
											</div>
										</div>
										<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
											<div class="form-group">
												<label for="role">User type <span class="text-danger">*</span></label>
												<select class="form-control" name="role" id="role">
													<option value="2" <?= $edit['role'] == 2 ? 'selected' : ''; ?>>User</option>
													<option value="1" <?= $edit['role'] == 1 ? 'selected' : ''; ?>>Admin</option>
												</select>
											</div>
										</div>
										<div class="clearfix"></div>
										<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
											<input type="hidden" name="sp_user_id" value="<?= $edit['emp_id'] ?>">
											<input type="hidden" name="accountname" value="<?= $edit['emp_name'] ?>">
											<button type="submit" name="add-account" class="btn btn-success pull-right "><i class="fa fa-plus" aria-hidden="true"></i>Create account</button>
										</div>
									</form>
								</div>
							</div>
						</div>
					<?php } ?>
					


								<div class="clearfix"></div>
							<div class="panel">
								<div class="panel-heading heading-with-btn">
									<h4 class="pull-left">Manage active staff(s)</h4>
									<span id="download-btn"></span>
									<div class="clearfix"></div>
								</div>
								<div class="panel-body">
									<div class="">
										<div class="table-responsive">
										<table class="table table-bordered no-margin grid">
											<thead>
												<tr>
													<th class="not-export-column">Profile image</th>
													<th>Name</th>
													<th>Contact number</th>
													<th>Emergency contact number</th>
													<th>Emergency contact person</th>
													<th>Role</th>
													<th>Attendence id</th>
													<th>Action</th>
												</tr>
											</thead>
											<tbody>
											<?php
												$sql1="SELECT * from employee where active=0 and branch_id='".$branch_id."' order by name asc";
												$result1=query_by_id($sql1,[],$conn);
												foreach($result1 as $row1) {
												$userid = query_by_id("SELECT id, username, role from user where sp_id=:id and active=0 and branch_id='".$branch_id."'",["id"=>'emp,'.$row1['id']],$conn)[0];
											?>
												<tr>
													<td  class="avatar">
														<?php if(strpos($row1['photo'], 'upload') !== false){ ?>
															<img src="<?= $row1['photo'] ?>" class="img-responsive" />
														<?php } else { 
															if($row1['gender'] == '1'){ ?>
																<img src="img/avatar/male.png" class="img-responsive" />
															<?php } else if($row1['gender'] == '2'){ ?>
																<img src="img/avatar/female.png" class="img-responsive" />
															<?php }
														}
														?>

														<?php if(strpos($row1['idproof'], 'upload') !== false){ ?>
															<a href="<?= $row1['idproof'] ?>" data-lightbox="<?= $row1['id'] ?>">
																<button type="button" class="btn btn-success btn-xs proof-btn"><i class="fa fa-eye" aria-hidden="true"></i> View proof id</button>
															</a>
														<?php } else { ?>
															<button disabled type="button" class="btn btn-danger btn-xs proof-btn"><i class="fa fa-eye-slash" aria-hidden="true"></i> No proof id</button>
														<?php } ?>
													</td>
													<td><?php echo $row1['name']; ?></td>
													<th><?php echo $row1['cont']; ?></th>
													<th><?php echo $row1['emergency_cont']; ?></th>
													<th><?php echo $row1['emergency_person']; ?></th>
													<th><?php 
														if($userid['role'] == 1){
															?>
															<span class="label label-success">Admin</span> 
															<?php }
														if($userid['role'] == 2){
															?> 
															<span class="label label-primary">User</span>
															<?php
														}
													?></th>
													<td><?php echo str_pad(2, 4, '0', STR_PAD_RIGHT)+$row1['id']; ?></td>
													<td class="multi-action-btn">
														<a href="employees.php?id=<?php echo $row1['id']; ?>"><button class="btn btn-warning btn-xs" type="button"><i class="fa fa-pencil-square-o" aria-hidden="true"></i>Edit</button></a> 
														<!-- <a href="payroll.php?pid=<?php echo $row1['id']; ?>"><button class="btn btn-warning btn-xs" type="button"><i class="fa fa-book" aria-hidden="true"></i>Payroll</button></a>  -->
														<?php if(DELETE_BUTTON_INACTIVE == 'true'){ ?>
														    <a href="#" onclick="return deleteDisabled();"><button class="btn btn-danger btn-xs" type="button"><i class="icon-delete"></i>Inactive</button></a>
														<?php } else { ?>
														    <a href="employees.php?d=<?php echo $row1['id']; ?>" onclick="return confirm('Are you sure?');"><button class="btn btn-danger btn-xs" type="button"><i class="icon-delete"></i>Inactive</button></a>
														<?php } ?>
														<?php if(!isset($userid)) { ?>
													<a href="employees.php?id=<?php echo $row1['id']; ?>#newaccount"> <button class="btn btn-success btn-xs" type="button"><i class="fa fa-plus" aria-hidden="true"></i>Create account</button></a>
													<?php } ?></td>
												</tr>
												<?php } ?>
											</tbody>
										</table>
										</div>
										
									</div>
								</div>
							</div>
							<div class="panel">
								<div class="panel-heading heading-with-btn">
									<h4 class="pull-left">Manage inactive staff(s)</h4>
									<span id="download-btn"></span>
									<div class="clearfix"></div>
								</div>
								<div class="panel-body">
									<div class="">
										<div class="table-responsive">
										<table class="table table-bordered no-margin grid">
											<thead>
												<tr>
													<th class="not-export-column">Profile image</th>
													<th>Name</th>
													<th>Contact number</th>
													<th>Emergency contact number</th>
													<th>Emergency contact person</th>
													<th>Role</th>
													<th>Attendence id</th>
													<th>Action</th>
												</tr>
											</thead>
											<tbody>
											<?php
												$sql1="SELECT * from employee where active=1 and branch_id='".$branch_id."' order by name asc";
												$result1=query_by_id($sql1,[],$conn);
												foreach($result1 as $row1) {
												$userid = query_by_id("SELECT id, username, role from user where sp_id=:id and active=0 and branch_id='".$branch_id."'",["id"=>'emp,'.$row1['id']],$conn)[0];
											?>
												<tr>
													<td  class="avatar">
														<?php if(strpos($row1['photo'], 'upload') !== false){ ?>
															<img src="<?= $row1['photo'] ?>" class="img-responsive" />
														<?php } else { 
															if($row1['gender'] == '1'){ ?>
																<img src="img/avatar/male.png" class="img-responsive" />
															<?php } else if($row1['gender'] == '2'){ ?>
																<img src="img/avatar/female.png" class="img-responsive" />
															<?php }
														}
														?>

														<?php if(strpos($row1['idproof'], 'upload') !== false){ ?>
															<a href="<?= $row1['idproof'] ?>" data-lightbox="<?= $row1['id'] ?>">
																<button type="button" class="btn btn-success btn-xs proof-btn"><i class="fa fa-eye" aria-hidden="true"></i> View proof id</button>
															</a>
														<?php } else { ?>
															<button disabled type="button" class="btn btn-danger btn-xs proof-btn"><i class="fa fa-eye-slash" aria-hidden="true"></i> No proof id</button>
														<?php } ?>
													</td>
													<td><?php echo $row1['name']; ?></td>
													<th><?php echo $row1['cont']; ?></th>
													<th><?php echo $row1['emergency_cont']; ?></th>
													<th><?php echo $row1['emergency_person']; ?></th>
													<th><?php 
														if($userid['role'] == 1){
															?>
															<span class="label label-success">Admin</span> 
															<?php }
														if($userid['role'] == 2){
															?> 
															<span class="label label-primary">User</span>
															<?php
														}
													?></th>
													<td><?php echo str_pad(2, 4, '0', STR_PAD_RIGHT)+$row1['id']; ?></td>
													<td class="multi-action-btn">
														<a href="employees.php?activeid=<?php echo $row1['id']; ?>"><button class="btn btn-success btn-xs" type="button"><i class="fa fa-check-square-o" aria-hidden="true"></i>Active</button></a> 
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
					</div>
					<!-- Row ends -->
				</div>
				<!-- Main container ends -->
			</div>
			<!-- Dashboard Wrapper End -->
		</div>
		<!-- Container fluid ends -->
<script>
function checkcont() {
	var cat = $('#bcont').val();
	<?php if(isset($id)) { ?>
		var id = $('#eid').val();
		var url = 'con='+cat+'&uid='+id;
	<?php } else { ?>
		var url = 'con='+cat;
	<?php } ?>

	jQuery.ajax({
		url: "checkemp.php?"+url,
		//data:'cat='+$("#bcont").val(),
		type: "POST",
		success:function(data){
		//alert(data);
		if(data=="Already Exist"){
			$("#bcont-status").html(data);
			// toastr.error("Employee Already Exists", "Error");
			$('#bcont').val("");
		}
	},
	error:function (){}
	});
}
function checklength() {		
	if($("#password").val().length <= 5 && $("#password").val().length > 0){
		//alert('Password Length 6 Chars minimum');
		$("#pass-status").html('Password Length 6 Chars minimum');
		$('#password').val("");
	} else if($("#password").val().length > 5 || $("#password").val().length <= 0){
		$("#pass-status").html('');
	}
}

function checkuser() {
	var cat = $('#userName').val();
	jQuery.ajax({
		url: "checkuser.php?usr="+$("#userName").val(),
		type: "POST",
		success:function(data){
			$("#user-status").html(data);
			if ( data.indexOf("Already Exist") > -1 ) {
				$('#userName').val("");
			}				
		},
		error:function (){}
	});
}
</script>
<?php 
include "footer.php";
?>