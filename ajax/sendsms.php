<?php
	include "../includes/db_include.php";
    // include("../../salonSoftFiles_new/send_sms.php");
    $branch_id = $_SESSION['branch_id'];
    if(isset($_REQUEST['json'])){
        $term = $_REQUEST['json'];
        $obj_json = json_decode($term,true);
        $count = count($obj_json);
        $salon = systemname();
        $ct = 0;
        $contacts = "";
        $message_template = $_REQUEST['message'];
        $sms_count = ceil((strlen($message_template)/160));
        $send_type = $_POST['sendtype'];
        $template_id = $_POST['tempid'];
        
        $data_array = array();
        
        if($send_type == 'single'){
            for($i=0;$i < $count;$i++){
        		$message = $message_template;
        		$message = str_replace("{#name#}",$obj_json[$i]['name'],$message);
        		$message = str_replace("{#enquired_service#}",$obj_json[$i]['enq_service'],$message);
        		$message = str_replace("{#salon_name#}",$salon,$message);
        		$message = str_replace("{#referral_point#}",REFERRAL_POINTS,$message);
        		$message = str_replace("{#booking_link#}",short_url(BASE_URL.'webapp'),$message);
        		if(query("SELECT (total - sent) as pending from sms where id = '1'", [], $conn)[0]['pending'] >= $sms_count){
        		  //  send_sms($obj_json[$i]['contact'],$message,'','single',$template_id);
            		$msg['contact'] = $obj_json[$i]['contact'];
            		$msg['content'] = $message;
            		$msg['msg_type'] = 'single';
            		$msg['template_id'] = $template_id;
            		$msg['software_link'] = BASE_URL;
            		$msg['sender_id'] = SENDER_ID;
            		array_push($data_array,$msg);
        		    $ct += 1;
        		} else {
        		    echo "Insufficient SMS Balance</br>";
                    break;
        		}
        	}
        } else if($send_type == 'bulk'){
            $message = $message_template;
    		$message = str_replace("{#name#}",$obj_json[$i]['name'],$message);
    		$message = str_replace("{#enquired_service#}",$obj_json[$i]['enq_service'],$message);
    		$message = str_replace("{#salon_name#}",$salon,$message);
    		$message = str_replace("{#referral_point#}",REFERRAL_POINTS,$message);
    		$message = str_replace("{#booking_link#}",short_url(BASE_URL.'webapp'),$message);
            for($i=0; $i<intval(count($obj_json)/91) +1 ; $i++ ) {
                for($j=0; $j<91; $j++){
                    $index = $j + $i*91;
                    if(!empty($obj_json[$index])){
                        $ct += 1;
                        $contacts .= PHONE_CODE.$obj_json[$index]['contact'].",";
                    }
                    else{
                        break;
                    }
                }
                $contacts = substr($contacts,0, -1);
                $check = (count(explode(',',$contacts)))*$sms_count;
                if(query("SELECT (total - sent) as pending from sms where id = '1'", [], $conn)[0]['pending'] >= $check){
                    $msg['contact'] = $contacts;
            		$msg['content'] = $message;
            		$msg['msg_type'] = 'bulk';
            		$msg['template_id'] = $template_id;
            		$msg['software_link'] = BASE_URL;
            		$msg['sender_id'] = SENDER_ID;
            		array_push($data_array,$msg);
                    // send_sms($contacts, $message, '','bulk',$template_id);
                    // query("UPDATE sms set sent = sent + ".$check, [], $conn);
                }
                else{
                    echo "Insufficient SMS Balance";
                    exit();
                    die();
                }
                $contacts = "";
            }
        }
        if(count($data_array) > 0){
            $data = json_encode($data_array);
            $ch = curl_init();
            $url = SMS_API_URL.'sms_queue.php';
            curl_setopt_array($ch,
              array(
                CURLOPT_URL => $url,
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => $data,
                CURLOPT_HTTPHEADER => array('Content-Type:application/json'),
                CURLOPT_RETURNTRANSFER => true,
              )
            );
            
            $data = curl_exec($ch);
            curl_close($ch);
            $character = json_decode($data,true);
            if($character['status'] == 1){
                echo $character['message'];
            } else {
                echo $character['message'];
            }
        } else {
            echo "Please check your SMS Balance";
        }
        // echo "Message Sent to ".$ct." Clients.";
    }
    
    // Send invite code to clients
    
    if(isset($_POST['action']) && $_POST['action'] == 'send_invite_code'){
        die();
        $term = $_REQUEST['data'];
    	$obj = json_decode($term,true);
    	$count = count($obj);
    	$salon = systemname();
    	$c = 0;
        for($i=0;$i<$count;$i++){
    		$message = "Dear {name} \n".$obj[$i]['ref_code']." is your referral code.\nYou & your friend will get ".REFERRAL_POINTS." points on your friend's first billing.\nFrom {salon_name}";
    		$message = str_replace("{name}",$obj[$i]['name'],$message);
    		$message = str_replace("{salon_name}",$salon,$message);
    		send_sms($obj[$i]['contact'],$message);
    		$c++;
    		$message='';
    	}
    	echo "Referral code Sent to ".$c." clients Successfully";
    }
    
    // send pending payment message to client
    
    if(isset($_POST['action']) && $_POST['action'] == 'send_pending_payment_reminder'){
        $term = $_REQUEST['data'];
    	$obj = json_decode($term,true);
    	$count = count($obj);
    	$salon = systemname();
    	$c = 0;
        for($i=0;$i<$count;$i++){
    		$message = "Dear {name} \nYou have a pending amount of ".CURRENCY." ".$obj[$i]['amount'].".\nPlease clear it as soon as possible.\nThank you!\nFrom {salon_name}";
    		$message = str_replace("{name}",$obj[$i]['name'],$message);
    		$message = str_replace("{salon_name}",$salon,$message);
    		send_sms($obj[$i]['contact'],$message);
    		$c++;
    		$message='';
    	}
    	echo "SMS Sent to ".$c." clients Successfully";
    }
    
?>