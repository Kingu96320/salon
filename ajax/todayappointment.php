<?php
include "../includes/db_include.php";
$date = date('Y-m-d');
?>
<div class="col-lg-12">
	<div class="table-responsive">
	<span id="close" onclick="this.parentNode.parentNode.parentNode.removeChild(this.parentNode.parentNode); return false;" style="float:right;display:inline-block;padding:2px 3px;background:#ccc;">x</span>
		<table id="table" class="table table-bordered no-margin">
		<thead>
			<tr>
				<th>Date Of Appointment</th>
				<th>Client</th>
				<th>Contact</th>
				<th>Time</th>
				<th>Status</th>
				<th>Manage</th>
			</tr>
		</thead>
		<tbody>
		<?php
			//$sql1="SELECT * from invoice where active=0 and invoice=0 and doa='$date' and type <> 3 order by id desc";
			$sql1 ="SELECT ai.*,c.name as client,c.cont,aso.name as app_source from `app_invoice_".$branch_id."` ai"
				  ." LEFT JOIN `app_source` aso on aso.id=ai.role "
				  ." LEFT JOIN `client` c on c.id=ai.client"
				  ." where ai.doa='$date' and ai.active=0 order by ai.id desc";
			$result1=query_by_id($sql1,[],$conn);
			foreach($result1 as $row1) {
		?>
		<tr>
			<td><?php echo $row1['doa']; ?></td>												
			<td><?php echo $row1['client']; ?></td>
			<td><?php echo $row1['cont']; ?></td>
			<td><?php echo $row1['itime']; ?></td>
			<td><?php echo $row1['status']; ?></td>
			<?php 
			$srt = $row1['status'];
			$link = "";
			if($srt=="Converted" || $srt=="Cancelled")
				$link = 'onclick="return false;"';
			else
				$link = '';
			?>
			<td><a href="appointment.php?id=<?=$row1['id']?>" class="btn btn-xs btn-primary">Edit</a>
												<?php if($row1['ss_created_status'] == 0){ ?>
													 
													<?php }if($row1['bill_created_status'] == 0){ ?>
													
												<a href="billing.php?bid=<?=$row1['id']?>" style="background-color: #008CBA;"class="btn btn-xs btn-primary">Create bill</a>
												
												<?php }else{ ?>
												<a class="btn btn-xs btn-success">Bill paid</a>
										<?php 	} ?>
				 </td>
			</tr>
			<?php } ?>
		</tbody>
	</table>
	</div>							
</div>
<br>
<?php
function getclient($cid){
		global $conn;
		global $branch_id;
		$sql="SELECT * from client where id=$cid and branch_id='".$branch_id."'";
		$result=query($sql,[],$conn);
		if($result) 
		{
			foreach($result as $row)
			{
			return $row['name'];
			}
		}
	}
		
function getcont($cid){
		global $conn;
		global $branch_id;
		$sql="SELECT * from client where id=$cid and branch_id='".$branch_id."'";
		$result=query_by_id($sql,[],$conn);
		if($result) 
		{
			foreach($result as $row)
			{
			return $row['cont'];
			}
		}
}
?>