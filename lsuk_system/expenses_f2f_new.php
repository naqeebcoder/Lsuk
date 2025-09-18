<?php if (session_id() == '' || !isset($_SESSION)) {
    session_start();
}
include 'actions.php';
$table = 'interpreter';
$allowed_type_idz = "72,84,119,160,167,175";
$extensions = array("jpg", "jpeg", "png", "bmp", "webp");
//Check if user has current action allowed
if ($_SESSION['is_root'] == 0) {
    $get_page_access = $obj->read_specific("id", "action_permissions", "user_id=" . $_SESSION['userId'] . " AND action_id IN (" . $allowed_type_idz . ")")['id'];
    if (empty($get_page_access)) {
      die("<center><h2 class='text-center text-danger'>You do not have access to <u>Update expenses</u> action for jobs!<br>Kindly contact admin for further process.</h2></center>");
    }
}
//Action for update company expenses
$company_expenses_action_idz = "168,169,170,185";
$action_update_company_expenses = $obj->read_specific("id", "action_permissions", "user_id=" . $_SESSION['userId'] . " AND action_id IN (" . $company_expenses_action_idz . ")")['id'];
$action_update_company_expenses = $_SESSION['is_root'] == 1 || !empty($action_update_company_expenses);
$array_added_via = array(0 => "<span class='label label-info'>LSUK Admin</span>", 1 => "<span class='label label-success'>Mobile App</span>", 2 => "<span class='label label-warning'>Interpreter Portal</span>", 3 => "<span class='label label-primary'>Android App</span>", 4 => "<span class='label label-danger'>iOS App</span>");
$update_id = @$_GET['update_id'];
$row = $obj->read_specific("$table.*,interpreter_reg.name,interpreter_reg.code,interpreter_reg.rph,interpreter_reg.ratetravelexpmile,interpreter_reg.ratetravelworkmile", "$table,interpreter_reg", "$table.intrpName=interpreter_reg.id AND $table.id=" . $update_id);
if ($_SESSION['is_root'] == 1) {
    $managment = 1;
} else {
    $managment = 0;
}
//Access actions
$get_actions = explode(",", $obj->read_specific("GROUP_CONCAT(action_permissions.action_id) as actions", "action_permissions,route_actions", "action_permissions.action_id=route_actions.id AND route_actions.route_id=135 AND action_permissions.user_id=" . $_SESSION['userId'])['actions']);
$action_auto_approve = $_SESSION['is_root'] == 1 || in_array(212, $get_actions); ?>
<script>
    function refreshParent() {
        window.opener.location.reload();
    }
</script>
<?php //If company expenses updated
if (isset($_POST['submit'])) {
    if ($_POST['rate_already_set'] == 0) {
        $update_company_array = array("company_rate_id" => $_POST['company_rate_id'], "company_rate_data" => $_POST['company_rate_data']);
        $obj->update($table, $update_company_array, "id=" . $update_id);
    }
    $C_hoursWorkd = $_POST['C_hoursWorkd'];
    $obj->editFun($table, $update_id, 'C_hoursWorkd', $C_hoursWorkd);
    $C_rateHour = $_POST['C_rateHour'];
    $obj->editFun($table, $update_id, 'C_rateHour', $C_rateHour);
    $C_chargInterp = $_POST['C_chargInterp'];
    $obj->editFun($table, $update_id, 'C_chargInterp', $C_chargInterp);
    $C_travelTimeHour = $_POST['C_travelTimeHour'];
    $obj->editFun($table, $update_id, 'C_travelTimeHour', $C_travelTimeHour);
    $C_travelTimeRate = $_POST['C_travelTimeRate'];
    $obj->editFun($table, $update_id, 'C_travelTimeRate', $C_travelTimeRate);
    $C_chargeTravelTime = $_POST['C_chargeTravelTime'];
    $obj->editFun($table, $update_id, 'C_chargeTravelTime', $C_chargeTravelTime);
    $C_cur_vat = $_POST['C_cur_vat'];
    $obj->editFun($table, $update_id, 'cur_vat', $C_cur_vat);
    $vat_no_post = $_POST['C_vat_no_comp'];
    $obj->editFun($table, $update_id, 'vat_no_comp', $vat_no_post);
    $C_travelMile = $_POST['C_travelMile'];
    $obj->editFun($table, $update_id, 'C_travelMile', $C_travelMile);
    $C_chargeTravel = $_POST['C_chargeTravel'];
    $obj->editFun($table, $update_id, 'C_chargeTravel', $C_chargeTravel);
    $C_rateMile = $_POST['C_rateMile'];
    $obj->editFun($table, $update_id, 'C_rateMile', $C_rateMile);
    $C_travelCost = $_POST['C_travelCost'];
    $obj->editFun($table, $update_id, 'C_travelCost', $C_travelCost);
    $C_admnchargs = $_POST['C_admnchargs'];
    $obj->editFun($table, $update_id, 'C_admnchargs', $C_admnchargs);
    $C_otherCost = $_POST['C_otherCost'];
    $obj->editFun($table, $update_id, 'C_otherCost', $C_otherCost);
    $obj->editFun($table, $update_id, 'C_deduction', $_POST['C_deduction']);
    $obj->editFun($table, $update_id, 'C_otherexpns', $_POST['C_otherexpns']);
    $obj->editFun($table, $update_id, 'C_comments', $_POST['C_comments']);
    $total1 = $C_hoursWorkd * $C_rateHour;
    $total2 = $C_travelTimeHour * $C_travelTimeRate;
    $total3 = $C_travelMile * $C_rateMile;
    $obj->editFun($table, $update_id, 'total_charges_comp', $total1 + $total2 + $total3 + $C_admnchargs);
    $obj->editFun($table, $update_id, 'comp_hrsubmited', ucwords($_SESSION['UserName']));
    $obj->editFun($table, $update_id, 'comp_hr_date', $misc->sys_date_db());
    if ($_SESSION['Temp'] == 1) {
        $obj->editFun($table, $update_id, 'is_temp', 1);
    }
    if ($action_auto_approve && isset($_POST['is_temp'])) {
        $obj->editFun($table, $update_id, 'is_temp', 0);
    }
    $obj->editFun($table, $update_id, 'edited_by', $_SESSION['UserName']);
    $obj->editFun($table, $update_id, 'edited_date', date("Y-m-d H:i:s"));
    $index_mapping = array(
        'Worked.Hours' => 'C_hoursWorkd', 'RPH' => 'C_rateHour', 'Interp.Time Payment' => 'C_chargInterp', 'Travel.Time' => 'C_travelTimeHour', 'Travel.Time (RPH)' => 'C_travelTimeRate',
        'Travel.Time.Charges' => 'C_chargeTravelTime', 'Travel Mileage' => 'C_travelMile', 'Rate.Per.Mile' => 'C_rateMile', 'Mileage.Cost' => 'C_chargeTravel', 'Travel Cost' => 'C_travelCost',
        'Additional.Pay' => 'C_admnchargs', 'Other.Costs' => 'C_otherCost', 'Deduction' => 'C_deduction', 'VAT' => 'cur_vat', 'VAT.No' => 'vat_no_comp', 'Other.Expenses' => 'C_otherexpns', 
        'Total.Charges' => 'total_charges_comp', 'Temporary' => 'is_temp', 'Remarks' => 'C_comments'
    );
    
    $old_values = array();
    $new_values = array();
    $get_new_data = $obj->read_specific("*", "$table", "id=" . $update_id);
    
    foreach ($index_mapping as $key => $value) {
        if (isset($get_new_data[$value])) {
            $old_values[$key] = $row[$value];
            $new_values[$key] = $get_new_data[$value];
        }
    }
    $obj->log_changes(json_encode($old_values), json_encode($new_values), $update_id, $table, "update", $_SESSION['userId'], $_SESSION['UserName'], "company_expenses_f2f_new");
    //Below history function to be deleted
    // $obj->new_old_table('hist_' . $table, $table, $update_id);
    $obj->insert("daily_logs", array("action_id" => 13, "user_id" => $_SESSION['userId'], "details" => "F2F Job ID: " . $update_id));
    echo '<script>alert("Company expenses for job have been updated.");</script>';
    if ($managment == 0) {
        echo '<script>window.onunload = refreshParent;</script>';
    }
}
$row = $obj->read_specific("$table.*,interpreter_reg.name,interpreter_reg.code,interpreter_reg.rph,interpreter_reg.ratetravelexpmile,interpreter_reg.ratetravelworkmile", "$table,interpreter_reg", "$table.intrpName=interpreter_reg.id AND $table.id=" . $update_id);
$row_lateness = $obj->read_specific("*", "job_late_minutes", "job_id=" . $row['id'] . " AND job_type=1 AND interpreter_id=" . $row['intrpName']);
$assignDate = $row['assignDate'];
$assignTime = $row['assignTime'];
$assignDur = $row['assignDur'];
$intrpName = $row['intrpName'];
$interp_name = $row['name'];
$code = $row['code'];
$bookinType = $row['bookinType'];
$company_rate_id = $row['company_rate_id'];
$company_rate_data = $row['company_rate_data'];
$orgName = $row['orgName'];
$order_company_id = $row['order_company_id'];
$find_language_type = $obj->read_specific("language_type", "lang", "lang='".$row['source']."'")['language_type'];
if (empty($order_company_id)){
  $get_company_data = $obj->read_specific("comp_reg.id,comp_type.company_type_id as type_id", "comp_reg,comp_type", "comp_reg.type_id=comp_type.id AND comp_reg.abrv='".$orgName."'");
  $order_company_id = $get_company_data['id'];
  $find_company_type = $get_company_data['type_id'];
} else {
  if (!empty($row['company_rate_data'])) {
    $extracted_data = (array) json_decode($row['company_rate_data']);
    $find_company_type = $extracted_data['company_type_id'];
  } else {
    $get_company_data = $obj->read_specific("comp_reg.id,comp_type.company_type_id as type_id", "comp_reg,comp_type", "comp_reg.type_id=comp_type.id AND comp_reg.id=" . $order_company_id);
    $find_company_type = $get_company_data['type_id'];
  }
}

if (empty($row['company_rate_data'])) {
    $ch = curl_init();
      $postData = [
        "find_company_rates"  => 1,
        "find_order_type"     => 1,
        "find_company_id"     => $order_company_id,
        "find_company_type"     => $find_company_type,
        "find_assignment_time"     => $row['assignTime'],
        "find_assignment_date"     => $row['assignDate'],
        "find_language_type"     => $find_language_type,
        "find_booked_time"     => $row['bookedtime'],
        "find_booked_date"     => $row['bookeddate']
      ];
    curl_setopt($ch, CURLOPT_URL, actionsClass::URL . "/lsuk_system/ajax_add_interp_data.php");
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response_data = curl_exec($ch);
    curl_close($ch);
    $json_data = json_decode($response_data, true);
    $extracted_data = !empty($json_data['company_rates'][0]) ? $json_data['company_rates'][0] : array();
}
//Interpreter Rates
if (!empty($row['interpreter_rate_data'])) {
    $extracted_data_int = (array) json_decode($row['interpreter_rate_data']);
} else {
    $ch_int = curl_init();
    $postData = [
    "find_interpreter_rates"  => 1,
    "find_order_type"     => 1,
    "find_interpreter_id"     => $row['intrpName'],
    "find_assignment_time"     => $row['assignTime'],
    "find_assignment_date"     => $row['assignDate'],
    "find_language_type"     => $find_language_type,
    "find_booked_time"     => $row['bookedtime'],
    "find_booked_date"     => $row['bookeddate']
    ];
    curl_setopt($ch_int, CURLOPT_URL, actionsClass::URL . "/lsuk_system/ajax_add_interp_data.php");
    curl_setopt($ch_int, CURLOPT_POST, true);
    curl_setopt($ch_int, CURLOPT_POSTFIELDS, $postData);
    curl_setopt($ch_int, CURLOPT_RETURNTRANSFER, true);
    $response_data_int = curl_exec($ch_int);
    curl_close($ch_int);
    $json_data_int = json_decode($response_data_int, true);
    $extracted_data_int = !empty($json_data_int['interpreter_rates'][0]) ? $json_data_int['interpreter_rates'][0] : array();
}

$input_hours_int = $assignDur/60 < $extracted_data_int['minimum_charge_interpreting'] ? $extracted_data_int['minimum_charge_interpreting'] : $assignDur/60;
$hoursWorkd = $misc->calculate_client_hours($input_hours_int, $extracted_data_int['incremental_charge_f2f']);

$selected_rate_title = !empty($extracted_data) ? $extracted_data['title'] : $bookinType;

$rateHour = $row['hoursWorkd'] || $row['rateHour'] ? $row['rateHour'] : $extracted_data_int['rate_value_f2f'];
$chargInterp = $row['hoursWorkd'] || $row['chargInterp'] ? $row['chargInterp'] : ($hoursWorkd * $rateHour);

$hrsubmited = $row['hrsubmited'];
$travelMile = $row['travelMile'] ?: 0;
$rateMile = $row['hoursWorkd'] || $row['rateMile'] ? $row['rateMile'] : $extracted_data_int['mileage_charge_rate'];
$chargeTravel = $row['chargeTravel'] ?: 0;
$travelCost = $row['travelCost'] ?: 0;
$otherCost = $row['otherCost'] ?: 0;
$travelTimeHour = $row['travelTimeHour'] ?: 0;
$travelTimeRate = $row['hoursWorkd'] || $row['travelTimeRate'] ? $row['travelTimeRate'] : $extracted_data_int['travel_time_rate'];
$chargeTravelTime = $row['chargeTravelTime'] ?: 0;
$dueDate = $row['dueDate'];
$tAmount = $row['tAmount'] ?: 0;
$admnchargs = $row['hoursWorkd'] || $row['admnchargs'] ? $row['admnchargs'] : $extracted_data_int['admin_charge_rate'];
$deduction = $row['deduction'] ?: 0;
$total_charges_interp = $row['total_charges_interp'] ?: 0;
$exp_remrks = $row['exp_remrks'];
$ni_dedu = $row['ni_dedu'] ?: 0;
$tax_dedu = $row['tax_dedu'] ?: 0;
$int_vat = $row['int_vat'];
$vat_no_int = $row['vat_no_int'];
$chk_hoursWorkd = $row['hoursWorkd'] ?: 0;
if ($row['C_hoursWorkd']) {
    $C_hoursWorkd = $row['C_hoursWorkd'];
} else {
    if ($row['hoursWorkd']) {
        $input_hours = $row['hoursWorkd'] < $extracted_data['minimum_charge_interpreting'] ? $extracted_data['minimum_charge_interpreting'] : $row['hoursWorkd'];
        $C_hoursWorkd = $misc->calculate_client_hours($input_hours, $extracted_data['incremental_charge_f2f']);
    } else {
        $input_hours = ($assignDur / 60) < $extracted_data['minimum_charge_interpreting'] ? $extracted_data['minimum_charge_interpreting'] : ($assignDur / 60);
        $C_hoursWorkd = $misc->calculate_client_hours($input_hours, $extracted_data['incremental_charge_f2f']);
        $C_hoursWorkd = round($C_hoursWorkd, 2);
    }
}

$extracted_rate_value = $extracted_data['rate_value_f2f'] ?: 0;
$extracted_admin_charge = $extracted_data['admin_charge']?:0;
$extracted_admin_charge_rate = $extracted_data['admin_charge_rate'] ?: 0;
$extracted_travel_time_charges = $extracted_data['travel_time_charges']?:0;
$extracted_travel_time_rate = $extracted_data['travel_time_rate'] ?: 0;
$extracted_mileage_charge = $extracted_data['mileage_charge']?:0;
$extracted_mileage_charge_rate = $extracted_data['mileage_charge_rate'] ?: 0;
$extracted_parking_charges = $extracted_data['parking_charges'] ?: 0;

$C_rateHour = $row['C_hoursWorkd'] || $row['C_rateHour'] ? $row['C_rateHour'] : $extracted_rate_value;
$C_chargInterp = $row['C_hoursWorkd'] || $row['C_chargInterp'] ? $row['C_chargInterp'] : ($C_hoursWorkd * $C_rateHour);
$C_travelMile = $row['C_travelMile'] ?: 0;
if ($row['C_hoursWorkd'] || $row['C_rateMile']) {
    $C_rateMile =  $row['C_rateMile'] ? $row['C_rateMile'] : 0;
} else {
    $C_rateMile = $extracted_mileage_charge == 1 ? $extracted_mileage_charge_rate : 0;
}
$C_chargeTravel = $row['C_chargeTravel'] ?: 0;
$C_travelCost = $row['C_travelCost'] ?: 0;
$C_otherCost = $row['C_otherCost'] ?: 0;
$C_travelTimeHour = $row['C_travelTimeHour'];
if ($row['C_hoursWorkd'] || $row['C_travelTimeRate']) {
    $C_travelTimeRate =  $row['C_travelTimeRate'] ? $row['C_travelTimeRate'] : 0;
} else {
    $C_travelTimeRate = $extracted_travel_time_charges == 1 ? $extracted_travel_time_rate : 0;
}

$C_chargeTravelTime = $row['C_chargeTravelTime'] ?: 0;
$C_deduction = $row['C_deduction'] ?: 0;
$C_admnchargs = $row['C_hoursWorkd'] != "" || $row['C_admnchargs'] != "" ? $row['C_admnchargs'] : $extracted_admin_charge_rate;
$cur_vat = $row['cur_vat'] ?: 0;
$C_otherexpns = $row['C_otherexpns'] ?: 0;
$porder = $row['porder'];
$C_comments = $row['C_comments'];
$vat_no_comp = $row['vat_no_comp'];
$interp_rph = $row['rph'] ?: 0;
$interp_rte = $row['ratetravelexpmile'] ?: 0;
$interp_rtw = $row['ratetravelworkmile'] ?: 0;

//Get company requirements & rates
$admin_ch = $extracted_admin_charge;
$tr_time = $extracted_travel_time_charges;
$admin_rate = $row['C_hoursWorkd'] != "" || $row['C_admnchargs'] != "" ? $row['C_admnchargs'] : $extracted_admin_charge_rate;
$total_charges_comp = $row['C_hoursWorkd'] || $row['total_charges_comp'] ? $row['total_charges_comp'] : ($C_chargInterp + $admin_rate);
$reqs = '';
if ($admin_ch == 1) {
    $reqs .= 'Admin Charge ';
}
if ($tr_time == 1) {
    $reqs .= ', Travel Time ';
}
if ($extracted_mileage_charge == 1) {
    $reqs .= ', Travel Mileage ';
}
if ($extracted_parking_charges == 1) {
    $reqs .= ', Parking Charges ';
}
$reqs .= ' required';
if ($admin_ch == 0 && $tr_time == 0 && $extracted_mileage_charge == 0 && $extracted_parking_charges == 0) {
    $reqs = 'No requirements !';
}
if ($assignDur > 60) {
    $hours = $assignDur / 60;
    if (floor($hours) > 1) {
        $hr = "hours";
    } else {
        $hr = "hour";
    }
    $mins = $assignDur % 60;
    if ($mins == 00) {
        $get_dur = sprintf("%2d $hr", $hours);
    } else {
        $get_dur = sprintf("%2d $hr %02d minutes", $hours, $mins);
    }
} else if ($assignDur == 60) {
    $get_dur = "1 Hour";
} else {
    $get_dur = $assignDur . " minutes";
}
if ($misc->IsDatedNull($dueDate)) {
    $dateAssignStart = date_create($assignDate);
    $assignDay = date_format($dateAssignStart, 'd');
    $assignMonth = date_format($dateAssignStart, 'm');
    $assignYear = date_format($dateAssignStart, 'Y');
    if ($assignDay >= 11) {
        $assignMonth++;
        if ($assignMonth > 12) {
            $assignMonth = 1;
            $assignYear++;
        }
    }

    //get last day of week
    $nextMonth = $assignMonth;
    $nextYear = $assignYear;

    $nextMonth++;
    if ($nextMonth > 12) {
        $nextMonth = 1;
        $nextYear++;
    }

    $dateNext = date_create("$nextYear-$nextMonth-1");
    $dateNextStr = date_format($dateNext, "Y-m-d");

    $dueLastStr = $misc->add_in_date($dateNextStr, -1);

    $dateLast = date_create($dueLastStr);
    $dueDayStr = date_format($dateLast, 'd');

    $dateDue = date_create("$assignYear-$assignMonth-$dueDayStr");
    $dueDate = date_format($dateDue, "Y-m-d");
}
if (date('Y-m-d H:i', strtotime($assignDate . ' ' . $assignTime)) > date('Y-m-d H:i')) {
    $problem_hours = 1;
    $problem_msg = 'Assignment Date & Time: <b class="text-danger">' . $assignDate . ' ' . $assignTime . '</b><br><br>This job is not completed yet! Thank you';
} else if ($row['deleted_flag'] == 1 || $row['order_cancel_flag'] == 1) {
    $problem_hours = 1;
    $problem_msg = 'This job is in processing mode [DELETED OR CANCELLED]! Thank you';
} else {
    $problem_hours = 0;
    $problem_msg = '';
} ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
    <title>Client Expenses - Face to Face Interpreting</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <link rel="stylesheet" type="text/css" href="css/bootstrap.css" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.3.0/css/font-awesome.css" rel="stylesheet" type='text/css'>
    <style>
        .cls_danger {
            background: red;
            border: 2px solid black;
            color: white;
            font-weight: bold;
        }

        .nav-tabs>li.active>a,
        .nav-tabs>li.active>a:focus,
        .nav-tabs>li.active>a:hover {
            color: #fff;
            background-color: #337ab7;
            font-weight: bold;
            border: 1px solid #000;
        }

        .nav-tabs>li>a {
            border: 1px solid #c5b7b7;
        }
    </style>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/js/bootstrap.min.js"></script>
    <script>
        function calcInterp() {
            var hoursWorkd = parseFloat(document.getElementById('hoursWorkd').value);
            var rateHour = parseFloat(document.getElementById('rateHour').value);
            var chargInterp = document.getElementById('chargInterp');
            var x = rateHour * hoursWorkd;
            chargInterp.value = x.toFixed(2);

            var travelMile = parseFloat(document.getElementById('travelMile').value);
            var rateMile = parseFloat(document.getElementById('rateMile').value);
            var chargeTravel = document.getElementById('chargeTravel');
            var y = travelMile * rateMile;
            chargeTravel.value = y.toFixed(2);

            var travelTimeHour = parseFloat(document.getElementById('travelTimeHour').value);
            var travelTimeRate = parseFloat(document.getElementById('travelTimeRate').value);
            var chargeTravelTime = document.getElementById('chargeTravelTime');
            var int_vat = document.getElementById('int_vat').value;
            var z = travelTimeHour * travelTimeRate;
            chargeTravelTime.value = z.toFixed(2);
            var otherCost = parseFloat(document.getElementById('otherCost').value);
            var deduction = parseFloat(document.getElementById('deduction').value);
            var admnchargs = parseFloat(document.getElementById('admnchargs').value);
            var travelCost = parseFloat(document.getElementById('travelCost').value);

            totalChages.value = (parseFloat(x + y + z + travelCost + otherCost + admnchargs) - parseFloat(deduction)).toFixed(2);
        }

        function fun_vat_no() {
            var int_vat = document.getElementById("int_vat").value;
            var vat_no_int = document.getElementById("vat_no_int");
            var div_vat_no = document.getElementById("div_vat_no");
            if (!isNaN(int_vat) && int_vat != 0) {
                div_vat_no.style.display = 'inline';
                vat_no_int.setAttribute("required", "required");
            } else {
                div_vat_no.style.display = 'none';
                vat_no_int.removeAttribute("required", "required");
            }
        }

        function C_calcInterp() {
            var C_admnchargs = parseFloat(document.getElementById('C_admnchargs').value);
            var C_hoursWorkd = parseFloat(document.getElementById('C_hoursWorkd').value);
            var C_rateHour = parseFloat(document.getElementById('C_rateHour').value);
            var C_chargInterp = document.getElementById('C_chargInterp');
            var x = C_rateHour * C_hoursWorkd;
            C_chargInterp.value = x;

            var C_travelMile = parseFloat(document.getElementById('C_travelMile').value);
            var C_rateMile = parseFloat(document.getElementById('C_rateMile').value);
            var C_chargeTravel = document.getElementById('C_chargeTravel');
            var y = C_travelMile * C_rateMile;
            C_chargeTravel.value = y;

            var C_travelTimeHour = parseFloat(document.getElementById('C_travelTimeHour').value);
            var C_travelTimeRate = parseFloat(document.getElementById('C_travelTimeRate').value);
            var C_chargeTravelTime = document.getElementById('C_chargeTravelTime');
            var C_travelCost = parseFloat(document.getElementById('C_travelCost').value);
            var z = C_travelTimeHour * C_travelTimeRate;
            C_chargeTravelTime.value = z;

            var C_otherCost = parseFloat(document.getElementById('C_otherCost').value);
            var C_deduction = parseFloat(document.getElementById('C_deduction').value);

            C_otherexpns.value = parseFloat(C_otherCost + C_travelCost);
            C_totalChages.value = parseFloat(x + y + z) - parseFloat(C_deduction) + C_admnchargs;
        }

        function checkDec(el) {
            var ex = /^[0-9]+\.?[0-9]*$/;
            if (ex.test(el.value) == false) {
                el.value = 0;
                el.select();
                calcInterp();
                C_calcInterp();
                int_hours();
                company_hours();
            }
        }

        function C_fun_vat_no() {
            var C_cur_vat = document.getElementById("C_cur_vat").value;
            var C_vat_no_comp = document.getElementById("C_vat_no_comp");
            var C_div_vat_no = document.getElementById("C_div_vat_no");
            if (!isNaN(C_cur_vat) && C_cur_vat != 0) {
                C_div_vat_no.style.display = 'block';
                C_vat_no_comp.setAttribute("required", "required");
            } else {
                C_div_vat_no.style.display = 'none';
                C_vat_no_comp.removeAttribute("required", "required");
            }
        }

        function int_hours() {
            var actual_dur = parseFloat('<?php echo round($hoursWorkd, 2); ?>');
            if (parseFloat($('#hoursWorkd').val()) < actual_dur) {
                $('#hoursWorkd').addClass('cls_danger');
                $('#hoursWorkd').attr('title', 'Hours Worked value must be atleast ' + actual_dur);
                $('#btn_submit_expense').attr("disabled", "disabled");
            } else {
                $('#hoursWorkd').removeClass('cls_danger');
                $('#btn_submit_expense').removeAttr("disabled");
            }
        }

        function company_hours() {
            var C_actual_hours = parseFloat('<?php echo $C_hoursWorkd; ?>');
            if (parseFloat($('#C_hoursWorkd').val()) < C_actual_hours) {
                $('#C_hoursWorkd').addClass('cls_danger');
                $('#C_hoursWorkd').attr('title', 'Hours Worked value must be atleast ' + C_actual_hours);
                $('#btn_company_expense').attr("disabled", "disabled");
            } else {
                $('#C_hoursWorkd').removeClass('cls_danger');
                $('#btn_company_expense').removeAttr("disabled");
            }
        }

        //window function
        function MM_openBrWindow(theURL, winName, features) {
            window.open(theURL, winName, features);
        }
        $(document).ready(function(){
            calcInterp();
            C_calcInterp();
        });
    </script>
</head>

<body class="container-fluid">
    <?php if ($problem_hours == 1) { ?>
        <center><br><br>
            <h3><?php echo isset($problem_msg) && !empty($problem_msg) ? $problem_msg : ''; ?></h3>
            <br><br><a class="btn btn-primary" href="javascript:void(0)" onclick="window.close();"><i class="glyphicon glyphicon-arrow-left"></i> Go Back</a>
        </center>
    <?php } else { ?>
        <table class="table table-bordered">
            <tbody>
                <tr>
                    <td width="25%">Assignment Date : <?php echo $assignDate . ' ' . $assignTime; ?></td>
                    <td>Duration : <?php echo $get_dur; ?></td>
                    <td>Timesheet Status : <?php echo $row['hoursWorkd'] == 0 ? 'Not Filled Yet !' : $row['hoursWorkd'] . ' hour(s)'; ?></td>
                </tr>
                <tr>
                    <td>Timesheet Updated VIA : <?php echo $row['hoursWorkd'] == 0 ? 'Not Updated' : $array_added_via[$row['added_via']];
                        $get_other_files=$obj->read_all("id,file_name","job_files","tbl='interpreter' AND order_id=".$row['id']." AND file_type='timesheet'");
                        echo !is_null($row['int_sign_date']) || !is_null($row['cl_sign_date']) || $row['is_parking'] == 1 || !empty($row['time_sheet']) || $get_other_files->num_rows>0 ? "<a data-toggle='modal' data-target='#attachments_modal' title='Click to view signatures' class='btn btn-sm btn-info'>View Uploaded Attachments</a>" : "";?></td>
                    <td><?php $row['wt_tm'] = is_null($row['wt_tm']) || $row['wt_tm'] == '1001-01-01 00:00:00' ? "Nil" : $row['wt_tm'];
                        $row['st_tm'] = is_null($row['st_tm']) || $row['st_tm'] == '1001-01-01 00:00:00' ? "Nil" : $row['st_tm'];
                        $row['fn_tm'] = is_null($row['fn_tm']) || $row['fn_tm'] == '1001-01-01 00:00:00' ? "Nil" : $row['fn_tm'];
                        echo "Waiting Time : " . $row['wt_tm'] . "<br>Starting Time : " . $row['st_tm'] . "<br>Finished Time : " . $row['fn_tm'];
                        ?>
                    </td>
                    <td>
                        <?php $expected_start = date($assignDate . ' ' . substr($assignTime,0,5));
                        echo "Expected Start Time: " . $expected_start . "<br>Expected End Time: " . date("Y-m-d H:i", strtotime("+" . $row['assignDur'] . " minutes", strtotime($expected_start)));?>
                        <br>Requirements : <?php echo $reqs; ?>
                    </td>
                </tr>
            </tbody>
        </table>
        <div id="attachments_modal" class="modal fade" role="dialog" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                <div class="modal-header alert-info">
                    <button type="button" class="close" data-dismiss="modal">×</button>
                    <h4 class="modal-title">Attachments with Job</h4>
                </div>
                <div class="modal-body">
                    <div class="container-fluid">
                        <ul class="nav nav-tabs">
                            <li class="active"><a data-toggle="tab" href="#tab_interpreter_signature">Interpreter Signature</a></li>
                            <li><a data-toggle="tab" href="#tab_client_signature">Client Signature</a></li>
                            <li><a data-toggle="tab" href="#tab_parking_attachments">Parking Expenses</a></li>
                            <li><a data-toggle="tab" href="#tab_other_attachments">Public Transport Attachments</a></li>
                            <li><a data-toggle="tab" href="#tab_uploaded_timesheet">Timesheet Proof</a></li>
                        </ul>
                        <div class="tab-content">
                            <div id="tab_interpreter_signature" class="tab-pane fade in active">
                                <h3>Interpreter Signature</h3>
                                <p><?php if ($row['added_via'] == 2) {// if web portal
                                        echo "<span class='text-danger text-center'>Interpreter signature not applicable from portal!</span>";
                                    } else {
                                        echo !is_null($row['int_sign_date']) ? "Signed on : " . $row['int_sign_date'] . "<br>" : "";
                                        if (!empty($row['int_sig']) && $row['int_sig'] != 'i_default.png') {
                                            echo file_exists('../file_folder/interpreter_signatures/' . $row['int_sig']) ? "<img width='35%' src='../file_folder/interpreter_signatures/" . $row['int_sig'] . "' title='Interpreter signature' class='img-responsive'>" : "<span class='text-danger text-center'>Interpreter signature not found!</span>";
                                        } else {
                                            echo "<span class='text-danger text-center'>Interpreter signature not found!</span>";
                                        }
                                    } ?>
                                </p>
                            </div>
                            <div id="tab_client_signature" class="tab-pane fade">
                                <h3>Client Signature</h3>
                                <p><?php if ($row['added_via'] == 2) {// if web portal
                                        echo "<span class='text-danger text-center'>Client signature not applicable from portal!</span>";
                                    } else {
                                        echo !is_null($row['cl_sign_date']) ? "Signed on : " . $row['cl_sign_date'] . "<br>" : "";
                                        if (!empty($row['cl_sig']) && $row['cl_sig'] != 'i_default.png') {
                                            echo file_exists('../file_folder/client_signatures/' . $row['cl_sig']) ? "<img width='35%' src='../file_folder/client_signatures/" . $row['cl_sig'] . "' title='Client signature' class='img-responsive'>" : "<span class='text-danger text-center'>Client signature not found!</span>";
                                        } else {
                                            echo "<span class='text-danger text-center'>Client signature not found!</span>";
                                        }
                                        } ?>
                                </p>
                            </div>
                            <div id="tab_parking_attachments" class="tab-pane fade">
                                <h3>Parking Expenses</h3>
                                <p>
                                    <?php if ($row['is_parking'] == 1){
                                        $parking_tickets = json_decode($row['parking_tickets']);
                                        if ($parking_tickets) {
                                            foreach ($parking_tickets as $park_key => $ticket) {
                                                $park_key++;
                                                $path_parking = file_exists('../file_folder/parking_tickets/' . $ticket) ? "../file_folder/parking_tickets/" . $ticket : "";
                                                if (in_array(strtolower(end(explode(".", $ticket))), $extensions)) {
                                                    echo $path_parking ? '<a class="btn btn-xs btn-primary" target="_blank" href="' . $path_parking . '">View/Download</a><img style="border: 1px solid black;" width="50%" src="' . $path_parking . '" class="img-responsive"><br>' : "<h3 class='text-danger text-center'>Attachment " . $park_key . " failed to upload!</h3>";
                                                } else {
                                                    echo $path_parking ? '<a class="btn btn-xs btn-primary" target="_blank" href="' . $path_parking . '">View/Download</a><iframe src="' . $path_parking . '" frameborder="2" width="100%" height="100%"></iframe>' : '';
                                                }
                                            }
                                        } else {
                                            echo "<span class='text-danger text-center'>Failed to upload all parking attachments!</span>";
                                        }
                                    } else {
                                        echo "<span class='text-danger text-center'>Parking attachments not added for this job!</span>";
                                    } ?>
                                </p>
                            </div>
                            <div id="tab_other_attachments" class="tab-pane fade">
                                <h3>Public Transport Attachment Uploads</h3>
                                <p>
                                    <?php if($get_other_files->num_rows>0){
                                        while ($row_other_file = $get_other_files->fetch_assoc()) {
                                            $other_key++;
                                            $path_other_file = file_exists('../file_folder/job_files/' . $row_other_file['file_name']) ? "../file_folder/job_files/" . $row_other_file['file_name'] : "";
                                            if (in_array(strtolower(end(explode(".", $row_other_file['file_name']))), $extensions)) {
                                                echo $path_other_file ? '<label>Uploaded Date: ' . $misc->dated($row_other_file['dated']) . ' <a class="btn btn-xs btn-primary" target="_blank" href="' . $path_other_file . '">View/Download</a></label><img style="border: 1px solid black;" width="50%" src="' . $path_other_file . '" class="img-responsive"><br>' : "<h3 class='text-danger text-center'>Attachment " . $other_key . " failed to upload!</h3>";
                                            } else {
                                                echo $path_other_file ? '<label>Uploaded Date: ' . $misc->dated($row_other_file['dated']) . '</label> <a class="btn btn-xs btn-primary" target="_blank" href="' . $path_other_file . '">View/Download</a><iframe src="' . $path_other_file . '" frameborder="2" width="100%" height="100%"></iframe><br>' : "<h3 class='text-danger text-center'>Attachment " . $other_key . " failed to upload!</h3>";
                                            }
                                        }
                                    } else {
                                        echo "<span class='text-danger text-center'>Other attachments not found for this job!</span>";
                                    } ?>
                                </p>
                            </div>
                            <div id="tab_uploaded_timesheet" class="tab-pane fade">
                                <h3>Filled Timesheet Proof Attachment</h3>
                                <p>
                                    <?php if (!empty($row['time_sheet'])) {
                                        $path_timesheet = file_exists('../file_folder/time_sheet_interp/' . $row['time_sheet']) ? '../file_folder/time_sheet_interp/' . $row['time_sheet'] : "";
                                        if (in_array(strtolower(end(explode(".", $row['time_sheet']))), $extensions)) {
                                            echo $path_timesheet ? '<label>Uploaded Timesheet: <a class="btn btn-xs btn-primary" target="_blank" href="' . $path_timesheet . '">View/Download</a></label><img width="50%" src="' . $path_timesheet . '" class="img-responsive"><br>' : "<h3 class='text-danger text-center'>Timesheet attachment failed to upload!</h3>";
                                        } else {
                                            echo $path_timesheet ? ' <a class="btn btn-xs btn-primary" target="_blank" href="' . $path_timesheet . '">View/Download</a><br><iframe src="' . $path_timesheet . '" frameborder="2" width="100%" height="100%"></iframe>' : '';
                                        }
                                    } else {
                                        echo "<span class='text-danger text-center'>Filled timesheet proof not uploaded for this job!</span>";
                                    } ?>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
                </div>
            </div>
        </div>
        <!-- Client timesheet verification -->
        <?php
        $has_access = true;
        if ($_SESSION['is_root'] == 0) {
            $get_access = $obj->read_specific("id", "action_permissions", "user_id=" . $_SESSION['userId'] . " AND action_id=204")['id'];
            if (empty($get_access)) {
                $has_access = false;
            }
        }
        if ($has_access) { ?>
            <div class="panel-group">
                <div class="panel panel-default">
                <?php if ($row['request_verify'] == 0) { ?>
                    <div class="panel-heading">
                        <h4 class="panel-title">
                        <button class="btn btn-primary" data-toggle="collapse" href="#collapse1"><b>Send email to client for timesheet verification</b></button>
                        </h4>
                    </div>
                    <div id="collapse1" class="panel-collapse collapse">
                        <div class="panel-body">
                            <div class="form-group">
                                <table class="table table-bordered"><tbody>
                                    <tr>
                                        <td width="15%"><label>Interpreter Name :</label></td>
                                        <td><span id="write_interpreter_name"><?=ucwords($row['name'])?></span></td>
                                    </tr>
                                    <tr>
                                        <td><label>Client Name :</label></td>
                                        <td><span id="write_client_name"><?=$row['inchPerson']?></span></td>
                                    </tr>
                                    <tr>
                                        <td><label>Client Email :</label></td>
                                        <td><span id="write_client_email"><?=$row['inchEmail']?></span></td>
                                    </tr>
                                </tbody></table>
                            </div>
                            <div class="form-group">
                                <input type="hidden" id="write_order_id" value="<?=$row['id']?>"/>
                                <input type="hidden" id="write_interpreter_id" value="<?=$row['intrpName']?>"/>
                            </div>
                            <div class="form-group">
                                <button type="button" onclick="send_client_email(this)" class="btn btn-success" id="btn_send_client_email">Click to send email to client</button>
                            </div>
                        </div>
                        <div class="panel-footer text-danger hidden">Note: Numbers should not contain initial 0 or + sign, Valid <b>12 digits</b> number sample: 447366312487</div>
                    </div>
                    <script>
                        function send_client_email(element) {
                            if (confirm("Are you sure to send email to client?")) {
                                if ($('#write_client_email').text()) {
                                    $(element).addClass("hidden");
                                    $.ajax({
                                        url: 'process/third_party_apis.php',
                                        method: 'post',
                                        dataType: 'json',
                                        data: {
                                            order_id: $('#write_order_id').val(),
                                            order_type: 1,
                                            interpreter_id: $('#write_interpreter_id').val(),
                                            client_email: $('#write_client_email').text(),
                                            interpreter_email: "",
                                            send_client_email: 1
                                        },
                                        success: function(data) {
                                            alert(data['message']);
                                            window.location.href = window.location.href;
                                        },
                                        error: function(data) {
                                            alert("Error code : " + data.status + " , Error message : " + data.statusText);
                                        }
                                    });
                                } else {
                                    alert("Client email is mssing! Please update client email");
                                }
                            }
                        }
                    </script>
                    <?php } else {
                        $array_category = array(1 => "Confirm interpreter timesheet hours");
                        $array_message_status = array(0 => "<i title='Message not delivered to client' class='fa fa-2x pull-right fa-remove text-danger'></i>", 1 => "<i title='Message delivered successfully to client' class='fa fa-2x pull-right fa-check text-success'></i>", 2 => "<i title='Client responded back [RESPONSE_DATE]' class='fa fa-2x pull-right fa-refresh text-success'></i>");
                        $get_sent_messages = $obj->read_all("*", "client_messages", "order_type=1 AND order_id=" . $row['id'] . " AND interpreter_id=" . $intrpName . " ORDER BY id DESC");
                        if ($get_sent_messages->num_rows > 0) {
                            echo "<div class='panel-body'>
                                <table class='table table-bordered'>
                                    <thead><tr class='bg-primary'>
                                        <td width='26%'>Category</td>
                                        <td width='10%'>Client Contact Number</td>
                                        <td width='18%'>Status</td>
                                        <td>Response</td>
                                        <td>Link & Password</td>
                                    </tr></thead>";
                            while ($row_message = $get_sent_messages->fetch_assoc()) {
                                $response_date = !is_null($row_message['response_date']) ? date("d-m-Y H:i:s", strtotime($row_message['response_date'])) : "";
                                if ($row_message['status'] == 2) {
                                    $is_verified = $row_message['is_verified'] == 1 ? "<small class='label label-success'>Correct Timesheet</small>" : "<small class='label label-danger'>Incorrect Timesheet</small>";
                                } else {
                                    if ($row_message['status'] == 0) {
                                        $is_verified = "<small title='Failed to deliver this Email to " . $row_message['sent_to'] . "' class='label label-danger'>Sending Failed</small>";
                                    } else {
                                        $is_verified = is_null($row_message['response_date']) ? "<small class='label label-warning'>No Response</small>" : "";
                                    }
                                }
                                $msg_status = str_replace("[RESPONSE_DATE]", $response_date, $array_message_status[$row_message['status']]);
                                echo "<tr>
                                    <td>" . $array_category[$row_message['message_category']] . "</td>
                                    <td>" . $row_message['sent_to'] . "<br><small>Sent: " . date("d-m-Y H:i:s", strtotime($row_message['created_date'])) . "</small></td>
                                    <td>" . $msg_status . $is_verified . "</td>
                                    <td style='font-size: 10px'>" . (!is_null($row_message['response_date']) ? "Date: " . date("d-m-Y H:i:s", strtotime($row_message['response_date'])) . "<br>" : "") . ($row_message['response_message'] ? $row_message['response_message'] : "No reply") . "</td>
                                    <td style='font-size: 10px'>Verification Link:https://lsuk.org?cl.php?i=" . $row_message['id'] . "<br>Verification password:" . $row_message['password'] . "</td>
                                </tr>";
                            }
                            echo "</tbody></table></div>";
                        } else {
                            echo "<h3 class='text-center text-danger'>No messages sent to client for this timesheet verification yet!</h3>";
                        }
                    } ?>
                </div>
            </div>
        <?php } ?>
        <!-- End timesheet verification -->
        <ul class="nav nav-tabs">
            <li class="active"><a data-toggle="tab" href="#int_hours"><i class="fa fa-user"></i> Interpreter Expenses</a></li>
            <?php if ($action_update_company_expenses) { ?><li><a data-toggle="tab" href="#comp_hours"><i class="fa fa-briefcase"></i> Company Expenses</a></li><?php } ?>
        </ul>

        <div class="tab-content">
            <div id="int_hours" class="tab-pane fade in active">
                <br>
                <div class="col-md-12">
                    <form action="#" method="get" class="register" id="frm_expenses">
                        <input type="hidden" name="rate_already_set" value='<?=!empty($row['company_rate_id'])?1:0;?>'/>
                        <input type="hidden" name="company_rate_id" value='<?=!empty($row['company_rate_id'])?$row['company_rate_id']:$extracted_data['id']?>'/>
                        <input type="hidden" name="company_rate_data" value='<?=!empty($row['company_rate_data'])?$row['company_rate_data']:json_encode($extracted_data)?>'/>
                        <input type="hidden" id="edit_idd" value="<?php echo $intrpName ?>" readonly />
                        <input type="hidden" id="namee" value="<?php echo $interp_name ?>" readonly />
                        <input type="hidden" id="orgName" value="<?php echo $orgName ?>" readonly />
                        <input type="hidden" id="code_qss" value="<?php echo $code ?>" readonly />
                        <div class="col-xs-12 text-center">
                            <h4>Face To Face - Update Interpreter Expenses For <span style="color:#F00;"><?php echo $interp_name . ' ( ' . $assignDate . ' )'; ?></span>
                                <?php if ($_SESSION['userId'] == 1) { ?>
                                    <label for="skip_rate" class="btn btn-info btn-xs pull-right"><input id='skip_rate' type="checkbox" /> Skip Rate limitation</label>
                                <?php } ?>
                            </h4>
                        </div>
                        <div class="bg-info col-xs-12 form-group">
                            <h4><?= $extracted_data['title'] ?></h4>
                        </div>
                        <?php 
                        if ($row_lateness['id']) {
                            echo '<div class="form-group col-md-12">
                                <table class="table table-bordered"><tbody>
                                    <tr class="bg-danger"><td width="20%">Lateness: ' . $row_lateness['minutes'] . ' minutes</td><td width="20%">Added Date: ' . $misc->dated($row_lateness['created_date']) . '</td><td>Reason: <small>' . $row_lateness['reason'] . '</small></td></tr>
                                </tbody></table>
                            </div>';
                        }
                        ?>
                        <div class="form-group col-md-3 col-sm-6">
                            <p>Hours Worked (<?php echo 'Atleast ' . round($hoursWorkd, 2) . ' Hours'; ?>) <i class="fa fa-question-circle" title="Actual or Minimum Agreed Interpreting Time"></i></p>
                            <input placeholder="<?php echo 'Job hours atleast value is ' . round($hoursWorkd, 2); ?>" class="form-control" name="hoursWorkd" type="text" id="hoursWorkd" value="<?php echo $hoursWorkd == 0 ? '' : $hoursWorkd; ?>" pattern="[0-9]+([\.|,][0-9]+)?" step="0.01" oninput="calcInterp();int_hours();" onkeyup="checkDec(this);" />
                        </div>
                        <div class="form-group col-md-3 col-sm-6">
                            <p>Rate Per Hour<?=$extracted_data_int['rate_value_f2f'] ? ' (Min '.$extracted_data_int['rate_value_f2f'].')' : ' Not Added Yet'?></p>
                            <input class="form-control" name="rateHour" id="rateHour" type="text" pattern="[0-9]+([\.|,][0-9]+)?" step="0.01" value="<?= ($rateHour != 0 || !empty($hrsubmited)) ? $rateHour : $extracted_data_int['rate_value_f2f'];?>" oninput="calcInterp()" onkeyup="checkDec(this);" />
                        </div>
                        <div class="form-group col-md-3 col-sm-6">
                            <p>Interpreting Time Payment</p>
                            <input class="form-control" name="chargInterp" type="text" pattern="[0-9]+([\.|,][0-9]+)?" step="0.01" id="chargInterp" value="<?php echo $chargInterp ?>" readonly />
                        </div>
                        <div class="form-group col-md-3 col-sm-6">
                            <p>Due Date for Bill Payment</p>
                            <input class="form-control" name="dueDate" type="date" id="dueDate" value="<?php echo $dueDate ?>" />
                        </div>
                        <div class="bg-info col-xs-12 form-group">
                            <h4>Travel Time</h4>
                        </div>
                        <div class="form-group col-md-4 col-sm-6">
                            <p <?=$extracted_data_int['travel_time_charges'] == 1 ? 'class="text-danger" title="Travel Time Must be filled!"' : 'class="text-warning" title="Not required"'; ?>> Travel Hours </p>
                            <input <?=$extracted_data_int['travel_time_charges'] == 1?'style="border:1px solid red;"':'';?> class="form-control" name="travelTimeHour" type="text" id="travelTimeHour" value="<?php echo $travelTimeHour ?>" placeholder='' pattern="[0-9]+([\.|,][0-9]+)?" step="0.01" oninput="calcInterp()" onkeyup="checkDec(this);" />
                        </div>
                        <div class="form-group col-md-4 col-sm-6">
                            <p <?=$extracted_data_int['travel_time_charges'] == 1 ? 'class="text-danger" title="Rate Per Hour Must be filled!"' : 'class="text-warning" title="Not required"'; ?>>Rate Per Hour<?=$extracted_data_int['travel_time_charges'] == 1 ? ' (Min '.$extracted_data_int['travel_time_rate'].')' : ''?></p>
                            <input <?=$extracted_data_int['travel_time_charges'] == 1?'style="border:1px solid red;"':'';?> class="form-control" name="travelTimeRate" type="text" id="travelTimeRate" pattern="[0-9]+([\.|,][0-9]+)?" step="0.01" value="<?=$travelTimeRate ? $travelTimeRate : $extracted_data_int['travel_time_rate'];?>" placeholder='' oninput="calcInterp()" onkeyup="checkDec(this);" />
                        </div>
                        <div class="form-group col-md-4 col-sm-6">
                            <p>Travel Time Payment </p>
                            <input class="form-control" name="chargeTravelTime" type="text" pattern="[0-9]+([\.|,][0-9]+)?" step="0.01" id="chargeTravelTime" value="<?php echo $chargeTravelTime ?>" placeholder='' readonly />
                        </div>
                        <div class="bg-info col-xs-12 form-group">
                            <h4>Travel Costs</h4>
                        </div>
                        <div class="form-group col-md-4 col-sm-6">
                            <p <?=$extracted_data_int['mileage_charge'] == 1 ? 'class="text-danger" title="Travel Mileage Must be filled!"' : 'class="text-warning" title="Not required"'; ?>>Travel Mileage</p>
                            <input <?=$extracted_data_int['mileage_charge'] == 1?'style="border:1px solid red;"':'';?> class="form-control long" name="travelMile" type="text" id="travelMile" pattern="[0-9]+([\.|,][0-9]+)?" step="0.01" value="<?php echo $travelMile ?>" oninput="calcInterp()" onkeyup="checkDec(this);" />
                        </div>
                        <div class="form-group col-md-4 col-sm-6">
                            <p <?=$extracted_data_int['mileage_charge'] == 1 ? 'class="text-danger" title="Rate per Mile Must be filled!"' : 'class="text-warning" title="Not required"'; ?>>Rate Per Mile<?=$extracted_data_int['mileage_charge'] == 1 ? ' (Min '.$extracted_data_int['mileage_charge_rate'].')' : ''?></p>
                            <input <?=$extracted_data_int['mileage_charge'] == 1?'style="border:1px solid red;"':'';?> class="form-control long" name="rateMile" type="text" pattern="[0-9]+([\.|,][0-9]+)?" step="0.01" id="rateMile" value="<?php echo $rateMile ? $rateMile : $extracted_data_int['mileage_charge_rate'];?>" oninput="calcInterp()" onkeyup="checkDec(this);" />
                        </div>
                        <div class="form-group col-md-4 col-sm-6">
                            <p> Mileage Cost &pound;</p>
                            <input class="form-control" name="chargeTravel" type="text" id="chargeTravel" pattern="[0-9]+([\.|,][0-9]+)?" step="0.01" style="border:1px solid #CCC" value="<?php echo $chargeTravel ?>" placeholder='' readonly />
                        </div>
                        <div class="form-group col-md-4 col-sm-6">
                            <p>Travel Cost</p>
                            <input title="(Public Transport or Fixed Travel Allowance)" class="form-control" name="travelCost" type="text" id="travelCost" pattern="[0-9]+([\.|,][0-9]+)?" step="0.01" value="<?php echo $travelCost ?>" placeholder='' oninput="calcInterp()" onkeyup="checkDec(this);" />
                        </div>
                        <div class="form-group col-md-4 col-sm-6">
                            <p <?=$extracted_data_int['admin_charge'] == 1 ? 'class="text-danger" title="Admin Charge Must be filled!"' : 'class="text-warning" title="Not required"'; ?>>Additional Payment<?=$extracted_data_int['admin_charge'] == 1 ? ' (Admin Charge: '.$extracted_data_int['admin_charge_rate'].')' : ''?></p>
                            <input <?=$extracted_data_int['admin_charge'] == 1?'style="border:1px solid red;"':'';?> class="form-control" name="admnchargs" type="text" id="admnchargs" pattern="[0-9]+([\.|,][0-9]+)?" step="0.01" value="<?php echo $admnchargs ?>" placeholder='' oninput="calcInterp()" onkeyup="checkDec(this);" />
                        </div>
                        <div class="form-group col-md-4 col-sm-6">
                            <p <?=$extracted_data_int['parking_charges'] == 1 ? 'class="text-danger" title="Parking can be given as agreed!"' : 'class="text-warning" title="Not required"'; ?>>Other Costs <?=$extracted_data_int['parking_charges'] == 1 ? ' (Parking)' : ''?></p>
                            <input <?=$extracted_data_int['parking_charges'] == 1?'style="border:1px solid red;"':'';?> title="(Parking , Bridge Toll)" class="form-control" name="otherCost" type="text" id="otherCost" pattern="[0-9]+([\.|,][0-9]+)?" step="0.01" value="<?php echo $otherCost ?>" placeholder='' oninput="calcInterp()" onkeyup="checkDec(this);" />
                        </div>
                        <div class="form-group col-md-4 col-sm-6">
                            <?php $get_late_minutes = !empty($row['intrpName']) ? $row_lateness['minutes'] : 0; ?>
                            <p>Deduction <?php echo $get_late_minutes>0?'<span class="h4 text-danger" style="display: inline;"><b>Late minutes: '.$get_late_minutes.'</b></span>':'';
                            echo $get_late_minutes>0 && !$deduction ? '<span class="label label-danger pull-right">Not deducted!</span>':'';?></p>
                            <input <?=$get_late_minutes>0 && !$deduction ? 'style="border:1px solid red"' : '';?> title="(No or Late Attendance or DBS Fee, etc (If Applicable)" class="form-control" name="deduction" type="text" id="deduction" pattern="[0-9]+([\.|,][0-9]+)?" step="0.01" value="<?php echo $deduction ?>" placeholder='' oninput="calcInterp()" onkeyup="checkDec(this);" />
                        </div>
                        <div class="form-group col-md-4 col-sm-6">
                            <p>National Insurance Deduction</p>
                            <input class="form-control" name="ni_dedu" type="text" id="ni_dedu" pattern="[0-9]+([\.|,][0-9]+)?" step="0.01" value="<?php echo $ni_dedu ?>" placeholder='' oninput="calcInterp()" onkeyup="checkDec(this);" />
                        </div>
                        <div class="form-group col-md-4 col-sm-6">
                            <p>Tax Deduction</p>
                            <input class="form-control" name="tax_dedu" type="text" id="tax_dedu" pattern="[0-9]+([\.|,][0-9]+)?" step="0.01" value="<?php echo $tax_dedu ?>" placeholder='' oninput="calcInterp()" onkeyup="checkDec(this);" />
                        </div>
                        <div class="form-group col-sm-4">
                            <p style="color:#F00">Current VAT % </p>
                            <input class="form-control" name="int_vat" type="text" pattern="[0-9]+([\.|,][0-9]+)?" step="0.01" id="int_vat" value="<?php echo $int_vat ?: 0; ?>" placeholder='' oninput="calcInterp();fun_vat_no();" onkeyup="checkDec(this);" required='' />
                        </div>
                        <div class="form-group col-sm-4" <?=(!empty($int_vat) && $int_vat != 0) ? 'style="display:inline"' : 'style="display:none"';?> id="div_vat_no">
                            <p style="color:#F00">VAT Number (if any) </p>
                            <input class="form-control" name="vat_no_int" type="text" id="vat_no_int" value="<?php echo $vat_no_int; ?>" placeholder='' />
                        </div>
                        <div class="form-group col-sm-4">
                            <p>Total Payment</p>
                            <input class="form-control" name="totalChages" type="text" id="totalChages" pattern="[0-9]+([\.|,][0-9]+)?" step="0.01" value="<?php echo $total_charges_interp - $deduction; ?>" placeholder='' readonly />
                        </div>
                        <div class="form-group col-sm-6">
                            <textarea placeholder="Notes (If Any) 1000 Characters, Please enter notes for future reference, details of all deductions or additional payments" class="form-control" name="exp_remrks" rows="3" id="exp_remrks"><?php echo $exp_remrks ?></textarea>
                        </div>
                        <div id="div_further" style="display:none;">
                            <div class="form-group col-sm-4">
                                <label id="lbl_feedback">Do you want to add feedback from timesheet?</label>
                                <select class="form-control" onchange="check_func(this);" id="check_further" required=''>
                                    <option value=""></option>
                                    <option value="yes">Proceed with feedback (on timesheet)</option>
                                    <option value="no">No Feedback (on timesheet)</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group col-sm-6">
                            <span <?= $row['is_temp'] == 0 || !$action_auto_approve ? "class='hidden'" : "";?>>
                                <input type="checkbox" name="is_temp" id="is_temp_int"> <label> Mark as confirmed for invoicing?</label><br>
                            </span>
                            <?php if ($row['paid_interp'] == 1) { ?>
                                <h4><label class="label label-danger">Payslip already issued!</label>
                                    <small><br><br>Note: Payroll needs to adjust the payslip to update these expenses.</small>
                                </h4>
                                <?php } else {
                                    if ($row['hrsubmited'] == "Self" && $row['approved_flag'] == 0) { ?>
                                        <p class="text-danger label_not_approved">Timesheet uploaded by interpreter but not approved by any staff user yet!</p>
                                    <?php } ?>
                                    <button onclick="submit_expenses(<?=$row['hrsubmited'] == 'Self' && $row['approved_flag'] == 0 ? 1 : 0?>)" class="btn btn-<?=$row['hrsubmited'] == 'Self' && $row['approved_flag'] == 0 ? 'success' : 'info'?>" style="<?=$row['hrsubmited'] == 'Self' && $row['approved_flag'] == 0 ? 'border-color: #000000;color: black;text-transform: uppercase;font-size: 20px;font-weight: bold;box-shadow: 2px 2px 2px #c5c5a3;' : 'border-color: #000000;color: black;text-transform: uppercase;font-size: 20px;font-weight: bold;box-shadow: 2px 2px 2px #c5c5a3;'?>" type="button" name="btn_submit_expense" id="btn_submit_expense"><?=$row['hrsubmited'] == 'Self' && $row['approved_flag'] == 0 ? 'Approve Hours' : 'Submit'?> &raquo;</button>
                                <?php } ?>
                            </p>
                        </div>

                        <!-- Modal -->
                        <div id="myModal" class="modal fade" role="dialog">
                            <div class="modal-dialog modal-lg" style="width: 820px;">

                                <!-- Modal content-->
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                                        <h4 class="modal-title">Feedback For Interpreter</h4>
                                    </div>
                                    <div class="modal-body" id="myModalBody">

                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <?php if ($action_update_company_expenses) { ?>
                <div id="comp_hours" class="tab-pane fade">
                    <br>
                    <div class="col-md-12">
                        <form action="" method="post">
                            <input type="hidden" name="rate_already_set" value='<?=!empty($row['company_rate_id'])?1:0;?>'/>
                            <input type="hidden" name="company_rate_id" value='<?=!empty($row['company_rate_id'])?$row['company_rate_id']:$extracted_data['id']?>'/>
                            <input type="hidden" name="company_rate_data" value='<?=!empty($row['company_rate_data'])?$row['company_rate_data']:json_encode($extracted_data)?>'/>
                            <div class="col-xs-12 text-center">
                                <h4>Face To Face - Update Client Expenses For Invoicing: <span style="color:#F00;"><?php echo $orgName . ' ( ' . $assignDate . ' )'; ?></span></h4>
                            </div>
                            <div class="bg-info col-xs-12 form-group">
                                <h4><?php echo $extracted_data['title'];echo $row['C_hoursWorkd']?'':'<span class="label label-danger pull-right">Company Hours Not Saved Yet!</span>' ?></h4>
                            </div>
                            <div class="form-group col-md-4 col-sm-3">
                                <p>Interpreting Time (<?php $value = $C_hoursWorkd . ' hour(s)';echo 'Atleast ' . $value; ?>) <i class="fa fa-question-circle" title="Enter Minimum or Requested Hours if less Than Actual Hours"></i></p>
                                <input class="form-control" title="Enter Minimum or Requested Hours if less Than Actual Hours" name="C_hoursWorkd" type="text" id="C_hoursWorkd" value="<?php echo $C_hoursWorkd ?>" pattern="[0-9]+([\.|,][0-9]+)?" step="0.01" oninput="C_calcInterp();company_hours();" onkeyup="checkDec(this);" />
                            </div>
                            <div class="form-group col-md-4 col-sm-3">
                                <p>Rate Per Hour<?=$extracted_data['rate_value_f2f'] ? ' (Min '.$extracted_data['rate_value_f2f'].')' : ' Not Added Yet'?></p>
                                <input class="form-control" name="C_rateHour" type="text" pattern="[0-9]+([\.|,][0-9]+)?" step="0.01" id="C_rateHour" value="<?php echo $C_rateHour ? $C_rateHour : 0; ?>" oninput="C_calcInterp()" onkeyup="checkDec(this);" />
                            </div>
                            <div class="form-group col-md-4 col-sm-3">
                                <p> Interpreting Time Charge <?= "<br><b class='text-danger'>Interpreter updated: " . $chargInterp . "</b>";?></p>
                                <input class="form-control" name="C_chargInterp" type="text" pattern="[0-9]+([\.|,][0-9]+)?" step="0.01" id="C_chargInterp" value="<?php echo $C_chargInterp ?>" />
                            </div>
                            <div class="bg-info col-xs-12 form-group">
                                <h4>Travel Time <?= "<b class='pull-right text-danger'>Interpreter updated: " . $travelTimeHour . "</b>";?></h4>
                            </div>
                            <div class="form-group col-md-4 col-sm-3">
                                <p <?=$tr_time == 1 ? 'class="text-danger" title="Travel Time Must be filled!"' : 'class="text-warning" title="Not required"'; ?>> Travel Time </p>
                                <input <?= $tr_time == 1 ?'style="border:1px solid red;"':'';?> class="form-control" name="C_travelTimeHour" type="text" id="C_travelTimeHour" value="<?php echo $C_travelTimeHour; ?>" placeholder='' pattern="[0-9]+([\.|,][0-9]+)?" step="0.01" oninput="C_calcInterp()" onkeyup="checkDec(this);" />
                            </div>
                            <div class="form-group col-md-4 col-sm-3">
                                <p <?php echo $tr_time == 1 ? 'class="text-danger" title="Must be filled!"' : 'class="text-warning" title="Not required"'; ?>>Rate Per Hour<?=$extracted_data['travel_time_charges'] == 1 ? ' (Min '.$extracted_data['travel_time_rate'].')' : ''?></p>
                                <input <?=$tr_time == 1 ? 'title="Travel charge must be filled!" style="border:1px solid red;"' : '';?> class="form-control" name="C_travelTimeRate" type="text" id="C_travelTimeRate" pattern="[0-9]+([\.|,][0-9]+)?" step="0.01" value="<?php echo $C_travelTimeRate; ?>" placeholder='' oninput="C_calcInterp()" onkeyup="checkDec(this);" />
                            </div>
                            <div class="form-group col-md-4 col-sm-3">
                                <p>Travel Time Charge</p>
                                <input class="form-control" name="C_chargeTravelTime" type="text" pattern="[0-9]+([\.|,][0-9]+)?" step="0.01" id="C_chargeTravelTime" value="<?php echo $C_chargeTravelTime ?>" placeholder='' />
                            </div>
                            <div class="form-group col-md-4 col-sm-3">
                                <p><strong><em>Purchase Order No.</em></strong></p>
                                <input class="form-control" name="porder" type="text" id="porder" value="<?php echo $porder ?>" placeholder='' readonly="readonly" />
                            </div>
                            <div class="form-group col-md-4 col-sm-3">
                                <p style="color:#F00">Current VAT @ % <?= "<b class='pull-right text-danger'>Interpreter updated: " . $int_vat . "</b>";?></p>
                                <input class="form-control" name="C_cur_vat" type="text" pattern="[0-9]+([\.|,][0-9]+)?" step="0.01" id="C_cur_vat" value="<?= ($C_cur_vat == 0) ? 0.2 : $C_cur_vat;?>" placeholder='' required='' oninput="C_calcInterp();C_fun_vat_no();" onkeyup="checkDec(this);" />
                            </div>
                            <div class="form-group col-md-4 col-sm-3" <?= (!empty($C_cur_vat) && $C_cur_vat != 0) ? 'style="display:block"' : 'style="display:none"';?> id="C_div_vat_no">
                                <p style="color:#F00">VAT Number (if any) </p>
                                <input class="form-control" name="C_vat_no_comp" type="text" id="C_vat_no_comp" value="<?php echo $vat_no_comp; ?>" placeholder='Write VAT Number' />
                            </div>
                            <div class="bg-info col-xs-12 form-group">
                                <h4>Travel Costs</h4>
                            </div>
                            <div class="form-group col-md-4 col-sm-3">
                                <p <?= $extracted_mileage_charge == 1 ? 'class="text-danger" title="Travel Mileage Must be filled!"' : 'class="text-warning" title="Not required"'; ?>>Travel Mileage <?= "<b class='pull-right text-danger'>Interpreter updated: " . $travelMile . "</b>";?></p>
                                <input <?= ($extracted_mileage_charge == 1) ? 'style="border:1px solid red;"' : ''; ?> name="C_travelMile" type="text" class="form-control long" id="C_travelMile" pattern="[0-9]+([\.|,][0-9]+)?" step="0.01" value="<?php echo $C_travelMile ?>" oninput="C_calcInterp()" onkeyup="checkDec(this);" />
                            </div>
                            <div class="form-group col-md-4 col-sm-3">
                                <p <?php echo $extracted_mileage_charge == 1 ? 'class="text-danger" title="Must be filled!"' : 'class="text-warning" title="Not required"'; ?>>Rate Per Mile<?=$extracted_data['mileage_charge'] == 1 ? ' (Min '.$extracted_data['mileage_charge_rate'].')' : ''?></p>
                                <input <?= ($extracted_mileage_charge == 1) ? 'title="Travel mileage rate must be filled!" style="border:1px solid red;"' : ''; ?> name="C_rateMile" type="text" pattern="[0-9]+([\.|,][0-9]+)?" step="0.01" class="form-control long" id="C_rateMile" value="<?php echo $C_rateMile; ?>" oninput="C_calcInterp()" onkeyup="checkDec(this);" />
                            </div>
                            <div class="form-group col-md-4 col-sm-3">
                                <p> Mileage Cost <?= "<b class='pull-right text-danger'>Interpreter updated: " . $chargeTravel . "</b>";?></p>
                                <input class="form-control" name="C_chargeTravel" type="text" id="C_chargeTravel" pattern="[0-9]+([\.|,][0-9]+)?" step="0.01" style="border:1px solid #CCC" value="<?php echo $C_chargeTravel ?>" placeholder='' />
                            </div>
                            <div class="form-group col-md-4 col-sm-3">
                                <p>Public Transport Cost <?= "<b class='pull-right text-danger'>Interpreter updated: " . $travelCost . "</b>";?></p>
                                <input class="form-control" name="C_travelCost" type="text" id="C_travelCost" pattern="[0-9]+([\.|,][0-9]+)?" step="0.01" value="<?php echo $C_travelCost ?>" placeholder='' oninput="C_calcInterp()" onkeyup="checkDec(this);" />
                            </div>
                            <div class="form-group col-md-4 col-sm-3">
                                <p <?php echo $admin_ch == 1 ? 'class="text-danger" title="Must be filled!"' : 'class="text-warning" title="Not required"'; ?>> Admin Charges</p>
                                <input <?=$admin_ch == 1 ? 'title="Admin charge must be filled!" style="border:1px solid red;"' : '';?> name="C_admnchargs" type="text" id="C_admnchargs" pattern="[0-9]+([\.|,][0-9]+)?" step="0.01" class="form-control" value="<?php echo $C_admnchargs != '' ? $C_admnchargs : ($admin_ch == 1 && $C_admnchargs == 0 ? $admin_rate : $C_admnchargs); ?>" placeholder='' oninput="C_calcInterp()" onkeyup="checkDec(this);" />
                            </div>
                            <div class="form-group col-md-4 col-sm-3">
                                <p <?=$extracted_parking_charges == 1 ? 'class="text-danger" title="Parking can be given as agreed!"' : 'class="text-warning" title="Not required"'; ?>>Other Costs <?=$extracted_parking_charges == 1 ? ' (Parking)' : ''?></p>
                                <input <?=$extracted_parking_charges == 1 ? 'title="Parking cost can be filled!" style="border:1px solid red;"' : '';?> title="(Parking , Bridge Toll) (If Applicable)" class="form-control" name="C_otherCost" type="text" id="C_otherCost" pattern="[0-9]+([\.|,][0-9]+)?" step="0.01" value="<?php echo $C_otherCost ?>" placeholder='' oninput="C_calcInterp()" onkeyup="checkDec(this);" />
                            </div>
                            <div class="form-group col-md-4 col-sm-3">
                                <p>Deduction <?= "<b class='pull-right text-danger'>Interpreter updated: " . $deduction . "</b>";?></p>
                                <input class="form-control" name="C_deduction" type="text" id="C_deduction" pattern="[0-9]+([\.|,][0-9]+)?" step="0.01" value="<?php echo $C_deduction ?>" placeholder='' oninput="C_calcInterp()" onkeyup="checkDec(this);" />
                            </div>
                            <div class="form-group col-md-4 col-sm-3">
                                <p>Other Expenses-Total</p>
                                <input class="form-control" name="C_otherexpns" type="text" id="C_otherexpns" pattern="[0-9]+([\.|,][0-9]+)?" step="0.01" value="<?php echo $C_otherexpns ?>" placeholder='' readonly />
                            </div>
                            <div class="form-group col-md-4 col-sm-3">
                                <p><b>Job Total</b> <?= "<b class='pull-right text-danger'>Interpreter updated: " . $total_charges_interp . "</b>";?></p>
                                <input title="(Excluding VAT and Non-VATable charge)" class="form-control" name="C_totalChages" type="text" id="C_totalChages" pattern="[0-9]+([\.|,][0-9]+)?" step="0.01" value="<?php echo $total_charges_comp - $C_deduction; ?>" placeholder='' readonly />
                            </div>
                            <div class="form-group col-sm-6">
                                <textarea placeholder="Notes if Any 1000 alphabets" class="form-control" name="C_comments" rows="3" id="C_comments"><?php echo $C_comments; ?></textarea>
                            </div>
                            <div class="form-group col-sm-6">
                                <span <?= $row['is_temp'] == 0 || !$action_auto_approve ? "class='hidden'" : "";?>>
                                    <input type="checkbox" name="is_temp" id="is_temp"> <label> Mark as confirmed for invoicing?</label><br>
                                </span>
                                <?php if ($row['paid_interp'] == 1) { ?>
                                    <h4><label class="label label-danger">Payslip already issued!</label>
                                        <small><br><br>Note: Payroll needs to adjust the payslip to update these expenses.</small>
                                    </h4>
                                <?php } else { ?>
                                    <button class="btn btn-info" style="border-color: #000000;color: black;text-transform: uppercase;font-size: 20px;font-weight: bold;box-shadow: 2px 2px 2px #c5c5a3;" type="submit" name="submit" id="btn_company_expense">Submit &raquo;</button>
                                <?php } ?>
                            </div>
                        </form>
                    </div>
                </div>
            <?php } ?>
        </div>
        <script>
            //ajax call for interpreter data
            function get_interp_data() {
                var edit_idd = $('#edit_idd').val();
                var namee = $('#namee').val();
                var orgName = $('#orgName').val();
                var code_qss = $('#code_qss').val();
                var order_id = '<?php echo $update_id ?>';
                $.ajax({
                    url: 'get_interp_data.php',
                    method: 'post',
                    data: {
                        edit_idd: edit_idd,
                        code_qss: code_qss,
                        namee: namee,
                        orgName: orgName,
                        order_id: order_id
                    },
                    success: function(data) {
                        $('#myModalBody').html(data);
                        $('#myModal').modal("show");
                    },
                    error: function(xhr) {
                        alert("An error occured: " + xhr.status + " " + xhr.statusText);
                    }
                });
            }

            //end of ajax call
            function check_func(element) {
                if (element.value == "") {
                    document.getElementById('btn_submit_expense').disabled = 'true';
                } else if (element.value == "yes") {
                    get_interp_data();
                    document.getElementById('btn_submit_expense').disabled = 'false';
                } else if (element.value == "no") {
                    $("#lbl_feedback").hide();
                    $("#check_further").before("<label id='lbl_feedback'>Do you want to add future job?</label>");
                    $("#check_further").empty();
                    $("#check_further").append("<option value=''></option>");
                    $("#check_further").append("<option value='no_future'>Not for Future Job (on timesheet)</option>");
                    $("#check_further").append("<option value='future'>Future Job (on timesheet)</option>");
                    document.getElementById('btn_submit_expense').disabled = 'false';
                } else if (element.value == "future") {
                    self.close();
                    MM_openBrWindow('interp_edit.php?edit_id=<?php echo $update_id; ?>&duplicate=<?php echo 'yes'; ?>', '_blank', 'scrollbars=yes,resizable=yes,width=1200,height=700');
                    document.getElementById('btn_submit_expense').disabled = 'false';
                } else if (element.value == "no_future") {
                    self.close();
                    document.getElementById('btn_submit_expense').disabled = 'false';
                } else {
                    document.getElementById('btn_submit_expense').disabled = 'true';
                }
            }
            // end of function

            function submit_expenses(action_approve_expenses = 0) {
                var form_elements = document.getElementById('frm_expenses').elements;
                var hoursWorkd = form_elements['hoursWorkd'].value;
                var rateHour = form_elements['rateHour'].value;
                var chargInterp = form_elements['chargInterp'].value;
                var dueDate = form_elements['dueDate'].value;
                var travelMile = form_elements['travelMile'].value;
                var rateMile = form_elements['rateMile'].value;
                var chargeTravel = form_elements['chargeTravel'].value;
                var travelCost = form_elements['travelCost'].value;
                var admnchargs = form_elements['admnchargs'].value;
                var otherCost = form_elements['otherCost'].value;
                var deduction = form_elements['deduction'].value;
                var ni_dedu = form_elements['ni_dedu'].value;
                var tax_dedu = form_elements['tax_dedu'].value;
                var totalChages = form_elements['totalChages'].value;
                var travelTimeHour = form_elements['travelTimeHour'].value;
                var travelTimeRate = form_elements['travelTimeRate'].value;
                var chargeTravelTime = form_elements['chargeTravelTime'].value;
                var int_vat = form_elements['int_vat'].value;
                var vat_no_int = form_elements['vat_no_int'].value;
                var exp_remrks = form_elements['exp_remrks'].value;
                var update_id = '<?php echo $update_id; ?>';
                var interp_hr_date = '<?php echo $misc->sys_date_db() ?>';
                var is_temp = document.getElementById("is_temp_int").checked == true ? 'yes' : 'no';
                if (hoursWorkd && hoursWorkd > 0) {
                    if (int_vat == '0' || (int_vat != '0' && vat_no_int != '')) {
                        $.ajax({
                            url: "store_expenses.php",
                            type: "POST",
                            data: {
                                hoursWorkd: hoursWorkd,
                                rateHour: rateHour,
                                chargInterp: chargInterp,
                                dueDate: dueDate,
                                travelMile: travelMile,
                                rateMile: rateMile,
                                chargeTravel: chargeTravel,
                                travelCost: travelCost,
                                admnchargs: admnchargs,
                                otherCost: otherCost,
                                deduction: deduction,
                                ni_dedu: ni_dedu,
                                tax_dedu: tax_dedu,
                                totalChages: totalChages,
                                travelTimeHour: travelTimeHour,
                                travelTimeRate: travelTimeRate,
                                chargeTravelTime: chargeTravelTime,
                                exp_remrks: exp_remrks,
                                update_id: update_id,
                                interp_hr_date: interp_hr_date,
                                int_vat: int_vat,
                                vat_no_int: vat_no_int,
                                is_temp: is_temp,
                                action_approve_expenses: action_approve_expenses
                            },
                            success: function(data) {
                                if (data == '1') {
                                    var management = '<?php echo $managment; ?>';
                                    if (management == 0) {
                                        window.onunload = refreshParent;
                                    }
                                    if (action_approve_expenses == 0) {
                                        alert("Interpreter expenses have been updated successfully.");
                                    } else {
                                        $('.label_not_approved').remove();
                                        alert("Interpreter expenses have been approved successfully.");
                                    }
                                    $("#div_further").show();
                                    $("#btn_submit_expense").hide();
                                }
                            },
                            error: function(xhr) {
                                alert("An error occured: " + xhr.status + " " + xhr.statusText);
                            }
                        });
                    } else {
                        alert('You must enter VAT Number for entered VAT value !');
                        form_elements['vat_no_int'].focus();
                    }
                } else {
                    alert('Hours worked value must be greater than 0 ');
                    form_elements['hoursWorkd'].focus();
                }
            }
        </script>
    <?php } ?>
</body>
<script>
    $('#rateHour').change(function() {
        var lang = '<?php echo $row["source"] ?>';
        // if (!$('#skip_rate').is(":checked")) {
        //     if ($(this).val() < 15) {
        //         alert("Rate value cannot be less then 15!");
        //         $('#btn_submit_expense').attr('disabled', 'disabled');
        //         $(this).select();
        //     }
        // }
        if (lang != 'Sign Language (BSL)' && $(this).val() > 40) {
            alert("Rate value cannot be greater then 40!");
            $('#btn_submit_expense').attr('disabled', 'disabled');
            $(this).select();
        }
        // if ($(this).val() >= 15 && (lang != 'Sign Language (BSL)' && $(this).val() <= 40)) {
        //     $('#btn_submit_expense').removeAttr('disabled');
        // }
        if (lang != 'Sign Language (BSL)' && $(this).val() <= 40) {
            $('#btn_submit_expense').removeAttr('disabled');
        }
    });

    $(document).ready(function() {
        $('#skip_rate').change(function() {
            if (this.checked) {
                $('#btn_submit_expense').removeAttr("disabled");
            }
        });
    });
</script>

</html>