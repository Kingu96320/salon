<?php
include_once '../includes/db_include.php';
$branch_id = $_SESSION['branch_id'];

// services-sale-report.php file functions START

if(isset($_POST['action']) && $_POST['action'] == 'getlist'){
	$type = $_POST['type'];
	$html = '';
	if($type == 'Service'){
		$service_list = query_by_id("SELECT id, name FROM service ORDER BY name ASC",[],$conn);
		if($service_list){
			$html .= '<option value="">--Select--</option>';
			foreach($service_list as $list){
				$html .= '<option value="sr,'.$list['id'].'">'.$list['name'].'</option>';
			}
		}
		echo $html;
		die();
	} else if($type == 'Product'){
		$product_list = query_by_id("SELECT id, name FROM products ORDER BY name ASC",[],$conn);
		if($product_list){
			$html .= '<option value="">--Select--</option>';
			foreach($product_list as $list){
				$html .= '<option value="pr,'.$list['id'].'">'.$list['name'].'</option>';
			}
		}
		echo $html;
		die();
	} else if($type == 'Package'){
		$package_list = query_by_id("SELECT id, name FROM packages WHERE branch_id='".$branch_id."'",[],$conn);
		if($package_list){
			$html .= '<option value="">--Select--</option>';
			foreach($package_list as $list){
				$html .= '<option value="pa,'.$list['id'].'">'.$list['name'].'</option>';
			}
		}
		echo $html;
		die();
	} else if($type == 'mem'){
		$membership_list = query_by_id("SELECT id, membership_name FROM membership_discount ORDER BY membership_name ASC",[],$conn);
		if($membership_list){
			$html .= '<option value="">--Select--</option>';
			foreach($membership_list as $list){
				$html .= '<option value="mem,'.$list['id'].'">'.$list['membership_name'].'</option>';
			}
		}
		echo $html;
		die();
	}
}

// services-sale-report.php file functions END

?>