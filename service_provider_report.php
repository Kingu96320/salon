<?php 
	include_once './includes/db_include.php';
	$branch_id = $_SESSION['branch_id'];
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
						<h4 class="pull-left">Service provider reports</h4>
    					<span id="download-btn"></span>					
    					<div class="clearfix"></div>
					</div>
					<div class="panel-body">
						<div class="row">
							<?php $current_date=date('Y-m-d')?>
							<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
								<div class="form-group">
									<label class=" control-label">Select date</label>
									<input type="text" class="form-control" range-attr="daterange" id="daterange" name="date"  placeholder="01/01/1990 - 12/05/2000" required readonly>
								</div>
							</div>
							<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
								<div class="form-group">
									<label class=" control-label">Service provider</label>
									<input type="text" class="form-control" id="staff" value="" name="staff" placeholder="Autocomplete(Name & Phone)">
									<input type="hidden" name="staffid" value="0" id="staffid" > 
								</div>
							</div>
							<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
								<div class="form-group">
									<button style="margin-top:22px;" type="submit" class="btn btn-filter" id="date_filter"><i class="fa fa-filter" aria-hidden="true"></i>Filter</button>
								</div>
							</div>
							
						</div>
						<div class="row">
							<div class="col-lg-12">
								<div class="panel-body">
									<div class="table-responsive">
										<table id="service_provider_table" class="table table-bordered no-margin">
											<thead>
												<tr>
													<th>Date</th>
													<th>Service provider</th>
													<th>Contact</th>
													<th>Service</th>
													<th>Service price</th>
													<th>Commission</th>
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
		<!-- Row ends -->
		
	</div>
	<!-- Main container ends -->
	
</div>
<!-- Dashboard Wrapper End -->

</div>
<!-- Container fluid ends -->

<?php include "footer.php"; ?>
<script> 
	$('#date_filter').click(function(){
		$('#service_provider_table').DataTable().destroy();
		service_provider_table();
	});
	function confirmDelete(){
		return confirm('Are you sure?');
	}
	/*******Server_side_datatable*********/
		$(document).ready(function(){
			service_provider_table();
		});
		$(function() {
			autocomplete_sers();										
		});
		function autocomplete_sers(){
			$("#staff").autocomplete({
				source: "autocomplete/beautician.php",
				minLength: 1,	
				select: function(event, ui) {
					$('#staffid').val(ui.item.id); 
					$('#staff').val(ui.item.cont); 
				}				
			});	
		}
		
		function service_provider_table(){
		    var daterange = $('#daterange').val();
    		var date = daterange.split("-");
    		if(daterange == ''){
    			var from_Date = '';
    			var to_Date = '';
    		} else {
    			var from_Date = isoDate(date[0]);
    			var to_Date = isoDate(date[1]);
    		}
    		
			var sp_id=$('#staffid').val();
			var table_title=$('.main-container').find('h4').html();
			var service_tbl = $('#service_provider_table').DataTable({
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
				{
					extend: 'csvHtml5',
					text: '<i class="fa fa-file-text-o"></i> CSV',
					titleAttr: 'CSV',
					title: table_title,
					exportOptions: {
						columns: ':not(:last-child)',
					}
				},
				{
					extend: 'print',
					title: table_title,
					exportOptions: {
						columns: ':visible'
					},
					customize: function(win) {
						$(win.document.body).find( 'table' ).find('td:last-child, th:last-child').remove();
					}
				},
				],
				'processing': true,
				'serverSide': true,
				'serverMethod': 'post',
				'aaSorting': [[ 0, "desc" ]],
				'ajax': {
					'url':'ajax/fetch_service_provider_report.php?from_date='+from_Date+'&to_date='+to_Date+'&sp_id='+sp_id
				},
				'columns': [
				{ data: 'doa' },
				{ data: 'service_provider' },
				{ data: 'cont' },
				{ data: 'service' },
				{ data: 'serice_price' },
				{ data: 'commission_amount' },
				],
				// 'columnDefs': [{
				// 'targets': [7,8], // column index (start from 0)
				// 'orderable': false, // set orderable false for selected columns
				// }],
			});
    		// To move download button in another div out of the dataTables
    		var buttons = new $.fn.dataTable.Buttons(service_tbl, {
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
    					
			
		}
		
		// jquery function to change daterange date in iso date standard  Y-m-d (2019-11-01)

    	function isoDate(date){	
    	    var datespit = date.split('/');
    		var day = datespit[1].replace(' ','');
    		var month = datespit[0].replace(' ','');
    		var year = datespit[2].replace(' ','');
    		return year+'-'+month+'-'+day;
    	}
		/*******End********/ </script>
		