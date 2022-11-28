<?php
include_once '../includes/db_include.php';
$branch_id = $_SESSION['branch_id'];
if(isset($_POST['filter_type']) && $_POST['filter_type'] == 'expenses'){
    $start_date = $_POST['startdate'];
    $end_date = $_POST['enddate'];
    $query = "SELECT pm.name as payment_method, ec.title as type,e.*,r.name as r_name,u.name as uname FROM expense e LEFT JOIN expensecat ec on ec.id=e.cat LEFT JOIN recipient r on r.id=e.recipient LEFT JOIN user u on u.id=e.user LEFT JOIN payment_method pm ON pm.id = e.mop WHERE e.active=0 AND e.date >= '".$start_date."' AND e.date <= '".$end_date."' and e.branch_id='".$branch_id."' order by date desc";
    $result = query($query,[],$conn);
    $total_amount = 0;
    if($result){
        foreach($result as $row)
		{
			?>
			<tr>
				<td><?= my_date_format($row['date'])?></td>
				<td><?= $row['type'];?></td>
				<td><?= number_format($row['amount'],2);?></td>
				<td><?= $row['payment_method'] ?></td>
				<td><?= $row['r_name'];?></td>
				<td><?= $row['uname'];?></td>
				<td><a href="expenses.php?eid=<?=$row['id']?>" class="btn btn-warning btn-xs"><i class="icon-edit"></i>Edit</a> 
				<?php if(DELETE_BUTTON_INACTIVE == 'true'){ ?>
				    <a href="#" onclick="return deleteDisabled();" class="btn btn-danger btn-xs"><i class="icon-delete"></i>Delete</a> 
				<?php } else { ?>
				    <a href="expenses.php?did=<?=$row['id']?>" onclick="return confirm('Are you sure?');" class="btn btn-danger btn-xs"><i class="icon-delete"></i>Delete</a> 
				<?php } ?>
				</td>
			</tr>
		    <?php 
		    $total_amount = $total_amount+$row['amount'];
		} ?>
		    <tr>
		        <td colspan="2" class="text-right"><strong>Total Amount </strong></td>
		        <td><strong><?= number_format($total_amount,2); ?></strong></td>
		        <td colspan="4"></td>
		    </tr>
		<?php
    } else {
        ?>
            <tr>
                <td colspan="7" class="text-center">No result found</td>
            </tr>
        <?php
    }
}

?>