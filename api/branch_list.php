<?php
include_once '../includes/db_include.php';
include_once 'cors.php';

$resultArr = array();
$branches = query("select b.id, b.branch_name, s.address from salon_branches b left join system s on s.branch_id = b.id where status = 1 order by id asc",[],$conn);
foreach($branches as $res){
    $branchArr = array();
    $branchArr['branchId'] = $res['id'];
    $branchArr['branchName'] = ucfirst(strtolower($res['branch_name']));
    $branchArr['address'] = htmlspecialchars($res['address']);
    array_push($resultArr,$branchArr);
}

echo json_encode($resultArr);
?>