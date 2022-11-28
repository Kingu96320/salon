<?php
include "../includes/db_include.php";
$branch_id = $_SESSION['branch_id'];
if (isset($_GET['term'])){
    $term = htmlspecialchars($_GET['term']);
    $sql="SELECT id,name as value FROM `units` WHERE name LIKE '%$term%' and branch_id='".$branch_id."'";
    $return_arr = query($sql,[],$conn); 
    if($return_arr){
        echo json_encode($return_arr);
    }
}
?>