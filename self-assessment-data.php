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
								<div class="panel-heading heading-with-btn">
									<h4 class="pull-left">Self assessment data</h4>
									    <span id="download-btn" class="mr-5 ml-5 pull-right"></span>
									<a href="self-assessment.php" target="_blank"><button type="button" class="btn btn-success pull-right"><span class="fa fa-plus mr-left-0" aria-hidden="true"></span>Add</button></a>
    													
    								<div class="clearfix"></div>
								</div>
								<div class="panel-body">
									<div class="row">
								        <div class="col-lg-12 table-responsive responsive_tbl">
									       <table class="table grid table-bordered table-striped">
                    						  <thead>
                    							<tr>
                    							  <th>Date</th>
                    							  <th>Name</th>
                    							  <th>Email</th>
                    							  <th>Phone</th>
                    							  <th>Address</th>
                    							  <th>Have you been to one of the COVID-19 affected countries in the last 14 days?</th>
                    							  <th>Have you been in close contact with a confirmed case of coronavirus?</th>
                    							  <th>Are you currently experiencing symptoms (cough, shortness of breath, fever)</th>
                    							</tr>
                    						  </thead>
                    						  <tbody>
                    						      <?php
                    						        $data = query_by_id("SELECT * FROM self_assessment WHERE branch_id='".$branch_id."' ORDER BY id DESC",[],$conn);
                    						        if($data){
                    						            foreach($data as $d){
                    						                ?>
                    						                <tr>
                    						                    <td><?= my_date_format($d['added_on']) ?></td>
                    						                    <td><?= $d['name'] ?></td>
                    						                    <td><?= $d['email'] ?></td>
                    						                    <td><?= $d['phone'] ?></td>
                    						                    <td><?= $d['address'] ?></td>
                    						                    <td><?= $d['q1'] ?></td>
                    						                    <td><?= $d['q2'] ?></td>
                    						                    <td><?= $d['q3'] ?></td>
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