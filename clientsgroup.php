<?php 
include_once './includes/db_include.php';
$branch_id = $_SESSION['branch_id'];

if(isset($_GET['gid']) && $_GET['gid']>0)
{
    $gid = $_GET['gid'];
    $edit = query_by_id("SELECT * FROM `clientgroups` where id=:id and active = 0 and branch_id='".$branch_id."'",["id"=>$gid],$conn)[0];
}

if(isset($_POST['edit-submit']))
{
	$name = $_POST['name'];
	$name = ucfirst($name);
	$min = $_POST['min'];
	$max = $_POST['max'];
	
	query("UPDATE `clientgroups` SET `name`='$name',`min`='$min',`max`='$max' WHERE `id`=$gid and branch_id='".$branch_id."'",[],$conn);
	$_SESSION['t']  = 1;
	$_SESSION['tmsg']  = "Group Added Successfully";
	echo '<meta http-equiv="refresh" content="0; clientsgroup.php?gid='.$gid.'" />';
}


if(isset($_POST['submit'])){
	$name =	$_POST['name'];
	$name = ucfirst($name);
	$min = $_POST['min'];
	$max = $_POST['max'];
	
	query("INSERT INTO `clientgroups`(`name`,`min`,`max`,`active`,`branch_id`) VALUES ('$name','$min','$max',0,'$branch_id')",[],$conn);
	$_SESSION['t']  = 1;
	$_SESSION['tmsg']  = "Group Added Successfully";
	echo '<meta http-equiv="refresh" content="0; url=clientsgroup.php" />';die();
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
									<h4>Add Client Groups</h4>
								</div>
								<div class="panel-body">
								<div class="row">
									<form action="" method="post">
										<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
											<div class="form-group">
												<label for="userName">Group Title :</label>
												<input type="text" class="form-control" value="<?php echo $edit['name']?>" name="name" placeholder="Group Title" required>
											</div>
										</div>
										<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
											<div class="form-group">
												<label for="userName">Minimum Points :</label>
												<input type="number" class="form-control" value="<?php echo $edit['max']?>" name="min" id="min" placeholder="Minimum Points" required>
												<span id="client-status"></span>
											</div>
										</div>
										<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
											<div class="form-group">
												<label for="userName">Maximum Points :</label>
												<input type="number" class="form-control" value="<?php echo $edit['min']?>" name="max" id="max" placeholder="Maximum Points" required>
											</div>
										</div>
										
										<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
											<div class="form-group">
												<label for="userName">  </label><br>
												
												<?php if(isset($gid))
													
													{
														?>
												<button type="submit" name="edit-submit" class="btn btn-info">Update Group</button>
												
													<?php 
													}
													else
													{ ?>
														
													<button type="submit" name="submit" class="btn btn-info">Add Group</button>
												<?php 
													}												
													?>
											</div>
										</div>
									</form>
									</div>
								<br>
								<div class="row">
								<div class="col-lg-12">
									<div class="table-responsive">
										<table id="grid" class="table table-bordered no-margin">
											<thead>
												<tr>
													<th>Group Title</th>
													<th>Max-Min Points</th>
													<th>Clients in Group</th>
													<th>Manage</th>
												</tr>
											</thead>
											<tbody>
											<?php
											$sql="SELECT * FROM `clientgroups` where active=0 and branch_id='".$branch_id."' order by name asc ";
											$result=query_by_id($sql,[],$conn);
											foreach($result as $row)
											{
													?>
												<tr>
													<td><?php echo $row['name']; ?></td>
													<td><?php echo $row['min']; ?>-<?php echo $row['max']; ?></td>
													<td><?php echo  getpoints($row['min'],$row['max']); ?></td>
													
													
													<td><a href="clientsgroup.php?gid=<?php echo $row['id']; ?>" ><button class="btn btn-info btn-xs" type="button">Edit</button></a> <a href="groupdetails.php?gid=<?php echo $row['id']; ?>" ><button class="btn btn-danger btn-xs" type="button">Details</button></a></td>
													
												
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

function getpoints($min,$max){
	global $conn;
	global $branch_id;
	$count = 0;
	$points = 0;
	$sql="SELECT distinct(client) from invoice_".$branch_id." where type=2 and active=0 and branch_id='".$branch_id."'";
	$result=query_by_id($sql,[],$conn);
	foreach($result as $row) 
	{
		$clnt = $row['client'];
		$points = getsum($clnt);
		if($points>=$min && $points<=$max)
			$count++;
	}
	return $count;
}

function getsum($client){
	global $conn;
	global $branch_id;
	$sum = 0;
	$sql = "SELECT * from invoice_".$branch_id." where type=2 and client='$client' and active=0 and branch_id='".$branch_id."'";
	$result=query_by_id($sql,[],$conn);
	foreach($result as $row) 
	{
		$inv = $row['id'];
		$point = getpoint($inv);
		$sum = $sum + $point;
	}
	return $sum;
}

function getpoint($inv){
	global $conn;
	global $branch_id;
	$sql = "SELECT sum(price) as total from invoice_items_".$branch_id." where iid=$inv and type='Service' and active=0 and branch_id='".$branch_id."'";
	$result=query_by_id($sql,[],$conn);
	if($result) 
	{
		foreach($result as $row)
		{
		return $row['total'];
		}
	}
}
?>