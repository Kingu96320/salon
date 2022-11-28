function formValidaiorns(){
	$('.ser').on('keyup',function(){ $(this).parent().find('.serr').val(""); });
	$('.ser').on('blur',function(){
	    var Div = $(this);
	    setTimeout(function(){
    		var row = Div.parent();
    		var serviceID = row.find('.serr').val(); 
    		if(serviceID === '' || serviceID == undefined){ 
    		    row.find('.serviceErrorMessage').remove();
    		    row.append('<span class="serviceErrorMessage">Please select service from list.</span>'); 
    		    Div.css("border-color","#ff7171e8"); 
    		    Div.val("");
    		} else{
    		    row.find('.serviceErrorMessage').remove();
    		    Div.css("border-color","");
    		}
	    },1000);
	});
}

function EnqformValidaiorns(){
    $('#enquiry').on('keyup',function(){ $(this).parent().find('#enquiry_service_id').val(""); });
	$('#enquiry').on('blur',function(){
		var row = $(this).parent();
		var serviceID = row.find('#enquiry_service_id').val(); 
		if(serviceID === ''){ 
		    row.find('.serviceErrorMessage').remove();
		    row.append('<span class="serviceErrorMessage">Please select options from list.</span>'); 
		    $(this).css("border-color","#ff7171e8"); 
		    $(this).val(""); 
		} else { 
		    row.find('.serviceErrorMessage').remove();
		    $(this).css("border-color","");
		}
	});
}


function formLoader(e,btn_name){
	var flag=true;
	$('form#main-form').find(':input[required]').each(function(){
		if($(this).val() === ''){
			flag=false;
		}
	});
	if(flag){	
	   
		e.parent().prepend('<a href="javascript:void(0)" class="btn btn-info pull-right"><i class="fa fa-spinner fa-spin"></i>'+btn_name+'</a>'); 
		e.hide();
	}
}
function deleteformLoader(){
	
	$('.main-container').append('<div class="loading">Loading&#8230;</div>');
}