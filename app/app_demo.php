<?php include 'action.php';
// error_reporting(E_ALL);

if(isset($_POST['home_notification'])){
    $json=(object) null;
    if(isset($_POST['ap_user_id']) && !empty($_POST['ap_user_id'])){
        $json->new_notification=$obj->read_specific("new_notification","notify_new_doc","interpreter_id=".$_POST['ap_user_id'])['new_notification'];
        /*if($noty==1){
            $json->new_notification=0;
        }else{
            $json->new_notification=1;
        }*/
    }else{
        $json->msg="not_logged_in";
    }
    header('Content-Type: application/json');
    echo json_encode($json, JSON_UNESCAPED_UNICODE);
}

if(isset($_POST['view_notifications'])){
    $json=(object) null;
    if(isset($_POST['ap_user_id']) && !empty($_POST['ap_user_id'])){
        
        // $existing_notification=$obj->read_specific("new_notification","notify_new_doc","interpreter_id=".$_POST['ap_user_id'])['new_notification'];
        // if($existing_notification>0){
        //     $obj->update("notify_new_doc",array("new_notification"=>$existing_notification-1),"interpreter_id=".$_POST['ap_user_id']);
        // }
        $query=$obj->read_all("*","app_notifications","1 ORDER BY id DESC");
        $json=array();
        while($row = $query->fetch_assoc()){
            $int_ids=explode(',',$row['int_ids']);
            $read_ids=explode(',',$row['read_ids']);
            if(in_array($_POST['ap_user_id'],$int_ids)){
                if(!in_array($_POST['ap_user_id'],$read_ids)){
                    $row['read']='1';
                }else{
                    $row['read']='0';
                }
            unset($row['int_ids']);
            unset($row['read_ids']);
            array_push($json,$row);
            }
        }
    }else{
        $json->msg="not_logged_in";
    }
    header('Content-Type: application/json');
    echo json_encode($json, JSON_UNESCAPED_UNICODE);
}

if(isset($_POST['notification_id'])){
    if(isset($_POST['ap_user_id']) && !empty($_POST['ap_user_id'])){
        $still_remove=0;
        $get_data=$obj->read_specific("*","app_notifications","id=".$_POST['notification_id']);
        if($get_data['type_key']=="nj" || $get_data['type_key']=="ja" || $get_data['type_key']=="jc"){
            $obj->delete("app_notifications","id=".$_POST['notification_id']);
        }else{
            $read_ids=explode(',',$get_data['read_ids']);
            if(in_array($_POST['ap_user_id'],$read_ids)){
                $still_remove=1;
                $new_read_ids = implode(',',array_diff($read_ids, [$_POST['ap_user_id']]));
                $obj->update("app_notifications",array("read_ids"=>$new_read_ids),"id=".$_POST['notification_id']);
            }
        }
        $existing_notification=$obj->read_specific("new_notification","notify_new_doc","interpreter_id=".$_POST['ap_user_id'])['new_notification'];
        if($existing_notification>0){
          if((!empty($get_data['read_ids']) && $get_data['type_key']!="nd" && $get_data['type_key']!="md") || $still_remove==1){
            $obj->update("notify_new_doc",array("new_notification"=>$existing_notification-1),"interpreter_id=".$_POST['ap_user_id']);
          }
        }
    }else{
        $json->msg="not_logged_in";
    }
}

//View subcription
if(isset($_POST['view_subscription'])){
    $json=(object) null;
    if(isset($_POST['ap_user_id']) && !empty($_POST['ap_user_id'])){
        $row=$obj->read_specific("subscribe","interpreter_reg","id=".$_POST['ap_user_id']);
        $json->status=$row['subscribe'];
    }else{
        $json->msg="not_logged_in";
    }
    header('Content-Type: application/json');
    echo json_encode($json, JSON_UNESCAPED_UNICODE);
}

//Update subcribe for bidding
if(isset($_POST['ap_subscribe'])){
    $json=(object) null;
    if(isset($_POST['ap_user_id']) && !empty($_POST['ap_user_id'])){
        $subscribe=$_POST['ap_subscribe'];
        $upd=$obj->update("interpreter_reg",array("subscribe"=>$subscribe),"id=".$_POST['ap_user_id']);
        if($upd && $_POST['ap_subscribe']==0){
            $json->status="0";
            $json->msg="You have been unsubscribed from jobs notifications!";
        }else{
            $json->status="1";
            $json->msg="You have successfully subscribed to jobs notifications.";
        }
    }else{
        $json->msg="not_logged_in";
    }
    header('Content-Type: application/json');
    echo json_encode($json, JSON_UNESCAPED_UNICODE);
}

if(isset($_POST['view_policy'])){
    $json=(object) null;
    if(isset($_POST['policy_id']) && !empty($_POST['policy_id'])){
        $json->policy=$obj->read_specific("html","timesheet_policy","id=".$_POST['policy_id'])['html'];
    }else{
        $json->msg="no_policy_selected";
    }
    header('Content-Type: application/json');
    echo json_encode($json, JSON_UNESCAPED_UNICODE);
}
if(isset($_POST['get_links'])){
    $json=(object) null;
    $json->privacy_policy="https://lsuk.org/lsuk_system/file_folder/lsuk_files/privacy policy 2021.pdf";
    $json->terms_conditions="https://lsuk.org/lsuk_system/file_folder/lsuk_files/Conditions for Linguists.pdf";
    header('Content-Type: application/json');
    echo json_encode($json, JSON_UNESCAPED_UNICODE);
}
?>