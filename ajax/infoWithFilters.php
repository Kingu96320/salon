<?php
	include "../includes/db_include.php";
	$branch_id = $_SESSION['branch_id'];
	$date = date('Y-m-d');
	$startDate = $_GET['startDate'];
	$endDate   = $_GET['endDate'];
	
	if(isset($_GET['startDate'])){
	    $start_date = $_GET['startDate'];
	    $start_date = explode("-",$start_date);
	    $start_date = $start_date['1'].'/'.$start_date['2'].'/'.$start_date['0'];
	} else {
	    $start_date = date('m/d/Y');
	}
	
	if(isset($_GET['endDate'])){
	    $end_date = $_GET['endDate'];
	    $end_date = explode("-",$end_date);
	    $end_date = $end_date['1'].'/'.$end_date['2'].'/'.$end_date['0'];
	} else {
	    $end_date = date('m/d/Y');
	}
	
	
	if(isset($_GET['type']) && $_GET['type'] == 'sales'){
	?>
	<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
		<div class="panel">
			<div class="panel-heading">
				<h4 class="pull-left">Sales</h4>
				<span onclick="$('#todaysalesDashboard').html('')" class="text-danger pull-right"><i style="font-size:20px;cursor:pointer;" class="fa fa-times-circle-o" aria-hidden="true"></i></span>
				<div class="clearfix"></div>
			</div> 
			<div class="panel-body">
				<div class="row">
					<div class="table-responsive">
						<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
							<div class="form-group">
								<label class=" control-label">Select dates</label>
								<input type="text" class="form-control" range-attr="daterange" id="daterange" name="date" value="<?= $start_date.' - '.$end_date ?>"  placeholder="01/01/1990 - 12/05/2000" required readonly>
							</div>
						</div>
						<div class="col-lg-1 col-md-1 col-sm-2 col-xs-12">
						    <label>&nbsp;</label>
						    <button type="button" class="btn btn-filter btn-block" onclick="dateFilter($('#daterange').val(),'sales');"><i class="fa fa-filter" aria-hidden="true"></i>Filter</button>
						</div>
						<div class="col-lg-2 col-md-2 col-sm-2 col-xs-12">
						    <label>&nbsp;</label><div class="clearfix"></div>
						    <a href="dailyreport.php?sdate=<?= $startDate ?>&edate=<?= $endDate ?>">
						        <button type="button" class="btn btn-info"><i class="fa fa-eye" aria-hidden="true"></i>View details</button>
						    </a>
						</div>
						<div class="col-md-12">
						    <table id="table" class="table table-bordered no-margin">
							<thead>
								<tr>
									<th>Date of bill</th>
									<th>Client name</th>
									<th>Contact number</th>
									<!--<th>Total</th>-->
									<th>Paid</th>
									<th>Advance</th>
									<th>Discount</th>
									<th>Pending</th>
									<th>Manage</th>
								</tr>
							</thead>
							<tbody>
								<?php
							        $total_discount = 0;
									$dqry = "and doa BETWEEN '$startDate' AND '$endDate'";
									$sql1="SELECT i.id,i.doa,c.name as client_name,c.cont,i.advance,i.due,i.paid,i.subtotal,i.total " 
									. " from invoice_".$branch_id." i LEFT JOIN `client` c on c.id=i.client where i.active=0 and i.branch_id='".$branch_id."' ".$dqry;
									$result1=query_by_id($sql1,[],$conn);
									foreach($result1 as $row1) {
									$total_discount += get_discount($row1['doa'], $row1['id'], 'invoice');
									?>
									<tr>
										<td><?= my_date_format($row1['doa']); ?></td>
										<td><?= $row1['client_name']; ?></td>
										<td><?= $row1['cont']; ?></td>
										<td><?= number_format($row1['paid'],2); ?></td>
										<td><?= number_format($row1['advance'],2); ?></td>
										<td><?= number_format(get_discount($row1['doa'], $row1['id'], 'invoice'),2);  ?></td>
										<td><?= number_format($row1['due'],2); ?></td>
										<td><a href="billing.php?beid=<?php echo $row1['id']; ?>"><button class="btn btn-warning btn-xs" type="button"><i class="fa fa-pencil-square-o" aria-hidden="true"></i>Edit</button></a></td>
									</tr>
									<?php } 
									$sql2="SELECT sum(paid) as total,sum(due) as pending from `invoice_".$branch_id."` where active=0 and type=2 and branch_id='".$branch_id."' ".$dqry. " ORDER BY doa DESC";
									$result2=query_by_id($sql2,[],$conn);
									foreach($result2 as $row2) {		
									?>
									<tr>
									    <td></td>
									    <td></td>
									    <td><b>Total</b></td>
									    <td><b><?=($row2['total'])?number_format($row2['total'],2):'0'?></b></td>
									    <td></td>
									    <td><b><?= number_format($total_discount,2) ?></b></td>
									    <td><b><?=($row2['pending'])?number_format($row2['pending'],2):'0'?></b></td>
									    <td></td>
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
	<br>
	<?php }else if(isset($_GET['type']) && $_GET['type'] == 'appointment'){ ?>
	<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
		<div class="panel">
			<div class="panel-heading">
				<h4 class="pull-left">Appointments</h4>
				<span onclick="$('#todaysalesDashboard').html('')" class="text-danger pull-right"><i style="font-size:20px;cursor:pointer;" class="fa fa-times-circle-o" aria-hidden="true"></i></span>
				<div class="clearfix"></div>
			</div> 
			<div class="panel-body">
				<div class="row">
				    <div class="col-md-12">
					    <div class="table-responsive">
						<table id="table" class="table table-bordered no-margin">
							<thead>
								<tr>
									<th>Date of appointment</th>
									<th>Client name</th>
									<th>Contact</th>
									<th>Appointment time</th>
									<th>Status</th>
									<th>Manage</th>
								</tr>
							</thead>
							<tbody>
								<?php
									$sql1 ="SELECT ai.*,c.name as client,c.cont,aso.name as app_source from `app_invoice_".$branch_id."` ai"
									." LEFT JOIN `app_source` aso on aso.id=ai.role "
									." LEFT JOIN `client` c on c.id=ai.client"
									." where ai.doa='$date' and ai.active=0 and ai.branch_id='".$branch_id."' order by ai.id desc";
									$result1=query_by_id($sql1,[],$conn);
									foreach($result1 as $row1) {
									?>
									<tr>
										<td><?php echo my_date_format($row1['doa']); ?></td>												
										<td><?php echo $row1['client']; ?></td>
										<td><?php echo $row1['cont']; ?></td>
										<td><?php echo my_time_format($row1['itime']); ?></td>
										<td><?= $row1['bill_created_status'] == 1?'Paid':$row1['status']; ?></td>
										<?php 
											$srt = $row1['status'];
											$link = "";
											if($srt=="Converted" || $srt=="Cancelled")
											$link = 'onclick="return false;"';
											else
											$link = '';
										?>
										<td><a href="appointment.php?id=<?=$row1['id']?>">
										    <button type="button" class="btn btn-xs btn-warning"><i class="fa fa-pencil-square-o" aria-hidden="true"></i>Edit</button>
										</a>
											<?php if($row1['ss_created_status'] == 0){ ?>
												
												<?php }if($row1['bill_created_status'] == 0){ ?>
												
												<a href="billing.php?bid=<?=$row1['id']?>">
												    <button type="button" class="btn btn-xs btn-primary"><i class="fa fa-money" aria-hidden="true"></i>Create bill</button>
											    </a>
												
												<?php }else{ ?>
												<button type="button" class="btn btn-xs btn-success"><i class="fa fa-money" aria-hidden="true"></i>Bill paid</button>
											<?php 	} ?>
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
	<br>
	<?php }else if(isset($_GET['type']) && $_GET['type'] == 'enquiry'){ ?>
	<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
		<div class="panel">
			<div class="panel-heading">
				<h4 class="pull-left">Enquiries</h4>
				<span onclick="$('#todaysalesDashboard').html('')" class="text-danger pull-right"><i style="font-size:20px;cursor:pointer;" class="fa fa-times-circle-o" aria-hidden="true"></i></span>
				<div class="clearfix"></div>
			</div> 
			<div class="panel-body">
				<div class="row">
				    <div class="col-md-12">
					    <div class="table-responsive">
						<table id="smstab" class="table tableenquiry table-striped table-bordered no-margin">
							<thead>
								<tr>									
									<th>Name</th>
									<th>Email</th>
									<th>Phone</th>
									<th>Date to follow</th>
									<th>Lead type</th>
									<th>Enquiry for</th>					
									<th>Action</th>
								</tr>
							</thead>
							<tbody>
								<?php
									$sql1="SELECT * from enquiry where active=0 and regon='$date' and branch_id='".$branch_id."' order by id desc";
									$result1=query_by_id($sql1,[],$conn);
									$it = 1;
									if($result1){
									foreach($result1 as $row1) {
									?>
									<tr>									
										<td><?php echo $row1['customer']; ?></td>
										<td><?php echo $row1['email']; ?></td>
										<td><?php echo $row1['cont']; ?></td>
										<td><?php echo my_date_format($row1['datefollow']); ?></td>
										<td><?php echo $row1['type']; ?></td>
										<td><?php echo ($row1['enquiry'] !='')?getEnquiryfor($row1['enquiry']):'' ?></td>			
										<td>
										    <a href="enquiry.php?id=<?php echo $row1['id']; ?>">
										        <button type="button" class="btn btn-xs btn-warning"><i class="fa fa-pencil-square-o" aria-hidden="true"></i>Edit</button>
										    </a> <!--<a href="enquiry.php?d=<?php echo $row1['id']; ?>" onclick="return confirm('Are You Sure?')" class="btn btn-success btn-xs">Delete</a>--></td>
									</tr>
									<?php 
										$it ++ ;
									} } else {
									    ?>
									    <tr><td colspan="7" class="text-center">No result found</td></tr>
									    <?php
									}?>
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
<br>
<?php }else if(isset($_GET['type']) && $_GET['type'] == 'clients'){ ?>
    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
    	<div class="panel">
    		<div class="panel-heading">
    			<h4 class="pull-left">Clients visit</h4>
    			<span onclick="$('#todaysalesDashboard').html('')" class="text-danger pull-right"><i style="font-size:20px;cursor:pointer;" class="fa fa-times-circle-o" aria-hidden="true"></i></span>
				<div class="clearfix"></div>
    		</div> 
    		<div class="panel-body">
    			<div class="row">
    			    <div class="col-md-12">	
    				    <div class="table-responsive">
    					<table id="smstab" class="table tableprint1 table-bordered no-margin">
    						<thead>
    							<tr>
    								<th>Name</th>
    								<th>Contact Number</th>
    								<th>Gender</th>
    							</tr>
    						</thead>
    						<tbody>
    							<?php
    								$sql="SELECT DISTINCT(c.id), c.* from invoice_$branch_id i left join client c ON i.client = c.id where c.active=0 and i.doa='".$date."' order by c.id asc";
    								$result=query_by_id($sql,[],$conn);
    								foreach($result as $row) {
    								?>
    								<tr>										
    									<td><?php echo $row['name']; ?></td>
    									<td><?php echo $row['cont']; ?></td>
    									<td><?= $row['gender']==1?'Male':'Female'; ?></td>
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
<br>
<?php } ?>
<script>
    $('input[range-attr="daterange"]').daterangepicker({
    	opens: 'right'
    });
</script>
<?php
    function get_discount($date, $id, $type){
		global $branch_id;
	    $total = 0;   
        if($type == 'invoice'){
        	$sql="SELECT paid, dis, subtotal, total, id from `invoice_".$branch_id."` where doa='$date' and active=0 and branch_id='".$branch_id."' and id='".$id."'";
	        $sql2="SELECT coupons.*, invoice.coupon, invoice.subtotal FROM coupons LEFT JOIN invoice_".$branch_id." invoice ON coupons.id = invoice.coupon WHERE invoice.doa='$date' and invoice.active=0 and invoice.id='".$id."' and invoice.branch_id='".$branch_id."'";
			global $conn;
			$result=query_by_id($sql,[],$conn);
			$result2=query_by_id($sql2,[],$conn);
			if($result){
			    foreach($result as $res){
			        $items = query_by_id("SELECT actual_price, price, quantity FROM `invoice_items_".$branch_id."` WHERE iid='".$res['id']."'",[],$conn);
			        foreach($items as $item){
			            $total = $total+(($item['actual_price']*$item['quantity'])-$item['price']);
			        }
			        $discount = explode(",",$res['dis']);
			        if($discount['0'] == CURRENCY){
			            $total = $total + $discount['1'];
			        } else if($discount['0'] == 'pr'){
			            if($discount['1'] != 0){
	    		            $dis_price = ($res['subtotal']/100)*$discount['1'];
	    		            $total = $total+$dis_price;
			            }
			        }
			    }
			}
			
			if($result2){
    			 foreach($result2 as $res){
    		        if($res['discount_type'] == 0){
    		            if($res['coupon'] != 0){
    		                $dis_price = ($res['subtotal']/100)*$res['discount'];
        		            $total = $total+$dis_price;
    		            }
    		        } else {
    		            $total = $total + $res['discount'];
    		        }
		        }
			}
			return $total;
        } else if($type == 'appointment'){
        	$sql="SELECT paid, dis, subtotal, total, id from `app_invoice_".$branch_id."` where doa='$date' and active=0 and branch_id='".$branch_id."' and id='".$id."'";
			global $conn;
			$result=query_by_id($sql,[],$conn);
			if($result){
			    foreach($result as $res){
			        $items = query_by_id("SELECT disc_row, price FROM `app_invoice_items_".$branch_id."` WHERE iid='".$res['id']."'",[],$conn);
			        foreach($items as $item){
			        	$discount = explode(",",$item['disc_row']);
				        if($discount['0'] == CURRENCY){
				            $total = $total + $discount['1'];
				        } else if($discount['0'] == 'pr'){
				            if($discount['1'] != 0){
		    		            $dis_price = (($item['price']*100)/(100-$discount['1']))-$item['price'];
		    		            $total = $total+$dis_price;
				            }
				        }
			        }
			        
			        
			    }
			}
			return $total;
        }
	}
	
?>