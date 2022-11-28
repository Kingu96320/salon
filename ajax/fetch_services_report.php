<?php
	include "../includes/db_include.php";
	$branch_id = $_SESSION['branch_id'];
    if(isset($_GET['type']) && $_GET['type'] == '1' ){
	?>
	<table class="table table-striped table-bordered">
		<tr>
			<th width="80">Sr. No</th>
			<th>Service Name</th>
			<th>Total Count</th>
			<th>Total Revenue</th>
		</tr>
		<tr>
			<?php
			    $services = query("SELECT * FROM service", [], $conn);
			    $i=0;
			    if($services){
					foreach($services as $service){
						$service_data = query("SELECT count(id) as service_count, sum(price) as price from invoice_items_".$branch_id." where type='Service' and service = 'sr,".$service['id']."' and date(start_time) >= '".$_GET['from']."' and date(start_time) <= '".$_GET['to']."' ", [], $conn)[0];
						$i += 1;
						$service_count = $service_data['service_count'];
						if($service_count == "" || $service_count == NULL)
							$service_count = 0;
						$service_price = $service_data['price'];
						if($service_price == "" || $service_price == NULL)
							$service_price = 0;
						echo "<tr><td>".$i."</td><td>".$service['name']."</td><td>".$service_count."</td><td>".number_format($service_price,2)."</td></tr>";
					}				    	
			    } else{
			        echo "<tr><td class='text-center' colspan='4'>No record found!!</td></tr>";
			    }
            	
			?>
		</tr>			
	</table>
	<?php
	}
	else if(isset($_GET['type']) && $_GET['type'] == '2' ){
	?>
		<table class="table table-striped table-bordered">
			<tr>
				<th width="80">Sr. No</th>
				<th>Product Name</th>
				<th>Total Count</th>
				<th>Total Revenue</th>
			</tr>
			<tr>
				<?php
				    $services = query("SELECT * FROM products", [], $conn);
				    $i=0;
				    if($services){
						foreach($services as $service){
							$service_data = query("SELECT count(id) as service_count, sum(price) as price from invoice_items_".$branch_id." where type='Product' and service='pr,".$service['id']."' and date(start_time) >= '".$_GET['from']."' and date(start_time) <= '".$_GET['to']."' ", [], $conn)[0];
							$i += 1;
							$service_count = $service_data['service_count'];
							if($service_count == "" || $service_count == NULL){
								$service_count = 0;
							}
							$service_price = $service_data['price'];
							if($service_price == "" || $service_price == NULL)
								$service_price = 0;
							echo "<tr><td>".$i."</td><td>".$service['name']."</td><td>".$service_count."</td><td>".$service_price."</td></tr>";
						}				    	
				    } else {
				        echo "<tr><td class='text-center' colspan='4'>No record found!!</td></tr>";
				    }
                	
				?>
			</tr>			
		</table>
	<?php
	}
	else if(isset($_GET['type']) && $_GET['type'] == '3' ){
	?>
		<table class="table table-striped table-bordered">
			<tr>
				<th width="80">Sr. No</th>
				<th>Package Name</th>
				<th>Total Count</th>
				<th>Total Revenue</th>
			</tr>
			<tr>
				<?php
				    $services = query("SELECT * FROM packages", [], $conn);
				    $i=0;
				    if($services){
						foreach($services as $service){
							$service_data = query("SELECT count(id) as service_count, sum(price) as price from invoice_items_".$branch_id." where type='Package' and service = 'pa,".$service['id']."' and date(start_time) >= '".$_GET['from']."' and date(start_time) <= '".$_GET['to']."'  ", [], $conn)[0];
							$i += 1;
							$service_count = $service_data['service_count'];
							if($service_count == "" || $service_count == NULL)
								$service_count = 0;
							$service_price = $service_data['price'];
							if($service_price == "" || $service_price == NULL)
								$service_price = 0;
							echo "<tr><td>".$i."</td><td>".$service['name']."</td><td>".$service_count."</td><td>".$service_price."</td></tr>";
						}				    	
				    } else {
				        echo "<tr><td class='text-center' colspan='4'>No record found!!</td></tr>";
				    }
                	
				?>
			</tr>			
		</table>
	<?php
	}
	else if(isset($_GET['type']) && $_GET['type'] == '4' ){
	?>
		<table class="table table-striped table-bordered">
			<tr>
				<th width="80">Sr. No</th>
				<th>Membership Name</th>
				<th>Total Count</th>
				<th>Total Revenue</th>
			</tr>
			<tr>
				<?php
				    $services = query("SELECT * FROM membership_discount", [], $conn);
				    $i=0;
				    if($services){
						foreach($services as $service){
							$service_data = query("SELECT count(id) as service_count, sum(price) as price from invoice_items_".$branch_id." where type='mem' and service = 'mem,".$service['id']."' and date(start_time) >= '".$_GET['from']."' and date(start_time) <= '".$_GET['to']."'  ", [], $conn)[0];
							$i += 1;
							$service_count = $service_data['service_count'];
							if($service_count == "" || $service_count == NULL)
								$service_count = 0;
							$service_price = $service_data['price'];
							if($service_price == "" || $service_price == NULL)
								$service_price = 0;
							echo "<tr><td>".$i."</td><td>".$service['membership_name']."</td><td>".$service_count."</td><td>".$service_price."</td></tr>";
						}				    	
				    } else {
				        echo "<tr><td class='text-center' colspan='4'>No record found!!</td></tr>";
				    }
				?>
			</tr>			
		</table>
	<?php
	}

	?>