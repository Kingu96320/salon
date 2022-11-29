<?php
	include "./includes/db_include.php";
	$branch_id = $_SESSION['branch_id'];
	if($_SESSION['u_role'] == '3'){
		$name = $_SESSION['name'];
		$sql="SELECT id FROM `beauticians` where `active`='0' and name='$name' and branch_id='".$branch_id."'";
		$service_provider_id = query_by_id($sql,[],$conn)[0];
	}
	
	$uid = $_SESSION['uid'];
	


	include "topbar.php";
	
	include "header.php";
	include "menu.php";
	
	if(isset($_POST['submit'])){
	    $client         = addslashes(trim($_POST['clientid'])); // client id
		$name           = addslashes(trim($_POST['client'])); // client name
		$cont           = addslashes(trim($_POST['cont'])); // client contact number
		$gender         = addslashes(trim($_POST['gender']));
		$dob            = addslashes(trim($_POST['dob']));
		$time 	        = addslashes(trim(date('H:i',strtotime($_POST['time'])))); // appointment time
		if($client == ''){
    		$client=get_insert_id("INSERT INTO `client` set `name`=:name,`cont`=:cont,`gst`=:gst,`gender`=:gender,`dob`=:dob,`aniversary`=:aniversary,`leadsource`=:leadsource, `active`=:active, `branch_id`='".$branch_id."'",['name'=>$name,'cont'  =>$cont,'gst'=>'','gender'=>$gender,'dob'=>'','aniversary'=>'','leadsource'=>'Walk-In','active'=>0],$conn);
    		query("INSERT INTO wallet SET iid='0', date = '".date('Y-m-d')."', client_id = '$client', wallet_amount='0', get_wallet_from='', status='1', branch_id='".$branch_id."'",[],$conn);
		} else {
			query("UPDATE `client` set `gender`=:gender where id=:id and branch_id='".$branch_id."'",['gender'=>$gender, 'id'=>$client],$conn);
		} 
	
		$doa    = $_POST['doa'];
		$appdate = date('Y-m-d');
		
		$gtime  = $_POST['time'];   // appointment time
		$drr 	= 0;
        
        $total = $_POST['total_amount'];
		$aid = get_insert_id("INSERT INTO `app_invoice_".$branch_id."`(`client`,`doa`,`itime`,`role`,`dis`,`disper`,`tax`,`taxtype`,`pay_method`,`total`,`subtotal`,`bmethod`,`paid`,`due`,`notes`,`type`, `status`,`details`,`appdate`,`active`,`appuid`,`branch_id`) VALUES ('$client','$doa','$time','2','".CURRENCY.",0','0','0','3','0','$total','$total','0','0','$total','','1','Pending','','$appdate',0,'$uid','$branch_id')",[],$conn);
		$gtime = $time;

		for($t=0;$t<count($_POST["services"]);$t++){
			$ser = addslashes(trim($_POST["service"][$t]));
			$prc = $_POST["price"][$t];
			$qt  = 1;
			$dur = $_POST["durr"][$t];
			
			$ser_stime = $_POST["ser_stime"][$t];
			$ser_etime = $_POST["ser_etime"][$t];
			
			$serr = explode(",", $ser); 
			if($serr[0]=="sr"){
				$serr[0] = "Service";
			}

			$app_inv_item_id = get_insert_id("INSERT INTO `app_invoice_items_".$branch_id."` set `iid`='$aid',`client`='$client',`service`='$ser', `package_service_id`='0',`quantity`='$qt',`staffid`='0',`disc_row`='".CURRENCY.",0',`price`='$prc',`type`='Service',`start_time`='$ser_stime',`end_time`='$ser_etime',`app_date`='$doa',`active`=0, `branch_id`='".$branch_id."'",[],$conn);    
			
			$dur = $_POST["durr"][$t];
			$drr = $drr + $dur * 60;
			$gtime = strtotime($_POST['time']) + $drr;
			$gtime = date('H:i', $gtime);
			
			$ser_cat_id=$_POST['ser_cat_id'][$t];	
			for($j=0;$j<count($_POST["staffid".$t.""]);$j++){

				$staffid = $_POST["staffid".$t.""][$j];
				$inv_item_id = get_insert_id("INSERT INTO `app_multi_service_provider` set `iid`='$aid',`aii_staffid`='$app_inv_item_id',`service_cat`='',`service_name`='$ser ',`service_provider`='$staffid',`status`='1', `branch_id`='".$branch_id."'",[],$conn);
			}		
		}
		
		
		$sms_data = array(
	        'name' => $name,
	        'date' => date('d-m-Y',strtotime($doa)),
	        'time' => date('h:i a',strtotime(explode(" ",$_POST['ser_stime'][0])[1])),
	        'salon_name' => systemname()
	    );
		send_sms($cont,$sms_data,'appointment_booking_software');
	 
		$_SESSION['t']  = 1;
		$_SESSION['tmsg']  = "Appointment Saved Successfully";
		echo '<meta http-equiv="refresh" content="0; url=dashboard.php" />';die(); 
	}
	
?>
<style type="text/css">
	.fc-time-grid-container{
		height: auto!important;
	}
	.fc-content{
	    cursor: pointer;
	}
	.fc-today-button, .fc-axis, .fc-resourceTimeGridDay-button, .fc-resourceTimeGridWeek-button, .fc-resourceDayGridMonth-button{
	   text-transform: capitalize;
	}
	.ui-autocomplete{
	    z-index: 9999;
	}
	.fc-time-grid-event{
	    min-height: 15px;
	}
	/*for adding horizontal scrollbar to calendar and making position sticky of timeline*/
	.fc-view {
    overflow-x: auto;
	 }
	
	.fc-view > table {
	   min-width: 100%;
	   width:1300px;
	 }
	      
	.fc-time-grid .fc-slats {
	    /*z-index: 4;
	    pointer-events: none;*/
	 }
	      
	.fc-scroller.fc-time-grid-container {
	    overflow: initial !important;
	 }
	      
	.fc-axis {
	    position: sticky;
	    left: 0;
	    background: white;
	 }
	 /*end*/
</style>
<!-- Navbar ends -->
<!-- Dashboard wrapper starts -->
<div class="dashboard-wrapper">
	
	<!-- Main container starts -->
	<div class="main-container">
		
		<!-- Row starts -->
		<div class="row gutter">
			<?php if ($_SESSION['u_role']!=3) { ?>
				<div class="col-lg-3 col-md-6 col-sm-6" >
					<a href="javascript:void(0)" id="sales"><div class="mini-widget">
						<div class="mini-widget-heading clearfix" >
							<div class="pull-left">Today Sales</div>
							<!-- <div class="pull-right"><i class="icon-arrow-<?php echo $arr; ?>-right2"></i> <?php echo intval($sper); ?>%</div> -->
						</div>
						<div class="mini-widget-body clearfix" id="salesDashboardLoader">
							<div class="pull-left">
								<i class="icon-credit-card"></i>
							</div>
							<div class="pull-right number"><?=CURRENCY?> <span id='todaysalesValue'></span></div>
						</div>
					</div></a>
				</div><?php } ?>
				<input type="hidden" value="<?=date('Y-m-d')?>" id="fromDate"/>
				<input type="hidden" value="<?=date('Y-m-d')?>" id="toDate"/>
				<div class="col-lg-3 col-md-6 col-sm-6">
					<a id="appointment"><div class="mini-widget red">
						<div class="mini-widget-heading clearfix">
							<div class="pull-left">Today Appointments</div>
							<!-- <div class="pull-right"><i class="icon-arrow-<?php echo $arr; ?>-right2"></i><?php echo intval($tper); ?>%</div> -->
						</div>
						
						<div class="mini-widget-body clearfix" id="appointmentDashboardLoader">
							<div class="pull-left">
								<i class="icon-perm_phone_msg"></i>
							</div>
							<div class="pull-right number"><span id='todayappointmentValue'></span></div>
						</div>
					</div></a>
				</div>
				<?php if ($_SESSION['u_role']!=3) { ?>
					<div class="col-lg-3 col-md-6 col-sm-6">
						<a href="javascript:void(0)" id="enquiry"><div class="mini-widget grey">
							<div class="mini-widget-heading clearfix">
								<div class="pull-left">Today Enquiry</div>
								<!-- <div class="pull-right"><i class="icon-arrow-<?php echo $arr; ?>-right2"></i><?php echo intval($eper); ?>%</div> -->
							</div>
							<div class="mini-widget-body clearfix" id="enquiryDashboardLoader">
								<div class="pull-left">
									<i class="icon-add-user"></i>
								</div>
								<div class="pull-right number"><span id='enquiryValue'></span></div>
							</div>
						</div></a>
					</div>
					<div class="col-lg-3 col-md-6 col-sm-6">
						<a id="clients"><div class="mini-widget green">
							<div class="mini-widget-heading clearfix">
								<div class="pull-left">Clients Visit</div>
								<!-- <div class="pull-right"><i class="icon-arrow-<?php echo $arr; ?>-right2"></i> <?php echo intval($vper); ?>%</div> -->
							</div>
							<div class="mini-widget-body clearfix" id="clientsDashboardLoader">
								<div class="pull-left">
									<i class="icon-emoji-happy"></i>
								</div>
								<div class="pull-right number"><span id='clientsValue'></span></div>
							</div>
						</div></a>
					</div><?php } ?>
				</div>
        		<div id="todaysalesDashboard"> 
        		</div> 
		
		
		<!-- Row ends -->
		
		
		
		<div id="calendar" class="fc-calendar"></div> 
		<!-- Row starts -->
	</div>
	<!-- Main container ends -->
	
</div>
<!-- Dashboard Wrapper End -->

</div>
<!-- Container fluid ends -->

<?php 
	include "footer.php";
?>		

<script>
	
	
	$(document).ready(function(){
		var now = new Date();
		var startDate=$('#fromDate').val();
		var endDate  =$('#toDate').val()
		today_SAEC_values(startDate,endDate,'sales');
		today_SAEC_values(startDate,endDate,'appointment');
		today_SAEC_values(startDate,endDate,'enquiry');
		today_SAEC_values(startDate,endDate,'clients');
		$('#sales,#appointment,#enquiry,#clients').on('click',function(){
			var type=$(this).attr('id');
			infoWithFilter(startDate,endDate,type);
			today_SAEC_values(startDate,endDate,type);
		});
		
		
		autocomplete_serr();
		
	});
	
	function dateFilter(date,type){
		// var type=$(this).attr('id');
		var date = date.split("-");
		if(date == ''){
			var startDate = '';
			var endDate = '';
		} else {
			var startDate = isoDate(date[0]);
			var endDate = isoDate(date[1]);
		}
		infoWithFilter(startDate,endDate,type);
		today_SAEC_values(startDate,endDate,type);
	}
	
	// jquery function to change daterange date in iso date standard  Y-m-d (2019-11-01)

	function isoDate(date){	
		var datespit = date.split('/');
		var day = datespit[1].replace(' ','');
		var month = datespit[0].replace(' ','');
		var year = datespit[2].replace(' ','');
		return year+'-'+month+'-'+day;
	}
	
	function today_SAEC_values(startDate,endDate,type){
		$.ajax({
			beforeSend: function () {
				if(type==='sales'){
					$('#salesDashboardLoader').append('<div class="divloader" style="margin-top:0px !important;padding:0px"><div class="divloader_ajax_small"></div></div>');
					}else if(type === 'appointment'){
					$('#appointmentDashboardLoader').append('<div class="divloader" style="margin-top:0px !important;padding:0px"><div class="divloader_ajax_small"></div></div>');
					}else if(type === 'enquiry'){
					$('#enquiryDashboardLoader').append('<div class="divloader" style="margin-top:0px !important;padding:0px"><div class="divloader_ajax_small"></div></div>');
					}else if(type === 'clients'){
					$('#clientsDashboardLoader').append('<div class="divloader" style="margin-top:0px !important;padding:0px"><div class="divloader_ajax_small"></div></div>');
				}	
			},
			url: "ajax/today_SAEC_values.php?startDate="+startDate+"&endDate="+endDate+"&type="+type,
			type: "POST",
			success:function(data){
				var jsonData=JSON.parse(data);
				$("#todaysalesValue").html(jsonData['salesTotal']);
				$("#todayappointmentValue").html(jsonData['appTotal']);
				$("#enquiryValue").html(jsonData['enquiryTotal']);
				$("#clientsValue").html(jsonData['clintsvisitTotal']);
				$('.divloader').fadeOut();
			},
			error:function (){}
		});
		
	}
	
	function infoWithFilter(startDate,endDate,type){
		
		$.ajax({
			url: "ajax/infoWithFilters.php?startDate="+startDate+"&endDate="+endDate+"&type="+type,
			type: "POST",
			success:function(data){
				$("#todaysalesDashboard").html(data);
			},
			error:function (){}
		});
	}
	
	
	/* initialize the calendar */
	
	$(function() {
		
		//####################Random color generator###########
		function getRandomColor() {
		    var colors = ['#FC3105','#BD2707','#F5A906','#35F506','#06F547','#06F59E','#06D8F5','#06B0F5','#0681F5','#0656F5','#0635F5','#0609F5','#4B06F5','#8106F5','#B406F5','#E706F5','#F506CD','#F50664','#F50647','#F5062A','#940000','#945100','#947E00','#5C9400','#229400','#00941D','#009469','#007594','#004F94','#002F94','#001694','#070094','#2B0094','#430094','#700094','#890094','#940090','#94006E','#940051','#940024','#940000','#A33E3E','#A34A3E','#FF0000','#FF4900','#FF7400','#FF9B00','#00FF36','#0070FF','#005DFF','#0032FF','#0C00FF','#4D00FF','#8000FF','#B600FF','#E400FF','#FF009E','#FF0055','#FF002E'];
		    return colors[Math.floor(Math.random() * colors.length)]
		} //####End####
		
		var date = new Date();
		var d = date.getDate();
		var m = date.getMonth();
		var y = date.getFullYear();
		var calendarEl = document.getElementById('calendar');
		var calendar = new FullCalendar.Calendar(calendarEl, {
          schedulerLicenseKey: 'GPL-My-Project-Is-Open-Source',
          plugins: ['interaction','resourceTimeGrid','resourceDayGrid','timeGrid'],  
          defaultView: 'resourceTimeGridDay',
          eventClick: function(calEvent) {
          	appointment(calEvent.event.id,calEvent.event.groupId);
    	  },
          header: {
            left: 'prev,next today',
            center: 'title',
            right: 'resourceTimeGridDay,resourceTimeGridWeek,resourceDayGridMonth'
          },
          defaultDate: '<?= date('Y-m-d'); ?>',
          navLinks: true, // can click day/week names to navigate views
          editable: false,
          eventLimit: true, // allow "more" link when too many events
          nowIndicator: true,
          minTime: '<?= shopopentime() ?>',
    	  maxTime: '<?= shopclosetime() ?>',
    //       minTime: '00:00:00',
    // 	  maxTime: '24:00:00',

          resources: [
    		<?php 
    			$sp_id = $service_provider_id['id'];
    			$srp_id ="";
    			$s_id ="";
    			if($sp_id > 0){
    				$srp_id="and amsp.service_provider='$sp_id'";
    				$s_id = "and id='$sp_id'";
    			}	
    			$col_code = array("0"=>"pink-bg","1"=>"green-bg","2"=>"blue-bg","3"=>"red-bg","4"=>"yellow-bg","5"=>"tw-bg","6"=>"fb-bg","7"=>"purple-bg","8"=>"black-bg");
    			$array_beauty =query_by_id("select b.id,b.name from beauticians b where active='0' and branch_id='".$branch_id."' ".$s_id,[],$conn);
    			$count = count($array_beauty);//used in set_calendar_width()
    			foreach($array_beauty as $value) { 
    				echo "{ id: '".$value['id']."', title: '".$value['name']."',eventColor: getRandomColor() },";
    			}
    		?>
    	  ],
          <?php 
    		$date = date('Y-m-d'); 
    		$array1 =query_by_id("SELECT aii.id as aid, CONCAT(aii.start_time) as start_time,aii.end_time as end_time,c.name as client,amsp.service_name,amsp.service_provider as service_provider,amsp.service_cat,amsp.id,amsp.iid,amsp.aii_staffid,SUBSTRING_INDEX(amsp.updated_date,' ',1) as doa,SUBSTRING_INDEX(amsp.updated_date,' ',-1) as itime FROM `app_multi_service_provider` amsp "
    		." LEFT JOIN `app_invoice_items_".$branch_id."` aii on aii.id=amsp.aii_staffid "
    		." LEFT JOIN `client` c on c.id=aii.client "
    		." LEFT JOIN `app_invoice_".$branch_id."` ai on ai.id=aii.iid "
    		." WHERE amsp.status='1' and aii.active='0' and ai.active='0' and ai.bill_created_status='0' and ai.status!='Cancelled' and amsp.branch_id='".$branch_id."' ".$srp_id."",[],$conn);
    	
    		
    		$date11 = date('Y-m-d'); 
    	
    		$array2 =query_by_id("SELECT va.*,va.id as vaid, va.inv_id as iid1,va.room_id as room_id, va.allocated_by as service_provider1, c.*, c.name as client1, ai.*,vr.*, vr.room_name as roomname from vip_appointment va "
    		."LEFT JOIN `vip_rooms` vr on vr.id = va.room_id"
    		." LEFT JOIN `client` c on c.id= va.allocated_for "
    		." LEFT JOIN `app_invoice_".$branch_id."` ai on ai.id=va.inv_id"
    		." WHERE ai.active='0' and ai.bill_created_status='0' and ai.appdate = '".$date11."'",[],$conn);
    	    
    		?>
    		
          events: [
            <?php
    			foreach ($array1 as $value1) { 
    				for($i=0;$i<=sizeof(explode(',' ,$value1['service_provider']));$i++) {
    					echo "{
    					id: '".$value1['aid']."', 
    					resourceId: '".explode(',' ,$value1['service_provider'])[$i]."',
    					groupId: '".$value1['iid']."', 
    					title: '".ucwords(strtolower($value1['client']))."',
    					start: '".date('Y-m-d H:i:s',strtotime(current(EXPLODE(",",$value1['start_time']))))."',
    					end	: '".date('Y-m-d H:i:s',strtotime(end(EXPLODE(",",$value1['end_time']))))."',
    					roomid :'',
    					},";
    				} 
    			}
    			
    			foreach ($array2 as $value11) { 
    			   
    				// for($i=0;$i<=sizeof($value11['service_provider1']);$i++) {
    					echo "{
    					id: '".$value11['vaid']."', 
    					resourceId: '".$value11['service_provider1']."',
    					groupId: '".$value11['iid1']."', 
    					roomid : '".$value11['room_id']."',
    					title: '".ucwords(strtolower($value11['roomname']))."',
    					start: '".date('Y-m-d H:i:s',strtotime($value11['app_start_time']))."',
    					end	: '".date('Y-m-d H:i:s',strtotime($value11['app_end_time']))."',
    				
    					},";
    				// } 
    			}
    			
    			
    		?>
          ],
          selectAllow: function(select) {
              return moment().diff(select.start) <= 0
          },
          selectable: true,
          selectHelper: true,
          select: function (event) {
              var provider_id = event.resource.id;
              var start_time = event.startStr;
              start_time = start_time.split('T');
              var date = start_time[0];
              start_time = date+" "+start_time[1].split('+')[0];
              var end_time = event.endStr;
              end_time = end_time.split('T');
              end_time = date+" "+end_time[1].split('+')[0];
              var curr_time = '<?= date('H:i:s') ?>';
              if(provider_id != ''){
                  $.ajax({
                    url: 'ajax/appointment_stafftime.php',
                    type: "POST",
        			data: {id : provider_id, date : date, time: curr_time, starttime : start_time, endtime : end_time},
        			dataType: 'json',
    			    beforeSend : function(){
        			    $('.loader-gif').show();
        			},
        			success: function(response) {
        				var ds = response;
        				if(ds['success']=='0'){
                            $('.staff').html('<option value="">--Select--</option><option value="'+ds['data']['pid']+'">'+ds['data']['pname']+'</option>');
                            $('.start_time').val(ds['data']['start']);
    						$('#book_appointment_modal').modal('show');
    						$('#date').val(date);
    						$('#time').val(ds['data']['start']);
    						$('.loader-gif').hide();
    					} else if(ds['success']=='1'){
    						toastr.error(ds['data']['spcat']+' Unavailable.');
    						calendar.render();
    						set_calendar_width();
    						$('.loader-gif').hide();
    					} else if(ds['success']=='2'){
    						toastr.error(ds['data']['spcat']+' Unavailable.');
    						calendar.render();
    						set_calendar_width();
    						$('.loader-gif').hide();
    					}
        			}
                  });
              }
          },
        });
    
        calendar.render();
	});	
	
	function autocomplete_serr(){
		$(".ser").autocomplete({ 
			source: function(request, response) {
				var ser_stime = '';
				if($(this.element).parent().parent().parent().parent().parent().parent().attr('id')=='TextBoxContainer'){
					ser_stime = $('#date').val()+' '+$('#time').val();
					}else{
					ser_stime = $(this.element).parent().parent().parent().parent().parent().parent().prev('tr').find('.ser_etime').val();
				}
				$.getJSON("ajax/bill.php", { term: request.term,ser_cat_id: $(this.element).parent().parent().find('.ser_cat_id').val(),ser_stime:ser_stime,page_info:'app' }, response);
			},
			minLength: 1,
			select:function (event, ui) { 
				var appo_time = $('#time').val();
				var appo_date = $('#date').val();
				if(appo_time == ''){
					$('#time').addClass('invalid');
				} else if(appo_date == ''){
					$('#date').addClass('invalid');
					$('#time').removeClass('invalid');
				} else{
				
					$('#date').removeClass('invalid');
					var etime = Date.parse('20 Aug 2000 '+$('#close_time').val());
					var setime = Date.parse('20 Aug 2000 '+ui.item.ser_etime.split(' ')[1]+':00');
					var et_status = '<?= extratimeStatus(); ?>'; // Extra time status
					if(setime > etime && et_status == '0'){	
						// var row = $(this).parent().parent().parent().parent().parent().parent();
						var row = $(this).parent().parent();
						row.find('input[type="text"].ser').val('');
						row.find('.serr').val('');
						// row.find('.pa_ser').val('');
						row.find('.prr').val('');
						row.find('.qt').val('0');
						row.find('.disc_row ').val('0');
						row.find('.duration').val('');
						row.find('.ser_stime').val('');
						row.find('.ser_etime').val('');
						row.find('.start_time').val(('').substring(11));
						row.find('.end_time').val(('').substring(11));
			
						toastr.error('Appointment can\'t book for this service. salon will close at '+onTimeChange($('#close_time').val()));
					} else {
						// var row = $(this).parent().parent().parent().parent().parent().parent();
						var row = $(this).parent().parent();
						row.find('.serr').val(ui.item.id);
						row.find('.prr').val(ui.item.price);
						row.find('.qt').val('1');
						row.find('.disc_row ').val('0');
						row.find('.duration').val(ui.item.duration);
						row.find('.ser_stime').val(ui.item.ser_stime);
						row.find('.ser_etime').val(ui.item.ser_etime);
						row.find('.start_time').val(onTimeChange((ui.item.ser_stime).substring(11)));
						row.find('.end_time').val(onTimeChange((ui.item.ser_etime).substring(11)));
						appointmenttime();
						var clientId = $('#clientid').val();
			
						price_calculate(row);
						sumup();
						// paymode($('.act').val());
					}
				}
			}
		});	
	}
	
	function appointmenttime(){
		
		var start_arr=[];	
		var end_arr  =[];
		var e=$('.TextBoxContainer');
		// e.find('.staff option[value=""]').prop('selected',true); 
		
		var date=$('#date').val();
		var duration = parseInt(e.find('.duration').val()); //duration
		
		var ser_stime=$('#time').val();

		e.find('.ser_stime').val(date+" "+time12to24(ser_stime));
		e.find('.start_time').val(ser_stime);
		
		var ser_stime1=e.find('.ser_stime').val();
		var da = new Date(ser_stime1.replace(/-/g, '/'));
		
		var new_endtime= new Date(da.getTime() + (duration * 60 * 1000));
		
		var final_atime=new_endtime.getFullYear() + '-' +('0' + (new_endtime.getMonth()+1)).slice(-2)+ '-' +  addZero(new_endtime.getDate()) + ' '+addZero(new_endtime.getHours())+ ':'+('00' + (new_endtime.getMinutes())).slice(-2)+ ':'+new_endtime.getSeconds()+'0';
		
		e.find('.ser_etime').val(final_atime);
		e.find('.end_time').val(onTimeChange(final_atime.substr(11)));
		
		var start = e.nextAll('tr').find('.ser_etime').length;
		var index_value=$('.TextBoxContainer').index();
		
		for(var i=0;i<start;i++){ 
			
			var prev_start_time = $(".ser_stime:eq("+(i+index_value)+")").val();
			
			var prev_stime = new Date(prev_start_time.replace(/-/g, '/'));
			var prev_duration = $('.duration:eq('+(i+index_value)+')').val(); //prev_duration
			
			var prev_starttime= new Date(prev_stime.getTime() + (prev_duration * 60 * 1000));
			
			var final_atime=prev_starttime.getFullYear() + '-' +('0' + (prev_starttime.getMonth()+1)).slice(-2)+ '-' +  addZero(prev_starttime.getDate()) + ' '+addZero(prev_starttime.getHours())+ ':'+('00' + (prev_starttime.getMinutes())).slice(-2)+ ':'+prev_starttime.getSeconds()+'0';
			
			var next_start_time = $(".ser_etime:eq("+(i+index_value)+")").val(); 
			
			var next_stime = new Date(next_start_time.replace(/-/g, '/'));
			var next_duration = $('.duration:eq('+(i+index_value+1)+')').val(); //next_duration
			var next_starttime= new Date(next_stime.getTime() + (next_duration * 60 * 1000));
			
			var final_etime=next_starttime.getFullYear() + '-' +('0' + (next_starttime.getMonth()+1)).slice(-2)+ '-' +  addZero(next_starttime.getDate()) + ' '+addZero(next_starttime.getHours())+ ':'+('00' + (next_starttime.getMinutes())).slice(-2)+ ':'+next_starttime.getSeconds()+'0';
			
			start_arr.push(final_atime);
			end_arr.push(final_etime);  
			
			$(".end_time:eq("+(i+index_value+1)+")").val(onTimeChange((end_arr[i]).substring(11))); 
			$(".ser_etime:eq("+(i+index_value+1)+")").val(end_arr[i]); 
			$(".start_time:eq("+(i+index_value+1)+")").val(onTimeChange((start_arr[i]).substring(11))); 
			$(".ser_stime:eq("+(i+index_value+1)+")").val(start_arr[i]); 
		}
	}
	
	function check() {
		$client_id = $('#clientid').val();				
		jQuery.ajax({
			url: "checkccont.php?p="+$("#cont").val(),
			type: "POST",
			success:function(data){
				
				if(data == '1'){
					if($client_id == ''){
						$("#client-status").html("Contact number already exists");
						$('#cont').val("");
						$('#dob').val("");
						$('#aniv').val("");
					}
				}else{
					$("#client-status").html("");
					$("#clientid").val("");
					$('#dob').val("");
					$('#aniv').val("");
				}
			},
			error:function (){}
		});
	}
	
	function servicestarttime(time, current){
		var e = current;
		var date = $('#date').val();
		if(date != ''){
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
							
						} else {
							if(cpmtime < stime || cpmtime > etime){
								e.parents('tr').find('.start_time').val('');
								e.parents('tr').find('.ser_etime').val('');
								e.parents('tr').find('.end_time').val('');
							} else {
								
							var start_arr=[];	
							var end_arr  =[];
							
							e.parents('tr').find('.staff option[value=""]').prop('selected',true); 
							
							var date=$('#date').val();
							var duration = parseInt(e.parents('tr').find('.duration').val()); //duration
							var ser_stime=e.parents('tr').find('.start_time').val();
							
							e.parents('tr').find('.ser_stime').val(date+" "+time12to24(ser_stime));
							
							var ser_stime1=e.parents('tr').find('.ser_stime').val();
							var da = new Date(ser_stime1.replace(/-/g, '/'));
							
							var new_endtime= new Date(da.getTime() + (duration * 60 * 1000));
							if(!isNaN(duration)){
								var final_atime=new_endtime.getFullYear() + '-' +('0' + (new_endtime.getMonth()+1)).slice(-2)+ '-' +  addZero(new_endtime.getDate()) + ' '+(new_endtime.getHours()<10?'0':'')+new_endtime.getHours()+ ':'+(new_endtime.getMinutes()<10?'0':'')+new_endtime.getMinutes()+ ':'+new_endtime.getSeconds()+'0';
							} else {
								var final_atime = '2000-08-20'+' '+time12to24(ser_stime);
							}
							
							
							e.parents('tr').find('.ser_etime').val(final_atime);
							e.parents('tr').find('.end_time').val(onTimeChange(final_atime.substr(11)));
							
							var start = e.parents('tr').nextAll('tr').find('.ser_etime').length;
							
							var index_value=e.parents('.TextBoxContainer').index();
							
							
							for(var i=0;i<start;i++){ 
								$(".staff option[value='']:eq("+(i+index_value+1)+")").prop('selected',true);
								
								var prev_start_time = $(".ser_stime:eq("+(i+index_value)+")").val();
								var prev_stime = new Date(prev_start_time.replace(/-/g, '/'));
								var prev_duration = $('.duration:eq('+(i+index_value)+')').val(); //prev_duration
								
								
								var prev_starttime= new Date(prev_stime.getTime() + (prev_duration * 60 * 1000));
								var final_atime=prev_starttime.getFullYear() + '-' +('0' + (prev_starttime.getMonth()+1)).slice(-2)+ '-' +  addZero(prev_starttime.getDate()) + ' '+addZero(prev_starttime.getHours())+ ':'+('00' + (prev_starttime.getMinutes())).slice(-2)+ ':'+prev_starttime.getSeconds()+'0';
								
								var next_start_time = $(".ser_stime:eq("+(i+index_value+1)+")").val(); 
								var next_stime = new Date(next_start_time.replace(/-/g, '/'));
								var next_duration = $('.duration:eq('+(i+index_value+1)+')').val(); //next_duration
								var next_starttime= new Date(next_stime.getTime() + (next_duration * 60 * 1000));
								var final_etime=next_starttime.getFullYear() + '-' +('0' + (next_starttime.getMonth()+1)).slice(-2)+ '-' +  addZero(next_starttime.getDate()) + ' '+time12to24(addZero(next_starttime.getHours())+ ':'+('00' + (next_starttime.getMinutes())).slice(-2)+ ':'+next_starttime.getSeconds());
								
								start_arr.push(final_atime);
								end_arr.push(final_etime);  
								
								$(".end_time:eq("+(i+index_value+1)+")").val((end_arr[i]).substring(11)); 
								$(".ser_etime:eq("+(i+index_value+1)+")").val(end_arr[i]); 
								$(".start_time:eq("+(i+index_value+1)+")").val((start_arr[i]).substring(11)); 
								$(".ser_stime:eq("+(i+index_value+1)+")").val(start_arr[i]); 
							}
							}
						}
	               	} else {
	               		current.val('');
	               	}
	            }
	       	});
		} else {
			current.val('');
			$('#date').addClass('invalid');
		}
		
	}
	
	function addZero(i) {
		if (i < 10) {
			i = "0" + i;
		}
		return i;
	}
	
	function price_calculate(row){
		var pr = row.find('.prr').val();
		var qt = row.find('.qt').val();
		var sum = pr * 1;
		row.find('.price').val(sum);
		var pric = 0;
		var sums = 0;
		var sump = 0;
		var sumt = 0;
		var sum  = 0;
		var ids  = $(".serr");
		
		
		var inputs = $(".price");
		for(var i = 0; i < inputs.length; i++){
			var service = $(ids[i]).val().split(',');
			if(service[0]=="sr"){
				sums = sums + parseFloat($(inputs[i]).val());
			}
			else if(service[0]=="pr"){
				sump = sump + parseFloat($(inputs[i]).val());
			}
			sum = parseFloat(sum) + parseFloat($(inputs[i]).val());
			$("#sum").html("Rs. "+sum.toFixed(2));
		}	
	}
	
	function sumup(){
    	var pric = 0;
    	var sums = 0;
    	var sump = 0;
    	var sumt = 0;
    	var sum = 0;
    	var ids = $(".serr");
    	
    	var inputs = $(".price");
    	for(var i = 0; i < inputs.length; i++){
    		var service = $(ids[i]).val().split(',');
    		if(service[0]=="sr"){
    			sums = sums + parseFloat($(inputs[i]).val());
    		}
    		else if(service[0]=="pr"){
    			sump = sump + parseFloat($(inputs[i]).val());
    		}
    		sum = parseFloat(sum) + parseFloat($(inputs[i]).val());
    		sum = sum || 0;

    		$("#total").val(sum);
    		$(".total-amount span").text(sum);
    	}
    }
    
    /* time changing function*/
	function change_timing(e){
		var start_arr=[];	
		var end_arr  =[];

		var ser_stime = e.parents('tr').find('.ser_stime').val(); //start time
		var ser_etime = e.parents('tr').find('.ser_etime').val(); //end time
		var d2 = new Date(ser_stime.replace(/-/g, '/'));
		var d1 = new Date(ser_etime.replace(/-/g, '/'));
		var diff_minutes =  (d1- d2);
		
		var start = e.parents('tr').prevAll('tr').find('.ser_etime').length;
		var count = e.parents('tr').nextAll('tr').find('.ser_etime').length;
		var p_service = e.parents('tr').find('.pa_ser').val();
		if(p_service != ''){
			e.parents('tr').hide();
		} else {
			e.parents('tr').remove();
		}
		 
		
		var add_in_start  = $(".ser_etime:eq("+(start-1)+")").val();
		start_arr.push(add_in_start);
		
		
		for(var i=0;i<count;i++){	
			var add_in_end = $(".ser_etime:eq("+(i+start)+")").val();
			var add_in_start1  = $(".ser_stime:eq("+(i+start)+")").val();
			var d2 = new Date(add_in_end.replace(/-/g, '/'));
			var new_endtime= (new Date(d2 - diff_minutes));
			
			var final_etime=new_endtime.getFullYear() + '-' +('0' + (new_endtime.getMonth()+1)).slice(-2)+ '-' +  addZero(new_endtime.getDate()) + ' '+addZero(new_endtime.getHours())+ ':'+('00' + (new_endtime.getMinutes())).slice(-2)+ ':'+new_endtime.getSeconds()+'0';
			
			end_arr.push(final_etime);
			start_arr.push(final_etime); 
			$(".end_time:eq("+(i+start)+")").val((end_arr[i]).substring(11)); 
			$(".ser_etime:eq("+(i+start)+")").val(end_arr[i]); 
			$(".start_time:eq("+(i+start)+")").val((start_arr[i]).substring(11)); 
			$(".ser_stime:eq("+(i+start)+")").val(start_arr[i]);  
			
		}
		
	}
	
	function increment_ids(){
		var row_len=$('.TextBoxContainer');
		var i=0;
		row_len.each(function(){
			var a=$(this).find('.staff').attr('name','staffid'+i+'[]');
			i++; 
		});
	}
    
	
	var json_values = [];
	
	
	function sendaniv() {
		json_values = [];
		var count = $('input.ann:checked').length;
		if(count>0){
			$(".ann").each(function()
            {
				if($(this).is(':checked'))
                {
                    json_values.push({name: $(this).data("name"),contact: $(this).data("contact") });
				}
			});
			$('#myModal').modal('show');
			}else{
            alert('Please Select at least one Client'); 
		}
	}
	
	function sendbday() {
		json_values = [];
		var count = $('input.bday:checked').length;
		if(count>0){
			$(".bday").each(function()
            {
				if($(this).is(':checked'))
                {
                    json_values.push({name: $(this).data("name"),contact: $(this).data("contact") });
				}
			});
			$('#myModal').modal('show');
			}else{
            alert('Please Select at least one Client'); 
		}
	}
	
	function sendmessage() {
		var message = $('#message').val();
		$.ajax({
			type: "POST",
			url: 'ajax/sendsms.php',
			data: {
				json: JSON.stringify(json_values,true),
				message: message,
			},
			dataType: 'text',
			success: function(result) {
				toastr.success(result);
				$('#message').val('Hello {name}\nFrom {Salon}');
				$("input[type='checkbox']").attr("checked",false);
			}
		});
	}
	
	var table_print,table_print1,table_print2,table_print3;
	$(document).ready(function() {
	    
	    $("#btnAdd").bind("click", function() {
    		var empty_fields = [];
    		$('.ser').each(function(){
    			if($(this).val() == ''){
    				empty_fields.push('empty_field');
    			}
    		});
    		$('.start_time').each(function(){
    			if($(this).val() == ''){
    				empty_fields.push('empty_field');
    			}
    		});
    		$('.end_time').each(function(){
    			if($(this).val() == ''){
    				empty_fields.push('empty_field');
    			}
    		});
    		$('.price').each(function(){
    			if($(this).val() == ''){
    				empty_fields.push('empty_field');
    			}
    		});
    		$('.staff option:selected').each(function(){
    			if($(this).val() == ''){
    				empty_fields.push('empty_field');
    			}
    		});
    		if(empty_fields && empty_fields.length == 0){
    			var clonetr = $("#TextBoxContainer").clone().addClass('TextBoxContainer');
    			clonetr.removeAttr('id');
    			clonetr.find("table.add_row").remove();
    			clonetr.find('.sno').html('<span class="remm icon-trash2 " style="color:red;" onclick="change_timing($(this));sumup();"></span>');
    			clonetr.find('input').val('');
    			$("#addBefore").before(clonetr);
    			clonetr.find('.staff option[value=""]').prop('selected',true);
    			increment_ids();
    			formValidaiorns();
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
    
    			$('.ser').on('click',function(){
    				$(this).keydown();
    			});
    
    			$('.ser').on('keyup',function(){
    				if($(this).val().length > 0){
    					autocomplete_serr();
    				}
    			});
    		}
    	});
    	
	
		table_print1 = $('.tableprint1').DataTable( {
			dom: 'Bfrtip',
			"aaSorting":[],
			buttons: [	{
				extend: 'excelHtml5',
				text: '<i class="fa fa-file-excel-o"></i> Excel',
				titleAttr: 'Export to Excel',
				title: '<?php echo systemname(); ?>',
				exportOptions: {
					columns: ':not(:last-child)',
				}
			},
			{
				extend: 'csvHtml5',
				text: '<i class="fa fa-file-text-o"></i> CSV',
				titleAttr: 'CSV',
				title: '<?php echo systemname(); ?>',
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
				$("#smstab_wrapper").find('.pagination').append('<li class="paginate_button"><button class="btn-info" style="float : right;" id="chkk2_sms">Send SMS</button></li>');
			},
		} );
		$("#chkk2_sms").on('click',function(){
			json_values = [];
			var inc = 0;
			
			table_print1.rows().every( function ( rowIdx, tableLoop, rowLoop ) {
				var data = this.data();
				
				var row = table_print1.rows(rowIdx);
				var data1 = row.nodes();
				var contact_node = $(data1).find(".chkk2");
				if($(contact_node).prop("checked") == true){
					inc++;
					json_values.push({name: $(contact_node).data("name"),contact: $(contact_node).data("contact") });
				}
				
			} );
			if(inc>0){
				$('#myModal').modal('show');
				
				}else{
				alert('Please Select at least one client'); 
			}
		});
		var rows = table_print1.rows({ 'search': 'applied' }).nodes();
		
		$("#iregchk").click(function () {
			if($(this).prop("checked") == true){
				$('.chkk2', rows).prop('checked', true);
			}
			else if($(this).prop("checked") == false){
				$('.chkk2', rows).prop('checked', false);
			}
		});
	});
	
	$(document).ready(function() {
	    
	   // client auto-complete
	    
	    $(".client").autocomplete({
            source: "autocomplete/client.php",
            minLength: 1,
            select: function(event, ui) {
                event.preventDefault();
                $('#client').val(ui.item.name);
                $('#clientid').val(ui.item.id); 
                $('#client_branch_id').val(ui.item.client_branch_id); 
                $('#cont').val(ui.item.cont);
                $('#gender #gn-'+ui.item.gender).attr('selected', true);
        	}				
    	});
	
		table_print2 = $('.tableprint3').DataTable( {
			dom: 'Bfrtip',
			"ordering": false,
			"aaSorting":[],
			buttons: [	{
				extend: 'excelHtml5',
				text: '<i class="fa fa-file-excel-o"></i> Excel',
				titleAttr: 'Export to Excel',
				title: '<?php echo systemname(); ?>',
				exportOptions: {
					columns: ':not(:last-child)',
				}
			},
			{
				extend: 'csvHtml5',
				text: '<i class="fa fa-file-text-o"></i> CSV',
				titleAttr: 'CSV',
				title: '<?php echo systemname(); ?>',
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
				$("#smstab2_wrapper").find('.pagination').append('<li class="paginate_button"><button class="btn-info" style="float : right;" id="chkk_sms">Send SMS</button></li>');
			},
		} );
		$("#chkk_sms").on('click',function(){
			json_values = [];
			var inc = 0;
			
			table_print2.rows().every( function ( rowIdx, tableLoop, rowLoop ) {
				var data = this.data();
				
				var row = table_print2.rows(rowIdx);
				var data1 = row.nodes();
				var contact_node = $(data1).find(".chkk");
				if($(contact_node).prop("checked") == true){
					inc++;
					json_values.push({name: $(contact_node).data("name"),contact: $(contact_node).data("contact") });
				}
				
			} );
			if(inc>0){
				$('#myModal').modal('show');
				
				}else{
				alert('Please Select at least one client'); 
			}
		});
		var rows = table_print2.rows({ 'search': 'applied' }).nodes();
		
		$("#enqchk").click(function () {
			if($(this).prop("checked") == true){
				$('.chkk', rows).prop('checked', true);
			}
			else if($(this).prop("checked") == false){
				$('.chkk', rows).prop('checked', false);
			}
		});
	} );
	
	$(document).ready(function() {
	    formValidaiorns();
		table_print3 = $('.tableprint4').DataTable( {
			dom: 'Bfrtip',
			"aaSorting":[],
			buttons: [	{
				extend: 'excelHtml5',
				text: '<i class="fa fa-file-excel-o"></i> Excel',
				titleAttr: 'Export to Excel',
				title: '<?php echo systemname(); ?>',
				exportOptions: {
					columns: ':not(:last-child)',
				}
			},
			{
				extend: 'csvHtml5',
				text: '<i class="fa fa-file-text-o"></i> CSV',
				titleAttr: 'CSV',
				title: '<?php echo systemname(); ?>',
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
				$("#smstab3_wrapper").find('.pagination').append('<li class="paginate_button"><button class="btn-info" style="float : right;"  id="chkk3_sms">Send SMS</button></li>');
			},
		} );
		$("#chkk3_sms").on('click',function(){
			json_values = [];
			var inc = 0;
			
			table_print3.rows().every( function ( rowIdx, tableLoop, rowLoop ) {
				var data = this.data();
				
				var row = table_print3.rows(rowIdx);
				var data1 = row.nodes();
				var contact_node = $(data1).find(".chkk3");
				if($(contact_node).prop("checked") == true){
					inc++;
					json_values.push({name: $(contact_node).data("name"),contact: $(contact_node).data("contact") });
				}
				
			} );
			if(inc>0){
				$('#myModal').modal('show');
				
				}else{
				alert('Please Select at least one client'); 
			}
		});
		var rows = table_print3.rows({ 'search': 'applied' }).nodes();
		
		$("#regchk").click(function () {
			if($(this).prop("checked") == true){
				$('.chkk3', rows).prop('checked', true);
			}
			else if($(this).prop("checked") == false){
				$('.chkk3', rows).prop('checked', false);
			}
		});
		//function for changing calender width according to no.of service providers
		set_calendar_width();
	} );
	
	function set_calendar_width(){

		var no_of_providers = "<?= $count?>";
		if(no_of_providers>4){

			var table_width = 1300;
			var extra_width = (no_of_providers-4)*300;
			table_width += extra_width;
			$(".fc-view").find("table").width(table_width);
		}

		$(".fc-resourceTimeGridDay-button,.fc-resourceTimeGridWeek-button,.fc-resourceDayGridMonth-button").click(function(){
			if(no_of_providers>4){

				var table_width = 1300;
				var extra_width = (no_of_providers-4)*300;
				table_width += extra_width;
				$(".fc-view").find("table").width(table_width);
			}
		});
	}
	function sendsms(cls){
		json_values = [];
		var inc = 0;
		
		table_print.rows().every( function ( rowIdx, tableLoop, rowLoop ) {
			var data = this.data();
			
			var row = table_print.rows(rowIdx);
			var data1 = row.nodes();
			var contact_node = $(data1).find(cls);
			if($(contact_node).prop("checked") == true){
				inc++;
				json_values.push({name: $(contact_node).data("name"),contact: $(contact_node).data("contact") });
			}
			
		} );
		if(inc>0){
			$('#myModal').modal('show');
			
			}else{
			alert('Please Select at least one client'); 
		}
	}
	
    $(function () {
		
		$(".event-tag span").click(function () {
			$(".event-tag span").removeClass("selected");
			$(this).addClass("selected");
		});
		
		$(document).on('click', '.remove-event', function (e) {
			$(this).parent().remove();
		});
		
		
		/* initialize the external events */
		
		$('#external-events .fc-event').each(function () {
			
			// store data so the calendar knows to render an event upon drop
			$(this).data('eventObject', {
				title: $.trim($(this).text()),
				className: $(this).attr("data-bg"), // use the element's text as the event title
				stick: true // maintain when user navigates (see docs on the renderEvent method)
			});
			
			// make the event draggable using jQuery UI
			$(this).draggable({
				zIndex: 999,
				revert: true, // will cause the event to go back to its
				revertDuration: 0 //  original position after the drag
			});
			
		});
		
		
		
		
		
		/*Add new event*/
		// Form to add new event
		
		$("#createEvent").on('submit', function (ev) {
			ev.preventDefault();
			
			var $event = $(this).find('.new-event-form'),
			event_name = $event.val(),
			tagColor = $('.event-tag  span.selected').attr('data-tag');
			
			if (event_name.length >= 3) {
				
				var newid = "new" + "" + Math.random().toString(36).substring(7);
				// Create Event Entry
				$("#external-events .checkbox").before(
				'<div id="' + newid + '" class="fc-event ' + tagColor + '" data-bg="' + tagColor + '">' + event_name + '<span class="fa fa-close remove-event"></span></div>'
				);
				
				
				var eventObject = {
					title: $.trim($("#" + newid).text()),
					className: $("#" + newid).attr("data-bg"), // use the element's text as the event title
					stick: true
				};
				
				// store the Event Object in the DOM element so we can get to it later
				$("#" + newid).data('eventObject', eventObject);
				
				// Reset draggable
				$("#" + newid).draggable({
					revert: true,
					revertDuration: 0,
					zIndex: 999
				});
				
				// Reset input
				$event.val('').focus();
				} else {
				$event.focus();
			}
		});
	});
	
	$(document).on("change", '.staff', function() {
		
		<?php if(isset($_GET['id']) && $_GET['id'] > 0){  ?>
			var app_eid = <?=$_GET['id']?>
			<?php }else { ?>
			var app_eid  = 0;
		<?php } ?>

		
		staff = $(this).val();
		var durr  		= $(this).parent().parent().find('.durr').val();
		var starttime   = $(this).parents('tr').find('.ser_stime').val();
		var endtime     = $(this).parents('tr').find('.ser_etime').val();
		var select_staff= $(this).parents('tr').find('.staff option[value=""]');	
		
		date = $('#date').val();
		time = $('#time').val();
		var durr_plus = 0;
		var prev_rows = $(this).parent().parent().prevAll('tr');
        $(this).parent().parent().prevAll('tr').each(function(){
            durr_plus += parseInt($(this).find('.durr').val());
		});
		if(starttime !=''){
			$.ajax({
				url: "ajax/appointment_stafftime.php?id="+staff+"&date="+date+"&time="+time+"&starttime="+starttime+"&endtime="+endtime+"&app_eid="+app_eid,
				type: "POST",
				success:function(data){
		
					var durr_count = 0;
					var ds = JSON.parse(data.trim());
					starttime = ds['start'];
					endtime = ds['end'];
					var ds = JSON.parse(data.trim());
					if(ds['success']=='0'){
						toastr.success(ds['data']['spcat']+' Available.');
						}else if(ds['success']=='1'){
						toastr.error(ds['data']['spcat']+' Unavailable.');
						select_staff.prop("selected",true);
						showmodal(date,staff);
						}else if(ds['success']=='2'){
						toastr.error(ds['data']['spcat']+' Unavailable.');
						select_staff.prop("selected",true);
					}
				},
				error:function (){}
			});
			}else{
			select_staff.prop('selected',true);
		}
	});
	
	
	function appointment(inv,inv_id){
		var row = $("#modal-body");
		row.find('.name').html('loading...');
		row.find('.service').html('loading...');
		row.find('.staff').html('loading...');
		$("#appointment_modal").modal('show');
		
		$.ajax({
			url: "ajax/invoice.php",
			data :{inv:inv_id, aii : inv},
			type: "POST",
			success:function(data){
				var row = $("#modal-body");
				var ds = JSON.parse(data.trim());
				row.find('.name').html(ds['client']);
				row.find('.service').html(ds['service']);
				row.find('.roomname').html(ds['roomname']);
				row.find('.staff').html(ds['beautician']);
				row.find('.spnotes').html(ds['notes']);
				row.find('.apdate').html(ds['date']);
				row.find('.aptime').html(ds['start_time']+' To '+ds['end_time']);
				if(ds['bill_status'] == '1'){
				    $('#but').hide();
				    // $('#bill').show();
				} else {
				    // $('#bill').hide();
				    $('#but').show();
				   
				    $('#edit_md_btn').attr('href',"appointment.php?id="+inv_id);
				    $('#conv_md_btn').attr('href',"billing.php?bid="+inv_id);
				   
				}
			},
			error:function (){},
		});
	}
	
	function appointmentedit(inv){
		
		$("#but").html('loading...');
		jQuery.ajax({
			url: "ajax/invbuttons.php?inv="+inv,
			type: "POST",
			success:function(data){
				$("#but").html(data);
			},
			error:function (){}
		});
	}
</script>


<!-- Modal -->
<div class="modal fade" id="appointment_modal" role="dialog">
	<div class="modal-dialog">
		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">Appointment Details</h4>
			</div>
			<div class="modal-body table-responsive" id="modal-body">
				<table class="table table-bordered">
					<tr>
						<th width="150">Client Name</th>
						<td class="name"></td>
					</tr>
					<tr>
						<th>Appointment Date</th>
						<td class="apdate"></td>
					</tr>
					<tr>
						<th>Appointment Time</th>
						<td class="aptime"></td>
					</tr>
					<tr>
						<th>Room</th>
						<td class="roomname"></td>
					</tr>
					<tr>
						<th>Service</th>
						<td class="service"></td>
					</tr>
					<tr>
						<th>Beautician Name</th>
						<td class="staff"></td>
					</tr>
					<tr>
						<th>Notes</th>
						<td class="spnotes"></td>
					</tr>
				</table>
				
			</div>
			<br>
			<div class="modal-footer">
				<div id="but">
				    <?php if ($_SESSION['u_role']!=3) { ?>
			            <a href="#" id="edit_md_btn"><button class="btn btn-warning" type="button"><i class="fa fa-pencil-square-o" aria-hidden="true"></i>Edit</button></a><?php } ?> 
			        <a href="#" id="conv_md_btn"><button class="btn btn-success"  type="button"><i class="fa fa-money" aria-hidden="true"></i>Generate bill</button></a>
				</div>
				<div id="bill">
				    <!--<a href="#" id="app_billed"><button class="btn btn-success"  type="button"><i class="fa fa-money" aria-hidden="true"></i>Bill paid</button></a>-->
				</div>
			</div>
		</div>
		
	</div>
</div>
<!-- Modal End -->


<!-- Book appointment modal start -->
<div class="modal disableOutsideClick fade" id="book_appointment_modal" role="dialog">
	<div class="modal-dialog modal-lg" style="width:80%;">
		<!-- Modal content-->
		<div class="modal-content">
		    <form action="" id="dash_app" method="post">
    			<div class="modal-header">
    				<h4 class="modal-title">Book appointment</h4>
    			</div>
    			<div class="table-responsive" id="book-app-modal-body">
    			    <div class="container-fluid">
			            <div class="col-md-3">
			                <label>Client name <span class="text-danger">*</span></label>
			                <input type="text" class="client form-control client_name" id="client" name="client" placeholder="Autocomplete (Phone)" value="" required>
						    <input type="hidden" name="clientid" id="clientid" value="" class="clt"> 
						    <input type="hidden" name="client_branch_id" id="client_branch_id" value="" class="clt"> 
			            </div>
			            <div class="col-md-3">
			                <label>Contact number <span class="text-danger">*</span></label>
			                <input type="text" class="form-control client" value=""  onBlur="check();contact_no_length($(this), this.value);" id="cont" name="cont" placeholder="Client contact" onkeyup="this.value = this.value.replace(/[^0-9\.]/g,'');" maxLength="<?= PHONE_NUMBER ?>" required>
							<span style="color:red" id="client-status"></span>
							<span style="color:red" id="digit_error"></span>
			            </div>
			            <div class="col-md-3">
			                <label>Appointment date <span class="text-danger">*</span></label>
			                <input type="text" class="form-control" id="date" name="doa" required readonly />
						    <span class="text-danger" id="dateerror"></span>
			            </div>
			            <div class="col-md-3">
			                <label>Gender <span class="text-danger">*</span></label>
			                <select class="form-control" name="gender" required id="gender">
								<option value="">--Select--</option>
								<option id="gn-1" value="1">Male</option>
								<option id="gn-2" value="2">Female</option>
							</select>
			            </div>
			            <div class="clearfix"></div><br />
			            <div class="col-md-12">
			                <table class="table table-bordered" style="margin-bottom:10px;">
            			        <thead>
            			            <tr>
            							<th colspan="2">Select service</th>
            							<!--<th>Discount</th>-->
            							<th>Service provider</th>
            							<th>Start & end time</th>
            							<th>Price</th>
            						</tr>
            			        </thead>
            			        <tbody>
            			            <tr id="TextBoxContainer" class="TextBoxContainer">
            							<td style="vertical-align: middle"><span class="sno"><span class="icon-dots-three-vertical"></span></span></td>
            							<td width="450"><input type="text" class="ser form-control slot" name="services[]" value="" placeholder="Service (Autocomplete)" required>
            								<input type="hidden" name="service[]" value="" class="serr">
            								<input type="hidden" name="durr[]" value="" class="durr">
            								<input type="hidden" name="pa_ser[]" value="" class="pa_ser">
            							</td>
            							<td class="spr_row" width="250"> 
            								<table id="add_row" style="width:100%"  class="inner-table-space">
            								    <tbody>
            								        <tr>
                    									<td width="100%" id="select_row">
                    										<select name="staffid0[]" data-validation="required" class="form-control staff" required>
                    											<option value="">Service provider</option>
                    										</select>
                    									</td>		
            								        </tr>
            								    </tbody>
            								</table>
            							</td>
            							<input type="hidden" name="duration[]" value="" class="duration">
            							<input type="hidden" name="ser_stime[]" value="" class="ser_stime">
            							<input type="hidden" name="ser_etime[]" value="" class="ser_etime">
            							<td>
            								<table class="inner-table-space">
            									<tr>
            										<td width="50%">
            											<input type="text" class="form-control start_time time" value="" placeholder="Start time" name="start_time[]" onchange="servicestarttime(this.value, $(this))" readonly>
            										</td>
            										<td width="50%">
            											<input type="text" class="form-control end_time"  name="end_time[]" value=""  placeholder="End time"  readonly>
            										</td>
            									</tr>
            								</table>
            							</td> 
            							<td>
            								<input type="number" readonly class="pr form-control price positivenumber decimalnumber" step="0.01" name="price[]" id="userName" placeholder="9800.00" > 
            								<input type="hidden" class="prr" >
            							</td>
            						</tr>
            						<tr id="addBefore"></tr>
            			        </tbody>
            			    </table>
            			    <div class="total-amount" style="text-align:right; font-weight: 600; margin-bottom: 10px;">
            			        <p>Total amount : <span><?= number_format('000',2); ?></span></p>
            			    </div>
            			    <button type="button" id="btnAdd" class="btn btn-info pull-right"><i class="fa fa-plus" aria-hidden="true"></i>Add service</button>
			            </div>
    			    </div>
    			</div>
    			<br>
    			<div class="modal-footer">
    				<div>
    				    <input type="hidden" id="date" />
    				    <input type="hidden" id="time" name="time" />
    				    <input type="hidden" id="total" name="total_amount" />
    				    <input type="text" class="hidden" id="close_time" value="<?= shopclosetime(); ?>">
    			        <button class="btn btn-success" name="submit" type="submit"><i class="fa fa-calendar-check-o" aria-hidden="true"></i>Book appointment</button>
    	                <button class="btn btn-danger" data-dismiss="modal" type="button" onclick="$('#dash_app')[0].reset();"><i class="fa fa-times" aria-hidden="true"></i>Cancel</button>
    				</div>
    			</div>
			</form>
    	</div>
    </div>
</div>
<!-- Book appointment modal end -->
