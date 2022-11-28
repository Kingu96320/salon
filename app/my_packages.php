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
$cid=$_GET['cid'];
$sql5= "SELECT count(cpsu.c_pack_id) as pack_count, GROUP_CONCAT(cpsu.inv) as inv,p.name as package_name,p.valid,p.price,s.name as service_name,cpsu.c_service_id,cpsu.client_id,cpsu.c_pack_id,sum(cpsu.quantity) - sum(cpsu.quantity_used) as qt,cpsu.quantity_used FROM `client_package_services_used` cpsu "
								." LEFT JOIN `packages` p on p.id=cpsu.c_pack_id "
								." LEFT JOIN `service` s on s.id=SUBSTRING_INDEX(cpsu.c_service_id,',',-1)"
								." where cpsu.active='1' and cpsu.client_id='".$_GET['cid']."' GROUP BY cpsu.c_pack_id";

$result5=query_by_id($sql5,[],$conn);
$arr['alldata'].='<div class="sidebars sidebars-light">
		<div class="sidebar sidebar-left">
			<div class="sidebar-header sidebar-header-image bg-2">
				<div class="overlay dark-overlay"></div>
				
				<a href="index.html" class="sidebar-logo">
					<strong></strong>
				</a>
			</div>
			
			<div class="menu-options icon-background no-submenu-numbers sidebar-menu">
				
				<a  href="index.html"><span>Welcome</span><i class="ion-record"></i></a>
				<a  href="offers.html"><span>Offers</span><i class="ion-record"></i></a>
				<a  class="active-item" href="profile.html"><span>My Profile</span><i class="ion-record"></i></a>
				<a  href="book-appointment.html"><span>Book appointment</span><i class="ion-record"></i></a>
				<a   href="services.html"><span>Services</span><i class="ion-record"></i></a>
				<a   href="packages.html"><span>Packages</span><i class="ion-record"></i></a>
				<a  href="gallery.html"><span>Gallery</span><i class="ion-record"></i></a>
				<a  href="about.html"><span>About</span><i class="ion-record"></i></a>
				<a  href="reviews.html"><span>Reviews</span><i class="ion-record"></i></a>
				<a  href="location.html"><span>Location</span><i class="ion-record"></i></a>
				
				
				
				
				
			</div>
		</div> 
		
	</div>
	
	<div class="header header-logo-center header-light">
		<a href="#" class="header-icon header-icon-1 hamburger-animated open-sidebar-left"></a>
		<a href="index.html" class="header-logo"></a>
		<a href="tel:62649-08825 " class="header-icon header-icon-4"><i class="ion-ios-telephone"></i></a>    
	</div>
	
	<div id="page-content" class="page-content">	
		<div id="page-content-scroll" class="header-clear"><!--Enables this element to be scrolled --> 

		
			
			<br>
			
			
			
			<div class="content no-bottom ">';
			
		
				
	
$sql5= "SELECT count(cpsu.c_pack_id) as pack_count, GROUP_CONCAT(cpsu.inv) as inv,p.name as package_name,p.valid,p.price,s.name as service_name,cpsu.c_service_id,cpsu.client_id,cpsu.c_pack_id,sum(cpsu.quantity) - sum(cpsu.quantity_used) as qt,cpsu.quantity_used FROM `client_package_services_used` cpsu "
		." LEFT JOIN `packages` p on p.id=cpsu.c_pack_id "
		." LEFT JOIN `service` s on s.id=SUBSTRING_INDEX(cpsu.c_service_id,',',-1)"
		." where cpsu.active='1' and cpsu.client_id='".$cid."' GROUP BY cpsu.c_pack_id";
		$packages=query_by_id($sql5,[],$conn);
if($packages){
    foreach($packages as $pack){
        $today=date("Y-m-d");
        $vlid=$pack['valid'];
        $diff = strtotime($vlid) - strtotime($today); 
        $days=abs(round($diff / 86400));
        $list_price=query("SELECT sum(s.price) as ser_price from packageservice ps left join service s on s.id=SUBSTR(ps.service,4) LEFT JOIN `servivcecat` c on c.id=ps.category where ps.pid='".$pack['c_pack_id']."' and ps.active=0 order by ps.id asc",array(),$conn)[0]['ser_price'];
        $discount =round($list_price-$pack['price'],2);
        $save_persant=round(($discount/$list_price)*100,2);
       
        $arr['alldata'].='<div class="price-table-item price-table-full bg-white">
    				<h1><i class="ion-ios-star"></i></h1>
    				<h2 class="color-black thiner">'.$pack['package_name'].'</h2>
    				<h3 class="color-green-dark">'.$pack['price'].'<em> Validity '.date("d m Y",strtotime($pack['valid'])).'</em></h3>
    				<h5 style="text-align:center;">Save upto '.$save_persant.'%</h5>
    				<div class="decoration no-bottom full-top"></div>
    				<ul class="price-table-list">'; ?>
    <?php $sql5="SELECT Distinct ps.pid,ps.service as c_service_id ,ps.quantity,p.name as package_name ,s.name as service_name FROM `packageservice`ps "
										." LEFT JOIN `packages` p on p.id=ps.pid"
										." LEFT JOIN `service` s on s.id=SUBSTRING_INDEX(ps.service,',',-1)"
										." where pid in(SELECT cpsu.c_pack_id FROM `client_package_services_used` cpsu where cpsu.client_id='".$cid."' and cpsu.c_pack_id='".$pack['c_pack_id']."' GROUP BY cpsu.c_pack_id) and ps.active='0' ORDER BY ps.pid ASC";
			$service=query_by_id($sql5,[],$conn); ?>
    <?php if($service){ foreach($service as $ser){	
        $sum_total_quantity=sum_total_quantity($ser['pid'],$cid,$ser['c_service_id']);
		$remain=$sum_total_quantity - (service_availed($ser['pid'],$_GET['cid'],$ser['c_service_id']));
        if($remain==0){
        $arr['alldata'].='<li><DEL>'.$ser['service_name'].'</DEL></li>'; 
        }else{
        $arr['alldata'].='<li>'.$ser['service_name'].'</li>'; 
        }    
            
        }}
    					
          $arr['alldata'].='</ul>
    			
    		    </div><br>'; 

    
    }
}


			
			

			
			
			       
	$arr['alldata'] .='</div>

		
			
			
			
			
			

			

			
		
		</div>		

		
	</div>';
    	function sum_total_quantity($pid,$cid,$ser_id){
			global $conn;
			$sql="SELECT sum(cpsu.quantity) - sum(cpsu.quantity_used) as qt FROM `client_package_services_used` cpsu "
			." where cpsu.client_id='$cid' and cpsu.active='1' and cpsu.c_service_id='$ser_id' and cpsu.c_pack_id='$pid'";
			$result=query_by_id($sql,[],$conn)[0];
			$used_quantity_count=0;
			if($result['qt'] > 0){
				return  $result['qt'];
			}
		}	
		
		
		function service_availed($pid,$cid,$ser){
			global $conn;
			
			$sql="SELECT sum(ii.quantity) as used_quantity FROM `invoice_items` ii where ii.client='$cid' and service = '$ser' and active='0' and package_id='$pid'  GROUP BY  ii.package_id";
			$used_quantity=query_by_id($sql,[],$conn)[0];
			if($used_quantity['used_quantity'] > 0){
				// foreach($used_quantity as $row_used_quantity){
					$used_quantity_count +=$used_quantity['used_quantity'];
				// }
			}
			
			if ($used_quantity_count > 0) {
				return $used_quantity_count;
				}else{
				return "0";
			}
		}
		
	

   echo json_encode($arr);
?>