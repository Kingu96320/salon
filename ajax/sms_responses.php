<?php
include "../includes/db_include.php";

// GET sms count

if(isset($_POST['action']) && $_POST['action'] == 'get_sms_count'){
    echo query("SELECT (total - sent) as pending from sms where id = '1'", [], $conn)[0]['pending'];
}


//  Save SMS history and deduct message count

if(isset($_POST['action']) && $_POST['action'] == 'save_sms_history'){
    $mobile_number = $_POST['mobile_number'];
    $message = $_POST['message'];
    $datetime = $_POST['datetime'];
    $status = $_POST['status'];
    $sender = $_POST['sender'];
    $type = $_POST['type'];
    $source = $_POST['source'];
    $template_id = $_POST['template_id'];
    $group_id = $_POST['group_id'];
    $created_date_time = $_POST['created_date_time'];
    $total_count = $_POST['total_count'];
    $message_id = $_POST['message_id'];
    $sms_status = $_POST['sms_status'];
    $refund_status = $_POST['refund_status'];
    $sms_count = $_POST['sms_count'];
    
    // save sms history
    
    query("INSERT INTO `sms_history` SET `mobile_number`='".$mobile_number."',`message`='".$message."',`datetime`= '".$datetime."', `status`='".$status."', `sender`='".$sender."', `type`='".$type."', `source`='".$source."', `template_id`='".$template_id."', `group_id`='".$group_id."', `created_date_time`='".$created_date_time."', `total_count`='".$total_count."', `message_id`='".$message_id."', `sms_status`='".$sms_status."', `refund_status`='".$refund_status."'",[],$conn);
    
    // deduct sms  
    
    query("UPDATE sms set sent = (sent + ".$sms_count.") where id='1'",[],$conn);
}

?>