
<?php
	include "./includes/db_include.php";
	$branch_id = $_SESSION['branch_id'];
	$uid = $_SESSION['uid'];
	
	if(isset($_POST['submit'])){
		$date 	= $_POST['date'];
		$amount = $_POST['amount'];
		$ecat 	= $_POST['ecatt'];
		$descc 	= $_POST['descc'];
		
		query("INSERT INTO `expense`(`date`,`cat`,`amount`,`descc`,`user`,`active`,`branch_id`) VALUES ('$date','$ecat','$amount','$descc','$uid',0,'$branch_id')",[],$conn);			
		$_SESSION['t']  = 1;
		$_SESSION['tmsg']  = "Expense Added Successfully";
		echo '<meta http-equiv="refresh" content="0; url=expenses.php" />';die();
	}
	
	if(isset($_GET['d'])){
		$d = $_GET['d'];
		query("update `expense` set active=1 where id=$d and branch_id='".$branch_id."'",[],$conn);
		
		$_SESSION['t']  = 1;
		$_SESSION['tmsg']  = "Expense Removed Successfully";
		echo '<meta http-equiv="refresh" content="0; url=expenses.php" />';die();
	}
	
	if(isset($_GET['del'])){
		$d = $_GET['del'];
		query("update `expensecat` set active=1 where id=$d and branch_id='".$branch_id."'",[],$conn);
		
		$_SESSION['t']  = 1;
		$_SESSION['tmsg']  = "Expense Category Removed Successfully";
		echo '<meta http-equiv="refresh" content="0; url=expenses.php" />';die();
	}
	
	if(isset($_POST['addcat'])){
		$cat  = $_POST['cat'];
		$cat  = ucfirst($cat);
		query("INSERT INTO `expensecat`(`title`,`active`,`branch_id`) VALUES ('$cat',0,'$branch_id')",[],$conn);
		
		$_SESSION['t']  = 1;
		$_SESSION['tmsg']  = "Expense Category Added Successfully";
		echo '<meta http-equiv="refresh" content="0; url=expenses.php" />';die();
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
					
					<div class="panel-body">
						<div class="row">
							
							<div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
								<div class="panel ">
									<div class="panel-heading">
										<h4>Membership count</h4>
									</div>
									<div class="panel-body">
										
										<div class="row">
											
											<div class="col-lg-12">
												<div class="chart-horiz clearfix">
													<ul class="sales-bar chart">
														<li class="current" title="Total clients">
															<span class="bar" data-number="5679"></span>
															<span class="number">5679</span>
														</li>
														<li class="current" title="Total members">
															<span class="bar" data-number="3458"></span>
															<span class="number">3458</span>
														</li>
														<li class="current" title="Client visited only once">
															<span class="bar" data-number="1934"></span>
															<span class="number">1934</span>
														</li>
													</ul>
												</div>
												
											</div>
											
											<div class="clearfix"></div>
											<br>
											
											<h5 style="text-align:center;"><strong>MEMBERSHIP STATS</strong></h5>
											
											<div id="advertising" class="chart-height1"></div>
											
										</div>
										
									</div>
								</div>
							</div>	
							
							
							<div class="col-lg-8 col-md-8 col-sm-6 col-xs-12">
								<div class="panel">
									<div class="panel-heading">
										<h4>Membership</h4>
									</div>
									<div class="panel-body">
										<a href="add-membership.php"><button class="btn btn-primary btn-lg" type="button">Manage membership type</button></a>
										<a href="manage-offers.php"><button class="btn btn-success btn-lg" type="button">Manage Offers</button></a>
									</div>
								</div>
								<div class="panel ">
									<div class="panel-heading">
										<h4>Reward points wise client's rank</h4>
									</div>
									<div class="panel-body">
										<div class="table-responsive">
											
											<table class="table table-bordered">
												
												<tr>
													<th>Name</th>
													<th>Number</th>
													<th>Membership</th>
													<th>Reward points</th>
													<th>Action</th>
												</tr>
												
												<tr>
													<td>Parbhat Jain</td>
													<td>9888335156</td>
													<td>Silver</td>
													<td>8000</td>
													<td><a href="" class="btn btn-xs btn-primary">View profile</a></td>
												</tr>
												<tr>
													<td>Parbhat Jain</td>
													<td>9888335156</td>
													<td>Silver</td>
													<td>8000</td>
													<td><a href="" class="btn btn-xs btn-primary">View profile</a></td>
												</tr>
												<tr>
													<td>Parbhat Jain</td>
													<td>9888335156</td>
													<td>Silver</td>
													<td>8000</td>
													<td><a href="" class="btn btn-xs btn-primary">View profile</a></td>
												</tr>
												<tr>
													<td>Parbhat Jain</td>
													<td>9888335156</td>
													<td>Silver</td>
													<td>8000</td>
													<td><a href="" class="btn btn-xs btn-primary">View profile</a></td>
												</tr>
												<tr>
													<td>Parbhat Jain</td>
													<td>9888335156</td>
													<td>Silver</td>
													<td>8000</td>
													<td><a href="" class="btn btn-xs btn-primary">View profile</a></td>
												</tr>
												
												
											</table>
											
											
										</div>
									</div>
								</div>
							</div>	
							
							
							
							
							
							
							
						</div>
						
						
					</div>
				</div>
			</div>
			
			<div class="clearfix"></div>
			
			
			
			
		</div>
		<!-- Row ends -->
		
	</div>
	<!-- Main container ends -->
	
</div>
<!-- Dashboard Wrapper End -->

</div>
<!-- Container fluid ends -->
<script>
	function checkAvailability() {
		var cat = $('#scat').val();
		//alert(cat);
		jQuery.ajax({
			url: "autocomplete/checkcat.php?cat="+$("#scat").val(),
			data:'cat='+$("#scat").val(),
			type: "POST",
			success:function(data){
				$("#check-status").html(data);
				if(data === "﻿Invalid Category"){
					$('#scat').val("");
				}
			},
			error:function (){}
		});
	}
	
	
	function showmodal(d,s){
		$.ajax({
			url: "ajax/timeslot.php?date="+d+"&staff="+s,
			type: "POST",
			success:function(data){
				$("#appoint").html(data);
				$("#appointment").modal("show");
			},
			error:function (){}
		});
	}
	
</script>

<script>
	function checkcat() {
		var cat = $('#cat').val();
		jQuery.ajax({
			url: "autocomplete/checkcats.php?cat="+$("#cat").val(),
			data:'cat='+$("#scat").val(),
			type: "POST",
			success:function(data){
				$("#cat-status").html(data);
				if(data === "﻿Already Exist"){
					$('#cat').val("");
				}
			},
			error:function (){}
		});
	}
</script>

<?php 
	include "footer.php"; 
	
	function getcat($cid) {
		global $conn;
		global $branch_id;
		$sql="SELECT * from expensecat where id='$cid' and branch_id='".$branch_id."'";
		$result=query_by_id($sql,[],$conn);
		foreach($result as $row)
		{
			return $row['title'];
		}
	}
	
	function get_user($cid) 
	{
		global $conn;
		global $branch_id;
		$sql="SELECT * from user where id='$cid' and branch_id='".$branch_id."'";
		$result=query_by_id($sql,[],$conn);
		foreach($result as $row)
		{
			return $row['name'];
		}
	}
?>

<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->


<!-- jquery ScrollUp JS -->
<script src="js/scrollup/jquery.scrollUp.js"></script>

<!-- Datepicker -->
<script src="js/jquery-ui.min.js"></script>

<!-- Sparkline Graphs -->
<script src="js/sparkline/retina.js"></script>
<script src="js/sparkline/custom-sparkline.js"></script>

<!-- Horizontal Bar JS -->
<script src="js/horizontal-bar/horizBarChart.min.js"></script>
<script src="js/horizontal-bar/horizBarCustom.js"></script>

<!-- D3 JS -->
<script src="js/d3/d3.v3.min.js"></script>

<!-- C3 Graphs -->
<script src="js/c3/c3.js"></script>
<script src="js/c3/c3.custom.js"></script>

<!-- Gauge JS -->
<script src="js/d3/gauge.js"></script>
<script src="js/d3/gauge-custom.js"></script>

<!-- Rating JS -->
<script src="js/rating/jquery.raty.js"></script>

<!-- JVector Map -->
<script src="js/jvectormap/jquery-jvectormap-1.2.2.min.js"></script>
<script src="js/jvectormap/gdp-data.js"></script>
<script src="js/jvectormap/world-mill-en.js"></script>

