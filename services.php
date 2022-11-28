<?php
	include "./includes/db_include.php";
	$branch_id = $_SESSION['branch_id'];

	if(!isset($_SESSION['user_type'])) {
		header('LOCATION : dashboard.php');
		die();
	}

	if(isset($_GET['id']) && $_GET['id']>0){
		$id = $_GET['id'];
		$edit = query_by_id("SELECT s.*,sc.cat as cat_name from service s "
							." left join servicecat sc on sc.id=s.cat where s.id=:id",["id"=>$id],$conn)[0];
	}
	
	if(isset($_POST['submit']))
	{
		$name = htmlspecialchars(trim($_POST['sname']));
		$name = htmlspecialchars(trim(ucfirst($name)));
		$scat = htmlspecialchars(trim(ucfirst($_POST['scat']))); //category name
		$cat  = $_POST['scatt']; // category id
		$service_for  = $_POST['service_for'];
        if($cat==''){
            $cat_res = query_by_id("SELECT id from servicecat where cat=:cat and active='0'",["cat"=>$scat],$conn);
            if($cat_res){
                $cat = $cat_res[0]['id'];
				}else{
				$cat = get_insert_id("INSERT INTO servicecat set cat=:cat, active='0', branch_id='0'",["cat"=>$scat],$conn); 
			}
		}
		$duration =  preg_replace("/[^0-9]/", "",trim($_POST['duration']));
		$price    =  trim($_POST['price']);
        $points   =  preg_replace("/[^0-9]/", "",trim($_POST['points']));
		
		$service_id = query_by_id("SELECT id from service where LOWER(name)=LOWER(:name) and active='0' ORDER BY id DESC LIMIT 1",["name"=>$name],$conn);
		if($service_id){
			$eid = $service_id[0]['id'];
			query("UPDATE `service` set `cat`=:cat,`duration`=:duration,`price`=:price,`points`=:points,`active`=:active, `service_for`=:service_for where `id`=:eid ",[
				'cat'  => $cat,
				'duration' => $duration,
				'price'    => $price,
				'points'   => $points,
				'active'   => 0, 
				'service_for'   => $service_for, 
				'eid'      => $eid],$conn);	
		}
		else {
    		query("INSERT INTO `service` set `name`=:name,`cat`=:cat,`duration`=:duration,`price`=:price,`points`=:points,`active`=:active, `service_for`=:service_for, branch_id='0'",[
				'name' => $name,
				'cat'  => $cat,
				'duration' => $duration,
				'price'    => $price,
				'points'   => $points,
				'service_for'   => $service_for,
				'active'   => 0 
				],$conn);
    	} 
		
        $_SESSION['t']  = 1;
        $_SESSION['tmsg']  = "Service Added Successfully";
        header('LOCATION:services.php');
        exit();
	}
	if(isset($_POST['edit-submit']))
	{
		$eid  = htmlspecialchars(trim($_POST['eid']));
		$name = htmlspecialchars(trim($_POST['sname']));
		$name = htmlspecialchars(trim(ucfirst($name)));
		$scat = htmlspecialchars(trim(ucfirst($_POST['scat'])));
		$cat  = htmlspecialchars(trim($_POST['scatt']));
		$service_for  = $_POST['service_for'];
		
        $cat_res = query_by_id("SELECT id from servicecat where LOWER(cat)=LOWER(:cat) and active='0'",["cat"=>$scat],$conn);
        if($cat_res){
            $cat = $cat_res[0]['id'];
		}else{
			$cat = get_insert_id("INSERT INTO servicecat set cat=:cat, active='0', branch_id='0'",["cat"=>$scat],$conn); 
		}
		 
		$duration = $_POST['duration'];
		$price    = $_POST['price'];
		$points   = $_POST['points'];
		
		query("UPDATE `service` set `name`=:name,`cat`=:cat,`duration`=:duration,`price`=:price,`points`=:points,`active`=:active, `service_for`=:service_for where `id`=:eid ",[
						'name' => $name,
						'cat'  => $cat,
						'duration' => $duration,
						'price'    => $price,
						'points'   => $points,
						'active'   => 0, 
						'service_for'   => $service_for, 
						'eid'      =>$eid],$conn);
		
		$_SESSION['t']  = 1;
		$_SESSION['tmsg']  = "Service Updated Successfully";
		header('LOCATION:services.php');
		exit();
	}
	
	if(isset($_GET['d'])){
	    if(DELETE_BUTTON_INACTIVE != 'true'){
    		$d = $_GET['d'];
    		query("update `service` set active=1 where id=$d",[],$conn);
            $_SESSION['t']  = 1;
            $_SESSION['tmsg']  = "Service Removed Successfully";
            header('LOCATION:services.php');
            exit();
	    }
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
						<h4><?php if(!isset($id)){ echo 'Add'; } else { echo 'Edit'; } ?> service</h4>
					</div>
					<div class="panel-body">
						<div class="row">
							<form action="" method="post" id="main-form">
								<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
									<div class="form-group">
										<label for="sname">Service name <span class="text-danger">*</span></label>
										<input type="text" class="form-control" name="sname" value="<?= isset($id)?$edit['name']:old('name')?>" id="ser" onBlur="checkservice()" placeholder="Service name" required>
										<span id="service-status"></span>
									</div>
								</div>
								<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
									<div class="form-group">
										<label for="scat">Category <span class="text-danger">*</span></label>
										<input type="text" class="auto form-control" id="scat" name="scat" value="<?= isset($id)?$edit['cat_name']:old('cat_name')?>"  placeholder="Select category" onKeyup="keyup_checkcetegory()" onBlur="checkcat()" required>
									<input type="hidden" name="scatt" id="scatt" value="<?= isset($id)?$edit['cat']:old('cat')?>"></span>
									<span id="check-status"></span>
										
									</div>
								</div>
								
								<div class="col-lg-2 col-md-3 col-sm-3 col-xs-12">
									<div class="form-group">
										<label for="duration">Duration <span class="text-danger">*</span></label>
										<input type="number" class="form-control" name="duration" id="duration" value="<?= isset($id)?$edit['duration']:old('duration')?>" placeholder="In minutes" min="0" required>
									</div>
								</div>
								<div class="col-lg-2 col-md-3 col-sm-3 col-xs-12">
									<div class="form-group">
										<label for="price">Price (Exclusive Taxes) <span class="text-danger">*</span></label>
										<input type="number" class="form-control" step="0.01" name="price" id="price" value="<?= isset($id)?$edit['price']:old('price')?>" placeholder="500" min="0" required>
									</div>
								</div>
								<div class="col-lg-2 col-md-3 col-sm-3 col-xs-12">
									<div class="form-group">
										<label for="points">Reward point(s)</label>
										<input type="number" class="form-control" name="points" id="points" value="<?= isset($id)?$edit['points']:old('points')?>" placeholder="500" min="0" >
									</div>
								</div>
								<div class="clearfix"></div>
								<div class="col-lg-2 col-md-3 col-sm-3 col-xs-12">
								    <div class="form-group">
										<label for="points">Service For</label>
										<select required name="service_for" class="form-control">
										    <option value="">--Select--</option>
										    <option value="1" <?= isset($edit)&&$edit['service_for']=='1'?'selected':'' ?>>Men</option>
										    <option value="2" <?= isset($edit)&&$edit['service_for']=='2'?'selected':'' ?>>Women</option>
										</select>
									</div>
								</div>
								
								<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
									<div class="form-group">
										<?php if(isset($id)){ ?>
											<input type="hidden" name="eid" value="<?=$id?>">
											<a href="services.php">
												<button type="button" class="btn btn-danger pull-right mr-left-5"><i class="fa fa-times" aria-hidden="true"></i>Cancel</button>
											</a>
											<button type="submit" name="edit-submit" class="btn btn-info pull-right form_update_button"><i class="fa fa-pencil-square-o" aria-hidden="true"></i> Update service</button> 
											<?php }else{ ?>
											<button type="submit" name="submit" class="btn btn-success pull-right"><i class="fa fa-plus" aria-hidden="true"></i> Add new service</button>
										<?php } ?>										
									</div>
								</div>
							</form>
						</div>
					</div>
				</div>
				<div class="panel">
					<div class="panel-heading heading-with-btn">
						<h4 class="pull-left">Manage service(s)</h4>
						<span data-toggle="modal" data-target="#uploadexcel"><button class="btn btn-primary pull-right upload-btn" data-toggle="tooltip" data-placement="top" data-html="true" title="To upload please fill your data in pre-defined excel sheet."><i class="fa fa-upload" aria-hidden="true"></i> Upload</button></span>
						<span id="download-btn"></span>
						<button data-toggle="modal" data-target="#add_new_Client_modal" class="btn btn-info pull-right" onClick="catTable();"> <i class="fa fa-eye" aria-hidden="true"></i> View category</button>
						<div class="clearfix"></div>
					</div>
					<div class="panel-body"></div>
						<div class="clearfix"></div>
						<div class="row">
							<div class="col-lg-12">
								<div class="table-responsive">
									<table class="table table-bordered no-margin grid">
										<thead>
											<tr>
												<th>Name</th>
												<th>Service for</th>
												<th>Category</th>
												<th>Duration</th>
												<th>Price</th>
												<th>Reward point</th>
												<th width="150">Action</th>
											</tr>
										</thead>
										<tbody>
											<?php
												$sql1="SELECT s.*,sc.cat as cat_name from service s left join servicecat sc on sc.id=s.cat where s.active='0' and sc.active='0' order by s.id desc";
												$result1 = query_by_id($sql1,[],$conn);
												foreach ($result1 as $key=>$row1) {
												?>
												<tr>
													<td><?php echo htmlspecialchars_decode($row1['name']); ?></td>
													<td><?php if($row1['service_for'] == '1'){ echo 'Men'; } else if($row1['service_for'] == '2'){ echo 'Women'; } else { echo '-'; } ?></td>
													<td><?php echo htmlspecialchars_decode($row1['cat_name']); ?></td>
													<td><?php echo $row1['duration']." Min"; ?></td>
													<td><?php echo $row1['price']; ?></td>
													<td><?php echo $row1['points']; ?></td>
													<td>
													    <a href="services.php?id=<?php echo $row1['id']; ?>">
													        <button class="btn btn-warning btn-xs" type="button"><i class="icon-edit"></i>Edit</button>
													   </a>
													   <?php if(DELETE_BUTTON_INACTIVE == 'true'){ ?>
													        <a href="#" onclick="return deleteDisabled();"><button class="btn btn-danger btn-xs" type="button"><i class="icon-delete"></i>Delete</button></a></td>
													   <?php } else { ?>
													        <a href="services.php?d=<?php echo $row1['id']; ?>" onclick="return confirm('Are you sure?');"><button class="btn btn-danger btn-xs" type="button"><i class="icon-delete"></i>Delete</button></a></td>
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
		<!-- Row ends -->
		
	</div>
	<!-- Main container ends -->
	
</div>
<!-- Dashboard Wrapper End -->

</div>
<!-- Container fluid ends -->

<!-- Modal to upload excel sheet start  -->
<!-- Modal -->
<div id="uploadexcel" class="modal fade disableOutsideClick" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <!-- <button type="button" class="close" data-dismiss="modal">&times;</button> -->
        <button onclick="samplefile('services')" class="btn btn-warning pull-right"><i class="fa fa-download" aria-hidden="true"></i> Sample File (.xlsx)</button>
        <h4 class="modal-title">Upload service(s) </h4>
      </div>
      <div class="modal-body">
		<p id="error_msg" class="text-danger"></p>
		<p>
			<input type="file" name="excelsheet" accept=".xlsx" id="serviceExcel" />
		</p>
		<div class="notes">
			<p><i class="fa fa-hand-o-right" aria-hidden="true"></i>Important notes:</p>
			<ol>
				<li>* File should be in .xlsx format.</li>
				<li>* Service name, Category name, Duration, Price must be filled in sheet. Records with empty field will not upload.</li>
			</ol>
		</div>
      </div>
      <div class="modal-footer">
      	<button type="button" data-attr="submit" class="btn btn-success" onclick="uploadServices()"><i class="fa fa-upload" aria-hidden="true"></i> Upload</button>
        <button type="button" class="btn btn-danger" data-dismiss="modal" onclick="clearModalForm('error_msg','uploadexcel')"><i class="fa fa-times" aria-hidden="true"></i> Close</button>
      </div>
    </div>

  </div>
</div>
<!-- Modal to upload excel sheet end  -->

<!-- Modal -->
<div class="modal fade" id="add_new_Client_modal" role="dialog">
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
	
	$(function(){
		autocomplete_ser();	
	});
	
	// function to upload service from excel sheet start
	function uploadServices(){
		var errordiv = $('#error_msg');
		var submit_btn = $('#uploadexcel button[data-attr=submit]');
		var submit_btn_icon = $('#uploadexcel .btn-success i');
		errordiv.text('');
		var file_data = $('#serviceExcel').prop('files')[0];
		var form_data = new FormData();
		form_data.append('excelsheet', file_data);
		form_data.append('module','service');
		if(file_data == undefined){
			errordiv.text('Please select file.');
		} else {
			$.ajax({  
	            url:"ajax/upload_services_excel.php",
	            method:"POST",
	            data: form_data,
	            contentType:false,
	            processData:false,
	            beforeSend: function() {
	            	submit_btn_icon.removeClass('fa-upload');
	            	submit_btn_icon.addClass('fa-spinner fa-spin');
	            	submit_btn.prop('disabled',true);
				},
	            success:function(data){
	            	// console.log(data);
	                if (data.status == 0) {
	                	errordiv.text(data.message);
	                	submit_btn_icon.removeClass('fa-spinner fa-spin');
	            		submit_btn_icon.addClass('fa-upload');
	                	submit_btn.prop('disabled',false);
	                }
	                else if(data.status == 1){
	                	location.reload(true);
	                }
	            }
	       });
		}
	}
	// function to upload service from excel sheet end

	function catTable(){
		$('#catTable').DataTable().destroy();
		$('#catTable').DataTable({
			'processing': true,
			'serverSide': true,
			'serverMethod': 'post',
			'ajax': {
				'url':'ajax/fetch_category.php',
			},
			'columns': [
			{ data: 'cat' },
			{ data: 'action' },
			],
			'columnDefs': [ {
				'orderable': false, // set orderable false for selected columns
			}],
			
		});
		
	}
	
	function deleteCat(catID){
	    var delete_status = '<?= DELETE_BUTTON_INACTIVE == 'true'?'true':'false'?>';
	    if(delete_status == 'false'){
    		if(confirm("Are you sure")){
    		$('#catTable').DataTable().destroy();
    		$('#catTable').DataTable({
    			'processing': true,
    			'serverSide': true,
    			'serverMethod': 'post',
    			'ajax': {
    				'url':'ajax/fetch_category.php?catID='+catID,
    			},
    			'columns': [
    			{ data: 'cat' },
    			{ data: 'action' },
    			],
    			'columnDefs': [ {
    				'orderable': false, // set orderable false for selected columns
    			}],
    			
    		}); 	
    		} 
	    } else {
	        deleteDisabled();
	    }
	}
	function autocomplete_ser(){
		$(".auto").autocomplete({
			source: "ajax/search.php",
			minLength: 1,
			select: function(event, ui) {
				event.preventDefault();
				$('#scatt').val(ui.item.id); 
				$('#scat').val(ui.item.cat); 
				$("#check-status").html("");
			}	
		});	
	}
	
	// $(document).ready(function(){
	// $("#scat").blur(function(){
	// var dat = $('#scat').val();
	// $.ajax({
	// url: "ajax/search.php",
	// type: "POST",
	// data:{term:dat},
	// success:function(data){
	// var ds = JSON.parse(data.trim());
	// $('#scatt').val(ds[0]['id']); 
	// $('#scat').val(ds[0]['cat']); 
	// },
	// error:function (){}
	// });	
	// });
	// });
	
	function checkcat() {
		var cat = $('#scat').val().replace(/\s/g, '');
		var cat_id=$('#scatt').val();
		$.ajax({
			url: "ajax/checkservice.php",
			data:{category: cat,cat_id:cat_id},
			type: "POST",
			success:function(data){
				if(data == '1'){
					$("#check-status").html("Duplicate category . Please select category from list").css("color","red");
					$('#scat').val("");
					}else{
					$("#check-status").html("");
				}
			},
			error:function (){}
		});
	}
	
	function checkservice() {
		var cat = $('#ser').val().replace(/\s/g,'');
		<?php if(isset($_GET['id'])){ ?>
			var id = <?=$_GET['id']?> ;
			<?php } else{ ?>
			var id = 0;
		<?php } ?>
		$.ajax({
			url: "ajax/checkservice.php",
			type: "POST",
			data:{'ser' : cat,'id':id},
			success:function(data){
				if(data == '1'){
					$("#service-status").html("Service name already Exists").css("color","red");
					$('#ser').val("");
					}else{
					$("#service-status").html("");
				}
			},
			error:function (){}
		});
	}	
	function keyup_checkcetegory(){
		$('#scatt').val("");
	}
</script>

<?php include "footer.php"; ?>		