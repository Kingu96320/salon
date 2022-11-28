<?php
    include_once '../includes/db_include.php';
    $sdate = strtotime(date('Y-m-d'));
    $edate = strtotime(date('Y-m-d'));
    if(isset($_GET['sdate'])){
        $sdate = strtotime($_GET['sdate']);
    }
    if(isset($_GET['edate'])){
        $edate = strtotime($_GET['edate']);
    }
    
    header("Content-Type: application/xls");    
    header("Content-Disposition: attachment; filename=MembershipReport_".$sdate.'-'.$edate.".xls"); 
    header("Pragma: no-cache"); 
    header("Expires: 0");

	$branch_id = $_SESSION['branch_id'];
?>
<table border="1">
    <thead>
        <tr>
            <th>Bill date</th>  
            <th>Invoice id</th> 
            <th>Client name</th>
            <th>Contact number</th>                             
            <th>Membership name</th>                                                
            <th>Amount</th>
            <th>Expiry date</th>    
            <th>Status</th>                                         
        </tr>
    </thead>
    <tbody>
    <?php
        $total_rows = 0;

        $ap_query = '';
        $mem_types = 0;

        if(isset($_GET['membership']) && $_GET['membership'] != ''){
            $ap_query .= ' AND mdh.md_id="'.$_GET['membership'].'" ';
        }

        if(isset($_GET['client']) && $_GET['client'] != ''){
            $ap_query .= ' AND mdh.client_id="'.$_GET['client'].'" ';
        }

        if(isset($_GET['mem_type']) && $_GET['mem_type'] != ''){
            $mem_types = $_GET['mem_type'];
        }

        if($ap_query != '' || $mem_types != 0){
            $data = query_by_id("SELECT mdh.*, i.doa, i.paid, md.membership_name as name, md.validity, md.membership_price FROM membership_discount_history mdh"
                    ." LEFT JOIN membership_discount md ON md.id = mdh.md_id"
                    ." LEFT JOIN invoice_$branch_id i ON i.id=mdh.invoice_id "
                    ." LEFT JOIN client c ON c.id = client_id"
                    ." WHERE 1=1 $ap_query ORDER BY mdh.invoice_id DESC",[],$conn);
            if($data){                                                  
                foreach($data as $memdata){
                    $package_expiry_date = my_date_format(date('Y-m-d', strtotime(date('Y-m-d',strtotime($memdata['doa'])). ' + '.$memdata['validity'].' days')));
                    if($mem_types == 2){
                        if(strtotime($package_expiry_date) > strtotime(date('Y-m-d'))){
                            continue;
                        }
                    }
                    ?>
                    <tr>
                        <td><?php echo my_date_format($memdata['time_update']); ?></td>
                        <td><?php echo $memdata['invoice_id'] ?></td>
                        <td><?php echo query_by_id("SELECT name FROM client WHERE id='".$memdata['client_id']."'",[],$conn)[0]['name']; ?></td>
                        <td><?php echo query_by_id("SELECT cont FROM client WHERE id='".$memdata['client_id']."'",[],$conn)[0]['cont']; ?></td>
                        <td><?php echo $memdata['name'] ?></td>
                        <td><?php echo $memdata['membership_price'] ?></td>
                        <td><?php                                                       
                            if(strtotime($package_expiry_date) > strtotime(date('Y-m-d'))){
                                echo $package_expiry_date;
                            }
                         ?></td>
                        <td>
                            <?php
                                if(strtotime($package_expiry_date) > strtotime(date('Y-m-d'))){
                                    echo "<lable class='badge badge-success'>Active</label>";
                                } else {
                                    echo "<lable class='badge badge-danger'>Expired</label>";
                                }
                            ?>
                        </td>
                    </tr>
                    <?php
                    $total_rows++;
                }
            }
        }
        else{
            for($i=$sdate; $i<=$edate; $i+=86400){
                $date = date("Y-m-d", $i);
                $data = query_by_id("SELECT mdh.*, i.doa, i.paid, md.membership_name as name, md.validity, md.membership_price FROM membership_discount_history mdh"
                    ." LEFT JOIN membership_discount md ON md.id = mdh.md_id"
                    ." LEFT JOIN invoice_$branch_id i ON i.id=mdh.invoice_id "
                    ." LEFT JOIN client c ON c.id = client_id"
                    ." WHERE date(mdh.time_update)='".$date."' ORDER BY mdh.invoice_id DESC",[],$conn);
                if($data){                                                  
                    foreach($data as $memdata){
                        ?>
                        <tr>
                            <td><?php echo my_date_format($memdata['time_update']); ?></td>
                            <td><?php echo $memdata['invoice_id'] ?></td>
                            <td><?php echo query_by_id("SELECT name FROM client WHERE id='".$memdata['client_id']."'",[],$conn)[0]['name']; ?></td>
                            <td><?php echo query_by_id("SELECT cont FROM client WHERE id='".$memdata['client_id']."'",[],$conn)[0]['cont']; ?></td>
                            <td><?php echo $memdata['name'] ?></td>
                            <td><?php echo $memdata['membership_price'] ?></td>
                            <td><?php 
                                $package_expiry_date = my_date_format(date('Y-m-d', strtotime(date('Y-m-d',strtotime($memdata['doa'])). ' + '.$memdata['validity'].' days')));
                                if(strtotime($package_expiry_date) > strtotime(date('Y-m-d'))){
                                    echo $package_expiry_date;
                                }
                             ?></td>
                            <td>
                                <?php
                                    if(strtotime($package_expiry_date) > strtotime(date('Y-m-d'))){
                                        echo "<lable class='badge badge-success'>Active</label>";
                                    } else {
                                        echo "<lable class='badge badge-danger'>Expired</label>";
                                    }
                                ?>
                            </td>
                        </tr>
                        <?php
                        $total_rows++;
                    }
                }
            }
        }
        if($total_rows == 0){
            echo "<tr>";
                echo "<td colspan='8' class='text-center'>No record found!!</td>";
            echo "<tr>";
        }                                   
    ?>
    </tbody>
</table>
<?php 
    include "footer.php";
?>