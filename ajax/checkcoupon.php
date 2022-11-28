<?php
	include_once '../includes/db_include.php';
	$branch_id = $_SESSION['branch_id'];
	if(!empty($_POST["coup"])) {
		$num = $_POST["coup"];
		$sql2="SELECT * from coupons where replace(ccode,' ','')='$num' and branch_id='".$branch_id."'";
		$result2=query_by_id($sql2,[],$conn);
		if($result2){
			echo "1";
		}
	}elseif(!empty($_GET['term'])){
		$arr    =array();
		$name	= $_GET["term"];
		$val 	= str_replace("%20", " ", $name);
		$sql2 	= "SELECT ccode as value,id,discount,discount_type,max_amount,min_amount,valid,c_per_user from `coupons` where ccode LIKE '%$val%' and branch_id='".$branch_id."'";
		$result2= query_by_id($sql2,[],$conn);
		foreach($result2 as $row1){
				$return_arr[] =  $row1;
			}
			echo json_encode($return_arr);
	}elseif(isset($_POST['cid'])){
	
			$cid = $_POST["cid"];
				$sql2="SELECT count(i.coupon) as used_coupon,c.c_per_user from coupons c  LEFT JOIN `invoice_".$branch_id."` i on i.coupon=c.id where i.client='$cid' and c.branch_id='".$branch_id."'";
				$result2=query_by_id($sql2,[],$conn);
				foreach($result2 as $row){
					if($row['c_per_user'] === $row['used_coupon']){
						echo 'allused';
					}  
					 
				}
	
	}
?>
