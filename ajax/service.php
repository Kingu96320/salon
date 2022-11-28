<?php

include_once '../includes/db_include.php';
$branch_id = $_SESSION['branch_id'];
if(isset($_POST['action']) && $_POST['action'] == 'services_list'){
    $service_list = implode(',',$_POST['services']);
    $provider_id = $_POST['provider_id'];
    $response = array();
    $result = query("UPDATE beauticians SET services='".$service_list."' WHERE id='".$provider_id."' AND active='0' and branch_id='".$branch_id."'",[],$conn);
    $response['status'] = 1; 
    echo json_encode($response);
}

if(isset($_POST['action']) && $_POST['action'] == 'provider_list_by_service'){
    $service_id = explode(',',$_POST['service_id'])[1];
    $appointment_date = $_POST['appointment_date'];
    $timestamp = strtotime($appointment_date);
    $day = date('l', $timestamp);
    $parent = array();
    if(explode(',',$_POST['service_id'])[0] == 'sr'){
        $result = query_by_id("SELECT * FROM beauticians WHERE find_in_set('".$service_id."', services) <> 0 AND active = '0' and type='2' and branch_id='".$branch_id."' ORDER BY name ASC",[],$conn);
        
        if($result){
            foreach($result as $res){
                $sub = array();
                $query = query_by_id("SELECT * FROM sp_week_off_days WHERE emp_id = '".$res['id']."' ",[],$conn);
                $checked_off_days = explode(',',$query[0]['week_day']);

                $query2 = query_by_id("SELECT * FROM sp_holiday WHERE emp_id = '".$res['id']."' ORDER BY id DESC",[],$conn);
                $row_count = get_row_count("SELECT * FROM sp_holiday WHERE emp_id = '".$res['id']."' ",[],$conn);
                $i=0;
                $all_off_dates = array();
                while($row_count>0){
                    $all_off_dates[$i] = $query2[$i]['off_dates'];
                    $i++;
                    $row_count--;
                }
                
                if(!in_array($day, $checked_off_days) && !in_array($appointment_date , $all_off_dates)){
                    $sub['id'] = $res['id'];
                    $sub['name'] = $res['name'];
                    array_push($parent, $sub);
                }
            }
        } else {
            $result = query_by_id("SELECT * FROM beauticians WHERE active = '0' and type='2' and branch_id='".$branch_id."' ORDER BY name ASC",[],$conn);
            if($result){
                foreach($result as $res){
                    $sub = array();
                    $query = query_by_id("SELECT * FROM sp_week_off_days WHERE emp_id = '".$res['id']."' ",[],$conn);
                    $checked_off_days = explode(',',$query[0]['week_day']);

                    $query2 = query_by_id("SELECT * FROM sp_holiday WHERE emp_id = '".$res['id']."' ",[],$conn);
                    $row_count = get_row_count("SELECT * FROM sp_holiday WHERE emp_id = '".$res['id']."' ",[],$conn);
                    $i=0;
                    $all_off_dates = array();
                    while($row_count>0){
                        $all_off_dates[$i] = $query2[$i]['off_dates'];
                        $i++;
                        $row_count--;
                    }
                    
                    if(!in_array($day, $checked_off_days)&& !in_array($appointment_date , $all_off_dates)){
                        $sub['id'] = $res['id'];
                        $sub['name'] = $res['name'];
                        array_push($parent, $sub);
                    }
                }
            }
        }
    } else {
        $result = query_by_id("SELECT * FROM beauticians WHERE active = '0' and type='2' and branch_id='".$branch_id."' ORDER BY name ASC",[],$conn);
        if($result){
            foreach($result as $res){
                $sub = array();
                $query = query_by_id("SELECT * FROM sp_week_off_days WHERE emp_id = '".$res['id']."' ",[],$conn);
                $checked_off_days = explode(',',$query[0]['week_day']);

                $query2 = query_by_id("SELECT * FROM sp_holiday WHERE emp_id = '".$res['id']."' ",[],$conn);
                $row_count = get_row_count("SELECT * FROM sp_holiday WHERE emp_id = '".$res['id']."' ",[],$conn);
                $i=0;
                $all_off_dates = array();
                while($row_count>0){
                    $all_off_dates[$i] = $query2[$i]['off_dates'];
                    $i++;
                    $row_count--;
                }
                
                if(!in_array($day, $checked_off_days)&& !in_array($appointment_date , $all_off_dates)){
                    $sub['id'] = $res['id'];
                    $sub['name'] = $res['name'];
                    array_push($parent, $sub);
                }
            }
        }
    }
    echo json_encode($parent);
}

// if (isset($_REQUEST['id'])){
// 	$id = $_REQUEST['id'];
// 	$arr = array();
// 	$sql1="SELECT * from service where id=$id";
// 	$result1=mysqli_query($con,$sql1);
// 	if ($row1 = mysqli_fetch_array($result1, MYSQLI_ASSOC)) {
// 		$arr['duration'] = $row1['duration'];
// 	}
// 	echo json_encode($arr);
// }
?>