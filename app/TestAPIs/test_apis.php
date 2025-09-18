<?php
include '../action.php';
include '../db.php';
 error_reporting(E_ALL);
 ini_set("display_errors", 1);
//Create test device notification
if(isset($_POST['device_notification'])){
    $token=$_POST['token'];
    $json=(object) null;
    $json->sent=0;$notification_id=135;
    $subject="Test notification from LSUK IT";
    $sub_title="Greetings! You have got the test notification from LSUK.";
    if(!empty($token)){
        $obj->notify($token,$subject,$sub_title,array("type_key"=>"ja","notification_id"=>$notification_id,"hashCode"=>123456));
        $json->sent=1;
    }
    header('Content-Type: application/json');
    echo json_encode($json, JSON_UNESCAPED_UNICODE);
}
//Create test notification to all devices for 1 interpreter
if(isset($_POST['test_notification'])){
    $json=(object) null;
    $json->sent=0;
    $type_key=$_POST['type_key'];
    $job_type=$_POST['job_type'];
    $notification_id=135;
    $subject="New ".$job_type." Interpreting Project 9545";
    $subject2="Mark your availability for Thursday";
    $sub_title=$job_type." Interpreting job of Arabic language on 12-11-2021 at 12:45:00 is available for you to bid.";
    $array_tokens=explode(',',$obj->read_specific("GROUP_CONCAT( DISTINCT token) as tokens","int_tokens","int_id=874")['tokens']);
    $obj->update("notify_new_doc",array("new_notification"=>1),"interpreter_id=874");
    $availability_note="Good morning!\nCan you mark your presence for the day. This doesn't guarantee a job but makes easy for LSUK to allocate a job.\nThank you";
    if(!empty($array_tokens)){
        foreach($array_tokens as $token){
            if(!empty($token)){
                $obj->notify($token,"🔔 ".$subject2,$availability_note,array("type_key"=>$type_key,"job_type"=>$job_type,"user_id"=>874));
                $json->sent=1;
            }
        }
    }
    header('Content-Type: application/json');
    echo json_encode($json, JSON_UNESCAPED_UNICODE);
}

//Create test active jobs in App
if(isset($_POST['make_active_jobs'])){
    $json=(object) null;
    $json->f2f_done = $json->tp_done = $json->tr_done = 1;
    $assignDate = $_POST['assignDate'] ? $_POST['assignDate'] : date("Y-m-d");
    $assignTime = $_POST['assignTime'] ? $_POST['assignTime'] : date("H:i:s");
    $check_existing=$obj->read_specific("parking_tickets","interpreter","id=11166")["parking_tickets"];
    if(!empty($check_existing)){
        $existing_parking_tickets=json_decode($check_existing, true);
        foreach($existing_parking_tickets as $key=>$val){
            $old_parking_file="../../file_folder/parking_tickets/".$val;
            if(file_exists($old_parking_file) && !empty($old_parking_file)){
                unlink($old_parking_file);
            }
        }
    }
    $check_existing_uploads=$obj->read_all("*","job_files","tbl='interpreter' AND order_id=11166 AND interpreter_id=874 AND file_type='timesheet'");
    if($check_existing_uploads->num_rows > 0){
        while($row = $check_existing_uploads->fetch_assoc()){
            $old_file="../../file_folder/job_files/".$row['file_name'];
            if(file_exists($old_file) && !empty($old_file)){
                unlink($old_file);
                $obj->delete("job_files","id = " . $row['id']);
            }
        }
    }
    $f2f_array = array("parking_tickets" => "", "assignDate" => $assignDate, "assignTime" => $assignTime, "hoursWorkd" => 0, "intrpName" => '874', "rateHour" => 0, "chargInterp" => 0, "deduction" => 0, "admnchargs" => 0, "travelMile" => 0, "rateMile" => 0, "chargeTravel" => 0, "travelCost" => 0, "otherCost" => 0, "travelTimeHour" => 0, "travelTimeRate" => 0, "chargeTravelTime" => 0, "total_charges_interp" => 0, "hrsubmited" => "", "order_cancel_flag" => 0, "deleted_flag" => 0, "wt_tm" => "1001:01:01 00:00:00", "st_tm" => "1001:01:01 00:00:00", "fn_tm" => "1001:01:01 00:00:00", "cl_sig" => "", "int_sig" => "", "int_sign_date" => NULL, "cl_sign_date" => NULL, "salary_id" => 0);
    $f2f_done = $obj->update("interpreter", $f2f_array, "id=11166");
    $tp_array = array("assignDate" => $assignDate, "assignTime" => $assignTime, "hoursWorkd" => 0, "intrpName" => '874', "rateHour" => 0, "chargInterp" => 0, "deduction" => 0, "admnchargs" => 0, "calCharges" => 0, "otherCharges" => 0, "total_charges_interp" => 0, "hrsubmited" => "", "order_cancel_flag" => 0, "deleted_flag" => 0, "wt_tm" => "1001:01:01 00:00:00", "st_tm" => "1001:01:01 00:00:00", "fn_tm" => "1001:01:01 00:00:00", "int_sig" => "", "int_sign_date" => NULL, "salary_id" => 0);
    $tp_done = $obj->update("telephone", $tp_array, "id=3288");
    $tr_array = array("asignDate" => $assignDate, "numberUnit" => 0, "intrpName" => '874', "rpU" => 0, "certificationCost" => 0, "deduction" => 0, "admnchargs" => 0, "proofCost" => 0, "postageCost" => 0, "otherCharg" => 0, "total_charges_interp" => 0, "hrsubmited" => "", "order_cancel_flag" => 0, "deleted_flag" => 0, "int_sig" => "", "int_sign_date" => NULL, "salary_id" => 0);
    $tr_done = $obj->update("translation", $tr_array, "id=1427");
    if ($f2f_done) {
        $json->f2f_done = 1;
    }
    if ($tp_done) {
        $json->tp_done = 1;
        $obj->delete("interpreter_hours", "order_id=3288");
    }
    if ($tr_done) {
        $json->tr_done = 1;
    }
    header('Content-Type: application/json');
    echo json_encode($json, JSON_UNESCAPED_UNICODE);
}

if(isset($_POST['make_available_jobs'])){
    $json=(object) null;
    $json->f2f_done = $json->tp_done = $json->tr_done = 1;
    $assignDate = $_POST['assignDate'] ? $_POST['assignDate'] : date("Y-m-d");
    $assignTime = $_POST['assignTime'] ? $_POST['assignTime'] : date("H:i:s");
    $check_existing=$obj->read_specific("parking_tickets","interpreter","id=11166")["parking_tickets"];
    if(!empty($check_existing)){
        $existing_parking_tickets=json_decode($check_existing, true);
        foreach($existing_parking_tickets as $key=>$val){
            $old_parking_file="../../file_folder/parking_tickets/".$val;
            if(file_exists($old_parking_file) && !empty($old_parking_file)){
                unlink($old_parking_file);
            }
        }
    }
    $check_existing_uploads=$obj->read_all("*","job_files","tbl='interpreter' AND order_id=11166 AND interpreter_id=874 AND file_type='timesheet'");
    if($check_existing_uploads->num_rows > 0){
        while($row = $check_existing_uploads->fetch_assoc()){
            $old_file="../../file_folder/job_files/".$row['file_name'];
            if(file_exists($old_file) && !empty($old_file)){
                unlink($old_file);
                $obj->delete("job_files","id = " . $row['id']);
            }
        }
    }
    $f2f_array = array("parking_tickets" => "", "assignDate" => $assignDate, "assignTime" => $assignTime, "hoursWorkd" => 0, "intrpName" => '', "rateHour" => 0, "chargInterp" => 0, "deduction" => 0, "admnchargs" => 0, "travelMile" => 0, "rateMile" => 0, "chargeTravel" => 0, "travelCost" => 0, "otherCost" => 0, "travelTimeHour" => 0, "travelTimeRate" => 0, "chargeTravelTime" => 0, "total_charges_interp" => 0, "hrsubmited" => "", "order_cancel_flag" => 0, "deleted_flag" => 0, "wt_tm" => "1001:01:01 00:00:00", "st_tm" => "1001:01:01 00:00:00", "fn_tm" => "1001:01:01 00:00:00", "cl_sig" => "", "int_sig" => "", "int_sign_date" => NULL, "cl_sign_date" => NULL, "salary_id" => 0);
    $f2f_done = $obj->update("interpreter", $f2f_array, "id=11166");
    $tp_array = array("assignDate" => $assignDate, "assignTime" => $assignTime, "hoursWorkd" => 0, "intrpName" => '', "rateHour" => 0, "chargInterp" => 0, "deduction" => 0, "admnchargs" => 0, "calCharges" => 0, "otherCharges" => 0, "total_charges_interp" => 0, "hrsubmited" => "", "order_cancel_flag" => 0, "deleted_flag" => 0, "wt_tm" => "1001:01:01 00:00:00", "st_tm" => "1001:01:01 00:00:00", "fn_tm" => "1001:01:01 00:00:00", "int_sig" => "", "int_sign_date" => NULL, "salary_id" => 0);
    $tp_done = $obj->update("telephone", $tp_array, "id=3288");
    $tr_array = array("asignDate" => $assignDate, "numberUnit" => 0, "intrpName" => '', "rpU" => 0, "certificationCost" => 0, "deduction" => 0, "admnchargs" => 0, "proofCost" => 0, "postageCost" => 0, "otherCharg" => 0, "total_charges_interp" => 0, "hrsubmited" => "", "order_cancel_flag" => 0, "deleted_flag" => 0, "int_sig" => "", "int_sign_date" => NULL, "salary_id" => 0);
    $tr_done = $obj->update("translation", $tr_array, "id=1427");
    if ($f2f_done) {
        $json->f2f_done = 1;
        $obj->delete("bid", "job=11166 AND tabName='interpreter' AND interpreter_id=874");
    }
    if ($tp_done) {
        $json->tp_done = 1;
        $obj->delete("interpreter_hours", "order_id=3288");
        $obj->delete("bid", "job=3288 AND tabName='telephone' AND interpreter_id=874");
    }
    if ($tr_done) {
        $json->tr_done = 1;
        $obj->delete("bid", "job=1427 AND tabName='translation' AND interpreter_id=874");
    }
    header('Content-Type: application/json');
    echo json_encode($json, JSON_UNESCAPED_UNICODE);
}

if (isset($_GET['bulk_notify']) && isset($_GET['notify_count']) && isset($_GET['event_id'])) {

    $notifyCount = (int) $_GET['notify_count'];
    $eventId = (int) $_GET['event_id'];

    $json = (object) ['total' => 0, 'sent' => 0, 'failed' => 0, 'details' => []];

    // Fetch 100 interpreters for this event with no reply and specified mobile_notification count
    $sql = "SELECT ce.event_id, ce.interpreter_id, it.token 
            FROM cpd_events ce 
            JOIN int_tokens it ON ce.interpreter_id = it.int_id 
            WHERE ce.reply = 0 
              AND ce.mobile_notification = $notifyCount 
              AND ce.event_id = $eventId 
            LIMIT 100";

    $res = mysqli_query($con, $sql);

    while ($row = mysqli_fetch_assoc($res)) {
        $interpreter_id = $row['interpreter_id'];
        $token = $row['token'];

        if (empty($token)) {
            $json->failed++;
            continue;
        }

        $subject = "Reminder: CPD Event Invitation";
        $sub_title = "You are invited to a CPD event. Please respond.";

        $sent=true;//$sent = $obj->notify($token, $subject, $sub_title, [
        //     "type_key" => "cpd_invite",
        //     "event_id" => $eventId,
        //     "interpreter_id" => $interpreter_id
        // ]);

        if ($sent) {
            mysqli_query($con, "UPDATE cpd_events 
                                 SET mobile_notification = mobile_notification + 1 
                                 WHERE interpreter_id = $interpreter_id AND event_id = $eventId");
            $json->sent++;
        } else {
            $json->failed++;
        }

        $json->total++;
        $json->details[] = [
            "interpreter_id" => $interpreter_id,
            "status" => $sent ? "sent" : "failed"
        ];
    }

    header('Content-Type: application/json');
    echo json_encode($json, JSON_UNESCAPED_UNICODE);
}


?>