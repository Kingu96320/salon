<?php 
	include_once './includes/db_include.php';
	$branch_id = $_SESSION['branch_id'];
	if(!isset($_SESSION['user_type'])){
		echo '<meta http-equiv="refresh" content="0; url=dashboard.php" />';die();
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
						<h4>Branches details</h4>
					</div>
					<div class="panel-body">
						<div class="row">
							<div class="col-lg-12">
								<div class="panel-body">
									<div class="table-responsive">       
										<table id="" class="table grid table-bordered no-margin">
											<thead>
												<tr>
													<th>Branch name</th>
													<th>Salon name</th>
													<th>Logo</th>
													<th>Phone</th>
													<th>Email</th>
													<th>Website</th>
													<th>GST</th>
													<th>Working hours</th>
													<th>Status</th>
												</tr>
											</thead>
											<tbody>
												<?php
													$system = query_by_id("SELECT * FROM system WHERE active='0'",[],$conn);
													foreach($system as $res){
														?>
														<tr>
															<td><?= ucfirst(branch_by_id($res['branch_id'])); ?></td>
															<td><?= $res['salon'] ?></td>
															<td>
																<img src="<?= $res['logo'] ?>" style="max-width: 80px;" />
															</td>
															<td><?= $res['phone']!=''?$res['phone']:'--' ?></td>
															<td><?= $res['email']!=''?$res['email']:'--' ?></td>
															<td><?= $res['website']!=''?$res['website']:'--' ?></td>
															<td><?= $res['gst']!=''?$res['gst']:'--' ?></td>
															<td><?= date('h:i A',strtotime($res['shpstarttime'])) ?> to <?= date('h:i A',strtotime($res['shpendtime'])) ?></td>
															<td><?php
																if($branch_id == $res['branch_id']){
																	?>
																	<button type="button" class="btn btn-success btn-xs">Selected</button>
																	<?php
																}
																?>
															</td>
														</tr>
														<?php
													}
												?>
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



<?php 
	include "footer.php";
?>