<?php
include "../includes/db_include.php";
$branch_id = $_SESSION['branch_id'];
$date = date('Y-m-d');
?>
<div class="col-lg-12">	
	<div class="table-responsive">
	<span id="close" onclick="this.parentNode.parentNode.parentNode.removeChild(this.parentNode.parentNode); return false;" style="float:right;display:inline-block;padding:2px 3px;background:#ccc;">x</span>
		<table id="smstab" class="table tableprint1 table-bordered no-margin">
			<thead>
				<tr>
				<th>Name</th>
				<th>Contact Number</th>
				<th>Gender</th>
				<th>Action</th>
				</tr>
			</thead>
			<tbody>
			<?php
				$sql="SELECT * from client where active=0 and branch_id='".$branch_id."' order by id asc";
				$result=query_by_id($sql,[],$conn);
				foreach($result as $row) {
					$chk = getclient($row['id']);
					if($chk==1)
						continue;
				 ?>
					<tr>										
					<td><?php echo $row['name']; ?></td>
					<td><?php echo $row['cont']; ?></td>
					<td><?php echo $row['gender']; ?></td>
					<td><a href="editclients.php?id=<?php echo $row['id']; ?>" ><button class="btn btn-info btn-xs" type="button">Edit</button></a> <a href="clientprofile.php?cid=<?php echo $row['id']; ?>"><button class="btn btn-info btn-xs" type="button">View Profile</button></a></td>
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
		$date = date('Y-m-d');
		$sql="SELECT * from invoice_".$branch_id." where active=0 and type=2 and client=$cid and doa='$date' and branch_id='".$branch_id."' ORDER BY id DESC";
		$result=query_by_id($sql,[],$conn);
		if($result) 
		{
			return 0;
		}else{
			return 1;
		}
	}
?>