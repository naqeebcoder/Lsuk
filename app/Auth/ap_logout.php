<?php
include '../action.php';
if(isset($_POST['ap_logout'])){
    $json=(object) null;
    $json->msg="logged_out";
    header('Content-Type: application/json');
    echo json_encode($json, JSON_UNESCAPED_UNICODE);
}
?>