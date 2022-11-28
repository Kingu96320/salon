<?php
include_once './includes/db_include.php';
$branch_id = $_SESSION['branch_id'];
$hidden_payment_method = '2,7,8,9';
if(isset($_GET['vid'])){
	$vid = $_GET['vid'];
	$sql="SELECT * from vendor where id=$vid and branch_id='".$branch_id."'";
	$row=query_by_id($sql,[],$conn)[0];
}
if(isset($_POST['edit-submit'])){
	$client = trim($_POST['name']);
	$cont = trim($_POST['cont']);
	$details = trim($_POST['details']);
	$email = trim($_POST['email']);
	$addr = trim($_POST['addr']);
	$gst  = trim($_POST['gst']);
	query("UPDATE `vendor` SET `name`='$client',`cont`='$cont',`email`='$email',`address`='$addr',`details`='$details',`gst`='$gst' WHERE id=$vid and branch_id='".$branch_id."'",[],$conn);
	$_SESSION['t']  = 1;
	$_SESSION['tmsg']  = "Profile Updated Successfully";
	echo '<meta http-equiv="refresh" content="0; url=vendorprofile.php?vid='.$vid.'" />';
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
        textarea{
            resize:none;
        }
    }
    .no-border td{
        border-top:0px!important;
    }
</style>
		
		<!-- Dashboard wrapper starts -->
		<div class="dashboard-wrapper">
			<!-- Main container starts -->
			<div class="main-container">
				<!-- Row starts -->
				<div class="row gutter">
					<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
					    <form action="" method="POST">
						    <div class="panel">
							<div class="panel-heading">
								<h4>Vendor profile</h4>
							</div>
							<div class="row">
							    <div class="panel-body">
								<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 w-20">
									<div class="form-group">
										<label for="name">Vendor name</label>
										<input type="text" class="form-control" name="name" placeholder="Vendor Name" value="<?=$row['name']?>" required />
									</div>
								</div>
								<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 w-20">
									<div class="form-group">
										<label for="cont">Contact</label>
										<input type="text" maxlength="<?= PHONE_NUMBER ?>" class="form-control" onBlur="check()" name="cont" id="cont" placeholder="Contact" value="<?=$row['cont']?>" required>
										<span id="client-status"></span>
									</div>
								</div>

								<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 w-20">
									<div class="form-group">
										<label for="email">Email</label>
										<input type="email" class="form-control" name="email" value="<?=$row['email'];?>" id="email" placeholder="Email">
									</div>
								</div>
								<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 w-20">
									<div class="form-group">
										<label for="addr">Address</label>
										<input type="text" class="form-control" value="<?=$row['address'];?>" name="addr" id="address" placeholder="Address">
									</div>
								</div>
								<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 w-20">
									<div class="form-group">
										<label for="gst">GST Number</label>
										<input type="text" class="form-control" value="<?=$row['gst'];?>" name="gst" id="address" placeholder="Address">
									</div>
								</div>
								<div class="clearfix"></div>
								<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
									<div class="form-group">
										<label for="details">Company details</label>
										<textarea name="details" rows="4" class="form-control"><?=$row['details']?></textarea>
									</div>
								</div>
								<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
									<div class="form-group text-right">
										<button type="submit" name="edit-submit" class="btn btn-info" ><i class="fa fa-pencil-square-o" aria-hidden="true"></i>Update profile</button>
								    </div>
								</div>
							</div>
							</div>
						</div>
					    </form>
					</div>
                    <!-- Purchase history table -->
					<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
						<div class="panel">
							<div class="panel-heading">
								<h4>Purchase history</h4>
							</div>
							<div class="panel-body">
								<div class="table-responsive ">
									<table id="grid" class="table table-bordered no-margin grid">
										<thead>
											<tr>
												<th>Date</th>
												<th>Invoice</th>
												<th>Amount payable</th>
												<th>Amount paid</th>
												<th>Payment mode</th>
												<th>Pending amount</th>
												<th>Action</th>
											</tr>
										</thead>
										<tbody>
											<?php 
												
												$sql1="SELECT p.*,pm.name as paymode from `purchase` p LEFT JOIN payment_method pm on pm.id=p.pay_method  where p.vendor='$vid' and p.branch_id='".$branch_id."' order by p.id desc";
												$result1=query_by_id($sql1,[],$conn);
												foreach($result1 as $row1) 
												{
													$sql2="SELECT sum(debit) as debit,sum(payment) as payment from payments where purchase_id='".$row1['id']."'";
													$result2=query_by_id($sql2,[],$conn);
													foreach($result2 as $row2)
													{
														
													?>
													<tr>
														<td><?= my_date_format($row1['dop']);?></td>
														<td><?= $row1['inv'];?></td>
														<td><?= number_format($row1['total'],2);?></td>
														<td><?= number_format($row1['paid'],2); ?></td>
														<td><?= $row1['paymode']; ?></td>
														<td>
														    <?php
														        $pay_history = query("SELECT SUM(paid) as paid_amount FROM payments WHERE bill_id='".$row1['inv']."' AND purchase_id='".$row1['id']."' AND active = 0 and branch_id='".$branch_id."'",[],$conn)[0]['paid_amount'];
														        if($pay_history > 0){
														            $p_amount = $row1['due']-$pay_history;
														            echo number_format($p_amount,2);
														        } else {
														            $p_amount = $row1['due'];
														            echo number_format($p_amount,2);
														        }
														    ?>
														</td>
														<td><div class="form-group"> 
														    <a href="addpurchase.php?pid=<?= $row1['id'] ?>">
														        <button class="btn btn-info btn-xs" type="button">
														            <?php if($p_amount > 0) { ?>
														                <i class="fa fa-pencil" aria-hidden="true"></i>Edit
														            <?php } else { ?>
														                <i class="fa fa-eye" aria-hidden="true"></i>View
														            <?php } ?>
														        </button>
														    </a>
    														<?php if($p_amount > 0){ ?>
															    <button type="button "  data-id="<?=$row1['id'];?>" data-b_id="<?=$row1['inv'];?>" data-amount="<?=$p_amount;?>" onclick="paypendingamount(this);" class="btn btn-warning btn-xs feed_id"><i class="fa fa-check" aria-hidden="true"></i>Apply Payment</button>
														    <?php  }  ?>
														</div></td>
													</tr>
													<?php } }  ?>
											</td>
										</tbody>
									</table>
									<script>
										function paypendingamount(e)
										{
											var id  = $(e).data('id');
											var total = $(e).data('amount');
											var inv_id = $(e).data('b_id');
											$('#pid').val(id);
											$('#tobepaid').val(total);
											$('#myModal').modal('show');
											$('#b_id').val(inv_id);
											$('#pend').val(total);
										}
									</script>
								</div>
							</div>
						</div>
					</div>
						
					<!-- Payment history table -->
					<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
							<div class="panel">
								<div class="panel-heading">
									<h4>Payment history</h4>
								</div>
								<div class="panel-body">
									<div class="table-responsive">
										<table id="grid" class="table table-bordered no-margin grid">
											<thead>
												<tr>
													<th>Date</th>
													<th>Invoice</th>
													<th>Amount paid</th>
													<th>Payment mode</th>
													<th>Notes</th>
												</tr>
											</thead>
											<tbody>
												<?php 
													$sql1="SELECT p.*,pm.name as mode FROM `payments` p LEFT JOIN payment_method pm on pm.id=p.mode  where p.vendor='$vid' and p.credit=0 and p.active=0 and p.branch_id='".$branch_id."' order by p.id desc";
													$result1=query_by_id($sql1,[],$conn);
													foreach($result1 as $row1) 
													{
													?>
													<tr>
														<td><?php echo my_date_format($row1['date']); ?> </td>
														<td><?php echo $row1['bill_id']; ?></td>
														<td><?php echo $row1['paid']; ?></td>
														<td><?php echo $row1['mode']; ?></td>
														<td><?php echo $row1['notes']; ?></td>
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
						
					<!-- Row ends -->

					<!-- Modal -->
					
					
					<div class="modal fade" id="myModal" role="dialog">
						<div class="modal-dialog">
							
							<!-- Modal content-->
							<div class="modal-content">
								<div class="modal-header">
									<button type="button"  class="close" data-dismiss="modal">&times;</button>
									<h4 class="modal-title">Apply payment</h4>
								</div>
								<div class="modal-body">
									<form id="">
									    <table class="table no-margin no-border">
									        <tr>
									            <td>
									                <label for="topaid">Amount to be paid</label>
									            </td>
									            <td>
									                <input type="hidden" name="bill_id" id="b_id" value="">
										            <input type="number" value="0.00" class="form-control" id="tobepaid" readonly min="0">
										            <input type="hidden" value="0.00" class="form-control" id="pid" readonly>
									            </td>
									        </tr>
									        <tr>
									            <td>
									                <label for="payment">Payment</label>
									            </td>
									            <td>
									                <input type="number" class="form-control" value="0.00" onkeyup="checkpay();" id="amount" min="0">
									            </td>
									        </tr>
									        <tr>
									            <td>
									                <label for="pending">Pending</label>
									            </td>
									            <td>
									                <input type="number" class="form-control" value="0.00" id="pend" readonly min="0">
									            </td>
									        </tr>
									        <tr>
									            <td>
									                <label for="pay_mode">Payment mode</label>
									            </td>
									            <td>
									                <select name="paymehod" id="mode" class="form-control act">
            											<?php
            												$sql = "select * from payment_method where status='1' and id not in ($hidden_payment_method) and branch_id='".$branch_id."' order by id asc";
            												$result=query_by_id($sql,[],$conn);
            												foreach($result as $row)
            												{ ?>
            												<option value="<?=$row[id]?>"><?=$row['name']?></option>
            											<?php } ?>   
            										</select>
									            </td>
									        </tr>
									        <tr>
									            <td>
									                <label for="notes">Notes any</label>
									            </td>
									            <td>
									                <textarea rows="4" name="notes" id="notes" class="form-control" ></textarea>
									            </td>
									        </tr>
									        <tr>
									            <td colspan="2">
									                <button type="submit" style="float : right;" onclick="acceptpayment()" data-dismiss="modal" class="btn btn-success"><i class="fa fa-check" aria-hidden="true"></i>Accept payment</button> 
									            </td>
									        </tr>
									    </table>
									</form>
								</div>
							</div>
						</div>
					</div>
					<!-- Modal End -->
					
				</div>
				<!-- Main container ends -->
			</div>
			<!-- Dashboard Wrapper End -->
		</div>
		<!-- Container fluid ends -->
		
		<script>
			function acceptpayment(){
				var payment = $('#tobepaid').val();
				var amnt 	= $('#amount').val();
				var mode 	= $('#mode').val();
				var pend 	= $('#pend').val();
				var notes 	= $('#notes').val();
				var client 	= <?php echo $vid; ?>;
				var purchase_id	= $('#pid').val();
				var bill_id		= $('#b_id').val();	
				
				//var purchase_id=$('#id').val();
				if(payment==""||amnt==""||mode==""){
					alert("Please Fill all Fields");
					}else{
					var dataString = 'pay='+ payment + '&amnt='+ amnt + '&mode='+ mode + '&pend='+ pend+'&notes='+notes+'&client='+client+'&purchase_id='+purchase_id + '&bill_id='+bill_id ;
					$.ajax({
						url: "vendorpayments.php",
						type: "POST",
						data: dataString,
						success:function(data){
							alert(data);
							location.reload();
							$('#tobepaid').val(pend);
							$('#cred').val(pend);
							$('#amount').val('');
							$('#mode').val('');
							$('#pend').val('');
							$('#notes').val('');
						},
						error:function (){}
					});
					
					
				}
			}
			
			function checkpay(){
				var payment = $('#tobepaid').val();
				var amnt = $('#amount').val();
				var pend = $('#pend').val();
				var sum = parseFloat(payment)-parseFloat(amnt);
				$('#pend').val(sum);
			}
		</script>
		
<?php
	include "footer.php";
	
	function firstvisit($uid){
		global $conn;
		global $branch_id;
		$sql="SELECT * from purchase where vendor=$uid and active=0 and branch_id='".$branch_id."' order by id asc limit 1";
		$result=query_by_id($sql,[],$conn);
		if ($result)
		{	
			foreach($result as $row)
			{	
				return $row['doa'];
			}
			}else{
			return "NA";
		}
	}
	
	function package($uid){
		global $conn;
		global $branch_id;
		$sql="SELECT * FROM `packages` where id=$uid and branch_id='".$branch_id."'";
		$result=query_by_id($sql,[],$conn);
		
		if ($result)
		{
			foreach($result as $row)
			{
				$name=$row['name'];
			}
			return $name; 
			}else{
			return "NA";
		}
	}
	
	function packageprice($uid){
		global $conn;
		global $branch_id;
		$sql="SELECT * FROM `packages` where id=$uid and branch_id='".$branch_id."'";
		$result=query_by_id($sql,[],$conn);
		if ($result)
		{
			foreach($result as $row)
			{
				$price=$row['price'];
			}
			return $price;
			}else{
			return "NA";
		}
	}
	
	function getdat($uid){
		global $conn;
		global $branch_id;
		$sql="SELECT * from invoice_".$branch_id." where id=$uid and branch_id='".$branch_id."'";
		$result=query_by_id($sql,[],$conn);
		if ($result)
		{	
			foreach($result as $row)
			{
				$doa=$row['doa']." ".$row['itime'];
			}
			return $doa;
			}else{
			return "NA";
		}
	}
	
	function getpayments($vid){
		global $conn;
		global $branch_id;
		$sql="SELECT sum(debit) as debit FROM `payments` WHERE vendor=$vid and active=0 and branch_id='".$branch_id."'";
		$result=query_by_id($sql,[],$conn);
		if($result)
		{
			foreach($result as $row)
			{
				$debit=$row['debit'];
			}
			return $debit;
			}else{
			return "0";
		}
	}
	
	function getcredit($vid){
		global $conn;
		global $branch_id;
		$sql="SELECT sum(total) as total from purchase where active=0 and vendor=$vid and branch_id='".$branch_id."'";
		$result=query_by_id($sql,[],$conn);
		if($result) 
		{
			foreach($result as $row)
			{
				$pays = getpayments($vid);
				$sum = $row['total'] - $pays;
				
			}
			return $sum;
			}else{
			return "0";
		}
	}
	function getindividual($vid)
	{
		global $conn;
		global $branch_id;
		$sql="SELECT total as total from purchase where active=0 and vendor=$vid and id=$id and branch_id='".$branch_id."'";
		$result=query_by_id($sql,[],$conn);
		if($result) 
		{
			foreach($result as $row)
			{
				
				$sum = $row['total']; 
				
			}
			return $sum;
			}else{
			return "0";
		}
	}
	
?>