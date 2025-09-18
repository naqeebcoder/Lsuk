<?php if (session_id() == '' || !isset($_SESSION)) {
    session_start();
}
include 'db.php';
include 'class.php';
$table = 'translation';
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
$array_added_via = array(0 => "<span class='label label-info'>LSUK Admin</span>", 1 => "<span class='label label-success'>Mobile App</span>", 2 => "<span class='label label-warning'>Interpreter Portal</span>", 3 => "<span class='label label-primary'>Android App</span>", 4 => "<span class='label label-danger'>iOS App</span>");
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
    $numberUnit = $_POST['numberUnit'];
    $acttObj->editFun($table, $update_id, 'numberUnit', $numberUnit);
    $rpU = $_POST['rpU'];
    $acttObj->editFun($table, $update_id, 'rpU', $rpU);
    $admnchargs = $_POST['admnchargs'];
    $acttObj->editFun($table, $update_id, 'admnchargs', $admnchargs);
    $otherCharg = $_POST['otherCharg'];
    $acttObj->editFun($table, $update_id, 'otherCharg', $otherCharg);
    $deduction = $_POST['deduction'];
    $acttObj->editFun($table, $update_id, 'deduction', $deduction);
    $dueDate = $_POST['dueDate'];
    $acttObj->editFun($table, $update_id, 'dueDate', $dueDate);
    $ni_dedu = $_POST['ni_dedu'];
    $acttObj->editFun($table, $update_id, 'ni_dedu', $ni_dedu);
    $tax_dedu = $_POST['tax_dedu'];
    $acttObj->editFun($table, $update_id, 'tax_dedu', $tax_dedu);
    $int_vat_post = $_POST['int_vat'];
    $acttObj->editFun($table, $update_id, 'int_vat', $int_vat_post);
    $vat_no_post = $_POST['vat_no_int'];
    $acttObj->editFun($table, $update_id, 'vat_no_int', $vat_no_post);
    $total_charges_i = $_POST['total_charges_interp'];
    $acttObj->editFun($table, $update_id, 'total_charges_interp', $total_charges_i);
    $exp_remrks = $_POST['exp_remrks'];
    $acttObj->editFun($table, $update_id, 'exp_remrks', $exp_remrks);
    if ($_SESSION['Temp'] == 1) {
        $acttObj->editFun($table, $update_id, 'is_temp', 1);
    }
    if ($action_auto_approve && isset($_POST['is_temp'])) {
        $acttObj->editFun($table, $update_id, 'is_temp', 0);
    }
    $total = ($numberUnit * $rpU) + $otherCharg;
    $acttObj->editFun($table, $update_id, 'total_charges_interp', $total);
    if ($_POST['action_approve_expenses'] == 1) {
        $approve_update_array = array("approved_flag" => 1, "approved_by" => $_SESSION['userId'], "approved_date" => $datetime);
        $acttObj->update($table, $approve_update_array, "id=" . $update_id);
    }else{
        $acttObj->editFun($table, $update_id, 'hrsubmited', ucwords($_SESSION['UserName']));
        $acttObj->editFun($table, $update_id, 'interp_hr_date', $misc->sys_date_db());
    }
    $index_mapping = array(
        'Units.Count' => 'numberUnit', 'RPU' => 'rpU', 'Other.Charges' => 'otherCharg', 'Additional.Pay' => 'admnchargs', 'Deduction' => 'deduction', 'NI.Deduction' => 'ni_dedu', 
        'Tax.Deduction' => 'tax_dedu', 'VAT' => 'int_vat', 'VAT.No' => 'vat_no_int', 'Total.Charges' => 'total_charges_interp', 'Temporary' => 'is_temp', 'Remarks' => 'exp_remrks'
    );
    if ($_POST['action_approve_expenses'] == 1) {
        $index_mapping['Approved.Flag'] = 'approved_flag';
        $index_mapping['Approved By'] = 'approved_by';
        $index_mapping['DateTime'] = 'approved_date';
        $acttObj->insert("daily_logs", array("action_id" => 39, "user_id" => $_SESSION['userId'], "details" => "TR Job ID: " . $update_id));
    } else {
        $acttObj->insert("daily_logs", array("action_id" => 12, "user_id" => $_SESSION['userId'], "details" => "TR Job ID: " . $update_id));
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
    $acttObj->log_changes(json_encode($old_values), json_encode($new_values), $update_id, $table, "update", $_SESSION['userId'], $_SESSION['UserName'], ($_POST['action_approve_expenses'] == 1 ? "approved_job_translation" : "interpreter_expenses_translation"));
    //Below history function to be deleted
    echo '<script>alert("Interpreter expenses have been updated successfully.");</script>';
    if ($managment == 0) {
        echo '<script>window.onunload = refreshParent;</script>';
    }
}
//If company expenses updated
if (isset($_POST['btn_company_expense'])) {
    $C_numberUnit = $_POST['C_numberUnit'];
    $acttObj->editFun($table, $update_id, 'C_numberUnit', $C_numberUnit);
    $C_rpU = $_POST['C_rpU'];
    $acttObj->editFun($table, $update_id, 'C_rpU', $C_rpU);
    /*$C_numberWord=$_POST['C_numberWord'];
    $acttObj->editFun($table,$update_id,'C_numberWord',$C_numberWord);
    $C_rpW=$_POST['C_rpW'];
    $acttObj->editFun($table,$update_id,'C_rpW',$C_rpW);*/
    $certificationCost = $_POST['certificationCost'];
    $acttObj->editFun($table, $update_id, 'certificationCost', $certificationCost);
    $proofCost = $_POST['proofCost'];
    $acttObj->editFun($table, $update_id, 'proofCost', $proofCost);
    $postageCost = $_POST['postageCost'];
    $acttObj->editFun($table, $update_id, 'postageCost', $postageCost);
    $C_otherCharg = $_POST['C_otherCharg'];
    $acttObj->editFun($table, $update_id, 'C_otherCharg', $C_otherCharg);
    $cur_vat = $_POST['cur_vat'];
    $acttObj->editFun($table, $update_id, 'cur_vat', $cur_vat);
    $vat_no_post = $_POST['vat_no_comp'];
    $acttObj->editFun($table, $update_id, 'vat_no_comp', $vat_no_post);
    $total_charges_c = $_POST['total_charges_comp'];
    $acttObj->editFun($table, $update_id, 'total_charges_comp', $total_charges_c);
    $C_admnchargs = $_POST['C_admnchargs'];
    $acttObj->editFun($table, $update_id, 'C_admnchargs', $C_admnchargs);
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
        'Units.Count' => 'C_numberUnit', 'RPU' => 'C_rpU', 'Certification.Cost' => 'certificationCost', 'Proofreading.Cost' => 'proofCost', 'Postage.Cost' => 'postageCost', 'Other.Charges' => 'C_otherCharg', 
        'Additional.Pay' => 'C_admnchargs', 'VAT' => 'cur_vat', 'VAT.No' => 'vat_no_comp', 'Total.Charges' => 'total_charges_comp', 'Temporary' => 'is_temp', 'Remarks' => 'C_comments'
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
    $acttObj->log_changes(json_encode($old_values), json_encode($new_values), $update_id, $table, "update", $_SESSION['userId'], $_SESSION['UserName'], "company_expenses_translation");
    //Below history function to be deleted
    $acttObj->insert("daily_logs", array("action_id" => 13, "user_id" => $_SESSION['userId'], "details" => "TR Job ID: " . $update_id));
    echo '<script>alert("Company expenses for job have been updated.");</script>';
    if ($managment == 0) {
        echo '<script>window.onunload = refreshParent;</script>';
    }
}
$row = $acttObj->read_specific("$table.*,interpreter_reg.name", "$table,interpreter_reg", "$table.intrpName=interpreter_reg.id AND $table.id=$update_id");
$bookinType = $row['bookinType'];
$numberUnit = $row['numberUnit'] ?: 0;
$rpU = $row['rpU'] ?: 0;
$otherCharg = $row['otherCharg'] ?: 0;
$intrpName = $row['intrpName'];
$total_charges_interp = $row['total_charges_interp'] ?: 0;
$dueDate = $row['dueDate'];
$deduction = $row['deduction'] ?: 0;
$admnchargs = $row['admnchargs'] ?: 0;
$exp_remrks = $row['exp_remrks'];
$ni_dedu = $row['ni_dedu'] ?: 0;
$tax_dedu = $row['tax_dedu'] ?: 0;
$interp_name = $row['name'];
$asignDate = $row['asignDate'];
$int_vat = $row['int_vat'];
$docType = $row['docType'];
if ($docType == 7) {
    $trans_single_label = 'Unit';
    $trans_multi_label = ' Units';
} else {
    $trans_single_label = 'Word';
    $trans_multi_label = ' Words';
}
$vat_no_int = $row['vat_no_int'];
$interp_rpu = $acttObj->unique_data('interpreter_reg', 'rpu', 'id', $intrpName) ?: 0;
$C_numberUnit = $row['C_numberUnit'] ?: 0;
$C_rpU = $row['C_rpU'] ?: 0;
$hrsubmited = $row['hrsubmited'];
$total_units = $C_numberUnit * $C_rpU;
$C_otherCharg = $row['C_otherCharg'] ?: 0;
$total_charges_comp = $row['total_charges_comp'] ?: 0;
$certificationCost = $row['certificationCost'] ?: 0;
$proofCost = $row['proofCost'] ?: 0;
$deliveryType = $row['deliveryType'];
$postageCost = $row['postageCost'] ?: 0;
$C_numberWord = $row['C_numberWord'] ?: 0;
$C_rpW = $row['C_rpW'] ?: 0;
$total_words = $C_numberWord * $C_rpW;
$C_admnchargs = $row['C_admnchargs'] != '' ? $row['C_admnchargs'] : 0;
$cur_vat = $row['cur_vat'];
$porder = $row['porder'];
$C_comments = $row['C_comments'];
$orgName = $row['orgName'];
$vat_no_comp = $row['vat_no_comp'];
$chk_numberUnit = $row['numberUnit'] ?: 0;
//Get company requirements
$get_comp = $acttObj->read_specific("admin_ch,admin_rate", "comp_reg", "abrv='" . $orgName . "'");
$admin_ch = $get_comp['admin_ch'];
$admin_rate = $get_comp['admin_rate'];
$reqs = '';
if ($admin_ch == 1) {
    $reqs = 'Admin Charge required';
} else {
    $reqs = 'No requirements !';
}
$C_interp_rpu = $acttObj->unique_data('booking_type', 'rate', 'title', $bookinType) ?: 0;
if ($asignDate > date('Y-m-d')) {
    $problem_hours = 1;
    $problem_msg = 'Assignment Date : <b class="text-danger">' . $asignDate . '</b><br><br>This job is not completed yet! Thank you';
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
    <title>Client Expenses - Translation Interpreting</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" />
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
            var numberUnit = parseFloat(document.getElementById('numberUnit').value);
            var rpU = parseFloat(document.getElementById('rpU').value);
            var x = rpU * numberUnit;
            document.getElementById('total_interp').value = parseFloat(x).toFixed(2);
            var deduction = parseFloat(document.getElementById('deduction').value);
            var admnchargs = parseFloat(document.getElementById('admnchargs').value);
            var otherCharges = parseFloat(document.getElementById('otherCharg').value);
            total_charges_interp.value = (parseFloat(x + otherCharges + admnchargs) - parseFloat(deduction)).toFixed(2);
        }

        function checkDec(el) {
            var ex = /^[0-9]+\.?[0-9]*$/;
            if (ex.test(el.value) == false) {
                el.value = 0;
                el.select();
                calcInterp();
                C_calcInterp();
                int_units();
                company_units();
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
            var C_numberUnit = parseFloat(document.getElementById('C_numberUnit').value);
            var C_rpU = parseFloat(document.getElementById('C_rpU').value);
            var certificationCost = parseFloat(document.getElementById('certificationCost').value);
            var proofCost = parseFloat(document.getElementById('proofCost').value);
            var postageCost = parseFloat(document.getElementById('postageCost').value);
            var x = C_rpU * C_numberUnit;
            total_units.value = parseFloat(x).toFixed(2);
            /*var C_numberWord = parseFloat(document.getElementById('C_numberWord').value); 
            var C_rpW = parseFloat(document.getElementById('C_rpW').value);
            var y = C_rpW * C_numberWord;
            total_words.value=parseFloat(y).toFixed(2);*/
            var C_otherCharges = parseFloat(document.getElementById('C_otherCharg').value);
            var C_admnchargs = parseFloat(document.getElementById('C_admnchargs').value);
            total_charges_comp.value = parseFloat(x + C_otherCharges + certificationCost + proofCost + postageCost + C_admnchargs).toFixed(2);
        }

        function int_units() {
            if (parseFloat($('#numberUnit').val()) == 0) {
                $('#numberUnit').addClass('cls_danger');
                $('#numberUnit').attr('title', 'Number units must be greater than 0');
                $('#btn_submit_expense').attr("disabled", "disabled");
            } else {
                $('#numberUnit').removeClass('cls_danger');
                $('#btn_submit_expense').removeAttr("disabled");
            }
        }

        function company_units() {
            var actual_units = parseFloat('<?php echo $chk_numberUnit; ?>');
            var show_title = '';
            if (parseFloat(actual_units) == 0) {
                show_title = 'Number Units must be greater than 0';
            } else {
                show_title = 'Number Units must be atleast ' + actual_units;
            }
            if (parseFloat($('#C_numberUnit').val()) < actual_units || parseFloat($('#C_numberUnit').val()) == 0) {
                $('#C_numberUnit').addClass('cls_danger');
                $('#C_numberUnit').attr('title', show_title);
                $('#btn_company_expense').attr("disabled", "disabled");
            } else {
                $('#C_numberUnit').removeClass('cls_danger');
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
                    <td width="25%">Assignment Date : <?php echo $asignDate; ?></td>
                    <td>Delivery Type : <?php echo $deliveryType; ?></td>
                    <td>Timesheet Status : <?php echo $numberUnit == 0 ? 'Not Filled Yet !' : $numberUnit . $trans_multi_label . ' (' . $trans_single_label . ' count)'; ?></td>
                </tr>
                <tr>
                    <td>Timesheet Updated VIA : <?php echo $hoursWorkd == 0 ? 'Not Updated' : $array_added_via[$row['added_via']];
                                                echo !is_null($row['int_sign_date']) ? "<br>Signed on : " . $row['int_sign_date'] : "";
                                                echo !empty($row['int_sig']) && $row['int_sig'] != 'i_default.png' ? "<a href='../file_folder/interpreter_signatures/" . $row['int_sig'] . "' target='_blank' title='Click to view interpreter signature' class='btn btn-success btn-xs'>View Interpreter Signature</a>" : ""; ?></td>
                    <td>Interpreter <?php echo $trans_multi_label; ?> Filled</td>
                    <td>Requirements : <?php echo $reqs; ?></td>
                </tr>
            </tbody>
        </table>
        <ul class="nav nav-tabs">
            <li class="active"><a data-toggle="tab" href="#int_hours"><i class="fa fa-user"></i> Interpreter Expenses</a></li>
            <?php if ($action_update_company_expenses) { ?><li><a data-toggle="tab" href="#comp_hours"><i class="fa fa-briefcase"></i> Company Expenses</a></li><?php } ?>
        </ul>

        <div class="tab-content">
            <div id="int_hours" class="tab-pane fade in active">
                <br>
                <div class="col-md-12">
                    <form action="" method="post" class="register">
                        <div class="col-xs-12 text-center">
                            <h4>Face To Face - Update Interpreter Expenses For <span style="color:#F00;"><?php echo $interp_name . ' ( ' . $asignDate . ' )'; ?></span>
                                <?php if ($_SESSION['userId'] == 1) { ?>
                                    <label for="skip_rate" class="btn btn-info btn-xs pull-right"><input id='skip_rate' type="checkbox" /> Skip Rate limitation</label>
                                <?php } ?>
                            </h4>
                        </div>
                        <div class="bg-info col-xs-12 form-group">
                            <h4>Fixed Rate or Per <?php echo $trans_single_label; ?> Rate (As Agreed) (Booking Type: <span style="color:#900;"><?php echo $bookinType; ?></span>)</h4>
                        </div>
                        <div class="form-group col-md-3 col-sm-6">
                            <p>Units (<?php echo $trans_single_label; ?> Count) </p>
                            <input class="form-control" name="numberUnit" type="text" pattern="[0-9]+([\.|,][0-9]+)?" step="0.01" id="numberUnit" style="border:1px solid #CCC" required='' value="<?php echo $numberUnit; ?>" oninput="calcInterp();int_units();" onkeyup="checkDec(this);" />
                        </div>
                        <div class="form-group col-md-3 col-sm-6">
                            <p> Rate per <?php echo $trans_single_label; ?> </p>
                            <input name="rpU" type="text" id="rpU" class="form-control" required='' value="<?php if ($rpU != 0 || !empty($hrsubmited)) {
                                                                                                                echo $rpU;
                                                                                                            } else {
                                                                                                                echo $interp_rpu;
                                                                                                            } ?>" oninput="calcInterp()" onkeyup="checkDec(this);" />
                        </div>
                        <div class="form-group col-md-3 col-sm-6">
                            <p> Total for Interpreting Time </p>
                            <input name="total_interp" type="text" id="total_interp" class="form-control" disabled value="<?php if ($rpU != 0) {
                                                                                                                                echo $rpU * $numberUnit;
                                                                                                                            } ?>" onkeyup="checkDec(this);" />
                        </div>
                        <div class="form-group col-md-3 col-sm-6">
                            <p> Any other Charges (If Applicable) </p>
                            <input class="form-control" name="otherCharg" type="text" pattern="[0-9]+([\.|,][0-9]+)?" step="0.01" id="otherCharg" required='' value="<?php echo $otherCharg; ?>" oninput="calcInterp()" onkeyup="checkDec(this);" />
                        </div>
                        <div class="form-group col-md-3 col-sm-6">
                            <p>Additional Payment (If Applicable)
                            </p>
                            <input class="form-control" name="admnchargs" type="text" id="admnchargs" pattern="[0-9]+([\.|,][0-9]+)?" step="0.01" value="<?php echo $admnchargs ?>" placeholder='' oninput="calcInterp()" onkeyup="checkDec(this);" />
                        </div>
                        <div class="form-group col-md-3 col-sm-6">
                            <p>Due Date for Bill Payment
                            </p>
                            <input class="form-control" name="dueDate" type="date" id="dueDate" value="<?php echo $dueDate ?>" />
                        </div>
                        <div class="form-group col-md-3 col-sm-6">
                            <p>Deduction (If Applicable) </p>
                            <input class="form-control" name="deduction" type="text" id="deduction" pattern="[0-9]+([\.|,][0-9]+)?" step="0.01" value="<?php echo $deduction ?>" placeholder='' oninput="calcInterp()" onkeyup="checkDec(this);" />
                        </div>
                        <div class="form-group col-md-3 col-sm-6">
                            <p>National Insurance Deduction <i class="glyphicon glyphicon-question-sign" title="(If Applicable)"></i></p>
                            <input class="form-control" name="ni_dedu" type="text" id="ni_dedu" pattern="[0-9]+([\.|,][0-9]+)?" step="0.01" value="<?php echo $ni_dedu ?>" placeholder='' oninput="calcInterp()" onkeyup="checkDec(this);" />
                        </div>
                        <div class="form-group col-md-3 col-sm-6">
                            <p>Tax Deduction (If Applicable)
                            </p>
                            <input class="form-control" name="tax_dedu" type="text" id="tax_dedu" pattern="[0-9]+([\.|,][0-9]+)?" step="0.01" value="<?php echo $tax_dedu ?>" placeholder='' oninput="calcInterp()" onkeyup="checkDec(this);" />
                        </div>
                        <div class="form-group col-md-3 col-sm-6">
                            <p style="color:#F00">Current VAT % </p>
                            <input class="form-control" name="int_vat" type="text" pattern="[0-9]+([\.|,][0-9]+)?" step="0.01" id="int_vat" value="<?php echo $int_vat ?: 0; ?>" placeholder='' oninput="calcInterp();fun_vat_no();" onkeyup="checkDec(this);" required='' />
                        </div>
                        <div class="form-group col-md-3 col-sm-6" <?php if (!empty($int_vat) && $int_vat != 0) {
                                                                        echo 'style="display:inline"';
                                                                    } else {
                                                                        echo 'style="display:none"';
                                                                    } ?> id="div_vat_no">
                            <p style="color:#F00">VAT Number (if any) </p>
                            <input class="form-control" name="vat_no_int" type="text" id="vat_no_int" value="<?php echo $vat_no_int; ?>" placeholder='' />
                        </div>
                        <div class="form-group col-md-3 col-sm-6">
                            <p><b>Total</b> </p>
                            <input class="form-control" name="total_charges_interp" type="text" pattern="[0-9]+([\.|,][0-9]+)?" step="0.01" id="total_charges_interp" value="<?php echo number_format($total_charges_interp + $admnchargs, 2) ?>" readonly="readonly" />
                        </div>
                        <div class="form-group col-sm-6">
                            <textarea class="form-control" placeholder="Notes (if Any) 1000 characters" class="form-control" name="exp_remrks" rows="3" id="exp_remrks"><?php echo $exp_remrks ?></textarea>
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
                                    <input type="hidden" name="action_approve_expenses" value="1"/>
                                    <p class="text-danger label_not_approved">Timesheet uploaded by interpreter but not approved by any staff user yet!</p>
                                <?php } else { ?>
                                    <br>    
                                <?php } ?>
                                <button class="btn btn-<?=$row['hrsubmited'] == 'Self' && $row['approved_flag'] == 0 ? 'success' : 'info'?>" style="<?=$row['hrsubmited'] == 'Self' && $row['approved_flag'] == 0 ? 'border-color: #000000;color: black;text-transform: uppercase;font-size: 20px;font-weight: bold;box-shadow: 2px 2px 2px #c5c5a3;' : 'border-color: #000000;color: black;text-transform: uppercase;font-size: 20px;font-weight: bold;box-shadow: 2px 2px 2px #c5c5a3;'?>" type="submit" name="btn_submit_expense" id="btn_submit_expense"><?=$row['hrsubmited'] == 'Self' && $row['approved_flag'] == 0 ? 'Approve Hours' : 'Submit'?> &raquo;</button>
                            <?php } ?>
                            </p>
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
                                <h4>Translation - Update Client Expenses For Invoicing: <span style="color:#F00;"><?php echo $orgName . ' ( ' . $asignDate . ' )'; ?></span></h4>
                            </div>
                            <div class="bg-info col-xs-12 form-group">
                                <h4>Fixed Rate or Per Word Rate (As Agreed) (Booking Type: <span style="color:#900;"><?php echo $bookinType; ?></span>)</h4>
                            </div>
                            <div class="form-group col-md-4 col-sm-3">
                                <p>Number of <?php echo $trans_multi_label; ?> (<?php $value = $chk_numberUnit == 0 ? 'greater than 0' : $chk_numberUnit . $trans_multi_label;
                                                                                echo 'Must be ' . $value; ?>)</p>
                                <input class="form-control" name="C_numberUnit" type="text" pattern="[0-9]+([\.|,][0-9]+)?" step="0.01" id="C_numberUnit" style="border:1px solid #CCC" required='' value="<?php echo $C_numberUnit; ?>" oninput="C_calcInterp();company_units();" onkeyup="checkDec(this);" />
                            </div>
                            <div class="form-group col-md-4 col-sm-3">
                                <p>Rate Per <?php echo $trans_single_label; ?> </p>
                                <input class="form-control" name="C_rpU" type="text" pattern="[0-9]+([\.|,][0-9]+)?" step="0.01" id="C_rpU" style="border:1px solid #CCC" required='' value="<?php if ($C_rpU != 0) {
                                                                                                                                                                                                    echo $C_rpU;
                                                                                                                                                                                                } else {
                                                                                                                                                                                                    echo $C_interp_rpu;
                                                                                                                                                                                                } ?>" oninput="C_calcInterp()" onkeyup="checkDec(this);" />
                            </div>
                            <div class="form-group col-md-4 col-sm-3">
                                <p><b>Total Value</b></p>
                                <input class="form-control" name="total_units" type="text" pattern="[0-9]+([\.|,][0-9]+)?" step="0.01" id="total_units" value="<?php echo $total_units ?>" oninput="C_calcInterp()" onkeyup="checkDec(this);" readonly />
                            </div>
                            <!--<div class="form-group col-md-4 col-sm-3">
<p>Number of Words </p>
<input class="form-control" name="C_numberWord" type="text" pattern="[0-9]+([\.|,][0-9]+)?" step="0.01" id="C_numberWord" style="border:1px solid #CCC" required='' value="<?php echo $C_numberWord; ?>" oninput="C_calcInterp()" onkeyup="checkDec(this);"/>
</div>
<div class="form-group col-md-4 col-sm-3">
<p>Rate Per Word </p>
<input class="form-control" name="C_rpW" type="text" pattern="[0-9]+([\.|,][0-9]+)?" step="0.01" id="C_rpW" style="border:1px solid #CCC" required='' value="<?php echo $C_rpW; ?>" oninput="C_calcInterp()" onkeyup="checkDec(this);"/>
</div>
  <div class="form-group col-md-4 col-sm-3">
     <p><b>Total Value</b></p>
     <input class="form-control" name="total_words" type="text"  pattern="[0-9]+([\.|,][0-9]+)?" step="0.01" id="total_words" value="<?php echo $total_words ?>" oninput="C_calcInterp()"  onkeyup="checkDec(this);" readonly/>
 </div>-->
                            <div class="form-group col-md-4 col-sm-3">
                                <p>CERTIFICATION COST (If Applicable) (£) </p>
                                <input class="form-control" name="certificationCost" type="text" pattern="[0-9]+([\.|,][0-9]+)?" step="0.01" id="certificationCost" style="border:1px solid #CCC" required='' value="<?php echo $certificationCost; ?>" oninput="C_calcInterp()" onkeyup="checkDec(this);" />
                            </div>
                            <div class="form-group col-md-4 col-sm-3">
                                <p>PROOFREADING COST(If Applicable) (£) </p>
                                <input class="form-control" name="proofCost" type="text" pattern="[0-9]+([\.|,][0-9]+)?" step="0.01" id="proofCost" style="border:1px solid #CCC" required='' value="<?php echo $proofCost; ?>" oninput="C_calcInterp()" onkeyup="checkDec(this);" />
                            </div>
                            <div class="form-group col-md-4 col-sm-3">
                                <p>POSTAGE COST (If Applicable) (£) </p>
                                <input class="form-control" name="postageCost" type="text" pattern="[0-9]+([\.|,][0-9]+)?" step="0.01" id="postageCost" required='' value="<?php echo $postageCost; ?>" oninput="C_calcInterp()" onkeyup="checkDec(this);" />
                            </div>
                            <div class="form-group col-md-4 col-sm-3">
                                <p> ANY OTHER CHARGES (If Applicable) (£) </p>
                                <input class="form-control" name="C_otherCharg" type="text" pattern="[0-9]+([\.|,][0-9]+)?" step="0.01" id="C_otherCharg" required='' value="<?php echo $C_otherCharg; ?>" oninput="C_calcInterp()" onkeyup="checkDec(this);" />
                            </div>
                            <div class="form-group col-md-4 col-sm-3">
                                <p> ADMIN CHARGES (<?php echo $admin_ch == 1 ? '<span class="label label-danger">Must be filled</span>' : 'Not Applicable'; ?>) </p>
                                <input <?php if ($admin_ch == 1) {
                                            echo 'title="Admin charge must be filled!" style="border:1px solid red;"';
                                        } ?> class="form-control" name="C_admnchargs" type="text" pattern="[0-9]+([\.|,][0-9]+)?" step="0.01" id="C_admnchargs" required='' value="<?php echo $C_admnchargs; ?>" oninput="C_calcInterp()" onkeyup="checkDec(this);" />
                            </div>
                            <div class="form-group col-sm-3">
                                <p style="color:#F00">Current VAT @ % </p>
                                <input class="form-control" name="cur_vat" type="text" pattern="[0-9]+([\.|,][0-9]+)?" step="0.01" id="cur_vat" value="<?php if ($cur_vat == 0) {
                                                                                                                                                            echo 0.2;
                                                                                                                                                        } else {
                                                                                                                                                            echo $cur_vat;
                                                                                                                                                        } ?>" placeholder='' required='' oninput="C_calcInterp();C_fun_vat_no();" onkeyup="checkDec(this);" />
                            </div>
                            <div class="form-group col-sm-3" <?php if (!empty($cur_vat) && $cur_vat != 0) {
                                                                    echo 'style="display:block"';
                                                                } else {
                                                                    echo 'style="display:none"';
                                                                } ?> id="C_div_vat_no">
                                <p style="color:#F00">VAT Number (if any) </p>
                                <input class="form-control" name="vat_no_comp" type="text" id="vat_no_comp" value="<?php echo $vat_no_comp; ?>" placeholder='Write VAT Number' />
                            </div>
                            <div class="form-group col-sm-3">
                                <p><strong><em>Purchase Order No.</em></strong></p>
                                <input class="form-control" name="porder" type="text" id="porder" value="<?php echo $porder ?>" placeholder='' readonly="readonly" />
                            </div>
                            <div class="form-group col-sm-3">
                                <p> <b>Job Total</b> </p>
                                <input class="form-control" name="total_charges_comp" type="text" pattern="[0-9]+([\.|,][0-9]+)?" step="0.01" id="total_charges_comp" value="<?php echo $total_charges_comp ?>" readonly="readonly" />
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
                                    <button class="btn btn-info" style="border-color: #000000;color: black;text-transform: uppercase;font-size: 20px;font-weight: bold;box-shadow: 2px 2px 2px #c5c5a3;" type="submit" name="btn_company_expense" id="btn_company_expense">Submit &raquo;</button>
                                <?php } ?>
                            </div>
                        </form>
                    </div>
                </div>
            <?php } ?>
        </div>
    <?php } ?>
</body>
<script>
    $('#rpU').change(function() {
        var lang = '<?php echo $row["source"] ?>';
        // if (!$('#skip_rate').is(":checked")) {
        //     if ($(this).val() < 0.01) {
        //         alert("Rate value cannot be less then 0.01!");
        //         $('#btn_submit_expense').attr('disabled', 'disabled');
        //         $(this).select();
        //     }
        // }
        if (lang != 'Sign Language (BSL)' && $(this).val() > 0.20) {
            alert("Rate value cannot be greater then 0.20!");
            $('#btn_submit_expense').attr('disabled', 'disabled');
            $(this).select();
        }
        // if ($(this).val() >= 0.01 && (lang != 'Sign Language (BSL)' && $(this).val() <= 0.20)) {
        //     $('#btn_submit_expense').removeAttr('disabled');
        // }
        if (lang != 'Sign Language (BSL)' && $(this).val() <= 0.20) {
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