<?php
include_once './includes/db_include.php';
include "topbar.php";
include "header.php";
include "menu.php";

header('LOCATION: dashboard.php'); die();

if (isset($_POST['submit'])){
	$branch_name = trim(htmlspecialchars($_POST['branch_name']));
	$name = trim(htmlspecialchars($_POST['name']));
	$username = strtolower(trim(htmlspecialchars($_POST['username'])));
	$password = trim($_POST['password']);
	$confirm_password = trim($_POST['confirm_password']);

	$branch = query_by_id("SELECT id FROM salon_branches WHERE LOWER(branch_name)='".$branch_name."' AND status='1'",[],$conn)[0]['id'];
	if($branch > 0){
		$_SESSION['t']  = 2;
	    $_SESSION['tmsg']  = "Branch already exist";
	    echo '<meta http-equiv="refresh" content="0; url=new_branch.php" />';die();
	} else {

		if(!empty($password) && !empty($confirm_password)) {
	        if($password==$confirm_password){
		        $password = md5($password);
    			$salt="ea7b7a7372bceab4a64b3c2d380c8a72";
    			$pass = $salt.$password;
    			$password = md5(sha1(md5($pass)));
		        
				$branch_id = get_insert_id("INSERT INTO salon_branches SET branch_name='".$branch_name."', status='1', branch_id='1'",[],$conn);

				query("INSERT INTO `user` SET `username`='$username',`name`='$name',`pass`='$password',`active`= 0, `branch_id`='".$branch_id."', `role`='1', `sp_id`='0'",[],$conn);  

				query("UPDATE salon_branches SET branch_id='".$branch_id."' WHERE id ='".$branch_id."'",[],$conn);
				$table1_name = "app_invoice_".$branch_id;
				$table1 = "CREATE TABLE IF NOT EXISTS `".$table1_name."` (
				  `id` int(11) NOT NULL,
				  `inv` int(10) NOT NULL DEFAULT '0',
				  `client` int(111) NOT NULL DEFAULT '0',
				  `role` varchar(255) DEFAULT NULL,
				  `doa` date DEFAULT NULL,
				  `itime` text,
				  `dis` varchar(111) DEFAULT NULL,
				  `disper` int(11) NOT NULL,
				  `tax` text,
				  `taxtype` int(5) NOT NULL,
				  `pay_method` int(111) DEFAULT '0',
				  `details` text NOT NULL,
				  `paydetails` text NOT NULL,
				  `total` float(14,2) DEFAULT NULL,
				  `subtotal` float(14,2) NOT NULL DEFAULT '0.00',
				  `paid` float(14,2) NOT NULL DEFAULT '0.00',
				  `due` float(14,2) NOT NULL DEFAULT '0.00',
				  `notes` text,
				  `coupon` text NOT NULL,
				  `bpaid` float(14,2) NOT NULL DEFAULT '0.00',
				  `bmethod` int(111) NOT NULL DEFAULT '0',
				  `chnge` int(11) NOT NULL DEFAULT '0',
				  `type` int(1) DEFAULT NULL,
				  `status` varchar(111) DEFAULT NULL,
				  `invoice` int(1) NOT NULL,
				  `active` int(1) NOT NULL,
				  `appdate` date DEFAULT NULL,
				  `billdate` date NOT NULL,
				  `updatetime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
				  `uid` int(25) NOT NULL,
				  `appuid` int(11) NOT NULL DEFAULT '0',
				  `ss_created_status` int(111) NOT NULL DEFAULT '0',
				  `bill_created_status` int(111) NOT NULL DEFAULT '0',
				  `draft_created_status` int(111) NOT NULL DEFAULT '0',
				  `branch_id` int(11) NOT NULL DEFAULT '".$branch_id."',
				  `client_branch_id` int(11) NOT NULL DEFAULT '".$branch_id."',
				  `app_from` int(11) NOT NULL,
  				  `app_status` int(11) NOT NULL
				) ENGINE=InnoDB DEFAULT CHARSET=latin1";

				create_table($table1, $conn);

				$table2_name = "app_invoice_items_".$branch_id;
				$table2 = "CREATE TABLE IF NOT EXISTS `".$table2_name."` (
				  `id` int(11) NOT NULL,
				  `iid` int(111) NOT NULL DEFAULT '0',
				  `client` int(111) NOT NULL DEFAULT '0',
				  `service` varchar(111) DEFAULT NULL,
				  `package_service_id` int(11) NOT NULL DEFAULT '0',
				  `quantity` int(111) NOT NULL DEFAULT '0',
				  `staffid` int(111) NOT NULL DEFAULT '0',
				  `disc_row` varchar(255) DEFAULT NULL,
				  `price` float(14,2) NOT NULL DEFAULT '0.00',
				  `bill` int(5) NOT NULL,
				  `type` varchar(111) DEFAULT NULL,
				  `start_time` varchar(155) DEFAULT NULL,
				  `end_time` varchar(155) DEFAULT NULL,
				  `app_date` varchar(255) DEFAULT NULL,
				  `active` int(1) DEFAULT NULL,
				  `branch_id` int(11) NOT NULL DEFAULT '".$branch_id."',
				  `client_branch_id` int(11) NOT NULL DEFAULT '".$branch_id."'
				) ENGINE=InnoDB DEFAULT CHARSET=latin1";
				create_table($table2, $conn);

				$table3_name = "invoice_".$branch_id;
				$table3 = "CREATE TABLE IF NOT EXISTS `".$table3_name."` (
				  `id` int(11) NOT NULL,
				  `inv` int(10) NOT NULL DEFAULT '0',
				  `appointment_id` int(11) NOT NULL DEFAULT '0',
				  `service_slip_id` int(111) DEFAULT NULL,
				  `gst` varchar(255) DEFAULT NULL,
				  `client` int(111) NOT NULL DEFAULT '0',
				  `referral_code` varchar(111) DEFAULT NULL,
				  `doa` date DEFAULT NULL,
				  `itime` varchar(111) DEFAULT NULL,
				  `dis` varchar(111) DEFAULT NULL,
				  `disper` varchar(11) DEFAULT NULL,
				  `tax` int(111) NOT NULL DEFAULT '0',
				  `taxtype` int(5) NOT NULL,
				  `pay_method` varchar(111) NOT NULL,
				  `wallet_status` int(11) NOT NULL DEFAULT '0',
				  `details` text NOT NULL,
				  `paydetails` text NOT NULL,
				  `total` float(14,2) DEFAULT '0.00',
				  `subtotal` float(14,2) DEFAULT '0.00',
				  `advance` float(14,2) DEFAULT '0.00',
				  `paid` float(14,2) NOT NULL DEFAULT '0.00',
				  `due` float(14,2) DEFAULT '0.00',
				  `notes` text,
				  `coupon` varchar(11) DEFAULT NULL,
				  `bpaid` float(14,2) NOT NULL DEFAULT '0.00',
				  `bmethod` int(11) NOT NULL DEFAULT '0',
				  `chnge` int(11) NOT NULL DEFAULT '0',
				  `type` int(1) DEFAULT NULL,
				  `used_reward_points` varchar(111) DEFAULT NULL,
				  `status` int(11) DEFAULT NULL,
				  `invoice` int(1) NOT NULL,
				  `active` int(1) NOT NULL,
				  `appdate` date DEFAULT NULL,
				  `billdate` date NOT NULL,
				  `updatetime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
				  `uid` int(25) NOT NULL,
				  `appuid` int(11) NOT NULL DEFAULT '0',
				  `membership_appilied` int(11) NOT NULL DEFAULT '0' COMMENT '1 = appplied, 0 = not applied',
				  `membership_id` int(11) NOT NULL DEFAULT '0',
				  `branch_id` int(11) NOT NULL DEFAULT '".$branch_id."',
				  `client_branch_id` int(11) NOT NULL DEFAULT '".$branch_id."',
				  `is_rewardpoint_given` int(1) NOT NULL DEFAULT '1'
				) ENGINE=InnoDB DEFAULT CHARSET=latin1";
				create_table($table3, $conn);

				$table4_name = "invoice_items_".$branch_id;
				$table4 = "CREATE TABLE IF NOT EXISTS `".$table4_name."` (
				  `id` int(11) NOT NULL,
				  `iid` int(111) NOT NULL DEFAULT '0',
				  `client` int(111) NOT NULL DEFAULT '0',
				  `package_id` int(255) DEFAULT NULL,
				  `package_service_id` int(11) NOT NULL DEFAULT '0',
				  `service` varchar(111) DEFAULT NULL,
				  `product_stock_batch` int(11) NOT NULL DEFAULT '0',
				  `quantity` int(111) DEFAULT '0',
				  `staffid` int(111) NOT NULL DEFAULT '0',
				  `disc_row` varchar(11) DEFAULT NULL,
				  `actual_price` float(14,2) NOT NULL DEFAULT '0.00',
				  `price` float(14,2) DEFAULT '0.00',
				  `reward_point` int(11) NOT NULL DEFAULT '0',
				  `bill` int(5) NOT NULL,
				  `type` varchar(111) DEFAULT NULL,
				  `start_time` varchar(155) NOT NULL,
				  `end_time` varchar(155) NOT NULL,
				  `active` int(1) DEFAULT NULL,
				  `branch_id` int(11) NOT NULL DEFAULT '".$branch_id."',
				  `client_branch_id` int(11) NOT NULL DEFAULT '".$branch_id."'
				) ENGINE=InnoDB DEFAULT CHARSET=latin1";
				create_table($table4, $conn);

				$table5_name = "sms_templates_".$branch_id;
				$table5 = "CREATE TABLE IF NOT EXISTS `".$table5_name."` (
				  `id` int(11) NOT NULL,
				  `template_name` varchar(255) NOT NULL,
				  `template_detail` longtext NOT NULL,
				  `status` int(11) NOT NULL DEFAULT '1' COMMENT '0 = deleted, 1 = active',
				  `branch_id` int(11) NOT NULL DEFAULT '".$branch_id."'
				) ENGINE=InnoDB DEFAULT CHARSET=latin1";
				create_table($table5, $conn);

				query("INSERT INTO `".$table5_name."` (`id`, `template_name`, `template_detail`, `status`, `branch_id`) VALUES
					(1, 'Enquiry bulk sms', '{name}\n\n\n{salon_name}', 1, ".$branch_id."),
					(2, 'Appointment booking', '{name}\n\n\n{salon_name}', 1, ".$branch_id."),
					(3, 'Service slip - client', '{name}\n\n\n{salon_name}', 1, ".$branch_id."),
					(4, 'Service slip - service provider', '{name}\n\n\n{salon_name}', 1, ".$branch_id."),
					(5, 'Billing', '{name}\n\n\n{salon_name}', 1, ".$branch_id."),
					(6, 'New client', '{name}\n\n\n{salon_name}', 1, ".$branch_id."),
					(7, 'Feedback after billing', '{name}\n\n\n{salon_name}', 1, ".$branch_id."),
					(8, 'Cancle appointment', '{name}\n\n\n{salon_name}', 1, ".$branch_id."),
					(9, 'Update appointment', '{name}\n\n\n{salon_name}', 1, ".$branch_id."),
					(10, 'Birthday', '{name}\n\n\n{salon_name}', 1, ".$branch_id."),
					(11, 'Anniversary', '{name}\n\n\n{salon_name}', 1, ".$branch_id."),
					(12, 'Pending payment', '{name}\n\n\n{salon_name}', 1, ".$branch_id."),
					(13, 'Package expiry', '{name}\n\n\n{salon_name}', 1, ".$branch_id."),
					(14, 'Enquiry followup', '{name}\n\n\n{salon_name}', 1, ".$branch_id."),
					(15, 'Irregular client', '{name}\n\n\n{salon_name}', 1, ".$branch_id."),
					(16, 'Client bulk sms', '{name}\n\n\n{salon_name}', 1, ".$branch_id.");",[],$conn);

				query("INSERT INTO `day_end_report` (`report_time`, `sms_status`, `email_status`, `status`, `branch_id`) VALUES
						('20:00:00', 1, 1, 1, '$branch_id')",[],$conn);

				query("INSERT INTO `system` (`salon`, `address`, `phone`, `logo`, `loginbg`, `website`, `email`, `gst`, `shpstarttime`, `shpendtime`, `extra_hours`, `active`, `branch_id`) VALUES
					('Easy Salon Software', '', '', 'upload/logo.png', 'upload/login_page_bg/make-up-glamour-portrait-of-beautiful-woman-model--P2ADV7C.jpg', 'https://google.com', '', '', '09:00:00', '21:00:00', 0, 0, '$branch_id');",[],$conn);

				query("INSERT INTO `working_days_time` (`day_name`, `open_time`, `close_time`, `working_status`, `status`, `branch_id`) VALUES
						('Monday', '09:00:00', '21:00:00', 1, 1, '$branch_id'),
						('Tuesday', '09:00:00', '21:00:00', 1, 1, '$branch_id'),
						('Wednesday', '09:00:00', '21:00:00', 1, 1, '$branch_id'),
						('Thursday', '09:00:00', '21:00:00', 1, 1, '$branch_id'),
						('Friday', '09:00:00', '21:00:00', 1, 1, '$branch_id'),
						('Saturday', '09:00:00', '21:00:00', 1, 1, '$branch_id'),
						('Sunday', '09:00:00', '21:00:00', 1, 1, '$branch_id');",[],$conn);

				query("ALTER TABLE `".$table1_name."` ADD PRIMARY KEY (`id`)",[],$conn);
				query("ALTER TABLE `".$table2_name."` ADD PRIMARY KEY (`id`)",[],$conn);
				query("ALTER TABLE `".$table3_name."` ADD PRIMARY KEY (`id`)",[],$conn);
				query("ALTER TABLE `".$table4_name."` ADD PRIMARY KEY (`id`)",[],$conn);
				query("ALTER TABLE `".$table1_name."` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT",[],$conn);
				query("ALTER TABLE `".$table2_name."` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT",[],$conn);
				query("ALTER TABLE `".$table3_name."` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT",[],$conn);
				query("ALTER TABLE `".$table4_name."` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT",[],$conn);
				query("ALTER TABLE `".$table5_name."` ADD PRIMARY KEY (`id`)",[],$conn);
				query("ALTER TABLE `".$table5_name."` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT",[],$conn);

				$_SESSION['t']  = 1;
			    $_SESSION['tmsg']  = "New branch added successfully";
			    echo '<meta http-equiv="refresh" content="0; url=new_branch.php" />';die();
			}else{
		        $_SESSION['t']  = 2;
			    $_SESSION['tmsg']  = "Invalid Password";
			    echo '<meta http-equiv="refresh" content="0; url=new_branch.php" />';die();
	        }
        } else {
            $_SESSION['t']  = 2;
		    $_SESSION['tmsg']  = "Password field is empty.";
		    echo '<meta http-equiv="refresh" content="0; url=new_branch.php" />';die();
        }
	}
}


?>
<!-- Dashboard wrapper starts -->
<div class="dashboard-wrapper">
	<!-- Main container starts -->
	<div class="main-container">
		<!-- Row starts -->
		<div class="row gutter">
		    <div class="col-lg-4 col-md-4 col-sm-2 hidden-xs"></div>
			<div class="col-lg-4 col-md-4 col-sm-8 col-xs-12">
				<div class="panel" style="border:1px solid #032f54;">
					<div class="panel-heading" style="background-color: #032f54;border-radius: 0px;color: #ffffff;">
						<h4 class="text-center">Add branch</h4>
					</div>
					<div class="panel-body">
						<div class="row">
						<form action="" method="post">
							<div class="col-lg-12">
								<div class="form-group">
									<label for="branch_name">Enter branch name <span class="text-danger">*</span></label>
									<input type="text" class="form-control" required id="branch_name" value="" name="branch_name" placeholder="Branch name" required>
								</div>
							</div>
							<div class="col-lg-12">
								<div class="form-group">
									<label for="name">Enter name <span class="text-danger">*</span></label>
									<input type="text" class="form-control" required id="name" value="" name="name" placeholder="Name" required>
								</div>
							</div>
							<div class="col-lg-12">
								<div class="form-group">
									<label for="username">Enter username <span class="text-danger">*</span></label>
									<input type="text" class="form-control" required id="username" value="" name="username" placeholder="Username" required>
								</div>
							</div>
							<div class="col-lg-12">
								<div class="form-group">
									<label for="password">Password <span class="text-danger">*</span></label>
									<input type="password" class="form-control" required id="password" value="" name="password" placeholder="Password" required>
								</div>
							</div>
							<div class="col-lg-12">
								<div class="form-group">
									<label for="confirm_password">Confirm password <span class="text-danger">*</span></label>
									<input type="password" class="form-control" required id="confirm_password" value="" name="confirm_password" placeholder="Confirm password" required>
								</div>
							</div>
							<div class="clearfix"></div>
							<div class="col-lg-12">
								<div class="form-group">
									<button class="btn btn-primary" type="submit" name="submit">Create branch</button>
								</div>
							</div>
							</form>
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
<?php 
    include "footer.php"; 
?>