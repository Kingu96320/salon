<?php 
include "./includes/db_include.php";
if(!isset($_SESSION['branch_id'])){
    $branch_id = decrypt_url($_GET['invshopid']);
    if($branch_id == '' || $branch_id <= 0){
        header('LOCATION: index.php');
    }
} else {
    $branch_id = $_SESSION['branch_id'];
}
$approved = 2;
if(isset($_GET['encinv']) && $_GET['encinv'] != ''){
    $invoice_id = decrypt_url($_GET['encinv']);
}
if(isset($_GET['inv']) && $_GET['inv'] != ''){
    $invoice_id = $_GET['inv'];
    $approved = 1;
}


if($invoice_id != ''){
    $invoice_id = $invoice_id;
} else {
    $invoice_id = '';
}

// $check_status = query_by_id("SELECT count(*) as count FROM client_feedback WHERE invoice_id=:id and branch_id='".$branch_id."'",['id'=>$invoice_id],$conn)[0]['count'];
// if ($check_status == '1') {
//     die('You have already submitted your Feedback.');
// }
if(isset($_POST['submit'])){
	  $name                         =   addslashes(trim(htmlspecialchars($_POST['c_name'])));
	  $inv                         =   addslashes(trim(htmlspecialchars($_POST['inv'])));
	  $client_id                    =   addslashes(trim(htmlspecialchars($_POST['cid'])));
	  $email                        =   addslashes(trim(htmlspecialchars($_POST['email'])));
	  $review                       =   addslashes(trim(htmlspecialchars($_POST['review'])));
	  $overal_experience            =   addslashes(trim(htmlspecialchars($_POST['radio'])));
	  $timely_response              =   addslashes(trim(htmlspecialchars($_POST['radio1'])));
	  $our_support                  =   addslashes(trim(htmlspecialchars($_POST['radio2'])));
	  $overal_satisfaction          =   addslashes(trim(htmlspecialchars($_POST['radio3'])));
	  $customer_services_rating     =   addslashes(trim(htmlspecialchars($_POST['rating'])));
	  $customer_suggestion          =   addslashes(trim(htmlspecialchars($_POST['suggestion'])));
	  $query=query("INSERT INTO client_feedback set invoice_id=:invoice_id, name=:name, client_id = :client_id, email=:email, review=:review, overall_exp=:overall_exp, timely_response=:timely_response, our_support=:our_support, overall_satisfaction=:overall_satisfaction, customer_service_rating=:customer_service_rating, suggestion=:suggestion, approve_status = :approve, branch_id='".$branch_id."'",
	  ['invoice_id'=>$inv,
	    'name'=>$name,
	    'client_id'=>$client_id,
		'email'=>$email,
		'review'=>$review,
		'overall_exp'=>$overal_experience,
		'timely_response'=>$timely_response,
		'our_support'=>$our_support,
		'overall_satisfaction'=>$overal_satisfaction,
		'customer_service_rating'=>$customer_services_rating,
		'suggestion'=>$customer_suggestion,
		'approve' => $approved
	  ],$conn);
	    if(isset($_GET['encinv'])){
	        echo "Thank you for your valuable feedback.";
	    } else {
    	    $_SESSION['feedback_success'] = "success";
	        header('LOCATION:client_feedback.php?type=padminrat');
	    }
	  }	
	
?>

<!DOCTYPE html>
<html>
<head>
<title>Feedback</title>
<!-- for-mobile-apps -->
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" /> 
<meta name="keywords" content="Feedback Widget Responsive, Login form web template, Sign up Web Templates, Flat Web Templates, Login signup Responsive web template, Smartphone Compatible web template, free webdesigns for Nokia, Samsung, LG, SonyEricsson, Motorola web design" />
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
	<h1>Feedback</h1>
	<div class="main" style="margin-top:15px;">
	    <?php if($invoice_id != '') { 
	        $get_username = query_by_id("SELECT count(*) as total, c.* FROM invoice_".$branch_id." i LEFT JOIN client c ON c.id=i.client WHERE i.id = '".$invoice_id."' AND i.active = 0 and i.branch_id='".$branch_id."'",[],$conn)[0];
	        if($get_username['total'] > 0){
	        ?>
		    <form method="post">
			<h5>Your Name</h5>
				<input type="text" name="c_name" placeholder="Enter Your Name" value="<?= $get_username['name']; ?>" required />
				<input type="hidden" name="cid" value="<?= $get_username['id']; ?>" />
			<h5>Email</h5>
				<input type="email" name="email" placeholder="Enter Your Email" value="<?= $get_username['email']; ?>" /><br>
			<h5>Your Review </h5>	
				<textarea name="review" id="review" required=""></textarea>
				<p>
				    <span style="float:right;float: right;margin-top: -23px;font-size: 13px;margin-right: 3px;color: #a7a7a7;">
				        <span id="review_text_lenght">0</span>/300
				    </span>
				</p>
			
		<h5>Overall Experience</h5>
			<div class="radio-btns">
					<div class="swit">								
						<div class="check_box_one"> <div class="radio"> <label><input type="radio" name="radio" value="Very Good" checked=""><i></i>Very Good</label> </div></div>
                        <div class="check_box"> <div class="radio"> <label><input type="radio" name="radio" value="Good"><i></i>Good</label> </div></div>
						<div class="check_box"> <div class="radio"> <label><input type="radio" name="radio" value="Fair"><i></i>Fair</label> </div></div>
						<div class="check_box"> <div class="radio"> <label><input type="radio" name="radio" value="Poor"><i></i>Poor</label> </div></div>
						<div class="clear"></div>
					</div>
			</div>
		<h5>Timely Response</h5>
			<div class="radio-btns">
					<div class="swit">								
						<div class="check_box_one"> <div class="radio1"> <label><input type="radio" name="radio1" value="Very Good" checked=""><i></i>Very Good</label> </div></div>
                        <div class="check_box"> <div class="radio1"> <label><input type="radio" name="radio1" value="Good"><i></i>Good</label> </div></div>
						<div class="check_box"> <div class="radio1"> <label><input type="radio" name="radio1" value="Fair"><i></i>Fair</label> </div></div>
						<div class="check_box"> <div class="radio1"> <label><input type="radio" name="radio1" value="Poor"><i></i>Poor</label> </div></div>
						<div class="clear"></div>
					</div>
			</div>
		<h5>Our Support</h5>
			<div class="radio-btns">
					<div class="swit">								
						<div class="check_box_one"> <div class="radio2"> <label><input type="radio" name="radio2" value="Very Good" checked=""><i></i>Very Good</label> </div></div>
                        <div class="check_box"> <div class="radio2"> <label><input type="radio" name="radio2" value="Good"><i></i>Good</label> </div></div> 
						<div class="check_box"> <div class="radio2"> <label><input type="radio" name="radio2" value="Fair"><i></i>Fair</label> </div></div>
						<div class="check_box"> <div class="radio2"> <label><input type="radio" name="radio2" value="Poor"><i></i>Poor</label> </div></div>
						<div class="clear"></div>
					</div>
			</div>
		<h5>Overall Satisfaction</h5>
			<div class="radio-btns">
					<div class="swit">								
						<div class="check_box_one"> <div class="radio3"> <label><input type="radio" name="radio3" value="Very Good" checked=""><i></i>Very Good</label> </div></div>
                        <div class="check_box"> <div class="radio3"> <label><input type="radio" name="radio3" value="Good"><i></i>Good</label> </div></div>
						<div class="check_box"> <div class="radio3"> <label><input type="radio" name="radio3" value="Fair"><i></i>Fair</label> </div></div>
						<div class="check_box"> <div class="radio3"> <label><input type="radio" name="radio3" value="Poor"><i></i>Poor</label> </div></div>
						<div class="clear"></div>
					</div>
			</div>
		<h5>Want to rate with us for customer services?</h5>
			<span class="starRating">
				<input id="rating5" type="radio" name="rating" value="5" checked>
				<label for="rating5">5</label>
				<input id="rating4" type="radio" name="rating" value="4">
				<label for="rating4">4</label>
				<input id="rating3" type="radio" name="rating" value="3" >
				<label for="rating3">3</label>
				<input id="rating2" type="radio" name="rating" value="2">
				<label for="rating2">2</label>
				<input id="rating1" type="radio" name="rating" value="1">
				<label for="rating1">1</label>
			</span>
			
			<h5>Is there anything you would like to tell us?</h5>	
				<textarea id="othernotes" required="" name="suggestion"> </textarea>
				<p>
				    <span style="float:right;float: right;margin-top: -23px;font-size: 13px;margin-right: 3px;color: #a7a7a7;">
				        <span id="othernotes_text_lenght">0</span>/300
				    </span>
				</p>
				<input type="submit" name="submit" value="Send Feedback"><br />
				<?php if(!isset($_GET['encenv'])){ ?>
				    <p style="margin-top:5px;"><a style="color:#333;" href="client_feedback.php?type=padminrat">Go Back</a></p>
				<?php } ?>
		</form>
	    <?php } else if(isset($_GET['inv'])){
	            ?>
    	   <form action="" method="get">
    	            <span style="color:#f00;">Invalid invoice number</span>
    	            <h5>Enter invoice number :</h5>
    	            <input type="text" class="form-control" name="inv" value="<?= $invoice_id; ?>" placeholder="Invoice id" required />
    	            <input type="submit" name="verify" value="Submit">
	        </form>
	            <?php
	        } 
	    } else if(isset($_GET['type']) && $_GET['type'] == 'padminrat'){ ?>
	        <form action="" method="get">
	            <?php if(isset($_SESSION['feedback_success']) && $_SESSION['feedback_success'] != ''){
	                echo "<p style='color:#fff;padding:10px 5px;margin-bottom:10px;background-color:#239a23;border-radius:3px;'>Thank you for you valuable feedback.</p>";
	                $_SESSION['feedback_success'] = '';
	            }?>
	            <h5>Enter invoice number :</h5>
	            <input type="text" class="form-control" name="inv" placeholder="Invoice number" required />
	            <input type="submit" name="verify" value="Submit">
	        </form>
	    <?php } else { 
	        echo "<p style='text-align:center'>Feedback not allowed, please contact to admin</p>";
	    }
	    ?>
	</div>
	
</div>

<script>
    $('#review').keyup(function(e){
		textlength('review', 'review_text_lenght',e);
	});
	
	$('#othernotes').keyup(function(e){
		textlength('othernotes', 'othernotes_text_lenght',e);
	});
	
	// function to check text lenght in textare and remove more the limit
	function textlength(textareaid, counterid,e){
		var textarea = $('#'+textareaid).val();
		var textlength = textarea.length;
		var set = 300;
		var remain = parseInt(set - textlength);
	    $('#'+counterid).text(textlength);
	    if(e == undefined){
	    	if(parseInt(textlength) > set){
				$('#'+textareaid).val((textarea).substring(0, textlength - Math.abs(remain)));
			    $('#'+counterid).text(textlength - Math.abs(remain));
			    return false;
			}
	    } else {
	    	if (remain <= 0 && e.which !== 0 && e.charCode === 0) {
		        $('#'+textareaid).val((textarea).substring(0, textlength - Math.abs(remain)));
		        $('#'+counterid).text(textlength - Math.abs(remain));
		        return false;
		    }
	    }
	}
</script>
</body>
</html>
