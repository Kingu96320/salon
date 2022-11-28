<?php
function get_reward_points($cid,$referral_code=null,$inv=null, $bid = null){
		global $conn;
		$used_referral_code = 0;
		$urc  = 0;
		$total_points = 0;
		$used_points = 0;
		if($inv > 0){
			$inv_id="and id<>'$inv'";
			$iid="and ii.iid<>'$inv'";

			$repoint = " AND invoice_id = 'bill,$inv'";
		} 

		if($bid > 0){
			$total_reward_point = "SELECT SUM(points) as reward_point FROM customer_reward_points WHERE status = 1 AND ( point_type = 1 OR point_type = 3) AND client_id='cust,$cid' and branch_id='".$bid."' AND ( SUBSTRING_INDEX(invoice_id,',',1) = 'bill' OR SUBSTRING_INDEX(invoice_id,',',1) = 'app')".$repoint;

				$reward_point = query_by_id($total_reward_point,[],$conn)[0];

				if($reward_point['reward_point'] > 0){
					$total_points += $reward_point['reward_point'];
				} else {
					$total_points += 0;
				}
			
			// $sql_reward_point="SELECT ii.service,s.points,ii.quantity as sum_quantity FROM invoice_items ii LEFT JOIN `service` s on s.id=SUBSTRING_INDEX(ii.service,',',-1) where ii.client='$cid' and ii.active='0'".$iid;
			
				$sql_used_reward_point = query("SELECT SUM(points) AS used_reward_points FROM customer_reward_points WHERE client_id = 'cust,$cid' AND status='1' AND point_type = '2' and branch_id='".$bid."'",[],$conn)[0];

				if($sql_used_reward_point['used_reward_points'] > 0){
					$used_points += $sql_used_reward_point['used_reward_points'];
				} else {
					$used_points += 0;
				}

				// $sql_used_reward_point['used_reward_points'];
				
				$sql_referral_code=query("SELECT count(*) as used_referral_code FROM `invoice_".$bid."` where referral_code='$referral_code' and referral_code<>'' and active='0' and branch_id='".$bid."'".$inv_id,[],$conn)[0];
				
				$sql_used_referral_code=query("SELECT count(referral_code) as urc FROM `invoice_".$bid."` where client='$cid' and referral_code<>'' and active='0' and branch_id='".$bid."'".$inv_id,[],$conn)[0];
				
				if($sql_referral_code['used_referral_code'] > 0){
					$used_referral_code += $sql_referral_code['used_referral_code'] * REFERRAL_POINTS;
				}

				$urc += $sql_used_referral_code['urc'] * REFERRAL_POINTS;
		}
		else {
			$total_branches = query_by_id("SELECT id FROM salon_branches WHERE status='1'",[],$conn);
			foreach($total_branches as $branch){
				$total_reward_point = "SELECT SUM(points) as reward_point FROM customer_reward_points WHERE status = 1 AND ( point_type = 1 OR point_type = 3) AND client_id='cust,$cid' and branch_id='".$branch['id']."' AND ( SUBSTRING_INDEX(invoice_id,',',1) = 'bill' OR SUBSTRING_INDEX(invoice_id,',',1) = 'app')".$repoint;

				$reward_point = query_by_id($total_reward_point,[],$conn)[0];

				if($reward_point['reward_point'] > 0){
					$total_points += $reward_point['reward_point'];
				} else {
					$total_points += 0;
				}
			
			// $sql_reward_point="SELECT ii.service,s.points,ii.quantity as sum_quantity FROM invoice_items ii LEFT JOIN `service` s on s.id=SUBSTRING_INDEX(ii.service,',',-1) where ii.client='$cid' and ii.active='0'".$iid;
			
				$sql_used_reward_point = query("SELECT SUM(points) AS used_reward_points FROM customer_reward_points WHERE client_id = 'cust,$cid' AND status='1' AND point_type = '2' and branch_id='".$branch['id']."'",[],$conn)[0];

				if($sql_used_reward_point['used_reward_points'] > 0){
					$used_points += $sql_used_reward_point['used_reward_points'];
				} else {
					$used_points += 0;
				}

				// $sql_used_reward_point['used_reward_points'];
				
				$sql_referral_code=query("SELECT count(*) as used_referral_code FROM `invoice_".$branch['id']."` where referral_code='$referral_code' and referral_code<>'' and active='0' and branch_id='".$branch['id']."'".$inv_id,[],$conn)[0];
				
				$sql_used_referral_code=query("SELECT count(referral_code) as urc FROM `invoice_".$branch['id']."` where client='$cid' and referral_code<>'' and active='0' and branch_id='".$branch['id']."'".$inv_id,[],$conn)[0];
				
				if($sql_referral_code['used_referral_code'] > 0){
					$used_referral_code += $sql_referral_code['used_referral_code'] * REFERRAL_POINTS;
				}

				$urc += $sql_used_referral_code['urc'] * REFERRAL_POINTS;
			}
		}
		if($inv > 0){
			return $total_points;
		} else {
			return($total_points - $used_points) + $used_referral_code  + $urc ;
		}
		
	}
	
	
	function service_availed($pid,$cid){
		global $conn;
		$sql="SELECT  GROUP_CONCAT(cpsu.inv) as inv,p.name as package_name,p.valid,s.name as service_name,cpsu.c_service_id,cpsu.client_id,cpsu.c_pack_id,sum(cpsu.quantity) - sum(cpsu.quantity_used) as qt,cpsu.quantity_used FROM `client_package_services_used` cpsu "
		." LEFT JOIN `packages` p on p.id=cpsu.c_pack_id "
		." LEFT JOIN `service` s on s.id=SUBSTRING_INDEX(cpsu.c_service_id,',',-1)"
		." where cpsu.client_id='$cid' and cpsu.active='1' and cpsu.branch_id='".$_SESSION['branch_id']."' GROUP BY cpsu.c_service_id";
		$result=query_by_id($sql,[],$conn);
		$used_quantity_count=0;
		foreach($result as $row){
			$sql="SELECT sum(ii.quantity) as used_quantity FROM `invoice_items_".$_SESSION['branch_id']."` ii where ii.client='$cid' and service = '".$row['c_service_id']."' and active='0' and package_id='$pid' and ii.branch_id='".$_SESSION['branch_id']."'  GROUP BY  ii.package_id";
			$used_quantity=query_by_id($sql,[],$conn)[0];
			if($used_quantity > 0){
				foreach($used_quantity as $row_used_quantity){
					$used_quantity_count +=$row_used_quantity['used_quantity'];
				}
			}
		}
		if ($used_quantity_count > 0) {
			return $used_quantity_count;
			}else{
			return "0";
		}
	}

	function check_package_expiry($client_id, $package_id){
	    global $conn;
	    $q = "SELECT i.doa as bill_date FROM invoice_".$_SESSION['branch_id']." i LEFT JOIN invoice_items_".$_SESSION['branch_id']." ii ON i.id = ii.iid WHERE i.client = '$client_id' AND ii.service = 'pa,".$package_id."' and i.branch_id='".$_SESSION['branch_id']."'";
	    $res = query_by_id($q,[],$conn)[0];
	    if($res['bill_date'] != ''){
	        $dur = query_by_id("SELECT duration FROM packages WHERE id = '$package_id' and branch_id='".$_SESSION['branch_id']."'",[],$conn)[0];
	        $current_date= my_date_format(date('Y-m-d'));
	        $days = $dur['duration'];
	        $package_expiry_date = my_date_format(date('Y-m-d', strtotime($res['bill_date']. ' + '.$days.' days')));
	        if((strtotime($current_date) > strtotime($package_expiry_date))){
	            return 0;
	        } else {
	            return 1;
	        }
	    }
	}

	function package_validity_date($client_id, $package_id, $invid, $branch){
	    global $conn;
	    $q = "SELECT i.doa as bill_date FROM invoice_".$branch." i LEFT JOIN invoice_items_".$branch." ii ON i.id = ii.iid WHERE i.client = '$client_id' AND ii.service = 'pa,".$package_id."' AND i.id = '$invid' and i.active='0' and i.branch_id='".$branch."'";
	    $res = query_by_id($q,[],$conn)[0];
	    if($res['bill_date'] != ''){
	        $dur = query_by_id("SELECT duration FROM packages WHERE id = '$package_id' and branch_id='".$branch."'",[],$conn)[0];
	        $days = $dur['duration'];
	        $validity_date = my_date_format(date('Y-m-d', strtotime($res['bill_date']. ' + '.$days.' days')));
	        return  $validity_date;
	    }
	}
	
	function package_count($cid,$pid){
		global $conn;
		$pack_count=0;
		$sql="SELECT cp.package as pack_count from `client_package_services_used` cpsu  LEFT JOIN `clientpackages` cp on cp.inv=cpsu.inv where cpsu.c_pack_id='$pid' and cpsu.client_id='$cid' and cpsu.active='1' and cpsu.branch_id='".$_SESSION['branch_id']."' GROUP  BY cpsu.inv";
		$result=query_by_id($sql,[],$conn);
		foreach($result as $row){
			
			$pack_count ++;
			
		}
		if ($pack_count > 0) {
			return $pack_count;
			}else{
			return "0";
		}
		
	}

	function customer_type($cid){
		global $conn;
		$current_date=date('Y-m-d');
		$sql="SELECT * from invoice_".$_SESSION['branch_id']." where client='$cid' and active='0' and branch_id='".$_SESSION['branch_id']."' order by id desc limit 1";
		$result=query_by_id($sql,[],$conn)[0];
		if($result)
		{	 
			$diff = abs(strtotime($result['doa']) - strtotime($current_date));
			$days = abs(round($diff / 86400));
			if($days > 180){
				return 'inactive';
			}else if($days >=90 && $days <=180){
				return 'churn_prediction';
			}else if($days < 90){
				return "active";
			}
		}else{
			return "newcustomer";
		}
	}
	
	function get_invoice_pending_payment($inv){
		global $conn;
		$pending_payment=0;
		$sql1="SELECT sum(pending_payment_received) as paid from `invoice_pending_payment` where status=1 and iid='$inv' and branch_id='".$_SESSION['branch_id']."'";
		$result1=query_by_id($sql1,[],$conn)[0];
		if($result1) 
		{
			return $result1['paid']; 
		}else{
			return "0";
		}
	}
	
	function get_pending_payment($client_id, $inv_id, $bid){
		global $conn;
		$pending_payment=0;

		$sql1="SELECT sum(pending_payment_received) as paid from `invoice_pending_payment` where status=1 and client_id='$client_id' and iid='$inv_id' and branch_id='".$bid."'";
		$result1=query_by_id($sql1,[],$conn)[0];
		if($result1) 
		{
			return $result1['paid']; 
		}else{
			return "0";
		}
	}
	
	function get_pending_payment_dash($client_id,$bid){
		global $conn;
		$pending_payment=0;
		$sql1="SELECT sum(pending_payment_received) as paid from `invoice_pending_payment` where status=1 and client_id='$client_id' and branch_id='".$bid."'";
		$result1=query_by_id($sql1,[],$conn)[0];
		if($result1) 
		{
			return $result1['paid']; 
		}else{
			return "0";
		}
	}
	
	
	function client_wallet($client_id){
		global $conn;
		$wallet_amount = 0 ;
		$sql = "SELECT w.client_id,sum(w.wallet_amount) as wallet_amount,max(w.time_update) as time_update,c.name as client_name,c.cont from wallet w LEFT JOIN client c on c.id=w.client_id  where w.status=1 and w.client_id='$client_id' GROUP BY w.client_id";
		$result = query_by_id($sql,[],$conn);
		if($result){
			foreach($result as $row){
				return $wallet_amount = ($row['wallet_amount']);
			}
		}else{
			return $wallet_amount;
		}
		
	}
	
	
	function wallet_money_used($cid){
		global $conn;
		$money = 0;
		$total_branches = query_by_id("SELECT id FROM salon_branches WHERE status='1'",[],$conn);
		foreach($total_branches as $branch){
			$sql="SELECT sum(amount_paid) as paid FROM `multiple_payment_method` WHERE SUBSTRING_INDEX(invoice_id,',',-1) IN (SELECT i.id as id from invoice_".$branch['id']." i where i.client='$cid' and i.active='0') and status=1 and payment_method = 7 and branch_id='".$branch['id']."'";
			$result = query_by_id($sql,[],$conn);
			if($result){
				foreach($result as $row){
					$money += $row['paid'];
				}
			}
		}
		return $money;
	}
	
	
	function get_price($get_id){
		global $conn;
		$id = EXPLODE(",",$get_id)[1];
		if(EXPLODE(",",$get_id)[0] == 'sr'){
		    	$sql ="SELECT price FROM `service` where active='0' and id='$id' and branch_id='".$_SESSION['branch_id']."'";	
		    	}else if(EXPLODE(",",$get_id)[0] == 'pr'){
		    	$sql ="SELECT price FROM `products` where active='0' and id='$id' and branch_id='".$_SESSION['branch_id']."'";	
			    }else if(EXPLODE(",",$get_id)[0] == 'pa'){
		    	$sql ="SELECT price FROM `packages` where active='0' and id='$id' and branch_id='".$_SESSION['branch_id']."'";	
	    	}
	    	$result=query_by_id($sql,[],$conn);
		    foreach($result as $row) {
		    	return $row['price'];
	    	}
    	}
	
	
	function getEnquiryfor($get_id){
		global $conn;
		$id = EXPLODE(",",$get_id)[1];
		if(EXPLODE(",",$get_id)[0] == 'sr'){
			$sql ="SELECT CONCAT('Service',' ',name) as name FROM `service` where id='$id'";	
			}else if(EXPLODE(",",$get_id)[0] == 'pr'){
			$sql ="SELECT CONCAT('Product',' ',name) as name FROM `products` where id='$id'";	
			}else if(EXPLODE(",",$get_id)[0] == 'pa'){
			$sql ="SELECT CONCAT('Package',' ',name) as name FROM `packages` where id='$id' and branch_id='".$_SESSION['branch_id']."'";	
			}else if(EXPLODE(",",$get_id)[0] == 'mem'){
			$sql ="SELECT CONCAT('Membership',' ',membership_name) as name FROM `membership_discount` where id='$id'";	
			}
		$result=query_by_id($sql,[],$conn);
		foreach($result as $row) {
			return $row['name'];
		}
	}
	
	function lastfeedback($client_id){
	    global $conn;
	    $query = query_by_id("SELECT review, customer_service_rating FROM client_feedback WHERE client_id = '".$client_id."' AND approve_status = '1' AND status = '1' AND review != '' ORDER BY id DESC LIMIT 1",[],$conn)[0];
	    if($query){
	        $rating = '';
	        if($query['review'] != ''){
	            for($i=1;$i<=5;$i++){
	                if($i <= $query['customer_service_rating']){
	                    $rating .= '<i class="fa fa-star rating-color" style="margin:0px;" aria-hidden="true"></i>';
	                } else {
	                    $rating .= '<i class="fa fa-star-o rating-color" style="margin:0px;" aria-hidden="true"></i>';
	                }
	            }
	            return $rating."<br />".$query['review'];
	        } else {
	            return '----';
	        }
	    } else {
	        return '----';
	    }
	}
	
	
	function getstock($pvid,$invoice_id,$batch_id = null, $type = null){
		global $conn;
		$iid = "";
		$b_id = "";
		$todaydate = date('Y-m-d');
		
		if($invoice_id>0){
			$iid= " and iid<>'$invoice_id'";
		}
		if($batch_id != null){
		    $b_id = $batch_id;
		}
		
		$product_detail = query_by_id("SELECT p.*, u.name as unit_name FROM products p LEFT JOIN units u ON u.id = p.unit WHERE p.id='".$pvid."'",[],$conn)[0];
		
		$stock_credit = query_by_id("SELECT sum(quantity) as stock from `purchase_items` where product_id=:product_id and id='".$b_id."' and active='0' and exp_date >='".$todaydate."' and branch_id='".$_SESSION['branch_id']."' ",[
		"product_id"=>$pvid, 
		],$conn);
		
		$stock_debit = query_by_id("SELECT sum(quantity) as stock from `invoice_items_".$_SESSION['branch_id']."` where  SUBSTRING_INDEX(service,',',-1)=:product_id AND  active='0' and type='Product' and product_stock_batch='".$b_id."' and branch_id='".$_SESSION['branch_id']."'".$iid,[
		"product_id"=>$pvid,
		],$conn);
		
		// $productused_debit = 0;

		$productused_debit = query_by_id("SELECT sum(quantity) as stock from `productused` where  SUBSTRING_INDEX(product,',',-1)=:product_id AND active='0' and branch_id='".$_SESSION['branch_id']."' AND product_stock_batch='".$b_id."'",["product_id"=>$pvid],$conn);

		// echo "SELECT sum(quantity) as stock from `productused` where  SUBSTRING_INDEX(product,',',-1)='".$pvid."' AND active='0' and product_stock_batch='".$b_id."' and branch_id='".$_SESSION['branch_id']."'";
		
		$product_used = query_by_id("SELECT pu.*, sum(quantity) as stock, u.name as unit_name FROM product_usage pu LEFT JOIN units u ON u.id = pu.unit WHERE pu.product_id='".$pvid."' AND stock_id='".$b_id."' AND pu.status='1' and type='1' and pu.branch_id='".$_SESSION['branch_id']."'",[],$conn)[0];
		
		if($stock_credit){
			if($stock_credit[0]['stock']<=0){
				$stock_credit = 0;
				}else{
				$stock_credit = $stock_credit[0]['stock'];
			}
		}
		if($stock_debit){
			if($stock_debit[0]['stock']<=0){
				$stock_debit = 0;
			}else{
				$stock_debit = $stock_debit[0]['stock'];
			}
		}
		if($productused_debit ){
			if($productused_debit [0]['stock']<=0){
				$productused_debit  = 0;
				}else{
				$productused_debit  = $productused_debit[0]['stock'];
			}
		}
		
		if($product_detail['unit'] == 1){
		    $total_stock = $stock_credit - $stock_debit - $productused_debit;
		    $stock_in_ml = ($total_stock*$product_detail['volume'])*1000;
		    $used_in_salon = $product_used['stock']*1000;
		    $final_stock = $stock_in_ml-$used_in_salon;
		    $final_stock = $final_stock/1000;
		    if($type == 'buy_product'){
		        $pro_pur = ($final_stock*1000)/(1000*$product_detail['volume']);
		        if(explode('.',$pro_pur)[0] >=1 ){
		            return explode('.',$pro_pur)[0];
		        } else {
		            return 0;
		        }
		    } else if($type == 'product_to_use'){
		        return $final_stock;
		    } else if($type == 'product_use_show'){
		        return number_format($final_stock,3)." ".$product_detail['unit_name'];
		    } else {
		        return $final_stock/$product_detail['volume']." (".number_format($final_stock,3)." ".$product_detail['unit_name'].")";
		    }
		} else if($product_detail['unit'] == 2){
		    $total_stock = $stock_credit - $stock_debit - $productused_debit;
		    // echo $stock_credit.' - '.$stock_debit.' - '.$productused_debit.", ";
		    $stock_in_ml = $total_stock*$product_detail['volume'];
		    $used_in_salon = $product_used['stock'];
		    $final_stock = $stock_in_ml-$used_in_salon;
		    if($type == 'buy_product'){
		         $in_pack = $final_stock/$product_detail['volume'];
		        if(explode('.',$in_pack)[0] >=1 ){
		            return explode('.',$in_pack)[0];
    	        } else {
    	            return 0;
    	        }
		    } else if($type == 'product_to_use'){
		        return $final_stock;
		    } else if($type == 'product_use_show'){
		        return $final_stock." ".$product_detail['unit_name'];
		    } else {
		        $total_stock = $final_stock/$product_detail['volume'];
		        return $total_stock." (".number_format($final_stock)." ".$product_detail['unit_name'].")";
		    }
		} else if($product_detail['unit'] == 4){
		    $total_stock = $stock_credit - $stock_debit - $productused_debit;
		    $stock_in_ml = ($total_stock*$product_detail['volume'])*1000;
		    $used_in_salon = $product_used['stock']*1000;
		    $final_stock = $stock_in_ml-$used_in_salon;
		    $final_stock = $final_stock/1000;
		    if($type == 'buy_product'){
		        $pro_pur = ($final_stock*1000)/(1000*$product_detail['volume']);
		        if(explode('.',$pro_pur)[0] >=1 ){
		            return explode('.',$pro_pur)[0];
		        } else {
		            return 0;
		        }
		    } else if($type == 'product_to_use'){
		        return $final_stock;
		    } else if($type == 'product_use_show'){
		        return number_format($final_stock,3)." ".$product_detail['unit_name'];
		    } else {
		        return $final_stock/$product_detail['volume']." (".number_format($final_stock,3)." ".$product_detail['unit_name'].")";
		    }
		} else {
		    $total_stock = $stock_credit - $stock_debit - $productused_debit;
		    $stock_in_ml = $total_stock*$product_detail['volume'];
		    $used_in_salon = $product_used['stock'];
		    $final_stock = $stock_in_ml-$used_in_salon;
		    if($type == 'buy_product'){
		        $in_pack = $final_stock/$product_detail['volume'];
		        if(explode('.',$in_pack)[0] >=1 ){
		            return explode('.',$in_pack)[0];
    	        } else {
    	            return 0;
    	        }
		    } else if($type == 'product_to_use'){
		        return $final_stock;
		    } else if($type == 'product_use_show'){
		        return $final_stock." ".$product_detail['unit_name'];
		    } else {
		        $total_stock = $final_stock/$product_detail['volume'];
		        if(explode('.',$total_stock)[0] >=1 ){
		            $total_stock = explode('.',$total_stock)[0];
		        }
		        return $total_stock." (".number_format($final_stock)." ".$product_detail['unit_name'].")";
		    }
		}
	}

	// stock without service provider asigned products
	function getstock_wo_pro($pvid,$invoice_id,$batch_id = null, $type = null){
		global $conn;
		$iid = "";
		$b_id = "";
		if($invoice_id>0){
			$iid= " and iid<>'$invoice_id'";
		}
		if($batch_id != null){
		    $b_id = $batch_id;
		}
		
		$product_detail = query_by_id("SELECT p.*, u.name as unit_name FROM products p LEFT JOIN units u ON u.id = p.unit WHERE p.id='".$pvid."'",[],$conn)[0];
		
		$stock_credit = query_by_id("SELECT sum(quantity) as stock from `purchase_items` where product_id=:product_id and id='".$b_id."' and active='0' and branch_id='".$_SESSION['branch_id']."' ",[
		"product_id"=>$pvid,
		],$conn);
		
		$stock_debit = query_by_id("SELECT sum(quantity) as stock from `invoice_items_".$_SESSION['branch_id']."` where  SUBSTRING_INDEX(service,',',-1)=:product_id AND  active='0' and type='Product' and product_stock_batch='".$b_id."' and branch_id='".$_SESSION['branch_id']."'".$iid,[
		"product_id"=>$pvid,
		],$conn);
		
		// $productused_debit = 0;

		$productused_debit = query_by_id("SELECT sum(quantity) as stock from `productused` where  SUBSTRING_INDEX(product,',',-1)=:product_id AND active='0' and branch_id='".$_SESSION['branch_id']."' and product_stock_batch='".$b_id."'",["product_id"=>$pvid,],$conn);

		// echo "SELECT sum(quantity) as stock from `productused` where  SUBSTRING_INDEX(product,',',-1)='".$pvid."' AND active='0' and product_stock_batch='".$b_id."' and branch_id='".$_SESSION['branch_id']."'";
		
		$product_used = query_by_id("SELECT pu.*, sum(quantity) as stock, u.name as unit_name FROM product_usage pu LEFT JOIN units u ON u.id = pu.unit WHERE pu.product_id='".$pvid."' AND stock_id='".$b_id."' AND pu.status='1' and type='1' and pu.branch_id='".$_SESSION['branch_id']."'",[],$conn)[0];
		
		if($stock_credit){
			if($stock_credit[0]['stock']<=0){
				$stock_credit = 0;
				}else{
				$stock_credit = $stock_credit[0]['stock'];
			}
		}
		if($stock_debit){
			if($stock_debit[0]['stock']<=0){
				$stock_debit = 0;
			}else{
				$stock_debit = $stock_debit[0]['stock'];
			}
		}
		if($productused_debit ){
			if($productused_debit [0]['stock']<=0){
				$productused_debit  = 0;
				}else{
				$productused_debit  = $productused_debit[0]['stock'];
			}
		}
		
		if($product_detail['unit'] == 1){
		    $total_stock = $stock_credit - $stock_debit - $productused_debit;
		    $stock_in_ml = ($total_stock*$product_detail['volume'])*1000;
		    $used_in_salon = $product_used['stock']*1000;
		    $final_stock = $stock_in_ml-$used_in_salon;
		    $final_stock = $final_stock/1000;
		    if($type == 'buy_product'){
		        $pro_pur = ($final_stock*1000)/(1000*$product_detail['volume']);
		        if(explode('.',$pro_pur)[0] >=1 ){
		            return explode('.',$pro_pur)[0];
		        } else {
		            return 0;
		        }
		    } else if($type == 'product_to_use'){
		        return $final_stock;
		    } else if($type == 'product_use_show'){
		        return number_format($final_stock,3)." ".$product_detail['unit_name'];
		    } else {
		        return $total_stock." (".number_format($final_stock,3)." ".$product_detail['unit_name'].")";
		    }
		} else if($product_detail['unit'] == 2){
		    $total_stock = $stock_credit - $stock_debit - $productused_debit;
		    // echo $stock_credit.' - '.$stock_debit.' - '.$productused_debit.", ";
		    $stock_in_ml = $total_stock*$product_detail['volume'];
		    $used_in_salon = $product_used['stock'];
		    $final_stock = $stock_in_ml-$used_in_salon;
		    if($type == 'buy_product'){
		         $in_pack = $final_stock/$product_detail['volume'];
		        if(explode('.',$in_pack)[0] >=1 ){
		            return explode('.',$in_pack)[0];
    	        } else {
    	            return 0;
    	        }
		    } else if($type == 'product_to_use'){
		        return $final_stock;
		    } else if($type == 'product_use_show'){
		        return $final_stock." ".$product_detail['unit_name'];
		    } else {
		        $total_stock = $final_stock/$product_detail['volume'];
		        return $total_stock." (".number_format($final_stock)." ".$product_detail['unit_name'].")";
		    }
		} else if($product_detail['unit'] == 4){
		    $total_stock = $stock_credit - $stock_debit - $productused_debit;
		    $stock_in_ml = ($total_stock*$product_detail['volume'])*1000;
		    $used_in_salon = $product_used['stock']*1000;
		    $final_stock = $stock_in_ml-$used_in_salon;
		    $final_stock = $final_stock/1000;
		    if($type == 'buy_product'){
		        $pro_pur = ($final_stock*1000)/(1000*$product_detail['volume']);
		        if(explode('.',$pro_pur)[0] >=1 ){
		            return explode('.',$pro_pur)[0];
		        } else {
		            return 0;
		        }
		    } else if($type == 'product_to_use'){
		        return $final_stock;
		    } else if($type == 'product_use_show'){
		        return number_format($final_stock,3)." ".$product_detail['unit_name'];
		    } else {
		        return $total_stock." (".number_format($final_stock,3)." ".$product_detail['unit_name'].")";
		    }
		} else {
		    $total_stock = $stock_credit - $stock_debit - $productused_debit;
		    $stock_in_ml = $total_stock*$product_detail['volume'];
		    $used_in_salon = $product_used['stock'];
		    $final_stock = $stock_in_ml-$used_in_salon;
		    if($type == 'buy_product'){
		        $in_pack = $final_stock/$product_detail['volume'];
		        if(explode('.',$in_pack)[0] >=1 ){
		            return explode('.',$in_pack)[0];
    	        } else {
    	            return 0;
    	        }
		    } else if($type == 'product_to_use'){
		        return $final_stock;
		    } else if($type == 'product_use_show'){
		        return $final_stock." ".$product_detail['unit_name'];
		    } else {
		        $total_stock = $final_stock/$product_detail['volume'];
		        if(explode('.',$total_stock)[0] >=1 ){
		            $total_stock = explode('.',$total_stock)[0];
		        }
		        return $total_stock." (".number_format($final_stock)." ".$product_detail['unit_name'].")";
		    }
		}
	}


	function getstockserviceprovider($pvid,$invoice_id,$batch_id = null, $type = null){
		global $conn;
		$iid = "";
		$b_id = "";
		if($invoice_id>0){
			$iid= " and iid<>'$invoice_id'";
		}
		if($batch_id != null){
		    $b_id = $batch_id;
		}
		
		$product_detail = query_by_id("SELECT p.*, u.name as unit_name FROM products p LEFT JOIN units u ON u.id = p.unit WHERE p.id='".$pvid."'",[],$conn)[0];

		$productused_debit = query_by_id("SELECT sum(quantity) as stock from `productused` where  SUBSTRING_INDEX(product,',',-1)=:product_id AND active='0' and branch_id='".$_SESSION['branch_id']."' and product_stock_batch='".$b_id."'",["product_id"=>$pvid],$conn);

		$product_used = query_by_id("SELECT pu.*, sum(quantity) as stock, u.name as unit_name FROM product_usage pu LEFT JOIN units u ON u.id = pu.unit WHERE pu.product_id='".$pvid."' AND stock_id='".$b_id."' AND pu.status='1' and type='1' and pu.branch_id='".$_SESSION['branch_id']."'",[],$conn)[0];
		
		if($productused_debit ){
			if($productused_debit [0]['stock']<=0){
				$productused_debit  = 0;
				}else{
				$productused_debit  = $productused_debit[0]['stock'];
			}
		}
		
		if($product_detail['unit'] == 1){
		    $total_stock = $productused_debit;
		    $stock_in_ml = ($total_stock*$product_detail['volume'])*1000;
		    $used_in_salon = $product_used['stock']*1000;
		    $final_stock = $stock_in_ml-$used_in_salon;
		    $final_stock = $final_stock/1000;
		    if($type == 'buy_product'){
		        $pro_pur = ($final_stock*1000)/(1000*$product_detail['volume']);
		        if(explode('.',$pro_pur)[0] >=1 ){
		            return explode('.',$pro_pur)[0];
		        } else {
		            return 0;
		        }
		    } else if($type == 'product_to_use'){
		        return $final_stock;
		    } else if($type == 'product_use_show'){
		        return number_format($final_stock,3)." ".$product_detail['unit_name'];
		    } else {
		        return $total_stock." (".number_format($final_stock,3)." ".$product_detail['unit_name'].")";
		    }
		} else if($product_detail['unit'] == 2){
		    $total_stock = $productused_debit;
		    // echo $stock_credit.' - '.$stock_debit.' - '.$productused_debit.", ";
		    $stock_in_ml = $total_stock*$product_detail['volume'];
		    $used_in_salon = $product_used['stock'];
		    $final_stock = $stock_in_ml-$used_in_salon;
		    if($type == 'buy_product'){
		         $in_pack = $final_stock/$product_detail['volume'];
		        if(explode('.',$in_pack)[0] >=1 ){
		            return explode('.',$in_pack)[0];
    	        } else {
    	            return 0;
    	        }
		    } else if($type == 'product_to_use'){
		        return $final_stock;
		    } else if($type == 'product_use_show'){
		        return $final_stock." ".$product_detail['unit_name'];
		    } else {
		        $total_stock = $final_stock/$product_detail['volume'];
		        return $total_stock." (".number_format($final_stock)." ".$product_detail['unit_name'].")";
		    }
		} else if($product_detail['unit'] == 4){
		    $total_stock = $productused_debit;
		    $stock_in_ml = ($total_stock*$product_detail['volume'])*1000;
		    $used_in_salon = $product_used['stock']*1000;
		    $final_stock = $stock_in_ml-$used_in_salon;
		    $final_stock = $final_stock/1000;
		    if($type == 'buy_product'){
		        $pro_pur = ($final_stock*1000)/(1000*$product_detail['volume']);
		        if(explode('.',$pro_pur)[0] >=1 ){
		            return explode('.',$pro_pur)[0];
		        } else {
		            return 0;
		        }
		    } else if($type == 'product_to_use'){
		        return $final_stock;
		    } else if($type == 'product_use_show'){
		        return number_format($final_stock,3)." ".$product_detail['unit_name'];
		    } else {
		        return $total_stock." (".number_format($final_stock,3)." ".$product_detail['unit_name'].")";
		    }
		} else {
		    $total_stock = $productused_debit;
		    $stock_in_ml = $total_stock*$product_detail['volume'];
		    $used_in_salon = $product_used['stock'];
		    $final_stock = $stock_in_ml-$used_in_salon;
		    if($type == 'buy_product'){
		        $in_pack = $final_stock/$product_detail['volume'];
		        if(explode('.',$in_pack)[0] >=1 ){
		            return explode('.',$in_pack)[0];
    	        } else {
    	            return 0;
    	        }
		    } else if($type == 'product_to_use'){
		        return $final_stock;
		    } else if($type == 'product_use_show'){
		        return $final_stock." ".$product_detail['unit_name'];
		    } else {
		        $total_stock = $final_stock/$product_detail['volume'];
		        if(explode('.',$total_stock)[0] >=1 ){
		            $total_stock = explode('.',$total_stock)[0];
		        }
		        return $total_stock." (".number_format($final_stock)." ".$product_detail['unit_name'].")";
		    }
		}
	}
	
	
	function view($path,$data = NULL){
		if($data){
            extract($data);	
		}
		$path = $path . '.view.php';
		
		include 'views/layout.php';
	}
	
	function remove_leading_zero($key){
		if(isset($_POST[$key])){
            return htmlspecialchars(ltrim($_POST[$key],'0'));	
		}
		return NULL;
	}
	
	function clean($str) {
		$str = @trim($str);
		return ($str);
	}
	
	function alert($message,$style){
		return 
        "<div class='alert alert-".$style." alert-dismissible' role='alert'>
		<button type='button' class='close' data-dismiss='alert' aria-label='Close'>		
		<span aria-hidden='true'>&times;</span>
		</button>"
		. $message .
        "</div>";			
	}
	function empty_session($var){
		unset($_SESSION[$var]);
	}
	function old($key){ 
		if( isset( $_POST[$key] ) ){
			if(is_array($_POST[$key])){
				$narray = [];
				foreach($_POST[$key] as $dd => $data){
					$narray[$dd] = htmlspecialchars( $data );
				}
				return $narray;
				}elseif( trim($_POST[$key]) !== "" ){
				return htmlspecialchars($_POST[$key]);
			}
		}
		return NULL;
	}
	function old_get($key){
		if( isset( $_GET[$key] ) ){
			if(!empty($_GET[$key])){
				return trim(htmlspecialchars($_GET[$key]));
			}
		}
		return NULL;
	}
	if(!function_exists('empty_fields')){
		function empty_fields($client_data){
            $flag = FALSE;
            foreach($client_data as $info)
            {
				if(empty($info)){
					$flag = TRUE;
				}
			}
            return $flag;
            //return ($flag == 0) ? true : false;	
		}
	}
	
	function check_empty_field($data){
		//    var_dump( $data );
		//    var_dump( count($data) );
		if( is_array($data) && count($data) > 0){
			foreach($data as $d){
				if( trim($d) === "" ){
					return false;
				}
			}
			return true;
		}
		
	}
	function encryptIt( $q ) {
		$cryptKey  = 'qJB0rGtIn5UB1xG03efyCp';
		$qEncoded      = base64_encode( mcrypt_encrypt( MCRYPT_RIJNDAEL_256, md5( $cryptKey ), $q,MCRYPT_MODE_CBC, md5( md5( $cryptKey ) ) ) );
		return( $qEncoded );
	}
	
	function decryptIt( $q ) {
		$cryptKey  = 'qJB0rGtIn5UB1xG03efyCp';
		$qDecoded      = rtrim( mcrypt_decrypt( MCRYPT_RIJNDAEL_256, md5( $cryptKey ), base64_decode( $q ), MCRYPT_MODE_CBC, md5( md5( $cryptKey ) ) ), "\0");
		return( $qDecoded );
	}
	
	function encrypt_url($data){
		return ((($data + 1000) * 7) + 5000)*2;
	}
	function decrypt_url($data){
		return (((($data/2)-5000)/7)-1000);
	}
	function compress($source, $destination, $quality) { 
		$info = getimagesize($source); 
		if ($info['mime'] == 'image/jpeg') 
        $image = imagecreatefromjpeg($source); 
		elseif ($info['mime'] == 'image/gif') 
        $image = imagecreatefromgif($source); 
		elseif ($info['mime'] == 'image/png') 
        $image = imagecreatefrompng($source); 
		imagejpeg($image, $destination, $quality); 
		
		return $destination; 
		
	}
	
	function is_employee_logged_in(){
		if( isset( $_SESSION['USER_TYPE'] ) ){
			if( $_SESSION['USER_TYPE'] === 'user' ){
				return TRUE;
			}
		}
		return FALSE;
	}
	
	function upper_name($name){
		return ucfirst( strtolower($name));
	}
	
	function is_valid_input($type,$data,$format = NULL,$length = NULL){
		switch($type){
			case 'alpha':
            $allowed_chars = array('-', '_'); 
            if(!ctype_alnum(str_replace($allowed_chars, '', $data))) { 
                return FALSE;
				}else{
                return TRUE;
			}
            break;
            
			case 'alpha_space':
            $allowed_chars = array(' '); 
            if(!ctype_alnum(str_replace($allowed_chars, '', $data))) { 
                return FALSE;
				}else{
                return TRUE;
			}
            break;
			
			case 'email':
            if(!filter_var($data, FILTER_VALIDATE_EMAIL)){
                return FALSE;
				}else{
                return TRUE;
			}
            break;
			
			case 'length':
            if ( strlen($data) > intval($length) ) {
                return FALSE;
				}else{
                return TRUE;
			}
            break;  
			
			case 'date':
            $result = val_date($data,$format);
            return $result;
			break;
			
			case 'number':
			if(!is_numeric ( $data )){
				return FALSE;
				}else{
				return TRUE;
			}      
		}
	}
	
	function val_date($data, $format){
		$test_date = $data;
		$test_arr  = explode('-', $test_date);
		if (count($test_arr) == 3) {
			if($format == 'd-m-y'){
				
				if (checkdate( (int)$test_arr[1], (int)$test_arr[0], (int)$test_arr[2]) == FALSE) {
					return FALSE;
					}else{
					return TRUE;
				} 
				}elseif($format == 'y-m-d'){
				
				if (checkdate((int)$test_arr[1], (int)$test_arr[2], (int)$test_arr[0]) == FALSE) {
					return FALSE;
					}else{
					return TRUE;
				}
			}
			} else {
			return FALSE;
		}
		
	}
	//image upload function
	function upload_file($name,$id){
		$upload_errors = array(
        UPLOAD_ERR_OK        => "No errors.",
        UPLOAD_ERR_INI_SIZE  => "Larger than upload_max_filesize",
        UPLOAD_ERR_FORM_SIZE => "Larger than form MAX_FILE_SIZE",
        UPLOAD_ERR_PARTIAL   => "partial upload.",
        UPLOAD_ERR_NO_FILE   => "No File.",
        UPLOAD_ERR_NO_TMP_DIR => "No temporary directory.",
        UPLOAD_ERR_CANT_WRITE => "Can`t write to disk.",
        UPLOAD_ERR_EXTENSION  => "File upload stopped by extension."
		);
		
		$extensions = array("jpeg" , "jpg" , "png" , "gif");
		
        $file_tmp = $_FILES[$name]['tmp_name'];
        $file_name = $_FILES[$name]['name'];
        $dir = "uploads/pics/".$id;
        $ext=pathinfo($file_name,PATHINFO_EXTENSION);
        $file_ext=explode('.',$_FILES[$name]['name']);
        $file_ext=end($file_ext); 
        $file_ext = strtolower($file_ext);
		
        if(in_array($file_ext,$extensions)){
            if(!is_dir($dir)){
                mkdir(__DIR__ . "/$dir", 0755, true);
			}
            $image_path = "$dir/".$file_name;
            if(move_uploaded_file($file_tmp,$dir.'/'.$file_name)){
                $source = $dir.'/'.$file_name;
                $destination = $dir.'/'.$file_name;
                $quality = 75;
                $newfile = compress($source, $destination, $quality);
                $insert_img_path = query("UPDATE customer_info SET photo = :path "
				. "WHERE id = :id and branch_id='".$_SESSION['branch_id']."'",
				array(
				'id' => $id,
				'path' =>$destination
				),
				$conn);
                $status = " UPLOAD SUCCESSFULL ";
                $alert = alert($status,'success');
                return $alert;
				}else{
                $error = $_FILES[$name]['error'];
                $status = $upload_errors[$error];
                $alert = alert($status,'warning');
                return $alert;
			}
			
			}else{
            $status = "ALLOWED IMAGE FORMAT ARE jpg, png, jpeg, jif";
            $alert = alert($status,'danger');
            return $alert;
		}
		
	}
	function current_date(){
		return date('d-m-Y h:i:s a');
	}
	
	function new_date(){
		return date('d-m-Y');
	}
	
	function new_time(){
		return date('h:i a');
	}
	
	function current_time(){
		return date('h:i:s a');
	}
	
	
	
	function send_sms_return($contact,$content){
		
		$mobile = $contact;
		$message = $content;
		$username = 'parbhat';
		$password = '91893135';
		$sender   = 'mlzldh';
		
		$fields_string = '';
		$fields = array(
        'username'=>urlencode($username),
        'password'=>urlencode($password),
        'sender'=>urlencode($sender),
        'mobile'=>urlencode($mobile),
        'message'=>urlencode($message)
		);
		foreach($fields as $key=>$value) { 
			$fields_string .= $key.'='.$value.'&'; 
		}
		
		$fields_string = rtrim($fields_string,'&');
		$url = 'http://www.smscgateway.com/messageapi.asp?'.$fields_string;
		$ch = curl_init();
		curl_setopt($ch,CURLOPT_URL,$url);
		$return = curl_exec($ch);
		curl_close($ch);
		
	}
	
	
	function format_d_m_y($date){
		$new_date = NULL;
		if( strpos($date, '-') ){
			
			$old_date = explode('-', $date);
			
			$new_date =  isset( $old_date[2] ) ?  trim($old_date[2]) : '00';
			$new_date .= '-';
			$new_date .= isset( $old_date[1] ) ?  trim($old_date[1]) : '00';
			$new_date .= '-';
			$new_date .= $old_date[0];       
			
			}elseif( strpos($date, '/') ){
			
			$old_date = explode('/', $date);
			
			$new_date =  isset( $old_date[2] ) ?  $old_date[2] : '00';
			$new_date .= '-';
			$new_date .= isset( $old_date[1] ) ?  $old_date[1] : '00';
			$new_date .= '-';
			$new_date .= $old_date[0]; 
		}
		return $new_date;
	}
	
	function upload( $post, $dir, $id ){
		$upload_errors = [
        UPLOAD_ERR_OK         => "No errors.",                      UPLOAD_ERR_INI_SIZE   => "Larger than upload_max_filesize",
        UPLOAD_ERR_FORM_SIZE  => "Larger than form MAX_FILE_SIZE",  UPLOAD_ERR_PARTIAL    => "partial upload.",
        UPLOAD_ERR_NO_FILE    => "No File.",                        UPLOAD_ERR_NO_TMP_DIR => "No temporary directory.",
        UPLOAD_ERR_CANT_WRITE => "Can`t write to disk.",            UPLOAD_ERR_EXTENSION  => "File upload stopped by extension."
		];
		$file_tmp  = $post['tmp_name'];
		$file_name = $post['name'];
		$directory       = '../images/' . $dir . "/" . $id;
		
		if( !file_exists( __DIR__ . "/$directory" ) ){
			mkdir(__DIR__ . "/$directory", 0755, true);
		}
		$image_path = "$directory/" . $file_name;
		$new_dir = 'images/' . $dir . "/" . $id . "/" . $file_name;
		if( move_uploaded_file( $file_tmp,SITE_ROOT.'/'.$image_path ) ){
			return array( TRUE , $new_dir);
			}else{
			$status = $upload_errors[$post['error']];
			return array( FALSE, $status );
		}
	}
	
	function get_payment_method($bill_id, $conn){
		return query("SELECT name, cheque_no, card_no FROM payment_modes WHERE bill_id = :id",
		[
		'id' => $bill_id,
		], $conn)[0];
	}
	
	function pay_method_name($method_id){
	    global $conn;
	    if($method_id != 0){
	        $query = query_by_id("SELECT name FROM payment_method WHERE id = '$method_id' AND status = '1'",[],$conn)[0];
	        return $query['name'];
	    }
	}
	
	function get_client_name($id, $conn, $type = NULL){
		if( $type === 'fullname' ){
			return query_by_id("SELECT name as name FROM client WHERE id = :id and branch_id='".$_SESSION['branch_id']."' LIMIT 1", [ 'id' => $id ], $conn)[0]['name'];
			}elseif( $type === 'withcontact'){
			return query_by_id("SELECT CONCAT(name, ' ', ' (', cont,') ') as name FROM client WHERE id = :id and branch_id='".$_SESSION['branch_id']."' LIMIT 1", [ 'id' => $id ], $conn)[0]['name'];
			}elseif( $type === NULL){
			return query_by_id("SELECT name as name FROM client WHERE id = :id and branch_id='".$_SESSION['branch_id']."' LIMIT 1", [ 'id' => $id ], $conn)[0]['name'];
		}	
	}
	
	function get_diet_plan($client_id, $date, $category_id, $conn){
		//var_dump( $client_id, $date, $category_id);
		return query_by_id("SELECT CONCAT(p.description , '\n (', p.time,')' ) AS name FROM diet_plan p "
		. " INNER JOIN diet_plan_details d "
		. " ON p.parent_id = d.id "
		. " WHERE d.date = :date AND d.client_id = :id AND p.category_id = :catid ",
		[
		'date' => format_d_m_y($date),
		'id' => $client_id,
		'catid' => $category_id
		], $conn)[0]['name'];
	}
	
	
	function get_last_date( $date ){ 
		$filter_tokens = explode('/', $date);
		$filter_month = $filter_tokens[0];
		$filter_year = $filter_tokens[1];
		$cr_date = date("$filter_year-$filter_month-01");
		
		return date('t', strtotime($cr_date));
	}
	
	function last_date( $date ){ 
		return date('t', strtotime($date));
	}
	function trainer_name( $trainer_id, $conn ){
		if( $trainer_id ){
			return upper_name( query_by_id("SELECT employee_name as name "
			. " FROM employees WHERE employee_id = :id",
			['id'=>$trainer_id], $conn)[0]['name'] );
		} 
		return NULL;
	}
	
	function image_resize($src, $dst, $width, $height, $crop=0){
		
		if(!list($w, $h) = getimagesize($src)) return "Unsupported picture type!";
		
		$type = strtolower(substr(strrchr($src,"."),1));
		if($type == 'jpeg') $type = 'jpg';
		switch($type){
			case 'bmp': $img = imagecreatefromwbmp($src); break;
			case 'gif': $img = imagecreatefromgif($src); break;
			case 'jpg': $img = imagecreatefromjpeg($src); break;
			case 'png': $img = imagecreatefrompng($src); break;
			default : return "Unsupported picture type!";
		}
		
		// resize
		if($crop){
			if($w < $width or $h < $height) return "Picture is too small!";
			$ratio = max($width/$w, $height/$h);
			$h = $height / $ratio;
			$x = ($w - $width / $ratio) / 2;
			$w = $width / $ratio;
		}
		else{
			if($w < $width and $h < $height) return "Picture is too small!";
			$ratio = min($width/$w, $height/$h);
			$width = $w * $ratio;
			$height = $h * $ratio;
			$x = 0;
		}
		
		$new = imagecreatetruecolor($width, $height);
		
		// preserve transparency
		if($type == "gif" or $type == "png"){
			imagecolortransparent($new, imagecolorallocatealpha($new, 0, 0, 0, 127));
			imagealphablending($new, false);
			imagesavealpha($new, true);
		}
		
		imagecopyresampled($new, $img, 0, 0, $x, 0, $width, $height, $w, $h);
		
		switch($type){
			case 'bmp': imagewbmp($new, $dst); break;
			case 'gif': imagegif($new, $dst); break;
			case 'jpg': imagejpeg($new, $dst); break;
			case 'png': imagepng($new, $dst); break;
		}
		return true;
	}
	function tax_charges($tax_id, $conn){
		return query_by_id("SELECT charges FROM taxtype WHERE id = :id", ['id' => $tax_id], $conn)[0]['charges'];
	}
	
	function inc_tax_charges($tax_id, $conn){
		return query_by_id("SELECT charges FROM inclusive_taxtype WHERE id = :id", ['id' => $tax_id], $conn)[0]['charges'];
	}
	
	function gym_name($conn){
		return upper_name( query_by_id("SELECT name FROM company_profile LIMIT 1", array(), $conn)[0]['name'] );
	}
	
	function check_image($fileToUpload){
		return getimagesize($fileToUpload["tmp_name"]);
	}
	
	function check_file_size($fileToUpload,$size){
		if ($fileToUpload["size"] > $size) {
			return FALSE;
		}
		return TRUE;
	}
	
	function check_file_type($imageFileType){
		if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif" ) {
			return FALSE;
		}
		return TRUE;
	}
	
	function create_folder($target_dir){
		mkdir($target_dir, 0777, true);
	}
	
	function check_file_exist($name,$extension){
		$increment = ''; //start with no suffix
		
		while(file_exists($name . $increment . '.' . $extension)) {
			$increment++;
		}
		return $name . $increment . '.' . $extension;
	}
	
	function upload_image($fileToUpload ){
		
		$size_limits = array(
        '1mb' => '1250000',
        '2mb' => '2500000',
        '3mb' => '3750000',
        '4mb' => '5000000',
        '5mb' => '6250000',
		);
		
		$size = $size_limits['2mb'];
		/**
			* get name and extension of the file to 
			* check if there is a file already existing in the directory 
			* we rename it and then upload 
		*/
		$name = pathinfo($fileToUpload['name'], PATHINFO_FILENAME);
		$extension = pathinfo($fileToUpload['name'], PATHINFO_EXTENSION);
		
		// Check if file already exists
		$file_name = check_file_exist($name, $extension);
		
		$dir = 'upload/docs';
		
		if (!file_exists($dir) && !is_dir($dir)) {
			mkdir($dir);    
		}
		
		$target_dir = $dir;
		
		$dir_to_save = $dir;
		
		/**
			*  we will check if the targeted 
			*  directory does not exist we will create a new one
		*/
		if (!file_exists($target_dir)) {
			create_folder($target_dir);
		}
		
		$target_file = $target_dir . $file_name;
		
		$target_to_move = $dir_to_save . $file_name;
		
		$imageFileType = strtolower( pathinfo($target_file,PATHINFO_EXTENSION) );
		
		$response = [];
		$response[0] = TRUE;
		// Check if image file is a actual image or fake image
		$check = check_image($fileToUpload);
		
		if($check === FALSE) {
			$response[0] = FALSE;
			$response[1] = "File is not an Image";
			return $response;
		} 
		// Check file size
		$check2 = check_file_size($fileToUpload, $size);
		
		if( $check2 === FALSE ){
			$response[0] = FALSE;
			$response[1] = "File Size is Too Large";
			return $response;
		}
		// Allow certain file formats
		$check3 = check_file_type($imageFileType);
		
		if( $check3 === FALSE ){
			$response[0] = FALSE;
			$response[1] = "Only jpeg, gif, png, jpeg Image Formats are Allowed To upload <br/> Your file format is " .$imageFileType;
			return $response;
		}
		
		if (move_uploaded_file($fileToUpload["tmp_name"], $target_to_move)) {
			$response[0] = TRUE;
			$response[1] = $target_file;
			return $response;
			} else {
			$response[0] = FALSE;
			$response[1] = "Error Occured While Upload The File, Please Try Again..";
			return $response;
		}
	}
	function reArrayFiles(&$file_post) {
		
		$file_ary = array();
		$file_count = count($file_post['name']);
		$file_keys = array_keys($file_post);
		
		for ($i=0; $i<$file_count; $i++) {
			foreach ($file_keys as $key) {
				$file_ary[$i][$key] = $file_post[$key][$i];
			}
		}
		
		return $file_ary;
	}
	
	//Salon functions
	function clientip(){
		$ipaddress = '';
		if (isset($_SERVER['HTTP_CLIENT_IP']))
        $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
		else if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
        $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
		else if(isset($_SERVER['HTTP_X_FORWARDED']))
        $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
		else if(isset($_SERVER['HTTP_FORWARDED_FOR']))
        $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
		else if(isset($_SERVER['HTTP_FORWARDED']))
        $ipaddress = $_SERVER['HTTP_FORWARDED'];
		else if(isset($_SERVER['REMOTE_ADDR']))
        $ipaddress = $_SERVER['REMOTE_ADDR'];
		else
        $ipaddress = 'UNKNOWN';
		return $ipaddress;
	}
	
	
    function get_ip_address($proxy = false)
    {
        if ($proxy === true)
        {
            foreach (array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED') as $key)
            {
                if (array_key_exists($key, $_SERVER) === true)
                {
                    foreach (array_map('trim', explode(',', $_SERVER[$key])) as $ip)
                    {
                        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 | FILTER_FLAG_IPV6 | FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false)
                        {
                            return $ip;
                        }
                    }
                }
            }
        }
    
        return $_SERVER['REMOTE_ADDR'];
    }
	
	
	
	function systemlogo($conn){
		$sql="SELECT * FROM `system` WHERE id='1'";
		$result=query_by_id($sql,[],$conn);
		if($result){
			return $result[0]['logo'];
			}else{
            return "upload/1597190335.png";
		}
	}
	
	function systemname(){
		global $conn;
		$sql="SELECT * FROM `system` WHERE branch_id='".$_SESSION['branch_id']."'";
		$result=query_by_id($sql,[],$conn);
		if($result){
			return addslashes($result[0]['salon']);
			}else{
            $sql="SELECT * FROM `system` WHERE id='1'";
    		$result=query_by_id($sql,[],$conn);
    		if($result){
    			return addslashes($result[0]['salon']);
    			}else{
                return "Easy Salon";
    		}
		}
	}
	
	function systemname_app($id){
		global $conn;
		$sql="SELECT * FROM `system` WHERE branch_id='".$id."'";
		$result=query_by_id($sql,[],$conn);
		if($result){
			return addslashes($result[0]['salon']);
			}else{
            return "Easy Salon";
		}
	}

	
	
	function getuser($user,$att,$ip,$conn){
		query("INSERT INTO `logactivity`(`username`,`attempt`,`ip`,`branch_id`) VALUES ('$user','$att','$ip','".$_SESSION['branch_id']."')",[],$conn);
	}		
	
	function my_date_format($givenDate){
			return date(MY_DATE_FORMAT,strtotime($givenDate));
		}
 	function my_time_format($givenDate){
			return date(TIME_FORMAT,strtotime($givenDate));
	}


	// function to get total sale of service provider


	function service_provider_total_sale($type, $provider_id, $id){
		global $conn;
		$type= strtolower($type);
		$query = "SELECT imsp.service_Provider,b.name, ii.service, sum(ii.price) as total_sale from `invoice_items_".$_SESSION['branch_id']."` ii LEFT JOIN `service` s on s.id=SUBSTRING_INDEX(ii.service,',',-1) LEFT JOIN `servicecat` scat on scat.id=s.cat LEFT JOIN `invoice_multi_service_provider` imsp on imsp.ii_id=ii.id LEFT JOIN `beauticians` b on b.id=imsp.service_Provider where b.id='$provider_id' and LOWER(ii.type)='$type' and DATE(ii.start_time) >= '".date('Y-m-01')."' and DATE(ii.start_time) <= '".date('Y-m-t')."' and ii.iid <= '".$id."' and ii.branch_id='".$_SESSION['branch_id']."'";
		$res = query_by_id($query,[],$conn)[0];
		if(count($res) > 0){
			if($res['total_sale'] != ''){
				return $res['total_sale'];
			} else {
				return 0;
			}
		} else {
			return 0;
		}
	} 

	// function to get service commission based on the advance commissions settings 
	//this function will accept three parameters "Commission type", "Service provider id", "Total sale amount of the service provider".
    // NOTE : $type should be in string format which will "service" or "product"

	// ++++++++++++ Function start +++++++++++++++++

	function service_provider_commission($type, $provider_id, $total_sale){
		global $conn;
		if(strtolower($type) == 'service'){
			$commission_type = 1;
		} else if(strtolower($type) == 'product'){
			$commission_type = 2;
		} else{
			return 0;
		}

		$commission_list = query_by_id("SELECT * from service_provider_advance_commission_setting where sp_id=:id and commission_type=:commission_type and status='0' and branch_id='".$_SESSION['branch_id']."' order by id ASC",["id"=>$provider_id, "commission_type"=> $commission_type],$conn);
		if($commission_list){
			foreach ($commission_list as $key => $res) {
				if($total_sale >= $res['from_price'] && $total_sale <= $res['to_price']){
					return $res['commission_rate'];
				}
			}
		} else {
			if(strtolower($type) == 'service'){
				$commission_column = 'serivce_commision';
			} else if(strtolower($type) == 'product'){
				$commission_column = 'prod_commision';
			} else{
				return 0;
			}
			$commission = query_by_id("SELECT $commission_column from beauticians where id=:id and active='0' and branch_id='".$_SESSION['branch_id']."' order by id ASC",["id"=>$provider_id],$conn)[0];
			if($commission){
				return $commission[$commission_column];
			} else {
				return 0;
			}
		}
	}
	// ++++++++++++ Function end +++++++++++++++++
	
	
	function service_provider_commission_saved($provider_id, $service_id, $bill_id){
	    global $conn;
	    $cper = query_by_id("SELECT commission_per FROM invoice_multi_service_provider WHERE inv='".$bill_id."' AND service_name='".$service_id."' AND service_provider='".$provider_id."' AND status='1' AND branch_id='".$_SESSION['branch_id']."'",[],$conn)[0]['commission_per'];
	    return $cper; 
	}
    
    // Function to get provider count for single service
    
    function provider_count($service_id, $bill_id){
        global $conn;
        $result = query_by_id("SELECT COUNT(DISTINCT service_provider) as total FROM invoice_multi_service_provider WHERE service_name='".$service_id."' AND inv='".$bill_id."' AND status='1' and branch_id='".$_SESSION['branch_id']."'",[],$conn)[0]['total'];
        return $result;
    }

	// php function to change daterange date in iso date standard  Y-m-d (2019-11-01)

	function isoDate($date){
		$datespit = explode('/', $date);
		$day = $datespit[1];
		$month = $datespit[0];
		$year = $datespit[2];
		return trim($year).'-'.trim($month).'-'.trim($day);
	}

	// function to get extra working hours

	function extratimeStatus(){
		global $conn;
		$ex_time = query_by_id("SELECT extra_hours from system where id='".$_SESSION['branch_id']."' and active='0' and branch_id='".$_SESSION['branch_id']."'",[],$conn)[0];
		if($ex_time && $ex_time['extra_hours'] == 1){
			return '1';
		} else {
			return '0';
		}
	}

	// function to get redeem point

	function redeempoint($return = 0){
		global $conn;
		$redeem_point = query_by_id("SELECT redeem_point from redeem_point_setting where status='1' and id='1'",[],$conn)[0];
		if($redeem_point && $redeem_point['redeem_point'] != ''){
			if($return == 1){
				return $redeem_point['redeem_point'];
			} else {
				echo $redeem_point['redeem_point'];
			}
		} 
	}


	// function to get redeem price

	function redeemprice(){
		global $conn;
		$redeem_price = query_by_id("SELECT price from redeem_point_setting where status='1' and id='1'",[],$conn)[0];
		if($redeem_price && $redeem_price['price'] != ''){
			echo $redeem_price['price'];
		}
	}

	// function to get max redeem point

	function maxredeempoint(){
		global $conn;
		$max_redeem_point = query_by_id("SELECT max_redeem_point from redeem_point_setting where status='1' and id='1'",[],$conn)[0];
		if($max_redeem_point && $max_redeem_point['max_redeem_point'] != ''){
			echo $max_redeem_point['max_redeem_point'];
		}
	}

	// function to get message template

	function getMessgeTemplate($id = null){
		global $conn;
		$template = query_by_id("SELECT template_detail from sms_templates_".$_SESSION['branch_id']." where id='$id' and status='1' and branch_id='".$_SESSION['branch_id']."'",[],$conn)[0];
		if($template && $template['template_detail'] != ''){
			echo $template['template_detail'];
		}
	} 

	// function to get service detail of package

	function sum_total_quantity($pid,$cid,$ser_id,$id, $bid){
		global $conn;
		$sql="SELECT (sum(cpsu.quantity) - sum(cpsu.quantity_used)) - sum(cpsu.tmp_qty)  as qt FROM `client_package_services_used` cpsu "
		." where cpsu.client_id='$cid' and cpsu.active='1' and cpsu.c_service_id='$ser_id' and cpsu.c_pack_id='$pid' and cpsu.id='$id' and cpsu.branch_id='".$bid."'";
		$result=query_by_id($sql,[],$conn)[0];
		if($result['qt'] > 0){
			return  $result['qt'];
		}
	}	

	// function to get package service id

	function package_service_id($pid,$cid,$ser_id){
		global $conn;
		$sql = "SELECT id FROM client_package_services_used WHERE client_id='".$cid."' AND c_pack_id = '".$pid."' AND c_service_id = '".$ser_id."' AND active = '1' and branch_id='".$_SESSION['branch_id']."'";
		$result=query_by_id($sql,[],$conn)[0];
		if($result['id'] > 0){
			return $result['id'];
		}
	}
	
	
	function service_availed_cp($pid,$cid,$ser){
		global $conn;
		
		$sql="SELECT sum(ii.quantity) as used_quantity FROM `invoice_items_".$_SESSION['branch_id']."` ii where ii.client='$cid' and service = '$ser' and active='0' and package_id='$pid' and ii.branch_id='".$_SESSION['branch_id']."'  GROUP BY  ii.package_id";
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

	// function to get shop opening time
	function shopopentime($id = null){
		global $conn;
		if($id != ''){
		    $bid = $id;
		} else {
		    $bid = $_SESSION['branch_id'];
		}
		$sql = "select shpstarttime from system where id = '".$bid."' and active='0' and branch_id='".$bid."'";
		$res=query_by_id($sql,[],$conn)[0];
		
		$eh = query_by_id("SELECT extra_hours FROM system where id='".$bid."' and branch_id='".$bid."'",[],$conn)[0]['extra_hours'];
		if($eh == 1){
		    return '00:00:00';   
		} else {
		    return $res['shpstarttime'];
		}
	}

	// function to get shop close time
	function shopclosetime($id = null){
		global $conn;
		if($id != ''){
		    $bid = $id;
		} else {
		    $bid = $_SESSION['branch_id'];
		}
		$sql = "select shpendtime from system where id = '".$bid."' and active='0' and branch_id='".$bid."'";
		$res=query_by_id($sql,[],$conn)[0];
		
		$eh = query_by_id("SELECT extra_hours FROM system where id='".$bid."' and branch_id='".$bid."'",[],$conn)[0]['extra_hours'];
		if($eh == 1){
		    return '24:00:00';   
		} else {
		    return $res['shpendtime'];
		}
	}

	// function to get shop open and close time
	function shopTimes(){
		global $conn;
		$sql = "select shpstarttime, shpendtime from system where id = '".$_SESSION['branch_id']."' and active='0' and branch_id='".$_SESSION['branch_id']."'";
		$res=query_by_id($sql,[],$conn)[0];
		$times = array();
		for($count = 0; $count < 24; $count++){
			if($count < $res['shpstarttime'] || $count > $res['shpendtime']-1){
				array_push($times, 'hour_'.$count);
			}
		}
		return $times;
	}

	// function to get list of holidays
	function holidayDates(){
		global $conn;
		$sql = "select date from holidays_list where status = '1'";
		$res = query_by_id($sql,[],$conn);
		$dates = '';
		for($count = 0; $count < count($res); $count++){
			$dates.= "'".$res[$count]['date']."'";
			if($count != count($res)-1){
				$dates .=',';
			}
		}
		return $dates;
	}

	// function to get off weekends
	function closeweekend(){
		global $conn;
		$sql = "select id from working_days_time where status = '".$_SESSION['branch_id']."' and working_status='0' and branch_id='".$_SESSION['branch_id']."'";
		$res = query_by_id($sql,[],$conn);
		$weekday = '';
		for($count = 0; $count < count($res); $count++){
			if($res[$count]['id'] == 7){
				$weekday.= 0;
			} else {
				$weekday .= $res[$count]['id'];
			}
			if($count != count($res)-1){
				$weekday .=',';
			}
		}
		return $weekday;
	}
	
	// function beautitian designation
    
    function beautician_category($sp_id){
        global $conn;
        return query("SELECT spt.name as type FROM beauticians b LEFT JOIN ser_pro_types spt ON spt.id = b.ser_pro_type_id WHERE b.id = '".$sp_id."' and b.branch_id='".$_SESSION['branch_id']."'",[],$conn)[0]['type'];
    }
    
    // get client details
    
    function client_profile($client_id){
        global $conn;
        $profile = query_by_id("SELECT * FROM client WHERE id='".$client_id."' and branch_id='".$_SESSION['branch_id']."'",[],$conn)[0];
        return $profile;
    }

    // function to get branch name

    function branch_name(){
    	global $conn;
    	return query_by_id("SELECT branch_name FROM salon_branches WHERE id='".$_SESSION['branch_id']."' AND status='1'",[],$conn)[0]['branch_name'];
    }

    function branch_by_id($branch_id){
    	global $conn;
    	return query_by_id("SELECT branch_name FROM salon_branches WHERE id='".$branch_id."' AND status='1'",[],$conn)[0]['branch_name'];
    }

    function total_branches(){
    	global $conn;
    	return query_by_id("SELECT id FROM salon_branches WHERE status='1'",[],$conn);
    }

    function provider_designation($id){
    	global $conn;
    	return query_by_id("SELECT name FROM ser_pro_types WHERE id='".$id."' AND status='1'",[],$conn)[0]['name'];
    }

    function client_name($client_id){
    	global $conn;
    	return query_by_id("SELECT name FROM client WHERE id='".$client_id."'",[],$conn)[0]['name'];
    }

    // function to check transfer status of service provider

    function check_sp_transfer_status($sp_id){
    	global $conn;
    	$data =  query_by_id("SELECT * FROM transfered_service_providers WHERE provider_id='".$sp_id."' ORDER BY id DESC LIMIT 1",[],$conn)[0];
    	if($data['transfer_type'] == 1){
    		return true;
    	} else if($data['transfer_type'] == 2){
    		if(strtotime(date('Y-m-d')) >= $data['start_date'] && strtotime(date('Y-m-d')) <= $data['end_date']){
    			return true;
    		} else {
    			return false;
    		}
    	}
    }

    // function to show wallet amount of client.

    function client_wallet_uptodate($client_id,$date,$time){		
		global $conn;
		$wallet_amount = 0 ;
		$sql0 = "SELECT w.client_id,sum(w.wallet_amount) as wallet_amount,c.name as client_name,c.cont from wallet_history w LEFT JOIN client c on c.id=w.client_id  where w.transaction_type = 0 and TIMESTAMP(w.time_update) <= '".$date." ".$time."' and  w.status=1 and w.client_id='$client_id' GROUP BY w.client_id";
		$sql1 = "SELECT w.client_id,sum(w.wallet_amount) as wallet_amount,c.name as client_name,c.cont from wallet_history w LEFT JOIN client c on c.id=w.client_id  where w.transaction_type = 1 and TIMESTAMP(w.time_update) <= '".$date." ".$time."' and  w.status=1 and w.client_id='$client_id' GROUP BY w.client_id";
		$sql2 = "SELECT w.client_id,sum(w.wallet_amount) as wallet_amount,c.name as client_name,c.cont from wallet_history w LEFT JOIN client c on c.id=w.client_id  where w.transaction_type = 2 and TIMESTAMP(w.time_update) <= '".$date." ".$time."' and  w.status=1 and w.client_id='$client_id' GROUP BY w.client_id";
		$debit = query_by_id($sql0,[],$conn);
		$credit = query_by_id($sql1,[],$conn);
		$refunded = query_by_id($sql2,[],$conn);
		$debit_amount = 0;
		$credit_amount = 0;
		$refunded_amount = 0;
		$total_amount = 0;
		if($debit){
			foreach($debit as $row){
				$debit_amount = ($row['wallet_amount']);
			}
		}
		if($credit){
			foreach($credit as $row){
				$credit_amount = ($row['wallet_amount']);
			}
		}
		if($refunded){
			foreach($refunded as $row){
				$refunded_amount = ($row['wallet_amount']);
			}
		}
		$total_amount = ($credit_amount+$refunded_amount)-$debit_amount;
		return $total_amount;		
		
	}
	
	function short_url($link){
        return file_get_contents('http://tinyurl.com/api-create.php?url='.$link);
	}
	
	function ssl_encrypt($string){
	    $ciphering = "AES-128-CTR";
	    $iv_length = openssl_cipher_iv_length($ciphering);
        $options = 0;
        $encryption_iv = '1234567891011121';
        $encryption_key = "ShivSofts2021";
        $encryption = openssl_encrypt($string, $ciphering,
            $encryption_key, $options, $encryption_iv);
        return $encryption;
	}
	
	function ssl_decrypt($string){
	    $ciphering = "AES-128-CTR";
	    $options = 0;
	    $decryption_iv = '1234567891011121';
	    $decryption_key = "ShivSofts2021";
	    $decryption = openssl_decrypt ($string, $ciphering, 
        $decryption_key, $options, $decryption_iv);
        return $decryption;
	}
	
?>