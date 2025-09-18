<?php
include '../action.php';
//app update token
if(isset($_POST['ap_update_token']) && isset($_POST['ap_token']) && isset($_POST['ap_device_id'])){
    $json=(object) null;
    if($obj->update("int_tokens",array("token"=>$_POST['ap_token'],"dated"=>date('Y-m-d H:i:s')),"device_id='".$_POST['ap_device_id']."'")){
        $json->msg="token_updated";
    }else{
        $json->msg="token_not_updated";
    }
    header('Content-Type: application/json');
    echo json_encode($json, JSON_UNESCAPED_UNICODE);
}
?>