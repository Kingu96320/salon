<?php 
	include_once './includes/db_include.php';
	$branch_id = $_SESSION['branch_id'];
	$hidden_payment_method = '2,7,8,9';
	$dr = 0;
	if($_GET['dr']){
		$dr = $_GET['dr'];
	}
	$date = date('Y-m-d');
	
	if(isset($_GET['sdate'])){
	    $start_date = $_GET['sdate'];
	    $start_date = explode("-",$start_date);
	    $start_date = $start_date['1'].'/'.$start_date['2'].'/'.$start_date['0'];
	} else {
	    $start_date = date('m/d/Y');
	}
	
	if(isset($_GET['edate'])){
	    $end_date = $_GET['edate'];
	    $end_date = explode("-",$end_date);
	    $end_date = $end_date['1'].'/'.$end_date['2'].'/'.$end_date['0'];
	} else {
	    $end_date = date('m/d/Y');
	}
	
	include "topbar.php";
	include "header.php";
	include "menu.php";
	include "reportMenu.php";

	// ini_set('display_errors', 1);
	// ini_set('display_startup_errors', 1);
	// error_reporting(E_ALL);
?>
<!-- Dashboard wrapper starts -->
<div class="dashboard-wrapper">
	<!-- Main container starts -->
	<div class="main-container">
		<!-- Row starts -->
		<div class="row gutter">
			<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
				<div class="panel">
					<div class="panel-heading heading-with-btn">
						<h4 class="pull-left">Expense report</h4>
						<?php
							if(isset($_GET['sdate'])){
				                $from = $_GET['sdate'];
				            } else {
								$from = date('Y-m-d');
				            }
				            if(isset($_GET['edate'])){
				                $to = $_GET['edate'];
				            } else {
				            	$to = date('Y-m-d');
				            }
				            if(isset($_GET['exp_type'])){
				            	$exp_type = $_GET['exp_type'];
				            } else {
				            	$exp_type = '';
				            }
				            if(isset($_GET['p_type'])){
				            	$p_type = $_GET['p_type'];
				            } else {
				            	$p_type = '';
				            }
				        ?>
					    <a href="exportdata/expense-report.php?sdate=<?= $from ?>&edate=<?= $to ?>&exp_type=<?= $exp_type ?>&p_type=<?= $p_type ?>" target="_blank">
						    <button type="button" class="btn btn-warning pull-right">
						        <i class="fa fa-file-excel-o" aria-hidden="true"></i>Export
						    </button>
						</a>
						<span id="download-btn"></span>					
						<div class="clearfix"></div>
					</div>
					<div class="panel-body">
						<div class="row">
						    <div class="col-md-12">
						        <div class="row">
    						        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
    									<div class="form-group">
    										<label for="date">Select dates</label>
    										<input type="text" class="form-control" range-attr="daterange" id="daterange" name="date" value="<?= $start_date.' - '.$end_date ?>"  placeholder="01/01/1990 - 12/05/2000" required readonly>		
    									</div>
    								</div>
    								<div class="col-lg-2 col-md-2 col-sm-2 col-xs-12">
    									<div class="form-group">
    										<label for="date">Expense type</label>
    										<select class="form-control" id="exp_type">
    											<option value="">--Select--</option>
    											<option <?= isset($_GET['exp_type'])&&$_GET['exp_type']=='Stock purchase'?'selected':'' ?> value="Stock purchase">Stock purchase</option>
    											<option <?= isset($_GET['exp_type'])&&$_GET['exp_type']=='Stock purchase (pending payment)'?'selected':'' ?> value="Stock purchase (pending payment)">Stock purchase (pending payment)</option>
    											<?php 
    												$type = query_by_id("SELECT * FROM expensecat WHERE active='0' AND branch_id='".$branch_id."'",[],$conn);
    												if($type){
    													foreach($type as $data){ ?>
    														<option <?= isset($_GET['exp_type'])&&$_GET['exp_type']==$data['id']?'selected':'' ?> value="<?= $data['id'] ?>"><?= $data['title'] ?></option>
    												<?php }
    												}
    											?>
    										</select>
    									</div>
    								</div>
    								<div class="col-lg-2 col-md-2 col-sm-2 col-xs-12">
    									<div class="form-group">
    										<label for="date">Payment method</label>
    										<select class="form-control" id="p_type">
    											<option value="">--Select--</option>
    											<?php
    											$sql = "select * from payment_method where status='1' and id not in ($hidden_payment_method) order by id asc";
												$result = query_by_id($sql,[],$conn);
												foreach($result as $row){
												?>
												<option <?= isset($_GET['p_type'])&&$_GET['p_type']==$row['id']?'selected':'' ?> value="<?=$row['id']?>"><?=$row['name']?></option>
												<?php } ?>
    										</select>
    									</div>
    								</div>
    								<div class="col-md-3 col-md-3 col-sm-3 col-xs-12">
    								    <lable>&nbsp;</lable>
    								    <div class="form-group">
    								        <button class="btn btn-filter btn-sm" onclick="filterExpensereport()"><i class="fa fa-filter" aria-hidden="true"></i>Filter</button>&nbsp;&nbsp;&nbsp;&nbsp;<button class="btn btn-danger btn-sm" onclick="window.location.href='expense-report.php'"><i class="fa fa-times" aria-hidden="true"></i>Clear</button>
    								    </div>
    								</div>
    							</div>
						    </div>
						    <div class="col-md-12">
						    	<div class="table-responsive">
							    	<table class="table table-stripped table-bordered">
							    		<thead>
							    			<tr>
							    				<th>Date</th>
							    				<th>Id</th>
							    				<th>Type of expense</th>
							    				<th>Amount</th>
							    				<th>Payment mode</th>
							    				<th>Recipient name</th>
							    				<th>Paid by</th>
							    				<th>Notes</th>
							    			</tr>
							    		</thead>
							    		<tbody>
									    <?php
									    	$sdate = strtotime(date('Y-m-d'));
								            $edate = strtotime(date('Y-m-d'));
								            if(isset($_GET['sdate'])){
								                $sdate = $_GET['sdate'];
								            } else {
								            	$sdate = date('Y-m-d');
								            }
								            if(isset($_GET['edate'])){
								                $edate = $_GET['edate'];
								            } else {
								            	$edate = date('Y-m-d');
								            }

								            if(isset($_GET['exp_type']) && $_GET['exp_type'] != ''){ 
								            	$exp1 = " AND e.cat = '".$_GET['exp_type']."' ";
								            	$exp2 = " AND 'Stock purchase' = '".$_GET['exp_type']."' ";
								            	$exp3 = " AND 'Stock purchase (pending payment)'='".$_GET['exp_type']."' ";
								           	} else { 
								           		$exp1 = "";
								            	$exp2 = "";
								            	$exp3 = "";
								           	}
								            if(isset($_GET['p_type']) && $_GET['p_type'] != ''){ 
								            	$pay1 = " AND e.mop='".$_GET['p_type']."' ";
								            	$pay2 = " AND pay_method='".$_GET['p_type']."' ";
								            	$pay3 = " AND p.mode='".$_GET['p_type']."' ";
								            } else { 
								            	$pay1 = "";
								            	$pay2 = "";
								            	$pay3 = "";
								            }

								            $sql = "SELECT e.id, e.cat, e.amount, e.descc as notes, e.mop as payment_method, e.recipient, e.user, 'Expense' as type, e.date as repdate FROM expense e WHERE e.active = '0' AND e.branch_id='".$branch_id."' AND e.date BETWEEN '".$sdate."' AND '".$edate."'".$exp1.$pay1
								            	. " UNION SELECT id, 0 as cat, paid as amount, notes, pay_method as payment_method, vendor as recipient, 0 as user, 'Stock purchase' as type, dop as repdate FROM purchase WHERE active='0' AND branch_id='".$branch_id."' AND dop BETWEEN '".$sdate."' AND '".$edate."'".$exp2.$pay2
								            	." UNION SELECT p.purchase_id as id, 0 as cat, p.paid as amount, p.notes, p.mode as payment_method, p.vendor as recipient, 0 as user, 'Stock purchase (pending payment)' as type, p.date as repdate FROM payments p WHERE p.active='0' AND p.branch_id='".$branch_id."' AND p.date BETWEEN '".$sdate."' AND '".$edate."' $exp3 $pay3 ORDER BY repdate DESC ";
								            $data = query_by_id($sql,[],$conn);
								            if($data){
								            	$total_expense = 0;
								            	foreach($data as $res){ ?>
								            		<tr>
								            			<td><?php echo my_date_format($res['repdate']); ?></td>
								            			<td><?php echo $res['id'] ?></td>
								            			<td><?php echo expense_type($res['cat'], $res['type']); ?></td>
								            			<td><?php echo number_format($res['amount'], 2) ?></td>
								            			<td><?php echo pay_method_name($res['payment_method']) ?></td>
								            			<td><?php echo recipient_name($res['recipient'], $res['type']) ?></td>
								            			<td><?php echo paid_by($res['user']) ?></td>
								            			<td><?php echo $res['notes'] ?></td>
								            		</tr>
								            	<?php	
								            		$total_expense += $res['amount'];
								        		}
								        		echo '<tr><td colspan="3" align="right"><b>Total</b></td><td><b>'.number_format($total_expense, 2).'</b></td><td colspan="4"></td></tr>';
								            } else {
								            	echo '<tr><td colspan="8" align="center"><b>No data found!</b></td></tr>';
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
	</div>
</div>
</div>
</div>
<?php 
	include "footer.php";
	function expense_type($id, $stype){
		global $conn;
		if($id != 0){
			$type = query_by_id("SELECT title FROM expensecat WHERE id='".$id."' AND active='0'",[],$conn)[0]['title'];
			return $type;
		} else {
			return $stype;
		}
	}

	function recipient_name($id, $stype){
		global $conn;
		if($stype == 'Expense'){
			$name = query_by_id("SELECT name FROM recipient WHERE id='".$id."'",[],$conn)[0]['name'];
			return $name;
		} else {
			$vendor = query_by_id("SELECT name, cont FROM vendor WHERE id='".$id."'",[],$conn)[0];
			return $vendor['name'].' ('.$vendor['cont'].')';
		}
	}

	function paid_by($id){
		global $conn;
		if($id != 0){
			$name = query_by_id("SELECT name FROM user WHERE id='".$id."'",[],$conn)[0]['name'];
			return $name;
		} else {
			$name = '-';
			return $name;
		}
	}
?>
<script>
	function filterExpensereport(){
		var daterange = $('#daterange').val();
		var date = daterange.split("-");
		var exp_type = $('#exp_type').val();
		var p_type = $('#p_type').val();
		if(daterange == ''){
			var from = '';
			var to = '';
		} else {
			var from = isoDate(date[0]);
			var to = isoDate(date[1]);
		}
		window.location.href = '?sdate='+from+'&edate='+to+'&exp_type='+exp_type+'&p_type='+p_type;
	}
	
	// jquery function to change daterange date in iso date standard  Y-m-d (2019-11-01)

	function isoDate(date){
		var datespit = date.split('/');
		var day = datespit[1].replace(' ','');
		var month = datespit[0].replace(' ','');
		var year = datespit[2].replace(' ','');
		return year+'-'+month+'-'+day;
	}
	
</script>