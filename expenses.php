<?php
include "./includes/db_include.php";
$branch_id = $_SESSION['branch_id'];
$uid = $_SESSION['uid'];
if(isset($_GET['eid']) && $_GET['eid']>0){
    $eid = $_GET['eid'];
    $edit = query_by_id("SELECT e.*,ec.title as type,r.name as r_name from expense e left join expensecat ec on ec.id=e.cat left join recipient r on r.id=e.recipient where e.id=:eid and e.branch_id='".$branch_id."'",["eid"=>$eid],$conn)[0];
}
if(isset($_POST['submit']))
{
	$date 	= addslashes(trim($_POST['date']));
	$amount = addslashes(trim($_POST['amount']));
	//$ecat = $_POST['ecatt'];
	$descc 	= addslashes(trim($_POST['descc']));
	$mop	= addslashes(trim($_POST['mop']));
	$rec	= addslashes(ucfirst(htmlspecialchars($_POST['rec'])));
	$reci	= addslashes(htmlspecialchars($_POST['rcatt']));
    $scat	= addslashes(ucfirst(htmlspecialchars($_POST['ecat'])));
    $cat 	= addslashes(htmlspecialchars($_POST['ecatt']));
    
        $cat_res = query_by_id("SELECT id from expensecat where title=:cat and active='0' and branch_id='".$branch_id."'",["cat"=>$scat],$conn);
        if($cat_res){
            $cat = $cat_res[0]['id'];
        }else{
           $cat = get_insert_id("INSERT INTO expensecat set title=:title, active='0', branch_id='".$branch_id."'",["title"=>$scat],$conn); 
        }
    
	 
		$recipient=query_by_id("select id from recipient where name=:name and active='0' and branch_id='".$branch_id."'",["name"=>$rec],$conn);
		if($recipient)
		{
			$reci=$recipient[0]['id'];
		}
		else
		{
			$reci=get_insert_id("insert into recipient set name=:name,active='0', branch_id='".$branch_id."'",["name"=>$rec],$conn);
		}
	 
	query("INSERT INTO `expense`(`date`,`cat`,`amount`,`descc`,`user`,`active`,`mop`,`recipient`,`branch_id`) VALUES ('$date','$cat','$amount','$descc','$uid',0,'$mop','$reci','$branch_id')",[],$conn);
			
				$_SESSION['t'] 	   = 1;
				$_SESSION['tmsg']  = "Expense Added Successfully";
				echo '<meta http-equiv="refresh" content="0; url=expenses.php" />';
}


if(isset($_POST['edit-submit']))
{	
	$eid	= addslashes(trim($_GET['eid']));
	$date 	= addslashes(trim($_POST['date']));
	$amount = addslashes(trim($_POST['amount']));
	//$ecat 	= $_POST['ecatt'];
	$descc 	= addslashes(trim($_POST['descc']));
	$mop	= addslashes(trim($_POST['mop']));
	$rec	= addslashes(ucfirst(htmlspecialchars($_POST['rec'])));
	$reci	= addslashes(htmlspecialchars($_POST['rcatt']));
    $scat	= addslashes(ucfirst(htmlspecialchars($_POST['ecat'])));
    $ecat 	= addslashes(htmlspecialchars($_POST['ecatt']));
    
        $cat_res = query_by_id("SELECT id from expensecat where title=:cat and active='0' and branch_id='".$branch_id."'",["cat"=>$scat],$conn);
        if($cat_res){
            $cat = $cat_res[0]['id'];
        }else{
           $cat = get_insert_id("INSERT INTO expensecat set title=:title, active='0', branch_id='".$branch_id."'",["title"=>$scat],$conn); 
        }
		
		$recipient=query_by_id("select id from recipient where name=:name and active='0' and branch_id='".$branch_id."'",["name"=>$rec],$conn);
		if($recipient)
		{
			$reci=$recipient[0]['id'];
		}
		else
		{
			$reci=get_insert_id("insert into recipient set name=:name,active='0', branch_id='".$branch_id."'",["name"=>$rec],$conn);
		}
	 
   
	query("Update `expense` set `date`='$date',`cat`='$cat',`amount`='$amount',`descc`='$descc',`user`='$uid',`active`='0',`mop`='$mop',`recipient`='$reci' where id=:eid and branch_id='".$branch_id."'",['eid'=>$eid],$conn);
			
				$_SESSION['t']  = 1;
				$_SESSION['tmsg']  = "Expense Updated Successfully";
				header('location:expenses.php?eid='.$eid);
}


if(isset($_GET['did'])){
    if(DELETE_BUTTON_INACTIVE != 'true'){
    	$d = $_GET['did'];
    	query("update `expense` set active=1 where id=$d and branch_id='".$branch_id."'",[],$conn);
    	$_SESSION['t']  = 1;
    	$_SESSION['tmsg']  = "Expense Removed Successfully";
    	echo '<meta http-equiv="refresh" content="0; url=expenses.php" />';
    }
}

if(isset($_GET['del'])){
    if(DELETE_BUTTON_INACTIVE != 'true'){
    	$d = $_GET['del'];
    	query("update `expensecat` set active=1 where id=$d and branch_id='".$branch_id."'",[],$conn);
    	$_SESSION['t']  = 1;
    	$_SESSION['tmsg']  = "Expense Category Removed Successfully";
    	echo '<meta http-equiv="refresh" content="0; url=expenses.php" />';die();
    }
}

if(isset($_POST['addcat'])){
	$cat  = $_POST['cat'];
	$cat  = ucfirst($cat);
	query("INSERT INTO `expensecat`(`title`,`active`,`branch_id`) VALUES ('$cat',0,'$branch_id')",[],$conn);
	$_SESSION['t']  = 1;
	$_SESSION['tmsg']  = "Expense Category Added Successfully";
	echo '<meta http-equiv="refresh" content="0; url=expenses.php" />';
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
							<?php 	if($edit) { ?>
							<h4>Edit expense</h4>
							<?php } else { ?>
									<h4>Add new expense</h4>
							<?php } ?>
								</div>
								<div class="panel-body">
									<div class="row">
									<form action="" method="post">
										<div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
											<div class="form-group">
												<label for="userName">Date <span class="text-danger">*</span></label>
												<?php $date = date('Y-m-d'); ?>
												<input type="text" class="form-control date" value="<?= isset($eid)?$edit['date']:$date?>" name="date"required readonly>
											</div>
										</div>
										<div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
											<div class="form-group">
												<label for="userName">Type of expense <span class="text-danger">*</span></label>
												<input type="text" class="auto form-control" id="scat" onBlur="checkAvailability()" name="ecat" placeholder="Enter Category" value="<?= isset($eid)?$edit['type']:old('type')?>" required>
												<input type="hidden" name="ecatt" id="ecatt"></span>
												<span id="check-status">
												
											</div>
										</div>
									<script type="text/javascript">
									$(function() {
										autocomplete_ser();										
									});
									function autocomplete_ser(){
										$(".auto").autocomplete({
											source: "autocomplete/search.php",
											minLength: 1,
											select: function(event, ui) 
											{
												//event.preventDefault();
												$('#ecatt').val(ui.item.id); 
												$('#ecat').val(ui.item.cat); 
											}	
										});	
									}
									</script>
										
										<div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
											<div class="form-group">
												<label for="userName">Amount paid <span class="text-danger">*</span></label>
												<input type="number" step="0.01" class="form-control" name="amount" placeholder="Enter Amount paid" value="<?= isset($eid)?$edit['amount']:old('amount')?>" min="0" required>
											</div>
										</div>
										<div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
											<div class="form-group">
												<label for="userName">Mode of payment <span class="text-danger">*</span></label>
												<!--<input type="text" step="0.01" class="form-control" name="mop" value="<?= isset($eid)?$edit['mop']:old('mop')?>" placeholder="Mode of payment" required>-->
											 
											<select name="mop" id="staff_id_0"  class="form-control" required>
															<option value="">Select payment mode</option>
															<?php $sql_pay_mode="Select * FROM `payment_method` where status='1'";
																$result_pay_mode =query_by_id($sql_pay_mode,[],$conn);
																foreach($result_pay_mode as $row_pay_mode){
																if($row_pay_mode['id'] !='7'){
																?>
																<option value="<?=$row_pay_mode['id']?>" <?=($row_pay_mode['id'] == $edit['mop'])?'Selected':''?> ><?=$row_pay_mode['name']?></option> 
																<?php } } ?>  
														</select>
                                              
											</div>
										</div>
										<div class="col-lg-6 col-md-4 col-sm-4 col-xs-12">
											<div class="form-group">
												<label for="userName">Recipient name <span class="text-danger">*</span></label>
												<input type="text" step="0.01" class="rauto form-control" id="srec" name="rec" value="<?= isset($eid)?$edit['r_name']:old('r_name')?>" placeholder="Enter Recipient name" required>
												<input type="hidden" name="rcatt" id="rcatt">
											</div>
										</div>
										<script type="text/javascript">
									$(function() {
										autocomplete_ser1();										
									});
									function autocomplete_ser1(){
										$(".rauto").autocomplete({
											source: "autocomplete/recipient.php",
											minLength: 1,
											select: function(event, ui) 
											{
												//event.preventDefault();
												$('#rcatt').val(ui.item.id); 
												$('#rec').val(ui.item.name); 
											}	
										});	
									}
									</script>
										<div class="col-lg-6 col-md-4 col-sm-4 col-xs-12">
											<div class="form-group">
											<label for="userName">Description <span class="text-danger">*</span></label>
											<input type="text" class="form-control" value="<?= isset($eid)?$edit['descc']:old('descc')?>" name="descc" placeholder="Enter Description" required>
											</div>
										</div>
										
										<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
											<div class="form-group">
												<label for="userName">  </label>
												
											<?php	if (isset($eid))
											{ ?>
											<button type="submit" name="edit-submit" class="btn btn-info pull-right"><i class="fa fa-pencil-square-o" aria-hidden="true"></i>Update Expenses</button>	
											
											<?php } else { ?>
												<button type="submit" name="submit" class="btn btn-success pull-right"><i class="fa fa-plus" aria-hidden="true"></i>Add Expenses</button>
											
											<?php } ?>
											
											</div>
										</div>
									</form>
									</div>
									
									
								</div>
							</div>
						</div>
						
						<div class="clearfix"></div>
						
						<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
							<div class="panel">
								<div class="panel-heading heading-with-btn">
								    <h4 class="pull-left">Manage expense(s)</h4>
    								<span><div class="btn-group d-block custom-download-btn pull-right"><a class="btn buttons-excel buttons-html5 btn-warning pull-right download-btn mr-left-5" onclick="exportExpense()" title="Export to Excel"><span><i class="fa fa-file-excel-o" aria-hidden="true"></i> Export</span></a></div></span>		
									<div class="pull-right col-md-6">
									    <div class="row">
									        <div class="col-md-9">
									            <input type="text" class="form-control" range-attr="daterange" id="daterange" name="date"  placeholder="01/01/1990 - 12/05/2000" required readonly>
									        </div>
									        <div class="col-md-3" style="padding:0px;">
									            <button type="button" id="filter" onclick="expensesFilter();" name="filter" class="btn btn-filter btn-block"><i class="fa fa-filter" aria-hidden="true"></i>Filter</button>
									        </div>
									    </div>
									    
									</div>
									<div class="clearfix"></div>
								</div>
								<div class="panel-body">
									<div class="row">
									 
									</div>
									
									<div class="clearfix"></div>
									
									<div class="row">
									
									<div class="col-lg-12">
										<div class="table-responsive">
										
											<table class="table table-bordered no-margin grid" id="">
											<thead>
												<tr>
													<th>Date</th>
													<th>Type</th>
													<th>Amount</th>
													<th>Payment mode</th>
													<th>Recipient</th>
													<th>Paid by</th>
													<th width="180">Action</th>
												</tr>
											<thead>
											<tbody id="filterData">
											<?php 
											$sql="select pm.name as payment_method, ec.title as type,e.*,r.name as r_name,u.name as uname from expense e left JOIN expensecat ec on ec.id=e.cat left join recipient r on r.id=e.recipient left join user u on u.id=e.user LEFT JOIN payment_method pm ON pm.id = e.mop where e.active=0 AND e.date = '".date('Y-m-d')."' and e.branch_id='".$branch_id."' order by date desc";
											$result	=query_by_id($sql,[],$conn);
										    $total_amount = 0;
											if($result){
    											foreach($result as $row)
    											{
    												?>
    												<tr>
    													<td><?= my_date_format($row['date'])?></td>
    													<td><?= $row['type'];?></td>
    													<td><?= number_format($row['amount'],2);?></td>
    													<td><?= $row['payment_method'] ?></td>
    													<td><?= $row['r_name'];?></td>
    													<td><?= $row['uname'];?></td>
    													<td><a href="expenses.php?eid=<?=$row['id']?>" class="btn btn-warning btn-xs"><i class="icon-edit"></i>Edit</a> 
    													<?php if(DELETE_BUTTON_INACTIVE == 'true'){ ?>
    													    <a href="#" onclick="return deleteDisabled();" class="btn btn-danger btn-xs"><i class="icon-delete"></i>Delete</a> 
    													<?php } else { ?>
    													    <a href="expenses.php?did=<?=$row['id']?>" onclick="return confirm('Are you sure?');" class="btn btn-danger btn-xs"><i class="icon-delete"></i>Delete</a> 
    													<?php } ?>
    													</td>
    												</tr>
    											<?php 
    											    $total_amount = $total_amount+$row['amount'];
    											} ?>
        											<tr>
                                        		        <td colspan="2" class="text-right"><strong>Total Amount </strong></td>
                                        		        <td><strong><?= number_format($total_amount,2); ?></strong></td>
                                        		        <td colspan="4"></td>
                                        		    </tr>
    											<?php
											} else {
											    
											}
											?>
											</tbody>
												
											</table>
											<p id="demo"></p>	
										
										
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
		<!-- Container fluid ends -->
<script>

$(document).ready(function(){
		var table =	$('#manage_expenses').DataTable();

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

    
});

function expensesFilter(){
	var daterange = $('#daterange').val();
	var date = daterange.split("-");
	if(daterange == ''){
		var startdate = '';
		var enddate = '';
	} else {
		var startdate = isoDate(date[0]);
		var enddate = isoDate(date[1]);
	}
	expensesAjax(startdate, enddate);
}


function exportExpense(){
	var daterange = $('#daterange').val();
	var date = daterange.split("-");
	if(daterange == ''){
		var startdate = '';
		var enddate = '';
	} else {
		var startdate = isoDate(date[0]);
		var enddate = isoDate(date[1]);
	}
	window.location.href="export-expenses.php?filter_type=export&startdate="+startdate+'&enddate='+enddate;
}

function expensesAjax(startdate, enddate){
	jQuery.ajax({
		url: "ajax/expenses_filter.php",
		data: {'startdate':startdate, 'enddate': enddate, 'filter_type':'expenses'},
		type: "POST",
		beforeSend: function() {
        	$('#filter i').removeClass('fa-filter');
        	$('#filter i').addClass('fa-spinner fa-spin');
        	$('#filter').prop('disabled',true);
		},
		success:function(data){
			$('#filter i').addClass('fa-filter');
        	$('#filter i').removeClass('fa-spinner fa-spin');
        	$('#filter').prop('disabled',false);
			$('#filterData').html(data);
		},
		error:function (){}
	});
}

// jquery function to change daterange date in iso date standard  Y-m-d (2019-11-01)

function isoDate(date){	
	var datespit = date.split('/');
	var day = datespit[1].replace(' ','');
	var month = datespit[0].replace(' ','');
	var year = datespit[2].replace(' ','');
	return year+'-'+month+'-'+day;
}


 
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

</script>
<script>
$(document).ready(function(){
$("#scat").blur(function(){
var dat = $('#scat').val();

$.ajax({
url: "autocomplete/search.php?term="+dat,
type: "POST",
success:function(data)
{
	var ds = JSON.parse(data.trim());
 
$('#scatt').val(ds[0]['id']); 
$('#scat').val(ds[0]['cat']); 
 
},
error:function (){}
});
		
});
});
</script>
 <script>
$(document).ready(function(){
$("#srec").blur(function(){
var dat = $('#srec').val();

$.ajax({
url: "autocomplete/recipient.php?term="+dat,
type: "POST",
success:function(data)
{
var d = JSON.parse(data.trim());
$('#ecatt').val(d[0]['id']); 
$('#srec').val(d[0]['value']); 
 

},
error:function (){}
});
		
});
});
</script>

<script>
function checkcat() {
	var cat = $('#cat').val();
jQuery.ajax({
url: "autocomplete/checkcats.php?cat="+$("#cat").val(),
data:'cat='+$("#scat").val(),
type: "POST",
success:function(data){
$("#cat-status").html(data);
if(data === "Already Exist"){
	$('#cat').val("");
}
},
error:function (){}
});
}
</script>

<?php  include "footer.php"; ?>
