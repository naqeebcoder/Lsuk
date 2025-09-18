<?php if (session_id() == '' || !isset($_SESSION)) {
    session_start();
}

include 'source/setup_email.php';
include 'source/db.php';
include 'source/class.php';

$array_rearrange_order = array("interpreter" => "order_f2f_multi_dup.php", "telephone" => "order_tp_multi_dup.php", "translation" => "order_tr_multi_dup.php");
$array_types = array("interpreter" => "Face To Face", "telephone" => "TP", "translation" => "Translation");
$array_order_types = array("interpreter" => 1, "telephone" => 2, "translation" => 3);
$table = $_GET['table'];
$email_id = base64_decode($_GET['job_id']);
$cancelled_at = date('Y-m-d');
$cancelled_time = date('H:i:s');
$get_job_data = $acttObj->read_specific("source,intrpName", "$table", "id=" . $email_id);
$interpreter_id = $get_job_data['intrpName'];
if (!$interpreter_id) {
    $row = $acttObj->read_specific("$table.*,comp_reg.name as orgzName", "$table,comp_reg", "$table.orgName=comp_reg.abrv AND $table.id=" . $email_id);
} else {
    $row = $acttObj->read_specific("$table.*,interpreter_reg.name,interpreter_reg.email,interpreter_reg.contactNo,interpreter_reg.country as interpreter_country,comp_reg.name as orgzName", "$table,interpreter_reg,comp_reg", "$table.intrpName=interpreter_reg.id AND $table.orgName=comp_reg.abrv AND $table.id=" . $email_id);
}
$cn_date = $cancelled_at;
if ($table == 'translation') {
    $assign_date = $row['asignDate'];
    $assign_time = "00:00:00";
} else {
    $assign_date = $row['assignDate'];
    $assign_time = $row['assignTime'];
}
if ($assign_date < $cn_date) {
    $diff = date_diff(date_create($assign_date), date_create($cn_date));
} else {
    $diff = date_diff(date_create($cn_date), date_create($assign_date));
}
if (isset($_POST['yes'])) {
    $post_pay_int = $_POST['pay_int'];
    $client_email = '0';
    $int_email = '0';
    $cn_t_id = $_POST['cn_t_id'];
    $charge_client = $_POST['charge_client'];
    $order_cancelledby = 'Client';
    //update database table
    $update_array = array('cn_t_id' => $cn_t_id, 'order_cancelledby' => $order_cancelledby, 'cn_date' => $cn_date, 'cn_time' => $cancelled_time);

    if (isset($post_pay_int) && $post_pay_int == 1) {
        $update_array['pay_int'] = 1;
    } else {
        $update_array['pay_int'] = 0;
        //Disable bid in bidding for this interpreter
        $acttObj->update('bid', array('allocated' => 0), array('job' => $email_id, 'tabName' => $table));
    }
    $cn_r_id = $_POST['cn_r_id'];
    if ($cn_r_id == 15) {
        $order_cancel_remarks = $_POST['order_cancel_remarks'];
        $update_array['order_cancel_remarks'] = $order_cancel_remarks;
    } else {
        $update_array['cn_r_id'] = $cn_r_id;
    }
    $get_cancelation_drops = $acttObj->read_specific("cd_effect,cancelled_hours", "cancellation_drops", "cd_id=" . $cn_t_id);
    $cd_effect = $get_cancelation_drops['cd_effect'];
    if (isset($charge_client) && $charge_client == 1) {
        $update_array['orderCancelatoin'] = 1;
        $write_charge = '<table><tbody><tr><td style="border: 1px solid black;padding:5px;background: #ef0808ab;color: #000;"><b>This is a short notice (under 48 hours) chargeable cancellation, Invoice to follow</b></td></tr></tbody></table>';
    } else {
        if ($cd_effect == 0) {
            $update_array['order_cancel_flag'] = 1;
            $write_charge = '<table><tbody><tr><td style="border: 1px solid black;padding:5px;background: #3f8a27ab;color: #000;"><b>This is an advance notice (over 48 hours) non-chargeable cancellation</b></td></tr></tbody></table>';
        } else {
            $update_array['orderCancelatoin'] = 1;
            $write_charge = '<table><tbody><tr><td style="border: 1px solid black;padding:5px;background: #ef0808ab;color: #000;"><b>This is a short notice (under 48 hours) chargeable cancellation, Invoice to follow</b></td></tr></tbody></table>';
        }
    }
    $write_cancel_type = str_replace("[DATE]", $cn_date, $acttObj->read_specific("cd_title", "cancellation_drops", "cd_id=" . $cn_t_id)['cd_title']);
    $write_cancel_remarks = $cn_r_id == 15 ? $order_cancel_remarks : $acttObj->read_specific("cr_title", "cancel_reasons", "cr_id=" . $cn_r_id)['cr_title'];
    $email = $row['email'];
    $source = $row['source'];
    $target = $row['target'];
    $orgRef = $row['orgRef'];
    $pay_int = $row['pay_int'];
    if ($post_pay_int == 0) {
        $write_pay = '<table><tbody><tr><td style="border: 1px solid black;padding:5px;background: #ef0808ab;color: #000;"><b>This is an advance notice cancellation, you will not be paid for this job</b></td></tr></tbody></table>';
    } else {
        $write_pay = '<table><tbody><tr><td style="border: 1px solid black;padding:5px;background: #3f8a27ab;color: #000;"><b>This is a short notice cancellation, you will be paid for this job</b></td></tr></tbody></table>';
    }
    // Insert cancellation record
    $acttObj->insert(
        "canceled_orders",
        array(
            "interpreter_id" => $row['intrpName'], "job_id" => $email_id, "job_type" => $array_order_types[$table], "cancel_type_id" => $cn_t_id, "cancel_reason_id" => $cn_r_id,
            "canceled_by" => 2, "canceled_date" => ($cancelled_at . " " . $cancelled_time), "canceled_reason" => $write_cancel_remarks, "notice_period" => $get_cancelation_drops['cancelled_hours'],
            "source_language" => $source, "target_language" => $target, "created_by" => $_SESSION['cust_userId'], "action_by" => 2, "created_date" => date("Y-m-d H:i:s")
        )
    );

    if ($table == 'interpreter' || $table == 'telephone') {
        $from_add = setupEmail::INFO_EMAIL;
        $from_password = setupEmail::INFO_PASSWORD;
        $assignDate = $misc->dated($row['assignDate']);
        $assignTime = $row['assignTime'];
        $orgzName = $row['orgzName'];
        $assignDur = $row['assignDur'];
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
    $remrks = $row['remrks'] ?: 'Nil';
    $int_notes = "<br><u><b>NOTES FOR THIS JOB:</b></u><br>" . $remrks . '<br>-----------------------------------';
    //Update DB
    $update_array['order_cancled_bystaff'] = $_SESSION['cust_UserName'];
    $update_array['edited_by'] = $_SESSION['cust_UserName'];
    $update_array['edited_date'] = date("Y-m-d H:i:s");
    $acttObj->update($table, $update_array, "id=" . $email_id);

    $index_mapping = array(
        'Cancel Staff' => 'order_cancled_bystaff', 'Normal Cancel' => 'order_cancel_flag', 'Chargeable Cancel' => 'orderCancelatoin', 'Cancel Remarks' => 'order_cancel_remarks',
        'Cancel By' => 'order_cancelledby', 'Pay Interpreter' => 'pay_int', 'Cancel Type ID' => 'cn_t_id', 'Cancel Reason ID' => 'cn_r_id'
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
    $acttObj->log_changes(json_encode($old_values), json_encode($new_values), $email_id, $table, "update", $_SESSION['cust_userId'], $_SESSION['cust_UserName'], "cancel_order", 2);

    //Create job note
    $interpreter_payable = isset($post_pay_int) && $post_pay_int == 1 ? "Interpreter payable" : "Interpreter non-payable";
    $client_chargeable = isset($charge_client) && $charge_client == 1 ? "Client chargeable" : "Client non-chargeable";
    $interpreter_payable_text = isset($post_pay_int) && $post_pay_int == 1 ? "\nYou will be paid for this job\n" : "\nYou will not be paid for this job\n";
    $job_note = "Cancellation Type: " . $write_cancel_type . "<br>" . $interpreter_payable . "<br>" . $client_chargeable . "<br>Cancellation Note: " . $write_cancel_remarks;
    $acttObj->insert('jobnotes', array('jobNote' => mysqli_escape_string($con, $job_note), 'tbl' => $table, 'time' => $misc->sys_datetime_db(), 'fid' => $email_id, 'submitted' => $_SESSION['cust_UserName'], 'dated' => date('Y-m-d')));
    //Send SMS for cancel job
    if (isset($_SESSION['cust_userId']) && $interpreter_id) {
        $get_application = $acttObj->read_specific("*", "job_messages", "order_type=" . $array_order_types[$table] . " AND order_id=" . $email_id . " AND interpreter_id=" . $row['intrpName'] . " AND message_category=3");
        if (empty($get_application['id'])) {
            //Adding config for SMS
            include 'source/setup_sms.php';
            $setupSMS = new setupSMS;
            $interpreter_phone = $setupSMS->format_phone($row['contactNo'], $row['interpreter_country']);
            $appendTime = $table != 'translation' ? " " . $row['assignTime'] : "";
            $sms_label =  $table == 'interpreter' ? "F2F" : ucwords($table);
            $message_body = "Your job has been cancelled" . $interpreter_payable_text . $sms_label . " Job ID:" . $email_id . "\nDate / Time:" . $assignDate . $appendTime . "\nIf reallocated it will appear on your App or portal";
            $sms_response = $setupSMS->send_sms($interpreter_phone, $message_body);
            $acttObj->insert("job_messages", array("order_id" => $email_id, "order_type" => $array_order_types[$table], "message_category" => 3, "interpreter_id" => $row['intrpName'], "created_by" => $_SESSION['cust_userId'], "message_body" => $message_body, "sent_to" => $interpreter_phone, "action_by" => 2));
            $message_inserted_id = $acttObj->con->insert_id;
            if ($message_inserted_id) {
                if ($sms_response['status'] == 0) {
                    $acttObj->update("job_messages", array("status" => 0), "id=" . $message_inserted_id);
                }
            }
        }
    }
    if ($table == 'translation') {
        $assignment_type = "Translation";
        $append_table = "
            <table>
            <tr>
            <td style='border: 1px solid black;padding:5px;text-align:center;background: #f33d3d;color: white;' colspan='2'><b>Cancelled Job Details</b></td>
            </tr>
            <tr>
            <td style='border: 1px solid red;padding:5px;'>Source Language</td>
            <td style='border: 1px solid red;padding:5px;'>" . $source . "</td>
            </tr>
            <tr>
            <td style='border: 1px solid red;padding:5px;'>Target Language</td>
            <td style='border: 1px solid red;padding:5px;'>" . $target . "</td>
            </tr>
            <tr>
            <td style='border: 1px solid red;padding:5px;'>Assignment Date</td>
            <td style='border: 1px solid red;padding:5px;'>" . $assignDate . "</td>
            </tr>
            <tr>
            <td style='border: 1px solid red;padding:5px;'>Document Type</td>
            <td style='border: 1px solid red;padding:5px;'>" . $acttObj->read_specific("tc_title", "trans_cat", "tc_id=" . $docType)['tc_title'] . "</td>
            </tr>
            <tr>
            <td style='border: 1px solid red;padding:5px;'>Translation Type(s)</td>
            <td style='border: 1px solid red;padding:5px;'>" . $acttObj->read_specific("GROUP_CONCAT(CONCAT(tt_title)  SEPARATOR ' <b> & </b> ') as tt_title", "trans_types", "tt_id IN (" . $trans_detail . ")")['tt_title'] . "</td>
            </tr>
            <tr>
            <td style='border: 1px solid red;padding:5px;'>Translation Category</td>
            <td style='border: 1px solid red;padding:5px;'>" . $acttObj->read_specific("GROUP_CONCAT(CONCAT(td_title)  SEPARATOR ' <b> & </b> ') as td_title", "trans_dropdown", "td_id IN (" . $transType . ")")['td_title'] . "</td>
            </tr>
            <tr>
            <td style='border: 1px solid red;padding:5px;'>Client Name</td>
            <td style='border: 1px solid red;padding:5px;'>" . $orgRef . "</td>
            </tr>
            <tr>
            <td style='border: 1px solid red;padding:5px;'>Delivery Type</td>
            <td style='border: 1px solid red;padding:5px;'>" . $deliveryType . "</td>
            </tr>
            </table>";
        $to_add = $inchEmail;
        $subject = "Cancellation of " . $assignment_type . " Project " . $email_id;
        $query_format = "SELECT em_format FROM email_format where id='9'";
        $result_format = mysqli_query($con, $query_format);
        $row_format = mysqli_fetch_array($result_format);
        //Get format from database
        $msg_body = $row_format['em_format'];
        $all_table = $write_charge . $append_table;
        $data   = ["[ORGCONTACT]", "[SOURCE]", "[ORGREF]", "[ASSIGNDATE]", "[CANCELLATION_TYPE]", "[CANCELLATION_REASON]", "[TABLE]"];
        $to_replace  = ["$orgContact", "$source", "$orgref", "$assignDate", "$write_cancel_type", "$write_cancel_remarks", "$all_table"];
        $message = str_replace($data, $to_replace, $msg_body);
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
                $mail->ClearAllRecipients();
                if ($row['inchEmail2']) {
                    $mail->addAddress($row['inchEmail2']);
                    $mail->addReplyTo($from_add, setupEmail::FROM_NAME);
                    $mail->isHTML(true);
                    $mail->Subject = $subject;
                    $mail->Body    = $message;
                    $mail->send();
                    $mail->ClearAllRecipients();
                }
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
        } catch (Exception $e) { ?>
            <script>
                alert("Mailer Library Error!");
            </script>
            <?php }
        //...........................for interpreter.............................
        if ($interpreter_id) {
            $to_add = $email;
            $subject = "Cancellation of " . $assignment_type . " Project " . $email_id;
            $query_format = "SELECT em_format FROM email_format where id='10'";
            $result_format = mysqli_query($con, $query_format);
            $row_format = mysqli_fetch_array($result_format);
            //Get format from database
            $msg_body = $row_format['em_format'];
            $all_table = $write_pay . $append_table . $int_notes;
            $data   = ["[NAME]", "[SOURCE]", "[ASSIGNDATE]", "[CANCELLATION_TYPE]", "[CANCELLATION_REASON]", "[TABLE]"];
            $to_replace  = ["$name", "$source", "$assignDate", "$write_cancel_type", "$write_cancel_remarks", "$all_table"];
            $message = str_replace($data, $to_replace, $msg_body);
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
            } catch (Exception $e) { ?>
                <script>
                    alert('<?php echo "Mailer Library Error!"; ?>');
                </script>
            <?php }
        }
    }

    if ($table == 'telephone') {
        $write_telep_cat = $post_telep_cat == '11' ? $assignIssue : $acttObj->read_specific("tpc_title", "telep_cat", "tpc_id=" . $telep_cat)['tpc_title'];
        $write_telep_type = $telep_cat == '11' ? '' : $acttObj->read_specific("GROUP_CONCAT(CONCAT(tpt_title)  SEPARATOR ' <b> & </b> ') as tpt_title", "telep_types", "tpt_id IN (" . $telep_type . ")")['tpt_title'];
        if ($post_telep_cat == '11') {
            $append_issue = "<tr><td style='border: 1px solid red;padding:5px;'>Assignment Category</td><td style='border: 1px solid red;padding:5px;'>Other</td></tr><tr><td style='border: 1px solid red;padding:5px;'>Assignment Details</td><td style='border: 1px solid red;padding:5px;'>" . $assignIssue . "</td></tr>";
        } else {
            $append_issue = "<tr><td style='border: 1px solid red;padding:5px;'>Assignment Category</td><td style='border: 1px solid red;padding:5px;'>" . $write_telep_cat . "</td></tr><tr><td style='border: 1px solid red;padding:5px;'>Assignment Details</td><td style='border: 1px solid red;padding:5px;'>" . $write_telep_type . "</td></tr>";
        }
        $write_comunic = $acttObj->read_specific("c_title", "comunic_types", "c_id=" . $comunic)['c_title'];
        $communication_type = empty($comunic) || $comunic == 11 ? "Telephone interpreting" : $write_comunic;
        $assignment_type = $communication_type;
        $append_table = "
            <table>
            <tr>
            <td style='border: 1px solid black;padding:5px;text-align:center;background: #f33d3d;color: white;' colspan='2'><b>Cancelled Job Details</b></td>
            </tr>
            <tr>
            <td style='border: 1px solid red;padding:5px;'>Communication Type</td>
            <td style='border: 1px solid red;padding:5px;'>" . $write_comunic . "</td>
            </tr>
            <tr>
            <td style='border: 1px solid red;padding:5px;'>Source Language</td>
            <td style='border: 1px solid red;padding:5px;'>" . $source . "</td>
            </tr>
            <tr>
            <td style='border: 1px solid red;padding:5px;'>Target Language</td>
            <td style='border: 1px solid red;padding:5px;'>" . $target . "</td>
            </tr>
            <tr>
            <td style='border: 1px solid red;padding:5px;'>Assignment Date</td>
            <td style='border: 1px solid red;padding:5px;'>" . $assignDate . "</td>
            <tr>
            <td style='border: 1px solid red;padding:5px;'>Assignment Time</td>
            <td style='border: 1px solid red;padding:5px;'>" . $assignTime . "</td>
            </tr>
            <tr>
            <td style='border: 1px solid red;padding:5px;'>Assignment Duration</td>
            <td style='border: 1px solid red;padding:5px;'>" . $get_dur . "</td>
            </tr>
            " . $append_issue . "
            <tr>
            <td style='border: 1px solid red;padding:5px;'>Report to</td>
            <td style='border: 1px solid red;padding:5px;'>" . $inchPerson . "</td>
            </tr>
            <tr>
            <td style='border: 1px solid red;padding:5px;'>Case Worker</td>
            <td style='border: 1px solid red;padding:5px;'>" . $orgContact . "</td>
            </tr>
            </table>";
        $to_add = $inchEmail;
        $subject = "Cancellation of " . $communication_type . " Project " . $email_id;
        $query_format = "SELECT em_format FROM email_format where id='11'";
        $result_format = mysqli_query($con, $query_format);
        $row_format = mysqli_fetch_array($result_format);
        //Get format from database
        $msg_body = $row_format['em_format'];
        $all_table = $write_charge . $append_table;
        $data   = ["[ORGCONTACT]", "[SOURCE]", "[ORGREF]", "[ASSIGNDATE]", "[ASSIGNTIME]", "[CANCELLATION_TYPE]", "[CANCELLATION_REASON]", "[TABLE]"];
        $to_replace  = ["$orgContact", "$source", "$orgRef", "$assignDate", "$assignTime", "$write_cancel_type", "$write_cancel_remarks", "$all_table"];
        $message = str_replace($data, $to_replace, $msg_body);
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
                $mail->ClearAllRecipients();
                if ($row['inchEmail2']) {
                    $mail->addAddress($row['inchEmail2']);
                    $mail->addReplyTo($from_add, setupEmail::FROM_NAME);
                    $mail->isHTML(true);
                    $mail->Subject = $subject;
                    $mail->Body    = $message;
                    $mail->send();
                    $mail->ClearAllRecipients();
                }
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
        } catch (Exception $e) { ?>
            <script>
                alert('<?php echo "Mailer Library Error!"; ?>');
            </script>
            <?php }
        //..............................for interpreter ............
        if ($interpreter_id) {
            $to_add = $email;
            $subject = "Cancellation of " . $communication_type . " Project " . $email_id;
            $query_format = "SELECT em_format FROM email_format where id='12'";
            $result_format = mysqli_query($con, $query_format);
            $row_format = mysqli_fetch_array($result_format);
            //Get format from database
            $msg_body = $row_format['em_format'];
            $all_table = $write_pay . $append_table . $int_notes;
            $data   = ["[NAME]", "[SOURCE]", "[ASSIGNDATE]", "[ASSIGNTIME]", "[CANCELLATION_TYPE]", "[CANCELLATION_REASON]", "[TABLE]"];
            $to_replace  = ["$name", "$source", "$assignDate", "$assignTime", "$write_cancel_type", "$write_cancel_remarks", "$all_table"];
            $message = str_replace($data, $to_replace, $msg_body);
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
            } catch (Exception $e) { ?>
                <script>
                    alert('<?php echo "Mailer Library Error!"; ?>');
                </script>
            <?php }
        }
    }

    if ($table == 'interpreter') {
        $assignment_type = "Face To Face Interpreting";
        $write_interp_cat = $interp_cat == '12' ? $assignIssue : $acttObj->read_specific("ic_title", "interp_cat", "ic_id=" . $interp_cat)['ic_title'];
        $write_interp_type = $interp_cat == '12' ? '' : $acttObj->read_specific("GROUP_CONCAT(CONCAT(it_title)  SEPARATOR ' <b> & </b> ') as it_title", "interp_types", "it_id IN (" . $interp_type . ")")['it_title'];
        if ($interp_cat == '12') {
            $append_issue = "<tr><td style='border: 1px solid red;padding:5px;'>Assignment Category</td><td style='border: 1px solid red;padding:5px;'>Other</td></tr><tr><td style='border: 1px solid red;padding:5px;'>Assignment Details</td><td style='border: 1px solid red;padding:5px;'>" . $assignIssue . "</td></tr>";
        } else {
            $append_issue = "<tr><td style='border: 1px solid red;padding:5px;'>Assignment Category</td><td style='border: 1px solid red;padding:5px;'>" . $write_interp_cat . "</td></tr><tr><td style='border: 1px solid red;padding:5px;'>Assignment Details</td><td style='border: 1px solid red;padding:5px;'>" . $write_interp_type . "</td></tr>";
        }
        $append_table = "
            <table>
            <tr>
            <td style='border: 1px solid black;padding:5px;text-align:center;background: #f33d3d;color: white;' colspan='2'><b>Cancelled Job Details</b></td>
            </tr>
            <tr>
            <td style='border: 1px solid red;padding:5px;'>Source Language</td>
            <td style='border: 1px solid red;padding:5px;'>" . $source . "</td>
            </tr>
            <tr>
            <td style='border: 1px solid red;padding:5px;'>Target Language</td>
            <td style='border: 1px solid red;padding:5px;'>" . $target . "</td>
            </tr>
            <tr>
            <td style='border: 1px solid red;padding:5px;'>Assignment Date</td>
            <td style='border: 1px solid red;padding:5px;'>" . $assignDate . "</td>
            </tr>
            <tr>
            <td style='border: 1px solid red;padding:5px;'>Assignment Time</td>
            <td style='border: 1px solid red;padding:5px;'>" . $assignTime . "</td>
            </tr>
            <tr>
            <td style='border: 1px solid red;padding:5px;'>Assignment Duration</td>
            <td style='border: 1px solid red;padding:5px;'>" . $get_dur . "</td>
            </tr>
            <tr>
            <td style='border: 1px solid red;padding:5px;'>Assignment Location</td>
            <td style='border: 1px solid red;padding:5px;'>" . (!empty(trim($buildingName))?htmlspecialchars($buildingName,ENT_QUOTES, 'UTF-8'):'') . (!empty(trim($street))?(', '.$street):'') . (!empty(trim($assignCity))?(', '.$assignCity):'') . (!empty(trim($postCode))?(', '.$postCode):'') . "</td>
            </tr>
            " . $append_issue . "
            <tr>
            <td style='border: 1px solid red;padding:5px;'>Report to</td>
            <td style='border: 1px solid red;padding:5px;'>" . $inchPerson . "</td>
            </tr>
            <tr>
            <td style='border: 1px solid red;padding:5px;'>Case Worker or Person Incharge</td>
            <td style='border: 1px solid red;padding:5px;'>" . $orgContact . "</td>
            </tr>
            <tr>
            <td style='border: 1px solid red;padding:5px;'>Client Name</td>
            <td style='border: 1px solid red;padding:5px;'>" . $orgRef . "</td>
            </tr>
            </table>";
        $to_add = $inchEmail;
        $subject = "Cancellation of " . $assignment_type . " Project " . $email_id;
        $query_format = "SELECT em_format FROM email_format where id='13'";
        $result_format = mysqli_query($con, $query_format);
        $row_format = mysqli_fetch_array($result_format);
        //Get format from database
        $msg_body = $row_format['em_format'];
        $all_table = $write_charge . $append_table;
        $data   = ["[ORGCONTACT]", "[SOURCE]", "[ORGREF]", "[ASSIGNDATE]", "[ASSIGNTIME]", "[BUILDINGNAME]", "[STREET]", "[ASSIGNCITY]", "[POSTCODE]", "[CANCELLATION_TYPE]", "[CANCELLATION_REASON]", "[TABLE]"];
        $to_replace  = ["$orgContact", "$source", "$orgRef", "$assignDate", "$assignTime", "$buildingName", "$street", "$assignCity", "$postCode", "$write_cancel_type", "$write_cancel_remarks", "$all_table"];
        $message = str_replace($data, $to_replace, $msg_body);
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
                $mail->ClearAllRecipients();
                if ($row['inchEmail2']) {
                    $mail->addAddress($row['inchEmail2']);
                    $mail->addReplyTo($from_add, setupEmail::FROM_NAME);
                    $mail->isHTML(true);
                    $mail->Subject = $subject;
                    $mail->Body    = $message;
                    $mail->send();
                    $mail->ClearAllRecipients();
                }
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
        } catch (Exception $e) { ?>
            <script>
                alert('<?php echo "Mailer Library Error!"; ?>');
            </script>
            <?php }
        //..............................for interpreter ...........
        if ($interpreter_id) {
            $to_add = $email;
            $subject = "Cancellation of " . $assignment_type . " Project " . $email_id;
            $query_format = "SELECT em_format FROM email_format where id='14'";
            $result_format = mysqli_query($con, $query_format);
            $row_format = mysqli_fetch_array($result_format);
            //Get format from database
            $msg_body = $row_format['em_format'];
            $all_table = $write_pay . $append_table . $int_notes;
            $data   = ["[NAME]", "[SOURCE]", "[ASSIGNDATE]", "[ASSIGNTIME]", "[BUILDINGNAME]", "[STREET]", "[ASSIGNCITY]", "[POSTCODE]", "[CANCELLATION_TYPE]", "[CANCELLATION_REASON]", "[TABLE]"];
            $to_replace  = ["$name", "$source", "$assignDate", "$assignTime", "$buildingName", "$street", "$assignCity", "$postCode", "$write_cancel_type", "$write_cancel_remarks", "$all_table"];
            $message = str_replace($data, $to_replace, $msg_body);
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
            } catch (Exception $e) { ?>
                <script>
                    alert('<?php echo "Mailer Library Error!"; ?>');
                </script>
    <?php   }
        }
    }

    //Send notification on cancellation
    if ($interpreter_id) {
        $title = "Your job has been cancelled !";
        $sub_title = $source . " " . $assignment_type . " assignment at " . $row['assignDate'] . " is now cancelled.";
        $type_key = "jc";
        //Send notification on APP
        $check_id = $acttObj->read_specific('id', 'notify_new_doc', 'interpreter_id=' . $interpreter_id)['id'];
        if (empty($check_id)) {
            $acttObj->insert('notify_new_doc', array("interpreter_id" => $interpreter_id, "status" => '1'));
        } else {
            $existing_notification = $acttObj->read_specific("new_notification", "notify_new_doc", "interpreter_id=" . $interpreter_id)['new_notification'];
            $acttObj->update('notify_new_doc', array("new_notification" => $existing_notification + 1), array("interpreter_id" => $interpreter_id));
        }
        $array_tokens = explode(',', $acttObj->read_specific("GROUP_CONCAT( DISTINCT token) as tokens", "int_tokens", "int_id=" . $interpreter_id)['tokens']);
        if (!empty($array_tokens)) {
            $acttObj->insert('app_notifications', array("title" => $title, "sub_title" => $sub_title, "dated" => date('Y-m-d'), "int_ids" => $interpreter_id, "read_ids" => $interpreter_id, "type_key" => $type_key));
            foreach ($array_tokens as $token) {
                if (!empty($token)) {
                    $full_data = "{ \"notification\": {    \"title\": \"$title\",     \"text\": \"$sub_title\"   }, \"data\": { \"click_action\": \"FLUTTER_NOTIFICATION_CLICK\",\"status\": \"done\" },    \"to\" : \"$token\"}";
                    $acttObj->notification($token, $title, $sub_title, $full_data);
                }
            }
        }
    }
    ?>
    <script>
        alert("This job has been cancelled successfully. Thank you");
        window.close();
        <?php if ($cn_r_id == 9) { ?>
            window.opener.location.href = '<?=$array_rearrange_order[$table] . "?id=" . $_GET['job_id']?>';
        <?php } else { ?>
            window.onunload = refreshParent;
        <?php } ?>

        function refreshParent() {
            window.opener.location.reload();
        }
    </script>
<?php }
if (isset($_POST['no'])) {
    echo "<script>window.close();</script>";
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
    <title>Booking Cancellation</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" />
    <script src="lsuk_system/js/jquery-1.11.3.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    <link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
    <script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>
</head>

<body>
    <div class="container-fluid">
        <form action="" method="post">
            <div class="row">
                <input type="hidden" required name="cancelled_language" id="cancelled_language" value="<?= $get_job_data['source']; ?>">
                <center>
                    <h4><?php echo '<b class="text-success">' . $row['orgzName'] . '</b> ' . $array_types[$table]; ?> Job Cancellation ID : <span class="label label-danger"><?php echo $email_id; ?></span></h4>
                </center>

                <div class="form-group col-xs-3">
                    <label for="assign_date">Assignment Date</label>
                    <input type="date" readonly required name="assign_date" id="assign_date" value="<?= $assign_date; ?>" class="form-control">
                </div>
                <div class="form-group col-xs-3">
                    <label for="assign_time">Assignment Time</label>
                    <input type="time" readonly required name="assign_time" id="assign_time" value="<?= $assign_time; ?>" class="form-control">
                </div>
                <div class="form-group col-xs-3" id="div_append">
                    <label>Job Cancelled By</label>
                    <select id="order_cancelledby" name="order_cancelledby" onchange="get_cd();" required class="form-control">
                        <option value="cl" selected>Client Portal</option>
                    </select>
                </div>
                <div class="form-group col-sm-11 hidden" id="div_order_cancel_remarks">
                    <textarea name="order_cancel_remarks" id="order_cancel_remarks" rows="3" class="form-control hidden" placeholder="Write reason of cancellation here ..."></textarea>
                </div>
                <div class="col-xs-12 div_cancellation_status hidden">
                    <div style="margin-top: 5px;" class="form-group col-xs-6 hidden" id="div_payable">
                        <label class="checkbox-inline">
                            <input type="checkbox" id="pay_int" name="pay_int" value="1" data-toggle="toggle" data-on="Yes" data-off="No"> <b>Do you want to pay to Interpreter?</b>
                        </label>
                    </div>
                    <div style="margin-top: 5px;" class="form-group col-xs-6 hidden" id="div_charge_client">
                        <label class="checkbox-inline">
                            <input type="checkbox" id="charge_client" name="charge_client" value="1" data-toggle="toggle" data-on="Yes" data-off="No"> <b>Do you want to charge the Client?</b>
                        </label>
                    </div>
                </div>
                <hr>
                <center>
                    <div class="form-group col-xs-12 hidden" id="div_buttons">
                        <h4 class="text-danger">Are you sure you want to cancel this booking?</h4>
                        <input type="submit" name="yes" id="yes" value="Yes" class="btn btn-primary" onclick="confirm_cancellation();" />
                        <input type="submit" name="no" value="No" class="btn btn-warning" />
                    </div>
                    <div class="form-group col-sm-10 col-sm-offset-1 alert alert-info hidden" id="div_confirmation">
                        <h4><b>Please wait!!! Cancellation action is in progress. It may take a while to complete.</b></h4>
                    </div>
                </center>
            </div>
        </form>
    </div>
</body>
<script type="text/javascript">
    function confirm_cancellation() {
        if (confirm('Are you sure to CANCEL this job?')) {
            $('#div_buttons').addClass('hidden');
            $('#div_confirmation').removeClass('hidden');
            return true;
        } else {
            $('#div_buttons').removeClass('hidden');
            $('#div_confirmation').addClass('hidden');
            return false;
        }
    }
    function get_cd() {
        var cd_for = 'cl';
        var cancel = 'yes';
        var lang = $('#cancelled_language').val();
        var assign_date = $('#assign_date').val();
        var assign_time = $('#assign_time').val();
        $('#div_cd,#div_reason,#div_cancel_details').remove();
        $.ajax({
            url: 'lsuk_system/ajax_add_comp_data.php',
            method: 'post',
            dataType: 'json',
            data: {
                assign_date: assign_date,
                assign_time: assign_time,
                cd_for: cd_for,
                cancel: cancel,
                lang: lang
            },
            success: function(response) {
                if (response['data']) {
                    $(response['data']).insertAfter("#div_append");
                }
                if (response['charge_client'] == 1) {
                    $('#charge_client').bootstrapToggle('on');
                } else {
                    $('#charge_client').bootstrapToggle('off');
                }
                if (response['pay_int'] == 1) {
                    $('#pay_int').bootstrapToggle('on');
                } else {
                    $('#pay_int').bootstrapToggle('off');
                }
            },
            error: function(xhr) {
                alert("An error occured: " + xhr.status + " " + xhr.statusText);
            }
        });
    }

    function reset_filters() {
        $("#order_cancelledby option[value='']").prop('selected', true);
        $('#div_cd,#div_cancel_details,#div_reason,#div_order_cancel_remarks,#div_payable,#div_charge_client,#div_buttons').addClass('hidden');
    }

    function get_buttons(element) {
        $('#div_buttons,#div_payable,#div_charge_client').removeClass('hidden');
        if ($(element).val() == 15) {
            $('#div_order_cancel_remarks,#order_cancel_remarks').removeClass('hidden');
        } else {
            $('#div_order_cancel_remarks,#order_cancel_remarks').addClass('hidden');
        }
    }

    $(document).ready(function() {
        get_cd();
    });
</script>

</html>