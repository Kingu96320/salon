	<?php 
	   echo getdate();
	
	    $u = $_SERVER['REQUEST_URI'];
	    $su = explode('/',$u);
	    if($su['1'] != $_SESSION['soft_url']){
            echo '<meta http-equiv="refresh" content="0; url=logout.php" />';
			die(); 
	    }
		if(isset($_SESSION['user_type']) && $_SESSION['user_type'] == 'superadmin'){
			if(isset($_POST['branch_list'])){
				$branch_id = $_POST['branch_list'];
				$_SESSION['branch_id'] = $branch_id;
				echo '<meta http-equiv="refresh" content="0; url=dashboard.php" />';
				die(); 
			}
		}
	?>
	<?php $toast = $_SESSION['t']; ?>
	<div class="loader-gif" style="display:none;position: fixed;width: 100%;height: 100%;background-color: #ffffffe8;z-index: 9999;">
	    <img src="../salonSoftFiles_new/img/loader.gif" alt="loader" style="max-width: 200px;position: absolute;left: 45%;top: 50%;"/>
	</div>
	<body <?php if($toast>0){ echo 'onload="myalert()"'; } ?> >

		<!-- Header starts -->
		<header>
		    <style>
		        nav li{
		            text-transform: capitalize;
		        }
		    </style>
		<?php
			$Hour = date('G');
			if ( $Hour >= 5 && $Hour < 12 ) {
				$wish = "Good Morning";
			} else if ( $Hour >= 12 && $Hour < 18 ) {
				$wish = "Good Afternoon";
			} else if ( $Hour >= 18 || $Hour < 20 ) {
				$wish = "Good Evening";
			} else if ( $Hour >= 20 || $Hour < 5 ) {
				$wish = "Good Night";
			}
		?>
			<!-- Logo starts -->
			<a href="dashboard.php" class="logo" style="color: #fff !important; font-size:18px; font-weight:bold;">
				 <?php echo ucwords($_SESSION['name'])." (".branch_name().")"; ?>
			</a>
			<!-- Logo ends -->

			<!-- Header actions starts -->
			<ul id="header-actions" class="clearfix">
				<li class="list-box user-admin dropdown" style="z-index:1">
					<a id="drop4" href="#" role="button" class="dropdown-toggle" style="background-color: #Fff;" data-toggle="dropdown">
						<!--<i class="icon-account_circle"></i>-->
						<img src="<?php echo systemlogo($conn); ?>" style="max-width: 70px;">
					</a>
					<ul class="dropdown-menu sm">
						<li class="dropdown-content">
							<!--<a href="#"><i class="icon-warning2"></i>Update Password<br><span>Your password will expire in 7 days.</span></a>-->
							<a href="changepassword.php">Change Password</a>
							<a href="logout.php">Logout</a>
						</li>
					</ul>
				</li>
			</ul>
			<!-- Header actions ends -->
			<span>
			    <a href="daily-follow-up.php">
			        <i class="fa fa-bell-o" style="max-width: 40px;border-radius: 50px;float: right;margin-top: 12px;color: #032f54; font-size: 20px; background-color: #fff; padding: 7px; <?= strpos($_SERVER['REQUEST_URI'],'daily-follow-up.php')?'background-color: #ff8100;color: #fff;':''?>" aria-hidden="true"></i>
			    </a>
		    </span>
		    <?php if(isset($_SESSION['user_type']) && $_SESSION['user_type'] == 'superadmin'){ ?>
		    <span>
		    	<?php 
		    		$branch_list = query_by_id("SELECT * FROM salon_branches WHERE status = '1'",[],$conn);
		 			if(count($branch_list) > 0){ 
		    	?>	
		    	<form action="" method="post" id="branch_form" style="width:350px; display: flex; float: right; margin-top: 12px;">
		    		<label style="color: #fff; float: left; margin-right: 10px; padding-top: 10px;"> Change branch : </label>
		    		<select name="branch_list" onchange="$('#branch_form').submit();" class="form-control" style="max-width: 200px;">
		    			<?php
		    				foreach($branch_list as $list){
		    					?>
		    						<option <?= $_SESSION['branch_id']==$list['id']?'selected':'' ?> value="<?= $list['id']; ?>"><?= ucfirst($list['branch_name']); ?></option>
		    					<?php
		    				}
		    			?>
		    		</select>
		    	</form>
		    	<?php } ?>
		    </span>
		    <?php } ?>
		</header>
		<!-- Header ends -->
    <div class="clearfix"></div>
		<!-- Container fluid Starts -->
		<div class="container-fluid">

			