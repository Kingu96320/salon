<?php
include_once '../includes/db_include.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
$branch_id = $_SESSION['branch_id'];
if(!empty($_POST["con"])) {
	$num = $_POST["con"];
	$sql2="SELECT * from enquiry where cont='$num' and active=0 and branch_id='".$branch_id."'";
	$result2=query_by_id($sql2,[],$conn);
	if($result2)
	{
		echo "1";
	}
}

// Enquiry filter code

if(isset($_POST['filter_type']) && $_POST['filter_type'] == 'enquiry'){
	$start_date = $_POST['startdate'];
	$end_date = $_POST['enddate'];
	$enquiry_for = $_POST['enquiry_for'];
	$enquiry_type = $_POST['enquiry_type'];
	$lead_source = $_POST['lead_source'];
	$lead_user = $_POST['lead_user'];
	// checking both start date and end date
	if($start_date != '' && $end_date !=''){
		$date = "and e.datefollow BETWEEN '".$start_date."' AND '".$end_date."'";
	} else {
		$date = "";
	}

	// checking enquiry for
	if($enquiry_for != ''){
		$enquiry_for = "or e.enquiry='".$enquiry_for."'";
	} else {
		$enquiry_for = "";
	}

	// checking enquiry type
	if($enquiry_type != ''){
		$enquiry_type = "or e.type='".$enquiry_type."'";
	} else {
		$enquiry_type = "";
	}

	// checking lead source
	if($lead_source != ''){
		$lead_source = "or e.leadsource='".$lead_source."'";
	} else {
		$lead_source = "";
	}
	// checking lead user
	if($lead_user != ''){
		$lead_user = "and e.leaduser='".$lead_user."'";
	} else {
		$lead_user = "";
	}

	$i = 1;
	$sql = "SELECT e.* from enquiry e where e.active=0 and e.branch_id='".$branch_id."' ".$date." ".$enquiry_for." ".$enquiry_type." ".$lead_user." ".$lead_source."  order by e.id desc";
	// echo $sql;
	// die();
	$result=query_by_id($sql,[],$conn);
	if($result){
		foreach($result as $row1){ ?>
		<tr>
			<td><input type="checkbox" class="chkk" value="option1" data-contact="<?php echo $row1['cont']; ?>" data-name="<?php echo $row1['customer']; ?>" data-service="<?=($row1['enquiry'] != '')?getEnquiryfor($row1['enquiry']):'Service' ?>" /></td>
			<td><?=$row1['customer']; ?></td>
			<td><?=$row1['email']; ?></td>
			<td><?=$row1['cont']; ?></td>
			<td><?=$row1['datefollow']; ?></td>
			<td><?=$row1['type']; ?></td>
			<td><?=($row1['enquiry'] != '')?getEnquiryfor($row1['enquiry']):'' ?></td>
			<td>
			    <?php
			        if($row1['leadstatus'] == 'Converted'){ ?>
			            <a href="enquiry.php?id=<?php echo $row1['id']; ?>">
        					<button class="btn btn-info btn-xs">
        						<i class="fa fa-eye" aria-hidden="true"></i>View 
        					</button>
        				</a>
			            <button class="btn btn-success btn-xs">
    						<i class="fa fa-check" aria-hidden="true"></i>Converted 
    					</button>
			        <?php } else if($row1['leadstatus'] == 'Close'){ ?>
			            <a href="enquiry.php?id=<?php echo $row1['id']; ?>">
        					<button class="btn btn-info btn-xs">
        						<i class="fa fa-eye" aria-hidden="true"></i>View 
        					</button>
        				</a>
			            <button class="btn btn-danger btn-xs">
    						<i class="fa fa-times" aria-hidden="true"></i>Closed 
    					</button>
			        <?php } else { ?>
			            <a href="enquiry.php?id=<?php echo $row1['id']; ?>">
        					<button class="btn btn-warning btn-xs">
        						<i class="fa fa-pencil-square-o" aria-hidden="true"></i>Edit 
        					</button>
        				</a>
        				<?php if(DELETE_BUTTON_INACTIVE == 'true') { ?>
        				    <a href="#" onclick="return deleteDisabled();">
            					<button class="btn btn-danger btn-xs">
            						<i class="icon-delete"></i>Delete
            					</button>
            				</a>
        				<?php } else { ?>
            				<a href="enquiry.php?d=<?php echo $row1['id']; ?>" onclick="return confirm('Are You Sure?')">
            					<button class="btn btn-danger btn-xs">
            						<i class="icon-delete"></i>Delete
            					</button>
            				</a>
            			<?php } ?>
			        <?php }
			    ?>
			</td>
		</tr>
		<?php 
			$i++;
		} 
	}
}

// All enquiry filter code

if(isset($_POST['filter_type']) && $_POST['filter_type'] == 'allenquiry'){
	$it = 1;
	$sql1="SELECT e.* from enquiry e where e.active=0 and e.branch_id='".$branch_id."'  order by e.id desc";
	$result1=query_by_id($sql1,[],$conn);
	if($result1){
		foreach($result1 as $row1)
		{
		?>
		<tr>
			<td><input type="checkbox" class="chkk" value="option1" data-contact="<?php echo $row1['cont']; ?>" data-name="<?php echo $row1['customer']; ?>" data-service="<?=($row1['enquiry'] != '')?getEnquiryfor($row1['enquiry']):'Service' ?>" /></td>
			<td><?=$row1['customer']; ?></td>
			<td><?=$row1['email']; ?></td>
			<td><?=$row1['cont']; ?></td>
			<td><?=my_date_format($row1['datefollow']); ?></td>
			<td><?=$row1['type']; ?></td>
			<td><?=($row1['enquiry'] != '')?getEnquiryfor($row1['enquiry']):'' ?></td>
			<td>
			    <?php
			        if($row1['leadstatus'] == 'Converted'){ ?>
			            <a href="enquiry.php?id=<?php echo $row1['id']; ?>">
        					<button class="btn btn-info btn-xs">
        						<i class="fa fa-eye" aria-hidden="true"></i>View 
        					</button>
        				</a>
			            <button class="btn btn-success btn-xs">
    						<i class="fa fa-check" aria-hidden="true"></i>Converted 
    					</button>
			        <?php } else if($row1['leadstatus'] == 'Close'){ ?>
			            <a href="enquiry.php?id=<?php echo $row1['id']; ?>">
        					<button class="btn btn-info btn-xs">
        						<i class="fa fa-eye" aria-hidden="true"></i>View 
        					</button>
        				</a>
			            <button class="btn btn-danger btn-xs">
    						<i class="fa fa-times" aria-hidden="true"></i>Closed 
    					</button>
			        <?php } else { ?>
			            <a href="enquiry.php?id=<?php echo $row1['id']; ?>">
        					<button class="btn btn-warning btn-xs">
        						<i class="fa fa-pencil-square-o" aria-hidden="true"></i>Edit 
        					</button>
        				</a>
        				<?php if(DELETE_BUTTON_INACTIVE == 'true') { ?>
        				    <a href="#" onclick="return deleteDisabled();">
            					<button class="btn btn-danger btn-xs">
            						<i class="icon-delete"></i>Delete
            					</button>
            				</a>
        				<?php } else { ?>
            				<a href="enquiry.php?d=<?php echo $row1['id']; ?>" onclick="return confirm('Are You Sure?')">
            					<button class="btn btn-danger btn-xs">
            						<i class="icon-delete"></i>Delete
            					</button>
            				</a>
            			<?php } ?>
			        <?php }
			    ?>
			</td>
		</tr>
		<?php 
			$it++;
		}
	} else { ?>
		<tr><td colspan='8' class="text-center">No record found!!</td></tr>
		<?php 
	}
}
?>