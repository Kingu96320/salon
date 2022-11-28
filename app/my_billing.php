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
$mobile=isset($_GET['mobile'])?$_GET['mobile']:0;
$billings= query_by_id("SELECT DISTINCT cou.discount,cou.discount_type,cou.max_amount,cou.min_amount,cou.ccode as coupon_name,i.gst,i.coupon as coupon_id,i.paid as bill_paid,i.advance,i.dis,i.doa,i.id,i.total,i.pay_method,i.subtotal,CONCAT(i.tax,',',i.taxtype) as tax_type,i.status,i.dis as total_discount,c.name,c.id as c_id,c.cont,c.gender from `invoice` i "
		." LEFT JOIN `client` c on c.id=i.client "
		." LEFT join `invoice_items` ii on ii.iid=i.id "
		." LEFT JOIN `beauticians` b on b.id=ii.staffid "
		." LEFT JOIN `coupons` cou on cou.id=i.coupon "
		." where i.active=0 and i.type <> 3 and c.cont='".$mobile."' order by i.id desc",[],$conn);
 
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
				<a  href="profile.html" class="active-item"><span>My Profile</span><i class="ion-record"></i></a>
				<a  href="book-appointment.html"><span>Book appointment</span><i class="ion-record"></i></a>
				<a  href="services.html"><span>Services</span><i class="ion-record"></i></a>
				<a  href="packages.html"><span>Packages</span><i class="ion-record"></i></a>
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
				if($billings){
				foreach($billings as $dates){
				    $b_id=$dates['id'];
				    $sql2="SELECT ii.package_id,imsp.ii_id,imsp.service_Provider,s.name,b.id b_id,ii.client,ii.service,ii.quantity,ii.disc_row,ii.staffid,ii.price,s.price as service_price,s.name as service_name,s.cat,CONCAT('sr,',s.id) as service_id,s.duration as service_durr,scat.cat as cat_name from `invoice_items` ii"
												." LEFT JOIN `service` s on s.id=SUBSTRING_INDEX(ii.service,',',-1)"
												." LEFT JOIN `servivcecat` scat on scat.id=s.cat"
												." LEFT JOIN `invoice_multi_service_provider` imsp on imsp.ii_id=ii.id"
												." LEFT JOIN `beauticians` b on b.id=imsp.service_Provider "
												." where ii.iid='$b_id' and ii.active=0 and ii.type='Service' GROUP BY ii_id" 
												." UNION SELECT ii.package_id,imsp.ii_id,imsp.service_Provider,s.name,b.id b_id,ii.client,ii.service,ii.quantity,ii.disc_row,ii.staffid,ii.price,s.price as service_price,s.name as service_name,'0' as cat,CONCAT('pr,',s.id) as service_id,'0' as service_durr,0 as cat_name from `invoice_items` ii"
												." LEFT JOIN `products` s on s.id=SUBSTRING_INDEX(ii.service,',',-1)"
												." LEFT JOIN `invoice_multi_service_provider` imsp on imsp.ii_id=ii.id"
												." LEFT JOIN `beauticians` b on b.id=imsp.service_Provider "
												." where ii.iid='$b_id' and ii.active=0 and ii.type='Product' GROUP BY ii_id"
												." UNION SELECT ii.package_id,imsp.ii_id,imsp.service_Provider,s.name,b.id b_id,ii.client,ii.service,ii.quantity,ii.disc_row,ii.staffid,ii.price,s.price as service_price,s.name as service_name,'0' as cat,CONCAT('pa,',s.id) as service_id,'0' as service_durr,0 as cat_name from `invoice_items` ii"
												." LEFT JOIN `packages` s on s.id=SUBSTRING_INDEX(ii.service,',',-1)"
												." LEFT JOIN `invoice_multi_service_provider` imsp on imsp.ii_id=ii.id"
												." LEFT JOIN `beauticians` b on b.id=imsp.service_Provider "
												." where ii.iid='$b_id' and ii.active=0 and ii.type='Package'  GROUP BY ii_id";
												$services=query_by_id($sql2,[],$conn);
				    $arr['alldata'].='<div class="dropdown-menu">
                    					<a href="#" class="dropdown-item dropdown-toggle bg-salon"><em style="color:#fff;">ID: #'.$dates['id'].' : '.date("d M Y",strtotime($dates['doa'])).' </em><i class="ion-android-add"></i></a>
                    					<div class="dropdown-content bg-salon-silver">';
					if($services){
					    foreach($services as $service){
					    $status="Confirmed";
					    $arr['alldata'].='<a href="#" class="dropdown-item"><em><strong>'.$service['name'].'</strong></em><br><em> '.$service['duration'].'.hh HRS  | INR '.number_format($service['price'],2).'</em></a>';
					
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