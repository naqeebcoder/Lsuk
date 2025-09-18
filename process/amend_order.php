<?php
session_start();
include '../source/setup_email.php';
include '../lsuk_system/actions.php'; 

if (isset($_POST['btn_amend_order'])) {
    if ($_SESSION['cust_userId']) {
    $job_id = $_POST['amend_id'];
    $array_tables = array("1" => "interpreter", "2" => "telephone", "3" => "translation");
    $row_amend_request = $obj->read_specific("*", "amendment_requests", "order_type='" . $_POST['amend_type'] . "' AND order_id=" . $_POST['amend_id']);
    if ($row_amend_request['id']) {
        $_SESSION['returned_message'] = '<center><div class="alert alert-danger alert-dismissible show col-md-12" role="alert">
            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
            <strong>Alert!</strong> There is already an amendment request submitted for this order on ' . $misc->dated($row_amend_request['created_date']) . '. Contact LSUK support or wait for status update. Thank you
        </div></center>';
    } else {
        $insert_array = array(
            "order_id" => $_POST['amend_id'], "order_type" => $_POST['amend_type'], "create_user_name" => $_SESSION['cust_UserName'], "company_id" => $_SESSION['company_id'], "created_date" => date('Y-m-d H:i:s'),"amend_reason" => trim($_POST['amend_reason']), "amend_date" => $_POST['amend_date'], "amend_time" => $_POST['amend_time']
        );
        $done = $obj->insert("amendment_requests", $insert_array);
        if ($done) {
            // Insert job note for admin
            $obj->insert('jobnotes', array('jobNote' => 'Client requested amendment at ' . date("Y-m-d H:i:s") . "<br>" . trim($_POST['amend_reason']) . " & new booking date-time is " . $misc->dated($_POST['amend_date']) . " " . $_POST['amend_time'], 'tbl' => $array_tables[$_POST['amend_type']], 'time' => $misc->sys_datetime_db(), 'fid' => $_POST['amend_id'], 'submitted' => $_SESSION['cust_UserName'], 'dated' => date('Y-m-d')));
            $_SESSION['returned_message'] = '<center><div class="alert alert-success alert-dismissible show col-md-12" role="alert">
                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                <strong>Success!</strong> Your amendment request for this order has been submitted successfully. Thank you
            </div></center>';
        } else {
            $_SESSION['returned_message'] = '<center><div class="alert alert-danger alert-dismissible show col-md-12" role="alert">
                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                <strong>Failed!</strong> Failed to update your amendment request for this order. Please try again
            </div></center>';
        }
    }

    $table = $array_tables[$_POST['amend_type']];

    if (session_id() == '' || !isset($_SESSION)) {
        session_start();
    }
    $chk_booked = $obj->read_specific("intrpName", "$table", "id=" . $job_id)['intrpName'];
    if ($table != 'translation') {
        $amend_id = $_POST['amend_id'];
        $amend_effect = $obj->read_specific('effect', 'amend_options', 'id=' . $amend_id)['effect'];
    }
    $amend_note_text = $_POST['amend_reason'];
    $client_email = '0';
    $int_email = '0';
    //update database table record here

    if (empty($chk_booked)) {
        $row = $obj->read_specific("$table.*,comp_reg.name as orgzName", "$table,comp_reg", "$table.orgName=comp_reg.abrv AND $table.id=" . $job_id);
    } else {
        $row = $obj->read_specific("$table.*,interpreter_reg.name,interpreter_reg.email,interpreter_reg.contactNo,interpreter_reg.country as interpreter_country,comp_reg.name as orgzName", "$table,interpreter_reg,comp_reg", "$table.intrpName=interpreter_reg.id AND $table.orgName=comp_reg.abrv AND $table.id=" . $job_id);
    }
    $email = $row['email'];
    $source = $row['source'];
    $target = $row['target'];
    $orgRef = $row['orgRef'];

    $new_date = $_POST['a_date'];
    $new_time = $_POST['a_time'];

    $I_Comments = $row['I_Comments'];
    $chk_orderCancelatoin = $row['orderCancelatoin'];
    $chk_order_cancel_flag = $row['order_cancel_flag'];
    $submited='';
    $submited = $row['submited'];
    
    if(isset($new_date) && !empty($new_date)){
        $assignDate = $misc->dated($new_date);
    }else{
        $assignDate = $misc->dated($row['assignDate']);
    }
    if(isset($new_time) && !empty($new_time)){
        $assignTime = $new_time;
    }else{
        $assignTime = $row['assignTime'];
    }

    if ($table == 'interpreter' || $table == 'telephone') {
        $from_add = setupEmail::INFO_EMAIL;
        $from_password = setupEmail::INFO_PASSWORD;        
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
        $assignDate = $assignDate;
    }
   
    $assignIssue = $row['assignIssue'];
    $orgContact = $row['orgContact'];
    $inchPerson = $row['inchPerson'];
    $inchEmail = $row['inchEmail'];
    $name = $row['name'];
    $remrks = $row['remrks'] ?: '';
    //roll back salary to payable
    $obj->update('bid', array('allocated' => 0), "job=" . $job_id . " AND tabName='" . $table . "'");
    //if interpreter OR telephone
    if ($table == 'interpreter' || $table == 'telephone') {
        if ($amend_effect == '1') {
            $put_amend = '1';
        } else {
            $put_amend = '0';
        }
    } else {
        $affect_int = $_POST['affect_int'];
        if ($affect_int == '1') {
            $put_amend = '1';
        } else {
            $put_amend = '0';
        }
    }
    $amend_counter = $obj->read_specific("count(id) as amend_counter", "amended_records", "order_id=" . $job_id . " and type='" . $table . "'")['amend_counter'];
    if ($amend_counter > 0) {
        $exist_amend = '1';
    } else {
        $exist_amend = '0';
    }
    if ($put_amend == '1' && $exist_amend == '0') {
        $obj->insert('amended_records', array('order_id' => $job_id, 'type' => $table, 'interpreter_id' => $row['intrpName'], 'amended_by' => $_SESSION['cust_UserName'], "dated" => date("Y-m-d")));
        $amended_id = $obj->con->insert_id;
    }
    $obj->update($table, array("intrpName" => "", "pay_int" => 1, "amend_note" => $amend_note_text, "edited_by" => $_SESSION['cust_UserName'], "edited_date" => date("Y-m-d H:i:s")), "id=" . $job_id);
    // $obj->new_old_table('hist_' . $table, $table, $job_id);
    $array_types = array("interpreter" => "F2F", "telephone" => "TP", "translation" => "TR");
    $index_mapping = array(
        'Interpreter.ID' => 'intrpName', 'Note' => 'amend_note', 'Pay.Interpreter' => 'pay_int'
    );

    $old_values = array();
    $new_values = array();
    $get_new_data = $obj->read_specific("*", "$table", "id=" . $job_id);

    foreach ($index_mapping as $key => $value) {
        if (isset($get_new_data[$value])) {
            $old_values[$key] = $row[$value];
            $new_values[$key] = $get_new_data[$value];
        }
    }
    $obj->log_changes(json_encode($old_values), json_encode($new_values), $job_id, $table, "update", 0, $_SESSION['cust_UserName'], "amend_order");

    //Below history function needs to be removed
    // $obj->insert("daily_logs", array("action_id" => 7, "user_id" => $_SESSION['userId'], "details" => $array_types[$table] . " Job ID: " . $job_id));
    //Create job note
    if ($table != 'translation') {
        // $amend_type = $obj->read_specific('value', 'amend_options', 'id=' . $amend_id)['value'];
        // $amend_tp = "Amendment Type: " . $amend_type . "<br>";
        $amend_tp = "Amendment Type: Ammendment from Client Portal<br>";
    } else {
        $amend_tp = "";
    }
    $job_note = $amend_tp . "Was allocated to : " . $row['name'] . "<br>Amendment Note: " . $amend_note_text;
    $obj->insert('jobnotes', array('jobNote' => $obj->con->real_escape_string($job_note), 'tbl' => $table, 'time' => $misc->sys_datetime_db(), 'fid' => $job_id, 'submitted' => $_SESSION['cust_UserName'], 'dated' => date('Y-m-d')));
    //Send SMS for cancel job
    if (isset($_SESSION['cust_userId'])) {
        $array_order_types = array("interpreter" => 1, "telephone" => 2, "translation" => 3);
        $get_application = $obj->read_specific("*", "job_messages", "order_type=" . $array_order_types[$table] . " AND order_id=" . $job_id . " AND interpreter_id=" . $row['intrpName'] . " AND message_category=4");
        if (empty($get_application['id'])) {
            //Adding config for SMS
            include '../source/setup_sms.php';
            $setupSMS = new setupSMS;
            $interpreter_phone = $setupSMS->format_phone($row['contactNo'], $row['interpreter_country']);
            $appendTime = $table != 'translation' ? " " . $row['assignTime'] : "";
            $sms_label =  $table == 'interpreter' ? "F2F" : ucwords($table);
            $message_body = "Your job has been taken away\n" . $sms_label . " Job ID:" . $job_id . "\nDate / Time:" . $assignDate . $appendTime . "\nIf reallocated it will appear on your App or portal";
            $sms_response = $setupSMS->send_sms($interpreter_phone, $message_body);
            $obj->insert("job_messages", array("order_id" => $job_id, "order_type" => $array_order_types[$table], "message_category" => 4, "interpreter_id" => $row['intrpName'],  "message_body" => $message_body, "sent_to" => $interpreter_phone));
            $message_inserted_id = $obj->con->insert_id;
            if ($message_inserted_id) {
                if ($sms_response['status'] == 0) {
                    $obj->update("job_messages", array("status" => 0), "id=" . $message_inserted_id);
                }
            }
        }
    }

    // Check if interpreter was responsible & needs penalty deduction from his account
    if (isset($_POST['status']) && $_POST['status'] == 2 && $_POST['given_amount']) {
        $array_type_idz = array("interpreter" => 1, "telephone" => 2, "translation" => 3);
        $array_tables = array("interpreter" => "Face To Face", "telephone" => "Telephone", "translation" => "Translation");
        $payable_date = $_POST['payable_date'] ?: date("Y-m-d");
        $data = array(
            "interpreter_id" => $row['intrpName'],
            "type_id" => ($_POST['type_id'] ?: 3),
            "job_type" => $array_type_idz[$table],
            "job_id" => $job_id,
            "loan_amount" => $_POST['given_amount'],
            "given_amount" => $_POST['given_amount'],
            "payable_date" => date('Y-m-01', strtotime($payable_date)),
            "duration" => $_POST['duration'],
            "percentage" => $_POST['percentage'],
            "reason" => "Penalty for " . $array_tables[$table] . " Job ID# " . $job_id,
            "created_date" => date("Y-m-d H:i:s"),
            "accepted_date" => date("Y-m-d H:i:s"),
            "status" => 2
        );
        $obj->insert("loan_requests", $data);
    }

    if ($table == 'translation') {
        $append_table = "
            <table>
            <tr>
            <td style='border: 1px solid #020202;padding: 5px;text-align: center;background: #58abab;color: white;' colspan='2'><b>Amended Job Details</b></td>
            </tr>
            <tr>
            <td style='border: 1px solid #58abab;padding:5px;'>Source Language</td>
            <td style='border: 1px solid #58abab;padding:5px;'>" . $source . "</td>
            </tr>
            <tr>
            <td style='border: 1px solid #58abab;padding:5px;'>Target Language</td>
            <td style='border: 1px solid #58abab;padding:5px;'>" . $target . "</td>
            </tr>
            <tr>
            <td style='border: 1px solid #58abab;padding:5px;'>Assignment Date</td>
            <td style='border: 1px solid #58abab;padding:5px;'>" . $assignDate . "</td>
            </tr>
            <tr>
            <td style='border: 1px solid #58abab;padding:5px;'>Document Type</td>
            <td style='border: 1px solid #58abab;padding:5px;'>" . $obj->read_specific("tc_title", "trans_cat", "tc_id=" . $docType)['tc_title'] . "</td>
            </tr>
            <tr>
            <td style='border: 1px solid #58abab;padding:5px;'>Translation Type(s)</td>
            <td style='border: 1px solid #58abab;padding:5px;'>" . $obj->read_specific("GROUP_CONCAT(CONCAT(tt_title)  SEPARATOR ' <b> & </b> ') as tt_title", "trans_types", "tt_id IN (" . $trans_detail . ")")['tt_title'] . "</td>
            </tr>
            <tr>
            <td style='border: 1px solid #58abab;padding:5px;'>Translation Category</td>
            <td style='border: 1px solid #58abab;padding:5px;'>" . $obj->read_specific("GROUP_CONCAT(CONCAT(td_title)  SEPARATOR ' <b> & </b> ') as td_title", "trans_dropdown", "td_id IN (" . $transType . ")")['td_title'] . "</td>
            </tr>
            <tr>
            <td style='border: 1px solid #58abab;padding:5px;'>Client Name</td>
            <td style='border: 1px solid #58abab;padding:5px;'>" . $orgRef . "</td>
            </tr>
            <tr>
            <td style='border: 1px solid #58abab;padding:5px;'>Delivery Type</td>
            <td style='border: 1px solid #58abab;padding:5px;'>" . $deliveryType . "</td>
            </tr>
            </table>";
        $to_add = $inchEmail;
        $subject = "Amending Translation Project " . $job_id;
        $row_format = $obj->read_specific("em_format", "email_format", "id=15");
        //Get format from database
        $msg_body = $row_format['em_format'];
        $data   = ["[ORGCONTACT]", "[SOURCE]", "[ASSIGNDATE]", "[AMENDMENT_REASON]", "[TABLE]"];
        $to_replace  = ["$orgContact", "$source", "$assignDate", "$amend_note_text", "$append_table"];
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
                    if($row['inchEmail2']){
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
            } catch (Exception $e) { 
                $_SESSION['returned_message'] = '<center><div class="alert alert-danger alert-dismissible show col-md-8 col-md-offset-2" role="alert">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                    <strong>Mailer Library Error!</strong> ' . $e->getMessage() . '
                </div></center>';    
            }
        //...........................for interpreter.............................
        if (!empty($chk_booked)) {
            $to_add = $email;
            $subject = "Amending Translation Project " . $job_id;
            $row_format = $obj->read_specific("em_format", "email_format", "id=16");
            //Get format from database
            $msg_body = $row_format['em_format'];
            if (!empty($remrks)) {
                $append_table .= "<br><u><b>NOTES FOR THIS JOB:</b></u><br>" . $remrks . "<br>";
            }
            $data   = ["[NAME]", "[SOURCE]", "[ASSIGNDATE]", "[AMENDMENT_REASON]", "[TABLE]"];
            $to_replace  = ["$name", "$source", "$assignDate", "$amend_note_text", "$append_table"];
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
                } catch (Exception $e) {
                    $_SESSION['returned_message'] = '<center><div class="alert alert-danger alert-dismissible show col-md-8 col-md-offset-2" role="alert">
                        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                        <strong>Mailer Library Error!</strong> ' . $e->getMessage() . '
                    </div></center>';    
                }
        }

    }
    //end if translation 

    if ($table == 'telephone') {
        $write_telep_cat = $telep_cat == '11' ? $assignIssue : $obj->read_specific("tpc_title", "telep_cat", "tpc_id=" . $telep_cat)['tpc_title'];
        $write_telep_type = $telep_cat == '11' ? '' : $obj->read_specific("GROUP_CONCAT(CONCAT(tpt_title)  SEPARATOR ' <b> & </b> ') as tpt_title", "telep_types", "tpt_id IN (" . $telep_type . ")")['tpt_title'];
        if ($telep_cat == '11') {
            $append_issue = "<tr><td style='border: 1px solid #58abab;padding:5px;'>Assignment Category</td><td style='border: 1px solid #58abab;padding:5px;'>Other</td></tr><tr><td style='border: 1px solid #58abab;padding:5px;'>Assignment Details</td><td style='border: 1px solid #58abab;padding:5px;'>" . $assignIssue . "</td></tr>";
        } else {
            $append_issue = "<tr><td style='border: 1px solid #58abab;padding:5px;'>Assignment Category</td><td style='border: 1px solid #58abab;padding:5px;'>" . $write_telep_cat . "</td></tr><tr><td style='border: 1px solid #58abab;padding:5px;'>Assignment Details</td><td style='border: 1px solid #58abab;padding:5px;'>" . $write_telep_type . "</td></tr>";
        }
        $write_comunic = $obj->read_specific("c_title", "comunic_types", "c_id=" . $comunic)['c_title'];
        $communication_type = empty($comunic) || $comunic == 11 ? "Telephone interpreting" : $write_comunic;
        $append_table = "
            <table>
            <tr>
            <td style='border: 1px solid #020202;padding: 5px;text-align: center;background: #58abab;color: white;' colspan='2'><b>Amended Job Details</b></td>
            </tr>
            <tr>
            <td style='border: 1px solid #58abab;padding:5px;'>Communication Type</td>
            <td style='border: 1px solid #58abab;padding:5px;'>" . $write_comunic . "</td>
            </tr>
            <tr>
            <td style='border: 1px solid #58abab;padding:5px;'>Source Language</td>
            <td style='border: 1px solid #58abab;padding:5px;'>" . $source . "</td>
            </tr>
            <tr>
            <td style='border: 1px solid #58abab;padding:5px;'>Target Language</td>
            <td style='border: 1px solid #58abab;padding:5px;'>" . $target . "</td>
            </tr>
            <tr>
            <td style='border: 1px solid #58abab;padding:5px;'>Assignment Date</td>
            <td style='border: 1px solid #58abab;padding:5px;'>" . $assignDate . "</td>
            <tr>
            <td style='border: 1px solid #58abab;padding:5px;'>Assignment Time</td>
            <td style='border: 1px solid #58abab;padding:5px;'>" . $assignTime . "</td>
            </tr>
            <tr>
            <td style='border: 1px solid #58abab;padding:5px;'>Assignment Duration</td>
            <td style='border: 1px solid #58abab;padding:5px;'>" . $assignDur . "</td>
            </tr>
            " . $append_issue . "
            <tr>
            <td style='border: 1px solid #58abab;padding:5px;'>Report to</td>
            <td style='border: 1px solid #58abab;padding:5px;'>" . $inchPerson . "</td>
            </tr>
            <tr>
            <td style='border: 1px solid #58abab;padding:5px;'>Case Worker</td>
            <td style='border: 1px solid #58abab;padding:5px;'>" . $orgContact . "</td>
            </tr>
            </table>";
        $to_add = $inchEmail;
        $subject = "Amending " . $communication_type . " Project " . $job_id;
        $row_format = $obj->read_specific("em_format", "email_format", "id=17");
        //Get format from database
        $msg_body = $row_format['em_format'];
        $data   = ["[ORGCONTACT]", "[SOURCE]", "[ASSIGNDATE]", "[ASSIGNTIME]", "[AMENDMENT_REASON]", "[TABLE]"];
        $to_replace  = ["$orgContact", "$source", "$assignDate", "$assignTime", "$amend_note_text", "$append_table"];
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
                    if($row['inchEmail2']){
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
            } catch (Exception $e) {
                $_SESSION['returned_message'] = '<center><div class="alert alert-danger alert-dismissible show col-md-8 col-md-offset-2" role="alert">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                    <strong>Mailer Library Error!</strong> ' . $e->getMessage() . '
                </div></center>';    
            }
        //..............................for interpreter .........
        if (!empty($chk_booked)) {
            $to_add = $email;
            $subject = "Amending " . $communication_type . " Project " . $job_id;
            $row_format = $obj->read_specific("em_format", "email_format", "id=18");
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
            $data   = ["[NAME]", "[SOURCE]", "[ASSIGNDATE]", "[ASSIGNTIME]", "[AMENDMENT_REASON]", "[TABLE]"];
            $to_replace  = ["$name", "$source", "$assignDate", "$assignTime", "$amend_note_text", "$append_table"];
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
                } catch (Exception $e) {
                    $_SESSION['returned_message'] = '<center><div class="alert alert-danger alert-dismissible show col-md-8 col-md-offset-2" role="alert">
                        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                        <strong>Mailer Library Error!</strong> ' . $e->getMessage() . '
                    </div></center>';
                }
        }

    } //if telephone

    if ($table == 'interpreter') {
        $write_interp_cat = $interp_cat == '12' ? $assignIssue : $obj->read_specific("ic_title", "interp_cat", "ic_id=" . $interp_cat)['ic_title'];
        $write_interp_type = $interp_cat == '12' ? '' : $obj->read_specific("GROUP_CONCAT(CONCAT(it_title)  SEPARATOR ' <b> & </b> ') as it_title", "interp_types", "it_id IN (" . $interp_type . ")")['it_title'];
        if ($interp_cat == '12') {
            $append_issue = "<tr><td style='border: 1px solid #58abab;padding:5px;'>Assignment Category</td><td style='border: 1px solid #58abab;padding:5px;'>Other</td></tr><tr><td style='border: 1px solid #58abab;padding:5px;'>Assignment Details</td><td style='border: 1px solid #58abab;padding:5px;'>" . $assignIssue . "</td></tr>";
        } else {
            $append_issue = "<tr><td style='border: 1px solid #58abab;padding:5px;'>Assignment Category</td><td style='border: 1px solid #58abab;padding:5px;'>" . $write_interp_cat . "</td></tr><tr><td style='border: 1px solid #58abab;padding:5px;'>Assignment Details</td><td style='border: 1px solid #58abab;padding:5px;'>" . $write_interp_type . "</td></tr>";
        }
        $append_table = "
            <table>
            <tr>
            <td style='border: 1px solid #020202;padding: 5px;text-align: center;background: #58abab;color: white;' colspan='2'><b>Amended Job Details</b></td>
            </tr>
            <tr>
            <td style='border: 1px solid #58abab;padding:5px;'>Source Language</td>
            <td style='border: 1px solid #58abab;padding:5px;'>" . $source . "</td>
            </tr>
            <tr>
            <td style='border: 1px solid #58abab;padding:5px;'>Target Language</td>
            <td style='border: 1px solid #58abab;padding:5px;'>" . $target . "</td>
            </tr>
            <tr>
            <td style='border: 1px solid #58abab;padding:5px;'>Assignment Date</td>
            <td style='border: 1px solid #58abab;padding:5px;'>" . $assignDate . "</td>
            </tr>
            <tr>
            <td style='border: 1px solid #58abab;padding:5px;'>Assignment Time</td>
            <td style='border: 1px solid #58abab;padding:5px;'>" . $assignTime . "</td>
            </tr>
            <tr>
            <td style='border: 1px solid #58abab;padding:5px;'>Assignment Duration</td>
            <td style='border: 1px solid #58abab;padding:5px;'>" . $assignDur . "</td>
            </tr>
            <tr>
            <td style='border: 1px solid #58abab;padding:5px;'>Assignment Location</td>
            <td style='border: 1px solid #58abab;padding:5px;'>" . (!empty(trim($buildingName))?htmlspecialchars($buildingName,ENT_QUOTES, 'UTF-8'):'') . (!empty(trim($street))?(', '.$street):'') . (!empty(trim($assignCity))?(', '.$assignCity):'') . (!empty(trim($postCode))?(', '.$postCode):'') . "</td>
            </tr>
            " . $append_issue . "
            <tr>
            <td style='border: 1px solid #58abab;padding:5px;'>Report to</td>
            <td style='border: 1px solid #58abab;padding:5px;'>" . $inchPerson . "</td>
            </tr>
            <tr>
            <td style='border: 1px solid #58abab;padding:5px;'>Case Worker or Person Incharge</td>
            <td style='border: 1px solid #58abab;padding:5px;'>" . $orgContact . "</td>
            </tr>
            <tr>
            <td style='border: 1px solid #58abab;padding:5px;'>Client Name</td>
            <td style='border: 1px solid #58abab;padding:5px;'>" . $orgRef . "</td>
            </tr>
            </table>";
        $to_add = $inchEmail;
        $subject = "Amending Face To Face Interpreting Project " . $job_id;
        $row_format = $obj->read_specific("em_format", "email_format", "id=19");
        //Get format from database
        $msg_body = $row_format['em_format'];
        $data   = ["[ORGCONTACT]", "[SOURCE]", "[ASSIGNDATE]", "[ASSIGNTIME]", "[AMENDMENT_REASON]", "[TABLE]"];
        $to_replace  = ["$orgContact", "$source", "$assignDate", "$assignTime", "$amend_note_text", "$append_table"];
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
                    if($row['inchEmail2']){
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
            } catch (Exception $e) {
                $_SESSION['returned_message'] = '<center><div class="alert alert-danger alert-dismissible show col-md-8 col-md-offset-2" role="alert">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                    <strong>Mailer Library Error!</strong> ' . $e->getMessage() . '
                </div></center>';    
            }
        //..............................for interpreter .........
        if (!empty($chk_booked)) {
            $to_add = $email;
            $subject = "Amending Face To Face Interpreting Project " . $job_id;
            $row_format = $obj->read_specific("em_format", "email_format", "id=20");
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
            $data   = ["[NAME]", "[SOURCE]", "[ASSIGNDATE]", "[ASSIGNTIME]", "[AMENDMENT_REASON]", "[TABLE]"];
            $to_replace  = ["$name", "$source", "$assignDate", "$assignTime", "$amend_note_text", "$append_table"];
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
                } catch (Exception $e) {
                    $_SESSION['returned_message'] = '<center><div class="alert alert-danger alert-dismissible show col-md-8 col-md-offset-2" role="alert">
                        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                        <strong>Mailer Library Error!</strong> ' . $e->getMessage() . '
                    </div></center>';
                }
        }

    }
    } else {
        $_SESSION['returned_message'] = '<center><div class="alert alert-danger alert-dismissible show col-md-12" role="alert">
            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
            <strong>Failed!</strong> Failed to update your amendment request for this order. Please try again
        </div></center>';
    }
    header('Location: ' . $_POST['redirect_url']);
}