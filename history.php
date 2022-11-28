<?php
include "./includes/db_include.php";
$branch_id = $_SESSION['branch_id'];
include "topbar.php";
include "header.php";
include "menu.php";
?>

<div class="dashboard-wrapper">
    <div class="main-container">
        <ul class="nav nav-tabs">
            <li class="active"><a data-toggle="tab" href="#appointment_history">Appointment</a></li>
            <li><a data-toggle="tab" href="#billing_history">Billing</a></li>
            <li><a data-toggle="tab" href="#wallet_history">Wallet</a></li>
        </ul>
        <div class="tab-content">
            <!-- Appointment history start -->
            <div id="appointment_history" class="tab-pane fade in active">
                <div class="row gutter">
        			<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        				<form action="" method="post">
        					<div class="panel">
        						<div class="panel-heading">
        							<h4>Appointment history</h4>
        						</div>
        						<div class="panel-body"> 
        							<div class="clearfix"></div>
        							<br>
        							<div class="table-responsive">
        								<table id="appointment_history_table" class="table table-bordered" >
        									<thead>
        										<tr>
        											<th>Date of appointment</th>
        											<th>Date of booking</th>
        											<th>Client name</th>
        											<th>Number</th>
        											<th>Amount</th>
        											<th>Paid</th>
        											<th>Pending</th>
        											<th>Notes</th>
        											<!--<th>Provider</th>-->
        											<th>Payment mode</th>
        											<th width="120">Action</th>
        										</tr>
        									</thead>	
        									<tbody>				
        									</tbody>				
        								</table>
        							</div>
        							
        						</div>
        					</div>
        				</form>
        			</div>
        		</div>
            </div>
            <!-- Appointment history end -->
            
            <!-- Billing history start -->
            <div id="billing_history" class="tab-pane fade">
                <div class="row gutter">
            		<div class="col-lg-12">	
            			<div class="panel">
            				<div class="panel-heading">
            					<h4>Billing history</h4>
            					<span class="hidden" id="download-btn"></span>
            					<div class="clearfix"></div>
            				</div>
            				<div class="panel-body">
            					<div class="clearfix"></div>
            					<div class="table-responsive">
            						<table id="billing_history_table" class="table table-bordered no-margin" style="width:100%; white-space: nowrap">
            							<thead>
            								<tr>
            									<th>Date of Bill</th>
            									<th>Client</th>
            									<th>Contact</th>
            									<th>Total</th>
            									<th>Appointment Advance</th>
            									<th>Paid</th>
            									<th>Pending</th>
            									<th>Notes</th>
            									<th>Payment mode</th>
            									<th>Action</th>
            								</tr>
            							</thead>
            						</table>
            					</div>
            				</div>
            			</div>
            		</div>
            	</div>
            </div>
            <!-- Billing history end -->
            
            <!-- Client wallet history start -->
            <div id="wallet_history" class="tab-pane fade">
                <div class="row gutter">
					<div class="col-lg-12">
						<div class="panel">
							<div class="panel-heading">
								<h4>Client's wallet</h4>
							</div>
							<div class="panel-body">
								<div class="">
									<div class="">
										<div class="table-responsive">
											<table id='empTablewallet' class="table table-bordered">
												<thead>
													<tr>
														<th>Date</th>
														<th>Client name</th>
														<th>Contact number</th>
														<th>Wallet amount</th>
														<th width="200">Action</th>
													</tr>
												</thead>
											</table>
										</div>
									</div>
								</div>
							</div>
						</div>	
					</div>
				</div>
            </div>
            <!-- Client wallet history end -->
        </div>
        
    </div>
</div>
</div>
<?php include "footer.php";?>

<script>
    $('document').ready(function(){
        
        // Wallet details
        
        $('#empTablewallet').DataTable({
			'processing': true,
			'serverSide': true,
			'serverMethod': 'post',
			'ajax': {
				'url':'ajax/fetch_wallet_details.php'
			},
			'columns': [
			{ data: 'time_update' },
			{ data: 'name' },
			{ data: 'cont' },
			{ data: 'remaning_amount' },
			{ data: 'action' },
			],
			'columnDefs': [ {
				'targets': [0,1,2,3,4], // column index (start from 0)
				'orderable': false, // set orderable false for selected columns
			}]
		});
        
        // appointment history
        var table_title="Appointment history";
    	var table = $('#appointment_history_table').DataTable({
    		'lengthMenu': [[10, 25, 50, 100, -1], [10, 25, 50, 100, 'All']],
    		'dom': 'lBfrtip',
        	'buttons': [
        	{
        		extend: 'excelHtml5',
        		text: '<i class="fa fa-file-excel-o"></i> Excel',
        		titleAttr: 'Export to Excel',
        		title: table_title,
        		exportOptions: {
        			columns: ':not(:last-child)',
        		}
        	},
        	],
    		'processing': true,
    		'serverSide': true,
    		'serverMethod': 'post',
    		'aaSorting': [[ 1, "desc" ]],
    		'ajax': {
    			'url':'ajax/fetch_appointment_history.php'
    		},
    		'columns': [
    		{ data: 'doa' },
    		{ data: 'appdate' },
    		{ data: 'name' },
    		{ data: 'cont' },
    		{ data: 'total' },
    		{ data: 'paid' },
    		{ data: 'due' },
    		{ data: 'notes' },
    		{ data: 'pay_method_name' },
    		{ data: 'action',class : 'multi-action-btn' },
    		],
    		'columnDefs': [ {
    			'targets': [0,8], // column index (start from 0)
    			'orderable': false, // set orderable false for selected columns
    		}]
    	});
    	var buttons = new $.fn.dataTable.Buttons(table, {
         	buttons: [
         		{
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
    	
        // billing history
        
        var table_title=$('.main-container').find('h4').html();
		var table = $('#billing_history_table').DataTable({
			'lengthMenu': [[10, 25, 50, 100, 9999999999], [10, 25, 50, 100, 'All']],
			'dom': 'lBfrtip',
			'buttons': [
			{
				extend: 'excelHtml5',
				text: '<i class="fa fa-file-excel-o"></i> Excel',
				titleAttr: 'Export to Excel',
				title: table_title,
				exportOptions: {
					columns: ':not(:last-child)',
				}
			},
			],
			'processing': true,
			'serverSide': true,
			'serverMethod': 'post',
			'aaSorting': [[ 0, "desc" ]],
			'ajax': {
				'url':'ajax/fetch_billing_history.php'
			},
			'columns': [
			{ data: 'doa' },
			{ data: 'c_name' },
			{ data: 'cont' },
			{ data: 'total' },
			{ data: 'advance' },
			{ data: 'paid' },
		    { data: 'due' },
			{ data: 'notes' },
			{ data: 'pay_mode' },
			{ data: 'action' },
			],
			'columnDefs': [ {
				'targets': [7,8], // column index (start from 0)
				'orderable': false, // set orderable false for selected columns
			}]
		});
		var buttons = new $.fn.dataTable.Buttons(table, {
	     	buttons: [
	     		{
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
</script>