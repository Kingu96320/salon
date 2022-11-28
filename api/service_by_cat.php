<?php
include_once '../includes/db_include.php';
include_once 'cors.php';
$service_for = $_POST['service_for'];

if($service_for != 0){
    $append = " and service_for='".$service_for."' ";
    $append2 = " and s.service_for='".$service_for."' ";
} else {
    $append = "";
    $append2 = "";
}

// service list according to the categories
$catlist = query("select DISTINCT sc.* from servicecat sc LEFT JOIN service s ON s.cat = sc.id where sc.active = 0 $append2 ",[],$conn);
$resultArr = array();
$subArray = array();
foreach($catlist as $list){
    $subArray['catId'] = $list['id'];
    $subArray['catName'] = $list['cat'];
    $service = query("select * from service where cat = '".$list['id']."' and active = 0",[],$conn);
    $serviceCount = query("select count(*) as total from service where cat = '".$list['id']."' and active = 0 $append ",[],$conn)[0]['total'];
    $subArray['serviceQty'] = $serviceCount;
    // $parentArray = array();
    // $serviceArray = array();
    // foreach($service as $ser){
    //     $serviceArray['serId'] = $ser['id'];
    //     $serviceArray['serName'] = $ser['name'];
    //     $serviceArray['serDuration'] = $ser['duration'];
    //     $serviceArray['serPrice'] = $ser['price'];a
    //     array_push($parentArray, $serviceArray);
    // }
    // $subArray['serviceList'] = $parentArray;
    array_push($resultArr, $subArray);
}
usort($resultArr, make_comparer(['catName',SORT_ASC]));

echo json_encode($resultArr);



function make_comparer() {
    // Normalize criteria up front so that the comparer finds everything tidy
    $criteria = func_get_args();
    foreach ($criteria as $index => $criterion) {
        $criteria[$index] = is_array($criterion)
            ? array_pad($criterion, 3, null)
            : array($criterion, SORT_ASC, null);
    }

    return function($first, $second) use (&$criteria) {
        foreach ($criteria as $criterion) {
            // How will we compare this round?
            list($column, $sortOrder, $projection) = $criterion;
            $sortOrder = $sortOrder === SORT_DESC ? -1 : 1;

            // If a projection was defined project the values now
            if ($projection) {
                $lhs = call_user_func($projection, $first[$column]);
                $rhs = call_user_func($projection, $second[$column]);
            }
            else {
                $lhs = $first[$column];
                $rhs = $second[$column];
            }

            // Do the actual comparison; do not return if equal
            if ($lhs < $rhs) {
                return -1 * $sortOrder;
            }
            else if ($lhs > $rhs) {
                return 1 * $sortOrder;
            }
        }

        return 0; // tiebreakers exhausted, so $first == $second
    };
}

?>