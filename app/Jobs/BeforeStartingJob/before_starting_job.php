<?php
include '../../action.php';
/* Marked on the way Start */
if(isset($_POST['on_the_way'])){
    // error_reporting(E_ALL);
    $json=(object) null;
    $table = 'interpreter';
    if(isset($_POST['ap_user_id']) && !empty($_POST['ap_user_id'])){
        $job_id = $_POST['job_id'];
        $json = [];
        $job = $obj->read_specific("on_the_way","interpreter","id=".$job_id);
        array_push($json , [
            'on_the_way' => $job['on_the_way']
        ]);
        $obj->update('interpreter',array('on_the_way' => 1),"id=".$job_id);
        }else{
            $json->msg="not_logged_in";
    }
    header('Content-Type: application/json');
    echo json_encode($json, JSON_UNESCAPED_UNICODE);
}
/* Marked on the way End */

/* Start Job */
if(isset($_POST['ap_start_job'])){
    // error_reporting(E_ALL);
    $json=(object) null;
    $table = 'interpreter';
    if(isset($_POST['ap_user_id']) && !empty($_POST['ap_user_id'])){
        $job_id = $_POST['job_id'];
        $json = [];
        $obj->update('interpreter',array('st_tm' => date('Y-m-d h:i')),"id=".$job_id);
        }else{
            $json->msg="not_logged_in";
    }
    header('Content-Type: application/json');
    echo json_encode($json, JSON_UNESCAPED_UNICODE);
}
/* Start Job end */

/* Marked arrived Start */
if(isset($_POST['marked_arrived'])){
    // error_reporting(E_ALL);
    $json=(object) null;
    $table = 'interpreter';
    if(isset($_POST['ap_user_id']) && !empty($_POST['ap_user_id'])){
        $job_id = $_POST['job_id'];
        $json = [];
        $job = $obj->read_specific("is_interpreter_arrived","interpreter","id=".$job_id);
        array_push($json , [
            'arrived' => $job['is_interpreter_arrived']
        ]);
        $obj->update('interpreter',array('is_interpreter_arrived' => 1,'wt_tm' => date('Y-m-d h:i')),"id=".$job_id);
        }else{
            $json->msg="not_logged_in";
    }
    header('Content-Type: application/json');
    echo json_encode($json, JSON_UNESCAPED_UNICODE);
}

/* Marked arrived End */
/* Marked ready telephone job */
if(isset($_POST['marked_ready'])){
    // error_reporting(E_ALL);
    $json=(object) null;
    $table = 'telephone';
    if(isset($_POST['ap_user_id']) && !empty($_POST['ap_user_id'])){
        $job_id = $_POST['job_id'];
        $json = [];
        $job = $obj->read_specific("is_interpreter_ready","telephone","id=".$job_id);
        array_push($json , [
            'ready' => $job['is_interpreter_ready']
        ]);
        $obj->update('telephone',array('is_interpreter_ready' => 1),"id=".$job_id);
        
        }else{
            $json->msg="not_logged_in";
    }
    header('Content-Type: application/json');
    echo json_encode($json, JSON_UNESCAPED_UNICODE);
}
?>