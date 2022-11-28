<?php
	require_once './includes/db_include.php';
	if(!isset($_SESSION['user_type'])){
	    header('LOCATION: dashboard.php');
	}
	$branch_id = $_SESSION['branch_id'];
	$uid = $_SESSION['uid'];
	if(isset($_GET['eid']) && $_GET['eid']>0)
	{
		$eid = $_GET['eid'];
		$edit = query_by_id("SELECT p.*,u.id as unit from products p left join units u on u.id=p.unit where p.id=:eid",["eid"=>$eid],$conn)[0];
	}
	
	if(isset($_GET['del']))
	{
	    if(DELETE_BUTTON_INACTIVE != 'true'){
    		$d = $_GET['del'];
    		query("update `products` set active=1 where id=$d",[],$conn);
    		$_SESSION['t']  = 1;
    		$_SESSION['tmsg']  = "Product Removed Successfully";
    		echo '<meta http-equiv="refresh" content="0; url=products.php" />';die();
	    }
	}
	
	if(isset($_POST['submit']))
	{	
		$name 	    = addslashes(trim($_POST['name']));
		$name 	    = addslashes(trim(ucfirst($name)));
		$price 	    = addslashes(trim($_POST['price']));
		$volume     = addslashes(trim($_POST['volume']));
		$barcode    = addslashes(trim($_POST['barcode']));
		$reward     = addslashes(trim($_POST['reward'])); 
		$unit_name  = addslashes(trim($_POST['role'])); 
		
		query("INSERT INTO `products` set `name`=:name,`price`=:price,`unit`=:unit,`active`=:active,`volume`=:volume,`barcode`=:barcode,`reward`=:reward, `branch_id`='0'",[
									'name'  => $name,
									'price' => $price,
									'unit'  => $unit_name,
									'volume'=> $volume,
									'active'=> 0,
									'barcode'=>$barcode,
									'reward'=>$reward
									],$conn);
		$_SESSION['t']  = 1;
		$_SESSION['tmsg']  = "Product Added Successfully";
		echo '<meta http-equiv="refresh" content="0; url=products.php" />';die();
	}
	if(isset($_POST['edit-submit']))
	{	
		$eid	    = addslashes(trim($_GET['eid']));
		$name 	    = addslashes(trim($_POST['name']));
		$name 	    = addslashes(trim(ucfirst($name)));
		$price 	    = addslashes(trim($_POST['price']));
		$volume     = addslashes(trim($_POST['volume']));
		$barcode    = addslashes(trim($_POST['barcode']));
		$reward     = addslashes(trim($_POST['reward']));
		$unit_name	= addslashes(trim($_POST['role']));
		query("update `products` set `name`=:name,`price`=:price,`unit`=:unit,`active`=:active,`volume`=:volume,`barcode`=:barcode,`reward`=:reward where id=:id",[
										'name'  => $name,
										'price' => $price,
										'unit'  => $unit_name,
										'volume'=> $volume,
										'active'=> 0,
										'barcode'=>$barcode,
										'reward'=>$reward,
										'id'=>$eid],$conn);
		$_SESSION['t']  = 1;
		$_SESSION['tmsg']  = "Product Updated Successfully";
		echo '<meta http-equiv="refresh" content="0; url=products.php?eid='.$_GET['eid'].'" />';die();
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
						<?php
							if(isset($edit)){ ?>
							<h4>Update product</h4>
							<?php } else { ?>
							<h4>Add new product</h4>
							
						<?php } ?> </div>
						<div class="panel-body">
							<div class="row">
								<form action="" method="post" id="main-form">
									<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
										<div class="form-group">
											<label for="name">Product name <span class="text-danger">*</span></label>
											<input type="text" class="form-control" value="<?=isset($edit)?$edit['name']:''?>" name="name" onBlur="checkservice()" id="prod" placeholder="Product name" required>
											<span id="service-status"></span>
										</div>									
									</div>			
									<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
										<div class="form-group">
											<label for="price">PRICE <span class="text-danger">*</span></label>
											<input type="number" class="form-control" value="<?=isset($edit)?$edit['price']:''?>" step="0.01" name="price" placeholder="Price" required>
										</div>
									</div>
									
									<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
										<div class="form-group">
											<label for="volume">Volume <span class="text-danger">*</span></label>
											<input type="number" class="form-control" value="<?=isset($edit)?$edit['volume']:''?>" step="0.01" required name="volume" placeholder="Enter volume" >
										</div>
									</div>
									<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
										<div class="form-group">
											<label for="role">Unit <span class="text-danger">*</span></label>
											<select class="form-control" name="role">
												<?php 		
													$sql	="select * from units where active=0 order by id asc";
													$result	=query_by_id($sql,[],$conn);
													foreach($result as $row)
													{
													?>
													<option value="<?=$row['id']?>" <?php if((isset($edit))&&($edit['unit']==$row['id'])) { echo "selected" ;} ?>><?=$row['name']?></option>
													<?php 
													}  
												?>												
											</select>
											<!--<input class="auto form-control" type="text" name="unit" id="u_unit" placeholder="Enter unit" value="<?=isset($edit)?$edit['unit']:''?>" required>
											<input type="hidden" name="unit_id" id="unit_id">-->						
										</div>
									</div>
									
									<div class="clearfix"></div>
									<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
										<div class="form-group">
											<label for="barcode">Barcode</label>
											<input type="text" class="form-control" value="<?=isset($edit)?$edit['barcode']:''?>" name="barcode" placeholder="Enter barcode" >
										</div>
									</div>
									<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
										<div class="form-group">
											<label for="reward">Reward points on purchase</label>
											<input type="number" class="form-control" step="0.01" name="reward" placeholder="Enter reward point"  value="<?=isset($edit)?$edit['reward']:''?>" >
										</div>
									</div>
									
									
									<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
											<div class="form-group">
												<label>  </label><br>
											<?php	
												if(isset($edit)){												
												?>
												<button type="submit" name="edit-submit" class="btn btn-info pull-left"><i class="fa fa-pencil-square-o" aria-hidden="true"></i>Update product</button>
												<?php } else { ?>
												<button type="submit" name="submit" class="btn btn-success pull-left"><i class="fa fa-plus" aria-hidden="true"></i>Add product</button>
											<?php } ?>		
										</div>
									</div>
								</form>
							</div>
							
							
						</div>
				</div>
			</div>
		</div>
		<!-- Row ends -->
		
		
		<div class="row gutter">
			
			<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
				<div class="panel">
					<div class="panel-heading  heading-with-btn">
						<h4 class="pull-left">Manage product(s)</h4>
						<span id="download-btn"></span>
						<span data-toggle="modal" data-target="#uploadexcel"><button class="btn btn-primary pull-right upload-btn" data-toggle="tooltip" data-placement="top" data-html="true" title="To upload please fill your data in pre-defined excel sheet."><i class="fa fa-upload" aria-hidden="true"></i> Upload</button></span>
						<div class="clearfix"></div>
					</div>
					<div class="panel-body">
						
						
						<div class="row">
							<div class="col-lg-12">
								<div class="table-responsive">
									<table class="table table-bordered no-margin " id="product_table">
										<thead>
											<tr>
												<th>Product name</th>
												<th>PRICE</th>
												<th>Volume</th>
												<th>Reward</th>
												<th>Barcode</th>
												<th width="120">Action</th>
											</tr>
										</thead>
										<tbody>
											<?php
												$sql1="SELECT p.*,u.name as unit_name from products p left join units u on u.id=p.unit where p.active=0 order by p.id DESC ";
												$result1=query_by_id($sql1,[],$conn);
												foreach($result1 as $row1)
												{
												?>
												<tr>
													<td><?php echo $row1['name']; ?></td>
													<td><?php echo number_format($row1['price'],2); ?></td>
													<td><?php echo $row1['volume']." ".$row1['unit_name']; ?></td>
													<td><?= $row1['reward'] ?></td>
													<td><?= $row1['barcode'] ?></td>
													<td><a href="products.php?eid=<?php echo $row1['id']; ?>">
													<button class="btn btn-warning btn-xs" type="button"><i class="fa fa-pencil-square-o" aria-hidden="true"></i>Edit</button></a> 
													<?php if(DELETE_BUTTON_INACTIVE == 'true'){ ?>
													    <a href="#" onclick="return deleteDisabled();">
    														<button class="btn btn-danger btn-xs" type="button"><i class="icon-delete"></i>Delete</button>
    													</a>
													<?php } else { ?>
    													<a href="products.php?del=<?php echo $row1['id']; ?>" onclick="return confirm('Are you sure?');">
    														<button class="btn btn-danger btn-xs" type="button"><i class="icon-delete"></i>Delete</button>
    													</a>
    												<?php } ?>
													</td>
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


<!-- Modal -->
<div id="uploadexcel" class="modal fade disableOutsideClick" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <!-- <button type="button" class="close" data-dismiss="modal">&times;</button> -->
        <button onclick="samplefile('products')" class="btn btn-warning pull-right"><i class="fa fa-download" aria-hidden="true"></i> Sample File (.xlsx)</button>
        <h4 class="modal-title">Upload product(s) </h4>
      </div>
      <div class="modal-body">
		<p id="error_msg" class="text-danger"></p>
		<p>
			<input type="file" name="excelsheet" accept=".xlsx" id="productExcel" />
		</p>
		<div class="notes">
			<p><i class="fa fa-hand-o-right" aria-hidden="true"></i>Important notes:</p>
			<ol>
				<li>* File should be in .xlsx format.</li>
				<li>* Product name, Sale price, Volume, Unit must be filled in sheet. Records with empty field will not upload.</li>
				<li>
					<strong>* Unit representation</strong><br />
					<b>&nbsp;&nbsp;
						<?php 
						$total_unit = count($result);
						$count = 0;
						foreach($result as $row){
							echo $row['name'];
							if(++$count != $total_unit){
								echo ", ";
							}
						} ?>
					</b>
					
				</li>
			</ol>
		</div>
      </div>
      <div class="modal-footer">
      	<button type="button" data-attr="submit" class="btn btn-success" onclick="uploadProducts()"><i class="fa fa-upload" aria-hidden="true"></i> Upload</button>
        <button type="button" class="btn btn-danger" data-dismiss="modal" onclick="clearModalForm('error_msg','uploadexcel')"><i class="fa fa-times" aria-hidden="true"></i> Close</button>
      </div>
    </div>

  </div>
</div>
<!-- Modal to upload excel sheet end  -->

<!-- Container fluid ends -->
<script type="text/javascript" src="./ajax/ajax.js">
</script>
<script>

    $(document).ready(function(){
        		var table = $('#product_table').DataTable( {
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
						columns: ':not(:last-child):not(.not-export-column)',
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
		
    })

	function checkservice() {
		var cat = $('#prod').val().replace(/\s/g,'');
		<?php if(isset($_GET['eid'])){ ?>
			var id = <?=$_GET['eid']?> ;
		<?php } else{ ?>
			var id = 0;
		<?php } ?>
		$.ajax({
			url: "ajax/checkservice.php",
			type: "POST",
			data:{'products' : cat,'id':id},
			success:function(data){
				if(data == '1'){
					$("#service-status").html("Product name already Exists").css("color","red");
					$('#prod').val("");
					}else{
					$("#service-status").html("");
				}
			},
			error:function (){}
		});
	}	



	// function to upload service from excel sheet start
	function uploadProducts(){
		var errordiv = $('#error_msg');
		var submit_btn = $('#uploadexcel button[data-attr=submit]');
		var submit_btn_icon = $('#uploadexcel .btn-success i');
		errordiv.text('');
		var file_data = $('#productExcel').prop('files')[0];
		var form_data = new FormData();
		form_data.append('excelsheet', file_data);
		form_data.append('module','product');
		if(file_data == undefined){
			errordiv.text('Please select file.');
		} else {
			$.ajax({  
	            url:"ajax/upload_products_excel.php",
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


</script>

<?php include "footer.php"; ?>