var cartDiv = $('#toast-container');
$(document).ready(function(){
	$('.date').datepicker({  //un restricted date
		format: "yyyy-mm-dd",
		todayBtn: "linked",
		todayHighlight: true,
		autoclose: true,
		weekStart: 1,
		startDate: 'today'
	});
	time();
	getServiceCategory();
	getBranchlist();
	serviceList(0, curr = 0);
	$('#categories > a').addClass('current');

	$('#apptime').on('change', function(){
		if($('#doa').val() == ''){
			toastr.warning('Please select appointment date');
			$(this).val('');
		} else {
			checktime($(this).val());
		}
	});

	// function to hide button time selection

	var btn_date_time = $('#btn-app-date-time').val();
	$.ajax({
		url : base_url+'/check_date_time.php',
		type : 'post',
		data : {action : 'check_btn_option', time : btn_date_time},
		dataType : 'json',
		success : function(res){
			if(res.status == 0){
				$('#button-time-selection').remove();
			}
		}
	})
});

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

function setAppDateTime(date, time){
	$('#doa').val(date);
	$('#apptime').val(time);
}

var base_url = 'https://easygymsoftware.in/new_design_salon/api/';

function getBranchlist(){
    $.ajax({
        url : base_url+'branch_list.php',
        type : 'post',
        dataType : 'json',
        success : function(response){
            var html = '<div class="row">';
            var count = 1;
            $.each(response, function(key, value){
               if(count == 1){
                   var selected = 'checked';
               } else {
                   var selected = '';
               }
               html += '<div class="col-md-6" style="margin-bottom:15px;"><label><input '+selected+' type="radio" name="branches" value="'+value.branchId+'" /> '+value.branchName+'</label><br />'+value.address+'</div>'; 
               count++;
            });
            html += '</div>';
            $('#branches').append(html);
        }
    })
}

function getServiceCategory(){
    var service_for = $('.btn-scat.active').attr('data-cat');
	$.ajax({
		url : base_url+'service_by_cat.php',
		type : 'post',
		data : {service_for : service_for},
		dataType : 'json',
		success : function(response){
			var html = '<a href="javascript:void(0)" onclick="serviceList(0, $(this))" class="btn btn-time current">All</a>';
			$.each(response, function(key, value){
				html += '<a href="javascript:void(0)" onclick="serviceList('+value.catId+', $(this))" class="btn btn-time">'+value.catName+'</a>';
			});
			$('#categories').html(html);
		}
	});
}

function maincat(div){
    $('.btn-scat').removeClass('active');
    div.addClass('active');
    getServiceCategory();
    serviceList(0, curr = 0);
}
	
function serviceList(category_id, curr){
	$('#categories a').removeClass('current');
	if(curr != 0){
		curr.addClass('current');
	}
	
	var service_for = $('.btn-scat.active').attr('data-cat');
	
	$.ajax({
		url : base_url+'servicelist.php',
		type : 'post',
		dataType : 'json',
		data : { cat_id : category_id, service_for : service_for },
		beforeSend: function() {
          $('#services').html('<tr><td colspan="4" class="text-center">Loading...</td></tr>');
       },
		success : function(res){
		    if(res != ''){
    			var html = '';
    			$.each(res, function(key, value){
    				html += '<tr>'+
    					      '<th>'+value.serName+'<br /><i class="fa fa-clock-o text-warning" aria-hidden="true"></i> '+value.serDuration+' Mins</th>'+
    					      '<td>'+value.serPrice+'</td>'+
    					   //   '<td>'++' </td>'+
    					      '<td><i class="fa fa-plus-square text-success" aria-hidden="true" onclick="addCart(\''+value.serName+'\',\''+value.serPrice+'\',\''+value.serDuration+'\',\''+value.serId+'\')"></i></td>'+
    					    '</tr>';
    			});
    			$('#services').html(html);
		    } else {
		        $('#services').html('<tr><td colspan="4" class="text-center">No service found!</td></tr>');
		    }
		}
	});
}

function addCart(service_name, service_price, service_duration, service_id){
    var index = $('#selectedService').children().length;
	$.ajax({
	    url : 'ajax/cart.php',
		type : 'post',
		dataType : 'json',
		data : { action : 'add_cart', sname : service_name, sprice : service_price, sduration: service_duration, sid : service_id },
		success : function(res){
		    var html = '<tr>'+
			      '<th><a href="javascript:void(0)" onclick="deleteService($(this), '+index+')"><i class="far fa-trash-alt" style="color:red;"></i> </a>'+service_name+'<br /><i class="fa fa-clock-o text-warning" aria-hidden="true"></i> '+service_duration+' Mins</th>'+
			      '<td>'+service_price+'</td>'+
			      '<input type="hidden" value="'+service_id+'" class="service_id">'+
			      '<input type="hidden" value="'+service_price+'" class="service_price">'+
			      '<input type="hidden" value="'+service_duration+'" class="service_duration">'+
			    '</tr>';
        	$('#selectedService').append(html);
        	$('.empty_cart').remove();
        	cartDiv.remove();
        	toastr.success('Service added in cart');
        	checkOut();
		}
	});
}

function deleteService(curr, index){
    $.ajax({
	    url : 'ajax/cart.php',
		type : 'post',
		dataType : 'json',
		data : { action : 'remove_cart', index : index },
		success : function(res){
        	curr.parents('tr').remove();
        	if($('#selectedService').children().length == 0){
        		$('#selectedService').html('<tr class="empty_cart">'+
        	   		'<td colspan="3" class="text-center">Cart is empty</td>'+
        	   	'</tr>');
        		$('.checkout').remove();
        	}
		}
    });
}

function checkOut(){
	if($('#selectedService').children().length > 0){
		$('.checkout').remove();
		$('.cart').after('<a href="javascript:void(0)" onclick="bookservice()" class="btn btn-time checkout">Confirm</a>');
	}
}


//   service booking function
function bookservice(){
	jsonObj = [];
  	var count = 0; 
  	$("input[class=service_id]").each(function() {
  	  var id = $(this).val();
      if(parseInt(id) > 0){
      	var service_id = id;
        services = {};
        services["id"] = service_id;
        jsonObj.push(services);
        count++;
      }
  	});
    if(count > 0){
      	localStorage.setItem('selected_services',JSON.stringify(jsonObj));
  	} else {
  		toastr.error('Cart is empty');	
      	localStorage.clear();
  	}

    var name = $('#name').val();
    var phone = $('#number').val();
    var doa = $('#doa').val();
    var apptime = $('#apptime').val();
    var data = [];
    if(name == ''){ $('#name').css('border-color','#f00'); data.push('name'); } else { $('#name').css('border-color','#ced4da'); }
    if(doa == ''){ $('#doa').css('border-color','#f00'); data.push('doa'); } else { $('#doa').css('border-color','#ced4da'); }
    if(apptime == ''){ $('#apptime').css('border-color','#f00'); data.push('apptime'); } else { $('#apptime').css('border-color','#ced4da'); } 
    if(phone == ''){ $('#number').css('border-color','#f00'); data.push('number');} else { $('#number').css('border-color','#ced4da'); } 
    if(data.length == 0){
        $.ajax({
            url : base_url+'/otp.php',
            method : 'post',
            data : {action : 'otp', phone : phone},
            beforeSend: function() {
                $('.checkout').text('Please wait..');
                $('.checkout').prop('disabled', true);
            },
            success : function(res){
                if(res != ''){
                    $('#booking-button').text('Confirm booking');
                    $('body').append(res);
                    $('#otp_modal').modal('show');
                    $('.checkout').text('CONFIRM');
                    $('.checkout').prop('disabled', false);
                }
            }
        });
    }
}

// function to check date

function checkdate(date){
	$.ajax({
		url : base_url+'/check_date_time.php',
		type : 'post',
		dataType : 'json',
		data : {action : 'checkdate', date : date},
		success : function(res){
			if(res.status == '0'){
				toastr.error(res.msg);
				$('#doa').val('');
			}
		}
	});
}


// function to check appointemnt time

function checktime(time){
	var date = $('#doa').val();
	$.ajax({
		url : base_url+'/check_date_time.php',
		type : 'post',
		dataType : 'json',
		data : {action : 'checktime', date : date, time : time},
		success : function(res){
			if(res.status == '0'){
				toastr.error(res.msg);
				$('#apptime').val('');
			}
		}
	});
}