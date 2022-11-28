<?php 
	include "./includes/db_include.php";
	$branch_id = $_SESSION['branch_id'];
	$uid = $_SESSION['uid'];
	if(isset($_GET['beid']) && $_GET['beid']>0)
	{
		$beid = $_GET['beid'];
		$edit = query_by_id("SELECT DISTINCT ii.start_time, ii.end_time,cou.discount,i.notes,i.itime,cou.discount_type,cou.max_amount,cou.min_amount,cou.ccode as coupon_name,i.referral_code,i.gst,i.coupon as coupon_id,i.paid as bill_paid,i.advance,i.dis,i.doa,i.id,i.total,i.pay_method,i.subtotal,CONCAT(i.tax,',',i.taxtype) as tax_type,i.status,i.dis as total_discount,c.name,c.id as c_id,c.cont,c.gender,c.dob,c.aniversary, ii.product_stock_batch, i.is_rewardpoint_given from `invoice_".$branch_id."` i "
		." LEFT JOIN `client` c on c.id=i.client "
		." LEFT join `invoice_items_".$branch_id."` ii on ii.iid=i.id "
		." LEFT JOIN `beauticians` b on b.id=ii.staffid "
		." LEFT JOIN `coupons` cou on cou.id=i.coupon "
		." where i.active=0 and i.id='$beid' and i.branch_id='".$branch_id."' order by i.id desc",[],$conn)[0];
		} elseif(isset($_GET['bid']) && $_GET['bid']>0){
		$beid = $_GET['bid'];
		
		$roomsql = "SELECT room_id from `app_invoice_".$branch_id."` where id = '".$beid."'";
        $roomresult = query_by_id($roomsql,[],$conn)[0];
        
        	if($roomresult['room_id'] == ""){
		
		$edit = query_by_id("SELECT DISTINCT b.name as provider,aii.start_time, aii.end_time, ai.paid,ai.dis,ai.id,ai.role,ai.total,ai.pay_method,ai.subtotal,CONCAT(ai.tax,',',ai.taxtype) as tax_type,ai.status,ai.dis as total_discount,c.name,c.id as c_id,c.cont,c.gender,c.dob,c.aniversary, 1 as is_rewardpoint_given from `app_invoice_".$branch_id."` ai "
		." LEFT JOIN `client` c on c.id=ai.client "
		." LEFT join `app_invoice_items_".$branch_id."` aii on aii.iid=ai.id "
		." LEFT JOIN `beauticians` b on b.id=aii.staffid "
		." where ai.active=0  and ai.id='$beid' and ai.branch_id='".$branch_id."' order by ai.id desc",[],$conn)[0];
        	}else{
        	    
        	    $edit = query_by_id("SELECT  b.name as provider,va.app_start_time as start_time, va.app_end_time as end_time, va.room_id, va.app_date, va.allocated_by, va.allocated_for, vr.room_name, ai.paid,ai.dis,ai.id,ai.role,ai.total,ai.pay_method,ai.subtotal,CONCAT(ai.tax,',',ai.taxtype) as tax_type,ai.status,ai.dis as total_discount,c.name,c.id as c_id,c.cont,c.gender,c.dob,c.aniversary, 1 as is_rewardpoint_given from `app_invoice_".$branch_id."` ai "
		." LEFT JOIN `vip_appointment` va on va.inv_id=ai.id"
		." LEFT JOIN `client` c on c.id=va.allocated_for"
		." LEFT JOIN `vip_rooms` vr on vr.id=va.room_id"
		." LEFT JOIN `beauticians` b on b.id=va.allocated_by"
		." where ai.active=0  and ai.id='$beid' and ai.branch_id='".$branch_id."' order by ai.id desc",[],$conn)[0];
		
        	}
		}elseif(isset($_GET['ssbid']) && $_GET['ssbid']>0){
		$sseid = $_GET['ssbid'];
		$edit = query_by_id("SELECT DISTINCT b.name as provider,ss.paid,ss.dis,ss.doa,ss.id,ss.role,ss.total,ss.pay_method,ss.subtotal,CONCAT(ss.tax,',',ss.taxtype) as tax_type,ss.status,ss.dis as total_discount,c.name,c.id as c_id,c.cont,c.gender,c.dob,c.aniversary, 1 as is_rewardpoint_given from `service_slip` ss "
		." LEFT JOIN `client` c on c.id=ss.client "
		." LEFT join `service_slip_items` ssi on ssi.iid=ss.id"
		." LEFT JOIN `beauticians` b on b.id=ssi.staffid "
		." where ss.active=0  and ss.id='$sseid' and ss.branch_id='".$branch_id."' order by ss.id desc",[],$conn)[0];
		}elseif(isset($_GET['eqid'])  && $_GET['eqid'] > 0){
		$enquiry_id = $_GET['eqid']; 
		$edit = query_by_id("SELECT customer as name,cont FROM `enquiry` where id='$enquiry_id' and active='0' and branch_id='".$branch_id."' ",[],$conn)[0];
	} 
	
	if(isset($_POST['submit']) || isset($_POST['submit-bill']) || isset($_POST['submit-bill-print']) || isset($_POST['submit-bill-sms']) )
	{
		$client             = addslashes(trim($_POST['clientid']));
		$gst	            = addslashes(trim(($_POST['gst'])?$_POST['gst']:''));
		$name               = addslashes(trim($_POST['client']));
		$cont               = addslashes(trim($_POST['contact']));
		$gender             = addslashes(trim($_POST['gender']));
		$dob                = addslashes(trim($_POST['dob']));
		$aniv               = addslashes(trim($_POST['aniv']));
		$leadsource         = addslashes(trim($_POST['leadsource'])); //source of client
		$referral_code      = addslashes(trim(strtoupper(substr($name,0,4)).strtoupper(substr(md5(sha1($cont)),0,4))));
		if($client == ''){
		    $client=get_insert_id("INSERT INTO `client` set `name`=:name,`cont`=:cont,`referral_code`=:referral_code,`gst`=:gst,`gender`=:gender,`dob`=:dob,`aniversary`=:aniversary,`leadsource`=:leadsource,`active`=:active, `branch_id`='$branch_id'",[
			'name'  =>$name,
			'cont'  =>$cont,
			'referral_code'=>$referral_code,
			'gst'   =>$gst,
			'gender'=>$gender,
			'dob'   =>$dob,
			'aniversary'=>$aniv,
			'leadsource'=>$leadsource,
			'active' =>0
			],$conn);
			    query("INSERT INTO wallet SET iid='0', date = '".date('Y-m-d')."', client_id = '$client', wallet_amount='0', get_wallet_from='', status='1', branch_id='".$branch_id."'",[],$conn);
			}else{
			    query("UPDATE `client` set `gender`=:gender,`dob`=:dob,`aniversary`=:aniversary,`referral_code`=:referral_code,`leadsource`=:leadsource where id=:id and branch_id='".$branch_id."'",[
			'gender'=>$gender,
			'dob'   =>$dob,
			'aniversary'=>$aniv,
			'referral_code'=>$referral_code,
			'leadsource'=>$leadsource,
			'id'    =>$client,
			],$conn);
		}
		
		$doa = $_POST['doa'];
		$time = date('H:i',strtotime($_POST['time'])); // billing time
		
		if($_POST['total_disc_row_type'] == '0'){
			$dis 	 = 'pr,'.$_POST['dis'];
			}else if($_POST['total_disc_row_type'] == '1'){
			$dis 	= CURRENCY.','.$_POST['dis'];
		}

		$billdate = date('Y-m-d');
		
		$subtotal = $_POST['subtotal'];
		$coupon = $_POST['ccid'];
		
		$disper = $_POST['discount2'];
		
		$tax 	= $_POST['tax'];
		$taxx 	= explode(",",$tax);
		$tax 	= $taxx[0];
		$taxtype = $taxx[2];
		$chnge 	= $_POST['chnge'];
		$total 	= $_POST['total'];
		$adv	= $_POST['adv'];
		$paid   = 0;
		for($p=0;$p<count($_POST['paid']);$p++){
			$paid+=$_POST['paid'][$p];
		}
		
		$due = $total - $adv - $paid ;
		
		if($due > 0 && $due < 1){
		    $amount_to_add = $due;
		    $due = 0;
		} else {
		    $amount_to_add = 0;
		    $due    = ($due > 0) ? $due : 0;
		}

		
		$method = $_POST['method']; 
		$notes ="";
		$notes = addslashes(trim($_POST['notes']));
		$used_reward_points = $_POST['used_redeem_points'];
		$referral_code = $_POST['referral_code'];

		$bill_id = 0;
		$ss_bill_id=0;
		if(isset($_GET['bid']) && $_GET['bid']>0){
			$bill_id =$_GET['bid'];
			query("UPDATE `app_invoice_".$branch_id."` set `bill_created_status`='1' where `id`='$bill_id' and branch_id='".$branch_id."'",[],$conn);
			}else if(isset($_GET['ssbid']) && $_GET['ssbid']>0){
			$ss_bill_id =$_GET['ssbid'];
			query("UPDATE `service_slip` set `bill_created_status`='1' where `id`='$ss_bill_id' and branch_id='".$branch_id."'",[],$conn);
		}
		
		if(isset($_GET['ssappid']) && $_GET['ssappid']>0){
			$ssappid =$_GET['ssappid'];
			query("UPDATE `app_invoice_".$branch_id."` set `bill_created_status`='1' where `id`='$ssappid' and branch_id='".$branch_id."'",[],$conn);
		}
		if(isset($_GET['ssbid']) && $_GET['ssbid']>0){
			$beid = $_GET['ssbid'];
		}else{
			$beid = 0;
		}
		 
		$reward_boost = $_POST['membership_reward_boost'];    
		$membership_appilied = $_POST['membership_appilied'];
		$membership_id = $_POST['membership_id'];
		$client_branch_id = $_POST['client_branch_id'];

		$aid = get_insert_id("INSERT INTO `invoice_".$branch_id."`  set `appointment_id`=:appointment_id,`service_slip_id`=:service_slip_id,`gst`=:gst,`client`=:client,`referral_code`=:referral_code,`doa`=:doa,`itime`=:itime,`dis`=:dis,`disper`=:disper,`tax`=:tax,`taxtype`=:taxtype,`subtotal`=:subtotal,`total`=:total,`paid`=:paid,`advance`=:advance,`due`=:due,`notes`=:notes,`bpaid`=:bpaid,`bmethod`=:bmethod,`pay_method`=:pay_method,`type`=:type,`used_reward_points`=:used_reward_points,`status`=:status,`invoice`=:invoice,`coupon`=:coupon,`uid`=:uid,`billdate`=:billdate,`active`=:active,`membership_appilied`=:membership_appilied,`membership_id`=:membership_id, `branch_id`='".$branch_id."', `client_branch_id`='".$client_branch_id."'",[
		'appointment_id'   => $bill_id,
		'service_slip_id'  => $beid,
		'gst' 			   => $gst,
		'client'		   => $client,
		'referral_code'	   => $referral_code,
		'doa'              => $doa,
		'itime'            => $time,
		'dis'			   => $dis,
		'disper'           => $disper,
		'tax'              => $tax,
		'taxtype'          => $taxtype,
		'subtotal'		   => $subtotal,
		'total'            => $total,
		'paid'			   => $paid,
		'advance'          => $adv,
		'due'			   => $due,
		'notes'            => $notes,
		'bpaid'            => $paid,
		'bmethod'		   => $method,
		'pay_method'       => $method,
		'type'			   => 2,
		'used_reward_points'=> $used_reward_points,
		'status'            => 'Bill',
		'invoice'		   => 1, 
		'coupon'		   => $coupon,
		'uid'			   => $uid,
		'billdate'		   => $billdate,
		'active'		   => 0,
		'membership_appilied' => $membership_appilied,
		'membership_id'     => $membership_id
		],$conn);
		
		
		$paid_amount = 0;
		for($count = 0; $count < count($_POST['method']); $count++){
			$transaction_id = $_POST['transid'][$count];
			$paid = trim($_POST['paid'][$count]);
			$amount = trim($_POST['paid'][$count]);
			$method = trim($_POST['method'][$count]);
			if($paid == '0' || $paid == ''){
				continue;
			} else {
				if(count($_POST['method']) == 1){
					$paid_amount += $paid;
					$paid_amount = $paid_amount+$amount_to_add;
				} else {
					$paid_amount = $paid_amount+$paid;
					if($count == 0){
					    $paid_amount = $paid_amount+$amount_to_add;
					}
				}
				query("INSERT INTO `multiple_payment_method` (`invoice_id`,`payment_method`,`amount_paid`,`status`,`branch_id`,`transaction_id`) VALUES ('bill,$aid','$method','$amount',1,'$branch_id','$transaction_id')",[],$conn);
				if($method == 7){
					query("INSERT INTO `wallet_history` (`client_id`,`iid`,`transaction_type`,`wallet_amount`,`get_wallet_from`,`status`,`branch_id`) VALUES ('$client','bill,$aid',0,'$amount','Bill',1,'$branch_id')",[],$conn);
					query("UPDATE `wallet` set `wallet_amount`= (wallet_amount-'$amount') WHERE `client_id` = '$client' and branch_id='".$branch_id."'",[],$conn);
				}
				if($method == 9){
					$rpoint = redeempoint(1);
					$points = $amount*($rpoint);
					query("INSERT INTO `customer_reward_points` SET `invoice_id`='bill,$aid',`client_id`='cust,$client',`points_on`='0',`point_type`='2',`points`='$points',`notes`='Points redeem at the time of Billing.',`status`='1', `branch_id`='".$branch_id."'",[],$conn);
				}
			}
		}
		
		query("UPDATE `invoice_".$branch_id."` set `paid` = '$paid_amount', `due` = '$due' WHERE `id` = '$aid' and `branch_id`='".$branch_id."'",[],$conn);

		if($_POST['invoice_wallet_amount'] > 0){
			$wallet_amount 	= $_POST['invoice_wallet_amount'];
			query("INSERT INTO `wallet_history` (`client_id`,`iid`,`transaction_type`,`wallet_amount`,`get_wallet_from`,`status`,`branch_id`) VALUES ('$client','bill,$aid',1,'$wallet_amount','Bill (Advance amount)',1,'$branch_id')",[],$conn);
			query("UPDATE `wallet` set `wallet_amount`= (wallet_amount+'$wallet_amount') WHERE `client_id` = '$client' and branch_id='".$branch_id."'",[],$conn);
		}
		
		for($t=0;$t<count($_POST["services"]);$t++){
			$ser 			= $_POST["service"][$t];
			$ser_cat_id 	= $_POST["ser_cat_id"][$t];
			$pservice = $_POST['pa_ser'][$t];
			if($pservice != ''){
				$id = explode("-",$pservice);
				if(isset($id[2]) && $id[2] == 'appointment'){
				    
				} else {
				    $id = $id[1];
    				$sql_up = "update client_package_services_used set quantity_used = (quantity_used+1) where id='".$id."'";
    				query($sql_up,[],$conn); 
    				$sql_up_2 = "update client_package_services_used set tmp_qty = '0' where id='".$id."'";
    				query($sql_up_2,[],$conn); 
    				$package_id = query_by_id("SELECT c_pack_id FROM client_package_services_used WHERE id='".$id."'",[],$conn)[0]['c_pack_id'];
    				query("INSERT INTO package_service_history SET package_id='".$package_id."', service_id='".$id."', quantity='1', client_id='".$client."', used_in_branch='".$branch_id."', invoice_id='".$aid."', status='1'",[],$conn);
				}
			} else {
				$id = '0';
			}
			
			
			if($_POST['disc_row_type'][$t] == '0'){
				$disc_row = 'pr,'.$_POST["disc_row"][$t];
				}else if($_POST['disc_row_type'][$t] == '1'){
				$disc_row =  CURRENCY.','.$_POST["disc_row"][$t];
			}
			
			$serr = explode(",", $ser); 
			if($serr[0]=="sr"){
				$serr[0] = "Service";
				}else if($serr[0]=="pr"){
				$serr[0] = "Product";
				}else if($serr[0]=="pa"){
				
				$serr[0] = "Package";
				query("INSERT INTO `clientpackages`(`package`,`client`,`inv`,`active`,`branch_id`) VALUES ('$serr[1]','$client','$aid',0,'$branch_id')",[],$conn);
				
				$client_package_services_used=query("SELECT category,service,quantity,price FROM `packageservice` where pid='$serr[1]' and active='0'",[],$conn);
				
				foreach($client_package_services_used as $cp_service){
					query("INSERT INTO `client_package_services_used` SET `inv`='$aid',`client_id`='$client',`c_pack_id`='$serr[1]',`category_id`='".$cp_service['category']."',`c_service_id`='".$cp_service['service']."',`quantity`='".$cp_service['quantity']."',`price`='".$cp_service['price']."', `branch_id`='".$branch_id."'",[],$conn);
				}
			}else if($serr[0]=="mem"){
				query("INSERT INTO `membership_discount_history` set md_id='$serr[1]',invoice_id='$aid',client_id='$client', `branch_id`='0'",[],$conn);
			}
			
			$prc = $_POST["price"][$t];
			$actual_price = $_POST["actual_price"][$t];
			$qt = $_POST["qt"][$t];
			$staffid = $_POST["staffid"][$t];
			$package_id_ck  = $_POST["package_id_ck"][$t];
			
			if($reward_boost >= 1){
			    $reward_point = $_POST["reward_point"][$t]*$reward_boost;
			} else {
			    $reward_point = $_POST["reward_point"][$t];
			}
			
			$ser_stime = $_POST["ser_stime"][$t];
			$ser_etime = $_POST["ser_etime"][$t];
			$stock_id = $_POST["stock_id"][$t];

			if($_POST['givepoint'] == '0'){
			    $reward_point = 0;
			    query("UPDATE `invoice_".$branch_id."` set `is_rewardpoint_given` = '0' WHERE `id` = '$aid' and `branch_id`='".$branch_id."'",[],$conn);
			}
			
			$app_inv_item_id = get_insert_id("INSERT INTO `invoice_items_".$branch_id."` set `iid`='$aid',`product_stock_batch`='$stock_id',`client`='$client',`package_id`='$package_id_ck',`service`='$ser', `package_service_id`='$id', `quantity`='$qt',`staffid`='0',`disc_row`='$disc_row',`price`='$prc',`actual_price`='$actual_price',`type`='$serr[0]',`start_time`='$ser_stime',`end_time`='$ser_etime',`reward_point`='$reward_point',`bill`=1,`active`=0, `branch_id`='".$branch_id."', `client_branch_id`='".$client_branch_id."'",[],$conn);

			if($reward_point > 0){
				query("INSERT INTO `customer_reward_points` (`client_id`,`invoice_id`,`points_on`,`point_type`,`points`,`notes`,`status`,`branch_id`) VALUES ('cust,$client','bill,$aid','$app_inv_item_id','1','$reward_point','','1','$branch_id')",[],$conn);
			}    	
			
			query("INSERT INTO `transactions` (`iid`,`inv`,`client`,`service`,`quantity`,`staffid`,`disc_row`,`price`,`type`, `credit`,`actual_price`,`debit`,`date`,`active`,`branch_id`) VALUES ('$aid','$aid','$client','$serr[1]','$qt','$staffid','$disc_row','$prc','$serr[0]','$prc','$actual_price',0,'$doa',0,'$branch_id')",[],$conn);
			
			for($j=0;$j<count($_POST["staffid".$t.""]);$j++){
				$staffid = $_POST["staffid".$t.""][$j];
				$serr1 = explode(",", $ser);
				$comm = 0;
				if($serr1[0]=="sr"){
				    $type = 'service';
				} else if($serr1[0]=="pr"){
				    $type = 'product';
				} else {
				    $type = '';
				} 
				if($type != ''){
				    $total_sale = service_provider_total_sale($type, $staffid, $aid);
				    $comm = service_provider_commission($type, $staffid, $total_sale);
				}
				get_insert_id("INSERT INTO  `invoice_multi_service_provider` set `ii_id`='$app_inv_item_id',`inv`='$aid',`service_cat`='$ser_cat_id',`service_name`='$ser ',`service_provider`='$staffid',`commission_per`='$comm', `status`='1', `branch_id`='".$branch_id."'",[],$conn);
			}
			
		}
		
		
		if(isset($_GET['enqid']) && $_GET['enqid']>0){
			$equiry_id = $_GET['enqid'];
			$enquiryresponse_id = $_GET['enquiryresponse_id'];
			$date = date("Y-m-d");
			$now = new DateTime();
			$time = $now->format('H:i:s');
			query("update `enquiryresponse` set `leadstatus`='Converted',date='$date',`time`='$time' where id='$enquiryresponse_id' and branch_id='".$branch_id."'",[],$conn);
			query("update `enquiry` set `leadstatus`='Converted' where id='$equiry_id' and branch_id='".$branch_id."'",[],$conn);
		}
		
		
		$_SESSION['t']  = 1;
		$_SESSION['tmsg']  = "Bill Generated Successfully";

		
		if(isset($_POST['submit-bill'])){
		   $_SESSION['bill_generated'] = "$aid";
		   $_SESSION['bill_type'] = 'new';
		   echo '<meta http-equiv="refresh" content="0; url=billing.php" />';die();
		}
	}
	
	if(isset($_POST['update']) || isset($_POST['update-bill']) || isset($_POST['update-bill-print']) || isset($_POST['update-bill-sms'])){
		$beid               = addslashes(trim($_GET['beid']));
		$billdate           = addslashes(trim(date('Y-m-d')));
		$client             = addslashes(trim($_POST['clientid']));
		$name               = addslashes(trim($_POST['client']));
		$cont               = addslashes(trim($_POST['contact']));
	    $gender             = addslashes(trim($_POST['gender']));
		$gst                = addslashes(trim(($_POST['gst']) ? $_POST['gst'] : ''));
		$doa 	            = addslashes(trim($_POST['doa']));
		$time 	            = addslashes(trim(date('H:i',strtotime($_POST['time'])))); // billing time
		$dob                = addslashes(trim($_POST['dob']));
		$aniv               = addslashes(trim($_POST['aniv']));
		$referral_code      = addslashes(trim(strtoupper(substr($name,0,4)).strtoupper(substr(md5(sha1($cont)),0,4))));
		$leadsource         = addslashes(trim($_POST['leadsource'])); //source of client
		if($client == ''){
			$client=get_insert_id("INSERT INTO `client` set `name`=:name,`cont`=:cont,`referral_code`=:referral_code,`gst`=:gst,`gender`=:gender,`dob`=:dob,`aniversary`=:aniversary,`leadsource`=:leadsource,`active`=:active, `branch_id`='".$branch_id."'",[
			'name'  =>$name,
			'cont'  =>$cont,
			'gst'   =>$gst,
			'gender'=>$gender,
			'dob'   =>$dob,
			'aniversary'=>$aniv,
			'referral_code'=>$referral_code,
			'leadsource'=>$leadsource,
			'active' =>0
			],$conn);
			 query("INSERT INTO wallet SET iid='0', date = '".date('Y-m-d')."', client_id = '$client', wallet_amount='0', get_wallet_from='', status='1', branch_id='".$branch_id."'",[],$conn);
		} else {
			query("UPDATE `client` set `gender`=:gender,`dob`=:dob,`aniversary`=:aniversary,`referral_code`=:referral_code,`leadsource`=:leadsource where id=:id and branch_id='".$branch_id."'",[
			'gender'=>$gender,
			'dob'   =>$dob,
			'aniversary'=>$aniv,
			'referral_code'=>$referral_code,
			'leadsource'=>$leadsource,
			'id'    =>$client,
			],$conn);
		}
		
		if($_POST['total_disc_row_type'] == '0'){
			$dis 	 = 'pr,'.$_POST['dis'];
		} else if($_POST['total_disc_row_type'] == '1'){
			$dis 	= CURRENCY.','.$_POST['dis'];
		}
		
		$subtotal = $_POST['subtotal'];
		$coupon   = $_POST['ccid'];
		
		$disper = $_POST['discount2'];
		$tax = $_POST['tax'];
		$taxx = explode(",",$tax);
		$tax = $taxx[0];
		$taxtype = $taxx[2];
		$total = $_POST['total'];
		$adv = $_POST['adv'];
		$paid   = 0;
		for($p=0;$p<count($_POST['paid']);$p++){
			$paid+=$_POST['paid'][$p];
		}
		$used_reward_points = $_POST['used_redeem_points'];
		$due    = $total - $adv - $paid ;
		if($due > 0 && $due < 1){
		    $amount_to_add = $due;
		    $due = 0;
		} else {
		    $amount_to_add = 0;
		    $due    = ($due > 0) ? $due : 0;
		}

		$method = $_POST['method'];
		$notes ="";
		$notes = addslashes(trim($_POST['notes']));
		$referral_code = $_POST['referral_code'];

		$client_branch_id = $_POST['client_branch_id'];

		
	    query("DELETE from `invoice_items_".$branch_id."` where iid='$beid' and branch_id='".$branch_id."'",[],$conn);
		query("DELETE from `transactions` where iid='$beid' and branch_id='".$branch_id."'",[],$conn);
		query("DELETE from `invoice_multi_service_provider` where inv='$beid' and branch_id='".$branch_id."'",[],$conn);
		$wallte_return_amount = query_by_id("SELECT sum(wallet_amount) as total FROM wallet_history WHERE iid='bill,$beid' AND transaction_type='0'",[],$conn)[0]['total'];
		if($wallte_return_amount > 0){
			query("UPDATE `wallet` set `wallet_amount`= (wallet_amount+'$wallte_return_amount') WHERE `client_id` = '$client' and branch_id='".$branch_id."'",[],$conn);
		}
		query("DELETE from `wallet_history` where iid='bill,$beid' and status='1' and branch_id='".$branch_id."'",[],$conn);
		query("DELETE from `multiple_payment_method` where invoice_id='bill,$beid' and status='1' and branch_id='".$branch_id."'",[],$conn);
		query("DELETE from `customer_reward_points` where invoice_id='bill,$beid' and point_type='1' and branch_id='".$branch_id."'",[],$conn);

		$us_qty = query_by_id("SELECT * FROM package_service_history WHERE used_in_branch='".$branch_id."' AND invoice_id='".$beid."' AND status='1'",[],$conn);
	    foreach($us_qty as $uq){
	    	$qt = $uq['quantity'];
	    	$serid = $uq['service_id'];
	    	query("UPDATE client_package_services_used SET quantity_used = (quantity_used - $qt) WHERE client_id='".$uq['client_id']."' AND c_pack_id='".$uq['package_id']."' AND c_service_id='sr,$serid'",[],$conn);
	    	query("DELETE FROM `package_service_history` WHERE id='".$uq['id']."' AND status='1'",[],$conn);
	    }
		
		$wallet_amount = $_POST['invoice_wallet_amount'];
		
	    $paid_amount = 0;
		for($count = 0; $count < count($_POST['method']); $count++){
			$transaction_id = $_POST['transid'][$count];
			$paid = trim($_POST['paid'][$count]);
			$amount = trim($_POST['paid'][$count]);
			$method = trim($_POST['method'][$count]);
			if($paid == '0' || $paid == ''){
				continue;
			} else {
				if(count($_POST['method']) == 1){
					$paid_amount += $paid;
					$paid_amount = $paid_amount+$amount_to_add;
				} else {
					$paid_amount = $paid_amount+$paid;
					if($count == 0){
					    $paid_amount = $paid_amount+$amount_to_add;
					}
				}
				query("INSERT INTO `multiple_payment_method` (`invoice_id`,`payment_method`,`amount_paid`,`status`,`branch_id`,`transaction_id`) VALUES ('bill,$beid','$method','$amount',1,'$branch_id','$transaction_id')",[],$conn);
				if($method == 7){
					query("INSERT INTO `wallet_history` (`client_id`,`iid`,`transaction_type`,`wallet_amount`,`get_wallet_from`,`status`,`branch_id`) VALUES ('$client','bill,$beid',0,'$amount','Bill',1,'$branch_id')",[],$conn);
					query("UPDATE `wallet` set `wallet_amount`= (wallet_amount-'$amount') WHERE `client_id` = '$client' and branch_id='".$branch_id."'",[],$conn);
				}
				if($method == 9){
					$rpoint = redeempoint(1);
					$points = $amount*($rpoint);
					query("INSERT INTO `customer_reward_points` SET `invoice_id`='bill,$beid',`client_id`='cust,$client',`points_on`='0',`point_type`='2',`points`='$points',`notes`='Points redeem at the time of Billing.',`status`='1', `branch_id`='".$branch_id."'",[],$conn);
				}
			}
		}
		
		$reward_boost = $_POST['membership_reward_boost'];    
		$membership_appilied = $_POST['membership_appilied'];
		$membership_id = $_POST['membership_id'];
		
		query("UPDATE `invoice_".$branch_id."` set `paid` = '$paid_amount', `due` = '$due' WHERE `id` = '$beid' and `branch_id`='".$branch_id."'",[],$conn);

		query("UPDATE `invoice_".$branch_id."` SET `gst`='$gst',`client`='$client',`referral_code`='$referral_code',`doa`='$doa',`itime`='$time',`dis`='$dis',`disper`='$disper',`tax`='$tax',`taxtype`='$taxtype',`total`='$total', `subtotal`='$subtotal',`advance`='$adv',`notes`='$notes',`coupon`='$coupon',`bpaid`='$paid',`bmethod`='$method',`pay_method`='$method',`type`='2',`used_reward_points`='$used_reward_points',`status`='Bill',`invoice`=1, `membership_appilied`='".$membership_appilied."',`membership_id`='".$membership_id."',`active`=0, `client_branch_id`='".$client_branch_id."' WHERE id=:beid and branch_id='".$branch_id."'",["beid"=>$beid],$conn); 
		
		
		for($t=0;$t<count($_POST["services"]);$t++){
			$pservice_rev = $_POST['pa_ser'][$t];
			if($pservice_rev != ''){
				$id_rev = explode("-",$pservice_rev);
			    if(isset($id_rev[2]) && $id_rev[2] == 'appointment'){
				     continue;
				} else {
				    $id_rev = $id_rev[1];
    				$used_qty = query("select quantity_used from client_package_services_used WHERE id='".$id_rev."'",[],$conn)[0]['quantity_used'];
    				$sql_up_2_rev = "update client_package_services_used set tmp_qty = '0' where id='".$id_rev."'";
    				query($sql_up_2_rev,[],$conn);
				}
			}
		}
		
		if($_POST['invoice_wallet_amount'] > 0){
			$wallet_amount 	= $_POST['invoice_wallet_amount'];
			query("INSERT INTO `wallet_history` (`client_id`,`iid`,`transaction_type`,`wallet_amount`,`get_wallet_from`,`status`,`branch_id`) VALUES ('$client','bill,$beid',2,'$wallet_amount','Bill',1,'$branch_id')",[],$conn);
			query("UPDATE `wallet` set `wallet_amount`= (wallet_amount-'$wallet_amount') WHERE `client_id` = '$client' and branch_id='".$branch_id."'",[],$conn);
			
			query("INSERT INTO `wallet_history` (`client_id`,`iid`,`transaction_type`,`wallet_amount`,`get_wallet_from`,`status`,`branch_id`) VALUES ('$client','bill,$beid',1,'$wallet_amount','Bill (Advance amount)',1,'$branch_id')",[],$conn);
			query("UPDATE `wallet` set `wallet_amount`= (wallet_amount+'$wallet_amount') WHERE `client_id` = '$client' and branch_id='".$branch_id."'",[],$conn);
		}
		
		for($t=0;$t<count($_POST["services"]);$t++){
			$ser 			= $_POST["service"][$t];
			$ser_cat_id 	= $_POST["ser_cat_id"][$t]; 
			$package_id_ck  = $_POST["package_id_ck"][$t];

			$pservice = $_POST['pa_ser'][$t];
			if($pservice != ''){
				$id = explode("-",$pservice);
				if(isset($id[2]) && isset($id[2]) == 'appointment'){
    				
				} else {
				    $id = $id[1];
    				$sql_up = "update client_package_services_used set quantity_used = quantity_used+1 where id='".$id."'";
    				query($sql_up,[],$conn); 
    				$sql_up_2 = "update client_package_services_used set tmp_qty = '0' where id='".$id."'";
    				query($sql_up_2,[],$conn);
    				$package_id = query_by_id("SELECT c_pack_id FROM client_package_services_used WHERE id='".$id."'",[],$conn)[0]['c_pack_id'];
    				query("INSERT INTO package_service_history SET package_id='".$package_id."', service_id='".$id."', quantity='1', client_id='".$client."', used_in_branch='".$branch_id."', invoice_id='".$beid."', status='1'",[],$conn);
				}
			} else {
				$id = '0';
			}
			
			
			if($_POST['disc_row_type'][$t] == '0'){
				$disc_row = 'pr,'.$_POST["disc_row"][$t];
				}else if($_POST['disc_row_type'][$t] == '1'){
				$disc_row =  CURRENCY.','.$_POST["disc_row"][$t];
			}
			
			$serr = explode(",", $ser); 
			if($serr[0]=="sr"){
				$serr[0] = "Service";
				}else if($serr[0]=="pr"){
				$serr[0] = "Product";
				}else if($serr[0]=="pa"){
				$serr[0] = "Package";
				$check_package = query_by_id("SELECT count(*) as total FROM clientpackages WHERE inv='".$beid."' AND package='".$serr[1]."'",[],$conn)[0]['total'];
				if($check_package <= 0){
				query("INSERT INTO `clientpackages`(`package`,`client`,`inv`,`active`,`branch_id`) VALUES ('$serr[1]','$client','$beid',0,'$branch_id')",[],$conn);
				
				$client_package_services_used=query("SELECT category,service,quantity,price FROM `packageservice` where pid='$serr[1]' and active='0' and branch_id='".$branch_id."'",[],$conn);
				
				foreach($client_package_services_used as $cp_service){
					query("INSERT INTO `client_package_services_used` SET `inv`='$beid',`client_id`='$client',`c_pack_id`='$serr[1]',`category_id`='".$cp_service['category']."',`c_service_id`='".$cp_service['service']."',`quantity`='".$cp_service['quantity']."',`price`='".$cp_service['price']."', `branch_id`='".$branch_id."'",[],$conn);
				}}
				}else if($serr[0]=="mem"){
					query("INSERT INTO `membership_discount_history` set md_id='$serr[1]',invoice_id='$aid',client_id='$client', branch_id='0'",[],$conn);
			}
			
			$prc = $_POST["price"][$t];
			$actual_price = $_POST["actual_price"][$t];
			$qt = $_POST["qt"][$t];
			$staffid = $_POST["staffid"][$t];
			
			$ser_stime = $_POST["ser_stime"][$t];
			$ser_etime = $_POST["ser_etime"][$t];
			$stock_id = $_POST["stock_id"][$t];
			
			if($reward_boost >= 1){
			    $reward_point = $_POST["reward_point"][$t]*$reward_boost;
			} else {
			    $reward_point = $_POST["reward_point"][$t];
			}

			if($_POST['givepoint'] == '0'){
			    $reward_point = 0;
			    query("UPDATE `invoice_".$branch_id."` set `is_rewardpoint_given` = '0' WHERE `id` = '$beid' and `branch_id`='".$branch_id."'",[],$conn);
			}
			
			$ii_id = $_POST["item_row"][$t];
			
			$app_inv_item_id = get_insert_id("INSERT INTO `invoice_items_".$branch_id."` set `iid`='$beid', `client`='$client',`product_stock_batch`='$stock_id',`package_id`='$package_id_ck',`service`='$ser', `package_service_id`='$id', `quantity`='$qt',`staffid`='$staffid',`disc_row`='$disc_row',`price`='$prc',`actual_price`='$actual_price',`type`='$serr[0]',`start_time`='$ser_stime',`end_time`='$ser_etime',`bill`=1, `active`=0, `branch_id`='".$branch_id."', `client_branch_id`='".$client_branch_id."'",[],$conn);
			
			query("UPDATE product_usage SET ii_id = '".$app_inv_item_id."' WHERE invoice_id='".$beid."' AND ii_id='".$ii_id."' and branch_id='".$branch_id."'",[],$conn);

			query("INSERT INTO `transactions` (`iid`,`inv`,`client`,`service`,`quantity`,`staffid`,`disc_row`,`price`,`type`, `credit`,`actual_price`,`debit`,`date`,`active`,`branch_id`) VALUES ('$beid','$beid','$client','$serr[1]','$qt','$staffid','$disc_row','$prc','$serr[0]','$prc','$actual_price',0,'$doa',0,'$branch_id')",[],$conn);
			
			
			if($reward_point > 0){
    			query("INSERT INTO `customer_reward_points` (`client_id`,`invoice_id`,`points_on`,`point_type`,`points`,`notes`,`status`,`branch_id`) VALUES ('cust,$client','bill,$beid','$app_inv_item_id','1','$reward_point','','1','$branch_id')",[],$conn);
    		}
			
			for($j=0;$j<count($_POST["staffid".$t.""]);$j++){
				$staffid = $_POST["staffid".$t.""][$j];
				$serr1 = explode(",", $ser);
				$comm = 0;
				if($serr1[0]=="sr"){
				    $type = 'service';
				} else if($serr1[0]=="pr"){
				    $type = 'product';
				} else {
				    $type = '';
				} 
				if($type != ''){
				    $total_sale = service_provider_total_sale($type, $staffid, $beid);
				    $comm = service_provider_commission($type, $staffid, $total_sale);
				}
				$inv_item_id = get_insert_id("INSERT INTO  `invoice_multi_service_provider` set `ii_id`='$app_inv_item_id',`inv`='$beid',`service_cat`='$ser_cat_id',`service_name`='$ser ',`service_provider`='$staffid', `commission_per`='$comm', `status`='1', `branch_id`='".$branch_id."'",[],$conn);
			}
			
		}	
		
		
		
		if(isset($_POST['update'])|| isset($_POST['update-bill-sms'])){
			$system = systemname();
			if($wallet_status == '1'){
				$current_date = date(MY_DATE_FORMAT);
				$current_time = date(TIME_FORMAT);
				$system = systemname();
				$msg = "Thank You ".$name." for your purchase of ".$total.". your wallet debited with ".CURRENCY." ".$paid." on ".$current_date." ".$current_time.". Avl Wallet Balance ".CURRENCY." ".client_wallet($client)." ".$system;
				send_sms($cont,$msg);
				}else{
				$msg = "Thank You ".$name." for your purchase of ".$total." on ".$doa.".\nFrom ".$system;
				send_sms($cont,$msg);
			}
		}
		
		$_SESSION['t']  = 1;
		$_SESSION['tmsg']  = "Bill Updated Successfully";
		
		if(isset($_POST['update-bill'])){
		   $_SESSION['bill_generated'] = "$beid";
		   $_SESSION['bill_type'] = 'updated';
		  	echo '<meta http-equiv="refresh" content="0; url=billing.php?beid='.$beid.'" />';die();
		}
		
	}
	
	if(isset($_GET['del']) && $_GET['del']>0){
	    if(DELETE_BUTTON_INACTIVE != 'true'){
    		$delete_id =$_GET['del'];
    		$client_id = $_GET['c_id'];

    		$services = query_by_id("SELECT * FROM invoice_items_".$branch_id." WHERE iid='".$delete_id."'",[],$conn);

    		foreach($services as $ser){
    			if($ser['package_service_id'] > 0){
    				$used_qty = query("select quantity_used from client_package_services_used WHERE id='".$ser['package_service_id']."'",[],$conn)[0]['quantity_used'];
    				if($used_qty > 0){
						$sql_up_rev = "update client_package_services_used set quantity_used =  '$used_qty'-1 where id='".$ser['package_service_id']."'";
						query($sql_up_rev,[],$conn); 
						query("update package_service_history set status='0' WHERE invoice_id='".$delete_id."' AND client_id='".$client_id."' AND used_in_branch='".$branch_id."' AND service_id='".$ser['package_service_id']."'",[],$conn);
					}
					$sql_up_2_rev = "update client_package_services_used set tmp_qty = '0' where id='".$ser['package_service_id']."'";
					query($sql_up_2_rev,[],$conn); 
					
    			}
    		}

    		$query3 = "SELECT * FROM wallet_history WHERE iid = 'bill,$delete_id' AND transaction_type = 0 AND status = 1 and branch_id='".$branch_id."'";
    		$result3 = query_by_id($query3,[],$conn);
    		if($result3){
    			foreach ($result3 as $res3) {
    				// query("INSERT INTO `wallet_history` (`client_id`,`iid`,`transaction_type`,`wallet_amount`,`get_wallet_from`,`status`,`branch_id`) VALUES ('".$res3['client_id']."','".$res3['iid']."',2,'".$res3['wallet_amount']."','Bill',1,'$branch_id')",[],$conn);
    				query("UPDATE `wallet` set `wallet_amount`= (wallet_amount+".$res3['wallet_amount'].") WHERE `client_id` = '".$res3['client_id']."' and branch_id='".$branch_id."'",[],$conn);
    			}
    		}

    		$sql_app_id= query_by_id("SELECT appointment_id from `invoice_".$branch_id."` where id='$delete_id' and active='0' and branch_id='".$branch_id."'",[],$conn)[0];
    		$sql_service_slip_id= query_by_id("SELECT service_slip_id from `invoice_".$branch_id."` where id='$delete_id' and active='0' and branch_id='".$branch_id."'",[],$conn)[0];
    		query("UPDATE `invoice_".$branch_id."` set `active`='1' where `id`='$delete_id' and active='0' and branch_id='".$branch_id."'",[],$conn);
    		query("UPDATE `invoice_items_".$branch_id."` set `active`='1' where `iid`='$delete_id' and active='0' and branch_id='".$branch_id."'",[],$conn);
    		query("UPDATE `invoice_".$branch_id."` set `status`='0' where `inv`='$delete_id' and status='1' and branch_id='".$branch_id."'",[],$conn);
    		query("UPDATE `app_invoice_".$branch_id."` set `bill_created_status`='0' where `id`='".$sql_app_id['appointment_id']."' and branch_id='".$branch_id."'",[],$conn);
    		query("UPDATE `service_slip` set `bill_created_status`='0' where `id`='".$sql_service_slip_id['service_slip_id']."' and  branch_id='".$branch_id."'",[],$conn);
    		query("UPDATE `wallet_history` set `status`='0' where `iid`='bill,$delete_id' and status='1' and branch_id='".$branch_id."'",[],$conn);
    		query("UPDATE `invoice_pending_payment` set `status`='0' where `iid`='$delete_id' and status='1' and branch_id='".$branch_id."'",[],$conn);
    		query("UPDATE `customer_reward_points` SET status='0' WHERE invoice_id='bill,".$delete_id."'",[],$conn);
    		query("UPDATE `transactions` SET active='1' WHERE inv='".$delete_id."'",[],$conn);
    		$_SESSION['t']  = 1;
    		$_SESSION['tmsg']  = "Bill Deleted Successfully";
    		echo '<meta http-equiv="refresh" content="0; url=billing.php" />';die();
	    }
	}
	
	if(isset($_POST['wallet_submit'])){

		$client_id 	            = addslashes(trim($_POST['client_id_wallet']));
		$wallet_amount 	        = addslashes(trim($_POST['wallet_amount']));
		$date 	                = addslashes(trim($_POST['date_wallet']));
		$payment_method         = addslashes(trim($_POST['payment_method_wallet']));
		$paid_amount            = addslashes(trim($_POST['paid_amount_wallet']));
		$gender                 = addslashes(trim($_POST['gender']));
		$dob                    = addslashes(trim($_POST['dob']));
		$aniv                   = addslashes(trim($_POST['aniv']));
		$leadsource             = addslashes(trim($_POST['leadsource']));
		$client_branch_id_wallet = addslashes(trim($_POST['client_branch_id_wallet']));

		// echo "UPDATE `wallet` set wallet_amount=wallet_amount+'".$wallet_amount."' where id=:id and client_id = '$client_id' and payment_method = '$payment_method' and status = '1' and branch_id='".$branch_id."'";
		// echo "<br/>INSERT INTO `wallet` set date='$date',payment_method='$payment_method',client_id='$client_id',wallet_amount='$wallet_amount',get_wallet_from='Add_wallet', branch_id='";

		// die();
		$wallet_id = query_by_id("SELECT id FROM `wallet` WHERE client_id = '$client_id' and status = '1' and branch_id='".$branch_id."' order by id DESC limit 1",[],$conn)[0];
		if(count($wallet_id) > 0){
			query("UPDATE `wallet` set wallet_amount=wallet_amount+'".$wallet_amount."' where id=:id and client_id = '$client_id' and status = '1' and branch_id='".$client_branch_id_wallet."'",['id'=>$wallet_id['id']],$conn);
		} else {
			$aid=get_insert_id("INSERT INTO `wallet` set date='$date',payment_method='0',client_id='$client_id',wallet_amount='$wallet_amount',get_wallet_from='Add_wallet', branch_id='".$branch_id."'",[],$conn);	
		}
		
		query("INSERT INTO `wallet_history` set client_id='$client_id',transaction_type='1', wallet_amount='$wallet_amount', paid_amount = '$paid_amount', get_wallet_from='Add_wallet', payment_method='$payment_method', branch_id='".$branch_id."'",[],$conn);
        
        query("UPDATE `client` set `gender`=:gender,`dob`=:dob,`aniversary`=:aniversary,`leadsource`=:leadsource where id=:id and branch_id='".$branch_id."'",[
			'gender'=>$gender,
			'dob'   =>$dob,
			'aniversary'=>$aniv,
			'leadsource'=>$leadsource,
			'id'    =>$client_id,
			],$conn);
			
		if(isset($_POST['send_receipt'])){
			$current_date = date(MY_DATE_FORMAT);
			$current_time = date(TIME_FORMAT);
			$sql_client_name = query_by_id("SELECT c.name as client_name,c.cont from client c where c.id=:client_id and branch_id='".$branch_id."'",["client_id"=>$client_id],$conn)[0];
			$system = systemname();
			$client_name = $sql_client_name['client_name'];
			$contact     = $sql_client_name['cont'];
			
			$sms_data = array(
    	        'name' => $client_name,
    	        'currency' => CURRENCY,
    	        'amount' => $wallet_amount,
    	        'date' => $current_date,
    	        'time' => $current_time,
    	        'balance' => client_wallet($client_id),
    	        'salon_name' => systemname()
    	    );
			
			send_sms($contact,$sms_data,'wallet_recharge_sms');
		}
		
		$_SESSION['t']  = 1;
		$_SESSION['tmsg']  = "Wallet amount added successfully";
		$_SESSION['sub-tab-id'] = "wallet_tab";
		header('location:billing.php');
		exit();
	}
	
	include "topbar.php";
	include "header.php";
	include "menu.php";
?>
<style>
	.btn-add{
    	/*border-radius: 6px!important;*/
    	padding: 6px 6px;
    	margin-left :100px;
    	position:relative;
	}
	
	.btn-remove{
    	/*border-radius: 6px!important;*/
    	padding: 6px 6px;
    	margin-left :100px;
    	position:relative;
	}
	
	/* css for small fields */
	.qt, .disc_row, .disc_row_type{
	    min-width:70px;
	}
	.start_time, .end_time{
	    min-width:85px;
	}
	
	.ui-autocomplete {
         z-index: 9999 !important;
    }
    
    .sub-tabs{
        background-color: #032f54;
        text-align: center;
        padding: 15px 0px;
        color: #fff;
        border-radius: 3px;
        cursor: pointer;
    }
    .sub-tabs:hover{
        background-color: #004782;
    }
    .sub-tabs.active{
        background-color : #be63de;
    }
    .sub-tabs.active:after {
        content: '';
        position: absolute;
        left: 40%;
        top: 50px;
        width: 0;
        height: 0;
        border-left: 10px solid transparent;
        border-right: 10px solid transparent;
        border-top: 10px solid #be63de;
        clear: both;
    }
	
</style>
<!-- Dashboard wrapper starts -->

<div class="dashboard-wrapper">
    
	<!-- Main container starts -->
	<div class="main-container">
	    
	    <div class="row gutter">
	        <div class="col-lg-1 col-md-2 col-sm-2 col-xs-3">
                <div class="sub-tabs" id="bill_tab">
                    Bill
                </div>
            </div>
            <div class="col-lg-1 col-md-2 col-sm-2 col-xs-3">
                <div class="sub-tabs" id="wallet_tab">
                    Wallet
                </div>
            </div>
            <!--<div class="col-lg-1 col-md-2 col-sm-2 col-xs-3">-->
            <!--    <div class="sub-tabs" id="gift_card_tab">-->
            <!--        Gift card-->
            <!--    </div>-->
            <!--</div>-->
        </div>
        <br />
    		<form action="" method="post" id="main-form">
    		<!-- Row starts -->
    		<div class="row gutter">
    			<div data-id="bill_tab" class="data-card">
    			<div class="col-lg-9 col-md-12 col-sm-12 col-xs-12">
    	    
    				<div class="panel">
    					<div class="panel-heading">
    						<h4>Generate new bill</h4>
    					</div>
    					<div class="panel-body">
    					    <div id="member_ship_message"></div>
    					    <div class="row">
    							<div class="col-lg-3 col-md-3 col-sm-3 col-xs-6">
    								<div class="form-group">
    									<label for="userName">Date of billing <span class="text-danger">*</span></label>
    									<?php $date = date('Y-m-d'); ?>
    									<input type="text" class="form-control date" value="<?=(($edit['doa']) ? $edit['doa'] : ((isset($_SESSION['store_bill_date'])) ? $_SESSION['store_bill_date'] : $date))?>" name="doa" id="date" readonly>
    								</div>
    							</div>
    							
    							<div class="col-lg-3 col-md-3 col-sm-3 col-xs-6">
    								<div class="form-group">
    									<label for="userName">Contact number <span class="text-danger">*</span></label>
    									<input type="text" class="form-control client " id="cont" onBlur="check();contact_no_length($(this), this.value);" name="contact" placeholder="Client contact" value="<?=($edit['cont'])?$edit['cont']:''?>" onkeyup="this.value = this.value.replace(/[^0-9\.]/g,'');" maxLength="<?= PHONE_NUMBER ?>" required>
    									<span style="color:red" id="client-status"></span>
    									<span style="color:red" id="digit_error"></span>
    								</div>
    							</div>
    							<div class="col-lg-3 col-md-3 col-sm-3 col-xs-6">
    								<div class="form-group">
    									<label for="userName">Client name <span class="text-danger">*</span></label>
    									<input type="text" class="client form-control client_name"  id="client" name="client" placeholder="Autocomplete (Phone)" value="<?=$edit['name']?>" required>
    									<input type="hidden" value="<?=($edit['c_id'])?$edit['c_id']:''?>" name="clientid" id="clientid" class="clt"> 
    									<input type="hidden" name="client_branch_id" id="client_branch_id" value="<?=$edit['client_branch_id']?>" class="clt">
    								</div>
    							</div>
    							<div class="col-lg-3 col-md-3 col-sm-3 col-xs-6">
    								<div class="form-group">
    									<?php $time = date('h:i A'); ?>
    									<label for="time">Time of billing <span class="text-danger">*</span></label>
    									<input type='text' class="form-control slot dat <?= extratimeStatus()=='1'?'maintime':'time'?>" name="time"  value="<?=($edit['itime'])?date('h:i A',strtotime($edit['itime'])):$time; ?>" id="time" required readonly>
    								</div>
    							</div>
    							
    							<div class="clearfix"></div>
    							<div class="col-lg-12">
    								<div class="table-responsive responsive_tbl">
    									<table class="table table-bordered">
    										<thead>
    											<tr>
    												
    												<th colspan="2">Service / Products / Packages</th>
    												<th >Qty</th>
    												<th >Discount</th>
    												<th >Service provider</th>
    												<th>Start & end time</th>
    												<th >Price</th>
    											</tr>
    										</thead>
    										<tbody>
    											<?php  if(isset($_GET['bid']) && $_GET['bid']>0){ 
    												$bid=$_GET['bid'];
    												
    												$roomsql1 = "SELECT room_id from `app_invoice_".$branch_id."` where id = '".$beid."'";
                                                    $roomresult1 = query_by_id($roomsql1,[],$conn)[0];
    												
    												if($roomresult1->room_id != ""){
    												$sql2_bid="SELECT  msp.aii_staffid,msp.service_Provider,b.name,b.id b_id,aii.client,aii.service,aii.package_service_id,aii.quantity,aii.disc_row,aii.staffid,aii.price,aii.start_time,aii.end_time,s.price as service_price,s.name as service_name,s.cat,CONCAT('sr,',s.id) as service_id,s.duration as service_durr,s.points,scat.cat as cat_name from `app_invoice_items_".$branch_id."` aii "
    												." LEFT JOIN `service` s on s.id=SUBSTRING_INDEX(aii.service,',',-1) "
    												." LEFT JOIN `servicecat` scat on scat.id=s.cat "
    												." LEFT JOIN `app_multi_service_provider` msp on msp.aii_staffid=aii.id " 
    												." LEFT JOIN `beauticians` b on b.id=msp.service_Provider "
    												." where aii.iid='$bid' and aii.active=0 and aii.type='Service' and aii.branch_id='".$branch_id."'  GROUP BY aii_staffid";
    												$result2_bid=query_by_id($sql2_bid,[],$conn);
    												foreach($result2_bid as $row2_bid) { ?>
    												
    												<tr id="TextBoxContainer" class="TextBoxContainer">
    													
    
    													<td style="vertical-align: middle"><span class="sno"><span class="icon-dots-three-vertical"></span></span></td>
    													<td>
    														<input type="text" class="ser form-control slot" name="services[]" value="<?=$row2_bid['service_name']?>" placeholder="Service (Autocomplete)" required>
    														<input type="hidden" name="service[]" value="<?= $row2_bid['service_id']?>" class="serr" required>
    														<input type="hidden" name="stock_id[]" value="0" class="stock_id">
    														<input type="hidden" name="durr[]" value="<?= $row2_bid['service_durr']?>" class="durr">
    														<input type="hidden" name="pa_ser[]" value="<?= $row2_bid['package_service_id'] != '0'?$row2_bid['service'].'-'.$row2_bid['package_service_id'].'-appointment':''?>" class="pa_ser">
    													    <input type="hidden" name="item_row[]" value="" />
    													</td>
    															
    													<td>
    														<input type="number" name="qt[]" min="1" class="positivenumber qt form-control sal slot" value="<?=($row2_bid['quantity'])?$row2_bid['quantity']:'1'?>">
    													</td>
    													
    													<td>
    														<table class="inner-table-space">
    															<tr>
    																<td>
    																	<input type="number"  value="<?=explode(",",$row2_bid['disc_row'])[1]?>" name="disc_row[]" class="form-control disc_row positivenumber decimalnumber singleservicediscount" step="0.01" >
    																</td>
    																<td>
    																	<select class="form-control disc_row_type" name="disc_row_type[]" id="disc_row_type">
    																		<option <?=(explode(",",$row2_bid['disc_row'])[0] == CURRENCY)?'value="1" Selected':'value="1"'?>><?=CURRENCY?></option>
    																		<option <?=(explode(",",$row2_bid['disc_row'])[0] == 'pr')?'value="0" Selected':'value="0"'?>>%</option>
    																	</select>
    																</td>
    															</tr>
    														</table>														
    													</td>
    													<td class="spr_row"> 
    														<?php 
    															$aii_staffid= $row2_bid['aii_staffid'];						
    															$sql1="SELECT msp.service_Provider,b.name,b.id b_id FROM `app_multi_service_provider` msp  LEFT JOIN `beauticians` b on b.id=msp.service_Provider where msp.aii_staffid='$aii_staffid' and status='1' and msp.branch_id='".$branch_id."'";
    															$result1 = query_by_id($sql1,[],$conn);
    															if($result1){
    																foreach ($result1 as $row_2){
    																	
    																?>
    																
    																<table id="add_row"><tbody><tr>
    																	<td id="select_row">  <select name="staffid0[]" data-validation="required" class="form-control staff" required>
    																		<option value="">Service provider</option>          
    																		<?php 
																				$sql1="SELECT * FROM beauticians WHERE find_in_set('".explode(',',$row2['service_id'])[1]."', services) <> 0 AND active = '0' and type='2' and branch_id='".$branch_id."' ORDER BY name ASC";
																				$sql2="SELECT * FROM beauticians WHERE active = '0' and type='2' and branch_id='".$branch_id."' ORDER BY name ASC";
																				$result1 = query_by_id($sql1,[],$conn);
																				$result2 = query_by_id($sql2,[],$conn);
																				if($result1 && explode(',',$row2['service_id'])[0] == 'ser'){
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
    																<input type="hidden" name="duration[]" value="<?=$row2_bid['service_durr']?>" class="duration">
    															    <input type="hidden" name="ser_stime[]" value="<?=$row2_bid['start_time']?>" class="ser_stime">
    															    <input type="hidden" name="ser_etime[]" value="<?=$row2_bid['end_time']?>" class="ser_etime">
                    												<td>
                    													<table>
                    														<tr>
                    															<td width="50%">
                    																<input type="text" class="form-control start_time time" value="<?= date('h:i A',strtotime($row2_bid['start_time']))?>" placeholder="Start time" name="start_time[]" onchange="servicestarttime(this.value, $(this))" readonly>
                    																</td>						<td>&nbsp;to&nbsp;</td>																										       <td width="50%">
                    																<input type="text" class="form-control end_time"  name="end_time[]" value="<?= date('h:i A',strtotime($row2_bid['end_time']))?>"  placeholder="End time"  readonly>
                    															</td>
                    														</tr>
                    													</table>														
                    												</td> 
    																
    															<?php } ?>
    													</td>
    													
    													<td>
    														<input type="number" class="pr form-control price positivenumber decimalnumber servicepriceafterdiscount" step="0.01" name="price[]" id="userName" placeholder="9800.00" value="<?=$row2_bid['price']?>" > 
    														<input type="hidden" class="prr" name="actual_price[]" value="<?=str_replace(",","",$row2_bid['service_price'])?>">
    														<input type="hidden" class="rpoint" name="reward_point[]" value="<?=str_replace(",","",$row2_bid['points'])?>">
    													</td>
    													
    												</tr> 
    												
    												<?php } } else { ?>
    												
    												<tr id="TextBoxContainer" class="TextBoxContainer">
    
    															<td style="vertical-align: middle"><span class="sno"><span class="icon-dots-three-vertical"></span></span></td>
    															<td width="95%">
    																<input type="text" class="ser form-control slot" name="services[]" value="<?=$row2['service_name']?>" placeholder="Service (Autocomplete)" required>
    																<input type="hidden" name="service[]" value="" class="serr" required>
    																<input type="hidden" name="stock_id[]" value="0" class="stock_id" required>
    																<input type="hidden" name="durr[]" value="" class="durr">
    																<input type="hidden" name="pa_ser[]" value="" class="pa_ser">
    																<input type="hidden" name="item_row[]" value="" />
    															</td>
    														
    												<td>
    												<input type="number" name="qt[]" min="1" class="positivenumber qt form-control sal slot" value="<?=($row2['quantity'])?$row2['quantity']:'1'?>"></td>
    												<td>
    													<table class="inner-table-space">
    														<tr>
    															<td>
    																<input type="number"  value="0" name="disc_row[]" class="form-control disc_row positivenumber decimalnumber singleservicediscount" step="0.01" >
    															</td>
    															<td>
    																<select class="form-control disc_row_type" name="disc_row_type[]" id="disc_row_type">
    																    <option <?=(explode(",",$row2['disc_row'])[0] == CURRENCY)?'value="1" Selected':'value="1"'?> selected><?=CURRENCY?></option>
    																	<option <?=(explode(",",$row2['disc_row'])[0] == 'pr')?'value="0" Selected':'value="0"'?>>%</option>
    																</select>
    															</td>
    														</tr>
    													</table>
    												</td>
    												
    												
    												<td class="spr_row"> 
    													
    													
    													<table id="add_row"><tbody><tr>
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
    														</td></tr>
    													</tbody>
    													</table>
    												</td>
    												<input type="hidden" name="duration[]"  value="" class="duration">
    												<input type="hidden" name="ser_stime[]" value="" class="ser_stime">
    												<input type="hidden" name="ser_etime[]" value="" class="ser_etime">
    												<td>
    													<table>
    														<tr>
    															<td width="50%">
    																<input type="text" class="form-control start_time time" value="" placeholder="Start time" name="start_time[]" onchange="servicestarttime(this.value, $(this))" readonly>
    																</td>						<td>&nbsp;to&nbsp;</td>																										       <td width="50%">
    																<input type="text" class="form-control end_time"  name="end_time[]" value=""  placeholder="End time"  readonly>
    															</td>
    														</tr>
    													</table>														
    												</td> 
    												<td>
    													<input type="number" class="pr form-control price positivenumber decimalnumber servicepriceafterdiscount" step="0.01" name="price[]" id="userName" placeholder="9800.00" value=""> 
    													<input type="hidden" class="prr" name="actual_price[]" value="">
    													<input type="hidden" class="rpoint" name="reward_point[]" value="">
    												</td>
    											</tr>
    												
    												
    												
    												
    												
    												
    											<?php }	} elseif(isset($_GET['beid']) && $_GET['beid']>0){
    												$b_id =  $_GET['beid'];	
    												$sql2="SELECT ii.id as item_id, ii.package_service_id, ii.actual_price,ii.package_id,imsp.ii_id,imsp.service_Provider,b.name,b.id b_id,ii.client,ii.service,ii.quantity,ii.disc_row,ii.staffid,ii.price,s.price as service_price,s.name as service_name,s.cat,CONCAT('sr,',s.id) as service_id,s.duration as service_durr,s.points,scat.cat as cat_name, ii.start_time, ii.end_time, ii.product_stock_batch from `invoice_items_".$branch_id."` ii"
    												." LEFT JOIN `service` s on s.id=SUBSTRING_INDEX(ii.service,',',-1)"
    												." LEFT JOIN `servicecat` scat on scat.id=s.cat"
    												." LEFT JOIN `invoice_multi_service_provider` imsp on imsp.ii_id=ii.id"
    												." LEFT JOIN `beauticians` b on b.id=imsp.service_Provider "
    												." where ii.iid='$b_id' and ii.active=0 and ii.type='Service' and ii.branch_id='".$branch_id."' GROUP BY ii_id" 
    												." UNION SELECT ii.id as item_id, ii.package_service_id, ii.actual_price,ii.package_id,imsp.ii_id,imsp.service_Provider,b.name,b.id b_id,ii.client,ii.service,ii.quantity,ii.disc_row,ii.staffid,ii.price,s.price as service_price,s.name as service_name,'0' as cat,CONCAT('pr,',s.id) as service_id,'0' as service_durr,s.reward as points,0 as cat_name, ii.start_time, ii.end_time, ii.product_stock_batch from `invoice_items_".$branch_id."` ii"
    												." LEFT JOIN `products` s on s.id=SUBSTRING_INDEX(ii.service,',',-1)"
    												." LEFT JOIN `invoice_multi_service_provider` imsp on imsp.ii_id=ii.id"
    												." LEFT JOIN `beauticians` b on b.id=imsp.service_Provider "
    												." where ii.iid='$b_id' and ii.active=0 and ii.type='Product' and ii.branch_id='".$branch_id."' GROUP BY ii_id"
    												." UNION SELECT ii.id as item_id, ii.package_service_id, ii.actual_price,ii.package_id,imsp.ii_id,imsp.service_Provider,b.name,b.id b_id,ii.client,ii.service,ii.quantity,ii.disc_row,ii.staffid,ii.price,s.price as service_price,s.name as service_name,'0' as cat,CONCAT('pa,',s.id) as service_id,'0' as service_durr,s.points,0 as cat_name, ii.start_time, ii.end_time, ii.product_stock_batch from `invoice_items_".$branch_id."` ii"
    												." LEFT JOIN `packages` s on s.id=SUBSTRING_INDEX(ii.service,',',-1)"
    												." LEFT JOIN `invoice_multi_service_provider` imsp on imsp.ii_id=ii.id"
    												." LEFT JOIN `beauticians` b on b.id=imsp.service_Provider "
    												." where ii.iid='$b_id' and ii.active=0 and ii.type='Package' and ii.branch_id='".$branch_id."' GROUP BY ii_id"
    												." UNION SELECT ii.id as item_id, ii.package_service_id, ii.actual_price, ii.package_id,imsp.ii_id,imsp.service_Provider,b.name,b.id b_id,ii.client,ii.service,ii.quantity,ii.disc_row,ii.staffid,ii.price,md.membership_price as service_price,md.membership_name as service_name,'' as cat,CONCAT('mem,',md.id) as service_id,'0' as service_durr,'0' as points,' ' as cat_name, ii.start_time, ii.end_time, ii.product_stock_batch from `invoice_items_".$branch_id."` ii "	
                                                    ." LEFT JOIN `membership_discount` md on md.id=SUBSTRING_INDEX(ii.service,',',-1) "	
                                                    ." LEFT JOIN `invoice_multi_service_provider` imsp on imsp.ii_id=ii.id "	
                                                    ." LEFT JOIN `beauticians` b on b.id=imsp.service_Provider where ii.iid='$b_id' and ii.active=0 and ii.type='mem' and ii.branch_id='".$branch_id."' GROUP BY ii_id";
    
    												$result2=query_by_id($sql2,[],$conn);
    												if($result2){
    													foreach($result2 as $row2) {								   
    													?>									
    													
    													<tr id="TextBoxContainer" class="TextBoxContainer">
    
    																	<td style="vertical-align: middle"><span class="sno"><span class="icon-dots-three-vertical"></span></span></td>
    																	<td>
    																		<input type="text" class="ser form-control slot" name="services[]" value="<?=$row2['service_name']?>" placeholder="Service (Autocomplete)" required>
    																		<input type="hidden" name="service[]" value="<?=$row2['service_id']?>" class="serr" required>
    																		<input type="hidden" name="stock_id[]" value="<?= $row2['product_stock_batch']?>" class="stock_id" required>
    																		<input type="hidden" name="durr[]" value="<?=$row2['service_durr']?>" class="durr">
    																		<input type="hidden" name="pa_ser[]" value="<?= $row2['package_service_id'] != '0'?$row2['service'].'-'.$row2['package_service_id']:''?>" class="pa_ser"> 	
    																		<input type="hidden" name="item_row[]" value="<?=$row2['item_id']?>" />
    																</td>
    															
    														<td>
    															<input type="number" name="qt[]" min="1" class="positivenumber qt form-control sal slot" value="<?=($row2['quantity'])?$row2['quantity']:'1'?>">
    															<input type="hidden" name="package_id_ck[]" value="<?=$row2['package_id']?>" class="package_id_ck_update">
    														</td>
    														
    														<td>
    															<table class="inner-table-space">
    																<tr>
    																	<td>
    																		<input type="number"  value="<?=explode(",",$row2['disc_row'])[1]?>" name="disc_row[]" class="form-control disc_row positivenumber decimalnumber singleservicediscount" step="0.01" >
    																	</td>
    																	<td>
    																		<select class="form-control disc_row_type" name="disc_row_type[]" id="disc_row_type">
    																		    <option <?=(explode(",",$row2['disc_row'])[0] == CURRENCY)?'value="1" Selected':'value="1"'?>><?=CURRENCY?></option>
    																			<option <?=(explode(",",$row2['disc_row'])[0] == 'pr')?'value="0" Selected':'value="0"'?>>%</option>
    																		</select>
    																	</td>
    																</tr>
    															</table>	
    														</td>
    														
    														
    														<td class="spr_row"> 
    															
    															<?php 
    																$ii_id= $row2['ii_id'];					
    																$sql1="SELECT imsp.service_Provider,b.name,b.id b_id FROM `invoice_multi_service_provider` imsp  LEFT JOIN `beauticians` b on b.id=imsp.service_Provider where imsp.ii_id='$ii_id' and status='1' and imsp.branch_id='".$branch_id."'";
    																
    																$result1 = query_by_id($sql1,[],$conn);
    																foreach ($result1 as $row_2){	
    																?>
    																
    																<table id="add_row"><tbody><tr>
    																	<td id="select_row"><select name="staffid0[]" data-validation="required" class="form-control staff" required>
    																		<option value="">Service provider</option>          
    																		<?php 
																				$sql1="SELECT * FROM beauticians WHERE find_in_set('".explode(',',$row2['service_id'])[1]."', services) <> 0 AND active = '0' and type='2' and branch_id='".$branch_id."' ORDER BY name ASC";
																				$sql2="SELECT * FROM beauticians WHERE active = '0' and type='2' and branch_id='".$branch_id."' ORDER BY name ASC";
																				$result1 = query_by_id($sql1,[],$conn);
																				$result2 = query_by_id($sql2,[],$conn);
																				if($result1 && explode(',',$row2['service_id'])[0] == 'ser'){
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
    								
    														</td>
    														
    														<td>
    															<input type="number" class="pr form-control price positivenumber decimalnumber servicepriceafterdiscount" step="0.01" name="price[]" id="userName" placeholder="9800.00" value="<?=$row2['price']?>" > 
    															<?php if($row2['price'] > 0){ ?>
    																<input type="hidden" class="prr" name="actual_price[]" value="<?=str_replace(",","",$row2['actual_price'])?>">
    																<input type="hidden" class="rpoint" name="reward_point[]" value="<?=str_replace(",","",$row2['points'])?>">
    																<?php }else{ ?>
    																<input type="hidden" class="prr" value="0" name="actual_price[]">
    																<input type="hidden" class="rpoint" value="0" name="reward_point[]">
    															<?php } ?>
    														</td>
    														<?php } ?>
    													</tr>
    													
    												<?php } else {
    													?>
    													<tr id="TextBoxContainer" class="TextBoxContainer">
    
    															<td style="vertical-align: middle"><span class="sno"><span class="icon-dots-three-vertical"></span></span></td>
    															<td width="95%">
    																<input type="text" class="ser form-control slot" name="services[]" value="<?=$row2['service_name']?>" placeholder="Service (Autocomplete)" required>
    																<input type="hidden" name="service[]" value="" class="serr" required>
    																<input type="hidden" name="stock_id[]" value="0" class="stock_id" required>
    																<input type="hidden" name="durr[]" value="" class="durr">
    																<input type="hidden" name="pa_ser[]" value="" class="pa_ser">
    																<input type="hidden" name="item_row[]" value="" />
    															</td>
    														
    												<td>
    												<input type="number" name="qt[]" min="1" class="positivenumber qt form-control sal slot" value="<?=($row2['quantity'])?$row2['quantity']:'1'?>"></td>
    												<td>
    													<table class="inner-table-space">
    														<tr>
    															<td>
    																<input type="number"  value="0" name="disc_row[]" class="form-control disc_row positivenumber decimalnumber singleservicediscount" step="0.01" >
    															</td>
    															<td>
    																<select class="form-control disc_row_type" name="disc_row_type[]" id="disc_row_type">
    																    <option <?=(explode(",",$row2['disc_row'])[0] == CURRENCY)?'value="1" Selected':'value="1"'?> selected><?=CURRENCY?></option>
    																	<option <?=(explode(",",$row2['disc_row'])[0] == 'pr')?'value="0" Selected':'value="0"'?>>%</option>
    																</select>
    															</td>
    														</tr>
    													</table>
    												</td>
    												
    												
    												<td class="spr_row"> 
    													
    													
    													<table id="add_row"><tbody><tr>
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
    														</td></tr>
    													</tbody>
    													</table>
    												</td>
    												<input type="hidden" name="duration[]"  value="" class="duration">
    												<input type="hidden" name="ser_stime[]" value="" class="ser_stime">
    												<input type="hidden" name="ser_etime[]" value="" class="ser_etime">
    												<td>
    													<table>
    														<tr>
    															<td width="50%">
    																<input type="text" class="form-control start_time time" value="" placeholder="Start time" name="start_time[]" onchange="servicestarttime(this.value, $(this))" readonly>
    																</td>						<td>&nbsp;to&nbsp;</td>																										       <td width="50%">
    																<input type="text" class="form-control end_time"  name="end_time[]" value=""  placeholder="End time"  readonly>
    															</td>
    														</tr>
    													</table>														
    												</td> 
    												<td>
    													<input type="number" class="pr form-control price positivenumber decimalnumber servicepriceafterdiscount" step="0.01" name="price[]" readonly id="userName" placeholder="9800.00" value=""> 
    													<input type="hidden" class="prr" name="actual_price[]" value="">
    													<input type="hidden" class="rpoint" name="reward_point[]" value="">
    												</td>
    											</tr>
    													<?php
    												} }elseif(isset($_GET['ssbid']) && $_GET['ssbid']>0){
    												$ss_id =  $_GET['ssbid'];	
    												$sql2="SELECT ssmsp.ii_id,ssmsp.service_Provider,b.name,b.id b_id,ssi.client,ssi.service,ssi.quantity,ssi.disc_row,ssi.staffid,ssi.price,s.price as service_price,s.name as service_name,s.cat,CONCAT('sr,',s.id) as service_id,s.duration as service_durr,s.points,scat.cat as cat_name from `service_slip_items` ssi"
    												." LEFT JOIN `service` s on s.id=SUBSTRING_INDEX(ssi.service,',',-1)"
    												." LEFT JOIN `servicecat` scat on scat.id=s.cat"
    												." LEFT JOIN `service_slip_multi_service_provider` ssmsp on ssmsp.ii_id=ssi.id"
    												." LEFT JOIN `beauticians` b on b.id=ssmsp.service_Provider "
    												." where ssi.iid='$ss_id' and ssi.active=0 and ssi.type='Service' and ssi.branch_id='".$branch_id."' GROUP BY ii_id"
    												." UNION SELECT ssmsp.ii_id,ssmsp.service_Provider,b.name,b.id b_id,ssi.client,ssi.service,ssi.quantity,ssi.disc_row,ssi.staffid,ssi.price,s.price as service_price,s.name as service_name,'0' as cat,CONCAT('pr,',s.id) as service_id,'0' as service_durr,0 as points, 0 as cat_name from `service_slip_items` ssi"
    												." LEFT JOIN `products` s on s.id=SUBSTRING_INDEX(ssi.service,',',-1)"
    												." LEFT JOIN `service_slip_multi_service_provider` ssmsp on ssmsp.ii_id=ssi.id"
    												." LEFT JOIN `beauticians` b on b.id=ssmsp.service_Provider "
    												." where ssi.iid='$ss_id' and ssi.active=0 and ssi.type='Product' and ssi.branch_id='".$branch_id."'";
    												$result2=query_by_id($sql2,[],$conn);
    												if($result2){
    													foreach($result2 as $row2) {	   												   
    													?>									
    													
    													<tr id="TextBoxContainer" class="TextBoxContainer">
    														
    														
    																	<td style="vertical-align: middle"><span class="sno"><span class="icon-dots-three-vertical"></span></span></td>
    																	<td width="95%">
    																		<input type="text" class="ser form-control slot" name="services[]" value="<?=$row2['service_name']?>" placeholder="Service (Autocomplete)" required>
    																		<input type="hidden" name="service[]" value="<?=$row2['service_id']?>" class="serr" required>
    																		<input type="hidden" name="durr[]" value="<?=$row2['service_durr']?>" class="durr">
    																		<input type="hidden" name="pa_ser[]" value="" class="pa_ser">
    																		<input type="hidden" name="item_row[]" value="" />
    																</td>
    																
    													
    													<td>
    														<input type="number" name="qt[]" min="1" class="positivenumber qt form-control sal slot" value="<?=($row2['quantity'])?$row2['quantity']:'1'?>">
    													</td>
    													
    													<td>
    														<table class="inner-table-space">
    															<tr>
    																<td>
    																	<input type="number"  value="<?=($row2['disc_row'])?explode(",",$row2['disc_row'])[1]:'0'?>" name="disc_row[]" class="form-control disc_row positivenumber decimalnumber singleservicediscount" step="0.01" >
    																</td>
    																<td>
    																	<select class="form-control disc_row_type" name="disc_row_type[]" id="disc_row_type">
    																	    <option <?=(explode(",",$row2['disc_row'])[0] == CURRENCY)?'value="1" Selected':'value="1"'?>><?=CURRENCY?></option>
    																		<option <?=(explode(",",$row2['disc_row'])[0] == 'pr')?'value="0" Selected':'value="0"'?>>%</option>
    																	</select>
    																</td>
    															</tr>
    														</table>														
    													</td>
    													
    													
    													<td class="spr_row"> 
    														
    														<?php 
    															$ii_id= $row2['ii_id'];	
    															$sql1="SELECT ssmsp.service_Provider,b.name,b.id b_id FROM `service_slip_multi_service_provider` ssmsp  LEFT JOIN `beauticians` b on b.id=ssmsp.service_Provider where ssmsp.ii_id='$ii_id' and status='1' and ssmsp.branch_id='".$branch_id."'";
    															
    															$result1 = query_by_id($sql1,[],$conn);
    															foreach ($result1 as $row_2){
    																
    															?>
    															
    															<table id="add_row"><tbody><tr>
    																<td width="95%" id="select_row">                                                                                                                              <select name="staffid0[]" data-validation="required" class="form-control staff" required>
    																	<option value="">Service provider</option>          
																		<?php 
																			$sql1="SELECT * FROM beauticians WHERE find_in_set('".explode(',',$row2['service_id'])[1]."', services) <> 0 AND active = '0' and type='2' and branch_id='".$branch_id."' ORDER BY name ASC";
																			$sql2="SELECT * FROM beauticians WHERE active = '0' and type='2' and branch_id='".$branch_id."' ORDER BY name ASC";
																			$result1 = query_by_id($sql1,[],$conn);
																			$result2 = query_by_id($sql2,[],$conn);
																			if($result1 && explode(',',$row2['service_id'])[0] == 'ser'){
																				foreach ($result1 as $provider){
																				?>    
																				    <option <?=(($provider['id'] === $row_2['service_Provider'])?'value="'.$provider['id'].'"Selected':'value='.$provider['id']).''?>><?=$provider['name']?></option>
																			<?php } } else if($result2) { 
																			    foreach ($result2 as $provider){ ?>
																			        <option <?=(($provider['id'] === $row_2['service_Provider'])?'value="'.$provider['id'].'"Selected':'value='.$provider['id']).''?>><?=$provider['name']?></option>
																			<?php } } ?>
    																</select>
    																
    																
    																</td>
    															
    															</tr>
    															</tbody>
    															</table>
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
    														
    														<?php } ?>
    													</td>
    													
    													<td>
    														<input type="number" class="pr form-control price positivenumber decimalnumber servicepriceafterdiscount" step="0.01" name="price[]" id="userName" placeholder="9800.00" value="<?=$row2['price']?>" > 
    														<input type="hidden" class="prr" name="actual_price[]" value="<?=$row2['actual_price']?>">
    														<input type="hidden" class="rpoint" name="reward_point[]" value="<?=$row2['points']?>">
    													</td>
    													
    												</tr>
    												
    												
    											<?php }} }else{ ?>							
    											<tr id="TextBoxContainer" class="TextBoxContainer">
    
    															<td style="vertical-align: middle"><span class="sno"><span class="icon-dots-three-vertical"></span></span></td>
    															<td width="95%">
    																<input type="text" class="ser form-control slot" name="services[]" value="<?=$row2['service_name']?>" placeholder="Service (Autocomplete)" required>
    																<input type="hidden" name="service[]" value="" class="serr" required>
    																<input type="hidden" name="stock_id[]" value="0" class="stock_id" required>
    																<input type="hidden" name="durr[]" value="" class="durr">
    																<input type="hidden" name="pa_ser[]" value="" class="pa_ser">
    																<input type="hidden" name="item_row[]" value="" />
    															</td>
    														
    												<td>
    												<input type="number" name="qt[]" min="1" class="positivenumber qt form-control sal slot" value="<?=($row2['quantity'])?$row2['quantity']:'1'?>"></td>
    												<td>
    													<table class="inner-table-space">
    														<tr>
    															<td>
    																<input type="number"  value="0" name="disc_row[]" class="form-control disc_row positivenumber decimalnumber singleservicediscount" step="0.01" >
    															</td>
    															<td>
    																<select class="form-control disc_row_type" name="disc_row_type[]" id="disc_row_type">
    																    <option <?=(explode(",",$row2['disc_row'])[0] == CURRENCY)?'value="1" Selected':'value="1"'?> selected><?=CURRENCY?></option>
    																	<option <?=(explode(",",$row2['disc_row'])[0] == 'pr')?'value="0" Selected':'value="0"'?>>%</option>
    																</select>
    															</td>
    														</tr>
    													</table>
    												</td>
    												
    												
    												<td class="spr_row"> 
    													
    													
    													<table id="add_row"><tbody><tr>
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
    														</td></tr>
    													</tbody>
    													</table>
    												</td>
    												<input type="hidden" name="duration[]"  value="" class="duration">
    												<input type="hidden" name="ser_stime[]" value="" class="ser_stime">
    												<input type="hidden" name="ser_etime[]" value="" class="ser_etime">
    												<td>
    													<table>
    														<tr>
    															<td width="50%">
    																<input type="text" class="form-control start_time time" value="" placeholder="Start time" name="start_time[]" onchange="servicestarttime(this.value, $(this))" readonly>
    																</td>						<td>&nbsp;to&nbsp;</td>																										       <td width="50%">
    																<input type="text" class="form-control end_time"  name="end_time[]" value=""  placeholder="End time"  readonly>
    															</td>
    														</tr>
    													</table>														
    												</td> 
    												<td>
    													<input type="number" class="pr form-control price positivenumber decimalnumber servicepriceafterdiscount" step="0.01" name="price[]" id="userName" placeholder="9800.00" value=""> 
    													<input type="hidden" class="prr" name="actual_price[]" value="">
    													<input type="hidden" class="rpoint" name="reward_point[]" value="">
    												</td>
    											</tr>
    										<?php } ?>
    										<tr id="addBefore">
    											<td colspan="7"><button type="button" id="btnAdd" class="btn btn-success pull-right"><i class="fa fa-plus" aria-hidden="true"></i>Add service / product / package</button></td>
    										</tr>
    										<tr>
    											<td class="total" colspan="6">Subtotal</td>
    											<td><div id="sum" style="display: inline;"><?=($edit['subtotal'])?"<?= CURRENCY ?> ".$edit['subtotal']:'0'?></div>
    											<input type="hidden" id="sum2" value="<?=($edit['subtotal'])?$edit['subtotal']:'0'?>" name="subtotal"></td>
    										</tr>
    										<tr>
    											<td class="total" colspan="6">Coupon</td>
    											<td><input type="text" id="cc" value="<?=$edit['coupon_name']?>" name="coupon" class="key form-control" >
    												<input type="hidden" id="discount" name="discount" value="<?=($edit['discount'])?$edit['discount']:'0'?>">
    												<input type="hidden" id="discount_type" name="discount_type" value="<?=($edit['discount_type'])?$edit['discount_type']:'0'?>">
    												<input type="hidden" id="ccid" name="ccid" value="<?=($edit['coupon_id'])?$edit['coupon_id']:'0'?>">
    												<input type="hidden" id="cmin" name="cmin" value="<?=($edit['min_amount'])?$edit['min_amount']:'0'?>">
    												<input type="hidden" id="cmax" name="cmax" value="<?=($edit['max_amount'])?$edit['max_amount']:'0'?>">
    												<input type="hidden" id="valid" name="valid" value="0">
    												<input type="hidden" id="c_per_user_used" name="c_per_user_used" value="0">
    												<input type="hidden" id="c_per_user" name="c_per_user" value="0">
    											</td>
    										</tr>
    										<!--<tr>
    											<td class="total" colspan="5">Reward points used</td>
    											<td><input type="text" id="sum" name="subtotal" class="key form-control" readonly></td>
    										</tr>-->
    										<tr>
    											<td class="total" colspan="5">Discount</td>
    											<td>
    											<input type="number" step="0.01" min="0" class="key1 form-control" name="dis" id="total_disc"  value="<?=($edit['total_discount'])?explode(",",$edit['total_discount'])[1]:'0'?>" placeholder="Discount Amount"></td>
    											<td>
    												<select class="form-control total_disc_row_type" name="total_disc_row_type" id="disc_row_type">
    													<option <?=(explode(",",$edit['total_discount'])[0] == CURRENCY)?'value="1" Selected':'value="1"'?>><?=CURRENCY?></option>
    													<option <?=( explode(",",$edit['total_discount'])[0] == 'pr')?'value="0" Selected':'value="0"'?>>%</option>
    												</select>
    											</td>
    										</tr>
    										<tr>
    											<td class="total" colspan="5">Taxes</td>
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
    														<option <?=((($edit['tax_type']) == ($row2['id'].',1'))?'value="'.$row2['id'].','.$row2['tax'].',1"Selected':'value="'.$row2['id'].','.$row2['tax'].',1"')?>><?php echo $row2['title']; ?></option>
    													<?php } ?>
    												</optgroup>              
    											</select></td>
    										</tr>
    										<tr>
    											<td class="total" id="tot" colspan="6">Total</td>
    											<td><input type="text" id="total" class="form-control" name="total" placeholder="Total Amount" value="<?=($edit['total'])?$edit['total']:'0'?>" readonly></td>
    										</tr>
    										<tr>
    											<td class="total"  colspan="5">Referral Code (Optional)</td>
    											<td colspan="2"><input type="text" id="referral_code" class="form-control" name="referral_code" placeholder="XXXXXXXX" value="<?=($edit['referral_code'])?$edit['referral_code']:''?>"></td>
    										</tr>
    										<tr>
    											<td class="total" colspan="6">Give reward point</td>
    											<td>
    												<label><input type="radio" name="givepoint" value="1" <?= !isset($edit['givepoint'])?'checked':'' ?> <?=($edit['givepoint'])&&$edit['referral_code']=='1'?'checked':''?> /> Yes</label>&nbsp;&nbsp;&nbsp;
    												<label><input type="radio" name="givepoint" value="0" <?=($edit['givepoint'])&&$edit['referral_code']=='0'?'checked':''?> /> No</label>
    											</td>
    										</tr>
    										<tr>
    											<td class="total" colspan="6">Advance received</td>
    											<td>
    												<input type="text" name="adv" class="key form-control" id="adv" placeholder="0" value="<?=($edit['paid'])?$edit['paid']:$edit['advance']?>" readonly>
    											</td>
    										</tr>
    										<tr>
    											<td class="total" colspan="6">Amount payable</td>
    											<td id="pend"><?=($edit['total'])?$edit['total']:'0'?></td>
    										</tr>
    										<tr class="payment_method_TextBoxContainer" id="payment_method_TextBoxContainer">
    											<td class="total" colspan="4">Amount paid <br /><span class="text-danger" id="red">*Reward points:- <?php redeempoint() ?> points = <?php redeemprice() ?> <?= CURRENCY ?>.</span></td>
    											<td colspan="3" class="spr_row_payment">
    												<?php
    												 if(count($edit) > 0 && isset($_GET['beid'])) { 
    													$payments = query_by_id("SELECT * FROM multiple_payment_method WHERE invoice_id = 'bill,$beid' and status = 1 and branch_id='".$branch_id."'",[],$conn);
    													if($payments){
    													    $count = 1;
    														foreach ($payments as $pay) {
    														?>
    														<?php if($count == 1){ ?>
    														<table class="inner-table-space pay_methods" style="width:100%;" id="pay_methods"> 
    														<?php } else { ?>
    														<table class="inner-table-space pay_methods">
    														<?php } ?>
    														<tr>
    															<td width="280"><input type="text" name="transid[]" class="key form-control transid" id="transctionid" value="<?=($pay['transaction_id']) ? $pay['transaction_id'] : '0'?>" placeholder="TXN ID"></td>
    															<td><input type="number" name="paid[]" step="0.01" class="key form-control paid" id="paid" value="<?=($pay['amount_paid'])?$pay['amount_paid']:'0'?>" min="0"></td>
    															<td><select name="method[]" data-validation="required" class="form-control act" onchange="paymode(this.value,$(this))">
    																<!--<option value="">--Select--</option>-->
    																<?php $sql_pay_mode="Select * FROM `payment_method` where status='1'";
    																	$result_pay_mode =query_by_id($sql_pay_mode,[],$conn);
    																	foreach($result_pay_mode as $row_pay_mode){
    																	?>
    																	    <option <?=(($pay['payment_method'] === $row_pay_mode['id'])?'value="'.$row_pay_mode['id'].'" Selected':'')?> value="<?=$row_pay_mode['id']?>"><?=$row_pay_mode['name']?></option> 
    																    <?php } ?>  
    															</select></td>
    															<?php if($count == 1){ ?>
        														    <td id="plus_button_payment" width="5%">
        																<span class="input-group-btn">
        																	<button style="" class="btn btn-add btn-plus btn-success btn-add add_spr_row_payment" type="button">
        																		<span class="glyphicon-plus"></span>
        																	</button>
        																</span>
    															    </td>
        														<?php } else { ?>
        														    <td id="minus_button" width="5%">
        																<span class="input-group-btn">
        																	<button onclick="$(this).parent().parent().parent().parent().parent().remove();sumup();" style="" class="btn btn-danger btn-remove btn_remove" type="button">
        																		<span class="glyphicon-minus"></span>
        																	</button>
        																</span>
        															</td>
        														<?php } ?>
    														</tr>
    														</table>
    												<?php $count++; } } else { ?>
    												<table class="inner-table-space pay_methods" style="width:100%;" id="pay_methods">
    												    <tr>
    												    	<td width="280"><input type="text" name="transid[]" class="key form-control transid" id="transctionid" value="" placeholder="TXN ID"></td>
    														<td><input type="number" name="paid[]" step="0.01" class="key form-control paid" id="paid" value="0.00" min="0"></td>
    														<td><select name="method[]" data-validation="required" class="form-control act" onchange="paymode(this.value,$(this))">
    															<!--<option value="">--Select--</option>-->
    															<?php $sql_pay_mode="Select * FROM `payment_method` where status='1'";
    																$result_pay_mode =query_by_id($sql_pay_mode,[],$conn);
    																foreach($result_pay_mode as $row_pay_mode){
    																?>
    																<option value="<?=$row_pay_mode['id']?>"><?=$row_pay_mode['name']?></option> 
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
    													</table>
    												   <?php } } else { ?>
    												   <table class="inner-table-space pay_methods" style="width:100%;" id="pay_methods">
    													<tr>
    														<td width="280"><input type="text" name="transid[]" class="key form-control transid" id="transctionid" value="" placeholder="TXN ID"></td>
    														<td><input type="number" name="paid[]" step="0.01" class="key form-control paid" id="paid" value="" min="0"></td>
    														<td><select name="method[]" data-validation="required" class="form-control act" onchange="paymode(this.value,$(this))">
    															<!--<option value="">--Select--</option>-->
    															<?php $sql_pay_mode="Select * FROM `payment_method` where status='1'";
    																$result_pay_mode =query_by_id($sql_pay_mode,[],$conn);
    																foreach($result_pay_mode as $row_pay_mode){
    																?>
    																<option value="<?=$row_pay_mode['id']?>"><?=$row_pay_mode['name']?></option> 
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
    												</table>
    											<?php } ?> 
    										</td>
    										</tr>
    										<tr>
    											<input type="hidden" class="remaining_service_worth" value="0">
    											<input type="hidden" class="used_service_worth" name="used_service_worth" value="">
    											<td class="total" colspan="6">Amount due/credit</td>
    											<td id="due">0</td>
    											<input type="hidden" id="invoice_wallet_amount" name="invoice_wallet_amount" value="0">
    										</tr>
    										<tr>
    											<td colspan="7"><textarea name="notes" class="form-control no-resize" rows="5" placeholder="Write notes about billing here..." id="textArea"><?= $edit['notes']; ?></textarea></td>
    										</tr>
    										<!--<tr>
    											<td class="total" colspan="5">Return</td>
    											<td><input type="number" class="form-control" name="chnge" id="chng" value="0"></td>
    										</tr>-->
    										
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
    						    
    							<?php if(isset($_GET['beid']) && $_GET['beid']>0)
    							{ ?>
    								<button type="submit" name="update-bill" class="btn btn-info pull-right mr-left-5"><i class="fa fa-pencil-square-o" aria-hidden="true"></i>Update Bill</button>
    							<?php }else { ?>
    								<button type="submit" name="submit-bill" class="btn btn-success pull-right mr-left-5"><i class="fa fa-money" aria-hidden="true"></i>Create Bill</button>
    							<?php } ?>
    						</div>
    					
    				        </div>
    				</div>
    			</div>
    		</div>
    		</div>
    		<div class="data-card" data-id="wallet_tab">
    		    <div class="col-lg-9 col-md-12 col-sm-12 col-xs-12">
        	        <div class="panel">
					<div class="panel-heading">
						<h4>Add wallet amount</h4>
					</div>
					<div class="panel-body">
						<div class="row">
							<form action="" method="post">
    							 <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
    								<div class="form-group">
    									<label for="date">Date <span class="text-danger">*</span></label>
    									<?php $date = date('Y-m-d'); ?>
    									<input type="text" class="form-control date" value="<?=($edit['doa'])?$edit['doa']:$date?>" name="date_wallet" readonly required>
    								</div>
    							</div>
								<div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
									<div class="form-group">
										<label for="name">Client name <span class="text-danger">*</span></label>
										<input type="text" class="form-control" id="client_name_wallet" name="name_wallet" placeholder="Client Name" value="<?= isset($id)?$edit['client_name']:''?>"   required>
										<input type="hidden" id="client_id_wallet" name="client_id_wallet" value="<?= isset($id)?$edit['client_id']:''?>"> 
										<input type="hidden" name="client_branch_id_wallet" id="client_branch_id_wallet" value="<?= isset($id)?$edit['client_branch_id']:''?>" class="clt">
										<span style="color:red" id="client-status-wallet"></span>
									</div>
								</div>
								<div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
									<div class="form-group">
										<label for="cont">Contact number<span class="text-danger">*</span></label>
										<input type="text"  onkeyup="this.value = this.value.replace(/[^0-9\.]/g,'');" maxLength="<?= PHONE_NUMBER ?>" class="form-control" onBlur="check()" name="cont_wallet" id="cont_wallet" placeholder="Contact" value="<?= isset($id)?$edit['cont']:''?>" readonly required>
									</div>
								</div>
								
								<div class="clearfix"></div>
								
								<div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
									<div class="form-group">
										<label for="paid_amount">Amount paid <span class="text-danger">*</span></label>
										<input type="number" step="0.01"  onkeyup="this.value = this.value.replace(/[^0-9\.]/g,'');" maxLength="<?= PHONE_NUMBER ?>" class="form-control" onBlur="check()" name="paid_amount_wallet" placeholder="Amount" value="<?= isset($id)?$edit['paid_amount']:''?>" required>
										
									</div>
								</div>
								<div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
									<div class="form-group">
										<label for="payment_method">Payment mode <span class="text-danger">*</span></label>
										<select name="payment_method_wallet" data-validation="required" required class="form-control act">
													<?php $sql_pay_mode="Select * FROM `payment_method` where status='1'";
														$result_pay_mode =query_by_id($sql_pay_mode,[],$conn);
														foreach($result_pay_mode as $row_pay_mode){
														if(($row_pay_mode['id'] == '7') || ($row_pay_mode['id'] == '9')){ continue; } else { ?>
														    <option <?=(($edit['payment_method'] === $row_pay_mode['id'])?'value="'.$row_pay_mode['id'].'" Selected':'')?> value="<?=$row_pay_mode['id']?>"><?=$row_pay_mode['name']?></option> 
														<?php } } ?>  
												</select>
									</div>
								</div>
								<div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
									<div class="form-group">
										<label for="wallet_amount">Amount to be credit <span class="text-danger">*</span></label>
										<input type="number" step="0.01" onkeyup="this.value = this.value.replace(/[^0-9\.]/g,'');" maxLength="<?= PHONE_NUMBER ?>" class="form-control" onBlur="check()" name="wallet_amount" placeholder="Amount" value="<?= isset($id)?$edit['wallet_amount']:''?>" required>
									</div>
								</div>
								<div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
									<div class="form-group">
										<label for="send_receipt">Send receipt:</label>
										<label class="checkbox-inline"><input type="checkbox" checked name="send_receipt">Send the deposit receipt to customer?</label>
									</div>
								</div>	
									<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
										<div class="form-group">
											
											<?php if(isset($id)) { ?>
												<button type="submit" name="edit-submit-wallet" class="btn btn-info pull-right"><i class="fa fa-pencil-square-o" aria-hidden="true"></i>Update wallet</button>
											<?php } else { ?>	
												<button type="submit" name="wallet_submit" class="btn btn-success pull-right"><i class="fa fa-plus" aria-hidden="true"></i>Add wallet</button>
											<?php } ?>		
										</div>
									</div>
								</form>
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
    									<!-- <tr>
    										<td>Available packages:</td>
    										<td id="available-package">
    											<select id="av_package" class="form-control">
    												<option value="">--Select--</option>
    											</select>
    										</td>
    									</tr> -->
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
    	</form>
    	
	<div class="data-card" data-id="gift_card_tab">
	    <?php include_once('gift-cards.php'); ?>
	</div>
</div>

</div>


<!-- Main container ends -->
</div>
<!-- Dashboard Wrapper End -->
</div>
<!-- Container fluid ends -->

<!-- Wallet_Modal -->
<div class="modal fade" id="wallet" role="dialog">
	<div class="modal-dialog">
		
		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">Wallet</h4>
			</div>
			<div class="modal-body">
				<table class="table table-bordered">
					<tr>
						<thead>
							<th>Client name</th>
							<th>Contact </th>
							<th>Wallet amount</th>
							<th></th>
						</thead>
						
					</tr>
					<tbody>
						<tr> 
							<td id="wallet_client_name"></td>
							<td id="wallet_cont"></td>
							<td class="wallet_amount"></td>
							<td></td>
						</tr>     
					</tbody>
					<tr><td></td><td>Bill total</td><td id="wallet_bill"></td><td  rowspan="2"><button style="background-color: #4cbe71;border-color: #4cbe71;color: #ffffff;"class="btn btn-success btn-xs use" type="button" onClick="use_wallet_pay()" >Pay</button></td><input type="hidden" class='index_id'></tr>
				</table>
				
			</div>
			<br>
			<div class="modal-footer">
				
			</div>
		</div>
	</div>
</div>
<!--- End-wallet-modal --->

<!-- Bill success modal -->
<div class="modal fade in disableOutsideClick" id="bill_options" role="dialog">
	<div class="modal-dialog modal-lg">
		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header">
			    <button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">Bill options</h4>
			</div>
			<div class="modal-body">
			    <div class="row">
			        <div class="col-md-12">
			            <div id="b_status"><div class="alert alert-success">Bill created successfully.</div></div>
			        </div>
			    </div>
				<div class="row">
				    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-6">
				        <button type="button" id="print_bill" class="btn btn-dark btn-block"><i class="fa fa-print" aria-hidden="true"></i>Print Bill</button>
				    </div>
				    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-6">
				        <button type="button" id="sms_bill" class="btn btn-dark btn-block"><i class="fa fa-paper-plane-o" aria-hidden="true"></i>SMS Bill</button>
				    </div>
				    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-6">
				        <button type="button" id="email_bill" class="btn btn-dark btn-block"><i class="fa fa-envelope-o" aria-hidden="true"></i>Email Bill</button>
				    </div>
				</div>
				<hr>
				<div class="row">
				    <div class="col-md-12">
				        <div id="product_usage">
				            <h4>Enter product usage (Optional)</h4>
				            <form class="table-responsive" id="consumption_form">
				                <table class="table table-responsive table-bordered">
				                <thead>
				                    <tr>
				                        <td width="25%">Service name</td>
				                        <td width="30%">Product name</td>
				                        <td width="10%">Quantity</td>
				                        <td width="10%">Unit</td>
				                        <td width="20%">Used by</td>
				                        <td width="5%"></td>
				                    </tr>
				                </thead>
				                <tbody id="cons_table">
				                    
				                </tbody>
				            </table></form>
				            <button type="button" style="margin-left: 5px;" class="btn btn-danger pull-right" data-dismiss="modal"><i class="fa fa-close" aria-hidden="true"></i>Close</button>
				            <button type="button" class="btn btn-success pull-right" id="usage_submit"><i class="fa fa-floppy-o" aria-hidden="true"></i>Save</button>
				        </div>
				    </div>
				</div>
			</div>
		</div>
	</div>
</div>
<!-- Bill success modal end-->
<?php include "footer.php";	
	
	function payment_mode($iid){
		global $conn;
		global $branch_id;
		$payment_method = '' ;
		$sql = "SELECT pm.name as payment_method FROM `multiple_payment_method` mpm LEFT JOIN payment_method pm on pm.id=mpm.payment_method WHERE mpm.invoice_id='bill,$iid' and mpm.status=1 and mpm.branch_id='".$branch_id."'";
		$result = query_by_id($sql,[],$conn);
		if($result){
			foreach($result as $row){
				$payment_method .= $row['payment_method'].'<br>';
			}
			}else{
			
			$payment_method;
		}
		return $payment_method;
	}
	
?>
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
                    sumup();
                });
            }  
        }
    } 
}); 
$("#total_disc").keypress(function() {
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
    sumup();
    }
});


// special discount division end


	/*******Server_side_datatable*********/

		// multiple payment method

			$(document).on("click",".add_spr_row_payment", function(){
				
				var empty = 0;

				if($('#total').val() == 0){
					empty += 1;
				} else {
					$('.paid').each(function(){
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
					var amount_div = $(this).parent().parent().parent().parent();
					var td_clone=$("#payment_method_TextBoxContainer").find('.spr_row_payment').children('table#pay_methods').clone().addClass('pay_methods');
					td_clone.removeAttr('id');
					td_clone.find('td#plus_button_payment').remove();
					td_clone.find('.paid').val(0);
					td_clone.find('tr').append('<td id="minus_button"><span class="input-group-btn"><button onclick="$(this).parent().parent().parent().parent().parent().remove();sumup();" class="btn btn-danger btn-remove btn_remove" type="button"><span class="glyphicon-minus" "></span></button></span></td>');
					var l_row=$(this).parents(".payment_method_TextBoxContainer").find('.spr_row_payment');
					l_row.children('table:last').after(td_clone);	
					l_row.children('table.pay_methods').first().find('.act option[value=""]').attr('selected','selected');  
					var staff_name=$(this).parents(".payment_method_TextBoxContainer").find('.act').attr('name');
					$(this).parents(".payment_method_TextBoxContainer").find('.act').attr('name',staff_name);
					price_change();
				}
			});
			
			
			// function to use package service

        	function usePackageService(service_id, row_id, e){
        		var ser = e.parents('.TextBoxContainer').find('.pa_ser').val();
        		if(ser != ''){
        			$('#avpackageModal').modal('hide');
        		} else {
        			e.parents('.TextBoxContainer').find('.pa_ser').val(service_id+'-'+row_id);
        			e.parents('.TextBoxContainer').find('.pr').val('0');
        			e.parents('.TextBoxContainer').find('.qt').prop('readonly', true);
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
				$('.pay_methods .act').each(function(){
					var selected_options = $(this).val();
					if(mode_id == selected_options){
						options += 1;
					}
				});
				if(options > 1){
					toastr.warning('Payment option is already selected.');
					modeDiv.parent().parent().parent().parent().remove();
					$('#payment_method_TextBoxContainer table:first-child .input-group-btn').html('<button style="" class="btn btn-add btn-plus btn-success btn-add add_spr_row_payment" type="button"><span class="glyphicon-plus"></span></button>');
					$('#payment_method_TextBoxContainer table:first-child').attr('id','pay_methods');
					$('#payment_method_TextBoxContainer table:first-child .input-group-btn').parent().attr('id','plus_button_payment');
					$('#payment_method_TextBoxContainer table:first-child').removeClass('pay_methods');
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
						modeDiv.find('.paid').val('0');
					} 
					else {
						
						var price_cal = parseInt(wallet_money);
						if(totalVal < wallet_money){
							modeDiv.find('.paid').val(parseFloat($('#due').text()));
						} else {
							modeDiv.find('.paid').val(price_cal);
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
						modeDiv.find('.paid').val('0');
						toastr.warning('Don\'t have any reward point.');
						modeDiv.find('.act').val('1');
						sumup();
					} else {
						
						var point;
						var point_price = <?= redeemprice() ?>;
						var redeem_point = <?= redeempoint() ?>;
						var pprice = parseFloat($('#due').text());
						if(reward_point > <?= maxredeempoint() ?>){
							point = <?= maxredeempoint() ?>;
						} else {
							point = reward_point;
						}
						var price_cal = (parseInt(point)/parseInt(redeem_point))*parseInt(point_price);
						
						if(pprice > price_cal){
							modeDiv.find('.paid').val(price_cal); 
						} else {
							modeDiv.find('.paid').val(pprice);
						}
						sumup();
					}
				}
			} else {
				modeDiv.find('.paid').val('0');
				sumup();
			}
		}
			
			// Add_payment_method_row 

			$(document).on("click",".add_payment_method_row", function(){
				var td_clone=$(this).parents(".payment_method_TextBoxContainer").find('table#payment_method_add_row').clone().addClass('payment_method_add_row');
				td_clone.removeAttr('id');
				td_clone.find('.pay_method option[value=""]').prop('selected',true); 
				td_clone.find('td#plus_button').remove();
				
				td_clone.find('tr').append('<td id="minus_button width=5%"><span class="input-group-btn"><button onclick="remove_comm($(this));$(this).parent().parent().parent().parent().parent().remove();sumup();" class="btn btn-danger btn-remove btn_remove" type="button"><span class="glyphicon-minus" "></span></button></span></td>');
				
				var l_row=$(this).parents(".payment_method_TextBoxContainer").find('table:last');
				l_row.after(td_clone);
				
				// Cloning of amount paid text field

				var td_clone1=$(this).parents(".payment_method_TextBoxContainer").find('.comm_row').children("table#comm_tab_row").clone().addClass('comm_tab_row');
				td_clone1.removeAttr('id');
				td_clone1.find('.paid').val("0").removeAttr('readonly',true);
				var l_row1=$(this).parents(".payment_method_TextBoxContainer").find('.comm_row');
				l_row1.children('table:last').after(td_clone1);
				
				// End
				price_change();
			});
			// End
			
			//function to remove amount_paid_textfield rows
			function remove_comm(e){
				var com_tab_index = 0;
				var i;
				var serve_pro=e.parents('table').index();
				var comm_tab =e.parents("tr.payment_method_TextBoxContainer").find('.comm_row').children("table").eq(serve_pro).remove();
				
				
			}
			
			$(document).ready(function(){
			
				$('#referral_code').on('blur',function(){
					var client_id = $('#clientid').val() ;
					var referral_code = $(this).val();
					if(referral_code != ''){
    					$.ajax({
    						url: "ajax/checkReferralCode.php?referral_code="+referral_code+"&client_id="+client_id,
    						type: "POST",
    						success:function(data){
    							 var jd = JSON.parse(data);
    							 if(jd['status'] === '1'){
    								 $('#referral_code').val("");
    								 toastr.error("Invalid code");
    							 }else if(jd['status'] === '2'){ 
    								 $('#referral_code').val("");
    								 toastr.warning("Code already used");
    							 }
    							
    							
    						},
    						error:function (){}
    					});
					}
				});
				var refresh = window.localStorage.getItem('refresh');
				if (refresh==1){
					window.location.reload();
					window.localStorage.setItem('refresh','1');
					window.localStorage.removeItem("refresh");
				}
			});
			
			// $(document).on("click", '#date', function() {
			// 	$('.staff option[value=""]').prop('selected',true);	
			// 	$('.ser').val("");
			// 	$('.start_time').val("");
			// 	$('.end_time').val("");
			// 	$('.ser_stime').val("");
			// 	$('.ser_etime').val("");
			// 	$('.prr').val("");
			// 	$('.pr ').val("");
			// 	$('.serr').val("");
			// 	$('.disc_row').val("");
			// });
			
			$(document).on("blur", '#date', function() {
			    if($('#clientid').val() != ''){
				    client_check_membership_availability($('#clientid').val());
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
			
			$(document).on("change", '.staff', function() {
				staff = $(this).val();
				findDuplicate($(this)); //call diplicate element function
			});
			
			
			// <!------function to find duplicate service_provider---->
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
			function findDuplicate_payment_mode(e){
				duplicate_arr=[];
				var row=$('.payment_method_row').children('table');
				row.each(function(){
					duplicate_arr.push($(this).find('.pay_method').val());
				});
				for(var i=0;i<duplicate_arr.length;i++){
					for(var j=i+1;j<duplicate_arr.length;j++){
						if(duplicate_arr[i]==duplicate_arr[j]){
							remove_comm(e);
							e.parent().parent().parent().parent().remove();
						}  
					}
				}
			}
			
	
			$(window).on('load', function(){
				<?php if(isset($_GET['beid']) || isset($_GET['ssbid']) || isset($_GET['bid'])) {?>
					var clientID=$('#clientid').val();
					clientView(clientID);
				<?php } ?>
				/******Redeem_point_radio_button******/
					var atr=$('.chargable_radio').find('.yes').attr('previousvalue');
					if(atr == 'true' || atr == 'checked'){
						$('.chargable_radio').find('.yes').prop('checked',true);
					}
					else if(atr == 'false'){
						$('.chargable_radio').find('.no').prop('checked',true);
					}
					/******End******/
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
								$(".TextBoxContainer").eq(l).find('.sno').html('<span class="remm icon-trash2 " style="color:red;" onclick="sumup();$(this).parent().parent().parent().remove();increment_ids();membershipDiscounts();"></span>');
								j++;
							});
							j=1;
							k++;
							l++;
						});
						var pm=1;
						$('.payment_method_row').find('table').each(function(){
							$('.comm_row').find('table').eq(pm).removeAttr('id');
							$('.comm_row').find('table').eq(pm).addClass('payment_method_add_row');
							$('.payment_method_row').find('table').eq(pm).removeAttr('id');
							$('.payment_method_row').find('table').eq(pm).addClass('payment_method_add_row');
							$('.payment_method_row').find('table').eq(pm).find('td#plus_button').remove();
							$('.payment_method_row').find('table').eq(pm).find('tr').append('<td><span class="input-group-btn"><button onclick="remove_comm($(this));$(this).parent().parent().parent().parent().parent().remove();sumup();"   class="btn btn-danger btn-remove btn_remove" type="button"><span class="glyphicon-minus" "></span></button></span></td>');
							$(".TextBoxContainer").eq(l).find('.sno').html('<span class="remm icon-trash2 " style="color:red;" onclick="remove_comm($(this));$(this).parent().parent().parent().parent().parent().remove();sumup();" class="btn btn-danger btn-remove btn_remove"></span>');
							pm++;
						});
						
						var row_len1=$('.TextBoxContainer');
						var i=0;
						row_len1.each(function(){
							var a=$(this).find('.staff').attr('name','staffid'+i+'[]');
							i++; 
							
							var service_row= $(this).find('.serr').val();
							var staff_row  = $(this).find('.spr_row').children('table').find('.staff');
							
							if((service_row).split(',')[0] == 'pr' || (service_row).split(',')[0] =='pa' ){
								staff_row.removeAttr('required');
								}else{
								staff_row.attr('required','true');
							}
							var package_id_ck_update=$(this).find('.package_id_ck_update').val();
							if(package_id_ck_update > 0){
								$(this).find('.qt').attr('readonly',true);
							}
						});
						sumup($('#earned_points').val());
						<?php if($_GET['beid']){ ?>
							var client_id = $('#clientid').val() ;
							var invoice_id = <?=$_GET['beid']?> ;
							var pm = $('.payment_method_row').children('table');
							pm.each(function(){
								if($(this).find('.pay_method').val() =='8'){
									pm_index=$(this).index();
								}
							});
							client_check_membership_availability(client_id);
						<?php } else if($_GET['bid']){ ?>
						    var client_id = $('#clientid').val();
						    client_check_membership_availability(client_id);
						<?php } ?>
						
						check();
						formValidaiorns();
					});
					
					$(document).on("click",".add_spr_row", function(){
						var td_clone=$("#TextBoxContainer").find('.spr_row').children('table#add_row').clone().addClass('add_row');
						td_clone.removeAttr('id');
						td_clone.find('td#plus_button').remove();
						td_clone.find('tr').append('<td id="minus_button"><span class="input-group-btn"><button onclick="$(this).parent().parent().parent().parent().parent().remove();" class="btn btn-danger btn-remove btn_remove" type="button"><span class="glyphicon-minus" "></span></button></span></td>');
						var l_row=$(this).parents(".TextBoxContainer").find('.spr_row');
						l_row.children('table:last').after(td_clone);
						
						l_row.next().children('table.add_row').find('.staff option[value=""]').prop('selected',true); 	
						l_row.children('table.add_row').find('.staff option[value=""]').attr('selected','selected'); 
						
						var staff_name=$(this).parents(".TextBoxContainer").find('.staff').attr('name');
						$(this).parents(".TextBoxContainer").find('.staff').attr('name',staff_name); 
					});
					
					$(function(){
						autocomplete_serr();
						barcode_scanner();
						autocomplete_serr_cat();
						price_change();
						change_event();
						formValidaiorns();
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
								$('#aniv').val(ui.item.anniversary);
								$('#dob').val(ui.item.dob);
								$('#gst').val(ui.item.gst);
								$('#cc').val('');
								$('#earned_points').val("");
								clientView(ui.item.id);
								/*******Client_REWARD_POINTS********/
									$.ajax({
										url : "ajax/client_reward_points.php?client_id="+ui.item.id+"&referral_code="+ui.item.referral_code,
										type: "POST",
										success:function(data){
											var obj = $.parseJSON(data);
											if(parseFloat(obj['reward_points']) > 0){
												$('#earned_points').val(obj['reward_points']); 
											}
										}	
									});
									
									
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
				    
									$('.chargable_radio').find('.no').attr('previousValue', 'checked');
									$("#client-status").html("");
									$('.pay_method option[value="1"]').prop('selected',true); 
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
                					
                					
                					
                					if(empty_fields && empty_fields.length == 0){
    									var clonetr = $("#TextBoxContainer").clone().addClass('TextBoxContainer');
    									clonetr.removeAttr('id');
    									clonetr.find("table.add_row").remove();
    									clonetr.find('.sno').html('<span class="remm icon-trash2 " style="color:red;" onclick="$(this).parent().parent().parent().remove();sumup();increment_ids();membershipDiscounts();"></span>');
    									clonetr.find('input').val('');
    									clonetr.find('.disc_row_type option').prop('disabled',false);
    									clonetr.find('.disc_row').prop('readonly',false);
    									clonetr.find('.staff option[value=""]').prop('selected',true);
    									$("#addBefore").before(clonetr);
    									autocomplete_serr_cat();
    									autocomplete_serr();
    									barcode_scanner();
    									price_change();
    									//funcClass();
    									change_event();
    									increment_ids();
    									$('.TextBoxContainer').last().children().find('.qt').removeAttr('readonly');
    									$('.TextBoxContainer').last().children().find('.package_service_quantity').remove();
    									$('.TextBoxContainer').last().children().find('.package_service_inv').remove();
    									$('.TextBoxContainer').last().children().find('.package_id_ck_update').remove();
    									
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
							
							/*******redeem_points_radio_button*******/
								$(".yes_lab,.yes").on('click',function(){ 
									
									var row=$(this).parents('.chargable_radio');
									var previousValue=row.find('.yes').attr('previousValue');
									var name = row.find('.yes').attr('name');
									$("input[name='"+name+"']:radio").attr('previousValue', false);
									row.find('.yes').attr('previousValue', 'checked');
									row.find('.yes').prop("checked",true);
									row.find('.no').removeAttr('checked');
									
									var earned_points=$("#earned_points").val();
									if(earned_points != '0'){
										sumup(earned_points);
									}
									
									
								});	
								$(".no_lab,.no").on('click',function(){ 
									
									var row=$(this).parents('.chargable_radio');
									var previousValue=row.find('.no').attr('previousValue');
									var name = row.find('.no').attr('name');
									
									$("input[name='"+name+"']:radio").attr('previousValue', false);
									row.find('.no').attr('previousValue', 'checked');
									row.find('.no').prop("checked",true); 
									row.find('.yes').removeAttr('checked');
									
									$(this).parents(".TextBoxContainer").find('.pr').val(0);
									sumup();
									
								});
								/*******END*******/
									
									
									// increment_id's function

									function increment_ids(){
										var row_len=$('.TextBoxContainer');
										var i=0;
										row_len.each(function(){
											var a=$(this).find('.staff').attr('name','staffid'+i+'[]');
											i++; 
										});
									}
									
									// End code
									
									$(document).on("click input", '.start_time:eq(0)', function() {
										var s_time=$('.start_time:eq(0)').val();
										if(s_time !=""){ 
										    servicestarttime(this.value, $(this));
										}
									});
									
									function addZero(i) {
                        				if (i < 10) {
                        					i = "0" + i;
                        				}
                        				return i;
                        			}
                        			
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
									    var array_elements = [];
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
												var row = $(this).parent().parent();
												// var childrow = $(this).parent().parent();
												row.find('.serr').val(ui.item.id);
												row.find('.prr').val(ui.item.price);
												row.find('.stock_id').val(ui.item.stock_id);
												row.find('.rpoint').val(ui.item.points);
												row.find('.qt').val('1');
												row.find('.qt').prop('readonly', false);
												row.find('.disc_row ').val('0');
												row.find('.duration').val(ui.item.duration);
												row.find('.ser_stime').val(ui.item.ser_stime);
												row.find('.ser_etime').val(ui.item.ser_etime);
												row.find('.start_time').val((ui.item.ser_stime).substring(11));
												row.find('.end_time').val((ui.item.ser_etime).substring(11));
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
												var product_stock_count = -1;
												var pid = null;
												if((ui.item.id).split(',')[0] == 'pr' || (ui.item.id).split(',')[0] =='pa'){
													row.find('.staff').removeAttr('required');
													$(".serr").each(function(){
														if($(this).val().split(',')[0] == 'pr' && row.find('.serr').val()==$(this).val()){
															product_stock_count += parseFloat($(this).parent().parent().find('.qt').val());
														}
													});
													pid = ui.item.id;
													stock_id = ui.item.stock_id;

													if((ui.item.id).split(',')[0] == 'pr'){
														var actual_stock = check_product_available_stock(pid,product_stock_count,stock_id);
													} else {
														var actual_stock = 0;
													}
													}else{
													row.find('.staff').attr('required','true');
													var client_id = $('#clientid').val();
													
													if(client_id != ''){
                    									$.ajax({
                    										url : 'ajax/bill.php',
                    										method : 'post',
                    										data : { sid : ui.item.id, cid : client_id, action:'packages' },
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
                    								
													if(client_id != '0')
													var count_qt = null;	
													var package_id_count_qt = '';
													var qttt = '';
													
													$(".serr").each(function(){
														var price=parseFloat($(this).parent().parent().find('.pr').val());
														if($(this).val() == ui.item.id && price==0){
															//count_qt += parseFloat($(this).parent().parent().find('.qt').val());
															qttt = parseFloat($(this).parent().parent().find('.qt').val());
															package_id_count_qt=$(this).parent().parent().find('.package_id_ck').val();
															if(package_id_count_qt !=''){
																array_elements.push({'pck_id':package_id_count_qt,'qt':qttt});
															}
														}
														
													});
													//console.log(array_elements);
													count_qt = JSON.stringify(array_elements);
													var index_value=$(this).parents('.TextBoxContainer').index();
													//check_package(client_id,ui.item.id,count_qt,index_value);
													
												}
												if(actual_stock == 0){
													row.find('.qt').val('0');
													
													}else{
													row.find('.qt').val('1');
												}
												price_calculate(row);
												sumup();
												formValidaiorns();
											}
                        				});	
                        			}
									
									function autocomplete_serr(){
										var array_elements = [];
										$(".ser").autocomplete({
											source: function(request, response) {
												var ser_stime = '';
												if($(this.element).parent().parent().attr('class')=='TextBoxContainer'){
													ser_stime = $('#date').val()+' '+$('#time').val();
													}else{
													ser_stime = $(this.element).parent().parent().prev('tr').find('.ser_etime').val();
												}
												
												$.getJSON("ajax/bill.php", { term: request.term,ser_cat_id: $(this.element).parent().parent().find('.ser_cat_id').val(),ser_stime:ser_stime}, response);
											},
											minLength: 1,
											select:function (event, ui) {  
												var row = $(this).parent().parent();
												// var childrow = $(this).parent().parent();
												row.find('.serr').val(ui.item.id);
												row.find('.stock_id').val(ui.item.stock_id);
												row.find('.prr').val(ui.item.price);
												row.find('.rpoint').val(ui.item.points);
												row.find('.qt').val('1');
												row.find('.qt').prop('readonly', false);
												row.find('.disc_row ').val('0');
												row.find('.duration').val(ui.item.duration);
												row.find('.ser_stime').val(ui.item.ser_stime);
												row.find('.ser_etime').val(ui.item.ser_etime);
												row.find('.start_time').val((ui.item.ser_stime).substring(11));
												row.find('.end_time').val((ui.item.ser_etime).substring(11));
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
												var product_stock_count = -1;
												var pid = null;
												if((ui.item.id).split(',')[0] == 'pr' || (ui.item.id).split(',')[0] =='pa'){
													row.find('.staff').removeAttr('required');
													$(".serr").each(function(){
														if($(this).val().split(',')[0] == 'pr' && row.find('.serr').val()==$(this).val()){
															product_stock_count += parseFloat($(this).parent().parent().find('.qt').val());
														}
													});
													pid = ui.item.id;
													stock_id = ui.item.stock_id;
                                            
													if((ui.item.id).split(',')[0] == 'pr'){
														var actual_stock = check_product_available_stock(pid,product_stock_count,stock_id);
													} else {
														var actual_stock = 1;
													}
													}else{
													row.find('.staff').attr('required','true');
													var client_id = $('#clientid').val();
													
													if(client_id != ''){
                    									$.ajax({
                    										url : 'ajax/bill.php',
                    										method : 'post',
                    										data : { sid : ui.item.id, cid : client_id, 'action':'packages' },
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
													
													
													if(client_id != '0')
													var count_qt = null;	
													var package_id_count_qt = '';
													var qttt = '';
													
													$(".serr").each(function(){
													    var row2 = $(this).parent().parent();
														var price=parseFloat($(this).parent().parent().find('.pr').val());
														if($(this).val() == ui.item.id && price==0){
															//count_qt += parseFloat($(this).parent().parent().find('.qt').val());
															qttt = parseFloat($(this).parent().parent().find('.qt').val());
															package_id_count_qt=$(this).parent().parent().find('.package_id_ck').val();
															if(package_id_count_qt !=''){
																array_elements.push({'pck_id':package_id_count_qt,'qt':qttt});
															}
														}
													});
													//console.log(array_elements);
													count_qt = JSON.stringify(array_elements);
													var index_value=$(this).parents('.TextBoxContainer').index();
													//check_package(client_id,ui.item.id,count_qt,index_value);
													
												}
												if(actual_stock == 0){
													row.find('.qt').val('0');
													
													}else{
													row.find('.qt').val('1');
												}
												price_calculate(row);
												sumup();
												formValidaiorns();
											}
										});	
									}
									
									function barcode_scanner(){
									    $(":input").keypress(function(event){
                                            if (event.which == '10' || event.which == '13') {
                                                $(".ser").blur();
                                                return false;
                                            }
                                        });
                                		$(".ser").on('blur',function(){
                                			var barcode = $(this).val();
                                			var row = $(this).parent().parent();
                                			$.ajax({
                                				url  : "ajax/fetch_barcode_product.php",
                                				type : "post",
                                				data : { action : 'checkbarcode', barcode : barcode },
                                				dataType : 'json',
                                				success : function(res){
                                					if(res != ''){
                                					    var flag = 'true';
                                					    var prid = res.id;
                                					    var product_stock_count = 0;
                                					    $('.serr').each(function(key, value){
                                					        var Div = $(this).parent().parent();
                                					        if(value.value == res.id){
                            									product_stock_count += parseInt(Div.find('.qt').val());
                            								}
                            							});
                            							
                            							if((prid).split(',')[0] == 'pr'){
                                							row.find('.staff').removeAttr('required');
                                							pid = prid;
                                							var actual_stock = check_product_available_stock(pid,product_stock_count, res.stock_id);
                            							}
                            							
                                					    $('.serr').each(function(key, value){
                                					        var Div = $(this).parent().parent();
                                					        if(value.value == res.id){
                                					            if(actual_stock == 0){
                                									toastr.warning('Stock is empty');
                                									row.find('.ser').focus();
                                    							}else{
                                    					            Div.find('.qt').val(parseInt(Div.find('.qt').val())+parseInt(1));
                                    					            price_calculate(Div);
                                    							    sumup();
                                    							    flag = 'false';
                                    							}
                                					        }
                                					    });
                                					    if(flag == 'false'){
                                					        row.find('.ser').val("");
                                					        row.find('.ser').focus();
                                					        return;
                                					    }
                                					    
                                					    row.find('.serviceErrorMessage').remove();
    		                                            row.find('.ser').css("border-color","");
                                						row.find('.ser').val(res.value);
                                						row.find('.serr').val(res.id);
                                						row.find('.pr').val(res.price);
                                						row.find('.prr').val(res.price);
                                						row.find('.qt').val('1');
                                						row.find('.disc_row ').val('0');
                                						row.find('.stock_id ').val(res.stock_id);
                                						row.find('.ser_stime ').val('<?= date('Y-m-d H:i').':00' ?>');
                                						row.find('.ser_etime ').val('<?= date('Y-m-d H:i').':00' ?>');
                                						row.find('.start_time ').val('<?= date('H:i A') ?>');
                                						row.find('.end_time ').val('<?= date('H:i A') ?>');
                                						var appointment_date = $('#date').val();
                                						var pid = null;
                                						if((prid).split(',')[0] == 'pr'){
                                							row.find('.staff').removeAttr('required');
                                							pid = prid;
                                							var actual_stock = check_product_available_stock(pid,product_stock_count, res.stock_id);
                            							}
                                							if(actual_stock == 0){
                            									row.find('.qt').val('0');
                            									row.remove();
                            									toastr.warning('Stock is empty');
                                							}else{
                                								row.find('.qt').val('1');
                                							}
                                							
                                							$.ajax({
                            								    url : "ajax/service.php",
                            								    method : "POST",
                            								    data : { action : 'provider_list_by_service', service_id : res.id, appointment_date : appointment_date},
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
                            								
                                							price_calculate(row);
                                							sumup();
                                					}
                                					$('#btnAdd').click();
                                					row.closest('tr').next('tr').find('.ser').focus();
                                				}
                                			});
                                		});
                                	}
									
									function count(array_elements) {
										// array_elements.sort();
										// var current = null;
										// var arr =[];
										// var cnt = 0;
										// for (var i = 0; i < array_elements.length; i++) {
										// if (array_elements[i]['pck_id'] != current) {
										// current = array_elements[i]['pck_id'];
										// cnt = array_elements[i]['qt'];
										// } else {
										// cnt +=array_elements[i]['qt'];
										// var pack_id = array_elements[i]['pck_id'];
										// arr.push({'packege_id':pack_id,'count':cnt});
										// }
										// }
										// if (cnt > 0) {
										// console.log(arr);
										// }
									}
									
									
									
									function check_product_available_stock(pid,used_stock,stock_id){
										var invoice_id=0;
										<?php if(isset($_GET['beid'])){ ?>
											invoice_id = <?=$_GET['beid']?>;
										<?php }  ?>
										var as= false;
										$.ajax({
											url : "ajax/check_product_available_stock.php",
											type: "POST",
											async: false,
											data: {pid:pid,used_stock:used_stock,invoice_id:invoice_id,stock_id:stock_id},
											success:function(data){
												var json = $.parseJSON(data);
												if(json !="")
												as = parseFloat(json[0]['actual_stock']);
												else
												as=0;
											}
										});
										return as ;
									}
									
									// Function to check package_services are available or not for client

									function check_package(client_id,service_id,count_qt,index_value){
										
										$.ajax({
											url : "ajax/fetch_package_details.php",
											type: "POST",
											data: {check_cid:client_id,check_sid:service_id,count_qt:count_qt},
											success:function(data){
												var json = $.parseJSON(data);
												var available_services=0;
												for(var i=0;i<json.length;i++){
													available_services	+= json[i]['available_services'];
												}
												
												if(available_services > 0){
													var options = {
														ajaxPrefix: '',
														ajaxType: 'POST',
														ajaxData: {cid:client_id,sid:service_id,count_qt:count_qt,index_value:index_value},
														// ajaxComplete:function(){
														// this.buttons([{
														// type: Dialogify.BUTTON_PRIMARY,
														// }]);	
														// }	
													};
													var dialog=new Dialogify('ajax/fetch_package_details.php', options)
													.title('View Package Details')
													.showModal();
												}	
												
											}
										});
									}
									
									
									function use_service(e,index_value){
										
										var $TextBoxContainer=$('.TextBoxContainer');
										$TextBoxContainer.eq(index_value).find('.package_service_quantity').remove();
										$TextBoxContainer.eq(index_value).find('.package_service_inv').remove();
										$TextBoxContainer.eq(index_value).find('.package_id_ck').remove();
										var row		= e.parents('tr');
										var valid 	= row.find('.valid').val();
										var pack_name = row.find('.pack_name').val();
										var ser_name= row.find('.ser_name').val();
										var qt 		= row.find('#package_service_qt').val();
										var inv     = row.find('.inv').val();
										var package_id_ck     = row.find('.package_id_ck').val();
										
										$("dialog").remove();
										
										var serr = $('.TextBoxContainer').find('.serr');
										if((serr.eq(index_value).val()) === ser_name && qt !=0){
											$TextBoxContainer.eq(index_value).find('.pr').val('0');
											$TextBoxContainer.eq(index_value).find('.qt').val(qt);
											$TextBoxContainer.eq(index_value).find('.prr').val("0");
											$TextBoxContainer.eq(index_value).find('.rpoint').val("0");
											$TextBoxContainer.eq(index_value).find('.qt').attr("readonly",true);
											$TextBoxContainer.eq(index_value).find('.qt').after("<input type='hidden' name='package_service_quantity[]' value='"+qt+"' class='package_service_quantity'>");
											$TextBoxContainer.eq(index_value).find('.qt').after("<input type='hidden' name='package_service_inv[]' value='"+inv+"' class='package_service_inv'>");
											$TextBoxContainer.eq(index_value).find('.qt').after("<input type='hidden' name='package_id_ck[]' value='"+package_id_ck+"' class='package_id_ck'>");
											
										}
										sumup();
									}
									// <!------------End------------>
									
									function price_calculate(row){
										var pr = row.find('.prr').val();
										var qt = row.find('.qt').val();
										var sum = pr * qt;
										var package_service = row.find('.pa_ser').val();
										if(package_service == ''){
											var disc_row_val = row.find('.disc_row').val();
											disc_row_val = disc_row_val>0?disc_row_val:0;
											var disc_row_type = row.find('.disc_row_type').val();
											if(disc_row_type=='0'){
	                        				    if(disc_row_val <= 100){
	                        					    var disc_row = parseFloat((sum * disc_row_val)/100);
	                        				    } else {
	                        				        row.find('.disc_row').val('0');
	                        				        var disc_row = 0;
	                        				        toastr.warning("In % max discount should be 100%");
	                        				    }
	                        				} else {
	                        					var disc_row = parseFloat(disc_row_val);
	                        				}
	                        				if(sum > 0){
												sum = sum - disc_row;
											}
											row.find('.price').val(sum);
											var pric = 0;
											var  sums = 0;
											var  sump = 0;
											var sumt = 0;
											var sum = 0;
											var ids = $(".serr");
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
												$("#sum").html("<?= CURRENCY ?> "+sum.toFixed(2));
												$("#sum2").val(sum);
											}
										} else {
											row.find('.qt').prop('readonly',true);
										}

										if($('#has_membership').val() == 1 && row.find('.disc_row').val() != 0){
											row.find('.pr').prop('readonly', true);
										} else {
											row.find('.pr').prop('readonly', false);
										}
									}
									
									function sumup(){
										var pric = 0;
										var sums = 0;
										var service_sum = 0;
										var sum = 0;
										var service_worth_rem_sum = 0;
										var ids = $(".serr");

										var remaining_service_worth = $('.remaining_service_worth').val()||0;
										
										var inputs = $(".price");
										
										for(var i = 0; i < inputs.length; i++){
											var service = $(ids[i]).val().split(',');
											sum = parseFloat(sum) + parseFloat($(inputs[i]).val())||0;	
										}
										
										
										$("#sum").html("<?= CURRENCY ?> "+sum);
										$("#sum2").val(sum);
										membershipDiscounts();
										sum = $('#sum2').val();
										$("#total").val(sum);
										$("#sum").val(sum);
										
										// var dis = parseFloat($("#disc").val());
										// dis = dis || 0;
										
										var coupon_per_user = parseFloat($("#c_per_user").val());
										coupon_per_user = coupon_per_user || 0;	
										
										var coupon_per_user_used = parseFloat($("#c_per_user_use").val());
										coupon_per_user_used = coupon_per_user_used || 0;
										
										var coupon_discount = parseFloat($("#discount").val());
										coupon_discount = coupon_discount || 0;
										
										var coupon_discount_type = parseFloat($("#discount_type").val());
										coupon_discount_type = coupon_discount_type || 0;
										
										var coupon_max = parseFloat($("#cmax").val());
										coupon_max = coupon_max || 0;
										
										var coupon_min = parseFloat($("#cmin").val());
										coupon_min = coupon_min || 0;
										
										
										
										if(coupon_discount != ''){
											if((coupon_discount_type == 0)){ 
												csum1 = sum * coupon_discount / 100;
												if(coupon_max == 0){
													coupon_max = 999999999;
												}
												if((csum1 < coupon_max) && (sum > coupon_min)){
													sum = sum - csum1;
													
													}else if((sum > coupon_min)){
													sum = sum - coupon_max ;
													
													}else{
													$('#ccid').val('');
													$('#cmax').val('');
													$('#cmin').val('');
													$('#valid').val('');
													$('#discount').val('');
													$('#discount_type').val('');
													$('#c_per_user_used').val('');
													$('#c_per_user').val(''); 
													setTimeout(function(){
														$('#cc').val('');
													}, 500);
													
												}
												}else if(coupon_discount_type == 1){
												if((sum > coupon_min)){
													sum = sum - coupon_discount;
													}else{
													
													$('#ccid').val('');
													$('#cmax').val('');
													$('#cmin').val('');
													$('#valid').val('');
													$('#discount').val('');
													$('#discount_type').val('');
													$('#c_per_user_used').val('');
													$('#c_per_user').val('');
													setTimeout(function(){
														$('#cc').val('');
													}, 500);
												}
											}
										}
										
										var total = sum ;
										if($('.total_disc_row_type').val() == 1){
											var tot_disc =sum;
											var tot_dis=$('#total_disc').val();
											total = tot_disc - tot_dis;
								// 			total = Math.round(tot_disc - tot_dis);
											
											}else{ 
											var tot_disc =sum;
											var tot_dis=$('#total_disc').val();
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


										
										var earned_points_used = 0;
										var atr = $('.chargable_radio').find('.yes').attr('previousvalue');

										if(atr == 'true' || atr == 'checked'){
											var earned_points = parseFloat($("#earned_points").val())||0;
											earned_points_used = (earned_points / 10)||0;
											$('#used_redeem_points').val(earned_points);
											} else {	
											$('#used_redeem_points').val("0");
										}
										$("#total").val(total.toFixed(2) - earned_points_used.toFixed(2));
								// 		$("#total").val(Math.round(total.toFixed(2) - earned_points_used.toFixed(2)));
										
										var adv = $("#adv").val()||0;
										var paid = 0;
										$('.paid').each(function(){
											paid += parseFloat($(this).val()||0);
										});
										

										var pend = 0 ;
										if(adv <= total){
								// 			pend = Math.round(total - parseFloat(adv));
											pend = total - parseFloat(adv);
										}
								// 		else{
								// 			$("#adv").val("");
								// 		}
										
										
										$("#pend").html(pend.toFixed(2) - earned_points_used.toFixed(2));
								// 		$("#pend").html(Math.round(pend.toFixed(2) - earned_points_used.toFixed(2)));
										
										var $paid_value = pend.toFixed(2) - earned_points_used.toFixed(2);
										
										for(var i = 0; i < inputs.length; i++){
											var service = $(ids[i]).val().split(',');
											// sum = parseFloat(sum) + parseFloat($(inputs[i]).val())||0;
											if(service[0]==="sr"){
												service_sum += parseFloat($(inputs[i]).val());
											} 
										}	
										
										
									
										
										$('.pay_methods').each(function(){

											var paid_sum=0;
											var $flag=0;
											var totalLen=$(".paid").length;
											
											$(".paid").each(function(){
												if($flag==(totalLen-1)){
													paid_sum += parseFloat($(this).attr('value')) || 0;
												}
												$flag++;
												
											});

											if($('#total').val() == 0){
												$('.adv').prop('readonly',true);
											} else {
												if(pend == 0){
													$('select[name=status] option:nth-child(2)').prop('selected',true);
													$('.add_spr_row_payment').css('pointer-events','none');
												} else {
													$('select[name=status] option:nth-child(1)').prop('selected',true);
													$('.add_spr_row_payment').css('pointer-events','initial');
												}
												$('.adv').prop('readonly',false);
											}
											
											
											var pm_index = $(this).parents('table').index(); 
											
							                if($(this).val() !='8'){  
												<?php if(!isset($_GET['beid'])){ ?>
													if(pm_index > 0){
														$('.comm_row').children("table").eq(pm_index).find('.paid').attr('value',(pend.toFixed(2) - earned_points_used.toFixed(2)) - paid_sum );
														}else{
														$('.comm_row').children("table").eq(pm_index).find('.paid').attr('value',pend.toFixed(2) - earned_points_used.toFixed(2) );
													}<?php } ?>
											} 
											
										});
										
										
										$("#wallet_bill").html(pend.toFixed(2) - earned_points_used.toFixed(2));
										
										var used_service_worth=$('.used_service_worth').val();
										
										var paid=0;
										$(".paid").each(function(){
											paid += parseFloat($(this).val()) || 0;
										});

										var fin = 0;
										if(paid > pend.toFixed(2)){
        								// 	fin = (Math.round(total.toFixed(2)) - paid - parseFloat(adv) - earned_points_used.toFixed(2));
        									fin = (total.toFixed(2) - paid - parseFloat(adv) - earned_points_used.toFixed(2));
        									$('#invoice_wallet_amount').val(Math.abs(fin));
        								}else{ 
        									fin = (total.toFixed(2) - paid - parseFloat(adv) - earned_points_used.toFixed(2));
        								// 	fin = (Math.round(total.toFixed(2)) - paid - parseFloat(adv) - earned_points_used.toFixed(2));
        									$('#invoice_wallet_amount').val("0");
										}
										
										// if(fin < 0){
										//     toastr.warning('Paid amount is more than payable amount');
										//     $("#due").html(Math.round(pend));
										//     $("#chng").val(Math.round(pend));
										//     $(".pay_methods .paid:last").val(0);
										//     sumup();
										// } else {
										    $("#due").html(fin.toFixed(2));
										  //  $("#due").html(Math.round(fin.toFixed(2)));
										    $("#chng").val(Math.round(fin.toFixed(2)));
										// }
										
										advKeyup();
									}
									
									
									
									
									function price_change(){
										$(".pr, #tax, .paid").on("keyup change", function () {
											var Price = $(this).val();
											$(this).parent().find('.prr').val(Price);
											$(this).parent().parent().find('.disc_row ').val('0'); 
											sumup();
											$(this).attr('value',$(this).val());
										});
									}	
									
									/*******Show_Wallet_modal********/
										
										$('.pay_method').on('change',function(){
											findDuplicate_payment_mode($(this));
											var pm_index = $(this).parents('table').index();
											var trv=$('.comm_row').children("table").eq(pm_index).find('.paid');
											var pm=$(this).val();
											var amount_paid = 0;
											var i= 0;
											$('.paid').each(function(){
												if($('.pay_method').eq(i).val() == '7'){
													amount_paid += parseFloat($(this).val());
												}
												i++;
											});
											
											var client_id =$('#clientid').val();
											var invoice_id = 0;
											<?php if(isset($_GET['beid'])){ ?>
												invoice_id = <?=$_GET['beid']?>;
											<?php } ?>
											
											if(pm === '7' && client_id !=''){
												trv.off('keyup change');
												jQuery.ajax({
													url:'ajax/fetch_wallet_details.php',
													type: "POST",
													data :{client_id:client_id,invoice_id:invoice_id,wallet_amount_paid:amount_paid},
													success:function(data){
														var json = $.parseJSON(data);
														if(json['wallet_amount'] > 0){
															$("#wallet").modal("show");
															$("#wallet_client_name").html(json['client_name']);
															$("#wallet_cont").html(json['cont']);
															$(".wallet_amount").html(json['wallet_amount']);
															$(".index_id").val(pm_index);
															$('.pay_method option[value="1"]').eq(pm_index).prop('selected',true); 
															}else{
															<?php if(!isset($_GET['beid'])){ ?>
																toastr.warning("Insufficient balance to pay bill !");
															<?php } ?>
															$('.pay_method option[value="1"]').eq(pm_index).prop('selected',true); 
															trv.removeAttr('readonly',true);
															
														}
													},
													error:function (){}
												});	
												
											}
											else{
												// $('.pay_method option[value="1"]').prop('selected',true); 
												trv.removeAttr('readonly',true);
												trv.val("0");
												
											}
											
											price_change();
											sumup();
											
										});	
										/********End*********/
											
											
											
											function use_wallet_pay(){
												var wallet_amount=parseFloat($('.wallet_amount').html());
												var wallet_bill=parseFloat($('#wallet_bill').html());
												var pm_index=parseFloat($('.index_id').val());
												var trev=$('.comm_row').find('.paid').eq(pm_index).attr('readonly',true);
												if(wallet_bill <= wallet_amount){
													trev.val(wallet_bill);
													}else{
													trev.val(wallet_amount);
												}
												
												sumup();
												$("#wallet").modal("hide");
												$('.pay_method option[value="7"]').eq(pm_index).prop('selected',true); 
												
											}
										
											
											function change_event(){
												$(".qt").on("blur keypress keyup keydown change", function () {
													var quant_val=$(this).val();
													var row = $(this).parent().parent(); 
													var product_stock_count = 0;
													
													$(".serr").each(function(){
														if($(this).val().split(',')[0] == 'pr' && row.find('.serr').val()==$(this).val()){
															product_stock_count += parseFloat($(this).parent().parent().find('.qt').val());
														}
													});
													
													if(row.find('.serr').val().split(',')[0] == 'pr'){ 
														var remaining_stock = check_product_available_stock(row.find('.serr').val(),product_stock_count,row.find('.stock_id').val());
													}
	
													var max_val=parseFloat(quant_val) + parseFloat(remaining_stock); 
													if((remaining_stock >= '0') && row.find('.serr').val().split(',')[0] == 'pr'){
														row.find('.qt').attr("max",max_val);
														row.find('.qt').addClass("count_product_stock");
														}else{
														row.find('.qt').removeAttr("max");
														row.find('.qt').removeClass("count_product_stock");
													}
													if(remaining_stock == '0'){
														row.find('.qt').val(quant_val);
													}
													if(quant_val > max_val){
														toastr.warning(max_val +" "+((max_val>1)?' Stocks are left.':'Stock is left '));
														row.find('.qt').val(max_val);
													}
													
													price_calculate(row);
													sumup();
												});
												$(".disc_row,.disc_row_type").on("blur keyup keypress change keydown", function () {
													var row = $(this).parent().parent().parent().parent().parent().parent(); 
													price_calculate(row);
													sumup();
												});
												
												
												$(".disc_row, .disc_row_type").on("blur", function () {
                                					var row = $(this).parent().parent().parent().parent().parent().parent(); 
                                					if(row.find('.prr').val() > 0){
                                						if (parseInt(row.find('.disc_row_type').val()) == 1) {
	                                					    if(parseFloat($(this).val()) <= (row.find('.prr').val()*row.find('.qt').val())){
	                                					        price_calculate(row);
	                                					        sumup();
	                                					    } else {
	                                					        $('#toast-container .toast-warning').remove();
	                                					        toastr.warning("Discount should be less then price");
	                                					        $(this).val(0);
	                                					        price_calculate(row);
	                                					        sumup();
	                                					    }
	                                					}
                                					} else {
                                					   if($(this).val() > 0){
                                					       $('#toast-container .toast-warning').remove();
                                					        toastr.warning("Price should be greater then 0 to apply discount");
                                					        $(this).val(0);
                                					   }
                                					}
                                				});
												
												$(".total_disc_row_type").on("change", function () {
													sumup(); 
												});
												
												$("#total_disc").on("input", function () {
                                				    var total_amount = parseFloat($('#sum2').val());
                                				    if($(this).val() > 0){
                                    				    if(total_amount > 0){
                                    				    	if($(".total_disc_row_type").val()==1){
	                                    				        if(parseFloat($(this).val()) > total_amount){
	                                    				            $(this).val(0);
	                                    				            toastr.warning("Discount should be less then "+total_amount+" <?= CURRENCY ?>");
	                                    				            sumup();
	                                    				        } else {
	                                    				            sumup();
	                                    				        }
	                                    				    }
                                    				    } else {
                                    				        $(this).val(0);
                                    				        sumup();
                                    				        toastr.warning("Total amount should be greater then 0 to apply discount");
                                    				    }
                                				    }else {
                                					    sumup();
                                				    }
                                				});
											}
											
											// Fetch coupons

											$("#cc").autocomplete({
												source: "ajax/checkcoupon.php",
												minLength: 1,
												select:function (event, ui){
													$('#ccid').val(ui.item.id);
													$('#cmax').val(ui.item.max_amount);
													$('#cmin').val(ui.item.min_amount);
													$('#valid').val(ui.item.valid);
													$('#discount').val(ui.item.discount);
													$('#discount_type').val(ui.item.discount_type);
													$('#c_per_user_used').val(ui.item.c_per_user_used);
													$('#c_per_user').val(ui.item.c_per_user);
													sumup();
													
													if(parseFloat(ui.item.min_amount) >= parseFloat($('#sum2').val())){
														$('#ccid').val('0'); 
														$('#cval').val('0'); 
														$('#cmax').val('0');
														$('#coupon').val(''); 
														toastr.warning("Minimum purchase value should be "+ui.item.min_amount+" <?= CURRENCY ?>");
													}else{
													    
												    }	
													<?php if(!isset($_GET['beid'])){
													?>	
													if($('#clientid').val() != '0'){
														$.ajax({
															url: "ajax/checkcoupon.php",
															data:{cid:$('#clientid').val()},
															type: "POST",
															success:function(data){
																if(data == 'allused'){
																	toastr.warning('Coupon usage limit exceeded');
																	$('#cc').val('');
																} else {
																    toastr.success("Coupon applied successfully");
																} 	 
															},
															error:function (){}
														});
													}
													<?php } ?>	
												}
												
											});

											// End Fetch coupons
											
											$(document).on("keyup blur", '#cc', function() {
												if($(this).val() == ''){
													$('#ccid').val('');
													$('#cmax').val('');
													$('#cmin').val('');
													$('#valid').val('');
													$('#discount').val('');
													$('#discount_type').val('');
													$('#c_per_user_used').val('');
													$('#c_per_user').val('');
													sumup();							
												} 
											});
											
											function check() {
												$client_id = $('#clientid').val();
												jQuery.ajax({
													url: "checkccont.php?p="+$("#cont").val(),
													//data:'p='+$("#prod").val(),
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
															$(".client_name").val("");
															$('#dob').val("");
															$('#aniv').val("");
														}
													},
													error:function (){}
												});
											}
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
            			                html += '</div></div>';
            			    $('#member_ship_message').html(html);
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
		}
		
		$('.client').on('blur',function(){
		    if($('#cont').val().length < 10){
		        emptyMembershipData();
		    }
		});
		
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
			                           parent.find('.disc_row').val(mem_service[0]);
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
		                        
		                        $('#membership_appilied').val(1);
		                        price_calculate(parent);
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
			                           parent.find('.disc_row').val(mem_service[0]);
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
		                     var editid = parseInt('<?= isset($_GET['beid'])?$_GET['beid']:0 ?>');
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
                     var editid = parseInt('<?= isset($_GET['beid'])?$_GET['beid']:0 ?>');
                     if(editid == 0){
		              //  parent.find('.disc_row').val(0);
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
<?php
    if(isset($_SESSION['bill_generated']) && $_SESSION['bill_generated'] != ''){
        if($_SESSION['bill_type'] == 'updated'){
            $type = '<div class="alert alert-success">Bill Updated successfully</div>';
        } else if($_SESSION['bill_type'] == 'new'){
            $type = '<div class="alert alert-success">Bill Created successfully</div>';
        } else {
            $type = '';
        }
	    ?>
	        <script>
	            setTimeout(function(){
	                $('#print_bill').attr('onclick','print_bill(<?= $_SESSION['bill_generated'] ?>)');
	                $('#sms_bill').attr('onclick','sms_bill(<?= $_SESSION['bill_generated'] ?>)');
	                $('#email_bill').attr('onclick','email_bill(<?= $_SESSION['bill_generated'] ?>)');
	                $('#b_status').html('<?= $type ?>');
		            $('#bill_options').modal('show');
		            $.ajax({
		                url: "ajax/sms_bill.php",
            			type: "POST",
            			data:{action: 'product_usage', inv: <?= $_SESSION['bill_generated'] ?>},
            			success: function(res){
            			   if(res != ''){
            			       $('#cons_table').html(res);
            			   } else {
            			       $('#product_usage').hide();
            			   }  
            			   autocomplete_serr_prod();
            			   check_prod_stock();
            			}
		            });
	            },1000);
		    </script>
	    <?php
	    $_SESSION['bill_generated'] = '';
	} 
?>
<script>
    $(document).ready(function(){
       //$('#bill_options').modal('show'); 
       autocomplete_serr_prod();
       check_prod_stock();
    });
    
    // product autocomplete
    
    function autocomplete_serr_prod(){
        $(".pro").autocomplete({
			source: function(request, response) {
				var provider = $(this.element).parent().parent().find('.staff_service').val();
				$.getJSON("autocomplete/addproduct.php?provider="+provider, { term: request.term}, response);
			},
			minLength: 1,
			select:function (event, ui){  
				var row1= $(this).parents('tr');
				row1.find('.product_id_prod').val(ui.item.id);
				row1.find('.product_name').val(ui.item.product_name);
				row1.find('.stock_id').val(ui.item.stock_id);
				row1.find('.qt_prod').val('0');
				row1.find('.v_unit_prod option[value="'+ui.item.unit+'"] ').prop('selected',true);
				$.ajax({
				    url: "ajax/check_product_available_stock.php",
        			type: "POST",
        			dataType : 'JSON',
        			data:{action: 'stock_for_use', product_id: ui.item.id, stock_id : ui.item.stock_id},
        			success: function(res){
        			    row1.find('.available').text(res.pending_stock);
        			    row1.find('.available_show').text(res.pending_stock_show);
        			}
				});
				findDuplicate($(this));
			}
		});	
	}
	
	// function to find duplicate Product
	function findDuplicate(e){
		duplicate_arr=[];
		var row=e.parents(".product_usage_row");
		var val=$('.product_id');
		val.each(function(){
			duplicate_arr.push($(this).val());
		});
		
		for(var i=0;i<duplicate_arr.length;i++){
			for(var j=i+1;j<duplicate_arr.length;j++){
				if(duplicate_arr[i]==duplicate_arr[j]){
					
					row.remove();
					toastr.warning("Duplicate Entry");
				}  
			}
		}
	}
    
    
    // function for print invoice
    function print_bill(inv_id){
        window.open( "<?= BASE_URL ?>invoice.php?inv="+inv_id, '_blank' );
    }
    
    // function for sms bill
    function sms_bill(inv_id){
        $.ajax({
            url: "ajax/sms_bill.php",
			type: "POST",
			data:{action: 'invoice_sms', inv:inv_id},
			success: function(res){
			    if(res == '1'){
			        toastr.success('SMS sent successfully');
			    } else if(res == '0') {
			        toastr.error('SMS not sent');
			    }
			}
        });
    }

    function email_bill(inv_id, emailrequired = null){
    	if(emailrequired == null){
    		toastr.warning('Please wait...');
    	}
    	var email_id = '';
    	if(emailrequired != '' && emailrequired != null){
    		email = $('#cust_email').val();
    		var pattern = /^\b[A-Z0-9._%-]+@[A-Z0-9.-]+\.[A-Z]{2,4}\b$/i
    		if(!pattern.test(email)){
    			toastr.error('Please enter valid email address');
    		} else {
    			email_id = email;
    		}
    	}
    	$.ajax({
    		url: "ajax/email_bill.php",
			type: "POST",
			dataType : 'json',
			data:{action: 'invoice_email', inv:inv_id, email_address : email_id},
			success: function(res){
			    if(res.status == '1'){
			    	toastr.success(res.message);
			    	$('#email_update').remove();
			    } else if(res.status == '2'){
			    	toastr.error('Client not found');
			    	$('#email_update').remove();
			    } else if(res.status == '3'){
			    	toastr.error('Email not sent');
			    	$('#email_update').remove();
			    } else if(res.status == '0'){
			    	$('#email_update').remove();
			    	var html = '<div class="modal fade in disableOutsideClick" id="email_update" role="dialog">'+
						'<div class="modal-dialog modal-sm">'+
							'<div class="modal-content">'+
								'<div class="modal-header">'+
									'<h4 class="modal-title">Add client email</h4>'+
								'</div>'+
								'<div class="modal-body">'+
									'<div class="row">'+
									    '<div class="col-lg-12 col-md-12 col-sm-12 col-xs-6">'+
									    	'<p>Add client email &amp; send invoice</p>'+
									        '<input type="email" id="cust_email" style="margin-bottom:15px;" class="form-control" />'+
									    '</div>'+
									    '<div class="col-lg-7 col-md-7 col-sm-7 col-xs-6">'+
									        '<button type="button" onclick="email_bill(\''+inv_id+'\', \'emailrequired\')" class="btn btn-dark btn-block"><i class="fa fa-paper-plane-o" aria-hidden="true"></i>Send Invoice</button>'+
									    '</div>'+
									    '<div class="col-lg-5 col-md-5 col-sm-5 col-xs-6">'+
									        '<button type="button" style="margin-left: 5px;" class="btn btn-danger" data-dismiss="modal" onclick="$(\'#email_update\').remove();"><i class="fa fa-close" aria-hidden="true"></i>Cancel</button>'+
									    '</div>'+
									'</div>'+
								'</div>'+
							'</div>'+
						'</div>'+
					'</div>';
					$('body').append(html);
					$('#email_update').show();
				}
			}
    	});
    }
    
    // add product usage function
    
    $('#usage_submit').on('click',function(){
        var data = $('#product_usage form').serializeArray();
        var parent = [];
        var child = [];
        var count = 1;
        $.each(data,function(index, value){
            var keyname = value.name;
            child.push({ [keyname] : value.value });
            if(count % 13 == 0){
                parent.push(child)
                child = [];
            }
            count++;
        });
        $.ajax({
            url: "ajax/product_usage.php",
			type: "POST",
			data:{action: 'product_usage', data:parent},
			success: function(res){
			    if(res == 1){
			        toastr.success('Product consumption saved successfully');
			    } else if(res == 0){
			        toastr.error('Product consumption not saved');
			    }
			}
        });
    });
    
    function advKeyup(){	
		$('.paid').on('keyup',function(){
			var paid = 0;
			var redeem_point = <?= redeempoint() ?>;
			var max_points = <?= maxredeempoint() ?>;
			var mainDiv = $(this).parent().parent();
			var currentDiv = $(this);
			var rewardPoint = $('#reward_point').val();
			var walletMoney = $('#wallet_money').val();
			
			$('.paid').each(function(){
				paid += parseFloat($(this).val()||0);
			});
			if(parseFloat(paid) > parseFloat($('#pend').val()) ){
				toastr.warning('Advance Amount exceeded total amount.');
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
	
	$(document).ready(function(){
	   var session = '<?= isset($_SESSION['sub-tab-id'])?$_SESSION['sub-tab-id']:0 ?>';
	   $('.data-card').hide();
	   if(session == 'wallet'){
	       $('#'+session).click();
	   } else {
	       $('#bill_tab').click();
	   }
	   <?php $_SESSION['sub-tab-id'] = ''; ?>
	});
	
	$('.sub-tabs').on('click',function(){
	    var id = $(this).attr('id');
	    $('.data-card').stop().hide();
	    $('.sub-tabs').removeClass('active');
	    $('.data-card').each(function(){
	        if($(this).attr('data-id') == id){
	            $(this).stop().show();
	            $('#'+id).addClass('active');
	            if(id == "wallet_tab"){
	                // Disable billing required fields
	                $('input[name=contact]').attr('required',false);
	                $('input[name=client]').attr('required',false);
	                $('input[name="services[]"]').attr('required',false);
	                $('select[name="staffid0[]"]').attr('required',false);
	                // Enable wallet tab
	                $('input[name=name_wallet]').attr('required',true);
	                $('input[name=paid_amount_wallet]').attr('required',true);
	                $('input[name=wallet_amount]').attr('required',true);
	            } else if(id == "bill_tab"){
	                // Enable billing required feild
	                $('input[name=contact]').attr('required',true);
	                $('input[name=client]').attr('required',true);
	                $('input[name="services[]"]').attr('required',true);
	                $('select[name="staffid0[]"]').attr('required',true);
	               // Disable wallet tab
	               $('input[name=name_wallet]').attr('required',false);
	               $('input[name=paid_amount_wallet]').attr('required',false);
	               $('input[name=wallet_amount]').attr('required',false);
	            }
	        }
	    })
	});
	
	
	/*******End********/
		$('#client_name_wallet').on('keypress',function(){
			$('#client_id_wallet').val("");
		});
		$("#client_name_wallet").autocomplete({
			source: "autocomplete/client.php",
			minLength: 1,
			select: function(event, ui) {
				event.preventDefault();
				$('#client_name_wallet').val(ui.item.name);
				$('#client_id_wallet').val(ui.item.id); 
				$('#client_branch_id_wallet').val(ui.item.client_branch_id); 
				$('#cont_wallet').val(ui.item.cont); 
				$('#client-status-wallet').html("");
				clientView(ui.item.id);
			}				
		});	
		
		$('#client_name_wallet').on('blur',function(){
			var client_id = $('#client_id_wallet').val();	
			if(client_id ==''){
				$('#client-status-wallet').html('Please select client name from list');
				$('#client_name_wallet').val("");
				$('#cont_wallet').val(""); 
			}
		});
		
		function checkcat() {
			var cat_id=$('#client_id_wallet').val();
			$.ajax({
				url: "ajax/checkservice.php",
				data:{category: cat,cat_id:cat_id},
				type: "POST",
				success:function(data){
					if(data == '1'){
						$("#check-status-wallet").html("Duplicate category . Please select category from list").css("color","red");
						$('#scat_wallet').val("");
						}else{
						$("#check-status-wallet").html("");
					}
				},
				error:function (){}
			});
		} 
    
        function duplicate(row_id){
            var row = $('#pro_row_'+row_id);
            var clone = row.clone().removeAttr('id');
            clone.find('.pro').val('');
            clone.find('.product_id_prod').val('');
            clone.find('.product_name').val('');
            clone.find('.qt_prod').val('0');
            clone.find('.stock_id').val('0');
            clone.find('.btn-action').html('<button style="margin-left:0px;" onclick="$(this).parent().parent().remove();" class="btn btn-danger btn-remove btn_remove" type="button"><span class="glyphicon-minus" "></span></button>');
            row.after(clone);
            autocomplete_serr_prod();
            check_prod_stock();
        }
        
        function check_prod_stock(){
        	
            $('.qt_prod').on("keyup blur", function() {
                var curr_use = parseFloat($(this).val());
                var row = $(this).parent().parent();
                var unit = row.find('.v_unit_prod').val();
                var stock_id = row.find('.stock_id').val();
                var product_id = row.find('.product_id_prod').val();
                var pending_quantity = parseFloat(row.find('.available').text());
                var pending_quantity_show = row.find('.available_show').text();
                if((parseFloat(curr_use) <= parseFloat(pending_quantity)) && pending_quantity > 0){
                    if(pending_quantity == 0){
                        toastr.error("Stock is empty");
                        $(this).val('0');
                    } else {
                        
                    }
                } else {
                    if(parseFloat(curr_use) > parseFloat(pending_quantity)){
                        toastr.warning("Only "+pending_quantity_show+" is left in stock.");
                        $(this).val(pending_quantity);
                    }
                }
            });
        }
        
        function due_amount(){
            var due = $('#due').text();
            due = parseFloat(due);
            if(due >= 1 || due < 0){
                $('#toast-container .toast-error').remove();
                toastr.error('Amount not received');
                return false;
            } else {
                return true;
            }
        }
</script>																		