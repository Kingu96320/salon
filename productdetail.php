<?php
	include "./includes/db_include.php";
	$branch_id = $_SESSION['branch_id'];
	if(isset($_GET['pid']) && $_GET['pid']>0)
	{
		$pid = $_GET['pid'];
		$stock_id = $_GET['item_id'];
		$edit=query_by_id("SELECT * from products where id=$pid",[],$conn);
	    $product_detail = query_by_id("SELECT p.*, u.name as unit_name FROM products p LEFT JOIN units u ON u.id = p.unit WHERE p.id='".$pid."'",[],$conn)[0];
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
					<?php
					    $sql1="select p.id as iid,p.inv as invoice,p.dop as date,0 as debit,pi.quantity as credit,3 as type,v.name, v.cont as contact from `purchase_items` pi inner join purchase p on p.id=pi.iid left join vendor v on v.id=p.vendor where pi.product_id='$pid' and p.active='0' and pi.active='0' and pi.id='".$stock_id."' and pi.branch_id='".$branch_id."'"
						. "union select i.id as iid,ii.iid as invoice,i.doa as date,ii.quantity as debit,0 as credit,2 as type,c.name, c.cont as contact from `invoice_items_".$branch_id."` ii inner join invoice_".$branch_id." i on i.id=ii.iid left join client c on c.id=i.client where SUBSTRING_INDEX(ii.service,',',-1)='$pid' and ii.type='Product' and i.active='0' and ii.active='0' and ii.product_stock_batch='".$stock_id."' and ii.branch_id='".$branch_id."'"
						. "union SELECT pu.invoice_id as iid, pu.invoice_id as invoice, i.doa as date, pu.quantity as debit, 0 as credit, 4 as type, b.name, b.cont as contact FROM product_usage pu LEFT JOIN units u ON u.id = pu.unit LEFT JOIN invoice_".$branch_id." i ON i.id = pu.invoice_id LEFT JOIN beauticians b ON b.id = pu.service_provider WHERE pu.product_id='".$pid."' AND pu.stock_id='".$stock_id."' AND pu.status='1' and pu.type='1' and pu.branch_id='".$branch_id."'"
						. "union SELECT pu.id as iid,pu.id as invoice,Date(pu.date) as date,pu.quantity as debit,0 as credit,1 as type,b.name, b.cont as contact FROM `productused` pu left join beauticians b on b.id=pu.staff WHERE pu.active='0' and SUBSTRING_INDEX(pu.product,',',-1)='$pid' and pu.product_stock_batch='".$stock_id."' and pu.branch_id='".$branch_id."' order by date desc, invoice desc";
					    $bal = query_by_id($sql1,[],$conn);
					    $credit = 0;$debit  = 0;
					    foreach($bal as $b) {
					        $credit = $credit + $b['credit'];
					        $debit = $debit + $b['debit'];
					    }
					?>
					<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
						<div class="panel">
							<div class="panel-heading">
								<h4>Purchase Reports (<?= $product_detail['name']." ".$product_detail['volume']." ".$product_detail['unit_name'] ?>)<span class="pull-right"><i class="fa fa-shopping-basket text-danger" aria-hidden="true"></i>In stock: <?php echo getstock($pid, 0, $stock_id); ?></span></h4>
							</div>
							<div class="panel-body">
								<div class="table-responsive">
									<table id="printtable" class="table table-bordered no-margin table_datatable">
										<thead>
											<tr>
												<th>Date</th>
												<th>Invoice</th>
												<th>Type</th>
												<th>Vendor/ client/ Service provider</th>
												<th>Credit</th>
												<th>Debit</th>
												<th>View</th>
											</tr>
										</thead>
										<tbody>
											<?php 
												$credit = 0;
												$debit  = 0;
												$result1=query_by_id($sql1,[],$conn);
												foreach($result1 as $row1) {
												?>
												<tr>
													<td><?php echo $row1['date']; ?></td>
													<td><?php echo $row1['invoice']; ?></td>
													<td><?php echo (($row1['type']=='3')?'Inventory Purchase':(($row1['type']=='2')?'Bill':'Product Used')); ?></td>
													<td><?php echo $row1['name'].' ('.$row1['contact'].')'; ?></td>
													<td><?php 
														$credit =  $row1['credit'];
														if($row1['credit']!=0){
    														if($product_detail['unit'] == 1){
    														    $stock_in_ml = ($credit*$product_detail['volume']);
    														    echo $credit." (".number_format($stock_in_ml,3)." ".$product_detail['unit_name'].")";
    														} else if($product_detail['unit'] == 4){
    														    $stock_in_ml = ($credit*$product_detail['volume']);
    														    echo $credit." (".number_format($stock_in_ml,3)." ".$product_detail['unit_name'].")";
    														} else {
    														    $stock_in_ml = ($credit*$product_detail['volume']);
    														    echo $credit." (".number_format($stock_in_ml)." ".$product_detail['unit_name'].")";
    														}
														} else {
														    echo '-';
														}
													?></td>
													<td><?php 
														$debit = $row1['debit'];
														if($row1['debit']!=0){
    														if($product_detail['unit'] == 1){
    														    if($row1['type']=='4'){
    														        echo number_format($row1['debit'],3)." ".$product_detail['unit_name'];
    														    } else {
        														    $stock_in_ml = ($debit*$product_detail['volume']);
        														    echo $debit." (".number_format($stock_in_ml,3)." ".$product_detail['unit_name'].")";
    														    }
    														} else if($product_detail['unit'] == 4){
    														    if($row1['type']=='4'){
    														        echo number_format($row1['debit'],3)." ".$product_detail['unit_name'];
    														    } else {
        														    $stock_in_ml = ($debit*$product_detail['volume']);
        														    echo $debit." (".number_format($stock_in_ml,3)." ".$product_detail['unit_name'].")";
    														    }
    														} else if($product_detail['unit'] == 3){
    														    if($row1['type']=='2'){
    														         $stock_in_mg = ($debit*$product_detail['volume']);
    														         echo $debit." (".number_format($stock_in_mg)." ".$product_detail['unit_name'].")";
    														    }
    														} else if($product_detail['unit'] == 2){    										
    														    if($row1['type']=='1'){
    														         $stock_in_mg = ($debit*$product_detail['volume']);
    														         echo $debit." (".number_format($stock_in_mg)." ".$product_detail['unit_name'].")";
    														    } else {
    														        echo number_format($row1['debit'],3)." ".$product_detail['unit_name'];
    														    }
    														} else {
    														    $stock_in_ml = $debit;
    														    echo $debit." (".number_format($stock_in_ml)." ".$product_detail['unit_name'].")";
    														}
														} else {
														    echo '-';
														} 
													?></td>
													<td>
														<?php
															if($row1['type'] == 3 || $row1['type'] == 2){ ?>
															<a href="<?php echo (($row1['type']=='3')?'addpurchase.php?pid=':(($row1['type']=='2')?'billing.php?beid=':'')).$row1['iid']; ?>">
													        <button type="button" class="btn btn-info btn-xs"><i class="fa fa-eye" aria-hidden="true"></i>view</button>				    
													   </a>
													   <?php } ?>
													</td>
												</tr>
												<?php 
												}
											?>
										</tbody>
									</table>
									
									
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
	<?php 


include "footer.php";

function getinvoice($iid){
	global $conn;
	global $branch_id;
	$sql="SELECT * from invoice_".$branch_id." where id=$iid and active=0 and branch_id='".$branch_id."'";
	$result=query_by_id($sql,[],$conn);
	if ($result) {
		return $$result['doa'];
		}else{
		return "NA";
	}
}

function getpurchase($iid){
	global $conn;
	global $branch_id;
	$sql="SELECT * from purchase where id=$iid and active=0 and branch_id='".$branch_id."'";
	$result=query_by_id($sql,[],$conn);
	if ($result) {
		return $result['dop'];
		}else{
		return "NA";
	}
}

function getpurinv($iid){
	global $conn;
	global $branch_id;
	$sql="SELECT * from purchase where id=$iid and active=0 and branch_id='".$branch_id."'";
	$result=query_by_id($sql,[],$conn);
	if ($result) {
		return $$result['inv'];
		}else{
		return "NA";
	}
}

function getclient($iid){
	global $conn;
	global $branch_id;
	$sql="SELECT * FROM `client` where id=$iid and active='0' and branch_id='".$branch_id."'";
	$result=query_by_id($sql,[],$conn);
	if ($result) {
		return $result['name'];
		}else{
		return "NA";
	}
}
?>