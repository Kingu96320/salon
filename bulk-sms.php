<?php 
	include "./includes/db_include.php";
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
			
			<div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">
				<div class="panel">
					<div class="panel-heading">
						<h4>Select clients</h4>
					</div>
					<div class="panel-body">
						
						<select class="form-control total_disc_row_type" id="select_clients">
							<option value="All clients" Selected>All clients</option>
							<option value="Acitve clients" >Active clients</option>
							<option value="Churn prediction">Churn prediction</option>
							<option value="Defected clients" >Defected clients</option>
							<option value="1" >Appointments today</option>
							<option value="0">Appointments tomorow</option>
							<option value="1" >Appointments next week</option>
							
							<option value="1" >Membership 1</option>
							<option value="0">memberhsip 2</option>
							<option value="1" >memberhsip3</option>
						</select>
						
						
						<div class="clear"></div>
						
						<table id='empTable' class="table table-bordered" style="width:100%;">
							<thead>
								<tr>
									<th><input type="checkbox" value="0" id="select-all"> Name</th>
									<th>Contact number</th>
									<th>First visit</th>
									<th>Last visit</th>
									<th>Gender</th>
									<th>Points</th>
									<th>Action</th>
								</tr>
							</thead>
						</table>
					</div>
				</div>
			</div>
			
			
			<div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">
				<div class="panel">
					<div class="panel-heading">
						<h4>Settings</h4>
					</div>
					<div class="panel-body">
						<textarea placeholder="Type SMS" class="form-control" name="review" rows="4" maxLength="160" id="message-content" ></textarea>
						<input type="hidden" id="update_message"></input>
						Character(s) Remaining
						<small><span id="rchars">0</span>/160 Characters | 1 SMS = 160 Characters<span class="pull-right" id="sent_messages" style="display:none;">0</span></small> <br>
						<button class="btn btn-success btn-md"  id="sms-button" onclick="sendsms_old()">Send Message</button> 
						<a href="javascript:void(0)" class="btn btn-success btn-md" id="sms-button-loading" style="display:none">
							<i class="fa fa-circle-o-notch fa-spin"></i>Sending....
						</a>
						<button class="btn btn-success btn-md pull-right"  id="save_message">Save Message</button> 
						<a href="javascript:void(0)" class="btn btn-success btn-md pull-right" id="saving-button-loading" style="display:none">
							<i class="fa fa-circle-o-notch fa-spin"></i>Saving....
						</a>
						<div class="panel">
							<div class="panel-heading">
								<h4>Messages</h4>
							</div>
							<div class="panel-body">
								<div class="row">
									<div class="col-lg-12">
										<div class="table-responsive">
											<table  class="table table-striped table-bordered about message">
												<thead>
													<tr>
														<th width="50%">Messages</th>
														<th width="50%">Action</th>
													</tr>
												</thead>
												<tbody> 
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
			
			<div class="clearfix"></div>
			<!-- Row ends -->
			<!-- Row starts -->
		</div>
		<!-- Main container ends -->
	</div>
	<!-- Dashboard Wrapper End -->
</div>
<!-- Container fluid ends -->
</div>
<script>
	/*******Server_side_datatable*********/
		$(document).ready(function(){
			$('#empTable').DataTable({
				"dom": "lfrti",
				"lengthChange": false,
				'iDisplayLength': 50000,
				"scrollY": 700,
				"scrollX": true,
				'processing': true,
				'serverSide': true,
				'serverMethod': 'post',
				"fnDrawCallback": function (oSetStings) {
					$("#empTable_wrapper").find('.paging_simple_numbers').append('<li class="paginate_button"><button class="btn-info" style="float : right;" onclick="sendsms_old()">Send SMS</button></li>');
				},
				
				'ajax': {
					'url':'ajax/fetch_clients_info.php?page=bulk_sms',
					'beforeSend': function () {$('.main-container').append('<div class="loading">Loading&#8230;</div>');},
					'complete'   : function () { $('.main-container').find('.loading').attr('style','display:none'); }	,
				},
				'columns': [
				{ data: 'name' },
				{ data: 'cont' },
				{ data: 'firstvisit' },
				{ data: 'lastvisit' },
				{ data: 'gender' },
				{ data: 'points' },
				{ data: 'action' },
				],
				'columnDefs': [{
					'targets': [0,2,3,5], // column index (start from 0)
					'orderable': false, // set orderable false for selected columns
				}]
			});
			
			$('#select_clients').on('change',function(){
			var selected_option=$(this).val();
				if(selected_option == 'All clients'){
					fetch_clients();
				}else if(selected_option == 'Acitve clients'){
					fetch_clients('active','Acitve clients');
				}else if(selected_option == 'Churn prediction'){
					fetch_clients('churn_prediction','Churn prediction');
				}else if(selected_option == 'Defected clients'){
					fetch_clients('inactive','Defected clients');
				}
			});
			
		});
		/*******End********/
			
			var maxLength = 0;
			$('textarea').keyup(function() {
				var textlen = maxLength + $(this).val().length;
				$('#rchars').text(textlen);
			});
			
			$("#select-all").click(function () {
				$(".chkk").prop('checked', $(this).prop('checked'));
				
			});
			
			$('input[type=checkbox],textarea').on('click keydown',function(){
				$('#sent_messages').html("");
				$('#message-content').css('border-color','');
				
				
			});
			var json_values = [];
			function sendsms_old() {
				json_values = [];
				var count = $('input.chkk:checked').length;
				if(count>0){
					$(".chkk").each(function()
					{
						if($(this).is(':checked'))
						{
							json_values.push({name: $(this).data("name"),contact: $(this).data("contact") });
						}
					});
					sendmessage();
					}else{
					alert('Please Select at least one client'); 
				}
			}
			
			function sendmessage() {
				var message = $('#message-content').val();
				if(message !=''){
				$('#sms-button-loading').show();
				$('#sms-button').hide();
				$.ajax({
					type: "POST",
					url: 'ajax/sendsms.php',
					data: {
						json: JSON.stringify(json_values,true),
						message: message,
					},
					dataType: 'text',
					success: function(result) {
						$('#sms-button-loading').hide();
						$('#sms-button').show();
						toastr.success(result);
						$('#sent_messages').show();
						$('#sent_messages').html(result).css("color","red");
						$('#message-content').val('Hello {name}\nFrom {Salon}');
						
					}
				});
				}else{
					$('#message-content').css('border-color','red');
				}
			}
			$('#save_message').click(function(){
				var eid=$('#update_message').val(); 
				var update_message="";
				if($('#update_message').val() > 0){
					var update_message="?update_message="+eid;
					}else{
					$('#add-slider-button-loading').show();
					$('#add-slider-button-loading-edit').hide();
					$('#add-slider-button').hide();
				}
				var message=$('#message-content').val(); 
				$.ajax({
					url: "ajax/message_saved.php"+update_message, // Url to which the request is send
					type: "POST",             // Type of request to be send, called as method
					data:{message:message},
					success: function(data){   // A function to be called if request succeeds
						if(data){
							var di = JSON.parse(data);
							if(di['data-inserted'] == '1'){
								fetch_meassges();
								$('#add-slider-button').show();
								$('#add-slider-button-loading').hide();
								$('input').val("");
								 
								$('#message-content').val('Hello {name}\nFrom {Salon}');
							} 
						}
					},
				});	
			});	
			$(window).on('load', function(){
				fetch_meassges();
				$('#message-content').val('Hello {name}\nFrom {Salon}');
			});
			function fetch_meassges(){
				$('.message').DataTable().destroy();
				var table =$('.message').dataTable({
					"destroy": true,	
					"sAjaxSource": "ajax/fetch_messages.php",
					"dataSrc":"",
					"aoColumns": [
					{ mData: 'messages' },
					{ mData: 'action'},
					],			 
				});
				
			}
			
			function edit_delete(id,type){
				if(type == 'edit'){
					$.ajax({
						type: "POST",
						url: "ajax/message_saved.php",
						data: {eid:id},
						cache: false,
						success: function(data){
							var ds=JSON.parse(data);
							$("#message-content").val(ds[0]['message']);
							$("#update_message").val(ds[0]['id']);
						}
					});
					
					}else if(type == 'delete'){
					var reply = confirm('Are you sure?'); 
					
					if(reply === true){
						$('.message').DataTable().destroy();
						$('.message').dataTable({
							"destroy": true,	
							"sAjaxSource": "ajax/message_saved.php?did="+id,
							"dataSrc":"",
							"aoColumns": [
							{ mData: 'messages' },
							{ mData: 'action'},
							],
						}); 
						fetch_meassges();
						$('#message-content').val('Hello {name}\nFrom {Salon}');
						$('input').val("");
						
					}
				}
				return false; 
			}
			
	function fetch_clients(client_type,table_title){
		var ct="";
		if(client_type){
			ct='?client_type='+client_type;
			}else{
			table_title = 'Clients';
		}
		
		$('#empTable').DataTable().destroy();
		$('#empTable').DataTable({
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
			
			
			// 'copy', 'csv', 'excel', 'pdf', 'print'
			],
			'destroy': true,
			'processing': true,
			'serverSide': true,
			'serverMethod': 'post',
			"fnDrawCallback": function (oSetStings) {
				$("#empTable_wrapper").find('.paging_simple_numbers').append('<li class="paginate_button"><button class="btn-info" style="float : right;" onclick="sendsms_old()">Send SMS</button></li>');
			},
			'ajax': {
				'url':'ajax/fetch_clients_info.php'+ct,
				'beforeSend': function () {
					$('.main-container').append('<div class="loading">Loading&#8230;</div>');
				},
				'complete': function () { $('.main-container').find('.loading').attr('style','display:none'); }
				
			},
			'columns': [
			{ data: 'name' },
			{ data: 'cont' },
			{ data: 'firstvisit' },
			{ data: 'lastvisit' },
			{ data: 'gender' },
			{ data: 'points' },
			{ data: 'action' },
			],
			'columnDefs': [ {
				'targets': [0,2,3,5], // column index (start from 0)
				'orderable': false, // set orderable false for selected columns
			}],
			
		});
		
	}
			
			
		</script>
		<?php include "footer.php"; ?>														
		