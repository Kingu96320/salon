<?php
    include '../includes/db_include.php';
	$append='';
	$append_order='';
	
	if(isset($_POST['action']) && $_POST['action'] == 'markPresent'){
	    $cid = $_POST['cid'];
	    $response['status'] = 0;
        query("INSERT INTO easybio_attendance (device_id, att_stamp, download_stamp, uid, branch_id) VALUES('1', '".date('Y-m-d H:i:s')."', '".date('Y-m-d H:i:s')."', '".$cid."', '0')", [], $conn);
        $response['status'] = 1;
        echo json_encode($response);
	}
	
	if($_GET['type']==1){
			$pre_id = str_pad(1, 4, '0', STR_PAD_RIGHT);
			$y = $_GET['year'];
			$m = $_GET['month'];
			if((!empty($_GET['suid']))){
			    $suid = str_pad(1, 4, '0', STR_PAD_RIGHT)+$_GET['suid'];
				$append .= " AND ca.uid ='".$suid."' ";
			}
			
			if(empty($append_order)){
				// $append_order = "  GROUP BY ca.uid,date(ca.att_stamp) order by date(ca.att_stamp) asc ";
			}	
				
			$sql="SELECT ca.att_stamp, ca.uid, ci.id as cid,ci.name as fname,ci.cont as contact_no,ds.device_name as DeviceLocation  from `easybio_attendance` ca inner join beauticians ci on (ca.uid= (ci.id+$pre_id)) inner join easybio_devices ds on (ds.id=ca.device_id) WHERE 1=1 ".$append.$append_order;	
			
			
			
			$result =  $sql;
			$get_cust_info = query($result,array('renew' => 0), $conn);
			

			$new_row=''; ?>
			
			<button  class="btn btn-warning" id="print"><i class="fa fa-print" style="margin-left:0px;" aria-hidden="true"></i>Print</button>
			
			<table id="table-dash" class="table table-striped table-bordered " style="margin-top:20px;">
    			<thead>
    			    <tr>
    			        <th style="text-align:center; font-size: 20px;" colspan="32">Monthly Attendance Summery For Month <?= date('F', strtotime($y.'-'.$m.'-01')) ?>/<?= $y ?></th>
    			    </tr>
    				<tr>
    				    <th>Enroll Id: </th>
    				    <th colspan="5"><?= $get_cust_info[0]['uid']  ?></th>
    				    <th colspan="9">Employee Name: </th>
    				    <th colspan="5"><?= $get_cust_info[0]['fname'] ?></th>
    				    <th colspan="9">Ref Id: </th>
    				    <th colspan="3"><?= $get_cust_info[0]['uid']  ?></th>
    				</tr>
    			</thead>
    			<tbody>
    			<tbody>
    			    <tr>
    			        <td>Day</td>
    			        <?php
    			            $start_date = date($y.'-'.$m.'-01');
                            $lastday = date('t',strtotime($start_date));
    			            for($i = 1; $i <= $lastday; $i++){
    			                echo '<td>'.$i.'</td>';
    			            }
    			        ?>
    			    </tr>
    			    <tr>
    			        <td>In</td>
    			        <?php
        			        for($i = 1; $i <= $lastday; $i++){
        			            if($i < 10){
        			                $c = '0'.$i;
        			            } else {
        			                $c = $i;
        			            }
    	           			    $in_time = query_by_id("SELECT att_stamp FROM easybio_attendance WHERE uid='".$get_cust_info[0]['uid']."' AND date(att_stamp) = '".date($y.'-'.$m.'-'.$c)."' ORDER BY id ASC LIMIT 1",[],$conn)[0]['att_stamp'];
        			            if($in_time != ''){
        			                echo '<td>'.my_time_format($in_time).'</td>';
        			            } else {
        			                echo '<td></td>';
        			            }
        			        } 
    			        ?>
    			    </tr>
    			    <tr>
    			        <td>Out</td>
    			        <?php
        			        for($i = 1; $i <= $lastday; $i++){
        			            if($i < 10){
        			                $c = '0'.$i;
        			            } else {
        			                $c = $i;
        			            }
        			            $co = query_by_id("SELECT count(*) as total FROM easybio_attendance WHERE uid='".$get_cust_info[0]['uid']."' AND date(att_stamp) = '".date($y.'-'.$m.'-'.$c)."'",[],$conn)[0]['total'];
        			            if($co >= 2){
        			                $out_time = query_by_id("SELECT att_stamp FROM easybio_attendance WHERE uid='".$get_cust_info[0]['uid']."' AND date(att_stamp) = '".date($y.'-'.$m.'-'.$c)."' ORDER BY id DESC LIMIT 1",[],$conn)[0]['att_stamp'];
        			                if($out_time != ''){
            			                echo '<td>'.my_time_format($out_time).'</td>';
            			            } else {
            			                echo '<td></td>';
            			            }
        			            } else{
        			                echo '<td></td>';
        			            }
        			        } 
    			        ?>
    			    </tr>
    			    <tr>
    			        <td>Total Hrs</td>
    			        <?php
        			        for($i = 1; $i <= $lastday; $i++){
        			            if($i < 10){
        			                $c = '0'.$i;
        			            } else {
        			                $c = $i;
        			            }
    	           			    $in_time = query_by_id("SELECT att_stamp FROM easybio_attendance WHERE uid='".$get_cust_info[0]['uid']."' AND date(att_stamp) = '".date($y.'-'.$m.'-'.$c)."' ORDER BY id ASC LIMIT 1",[],$conn)[0]['att_stamp'];
        			            if($in_time != ''){
        			                $stime = $in_time;
        			            } else {
        			                $stime = '';
        			            }
        			            
        			            $co = query_by_id("SELECT count(*) as total FROM easybio_attendance WHERE uid='".$get_cust_info[0]['uid']."' AND date(att_stamp) = '".date($y.'-'.$m.'-'.$c)."'",[],$conn)[0]['total'];
        			            if($co >= 2){
        			                $out_time = query_by_id("SELECT att_stamp FROM easybio_attendance WHERE uid='".$get_cust_info[0]['uid']."' AND date(att_stamp) = '".date($y.'-'.$m.'-'.$c)."' ORDER BY id DESC LIMIT 1",[],$conn)[0]['att_stamp'];
        			                if($out_time != ''){
            			                $etime = $out_time;
            			            } else {
            			                $etime = '';
            			            }
        			            } else{
        			                $etime = '';
        			            }
        			            
        			            if($stime != '' && $etime != ''){
        			                $datetime1 = new DateTime($stime);
                                    $datetime2 = new DateTime($etime);
        			                $interval = $datetime1->diff($datetime2);
        			                echo '<td>'.$interval->format('%h').":".$interval->format('%i')." Min".'</td>';
        			            } else {
        			                echo '<td></td>';
        			            }
        			        } 
    			        ?>
    			    </tr>
    			    <tr>
    			        <td>Status</td>
    			        <?php 
    			        for($i = 1; $i <= $lastday; $i++){
        			            if($i < 10){
        			                $c = '0'.$i;
        			            } else {
        			                $c = $i;
        			            }
    	           			    $in_time = query_by_id("SELECT att_stamp FROM easybio_attendance WHERE uid='".$get_cust_info[0]['uid']."' AND date(att_stamp) = '".date($y.'-'.$m.'-'.$c)."' ORDER BY id ASC LIMIT 1",[],$conn)[0]['att_stamp'];
        			            if($in_time != ''){
        			                $stime = $in_time;
        			            } else {
        			                $stime = '';
        			            }
        			            
        			            $co = query_by_id("SELECT count(*) as total FROM easybio_attendance WHERE uid='".$get_cust_info[0]['uid']."' AND date(att_stamp) = '".date($y.'-'.$m.'-'.$c)."'",[],$conn)[0]['total'];
        			            if($co >= 2){
        			                $out_time = query_by_id("SELECT att_stamp FROM easybio_attendance WHERE uid='".$get_cust_info[0]['uid']."' AND date(att_stamp) = '".date($y.'-'.$m.'-'.$c)."' ORDER BY id DESC LIMIT 1",[],$conn)[0]['att_stamp'];
        			                if($out_time != ''){
            			                $etime = $out_time;
            			            } else {
            			                $etime = '';
            			            }
        			            } else{
        			                $etime = '';
        			            }
        			            
        			            if($stime != '' && $etime != ''){
        			                echo '<td>P</td>';
        			            } else {
        			                echo '<td></td>';
        			            }
        			        }
        			 ?>
    			    </tr>
    			</tbody>
    		</table>
    <?php
    } 	else if($_GET['type']==2){
			$pre_id = str_pad(2, 4, '0', STR_PAD_RIGHT);
			$y = $_GET['year'];
			$m = $_GET['month'];
			if((!empty($_GET['euid']))){
			    $suid = str_pad(2, 4, '0', STR_PAD_RIGHT)+$_GET['euid'];
				$append .= " AND ca.uid ='".$suid."' ";
			}
			
			if(empty($append_order)){
				// $append_order = "  GROUP BY ca.uid,date(ca.att_stamp) order by date(ca.att_stamp) asc ";
			}	
				
			$sql="SELECT ca.att_stamp, ca.uid, ci.id as cid,ci.name as fname,ci.cont as contact_no,ds.device_name as DeviceLocation  from `easybio_attendance` ca inner join employee ci on (ca.uid= (ci.id+$pre_id)) inner join easybio_devices ds on (ds.id=ca.device_id) WHERE 1=1 ".$append.$append_order;	
			
			$result =  $sql;
			$get_cust_info = query($result,array('renew' => 0), $conn);
			

			$new_row=''; ?>
			
			<button  class="btn btn-warning" id="print"><i class="fa fa-print" style="margin-left:0px;" aria-hidden="true"></i>Print</button>
			
			<table id="table-dash" class="table table-striped table-bordered " style="margin-top:20px;">
    			<thead>
    			    <tr>
    			        <th style="text-align:center; font-size: 20px;" colspan="32">Monthly Attendence Summery For Month <?= date('F', strtotime($y.'-'.$m.'-01')) ?>/<?= $y ?></th>
    			    </tr>
    				<tr>
    				    <th>Enroll Id: </th>
    				    <th colspan="5"><?= $get_cust_info[0]['uid']  ?></th>
    				    <th colspan="9">Employee Name: </th>
    				    <th colspan="5"><?= $get_cust_info[0]['fname'] ?></th>
    				    <th colspan="9">Ref Id: </th>
    				    <th colspan="3"><?= $get_cust_info[0]['uid']  ?></th>
    				</tr>
    			</thead>
    			<tbody>
    			<tbody>
    			    <tr>
    			        <td>Day</td>
    			        <?php
    			            $start_date = date($y.'-'.$m.'-01');
                            $lastday = date('t',strtotime($start_date));
    			            for($i = 1; $i <= $lastday; $i++){
    			                echo '<td>'.$i.'</td>';
    			            }
    			        ?>
    			    </tr>
    			    <tr>
    			        <td>In</td>
    			        <?php
        			        for($i = 1; $i <= $lastday; $i++){
        			            if($i < 10){
        			                $c = '0'.$i;
        			            } else {
        			                $c = $i;
        			            }
    	           			    $in_time = query_by_id("SELECT att_stamp FROM easybio_attendance WHERE uid='".$get_cust_info[0]['uid']."' AND date(att_stamp) = '".date($y.'-'.$m.'-'.$c)."' ORDER BY id ASC LIMIT 1",[],$conn)[0]['att_stamp'];
        			            if($in_time != ''){
        			                echo '<td>'.my_time_format($in_time).'</td>';
        			            } else {
        			                echo '<td></td>';
        			            }
        			        } 
    			        ?>
    			    </tr>
    			    <tr>
    			        <td>Out</td>
    			        <?php
        			        for($i = 1; $i <= $lastday; $i++){
        			            if($i < 10){
        			                $c = '0'.$i;
        			            } else {
        			                $c = $i;
        			            }
        			            $co = query_by_id("SELECT count(*) as total FROM easybio_attendance WHERE uid='".$get_cust_info[0]['uid']."' AND date(att_stamp) = '".date($y.'-'.$m.'-'.$c)."'",[],$conn)[0]['total'];
        			            if($co >= 2){
        			                $out_time = query_by_id("SELECT att_stamp FROM easybio_attendance WHERE uid='".$get_cust_info[0]['uid']."' AND date(att_stamp) = '".date($y.'-'.$m.'-'.$c)."' ORDER BY id DESC LIMIT 1",[],$conn)[0]['att_stamp'];
        			                if($out_time != ''){
            			                echo '<td>'.my_time_format($out_time).'</td>';
            			            } else {
            			                echo '<td></td>';
            			            }
        			            } else{
        			                echo '<td></td>';
        			            }
        			        } 
    			        ?>
    			    </tr>
    			    <tr>
    			        <td>Total Hrs</td>
    			        <?php
        			        for($i = 1; $i <= $lastday; $i++){
        			            if($i < 10){
        			                $c = '0'.$i;
        			            } else {
        			                $c = $i;
        			            }
    	           			    $in_time = query_by_id("SELECT att_stamp FROM easybio_attendance WHERE uid='".$get_cust_info[0]['uid']."' AND date(att_stamp) = '".date($y.'-'.$m.'-'.$c)."' ORDER BY id ASC LIMIT 1",[],$conn)[0]['att_stamp'];
        			            if($in_time != ''){
        			                $stime = $in_time;
        			            } else {
        			                $stime = '';
        			            }
        			            
        			            $co = query_by_id("SELECT count(*) as total FROM easybio_attendance WHERE uid='".$get_cust_info[0]['uid']."' AND date(att_stamp) = '".date($y.'-'.$m.'-'.$c)."'",[],$conn)[0]['total'];
        			            if($co >= 2){
        			                $out_time = query_by_id("SELECT att_stamp FROM easybio_attendance WHERE uid='".$get_cust_info[0]['uid']."' AND date(att_stamp) = '".date($y.'-'.$m.'-'.$c)."' ORDER BY id DESC LIMIT 1",[],$conn)[0]['att_stamp'];
        			                if($out_time != ''){
            			                $etime = $out_time;
            			            } else {
            			                $etime = '';
            			            }
        			            } else{
        			                $etime = '';
        			            }
        			            
        			            if($stime != '' && $etime != ''){
        			                $datetime1 = new DateTime($stime);
                                    $datetime2 = new DateTime($etime);
        			                $interval = $datetime1->diff($datetime2);
        			                echo '<td>'.$interval->format('%h').":".$interval->format('%i')." Min".'</td>';
        			            } else {
        			                echo '<td></td>';
        			            }
        			        } 
    			        ?>
    			    </tr>
    			    <tr>
    			        <td>Status</td>
    			        <?php 
    			        for($i = 1; $i <= $lastday; $i++){
        			            if($i < 10){
        			                $c = '0'.$i;
        			            } else {
        			                $c = $i;
        			            }
    	           			    $in_time = query_by_id("SELECT att_stamp FROM easybio_attendance WHERE uid='".$get_cust_info[0]['uid']."' AND date(att_stamp) = '".date($y.'-'.$m.'-'.$c)."' ORDER BY id ASC LIMIT 1",[],$conn)[0]['att_stamp'];
        			            if($in_time != ''){
        			                $stime = $in_time;
        			            } else {
        			                $stime = '';
        			            }
        			            
        			            $co = query_by_id("SELECT count(*) as total FROM easybio_attendance WHERE uid='".$get_cust_info[0]['uid']."' AND date(att_stamp) = '".date($y.'-'.$m.'-'.$c)."'",[],$conn)[0]['total'];
        			            if($co >= 2){
        			                $out_time = query_by_id("SELECT att_stamp FROM easybio_attendance WHERE uid='".$get_cust_info[0]['uid']."' AND date(att_stamp) = '".date($y.'-'.$m.'-'.$c)."' ORDER BY id DESC LIMIT 1",[],$conn)[0]['att_stamp'];
        			                if($out_time != ''){
            			                $etime = $out_time;
            			            } else {
            			                $etime = '';
            			            }
        			            } else{
        			                $etime = '';
        			            }
        			            
        			            if($stime != '' && $etime != ''){
        			                echo '<td>P</td>';
        			            } else {
        			                echo '<td></td>';
        			            }
        			        }
        			 ?>
    			    </tr>
    			</tbody>
    		</table>
    <?php
}
if(!isset($_POST['action'])){
?>
<style>
    @media print
    {
        header, nav, .panel-heading, #filter_input, footer, .modal, #print {
            display: none;
        }
    
        #table-dash, #table-dash th, #table-dash td {
            border: 1px solid;
            border-collapse: collapse;
        }
        #table-dash td, #table-dash th{
            padding :3px 4px;
        }
        #table-dash{
            width: 100%;
        }
        
    }
</style>
<script>
    $(function() {
        $("#print").click(function (){
            if (window.print) {
                window.print();
            }
        });
    });
</script>
<?php } ?>