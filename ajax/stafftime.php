<?php
include "../includes/db_include.php";

if (isset($_REQUEST['id'])){
	$id = $_REQUEST['id'];
	$arr = array();
	$sql1=query_by_id("SELECT * from beauticians where id=$id",[],$conn);
	 
	foreach($sql1 as $row1){
		$arr['start'] = $row1['starttime'];
		$arr['end'] = $row1['endtime'];
	}
	echo json_encode($arr);
}
?>