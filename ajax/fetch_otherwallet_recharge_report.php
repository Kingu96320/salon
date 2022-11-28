<?php
	include_once '../includes/db_include.php'; 
    ?>
    <table class="table table-striped table-bordered">
        <tr>
            <th>#</th>
            <th>Name</th>
            <th>Contact number</th>
            <th>Wallet amount</th>
            <th>Payment mode</th>
            <th>Date</th>
        </tr>
        <?php
            $custs = query("SELECT c.id, c.name, c.cont, w.wallet_amount, pm.name as pay, date(time_update) as date FROM client c left join wallet_history w on w.client_id = c.id inner join payment_method pm on pm.id = w.payment_method where c.active='0' and c.branch_id='".$_SESSION['branch_id']."' and w.transaction_type='1' and date(time_update) >= '".$_GET['from']."' and date(time_update) <= '".$_GET['to']."' ", [], $conn);
            if($custs){
                $i = 0;
                foreach($custs as $ct){
                    $i += 1;
                    $wallet_amt = $ct['wallet_amount'];
                    if($wallet_amt == "" || $wallet_amt == NULL){
                        $wallet_amt = 0;
                    }
                echo "<tr><td>".$i."</td><td>".$ct['name']."</td><td>".$ct['cont']."</td><td>".$wallet_amt."</td><td>".$ct['pay']."</td><td>".date('F d, Y', strtotime($ct['date']))."</td></tr>";
                }
            }else{
                echo "<tr><td colspan='6' class='text-center'>No record found!!</td></tr>";
            }
        ?>
    </table>
