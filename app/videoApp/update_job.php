<?php
include '../action.php';
//Update the job
if (isset($_POST['update_job']) && isset($_POST['job_id']) && isset($_POST['role'])) {
    $json = (object) null;
    $role = trim($_POST['role']);
    $job_id = trim($_POST['job_id']);
    if (isset($role) && isset($job_id) && (isset($_POST['start_time']) || isset($_POST['finish_time']))) {
        $row = $obj->read_specific("telephone.*,interpreter_reg.specific_agreed,interpreter_reg.rpm,interpreter_reg.email as interpreter_email,interpreter_reg.ratetravelworkmile,interpreter_reg.ratetravelexpmile", "telephone,interpreter_reg", "telephone.intrpName=interpreter_reg.id and telephone.id=" . $job_id);
        $interpreter_email = $row['interpreter_email'];
        $interpreter_rate_id = $row['interpreter_rate_id'];
        $interpreter_rate_data = !empty($row['interpreter_rate_data']) ? (array) json_decode($row['interpreter_rate_data']) : array();
        $hoursWorkd = number_format($row['hoursWorkd'], 2);
        $interpreter_id = $row['intrpName'];
        $rate_per_minute = $row['rpm'];
        $wt_tm = $row['wt_tm'];
        $st_tm = $row['st_tm'];
        $fn_tm = $row['fn_tm'];
        $assignDate = $row['assignDate'];
        $assignTime = $row['assignTime'];
        $assignDur = $row['assignDur'];
        $expected_start = date($assignDate . ' ' . substr($assignTime, 0, 5));
        $expected_end = date("Y-m-d H:i", strtotime("+$assignDur minutes", strtotime($expected_start)));
        $first_time = $row['wt_tm'] != '1001-01-01 00:00:00' ? $row['wt_tm'] : $row['st_tm'];
        if ($hoursWorkd == 0) {
            $row['hours_filled'] = 0;
        } else {
            $row['hours_filled'] = 1;
        }
        $row['wait_time_filled'] = $row['wt_tm'] == '1001-01-01 00:00:00' ? 0 : $row['wt_tm'];
        $row['start_time_filled'] = $row['st_tm'] == '1001-01-01 00:00:00' ? 0 : $row['st_tm'];
        $row['finish_time_filled'] = $row['fn_tm'] == '1001-01-01 00:00:00' ? 0 : $row['fn_tm'];

        if (isset($_POST['start_time']) && !isset($_POST['finish_time'])) {
            $start_time = $_POST['start_time'] == 1 ? date('Y-m-d H:i') : date("Y-m-d H:i", strtotime($_POST['start_time']));
            if ($role == 1) {
                $check_meeting_started = $obj->read_specific("id", "meeting_hours", "order_id=" . $job_id . " AND interpreter_id=" . $interpreter_id)['id'];
                if (empty($check_meeting_started)) {
                    $obj->insert("meeting_hours", array("order_id" => $job_id, "interpreter_id" => $interpreter_id, "created_by" => $interpreter_id, "start_time" => $start_time, "created_date" => $start_time), "id=" . $job_id);
                }
            } else {
                if ($row['start_time_filled'] == 0) {
                    $obj->update("telephone", array("st_tm" => $start_time), "id=" . $job_id);
                }
            }
            $json->status = "success";
            $json->msg = date("d-m-Y H:i", strtotime($start_time));
        }

        if (isset($_POST['finish_time']) && !isset($_POST['start_time'])) {
            $finish_time = $_POST['finish_time'] == 1 ? date('Y-m-d H:i') : date("Y-m-d H:i", strtotime($_POST['finish_time']));
            $check_meeting_finished = $obj->read_specific("id, finish_time", "meeting_hours", "order_id=" . $job_id . " AND interpreter_id=" . $interpreter_id);
            if ($role == 1) {
                if (!empty($check_meeting_finished['id']) && empty($check_meeting_finished['finish_time'])) {
                    $obj->update("meeting_hours", array("order_id" => $job_id, "interpreter_id" => $interpreter_id, "created_by" => $interpreter_id, "finish_time" => $finish_time), "order_id=" . $job_id . " AND interpreter_id=" . $interpreter_id);
                }
            } else {
                if ($row['finish_time_filled'] == 0) {
                    $obj->update("telephone", array("fn_tm" => $finish_time), "id=" . $job_id);
                }
                if (!empty($check_meeting_finished['id']) && empty($check_meeting_finished['finish_time'])) {
                    $obj->update("meeting_hours", array("order_id" => $job_id, "interpreter_id" => $interpreter_id, "created_by" => $interpreter_id, "finish_time" => $finish_time), "order_id=" . $job_id . " AND interpreter_id=" . $interpreter_id);
                }
            }
            $get_rec = $obj->read_specific("assignDate,assignTime,assignDur,wt_tm,st_tm,fn_tm", "telephone", "id=" . $job_id);
            $first_time = $get_rec['st_tm'];
            $last_time = $get_rec['fn_tm'];
            $t1 = strtotime($first_time);
            $t2 = strtotime($last_time);
            $diff = $t2 - $t1;
            $hours = $diff / 3600;
            $rounded_value = $misc->round_quarter($hours, 4);
            $new_hour_val = ($hours * 60) < $get_rec['assignDur'] ? $get_rec['assignDur'] : round($hours * 60);

            $obj->update("telephone", array("hoursWorkd" => $new_hour_val), "id=" . $job_id);
            $json->status = "success";
            $json->msg = date("d-m-Y H:i", strtotime($finish_time));
            $row = $obj->read_specific("telephone.hoursWorkd,telephone.chargInterp", "telephone", "telephone.id=" . $job_id);
            $hour_calculated = $row['hoursWorkd'];
            if ($hour_calculated != 0) {
                $hours_done = $hour_calculated;
                if ($hours_done > 60) {
                    $hours_c = $hours_done / 60;
                    if (floor($hours_c) > 1) {
                        $hr_c = "hours";
                    } else {
                        $hr_c = "hour";
                    }
                    $mins_c = $hours_done % 60;
                    if ($mins_c == 00) {
                        $get_dur_c = sprintf("%2d $hr_c", $hours_c);
                    } else {
                        $get_dur_c = sprintf("%2d $hr_c %02d minutes", $hours_c, $mins_c);
                    }
                } else if ($hours_done == 60) {
                    $get_dur_c = "1 Hour";
                } else {
                    $get_dur_c = number_format($hours_done) . " minutes";
                }
                $json->duration_worked = $get_dur_c;
            } else {
                $json->duration_worked = "Not filled yet";
            }
        }
    } else {
        $json->status = "failed";
        $json->msg = "Please provide start or finish time and job ID!";
    }

    header('Content-Type: application/json');
    echo json_encode($json, JSON_UNESCAPED_UNICODE);
}
