<?php
include_once './includes/db_include.php';
$branch_id = $_SESSION['branch_id'];
if(!empty($_GET["pack"])) {
	$num = $_GET["pack"];
	$res = array();
	$sql="SELECT * from client where cont like '%$num%' and branch_id='".$branch_id."'";
	$result=query_by_id($sql,[],$conn);
	if($result) 
	{
		foreach($result as $row)
		{
		$res[] = $row; 
		}
	}else{
		$res = "Wrong Values";
	}
	echo json_encode($res);
}
?>
