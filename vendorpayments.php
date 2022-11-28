<?php
include_once './includes/db_include.php';
$branch_id = $_SESSION['branch_id'];
$date 		= date('Y-m-d');
$pay 		= $_POST['pay'];
$amnt 		= $_POST['amnt'];
$mode 		= $_POST['mode'];
$pend 		= $_POST['pend'];
$notes 		= $_POST['notes'];
$client 	= $_POST['client'];
$purchase_id=$_POST['purchase_id'];	
$bill_id	=$_POST['bill_id'];
query("INSERT INTO `payments`(`bill_id`,`purchase_id`,`vendor`,`paid`,`payment`,`pend`,`date`,`mode`,`notes`,`active`,`debit`,`branch_id`) VALUES ('$bill_id','$purchase_id','$client','$amnt','$pay','$pend','$date','$mode','$notes',0,'$amnt','$branch_id')",[],$conn);
echo "Payment Accepted Successfully";
?>