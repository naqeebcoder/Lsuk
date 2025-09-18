<?php 
include '../action.php';
//Update app version
if(isset($_POST['version_update'])){
    $json=(object) null;
    $update_done=0;
    
    if(isset($_POST['android_version'])){
        $obj->update("app_update",array("android_update"=>$_POST['android_version']),"id=1");
        $update_done=1;
    }
    if(isset($_POST['ios_version'])){
        $obj->update("app_update",array("ios_update"=>$_POST['ios_version']),"id=1");
        $update_done=1;
    }
    if($update_done==1){
        $json->msg="success";
    }else{
        $json->msg="failed";
    }
    header('Content-Type: application/json');
    echo json_encode($json, JSON_UNESCAPED_UNICODE);
}
?>