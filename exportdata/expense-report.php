<?php
	include_once '../includes/db_include.php';
	$branch_id = $_SESSION['branch_id'];
	if(isset($_GET['sdate'])){
        $from = $_GET['sdate'];
        $sdate = strtotime($_GET['sdate']);
    } else {
		$from = date('Y-m-d');
		$sdate = strtotime(date('Y-m-d'));
    }
    if(isset($_GET['edate'])){
        $to = $_GET['edate'];
        $edate = strtotime($_GET['sdate']);
    } else {
    	$to = date('Y-m-d');
    	$edate = strtotime(date('Y-m-d'));
    }
	header("Content-Type: application/xls");    
    header("Content-Disposition: attachment; filename=ExpenseReport_".$sdate.'-'.$edate.".xls"); 
    header("Pragma: no-cache"); 
    header("Expires: 0");
?>
<table border="1">
	<thead>
		<tr>
			<th>Date</th>
			<th>Id</th>
			<th>Type of expense</th>
			<th>Amount</th>
			<th>Payment mode</th>
			<th>Recipient name</th>
			<th>Paid by</th>
			<th>Notes</th>
		</tr>
	</thead>
	<tbody>
    <?php
    	$sdate = strtotime(date('Y-m-d'));
        $edate = strtotime(date('Y-m-d'));
        if(isset($_GET['sdate'])){
            $sdate = $_GET['sdate'];
        } else {
        	$sdate = date('Y-m-d');
        }
        if(isset($_GET['edate'])){
            $edate = $_GET['edate'];
        } else {
        	$edate = date('Y-m-d');
        }

        if(isset($_GET['exp_type']) && $_GET['exp_type'] != ''){ 
        	$exp1 = " AND e.cat = '".$_GET['exp_type']."' ";
        	$exp2 = " AND 'Stock purchase' = '".$_GET['exp_type']."' ";
        	$exp3 = " AND 'Stock purchase (pending payment)'='".$_GET['exp_type']."' ";
       	} else { 
       		$exp1 = "";
        	$exp2 = "";
        	$exp3 = "";
       	}
        if(isset($_GET['p_type']) && $_GET['p_type'] != ''){ 
        	$pay1 = " AND e.mop='".$_GET['p_type']."' ";
        	$pay2 = " AND pay_method='".$_GET['p_type']."' ";
        	$pay3 = " AND p.mode='".$_GET['p_type']."' ";
        } else { 
        	$pay1 = "";
        	$pay2 = "";
        	$pay3 = "";
        }

        $sql = "SELECT e.id, e.cat, e.amount, e.descc as notes, e.mop as payment_method, e.recipient, e.user, 'Expense' as type, e.date as repdate FROM expense e WHERE e.active = '0' AND e.branch_id='".$branch_id."' AND e.date BETWEEN '".$sdate."' AND '".$edate."'".$exp1.$pay1
        	. " UNION SELECT id, 0 as cat, paid as amount, notes, pay_method as payment_method, vendor as recipient, 0 as user, 'Stock purchase' as type, dop as repdate FROM purchase WHERE active='0' AND branch_id='".$branch_id."' AND dop BETWEEN '".$sdate."' AND '".$edate."'".$exp2.$pay2
        	." UNION SELECT p.purchase_id as id, 0 as cat, p.paid as amount, p.notes, p.mode as payment_method, p.vendor as recipient, 0 as user, 'Stock purchase (pending payment)' as type, p.date as repdate FROM payments p WHERE p.active='0' AND p.branch_id='".$branch_id."' AND p.date BETWEEN '".$sdate."' AND '".$edate."' $exp3 $pay3 ORDER BY repdate DESC ";
        $data = query_by_id($sql,[],$conn);
        if($data){
        	$total_expense = 0;
        	foreach($data as $res){ ?>
        		<tr>
        			<td><?php echo my_date_format($res['repdate']); ?></td>
        			<td><?php echo $res['id'] ?></td>
        			<td><?php echo expense_type($res['cat'], $res['type']); ?></td>
        			<td><?php echo number_format($res['amount'], 2) ?></td>
        			<td><?php echo pay_method_name($res['payment_method']) ?></td>
        			<td><?php echo recipient_name($res['recipient'], $res['type']) ?></td>
        			<td><?php echo paid_by($res['user']) ?></td>
        			<td><?php echo $res['notes'] ?></td>
        		</tr>
        	<?php	
        		$total_expense += $res['amount'];
    		}
    		echo '<tr><td colspan="3" align="right"><b>Total</b></td><td><b>'.number_format($total_expense, 2).'</b></td><td colspan="4"></td></tr>';
        } else {
        	echo '<tr><td colspan="8" align="center"><b>No data found!</b></td></tr>';
        }
    ?>
</tbody>
</table>
<?php
	function expense_type($id, $stype){
		global $conn;
		if($id != 0){
			$type = query_by_id("SELECT title FROM expensecat WHERE id='".$id."' AND active='0'",[],$conn)[0]['title'];
			return $type;
		} else {
			return $stype;
		}
	}

	function recipient_name($id, $stype){
		global $conn;
		if($stype == 'Expense'){
			$name = query_by_id("SELECT name FROM recipient WHERE id='".$id."'",[],$conn)[0]['name'];
			return $name;
		} else {
			$vendor = query_by_id("SELECT name, cont FROM vendor WHERE id='".$id."'",[],$conn)[0];
			return $vendor['name'].' ('.$vendor['cont'].')';
		}
	}

	function paid_by($id){
		global $conn;
		if($id != 0){
			$name = query_by_id("SELECT name FROM user WHERE id='".$id."'",[],$conn)[0]['name'];
			return $name;
		} else {
			$name = '-';
			return $name;
		}
	}
?>