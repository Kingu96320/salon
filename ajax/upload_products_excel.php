<?php
include_once '../includes/db_include.php';
$branch_id = $_SESSION['branch_id'];
if ($_POST['module'] && $_POST['module'] == "product") {
	$file_array = explode(".", $_FILES["excelsheet"]["name"]);
	$return_arr = array();
	$empty_rows = 0;
	header('Content-Type: application/json');
	if($file_array[1] == "xlsx"){
		include_once '../includes/PHPExcel/IOFactory.php';
		$object = PHPExcel_IOFactory::load($_FILES["excelsheet"]["tmp_name"]);
		foreach($object->getWorksheetIterator() as $worksheet){  
            $highestRow = $worksheet->getHighestRow();
            if($highestRow > 1){  
	            for($row=2; $row<=$highestRow; $row++)
	            {
	            	$product_name = trim(ucfirst($worksheet->getCellByColumnAndRow(0, $row)->getValue()));  
					$sale_price =  trim(ucfirst($worksheet->getCellByColumnAndRow(1, $row)->getValue()));  
					$volume = trim($worksheet->getCellByColumnAndRow(2, $row)->getValue());  
					$unit = trim($worksheet->getCellByColumnAndRow(3, $row)->getValue());  
					$barcode = trim($worksheet->getCellByColumnAndRow(4, $row)->getValue());  
					$reward_point = preg_replace("/[^0-9]/", "",trim($worksheet->getCellByColumnAndRow(5, $row)->getValue()));

					if($product_name == '' || $sale_price == '' || $volume == '' || $unit == ''){
						$empty_rows += 1;
						$return_arr['empty_rows'] = $empty_rows;
						continue;
					}
			
					$unit_id = query_by_id("SELECT id from units where LOWER(name)= LOWER('$unit') and active = '0'",[],$conn);
					if($unit_id){
						$unit = $unit_id[0]['id'];
					} else {
						$unit = 0;
					}

					// $cat_res = query_by_id("SELECT id from servicecat where LOWER(cat)=LOWER(:cat) and active='0'",["cat"=>$category],$conn);

					// if($cat_res){
					// 	$category = $cat_res[0]['id'];
					// } else {
					// 	$category = get_insert_id("INSERT INTO servicecat set cat=:cat, active='0'",["cat"=>$category],$conn);
					// }

					$product_id = query_by_id("SELECT id from products where LOWER(name)=LOWER(:name) and LOWER(volume)=LOWER(:volume) and LOWER(unit)=LOWER(:unit) and active='0' ORDER BY id DESC LIMIT 1",["name"=>$product_name, "volume"=>$volume, "unit"=>$unit],$conn);
					if($product_id){
						$pid = $product_id[0]['id'];
						query("UPDATE `products` set `name`=:name,`price`=:price,`volume`=:volume,`unit`=:unit, `barcode`=:barcode, `reward`=:reward, `active`=:active where `id`=:pid",[
							'name'  => $product_name,
							'price' => $sale_price,
							'volume'    => $volume,
							'unit'   => $unit,
							'barcode'   => $barcode,
							'reward'   => $reward_point,
							'active'   => 0, 
							'pid'      => $pid],$conn);	
					}
					else {
						query("INSERT INTO `products` set `name`=:name,`price`=:price,`volume`=:volume,`unit`=:unit, `barcode`=:barcode, `reward`=:reward, `active`=:active, `branch_id`='0'",[
							'name'  => $product_name,
							'price' => $sale_price,
							'volume'    => $volume,
							'unit'   => $unit,
							'barcode'   => $barcode,
							'reward'   => $reward_point,
							'active'   => 0, 
							],$conn);
					}		
	            }
	            $return_arr['status'] = 1;
	            $return_arr['message'] = "success";
	        }
	        else{
	        	$return_arr['status'] = 0;
	            $return_arr['message'] = "Selected file is empty.";
	        }
            
            // $return_arr['row_in_sheet'] = $highestRow;
            
            $_SESSION['t']  = 1;
			$_SESSION['tmsg']  = "Products added Successfully";
        }
	} else {
		$return_arr['status'] = 0;
		$return_arr['message'] = "File Should be in .xlsx Format.";
	}

	echo json_encode($return_arr);
}

?>