<?php
session_start();
//require __DIR__ . '/vendor/autoload.php';
// Merchant key here as provided by Payu
//$MERCHANT_KEY = "JBZaLc";
$MERCHANT_KEY = "db3jMd";

error_reporting(E_ALL);
ini_set("display_errors", 1);

// Merchant Salt as provided by Payu
//$SALT = "GQs7yium";
$SALT = "0SiDlXnx";

// End point - change to https://secure.payu.in for LIVE mode
//$PAYU_BASE_URL = "https://test.payu.in";
$PAYU_BASE_URL = "https://secure.payu.in";

$action = 'index.php#services';

require_once '../../includes/db_include.php';
$customer_info=query("select * from client where cont='".$_GET['mobile']."' and active='0'",array(),$conn)[0];

echo "</pre>";
$admin_email = 'salesmanager@13designstreet.com';
$admin_name = 'Support';

$error_messages = [];
$success_messages = [];

if( isset($_SESSION['status'], $_SESSION['status_type']) ){
    $status_type = htmlspecialchars( $_SESSION['status_type'] );
    $order_no = htmlspecialchars( $_SESSION['order_no'] );

    if( $status_type === 'error' ){
        $error_messages[] = htmlspecialchars( $_SESSION['status'] );
        $status_html = "<html></body><h1>Transaction Failed: {$order_no}</h1></body></html>";
    }else{
        $success_messages[] = htmlspecialchars( $_SESSION['status'] );
        $status_html = "<html></body><h1>Transaction Successfull: {$order_no}</h1></body></html>";
    }
    unset($_SESSION['status'], $_SESSION['status_type'], $_SESSION['order_no']);
    
    
    
}

$posted = array();
if(!empty($_POST)) {
    //print_r($_POST);
    $order_no = uniqid();
    $_SESSION['order_no'] = $order_no;
    $html = "<html></body><h1>Indian Run Festival Purchase Order no: {$order_no}</h1><ol>";
    foreach($_POST as $key => $value) {   
        if( !is_array( $_POST[$key] ) ){
            if( trim( $_POST[$key] ) !== "" ){
                if( $key !== "key" && $key !== "surl" && $key !== "furl" ){
                    $html_label = ucfirst( str_replace("_", " ", $key) );
                    $html .= "<li>{$html_label}: {$_POST[$key]}</li>";
                }
            }
        } 
      
        $posted[$key] = $value; 
    }
    $html .= "</ol></body></html>";
}

$formError = 0;

if(empty($posted['txnid'])) {
  // Generate random transaction id
  $txnid = substr(hash('sha256', mt_rand() . microtime()), 0, 20);
} else {
  $txnid = $posted['txnid'];
}
$hash = '';
// Hash Sequence
$hashSequence = "key|txnid|amount|productinfo|firstname|email|udf1|udf2|udf3|udf4|udf5|udf6|udf7|udf8|udf9|udf10";
if(empty($posted['hash']) && sizeof($posted) > 0) {
/* echo "<pre>";
print_r($posted); die();
echo "</pre>"; */
  
  if(
          empty($posted['key'])
          || empty($posted['txnid'])
          || empty($posted['amount'])
          || empty($posted['firstname'])
          || empty($posted['email'])
          || empty($posted['phone'])
          || empty($posted['surl'])
          || empty($posted['furl'])
		  || empty($posted['service_provider'])
  ) {
    $formError = 1;
    $error_messages[] = "Please fill all the mendatory fields";
  } else {
    //$posted['productinfo'] = json_encode(json_decode('[{"name":"tutionfee","description":"","value":"500","isRequired":"false"},{"name":"developmentfee","description":"monthly tution fee","value":"1500","isRequired":"false"}]'));
	$hashVarsSeq = explode('|', $hashSequence);
    $hash_string = '';	
	foreach($hashVarsSeq as $hash_var) {
      $hash_string .= isset($posted[$hash_var]) ? $posted[$hash_var] : '';
      $hash_string .= '|';
    }

    $hash_string .= $SALT;


    $hash = strtolower(hash('sha512', $hash_string));
    $action = $PAYU_BASE_URL . '/_payment';
  }
} elseif(!empty($posted['hash'])) {
  $hash = $posted['hash'];
  $action = $PAYU_BASE_URL . '/_payment';
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/js/bootstrap.min.js"></script>
	<!-- core CSS -->
    
    <!--[if lt IE 9]>
    <script src="js/html5shiv.js"></script>
    <script src="js/respond.min.js"></script>
    <![endif]-->       
 
    <link href='https://fonts.googleapis.com/css?family=Cardo:400,700,400italic' rel='stylesheet' type='text/css'>
		<script>
		var hash = '<?php echo $hash ?>';
		function submitPayuForm() {
		  if(hash == '') {
			return;
		  }
		  
		  var payuForm = document.forms.payuForm;
		  payuForm.submit();
		}
	  </script>
</head><!--/head-->

<body id="home" class="homepage" onload="submitPayuForm()">

	


    <section id="services" >
        <div class="container">
                <h2 style="margin-left:20px;">Please fill your form</h2>
                <form method="post" id="purchaseForm" action="<?php echo $action; ?>" name="payuForm">
                    <input type="hidden" name="key" value="<?php echo $MERCHANT_KEY ?>" />
                    <input type="hidden" name="hash" value="<?php echo $hash ?>"/>
                    <input type="hidden" name="txnid" value="<?php echo $txnid ?>" />
                    <input type="hidden" name="amount" value="<?php echo (empty($posted['amount'])) ? $_GET['amount'] : $posted['amount'] ?>" />
                    
                    <input type="hidden" id="h_productInfo" name="productinfo" value="<?php echo (empty($posted['productinfo'])) ? $_GET['days'] : $posted['productinfo'] ?>" />
                    <input type="hidden" name="surl" value="<?php echo (empty($posted['surl'])) ? 'https://'.$_SERVER['SERVER_NAME'].'/easy_app/success.php' : $posted['surl'] ?>" size="64" />
                    <input type="hidden" name="furl" value="<?php echo (empty($posted['furl'])) ? 'https://'.$_SERVER['SERVER_NAME'].'/easy_app/failure.php' : $posted['furl'] ?>" size="64" />
                    <input type="hidden" name="service_provider" value="payu_paisa" size="64" />
                    <input type="hidden" name="student" class="form-control" placeholder="Student">
                    
                    <div class="form-group col-lg-4">
                        <input type="text" name="firstname" class="form-control" placeholder="Full Name" required="required" value="<?= ( empty( $posted['firstname'] ) ) ? $customer_info['name'] : $posted['firstname']; ?>">
                    </div>
                    <div class="form-group col-lg-8">
                        <input type="text" name="address" class="form-control" placeholder="Address" required="required" value="<?= ( empty( $posted['address'] ) ) ? $customer_info['address'] : $posted['address']; ?>">
                    </div>
                    <div class="form-group col-lg-3">
                        <input type="text" name="city" class="form-control" placeholder="City" required="required" >
                    </div>
                    <div class="form-group col-lg-3">
                       <input type="text" name="state" class="form-control" placeholder="State" required="required">
                    </div>
                    <div class="form-group col-lg-3">
                       <input type="text" name="pincode" class="form-control" placeholder="Pincode" required="required">
                    </div>
                    <div class="form-group col-lg-3">
                       <input type="text" name="country" class="form-control" placeholder="Country" required="required">
                    </div>
                    <div class="form-group col-lg-3">
                       <input type="text" name="phone" class="form-control" placeholder="Phone" required="required" value="<?php echo (empty($posted['phone'])) ? $customer_info['cont'] : $posted['phone']; ?>">
                   </div>
                    <div class="form-group col-lg-3">
                       <input type="email" name="email" class="form-control" placeholder="email" required="required" id="email" value="<?php echo (empty($posted['email'])) ? $customer_info['email'] : $posted['email']; ?>">
                   </div>
                    <div class="form-group col-lg-3">
                       <input type="text" name="company_name" class="form-control"   placeholder="Name of company / organisation where employed">
                   </div>
                    
                    
                    
                    
                    
                    
                    <?php if(!$hash) { ?>
                    <div class="form-group col-lg-4">  <label>&nbsp;</label><br>
                        <button type="submit" class="btn btn-primary wow rotateIn" data-wow-delay="200ms">Pay</button>
                    </div>
                    <?php  } ?>
                </form>
                            
                            
            
              
             
              
             
        </div><!--/.container-->
    </section><!--/#services-->




      





    <script src="https://ajax.aspnetcdn.com/ajax/jQuery/jquery-3.3.1.min.js"></script>
    
  
	<script type="text/javascript">
		$(function () {
			$("#race").change(function () {
				if ($(this).val() == "180 days package" || $(this).val() == "365 days package" ) {
					$(".dvPassport").show();
					$('#coupan_code').blur(function () {
						var code = $(this).val();
						
						function isInArray(value, array) {
						  return array.indexOf(value) > -1;
						}

						var couponCodes = [
							{
								'name': 'COUPON1',
								'value': 10
							},
							{
								'name': 'COUPON2',      
								'value': 10
							}
						];
						
						
						//var res = isInArray(code, valcodes);
						
						//console.log(couponCodes);
						
						var rate = null;
						couponCodes.forEach(function(i, v){
							console.log(i,v);
							if( i.name == code ){
								rate = i.value;
							}
						}); 
						
						if( rate != null ){
							
							var getCode = this.value;
							$("#divCheckPasswordMatch").html("Coupon Code match.");
							
							var currentAmount = $('#h_amount').val();
							
							var discount = ( currentAmount * rate ) / 100;
							
							var aAmount = currentAmount - discount;
							
							$('#h_amount').val(aAmount);
							
							console.log( $('#h_amount').val() );
							
							//console.log( rate + ' % discount');
						}else{
							$("#divCheckPasswordMatch").html("Coupon Code not match!");
							
							 var currentAmount = $('#h_amount').val();
							 $('#h_amount').val(currentAmount);
							 console.log( $('#h_amount').val() );
							//console.log('Coupon code not match');
						}
						
						/*
						console.log(valcodes);
						var res = valcodes.find('123');
						console.log(res);*/
					})
				} else {
					$(".dvPassport").hide();
				}
				
			});
		}); 
	</script>    
</body>
</html>