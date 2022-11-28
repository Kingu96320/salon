 function viewPackageModal(client_id){
	$.ajax({
		url: "ajax/viewPackageModal.php?client_id="+client_id,
		success:function(data){
			$("#packageModal").find(".modal-content").html(data);
			$("#packageModal").modal("show");
		},
		error:function (){}
	});
}

function viewModal(client_id){
	$.ajax({
		url: "ajax/viewInviteCodeModal.php?client_id="+client_id+"&action=show_refferal",
		success:function(data){
			$("#refModal").find(".modal-content").html(data);
			$('#refModal .modal-dialog').removeClass('modal-lg');
			$("#refModal").modal("show");
		},
		error:function (){}
	});
}

function generateRefcode(client_id,reff_code){
    $.ajax({
		url: "ajax/viewInviteCodeModal.php?client_id="+client_id+"&ref_code="+reff_code+"&action=update_refferal_code",
		success:function(data){
			if(data == 1){
			    $('#row_'+client_id).attr('onClick','viewModal('+client_id+')');
			    $('#row_'+client_id).html('<i class="icon-eye3"></i> ');
			    $('#row_'+client_id).append('<b>'+reff_code+'</b>');
			    $('#row_'+client_id).removeClass('text-danger');
			    $('#check_'+client_id).attr('data-ref',reff_code);
			    toastr.success("Code generated successfully.");
			}
		},
		error:function (){}
	});
}