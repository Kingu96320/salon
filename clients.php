<?php 
	include "./includes/db_include.php";
 	$branch_id = $_SESSION['branch_id'];
	if(isset($_GET['id']) && $_GET['id']>0)
	{
		$id = $_GET['id'];
		$edit = query_by_id("SELECT * from client where id=:id and branch_id='".$branch_id."'",["id"=>$id],$conn)[0];
	}

	$col_code = array("0"=>"#641E16","1"=>"#78281F","2"=>"#512E5F","3"=>"#4A235A","4"=>"#154360","5"=>"#1B4F72","6"=>"#0E6251","7"=>"#0B5345","8"=>"#145A32","9"=>"#186A3B","10"=>"#7D6608","11"=>"#7E5109","12"=>"#784212","13"=>"#6E2C00","14"=>"#1B2631","15"=>"#17202A");
	$append = "";
	$cvisit = "";
	$cgrps = "";
	$gen = "";
	$min = 0;
	$max = 0;
	
	if(isset($_POST['follow-up'])){
	    $cid = addslashes(trim($_POST['client_id']));
	    $date = $_POST['followup-date'];
	    $time = date('H:i',strtotime($_POST['followup-time']));
	    $response = htmlspecialchars(trim($_POST['followup-response']));
	    if($_SESSION['user_type'] == 'superadmin'){
	        $is_superadmin = 1;
	    } else {
	        $is_superadmin = 0;
	    }
	    query("INSERT INTO client_followups SET client_id='".$cid."', follow_up_date='".$date."', follow_up_time='".$time."', response='".$response."', added_by='".$_SESSION['uid']."', branch_id='".$branch_id."', is_superadmin='".$is_superadmin."'",[],$conn);
	    $_SESSION['t']  = 1;
		$_SESSION['tmsg']  = "Follow up added Successfully";
		echo '<meta http-equiv="refresh" content="0; url=clients.php" />';die();
	}
	
	if(isset($_POST['submit'])){
		$client             = addslashes(trim($_POST['name']));
		$client             = addslashes(trim(ucfirst($client)));
		$cont 	            = addslashes(trim($_POST['cont']));
		$referral_code      = addslashes(trim(strtoupper(substr($client,0,4)).strtoupper(substr(md5(sha1($cont)),0,4))));
		$gst 	            = addslashes(trim($_POST['gst']));
		$dob 	            = addslashes(trim($_POST['dob']));
		$gender             = addslashes(trim($_POST['gender']));
		$email 	            = addslashes(trim($_POST['email']));
		$addr 	            = addslashes(trim($_POST['addr']));
		$aniv 	            = addslashes(trim($_POST['aniv']));
        $col_inc = 0;
        $col_id_qry =  query_by_id("SELECT cal_color from client where branch_id='".$branch_id."' order by id desc limit 1",[],$conn);
        if( $col_id_qry > 0){
            $col_inc = (int)$col_id_qry['cal_color'] + 1;
            if($col_inc>15){
                $col_inc = 0;
			}
		}
		$check_client = query_by_id("SELECT COUNT(*) as total FROM client WHERE cont='".$cont."' AND active='0' AND branch_id='".$branch_id."'",[],$conn)[0]['total'];
		if($check_client <= 0){
    		$client_id=get_insert_id("INSERT INTO `client` set `name`=:name,`cont`=:cont,`referral_code`=:referral_code,,`gst`=:gst,`email`=:email,`address`=:address,`gender`=:gender,`dob`=:dob,`aniversary`=:anivesary,`cal_color`=:cal_color,`active`=:active, `branch_id`='".$branch_id."'",[
                'name'=>$client,
                'cont'=>$cont,
    			'referral_code'=>$referral_code,
                'gst'=>$gst,
                'email'=>$email,
                'address'=>$addr,
                'gender'=>$gender,
                'dob'=>$dob,
                'anivesary'=>$aniv,
                'cal_color'=>$col_inc,
                'active'=>0
                ],$conn);
		    query("INSERT INTO wallet SET iid='0', date = '".date('Y-m-d')."', client_id = '$client_id', wallet_amount='0', get_wallet_from='', status='1', branch_id='".$branch_id."'",[],$conn);
		    $_SESSION['t']  = 1;
		    $_SESSION['tmsg']  = "Client added successfully";
		} else {
		    $_SESSION['t']  = 1;
		    $_SESSION['tmsg']  = "Client already exist";
		}
		echo '<meta http-equiv="refresh" content="0; url=clients.php" />';die();
	}
	

	
	if(isset($_GET['del_id'])){
		$del=$_GET['del_id'];
		query("UPDATE `client` SET `active`=1 WHERE id='".$del."' and branch_id='".$branch_id."'",[],$conn);
        query("UPDATE `wallet` SET `status`=0 WHERE client_id='".$del."' and branch_id='".$branch_id."'",[],$conn);
		query("UPDATE `wallet_history` SET `status`=0 WHERE client_id='".$del."' and branch_id='".$branch_id."'",[],$conn);
		$_SESSION['t']  = 1;
		$_SESSION['tmsg']  = "Client Removed Successfully";
		echo '<meta http-equiv="refresh" content="0; url=clients.php" />';die();
	}
	$existing_client = 0;
	$active_client = 0;
	$churn_prediction = 0;
	$defected_client = 0;
	$client_type = query_by_id("SELECT id from client where active='0' and branch_id='".$branch_id."'",[],$conn);
	if($client_type){
		foreach($client_type as $client_row){
			$customer_type=customer_type($client_row['id']);
			if($customer_type === 'active')
			    $active_client ++;
			else if($customer_type === 'churn_prediction')
			    $churn_prediction ++;
			else if($customer_type === 'inactive'){
			   $defected_client++;
			} else {
			    
			}
			$existing_client++;
		}
	}
	include "topbar.php";
	include "header.php";
	include "menu.php";
?>
<style>
    #empTable tr th:nth-child(2), #empTable tr td:nth-child(2){
        text-align:center;
        vertical-align:middle;
    }
    .tmps{
        border: 1px solid #ccc;
        border-radius: 3px;
        padding: 10px;
        cursor: pointer;
        margin-bottom: 10px;
    }
</style>
<!-- Dashboard wrapper starts -->
<div class="dashboard-wrapper">
	
	<!-- Main container starts -->
	<div class="main-container">
		
		<!-- Row starts -->
		<div class="row gutter">
			
			<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
				<div class="panel">
					<div class="panel-heading">
						<h4>Clients segmentation</h4>
					</div>
					<div class="panel-body">
						<div class="row client-counter">
							<div class="col-lg-3 col-md-3 col-sm-6" onclick="fetch_clients();" style="padding-bottom:15px">
							
								<div class="badge-box existingClients" style="background:url(img/statistic-box-purple.png);"  >
									<h3 style="color:#fff; font-size:54px;"><?=$existing_client?></h3>
									<p class="text-white">Existing Clients</p>
									<small class="text-white">Clients who are existing in the <br class="visible-sm"> software.</small>
								</div>
								
							</div>
							<div class="col-lg-3 col-md-3 col-sm-6" style="padding-bottom:15px">
								
								<div class="badge-box activeClients" style="background:url(img/statistic-box-green.png);" onclick="fetch_clients('active','Acitve clients');">
									<h3 style="color:#fff; font-size:54px;"><?=$active_client?></h3>
									<p class="text-white">Active</p>
									<small class="text-white">Clients who visit your outlet at regular intervals.</small>
									
								</div>
								
							</div>
							<div class="col-lg-3 col-md-3 col-sm-6" style="padding-bottom:15px">
								
								<div class="badge-box churnClients" style="background:url(img/statistic-box-yellow.png);" onclick="fetch_clients('churn_prediction','Churn prediction');" >
									
									<h3 style="color:#fff; font-size:54px;"><?=$churn_prediction?></h3>
									<p class="text-white">Churn prediction</p>
									<small class="text-white">Clients who haven't visited your outlet and who are likely to leave.</small>
								</div>
								
							</div>
							<div class="col-lg-3 col-md-3 col-sm-6" style="padding-bottom:15px">
								
								<div class="badge-box defectedClients" style="background:url(img/statistic-box-red.png);" onclick="fetch_clients('inactive','Defected clients');" >
									
									<h3 style="color:#fff; font-size:54px;"><?=$defected_client?></h3>
									<p class="text-white">Defected clients</p>
									<small class="text-white">Clients who haven't visited your outlet and become inactive.</small>
									
								</div>
								
							</div>
							
						</div>
						
					</div>
					
				</div>
				
				<!-- Row starts -->
				<div class="row gutter">
					<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
						<div class="panel">
							<div class="panel-heading heading-with-btn">
								<h4 class="pull-left">Manage clients</h4>
								<?php if(isset($_SESSION['user_type']) && $_SESSION['user_type'] == 'superadmin'){ ?>
								<span data-toggle="modal" data-target="#uploadexcel"><button class="btn btn-primary pull-right upload-btn" data-toggle="tooltip" data-placement="top" data-html="true" title="To upload please fill your data in pre-defined excel sheet."><i class="fa fa-upload" aria-hidden="true"></i> Upload</button></span>
								<span id="download-btn"></span>		
								<?php } ?>
								<span data-toggle="modal" data-target="#add_new_Client_modal" onClick="reset_modal();"><button type="button" class="btn btn-success pull-right"><span class="fa fa-plus mr-left-0" aria-hidden="true"></span>Add New Client</button></span>
								<div class="clearfix"></div>
							</div>
							<div class="clearfix"></div><br />
							<form action="" method="get">
						        <div class="col-lg-2 col-md-2 col-sm-3 col-xs-12">
        							<div class="form-group">
        								<label for="">Client id</label>
        								<input type="number"  class="form-control" name="uid" value="<?= isset($_GET['uid'])?$_GET['uid']:'' ?>">
        							</div>
        						</div>
        						<div class="col-lg-2 col-md-2 col-sm-3 col-xs-12">
        							<div class="form-group">
        								<label for="">Client name</label>
        								<input type="text"  class="form-control" name="name" value="<?= isset($_GET['name'])?$_GET['name']:'' ?>">
        							</div>
        						</div>
        						<div class="col-lg-2 col-md-2 col-sm-3 col-xs-12">
        							<div class="form-group">
        								<label for="">Contact number</label>
        								<input type="number"  class="form-control" name="number" value="<?= isset($_GET['number'])?$_GET['number']:'' ?>">
        							</div>
        						</div>
        						<div class="col-lg-2 col-md-2 col-sm-3 col-xs-12">
        							<div class="form-group">
        								<label for="">Email</label>
        								<input type="email"  class="form-control" name="email" value="<?= isset($_GET['email'])?$_GET['email']:'' ?>">
        							</div>
        						</div>
        						<div class="col-lg-2 col-md-2 col-sm-3 col-xs-12">
        							<div class="form-group">
        								<label for="">Source</label>
        								<select class="form-control" name="source">
    										<option value="">-- Select--</option>
    										<option <?= isset($_GET['source'])&&$_GET['source']=='Client refrence'?'selected':'' ?> value="Client refrence">Client refrence</option>
    										<option <?= isset($_GET['source'])&&$_GET['source']=='Cold Calling'?'selected':'' ?> value="Cold Calling">Cold Calling</option>
    										<option <?= isset($_GET['source'])&&$_GET['source']=='Facebook'?'selected':'' ?> value="Facebook">Facebook</option>
    										<option <?= isset($_GET['source'])&&$_GET['source']=='Twitter'?'selected':'' ?> value="Twitter">Twitter</option>
    										<option <?= isset($_GET['source'])&&$_GET['source']=='Instagram'?'selected':'' ?> value="Instagram">Instagram</option>
    										<option <?= isset($_GET['source'])&&$_GET['source']=='Other Social Media'?'selected':'' ?> value="Other Social Media">Other Social Media</option>
    										<option <?= isset($_GET['source'])&&$_GET['source']=='Website'?'selected':'' ?> value="Website">Website</option>
    										<option <?= isset($_GET['source'])&&$_GET['source']=='Walk-In'?'selected':'' ?> value="Walk-In">Walk-In</option>
    										<option <?= isset($_GET['source'])&&$_GET['source']=='Flex'?'selected':'' ?> value="Flex">Flex</option>
    										<option <?= isset($_GET['source'])&&$_GET['source']=='Flyer'?'selected':'' ?> value="Flyer">Flyer</option>
    										<option <?= isset($_GET['source'])&&$_GET['source']=='Newspaper'?'selected':'' ?> value="Newspaper">Newspaper</option>
    										<option <?= isset($_GET['source'])&&$_GET['source']=='SMS'?'selected':'' ?> value="SMS">SMS</option>
    										<option <?= isset($_GET['source'])&&$_GET['source']=='Street Hoardings'?'selected':'' ?> value="Street Hoardings">Street Hoardings</option>
    										<option <?= isset($_GET['source'])&&$_GET['source']=='Event'?'selected':'' ?> value="Event">Event</option>
    										<option <?= isset($_GET['source'])&&$_GET['source']=='TV/Radio'?'selected':'' ?> value="TV/Radio">TV/Radio</option>
    									</select>
        							</div>
        						</div>
        						<?php
        						    $sp = query_by_id("SELECT * from beauticians where active=0 and type=2 and branch_id='".$branch_id."' order by name ASC",[],$conn);
        						?>
        						<div class="col-lg-2 col-md-2 col-sm-3 col-xs-12">
        							<div class="form-group">
        								<label for="">Assigned to</label>
        								<select class="form-control" name="spname">
        								    <option value="">-- Select--</option>
        								    <?php
        								        foreach($sp as $sp){ ?>
        								            <option <?= isset($_GET['spname'])&&$_GET['spname']==$sp['id']?'selected':'' ?> value="<?= $sp['id'] ?>"><?= $sp['name'] ?></option>
        								        <?php }
        								    ?>
        								</select>
        							</div>
        						</div>
        						<div class="col-lg-2 col-md-2 col-sm-3 col-xs-12">
        							<div class="form-group">
        								<label for="">Service</label>
        								<input type="text"  class="ser form-control" name="sname" value="<?= isset($_GET['sname'])?$_GET['sname']:'' ?>">
        								<input type="hidden" class="serr" name="sid" value="<?= isset($_GET['sid'])?$_GET['sid']:'' ?>" />
        							</div>
        						</div>
								<div class="col-lg-2 col-md-2 col-sm-3 col-xs-12">
								<div class="form-group">
        								<label for="">Gender</label>
        								<select class="form-control" name="gender">
    										<option value="">-- Select--</option>
    										<option  <?= isset($_GET['gender'])&&$_GET['gender']=='1'?'selected':'' ?> value="1">Male</option>
    										<option  <?= isset($_GET['gender'])&&$_GET['gender']=='2'?'selected':'' ?> value="2">Female</option>
    									</select>
        							</div>
        						</div>
        						<div class="col-md-2">
								    <lable>&nbsp;</lable>
								    <div class="form-group">
								        <button type="submit" name="filter" value="mfilter" class="btn btn-filter btn-sm btn-block"><i class="fa fa-filter" aria-hidden="true"></i>Filter</button>
								    </div>
								</div>
								<div class="col-md-2">
								    <lable>&nbsp;</lable>
								    <div class="form-group">
								        <a href="clients.php" class="btn btn-danger btn-sm d-block"><i class="fa fa-times" aria-hidden="true"></i>Clear</a>
								    </div>
								</div>
							</form>
							<div class="clearfix"></div>
							<div class="panel-body">
								
								<!-- Modal -->
								<div class="modal fade" id="myModal" role="dialog">
									<div class="modal-dialog">
										<!-- Modal content-->
										<div class="modal-content">
											<div class="modal-header">
												<button type="button" class="close" data-dismiss="modal">&times;</button>
												<h4 class="modal-title">SEND SMS</h4>
											</div>
											<div class="modal-body">
											    <div class="row">
        										    <div class="col-md-6">
        										        <div class="form-group">
        										            <label>Category</label>
        										            <select class="form-control" id="template_category" onchange="getTemplateType(this.value)">
                
        										            </select>
        										        </div>
        										    </div>
        										    <div class="col-md-6">
        										        <div class="form-group">
        										            <label>Template type</label>
        										            <select class="form-control" id="template_type" onchange="getTemplate(this.value)">
        										                <option>--Select--</option>
        										                
        										            </select>
        										        </div>
        										    </div>
        										</div>
												<div id="msg_body"></div>
												<br>
												<div id="send_button"></div>
												<div class="clearfix"></div>
												<div class="notes">
                                        			<p><i class="fa fa-hand-o-right" aria-hidden="true"></i>Important notes:</p>
                                        			<ol>
                                        				<li>* Don't replace <b>{#name#}</b>, <b>{#salon_name#}</b>, <b>{#booking_link#}</b>, <b>{#referral_point#}</b> variables.</li>
                                        				<li>* One variable {#__#} text should be less then 30 chars.</li>
                                        				<li>* No extra content is allowed in approved templates, you can only replace {#__#}.</li>
                                        			</ol>
                                        		</div>
												
											</div>
										</div>
										
									</div>
								</div>
								<!-- Modal End --> 
								<div class="row">
									<div class="col-lg-12">
										<div class="table-responsive">
											<table id='empTable' class="table table-bordered " style="width:100%;">
												<thead>
													<tr>
														<th><input type="checkbox" value="0" id="select-all"></th>
														<th>Id</th>
														<th>Name</th>
														<th>Contact number</th>
														<th>Your Invite Code</th>
														<th>First visit</th>
														<th>Last visit</th>
														<th>Last service</th>
														<th>Last service provider</th>
														<th>Last bill amount</th>
														<th>Gender</th>
														<th>Points</th>
														<th width="165">Action</th>
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
		</div>
		<!-- Row ends -->
		
	</div>
	<!-- Main container ends -->
	
</div>
<!-- Dashboard Wrapper End -->

</div>
<!-- Container fluid ends -->

<!-- modal to select templates to send messages -->
<div class="modal fade disableOutsideClick" id="final_templates" role="dialog">
	<div class="modal-dialog  modal-lg">
		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header">
			    <button type="button" class="close" data-dismiss="modal">&times;</button> 
				<h4 class="modal-title"><span id="mtitle">Choose template</h4>
			</div>
			<div class="modal-body">
				<div class="panel-body row">
				    
			    </div>
			</div>
		</div>
	</div>
</div>
<!-- modal end -->

<!-- Modal -->
<div class="modal fade disableOutsideClick" id="add_new_Client_modal" role="dialog">
	<div class="modal-dialog  modal-lg">
		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title"><span id="mtitle">Add new</span> client</h4>
			</div>
			<div class="modal-body">
				<div class="panel-body">
					<div class="row">
						<form action="" method="post" id="add-client-form">
							<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
								<div class="form-group">
									<label for="userName">Client name <span class="text-danger">*</span></label>
									<input type="text" class="form-control" name="name" placeholder="Client Name" value="<?= isset($id)?$edit['name']:old('name')?>"  id="name" required>
									<input type="hidden" name="edit_id" id="id" value="" >
								</div>
							</div>
							<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
								<div class="form-group">
									<label for="userName">Contact <span class="text-danger">*</span></label>
									<input type="text"  onkeyup="this.value = this.value.replace(/[^0-9\.]/g,'');" maxLength="<?= PHONE_NUMBER ?>" class="form-control" onBlur="check();contact_no_length($(this), this.value);" name="cont" id="cont" placeholder="Contact" value="<?= isset($id)?$edit['cont']:old('name')?>" required>
									<span style="color:red" id="client-status"></span>
									<span style="color:red" id="digit_error"></span>
								</div>
							</div>
							<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
								<div class="form-group">
									<label for="userName">Date of birth</label>
									<input type="text" class="form-control dob_annv_date" name="dob" id="dob" value="<?= isset($id)?$edit['dob']:old('name')?>" readonly>
								</div>
							</div>
							
							<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
								<div class="form-group">
									<label for="userName">Anniversary</label>
									<input type="text" class="form-control dob_annv_date" name="aniv" id="aniversary"  value="<?= isset($id)?$edit['aniversary']:old('name')?>" readonly>
								</div>
							</div><div class="clearfix"></div>
							<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
								<div class="form-group">
									<label for="userName">Gender :</label>
									<select name="gender" class="form-control" id="gender">
										<?php  if(isset($id))  { 
										$gd = $edit['gender'];  ?>
										<option <?php if($gd=="1") echo "selected"; ?> value="1">Male</option>
										<option <?php if($gd=="2") echo "selected"; ?> value="2">Female</option>
										<?php  }  else  {  ?>
										<option value="1">Male</option>
										<option value="2">Female</option>
										<?php  }  ?>
										
									</select>
								</div>
							</div>
							
							<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
								<div class="form-group">
									<label for="userName">Email</label>
									<input type="email" class="form-control" name="email" id="email" value="<?=isset($id)?$edit['email']:old('name')?>" placeholder="Email">
								</div>
							</div>
							<div class="col-lg-3 col-md-3 col-md-3 col-xs-12">
								<div class="form-group">
									<label for="clientsource">Source of client</label>
									<select class="form-control" name="clientsource" id="clientsource">
										<option value="">-- Select--</option>
										<option value="Client refrence" <?= isset($id)?$edit['leadsource']=='Client refrence'?'selected':'':''?> >Client refrence</option>
										<option value="Cold Calling" <?= isset($id)?$edit['leadsource']==''?'selected':'':''?>>Cold Calling</option>
										<option value="Facebook" <?= isset($id)?$edit['leadsource']=='Facebook'?'selected':'':''?>>Facebook</option>
										<option value="Twitter" <?= isset($id)?$edit['leadsource']=='Twitter'?'selected':'':''?>>Twitter</option>
										<option value="Instagram" <?= isset($id)?$edit['leadsource']=='Instagram'?'selected':'':''?>>Instagram</option>
										<option value="Other Social Media" <?= isset($id)?$edit['leadsource']=='Other Social Media'?'selected':'':''?>>Other Social Media</option>
										<option value="Website" <?= isset($id)?$edit['leadsource']=='Website'?'selected':'':''?>>Website</option>
										<option value="Walk-In" <?= isset($id)?$edit['leadsource']=='Walk-In'?'selected':'':''?>>Walk-In</option>
										<option value="Flex" <?= isset($id)?$edit['leadsource']=='Flex'?'selected':'':''?>>Flex</option>
										<option value="Flyer" <?= isset($id)?$edit['leadsource']=='Flyer'?'selected':'':''?>>Flyer</option>
										<option value="Newspaper" <?= isset($id)?$edit['leadsource']=='Newspaper'?'selected':'':''?>>Newspaper</option>
										<option value="SMS" <?= isset($id)?$edit['leadsource']=='SMS'?'selected':'':''?>>SMS</option>
										<option value="Street Hoardings" <?= isset($id)?$edit['leadsource']=='Street Hoardings'?'selected':'':''?>>Street Hoardings</option>
										<option value="Event" <?= isset($id)?$edit['leadsource']=='Event'?'selected':'':''?>>Event</option>
										<option value="TV/Radio" <?= isset($id)?$edit['leadsource']=='TV/Radio'?'selected':'':''?>>TV/Radio</option>
									</select>
								</div>
							</div>
							<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
								<div class="form-group">
									<label for="userName">Address</label>
									<input type="text" class="form-control" name="addr" id="address" value="<?=isset($id)?$edit['address']:old('name')?>"placeholder="Address">
								</div>
							</div>
							
							
							<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
								<div class="form-group">
									
									<button type="button" class="btn btn-danger mr-left-5 pull-right" data-dismiss="modal" onclick="$('#mtitle').text('Add new');"><i class="fa fa-times" aria-hidden="true"></i> Close</button>
									<button type="submit" name="edit-submit" class="btn btn-info pull-right update-button" style="display:none"><i class="fa fa-pencil-square-o" aria-hidden="true"></i>Update client</button>
									<button type="submit" name="submit" class="btn btn-success pull-right add-button" ><span class="fa fa-plus mr-left-0" aria-hidden="true"></span>Add new client</button>									
								</div>
							</div>
						</form>
					</div> 
				</div>
			</div>
			<br>
			<div class="modal-footer">
			</div>
		</div>
		
	</div>
</div>
<!-- Modal End --> 

<!-- Modal -->
<div class="modal fade disableOutsideClick" id="add_follow_up" role="dialog">
	<div class="modal-dialog ">
		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title"><span id="mtitle">Add new</span> followup</h4>
			</div>
			<div class="modal-body">
				<div class="panel-body">
					<div class="row">
						<form action="" method="post" id="add-client-form">
							<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
								<div class="form-group">
									<label for="">Next Followup Date : <span class="text-danger">*</span></label>
									<input type="text" name="followup-date" placeholder="" class="min_present_date form-control" value="<?= date('Y-m-d'); ?>" readonly required />
								</div>
							</div>
							<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
								<div class="form-group">
									<label for="">Next Followup Time : <span class="text-danger">*</span></label>
									<input type="text" name="followup-time" placeholder="" class="maintime form-control" value="<?= date('h:i A'); ?>" required />
								</div>
							</div>
							<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
								<div class="form-group">
									<label for="userName">Response : <span class="text-danger">*</span></label>
									<textarea name="followup-response" style="resize:none;" rows="5" class="form-control" required></textarea>
								</div>
							</div>
							<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
								<div class="form-group">
								    <input type="hidden" name="client_id" id="cfid" value="" />
									<button type="button" class="btn btn-danger mr-left-5 pull-right" data-dismiss="modal" onclick="$('#mtitle').text('Add new');"><i class="fa fa-times" aria-hidden="true"></i> Close</button>
									<button type="submit" name="follow-up" class="btn btn-success pull-right add-button" ><span class="fa fa-plus mr-left-0" aria-hidden="true"></span>Add</button>									
								</div>
							</div>
						</form>
					</div> 
				</div>
			</div>
		</div>
		
	</div>
</div>
<!-- Modal End --> 
<!-- Modal -->
<div id="uploadexcel" class="modal fade disableOutsideClick" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
         <!--<button type="button" class="close" data-dismiss="modal">&times;</button> -->
        <button onclick="samplefile('clients')" class="btn btn-warning pull-right"><i class="fa fa-download" aria-hidden="true"></i> Sample File (.xlsx)</button>
        <h4 class="modal-title">Upload clients(s) </h4>
      </div>
      <div class="modal-body">
		<p id="error_msg" class="text-danger"></p>
		<p>
			<input type="file" name="excelsheet" accept=".xlsx" id="clientExcel" />
		</p>
		<div class="notes">
			<p><i class="fa fa-hand-o-right" aria-hidden="true"></i>Important notes:</p>
			<ol>
				<li>* File should be in .xlsx format.</li>
				<li>* Client name, Contact number must be filled in sheet. Records with empty field will not upload.</li>
				<li>* Contact length should be <b>10 digit</b>.</li>
				<li>* Client with existing contact number will not upload.</li>
				<li>* Gender should be mention as <b>Male</b> or <b>Female</b>.</li>
				<li>* Date of birth/ Anniversary date format should be in <b>YYYY-MM-DD</b> format <br />Example : (<?php echo date('Y-m-d') ?>)</li>
			</ol>
		</div>
      </div>
      <div class="modal-footer">
      	<button type="button" data-attr="submit" class="btn btn-success" onclick="uploadClients()"><i class="fa fa-upload" aria-hidden="true"></i> Upload</button>
        <button type="button" class="btn btn-danger" data-dismiss="modal" onclick="clearModalForm('error_msg','uploadexcel')"><i class="fa fa-times" aria-hidden="true"></i> Close</button>
      </div>
    </div>

  </div>
</div>
<!-- Modal to upload excel sheet end  -->
<script>
    
    $(document).ready(function(){
        getTemplateCategory();
    });
    
    function getTemplateCategory(){
        $.ajax({
           url : '<?= SMS_API_URL ?>sms_templates.php',
           type : 'post',
           data : {action : 'get_template_category'},
           success : function(res){
               if(res != ''){
                   $('#template_category').html(res);
               }
           }
        });
    }
    
    function getTemplateType(value){
        if(value == ''){
            $('#template_type').html('<option value="">--Select--</option>');
            $('#msg_body, #send_button').html('');
        } else {
            $.ajax({
                url : '<?= SMS_API_URL ?>sms_templates.php',
                type : 'post',
                data : {action : 'getTemplate', category : value},
                success : function(res){
                    if(res != ''){
                        $('#template_type').html('<option value="">--Select--</option>'+res);
                    } else {
                        $('#template_type').html('<option value="">--Select--</option>');
                    }
                }
            });
        }
    }
    
    function getTemplate(value){
        var template_category = $('#template_category').val();
        if(value == ''){
            $('#msg_body, #send_button').html('');
        } else {
            $.ajax({
                url : '<?= SMS_API_URL ?>sms_templates.php',
                type : 'post',
                data : {action : 'getMessages', group_name : value, template_category : template_category},
                success : function(res){
                    if(res != ''){
                        $('#final_templates .panel-body').html(res);
                        $('#final_templates').modal('show');
                    } else {
                        $('#msg_body, #send_button').html('');
                    }
                }
            });
        }
    }
    
    function useTemplate(tempid, sendas, element){
        var html = '<label for="userName">Message</label><br /><span class="text-danger">* Please check SMS content and update variable values before sending SMS.</span>';
        html += '<input type="hidden" id="tempid" value="'+tempid+'">';
        html += '<input type="hidden" id="sendas" value="'+sendas+'">';
        html += '<textarea id="message" style="height: 100px;" class="form-control">'+element.html()+'</textarea><p class="text-success" id="message_show"></p>';
        $('#msg_body').html(html);
        $('#send_button').html('<button type="submit" style="float : right;" onclick="sendmessage()" class="btn btn-success" id="final_submit">SEND SMS</button>');
        $('#final_templates').modal('hide');
    }

    function reset_followup_modal(id){
        $('#cfid').val(id);
    	$('#add_follow_up').find('textarea').val("");
    }
	
	$(function(){
		
		$('#add-client-form').on('submit',function(e){
			e.preventDefault();
			var edit_id = $('#id').val();
			var qstr="";
			if(edit_id > 0){
				qstr = "?edit_id="+edit_id;
			} 
			$.ajax({
				url: "ajax/client_form_submit.php"+qstr,
				beforeSend: function () {
					$("button").prop("disabled", true);
				},
				'complete': function () { $('.add-button').find('i').remove();
					$("button").prop("disabled", false);
					if(edit_id > 0){
						toastr.success("Client Updated Successfully");
					} else {
				// 		toastr.success("Client Added Successfully");	
					}
					$('#add_new_Client_modal').modal('toggle');
				 },				
				type: "POST",             
				data: new FormData(this), // Data sent to server, a set of key/value pairs (i.e. form fields and values)
				contentType: false,       // The content type used when sending data to the server.
				cache: false,             // To unable request pages to be cached
				processData:false,        // To send DOMDocument or non processed data file it is set to false
				success: function(data)   // A function to be called if request succeeds
				{
					
					var jd=JSON.parse(data);
					
					if(jd['data-inserted'] == '1'){
						$("input").val("");
						$('#empTable').DataTable().destroy();
						$('#empTable').DataTable({
							'processing': true,
							'serverSide': true,
							'serverMethod': 'post',
							'aaSorting': [[ 1, "asc" ]],
							'ajax': {
								'url':'ajax/fetch_clients_info.php',
							},
							'columns': [
							{ data: 'checkbox' },
							{ data: 'id' },
							{ data: 'name' },
							{ data: 'cont' },
							{ data: 'referral_code' },
							{ data: 'firstvisit' },
							{ data: 'lastvisit' },
							{ data: 'last_service' },
							{ data: 'last_service_provider' },
							{ data: 'last_bill_amount' },
							{ data: 'gender' },
							{ data: 'points' },
							{ data: 'action' },
							],
							'columnDefs': [ {
								'orderable': false, // set orderable false for selected columns
							}],
							
						});
						toastr.success("Client Added Successfully");	
						$('.update-button').hide();
						$('.add-button').show();
					} else if(jd['data-inserted'] == '2'){
					    toastr.error("Client already exist");	
					}
				},
				error: function (xhr, ajaxOptions, thrownError) {
					alert(xhr.status);
					alert(thrownError);
				}
			});
		});
		
		$(".ser").autocomplete({
			source: function(request, response) {
				$.getJSON("ajax/bill.php", { term: request.term}, response);
			},
			minLength: 1,
			select:function (event, ui) {  
				$('.serr').val(ui.item.id);
			}
		});
	});
	
	function fetch_clients(client_type,table_title){
		var ct="";
		if(client_type){
			ct='?client_type='+client_type;
			}else{
			table_title = 'Clients';
		}
		
		$('#empTable').DataTable().destroy();
		var table = $('#empTable').DataTable({
			'lengthMenu': [[10, 25, 50, 99999999], [10, 25, 50, 'All']],
			// 'aaSorting': [[ 1, "asc" ]],
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
			'destroy': true,
			'processing': true,
			'serverSide': true,
			'serverMethod': 'post',
			"fnDrawCallback": function (oSetStings) {
				$("#empTable_wrapper").find('.paging_simple_numbers').append('<li class="paginate_button"><button class="btn btn-info" style="float : right;" onclick="sendsms_old()"><i class="fa fa-paper-plane" aria-hidden="true"></i>Send SMS</button> </li>');
			},
// 			'aaSorting': [[ 0, "desc" ]],
			'ajax': {
				'url':'ajax/fetch_clients_info.php'+ct,
				'beforeSend': function () {
					if(table_title==='Acitve clients'){
					$('.activeClients').append('<div class="divloader" style="position:absolute;z-index:1;bottom: 30%;left: 15%;right: 15%;"><div class="divloader_ajax_small"></div></div>');
					}else if(table_title === 'Defected clients'){
					$('.defectedClients').append('<div class="divloader" style="position:absolute;z-index:1;bottom: 30%;left: 15%;right: 15%;"><div class="divloader_ajax_small"></div></div>');
					}else if(table_title === 'Churn prediction'){
					$('.churnClients').append('<div class="divloader" style="position:absolute;z-index:1;bottom: 30%;left: 15%;right: 15%;"><div class="divloader_ajax_small"></div></div>');
					}else{
					$('.existingClients').append('<div class="divloader" style="position:absolute;z-index:1;bottom: 30%;left: 15%;right: 15%;"><div class="divloader_ajax_small"></div></div>');
				}	 
				},
				'complete': function () { $('.divloader').fadeOut(function(){$('.divloader').remove();}); }
				
			},
			'columns': [
			{ data: 'checkbox' },
			{ data: 'id' },
			{ data: 'name' },
			{ data: 'cont' },
			{ data: 'referral_code' },
			{ data: 'firstvisit' },
			{ data: 'lastvisit' },
			{ data: 'last_service' },
			{ data: 'last_service_provider' },
			{ data: 'last_bill_amount' },
			{ data: 'gender' },
			{ data: 'points' },
			{ data: 'action' },
			],
			'columnDefs': [ {
				'targets': [0,7,8,10,11], // column index (start from 0)
				'orderable': false, // set orderable false for selected columns
			}]
			
		});
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
	}

	// function to upload clients from excel sheet start
	function uploadClients(){
		var errordiv = $('#error_msg');
		var submit_btn = $('#uploadexcel button[data-attr=submit]');
		var submit_btn_icon = $('#uploadexcel .btn-success i');
		errordiv.text('');
		var file_data = $('#clientExcel').prop('files')[0];
		var form_data = new FormData();
		form_data.append('excelsheet', file_data);
		form_data.append('module','clients');
		if(file_data == undefined){
			errordiv.text('Please select file.');
		} else {
			$.ajax({  
	            url:"ajax/upload_clients_excel.php",
	            method:"POST",
	            data: form_data,
	            contentType:false,
	            processData:false,
	            beforeSend: function() {
	            	submit_btn_icon.removeClass('fa-upload');
	            	submit_btn_icon.addClass('fa-spinner fa-spin');
	            	submit_btn.prop('disabled',true);
				},
	            success:function(data){
	            	// console.log(data);
	                if (data.status == 0) {
	                	errordiv.text(data.message);
	                	submit_btn_icon.removeClass('fa-spinner fa-spin');
	            		submit_btn_icon.addClass('fa-upload');
	                	submit_btn.prop('disabled',false);
	                }
	                else if(data.status == 1){
	                	location.reload(true);
	                }
	            }
	       });
		}
	}
	
	
	$('#select-all').click(function(event) {   
		if(this.checked) {
			$(':checkbox').each(function() {
				this.checked = true;                        
			});
			} else {
			$(':checkbox').each(function() {
				this.checked = false;                       
			});
		}
	});
	function confirmDelete()
	{
		return confirm('Are you sure?')
	}
	/*******Server_side_datatable*********/
		$(document).ready(function(){
		var table =	$('#empTable').DataTable({
				'lengthMenu': [[10, 25, 50, 100, 99999999], [10, 25, 50, 100, 'All']],
				//'aaSorting': [[ 1, "asc" ]],
				'dom': 'lBfrtip',				
				'buttons': [				
				{
					extend: 'excelHtml5',
					text: '<i class="fa fa-file-excel-o"></i> Excel',
					titleAttr: 'Export to Excel',
					title: 'Clients',
					exportOptions: {
						columns: ':not(:last-child)',
					}
				},
				],				
				'processing': true,
				'serverSide': true,
				'serverMethod': 'post',
				"fnDrawCallback": function (oSetStings) {
					$("#empTable_wrapper").find('.paging_simple_numbers').append('<li class="paginate_button"><button class="btn btn-info" style="float : right;" onclick="sendsms_old()"><i class="fa fa-paper-plane" aria-hidden="true"></i>Send SMS</button></li>');
				},
				'ajax': {
					'url':'ajax/fetch_clients_info.php<?= isset($_GET['filter'])?'?filter=mfilter&uid='.$_GET['uid'].'&name='.$_GET['name'].'&gender='.$_GET['gender'].'&number='.$_GET['number'].'&email='.$_GET['email'].'&source='.$_GET['source'].'&spname='.$_GET['spname'].'&sname='.$_GET['sname'].'&sid='.$_GET['sid']:'' ?>'
				},
				'columns': [
				{ data: 'checkbox' },
				{ data: 'id' },
				{ data: 'name' },
				{ data: 'cont' },
				{ data: 'referral_code' },
				{ data: 'firstvisit' },
				{ data: 'lastvisit' },
				{ data: 'last_service' },
				{ data: 'last_service_provider' },
				{ data: 'last_bill_amount' },
				{ data: 'gender' },
				{ data: 'points' },
				{ data: 'action' },
				],
				'columnDefs': [ {
					'targets': [0,7,8,10,11], // column index (start from 0)
					'orderable': false, // set orderable false for selected columns
				}]
			});
			
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

		});
		/*******End********/
			
			
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
					$('#myModal').modal('show');
					}else{
					    toastr.warning('Please Select at least one client');
				}
			}
			
			var ref_code_user_list = [];
			function send_refcode(Div){
			    ref_code_user_list = [];
			    var btn_html = Div.html();
			    var btn = Div;
			    var count = $('input.chkk:checked').length;
				if(count>0){
					$(".chkk").each(function(){
						if($(this).is(':checked'))
						{
							ref_code_user_list.push({name: $(this).data("name"), contact: $(this).data("contact"), ref_code: $(this).data("ref") });
						}
					});
					if(ref_code_user_list.length > 0){
					    $.ajax({
					        type : "POST",
        					url : "ajax/sendsms.php",
        					data : {
        						data: JSON.stringify(ref_code_user_list,true),
        						action: 'send_invite_code',
        					},
        					beforeSend: function() {
        					    btn.html('<i class="fa fa-spinner fa-spin" aria-hidden="true"></i>Sending...');
        					    btn.removeAttr('onclick');
        					},
        					success : function(response){
        					    toastr.success(response);
        					    $('.chkk').prop('checked',false);
        					    btn.html(btn_html);
        					    btn.attr('onclick','send_refcode($(this))');
        					}
					    });
					}
				}else{
					toastr.warning('Please Select at least one client');
				}
			}
			
			function sendsms(){
				json_values = [];
				var inc = 0;
				
				table_print.rows().every( function ( rowIdx, tableLoop, rowLoop ) {
					var data = this.data();
					var row = table_print.rows(rowIdx);
					var data1 = row.nodes();
					var contact_node = $(data1).find('.chkk');
					if($(contact_node).prop("checked") == true){
						inc++;
						json_values.push({name: $(contact_node).data("name"),contact: $(contact_node).data("contact") });
					}
				});
				if(inc>0){
					$('#myModal').modal('show');
					
					}else{
					toastr.warning('Please Select at least one client');
				}
			}
			
			function sendmessage() {
			    if(confirm('Have you replaced appropriate variable {#__#} values in SMS content?')){
    				var message = $('#message').val();
    				var sendtype = $('#sendas').val();
    				var tempid = $('#tempid').val();
    				$.ajax({
    					type: "POST",
    					url: 'ajax/sendsms.php',
    					data: {
    						json: JSON.stringify(json_values,true),
    						message: message,
    						sendtype : sendtype,
    						tempid : tempid
    					},
    					dataType: 'text',
    					beforeSend : function(){
    					  $('#final_submit').prop('disabled', true);
    					  $('#message_show').text('SMS process started, please do not refresh or close page.');
    					},
    					success: function(result) {
    						toastr.success(result);
    						$('#msg_body').html('');
    						$('#send_button').html('');
    						$('#template_category option[value=""]').prop('selected', true);
    						$('#template_type').html('<option value="">--Select--</option>');
    						$('#final_submit').prop('disabled', false);
    					    $('#message_show').text('');
    					    $('input[type="checkbox"]').prop('checked', false);
    					}
    				});
			    }
			}
			
			<?php if(!isset($_GET['id'])){?>
				function check() {
					jQuery.ajax({
						url: "checkccont.php?p="+$("#cont").val(),
						//data:'p='+$("#prod").val(),
						type: "POST",
						success:function(data){
							if(data == '1'){
								$("#client-status").html("Contact number already exists");
								$('#cont').val("");
							}else{
								$("#client-status").html("");
							}
						},
						error:function (){}
					});
				}
				
				function editclients_showmodal(clientId){
					$("#add_new_Client_modal").modal('show');
					$('#mtitle').text('Update');
					$('.add-button').hide();
					$('.update-button').show();
					$.ajax({
						url: "ajax/fetch_clients_info.php?clientID="+clientId,
						type: 'POST',
						success: function(data){
							var jd=JSON.parse(data);
							$('#add-client-form').find('input').each(function() {
								$('#'+$(this).attr('id')).val(jd[$(this).attr('id')]);
							});
							$('#email').val(jd['email']);
							$('#gender').val(jd['gender']);t
							$('#clientsource option[value="'+jd['leadsource']+'"]').prop('selected',true);
						},
						error: function (xhr, ajaxOptions, thrownError) {
							alert(xhr.status);
							alert(thrownError);
						}
					});
				}
				
				function reset_modal(){
					$('.update-button').hide();
					$('.add-button').show();
					$('#add-client-form').find('input').val("");
					
				}
			</script>
			
			
		<?php } include "footer.php"; ?>