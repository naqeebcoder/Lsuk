<?php
include '../action.php';
//get interpreter document details request
if(isset($_POST['view_single_document']) && isset($_POST['ap_doc_id'])){
    $json=(object) null;
    if(isset($_POST['ap_user_id']) && !empty($_POST['ap_user_id'])){
        $row_doc=$obj->read_specific("em_type as title,em_date as date,em_format as content","post_format","id=".$_POST['ap_doc_id']);
        $json=$row_doc;
    }else{
        $json->msg="not_logged_in";
    }
    header('Content-Type: application/json');
    echo json_encode($json, JSON_UNESCAPED_UNICODE);
}
?>