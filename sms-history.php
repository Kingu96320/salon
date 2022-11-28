<?php
	include "./includes/db_include.php";
	$branch_id = $_SESSION['branch_id'];

	ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

	if(!isset($_SESSION['user_type'])) {
		header('LOCATION: dashboard.php');
		die();
	}
	
	if(isset($_GET['date'])){
	    $date = $_GET['date'];
	} else {
	    $date = date('Y-m-d');
	}
	
	$sms_status = query_by_id("SELECT message_id, id FROM sms_history WHERE date(datetime)='".$date."' AND sent_status='0' AND status='1' || sms_status='Undelivered'",[],$conn);
	if($sms_status){
	    foreach($sms_status as $status){
	        $ch = curl_init();
            $url = SMS_API_URL."sms_status.php";
            $postData = array(
              'action' => 'check_sms_status',
              'message_id' => $status['message_id']
            );
            curl_setopt_array($ch,
              array(
                CURLOPT_URL => $url,
                CURLOPT_POST       => true,
                CURLOPT_POSTFIELDS => $postData,
                CURLOPT_RETURNTRANSFER     => true,
              )
            );
            $data = curl_exec($ch);
            curl_close($ch);
            $character = json_decode($data,true);
            
            if($character){
                query("UPDATE sms_history SET sms_status =  '".$character['sms_status']."', status_details='".$character['status_details']."',  sent_status='1' WHERE id='".$status['id']."'",[],$conn);
            }
	    }
	}
	
	
	include "topbar.php";
	include "header.php";
	include "menu.php";
	
?>
<style>
    table, th, td{
        font-size: 13px!important;
        vertical-align: middle!important;
        text-align: center;
        padding: 5px!important;
    }
</style>
<!-- Dashboard wrapper starts -->
<div class="dashboard-wrapper">
	
	<!-- Main container starts -->
	<div class="main-container">
		
		<!-- Row starts -->
		<div class="row gutter">
			
			<?php
			    if(isset($_GET['filter']) && $_GET['filter'] == 'sms_filter'){
			        $sql1="SELECT sh.id, c.name, c.cont, sh.sender, sh.message, sh.datetime, sh.status_details, sh.sms_status, sh.template_id FROM sms_history sh LEFT JOIN client c ON CONCAT(".PHONE_CODE.",'',c.cont)=sh.mobile_number WHERE sh.status='1' AND DATE(datetime)='".$date."' UNION SELECT sh.id, e.customer as name, e.cont, sh.sender, sh.message, sh.datetime, sh.status_details, sh.sms_status, sh.template_id FROM sms_history sh LEFT JOIN enquiry e ON CONCAT(".PHONE_CODE.",'',e.cont)=sh.mobile_number WHERE sh.status='1' AND DATE(datetime)='".$date."' ORDER BY id DESC";
			        // echo $sql1."<br />";
			    } else {
			        $sql1="SELECT sh.id, c.name, c.cont, sh.sender, sh.message, sh.datetime, sh.status_details, sh.sms_status, sh.template_id FROM sms_history sh LEFT JOIN client c ON CONCAT(".PHONE_CODE.",'',c.cont)=sh.mobile_number WHERE sh.status='1' AND DATE(datetime)='".$date."' UNION SELECT sh.id, e.customer as name, e.cont, sh.sender, sh.message, sh.datetime, sh.status_details, sh.sms_status, sh.template_id FROM sms_history sh LEFT JOIN enquiry e ON CONCAT(".PHONE_CODE.",'',e.cont)=sh.mobile_number WHERE sh.status='1' AND DATE(datetime)='".$date."' ORDER BY id DESC";
			        // echo $sql1."<br />";
			    }
			    
    			$sql="SELECT total,sent,(total-sent) as pending FROM `sms`";
                $pending_sms = query_by_id($sql,[],$conn)[0]['pending'];
			?>
			
	
			<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
				<div class="panel">
					<div class="panel-heading">
						<h4>Filter SMS History <span class="pull-right" style="color: #f00;">SMS Balance : <?= $pending_sms ?></span></h4>
					</div>
					<div class="panel-body">
						<div class="row">
							<form action="" method="get" id="main-form">
								<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
									<div class="form-group">
										<label>Date</label>
										<input type="text" class="form-control date" name="date" value="<?= $date ?>" required>
									</div>
								</div>

								<div class="col-lg-2 col-md-3 col-sm-3 col-xs-12">
									<div class="form-group">
										<label>&nbsp;</label><br />
										<button type="submit" name="filter" value="sms_filter" class="btn btn-filter">Submit</button>
										<a href="sms-history.php"><button type="button" class="btn btn-warning">Show All</button></a>
									</div>
								</div>
							</form>
						</div>
					</div>
				</div>
				<div class="panel">
					<div class="panel-heading">
						<h4>SMS History</h4>
					</div>
					<div class="panel-body"></div>
						<div class="clearfix"></div>
						<div class="row">
							<div class="col-lg-12">
								<div class="table-responsive">
									<table class="table table-bordered no-margin">
										<thead>
											<tr>
											    <th>Sr.No</th>
												<th>Name</th>
												<th>Phone</th>
												<th>Senderid</th>
												<th>Message</th>
												<th>Credits Used</th>
												<th>Char.</th>
												<th>Date.</th>
												<th>Time.</th>
												<th>Status</th>
												<th>TokenID</th>
											</tr>
										</thead>
										<tbody>
										    <!-- GET in-quoue sms -->
										    <?php 
										        $count = 1;
                                                $ch = curl_init();
                                                $url = SMS_API_URL."pending_sms.php";
                                                $postData = array(
                                                  'action' => 'get_pending_sms',
                                                  'date' => $date,
                                                  'software_link' => BASE_URL
                                                );
                                                
                                                curl_setopt_array($ch,
                                                  array(
                                                    CURLOPT_URL => $url,
                                                    CURLOPT_POST       => true,
                                                    CURLOPT_POSTFIELDS => $postData,
                                                    CURLOPT_RETURNTRANSFER     => true,
                                                  )
                                                );
                                                
                                                $data = curl_exec($ch);
                                                curl_close($ch);
                                                if($data != ''){
                                                    $data = json_decode($data);
                                                    foreach($data as $pending){
                                                        $pending = json_decode(json_encode($pending), true);
                                                        if($pending['sms_type'] == 'single'){
                                                            ?>
                                                            <tr>
            													<td><?= $count ?></td>
            													<td>--</td>
            													<td><?php echo $pending['contact_number']; ?></td>
            													<td><?php echo $pending['sender_id']; ?></td>
            													<td class="text-center"><?php echo $pending['content']."<br /><b>".$pending['template_id']."</b>"; ?></td>
            													<td><?php
            													    if(strlen($pending['content']) <= 160){
            													        echo '1';
            													    } else if(strlen($pending['content']) > 160){
            													        echo ceil(strlen($pending['content'])/153);
            													    }
            													?></td>
            													<td><?php echo strlen($pending['content']) ?></td>
            													<td>-</td>
            													<td>-</td>
            													<td>In Queue</td>
            													<td>
            													    <?php 
            													        echo $pending['contact_number']."<br /><b>Pending</b>";
            													    ?>
            													</td>
            												</tr>
                                                            <?php
                                                            $count++;
                                                        } else if($pending['sms_type'] == 'bulk'){
                                                            $contacts = explode(',',$pending['contact_number']);
                                                            foreach($contacts as $cont){
                                                                ?>
                                                                <tr>
                													<td><?= $count ?></td>
                													<td>--</td>
                													<td><?php echo ltrim($cont, PHONE_CODE); ?></td>
                													<td><?php echo $pending['sender_id']; ?></td>
                													<td class="text-center"><?php echo $pending['content']."<br /><b>".$pending['template_id']."</b>"; ?></td>
                													<td><?php
                													    if(strlen($pending['content']) <= 160){
                													        echo '1';
                													    } else if(strlen($pending['content']) > 160){
                													        echo ceil(strlen($pending['content'])/153);
                													    }
                													?></td>
                													<td><?php echo strlen($pending['content']) ?></td>
                													<td>-</td>
                													<td>-</td>
                													<td>In Queue</td>
                													<td>
                													    <?php 
                													        echo ltrim($cont, PHONE_CODE)."<br /><b>Pending</b>";
                													    ?>
                													</td>
                												</tr>
                                                                <?php
                                                                $count++;
                                                            }
                                                        } else {
                                                            
                                                        }
                                                        
                                                    }
                                                }										    
										    ?>
											<?php
												$result1 = query_by_id($sql1,[],$conn);
												if($result1){
												foreach ($result1 as $row1) {
													if($row1['cont'] == null || $row1['cont'] == ''){
														continue;
													}
												?>
												<tr>
													<td><?= $count ?></td>
													<td><?php echo htmlspecialchars_decode($row1['name']); ?></td>
													<td><?php echo $row1['cont']; ?></td>
													<td><?php echo $row1['sender']; ?></td>
													<td class="text-center"><?php echo $row1['message']."<br /><b>".$row1['template_id']."</b>"; ?></td>
													<td><?php
													    if(strlen($row1['message']) <= 160){
													        echo '1';
													    } else if(strlen($row1['message']) > 160){
													        echo ceil(strlen($row1['message'])/153);
													    }
													?></td>
													<td><?php echo strlen($row1['message']) ?></td>
													<td><?php echo date('d-m-Y', strtotime($row1['datetime'])); ?></td>
													<td><?php echo date('H:i:s', strtotime($row1['datetime'])); ?></td>
													<td><?php echo $row1['status_details'] ?></td>
													<td>
													    <?php 
													        echo $row1['cont']."<br /><b>".$row1['sms_status']."</b>";
													    ?>
													</td>
												</tr>
											<?php $count++; }} else if($count <= 1) { echo '<tr><td colspan="11" class="text-center">No record found!!!</td></tr>'; }  ?>
										</tbody>
									</table>
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

<?php include "footer.php"; ?>		