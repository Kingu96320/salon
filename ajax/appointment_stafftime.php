<?php
	include "../includes/db_include.php";
	$branch_id = $_SESSION['branch_id'];
	if (isset($_REQUEST['id']) && $_REQUEST['id'] >0 ){
		$id = $_REQUEST['id'];
		$time = $_REQUEST['time'];
		$date = $_REQUEST['date'];
		$start_time = $_REQUEST['starttime'];
		$end_time = $_REQUEST['endtime'];
		$app_eid = $_REQUEST['app_eid'];
		if($app_eid > 0){
			$app_query = " and msp.iid <> '$app_eid'";	
		}

		$spstarttime = explode(":",explode(" ",$start_time)[1]);
		$spstarttime = $spstarttime[0].':'.$spstarttime[1].':00';
		$spendtime = explode(":",explode(" ",$end_time)[1]);
		$spendtime = $spendtime[0].':'.$spendtime[1].':00';		

		$arr = array();


		//$sql1="SELECT * from `app_invoice_items` where `staffid`='$id' and ((`start_time` < '$start_time' and `end_time` >'$start_time') OR (`start_time` < '$end_time' and `end_time` > '$end_time')) and app_date='$date' and active=0";

		$check_sp_time = "SELECT * FROM beauticians WHERE starttime <= '$spstarttime' and endtime >= '$spendtime' and id = $id and branch_id='".$branch_id."'";
		//echo $check_sp_time;
		if(get_row_count($check_sp_time,[],$conn)>0){
			$sql_check_staff_time="SELECT 1 FROM `beauticians` where id='$id' and (`starttime` < SUBSTRING_INDEX('$end_time',' ',-1) and `endtime` > SUBSTRING_INDEX(AddTime('$end_time','00:15:00'),' ',-1)) and active='0' and branch_id='".$branch_id."' ";
			//echo '{"success":"'.$sql_check_staff_time.'"}';
			if(get_row_count($sql_check_staff_time,[],$conn)>0){
			$sql1 ="SELECT DISTINCT msp.service_provider FROM `app_multi_service_provider` msp "
			." LEFT JOIN `app_invoice_items_".$branch_id."` aii on aii.id=msp.aii_staffid "
			." LEFT JOIN `app_invoice_".$branch_id."` ai on ai.id = aii.iid "
			." where msp.`service_provider`='$id' and ((aii.`start_time` BETWEEN AddTime('$start_time','00:01:00') and '$end_time') OR (aii.`end_time` BETWEEN AddTime('$start_time','00:01:00') and '$end_time')) and aii.app_date='$date' and aii.active=0 and ai.status != 'Cancelled' and ai.bill_created_status = 0 and ai.branch_id='".$branch_id."' ".$app_query ;
    			if(get_row_count($sql1,[],$conn)>0){
    				$result=	query_by_id($sql1,[],$conn);
    				foreach($sql1 as $row1){
    					$arr['start'] = $row1['start_time'];
    					$arr['end']   = $row1['end_time'];
    					$arr['app_date'] = $row['app_date'];
    				}
    				$arr['spcat'] = ucfirst(strtolower(beautician_category($id)));
    				echo '{"success":"1","data":'.json_encode($arr).'}';
    				}
    			else{
    			    $arr['start'] = date('h:i A',strtotime($spstarttime));
    			    $arr['pid'] = $id;
    			    $arr['pname'] = query_by_id($check_sp_time,[],$conn)[0]['name'];
    			    $arr['spcat'] = ucfirst(strtolower(beautician_category($id)));
    				echo '{"success":"0","data":'.json_encode($arr).'}';
    			}
			}else{
			    $arr['spcat'] = ucfirst(strtolower(beautician_category($id)));
				echo '{"success":"2","test":"","data":'.json_encode($arr).'}';
			}
		} else {
		    $arr['spcat'] = ucfirst(strtolower(beautician_category($id)));
			echo '{"success":"2","data":'.json_encode($arr).'}';
		}
		
	}
?>