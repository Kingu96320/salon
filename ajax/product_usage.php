<?php
include_once '../includes/db_include.php';
$branch_id = $_SESSION['branch_id'];
// function to add product usage
if(isset($_POST['action']) && $_POST['action'] == 'product_usage'){
    $data = $_POST['data'];
    $i = 0;
    query("DELETE FROM product_usage WHERE invoice_id='".$data[0][3]['invId']."' and branch_id='".$branch_id."'",[],$conn);
    for($i = 0; $i < count($data); $i++){
        $sname              = htmlspecialchars($data[$i][0]['sname']);
        $ser_name           = htmlspecialchars($data[$i][1]['serName']);
        $sid                = $data[$i][2]['serId'];
        $inv_id             = $data[$i][3]['invId'];
        $sp_id              = $data[$i][4]['spId'];
        $siid               = $data[$i][5]['siid'];
        $stock_id           = $data[$i][6]['stock_id'];
        $pname              = $data[$i][7]['pname'];
        $pid                = $data[$i][8]['pid'];
        $product_name       = $data[$i][9]['product_name'];
        $qty                = $data[$i][10]['qty'];
        $unit               = $data[$i][11]['unit'];
        $service_provider   = $data[$i][12]['service_provider'];
        
        $product_assigned_id = query_by_id("SELECT id from `productused` where  SUBSTRING_INDEX(product,',',-1)=:product_id AND active='0' and branch_id='".$_SESSION['branch_id']."' and product_stock_batch='".$stock_id."'",["product_id"=>$pid],$conn);
        if($product_assigned_id){
            $total_qty = getstockserviceprovider($pid,0,$stock_id, 'product_to_use');
            if($qty > $total_qty){
                $remaining_qty = $qty-$total_qty;
                query("INSERT INTO product_usage SET invoice_id = '".$inv_id."', ii_id='".$siid."', service_name='".$ser_name."', service_id='".$sid."', product_name='".$product_name."', product_id='".$pid."', quantity='".$remaining_qty."', unit='".$unit."', service_provider='".$service_provider."', stock_id='".$stock_id."', status = '1', branch_id='".$branch_id."', type='1'",[],$conn);
                query("INSERT INTO product_usage SET invoice_id = '".$inv_id."', ii_id='".$siid."', service_name='".$ser_name."', service_id='".$sid."', product_name='".$product_name."', product_id='".$pid."', quantity='".$total_qty."', unit='".$unit."', service_provider='".$service_provider."', stock_id='".$stock_id."', status = '1', branch_id='".$branch_id."', type='2'",[],$conn);
            } else {
                query("INSERT INTO product_usage SET invoice_id = '".$inv_id."', ii_id='".$siid."', service_name='".$ser_name."', service_id='".$sid."', product_name='".$product_name."', product_id='".$pid."', quantity='".$qty."', unit='".$unit."', service_provider='".$service_provider."', stock_id='".$stock_id."', status = '1', branch_id='".$branch_id."', type='2'",[],$conn);
            }
        } else {    
            if($pid != '' && $qty > 0){
                query("INSERT INTO product_usage SET invoice_id = '".$inv_id."', ii_id='".$siid."', service_name='".$ser_name."', service_id='".$sid."', product_name='".$product_name."', product_id='".$pid."', quantity='".$qty."', unit='".$unit."', service_provider='".$service_provider."', stock_id='".$stock_id."', status = '1', branch_id='".$branch_id."', type='1'",[],$conn);
            }
        }
    }
    echo "1";
}
?>