<?php
include_once './includes/db_include.php';
$branch_id = $_SESSION['branch_id'];
if (isset($_POST['ser'])){
	$res = array();
	$term = $_POST['ser'];
	$val = str_replace("%20", " ", $term);
		$sql="select concat('sr,',id) as id,name,price from service where name='$val' and branch_id='".$branch_id."' UNION select concat('pr,',id) as id,name,price from products where name='$val' and branch_id='".$branch_id."' UNION select concat('pa,',id) as id,name,price from packages where name='$val' and branch_id='".$branch_id."' ";
		$result=query_by_id($sql,[],$conn);
		if ($result) 
		{
			foreach($result as $row)
			{
			//echo  $row['price'];
			$res[] = $row; 
			}
		}else{
			$res[] = "No Column Found";
		}
		echo json_encode($res);
}
?>