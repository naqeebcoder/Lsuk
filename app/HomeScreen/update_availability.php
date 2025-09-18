<?php
include '../action.php';
//Update availability today
if(isset($_POST['update_availability'])){
$json=(object) null;
$update_done=0;
if(isset($_POST['ap_user_id'])){
    $label=$_POST['update_availability']==1?"Yes":"No";
    $update_array=array(strtolower(date("l"))=>$label);
    if($_POST['update_availability']==1){
        $update_array[strtolower(date("l"))."_time"]=$_POST['availability_from'];
        $update_array[strtolower(date("l"))."_to"]=$_POST['availability_to'];
    }else{
        $update_array[strtolower(date("l"))."_time"]="00:00:00";
        $update_array[strtolower(date("l"))."_to"]="00:00:00";
    }
    $update_array['is_marked']=1;
    $obj->update("interpreter_reg",$update_array,"id=".$_POST['ap_user_id']);
    $update_done=1;
    if($update_done==1){
        $json->status="1";
        $json->msg="Your today's availability has been updated. Thank you";
    }else{
        $json->status="0";
        $json->msg="Failed to update your today's availability. Try again";
    }
}else{
    $json->status="0";
    $json->msg="You must login to perform this action!";
}
header('Content-Type: application/json');
echo json_encode($json, JSON_UNESCAPED_UNICODE);
}
?>