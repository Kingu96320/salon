<?php 
	include_once './includes/db_include.php';
	$branch_id = $_SESSION['branch_id'];
	$uid = $_SESSION['uid'];
	
	if(isset($_GET['cid'])){
		$cid = $_GET['cid'];
		$sql="SELECT * FROM `clientpackages` where inv='$cid'";
		$result=query_by_id($sql,[],$conn);
		foreach($result as $row) {
			$pck = $row['package'];
			$clt = $row['client'];
			$inv = $row['inv'];
		}
		$arrservice = array();
		$sql3="SELECT distinct(service) FROM `packageservice` where pid='$pck' and active=0";
		$result3=query_by_id($sql3,[],$conn);
		$r = 0;
		foreach($result3 as $row3) {
			$arrservice['ser'][$r] = $row3['service'];
			//$arrservice['qt'][$r] = sumservices($pck,$row3['service'],$clt);
			$r++;
		}
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
			
			<div class="col-md-12 col-sm-12 col-xs-12">
				
				<div class="panel">
					<div class="panel-heading">
						<h4>Package services details </h4>
					</div>
					<div class="panel-body">
						<div class="table-responsive">
							<table class="table grid table-bordered no-margin">
								<thead>
									<tr>
										
										<th>Package name</th>
										<th>Branch</th>
										<th>Service name</th>
										<th>Total quantity</th>
										<th>Remaining quantity</th>
										<th>History</th>
									</tr>
								</thead>
								<tbody> 
									<?php 
										$sql5 = "SELECT p.branch_id, p.id, cpsu.id as service_id , p.name as package_name, s.name as service_name, cpsu.quantity as qty, cpsu.quantity_used as used_qty, cpsu.inv FROM client_package_services_used cpsu
											LEFT JOIN packages p ON p.id = cpsu.c_pack_id
											LEFT JOIN service s ON s.id = SUBSTRING_INDEX(cpsu.c_service_id,',',-1)
											WHERE cpsu.client_id = '".$_GET['cid']."' AND cpsu.c_pack_id = '".$_GET['pid']."' AND cpsu.inv = '".$_GET['invid']."'";
										$result5 = query_by_id($sql5,[],$conn);
										foreach($result5 as $row5) {
										?>
										<tr>
											<td><?= $row5['package_name'] ?></td>
											<td><?= ucfirst(branch_by_id($row5['branch_id'])); ?></td>
											<td><?= $row5['service_name'] ?></td>
											<td><?= $row5['qty'] ?></td>
											<td><?= $row5['qty']-$row5['used_qty'] ?></td>
											<td><button onclick="package_history(<?= $row5['id'] ?>, <?= $row5['service_id'] ?>)" type="button" class="btn btn-danger btn-xs"><i class="fa fa-history" aria-hidden="true"></i> View history</button></td>
										</tr>
									<?php } ?>
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

<div class="modal fade in disableOutsideClick" id="package_history" role="dialog">
	<div class="modal-dialog modal-lg">
		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Package service history <span class="package_name"></span></h4>
			</div>
			<div class="modal-body">
				<div class="row">
				    <div class="col-md-12">
				        <div id="package_history_data">
				            
				            
				        </div>
				    </div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" data-dismiss="modal" class="btn btn-danger pull-right" id=""><i class="fa fa-times" aria-hidden="true"></i>Close</button>
			</div>
		</div>
	</div>
</div>

	<?php 		
		include "footer.php";
		
		function firstvisit($uid){
			global $conn;
			global $branch_id;
			$sql="SELECT * from invoice_".$branch_id." where client=$uid and type=2 and branch_id='".$branch_id."' order by id asc limit 1";
			$result=query_by_id($sql,[],$conn)[0];
			if ($result) {
				return $result['doa'];
				}else{
				return "NA";
			}
		}
		
		function package($uid){
			global $conn;
			global $branch_id;
			$sql="SELECT * FROM `packages` where id=$uid and branch_id='".$branch_id."'";
			$result=query_by_id($sql,[],$conn)[0];
			if ($result) {
				return $result['name'];
				}else{
				return "NA";
			}
		}
		
		function packageprice($uid){
			global $conn;
			global $branch_id;
			$sql="SELECT * FROM `packages` where id=$uid and branch_id='".$branch_id."'";
			$result=query_by_id($sql,[],$conn)[0];
			if ($result) {
				return $result['price'];
				}else{
				return "NA";
			}
		}
		
		function servicsavail($pid,$inv){
			global $conn;
			global $branch_id;
			$sql="SELECT sum(quantity) as total FROM `clientservices` where branch_id='".$branch_id."'";
			$result=query_by_id($sql,[],$conn)[0];
			if ($result) {
				return $result['total'];
				}else{
				return "0";
			}
		}
		
		function sumservice($pkg,$inv){
			global $conn;
			global $branch_id;
			$sql="SELECT sum(quantity) as total FROM `client_package_services_used` WHERE `c_pack_id`='$pkg' and `inv`='$inv' and active=1 and branch_id='".$branch_id."'";
			$result=query_by_id($sql,[],$conn)[0];
			if ($result) {
				return $result['total'];
				}else{
				return "0";
			}
		}
		function sumpack($uid,$cid,$sid){
			
			global $conn;
			global $branch_id;
			//$sql="SELECT sum(quantity) as total FROM `packageservice` WHERE pid=$uid and active=0";
			$sql="SELECT sum(ii.quantity) as used_quantity FROM `invoice_items_".$branch_id."` ii where ii.client='$cid' and active='0' and service='$sid' and package_id='$uid' and ii.branch_id='".$branch_id."'";
			$result=query_by_id($sql,[],$conn)[0];
			if ($result) {
				return $result['used_quantity'];
				}else{
				return "0";
			}
		}
		
		function suminvoice($cid){
			global $conn;
			global $branch_id;
			$sql="SELECT sum(paid) as paid,sum(bpaid) as bpaid,sum(total) as total,sum(chnge) as rett FROM `invoice_".$branch_id."` WHERE client=$cid and status <> 'Cancelled' and active=0 and branch_id='".$branch_id."'";
			$result=query_by_id($sql,[],$conn)[0];
			if ($result) {
				$total = $result['total'];
				$bpaid = $result['bpaid'];
				$paid  = $result['paid'];
				$chnge = $result['rett'];
				$clnt = getpayments($cid);
				$pend = $total - $bpaid - $paid - $clnt;
				//$pend = $pend + $chnge;
				return $pend;
				}else{
				return "0";
			}
		}
		
		function getpayments($cid){
			global $conn;
			global $branch_id;
			$sql="SELECT sum(credit) as credit FROM `payments_client` WHERE client=$cid and active=0 and branch_id='".$branch_id."'";
			$result=query_by_id($sql,[],$conn)[0];
			if ($result) {
				return $result['credit'];
				}else{
				return "0";
			}
		}
		
		function suminv($id){
			global $conn;
			global $branch_id;
			$sql="SELECT paid,bpaid,total FROM `invoice_".$branch_id."` WHERE id=$id  and active=0 and branch_id='".$branch_id."'";
			$result=query_by_id($sql,[],$conn)[0];
			if ($result) {
				$total = $result['total'];
				$bpaid = $result['bpaid'];
				$paid  = $result['paid'];
				$pend  = $total - $bpaid - $paid;
				return $pend;
				}else{
				return "0";
			}
		}
		
		
		
		function getdat($uid){
			global $conn;
			global $branch_id;
			$sql="SELECT * from invoice_".$branch_id." where id=$uid and branch_id='".$branch_id."'";
			$result=query_by_id($sql,[],$conn)[0];
			if ($result) {
				return $result['doa']." ".$result['itime'];
				}else{
				return "NA";
			}
		}
		function get_packages_total_services($pid){
			global $conn;
			global $branch_id;
			$sql="SELECT count(id) as total_service from `packageservice` where pid=$pid and active='0' and branch_id='".$branch_id."'";
			$result=query_by_id($sql,[],$conn);
			foreach($result as $row){
				return $row['total_service'];
			}
			
		}
	?>		

	<script type="text/javascript">
		
		function package_history(package_id, service_id){
			$.ajax({
				url : 'ajax/fetch_package_details.php',
				type : 'POST',
				dataType : 'json',
				data : {action : 'package_history', package_id : package_id, service_id : service_id },
				success : function(response){
					if(response.status == 1){
						var html = '<table class="table table-stripped table-bordered"><thead><tr><th>Used on</th><th>Branch</th><th>Quantity</th><th>Invoice</th></tr></thead><tbody>';
						$.each(response.data, function(key, value){
							html +='<tr><td>'+value.used_on+'</td><td>'+value.used_in_branch+'</td><td>'+value.quantity+'</td><td>'+value.invoice_id+'</td></tr>';
						});
						html += '</tbody></table>';
						$('#package_history_data').html(html);
					} else if(response.status == 0){
						$('#package_history_data').html('<p class="text-center">No history found!!</p>');
					}
					$('#package_history').modal('show');
				}
			});
		}
	</script>			