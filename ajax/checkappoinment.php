<?php
	include "../includes/db_include.php";
	$branch_id = $_SESSION['branch_id'];
	if (isset($_REQUEST['room'])){
		$room = $_REQUEST['room'];
		$startt = date('H:i',strtotime($_REQUEST['startt']));
		$endt = date('H:i',strtotime($_REQUEST['endt']));
		$apdate = $_REQUEST['apdate'];
		
                
            
		$check_sp_time = "SELECT * FROM vip_appointment WHERE app_start_time <= '$startt' and app_end_time >= '$endt' and room_id = '$room' and app_date = '$apdate' ";
		$result = get_row_count($check_sp_time,[],$conn);
                
                if($result > 0){
    		        echo '{"success":"0","data1":'.json_encode($result).'}';
		} else {
			echo '{"success":"1","data1":'.json_encode($result).'}';
		}
		
	}
?>