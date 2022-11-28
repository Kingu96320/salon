<?php
	include_once './includes/db_include.php';
    $branch_id = $_SESSION['branch_id'];
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
						<h4>Mark attendance</h4>
					</div>
					<div class="panel-body">
						<div class="row">
							<div class="col-lg-12">
								<div class="table-responsive">
									<table class="table table-bordered no-margin">
										<thead>
											<tr>
												<th>Name</th>
												<th>Contact Number</th>
												<th>Type</th>
												<th>Attendence id</th>
												<th>Action</th>
											</tr>
										</thead>
										<tbody>
											<?php
												$sql1="SELECT * from beauticians where active=0 and type=2 and branch_id='".$branch_id."' order by name asc";
												$result1=query_by_id($sql1,[],$conn);
												foreach ($result1 as $row1) {
												?>
												<tr>
													<td><?php echo $row1['name']; ?></td>
													<td><?php echo $row1['cont']; ?></td>
													<td>Service provider</td>
													<td><b><?php echo str_pad(1, 4, '0', STR_PAD_RIGHT)+$row1['id']; $att_id = str_pad(1, 4, '0', STR_PAD_RIGHT)+$row1['id']; ?></b></td>
													<td><button type="button" onclick="markAttendance('<?= $att_id ?>')" class="btn btn-info btn-sm">Mark attendance</button></td>
												</tr>
											<?php } ?>
											<?php
												$sql1="SELECT * from employee where active=0 and branch_id='".$branch_id."' order by name asc";
												$result1=query_by_id($sql1,[],$conn);
												foreach($result1 as $row1) {
											?>
												<tr>
													<td><?php echo $row1['name']; ?></td>
													<td><?php echo $row1['cont']; ?></td>
													<td>Staff</td>
													<td><b><?php echo str_pad(2, 4, '0', STR_PAD_RIGHT)+$row1['id']; $att_id = str_pad(2, 4, '0', STR_PAD_RIGHT)+$row1['id']; ?></b></td>
													<td><button type="button" onclick="markAttendance('<?= $att_id ?>')" class="btn btn-info btn-sm">Mark attendance</button></td>
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

</div>
<?php 
	include "footer.php";
?>
<script>
    function markAttendance(id){
        $.ajax({
           url      : "ajax/attendance.php",
           type     : "post",
           dataType : "json",
           data     : {action : "markPresent", cid : id},
           success  : function(data){
               if(data.status == 1){
                    toastr.success("Attendance has been marked !");
                }
                else{
                    toastr.error("Could not Mark Attendance, Please try again.");
                }
           },
           error    : function(data){
               toastr.error("Internal Server Error");
           }
        });
    }
</script>