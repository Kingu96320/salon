<?php
include_once './includes/db_include.php';
$branch_id = $_SESSION['branch_id'];
$id = $_SESSION['uid'];

if(isset($_SESSION['user_type']) && $_SESSION['user_type']=='superadmin'){
	$sql = "SELECT * from superadmin where id='".$id."'";
} else {
	$sql = "SELECT * from user where id='".$id."' and branch_id='".$branch_id."' order by name asc";
}
$row=query_by_id($sql,[],$conn)[0];
if($row)
{
    if(isset($_POST['submit'])){
    	if(!isset($_SESSION['user_type'])){
        	$name = $_POST['name'];
        }
        if(isset($_POST['pass1'])){
    		$pass1 = $_POST['pass1'];
    		$pass2 = $_POST['pass2'];
    		if(!empty($pass1) && !empty($pass2)) {
    	        if($pass1==$pass2){
    		        $pass1 = md5($pass1);
        			$salt="ea7b7a7372bceab4a64b3c2d380c8a72";
        			$pass = $salt.$pass1;
        			$pass1 = md5(sha1(md5($pass)));
        			if(isset($_SESSION['user_type']) && $_SESSION['user_type']=='superadmin'){
    		        	query("UPDATE `superadmin` SET `password`='$pass1',`status`= 1 WHERE id=$id",[],$conn);
    		        } else {
    		        	query("UPDATE `user` SET `name`='$name',`pass`='$pass1',`active`= 0 WHERE id=$id and branch_id='".$branch_id."'",[],$conn);
    		        }

    				$_SESSION['t']  = 1;
    				$_SESSION['tmsg']  = "Password changed Successfully ";
    				echo '<meta http-equiv="refresh" content="0; url=changepassword.php" />';die();
    		    }else{
    		        $_SESSION['t']  = 2;
    			    $_SESSION['tmsg']  = "Invalid Password";
    			    echo '<meta http-equiv="refresh" content="0; url=changepassword.php" />';die();
    	        }
            } else {
                $_SESSION['t']  = 2;
			    $_SESSION['tmsg']  = "Password field is empty.";
			    echo '<meta http-equiv="refresh" content="0; url=changepassword.php" />';die();
            }
        }
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
		    <div class="col-lg-4 col-md-4 col-sm-2 hidden-xs"></div>
			<div class="col-lg-4 col-md-4 col-sm-8 col-xs-12">
				<div class="panel" style="border:1px solid #032f54;">
					<div class="panel-heading" style="background-color: #032f54;border-radius: 0px;color: #ffffff;">
						<h4 class="text-center">Update profile / password</h4>
					</div>
					<div class="panel-body">
						<div class="row">
						<form action="" method="post">
							<?php if(!isset($_SESSION['user_type'])){ ?>
							<div class="col-lg-12">
								<div class="form-group">
									<label for="name">Enter name <span class="text-danger">*</span></label>
									<input type="text" class="form-control" required id="userName" value="<?php echo $row['name']; ?>" name="name" placeholder="Name" required>
								</div>
							</div>
							<?php } ?>
							<div class="col-lg-12">
								<div class="form-group">
									<label for="user">Username</label>
									<input type="text" class="form-control" value="<?php echo $row['username']; ?>" name="user" id="user" placeholder="username" readonly>
									<span id="user-status"></span>
								</div>
							</div>
							<div class="col-lg-12">
								<div class="form-group">
									<label for="pass1">New password <span class="text-danger">*</span></label>
									<input type="password" onBlur="checklength()" class="form-control" name="pass1" id="pass1" autocomplete="off" placeholder="Password">
									<span id="pass-status"  class="text-danger"></span>
								</div>
							</div>
							<div class="col-lg-12">
								<div class="form-group">
									<label for="pass2">Confirm password <span class="text-danger">*</span></label>
									<input type="password" class="form-control" name="pass2" id="pass2" placeholder="Password">
								</div>
							</div>
							<div class="clearfix"></div>
							<div class="col-lg-12">
								<div class="form-group">
									<?php if(DELETE_BUTTON_INACTIVE == 'true'){ ?>
									<div class="alert alert-danger">Disabled on demo mode.</div>
									<?php } else { ?>
									<label for=""> </label>
									<button style="background-color:#032f54;border-color:#032f54;" type="submit" name="submit" class="btn btn-success"><i class="fa fa-user" aria-hidden="true"></i> Update user</button>
								    <?php } ?>
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
<script>
function checklength() {
	if($("#pass1").val() != '' && $("#pass1").val().length < 6){
	    $("#pass-status").html('Password Length 6 Chars minimum');
	    $('#pass1').val("");
    }
}
</script>

<?php 
    include "footer.php"; 
?>