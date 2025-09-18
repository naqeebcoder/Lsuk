<?php
include '../action.php';
if(isset($_POST['get_available_jobs'])){
    $json=(object) null;
    if(isset($_POST['ap_user_id']) && !empty($_POST['ap_user_id'])){
        $row=$obj->read_specific("interp,telep,trans,availability_option,is_marked","interpreter_reg","id=".$_POST['ap_user_id']);
        $available_jobs=array();
        if($row['interp']=="Yes"){
            array_push($available_jobs,"Face To Face");
        }
        if($row['telep']=="Yes"){
            array_push($available_jobs,"Telephone");
        }
        if($row['trans']=="Yes"){
            array_push($available_jobs,"Translation");
        }
        $json->availability_note="Goog morning! \nCan you mark your presence for the day. This doesn't guarantee a job but makes easy for LSUK to allocate a job. \nThank you";
        if($row['availability_option']=="1" && $row['is_marked']=="0"){
            $json->show_availability="1";
        }else{
            $json->show_availability="0";
        }
        $json->ap_status="success";
        $json->available_jobs=$available_jobs;
    }else{
        $json->msg="not_logged_in";
        $json->ap_status="failed";
    }
    header('Content-Type: application/json');
    echo json_encode($json, JSON_UNESCAPED_UNICODE);
}
?>