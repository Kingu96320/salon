<?php 
include "./includes/db_include.php";
$branch_id = $_SESSION['branch_id'];
//$uid = $_SESSION['uid'];
$submit = "";
$sdate = "";
$edate = "";
$client = "";
$status = "";
$st = "";
$cl = "";


if(isset($_POST['reset'])){
	echo '<meta http-equiv="refresh" content="0; url=appointmentreport.php" />';die();
}

if(isset($_POST['submit'])){
	
	if($_POST['sdate']!=""){
		$sdate = $_POST['sdate'];
		$edate = $_POST['edate'];
		$dqry = "and doa BETWEEN '$sdate' AND '$edate'";
	}
	
	if($_POST['status']){
		$st = $_POST['status'];
		$status = "and status='".$_POST['status']."' ";//leaduser
	}
	if($_POST['clientid']){
		$cl = $_POST['clientid'];
		$client = "and client='".$_POST['clientid']."' ";
	}
	$submit = $client.$status.$dqry;
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
									<h4>Appointment Reports</h4>
								</div>
								<div class="panel-body">
					<div class="row">
					<form action="" method="post">
					<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
						<div class="form-group row gutter">
							<label class="col-lg-12 control-label">Appointment Status</label>
								<div class="col-lg-12">
									<select class="form-control" name="status">
										<option value="">-- Select a type --</option>
										<option value="Pending" <?php if($st=="Pending") echo "selected"; ?>>Pending</option>
										<option value="Cancelled" <?php if($st=="Cancelled") echo "selected"; ?>>Cancelled</option>
										<option value="Converted" <?php if($st=="Converted") echo "selected"; ?>>Converted</option>				
									</select>
								</div>
						</div>
					</div>
					
					<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
						<div class="form-group row gutter">
							<label class=" control-label">Client :</label>
							<?php if($cl!=0){ ?>
							<input type="text" class="form-control client" id="client" value="<?php echo getclient($cl) ?>" name="client" placeholder="Autocomplete(Name & Phone)">
							<?php }else{ ?>
							<input type="text" class="form-control client" id="client" name="client" placeholder="Autocomplete(Name & Phone)">
							<?php } ?>
							<input type="hidden" value="0" name="clientid" id="clientid" class="clt"> 
						</div>
					</div>
					<script type="text/javascript">
						$(function() {
							autocomplete_ser();										
						});
						function autocomplete_ser(){
							$(".client").autocomplete({
								source: "client.php",
								minLength: 1,	
								//autoFocus: true,
							select: function(event, ui) {
								//event.preventDefault();
								$('#clientid').val(ui.item.id); 
								$('#cont').val(ui.item.cont); 
							 }				
						});	
						}
					</script>					
					<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
						<div class="form-group row gutter">
							<label class=" control-label">From Date :</label>
							<input type="text" class="form-control date" value="<?php echo $sdate; ?>" name="sdate" readonly>
						</div>
					</div>
					
					<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
						<div class="form-group row gutter">
							<label class=" control-label">End Date :</label>
							<input type="text" class="form-control date" value="<?php echo $edate; ?>" name="edate" readonly>
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
					<div class="row">
					<div class="col-lg-12">
						<div class="panel-body">
									<div class="table-responsive">
										<table id="table" class="table table-bordered no-margin">
											<thead>
												<tr>
													<th>Date Of Appointment</th>
													<th>Client</th>
													<th>Contact</th>
													<th>Time</th>
													<th>Status</th>
													<th>Manage</th>
												</tr>
											</thead>
											<tbody>
											<?php
												$sql1="SELECT * from invoice_".$branch_id." where active=0 and invoice=0 and type <> 3 and branch_id='".$branch_id."' ".$submit." order by id desc";
												//echo $sql1;
												$result1=query_by_id($sql1,[],$conn);
												foreach($result1 as $row1){
											?>
												<tr>
													<td><?php echo $row1['doa']; ?></td>
													<?php 
													$cid = $row1['client'];
														$sql2="SELECT * from client where id=$cid and branch_id='".$branch_id."'";
														$result2=query_by_id($sql2,[],$conn);
														if ($result2) {
															foreach($result2 as $row2)
															{
													?>
													
													<td><?php echo $row2['name']; ?></td>
													<td><?php echo $row2['cont']; ?></td>
														<?php }} ?>
													<td><?php echo $row1['itime']; ?></td>
													<td><?php echo $row1['status']; ?></td>
													<?php 
														$srt = $row1['status'];
														$link = "";
														if($srt=="Converted" || $srt=="Cancelled")
															$link = 'onclick="return false;"';
														else
															$link = '';
													?>
													<td><a href="appointment.php?id=<?php echo $row1['id']; ?>" <?php echo $link; ?>><button class="btn btn-info btn-xs" type="button">Edit</button></a> <a href="ceditbilling.php?inv=<?php echo $row1['id']; ?>" <?php echo $link; ?>><button class="btn btn-info btn-xs" type="button">Convert</button></a></td>
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
					<!-- Row ends -->
					
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
		
function getclient($cid){
	global $con;
			$sql="SELECT * from client where id='$cid' and branch_id='".$branch_id."'";
			$result=query_by_id($sql,[],$conn);
			foreach($result as $row)
			{			
				return $row['name']."(".$row['cont'].")";
			}
    }

?>