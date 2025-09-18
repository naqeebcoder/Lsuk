<?php
include '../action.php';
//app delete token
if(isset($_POST['ap_delete_token']) && isset($_POST['ap_device_id'])){
    $json=(object) null;
    
    if($obj->delete("int_tokens","device_id='".$_POST['ap_device_id']."'")){
        $json->msg="token_deleted";
    }else{
        $json->msg="token_not_deleted";
    }
    header('Content-Type: application/json');
    echo json_encode($json, JSON_UNESCAPED_UNICODE);
}
?>