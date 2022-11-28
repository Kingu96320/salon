<?php
include "./includes/db_include.php";
$branch_id = $_SESSION['branch_id'];
$uid = $_SESSION['uid'];
if(isset($_GET['pid']) && $_GET['pid']>0)
{
    $pid = $_GET['pid'];
    $edit = query_by_id("SELECT p.*,v.* from purchase p LEFT JOIN vendor v on v.id=p.vendor  where p.id=$pid and p.branch_id='".$branch_id."'",["id"=>$pid],$conn)[0];
}
if(isset($_POST['submit']))
	{
	$vendor = $_POST['vendor_id'];
	if($vendor==0)
	{
		$name =$_POST['vendor'];
		$vendor = setvendor($name);
	}
	
	$dop 	= $_POST['dop'];
	$inv 	= $_POST['inv'];
	
	//$prod=count($_POST['product']);
	//echo "<script type='text/javascript'>alert('$prod');</script>";
	$volume	=$_POST['volume'];
	$unit	=$_POST['unit'];
	$price	=$_POST['purchase_price'];
	//$barcode=$_POST['bar'];
	for($t=0;$t<count($_POST["product"]);$t++)
	{
	$prod	= ucfirst($_POST['product'][$t]) ;
	$volume	=$_POST['volume'][$t];
	$unit	=$_POST['volume_unit'][$t];
	//$price	=$_POST['purchase_price'][$t];
	//$prod=explode(",",$pr);
	$product_name = query_by_id("SELECT id from products where name=:product and active='0' and branch_id='".$branch_id."'",["product"=>$prod],$conn);
        if($product_name)
		{
            $product = $product_name[0]['id'];
        }else
		{
           $product = get_insert_id("INSERT INTO products  set name=:product,volume='$volume',unit='$unit', active='0', branch_id='".$branch_id."'",["product"=>$prod],$conn); 
        }
	}
	$tax 	= $_POST['tax'];
	$dis 	= $_POST['dis'];
	$misc   = $_POST['misc'];
	$ship	= $_POST['ship'];
	$tot 	= $_POST['tot'];
	$pay 	= $_POST['paymode'];
	$payment= $_POST['paid'];
	$credit = $_POST['credit'];
	if($credit != 0)
	{
	$credit = $_POST['credit'];
	$debit 	=$payment -$tot;
	}
	else
	{
		$credit = 0;
		$debit 	=$_POST['paid'];
	}
	
	//$notes= mysqli_real_escape_string($con, $_POST['notes']);
	
	$details =  $_POST['detail'];
	if($payment == 0)
	{
	$aid	=get_insert_id("INSERT INTO `purchase`(`inv`,`vendor`,`dop`,`tax`,`dis`,`misc`,`ship`,`tot`,`paymode`,`details`,`payment`, `credit`,`active`,`branch_id`) VALUES ('$inv','$vendor','$dop','$tax','$dis','$misc','$ship','$tot','$pay','$details','$payment','$credit',0,'$branch_id')",[],$conn);
	}
	if($payment !=0)
	{
	query("INSERT INTO `payments` SET `bill_id`='$inv',`purchase_id`='$aid',`vendor`='$vendor',`paid`='$payment',`payment`='$tot',`pend`='',`date`='$dop',`mode`='$pay',`notes`='$details',`active`='0',`debit`='$debit',credit='$credit', branch_id='".$branch_id."'",[],$conn);
	//$aid = mysqli_insert_id($con);
	}
	for($t=0;$t<count($_POST["product"]);$t++)
	{
		$prod	    = $_POST["product"][$t];
		$prc	    = $_POST["total_price"][$t];
		$qt 	    = $_POST["quantity"][$t];
		$product_id	= $_POST['product_id'][$t];
		query("INSERT INTO `purchase_items`(`iid`,`vendor`,`product`,`product_id`,`quantity`,`price`,`active`,`branch_id`) VALUES ('$aid','$vendor','$prod','$product_id','$qt','$prc',0,'$branch_id')",[],$conn);
		
		query("INSERT INTO `transactions`(`iid`,`inv`,`client`,`service`,`quantity`,`price`,`debit`,`date`,`type`,`notes`,`active`,`uid`,`branch_id`) VALUES ('$aid','$inv','$vendor','$prod','$qt','$prc','$prc','$dop','Product','$details',0,'$uid','$branch_id')",[],$conn);
		
		 
	}

			$_SESSION['t']  = 1;
			$_SESSION['tmsg']  = "Purchased Saved Successfully";
			echo '<meta http-equiv="refresh" content="0; url=addpurchase.php" />';die();
	}
	if(isset($_POST['edit-submit']))
	{
	$pid	= $_GET['pid'];
	$vendor = $_POST['vendor_id'];
	//echo '<script type="text/javascript">alert($vendor)</script>';
	 
	$dop 	= $_POST['dop'];
	$inv 	= $_POST['inv'];
	
 
	$volume	=$_POST['volume'];
	//$unit	=$_POST['unit'];
	$price	=$_POST['purchase_price'];
	for($t=0;$t<count($_POST["product"]);$t++)
	{
	$prod	= ucfirst($_POST['product'][$t]) ;
	$volume	=$_POST['volume'][$t];
	$unit	=$_POST['volume_unit'][$t];
	 
	$product_name = query_by_id("SELECT id from products where name=:product and active='0' and branch_id='".$branch_id."'",["product"=>$prod],$conn);
        if($product_name)
		{
            $product = $product_name[0]['id'];
        }
		else
		{
           $product = get_insert_id("INSERT INTO products  set name=:product,volume='$volume',unit='$unit', active='0', branch_id='".$branch_id."'",["product"=>$prod],$conn); 
        }
	}
	$tax 	= $_POST['tax'];
	$dis 	= $_POST['dis'];
	$misc   = $_POST['misc'];
	$ship	= $_POST['ship'];
	$tot 	= $_POST['tot'];
	$pay 	= $_POST['paymode'];
	$payment= $_POST['paid'];
	$credit = $_POST['credit'];
 
	
	query("DELETE from `purchase_items` where iid='$pid' and branch_id='".$branch_id."'",[],$conn);
	
	 
	query("DELETE from 	`transactions` where iid='$pid' and branch_id='".$branch_id."'",[],$conn);
	 
	query("UPDATE `purchase` set `inv`='$inv',`vendor`='$vendor',`dop`='$dop',`tax`='$tax',`dis`='$dis',`misc`='$misc',`ship`='$ship',`tot`='$tot',`paymode`='$pay',`details`='$details',`payment`='$payment',`credit`='$credit',`active`='0' where id='$pid' and branch_id='".$branch_id."'",[],$conn);
	
	
	for($k=0;$k<count($_POST['product']);$k++)
	{
		
		$prod		= $_POST["product"][$k];
		$prc	    = $_POST["purchase_price"][$k];
		$qt 		= $_POST["quantity"][$k];
		$product_id	= $_POST['product_id'][$k];
		 
		
		
		
		query("INSERT INTO `purchase_items`(`iid`,`vendor`,`product`,`product_id`,`quantity`,`price`,`active`,`branch_id`) VALUES ('$pid','$vendor','$prod','$product_id','$qt','$prc',0,'$branch_id')",[],$conn);
		
		query("INSERT INTO `transactions`(`iid`,`inv`,`client`,`service`,`quantity`,`price`,`debit`,`date`,`type`,`notes`,`active`,`uid`,`branch_id`) VALUES ('$pid','$inv','$vendor','$prod','$qt','$prc','$prc','$dop','Product','$details',0,'$uid','$branch_id')",[],$conn);
		
		 
	}
			$_SESSION['t']  = 1;
			$_SESSION['tmsg']  = "Purchased Updated Successfully";
			echo '<meta http-equiv="refresh" content="0; url=addpurchase.php?pid='.$_GET['pid'].'" />';die();
}
include "topbar.php";
include "header.php";
include "menu.php";
?>

 
<script type="text/javascript">
<?php if(isset($edit)) { ?> 		
        
	var sno=0;
	$(window).on('load', function()
	{
	var inputs=$('.pro');
	for(var i = 0; i < inputs.length; i++)
	{
	   sno++;
		 
	}
		
	});	  
<?php  } else { ?>	
var sno = 1;
<?php } ?>

   $(function() 
   {
  $("#btnAdd").bind("click", function() {
    var tr = $("<tr />");
    tr.html(GetDynamicTextBox(""));
	var clonetr = $("#TextBoxContainer").clone();
	clonetr.removeAttr('id');
	clonetr.find('.sno').html((++sno)+'<span class="remm icon-trash2" style="color:red;" onclick="$(this).parent().parent().remove();"></span>');
	clonetr.find('input').val('');
	
    $("#addBefore").before(clonetr);
	autocomplete_serr();
	autocomplete_ser();
	cal();
	check();
	upcase();
 

  });
  $("#btnGet").bind("click", function() {
    var values = 
      $.map($("input[name=DynamicTextBox]"), function(el) {
        return el.value
      }).join(",\n");
    $("#anotherTextbox").val(values);
  });
  $("body").on("click", ".remove", function() 
  {
    $(this).closest("tr").remove();
  });
});

function GetDynamicTextBox(value) {
  return '<tr><td>001 <span class="icon-trash2" style="color:red;"></span></td>'+
		'<td><input type="text" class="form-control" id="userName" placeholder="Service (Autocomplete)"></td>'+
		'<td><input type="text" class="form-control" id="userName" value="1"></td>'+
		'<td><input type="text" class="form-control" id="userName" placeholder="9800.00" readonly></td>'+
		'<td><input type="button" value="X" class="remove btn btn-warning col-lg-2" style="width: 40px;" /><td>'
}
</script>

<script type='text/javascript'>//<![CDATA[
$(window).on('load', function(){

$(".table").on("keyup", ".ser", function () {
	 var $row = $(this).closest(".ser");
	 var row = $(this).parent().parent();
	
$.ajax({
url: "autocomplete/getproduct.php",//+$(this).val(),
type: "POST",
data: {'ser' : $(this).val()},
success:function(data){
	var ds = JSON.parse(data.trim());
	row.find('.serr').val(ds[0]['id']);

},
error:function (){}
});
	 
});
});
</script>

<script type='text/javascript'>//<![CDATA[
$(window).on('load', function(){

$(".table").on("blur", ".ser", function () {
    var $row = $(this).closest(".ser");
	 var row = $(this).parent().parent();
	
$.ajax({
url: "autocomplete/getproduct.php",//+$(this).val(),
type: "POST",
data: {'ser' : $(this).val()},
success:function(data){
	var ds = JSON.parse(data.trim());
	row.find('.serr').val(ds[0]['id']);

},
error:function (){}
});
	 
});
});
// $(window).on('load', function() {
    page is fully loaded, including all frames, objects and images
    // alert("window is loaded");
// });
</script>

		<div class="dashboard-wrapper">

				<!-- Main container starts -->
				<div class="main-container">

					<!-- Row starts -->
					<div class="row gutter">
						
						<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
						<form action="" method="post">
							<div class="panel">
								<div class="panel-heading">
									<h4>Purchase from vendor / Add stock</h4>
								</div>
								<div class="panel-body">
									<div class="col-lg-3 col-md-2 col-sm-3 col-xs-12">
											<div class="form-group">
												<label for="userName">Product vendor name</label>
												<input type="text" class="vendor form-control" id="vendor" name="vendor" value="<?=isset($edit)?$edit['name'].'('.$edit[cont].')':''?>" placeholder="Autocomplete (Phone)" required>
												<input type="hidden" name="vendor_id" id="vendorid" value="<?=isset($edit)?$edit['id']:''?>" class="clt">
											</div>
									</div>
									
									<div class="col-lg-3 col-md-2 col-sm-2 col-xs-12">
											<div class="form-group">
												<label for="userName">Invoice give by vendor</label>
												<input type="text" class="form-control" name="inv" placeholder="Invoice No." value="<?=isset($edit)?$edit['inv']:''?>" required>
											</div>
									</div>
									
									<div class="col-lg-3 col-md-2 col-sm-2 col-xs-12">
									<div class="form-group">
												<?php $date = date('Y-m-d'); ?>
												<label for="userName">Date of purchase</label>
												<?php $date = date('Y-m-d'); ?>
												<input type="text" class="form-control date" value="<?=isset($edit)?$edit['dop']:$date?>" name="dop" placeholder="Date of Purchase" value="" readonly>
											 
						     		</div>
									</div>
										
										<script type="text/javascript">
												$(function() {
													autocomplete_vendor();										
												});
												function autocomplete_vendor(){
													$(".vendor").autocomplete({
														source: "autocomplete/vendor.php",
														minLength: 1,	
														//autoFocus: true,
													select: function(event, ui) {
														//event.preventDefault();
														//$('#client').val(ui.item.label);
														//alert(ui.item.name);
														$('#vendorid').val(ui.item.id);
													 }				
												});	
												}
												
												$(document).ready(function()
												{
												$("#vendor").blur(function(){
												var dat = $('#vendor').val();

												$.ajax({
												url: "autocomplete/vendor.php?term="+dat,
												type: "POST",
												success:function(data)
												{
												var d = JSON.parse(data.trim());
												$('#vendorid').val(d[0]['id']); 
												$('#vendor').val(d[0]['value']); 
												},
												error:function (){}
												});
		
												});
												});
												
										</script>
										
										<script type="text/javascript">
											$(function() 
											{
												autocomplete_serr();										
											});
											function autocomplete_serr(){
												$(".ser").autocomplete({
													source: "autocomplete/product.php",
													minLength: 1
											});	
											}
										</script>
										
									<div class="clearfix"></div>
									
									<div class="col-lg-12">
										<div class="table-responsive">
													<table id="myTable" class="table table-bordered">
														<thead>
															<tr>
																<th style="width:5%">Sl.No.</th>
																<th style="width:70%">Product</th>
																<th style="width:10%">Purchase price</th>
																<th style="width:10%">Unit</th>
																<th style="width:5%">Total</th>
															</tr>
														</thead>
														<tbody>
				<?php 
				if(isset($_GET['pid']))
				{
				$sr=1;
				$sql1	="SELECT pi.*,p.volume,p.unit from purchase_items pi LEFT JOIN  products p on p.id = pi.product_id where pi.iid=$pid and pi.active=0 and pi.branch_id='".$branch_id."'";
				$result =query_by_id($sql1,[],$conn);
				foreach($result as $row)
				{
				?>
														
												<tr id="TextBoxContainer">
													<td class="sno"><?=$sr++;?></td>
															<td>
																
				<table>
				
				<tr>
				<td width="60%">
				
				<input type="text" name="product[]" class="pro form-control" value="<?=isset($_GET['pid'])?$row['product']:''?>" placeholder="Product (Autocomplete)" required>
				<input type="hidden" name="product_id[]" class="product_id" value="<?=isset($_GET['pid'])?$row['product_id']:''?>">
			
				</td>
				 
				<td width="20%">
				
											<script>
												$(function() 
												{
												
												autocomplete_serr();	
													 
												});
												function autocomplete_serr()
												{
													$('.pro').autocomplete({
													source: "autocomplete/addproduct.php",
													minLength: 1,
													select: function(event,ui) 
													{
													//event.preventDefault();
													// var pr=$('#price').val(ui.item.price);
													// var quantity=$('#quantity').val();
													// var result =pr*quantity;
													  
													 
                                                     	 
													}	
												});	
												
												 												
												$(document).ready(function()
												{
												$(".pro,.price").on("blur",function()
												{
												var row	=$(this).parent().parent();
												var dat = row.find('.pro').val();
												row.find('.qty').val('1'); 
												$.ajax
												({
												url: "autocomplete/addproduct.php?term="+dat,
												type: "POST",
												success:function(data)
												{
												var d = JSON.parse(data.trim());
												row.find('.product_id').val(d[0]['id']); 
												row.find('.pro').val(d[0]['value']); 
												row.find('.qt').val(d[0]['volume']);
												row.find('.vu').val(d[0]['unit']);
											 
												},
												error:function (){}
												});
		
												});
												});
											 }
										</script>		
	<input type="number" name="volume[]" class="qt form-control" placeholder="Volume"  id="vol" value="<?=isset($_GET['pid'])?$row['volume']:''?>"  required></td>
		<td width="10%">	
												
	<!--<input class="auto form-control" type="text" name="unit" id="u_unit" placeholder="Unit" value="<?=isset($edit)?$edit['unit']:''?>" required>
    <input type="hidden" name="unit_id" id="unit_id">	-->
	
	<select class="form-control v_unit vu"  name="volume_unit[]">
	
									
									<?php 		
									$sql	="select * from units where active=0 order by id asc";
									$result	=query_by_id($sql,[],$conn);
									foreach($result as $row1)
									{
									?>
									<option  value="<?=$row1['id']?>" <?php if($edit['unit']==$row1['name'] || ($row1['id']==$row['unit'])) { echo "selected" ;} ?>><?=$row1['name']?></option>
									<?php 
									}  
									?>
 
									</select>
													</td>
													
	<td width="10%"> <i class="icon-straighten" style="font-size:32px;"></i></td>
    </tr>
																		
																	</table></td>
																			
				
				<td><input type="text" name="purchase_price[]" value="<?=isset($_GET['pid'])?$row['price']:''?>" class="form-control price key" placeholder="0.00" required></td>
				<td><input type="number" class="form-control qty key" name="quantity[]" placeholder="0.00" value="<?=isset($_GET['pid'])?$row['quantity']:'1'?>"></td>
				<td><input type="text" class="form-control subtotal key" name="total_price[]" placeholder="0.00" readonly></td>
					
				</tr>
				<script>
				$(window).on('load', function()
				{
				$('.price').each(function()
				{
				var row 	=$(this).parent().parent();
				var price	=parseFloat(row.find('.price').val());	
					price	=price || 0;
				var quantity= parseInt(row.find('.qty').val());	 
				var mul		=price * quantity;
					mul		=mul || 0;
				 
					
					row.find('.subtotal').val(mul);
				});
				 var sum=0;
					var inputs	= $(".subtotal");
					for(var i = 0; i < inputs.length; i++)
					{
			 
					sum = parseInt(sum) + parseInt($(inputs[i]).val());
					sum = sum || 0;
		 
					} 
					$(".sub_total").val(sum);		 
				});
				 
				 
				</script>
					<?php } } else	{ ?>
					
														<tr id="TextBoxContainer">
																<td class="sno">1</td>
																<td>
																
				<table>
				
				<tr>
				<td width="60%">
				
				<input type="text" name="product[]" class="pro form-control" placeholder="Product (Autocomplete)" required>
				<input type="hidden" name="product_id[]" class="product_id" >
				
				</td>
				<td width="20%">
				
											<script>
												 
												$(function() 
												{
												autocomplete_serr();	
													 
												});
												function autocomplete_serr()
												{
													$('.pro').autocomplete({
													source: "autocomplete/addproduct.php",
													minLength: 1,
													select: function(event,ui) 
													{
													//event.preventDefault();
													// var pr=$('#price').val(ui.item.price);
													// var quantity=$('#quantity').val();
													// var result =pr*quantity;
													// $('#subtotal').val('result');
                                                     	 
													}	
												});	
												
												 												
												$(document).ready(function()
												{
													 
												$(".pro,.price").on("blur focus",function()
												{
												var row	=$(this).parent().parent();
												var dat = row.find('.pro').val();
												row.find('.qty').val('1');
												$.ajax
												({
												url: "autocomplete/addproduct.php?term="+dat,
												type: "POST",
												success:function(data)
												{
												var d = JSON.parse(data.trim());
												row.find('.product_id').val(d[0]['id']); 
												row.find('.pro').val(d[0]['value']); 
												row.find('.vol').val(d[0]['volume']);
												row.find('.vu').val(d[0]['unit']);
												 
												},
												error:function (){}
												});
		
												});
												});
											 }
										</script>		
							<input type="number" name="volume[]" class="qt form-control vol" placeholder="Volume" required></td>
							<td width="10%">	
												
							<!--<input class="auto form-control" type="text" name="unit" id="u_unit" placeholder="Unit" value="<?=isset($edit)?$edit['unit']:''?>" required>
							<input type="hidden" name="unit_id" id="unit_id">	-->
						<select class="form-control v_unit vu " name="volume_unit[]">
	
									<?php 		
									$sql	="select * from units where active=0 order by id asc";
									$result	=query_by_id($sql,[],$conn);
									foreach($result as $row)
									{
									?>
									<option  value="<?=$row['id']?>" <?php if($edit['unit']==$row['name']) { echo "selected";}  ?>><?=$row['name']?></option>
									<?php 
									}  
									?>
 
									</select>
						</td>
													
							<td width="10%"> <i class="icon-straighten" style="font-size:32px;"></i></td>
						</tr>
																		
																	</table>
																			
																
																
																
				</td>
				<td><input type="text" name="purchase_price[]" class="form-control price key"  placeholder="0.00" required></td>
				<td><input type="number" class="form-control qty  key" name="quantity[]" placeholder="0.00" required></td>
				<td><input type="text" class="form-control subtotal key" name="total_price[]" placeholder="0.00" readonly></td>
				
				
					
				</tr>
				<?php } ?>
				<tr id="addBefore">
				<td colspan="5"><button type="button" id="btnAdd" class="btn btn-info pull-right">Add product</button></td>
				</tr>
				
				<tr>
				<td colspan="4">Subtotal</td>
				<td colspan="2"><input type="number" step="0.01"   name="subtotal" value="" class="sub_total form-control" placeholder="0.00" readonly></td>
				</tr>
				
				<script type='text/javascript'> 
					$(window).on('load', function()
					{
					$(".table").on("blur", ".pro,.price,.qty,.qt,.v_unit", function () 
					{	
					
					var row = $(this).parent().parent();
					var pr = row.find('.product_id').val();
					
					if(pr !='')
					{
					var bol = checkstock(pr);
					}
					 
					if(bol>1)
					{
					toastr.warning("Duplicate Entry");
					row.find(".product_id").val("");
					row.find(".pro").val("");
					row.find(".qt").val("0.00");
					row.find('.price').val("0.00");
					}
					});
					});
					function checkstock(sid) 
					{
					var so  = 0;
					var ids = $(".product_id");
					var inputs = $(".pro");
					for(var i = 0; i < inputs.length; i++)
					{ 
					if($(ids[i]).val()==sid)
					{
					so++; 
					}
					}
					return so;	
					}
				</script>
				<script>
				function check()
				{
					$('.key1,.key,.price,.k').on("input change keyup keydown keypress",function()
					{
					
					var amount=0;
					var subtotal=0;
					var dis=0;
					var shipping=0;
					subtotal=parseFloat($('.sub_total').val());
					dis=parseFloat($('#dis').val());
					dis=dis||0;
					amount +=subtotal-dis;
					shipping=$('#ship').val();
					shipping=shipping||0;
					var tax = $('#tax').val();
					var taxx = tax.split(',');
					
					if(taxx[2]!=0)
					{
					var tsum = amount * parseInt(taxx[1]) / 100;
					tsum = tsum || 0;
					amount += tsum;
					//console.log(total);
					}
					else
					{
						tsum = 0;
					}
					
					amount +=parseFloat(shipping);
					
					amount +=parseFloat($('#misc').val());
					var c=parseFloat($('#c').val()||0);  
					 
					$('.tot').val(amount.toFixed(2));
					var amount_id=parseFloat($('.tot').val()||0);  
					var ap=parseFloat($('#amount_paid').val()||0);
					
					var credit= amount_id - ap;
					 
					credit =credit - c;
					//credit =credit ;
					
					$('#credit').val(credit.toFixed(2));
					 
					});	
					// $('.k').on("input change keyup keydown keypress",function()
					// {
					// var ap=$('#amount_paid').val();
					// var amount=$('.tot').val();
					// var credit=amount-ap;
					// $('#credit').val(credit.toFixed(2));
					// });
					
				}
				</script>
				<script>
				$(function()
				{
					cal();
					check();
					upcase();
				});
				
				function cal()
				{
					 
				$('.table').on("input change keyup keydown keypress",".qty,.price",function()
				{
					var row 	= $(this).parent().parent();
					var price	=parseFloat(row.find('.price').val()||0);
					 
					var quantity= parseInt(row.find('.qty').val());
					var mul		=price * quantity;
					row.find('.subtotal').val(mul)||0;
					var sum=0;
					var inputs	= $(".subtotal");
					for(var i = 0; i < inputs.length; i++)
					{
			 
					sum = parseInt(sum) + parseInt($(inputs[i]).val());
					sum = sum || 0;
		 
					} 
					$(".sub_total").val(sum);		 
						  
					});
				
				}
				
				 
				function remove()
				{
					
					var row 	= $(this).parent().parent();
					var price	=parseFloat(row.find('.price').val());
					 
					var quantity= parseInt(row.find('.qty').val());
					var mul		=price * quantity;
					row.find('.subtotal').val(mul);
					var sum=0;
					var inputs	= $(".subtotal");
					for(var i = 0; i < inputs.length; i++)
					{
			 
					sum = parseInt(sum) + parseInt($(inputs[i]).val());
					sum = sum || 0;
		 
					} 
					$(".sub_total").val(sum);	
	 
					var amount=0;
					var subtotal=0;
					var dis=0;
					var shipping=0;
					subtotal=parseFloat($('.sub_total').val());
					dis=parseFloat($('#dis').val());
					amount +=subtotal-dis;
					shipping=$('#ship').val();
					
					var tax = $('#tax').val();
					var taxx = tax.split(',');
					
					if(taxx[2]!=0)
					{
					var tsum = amount * parseInt(taxx[1]) / 100;
					tsum = tsum || 0;
					amount += tsum;
					//console.log(total);
					}
					else
					{
						tsum = 0;
					}
					
					amount +=parseFloat(shipping);
					
					amount +=parseFloat($('#misc').val());
				
					$('.tot').val(amount.toFixed(2));	  
					 
				} 
		 
				</script>
				 
				<tr>
				<td colspan="4">Discount </td>
				<td colspan="2"><input type="number" step="0.01" id="dis" name="dis" class="form-control key1" value="<?=isset($edit)?$edit['dis']:'0'?>"></td>
				</tr>
				<tr>
				<td colspan="4">TAX (in %)</td>
				
					<td><select name="tax" id="tax" data-validation="required" class="form-control key1 key3">
					<option value="0,0,3">Select Taxes</option>				
														<?php 
														 
				 if(isset($_GET['pid']))
				 {
					$pid    =$_GET['pid'];
					$tax	=query_by_id("SELECT pi.iid,p.tax from `purchase_items` pi LEFT JOIN `purchase` p on p.id=pi.iid  where pi.iid='$pid' and pi.active='0' and pi.branch_id='".$branch_id."'",[],$conn)[0];
					 
				 ?>
				  
				 <optgroup label="Inclusive Taxes">
				 <?php
			  
				$sql2="SELECT * FROM `tax` where active=0 order by title asc";
				$result2=query_by_id($sql2,[],$conn);
				foreach($result2 as $key=>$row2) 
				{
				 ?>
				<option value="<?php echo $row2['id']; ?>,<?php echo $row2['tax']; ?>,0" <?php if(isset($edit)){ if($tax['tax'] == $row2['id']){ echo "selected" ;} }?><?php echo $row2['title']; echo $tax['tax']; ?>><?php echo $row2['title']; ?></option>
			    <?php } ?>
			    </optgroup>
			    <optgroup label="Exclusive Taxes">
				<?php 
				$sql2="SELECT * FROM `tax` where active=0 order by title asc";
				$result2=query_by_id($sql2,[],$conn);
				foreach($result2 as $key=>$row2) 
				{
				?>
                <option value="<?php echo $row2['id']; ?>,<?php echo $row2['tax']; ?>,1"  <?php if(isset($edit)){ if($tax['tax'] == $row2['id']){ echo "selected" ;} }?>><?php echo $row2['title']; ?></option>
			    <?php }  ?>
				</optgroup>              
				</select>
				</td>
				<?php 
				}
				 				
				else 
				{ 
				?>
			 
				<optgroup label="Inclusive Taxes">
				<?php 
				$sql2="SELECT * FROM `tax` where active=0 order by title asc";
				$result2=query_by_id($sql2,[],$conn);
				foreach($result2 as $key=>$row2) 
				{
				?>
                <option value="<?php echo $row2['id']; ?>,<?php echo $row2['tax']; ?>,0"><?php echo $row2['title']; ?></option>
				<?php } ?>
				</optgroup>
				<optgroup label="Exclusive Taxes">
				<?php 
				$sql2="SELECT * FROM `tax` where active=0 order by title asc";
				$result2=query_by_id($sql2,[],$conn);
				foreach($result2 as $key=>$row2) 
				{
				?>
                <option value="<?php echo $row2['id']; ?>,<?php echo $row2['tax']; ?>,1"><?php echo $row2['title']; ?></option>
				<?php } ?>
				</optgroup>              
				</select></td>
				<?php } ?> 													
				</tr>
				<tr>
				<td colspan="4">Misc </td>
				<td colspan="2"><input id="misc" step="0.01" type="number" name="misc" value="<?=isset($edit)?$edit['misc']:'0';?>" class="form-control key1" value="0"></td>
				</tr>
			    <tr>
				<td colspan="4">Shipping Charges</td>
				<td colspan="2"><input type="number" step="0.01" id="ship" name="ship" value="<?=isset($edit)?$edit['ship']:'0'?>" class="form-control key1" value="0"></td>
				</tr>
				
				<tr>
				<td colspan="4">Total Charges</td>
				<td colspan="2"><input type="text" class="form-control tot"   name="tot" value="<?=isset($edit)?$edit['tot']:'0'?>" readonly></td>
				</tr>
				<tr>
				<td colspan="4">Amount Paid</td>
				<td colspan="2"><input type="number" step="0.01" class="form-control k" id="amount_paid" value="<?=isset($edit)?$edit['payment']:'0'?>" name="paid" value="0" ></td>
				</tr>
				<tr>
				<td colspan="4">Payment Mode </td>
				<td colspan="2">
				<select class="form-control" name="paymode">
				<?php 		
									$sql5	="select * from payment_mode where active=0 order by id asc";
									$result5	=query_by_id($sql5,[],$conn);
									foreach($result5 as $row5)
									{
									?>
				<option value="<?=$row5['id']?>" <?php if($edit['paymode']==$row5['id']) { echo "selected" ;} ?>><?=$row5['name']?></option>
									<?php 
									}  
									?>
				</select>
				</td>
			    </tr>
				<tr>
			    <td colspan="4">Credit</td>
				<Script>
				$(function()
				{
				$('#vendor').blur(function(){
			 	
				if($('#vendorid').val() != '')
				{
				var vendor_id=$('#vendorid').val();	
				}
				 
				$.ajax({
				url:'autocomplete/getpurchase.php?term='+vendor_id,
				type:'post',
				success:function(data)
				{
					var d=JSON.parse(data);
					
				 
					if( d[0]['credit'])
					{
						 $('#c').val(d[0]['credit']);
					}
					 
					 
				}
								
				});	
				});	
				});
				</script>
				<?php 
						$sql6="SELECT * from payments where active=0 and branch_id='".$branch_id."'";
						$result6=query_by_id($sql6,[],$conn);
						foreach($result6 as $row6)
						{
								
						}
				?>
			    <td colspan="2"><input type="text"  class="form-control" id="credit" name="credit" value="<?=isset($edit)?$edit['credit']:'0'?>" readonly>
				<input type="hidden"  class="form-control" id="c" name="credit1" readonly></td>
				</tr>
				<tr>
				<td colspan="4"><textarea name="detail" class="form-control" rows="5" placeholder="Write Notes About Purchase here..." id="textArea"></textarea></td>
				<td colspan="2">
				<?php 
				if(isset($edit))
				{	
				?>
				<button type="submit" name="edit-submit" class="btn btn-info pull-right">Update Purchase</button></td>
				<?php } else { ?>
				<button type="submit" name="submit" class="btn btn-info pull-right">Add Purchase</button></td>
				<?php } ?>
				</tr>
			    </tbody>
											</table>
											</div>
									</div>
								</div>
							</div>
						</form>
						
						

						</div>
					</div>
					<!-- Row ends -->
					
					<div class="row">
						
						<div class="col-lg-12">
						
						
							<div class="panel">
								<div class="panel-heading">
									<h4>Purchase history</h4>
								</div>
								<div class="panel-body">
							
							<div class="row">
							
							
							<div class="col-lg-2 col-md-4 col-sm-4 col-xs-12">
											<div class="form-group">
												<label for="userName">Vendor</label>
												<input type="number" step="0.01" class="form-control" name="amount" placeholder="0" required>
											</div>
							</div>
							
							<div class="col-lg-2 col-md-4 col-sm-4 col-xs-12">
											<div class="form-group">
												<label for="userName">Date of purchase</label>
												<input type="number" step="0.01" class="form-control" name="amount" placeholder="0" required>
											</div>
										</div>
										
										
							
							
							
										<div class="col-lg-2 col-md-4 col-sm-4 col-xs-12">
											<div class="form-group">
												<label for="userName">Payment status</label>
												<select class="form-control" name="role">
																		<option value="1" selected >All</option>
																		<option value="1">Pending</option>
																		<option value="1">Paid</option>
																	
																		
																		
												
																	</select>
											</div>
							</div>
							
										<div class="col-lg-2 col-md-4 col-sm-4 col-xs-12">
											<div class="form-group">
												<label for="userName">  </label><br>
												<button type="submit" name="submit" class="btn btn-info">Filter</button>

											</div>
										</div>
							
							
						<div class="row">
									<div class="col-lg-12">
										<div class="table-responsive">
										<table id="table" class="table table-bordered no-margin">
											<thead>
												<tr>
													<th>Invoice</th>
													<th>Date of Purchase</th>
													<th>Vendor</th>
													<th>Total</th>
													<th>Payment Mode</th>
													<th>Manage</th>
												</tr>
											</thead>
											<tbody>
											<?php
												$sql1="SELECT p.*,pm.name as p_mode from purchase p left join payment_mode pm on pm.id=p.paymode where p.active=0 and p.branch_id='".$branch_id."' order by id desc";
												$result1=query_by_id($sql1,[],$conn);
												foreach($result1 as $row1) 
												{
											?>
												<tr>
												<th><?php echo $row1['inv']; ?></th>
												<th><?php echo $row1['dop']; ?></th>
												<th><?php echo getvendor($row1['vendor']); ?></th>
												<th><?php echo $row1['tot']; ?></th>
												<th><?php echo $row1['p_mode']; ?></th>
												<th><a href="addpurchase.php?pid=<?php echo $row1['id']; ?>" ><button class="btn btn-info btn-xs" type="button">Edit</button></a></th>
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
		<!-- Container fluid ends -->
<script>
				 
		function upcase()
			{
				$('.pro').on('input keyup keydown',function()
				{	
					var row=$(this).parent().parent();
					var str=row.find('.pro').val();
					var result=str.charAt(0).toUpperCase() + str.slice(1).toLowerCase();
					row.find('.pro').val(result);
				});
			}
</script>		
		
		
		
<script type="text/javascript" src="./ajax/ajax.js"></script>
<script type='text/javascript'>//<![CDATA[
$(window).on('load', function(){

	$(".table").on("keyup", ".key", function () 
	{
		sumup();
	 
	});
});
</script>
		
<script type='text/javascript'>//<![CDATA[
$(document).on("click", '.remm', function() {
  
	remove();
	 
});
 	
</script>
			 

<script type='text/javascript'>//<![CDATA[
$(document).on("change", '.act', function() {
  
  var str = $('.act').val();
  if(str=="Credit/Debit Card" || str=="Cheque")
	  $('#detail').show();
  else
	  $('#detail').hide();
	sumup();
	 
});

$( document ).ready(function() {
    $('#detail').hide();
});

</script>


		
<script>
function sumup()
{
	
	 var pric = 0;
	 var  sum = 0;
	 var inputs = $(".price");
	 for(var i = 0; i < inputs.length; i++){
		 var pri = parseInt($(inputs[i]).val());
		 pri = pri || 0;
		 sum = parseInt(sum) + pri;
		 $("#sum").html("Rs. "+sum);
	}	
	//alert(sum);
		$("#tot").val(sum);
		var dis = parseInt($('#dis').val());
		var ship = parseInt($('#ship').val());
		var misc = parseInt($('#misc').val());
		
		dis = dis || 0;
		ship = ship || 0;
		misc = misc || 0;
		var tax = $('#tax').val();
		tax = tax || 0;
		var total = sum;
		$("#tot").val(total);
		
		var tt = 0;
		if(tax!=0)
			tt = sum * parseInt(tax) / 100;
		else
			tt = 0;
		var tot = sum - parseInt(dis) + tt + ship + misc;
		tot = tot || 0;
		$("#tot").val(tot);
		//$("#pay").val(tot);
		var pay = parseInt($('#pay').val());
		pay = pay || 0;
		var credit =  tot - pay;
		credit = credit || 0 ;
		//$("#credit").val(credit);
}
</script>

<script>
	(function ($) {
    $.fn.codeScanner = function (options) {
        var settings = $.extend({}, $.fn.codeScanner.defaults, options);

        return this.each(function () {
            var pressed = false;
            var chars = [];
            var $input = $(this);

            $(window).keypress(function (e) {
                var keycode = (e.which) ? e.which : e.keyCode;
                if ((keycode >= 65 && keycode <= 90) ||
                    (keycode >= 97 && keycode <= 122) ||
                    (keycode >= 48 && keycode <= 57)
                ) {
                    chars.push(String.fromCharCode(e.which));
                }
                // console.log(e.which + ":" + chars.join("|"));
                if (pressed == false) {
                    setTimeout(function () {
                        if (chars.length >= settings.minEntryChars) {
                            var barcode = chars.join('');
                            settings.onScan($input, barcode);
                        }
                        chars = [];
                        pressed = false;
                    }, settings.maxEntryTime);
                }
                pressed = true;
            });

            $(this).keypress(function (e) {
                if (e.which === 13) {
                    e.preventDefault();
                }
            });

            return $(this);
        });
    };

    $.fn.codeScanner.defaults = {
        minEntryChars: 8,
        maxEntryTime: 100,
        onScan: function ($element, barcode) {
            $element.val(barcode);
			dummy(barcode);
        }
    };
})(jQuery);

 function dummy(barcode){
	
	var row	=$('.TextBoxContainer').last().children();
	row.find('.barcode').val(barcode);
	var dat = row.find('.barcode').val();
	
	
	$.ajax
		({
			url: "autocomplete/productBilling.php?barcode="+barcode,
			type: "POST",
			success:function(data)
			{   
			    var d = JSON.parse(data.trim());
				
				if(!d['fail']){
				row.find('.product_id').val(d[0]['id']); 
				row.find('.pro').val(d[0]['value']); 
				row.find('.vol').val(d[0]['volume']);
				row.find('.vu').val(d[0]['unit']);
				row.find('.price').val(d[0]['price']);
				row.find('.qty').val('1');
				
				var pr = row.find('.product_id').val();
					
				if(pr !='')
				{
					var bol = checkstock(pr);
					
				}
				 
				if(bol>1)
				{
				
					
					row.find(".product_id").val("");
					row.find(".pro").val("");
					row.find(".qt").val("0.00");
					row.find('.price').val("0.00");
					row.find('.qty').val('0');
					row.find('.subtotal').val('0.00');
					var row2	=$('.TextBoxContainer').prev().last().children();
					var qty=parseInt(row2.find('.qty').val());	
					
					row2.find('.qty').val(qty+1);
					var quantity= parseInt(row2.find('.qty').val());
					var price	=parseFloat(row2.find('.price').val()||0);
					var mul		=parseFloat(price * quantity);
					row2.find('.subtotal').val(mul)||0;
				}else{
					 var quantity= parseInt(row.find('.qty').val());
					 var price	=parseFloat(row.find('.price').val()||0);
					 var mul		=parseFloat(price * quantity);
					 row.find('.subtotal').val(mul)||0;
					 //$('#btnAdd').click();
				}
				
			
				
				
				
			
				var sum=0;
				var inputs	= $(".subtotal");
				
				for(var i = 0; i < inputs.length; i++)
				{
		 
				sum = parseInt(sum) + parseInt($(inputs[i]).val());
				sum = sum || 0;
	 
				} 
				$(".sub_total").val(sum);
				if(bol<=1){
					$('#btnAdd').click();
				}
				
			   
					var amount=0;
					var subtotal=0;
					var dis=0;
					var shipping=0;
					subtotal=parseFloat($('.sub_total').val());
					dis=parseFloat($('#dis').val());
					dis=dis||0;
					amount +=subtotal-dis;
					shipping=$('#ship').val();
					shipping=shipping||0;
					var tax = $('#tax').val();
					var taxx = tax.split(',');
					
					if(taxx[2]!=0)
					{
					var tsum = amount * parseInt(taxx[1]) / 100;
					tsum = tsum || 0;
					amount += tsum;
					//console.log(total);
					}
					else
					{
						tsum = 0;
					}
					
					amount +=parseFloat(shipping);
					
					amount +=parseFloat($('#misc').val());
				
					$('.tot').val(amount.toFixed(2));
					var amount_id=$('.tot').val();
					
					
					
					
					var ap=$('#amount_paid').val();
					
					var credit=amount_id-ap;
					$('#credit').val(credit.toFixed(2));
					}else{
						toastr.warning("All stock has been sold.");
					}
					
			},
			error:function (){}
		});
} 

$(function(){
		
	var row	=$('.TextBoxContainer').last().children();
	row.find('.barcode').codeScanner();
	
});

</script>

<?php 
include "footer.php"; 

function setvendor($ven)
{
	global $conn;
	global $branch_id;
	$aid=get_insert_id("INSERT INTO `vendor`(`name`,`active`,`branch_id`) VALUES ('$ven',0,'$branch_id')",[],$conn); 
	return $aid;
}

function getvendor($vid){
	global $conn;
	global $branch_id;
	$sql="SELECT * from vendor where id=$vid and branch_id='".$branch_id."'";
	$result=query_by_id($sql,[],$conn);
	foreach($result as $row) {
		return $row['name'].'('.$row['cont'].')';
	     
	}
}
?>
<script>
$(document).ready(function() {
    $('#example').DataTable( {
        "scrollX": true
    } );
} );
</script>