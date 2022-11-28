<?php 
include_once '../includes/db_include.php'; 
$branch_id = $_SESSION['branch_id'];
if(isset($_POST['client_id']) && $_POST['client_id'] > 0){
    $obj = [];
	$client_id = $_POST['client_id'];
    $current_date = my_date_format(date('Y-m-d'));
	$sql="SELECT mdh.*, md.* from `membership_discount_history` mdh"
	    ." LEFT JOIN `membership_discount` md on md.id=mdh.md_id where mdh.client_id='$client_id' and md.status='1' ORDER BY md.id DESC LIMIT 1";
	$result_row = query_by_id($sql,[],$conn);
	if($result_row){
    	foreach($result_row as $result){
    		 $days=$result['validity'];
    		 $prepaid_expiry_date=my_date_format(date('Y-m-d', strtotime($result['time_update']. ' + '.$days.' days')));
    		 if((strtotime($current_date) <= strtotime($prepaid_expiry_date))){
    				$obj["total"]='1';
    				$obj["result"] = $result;
    		} else {
    			$obj["total"]='0';
    		}
    	}
	} else {
		$obj["total"]='0';
	}
	echo json_encode([$obj]);
}
?>