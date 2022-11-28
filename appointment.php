<?php
	include "./includes/db_include.php";
	
	$branch_id = $_SESSION['branch_id'];
	$uid = $_SESSION['uid'];
	if(isset($_GET['id']) && $_GET['id']>0)
	{
		$eid = $_GET['id'];
		$edit = query_by_id("SELECT DISTINCT c.gender,ai.status as status, ai.bill_created_status,b.name as provider,ai.notes,ai.dis,ai.doa,ai.appdate,ai.itime,ai.id,ai.role,ai.total,ai.paid,ai.pay_method,ai.subtotal,CONCAT(ai.tax,',',ai.taxtype) as tax_type,ai.status,ai.dis as total_discount,c.name,c.id as client_id,c.cont,c.dob,c.aniversary from `app_invoice_".$branch_id."` ai LEFT JOIN `client` c on c.id=ai.client LEFT join `app_invoice_items_".$branch_id."` aii on aii.iid=ai.id LEFT JOIN `beauticians` b on b.id=aii.staffid where ai.active=0  and ai.id='$eid' and ai.branch_id='".$branch_id."' order by ai.id desc",[],$conn)[0];
	} else if(isset($_GET['enqid']) && $_GET['enqid']>0){
		$enqid = $_GET['enqid'];
		$edit = query_by_id("SELECT customer as name,cont,1 as enquiry,client_id FROM `enquiry` where id='$enqid' and active='0' and branch_id='".$branch_id."'",[],$conn)[0];
	}  
	
	if(isset($_GET['cancel_appointment']) && $_GET['cancel_appointment']=='1'){
	    $eid = addslashes(trim($_GET['id']));
	    query("UPDATE `app_invoice_".$branch_id."` set `app_status` = '2', `status`= 'Cancelled' WHERE `id` = '$eid'",[],$conn);
	    $_SESSION['t']  = 1;
	    $_SESSION['tmsg']  = "Appointment Cancelled Successfully";
	    echo '<meta http-equiv="refresh" content="0; url=appointment.php?id='.$eid.'" />';die();
	}
		
	if(isset($_POST['submit'])){
	    $client         = addslashes(trim($_POST['clientid'])); // client id
		$name           = addslashes(trim($_POST['client'])); // client name
		$cont           = addslashes(trim($_POST['cont'])); // client contact number
		$gender         = addslashes(trim($_POST['gender']));
		$gst            = addslashes(trim(($_POST['gst']) ? $_POST['gst'] : ''));
		$dob            = addslashes(trim($_POST['dob']));
		$aniv           = addslashes(trim($_POST['aniv']));  // anniversary date 
		$time 	        = addslashes(trim(date('H:i',strtotime($_POST['time'])))); // appointment time
		$leadsource     = addslashes(trim($_POST['leadsource'])); //source of client
		if($client == ''){
    		$client=get_insert_id("INSERT INTO `client` set `name`=:name,`cont`=:cont,`gst`=:gst,`gender`=:gender,`dob`=:dob,`aniversary`=:aniversary,`leadsource`=:leadsource, `active`=:active, `branch_id`='".$branch_id."'",['name'=>$name,'cont'  =>$cont,'gst'=>$gst,'gender'=>$gender,'dob'=>$dob,'aniversary'=>$aniv,'leadsource'=>$leadsource,'active'=>0],$conn);
    		query("INSERT INTO wallet SET iid='0', date = '".date('Y-m-d')."', client_id = '$client', wallet_amount='0', get_wallet_from='', status='1', branch_id='".$branch_id."'",[],$conn);
		} else {
			query("UPDATE `client` set `gender`=:gender,`dob`=:dob,`aniversary`=:aniversary, `leadsource`=:leadsource where id=:id and branch_id='".$branch_id."'",['gender'=>$gender,'dob'=>$dob,'aniversary'=>$aniv,'leadsource'=>$leadsource, 'id'=>$client],$conn);
		} 

		if($_POST['total_disc_row_type'] == '0'){   // discount type (INR or %)
			$dis = 'pr,'.$_POST['dis'];             // if %
		} else if($_POST['total_disc_row_type'] == '1'){
			$dis 	= CURRENCY.','.$_POST['dis'];   // if INR
		}
		
		$doa    = $_POST['doa'];
		// $disper = $_POST['discount2'];
		$role   = $_POST['role'];  //(source of appointment)
		$appdate = date('Y-m-d');
		$tax	 = $_POST['tax'];  // type of tax
		$taxx	 = explode(",",$tax);  // 
		$tax	 = $taxx[0];
		$typ 	 = $taxx[2];
		
		$adv 	= 0;   // advance payment
		$total 	= $_POST['total'];
		$sub_total=$_POST['subtotal'];
		$due    = $total - $adv;
		
		$method = $_POST['method'];   // payment method
		$stat 	= $_POST['status'];   // appointment status
		$detail = $_POST['detail'];   // missing field
		
		$gtime  = $_POST['time'];   // appointment time
		$drr 	= 0;
		$notes  = addslashes(trim($_POST['notes']));  // extra notes for appointment

		$client_branch_id = $_POST['client_branch_id'];

		$aid = get_insert_id("INSERT INTO `app_invoice_".$branch_id."`(`client`,`doa`,`itime`,`role`,`dis`,`disper`,`tax`,`taxtype`,`pay_method`,`total`,`subtotal`,`bmethod`,`paid`,`due`,`notes`,`type`, `status`,`details`,`appdate`,`active`,`appuid`,`branch_id`,`client_branch_id`) VALUES ('$client','$doa','$time','$role','$dis','$disper','$tax','$typ','$method','$total','$sub_total','$method','$adv','$due','$notes','1','$stat','$detail','$appdate',0,'$uid','$branch_id','$client_branch_id')",[],$conn);

		$gtime = $time;
		$advance = 0;
		for($count = 0; $count < count($_POST['method']); $count++){
			$transaction_id = $_POST['transid'][$count];
			$adv = trim($_POST['adv'][$count]);
			$amount = trim($_POST['adv'][$count]);
			$method = trim($_POST['method'][$count]);
			if($adv == '0' || $adv == ''){
				continue;
			} else {
				if(count($_POST['method']) == 1){
					$advance = $adv;
				} else {
					$advance = $advance+$adv;
				}
				query("INSERT INTO `multiple_payment_method` (`invoice_id`,`payment_method`,`amount_paid`,`status`, `branch_id`,`transaction_id`) VALUES ('app,$aid','$method','$amount',1, '$branch_id','$transaction_id')",[],$conn);
				if($method == 7){
					query("INSERT INTO `wallet_history` (`client_id`,`iid`,`transaction_type`,`wallet_amount`,`get_wallet_from`,`status`,`branch_id`) VALUES ('$client','app,$aid',0,'$amount','Appointment',1,'$branch_id')",[],$conn);
					query("UPDATE `wallet` set `wallet_amount`= (wallet_amount-'$amount') WHERE `client_id` = '$client' and `branch_id`='".$branch_id."'",[],$conn);
				}
				if($method == 9){
					$rpoint = redeempoint(1);
					$points = $amount*($rpoint);
					query("INSERT INTO `customer_reward_points` SET `invoice_id`='app,$aid',`client_id`='cust,$client',`points_on`='0',`point_type`='2',`points`='$points',`notes`='Points redeem at the time of appointment.',`status`='1', `branch_id`='".$branch_id."'",[],$conn);
				}
			}
		}

		query("UPDATE `app_invoice_".$branch_id."` set `paid` = '$advance', `due` = (due-$advance) WHERE `id` = '$aid' and `branch_id`='".$branch_id."'",[],$conn);

        $totalTime = 0;
		for($t=0;$t<count($_POST["services"]);$t++){
			$ser = addslashes(trim($_POST["service"][$t]));
			$prc = $_POST["price"][$t];
			$qt  = $_POST["qt"][$t];
			$dur = $_POST["durr"][$t];
			
			$pservice = $_POST['pa_ser'][$t];
			if($pservice != ''){
				$id = explode("-",$pservice);
				$id = $id[1];
				$sql_up = "update client_package_services_used set quantity_used = (quantity_used+1) where id='".$id."'";
				query($sql_up,[],$conn); 
				$sql_up_2 = "update client_package_services_used set tmp_qty = '0' where id='".$id."'";
				query($sql_up_2,[],$conn); 
				$package_id = query_by_id("SELECT c_pack_id FROM client_package_services_used WHERE id='".$id."'",[],$conn)[0]['c_pack_id'];
    			query("INSERT INTO package_service_history SET package_id='".$package_id."', service_id='".$id."', quantity='1', client_id='".$client."', used_in_branch='".$branch_id."', invoice_id='".$aid."', status='1'",[],$conn);
			} else {
				$id = '0';
			}


			if($_POST['disc_row_type'][$t] == '0'){
				$disc_row = 'pr,'.$_POST["disc_row"][$t];
				}else if($_POST['disc_row_type'][$t] == '1'){
				$disc_row =  CURRENCY.','.$_POST["disc_row"][$t];
			}
			
			$ser_stime = $_POST["ser_stime"][$t];
			 $ser_etime = $_POST["ser_etime"][$t];
			
			$start = date("H:i:s",strtotime($ser_stime));
			$end = date("H:i:s",strtotime($ser_etime));
			
			$start1 = strtotime($start);
			$end1 = strtotime($end);
			
			$diff = $end1 - $start1;
              $ldiff = strtotime($diff);
             (date("H:i:s", strtotime($ldiff)));
            
			$totalTime = strtotime($totalTime);
			$totalTime = $totalTime + $ldiff;
				 date("H:i:s", strtotime($totalTime));	
			$serr = explode(",", $ser); 
			if($serr[0]=="sr"){
				$serr[0] = "Service";
			}else if($serr[0]=="pr"){
				$serr[0] = "Product";
			}else if($serr[0]=="pa"){
				$serr[0] = "Package";
				query($con,"INSERT INTO `clientpackages`(`package`,`client`,`inv`,`active`,`branch_id`) VALUES ('$serr[1]','$client','$eid',0,'$branch_id')",[],$conn);
			}

			$app_inv_item_id = get_insert_id("INSERT INTO `app_invoice_items_".$branch_id."` set `iid`='$aid',`client`='$client',`service`='$ser', `package_service_id`='$id',`quantity`='$qt',`staffid`='0',`disc_row`='$disc_row',`price`='$prc',`type`='Service',`start_time`='$ser_stime',`end_time`='$ser_etime',`app_date`='$doa',`active`=0, `branch_id`='".$branch_id."', `client_branch_id`='".$client_branch_id."'",[],$conn);    
			
			$dur = $_POST["durr"][$t];
			$drr = $drr + $dur * 60;
			$gtime = strtotime($_POST['time']) + $drr;
			$gtime = date('H:i', $gtime);
			
			//$system = systemname();
			//send_sms($bcont,"You have a new appointment on ".$date." at ".$time.".".$system);
			
			
			
			$ser_cat_id=$_POST['ser_cat_id'][$t];	
			for($j=0;$j<count($_POST["staffid".$t.""]);$j++){

				$staffid = $_POST["staffid".$t.""][$j];
				$inv_item_id = get_insert_id("INSERT INTO `app_multi_service_provider` set `iid`='$aid',`aii_staffid`='$app_inv_item_id',`service_cat`='$ser_cat_id',`service_name`='$ser ',`service_provider`='$staffid',`status`='1', `branch_id`='".$branch_id."'",[],$conn);
			}
		}
	
			  $room = $_POST['vip_room'];
		query("UPDATE `vip_rooms` set `allocated` = '1', `allocated_by` = '".$uid."', `allocated_for` = '".$client."', `allocated_time_period` = '".$gtime."', `allocated_date` = '".$doa."'  WHERE `id` = '".$room."' and `branch_id`='".$branch_id."'",[],$conn);
		
		
	
		
		if(isset($_GET['enqid']) && $_GET['enqid']>0){
			$equiry_id = $_GET['enqid'];
			$enquiryresponse_id = $_GET['enquiryresponse_id'];
			$date = date("Y-m-d");
			$now = new DateTime();
			$time = $now->format('H:i:s');
			query("update `enquiryresponse` set `leadstatus`='Converted',date='$date',`time`='$time' where id='$enquiryresponse_id' and branch_id='".$branch_id."'",[],$conn);
			query("update `enquiry` set `leadstatus`='Converted' where id='$equiry_id' and branch_id='".$branch_id."'",[],$conn);
		}
		
		
		
		
		$_POST['ser_stime'][0];
	
		$sms_data = array(
	        'name' => $name,
	        'date' => date('d-m-Y',strtotime($doa)),
	        'time' => date('h:i a',strtotime(explode(" ",$_POST['ser_stime'][0])[1])),
	        'salon_name' => systemname()
	    );
		
		send_sms($cont,$sms_data,'appointment_booking_software');
	 
		$_SESSION['t']  = 1;
		$_SESSION['tmsg']  = "Appointment Saved Successfully";
		echo '<meta http-equiv="refresh" content="0; url=appointment.php" />';die(); 
	}
	else if(isset($_POST['edit-submit'])){
		$eid                = addslashes(trim($_GET['id']));
		$client             = addslashes(trim($_POST['clientid']));
		$gst	            = addslashes(trim(($_POST['gst'])?$_POST['gst']:''));
		$name               = addslashes(trim($_POST['client']));
		$cont               = addslashes(trim($_POST['cont']));
		$gender             = addslashes(trim($_POST['gender']));
		$dob                = addslashes(trim($_POST['dob']));
		$aniv               = addslashes(trim($_POST['aniv']));
		$time 	            = addslashes(trim(date('H:i',strtotime($_POST['time']))));
		$leadsource         = addslashes(trim($_POST['leadsource']));
		$package_service    = addslashes(trim($_POST['package_service']));

		if($client == ''){
		    $client=get_insert_id("INSERT INTO `client` set `name`=:name,`cont`=:cont,`gst`=:gst,`gender`=:gender,`dob`=:dob,`aniversary`=:aniversary,`leadsource`=:leadsource, `active`=:active,  `branch_id`='".$branch_id."'",['name'=>$name,'cont'=>$cont,'gst'=>$gst,'gender'=>$gender,'dob'=>$dob,'aniversary'=>$aniv,	 'active' =>0],$conn);
		    query("INSERT INTO wallet SET iid='0', date = '".date('Y-m-d')."', client_id = '$client', wallet_amount='0', get_wallet_from='', status='1', branch_id='".$branch_id."'",[],$conn);
		}else{
			query("UPDATE `client` set `gender`=:gender,`dob`=:dob,`aniversary`=:aniversary, `leadsource`=:leadsource where id=:id and branch_id='".$branch_id."'",['gender'=>$gender,'dob'=>$dob,'aniversary'=>$aniv, 'leadsource' => $leadsource, 'id'=>$client],$conn);
		}
		
		if($_POST['total_disc_row_type'] == '0'){
			$dis = 'pr,'.$_POST['dis'];
		}else if($_POST['total_disc_row_type'] == '1'){
			$dis = CURRENCY.','.$_POST['dis'];
		}
		$doa    = $_POST['doa'];
		$disper = $_POST['discount2'];
		$role   = $_POST['role'];
		$appdate = date('Y-m-d');
		$tax	 = $_POST['tax'];
		$taxx	 = explode(",",$tax);
		$tax	 = $taxx[0];
		$taxtype = $taxx[2];
		
		$adv 	= $_POST['adv'];
		$total 	= $_POST['total'];
		$sub_total=$_POST['subtotal'];
		$adv = 0;
		$due    = $total - $adv;
		$method = $_POST['method'];
		$stat 	= $_POST['status'];
		$detail = addslashes(trim($_POST['detail']));

		$client_branch_id = $_POST['client_branch_id'];

		if($stat == 'Cancelled'){
			for($t=0;$t<count($_POST["services"]);$t++){
				$pservice_rev = $_POST['pa_ser'][$t];
				if($pservice_rev != ''){
					$id_rev = explode("-",$pservice_rev);
					$id_rev = $id_rev[1];
					$used_qty = query("select quantity_used from client_package_services_used WHERE id='".$id_rev."'",[],$conn)[0]['quantity_used'];
					if($used_qty > 0){
						$sql_up_rev = "update client_package_services_used set quantity_used =  '$used_qty'-1 where id='".$id_rev."'";
						query($sql_up_rev,[],$conn); 
						$package_id = query_by_id("SELECT c_pack_id FROM client_package_services_used WHERE id='".$id."'",[],$conn)[0]['c_pack_id'];
	    				query("UPDATE package_service_history SET status='0' WHERE package_id='".$package_id."' and service_id='".$id."' and client_id='".$client."' and used_in_branch='".$branch_id."'",[],$conn);
						}
					$sql_up_2_rev = "update client_package_services_used set tmp_qty = '0' where id='".$id_rev."'";
					query($sql_up_2_rev,[],$conn); 
				}
			}

			$q = "SELECT SUM(amount_paid) as amount FROM multiple_payment_method WHERE invoice_id='app,$eid' AND (payment_method <> 8 OR payment_method <> 9) AND status = 1 and branch_id='".$branch_id."'";
			$res = query_by_id($q,[],$conn)[0];
			if(count($res) > 0){
				query("INSERT INTO `wallet_history` (`client_id`,`iid`,`transaction_type`,`wallet_amount`,`get_wallet_from`,`status`,`branch_id`) VALUES ('$client','app,$eid',2,'".$res['amount']."','Appointment',1,'$branch_id')",[],$conn);
				if(!isset($res['amount'])){
					$res['amount'] = 0;
				}
				
				query("UPDATE `wallet` set `wallet_amount`= (wallet_amount+".$res['amount'].") WHERE `client_id` = '$client' and branch_id='".$branch_id."'",[],$conn);

			}

			query("UPDATE `app_invoice_".$branch_id."` set `status`= '$stat' WHERE `id` = '$eid' and branch_id='".$branch_id."'",[],$conn);

			$query = "SELECT * FROM customer_reward_points WHERE invoice_id = 'app,$eid' AND status = 1 and branch_id='".$branch_id."'";
			$result = query_by_id($query,[],$conn);
			if($result){
				foreach ($result as $res) {
					query("INSERT INTO customer_reward_points set client_id = '".$res['client_id']."', invoice_id = '".$res['invoice_id']."', points_on = '0', point_type = '3', points = '".$res['points']."', notes = 'Refunded', status = '1', branch_id='".$branch_id."'",[],$conn);
				}
			}

			$_SESSION['t']  = 1;
			$_SESSION['tmsg']  = "Appointment Cancelled Successfully";
			echo '<meta http-equiv="refresh" content="0; url=appointment.php" />';die();
		} else {
		
		$gtime  = $_POST['time'];
		$drr 	= 0;
		$notes  = addslashes(trim($_POST['notes']));
		
		query("DELETE from `app_invoice_items_".$branch_id."` where iid='$eid' and branch_id='".$branch_id."'",[],$conn);
		
		query("DELETE from `app_multi_service_provider` where iid='$eid' and branch_id='".$branch_id."'",[],$conn);

		query("DELETE from `multiple_payment_method` where invoice_id='app,$eid' and branch_id='".$branch_id."'",[],$conn);
		
		query("UPDATE `app_invoice_".$branch_id."` SET `client`='$client',`doa`='$doa',`itime`='$time',`dis`='$dis',`disper`='$disper',`tax`='$tax',`taxtype`='$taxtype',`pay_method`='$method',`total`='$total', `subtotal`='$sub_total',`paid`='$adv',`due`='$due',`notes`='$notes',`coupon`='$coupon',`bpaid`='$paid',`bmethod`='$method',`type`='2',`status`='$stat',`invoice`=1,`active`=0, `client_branch_id`='".$client_branch_id."' WHERE id=:eid and branch_id='".$branch_id."'",["eid"=>$eid],$conn); 


		$query = "SELECT * FROM customer_reward_points WHERE invoice_id = 'app,$eid' AND status = 1 and branch_id='".$branch_id."' ORDER BY id DESC LIMIT 1";
		$result = query_by_id($query,[],$conn);
		if($result){
			foreach ($result as $res) {
				query("INSERT INTO customer_reward_points set client_id = '".$res['client_id']."', invoice_id = '".$res['invoice_id']."', points_on = '0', point_type = '3', points = '".$res['points']."', notes = 'Refunded', status = '1', branch_id='".$branch_id."'",[],$conn);
			}
		}
		$query3 = "SELECT * FROM wallet_history WHERE iid = 'app,$eid' AND transaction_type = 0 AND status = 1 and branch_id='".$branch_id."' ORDER BY id DESC LIMIT 1";
		$result3 = query_by_id($query3,[],$conn);
		if($result3){
			foreach ($result3 as $res3) {
				query("INSERT INTO `wallet_history` (`client_id`,`iid`,`transaction_type`,`wallet_amount`,`get_wallet_from`,`status`,`branch_id`) VALUES ('".$res3['client_id']."','".$res3['iid']."',2,'".$res3['wallet_amount']."','Appointment',1,'$branch_id')",[],$conn);
				query("UPDATE `wallet` set `wallet_amount`= (wallet_amount+".$res3['wallet_amount'].") WHERE `client_id` = '".$res3['client_id']."' and branch_id='".$branch_id."'",[],$conn);
			}
		}
		
		// query("UPDATE `app_invoice_items` SET `active`=1 WHERE iid=:inv",["inv"=>$eid],$conn);
		// query("UPDATE `transactions` SET `active`=1 WHERE iid=:inv",["inv"=>$eid],$conn);


		$gtime = $time;
		$advance = 0;
		for($count = 0; $count < count($_POST['method']); $count++){
			$transaction_id = $_POST['transid'][$count];
			$adv = trim($_POST['adv'][$count]);
			$amount = trim($_POST['adv'][$count]);
			$method = trim($_POST['method'][$count]);
			if($adv == '0' || $adv == ''){
				continue;
			} else {
				if(count($_POST['method']) == 1){
					$advance = $adv;
				} else {
					$advance = $advance+$adv;
				}
				query("INSERT INTO `multiple_payment_method` (`invoice_id`,`payment_method`,`amount_paid`,`status`,`branch_id`,`transaction_id`) VALUES ('app,$eid','$method','$amount',1,'$branch_id','$transaction_id')",[],$conn);
				if($method == 7){
					query("INSERT INTO `wallet_history` (`client_id`,`iid`,`transaction_type`,`wallet_amount`,`get_wallet_from`,`status`,`branch_id`) VALUES ('$client','app,$eid',0,'$amount','Appointment',1,'$branch_id')",[],$conn);
					query("UPDATE `wallet` set `wallet_amount`= (wallet_amount-'$amount') WHERE `client_id` = '$client' and branch_id='".$branch_id."'",[],$conn);
				}
				if($method == 9){
					$rpoint = redeempoint(1);
					$points = $amount*($rpoint);
					query("INSERT INTO `customer_reward_points` SET `invoice_id`='app,$eid',`client_id`='cust,$client',`points_on`='0',`point_type`='2',`points`='$points',`notes`='Points redeem at the time of appointment.',`status`='1', `branch_id`='".$branch_id."'",[],$conn);
				}
			}
		}
		query("UPDATE `app_invoice_".$branch_id."` set `paid` = '$advance', `due` = (due-$advance) WHERE `id` = '$eid' and branch_id='".$branch_id."'",[],$conn);
		if(isset($_POST['web_appointment']) && $_POST['web_appointment'] == 1){
		    query("UPDATE `app_invoice_".$branch_id."` set `app_status` = '1' WHERE `id` = '$eid'",[],$conn);
		}
		for($t=0;$t<count($_POST["services"]);$t++){
			$pservice_rev = $_POST['pa_ser'][$t];
			if($pservice_rev != ''){
				$id_rev = explode("-",$pservice_rev);
				$id_rev = $id_rev[1];
				$used_qty = query("select quantity_used from client_package_services_used WHERE id='".$id_rev."'",[],$conn)[0]['quantity_used'];
				if($used_qty > 0){
					$sql_up_rev = "update client_package_services_used set quantity_used =  '$used_qty'-1 where id='".$id_rev."'";
					query($sql_up_rev,[],$conn);
					$package_id = query_by_id("SELECT c_pack_id FROM client_package_services_used WHERE id='".$id."'",[],$conn)[0]['c_pack_id'];
	    			query("UPDATE package_service_history SET status='0' WHERE package_id='".$package_id."' and service_id='".$id."' and client_id='".$client."' and used_in_branch='".$branch_id."'",[],$conn);
				} 
				$sql_up_2_rev = "update client_package_services_used set tmp_qty = '0' where id='".$id_rev."'";
				query($sql_up_2_rev,[],$conn); 
			}
		}
		
		for($t=0;$t<count($_POST["services"]);$t++){
			$ser = addslashes(trim($_POST["service"][$t]));
			$ser_cat_id=$_POST['ser_cat_id'][$t];
			$prc = $_POST["price"][$t];
			$qt = $_POST["qt"][$t];
			$dur = $_POST["durr"][$t];
			
			$pservice = $_POST['pa_ser'][$t];
			if($pservice != ''){
				$id = explode("-",$pservice);
				$id = $id[1];

				$sql_up = "update client_package_services_used set quantity_used = quantity_used+1 where id='".$id."'";
				query($sql_up,[],$conn); 
				$sql_up_2 = "update client_package_services_used set tmp_qty = '0' where id='".$id."'";
				query($sql_up_2,[],$conn); 
				$package_id = query_by_id("SELECT c_pack_id FROM client_package_services_used WHERE id='".$id."'",[],$conn)[0]['c_pack_id'];
    			query("INSERT INTO package_service_history SET package_id='".$package_id."', service_id='".$id."', quantity='1', client_id='".$client."', used_in_branch='".$branch_id."', invoice_id='".$eid."', status='1'",[],$conn);

			} else {
				$id = '0';
			}
			
			if($_POST['disc_row_type'][$t] == '0'){
				$disc_row = 'pr,'.$_POST["disc_row"][$t];
				}else if($_POST['disc_row_type'][$t] == '1'){
				$disc_row = CURRENCY.','.$_POST["disc_row"][$t];
			}
			
			$ser_stime = $_POST["ser_stime"][$t];
			$ser_etime = $_POST["ser_etime"][$t];
			
			$serr = explode(",", $ser); 
			if($serr[0]=="sr"){
				$serr[0] = "Service";
				}else if($serr[0]=="pr"){
				$serr[0] = "Product";
				}else if($serr[0]=="pa"){
				$serr[0] = "Package";
				query($con,"INSERT INTO `clientpackages`(`package`,`client`,`inv`,`active`,`branch_id`) VALUES ('$serr[1]','$client','$eid',0,'$branch_id')",[],$conn);
			}
			$prc = $_POST["price"][$t];
			$qt = $_POST["qt"][$t];
			
			$app_inv_item_id = get_insert_id("INSERT INTO `app_invoice_items_".$branch_id."` set `iid`='$eid',`client`='$client',`service`='$ser', `package_service_id`='$id', `quantity`='$qt',`staffid`='$staffid',`disc_row`='$disc_row',`price`='$prc',`type`='Service',`start_time`='$ser_stime',`end_time`='$ser_etime',`app_date`='$doa',`active`=0, `branch_id`='".$branch_id."', `client_branch_id`='".$client_branch_id."'",[],$conn);
			
			//query("INSERT INTO `transactions` (`iid`,`inv`,`client`,`service`,`quantity`,`staffid`,`disc_row`,`price`,`type`, `credit`,`debit`,`date`,`active`) VALUES ('$eid','$eid','$client','$serr[1]','$qt','$staffid','$disc_row','$prc','$serr[0]','$prc',0,'$doa',0)",[],$conn);

			$dur = $_POST["durr"][$t];
			$drr = $drr + $dur * 60;
			$gtime = strtotime($_POST['time']) + $drr;
			$gtime = date('H:i', $gtime);

			
			for($j=0;$j<count($_POST["staffid".$t.""]);$j++){
				$staffid = $_POST["staffid".$t.""][$j];
				$inv_item_id = get_insert_id("INSERT INTO `app_multi_service_provider` set `iid`='$eid',`aii_staffid`='$app_inv_item_id',`service_cat`='$ser_cat_id',`service_name`='$ser ',`service_provider`='$staffid',`status`='1', `branch_id`='".$branch_id."'",[],$conn);
			}	
			
		}
		
		$ip = clientip();
		// activity($eid,$client,$uid,$subtotal,$ip);
		$_SESSION['t']  = 1;
		if(isset($_POST['web_appointment']) && $_POST['web_appointment'] == 1){
		    $_SESSION['tmsg']  = "Appointment approved Successfully";
		} else {
		    $_SESSION['tmsg']  = "Appointment Updated Successfully";
		}
		echo '<meta http-equiv="refresh" content="0; url=appointment.php?id='.$eid.'" />';die();	
		}	
	}
	
	if(isset($_GET['del_id'])){
	    if(DELETE_BUTTON_INACTIVE != 'true'){
    		$id = $_GET['del_id'];
    		$client_id = $_GET['c_id'];

    		$query3 = "SELECT * FROM wallet_history WHERE iid = 'app,$id' AND transaction_type = 0 AND status = 1 and branch_id='".$branch_id."'";
    		$result3 = query_by_id($query3,[],$conn);
    		if($result3){
    			foreach ($result3 as $res3) {
    				query("INSERT INTO `wallet_history` (`client_id`,`iid`,`transaction_type`,`wallet_amount`,`get_wallet_from`,`status`,`branch_id`) VALUES ('".$res3['client_id']."','".$res3['iid']."',2,'".$res3['wallet_amount']."','Appointment',1,'$branch_id')",[],$conn);
    				query("UPDATE `wallet` set `wallet_amount`= (wallet_amount+".$res3['wallet_amount'].") WHERE `client_id` = '".$res3['client_id']."' and branch_id='".$branch_id."'",[],$conn);
    			}
    		}
    
    		$q = "SELECT SUM(amount_paid) as amount FROM multiple_payment_method WHERE invoice_id='app,$id' AND (payment_method <> 8 OR payment_method <> 9) AND status = 1 and branch_id='".$branch_id."'";
    		$res = query_by_id($q,[],$conn)[0];
    		if(count($res) > 0){
    			query("INSERT INTO `wallet_history` (`client_id`,`iid`,`transaction_type`,`wallet_amount`,`get_wallet_from`,`status`,`branch_id`) VALUES ('$client_id','app,$id',2,'".$res['amount']."','Appointment',1,'$branch_id')",[],$conn);
    			if(!isset($res['amount'])){
    					$res['amount'] = 0;
    				}
    			query("UPDATE `wallet` set `wallet_amount`= (wallet_amount+".$res['amount'].") WHERE `client_id` = '$client_id' and branch_id='".$branch_id."'",[],$conn);
    		}
    		query("UPDATE `app_invoice_".$branch_id."` SET `active`=1 WHERE id='$id' and branch_id='".$branch_id."'",[],$conn);
    		query("UPDATE `app_invoice_items_".$branch_id."` SET `active`=1 WHERE iid='$id' and branch_id='".$branch_id."'",[],$conn);
    		query("UPDATE `app_multi_service_provider` SET `status`=0 WHERE iid='$id' and branch_id='".$branch_id."'",[],$conn);
    		query("UPDATE `service_slip` SET `active`=1 WHERE appointment_id='$id' and branch_id='".$branch_id."'",[],$conn);
    		$_SESSION['t']  = 1;
    		$_SESSION['tmsg']  = "Appointment Removed Successfully";
    		echo '<meta http-equiv="refresh" content="0; url=appointment.php" />';die();
	    }
	}
	
	include "topbar.php";
	include "header.php";
	include "menu.php";
?>
<style>
	.btn-add{
	padding: 6px 6px;
	margin-left :100px;
	position:relative;
	
	}
	.btn-remove{
	padding: 6px 6px;
	margin-left :100px;
	position:relative;
	}
</style>
<div class="dashboard-wrapper">
	
	<!-- Main container starts -->
	<div class="main-container">
		<form action="" method="post" id="main-form">
		<!-- Row starts -->
		<div class="row gutter">
			
			<div class="col-lg-9 col-md-12 col-sm-12 col-xs-12">
				
					<div class="panel">
						<div class="panel-heading">
							<h4><?= isset($eid)&&$eid!=''?'Update':'Create'; ?> appointment</h4>
						</div>
						<div class="panel-body">
						    <div id="member_ship_message"></div>
							<div class="row">
							<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
								<div class="form-group">
									<label for="client">Client name <span class="text-danger">*</span></label>
									<input type="text" class="client form-control client_name" id="client" name="client" placeholder="Autocomplete (Phone)" value="<?=$edit['name']?>" required>
									<input type="hidden" name="clientid" id="clientid" value="<?=$edit['client_id']?>" class="clt"> 
									<input type="hidden" name="client_branch_id" id="client_branch_id" value="<?=$edit['client_branch_id']?>" class="clt"> 
									
								</div>
							</div>
							<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
								<div class="form-group">
									<label for="cont">Contact number <span class="text-danger">*</span></label>
									<input type="text" class="form-control client" value="<?=$edit['cont']?>"  onBlur="check();contact_no_length($(this), this.value);" id="cont" name="cont" placeholder="Client contact" onkeyup="this.value = this.value.replace(/[^0-9\.]/g,'');" maxLength="<?= PHONE_NUMBER ?>" required>
									<span style="color:red" id="client-status"></span>
									<span style="color:red" id="digit_error"></span>
								</div>
							</div>	
							<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
								<div class="form-group">
									<?php $date = date('Y-m-d'); ?>
									<label for="doa">Appointment is on <span class="text-danger">*</span></label>
									<input type="text" class="form-control dat <?= isset($edit['id'])?'date':'min_present_date' ?>" id="date" onblur="$('.staff').val('')" onchange="dateAvailability(this.value)" value="<?=($edit['doa'])?$edit['doa']:$date ?>" name="doa" required readonly />
									<span class="text-danger" id="dateerror"></span>
								</div>
							</div>
							
							<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
								<div class="form-group">
									<?php $time = date('h:i A'); ?>
									<label for="time">Time of appointment <span class="text-danger">*</span></label>
									<input type='text' onchange="checkappTime('time',this.value,'apptime')" class="form-control slot dat <?= extratimeStatus()=='1'?'maintime':'time'?>" name="time"  value="<?=($edit['itime'])?date('h:i A',strtotime($edit['itime'])):$time; ?>" id="time" required readonly>
									<input type="text" class="hidden" id="close_time" value="<?= shopclosetime(); ?>">
									<span id="apptime" class="text-danger"></span>
								</div>
							</div>
							<div class="clearfix"></div>
							<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
								<div class="form-group">
									<label for="role">Source of appointment</label>
									<select class="form-control" name="role">
										<?php 
											$app_source = query_by_id("SELECT * from app_source where status='1' order by name asc",[],$conn);
											if($app_source){
												foreach ($app_source as $val){ ?>
													<option <?= $edit['role']==$val['id']?'selected':''?> value="<?=$val['id']?>"><?= $val['name'] ?></option>
												<?php }
											}								
										?>
									</select>
								</div>
							</div>
							<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
								<div class="form-group">
									<label for="">&nbsp;</label>
									<button type="button" onclick="todaySchedule($('#date').val())" class="btn btn-warning btn-block"><i class="fa fa-calendar" aria-hidden="true"></i>Check schedule</button>
								</div>
							</div>
							<div class="clearfix"></div>
							<div class="col-lg-12">
								<div class="table-responsive">
									<table id="catTable" class="table table-bordered">
										<thead>
											<tr>
												
												<th width="100%" colspan="2">Select service</th>
												<th >Discount</th>
												<th >Service provider</th>
												<th >Start & end time</th>
												<th >Price</th>
											</tr>
										</thead>
										<tbody>
										<?php 
											if(isset($_GET['id']) && $_GET['id']>0)
											{
													$app_id =  $_GET['id'];	
													$sql2="SELECT  msp.aii_staffid,msp.service_Provider,b.name,b.id b_id,aii.client,aii.service, aii.package_service_id, aii.quantity,aii.disc_row,aii.staffid,aii.price,aii.start_time,aii.end_time,s.price as service_price,s.name as service_name,s.cat,CONCAT('sr,',s.id) as service_id,s.duration as service_durr,scat.cat as cat_name from `app_invoice_items_".$branch_id."` aii"
													." LEFT JOIN `service` s on s.id=SUBSTR(aii.service,4)"
													." LEFT JOIN `servicecat` scat on scat.id=s.cat"
													." LEFT JOIN `app_multi_service_provider` msp on msp.aii_staffid=aii.id"
													." LEFT JOIN `beauticians` b on b.id=msp.service_Provider"
													." where aii.iid='$app_id' and aii.active=0 and aii.type='Service' and aii.branch_id='".$branch_id."'  GROUP BY aii_staffid"; 
													
													$result2=query_by_id($sql2,[],$conn);
													if($result2){
														$predata = array();
														foreach($result2 as $row2) {
														if($row2['package_service_id'] != '0'){		
															array_push($predata, $row2['package_service_id']);
														}
														?>
														<tr id="TextBoxContainer" class="TextBoxContainer">
															<td style="vertical-align: middle"><span class="sno"><span class="icon-dots-three-vertical"></span></span></td>
															<td width="95%">
																<input type="text" class="ser form-control slot" name="services[]" value="<?=html_entity_decode(html_entity_decode($row2['service_name']))?>" placeholder="Service (Autocomplete)" required>
																<input type="hidden" name="service[]" value="<?=$row2['service_id']?>" class="serr">
																<input type="hidden" name="durr[]" value="<?=$row2['service_durr']?>" class="durr">
																<input type="hidden" name="pa_ser[]" value="<?= $row2['package_service_id'] != '0'?$row2['service'].'-'.$row2['package_service_id']:''?>" class="pa_ser">
															</td>
															<td>
																<table class="inner-table-space">
																	<tr>
																		<td width="40%">
																			<input type="number"  value="<?=explode(",",$row2['disc_row'])[1]?>" name="disc_row[]" class="form-control disc_row positivenumber decimalnumber" min="0" step="0.1">
																		</td>
																		<td width="60%">
																			<select class="form-control disc_row_type" name="disc_row_type[]" id="disc_row_type">
																				<option <?=(explode(",",$row2['disc_row'])[0] == 'pr')?'value="0" Selected':'value="0"'?>>%</option>
																				<option <?=(explode(",",$row2['disc_row'])[0] == CURRENCY)?'value="1" Selected':'value="1"'?>><?=CURRENCY?></option>
																			</select>
																		</td>
																	</tr>
																</table>
																
																
															</td>
															
															
															<td class="spr_row"> 
																
																<?php 
																	$aii_id= $row2['aii_staffid'];																													
																	$sql1="SELECT msp.service_Provider,b.name,b.id b_id FROM `app_multi_service_provider` msp  LEFT JOIN `beauticians` b on b.id=msp.service_Provider where msp.aii_staffid='$aii_id' and status='1' and msp.branch_id='".$branch_id."'";
																	
																	$result1 = query_by_id($sql1,[],$conn);
																	foreach ($result1 as $row_2){
																		
																	?>
																	<table id="add_row"><tbody><tr>
																		<td width="95%" id="select_row">
																			<select name="staffid0[]" data-validation="required" class="form-control staff" required>
																			<option value="">Service provider</option>          
																			<?php 
																				$sql1="SELECT * FROM beauticians WHERE find_in_set('".explode(',',$row2['service_id'])[1]."', services) <> 0 AND active = '0' and type='2' and branch_id='".$branch_id."' ORDER BY name ASC";
																				$sql2="SELECT * FROM beauticians WHERE active = '0' and type='2' and branch_id='".$branch_id."' ORDER BY name ASC";
																				$result1 = query_by_id($sql1,[],$conn);
																				$result2 = query_by_id($sql2,[],$conn);
																				if($result1){
    																				foreach ($result1 as $provider){
    																				?>    
    																				    <option <?=(($provider['id'] === $row_2['service_Provider'])?'value="'.$provider['id'].'"Selected':'value='.$provider['id']).''?>><?=$provider['name']?></option>
    																			<?php } } else if($result2) { 
    																			    foreach ($result2 as $provider){ ?>
    																			        <option <?=(($provider['id'] === $row_2['service_Provider'])?'value="'.$provider['id'].'"Selected':'value='.$provider['id']).''?>><?=$provider['name']?></option>
    																			<?php } } ?>
    																			 
																		</select>
																		
																		
																		</td>
																		<td id="plus_button" width="5%">
																			<span class="input-group-btn">
																				<button style="" class="btn btn-add btn-plus btn-success btn-add add_spr_row" type="button">
																					<span class="glyphicon-plus"></span>
																				</button>
																			</span>
																		</td>
																	</tr>
																	</tbody>
																	</table>
																<?php } ?>
															</td>
															<input type="hidden" name="duration[]" value="<?=$row2['service_durr']?>" class="duration">
															<input type="hidden" name="ser_stime[]" value="<?=$row2['start_time']?>" class="ser_stime">
															<input type="hidden" name="ser_etime[]" value="<?=$row2['end_time']?>" class="ser_etime">
															<td>
																<table>
																	<tr>
																		<td width="50%">
																			<input type="text" class="form-control start_time time" value="<?= date('h:i A',strtotime($row2['start_time']))?>" placeholder="Start time" name="start_time[]" onchange="servicestarttime(this.value, $(this))" readonly>
																			</td>						<td>&nbsp;to&nbsp;</td>																										       <td width="50%">
																			<input type="text" class="form-control end_time"  name="end_time[]" value="<?= date('h:i A',strtotime($row2['end_time']))?>"  placeholder="End time"  readonly>
																		</td>
																	</tr>
																</table>														
															</td> 
															<td>
																<input type="number" readonly class="pr form-control price positivenumber decimalnumber" readonly step="0.01" name="price[]" id="userName" placeholder="9800.00" value="<?=$row2['price']?>"> 
																<input type="hidden" class="prr" value="<?=$row2['service_price']?>">
															</td>
														</tr>
													<?php } } }
											else if(isset($_GET['enqid']) && $_GET['enqid']>0)
										    {
													$enq_id =  $_GET['enqid'];	
													$sql2="SELECT s.duration as service_durr,CONCAT('sr,',s.id) as service_id,s.name as service_name,s.cat,scat.cat as cat_name,s.price as service_price from `enquiry` e"
													." LEFT JOIN `service` s on concat('sr,',s.id)=e.enquiry"
													." LEFT JOIN `servicecat` scat on scat.id=s.cat"
													." where e.id='$enq_id' and e.active=0 and e.branch_id='".$branch_id."'"; 
													$result2=query_by_id($sql2,[],$conn);
													if($result2){
														foreach($result2 as $row2) {	   												   
														?>
														<tr id="TextBoxContainer" class="TextBoxContainer">
															<td style="vertical-align: middle"><span class="sno"><span class="icon-dots-three-vertical"></span></span></td>
															<td width="95%">
																<input type="text" class="ser form-control slot" name="services[]" value="<?=$row2['service_name']?>" placeholder="Service (Autocomplete)" required>
																<input type="hidden" name="service[]" value="<?=$row2['service_id']?>" class="serr">
																<input type="hidden" name="durr[]" value="<?=$row2['service_durr']?>" class="durr">
																<input type="hidden" name="pa_ser[]" value="" class="pa_ser">
															</td>
															<td>
															<table class="inner-table-space">
																	<tr>
																		<td width="40%">
																			<input type="number"  value="<?=explode(",",$row2['disc_row'])[1]?>" name="disc_row[]" class="form-control disc_row positivenumber decimalnumber" step="0.1" >
																		</td>
																		<td width="60%">
																			<select class="form-control disc_row_type" name="disc_row_type[]" id="disc_row_type">
																				<option <?=(explode(",",$row2['disc_row'])[0] == 'pr')?'value="0" Selected':'value="0"'?>>%</option>
																				<option <?=(explode(",",$row2['disc_row'])[0] == CURRENCY)?'value="1" Selected':'value="1"'?>><?=CURRENCY?></option>
																			</select>
																		</td>
																	</tr>
																</table>
																
																
															</td>
															
															
															<td class="spr_row"> 
																
																<?php 
																	$aii_id= $row2['aii_staffid'];																if($aii_id != ''){													
																		$sql1="SELECT msp.service_Provider,b.name,b.id b_id FROM `app_multi_service_provider` msp  LEFT JOIN `beauticians` b on b.id=msp.service_Provider where msp.aii_staffid='$aii_id' and status='1' and msp.branch_id='".$branch_id."'";
																		
																		$result1 = query_by_id($sql1,[],$conn);
																		foreach ($result1 as $row_2){
																			
																		?>
																		<table id="add_row" class="inner-table-space"><tbody><tr>
																			<td width="95%" id="select_row">                                                                                                                              <select name="staffid0[]" data-validation="required" class="form-control staff" required>
																				<option value="">Service provider</option>          
																				<?php 
																				$sql1="SELECT * FROM beauticians WHERE find_in_set('".explode(',',$row2['service_id'])[1]."', services) <> 0 AND active = '0' and type='2' and branch_id='".$branch_id."' ORDER BY name ASC";
																				$sql2="SELECT * FROM beauticians WHERE active = '0' and type='2' and branch_id='".$branch_id."' ORDER BY name ASC";
																				$result1 = query_by_id($sql1,[],$conn);
																				$result2 = query_by_id($sql2,[],$conn);
																				if($result1){
    																				foreach ($result1 as $provider){
    																				?>    
    																				    <option <?=(($provider['id'] === $row_2['service_Provider'])?'value="'.$provider['id'].'"Selected':'value='.$provider['id']).''?>><?=$provider['name']?></option>
    																			<?php } } else if($result2) { 
    																			    foreach ($result2 as $provider){ ?>
    																			        <option <?=(($provider['id'] === $row_2['service_Provider'])?'value="'.$provider['id'].'"Selected':'value='.$provider['id']).''?>><?=$provider['name']?></option>
    																			<?php } } ?>
																			</select>
																			
																			
																			</td>
																			<td id="plus_button" width="5%">
																				<span class="input-group-btn">
																					<button style="" class="btn btn-add btn-plus btn-success btn-add add_spr_row" type="button">
																						<span class="glyphicon-plus"></span>
																					</button>
																				</span>
																			</td>
																		</tr>
																		</tbody>
																		</table>
																	<?php } }else{ ?>
																	
																	<table id="add_row" class="inner-table-space"><tbody><tr>
																		<td width="95%" id="select_row">                                                                                                                              <select name="staffid0[]" data-validation="required" class="form-control staff" required>
																			<option value="">Service provider</option>          
																			<?php 
																				$sql1="SELECT * FROM `beauticians` where active='0' and type='2' and branch_id='".$branch_id."' order by name asc";
																				$result1 = query_by_id($sql1,[],$conn);
																				foreach ($result1 as $row1){
																				?>    
																				<option <?=(($row1['id'] === $row_2['service_Provider'])?'value="'.$row1['id'].'"Selected':'value='.$row1['id']).''?>><?=$row1['name']?></option>
																			<?php } ?>
																		</select>
																		
																		
																		</td>
																		<td id="plus_button" width="5%">
																			<span class="input-group-btn">
																				<button style="" class="btn btn-add btn-plus btn-success btn-add add_spr_row" type="button">
																					<span class="glyphicon-plus"></span>
																				</button>
																			</span>
																		</td>
																	</tr>
																	</tbody>
																	</table>
																	
																	
																<?php } ?>
															</td>
															<input type="hidden" name="duration[]" value="<?=$row2['service_durr']?>" class="duration">
															<input type="hidden" name="ser_stime[]" value="<?=$row2['start_time']?>" class="ser_stime">
															<input type="hidden" name="ser_etime[]" value="<?=$row2['end_time']?>" class="ser_etime">
															<td>
																<table class="inner-table-space">
																	<tr>
																		<td width="50%">
																			<input type="text" class="form-control start_time time" value="<?=date('h:i A',strtotime($row2['start_time']))?>" placeholder="Start time" name="start_time[]" onchange="servicestarttime(this.value, $(this))" readonly>
																			</td>																																       <td width="50%">
																			<input type="text" class="form-control end_time"  name="end_time[]" value="<?=date('h:i A',strtotime($row2['end_time']))?>"  placeholder="End time"  readonly>
																		</td>
																	</tr>
																</table>														
															</td> 
															<td>
																<input type="number" readonly class="pr form-control price positivenumber decimalnumber" step="0.01" name="price[]" id="userName" placeholder="9800.00" value="<?=$row2['price']?>"> 
																<input type="hidden" class="prr" value="<?=$row2['service_price']?>">
															</td>
														</tr>
													<?php } } } 
											else{ ?>
														
														<tr id="TextBoxContainer" class="TextBoxContainer">
															<td style="vertical-align: middle"><span class="sno"><span class="icon-dots-three-vertical"></span></span></td>
															<td width="95%"><input type="text" class="ser form-control slot" name="services[]" value="<?=$row2['service_name']?>" placeholder="Service (Autocomplete)" required>
																<input type="hidden" name="service[]" value="" class="serr">
																<input type="hidden" name="durr[]" value="" class="durr">
																<input type="hidden" name="pa_ser[]" value="" class="pa_ser">
															</td>
															<td>
																<table class="inner-table-space">
																	<tr>
																		<td width="50%">
																			<input type="number"   name="disc_row[]" class="form-control disc_row positivenumber decimalnumber" step="0.1" value="0">
																		</td>
																		<td width="50%">
																			<select class="form-control disc_row_type" name="disc_row_type[]" id="disc_row_type">
																				<option value="0" >%</option>
																				<option value="1" Selected><?=CURRENCY?></option>
																			</select>
																		</td>
																	</tr>
																</table>
																
																
															</td>
															<td class="spr_row"> 
																<table id="add_row" class="inner-table-space"><tbody><tr>
																	<td width="95%" id="select_row">
																		<select name="staffid0[]" data-validation="required" class="form-control staff" required>
																			<option value="">Service provider</option>
																			
																		</select>
																	</td>		
																	<td id="plus_button" width="5%">
																		<span class="input-group-btn">
																			<button style="" class="btn btn-add btn-plus btn-success btn-add add_spr_row" type="button">
																				<span class="glyphicon-plus"></span>
																			</button>
																		</span>
																	</td>
																</tr>
																</tbody>
																</table>
															</td>
															<input type="hidden" name="duration[]" value="" class="duration">
															<input type="hidden" name="ser_stime[]" value="" class="ser_stime">
															<input type="hidden" name="ser_etime[]" value="" class="ser_etime">
															<td>
																<table class="inner-table-space">
																	<tr>
																		<td width="50%">
																			<input type="text" class="form-control start_time time" value="" placeholder="Start time" name="start_time[]" onchange="servicestarttime(this.value, $(this))" readonly>
																		</td>
																		<td width="50%">
																			<input type="text" class="form-control end_time"  name="end_time[]" value=""  placeholder="End time"  readonly>
																		</td>
																	</tr>
																</table>
																
																
															</td> 
															<td>
																<input type="number" readonly class="pr form-control price positivenumber decimalnumber" step="0.01" name="price[]" id="userName" placeholder="9800.00" > 
																<input type="hidden" class="prr" >
															</td>
														</tr>
														
											<?php } ?>
											<tr id="addBefore">
												<td colspan="6"><button type="button" id="btnAdd" class="btn btn-success pull-right"><i class="fa fa-plus" aria-hidden="true"></i>Add service</button></td>
											</tr>
											<tr>
												<td class="total" colspan="5">Subtotal</td>
												<td><div id="sum" style="display: inline;"><?=($edit['subtotal'])?$edit['subtotal']:'0'?></div>
												<input type="hidden" id="sum2" value="<?=($edit['subtotal'])?$edit['subtotal']:'0'?>" name="subtotal"></td>
											</tr>
											<tr>
												<td class="total" colspan="4">Discount</td>
												<td width="40%">
												<input type="number" step="0.01" class="key1 form-control" name="dis" id="total_disc"  value="<?=($edit['total_discount'])?explode(",",$edit['total_discount'])[1]:'0'?>" placeholder="Discount Amount" min="0"></td>
												<td width="60%">
													<select class="form-control total_disc_row_type" name="total_disc_row_type" id="disc_row_type">
														<option <?=(substr($edit['total_discount'],0,3) == 'pr,')?'value="0" Selected':'value="0"'?>>%</option>
														<option <?=(explode(",",$edit['total_discount'])[0] == CURRENCY)?'value="1" Selected':'value="1"'?>><?=CURRENCY?></option>
													</select>
												</td>
											</tr>
											<!--<tr>
												<td class="total" colspan="6">Discount(in %)</td>
												<td>
												<input type="text" value="0" name="discount2" step="0.01" class="key form-control disc" id="disc2" placeholder="0"></td>
											</tr>-->
											<tr>
												<td class="total" colspan="4">Taxes</td>
												<td colspan="2"><select name="tax" id="tax" data-validation="required" class="form-control">
													<option value="0,0,3">Select Taxes</option>
													<optgroup label="Inclusive Taxes">
														<?php 
															$sql2="SELECT * FROM `tax` where active=0 order by title asc";
															$result2=query_by_id($sql2,[],$conn);
															foreach ($result2 as $row2) {
															?>
															<option <?=((($edit['tax_type']) == ($row2['id'].',0'))?'value="'.$row2['id'].','.$row2['tax'].',0"Selected':'value="'.$row2['id'].','.$row2['tax'].',0"')?>><?php echo $row2['title']; ?></option>
														<?php } ?>
													</optgroup>
													<optgroup label="Exclusive Taxes">
														<?php 
															$sql2="SELECT * FROM `tax` where active=0 order by title asc";
															$result2=query_by_id($sql2,[],$conn);
															foreach ($result2 as $row2) {
															?>
															<option <?=((($edit['tax_type']) == ($row2['id'].',1'))?'value="'.$row2['id'].','.$row2['tax'].',1" Selected':'value="'.$row2['id'].','.$row2['tax'].',1"')?>><?php echo $row2['title']; ?></option>
														<?php } ?>
													</optgroup>              
												</select></td>
											</tr>
											<tr>
												<td class="total" id="tot" colspan="5">Total</td>
												<td><input type="text" id="total" class="form-control" name="total" placeholder="Total Amount" value="<?=($edit['total'])?$edit['total']:'0'?>" readonly></td>
											</tr>
											<tr>
												<td class="total" id="tot" colspan="4">Select Vip Room</td>
												<td colspan="2"><select name="vip_room" id="vip_room" data-validation="required" class="form-control">
													<option>Select Vip room</option>
													
														<?php 
															$vipsql="SELECT * FROM `vip_rooms` where active=0";
															$vipresult=query_by_id($vipsql,[],$conn);
															$today = date('Y-m-d');
															$current_time = date('H:i');
															foreach ($vipresult as $viprow) {
                                                                if($viprow['allocated_time_period'] <= $current_time || $viprow['allocated_time_period'] >= $current_time && $viprow['allocated_date'] != $today){
															?>
                                                                                                                        
															<option value="<?php echo $viprow['id']; ?>"><?php echo $viprow['room_name']; ?></option>
                                                            <?php }else{ ?>
															<option value="<?php echo $viprow['id']; ?>" disabled><?php echo $viprow['room_name']; ?></option>
                                                                                                                            <?php } ?>
															<!--<option <?=((($edit['tax_type']) == ($row2['id'].',0'))?'value="'.$row2['id'].','.$row2['tax'].',0"Selected':'value="'.$row2['id'].','.$row2['tax'].',0"')?>><?php echo $row2['title']; ?></option>-->
														<?php } ?>
													
													              
												</select>
                                                                                                </td>
											</tr>
											<tr id="TextBoxContainerPayment" class="TextBoxContainerPayment">
												<td class="total" colspan="3">Advance given <br />
												<span class="text-danger">*Reward points:- <?php redeempoint() ?> points = <?php redeemprice() ?> <?php echo CURRENCY ?></span>
												</td>
												<td colspan="3" class="spr_row_payment">
													<table class="inner-table-space" id="pay_methods">
														<?php if(count($edit) > 0) { 
															$payments = query_by_id("SELECT * FROM multiple_payment_method WHERE invoice_id = 'app,$eid' and status = 1 and branch_id='".$branch_id."'",[],$conn);
															$count = 1;
															if($payments){
															foreach ($payments as $pay) {
															?>
															<tr>
																<td width="280"><input type="text" name="transid[]" class="key form-control transid" id="transctionid" value="<?=($pay['transaction_id']) ? $pay['transaction_id'] : '0'?>" placeholder="TXN ID"></td>
																<td><input type="number" name="adv[]" step="0.01" class="key form-control adv" id="adv" value="<?=($pay['amount_paid'])?$pay['amount_paid']:'0'?>" min="0"></td>
																<td><select name="method[]" data-validation="required" class="form-control act" onchange="paymode(this.value,$(this))">
																	<!--<option value="">--Select--</option>-->
																	<?php $sql_pay_mode="Select * FROM `payment_method` where status='1'";
																		$result_pay_mode =query_by_id($sql_pay_mode,[],$conn);
																		foreach($result_pay_mode as $row_pay_mode){
																		?>
																		<option <?=(($pay['payment_method'] === $row_pay_mode['id'])?'value="'.$row_pay_mode['id'].'" Selected':'')?> value="<?=$row_pay_mode['id']?>"><?=$row_pay_mode['name']?></option> 
																	<?php } ?>  
																</select></td>
																<?php if($count != 1){ ?>
																	<td id="minus_button"><span class="input-group-btn"><button onclick="$(this).parent().parent().parent().remove();sumup();" class="btn btn-danger btn-remove btn_remove" type="button"><span class="glyphicon-minus"></span></button></span></td>
																<?php } else { ?>
																<td id="plus_button_payment" width="5%">
																	<span class="input-group-btn">
																		<button style="" class="btn btn-add btn-plus btn-success btn-add add_spr_row_payment" type="button">
																			<span class="glyphicon-plus"></span>
																		</button>
																	</span>
																</td>
															<?php } $count++;?>
															</tr>
														<?php } } else {
															?>
																<tr>
																<td width="280"><input type="text" name="transid[]" class="key form-control transid" id="transctionid" value="" placeholder="TXN ID"></td>
																<td><input type="number" name="adv[]" step="0.01" class="key form-control adv" id="adv" value="<?=($edit['paid'])?$edit['paid']:'0'?>" min="0"></td>
																<td><select name="method[]" data-validation="required" class="form-control act" onchange="paymode(this.value,$(this))">
																	<!--<option value="">--Select--</option>-->
																	<?php $sql_pay_mode="Select * FROM `payment_method` where status='1'";
																		$result_pay_mode =query_by_id($sql_pay_mode,[],$conn);
																		foreach($result_pay_mode as $row_pay_mode){
																		?>
																		<option <?=(($edit['pay_method'] === $row_pay_mode['id'])?'value="'.$row_pay_mode['id'].'" Selected':'')?> value="<?=$row_pay_mode['id']?>"><?=$row_pay_mode['name']?></option> 
																	<?php } ?>  
																</select></td>
																<td id="plus_button_payment" width="5%">
																	<span class="input-group-btn">
																		<button style="" class="btn btn-add btn-plus btn-success btn-add add_spr_row_payment" type="button">
																			<span class="glyphicon-plus"></span>
																		</button>
																	</span>
																</td>
															</tr>
															<?php
															}
														} else { ?>
															<tr>
																<td width="280"><input type="text" name="transid[]" class="key form-control transid" id="transctionid" value="" placeholder="TXN ID"></td>
																<td><input type="number" name="adv[]" step="0.01" class="key form-control adv" id="adv" value="<?=($edit['paid'])?$edit['paid']:'0'?>" min="0"></td>
																<td><select name="method[]" data-validation="required" class="form-control act" onchange="paymode(this.value,$(this))">
																	<!--<option value="">--Select--</option>-->
																	<?php $sql_pay_mode="Select * FROM `payment_method` where status='1'";
																		$result_pay_mode =query_by_id($sql_pay_mode,[],$conn);
																		foreach($result_pay_mode as $row_pay_mode){
																		?>
																		<option <?=(($edit['pay_method'] === $row_pay_mode['id'])?'value="'.$row_pay_mode['id'].'" Selected':'')?> value="<?=$row_pay_mode['id']?>"><?=$row_pay_mode['name']?></option> 
																	<?php } ?>  
																</select></td>
																<td id="plus_button_payment" width="5%">
																	<span class="input-group-btn">
																		<button style="" class="btn btn-add btn-plus btn-success btn-add add_spr_row_payment" type="button">
																			<span class="glyphicon-plus"></span>
																		</button>
																	</span>
																</td>
															</tr>
														<?php } ?> 
													</table>
												<!--<input type="text" class="form-control" style="margin-top : 5px;" name="detail" id="detail" placeholder="Enter Card 4 digits or Cheque No.">
												</td>-->
											</tr>
											<tr>
												<td class="total" colspan="5">Pending dues</td>
												<td id="pend">0</td>
											</tr>
											<tr>
												<td class="total" colspan="5">Appointment Status</td>
												<?php if($edit['status'] == "Cancelled"){ ?>
													<td class="text-danger">Cancelled</td>
												<?php } else { ?>
													<td><select name="status" data-validation="required" class="form-control">
													<option <?=(($edit['status'] == "Pending") && ($edit['bill_created_status'] == 0)?'value="Pending" selected':'value="Pending"')?>>Pending</option>
													<option <?=(($edit['status'] === "Billed") || ($edit['bill_created_status'] == 1)?'value="Billed" selected':'value="Billed"')?>>Billed</option>
													<?php if($edit) { ?><option <?=(($edit['status'] === "Cancelled")?'value="Cancelled" selected':'value="Cancelled"')?>>Cancelled</option> <?php } ?>		 
												</select></td>
											<?php } ?>
											</tr>
											
											<tr>
												<td colspan="8"><textarea name="notes" class="form-control no-resize" rows="5" placeholder="Write Notes About Appointment here..." id="textArea"><?= $edit['notes']; ?></textarea></td>
											
											</tr>
										</tbody>
									</table>
								</div>
							</div>
							<div class="clearfix"></div>
							<div class="col-sm-12">
							    <input type="hidden" id="mem_service" value="" />
    						    <input type="hidden" id="mem_product" value="" />
    						    <input type="hidden" id="mem_package" value="" />
    						    <input type="hidden" id="has_membership" value="0" />
    						    <input type="hidden" id="mem_condition" value="0" />
    						    <input type="hidden" id="mem_reward_point" value="0" />
    						    <input type="hidden" id="mem_bill_amount" value="0" />
    						    <input type="hidden" name="membership_appilied" id="membership_appilied" value="0" />
    						    <input type="hidden" name="membership_id" id="membership_id" value="0" />
    						    <input type="hidden" name="membership_reward_boost" id="membership_reward_boost" value="1" />
								<?php if(isset($_GET['id']) && $_GET['id']>0)
								if(isset($_GET['type']) && $_GET['type'] == 1){
								    if($edit['bill_created_status'] == 0 && $edit['status'] != 'Cancelled'){ ?>
								    <a href="appointment.php?cancel_appointment=1&id=<?= $eid ?>"><button type="button" name="cancel_appointment" class="btn mr-left-5 btn-danger pull-right">Cancel</button></a>
								    <input type="hidden" name="web_appointment" value="1" />
								    <button type="submit" name="edit-submit" class="btn btn-success pull-right"><i class="fa fa-calendar-check-o" aria-hidden="true"></i>Approve</button>
								<?php } } else
								{ 
								$offerstring = implode(',', $predata); ?>
								<input type="hidden" name="package_service" value="<?= $offerstring ?>">
								<?php if($edit['bill_created_status'] == 0 && $edit['status'] != 'Cancelled'){ ?>	
								<button type="submit" name="edit-submit" class="btn btn-info pull-right" ><i class="fa fa-calendar-plus-o" aria-hidden="true"></i>Update Appointment</button>
							<?php } ?>
								<?php }else{?>
								<button type="button" name="" class="btn mr-left-5 btn-danger pull-right" onClick="location.reload();">Reset</button>
								<button type="submit" name="submit" class="btn btn-success pull-right"><i class="fa fa-calendar-check-o" aria-hidden="true"></i>Create appointments</button>
							<?php } ?>	
							</div>
							</div>
						</div>
					</div>
				
			</div>
			<div class="col-lg-3 col-xs-12 grey-box">
				<div class="row">
					<div class="col-lg-12">
						<div class="panel">
						<div class="panel-heading">
							<h4><i class="fa fa-repeat mr-left-0 text-warning fa-spin" aria-hidden="true"></i>Client 360&#176; view</h4>
						</div>
						<div class="client-view">
							<div class="client-view-content">
								<div id="customer_type" >
									
								</div>
								
								<table width="100%" class="table table-striped">
									<tr>
										<td>Branch:</td>
										<td id="branch_name">----<br></td>
									</tr>
									<tr>
										<td>Last visit on:</td>
										<td id="last_visit">----<br></td>
									</tr>
									<tr>
										<td>Total visits:</td>
										<td id="total_visit">0</td>
									</tr>
									<tr>
										<td>Total spendings:</td>
										<td id="total_spending">0</td>
									</tr>
									<tr>
										<td>Membership:</td>
										<td id="membership">----</td>
									</tr>
									<tr>
										<td>Active packages:</td>
										<td id="active_package">----<br></td>
									</tr>
									
									<tr>
										<td>Last feedback:</td>
										<td id="last_feedback"><a href="#"><u>----</u></a></td>
									</tr>
									<tr>
										<td>My wallet	:</td>
										<td id="wallet">0</td>
										<input type="hidden" id="wallet_money" value="0">
									</tr>
									<tr>
										<td>Reward points:</td>
										<td id="earned_points">0</td>
										<input type="hidden" id="reward_point" value="0">
									</tr>
									
									
									<tr>
										<td>Gender:</td>
										<td id="gender">
											<select class="form-control" name="gender" id="gender">
												<option value="">--Select--</option>
												<option id="gn-1" value="1" selected>Male</option>
												<option id="gn-2" value="2">Female</option>
											</select>
										</td>
									</tr>
									<tr>
										<td>Date of birth	:</td>
										<td id="dob"><input type="text" class="form-control dob_annv_date" name="dob" id="clientdob" value="" readonly></td>
									</tr>
									<tr>
										<td>Anniversary	:</td>
										<td><input type="text" class="form-control dob_annv_date" name="aniv" id="anniversary" value="" readonly></td>
									</tr>
									
									<tr>
										<td>Source of client:</td>
										<td>       
											<select class="form-control" name="leadsource" id="leadsource">
												<option value="">--Select--</option>
												<option value="Client refrence">Client refrence</option>
												<option value="Cold Calling">Cold Calling</option>
												<option value="Facebook">Facebook</option>
												<option value="Twitter">Twitter</option>
												<option value="Instagram">Instagram</option>
												<option value="Other Social Media">Other Social Media</option>
												<option value="Website">Website</option>
												<option value="Walk-In">Walk-In</option>
												<option value="Flex">Flex</option>
												<option value="Flyer">Flyer</option>
												<option value="Newspaper">Newspaper</option>
												<option value="SMS">SMS</option>
												<option value="Street Hoardings">Street Hoardings</option>
												<option value="Event">Event</option>
												<option value="TV/Radio">TV/Radio</option>		
											</select>
										</td>
									</tr>												
								</table>
							</div>
						</div>
					</div>
					</div>
				</div>
			</div>
		
		</div>
		<!-- Row ends -->
		</form>
		
		<div class="clearfix"></div>
		
	</div>
	
</div>

</div>
<!-- Container fluid ends -->


<!-- Modal -->
<div class="modal fade disableOutsideClick" id="appointment" role="dialog">
	<div class="modal-dialog modal-lg">
		
		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Appointment Details</h4>
			</div>
			<div class="modal-body">
				<table class="table table-bordered">
					<tr>
						<thead>
							<th>Service provider</th>
							<th>Client name</th>
							<th>Service name</th>
							<th>Start time</th>
							<th>End time</th>
							<th>Duration</th>
						</thead>
					</tr>
					<tbody id="appoint">
					</tbody>
				</table>		
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times" aria-hidden="true"></i> Close</button>
			</div>
		</div>
	</div>
</div>
<!-- Modal End -->


<!-- Modal -->
<div class="modal fade disableOutsideClick" id="spschedule" role="dialog">
	<div class="modal-dialog modal-lg">
		
		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Service provider's schedule on <span id="spdate"></span></h4>
			</div>
			<div class="modal-body">
				<table class="table table-bordered">
					<tr>
						<thead>
							<th>Service provider</th>
							<th>Client name</th>
							<th>Service name</th>
							<th>Start time</th>
							<th>End time</th>
							<th>Duration</th>
						</thead>
					</tr>
					<tbody id="todaySchedule">
					</tbody>
				</table>		
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times" aria-hidden="true"></i> Close</button>
			</div>
		</div>
	</div>
</div>
<!-- Modal End -->


<?php include "footer.php"; ?>

<script>

// special discount division start
document.getElementById('total_disc').addEventListener('keyup', function(event) { 
    const key = event.key; 
    if(parseInt($("#has_membership").val()) == 0){
        if (key === "Backspace" || key === "Delete") {
            if(parseInt($("#total_disc").val())==0 || $("#total_disc").val()==''){
                $("#total_disc").val(0);
                $(".singleservicediscount").val(0);

                $(".TextBoxContainer").each(function(index, value) {
                var row = $(this);
                var qnt = row.find(".qt").val();
                var actual = row.find(".prr").val();
                var totalprice = parseInt(qnt) * parseInt(actual);
                row.find(".pr").val(totalprice);   
                price_calculate(row);     
                });
            }  
        }
    } 
}); 
$("#total_disc").keypress(function() {
    //debugger;
    if(parseInt($("#has_membership").val()) == 0){
        $(".singleservicediscount").val(0);
        var count = document.getElementsByClassName("singleservicediscount").length;
        $(".TextBoxContainer").each(function(index, value) {
            var row = $(this);
            var qnt = row.find(".qt").val();
            var actual = row.find(".prr").val();
            var totalprice = parseInt(qnt) * parseInt(actual);
            row.find(".pr").val(totalprice);
            
        });
    }
});

$("#total_disc").change(function() {
    //debugger;
    if(parseInt($("#has_membership").val()) == 0){
    var dis_type = $(".total_disc_row_type").val();
    dis_type = parseInt(dis_type);
    if (dis_type == 1) {
        $(".TextBoxContainer").each(function(index, value) {
            var row = $(this);
            row.find("#disc_row_type").val('1');
        });
        $(".singleservicediscount").val(0);
        var value = $(this).val();
        var count = document.getElementsByClassName("singleservicediscount").length;
        var total_per = parseInt(value) / parseInt(count);
        total_per = parseFloat(total_per.toFixed(2));
        var orignalprice = 0;
        var prev;
        
        for (var i = 0; i < count; i++) {
            prev = document.getElementsByClassName("servicepriceafterdiscount")[i].value;
            orignalprice = orignalprice + parseFloat(prev);
        }
        orignalprice = Math.round(orignalprice);
        var sum = 0;
        for (var i = 0; i < count; i++) {
            var price = document.getElementsByClassName("servicepriceafterdiscount")[i].value;
            discountper = (parseFloat(price) / orignalprice) * 100;
            discount = (discountper / 100) * parseFloat(value);
            document.getElementsByClassName("singleservicediscount")[i].value = parseFloat(discount.toFixed(2));
            var exactprice = parseInt(price) - discount;
            document.getElementsByClassName("servicepriceafterdiscount")[i].value = parseFloat(exactprice
                .toFixed(2));
            price_calculate($("#TextBoxContainer"));
            sum += exactprice;
        }
        $("#sum").html("<?= CURRENCY ?> " + sum.toFixed(2));

            $("#sum2").val(sum);
         
        $("#total_disc").val(0);
    } else {
        $(".TextBoxContainer").each(function(index, value) {
            var row = $(this);
            row.find("#disc_row_type").val('0');
        });
        $(".singleservicediscount").val(0);
        var value = $(this).val();
        var count = document.getElementsByClassName("singleservicediscount").length;

        var orignalprice = 0;
        var prev;
        for (var i = 0; i < count; i++) {
            prev = document.getElementsByClassName("servicepriceafterdiscount")[i].value;
            orignalprice = orignalprice + parseFloat(prev);
        }
        orignalprice = Math.round(orignalprice);
        var total_dis = (parseInt(value) / 100) * orignalprice;
        var total_per = parseInt(total_dis) / parseInt(count);
        total_per = parseFloat(total_per.toFixed(2));
        var sum=0;
        for (var i = 0; i < count; i++) {
            var price = document.getElementsByClassName("servicepriceafterdiscount")[i].value;
            discountper = (parseFloat(price) / orignalprice) * 100;
            discount = (discountper / 100) * parseFloat(total_dis);
            var exactprice = parseInt(price) - discount;
            document.getElementsByClassName("servicepriceafterdiscount")[i].value = parseFloat(exactprice
                .toFixed(2));
                
            if(document.getElementsByClassName("pa_ser")[i].value==''){
            document.getElementsByClassName("singleservicediscount")[i].value = value;}
            price_calculate($("#TextBoxContainer"));
                //sumup();
            sum += exactprice;
        }
        
        $("#sum").html("<?= CURRENCY ?> " + sum.toFixed(2));

            $("#sum2").val(sum);
        $("#total_disc").val(0);
    }
    }
});
// special discount  end

	/*******Server_side_datatable*********/
		$(document).ready(function(){
			var bill_status = <?= $edit['bill_created_status']!='1'?'0':$edit['bill_created_status'] ?>;
			if(bill_status == 1){
				$('input').prop('disabled',true);
				$('#btnAdd').hide();
			}
		});
		/*******End********/
	$(document).on("click", '#date', function() {
		// $('.staff option[value=""]').prop('selected',true);	
		// $('.ser').val("");
		// $('.start_time').val("");
		// $('.end_time').val("");
		// $('.ser_stime').val("");
		// $('.ser_etime').val("");
		// $('.prr').val("");
		// $('.pr ').val("");
		// $('.serr').val("");
		// $('.disc_row').val("");
		
	});
	
	
	$(document).on("change", '.staff', function() {
		
		<?php if(isset($_GET['id']) && $_GET['id'] > 0){  ?>
			var app_eid = <?=$_GET['id']?>
			<?php }else { ?>
			var app_eid  = 0;
		<?php } ?>

		
		staff = $(this).val();
		
		findDuplicate($(this)); //call diplicate element function
		
		var durr  		= $(this).parent().parent().find('.durr').val();
		var starttime   = $(this).parents('tr').find('.ser_stime').val();
		var endtime     = $(this).parents('tr').find('.ser_etime').val();
		var select_staff= $(this).parents('tr').find('.staff option[value=""]');	
		
		date = $('#date').val();
		time = $('#time').val();
		var durr_plus = 0;
		var prev_rows = $(this).parent().parent().prevAll('tr');
        $(this).parent().parent().prevAll('tr').each(function(){
            durr_plus += parseInt($(this).find('.durr').val());
		});
		if(starttime !=''){
			$.ajax({
				url: "ajax/appointment_stafftime.php?id="+staff+"&date="+date+"&time="+time+"&starttime="+starttime+"&endtime="+endtime+"&app_eid="+app_eid,
				type: "POST",
				success:function(data){
		
					var durr_count = 0;
					var ds = JSON.parse(data.trim());
					starttime = ds['start'];
					endtime = ds['end'];
					var ds = JSON.parse(data.trim());
					if(ds['success']=='0'){
						toastr.success(ds['data']['spcat']+' Available.');
						}else if(ds['success']=='1'){
						toastr.error(ds['data']['spcat']+' Unavailable.');
						select_staff.prop("selected",true);
						showmodal(date,staff);
						}else if(ds['success']=='2'){
						toastr.error(ds['data']['spcat']+' Unavailable.');
						select_staff.prop("selected",true);
					}
				},
				error:function (){}
			});
			}else{
			select_staff.prop('selected',true);
		}
	});
	
	function showmodal(d,s){
		$.ajax({
			url: "ajax/timeslot.php?date="+d+"&staff="+s,
			type: "POST",
			success:function(data){
				$("#appoint").html(data);
				$("#appointment").modal("show");
			},
			error:function (){}
		});
	}
	

	function todaySchedule(date){
		$.ajax({
			url: "ajax/timeslot.php",
			type: "POST",
			data: {date : date, 'action':'todaySchedule'},
			success:function(data){
				date = date.split('-');
				date = date[2]+'-'+date[1]+'-'+date[0];
				$('#spdate').text(date);
				$("#todaySchedule").html(data);
				$("#spschedule").modal("show");
			},
			error:function (){}
		});
	}

	// function to find duplicate service_provider
	function findDuplicate(e){
		duplicate_arr=[];
		var row=e.parents(".TextBoxContainer").find('.spr_row').children('table');
		var row1=$(".TextBoxContainer").parents('table.add_row').find('.spr_row').children('table.add_row');
		var val=row.find('.staff');
		val.each(function(){
			duplicate_arr.push($(this).val());
			
		});
		
		for(var i=0;i<duplicate_arr.length;i++){
			for(var j=i+1;j<duplicate_arr.length;j++){
				if(duplicate_arr[i]==duplicate_arr[j]){
					e.parents('table.add_row').find('td#minus_button').remove()
					e.parents('table.add_row').find('td#select_row').remove();
				}  
			}
		}
	}
	
	$(window).on('load', function(){
		<?php if(isset($_GET['id']) || isset($_GET['enqid'])){ ?>
			var clientID=$('#clientid').val();
			clientView(clientID);
			client_check_membership_availability(clientID);
		<?php } ?>
		
		var contact=$('#cont').val();
		$.ajax({
			url:"autocomplete/client.php",
			type:"POST",
			data:{cont:contact},
			success:function(data){
				if(data){
					var da=JSON.parse(data.trim());
					if(da[0]['id'] > 0){
						$('#clientid').val(da[0]['id']);
						$("#client-status").html("");
					}
				}
			}
		});
		
		<?php if(isset($_GET['enqid'])){ ?>
			$('.TextBoxContainer').each(function(){
				var e =$(this);
				$.ajax({
					url:"ajax/bill.php?term="+$('.ser').val()+"&ser_stime="+$('#date').val()+' '+$('#time').val()+"&page_info=app",
					type: "GET",
					success:function(data){
						var ui = JSON.parse(data.trim());
						e.find('.serr').val(ui[0]['id']);
						e.find('.prr').val(ui[0]['price']);
						e.find('.qt').val('1');
						e.find('.disc_row ').val('0');
						e.find('.duration').val(ui[0]['duration']);
						e.find('.ser_stime').val(ui[0]['ser_stime']);
						e.find('.ser_etime').val(ui[0]['ser_etime']);
						e.find('.start_time').val((ui[0]['ser_stime']).substring(11));
						e.find('.end_time').val((ui[0]['ser_etime']).substring(11));
						price_calculate(row);
						sumup();
					},
				});
				
			});
		<?php } ?>
		
		
		var e=$(this);
		var row=$(".TextBoxContainer");
		var row_len=row.length;
		var j=1;
		var k=0;
		var l=1;
		row.each(function(){			
			var table_row=row.eq(k).find('.spr_row').children('table');
			table_row.each(function(){
				table_row.eq(j).removeAttr('id');
				table_row.eq(j).addClass('add_row');
				table_row.eq(j).find('td#plus_button').remove();
				table_row.eq(j).find('tr').append('<td><span class="input-group-btn"><button onclick="$(this).parent().parent().parent().parent().parent().remove();"   class="btn btn-danger btn-remove btn_remove" type="button"><span class="glyphicon-minus" "></span></button></span></td>');
				$(".TextBoxContainer").eq(l).find('.sno').html('<span class="remm icon-trash2 " style="color:red;" onclick="change_timing($(this));sumup();increment_ids();membershipDiscounts();"></span>');
				j++;
			});
			j=1;
			k++;
			l++;
		});
	    increment_ids();
		sumup();	
		check();
		formValidaiorns();
	});
	
	<!-- Add service_Provider row -->
	$(document).on("click",".add_spr_row", function(){
		var td_clone=$("#TextBoxContainer").find('.spr_row').children('table#add_row').clone().addClass('add_row');
		td_clone.removeAttr('id');
		td_clone.find('td#plus_button').remove();
		td_clone.find('tr').append('<td id="minus_button"><span class="input-group-btn"><button onclick="$(this).parent().parent().parent().parent().parent().remove();" class="btn btn-danger btn-remove btn_remove" type="button"><span class="glyphicon-minus" "></span></button></span></td>');
		var l_row=$(this).parents(".TextBoxContainer").find('.spr_row');
		l_row.children('table:last').after(td_clone);
		
		// l_row.next().children('table.add_row').find('.staff option[value=""]').prop('selected',true); 	
		td_clone.find('.staff option[value=""]').attr('selected','selected'); 
		
		var staff_name=$(this).parents(".TextBoxContainer").find('.staff').attr('name');
		$(this).parents(".TextBoxContainer").find('.staff').attr('name',staff_name); 
	});


	// multiple payment method

	$(document).on("click",".add_spr_row_payment", function(){
		var empty = 0;

		if($('#total').val() == 0){
			empty += 1;
		} else {
			$('.adv').each(function(){
				if($(this).val() == 0 || $(this).val() == ''){
					empty += 1;
					$(this).addClass('invalid');
				} else {
					$(this).removeClass('invalid');
				}
			});

			$('.act').each(function(){
				if($(this).val() == 0 || $(this).val() == ''){
					empty += 1;
					$(this).addClass('invalid');
				} else {
					$(this).removeClass('invalid');
				}
			});
		}

		if(empty <= 0){
			var amount_div = $(this).parent().parent().parent();
			var td_clone=$("#TextBoxContainerPayment").find('.spr_row_payment').children('table#pay_methods').clone().addClass('pay_methods');
			td_clone.removeAttr('id');
			td_clone.find('td#plus_button_payment').remove();
			td_clone.find('.adv').val(0);
			td_clone.find('tr').append('<td id="minus_button"><span class="input-group-btn"><button onclick="$(this).parent().parent().parent().parent().parent().remove();sumup();" class="btn btn-danger btn-remove btn_remove" type="button"><span class="glyphicon-minus" "></span></button></span></td>');
			var l_row=$(this).parents(".TextBoxContainerPayment").find('.spr_row_payment');
			l_row.children('table:last').after(td_clone);	
			l_row.children('table.pay_methods').first().find('.act option[value="1"]').attr('selected','selected');  
			var staff_name=$(this).parents(".TextBoxContainerPayment").find('.act').attr('name');
			$(this).parents(".TextBoxContainerPayment").find('.act').attr('name',staff_name);
			price_change();
		}
	});
	
    $(document).ready(function(){
        autocomplete_serr();
        autocomplete_serr_cat();
        price_change();
		formValidaiorns();
        change_event();
        $(".client").autocomplete({
			source: "autocomplete/client.php",
			minLength: 1,
            select: function(event, ui) {
          
                event.preventDefault();
                $('#client').val(ui.item.name);
                $('#clientid').val(ui.item.id); 
                $('#client_branch_id').val(ui.item.client_branch_id); 
                $('#cont').val(ui.item.cont); 
                $('#gender').val(ui.item.gender);
				$('#anniversary').val(ui.item.anniversary);
				$("#client-status").html("");
				$('#clientdob').val(ui.item.dob);
				$('#gst').val(ui.item.gst);
				clientView(ui.item.id);
				
				/*******Client_REWARD_POINTS********/
					$.ajax({
						url : "ajax/client_reward_points.php?client_id="+ui.item.id+"&referral_code="+ui.item.referral_code,
						type: "POST",
						success:function(data){
							
							var obj = $.parseJSON(data);
							if(parseInt(obj['reward_points']) > 0){
								$('#earned_points').html(obj['reward_points']); 
							}
						}	
					});
					
					/*******END******/
					
				    // check client pending payments
				        
				    $.ajax({
				        url : "ajax/get_pending_payments.php",
				        type : "post",
				        data : {action : 'check_pending_payments', client_id : ui.item.id},
				        success : function(res){
				            if(res != 0){
				                $('#ppaymentModal .modal-content').html(res);
				                var size = $('#ppaymentModal table tbody tr').length;
            	                if(size <= 0){
            	                    $('#ppaymentModal').modal('hide');
            	                } else {
				                    $('#ppaymentModal').modal('show');
            	                }
				            }
				        }
				    });
				    var client_id = $('#clientid').val();
				    client_check_membership_availability(client_id);
					}				
				});	
				 
				
				$("#btnAdd").bind("click", function() {
					var empty_fields = [];
					$('.ser').each(function(){
						if($(this).val() == ''){
							empty_fields.push('empty_field');
						}
					});
					$('.start_time').each(function(){
						if($(this).val() == ''){
							empty_fields.push('empty_field');
						}
					});
					$('.end_time').each(function(){
						if($(this).val() == ''){
							empty_fields.push('empty_field');
						}
					});
					$('.price').each(function(){
						if($(this).val() == ''){
							empty_fields.push('empty_field');
						}
					});
					if(empty_fields && empty_fields.length == 0){
						var clonetr = $("#TextBoxContainer").clone().addClass('TextBoxContainer');
						clonetr.removeAttr('id');
						clonetr.find("table.add_row").remove();
						clonetr.find('.sno').html('<span class="remm icon-trash2 " style="color:red;" onclick="change_timing($(this));sumup();increment_ids();membershipDiscounts();"></span>');
						//clonetr.find('input[text]').val('');
						clonetr.find('input').val('');
						$("#addBefore").before(clonetr);
						clonetr.find('.staff option[value=""]').prop('selected',true);
						autocomplete_serr_cat();
						// autocomplete_serr();
						price_change();
						change_event();
						increment_ids();
						formValidaiorns();
						$(".time").datetimepicker({
					        format: "HH:ii P",
					        showMeridian: true,
					        autoclose: true,
					        pickDate: false,
					        startView: 1,
				    		maxView: 1
					    });
					    $(".datetimepicker").find('thead th').remove();
				  		$(".datetimepicker").find('thead').append($('<th class="switch text-warning">').html('Pick Time'));
				  		$(".datetimepicker").find('tbody').addClass('alltimes');
				  		$('.switch').css('width','190px');
						// funcClass();
						$('.ser').on('click',function(){
							$(this).keydown();
							autocomplete_serr_top();
						});

						$('.ser').on('keyup',function(){
							if($(this).val().length > 0){
								autocomplete_serr();
							} else {
								autocomplete_serr_top();
							}
						});
					}
				});
				$(".ser_cat").on('keyup keydown keypress change',function(){
					if($(this).val()==''){
						$(this).parent().find('.ser_cat_id').val('');
					}
				});
			});
				function clientView(id){
						jQuery.ajax({
							url: "ajax/client_view.php",
							type: "POST",
							data:{client_id:id},
							beforeSend: function () {
								$('.client-view-content').fadeOut();
								$('.client-view').append('<div class="divloader"><div class="divloader_ajax_small"></div></div>');
							},
							success:function(data){
								if(data !=''){
						
									var ds = JSON.parse(data);
									// $('#last_visit').html('----');
									if(ds['lastvisit'] != ''){
										$('#last_visit').html(ds['lastvisit']);
									} else {
										$('#last_visit').html('');
									}

									if(ds['branch_name'] != ''){
										$('#branch_name').html(ds['branch_name']);
									} else {
										$('#branch_name').html('');
									}

									$('#total_visit').html(ds['total_visit']);
									$('#total_spending').html(ds['total_spending']+ ' <?= CURRENCY ?>');
									$('#membership').html(ds['membership']);
									if(ds['packages'] != ''){
										$('#active_package').html(ds['packages']+'<br><a href="javascript:void(0)" onClick="viewPackageModal('+id+')"><i class="icon-eye3"></i> View details</a>');
									} else {
										$('#active_package').html('----');
									}
									$('#earned_points').html(ds['reward_points']);
									$('#reward_point').val(ds['reward_points']);
									$('#last_feedback').html(ds['last_feedback']);
									$('#wallet').html(ds['wallet']+" <?=CURRENCY?>");
									$('#wallet_money').val(ds['wallet']);
									$('#gender option').attr('selected', false);
									if(ds['gender'] !=''){
									    $('#gender #gn-'+ds['gender']).attr('selected', true);
									} else {
									    $('#gender #gn-1').attr('selected', true);
									}
									
									if(ds['dob'] != '0000-00-00'){
										$('#clientdob').val(ds['dob']);
									} else {
										$('#clientdob').val('');
									}
									if(ds['Anniversary'] != '0000-00-00'){
										$('#anniversary').val(ds['Anniversary']);
									} else {
										$('#anniversary').val('');
									}
									$('#leadsource option[value="'+ds['leadsource']+'"]').prop('selected',true);
									$('.divloader').remove();
									
									if(ds['customer_type'] == 'active'){
										$('#customer_type').html("<div style='background:#00a400; margin:10px 0; padding:10px; border-radius:3px;'><h4 style='margin-bottom:0; color:#fff;'>Customer type: Active</h4><small class='text-white'>Active Customers - Customers who visit your outlet at regular intervals.  </small></div>");
										}else if(ds['customer_type'] == 'inactive'){
										$('#customer_type').html("<div style='background:#fa383e; margin:10px 0; padding:10px; border-radius:3px;'><h4 style='margin-bottom:0; color:#fff;'>Customer type: Defected customer</h4><small class='text-white'>Defected Customers - Customers who haven't visited your outlet and become inactive. </small></div>");
										} else if(ds['customer_type'] == 'newcustomer'){
											$('#customer_type').html("<div style='background:#622bfb; margin:10px 0; padding:10px; border-radius:3px;'><h4 style='margin-bottom:0; color:#fff;'>Customer type: New Customer</h4><small class='text-white'>Customer who haven't visited your outlet. </small></div>");
										} else{
										$('#customer_type').html("<div  style='background:#fff200; margin:10px 0; padding:10px; border-radius:3px;'><h4 style='margin-bottom:0;'>Customer type: Churn prediction</h4><small >Churn Prediction - Customers who haven't visited your outlet and who are likely to leave. </small> </div>");
									}
									$('.client-view-content').fadeIn();
									
								} 
							},
							error:function (){
								$('.client-view-content').css('display','none');
								$('.client-view').append('<div class="divloader"><div class="divloader_ajax_small"></div></div>');
							},
							
						});
					}
			// <!----increment_id's function -->
			function increment_ids(){
				var row_len=$('.TextBoxContainer');
				var i=0;
				row_len.each(function(){
					var a=$(this).find('.staff').attr('name','staffid'+i+'[]');
					i++; 
				});
			}
			<!--End code-->
			
			$(document).on("click input", '.start_time:eq(0)', function() {
				var s_time=$('.start_time:eq(0)').val();
				if(s_time !=""){
					// checkappTime('time',$('#time').val(),'apptime');
					servicestarttime(this.value, $(this));
				}
			});
			
			function addZero(i) {
				if (i < 10) {
					i = "0" + i;
				}
				return i;
			}
			
			
			//$(document).on("click input keydown ", '#time', function() {

				function appointmenttime(){
				
				var start_arr=[];	
				var end_arr  =[];
				var e=$('.TextBoxContainer');
				// e.find('.staff option[value=""]').prop('selected',true); 
				
				var date=$('#date').val();
				var duration = parseInt(e.find('.duration').val()); //duration
				
				var ser_stime=$('#time').val();
	
				e.find('.ser_stime').val(date+" "+time12to24(ser_stime));
				e.find('.start_time').val(ser_stime);
				
				var ser_stime1=e.find('.ser_stime').val();
				var da = new Date(ser_stime1.replace(/-/g, '/'));
				
				var new_endtime= new Date(da.getTime() + (duration * 60 * 1000));
				
				var final_atime=new_endtime.getFullYear() + '-' +('0' + (new_endtime.getMonth()+1)).slice(-2)+ '-' +  addZero(new_endtime.getDate()) + ' '+addZero(new_endtime.getHours())+ ':'+('00' + (new_endtime.getMinutes())).slice(-2)+ ':'+new_endtime.getSeconds()+'0';
				
				e.find('.ser_etime').val(final_atime);
				e.find('.end_time').val(onTimeChange(final_atime.substr(11)));
				
				var start = e.nextAll('tr').find('.ser_etime').length;
				var index_value=$('.TextBoxContainer').index();
				
				for(var i=0;i<start;i++){ 
					
					var prev_start_time = $(".ser_stime:eq("+(i+index_value)+")").val();
					
					var prev_stime = new Date(prev_start_time.replace(/-/g, '/'));
					var prev_duration = $('.duration:eq('+(i+index_value)+')').val(); //prev_duration
					
					var prev_starttime= new Date(prev_stime.getTime() + (prev_duration * 60 * 1000));
					
					var final_atime=prev_starttime.getFullYear() + '-' +('0' + (prev_starttime.getMonth()+1)).slice(-2)+ '-' +  addZero(prev_starttime.getDate()) + ' '+addZero(prev_starttime.getHours())+ ':'+('00' + (prev_starttime.getMinutes())).slice(-2)+ ':'+prev_starttime.getSeconds()+'0';
					
					var next_start_time = $(".ser_etime:eq("+(i+index_value)+")").val(); 
					
					var next_stime = new Date(next_start_time.replace(/-/g, '/'));
					var next_duration = $('.duration:eq('+(i+index_value+1)+')').val(); //next_duration
					var next_starttime= new Date(next_stime.getTime() + (next_duration * 60 * 1000));
					
					var final_etime=next_starttime.getFullYear() + '-' +('0' + (next_starttime.getMonth()+1)).slice(-2)+ '-' +  addZero(next_starttime.getDate()) + ' '+addZero(next_starttime.getHours())+ ':'+('00' + (next_starttime.getMinutes())).slice(-2)+ ':'+next_starttime.getSeconds()+'0';
					
					start_arr.push(final_atime);
					end_arr.push(final_etime);  
					
					$(".end_time:eq("+(i+index_value+1)+")").val(onTimeChange((end_arr[i]).substring(11))); 
					$(".ser_etime:eq("+(i+index_value+1)+")").val(end_arr[i]); 
					$(".start_time:eq("+(i+index_value+1)+")").val(onTimeChange((start_arr[i]).substring(11))); 
					$(".ser_stime:eq("+(i+index_value+1)+")").val(start_arr[i]); 
					// $(".start_time:eq("+(i+index_value+1)+")").attr('min',(start_arr[i]).substring(11));
					// $(".start_time:eq("+(i+index_value+1)+")").attr('max',(end_arr[i]).substring(11));
				}
			}
			// );
			
			
			// $(document).on("click input keydown", '.start_time', function() {
				function servicestarttime(time, current){
		
				var e = current;
				var date = $('#date').val();
				if(date != ''){
					$('#date').removeClass('invalid');
					$.ajax({  
			        url:"ajax/system_details.php",
			        method:"POST",
			        dataType: "json",
			        data: {date : date, 'action':'checkapptime'},
			        success:function(response){
			        	
			            	if(response.status == '1'){
			            		sstime = response.starttime;
			            		eetime = response.endtime;
			               		var stime = Date.parse('20 Aug 2000 '+sstime);
								var etime = Date.parse('20 Aug 2000 '+eetime);
								var cpmtime = Date.parse('20 Aug 2000 '+time+':00');
								if(stime == '' || etime == ''){
									
								} else {
									if(cpmtime < stime || cpmtime > etime){
										e.parents('tr').find('.start_time').val('');
										e.parents('tr').find('.ser_etime').val('');
										e.parents('tr').find('.end_time').val('');
									} else {
										
									var start_arr=[];	
									var end_arr  =[];
									
									e.parents('tr').find('.staff option[value=""]').prop('selected',true); 
									
									var date=$('#date').val();
									var duration = parseInt(e.parents('tr').find('.duration').val()); //duration
									var ser_stime=e.parents('tr').find('.start_time').val();
									
									e.parents('tr').find('.ser_stime').val(date+" "+time12to24(ser_stime));
									
									var ser_stime1=e.parents('tr').find('.ser_stime').val();
									var da = new Date(ser_stime1.replace(/-/g, '/'));
									
									var new_endtime= new Date(da.getTime() + (duration * 60 * 1000));
									if(!isNaN(duration)){
										var final_atime=new_endtime.getFullYear() + '-' +('0' + (new_endtime.getMonth()+1)).slice(-2)+ '-' +  addZero(new_endtime.getDate()) + ' '+(new_endtime.getHours()<10?'0':'')+new_endtime.getHours()+ ':'+(new_endtime.getMinutes()<10?'0':'')+new_endtime.getMinutes()+ ':'+new_endtime.getSeconds()+'0';
									} else {
										var final_atime = '2000-08-20'+' '+time12to24(ser_stime);
									}
									
									
									e.parents('tr').find('.ser_etime').val(final_atime);
									e.parents('tr').find('.end_time').val(onTimeChange(final_atime.substr(11)));
									
									var start = e.parents('tr').nextAll('tr').find('.ser_etime').length;
									
									var index_value=e.parents('.TextBoxContainer').index();
									
									
									for(var i=0;i<start;i++){ 
										$(".staff option[value='']:eq("+(i+index_value+1)+")").prop('selected',true);
										
										var prev_start_time = $(".ser_stime:eq("+(i+index_value)+")").val();
										var prev_stime = new Date(prev_start_time.replace(/-/g, '/'));
										var prev_duration = $('.duration:eq('+(i+index_value)+')').val(); //prev_duration
										
										
										var prev_starttime= new Date(prev_stime.getTime() + (prev_duration * 60 * 1000));
										var final_atime=prev_starttime.getFullYear() + '-' +('0' + (prev_starttime.getMonth()+1)).slice(-2)+ '-' +  addZero(prev_starttime.getDate()) + ' '+addZero(prev_starttime.getHours())+ ':'+('00' + (prev_starttime.getMinutes())).slice(-2)+ ':'+prev_starttime.getSeconds()+'0';
										
										var next_start_time = $(".ser_stime:eq("+(i+index_value+1)+")").val(); 
										var next_stime = new Date(next_start_time.replace(/-/g, '/'));
										var next_duration = $('.duration:eq('+(i+index_value+1)+')').val(); //next_duration
										var next_starttime= new Date(next_stime.getTime() + (next_duration * 60 * 1000));
										var final_etime=next_starttime.getFullYear() + '-' +('0' + (next_starttime.getMonth()+1)).slice(-2)+ '-' +  addZero(next_starttime.getDate()) + ' '+time12to24(addZero(next_starttime.getHours())+ ':'+('00' + (next_starttime.getMinutes())).slice(-2)+ ':'+next_starttime.getSeconds());
										
										start_arr.push(final_atime);
										end_arr.push(final_etime);  
										
										$(".end_time:eq("+(i+index_value+1)+")").val((end_arr[i]).substring(11)); 
										$(".ser_etime:eq("+(i+index_value+1)+")").val(end_arr[i]); 
										$(".start_time:eq("+(i+index_value+1)+")").val((start_arr[i]).substring(11)); 
										$(".ser_stime:eq("+(i+index_value+1)+")").val(start_arr[i]); 
										// $(".start_time:eq("+(i+index_value+1)+")").attr('min',(start_arr[i]).substring(11));
										// $(".start_time:eq("+(i+index_value+1)+")").attr('max',(end_arr[i]).substring(11));
									}

									}
								}
			               	} else {
			               		current.val('');
			               	}
			            }
			       	});
				} else {
					current.val('');
					$('#date').addClass('invalid');
				}
				
			}
			// ); 
			
			
			/* time changing function*/
			function change_timing(e){
				var start_arr=[];	
				var end_arr  =[];
				update_temp_qty(e);
				var ser_stime = e.parents('tr').find('.ser_stime').val(); //start time
				var ser_etime = e.parents('tr').find('.ser_etime').val(); //end time
				var d2 = new Date(ser_stime.replace(/-/g, '/'));
				var d1 = new Date(ser_etime.replace(/-/g, '/'));
				var diff_minutes =  (d1- d2);
				
				var start = e.parents('tr').prevAll('tr').find('.ser_etime').length;
				var count = e.parents('tr').nextAll('tr').find('.ser_etime').length;
				var p_service = e.parents('tr').find('.pa_ser').val();
				if(p_service != ''){
					e.parents('tr').hide();
				} else {
					e.parents('tr').remove();
				}
				 
				
				var add_in_start  = $(".ser_etime:eq("+(start-1)+")").val();
				start_arr.push(add_in_start);
				
				
				for(var i=0;i<count;i++){	
					var add_in_end = $(".ser_etime:eq("+(i+start)+")").val();
					var add_in_start1  = $(".ser_stime:eq("+(i+start)+")").val();
					var d2 = new Date(add_in_end.replace(/-/g, '/'));
					var new_endtime= (new Date(d2 - diff_minutes));
					
					var final_etime=new_endtime.getFullYear() + '-' +('0' + (new_endtime.getMonth()+1)).slice(-2)+ '-' +  addZero(new_endtime.getDate()) + ' '+addZero(new_endtime.getHours())+ ':'+('00' + (new_endtime.getMinutes())).slice(-2)+ ':'+new_endtime.getSeconds()+'0';
					
					end_arr.push(final_etime);
					start_arr.push(final_etime); 
					$(".end_time:eq("+(i+start)+")").val((end_arr[i]).substring(11)); 
					$(".ser_etime:eq("+(i+start)+")").val(end_arr[i]); 
					$(".start_time:eq("+(i+start)+")").val((start_arr[i]).substring(11)); 
					$(".ser_stime:eq("+(i+start)+")").val(start_arr[i]);  
					
				}
				
			}
			
			function autocomplete_serr_cat(){
				$(".ser_cat").autocomplete({
					source: "ajax/bill_cat.php",
					minLength: 1,
					select:function (event, ui) {  
						var row = $(this).parent().parent();
						var row1= $(this).parents('tr');
						$(this).val(ui.item.value);
						row.find('.ser_cat_id').val(ui.item.id);
						row1.find('.ser').val("");
						row1.find('.start_time').val("");
						row1.find('.end_time').val("");
						row1.find('.ser_stime').val("");
						row1.find('.ser_etime').val("");
						row1.find('.prr').val("");
						row1.find('.pr ').val("");
						row1.find('.serr').val("");
						row1.find('.disc_row').val("");
						row1.find('.staff option[value=""]').prop('selected',true);
						
					}
				});	
			}


			$('.ser').on('click',function(){
				$(this).keydown();
				autocomplete_serr_top();
			});

			$('.ser').on('keyup',function(){
				if($(this).val().length > 0){
					autocomplete_serr();
				} else {
					autocomplete_serr_top();
				}
			});

			function autocomplete_serr_top(){
				$(".ser").autocomplete({ 
					source: function(request, response) {
						var ser_stime = '';
						if($(this.element).parent().parent().parent().parent().parent().parent().attr('id')=='TextBoxContainer'){
							ser_stime = $('#date').val()+' '+$('#time').val();
							}else{
							ser_stime = $(this.element).parent().parent().parent().parent().parent().parent().prev('tr').find('.ser_etime').val();
						}
						$.getJSON("ajax/bill.php", { term: 'topservices',ser_cat_id: $(this.element).parent().parent().find('.ser_cat_id').val(),ser_stime:ser_stime,page_info:'topservices' }, response);
					},
					minLength: 0,
					select:function (event, ui) { 
						var appo_time = $('#time').val();
						var appo_date = $('#date').val();
						if(appo_time == ''){
							$('#time').addClass('invalid');
						} else if(appo_date == ''){
							$('#date').addClass('invalid');
							$('#time').removeClass('invalid');
						} else{
						
							$('#date').removeClass('invalid');
							var etime = Date.parse('20 Aug 2000 '+$('#close_time').val());
							var setime = Date.parse('20 Aug 2000 '+ui.item.ser_etime.split(' ')[1]+':00');
							var et_status = '<?= extratimeStatus(); ?>'; // Extra time status
							if(setime > etime && et_status == '0'){	
								var row = $(this).parent().parent();
								// var row = $(this).parent().parent().parent().parent().parent().parent();
								row.find('input[type="text"].ser').val('');
								row.find('.serr').val('');
								// row.find('.pa_ser').val('');
								row.find('.prr').val('');
								row.find('.qt').val('0');
								row.find('.disc_row ').val('0');
								row.find('.duration').val('');
								row.find('.ser_stime').val('');
								row.find('.ser_etime').val('');
								row.find('.start_time').val(('').substring(11));
								row.find('.end_time').val(('').substring(11));
					
								toastr.error('Appointment can\'t book for this service. salon will close at '+onTimeChange($('#close_time').val()));
							} else {
								var row = $(this).parent().parent();
								// var row = $(this).parent().parent().parent().parent().parent().parent();
								if(row.find('.pa_ser').val() != ''){
									$.ajax({
										url : 'ajax/bill.php',
										method : 'post',
										data : {id : row.find('.pa_ser').val(), action : 'removeTemp' },
										success : function(response){
								
										}
									});
									row.find('.pa_ser').val('');
								}
								row.find('.serr').val(ui.item.id);
								row.find('.prr').val(ui.item.price);
								row.find('.qt').val('1');
								row.find('.disc_row ').val('0');
								row.find('.duration').val(ui.item.duration);
								row.find('.ser_stime').val(ui.item.ser_stime);
								row.find('.ser_etime').val(ui.item.ser_etime);
								row.find('.start_time').val(onTimeChange((ui.item.ser_stime).substring(11)));
								row.find('.end_time').val(onTimeChange((ui.item.ser_etime).substring(11)));
								var appointment_date = $('#date').val();
								$.ajax({
								    url : "ajax/service.php",
								    method : "POST",
								    data : { action : 'provider_list_by_service', service_id : ui.item.id, appointment_date : appointment_date},
								    dataType : "JSON",
								    success : function(response){
								        var options = '<option value="">Service provider</option>';
								        if(response.length != 0){
								            $.each(response, function(i, item) {
                                                options += '<option value="'+item.id+'">'+item.name+'</option>';
                                            });
								        }
								        row.find('.staff').html(options);
								    }
								});
								appointmenttime();
								var clientId = $('#clientid').val();
								if(clientId != ''){
						
									$.ajax({
										url : 'ajax/bill.php',
										method : 'post',
										data : { sid : ui.item.id, cid : clientId, 'action':'packages' },
										success : function(response){
								
											if(response != '0'){
									
												$('#avpackageModal').remove();
												row.append(response);
												if($('#avpackageModal table tbody').children().length != 0) {
													$('#avpackageModal').modal('show');
												}
											} else {
												$.ajax({
													url : 'ajax/bill.php',
													method : 'post',
													data : {id : row.find('.pa_ser').val(), action : 'removeTemp' },
													success : function(response){
											
													}
												});
												row.find('.pa_ser').val('');

											}
										}
									});
								}
					
								price_calculate(row);
								sumup();
								// paymode($('.act').val());
							}
						}
					}
				});	
			}

			function autocomplete_serr(){
				$(".ser").autocomplete({ 
					source: function(request, response) {
						var ser_stime = '';
						if($(this.element).parent().parent().parent().parent().parent().parent().attr('id')=='TextBoxContainer'){
							ser_stime = $('#date').val()+' '+$('#time').val();
							}else{
							ser_stime = $(this.element).parent().parent().parent().parent().parent().parent().prev('tr').find('.ser_etime').val();
						}
						$.getJSON("ajax/bill.php", { term: request.term,ser_cat_id: $(this.element).parent().parent().find('.ser_cat_id').val(),ser_stime:ser_stime,page_info:'app' }, response);
					},
					minLength: 1,
					select:function (event, ui) { 
						var appo_time = $('#time').val();
						var appo_date = $('#date').val();
						if(appo_time == ''){
							$('#time').addClass('invalid');
						} else if(appo_date == ''){
							$('#date').addClass('invalid');
							$('#time').removeClass('invalid');
						} else{
						
							$('#date').removeClass('invalid');
							var etime = Date.parse('20 Aug 2000 '+$('#close_time').val());
							var setime = Date.parse('20 Aug 2000 '+ui.item.ser_etime.split(' ')[1]+':00');
							var et_status = '<?= extratimeStatus(); ?>'; // Extra time status
							if(setime > etime && et_status == '0'){	
								// var row = $(this).parent().parent().parent().parent().parent().parent();
								var row = $(this).parent().parent();
								row.find('input[type="text"].ser').val('');
								row.find('.serr').val('');
								// row.find('.pa_ser').val('');
								row.find('.prr').val('');
								row.find('.qt').val('0');
								row.find('.disc_row ').val('0');
								row.find('.duration').val('');
								row.find('.ser_stime').val('');
								row.find('.ser_etime').val('');
								row.find('.start_time').val(('').substring(11));
								row.find('.end_time').val(('').substring(11));
					
								toastr.error('Appointment can\'t book for this service. salon will close at '+onTimeChange($('#close_time').val()));
							} else {
								// var row = $(this).parent().parent().parent().parent().parent().parent();
								var row = $(this).parent().parent();
								if(row.find('.pa_ser').val() != ''){
									$.ajax({
										url : 'ajax/bill.php',
										method : 'post',
										data : {id : row.find('.pa_ser').val(), action : 'removeTemp' },
										success : function(response){
								
										}
									});
									row.find('.pa_ser').val('');
								}
								row.find('.serr').val(ui.item.id);
								row.find('.prr').val(ui.item.price);
								row.find('.qt').val('1');
								row.find('.disc_row ').val('0');
								row.find('.duration').val(ui.item.duration);
								row.find('.ser_stime').val(ui.item.ser_stime);
								row.find('.ser_etime').val(ui.item.ser_etime);
								row.find('.start_time').val(onTimeChange((ui.item.ser_stime).substring(11)));
								row.find('.end_time').val(onTimeChange((ui.item.ser_etime).substring(11)));
								var appointment_date = $('#date').val();
								$.ajax({
								    url : "ajax/service.php",
								    method : "POST",
								    data : { action : 'provider_list_by_service', service_id : ui.item.id, appointment_date : appointment_date},
								    dataType : "JSON",
								    success : function(response){
								        var options = '<option value="">Service provider</option>';
								        if(response.length != 0){
								            $.each(response, function(i, item) {
                                                options += '<option value="'+item.id+'">'+item.name+'</option>';
                                            });
								        }
								        row.find('.staff').html(options);
								    }
								});
								appointmenttime();
								var clientId = $('#clientid').val();
								if(clientId != ''){
						
									$.ajax({
										url : 'ajax/bill.php',
										method : 'post',
										data : { sid : ui.item.id, cid : clientId, 'action':'packages' },
										success : function(response){
								
											if(response != '0'){
									
												$('#avpackageModal').remove();
												row.append(response);
												if($('#avpackageModal table tbody').children().length != 0) {
													$('#avpackageModal').modal('show');
												}
											} else {
												$.ajax({
													url : 'ajax/bill.php',
													method : 'post',
													data : {id : row.find('.pa_ser').val(), action : 'removeTemp' },
													success : function(response){
											
													}
												});
												row.find('.pa_ser').val('');

											}
										}
									});
								}
					
								price_calculate(row);
								sumup();
								// paymode($('.act').val());
							}
						}
					}
				});	
			}
			
			function update_temp_qty(e){
	
				var parent = e.parents('tr');
				var val = parent.find('.pa_ser').val();
				if(val != ''){
					$.ajax({
						url : 'ajax/bill.php',
						method : 'post',
						data : {id : val, action : 'removeTemp' },
						success : function(response){
				
						}
					});
				}
			}
			
			function price_calculate(row){
				var pr = row.find('.prr').val();
				var qt = row.find('.qt').val();
				var sum = pr * 1;
				var disc_row_val = row.find('.disc_row').val();
				disc_row_val = disc_row_val>0?disc_row_val:0;
				var disc_row_type = row.find('.disc_row_type').val();
				if(disc_row_type=='0'){
				    if(parseFloat(disc_row_val) <= 100){
					    var disc_row = parseFloat((sum * disc_row_val)/100);
				    } else {
				        row.find('.disc_row').val('0');
				        var disc_row = 0;
				        toastr.warning("In % max discount should be 100%");
				    }
				} else {
					var disc_row = parseFloat(disc_row_val);
				}
	
				sum = sum - disc_row;
				row.find('.price').val(sum);
				var pric = 0;
				var sums = 0;
				var sump = 0;
				var sumt = 0;
				var sum  = 0;
				var ids  = $(".serr");
				
				
				var inputs = $(".price");
				for(var i = 0; i < inputs.length; i++){
					var service = $(ids[i]).val().split(',');
					if(service[0]=="sr"){
						sums = sums + parseFloat($(inputs[i]).val());
					}
					else if(service[0]=="pr"){
						sump = sump + parseFloat($(inputs[i]).val());
					}
					sum = parseFloat(sum) + parseFloat($(inputs[i]).val());
					$("#sum").html("Rs. "+sum.toFixed(2));
				}	
			}

			function sumup(){
				var pric = 0;
				var sums = 0;
				var sump = 0;
				var sumt = 0;
				var sum = 0;
				var ids = $(".serr");
				membershipDiscounts();
				var inputs = $(".price");
				for(var i = 0; i < inputs.length; i++){
					var service = $(ids[i]).val().split(',');
					if(service[0]=="sr"){
						sums = sums + parseFloat($(inputs[i]).val());
					}
					else if(service[0]=="pr"){
						sump = sump + parseFloat($(inputs[i]).val());
					}
					sum = parseFloat(sum) + parseFloat($(inputs[i]).val());
					sum = sum || 0;
					$("#sum").html("<?= CURRENCY ?> "+sum.toFixed(2));
					$("#sum2").val(sum);
					$("#total").val(sum);
				}
				
				
				var paid = $('#paid').val();
				$("#sums").val(sums);
				$("#sump").val(sump);
				$("#sum").val(sum);
				var dis = parseFloat($("#disc").val());
				dis = dis || 0;
				var cval = parseFloat($("#cval").val());
				cval = cval || 0;
				var cmax = parseFloat($("#cmax").val());
				cmax = cmax || 0;
				var paid = parseFloat($("#paid").val());
				paid = paid || 0;
				var csum = sum * cval / 100;
				if(csum > cmax){
					csum = cmax;
				}
				csum = csum || 0;
				sum = sum - csum;
				
				var total = sum - dis ;
				
				
				if($('.total_disc_row_type').val() == 1){
					var tot_disc =$("#sum2").val();
					var tot_dis=$('#total_disc').val();
					total = tot_disc - tot_dis;
				} else { 
					var tot_disc = $("#sum2").val();
					var tot_dis = $('#total_disc').val();
					if(tot_dis <= 100){
					    total = total - (tot_disc * tot_dis / 100);
					} else {
					    $('#total_disc').val('0');
					    total = total;
					    toastr.warning("In % max discount should be 100%");
					}
				}
				
				var tax = $('#tax').val();
				var taxx = tax.split(',');
				
				if(taxx[2]!=0){
					var tsum = total * parseFloat(taxx[1]) / 100;
					tsum = tsum || 0;
					total = total + tsum;
					}else{
					tsum = 0;
				}
					
				$("#total").val(total.toFixed(2));
				$("#paid").val(total.toFixed(2));
	
				var adv = 0;
				$('.adv').each(function(){
					adv += parseFloat($(this).val()||0);
				});
				// var pend = 0;
	
				if(adv <= total){
					var pend = total - parseFloat(adv);
				}else{
					$(".adv").val();
					$("#pend").val(0);
					var pend = parseFloat(adv);
				}
				
				$("#pend").html(pend.toFixed(2));
				var paid = $("#paid").val();
				var fin = total - paid - parseFloat(adv);
				
				$("#due").html(fin.toFixed(2));
				$("#chng").val(fin.toFixed(2));

				if($('#total').val() == 0){
					$('.adv').prop('readonly',true);
				} else {
					if(pend == 0){
						$('select[name=status] option:nth-child(2)').prop('selected',true);
						$('.add_spr_row_payment').css('pointer-events','none');
					} else {
				// 		$('select[name=status] option:nth-child(1)').prop('selected',true);
						$('.add_spr_row_payment').css('pointer-events','initial');
					}
					$('.adv').prop('readonly',false);
				}
				advKeyup();
			}
			function price_change(){
				$(".pr, #tax, .adv").on("keyup change", function () {
					sumup();
				});
			}

			function change_event(){
				
				$(".qt").on("keyup keypress change click", function () {
					var row = $(this).parent().parent(); 
					price_calculate(row);
					sumup();
					
				});
				$(".disc_row,.disc_row_type").on("blur keypress keyup keydown change", function () {
					var row = $(this).parent().parent().parent().parent().parent().parent(); 
					if(row.find('.prr').val() > 0){
						if ($(".total_disc_row_type").val() == 1) {
						    if(parseFloat($(this).val()) <= row.find('.prr').val()){
						        var row = $(this).parent().parent(); 
						        price_calculate(row);
						        sumup();
						    } else {
						        $('#toast-container .toast-warning').remove();
						        toastr.warning("Discount should be less then price");
						        $(this).val(0);
						        price_calculate(row);
						        sumup();
						    }
						} else {
							
						}
					} else {
					   if($(this).val() > 0){
					        $('#toast-container .toast-warning').remove();
					        toastr.warning("Price should be greater then 0 to apply discount");
					   }
					   $(this).val(0);
					}
				});
				
				$(".disc_row,.disc_row_type").on("keyup keypress change keydown", function () {
				    var row = $(this).parent().parent().parent().parent().parent().parent(); 
				    price_calculate(row);
					sumup();
				});
				$("#total_disc,.total_disc_row_type").on("keyup keypress change keydown", function () {
				    sumup();
				});
				$("#total_disc,.total_disc_row_type").on("blur", function () {
				    var total_amount = parseFloat($('#sum2').val());
				    if($(this).val() > 0){
    				    if(total_amount > 0){
    				    	if ($(".total_disc_row_type").val() == 1) {
	    				        if(parseFloat($(this).val()) > total_amount){
	    				            $(this).val(0);
	    				            $('#toast-container .toast-warning').remove();
	    				            toastr.warning("Discount should be less then "+total_amount);
	    				            sumup();
	    				        } else {
	    				            sumup();
	    				        }
	    				    } else {
    				            sumup();
    				        }
    				    } else {
    				        $(this).val(0);
    				        sumup();
    				        $('#toast-container .toast-warning').remove();
    				        toastr.warning("Total amount should be greater then 0 to apply discount");
    				    }
				    }
				// 	sumup(); 
				});
			}
			
			
			function check() {
				$client_id = $('#clientid').val();				
				jQuery.ajax({
					url: "checkccont.php?p="+$("#cont").val(),
					type: "POST",
					success:function(data){
						
						if(data == '1'){
							if($client_id == ''){
								$("#client-status").html("Contact number already exists");
								$('#cont').val("");
								$('#dob').val("");
								$('#aniv').val("");
							}
						}else{
							$("#client-status").html("");
							$("#clientid").val("");
							$('#dob').val("");
							$('#aniv').val("");
						}
					},
					error:function (){}
				});
			}

	// function to use package service

	function usePackageService(service_id, row_id, e){
		var ser = e.parents('.TextBoxContainer').find('.pa_ser').val();
		if(ser != ''){
			$('#avpackageModal').modal('hide');
		} else {
			e.parents('.TextBoxContainer').find('.pa_ser').val(service_id+'-'+row_id);
			e.parents('.TextBoxContainer').find('.pr').val('0');
			e.parents('.TextBoxContainer').find('.prr').val('0');
			e.parents('.TextBoxContainer').find('.disc_row').val('0');
			$.ajax({
				url : 'ajax/bill.php',
				method : 'post',
				data : {row_id : row_id, action : 'tmpqty'},
				success : function(response){
					if(response == 1){
						sumup();
						$('#avpackageModal').modal('hide');
						setTimeout(function(){
							$('#avpackageModal').remove();
						}, 1000);
					} else {

					}
				}
			});
		}
	}

	function removeModal(modalid){
		setTimeout(function(){
			$('#'+modalid).remove();
		},1000);
	}

	function paymode(mode_id, modeDiv){
		var options = 0;
		var totalVal = parseInt($('#total').val());
		if(totalVal == 0){
			modeDiv.val('1');
		} else {
			$('.act').each(function(){
				var selected_options = $(this).val();
				if(mode_id == selected_options){
					options += 1;
				}
			});
			if(options > 1){
				toastr.warning('Payment option is already selected.');
				modeDiv.parent().parent().parent().parent().remove();
				$('#TextBoxContainerPayment table:first-child .input-group-btn').html('<button style="" class="btn btn-add btn-plus btn-success btn-add add_spr_row_payment" type="button"><span class="glyphicon-plus"></span></button>');
				$('#TextBoxContainerPayment table:first-child').attr('id','pay_methods');
				$('#TextBoxContainerPayment table:first-child .input-group-btn').parent().attr('id','plus_button_payment');
				$('#TextBoxContainerPayment table:first-child').removeClass('pay_methods');
				sumup();
				return false;
			}
		}
		var modeDiv = modeDiv.parent().parent();
		var wallet_money = parseInt($('#wallet_money').val());
		var reward_point = parseInt($('#reward_point').val());
		if(mode_id == '7'){	
			if(totalVal != 0){
				if(wallet_money == '0' || wallet_money == ''){
					toastr.warning('Wallet is empty.');
					modeDiv.find('.act').val('1');
					modeDiv.find('.adv').val('0');
				} 
				else {
					
					var price_cal = parseInt(wallet_money);
					if(totalVal < wallet_money){
						modeDiv.find('.adv').val(parseFloat($('#pend').text()));
					} else {
						modeDiv.find('.adv').val(price_cal);
					} 
					sumup();
					if(reward_point == '' || reward_point == '0'){

					} else {
						$('#reward_point').val(reward_point);
						$('#earned_points').text(reward_point);
					}
				}
			}
		} else if(mode_id == '9'){
			if(totalVal != 0){
				if(reward_point == '' || reward_point == '0'){
					modeDiv.find('.adv').val('0');
					toastr.warning('Don\'t have any reward point.');
					modeDiv.find('.act').val('1');
					sumup();
				} else {
					
					var point;
					var point_price = <?= redeemprice() ?>;
					var redeem_point = <?= redeempoint() ?>;
					var pprice = parseFloat($('#pend').text());
					if(reward_point > <?= maxredeempoint() ?>){
						point = <?= maxredeempoint() ?>;
					} else {
						point = reward_point;
					}
					var price_cal = (parseInt(point)/parseInt(redeem_point))*parseInt(point_price);
					
					if(pprice > price_cal){
						modeDiv.find('.adv').val(price_cal); 
					} else {
						modeDiv.find('.adv').val(pprice);
					}
					sumup();
				}
			}
		} else {
			modeDiv.find('.adv').val('0');
			sumup();
		}
	}
	
	$(document).on("blur", '#date', function() {
	    if($('#clientid').val() != ''){
		    client_check_membership_availability($('#clientid').val());
		}
	});

	function advKeyup(){	
		$('.adv').on('keyup',function(){
			var adv = 0;
			var redeem_point = <?= redeempoint() ?>;
			var max_points = <?= maxredeempoint() ?>;
			var mainDiv = $(this).parent().parent();
			var currentDiv = $(this);
			var rewardPoint = $('#reward_point').val();
			var walletMoney = $('#wallet_money').val();
			
			$('.adv').each(function(){
				adv += parseFloat($(this).val()||0);
			});
			if(parseFloat(adv) > parseFloat($('#total').val()) ){
				toastr.warning('Advance amount exceeded total amount.');
				currentDiv.val(0);
				sumup();
			}
			if(mainDiv.find('.act').val() == '9'){
				
				if(rewardPoint == '0'){
					toastr.warning('Don\'t have any reward point.');
				} else if(rewardPoint > 0){
				    if(parseFloat((currentDiv.val()*redeem_point)) > parseFloat(max_points)){
				        toastr.warning('You can redeem max '+max_points+' points ( '+(max_points/redeem_point) +' <?= CURRENCY ?> ) at a time.');
						currentDiv.val(0);
				    }
					else if(parseFloat((currentDiv.val()*redeem_point)) > parseFloat(rewardPoint)){
						toastr.warning('You have only '+rewardPoint+' reward points');
						currentDiv.val(0);
					}
				}
			} else if(mainDiv.find('.act').val() == '7'){
				if(walletMoney == '0'){
					toastr.warning('Wallet is empty.');
				} else if(walletMoney > 0){
					
					if(parseFloat(currentDiv.val()) > parseFloat(walletMoney)){
						toastr.warning('You have only '+walletMoney+' <?= CURRENCY ?> in wallet.');
						currentDiv.val(0);
					}
				}
			}
		});
	}
	
	
	function client_check_membership_availability(client_id){
    		$.ajax({
    			url : "ajax/check_membership_availability.php",
    			type: "POST",
    			data: {client_id:client_id},
    			success:function(data){
        			if(data){
            			var dt = $.parseJSON(data);
            			if(dt[0]['total'] == '1'){
        				var str = dt[0]['result']['time_update'];
        			 	dt[0]['result']['time_update'] = str.replace(" ", "T");//for mac and ios
            			 if(new Date(dt[0]['result']['time_update']) < new Date($('#date').val()+'T'+time12to24(($('#time').val())))){
            			    dt = dt[0]['result'];
            			    $('#has_membership').val(1);
            			    $('#mem_condition').val(dt['mem_condition']);
            			    $('#mem_reward_point').val(dt['min_reward_points_earned']);
            			    $('#mem_bill_amount').val(dt['min_bill_amount']);
            			    $('#membership_id').val(dt['md_id']);
		                    $('#membership_reward_boost').val(dt['reward_points_boost']);
            			    
            			    var s_dis = dt['discount_on_service'];
            			    var p_dis = dt['discount_on_product'];
            			    var pack_dis = dt['discount_on_package'];
            			    
            			    if(dt['mem_condition'] == 1){
            			        var condition = '<strong>AND</strong>';
            			    } else if(dt['mem_condition'] == 2){
            			        var condition = '<strong>OR</strong>';
            			    }
            			    
            			    $('#mem_service').val(s_dis);
            			    $('#mem_product').val(p_dis);
            			    $('#mem_package').val(pack_dis);
            			    
            			    s_dis = s_dis.split(",");
            			    p_dis = p_dis.split(",");
            			    pack_dis = pack_dis.split(",");
            			    
            			    if(s_dis[1] == 'pr'){
            			        s_dis = s_dis[0]+'%';
            			    } else {
            			        s_dis = s_dis[0]+' <?= CURRENCY ?>';
            			    }
            			    
            			    if(p_dis[1] == 'pr'){
            			        p_dis = p_dis[0]+'%';
            			    } else {
            			        p_dis = p_dis[0]+' <?= CURRENCY ?>';
            			    }
            			    
            			    if(pack_dis[1] == 'pr'){
            			        pack_dis = pack_dis[0]+'%';
            			    } else {
            			        pack_dis = pack_dis[0]+' <?= CURRENCY ?>';
            			    }
            			    
            			    var html = '<br /><div class="alert alert-success light"><i class="icon-check_circle"></i><strong>Wow! </strong>';
            			                html += 'This client has a membership with ';
            			                html += 'Discount on <strong>Service '+s_dis+'</strong> , ';
            			                html += 'Discount on <strong>Products '+p_dis+'</strong> , ';
            			                html += 'Discount on <strong>Packages '+pack_dis+'</strong>';
            			                html += '<div class="text-danger"><i style="margin-left:0px;" class="fa fa-exclamation-circle" aria-hidden="true"></i> <strong>Note:</strong> ';
            			                html += 'Minimum <strong>Reward point</strong> should be <strong>'+dt['min_reward_points_earned']+'</strong> '+condition+' minimum <strong>Bill amount</strong> should be <strong><?= CURRENCY ?> '+dt['min_bill_amount']+'</strong> to apply membership discount.';
            			                html += '</div>';
            			    $('#member_ship_message').html(html);
            			 
            			 }else{
            			     $('#member_ship_message').html('');
            			     emptyMembershipData();
            			 }
        			    }else{
        			        $('#member_ship_message').html('');
            				emptyMembershipData();
        			    }
        			} else {
        			    $('#member_ship_message').html('');
        			    emptyMembershipData();
        			}
        			membershipDiscounts();
        			sumup();
    			}
    		});
    	}
    		
		function emptyMembershipData(){
		    $('#member_ship_message').html("");
			$('#mem_service').val('');
		    $('#mem_product').val('');
		    $('#mem_package').val('');
		    $('#mem_condition').val('0');
		    $('#has_membership').val(0);
		    $('#mem_reward_point').val(0);
		    $('#mem_bill_amount').val(0);
		    $('#membership_id').val(0);
		}
    		
    		
	function membershipDiscounts(){
	    // membership discount calculation code		
									
		// check membership status
		var membership = parseInt($('#has_membership').val());
        // subtotal
        var subtotal = parseFloat($('#sum2').val());
		if(membership > 0 && membership == 1){
            // membership discount uploading code
	        var mem_service = $('#mem_service').val();
	        var mem_product = $('#mem_product').val();
	        var mem_package = $('#mem_package').val();
	        var reward_point = parseInt($('#reward_point').val());
	        var condition = parseInt($('#mem_condition').val());
	        var min_rpoint = parseInt($('#mem_reward_point').val());
	        var min_amount = parseFloat($('#mem_bill_amount').val());
	        
	        // spliting service types
	        mem_service = mem_service.split(',');
            mem_product = mem_product.split(',');
            mem_package = mem_package.split(',');
	                        
	        if(condition > 0 && condition == 1){ // AND condition
	            if(reward_point >= min_rpoint && subtotal >= min_amount){
	                $('.serr').each(function(){
	                   var parent = $(this).parent().parent();
	                   var type = parent.find('.serr').val();
	                   var pack = parent.find('.pa_ser').val();
	                   if(pack == ''){
	                        type = type.split(',')[0];
		                    if(type == 'sr'){
		                       if(parseFloat(parent.find('.prr').val()) < parseFloat(mem_service[0])){
		                           parent.find('.disc_row').val(parent.find('.prr').val());
		                       } else {
		                           parent.find('.disc_row').val(mem_service[0]);
		                       }
	                           parent.find('.disc_row').prop('readonly',true);
	                           if(mem_service[1] == 'pr'){
	                               parent.find('.disc_row_type option[value = 0]').prop('selected',true);
	                           } else {
	                               parent.find('.disc_row_type option[value = 1]').prop('selected',true);
	                           }
	                           parent.find('.disc_row_type option:not(:selected)').attr('disabled', true);
	                        } else if(type == 'pa'){
	                           parent.find('.disc_row').val(mem_package[0]);
	                           parent.find('.disc_row').prop('readonly',true);
	                           if(mem_package[1] == 'pr'){
	                               parent.find('.disc_row_type option[value = 0]').prop('selected',true);
	                           } else {
	                               parent.find('.disc_row_type option[value = 1]').prop('selected',true);
	                           }
	                           parent.find('.disc_row_type option:not(:selected)').attr('disabled', true);
	                        } else if(type == 'pr'){
	                           parent.find('.disc_row').val(mem_product[0]);
	                           parent.find('.disc_row').prop('readonly',true);
	                           if(mem_product[1] == 'pr'){
	                               parent.find('.disc_row_type option[value = 0]').prop('selected',true);
	                           } else {
	                               parent.find('.disc_row_type option[value = 1]').prop('selected',true);
	                           }
	                           parent.find('.disc_row_type option:not(:selected)').attr('disabled',true);
	                        }
	                        price_calculate(parent);
	                        $('#membership_appilied').val(1);
						   if(parent.find('.serr').val().split(',')[0] == 'mem'){
							    if($('#has_membership').val() == 1){
							        parent.find('.start_time').val('');
							        parent.find('.end_time').val('');
							        parent.find('.pr').val('0');
							        parent.find('.prr').val('');
							        parent.find('.rpoint').val('');
							        setTimeout(function(){
							            parent.find('.ser').val('');
							        },100);
							        toastr.warning("One membership is already activated.");
							    }
							} 
	                   }
	                });
	            } else {
	                $('.serr').each(function(){
	                     var parent = $(this).parent().parent();
	                     parent.find('.disc_row').val(0);
	                     parent.find('.disc_row').prop('readonly',false);
	                     parent.find('.disc_row_type option').prop('disabled',false);
	                     price_calculate(parent);
	                     $('#membership_appilied').val(0);
	                });
	            }
	        } else if(condition > 0 && condition == 2){ // OR condition
	            if(reward_point >= min_rpoint || subtotal >= min_amount){
	                $('.serr').each(function(){
	                   var parent = $(this).parent().parent();
	                   var type = parent.find('.serr').val();
	                   var pack = parent.find('.pa_ser').val();
	                   if(pack == ''){
	                        type = type.split(',')[0];
		                    if(type == 'sr'){
	                           if(parseFloat(parent.find('.prr').val()) < parseFloat(mem_service[0])){
		                           parent.find('.disc_row').val(parent.find('.prr').val());
		                       } else {
		                           parent.find('.disc_row').val(mem_service[0]);
		                       }
	                           parent.find('.disc_row').prop('readonly',true);
	                           if(mem_service[1] == 'pr'){
	                               parent.find('.disc_row_type option[value = 0]').prop('selected',true);
	                           } else {
	                               parent.find('.disc_row_type option[value = 1]').prop('selected',true);
	                           }
	                           parent.find('.disc_row_type option:not(:selected)').attr('disabled', true);
	                        } else if(type == 'pa'){
	                           parent.find('.disc_row').val(mem_package[0]);
	                           parent.find('.disc_row').prop('readonly',true);
	                           if(mem_product[1] == 'pr'){
	                               parent.find('.disc_row_type option[value = 0]').prop('selected',true);
	                           } else {
	                               parent.find('.disc_row_type option[value = 1]').prop('selected',true);
	                           }
	                           parent.find('.disc_row_type option:not(:selected)').attr('disabled', true);
	                        } else if(type == 'pr'){
	                           parent.find('.disc_row').val(mem_product[0]);
	                           parent.find('.disc_row').prop('readonly',true);
	                           if(mem_package[1] == 'pr'){
	                               parent.find('.disc_row_type option[value = 0]').prop('selected',true);
	                           } else {
	                               parent.find('.disc_row_type option[value = 1]').prop('selected',true);
	                           }
	                           parent.find('.disc_row_type option:not(:selected)').attr('disabled',true);
	                        }
	                        price_calculate(parent);
	                        $('#membership_appilied').val(1);
	                        <?php if(!isset($_GET['beid'])){ ?>
	                        if(parent.find('.serr').val().split(',')[0] == 'mem'){
							    if($('#has_membership').val() == 1){
							        parent.find('.start_time').val('');
							        parent.find('.end_time').val('');
							        parent.find('.pr').val('0');
							        parent.find('.serr').val('');
							        parent.find('.remm').click();
							        parent.find('.prr').val('');
							        parent.find('.rpoint').val('');
							        setTimeout(function(){
							            parent.find('.ser').val('');
							        },100);
							        toastr.warning("One membership is already activated.");
							    }
							}
							<?php } ?>
	                   }
	                });
	            } else {
	                $('.serr').each(function(){
	                     var parent = $(this).parent().parent();
	                     var editid = parseInt('<?= isset($_GET['id'])?$_GET['id']:0 ?>');
	                     if(editid == 0){
	                         parent.find('.disc_row').val(0);
	                     }
	                     parent.find('.disc_row').prop('readonly',false);
	                     parent.find('.disc_row_type option').prop('disabled',false);
	                     price_calculate(parent);
	                     $('#membership_appilied').val(0);
	                });
	            }
	        }
        } else {
            $('.serr').each(function(){
                 var parent = $(this).parent().parent();
                 var editid = parseInt('<?= isset($_GET['id'])?$_GET['id']:0 ?>');
                 if(editid == 0){
	               // parent.find('.disc_row').val(0);
	             }
                 parent.find('.disc_row').prop('readonly',false);
                 parent.find('.disc_row_type option').prop('disabled',false);
                 price_calculate(parent);
                 $('#membership_appilied').val(0);
                //  parent.find('.disc_row_type option[value="1"]').prop('selected', true);
            });
        }
	}
		
</script>											