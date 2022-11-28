<?php
include "../includes/db_include.php";
$branch_id = $_SESSION["branch_id"];
if (isset($_POST['column_name']) && isset($_POST['value']) || isset($_GET['column_name']) ){
	if($_POST['column_name']){
		$column_name = $_POST['column_name'];
	}else{
		$column_name = $_GET['column_name'];
	}
	if($column_name == 'logo'){
        $extensions = array("jpeg" , "jpg" , "png" , "gif");
        $file_tmp = $_FILES['file']['tmp_name'];
        $file_name = $_FILES['file']['name'];
        $dir = '../upload/';
        $ext=pathinfo($file_name,PATHINFO_EXTENSION);
        $file_ext=explode('.',$_FILES['file']['name']);
        $file_ext=end($file_ext); 
        $file_ext = strtolower($file_ext);
	      if(in_array($file_ext,$extensions)){
              if (($_FILES["file-input"]["size"] > 2000000)) {
                    $status = 'maxfilesize';
              } else { 
                $new_name = strtotime(date('Y-m-d h:i:s')).'.'.$ext;      
                $image_path = "$dir".$file_name;
                if(move_uploaded_file($file_tmp,$dir.$new_name)){
                    $source = $dir.$file_name;
                    $destination = $dir.$new_name;
                    $quality = 75;
                    $value = 'upload/'.$new_name;            
    			}
            }
		}else{
            $status = "wrongext";
		}	 
	} else if($column_name == 'loginbg'){
        $extensions = array("jpeg" , "jpg" , "png" , "gif");
        $file_tmp = $_FILES['file']['tmp_name'];
        $file_name = $_FILES['file']['name'];
        $dir = '../upload/login_page_bg/';
        $ext=pathinfo($file_name,PATHINFO_EXTENSION);
        $file_ext=explode('.',$_FILES['file']['name']);
        $file_ext=end($file_ext); 
        $file_ext = strtolower($file_ext);
          if(in_array($file_ext,$extensions)){
             if (($_FILES["file-input"]["size"] > 2000000)) {
                $status = 'maxfilesize';
            } else {        
                $image_path = "$dir".$file_name;
                $new_name = strtotime(date('Y-m-d h:i:s')).'.'.$ext;
                if(move_uploaded_file($file_tmp,$dir.$new_name)){
                    $source = $dir.$file_name;
                    $destination = $dir.$file_name;
                    $quality = 75;
                    $value = 'upload/login_page_bg/'.$new_name;            
                }
            }
        }else{
            $status = "wrongext";
        }    
    }else{
        $value = $_POST['value'];
    }
	if($status=='wrongext'){
        echo "0";
   } else if($status=='maxfilesize'){
        echo "1";
   } else{
        if($column_name == 'shpstarttime' || $column_name == 'shpendtime'){
            //query("UPDATE `working_days_time` set `open_time` = '', `close_time` = '', `working_status`='0'",[],$conn);
            $value = date('H:i',strtotime($value));
            if($column_name == 'shpstarttime'){
                query("UPDATE `working_days_time` set `open_time` = '$value' where branch_id='".$branch_id."'",[],$conn);
            }
            if($column_name == 'shpendtime'){
                query("UPDATE `working_days_time` set `close_time` = '$value' where branch_id='".$branch_id."'",[],$conn);
            }
        }
        $sql=query("UPDATE `system`  set ".$column_name."=:value WHERE active='0' and branch_id='".$branch_id."'",['value'=>$value],$conn);
       
        $_SESSION['t']  = 1;
        $_SESSION['tmsg']  = "System setting updated successfully";
        echo "2";
   }
}

if(isset($_POST['action']) && $_POST['action'] == 'workingdays'){
    $data_array = array();

    $days = 7;

    for($i = 1; $i <= $days; $i++){
        $id = trim($_POST['day'.$i.'id']);
        $day_status = trim($_POST['day'.$i.'_status']);
        $day_work_from = trim(date('H:i',strtotime($_POST['day'.$i.'_work_from'])));
        $day_work_to = trim(date('H:i',strtotime($_POST['day'.$i.'_work_to'])));
        $data = array('id'=>$id, 'day_status'=> $day_status, 'open'=>$day_work_from, 'close'=>$day_work_to);
        array_push($data_array, $data);
    }

    query("UPDATE `working_days_time` set `working_status`='0' where branch_id='".$branch_id."'",[],$conn);

    foreach ($data_array as $day) {
        if($day['id'] != ''){
            $sql = query("UPDATE `working_days_time` set `open_time`=:open, `close_time`=:close, `working_status`=:workingstatus, `status`='1' WHERE id=:id and branch_id='".$branch_id."'",['open'=>$day['open'], 'close'=>$day['close'], 'workingstatus'=>$day['day_status'], 'id'=>$day['id']],$conn);
        }
    }

    $_SESSION['t']  = 1;
    $_SESSION['tmsg']  = "Working days & hours setting updated successfully"; 
}

// function to save api settings

if(isset($_POST['action']) && $_POST['action'] == 'addapisetting'){
    $url = trim($_POST['url']);
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $sender_id = trim($_POST['sender_id']);
    $q = query("UPDATE `sms` set `api_url`='$url', `api_username`='$username', `api_password`='$password', `sender_id`='$sender_id' where `id`='1'",[],$conn);
    $_SESSION['t']  = 1;
    $_SESSION['tmsg']  = "API setting saved successfully"; 
    echo '1';
}


// function select sms template data base on sms type selection

if(isset($_POST['action']) && $_POST['action'] == 'getsmstemplate'){
    $id = trim($_POST['id']);
    $data = query_by_id("SELECT * from sms_templates_".$branch_id." where status=1 and id='$id' and branch_id='".$branch_id."'",[],$conn)[0];
    echo $data['template_detail'];
}

// function to save sms template settings

if(isset($_POST['action']) && $_POST['action'] == 'savesmstemplate'){
    $id = trim($_POST['id']);
    $detail = trim($_POST['detail']);
    query("UPDATE `sms_templates_".$branch_id."` set `template_detail`='$detail' where `id`='$id' and branch_id='".$branch_id."'",[],$conn);
    $_SESSION['t']  = 1;
    $_SESSION['tmsg']  = "SMS template saved successfully"; 
    echo '1';
}

// function to save reminder settings

if(isset($_POST['action']) && $_POST['action'] == 'saveremindersetting'){
    $id = trim($_POST['id']);
    $status = trim($_POST['status']);
    query("UPDATE `automatic_reminders` set `reminder_status`='$status' where `id`='$id'",[],$conn);
    //$_SESSION['t']  = 1;
    //$_SESSION['tmsg']  = "SMS template saved successfully"; 
    echo '1';
}

// function to update extra hours setting

if(isset($_POST['action']) && $_POST['action'] == 'upExtraHours'){
    $status = $_POST['status'];
    if($status != ''){
        query("UPDATE `system` set `extra_hours`='$status' where branch_id='".$branch_id."'",[],$conn);
        return true;
    }
}

// function to save end day report

if(isset($_POST['action']) && $_POST['action'] == 'saveEndDayReport'){
    $id = trim($_POST['id']);
    $sms = trim($_POST['sms']);
    $email = trim($_POST['email']);
    $time = trim(date('H:i',strtotime($_POST['time'])));
    if($id == '' || $id == 0){
        query("INSERT INTO `day_end_report` set `report_time`='$time', `sms_status`='$sms', `email_status`='$email', `status`='1', branch_id='".$branch_id."'",[],$conn);
    } else {
        query("UPDATE `day_end_report` set `report_time`='$time', `sms_status`='$sms', `email_status`='$email' where `id`='$id' and branch_id='".$branch_id."'",[],$conn);
    }

    $_SESSION['t']  = 1;
    $_SESSION['tmsg']  = "Day end report setting saved successfully"; 
    echo "1";
}

// function to save redeem point setting

if(isset($_POST['action']) && $_POST['action'] == 'redeemPoint'){
    $id = trim($_POST['id']);
    $point = trim($_POST['point']);
    $price = trim($_POST['price']);
    $max_point = trim($_POST['max_point']);
    if($id == '' || $id == 0){
        query("INSERT INTO `redeem_point_setting` set `redeem_point`='$point', `price`='$price', `max_redeem_point`='$max_point', `status`='1', branch_id='0'",[],$conn);
    } else {
        query("UPDATE `redeem_point_setting` set `redeem_point`='$point', `price`='$price', `max_redeem_point`='$max_point' where `id`='$id'",[],$conn);
    }

    $_SESSION['t']  = 1;
    $_SESSION['tmsg']  = "Reedem point setting saved successfully"; 
    echo "1";
}

// check date

if(isset($_POST['action']) && $_POST['action'] == 'checkDate'){
    $date = $_POST['date'];
    $return_arr = array();
    $check_holiday = query_by_id("SELECT hl.date from holidays_list as hl where hl.date = '$date' and status=1",[],$conn)[0];
    if($check_holiday != ''){
        $return_arr['status'] = 0;
    } else {
        $day = strtolower(date('l', strtotime($date)));
        $check_day = query_by_id("SELECT open_time, close_time from working_days_time where LOWER(day_name)='$day' and status=1 and working_status=1 and branch_id='".$branch_id."'",[],$conn)[0];
        if($check_day == ''){
            $return_arr['status'] = 0;
        } else {
            $return_arr['status'] = 1;
        }
    }
    echo json_encode($return_arr);
}

// check appointment time
if(isset($_POST['action']) && $_POST['action'] == 'checkapptime'){
    $date = $_POST['date'];
    $return_arr = array();
    $day = strtolower(date("l",strtotime($date)));
    $gettime = query_by_id("SELECT open_time, close_time from working_days_time where LOWER(day_name) = '$day' and status=1 and working_status=1 and branch_id='".$branch_id."'",[],$conn)[0];
    if($gettime != ''){
        $return_arr['status'] = 1;
        $return_arr['starttime'] = $gettime['open_time'];
        $return_arr['endtime'] = $gettime['close_time'];
    } else {
        // $data = query_by_id("SELECT shpstarttime, shpendtime from system where active=0 and id=1",[],$conn)[0];
        $return_arr['status'] = 0;
        // $return_arr['starttime'] = $data['shpstarttime'];
        // $return_arr['endtime'] = $data['shpendtime'];
    }
    echo json_encode($return_arr);
}

// get start & end time
if(isset($_POST['action']) && $_POST['action'] == 'getStartEndTime'){
    $return_arr = array();
    $data = query_by_id("SELECT shpstarttime, shpendtime from system where active=0 and branch_id='".$branch_id."'",[],$conn)[0];
    $return_arr['status'] = 1;
    $return_arr['starttime'] = $data['shpstarttime'];
    $return_arr['endtime'] = $data['shpendtime'];
    echo json_encode($return_arr);
}


// function to add/update holidays
$json = file_get_contents('php://input');
$data = json_decode($json);
if ($data->action && $data->action == "addholidays") {
    $return_arr = array();
    $holiday_data = $data->data;
    query("UPDATE `holidays_list` set `status`='0'",[],$conn);
    for ($i=0; $i < count($holiday_data); $i++) {

        $id = trim($holiday_data[$i][0]);
        $date = trim($holiday_data[$i][1]);
        $description = trim(ucfirst($holiday_data[$i][2]));

        if($id != ''){
            query("UPDATE `holidays_list` set `date`='$date', `description`='$description', `status`='1'  where `id`='$id'",[],$conn);
        } else {
            query("INSERT INTO `holidays_list` set `date`='$date', `description`='$description', `status`='1', `branch_id`='0'",[],$conn);
        }

    }  
    $_SESSION['t']  = 1;
    $_SESSION['tmsg']  = "Holidays list setting saved successfully"; 
    $return_arr['status'] = 1;
    $return_arr['message'] = "success";
    echo json_encode($return_arr);
}


// get logo url for different branches

if(isset($_POST['action']) && $_POST['action'] == 'logo_url'){
    $bid = $_POST['branch_id'];
    $result = query_by_id("SELECT logo, loginbg FROM system WHERE branch_id = '".$bid."'",[],$conn)[0];
    $res['status'] = 1;
    $res['logo_url'] = $result['logo'];
    $res['bg_url'] = $result['loginbg'];
    echo json_encode($res);
}
