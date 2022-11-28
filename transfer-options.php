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
            <li class="active"><a data-toggle="tab" href="#sp_transfer">Service provider</a></li>
            <li><a data-toggle="tab" href="#staff_transfer">Staff</a></li>
            <li><a data-toggle="tab" href="#product_transfer">Product</a></li>
        </ul>
        <div class="tab-content">

            <div id="sp_transfer" class="tab-pane fade in active">
                <div class="row gutter">
        			<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
    					<div class="panel">
    						<div class="panel-heading">
    							<h4>Transfer service provider</h4> 
    						</div>
    						<div class="panel-body"> 
    							<div class="clearfix"></div>
    							<br>
    							<div class="table-responsive">
                                    <?php
                                        $sp = query_by_id("SELECT * FROM beauticians WHERE branch_id='".$branch_id."' AND active='0'",[],$conn);
                                    ?>
    								<table id="" class="table grid table-bordered" >
                                        <thead>
                                            <th width="150">Profile image</th>
                                            <th>Name</th>
                                            <th>Contact</th>
                                            <th>Designation</th>
                                            <th>Working hours</th> 
                                            <th>Action</th>
                                        </thead>
    									<tbody>		
                                            <?php foreach ($sp as $data) { 
                                                if(!check_sp_transfer_status($data['id'])){
                                            ?>
                                            <tr class="row_<?= $data['id']?>">
                                                <td class="avatar">
                                                    <?php if(strpos($data['photo'], 'upload') !== false){ ?>
                                                        <img src="<?= $data['photo'] ?>" class="img-responsive" style="max-width: 50px" />
                                                    <?php } else { 
                                                        if($data['gender'] == '1'){ ?>
                                                            <img src="img/avatar/male.png" class="img-responsive" style="max-width: 50px" />
                                                        <?php } else if($data['gender'] == '2'){ ?>
                                                            <img src="img/avatar/female.png" class="img-responsive" style="max-width: 50px" />
                                                        <?php }
                                                    }
                                                    ?>
                                                </td>
                                                <td><?= $data['name'] ?></td>
                                                <td><?= $data['cont'] ?></td>
                                                <td><?= ucfirst(provider_designation($data['ser_pro_type_id'])); ?></td>
                                                <td><?= date('h:i A',strtotime($data['starttime'])); ?> To <?= date('h:i A',strtotime($data['endtime'])); ?></td>
                                                <td><button type="botton" onclick="transfer_sp_function(<?= $data['id'] ?>, '<?= $data['name'] ?>')" class="btn btn-success btn-xs"><i class="fa fa-exchange" aria-hidden="true"></i>Transfer</button></td>
                                            </tr>
                                            <?php } } ?>		
    									</tbody>				
    								</table>
    							</div>
    							
    						</div>
    					</div>
        			</div>
        		</div>
            </div>

            <div id="staff_transfer" class="tab-pane fade">
                <div class="row gutter">
            		<div class="col-lg-12">	
            			<div class="panel">
            				<div class="panel-heading">
            					<h4>Transfer staff</h4>
            				</div>
            				<div class="panel-body">
            					<div class="clearfix"></div>
            					<div class="table-responsive">
            						<table id="" class="table table-bordered no-margin" style="width:100%; white-space: nowrap">
            							
            						</table>
            					</div>
            				</div>
            			</div>
            		</div>
            	</div>
            </div>

            <div id="product_transfer" class="tab-pane fade">
                <div class="row gutter">
					<div class="col-lg-12">
						<div class="panel">
							<div class="panel-heading">
								<h4>Transfer product</h4>
							</div>
							<div class="panel-body">
								<div class="">
									<div class="">
										<div class="table-responsive">
											<table id='' class="table table-bordered">
												
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
</div>
</div>
<!-- sp transfer modal start -->
<div class="modal fade disableOutsideClick" id="sp_trans" role="dialog">
    <div class="modal-dialog modal-md">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Transfer service provider (<span id="sp_name"></span>)</h4>
            </div>
            <div class="modal-body">
                <form action="javascript:void(0)" method="" id="sp_tra_form">
                    <div class="row">
                        <div class="form-group">
                            <div class="col-md-3 text-right">Transfer type :</div>
                            <div class="col-md-9">
                                <select class="form-control sp_tra_type">
                                    <option value="1">Permanent</option>
                                    <option value="2">Temporary</option>
                                </select>
                            </div>
                            <div class="clearfix"></div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group">
                            <div class="col-md-3 text-right">Select Date :</div>
                            <div class="col-md-9 sp_tra_date">
                                <input type="text" class="form-control datetr" readonly />
                            </div>
                            <div class="clearfix"></div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group">
                            <div class="col-md-3 text-right">Move to branch :</div>
                            <div class="col-md-9">
                                <?php $branches = query_by_id("SELECT * FROM salon_branches WHERE id != '".$branch_id."' AND status='1'",[],$conn); ?>
                                <select class="form-control sp_tra_branch">
                                    <option value="">--Select--</option>
                                    <?php 
                                        foreach($branches as $branch){
                                            ?>
                                            <option value="<?= $branch['id'] ?>"><?= ucfirst($branch['branch_name']) ?></option>
                                            <?php
                                        }
                                    ?>
                                </select>
                            </div>
                            <div class="clearfix"></div>
                        </div>
                    </div>
                    <input type="hidden" class="sp_id" />
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success sp_tra_button"><i class="fa fa-exchange" aria-hidden="true"></i>Transfer</button>
                <button type="button" onclick="$('#sp_tra_form')[0].reset();" id="cancel_button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times" aria-hidden="true"></i> Close</button>
            </div>
        </div>
    </div>
</div>
<!-- sp transfer modal end -->

<div class="modal fade disableOutsideClick" id="sp_shedule" role="dialog">
    <div class="modal-dialog modal-md">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title"><i class="fa fa-exclamation-triangle mr-left-0 text-warning" aria-hidden="true"></i> Warning</h4>
            </div>
            <div class="modal-body table-responsive">
                <p>Already appointment are booked for date(s) you selected</p><br />
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Appointment date</th>
                            <th>Client name</th>
                            <th>Appointment time</th>
                        </tr>
                    </thead>
                    <tbody class="sp_shedule_detail">

                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <span class="pull-left" style="font-size: 18px;">Do you want to transfer?</span>
                <button type="button" class="btn btn-success" data-dismiss="modal">Yes</button>
                <button type="button" onclick="$('.sp_tra_date input').val('');" class="btn btn-danger" data-dismiss="modal">No</button>
            </div>
        </div>
    </div>
</div>

<?php include "footer.php";?>
<script>
    var alerts = $('#toast-container');
    $(document).ready(function(){
        datechange();
        $('.sp_tra_button').on('click', function(){
            var transfer_type = $('.sp_tra_type').val();
            var date = $('.sp_tra_date input').val();
            var branch = $('.sp_tra_branch').val();
            var sp_id = $('.sp_id').val();
            var errors = [];
            if(transfer_type == ''){
                alerts.remove();
                toastr.error('Please select Transfer type');
            } else if(date == ''){
                alerts.remove();
                toastr.error('Please select Date');
            } else if(branch == ''){
                alerts.remove();
                toastr.error('Please select Branch');
            } else {
                $.ajax({
                    url : 'ajax/transfers.php',
                    type : 'post',
                    data : { action : 'transfer_service_provider', sp_id : sp_id, date : date, moved_to_branch : branch, transfer_type : transfer_type},
                    dataType : 'json',
                    success : function(response){
                        if(response.status == 1){
                            toastr.success('Service provider moved successfully');
                            $('.row_'+sp_id).remove();
                            $('#cancel_button').click();
                        } else if(response.status == 0){
                            toastr.error('Some error occured! please try again');
                        } else {}
                    }
                }); 
            }
        });
    });

    function datechange(){
        $('.sp_tra_date input').on('change', function(){
            var sp_id = $('.sp_id').val();
            var date = $(this).val();
            if(sp_id == ''){
                alerts.remove();
                toastr.error('Service provider not selected properly');
            } else if(date == ''){
                alerts.remove();
                toastr.error('Please select date');
            } else {
                $.ajax({
                    url : 'ajax/transfers.php',
                    type : 'post',
                    data : {action : 'check_provider_schedule_migrate', date : date, sp_id : sp_id},
                    dataType : 'json',
                    success : function(response){
                        if(response.status == 0){
                            var html = '';
                            $.each(response.records, function(key, value){
                                html +='<tr><td>'+value.doa+'</td><td>'+value.client+'</td><td>'+value.itime+'</td></tr>';
                            });
                            $('.sp_shedule_detail').html(html);
                            $('#sp_shedule').modal('show');
                        }
                    }
                });
            }
        });
    }

    function transfer_sp_function(sp_id, sp_name){
        datecall();
        $('.sp_id').val(sp_id);
        $('#sp_name').text(sp_name);
        $('#sp_trans').modal('show');
    }

    function daterangecall(){
        $('input[range-attr="daterange"]').daterangepicker({
            opens: 'right'
        });
    }

    function datecall(){
        $('.datetr').datepicker({
            format: "yyyy-mm-dd",
            todayBtn: "linked",
            todayHighlight: true,
            autoclose: true,
            weekStart: 1,
            startDate: new Date()
        });
    }

    $('.sp_tra_type').on('change', function(){
        var type = $(this).val();
        if(type == 1){
            var input = '<input type="text" class="form-control datetr" readonly />';
            $('.sp_tra_date').html(input);
            $('.daterangepicker').remove();
            datechange();
            datecall();
        }
        if(type == 2){
            var input = '<input type="text" class="form-control" value="" range-attr="daterange" readonly />';
            $('.sp_tra_date').html(input);
            datechange();
            daterangecall();
            $('.sp_tra_type input').val('');
        }
    });

</script>