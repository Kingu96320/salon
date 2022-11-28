<?php
	include "./includes/db_include.php";
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
						<h4>Referral</h4>
					</div>
					<div class="panel-body">
						<div class="row">
							<form action="" method="post" id="main-form">
								<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
									<div class="form-group">
										<label for="userName">Enter Name</label>
										<input type="text" class="form-control" name="sname" value="<?= isset($id)?$edit['name']:old('name')?>" id="ser" onBlur="checkservice()" placeholder="Service name" required>
										<span id="service-status"></span>
									</div>
								</div>
								<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
									<div class="form-group">
										<label for="userName">Category</label>
										<input type="text" class="auto form-control" id="scat" name="scat" value="<?= isset($id)?$edit['cat_name']:old('cat_name')?>"  placeholder="Select Category" onKeyup="keyup_checkcetegory()" onBlur="checkcat()" required>
									<input type="hidden" name="scatt" id="scatt" value="<?= isset($id)?$edit['cat']:old('cat')?>"></span>
									<span id="check-status">
										
									</div>
								</div> 
								<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
									<div class="form-group">
										<?php if(isset($id)){ ?>
											<input type="hidden" name="eid" value="<?=$id?>">
											<button type="submit" name="edit-submit" class="btn btn-info pull-right form_update_button" onClick="formLoader($(this),$(this).html());">Update service</button> 
											<?php }else{ ?>
											<button type="submit" name="submit" class="btn btn-info pull-right" onClick="formLoader($(this),$(this).html());">Add new service</button>
										<?php } ?>
										<a href="" data-toggle="modal" data-target="#add_new_Client_modal" class="btn btn-danger pull-left"    onClick="catTable();">View Category</a>
									</div>
								</div>
							</form>
						</div>
						
						<div class="clearfix"></div><br>
						<div class="row">
							<div class="col-lg-12">
								<div class="table-responsive">
									<table class="table table-bordered no-margin table_datatable">
										<thead>
											<tr>
											</tr>
										</thead>
										<tbody>
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

<!-- Modal -->
<div class="modal" id="add_new_Client_modal" role="dialog">
	<div class="modal-dialog  modal-lg">
		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">Manage Categories</h4>
			</div>
			<div class="modal-body">
				<div class="panel-body">
					<div class="row">
						<table id="catTable" class="table table-bordered no-margin">
							<thead>
								<tr> 
									<th>Categories</th>
									<th>Action</th>
								</tr>
							</thead>
						</table> 
					</div> 
				</div>
			</div>
			<br>
			<div class="modal-footer">
			</div>
		</div>
		
	</div>
</div>
<!-- Modal End --> 
<script>
	

</script>

<?php include "footer.php"; ?>		