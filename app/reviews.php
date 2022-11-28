<?php
require_once '../includes/db_include.php';
require_once 'path.php';

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
  $system=query("select * from system",array(),$conn)[0]; 
$arr=[];
$arr['success']=1;

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
				<a  href="profile.html"><span>My Profile</span><i class="ion-record"></i></a>
				<a  href="book-appointment.html"><span>Book appointment</span><i class="ion-record"></i></a>
				<a  href="services.html"><span>Services</span><i class="ion-record"></i></a>
				<a  href="packages.html"><span>Packages</span><i class="ion-record"></i></a>
				<a  href="gallery.html"><span>Gallery</span><i class="ion-record"></i></a>
				<a  href="about.html"><span>About</span><i class="ion-record"></i></a>
				<a  href="reviews.html" class="active-item"><span>Reviews</span><i class="ion-record"></i></a>
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
	</div><div id="page-content" class="page-content" style="background: rgb(237,78,136); /* Old browsers */
background: -moz-linear-gradient(-45deg, rgb(237,78,136) 0%, rgb(240,48,47) 100%); /* FF3.6-15 */
background: -webkit-linear-gradient(-45deg, rgb(237,78,136) 0%,rgb(240,48,47) 100%); /* Chrome10-25,Safari5.1-6 */
background: linear-gradient(135deg, rgb(237,78,136) 0%,rgb(240,48,47) 100%); /* W3C, IE10+, FF16+, Chrome26+, Opera12+, Safari7+ */
filter: progid:DXImageTransform.Microsoft.gradient( startColorstr="#ed4e88", endColorstr="#f0302f",GradientType=1 ); /* IE6-9 fallback on horizontal gradient */">	
		<div id="page-content-scroll" class="header-clear"><!--Enables this element to be scrolled --> 

		
			
			<br>
			
			
			
			<div class="content no-bottom ">
			
			
		
			
			<div class="one-half-responsive last-column animate-bottom animate-time-1000">';
			$client=query("SELECT * FROM `reviews` r inner join  client c on(r.cid=c.id) WHERE r.status=1",array(),$conn);	
			if($client){
			 foreach($client as $cl){
			$arr['alldata'].='<div class="quote-2-left container">
						<i class="ion-quote"></i>
						<p>
							'.$cl['review'].'<br>
							- 	'.$cl['name'].'
						</p>
						
					</div>
					<div class="decoration"></div>';
			}}
					
		$arr['alldata'].='</div>
				
				
				
			
			
			<a href="book-appointment.html" class="button button-icon" style="background:#000; width:100%;"><i class="ion-ios-arrow-right"></i>Book appointment</a>

					
					
			

			
			
			       
			</div>

		
			
			
			
			
			

			

			
		
		</div>		

		
	</div>
	';
    

   echo json_encode($arr);
?>