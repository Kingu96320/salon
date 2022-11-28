<?php
require_once '../includes/db_include.php';
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

require_once './path.php';
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$data = json_decode(file_get_contents("php://input"), true);
$system=query("select * from system",array(),$conn)[0]; 

$arr=[];

$arr['success']=1;

$system=query("select * from system",array(),$conn)[0]; 

$mobile = isset($_GET['mobile'])?$_GET['mobile']:0;
$branch_id = isset($_GET['branch_id'])?$_GET['branch_id']:0;

if(!empty($mobile)){
    $billings= query_by_id("SELECT DISTINCT cou.discount,cou.discount_type,cou.max_amount,cou.min_amount,cou.ccode as coupon_name,i.gst,i.coupon as coupon_id,i.paid as bill_paid,i.advance,i.dis,i.doa,i.id,i.total,i.pay_method,i.subtotal,CONCAT(i.tax,',',i.taxtype) as tax_type,i.status,i.dis as total_discount,c.name,c.id as c_id,c.cont,c.gender from `invoice_".$branch_id."` i "
		." LEFT JOIN `client` c on c.id=i.client "
		." LEFT join `invoice_items_".$branch_id."` ii on ii.iid=i.id "
		." LEFT JOIN `beauticians` b on b.id=ii.staffid "
		." LEFT JOIN `coupons` cou on cou.id=i.coupon "
		." where i.active=0 and i.type <> 3 and c.cont='".$mobile."' order by i.id desc",[],$conn);
} else {
     $billings=[];
} 

$booking_dates=query("select * from app_bookings ab inner join app_bookings_clients abc on(ab.cid=abc.cid) where abc.contact_no='".$mobile."' group by booking_date order by booking_date desc",array(),$conn);
$client=query("select * from client where cont='".$mobile."'",array(),$conn);											 
if(!empty($client)){
    $cid = $client[0]['id'];
}else{
    $cid=$_GET['cid'];
}

$sql5= "SELECT count(cpsu.c_pack_id) as pack_count, GROUP_CONCAT(cpsu.inv) as inv,p.name as package_name,p.valid,p.price,s.name as service_name,cpsu.c_service_id,cpsu.client_id,cpsu.c_pack_id,sum(cpsu.quantity) - sum(cpsu.quantity_used) as qt,cpsu.quantity_used FROM `client_package_services_used` cpsu "
								." LEFT JOIN `packages` p on p.id=cpsu.c_pack_id "
								." LEFT JOIN `service` s on s.id=SUBSTRING_INDEX(cpsu.c_service_id,',',-1)"
								." where cpsu.active='1' and cpsu.client_id='".$cid."' GROUP BY cpsu.c_pack_id";

$result5=query_by_id($sql5,[],$conn);

$sql5= "SELECT count(cpsu.c_pack_id) as pack_count, GROUP_CONCAT(cpsu.inv) as inv,p.name as package_name,i.tax,p.valid,p.price,s.name as service_name,cpsu.c_service_id,cpsu.client_id,cpsu.c_pack_id,sum(cpsu.quantity) - sum(cpsu.quantity_used) as qt,cpsu.quantity_used FROM `client_package_services_used` cpsu "
		." LEFT JOIN `packages` p on p.id=cpsu.c_pack_id "
		." LEFT JOIN `service` s on s.id=SUBSTRING_INDEX(cpsu.c_service_id,',',-1) "
		." LEFT JOIN `invoice_".$branch_id."` i on i.client=cpsu.client_id "
		." where cpsu.active='1' and cpsu.client_id='".$cid."' GROUP BY cpsu.c_pack_id ";

$packages=query_by_id($sql5,[],$conn);
if($packages){
    foreach($packages as $pack){
        $today=date("Y-m-d");
        $vlid=$pack['valid'];
        $diff = strtotime($vlid) - strtotime($today); 
        $days=abs(round($diff / 86400));
        $list_price=query("SELECT sum(ps.price) as ser_price from packageservice ps left join service s on s.id=SUBSTR(ps.service,4) LEFT JOIN `servicecat` c on c.id=ps.category where ps.pid='".$pack['c_pack_id']."' and ps.active=0 order by ps.id asc",array(),$conn)[0]['ser_price'];
        $discount =round($list_price-$pack['price'],2);
        $save_persant=round(($discount/$list_price)*100,2);
        $arr['packages'].='<div class="accordion-item accordion-ghost">
							<a href="#" class="accordion-toggle">'.$pack['package_name'].' <span style="font-size:12px; font-style:italic;">(Valid till '.date("d-m-Y",strtotime($pack['valid'])).')</span> <i class="ion-android-add"></i></a>
							<div class="accordion-content ">
							<p>
							
							<strong>Amount: </strong>'.$pack['price'].'<br>
							<strong>Tax: </strong>'.(($pack['price']*$pack['tax'])/100).'<br>
							<strong>Total savings: </strong>'.$discount.'<br>
							<strong>Service(s):<br></strong>';
		
		  				 $sql5="SELECT Distinct ps.pid,ps.service as c_service_id ,ps.quantity,p.name as package_name ,s.name as service_name FROM `packageservice`ps "
										." LEFT JOIN `packages` p on p.id=ps.pid"
										." LEFT JOIN `service` s on s.id=SUBSTRING_INDEX(ps.service,',',-1)"
										." where pid in(SELECT cpsu.c_pack_id FROM `client_package_services_used` cpsu where cpsu.client_id='".$cid."' and cpsu.c_pack_id='".$pack['c_pack_id']."' GROUP BY cpsu.c_pack_id) and ps.active='0' ORDER BY ps.pid ASC";
                        			$service=query_by_id($sql5,[],$conn);
                             if($service){ foreach($service as $ser){	
                                $sum_total_quantity=sum_total_quantity_app($ser['pid'],$cid,$ser['c_service_id']);
                        		$remain=$sum_total_quantity - (service_availed_app($ser['pid'],$cid,$ser['c_service_id'], $branch_id));
                                if($remain==0){
                                $arr['packages'].='<DEL>'.$ser['service_name'].'</DEL><br>'; 
                                }else{
                                $arr['packages'].=''.$ser['service_name'].'<br>'; 
                                }    
                                    
                                }}
                                
			$arr['packages'].='</p>
							</div>
						</div>';
       
    
    }
}else{
				   $arr['packages'].='<b style="color:white;text-align:center;"> No data available</b>'; 
				}

    
    
    	if($billings){
				foreach($billings as $dates){
				    $b_id=$dates['id'];
				    $sql2="SELECT ii.package_id,imsp.ii_id,imsp.service_Provider,s.name,b.id b_id,ii.client,ii.service,ii.quantity,ii.disc_row,ii.staffid,ii.price,s.price as service_price,s.name as service_name,s.cat,CONCAT('sr,',s.id) as service_id,s.duration as service_durr,scat.cat as cat_name from `invoice_items_".$branch_id."` ii"
												." LEFT JOIN `service` s on s.id=SUBSTRING_INDEX(ii.service,',',-1)"
												." LEFT JOIN `servicecat` scat on scat.id=s.cat"
												." LEFT JOIN `invoice_multi_service_provider` imsp on imsp.ii_id=ii.id"
												." LEFT JOIN `beauticians` b on b.id=imsp.service_Provider "
												." where ii.iid='$b_id' and ii.active=0 and ii.type='Service' GROUP BY ii_id" 
												." UNION SELECT ii.package_id,imsp.ii_id,imsp.service_Provider,s.name,b.id b_id,ii.client,ii.service,ii.quantity,ii.disc_row,ii.staffid,ii.price,s.price as service_price,s.name as service_name,'0' as cat,CONCAT('pr,',s.id) as service_id,'0' as service_durr,0 as cat_name from `invoice_items_".$branch_id."` ii"
												." LEFT JOIN `products` s on s.id=SUBSTRING_INDEX(ii.service,',',-1)"
												." LEFT JOIN `invoice_multi_service_provider` imsp on imsp.ii_id=ii.id"
												." LEFT JOIN `beauticians` b on b.id=imsp.service_Provider "
												." where ii.iid='$b_id' and ii.active=0 and ii.type='Product' GROUP BY ii_id"
												." UNION SELECT ii.package_id,imsp.ii_id,imsp.service_Provider,s.name,b.id b_id,ii.client,ii.service,ii.quantity,ii.disc_row,ii.staffid,ii.price,s.price as service_price,s.name as service_name,'0' as cat,CONCAT('pa,',s.id) as service_id,'0' as service_durr,0 as cat_name from `invoice_items_".$branch_id."` ii"
												." LEFT JOIN `packages` s on s.id=SUBSTRING_INDEX(ii.service,',',-1)"
												." LEFT JOIN `invoice_multi_service_provider` imsp on imsp.ii_id=ii.id"
												." LEFT JOIN `beauticians` b on b.id=imsp.service_Provider "
												." where ii.iid='$b_id' and ii.active=0 and ii.type='Package'  GROUP BY ii_id";
												$services=query_by_id($sql2,[],$conn);
				    $arr['bills'].='<div class="accordion-item accordion-ghost">
                    					<a href="#" class="accordion-toggle"><em style="color:#white;">ID: #'.$dates['id'].' : '.date("d M Y",strtotime($dates['doa'])).' </em><i class="ion-android-add"  ></i></a>
                    					<div class="accordion-content">';
					if($services){
					    foreach($services as $service){
					    $status="Confirmed";
					    $arr['bills'].='<a href="#" class="dropdown-item"  ><em><strong style="color:white;" >'.$service['name'].'</strong></em><br><em style="color:white;"> '.$service['service_durr'].' MINS  | INR '.number_format($service['price'],2).'</em></a>';
					
					    }        
					}    				
                $arr['bills'].='</div>
                    				</div>'; 
				}}else{
				   $arr['bills'].='<b style="color:white;text-align:center;"> No data available</b>'; 
				}
				
			if($booking_dates){
				foreach($booking_dates as $dates){
	                     $arr['appoint'].='<div class="accordion-item accordion-ghost">
							<a href="#" class="accordion-toggle">AP#'.$dates['id'].' - '.date("d M Y",strtotime($dates['booking_date'])).' <i class="ion-android-add"></i></a>
							<div class="accordion-content ">
							<p>';
							$service=query("select * from app_bookings ab inner join app_bookings_clients abc on(ab.cid=abc.cid) inner join service s on(ab.service_id=s.id)  where abc.contact_no='".$_GET['mobile']."' and ab.booking_date='".$dates['booking_date']."'",array(),$conn);
					if($service){
					    foreach($service as $ser){ 
					        $status=empty($ser['confirm_status'])?"Pending":"Confirmed";
					        $arr['appoint'].='<br>'.'<a href="#" class="dropdown-item"  ><em><strong style="color:white;">'.$ser['name'].'</strong></em><br><em style="color:white;"> '.$ser['duration'].' MINS  | INR '.round($ser['price'],2).'('.$status.')</em></a>';
					    }
					 }	
				$arr['appoint'].='	</p>
							</div>
						</div>';
			}}
		    $booking_dates2=query("SELECT  pm.name as pay_method_name,ai.paid,ai.due,ai.bill_created_status,ai.ss_created_status,ai.doa,ai.id,ai.total,ai.pay_method,c.name,c.cont from `app_invoice_".$branch_id."` ai LEFT JOIN `client` c on c.id=ai.client LEFT JOIN `payment_mode` pm on pm.id = ai.pay_method where c.cont='".$_GET['mobile']."' and ai.active=0 and ai.type <> 3 order by ai.id desc",array(),$conn);  

		    if($booking_dates2){
				foreach($booking_dates2 as $dates){
	                     $arr['appoint'].='<div class="accordion-item accordion-ghost">
							<a href="#" class="accordion-toggle">AP#'.$dates['id'].' - '.date("d M Y",strtotime($dates['doa'])).' <i class="ion-android-add"></i></a>
							<div class="accordion-content ">
							<p>';
							$service=query("select * from app_invoice_".$branch_id." ab inner join client abc on(ab.client=abc.id) inner join app_invoice_items_".$branch_id." s on(ab.id=s.iid)  inner JOIN service ser on ser.id=SUBSTR(s.service,4)   where  abc.cont='".$_GET['mobile']."' and ab.id='".$dates['id']."'",array(),$conn);
					      
					if($service){
					    foreach($service as $ser){ 
					        $status="Confirmed";
					        $arr['appoint'].='<a href="#" class="dropdown-item" ><em><strong style="color:white">'.$ser['name'].'</strong></em><br><em style="color:white"> '.$ser['duration'].' MINS  | INR '.round($ser['price'],2).'('.$status.')</em></a><br>';
					    }
					 }	
				$arr['appoint'].='	</p>
							</div>
						</div>';
			}}

		    
		    if((empty($booking_dates2)) && (empty($booking_dates))){
			   $arr['appoint'].='<b style="color:white;text-align:center;"> No data available</b>'; 
			}
					

		
    	function sum_total_quantity_app($pid,$cid,$ser_id){
			global $conn;
			$sql="SELECT sum(cpsu.quantity) - sum(cpsu.quantity_used) as qt FROM `client_package_services_used` cpsu "
			." where cpsu.client_id='$cid' and cpsu.active='1' and cpsu.c_service_id='$ser_id' and cpsu.c_pack_id='$pid'";
			$result=query_by_id($sql,[],$conn)[0];
			$used_quantity_count=0;
			if($result['qt'] > 0){
				return  $result['qt'];
			}
		}	
		
		
		function service_availed_app($pid,$cid,$ser, $branch_id){
			global $conn;
			
			$sql="SELECT sum(ii.quantity) as used_quantity FROM `invoice_items_".$branch_id."` ii where ii.client='$cid' and service = '$ser' and active='0' and package_id='$pid'  GROUP BY  ii.package_id";
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