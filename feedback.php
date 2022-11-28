<?php
    include "./includes/db_include.php";
    $branch_id = $_SESSION['branch_id'];
    if(isset($_GET['approve']) && $_GET['approve'] != ''){
        query("UPDATE client_feedback SET approve_status = '1' WHERE id = '".$_GET['approve']."' and branch_id='".$branch_id."'",[],$conn);
        $_SESSION['t']  = 1;
		$_SESSION['tmsg']  = "Feedback Approved";
		echo '<meta http-equiv="refresh" content="0; url=feedback.php" />';
    } else if(isset($_GET['cancel']) && $_GET['cancel'] != ''){
        query("UPDATE client_feedback SET approve_status = '2' WHERE id = '".$_GET['cancel']."' and branch_id='".$branch_id."'",[],$conn);
        $_SESSION['t']  = 1;
		$_SESSION['tmsg']  = "Feedback Cancelled";
		echo '<meta http-equiv="refresh" content="0; url=feedback.php" />';
    }
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
								<div class="panel-heading heading-with-btn">
									<h4 class="pull-left">Latest feedbacks &nbsp; &nbsp; &nbsp; &nbsp; Average Rating : 
									<?php 
									    $rating = query("SELECT AVG(customer_service_rating) as avg_rating from client_feedback where approve_status ='1' and branch_id='".$branch_id."'", [], $conn)[0]['avg_rating']; 
									    if($rating=="" || $rating==NULL){
									        echo "0";
									    }else{
				                            for($i=1;$i<=5;$i++){
                            	                if($i <= $rating){
                            	                     echo '<i class="fa fa-star rating-color" style="margin:0px;" aria-hidden="true"></i>';
                            	                } else if(($i-$rating) < 1){
                            	                    echo '<i class="fa fa-star-half-o rating-color" style="margin:0px;" aria-hidden="true"></i>';
                            	                } else {
                            	                    echo '<i class="fa fa-star-o rating-color" style="margin:0px;" aria-hidden="true"></i>';
                            	                }
                            	            }
									    } ?> </h4>
									    <span id="download-btn" class="mr-5 ml-5 pull-right"></span>
									<a href="client_feedback.php?type=padminrat" target="_blank"><button type="button" class="btn btn-success pull-right"><span class="fa fa-plus mr-left-0" aria-hidden="true"></span>Add feedback</button></a>
    													
    								<div class="clearfix"></div>
								</div>
								<div class="panel-body">
									<div class="row">
								        <div class="col-lg-12 table-responsive responsive_tbl">
									       <table class="table grid table-bordered table-striped">
                    						  <thead>
                    							<tr>
                    							  <th>#</th>
                    							  <th>Date</th>
                    							  <th>Name</th>
                    							  <th>Email</th>
                    							  <!--<th>#Inv</th>-->
                    							  <th>Review</th>
                    							  <th>Overall experience</th>
                    							  <th>Timely response</th>
                    							  <th>Support</th>
                    							  <th>Overall satisfaction</th>
                    							  <th>Service rating</th>
                    							  <th>Suggestion</th>
                    							  <th>Action</th>
                    							</tr>
                    						  </thead>
                    						  <tbody>
                    						      <?php
                    						        $review = query_by_id("SELECT * FROM client_feedback WHERE status = 1 and branch_id='".$branch_id."' ORDER BY id DESC",[],$conn);
                    						        if($review){
                    						            foreach($review as $rev){
                    						                ?>
                    						                    <tr>
                    						                        <td><?= $rev['id']; ?></td>
                    						                        <td><?= my_date_format($rev['created_at']) ?></td>
                    						                        <td style="white-space: nowrap"><?= $rev['name']; ?></td>
                    						                        <td><?= $rev['email']; ?></td>
                    						                        <!--<td><?php //$rev['invoice_id']; ?></td>-->
                    						                        <td style="word-break: break-word"><?= $rev['review']; ?></td>
                    						                        <td><?= $rev['overall_exp']; ?></td>
                    						                        <td><?= $rev['timely_response']; ?></td>
                    						                        <td><?= $rev['our_support']; ?></td>
                    						                        <td><?= $rev['overall_satisfaction']; ?></td>
                    						                        <td><?php
                    						                            for($i=1;$i<=5;$i++){
                                                        	                if($i <= $rev['customer_service_rating']){
                                                        	                     echo '<i class="fa fa-star rating-color" style="margin:0px;" aria-hidden="true"></i>';
                                                        	                } else {
                                                        	                    echo '<i class="fa fa-star-o rating-color" style="margin:0px;" aria-hidden="true"></i>';
                                                        	                }
                                                        	            }
                    						                            ?>
                    						                            <span class="hide"><?= $rev['customer_service_rating'] ?></span>
                    						                        </td>
                    						                        <td style="word-break: break-word"><?= $rev['suggestion']; ?></td>
                    						                        <td>
                    						                            <?php
                    						                                if($rev['approve_status'] == 0){
                    						                                    echo '<a href="feedback.php?approve='.$rev['id'].'"><button type="button" class="btn btn-sm btn-info">Approve</button></a>';
                    						                                    echo '<a href="feedback.php?cancel='.$rev['id'].'"><button type="button" class="btn btn-sm btn-danger">Cancel</button></a>';
                    						                                } else if($rev['approve_status'] == 1){
                    						                                    echo '<button type="button" class="btn btn-sm btn-success"><i class="fa fa-thumbs-o-up" aria-hidden="true"></i>Approved</button>';
                    						                                    echo '<a href="feedback.php?cancel='.$rev['id'].'"><button type="button" class="btn btn-sm btn-danger">Cancel</button></a>';
                    						                                } else if($rev['approve_status'] == 2){
                    						                                    echo '<a href="feedback.php?approve='.$rev['id'].'"><button type="button" class="btn btn-sm btn-info">Approve</button></a>';
                    						                                    echo '<button type="button" class="btn btn-sm btn-danger"><i class="fa fa-times-circle-o" aria-hidden="true"></i>Cancelled</button>';
                    						                                } else {}
                    						                            ?>
                    						                        </td>
                    						                    </tr>
                    						                <?php
                    						            }
                    						        }
                    						      ?>
                    						  </tbody>
                    						</table>
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