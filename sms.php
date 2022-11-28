<?php
	include "includes/db_include.php";
    $branch_id = $_SESSION['branch_id'];
    $sql="SELECT total,sent,(total-sent) as pending FROM `sms` where branch_id='".$branch_id."'";
    $result=query_by_id($sql,[],$conn)[0];
 
?>
<html>
    <head>
        <title><?=systemname()?></title>
    </head>
    <body>
        <div style="text-align:center;vertical-align: middle;font-size: 24px;">
            Total SMS: <?=$result['total']?><br>
            SENT: <?=$result['sent']?><br>
            REMAINING: <?=$result['pending']?><br>
        </div>
    </body>
</html>