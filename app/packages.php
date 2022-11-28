<?php
require_once '../includes/db_include.php';
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$arr=[];
$arr['success']=1;

$packages=query("select * from packages  where valid>='".date("Y-m-d")."' and active='0' and branch_id='".$branch_id."'",array(),$conn);

$branch_id = $_GET['branch_id'];

$system=query("select * from system",array(),$conn)[0];

if($packages){
    foreach($packages as $pack){
        $today=date("Y-m-d");
        $vlid=$pack['valid'];
		$days=$pack['duration'];
		$package_price = $pack['price'];
		$package_expiry_day=date('Y-m-d', strtotime($vlid. ' + '.$days.' days'));
        // $diff = strtotime($vlid) - strtotime($today); 
        // $days=abs(round($diff / 86400));
        $list_price=query("SELECT sum(ps.price) as ser_price from packageservice ps left join service s on s.id=SUBSTR(ps.service,4) LEFT JOIN `servicecat` c on c.id=ps.category where ps.pid='".$pack['id']."' and ps.active=0 order by ps.id asc",array(),$conn)[0]['ser_price'];
        $discount = round($list_price-$package_price,2);
        $save_persant = number_format($discount/$list_price*100,2);
        // $save_persant = number_format(100-($package_price*100/$list_price),2);
        // echo $discount."/".$list_price.'*100';
       
        $arr['packages'].='<div class="price-table-item animate-zoom  bill-salon">
    				<h1><i class="ion-ios-star"></i></h1>
    				<h2 class="color-black thiner">'.$pack['name'].'</h2>
    				<h3 class="color-green-dark">'.$pack['price'].'<em> Valid '.$days.' days</em></h3>
    				<h5 style="text-align:center;">Save upto '.$save_persant.'%</h5>
    				<div class="decoration no-bottom full-top"></div>
    				<ul class="price-table-list">'; ?>
    				<?php 
    			//	echo "SELECT s.price as service_price,CONCAT('sr,',s.id) as service_id,ps.pid,ps.category as cat,c.cat as cat_name,ps.service,ps.quantity,ps.price,s.price as ser_price,s.name as service_name from packageservice ps left join service s on s.id=SUBSTR(ps.service,4) LEFT JOIN `servicecat` c on c.id=ps.category where ps.pid='".$pack['id']."' order by ps.id asc";
    $service=query("SELECT s.price as service_price,CONCAT('sr,',s.id) as service_id,ps.pid,ps.category as cat,c.cat as cat_name,ps.service,ps.quantity,ps.price,s.price as ser_price,s.name as service_name from packageservice ps left join service s on s.id=SUBSTR(ps.service,4) LEFT JOIN `servicecat` c on c.id=ps.category where ps.pid='".$pack['id']."' and ps.active='0' order by ps.id asc",array(),$conn); ?>
    <?php if($service){ 
        foreach($service as $ser){	
            $arr['packages'].='<li>'.$ser['service_name'].'</li>'; 
        }
    }
    					
          $arr['packages'].='</ul>
    				<a href="javascript:void(0)" class="button bg-salon button-round uppercase bold button-xs" style="display:none;" onClick="check_login_logout('."'".$pack['name']."'".','."'".$pack['price']."'".','."'".$pack['id']."'".');">Purchase</a>
    		    </div><br>'; 

    }
}

if(empty($arr['packages'])){
    $arr['packages']="No package available";
}				
   echo json_encode($arr);
?>