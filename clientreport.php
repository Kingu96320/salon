<?php 
include_once './includes/db_include.php';
$branch_id = $_SESSION['branch_id'];
$uid = $_SESSION['uid'];
$type = "";
$user = "";

if(isset($_POST['submit'])){
	$type = $_POST['type'];
	if(strlen($type)>0)
		$type = " and type='$type'";
	
	$user = $_POST['user'];
	if(strlen($user)>0)
		$user = " and leaduser='$user'";
	
	$date = $_POST['date'];
	if(strlen($date)>0)
		$date = " and regon='$date'";
	
	$status = $_POST['status'];
	if(strlen($status)>0)
		$status = " and leadstatus='$status'";
	
	$submit = " ".$type." ".$user." ".$date." ".$status;
}
if(isset($_POST['reset'])){
		$type = "";
		$user = "";
		$date = "";
}

if(isset($_POST['rsubmit'])){
	$sdate = $_POST['fdate'];
	$edate = $_POST['tdate'];
	
	if(strlen($sdate)>0||strlen($edate)>0)
		$submit = "and DATE(regon) BETWEEN $sdate AND $edate";
}

$sql="SELECT * from enquiry where active=0 and branch_id='".$branch_id."' $submit order by id desc";
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
									<h4>Reports</h4>
								</div>
								<div class="panel-body">
									<div class="row">
					<form action="" method="post">
					<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
							
									<div class="form-group">
										<label for="userName">Client :</label>
										<select class="form-control" name="user" required>
										<option value="">-Select User-</option>
														<?php
															$sql2="SELECT * from user where branch_id='".$branch_id."' order by username asc";
															$result2=query_by_id($sql2,[],$conn);
															foreach($result2 as $row2) {
															?>
														<option value="<?php echo $row2['id']; ?>"><?php echo $row2['username']; ?></option>
															<?php } ?>
										</select>
									</div>
					</div>
					
					<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
							
									<div class="form-group">
										<label for="userName">Enquiry Type :</label>
										<select class="form-control" name="type">
														<option value="">-- Select a type --</option>
														<option value="Hot">Hot</option>
														<option value="Cold">Cold</option>
														<option value="Warm">Warm</option>	
										</select>
									</div>

					</div>
					
					<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
									<div class="form-group row gutter">
										<label class=" control-label">Date to follow</label>
										<input type="text" class="form-control date" name="date" readonly>
									</div>
					</div>
					
					<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
									<div class="form-group row gutter">
										<label class=" control-label">Status</label>
										<select class="form-control" name="status">
											<option value="">-- Select a type --</option>
											<option value="Pending">Pending</option>
											<option value="Converted">Converted</option>
											<option value="Close">Close</option>
														
										</select>
									</div>
					</div>
					
					<div class="clearfix"></div>
					<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
							<div class="panel-body">
								<div class="form-group row gutter">
												<button type="submit" name="submit" class="btn btn-warning">Submit</button>
												<button type="submit" name="reset" class="btn btn-warning">Reset</button>
								</div>
							</div>
					</div>
					
					</form>
					</div>
								</div>
							</div>
						</div>
					</div>
					<!-- Row ends -->

					<div class="col-lg-12">
						<div class="panel-body">
									<div class="table-responsive">
										<table id="grid" class="table table-bordered no-margin">
											<thead>
												<tr>
													<th>Date of Bill</th>
													<th>Client</th>
													<th>Contact</th>
													<th>Total</th>
													<th>Paid</th>
													<th>Advance</th>
													<th>Type</th>
													<th>Manage</th>
												</tr>
											</thead>
											<tbody>
											<?php
												$sql1="SELECT * from invoice_".$branch_id." where active=0 and type=2 and branch_id='".$branch_id."' order by id desc";
												$result1=query_by_id($sql1,[],$conn);
												foreach($result1 as $row1) {
											?>
												<tr>
													<td><?php echo $row1['doa']; ?></td>
													<td><?php echo getclient($row1['client']); ?></td>
													<td><?php echo getcont($row1['client']); ?></td>
													<td><?php echo $row1['total']; ?></td>
													<td><?php echo $row1['bpaid']; ?></td>
													<td><?php echo $row1['paid']; ?></td>
													<td><?php 
													$ty = $row1['invoice'];
													if($ty==1)
														echo "Bill";
													else 
														echo "Converted";
													?></td>
													<td><a href="billing.php?inv=<?php echo $row1['id']; ?>" onclick="return confirm('Are you sure?');"><button class="btn btn-info btn-xs" type="button">Edit</button></a> <a href="invoice.php?inv=<?php echo $row1['id']; ?>" target="_blank"><button class="btn btn-info btn-xs"  type="button">View</button></a></td>
												</tr>
											<?php } ?>
											</tbody>
										</table>
									</div>
								</div>
					</div>
					
				</div>
				<!-- Main container ends -->
			
			</div>
			<!-- Dashboard Wrapper End -->
		
		</div>
		<!-- Container fluid ends -->

<script>
function checkcont() {
	var cat = $('#cont').val();
jQuery.ajax({
url: "checkenq.php?con="+$("#cont").val(),
//data:'cat='+$("#bcont").val(),
type: "POST",
success:function(data){
$("#cont-status").html(data);
//alert(data);
if ( data.indexOf("Already Exist") > -1 ) {
	$('#cont').val("");
}
},
error:function (){}
});
}
</script>
		
<?php 
include "footer.php";
		
function get_user($user){
	global $conn;
	global $branch_id;
	$sql="SELECT * from user where id=$user and branch_id='".$branch_id."' order by id desc";
	$result=query_by_id($sql,[],$conn);
	if ($result) 
	{
		foreach($result as $row)
		{
		return $row['name'];
		}
	}
}

function getclient($cid){
		global $conn;
		global $branch_id;
		$sql="SELECT * from client where id=$cid and  branch_id='".$branch_id."'";
		$result=query_by_id($sql,[],$conn);
		 
			foreach($result as $row)
			{
			return $row['name'];
			}
		 
	}
	function getcont($cid){
		global $conn;
		global $branch_id;
		$sql="SELECT * from client where id=$cid and branch_id='".$branch_id."'";
		$result=query_by_id($sql,[],$conn);
		foreach($result as $row)
		{
			return $row['cont'];
		}
	}

?>