<?php
	include_once '../includes/db_include.php';
	$branch_id = $_SESSION['branch_id'];
	if(isset($_POST['name']) && !isset($_GET['edit_id'])){
		
		$client = $_POST['name'];
		$client = ucfirst($client);
		$cont 	= $_POST['cont'];
		$gst 	= $_POST['gst'];
		$dob 	= $_POST['dob'];
		$gender = $_POST['gender'];
		$email 	= $_POST['email'];
		$address 	= $_POST['addr'];
		$aniversary 	= $_POST['aniv'];
		$leadsource = $_POST['clientsource'];
		$referral_code  = strtoupper(substr($client,0,4).substr(md5(sha1($cont)),0,4));
		$check_client = query_by_id("SELECT COUNT(*) as total FROM client WHERE cont='".$cont."' AND active='0' AND branch_id='".$branch_id."'",[],$conn)[0]['total'];
		if($check_client <= 0){
    		$insert_sql="INSERT INTO `client` set `name`=:name,`cont`=:cont,`gst`=:gst,`email`=:email,`address`=:address,`gender`=:gender,`dob`=:dob,`aniversary`=:aniversary,`leadsource`=:leadsource, `cal_color`=:cal_color,`referral_code`=:referral_code, `branch_id`='".$branch_id."'";
    		query($insert_sql,[
    							'name'	=>$client,
    							'cont'	=>$cont,
    							'gst'	=>$gst,
    							'email'	=>$email,
    							'address'=>$address,
    							'gender' =>$gender,
    							'dob'   =>$dob,
    							'aniversary'=>$aniversary,
    							'leadsource'=> $leadsource,
    							'cal_color'=>$cal_color,
    							'referral_code'=>$referral_code,
    							],$conn);
    	sleep(2);
    	    echo '{"data-inserted":"1"}';
		} else {
		    echo '{"data-inserted":"2"}';
		}
	}else if(isset($_GET['edit_id']) & $_GET['edit_id'] > 0){
		$edit_id = $_GET['edit_id'];
		$client = $_POST['name'];
		$client = ucfirst($client);
		$cont 	= $_POST['cont'];
		$gst 	= $_POST['gst'];
		$dob 	= $_POST['dob'];
		$gender = $_POST['gender'];
		$email 	= $_POST['email'];
		$address 	= $_POST['addr'];
		$aniversary 	= $_POST['aniv'];
		$leadsource = $_POST['clientsource'];
		$referral_code  = strtoupper(substr($client,0,4)).strtoupper(substr(md5(sha1($cont)),0,4));
		$insert_sql="UPDATE  `client` set `name`=:name,`cont`=:cont,`gst`=:gst,`email`=:email,`address`=:address,`gender`=:gender,`dob`=:dob,`aniversary`=:aniversary, `leadsource`=:leadsource,`cal_color`=:cal_color,`referral_code`=:referral_code where id=:id and branch_id='".$branch_id."'";
		query($insert_sql,[
							'name'	=>$client,
							'cont'	=>$cont,
							'gst'	=>$gst,
							'email'	=>$email,
							'address'=>$address,
							'gender' =>$gender,
							'dob'   =>$dob,
							'aniversary'=>$aniversary,
							'leadsource'=> $leadsource,
							'cal_color'=>$cal_color,
							'referral_code'=>$referral_code,
							'id'    =>$edit_id,
							],$conn);
	sleep(2);
	echo '{"data-inserted":"1"}';
		
		
	}
	 