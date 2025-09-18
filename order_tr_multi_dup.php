<?php
//php mailer library
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require 'lsuk_system/phpmailer/vendor/autoload.php';
$mail = new PHPMailer(true);
if (session_id() == '' || !isset($_SESSION)) {
    session_start();
}
if (empty($_SESSION['cust_UserName'])) {
    echo '<script>window.location="index.php";</script>';
}
include 'source/db.php';
include 'source/class.php';
$id = base64_decode($_GET['id']);
$table = 'translation';
$row = $acttObj->read_specific("*", "$table", "id=" . $id);
$query_get_info = $acttObj->read_specific("id,name", "comp_reg", "abrv='" . $row['orgName'] . "'");
$orgName = $row['orgName'];
$name = $query_get_info['name'];
$row_selected = $acttObj->read_specific("name,contactNo1,contactPerson,email,city,buildingName,streetRoad,postCode,line1,line2", "comp_reg", "status <> 'Company Seized trading in' and status <> 'Company Blacklisted' and abrv='$orgName' limit 1");
?>
<!DOCTYPE HTML>
<html class="no-js">

<head>
    <?php include 'source/header.php'; ?>
    <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" />
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php if ((strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== FALSE) || (strpos($_SERVER['HTTP_USER_AGENT'], 'Trident') !== FALSE)) { ?>
        <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
        <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
        <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
        <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/timepicker/1.3.5/jquery.timepicker.min.css">
        <script src="//cdnjs.cloudflare.com/ajax/libs/timepicker/1.3.5/jquery.timepicker.min.js"></script>
        <script>
            $(function() {
                $(".date_picker").datepicker({
                    dateFormat: 'yy-mm-dd'
                });
                $(".time_picker").timepicker({
                    timeFormat: 'HH:mm',
                    interval: 5,
                    defaultTime: '08',
                    dropdown: true,
                    scrollbar: true
                });
                $(".time_picker2").timepicker({
                    timeFormat: 'HH:mm',
                    interval: 1,
                    defaultTime: '08',
                    dropdown: true,
                    scrollbar: true
                });
            });
        </script>
    <?php } else { ?>
        <script src="lsuk_system/js/jquery-1.11.3.min.js"></script>
    <?php } ?>
    <script type="text/javascript" src="lsuk_system/js/debug.js"></script>
    <style>
        .ri {
            margin-top: 7px;
        }

        .ri .label {
            font-size: 100%;
            padding: .5em 0.6em 0.5em;
        }

        .checkbox-inline+.checkbox-inline,
        .radio-inline+.radio-inline {
            margin-top: 4px;
        }

        .multiselect {
            min-width: 295px;
        }

        .multiselect-container {
            max-height: 400px;
            overflow-y: auto;
            max-width: 380px;
        }

        .multiselect-native-select {
            display: block;
        }

        .multiselect-container li.active label.checkbox {
            color: white;
        }

        .sky-form select {
            -webkit-appearance: auto !important;
        }

        /* Formatting search box */
        .search-box {
            position: relative;
            display: inline-block;
            font-size: 14px;
        }

        .search-box input[type="text"] {
            height: 32px;
            padding: 5px 10px;
            font-size: 14px;
        }

        .result {
            position: absolute;
            z-index: 1000;
            top: 100%;
            width: 90% !important;
            background: white;
            max-height: 246px;
            overflow-y: auto;
        }

        .search-box input[type="text"],
        .result {
            width: 100%;
            box-sizing: border-box;
        }

        /* Formatting result items */
        .result p {
            margin: 0;
            padding: 7px 10px;
            border: 1px solid #CCCCCC;
            border-top: none;
            cursor: pointer;
        }

        .result p:hover {
            background: #f2f2f2;
        }

        .stepwizard-step p {
            margin-top: 0px;
            color: #666;
        }

        .stepwizard-row {
            display: table-row;
        }

        .stepwizard {
            display: table;
            width: 100%;
            position: relative;
        }

        .stepwizard-step button[disabled] {
            /*opacity: 1 !important;
    filter: alpha(opacity=100) !important;*/
        }

        .stepwizard .btn.disabled,
        .stepwizard .btn[disabled],
        .stepwizard fieldset[disabled] .btn {
            opacity: 1 !important;
            color: #bbb;
        }

        .stepwizard-row:before {
            top: 14px;
            bottom: 0;
            position: absolute;
            content: " ";
            width: 100%;
            height: 1px;
            background-color: #ccc;
            z-index: 0;
        }

        .stepwizard-step {
            display: table-cell;
            text-align: center;
            position: relative;
        }

        .btn-circle {
            width: 30px;
            height: 30px;
            text-align: center;
            padding: 6px 0;
            font-size: 12px;
            line-height: 1.428571429;
            border-radius: 15px;
        }
    </style>
    <?php  //submission starts here
    //.........................................captcha........................................//
    $msg_booking = '';
    $msg_error = '';
    // if (isset($_POST['g-recaptcha-response']) && $_POST['g-recaptcha-response']) {
    //     $secret = '6LextRoUAAAAAPvBF31eiYCmVP7Ne8a6mSez83zl';
    //     $ip = $_SERVER['REMOTE_ADDR'];
    //     $captcha = $_POST['g-recaptcha-response'];
    //     $rsp = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=$secret&response=$captcha&remoteip=$ip");
    //     $arr = json_decode($rsp, true);
    //     if ($arr['success']) {
    //         $captcha_flag = 1;
    //     } else {
    //         $msg_error = '<p style="font-size: 22px;" class="text-danger">Failed to validate captcha! Please try again <p>';
    //     }
    // } else if (SafeVar::IsLocal() == true) {
    //     $captcha_flag = 1;
    // }
    $captcha_flag = 1;
    //if form is submitted AND recaptcha OK
    if (isset($_POST['submit']) && isset($captcha_flag) && $captcha_flag == 1) {
        $array_languages = explode(',', $_POST['array_languages']);
        $from_add = 'translationservice@lsuk.org';
        $subject = 'Acknowledgment of your booking request'; //"Order for Interpreter (TR)";
        $count = 0;
        foreach ($array_languages as $language) {
            $count++;
            $get_language = explode(':', $language);
            $source = $get_language[0];
            $assignDate = $_POST['assignDate'];
            $edit_id = $acttObj->get_id($table);
            //Create & save new reference no
            $reference_no = $acttObj->generate_reference(3, $table, $edit_id);
            $acttObj->editFun($table, $edit_id, 'source', $source);
            //Assign it to an operator randomly, check if an operator already has same job today
            $get_same_job_user = $acttObj->read_specific(
                "assigned_jobs_users.user_id",
                "assigned_jobs_users,interpreter",
                "assigned_jobs_users.order_id = interpreter.id 
            AND assigned_jobs_users.order_type=1 
            AND interpreter.source='" . $source . "' 
            AND interpreter.order_company_id='" . $query_get_info['id'] . "' 
            AND interpreter.dated='" . date('Y-m-d') . "' 
            ORDER BY assigned_jobs_users.id DESC
            LIMIT 1"
            )['user_id'];
            if (!empty($get_same_job_user)) {
                $acttObj->insert("assigned_jobs_users", array("user_id" => $get_same_job_user, "order_id" => $edit_id, "order_type" => 1, "assigned_by" => 1, "assigned_date" => date('Y-m-d H:i:s'), "created_date" => date('Y-m-d H:i:s')));
            } else {
                //Pick random operator with low jobs
                $get_random_job_user = $acttObj->read_specific(
                    "login.id",
                    "login LEFT JOIN assigned_jobs_users ON login.id=assigned_jobs_users.user_id 
                LEFT JOIN users_timings ON login.id=users_timings.user_id",
                    "login.prv='Operator' AND login.user_status=1 AND login.is_allocation_member=1 
                AND (assigned_jobs_users.order_type = 1 OR assigned_jobs_users.order_type IS NULL)
                AND users_timings." . strtolower(date('l')) . " = 1 AND '" . date('H:i:s') . "' BETWEEN users_timings." . strtolower(date('l')) . "_time AND users_timings." . strtolower(date('l')) . "_to
                GROUP BY login.id
                ORDER BY COUNT(assigned_jobs_users.order_id) ASC, RAND() LIMIT 1"
                )['id'];
                if (!empty($get_random_job_user)) {
                    $acttObj->insert("assigned_jobs_users", array("user_id" => $get_random_job_user, "order_id" => $edit_id, "order_type" => 1, "assigned_by" => 1, "assigned_date" => date('Y-m-d H:i:s'), "created_date" => date('Y-m-d H:i:s')));
                }
            }
            //Assign to operator ends
            $target = $get_language[1];
            $acttObj->editFun($table, $edit_id, 'target', $target);
            $docType = $_POST['docType'];
            $acttObj->editFun($table, $edit_id, 'docType', $docType);
            $transType = implode(",", $_POST['transType']);
            $acttObj->editFun($table, $edit_id, 'transType', $transType);
            $trans_detail = implode(",", $_POST['trans_detail']);
            $acttObj->editFun($table, $edit_id, 'trans_detail', $trans_detail);
            $acttObj->editFun($table, $edit_id, 'asignDate', $assignDate);
            $bookedDate = $_POST['bookeddate'];
            $acttObj->editFun($table, $edit_id, 'bookeddate', $bookedDate);
            $bookedTime = $_POST['bookedtime'];
            $acttObj->editFun($table, $edit_id, 'bookedtime', $bookedTime);
            $orgRef = $_POST['orgRef'];
            $acttObj->editFun($table, $edit_id, 'orgRef', $orgRef);
            $ref_counter = $acttObj->read_specific("count(*) as counter", "comp_ref", "company='" . $orgName . "' AND reference='" . $orgRef . "'")['counter'];
            if ($ref_counter == 0 && !empty($orgRef)) {
                $get_reference_id = $acttObj->get_id("comp_ref");
                $acttObj->update("comp_ref", array("company" => $orgName, "reference" => $orgRef), array("id" => $get_reference_id));
                $acttObj->editFun($table, $edit_id, 'reference_id', $get_reference_id);
            } else {
                $existing_ref_id = $acttObj->read_specific("id", "comp_ref", "company='" . $orgName . "' AND reference='" . $orgRef . "'")['id'];
                $acttObj->editFun($table, $edit_id, 'reference_id', $existing_ref_id);
            }
            $porder_email = $_POST['po_req'];
            $acttObj->editFun($table, $edit_id, 'porder_email', $porder_email);
            if (isset($_POST['po_number']) && isset($_POST['purchase_order_number'])) {
                $purchase_order_number = $_POST['purchase_order_number'];
                $po_counter = $acttObj->read_specific("count(*) as counter", "porder_details", "company='" . $orgName . "' AND porder='" . $purchase_order_number . "'")['counter'];
                if ($po_counter == 0 && !empty($purchase_order_number)) {
                    $acttObj->insert('jobnotes', array('jobNote' => 'Add purchase order #' . $purchase_order_number . ' for job reference:' . $c6, 'tbl' => $table, 'time' => $misc->sys_datetime_db(), 'fid' => $edit_id, 'submitted' => "Portal: " . $_SESSION['cust_UserName'], 'dated' => date('Y-m-d')));
                } else {
                    $acttObj->editFun($table, $edit_id, 'porder', $purchase_order_number);
                }
            }
            $month = date('M');
            $month = substr($month, 0, 3);
            $nameRef = 'LSUK/' . $month . '/' . $reference_no;
            $acttObj->editFun($table, $edit_id, 'nameRef', $nameRef);
            $deliveryType = $_POST['deliveryType'];
            $acttObj->editFun($table, $edit_id, 'deliveryType', $deliveryType);
            $deliverDate = $_POST['deliverDate'];
            $acttObj->editFun($table, $edit_id, 'deliverDate', $deliverDate);
            $orgContact = $_POST['orgContact'];
            $acttObj->editFun($table, $edit_id, 'orgContact', $orgContact);
            $inchContact = $_POST['inchContact'];
            $acttObj->editFun($table, $edit_id, 'inchContact', $inchContact);
            $inchEmail = $_POST['inchEmail'];
            $acttObj->editFun($table, $edit_id, 'inchEmail', $inchEmail);
            $acttObj->editFun($table, $edit_id, 'orgName', $orgName);
            $acttObj->editFun($table, $edit_id, 'order_company_id', $query_get_info['id']);
            $inchNo = $_POST['inchNo'];
            $acttObj->editFun($table, $edit_id, 'inchNo', $inchNo);
            $line1 = $_POST['line1'];
            $acttObj->editFun($table, $edit_id, 'line1', $line1);
            $line2 = $_POST['line2'];
            $acttObj->editFun($table, $edit_id, 'line2', $line2);
            $inchRoad = $_POST['inchRoad'];
            $acttObj->editFun($table, $edit_id, 'inchRoad', $inchRoad);
            $inchCity = $_POST['inchCity'];
            $acttObj->editFun($table, $edit_id, 'inchCity', $inchCity);
            $inchPcode = $_POST['inchPcode'];
            $acttObj->editFun($table, $edit_id, 'inchPcode', $inchPcode);
            $jobStatus = $_POST['jobStatus'];
            $jobStatus_label = $jobStatus == 0 ? 'Enquiry' : 'Confirmed';
            $acttObj->editFun($table, $edit_id, 'jobStatus', $jobStatus);
            $jobDisp = $_POST['jobDisp'];
            $acttObj->editFun($table, $edit_id, 'jobDisp', $jobDisp);
            $I_Comments = $_POST['I_Comments'];
            $acttObj->editFun($table, $edit_id, 'I_Comments', $I_Comments);
            $assignDate = $misc->dated($assignDate);
            $to_add = $inchEmail;
            $subject = 'Acknowledgment of your booking request'; //"Order for Interpreter (TR)";
            $message = "<p>Dear " . $orgContact . "</p>
        <p>Thanks for booking with LSUK. This is an acknowledgment of the following booking:</p>
        <p>Language (" . $source . ")</p>
        <p>Date (" . $assignDate . ")</p>
        <p>We will write to you once again when the job is allocated to the interpreter.</p>
        <style type='text/css'>
        table.myTable {
        border-collapse: collapse;
        }
        table.myTable td,
        table.myTable th {
        border: 1px solid yellowgreen;
        padding: 5px;
        }
        </style>
        <caption align='center' style='background: grey;color: white;padding: 5px;'>Order for Translation</caption>
        <table class='myTable'>
        <tr>
        <td style='border: 1px solid yellowgreen;padding:5px;'>Source Language</td>
        <td style='border: 1px solid yellowgreen;padding:5px;'>" . $source . "</td>
        <td style='border: 1px solid yellowgreen;padding:5px;'>Target Language</td>
        <td style='border: 1px solid yellowgreen;padding:5px;'>" . $target . "</td>
        </tr>
        <tr>
        <td style='border: 1px solid yellowgreen;padding:5px;'>Assignment Date</td>
        <td style='border: 1px solid yellowgreen;padding:5px;'>" . $assignDate . "</td>
        <td style='border: 1px solid yellowgreen;padding:5px;'>Document Type</td>
        <td style='border: 1px solid yellowgreen;padding:5px;'>" . $acttObj->read_specific("tc_title", "trans_cat", "tc_id=" . $docType)['tc_title'] . "</td>
        </tr>
        <tr>
        <td style='border: 1px solid yellowgreen;padding:5px;'>Translation Type(s)</td>
        <td style='border: 1px solid yellowgreen;padding:5px;'>" . $acttObj->read_specific("GROUP_CONCAT(CONCAT(tt_title)  SEPARATOR '<br>') as tt_title", "trans_types", "tt_id IN (" . $trans_detail . ")")['tt_title'] . "</td>
        <td style='border: 1px solid yellowgreen;padding:5px;'>Translation Category</td>
        <td style='border: 1px solid yellowgreen;padding:5px;'>" . $acttObj->read_specific("GROUP_CONCAT(CONCAT(td_title)  SEPARATOR '<br>') as td_title", "trans_dropdown", "td_id IN (" . $transType . ")")['td_title'] . "</td>
        </tr>
        <tr>
        <td colspan='4' align='center' style='background: grey; color: white;'>More Information</td>
        </tr>
        <tr>
        <td style='border: 1px solid yellowgreen;padding:5px;'>Develivery Type</td>
        <td style='border: 1px solid yellowgreen;padding:5px;'>" . $deliveryType . "</td>
        <td style='border: 1px solid yellowgreen;padding:5px;'>Delivery Date</td>
        <td style='border: 1px solid yellowgreen;padding:5px;'>" . $misc->dated($deliverDate) . "</td>
        </tr>
        <tr>
        <td style='border: 1px solid yellowgreen;padding:5px;'>Company Name</td>
        <td style='border: 1px solid yellowgreen;padding:5px;'>" . $orgName . "</td>
        <td style='border: 1px solid yellowgreen;padding:5px;'>Booking Ref/Name</td>
        <td style='border: 1px solid yellowgreen;padding:5px;'>" . $orgRef . "</td>
        </tr>
        <td style='border: 1px solid yellowgreen;padding:5px;'>Building Number / Name</td>
        <td style='border: 1px solid yellowgreen;padding:5px;'>" . $inchNo . "</td>
        </tr>
        <tr>
        <td style='border: 1px solid yellowgreen;padding:5px;'>Address Line</td>
        <td style='border: 1px solid yellowgreen;padding:5px;'>" . $line1 . "</td>
        <td style='border: 1px solid yellowgreen;padding:5px;'>Address Line 2</td>
        <td style='border: 1px solid yellowgreen;padding:5px;'>" . $inchRoad . "</td>
        </tr>
        <tr>
        <td style='border: 1px solid yellowgreen;padding:5px;'>City</td>
        <td style='border: 1px solid yellowgreen;padding:5px;'>" . $inchCity . "</td>
        <td style='border: 1px solid yellowgreen;padding:5px;'>City / Town Post Code</td>
        <td style='border: 1px solid yellowgreen;padding:5px;'>" . $inchPcode . "</td>
        </tr>
        <tr>
        <td style='border: 1px solid yellowgreen;padding:5px;'>Booking Person Name*</td>
        <td style='border: 1px solid yellowgreen;padding:5px;'>" . $orgContact . "</td>
        <td style='border: 1px solid yellowgreen;padding:5px;'>Status</td>
        <td style='border: 1px solid yellowgreen;padding:5px;'>" . $jobStatus_label . "</td>
        </tr>
        <tr>
        <td colspan='4' align='center' style='background: grey; color: white;'>Contact Details</td>
        </tr>
        <tr>
        <td style='border: 1px solid yellowgreen;padding:5px;'>Contact Number</td>
        <td style='border: 1px solid yellowgreen;padding:5px;'>" . $inchContact . "</td>
        <td style='border: 1px solid yellowgreen;padding:5px;'>Email</td>
        <td style='border: 1px solid yellowgreen;padding:5px;'>" . $inchEmail . "</td>
        </tr>
        <tr>
        <td style='border: 1px solid yellowgreen;padding:5px;'>Notes if Any 1000 alphabets</td>
        <td style='border: 1px solid yellowgreen;padding:5px;' colspan='4' align='center'>" . $I_Comments . "</td>
        </tr>
        </table>
        <p>Kindest Regards </p>
        <p>Admin Team</p>
        <p>Language Services UK Limited</p>";
            $ack_message = 'Hi <b>Admin</b>
        <p>This is an email acknowledgement for ' . $source . ' Translation Job requested by ' . $orgName . ' booked on ' . $misc->dated($bookedDate) . ' ' . $bookedTime . ' for assignment date ' . $assignDate . '.</p>
        <p>Kindly verify at LSUK system.</p>
        <p>Thank you</p>';
            //php mailer used at top
            try {
                $mail->SMTPDebug = 0;
                //$mail->isSMTP(); 
                //$mailer->Host = 'smtp.office365.com';
                $mail->SMTPAuth   = true;
                $mail->Username   = 'info@lsuk.org';
                $mail->Password   = 'LangServ786';
                $mail->SMTPSecure = 'tls';
                $mail->Port       = 587;
                $mail->setFrom($from_add, 'LSUK');
                $mail->addAddress($to_add);
                $mail->addReplyTo($from_add, 'LSUK');
                $mail->isHTML(true);
                $mail->Subject = $subject;
                $mail->Body    = $message;
                if ($_FILES["file"]["name"] != NULL) {
                    if ($count < 2) {
                        for ($i = 0; $i < count($_FILES['file']['tmp_name']); $i++) {
                            $picName = $acttObj->upload_file("file_folder/trans_dox", $_FILES["file"]["name"][$i], $_FILES["file"]["type"][$i], $_FILES["file"]["tmp_name"][$i], round(microtime(true)) . $i . $count);
                            $data = array('tbl' => $table, 'file_name' => $picName, 'order_id' => $edit_id, 'dated' => date('Y-m-d h:i:s'), 'file_type' => 'c_portal', 'orgName' => $orgName);
                            $acttObj->insert('job_files', $data);
                            $mail->AddAttachment("file_folder/trans_dox/" . $picName, "Translation Attachment");
                        }
                    }
                }
                if ($mail->send()) {
                    $mail->ClearAllRecipients();
                    $mail->clearAttachments();
                    //$mail->addAddress('inf@lsuk.org');
                    $mail->addAddress($from_add);
                    $mail->addReplyTo($from_add, 'LSUK');
                    $mail->isHTML(true);
                    $mail->Subject = 'Acknowledgement for new Translation Online Portal Job';
                    $mail->Body    = $ack_message;
                    $mail->send();
                    $mail->ClearAllRecipients();
                    $mail->clearAttachments();
                    //Invoice //
                    if ($_POST['jobStatus'] == 1) {
                        $nmbr = $acttObj->get_id('invoice');
                        if ($nmbr == null) {
                            $nmbr = 0;
                        }
                        $new_nmbr = str_pad($nmbr, 5, "0", STR_PAD_LEFT);
                        $invoice = date("my") . $new_nmbr;
                        $maxId = $nmbr;
                        $acttObj->editFun('invoice', $maxId, 'invoiceNo', $invoice);
                        $acttObj->editFun($table, $edit_id, 'invoiceNo', $invoice);
                    }
                    //Email notification to related interpreters
                    $jobDisp_req = $_POST['jobDisp'];
                    $jobStatus_req = $_POST['jobStatus'];
                    if ($jobDisp_req == '1' && $jobStatus_req == '1') {
                        $source_lang_req = $source;
                        $assignDate_req = $misc->dated($_POST['assignDate']);
                        $append_table = "
                        <table>
                        <tr>
                        <td style='border: 1px solid yellowgreen;padding:5px;'>Source Language</td>
                        <td style='border: 1px solid yellowgreen;padding:5px;'>" . $source_lang_req . "</td>
                        </tr>
                        <tr>
                        <td style='border: 1px solid yellowgreen;padding:5px;'>Target Language</td>
                        <td style='border: 1px solid yellowgreen;padding:5px;'>" . $target . "</td>
                        </tr>
                        <tr>
                        <td style='border: 1px solid yellowgreen;padding:5px;'>Assignment Date</td>
                        <td style='border: 1px solid yellowgreen;padding:5px;'>" . $assignDate_req . "</td>
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
                        <td style='border: 1px solid yellowgreen;padding:5px;'>Delivery Date</td>
                        <td style='border: 1px solid yellowgreen;padding:5px;'>" . $misc->dated($deliverDate) . "</td>
                        </tr>
                        <tr>
                        <td style='border: 1px solid yellowgreen;padding:5px;'>Delivery Type</td>
                        <td style='border: 1px solid yellowgreen;padding:5px;'>" . $deliveryType . "</td>
                        </tr>
                        </table>";
                        if ($source_lang_req == $target) {
                            $put_lang = "";
                            $query_style = '0';
                        } else if ($source_lang_req != 'English' && $target != 'English') {
                            $put_lang = "";
                            $query_style = '1';
                        } else if ($source_lang_req == 'English' && $target != 'English') {
                            $put_lang = "interp_lang.lang='$target' and interp_lang.level<3";
                            $query_style = '2';
                        } else if ($source_lang_req != 'English' && $target == 'English') {
                            $put_lang = "interp_lang.lang='$source_lang_req' and interp_lang.level<3";
                            $query_style = '2';
                        } else {
                            $put_lang = "";
                            $query_style = '3';
                        }
                        if ($query_style == '0') {
                            $query_emails = "SELECT DISTINCT interpreter_reg.name, interpreter_reg.email, interpreter_reg.id FROM interpreter_reg,interp_lang WHERE interpreter_reg.code=interp_lang.code AND (SELECT COUNT(DISTINCT interp_lang.lang) FROM interp_lang WHERE interp_lang.type='trans' AND interp_lang.lang IN ('" . $source_lang_req . "') and interp_lang.level<3 and interp_lang.code=interpreter_reg.code)=1 and 
                            interpreter_reg.active='0' AND (interpreter_reg.actnow='Active' OR (interpreter_reg.actnow='Inactive' AND CURRENT_DATE() NOT BETWEEN interpreter_reg.actnow_time AND interpreter_reg.actnow_to)) AND interpreter_reg.trans='Yes' AND interpreter_reg.deleted_flag=0 AND interpreter_reg.subscribe=1 AND interpreter_reg.is_temp=0 AND interpreter_reg.isAdhoc=0";
                        } else if ($query_style == '1') {
                            $query_emails = "SELECT DISTINCT interpreter_reg.name, interpreter_reg.email, interpreter_reg.id FROM interpreter_reg,interp_lang WHERE interpreter_reg.code=interp_lang.code AND (SELECT COUNT(DISTINCT interp_lang.lang) FROM interp_lang WHERE interp_lang.type='trans' AND interp_lang.lang IN ('" . $source_lang_req . "','" . $target . "') and interp_lang.level<3 and interp_lang.code=interpreter_reg.code)=2 and 
                            interpreter_reg.active='0' AND (interpreter_reg.actnow='Active' OR (interpreter_reg.actnow='Inactive' AND CURRENT_DATE() NOT BETWEEN interpreter_reg.actnow_time AND interpreter_reg.actnow_to)) AND interpreter_reg.trans='Yes' AND interpreter_reg.deleted_flag=0 AND interpreter_reg.subscribe=1 AND interpreter_reg.is_temp=0 AND interpreter_reg.isAdhoc=0";
                        } else if ($query_style == '2') {
                            $query_emails = "SELECT DISTINCT interpreter_reg.name, interpreter_reg.email, interpreter_reg.id FROM interpreter_reg,interp_lang WHERE interpreter_reg.code=interp_lang.code AND interp_lang.type='trans' AND $put_lang and 
                            interpreter_reg.active='0' AND (interpreter_reg.actnow='Active' OR (interpreter_reg.actnow='Inactive' AND CURRENT_DATE() NOT BETWEEN interpreter_reg.actnow_time AND interpreter_reg.actnow_to)) AND interpreter_reg.trans='Yes' AND interpreter_reg.deleted_flag=0 AND interpreter_reg.subscribe=1 AND interpreter_reg.is_temp=0 AND interpreter_reg.isAdhoc=0";
                        } else {
                            $query_emails = "SELECT DISTINCT interpreter_reg.name, interpreter_reg.email, interpreter_reg.id FROM interpreter_reg WHERE 
                            interpreter_reg.active='0' AND (interpreter_reg.actnow='Active' OR (interpreter_reg.actnow='Inactive' AND CURRENT_DATE() NOT BETWEEN interpreter_reg.actnow_time AND interpreter_reg.actnow_to)) AND interpreter_reg.trans='Yes' AND interpreter_reg.deleted_flag=0 AND interpreter_reg.subscribe=1 AND interpreter_reg.is_temp=0 AND interpreter_reg.isAdhoc=0";
                        }
                        $res_emails = mysqli_query($con, $query_emails);
                        //Getting bidding email from em_format table
                        $row_format = $acttObj->read_specific("em_format", "email_format", "id=27");
                        $subject_int = "New Translation Project " . $edit_id;
                        $sub_title = "Translation job of " . $source_lang_req . " language on " . $assignDate_req . " is available for you to bid.";
                        $type_key = "nj";
                        //$app_int_ids=array();
                        while ($row_emails = mysqli_fetch_assoc($res_emails)) {
                            if ($acttObj->read_specific("COUNT(*) as blacklisted", "interp_blacklist", "interpName='id-" . $row_emails['id'] . "' AND orgName='" . $orgName . "' AND deleted_flag=0 AND blocked_for=2")["blacklisted"] == 0) {
                                $to_int_address = $row_emails['email'];
                                //Send notification on APP
                                $check_id = $acttObj->read_specific('id', 'notify_new_doc', 'interpreter_id=' . $row_emails['id'])['id'];
                                if (empty($check_id)) {
                                    $acttObj->insert('notify_new_doc', array("interpreter_id" => $row_emails['id'], "status" => '1'));
                                } else {
                                    $existing_notification = $acttObj->read_specific("new_notification", "notify_new_doc", "interpreter_id=" . $row_emails['id'])['new_notification'];
                                    $acttObj->update('notify_new_doc', array("new_notification" => $existing_notification + 1), array("interpreter_id" => $row_emails['id']));
                                }
                                $array_tokens = explode(',', $acttObj->read_specific("GROUP_CONCAT( DISTINCT token) as tokens", "int_tokens", "int_id=" . $row_emails['id'])['tokens']);
                                if (!empty($array_tokens)) {
                                    $acttObj->insert('app_notifications', array("title" => $subject_int, "sub_title" => $sub_title, "dated" => date('Y-m-d'), "int_ids" => $row_emails['id'], "read_ids" => $row_emails['id'], "type_key" => $type_key));
                                    //array_push($app_int_ids,$row_emails['id']);
                                    foreach ($array_tokens as $token) {
                                        if (!empty($token)) {
                                            $acttObj->notify($token, $subject_int, $sub_title, array("type_key" => $type_key, "job_type" => "Translation"));
                                        }
                                    }
                                }
                                //Replace date in email bidding 
                                $data   = ["[NAME]", "[ASSIGNDATE]", "[TABLE]", "[EDIT_ID]"];
                                $to_replace  = [$row_emails['name'], "$assignDate_req", "$append_table", "$edit_id"];
                                $message_int = str_replace($data, $to_replace, $row_format['em_format']);
                                $mail->setFrom($from_add, 'LSUK');
                                $mail->addAddress($to_int_address);
                                $mail->addReplyTo($from_add, 'LSUK');
                                $mail->isHTML(true);
                                $mail->Subject = $subject_int;
                                $mail->Body    = $message_int;
                                $mail->send();
                                $mail->ClearAllRecipients();
                            }
                        }
                    }
                    $msg_booking = '<p style="font-size: 22px;">Thanks for booking with LSUK Limited. You have successfully submitted the form.<br>
                        You will shortly receive a confirmation email of the request. Please check your email.<br>
                        Any problem please get in touch with LSUK Booking Team on 01173290610.</p>';
                } else {
                    $msg_error .= '<p style="font-size: 22px;" class="text-danger">Oops! An email was failed to send but the job is still booked!</p>';
                }
            } catch (Exception $e) {
                $msg_error .= '<p style="font-size: 22px;" class="text-danger">Message could not be sent! Mailer library error.</p>';
            }
            $acttObj->editFun($table, $edit_id, 'submited', 'Online');
            $acttObj->editFun($table, $edit_id, 'edited_by', 'Online');
            $acttObj->editFun($table, $edit_id, 'edited_date', date("Y-m-d H:i:s"));
            // $acttObj->new_old_table('hist_' . $table, $table, $edit_id);
            $acttObj->editFun($table, $edit_id, 'bookedVia', 'Online Portal');
        } //end of foreach
        $msg = $msg_booking . $msg_error;
    } //submission ends here
    $source = $row['source'];
    $target = $row['target'];
    $docType = $row['docType'];
    $transType = $row['transType'];
    $trans_detail = $row['trans_detail'];
    $deliveryType = $row['deliveryType'];
    $inchContact = $row['inchContact'];
    $inchEmail = $row['inchEmail'];
    $inchEmail2 = $row['inchEmail2'];
    $file = $row['file'];
    $orgName = $row['orgName'];
    $orgRef = $row['orgRef'];
    $orgContact = $row['orgContact'];
    $assignDate = $row['asignDate'];
    $deliverDate = $row['deliverDate'];
    $deliverDate2 = $row['deliverDate2'];
    $deliveryType = $row['deliveryType'];
    $remrks = $row['remrks'];
    $intrpName = $row['intrpName'];
    $dated = $row['dated'];
    $invoiceNo = $row['invoiceNo'];
    $jobStatus = $row['jobStatus'];
    $bookinType = $row['bookinType'];
    $nameRef = $row['nameRef'];
    $I_Comments = $row['I_Comments'];
    $snote = $row['snote'];
    $jobDisp = $row['jobDisp'];
    $bookedVia = $row['bookedVia'];
    $inchNo = $row['inchNo'];
    $line1 = $row['line1'];
    $line2 = $row['line2'];
    $inchRoad = $row['inchRoad'];
    $inchCity = $row['inchCity'];
    $inchPcode = $row['inchPcode'];
    $bookeddate = $row['bookeddate'];
    $bookedtime = $row['bookedtime'];
    $dbs_bookednamed = $row['namedbooked'];
    $is_temp = $row['is_temp'];
    $porder = $row['porder'];
    $po_req = $acttObj->read_specific("po_req", "comp_reg", "abrv='" . $orgName . "'")['po_req'];
    $porder_email = $row['porder_email'];
    $month = date('M');
    $month = substr($month, 0, 3);
    $lastid = $acttObj->max_id("global_reference_no") + 1;
    $nameRef = 'LSUK/' . $month . '/' . $lastid;
    $name_comp = $row_selected['name'];
    $contactNo1 = $row_selected['contactNo1'];
    $contactPerson = $row_selected['contactPerson'];
    $email = $row_selected['email'];
    $city = $row_selected['city'];
    $streetRoad = $row_selected['streetRoad'];
    $buildingName = $row_selected['buildingName'];
    $postCode = $row_selected['postCode'];
    $line1 = $row_selected['line1'];
    $line2 = $row_selected['line2'];
    ?>
</head>

<body class="boxed">
    <!-- begin container -->
    <div id="wrap">
        <!-- begin header -->
        <?php include 'source/top_nav.php'; ?>
        <!-- end header -->

        <!-- begin page title -->
        <section id="page-title">
            <div class="container clearfix">
                <h1>Place an Order (Telephone Interpreter)</h1>
                <nav id="breadcrumbs">
                    <ul>
                        <li><a href="customer_area.php">Home</a> &rsaquo;</li>
                    </ul>
                </nav>
            </div>
        </section>
        <div class="container">
            <?php if (isset($msg) && !empty($msg)) {
                echo '<div class="alert alert-info col-md-12 text-center"><b>' . $msg . '<br>
        <a href="client_orders.php" class="btn btn-primary btn-lg">View All Orders</a></b></div>';
            } else { ?>
                <div class="stepwizard">
                    <div class="stepwizard-row setup-panel">
                        <div class="stepwizard-step col-md-2 col-xs-4">
                            <a href="#step-1" type="button" class="btn btn-primary btn-circle">1</a>
                            <p><small>Assignment Details</small></p>
                        </div>
                        <div class="stepwizard-step col-md-3 col-xs-4">
                            <a href="#step-2" type="button" class="btn btn-default btn-circle disabled" disabled="disabled">2</a>
                            <p><small>Translation Delivery / Deadline Information</small></p>
                        </div>
                        <div class="stepwizard-step col-md-2 col-xs-4">
                            <a href="#step-3" type="button" class="btn btn-default btn-circle disabled" disabled="disabled">3</a>
                            <p><small>Booking Details</small></p>
                        </div>
                        <div class="stepwizard-step col-md-3 col-xs-4">
                            <a href="#step-4" type="button" class="btn btn-default btn-circle disabled" disabled="disabled">4</a>
                            <p><small>Translation Document(s)</small></p>
                        </div>
                        <div class="stepwizard-step col-md-2 col-xs-4">
                            <a href="#step-5" type="button" class="btn btn-default btn-circle disabled" disabled="disabled">5</a>
                            <p><small>Interpreter Preferences</small></p>
                        </div>
                    </div>
                </div>

                <form class="sky-form" action="" method="post" enctype="multipart/form-data">
                    <div class="panel panel-primary setup-content" id="step-1">
                        <div class="panel-heading">
                            <h3 class="panel-title">Assignment Details</h3>
                        </div>
                        <div class="panel-body">
                            <table class="table table-bordered">
                                <tr>
                                    <td align="center">
                                        <h4 style="text-transform: uppercase;"><?php echo $name; ?></h4>
                                    </td>
                                    <td style="padding-top: 18px;" align="center"><?php echo $buildingName . ' ' . $line1 . ' ' . $line2 . ' ' . $streetRoad . ' ' . $postCode . ' ' . $city; ?></td>
                                </tr>
                            </table>
                            <div class="form-group col-md-4 col-sm-6">
                                <label class="control-label select">Select Source Language *</label>
                                <select class="form-control" name="source" id="source" required=''>
                                    <?php $sql_opt = "SELECT lang FROM lang ORDER BY lang ASC";
                                    $result_opt = mysqli_query($con, $sql_opt);
                                    $options = "";
                                    while ($row_opt = mysqli_fetch_array($result_opt)) {
                                        $code = $row_opt["lang"];
                                        $name_opt = $row_opt["lang"];
                                        $options .= "<option value='$code'>" . $name_opt . "</option>";
                                    } ?>
                                    <option value="<?php echo $row['source']; ?>"><?php echo $row['source']; ?></option>
                                    <option value="">--- Select From List ---</option>
                                    <?php echo $options; ?>
                                </select>
                            </div>
                            <div class="form-group col-md-4 col-sm-6">
                                <label class="control-label select">Select Target Language *</label>
                                <select class="form-control" name="target" id="target" required=''>
                                    <?php $sql_opt = "SELECT lang FROM lang ORDER BY lang ASC";
                                    $result_opt = mysqli_query($con, $sql_opt);
                                    $options = "";
                                    while ($row_opt = mysqli_fetch_array($result_opt)) {
                                        $code = $row_opt["lang"];
                                        $name_opt = $row_opt["lang"];
                                        $options .= "<option value='$code'>" . $name_opt . "</option>";
                                    } ?>
                                    <option value="<?php echo $row['target']; ?>"><?php echo $row['target']; ?></option>
                                    <option value="" disabled>--- Select From List ---</option>
                                    <?php echo $options; ?>
                                </select>
                            </div>
                            <div class="form-group col-md-4 col-sm-6">
                                <br><button onclick="set_language()" class="btn btn-info btn_add_language" type="button" style="margin-top: 5px;">Add <i class="fa fa-plus"></i></button>
                            </div>
                            <input type="hidden" id="array_languages" name="array_languages" value="<?php echo $source . ':' . $target; ?>" />
                            <div class="form-group col-md-10 col-sm-6" id="append_language">
                                <h4 class="multi_translation_label"></h4>
                                <table align="center" class="table table-bordered">
                                    <tr class="bg-info add_tr">
                                        <td>Selected Source Language</td>
                                        <td>Selected Target Language</td>
                                        <td>Action</td>
                                    </tr>
                                    <tr id="tr_<?php echo $row['source']; ?>">
                                        <td class="<?php echo $row['source']; ?>"><?php echo $row['source']; ?></td>
                                        <td class="<?php echo $row['target']; ?>"><?php echo $row['target']; ?></td>
                                        <td><button type='button' class='btn btn-danger btn-sm' onclick='remove_language(this)'>Remove</button></td>
                                    </tr>
                                </table>
                            </div>
                            <div class="form-group col-md-4 col-sm-6" id="div_tc">
                                <label class="control-label">Select Document Type *</label>
                                <select name="docType" id="docType" class="form-control" onchange="get_trans_types($(this));">
                                    <?php $q_trans_cat = $acttObj->read_all("tc_id,tc_title", "trans_cat", "tc_status=1 ORDER BY tc_title ASC");
                                    $opt_tc = "";
                                    while ($row_tc = $q_trans_cat->fetch_assoc()) {
                                        $tc_id = $row_tc["tc_id"];
                                        $tc_title = $row_tc["tc_title"];
                                        $opt_tc .= "<option value='$tc_id'>" . $tc_title . "</option>";
                                    } ?>
                                    <?php if (isset($docType) && $docType != '8') { ?>
                                        <option selected value="<?php echo $docType; ?>"><?php echo $acttObj->read_specific("tc_title", "trans_cat", "tc_id=" . $docType)['tc_title']; ?></option>
                                    <?php } ?>
                                    <option disabled value="8">Select Translation Category</option>
                                    <?php echo $opt_tc; ?>
                                </select>
                            </div>
                            <div class="form-group col-md-4 col-sm-6" id="div_tt">
                                <label class="control-label">Select Translation Type</label>
                                <select name="trans_detail[]" multiple="multiple" id="trans_detail" class="form-control multi_class" required>
                                    <?php $q_tt = $acttObj->read_all('tt_id,tt_title', 'trans_types', "tc_id='$docType' AND tt_id NOT IN ($trans_detail) ORDER BY tt_title ASC");
                                    $arr_trans_detail = explode(',', $trans_detail);
                                    $option_tt = "";
                                    for ($tt_i = 0; $tt_i < count($arr_trans_detail); $tt_i++) {
                                        $option_tt .= "<option selected value='$arr_trans_detail[$tt_i]'>" . $acttObj->read_specific("tt_title", "trans_types", "tt_id=" . $arr_trans_detail[$tt_i])['tt_title'] . "</option>";
                                    }
                                    echo $option_tt;
                                    while ($row_tt = $q_tt->fetch_assoc()) {
                                        echo '<option value="' . $row_tt['tt_id'] . '">' . $row_tt['tt_title'] . '</option>';
                                    } ?>
                                </select>
                            </div>
                            <div class="form-group col-md-4 col-sm-6" id="div_td">
                                <label class="control-label">Select Translation Category</label>
                                <select name="transType[]" multiple="multiple" id="transType" class="form-control multi_class" required>
                                    <?php $q_td = $acttObj->read_all('td_id,td_title', 'vw_translation', "tc_id='$docType' AND td_id NOT IN ($transType) ORDER BY td_title ASC");
                                    $arr_transType = explode(',', $transType);
                                    $option_td = "";
                                    for ($td_i = 0; $td_i < count($arr_transType); $td_i++) {
                                        $option_td .= "<option selected value='$arr_transType[$td_i]'>" . $acttObj->read_specific("td_title", "trans_dropdown", "td_id=" . $arr_transType[$td_i])['td_title'] . "</option>";
                                    }
                                    echo $option_td;
                                    while ($row_td = $q_td->fetch_assoc()) {
                                        echo '<option value="' . $row_td['td_id'] . '">' . $row_td['td_title'] . '</option>';
                                    } ?>
                                </select>
                            </div>
                            <div class="form-group col-md-4 col-sm-6 search-box">
                                <label class="control-label input">Your Reference <i class="fa fa-question-circle" title="(Name, Initials or File Ref. Number)"></i></label>
                                <input class="form-control" name="orgRef" id="orgRef" type="text" required='' autocomplete="off" placeholder="Type your reference" value="<?php echo $orgRef; ?>" />
                                <i id="confirm_value" style="display:none;position: absolute;right: 25px;top: 35px;cursor:pointer;" onclick="$(this).hide();$('.result').empty();" class="glyphicon glyphicon-ok-sign text-success" title="Confirm this reference"></i>
                                <div class="result"></div>
                            </div>
                            <div class="form-group col-md-4 col-sm-6" title="System generated ID by LSUK">
                                <label class="control-label input">Booking Reference (LSUK)</label>
                                <input class="form-control" name="nameRef" type="text" required='' readonly="readonly" value="<?php echo $nameRef; ?>" />
                            </div>
                            <div class="form-group col-md-12">
                                <button class="btn btn-primary nextBtn pull-right" type="button">Next <i class="fa fa-angle-right"></i><i class="fa fa-angle-right"></i></button>
                            </div>
                        </div>
                    </div>
                    <div class="panel panel-primary setup-content" id="step-2">
                        <div class="panel-heading">
                            <h3 class="panel-title">Translation Delivery / Deadline Information</h3>
                        </div>
                        <div class="panel-body">
                            <div class="form-group col-md-4 col-sm-6">
                                <label class="control-label input2">Assignment Date*</label>
                                <input class="form-control date_picker" type="date" id="assignDate" name="assignDate" required='' value='<?php echo $assignDate; ?>' />
                            </div>
                            <div class="form-group col-md-4 col-sm-6">
                                <label class="control-label select">Select Delivery Type</label>
                                <select class="form-control" name="deliveryType" id="deliveryType" required>
                                    <option value="<?php echo $deliveryType; ?>"><?php echo $deliveryType; ?></option>
                                    <option value="">--Select--</option>
                                    <option>Standard Service (1 -2 Weeks)</option>
                                    <option>Quick Service (2-3 Days)</option>
                                    <option>Emergency Service (1-2 Days)</option>
                                </select>
                            </div>
                            <div class="form-group col-md-4 col-sm-6">
                                <label class="control-label input">Delivery Date*</label>
                                <input name="deliverDate" type="date" class="form-control date_picker" value='<?php echo $deliverDate; ?>' required />
                            </div>
                            <!--Purchase order on off-->
                            <div class="row"></div>
                            <div class="form-group col-md-4 col-sm-6 <?php if ($po_req == 0) {
                                                                            echo 'hidden';
                                                                        } ?>" id="div_check_po">
                                <label>Do you have purchase order number?</label>
                                <br><span class="col-md-offset-2">
                                    <label class="checkbox-inline" style="margin-top: 4px;border: 1px solid lightgrey;padding: 4px 10px;"><input <?php if ($po_req == 1 && !empty($porder)) {
                                                                                                                                                        echo 'checked';
                                                                                                                                                    } ?> style="transform: scale(1.2);" onchange="booking_purch_order();" type="radio" name="po_number" value="1"> Yes</label>
                                    <label class="checkbox-inline" style="border: 1px solid lightgrey;padding: 4px 10px;"><input <?php if ($po_req == 1 && empty($porder)) {
                                                                                                                                        echo 'checked';
                                                                                                                                    } ?> style="transform: scale(1.2);" onchange="booking_purch_order();" type="radio" name="po_number" value="0"> No</label>
                                </span>
                            </div>
                            <div class="form-group col-md-4 col-sm-6 <?php if (($po_req == 0) || ($po_req == 1 && empty($porder))) {
                                                                            echo 'hidden';
                                                                        } ?> search-box" id="div_po_number">
                                <label class="optional">Enter purchase order number</label>
                                <input name="purchase_order_number" id="purchase_order_number" type="text" class="form-control" autocomplete="off" placeholder="Search purchase order number" <?php if ($po_req == 1 && !empty($porder)) { ?> value="<?php echo $porder; ?>" <?php } ?> />
                                <i id="confirm_value" style="position: absolute; right: 25px; top: 35px; display: block;cursor:pointer;" onclick="$(this).hide();$(this).next('.result').empty();" class="glyphicon glyphicon-ok-sign text-success confirm_element" title="Confirm this purchase order number"></i>
                                <div class="result"></div>
                            </div>
                            <div id="div_po_req" class="form-group <?php if (($po_req == 0) || ($po_req == 1 && !empty($porder))) {
                                                                        echo 'hidden';
                                                                    } ?> col-md-4 col-sm-6">
                                <label>Purchase Order Email Address </label>
                                <input oninput="$('#write_po_email').html($(this).val());if($(this).val()){$('.tr_po_email').removeClass('hidden');}" name="po_req" id="po_req" type="text" class="long form-control" placeholder='Fill email for purchase order' <?php if ($po_req == 1) { ?>required value="<?php echo $porder_email; ?>" data-value="<?php echo $porder_email; ?>" <?php } ?> />
                            </div>
                            <div class="row"></div>
                            <div class="form-group col-md-12">
                                <button class="btn btn-primary nextBtn pull-right" type="button">Next <i class="fa fa-angle-right"></i><i class="fa fa-angle-right"></i></button>
                            </div>
                        </div>
                    </div>
                    <div class="panel panel-primary setup-content" id="step-3">
                        <div class="panel-heading">
                            <h3 class="panel-title">Booking Person Details</h3>
                        </div>
                        <div class="panel-body">
                            <div class="form-group col-md-4">
                                <label class="control-label input">Booking Person Name</label>
                                <input class="long form-control" name="inchPerson" type="text" value="<?php echo @$contactPerson; ?>" />
                            </div>
                            <div class="form-group col-md-4">
                                <label class="control-label input">Contact Number</label>
                                <input name="inchContact" id="inchContact" type="text" class="long form-control" value="<?php echo @$contactNo1; ?>" />
                            </div>
                            <div class="form-group col-md-4">
                                <label class="control-label input">Interpreter Contact Name&nbsp;* <i class="fa fa-question-circle" title="Assignment in-Charge"></i></label>
                                <input class="form-control" name="orgContact" id="orgContact" type="text" placeholder='' required='' />
                            </div>
                            <div class="form-group col-md-4">
                                <label class="control-label input">Email Address (For Booking Confirmation)</label>
                                <input name="inchEmail" id="inchEmail" type="email" class="long form-control" placeholder='' required value="<?php echo @$email; ?>" />
                            </div>
                            <div class="form-group col-md-4">
                                <label class="control-label input">Booking Date&nbsp;*</label>
                                <input onchange="OnDateChgAjax();" type="date" name="bookeddate" id="bookeddate" required placeholder='Booked Date' class="form-control date_picker" />
                            </div>
                            <div class="form-group col-md-4">
                                <label class="control-label input">Booking Time&nbsp;*</label>
                                <input onchange="OnTimeChgAjax();" type="time" name="bookedtime" id="bookedtime" required placeholder='Booked Time' step="300" class="form-control time_picker2" />
                            </div>
                            <div class="form-group col-md-4 hidden">
                                <label class="input">Building Number / Name (Business Name)</label>
                                <input class="" name="inchNo" id="inchNo" type="text" value="<?php echo @$buildingName; ?>" placeholder='' readonly />
                            </div>
                            <div class="form-group col-md-4 hidden">
                                <label class="input">Address Line 1</label>
                                <input class="" name="line1" id="line1" type="text" placeholder='' value="<?php echo @$line1; ?>" readonly />
                            </div>
                            <div class="form-group col-md-4 hidden">
                                <label class="input">Address Line 2</label>
                                <input class="" name="line2" id="line2" type="text" placeholder='' value="<?php echo @$line2; ?>" readonly />
                            </div>
                            <div class="form-group col-md-4 hidden">
                                <label class="input">Address Line 3</label>
                                <input class="" name="inchRoad" id="inchRoad" type="text" value="<?php echo isset($inchRoad) ? @$inchRoad : ""; ?>" placeholder='' readonly />
                            </div>
                            <div class="form-group col-md-4 hidden">
                                <label class="input">City / Town</label>
                                <input class="" name="inchCity" id="inchCity" type="text" value="<?php echo @$city; ?>" placeholder='' readonly />
                            </div>
                            <div class="form-group col-md-4 hidden">
                                <label class="input">Post Code</label>
                                <input class="" name="inchPcode" id="inchPcode" type="text" value="<?php echo @$postCode; ?>" readonly />
                            </div>
                            <div class="form-group col-md-12">
                                <button class="btn btn-primary nextBtn pull-right" type="button">Next <i class="fa fa-angle-right"></i><i class="fa fa-angle-right"></i></button>
                            </div>
                        </div>
                    </div>
                    <div class="panel panel-primary setup-content" id="step-4">
                        <div class="panel-heading">
                            <h3 class="panel-title">Upload Documents (if any)</h3>
                        </div>
                        <div class="panel-body">
                            <br>
                            <div class="form-group col-sm-12" id="dvPreview"></div>
                            <div class="form-group col-md-4 col-sm-6">
                                <label class="input">Upload Document <i class="fa fa-question-circle" title='Acceptable formats: ("gif", "jpeg", "jpg", "png", "pdf", "doc", "docx", "xlsx")'></i></label>
                                <input title='Acceptable formats: ("gif", "jpeg", "jpg", "png", "pdf", "doc", "docx", "xlsx")' name="file[]" onchange="loadFiles(event)" multiple="multiple" type="file" size="60" multiple="multiple" class="long form-control" id="file" style="border:1px solid #CCC" />
                                <script language="javascript" type="text/javascript">
                                    window.onload = function() {
                                        var fileUpload = document.getElementById("file");
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
                                                        //alert(ext);

                                                        /*if( file_name.length === 1 || ( file_name[0] === "" && file_name.length === 2 ) ) {
                                                            return "";
                                                        }else{
                                                            var ext=file_name.pop();
                                                        }*/
                                                        var reader = new FileReader();
                                                        reader.onload = function(e) {
                                                            //if (accepted_types.indexOf(file_name[1]) > 0) {
                                                            var img = document.createElement("IMG");
                                                            img.height = "100";
                                                            img.width = "100";
                                                            img.title = "Document Attachment";
                                                            img.style.display = 'inline';
                                                            img.style.margin = '0px 2px 0px 0px';
                                                            img.style.padding = '0px 2px';
                                                            img.src = e.target.result;
                                                            /*}else{
                                                                var img = document.createElement("DIV");
                                                                img.setAttribute("class", "img-thumbnail");
                                                                img.setAttribute("style", "margin:1px;height:100px;width:100px;text-align:center;");
                                                                img.innerHTML = "Doc "+i;
                                                            }*/
                                                            dvPreview.appendChild(img);
                                                        }
                                                        reader.readAsDataURL(file);
                                                    } else {
                                                        alert(file.name + " is not a valid file.");
                                                        dvPreview.innerHTML = "";
                                                        return false;
                                                    }
                                                }
                                            } else {
                                                alert("This browser does not support HTML5 FileReader.");
                                            }
                                        }
                                    };
                                </script>
                            </div>
                            <div class="form-group col-md-12">
                                <button class="btn btn-primary nextBtn pull-right" type="button">Next <i class="fa fa-angle-right"></i><i class="fa fa-angle-right"></i></button>
                            </div>
                        </div>
                    </div>
                    <div class="panel panel-primary setup-content" id="step-5">
                        <div class="panel-heading">
                            <h3 class="panel-title">Interpreter Preferences</h3>
                        </div>
                        <div class="panel-body">
                            <div class="form-group col-sm-5">
                                <label class="control-label optional">Booking Status:</label><br>
                                <div class="radio-inline ri"><label><input type="radio" name="jobStatus" value="1" <?php if ($jobStatus == '1') { ?> checked="checked" <?php } ?> />
                                        <span class="label label-primary">Confirmed <i class="fa fa-check-circle"></i></span></label></div>
                                <div class="radio-inline ri"><label><input name="jobStatus" type="radio" value="0" <?php if ($jobStatus == '0') { ?> checked="checked" <?php } ?> />
                                        <span class="label label-info">Enquiry <i class="fa fa-question"></i></span></label></div>
                            </div>
                            <div class="form-group col-sm-5">
                                <label class="control-label optional">Display job on website ?</label><br>
                                <div class="radio-inline ri"><label><input name="jobDisp" type="radio" value="1" <?php if ($jobDisp == '1') { ?> checked="checked" <?php } ?> />
                                        <span class="label label-primary" style="font-size:100%;padding: .5em 0.6em 0.5em;">Yes <i class="fa fa-check-circle"></i></span></label></div>
                                <div class="radio-inline ri"><label><input type="radio" name="jobDisp" value="0" <?php if ($jobDisp == '0') { ?> checked="checked" <?php } ?> />
                                        <span class="label label-info" style="font-size:100%;padding: .5em 0.6em 0.5em;">No <i class="fa fa-remove"></i></span></label></div>
                            </div>
                            <div class="form-group col-md-8">
                                <b>NOTES (if Any):</b>
                                <textarea class="form-control" name="I_Comments" rows="4"></textarea>
                            </div>
                            <?php if (1 == 2) { ?>
                                <div class="form-group col-sm-12">
                                    <script src='https://www.google.com/recaptcha/api.js'></script>
                                    <div class="g-recaptcha" data-sitekey="6LextRoUAAAAAGSGzslurL5xeNDw3lDDVkxM9rZe"></div>
                                </div>
                            <?php } ?>
                            <div class="form-group col-md-12">
                                <input type="submit" name="submit" class="btn btn-lg btn-primary" value="Submit" />
                            </div>
                        </div>
                    </div>
                </form>
        </div>
        <?php include 'source/footer.php'; ?>
    <?php } ?>
    </div>
</body>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.13/css/bootstrap-multiselect.css" rel="stylesheet" type="text/css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.13/js/bootstrap-multiselect.js" type="text/javascript"></script>
<script type="text/javascript">
    function get_trans_types(elem) {
        var tc_id = elem.val();
        $.ajax({
            url: 'ajax_client_portal.php',
            method: 'post',
            dataType: "json",
            data: {
                tc_id: tc_id
            },
            success: function(data) {
                $('#div_tt').css('display', 'block');
                $('#div_td').css('display', 'block');
                $('#div_tt').html(data[0]);
                $('#div_td').html(data[1]);
                $('.multi_class').multiselect({
                    includeSelectAllOption: true,
                    numberDisplayed: 1,
                    enableFiltering: true,
                    enableCaseInsensitiveFiltering: true
                });
            },
            error: function(xhr) {
                alert("An error occured: " + xhr.status + " " + xhr.statusText);
            }
        });
    }

    function pop_language(arr, value) {
        var index = arr.indexOf(value);
        if (index > -1) {
            arr.splice(index, 1);
        }
        return arr;
    }
    var selected_languages = [];
    var counter = 1;
    selected_languages.push($('#source option:selected').text() + ":" + $('#target option:selected').text());

    function set_language() {
        var source_text = $('#source option:selected').text();
        var source_value = $('#source option:selected').val();
        var target_text = $('#target option:selected').text();
        var target_value = $('#target option:selected').val();
        if (!source_value) {
            $('#source').focus();
        } else if (!target_value) {
            $('#target').focus();
        } else {
            counter++;
            if (counter >= 2) {
                $('.multi_translation_label').text('Added Multiple Language Translations');
            } else {
                $('.multi_translation_label').text('');
            }
            if ($('#array_languages').val().includes(source_text + ":" + target_text)) {
                if (confirm('Are you sure to add the same source & target language again?')) {
                    $('.btn_add_language').html('Add More Languages');
                    $("#append_language table").removeClass("hidden");
                    $("#append_language table tr:last").after("<tr id='tr_" + source_value + "'><td class='" + source_value + "'>" + source_text + "</td><td class='" + target_value + "'>" + target_text + "</td><td><button type='button' class='btn btn-danger btn-sm' onclick='remove_language(this)'>Remove</button></td></tr>");
                    // $('#source').find('option:eq(0)').attr("selected",true);
                    // $('#target').find('option:eq(1)').attr("selected",true);
                    selected_languages.push(source_text + ":" + target_text);
                    $('#array_languages').val(selected_languages);
                }
            } else {
                $('.btn_add_language').html('Add More Languages');
                $("#append_language table").removeClass("hidden");
                $("#append_language table tr:last").after("<tr id='tr_" + source_value + "'><td class='" + source_value + "'>" + source_text + "</td><td class='" + target_value + "'>" + target_text + "</td><td><button type='button' class='btn btn-danger btn-sm' onclick='remove_language(this)'>Remove</button></td></tr>");
                // $('#source').find('option:eq(0)').attr("selected",true);
                // $('#target').find('option:eq(1)').attr("selected",true);
                selected_languages.push(source_text + ":" + target_text);
                $('#array_languages').val(selected_languages);
            }
        }
    }

    function remove_language(elem) {
        counter--;
        if (counter <= 1) {
            $('.multi_translation_label').text('');
        } else {
            $('.multi_translation_label').text('Added Multiple Language Translations');
        }
        $(elem).closest('tr').remove();
        var old_source_text = $(elem).closest('tr').find("td:first").text();
        var old_target_text = $(elem).closest('tr').find("td:eq(1)").text();
        pop_language(selected_languages, old_source_text + ":" + old_target_text);
        $('#array_languages').val(selected_languages);
        if (!$('#array_languages').val()) {
            $('#append_language table').addClass('hidden');
            $('.btn_add_language').html('Add <i class="fa fa-plus"></i>');
        }
    }
    $('.nextBtn:eq(0)').click(function() {
        var check_languages = $('#array_languages').val();
        var source_language = $('#source').val();
        var target_language = $('#target').val();
        if (!check_languages && source_language && target_language) {
            set_language();
        }
    });
    $(document).ready(function() {
        var navListItems = $('div.setup-panel div a'),
            allWells = $('.setup-content'),
            allNextBtn = $('.nextBtn');
        allWells.hide();
        navListItems.click(function(e) {
            e.preventDefault();
            var $target = $($(this).attr('href')),
                $item = $(this);
            if (!$item.hasClass('disabled')) {
                navListItems.removeClass('btn-primary').addClass('btn-default');
                $item.addClass('btn-primary');
                allWells.hide();
                $target.show();
                $target.find('.form-control:eq(0)').focus();
            }
        });

        allNextBtn.click(function() {
            var curStep = $(this).closest(".setup-content"),
                curStepBtn = curStep.attr("id"),
                nextStepWizard = $('div.setup-panel div a[href="#' + curStepBtn + '"]').parent().next().children("a"),
                curInputs = curStep.find(".form-control"),
                isValid = true;

            $(".form-group").removeClass("has-error");
            for (var i = 0; i < curInputs.length; i++) {
                if (!curInputs[i].validity.valid) {
                    isValid = false;
                    $(curInputs[i]).closest(".form-group").addClass("has-error");
                }
            }

            if (isValid) nextStepWizard.removeAttr('disabled').removeClass('disabled').trigger('click');
        });

        $('div.setup-panel div a.btn-primary').trigger('click');
        $(function() {
            $('.multi_class').multiselect({
                includeSelectAllOption: true,
                numberDisplayed: 1,
                enableFiltering: true,
                enableCaseInsensitiveFiltering: true
            });
        });
    });
    $(document).ready(function() {
        $('.search-box input[type="text"]').on("keyup input", function() {
            var current_element = $(this);
            /* Get input value on change */
            var inputVal = $(this).val();
            var orgName = '<?php echo $orgName; ?>';
            var resultDropdown = $(this).siblings(".result");
            if (inputVal.length) {
                $.get("ajax_client_portal.php", {
                    term: inputVal,
                    orgName: orgName
                }).done(function(data) {
                    // Display the returned data in browser
                    resultDropdown.html(data);
                    current_element.parents('div').find('i#confirm_value').show();
                });
            } else {
                resultDropdown.empty();
                current_element.parents('div').find('i#confirm_value').show();
            }
        });
        // Set search input value on click of result item
        $(document).on("click", ".result p.click", function() {
            $(this).parents(".search-box").find('input[type="text"]').val($(this).text());
            $(this).parent(".result").empty();
            $(this).parents('div').find('i#confirm_value').hide();
        });
    });

    function booking_purch_order() {
        if ($('input[name="po_number"]:checked').val() == 1) {
            $('.tr_po_email,#div_po_req').addClass('hidden');
            $('#purchase_order_number').attr('required', 'required');
            $('#po_req').removeAttr('required');
            var orgName = '<?php echo $orgName; ?>';
            if (orgName) {
                $('#div_po_number').removeClass('hidden');
            } else {
                $('#div_po_number').addClass('hidden');
            }
        } else {
            $('.tr_po_email,#div_po_req').removeClass('hidden');
            $('#purchase_order_number').removeAttr('required');
            $('#po_req').attr('required', 'required');
            $('#div_po_number').addClass('hidden');
        }
    }
</script>
<script>
    window.__lo_site_id = 300741;
    (function() {
        var wa = document.createElement('script');
        wa.type = 'text/javascript';
        wa.async = true;
        wa.src = 'https://d10lpsik1i8c69.cloudfront.net/w.js';
        var s = document.getElementsByTagName('script')[0];
        s.parentNode.insertBefore(wa, s);
    })();
</script>

</html>