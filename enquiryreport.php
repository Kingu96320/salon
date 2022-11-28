<?php 
include_once './includes/db_include.php';
$branch_id = $_SESSION['branch_id'];
//$uid = $_SESSION['uid'];
$submit = "";
$date = "";
$type = "";
$lead = "";
$status = "";
$user = "";
$etype =  "";
$leadsource = "";
$leadstatus = "";
$leadr = "";

$start_date = date('m/d/Y');
$end_date = date('m/d/Y');

if(isset($_POST['reset'])){
	echo '<meta http-equiv="refresh" content="0; url=enquiryreport.php" />';die();
}

if(isset($_POST['submit'])){
	
	if($_POST['date']!=""){
	    $date = $_POST['date'];
	    $date = explode("-",$date);
	    $start_date = $date[0];
	    $end_date = $date[1];
	    $sdate = isoDate($date[0]);
	    $edate = isoDate($date[1]);
		$dqry = "and regon BETWEEN '$sdate' AND '$edate'";
	}
	
	if($_POST['enquirytype']){
		$etype =$_POST['enquirytype'];
		$type = "and type='".$_POST['enquirytype']."' ";
	}
	if($_POST['leadsource']){
		$leadsource = $_POST['leadsource'];
		$lead = "and leadsource='".$_POST['leadsource']."' ";
	}
	if($_POST['leadstatus']){
		$leadstatus = $_POST['leadstatus'];
		$status = "and leadstatus='".$_POST['leadstatus']."' ";//leaduser
	}
	if($_POST['leaduser']){
		$leadr = $_POST['leaduser'];
		$user = "and leaduser='".$_POST['leaduser']."' ";
	}
	
	$submit = $type.$lead.$status.$user.$dqry;
} else {
    $dqry = "and regon = '".date('Y-m-d')."'";
    $submit = $dqry;
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
								<div class="panel-heading heading-with-btn">
									<h4 class="pull-left">Enquiry Reports</h4>
                					<span id="download-btn"></span>					
                					<div class="clearfix"></div>
								</div>
								<div class="panel-body">
					<div class="row">
					<form action="" method="post">
					<div class="col-lg-4 col-md-3 col-sm-3 col-xs-4">
						<div class="form-group">
							<label class=" control-label">Select date</label>
							<input type="text" class="form-control" range-attr="daterange" id="daterange" name="date" value="<?= $start_date.' - '.$end_date ?>"  placeholder="01/01/1990 - 12/05/2000" required readonly>
						</div>
					</div>
					<div class="col-lg-2 col-md-3 col-sm-3 col-xs-4">
						<div class="form-group">
							<label class="control-label">Enquiry type</label>
							<select class="form-control" name="enquirytype">
								<option value="">-- Select a type --</option>
								<option value="Hot" <?php if($etype=="Hot") echo "selected"; ?>>Hot</option>
								<option value="Cold" <?php if($etype=="Cold") echo "selected"; ?>>Cold</option>
								<option value="Warm" <?php if($etype=="Warm") echo "selected"; ?>>Warm</option>				
							</select>
						</div>
					</div>
					<div class="col-lg-2 col-md-3 col-sm-3 col-xs-4">
						<div class="form-group">
							<label class=" control-label">Source of lead</label>
								<select class="form-control" name="leadsource" >
									<option value="" <?php if($leadsource=="") echo "selected"; ?>>-- Select A Type --</option>
									<option value="Client refrence" <?php if($leadsource=="Client refrence") echo "selected"; ?>>Client refrence</option>
									<option value="Cold Calling" <?php if($leadsource=="Cold Calling") echo "selected"; ?>>Cold Calling</option>
									<option value="Facebook" <?php if($leadsource=="Facebook") echo "selected"; ?>>Facebook</option>
									<option value="Twitter" <?php if($leadsource=="Twitter") echo "selected"; ?>>Twitter</option>
									<option value="Instagram" <?php if($leadsource=="Instagram") echo "selected"; ?>>Instagram</option>
									<option value="Other Social Media" <?php if($leadsource=="Other Social Media") echo "selected"; ?>>Other Social Media</option>
									<option value="Website" <?php if($leadsource=="Website") echo "selected"; ?>>Website</option>
									<option value="Walk-In" <?php if($leadsource=="Walk-In") echo "selected"; ?>>Walk-In</option>
									<option value="Flex" <?php if($leadsource=="Flex") echo "selected"; ?>>Flex</option>
									<option value="Flyer" <?php if($leadsource=="Flyer") echo "selected"; ?>>Flyer</option>
									<option value="Newspaper" <?php if($leadsource=="Newspaper") echo "selected"; ?>>Newspaper</option>
									<option value="SMS"<?php if($leadsource=="SMS") echo "selected"; ?>>SMS</option>
									<option value="Street Hoardings" <?php if($leadsource=="Street Hoardings") echo "selected"; ?>>Street Hoardings</option>
									<option value="Event" <?php if($leadsource=="Event") echo "selected"; ?>>Event</option>
									<option value="TV/Radio" <?php if($leadsource=="TV/Radio") echo "selected"; ?>>TV/Radio</option>							
								</select>
						</div>
					</div>
					<div class="col-lg-2 col-md-3 col-sm-3 col-xs-4">
						<div class="form-group">
							<label class=" control-label">Lead status</label>
							<select class="form-control" name="leadstatus" >
								<option value="" <?php if($leadstatus=="") echo "selected"; ?>>-- Select a type --</option>
								<option value="Pending" <?php if($leadstatus=="Pending") echo "selected"; ?>>Pending</option>
								<option value="Converted" <?php if($leadstatus=="Converted") echo "selected"; ?>>Converted</option>
								<option value="Close" <?php if($leadstatus=="Close") echo "selected"; ?>>Close</option>							
							</select>
						</div>
					</div>
					<div class="col-lg-2 col-md-3 col-sm-3 col-xs-4">
						<div class="form-group">
							<label class=" control-label">Lead representative</label>
							<select class="form-control" name="leaduser" >
								<option value="" <?php if($leadr=="") echo "selected"; ?>>-- Select User --</option>
								<?php
								$sql2="SELECT * from user where branch_id='".$branch_id."' order by username asc";
								$result2=query_by_id($sql2,[],$conn);
								foreach($result2 as $row2) {
									?>
								<option value="<?php echo $row2['id']; ?>" <?php if($uid==$row2['id']||$leadr==$row2['id']) echo "selected"; ?>><?php echo $row2['username']; ?></option>
								<?php } ?>
							</select>
						</div>
					</div>
					<div class="clearfix"></div>
					<div class="col-lg-12">
						<div class="panel-body">
							<div class="form-group pull-right">
								<button type="submit" name="submit" class="btn btn-filter"><i class="fa fa-filter" aria-hidden="true"></i>Filter</button>
								<button type="submit" name="reset" class="btn btn-danger"><i class="fa fa-times" aria-hidden="true"></i>Reset</button>
							</div>
						</div>
					</div>
					
					</form>
					</div>
					<div class="row">
					<div class="col-lg-12">
						<div class="panel-body">
									<div class="table-responsive">
										<table class="table table-bordered no-margin table_datatable" id="enquiry_tbl">
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
													$sql1="SELECT * from enquiry where active=0 and branch_id='".$branch_id."' ".$submit." order by id desc";
													$result1=query_by_id($sql1,[],$conn);
													$it = 1;
													foreach($result1 as $row1) {
													?>
												<tr>													
													<td><?php echo $row1['customer']; ?></td>
													<td><?php echo $row1['email']; ?></td>
													<td><?php echo $row1['cont']; ?></td>
													<td><?php echo my_date_format($row1['datefollow']); ?></td>
													<td><?php echo $row1['type']; ?></td>
													<td><?=($row1['enquiry'] != '')?getEnquiryfor($row1['enquiry']):'' ?></td>
													
													<td>
                                    			    <?php
                                    			        if($row1['leadstatus'] == 'Converted'){ ?>
                                    			            <a href="enquiry.php?id=<?php echo $row1['id']; ?>">
                                            					<button class="btn btn-info btn-xs">
                                            						<i class="fa fa-eye" aria-hidden="true"></i>View 
                                            					</button>
                                            				</a>
                                    			            <button class="btn btn-success btn-xs">
                                        						<i class="fa fa-check" aria-hidden="true"></i>Converted 
                                        					</button>
                                    			        <?php } else if($row1['leadstatus'] == 'Close'){ ?>
                                    			            <a href="enquiry.php?id=<?php echo $row1['id']; ?>">
                                            					<button class="btn btn-info btn-xs">
                                            						<i class="fa fa-eye" aria-hidden="true"></i>View 
                                            					</button>
                                            				</a>
                                    			            <button class="btn btn-danger btn-xs">
                                        						<i class="fa fa-times" aria-hidden="true"></i>Closed 
                                        					</button>
                                    			        <?php } else { ?>
                                    			            <a href="enquiry.php?id=<?php echo $row1['id']; ?>">
                                            					<button class="btn btn-warning btn-xs">
                                            						<i class="fa fa-pencil-square-o" aria-hidden="true"></i>Edit 
                                            					</button>
                                            				</a>
                                            				<a href="enquiry.php?d=<?php echo $row1['id']; ?>" onclick="return confirm('Are You Sure?')">
                                            					<button class="btn btn-danger btn-xs">
                                            						<i class="icon-delete"></i>Delete
                                            					</button>
                                            				</a>
                                    			        <?php }
                                    			    ?>
                                    			</td>
												</tr>
													<?php 
													$it ++ ;
													} ?>
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
					<!-- Row ends -->
					
				</div>
				<!-- Main container ends -->
			
			</div>
			<!-- Dashboard Wrapper End -->
		
		</div>
		<!-- Container fluid ends -->

<script>


$(document).ready(function(){
    
		var table = $('#enquiry_tbl').DataTable( {
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

function checkcont() {
	var cat = $('#cont').val();
jQuery.ajax({
url: "checkenq.php?con="+$("#cont").val(),
//data:'cat='+$("#bcont").val(),
type: "POST",
success:function(data){
$("#cont-status").html(data);
//alert(data);
if ( data.indexOf("Already Exist") > -1 ) {
	$('#cont').val("");
}
},
error:function (){}
});
}
</script>
		
<?php 
include "footer.php";
		
function getcat($cid) {
	global $conn;
	global $branch_id;
			$sql="SELECT * from expensecat where id='$cid' and branch_id='".$branch_id."'";
			$result=query_by_id($sql,[],$conn)[0];
			if($result) {
				return $result['title'];
			}
    }
 

?>