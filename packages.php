<?php 
	include "./includes/db_include.php";
	$branch_id = $_SESSION['branch_id'];
	if(isset($_GET['id']) && $_GET['id']>0){
		$id = $_GET['id'];
		$edit = query_by_id("SELECT * from packages where id=:id and branch_id='".$branch_id."'",["id"=>$id],$conn)[0];
	}
	
	if(isset($_POST['submit'])){
		$name       = addslashes(trim(htmlspecialchars($_POST['name'])));
		$name       = addslashes(trim(ucfirst($name)));
		$duration   = addslashes(trim(htmlspecialchars($_POST['duration'])));
		$valid      = addslashes(trim(htmlspecialchars($_POST['valid'])));
		$pprice     = addslashes(trim(htmlspecialchars($_POST['package_price'])));
        $points     = addslashes(trim(htmlspecialchars($_POST['points'])));
		$pid = get_insert_id("INSERT INTO `packages` set `name`=:name,`duration`=:duration,`valid`=:valid,`price`=:price,`points`=:points,`active`=:active, `branch_id`='".$branch_id."'",['name'     =>$name,
								'duration' =>$duration,
								'valid'	   =>$valid,
								'price'    =>$pprice,
								'points'   =>$points,
								'active'   =>0
								],$conn);
		
		for($t=0;$t<count($_POST["services"]);$t++)
		{
			$ser        = addslashes(trim($_POST["service"][$t]));
			$ser_cat_id = addslashes(trim($_POST["ser_cat_id"][$t]));
			$prc        = addslashes(trim($_POST["price"][$t]));
			$qt         = addslashes(trim($_POST["qt"][$t]));
			query("INSERT INTO `packageservice` set `pid`=:pid,`category`=:category,`service`=:service,`quantity`=:quantity,`price`=:price,`active`=:active, `branch_id`='".$branch_id."'",[
							'pid'		=>$pid,
							'category'	=>$ser_cat_id,
							'service'   =>$ser,
							'quantity'  =>$qt,
							'price'		=>$prc,
							'active'    =>0
							],$conn);
		}
		
		$_SESSION['t']  = 1;
		$_SESSION['tmsg']  = "Package Added Successfully";
		header('LOCATION:packages.php');
	}
	if(isset($_POST['edit-submit'])){
        $eid        = addslashes(trim($_POST['eid']));
        $pid        = addslashes(trim($eid));
		$name       = addslashes(trim($_POST['name']));
		$name       = addslashes(trim(ucfirst($name)));
		$duration   = addslashes(trim($_POST['duration']));
		$valid      = addslashes(trim($_POST['valid']));
		$pprice     = addslashes(trim($_POST['package_price']));
        $points     = addslashes(trim($_POST['points']));
		query("UPDATE `packages` set `name`=:name,`duration`=:duration,`valid`=:valid,`price`=:price,`points`=:points,`active`=:active where id=:eid and branch_id='".$branch_id."'",[
										'name'		=>$name,
										'duration'	=>$duration,
										'valid'		=>$valid,
										'price'		=>$pprice,
										'points'	=>$points,
										'active'	=>0,
										'eid'		=>$eid
									  ],$conn);
		
		
		query("UPDATE `packageservice` SET `active`=1 WHERE pid=$eid and branch_id='".$branch_id."'",[],$conn);
		for($t=0;$t<count($_POST["services"]);$t++){
			$ser_cat_id     = addslashes(trim($_POST["ser_cat_id"][$t]));
			$ser            = addslashes(trim($_POST["service"][$t]));
			$prc            = addslashes(trim($_POST["price"][$t]));
			$qt             = addslashes(trim($_POST["qt"][$t]));
			query("INSERT INTO `packageservice` set `pid`=:pid,`category`=:category,`service`=:service,`quantity`=:quantity,`price`=:price,`active`=:active, `branch_id`='".$branch_id."'",[
							'pid'		=>$pid,
							'category'	=>$ser_cat_id,
							'service'   =>$ser,
							'quantity'  =>$qt,
							'price'		=>$prc,
							'active'    =>0
							],$conn);
		}
		
		$_SESSION['t']  = 1;
		$_SESSION['tmsg']  = "Package Updated Successfully";
		header('LOCATION:packages.php?id='.$eid);exit();
	}
	
	if(isset($_GET['del'])){
	    if(DELETE_BUTTON_INACTIVE != 'true'){
    		$id = $_GET['del'];
    		query("UPDATE `packages` SET `active`=1 WHERE id=$id and branch_id='".$branch_id."'",[],$conn);
    		$_SESSION['t']  = 1;
    		$_SESSION['tmsg']  = "Package Removed Successfully";
    		header('LOCATION:packages.php');exit();
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
						<h4>Add new package</h4>
					</div>
					<div class="panel-body">
						
							<form action="" method="post" id="main-form">
								<div class="row">
								<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
									<div class="form-group">
										<label for="userName">Name of Package <span class="text-danger">*</span></label>
										<input type="text" class="form-control" name="name" value="<?= isset($id)?$edit['name']:old('name')?>" 
										<?=(!isset($id))?'onBlur=check();':''?> id="package" placeholder="Package name" required><span id="package-status"></span>
									</div>
								</div>
								
								<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
									<div class="form-group">
										<label for="userName">Duration (in days start from day of purchase) <span class="text-danger">*</span></label>
										<input type="number" class="form-control" name="duration" value="<?= isset($id)?$edit['duration']:old('duration')?>" id="userName" placeholder="60" min="0" required>
									</div>
								</div>
								<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
									<div class="form-group">
										<label for="userName">Package validity till <span class="text-danger">*</span></label>
										<input type="text" class="form-control date" name="valid" value="<?= isset($id)?$edit['valid']:old('valid')?>" id="userName" required readonly>
									</div>
								</div>
								<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
									<div class="form-group">
										<label for="userName">Package price <span class="text-danger">*</span></label>
										<input type="number" class="form-control" name="package_price" value="<?= isset($id)?$edit['price']:old('price')?>" id="pprice" value="0" placeholder="9800.00" min="0" required>
									</div>
								</div>
							</div>
								<div class="clearfix"></div>
								
								<div class="table-responsive">
									<table id="myTable" class="table table-bordered">
										<thead>
											<tr>
												<th style="width:5%" colspan="2">Category</th>
												<th style="width:20%">Service</th>
												<th style="width:10%">Quantity</th>
												<th style="width:10%">Price</th>
											</tr>
										</thead>
										<tbody>
											<?php
												if(isset($id) && $id>0){
													$sql2="SELECT s.price as service_price,CONCAT('sr,',s.id) as service_id,ps.pid,ps.category as cat,c.cat as cat_name,ps.service,ps.quantity,ps.price,s.price as ser_price,s.name as service_name from packageservice ps left join service s on s.id=SUBSTR(ps.service,4) LEFT JOIN `servicecat` c on c.id=ps.category where ps.pid='$id' and ps.active=0 and ps.branch_id='".$branch_id."' order by ps.id asc";
													$sn = 1;
													$result2=query_by_id($sql2,[],$conn);
													foreach ($result2 as $row2_bid) {
													?>
													<tr id="TextBoxContainer" class="TextBoxContainer">
										                <td style="vertical-align: middle" class="text-center">
										                    <span class="sno"><span class="icon-dots-three-vertical"></span></span>
										                </td>
										                <td>
										                    <input type="text" placeholder="category" value="<?=$row2_bid['cat_name']?>" class="ser_cat form-control">
															<input type="hidden" value="<?=$row2_bid['cat']?>" name="ser_cat_id[]" class="ser_cat_id">
														</td>
														<td>
															<input type="text" class="ser form-control slot" name="services[]" value="<?=$row2_bid['service_name']?>" placeholder="Service (Autocomplete)" required>
															<input type="hidden" name="service[]" value="<?=$row2_bid['service_id']?>" class="serr">
															<input type="hidden" name="durr[]" value="<?=$row2_bid['service_durr']?>" class="durr">
														</td>
														<td>
															<input type="number" name="qt[]" class="positivenumber qt form-control sal slot" name="quantity[]" value="<?=($row2_bid['quantity'])?$row2_bid['quantity']:'1'?>">
														</td>
														<td>
															<input type="number" class="pr form-control price positivenumber decimalnumber" step="0.01" name="price[]" id="userName" placeholder="9800.00" value="<?=$row2_bid['price']?>"> 
															<input type="hidden" class="prr" value="<?=$row2_bid['service_price']?>">
														</td>
														
													</tr> 
													<?php 
														$sn++ ;
													} 
													}else{
												?>
												
												<tr id="TextBoxContainer" class="TextBoxContainer">
													<td style="vertical-align: middle" class="text-center">
													    <span class="sno"><span class="icon-dots-three-vertical"></span></span>
													</td>
													<td>
														<input type="text" placeholder="category" value="<?=$row2_bid['cat_name']?>" class="ser_cat form-control">
														<input type="hidden" value="<?=$row2_bid['cat']?>" name="ser_cat_id[]" class="ser_cat_id">
													</td>
													<td>
														<input type="text" class="ser form-control slot" name="services[]" value="<?=$row2_bid['service_name']?>" placeholder="Service (Autocomplete)" required>
														<input type="hidden" name="service[]" value="<?=$row2_bid['service_id']?>" class="serr">
														<input type="hidden" name="durr[]" value="<?=$row2_bid['service_durr']?>" class="durr">
													</td>
													<td>
														<input type="number" name="qt[]" class="positivenumber qt form-control sal slot" name="quantity[]" value="<?=($row2_bid['quantity'])?$row2_bid['quantity']:'1'?>">
													</td>
													
													<td>
														<input type="number" class="pr form-control price positivenumber decimalnumber" step="0.01" name="price[]" id="userName" placeholder="9800.00" value="<?=$row2_bid['price']?>"> 
														<input type="hidden" class="prr" value="<?=$row2_bid['service_price']?>">
													</td>
													
												</tr> 
											<?php } ?>
											
											<tr id="addBefore">
												<td colspan="5"><button type="button" id="btnAdd" class="btn btn-success pull-right"><i class="fa fa-plus" aria-hidden="true"></i>Add more service</button></td>
											</tr>
											<tr>
												<td class="total" colspan="4">Package worth</td>
												<td id="sum">KWD. 00</td>
											</tr>
											<tr>
												<td class="total" colspan="4">Total Savings in <?=CURRENCY?></td>
												<td id="dis">KWD. 00</td>
											</tr>
											<tr>
												<td class="total" colspan="4">Total Savings in %</td>
												<td id="per"> 0%</td>
											</tr>
										</tbody>
									</table>
								</div>
								<div class="row">
								    <div class="col-md-12">
							        	<?php if(isset($id)){ ?>
											<input type="hidden" name="eid" value="<?=$id?>">
											<button type="submit" name="edit-submit" class="btn btn-info pull-right"><i class="fa fa-pencil-square-o" aria-hidden="true"></i>Update package</button>
											<?php }else{ ?>
											<button type="submit" name="submit" class="btn btn-success pull-right"><i class="fa fa-check" aria-hidden="true"></i>Create package</button>
										<?php } ?>
								    </div>
								</div>
							</form>	
					</div>
				</div>
			</div>
		</div>
		<!-- Row ends -->
		
		<div class="row">
			<div class="col-lg-12">
				<div class="panel">
					<div class="panel-heading">
						<h4>Edit / manage packages</h4>
					</div>
					<div class="panel-body">
						
						<div class="row">
						    <div class="col-lg-12">
							<div class="table-responsive">
							<table class="table grid table-bordered no-margin table_datatable">
									<thead>
										<tr>
											<th>Package</th>
											<th>Duration(In Days)</th>
											<th>Valid upto</th>
											<th>Price</th>
											<th width="150">Manage</th>
										</tr>
									</thead>
									<tbody>
										<?php
											$sql1="SELECT * from packages where active=0 and branch_id='".$branch_id."' order by id desc";
											$result1= query_by_id($sql1,[],$conn);
											foreach ($result1 as $row1) {
											?>
											<tr>
												<td><?php echo $row1['name']; ?></td>
												<th><?php echo $row1['duration']; ?></th>
												<td><?php echo my_date_format($row1['valid']); ?></td>
												<th><?php echo number_format($row1['price'],2); ?></th>
												<td><a href="packages.php?id=<?php echo $row1['id']; ?>"><button class="btn btn-warning btn-xs" type="button"><i class="fa fa-pencil-square-o" aria-hidden="true"></i>Edit</button></a>
												<?php if(DELETE_BUTTON_INACTIVE == 'true'){ ?>
												    <a href="#" onclick="return deleteDisabled();"><button class="btn btn-danger btn-xs" type="button"><i class="icon-delete"></i>Delete</button></a></td>
												<?php } else { ?>
												    <a href="packages.php?del=<?php echo $row1['id']; ?>" onclick="return confirm('Are you sure?');"><button class="btn btn-danger btn-xs" type="button"><i class="icon-delete"></i>Delete</button></a></td>
											    <?php } ?>
											</tr>
										<?php   } ?>
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
	<!-- Main container ends -->
	
</div>
<!-- Dashboard Wrapper End -->

</div>
<!-- Container fluid ends -->

<div class="copy hide">
	<div class="control-group input-group" style="margin-top:10px">
		<input type="text" name="addmore[]" class="form-control" placeholder="Enter Name Here">
		<div class="input-group-btn"> 
			<button class="btn btn-danger remove" type="button"><i class="glyphicon glyphicon-remove"></i> Remove</button>
		</div>
	</div>
</div>
<?php include "footer.php"; ?>	
<script>
	
	$(document).on("click", '#date', function() {
		$('.staff option[value=""]').prop('selected',true);	
		$('.ser').val("");
		$('.start_time').val("");
		$('.end_time').val("");
		$('.ser_stime').val("");
		$('.ser_etime').val("");
		$('.prr').val("");
		$('.pr ').val("");
		$('.serr').val("");
		$('.disc_row').val("");
	});
	
	
	$(document).on("change", '.staff', function() {
		staff = $(this).val();
		findDuplicate($(this)); //call diplicate element function
		var durr  		= $(this).parent().parent().find('.durr').val();
		var starttime   = $(this).parents('tr').find('.ser_stime').val();
		var endtime     = $(this).parents('tr').find('.ser_etime').val();
		var select_staff= $(this).parents('tr').find('.staff option[value=""]');	
		
		date = $('#date').val();
		time = $('#time').val();
		var durr_plus = 0;
		var prev_rows = $(this).parent().parent().prevAll('tr');
        $(this).parent().parent().prevAll('tr').each(function(){
            durr_plus += parseInt($(this).find('.durr').val());
		});
		if(starttime !=''){
			$.ajax({
				url: "ajax/appointment_stafftime.php?id="+staff+"&date="+date+"&time="+time+"&starttime="+starttime+"&endtime="+endtime,
				type: "POST",
				success:function(data){
					var durr_count = 0;
					var ds = JSON.parse(data.trim());
					starttime = ds['start'];
					endtime = ds['end'];
					var ds = JSON.parse(data.trim());
					
				},
				error:function (){}
			});
			}else{
			select_staff.prop('selected',true);
		}
	});
	
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
	
	<!--##### function to find duplicate service_provider ###-->
	function findDuplicate(e){
		duplicate_arr=[];
		var row=e.parents(".TextBoxContainer").find('.spr_row').children('table');
		var row1=$(".TextBoxContainer").parents('table.add_row').find('.spr_row').children('table.add_row');
		var val=row.find('.staff');
		val.each(function(){
			duplicate_arr.push($(this).val());
		});
		
		for(var i=0;i<duplicate_arr.length;i++){
			for(var j=i+1;j<duplicate_arr.length;j++){
				if(duplicate_arr[i]==duplicate_arr[j]){
					e.parents('table.add_row').find('td#minus_button').remove()
					e.parents('table.add_row').find('td#select_row').remove();
				}  
			}
		}
	}
	
	$('.ser').on('keyup',function(){
	   var parent = $(this).parent().parent().find('.ser_cat_id').val(''); 
	});
	
	$(window).on('load', function(){
		var e=$(this);
		var row=$(".TextBoxContainer");
		var row_len=row.length;
		var l=1;
		row.each(function(){
			$(".TextBoxContainer").eq(l).find('.sno').html('<span class="remm icon-trash2" style="color:red;cursor:pointer;" onclick="sumup();$(this).parent().parent().parent().remove();"></span>');
			l++;
		});
		sumup();
		
	});
	
	$(document).on("click",".add_spr_row", function(){
		var td_clone=$("#TextBoxContainer").find('.spr_row').children('table#add_row').clone().addClass('add_row');
		td_clone.removeAttr('id');
		td_clone.find('td#plus_button').remove();
		td_clone.find('tr').append('<td id="minus_button"><span class="input-group-btn"><button onclick="$(this).parent().parent().parent().remove();" class="btn btn-danger btn-remove btn_remove" type="button"><span class="glyphicon-minus" "></span></button></span></td>');
		var l_row=$(this).parents(".TextBoxContainer").find('.spr_row');
		l_row.children('table#add_row').after(td_clone);
		
		l_row.next().children('table.add_row').find('.staff option[value=""]').prop('selected',true); 	
		l_row.children('table.add_row').find('.staff option[value=""]').attr('selected','selected'); 
		
		var staff_name=$(this).parents(".TextBoxContainer").find('.staff').attr('name');
		$(this).parents(".TextBoxContainer").find('.staff').attr('name',staff_name); 
	});
	
    $(function(){
        autocomplete_serr();
        autocomplete_serr_cat();
        formValidaiorns();
        price_change();
        change_event();
        $(".client").autocomplete({
			source: "client.php",
			minLength: 1,
            select: function(event, ui) {
                event.preventDefault();
                $('#client').val(ui.item.name);
                $('#clientid').val(ui.item.id); 
                $('#cont').val(ui.item.cont); 
                $('#gender').val(ui.item.gender);
				$('#cc').val('');
			}				
		});	
        
        $("#btnAdd").bind("click", function() {
            var clonetr = $("#TextBoxContainer").clone().addClass('TextBoxContainer');
            clonetr.removeAttr('id');
			clonetr.find("table.add_row").remove();
            clonetr.find('.sno').html('<span class="remm icon-trash2 " style="color:red; cursor:pointer;" onclick="$(this).parent().parent().parent().remove();sumup();"></span>');
            clonetr.find('input').val('');
			clonetr.find('.staff option[value=""]').prop('selected',true);
            $("#addBefore").before(clonetr);
            autocomplete_serr_cat();
            autocomplete_serr();
            formValidaiorns();
            price_change();
            //funcClass();
            change_event();
			
			
		});
		
        $(".ser_cat").on('keyup keydown keypress change',function(){
            if($(this).val()==''){
                $(this).parent().find('.ser_cat_id').val('');
			}
		});
	});
	
	
	
	function autocomplete_serr_cat(){
		$(".ser_cat").autocomplete({
			source: "ajax/bill_cat.php",
			minLength: 1,
			select:function (event, ui) {  
				var row = $(this).parent().parent();
				var row1= $(this).parents('tr');
				$(this).val(ui.item.value);
				row.find('.ser_cat_id').val(ui.item.id);
				
				row1.find('.ser').val("");
				row1.find('.start_time').val("");
				row1.find('.end_time').val("");
				row1.find('.ser_stime').val("");
				row1.find('.ser_etime').val("");
				row1.find('.prr').val("");
				row1.find('.pr ').val("");
				row1.find('.serr').val("");
				row1.find('.disc_row').val("");
				row1.find('.staff option[value=""]').prop('selected',true);
				
			}
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
				$.getJSON("ajax/bill.php", { term: request.term,ser_cat_id: $(this.element).parent().parent().find('.ser_cat_id').val(),ser_stime:ser_stime, type : 'package_services' }, response);
			},
			minLength: 1,
			select:function (event, ui) {  
				var row = $(this).parent().parent();
				row.find('.serr').val(ui.item.id);
				row.find('.prr').val(ui.item.price);
				row.find('.qt').val('1');
				row.find('.ser_cat').val(ui.item.cat_name);
				row.find('.ser_cat_id').val(ui.item.cat);
				row.find('.disc_row ').val('0');
				row.find('.duration').val(ui.item.duration);
				row.find('.ser_stime').val(ui.item.ser_stime);
				row.find('.ser_etime').val(ui.item.ser_etime);
				row.find('.start_time').val((ui.item.ser_stime).substring(11));
				row.find('.end_time').val((ui.item.ser_etime).substring(11));
				price_calculate(row);
				sumup();
			}
		});	
	}
	
	
	function price_calculate(row){
		var pr = row.find('.prr').val();
		var qt = row.find('.qt').val();
		var sum = pr * qt;
		var disc_row_val = row.find('.disc_row').val();
		disc_row_val = disc_row_val>0?disc_row_val:0;
		var disc_row_type = row.find('.disc_row_type').val();
		if(disc_row_type=='0'){
			var disc_row = parseFloat((sum * disc_row_val)/100);
			}else{
			var disc_row = parseFloat(disc_row_val);
		}
		sum = sum - disc_row;
		row.find('.price').val(sum);
		var pric = 0;
		var  sums = 0;
		var  sump = 0;
		var sumt = 0;
		var sum = 0;
		var ids = $(".serr");
		var inputs = $(".price");
		for(var i = 0; i < inputs.length; i++){
            var service = $(ids[i]).val().split(',');
            if(service[0]=="sr"){
				sums = sums + parseInt($(inputs[i]).val());
			}
            else if(service[0]=="pr"){
				sump = sump + parseInt($(inputs[i]).val());
			}
            sum = parseInt(sum) + parseInt($(inputs[i]).val());
            $("#sum").html("KWD. "+sum);
		}
	}
	
	
	function sumup(){
		
		var pric = $('#pprice').val();
		pric = pric || 0;
		var  sum = 0;
		var inputs = $(".price");
		
		for(var i = 0; i < inputs.length; i++){
			sum = parseInt(sum) + parseInt($(inputs[i]).val()||0);
			
			$("#sum").html("KWD. "+sum);
		}
		sum = sum || 0;
		var tot = parseInt(sum) - parseInt(pric);
		if (tot < 0) {
			tot = 0;
		}
		tot = tot || 0;
		$("#dis").html("KWD. "+tot);
		
		var per = parseInt(tot)/parseInt(sum) * 100;
		if (per < 0) {
			per = 0;
		}
		per = per || 0;
		$("#per").html(per.toFixed(2)+"%");
        $("#dis_per").val(per.toFixed(2));
	}
	function price_change(){
		$(".pr,#tax").on("keyup change", function (e) {
			sumup();
		});
	}
	function change_event(){
		
		$(".qt").on("keyup keypress change click", function () {
			var row = $(this).parent().parent(); 
			price_calculate(row);
			sumup();
			
		});
		$("#pprice").on("keyup keypress change click", function () {
			var row = $(this).parent().parent(); 
			price_calculate(row);
			sumup();
			
		});
		$(".disc_row,.disc_row_type").on("keyup keypress change keydown", function () {
			var row = $(this).parent().parent().parent().parent().parent().parent(); 
			price_calculate(row);
			sumup();
			
		});
		$("#total_disc,.total_disc_row_type").on("keyup keypress change keydown", function () {
			sumup(); 
		});
	}
	
	function check() {
		var cat = $('#package').val();
		jQuery.ajax({
			url: "checkpackage.php?pack="+cat,
			type: "POST",
			success:function(data){
				$("#package-status").html(data).css('color','red');
				if ( data.indexOf("Already Exist") > -1 ){
					$('#package').val("");
				}
			},
			error:function (){}
		});
	}
</script>
