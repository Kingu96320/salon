<?php
	include "../includes/db_include.php";
	$branch_id = $_SESSION['branch_id'];
	if (isset($_REQUEST['staffno'])){
		
	        $startt = date('H:i',strtotime($_REQUEST['startt']));
		$endt = date('H:i',strtotime($_REQUEST['endt']));
		$apdate = $_REQUEST['apdate'];
		$staffno = $_REQUEST['staffno'];
		
                
            
		$check_sp_time = "SELECT * FROM vip_appointment WHERE app_start_time <= '$startt' and app_end_time >= '$endt' and allocated_by = '$staffno' and app_date = '$apdate' ";
		$result = get_row_count($check_sp_time,[],$conn);
                if($result > 0){
    		        echo '{"success":"0","data":'.json_encode($result).'}';
		} else {
			echo '{"success":"1","data":'.json_encode($result).'}';
		}
		
	}
?>