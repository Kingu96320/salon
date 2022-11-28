<?php
	include "./includes/db_include.php";
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
					<div class="panel-heading">
						<h4>Mobile app</h4>
					</div>
					<div class="panel-body">
						<!-- Row Start -->
						
						<div id="exTab1" class="container">	
							<ul  class="nav nav-pills">
								<li class="active">
									<a  href="#1a" onclick="fetch_slider();" data-toggle="tab"  aria-expanded="true" id="slider_tab">Slider</a>
								</li>
								<li><a href="#2a"  onclick="fetch_offer();" data-toggle="tab">Offers</a>
								</li>
								<li><a href="#3a" onclick="fetch_featured_services();" data-toggle="tab">Featured services</a>
								</li>
								<li><a href="#4a" onclick="fetch_gallery();" data-toggle="tab">Gallery</a>
								</li>
								<li><a href="#5a" onclick="fetch_about();" data-toggle="tab">About</a>
								</li>
								
							</ul>
							
							<div class="tab-content clearfix">
									<div class="tab-pane active" id="1a" >
										<div class="panel-body">
										<div class="row">
											<form id="slider-image-form" method="post" enctype="multipart/form-data">
												<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
													<div class="form-group">
														<label for="userName">Slider images:</label>
														<input type="file" name="slider_image[]" id="slider-photo-add" value="" class="form-control" accept=".jpg,.png,.jpeg" multiple />
													</div>
												</div>
												<div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
													<div class="form-group">
														<label for="userName">Preview:</label>
														<div class="slider"></div>
													</div>
												</div>	
												<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
													<div class="form-group">
														
														<button type="submit" name="edit-submit" class="btn btn-info pull-right" id="add-slider-button-edit">Update slider image</button>
														<a  href="javascript:void(0)" class="btn btn-info pull-right" id="add-slider-button-loading-edit"  style="display:none;">
															<i class="fa fa-circle-o-notch fa-spin"></i>Update slider image
														</a>
														
														<button type="submit" name="submit"  class="btn btn-info pull-right" id="add-slider-button">Add slider image</button>
														<a  href="javascript:void(0)" class="btn btn-info pull-right" id="add-slider-button-loading"  style="display:none;">
															<i class="fa fa-circle-o-notch fa-spin"></i>Add slider image
														</a>
													</div>
												</div>
											</form>
										</div>
									</div>
									<!-- Row starts -->
									<div class="row gutter">
										<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
											<div class="panel">
												<div class="panel-heading">
													<h4>Manage Slider </h4>
												</div>
												<div class="panel-body">
													<div class="row">
														<div class="col-lg-12">
															<div class="table-responsive">
																<table  class="table table-striped table-bordered slider_image">
																	<thead>
																		<tr>
																			<th width="50%">Images</th>
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
								<div class="tab-pane" id="2a">
									<div class="panel-body">
										<div class="row">
											<form id="offer-image-form" method="post" enctype="multipart/form-data">
												<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
													<div class="form-group">
														<label for="userName">Offer Name:</label>
														<input type="text" class="form-control" name="offer_name" id="offer_name" placeholder="Offer Name" value="" required>
														<input type="hidden" name="offer_edit_id" id="offer_edit_id">
													</div>
												</div>
												<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
													<div class="form-group">
														<label for="userName">Offer Description:</label>
														<input type="text" class="form-control" name="offer_desc" id="offer_desc" placeholder="Offer Description" value=""  required>
													</div>
												</div>
												<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
													<div class="form-group">
														<label for="userName">Images:</label>
														<input type="file" name="offer_image"  id="offer_image_input" value="" onChange="readURL(this,'offer_image')" class="form-control" accept=".jpg,.png,.jpeg" />
														<img class="img img-responsive" id="offer_image" style="height : 100px; width : 200px;">
													</div>
												</div>
												<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
													<div class="form-group">
														<label for="userName">Image role:</label>
														<select name="image_role" id="image_role" class="form-control">
															<option value="1">Main image</option>
															<option value="0">Sub image</option>
														</select>
													</div>
												</div>
												<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
													<div class="form-group">
														
														<button type="submit" name="edit-submit" class="btn btn-info pull-right" id="add-offer-button-edit">Update Offer Image</button>
														<a  href="javascript:void(0)" class="btn btn-info pull-right" id="add-offer-button-loading-edit"  style="display:none;">
															<i class="fa fa-circle-o-notch fa-spin"></i>Update Offer Image
														</a>
														
														<button type="submit" name="submit"  class="btn btn-info pull-right" id="add-offer-button">Add Offer image</button>
														<a  href="javascript:void(0)" class="btn btn-info pull-right" id="add-offer-button-loading"  style="display:none;">
															<i class="fa fa-circle-o-notch fa-spin"></i>Add Offer Image
														</a>
														
													</div>
												</div>
											</form>
										</div>
									</div>
									<!-- Row starts -->
									<div class="row gutter">
										<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
											<div class="panel">
												<div class="panel-heading">
													<h4>Manage featured services </h4>
												</div>
												<div class="panel-body">
													<div class="row">
														<div class="col-lg-12">
															<div class="table-responsive">
																<table  class="table table-striped table-bordered offers">
																	<thead>
																		<tr>
																			<th width="25%">Offer Name</th>
																			<th width="25%">Offer Description</th>
																			<th width="25%">Image</th>
																			<th width="25%">Action</th>
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
								<div class="tab-pane" id="3a">
									<div class="panel-body">
										<div class="row">
											<form id="featured-image-form" method="post" enctype="multipart/form-data">
												<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
													<div class="form-group">
														<label for="userName">Select service:</label>
														<input type="text" class="form-control" name="service_name" id="service_name" placeholder="Autocomplete(services)" value="" required>
														<input type="hidden" name="service_id" value="" id="service_id">
														<input type="hidden" name="edit_id" id="edit_id">
													</div>
												</div>
												<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
													<div class="form-group">
														<label for="userName">Service price:</label>
														<input type="text" class="form-control" name="service_price" id="service_price" placeholder="Autocomplete(services)" value="0" readonly>
													</div>
												</div>
												<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
													<div class="form-group">
														<label for="userName">Featured images:</label>
														<input type="file" name="featured_image" id="featured_image_input" value="" onChange="readURL(this,'featured_image')" class="form-control" accept=".jpg,.png,.jpeg" />
														<img class="img img-responsive" id="featured_image" style="height : 100px; width : 200px;">
													</div>
												</div>
												<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
													<div class="form-group">
														
														<button type="submit" name="edit-submit" class="btn btn-info pull-right" id="add-featured-button-edit">Update featured image</button>
														<a  href="javascript:void(0)" class="btn btn-info pull-right" id="add-featured-button-loading-edit"  style="display:none;">
															<i class="fa fa-circle-o-notch fa-spin"></i>Update featured image
														</a>
														
														<button type="submit" name="submit"  class="btn btn-info pull-right" id="add-featured-button">Add featured image</button>
														<a  href="javascript:void(0)" class="btn btn-info pull-right" id="add-featured-button-loading"  style="display:none;">
															<i class="fa fa-circle-o-notch fa-spin"></i>Add featured image
														</a>
														
													</div>
												</div>
											</form>
										</div>
									</div>
									<!-- Row starts -->
									<div class="row gutter">
										<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
											<div class="panel">
												<div class="panel-heading">
													<h4>Manage featured services </h4>
												</div>
												<div class="panel-body">
													<div class="row">
														<div class="col-lg-12">
															<div class="table-responsive">
																<table  class="table table-striped table-bordered featured_image">
																	<thead>
																		<tr>
																			<th width="25%">Featured service</th>
																			<th width="25%">Featured image</th>
																			<th width="25%">price</th>
																			<th width="25%">Action</th>
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
								<div class="tab-pane" id="4a">
										<div class="panel-body">
										<div class="row">
											<form id="gallery-image-form" method="post" enctype="multipart/form-data">
												<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
													<div class="form-group">
														<label for="userName">Gallery images:</label>
														<input type="file" name="gallery_image[]" id="gallery-photo-add" value="" class="form-control" accept=".jpg,.png,.jpeg" multiple />
													</div>
												</div>
												<div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
													<div class="form-group">
														<label for="userName">Preview:</label>
														<div class="gallery"></div>
													</div>
												</div>	
												<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
													<div class="form-group">
														
														<button type="submit" name="edit-submit" class="btn btn-info pull-right" id="add-gallery-button-edit">Update gallery image</button>
														<a  href="javascript:void(0)" class="btn btn-info pull-right" id="add-gallery-button-loading-edit"  style="display:none;">
															<i class="fa fa-circle-o-notch fa-spin"></i>Update gallery image
														</a>
														
														<button type="submit" name="submit"  class="btn btn-info pull-right" id="add-gallery-button">Add gallery image</button>
														<a  href="javascript:void(0)" class="btn btn-info pull-right" id="add-gallery-button-loading"  style="display:none;">
															<i class="fa fa-circle-o-notch fa-spin"></i>Add gallery image
														</a>
													</div>
												</div>
											</form>
										</div>
									</div>
									<!-- Row starts -->
									<div class="row gutter">
										<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
											<div class="panel">
												<div class="panel-heading">
													<h4>Manage Gallery </h4>
												</div>
												<div class="panel-body">
													<div class="row">
														<div class="col-lg-12">
															<div class="table-responsive">
																<table  class="table table-striped table-bordered gallery_image">
																	<thead>
																		<tr>
																			<th width="50%">Images</th>
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
								
								<div class="tab-pane" id="5a">
									<div class="panel-body">
										<div class="row">
											<form id="about-form" method="post" enctype="multipart/form-data">
												<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
													<div class="form-group">
														<label for="userName">About:</label>
														<textarea name="about" class="form-control" rows="5" id="about_textarea" required></textarea>
														<input type="hidden" name="edit_about_id" id="edit_about_id">
													</div>
												</div>
												<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
													<div class="form-group">
													
														<button type="submit" name="edit-submit" class="btn btn-info pull-right" id="add-about-button-edit">Update </button>
														<a  href="javascript:void(0)" class="btn btn-info pull-right" id="add-about-button-loading-edit"  style="display:none;">
															<i class="fa fa-circle-o-notch fa-spin"></i>Update
														</a>
														
														<button type="submit" name="submit"  class="btn btn-info pull-right" id="add-about-button">Add</button>
														<a  href="javascript:void(0)" class="btn btn-info pull-right" id="add-about-button-loading" style="display:none;">
															<i class="fa fa-circle-o-notch fa-spin"></i>Add
														</a>
														
													</div>
												</div>
											</form>
										</div>
									</div>
									<!-- Row starts -->
									<div class="row gutter">
										<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
											<div class="panel">
												<div class="panel-heading">
													<h4>Manage About</h4>
												</div>
												<div class="panel-body">
													<div class="row">
														<div class="col-lg-12">
															<div class="table-responsive">
																<table  class="table table-striped table-bordered about">
																	<thead>
																		<tr>
																			<th width="50%">About</th>
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
							</div>
						</div>
						<!-- Row End -->
						<div class="clearfix"></div><br>
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
	
	$(document).ready(function(){
	
		$('#slider_tab').click();
		
		$('#add-featured-button-loading').hide();
		$('#add-featured-button-edit').hide();
		$('#add-featured-button-loading-edit').hide();
		$('#add-gallery-button-loading').hide();
		$('#add-gallery-button-edit').hide();
		$('#add-gallery-button-loading-edit').hide();
		$('#add-slider-button-loading').hide();
		$('#add-slider-button-edit').hide();
		$('#add-slider-button-loading-edit').hide();
		$('#add-offer-button-loading').hide();
		$('#add-offer-button-edit').hide();
		$('#add-offer-button-loading-edit').hide();
		$('#add-about-button-loading').hide();
		$('#add-about-button-edit').hide();
		$('#add-about-button-loading-edit').hide();
		
		
	});
	
	
	function edit_delete(id,type){
		if(type == 'edit'){
			$('input').val("");
			$('img #featured_image').removeAttr('src');
			$('#add-featured-button-loading-edit').hide();
			// var edid = 'id='+id+'&type=delete';
			$('#edit_id').val(id);
			$.ajax({
				type: "POST",
				url: "ajax/mobile_app_crud.php",
				data: {eid:id},
				cache: false,
				success: function(data){
					var ds=JSON.parse(data);
					$("#service_id").val(ds[0]['service_id']);
					$("#service_name").val(ds[0]['service_name']);
					$("#featured_image").attr("src","app/images/"+ds[0]['featured_image']);
					$("#service_price").val(ds[0]['price']);
					
					$('#add-featured-button').hide();
					$('#add-featured-button-edit').show();
					 
				}
			});
			
			}else if(type == 'delete'){
			var reply = confirm('Are you sure?'); 
			var edid = 'id='+id+'&type=featuredservicedelete';
			if(reply === true){
				$('.featured_image').DataTable().destroy();
				$('.featured_image').dataTable({
					"destroy": true,	
					"sAjaxSource": "ajax/fetch_app_data.php?"+edid,
					"dataSrc":"",
					"aoColumns": [
					{ mData: 'f_service' },
					{ mData: 'f_image'},
					{ mData: 'f_price'},
					{ mData: 'action'},
					],
				}); 
				
				$('input').val("");
			}
		}
        return false; 
	}
	
	
	function offer_edit_delete(id,type){
		 
		if(type == 'edit'){
			$('input').val("");
			$('img #offer_image').removeAttr('src');
			$('#add-offer-button-loading-edit').hide();
			$('#edit_id').val(id);
			$.ajax({
				type: "POST",
				url: "ajax/mobile_app_crud.php",
				data: {offer_eid:id},
				cache: false,
				success: function(data){
					var ds=JSON.parse(data);
					$("#offer_edit_id").val(ds[0]['id']);
					$("#offer_name").val(ds[0]['offer_name']);
					$("#offer_image").attr("src","app/images/"+ds[0]['name']);
					$("#offer_desc").val(ds[0]['offer_desc']);
					$('#image_role option[value="'+ds[0]['main']+'"]').prop('selected',true); 
					$('#add-offer-button').hide();
					$('#add-offer-button-edit').show();
				}
			});
			
			}else if(type == 'delete'){
			var reply = confirm('Are you sure?'); 
			var offer_edid = 'offer_id='+id+'&type=offerdelete';
			if(reply === true){
				$('.offers').DataTable().destroy();
				$('.offers').dataTable({
					"destroy": true,	
					"sAjaxSource": "ajax/fetch_app_data.php?"+offer_edid,
					"dataSrc":"",
					"aoColumns": [
					{ mData: 'offer_name' },
					{ mData: 'offer_desc'},
					{ mData: 'offer_image'},
					{ mData: 'action'},
					],
				}); 
				
				$('input').val("");
			}
		}
        return false; 
	}
	
	
	function edit_delete_about(id,type){
		if(type == 'edit'){
			 
			$('#add-about-button-loading-edit').hide();
			$('#edit_about_id').val(id);
			$.ajax({
				type: "POST",
				url: "ajax/mobile_app_crud.php",
				data: {edit_about_id:id},
				cache: false,
				success: function(data){
					var ds=JSON.parse(data);
					$("#edit_about_id").val(ds[0]['id']);
					$("#about_textarea").val(ds[0]['about_us']);
					$('#add-about-button').hide();
					$('#add-about-button-edit').show();
				}
			});
			
			}else if(type == 'delete'){
			var reply = confirm('Are you sure?'); 
			var offer_edid = 'edit_about_id='+id+'&type=aboutusdelete';
			if(reply === true){
				$('.about').DataTable().destroy();
				$('.about').dataTable({
					"destroy": true,	
					"sAjaxSource": "ajax/fetch_app_data.php?"+offer_edid,
					"dataSrc":"",
					"aoColumns": [
					{ mData: 'about_us' },
					{ mData: 'action'},
					],
				}); 
				
				$("#about_textarea").val("");
				$('input').val("");
			}
		}
        return false; 
	}
	
	function delete_gallery_images(id,type){
		if(type == 'delete'){
			var reply = confirm('Are you sure?'); 
			var edid = 'gallery_images_id='+id+'&type=delete';
			if(reply === true){
				$('.gallery_image').DataTable().destroy();
				$('.gallery_image').dataTable({
					"destroy": true,	
					"sAjaxSource": "ajax/fetch_app_data.php?"+edid,
					"dataSrc":"",
					"aoColumns": [
						{ mData: 'gallery_images' },
						{ mData: 'action'},
						],	
				}); 
				$('input').val("");
			}
		}
        return false; 
	}
	
	function delete_slider_images(id,type){
		if(type == 'delete'){
			var reply = confirm('Are you sure?'); 
			var edid = 'slider_images_id='+id+'&type=delete';
			if(reply === true){
				$('.slider_image').DataTable().destroy();
				$('.slider_image').dataTable({
					"destroy": true,	
					"sAjaxSource": "ajax/fetch_app_data.php?"+edid,
					"dataSrc":"",
					"aoColumns": [
						{ mData: 'slider_images' },
						{ mData: 'action'},
						],	
				}); 
				$('input').val("");
			}
		}
        return false; 
	}
	function fetch_featured_services(){
		$('.featured_image').DataTable().destroy();
		var table =$('.featured_image').dataTable({
			"destroy": true,	
			"sAjaxSource": "ajax/fetch_app_data.php?page=featured_services",
			"dataSrc":"",
			"aoColumns": [
					{ mData: 'f_service' },
					{ mData: 'f_image'},
					{ mData: 'f_price'},
					{ mData: 'action'},
					],			 
		});  
	}
	
	function fetch_offer(){
		$('.offers').DataTable().destroy();
		var table =$('.offers').dataTable({
			"destroy": true,	
			"sAjaxSource": "ajax/fetch_app_data.php?page=offers",
			"dataSrc":"",
			"aoColumns": [
					{ mData: 'offer_name' },
					{ mData: 'offer_desc'},
					{ mData: 'offer_image'},
					{ mData: 'action'},
					],			 
		});  
	}
	
	function fetch_gallery(){
		$('.gallery_image').DataTable().destroy();
		var table =$('.gallery_image').dataTable({
			"destroy": true,	
			"sAjaxSource": "ajax/fetch_app_data.php?page=gallery_images",
			"dataSrc":"",
			"aoColumns": [
			{ mData: 'gallery_images' },
			{ mData: 'action'},
			],			 
		});  
	}
	function fetch_slider(){
		$('.slider_image').DataTable().destroy();
		var table =$('.slider_image').dataTable({
			"destroy": true,	
			"sAjaxSource": "ajax/fetch_app_data.php?page=slider_images",
			"dataSrc":"",
			"aoColumns": [
			{ mData: 'slider_images' },
			{ mData: 'action'},
			],			 
		});  
	}
	
	function fetch_about(){
	$('.about').DataTable().destroy();
		var table =$('.about').dataTable({
			"destroy": true,	
			"sAjaxSource": "ajax/fetch_app_data.php?page=aboutus",
			"dataSrc":"",
			"aoColumns": [
			{ mData: 'about' },
			{ mData: 'action'},
			],			 
		});  
	}
	
	$("#service_name").autocomplete({
		source: function(request, response) {
			$.getJSON("ajax/bill.php", { term: request.term,page_info:'app' }, response);
		},
		minLength: 1,
		select:function (event, ui) {  
			$('#service_id').val(ui.item.id.split(',')[1]);
			$('#service_name').val(ui.item.value);
			$('#service_price').val(ui.item.price);
		}
	});	
	
	
	$("#featured-image-form").on('submit',(function(e){
		if($('#edit_id').val() > 0){
			$('#add-featured-button-loading-edit').show();
			$('#add-featured-button-loading').hide();
			$('#add-featured-button-edit').hide();
			}else{
			$('#add-featured-button-loading').show();
			$('#add-featured-button-loading-edit').hide();
			$('#add-featured-button').hide();
		}
		e.preventDefault();
		$.ajax({
			url: "ajax/mobile_app_crud.php", // Url to which the request is send
			type: "POST",             // Type of request to be send, called as method
			data: new FormData(this), // Data sent to server, a set of key/value pairs (i.e. form fields and values)
			contentType: false,       // The content type used when sending data to the server.
			cache: false,             // To unable request pages to be cached
			processData:false,        // To send DOMDocument or non processed data file it is set to false
			success: function(data){   // A function to be called if request succeeds
				if(data){
					var di = JSON.parse(data);
					if(di['data-inserted'] == '1'){
						$('#add-featured-button').show();
						$('#add-featured-button-loading').hide();
						}else if(di['data-updated'] == '1'){
						$('#add-featured-button').show();
						$('#add-featured-button-loading').hide();
						$('#add-featured-button-loading-edit').hide();
						
					}
					fetch_featured_services();
					$('input').val("");
					$('#featured_image').attr("src"," ");
				}
			},
		});
	}));
	
	
	$("#gallery-image-form").on('submit',(function(e){
 
		if($('#edit_id').val() > 0){
			$('#add-gallery-button-loading-edit').show();
			$('#add-gallery-button-loading').hide();
			$('#add-gallery-button-edit').hide();
		}else{
			$('#add-gallery-button-loading').show();
			$('#add-gallery-button-loading-edit').hide();
			$('#add-gallery-button').hide();
		}
		e.preventDefault();
		$.ajax({
			url: "ajax/mobile_app_crud.php", // Url to which the request is send
			type: "POST",             // Type of request to be send, called as method
			data: new FormData(this), // Data sent to server, a set of key/value pairs (i.e. form fields and values)
			contentType: false,       // The content type used when sending data to the server.
			cache: false,             // To unable request pages to be cached
			processData:false,        // To send DOMDocument or non processed data file it is set to false
			success: function(data){   // A function to be called if request succeeds
				if(data){
					var di = JSON.parse(data);
					if(di['data-inserted'] == '1'){
						$('#add-gallery-button').show();
						$('#add-gallery-button-loading').hide();
					}else if(di['data-updated'] == '1'){
						$('#add-gallery-button').show();
						$('#add-gallery-button-loading').hide();
						$('#add-gallery-button-loading-edit').hide();
					}
					fetch_gallery();
					$('input').val("");
					$('div.gallery').empty();
				}
			},
		});
	}));
	
	$("#slider-image-form").on('submit',(function(e){
 
		if($('#edit_id').val() > 0){
			$('#add-slider-button-loading-edit').show();
			$('#add-slider-button-loading').hide();
			$('#add-slider-button-edit').hide();
		}else{
			$('#add-slider-button-loading').show();
			$('#add-slider-button-loading-edit').hide();
			$('#add-slider-button').hide();
		}
		e.preventDefault();
		$.ajax({
			url: "ajax/mobile_app_crud.php", // Url to which the request is send
			type: "POST",             // Type of request to be send, called as method
			data: new FormData(this), // Data sent to server, a set of key/value pairs (i.e. form fields and values)
			contentType: false,       // The content type used when sending data to the server.
			cache: false,             // To unable request pages to be cached
			processData:false,        // To send DOMDocument or non processed data file it is set to false
			success: function(data){   // A function to be called if request succeeds
				if(data){
					var di = JSON.parse(data);
					if(di['data-inserted'] == '1'){
						$('#add-slider-button').show();
						$('#add-slider-button-loading').hide();
					}else if(di['data-updated'] == '1'){
						$('#add-slider-button').show();
						$('#add-slider-button-loading').hide();
						$('#add-slider-button-loading-edit').hide();
					}
					fetch_slider();
					$('input').val("");
					$('div.slider').empty();
				}
			},
		});
	}));
	
	 $("#offer-image-form").on('submit',(function(e){
		if($('#offer_edit_id').val() > 0){
			$('#add-offer-button-loading-edit').show();
			$('#add-offer-button-loading').hide();
			$('#add-offer-button-edit').hide();
		}else{
			$('#add-offer-button-loading').show();
			$('#add-offer-button-loading-edit').hide();
			$('#add-offer-button').hide();
		}
		e.preventDefault();
		$.ajax({
			url: "ajax/mobile_app_crud.php", // Url to which the request is send
			type: "POST",             // Type of request to be send, called as method
			data: new FormData(this), // Data sent to server, a set of key/value pairs (i.e. form fields and values)
			contentType: false,       // The content type used when sending data to the server.
			cache: false,             // To unable request pages to be cached
			processData:false,        // To send DOMDocument or non processed data file it is set to false
			success: function(data){   // A function to be called if request succeeds
				if(data){
					var di = JSON.parse(data);
					if(di['data-inserted'] == '1'){
						$('#add-offer-button').show();
						$('#add-offer-button-loading').hide();
					}else if(di['data-updated'] == '1'){
						$('#add-offer-button').show();
						$('#add-offer-button-loading').hide();
						$('#add-offer-button-loading-edit').hide();
					}
					fetch_offer();
					$('input').val("");
				}
			},
		});
	}));
	
	 $("#about-form").on('submit',(function(e){
		 if($('#edit_about_id').val() > 0){
			$('#add-about-button-loading-edit').show();
			$('#add-about-button-loading').hide();
			$('#add-about-button-edit').hide();
		}else{
			$('#add-about-button-loading').show();
			$('#add-about-button-loading-edit').hide();
			$('#add-about-button').hide();
		}
		e.preventDefault();
		$.ajax({
			url: "ajax/mobile_app_crud.php", // Url to which the request is send
			type: "POST",             // Type of request to be send, called as method
			data: new FormData(this), // Data sent to server, a set of key/value pairs (i.e. form fields and values)
			contentType: false,       // The content type used when sending data to the server.
			cache: false,             // To unable request pages to be cached
			processData:false,        // To send DOMDocument or non processed data file it is set to false
			success: function(data){   // A function to be called if request succeeds
				if(data){
					var di = JSON.parse(data);
					if(di['data-inserted'] == '1'){
						$('#add-about-button').show();
						$('#add-about-button-loading').hide();
					}else if(di['data-updated'] == '1'){
						$('#add-about-button').show();
						$('#add-about-button-loading').hide();
						$('#add-about-button-loading-edit').hide();
					}
					$('#about_textarea').val("");
					$('input').val("");
					fetch_about();
					
					
				}
			},
		});
	}));
	
	function readURL(input,key) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                $('#'+key).attr('src', e.target.result);
			}
            reader.readAsDataURL(input.files[0]);
		}
	}
	
	
	
	
	// Multiple images preview in browser
	$(function() {
    var imagesPreview = function(input, placeToInsertImagePreview) {
        if (input.files) {
            var filesAmount = input.files.length;

            for (i = 0; i < filesAmount; i++) {
                var reader = new FileReader();

                reader.onload = function(event) {
                    $($.parseHTML('<img style="height : 50px; width : 100px;">')).attr('src', event.target.result).appendTo(placeToInsertImagePreview);
                }

                reader.readAsDataURL(input.files[i]);
            }
        }

    };

    $('#gallery-photo-add').on('change', function() {
        imagesPreview(this,'div.gallery');
    }); 
	
	$('#slider-photo-add').on('change', function() {
        imagesPreview(this,'div.slider');
    });
});
	
</script>

