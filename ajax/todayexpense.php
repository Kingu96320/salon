<?php
include "../includes/db_include.php";
$branch_id = $_SESSION['branch_id'];
$date = date('Y-m-d');
?>
<div class="col-lg-12">	
	<div class="table-responsive">
	<span id="close" onclick="this.parentNode.parentNode.parentNode.removeChild(this.parentNode.parentNode); return false;" style="float:right;display:inline-block;padding:2px 3px;background:#ccc;">x</span>
	
	<form action="" method="POST">
	<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
		<div class="form-group row gutter">
			<label class=" control-label">From Date :</label>
			<input type="text" onChange="showexpenses();" id="expdate" class="form-control date" value="<?php echo (isset($_GET['esdate'])&&$_GET['esdate']!=''&&$_GET['esdate']!='undefined')?$_GET['esdate']:$date; ?>" name="sdate" readonly>
		</div>
	</div>
	<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
		<div class="form-group row gutter">
			<label class=" control-label">End Date :</label>
			<input type="text" onChange="showexpenses();" id="expsdate" class="form-control date" value="<?php echo (isset($_GET['eedate'])&&$_GET['eedate']!=''&&$_GET['eedate']!='undefined')?$_GET['eedate']:$date; ?>" name="edate" readonly>
		</div>
	</div>
	</form>
	
		<table id="table" class="table table-bordered no-margin">
			<thead>
				<tr>
				<th>Date</th>
				<th>Category</th>
				<th>Amount</th>
				<th>User</th>
				<th>Action</th>
				</tr>
			</thead>
			<tbody>
			<?php
			if($_GET['esdate']!="undefined"){
					$sdate = $_GET['esdate'];
					$edate = $_GET['eedate'];
					$dqry = "and date BETWEEN '$sdate' AND '$edate'";
				}else{
					$dqry = "and date='$date'";
				}
				$sql1="SELECT * from expense where active=0 and branch_id='".$branch_id."' ".$dqry." order by id desc";
				$result1=query_by_id($sql1,[],$conn);
				foreach($result1 as $row1) {
			?>
			<tr>
			<td><?php echo $row1['date']; ?></td>
			<td><?php echo getcat($row1['cat']); ?></td>
			<td><?php echo $row1['amount']; ?></td>
			<td><?php echo get_user($row1['user']); ?></td>
			<td><a href="expenses.php?exp=<?php echo $row1['id']; ?>"><button class="btn btn-info btn-xs" type="button">Edit</button></a> <!--<a href="services.php?d=<?php echo $row1['id']; ?>" onclick="return confirm('Are you sure?');"><button class="btn btn-info btn-xs" type="button">Delete</button></a>--></td>
			</tr>
			<?php } ?>
			</tbody>
		</table>
	</div>
</div>
<br>
<?php
function getcat($cid) {
	global $conn;
	global $branch_id;
			$sql="SELECT * from expensecat where id='$cid' and branch_id='".$branch_id."'";
			$result=query_by_id($sql,[],$conn);
			if($result) 
			{
				foreach($result as $row)
				{
				return $row['title'];
				}
			}
    }
	
function get_user($cid) {
	global $conn;
	global $branch_id;
			$sql="SELECT * from user where id='$cid' and branch_id='".$branch_id."'";
			$result=query_by_id($sql,[],$conn);
			if($result) 
			{  
				foreach($result as $row)
				{
				return $row['name'];
				}
			}
    }
	
 
?>