<?php
include '../action.php';
//show bank details
if(isset($_POST['show_bank_details'])){
    $json=(object) null;
    if(isset($_POST['ap_user_id']) && !empty($_POST['ap_user_id'])){
        $json=$obj->read_specific("bnakName as bank_name,acName as account_name,acntCode as sort_code,acNo as account_number,(CASE WHEN acNo='' THEN '0' ELSE '1' END) as fill","interpreter_reg","id=".$_POST['ap_user_id']);
    }else{
        $json->msg="not_logged_in";
    }
    header('Content-Type: application/json');
    echo json_encode($json, JSON_UNESCAPED_UNICODE);
}
?>