<?php
include "./includes/db_include.php";
$branch_id = $_SESSION['branch_id'];
if(isset($_GET['id']) && $_GET['id']>0)
{
    $id = $_GET['id'];
    $edit = query_by_id("SELECT sr.*, s.name FROM service_reminder sr LEFT JOIN service s ON s.id = sr.service_id WHERE sr.id=:id AND sr.status = 1 and sr.branch_id='".$branch_id."'",["id"=>$id],$conn)[0];
}
if(isset($_GET['del'])){
	$d = $_GET['del'];
	query("update `service_reminder` set status=0 where id=$d and branch_id='".$branch_id."'",[],$conn);
			$_SESSION['t']  = 1;
			$_SESSION['tmsg']  = "Reminder Deactivated Successfully";
			echo '<meta http-equiv="refresh" content="0; url=services_reminder.php" />';
}

if(isset($_GET['act'])){
	$dd = $_GET['act'];
	query("update `service_reminder` set status=1 where id=$dd and branch_id='".$branch_id."'",[],$conn);
			$_SESSION['t']  = 1;
			$_SESSION['tmsg']  = "Reminder active Successfully";
			echo '<meta http-equiv="refresh" content="0; url=services_reminder.php" />';
}
if(isset($_GET['id']))
{
	$id=$_GET['id'];
}
if(isset($_POST['submit'])){
	$service_id = trim($_POST['ser_id']);
	$days = $_POST['days'];
	$message = $_POST['msg'];
	if($service_id != '' && $days != '' && $message != ''){
	    query("INSERT INTO service_reminder SET service_id = '".$service_id."', days_interval = '".$days."', message = '".$message."', status = 1, branch_id='".$branch_id."'",[],$conn);
	    $_SESSION['t']  = 1;
    	$_SESSION['tmsg']  = "Service reminder Added Successfully";
    	echo '<meta http-equiv="refresh" content="0; url=services_reminder.php" />';
	}
}

if(isset($_POST['edit-submit']))
{
  
    $service_id = trim($_POST['ser_id']);
	$days = $_POST['days'];
	$message = $_POST['msg'];
    
    query("UPDATE `service_reminder` set `service_id`='$service_id',`days_interval`='$days' , `message` = '$message', `status`='1' where id=:id and branch_id='".$branch_id."'",["id"=>$id],$conn);

    $_SESSION['t']  = 1;
    $_SESSION['tmsg']  = "Service reminder updated successfully";
    header('LOCATION:services_reminder.php?id='.$id);die();
}



include "topbar.php";
include "header.php";
include "menu.php";

?>
			<!-- Dashboard wrapper starts -->
			<div class="dashboard-wrapper">

				<!-- Main container starts -->
				<div class="main-container">

					<!-- Row starts -->
					<div class="row gutter">
						
						<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
							<div class="panel">
								<div class="panel-heading">
									<h4>Manage autometic service reminder</h4>
								</div>
								<div class="panel-body">
									<div class="row">
									<form action="" method="post">
										<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
											<div class="form-group">
												<label for="service">Service <span class="text-danger">*</span></label>
												<input type="text" class="form-control ser" name="service" id="service" placeholder="autocomplete" value="<?= isset($edit)?$edit['name']:'';?>" required>
												<input type="hidden" class="ser_id" name="ser_id" id="ser_id" value="<?= isset($edit)?$edit['service_id']:'';?>" />
												<span id="ser-status" class="text-danger"></span>
											</div>
										</div>
										<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
											<div class="form-group">
												<label for="days">Interval days <span class="text-danger">*</span></label>
												<input type="number" step="1" class="form-control" name="days" id="days" placeholder="0" value="<?= isset($edit)?$edit['days_interval']:'';?>" min="0" max="365" required>
											</div>
										</div>
										<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
											<div class="form-group">
												<label for="msg">Message <span class="text-danger">*</span></label>
<textarea name="msg" rows="4" style="resize:none" class="msg form-control" required><?= isset($edit)?$edit['message']:'{name} 
Content goes here
{salon_name}';?></textarea>Client name : {name} , Salon name : {salon_name}
											</div>
										</div>
										<div class="clearfix"></div>
										<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
											<div class="form-group pull-right">
												<label for="userName">  </label>
												<?php if(isset($id))
												{
												?>
												<button type="submit" name="edit-submit" class="btn btn-info"><i class="fa fa-pencil-square-o" aria-hidden="true"></i>Update reminder</button>
												<?php
												}
												else
												{
												?>
												<button type="submit" name="submit" class="btn btn-success"><span class="fa fa-plus mr-left-0" aria-hidden="true"></span>Add reminder</button>
												<?php 
												}
												?>
											</div>
										</div>
									</form>
									</div>
									
									<div class="row">
									<div class="col-lg-12">
									    <div class="panel">
									        <div class="panel-heading">
                    							<h4>Active service reminder's</h4>
                    						</div>
                    						<div class="panel-body"><br />
										        <div class="table-responsive">
										<table class="grid dataTable table-bordered table-stripped no-margin no-footer">
											<thead>
												<tr>
													<th>Service name</th>
													<th>Days interval</th>
													<th>SMS content</th>
													<th>Action</th>
												</tr>
											</thead>
											<tbody>
											<?php
                    						$list = query_by_id("SELECT sr.*, s.name FROM service_reminder sr LEFT JOIN service s ON s.id = sr.service_id WHERE sr.status = 1 and sr.branch_id='".$branch_id."'",[],$conn);
                    						foreach($list as $key=>$row1)
                    						{
											?>
												<tr>
													<td><?php echo $row1['name']; ?></td>
													<th><?php echo $row1['days_interval']; ?></th>
													<th><?php echo $row1['message']; ?></th>
													<td><a href="services_reminder.php?id=<?php echo $row1['id']; ?>"><button class="btn btn-info btn-xs" type="button"><i class="fa fa-pencil" aria-hidden="true"></i>Edit</button></a>
													<a href="services_reminder.php?del=<?php echo $row1['id']; ?>" onclick="return confirm('Are you sure to make it inactive?');">
														<button class="btn btn-danger btn-xs" type="button"><i class="fa fa-times" aria-hidden="true"></i>Inactive</button>
													</a></td>
												</tr>
					                        <?php } ?>
											</tbody>
										</table>
										</div>
										    </div>
										</div>
									</div>
									</div>
									<div class="row">
									<div class="col-lg-12">
									    <div class="panel">
									        <div class="panel-heading">
                    							<h4>Inactive service reminder's</h4>
                    						</div>
                    						<div class="panel-body"><br />
										        <div class="table-responsive">
										<table class="grid dataTable table-bordered table-stripped no-margin no-footer">
											<thead>
												<tr>
													<th>Service name</th>
													<th>Days interval</th>
													<th>SMS content</th>
													<th>Action</th>
												</tr>
											</thead>
											<tbody>
											<?php
                    						$list = query_by_id("SELECT sr.*, s.name FROM service_reminder sr LEFT JOIN service s ON s.id = sr.service_id WHERE sr.status = 0 and sr.branch_id='".$branch_id."'",[],$conn);
                    						foreach($list as $key=>$row1)
                    						{
											?>
												<tr>
													<td><?php echo $row1['name']; ?></td>
													<th><?php echo $row1['days_interval']; ?></th>
													<th><?php echo $row1['message']; ?></th>
													<td>
													    <a href="services_reminder.php?act=<?php echo $row1['id']; ?>" onclick="return confirm('Are you sure to make it active?');">
														    <button class="btn btn-success btn-xs" type="button"><i class="fa fa-check" aria-hidden="true"></i>Active</button>
													    </a>
													</td>
												</tr>
					                        <?php } ?>
											</tbody>
										</table>
										</div>
										    </div>
										</div>
									</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<!-- Row ends -->

				</div>
				<!-- Main container ends -->
			
			</div>
			<!-- Dashboard Wrapper End -->
		
		</div>
		<!-- Container fluid ends -->

<script>

$(document).ready(function(){
    $(".ser").autocomplete({
		source: function(request, response) {
			$.getJSON("ajax/bill.php", { term: request.term,page_info:'service_reminder' }, response);
		},
		minLength: 1,
		select:function (event, ui) {  
			$('#ser_id').val(ui.item.id);
			$('#ser-status').text('');
			check();
		}
	}); 
	
	$(".ser").on('keyup',function(){
	   $('#ser_id').val(''); 
	});
	
	$(".ser").on('blur',function(){
	    if($('#ser_id').val() == ''){
	        $('#ser-status').text('Please select service from dropdown');
	        $('#service').val('');
	    }
	});
});

function check() {
    var id = <?= isset($id)?$id:'0'; ?>;
	var ser_id = $('#ser_id').val();
    jQuery.ajax({
        url: "ajax/bill.php",
        type: "POST",
        dataType : "json",
        data : {page_info : ser_id},
        success:function(response){
           if(response.status == 0){
               $('#ser-status').text(response.msg);
               $('#service').val('');
           } else if(response.status == 1){
               $('#ser-status').text(response.msg);
               $('#service').val('');
           }
        },
        error:function (){}
    });
}
</script>
<?php include "footer.php"; ?>