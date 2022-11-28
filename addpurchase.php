<?php
	include "./includes/db_include.php";
	$branch_id = $_SESSION['branch_id'];
	$hidden_payment_method = '2,7,8,9';
	$date = date('Y-m-d');
	$uid = isset($_SESSION['uid']);
	if(isset($_GET['pid']) && $_GET['pid']>0){
		$pid = $_GET['pid'];
		$edit = query_by_id("SELECT p.bpaid,CONCAT(p.tax,',',p.taxtype)as tax_type,p.details as pur_detail,p.dis as total_discount,p.inv,p.vendor,p.dop, p.paid, p.ship,p.due,p.total,p.pay_method,v.* from purchase p LEFT JOIN vendor v on v.id=p.vendor  where p.id=$pid and p.active='0' and p.branch_id='".$branch_id."'",["id"=>$pid],$conn)[0];
	}
	
	if(isset($_POST['submit']))
	{
		$vendor      = addslashes(trim($_POST['vendor_id']));
		$vendor_name = addslashes(trim($_POST['vendor']));
		$contact_no = addslashes(trim($_POST['contact_no']));
		$gst_no = addslashes(trim($_POST['gst_no']));
		
		$vendor_id = query_by_id("SELECT id from `vendor` where cont=:cont  and active='0' and branch_id='".$branch_id."'",["cont"=>$contact_no],$conn);
        if($vendor_id)
		{
			$vendor_id = $vendor_id[0]['id'];
		}else{
			$vendor_id = get_insert_id("INSERT INTO  `vendor` set name=:name, cont=:cont, gst=:gst, active='0', branch_id='".$branch_id."'",["name"=>$vendor_name,"cont"=>$contact_no,"gst"=>$gst_no],$conn); 
		}
		
		$dop 	    = addslashes(trim($_POST['dop']));
		$inv 	    = addslashes(trim($_POST['inv']));
		$tax 		= $_POST['tax'];
		$taxx 		= explode(",",$tax);
		$tax 		= $taxx[0];
		$taxtype 	= $taxx[2];
		
		if($_POST['total_disc_row_type'][$t] == '0'){
			$dis 	 = 'pr,'.$_POST['dis'];
			}else{
			$dis 	= CURRENCY.','.$_POST['dis'];
		}
		$misc           = addslashes(trim($_POST['misc']));
		$ship	        = addslashes(trim($_POST['ship']));
		$tot 	        = addslashes(trim($_POST['total']));
		$pay 	        = addslashes(trim($_POST['paymode']));
		$paid           = addslashes(trim($_POST['amount_paid']));
		$credit         = addslashes(trim($_POST['credit']));
		$details        = addslashes(trim($_POST['detail']));
		$subtotal       = addslashes(trim($_POST['subtotal']));
		$amount_paid    = addslashes(trim($_POST['amount_paid']));
		$total          = addslashes(trim($_POST['total']));
		$due            = addslashes(trim($total - $amount_paid));
		
		$aid = get_insert_id("INSERT INTO `purchase` set `inv`='$inv',`vendor`='$vendor_id',`dop`='$dop',`tax`='$tax',`taxtype`='$taxtype',`dis`='$dis',`ship`='$ship',`total`='$tot',`due`='$due',`pay_method`='$pay',`details`='$details',`paid`='$paid',`bpaid`='$amount_paid',`subtotal`='$subtotal',`active`='0', `branch_id`='".$branch_id."'",[],$conn);
		
		for($t=0;$t<count($_POST["product"]);$t++)
		{
			$purchase_price   = addslashes(trim($_POST["purchase_price"][$t]));
			$Price            = addslashes(trim($_POST["mrp_price"][$t]));
			$sale_price   	  = addslashes(trim($_POST["sale_price"][$t]));
			$exp_date         = addslashes(trim($_POST["exp_date"][$t]));
			$qt 	    	  = addslashes(trim($_POST["quantity"][$t]));
			$product_id		  = addslashes(trim($_POST['product_id'][$t]));
			$product_name	  = addslashes(trim($_POST['product'][$t]));
			$volume			  = addslashes(trim($_POST['volume'][$t]));	
			$unit			  = addslashes(trim($_POST['volume_unit'][$t]));	
			
			$pro_id = query_by_id("SELECT id from `products` where id=:id  and active='0'",["id"=>$product_id],$conn);
			if($pro_id)
			{
				$product_id = $pro_id[0]['id'];
			} else {
				$product_id = get_insert_id("INSERT INTO  `products` set name=:name, price =:price, volume =:volume, unit =:unit, active='0', branch_id='0'",["name"=>$product_name, "price"=>$mrp, "volume"=>$volume, "unit"=>$unit],$conn); 
			}
			query("INSERT INTO `purchase_items` set `iid` = '$aid',`vendor`='$vendor_id',`product`='$product_name',`product_id`='$product_id',`volume`='$volume',`unit`='$unit',`quantity`='$qt',`mrp`='$mrp',`price`='$purchase_price',`sale_price`='$sale_price', `exp_date`='$exp_date', `active`='0', branch_id='".$branch_id."'",[],$conn); 
			
		}
		
		$_SESSION['t']     = 1;
		$_SESSION['tmsg']  = "Purchased Saved Successfully";
		header('location:addpurchase.php');
		exit();
	}
	
	if(isset($_POST['edit-submit']))
	{
		$pid 		    = addslashes(trim($_GET['pid']));
		$vendor         = addslashes(trim($_POST['vendor_id']));
		$vendor_name    = addslashes(trim($_POST['vendor']));
		$contact_no = addslashes(trim($_POST['contact_no']));
		$gst_no = addslashes(trim($_POST['gst_no']));
		
		$vendor_id = query_by_id("SELECT id from `vendor` where cont=:cont  and active='0' and branch_id='".$branch_id."'",["cont"=>$contact_no],$conn);
        if($vendor_id)
		{
			$vendor_id = $vendor_id[0]['id'];
		}else{
			$vendor_id = get_insert_id("INSERT INTO  `vendor` set name=:name,active='0',cont=:cont, gst=:gst, branch_id='".$branch_id."'",["name"=>$vendor_name, "cont"=>$contact_no, "gst"=>$gst_no ],$conn); 
		}
		
		$dop 	    = addslashes(trim($_POST['dop']));
		$inv 	    = addslashes(trim($_POST['inv']));
		$tax 		= $_POST['tax'];
		$taxx 		= explode(",",$tax);
		$tax 		= $taxx[0];
		$taxtype 	= $taxx[2];
		
		if($_POST['total_disc_row_type'][$t] == '0'){
			$dis 	 = 'pr,'.$_POST['dis'];
		}else{
			$dis 	= CURRENCY.','.$_POST['dis'];
		}
		
		$misc       = addslashes(trim($_POST['misc']));
		$ship	    = addslashes(trim($_POST['ship']));
		$tot 	    = addslashes(trim($_POST['total']));
		$pay 	    = addslashes(trim($_POST['paymode']));
		$paid       = addslashes(trim($_POST['amount_paid']));
		$credit     = addslashes(trim($_POST['credit']));
		$details    = addslashes(trim($_POST['detail']));
		$subtotal   = addslashes(trim($_POST['subtotal']));
		
		$amount_paid = $_POST['amount_paid'];
		$total     = $_POST['total'];
		$due = $total - $amount_paid;
		
		$data = query_by_id("SELECT * FROM purchase_items where iid='".$pid."' and branch_id='".$branch_id."'",[],$conn);

		query("DELETE from `purchase_items` where iid='$pid' and branch_id='".$branch_id."'",[],$conn);
		query("DELETE from `service_slip_add_products` where purchase_id='$pid' and branch_id='".$branch_id."'",[],$conn);
		
		query("UPDATE `purchase` set `inv`='$inv',`vendor`='$vendor_id',`dop`='$dop',`tax`='$tax',`taxtype`='$taxtype',`dis`='$dis',`ship`='$ship',`total`='$tot',`pay_method`='$pay',`details`='$details',`subtotal`='$subtotal',`due`='$due',`paid`='$paid',`bpaid`='$amount_paid' where id='$pid' and active='0' and branch_id='".$branch_id."' ",[],$conn);
		
		for($t=0;$t<count($_POST["product"]);$t++)
		{
			$purchase_price   = addslashes(trim($_POST["purchase_price"][$t]));
			$Price            = addslashes(trim($_POST["mrp_price"][$t]));
			$sale_price   	  = addslashes(trim($_POST["sale_price"][$t]));
			$exp_date         = addslashes(trim($_POST["exp_date"][$t]));
			$qt 	    	  = addslashes(trim($_POST["quantity"][$t]));
			$product_id		  = addslashes(trim($_POST['product_id'][$t]));
			$product_name	  = addslashes(trim($_POST['product'][$t]));
			$volume			  = addslashes(trim($_POST['volume'][$t]));	
			$unit			  = addslashes(trim($_POST['volume_unit'][$t]));	
			
			$pro_id = query_by_id("SELECT id from `products` where id=:id  and active='0'",["id"=>$product_id],$conn);
			if($pro_id)
			{
				$product_id = $pro_id[0]['id'];
				query("UPDATE `products` set price=:price where id=:id ",["price"=>$mrp, "id"=>$product_id],$conn);
			}else{
				$product_id = get_insert_id("INSERT INTO  `products` set name=:name, price =:price, volume =:volume, unit =:unit, active='0', branch_id='0'",["name"=>$product_name, "price"=>$mrp, "volume"=>$volume, "unit"=>$unit],$conn); 
			}

			$stock_id = get_insert_id("INSERT INTO `purchase_items` set `iid` = '$pid',`vendor`='$vendor_id',`product`='$product_name',`product_id`='$product_id',`volume`='$volume',`unit`='$unit',`quantity`='$qt',`mrp`='$mrp',`price`='$purchase_price', `sale_price`='$sale_price', `exp_date`='$exp_date', `active`='0', branch_id='".$branch_id."'",[],$conn); 
		    foreach($data as $res){
		        query("UPDATE product_usage SET stock_id='".$stock_id."' WHERE stock_id='".$res['id']."' and product_id='".$res['product_id']."' and branch_id='".$branch_id."'",[],$conn);
		    }
		}
		
		$_SESSION['t']  = 1;
		$_SESSION['tmsg']  = "Purchased Updated Successfully";
		header('location:addpurchase.php?pid='.$_GET['pid']);
		exit();
	}
	if(isset($_GET['del'])){
		$d = $_GET['del'];
		query("update `purchase` set active=1 where id=$d",[],$conn);
		query("update `purchase_items` set active=1 where iid=$d",[],$conn);
        $_SESSION['t']  = 1;
        $_SESSION['tmsg']  = "Purchased Removed Successfully";
        header('LOCATION:addpurchase.php');
		exit();
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
</style>


<div class="dashboard-wrapper">
	
	<!-- Main container starts -->
	<div class="main-container">
		
		<!-- Row starts -->
		<div class="row gutter">
			
			<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
				<form action="" method="post" id="main-form">
					<div class="panel">
						<div class="panel-heading">
							<h4>Purchase from vendor / add stock</h4>
						</div>
						<div class="panel-body">
							<div class="row">
							<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 w-20">
								<div class="form-group">
									<label for="vendor">Product vendor name <span class="text-danger">*</span></label>
									<input type="text" class="vendor form-control" id="vendor" name="vendor" value="<?=isset($edit)?$edit['name']:''?>" placeholder="Autocomplete (Phone)" required>
									<input type="hidden" name="vendor_id" id="vendorid" value="<?=isset($edit)?$edit['id']:''?>" class="clt">
								</div>
							</div>
							
							<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 w-20">
								<div class="form-group">
									<label for="contact_no">Contact no <span class="text-danger">*</span></label>
									<input type="text" class="form-control" id="contact_no" name="contact_no" value="<?=isset($edit)?$edit['cont']:''?>" placeholder="Contact no" required>
								</div>
							</div>
							
							<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 w-20">
								<div class="form-group">
									<label for="gst_no">GST number</label>
									<input type="text" class="form-control" id="gst_no" name="gst_no" value="<?=isset($edit)?$edit['gst']:''?>" placeholder="GST">
								</div>
							</div>
							
							
							<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 w-20">
								<div class="form-group inv_no">
									<label for="inv">Invoice give by vendor <span class="text-danger">*</span></label>
									<input type="text" class="form-control" name="inv" placeholder="Invoice no" value="<?=isset($edit)?$edit['inv']:''?>" onblur="checkinvoice_no(this.value, <?= $pid!=''?$pid:'\'\'' ?>)" required>
									<span class="text-danger"></span>
								</div>
							</div>
							
							<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 w-20">
								<div class="form-group">
									<?php $date = date('Y-m-d'); ?>
									<label for="dop">Date of purchase <span class="text-danger">*</span></label>
									<?php $date = date('Y-m-d'); ?>
									<input type="text" class="form-control dob_annv_date" value="<?=isset($edit)?$edit['dop']:$date?>" name="dop"  value="" required readonly>
									
								</div>
							</div>
							
							
							
							<div class="clearfix"></div>
							
							<div class="col-lg-12">
								<div class="table-responsive">
									<table id="myTable" class="table table-bordered">
										<thead>
											<tr>
												<th style="width:3%"></th>
												<th style="width:60%">Product</th>
												<th style="width:15%">Price</th> <!-- Price price -->
												<th style="width:15%">Price</th> <!-- purchcase price -->
												<th>Sale price</th>
												<th style="width:10%">Quantity</th>
												<th style="width: 5%">Exp. date</th>
												<th style="width:7%">Total</th>
											</tr>
										</thead>
										<tbody>
											<?php 
												if(isset($_GET['pid']))
												{
													$sr=1;
													$sql1	="SELECT pi.*,pi.volume,pi.unit from purchase_items pi LEFT JOIN  products p on p.id = pi.product_id where pi.iid=$pid and pi.active=0 and pi.branch_id='".$branch_id."'";
													$result =query_by_id($sql1,[],$conn);
													foreach($result as $row)
													{
													?>
													
													<tr id="TextBoxContainer" class="TextBoxContainer">
														<td style="vertical-align: middle;"><span class="sno"><span class="icon-dots-three-vertical"></span></span></td>
														<td>
															
															<table class="inner-table-space">
																<tr>
																	<td width="60%">
																		
																		<input type="text" name="product[]" class="pro form-control" value="<?=isset($_GET['pid'])?$row['product']:''?>" placeholder="Product (Autocomplete)" required>
																		<input type="hidden" name="product_id[]" class="product_id" value="<?=isset($_GET['pid'])?$row['product_id']:''?>">
																		<span class="serviceErrorMessage"></span>
																		
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
																				<option  value="<?=$row1['id']?>" <?php if(($row1['id']==$row['unit'])) { echo "selected" ;} ?>><?=$row1['name']?></option>
																				<?php 
																				}  
																			?>
																			
																		</select>
																	</td>
																	<!-- <td width="10%"> <i class="icon-straighten" style="font-size:32px;"></i></td> -->
																	
																</tr>
																
															</table><span class="check-status" style="font-size: 7pt"></span></td>
															
															<td><input type="number" step="0.01" name="mrp_price[]" value="<?=isset($_GET['pid'])?$row['mrp']:''?>" class="form-control price key mrp" placeholder="0.00" required></td>
															<td><input type="number" step="0.01" name="purchase_price[]" value="<?=isset($_GET['pid'])?$row['price']:''?>" class="form-control price key purchase_price" placeholder="0.00" required></td>
															<td><input type="number" step="0.01" name="sale_price[]" value="<?=isset($_GET['pid'])?$row['sale_price']:'0.00'?>" class="form-control price key sale_price" placeholder="0.00" onblur="cmpsalewithmrp($(this))" required></td>
															<td><input type="number" class="form-control qty key" name="quantity[]" placeholder="0.00" value="<?=isset($_GET['pid'])?$row['quantity']:'1'?>" min="0"></td>
															<td><input type="text" name="exp_date[]" class="form-control urdate" value="<?= $row['exp_date']!='0000-00-00'?$row['exp_date']:date('Y-m-d', strtotime(date('Y-m-d') . ' +11 months')) ?>" required readonly></td>
															<td><input type="text" class="form-control subtotal key" name="total_price[]" placeholder="0.00" readonly></td>
													</tr>
													
												<?php } } else	{ ?>
												
												<tr id="TextBoxContainer" class="TextBoxContainer">
													<td style="vertical-align: middle;"><span class="sno"><span class="icon-dots-three-vertical"></span></span></td>
													<td>
														
														<table class="inner-table-space">
															
															<tr>
																<td width="60%">
																	
																	<input type="text" name="product[]" class="pro form-control" placeholder="Product (Autocomplete)" required>
																	<input type="hidden" name="product_id[]" class="product_id" >
																	<input type="hidden" name="barcode[]" class="barcode form-control" value="<?=isset($_GET['pid'])?$row['barcode']:''?>" placeholder="barcode(Autocomplete)" required>
																	<span class="serviceErrorMessage"></span>
																</td>
																<td width="20%">				
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
																			<option  value="<?=$row['id']?>" <?php if((isset($edit['unit'])) && ($edit['unit']==$row['name'])) { echo "selected";}  ?>><?=$row['name']?></option>
																			<?php 
																			}  
																		?>
																		
																	</select>
																</td>
																
																<!-- <td width="10%"> <i class="icon-straighten" style="font-size:32px;"></i></td> -->
															</tr>
															
														</table>
														<span class="check-status" style="font-size: 7pt"></span>
													</td>
													<td><input type="number" step="0.01" name="mrp_price[]" class="form-control price key mrp" placeholder="0.00" required></td>
													<td><input type="number" step="0.01" name="purchase_price[]" class="form-control price key purchase_price"  placeholder="0.00" value="0.00" required></td>
													<td><input type="number" step="0.01" name="sale_price[]" value="<?=isset($_GET['pid'])?$row['sale_price']:'0.00'?>" class="form-control price key sale_price" onblur="cmpsalewithmrp($(this))" placeholder="0.00" required></td>
													<td><input type="number" class="form-control qty  key" name="quantity[]" placeholder="0.00" value="1" min="0" required></td>
													<td><input type="text" value="<?= date('Y-m-d', strtotime(date('Y-m-d') . ' +11 months')) ?>" name="exp_date[]" class="form-control urdate" value="" readonly required></td>
													<td><input type="text" class="form-control subtotal key" name="total_price[]" placeholder="0.00" value="0" readonly></td>
												</tr>
											<?php } ?>
											<tr id="addBefore">
												<td colspan="8"><button type="button" id="btnAdd" class="btn btn-success pull-right"><i class="fa fa-plus" aria-hidden="true"></i>Add product</button></td>
											</tr>
											
											<tr>
												<td class="total" colspan="7">Subtotal</td>
												<td><div id="sum" style="display: inline;"><?=($edit['subtotal'])?CURRENCY." ".$edit['subtotal']:'0'?></div>
												<input type="hidden" id="sum2" value="<?=($edit['subtotal'])?$edit['subtotal']:'0'?>" name="subtotal"></td>
											</tr>
											
											<tr>
												<td class="total" colspan="6">Discount</td>
												<td width="40%">
												<input type="number" step="0.01" class="key1 form-control" name="dis" id="total_disc"  value="<?=($edit['total_discount'])?explode(",",$edit['total_discount'])[1]:'0'?>" placeholder="Discount Amount"></td>
												<td width="60%">
													<select class="form-control total_disc_row_type" name="total_disc_row_type" id="disc_row_type">
														<option <?=(substr($edit['total_discount'],0,3) == 'pr,')?'value="0" Selected':'value="0"'?>>%</option>
														<option <?=(explode(",",$edit['total_discount'])[0] == CURRENCY)?'value="1" Selected':'value="1"'?>><?=CURRENCY?></option>
													</select>
												</td>
											</tr>
											
											<tr>
												<td class="total" colspan="6">Taxes</td>
												<td colspan="2"><select name="tax" id="tax" data-validation="required" class="form-control">
													<option value="0,0,3">Select Taxes</option>
													<optgroup label="Inclusive Taxes">
														<?php 
															$sql2="SELECT * FROM `tax` where active=0 and branch_id='".$branch_id."' order by title asc";
															$result2=query_by_id($sql2,[],$conn);
															foreach ($result2 as $row2) {
															?>
															<option <?=((($edit['tax_type']) == ($row2['id'].',0'))?'value="'.$row2['id'].','.$row2['tax'].',0"Selected':'value="'.$row2['id'].','.$row2['tax'].',0"')?>><?php echo $row2['title']; ?></option>
														<?php } ?>
													</optgroup>
													<optgroup label="Exclusive Taxes">
														<?php 
															$sql2="SELECT * FROM `tax` where active=0 and branch_id='".$branch_id."' order by title asc";
															$result2=query_by_id($sql2,[],$conn);
															foreach ($result2 as $row2) {
															?>
															<option <?=((($edit['tax_type']) == ($row2['id'].',1'))?'value="'.$row2['id'].','.$row2['tax'].',1"Selected':'value="'.$row2['id'].','.$row2['tax'].',1"')?>><?php echo $row2['title']; ?></option>
														<?php } ?>
													</optgroup>              
												</select></td>
											</tr>
											<!--<tr>
												<td colspan="4">Misc </td>
												<td colspan="2"><input id="misc" step="0.01" type="number" name="misc" value="<?=isset($edit)?$edit['misc']:'0';?>" class="form-control key1" value="0"></td>
											</tr>-->
											<tr>
												<td colspan="6">Shipping charges</td>
												<td colspan="3"><input type="number" step="0.01" id="ship" name="ship" value="<?=isset($edit)?$edit['ship']:'0'?>" class="form-control key1" value="0"></td>
											</tr>
											
											<tr>
												<td colspan="6">Total charges</td>
												<td colspan="3"><input type="text" id="total" class="form-control" name="total" placeholder="Total Amount" value="<?=isset($edit)?$edit['total']:'0'?>" readonly=""></td>
											</tr>
											<tr>
												<td colspan="6">Amount paid</td>
												<td colspan="3"><input type="number" step="0.01" class="form-control k" id="amount_paid" value="<?=isset($edit)?$edit['paid']:'0'?>" name="amount_paid" value="0" ></td>
											</tr>
											<tr>
												<td colspan="6">Payment mode </td>
												<td colspan="3">
													<?php 
														$sql5 = "select * from payment_method where status='1' and id not in ($hidden_payment_method) and branch_id='".$branch_id."' order by id asc";
														$result5 =query_by_id($sql5,[],$conn);
														
													?>
													<select class="form-control" name="paymode">
														<?php 		
															
															foreach($result5 as $row5)
															{
															?>
															<option value="<?=$row5['id']?>" <?php if((isset($edit))&&($edit['pay_method']==$row5['id'])) { echo "selected" ;} ?>><?=$row5['name']?></option>
															<?php 
															}  
														?>
													</select>
												</td>
											</tr>
											<tr>
												<td colspan="6">
												    Amount due/credit
												    <?php
												        $pay_history = query("SELECT SUM(paid) as paid_amount FROM payments WHERE bill_id='".$edit['inv']."' AND purchase_id='".$pid."' AND active = 0 and branch_id='".$branch_id."'",[],$conn)[0]['paid_amount'];
												        if($pay_history > 0){
												            echo "<p><span class='text-danger'>Amount paid via installments : </span><strong>".number_format($pay_history,2)."</strong></p>";
												        }
												    ?>
												</td>
												<td colspan="3">
												    <?php if($pay_history > 0) { 
												        $p_amount = $edit['due']-$pay_history;
												    ?>
												        <input type="text"  class="form-control" id="credit" name="credit" value="<?= $p_amount ?>" readonly>
												        <input type="hidden" id="installment_price" value="<?= $pay_history ?>" />
												    <?php } else { 
												        $p_amount = $edit['due'];
												    ?>
												        <input type="text"  class="form-control" id="credit" name="credit" value="<?= $p_amount ?>" readonly>
												        <input type="hidden" id="installment_price" value="0" />
												    <?php } ?>
												</td>
											</tr>
											<tr>
												<td colspan="8"><textarea name="detail" class="form-control" rows="5" placeholder="Write notes about purchase here..." id="textArea"><?php if(isset($edit)){ echo $edit['pur_detail']; } ?></textarea></td>
										</tr>
									</tbody>
								</table>

							</div>
						</div>
						<div class="clearfix"></div>
						<div class="col-sm-12">
							<?php  if(isset($edit)){ 
							    if($p_amount != 0){ ?>
								<button type="submit" name="edit-submit" class="btn btn-info pull-right"><i class="fa fa-pencil-square-o" aria-hidden="true"></i>Update stock</button>
							<?php } else { ?>
							    <script>
						            $('input, textarea, select').prop('disabled',true);
						        </script>
							<?php } 
							} else { ?>
								<button type="submit" name="submit" class="btn btn-success pull-right"><i class="fa fa-plus" aria-hidden="true"></i>Add stock</button>
							<?php } ?>
						</div>
					</div>
				</div>
			</form>
			
			
			
		</div>
	</div>
	<!-- Row ends -->

</div>

</div>
</div>
<?php include "footer.php"; ?>

<?php if(isset($_SESSION['user_type']) && $_SESSION['user_type'] == 'superadmin'){ ?>
    <script>
        function stockProductValidator(){
        }
    </script>
<?php } else { ?>
    <script>
        function stockProductValidator(){
        	$('.pro').on('keyup',function(){ $(this).parent().find('.product_id').val(""); });
        	$('.pro').on('blur',function(){
        		var row = $(this).parent();
        		var productID = row.find('.product_id').val(); 
        		if(productID === ''){ 
        		    row.find('.serviceErrorMessage').remove();
        		    row.append('<span class="serviceErrorMessage">Please select product from list.</span>'); 
        		    $(this).css("border-color","#ff7171e8"); 
        		    $(this).val("");
        		} else{
        		    row.find('.serviceErrorMessage').remove();
        		    $(this).css("border-color","");
        		}
        	});
        }
    </script>
<?php } ?>

<script>
	// function to find duplicate Product
	function findDuplicate(e){
		duplicate_arr=[];
		var row=e.parents(".TextBoxContainer");
		var val=$('.product_id');
		val.each(function(){
			duplicate_arr.push($(this).val());
		});
		
		for(var i=0;i<duplicate_arr.length;i++){
			for(var j=i+1;j<duplicate_arr.length;j++){
				if(duplicate_arr[i]==duplicate_arr[j]){
					
					row.remove();
					toastr.warning("Duplicate Entry");
				}  
			}
		}
	}
	
	$(window).on('load', function(){
		var e=$(this);
		var row=$(".TextBoxContainer");
		var k=1;
		row.each(function(){
			$(".TextBoxContainer").eq(k).find('.sno').html('<span class="remm icon-trash2 " style="color:red;" onclick="sumup();$(this).parent().parent().parent().remove();"></span>');
			k++;
			
			
			var price 	=$(this).find('.purchase_price').val();
			var quantity=$(this).find('.qty').val();
			var total_price=price * quantity;
			$(this).find('.subtotal ').val(total_price.toFixed(2));
 			sumup();
			
		});
	});
	
	
	
    $(function(){
        autocomplete_serr();
        barcode_scanner();
        price_change();
        change_event();
        stockProductValidator();
        $(".vendor").on('keyup',function(){
           if($(this).val() == ''){
               $('#vendorid').val('');
           } 
        });
        $(".vendor").autocomplete({
			source: "autocomplete/vendor.php",
			minLength: 1,
            select: function(event, ui) {
                $('#vendorid').val(ui.item.id);
                $('#contact_no').val(ui.item.cont);
                $('#gst_no').val(ui.item.gst);
			}				
		});	
        
        $("#btnAdd").bind("click", function() {
        	var empty_fields = [];
			$('.pro').each(function(){
				if($(this).val() == ''){
					empty_fields.push('empty_field');
					$(this).addClass('invalid');
				} else {
					$(this).removeClass('invalid');
				}
			});
			$('.price').each(function(){
				if($(this).val() == '' || $(this).val() == '0'){
					$(this).addClass('invalid');
					empty_fields.push('empty_field');
				} else {
					$(this).removeClass('invalid');
				}
			});
			$('.qt').each(function(){
				if($(this).val() == '0' || $(this).val() == ''){
					$(this).addClass('invalid');
					empty_fields.push('empty_field');
				} else {
					$(this).removeClass('invalid');
				}
			});
			$('.qty').each(function(){	
				if($(this).val() == '' || $(this).val() == '0'){
					$(this).addClass('invalid');
					empty_fields.push('empty_field');
				} else {
					$(this).removeClass('invalid');
				}
			});
			$('.sale_price').each(function(){	
				if($(this).val() == '0' || $(this).val() == ''){
					$(this).addClass('invalid');
					empty_fields.push('empty_field');
				} else {
					$(this).removeClass('invalid');
				}
			});
			if(empty_fields && empty_fields.length == 0){
	            var clonetr = $("#TextBoxContainer").clone().addClass('TextBoxContainer');
	            clonetr.removeAttr('id');
	            clonetr.find('.sno').html('<span class="remm icon-trash2 " style="color:red;" onclick="$(this).parent().parent().parent().remove();sumup();"></span>');
	            clonetr.find('input:not(input[type=number])').val('');
	            clonetr.find('.urdate').val('<?= date('Y-m-d', strtotime(date('Y-m-d') . ' +11 months')) ?>');
	            clonetr.find('input[type="number"]:not(.qty)').val('0');
	            clonetr.find('select.v_unit option:first-child').prop('selected',true);
	            $("#addBefore").before(clonetr);
	            autocomplete_serr();
	            barcode_scanner();
	            price_change();
	            change_event();
				$('.TextBoxContainer').last().children().find('.qty').val("1");
				dp();
				stockProductValidator();
			}
		});
	});

	$('.pro').on('keyup', function(){
		var pt = $(this).parent().parent().parent().parent().parent().parent();
		pt.find('input:not(.pro, .urdate)').val('');
		pt.find('.qt, .v_unit').prop('readonly', false);
	});
	
	$(document).on("click input", '.start_time:eq(0)', function() {
		var s_time=$('.start_time:eq(0)').val();
		if(s_time !=""){ 
		$('#time').val(s_time);}
	});
	
	function autocomplete_serr(){
        $(".pro").autocomplete({
			source: function(request, response) {
				var ser_stime = '';
				$.getJSON("autocomplete/addproduct.php", { term: request.term, action : 'stock_product'}, response);
			},
			minLength: 1,
			select:function (event, ui){  
				var row1= $(this).parents('tr');
				row1.find('.product_id').val(ui.item.id);
				row1.find('.qt').val(ui.item.volume);
				row1.find('.qt, .v_unit').prop('readonly', true);
				row1.find('.mrp').val(ui.item.price);
				row1.find('.price').val(ui.item.price);
				row1.find('.v_unit option[value="'+ui.item.unit+'"]').prop('selected',true);
				row1.find(".check-status").html("");
				row1.find(".sale_price").click();
				// findDuplicate($(this));
			}
		});	
	}
	
	function barcode_scanner(){
        $(".pro").on('blur',function(){
            var barcode = $(this).val();
            var row = $(this).parents('tr');
            $.ajax({
                url  : "ajax/fetch_barcode_product.php",
				type : "post",
				data : { action : 'addstockbarcode', barcode : barcode },
				dataType : 'json',
				success : function(res){
				    if(res != ''){
				        row.find('.pro').val(res.value);
				        row.find('.product_id').val(res.id);
        				row.find('.vol').val(res.volume);
        				row.find('.price').val(res.price);
        				row.find('.v_unit option[value="'+parseInt(res.unit)+'"] ').prop('selected',true);
        				row.find(".check-status").html("");
        				row.find('.subtotal').val(res.price);
        				row.find('.qty').val(1);
        				// findDuplicate($(this));
			            sumup();
			            stockProductValidator();
			            $('#btnAdd').click();
			            row.closest('tr').next('tr').find('.pro').focus();
				    }
				}
            });
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
		var sums = 0;
		var sump = 0;
		var sumt = 0;
		var sum  = 0;
		var ids  = $(".serr");
		var inputs = $(".price");
		for(var i = 0; i < inputs.length; i++){
            var service = $(ids[i]).val().split(',');
            if(service[0]=="sr"){
				sums = sums + parseFloat($(inputs[i]).val());
			}
            else if(service[0]=="pr"){
				sump = sump + parseFloat($(inputs[i]).val());
			}
            sum = parseFloat(sum) + parseFloat($(inputs[i]).val());
            $("#sum").html("<?= CURRENCY ?> "+sum);
		}
	}
	
	function sumup(){
		var pric = 0;
		var sums = 0;
		var sump = 0;
		var sumt = 0;
		var sum = 0;
		var ids = $(".serr");
		var total=0;
		var fin =0 ;
		var installment_price = $('#installment_price').val();
		var inputs = $(".subtotal");
		for(var i = 0; i < inputs.length; i++){
			sump = sump + parseFloat($(inputs[i]).val());
			sum = parseFloat(sum) + parseFloat($(inputs[i]).val());
			sum = sum || 0;
			$("#sum").html("<?= CURRENCY ?> "+sum);
			$("#sum2").val(sum);
			$("#total").val(sum);
		}
		
		var paid = $('#paid').val();
		
		$("#sums").val(sums);
		$("#sump").val(sump);
		$("#sum").val(sum);
		var sum2=$('#sum2').val();
		var dis = parseFloat($("#disc").val());
		dis = dis || 0;
		
		
		if($('.total_disc_row_type').val() == 1){
			var tot_disc =$("#sum2").val();
			var tot_dis=$('#total_disc').val();
			total = tot_disc - tot_dis;
			
			}else{ 
			var tot_disc =$("#sum2").val();
			var tot_dis	 =$('#total_disc').val();
			total 		 = sum2-(tot_disc * tot_dis / 100);
			
		}
		
		var tax = $('#tax').val();
		var taxx = tax.split(',');
		
		if(taxx[2]!=0){
			var tsum = total * parseFloat(taxx[1]) / 100;
			tsum = tsum || 0;
			total = total + tsum;
			}else{
			tsum = 0;
		}
		
		var shipping = parseFloat($('#ship').val())||0;
		total = total + shipping;
		
		$("#total").val(total.toFixed(2));
		
		//$("#amount_paid").val(total.toFixed(2));
		var adv = $("#adv").val()||0;
		var pend = total - parseFloat(adv);
		
		$("#pend").html(pend.toFixed(2));
		
		var paid = $("#amount_paid").val()||0;
		
		if(paid <= total){
			$("#amount_paid").val(paid);
			var fin = (total - paid)-parseFloat(installment_price);
		}else{
			
		  //  toastr.warning("Paid amount should not be greater then total amount");
		    $("#amount_paid").val(paid);
			$("#credit").val("");
		}
		$("#credit").val(fin.toFixed(2));
		$("#due").html(fin.toFixed(2));
		$("#chng").val(fin.toFixed(2));
	
	}
	
	function price_change(){
		$(".pr,#tax,#amount_paid").on("keyup change", function (e) {
			sumup();
		});
		
	}
	function change_event(){
		
		$('.pro').on('blur',function(){
			var row = $(this).parent().parent();
			checkproduct(row);
		});
		
		$(".qt").on("keyup keypress change click", function () {
			var row = $(this).parent().parent(); 
			price_calculate(row);
			sumup();
			
		});
		
		$(".disc_row,.disc_row_type").on("keyup keypress change keydown", function () {
			var row = $(this).parent().parent().parent().parent().parent().parent(); 
			price_calculate(row);
			sumup();
			
		});
		
		$("#total_disc,.total_disc_row_type,#ship").on("keyup keypress change keydown", function () {
			sumup(); 
		});
		
		$(".price").on("keyup keypress change keydown", function () {
			sumup(); 
		});
		
		$(".qty,.price").on("keyup click keydown ", function () {
			var row   	=$(this).parent().parent();
			var price 	=row.find('.purchase_price').val();
			var quantity=row.find('.qty').val();
			var total_price=price * quantity;
			row.find('.subtotal ').val(total_price.toFixed(2));
			sumup();
		});
	}
	
	function checkproduct(row) {
		var products = row.find('.pro').val().replace(/\s/g, '');
		var product_id = row.find('.product_id').val();
		$.ajax({
			url: "ajax/checkservice.php",
			data:{products:products,product_id:product_id},
			type: "POST",
			success:function(data){
				if(data == '1'){
					row.parent().parent().parent().find(".check-status").html("Duplicate products . Please select products from list").css("color","red");
					row.find('.pro').val("");
					}else{
					row.parent().parent().parent().find(".check-status").html("");
				}
			},
			error:function (){}
		});
	}

	// Function to check invoice number 

	function checkinvoice_no(invoice_no,vendor_id){
	    var exist_vendor = $('#vendorid').val();
	    if(exist_vendor > 0){
    		var invoice_no = $.trim(invoice_no);
    		var vendor_id = $.trim(vendor_id);
    		if(invoice_no != ''){
    			$.ajax({
    				url: "ajax/checkservice.php",
    				data:{invoice_no:invoice_no,vendor_id:vendor_id, 'action':'checkinvoice'},
    				type: "POST",
    				success:function(data){
    					if(data == '1'){
    						$('.inv_no input').val('');
    						$('.inv_no span').text('Invoice no already exist');
    					} else{
    						$('.inv_no span').text('');
    					}
    				},
    				error:function (){}
    			});
    		} else{
    			$('.inv_no input').val('');
    		}
	    }
	}

	// comparing sale price with mrp price, sale pric should be grater then mrp price

	function cmpsalewithmrp(blurdiv){
		var div = blurdiv.parent().parent();
		var sale_price = div.find('.sale_price').val();
		var purchase_price = div.find('.purchase_price').val();
		if(parseFloat(sale_price) < parseFloat(purchase_price)){
			toastr.error('Sale price should be greater then Price');
			div.find('.sale_price').val('0');
			div.find('.subtotal').val('0');
			sumup();
		}
	}
	
	</script>					