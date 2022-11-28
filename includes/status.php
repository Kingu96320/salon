<?php
    if(isset($alert)){
        echo $alert;
    }
    
    if(isset($msg)){
        echo alert($msg,'warning');
    }
    
    if(isset($_SESSION['status'])){
        echo alert($_SESSION['status'],'info');
        unset($_SESSION['status']);
    }   