<?php
	include_once '../includes/db_include.php';

	if(isset($_GET['sdate'])){
        $sdate = strtotime($_GET['sdate']);
    }
    if(isset($_GET['edate'])){
        $edate = strtotime($_GET['edate']);
    }

    header("Content-Type: application/xls");    
    header("Content-Disposition: attachment; filename=UpsellReport".$sdate.'-'.$edate.".xls"); 
    header("Pragma: no-cache"); 
    header("Expires: 0");

    $branch_id = $_SESSION['branch_id'];

    $append_query = '';
    if(isset($_GET['client'])){
        $append_query .= " AND i.client='".$_GET['client']."' " ;
    }
    
    if(isset($_GET['sp'])){
        $append_query .= " AND imsp.service_provider='".$_GET['sp']."' " ;
    }
?>
<table border="1">
    <thead>
        <tr>
            <th style="white-space: nowrap">Date</th>
            <th>Invoice id</th>
            <th>Client name</th>
            <th>Contact number</th>
            <th>Service name</th>
            <th>Service provider</th>
            <th>Amount</th>
        </tr>
    </thead>
    <tbody>
        <?php
            $total_price = 0;
            for($i=$sdate; $i<=$edate; $i+=86400){
                $date = date("Y-m-d", $i);
                $app = query_by_id("SELECT appointment_id FROM invoice_".$branch_id." WHERE doa='".$date."' and active = '0' and appointment_id > '0'",[],$conn);
                if($app){
                    foreach($app as $ap){
                        $app_service_list = array();
                        $app_services = query_by_id("SELECT ai.id, ai.client, ai.doa, ai.total, ai.subtotal, aii.service, aii.price as item_price FROM app_invoice_".$branch_id." ai LEFT JOIN app_invoice_items_".$branch_id." aii ON aii.iid = ai.id WHERE ai.id = '".$ap['appointment_id']."'",[],$conn);
                        if($app_services){
                            foreach($app_services as $app_ser){
                                array_push($app_service_list, str_replace(',', '-', $app_ser['service']));
                            }
                        }

                        $inv = query_by_id("SELECT i.id, c.name, c.cont, i.doa, i.appointment_id, ii.service, ii.price as item_price, imsp.service_provider, GROUP_CONCAT(b.name,' ') as spname FROM invoice_".$branch_id." i LEFT JOIN invoice_items_".$branch_id." ii ON ii.iid = i.id LEFT JOIN invoice_multi_service_provider imsp ON imsp.ii_id = ii.id LEFT JOIN client c ON c.id = i.client LEFT JOIN `beauticians` b on b.id=imsp.service_provider WHERE REPLACE(ii.service,',','-') NOT IN ('".implode(',',$app_service_list)."') AND i.appointment_id = '".$ap['appointment_id']."' $append_query  GROUP BY ii_id ",[],$conn);
                        $rows = count($inv);
                        $count = 1;
                        $sp_id = 0;
                        if($inv){
                            foreach($inv as $in){
                                ?>
                                <tr>
                                    <?php
                                        if($count == 1){ ?>
                                            <td style="vertical-align: middle;" rowspan="<?= $rows ?>"><?php echo my_date_format($in['doa']) ?></td>
                                            <td style="vertical-align: middle;" rowspan="<?= $rows ?>"><?php echo $in['id'] ?></td>
                                            <td style="vertical-align: middle;" rowspan="<?= $rows ?>"><?php echo $in['name'] ?></td>
                                            <td style="vertical-align: middle;" rowspan="<?= $rows ?>"><?php echo $in['cont'] ?></td>
                                        <?php }
                                    ?>                                                          
                                    <td style="vertical-align: middle;"><?php echo getservice($in['service']) ?></td>
                                    <td>
                                        <?php
                                            $sprovider = explode(',',$in['spname']);
                                            $slast = end($sprovider);
                                            foreach($sprovider as $sp){
                                                if(count($sprovider) > 1 && $sp != $slast){
                                                    $cat = ',';
                                                } else { $cat = ''; }
                                                echo ucfirst(strtolower($sp)).$cat."<br />";
                                            }
                                        ?>
                                    </td>
                                    <td><?php echo number_format($in['item_price'],2); $total_price += $in['item_price']; ?></td>
                                </tr>
                                <?php
                                $count++;
                            }
                        }
                    }
                }
            }
        ?>
        <tr>
            <td colspan="5"></td>
            <td class="text-right"><strong>Total</strong></td>
            <td ><strong><?= number_format($total_price,2) ?></strong></td>
        </tr>
    </tbody>
</table>
<?php
    function getservice($get_id){
        global $conn;
        global $branch_id;
        $id = EXPLODE(",",$get_id)[1];
        if(EXPLODE(",",$get_id)[0] == 'sr'){
            $sql ="SELECT CONCAT('(Service)',' ',name) as name FROM `service` where active='0' and id='$id'";   
        }else if(EXPLODE(",",$get_id)[0] == 'pr'){
            $sql ="SELECT CONCAT('(Product)',' ',name) as name FROM `products` where active='0' and id='$id'";  
        }else if(EXPLODE(",",$get_id)[0] == 'pa'){
            $sql ="SELECT CONCAT('(Package)',' ',name) as name FROM `packages` where active='0' and id='$id' and branch_id='".$branch_id."'";   
        }else if(EXPLODE(",",$get_id)[0] == 'prepaid'){
            $sql ="SELECT CONCAT('(Prepaid)',' ',pack_name) as name FROM `prepaid` where status='1' and id='$id' and branch_id='".$branch_id."'";   
        }else if(EXPLODE(",",$get_id)[0] == 'mem'){
            $sql ="SELECT CONCAT('(Membership)',' ',membership_name) as name FROM `membership_discount` where status='1' and id='$id'";
        }
        $result=query_by_id($sql,[],$conn);
        foreach($result as $row) {
            return $row['name'];
        }
    }
?>