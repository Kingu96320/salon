<?php
	include_once '../includes/db_include.php';
	$branch_id = $_SESSION['branch_id'];
	if (isset($_POST['pid'])){
		$pid = explode(",",$_POST['pid'])[1];
		$used_stock = $_POST['used_stock'];
		$invoice_id = $_POST['invoice_id'];
		$stock_id = $_POST['stock_id'];
		$return_arr = array();
			$sql = "Select concat('pr,',id) as id,name as value,price,'0' as duration from products WHERE id = '$pid'";
			$result = query_by_id($sql,[],$conn);
			foreach($result as $row) 
			{
				$remaining_stock = ((explode(",",$row['id'])[0]) == 'pr')?(getstock(explode(",",$row['id'])[1],$invoice_id,$stock_id, $type ='buy_product')) - ($used_stock):'0';
				$row['actual_stock'] = ($remaining_stock>0)?$remaining_stock:$remaining_stock;
				$return_arr[] =  $row;
			}
		echo json_encode($return_arr);
	}
	
	
	if(isset($_POST['action']) && $_POST['action']=="stock_for_use"){
	    $product_id = $_POST['product_id'];
	    $stock_id = $_POST['stock_id'];
	    $stock1 = getstockserviceprovider($product_id,0,$stock_id, 'product_to_use');
        $stock2 = getstock_wo_pro($product_id,0,$stock_id, 'product_to_use');
	    $pending_stock = $stock1+$stock2;	
	    $pending_stock_show = $stock1+$stock2;
	    $result = array();
	    $result['pending_stock'] = $pending_stock;
	    $result['pending_stock_show'] = $pending_stock_show;
	    echo json_encode($result);
	}
	
?>