<?php
session_start();
$datetime = date("Y-m-d H:i:s");

if (isset($_POST['btn_submit_lateness']) && (isset($_POST['job_id']) || isset($_POST['job_type']))) {
    include '../actions.php';
    $jobNote = '';
    $update_job_lateness = false;
    $array_tables = array(1 => "interpreter", 2 => "telephone", 3 => "translation");
    $job_id = $_POST['job_id'];
    $job_type = $_POST['job_type'];
    $lateness_minutes = $_POST['lateness_minutes'];
    $_SESSION['returned_message'] = "<div class='alert alert-danger alert-dismissible text-center'><a href='javascript:void(0)' class='close' data-dismiss='alert' aria-label='close'>&times;</a>Failed to update lateness record against this job! Please try again</div>";
    if ($_POST['lateness_id']) {
        if (isset($_POST['remove_lateness'])) {
            $done = $obj->delete("job_late_minutes", "id=" . $_POST['lateness_id']);
            $jobNote = '<span class="bg-danger">Lattness Deleted </span> <br>';
            $action_label = "deleted";
            if ($job_type != 3) { // if not translation job
                $row = $obj->read_specific("*", $array_tables[$job_type], "id=" . $job_id);
                if ($row['deduction'] > 0 && $row['intrpName']) {
                    $get_interpreter_data = $obj->read_specific("*", "interpreter_reg", "id=" . $row['intrpName']);
                    if ($job_type == 1) {
                        $lateness_duration = round($lateness_minutes / 60, 2);
                        $rate = $row['rateHour'] > 0 ? $row['rateHour'] : $get_interpreter_data['rph'];
                    } else {
                        $lateness_duration = round($lateness_minutes, 2);
                        $rate = $row['rateHour'] > 0 ? $row['rateHour'] : $get_interpreter_data['rpm'];
                    }
                    $deduction = $lateness_duration * $rate;
                    $obj->update($array_tables[$job_type], array('deduction' => 0, 'total_charges_interp' => ($row['total_charges_interp'] + $deduction)), "id=" . $job_id);
                }
            }
        } else {
            $done = $obj->update("job_late_minutes", array("minutes" => $lateness_minutes, "created_by" => $_POST['lateness_created_by'], "reason" => trim($_POST['lateness_reason']), "updated_by" => $_SESSION['userId'], "updated_date" => $datetime), "id=" . $_POST['lateness_id']);
            $action_label = "updated";
            $jobNote = '<span class="bg-info">Lattness updated</span> <br>';
            if ($done) {
                $update_job_lateness = true;
            }
        }
        if ($done) {
            $_SESSION['returned_message'] = "<div class='alert alert-success alert-dismissible text-center'><a href='javascript:void(0)' class='close' data-dismiss='alert' aria-label='close'>&times;</a>Interpreter lateness record has been $action_label against this job! Thank you</div>";
        }
    } else {
        if ($lateness_minutes) {
            $done = $obj->insert("job_late_minutes", array("job_type" => $job_type, "job_id" => $job_id, "interpreter_id" => $_POST['interpreter_id'], "minutes" => $lateness_minutes, "created_by" => $_POST['lateness_created_by'], "reason" => trim($_POST['lateness_reason']), "added_by" => $_SESSION['userId'], "created_date" => $datetime));
            if ($done) {
                $update_job_lateness = true;
                $jobNote = '<span class="bg-success">Lattness Added </span> <br>';
                $_SESSION['returned_message'] = "<div class='alert alert-success alert-dismissible text-center'><a href='javascript:void(0)' class='close' data-dismiss='alert' aria-label='close'>&times;</a>Interpreter lateness record has been added against this job! Thank you</div>";
            }
        }
    }
    if ($update_job_lateness && $job_type != 3) {// if not translation job
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

        $source_labels = array(
            0 => 'Client LSUK App',
            1 => 'Interpreter informed LSUK about lateness',
            2 => 'LSUK phoned interpreter for reason of lateness',
            3 => 'Other'
        );

        $created_by_label = isset($source_labels[$_POST['lateness_created_by']]) ? $source_labels[$_POST['lateness_created_by']] : 'Unknown';
        $jobNote .= "Lateness minutes: {$lateness_minutes}<br>" .
                "Lateness communicated by: {$created_by_label}<br>" .
                "Reason: " . $_POST['lateness_reason']. "<br>" .
                "Added by: {$_SESSION['UserName']}<br>" .
                "Date time: {$datetime}" .
                "<br><span class='rate_change' style='display:none' data-old='1' data-new='1'>this is inside span</span>";

        $obj->insert("jobnotes", array(
            "dated" => date("Y-m-d"),
            "jobNote" => $obj->con->real_escape_string($jobNote),
            "notesread" => $_POST['notedfor'],
            "tbl" => $array_tables[$job_type],
            "fid" => $job_id,
            "time" => $datetime,
            "submitted" => $_SESSION['UserName']
        ));
    header('Location: ' . $_POST['redirect_url']);
}