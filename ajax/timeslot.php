<?php
include_once '../includes/db_include.php';
$branch_id = $_SESSION['branch_id'];
if (isset($_REQUEST['date'])){
	$date = $_REQUEST['date'];
	$staff = $_REQUEST['staff'];
   $sql1="SELECT aii.*,b.name as sp, s.name as service_name,  s.duration as duration, c.name as client_name FROM `app_invoice_items_".$branch_id."` aii  LEFT JOIN `app_invoice_".$branch_id."` ai ON ai.id = aii.iid LEFT JOIN `app_multi_service_provider` amsp on amsp.aii_staffid=aii.id LEFT JOIN `beauticians` b on b.id=amsp.service_provider LEFT JOIN `service` s on s.id = SUBSTRING_INDEX(amsp.service_name,',',-1) LEFT JOIN `client` c on c.id = aii.client  where aii.active=0 and aii.app_date='$date' and amsp.service_provider = '$staff' and ai.status != 'Cancelled' and aii.branch_id='".$branch_id."' order by aii.id asc";
	$result1=query_by_id($sql1,[],$conn);
    foreach ($result1 as $row1 ) {
        echo "<tr>";
        echo "<td>".$row1['sp']."</td>";
        echo "<td>".$row1['client_name']."</td>";
        echo "<td>".$row1['service_name']."</td>";
        echo "<td>".date('h:i A', strtotime($row1['start_time']))."</td>";
        echo "<td>".date('h:i A', strtotime($row1['end_time']))."</td>";
        echo "<td>".$row1['duration']." Min</td>";
        echo "</tr>";
    }
}

if(isset($_POST['action']) && $_POST['action'] == 'todaySchedule'){
	$date = $_POST['date'];
	$sql1="SELECT aii.*,b.name as sp, s.name as service_name, s.duration as duration, c.name as client_name FROM `app_invoice_items_".$branch_id."` aii LEFT JOIN `app_invoice_".$branch_id."` ai ON ai.id = aii.iid  LEFT JOIN `app_multi_service_provider` amsp on amsp.aii_staffid=aii.id LEFT JOIN `beauticians` b on b.id=amsp.service_provider LEFT JOIN `service` s on s.id = SUBSTRING_INDEX(amsp.service_name,',',-1) LEFT JOIN `client` c on c.id = aii.client  where aii.active=0 and aii.app_date='$date' and ai.status != 'Cancelled' and ai.bill_created_status != '1' and aii.branch_id='".$branch_id."'  order by aii.id asc";
	$result1=query_by_id($sql1,[],$conn);
	if(!empty($result1)){
	    foreach ($result1 as $row1 ) { ?>
	        <tr <?= strtotime($row1['end_time']) .','. strtotime(date('Y-m-d H:i:s')) ?> style="background-color:<?= strtotime($row1['end_time']) < strtotime(date('Y-m-d H:i:s'))?'#ffcbcb':'#cefdce'?>">
	       	<?php
	        echo "<td>".$row1['sp']."</td>";
	        echo "<td>".$row1['client_name']."</td>";
	        echo "<td>".$row1['service_name']."</td>";
	        echo "<td>".date('h:i A', strtotime($row1['start_time']))."</td>";
	        echo "<td>".date('h:i A', strtotime($row1['end_time']))."</td>";
	        echo "<td>".$row1['duration']." Min</td>";
	        echo "</tr>";
	    }
	} else {
		echo "<tr><td colspan='6' class='text-center'>No schedule available..</td></tr>";
	}
}

function getservice($id){
	global $con;
	$sql="SELECT * from service where id='$id'";
	$result=mysqli_query($con,$sql);
    if ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
		return $row['name'];
	}
}
?>