<head>
<!-- Horizontal bar CSS -->
		<link href="css/horizontal-bar/chart.css" rel="stylesheet" />

		</head>

<?php
include "./includes/db_include.php";

$uid = $_SESSION['uid'];

if(isset($_POST['submit'])){
	$date 	= $_POST['date'];
	$amount = $_POST['amount'];
	$ecat 	= $_POST['ecatt'];
	$descc 	= $_POST['descc'];
	
		query("INSERT INTO `expense`(`date`,`cat`,`amount`,`descc`,`user`,`active`) VALUES ('$date','$ecat','$amount','$descc','$uid',0)",[],$conn);
			
				$_SESSION['t']  = 1;
				$_SESSION['tmsg']  = "Expense Added Successfully";
				echo '<meta http-equiv="refresh" content="0; url=expenses.php" />';die();
}

if(isset($_GET['d'])){
	$d = $_GET['d'];
	query("update `expense` set active=1 where id=$d",[],$conn);
			
				$_SESSION['t']  = 1;
				$_SESSION['tmsg']  = "Expense Removed Successfully";
				echo '<meta http-equiv="refresh" content="0; url=expenses.php" />';die();
}

if(isset($_GET['del'])){
	$d = $_GET['del'];
	query("update `expensecat` set active=1 where id=$d",[],$conn);
	
				$_SESSION['t']  = 1;
				$_SESSION['tmsg']  = "Expense Category Removed Successfully";
				echo '<meta http-equiv="refresh" content="0; url=expenses.php" />';die();
}

if(isset($_POST['addcat'])){
	$cat  =  $_POST['cat'];
	$cat  = ucfirst($cat);
	query_by_id("INSERT INTO `expensecat`(`title`,`active`) VALUES ('$cat',0)",[],$conn);

				$_SESSION['t']  = 1;
				$_SESSION['tmsg']  = "Expense Category Added Successfully";
				echo '<meta http-equiv="refresh" content="0; url=expenses.php" />';die();
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
						
						
						
						
					
									<div class="row">
										
										


										<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
											<div class="panel">
												<div class="panel-heading">
													<h4>Add new offer</h4>
												</div>
												<div class="panel-body">
													<div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
															<div class="form-group">
																<label for="userName">Offer name</label>
																<input type="text" class="form-control"  name="date" placeholder="" required>
															</div>
													</div>
													<div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
															<div class="form-group">
																<label for="userName">Offer valid till</label>
																<input type="text" class="form-control date"  name="date" placeholder="" required readonly>
															</div>
													</div>
													
													<div class="clearfix"></div>
													
													
													<div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
															<div class="form-group">
																<label for="userName">Activity</label>
																<select class="form-control" name="role">
																	<option value="1" selected >Purchase of product</option>
																	<option value="1">Purchase of service</option>
																	<option value="2" >Purchase of package</option>
																	<option value="2" >Given feedback</option>
																	<option value="1">Refer new client</option>
																	<option value="2" >Appointment via website</option>
																	<option value="2" >Total bill amount</option>
																	<option value="2" >Use of reward points</option>
																	<option value="2" >Other</option>
																</select>
															</div>
													</div>
													<div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
															<div class="form-group">
																<label for="userName">Product name</label>
																<input type="text" class="form-control"  name="date" placeholder="" required>
															</div>
													</div>
													<div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
															<div class="form-group">
																<label for="userName">Discount</label>
																<table>
																
																<tr>
																
																<td width="70%">
																<input  name="disc_row[]" class="form-control disc_row"  value="0">
																</td>
																
																<td width="30%">
																	
																	<select class="form-control" name="role">
																		<option value="1" selected >%</option>
																		<option value="1"><?= CURRENCY ?></option>
																		
												
																	</select>
									
									
																</td>
																
																</tr>
																
																</table>
															</div>
													</div>
													
													<div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
															<div class="form-group">
																<label for="userName">Free addon</label>
																<input type="text" class="form-control"  name="date" placeholder="" required>
															</div>
													</div>
													
													
													
													<div class="clearfix"></div>
													<div class="col-lg-9">
													
														<div class="form-group">
																<label for="userName"><strong>Offer valid for membership</strong></label><br>
																<div class="checkbox checkbox-inline">
																	<input type="checkbox" id="germany" value="option1" checked />
																	<label for="germany">Membership 1</label>
																</div>
																<div class="checkbox checkbox-inline">
																	<input type="checkbox" id="germany" value="option2" />
																	<label for="germany">Membership 2</label>
																</div>
																<div class="checkbox checkbox-inline">
																	<input type="checkbox" id="germany" value="option3"  />
																	<label for="germany">Membership 3</label>
																</div>
																<div class="checkbox checkbox-inline">
																	<input type="checkbox" id="germany" value="option3"  />
																	<label for="germany">Membership 4</label>
																</div>
															</div>
															
															
													
													</div>
													<div class="col-lg-3">
													
														<button class="btn btn-primary pull-right" type="button">Create offer</button>
														
														
													</div>
													
													
													
												</div>
											</div>
											
										</div>	
										
										
										<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
											<div class="panel">
												<div class="panel-heading">
													<h4>Add new offer (if feedback)</h4>
												</div>
												<div class="panel-body">
													
													<div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
															<div class="form-group">
																<label for="userName">Activity</label>
																<select class="form-control" name="role">
																	<option value="1"  >Purchase of product</option>
																	<option value="1">Purchase of service</option>
																	<option value="2" >Purchase of package</option>
																	<option value="2" selected >Given feedback</option>
																	<option value="1">Refer new client</option>
																	<option value="2" >Appointment via website</option>
																	<option value="2" >Total bill amount</option>
																	<option value="2" >Use of reward points</option>
																</select>
															</div>
													</div>
													<div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
															<div class="form-group">
																<label for="userName">Feedback no.</label>
																<input type="text" class="form-control"  name="date" placeholder="1" required>
															</div>
													</div>
													<div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
															<div class="form-group">
																<label for="userName">Reward points to be given</label>
																<input type="text" class="form-control"  name="date" placeholder="1" >
															</div>
													</div>
													
													<div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
															<div class="form-group">
																<label for="userName">Free addon</label>
																<input type="text" class="form-control"  name="date" placeholder="" required>
															</div>
													</div>
													
													
													
													<div class="clearfix"></div>
													<div class="col-lg-9">
													
														<div class="form-group">
																<label for="userName"><strong>Offer valid for membership</strong></label><br>
																<div class="checkbox checkbox-inline">
																	<input type="checkbox" id="germany" value="option1" checked />
																	<label for="germany">Membership 1</label>
																</div>
																<div class="checkbox checkbox-inline">
																	<input type="checkbox" id="germany" value="option2" />
																	<label for="germany">Membership 2</label>
																</div>
																<div class="checkbox checkbox-inline">
																	<input type="checkbox" id="germany" value="option3"  />
																	<label for="germany">Membership 3</label>
																</div>
																<div class="checkbox checkbox-inline">
																	<input type="checkbox" id="germany" value="option3"  />
																	<label for="germany">Membership 4</label>
																</div>
															</div>
															
															
													
													</div>
													<div class="col-lg-3">
													
														<button class="btn btn-primary pull-right" type="button">Create offer</button>
														
														
													</div>
													
													
													
												</div>
											</div>
											
										</div>	
										
										
										<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
											<div class="panel">
												<div class="panel-heading">
													<h4>Add new offer (if refer)</h4>
												</div>
												<div class="panel-body">
													
													<div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
															<div class="form-group">
																<label for="userName">Activity</label>
																<select class="form-control" name="role">
																	<option value="1"  >Purchase of product</option>
																	<option value="1">Purchase of service</option>
																	<option value="2" >Purchase of package</option>
																	<option value="2"  >Given feedback</option>
																	<option value="1" selected>Refer new client</option>
																	<option value="2" >Appointment via website</option>
																	<option value="2" >Total bill amount</option>
																	<option value="2" >Use of reward points</option>
																</select>
															</div>
													</div>
													<div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
															<div class="form-group">
																<label for="userName">No. of client refered</label>
																<input type="text" class="form-control"  name="date" placeholder="1" required>
															</div>
													</div>
													<div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
															<div class="form-group">
																<label for="userName">Reward points to be given</label>
																<input type="text" class="form-control"  name="date" placeholder="1" >
															</div>
													</div>
													
													<div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
															<div class="form-group">
																<label for="userName">Free addon</label>
																<input type="text" class="form-control"  name="date" placeholder="" required>
															</div>
													</div>
													
													
													
													<div class="clearfix"></div>
													<div class="col-lg-9">
													
														<div class="form-group">
																<label for="userName"><strong>Offer valid for membership</strong></label><br>
																<div class="checkbox checkbox-inline">
																	<input type="checkbox" id="germany" value="option1" checked />
																	<label for="germany">Membership 1</label>
																</div>
																<div class="checkbox checkbox-inline">
																	<input type="checkbox" id="germany" value="option2" />
																	<label for="germany">Membership 2</label>
																</div>
																<div class="checkbox checkbox-inline">
																	<input type="checkbox" id="germany" value="option3"  />
																	<label for="germany">Membership 3</label>
																</div>
																<div class="checkbox checkbox-inline">
																	<input type="checkbox" id="germany" value="option3"  />
																	<label for="germany">Membership 4</label>
																</div>
															</div>
															
															
													
													</div>
													<div class="col-lg-3">
													
														<button class="btn btn-primary pull-right" type="button">Create offer</button>
														
														
													</div>
													
													
													
												</div>
											</div>
											
										</div>	
										
										
										<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
											<div class="panel">
												<div class="panel-heading">
													<h4>Add new offer (if appointment)</h4>
												</div>
												<div class="panel-body">
													
													<div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
															<div class="form-group">
																<label for="userName">Activity</label>
																<select class="form-control" name="role">
																	<option value="1"  >Purchase of product</option>
																	<option value="1">Purchase of service</option>
																	<option value="2" >Purchase of package</option>
																	<option value="2"  >Given feedback</option>
																	<option value="1" >Refer new client</option>
																	<option value="2" selected >Appointment via website</option>
																	<option value="2" >Total bill amount</option>
																	<option value="2" >Use of reward points</option>
																</select>
															</div>
													</div>
													<div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
															<div class="form-group">
																<label for="userName">No. of appointment</label>
																<input type="text" class="form-control"  name="date" placeholder="1" required>
															</div>
													</div>
													<div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
															<div class="form-group">
																<label for="userName">Discount</label>
																<table>
																
																<tr>
																
																<td width="70%">
																<input  name="disc_row[]" class="form-control disc_row"  value="0">
																</td>
																
																<td width="30%">
																	
																	<select class="form-control" name="role">
																		<option value="1" selected >%</option>
																		<option value="1"><?= CURRENCY ?></option>
																		
												
																	</select>
									
									
																</td>
																
																</tr>
																
																</table>
															</div>
													</div>
													
													
													<div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
															<div class="form-group">
																<label for="userName">Reward points to be given</label>
																<input type="text" class="form-control"  name="date" placeholder="1" >
															</div>
													</div>
													
													<div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
															<div class="form-group">
																<label for="userName">Free addon</label>
																<input type="text" class="form-control"  name="date" placeholder="" required>
															</div>
													</div>
													
													
													
													<div class="clearfix"></div>
													<div class="col-lg-9">
													
														<div class="form-group">
																<label for="userName"><strong>Offer valid for membership</strong></label><br>
																<div class="checkbox checkbox-inline">
																	<input type="checkbox" id="germany" value="option1" checked />
																	<label for="germany">Membership 1</label>
																</div>
																<div class="checkbox checkbox-inline">
																	<input type="checkbox" id="germany" value="option2" />
																	<label for="germany">Membership 2</label>
																</div>
																<div class="checkbox checkbox-inline">
																	<input type="checkbox" id="germany" value="option3"  />
																	<label for="germany">Membership 3</label>
																</div>
																<div class="checkbox checkbox-inline">
																	<input type="checkbox" id="germany" value="option3"  />
																	<label for="germany">Membership 4</label>
																</div>
															</div>
															
															
													
													</div>
													<div class="col-lg-3">
													
														<button class="btn btn-primary pull-right" type="button">Create offer</button>
														
														
													</div>
													
													
													
												</div>
											</div>
											
										</div>	
										
										
										<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
											<div class="panel">
												<div class="panel-heading">
													<h4>Add new offer (if bill amount)</h4>
												</div>
												<div class="panel-body">
													
													<div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
															<div class="form-group">
																<label for="userName">Activity</label>
																<select class="form-control" name="role">
																	<option value="1"  >Purchase of product</option>
																	<option value="1">Purchase of service</option>
																	<option value="2" >Purchase of package</option>
																	<option value="2"  >Given feedback</option>
																	<option value="1" >Refer new client</option>
																	<option value="2"  >Appointment via website</option>
																	<option value="2" selected>Total bill amount</option>
																	<option value="2" >Use of reward points</option>
																</select>
															</div>
													</div>
													<div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
															<div class="form-group">
																<label for="userName">Min amount of bill</label>
																<input type="text" class="form-control"  name="date" placeholder="1" required>
															</div>
													</div>
													<div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
															<div class="form-group">
																<label for="userName">Discount to be given on total bill amount.</label>
																<table>
																
																<tr>
																
																<td width="70%">
																<input  name="disc_row[]" class="form-control disc_row"  value="0">
																</td>
																
																<td width="30%">
																	
																	<select class="form-control" name="role">
																		<option value="1" selected >%</option>
																		<option value="1"><?= CURRENCY ?></option>
																		
												
																	</select>
									
									
																</td>
																
																</tr>
																
																</table>
															</div>
													</div>
													
													
													<div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
															<div class="form-group">
																<label for="userName">Add on reward points to be given (extra then on products, services & packages)</label>
																<input type="text" class="form-control"  name="date" placeholder="1" >
															</div>
													</div>
													
													<div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
															<div class="form-group">
																<label for="userName">Free addon</label>
																<input type="text" class="form-control"  name="date" placeholder="" required>
															</div>
													</div>
													
													<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
															<div class="form-group">
																<label for="userName">Amount includes</label>
																<div class="checkbox checkbox-inline">
																	<input type="checkbox" id="germany" value="option1" checked />
																	<label for="germany">Services</label>
																</div>
																<div class="checkbox checkbox-inline">
																	<input type="checkbox" id="germany" value="option2" />
																	<label for="germany">Products</label>
																</div>
																<div class="checkbox checkbox-inline">
																	<input type="checkbox" id="germany" value="option3"  />
																	<label for="germany">Package</label>
																</div>
															</div>
													</div>
													
													<div class="clearfix"></div>
													<div class="col-lg-9">
													
														<div class="form-group">
																<label for="userName"><strong>Offer valid for membership</strong></label><br>
																<div class="checkbox checkbox-inline">
																	<input type="checkbox" id="germany" value="option1" checked />
																	<label for="germany">Membership 1</label>
																</div>
																<div class="checkbox checkbox-inline">
																	<input type="checkbox" id="germany" value="option2" />
																	<label for="germany">Membership 2</label>
																</div>
																<div class="checkbox checkbox-inline">
																	<input type="checkbox" id="germany" value="option3"  />
																	<label for="germany">Membership 3</label>
																</div>
																<div class="checkbox checkbox-inline">
																	<input type="checkbox" id="germany" value="option3"  />
																	<label for="germany">Membership 4</label>
																</div>
															</div>
															
															
													
													</div>
													<div class="col-lg-3">
													
														<button class="btn btn-primary pull-right" type="button">Create offer</button>
														
														
													</div>
													
													
													
												</div>
											</div>
											
										</div>	
										
										
										<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
											<div class="panel">
												<div class="panel-heading">
													<h4>Add new offer (Use of reward points)</h4>
												</div>
												<div class="panel-body">
													
													<div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
															<div class="form-group">
																<label for="userName">Activity</label>
																<select class="form-control" name="role">
																	<option value="1"  >Purchase of product</option>
																	<option value="1">Purchase of service</option>
																	<option value="2" >Purchase of package</option>
																	<option value="2"  >Given feedback</option>
																	<option value="1" >Refer new client</option>
																	<option value="2"  >Appointment via website</option>
																	<option value="2" >Total bill amount</option>
																	<option value="2" selected>Use of reward points</option>
																</select>
															</div>
													</div>
													<div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
															<div class="form-group">
																<label for="userName">Reward points</label>
																<input type="text" class="form-control"  name="date" placeholder="1" required>
															</div>
													</div>
													<div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
															<div class="form-group">
																<label for="userName">Discount to be given on total bill amount.</label>
																<table>
																
																<tr>
																
																<td width="70%">
																<input  name="disc_row[]" class="form-control disc_row"  value="0">
																</td>
																
																<td width="30%">
																	
																	<select class="form-control" name="role">
																		<option value="1" selected >%</option>
																		<option value="1"><?= CURRENCY ?></option>
																		
												
																	</select>
									
									
																</td>
																
																</tr>
																
																</table>
															</div>
													</div>
													
													
													
													
													<div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
															<div class="form-group">
																<label for="userName">Free addon</label>
																<input type="text" class="form-control"  name="date" placeholder="" required>
															</div>
													</div>
													
													<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
															<div class="form-group">
																<label for="userName">Discount applicable on includes</label>
																<div class="checkbox checkbox-inline">
																	<input type="checkbox" id="germany" value="option1" checked />
																	<label for="germany">Services</label>
																</div>
																<div class="checkbox checkbox-inline">
																	<input type="checkbox" id="germany" value="option2" />
																	<label for="germany">Products</label>
																</div>
																<div class="checkbox checkbox-inline">
																	<input type="checkbox" id="germany" value="option3"  />
																	<label for="germany">Package</label>
																</div>
															</div>
													</div>
													
													<div class="clearfix"></div>
													<div class="col-lg-9">
													
														<div class="form-group">
																<label for="userName"><strong>Offer valid for membership</strong></label><br>
																<div class="checkbox checkbox-inline">
																	<input type="checkbox" id="germany" value="option1" checked />
																	<label for="germany">Membership 1</label>
																</div>
																<div class="checkbox checkbox-inline">
																	<input type="checkbox" id="germany" value="option2" />
																	<label for="germany">Membership 2</label>
																</div>
																<div class="checkbox checkbox-inline">
																	<input type="checkbox" id="germany" value="option3"  />
																	<label for="germany">Membership 3</label>
																</div>
																<div class="checkbox checkbox-inline">
																	<input type="checkbox" id="germany" value="option3"  />
																	<label for="germany">Membership 4</label>
																</div>
															</div>
															
															
													
													</div>
													<div class="col-lg-3">
													
														<button class="btn btn-primary pull-right" type="button">Create offer</button>
														
														
													</div>
													
													
													
												</div>
											</div>
											
										</div>	
										
										
										<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
											<div class="panel">
												<div class="panel-heading">
													<h4>Add new offer</h4>
												</div>
												<div class="panel-body">
													<div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
															<div class="form-group">
																<label for="userName">Offer name</label>
																<input type="text" class="form-control"  name="date" placeholder="" required>
															</div>
													</div>
													<div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
															<div class="form-group">
																<label for="userName">Offer valid till</label>
																<input type="text" class="form-control date"  name="date" placeholder="" required readonly>
															</div>
													</div>
													
													<div class="clearfix"></div>
													
													
													<div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
															<div class="form-group">
																<label for="userName">Activity</label>
																<select class="form-control" name="role">
																	<option value="1"  >Purchase of product</option>
																	<option value="1">Purchase of service</option>
																	<option value="2" >Purchase of package</option>
																	<option value="2" >Given feedback</option>
																	<option value="1">Refer new client</option>
																	<option value="2" >Appointment via website</option>
																	<option value="2" >Total bill amount</option>
																	<option value="2" >Use of reward points</option>
																	<option value="2"  selected>Other</option>
																</select>
															</div>
													</div>
													<div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
															<div class="form-group">
																<label for="userName">Reward point given to client</label>
																<input type="text" class="form-control"  name="date" placeholder="" required>
															</div>
													</div>
													<div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
															<div class="form-group">
																<label for="userName">Discount</label>
																<table>
																
																<tr>
																
																<td width="70%">
																<input  name="disc_row[]" class="form-control disc_row"  value="0">
																</td>
																
																<td width="30%">
																	
																	<select class="form-control" name="role">
																		<option value="1" selected >%</option>
																		<option value="1"><?= CURRENCY ?></option>
																		
												
																	</select>
									
									
																</td>
																
																</tr>
																
																</table>
															</div>
													</div>
													
													<div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
															<div class="form-group">
																<label for="userName">Free addon</label>
																<input type="text" class="form-control"  name="date" placeholder="" required>
															</div>
													</div>
													
													
													
													<div class="clearfix"></div>
													<div class="col-lg-9">
													
														<div class="form-group">
																<label for="userName"><strong>Offer valid for membership</strong></label><br>
																<div class="checkbox checkbox-inline">
																	<input type="checkbox" id="germany" value="option1" checked />
																	<label for="germany">Membership 1</label>
																</div>
																<div class="checkbox checkbox-inline">
																	<input type="checkbox" id="germany" value="option2" />
																	<label for="germany">Membership 2</label>
																</div>
																<div class="checkbox checkbox-inline">
																	<input type="checkbox" id="germany" value="option3"  />
																	<label for="germany">Membership 3</label>
																</div>
																<div class="checkbox checkbox-inline">
																	<input type="checkbox" id="germany" value="option3"  />
																	<label for="germany">Membership 4</label>
																</div>
															</div>
															
															
													
													</div>
													<div class="col-lg-3">
													
														<button class="btn btn-primary pull-right" type="button">Create offer</button>
														
														
													</div>
													
													
													
												</div>
											</div>
											
										</div>	
										
										
										
										
						
						
										
						
						
									</div>
									
									
							
						
						<div class="clearfix"></div>
						
						
						
												
					</div>
					<!-- Row ends -->

				</div>
				<!-- Main container ends -->
			
			</div>
			<!-- Dashboard Wrapper End -->
		
		</div>
		<!-- Container fluid ends -->
<script>
function checkAvailability() {
	var cat = $('#scat').val();
	//alert(cat);
jQuery.ajax({
url: "autocomplete/checkcat.php?cat="+$("#scat").val(),
data:'cat='+$("#scat").val(),
type: "POST",
success:function(data){
$("#check-status").html(data);
if(data === "﻿Invalid Category"){
	$('#scat').val("");
}
},
error:function (){}
});
}


function showmodal(d,s){
$.ajax({
url: "ajax/timeslot.php?date="+d+"&staff="+s,
type: "POST",
success:function(data){
	$("#appoint").html(data);
	$("#appointment").modal("show");
},
error:function (){}
});
}

</script>

<script>
function checkcat() {
	var cat = $('#cat').val();
jQuery.ajax({
url: "autocomplete/checkcats.php?cat="+$("#cat").val(),
data:'cat='+$("#scat").val(),
type: "POST",
success:function(data){
$("#cat-status").html(data);
if(data === "﻿Already Exist"){
	$('#cat').val("");
}
},
error:function (){}
});
}
</script>

<?php 
include "footer.php"; 

function getcat($cid) {
	global $conn;
	global $branch_id;
			$sql="SELECT * from expensecat where id='$cid' and branch_id='".$branch_id."'";
			$result=query_by_id($sql,[],$conn);
			foreach($result as $row)
			{
				return $row['title'];
			}
    }
	
function get_user($cid) 
{
	global $conn;
			$sql="SELECT * from user where id='$cid'";
			$result=query_by_id($sql,[],$conn);
			foreach($result as $row)
			{
				return $row['name'];
			}
}
?>

<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
		<script src="js/jquery.js"></script>

		<!-- Include all compiled plugins (below), or include individual files as needed -->
		<script src="js/bootstrap.min.js"></script>
		
		<!-- jquery ScrollUp JS -->
		<script src="js/scrollup/jquery.scrollUp.js"></script>

		<!-- Datepicker -->
		<script src="js/jquery-ui.min.js"></script>

		<!-- Sparkline Graphs -->
		<script src="js/sparkline/retina.js"></script>
		<script src="js/sparkline/custom-sparkline.js"></script>

		<!-- Horizontal Bar JS -->
		<script src="js/horizontal-bar/horizBarChart.min.js"></script>
		<script src="js/horizontal-bar/horizBarCustom.js"></script>

		<!-- D3 JS -->
		<script src="js/d3/d3.v3.min.js"></script>

		<!-- C3 Graphs -->
		<script src="js/c3/c3.js"></script>
		<script src="js/c3/c3.custom.js"></script>

		<!-- Gauge JS -->
		<script src="js/d3/gauge.js"></script>
		<script src="js/d3/gauge-custom.js"></script>

		<!-- Rating JS -->
		<script src="js/rating/jquery.raty.js"></script>

		<!-- JVector Map -->
		<script src="js/jvectormap/jquery-jvectormap-1.2.2.min.js"></script>
		<script src="js/jvectormap/gdp-data.js"></script>
		<script src="js/jvectormap/world-mill-en.js"></script>

		<!-- Custom JS -->
		<script src="js/custom.js"></script>
		<script src="js/custom-widgets.js"></script>