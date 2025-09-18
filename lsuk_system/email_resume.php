<?php if (session_id() == '' || !isset($_SESSION)) {
    session_start();
}

include '../source/setup_email.php';
include 'db.php';
include 'class.php';
$allowed_type_idz = "6,20,33,123";
$notificationStatus = [
    'client' => ['email' => 'N/A', 'mobile' => 'N/A'],
    'interpreter' => ['email' => 'N/A', 'mobile' => 'N/A'],
];
//Check if user has current action allowed
if ($_SESSION['is_root'] == 0) {
    $get_page_access = $acttObj->read_specific("id", "action_permissions", "user_id=" . $_SESSION['userId'] . " AND action_id IN (" . $allowed_type_idz . ")")['id'];
    if (empty($get_page_access)) {
        die("<center><h2 class='text-center text-danger'>You do not have access to <u>Resume Order</u> action for jobs!<br>Kindly contact admin for further process.</h2></center>");
    }
}
$table = $_GET['table'];
$email_id = $_GET['email_id'];
$get_data = $acttObj->read_specific("order_cancel_remarks,cn_r_id", "$table", "id=" . $email_id);
$reason_of_cancellation = $get_data['cn_r_id'] == 0 ? $get_data['order_cancel_remarks'] : $acttObj->read_specific("cr_title", "cancel_reasons", "cr_id=" . $get_data['cn_r_id'])['cr_title'];
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
    <title>Resume Booking</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" />
    <script src="js/jquery-1.11.3.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    <link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
    <script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>
</head>

<body>
    <div class="container-fluid">
        <form action="" method="post">
            <div class="row"><br>
                <center>
                    <h4><b class="text-success"><?php echo $_GET['orgName']; ?></b> Resume Job ID : <span class="label label-info"><?php echo @$_GET['email_id']; ?></span></h4>
                    <div class="form-group col-sm-6" id="div_notify"><br><br>
                        <h3 class="text-danger">Are you sure you want to RESUME this booking ?</label></h3><br>
                        <p><b>Reason of cancellation</b> : <?php echo $reason_of_cancellation; ?></p>
                        <div class="form-group col-sm-6 col-sm-offset-3">
                          <label class="control-label" for="email">Resume Reason (Job Note - Public)</label>
                          <textarea class="form-control" rows="4" name="jobNote" type="text" placeholder='' required='' id="jobNote"></textarea>
                        </div>
                        
                        <label class="checkbox-inline">
                            <input type="checkbox" id="email_int" name="email_int" value="1" data-toggle="toggle" data-on="Yes" data-off="No"> <b>Do you want to notify interpreter ?</b>
                        </label>
                        <label class="checkbox-inline">
                            <input type="checkbox" id="email_cl" name="email_cl" value="1" data-toggle="toggle" data-on="Yes" data-off="No"> <b>Do you want to notify client ?</b>
                        </label>
                    </div>
                    <div class="form-group col-sm-6"><br><br>
                        <input type="submit" name="yes" id="yes" value="Yes" class="btn btn-primary" onclick="return confirm('Are you sure to RESUME this job?');" />&nbsp;&nbsp;
                        <input type="submit" name="no" value="No" class="btn btn-warning" />
                    </div>
                </center>
            </div>
        </form>
    </div>
    <?php

    if (isset($_POST['yes'])) {
        $chk_booked = $acttObj->read_specific("intrpName", "$table", "id=" . $email_id)['intrpName'];

        if (empty($chk_booked)) {
            $row = $acttObj->read_specific("$table.*,comp_reg.name as orgzName", "$table,comp_reg", "$table.orgName=comp_reg.abrv AND $table.id=".$email_id);
        } else {
            $row = $acttObj->read_specific("$table.*,interpreter_reg.name,interpreter_reg.email,comp_reg.name as orgzName", "$table,interpreter_reg,comp_reg", "$table.intrpName=interpreter_reg.id AND $table.orgName=comp_reg.abrv AND $table.id=".$email_id);
        }
        $email = $row['email'];
        $source = $row['source'];
        $target = $row['target'];
        $orgRef = $row['orgRef'];
        $I_Comments = $row['I_Comments'];
        $chk_orderCancelatoin = $row['orderCancelatoin'];
        $chk_order_cancel_flag = $row['order_cancel_flag'];
        if ($table == 'interpreter' || $table == 'telephone') {
            $from_add = setupEmail::INFO_EMAIL;
            $from_password = setupEmail::INFO_PASSWORD;
            $assignDate = $misc->dated($row['assignDate']);
            $assignTime = $row['assignTime'];
            $orgzName = $row['orgzName'];
            $db_assignDur = $row['assignDur'];
            $guess_dur = $row['guess_dur'];
            if ($db_assignDur > 60) {
                $hours = $db_assignDur / 60;
                if (floor($hours) > 1) {
                    $hr = "hours";
                } else {
                    $hr = "hour";
                }
                $mins = $db_assignDur % 60;
                if ($mins == 00) {
                    $assignDur = sprintf("%2d $hr", $hours);
                } else {
                    $assignDur = sprintf("%2d $hr %02d minutes", $hours, $mins);
                }
            } else if ($db_assignDur == 60) {
                $assignDur = "1 Hour";
            } else {
                $assignDur = $db_assignDur . " minutes";
            }
            if ($db_assignDur != $guess_dur) {
                if ($guess_dur > 60) {
                    $guess_hours = $guess_dur / 60;
                    if (floor($guess_hours) > 1) {
                        $guess_hr = "hours";
                    } else {
                        $guess_hr = "hour";
                    }
                    $guess_mins = $guess_dur % 60;
                    if ($guess_mins == 0) {
                        $get_guess_dur = sprintf("%2d $guess_hr", $guess_hours);
                    } else {
                        $get_guess_dur = sprintf("%2d $guess_hr %02d minutes", $guess_hours, $guess_mins);
                    }
                } else if ($guess_dur == 60) {
                    $get_guess_dur = "1 Hour";
                } else {
                    $get_guess_dur = $guess_dur . " minutes";
                }
            }
            if ($table == 'telephone') {
                $comunic = $row['comunic'];
                $telep_cat = $row['telep_cat'];
                $telep_type = $row['telep_type'];
            }
            if ($table == 'interpreter') {
                $interp_cat = $row['interp_cat'];
                $interp_type = $row['interp_type'];
                $buildingName = $row['buildingName'];
                $street = $row['street'];
                $assignCity = $row['assignCity'];
                $postCode = $row['postCode'];
            }
        } else {
            $from_add = setupEmail::TRANSLATION_EMAIL;
            $from_password = setupEmail::TRANSLATION_PASSWORD;
            $docType = $row['docType'];
            $transType = $row['transType'];
            $trans_detail = $row['trans_detail'];
            $deliverDate = $row['deliverDate'];
            $deliverDate2 = $row['deliverDate2'];
            $deliveryType = $row['deliveryType'];
            $assignDate = $row['asignDate'];
        }
        $assignIssue = $row['assignIssue'];
        $orgContact = $row['orgContact'];
        $inchPerson = $row['inchPerson'];
        $inchEmail = $row['inchEmail'];
        $name = $row['name'];
        $remrks = $row['remrks'] ?: '';
        $acttObj->editFun($table, $email_id, 'pay_int', 1);
        if ($chk_orderCancelatoin == 1) {
            $acttObj->editFun($table, $email_id, 'orderCancelatoin', 0);
        } else {
            $acttObj->editFun($table, $email_id, 'order_cancel_flag', 0);
        }
        $acttObj->editFun($table, $email_id, 'edited_by', $_SESSION['UserName']);
        $acttObj->editFun($table, $email_id, 'edited_date', date("Y-m-d H:i:s"));
        // $acttObj->new_old_table('hist_' . $table, $table, $email_id);
        $array_types = array("interpreter" => "F2F", "telephone" => "TP", "translation" => "TR");
        $index_mapping = array(
            'Normal Cancel' => 'order_cancel_flag', 'Chargeable Cancel' => 'orderCancelatoin', 'Pay Interpreter' => 'pay_int'
        );
      
        $old_values = array();
        $new_values = array();
        $get_new_data = $acttObj->read_specific("*", "$table", "id=" . $email_id);
      
        foreach ($index_mapping as $key => $value) {
            if (isset($get_new_data[$value])) {
                $old_values[$key] = $row[$value];
                $new_values[$key] = $get_new_data[$value];
            }
        }
        $acttObj->log_changes(json_encode($old_values), json_encode($new_values), $email_id, $table, "update", $_SESSION['userId'], $_SESSION['UserName'], "resume_order");

        //Below history function needs to be removed
        $acttObj->insert("daily_logs", array("action_id" => 22, "user_id" => $_SESSION['userId'], "details" => $array_types[$table] . " Job ID: " . $email_id));

        if ($table == 'translation') {
            $append_table = "
                <table>
                <tr>
                <td style='border: 1px solid #020202;padding: 5px;text-align: center;background: #78ab58;color: white;' colspan='2'><b>Resumed Job Details</b></td>
                </tr>
                <tr>
                <td style='border: 1px solid yellowgreen;padding:5px;'>Source Language</td>
                <td style='border: 1px solid yellowgreen;padding:5px;'>" . $source . "</td>
                </tr>
                <tr>
                <td style='border: 1px solid yellowgreen;padding:5px;'>Target Language</td>
                <td style='border: 1px solid yellowgreen;padding:5px;'>" . $target . "</td>
                </tr>
                <tr>
                <td style='border: 1px solid yellowgreen;padding:5px;'>Assignment Date</td>
                <td style='border: 1px solid yellowgreen;padding:5px;'>" . $assignDate . "</td>
                </tr>
                <tr>
                <td style='border: 1px solid yellowgreen;padding:5px;'>Document Type</td>
                <td style='border: 1px solid yellowgreen;padding:5px;'>" . $acttObj->read_specific("tc_title", "trans_cat", "tc_id=" . $docType)['tc_title'] . "</td>
                </tr>
                <tr>
                <td style='border: 1px solid yellowgreen;padding:5px;'>Translation Type(s)</td>
                <td style='border: 1px solid yellowgreen;padding:5px;'>" . $acttObj->read_specific("GROUP_CONCAT(CONCAT(tt_title)  SEPARATOR ' <b> & </b> ') as tt_title", "trans_types", "tt_id IN (" . $trans_detail . ")")['tt_title'] . "</td>
                </tr>
                <tr>
                <td style='border: 1px solid yellowgreen;padding:5px;'>Translation Category</td>
                <td style='border: 1px solid yellowgreen;padding:5px;'>" . $acttObj->read_specific("GROUP_CONCAT(CONCAT(td_title)  SEPARATOR ' <b> & </b> ') as td_title", "trans_dropdown", "td_id IN (" . $transType . ")")['td_title'] . "</td>
                </tr>
                <tr>
                <td style='border: 1px solid yellowgreen;padding:5px;'>Client Name</td>
                <td style='border: 1px solid yellowgreen;padding:5px;'>" . $orgRef . "</td>
                </tr>
                <tr>
                <td style='border: 1px solid yellowgreen;padding:5px;'>Delivery Type</td>
                <td style='border: 1px solid yellowgreen;padding:5px;'>" . $deliveryType . "</td>
                </tr>
                </table>";
            $to_add = $inchEmail;
            $subject = "Resuming Translation Project " . $email_id;
            $query_format = "SELECT em_format FROM email_format where id='21'";
            $result_format = mysqli_query($con, $query_format);
            $row_format = mysqli_fetch_array($result_format);
            //Get format from database
            $msg_body = $row_format['em_format'];
            $data   = ["[ORGCONTACT]", "[SOURCE]", "[ASSIGNDATE]", "[ORGREF]", "[TABLE]"];
            $to_replace  = ["$orgContact", "$source", "$assignDate", "$orgRef", "$append_table"];
            $message = str_replace($data, $to_replace, $msg_body);
            if (isset($_POST['email_cl'])) {
                try {
                    $mail->SMTPDebug = 0;
                    $mail->isSMTP(); 
                    $mail->Host = setupEmail::EMAIL_HOST;
                    $mail->SMTPAuth   = true;
                    $mail->Username   = $from_add;
                    $mail->Password   = $from_password;
                    $mail->SMTPSecure = setupEmail::SECURE_TYPE;
                    $mail->Port       = setupEmail::SENDING_PORT;
                    $mail->setFrom($from_add, setupEmail::FROM_NAME);
                    $mail->addAddress($to_add);
                    $mail->addReplyTo($from_add, setupEmail::FROM_NAME);
                    $mail->isHTML(true);
                    $mail->Subject = $subject;
                    $mail->Body    = $message;
                    if ($mail->send()) {
                        $notificationStatus['client']['email'] ="sent";
                        $mail->ClearAllRecipients();
                        $mail->addAddress(setupEmail::LSUK_GMAIL);
                        $mail->addReplyTo($from_add, setupEmail::FROM_NAME);
                        $mail->isHTML(true);
                        $mail->Subject = $subject;
                        $mail->Body    = $message;
                        $mail->send();
                        $mail->ClearAllRecipients();
                        $client_email = '1';
                    } else {
                        $client_email = '0';
                    }
                } catch (Exception $e) { $notificationStatus['client']['email'] ="Error"; ?>
                    <script>
                        alert('<?php echo "Mailer Library Error!"; ?>');
                    </script>
                    <?php }
            }else{
              $notificationStatus['client']['email'] ="Not sent";
            }
            //...........................for interpreter.............................
            if (!empty($chk_booked)) {
                $to_add = $email;
                $subject = "Resuming Translation Project " . $email_id;
                $query_format = "SELECT em_format FROM email_format where id='22'";
                $result_format = mysqli_query($con, $query_format);
                $row_format = mysqli_fetch_array($result_format);
                //Get format from database
                $msg_body = $row_format['em_format'];
                if (!empty($remrks)) {
                    $append_table .= "<br><u><b>NOTES FOR THIS JOB:</b></u><br>" . $remrks . "<br>";
                }
                $data   = ["[NAME]", "[SOURCE]", "[ASSIGNDATE]", "[TABLE]"];
                $to_replace  = ["$name", "$source", "$assignDate", "$append_table"];
                $message = str_replace($data, $to_replace, $msg_body);
                if (isset($_POST['email_int'])) {
                    try {
                        $mail->SMTPDebug = 0;
                        $mail->isSMTP(); 
                        $mail->Host = setupEmail::EMAIL_HOST;
                        $mail->SMTPAuth   = true;
                        $mail->Username   = $from_add;
                        $mail->Password   = $from_password;
                        $mail->SMTPSecure = setupEmail::SECURE_TYPE;
                        $mail->Port       = setupEmail::SENDING_PORT;
                        $mail->setFrom($from_add, setupEmail::FROM_NAME);
                        $mail->addAddress($to_add);
                        $mail->addReplyTo($from_add, setupEmail::FROM_NAME);
                        $mail->isHTML(true);
                        $mail->Subject = $subject;
                        $mail->Body    = $message;
                        if ($mail->send()) {
                            $notificationStatus['interpreter']['email'] ="sent";
                            $mail->ClearAllRecipients();
                            $mail->addAddress(setupEmail::LSUK_GMAIL);
                            $mail->addReplyTo($from_add, setupEmail::FROM_NAME);
                            $mail->isHTML(true);
                            $mail->Subject = $subject;
                            $mail->Body    = $message;
                            $mail->send();
                            $mail->ClearAllRecipients();
                            $int_email = '1';
                        } else {
                            $int_email = '0';
                        }
                    } catch (Exception $e) { $notificationStatus['interpreter']['email'] ="Error"; ?>
                        <script>
                            alert('<?php echo "Mailer Library Error!"; ?>');
                        </script>
                    <?php }
                }else{
                    $notificationStatus['interpreter']['email'] ="Not sent";
                  }
            }
            if ($client_email == '1' && (empty($chk_booked) && $int_email == '0') || (!empty($chk_booked) && $int_email == '1')) {
                echo "<script>alert('Email successfully sent to client and interpreter.');</script>";
            } else {
                echo "<script>alert('Email notifications were blocked for client OR interpreter!');</script>";
            }
        }

        if ($table == 'telephone') {
            $write_telep_cat = $post_telep_cat == '11' ? $assignIssue : $acttObj->read_specific("tpc_title", "telep_cat", "tpc_id=" . $telep_cat)['tpc_title'];
            $write_telep_type = $telep_cat == '11' ? '' : $acttObj->read_specific("GROUP_CONCAT(CONCAT(tpt_title)  SEPARATOR ' <b> & </b> ') as tpt_title", "telep_types", "tpt_id IN (" . $telep_type . ")")['tpt_title'];
            if ($post_telep_cat == '11') {
                $append_issue = "<tr><td style='border: 1px solid yellowgreen;padding:5px;'>Assignment Category</td><td style='border: 1px solid yellowgreen;padding:5px;'>Other</td></tr><tr><td style='border: 1px solid yellowgreen;padding:5px;'>Assignment Details</td><td style='border: 1px solid yellowgreen;padding:5px;'>" . $assignIssue . "</td></tr>";
            } else {
                $append_issue = "<tr><td style='border: 1px solid yellowgreen;padding:5px;'>Assignment Category</td><td style='border: 1px solid yellowgreen;padding:5px;'>" . $write_telep_cat . "</td></tr><tr><td style='border: 1px solid yellowgreen;padding:5px;'>Assignment Details</td><td style='border: 1px solid yellowgreen;padding:5px;'>" . $write_telep_type . "</td></tr>";
            }
            $write_comunic = $acttObj->read_specific("c_title", "comunic_types", "c_id=" . $comunic)['c_title'];
            $communication_type = empty($comunic) || $comunic == 11 ? "Telephone interpreting" : $write_comunic;
            $append_table = "
                <table>
                <tr>
                <td style='border: 1px solid #020202;padding: 5px;text-align: center;background: #78ab58;color: white;' colspan='2'><b>Resumed Job Details</b></td>
                </tr>
                <tr>
                <td style='border: 1px solid yellowgreen;padding:5px;'>Communication Type</td>
                <td style='border: 1px solid yellowgreen;padding:5px;'>" . $write_comunic . "</td>
                </tr>
                <tr>
                <td style='border: 1px solid yellowgreen;padding:5px;'>Source Language</td>
                <td style='border: 1px solid yellowgreen;padding:5px;'>" . $source . "</td>
                </tr>
                <tr>
                <td style='border: 1px solid yellowgreen;padding:5px;'>Target Language</td>
                <td style='border: 1px solid yellowgreen;padding:5px;'>" . $target . "</td>
                </tr>
                <tr>
                <td style='border: 1px solid yellowgreen;padding:5px;'>Assignment Date</td>
                <td style='border: 1px solid yellowgreen;padding:5px;'>" . $assignDate . "</td>
                <tr>
                <td style='border: 1px solid yellowgreen;padding:5px;'>Assignment Time</td>
                <td style='border: 1px solid yellowgreen;padding:5px;'>" . $assignTime . "</td>
                </tr>
                <tr>
                <td style='border: 1px solid yellowgreen;padding:5px;'>Assignment Duration</td>
                <td style='border: 1px solid yellowgreen;padding:5px;'>" . $assignDur . "</td>
                </tr>
                " . $append_issue . "
                <tr>
                <td style='border: 1px solid yellowgreen;padding:5px;'>Report to</td>
                <td style='border: 1px solid yellowgreen;padding:5px;'>" . $inchPerson . "</td>
                </tr>
                <tr>
                <td style='border: 1px solid yellowgreen;padding:5px;'>Case Worker</td>
                <td style='border: 1px solid yellowgreen;padding:5px;'>" . $orgContact . "</td>
                </tr>
                </table>";
            $to_add = $inchEmail;
            $subject = "Resuming " . $communication_type . " Project " . $email_id;
            $query_format = "SELECT em_format FROM email_format where id='23'";
            $result_format = mysqli_query($con, $query_format);
            $row_format = mysqli_fetch_array($result_format);
            //Get format from database
            $msg_body = $row_format['em_format'];
            $data   = ["[ORGCONTACT]", "[SOURCE]", "[ASSIGNDATE]", "[ASSIGNTIME]", "[ORGREF]", "[TABLE]"];
            $to_replace  = ["$orgContact", "$source", "$assignDate", "$assignTime", "$orgRef", "$append_table"];
            $message = str_replace($data, $to_replace, $msg_body);
            if (isset($_POST['email_cl'])) {
                try {
                    $mail->SMTPDebug = 0;
                    $mail->isSMTP(); 
                    $mail->Host = setupEmail::EMAIL_HOST;
                    $mail->SMTPAuth   = true;
                    $mail->Username   = $from_add;
                    $mail->Password   = $from_password;
                    $mail->SMTPSecure = setupEmail::SECURE_TYPE;
                    $mail->Port       = setupEmail::SENDING_PORT;
                    $mail->setFrom($from_add, setupEmail::FROM_NAME);
                    $mail->addAddress($to_add);
                    $mail->addReplyTo($from_add, setupEmail::FROM_NAME);
                    $mail->isHTML(true);
                    $mail->Subject = $subject;
                    $mail->Body    = $message;
                    if ($mail->send()) {
                        $notificationStatus['client']['email'] ="sent";
                        $mail->ClearAllRecipients();
                        $mail->addAddress(setupEmail::LSUK_GMAIL);
                        $mail->addReplyTo($from_add, setupEmail::FROM_NAME);
                        $mail->isHTML(true);
                        $mail->Subject = $subject;
                        $mail->Body    = $message;
                        $mail->send();
                        $mail->ClearAllRecipients();
                        $client_email = '1';
                    } else {
                        $client_email = '0';
                    }
                } catch (Exception $e) { $notificationStatus['client']['email'] ="Error"; ?>
                    <script>
                        alert('<?php echo "Mailer Library Error!"; ?>');
                    </script>
                    <?php }
            }else{
              $notificationStatus['client']['email'] ="Not sent";
            }
            //..............................for interpreter .....................//
            if (!empty($chk_booked)) {
                $to_add = $email;
                $subject = "Resuming " . $communication_type . " Project " . $email_id;
                $query_format = "SELECT em_format FROM email_format where id='24'";
                $result_format = mysqli_query($con, $query_format);
                $row_format = mysqli_fetch_array($result_format);
                //Get format from database
                $msg_body = $row_format['em_format'];
                if ($db_assignDur != $guess_dur) {
                    $append_table .= "<br><u><b>NOTES FOR THIS JOB:</b></u><br>
                        This session is booked for " . $assignDur . ", however it can take  up to " . $get_guess_dur . " or longer.<br>
                        Therefore please consider your unrestricted availability before bidding / accepting this job.
                        In cases of short notice cancellation, you will be paid the booked time (" . $assignDur . ").<br>";
                    if (!empty($remrks)) {
                        $append_table .= $remrks . "<br>";
                    }
                } else {
                    if (!empty($remrks)) {
                        $append_table .= "<br><u><b>NOTES FOR THIS JOB:</b></u><br>" . $remrks . "<br>";
                    }
                }
                $data   = ["[NAME]", "[SOURCE]", "[ASSIGNDATE]", "[ASSIGNTIME]", "[TABLE]"];
                $to_replace  = ["$name", "$source", "$assignDate", "$assignTime", "$append_table"];
                $message = str_replace($data, $to_replace, $msg_body);
                if (isset($_POST['email_int'])) {
                    try {
                        $mail->SMTPDebug = 0;
                        $mail->isSMTP(); 
                        $mail->Host = setupEmail::EMAIL_HOST;
                        $mail->SMTPAuth   = true;
                        $mail->Username   = $from_add;
                        $mail->Password   = $from_password;
                        $mail->SMTPSecure = setupEmail::SECURE_TYPE;
                        $mail->Port       = setupEmail::SENDING_PORT;
                        $mail->setFrom($from_add, setupEmail::FROM_NAME);
                        $mail->addAddress($to_add);
                        $mail->addReplyTo($from_add, setupEmail::FROM_NAME);
                        $mail->isHTML(true);
                        $mail->Subject = $subject;
                        $mail->Body    = $message;
                        if ($mail->send()) {
                            $notificationStatus['interpreter']['email'] ="sent";
                            $mail->ClearAllRecipients();
                            $mail->addAddress(setupEmail::LSUK_GMAIL);
                            $mail->addReplyTo($from_add, setupEmail::FROM_NAME);
                            $mail->isHTML(true);
                            $mail->Subject = $subject;
                            $mail->Body    = $message;
                            $mail->send();
                            $mail->ClearAllRecipients();
                            $int_email = '1';
                        } else {
                            $int_email = '0';
                        }
                    } catch (Exception $e) { $notificationStatus['interpreter']['email'] ="Error"; ?>
                        <script>
                            alert('<?php echo "Mailer Library Error!"; ?>');
                        </script>
        <?php }
                }else{ $notificationStatus['interpreter']['email'] ="Not sent"; }
            }
            if ($client_email == '1' && (empty($chk_booked) && $int_email == '0') || (!empty($chk_booked) && $int_email == '1')) {
                echo "<script>alert('Email successfully sent to client and interpreter.');</script>";
            } else {
                echo "<script>alert('Failed to send email to client and interpreter!');</script>";
            }
        } ?>
        <?php

        if ($table == 'interpreter') {
            $write_interp_cat = $interp_cat == '12' ? $assignIssue : $acttObj->read_specific("ic_title", "interp_cat", "ic_id=" . $interp_cat)['ic_title'];
            $write_interp_type = $interp_cat == '12' ? '' : $acttObj->read_specific("GROUP_CONCAT(CONCAT(it_title)  SEPARATOR ' <b> & </b> ') as it_title", "interp_types", "it_id IN (" . $interp_type . ")")['it_title'];
            if ($interp_cat == '12') {
                $append_issue = "<tr><td style='border: 1px solid yellowgreen;padding:5px;'>Assignment Category</td><td style='border: 1px solid yellowgreen;padding:5px;'>Other</td></tr><tr><td style='border: 1px solid yellowgreen;padding:5px;'>Assignment Details</td><td style='border: 1px solid yellowgreen;padding:5px;'>" . $assignIssue . "</td></tr>";
            } else {
                $append_issue = "<tr><td style='border: 1px solid yellowgreen;padding:5px;'>Assignment Category</td><td style='border: 1px solid yellowgreen;padding:5px;'>" . $write_interp_cat . "</td></tr><tr><td style='border: 1px solid yellowgreen;padding:5px;'>Assignment Details</td><td style='border: 1px solid yellowgreen;padding:5px;'>" . $write_interp_type . "</td></tr>";
            }
            $append_table = "
                <table>
                <tr>
                <td style='border: 1px solid #020202;padding: 5px;text-align: center;background: #78ab58;color: white;' colspan='2'><b>Resumed Job Details</b></td>
                </tr>
                <tr>
                <td style='border: 1px solid yellowgreen;padding:5px;'>Source Language</td>
                <td style='border: 1px solid yellowgreen;padding:5px;'>" . $source . "</td>
                </tr>
                <tr>
                <td style='border: 1px solid yellowgreen;padding:5px;'>Target Language</td>
                <td style='border: 1px solid yellowgreen;padding:5px;'>" . $target . "</td>
                </tr>
                <tr>
                <td style='border: 1px solid yellowgreen;padding:5px;'>Assignment Date</td>
                <td style='border: 1px solid yellowgreen;padding:5px;'>" . $assignDate . "</td>
                </tr>
                <tr>
                <td style='border: 1px solid yellowgreen;padding:5px;'>Assignment Time</td>
                <td style='border: 1px solid yellowgreen;padding:5px;'>" . $assignTime . "</td>
                </tr>
                <tr>
                <td style='border: 1px solid yellowgreen;padding:5px;'>Assignment Duration</td>
                <td style='border: 1px solid yellowgreen;padding:5px;'>" . $assignDur . "</td>
                </tr>
                <tr>
                <td style='border: 1px solid yellowgreen;padding:5px;'>Assignment Location</td>
                <td style='border: 1px solid yellowgreen;padding:5px;'>" . (!empty(trim($buildingName))?htmlspecialchars($buildingName,ENT_QUOTES, 'UTF-8'):'') . (!empty(trim($street))?(', '.$street):'') . (!empty(trim($assignCity))?(', '.$assignCity):'') . (!empty(trim($postCode))?(', '.$postCode):'') . "</td>
                </tr>
                " . $append_issue . "
                <tr>
                <td style='border: 1px solid yellowgreen;padding:5px;'>Report to</td>
                <td style='border: 1px solid yellowgreen;padding:5px;'>" . $inchPerson . "</td>
                </tr>
                <tr>
                <td style='border: 1px solid yellowgreen;padding:5px;'>Case Worker or Person Incharge</td>
                <td style='border: 1px solid yellowgreen;padding:5px;'>" . $orgContact . "</td>
                </tr>
                <tr>
                <td style='border: 1px solid yellowgreen;padding:5px;'>Client Name</td>
                <td style='border: 1px solid yellowgreen;padding:5px;'>" . $orgRef . "</td>
                </tr>
                </table>";
            $to_add = $inchEmail;
            $subject = "Resuming Face To Face Interpreting Project " . $email_id;
            $query_format = "SELECT em_format FROM email_format where id='25'";
            $result_format = mysqli_query($con, $query_format);
            $row_format = mysqli_fetch_array($result_format);
            //Get format from database
            $msg_body = $row_format['em_format'];
            $data   = ["[ORGCONTACT]", "[SOURCE]", "[ORGREF]", "[ASSIGNDATE]", "[ASSIGNTIME]", "[BUILDINGNAME]", "[STREET]", "[ASSIGNCITY]", "[POSTCODE]", "[TABLE]"];
            $to_replace  = ["$orgContact", "$source", "$orgRef", "$assignDate", "$assignTime", "$buildingName", "$street", "$assignCity", "$postCode", "$append_table"];
            $message = str_replace($data, $to_replace, $msg_body);
            if (isset($_POST['email_cl'])) {
                try {
                    $mail->SMTPDebug = 0;
                    $mail->isSMTP(); 
                    $mail->Host = setupEmail::EMAIL_HOST;
                    $mail->SMTPAuth   = true;
                    $mail->Username   = $from_add;
                    $mail->Password   = $from_password;
                    $mail->SMTPSecure = setupEmail::SECURE_TYPE;
                    $mail->Port       = setupEmail::SENDING_PORT;
                    $mail->setFrom($from_add, setupEmail::FROM_NAME);
                    $mail->addAddress($to_add);
                    $mail->addReplyTo($from_add, setupEmail::FROM_NAME);
                    $mail->isHTML(true);
                    $mail->Subject = $subject;
                    $mail->Body    = $message;
                    if ($mail->send()) {
                        $notificationStatus['client']['email'] ="sent";
                        $mail->ClearAllRecipients();
                        $mail->addAddress(setupEmail::LSUK_GMAIL);
                        $mail->addReplyTo($from_add, setupEmail::FROM_NAME);
                        $mail->isHTML(true);
                        $mail->Subject = $subject;
                        $mail->Body    = $message;
                        $mail->send();
                        $mail->ClearAllRecipients();
                        $client_email = '1';
                    } else {
                        $client_email = '0';
                    }
                } catch (Exception $e) { $notificationStatus['client']['email'] ="Error"; ?>
                    <script>
                        alert('<?php echo "Mailer Library Error!"; ?>');
                    </script>
                    <?php }
            }else{ $notificationStatus['client']['email'] ="Not sent"; }
            //..............................for interpreter .....................//
            if (!empty($chk_booked)) {
                $to_add = $email;
                $subject = "Resuming Face To Face Interpreting Project " . $email_id;
                $query_format = "SELECT em_format FROM email_format where id='26'";
                $result_format = mysqli_query($con, $query_format);
                $row_format = mysqli_fetch_array($result_format);
                //Get format from database
                $msg_body = $row_format['em_format'];
                if ($db_assignDur != $guess_dur) {
                    $append_table .= "<br><u><b>NOTES FOR THIS JOB:</b></u><br>
                        This session is booked for " . $assignDur . ", however it can take  up to " . $get_guess_dur . " or longer.<br>
                        Therefore please consider your unrestricted availability before bidding / accepting this job.
                        In cases of short notice cancellation, you will be paid the booked time (" . $assignDur . ").<br>";
                    if (!empty($remrks)) {
                        $append_table .= $remrks . "<br>";
                    }
                } else {
                    if (!empty($remrks)) {
                        $append_table .= "<br><u><b>NOTES FOR THIS JOB:</b></u><br>" . $remrks . "<br>";
                    }
                }
                $data   = ["[NAME]", "[SOURCE]", "[ASSIGNDATE]", "[ASSIGNTIME]", "[BUILDINGNAME]", "[STREET]", "[ASSIGNCITY]", "[POSTCODE]", "[TABLE]"];
                $to_replace  = ["$name", "$source", "$assignDate", "$assignTime", "$buildingName", "$street", "$assignCity", "$postCode", "$append_table"];
                $message = str_replace($data, $to_replace, $msg_body);
                if (isset($_POST['email_int'])) {
                    try {
                        $mail->SMTPDebug = 0;
                        $mail->isSMTP(); 
                        $mail->Host = setupEmail::EMAIL_HOST;
                        $mail->SMTPAuth   = true;
                        $mail->Username   = $from_add;
                        $mail->Password   = $from_password;
                        $mail->SMTPSecure = setupEmail::SECURE_TYPE;
                        $mail->Port       = setupEmail::SENDING_PORT;
                        $mail->setFrom($from_add, setupEmail::FROM_NAME);
                        $mail->addAddress($to_add);
                        $mail->addReplyTo($from_add, setupEmail::FROM_NAME);
                        $mail->isHTML(true);
                        $mail->Subject = $subject;
                        $mail->Body    = $message;
                        if ($mail->send()) {
                            $notificationStatus['interpreter']['email'] ="sent";
                            $mail->ClearAllRecipients();
                            $mail->addAddress(setupEmail::LSUK_GMAIL);
                            $mail->addReplyTo($from_add, setupEmail::FROM_NAME);
                            $mail->isHTML(true);
                            $mail->Subject = $subject;
                            $mail->Body    = $message;
                            $mail->send();
                            $mail->ClearAllRecipients();
                            $int_email = '1';
                        } else {
                            $int_email = '0';
                        }
                    } catch (Exception $e) { $notificationStatus['interpreter']['email'] ="Error"; ?>
                        <script>
                            alert('<?php echo "Mailer Library Error!"; ?>');
                        </script>
        <?php }
                }else{ $notificationStatus['interpreter']['email'] ="Not sent"; }
            }
            if ($client_email == '1' && (empty($chk_booked) && $int_email == '0') || (!empty($chk_booked) && $int_email == '1')) {
                echo "<script>alert('Email successfully sent to client and interpreter.');</script>";
            } else {
                echo "<script>alert('Failed to send email to client and interpreter!');</script>";
            }
        }      
        function styleCell($value) {
            return $value === 'Error' ? '<span style="color:red;">' . $value . '</span>' : $value;
          }
          $job_note = "Resume Note: ".$_POST['jobNote'];
          $job_note .= '<table class="table" style="background:transparent">
          <tr><th>Type</th><th>Email</th></tr>
          <tr>
              <td>Client</td>
              <td>' . styleCell($notificationStatus['client']['email']) . '</td>
            </tr>
            <tr>
                <td>Interpreter</td>
                <td>' . styleCell($notificationStatus['interpreter']['email']) . '</td>
            </tr>
        </table>';
         
        $acttObj->insert('jobnotes', array('jobNote' => mysqli_escape_string($con, $job_note), 'tbl' => $table, 'time' => $misc->sys_datetime_db(), 'fid' => $email_id, 'submitted' => $_SESSION['UserName'], 'dated' => date('Y-m-d')));
        ?>
                <script>
            window.close();
            window.onunload = refreshParent;

            function refreshParent() {
                window.opener.location.reload();
          }
        </script>
        <?php
    }
    if (isset($_POST['no'])) {
        echo "<script>window.close();</script>";
    };
    ?>