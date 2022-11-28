
<footer>
	© FSNPOS Solutions <span>Powered by <a href="http://fsnpos.com" target="_blank" style="color:#ffb400">fsnpos</a></span>
</footer>

<style type="text/css">
	<?php
		$disable_time = shopTimes();
		foreach($disable_time as $dt){
			echo ".alltimes .".$dt;
			if($dt != 'hour_23'){
				echo ", ";
			}
		} echo "{
			pointer-events: none;
			opacity : 0.3;
		}";
	?>
</style>

<script type="text/javascript">
	$( document ).ready(function() {
	    
	    $('.tableprint').DataTable({
			dom: 'lBfrtip',
			"bProcessing": true,
			"aaSorting":[],
			'lengthMenu': [[10, 25, 50, 100, 99999999], [10, 25, 50, 100, 'All']],
			"aoColumnDefs": [
                { "bSortable": false, "aTargets": [ 0, 7 ] }, 
            ],
			buttons: [	{
				extend: 'excelHtml5',
				text: '<i class="fa fa-file-excel-o"></i> Excel',
				titleAttr: 'Export to Excel',
				title: '<?php echo addSlashes(systemname($conn)); ?>',
				exportOptions: {
					columns: ':not(:last-child)',
				}
			},
			{
				extend: 'csvHtml5',
				text: '<i class="fa fa-file-text-o"></i> CSV',
				titleAttr: 'CSV',
				title: '<?php echo systemname($conn); ?>',
				exportOptions: {
					columns: ':not(:last-child)',
				}
			},
			{
				extend: 'print',
				exportOptions: {
					columns: ':visible'
				},
				customize: function(win) {
					$(win.document.body).find( 'table' ).find('td:last-child, th:last-child').remove();
				}
			},
			],
			"fnDrawCallback": function (oSettings) {
				$("#smstab_wrapper").find('.pagination').append('<li class="paginate_button"><a href="javascript:void(0)" style="border-radius:0px;background-color:#2877aa;border-color:#2877aa;color:#fff;" class="btn btn-info" onclick="sendsms()"><i style="margin-left:0px;" class="fa fa-paper-plane" aria-hidden="true"></i>Send SMS</a></li>');
			},
		});
		
		var tableFB = $('.grid').DataTable({
			"dom": 'lBfrtip',
			'lengthMenu': [[10, 25, 50, 100, 99999999], [10, 25, 50, 100, 'All']],
			"buttons": [
			'copy', 'csv', 'excel', 'print'
			],
			"bProcessing": true,
			"aaSorting":[],
			'columnDefs': [ {
				'targets': [0], // column index (start from 0)
				'orderable': false, // set orderable false for selected columns
			}],
		});
		var buttons = new $.fn.dataTable.Buttons(tableFB, {
		     buttons: [{
					extend: 'excelHtml5',
					text: '<i class="fa fa-file-excel-o" aria-hidden="true"></i> Export',
					titleAttr: 'Export to Excel',
					title: '<?php echo systemname($conn); ?>',
					exportOptions: {
						columns: ':not(:last-child)',
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

		$('.order_date_desc').DataTable({
			"dom": 'lBfrtip',
			'lengthMenu': [[10, 25, 50, 100, 99999999], [10, 25, 50, 100, 'All']],
			"buttons": [
			'copy', 'csv', 'excel', 'print'
			],
			"bProcessing": true,
			"aaSorting":[[0,'desc']],
			'columnDefs': [ {
				'targets': [0], // column index (start from 0)
				'orderable': false, // set orderable false for selected columns
			}],
		});  
		
		
		$('.gridsms').DataTable({
			"bProcessing": true,
			"aaSorting":[],
			"fnDrawCallback": function (oSettings) {
				$("#gridsms_wrapper").find('.pagination').append('<li class="paginate_button"><button class="btn-info" style="float : right;" onclick="sendsms()">Send SMS</button></li>');
			}
		});
		var table = $('.table_datatable').DataTable( {
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
		
		$('#printtable').DataTable({
			dom: 'lBfrtip',
			"aaSorting":[],
			buttons: [	
			'excelHtml5',
			'csvHtml5',
			'print'
			]
		});
		
		$('#excel').click(function(){
			var reportName=$(this).parents('.main-container').find('h4').html();    
			$("table").table2excel({
				exclude: ".noExl",
				name: "Worksheet Name",
				filename: reportName,
				fileext: ".xls",
				exclude_img: true,
				exclude_links: true,
				exclude_inputs: true,
			});
		});
	});
	function myalert() {
		<?php 
			$t = $_SESSION['t'];
			if($t==1){
				echo 'toastr.success("'.$_SESSION['tmsg'].'");';
				echo '$(".loading").attr("style","display:none")';
				}else if($t==2){
				echo 'toastr.error("'.$_SESSION['tmsg'].'", "Error");';
			}
		?>
		
		<?php $_SESSION['t'] = 0;
			$_SESSION['tmsg'] = 0;
		?>
	}
	
	
	
	function setTwoNumberDecimal(e) {
		if(parseFloat($(e).val())>0){
			$(e).val(parseFloat($(e).val()).toFixed(2)) ;
			}else{
			$(e).val(0);
		}
	}
	
	
	// $('.main-container').append('<div class="loading">Loading&#8230;</div>');
	function loader(){
		var flag=true;
		$('form#main-form').find(':input[required]').each(function(){
			if($(this).val() == ''){
				flag=false;
			}
		});
		if(flag){	
			$('.main-container').append('<div class="loading">Loading&#8230;</div>');
		}
	}
	function deleteloader(){
		
		$('.main-container').append('<div class="loading">Loading&#8230;</div>');
	}

	// function to download excel file

	function samplefile(filename){
		window.location.href = 'xlsxsamlefiles/sample_'+filename+'.xlsx';
	}
	
	function deleteDisabled(){
	    alert('Delete option is disabled in demo mode.');
	}


	// function to clear all form and error message when modal close button is clicked.

	function clearModalForm(errorDiv, modalParentDiv){
		$('#'+errorDiv).text('');
		$('#'+modalParentDiv+' input').val('');
	}

	$(document).ready(function(){
		$("[data-toggle=tooltip]").tooltip();
		$('.disableOutsideClick').modal({
		    show: false,
		    keyboard: false,
		    backdrop: 'static'
		});	

		// Bookmarking smooth scrollng

		$(document).on('click', 'a[href^="#"]', function (event) {
		    event.preventDefault();

		    $('html, body').animate({
		        scrollTop: $($.attr(this, 'href')).offset().top
		    }, 500);
		});

	});

	// function to check start and end time of salon

	function checkTime(id, time, errorDivId){
		var d = new Date();
		var sstime, eetime;
		$.ajax({  
        url:"ajax/system_details.php",
        method:"POST",
        dataType: "json",
        data: {'action':'getStartEndTime'},
        success:function(response){
            	if(response.status == '1'){
            		sstime = response.starttime;
            		eetime = response.endtime;
               		var stime = Date.parse('20 Aug 2000 '+sstime);
					var etime = Date.parse('20 Aug 2000 '+eetime);
					var cpmtime = Date.parse('20 Aug 2000 '+time+':00');
					if(stime == '' || etime == ''){
						//alert('Please select working hours in system setting tab.');
					} else {
						if(cpmtime < stime || cpmtime > etime){
							$('#'+id).val('');
							if(errorDivId != ''){
								$('#'+errorDivId).text('Select between '+onTimeChange(sstime)+' to '+onTimeChange(eetime));
								appointmenttime();
								$('.ser').prop('disabled',true);
							}
						} else {
							$('#'+errorDivId).text('');
							$('.ser').prop('disabled',false);
							appointmenttime();
						}
					}
               	} else if(response.status == '0'){
               		$('#'+id).val('');
               		$('#'+errorDivId).text('Appointment not available');
               		$('.ser').prop('disabled',true);
               	}
            }
       	});
	}

	// function to check appointment time of salon

	function checkappTime(id, time, errorDivId){
		var sstime, eetime;
		var date = $('#date').val();
		if(date != ''){
			if(time != ''){
				$('#date').removeClass('invalid');
				$.ajax({  
		        url:"ajax/system_details.php",
		        method:"POST",
		        dataType: "json",
		        data: {date : date, 'action':'checkapptime'},
		        success:function(response){
		            	if(response.status == '1'){
		            		sstime = response.starttime;
		            		eetime = response.endtime;
		               		var stime = Date.parse('20 Aug 2000 '+sstime);
							var etime = Date.parse('20 Aug 2000 '+eetime);
							var cpmtime = Date.parse('20 Aug 2000 '+time+':00');
							if(stime == '' || etime == ''){
								//alert('Please select working hours in system setting tab.');
							} else {
								var et_status = '<?= extratimeStatus(); ?>';
								if((cpmtime < stime || cpmtime > etime) && et_status!= '1'){
									$('#'+id).val('');
									if(errorDivId != ''){
										$('#'+errorDivId).text('Select between '+onTimeChange(sstime)+' to '+onTimeChange(eetime));
										// appointmenttime();
										//$('.ser').prop('disabled',true);
										//$('.ser').val('');
									}
								} else {
									var d = new Date();
									var dd = new Date(date);
									if(d.toDateString() === dd.toDateString()){
										if(Date.parse("'"+date+"' '"+time+':59') <	 Date.parse(d)){
											$('#'+errorDivId).text('Past time not allowed');
											$('#time').val('');
										} else {
											$('#'+errorDivId).text('');
											//$('.ser').prop('disabled',false);
											//$('.ser').val('');
											$('#close_time').val(eetime);
											$('#time').removeClass('invalid');
										}
									} else{
										$('#'+errorDivId).text('');
										//$('.ser').prop('disabled',false);
										//$('.ser').val('');
										$('#close_time').val(eetime);
										$('#time').removeClass('invalid');
									}
									if($('.ser').val() != ''){
										appointmenttime();
									}
								}
							}
		               	} else if(response.status == '0'){
		               		$('#'+id).val('');
		               		$('#'+errorDivId).text('Appointment not available');
		               		//$('.ser').prop('disabled',true);
		               	}
		            }
		       	});
			} else {
				$('#'+errorDivId).text('Please select time');
			}
		} else {
			$('#date').addClass('invalid');
			$('#time').val('');
			$('#time').removeClass('invalid');
		}
	}


	// function to change time in am/pm

	function onTimeChange(time) {
	  var timeSplit = time.split(':'), hours, minutes, meridian;
	  hours = timeSplit[0];
	  minutes = timeSplit[1];
	  if (hours > 12) {
	    meridian = 'PM';
	    hours -= 12;
	  } else if (hours < 12) {
	    meridian = 'AM';
	    if (hours == 0) {
	      hours = 12;
	    }
	  } else {
	    meridian = 'PM';
	  }
	  return((hours<9?'0':'')+hours + ':' + minutes + ' ' + meridian);
	}

	// functiont to change 12 hour time into 24 time

	function time12to24(time){
		var time = time;
		var hours = Number(time.match(/^(\d+)/)[1]);
		var minutes = Number(time.match(/:(\d+)/)[1]);
		var AMPM = time.match(/\s(.*)$/)[1];
		if(AMPM == "PM" && hours<12) hours = hours+12;
		if(AMPM == "AM" && hours==12) hours = hours-12;
		var sHours = hours.toString();
		var sMinutes = minutes.toString();
		if(hours<10) sHours = "0" + sHours;
		if(minutes<10) sMinutes = "0" + sMinutes;
		return (sHours + ":" + sMinutes + ":00");
	}
	// check date availability

	function dateAvailability(date){
		if(date != ''){
			$('#date').removeClass('invalid');
			$.ajax({  
		        url:"ajax/system_details.php",
		        method:"POST",
		        dataType: "json",
		        data: {'date': date, 'action':'checkDate'},
		        success:function(response){
		        	if(response.status == 0){
		        		$('#date').val('');
		        		$('#dateerror').text('Salon will be closed');
		        	} else {
		        		$('#dateerror').text('');
		        		checkappTime('time', $('#time').val(), 'apptime');
		        	}
		        }
		    });
		} else {
			$('#date').addClass('invalid');
		}
	}
	
</script>
<script type="text/javascript" src="ajax/ajax.js"></script>
<!--Full Calendar With scheduler---->
<script src="../salonSoftFiles_new/js/moment.min.js"></script>
<script src='../salonSoftFiles_new/full_calendar/lib/fullcalendar.min.js'></script>
<script src='../salonSoftFiles_new/full_calendar/scheduler.min.js'></script>
<script src="../salonSoftFiles_new/js/jquery.table2excel.js"></script>
<!-- D3 JS -->
<script src="../salonSoftFiles_new/js/d3/d3.v3.min.js"></script>

<!-- C3 Graphs -->
<script src="../salonSoftFiles_new/js/c3/c3.js"></script>
<script type="text/javascript" src="../salonSoftFiles_new/js/jquery.transit.js"></script>
<script type="text/javascript" src="../salonSoftFiles_new/js/ytLoad.jquery.js"></script>
<script type="text/javascript" src="../salonSoftFiles_new/js/lightbox.min.js"></script>

<script type="text/javascript" src="softsJS/formFieldsValidations.js"></script>
<script type="text/javascript" src="../salonSoftFiles_new/js/daterangepicker.js"></script>

<!-- new  version calander js files -->

<script src='../salonSoftFiles_new/packages/core/main.js'></script>
<script src='../salonSoftFiles_new/packages/interaction/main.js'></script>
<script src='../salonSoftFiles_new/packages/daygrid/main.js'></script>
<script src='../salonSoftFiles_new/packages/timegrid/main.js'></script>
<script src='../salonSoftFiles_new/packages/list/main.js'></script>

<script type="text/javascript" src="https://unpkg.com/@fullcalendar/core@4.3.1/main.min.js"></script>
<script type="text/javascript" src="https://unpkg.com/@fullcalendar/daygrid@4.3.0/main.min.js"></script>
<script type="text/javascript" src="https://unpkg.com/@fullcalendar/timegrid@4.3.0/main.min.js"></script>
<script type="text/javascript" src="https://unpkg.com/@fullcalendar/resource-common@4.3.1/main.min.js"></script>
<script type="text/javascript" src="https://unpkg.com/@fullcalendar/resource-daygrid@4.3.0/main.min.js"></script>
<script type="text/javascript" src="https://unpkg.com/@fullcalendar/resource-timegrid@4.3.0/main.min.js"></script>
<script src="https://polyfill.io/v3/polyfill.min.js?features=Intl.~locale.en"></script>
<script>
	function dp(){
	  	$('.date').datepicker({
			format: "yyyy-mm-dd",
			todayBtn: "linked",
			todayHighlight: true,
			autoclose: true,
			weekStart: 1,
			datesDisabled : [<?= holidayDates(); ?>],
			daysOfWeekDisabled : [<?= closeweekend(); ?>]
		});

		$('.urdate').datepicker({  //un restricted date
			format: "yyyy-mm-dd",
			todayBtn: "linked",
			todayHighlight: true,
			autoclose: true,
			weekStart: 1
		});
		
		$('.min_present_date').datepicker({
			format: "yyyy-mm-dd",
			todayBtn: "linked",
			todayHighlight: true,
			autoclose: true,
			weekStart: 1,
			startDate: "today"
		});
		
		$('.dob_annv_date').datepicker({
		    format: "yyyy-mm-dd",
			todayBtn: "linked",
			todayHighlight: true,
			autoclose: true,
			weekStart: 1,
			endDate: "today"
		});
	}
	function time(){
		$(".time").datetimepicker({
	        format: "HH:ii P",
	        showMeridian: true,
	        autoclose: true,
	        pickDate: false,
	        startView: 1,
    		maxView: 1
	    });
	    $(".datetimepicker").find('thead th').remove();
  		$(".datetimepicker").find('thead').append($('<th class="switch text-warning">').html('Pick Time'));
  		$(".datetimepicker").find('tbody').addClass('alltimes');
  		$('.switch').css('width','190px');

	}
	function maintime(){
		$(".maintime").datetimepicker({
	        format: "HH:ii P",
	        showMeridian: true,
	        autoclose: true,
	        pickDate: false,
	        startView: 1,
    		maxView: 1
	    });
	    $(".datetimepicker").find('thead th').remove();
  		$(".datetimepicker").find('thead').append($('<th class="switch text-warning">').html('Pick Time'));
  		$(".datetimepicker").find('tbody').addClass('maintime');
  		$('.switch').css('width','190px');

	}


	$(document).ready(function() {
	    $('[data-toggle="tooltip"]').tooltip();
	  // $ = jQuery.noConflict();
	  	$('input[range-attr="daterange"]').daterangepicker({
	    	opens: 'right'
	  	});

	 //  	$('#btnAdd').click(function(){
		// 	time();
		// });

		dp();
		time();
		maintime();
		$('input[type=number]').attr('min','0');
// 		$('input[type=number]').attr('maxLength','11');
// 		$('input[type=number]:not(".disc_row")').attr('oninput','this.value = Math.abs(this.value); javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);');
		$('input.price[type=number]').change(function(){
			$(this).val(parseFloat($(this).val(),10).toFixed(3).slice(0,-1));
		});
	});
	
	function pendingpaymode(mode_id, modeDiv){
		var options = 0;
		var totalVal = parseFloat(modeDiv.parent().parent().find('.pendtotal').val());
		if(totalVal == 0){
			modeDiv.val('1');
		} else {
	        var modeDiv = modeDiv.parent().parent();
    		var wallet_money = parseFloat($('#wallet_money').val());
    		var reward_point = parseInt($('#reward_point').val());
    		if(mode_id == '7'){	
    			if(totalVal != 0){
    				if(wallet_money == '0' || wallet_money == ''){
    					toastr.warning('Wallet is empty.');
    					modeDiv.find('.mthd').val('1');
    				} 
    				else {
    					var price_cal = parseFloat(wallet_money);
    					if(totalVal < wallet_money){
    						modeDiv.find('.amtpay').val(totalVal);
    					} else {
    						modeDiv.find('.amtpay').val(price_cal);
    					} 
    					if(reward_point == '' || reward_point == '0'){
    
    					} else {
    						$('#reward_point').val(reward_point);
    						$('#earned_points').text(reward_point);
    					}
    				}
    			}
    		} else if(mode_id == '9'){
    			if(totalVal != 0){
    				if(reward_point == '' || reward_point == '0'){
    					modeDiv.find('.amtpay').val('0');
    					toastr.warning('Don\'t have any reward point.');
    					modeDiv.find('.mthd').val('1');
    					sumup();
    				} else {
    					var point;
    					var point_price = <?= redeemprice()!=''?redeemprice():'0' ?>;
    					var redeem_point = <?= redeempoint()!=''?redeempoint():'0' ?>;
    					var pprice = parseFloat(totalVal);
    					if(reward_point > <?= maxredeempoint()!=''?maxredeempoint():'0' ?>){
    						point = <?= maxredeempoint()!=''?maxredeempoint():'0' ?>;
    					} else {
    						point = reward_point;
    					}
    					var price_cal = (parseInt(point)/parseInt(redeem_point))*parseInt(point_price);
    					
    					if(pprice > price_cal){
    						modeDiv.find('.amtpay').val(price_cal); 
    					} else {
    						modeDiv.find('.amtpay').val(pprice);
    					}
    				}
    			}
    		} else {
    			modeDiv.find('.amtpay').val('0');
    		}   
		}
	}
	
	function maxpendpayment(pend_amount, amount, currDiv){
	    var pending_amount = parseFloat(pend_amount);
	    if(amount == ''){
	        var amount = 0;
	    } else {
	        var amount = parseFloat(amount);
	        currDiv.removeClass('invalid');
	    }
    	var paid = 0;
		var redeem_point = <?= redeempoint()!=''?redeempoint():'0' ?>;
		var max_points = <?= maxredeempoint()!=''?maxredeempoint():'0' ?>;
		var mainDiv = currDiv.parent().parent();
		var currentDiv = currDiv;
		var rewardPoint = $('#reward_point').val();
		var walletMoney = $('#wallet_money').val();
		paid += parseFloat(currDiv.val()||0);
		if(parseFloat(paid) > pending_amount){
			toastr.warning('Amount exceeded total amount.');
			currentDiv.val(0);
			sumup();
		}
		else if(mainDiv.find('.mthd').val() == '9'){
			if(rewardPoint == '0'){
				toastr.warning('Don\'t have any reward point.');
			} else if(rewardPoint > 0){
				if(parseFloat((currentDiv.val()*redeem_point)) > parseFloat(max_points)){
			        toastr.warning('You can redeem max '+max_points+' points ( '+(max_points/redeem_point) +' <?= CURRENCY ?> ) at a time.');
					currentDiv.val(0);
			    }
				else if(parseFloat((currentDiv.val()*redeem_point)) > parseFloat(rewardPoint)){
					toastr.warning('You have only '+rewardPoint+' reward points');
					currentDiv.val(0);
				}
			}
		} else if(mainDiv.find('.mthd').val() == '7'){
			if(walletMoney == '0'){
				toastr.warning('Wallet is empty.');
			} else if(walletMoney > 0){
				
				if(parseFloat(currentDiv.val()) > parseFloat(walletMoney)){
					toastr.warning('You have '+walletMoney+' <?= CURRENCY ?> in your wallet account.');
					currentDiv.val(0);
				}
			}
		}
	}
	
	function pendingPayment(div){
	    var curDiv = div.parent().parent();
	    var payamount = curDiv.find('.amtpay').val();
	    if(payamount == '0'){
	        curDiv.find('.amtpay').addClass('invalid');
	    } else {
	        curDiv.find('.amtpay').removeClass('invalid');
	        var pay_amount = curDiv.find('.amtpay').val();
	        var clientid = curDiv.find('.clientid').val();
	        var inv_id = curDiv.find('.inv_id').val();
	        var method = curDiv.find('.mthd').val();
	        var pend = curDiv.find('.pendtotal').val();
	        var branch_id = curDiv.find('.branch_id').val();
	        $.ajax({
	            url : "ajax/get_pending_payments.php",
    	        type : "post",
    	        dataType : 'json',
    	        data : {action : 'apply_pending_payment', client_id : clientid, inv_id : inv_id, method : method, amount : pay_amount, branch_id : branch_id},
    	        success : function(res){
    	            if(res.status == '1'){
    	                toastr.success('Amount paid successfully.');
    	                if(parseFloat(pend) > parseFloat(pay_amount)){
    	                    curDiv.find('.pri').text(parseFloat(pend)-parseFloat(pay_amount));
    	                    curDiv.find('.pendtotal').val(parseFloat(pend)-parseFloat(pay_amount));
    	                    var pending = parseFloat(pend)-parseFloat(pay_amount);
    	                    curDiv.find('.amtpay').val('0');
    	                    curDiv.find('.amtpay').attr('onkeyup','maxpendpayment('+pending+',this.value, $(this))');
    	                    curDiv.find('.amtpay').attr('onblur','maxpendpayment('+pending+',this.value, $(this))');
    	                    curDiv.find('.amtpay').val('0');
    	                    curDiv.find('.mthd').val('1');
    	                    
    	                    clientView(clientid);
    	                } else {
    	                    clientView(clientid);
    	                    curDiv.remove();
    	                }
    	                var size = $('#ppaymentModal table tbody tr').length;
    	                if(size <= 0){
    	                    $('#ppaymentModal').modal('hide');
    	                }
    	            } else {
    	                toastr.warning('Amount not paid, please try again.');
    	            }
    	        }
	        });
	    }
	}
	function pendingPaymentSingleInvoice(div){
	    var curDiv = div.parent().parent();
	    var payamount = curDiv.find('.amtpay').val();
	    if(payamount == '0'){
	        curDiv.find('.amtpay').addClass('invalid');
	    } else {
	        curDiv.find('.amtpay').removeClass('invalid');
	        var pay_amount = curDiv.find('.amtpay').val();
	        var clientid = curDiv.find('.clientid').val();
	        var inv_id = curDiv.find('.inv_id').val();
	        var method = curDiv.find('.mthd').val();
	        var pend = curDiv.find('.pendtotal').val();
	        var branch_id = curDiv.find('.branch_id').val();
	        $.ajax({
	            url : "ajax/get_pending_payments.php",
    	        type : "post",
    	        dataType : 'json',
    	        data : {action : 'apply_pending_payment', client_id : clientid, inv_id : inv_id, method : method, amount : pay_amount, branch_id : branch_id},
    	        success : function(res){
    	            if(res.status == '1'){
    	                toastr.success('Amount paid successfully.');
    	                localStorage.setItem('invoice_paid', 'paid_success');
    	                window.location.href = 'clientprofile.php?cid='+clientid;
    	            } else {
    	                toastr.warning('Amount not paid, please try again.');
    	            }
    	        }
	        });
	    }
	}
	
	function contact_no_length(currDiv, phone_number){
	    var digit = phone_number.length;
	    if(digit > 0 && digit < <?= PHONE_NUMBER ?>){
	        currDiv.val('');
	        $('#digit_error').text('Please enter <?= PHONE_NUMBER ?> digit number.');
	    } else {
	        $('#digit_error').text('');
	    }
	}
	
	function othercontact(currDiv, phone_number){
	    var digit = phone_number.length;
	    if(digit > 0 && digit < <?= PHONE_NUMBER ?>){
	        currDiv.parent().find('input').val('');
	        currDiv.parent().find('.conterror').text('Please enter <?= PHONE_NUMBER ?> digit number.');
	    } else {
	        currDiv.parent().find('.conterror').text('');
	    } 
	}
	
	function maxcommissionper(currDiv, divVal){
	    if(divVal > 100){
	        currDiv.val('100');
	    }
	}
</script>

</body>
</html>

<!-- Payment Modal -->
<div id="paymentModal" class="modal fade" role="dialog">
	<div class="modal-dialog" style="width:1000px">
		
		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">Pending payments</h4>
			</div>
			<div class="modal-body">
				
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
			</div>
		</div>
		
	</div>
</div>


<!-- Package Modal -->
<div id="packageModal" class="modal disableOutsideClick fade" role="dialog">
	<div class="modal-dialog modal-lg">		
		<!-- Modal content-->
		<div class="modal-content">
			
		</div>
		
	</div>
</div>

<!-- Refferal Modal -->
<div id="refModal" class="modal fade" role="dialog">
	<div class="modal-dialog">		
		<!-- Modal content-->
		<div class="modal-content">
			
		</div>
		
	</div>
</div>	

<!-- Package Modal -->
<div id="ppaymentModal" class="modal disableOutsideClick fade" role="dialog">
	<div class="modal-dialog modal-lg">		
		<!-- Modal content-->
		<div class="modal-content">
			
		</div>
		
	</div>
</div>
