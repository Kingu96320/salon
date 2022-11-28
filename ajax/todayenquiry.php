<?php
include "../includes/db_include.php";
$branch_id = $_SESSION['branch_id'];
$date = date('Y-m-d');
?>
<div class="col-lg-12">	
	<div class="table-responsive">
	<span id="close" onclick="this.parentNode.parentNode.parentNode.removeChild(this.parentNode.parentNode); return false;" style="float:right;display:inline-block;padding:2px 3px;background:#ccc;">x</span>
		<table id="smstab" class="table tableenquiry table-striped no-margin">
			<thead>
			<tr>									
				<th>Name</th>
				<th>Email</th>
				<th>Phone</th>
				<th>Date to follow</th>
				<th>Lead type</th>
				<th>Enquiry for</th>					
				<th>Action</th>
			</tr>
			</thead>
			<tbody>
			<?php
			$sql1="SELECT * from enquiry where active=0 and regon='$date' and branch_id='".$branch_id."' order by id desc";
			$result1=query_by_id($sql1,[],$conn);
			$it = 1;
			foreach($result1 as $row1) {
			?>
			<tr>									
				<td><?php echo $row1['customer']; ?></td>
				<td><?php echo $row1['email']; ?></td>
				<td><?php echo $row1['cont']; ?></td>
				<td><?php echo $row1['datefollow']; ?></td>
				<td><?php echo $row1['type']; ?></td>
				<td><?php echo ($row1['enquiry'] !='')?getEnquiryfor($row1['enquiry']):'' ?></td>			
			<td> <a href="enquiry.php?id=<?php echo $row1['id']; ?>" class="btn btn-info btn-xs">Edit </a> <!--<a href="enquiry.php?d=<?php echo $row1['id']; ?>" onclick="return confirm('Are You Sure?')" class="btn btn-success btn-xs">Delete</a>--></td>
			</tr>
			<?php 
			$it ++ ;
			} ?>
		</tbody>
	</table>
										
	</div>
</div>
<br>
 