<?php
include '../action.php';
if(isset($_POST['ap_get_notification'])){
    $json=(object) null;
    if(isset($_POST['ap_user_id']) && !empty($_POST['ap_user_id'])){
        $json->ap_status="success";
        $json=$obj->read_specific("(CASE WHEN notify_new_doc.status=1 THEN 0 ELSE 1 END) as doc_notify","interpreter_reg,notify_new_doc","interpreter_reg.id=notify_new_doc.interpreter_id AND interpreter_reg.id=".$_POST['ap_user_id']);
    }else{
        $json->msg="not_logged_in";
        $json->ap_status="failed";
    }
    header('Content-Type: application/json');
    echo json_encode($json, JSON_UNESCAPED_UNICODE);
}
?>