<?php
require_once '../includes/db_include.php';
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$data = json_decode(file_get_contents("php://input"), true);

$path="https://easysalon.in/new_salon_demo/app/images/";
  
$arr=[];
$arr['success']=1;
$system=query("select * from system",array(),$conn)[0];
$booking_dates=query("select * from app_bookings ab inner join app_bookings_clients abc on(ab.cid=abc.cid) where abc.contact_no='".$_GET['mobile']."' group by booking_date order by booking_date desc",array(),$conn);
 
$arr['alldata'].='<div class="sidebars sidebars-light">
		<div class="sidebar sidebar-left">
			<div class="sidebar-header sidebar-header-image bg-2">
				<div class="overlay dark-overlay"></div>
				<a href="index.html" class="sidebar-logo">
					<strong id="profile"></strong>
				</a>
			</div>
			<div class="menu-options icon-background no-submenu-numbers sidebar-menu">
				<a  href="index.html"><span>Welcome</span><i class="ion-record"></i></a>
				<a  href="offers.html"><span>Offers</span><i class="ion-record"></i></a>
				<a href="profile.html" class="active-item"><span>My Profile</span><i class="ion-record"></i></a>
				<a  href="book-appointment.html"><span>Book appointment</span><i class="ion-record"></i></a>
				<a   href="services.html"><span>Services</span><i class="ion-record"></i></a>
				<a   href="packages.html"><span>Packages</span><i class="ion-record"></i></a>
				<a  href="gallery.html"><span>Gallery</span><i class="ion-record"></i></a>
				<a  href="about.html"><span>About</span><i class="ion-record"></i></a>
				<a  href="reviews.html"><span>Reviews</span><i class="ion-record"></i></a>
				<a  href="location.html"><span>Location</span><i class="ion-record"></i></a>
				<a  href="login.html" id="login"><span>Login</span><i class="ion-record"></i></a>
				<a  href="logout.html" id="logout"><span>Logout</span><i class="ion-record"></i></a>
			</div>
		</div> 
		
	</div>
	
	<div class="header header-logo-center header-light">
		<a href="#" class="header-icon header-icon-1 hamburger-animated open-sidebar-left"></a>
		<a href="index.html" class="header-logo"></a>
		<a href="book-appointment.html" class="header-icon header-icon-4"><i class="ion-ios-cart"><span class="badge badge-secondary" style="color:white;margin-bottom:3px;" id="show_cart">1</span></i></a>     
	</div><div id="page-content" class="page-content" >
		<div id="page-content-scroll" class="header-clear"><!--Enables this element to be scrolled --> 
			<br>
			<div class="content no-bottom ">';
				if($booking_dates){
				foreach($booking_dates as $dates){
    				$arr['alldata'].='<div class="dropdown-menu">
                    					<a href="#" class="dropdown-item dropdown-toggle bg-salon"><em style="color:#fff;">ID: #'.$dates['id'].' : '.date("d M Y",strtotime($dates['booking_date'])).' </em><i class="ion-android-add"></i></a>
                    					<div class="dropdown-content bg-salon-silver">';
					$service=query("select * from app_bookings ab inner join app_bookings_clients abc on(ab.cid=abc.cid) inner join service s on(ab.service_id=s.id)  where abc.contact_no='".$_GET['mobile']."' and ab.booking_date='".$dates['booking_date']."'",array(),$conn);
					if($service){
					    foreach($service as $ser){ 
					        $status=empty($ser['confirm_status'])?"Confirmation Pending":"Confirmed";
					        $arr['alldata'].='<a href="#" class="dropdown-item"><em><strong>'.$ser['name'].'</strong></em><br><em> 0.'.$ser['duration'].' HRS  | INR '.round($ser['price'],2).'('.$status.')</em></a>';
					    }
					 }					
               $arr['alldata'].='</div>
                    				</div>'; 
				}}else{
				   $arr['alldata'].='<b style="color:white;text-align:center;"> No data available</b>'; 
				}
			
	$arr['alldata'].='</div>
		</div>		
	</div>';
    

   echo json_encode($arr);
?>