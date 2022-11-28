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
    header("Content-Disposition: attachment; filename=ProductPurchaseReport_".$sdate.'-'.$edate.".xls"); 
    header("Pragma: no-cache"); 
    header("Expires: 0");

    $branch_id = $_SESSION['branch_id'];

    if(isset($_GET['vendor']) && $_GET['vendor'] > 0){
        $vid = ' AND vendor="'.$_GET['vendor'].'" ';
    } else {
        $vid = '';
    }
?>
<table border="1">
    <thead>
        <tr>
            <th class="text-center">Purchase date</th>
            <th class="text-center">Invoice</th>
            <th>Vendor name</th>
            <th>Product name</th>                                               
            <th>Unit</th>                                               
            <th class="text-center">Price</th>
            <th class="text-center">Quantity</th>
            <th class="text-center">Amount</th>
            <th class="text-center">Discount</th>
            <th>Tax</th>
            <th class="text-center">Net amount</th>
            <th class="text-center">Shipping charges</th>                                          
            <th class="text-center">Total price</th>
            <th class="text-center">Paid</th>
            <th class="text-center">Due</th>
            <th>Payment method</th>
        </tr>
    </thead>
    <tbody>
    <?php
        for($i=$sdate; $i<=$edate; $i+=86400){
            $date = date("Y-m-d", $i);
            $data = query_by_id("SELECT id, inv, vendor, dis, pay_method, tax, taxtype, total, subtotal, paid, due, notes, dop, ship FROM purchase WHERE dop='".$date."' AND branch_id='".$branch_id."' $vid ",[],$conn);
            if($data){                                          
                foreach ($data as $data) {
                    $products = query_by_id("SELECT product, product_id, quantity, volume, unit, mrp, price, sale_price FROM purchase_items WHERE iid='".$data['id']."'",[],$conn);
                    if($products){
                        $rowspan = count($products);
                        $count = 1;
                        foreach ($products as $list) {                                      
                            echo '<tr>';
                                if($count == 1){
                                    echo '<td align="center" style="vertical-align:middle;" rowspan="'.$rowspan.'">'.my_date_format($data['dop']).'</td>';
                                    echo '<td align="center" style="vertical-align:middle;"  rowspan="'.$rowspan.'">'.$data['id'].'</td>';
                                    echo '<td style="vertical-align:middle;"  rowspan="'.$rowspan.'">'.vendor_name($data['vendor']).'</td>';
                                }
                                echo '<td>'.product_name($list['product_id']).'</td>';
                                echo '<td>'.$list['volume']." ".unit_name($list['unit']).'</td>';
                                echo '<td align="center">'.number_format($list['price'],2).'</td>';  
                                echo '<td align="center">'.$list['quantity'].'</td>';
                                echo '<td align="center">'.number_format(($list['price']*$list['quantity']),2).'</td>';     
                                if($count == 1){ ?>                                   
                                    <?php
                                    echo '<td style="vertical-align:middle;"  rowspan="'.$rowspan.'" align="center">'.number_format($data['subtotal']-get_discount_invoice($data['id'], $date),2).'</td>';
                                    ?>
                                    <td style="vertical-align:middle;" rowspan="<?= $rowspan ?>"><?php echo taxcalculation($date,'',$data['id']) ?></td>
                                    <?php
                                    echo '<td style="vertical-align:middle;"  rowspan="'.$rowspan.'" align="center">'.number_format(get_discount_invoice($data['id'], $date),2).'</td>';    
                                    echo '<td style="vertical-align:middle;"  rowspan="'.$rowspan.'" align="center">'.number_format($data['ship'],2).'</td>';       
                                    echo '<td align="center" style="vertical-align:middle;"  rowspan="'.$rowspan.'">'.number_format($data['total'], 2).'</td>';
                                    echo '<td align="center" style="vertical-align:middle;"  rowspan="'.$rowspan.'">'.number_format($data['paid'], 2).'</td>';
                                    echo '<td align="center" style="vertical-align:middle;"  rowspan="'.$rowspan.'">'.number_format($data['due'], 2).'</td>';
                                    echo '<td style="vertical-align:middle;"  rowspan="'.$rowspan.'">'.pay_method_name($data['pay_method']).'</td>';
                                }

                            echo '</tr>';
                            $count++;
                        }
                    }
                }
            }
        }
    ?>
    </tbody>
</table>
<?php

    function vendor_name($id){
        global $conn;
        $name = query_by_id("SELECT name FROM vendor WHERE id='".$id."'",[],$conn)[0]['name'];
        return $name;
    }

    function product_name($id){
        global $conn;
        $product_name = query_by_id("SELECT name FROM products WHERE id='".$id."'",[],$conn)[0]['name'];
        return $product_name;
    }

    function unit_name($id){
        global $conn;
        $unit_name = query_by_id("SELECT name FROM units WHERE id='".$id."'",[],$conn)[0]['name'];
        return $unit_name;
    }

    function taxcalculation($date, $is_exce = null, $invid){
        global $branch_id;
        global $conn;
        if($is_exce == 'exclusive'){
            $exl_tax = 0;
            $check_tax = query_by_id("SELECT id, tax, taxtype, dis, subtotal  FROM purchase WHERE dop='".$date."' AND id='".$invid."' AND branch_id='".$branch_id."'",[],$conn);
            foreach($check_tax as $tax){
               $gst_amount = get_discount_invoice($tax['id'], $date);
               if($tax['taxtype'] == '1'){
                   $tx = query_by_id("SELECT t.tax FROM tax t WHERE id='".$tax['tax']."' and active='0'",[],$conn)[0];
                   $exl_tax += ($gst_amount*$tx['tax']/100);
               }
            }
            return $exl_tax;
        } else if($is_exce == 'inclusive'){
            $inc_tax = 0;
            $check_tax = query_by_id("SELECT id, tax, taxtype, dis, subtotal  FROM purchase WHERE dop='".$date."' AND id='".$invid."' AND branch_id='".$branch_id."'",[],$conn);
            foreach($check_tax as $tax){
               $gst_amount = get_discount_invoice($tax['id'], $date);
               if($tax['taxtype'] == '0'){
                   $tx = query_by_id("SELECT t.tax FROM tax t WHERE id='".$tax['tax']."' and active='0'",[],$conn)[0];
                   $inc_tax += ($gst_amount-($gst_amount/($tx['tax']+100))*100);
               }
            }
            return $inc_tax;
        } else {
            $inc_tax = 0;
            $exl_tax = 0;
            $check_tax = query_by_id("SELECT id, tax, taxtype, dis, subtotal  FROM purchase WHERE dop='".$date."' AND id='".$invid."' AND branch_id='".$branch_id."'",[],$conn);
            foreach($check_tax as $tax){
               $gst_amount = get_discount_invoice($tax['id'], $date);
               if($tax['taxtype'] == '0'){
                   $tx = query_by_id("SELECT t.tax FROM tax t WHERE id='".$tax['tax']."' and active='0'",[],$conn)[0];
                   $inc_tax += ($gst_amount-($gst_amount/($tx['tax']+100))*100);
               } else if($tax['taxtype'] == '1'){
                   $tx = query_by_id("SELECT t.tax FROM tax t WHERE id='".$tax['tax']."' and active='0'",[],$conn)[0];
                   $exl_tax += ($gst_amount*$tx['tax']/100);
               }
            }
            
            echo '<strong>Inclusive : </strong>'.number_format($inc_tax,2)."<br />";
            echo '<strong>Exclusive : </strong>'.number_format($exl_tax,2);
        }
    }

    function get_discount_invoice($id, $date){
        global $branch_id;
        $total = 0;
        $sql="SELECT paid, dis, subtotal, total, id from purchase where dop='$date' and active=0 and branch_id='".$branch_id."' and id='".$id."'";
        global $conn;
        $result=query_by_id($sql,[],$conn);
        if($result){
            foreach($result as $res){
                $discount = explode(",",$res['dis']);
                if($discount['0'] == CURRENCY){
                    $total = $res['subtotal'] - $discount['1'];
                } else if($discount['0'] == 'pr'){
                    if($discount['1'] != 0){
                        $dis_price = ($res['subtotal']*$discount['1'])/100;
                        $total = $res['subtotal']-$dis_price;
                    } else {
                        $total = $res['subtotal'];
                    }
                }
            }
        }
        return $total;
    }
?>