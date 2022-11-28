<?php
include "./includes/db_include.php";

include "topbar.php";
include "header.php";
include "menu.php";
?>

<div class="dashboard-wrapper">
    <div class="main-container">
        <div id="appointment_history" class="tab-pane fade in active">
            <div class="row gutter">
    			<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
    				<form action="" method="post">
    					<div class="panel">
    						<div class="panel-heading">
    							<h4>Web appointment history</h4>
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
    </div>
</div>
</div>
<?php include "footer.php";?>

<script>
    $('document').ready(function(){
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
    			'url':'ajax/fetch_appointment_history.php?type=1'
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
    	$('.custom-download-btn a').addClass('btn-warning pull-right download-btn mr-left-5'); 
    
    	
    });
</script>