<?php
	require_once './header.php';
?>
<style>
    .btn-scat{
        border-radius : 3px;
        border-color: #333;
        margin: 0px auto;
    }
    .btn-scat.active{
        background: #333;
    }
</style>
 <div class="clear"></div>
 <section style="background:#fde0ea;">
 <div class="container">
	<div class="row " >
		<div class="col-lg-12 mt-5">
			<h2 class="text-center text-white featured-heading">BOOK APPOINTMENT</h2>
		</div>
	</div>
	<div class="clear"></div>
	<div class="row flex-column-reverse flex-md-row">
		<div class="col-lg-8 mt-5 mb-5">
			<div class="white-box p-4 appointment">
			<div class="row">
				<div class="col-lg-6 p-1">
					<label>Your name*</label>
					<div class="input-group mb-3">
						<div class="input-group-prepend">
							<span class="input-group-text" id="basic-addon1"><i class="fas fa-user" aria-hidden="true"></i></span>
						</div>
						<input type="text" id="name" class="form-control" placeholder="" aria-label="" aria-describedby="basic-addon1" required />
					</div>
				</div>
				<div class="col-lg-6 p-1">
					<label>Phone number*</label>
					<div class="input-group mb-3">
						<div class="input-group-prepend">
							<span class="input-group-text" id="basic-addon1"><i class="fas fa-phone" style="transform: rotate(90deg);" aria-hidden="true"></i></span>
						</div>
						<input type="text" id="number" class="form-control" placeholder="" aria-label="" aria-describedby="basic-addon1" required />
					</div>
				</div>
			</div>
			<div class="row ">
				<?php 
					$app_time = date('h:i A', strtotime('+2 hour'));
					$today_date = date('Y-m-d');
					$next_date = date('Y-m-d', strtotime('+1 day'));
					$next_sunday_date = date('Y-m-d', strtotime('next Sunday', strtotime($today_date)));
				?>
				<div class="col-lg-12 text-center" id="button-time-selection">
					<input type="hidden" id="btn-app-date-time" value="<?php echo $app_time ?>">
					<a href="javascript:void(0)" onclick="setAppDateTime('<?= $today_date ?>','<?= $app_time ?>')" class="btn btn-time">TODAY<br><small><?php echo $app_time; ?></small>
					</a>
					<a href="javascript:void(0)" onclick="setAppDateTime('<?= $next_date ?>','<?= $app_time ?>')" class="btn btn-time">TOMOROW<br><small><?php echo $app_time; ?></small>
					</a>
					<a href="javascript:void(0)" onclick="setAppDateTime('<?= $next_sunday_date ?>','<?= $app_time ?>')" class="btn btn-time">SUNDAY<br><small><?php echo $app_time; ?></small>
					</a>
					<hr>OR<hr>
				</div>
				<div class="col-lg-6 p-1">
						<label>Appointment date*</label>
						<div class="input-group mb-3">
							<div class="input-group-prepend">
								<span class="input-group-text" id="basic-addon1"><i class="fas fa-calendar-alt" aria-hidden="true"></i></span>
							</div>
							<input type="text" onchange="checkdate(this.value)" class="form-control date" min="2020-04-24" id="doa" placeholder="" aria-label="Appointment date" aria-describedby="basic-addon1" readonly required />
						    <span id="date-error" class="text-danger"></span>
						</div>
					</div>
					<div class="col-lg-6 p-1">
						<label>Appointment Time*</label>
						<div class="input-group mb-3">
							<div class="input-group-prepend">
								<span class="input-group-text" id="basic-addon1"><i class="far fa-clock" aria-hidden="true"></i></span>
							</div>
							<input type="text" class="form-control time" id="apptime" placeholder="" aria-label="Appointment Time" aria-describedby="basic-addon1" required readonly />
						    <span id="time-error" class="text-danger"></span>
						</div>
					</div>
	    
					<div class="col-lg-12" id="branches" style="margin-bottom:10px;">
					    
					</div>
					
					<div class="col-lg-12" style="margin: 0px auto; text-align: center;">
            	        <button type="button" class="btn btn-scat active" onclick="maincat($(this))" data-cat="0">All</button>
            	        <button type="button" class="btn btn-scat" onclick="maincat($(this))" data-cat="1"><i class="fa fa-male" aria-hidden="true"></i> Men</button>
            	        <button type="button" class="btn btn-scat" onclick="maincat($(this))" data-cat="2"><i class="fa fa-female" aria-hidden="true"></i> Women</button>
            	        <br /><br />
            	    </div><div class="clearfix"></div>
            	    
					<div class="col-lg-12 text-center" id="categories">
						<a href="javascript:void(0)" onclick="serviceList(0, $(this))" class="btn btn-time current">All</a>
					</div><br />
					<div class="col-lg-12 mt-1 mb-1" style="padding:0px;">
						<table class="table">
						  <thead>
						    <tr>
						      <th scope="col">Name</th>
						      <th scope="col">Price</th>
						      <!--<th scope="col">Service time</th>-->
						      <th scope="col"></th>
						    </tr>
						  </thead>
						  <tbody id="services">
						    
						  </tbody>
						</table>

				</div>
				
				
			
			</div>
			
			</div>
			
		</div>
		
		<div class="col-lg-4 mt-5 mb-5 ">
			
			<div class="white-box p-4">
			
				<h5>Cart</h5>
				
				<p>
					Date: <strong><?php echo date('d M Y'); ?></strong>
				</p>
				<table class="table cart">
				  <thead>
				    <tr>
				      <th scope="col">Name</th>
				      <th scope="col">Price</th>
				    </tr>
				  </thead>
				  <tbody id="selectedService">
				    <?php
				        if(isset($_SESSION['cart']) && count($_SESSION['cart']) > 0){
				            foreach($_SESSION['cart'] as $index=>$cart){
				                ?>
				                     <tr>
                        			       <th><a href="javascript:void(0)" onclick="deleteService($(this), <?= $index; ?>)"><i class="far fa-trash-alt" style="color:red;"></i> </a><?= $cart['name'] ?><br /><i class="fa fa-clock-o text-warning" aria-hidden="true"></i> <?= $cart['duration'] ?> Mins</th>
                        			      <td><?= $cart['price'] ?></td>
                        			      <input type="hidden" value="<?= $cart['sid'] ?>" class="service_id">
                        			      <input type="hidden" value="<?= $cart['price'] ?>" class="service_price">
                        			      <input type="hidden" value="<?= $cart['duration'] ?>" class="service_duration">
                        			 </tr>
				                <?php
				            }
				        } else {
				    ?>
				   	<tr class="empty_cart">
				   		<td colspan="3" class="text-center">Cart is empty</td>
				   	</tr>
				   	<?php } ?>
				  </tbody>
				</table>
                <?php
                    if(isset($_SESSION['cart']) && count($_SESSION['cart']) > 0){
                        echo '<a href="javascript:void(0)" onclick="bookservice()" class="btn btn-time checkout">Confirm</a>';
                    }
                ?>
			</div>
			
		</div>
	</div>
	<div class="clear"></div>
</div>
<?php
	require_once './footer.php';
?>