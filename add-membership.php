   
   <?php
	include "./includes/db_include.php";
	
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
			<div class="row">
				<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
					<div class="panel">
						<div class="panel-heading">
							<h4>Add membership type</h4>
						</div>
						<div class="panel-body">
							
							<div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
								<div class="form-group">
									<label for="userName">Membership type name</label>
									<input type="text" class="form-control"  name="date" placeholder="" required>
								</div>
							</div>
							<div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
								<div class="form-group">
									<label for="userName">Min. reward points earned <i class="icon-info2"  title="Membership will be automaticaly granted to client when min. reward points earned."></i> </label>
									<input type="text" class="form-control"  name="date" placeholder="" required>
								</div>
							</div>
							<div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
								<div class="form-group">
									<label for="userName" >Min. billed amount <i class="icon-info2"  title="Membership will be automaticaly granted to client when min. total amount of bills are generated."></i></label>
									<input type="text" class="form-control"  name="date" placeholder="" required>
								</div>
							</div>
							<div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
								<div class="form-group">
									<label for="userName">Membership price <i class="icon-info2"  title="Enter amount if you want to set price for selling membership"></i></label>
									<input type="text" class="form-control"  name="date" placeholder="" required >
								</div>
							</div>
							<div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
								<div class="form-group">
									<label for="userName">Reward points on purchase of memberhsip <i class="icon-info2"  title="Enter reward points that will be earened by client if membership is purchased."></i></label>
									<input type="text" class="form-control"  name="date" placeholder="" required>
								</div>
							</div>
							<div class="col-lg-9">
								<button class="btn btn-primary pull-right" type="button">Add membership type</button>
							</div>
						</div>
					</div>
					<div class="panel ">
						<div class="panel-heading">
							<h4>Manage membership type</h4>
						</div>
						<div class="panel-body">
							<div class="table-responsive">
								
								<table class="table table-bordered">
									
									<tr>
										<th>Name</th>
										
										<th>Action</th>
									</tr>
									
									<tr>
										<td>Parbhat Jain</td>
										
										<td><a href="" class="btn btn-xs btn-primary">Edit</a></td>
									</tr>
								</table>
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
<?php include "footer.php"; ?>