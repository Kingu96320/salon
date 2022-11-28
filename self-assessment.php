<?php 
    include "./includes/db_include.php";
    $branch_id = $_SESSION['branch_id'];
    if(!$_SESSION['uid'] || (isset($_SESSION['uid'],$_SESSION['salon_uid']) && $_SESSION['salon_uid']!=$salon_uid)){
        header('LOCATION:logout.php');die();
    }
    
    if(isset($_POST['submit'])){
        $name = htmlspecialchars(trim($_POST['c_name']));
        $email = htmlspecialchars(trim($_POST['email']));
        $phone = htmlspecialchars(trim($_POST['c_phone']));
        $address = htmlspecialchars(trim($_POST['address']));
        $q1 = htmlspecialchars(trim($_POST['q1']));
        $q2 = htmlspecialchars(trim($_POST['q2']));
        $q3 = htmlspecialchars(trim($_POST['q3']));
        if($name == '' || $email == '' || $phone == '' || $address =='' || $q1 == '' || $q2 == '' || $q3 == ''){
            echo '<script>alert("All fields are required.")</script>';
        } else {
            query("INSERT INTO self_assessment SET name='".$name."', email='".$email."', phone='".$phone."', address='".$address."', q1='".$q1."', q2='".$q2."', q3='".$q3."', branch_id='".$branch_id."'",[],$conn);
            echo '<script>alert("Form submitted successfully")</script>';
            echo '<meta http-equiv="refresh" content="0; url=self-assessment.php" />';die();
        }
    }
?>
<!DOCTYPE html>
<html>
<head>
<title>Self-Assessment Form</title>
<!-- for-mobile-apps -->
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" /> 
<meta name="keywords" content="" />
<!-- //for-mobile-apps -->
<link href='//fonts.googleapis.com/css?family=Amaranth:400,400italic,700,700italic' rel='stylesheet' type='text/css'>
<link href='//fonts.googleapis.com/css?family=Josefin+Slab:400,100,100italic,300,300italic,400italic,600,600italic,700,700italic' rel='stylesheet' type='text/css'>
<link href='//fonts.googleapis.com/css?family=Open+Sans:400,300,300italic,400italic,600,600italic,700,700italic,800,800italic' rel='stylesheet' type='text/css'>
<link href="abc/style.css" rel="stylesheet" type="text/css" media="all" />
<script src="../salonSoftFiles_new/js/jquery.js"></script>
</head>
<body>
<div class="content">
    <img src="<?php echo systemlogo($conn); ?>" alt="<?php echo systemname($conn); ?>" style="max-width:150px;margin:0px auto;display:block;background-color:#fff;padding:5px;border-radius:5px;"/>
	<br />
	<h1>Self-Assessment Form</h1>
	<div class="main" style="margin-top:15px;">
	    <form method="post">
		<h5>Name</h5>
		<input type="text" name="c_name" placeholder="Your Name" value="" required />
		<h5>Email</h5>
		<input type="email" name="email" placeholder="Your Email" value="" required /><br>
		<h5>Phone number</h5>
		<input type="text" name="c_phone" placeholder="Your phone number" onkeyup="this.value = this.value.replace(/[^0-9]/g,'');" maxlength="<?= PHONE_NUMBER ?>" value="" required />
		<h5>Address</h5>
		<textarea name="address" id="address" required></textarea><br>
	    <h5>Have you been to one of the COVID-19 affected countries in the last 14 days?</h5>
		<div class="radio-btns">
			<div class="swit">								
				<div class="check_box_one">
				    <div class="radio">
				        <label><input type="radio" name="q1" value="Yes"><i></i>Yes</label>
				    </div>
				</div>
                <div class="check_box">
                    <div class="radio">
                        <label><input type="radio" name="q1" value="No"><i></i>No</label>
                    </div>
                </div>
				<div class="clear"></div>
			</div>
		</div>
		<h5>Have you been in close contact with a confirmed case of coronavirus?</h5>
		<div class="radio-btns">
			<div class="swit">								
				<div class="check_box_one">
				    <div class="radio">
				        <label><input type="radio" name="q2" value="Yes"><i></i>Yes</label>
				    </div>
				</div>
                <div class="check_box">
                    <div class="radio">
                        <label><input type="radio" name="q2" value="No"><i></i>No</label>
                    </div>
                </div>
				<div class="clear"></div>
			</div>
		</div>
		<h5>Are you currently experiencing symptoms (cough, shortness of breath, fever)</h5>
		<div class="radio-btns">
			<div class="swit">								
				<div class="check_box_one">
				    <div class="radio">
				        <label><input type="radio" name="q3" value="Yes"><i></i>Yes</label>
				    </div>
				</div>
                <div class="check_box">
                    <div class="radio">
                        <label><input type="radio" name="q3" value="No"><i></i>No</label>
                    </div>
                </div>
				<div class="clear"></div>
			</div>
		</div>
		<input type="submit" name="submit" value="Submit"><br />
		</form>
	</div>
</div>
</body>
</html>