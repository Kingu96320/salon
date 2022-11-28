<?php
    include_once './includes/db_include.php';
	$branch_id = $_SESSION['branch_id'];
    if($_SESSION['user_type'] != 'superadmin'){
        header('LOCATION: dashboard.php'); die();    
    }
    if(isset($_GET['filter_type']) && $_GET['filter_type'] == 'export'){
        $start_date = $_GET['startdate'];
        $end_date = $_GET['enddate'];
        
        header("Content-Type: application/xls");    
        header("Content-Disposition: attachment; filename=ExpenseReport_".$start_date.'to'.$end_date.".xls");  
        header("Pragma: no-cache"); 
        header("Expires: 0");
        
        $query = "SELECT pm.name as payment_method, ec.title as type,e.*,r.name as r_name,u.name as uname FROM expense e LEFT JOIN expensecat ec on ec.id=e.cat LEFT JOIN recipient r on r.id=e.recipient LEFT JOIN user u on u.id=e.user LEFT JOIN payment_method pm ON pm.id = e.mop WHERE e.active=0 AND e.date >= '".$start_date."' AND e.date <= '".$end_date."' and e.branch_id='".$branch_id."' order by date asc";
        $result = query($query,[],$conn);
        $total_amount = 0;
        ?>
        <table class="table table-bordered no-margin">
    		<thead>
    			<tr>
    				<th>Date</th>
    				<th>Type</th>
    				<th>Amount</th>
    				<th>Payment mode</th>
    				<th>Recipient</th>
    				<th>Paid by</th>
    			</tr>
    		<thead>
    		<tbody id="filterData">
        <?php
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
        ?>
            </tbody>
        </table>
    <?php }
?>