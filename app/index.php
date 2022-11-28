<?php
require_once '../includes/db_include.php';
require_once 'path.php';

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$arr=[];
$system=query("select * from system",array(),$conn)[0];

        $sliders=query("select * from app_slider where status=1",array(),$conn);
     
      
		if($sliders){
            foreach($sliders as $sli){
              $arr['main_slider'] .= '<div class="swiper-slide">
									<img src="'.$path.$sli['name'].'"  class="responsive-image" alt="img">
								</div>';  
            }
        }				
		
		
		 $sliders=query("select ass.*,s.duration from app_slider_services ass LEFT JOIN service s on s.id=ass.service_id where ass.status=1",array(),$conn);
        
        if($sliders){
            foreach($sliders as $sli){
              $arr['featured_services'] .='<a class="swiper-slide" href="book-appointment.html" >
								<img class="responsive-image" src="'.$path.$sli['featured_image'].'" id="service1" alt="img" >
								<em id="ser_title1">'.$sli['service_name'].'</em>
								<strong id="ser_price1">Rs.'.number_format($sli['price'],2).'</strong>
							</a>';    
            }
        }				
		 
						
	$offer=query("select name from app_offer where status=1",array(),$conn)[0]['name'];  
        $arr['offer'] .= '<img src="'.$path.$offer.'" class="responsive-image rounded shadow-salon">';					
					
                 
    $arr['success']=1;
	
    echo json_encode($arr);
?>