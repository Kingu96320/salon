<?php
include_once '../includes/db_include.php';

if(isset($_POST['action']) && $_POST['action'] == 'off_days_list'){
    $week_day = implode(',',$_POST['week_day']);
    $provider_id = $_POST['provider_id'];
    $row_count = get_row_count("SELECT * FROM sp_week_off_days WHERE emp_id = '".$provider_id."' ",[],$conn);
    
    $response = array();
    if($row_count>0){
        $result = query("UPDATE sp_week_off_days SET week_day='".$week_day."' WHERE emp_id='".$provider_id."'",[],$conn);
    }
    else{
        $result = query("INSERT INTO `sp_week_off_days`(`emp_id`,`week_day`) VALUES ('$provider_id','$week_day')",[],$conn);
    }

    $response['status'] = 1; 
    echo json_encode($response);
}

if(isset($_POST['action']) && $_POST['action'] == 'save_off_dates'){
    $startDate = $_POST['startDate'];
    $endDate = $_POST['endDate'];
    $provider_id = $_POST['provider_id'];
   
    $response = array();

    $begin = new DateTime($startDate);
    $end = new DateTime($endDate);
    $end->setTime(0,0,1);

    $daterange = new DatePeriod($begin, new DateInterval('P1D'), $end);

    foreach($daterange as $date){
        $date = $date->format("Y-m-d");
        $result = query("INSERT INTO `sp_holiday`(`emp_id`,`off_dates`) VALUES ('$provider_id','$date')",[],$conn);
    }

    $response['status'] = 1; 
    echo json_encode($response);
}

if(isset($_POST['action']) && $_POST['action'] == 'delete_dates'){
    $id = $_POST['id'];
    $response = array();
    $result = query("UPDATE sp_holiday SET status='0' WHERE id='".$id."'",[],$conn);
    $response['status'] = 1; 
    echo json_encode($response);
}

?>