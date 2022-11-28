<?php 
    $duration =$_POST['duration'];
    $date = $_POST['sdate'];
    echo date('Y-m-d H:i:s',strtotime($date.' +'.$duration.' minutes'));
?>