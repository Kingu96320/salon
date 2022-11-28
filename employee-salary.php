<?php
    include "./includes/db_include.php";
    $branch_id = $_SESSION['branch_id'];
    
    if($_SESSION['user_type'] != 'superadmin'){
        header('Location: dashboard.php');
    }
    
    if(isset($_POST['submit'])){
        $date = htmlspecialchars(trim($_POST['date']));
        $emp_type = htmlspecialchars(trim($_POST['emp_type']));
        $emp_id = htmlspecialchars(trim($_POST['emp_id']));
        $sal_type = htmlspecialchars(trim($_POST['sal_type']));
        $amount = htmlspecialchars(trim($_POST['amount']));
        $comment = htmlspecialchars(trim($_POST['comment']));
        if($emp_id != ''){
            $id = get_insert_id("INSERT INTO salary_history SET date='".$date."', emp_type='".$emp_type."', emp_id='".$emp_id."', sal_type='".$sal_type."', amount = '".$amount."', comment='".$comment."', branch_id='".$branch_id."', status='1'",[],$conn);
            $_SESSION['t']  = 1;
    		$_SESSION['tmsg']  = "Saved Successfully";
    		echo '<meta http-equiv="refresh" content="0; url=employee-salary.php" />';die(); 
        } else {
            $_SESSION['t']  = 2;
    		$_SESSION['tmsg']  = "Employee not selected from dropdown.";
    		echo '<meta http-equiv="refresh" content="0; url=employee-salary.php" />';die(); 
        }
    }
    
    if(isset($_GET['action']) && $_GET['action'] == 'get_employee'){
        $emp_type = $_GET['emp_type'];
        $term = htmlspecialchars($_GET['term']);
        if($emp_type == 1){
            $sql = query_by_id("SELECT concat(name,' - ',cont) as value, id FROM beauticians WHERE name LIKE '%$term%' OR cont LIKE '%$term%' AND branch_id='".$branch_id."'",[],$conn);
        } else if($emp_type == 2){
            $sql = query_by_id("SELECT concat(name,' - ',cont) as value, id FROM employee WHERE name LIKE '%$term%' OR cont LIKE '%$term%' AND branch_id='".$branch_id."'",[],$conn);
        }
        echo json_encode($sql);
        die();
    }
    
    if(isset($_GET['d']) && $_GET['d'] != ''){
        query("UPDATE salary_history SET status='0' WHERE id='".htmlspecialchars(trim($_GET['d']))."'",[],$conn);
        $_SESSION['t']  = 1;
		$_SESSION['tmsg']  = "Deleted successfully";
		echo '<meta http-equiv="refresh" content="0; url=employee-salary.php" />';die();
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
						<h4>Manage Salaries</h4>
					</div>
					<div class="panel-body">
					    <div class="row">
					        <form action="" method="post">
					            <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
									<div class="form-group">
										<label for="name">Date <span class="text-danger">*</span></label>
										<input type="text" readonly class="form-control date" value="<?= date('Y-m-d') ?>" name="date" required />
									</div>
								</div>
								<div class="col-lg-2 col-md-2 col-sm-2 col-xs-12">
									<div class="form-group">
										<label for="name">Employee type <span class="text-danger">*</span></label>
										<select class="form-control" name="emp_type" id="emp_type" required>
										    <option value="1">Service provider</option>
										    <option value="2">Staff</option>
										</select>
									</div>
								</div>
								<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
									<div class="form-group">
										<label for="name">Employee name <span class="text-danger">*</span></label>
										<input type="text" name="emp_name" class="form-control emp_name" required />
										<input type="hidden" id="emp_id" name="emp_id" />
									</div>
								</div>
								<div class="col-lg-2 col-md-2 col-sm-2 col-xs-12">
									<div class="form-group">
										<label for="name">Salary type <span class="text-danger">*</span></label>
										<select class="form-control" name="sal_type" required>
										    <option value="1">Salary</option>
										    <option value="2">Advance</option>
										    <option value="3">Incentives</option>
										    <option value="4">Bonus</option>
										</select>
									</div>
								</div>
								<div class="col-lg-2 col-md-2 col-sm-2 col-xs-12">
									<div class="form-group">
										<label for="name">Amount <span class="text-danger">*</span></label>
										<input type="number" step="0.01" name="amount" class="form-control" required />
									</div>
								</div>
								<div class="clearfix"></div>
								<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
									<div class="form-group">
										<label for="name">Comments</label>
										<textarea name="comment" rows="5" style="resize:none;" class="form-control"></textarea>
									</div>
								</div>
								<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 pull-right">
    								<div class="form-group">
    								    <button type="submit" name="submit" class="btn btn-success pull-right"><i class="fa fa-plus" aria-hidden="true"></i>Add</button>
    								</div>
    							</div>
					        </form>
					    </div>
					</div>
				</div>
			</div>
		</div>
		<div class="row gutter">
			<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
				<div class="panel">
					<div class="panel-heading  heading-with-btn">
						<h4 class="pull-left">Salary History</h4>
						<span id="download-btn"></span>
						<div class="clearfix"></div>
					</div>
					<div class="panel-body">
						<div class="row">
							<div class="col-lg-12">
								<div class="table-responsive">
								    <table class="table table-bordered no-margin" id="salary_table">
								        <thead>
								            <tr>
								                <th>Date</th>
								                <th>Employee type</th>
								                <th>Employee name</th>
								                <th>Salary type</th>
								                <th>Amount</th>
								                <th>Comment</th>
								                <th>Action</th>
								            </tr>
								        </thead>
								        <tbody>
								            <?php
								                $data = query_by_id("SELECT * FROM salary_history WHERE branch_id='".$branch_id."' AND status='1'",[],$conn);
								                if($data){
								                    foreach($data as $sd){
								                        if($sd['emp_type'] == '1'){
								                            $name = query_by_id("SELECT CONCAT(name,'-',cont) as name FROM beauticians WHERE id='".$sd['emp_id']."'",[],$conn)[0]['name'];
								                        } else if($sd['emp_type'] == '2'){
								                            $name = query_by_id("SELECT CONCAT(name,'-',cont) as name FROM employee WHERE id='".$sd['emp_id']."'",[],$conn)[0]['name'];
								                        }
								                        ?>
								                        <tr>
								                            <td><?php echo my_date_format($sd['date']); ?></td>
								                            <td><?php echo $sd['emp_type']=='1'?'Service provider':'Staff'; ?></td>
								                            <td><?php echo $name; ?></td>
								                            <td><?php 
								                                if($sd['sal_type'] == '1'){
								                                    echo 'Salary';
								                                } else if($sd['sal_type'] == '2'){
								                                    echo 'Advance';
								                                } else if($sd['sal_type'] == '3'){
								                                    echo 'Incentives';
								                                } else if($sd['sal_type'] == '4'){
								                                    echo 'Bonus';
								                                } else {
								                                    
								                                }
								                            ?></td>
								                            <td><?php echo number_format($sd['amount'],2) ?></td>
								                            <td><?php echo $sd['comment'] ?></td>
								                            <td><a href="employee-salary.php?d=<?php echo $sd['id'] ?>" class="btn btn-danger btn-sm"><i class="icon-delete"></i>Delete</a></td>
								                        </tr>
								                        <?php
								                    }
								                }
								            ?>
								        </tbody>
								        <tfoot>
                                        <tr>
                                            <th colspan="4" style="text-align:right">Total:</th>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                        </tr>
                                    </tfoot>
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
?>
<script>
    $(document).ready(function(){
        $(".emp_name").autocomplete({
            source: function(request, response) {
				var emp_type = $('#emp_type').val();
				$.getJSON("employee-salary.php?emp_type="+emp_type, { term: request.term, action : 'get_employee'}, response);
			},
            minLength: 1,
            select: function(event, ui) {
               $('#emp_id').val(ui.item.id);
            }				
        });
        
        var table = $('#salary_table').DataTable( {
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
			],
			"footerCallback": function ( row, data, start, end, display ) {
                var api = this.api(), data;
     
                // Remove the formatting to get integer data for summation
                var intVal = function ( i ) {
                    return typeof i === 'string' ?
                        i.replace(/[\$,]/g, '')*1 :
                        typeof i === 'number' ?
                            i : 0;
                };
     
                // Total over all pages
                total = api
                    .column( 4 )
                    .data()
                    .reduce( function (a, b) {
                        return intVal(a) + intVal(b);
                    }, 0 );
     
                // Total over this page
                pageTotal = api
                    .column( 4, { page: 'current'} )
                    .data()
                    .reduce( function (a, b) {
                        return intVal(a) + intVal(b);
                    }, 0 );
     
                // Update footer
                $( api.column( 4 ).footer() ).html(pageTotal.toFixed(2));
            }
		});
		
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
		$('.custom-download-btn a').addClass('btn-warning pull-right download-btn mr-left-5');
    });
</script>