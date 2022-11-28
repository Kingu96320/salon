<?php
include_once "./includes/db_include.php";
if(!empty($_GET['billHis'])){
	$data=query("select * from product_billing_info pbi inner join product_billing pb on(pbi.bill_id=pb.bill_id) inner join customer_info ci on(ci.cid=pbi.cid) where pb.product_id='".$_GET['product_id']."'",array(),$conn);
$new_row=[];	
if($data){
		foreach($data as $da){
			$new_row[] =array(
				 '0'=>$da['fname'],
				 '1'=>$da['quantity'],
				 '2'=>$da['amount_payable'],
				 '3'=>$da['amount_paid'],
				 '4'=>$da['amount_pending'],
			);
		} 
	}
  echo ' { "data": ' . json_encode($new_row) . '}';
	exit(0);
}
$uid = $_SESSION['uid'];


if(isset($_GET['pid']) && $_GET['pid']>0)
{
    $pid = $_GET['pid'];
    $edit = query_by_id("SELECT p.*,v.* from purchase p LEFT JOIN vendor v on v.id=p.vendor  where p.id=$pid",["id"=>$pid],$conn)[0];

}
if(isset($_POST['submit'])){

$am_payble=$_POST['tot']-$_POST['dis'];
if(empty($_POST['cid'])){
$cid=get_insert_id("insert into customer_info set fname='".$_POST['customer_name']."',contact_no='".$_POST['contact_no']."'",array(),$conn);
}else{
$cid=$_POST['cid'];
}

    $bill_id=get_insert_id("insert into product_billing_info set cid='".$cid."',sub_total='".$_POST['tot']."',amount_payable='".$am_payble."',amount_paid='".$_POST['paid']."',payment_mode='".$_POST['paymode']."',discount='".$_POST['dis']."',tax_info='".$_POST['tax']."',amount_pending='".$_POST['credit']."',employee_id='".$_SESSION['uid']."'",array(),$conn);
 
	$i=0;
	while(isset($_POST['product_id'][$i])){
		get_insert_id("insert into product_billing set bill_id='".$bill_id."',product_id='".$_POST['product_id'][$i]."',quantity='".$_POST['quantity'][$i]."',total_price='".$_POST['total_price'][$i]."'",array(),$conn);
		$i++;
	}

	
}
include "topbar.php";
include "header.php";
include "menu.php";
?>

 
<?php 
	$sql2="SELECT title,tax,active,id FROM `tax` where active=0 order by title asc";

	
?>
<div class="row-fluid">
    <div class="span12">
        <div class="widget">
            <div class="widget-header">
                <div class="title">
                    <a id="redgForm">Point of sale for products & services</a>
                </div>
               
            </div>
            <?php 
			$bill_id=query("select max(bill_id) as bill_id from product_billing_info",array(),$conn)[0]['bill_id'];
			$bill_id+=1;
			?>
			<div class="widget-body">
				
                <form class="form-horizontal sub-form no-margin" name="form1" id="form1" action='' method="post">
                    <div class="row">
                        
                        <div class="control-group col-lg-2">
                            <label class="control-label">
                               Bill no.
                            </label>
                            <div class="controls">
                                <input readonly type="text" class="form-control"
                                       name="subscription_type" 
                                       placeholder="108" 
                                       required="required"
                                       value="<?php echo $bill_id ?>"/>
                            </div>
                        </div>
						 <div class="control-group col-lg-2">
                            <label class="control-label">
                               Bill date
                            </label>
                            <div class="controls">
                                <input type="text" class="form-control date"
                                       name="bill_type" 
                                       placeholder="" 
                                       required="required"
                                       value="<?php echo date('Y-m-d'); ?>" readonly />
                            </div>
                        </div>
						
						
                        <div class="control-group col-lg-4">
                            <label class="control-label">
                               Client / customer name
                            </label>
                            <div class="controls">
                                <input type="text" id="cust"  class="form-control cust"
                                       name="customer_name" 
                                       placeholder="Parbhat Jain" 
                                       required="required"
                                       value="<?php echo (isset($query['customer_name'])) ? ucfirst(strtolower($query['customer_name'])) : NULL ?>"/>
								<input type="hidden" name="cid" id="cid" value="<?=isset($edit)?$edit['cid']:''?>" class="clt">	   
                            </div>
                        </div>
                        <div class="control-group col-lg-4">
                            <label class="control-label">
                                Client / customer number
                            </label>
                            <div class="controls">
                                <div class="input-prepend input-append">
                                    <input type="text"  class="form-control" id="contact_no" placeholder="98883-35156"  name="contact_no" required="required" value="<?php echo (isset($query['subscription_duration'])) ? $query['subscription_duration'] : NULL ?>"/>
                                </div>
                            </div>
                        </div>
                        
						
						<div class="clearfix"></div>
                     
<div class="col-lg-12" style="margin-top:10px;">
										<div class="table-responsive">
													<table id="myTable" class="table table-bordered">
														<thead>
															<tr>
																<th style="width:5%">Sl.No.</th>
																<th style="width:30%">Product name</th>
																<th style="width:30%">Unit price</th>
																<th style="width:30%">Unit(s)</th>
																<th style="width:5%">Total</th>
															</tr>
														</thead>
														<tbody>
														<?php 
														if(isset($_GET['pid']))
														{
														$sr=1;
														$sql1	="SELECT pi.*,p.volume,p.unit from purchase_items pi LEFT JOIN  products p on p.id = pi.product_id where pi.iid=$pid and pi.active=0";
														$result =query_by_id($sql1,[],$conn);
														foreach($result as $row)
														{
														?>
														
													<tr id="TextBoxContainer">
														<td class="sno"><?=$sr++;?></td>
																												<td>
																													
																
																	
																	<tr>
																	<td width="60%">
																	
																	<input type="text" name="product[]" class="pro form-control" value="<?=isset($_GET['pid'])?$row['product']:''?>" placeholder="Product(Autocomplete)" required>
																	<input type="hidden" name="barcode[]" class="barcode form-control" value="<?=isset($_GET['pid'])?$row['barcode']:''?>" placeholder="barcode(Autocomplete)" required>
																	<input type="hidden" name="product_id[]" class="product_id" value="<?=isset($_GET['pid'])?$row['product_id']:''?>">
																
																	</td>
																	 
																	<td width="20%">
																	
																								
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
																	<option  value="<?=$row1['id']?>" <?php if((isset($edit['unit'])) && ($edit['unit']==$row1['name'] || ($row1['id']==$row['unit']))) { echo "selected" ;} ?>><?=$row1['name']?></option>
																	<?php 
																	}  
																	?>
													 
														</select>
														</td>
																										
														<td width="10%"> <i class="icon-straighten" style="font-size:32px;"></i></td>
														</tr>
																															
																														</td>
																																
														<td><input type="text" name="purchase_price[]" value="<?=isset($_GET['pid'])?$row['price']:''?>" class="form-control price key" placeholder="0.00" required></td>
														<td><input type="number" class="form-control qty key" name="quantity[]" placeholder="0.00" value="<?=isset($_GET['pid'])?$row['quantity']:'1'?>"></td>
														<td><input type="text" class="form-control subtotal key" value="0.00" name="total_price[]" placeholder="0.00" readonly></td>
													</tr>
				
												<?php } } else	{ ?>
					
														<tr id="TextBoxContainer" class="TextBoxContainer">
																		<td class="sno">1</td>
																		<td>
																		
															
															<input type="text" name="product[]" class="pro form-control" placeholder="Product (Autocomplete)" required>
															<input type="hidden" name="barcode[]" class="barcode form-control" value="<?=isset($_GET['pid'])?$row['barcode']:''?>" placeholder="barcode(Autocomplete)" required>
															<input type="hidden" name="product_id[]" class="product_id" >
																											
																											
															</td>
															<td><input type="text" name="purchase_price[]" readonly="readonly" class="form-control price key"  placeholder="0.00" required></td>
															<td><input type="number" class="form-control qty  key" name="quantity[]" placeholder="0.00" required></td>
															<td><input type="text" class="form-control subtotal key" name="total_price[]" value="0.00" placeholder="0.00" readonly></td>
															
														
															
														</tr>
													<?php } ?>
													<tr id="addBefore">
													<td colspan="5"><button type="button" id="btnAdd" class="btn btn-info pull-right">Add product</button></td>
													</tr>
													
													<tr>
													<td colspan="4">Subtotal</td>
													<td colspan="2"><input type="number" step="0.01"   name="subtotal" value="0.00" class="sub_total form-control" placeholder="0.00" readonly></td>
													</tr>
													
													<tr>
													<td colspan="4">Discount </td>
													<td colspan="2"><input type="number" step="0.01" id="dis" name="dis" class="form-control key1" value="<?=isset($edit)?$edit['dis']:'0'?>"></td>
													</tr>
													<tr>
													<td colspan="4">TAX (in %)</td>
													
														<td><select name="tax" id="tax" data-validation="required" class="form-control key1 key3">
																	
																							<?php 
																							 
													 if(isset($_GET['pid']))
													 {
														$pid    =$_GET['pid'];
														$tax	=query_by_id("SELECT pi.iid,p.tax from `purchase_items` pi LEFT JOIN `purchase` p on p.id=pi.iid  where pi.iid='$pid' and pi.active='0'",[],$conn)[0];
														 
													 ?>
													  
													 <optgroup label="Inclusive Taxes">
													 <?php
												  
													
													$result2=query_by_id($sql2,[],$conn);
													foreach($result2 as $key=>$row2) 
													{
													 ?>
													<option value="<?php echo $row2['id']; ?>,<?php echo $row2['tax']; ?>,0" <?php if(isset($edit)){ if($tax['tax'] == $row2['id']){ echo "selected" ;} }?><?php echo $row2['title']; echo $tax['tax']; ?>><?php echo $row2['title']; ?></option>
													<?php } ?>
													</optgroup>
													<optgroup label="Exclusive Taxes">
													<?php 
													
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
													
													$result2=query_by_id($sql2,[],$conn);
													foreach($result2 as $key=>$row2) 
													{
													?>
													<option value="<?php echo $row2['id']; ?>,<?php echo $row2['tax']; ?>,0"><?php echo $row2['title']; ?></option>
													<?php } ?>
													</optgroup>
													<optgroup label="Exclusive Taxes">
													<?php 
													
													$result2=query_by_id($sql2,[],$conn);
													foreach($result2 as $key=>$row2) 
													{
													?>
													<option value="<?php echo $row2['id']; ?>,<?php echo $row2['tax']; ?>,1"><?php echo $row2['title']; ?></option>
													<?php } ?>
													</optgroup>              
													</select></td>
													<?php } ?> 													
													</tr >
													<tr style="style='display:none;'">
													<td colspan="4">Misc </td>
													<td colspan="2"><input id="misc" step="0.01" type="number" name="misc" value="<?=isset($edit)?$edit['misc']:'0';?>" class="form-control key1" value="0"></td>
													</tr>
													<tr>
													<td colspan="4" class="hidden">Shipping Charges</td>
													<td colspan="2" class="hidden"><input type="number" step="0.01" id="ship" name="ship" value="<?=isset($edit)?$edit['ship']:'0'?>" class="form-control key1" value="0"></td>
													</tr>
													
													<tr>
													<td colspan="4">Amolunt Payable</td>
													<td colspan="2"><input type="text" class="form-control tot"   name="tot" value="<?=isset($edit)?$edit['tot']:'0'?>" readonly></td>
													</tr>
													<tr>
													<td colspan="4">Amount Paid</td>
													<td colspan="2"><input type="number" step="0.01" class="form-control k" id="amount_paid" value="<?=isset($edit)?$edit['payment']:'0'?>" name="paid" value="0" ></td>
													</tr>
													<tr>
													<td colspan="4">Payment Mode </td>
													<td colspan="2">
														<?php 
														$sql5	="select *  from payment_mode where active='0' order by id asc";
																			$result5 =query_by_id($sql5,[],$conn);
																		
														?>
													<select class="form-control" name="paymode">
														<?php 		
																			
																			foreach($result5 as $row5)
																			{
																			?>
														<option value="<?=$row5['id']?>" <?php if((isset($edit))&&($edit['paymode']==$row5['id'])) { echo "selected" ;} ?>><?=$row5['name']?></option>
																			<?php 
																			}  
																			?>
														</select>
													</td>
													</tr>
													<tr>
													<td colspan="4">AMount Left</td>
													<td colspan="2"><input type="text"  class="form-control" id="credit" name="credit" value="<?=isset($edit)?$edit['credit']:'0'?>" readonly></td>
													</tr>
													<tr>
													<td colspan="4"><textarea name="detail" class="form-control" rows="5" placeholder="Write Notes About Billing here..." id="textArea"></textarea></td>
													<td colspan="2">
													<?php 
													if(isset($edit))
													{	
													?>
													<button type="submit" name="edit-submit" class="btn btn-info pull-right">Submit bill</button></td>
													<?php } else { ?>
													<button type="submit" name="submit" class="btn btn-info pull-right">Submit bill</button></td>
													<?php } ?>
													</tr>
													</tbody>
											</table>
											</div>
									</div>
                
                   
                    </div>
                                
                           
                            
                        
                    
                </form>

            </div>
			<?php //purchase_items
				$products=query("select p.name,sum(pb.quantity) as total_qty,product_id from products p inner join product_billing pb on(p.id=pb.product_id)  group by pb.product_id",array(),$conn);
			?>

			
		
        </div>
    </div>
</div>


</div>


	
<?php //multiple billing ?>
<?php 
include "footer.php"; 

function setvendor($ven)
{
	global $conn;
	$aid=get_insert_id("INSERT INTO `vendor`(`name`,`active`) VALUES ('$ven',0)",[],$conn); 
	return $aid;
}

function getvendor($vid){
	global $conn;
	$sql="SELECT * from vendor where id=$vid";
	$result=query_by_id($sql,[],$conn);
	foreach($result as $row) {
		return $row['name'].'('.$row['cont'].')';
	     
	}
}
?>
<div id="myModal44" class="modal fade col-lg-12" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title" ><span id="productName" >Product</span> Billing History</h4>
      </div>
      <div class="modal-body">
        <div class="col-lg-12">
				<div class="table-responsive">
				<table id="titable" class="titable table table-bordered no-margin">
					<thead>
						<tr>
							<th>Customer name</th>
							<th>Soled Qty</th>
							<th>Amount Payable</th>
							<th>Amount Paid</th>
							<th>Amount Left</th>
						</tr>
					</thead>
					
				</table>
				</div>
			</div>
      </div>
      <div class="modal-footer">
			
      </div>
    </div>

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
              
				
				<script>
						 
						$(function() 
						{
							autocomplete_serr();	
						});
						function autocomplete_serr()
						{
							$('.pro').autocomplete({
							source: "autocomplete/productBilling.php",
							minLength: 1,
							select: function(event,ui) 
							{
								if(ui.item.quantity>ui.item.total_qty){	
							
									var row	=$(this).parent().parent();
									
									row.find('.product_id').val(ui.item.id); 
									row.find('.barcode').val(ui.item.barcode); 
									row.find('.price').val(ui.item.price);
									row.find('.qty').val(1);
									
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
								}else{
									toastr.warning("All stock has been sold.");
								}	
								
							}	
						});	
						
																		
						$(function(){
							 
							$(".price").on("blur",function()
							{
								
								
								var row	=$(this).parent().parent();
								var dat = row.find('.pro').val();
								var product_id = row.find('.product_id').val();
								
								
								row.find('.qty').val('1');
								$.ajax
								({
									url: "autocomplete/productBilling.php?term="+dat,
									type: "POST",
									success:function(data)
									{
										
										var d = JSON.parse(data.trim());
										
										row.find('.product_id').val(d[0]['id']); 
										row.find('.barcode').val(d[0]['barcode']); 
										row.find('.pro').val(d[0]['value']); 
										row.find('.vol').val(d[0]['volume']);
										row.find('.vu').val(d[0]['unit']);
										row.find('.price').val(d[0]['price']);
										
									},
									error:function (){}
								});

							});
						});
					 }
				</script>	
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
				
					$('.tot').val(amount.toFixed(2));
					var amount_id=$('.tot').val();
					
					
					
					
					var ap=$('#amount_paid').val();
					
					var credit=amount_id-ap;
					$('#credit').val(credit.toFixed(2));
					
					});	
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
   // page is fully loaded, including all frames, objects and images
    // alert("window is loaded");
// });
</script>
<script type="text/javascript" src="js/ajax.js"></script>
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
	$(document).ready( function () {
		$('.titable').DataTable();
	} );
</script>		

<script type="text/javascript">
		$(function() { 
			autocomplete_cust();										
		});
		function autocomplete_cust(){
			
			$(".cust").autocomplete({
					source: "autocomplete/cust.php",
					minLength: 1,	
					//autoFocus: true,
				select: function(event, ui) {
					//event.preventDefault();
					//$('#client').val(ui.item.label);
					
					$('#cid').val(ui.item.cid);
					$('#contact_no').val(ui.item.contact_no);
				 }				
			});	
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

<script>
function view_history(pro_id,productName){
	$('#myModal44').modal('show');
	$('#productName').html(productName);
	$('#titable').DataTable().destroy();	
	$('#titable').DataTable( {
                "ajax": "pos.php?billHis=1&product_id="+pro_id,
                "paging":true 
				});

}
</script>
