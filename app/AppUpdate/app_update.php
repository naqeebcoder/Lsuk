<?php
include '../action.php';
//Check if app update available
if(isset($_POST['app_update'])){
    $json=(object) null;
    $row=$obj->read_specific("android_update,ios_update","app_update","id=1");
    $json->android_update=$row['android_update'];
    $json->ios_update=$row['ios_update'];
    header('Content-Type: application/json');
    echo json_encode($json, JSON_UNESCAPED_UNICODE);
}
?>