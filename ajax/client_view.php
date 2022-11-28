<?php
	include_once '../includes/db_include.php';
	$branch_id = $_SESSION['branch_id'];
	if (isset($_POST['client_id'])){
		$return_arr = array();
		$client_id = $_POST['client_id'];
		$i=0;
		$inv=0;
		$total_spending = 0;
		$total_visit = 0;
		if($_POST['inv_id'] > 0){
			$inv = $_POST['inv_id'];
		}

		$view_profile = '';
		
		$client_info = query_by_id("SELECT * from client where id=:id",["id"=>$client_id],$conn)[0];
		$return_arr['leadsource'] = $client_info['leadsource'];
		$return_arr['gender'] = $client_info['gender'];	


		if($client_info['branch_id'] != $branch_id){
		    $view_profile = '<br /><i class="fa fa-user" style="margin-left:0px; margin-right:0px;" aria-hidden="true"></i> <a href="clientprofile.php?cid='.$client_id.'&bid='.encrypt_url($client_info['branch_id']).'" target="_blank"><u>View profile</u></a>';
		} else {
		    $view_profile = '<br /><i class="fa fa-user" style="margin-left:0px; margin-right:0px;" aria-hidden="true"></i> <a href="clientprofile.php?cid='.$client_id.'" target="_blank"><u>View profile</u></a>';
		}

		$return_arr['branch_name'] = ucfirst(branch_by_id($client_info['branch_id'])).$view_profile;	
		$return_arr['dob'] = $client_info['dob'];	
		$return_arr['Anniversary'] = $client_info['aniversary'];	
		if(lastvisit($client_id) != 'NA'){
			$return_arr['lastvisit'] = my_date_format(lastvisit($client_id));	
		} else {
			$return_arr['lastvisit'] = lastvisit($client_id);
		}
		$customer_type=customer_type($client_id);

		$total_branches = query_by_id("SELECT id FROM salon_branches WHERE status='1'",[],$conn);
		foreach($total_branches as $branch){
		$sql="SELECT sum(mpm.amount_paid) as paid,0 as wallet,count(DISTINCT i.id) as total_visit FROM `invoice_".$branch['id']."` i LEFT JOIN multiple_payment_method mpm ON mpm.invoice_id = CONCAT('bill',',',i.id)"
				 ." WHERE i.active='0' AND i.client='$client_id' AND mpm.payment_method not in ('2,7,8,9') and i.branch_id='".$branch['id']."' and mpm.branch_id='".$branch['id']."'"
				 ." UNION ALL SELECT sum(mpm.amount_paid) as paid,0 as wallet,0 as total_visit FROM `app_invoice_".$branch['id']."` ai LEFT JOIN multiple_payment_method mpm ON mpm.invoice_id = CONCAT('app',',',ai.id)"
				 ." WHERE ai.active='0' AND ai.client='$client_id' AND mpm.payment_method not in ('2,7,8,9') and ai.branch_id='".$branch['id']."'"
				 ." UNION ALL SELECT sum(ipp.pending_payment_received) as paid,0 as wallet,0 as total_visit FROM `invoice_pending_payment` ipp "
				 ." WHERE ipp.status='1' AND ipp.client_id='$client_id' and ipp.branch_id='".$branch['id']."'"
				 ." UNION ALL SELECT sum(w.paid_amount) as paid, 0 as wallet, 0 as total_visit FROM `wallet_history` w "
				 ." WHERE w.status='1' AND w.branch_id='".$branch['id']."' AND w.client_id='$client_id'  AND (w.get_wallet_from = 'Bill' or w.get_wallet_from = 'Add_wallet')";
			$result=query_by_id($sql,[],$conn);
			if($result){
				foreach($result as $row) 
				{
					$total_spending	+=  $row['paid'];
					$total_visit	+=  $row['total_visit'];
				}
			}
		}
		
		$return_arr['total_spending'] = number_format(round($total_spending),2);
		$return_arr['total_visit'] = $total_visit;
		
		// $spending_query = query_by_id("SELECT 0 as paid,sum(w.wallet_amount) as wallet,0 as total_visit FROM `wallet` w "
		// 	 ." WHERE w.status='1' AND w.client_id='$client_id'",[],$conn);
		
		// foreach($spending_query as $query){
		//     	$return_arr['wallet'] +=  $query['wallet'];
		// }

		$return_arr['wallet'] = client_wallet($client_id);
		
		$return_arr['packages'] = "";
		$total_branches = query_by_id("SELECT id FROM salon_branches WHERE status='1'",[],$conn);
		foreach($total_branches as $branch){
			$sql5= "SELECT count(cpsu.c_pack_id) as pack_count,i.id as inv_id, p.name as package_name,p.valid,p.price,p.duration,s.name as service_name,cpsu.inv,cpsu.c_service_id,cpsu.client_id,cpsu.c_pack_id,sum(cpsu.quantity) as qt, sum(cpsu.quantity_used) as used_qty,cpsu.quantity_used FROM `client_package_services_used` cpsu "
							." LEFT JOIN `packages` p on p.id=cpsu.c_pack_id "
							." LEFT JOIN `service` s on s.id=SUBSTRING_INDEX(cpsu.c_service_id,',',-1)"
							." LEFT JOIN `invoice_".$branch['id']."` i on i.id = cpsu.inv"
							." where cpsu.active='1' and cpsu.client_id='".$client_id."' and cpsu.branch_id='".$branch['id']."' GROUP by cpsu.inv";
			// echo $sql5."<br />";
			$result5=query_by_id($sql5,[],$conn);
			if($result5){
				$count = 0;
				foreach($result5 as $row5) {
					$sql_up = "update client_package_services_used set tmp_qty = 0 where client_id='".$row5['client_id']."' and c_pack_id='".$row5['c_pack_id']."' and branch_id='".$branch['id']."'";
					$tmpqty = query($sql_up,[],$conn); 

					$days=$row5['duration'];
					$package_expiry_date = my_date_format(date('Y-m-d', strtotime($row5['valid']. ' + '.$days.' days')));
					if(strtotime(package_validity_date($row5['client_id'], $row5['c_pack_id'], $row5['inv_id'], $branch['id'])) > strtotime(date('d-m-Y'))){
	    				$return_arr['packages'] .= $row5['package_name']."<br /> Valid : ".package_validity_date($row5['client_id'], $row5['c_pack_id'], $row5['inv_id'], $branch['id'])."<br /> Branch : ".branch_by_id($branch['id']);
	    				if($count < count($result5)-1){
	    					$return_arr['packages'] .="<br />--------------------------<br />";
	    				}else{
	    					$return_arr['packages'] .="<br />";
	    				}	
					}
					$count++;
				}
				$return_arr['packages'] .="--------------------------<br />";
			}
		}
		
        // Membership packages
        $return_arr['membership'] = '';

        $total_branches = query_by_id("SELECT id FROM salon_branches WHERE status='1'",[],$conn);
		foreach($total_branches as $branch){
	        $membership = query_by_id("SELECT mdh.*, i.doa, md.membership_name as name, md.validity FROM membership_discount_history mdh"
	                                ." LEFT JOIN membership_discount md ON md.id = mdh.md_id"
	                                ." LEFT JOIN invoice_$branch_id i ON i.id=mdh.invoice_id "
	                                ." LEFT JOIN client c ON c.id = client_id"
	                                ." WHERE mdh.client_id='".$client_id."'",[],$conn);
			if($membership){
			    $count = 0;
			    foreach($membership as $res){
			        $package_expiry_date = my_date_format(date('Y-m-d', strtotime(date('Y-m-d',strtotime($res['doa'])). ' + '.$res['validity'].' days')));
			        if(strtotime($package_expiry_date) > strtotime(date('Y-m-d'))){
			                $return_arr['membership'] = $res['name']."<br /> Valid : ".$package_expiry_date;
			            if($count < count($membership)-1){
	    					$return_arr['membership'] .="<br />--------------------------<br />";
	    				}else{
	    					$return_arr['membership'] .="<br />";
	    				}	
			        } else {
			            $return_arr['membership'] .= $package_expiry_date;
			        }
			        $count++;
			    }
			} else {
			    $return_arr['membership'] = '----';
			}
		}
		
		$return_arr['customer_type'] =  $customer_type;
	
		
		$return_arr["reward_points"]= get_reward_points($client_id,$client_info['referral_code'],$inv);
		$return_arr["last_feedback"] = lastfeedback($client_id); 
		
		sleep(1);
		echo json_encode($return_arr);
	}	
		 
 function lastvisit($uid){
	global $conn;
	global $branch_id;
	$sql="SELECT * from invoice_".$branch_id." where client='$uid' and active='0' and branch_id='".$branch_id."' order by id desc limit 1";
	$result=query_by_id($sql,[],$conn);
	if($result)
	{
		foreach($result as $row)
		{
			return $row['doa'];
		}
		}else{
		return "NA";
	}
}