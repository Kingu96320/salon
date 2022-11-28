<?php
    include_once "../includes/db_include.php";
    $sql = "";
    if($_GET['type'] == 0){
        // new
        $sql = "SELECT id, name as t from client where doj = '".date('Y-m-d')."' ";
    }
    else if($_GET['type'] == 1){
        // repeating
        $sql = " SELECT c.id, c.name from invoice_".$_SESSION['branch_id']." i inner join client c on i.client = c.id where c.doj not in ('".date('Y-m-d')."') and i.doa='".date('Y-m-d')."' UNION SELECT  c.id, c.name from app_invoice_".$_SESSION['branch_id']." a  inner join client c on a.client = c.id where c.doj not in ('".date('Y-m-d')."') and a.doa='".date('Y-m-d')."' ";
    }
    $clients = query_by_id($sql, [], $conn);
    if($clients){
        $i = 0;
        echo "<table class='table tbl responsive'>";
        echo "<tr><th>#</th><th>Client ID</th><th>Name</th></tr>";
        foreach($clients as $cl){
            $i+=1;
            echo "<tr><td>".$i."</td><td>".$cl['id']."</td><td>".$cl['name']."</td></tr>";
        }
        echo "</table>";
    }
    
?>