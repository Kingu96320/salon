<?php 
include "./includes/db_include.php";
if(isset($_SESSION['uid']) && isset($_SESSION['user_type'])){
    header('LOCATION:dashboard.php');die();
}
if(isset($_POST['signin'])){
	$user = $_POST['user'];
	$pass = $_POST['pass'];
	$pass = md5($pass);
	$salt="ea7b7a7372bceab4a64b3c2d380c8a72";
	$pass = $salt.$pass;
	$pass = md5(sha1(md5($pass)));
	$branch_id = $_POST['branch_id'];
	$remember = $_POST['remember'];
	$query = "SELECT * FROM `superadmin` WHERE username='".$user."' and password='".$pass."' and status=1";

        $result = get_row_count($query,[],$conn);
        $ip = clientip();  
        if($result && ($result == 1)){ 
            $row = query_by_id($query,[],$conn)[0];
            $u = $_SERVER['REQUEST_URI'];
	        $su = explode('/',$u);
            $_SESSION['soft_url'] = $su[1];
            $_SESSION['user'] = $row['username'];
            $_SESSION['name'] = ucfirst($row['username']);
            $_SESSION['uid'] = $row['id'];
            $_SESSION['u_role'] = 1;
            $_SESSION['user_type'] = 'superadmin';
            $_SESSION['branch_id'] = 1;
            $system = systemname($conn);

            $cookie_name = $_SESSION['user'];
            $cookie_value = $row['id'];
            $expiry = time() + (86400 * 30);
            getuser($user,"Success",$ip,$conn);
            $_SESSION['t']  = 1;
            $_SESSION['tmsg']  = "Welcome ".$_SESSION['name']." To ".$system." ";
            header('LOCATION:dashboard.php');die();
        }else
		{
            getuser($user,"Failed",$ip,$conn);
            $_SESSION['t']  = 2;
            $_SESSION['tmsg']  = "Login Failed";
            header('LOCATION:superadmin.php');die();
        }
}

$bgurl = query_by_id("SELECT loginbg FROM `system` WHERE active=0",[],$conn)[0];
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="UTF-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta http-equiv="X-UA-Compatible" content="IE=edge" />
		<meta name="author" content="13DesignStreet" />
		<link rel="shortcut icon" href="img/favicon.ico">
		<title>FSNPOS Solutions</title>
		<!-- Bootstrap CSS -->
		<link href="../salonSoftFiles_new/css/bootstrap.min.css" rel="stylesheet" media="screen" />
		<!-- Login CSS -->
		<link href="../salonSoftFiles_new/css/main.css" rel="stylesheet" />
		<!-- Ion Icons -->
		<link href="../salonSoftFiles_new/fonts/icomoon/icomoon.css" rel="stylesheet" />
		<script src="//code.jquery.com/jquery-latest.min.js" type="text/javascript"></script>
		<!-- Calendar CSS -->
		<script src="../salonSoftFiles_new/js/jquery.js"></script>
		<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
		<script src="//code.jquery.com/jquery-1.10.2.js"></script>
		<script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
		<script type="text/javascript" src="../salonSoftFiles_new/js/jquery-form.js"></script>
		<script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/toastr.js/1.3.1/js/toastr.js"></script>
		<link rel="stylesheet" type="text/css" href="//cdnjs.cloudflare.com/ajax/libs/toastr.js/1.3.1/css/toastr.css">
	</head>
	<?php 
	    $toast = $_SESSION['t']; 
	    if($bgurl['loginbg'] != ''){
	        $background = $bgurl['loginbg'];
	    } else {
	        $background = '../salonSoftFiles_new/uploads/shutterstock_83871361.jpg';
	    }
    ?>
	<body <?php if($toast>0){ echo 'onload="myalert()"'; } ?> style="background-size:cover;background-position: center; background-repeat: no-repeat;background-image:url('<?= $background ?>');">
		<div class="login-wrapper">
			<div class="login">
			<form action="" method="post">
				<div class="login-header">
					<img src="<?php echo systemlogo($conn); ?>" alt="<?php echo systemname($conn); ?>" style="max-width:150px"/>
				</div>
				<div class="login-body">
					<div id="login_form">
						<div class="form-group">
							<label for="emailID">Username</label>
							<input id="emailID" type="text" name="user" required class="form-control" placeholder="Username">
						</div>
						<div class="form-group">
							<label for="password">Password</label>
							<input id="password" type="password" required name="pass" class="form-control" placeholder="Password">
						</div>
						<button class="btn btn-danger btn-block" name="signin" type="submit">Sign in</button>
					</div>
				</div>
               </form> 
			    <div class="clearfix"></div>
                <p class="text-left">Powered by <a href="https://fsnpos.com/" target="_blank">Fsnpos Solution</a><br>For support : info@fsnpos.com <br> +965 69992786 / +965 60952502</p>  
			</div>
		</div>
		<script>
			function myalert() {
				<?php 
				$t = $_SESSION['t'];
				if($t==1){
					echo 'toastr.success("'.$_SESSION['tmsg'].'");';
				}else if($t==2){
					echo 'toastr.error("'.$_SESSION['tmsg'].'", "Error");';
				}
				?>
				
				<?php $_SESSION['t'] = 0;
					$_SESSION['tmsg'] = 0;
				?>
			}
		</script>      
	</body>
</html>