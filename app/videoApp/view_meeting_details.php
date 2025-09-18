<?php
include '../action.php';
//View meeting edtails
if (isset($_POST['view_meeting_details']) && isset($_POST['meeting_id'])) {
    $json = (object) null;
    $table = "telephone";
    $device_id = trim($_POST['device_id']);
    $display_name = trim(ucwords($_POST['display_name']));
    $display_name = !empty($display_name) ? $display_name : "Unknown User";
    $meeting_id = (int) trim(preg_replace("/[^0-9]/", "", $_POST['meeting_id']));
    $put_id = "AND $table.id=" . $meeting_id;
    $query_details = "$table.assignDate,$table.assignTime,$table.assignDur,(SELECT comunic_types.c_title from comunic_types WHERE comunic_types.c_id=telephone.comunic) as meeting_title,telephone.fn_tm";
    $row = $obj->read_specific("$table.id as job_id,$table.nameRef as meeting_id,$table.source,$table.target,$query_details", "$table,interpreter_reg", "$table.intrpName=interpreter_reg.id AND $table.deleted_flag=0 and $table.order_cancel_flag=0 and $table.jobStatus= 1 and $table.salary_id=0 and $table.hrsubmited='' and $table.fn_tm = '1001-01-01 00:00:00' $put_id");
    if ($row) {
        if (!empty($device_id)) {
            $row_user = $obj->read_specific("*", "meeting_users", "device_id='" . $device_id . "' AND order_id=" . $meeting_id);
            if (!empty($row_user)) {
                $row['display_name'] = ucwords($row_user['name']);
                $row['device_id'] = $row_user['device_id'];
            } else {
                $obj->insert("meeting_users", array("order_id" => $meeting_id, "device_id" => $device_id, "name" => $display_name, "created_date" => date("Y-m-d H:i:s")));
                $row['display_name'] = $display_name;
                $row['device_id'] = $device_id;
            }
        } else {
            $obj->insert("meeting_users", array("order_id" => $meeting_id, "name" => $display_name, "created_date" => date("Y-m-d H:i:s")));
            $row['display_name'] = $display_name;
            $row['device_id'] = NULL;
        }
        $row['meeting_id'] = strtoupper(str_replace("/", "", $row['meeting_id']));
        $assignDate = $row['assignDate'];
        $assignTime = $row['assignTime'];
        $assignDur = $row['assignDur'];
        $assignDurDouble = ($row['assignDur'] * 2);
        $expected_start = date($assignDate . ' ' . substr($assignTime, 0, 5));
        $expected_end = date("Y-m-d H:i", strtotime("+$assignDur minutes", strtotime($expected_start)));
        $expected_end_double = date("Y-m-d H:i", strtotime("+$assignDurDouble minutes", strtotime($expected_start)));
        $row['expected_start'] = date("d-m-Y H:i", strtotime($expected_start));
        $row['expected_end'] = date("d-m-Y H:i", strtotime($expected_end));
        if ($expected_end_double < date("Y-m-d H:i:s")) {
            $json->status = "failed";
            $json->msg = "This meeting has been finished over now! Thank you";
        } else {
            if ($assignDur > 60) {
                $hours = $assignDur / 60;
                if (floor($hours) > 1) {
                    $hr = "hours";
                } else {
                    $hr = "hour";
                }
                $mins = $assignDur % 60;
                if ($mins == 0) {
                    $get_dur = sprintf("%2d $hr", $hours);
                } else {
                    $get_dur = sprintf("%2d $hr %02d minutes", $hours, $mins);
                }
            } else if ($assignDur == 60) {
                $get_dur = "1 Hour";
            } else {
                $get_dur = $assignDur . " minutes";
            }
            $row['assignDur'] = $get_dur;
            $row['assignDate'] = $misc->dated($row['assignDate']);
            unset($row['fn_tm']);
            $row['status'] = "success";
            $json = $row;
        }
    } else {
        $json->status = "failed";
        $json->msg = "No record found against entered meeting ID!";
    }
    header('Content-Type: application/json');
    echo json_encode($json, JSON_UNESCAPED_UNICODE);
}
