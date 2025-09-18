<?php
include '../action.php';
//app token creation
if(isset($_POST['ap_device_id']) && isset($_POST['ap_add_token']) && isset($_POST['ap_user_id'])){
    $json=(object) null;
    if(isset($_POST['ap_user_id']) && !empty($_POST['ap_user_id'])){
        $token_done=0;
		$idz=$obj->read_specific("GROUP_CONCAT(id) as idz","int_tokens","device_id='".$_POST['ap_device_id']."' OR token='".$_POST['ap_add_token']."' OR (int_id=".$_POST['ap_user_id']." and dated < DATE_SUB(NOW(), INTERVAL 30 DAY))")["idz"];
        if(!is_null($idz)){
			$obj->delete("int_tokens","id IN (".$idz.")");
		}
        $obj->insert("int_tokens",array("device_id"=>$_POST['ap_device_id'],"int_id"=>$_POST['ap_user_id'],"token"=>$_POST['ap_add_token'],"dated"=>date('Y-m-d H:i:s')));
        $token_done=1;
        if($token_done==1){
            $json->msg="token_success";
        }else{
            $json->msg="token_failed";
        }
    }else{
        $json->msg="not_logged_in";
    }
    header('Content-Type: application/json');
    echo json_encode($json, JSON_UNESCAPED_UNICODE);
}
?>