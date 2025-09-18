<?php if (session_id() == '' || !isset($_SESSION)) {
    session_start();
}
error_reporting(0);
//php mailer library
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require 'lsuk_system/phpmailer/vendor/autoload.php';
$mail = new PHPMailer(true);
function round_quarter($num, $parts)
{
    $res = $num * $parts;
    $res = ceil($res);
    return $res / $parts;
}

include 'source/db.php';
include 'source/class.php';
$table = 'interpreter';
$update_id = @$_GET['update_id'];
$row = $acttObj->read_specific(
    "$table.*,interpreter_reg.ratetravelworkmile,interpreter_reg.ratetravelexpmile,interpreter_reg.rph,interpreter_reg.email as int_email",
    "$table,interpreter_reg",
    "$table.intrpName=interpreter_reg.id AND $table.id=$update_id"
);
$intrpName = $row['intrpName'];
$bookinType = $row['bookinType'];
$hoursWorkd = $row['hoursWorkd'];
$chargInterp = $row['chargInterp'];
$travelMile = $row['travelMile'];
$chargeTravel = $row['chargeTravel'];
$travelCost = $row['travelCost'];
$otherCost = $row['otherCost'];
$travelTimeHour = $row['travelTimeHour'];
$rateHour = $row['rateHour'] != 0 ?: $row['rph'];
$rateMile = $row['rateMile'] != 0 ?: $row['ratetravelexpmile'];
$travelTimeRate = $row['travelTimeRate'] != 0 ?: $row['ratetravelworkmile'];
$chargeTravelTime = $row['chargeTravelTime'];
$tAmount = $row['tAmount'];
$admnchargs = $row['admnchargs'];
$total_charges_interp = $row['total_charges_interp'];
$old_time_sheet = $row['time_sheet'];
$wt_tm = $row['wt_tm'];
$st_tm = $row['st_tm'];
$fn_tm = $row['fn_tm'];
$int_email = $row['int_email'];
$assignDur = $row['assignDur'];
$valid_check_q = $acttObj->unique_dataAnd($table, 'id', 'intrpName', $_SESSION['web_userId'], 'id', $update_id);
$valid_check = $valid_check_q != '' ? 'yes' : 'no';
if ($valid_check == 'no') {
    echo '<script>window.location.href="index.php";</script>';
}
if (date('Y-m-d H:i', strtotime($row['assignDate'] . ' ' . $row['assignTime'])) > date('Y-m-d H:i')) {
    $problem_hours = 1;
    $problem_msg = 'This job is not completed yet! Thank you';
} else if ($hoursWorkd > 0) {
    $problem_hours = 1;
    $problem_msg = 'Hours for this job already updated! Thank you';
} else if ($row['deleted_flag'] == 1 || $row['order_cancel_flag'] == 1 || $row['orderCancelatoin'] == 1 || $row['intrp_salary_comit'] == 1) {
    $problem_hours = 1;
    $problem_msg = 'This job is in processing mode! Thank you';
} else {
    $problem_hours = 0;
    $problem_msg = '';
}
if (isset($_POST['submit'])) {
    error_reporting(0);
    if ($_FILES["time_sheet"]["name"] == NULL && empty($old_time_sheet)) {
        echo "<script>alert('You must provide an updated copy of your Timehseet!Please upload your timesheet & try again');</script>";
    } else {
        if (!empty($_POST['hoursWorkd']) && $_POST['hoursWorkd'] != 0) {
            $date_1 = $_POST['date_1'];
            $date_2 = $_POST['date_2'];
            $t1 = strtotime($_POST['date_1']);
            $t2 = strtotime($_POST['date_2']);
            $diff = $t2 - $t1;
            $hours = $diff / 3600;
            $post_hours = ($hours) < $assignDur / 60 ? $assignDur / 60 : round_quarter($hours, 4);
            $post_chargInterp = $post_hours * $rateHour;
            $post_travelTimeHour = $_POST['travelTimeHour'];
            $total_travel_time = $post_travelTimeHour * $travelTimeRate;
            $post_travelMile = $_POST['travelMile'];
            $post_travelCost = $_POST['travelCost'];
            $post_otherCost = $_POST['otherCost'];
            $post_admnchargs = 0.50;
            $update_array = array(
                'hoursWorkd' => $post_hours, 'rateHour' => $rateHour, 'chargInterp' => $post_chargInterp, 'travelTimeHour' => $post_travelTimeHour, 'travelTimeRate' => $travelTimeRate,
                'chargeTravelTime' => $total_travel_time, 'st_tm' => $date_1, 'fn_tm' => $date_2, 'tm_by' => 'i', "added_via" => 2, 'int_sig' => 'i_default.png'
            );
            if ($_POST['transport_type'] == "public") {// Public transport
                $post_travelMile = 0;
                $post_otherCost = 0;
                if ($post_travelCost > 0 && $_FILES["interpreter_t_file"]["name"] != NULL) {
                    $check_existing = $acttObj->read_all("id,file_name", "job_files", "tbl='" . $table . "' AND order_id=" . $update_id . " AND file_type='timesheet'");
                    if ($check_existing->num_rows > 0) {
                        while ($row_existing = $check_existing->fetch_assoc()) {
                            $old_expenses_file = "file_folder/job_files/" . $row_existing['file_name'];
                            if (file_exists($old_expenses_file) && !empty($old_expenses_file)) {
                                unlink($old_expenses_file);
                            }
                            $acttObj->delete("job_files", "id = " . $row_existing['id']);
                        }
                    }
                    $is_receipt_uploaded = false;
                    for ($i = 0; $i < count($_FILES['interpreter_t_file']['tmp_name']); $i++) {
                        $picName = $acttObj->upload_file("file_folder/job_files", $_FILES["interpreter_t_file"]["name"][$i], $_FILES["interpreter_t_file"]["type"][$i], $_FILES["interpreter_t_file"]["tmp_name"][$i], round(microtime(true)) . $i);
                        $data = array('tbl' => $table, 'file_name' => $picName, 'order_id' => $update_id, 'interpreter_id' => $_SESSION['web_userId'], 'dated' => date('Y-m-d H:i:s'));
                        $acttObj->insert('job_files', $data);
                        $is_receipt_uploaded = true;
                    }
                    $transport_uploaded = $is_receipt_uploaded ? "Your public transport attachments have been uploaded." : "Failed to upload your public transport attachments";
                }
                if (!$is_receipt_uploaded) {
                    $post_travelCost = 0;
                }
            } else {// Private transport
                $post_travelCost = 0;
                if ($post_otherCost > 0 && $_FILES["interpreter_file"]["name"] != NULL) {
                    $get_parking_tickets = $acttObj->read_specific("parking_tickets","interpreter","id=" . $update_id)["parking_tickets"];
                    if(!empty($get_parking_tickets)){
                        $existing_parking_tickets = json_decode($get_parking_tickets, true);
                        foreach($existing_parking_tickets as $p_key => $p_val){
                            $old_parking_file = "file_folder/parking_tickets/" . $p_val;
                            if(file_exists($old_parking_file) && !empty($old_parking_file)){
                                unlink($old_parking_file);
                            }
                        }
                    }
                    $is_parking_uploaded = false;
                    $array_parking_files = array();
                    for ($ti = 0; $ti < count($_FILES['interpreter_file']['tmp_name']); $ti++) {
                        $picName = $acttObj->upload_file("file_folder/parking_tickets", $_FILES["interpreter_file"]["name"][$ti], $_FILES["interpreter_file"]["type"][$ti], $_FILES["interpreter_file"]["tmp_name"][$ti], round(microtime(true)) . $ti);
                        $array_parking_files[] = $picName;
                        $is_parking_uploaded = true;
                    }
                    if ($is_parking_uploaded) {
                        $update_array['parking_tickets'] = json_encode($array_parking_files);
                        $update_array['is_parking'] = 1;
                        $parking_expense_uploaded = "Your private expense attachments have been uploaded.";
                    } else {
                        $post_travelMile = 0;
                        $post_otherCost = 0;
                        $parking_expense_uploaded = "Failed to upload your private transport attachments.";
                    }
                }
            }
            $total_travel_miles = $post_travelMile * $rateMile;
            $net_total = number_format($post_chargInterp + $total_travel_time + $total_travel_miles + $post_travelCost + $post_otherCost + $post_admnchargs, 2);
            if ($_FILES["time_sheet"]["name"] != NULL) {
                if (!empty($old_time_sheet)) {
                    unlink('file_folder/time_sheet_interp/' . $old_time_sheet);
                }
                $picName = $acttObj->upload_file("file_folder/time_sheet_interp", $_FILES["time_sheet"]["name"], $_FILES["time_sheet"]["type"], $_FILES["time_sheet"]["tmp_name"], round(microtime(true)));
                $update_array['time_sheet'] = $picName;
                if (empty($old_time_sheet)) {
                    if ($intrpName != 874) { 
                        try {
                            $to_add = $int_email;
                            //$to_add = "waqarecp1992@gmail.com";
                            $from_add = "info@lsuk.org";
                            $mail->SMTPDebug = 0;
                            $mail->isSMTP(); 
                            $mail->Host = 'smtp.office365.com';
                            $mail->SMTPAuth   = true;
                            $mail->Username   = 'info@lsuk.org';
                            $mail->Password   = 'xtxwzcvtdbjpftdj';
                            $mail->SMTPSecure = 'tls';
                            $mail->Port       = 587;
                            $mail->setFrom($from_add, 'LSUK Timehseet Confirmation');
                            $mail->addAddress($to_add);
                            $mail->addReplyTo($from_add, 'LSUK');
                            $mail->isHTML(true);
                            $mail->Subject = 'Confirmation of timesheet upload for interpreting assignment';
                            $mail->Body    = 'Dear Linguist!<br>You have successfully uploaded your timesheet for Job.<br>Thank you<br>Best Regards<br>LSUK Limited';
                            if ($mail->send()) {
                                $mail->ClearAllRecipients();
                            }
                        } catch (Exception $e) {
                        }
                    }
                }
            }
            // Add rest of calculations
            $update_array_2 = array(
                'travelMile' => $post_travelMile, 'rateMile' => $rateMile, 'chargeTravel' => $total_travel_miles, 'travelCost' => $post_travelCost, 'admnchargs' => $post_admnchargs,
                'otherCost' => $post_otherCost, 'total_charges_interp' => $net_total, 'approved_flag' => 0, 'hrsubmited' => "Self", 'interp_hr_date' => date("Y-m-d")
            );
            $update_array = array_merge($update_array, $update_array_2);
            // Update job data now
            $acttObj->update($table, $update_array, array("id" => $update_id));
            $problem_hours = 1;
            $problem_msg = 'Hours for this job already updated! Thank you';
            echo "<script>alert('Your timesheet has been successfuly updated. " . $transport_uploaded . $parking_expense_uploaded . "');</script>";
        } else {
            echo "<script>alert('Kindly update hours worked value! Thank you');</script>";
        }
    }
} ?>
<!DOCTYPE HTML>
<html class="no-js">
<style>
    #dvPreview img, #t_dvPreview img {
        border: 2px solid black;
        margin: 4px;
    }
</style>
<head>
    <?php include 'source/header.php'; ?>
    <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
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
            var z = travelTimeHour * travelTimeRate;
            chargeTravelTime.value = z.toFixed(2);

            var otherCost = parseFloat(document.getElementById('otherCost').value);
            var admnchargs = 0.50;
            var travelCost = parseFloat(document.getElementById('travelCost').value);

            totalChages.value = (parseFloat(x + y + z + travelCost + otherCost + admnchargs)).toFixed(2);
        }

        function checkDec(el) {
            var ex = /^[0-9]+\.?[0-9]*$/;
            if (ex.test(el.value) == false) {
                el.value = 0;
                el.select();
                calcInterp();
            }
        }
    </script>
</head>

<body class="boxed">
    <!-- begin container -->
    <div id="wrap">
        <!-- begin header -->
        <?php include 'source/top_nav.php';
        if ($problem_hours == 1) { ?>
            <center><br><br>
                <h3><?php echo isset($problem_msg) && !empty($problem_msg) ? $problem_msg : ''; ?></h3>
                <br><br><a class="button" href="time_sheet_interp.php"><i class="glyphicon glyphicon-arrow-left"></i> Go Back</a>
            </center>
        <?php } else { ?>
            <section id="page-title">
                <div class="container clearfix">
                    <h1 style="font-size:18px">Upload Face to Face Assignment Expenses</h1>
                    <nav id="breadcrumbs">
                        <ul>
                            <li><a href="index.php">Home</a> &rsaquo;</li>
                            <li><a href="<?php echo basename($_SERVER['HTTP_REFERER']); ?>"><?php echo ucwords(basename($_SERVER['HTTP_REFERER'], '.php')); ?></a> &rsaquo;</li>
                            <li><?php echo ucwords(basename($_SERVER['REQUEST_URI'], '.php')); ?></li>
                        </ul>
                    </nav>
                </div>
            </section>
            <section id="content" class="container clearfix">
                <form id="first_form" action="#" method="post" enctype="multipart/form-data">
                    <center>
                        <h4 class="alert alert-success">0.50 has been added to your pay for online timesheet submission</h4>
                    </center>
                    <div class="row">
                        <div class='col-sm-4'>
                            <div class="form-group">
                                <label class="input">Assignment Start Time <span title="Expected start time for assignment has been placed. Update start time for this assignment if different." class="fa fa-question-circle"></span></label>
                                <div class='input-group date datetimepicker'>
                                    <input value="<?php echo $row['assignDate'] . ' ' . $row['assignTime']; ?>" id="date_1" name="date_1" type='text' class="form-control" required /><span class="input-group-addon">
                                        <span class="glyphicon glyphicon-calendar"></span> Click to change
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class='col-sm-4'>
                            <div class="form-group">
                                <label class="input">Assignment End Time <span title="Expected end time for assignment has been placed. Update end time for this assignment if different." class="fa fa-question-circle"></span></label>
                                <div class='input-group date datetimepicker'>
                                    <?php $expected_end_date = $row['assignDate'] . ' ' . $row['assignTime'];
                                    $expected_end_date = strtotime($expected_end_date);
                                    $expected_end_date = strtotime("+" . $assignDur . " minute", $expected_end_date);
                                    $expected_end_date = date('Y-m-d H:i', $expected_end_date); ?>
                                    <input value="<?php echo $expected_end_date; ?>" id="date_2" name="date_2" type='text' class="form-control" required /><span class="input-group-addon">
                                        <span class="glyphicon glyphicon-calendar"></span> Click to change
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class='col-sm-4'>
                            <div class="form-group">
                                <h2><span class="label label-info duration_label"></span></h2>
                            </div>
                        </div>
                        <hr>
                        <div class="form-group col-md-4">
                            <label class="input">Hours Worked <span title="Expected workout duration has been placed. Update start and end time if different." class="fa fa-question-circle"></span></label>
                            <input readonly class="form-control" name="hoursWorkd" type="text" id="hoursWorkd" onkeyup="checkDec(this);" pattern="[0-9]+([\.|,][0-9]+)?" step="0.01" value="<?php echo number_format($assignDur / 60, 2); ?>" />
                        </div>
                        <div class="form-group col-md-4">
                            <label class="input">Rate Per Hour</label>
                            <input class="form-control" readonly name="rateHour" type="number" min="10" <?php if ($row["source"] != "Sign Language (BSL)") { ?> max="40" <?php } ?> id="rateHour" oninput="calcInterp()" onkeyup="checkDec(this);" pattern="[0-9]+([\.|,][0-9]+)?" step="0.01" value="<?php echo $rateHour; ?>" />
                        </div>
                        <div class="form-group col-md-4">
                            <label class="input">Charge for Interpreting Time <i class="fa fa-question-circle" title="Minimum 1 hour , additional time in incremental units example 1 hour 5 minutes is 1.25 , 1 hour 20 minutes is 1.50 hour,  1 hour 35 minutes is 1.75 and 1 hour 50 minutes is 2 hours"></i></label>
                            <input class="form-control bg-info" name="chargInterp" type="text" id="chargInterp" readonly value="<?php echo $chargInterp != 0 ?: ($assignDur / 60) * $rateHour; ?>" />
                        </div>
                    </div>

                    <div class="row">
                        <div class="form-group col-md-4">
                            <label class="input">Travel Time Hours <i class="fa fa-question-circle" title="Minimum 1 hour , additional time in incremental units example 1 hour 5 minutes is 1.25 , 1 hour 20 minutes is 1.50 hour,  1 hour 35 minutes is 1.75 and 1 hour 50 minutes is 2 hours"></i></label>
                            <input class="form-control" name="travelTimeHour" type="text" placeholder='' oninput="calcInterp()" onkeyup="checkDec(this);" pattern="[0-9]+([\.|,][0-9]+)?" step="0.01" id="travelTimeHour" value="<?php echo $travelTimeHour; ?>" />
                        </div>
                        <div class="form-group col-md-4">
                            <label class="input">Travel Time Rate Per Hour</label>
                            <input readonly class="form-control" name="travelTimeRate" type="text" placeholder='' oninput="calcInterp()" onkeyup="checkDec(this);" pattern="[0-9]+([\.|,][0-9]+)?" step="0.01" id="travelTimeRate" value="<?php echo $travelTimeRate; ?>" />
                        </div>
                        <div class="form-group col-md-4">
                            <label class="input">Charge for Travel Time</label>
                            <input class="form-control bg-info" name="chargeTravelTime" type="text" placeholder='' oninput="calcInterp()" onkeyup="checkDec(this);" pattern="[0-9]+([\.|,][0-9]+)?" step="0.01" id="chargeTravelTime" readonly value="<?php echo $chargeTravelTime; ?>" />
                        </div>

                    </div>
                    <div class="row">
                        <div class="form-group col-md-4">
                            <label>Select Transport Type * <span class="label label-danger">Required</span></label>
                            <label class="btn btn-default"><input onchange="toggle_transport(this)"  <?= $travelCost > 0 ? 'checked' : ''; ?> type="radio" value="public" name="transport_type" required> Public Transport</label>
                            <label class="btn btn-default"><input onchange="toggle_transport(this)"  <?= $travelMile > 0 || $otherCost > 0 ? 'checked' : ''; ?> type="radio" value="private" name="transport_type" required> Private Transport</label>
                        </div>
                    </div>
                    <div class="div_transport row <?=$total_charges_interp > 0 ? '' : 'hidden'?>">
                        <div class="form-group col-md-4 div_private_transport <?= $travelMile > 0 || $otherCost > 0 ? '' : 'hidden'; ?>">
                            <label class="input">Travel Mileage</label>
                            <input class="form-control transport_fields" name="travelMile" type="text" placeholder='' oninput="calcInterp()" onkeyup="checkDec(this);" pattern="[0-9]+([\.|,][0-9]+)?" step="0.01" id="travelMile" value="<?php echo $travelMile; ?>" />
                        </div>
                        <div class="form-group col-md-4 div_private_transport <?= $travelMile > 0 || $otherCost > 0 ? '' : 'hidden'; ?>">
                            <label class="input">Rate Per Mileage</label>
                            <input readonly class="form-control" name="rateMile" type="text" placeholder='' oninput="calcInterp()" onkeyup="checkDec(this);" pattern="[0-9]+([\.|,][0-9]+)?" step="0.01" id="rateMile" value="<?php echo $rateMile; ?>" />
                        </div>
                        <div class="form-group col-md-4 div_private_transport <?= $travelMile > 0 || $otherCost > 0 ? '' : 'hidden'; ?>">
                            <label class="input">Charge for Travel Cost</label>
                            <input class="form-control bg-info" name="chargeTravel" type="text" placeholder='' id="chargeTravel" readonly value="<?php echo $chargeTravel; ?>" />
                        </div>
                        <div class="form-group col-md-4 div_private_transport <?= $travelMile > 0 || $otherCost > 0 ? '' : 'hidden'; ?>">
                            <label class="input">Other Costs (Parking , Bridge Toll)</label>
                            <input class="form-control transport_fields" name="otherCost" type="text" placeholder='' oninput="calcInterp();toggle_div_ticket(this, 'div_ticket', 'fileupload')" onkeyup="checkDec(this);" pattern="[0-9]+([\.|,][0-9]+)?" step="0.01" id="otherCost" value="<?php echo $otherCost; ?>" />
                        </div>
                        <div class="form-group col-md-12 div_private_transport div_ticket <?= $otherCost > 0 ? '' : 'hidden'; ?>">
                            <div style="padding: 3px;" align="center" class="alert alert-info col-md-5">Upload Parking, Bridge Toll Files (if any)<br><small class="text-danger">Change the amount in <b>Other Cost</b> to 0 if you do not have the any receipts!</small></div>
                            <div class="form-group col-md-12" id="dvPreview"></div>
                            <div class="form-group col-md-4">
                                <label for="fileupload">Upload Attachments (Scanned / Picture) <i class="fa fa-question-circle" title="Acceptable Formats: gif, jpeg, jpg, png, pdf, doc, docx, xlsx"></i></label>
                                <input class="form-control" style="width : 350px" name="interpreter_file[]" onchange="loadFiles(event)" multiple="multiple" type="file" size="60" multiple="multiple" accept=".docx,.xlsx,.pdf,.png,.jpeg,.jpg" id="fileupload" <?= $otherCost > 0 ? 'required' : ''; ?>>
                            </div>
                        </div>
                        <div class="form-group col-md-4 div_public_transport div_travelCost <?= $travelCost > 0 ? '' : 'hidden'; ?>">
                            <label class="input">Travel Cost</label>
                            <input class="form-control transport_fields" name="travelCost" type="text" placeholder='' oninput="calcInterp();toggle_div_ticket(this, 'div_travelCost_ticket', 't_fileupload')" onkeyup="checkDec(this);" pattern="[0-9]+([\.|,][0-9]+)?" step="0.01" id="travelCost" value="<?php echo $travelCost; ?>" />
                        </div>
                        <div class="form-group col-md-12 div_travelCost_ticket <?= $travelCost > 0 ? '' : 'hidden'; ?>">
                            <div style="padding: 3px;" align="center" class="alert alert-info col-md-5">Upload Receipts, Files (if any)<br><small class="text-danger">Change the amount in <b>Travel Cost</b> to 0 if you do not have the any receipts!</small></div>
                            <div class="form-group col-md-12" id="t_dvPreview"></div>
                            <div class="form-group col-md-4">
                                <label for="t_fileupload">Upload Receipts (Scanned / Picture) <i class="fa fa-question-circle" title="Acceptable Formats: gif, jpeg, jpg, png, pdf, doc, docx, xlsx"></i></label>
                                <input class="form-control" style="width : 350px" name="interpreter_t_file[]" onchange="loadFiles(event)" multiple="multiple" type="file" size="60" multiple="multiple" accept=".docx,.xlsx,.pdf,.png,.jpeg,.jpg" id="t_fileupload" <?= $travelCost > 0 ? 'required' : ''; ?>>
                            </div>
                        </div>
                        <hr>
                        <div class="form-group col-md-4">
                            <label class="input">Total Charges</label>
                            <input class="form-control bg-success" name="totalChages" type="text" placeholder='' readonly id="totalChages" value="<?php echo @$total_charges_interp; ?>" />
                        </div>
                        <div class="form-group col-md-4">
                            <label class="input">Upload Timesheet (Scanned / Picture) <i class="fa fa-question-circle" title="Acceptable Formats: gif, jpeg, jpg, png, pdf, doc, docx, xlsx"></i></label>
                            <input class="form-control" name="time_sheet" type="file" placeholder='' id="time_sheet" <?php if ($old_time_sheet == NULL) { ?>required <?php } ?> />
                        </div>
                        <div class="form-group col-md-4">
                            <?php if (!empty($old_time_sheet)) { ?>
                                <label class="input">View your Time Sheet</label>
                                <a href="javascript:void(0)" onClick="MM_openBrWindow('timesheet_view.php?t_id=<?php echo $update_id; ?>&table=<?php echo $table; ?>','_blank','scrollbars=yes,resizable=yes,width=600,height=400,left=400,top=200')"><br><img src="lsuk_system/images/images.jpg" width="30" height="30" title="View Time Sheet"></a>
                            <?php } else { ?> <label class="text-danger">Timesheet is not uploaded!</label><br><img src="lsuk_system/images/missing.jpg" width="35" height="35" title="Time Sheet is missing for this JOB!">
                            <?php } ?>
                        </div>
                    </div>
                    <input type="submit" name="submit" class="btn btn-primary" value="Submit" />
                </form>
            </section>
            <hr>
            </section>
            <?php include 'source/footer.php'; ?>
    </div>
<?php } ?>
</body>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.15.1/moment.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.7.14/js/bootstrap-datetimepicker.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.7.14/css/bootstrap-datetimepicker.min.css">
<script type="text/javascript">
    function round_quarter(num, parts) {
        var res = num * parts;
        res = Math.ceil(res);
        return res / parts;
    }
    $(function() {
        $('.datetimepicker').datetimepicker({
            format: 'YYYY-MM-DD HH:mm',
            minDate: "<?php echo $row['assignDate'] . ' ' . $row['assignTime']; ?>"
        });
        $('.datetimepicker:eq(1)').datetimepicker().on('dp.change', function(event) {
            var dt_new_1 = $('#date_1').val().split('-');
            var tt_new_1 = dt_new_1[2].split(' ');
            dt_new_1 = dt_new_1[1] + "/" + tt_new_1[0] + "/" + dt_new_1[0] + " " + tt_new_1[1];

            var dt_new_2 = $('#date_2').val().split('-');
            var tt_new_2 = dt_new_2[2].split(' ');
            dt_new_2 = dt_new_2[1] + "/" + tt_new_2[0] + "/" + dt_new_2[0] + " " + tt_new_2[1];
            var assignDur = '<?php echo $assignDur; ?>';
            var t1 = new Date(dt_new_1);
            var t2 = new Date(dt_new_2);
            t1 = (t1.getTime() / 1000) + 20100;
            t2 = (t2.getTime() / 1000) + 20100;
            var diff = t2 - t1;
            var hours = diff / 3600;
            var result = (hours) < assignDur / 60 ? assignDur / 60 : round_quarter(hours, 4);
            $('#hoursWorkd').val(result);
            var hourss;
            var hr;
            var get_dur;
            var mins;
            result = Math.floor(result * 60);
            if (result > 60) {
                hourss = Math.floor(result / 60);
                if (Math.floor(hourss) > 1) {
                    hr = " hours";
                } else {
                    hr = " hour";
                }
                mins = Math.floor(result % 60);
                if (mins == 00) {
                    get_dur = hourss + hr;
                } else {
                    get_dur = hourss + hr + " " + mins + " minutes";
                }
            } else if (result == 60) {
                get_dur = "1 Hour";
            } else {
                get_dur = result + " minutes";
            }
            $('.duration_label').text(get_dur);
            calcInterp();
        });
        calcInterp();
    });
    window.onload = function() {
        var fileUpload = document.getElementById("fileupload");
        fileUpload.onchange = function() {
            if (typeof(FileReader) != "undefined") {
                var dvPreview = document.getElementById("dvPreview");
                dvPreview.innerHTML = "";
                var regex = /^([a-zA-Z0-9\s_\\.\-:()])+(.jpg|.jpeg|.gif|.png|.pdf|.rtf|.JPG|.JPEG|.GIF|.PNG|.PDF|.RTF|.doc|.docx|.xlsx)$/;
                var i;
                for (i = 0; i < fileUpload.files.length; i++) {
                    var file = fileUpload.files[i];
                    if (regex.test(file.name.toLowerCase())) {
                        var file_name = file.name.toLowerCase().split(".");
                        var accepted_types = ['jpg', 'gif', 'png', 'jpeg'];
                        var reader = new FileReader();
                        reader.onload = function(e) {
                            var img = document.createElement("IMG");
                            img.height = "100";
                            img.width = "100";
                            img.style.display = 'inline';
                            img.style.padding = '0px 2px';
                            img.src = e.target.result;
                            dvPreview.appendChild(img);
                        }
                        reader.readAsDataURL(file);
                    } else {
                        alert(file.name + " is not a valid image file.");
                        dvPreview.innerHTML = "";
                        return false;
                    }
                }
            } else {
                alert("This browser does not support HTML5 FileReader.");
            }
        }
        var t_fileUpload = document.getElementById("t_fileupload");
        t_fileUpload.onchange = function() {
            if (typeof(FileReader) != "undefined") {
                var t_dvPreview = document.getElementById("t_dvPreview");
                t_dvPreview.innerHTML = "";
                var regex = /^([a-zA-Z0-9\s_\\.\-:()])+(.jpg|.jpeg|.gif|.png|.pdf|.rtf|.JPG|.JPEG|.GIF|.PNG|.PDF|.RTF|.doc|.docx|.xlsx)$/;
                var i;
                for (i = 0; i < t_fileUpload.files.length; i++) {
                    var file = t_fileUpload.files[i];
                    if (regex.test(file.name.toLowerCase())) {
                        var file_name = file.name.toLowerCase().split(".");
                        var accepted_types = ['jpg', 'gif', 'png', 'jpeg'];
                        var reader = new FileReader();
                        reader.onload = function(e) {
                            var img = document.createElement("IMG");
                            img.height = "100";
                            img.width = "100";
                            img.style.display = 'inline';
                            img.style.padding = '0px 2px';
                            img.src = e.target.result;
                            t_dvPreview.appendChild(img);
                        }
                        reader.readAsDataURL(file);
                    } else {
                        alert(file.name + " is not a valid image file.");
                        t_dvPreview.innerHTML = "";
                        return false;
                    }
                }
            } else {
                alert("This browser does not support HTML5 FileReader.");
            }
        }
    }

    function toggle_div_ticket(element, div, file) {
        if ($(element).val() > 0) {
            $('.' + div).removeClass("hidden");
            $("#" + file).attr("required", "required");
        } else {
            $('.' + div).addClass("hidden");
            $("#" + file).removeAttr("required");
        }
    }

    function toggle_transport(element) {
        $('.div_transport').removeClass("hidden");
        $('.transport_fields').removeAttr("required");
        if ($(element).val() == "public") {
            $('#travelCost, .div_public_transport').removeClass("hidden");
            $('#travelMile, .div_private_transport').addClass("hidden");
            //Remove private transport files & options
            $("#travelMile, #otherCost").val(0);
            $('.div_ticket').addClass("hidden");
            $("#fileupload").removeAttr("required", "required");
            if ($("#travelCost").val() && $("#travelCost").val() > 0) {
                $('.div_travelCost_ticket').removeClass("hidden");
                $("#t_fileupload").attr("required", "required");
            } else {
                $('.div_travelCost_ticket').addClass("hidden");
                $("#t_fileupload").removeAttr("required");
            }
        } else {
            $('#travelCost, .div_public_transport').addClass("hidden");
            $('#travelMile, .div_private_transport').removeClass("hidden");
            //Remove public transport files & options
            $("#travelCost").val(0);
            $('.div_travelCost_ticket').addClass("hidden");
            $("#t_fileupload").removeAttr("required", "required");
            if ($("#otherCost").val() && $("#otherCost").val() > 0) {
                $('.div_ticket').removeClass("hidden");
                $("#fileupload").attr("required", "required");
            } else {
                $('.div_ticket').addClass("hidden");
                $("#fileupload").removeAttr("required");
            }
        }
        calcInterp();
    }
</script>

</html>