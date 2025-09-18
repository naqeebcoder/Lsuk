<?php
include '../../action.php';
// Update Job Late minutes ..
if (isset($_POST['add_late_minutes']) && isset($_POST['user_id'])) {
    $json = (object) null;
    if (isset($_POST['user_id']) && !empty($_POST['user_id'])) {
        $array_tables = array(1 => "interpreter", 2 => "telephone", 3 => "translation");
        $job_id = $_POST['job_id'];
        $job_type = isset($_POST['job_type']) ? $_POST['job_type'] : 1;
        $lateness_minutes = $_POST['minutes'];
        $idz = $obj->read_specific("GROUP_CONCAT(id) as idz", "job_late_minutes", "job_id=" . $job_id . " AND job_type=" . $job_type . " AND interpreter_id=" . $_POST['user_id'])["idz"];
        if (!empty($idz)) {
            $obj->delete("job_late_minutes", "id IN (" . $idz . ")");
        }
        $insert_array = [
            'job_id' => $job_id,
            'job_type' => $job_type,
            'minutes' => $lateness_minutes,
            'interpreter_id' => $_POST['user_id'],
            'created_date' => date('Y-m-d H:i:s')
        ];
        if ($lateness_minutes > 0) {
            $done = $obj->insert("job_late_minutes", $insert_array);
            if ($done) {
                $json->status = "success";
                $json->msg = "Minutes successfully added. Thank you";
                if ($job_type != 3) {// if not translation job
                    $row = $obj->read_specific("*", $array_tables[$job_type], "id=" . $job_id);
                    if ($row['intrpName']) {
                        $get_interpreter_data = $obj->read_specific("*", "interpreter_reg", "id=" . $row['intrpName']);
                        if ($job_type == 1) {
                            $lateness_duration = round($lateness_minutes / 60, 2);
                            $rate = $row['rateHour'] > 0 ? $row['rateHour'] : $get_interpreter_data['rph'];
                        } else {
                            $lateness_duration = round($lateness_minutes, 2);
                            $rate = $row['rateHour'] > 0 ? $row['rateHour'] : $get_interpreter_data['rpm'];
                        }
                        $deduction = $lateness_duration * $rate;
                        $obj->update($array_tables[$job_type], array('deduction' => $deduction, 'total_charges_interp' => ($row['total_charges_interp'] - $deduction)), "id=" . $job_id);
                    }
                }
            } else {
                $json->status = "failed";
                $json->msg = "Failed to add minutes. Please try again";
            }
        } else {
            $json->status = "failed";
            $json->msg = "Lateness minutes must be greater value then zero!";
        }
    } else {
        $json->status = "failed";
        $json->msg = "You must be login to perform this action!";
    }
    header('Content-Type: application/json');
    echo json_encode($json, JSON_UNESCAPED_UNICODE);
}
