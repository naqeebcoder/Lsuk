<?php if (session_id() == '' || !isset($_SESSION)) {
    session_start();
}
include '../source/setup_email.php';
if ($_SESSION['prv'] == 'Management') {
    $managment = 1;
} else {
    $managment = 0;
}

include 'actions.php';
$allowed_type_idz = "10,24,37,50,67,121";
//Check if user has current action allowed
if ($_SESSION['is_root'] == 0) {
    $get_page_access = $obj->read_specific("id", "action_permissions", "user_id=" . $_SESSION['userId'] . " AND action_id IN (" . $allowed_type_idz . ")")['id'];
    if (empty($get_page_access)) {
        die("<center><h2 class='text-center text-danger'>You do not have access to <u>Confirm Temporary</u> action for jobs!<br>Kindly contact admin for further process.</h2></center>");
    }
} ?>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="css/bootstrap.css">
<title>Confirm Temporary Record</title>
<script>
    function refreshParent() {
        window.opener.location.reload();
    }
</script>
<div align="center">
    <h3>Record ID: <span class="label label-info"><?php echo @$_GET['id']; ?></span></h3><br />
    <?php $temp_check = $obj->read_specific('is_temp', $_GET['table'], 'id=' . $_GET['id']);
    if ($temp_check['is_temp'] == '0') { ?>
        <h3>This record is already confirmed.<br>No need to confirm again.<br>Thank you!</h3>
        <input class="btn btn-warning" type="button" value="Close Window" onclick="window.close();" />
    <?php } else { ?>
        <form action="" method="post">
            <h4>Are you sure to <span class="text-success h3"><b>confirm</b></span> this record for processing ?</h4>
            <input type="submit" name="yes" value="Yes >>" class="btn btn-primary" />&nbsp;&nbsp;<input type="submit" name="no" value="No" class="btn btn-warning" />
        </form>
    <?php } ?>
</div>
<?php if (isset($_POST['yes'])) {
    $type = (isset($_GET['type']) && $_GET['type'] == 1) ? 1 : 0;
    $edit_id = $_GET['id'];
    $table = $_GET['table'];
    if ($type == 1) {
        $obj->update($table, array('is_temp' => 0), "id=" . $edit_id);
        if ($managment == 1) {
            echo '<script>alert("Record Confirmed Successfuly !");
                window.close();</script>';
        } else {
            echo '<script>alert("Record Confirmed Successfuly !");
                window.onunload = refreshParent;
                window.close();</script>';
        }
    } else {
        if ($table != 'translation') {
            $from_add = setupEmail::INFO_EMAIL;
            $from_password = setupEmail::INFO_PASSWORD;
        } else {
            $from_add = setupEmail::TRANSLATION_EMAIL;
            $from_password = setupEmail::TRANSLATION_PASSWORD;
        }
        $blocked_for = array("interpreter" => 1, "telephone" => 1, "translation" => 2);
        $row = $obj->read_specific('*', $table, 'id=' . $edit_id);
        //Email notification to related interpreters
        $is_temp = $row['is_temp'];
        $submited = $row['submited'];
        $check_role = $obj->read_specific('login.prv', "login", 'name="' . $submited.'"')['prv'];
        if ($is_temp == 1) {
            $obj->update($table, array('is_temp' => 0), "id=" . $edit_id);
            if ($table != 'interpreter_reg' && $table != 'comp_reg') {
                $orgName = $row['orgName'];
                $jobDisp = $row['jobDisp'];
                // $gender = $table == "translation" ? "No Preference" : $row['gender'];
                $gender = (empty($row['gender'])?"No Preference":$row['gender']);
                $jobStatus = $row['jobStatus'];
                $orgContact = $row['orgContact'];
                $orgRef = $row['orgRef'];
                if ($table == 'interpreter') {
                    $label = "F2F";
                } else if ($table == 'interpreter') {
                    $label = "TP";
                } else {
                    $label = "TR";
                }
                $obj->insert("daily_logs", array("action_id" => 35, "user_id" => $_SESSION['userId'], "details" => $label . " Job ID: " . $edit_id));
                $is_temp = $obj->read_specific('is_temp', $_GET['table'], 'id=' . $_GET['id']);
                if ($jobDisp == '1' && $jobStatus == '1' && $is_temp['is_temp'] == '0' && $check_role!="Test") {
                    $source_lang = $row['source'];
                    $target_lang = $row['target'];
                    $assignDate = $table == "translation" ? $misc->dated($row['asignDate']) : $misc->dated($row['assignDate']);
                    if ($table == 'interpreter') {
                        $dbs_checked = $row['dbs_checked'];
                        $assignCity_name = explode(',', $row['assignCity']);
                        $assignCity = $assignCity_name[0];
                        $buildingName = $row['buildingName'];
                        $street = $row['street'];
                        $postCode = $row['postCode'];
                    }
                    if ($table != 'translation') {
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
                        $assignTime = $row['assignTime'];
                        $inchPerson = $row['inchPerson'];
                    }
                    $dbs_required = isset($dbs_checked) && !empty($dbs_checked) && $dbs_checked == 0 ? 'AND interpreter_reg.dbs_checked=0' : '';
                    $remrks = $row['remrks'] ?: '';
                    if ($table == 'interpreter') {
                        $job_type = "Face To Face";
                        $order_type = 1;
                        $row_format = $obj->read_specific("em_format", "email_format", "id=28");
                        $subject = "Bidding Invitation For Face To Face Project " . $edit_id;
                        $sub_title = "New Face To Face job of " . $source_lang . " language is available for you to bid.";
                        $chek_col = 'interp';
                        $write_interp_cat = $row['interp_cat'] == '12' ? $row['assignIssue'] : $obj->read_specific("ic_title", "interp_cat", "ic_id=" . $row['interp_cat'])['ic_title'];
                        $write_interp_type = $row['interp_cat'] == '12' ? '' : $obj->read_specific("GROUP_CONCAT(CONCAT(it_title)  SEPARATOR ' <b> & </b> ') as it_title", "interp_types", "it_id IN (" . $row['interp_type'] . ")")['it_title'];
                        if ($row['interp_cat'] == '12') {
                            $append_issue = "<tr><td style='border: 1px solid yellowgreen;padding:5px;'>Assignment Category</td><td style='border: 1px solid yellowgreen;padding:5px;'>Other</td></tr><tr><td style='border: 1px solid yellowgreen;padding:5px;'>Assignment Details</td><td style='border: 1px solid yellowgreen;padding:5px;'>" . $row['assignIssue'] . "</td></tr>";
                        } else {
                            $append_issue = "<tr><td style='border: 1px solid yellowgreen;padding:5px;'>Assignment Category</td><td style='border: 1px solid yellowgreen;padding:5px;'>" . $write_interp_cat . "</td></tr><tr><td style='border: 1px solid yellowgreen;padding:5px;'>Assignment Details</td><td style='border: 1px solid yellowgreen;padding:5px;'>" . $write_interp_type . "</td></tr>";
                        }
                        $append_table = "
                            <table>
                            <tr>
                            <td style='border: 1px solid yellowgreen;padding:5px;'>Source Language</td>
                            <td style='border: 1px solid yellowgreen;padding:5px;'>" . $source_lang . "</td>
                            </tr>
                            <tr>
                            <td style='border: 1px solid yellowgreen;padding:5px;'>Target Language</td>
                            <td style='border: 1px solid yellowgreen;padding:5px;'>" . $target_lang . "</td>
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
                            <td style='border: 1px solid yellowgreen;padding:5px;'>To be informed after successful allocation</td>
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
                    }
                    if ($table == 'telephone') {
                        $job_type = "Telephone";
                        $order_type = 2;
                        $row_format = $obj->read_specific("em_format", "email_format", "id=29");
                        $chek_col = 'telep';
                        $write_telep_cat = $row['telep_cat'] == '11' ? $row['assignIssue'] : $obj->read_specific("tpc_title", "telep_cat", "tpc_id=" . $row['telep_cat'])['tpc_title'];
                        $write_telep_type = $row['telep_cat'] == '11' ? '' : $obj->read_specific("GROUP_CONCAT(CONCAT(tpt_title)  SEPARATOR ' <b> & </b> ') as tpt_title", "telep_types", "tpt_id IN (" . $row['telep_type'] . ")")['tpt_title'];
                        if ($row['telep_cat'] == '11') {
                            $append_issue = "<tr><td style='border: 1px solid yellowgreen;padding:5px;'>Assignment Category</td><td style='border: 1px solid yellowgreen;padding:5px;'>Other</td></tr><tr><td style='border: 1px solid yellowgreen;padding:5px;'>Assignment Details</td><td style='border: 1px solid yellowgreen;padding:5px;'>" . $row['assignIssue'] . "</td></tr>";
                        } else {
                            $append_issue = "<tr><td style='border: 1px solid yellowgreen;padding:5px;'>Assignment Category</td><td style='border: 1px solid yellowgreen;padding:5px;'>" . $write_telep_cat . "</td></tr><tr><td style='border: 1px solid yellowgreen;padding:5px;'>Assignment Details</td><td style='border: 1px solid yellowgreen;padding:5px;'>" . $write_telep_type . "</td></tr>";
                        }
                        $write_comunic = $obj->read_specific("c_title", "comunic_types", "c_id=" . $row['comunic'])['c_title'];
                        $communication_type = empty($row['comunic']) || $row['comunic'] == 11 ? "Telephone interpreting" : $write_comunic;
                        $subject = "Bidding Invitation For " . $communication_type . " Project " . $edit_id;
                        $sub_title = "New " . $communication_type . " job of " . $source_lang . " language is available for you to bid.";
                        $append_table = "
                            <table>
                            <tr>
                            <td style='border: 1px solid yellowgreen;padding:5px;'>Communication Type</td>
                            <td style='border: 1px solid yellowgreen;padding:5px;'>" . $write_comunic . "</td>
                            </tr>
                            <tr>
                            <td style='border: 1px solid yellowgreen;padding:5px;'>Source Language</td>
                            <td style='border: 1px solid yellowgreen;padding:5px;'>" . $source_lang . "</td>
                            </tr>
                            <tr>
                            <td style='border: 1px solid yellowgreen;padding:5px;'>Target Language</td>
                            <td style='border: 1px solid yellowgreen;padding:5px;'>" . $target_lang . "</td>
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
                    }
                    if ($table == 'translation') {
                        // $row['noty']='';
                        $job_type = "Translation";
                        $order_type = 3;
                        $row_format = $obj->read_specific("em_format", "email_format", "id=27");
                        $subject = "Bidding Invitation For Translation Project " . $edit_id;
                        $sub_title = "New Translation job of " . $source_lang . " language is available for you to bid.";
                        $chek_col = 'trans';
                        $append_table = "
                            <table>
                            <tr>
                            <td style='border: 1px solid yellowgreen;padding:5px;'>Source Language</td>
                            <td style='border: 1px solid yellowgreen;padding:5px;'>" . $source_lang . "</td>
                            </tr>
                            <tr>
                            <td style='border: 1px solid yellowgreen;padding:5px;'>Target Language</td>
                            <td style='border: 1px solid yellowgreen;padding:5px;'>" . $target_lang . "</td>
                            </tr>
                            <tr>
                            <td style='border: 1px solid yellowgreen;padding:5px;'>Document Type</td>
                            <td style='border: 1px solid yellowgreen;padding:5px;'>" . $obj->read_specific("tc_title", "trans_cat", "tc_id=" . $row['docType'])['tc_title'] . "</td>
                            </tr>
                            <tr>
                            <td style='border: 1px solid yellowgreen;padding:5px;'>Translation Type(s)</td>
                            <td style='border: 1px solid yellowgreen;padding:5px;'>" . $obj->read_specific("GROUP_CONCAT(CONCAT(tt_title)  SEPARATOR ' <b> & </b> ') as tt_title", "trans_types", "tt_id IN (" . $row['trans_detail'] . ")")['tt_title'] . "</td>
                            </tr>
                            <tr>
                            <td style='border: 1px solid yellowgreen;padding:5px;'>Translation Category</td>
                            <td style='border: 1px solid yellowgreen;padding:5px;'>" . $obj->read_specific("GROUP_CONCAT(CONCAT(td_title)  SEPARATOR ' <b> & </b> ') as td_title", "trans_dropdown", "td_id IN (" . $row['transType'] . ")")['td_title'] . "</td>
                            </tr>
                            <tr>
                            <td style='border: 1px solid yellowgreen;padding:5px;'>Delivery Date</td>
                            <td style='border: 1px solid yellowgreen;padding:5px;'>" . $row['deliverDate2'] . "</td>
                            </tr>
                            <tr>
                            <td style='border: 1px solid yellowgreen;padding:5px;'>Delivery Type</td>
                            <td style='border: 1px solid yellowgreen;padding:5px;'>" . $row['deliveryType'] . "</td>
                            </tr>
                            </table>";
                        if (!empty($remrks)) {
                            $append_table .= "<br><u><b>NOTES FOR THIS JOB:</b></u><br>" . $remrks . "<br>";
                        }
                    }
                    if ($gender == '' || $gender == 'No Preference') {
                        $put_gender = "";
                    } else {
                        $put_gender = "AND interpreter_reg.gender='$gender'";
                    }
                    if ($source_lang == $target_lang) {
                        $put_lang = "";
                        $query_style = '0';
                    } else if ($source_lang != 'English' && $target_lang != 'English') {
                        $put_lang = "";
                        $query_style = '1';
                    } else if ($source_lang == 'English' && $target_lang != 'English') {
                        $put_lang = "interp_lang.lang='$target_lang' ";
                        $query_style = '2';
                    } else if ($source_lang != 'English' && $target_lang == 'English') {
                        $put_lang = "interp_lang.lang='$source_lang' ";
                        $query_style = '2';
                    } else {
                        $put_lang = "";
                        $query_style = '3';
                    }
                    //If job is booked to notify specific interpreters
                    $noty_adv = 0;
                    if (array_key_exists("noty", $row)) {
                        if (!empty($row['noty'])) {
                            $append_specific_interpreters .= ' and interpreter_reg.id IN (' . $row['noty'] . ') ';
                            $noty_adv = 1;
                        }
                    }

                    if ($query_style == '0') {
                        $query_emails = $obj->read_all("DISTINCT interpreter_reg.name, interpreter_reg.email, interpreter_reg.id", "interpreter_reg,interp_lang", "interpreter_reg.code=interp_lang.code AND (SELECT COUNT(DISTINCT interp_lang.lang) FROM interp_lang WHERE interp_lang.lang IN ('" . $source_lang . "')  and interp_lang.code=interpreter_reg.code AND interp_lang.type='$chek_col' " . ($noty_adv == 0 ? " and interp_lang.level<3 )=1 and 
                        interpreter_reg.active='0' AND (interpreter_reg.actnow='Active' OR (interpreter_reg.actnow='Inactive' AND CURRENT_DATE() NOT BETWEEN interpreter_reg.actnow_time AND interpreter_reg.actnow_to)) AND interpreter_reg.$chek_col='Yes' $dbs_required $put_gender AND interpreter_reg.deleted_flag=0 AND interpreter_reg.subscribe=1 AND interpreter_reg.is_temp=0 AND interpreter_reg.isAdhoc=0" : " )=1 $append_specific_interpreters "));
                    } else if ($query_style == '1') {
                        $query_emails = $obj->read_all("DISTINCT interpreter_reg.name, interpreter_reg.email, interpreter_reg.id", "interpreter_reg,interp_lang", "interpreter_reg.code=interp_lang.code AND (SELECT COUNT(DISTINCT interp_lang.lang) FROM interp_lang WHERE interp_lang.lang IN ('" . $source_lang . "','" . $target_lang . "')  and interp_lang.code=interpreter_reg.code AND interp_lang.type='$chek_col' " . ($noty_adv == 0 ? " and interp_lang.level<3 )=2 and 
                        interpreter_reg.active='0' AND (interpreter_reg.actnow='Active' OR (interpreter_reg.actnow='Inactive' AND CURRENT_DATE() NOT BETWEEN interpreter_reg.actnow_time AND interpreter_reg.actnow_to)) AND interpreter_reg.$chek_col='Yes' $dbs_required $put_gender AND interpreter_reg.deleted_flag=0 AND interpreter_reg.subscribe=1 AND interpreter_reg.is_temp=0 AND interpreter_reg.isAdhoc=0" : " )=2 $append_specific_interpreters "));
                    } else if ($query_style == '2') {
                        $query_emails = $obj->read_all("DISTINCT interpreter_reg.name, interpreter_reg.email, interpreter_reg.id", "interpreter_reg,interp_lang", "interpreter_reg.code=interp_lang.code AND $put_lang  AND interp_lang.type='$chek_col' " . ($noty_adv == 0 ? " and interp_lang.level<3 and 
                        interpreter_reg.active='0' AND (interpreter_reg.actnow='Active' OR (interpreter_reg.actnow='Inactive' AND CURRENT_DATE() NOT BETWEEN interpreter_reg.actnow_time AND interpreter_reg.actnow_to)) AND interpreter_reg.$chek_col='Yes' $dbs_required $put_gender AND interpreter_reg.deleted_flag=0 AND interpreter_reg.subscribe=1 AND interpreter_reg.is_temp=0 AND interpreter_reg.isAdhoc=0" : " $append_specific_interpreters "));
                    } else {
                        $query_emails = $obj->read_all("DISTINCT interpreter_reg.name, interpreter_reg.email, interpreter_reg.id", "interpreter_reg", " " . ($noty_adv == 0 ? " interpreter_reg.active='0' AND (interpreter_reg.actnow='Active' OR (interpreter_reg.actnow='Inactive' AND CURRENT_DATE() NOT BETWEEN interpreter_reg.actnow_time AND interpreter_reg.actnow_to)) AND 
                            interpreter_reg.$chek_col='Yes' $dbs_required $put_gender AND interpreter_reg.deleted_flag=0 AND interpreter_reg.subscribe=1 AND interpreter_reg.is_temp=0 AND interpreter_reg.isAdhoc=0" : " interpreter_reg.id IN (" . $row['noty'] . ") "));
                    }
                    $type_key = "nj";
                    while ($row_emails = $query_emails->fetch_assoc()) {
                        if ($obj->read_specific("COUNT(*) as blacklisted", "interp_blacklist", "interpName='id-" . $row_emails['id'] . "' AND orgName='" . $orgName . "' AND deleted_flag=0 AND blocked_for=" . $blocked_for[$table])["blacklisted"] == 0) {
                            $to_add = $row_emails['email'];
                            //Send notification on APP
                            $check_id = $obj->read_specific('id', 'notify_new_doc', 'interpreter_id=' . $row_emails['id'])['id'];
                            if (empty($check_id)) {
                                $obj->insert('notify_new_doc', array("interpreter_id" => $row_emails['id'], "status" => '1'));
                            } else {
                                $existing_notification = $obj->read_specific("new_notification", "notify_new_doc", "interpreter_id=" . $row_emails['id'])['new_notification'];
                                $obj->update('notify_new_doc', array("new_notification" => $existing_notification + 1), "interpreter_id=" . $row_emails['id']);
                            }
                            $array_tokens = explode(',', $obj->read_specific("GROUP_CONCAT( DISTINCT token) as tokens", "int_tokens", "int_id=" . $row_emails['id']." ORDER BY id DESC")['tokens']);
                            if (!empty($array_tokens)) {
                                $obj->insert('app_notifications', array("title" => $subject, "sub_title" => $sub_title, "dated" => date('Y-m-d'), "int_ids" => $row_emails['id'], "read_ids" => $row_emails['id'], "type_key" => $type_key));
                                // foreach ($array_tokens as $token) {
                                //     if (!empty($token)) {
                                //         try {
                                //             $obj->notify($token, $subject, $sub_title, array("type_key" => $type_key, "job_type" => $job_type));
                                //         } catch (Exception $ex) {
                                //             continue;
                                //         }
                                //     }
                                // }
                                $obj->notify($array_tokens[0], $subject, $sub_title, array("type_key" => $type_key, "job_type" => $job_type));
                            }
                            if ($table == "interpreter") {
                                $data   = ["[NAME]", "[ASSIGNTIME]", "[ASSIGNDATE]", "[POSTCODE]", "[TABLE]", "[EDIT_ID]"];
                                $to_replace  = [$row_emails['name'], "$assignTime", "$assignDate", "$postCode", "$append_table", "$edit_id"];
                                $message = str_replace($data, $to_replace, $row_format['em_format']);
                            } else if ($table == "telephone") {
                                $data   = ["[NAME]", "[ASSIGNTIME]", "[ASSIGNDATE]", "[TABLE]", "[EDIT_ID]"];
                                $to_replace  = [$row_emails['name'], "$assignTime", "$assignDate", "$append_table", "$edit_id"];
                                $message = str_replace($data, $to_replace, $row_format['em_format']);
                            } else {
                                $data   = ["[NAME]", "[ASSIGNDATE]", "[TABLE]", "[EDIT_ID]"];
                                $to_replace  = [$row_emails['name'], "$assignDate", "$append_table", "$edit_id"];
                                $message = str_replace($data, $to_replace, $row_format['em_format']);
                            }
                            try {
                                $obj->insert(
                                    'cron_emails',
                                    array(
                                        "order_id" => $edit_id,
                                        "order_type" => $order_type,
                                        "user_id" => $row_emails['id'],
                                        "send_from" => $from_add,
                                        "send_password" => $from_password,
                                        "send_to" => $to_add,
                                        "subject" => $subject,
                                        "template_type" => 4,
                                        "template_body" => $obj->con->real_escape_string($message),
                                        "created_date" => date("Y-m-d H:i:s")
                                    )
                                );
                                // $mail->SMTPDebug = 0;
                                // $mail->isSMTP();
                                // $mail->Host = setupEmail::EMAIL_HOST;
                                // $mail->SMTPAuth   = true;
                                // $mail->Username   = $from_add;
                                // $mail->Password   = $from_password;
                                // $mail->SMTPSecure = setupEmail::SECURE_TYPE;
                                // $mail->Port       = setupEmail::SENDING_PORT;
                                // $mail->setFrom($from_add, setupEmail::FROM_NAME);
                                // $mail->addAddress($to_add);
                                // $mail->addReplyTo($from_add, setupEmail::FROM_NAME);
                                // $mail->isHTML(true);
                                // $mail->Subject = $subject;
                                // $mail->Body    = $message;
                                // $mail->send();
                                // $mail->ClearAllRecipients();
                            } catch (Exception $e) { ?>
                                <script>
                                    alert("Message could not be sent! Mailer library error.");
                                </script>
<?php   }
                        }
                    }
                }
                if ($managment == 1) {
                    echo '<script>alert("Record Confirmed Successfuly !");
                        window.close();</script>';
                } else {
                    echo '<script>alert("Record Confirmed Successfuly !");
                        window.onunload = refreshParent;
                        window.close();</script>';
                }
            } else {
                if ($managment == 1) {
                    echo '<script>alert("Record Confirmed Successfuly !");
                        window.close();</script>';
                } else {
                    echo '<script>alert("Record Confirmed Successfuly !");
                        window.onunload = refreshParent;
                        window.close();</script>';
                }
            }
        } else {
            echo "<script>alert('This record is already confirmed. No need to confirm again.');window.close();</script>";
        }
    }
}
if (isset($_POST['no'])) {
    echo "<script>window.close();</script>";
} ?>