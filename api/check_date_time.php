<?php
include "../includes/db_include.php";
include_once 'cors.php';

if(isset($_POST['action']) && $_POST['action'] == 'checkdate'){
    $d = $_POST['date'];
    $date = strtotime($d);
    $day = date('w', $date);
    $resultArr = array();
    if($day == '1'){
        $rid = 1; 
    } elseif($day == '2'){
        $rid = 2;
    } elseif($day == '3'){
        $rid = 3;
    } elseif($day == '4'){
        $rid = 4;
    } elseif($day == '5'){
        $rid = 5;
    } elseif($day == '6'){
        $rid = 6;
    } elseif($day == '0'){
        $rid = 7;
    } else {
        $rid = 0;
    }

    $holiday = query("SELECT * FROM working_days_time WHERE id = $rid AND working_status = 1",[],$conn)[0]['id'];
    $holiday_list = query("SELECT count(*) as total FROM holidays_list WHERE date='".$d."' AND status='1'",[],$conn)[0]['total'];
    if($holiday != ''){
        if($holiday_list != 0){
            $resultArr['status'] = '0';
            $resultArr['msg'] = 'Salon is closed on '.my_date_format($d);
        } else {
            $resultArr['status'] = '1';
            $resultArr['msg'] = '';
        }
    } else {
        $resultArr['status'] = '0';
        $resultArr['msg'] = 'Salon is closed on '.my_date_format($d);
    }
    echo json_encode($resultArr);
}


if(isset($_POST['action']) && $_POST['action'] == 'checktime'){
    $comptime = strtotime(str_replace('+',' ',$_POST['time']));
    $time = str_replace('+',' ',$_POST['time']);
    $date = $_POST['date'];
    $date = strtotime($date);
    $day = date('w', $date);
    $resultArr = array();
    if($day == '1'){
        $rid = 1; 
    } elseif($day == '2'){
        $rid = 2;
    } elseif($day == '3'){
        $rid = 3;
    } elseif($day == '4'){
        $rid = 4;
    } elseif($day == '5'){
        $rid = 5;
    } elseif($day == '6'){
        $rid = 6;
    } elseif($day == '0'){
        $rid = 7;
    } else {
        $rid = 0;
    }
    
    $open_time = shopopentime(1);
    $close_time = shopclosetime(1);
    
    if($comptime < strtotime($open_time) || $comptime > strtotime($close_time)){
        $resultArr['status'] = '0';
        $resultArr['msg'] = 'Salon is open between '.date("h:i a", strtotime($open_time)).' to '.date("h:i a", strtotime($close_time));
    } else {
        $resultArr['status'] = '1';
        $resultArr['msg'] = '';
    }
    echo json_encode($resultArr);
}

if(isset($_POST['action']) && $_POST['action'] == 'check_btn_option'){
    $comptime = strtotime($_POST['time']);
    $open_time = shopopentime(1);
    $close_time = shopclosetime(1);
    if($comptime < strtotime($open_time) || $comptime > strtotime($close_time)){
        $resultArr['status'] = '0';
    } else {
        $resultArr['status'] = '1';
    }
    echo json_encode($resultArr);
}

?>