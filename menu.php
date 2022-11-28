<!-- Navbar starts -->

<nav class="navbar navbar-default">
	<div class="navbar-header">
		<!--<span class="navbar-text">Menu</span>-->
		<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#collapse-navbar" aria-expanded="false">
			<span class="sr-only">Toggle navigation</span>
			<span class="icon-bar"></span>
			<span class="icon-bar"></span>
			<span class="icon-bar"></span>
		</button>
	</div>
	
	<!-- Collect the nav links, forms, and other content for toggling -->
	<div class="collapse navbar-collapse" id="collapse-navbar">
		<ul class="nav navbar-nav">
			<li 
			<?php if(strpos($_SERVER['REQUEST_URI'],'index.php')||strpos($_SERVER['REQUEST_URI'],'dashboard.php'))
				echo 'class="active"';
			?>
			>
				<a href="dashboard.php"><i class="icon-shop"></i>Dashboard</a>
			</li>
			
			<li 
			<?php if(strpos($_SERVER['REQUEST_URI'],'enquiry.php')||strpos($_SERVER['REQUEST_URI'],'editenquiry.php'))
				echo 'class="active"';
			?>
			>
				<a href="enquiry.php"><i class="icon-add-user"></i>Enquiry</a>
			</li>
			<li 
			<?php if(strpos($_SERVER['REQUEST_URI'],'appointment.php') || strpos($_SERVER['REQUEST_URI'],'webappointment.php'))
				echo 'class="active"';
				$sql="Select count(*) as total from `app_invoice_".$_SESSION['branch_id']."` where active='0' and app_status = '0' and app_from = '1'";		
			    $result=query_by_id($sql,[],$conn)[0];
			    if($result){
				  $total_appointment=$result['total'];
			    }else{
			        $total_appointment = 0;
			    }
			 ?>
			>
			    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><i class="icon-perm_phone_msg"><?=($total_appointment > 0)?'<span class="badge badge-secondary" style="position:absolute;background-color:red;margin-bottom:3px;right:33px;">'.$total_appointment.'</span>':''?></i>Appointments<span class="caret"></span></a>
				<ul class="dropdown-menu">
					<li class="<?= strpos($_SERVER['REQUEST_URI'],'/appointment.php')?'active':'' ?>"><a href="appointment.php">Software appointment</a></li>
					<li class="<?= strpos($_SERVER['REQUEST_URI'],'/webappointment.php')?'active':'' ?>"><a href="webappointment.php">Web appointment <?=($total_appointment > 0)?'<span class="badge badge-secondary" style="position:absolute;background-color:red;margin-bottom:3px;right:15px;">'.$total_appointment.'</span>':''?></a></li>
				    <li class="<?= strpos($_SERVER['REQUEST_URI'],'/viproomappointment.php')?'active':'' ?>"><a href="viproomappointment.php">Vip Room appointment</a></li>
				</ul>
			</li>
			<li 
			<?php if(strpos($_SERVER['REQUEST_URI'],'billing.php'))
				echo 'class="active"';
			?>
			>
				<a href="billing.php"><i class="icon-credit-card"></i>Billing</a>
			</li>
			
			<li 
			<?php if(strpos($_SERVER['REQUEST_URI'],'clients.php') || strpos($_SERVER['REQUEST_URI'],'clientprofile.php') || strpos($_SERVER['REQUEST_URI'],'clientpackage.php'))
				echo 'class="active"';
			?>
			>
				<a href="clients.php"><i class="icon-emoji-happy"></i>Clients</a>
			</li>
			
			<li 
			<?php if(strpos($_SERVER['REQUEST_URI'],'feedback.php'))
				echo 'class="active"';
			?>
			>
				<a href="feedback.php"><i class="icon-stars"></i>Feedbacks</a>
			</li>
			<li 
			<?php if(strpos($_SERVER['REQUEST_URI'],'addpurchase.php')
					|| strpos($_SERVER['REQUEST_URI'],'productdetail.php')
					|| strpos($_SERVER['REQUEST_URI'],'vendorprofile.php')
					|| strpos($_SERVER['REQUEST_URI'],'productused.php')
					|| strpos($_SERVER['REQUEST_URI'],'vendor.php') 
					|| strpos($_SERVER['REQUEST_URI'],'productsreport.php') 
					|| strpos($_SERVER['REQUEST_URI'],'products.php'))
				echo 'class="active"';
			?>
			>
				<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><i class="icon-shopping-basket"></i>Products<span class="caret"></span></a>
				<ul class="dropdown-menu">
					<li class="<?= strpos($_SERVER['REQUEST_URI'],'productsreport.php')?'active':'' ?>"><a href="productsreport.php">Current stock</a></li>
					<?php if(isset($_SESSION['user_type']) && $_SESSION['user_type'] == 'superadmin') { ?>
						<li class="<?= strpos($_SERVER['REQUEST_URI'],'products.php')?'active':'' ?>"><a href="products.php">Product list</a></li>
					<?php } ?>
					<li class="<?= strpos($_SERVER['REQUEST_URI'],'addpurchase.php')?'active':'' ?>"><a href="addpurchase.php">Add stock</a></li>
					<li class="<?= strpos($_SERVER['REQUEST_URI'],'vendor.php')||strpos($_SERVER['REQUEST_URI'],'vendorprofile.php')?'active':'' ?>"><a href="vendor.php">Product vendor(s)</a></li>
					<li class="<?= strpos($_SERVER['REQUEST_URI'],'productused.php')?'active':'' ?>"><a href="productused.php">Use product in salon</a></li>
				</ul>
			</li>
			
			
			
			<?php if($_SESSION['u_role']==1){ ?>
				<li
				<?php if(strpos($_SERVER['REQUEST_URI'],'balancereports.php')
						|| strpos($_SERVER['REQUEST_URI'],'dailyreport.php')
						|| strpos($_SERVER['REQUEST_URI'],'day-summary.php')
						|| strpos($_SERVER['REQUEST_URI'],'billreport.php')
						|| strpos($_SERVER['REQUEST_URI'],'service_provider_report.php')
						|| strpos($_SERVER['REQUEST_URI'],'enquiryreport.php')
						|| strpos($_SERVER['REQUEST_URI'],'bulk-sms.php') 
					    || strpos($_SERVER['REQUEST_URI'],'bulk-email.php')
					    || strpos($_SERVER['REQUEST_URI'],'received-pending-payments.php')
					    || strpos($_SERVER['REQUEST_URI'],'/history.php')
					    || strpos($_SERVER['REQUEST_URI'],'/sms-history.php')
					    || strpos($_SERVER['REQUEST_URI'],'otherreports.php')
					)
					echo 'class="active"';
				?>
				>
					<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><i class="icon-area-graph"></i>Reports<span class="caret"></span></a>
				<ul class="dropdown-menu">
				        <li class="<?= strpos($_SERVER['REQUEST_URI'],'dailyreport.php')?'active':'' ?>">
							<a href='dailyreport.php'>Daily Reports</a>
						</li> 
						<li class="<?= strpos($_SERVER['REQUEST_URI'],'day-summary.php')?'active':'' ?>">
							<a href='day-summary.php'>Day Summary</a>
						</li>
						<li class="<?= strpos($_SERVER['REQUEST_URI'],'billreport.php')?'active':'' ?>">
							<a href='billreport.php'>Billing Reports</a>
						</li>
						<li class="<?= strpos($_SERVER['REQUEST_URI'],'enquiryreport.php')?'active':'' ?>">
							<a href='enquiryreport.php'>Enquiry Reports</a>
						</li>
						<li class="<?= strpos($_SERVER['REQUEST_URI'],'service_provider_report.php')?'active':'' ?>">
							<a href='service_provider_report.php'>Service Provider Reports</a>
						</li>
						<li class="<?= strpos($_SERVER['REQUEST_URI'],'received-pending-payments.php')?'active':'' ?>">
							<a href='received-pending-payments.php'>Received Pending Payments</a>
						</li>
						<li class="<?= strpos($_SERVER['REQUEST_URI'],'/history.php')?'active':'' ?>">
					        <a href="history.php">History</a>
					    </li>
						<li class="<?= strpos($_SERVER['REQUEST_URI'],'balancereports.php')?'active':'' ?>">
							<a href='balancereports.php'>Balance Reports</a>
						</li>
						<li class="<?= strpos($_SERVER['REQUEST_URI'],'otherreports.php')?'active':'' ?>">
							<a href='otherreports.php'>Advance Reports</a>
						</li>
                        <?php if($_SESSION['user_type']=='superadmin' || $_SESSION['u_role']==1){ ?>
						<li>
						    <a href="attendance.php">Attendance report</a>
					    </li>
					    <li class="<?= strpos($_SERVER['REQUEST_URI'],'/sms-history.php')?'active':'' ?>">
						    <a href="sms-history.php">SMS History</a>
					    </li>
					    <?php } ?>
					    <li class="<?= strpos($_SERVER['REQUEST_URI'],'invoicedeletereport.php')?'active':'' ?>">
							<a href='invoicedeletereport.php'>Invoice Delete Reports</a>
						</li>
					 <!--   <li class="<?= strpos($_SERVER['REQUEST_URI'],'bulk-email.php')?'active':'' ?>">-->
					 <!--       <a href="bulk-email.php">Bulk EMAIL</a>-->
					 <!--   </li>-->
					</ul>
				</li>
			<?php } ?>
			
			
			<li 
			<?php if(strpos($_SERVER['REQUEST_URI'],'expenses.php')
			    || strpos($_SERVER['REQUEST_URI'],'reward-points.php')
			    || strpos($_SERVER['REQUEST_URI'],'services_reminder.php')
				|| strpos($_SERVER['REQUEST_URI'],'memberships.php')
				|| strpos($_SERVER['REQUEST_URI'],'membership_list.php')
			    || strpos($_SERVER['REQUEST_URI'],'coupons.php')
			    || strpos($_SERVER['REQUEST_URI'],'employee-salary.php')
			    || strpos($_SERVER['REQUEST_URI'],'packages.php')
				|| strpos($_SERVER['REQUEST_URI'],'services.php')
				|| strpos($_SERVER['REQUEST_URI'],'beauticians.php') 
				|| strpos($_SERVER['REQUEST_URI'],'employees') 
				|| strpos($_SERVER['REQUEST_URI'],'software_setting.php')
				|| strpos($_SERVER['REQUEST_URI'],'mobile_app.php')
				|| strpos($_SERVER['REQUEST_URI'],'all_branches.php')
				|| strpos($_SERVER['REQUEST_URI'],'transfer-options.php')
				|| strpos($_SERVER['REQUEST_URI'],'self-assessment-data.php')
			)
				echo 'class="active"';
			?>
			>
				<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><i class="icon-plus4"></i>Add & Manage<span class="caret"></span></a>
				<ul class="dropdown-menu">		
					<li class="<?= strpos($_SERVER['REQUEST_URI'],'expenses.php')?'active':'' ?>">
						<a href='expenses.php'>Expenses</a>
					</li>
					<?php if(isset($_SESSION['user_type']) && $_SESSION['user_type'] == 'superadmin') { ?>
						<li class="<?= strpos($_SERVER['REQUEST_URI'],'services.php')?'active':'' ?>">
							<a href='services.php'>Services</a>
						</li>
					<?php } ?>
					<li class="<?= strpos($_SERVER['REQUEST_URI'],'packages.php')?'active':'' ?>">
					    <a href="packages.php">Packages</a>
				    </li>
				    <li class="<?= strpos($_SERVER['REQUEST_URI'],'coupons.php')?'active':'' ?>">
				        <a href="coupons.php">Coupons</a>
				    </li>
				    <?php if($_SESSION['user_type'] == 'superadmin'){ ?>
				    <li class="<?= strpos($_SERVER['REQUEST_URI'],'employee-salary.php')?'active':'' ?>">
						<a href="employee-salary.php">Employee salary</a>
					</li>
					<?php } ?>
					<li class="<?= strpos($_SERVER['REQUEST_URI'],'beauticians.php')?'active':'' ?>">
						<a href="beauticians.php">Service providers</a>
					</li>
					<li class="<?= strpos($_SERVER['REQUEST_URI'],'services_reminder.php')?'active':'' ?>">
                		<a href='services_reminder.php'>Automatic service reminder</a>
                	</li>
					<li class="<?= strpos($_SERVER['REQUEST_URI'],'employees.php')?'active':'' ?>">
						<a href='employees.php'>Staff</a>
					</li>
					<!--<li class="<?= strpos($_SERVER['REQUEST_URI'],'reward-points.php')?'active':'' ?>">-->
					<!--    <a href="reward-points.php">Reward points</a>-->
					<!--</li>-->
					<?php if(isset($_SESSION['user_type']) && $_SESSION['user_type'] == 'superadmin'){ ?>
					<li class="<?= strpos($_SERVER['REQUEST_URI'],'memberships.php')?'active':'' ?>">
					    <a href="memberships.php">Membership</a>
				    </li>
				    <?php } ?>

				    <?php if(!isset($_SESSION['user_type']) && $_SESSION['u_role']==1){ ?>
					<li class="<?= strpos($_SERVER['REQUEST_URI'],'membership_list.php')?'active':'' ?>">
					    <a href="membership_list.php">Membership</a>
				    </li>
				    <?php } ?>
					<?php
						if(isset($_SESSION['user_type']) && $_SESSION['user_type'] == 'superadmin'){ ?>
						<li class="<?= strpos($_SERVER['REQUEST_URI'],'all_branches.php')?'active':'' ?>">
							<a href='all_branches.php'>All branches</a>
						</li>
						<li class="<?= strpos($_SERVER['REQUEST_URI'],'transfer-options.php')?'active':'' ?>">
							<a href='transfer-options.php'>Transfer options</a>
						</li>
					<?php } ?>
					<li class="<?= strpos($_SERVER['REQUEST_URI'],'software_setting.php')?'active':'' ?>">
						<a href='software_setting.php'>Software setting</a>
					</li>
					<li class="<?= strpos($_SERVER['REQUEST_URI'],'self-assessment-data.php')?'active':'' ?>">
						<a href='self-assessment-data.php'>Self assessment data</a>
					</li>
					<?php if($_SESSION['user_type']=='superadmin' || $_SESSION['u_role']==1){ ?>
						<li>
						    <a href="mark_attendance.php">Mark attendance</a>
					    </li>
				    <?php } ?>
					<!--<li class="<?= strpos($_SERVER['REQUEST_URI'],'mobile_app.php')?'active':'' ?>">-->
					<!--	<a href='mobile_app.php'>Mobile app</a>-->
					<!--</li>-->
				</ul>
			</li>
		</ul>
	</div>
</nav>