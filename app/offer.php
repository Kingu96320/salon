<?php
require_once '../includes/db_include.php';
require_once 'path.php';

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
$arr=[];
    $offers=query("select * from app_offer where  status=1",array(),$conn) ;   
        if($offers){
            foreach($offers as $off){
                  $arr['offers'] .= '<div class="offer shadow-salon">
            			<h3>'.$off['offer_name'].'<h3>
            			<img src="'.$path.$off['name'].'" class="responsive-image">
            			<p>'.$off['offer_desc'].'</p>
            		</div>';
            }
        }
        
     
$arr['success']=1;
echo json_encode($arr);
?>