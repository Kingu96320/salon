<?php 
include_once './includes/db_include.php';
$branch_id = $_SESSION['branch_id'];
if(isset($_POST['searchclient'])){
	$search = $_POST['searchclient'];

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
									<h4>Search Clients</h4>
								</div>
								<div class="panel-body">
								<div class="row">
									<div class="table-responsive">
										<table id="grid" class="table table-bordered no-margin">
											<thead>
												<tr>
													<th>Name</th>
													<th>Contact Number</th>
													<th>First Visit</th>
													<th>Last Visit</th>
													<th>Gender</th>
													<th>Action</th>
												</tr>
											</thead>
											<tbody>
											<?php
											$sql="SELECT * from client where name like '%$search%' or cont like '%$search%' and active=0 and branch_id='".$branch_id."' order by name asc";
											$result=query_by_id($sql,[],$conn);
											foreach($result as $row){
													?>
												<tr>
													<td><?php echo $row['name']; ?></td>
													<td><?php echo $row['cont']; ?></td>
													<td><?php echo firstvisit($row['id']); ?></td>
													<td><?php echo lastvisit($row['id']); ?></td>
													<td><?php echo $row['gender']; ?></td>
													<td><a href="clients.php?id=<?php echo $row['id']; ?>" ><button class="btn btn-info btn-xs" type="button">Edit</button></a> <a href="clientprofile.php?cid=<?php echo $row['id']; ?>"><button class="btn btn-info btn-xs" type="button">View Profile</button></a></td>
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
					<!-- Row ends -->

				</div>
				<!-- Main container ends -->
			
			</div>
			<!-- Dashboard Wrapper End -->
		
		</div>
		<!-- Container fluid ends -->

	<script>
function check() {
jQuery.ajax({
url: "checkccont.php?p="+$("#cont").val(),
//data:'p='+$("#prod").val(),
type: "POST",
success:function(data){
$("#client-status").html(data);
if(data === "﻿Already Exist"){
	$('#cont').val("");
}
},
error:function (){}
});
}
</script>
		
<?php 
include "footer.php";
}else{
	echo '<meta http-equiv="refresh" content="0; url=dashboard.php" />';die();
}

function firstvisit($uid){
	global $conn;
	global $branch_id;
	$sql="SELECT * from invoice_".$branch_id." where client=$uid and type=2 and branch_id='".$branch_id."' order by id asc limit 1";
	$result=query_by_id($sql,[],$conn);
	if ($result) 
	{	 
		foreach($result as $row)
		{
		return $row['doa'];
		}
	}
	else
	{
		return "NA";
	}
}

function lastvisit($uid){
	global $conn;
	global $branch_id;
	$sql="SELECT * from invoice_".$branch_id." where client=$uid and type=2 and branch_id='".$branch_id."' order by id desc limit 1";
	$result=query_by_id($con,$sql);
	if ($result) 
	{	
		foreach($result as $row)
		{
		return $row['doa'];
		}
	}else{
		return "NA";
	}
}

?>