<?php
include_once '../includes/db_include.php';
$branch_id = $_SESSION['branch_id'];
if ($_POST['module'] && $_POST['module'] == "service") {
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
	            	$service_name = trim(ucfirst($worksheet->getCellByColumnAndRow(0, $row)->getValue()));  
					$category = trim(ucfirst($worksheet->getCellByColumnAndRow(1, $row)->getValue()));  
					$duration = preg_replace("/[^0-9]/", "",trim($worksheet->getCellByColumnAndRow(2, $row)->getValue()));  
					$price = preg_replace("/[^0-9]/", "",trim($worksheet->getCellByColumnAndRow(3, $row)->getValue()));  
					$price = round($price,2);
					$reward_point = preg_replace("/[^0-9]/", "",trim($worksheet->getCellByColumnAndRow(4, $row)->getValue()));

					if($service_name == '' || $category == '' || $duration == '' || $price == ''){
						$empty_rows += 1;
						$return_arr['empty_rows'] = $empty_rows;
						continue;
					}
					$cat_res = query_by_id("SELECT id from servicecat where LOWER(cat)=LOWER(:cat) and active='0'",["cat"=>$category],$conn);
					if($cat_res){
						$category = $cat_res[0]['id'];
					} else {
						$category = get_insert_id("INSERT INTO servicecat set cat=:cat, active='0', branch_id='0'",["cat"=>$category],$conn);
					}

					$service_id = query_by_id("SELECT id from service where LOWER(name)=LOWER(:name) and active='0' ORDER BY id DESC LIMIT 1",["name"=>$service_name],$conn);
					if($service_id){
						$eid = $service_id[0]['id'];
						query("UPDATE `service` set `cat`=:cat,`duration`=:duration,`price`=:price,`points`=:points,`active`=:active where `id`=:eid and branch_id='0'",[
							'cat'  => $category,
							'duration' => $duration,
							'price'    => $price,
							'points'   => $reward_point,
							'active'   => 0, 
							'eid'      => $eid],$conn);	
					}
					else {
						query("INSERT INTO `service` set `name`=:name,`cat`=:cat,`duration`=:duration,`price`=:price,`points`=:points,`active`=:active, branch_id='0'",[
							'name' => $service_name,
							'cat'  => $category,
							'duration' => $duration,
							'price'    => $price,
							'points'   => $reward_point,
							'active'   => 0 
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
			$_SESSION['tmsg']  = "Services added Successfully";
        }
	} else {
		$return_arr['status'] = 0;
		$return_arr['message'] = "File Should be in .xlsx Format.";
	}

	echo json_encode($return_arr);
}

?>