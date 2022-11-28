<?php
include_once '../includes/db_include.php';
$branch_id = $_SESSION['branch_id'];
if ($_POST['module'] && $_POST['module'] == "clients") {
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
	            	$client_name = htmlspecialchars(trim(ucfirst($worksheet->getCellByColumnAndRow(0, $row)->getValue())));  
					$phone_number = htmlspecialchars(trim(ucfirst($worksheet->getCellByColumnAndRow(1, $row)->getValue())));  
					$gender = htmlspecialchars(trim($worksheet->getCellByColumnAndRow(2, $row)->getValue()));  
					$email = htmlspecialchars(trim($worksheet->getCellByColumnAndRow(3, $row)->getValue()));  
					$dob = trim($worksheet->getCellByColumnAndRow(4, $row)->getValue());
					$dob = date('Y-m-d', PHPExcel_Shared_Date::ExcelToPHP($dob));
					$anniversary = trim($worksheet->getCellByColumnAndRow(5, $row)->getValue());
					$anniversary = date('Y-m-d', PHPExcel_Shared_Date::ExcelToPHP($anniversary));
					$address = htmlspecialchars(trim($worksheet->getCellByColumnAndRow(6, $row)->getValue())); 

					if($client_name == '' || $phone_number == '' || strlen($phone_number) != 10){
						$empty_rows += 1;
						$return_arr['empty_rows'] = $empty_rows;
						continue;
					}
                    
                    $get_exist_client = query_by_id("SELECT count(*) as total FROM client WHERE cont='".$phone_number."' AND branch_id='".$branch_id."' AND active='0'",[],$conn)[0]['total'];
                    
                    if($get_exist_client > 0){
                        continue;
                    } else {
                        if($gender == 'Male'){
        	       	   		$gender_id = '1';
        	       	    } else if($gender == 'Female'){
        	       	   		$gender_id = '2';
        	       	    } else {
                            $gender_id = '0';
                        }
                        $referral_code  = str_replace('.','0',str_replace(' ', '0', addslashes(trim(strtoupper(substr($client_name,0,4))).strtoupper(substr(md5(sha1($phone_number)),0,4)))));
                        query("INSERT INTO client SET name='".$client_name."', cont='".$phone_number."', gender='".$gender_id."', active='0', branch_id='".$branch_id."', dob='".$dob."', aniversary ='".$anniversary."', email='".$email."', referral_code='".$referral_code."', address='".$address."'", [], $conn);
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
			$_SESSION['tmsg']  = "Clients uploaded Successfully";
        }
	} else {
		$return_arr['status'] = 0;
		$return_arr['message'] = "File Should be in .xlsx Format.";
	}

	echo json_encode($return_arr);
}

?>