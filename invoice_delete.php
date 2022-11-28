<?php 
	include_once './includes/db_include.php';

 	$branch_id = $_SESSION['branch_id'];

// 	if(isset($_POST['inv'])){
	   
		$inv = $_POST['inv'];
		$table = 'invoice_'.$branch_id ;
	
	
	        $result =  query("UPDATE $table set `active`= 1 where `id`='$inv' and `branch_id`='".$branch_id."'",[],$conn);
	   
	   
		 	echo json_encode(1);
	   
// 	}


	
?>