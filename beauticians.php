<?php
	include_once './includes/db_include.php';
	$branch_id = $_SESSION['branch_id'];
	if(isset($_GET['id']) && $_GET['id']>0)
	{
		$id = $_GET['id'];
		if(check_sp_transfer_status($id)){
			header('LOCATION:beauticians.php');
		}
		$edit = query_by_id("SELECT b.*,s.name as ser_pro_type from beauticians b left join ser_pro_types s on s.id=b.ser_pro_type_id where b.id=:id and b.branch_id='".$branch_id."'",["id"=>$id],$conn)[0];
		if(!$edit){
			header('LOCATION:beauticians.php');
			exit();
		}
		$uid = query_by_id("SELECT id, username from user where sp_id=:id and active=0 and branch_id='".$branch_id."'",["id"=>'sp,'.$edit['id']],$conn)[0];
		$service_commission = service_provider_commission('product',10,600000);
	}
	
	if(isset($_POST['submit'])){
		$name	            = addslashes(trim(htmlspecialchars($_POST['Name'])));
		$name	            = ucfirst($name);
		$scom	            = addslashes(trim(htmlspecialchars($_POST['scom'])));
		$pcom	            = addslashes(trim(htmlspecialchars($_POST['pcom'])));
		$dob  	            = addslashes(trim(htmlspecialchars($_POST['dob'])));
		$doj  	            = addslashes(trim(htmlspecialchars($_POST['doj'])));
		$email              = addslashes(trim(htmlspecialchars($_POST['email'])));
		$cont               = addslashes(trim(htmlspecialchars($_POST['cont'])));
		$econt              = addslashes(trim(htmlspecialchars($_POST['econt'])));
		$eperson            = addslashes(trim(htmlspecialchars($_POST['eperson'])));
		$addr	            = addslashes(trim(htmlspecialchars($_POST['addr'])));		
		$start	            = addslashes(trim(htmlspecialchars(date('H:i:s',strtotime($_POST['start'])))));
		$end 	            = addslashes(trim(htmlspecialchars(date('H:i:s',strtotime($_POST['end'])))));
		$month_sal          = addslashes(trim(htmlspecialchars($_POST['month_sal'])));
		$username           = addslashes(trim($_POST['userName']));
		$password           = addslashes(trim($_POST['password']));
		$confirm_password   = addslashes(trim($_POST['confirm_password']));
		$gender             = addslashes(trim($_POST['gender']));		
		$ser_pro_type 	    = addslashes(trim(ucfirst(htmlspecialchars($_POST['ser_pro_type']))));
		$ser_pro_type_id    = addslashes(trim(htmlspecialchars($_POST['ser_pro_type_id'])));

		if($password == $confirm_password){

			$password = md5($password);
			$salt="ea7b7a7372bceab4a64b3c2d380c8a72";
			$pass = $salt.$password;
			$password = md5(sha1(md5($pass)));

			if($username == 'admin'){
				$_SESSION['t']  = 2;
	            $_SESSION['tmsg']  = 'You can\'t create Beautician with username `admin`';
	            header('LOCATION:beauticians.php');
	            exit();
			}
			
			// check if profile image is selected
			if($_FILES["profile"]['name'] != '') {
		        $profile = upload_image($_FILES["profile"] );
		        if(!$profile[0]){
		            $_SESSION['t']  = 2;
		            $_SESSION['tmsg']  = $profile[1];
		            header('LOCATION:beauticians.php');
		            exit();
				}
			} else {
				$profile = '';
			}

			// check if proof image is uploaded

			if($_FILES["proof"]['name'] != ''){
		        $proof = upload_image($_FILES["proof"]);
		        if(!$proof[0]){
		            $_SESSION['t']  = 2;
		            $_SESSION['tmsg']  = $proof[1];
		            header('LOCATION:beauticians.php');
		            exit();
				}
			} else {
				$proof = '';
			}
			
			if($ser_pro_type_id==''){
				$res = query_by_id("SELECT id from ser_pro_types where LOWER(name)=LOWER(:name) and status='1' and branch_id='".$branch_id."'",["name"=>$ser_pro_type],$conn);
				if($res){
					$ser_pro_type_id = $res[0]['id'];
					}else{
					$ser_pro_type_id = get_insert_id("INSERT INTO ser_pro_types set name=:name, status='1', branch_id='".$branch_id."'",["name"=>$ser_pro_type],$conn); 
				}
			}
			
	        $col_inc = 0;
	        $col_id_qry = query_by_id("SELECT cal_color from beauticians where type=2 and branch_id='".$branch_id."' order by id desc limit 1",[],$conn);
	        if($col_id_qry){
	            $col_inc = ((int)$col_id_qry[0]['cal_color']) + 1;
	            if($col_inc>COLOR_TOTAL){
	                $col_inc = 0;
				}
			}
			$beautician = get_insert_id("INSERT INTO `beauticians` set `name`='$name',`serivce_commision`='$scom',`prod_commision`='$pcom',`dob`='$dob', `doj`='$doj',`gender`='$gender',`cont`='$cont',`email`='$email',`emergency_cont`='$econt',"
			. "`emergency_person`='$eperson', `address`='$addr', `month_sal`='$month_sal', `photo`='$profile[1]', `idproof`='$proof[1]',`cal_color`='$col_inc',`active`='0',`starttime`='$start',"
			. "`endtime`='$end',`type`='2',`ser_pro_type_id`='$ser_pro_type_id', `branch_id`='".$branch_id."'",[],$conn);

			if($username != '' && $password != ''){
				query("INSERT INTO `user`(`username`,`name`,`sp_id`,`pass`,`role`,`active`,`branch_id`) VALUES ('$username','$name','sp,$beautician','$password',3,0,'$branch_id')",[],$conn);
			}

			// send_sms($cont,"Thank You ".$name." to Join Us as Beautician");
			 
			$_SESSION['t']  = 1;
	        $_SESSION['tmsg']  = "Beautician Added Successfully";
	        header('LOCATION:beauticians.php?id='.$beautician);
	        die();

	    } else {
	    	$_SESSION['t']  = 2;
			$_SESSION['tmsg']  = "Password does not match";
			header('LOCATION:beauticians.php');
			die();
	    }
	}

	if (isset($_POST['update-password'])) {
		$uid = trim($_POST['user_id']);
		$sp_user_id = trim($_POST['sp_user_id']);
		$upass = trim($_POST['upassword']);
		$ucpass = trim($_POST['ucpassword']);
		if($upass == $ucpass){
			if($upass == '' || $ucpass == ''){
				$_SESSION['t']  = 2;
				$_SESSION['tmsg']  = "Empty password field";
				header('LOCATION:beauticians.php?id='.$sp_user_id);
				die();
			} else {
				$password = md5($upass);
				$salt="ea7b7a7372bceab4a64b3c2d380c8a72";
				$pass = $salt.$password;
				$password = md5(sha1(md5($pass)));
				query("UPDATE `user` set `pass`='$password' where id=:id and sp_id=:sp_id and branch_id='".$branch_id."'",["id"=>$uid, "sp_id"=>'sp,'.$sp_user_id],$conn);	
				$_SESSION['t']  = 1;
				$_SESSION['tmsg']  = "Password Updated Successfully";
				header('LOCATION:beauticians.php?id='.$sp_user_id);
				die();
			}
		} else {
			$_SESSION['t']  = 2;
			$_SESSION['tmsg']  = "Password does not match";
			header('LOCATION:beauticians.php?id='.$sp_user_id);
			die();
		}
	}

	//  function to create new account for un-registered service providers

	if(isset($_POST['add-account'])){
		$sp_user_id     = addslashes(trim($_POST['sp_user_id']));
		$name           = addslashes(trim($_POST['accountname']));
		$username       = addslashes(trim($_POST['newuserName']));
		$upass          = addslashes(trim($_POST['newpassword']));
		$ucpass         = addslashes(trim($_POST['newconfirm_password']));
		if($upass == $ucpass){
			if($upass == '' || $ucpass == ''){
				$_SESSION['t']  = 2;
				$_SESSION['tmsg']  = "Empty password field";
				header('LOCATION:beauticians.php?id='.$sp_user_id);
				die();
			} else {
				$password = md5($upass);
				$salt="ea7b7a7372bceab4a64b3c2d380c8a72";
				$pass = $salt.$password;
				$password = md5(sha1(md5($pass)));
				query("INSERT INTO `user`(`username`,`name`,`sp_id`,`pass`,`role`,`active`,`branch_id`) VALUES ('$username','$name','sp,$sp_user_id','$password',3,0,'$branch_id')",[],$conn);	
				$_SESSION['t']  = 1;
				$_SESSION['tmsg']  = "Account created Successfully";
				header('LOCATION:beauticians.php?id='.$sp_user_id);
				die();
			}
		} else {
			$_SESSION['t']  = 2;
			$_SESSION['tmsg']  = "Password does not match";
			header('LOCATION:beauticians.php?id='.$sp_user_id);
			die();
		}
	}

	if(isset($_POST['edit-submit'])){
		$id 	    = addslashes(trim($_POST['eid']));
		$uid        = addslashes(trim($_POST['uid']));
		$name	    = addslashes(trim(htmlspecialchars($_POST['Name'])));
		$name	    = ucfirst($name);
		$scom	    = addslashes(trim(htmlspecialchars($_POST['scom'])));
		$pcom	    = addslashes(trim(htmlspecialchars($_POST['pcom'])));
		$dob  	    = addslashes(trim(htmlspecialchars($_POST['dob'])));
		$doj  	    = addslashes(trim(htmlspecialchars($_POST['doj'])));
		$email      = addslashes(trim(htmlspecialchars($_POST['email'])));
		$cont       = addslashes(trim(htmlspecialchars($_POST['cont'])));
		$econt      = addslashes(trim(htmlspecialchars($_POST['econt'])));
		$eperson    = addslashes(trim(htmlspecialchars($_POST['eperson'])));
		$addr	    = addslashes(trim(htmlspecialchars($_POST['addr'])));
		$start	    = addslashes(trim(htmlspecialchars(date('H:i:s',strtotime($_POST['start'])))));
		$end 	    = addslashes(trim(htmlspecialchars(date('H:i:s',strtotime($_POST['end'])))));
		$month_sal  = addslashes(trim(htmlspecialchars($_POST['month_sal'])));
		
		//$username = trim($_POST['userName']);
		//$password = trim($_POST['password']);
		//$confirm_password = trim($_POST['confirm_password']);
		$gender = trim($_POST['gender']);	

		$ser_pro_type 	 = trim(ucfirst(htmlspecialchars($_POST['ser_pro_type'])));
		$ser_pro_type_id = trim(htmlspecialchars($_POST['ser_pro_type_id']));
		if($ser_pro_type_id ==''){
			$res = query_by_id("SELECT id from ser_pro_types where name=:name and status='1' and branch_id='".$branch_id."'",["name"=>$ser_pro_type],$conn);
			if(isset($res))
			{
				$ser_pro_type_id = $res[0]['id'];
				}else{
				$ser_pro_type_id = get_insert_id("UPDATE ser_pro_types set name=:name, status='1'",["name"=>$ser_pro_type],$conn); 
			}
		}
		
		if($_FILES['profile']['name'] != '' )
		{
			$profile = upload_image($_FILES["profile"]);
			if(!$profile[0])
			{
				$_SESSION['t']  = 2;
				$_SESSION['tmsg']  = $profile[1];
				header('LOCATION:beauticians.php?id='.$id);
			}
			$proof	 = upload_image($_FILES["proof"]);
			if(!$proof[0])
			{
				$_SESSION['t']  = 2;
				$_SESSION['tmsg']  = $proof[1];
				header('LOCATION:beauticians.php?id='.$id);
			}
			
			
			$col_inc = 0;
			$col_id_qry =  query_by_id("SELECT cal_color from beauticians where type=2 and branch_id='".$branch_id."' order by id desc limit 1",[],$conn);
			if($col_id_qry){
				$col_inc = ((int)$col_id_qry[0]['cal_color']) + 1;
				if($col_inc>COLOR_TOTAL){
					$col_inc = 0;
				}
			}
			
			query("UPDATE `beauticians` set `name`='$name',`serivce_commision`='$scom',`prod_commision`='$pcom',`dob`='$dob',`doj`='$doj',`gender`='$gender',`cont`='$cont',`email`='$email',`emergency_cont`='$econt',`emergency_person`='$eperson', `address`='$addr', `month_sal`='$month_sal', `photo`='$profile[1]',`cal_color`='$col_inc',`starttime`='$start',"
			. "`endtime`='$end',`type`='2',`ser_pro_type_id`='$ser_pro_type_id' where id='$id' and active='0' and branch_id='".$branch_id."'",[],$conn);
			
			// check name in user table and update 
			if(isset($uid)){
				query("UPDATE `user` set `name`='$name' where id=:id and sp_id=:sp_id and branch_id='".$branch_id."'",["id"=>$uid, "sp_id"=>'sp,'.$id],$conn);
			}

			// Send message to client phone  
			
			// send_sms($cont,"Thank You ".$name." to Join Us as Beautician");
			
			$_SESSION['t']  = 1;
			$_SESSION['tmsg']  = "Beautician Updated Successfully";
			header('LOCATION:beauticians.php?id='.$id);
			exit();
		}
		elseif($_FILES["proof"]['name'] != '')
		{
			$profile = upload_image($_FILES["profile"]);
			if(!$profile[0])
			{
				$_SESSION['t']  = 2;
				$_SESSION['tmsg']  = $profile[1];
				header('LOCATION:beauticians.php?id='.$id);
			}
			$proof	 = upload_image($_FILES["proof"]);
			if(!$proof[0])
			{
				$_SESSION['t']  = 2;
				$_SESSION['tmsg']  = $proof[1];
				header('LOCATION:beauticians.php?id='.$id);
			}
			
			
			$col_inc = 0;
			$col_id_qry =  query_by_id("SELECT cal_color from beauticians where type=2 and branch_id='".$branch_id."' order by id desc limit 1",[],$conn);
			if($col_id_qry){
				$col_inc = ((int)$col_id_qry[0]['cal_color']) + 1;
				if($col_inc>COLOR_TOTAL){
					$col_inc = 0;
				}
			}
			
			query("UPDATE `beauticians` set `name`='$name',`serivce_commision`='$scom',`prod_commision`='$pcom',`dob`='$dob',`doj`='$doj',`gender`='$gender',`cont`='$cont',`email`='$email',`emergency_cont`='$econt',`emergency_person`='$eperson', `address`='$addr', `month_sal`='$month_sal',  `idproof`='$proof[1]',`cal_color`='$col_inc',`active`='0',`starttime`='$start',`endtime`='$end',`type`='2',`ser_pro_type_id`='$ser_pro_type_id' where id='$id' and active='0' and branch_id='".$branch_id."'",[],$conn);
			
			// check name in user table and update 
			if(isset($uid)){
				query("UPDATE `user` set `name`='$name' where id=:id and sp_id=:sp_id and branch_id='".$branch_id."'",["id"=>$uid, "sp_id"=>'sp,'.$id],$conn);
			}

			// send_sms($cont,"Thank You ".$name." to Join Us as Beautician");
			
			$_SESSION['t']  = 1;
			$_SESSION['tmsg']  = "Beautician Updated Successfully";
			header('LOCATION:beauticians.php?id='.$id);
			exit();
			
		}
		else
		{
			
			$col_inc = 0;
			$col_id_qry =  query_by_id("SELECT cal_color from beauticians where type=2 and branch_id='".$branch_id."' order by id desc limit 1",[],$conn);
			if($col_id_qry){
				$col_inc = ((int)$col_id_qry[0]['cal_color']) + 1;
				if($col_inc>COLOR_TOTAL){
					$col_inc = 0;
				}
			}
			
			query("UPDATE `beauticians` set `name`='$name',`serivce_commision`='$scom',`prod_commision`='$pcom',`dob`='$dob',`doj`='$doj',`gender`='$gender',`cont`='$cont',`email`='$email',`emergency_cont`='$econt',`emergency_person`='$eperson', `address`='$addr', `month_sal`='$month_sal',`cal_color`='$col_inc',`starttime`='$start',`endtime`='$end',`type`='2',`ser_pro_type_id`='$ser_pro_type_id' where id='$id' and active='0' and branch_id='".$branch_id."'",[],$conn);
			
			// check name in user table and update 
			if(isset($uid)){
				query("UPDATE `user` set `name`='$name' where id=:id and sp_id=:sp_id and branch_id='".$branch_id."'",["id"=>$uid, "sp_id"=>'sp,'.$id],$conn);
			}
			
			//send_sms($cont,"Thank You ".$name." to Join Us as Beautician");
			
			$_SESSION['t']  = 1;
			$_SESSION['tmsg']  = "Beautician Updated Successfully";
			header('LOCATION:beauticians.php?id='.$id);
			exit();
		}
		
	}
	if(isset($_GET['d'])){
	    if(DELETE_BUTTON_INACTIVE != 'true'){
    		$d = $_GET['d'];
    		query("update `beauticians` set active=1 where id=$d and branch_id='".$branch_id."'",[],$conn);
    		query("update `user` set active=1 where sp_id='sp,$d' and branch_id='".$branch_id."'",[],$conn);		
    		$_SESSION['t']  = 1;
    		$_SESSION['tmsg']  = "Beautician In-activated";
    		header('LOCATION:beauticians.php');
    		exit();
	    }
	}

	if(isset($_GET['activeid'])){
	    if(DELETE_BUTTON_INACTIVE != 'true'){
    		$d = $_GET['activeid'];
    		query("update `beauticians` set active=0 where id=$d and branch_id='".$branch_id."'",[],$conn);
    		query("update `user` set active=0 where sp_id='sp,$d' and branch_id='".$branch_id."'",[],$conn);		
    		$_SESSION['t']  = 1;
    		$_SESSION['tmsg']  = "Beautician Activated";
    		header('LOCATION:beauticians.php');
    		exit();
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
		<div class="row">			
			<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
				<div class="panel">
					<div class="panel-heading heading-with-btn">
						<h4 class="pull-left"><?php if(!isset($id)){ echo 'Add'; } else { echo 'Edit'; } ?> service provider</h4>
						<?php if(isset($id)) { ?>
						    <span data-toggle="modal" data-target="#provider_service_modal"><button type="button" class="btn btn-success pull-right"><span class="fa fa-plus mr-left-0" aria-hidden="true"></span>Select services <span id="s_services">(<?php echo $edit['services']!=''?count(explode(',',$edit['services'])):'0' ?>)</span></button></span>
						<?php } else { ?>
						    <button class="btn btn-success pull-right" style="visibility: hidden;">&nbsp;</button>
						<?php } ?>
						<?php if(isset($id)) { ?>
						    <span data-toggle="modal" data-target="#provider_off_days"><button type="button" class="btn btn-warning pull-right" style="margin-right: 20px;"><i class="fa fa-spin fa-cog" aria-hidden="true"></i>Off days setting</button></span>
						<?php } else { ?>
						    <button class="btn btn-success pull-right" style="visibility: hidden;">&nbsp;</button>
						<?php } ?>
						<div class="clearfix"></div>
					</div>
					<div class="panel-body">
						<div class="row">
							<form action="" method="post" autocomplete="off" enctype="multipart/form-data">
								<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
									<div class="form-group">
										<label for="name">Enter name <span class="text-danger">*</span></label>
										<input type="text" class="form-control" name="Name" value="<?= isset($id)?$edit['name']:old('name')?>" id="Name" placeholder="Service provider name" required>
									</div>
								</div>
								<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
									<div class="form-group">
										<label for="scom">Service commission % 
											<?php if(!isset($id)){ ?>
												<i data-toggle="tooltip" data-placement="top" data-title="Advance service commission setting will available on service provider edit page." class="fa mr-left-0 fa-info-circle" style="font-size: 14px;cursor: pointer;" aria-hidden="true"></i>
											<?php } ?>
										</label>
										<input type="number" onkeyup="maxcommissionper($(this), this.value);" onchange="maxcommissionper($(this), this.value);" class="form-control" step="0.01" name="scom" value="<?= isset($id)?$edit['serivce_commision']:old('scom')?>" id="scom" placeholder="Service commission" min="0" max="100">
										<?php if(isset($id) && isset($uid)){ ?>
											<u class="text-warning"><em><i class="fa fa-cog fa-spin mr-left-0" aria-hidden="true"></i> <a href="javascript:void(0)" data-toggle="modal" data-target="#advance_service_commission">Advance service commission setting</a></em></u>
										<?php } else if(isset($id) && !isset($uid)){
											echo "<u class='text-warning'><em><i class='fa fa-user mr-left-0' aria-hidden='true'></i><a href='#newaccount'>Create account for advance settings</a></em></u>";
										} ?>
									</div>
								</div>
								<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
									<div class="form-group">
										<label for="pcom">Product commission % 
											<?php if(!isset($id)){ ?>
												<i data-toggle="tooltip" data-placement="top" data-title="Advance product commission setting will available on service provider edit page." class="fa mr-left-0 fa-info-circle" style="font-size: 14px;cursor: pointer;" aria-hidden="true"></i>
											<?php } ?>
										</label>
										<input type="number" onkeyup="maxcommissionper($(this), this.value);" onchange="maxcommissionper($(this), this.value);" class="form-control" step="0.01" name="pcom" value="<?= isset($id)?$edit['prod_commision']:old('pcom')?>" id="pcom" placeholder="Product commision" min="0" max="100">
										<?php if(isset($id) && isset($uid)){ ?>
											<u class="text-warning"><em><i class="fa fa-cog fa-spin mr-left-0" aria-hidden="true"></i> <a href="javascript:void(0)" data-toggle="modal" data-target="#advance_product_commission">Advance product commission setting</a></em></u>
										<?php } else if(isset($id) && !isset($uid)){
											echo "<u class='text-warning'><em><i class='fa fa-user mr-left-0' aria-hidden='true'></i><a href='#newaccount'>Create account for advance settings</a></em></u>";
										} ?>
									</div>
								</div>
								<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
									<div class="form-group">
										<label for="dob">Date of birth</label>
										<input type="text" class="form-control dob_annv_date" name="dob" value="<?= isset($id)?$edit['dob']:old('dob')?>" id="dob" readonly>
									</div>
								</div>
								
								<div class="clearfix"></div>
								<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
									<div class="form-group">
										<label for="working-hours">Working hours <span class="text-danger">*</span></label>
										<div class="clearfix"></div>
										<div class="row">
											<?php $start_time = shopopentime(); ?>
											<div class="col-lg-6 col-md-6 col-sm-3 col-xs-12">
												<input type="text" class="form-control time" name="start" value="<?= isset($id)?date('h:i A',strtotime($edit['starttime'])):date('h:i A',strtotime($start_time))?>" placeholder="Start time" required readonly>
											</div>
											<?php $end_time = shopclosetime(); ?>
											<div class="col-lg-6 col-md-6 col-sm-3 col-xs-12">
											<input type="text" class="form-control time" name="end" value="<?= isset($id)?date('h:i A',strtotime($edit['endtime'])):date('h:i A',strtotime($end_time))?>" placeholder="End time" required readonly></div>
										</div>
									</div>
								</div>
								<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
									<div class="form-group">
										<label for="month_sal">Monthly salary <span class="text-danger">*</span></label>
										<input type="number" class="form-control" name="month_sal" value="<?= isset($id)?$edit['month_sal']:old('month_sal')?>" id="month_sal" placeholder="Monthly salary" min="0" required>
									</div>
								</div>
								<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
									<div class="form-group">
										<label for="ser_pro_type">Service provider type <span class="text-danger">*</span></label>
										<input type="text" required class="form-control" name="ser_pro_type" value="<?= isset($id)?$edit['ser_pro_type']:old('ser_pro_type')?>" id="ser_pro_type" placeholder="Hair dresser" >
										<input type="hidden" class="form-control" name="ser_pro_type_id" value="<?= isset($id)?$edit['ser_pro_type_id']:old('ser_pro_type_id')?>" id="ser_pro_type_id">	
									</div>
								</div>
								
								<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
									<div class="form-group">
										<label for="cont">Contact number <span class="text-danger">*</span></label>
										<input type="text" class="form-control" name="cont" value="<?= isset($id)?$edit['cont']:old('cont')?>" maxlength="<?= PHONE_NUMBER ?>" onBlur="checkcont();contact_no_length($(this), this.value);" id="bcont" placeholder="Contact number" required>
										<span id="bcont-status" class="text-danger"></span>
										<span style="color:red" id="digit_error"></span>
									</div>
								</div>
								
								
								
								
								<div class="clearfix"></div>
								
								<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
									<div class="form-group">
										<label for="email">Email address</label>
										<input autocomplete="off" type="email" class="form-control" name="email" value="<?= isset($id)?$edit['email']:old('email')?>" id="email" placeholder="Email" >
									</div>
								</div>
								<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
									<div class="form-group">
										<label for="econt">Emergency contact number</label>
										<input type="text" class="form-control" onblur="othercontact($(this), this.value);" name="econt" value="<?= isset($id)?$edit['emergency_cont']:old('econt')?>" maxlength="<?= PHONE_NUMBER ?>" id="econt" placeholder="Emergency contact" >
									    <span style="color:red" class="conterror"></span>
									</div>
								</div>
								<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
									<div class="form-group">
										<label for="eperson">Emergency contact person</label>
										<input type="text" class="form-control" onblur="othercontact($(this), this.value);" name="eperson" value="<?= isset($id)?$edit['emergency_person']:old('eperson')?>" id="eperson" placeholder="Emergency contact person" >
									    <span style="color:red" class="conterror"></span>
									</div>
								</div>
								<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
									<div class="form-group">
										<label for="addr">Address</label>
										<input type="text" class="form-control" name="addr" value="<?= isset($id)?$edit['address']:old('addr')?>" id="addr" placeholder="Address" >
									</div>
								</div>
								<div class="clearfix"></div>
								<?php if(!isset($id)){ ?>
								<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
									<div class="form-group">
										<label for="userName">Username</label>
										<input autocomplete="off" type="text" class="form-control" onBlur="checkuser()" name="userName" id="userName" placeholder="Username" value="">
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
								<?php if(!isset($id)){ ?> <div class="clearfix"></div> <?php } ?>
								<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
									<div class="form-group">
										<label for="dob">Date of joining <span class="text-danger">*</span></label>
										<input type="text" class="form-control urdate" name="doj" value="<?= isset($id)?$edit['doj']:date('Y-m-d')?>" id="doj" required readonly>
									</div>
								</div>
								<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
									<div class="form-group">
										<label for="proof">Upload ID proof</label>
										<input type="file" class="form-control" name="proof" id="proof" >
										<?php if(isset($id)){ 
											if(strpos($edit['idproof'], 'upload') !== false){ ?>
											<a href="<?= $edit['idproof'] ?>" data-lightbox="<?= $edit['id'] ?>">
											<img src="<?= $edit['idproof'] ?>" class="img-responsive edit-avt-img img-thumbnail" />
											</a>
										<?php }
											} 
										?>
									</div>
								</div>
								
								<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
									<div class="form-group">
										<label for="profile">Upload photo</label>
										<input type="file" class="form-control" name="profile"  id="profile">
										<?php if(strpos($edit['photo'], 'upload') !== false){ ?>
											<img src="<?= $edit['photo'] ?>" class="img-responsive edit-avt-img img-thumbnail" />
										<?php } else { 
											if($edit['gender'] == '1'){ ?>
												<img src="img/avatar/male.png" class="img-responsive edit-avt-img img-thumbnail" />
											<?php } else if($edit['gender'] == '2'){ ?>
												<img src="img/avatar/female.png" class="img-responsive edit-avt-img img-thumbnail" />
											<?php }
										}
										?>
									</div>
								</div>
								<div class="clearfix"></div>
								<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
									<div class="form-group">
										<label>  </label><br>
										<?php if(isset($id)){ ?>
											<input type="hidden" name="eid" id="eid" value="<?=$id?>">
											<input type="hidden" name="uid" value="<?=$uid['id']?>">
											<a href="beauticians.php">
												<button type="button" class="btn btn-danger pull-right mr-left-5"><i class="fa fa-times" aria-hidden="true"></i>Cancel</button>
											</a>
											<button type="submit" name="edit-submit" class="btn btn-info pull-right "><i class="fa fa-pencil-square-o" aria-hidden="true"></i>Update service provider</button>
											<?php }else{ ?>
											<button type="submit" name="submit" class="btn btn-success pull-right"><i class="fa fa-plus" aria-hidden="true"></i>Add new service provider</button>
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
											<input type="text" class="form-control" readonly disabled name="username" value="<?= isset($id)?$uid['username']:old('name')?>" id="username" placeholder="Service provider name">
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
											<label for="ucpassword">Confirm password<span class="text-danger">*</span></label>
											<input required type="password" class="form-control" name="ucpassword" id="ucpassword" placeholder="Confirm password">
										</div>
									</div>
									<div class="clearfix"></div>
									<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
										<input type="hidden" name="user_id" value="<?= $uid['id'] ?>">
										<input type="hidden" name="sp_user_id" value="<?= $edit['id'] ?>">
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
									<div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
										<div class="form-group">
											<label for="newuserName">Username <span class="text-danger">*</span></label>
											<input type="text" class="form-control" onBlur="checkuser()" name="newuserName" id="userName" placeholder="Username" value="" required>
											<span id="user-status" class="text-danger"></span>				
										</div>
									</div>
									<div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
										<div class="form-group">
											<label for="newpassword">Password <span class="text-danger">*</span></label>
											<input type="password" onBlur="checklength()" class="form-control" name="newpassword" id="password" placeholder="Password" required>
											<span id="pass-status" class="text-danger"></span>		
										</div>
									</div>
									<div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
										<div class="form-group">
											<label for="newconfirm-password">Confirm password <span class="text-danger">*</span></label>
											<input type="password" class="form-control" name="newconfirm_password" id="confirm_password" placeholder="Confirm password" required>
										</div>
									</div>
									<div class="clearfix"></div>
									<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
										<input type="hidden" name="sp_user_id" value="<?= $edit['id'] ?>">
										<input type="hidden" name="accountname" value="<?= $edit['name'] ?>">
										<button type="submit" name="add-account" class="btn btn-success pull-right "><i class="fa fa-plus" aria-hidden="true"></i>Create account</button>
									</div>
								</form>
							</div>
						</div>
					</div>
				<?php } ?>

				<div class="panel">
					<div class="panel-heading heading-with-btn">
						<h4 class="pull-left">Manage active service provider(s)</h4>
						<span id="download-btn"></span>
						<div class="clearfix"></div>
					</div>
					<div class="panel-body">
						<div class="row">
							<div class="col-lg-12">
								<div class="table-responsive">
									<table class="table table-bordered grid no-margin">
										<thead>
											<tr>
												<th class="not-export-column">Profile image</th>
												<th>Name</th>
												<th>Contact number</th>
												<th>Emergency contact number</th>
												<th>Emergency contact person</th>
												<th>Last 30 days service commission</th>
												<th>Last 30 days product commission</th>
												<th>Attendence id</th>
												<th>Action</th>
											</tr>
										</thead>
										<tbody>
											<?php
												$sql1="SELECT * from beauticians where active=0 and type=2 and branch_id='".$branch_id."' order by id DESC";
												$result1=query_by_id($sql1,[],$conn);
												foreach ($result1 as $row1) {
												$userid = query_by_id("SELECT id, username from user where sp_id=:id and active=0 and branch_id='".$branch_id."'",["id"=>'sp,'.$row1['id']],$conn)[0];
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
															<button disabled type="button" class="btn btn-danger btn-xs proof-btn"><i class="fa fa-eye-slash" aria-hidden="true"></i> No id proof</button>
														<?php } ?>
													</td>
													<td>
														<?php echo $row1['name']; ?>
													</td>
													<td><?php echo $row1['cont']; ?></td>
													<td><?php echo $row1['emergency_cont']; ?></td>
													<td><?php echo $row1['emergency_person']; ?></td>
													<td><?php echo number_format(getservicecom($row1['id']),2); ?></td>
													<td><?php echo number_format(getprodcom($row1['id']),2); ?></td>
													<td><?php echo str_pad(1, 4, '0', STR_PAD_RIGHT)+$row1['id']; ?></td>
													<td class="multi-action-btn">
													<?php if(!check_sp_transfer_status($row1['id'])){ ?>
														<a href="beauticians.php?id=<?php echo $row1['id']; ?>"> <button class="btn btn-warning btn-xs" type="button"><i class="fa fa-pencil-square-o" aria-hidden="true"></i>Edit</button></a>
														<a href="commision.php?pid=<?php echo $row1['id']; ?>"> <button class="btn btn-primary btn-xs" type="button"><i class="fa fa-money" aria-hidden="true"></i>Commission</button></a>
														<?php if(DELETE_BUTTON_INACTIVE == 'true'){ ?>
														    <a href="#" onclick="return deleteDisabled();"> <button class="btn btn-danger btn-xs" type="button"><i class="icon-delete"></i>Inactive</button></a>
														<?php } else { ?>
														    <a href="beauticians.php?d=<?php echo $row1['id']; ?>" onclick="return confirm('Are you sure?');"> <button class="btn btn-danger btn-xs" type="button"><i class="icon-delete"></i>Inactive</button></a>
														<?php } ?>
														<?php if(!isset($userid)) { ?>
														<a href="beauticians.php?id=<?php echo $row1['id']; ?>#newaccount"> <button class="btn btn-success btn-xs" type="button"><i class="fa fa-plus" aria-hidden="true"></i>Create account</button></a>
														<?php } ?>
													<?php } else { ?>
														<button type="button" class="btn btn-success btn-xs">Transferred</button>
													<?php } ?>
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

				<div class="panel">
					<div class="panel-heading heading-with-btn">
						<h4 class="pull-left">Manage In-active service provider(s)</h4>
						<span id="download-btn"></span>
						<div class="clearfix"></div>
					</div>
					<div class="panel-body">
						<div class="row">
							<div class="col-lg-12">
								<div class="table-responsive">
									<table class="table table-bordered grid no-margin">
										<thead>
											<tr>
												<th class="not-export-column">Profile image</th>
												<th>Name</th>
												<th>Contact number</th>
												<th>Emergency contact number</th>
												<th>Emergency contact person</th>
												<th>Last 30 days service commission</th>
												<th>Last 30 days product commission</th>
												<th>Attendence id</th>
												<th>Action</th>
											</tr>
										</thead>
										<tbody>
											<?php
												$sql1="SELECT * from beauticians where active=1 and type=2 and branch_id='".$branch_id."' order by id DESC";
												$result1=query_by_id($sql1,[],$conn);
												foreach ($result1 as $row1) {
												$userid = query_by_id("SELECT id, username from user where sp_id=:id and active=1 and branch_id='".$branch_id."'",["id"=>'sp,'.$row1['id']],$conn)[0];
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
															<button disabled type="button" class="btn btn-danger btn-xs proof-btn"><i class="fa fa-eye-slash" aria-hidden="true"></i> No id proof</button>
														<?php } ?>
													</td>
													<td>
														<?php echo $row1['name']; ?>
													</td>
													<td><?php echo $row1['cont']; ?></td>
													<td><?php echo $row1['emergency_cont']; ?></td>
													<td><?php echo $row1['emergency_person']; ?></td>
													<td><?php echo number_format(getservicecom($row1['id']),2); ?></td>
													<td><?php echo number_format(getprodcom($row1['id']),2); ?></td>
													<td><?php echo str_pad(1, 4, '0', STR_PAD_RIGHT)+$row1['id']; ?></td>
													<td class="multi-action-btn">
													<?php if(!check_sp_transfer_status($row1['id'])){ ?>
														<a href="beauticians.php?activeid=<?php echo $row1['id']; ?>"> <button class="btn btn-success btn-xs" type="button"><i class="fa fa-check-square-o" aria-hidden="true"></i>Active</button></a>
													<?php } ?>
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
		</div>
		
	</div>
	
</div>

</div>


<?php if(isset($id) && isset($uid)) { ?>
<!-- Modal to add advance service commissions start -->
<div id="advance_service_commission" class="modal fade disableOutsideClick" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <!-- <button type="button" class="close" data-dismiss="modal">&times;</button> -->
        <h4 class="modal-title">Advance service commission setting % </h4>
      </div>
      <div class="modal-body">
		<div class="table-responsive">
			<table id="myTable" class="table table-bordered adv-service-comm-table">
				<thead>
					<tr>
						<th style="width:3%"></th>
						<th style="width:20%">From</th>
						<th style="width:20%">To</th>
						<th style="width:10%">Commission % </th>
					</tr>
				</thead>
				<tbody>
					<?php 
						$query = query_by_id("SELECT * from service_provider_advance_commission_setting where sp_id=:sp_id and uid=:uid and commission_type = 1 and status=0 and branch_id='".$branch_id."'",["sp_id"=>$id, "uid"=>$uid['id']],$conn);
						if($query){
							$count = 1;
							foreach ($query as $key=>$row) {
								if($count == 1){ ?>
									<tr id="TextBoxContainer" class="TextBoxContainer service_row_1" data-row="service_row_1">
										<td class="sno" style="vertical-align: middle;">
											<span class="remm icon-trash2 " style="color:red;" onclick="$(this).parent().parent().remove();sumup();"></span>
										</td>
										<td>
											<input type="number" class="form-control sal" id="fromSale" name="fromsale[]" value="<?= $row['from_price'] ?>" min='0' placeholder="10000" required>
										</td>
										<td>
											<input type="number" placeholder="100000" class="form-control sal" id="toSale" name="tosale[]" value="<?= $row['to_price'] ?>" min='0' required>
										</td>
										<td>
											<input type="number" placeholder="10%" class="form-control sal" id="commission" name="commission[]" value="<?= $row['commission_rate'] ?>" min='0' required>
										</td>
										<input type="hidden" name="id" id="id" value="<?= $row['id'] ?>">
									</tr>
								<?php } else { ?>
									<tr class="TextBoxContainer service_row_<?= $count ?>" data-row="service_row_<?= $count ?>">
										<td class="sno" style="vertical-align: middle;">
											<span class="remm icon-trash2 " style="color:red;" onclick="$(this).parent().parent().remove();sumup();"></span>
										</td>
										<td>
											<input type="number" class="form-control sal" id="fromSale" name="fromsale[]" value="<?= $row['from_price'] ?>" min='0' placeholder="10000" required>
										</td>
										<td>
											<input type="number" placeholder="100000" class="form-control sal" id="toSale" name="tosale[]" value="<?= $row['to_price'] ?>" min='0' required>
										</td>
										<td>
											<input type="number" placeholder="10%" class="form-control sal" id="commission" name="commission[]" value="<?= $row['commission_rate'] ?>" min='0' required>
										</td>
										<input type="hidden" name="id" id="id" value="<?= $row['id'] ?>">
									</tr>
								<?php }
								$count += 1;
							}
						} else { ?>
							<tr id="TextBoxContainer" class="TextBoxContainer service_row_1" data-row="service_row_1">
								<td class="sno" style="vertical-align: middle;">
									<span class="icon-dots-three-vertical"></span>
								</td>
								<td>
									<input type="number" class="form-control sal" id="fromSale" name="fromsale[]" value="" min='0' placeholder="10000" required>
								</td>
								<td>
									<input type="number" placeholder="100000" class="form-control sal" id="toSale" name="tosale[]" value="" min='0' required>
								</td>
								<td>
									<input type="number" placeholder="10%" class="form-control sal" id="commission" name="commission[]" value="" min='0' required>
								</td>
							</tr>

						<?php }
					?>				
					
					<tr id="addBefore">
						<td colspan="5">
							<button type="button" id="btnAdd" class="btn btn-warning btn-xs pull-right"><i class="fa fa-plus mr-right-0" aria-hidden="true"></i></button>
						</td>					
					</tr>
				</tbody>
			</table>
		</div>
		<div class="notes">
			<p><i class="fa fa-hand-o-right" aria-hidden="true"></i>Important notes:</p>
			<ol>
				<li>* All fields are required.</li>
				<li>* From price of Next rows must be 1 number greater then the previous row.<br /><strong>
					Row1 => From: 0.00 - To: 100000.00<br />
					Row2 => From: 100001.00 To: 1500000.00</strong>
				</li>
			</ol>
		</div>
      </div>
      <div class="modal-footer">
		<input type="hidden" name="servicecommission" id="servicecommission" value="1">
      	<button type="button" data-attr="submit" class="btn btn-success" onclick="advanceServiceCommission()"><i class="fa fa-floppy-o" aria-hidden="true"></i> Save</button>
        <button type="button" class="btn btn-danger" data-dismiss="modal" onclick="emptyAdvComm('adv-service-comm-table','TextBoxContainer')"><i class="fa fa-times" aria-hidden="true"></i>Close</button>
      </div>
    </div>

  </div>
</div>
<!-- Modal to add advance service commissions end -->

<!-- Modal to add advance product commissions start -->
<div id="advance_product_commission" class="modal fade disableOutsideClick" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <!-- <button type="button" class="close" data-dismiss="modal">&times;</button> -->
        <h4 class="modal-title">Advance product commission setting % </h4>
      </div>
      <div class="modal-body">
      	<div class="table-responsive">
			<table id="myTable" class="table table-bordered adv-product-comm-table">
				<thead>
					<tr>
						<th style="width:3%"></th>
						<th style="width:20%">From</th>
						<th style="width:20%">To</th>
						<th style="width:10%">Commission % </th>
					</tr>
				</thead>
				<tbody>
					<?php 
						$query = query_by_id("SELECT * from service_provider_advance_commission_setting where sp_id=:sp_id and uid=:uid and commission_type = 2 and status=0 and branch_id='".$branch_id."'",["sp_id"=>$id, "uid"=>$uid['id']],$conn);
						if($query){
							$countpro = 1;
							foreach ($query as $key=>$row) {
								if($countpro == 1){ ?>
									<tr id="TextBoxContainer2" class="TextBoxContainer2 product_row_1" data-row="product_row_1">
										<td class="sno2" style="vertical-align: middle;">
											<span class="remm icon-trash2 " style="color:red;" onclick="$(this).parent().parent().remove();sumup();"></span>
										</td>
										<td>
											<input type="number" class="form-control sal" id="profromSale" name="profromsale[]" value="<?= $row['from_price'] ?>" min='0' placeholder="10000" required>
										</td>
										<td>
											<input type="number" placeholder="100000" class="form-control sal" id="protoSale" name="protosale[]" value="<?= $row['to_price'] ?>" min='0' required>
										</td>
										<td>
											<input type="number" placeholder="10%" class="form-control sal" id="procommission" name="procommission[]" value="<?= $row['commission_rate'] ?>" min='0' required>
										</td>
										<input type="hidden" name="proid" id="proid" value="<?= $row['id'] ?>">
									</tr>
								<?php } else { ?>
									<tr id="TextBoxContainer2" class="TextBoxContainer2 product_row_<?= $countpro ?>" data-row="product_row_<?= $countpro ?>">
										<td class="sno2" style="vertical-align: middle;">
											<span class="remm icon-trash2 " style="color:red;" onclick="$(this).parent().parent().remove();sumup();"></span>
										</td>
										<td>
											<input type="number" class="form-control sal" id="profromSale" name="profromsale[]" value="<?= $row['from_price'] ?>" min='0' placeholder="10000" required>
										</td>
										<td>
											<input type="number" placeholder="100000" class="form-control sal" id="protoSale" name="protosale[]" value="<?= $row['to_price'] ?>" min='0' required>
										</td>
										<td>
											<input type="number" placeholder="10%" class="form-control sal" id="procommission" name="procommission[]" value="<?= $row['commission_rate'] ?>" min='0' required>
										</td>
										<input type="hidden" name="proid" id="proid" value="<?= $row['id'] ?>">
									</tr>
									<?php }
								$countpro += 1;
							}
						} else { ?>
							<tr id="TextBoxContainer2" class="TextBoxContainer2 product_row_1" data-row="product_row_1">
								<td class="sno2" style="vertical-align: middle;">
									<span class="icon-dots-three-vertical"></span>
								</td>
								<td>
									<input type="number" class="form-control sal" id="profromSale" name="profromsale[]" value="" min='0' placeholder="10000" required>
								</td>
								<td>
									<input type="number" placeholder="100000" class="form-control sal" id="protoSale" name="protosale[]" value="" min='0' required>
								</td>
								<td>
									<input type="number" placeholder="10%" class="form-control sal" id="procommission" name="procommission[]" value="" min='0' required>
								</td>
							</tr>
						<?php } ?>
					<tr id="addBefore2">
						<td colspan="5">
							<button type="button" id="btnAdd2" class="btn btn-warning btn-xs pull-right"><i class="fa fa-plus mr-right-0" aria-hidden="true"></i></button>
						</td>					
					</tr>
				</tbody>
			</table>
		</div>
		<div class="notes">
			<p><i class="fa fa-hand-o-right" aria-hidden="true"></i>Important notes:</p>
			<ol>
				<li>* All fields are required.</li>
				<li>* From price of Next rows must be 1 number greater then the previous row.<br /><strong>
					Row1 => From: 0.00 - To: 100000.00<br />
					Row2 => From: 100001.00 To: 1500000.00</strong>
				</li>
			</ol>
		</div>
      </div>
      <div class="modal-footer">
		<input type="hidden" name="productcommission" id="productcommission" value="2">
      	<button type="button" data-attr="submit" class="btn btn-success" onclick="advanceProductCommission()"><i class="fa fa-floppy-o" aria-hidden="true"></i> Save</button>
        <button type="button" class="btn btn-danger" data-dismiss="modal" onclick="emptyAdvComm('adv-product-comm-table','TextBoxContainer2')"><i class="fa fa-times" aria-hidden="true"></i>Close</button>
      </div>
    </div>

  </div>
</div>
<input type="hidden" name="userid" id="userid" value="<?= $uid['id']; ?>">
<input type="hidden" name="providerid" id="providerid" value="<?= $id;  ?>">
<!-- Modal to add advance product commissions end -->
<?php } ?>

<?php if(isset($id)){ ?>
<!-- Modal to add service start -->
<div id="provider_service_modal" class="modal fade disableOutsideClick" role="dialog">
  <div class="modal-dialog modal-lg">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <!-- <button type="button" class="close" data-dismiss="modal">&times;</button> -->
        <h4 class="modal-title">Select services</h4>
      </div>
      <div class="modal-body">
      	<div class="row">
		    <?php
		        $query = query_by_id("SELECT * FROM service WHERE active = '0' ORDER BY name ASC ",[],$conn);
		        if($query){
		        	if(count($query) > 0){
		        		echo '<div class="col-lg-12"><label><input type="checkbox" id="checkAll"> Select All</label></div><br />';
		        	}
		            foreach($query as $res){
		                $checked_services = explode(',',$edit['services']);
		                ?>
		                    <div class="col-lg-3 col-md-3 col-sm-4 col-xs-2">
		                        <label>
		                            <input type="checkbox" <?php echo in_array($res[id], $checked_services)?'checked':'' ?> class="provider_service" value="<?php echo $res['id']; ?>" /> <?php echo ucfirst(strtolower($res['name'])); ?>
		                        </label>
		                    </div>
		                <?php
		            }
		        }
		    ?>
		</div>
      </div>
      <div class="modal-footer">
      	<button type="button" data-attr="submit" class="btn btn-success" onclick="save_provider_service()" id="save_service_btn"><i class="fa fa-floppy-o" aria-hidden="true"></i> Save</button>
        <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times" aria-hidden="true"></i>Close</button>
      </div>
    </div>

  </div>
</div>
<?php } ?>

<!-- Modal to add service end -->

<?php if(isset($id)){ ?>
<!-- Modal to add off days start -->
<div id="provider_off_days" class="modal fade disableOutsideClick" role="dialog">
  <div class="modal-dialog modal-lg">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <!-- <button type="button" class="close" data-dismiss="modal">&times;</button> -->
        <h4 class="modal-title">Off days setting</h4>
      </div>
      <div class="modal-body">
      	<div class="row">
		  <div class="col-lg-3">
		  <h5>Week days</h5>
			<?php 
				$query = query_by_id("SELECT * FROM sp_week_off_days WHERE emp_id = '".$id."' ",[],$conn);
				//print_r($query);
				$checked_off_days = explode(',',$query[0]['week_day']);
				$days = array("Monday", "Tuesday", "Wednesday","Thursday","Friday","Saturday","Sunday");
				echo "<ul>";
				foreach($days as $d){
				?>
					<li><label>
						<input type="checkbox" name="day" value="<?php echo $d;?>" class="day" <?php echo in_array($d, $checked_off_days)?'checked':'' ?> onclick="save_off_days()"/> &nbsp<?php echo $d;?>
					</label></li>
				<?php } ?>
				<ul>
		   </div>
		   <div class="col-lg-9" style="border-left: 1px solid #ccc;">
		   	<h5>Holidays</h5>
		   	<div class="row">
		   		<div class="form-group col-md-2">
			   		<label style="margin-top: 10px;">Select dates</label>
			   	</div>
			   	<div class="form-group col-lg-5">
					<input type="text" class="form-control" name="daterange" value="Select date" id="off_date"/>
				</div>
				<div class="form-group col-md-5">
					<button type="button" data-attr="submit" class="btn btn-success" id="save_off_days_btn" onclick="save_off_dates()"><i class="fa fa-floppy-o" aria-hidden="true"></i> Save</button>
				</div>
		   	</div>
		   	<div class="row">
		   		<div class="col-md-12 table-responsive">
		   			<table class="table table-bordered table-stripped grid">
		   				<thead>
		   					<tr>
		   						<th>#</th>
		   						<th>Date</th>
		   						<th>Action</th>
		   					</tr>
		   				</thead>
		   				<tbody>
			   			<?php			   			
			   				$leaves = query_by_id("SELECT * FROM sp_holiday WHERE emp_id='".$id."' AND status='1' ORDER BY id DESC",[],$conn);
			   				if($leaves){
			   					$count = 1;
			   					foreach($leaves as $leave){
			   						?>
			   						<tr>
			   							<td><?= $count ?></td>
			   							<td><?= my_date_format($leave['off_dates']) ?></td>
			   							<td><button onclick="removeHoliday(<?= $leave['id'] ?>)" type="button" class="btn btn-xs btn-danger"><span style="font-size: 16px;" class="icon-delete"></span> Delete</td>
			   						</tr>
			   						<?php
			   						$count++;
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
      <div class="modal-footer">
      	
        <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times" aria-hidden="true"></i>Close</button>
      </div>
    </div>

  </div>
</div>

<?php } ?>

<!-- Modal to add off days end -->

<script>

	// function for service commission modal inserting more fields
	<?php if(isset($count)) { ?>
		var service_row = <?= $count-1 ?>;
	<?php } else { ?>
		var service_row = 1;
	<?php } ?>

	$("#btnAdd").bind("click", function() {
		service_row += 1;
		var clonetr = $("#TextBoxContainer").clone().addClass('TextBoxContainer service_row_'+service_row);
		clonetr.removeAttr('id');
		clonetr.removeClass('service_row_1');
		clonetr.attr('data-row','service_row_'+service_row);
		clonetr.find("table.add_row").remove();
		clonetr.find('.sno').html('<span class="remm icon-trash2 " style="color:red;" onclick="$(this).parent().parent().remove();sumup();"></span>');
		clonetr.find('input').val('');
		// clonetr.find('.staff option[value=""]').prop('selected',true);
		$("#addBefore").before(clonetr);
		$('.service_row_'+service_row+' input').removeClass('invalid');
	});

	// function for product commission modal inserting more fields

	<?php if(isset($countpro)) { ?>
		var product_row = <?= $countpro-1 ?>;
	<?php } else { ?>
		var product_row = 1;
	<?php } ?>

	$("#btnAdd2").bind("click", function() {
		product_row += 1;
		var clonetr = $("#TextBoxContainer2").clone().addClass('TextBoxContainer2 product_row_'+product_row);
		clonetr.removeAttr('id');
		clonetr.removeClass('product_row_1');
		clonetr.attr('data-row','product_row_'+product_row);
		clonetr.find("table.add_row").remove();
		clonetr.find('.sno2').html('<span class="remm icon-trash2 " style="color:red;" onclick="$(this).parent().parent().remove();sumup();"></span>');
		clonetr.find('input').val('');
		// clonetr.find('.staff option[value=""]').prop('selected',true);
		$("#addBefore2").before(clonetr);
		$('.product_row_'+product_row+' input').removeClass('invalid');
	});

	// function to insert service commission 
	function advanceServiceCommission(){
		var row_no;
		var uid = $('#userid').val();
		var pr_id = $('#providerid').val();
		var submit_btn = $('#advance_service_commission button[data-attr=submit]');
		var submit_btn_icon = $('#advance_service_commission .btn-success i');
		var comm_data = [];
		$('.adv-service-comm-table  tbody   tr.TextBoxContainer').each(function() {
			var row_no = $(this).attr('data-row');
			var commission_type = $('#servicecommission').val();
			var fromsale = $('.'+row_no+' #fromSale').val();
			var tosale = $('.'+row_no+' #toSale').val();
			var commission_rate = $('.'+row_no+' #commission').val();
			var id = $('.'+row_no+' #id').val();
			var row_data = [id, fromsale, tosale, commission_rate, commission_type];
			if(fromsale == '' || tosale == '' || commission_rate == ''){
				if(fromsale == ''){
					$('.'+row_no+' #fromSale').addClass('invalid');
				} else {
					$('.'+row_no+' #fromSale').removeClass('invalid');
				}
				if(tosale == ''){
					$('.'+row_no+' #toSale').addClass('invalid');
				} else {
					$('.'+row_no+' #toSale').removeClass('invalid');
				}
				if(commission_rate == ''){
					$('.'+row_no+' #commission').addClass('invalid');
				} else {
					$('.'+row_no+' #commission').removeClass('invalid');
				}				
			} else {
				$('.'+row_no+' #fromSale').removeClass('invalid');
				$('.'+row_no+' #toSale').removeClass('invalid');
				$('.'+row_no+' #commission').removeClass('invalid');
				comm_data.push(row_data);
			}
		});

		if($('.adv-service-comm-table input').hasClass('invalid')){
			// alert('field error');
		} else {
			$.ajax({  
	            url:"ajax/advance_commission_setting.php",
	            method:"POST",
	            data: JSON.stringify({uid:uid, pr_id:pr_id, comm_data:comm_data, 'module':'advsercomm'}),
	            contentType: false,
	            dataType: "json",
	            processData:false,
	            beforeSend: function() {
	            	submit_btn_icon.removeClass('fa-floppy-o');
	            	submit_btn_icon.addClass('fa-spinner fa-spin');
	            	submit_btn.prop('disabled',true);
				},
	            success:function(response){
	            	// console.log(response);
	                if (response.status == 0) {
	                	errordiv.text(response.message);
	                	submit_btn_icon.removeClass('fa-spinner fa-spin');
	            		submit_btn_icon.addClass('fa-floppy-o');
	                	submit_btn.prop('disabled',false);
	                }
	                else if(response.status == 1){
	                	location.reload(true);
	                }
	            }
	       });
		}
		// console.log(comm_data);
	}

	// function to insert service commission 
	function advanceProductCommission(){
		var row_no;
		var uid = $('#userid').val();
		var pr_id = $('#providerid').val();
		var submit_btn = $('#advance_product_commission button[data-attr=submit]');
		var submit_btn_icon = $('#advance_product_commission .btn-success i');
		var comm_data = [];
		$('.adv-product-comm-table  tbody   tr.TextBoxContainer2').each(function() {
			var row_no = $(this).attr('data-row');
			var commission_type = $('#productcommission').val();
			var fromsale = $('.'+row_no+' #profromSale').val();
			var tosale = $('.'+row_no+' #protoSale').val();
			var commission_rate = $('.'+row_no+' #procommission').val();
			var id = $('.'+row_no+' #proid').val();
			var row_data = [id, fromsale, tosale, commission_rate, commission_type];
			if(fromsale == '' || tosale == '' || commission_rate == ''){
				if(fromsale == ''){
					$('.'+row_no+' #profromSale').addClass('invalid');
				} else {
					$('.'+row_no+' #profromSale').removeClass('invalid');
				}
				if(tosale == ''){
					$('.'+row_no+' #protoSale').addClass('invalid');
				} else {
					$('.'+row_no+' #protoSale').removeClass('invalid');
				}
				if(commission_rate == ''){
					$('.'+row_no+' #procommission').addClass('invalid');
				} else {
					$('.'+row_no+' #procommission').removeClass('invalid');
				}				
			} else {
				$('.'+row_no+' #profromSale').removeClass('invalid');
				$('.'+row_no+' #protoSale').removeClass('invalid');
				$('.'+row_no+' #procommission').removeClass('invalid');
				comm_data.push(row_data);
			}
		});

		if($('.adv-product-comm-table input').hasClass('invalid')){
			// alert('field error');
		} else {
			$.ajax({  
	            url:"ajax/advance_commission_setting.php",
	            method:"POST",
	            data: JSON.stringify({uid:uid, pr_id:pr_id, comm_data:comm_data, 'module':'advprocomm'}),
	            contentType: false,
	            dataType: "json",
	            processData:false,
	            beforeSend: function() {
	            	submit_btn_icon.removeClass('fa-floppy-o');
	            	submit_btn_icon.addClass('fa-spinner fa-spin');
	            	submit_btn.prop('disabled',true);
				},
	            success:function(response){
	            	// console.log(response);
	                if (response.status == 0) {
	                	errordiv.text(response.message);
	                	submit_btn_icon.removeClass('fa-spinner fa-spin');
	            		submit_btn_icon.addClass('fa-floppy-o');
	                	submit_btn.prop('disabled',false);
	                }
	                else if(response.status == 1){
	                	location.reload(true);
	                }
	            }
	       });
		}
		// console.log(comm_data);
	}


	function emptyAdvComm(tableClass, rowId){
		<?php if(!isset($id)){ ?>
			$('.'+tableClass+' tr.'+rowId+':not(#'+rowId+')').remove();
			$('tr.'+rowId+' input').val('');
		<?php } ?>
	}

	function checkcont() {
		var cat = $('#bcont').val();
		<?php if(isset($id)) { ?>
			var id = $('#eid').val();
			var url = 'con='+cat+'&uid='+id;
		<?php } else { ?>
			var url = 'con='+cat;
		<?php } ?>
		jQuery.ajax({
			url: "checkcont.php?"+url,
			//data:'cat='+$("#bcont").val(),
			type: "POST",
			success:function(data){
				//alert(data);
				if ( data.indexOf("Already Exist") > -1 ){
					$("#bcont-status").html(data);
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
	
    // save provider services
    
    function save_provider_service(){
        var service = [];
        var provider_id = '<?= isset($id)?$id:'' ?>';
        $('.provider_service').each(function(){
            if($(this)[0].checked){
                service.push($(this).val());
            }
        });
        if(service.length == 0){
            toastr.warning('Please select min 1 service');
        } else {
            jQuery.ajax({
    			url: "ajax/service.php",
    			type: "POST",
    			dataType : 'JSON',
    			data : { action : 'services_list', services : service, provider_id : provider_id},
    			beforeSend : function(){
    			    $('#save_service_btn').html('<i class="fa fa-spinner fa-spin" aria-hidden="true"></i> Saving...');
    			},
    			success:function(response){
    			    	if(response.status == 1){
    			    	    toastr.success('Services saved successfully');
    			    	    $('#save_service_btn').html('<i class="fa fa-floppy-o" aria-hidden="true"></i> Save');
    			    	    $('#s_services').html('('+service.length+')');
    			    	} else if(response.status == 0) {
    			    	    toastr.error('Error occured, please save again');
    			    	}
    			},
    			error:function (){}
    		});
        }
    }
    
    function save_off_days(){
        var week_day = [];
        var provider_id = '<?= isset($id)?$id:'' ?>';
        $('.day').each(function(){
            if($(this)[0].checked){
                week_day.push($(this).val());
            }
        });
        
        jQuery.ajax({
			url: "ajax/off_days.php",
			type: "POST",
			dataType : 'JSON',
			data : { action : 'off_days_list', week_day : week_day, provider_id : provider_id},
	
			success:function(response){
			    	if(response.status == 1){
			    	    toastr.success('Saved successfully');
			    	} else if(response.status == 0) {
			    	    toastr.error('Error occured, please save again');
			    	}
			},
			error:function (){}
		});
        
    }

    function save_off_dates(){
        var off_dates = $("#off_date").val();
        var startDate= $("#off_date").data('daterangepicker').startDate.format('YYYY-MM-DD');
        var endDate=  $("#off_date").data('daterangepicker').endDate.format('YYYY-MM-DD');
        var provider_id = '<?= isset($id)?$id:'' ?>';
        console.log(startDate);
        console.log(endDate);

       jQuery.ajax({
			url: "ajax/off_days.php",
			type: "POST",
			dataType : 'JSON',
			data : { action : 'save_off_dates', startDate : startDate, endDate : endDate, provider_id : provider_id},
	
			success:function(response){
			    	if(response.status == 1){
			    	    toastr.success('Saved successfully');
			    	    window.location.reload();
			    	} else if(response.status == 0) {
			    	    toastr.error('Error occured, please save again');
			    	}
			},
			error:function (){}
		});
        
    }
    
    function removeHoliday(id){

       jQuery.ajax({
			url: "ajax/off_days.php",
			type: "POST",
			dataType : 'JSON',
			data : { action : 'delete_dates', id : id},
	
			success:function(response){
			    	if(response.status == 1){
			    	    toastr.success('Deleted successfully');
			    	    window.location.reload();
			    	} else if(response.status == 0) {
			    	    toastr.error('Error occured, please save again');
			    	}
			},
			error:function (){}
		});
    }
</script>
<?php 
	include "footer.php";
	
	if(!isset($_GET['id'])){
	    ?>
	    <script>
	        setTimeout(function(){
	           $('#userName, #password').val(''); 
	        },1000);
	    </script>
	    <?php
	}
	
	function getservicecom($bid){
		global $conn;
		global $branch_id;
		$sumFinal = 0;
		$sql = "SELECT ii.price as service_price, imsp.service_name, s.id as service_id, i.id as id, i.doa from `invoice_multi_service_provider` imsp "
				." LEFT JOIN `invoice_items_".$branch_id."` ii on ii.id=imsp.ii_id "
				." LEFT JOIN `invoice_".$branch_id."` i on i.id=ii.iid " 
				." LEFT JOIN `beauticians` b on b.id = imsp.service_provider"
				." LEFT JOIN `service` s on s.id = SUBSTRING_INDEX(ii.service,',',-1)"
				." WHERE imsp.service_provider='$bid' and i.active=0 and ii.type='Service' and imsp.branch_id='".$branch_id."' and i.doa BETWEEN NOW() - INTERVAL 30 DAY AND NOW()";
		$result=query_by_id($sql,[],$conn);
		if ($result){

			foreach($result as $res){
				$sum = 0;
				$provider_id = $bid;
				$service = $res['service_name'];
				$type = 'Service';
				$price = $res['service_price'];
				$total_sale = service_provider_total_sale($type, $provider_id, $res['id']);
				$commission_per = service_provider_commission($type, $provider_id, $total_sale);
				$com = $commission_per;
				$val = $price * $com / 100;
				$sum = $sum + $val;
				$pcount = provider_count($service,$res['id']);
				if($pcount > 0){
				    $sum = $sum/$pcount;
				} else {
				    $sum = $sum;
				}
				$sumFinal += $sum;
			}
		}
		return $sumFinal;
	}
	
	function getprodcom($bid){
		global $conn;
		global $branch_id;
		$sumFinal = 0;
		$sql = "SELECT ii.price as service_price, imsp.service_name, s.id as service_id, i.id as id, i.doa from `invoice_multi_service_provider` imsp "
				." LEFT JOIN `invoice_items_".$branch_id."` ii on ii.id=imsp.ii_id "
				." LEFT JOIN `invoice_".$branch_id."` i on i.id=ii.iid " 
				." LEFT JOIN `beauticians` b on b.id = imsp.service_provider"
				." LEFT JOIN `service` s on s.id = SUBSTRING_INDEX(ii.service,',',-1)"
				." WHERE imsp.service_provider='$bid' and i.active=0 and ii.type='Product' and imsp.branch_id='".$branch_id."' and i.doa BETWEEN NOW() - INTERVAL 30 DAY AND NOW()";
		$result = query_by_id($sql,[],$conn);
		if ($result){
			foreach($result as $res){
				$sum = 0;
				$provider_id = $bid;
				$service = $res['service_name'];
				$type = 'Product';
				$price = $res['service_price'];
				$total_sale = service_provider_total_sale($type, $provider_id, $res['id']);
				$commission_per = service_provider_commission($type, $provider_id, $total_sale);
				$com = $commission_per;
				$val = $price * $com / 100;
				$sum = $sum + $val;
				$pcount = provider_count($service,$res['id']);
				if($pcount > 0){
				    $sum = $sum/$pcount;
				} else {
				    $sum = $sum;
				}
				$sumFinal += $sum;
			}
		}
		return $sumFinal;
	}
	
	function getcoms($bid){
		global $conn;
		global $branch_id;
		$sum = 0;
		$sql="SELECT * from invoice_multi_service_provider where service_provider = '$bid' and status='1' and branch_id='".$branch_id."'";
		$result=query_by_id($sql,[],$conn);
		foreach($result as $row) {
			return $row['commission_per'];
		}
	}
	
	function getcomp($bid){
		global $conn;
		global $branch_id;
		$sum = 0;
        $sql="SELECT * from invoice_multi_service_provider where service_provider = '$bid' and status='1' and branch_id='".$branch_id."'";
		$result=query_by_id($sql,[],$conn);
		foreach($result as $row) {
			return $row['commission_per'];
		}
	}
	
?>	
<script type="text/javascript">
	$(document).ready(function(){
		$('#checkAll').click(function(){
			if ($('#checkAll').is(":checked")) { 
                $('.provider_service').prop('checked', true);
            } else { 
                $('.provider_service').prop('checked', false);
            } 
		});
		
		$('input[name="daterange"]').daterangepicker({
			opens: 'right',
			locale: {
                format: 'DD/MM/YYYY'
        	}
		});
	});
</script>