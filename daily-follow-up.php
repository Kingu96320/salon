<?php
    include "./includes/db_include.php";
    $branch_id = $_SESSION['branch_id'];
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
    <div class="dashboard-wrapper" style="margin:0px;">
    	<!-- Main container starts -->
    	<div class="main-container">
    	    <ul class="nav nav-tabs">
                <li class="<?= !isset($_GET['type'])?'active':'' ?>"><a data-toggle="tab" href="#birthday-anvi">Birthday & Aniversary</a></li>
                <li><a data-toggle="tab" href="#enquiry">Enquiry follow-up</a></li>
                <li><a data-toggle="tab" href="#pending-payment">Pending payment(s)</a></li>
                <li><a data-toggle="tab" href="#low-stock">Product Low stock</a></li>
                <li class="<?= isset($_GET['type'])?'active':'' ?>"><a data-toggle="tab" href="#irregular-clients">Irregular Client(s)</a></li>
            </ul>
            <div class="tab-content">
                <!-- Birthday & Aniversary -->
                <div id="birthday-anvi" class="tab-pane fade <?= !isset($_GET['type'])?' in active':'' ?>">
                    <div class="row gutter">
            			<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
            				<div class="panel t_birthday">
            					<div class="panel-heading">
            						<h4><i class="fa fa-birthday-cake text-danger" style="margin-left:0px;" aria-hidden="true"></i> Client birthday's (7 Days)</h4>
            					</div>
    					        <div class="panel-body">
    					            <table id="birthdaylist" class="table birthdaylist table-stripped table-bordered table-responsive">
    					                <thead>
    					                    <tr>
    					                        <th class="text-center"><input type="checkbox" id="birthchk" ></th>
    					                        <th>Name</th>
    					                        <th>Phone</th>
    					                        <th>Date of birth</th>
    					                    </tr>
    					                </thead>
    					                <tbody>
        					            <?php 
                							$date = date('m-d');
                							$sql1="SELECT * FROM `client` where active=0 and branch_id='".$branch_id."' and DATE_FORMAT(dob,'%m-%d') BETWEEN DATE_FORMAT(CURDATE(),'%m-%d') AND DATE_FORMAT(DATE_ADD(CURDATE(), INTERVAL 7 DAY),'%m-%d')";
                							$result1=query_by_id($sql1,[],$conn);
                							$row_count=get_row_count($sql1,[],$conn);
                							if($row_count > 0){ 
        										foreach($result1 as $row1) {
        									?>
        									    <tr>
        									        <td class="text-center"><input type="checkbox" class="bday" value="option1" data-contact="<?php echo $row1['cont']; ?>" data-name="<?php echo $row1['name']; ?>"/></td>
        									        <td><?= ucfirst($row1['name']) ?></td>
        									        <td><?= $row1['cont'] ?></td>
        									        <td><?= my_date_format($row1['dob']) ?></td>
        									    </tr>
        									<?php } }else{ ?>
                						
                						<?php } ?>
                					    </tbody>
                			        </table>
                			        <script>
										$("#birthchk").click(function () {
											if($(this).prop("checked") == true){
												$('.bday').prop("checked", true);
											}
											else if($(this).prop("checked") == false){
												$('.bday').prop("checked", false);
											}
										});
									</script>
    					        </div>
            				</div>
            			</div>
            			<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
            				<div class="panel t_anniversary">
            					<div class="panel-heading">
            						<h4>
            						    <i class="fa fa-male text-danger" style="margin-left:0px;" aria-hidden="true"></i>
            						    <i class="fa fa-female text-danger" style="margin-left:-13px;" aria-hidden="true"></i>Client anniversary (7 Days)</h4>
            					</div>
    					        <div class="panel-body">
    					            <table id="anniversarylist" class="table anniversarylist table-stripped table-bordered table-responsive">
    					                <thead>
    					                    <tr>
    					                        <th class="text-center"><input type="checkbox" id="anvicheck" ></th>
    					                        <th>Name</th>
    					                        <th>Phone</th>
    					                        <th>Anniversary date</th>
    					                    </tr>
    					                </thead>
    					                <tbody>
        					            <?php 
                							$date = date('m-d');
                							$sql1="SELECT * FROM `client` where active=0 and branch_id='".$branch_id."' and DATE_FORMAT(aniversary,'%m-%d') BETWEEN DATE_FORMAT(CURDATE(),'%m-%d') AND DATE_FORMAT(DATE_ADD(CURDATE(), INTERVAL 7 DAY),'%m-%d')";
                							$result1=query_by_id($sql1,[],$conn);
                							$row_count=get_row_count($sql1,[],$conn);
                							if($row_count > 0){ 
        										foreach($result1 as $row1) {
        									?>
        									    <tr>
        									        <td class="text-center"><input type="checkbox" class="ann" value="option1" data-contact="<?php echo $row1['cont']; ?>" data-name="<?php echo $row1['name']; ?>"/></td>
        									        <td><?= ucfirst($row1['name']) ?></td>
        									        <td><?= $row1['cont'] ?></td>
        									        <td><?= my_date_format($row1['aniversary']) ?></td>
        									    </tr>
        									<?php } }else{ ?>
                							<!--<tr>-->
                							<!--    <td colspan="4" class="text-center">No result found!</td>-->
                							<!--</tr>-->
                						<?php } ?>
                					    </tbody>
                			        </table>
                			        <script>
										$("#anvicheck").click(function () {
											if($(this).prop("checked") == true){
												$('.ann').prop("checked", true);
											}
											else if($(this).prop("checked") == false){
												$('.ann').prop("checked", false);
											}
										});
									</script>
    					        </div>
            				</div>
            			</div>
            		</div>
            	</div>
            	
            	<!-- Enquiry follow-up -->
            	<div id="enquiry" class="tab-pane fade">
                    <div class="row gutter">
            			<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            				<div class="panel">
            					<div class="panel-heading">
            						<h4><i class="fa fa-users text-danger" style="margin-left:0px;" aria-hidden="true"></i>Enquiry follow-up's</h4>
            					</div>
    					        <div class="panel-body">
    					            <div class="table-responsive">
            							<table id="enquirylist" class="table enquirylist table-bordered no-margin">
            								<thead>
            									<tr>
            										<th class="text-center"><input type="checkbox" id="enqchk" ></th>
            										<th>Name</th>
            										<th>Phone</th>
            										<th>Enquiry for</th>
            										<th>Followup date</th>
            										<th>Action</th>
            									</tr>
            								</thead>
            								<tbody>
            									<?php
            										$sql2 = "SELECT * FROM `enquiry` where leadstatus='Pending' and branch_id='".$branch_id."' and datefollow <= '".date('Y-m-d')."' and active=0 order by datefollow desc";
            										$result2 = query_by_id($sql2,[],$conn);
            										if($result2){
                										foreach($result2 as $row2) {
                										?>
                										<tr style="background-color:<?= $row2['datefollow'] == date('Y-m-d')?'#e8fdf3':'#fde8e8'?>">
                											<td class="text-center"><input type="checkbox" class="chkk" value="option1" data-contact="<?php echo $row2['cont']; ?>" data-name="<?php echo $row2['customer']; ?>" data-service="<?php echo ($row2['enquiry'] != '')?getEnquiryfor($row2['enquiry']):'Service' ?>" /></td>
                											<td><?php echo ucfirst($row2['customer']); ?></td>
                											<td><?php echo $row2['cont']; ?></td>
                											<td><?php echo ($row2['enquiry'] != '')?getEnquiryfor($row2['enquiry']):'' ?></td>
                											<td><?php echo my_date_format($row2['datefollow']); ?></td>
                											<td><a href="enquiry.php?id=<?php echo $row2['id']; ?>" ><button class="btn btn-success btn-xs" type="button"><i class="fa fa-plus" aria-hidden="true"></i>Add new response</button></a></td>
                										</tr>
                									    <?php 
                										}    
                									} else { ?>
                									    <!--<tr>-->
                									    <!--    <td colspan="6" class="text-center">No result found!</td>-->
                									    <!--</tr>-->
                								    <?php } ?>
            									<script>
            										$("#enqchk").click(function () {
            											if($(this).prop("checked") == true){
            												$('.chkk').prop("checked", true);
            											}
            											else if($(this).prop("checked") == false){
            												$('.chkk').prop("checked", false);
            											}
            										});
            									</script>
            								</tbody>
            							</table>
            							
            						</div>
    					        </div>
            				</div>
            			</div>
            		</div>
            	</div>
            	
            	<!-- Pending payment(s) -->
            	<div id="pending-payment" class="tab-pane fade">
                    <div class="row gutter">
            			<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            				<div class="panel">
            					<div class="panel-heading">
            						<h4><i class="fa fa-money text-danger" style="margin-left:0px;" aria-hidden="true"></i>Pending payment(s)</h4>
            					</div>
    					        <div class="panel-body">
    					            <table id="pending_payment" class="table pending_payment table-responsive table-bordered table-stripped">
            							<thead>
            								<tr>
            								    <th class="text-center"><input type="checkbox" id="pendpay" ></th>
            									<th>Name</th>
            									<th>Phone</th>
            									<th>Pending amount</th>
                                                <th>Branch</th>
            									<th>Action</th>
            								</tr>
            							</thead>
            							<tbody>
            								<?php
                                                $sql1 = '';
                                                $branches = total_branches(); 
                                                if(count($branches) > 0){
                                                    for($i = 0; $i < count($branches); $i++){
                                                        $sql1.="SELECT c.id as client_id,c.name,c.cont,i.*,i.id as iid,sum(i.due) as pending, '".$branches[$i]['id']."' as branch_id from `invoice_".$branches[$i]['id']."` i LEFT JOIN `client` c on c.id=i.client where i.active=0 and i.branch_id='".$branches[$i]['id']."' Group BY i.client ";
                                                            if($i < count($branches)-1){
                                                                $sql1 .= ' UNION ';
                                                            }
                                                    }
                                                }
            									$result1=query_by_id($sql1,[],$conn);
            									if($result1){
            									    $count = 1;
                									foreach($result1 as $row1) { 
                    									if($row1['pending'] - get_pending_payment_dash($row1['client_id'],$row1['branch_id']) > 0){	
                    									?>
                        									<tr id="rowid-<?= $row1['client_id'] ?>">
                        									    <td class="text-center"><input type="checkbox" class="chkkpend" value="option1" data-contact="<?php echo $row1['cont']; ?>" data-name="<?php echo $row1['name']; ?>" data-payment="<?php echo $row1['pending'] - get_pending_payment_dash($row1['client_id'],$row1['branch_id']); ?>"/></td>
                        										<td><?= ucfirst($row1['name']); ?></td>
                        										<td><?= $row1['cont']; ?></td>
                        										<td class="amount_pending"><?= number_format($row1['pending'] - get_pending_payment_dash($row1['client_id'],$row1['branch_id']),2); ?></td>
                                                                <td><?= ucfirst(branch_by_id($row1['branch_id'])) ?></td>
                        										<td><button onclick="invoice_pending_payment('<?= $row1['client_id'] ?>','<?= $row1['branch_id'] ?>')" class="btn btn-danger btn-xs" type="button"><i class="fa fa-money" aria-hidden="true"></i>Pay now</button></td>
                        									</tr>
                        								<?php  $count++; }  }
                    							    } else { ?>
                							        <tr>
            									        <td colspan="6" class="text-center">No result found!</td>
            									    </tr>
                							    <?php } ?>
                							    <script>
            										$("#pendpay").click(function () {
            											if($(this).prop("checked") == true){
            												$('.chkkpend').prop("checked", true);
            											}
            											else if($(this).prop("checked") == false){
            												$('.chkkpend').prop("checked", false);
            											}
            										});
            									</script>
            							</tbody>
            						</table>
    					        </div>
            				</div>
            			</div>
            		</div>
            	</div>
            	
            	<!-- Product Low stock -->
            	<div id="low-stock" class="tab-pane fade">
                    <div class="row gutter">
            			<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            				<div class="panel">
            					<div class="panel-heading">
            						<h4><i class="fa fa-shopping-basket text-danger" style="margin-left:0px;" aria-hidden="true"></i>Low stock (below 5)</h4>
            					</div>
    					        <div class="panel-body">
    					            <table class="table grid table-bordered no-margin">
            							<thead>
            								<tr>
            								    <th>#</th>
            									<th>Product name</th>
            									<th>Remaining quantity</th>
            									<th>Expiry date</th>
            									<th>Action</th>
            								</tr>
            							</thead>
            							<tbody>
            								<?php 
            								    $date = date('Y-m-d');
            									$sql1="SELECT ps.*, ps.id as item_id, p.name, p.id as pid, p.volume, u.name as unit from purchase_items ps LEFT JOIN products p ON p.id = ps.product_id LEFT JOIN units u ON u.id = p.unit where p.active=0 and ps.branch_id='".$branch_id."' and ps.active=0 and ps.exp_date >='".$date."' order by ps.iid DESC";
            									$result1=query_by_id($sql1,[],$conn);
            									$count = 1;
            									if($result1){
                									foreach($result1 as $row1) {
                										$qnt = getstock($row1['pid'],0,$row1['item_id']);
                										if($qnt >= 5){
                										    continue;
                										}
                										else{
                										?>
                										<tr>
                										    <td><?= $count.'.'; ?></td>
                											<td><?php echo $row1['name'].' ('.$row1['volume'].' '.$row1['unit'].')'; ?></td>
                											<td><?php echo getstock($row1['pid'],0,$row1['item_id']); ?></td>
                											<td><?= my_date_format($row1['exp_date']); ?></td>
                											<td><a href="addpurchase.php"><button type="button" class="btn btn-success btn-xs"><i class="fa fa-plus" aria-hidden="true"></i>Add stock</button></a></td>
                										</tr>
                									<?php } $count++; } }
                								else { ?>
                								    <tr>
            									        <td colspan="5" class="text-center">No result found!</td>
            									    </tr>
                								<?php } ?>
            							</tbody>
            						</table>
    					        </div>
            				</div>
            			</div>
            		</div>
            	</div>
            	
            	<?php
                	if(isset($_GET['sdate'])){
                	    $start_date = $_GET['sdate'];
                	    $start_date = explode("-",$start_date);
                	    $start_date = $start_date['1'].'/'.$start_date['2'].'/'.$start_date['0'];
                	    $sdate = $_GET['sdate'];
                	} else {
                	    $start_date = date('m/d/Y');
                	    $sdate = date('Y-m-d');
                	}
                	
                	if(isset($_GET['edate'])){
                	    $end_date = $_GET['edate'];
                	    $end_date = explode("-",$end_date);
                	    $end_date = $end_date['1'].'/'.$end_date['2'].'/'.$end_date['0'];
                	    $edate = $_GET['edate'];
                	} else {
                	    $end_date = date('m/d/Y');
                	    $edate = date('Y-m-d');
                	}
            	?>
            	
            	<!-- Irregular Client(s) -->
            	<div id="irregular-clients" class="tab-pane fade <?= isset($_GET['type'])?'fade in active':'fade' ?>">
                    <div class="row gutter">
            			<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            				<div class="panel">
            					<div class="panel-heading">
            						<h4><i class="fa fa-user-times text-danger" style="margin-left:0px;" aria-hidden="true"></i>
            						<?php if(!isset($_GET['type'])){ ?>
            						Irregular Client(s) (Clients not coming from last 10 days)
            						<?php } else {
            						    echo 'Irregular Client(s) FROM <i>'.my_date_format($sdate).'</i> To <i>'.my_date_format($edate).'</i>';
            						} ?>
            						</h4>
            					</div>
        					    <div class="panel-body">
        					        <div class="row">
        						        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
        									<div class="form-group">
        										<label for="date">Select dates</label>
        										<input type="text" class="form-control" range-attr="daterange" id="daterange" name="date" value="<?= $start_date.' - '.$end_date ?>"  placeholder="01/01/1990 - 12/05/2000" required readonly>		
        									</div>
        								</div>
        								<div class="col-md-3">
        								    <lable>&nbsp;</lable>
        								    <div class="form-group">
        								        <button class="btn btn-filter btn-sm" onclick="filterDailyreport()"><i class="fa fa-filter" aria-hidden="true"></i>Filter</button>&nbsp;&nbsp;&nbsp;&nbsp;<button class="btn btn-danger btn-sm" onclick="window.location.href='daily-follow-up.php'"><i class="fa fa-times" aria-hidden="true"></i>Clear</button>
        								    </div>
        								</div>
        							</div>
        						    <div class="clearfix"></div>
        					        <div class="table-responsive">
            							<table id="irrclient" class="table irrclient table-stripped table-bordered">
            								<thead>
            									<tr>
            										<th class="text-center"><input type="checkbox" id="iregchk" value="option1" /></th>
            										<th>Name</th>
            										<th>Phone</th>
            										<th>Last visited on</th>
            									</tr>
            								</thead>
            								<tbody>
            									<?php
            										$dr = 0 ;
            										if(!isset($_GET['type'])){
            										    $sql2="SELECT i.client,IF(max(i.doa) <= (CURDATE() - INTERVAL 10 DAY),max(i.doa),'0') as doa,i.id,c.name,c.cont FROM `invoice_".$branch_id."` i "
            										      ." LEFT JOIN `client` c on c.id=i.client where i.active=0 and i.branch_id='".$branch_id."' GROUP BY i.client order by i.doa desc";
            										} else {
            										    $sql2="SELECT * FROM (SELECT i.client,max(i.doa) as doa,i.id,c.name,c.cont FROM `invoice_".$branch_id."` i "
            										      ." LEFT JOIN `client` c on c.id=i.client where i.active=0 and i.branch_id='".$branch_id."' GROUP BY client ) as i WHERE i.doa NOT BETWEEN '".$sdate."' AND '".$edate."' GROUP BY i.client order by i.doa desc";
            										}
            										$result2 = query_by_id($sql2,[],$conn);
            										if($result2){
                										foreach($result2 as $row2) {
                										    if($row2['doa'] != 0){
                										?>
                										<tr>
                											<td class="text-center"><input type="checkbox" class="chkk2" value="option1"  data-contact="<?php echo $row2['cont']; ?>" data-name="<?php echo $row2['name']; ?>"/></td>
                											<td><?= ucfirst($row2['name']) ?></td>
                											<td><?= $row2['cont'] ?></td>
                											<td><?= my_date_format($row2['doa']); ?></td>	
                										</tr>
                										<?php 
            										    } }
            										} else { ?>
            										    <tr>
            										        <td colspan="4" class="text-center">No result found!</td>
            										    </tr>
            										<?php }
            									?>
            								</tbody>
            								
            								<script>
            									$("#iregchk").click(function () {
            										if($(this).prop("checked") == true){
            											$('.chkk2').prop("checked", true);
            										}
            										else if($(this).prop("checked") == false){
            											$('.chkk2').prop("checked", false);
            										}
            									});
            								</script>
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
<?php 
	include "footer.php";
?>

<!-- Birthday Modal -->
<div class="modal fade" id="birthdayModal" role="dialog">
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
				            <select class="form-control template_category" onchange="getTemplateType('birthdayModal', this.value)">

				            </select>
				        </div>
				    </div>
				    <div class="col-md-6">
				        <div class="form-group">
				            <label>Template type</label>
				            <select class="form-control template_type" onchange="getTemplate('birthdayModal', this.value)">
				                <option>--Select--</option>
				                
				            </select>
				        </div>
				    </div>
				</div>
				<div class="msg_body"></div>
				<br>
				<div class="send_button"></div>
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

<!-- Anniversary Modal -->
<div class="modal fade" id="anniversaryModal" role="dialog">
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
				            <select class="form-control template_category" onchange="getTemplateType('anniversaryModal', this.value)">

				            </select>
				        </div>
				    </div>
				    <div class="col-md-6">
				        <div class="form-group">
				            <label>Template type</label>
				            <select class="form-control template_type" onchange="getTemplate('anniversaryModal', this.value)">
				                <option>--Select--</option>
				                
				            </select>
				        </div>
				    </div>
				</div>
				<div class="msg_body"></div>
				<br>
				<div class="send_button"></div>
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

<!-- Enquiry Modal -->
<div class="modal fade" id="enqModal" role="dialog">
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
				            <select class="form-control template_category" onchange="getTemplateType('enqModal', this.value)">

				            </select>
				        </div>
				    </div>
				    <div class="col-md-6">
				        <div class="form-group">
				            <label>Template type</label>
				            <select class="form-control template_type" onchange="getTemplate('enqModal', this.value)">
				                <option>--Select--</option>
				                
				            </select>
				        </div>
				    </div>
				</div>
				<div class="msg_body"></div>
				<br>
				<div class="send_button"></div>
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


<!-- Enquiry Modal -->
<div class="modal fade" id="irclientModal" role="dialog">
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
				            <select class="form-control template_category" onchange="getTemplateType('irclientModal', this.value)">

				            </select>
				        </div>
				    </div>
				    <div class="col-md-6">
				        <div class="form-group">
				            <label>Template type</label>
				            <select class="form-control template_type" onchange="getTemplate('irclientModal', this.value)">
				                <option>--Select--</option>
				                
				            </select>
				        </div>
				    </div>
				</div>
				<div class="msg_body"></div>
				<br>
				<div class="send_button"></div>
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

<!-- Package Modal -->
<div id="ppaymentModal_fup" class="modal disableOutsideClick fade" role="dialog">
	<div class="modal-dialog modal-lg">		
		<!-- Modal content-->
		<div class="modal-content">
			
		</div>
		
	</div>
</div>


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

<script>
    
    $(document).ready(function(){
        getTemplateCategory('birthdayModal');
        getTemplateCategory('anniversaryModal');
        getTemplateCategory('enqModal');
        getTemplateCategory('irclientModal');
    });
    
    function getTemplateCategory(modalId){
        $.ajax({
           url : '<?= SMS_API_URL ?>sms_templates.php',
           type : 'post',
           data : {action : 'get_template_category'},
           success : function(res){
               if(res != ''){
                   $('#'+modalId+' .template_category').html(res);
               }
           }
        });
    }
    
    function getTemplateType(modalId, value){
        if(value == ''){
            $('#'+modalId+' .template_type').html('<option value="">--Select--</option>');
            $('#'+modalId+' .msg_body, #'+modalId+' .send_button').html('');
        } else {
            $.ajax({
                url : '<?= SMS_API_URL ?>sms_templates.php',
                type : 'post',
                data : {action : 'getTemplate', category : value},
                success : function(res){
                    if(res != ''){
                        $('#'+modalId+' .template_type').html('<option value="">--Select--</option>'+res);
                    } else {
                        $('#'+modalId+' .template_type').html('<option value="">--Select--</option>');
                    }
                }
            });
        }
    }
    
    function getTemplate(modalId, value){
        var template_category = $('#'+modalId+' .template_category').val();
        if(value == ''){
            $('#'+modalId+' .msg_body, #'+modalId+' .send_button').html('');
        } else {
            $.ajax({
                url : '<?= SMS_API_URL ?>sms_templates.php',
                type : 'post',
                data : {action : 'getMessages', group_name : value, template_category : template_category, modalId : modalId},
                success : function(res){
                    if(res != ''){
                        $('#final_templates .panel-body').html(res);
                        $('#final_templates').modal('show');
                    } else {
                        $('#'+modalId+' .msg_body, #'+modalId+' .send_button').html('');
                    }
                }
            });
        }
    }
    
    function useTemplate(modalId, tempid, sendas, element){
        var html = '<label for="userName">Message</label><br /><span class="text-danger">* Please check SMS content and update variable values before sending SMS.</span>';
        html += '<input type="hidden" class="tempid" value="'+tempid+'">';
        html += '<input type="hidden" class="sendas" value="'+sendas+'">';
        html += '<textarea style="height: 100px;" class="form-control message">'+element.html()+'</textarea><p class="text-success message_show"></p>';
        $('#'+modalId+' .msg_body').html(html);
        $('#'+modalId+' .send_button').html('<button type="submit" style="float : right;" onclick="sendmessage(\''+modalId+'\')" class="btn btn-success" class="final_submit">SEND SMS</button>');
        $('#final_templates').modal('hide');
    }
    
    
    function sendmessage(modalId) {
        if(confirm('Have you replaced appropriate variable {#__#} values in SMS content?')){
		var message = $('#'+modalId+' .message').val();
		var sendtype = $('#'+modalId+' .sendas').val();
		var tempid = $('#'+modalId+' .tempid').val();
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
			  $('#'+modalId+' .final_submit').prop('disabled', true);
			  $('#'+modalId+' .message_show').text('SMS process started, please do not refresh or close page.');
			},
			success: function(result) {
				toastr.success(result);
				$('#'+modalId+' .msg_body').html('');
				$('#'+modalId+' .send_button').html('');
				$('#'+modalId+' .template_category option[value=""]').prop('selected', true);
				$('#'+modalId+' .template_type').html('<option value="">--Select--</option>');
				$('#'+modalId+' .final_submit').prop('disabled', false);
			    $('#'+modalId+' .message_show').text('');
			    $('input[type="checkbox"]').prop('checked', false);
			}
		});
        }
	}
    
    function isoDate(date){	
		var datespit = date.split('/');
		var day = datespit[1].replace(' ','');
		var month = datespit[0].replace(' ','');
		var year = datespit[2].replace(' ','');
		return year+'-'+month+'-'+day;
	}
	
	function filterDailyreport(){
		var daterange = $('#daterange').val();
		var date = daterange.split("-");
		if(daterange == ''){
			var from = '';
			var to = '';
		} else {
			var from = isoDate(date[0]);
			var to = isoDate(date[1]);
		}
		window.location.href = '?type=irrclient&sdate='+from+'&edate='+to;
	}
	
    $(document).ready(function(){
        $('.follow_up').DataTable({
			"dom": 'lBfrtip',
			'lengthMenu': [[10, 25, 50, 100, 99999999], [10, 25, 50, 100, 'All']],
			"buttons": [
			'copy', 'csv', 'excel', 'print'
			],
			"fnDrawCallback": function (oSetStings) {
				$("#follow_up_wrapper").find('.paging_simple_numbers').append('<li class="paginate_button"><button class="btn btn-info" style="float : right;" onclick="sendenqsms()"><i class="fa fa-paper-plane" aria-hidden="true"></i>Send SMS</button></li>');
			},
			"bProcessing": true,
			"aaSorting":[],
			'columnDefs': [ {
				'targets': [0], // column index (start from 0)
				'orderable': false, // set orderable false for selected columns
			}],
		});
		
		$('.birthdaylist').DataTable({
			"dom": 'lBfrtip',
			'lengthMenu': [[10, 25, 50, 100, 99999999], [10, 25, 50, 100, 'All']],
			"buttons": [
			'copy', 'csv', 'excel', 'print'
			],
			"fnDrawCallback": function (oSetStings) {
				$("#birthdaylist_wrapper").find('.paging_simple_numbers').append('<li class="paginate_button"><button class="btn btn-info" style="float : right;" onclick="sendbday()"><i class="fa fa-paper-plane" aria-hidden="true"></i>Send SMS</button></li>');
			},
			"bProcessing": true,
			"aaSorting":[],
			'columnDefs': [ {
				'targets': [0], // column index (start from 0)
				'orderable': false, // set orderable false for selected columns
			}],
		});
		
		
		$('.anniversarylist').DataTable({
			"dom": 'lBfrtip',
			'lengthMenu': [[10, 25, 50, 100, 99999999], [10, 25, 50, 100, 'All']],
			"buttons": [
			'copy', 'csv', 'excel', 'print'
			],
			"fnDrawCallback": function (oSetStings) {
				$("#anniversarylist_wrapper").find('.paging_simple_numbers').append('<li class="paginate_button"><button class="btn btn-info" style="float : right;" onclick="sendaniv()"><i class="fa fa-paper-plane" aria-hidden="true"></i>Send SMS</button></li>');
			},
			"bProcessing": true,
			"aaSorting":[],
			'columnDefs': [ {
				'targets': [0], // column index (start from 0)
				'orderable': false, // set orderable false for selected columns
			}],
		});
		
		$('.enquirylist').DataTable({
			"dom": 'lBfrtip',
			'lengthMenu': [[10, 25, 50, 100, 99999999], [10, 25, 50, 100, 'All']],
			"buttons": [
			'copy', 'csv', 'excel', 'print'
			],
			"fnDrawCallback": function (oSetStings) {
				$("#enquirylist_wrapper").find('.paging_simple_numbers').append('<li class="paginate_button"><button class="btn btn-info" style="float : right;" onclick="sendenqsms()"><i class="fa fa-paper-plane" aria-hidden="true"></i>Send SMS</button></li>');
			},
			"bProcessing": true,
			"aaSorting":[],
			'columnDefs': [ {
				'targets': [0], // column index (start from 0)
				'orderable': false, // set orderable false for selected columns
			}],
		});
		
		$('.irrclient').DataTable({
			"dom": 'lBfrtip',
			'lengthMenu': [[10, 25, 50, 100, 99999999], [10, 25, 50, 100, 'All']],
			"buttons": [
			'copy', 'csv', 'excel', 'print'
			],
			"fnDrawCallback": function (oSetStings) {
				$("#irrclient_wrapper").find('.paging_simple_numbers').append('<li class="paginate_button"><button class="btn btn-info" style="float : right;" onclick="irrclientsms()"><i class="fa fa-paper-plane" aria-hidden="true"></i>Send SMS</button></li>');
			},
			"bProcessing": true,
			"aaSorting":[],
			'columnDefs': [ {
				'targets': [0], // column index (start from 0)
				'orderable': false, // set orderable false for selected columns
			}],
		});
		
		$('.pending_payment').DataTable({
			"dom": 'lBfrtip',
			'lengthMenu': [[10, 25, 50, 100, 99999999], [10, 25, 50, 100, 'All']],
			"buttons": [
			'copy', 'csv', 'excel', 'print'
			],
			"fnDrawCallback": function (oSetStings) {
				$("#pending_payment_wrapper").find('.paging_simple_numbers').append('<li class="paginate_button"><button class="btn btn-info" style="float : right;" onclick="pending_paymentsms($(this))"><i class="fa fa-paper-plane" aria-hidden="true"></i>Send SMS</button></li>');
			},
			"bProcessing": true,
			"aaSorting":[],
			'columnDefs': [ {
				'targets': [0], // column index (start from 0)
				'orderable': false, // set orderable false for selected columns
			}],
		});
    });
    
    // pending payment sms function 
    
	var user_list = [];
	function pending_paymentsms(Div){
	    user_list = [];
	    var btn_html = Div.html();
	    var btn = Div;
	    var count = $('input.chkkpend:checked').length;
		if(count>0){
			$(".chkkpend").each(function(){
				if($(this).is(':checked'))
				{
					user_list.push({name: $(this).data("name"), contact: $(this).data("contact"), amount: $(this).data("payment") });
				}
			});
			if(user_list.length > 0){
			    $.ajax({
			        type : "POST",
					url : "ajax/sendsms.php",
					data : {
						data: JSON.stringify(user_list,true),
						action: 'send_pending_payment_reminder',
					},
					beforeSend: function() {
					    btn.html('<i class="fa fa-spinner fa-spin" aria-hidden="true"></i>Sending...');
					    btn.removeAttr('onclick');
					},
					success : function(response){
					    toastr.success(response);
					    $('.chkkpend').prop('checked',false);
					    btn.html(btn_html);
					    btn.attr('onclick','send_refcode($(this))');
					}
			    });
			}
		}else{
			toastr.warning('Please Select at least one client');
		}
	}
    
    // Birthday messages
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
			$('#birthdayModal').modal('show');
			}else{
            alert('Please Select at least one Client'); 
		}
	}
	
    // 	Anniversary messages
    
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
    		$('#anniversaryModal').modal('show');
    		}else{
            alert('Please Select at least one Client'); 
    	}
    }
    
    
    // 	Enquiry messages
    
    function sendenqsms() {
    	json_values = [];
    	var count = $('input.chkk:checked').length;
    	if(count>0){
    		$(".chkk").each(function()
            {
    			if($(this).is(':checked'))
                {
                    json_values.push({name: $(this).data("name"),contact: $(this).data("contact"), enq_service: $(this).data("service") });
    			}
    		});
    		$('#enqModal').modal('show');
    		}else{
            alert('Please Select at least one Client'); 
    	}
    }
    
    // 	Irregular client messages
    
    function irrclientsms() {
    	json_values = [];
    	var count = $('input.chkk2:checked').length;
    	if(count>0){
    		$(".chkk2").each(function()
            {
    			if($(this).is(':checked'))
                {
                    json_values.push({name: $(this).data("name"),contact: $(this).data("contact") });
    			}
    		});
    		    $('#irclientModal').modal('show');
    		} else {
                alert('Please Select at least one Client'); 
    	}
    }

    // send messages
    
    function sendbdaymessage() {
    	var message = $('#bdymessage').val();
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
    			$("input[type='checkbox']").prop("checked",false);
    		}
    	});
    }

    // anniveresry message
    
    function sendanimessage(){
    	var message = $('#anvimessage').val();
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
    			$("input[type='checkbox']").prop("checked",false);
    		}
    	});
    }


    // Enquiry message
    
    function sendenqmessage(){
    	var message = $('#enqmessage').val();
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
    			$("input[type='checkbox']").prop("checked",false);
    		}
    	});
    }

    // Send message to irregular clients
    
    function sendirrclimessage(){
    	var message = $('#irrclientmessage').val();
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
    			$("input[type='checkbox']").prop("checked",false);
    		}
    	});
    }
    
    // client pending payments
    function invoice_pending_payment(client_id, branch_id){
	    // check client pending payments
	    $.ajax({
	        url : "ajax/get_pending_payments.php",
	        type : "post",
	        data : {action : 'check_pending_payments_fup', client_id : client_id, branch_id : branch_id},
	        success : function(res){
	            if(res != 0){
	                $('#ppaymentModal_fup .modal-content').html(res);
	                var size = $('#ppaymentModal_fup table tbody tr').length;
	                if(size <= 0){
	                    $('#ppaymentModal_fup').modal('hide');
	                } else {
	                    $('#ppaymentModal_fup').modal('show');
	                }
	            }
	        }
	    });
	}
	
	
// 	payment process
	
	function pendingPayment_fup(div){
	    var curDiv = div.parent().parent();
	    var table = div.parent().parent().parent().parent();
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
    	                    var ppend = 0;
    	                    $('.pendtotal').each(function(){
    	                        ppend += parseFloat($(this).val());
    	                    });
    	                    $('#rowid-'+clientid+' .amount_pending').text(ppend);
    	                } else {
    	                    curDiv.remove();
    	                    var ppend = 0;
    	                    $('.pendtotal').each(function(){
    	                        ppend += parseFloat($(this).val());
    	                    });
    	                    $('#rowid-'+clientid+' .amount_pending').text(ppend);
    	                }
    	                var size = $('#ppaymentModal_fup table tbody tr').length;
    	                if(size <= 0){
    	                    $('#ppaymentModal_fup').modal('hide');
    	                    $('#rowid-'+clientid).remove();
    	                }
    	            } else {
    	                toastr.warning('Amount not paid, please try again.');
    	            }
    	        }
	        });
	    }
	}
	
	function pendingpaymode_fup(mode_id, modeDiv){
		var options = 0;
		var table = modeDiv.parent().parent().parent().parent();
		var totalVal = parseFloat(modeDiv.parent().parent().find('.pendtotal').val());
		if(totalVal == 0){
			modeDiv.val('1');
		} else {
	        var modeDiv = modeDiv.parent().parent();
    		var wallet_money = parseFloat(table.find('.wallet_money').val());
    		var reward_point = parseInt(table.find('.reward_point').val());
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
    						table.find('.reward_point').val(reward_point);
    						table.find('.earned_points').text(reward_point);
    					}
    				}
    			}
    		} else if(mode_id == '9'){
    			if(totalVal != 0){
    				if(reward_point == '' || reward_point == '0'){
    					modeDiv.find('.amtpay').val('0');
    					toastr.warning('Don\'t have any reward point.');
    					modeDiv.find('.mthd').val('1');
    				} else {
    					var point;
    					var point_price = <?= redeemprice() ?>;
    					var redeem_point = <?= redeempoint() ?>;
    					var pprice = parseFloat(totalVal);
    					if(reward_point > <?= maxredeempoint() ?>){
    						point = <?= maxredeempoint() ?>;
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
	
	function maxpendpayment_fup(pend_amount, amount, currDiv){
	    var pending_amount = parseFloat(pend_amount);
	    if(amount == ''){
	        var amount = 0;
	    } else {
	        var amount = parseFloat(amount);
	        currDiv.removeClass('invalid');
	    }
    	var paid = 0;
		var redeem_point = <?= redeempoint() ?>;
		var max_points = <?= maxredeempoint() ?>;
		var mainDiv = currDiv.parent().parent();
		var currentDiv = currDiv;
		var rewardPoint = $('#reward_point').val();
		var walletMoney = $('#wallet_money').val();
		paid += parseFloat(currDiv.val()||0);
		if(parseFloat(paid) > pending_amount){
			toastr.warning('Amount exceeded total amount.');
			currentDiv.val(0);
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
    }
</script>