<?php if (session_id() == '' || !isset($_SESSION)) {
    session_start();
}

include '../source/setup_email.php';
include 'db.php';
include 'class.php';
$allowed_type_idz = "122";
$notificationStatus = [
    'client' => ['email' => 'N/A', 'mobile' => 'N/A'],
    'interpreter' => ['email' => 'N/A', 'mobile' => 'N/A'],
];
//Check if user has current action allowed
if ($_SESSION['is_root'] == 0) {
    $get_page_access = $acttObj->read_specific("id", "action_permissions", "user_id=" . $_SESSION['userId'] . " AND action_id IN (" . $allowed_type_idz . ")")['id'];
    if (empty($get_page_access)) {
        die("<center><h2 class='text-center text-danger'>You do not have access to <u>Cancel Order</u> action for jobs!<br>Kindly contact admin for further process.</h2></center>");
    }
}

$array_types = array("interpreter" => "F2F", "telephone" => "TP", "translation" => "TR");
$array_order_types = array("interpreter" => 1, "telephone" => 2, "translation" => 3);
$lang = $_GET['lang'];
$table = $_GET['table'];
$email_id = $_GET['job_id'];
$cancelled_at = date('Y-m-d', strtotime($_REQUEST['cancelled_at']));
$cancelled_time = date('H:i:s', strtotime($_REQUEST['cancelled_at']));
$get_job_data = $acttObj->read_specific("source,intrpName", "$table", "id=" . $email_id);
$chk_booked = $get_job_data['intrpName'];
if (empty($chk_booked)) {
    $row = $acttObj->read_specific("$table.*,comp_reg.name as orgzName", "$table,comp_reg", "$table.orgName=comp_reg.abrv AND $table.id=" . $email_id);
} else {
    $row = $acttObj->read_specific("$table.*,interpreter_reg.name,interpreter_reg.email,interpreter_reg.rph,interpreter_reg.rpm,interpreter_reg.rpu,interpreter_reg.contactNo,interpreter_reg.country as interpreter_country,comp_reg.name as orgzName", "$table,interpreter_reg,comp_reg", "$table.intrpName=interpreter_reg.id AND $table.orgName=comp_reg.abrv AND $table.id=" . $email_id);
}
$submited = '';
$submited = $row['submited'];
$check_role = $acttObj->read_specific('login.prv', "login", 'name="' . $submited . '"')['prv'];
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
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
    <title>Booking Cancellation</title>
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
            <div class="row">
                <input type="hidden" required name="cancelled_language" id="cancelled_language" value="<?= $get_job_data['source']; ?>">
                <center>
                    <h4><b class="text-success"><?php echo $_GET['orgName']; ?></b> Job Cancellation ID : <span class="label label-danger"><?php echo @$_GET['job_id']; ?></span></h4>
                </center>
                <div class="form-group col-xs-3">
                    <label for="">Assignment Date</label>
                    <input type="date" readonly required name="assign_date" id="assign_date" value="<?= $assign_date; ?>" class="form-control">
                </div>
                <div class="form-group col-xs-3">
                    <label for="">Assignment Time</label>
                    <input type="time" readonly required name="assign_time" id="assign_time" value="<?= $assign_time; ?>" class="form-control">
                </div>
                <div class="form-group col-xs-6">
                    <label for="">Requested Date-time for cancellation</label>
                    <input onchange="reset_filters()" type="datetime-local" required name="cancelled_at" id="cancelled_at" class="form-control">
                </div>
                <div class="form-group col-xs-3" id="div_append">
                    <label>Job Cancelled By </label>
                    <select id="order_cancelledby" name="order_cancelledby" onchange="get_cd(this);" required class="form-control">
                        <option value="" disabled selected>Select From List</option>
                        <option value="cl">CLIENT</option>
                        <option value="ls">LSUK</option>
                    </select>
                </div>
                <div class="form-group col-xs-7 hidden" id="div_order_cancel_remarks">
                    <textarea name="order_cancel_remarks" id="order_cancel_remarks" rows="3" class="form-control hidden" placeholder="Write reason here ..."></textarea>
                </div>
                <div class="col-xs-12 div_cancellation_status hidden">
                    <div class="bg-danger" style="padding: 4px;">
                        <h4 class="text-center">Confirm Interpreter Payment & Client chargeable Status</h4>
                    </div>
                    <div style="margin-top: 5px;" class="form-group col-xs-6 hidden" id="div_payable">
                        <label class="checkbox-inline">
                            <input type="checkbox" id="pay_int" name="pay_int" value="1" data-toggle="toggle" data-on="Yes" data-off="No" checked>
                            <b>Do you want to pay to Interpreter?</b>
                            
                        </label>
                    </div>
                    <div style="margin-top: 5px;" class="form-group col-xs-6 hidden" id="div_chargable_interp">
                        <label class="checkbox-inline">
                            <input type="checkbox" id="charg_int" name="charg_int" value="1" data-toggle="toggle" data-on="Yes" data-off="No">
                            <b>Do you want to charge/penalize the interpreter?</b>
                            
                        </label>
                    </div>
                    <div style="margin-top: 5px;" class="form-group col-xs-6 hidden" id="div_charge_client">
                        <label class="checkbox-inline">
                            <input type="checkbox" id="charge_client" name="charge_client" value="1" data-toggle="toggle" data-on="Yes" data-off="No"> <b>Do you want to charge the Client?</b>
                        </label>
                    </div>
                </div>
                <hr>
                <div class="col-xs-12 div_emails hidden">
                    <div class="bg-info" style="padding: 4px;">
                        <h4 class="text-center">Confirm Email Notifications for Interpreter & Client</h4>
                    </div>
                    <div style="margin-top: 5px;" class="form-group col-xs-6 div_email_options">
                        <label class="checkbox-inline">
                            <input type="checkbox" id="email_int" name="email_int" value="1" data-toggle="toggle" data-on="Yes" data-off="No" checked> <b>Do you want to notify interpreter?</b>
                        </label>
                    </div>
                    <div style="margin-top: 5px;" class="form-group col-xs-6 div_email_options">
                        <label class="checkbox-inline">
                            <input type="checkbox" id="email_cl" name="email_cl" value="1" data-toggle="toggle" data-on="Yes" data-off="No" checked> <b>Do you want to notify client?</b>
                        </label>
                    </div>
                </div>
                <center>
                    <div class="form-group col-xs-12 hidden" id="div_buttons">
                        <h4 class="text-danger">Are you sure you want to cancel this booking ?</h4>
                        <input type="submit" name="yes" id="yes" value="Yes" class="btn btn-primary" onclick="
                        if($('#pay_int').is(':checked') && $('#pay_int').val()){ 
                            return confirm('Are you sure to CANCEL and PAY to interpreter?');
                        }else if($('#charg_int').is(':checked') && $('#charg_int').val()){
                            return confirm('Are you sure to CANCEL and CHARGE the interpreter?');
                        }else{ 
                            if($('#charge_client').is(':checked') && $('#charge_client').val()){ 
                                return confirm('Are you sure to CANCEL and CHARGE the client?');
                            }else{
                                return confirm('Are you sure to cancel this job?');
                            }
                        }" />
                        <input type="submit" name="no" value="No" class="btn btn-warning" />
                    </div>
                </center>
            </div>
        </form>
    </div>
    <?php if (isset($_POST['yes'])) {
        $post_pay_int = $_POST['pay_int'];
        $charg_int = $_POST['charg_int'];
        $client_email = '0';
        $int_email = '0';
        $cn_t_id = $_POST['cn_t_id'];
        $charge_client = $_POST['charge_client'];
        $order_cancelledby = $_POST['order_cancelledby'] == 'ls' ? 'LSUK' : 'Client';
        $assignDur = $row['assignDur'];
        $rph = $row['rph'];
        $rpm = $row['rpm'];
        $rpu = $row['rpu'];
        $interp_charge_amount = 0;

        

        if ($table == 'translation') {
            $interp_charge_amount = $row['numberUnit'] * $rpu;
        }
        if ($table == 'telephone') {
            $interp_charge_amount = $assignDur * $rpm;
        }


        if ($table == 'interpreter') {
            if($assignDur<60){
                $assignDur=60;
            }
            $hours = $assignDur / 60;
            $interp_charge_amount = $hours * $rph;
            // if ($assignDur > 60) {
            //     $hours = $assignDur / 60;
            //     $interp_charge_amount = $hours * $rph;
            // } else {
            //     $interp_charge_amount = $assignDur * $rph;
            // }
        }

       
        $acttObj->editFun($table, $email_id, 'cn_t_id', $cn_t_id);
        $acttObj->editFun($table, $email_id, 'order_cancelledby', $order_cancelledby);
        $acttObj->editFun($table, $email_id, 'cn_date', $cn_date);
        $acttObj->editFun($table, $email_id, 'cn_time', $cancelled_time);
        
        if (isset($post_pay_int) && $post_pay_int == 1) {
            $acttObj->editFun($table, $email_id, 'pay_int', 1);
        } else {
            $acttObj->editFun($table, $email_id, 'pay_int', 0);
            //Disable bid in bidding for this interpreter
            $acttObj->update('bid', array('allocated' => 0), array('job' => $email_id, 'tabName' => $table));
        }
       
        $cn_r_id = $_POST['cn_r_id'];
        if ($cn_r_id == 15) {
            $order_cancel_remarks = $_POST['order_cancel_remarks'];
            $acttObj->editFun($table, $email_id, 'order_cancel_remarks', $order_cancel_remarks);
        } else {
            $acttObj->editFun($table, $email_id, 'cn_r_id', $cn_r_id);
        }
        $get_cancelation_drops = $acttObj->read_specific("cd_effect,cd_effect_interp,cancelled_hours", "cancellation_drops", "cd_id=" . $cn_t_id);
        $cd_effect = $get_cancelation_drops['cd_effect'];
        $cd_effect_interp = $get_cancelation_drops['cd_effect_interp'];
        $client_write_charge = '';
        $interp_write_charge = '';

        
        
        $write_cancel_type = str_replace("[DATE]", $cn_date, $acttObj->read_specific("cd_title", "cancellation_drops", "cd_id=" . $cn_t_id)['cd_title']);
        $write_cancel_remarks = $cn_r_id == 15 ? $order_cancel_remarks : $acttObj->read_specific("cr_title", "cancel_reasons", "cr_id=" . $cn_r_id)['cr_title'];

        $email = $row['email'];
        $email2 = $row['email2'];
        $source = $row['source'];
        $target = $row['target'];
        $orgRef = $row['orgRef'];
        $pay_int = $row['pay_int'];
       
        // setting header of emails
        if ($post_pay_int == 0 && $charg_int == 0) {
            $write_pay = '<table><tbody><tr><td style="border: 1px solid black;padding:5px;background: #ef0808ab;color: #000;"><b>This is an advance notice cancellation, you will not be paid for this job</b></td></tr></tbody></table>';
        } else if ($post_pay_int == 0 && $charg_int == 1) {
            $write_pay = '<table><tbody><tr><td style="border: 1px solid black;padding:5px;background: #ef0808ab;color: #000;"><b>This is a short notice cancellation, you will be charged for this job</b></td></tr></tbody></table>';
        } else if ($post_pay_int == 1 && $charg_int == 0) {
            $write_pay = '<table><tbody><tr><td style="border: 1px solid black;padding:5px;background: #3f8a27ab;color: #000;"><b>This is a short notice cancellation, you will be paid for this job</b></td></tr></tbody></table>';
        }

        // Insert cancellation record
        $acttObj->insert(
            "canceled_orders",
            array(
                "interpreter_id" => $row['intrpName'],
                "job_id" => $email_id,
                "job_type" => $array_order_types[$table],
                "cancel_type_id" => $cn_t_id,
                "cancel_reason_id" => $cn_r_id,
                "canceled_by" => ($order_cancelledby = $_POST['order_cancelledby'] == 'LSUK' ? 1 : 2),
                "canceled_date" => ($cancelled_at . " " . $cancelled_time),
                "canceled_reason" => $write_cancel_remarks,
                "notice_period" => $get_cancelation_drops['cancelled_hours'],
                "source_language" => $source,
                "target_language" => $target,
                "created_by" => $_SESSION['userId'],
                "created_date" => date("Y-m-d H:i:s")
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
            $days = '';
            $hours_diff = '';
            if ($source == "Sign Language (BSL)" || $source == "Sign Language") {
                if (isset($cancelled_at) && !empty($cancelled_at)) {
                    $cancelled_datetime = $cancelled_at . " " . $cancelled_time;
                    $assign_datetime = DateTime::createFromFormat('d-m-Y H:i', $assignDate . ' ' . $assignTime);

                    $cancelled_datetime = new DateTime($cancelled_datetime);

                    $interval = $cancelled_datetime->diff($assign_datetime);

                    if ($interval->d < 1) {
                        $hours_diff = $interval->h + ($interval->i / 60);
                        $hours_diff = floor($hours_diff);
                    } else {
                        $days = $interval->days;
                    }
                }


                if ($days <= 7) {
                    $interp_charge_amount = $interp_charge_amount;
                } else if ($days > 7 && $days <= 14) {
                    $interp_charge_amount = $interp_charge_amount / 2;
                } else {
                    $interp_charge_amount = 0;
                }
            }
            if (isset($charge_client) && $charge_client == 1) {

                $acttObj->editFun($table, $email_id, 'orderCancelatoin', 1);
                $client_write_charge = '<table><tbody><tr><td style="border: 1px solid black;padding:5px;background: #ef0808ab;color: #000;"><b>This is a short notice chargeable cancellation, Invoice to follow</b></td></tr></tbody></table>';
            } elseif (!isset($charge_client) && $charge_client == 0) {
                $acttObj->editFun($table, $email_id, 'order_cancel_flag', 1);
                $client_write_charge = '<table><tbody><tr><td style="border: 1px solid black;padding:5px;background: #3f8a27ab;color: #000;"><b>This is non-chargeable cancellation</b></td></tr></tbody></table>';
            }
            if (isset($cd_effect_interp)) {
                if ($cd_effect_interp == "pay") {
                    $acttObj->editFun($table, $email_id, 'orderCancelatoin', 1);
                    $interp_write_charge = '<table><tbody><tr><td style="border: 1px solid black;padding:5px;background: #3f8a27ab;color: #000;"><b>This is a short notice payable cancellation</b></td></tr></tbody></table>';
                } else if ($cd_effect_interp == "npay") {
                    // $acttObj->editFun($table, $email_id, 'order_cancel_flag', 1);
                    $interp_write_charge = '<table><tbody><tr><td style="border: 1px solid black;padding:5px;background: #3f8a27ab;color: #000;"><b>This is non-payable cancellation</b></td></tr></tbody></table>';
                } else if ($cd_effect_interp == "charg") {
                    if ($charg_int == 0) {
                        $interp_charge_amount = 0;
                    }
                    $acttObj->editFun($table, $email_id, 'orderCancelatoin', 1);
                    $interp_write_charge = '<table><tbody><tr><td style="border: 1px solid black;padding:5px;background: #ef0808ab;color: #000;"><b>This is a short notice  chargeable cancellation (' . $interp_charge_amount . ')</b></td></tr></tbody></table>';
                } else if ($cd_effect_interp == "ncharg") {
                    // $acttObj->editFun($table, $email_id, 'order_cancel_flag', 1);
                    $interp_write_charge = '<table><tbody><tr><td style="border: 1px solid black;padding:5px;background: #3f8a27ab;color: #000;"><b>This is non-chargeable cancellation</b></td></tr></tbody></table>';
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
        $inchEmail2 = $row['inchEmail2'];
        $name = $row['name'];
        $remrks = $row['remrks'] ?: 'Nil';
        $int_notes = "<br><u><b>NOTES FOR THIS JOB:</b></u><br>" . $remrks . '<br>-----------------------------------';
        //Update DB
        $acttObj->editFun($table, $email_id, 'order_cancled_bystaff', $_SESSION['UserName']);
        $acttObj->editFun($table, $email_id, 'edited_by', $_SESSION['UserName']);
        $acttObj->editFun($table, $email_id, 'edited_date', date("Y-m-d H:i:s"));
        $index_mapping = array(
            'Cancel Staff' => 'order_cancled_bystaff',
            'Normal Cancel' => 'order_cancel_flag',
            'Chargeable Cancel' => 'orderCancelatoin',
            'Cancel Remarks' => 'order_cancel_remarks',
            'Cancel By' => 'order_cancelledby',
            'Pay Interpreter' => 'pay_int',
            'Cancel Type ID' => 'cn_t_id',
            'Cancel Reason ID' => 'cn_r_id'
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
        $acttObj->log_changes(json_encode($old_values), json_encode($new_values), $email_id, $table, "update", $_SESSION['userId'], $_SESSION['UserName'], "cancel_order");

        //Below history function needs to be removed
        $acttObj->insert("daily_logs", array("action_id" => 6, "user_id" => $_SESSION['userId'], "details" => $array_types[$table] . " Job ID: " . $email_id));

        $interpreter_payable = isset($post_pay_int) && $post_pay_int == 1 ? "Interpreter payable" : "Interpreter non-payable";
        $client_chargeable = isset($charge_client) && $charge_client == 1 ? "Client chargeable" : "Client non-chargeable";
        $charge_interpre = isset($charg_int) && $charg_int == 1 ? "Interpreter chargeable" : "Interpreter non-chargeable";
        $interpreter_payable_text = '';
        if (isset($post_pay_int) && $post_pay_int == 1) {
            $interpreter_payable_text = "\nYou will be paid for this job\n";
        } elseif (isset($charg_int) && $charg_int == 1) {
            $interpreter_payable_text = "\nYou will be charged for this job\n";
        } else {
            $interpreter_payable_text = "\nYou will not be paid for this job\n";
        }
        $job_note = "Cancellation Type: " . $write_cancel_type . "<br>" . $interpreter_payable . "<br>" . $client_chargeable . "<br>Cancellation Note: " . $write_cancel_remarks;
        //$acttObj->insert('jobnotes', array('jobNote' => mysqli_escape_string($con, $job_note), 'tbl' => $table, 'time' => $misc->sys_datetime_db(), 'fid' => $email_id, 'submitted' => $_SESSION['UserName'], 'dated' => date('Y-m-d')));
        $interp_charge_amount = round($interp_charge_amount);
        //Send SMS for cancel job
        if ($check_role != "Test") {
            if (isset($_SESSION['userId'])) {
                $get_application = $acttObj->read_specific("*", "job_messages", "order_type=" . $array_order_types[$table] . " AND order_id=" . $email_id . " AND interpreter_id=" . $row['intrpName'] . " AND message_category=3");
                if (empty($get_application['id'])) {
                    //Adding config for SMS
                    include '../source/setup_sms.php';
                    $setupSMS = new setupSMS;
                    $interpreter_phone = $setupSMS->format_phone($row['contactNo'], $row['interpreter_country']);
                    $appendTime = $table != 'translation' ? " " . $row['assignTime'] : "";
                    $sms_label =  $table == 'interpreter' ? "F2F" : ucwords($table);
                    $message_body = "Your job has been cancelled" . $interpreter_payable_text . $sms_label . " Job ID:" . $email_id . "\nDate / Time:" . $assignDate . $appendTime . "\nIf reallocated it will appear on your App or portal";
                    $sms_response = $setupSMS->send_sms($interpreter_phone, $message_body);
                    $acttObj->insert("job_messages", array("order_id" => $email_id, "order_type" => $array_order_types[$table], "message_category" => 3, "interpreter_id" => $row['intrpName'], "created_by" => $_SESSION['userId'], "message_body" => $message_body, "sent_to" => $interpreter_phone));
                    $message_inserted_id = $acttObj->con->insert_id;
                    if ($message_inserted_id) {
                        if ($sms_response['status'] == 0) {
                            $acttObj->update("job_messages", array("status" => 0), "id=" . $message_inserted_id);
                        }
                    }
                }
            }
                // Saving Payslip
                if(isset($charg_int) && $charg_int == 1){
                    $data = array(
                        "interpreter_id" => $row['intrpName'],
                        "type_id" => 3,
                        "job_type" => $array_order_types[$table],
                        "loan_amount" => $interp_charge_amount,
                        "given_amount" => $interp_charge_amount,
                        "payable_date" => date('Y-m-01', strtotime($cancelled_at)),
                        "duration" => 1,
                        "percentage" => 100,
                        "reason" => $write_cancel_remarks,
                        "status" => 2,
                        "created_by" => $_SESSION['userId'],
                        "created_date" => date("Y-m-d H:i:s"),
                        "job_id" => $email_id
                    );
                    $done = $acttObj->insert("loan_requests", $data);
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
                    ";
                if (isset($charge_client) && $charge_client == 1) {
                    $append_table .= "
                            <tr>
                                <td style='border: 1px solid red;padding:5px;'<strong>>Charge Amount<strong/></td>
                                <td style='border: 1px solid red;padding:5px;'><strong>Will be shared through Invoice</strong></td>
                            </tr>
                        ";
                } elseif (isset($charg_int) && $charg_int == 1) {
                    $append_table .= "
                        <tr>
                            <td style='border: 1px solid red;padding:5px;'><strong>Penalty Amount<strong/></td>
                            <td style='border: 1px solid red;padding:5px;'><strong>" . $interp_charge_amount . "<strong/></td>
                        </tr>
                    ";
                }
                $append_table .= "
                    <tr>
                    <td style='border: 1px solid red;padding:5px;'>Delivery Type</td>
                    <td style='border: 1px solid red;padding:5px;'>" . $deliveryType . "</td>
                    </tr>
                </table>";
                if (!empty($inchEmail)) {
                    $to_add = $inchEmail;
                }else {
                    $to_add = $row['inchEmail2'];
                }
                $subject = "Cancellation of " . $assignment_type . " Project " . $email_id;
                $query_format = "SELECT em_format FROM email_format where id='9'";
                $result_format = mysqli_query($con, $query_format);
                $row_format = mysqli_fetch_array($result_format);
                $msg_body = $row_format['em_format'];
                $all_table = $client_write_charge . $append_table;
                $data   = ["[ORGCONTACT]", "[SOURCE]", "[ORGREF]", "[ASSIGNDATE]", "[CANCELLATION_TYPE]", "[CANCELLATION_REASON]", "[TABLE]"];
                $to_replace  = ["$orgContact", "$source", "$orgref", "$assignDate", "$write_cancel_type", "$write_cancel_remarks", "$all_table"];
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
                            $notificationStatus['client']['email'] = 'sent';
                            $mail->ClearAllRecipients();
                            if ($row['inchEmail2']) {
                                $mail->addAddress($row['inchEmail2']);
                                $mail->addReplyTo($from_add, setupEmail::FROM_NAME);
                                $mail->isHTML(true);
                                $mail->Subject = $subject;
                                $mail->Body    = $message;
                                $mail->send();
                                $notificationStatus['client']['email'] = 'sent to all';
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
                    } catch (Exception $e) { $notificationStatus['client']['email'] = $notificationStatus['client']['email'] === 'sent' ? 'sent' : 'Error'; ?>
                        <script>
                            alert("Mailer Library Error!");
                        </script>
                        <?php }
                }else{
                    $notificationStatus['client']['email'] = 'Not sent';
                }
                //...........................for interpreter.............................
                if (!empty($chk_booked)) {
                   
                    $subject = "Cancellation of " . $assignment_type . " Project " . $email_id;
                    $query_format = "SELECT em_format FROM email_format where id='10'";
                    $result_format = mysqli_query($con, $query_format);
                    $row_format = mysqli_fetch_array($result_format);
                    //Get format from database
                    $msg_body = $row_format['em_format'];
                    $all_table = $interp_write_charge . $append_table . $int_notes;
                    $data   = ["[NAME]", "[SOURCE]", "[ASSIGNDATE]", "[CANCELLATION_TYPE]", "[CANCELLATION_REASON]", "[TABLE]"];
                    $to_replace  = ["$name", "$source", "$assignDate", "$write_cancel_type", "$write_cancel_remarks", "$all_table"];
                    $message = str_replace($data, $to_replace, $msg_body);
                    if (isset($_POST['email_int'])) {
                        $interpreters_emails = [];
                        if (!empty($email)) {
                            array_push($interpreters_emails, $email);
                        }
                        if (!empty($email2)) {
                            array_push($interpreters_emails, $email2);
                        }
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
                            $mail->addReplyTo($from_add, setupEmail::FROM_NAME);
                            $mail->isHTML(true);
                            $mail->Subject = $subject;
                            $mail->Body    = $message;
                            foreach ($interpreters_emails as $interpreter_email) {
                                $mail->addAddress($interpreter_email);
                            }
                            if ($mail->send()) {
                                $notificationStatus['interpreter']['email'] = 'sent';
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
                        } catch (Exception $e) { $notificationStatus['interpreter']['email'] = 'Error'; ?>
                            <script>
                                alert('<?php echo "Mailer Library Error!"; ?>');
                            </script>
                        <?php }
                    }else{
                    $notificationStatus['interpreter']['email'] = 'Not sent';
                }
                }
                if ($client_email == '1' && (empty($chk_booked) && $int_email == '0') || (!empty($chk_booked) && $int_email == '1')) {
                    echo "<script>alert('Email successfully sent to client and interpreter.');</script>";
                } else {
                    echo "<script>alert('Email notifications were skipped for client OR interpreter!');</script>";
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
                $c_cat = $acttObj->read_specific("c_cat", "comunic_types", "c_id=" . $comunic)['c_cat'];
                $cat_typ = '';
                if ($c_cat = 'a') {
                    $cat_typ = 'Audio';
                } elseif ($c_cat = 'v') {
                    $cat_typ = "Video";
                }
                $append_table = "
                <table>
                    <tr>
                        <td style='border: 1px solid black;padding:5px;text-align:center;" . (isset($charge_client) && $charge_client == 1 ? " background: #f33d3d;color: white;" : " background: #f33d3d;color: white;") . "' colspan='2'><b>Cancelled Job Details</b></td>
                    </tr>
                    <tr>
                    <td style='border: 1px solid red;padding:5px;'>Communication Type</td>
                    <td style='border: 1px solid red;padding:5px;'>" . "Remote/" . $cat_typ . "/" . $communication_type . "</td>
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
                    ";
                if (isset($charge_client) && $charge_client == 1) {
                    $append_table .= "
                            <tr>
                                <td style='border: 1px solid red;padding:5px;'><strong>Charge Amount</strong></td>
                                <td style='border: 1px solid red;padding:5px;'><strong>Will be shared through Invoice</strong></td>
                            </tr>
                        ";
                }
                $append_table .= "
                    <tr>
                    <td style='border: 1px solid red;padding:5px;'>Report to</td>
                    <td style='border: 1px solid red;padding:5px;'>" . $inchPerson . "</td>
                    </tr>
                    <tr>
                    <td style='border: 1px solid red;padding:5px;'>Case Worker</td>
                    <td style='border: 1px solid red;padding:5px;'>" . $orgContact . "</td>
                    </tr>
                </table>";
                if (!empty($inchEmail)) {
                    $to_add = $inchEmail;
                } else {
                    $to_add = $inchEmail2;
                }
                $subject = "Cancellation of " . "Remote/" . $cat_typ . "/" . $communication_type . " Project " . $email_id;
                $query_format = "SELECT em_format FROM email_format where id='11'";
                $result_format = mysqli_query($con, $query_format);
                $row_format = mysqli_fetch_array($result_format);
                //Get format from database
                $msg_body = $row_format['em_format'];
                $all_table = $client_write_charge . $append_table;
                $data   = ["[ORGCONTACT]", "[SOURCE]", "[ORGREF]", "[ASSIGNDATE]", "[ASSIGNTIME]", "[CANCELLATION_TYPE]", "[CANCELLATION_REASON]", "[TABLE]"];
                $to_replace  = ["$orgContact", "$source", "$orgRef", "$assignDate", "$assignTime", "$write_cancel_type", "$write_cancel_remarks", "$all_table"];
                $message = str_replace($data, $to_replace, $msg_body);
                
                if (isset($_POST['email_cl'])) {
                    $client_emails = [];
                    if (!empty($inchEmail)) {
                        array_push($client_emails, $inchEmail);
                    }
                    if (!empty($inchEmail2)) {
                        array_push($client_emails, $inchEmail2);
                    }
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
                            $notificationStatus['client']['email'] = 'Sent';
                            $mail->ClearAllRecipients();
                            if ($row['inchEmail2']) {
                                $mail->addAddress($row['inchEmail2']);
                                $mail->addReplyTo($from_add, setupEmail::FROM_NAME);
                                $mail->isHTML(true);
                                $mail->Subject = $subject;
                                $mail->Body    = $message;
                                $mail->send();
                                $notificationStatus['client']['email'] = 'Sent to all';
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
                    } catch (Exception $e) { $notificationStatus['client']['email'] = $notificationStatus['client']['email'] === 'sent' ? 'sent' : 'Error'; ?>
                        <script>
                            alert('<?php echo "Mailer Library Error!"; ?>');
                        </script>
                        <?php }
                }else{
                    $notificationStatus['client']['email'] = 'Not sent';
                }
                //..............................for interpreter ............
                if (!empty($chk_booked)) {
                    $append_table_interp = "
                    <table>
                        <tr>
                            <td style='border: 1px solid black;padding:5px;text-align:center;" . (isset($charge_client) && $charge_client == 1 ? " background: #f33d3d;color: white;" : " background: #f33d3d;color: white;") . "' colspan='2'><b>Cancelled Job Details</b></td>
                        </tr>
                        <tr>
                        <td style='border: 1px solid red;padding:5px;'>Communication Type</td>
                        <td style='border: 1px solid red;padding:5px;'>" . "Remote/" . $cat_typ . "/" . $communication_type . "</td>
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
                        ";
                    if (isset($charg_int) && $charg_int == 1) {
                        $append_table_interp .= "
                                <tr>
                                    <td style='border: 1px solid red;padding:5px;'><strong>Penalty Amount</strong></td>
                                    <td style='border: 1px solid red;padding:5px;'><strong>" . $interp_charge_amount . "</strong></td>
                                </tr>
                            ";
                    } elseif ($cd_effect_interp == "charg" && $charg_int == 0) {
                        $append_table_interp .= "
                            <tr>
                                <td style='border: 1px solid red;padding:5px;'><strong>Penalty Amount</strong></td>
                                <td style='border: 1px solid red;padding:5px;'><strong>" . 0 . "</strong></td>
                            </tr>
                            ";
                    }
                    if (isset($post_pay_int) && $post_pay_int == 1) {
                        $append_table_interp .= "
                                <tr>
                                    <td style='border: 1px solid red;padding:5px;'><strong>Payable Amount</strong></td>
                                    <td style='border: 1px solid red;padding:5px;'><strong>" . $interp_charge_amount . "</strong></td>
                                </tr>
                            ";
                    }
                    $append_table_interp .= "
                        <tr>
                        <td style='border: 1px solid red;padding:5px;'>Report to</td>
                        <td style='border: 1px solid red;padding:5px;'>" . $inchPerson . "</td>
                        </tr>
                        <tr>
                        <td style='border: 1px solid red;padding:5px;'>Case Worker</td>
                        <td style='border: 1px solid red;padding:5px;'>" . $orgContact . "</td>
                        </tr>
                    </table>";
                    if (!empty($email)) {
                        $to_add = $email;
                    } else {
                        $to_add = $email2;
                    }

                    $subject = "Cancellation of " . $communication_type . " Project " . $email_id;
                    $query_format = "SELECT em_format FROM email_format where id='12'";
                    $result_format = mysqli_query($con, $query_format);
                    $row_format = mysqli_fetch_array($result_format);
                    //Get format from database
                    $msg_body = $row_format['em_format'];
                    $all_table = $interp_write_charge . $append_table_interp . $int_notes;
                    $data   = ["[NAME]", "[SOURCE]", "[ASSIGNDATE]", "[ASSIGNTIME]", "[CANCELLATION_TYPE]", "[CANCELLATION_REASON]", "[TABLE]"];
                    $to_replace  = ["$name", "$source", "$assignDate", "$assignTime", "$write_cancel_type", "$write_cancel_remarks", "$all_table"];
                    $message = str_replace($data, $to_replace, $msg_body);
                    if (isset($_POST['email_int'])) {
                        $interpreters_emails = [];
                        if (!empty($email)) {
                            array_push($interpreters_emails, $email);
                        }
                        if (!empty($email2)) {
                            array_push($interpreters_emails, $email2);
                        }

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
                            $mail->addReplyTo($from_add, setupEmail::FROM_NAME);
                            $mail->isHTML(true);
                            $mail->Subject = $subject;
                            $mail->Body    = $message;
                            foreach ($interpreters_emails as $interpreter_email) {
                                $mail->addAddress($interpreter_email);
                            }
                            if ($mail->send()) {
                                $notificationStatus['interpreter']['email'] = 'sent';
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
                        } catch (Exception $e) {  $notificationStatus['interpreter']['email'] = 'Error'; ?>
                            <script>
                                alert('<?php echo "Mailer Library Error!"; ?>');
                            </script>
                        <?php }
                    }else{
                    $notificationStatus['interpreter']['email'] = 'Not sent';
                }
                }
                if ($client_email == '1' && (empty($chk_booked) && $int_email == '0') || (!empty($chk_booked) && $int_email == '1')) {
                    echo "<script>alert('Email successfully sent to client and interpreter.');</script>";
                } else {
                    echo "<script>alert('Email notifications were skipped for client OR interpreter!');</script>";
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
                    ";
                if (isset($charge_client) && $charge_client == 1) {
                    $append_table .= "
                            <tr>
                                <td style='border: 1px solid red;padding:5px;'><strong>Charge Amount</strong></td>
                                <td style='border: 1px solid red;padding:5px;'><strong>Will be shared through Invoice</strong></td>
                            </tr>
                        ";
                }
                $append_table .= "
                    <td style='border: 1px solid red;padding:5px;'>Assignment Location</td>
                    <td style='border: 1px solid red;padding:5px;'>" . (!empty(trim($buildingName)) ? htmlspecialchars($buildingName, ENT_QUOTES, 'UTF-8') : '') . (!empty(trim($street)) ? (', ' . $street) : '') . (!empty(trim($assignCity)) ? (', ' . $assignCity) : '') . (!empty(trim($postCode)) ? (', ' . $postCode) : '') . "</td>
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
                if (!empty($inchEmail)) {
                    $to_add = $inchEmail;
                } else {
                    $to_add = $inchEmail2;
                }
                $subject = "Cancellation of " . $assignment_type . " Project " . $email_id;
                $query_format = "SELECT em_format FROM email_format where id='13'";
                $result_format = mysqli_query($con, $query_format);
                $row_format = mysqli_fetch_array($result_format);
                //Get format from database
                $msg_body = $row_format['em_format'];
                $all_table = $client_write_charge . $append_table;
                $data   = ["[ORGCONTACT]", "[SOURCE]", "[ORGREF]", "[ASSIGNDATE]", "[ASSIGNTIME]", "[BUILDINGNAME]", "[STREET]", "[ASSIGNCITY]", "[POSTCODE]", "[CANCELLATION_TYPE]", "[CANCELLATION_REASON]", "[TABLE]"];
                $to_replace  = ["$orgContact", "$source", "$orgRef", "$assignDate", "$assignTime", "$buildingName", "$street", "$assignCity", "$postCode", "$write_cancel_type", "$write_cancel_remarks", "$all_table"];
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
                            $notificationStatus['client']['email'] = 'Sent';
                            $mail->ClearAllRecipients();
                            if ($row['inchEmail2']) {
                                $mail->addAddress($row['inchEmail2']);
                                $mail->addReplyTo($from_add, setupEmail::FROM_NAME);
                                $mail->isHTML(true);
                                $mail->Subject = $subject;
                                $mail->Body    = $message;
                                $mail->send();
                                $notificationStatus['client']['email'] = 'Sent to all';
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
                    } catch (Exception $e) { $notificationStatus['client']['email'] = $notificationStatus['client']['email'] === 'sent' ? 'sent' : 'Error';  ?>
                        <script>
                            alert('<?php echo "Mailer Library Error!"; ?>');
                        </script>
                        <?php }
                }else{
                    $notificationStatus['client']['email'] = 'Not sent';
                }
                //..............................for interpreter ...........
                if (!empty($chk_booked)) {
                    $append_table_interp = "
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
                        ";
                    if (isset($charg_int) && $charg_int == 1) {
                        $append_table_interp .= "
                                <tr>
                                    <td style='border: 1px solid red;padding:5px;'><strong>Penalty Amount</strong></td>
                                    <td style='border: 1px solid red;padding:5px;'><strong>" . $interp_charge_amount . "</strong></td>
                                </tr>
                            ";
                    } elseif ($cd_effect_interp == "charg" && $charg_int == 0) {
                        $append_table_interp .= "
                            <tr>
                                <td style='border: 1px solid red;padding:5px;'><strong>Penalty Amount</strong></td>
                                <td style='border: 1px solid red;padding:5px;'><strong>" . 0 . "</strong></td>
                            </tr>
                            ";
                    }
                    if (isset($post_pay_int) && $post_pay_int == 1) {
                        $append_table_interp .= "
                                <tr>
                                    <td style='border: 1px solid red;padding:5px;'><strong>Payable Amount</strong></td>
                                    <td style='border: 1px solid red;padding:5px;'><strong>" . $interp_charge_amount . "</strong></td>
                                </tr>
                            ";
                    }
                    $append_table_interp .= "
                        <td style='border: 1px solid red;padding:5px;'>Assignment Location</td>
                        <td style='border: 1px solid red;padding:5px;'>" . (!empty(trim($buildingName)) ? htmlspecialchars($buildingName, ENT_QUOTES, 'UTF-8') : '') . (!empty(trim($street)) ? (', ' . $street) : '') . (!empty(trim($assignCity)) ? (', ' . $assignCity) : '') . (!empty(trim($postCode)) ? (', ' . $postCode) : '') . "</td>
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
                    if (!empty($email)) {
                        $to_add = $email;
                    } else {
                        $to_add = $email2;
                    }
                    $subject = "Cancellation of " . $assignment_type . " Project " . $email_id;
                    $query_format = "SELECT em_format FROM email_format where id='14'";
                    $result_format = mysqli_query($con, $query_format);
                    $row_format = mysqli_fetch_array($result_format);
                    //Get format from database
                    $msg_body = $row_format['em_format'];
                    $all_table = $interp_write_charge . $append_table_interp . $int_notes;
                    $data   = ["[NAME]", "[SOURCE]", "[ASSIGNDATE]", "[ASSIGNTIME]", "[BUILDINGNAME]", "[STREET]", "[ASSIGNCITY]", "[POSTCODE]", "[CANCELLATION_TYPE]", "[CANCELLATION_REASON]", "[TABLE]"];
                    $to_replace  = ["$name", "$source", "$assignDate", "$assignTime", "$buildingName", "$street", "$assignCity", "$postCode", "$write_cancel_type", "$write_cancel_remarks", "$all_table"];
                    $message = str_replace($data, $to_replace, $msg_body);
                    if (isset($_POST['email_int'])) {
                        $interpreters_emails = [];
                        if (!empty($email)) {
                            array_push($interpreters_emails, $email);
                        }
                        if (!empty($email2)) {
                            array_push($interpreters_emails, $email2);
                        }
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
                            // $mail->addAddress($to_add);
                            $mail->addReplyTo($from_add, setupEmail::FROM_NAME);
                            $mail->isHTML(true);
                            $mail->Subject = $subject;
                            $mail->Body    = $message;
                            foreach ($interpreters_emails as $interpreter_email) {
                                $mail->addAddress($interpreter_email);
                            }
                            if ($mail->send()) {
                                $notificationStatus['interpreter']['email'] = 'sent';
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
                        } catch (Exception $e) { $notificationStatus['interpreter']['email'] = 'Error';?>
                            <script>
                                alert('<?php echo "Mailer Library Error!"; ?>');
                            </script>
        <?php   }
                    }else{
                    $notificationStatus['interpreter']['email'] = 'Not sent';
                }
                }

                if ($client_email == '1' && (empty($chk_booked) && $int_email == '0') || (!empty($chk_booked) && $int_email == '1')) {
                    echo "<script>alert('Email successfully sent to client and interpreter.');</script>";
                } else {
                    echo "<script>alert('Email notifications were skipped for client OR interpreter!');</script>";
                }
            }

            //Send notification on cancellation
            if (!empty($chk_booked)) {
                $title = "Your job has been cancelled !";
                $sub_title = $source . " " . $assignment_type . " assignment at " . $row['assignDate'] . " is now cancelled by LSUK.";
                $type_key = "jc";
                //Send notification on APP
                $check_id = $acttObj->read_specific('id', 'notify_new_doc', 'interpreter_id=' . $chk_booked)['id'];
                if (empty($check_id)) {
                    $acttObj->insert('notify_new_doc', array("interpreter_id" => $chk_booked, "status" => '1'));
                    $notificationStatus['interpreter']['mobile'] = 'sent';
                } else {
                    $existing_notification = $acttObj->read_specific("new_notification", "notify_new_doc", "interpreter_id=" . $chk_booked)['new_notification'];
                    $acttObj->update('notify_new_doc', array("new_notification" => $existing_notification + 1), array("interpreter_id" => $chk_booked));
                    $notificationStatus['interpreter']['mobile'] = 'sent';
                }
                $array_tokens = explode(',', $acttObj->read_specific("GROUP_CONCAT( DISTINCT token) as tokens", "int_tokens", "int_id=" . $chk_booked)['tokens']);
                if (!empty($array_tokens)) {
                    $acttObj->insert('app_notifications', array("title" => $title, "sub_title" => $sub_title, "dated" => date('Y-m-d'), "int_ids" => $chk_booked, "read_ids" => $chk_booked, "type_key" => $type_key));
                    foreach ($array_tokens as $token) {
                        if (!empty($token)) {
                            $full_data = "{ \"notification\": {    \"title\": \"$title\",     \"text\": \"$sub_title\"   }, \"data\": { \"click_action\": \"FLUTTER_NOTIFICATION_CLICK\",\"status\": \"done\" },    \"to\" : \"$token\"}";
                            $acttObj->notification($token, $title, $sub_title, $full_data);
                        }
                    }
                }
            }
        }
        ?>

    <?php }
    if (isset($_POST['no'])) {
        echo "<script>window.close();</script>";
    } ?>
    <?php 
    // here we will update the job note
    if(isset($_POST['yes'])){
        function styleCell($value) {
            return $value === 'Error' ? '<span style="color:red;">' . $value . '</span>' : $value;
        }

        $job_note .= '<table class="table" style="background:transparent">
            <tr><th>Type</th><th>Email</th><th>Mobile</th></tr>
            <tr>
                <td>Client</td>
                <td>' . styleCell($notificationStatus['client']['email']) . '</td>
                <td>' . styleCell($notificationStatus['client']['mobile']) . '</td>
            </tr>
            <tr>
                <td>Interpreter</td>
                <td>' . styleCell($notificationStatus['interpreter']['email']) . '</td>
                <td>' . styleCell($notificationStatus['interpreter']['mobile']) . '</td>
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
    ?>
    <script type="text/javascript">
        function get_cd(element) {
            var cd_for = $(element).val();
            var cancel = 'yes';
            var lang = $('#cancelled_language').val();
            var cancelled_at = $('#cancelled_at').val();
            var assign_date = $('#assign_date').val();
            var assign_time = $('#assign_time').val();
            $('#div_cd,#div_reason,#div_cancel_details').remove();
            $.ajax({
                url: 'ajax_add_interp_data.php',
                method: 'post',
                dataType: 'json',
                data: {
                    cancelled_at: cancelled_at,
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
                    if (response['charg_interp'] == "charg") {
                        $('#charg_int').bootstrapToggle('on');
                    } else {
                        $('#charg_int').bootstrapToggle('off');
                    }
                },
                error: function(xhr) {
                    alert("An error occured: " + xhr.status + " " + xhr.statusText);
                }
            });
        }

        function reset_filters() {
            $("#order_cancelledby option[value='']").prop('selected', true);
            $('#div_cd,#div_cancel_details,#div_reason,#div_order_cancel_remarks,.div_cancellation_status,.div_emails,#div_payable,#div_chargable_interp,#div_charge_client,#div_buttons').addClass('hidden');
        }

        function get_buttons(element) {

            $('#div_buttons,.div_cancellation_status,.div_emails,#div_payable,#div_charge_client').removeClass('hidden');
            if ($(element).val() == 1 || $(element).val() == 2 || $(element).val() == 3 || $(element).val() == 14) {
                $('#div_chargable_interp').removeClass('hidden');
            }
            if ($(element).val() == 15) {
                $('#div_order_cancel_remarks,#order_cancel_remarks').removeClass('hidden');
            } else {
                $('#div_order_cancel_remarks,#order_cancel_remarks,div_chargable_interp').addClass('hidden');
            }
        }
    </script>
    <script>
        $(document).ready(function() {
            $('#pay_int').bootstrapToggle();
            $('#charg_int').bootstrapToggle();
            $('#charg_int').on('change', function() {
                if ($(this).prop('checked')) {
                    $('#pay_int').bootstrapToggle('off');
                }
            });
            $('#pay_int').on('change', function() {
                if ($(this).prop('checked')) {
                    $('#charg_int').bootstrapToggle('off');
                }
            });
        });
    </script>