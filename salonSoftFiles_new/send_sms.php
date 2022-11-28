<?php
	
function send_sms($contact,$content,$template_type = null, $sms_type = null, $template_id = null){
    // $send_via variables = textguru, webber, kaleyra
    $send_via = 'kaleyra'; 
    
    global $conn,$client_sms_api_url, $save_sms;
    
    $sql="SELECT *,(total-sent) as pending FROM `sms` where id='1'";
    
    $row=query_by_id($sql,[],$conn)[0];
    
    $message_data = $content;
    
    if(empty($client_sms_api_url)){
        if($template_type == 'appointment_booking_software'){
            $template = "Thank You {#name#}. Your Appointment is booked for {#date#} at {#time#}. {#salon_name#} eSALON";
            $template_id = '1507161692185695627';
            $template = str_replace('{#name#}', $message_data['name'], $template);
            $template = str_replace('{#date#}', $message_data['date'], $template);
            $template = str_replace('{#time#}', $message_data['time'], $template);
            $template = str_replace('{#salon_name#}', $message_data['salon_name'], $template);
            $content = $template;
        }
        
        if($template_type == 'wallet_recharge_sms'){
            $template = "Dear {#name#}. your wallet credited with {#currency#} {#amount#} on {#date#} {#time#}. Avl Wallet Balance {#currency#} {#balance#} {#salon_name#} eSALON";
            $template_id = '1507161692193147377';
            $template = str_replace('{#name#}', $message_data['name'], $template);
            $template = str_replace('{#currency#}', $message_data['currency'], $template);
            $template = str_replace('{#amount#}', $message_data['amount'], $template);
            $template = str_replace('{#date#}', $message_data['date'], $template);
            $template = str_replace('{#time#}', $message_data['time'], $template);
            $template = str_replace('{#balance#}', $message_data['balance'], $template);
            $template = str_replace('{#salon_name#}', $message_data['salon_name'], $template);
            $content = $template;
        }
        
        if($template_type == 'new_enquiry_sms'){
            $template = "Thank You {#name#} to Join Us eSALON";
            $template_id = '1507161692201309972';
            $template = str_replace('{#name#}', $message_data['name'], $template);
            $content = $template;
        }
        
        if($template_type == 'billing_sms'){
            $template = "Thank You {#name#} for your purchase of {#amount#} on {#date#} Invoice link {#inv_link#} Feedback Link {#feedback_link#} From {#salon_name#} eSalon";
            $template_id = '1507161520972454025';
            $template = str_replace('{#name#}', $message_data['name'], $template);
            $template = str_replace('{#amount#}', $message_data['amount'], $template);
            $template = str_replace('{#date#}', $message_data['date'], $template);
            $template = str_replace('{#inv_link#}', $message_data['inv_link'], $template);
            $template = str_replace('{#feedback_link#}', $message_data['feedback_link'], $template);
            $template = str_replace('{#salon_name#}', $message_data['salon_name'], $template);
            $content = $template;
        }
        
        if($template_type == 'billing_sms_with_earned_pending_point'){
            $template = "Thank You {#name#} for your purchase of {#amount#} on {#date#} Invoice link {#inv_link#} Feedback Link {#feedback_link#} You earned {#reward_point#} reward points your pending reward points is {#pending_point#} From {#salon_name#} eSALON";
            $template_id = '1507161692242578442';
            $template = str_replace('{#name#}', $message_data['name'], $template);
            $template = str_replace('{#amount#}', $message_data['amount'], $template);
            $template = str_replace('{#date#}', $message_data['date'], $template);
            $template = str_replace('{#inv_link#}', $message_data['inv_link'], $template);
            $template = str_replace('{#feedback_link#}', $message_data['feedback_link'], $template);
            $template = str_replace('{#reward_point#}', $message_data['reward_point'], $template);
            $template = str_replace('{#pending_point#}', $message_data['pending_point'], $template);
            $template = str_replace('{#salon_name#}', $message_data['salon_name'], $template);
            $content = $template;
        }
        
        if($template_type == 'billing_sms_with_pending_point'){
            $template = "Thank You {#name#} for your purchase of {#amount#} on {#date#} Invoice link {#inv_link#} Feedback Link {#feedback_link#} your pending reward points is {#pending_point#} From {#salon_name#} eSALON";
            $template_id = '1507161692247365651';
            $template = str_replace('{#name#}', $message_data['name'], $template);
            $template = str_replace('{#amount#}', $message_data['amount'], $template);
            $template = str_replace('{#date#}', $message_data['date'], $template);
            $template = str_replace('{#inv_link#}', $message_data['inv_link'], $template);
            $template = str_replace('{#feedback_link#}', $message_data['feedback_link'], $template);
            $template = str_replace('{#pending_point#}', $message_data['pending_point'], $template);
            $template = str_replace('{#salon_name#}', $message_data['salon_name'], $template);
            $content = $template;
        }
        
        if($template_type == 'appointment_booking_otp'){
            $template = "{#otp#} is your one-time password for appointment booking eSALON";
            $template_id = '1507161692250371412';
            $template = str_replace('{#otp#}', $message_data['otp'], $template);
            $content = $template;
        }
        
        if($template_type == 'online_appointment_booking_sms'){
            $template = "Thank You {#name#} Your Appointment is booked on {#date#} at {#time#}. {#salon_name#} eSALON";
            $template_id = '1507161692257248737';
            $template = str_replace('{#name#}', $message_data['name'], $template);
            $template = str_replace('{#date#}', $message_data['date'], $template);
            $template = str_replace('{#time#}', $message_data['time'], $template);
            $template = str_replace('{#salon_name#}', $message_data['salon_name'], $template);
            $content = $template;
        }
        
        if($template_type == 'app_appointment_booking_otp'){
            $template = "{#otp#} is your OTP eSALON";
            $template_id = '1507161692260704673';
            $template = str_replace('{#otp#}', $message_data['otp'], $template);
            $content = $template;
        }
        
        if($template_type == 'appointment_reminder_sms'){
            $template = "Reminder for your appointment booked on {#date#} {#time#} From {#salon_name#} eSALON";
            $template_id = '1507161692264286035';
            $template = str_replace('{#date#}', $message_data['date'], $template);
            $template = str_replace('{#time#}', $message_data['time'], $template);
            $template = str_replace('{#salon_name#}', $message_data['salon_name'], $template);
            $content = $template;
        }
        
        if($template_type == '' && $sms_type == 'bulk'){
            $contact = $contact;
            $template_id = $template_id;
        } else if($template_type == '' && $sms_type == 'single'){ 
            $contact = PHONE_CODE.$contact;
            $template_id = $template_id;
        } else {
            $contact = PHONE_CODE.$contact;
        }
        
        
        $sms_count = ceil((strlen($content)/160));    
        if(((int)$row['pending']>=$sms_count) || ($contact=="9888335156")){
            
            if($send_via == 'textguru'){
                $apiurl = 'https://www.txtguru.in/imobile/api.php';
                $username = '13designstreet';
                $password = '76038518';
                $sender_id='eSALON';
        	   	$url = $apiurl.'?username='.$username.'&password='.$password.'&dltentityid=1501625580000016692&dltheaderid=1505159826226469634&dlttempid='.$template_id.'&source='.$sender_id.'&dmobile='.urlencode($contact).'&message='.urlencode($content);
            } else if($send_via == 'webber'){
                $apiurl = 'https://www.hellotext.live/vb/apikey.php';
                $apikey = 'KlVTx5BwFh29srsc';
                $sender_id = 'eSALON';
        	   	$url = $apiurl.'?apikey='.$apikey.'&senderid='.c.'&templateid='.$template_id.'&number='.urlencode($contact).'&message='.urlencode($content);
            } else if($send_via == 'kaleyra'){
                $sms_status = "";
                $sender_id = 'eSALON';
                $ch = curl_init();
                $url = "https://api.kaleyra.io/v1/HXIN1698532611IN/messages";
                $postData = array(
                  'sender' => $sender_id,
                  'source'  => 'API',
                  'type'    => 'TXN',
                  'template_id'  => $template_id,
                  'to'  => $contact,
                  'body'    => $content
                );
                
                curl_setopt_array($ch,
                  array(
                    CURLOPT_URL => $url,
                    CURLOPT_POST       => true,
                    CURLOPT_POSTFIELDS => $postData,
                    CURLOPT_HTTPHEADER => array('api-key: A515db18f1067a0fb59c513dee839d23a'),
                    CURLOPT_RETURNTRANSFER     => true,
                  )
                );
                
                $data = curl_exec($ch);
                curl_close($ch);
                $character = json_decode($data,true);
                
                if($character != ''){
                    // $sql="UPDATE sms set sent = (sent + ".$sms_count.") where id='1'";
                    // if($sms_type != 'bulk'){
                    //     $result=query($sql,[],$conn);
                    // }
                    
                    if(isset($save_sms) && $save_sms == 1){
                        foreach($character['data'] as $data){
                            // query("INSERT INTO `sms_history` SET `mobile_number`='".$data['recipient']."',`message`='".$character['body']."',`datetime`= '".date('Y-m-d H:i:s')."', `status`='1', `sender`='".$character['sender']."', `type`='".$character['type']."', `source`='".$character['source']."', `template_id`='".$character['template_id']."', `group_id`='".$character['id']."', `created_date_time`='".date('Y-m-d H:i:s')."', `total_count`='".$character['totalCount']."', `message_id`='".$data['message_id']."', `sms_status`='PENDING', `refund_status`='0'",[],$conn2);
                            // $result=query("UPDATE sms set sent = (sent + ".$sms_count.") where id='1'",[],$conn2);
                        }
                        $sms_status = 'success';
                    }
                    return $sms_status;
                }

            } else {
                return false;
                exit();
            }
    	   	
            // echo $url;
            $ch = curl_init();
            curl_setopt($ch,CURLOPT_URL,$url);
            curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
            $return = curl_exec($ch);
            curl_close($ch);
    		$character = json_decode($return,true);
    		$msg =explode(",",$return)[0] ;
            if($msg != ''){
               
                $sql="UPDATE sms set sent = (sent + ".$sms_count.") where id='1'";
                $result=query($sql,[],$conn);
                return 'success';
            } 
        }
    }else{
        $url = $client_sms_api_url.urlencode($contact).'&message='.urlencode($content);
        $ch = curl_init();
        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
        $return = curl_exec($ch);
        curl_close($ch);
		$character = json_decode($return,true);
       }
}	
	
	
?>