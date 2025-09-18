<?php session_start();
//When review is submitted
if (isset($_POST['update_id'])) {
    include 'actions.php';
    $datetime = date("Y-m-d H:i:s");
    $table = 'interpreter';
    $edit_id = $_POST['update_id'];
    //Access actions
    $get_actions = explode(",", $obj->read_specific("GROUP_CONCAT(action_permissions.action_id) as actions", "action_permissions,route_actions", "action_permissions.action_id=route_actions.id AND route_actions.route_id=135 AND action_permissions.user_id=" . $_SESSION['userId'])['actions']);
    $action_auto_approve = $_SESSION['is_root'] == 1 || in_array(212, $get_actions);
    //Access actions
    $row = $obj->read_specific("*", "$table", "id=" . $edit_id);
    $UserName = $_SESSION['UserName'];
    if ($_POST['rate_already_set'] == 0) {
        $update_interpreter_array = array("company_rate_id" => $_POST['company_rate_id'], "company_rate_data" => $_POST['company_rate_data']);
        $obj->update($table, $update_interpreter_array, "id=" . $edit_id);
    }
    $c111 = $_POST['hoursWorkd'];
    $obj->editFun($table, $edit_id, 'hoursWorkd', $c111);
    $c112 = $_POST['rateHour'];
    $obj->editFun($table, $edit_id, 'rateHour', $c112);
    $c113 = $_POST['chargInterp'];
    $obj->editFun($table, $edit_id, 'chargInterp', $c113);
    $c114 = $_POST['dueDate'];
    $obj->editFun($table, $edit_id, 'dueDate', $c114);
    $c115 = $_POST['travelMile'];
    $obj->editFun($table, $edit_id, 'travelMile', $c115);
    $c116 = $_POST['rateMile'];
    $obj->editFun($table, $edit_id, 'rateMile', $c116);
    $c117 = $_POST['chargeTravel'];
    $obj->editFun($table, $edit_id, 'chargeTravel', $c117);
    $c118 = $_POST['travelCost'];
    $obj->editFun($table, $edit_id, 'travelCost', $c118);
    $c119 = $_POST['admnchargs'];
    $obj->editFun($table, $edit_id, 'admnchargs', $c119);
    $c120 = $_POST['otherCost'];
    $obj->editFun($table, $edit_id, 'otherCost', $c120);
    $c121 = $_POST['deduction'];
    $obj->editFun($table, $edit_id, 'deduction', $c121);
    $c122 = $_POST['ni_dedu'];
    $obj->editFun($table, $edit_id, 'ni_dedu', $c122);
    $c123 = $_POST['tax_dedu'];
    $obj->editFun($table, $edit_id, 'tax_dedu', $c123);
    $c124 = $_POST['totalChages'];
    $obj->editFun($table, $edit_id, 'total_charges_interp', $c124);
    $c125 = $_POST['travelTimeHour'];
    $obj->editFun($table, $edit_id, 'travelTimeHour', $c125);
    $c126 = $_POST['travelTimeRate'];
    $obj->editFun($table, $edit_id, 'travelTimeRate', $c126);
    $c127 = $_POST['chargeTravelTime'];
    $obj->editFun($table, $edit_id, 'chargeTravelTime', $c127);
    $int_vat = $_POST['int_vat'];
    $obj->editFun($table, $edit_id, 'int_vat', $int_vat);
    $vat_no_int = $_POST['vat_no_int'];
    $obj->editFun($table, $edit_id, 'vat_no_int', $vat_no_int);
    $c128 = $_POST['exp_remrks'];
    $obj->editFun($table, $edit_id, 'exp_remrks', $c128);
    if ($_SESSION['Temp'] == 1) {
        $obj->editFun($table, $edit_id, 'is_temp', 1);
    }
    if ($action_auto_approve && isset($_POST['is_temp'])) {
        $obj->editFun($table, $edit_id, 'is_temp', 0);
    }

    if ($_POST['action_approve_expenses'] == 1) {
        $approve_update_array = array("approved_flag" => 1, "approved_by" => $_SESSION['userId'], "approved_date" => $datetime);
        $obj->update($table, $approve_update_array, "id=" . $edit_id);
    }else{
        $obj->editFun($table, $edit_id, 'added_via',0); 
        $obj->editFun($table, $edit_id, 'hrsubmited', ucwords($UserName));
        $interp_hr_date = $_POST['interp_hr_date'];
        $obj->editFun($table, $edit_id, 'interp_hr_date', $interp_hr_date);
        $obj->editFun($table, $edit_id, 'edited_by', $UserName);
        $obj->editFun($table, $edit_id, 'edited_date', $datetime);
    }

    $index_mapping = array(
        'Worked.Hours' => 'hoursWorkd', 'RPH' => 'rateHour', 'Interp.Time Payment' => 'chargInterp', 'Travel.Time' => 'travelTimeHour', 'Travel.Time (RPH)' => 'travelTimeRate',
        'Travel.Time.Charges' => 'chargeTravelTime', 'Travel Mileage' => 'travelMile', 'Rate.Per.Mile' => 'rateMile', 'Mileage.Cost' => 'chargeTravel', 'Travel Cost' => 'travelCost',
        'Additional.Pay' => 'admnchargs', 'Other.Costs' => 'otherCost', 'Deduction' => 'deduction', 'NI.Deduction' => 'ni_dedu', 'Tax.Deduction' => 'tax_dedu', 'VAT' => 'int_vat',
        'VAT.No' => 'vat_no_int', 'Total.Charges' => 'total_charges_interp', 'Temporary' => 'is_temp', 'Remarks' => 'exp_remrks'
    );
    if ($_POST['action_approve_expenses'] == 1) {
        $index_mapping['Approved.Flag'] = 'approved_flag';
        $index_mapping['Approved By'] = 'approved_by';
        $index_mapping['DateTime'] = 'approved_date';
        $obj->insert("daily_logs", array("action_id" => 39, "user_id" => $_SESSION['userId'], "details" => "F2F Job ID: " . $edit_id));
    } else {
        $obj->insert("daily_logs", array("action_id" => 12, "user_id" => $_SESSION['userId'], "details" => "F2F Job ID: " . $edit_id));
    }

    $old_values = array();
    $new_values = array();
    $get_new_data = $obj->read_specific("*", "$table", "id=" . $edit_id);

    foreach ($index_mapping as $key => $value) {
        if (isset($get_new_data[$value])) {
            $old_values[$key] = $row[$value];
            $new_values[$key] = $get_new_data[$value];
        }
    }
    $obj->log_changes(json_encode($old_values), json_encode($new_values), $edit_id, $table, "update", $_SESSION['userId'], $UserName, ($_POST['action_approve_expenses'] == 1 ? "approved_job_f2f" : "interpreter_expenses_f2f"));
    //Below history function to be deleted
    // echo $obj->new_old_table('hist_interpreter', $table, $edit_id);
}
