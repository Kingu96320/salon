<?php
include "../includes/db_include.php";
$branch_id = $_SESSION['branch_id'];
$date = date('Y-m-d');
if (!empty($_GET['term']) && !isset($_GET['action']))
{
    $term = htmlspecialchars($_GET['term']);
    $provider = htmlspecialchars($_GET['provider']);
    $sql = "select ps.product_id as id, concat(concat(ps.product,' - ',ps.sale_price),' - ',DATE_FORMAT(ps.exp_date,'%d-%M-%Y')) as value,ps.sale_price as price,'0' as duration,p.reward as points, ps.exp_date, ps.id as stock_id, p.unit, ps.product as product_name, pu.id as puid from purchase_items ps LEFT JOIN products p ON p.id = ps.product_id LEFT JOIN productused pu ON p.id = pu.product WHERE ps.product LIKE '%$term%' and ps.exp_date >= '".$date."' and p.active='0' group by ps.id";
    $return_arr = query($sql,[],$conn); 
    if($return_arr)
	{   
        $arrdata = array();
        foreach($return_arr as $data){
            if($data['puid'] > 0){
                $stock1 = getstockserviceprovider($data['id'],0,$data['stock_id'], 'product_to_use');
                $stock2 = getstock_wo_pro($data['id'],0,$data['stock_id'], 'product_to_use');
                $stock = $stock1+$stock2;
            } else {
                $stock = getstock($data['id'],0,$data['stock_id'], 'product_to_use');
            }
            
            if($stock <= 0){
                continue;
            }
            $arr['duration'] = $data['duration'];
            $arr['exp_date'] = $data['exp_date'];
            $arr['id'] = $data['id'];
            $arr['points'] = $data['points'];
            $arr['price'] = $data['price'];
            $arr['product_name'] = $data['product_name'];
            $arr['stock_id'] = $data['stock_id'];
            $arr['unit'] = $data['unit'];
            $arr['value'] = $data['value'];
            array_push($arrdata, $arr);
        }
        echo json_encode($arrdata);
    }
}

if(isset($_GET['action']) && $_GET['action'] == 'stock_product'){
    $term = htmlspecialchars($_GET['term']);
    $sql="SELECT p.id, CONCAT(p.name,' (',p.volume,' ',u.name,')') as label, p.price,p.volume, p.name as value, u.id as unit FROM `products` p LEFT JOIN `units` u on u.id=p.unit  WHERE p.name LIKE '%$term%' and p.active=0 ";
    $return_arr = query($sql,[],$conn); 
    if($return_arr)
	{
        echo json_encode($return_arr);
    }
}

?>