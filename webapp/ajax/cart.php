<?php
    session_start();
    if(!isset($_SESSION['cart'])){
        $_SESSION['cart'] = array();
    }
    if(isset($_POST['action']) && $_POST['action'] == 'add_cart'){
        $service = array();
        $service['name'] = $_POST['sname'];
        $service['price'] = $_POST['sprice'];
        $service['duration'] = $_POST['sduration'];
        $service['sid'] = $_POST['sid'];
        array_push($_SESSION['cart'],$service);
        echo json_encode('1');
    }
    
    if(isset($_POST['action']) && $_POST['action'] == 'remove_cart'){
        $index = $_POST['index'];
        unset($_SESSION['cart'][$index]);
        echo json_encode('1');
    }
?>