<?php
	include_once '../includes/db_include.php';
	if(isset($_GET['sdate'])){
        $sdate = strtotime($_GET['sdate']);
    }
    if(isset($_GET['edate'])){
        $edate = strtotime($_GET['edate']);
    }

    header("Content-Type: application/xls");    
    header("Content-Disposition: attachment; filename=ProductUsageReport_".$sdate.'-'.$edate.".xls"); 
    header("Pragma: no-cache"); 
    header("Expires: 0");

    $branch_id = $_SESSION['branch_id'];

    $append_query = '';
    if(isset($_GET['client'])){
        $append_query .= " AND i.client='".$_GET['client']."' " ;
    }
    if(isset($_GET['product'])){
        $append_query .= " AND pu.product_id='".$_GET['product']."' " ;
    }
    if(isset($_GET['sp'])){
        $append_query .= " AND pu.service_provider='".$_GET['sp']."' " ;
    }
    if(isset($_GET['from'])){
        $append_query .= " AND pu.type='".$_GET['from']."' " ;
    }
?>
<table border="1">
    <thead>
        <tr>
            <th>Bill date</th>  
            <th>Invoice id</th> 
            <th>Client name</th>
            <th>Service name</th>                                   
            <th>Product name</th>                                               
            <th>Quantity</th>
            <th>Unit</th>
            <th>Stock id</th>
            <th>Service provider</th>
            <th>Used From</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $total_rows = 0;
        for($i=$sdate; $i<=$edate; $i+=86400){
            $date = date("Y-m-d", $i);
            $data = query_by_id("SELECT pu.*, i.doa as bill_date, i.client as client_id FROM product_usage pu LEFT JOIN invoice_".$branch_id." i ON i.id = pu.invoice_id WHERE pu.status='1' AND pu.branch_id='".$branch_id."' and i.doa='".$date."' $append_query GROUP BY pu.invoice_id ORDER BY i.id DESC",[],$conn);
            if($data){
                $total_rows += count($data);
                foreach($data as $pu){
                    $count = 1;
                    $pd = query_by_id("SELECT pu.*, i.doa as bill_date, i.client as client_id FROM product_usage pu LEFT JOIN invoice_".$branch_id." i ON i.id = pu.invoice_id WHERE pu.invoice_id='".$pu['invoice_id']."' $append_query ",[],$conn);
                    if($pd){
                        $rowspan = count($pd);
                        foreach($pd as $pr){
                            ?>
                            <tr>
                                <?php if($count == 1){ ?>
                                <td rowspan="<?php echo $rowspan ?>"><?php echo my_date_format($pr['bill_date']) ?></td>
                                <td class="text-center" rowspan="<?php echo $rowspan ?>"><?php echo $pr['invoice_id'] ?></td>
                                <td rowspan="<?php echo $rowspan ?>"><?php echo get_client_name($pr['client_id'],$conn,'withcontact') ?></td>
                                <?php } ?>
                                <td><?php echo $pr['service_name'] ?></td>
                                <td><?php echo $pr['product_name'] ?></td>
                                <td><?php echo $pr['quantity'] ?></td>
                                <td><?php echo query_by_id("SELECT name FROM units WHERE id='".$pr['unit']."'",[],$conn)[0]['name']; ?></td>
                                <td><?php echo $pr['stock_id'] ?></td>
                                <td><?php echo query_by_id("SELECT CONCAT(name, ' ', ' (', cont,') ') as name FROM beauticians WHERE id='".$pr['service_provider']."'",[],$conn)[0]['name'] ?></td>
                                <td>
                                    <?php 
                                        if($pr['type'] == '1'){
                                            echo "Product stock";
                                        } else if($pr['type'] == '2'){
                                            echo "Assigned stock";
                                        }
                                    ?>
                                </td>
                            </tr>
                    <?php
                    $count++;
                        }
                    }                   
                }
            }
        }
        if($total_rows == 0){
            echo "<tr>";
                echo "<td colspan='10' class='text-center'>No record found!!</td>";
            echo "<tr>";
        }                                   
        ?>
    </tbody>
</table>