<?php
include '../action.php';
if(isset($_POST['logout'])){
    $json=(object) null;
    $json->status="success";
    $json->msg="You have been successfully logged out! Thank you";
    header('Content-Type: application/json');
    echo json_encode($json, JSON_UNESCAPED_UNICODE);
}
?>