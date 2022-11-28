<?php
include_once "../includes/db_include.php";
$branch_id = $_SESSION['branch_id'];
if (!empty($_GET['term']))
{
		
		$term = isset($_GET['term'])?htmlspecialchars($_GET['term']):'';
		$sql="SELECT p.id,p.name as value,p.price,p.volume,u.id as unit,p.barcode,sum(pb.quantity) as total_qty FROM `products` p LEFT JOIN `units` u on u.id=p.unit left join product_billing pb on(pb.product_id=p.id)   WHERE p.name LIKE '$term%' and p.branch_id='".$branch_id."'";
		$return_arr = query($sql,[],$conn); 
		$quantity=query("select sum(quantity) as stockQty from purchase_items where product_id='".$return_arr[0]['id']."' and branch_id='".$branch_id."'",array(),$conn)[0]['stockQty'];
		
		if($return_arr)
		{
			$return_arr[0]['quantity']=$quantity;
			echo json_encode($return_arr);
		}
	
	
}
if (!empty($_GET['barcode']))
{	
		
		$barcode = isset($_GET['barcode'])?$_GET['barcode']:'0';
	    	$sql="SELECT p.id,p.name as value,p.price,p.volume,u.id as unit,p.barcode,sum(pb.quantity) as total_qty FROM `products` p LEFT JOIN `units` u on u.id=p.unit left join product_billing pb on(pb.product_id=p.id)   WHERE p.barcode='$barcode' and p.branch_id='".$branch_id."'";
		$return_arr = query($sql,[],$conn); 
		$quantity=query("select sum(quantity) as stockQty from purchase_items where product_id='".$return_arr[0]['id']."' and branch_id='".$branch_id."'",array(),$conn)[0]['stockQty'];
		
		if($return_arr)
		{
			if($return_arr[0]['total_qty']>$quantity){
				$return_arr['fail']=1;
			}	
			echo json_encode($return_arr);
		}
		
}
?>