<?php 
require_once '../includes/db_include.php';
$branch_id = $_SESSION['branch_id'];
$arr=[];
$sql="SELECT * from `messages` where status='1' and branch_id='".$branch_id."'";
		$query=query_by_id($sql,[],$conn);
		foreach($query as $row){
			$arr[] = array(
			'messages'	=> $row['message'],
			'action'    => '<a href="javascript:void(0);" class="btn btn-xs btn-info" onClick="edit_delete('.$row['id'].',\'edit\');">Edit</a> <a href="javascript:void(0);" class="btn btn-xs btn-danger" onClick="edit_delete('.$row['id'].',\'delete\');">Delete</a>',
			);
	}

$results = array("sEcho" => 2,
"iTotalRecords" => count($arr),
"iTotalDisplayRecords" => count($arr),
"aaData"=>  $arr);
if(isset($results))
{
	echo json_encode($results);
}
 