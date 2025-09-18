<?php
include '../../action.php';
//Update availability today
if(isset($_POST['save_lat_long'])){
    $json=(object) null;
    $update_done=0;
    if(isset($_POST['job_id'])){
        $latitude=$_POST['latitude'];
        $longitude=$_POST['longitude'];
        $postcode_data=$latitude.",".$longitude;
        $update_array=array("postcode_data"=>$postcode_data);
        $obj->update("interpreter",$update_array,"id=".$_POST['job_id']);
        $update_done=1;
        if($update_done==1){
            $json->status="1";
            $json->msg="Job postcode data has been updated. Thank you";
        }else{
            $json->status="0";
            $json->msg="Failed to update job postcode data. Try again";
        }
    }else{
        $json->status="0";
        $json->msg="Job ID is required to perform this action!";
    }
    header('Content-Type: application/json');
    echo json_encode($json, JSON_UNESCAPED_UNICODE);
}
/* update interpreter location start */
if(isset($_POST['update_location'])){
    // error_reporting(E_ALL);
    $json=(object) null;
    $table = 'interpreter_reg';
    if(isset($_POST['ap_user_id']) && !empty($_POST['ap_user_id'])){
        $interpreter_id = $_POST['ap_user_id'];
        $lat = $_POST['lat'];
        $lng = $_POST['lng'];
        $json = [];
        $obj->update($table,array('lat' => $lat,'lng' => $lng),"id=".$interpreter_id);
        
        }else{
            $json->msg="not_logged_in";
    }
    header('Content-Type: application/json');
    echo json_encode($json, JSON_UNESCAPED_UNICODE);
}
?>