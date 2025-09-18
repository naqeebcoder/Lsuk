<?php if (session_id() == '' || !isset($_SESSION)) {
    session_start();
}
include 'db.php';
include 'class.php';
$table = 'telephone';
$allowed_type_idz = "72,84,119,160,167,175";
//Check if user has current action allowed
if ($_SESSION['is_root'] == 0) {
    $get_page_access = $acttObj->read_specific("id", "action_permissions", "user_id=" . $_SESSION['userId'] . " AND action_id IN (" . $allowed_type_idz . ")")['id'];
    if (empty($get_page_access)) {
        die("<center><h2 class='text-center text-danger'>You do not have access to <u>Update expenses</u> action for jobs!<br>Kindly contact admin for further process.</h2></center>");
    }
}
//Action for update company expenses
$company_expenses_action_idz = "168,169,170,185";
$action_update_company_expenses = $acttObj->read_specific("id", "action_permissions", "user_id=" . $_SESSION['userId'] . " AND action_id IN (" . $company_expenses_action_idz . ")")['id'];
$action_update_company_expenses = $_SESSION['is_root'] == 1 || !empty($action_update_company_expenses);
$array_added_via = array(-1 =>"Not yet Fixed",0 => "<span class='label label-info'>LSUK Admin</span>", 1 => "<span class='label label-success'>Mobile App</span>", 2 => "<span class='label label-warning'>Interpreter Portal</span>", 3 => "<span class='label label-primary'>Android App</span>", 4 => "<span class='label label-danger'>iOS App</span>");

$update_id = @$_GET['update_id'];
$row = $acttObj->read_specific("$table.*,interpreter_reg.name", "$table,interpreter_reg", "$table.intrpName=interpreter_reg.id AND $table.id=$update_id");
if ($_SESSION['is_root'] == 1) {
    $managment = 1;
} else {
    $managment = 0;
}
//Access actions
$get_actions = explode(",", $acttObj->read_specific("GROUP_CONCAT(action_permissions.action_id) as actions", "action_permissions,route_actions", "action_permissions.action_id=route_actions.id AND route_actions.route_id=135 AND action_permissions.user_id=" . $_SESSION['userId'])['actions']);
$action_auto_approve = $_SESSION['is_root'] == 1 || in_array(212, $get_actions); ?>
<script>
    function refreshParent() {
        window.opener.location.reload();
    }
</script>
<?php //If interpreter expenses updated
if (isset($_POST['btn_submit_expense'])) {
    $datetime = date("Y-m-d H:i:s");
    $hoursWorkd = $_POST['hoursWorkd'];
    $acttObj->editFun($table, $update_id, 'hoursWorkd', $hoursWorkd);
    $rateHour = $_POST['rateHour'];
    $acttObj->editFun($table, $update_id, 'rateHour', $rateHour);
    $chargInterp = $_POST['chargInterp'];
    $acttObj->editFun($table, $update_id, 'chargInterp', $chargInterp);
    $dueDate = $_POST['dueDate'];
    $acttObj->editFun($table, $update_id, 'dueDate', $dueDate);
    $calCharges = $_POST['calCharges'];
    $acttObj->editFun($table, $update_id, 'calCharges', $calCharges);
    $otherCharges = $_POST['otherCharges'];
    $acttObj->editFun($table, $update_id, 'otherCharges', $otherCharges);
    $admnchargs = $_POST['admnchargs'];
    $acttObj->editFun($table, $update_id, 'admnchargs', $admnchargs);
    $ni_dedu = $_POST['ni_dedu'];
    $acttObj->editFun($table, $update_id, 'ni_dedu', $ni_dedu);
    $tax_dedu = $_POST['tax_dedu'];
    $acttObj->editFun($table, $update_id, 'tax_dedu', $tax_dedu);
    $deduction = $_POST['deduction'];
    $acttObj->editFun($table, $update_id, 'deduction', $deduction);
    $total_chrg_i = $_POST['total_charges_interp'];
    $acttObj->editFun($table, $update_id, 'total_charges_interp', $total_chrg_i);
    $int_vat_post = $_POST['int_vat'];
    $acttObj->editFun($table, $update_id, 'int_vat', $int_vat_post);
    $vat_no_post = $_POST['vat_no_int'];
    $acttObj->editFun($table, $update_id, 'vat_no_int', $vat_no_post);
    $exp_remrks = $_POST['exp_remrks'];
    $acttObj->editFun($table, $update_id, 'exp_remrks', $exp_remrks);
    if ($_SESSION['Temp'] == 1) {
        $acttObj->editFun($table, $update_id, 'is_temp', 1);
    }
    if ($action_auto_approve && isset($_POST['is_temp'])) {
        $acttObj->editFun($table, $update_id, 'is_temp', 0);
    }
    if ($_POST['action_approve_expenses'] == 1) {
        $approve_update_array = array("approved_flag" => 1, "approved_by" => $_SESSION['userId'], "approved_date" => $datetime);
        $acttObj->update($table, $approve_update_array, "id=" . $update_id);
    }else{
        $acttObj->editFun($table, $update_id, 'added_via',0); 
        $acttObj->editFun($table, $update_id, 'hrsubmited', ucwords($_SESSION['UserName']));
        $acttObj->editFun($table, $update_id, 'interp_hr_date', $misc->sys_date_db());
    }
    $index_mapping = array(
        'Duration.Mins' => 'hoursWorkd', 'RPM' => 'rateHour', 'Interp.Time Payment' => 'chargInterp', 'Call.Charges' => 'calCharges', 'Other.Charges' => 'otherCharges', 'Additional.Pay' => 'admnchargs', 
        'NI.Deduction' => 'ni_dedu', 'Tax.Deduction' => 'tax_dedu', 'Deduction' => 'deduction', 'VAT' => 'int_vat', 'VAT.No' => 'vat_no_int', 'Total.Charges' => 'total_charges_interp', 'Temporary' => 'is_temp', 'Remarks' => 'exp_remrks'
    );
    if ($_POST['action_approve_expenses'] == 1) {
        $index_mapping['Approved.Flag'] = 'approved_flag';
        $index_mapping['Approved By'] = 'approved_by';
        $index_mapping['DateTime'] = 'approved_date';
        $acttObj->insert("daily_logs", array("action_id" => 39, "user_id" => $_SESSION['userId'], "details" => "TP Job ID: " . $update_id));
    } else {
        $acttObj->insert("daily_logs", array("action_id" => 12, "user_id" => $_SESSION['userId'], "details" => "TP Job ID: " . $update_id));
    }
    $old_values = array();
    $new_values = array();
    $get_new_data = $acttObj->read_specific("*", "$table", "id=" . $update_id);
    
    foreach ($index_mapping as $key => $value) {
        if (isset($get_new_data[$value])) {
            $old_values[$key] = $row[$value];
            $new_values[$key] = $get_new_data[$value];
        }
    }
    $acttObj->log_changes(json_encode($old_values), json_encode($new_values), $update_id, $table, "update", $_SESSION['userId'], $_SESSION['UserName'], ($_POST['action_approve_expenses'] == 1 ? "approved_job_telephone" : "interpreter_expenses_telephone"));
    //Below history function to be deleted
    echo '<script>alert("Interpreter expenses have been ' . ($_POST['action_approve_expenses'] == 1 ? 'approved' : 'updated') . ' successfully.");</script>';
    if ($managment == 0) {
        echo '<script>window.onunload = refreshParent;</script>';
    }
}
//If company expenses updated
if (isset($_POST['btn_company_expense'])) {
    $C_hoursWorkd = $_POST['C_hoursWorkd'];
    $acttObj->editFun($table, $update_id, 'C_hoursWorkd', $C_hoursWorkd);
    $C_rateHour = $_POST['C_rateHour'];
    $acttObj->editFun($table, $update_id, 'C_rateHour', $C_rateHour);
    $C_chargInterp = $_POST['C_chargInterp'];
    $acttObj->editFun($table, $update_id, 'C_chargInterp', $C_chargInterp);
    $C_otherCharges = $_POST['C_otherCharges'];
    $acttObj->editFun($table, $update_id, 'C_otherCharges', $C_otherCharges);
	$acttObj->editFun($table, $update_id, 'C_callcharges', $_POST['C_callcharges']);
    $C_admnchargs = $_POST['C_admnchargs'];
    $acttObj->editFun($table, $update_id, 'C_admnchargs', $C_admnchargs);
    $total_chrg_c = $_POST['total_charges_comp'];
    $acttObj->editFun($table, $update_id, 'total_charges_comp', $total_chrg_c);
    $cur_vat = $_POST['cur_vat'];
    $acttObj->editFun($table, $update_id, 'cur_vat', $cur_vat);
    $vat_no_comp = $_POST['vat_no_comp'];
    $acttObj->editFun($table, $update_id, 'vat_no_comp', $vat_no_comp);
    $C_comments = $_POST['C_comments'];
    $acttObj->editFun($table, $update_id, 'C_comments', $C_comments);
    if ($_SESSION['Temp'] == 1) {
        $acttObj->editFun($table, $update_id, 'is_temp', 1);
    }
    if ($action_auto_approve && isset($_POST['is_temp'])) {
        $acttObj->editFun($table, $update_id, 'is_temp', 0);
    }
    $acttObj->editFun($table, $update_id, 'comp_hrsubmited', ucwords($_SESSION['UserName']));
    $acttObj->editFun($table, $update_id, 'comp_hr_date', $misc->sys_date_db());
    $index_mapping = array(
        'comp_call.chargers'=>'c_callcharges','Duration.Mins' => 'C_hoursWorkd', 'RPM' => 'C_rateHour', 'Interp.Time Payment' => 'C_chargInterp', 'Other.Charges' => 'C_otherCharges', 'Additional.Pay' => 'C_admnchargs', 
        'Deduction' => 'C_deduction', 'VAT' => 'cur_vat', 'VAT.No' => 'vat_no_comp', 'Total.Charges' => 'total_charges_comp', 'Temporary' => 'is_temp', 'Remarks' => 'C_comments'
    );
    
    $old_values = array();
    $new_values = array();
    $get_new_data = $acttObj->read_specific("*", "$table", "id=" . $update_id);
    
    foreach ($index_mapping as $key => $value) {
        if (isset($get_new_data[$value])) {
            $old_values[$key] = $row[$value];
            $new_values[$key] = $get_new_data[$value];
        }
    }
    $acttObj->log_changes(json_encode($old_values), json_encode($new_values), $update_id, $table, "update", $_SESSION['userId'], $_SESSION['UserName'], "company_expenses_telephone");
    //Below history function to be deleted
    $acttObj->insert("daily_logs", array("action_id" => 13, "user_id" => $_SESSION['userId'], "details" => "TP Job ID: " . $update_id));
    echo '<script>alert("Company expenses for job have been updated.");</script>';
    if ($managment == 0) {
        echo '<script>window.onunload = refreshParent;</script>';
    }
}

$row = $acttObj->read_specific("$table.*,interpreter_reg.name", "$table,interpreter_reg", "$table.intrpName=interpreter_reg.id AND $table.id=$update_id");
$row_lateness = $acttObj->read_specific("*", "job_late_minutes", "job_id=" . $row['id'] . " AND job_type=2 AND interpreter_id=" . $row['intrpName']);
$bookinType = $row['bookinType'];
$hoursWorkd = $row['hoursWorkd'] ?: 0;
$chargInterp = $row['chargInterp'] ?: 0;
$rateHour = $row['rateHour'] ?: 0;
$calCharges = $row['calCharges'] ?: 0;
$otherCharges = $row['otherCharges'] ?: 0;
$dueDate = $row['dueDate'];
$intrpName = $row['intrpName'];
$is_order_cancelled = $row['orderCancelatoin'];
$total_charges_interp = $row['total_charges_interp'] ?: 0;
$admnchargs = $row['admnchargs'] ?: 0;
$C_admnchargs = $row['C_admnchargs'] != '' ? $row['C_admnchargs'] : 0;
$deduction = $row['deduction'] ?: 0;
$exp_remrks = $row['exp_remrks'];
$ni_dedu = $row['ni_dedu'] ?: 0;
$tax_dedu = $row['tax_dedu'] ?: 0;
$interp_name = $row['name'];
$assignDate = $row['assignDate'];
$assignTime = $row['assignTime'];
$assignDur = $row['assignDur'];
$vat_no_comp = $row['vat_no_comp'];
$cur_vat = $row['cur_vat'];
$chk_hoursWorkd = $row['hoursWorkd'] ?: 0;
$orgName = $row['orgName'];
$C_hoursWorkd = $row['C_hoursWorkd'] ?: 0;
$C_chargInterp = $row['C_chargInterp'] ?: 0;
$C_rateHour = $row['C_rateHour'] ?: 0;
$C_otherCharges = $row['C_otherCharges'] ?: 0;
$C_callcharges = $row['C_callcharges'] ?: 0;
$total_charges_comp = $row['total_charges_comp'] ?: 0;
$hrsubmited = $row['hrsubmited'];
$int_vat = $row['int_vat'];
$net_vat = ($chargInterp) * $int_vat ?: 0;
$vat_no_int = $row['vat_no_int'];
$reqs = '';
if ($admin_ch == 1) {
    $reqs .= 'Admin Charge ';
}
if ($tr_time == 1) {
    $reqs .= ', Travel Time ';
}
if ($interp_time == 1) {
    $reqs .= ', Interpreting Time ';
}
if ($admin_ch == 0 && $tr_time == 0 && $interp_time == 0) {
}
$reqs .= ' required';
if ($admin_ch == 0 && $tr_time == 0 && $interp_time == 0) {
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
$interp_rpm = $acttObj->unique_data('interpreter_reg', 'rpm', 'id', $intrpName) ?: 0;
$C_interp_rpm = $acttObj->unique_data('booking_type', 'rate', 'title', $bookinType) ?: 0;
if ((date('Y-m-d H:i', strtotime($assignDate . ' ' . $assignTime)) > date('Y-m-d H:i')) && $row['orderCancelatoin'] == 0) {
    $problem_hours = 1;
    $problem_msg = 'Assignment Date & Time: <b class="text-danger">' . $assignDate . ' ' . $assignTime . '</b><br><br>This job is not completed yet! Thank you';
} else if ($row['deleted_flag'] == 1 || $row['order_cancel_flag'] == 1) {
    $problem_hours = 1;
    $problem_msg = 'This job is in processing mode! Thank you';
} else {
    $problem_hours = 0;
    $problem_msg = '';
} ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
    <title>Client Expenses - Telephone Interpreting</title>
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
		.d-none-imp{
            display:none !important;
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

            var calCharges = parseFloat(document.getElementById('calCharges').value);
            var otherCharges = parseFloat(document.getElementById('otherCharges').value);
            var admnchargs = parseFloat(document.getElementById('admnchargs').value);
            var deduction = parseFloat(document.getElementById('deduction').value);

            var int_vat = document.getElementById('int_vat').value;
            var net_vat = x * int_vat;
            document.getElementById('net_vat').value = parseFloat(net_vat.toFixed(2));
            total_charges_interp.value = (parseFloat(calCharges + x + otherCharges + admnchargs) - parseFloat(deduction)).toFixed(2);
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

        function C_fun_vat_no() {
            var cur_vat = document.getElementById("cur_vat").value;
            var vat_no_comp = document.getElementById("vat_no_comp");
            var C_div_vat_no = document.getElementById("C_div_vat_no");
            if (!isNaN(cur_vat) && cur_vat != 0) {
                C_div_vat_no.style.display = 'block';
                vat_no_comp.setAttribute("required", "required");
            } else {
                C_div_vat_no.style.display = 'none';
                vat_no_comp.removeAttribute("required", "required");
            }
        }

        function C_calcInterp() {
            var C_hoursWorkd = parseFloat(document.getElementById('C_hoursWorkd').value);
            var C_rateHour = parseFloat(document.getElementById('C_rateHour').value);
            var C_chargInterp = document.getElementById('C_chargInterp');
            var x = C_rateHour * C_hoursWorkd;
            C_chargInterp.value = x.toFixed(2);

            var C_otherCharges = parseFloat(document.getElementById('C_otherCharges').value);
			var C_callcharges = parseFloat(document.getElementById('C_callcharges').value);
            var C_admnchargs = parseFloat(document.getElementById('C_admnchargs').value);

            total_charges_comp.value = (parseFloat(x + C_otherCharges + C_admnchargs + C_callcharges)).toFixed(2);
        }

        function int_hours() {
            var actual_dur = parseFloat('<?php echo $assignDur; ?>');
            if (parseFloat($('#hoursWorkd').val()) < actual_dur) {
                <?php if (!$is_order_cancelled): ?>
                    $('#hoursWorkd').addClass('cls_danger');
                    $('#hoursWorkd').attr('title', 'Duration minutes must be atleast ' + actual_dur);
                    $('#btn_submit_expense').attr("disabled", "disabled");
                <?php endif; ?>
            } else {
                $('#hoursWorkd').removeClass('cls_danger');
                $('#btn_submit_expense').removeAttr("disabled");
            }
        }

        function company_hours() {
            var C_actual_hours = parseFloat('<?php echo $chk_hoursWorkd == 0 ? round($assignDur, 2) : $chk_hoursWorkd; ?>');
            if (parseFloat($('#C_hoursWorkd').val()) < C_actual_hours) {
                $('#C_hoursWorkd').addClass('cls_danger');
                $('#C_hoursWorkd').attr('title', 'Duration minutes must be atleast ' + C_actual_hours);
                $('#btn_company_expense').attr("disabled", "disabled");
            } else {
                $('#C_hoursWorkd').removeClass('cls_danger');
                $('#btn_company_expense').removeAttr("disabled");
            }
        }

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
                <caption>
                    <h4>Job Actual Booking Information</h4>
                </caption>
                <tr>
                    <td width="25%">Assignment Date : <?php echo $assignDate . ' ' . $assignTime; ?></td>
                    <td>Duration : <?php echo $get_dur; ?></td>
                    <td>Timesheet Status : <?php echo $hoursWorkd == 0 ? 'Not Filled Yet !' : $hoursWorkd . ' minutes(s)'; ?></td>
                </tr>
                <tr>
                    <td>Timesheet Updated VIA : <?php echo $hoursWorkd == 0 ? 'Not Updated' : $array_added_via[$row['added_via']];
                                                echo !is_null($row['int_sign_date']) ? "<br>Signed on : " . $row['int_sign_date'] : "";
                                                echo !empty($row['int_sig']) && $row['int_sig'] != 'i_default.png' ? "<a href='../file_folder/interpreter_signatures/" . $row['int_sig'] . "' target='_blank' title='Click to view interpreter signature' class='btn btn-success btn-xs'>View Interpreter Signature</a>" : ""; ?></td>
                    <td><?php $row['wt_tm'] = is_null($row['wt_tm']) || $row['wt_tm'] == '1001-01-01 00:00:00' ? "Nil" : $row['wt_tm'];
                        $row['st_tm'] = is_null($row['st_tm']) || $row['st_tm'] == '1001-01-01 00:00:00' ? "Nil" : $row['st_tm'];
                        $row['fn_tm'] = is_null($row['fn_tm']) || $row['fn_tm'] == '1001-01-01 00:00:00' ? "Nil" : $row['fn_tm'];
                        echo "Waiting Time : " . $row['wt_tm'] . "<br>Starting Time : " . $row['st_tm'] . "<br>Finished Time : " . $row['fn_tm'];
                        ?>
                    </td>
                    <td>
                        <?php $expected_start = date($assignDate . ' ' . substr($assignTime, 0, 5));
                        echo "Expected Start Time: " . $expected_start . "<br>Expected End Time: " . date("Y-m-d H:i", strtotime("+" . $row['assignDur'] . " minutes", strtotime($expected_start))); ?>
                        <br>Requirements : <?php echo $reqs; ?>
                    </td>
                </tr>
            </tbody>
        </table>
        <!-- Client timesheet verification -->
        <?php
        $has_access = true;
        if ($_SESSION['is_root'] == 0) {
            $get_access = $acttObj->read_specific("id", "action_permissions", "user_id=" . $_SESSION['userId'] . " AND action_id=204")['id'];
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
                                            order_type: 2,
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
                        $get_sent_messages = $acttObj->read_all("*", "client_messages", "order_type=2 AND order_id=" . $row['id'] . " AND interpreter_id=" . $intrpName . " ORDER BY id DESC");
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
        <label for="toggleIntHours" class="btn btn-info btn-xs pull-right" style="margin: 8px 10px 0 0;">
                <input id="toggleIntHours" type="checkbox" <?= !$is_order_cancelled ? 'checked' : '' ?>> Add Interpreter Rate
        </label>
		</ul>

        <div class="tab-content">
            <div id="int_hours" class="tab-pane fade in active <?= !$is_order_cancelled ? '' : 'd-none-imp' ?>">
                <br>
                <div class="col-md-12">
                    <form action="" method="post" class="register">
                        <div class="col-xs-12 text-center">
                            <h4>Telephone - Update Telephone Expenses For <span style="color:#F00;"><?php echo $interp_name . ' ( ' . $assignDate . ' )'; ?></span>
                                <?php if ($_SESSION['userId'] == 1) { ?>
                                    <label for="skip_rate" class="btn btn-info btn-xs pull-right"><input id='skip_rate' type="checkbox" /> Skip Rate limitation</label>
                                <?php } ?>
                            </h4>
                        </div>
                        <div class="bg-info col-xs-12 form-group">
                            <h4>Fixed Rate or Per Hour Rate (As Agreed) Booking Type:
                                <span style="color:#900;">
                                    <?php if (!empty($row['company_rate_data']) && $row['company_rate_data'] != null) {
                                        $booking_type_array = json_decode($row['company_rate_data'], true);
                                        // $booking_type = explode("-", $booking_type_array['title']);
                                        echo trim($booking_type_array['title']);
                                    } else {
                                        echo ucwords($bookinType) ?: 'Nil';
                                    } ?>
                                </span>
                            </h4>
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
                            <p> Worked Duration (<?php echo 'Atleast ' . round($assignDur, 2) . ' minutes'; ?>)</p>
                            <input class="form-control" name="hoursWorkd" type="text" id="hoursWorkd" value="<?php echo $hoursWorkd ?>" pattern="[0-9]+([\.|,][0-9]+)?" step="0.01" oninput="calcInterp();int_hours();" onkeyup="checkDec(this);" />
                        </div>
                        <div class="form-group col-md-3 col-sm-6">
                            <p>Rate Per Minute</p>
                            <input class="form-control" name="rateHour" type="text" pattern="[0-9]+([\.|,][0-9]+)?" step="0.01" id="rateHour" value="<?php if ($rateHour != 0 || !empty($hrsubmited)) {
                                                                                                                                                            echo $rateHour;
                                                                                                                                                        } else {
                                                                                                                                                            echo $interp_rpm;
                                                                                                                                                        } ?>" oninput="calcInterp()" onkeyup="checkDec(this);" />
                        </div>
                        <div class="form-group col-md-3 col-sm-6">
                            <p>Total for Interpreting Time
                            </p>
                            <input class="form-control" name="chargInterp" type="text" pattern="[0-9]+([\.|,][0-9]+)?" step="0.01" id="chargInterp" value="<?php echo $chargInterp ?>" oninput="calcInterp()" onkeyup="checkDec(this);" readonly />
                        </div>
                        <div class="form-group col-md-3 col-sm-6">
                            <p>Due Date for Bill Payment</p>
                            <input class="form-control" name="dueDate" type="date" id="dueDate" value="<?php echo $dueDate ?>" />
                        </div>
                        <div class="form-group col-md-3 col-sm-6">
                            <p>Call Charges (If Applicable)</p>
                            <input class="form-control" name="calCharges" type="text" pattern="[0-9]+([\.|,][0-9]+)?" step="0.01" id="calCharges" value="<?php echo $calCharges ?>" oninput="calcInterp()" onkeyup="checkDec(this);" />
                        </div>
                        <div class="form-group col-md-3 col-sm-6">
                            <p>Other Charges (If Applicable)</p>
                            <input class="form-control" name="otherCharges" type="text" pattern="[0-9]+([\.|,][0-9]+)?" step="0.01" id="otherCharges" value="<?php echo $otherCharges ?>" oninput="calcInterp()" onkeyup="checkDec(this);" />
                        </div>
                        <div class="form-group col-md-3 col-sm-6">
                            <p>Additional Payment (If Applicable)</p>
                            <input class="form-control" name="admnchargs" type="text" id="admnchargs" pattern="[0-9]+([\.|,][0-9]+)?" step="0.01" value="<?php echo $admnchargs ?>" placeholder='' oninput="calcInterp()" onkeyup="checkDec(this);" />
                        </div>
                        <div class="form-group col-md-3 col-sm-6">
                            <p>National Insurance Deduction</p>
                            <input title="(If Applicable)" class="form-control" class="form-control" name="ni_dedu" type="text" id="ni_dedu" pattern="[0-9]+([\.|,][0-9]+)?" step="0.01" value="<?php echo $ni_dedu ?>" placeholder='' oninput="calcInterp();int_hours();" onkeyup="checkDec(this);" />
                        </div>
                        <div class="form-group col-md-3 col-sm-6">
                            <p>Tax Deduction (If Applicable)</p>
                            <input class="form-control" name="tax_dedu" type="text" id="tax_dedu" pattern="[0-9]+([\.|,][0-9]+)?" step="0.01" value="<?php echo $tax_dedu ?>" placeholder='' oninput="calcInterp()" onkeyup="checkDec(this);" />
                        </div>
                        <div class="form-group col-md-4 col-sm-6">
                            <?php $get_late_minutes = !empty($row['intrpName']) ? $row_lateness['minutes'] : 0; ?>
                            <p>Deduction <?php echo $get_late_minutes>0?'<span class="h4 text-danger" style="display: inline;"><b>Late minutes: '.$get_late_minutes.'</b></span>':'';
                            echo $get_late_minutes>0 && !$deduction ? '<span class="label label-danger pull-right">Not deducted!</span>':'';?></p>
                            <input <?=$get_late_minutes>0 && !$deduction ? 'style="border:1px solid red"' : '';?> class="form-control" name="deduction" type="text" id="deduction" pattern="[0-9]+([\.|,][0-9]+)?" step="0.01" value="<?php echo $deduction ?>" placeholder='' oninput="calcInterp()" onkeyup="checkDec(this);" />
                        </div>

                        <div class="form-group col-md-3 col-sm-6">
                            <p><b>Total</b></p>
                            <input class="form-control" name="total_charges_interp" type="text" pattern="[0-9]+([\.|,][0-9]+)?" step="0.01" id="total_charges_interp" value="<?php echo $total_charges_interp ?>" readonly />
                        </div>
                        <div class="row col-md-12">
                            <div class="form-group col-md-2 col-sm-4">
                                <p style="color:#F00">Current VAT % </p>
                                <input class="form-control" name="int_vat" type="text" pattern="[0-9]+([\.|,][0-9]+)?" step="0.01" id="int_vat" value="<?php echo $int_vat ?: 0; ?>" placeholder='' oninput="calcInterp();fun_vat_no();" onkeyup="checkDec(this);" required='' />
                            </div>
                            <div class="form-group col-md-2 col-sm-4" <?php if (!empty($int_vat) && $int_vat != 0) {
                                                                            echo 'style="display:inline"';
                                                                        } else {
                                                                            echo 'style="display:none"';
                                                                        } ?> id="div_vat_no">
                                <p style="color:#F00">VAT Number </p>
                                <input class="form-control" name="vat_no_int" type="text" id="vat_no_int" value="<?php echo $vat_no_int; ?>" placeholder='' />
                            </div>
                            <div class="form-group col-md-2 col-sm-4">
                                <p style="color:#F00">Total VAT Cost </p>
                                <input class="form-control" name="net_vat" type="text" pattern="[0-9]+([\.|,][0-9]+)?" step="0.01" id="net_vat" value="<?php echo $net_vat; ?>" readonly />
                            </div>
                            <div class="form-group col-sm-6">
                                <span <?= $row['is_temp'] == 0 || !$action_auto_approve ? "class='hidden'" : "";?>>
                                    <input type="checkbox" name="is_temp" id="is_temp_int"> <label> Mark as confirmed for invoicing?</label><br>
                                </span>
                                <?php if ($row['intrp_salary_comit'] == 1 || $row['salary_id'] != 0) { ?>
                                    <h4><label class="label label-danger">Payslip already issued!</label>
                                        <small><br><br>Note: Payroll needs to rollback the payslip to update these expenses.</small>
                                    </h4>
                                <?php } else {
                                    if ($row['hrsubmited'] == "Self" && $row['approved_flag'] == 0) { ?>
                                        <input type="hidden" name="action_approve_expenses" value="1"/>
                                        <p class="text-danger label_not_approved">Timesheet uploaded by interpreter but not approved by any staff user yet!</p>
                                    <?php } else { ?>
                                        <br>    
                                    <?php } ?>
                                    <button class="btn btn-<?=$row['hrsubmited'] == 'Self' && $row['approved_flag'] == 0 ? 'success' : 'info'?>" style="<?=$row['hrsubmited'] == 'Self' && $row['approved_flag'] == 0 ? 'border-color: #000000;color: black;text-transform: uppercase;font-size: 20px;font-weight: bold;box-shadow: 2px 2px 2px #c5c5a3;' : 'border-color: #000000;color: black;text-transform: uppercase;font-size: 20px;font-weight: bold;box-shadow: 2px 2px 2px #c5c5a3;'?>" type="submit" name="btn_submit_expense" id="btn_submit_expense"><?=$row['hrsubmited'] == 'Self' && $row['approved_flag'] == 0 ? 'Approve Hours' : 'Submit'?> &raquo;</button>
                                <?php } ?>
                                </p>
                            </div>
                            <div class="form-group col-sm-6">
                                <textarea placeholder="Notes (if Any) up to 1000 characters" class="form-control" name="exp_remrks" rows="2" id="exp_remrks"><?php echo $exp_remrks ?></textarea>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <?php if ($action_update_company_expenses) { ?>
                <div id="comp_hours" class="tab-pane fade">
                    <br>
                    <div class="col-md-12">
                        <form action="" method="post" class="register">
                            <div class="col-xs-12 text-center">
                                <h4>Telephone - Update Client Expenses For Invoicing: <span style="color:#F00;"><?php echo $orgName . ' ( ' . $assignDate . ' )'; ?></span></h4>
                            </div>
                            <div class="bg-info col-xs-12 form-group">
                                <h4>Fixed Rate or Per Minute Rate (As Agreed) (Booking Type: <span style="color:#900;"><?php echo $bookinType; ?></span>)</h4>
                            </div>
                            <div class="form-group col-md-4 col-sm-3">
                                <p>Duration (<?php echo 'Atleast ' . ($chk_hoursWorkd == 0 ? $get_dur : $chk_hoursWorkd . ' minutes');?>)
                                <?= "<br><b class='text-danger'>Interpreter updated: " . $hoursWorkd . "</b>";?></p>
                                <input class="form-control" name="C_hoursWorkd" type="text" id="C_hoursWorkd" value="<?php echo $C_hoursWorkd ?>" pattern="[0-9]+([\.|,][0-9]+)?" step="0.01" oninput="C_calcInterp();company_hours();" onkeyup="checkDec(this);" />
                            </div>
                            <div class="form-group col-md-4 col-sm-3">
                                <p>Rate Per Minute <?= "<br><b class='text-danger'>Interpreter updated: " . $rateHour . "</b>";?></p>
                                <input class="form-control" name="C_rateHour" type="text" pattern="[0-9]+([\.|,][0-9]+)?" step="0.01" id="C_rateHour" value="<?php if ($C_rateHour != 0) {
                                                                                                                                                                    echo $C_rateHour;
                                                                                                                                                                } else {
                                                                                                                                                                    echo $C_interp_rpm;
                                                                                                                                                                } ?>" oninput="C_calcInterp()" onkeyup="checkDec(this);" />
                            </div>
                            <div class="form-group col-md-4 col-sm-3">
                                <p>Interpreting Time Charge <?= "<br><b class='text-danger'>Interpreter updated: " . $chargInterp . "</b>";?></p>
                                <input class="form-control" name="C_chargInterp" type="text" pattern="[0-9]+([\.|,][0-9]+)?" step="0.01" id="C_chargInterp" value="<?php echo $C_chargInterp ?>" readonly />
                            </div>
							<div class="form-group col-md-4 col-sm-3">
                                <p>Call Charges (If Applicable) <?= "<b class='pull-right text-danger'>Interpreter updated: " . $calCharges . "</b>";?></p>
                                <input class="form-control" name="C_callcharges" type="text" pattern="[0-9]+([\.|,][0-9]+)?" step="0.01" id="C_callcharges" value="<?php echo $C_callcharges ?>" oninput="C_calcInterp()" onkeyup="checkDec(this);" />
                            </div>
                            <div class="form-group col-md-4 col-sm-3">
                                <p>Other Charges (If Applicable) <?= "<b class='pull-right text-danger'>Interpreter updated: " . $otherCharges . "</b>";?></p>
                                <input class="form-control" name="C_otherCharges" type="text" pattern="[0-9]+([\.|,][0-9]+)?" step="0.01" id="C_otherCharges" value="<?php echo $C_otherCharges ?>" oninput="C_calcInterp()" onkeyup="checkDec(this);" />
                            </div>
                            <div class="form-group col-md-4 col-sm-3">
                                <p <?php echo $admin_ch == 1 ? 'class="text-danger" title="Must be filled!"' : 'class="text-warning" title="Not required"'; ?>> Admin Charges <?= "<b class='pull-right text-danger'>Interpreter updated: " . $admnchargs . "</b>";?></p>
                                <input <?php if ($admin_ch == 1) {
                                            echo 'title="Admin charge must be filled!" style="border:1px solid red;"';
                                        } ?> class="form-control" name="C_admnchargs" type="text" pattern="[0-9]+([\.|,][0-9]+)?" step="0.01" id="C_admnchargs" required='' value="<?php echo $C_admnchargs; ?>" oninput="C_calcInterp()" onkeyup="checkDec(this);" />
                            </div>
                            <div class="form-group col-md-4 col-sm-3">
                                <p><b>Total</b> <?= "<b class='pull-right text-danger'>Interpreter updated: " . $total_charges_interp . "</b>";?></p>
                                <input class="form-control" name="total_charges_comp" type="text" pattern="[0-9]+([\.|,][0-9]+)?" step="0.01" id="total_charges_comp" value="<?php echo $total_charges_comp ?>" readonly />
                            </div>
                            <div class="form-group col-md-4 col-sm-3">
                                <p style="color:#F00">Current VAT @ % <?= "<b class='pull-right text-danger'>Interpreter updated: " . $int_vat . "</b>";?></p>
                                <input class="form-control" name="cur_vat" type="text" pattern="[0-9]+([\.|,][0-9]+)?" step="0.01" id="cur_vat" value="<?php if ($cur_vat == 0) {
                                                                                                                                                            echo 0.2;
                                                                                                                                                        } else {
                                                                                                                                                            echo $cur_vat;
                                                                                                                                                        } ?>" placeholder='' required='' oninput="C_calcInterp();C_fun_vat_no();" onkeyup="checkDec(this);" />
                            </div>
                            <div class="form-group col-md-4 col-sm-3" <?php if (!empty($cur_vat) && $cur_vat != 0) {
                                                                            echo 'style="display:inline"';
                                                                        } else {
                                                                            echo 'style="display:none"';
                                                                        } ?> id="C_div_vat_no">
                                <p style="color:#F00">VAT Number (if any) </p>
                                <input class="form-control" name="vat_no_comp" type="text" id="vat_no_comp" value="<?php echo $vat_no_comp; ?>" placeholder='Write VAT Number' />
                            </div>
                            <div class="form-group col-md-4 col-sm-3">
                                <p><strong><em>Purchase Order No.</em></strong></p>
                                <input class="form-control" name="porder" type="text" id="porder" value="<?php echo $porder ?>" placeholder='' readonly="readonly" />
                            </div>
                            <div class="form-group col-sm-6">
                                <textarea placeholder="Notes if Any 1000 alphabets" class="form-control" name="C_comments" rows="3" id="C_comments"><?php echo $C_comments; ?></textarea>
                            </div>
                            <div class="form-group col-md-6">
                                <span <?= $row['is_temp'] == 0 || !$action_auto_approve ? "class='hidden'" : "";?>>
                                    <input type="checkbox" name="is_temp" id="is_temp"> <label> Mark as confirmed for invoicing?</label><br>
                                </span>
                                <?php if ($row['commit'] == 1) { ?>
                                    <h4><label class="label label-danger">Invoice already issued!</label>
                                        <small><br><br>Note: Finance need to un-commit the invoice to update these expenses.</small>
                                    </h4>
                                <?php }  else { ?>
                                        <button class="btn btn-info" style="border-color: #000000;color: black;text-transform: uppercase;font-size: 20px;font-weight: bold;box-shadow: 2px 2px 2px #c5c5a3;" type="submit" name="btn_company_expense" id="btn_company_expense">Submit &raquo;</button>
                                    <?php } ?>
                                </p>
                            </div>
                        </form>
                    </div>
                </div>
            <?php } ?>
        </div>
    <?php } ?>
</body>
<script>
    $('#rateHour').change(function() {
        var lang = '<?php echo $row["source"] ?>';
        // if (!$('#skip_rate').is(":checked")) {
        //     if ($(this).val() < 0.1) {
        //         alert("Rate value cannot be less then 0.1!");
        //         $('#btn_submit_expense').attr('disabled', 'disabled');
        //         $(this).select();
        //     }
        // }
        if (lang != 'Sign Language (BSL)' && $(this).val() > 0.75) {
            alert("Rate value cannot be greater then 0.75!");
            $('#btn_submit_expense').attr('disabled', 'disabled');
            $(this).select();
        }
        // if ($(this).val() >= 0.1 && (lang != 'Sign Language (BSL)' && $(this).val() <= 0.75)) {
        //     $('#btn_submit_expense').removeAttr('disabled');
        // }
        if (lang != 'Sign Language (BSL)' && $(this).val() <= 0.75) {
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
<script>
    document.getElementById('toggleIntHours').addEventListener('change', function () {
        const intHoursDiv = document.getElementById('int_hours');
        if (this.checked) {
            intHoursDiv.classList.remove('d-none-imp');
        } else {
            intHoursDiv.classList.add('d-none-imp');
        }
    });
</script>

</html>