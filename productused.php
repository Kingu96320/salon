<?php 
	include_once './includes/db_include.php';
	$branch_id = $_SESSION['branch_id'];
	$uid = $_SESSION['uid'];
	
	if(isset($_GET['sid'])>0)
	{	
		$sid=$_GET['sid'];
		$edit=query_by_id("SELECT pu.*,p.name,p.id as product_id ,b.id as staff,u.name as user,p.volume,p.unit as volume_unit from productused pu LEFT JOIN products p on p.id=SUBSTRING_INDEX(pu.product,',',-1) LEFT JOIN beauticians b on b.id=pu.staff LEFT JOIN user u on u.id=pu.user where pu.id='$sid' and pu.active=0 and pu.branch_id='".$branch_id."' order by pu.id desc",[],$conn)[0];
	}
	
	if(isset($_POST['submit'])){
		
		for($t=0;$t<count($_POST["products"]);$t++){
			$product    = addslashes(trim($_POST["products"][$t]));
			$prc        = addslashes(trim($_POST["quantity"][$t]));
			$staffid    = addslashes(trim($_POST["staffid"][$t]));
			$notes      = addslashes(trim($_POST["notes"][$t]));
			$stock_id   = addslashes(trim($_POST["stock_id"][$t]));
			$date       = addslashes(trim(date('Y-m-d H:i:s')));
			
			$aid = get_insert_id("INSERT INTO `productused`(`product`,`staff`,`quantity`,`user`,`notes`,`date`,`active`,`product_stock_batch`,`branch_id`) VALUES ('$product','$staffid','$prc','$uid','$notes','$date ',0,'$stock_id','$branch_id')",[],$conn);
			
			for($i=0;$i<$prc;$i++){
				$product_id		  = addslashes(trim(explode(",",$_POST['products'][$t])[1]));
				$product_name	  = addslashes(trim($_POST['product'][$t]));
				$volume			  = addslashes(trim($_POST['volume'][$t]));	
				$unit			  = addslashes(trim($_POST['volume_unit'][$t]));
				query("INSERT INTO `service_slip_add_products` set 	`purchase_id`='$aid',`product_id`='$product_id',`product_volume`='$volume',`product_unit`='$unit',unused_volume='$volume', branch_id='".$branch_id."'",[],$conn); 
			}
		}
		
		$_SESSION['t']  = 1;
		$_SESSION['tmsg']  = "Record Inserted Successfully";
		echo '<meta http-equiv="refresh" content="0; url=productused.php" />';die();
	}
	
	if(isset($_POST['edit-submit'])){
		$sid=$_GET['sid'];
		
		for($t=0;$t<count($_POST["products"]);$t++){
			$product    = addslashes(trim($_POST["products"][$t]));
			$prc        = addslashes(trim($_POST["quantity"][$t]));
			$staffid    = addslashes(trim($_POST["staffid"][$t]));
			$notes      = addslashes(trim($_POST["notes"][$t]));
			$stock_id   = addslashes(trim($_POST["stock_id"][$t]));
			$date       = addslashes(trim(date('Y-m-d H:i:s')));
			query("Update `productused` set `product`='$product',`staff`='$staffid',`quantity`='$prc',`user`='$uid',`notes`='$notes',`date`='$date',`active`=0, `product_stock_batch`='$stock_id' where id='$sid' and branch_id='".$branch_id."'",[],$conn);
			 
			query("DELETE from `service_slip_add_products` where purchase_id='$sid' and branch_id='".$branch_id."'",[],$conn);
			for($i=0;$i< $prc;$i++){
				$product_id		  = addslashes(trim(explode(",",$_POST['products'][$t])[1]));
				$product_name	  = addslashes(trim($_POST['product'][$t]));
				$volume			  = addslashes(trim($_POST['volume'][$t]));	
				$unit			  = addslashes(trim($_POST['volume_unit'][$t]));
				query("INSERT INTO `service_slip_add_products` set `purchase_id`='$sid',`product_id`='$product_id',`product_volume`='$volume',`product_unit`='$unit',unused_volume='$volume', branch_id='".$branch_id."'",[],$conn); 
				 
			}
		}
		
		$_SESSION['t']  = 1;
		$_SESSION['tmsg']  = "Record Updated Successfully";
		echo '<meta http-equiv="refresh" content="0; url=productused.php?sid='.$sid.'" />';die();
	}
	
	if(isset($_GET['del']) && $_GET['del']>0){
	    if(DELETE_BUTTON_INACTIVE != 'true'){
    		$delete_id =$_GET['del'];
    		query("UPDATE `productused` set `active`='1' where `id`='$delete_id' and active='0' and branch_id='".$branch_id."'",[],$conn);
    		query("DELETE FROM `service_slip_add_products` where `purchase_id`='$delete_id' and branch_id='".$branch_id."' ",[],$conn);
    		$_SESSION['t']  = 1;
    		$_SESSION['tmsg']  = "Record Deleted Successfully";
    		echo '<meta http-equiv="refresh" content="0; url=productused.php" />';die();
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
						<h4>Give product to service provider</h4>
					</div>
					<div class="panel-body">
						<form action="" method="post" id="main-form">
							
							<div class="clearfix"></div>
							
							<div class="table-responsive">
								<table id="myTable" class="table table-bordered">
									<thead>
										<tr>
											<th style="width:20%" colspan="2">Products</th>
											<th style="width:3%">Quantity</th>
											<th style="width:15%">Service provider</th>
											<th style="width:15%">Remarks</th>
										</tr>
									</thead>
									<tbody>
										
										<tr  id="TextBoxContainer" class="TextBoxContainer">
											<td class="sno text-center" style="vertical-align:middle;"><span class="icon-dots-three-vertical"></span></td>
											<td><input type="text" class="ser form-control sal" id="service" name="product[]" onBlur="" placeholder="Product (Autocomplete)" value="<?=isset($edit)?$edit['name']:""?>" required> 
												<input type="hidden" name="products[]" class="serr" value="<?=isset($edit)?$edit['product']:""?>">
												<input type="hidden" name="volume[]" class="volume" value="<?=isset($edit)?$edit['volume']:""?>">
												<input type="hidden" name="stock_id[]" class="stock_id" value="<?=isset($edit)?$edit['product_stock_batch']:""?>">
												<input type="hidden" name="volume_unit[]" class="unit" value="<?=isset($edit)?$edit['volume_unit']:""?>">
											</td>
											<td><input type="number" class="qt form-control sal" id="quantity" name="quantity[]" value="<?=isset($edit)?$edit['quantity']:"0"?>" min='1' required></td>
											<td><select name="staffid[]" data-validation="required" required class="form-control">
												<option value="">Select Service Provider</option>
												<?php 
													$sql1="SELECT * FROM `beauticians` where active=0 and branch_id='".$branch_id."' order by name asc";
													$result1=query_by_id($sql1,[],$conn);
													foreach($result1 as $row1) {
													?>
													<option value="<?php echo $row1['id']; ?>"<?php if (isset($edit)){ if ($row1['id']==$edit['staff']) { echo "selected";} } ?>> <?php echo $row1['name']; ?></option>
													
												<?php } ?>
											</select>
											</td>
											<td><input type="text" class="form-control" id="quantity" name="notes[]" value="<?=isset($edit)?$edit['notes']:""?>"placeholder="Remarks"></td>
										</tr>
										
										<tr id="addBefore">
											<td colspan="5"><button type="button" id="btnAdd" class="btn btn-success pull-right"><i class="fa fa-plus" aria-hidden="true"></i>Add product</button></td>
											
										</tr>
										
										<tr>
											<?php if(isset($edit)) { ?> 
												<td colspan="5"><button type="submit" name="edit-submit" class="btn btn-info pull-right"><i class="fa fa-pencil-square-o" aria-hidden="true"></i>Update</button></td>
												<?php } else { ?>
												<td colspan="5"><button type="submit" name="submit" class="btn btn-success pull-right">Submit</button></td>
											<?php } ?>
											
										</tr>
									</tbody>
								</table>
							</div>
							
						</form>	
						
						<script type="text/javascript">
							$(function() {
								autocomplete_ser();										
								
							});
							function autocomplete_ser(){
								$(".ser").autocomplete({
									source: "autocomplete/product.php",
									minLength: 1
								});	
							}
						</script>
					</div>
					
					
				</div>
			</div>
		</div>
		<!-- Row ends -->
		
		
		<div class="row gutter">
			
			<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
				<div class="panel">
					<div class="panel-heading">
						<h4 class="pull-left">Use product in salon history</h4>
						<span id="download-btn"></span>
						<div class="clearfix"></div>
					</div>
					<div class="panel-body">
						<div class="row"> 
							<div class="clearfix"></div> 
							<div class="col-lg-12">
								<div class="table-responsive">
									<table class="table table-bordered no-margin table_datatable" id="product_used">
										<thead>
											<tr>
												
												<th>Product</th>
												<th>Service Provider</th>
												<th>Quantity</th>
												<th>Assigned by</th>
												<th>Date / Time</th>
												<th width="130">Action</th>
											</tr>
										</thead>
										<tbody>
											<?php
												$sql1="SELECT pu.*,p.name,b.name as staff,u.name as user from productused pu LEFT JOIN products p on p.id=SUBSTRING_INDEX(pu.product,',',-1) LEFT JOIN beauticians b on b.id=pu.staff LEFT JOIN user u on u.id=pu.user where pu.active=0 and pu.branch_id='".$branch_id."' order by pu.id desc";
												$result1=query_by_id($sql1,[],$conn);
												foreach($result1 as $row1) 
												{
												?>
												<tr>
													<td><?php echo $row1['name']; ?></td>
													<td><?php echo $row1['staff']; ?></td>
													<td><?php echo $row1['quantity']; ?></td>
													<td><?php echo $row1['user']; ?></td>
													<td><?php echo date('d-m-Y h:i:s a',strtotime($row1['date'])); ?></td>
													<td>
													    <a href="productused.php?sid=<?php echo $row1['id']; ?>">
													        <button class="btn btn-warning btn-xs" type="button">
													            <i class="fa fa-pencil-square-o" aria-hidden="true"></i>Edit
													       </button>
													   </a> 
													   <?php if(DELETE_BUTTON_INACTIVE == 'true'){ ?>
													        <a href="#" onclick="return deleteDisabled();"><button class="btn btn-danger btn-xs" type="button"><i class="icon-delete"></i>Delete</button></a></td>
													   <?php } else { ?>
													        <a href="productused.php?del=<?php echo $row1['id']; ?>" onclick="return confirm('Are you sure?');"><button class="btn btn-danger btn-xs" type="button"><i class="icon-delete"></i>Delete</button></a></td>
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
		<!-- Main container ends -->
		
	</div>
	<!-- Dashboard Wrapper End -->
	
</div>
<!-- Container fluid ends -->

</div>
<div class="copy hide">
	<div class="control-group input-group" style="margin-top:10px">
		<input type="text" name="addmore[]" class="form-control" placeholder="Enter Name Here">
		<div class="input-group-btn"> 
			<button class="btn btn-danger remove" type="button"><i class="glyphicon glyphicon-remove"></i> Remove</button>
		</div>
	</div>
</div>


<script type='text/javascript'>//<![CDATA[
	
	$(document).ready(function(){
	   barcode_scanner();
	   var table = $('#product_used').DataTable( {
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
 
	$(function(){
		autocomplete_serr();
		change_event();
	});
	$("#btnAdd").bind("click", function() {
		var clonetr = $("#TextBoxContainer").clone().addClass('TextBoxContainer');
		clonetr.removeAttr('id');
		clonetr.find("table.add_row").remove();
		clonetr.find('.sno').html('<span class="remm icon-trash2 " style="color:red;" onclick="$(this).parent().parent().remove();"></span>');
		clonetr.find('input').val('');
		clonetr.find('.staff option[value=""]').prop('selected',true);
		$("#addBefore").before(clonetr);
		autocomplete_serr();
		$('.TextBoxContainer').last().children().find('.qt').removeAttr('readonly');
		$('.TextBoxContainer').last().children().find('.package_service_quantity').remove();
		$('.TextBoxContainer').last().children().find('.package_service_inv').remove();
		change_event();
		barcode_scanner();
	});
	
	// <!------function to find duplicate service_provider---->
	function findDuplicate(e){
		duplicate_arr=[];
		var row=$(".TextBoxContainer");
		var val=row.find('.serr');
		val.each(function(){
			duplicate_arr.push($(this).val());
		});
		
		for(var i=0;i<duplicate_arr.length;i++){
			for(var j=i+1;j<duplicate_arr.length;j++){
				if(duplicate_arr[i]==duplicate_arr[j]){
					e.parents('.TextBoxContainer').remove();
					toastr.warning("Duplicate Entry");
				}  
			}
		}
	}
	// <!----END---->
	
	
	function barcode_scanner(){
        $(".ser").on('blur',function(){
            var barcode = $(this).val();
            var row = $(this).parents('tr');
            $.ajax({
                url  : "ajax/bill.php",
				type : "get",
				data : { term : barcode, page_info:'pu'},
				dataType : 'json',
				success : function(res){
				    if(res != ''){
				        res = res[0];
        				row.find('.serr').val(res.id);
        				row.find('.ser').val(res.value);
        				row.find('.prr').val(res.price);
        				row.find('.volume').val(res.volume);
        				row.find('.stock_id').val(res.stock_id);
        				row.find('.unit').val(res.unit);
        				row.find('.qt').attr("max",check_product_available_stock(res.id,0,0,res.stock_id));
        				row.find('.qt').val('0');
        				findDuplicate($(this)); //call diplicate element function
				    }
				}
            });
        });
	}
	
	
	function autocomplete_serr(){
		$(".ser").autocomplete({
			source: function(request, response) {
				var ser_stime = '';
				if($(this.element).parent().parent().attr('id')=='TextBoxContainer'){
					ser_stime = $('#date').val()+' '+$('#time').val();
					}else{
					ser_stime = $(this.element).parent().parent().prev('tr').find('.ser_etime').val();
				}
				$.getJSON("ajax/bill.php", { term: request.term,ser_cat_id: $(this.element).parent().parent().find('.ser_cat_id').val(),page_info:'pu' }, response);
			},
			minLength: 1,
			select:function (event, ui) {  
				var row = $(this).parent().parent();
				row.find('.serr').val(ui.item.id);
				row.find('.prr').val(ui.item.price);
				row.find('.volume').val(ui.item.volume);
				row.find('.stock_id').val(ui.item.stock_id);
				row.find('.unit').val(ui.item.unit);
				row.find('.qt').attr("max",check_product_available_stock(ui.item.id,0,0,ui.item.stock_id));
				row.find('.qt').val('0');
				findDuplicate($(this)); //call diplicate element function
			}
		});		
	}
	
	<?php if($_GET['sid'] > 0) {  ?>
			var remaining_stock=parseInt(check_product_available_stock($('.serr').val()));
			var select_stock=parseInt($('.qt').val());
			var total_stock=remaining_stock+select_stock;
			$('.qt').attr('max',total_stock);
	<?php } ?>
	
	
	function change_event(){
		
		$(".qt").on("keyup keypress change click", function () {
			
			/******check_Stock******/
				var max_val = parseInt($(this).attr('max'));
				var quant_val =parseInt($(this).val());
				if(quant_val > max_val){
					toastr.warning(max_val +" "+((max_val>1)?' Stocks are left.':'Stock is left '));	
					$(this).val('1');
				} 
				/**********End**********/
				});
	}
	
	
	
	function check_product_available_stock(pid, inv_id, used_stock, stock_id){
			var as= false;
			$.ajax({
			url : "ajax/check_product_available_stock.php",
			type: "POST",
			async: false,
			data: {pid:'pr,'+pid,invoice_id:inv_id,used_stock:used_stock, stock_id:stock_id},
				success:function(data){
					var json = $.parseJSON(data);
					if(json != ''){
					    as = parseInt(json[0]['actual_stock']);
					} else {
					    as = 0;
					}
				}
			});
		 return as;
	}
	
	</script>
<?php include "footer.php"; ?>