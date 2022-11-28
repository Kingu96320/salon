<?php
include "../includes/db_include.php";
$branch_id = $_SESSION['branch_id'];
if (isset($_GET['term'])){
    $term = htmlspecialchars($_GET['term']);
    $sql="SELECT id,cat as value,cat FROM `servicecat` WHERE cat LIKE '%$term%' and active='0'";
    $return_arr = query($sql,[],$conn); 
    if($return_arr){
        echo json_encode($return_arr);
    }
}
?>