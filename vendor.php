<?php 
include_once './includes/db_include.php';
$branch_id = $_SESSION['branch_id'];
if(isset($_GET['vid']) && $_GET['vid']>0)
{
	$vid=$_GET['vid'];
	$edit=query_by_id("select * from vendor where id=:id and active=0 and branch_id='".$branch_id."'",["id"=>$vid],$conn)[0];
}

if(isset($_POST['edit-submit'])){
	$client     = addslashes(trim($_POST['name']));
	$cont       = addslashes(trim($_POST['cont']));
	$details    = addslashes(trim($_POST['details']));
	$email      = addslashes(trim($_POST['email']));
	$addr       = addslashes(trim($_POST['addr']));
	$gst        = addslashes(trim($_POST['gst']));
	query("UPDATE `vendor` SET `name`='$client',`cont`='$cont',`email`='$email',`address`='$addr',`details`='$details',`gst`='$gst' WHERE id=$vid and branch_id='".$branch_id."'",[],$conn);
	$_SESSION['t']  = 1;
	$_SESSION['tmsg']  = "Vendor Updated Successfully";
	echo '<meta http-equiv="refresh" content="0; url=vendor.php?vid='.$vid.'" />';
}




if(isset($_POST['submit'])){
	$client     = addslashes(trim($_POST['name']));
	$cont       = addslashes(trim($_POST['cont']));
	$details    = addslashes(trim($_POST['details']));
	$email      = addslashes(trim($_POST['email']));
	$addr       = addslashes(trim($_POST['addr']));
	$gst        = addslashes(trim($_POST['gst']));
	query("INSERT INTO `vendor`(`name`,`cont`,`email`,`address`,`details`,`active`,`gst`,`branch_id`) VALUES ('$client','$cont','$email','$addr','$details',0,'$gst','$branch_id')",[],$conn);
	$_SESSION['t']  = 1;
	$_SESSION['tmsg']  = "Vendor Added Successfully";
	echo '<meta http-equiv="refresh" content="0; url=vendor.php" />';
}

if(isset($_GET['did'])){
    if(DELETE_BUTTON_INACTIVE != 'true'){
    	$del=$_GET['did'];
    	query("UPDATE `vendor` SET `active`=1 WHERE id=$del and branch_id='".$branch_id."'",[],$conn);
    	$_SESSION['t']  = 1;
    	$_SESSION['tmsg']  = "Vendor Removed Successfully";
    	echo '<meta http-equiv="refresh" content="0; url=vendor.php" />';
    }
}

include "topbar.php";
include "header.php";
include "menu.php";
?>
<style>
    @media(min-width:768px){
        .w-20{
            width:20%;   
        }
    }
    textarea{
            resize:none;
        }
</style>

			<!-- Dashboard wrapper starts -->
			<div class="dashboard-wrapper">

				<!-- Main container starts -->
				<div class="main-container">

					<!-- Row starts -->
					<div class="row gutter">
						
						<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
							<div class="panel">
								<div class="panel-heading">
							<?php	if($edit){ ?>
									<h4>Update vendor</h4>
							<?php } else {?>
									<h4>Manage product vendor</h4>							
								<?php } ?>	
							</div>
								<div class="panel-body">
								<div class="row">
									<form action="" method="post">
										<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 w-20">
											<div class="form-group">
												<label for="name">Vendor name <span class="text-danger">*</span></label>
												<input type="text" class="form-control" value="<?php echo $edit['name'];?>" name="name" placeholder="Vendor Name" required>
											</div>
										</div>
										<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 w-20">
											<div class="form-group">
												<label for="cont">Contact <span class="text-danger">*</span></label>
												<input type="text" maxlength="10" class="form-control" onBlur="check();contact_no_length($(this), this.value);" name="cont" id="cont" pattern="[0-9]{10}" placeholder="Contact" value="<?php echo $edit['cont']; ?>" onkeyup="this.value = this.value.replace(/[^0-9\.]/g,'');" required>
												<span id="client-status"></span>
												<span style="color:red" id="digit_error"></span>
											</div>
										</div>
										<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 w-20">
											<div class="form-group">
												<label for="email">Email</label>
												<input type="email" class="form-control"  value="<?php echo $edit['email'];?>"name="email" id="email" placeholder="Email">
											</div>
										</div>
										<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 w-20">
											<div class="form-group">
												<label for="addr">Address</label>
												<input type="text" class="form-control" value="<?php echo $edit['address']?>"name="addr" id="addr" placeholder="Address">
											</div>
										</div>
										<div class="col-lg-3 col-md-3 col-sm-6 col-xs-12 w-20">
											<div class="form-group">
												<label for="gst"></label>
												<input type="text" class="form-control" value="<?php echo $edit['gst']?>" name="gst" id="gst" placeholder
											</div>
										</div>
										<div class="clearfix"></div>
										<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
											<div class="form-group">
												<label for="details">Company details</label>
												<textarea name="details" rows="4" class="form-control"><?php echo $edit['details']; ?></textarea>
											</div>
										</div>
										<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
											<div class="form-group text-right">
											<?php if(isset($vid)){ ?>
											<div><button type="submit" name="edit-submit" class="btn btn-info" ><i class="fa fa-pencil-square-o" aria-hidden="true"></i>Update vendor</button></div>	
										<?php }else {?>
												<div><button type="submit" name="submit" class="btn btn-success" ><i class="fa fa-plus" aria-hidden="true"></i>Add vendor</button></div>
												
										<?php }?>	
											</div>
										</div>
									</form>
									</div>
								<br>
								<div class="row">
								<div class="col-lg-12">
            						<div class="panel">
            							<div class="panel-heading heading-with-btn">
            								<h4 class="pull-left">Vendors</h4>
            								<span id="download-btn"></span>					
            								<div class="clearfix"></div>
            							</div>
            							<div class="panel-body">
									        <div class="table-responsive">
										<table class="table table-bordered no-margin table_datatable" id = "vendors_table">
											<thead>
												<tr>
													<th>Name</th>
													<th>Contact Number</th>
													<th>Email</th>
													<th>Address</th>
													<th width="250">Action</th>
												</tr>
											</thead>
											<tbody>
											<?php
											$sql="SELECT * from vendor where active=0 order by id desc";
											$result=query_by_id($sql,[],$conn);
											foreach($result as $row)
											{
											?>
												<tr>
													<td><?php echo $row['name']; ?></td>
													<td><?php echo $row['cont']; ?></td>
													<td><?php echo $row['email']; ?></td>
													<td><?php echo $row['address']; ?></td>
													<td><a href="vendor.php?vid=<?php echo $row['id']; ?>"><button class="btn btn-warning btn-xs" type="button"><i class="fa fa-pencil-square-o" aria-hidden="true"></i>Edit</button></a> <a href="vendorprofile.php?vid=<?php echo $row['id']; ?>"><button class="btn btn-info btn-xs" type="button"><i class="fa fa-user" aria-hidden="true"></i>View Profile</button></a>
													<?php if(DELETE_BUTTON_INACTIVE == 'true'){ ?>
													    <a href="#" onclick="return deleteDisabled();"><button class="btn btn-danger btn-xs" type="button"><i class="icon-delete"></i>Delete</button></a></td>
													<?php } else { ?>
													    <a href="vendor.php?did=<?php echo $row['id']; ?>" onclick="return confirm('Are you sure')"><button class="btn btn-danger btn-xs" type="button"><i class="icon-delete"></i>Delete</button></a></td>
												    <?php } ?>
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
		
		</div>

	<script>
	function check() {
		jQuery.ajax({
			url: "autocomplete/checkvendor.php?p="+$("#cont").val(),
			//data:'p='+$("#prod").val(),
			type: "POST",
			success:function(data){
				$("#client-status").html(data);
				if(data === "Already Exist"){
					$('#cont').val("");
				}
			},
			error:function (){}
		});
	}
	
	$(document).ready(function(){
		var table = $('#vendors_table').DataTable( {
			dom: 'lBfrtip',
			'lengthMenu': [[10, 25, 50, 100, -1], [10, 25, 50, 100, 'All']],
			"aaSorting":[],
			buttons: [	{
				extend: 'excelHtml5',
				text: '<i class="fa fa-file-excel-o"></i> Excel',
				titleAttr: 'Export to Excel',
				title: '<?php echo systemname($conn); ?>',
				exportOptions: {
					columns: ':not(:last-child)',
				}
			},
			// {
			// 	extend: 'csvHtml5',
			// 	text: '<i class="fa fa-file-text-o"></i> CSV',
			// 	titleAttr: 'CSV',
			// 	title: '<?php //echo systemname($conn); ?>',
			// 	exportOptions: {
			// 		columns: ':not(:last-child)',
			// 	}
			// },
			// {
			// 	extend: 'print',
			// 	exportOptions: {
			// 		columns: ':visible'
			// 	},
			// 	customize: function(win) {
			// 		$(win.document.body).find( 'table' ).find('td:last-child, th:last-child').remove();
			// 	}
			// },
			],
			
		} );

		// To move download button in another div out of the dataTables
		var buttons = new $.fn.dataTable.Buttons(table, {
		     buttons: [{
					extend: 'excelHtml5',
					text: '<i class="fa fa-file-excel-o" aria-hidden="true"></i> Export',
					titleAttr: 'Export to Excel',
					title: '<?php echo systemname($conn); ?>',
					exportOptions: {
						columns: ':not(:last-child)',
					}
				}
		    ]
		}).container().appendTo($('#download-btn'));

		buttons[0].classList.add('d-block');
		buttons[0].classList.add('custom-download-btn');
		buttons[0].classList.add('pull-right');
		buttons[0].classList.remove('dt-buttons');
		$('.custom-download-btn a').removeClass('btn-default');
		// $('.custom-download-btn a').attr({"data-toggle":"tooltip","data-placement":"top","data-html":"true"});
		$('.custom-download-btn a').addClass('btn-warning pull-right download-btn mr-left-5');
		
	    
	});
	
	</script>
			
	<?php 
	include "footer.php";

	?>