<?php
if(!isset($_SESSION['t'])){
	$_SESSION['t']  = 0;
}
if(!isset($_SESSION['tmsg'])){
	$_SESSION['tmsg']  = "";
}
if(!$_SESSION['uid'] || (isset($_SESSION['uid'],$_SESSION['salon_uid']) && $_SESSION['salon_uid']!=$salon_uid)){
        header('LOCATION:logout.php');die();
}

$role = $_SESSION['u_role'];
 if(strpos($_SERVER['REQUEST_URI'],'services.php')||strpos($_SERVER['REQUEST_URI'],'products.php')||strpos($_SERVER['REQUEST_URI'],'editproduct.php')||strpos($_SERVER['REQUEST_URI'],'beauticians.php')||strpos($_SERVER['REQUEST_URI'],'editbeauticians.php')||strpos($_SERVER['REQUEST_URI'],'coupons.php')||strpos($_SERVER['REQUEST_URI'],'editcoupons.php')||strpos($_SERVER['REQUEST_URI'],'employees.php')||strpos($_SERVER['REQUEST_URI'],'editemployees.php')||strpos($_SERVER['REQUEST_URI'],'users.php')||strpos($_SERVER['REQUEST_URI'],'edituser.php')||strpos($_SERVER['REQUEST_URI'],'packages.php')||strpos($_SERVER['REQUEST_URI'],'vendor.php')||strpos($_SERVER['REQUEST_URI'],'editvendor.php')||strpos($_SERVER['REQUEST_URI'],'editpackage.php')){
	if($role==2){
            header('LOCATION:dashboard.php');die();
	}
 }

?>

<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="UTF-8" />
		<meta name='viewport' content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0' />

		<meta http-equiv="X-UA-Compatible" content="IE=edge" />
		
		<title>FSNPOS Solutions</title>
		<link href="../salonSoftFiles_new/css/ytLoad.jquery.css" rel="stylesheet" type="text/css">
		<!-- Bootstrap CSS -->
		<link href="../salonSoftFiles_new/css/bootstrap.min.css" media="screen" rel="stylesheet" />

		<!-- Main CSS -->
		<link href="../salonSoftFiles_new/css/timepicker.css" rel="stylesheet" />
		<link href="../salonSoftFiles_new/css/daterangepicker.css" rel="stylesheet" />
		<link href="../salonSoftFiles_new/css/main.css" rel="stylesheet" media="screen" />

		<!-- Ion Icons -->
		<link href="../salonSoftFiles_new/fonts/icomoon/icomoon.css" rel="stylesheet" />
		<link href="../salonSoftFiles_new/fonts/font-awesome.min.css" rel="stylesheet" />
		
		<!-- C3 CSS -->
		<link href="../salonSoftFiles_new/css/c3/c3.css" rel="stylesheet" rel="stylesheet" />

		<!-- Circliful CSS -->
		<link href="../salonSoftFiles_new/css/circliful/circliful.css" rel="stylesheet" />


		<link href='../salonSoftFiles_new/full_calendar/lib/fullcalendar.min.css' rel='stylesheet' />
		<link href='../salonSoftFiles_new/full_calendar/lib/fullcalendar.print.min.css' rel='stylesheet' media='print' />
		<link href='../salonSoftFiles_new/full_calendar/scheduler.min.css' rel='stylesheet' />	
		
		<link href="../salonSoftFiles_new/css/dialogify.css" id="dialogifyCss" rel="stylesheet" type="text/css">
		<link href="../salonSoftFiles_new/css/lightbox.min.css" rel="stylesheet" type="text/css">
		<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">
		<!-- <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-timepicker/0.5.2/css/bootstrap-timepicker.css"> -->


		<link rel="stylesheet" type="text/css" href="../salonSoftFiles_new/css/bootstrap-datetimepicker.css">


		<!--<link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.0.1/css/toastr.css" rel="stylesheet"/>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.0.1/js/toastr.js"></script>-->
		<!-- HTML5 shiv and Respond.js IE8 support of HTML5 elements and media queries -->
		<!--[if lt IE 9]>
			<script src="js/html5shiv.js"></script>
			<script src="js/respond.min.js"></script>
		<![endif]-->
		<script src="//code.jquery.com/jquery-latest.min.js" type="text/javascript"></script>
		<!-- Calendar CSS -->
		<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
		<script src="../salonSoftFiles_new/js/jquery.js"></script>
		<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
		<!--<script src="//code.jquery.com/jquery-3.3.1.js"></script>-->
		<!-- <script src="//code.jquery.com/jquery-1.10.2.js"></script> -->
		<script src="//code.jquery.com/ui/1.12.0/jquery-ui.js"></script>
		<link href="../salonSoftFiles_new/css/calendar/fullcalendar.min.css" rel="stylesheet" />
		<!--<link href="../salonSoftFiles_new/css/calendar/custom-calendar.css" rel="stylesheet" />-->
		 <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
		<script type="text/javascript" src="../salonSoftFiles_new/js/jquery-form.js"></script>
		<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.9/css/jquery.dataTables.min.css"/>
		<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.15/css/dataTables.bootstrap.min.css"/>
		<script type="text/javascript" src="https://cdn.datatables.net/1.10.9/js/jquery.dataTables.min.js"></script>
		<script type="text/javascript" src="https://cdn.datatables.net/1.10.15/js/dataTables.bootstrap.min.js"></script>
		
		
		<!--<script src="https://www.jqueryscript.net/demo/Dialog-Modal-Dialogify/dist/dialogify.min.js"></script>-->
		<script src="dialogify/dialogify.min.js"></script>
		<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/s/bs/jszip-2.5.0,pdfmake-0.1.18,dt-1.10.10,b-1.1.0,b-flash-1.1.0,b-html5-1.1.0,b-print-1.1.0,fh-3.1.0,sc-1.4.0/datatables.min.css">
        <script type="text/javascript" src="https://cdn.datatables.net/s/bs/jszip-2.5.0,pdfmake-0.1.18,dt-1.10.10,b-1.1.0,b-flash-1.1.0,b-html5-1.1.0,b-print-1.1.0,fh-3.1.0,sc-1.4.0/datatables.min.js"></script>
		
		<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/1.3.1/js/toastr.js"></script>
		<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/1.3.1/css/toastr.css">
		<!--<link rel="stylesheet" href="css/wickedpicker.css">
        <script type="text/javascript" src="js/wickedpicker.js"></script>-->
        <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
        <!-- <script type="text/javascript" src="../salonSoftFiles/js/bootstrap-timepicker.min.js"></script> -->
        <script type="text/javascript" src="../salonSoftFiles_new/js/bootstrap-datetimepicker.min.js"></script>
        
        <!-- New design css file -->
        <link href='../salonSoftFiles_new/packages/core/main.css' rel='stylesheet' />
        <link href='../salonSoftFiles_new/packages/daygrid/main.css' rel='stylesheet' />
        <link href='../salonSoftFiles_new/packages/timegrid/main.css' rel='stylesheet' />
        <link href='../salonSoftFiles_new/packages/list/main.css' rel='stylesheet' />
        <style>
            @media(max-width: 768px){
                .daterangepicker{
                    width: 318px!important;
                }
            }
        </style>
	</head>