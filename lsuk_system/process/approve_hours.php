<?php
session_start();

if (isset($_POST['approve_hours']) && (isset($_POST['job_id']) || isset($_POST['table']))) {
    $update_array = array("approved_flag" => 1, "approved_by" => $_SESSION['userId'], "approved_date" => date("Y-m-d H:i:s"));
    include '../actions.php';
    $type_array = array("interpreter" => "f2f", "telephone" => "telephone", "translation" => "translation");
    $short_type_array = array("interpreter" => "F2F", "telephone" => "TP", "translation" => "TR");
    $response = array("status" => 0, "message" => "Cannot approve this job! Please try again later");
    $is_already_approved = $obj->read_specific("approved_flag", $_POST['table'], "id=" . $_POST['job_id'])['approved_flag'];
    if ($is_already_approved == 1) {
        $response['message'] = "Hours are already approved by another system user! Please refresh the page";
    } else {
        $done = $obj->update($_POST['table'], $update_array, "id=" . $_POST['job_id']);
        if ($done) {
            $response['status'] = 1;
            $response['message'] = "You have successfully approved hours for this job. Thank you";
            // Log action
            $obj->insert("daily_logs", array("action_id" => 39, "user_id" => $_SESSION['userId'], "details" => $short_type_array[$_POST['table']] . " Job ID: " . $_POST['job_id']));
            $index_mapping = array(
                'Approved.Flag' => 'approved_flag', 'Approved By' => 'approved_by', 'DateTime' => 'approved_date'
            );

            $old_values = array();
            $new_values = array();
            $get_new_data = $obj->read_specific("*", $_POST['table'], "id=" . $_POST['job_id']);

            foreach ($index_mapping as $key => $value) {
                if (isset($get_new_data[$value])) {
                    $old_values[$key] = $row[$value];
                    $new_values[$key] = $get_new_data[$value];
                }
            }
            $obj->log_changes(json_encode($old_values), json_encode($new_values), $_POST['job_id'], $_POST['table'], "update", $_SESSION['userId'], $_SESSION['UserName'], "approved_job_" . $type_array[$_POST['table']]);
        }
    }
    echo json_encode($response);
}