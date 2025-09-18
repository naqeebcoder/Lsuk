<?php include 'source/setup_email.php';
if (session_id() == '' || !isset($_SESSION)) {
    session_start();
}
if (empty($_SESSION['cust_UserName'])) {
    echo '<script type="text/javascript">window.location="index.php";</script>';
}
include 'source/db.php';
include 'source/class.php';
$id = base64_decode($_GET['id']);
$table = 'interpreter';
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
    <script type="text/javascript" src="lsuk_system/js/postcodelookup.js"></script>
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
        $from_add = "info@lsuk.org";
        $subject = 'Acknowledgment of your booking request'; //"Order for Interpreter (F 2 F)";
        foreach ($array_languages as $language) {
            $get_language = explode(':', $language);
            $source = $get_language[0];
            $assignDate = $_POST['assignDate'];
            $bookedDate = $_POST['bookeddate'];
            $bookedTime = $_POST['bookedtime'];
            $assignTime = $_POST['assignTime'];
            $assignDur = $_POST['assignDur'];
            $orgContact = $_POST['orgContact'];
            $orgRef = $_POST['orgRef'];
            $row = $acttObj->read_specific("count(id) as val", $table, "source='$source' and assignDate='$assignDate'
        and assignTime='$assignTime' and orgName='$orgName' and orgContact='$orgContact' and orgRef='$orgRef'");
            $val = $row['val'];
            if ($val == 0) {
                $edit_id = $acttObj->get_id($table);
                //Create & save new reference no
                $reference_no = $acttObj->generate_reference(1, $table, $edit_id);
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
                if (!empty($_POST['interp_cat'])) {
                    $interp_cat = $_POST['interp_cat'];
                    $acttObj->editFun($table, $edit_id, 'interp_cat', $interp_cat);
                }
                if ($_POST['interp_cat'] != '12') {
                    $interp_type = implode(",", $_POST['interp_type']);
                    $acttObj->editFun($table, $edit_id, 'interp_type', $interp_type);
                }
                $acttObj->editFun($table, $edit_id, 'assignDate', $assignDate);
                $acttObj->editFun($table, $edit_id, 'assignTime', $assignTime);
                $acttObj->editFun($table, $edit_id, 'bookeddate', $bookedDate);
                $acttObj->editFun($table, $edit_id, 'bookedtime', $bookedTime);
                $acttObj->editFunTimeAsMins($table, $edit_id, 'assignDur', $assignDur);
                $acttObj->editFunTimeAsMins($table, $edit_id, 'guess_dur', $assignDur);
                list($part1, $part2) = explode(':', $assignDur);
                $total_dur = $part1 * 60 + $part2;
                if ($total_dur > 60) {
                    $hours = $total_dur / 60;
                    if (floor($hours) > 1) {
                        $hr = "hours";
                    } else {
                        $hr = "hour";
                    }
                    $mins = $total_dur % 60;
                    if ($mins == 00) {
                        $get_dur = sprintf("%2d $hr", $hours);
                    } else {
                        $get_dur = sprintf("%2d $hr %02d minutes", $hours, $mins);
                    }
                } else if ($total_dur == 60) {
                    $get_dur = "1 Hour";
                } else {
                    $get_dur = $total_dur . " minutes";
                }
                if (!empty($_POST['interp_cat']) && $_POST['interp_cat'] == '12') {
                    $assignIssue = $_POST['assignIssue'];
                    $acttObj->editFun($table, $edit_id, 'assignIssue', $assignIssue);
                }
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
                $buildingName = $_POST['buildingName'];
                $acttObj->editFun($table, $edit_id, 'buildingName', $buildingName);
                $street = $_POST['street'];
                $acttObj->editFun($table, $edit_id, 'street', $street);
                $assignCity = $_POST['assignCity'];
                $acttObj->editFun($table, $edit_id, 'assignCity', $assignCity);
                $postCode = $_POST['postCode'];
                $acttObj->editFun($table, $edit_id, 'postCode', $postCode);
                $inchPerson = $_POST['inchPerson'];
                $acttObj->editFun($table, $edit_id, 'inchPerson', $inchPerson);
                $inchContact = $_POST['inchContact'];
                $acttObj->editFun($table, $edit_id, 'inchContact', $inchContact);
                $acttObj->editFun($table, $edit_id, 'orgContact', $orgContact);
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
                $dbs_checked = $_POST['dbs_checked'];
                $acttObj->editFun($table, $edit_id, 'dbs_checked', $dbs_checked);
                $dbs_checked_label = $dbs_checked == 0 ? 'Yes' : 'No';
                $dbs_checked_req = isset($dbs_checked) && !empty($dbs_checked) && $dbs_checked == 0 ? 'AND interpreter_reg.dbs_checked=0' : '';
                $jobStatus = $_POST['jobStatus'];
                $jobStatus_label = $jobStatus == 0 ? 'Enquiry' : 'Confirmed';
                $acttObj->editFun($table, $edit_id, 'jobStatus', $jobStatus);
                $jobDisp = $_POST['jobDisp'];
                $acttObj->editFun($table, $edit_id, 'jobDisp', $jobDisp);
                $I_Comments = $_POST['I_Comments'];
                $acttObj->editFun($table, $edit_id, 'I_Comments', $I_Comments);
                $gender = $_POST['gender'];
                $acttObj->editFun($table, $edit_id, 'gender', $gender);
                $assignDate = $misc->dated($assignDate);
                $assignCity_name = explode(',', $_POST['assignCity']);
                $assignCity_req = $assignCity_name[0];
                $to_add = $inchEmail;
                $write_interp_cat = $interp_cat == '12' ? $assignIssue : $acttObj->read_specific("ic_title", "interp_cat", "ic_id=" . $interp_cat)['ic_title'];
                $write_interp_type = $interp_cat == '12' ? '' : $acttObj->read_specific("GROUP_CONCAT(CONCAT(it_title)  SEPARATOR ' <b> & </b> ') as it_title", "interp_types", "it_id IN (" . $interp_type . ")")['it_title'];
                if ($interp_cat == '12') {
                    $append_issue = "<tr><td style='border: 1px solid yellowgreen;padding:5px;'>Assignment Category</td><td style='border: 1px solid yellowgreen;padding:5px;'>Other</td><td style='border: 1px solid yellowgreen;padding:5px;'>Assignment Details</td><td style='border: 1px solid yellowgreen;padding:5px;'>" . $assignIssue . "</td></tr>";
                } else {
                    $append_issue = "<tr><td style='border: 1px solid yellowgreen;padding:5px;'>Assignment Category</td><td style='border: 1px solid yellowgreen;padding:5px;'>" . $write_interp_cat . "</td><td style='border: 1px solid yellowgreen;padding:5px;'>Assignment Details</td><td style='border: 1px solid yellowgreen;padding:5px;'>" . $write_interp_type . "</td></tr>";
                }
                $message = "<p>Dear " . $inchPerson . "</p>
            <p>Thanks for booking with LSUK. This is an acknowledgment of the following booking:</p>
            <p>Language (" . $source . ")</p>
            <p>Date (" . $assignDate . ")</p>
            <p>Time (" . $assignTime . ")</p>
            <p>At (" . $buildingName . " " . $street . " " . $assignCity . " " . $postCode . ")</p>
            <p>We will write to you once again when the job is allocated to the interpreter.</p>
            <style type='text/css'>
            table.myTable {border-collapse: collapse;}
            table.myTable td,table.myTable th {border: 1px solid yellowgreen;padding: 5px;}
            </style>
            <caption align='center' style='background: grey;color: white;padding: 5px;'>Order for Interpreter (F 2 F)</caption>
            <table class='myTable'>
            <tr>
            <td style='border: 1px solid yellowgreen;padding:5px;'>Source Language</td>
            <td style='border: 1px solid yellowgreen;padding:5px;'>" . $source . "</td>
            <td style='border: 1px solid yellowgreen;padding:5px;'>Target Language</td>
            <td style='border: 1px solid yellowgreen;padding:5px;'>" . $target . "</td>
            </tr>
            <tr>
            <td style='border: 1px solid yellowgreen;padding:5px;'>Assignment Date/Time</td>
            <td style='border: 1px solid yellowgreen;padding:5px;'>" . $assignDate . " " . $assignTime . "</td>
            <td style='border: 1px solid yellowgreen;padding:5px;'>Assignment Duration</td>
            <td style='border: 1px solid yellowgreen;padding:5px;'>" . $get_dur . "</td>
            </tr>

            " . $append_issue . "

            <tr>
            <td style='border: 1px solid yellowgreen;padding:5px;'>DBS checked interpreter Requested ?</td>
            <td style='border: 1px solid yellowgreen;padding:5px;'>" . $dbs_checked_label . "</td>
            <td style='border: 1px solid yellowgreen;padding:5px;'>Booking Reference</td>
            <td style='border: 1px solid yellowgreen;padding:5px;'>" . $nameRef . "</td>
            </tr>
            <tr>
            <td colspan='4' align='center' style='background: grey; color: white;'>Assignment Location</td>
            </tr>
            <tr>
            <td style='border: 1px solid yellowgreen;padding:5px;'>Building No / Name</td>
            <td style='border: 1px solid yellowgreen;padding:5px;'>" . $buildingName . "</td>
            <td style='border: 1px solid yellowgreen;padding:5px;'>Street / Road</td>
            <td style='border: 1px solid yellowgreen;padding:5px;'>" . $street . "</td>
            </tr>
            <tr>
            <td style='border: 1px solid yellowgreen;padding:5px;'>City</td>
            <td style='border: 1px solid yellowgreen;padding:5px;'>" . $assignCity . "</td>
            <td style='border: 1px solid yellowgreen;padding:5px;'>City / Town Post Code</td>
            <td style='border: 1px solid yellowgreen;padding:5px;'>" . $postCode . "</td>
            </tr>
            <tr>
            <td colspan='4' align='center' style='background: grey; color: white;'>Booking Organization Details</td>
            </tr>
            <tr>
            <td style='border: 1px solid yellowgreen;padding:5px;'>Company Name</td>
            <td style='border: 1px solid yellowgreen;padding:5px;'>" . $orgName . "</td>
            <td style='border: 1px solid yellowgreen;padding:5px;'>Booking Ref/Name</td>
            <td style='border: 1px solid yellowgreen;padding:5px;'>" . $orgRef . "</td>
            </tr>
            <tr>
            <td style='border: 1px solid yellowgreen;padding:5px;'>Interpreter Contact Name</td>
            <td style='border: 1px solid yellowgreen;padding:5px;'>" . $orgContact . "</td>
            <td style='border: 1px solid yellowgreen;padding:5px;'>&nbsp;</td>
            <td style='border: 1px solid yellowgreen;padding:5px;'>&nbsp;</td>
            </tr>
            <tr>
            <td colspan='4' align='center' style='background: grey; color: white;'>Assignment in-Charge</td>
            </tr>
            <tr>
            <td style='border: 1px solid yellowgreen;padding:5px;'>Booking Person Name if Different</td>
            <td style='border: 1px solid yellowgreen;padding:5px;'>" . $inchPerson . "</td>
            <td style='border: 1px solid yellowgreen;padding:5px;'>Contact Number</td>
            <td style='border: 1px solid yellowgreen;padding:5px;'>" . $inchContact . "</td>
            </tr>
            <tr>
            <td style='border: 1px solid yellowgreen;padding:5px;'>Email Address</td>
            <td style='border: 1px solid yellowgreen;padding:5px;'>" . $inchEmail . "</td>
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
            <td style='border: 1px solid yellowgreen;padding:5px;'>Booked Date</td>
            <td style='border: 1px solid yellowgreen;padding:5px;'>" . $misc->dated($bookedDate) . "</td>
            <td style='border: 1px solid yellowgreen;padding:5px;'>Booked Time</td>
            <td style='border: 1px solid yellowgreen;padding:5px;'>" . $bookedTime . "</td>
            </tr>
            <tr>
            <td colspan='4' align='center' style='background: grey; color: white;'>Interpreter Details</td>
            </tr>
            <tr>
            <td style='border: 1px solid yellowgreen;padding:5px;'>Gender</td>
            <td style='border: 1px solid yellowgreen;padding:5px;'>" . $gender . "</td>
            <td style='border: 1px solid yellowgreen;padding:5px;'>Status</td>
            <td style='border: 1px solid yellowgreen;padding:5px;'>" . $jobStatus_label . "</td>
            </tr>
            <tr>
            <td style='border: 1px solid yellowgreen;padding:5px;'>Notes if Any 1000 alphabets</td>
            <td colspan='4' align='center' style='border: 1px solid yellowgreen;padding:5px;'>" . $I_Comments . "</td>
            </tr>
            </table>
            <p>Kindest Regards </p>
            <p>Admin Team</p>
            <p>Language Services UK Limited</p>";
                $ack_message = 'Hi <b>Admin</b>
            <p>This is an email acknowledgement for ' . $source . ' Face to Face Job of ' . $get_dur . ' requested by ' . $orgName . ' booked on ' . $misc->dated($bookedDate) . ' ' . $bookedTime . ' for assignment date ' . $assignDate . ' ' . $assignTime . '.</p>
            <p>Kindly verify at LSUK system.</p>
            <p>Thank you</p>';
                //php mailer used at top
                try {
                    $mail->SMTPDebug = 0;
                    $mail->isSMTP();
                    $mail->Host = setupEmail::EMAIL_HOST;
                    $mail->SMTPAuth   = true;
                    $mail->Username   = setupEmail::INFO_EMAIL;
                    $mail->Password   = setupEmail::INFO_PASSWORD;
                    $mail->SMTPSecure = setupEmail::SECURE_TYPE;
                    $mail->Port       = setupEmail::SENDING_PORT;
                    $mail->setFrom(setupEmail::INFO_EMAIL, setupEmail::FROM_NAME);
                    $mail->addAddress($to_add);
                    $mail->addReplyTo(setupEmail::INFO_EMAIL, setupEmail::FROM_NAME);
                    $mail->isHTML(true);
                    $mail->Subject = $subject;
                    $mail->Body    = $message;
                    if ($mail->send()) {
                        $mail->ClearAllRecipients();
                        $mail->addAddress(setupEmail::INFO_EMAIL);
                        $mail->addReplyTo(setupEmail::INFO_EMAIL, setupEmail::FROM_NAME);
                        $mail->isHTML(true);
                        $mail->Subject = 'Acknowledgement for new Face to Face Online Portal Job';
                        $mail->Body    = $ack_message;
                        $mail->send();
                        $mail->ClearAllRecipients();
                        //Invoice //
                        if ($jobStatus == 1) {
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
                        if ($jobDisp == '1' && $jobStatus == '1') {
                            if ($interp_cat == '12') {
                                $append_issue_bid = "<tr><td style='border: 1px solid yellowgreen;padding:5px;'>Assignment Category</td><td style='border: 1px solid yellowgreen;padding:5px;'>Other</td></tr><tr><td style='border: 1px solid yellowgreen;padding:5px;'>Assignment Details</td><td style='border: 1px solid yellowgreen;padding:5px;'>" . $assignIssue . "</td></tr>";
                            } else {
                                $append_issue_bid = "<tr><td style='border: 1px solid yellowgreen;padding:5px;'>Assignment Category</td><td style='border: 1px solid yellowgreen;padding:5px;'>" . $write_interp_cat . "</td></tr><tr><td style='border: 1px solid yellowgreen;padding:5px;'>Assignment Details</td><td style='border: 1px solid yellowgreen;padding:5px;'>" . $write_interp_type . "</td></tr>";
                            }
                            $append_table = "
                    <table>
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
                    <td style='border: 1px solid yellowgreen;padding:5px;'>" . $get_dur . "</td>
                    </tr>
                    <tr>
                    <td style='border: 1px solid yellowgreen;padding:5px;'>Assignment Location</td>
                    <td style='border: 1px solid yellowgreen;padding:5px;'>To be informed after successful allocation</td>
                    </tr>
                    " . $append_issue_bid . "
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
                            if ($gender == '' || $gender == 'No Preference') {
                                $put_gender = "";
                            } else {
                                $put_gender = "AND interpreter_reg.gender='$gender'";
                            }
                            if ($source == $target) {
                                $put_lang = "";
                                $query_style = '0';
                            } else if ($source != 'English' && $target != 'English') {
                                $put_lang = "";
                                $query_style = '1';
                            } else if ($source == 'English' && $target != 'English') {
                                $put_lang = "interp_lang.lang='$target' and interp_lang.level<3";
                                $query_style = '2';
                            } else if ($source != 'English' && $target == 'English') {
                                $put_lang = "interp_lang.lang='$source' and interp_lang.level<3";
                                $query_style = '2';
                            } else {
                                $put_lang = "";
                                $query_style = '3';
                            }
                            if ($query_style == '0') {
                                $query_emails = "SELECT DISTINCT interpreter_reg.name, interpreter_reg.email, interpreter_reg.id FROM interpreter_reg,interp_lang WHERE interpreter_reg.code=interp_lang.code AND (SELECT COUNT(DISTINCT interp_lang.lang) FROM interp_lang WHERE interp_lang.type='interp' AND interp_lang.lang IN ('" . $source . "') and interp_lang.level<3 and interp_lang.code=interpreter_reg.code)=1 and 
                                ((interpreter_reg.uk_citizen=1 AND interpreter_reg.id_doc_expiry_date != '1001-01-01' AND interpreter_reg.id_doc_expiry_date > CURRENT_DATE()) OR (interpreter_reg.uk_citizen=0 AND interpreter_reg.work_evid_expiry_date != '1001-01-01' AND interpreter_reg.work_evid_expiry_date > CURRENT_DATE())) 
                                            AND (interpreter_reg.is_dbs_auto=1 OR (interpreter_reg.is_dbs_auto=0 AND interpreter_reg.dbs_expiry_date != '1001-01-01' AND interpreter_reg.dbs_expiry_date > CURRENT_DATE())) and 
                                interpreter_reg.active='0' AND (interpreter_reg.actnow='Active' OR (interpreter_reg.actnow='Inactive' AND CURRENT_DATE() NOT BETWEEN interpreter_reg.actnow_time AND interpreter_reg.actnow_to)) AND interpreter_reg.interp='Yes' $dbs_checked_req AND interpreter_reg.city LIKE '$assignCity_req' $put_gender AND interpreter_reg.deleted_flag=0 AND interpreter_reg.subscribe=1 AND interpreter_reg.is_temp=0 AND interpreter_reg.isAdhoc=0";
                            } else if ($query_style == '1') {
                                $query_emails = "SELECT DISTINCT interpreter_reg.name, interpreter_reg.email, interpreter_reg.id FROM interpreter_reg,interp_lang WHERE interpreter_reg.code=interp_lang.code AND (SELECT COUNT(DISTINCT interp_lang.lang) FROM interp_lang WHERE interp_lang.type='interp' AND interp_lang.lang IN ('" . $source . "','" . $target . "') and interp_lang.level<3 and interp_lang.code=interpreter_reg.code)=2 and 
                                ((interpreter_reg.uk_citizen=1 AND interpreter_reg.id_doc_expiry_date != '1001-01-01' AND interpreter_reg.id_doc_expiry_date > CURRENT_DATE()) OR (interpreter_reg.uk_citizen=0 AND interpreter_reg.work_evid_expiry_date != '1001-01-01' AND interpreter_reg.work_evid_expiry_date > CURRENT_DATE())) 
                                            AND (interpreter_reg.is_dbs_auto=1 OR (interpreter_reg.is_dbs_auto=0 AND interpreter_reg.dbs_expiry_date != '1001-01-01' AND interpreter_reg.dbs_expiry_date > CURRENT_DATE())) and 
                                interpreter_reg.active='0' AND (interpreter_reg.actnow='Active' OR (interpreter_reg.actnow='Inactive' AND CURRENT_DATE() NOT BETWEEN interpreter_reg.actnow_time AND interpreter_reg.actnow_to)) AND interpreter_reg.interp='Yes' $dbs_checked_req AND interpreter_reg.city LIKE '$assignCity_req' $put_gender AND interpreter_reg.deleted_flag=0 AND interpreter_reg.subscribe=1 AND interpreter_reg.is_temp=0 AND interpreter_reg.isAdhoc=0";
                            } else if ($query_style == '2') {
                                $query_emails = "SELECT DISTINCT interpreter_reg.name, interpreter_reg.email, interpreter_reg.id FROM interpreter_reg,interp_lang WHERE interpreter_reg.code=interp_lang.code AND interp_lang.type='interp' AND $put_lang and 
                                ((interpreter_reg.uk_citizen=1 AND interpreter_reg.id_doc_expiry_date != '1001-01-01' AND interpreter_reg.id_doc_expiry_date > CURRENT_DATE()) OR (interpreter_reg.uk_citizen=0 AND interpreter_reg.work_evid_expiry_date != '1001-01-01' AND interpreter_reg.work_evid_expiry_date > CURRENT_DATE())) 
                                            AND (interpreter_reg.is_dbs_auto=1 OR (interpreter_reg.is_dbs_auto=0 AND interpreter_reg.dbs_expiry_date != '1001-01-01' AND interpreter_reg.dbs_expiry_date > CURRENT_DATE())) and 
                                interpreter_reg.active='0' AND (interpreter_reg.actnow='Active' OR (interpreter_reg.actnow='Inactive' AND CURRENT_DATE() NOT BETWEEN interpreter_reg.actnow_time AND interpreter_reg.actnow_to)) AND interpreter_reg.interp='Yes' $dbs_checked_req AND interpreter_reg.city LIKE '$assignCity_req' $put_gender AND interpreter_reg.deleted_flag=0 AND interpreter_reg.subscribe=1 AND interpreter_reg.is_temp=0 AND interpreter_reg.isAdhoc=0";
                            } else {
                                $query_emails = "SELECT DISTINCT interpreter_reg.name, interpreter_reg.email, interpreter_reg.id FROM interpreter_reg WHERE 
                                interpreter_reg.active='0' and ((interpreter_reg.uk_citizen=1 AND interpreter_reg.id_doc_expiry_date != '1001-01-01' AND interpreter_reg.id_doc_expiry_date > CURRENT_DATE()) OR (interpreter_reg.uk_citizen=0 AND interpreter_reg.work_evid_expiry_date != '1001-01-01' AND interpreter_reg.work_evid_expiry_date > CURRENT_DATE())) AND (interpreter_reg.is_dbs_auto=1 OR (interpreter_reg.is_dbs_auto=0 AND interpreter_reg.dbs_expiry_date != '1001-01-01' AND interpreter_reg.dbs_expiry_date > CURRENT_DATE())) AND (interpreter_reg.actnow='Active' OR (interpreter_reg.actnow='Inactive' AND CURRENT_DATE() NOT BETWEEN interpreter_reg.actnow_time AND interpreter_reg.actnow_to)) AND interpreter_reg.interp='Yes' $dbs_checked_req AND interpreter_reg.city LIKE '$assignCity_req' $put_gender AND interpreter_reg.deleted_flag=0 AND interpreter_reg.subscribe=1 AND interpreter_reg.is_temp=0 AND interpreter_reg.isAdhoc=0";
                            }
                            $res_emails = mysqli_query($con, $query_emails);
                            //Getting bidding email from em_format table
                            $row_format = $acttObj->read_specific("em_format", "email_format", "id=28");
                            $subject_int = "New Face To Face Project " . $edit_id;
                            $sub_title = "Face To Face job of " . $source . " language on " . $assignDate . " at " . $assignTime . " is available for you to bid.";
                            $type_key = "nj";
                            //$app_int_ids=array();
                            while ($row_emails = mysqli_fetch_assoc($res_emails)) {
                                if ($acttObj->read_specific("COUNT(*) as blacklisted", "interp_blacklist", "interpName='id-" . $row_emails['id'] . "' AND orgName='" . $orgName . "' AND deleted_flag=0 AND blocked_for=1")["blacklisted"] == 0) {
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
                                                $acttObj->notify($token, $subject_int, $sub_title, array("type_key" => $type_key, "job_type" => "Face To Face"));
                                            }
                                        }
                                    }
                                    //Replace date in email bidding
                                    $data   = ["[NAME]", "[ASSIGNTIME]", "[ASSIGNDATE]", "[POSTCODE]", "[TABLE]", "[EDIT_ID]"];
                                    $to_replace  = [$row_emails['name'], "$assignTime", "$assignDate", "$postCode", "$append_table", "$edit_id"];
                                    $message_int = str_replace($data, $to_replace, $row_format['em_format']);
                                    $mail->setFrom(setupEmail::INFO_EMAIL, setupEmail::FROM_NAME);
                                    $mail->addAddress($to_int_address);
                                    $mail->addReplyTo(setupEmail::INFO_EMAIL, setupEmail::FROM_NAME);
                                    $mail->isHTML(true);
                                    $mail->Subject = $subject_int;
                                    $mail->Body    = $message_int;
                                    $mail->send();
                                    $mail->ClearAllRecipients();
                                }
                            }
                        }
                        //Email to interpreters ends here
                        $acttObj->editFun($table, $edit_id, 'bookedVia', 'Online Portal');
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
            } else {
                $msg_error .= '<p style="font-size: 22px;" class="text-danger">Oops! A job with same information was already booked!</p>';
            }
        } //end of foreach
        $msg = $msg_booking . $msg_error;
    } //submission ends here
    $source = $row['source'];
    $target = $row['target'];
    $interp_cat = $row['interp_cat'];
    $interp_type = $row['interp_type'];
    $assignDate = $row['assignDate'];
    $assignTime = $row['assignTime'];
    $assignDur = $row['assignDur'];
    $nameRef = $row['nameRef'];
    $buildingName = $row['buildingName'];
    $line1 = $row['line1'];
    $line2 = $row['line2'];
    $street = $row['street'];
    $assignCity = $row['assignCity'];
    $postCode = $row['postCode'];
    $inchPerson = $row['inchPerson'];
    $inchContact = $row['inchContact'];
    $inchEmail = $row['inchEmail'];
    $inchEmail2 = $row['inchEmail2'];
    $inchNo = $row['inchNo'];
    $inchRoad = $row['inchRoad'];
    $inchCity = $row['inchCity'];
    $inchPcode = $row['inchPcode'];
    $orgName = $row['orgName'];
    $orgRef = $row['orgRef'];
    $orgContact = $row['orgContact'];
    $remrks = $row['remrks'];
    $gender = $row['gender'];
    $intrpName = $row['intrpName'];
    $jobStatus = $row['jobStatus'];
    $bookinType = $row['bookinType'];
    $I_Comments = $row['I_Comments'];
    $snote = $row['snote'];
    $jobDisp = $row['jobDisp'];
    $invoiceNo = $row['invoiceNo'];
    $bookedVia = $row['bookedVia'];
    $assignIssue = $row['assignIssue'];
    $dbs_checked = $row['dbs_checked'];
    $noty = $row['noty'];
    $noty_reason = $row['noty_reason'];
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
                <h1>Place an Order (Face to Face Interpreter)</h1>
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
                        <div class="stepwizard-step col-md-3 col-xs-4">
                            <a href="#step-1" type="button" class="btn btn-primary btn-circle">1</a>
                            <p><small>Assignment Details</small></p>
                        </div>
                        <div class="stepwizard-step col-md-2 col-xs-4">
                            <a href="#step-2" type="button" class="btn btn-default btn-circle disabled" disabled="disabled">2</a>
                            <p><small>Assignment Reference</small></p>
                        </div>
                        <div class="stepwizard-step col-md-2 col-xs-4">
                            <a href="#step-3" type="button" class="btn btn-default btn-circle disabled" disabled="disabled">3</a>
                            <p><small>Assignment Location</small></p>
                        </div>
                        <div class="stepwizard-step col-md-2 col-xs-4">
                            <a href="#step-4" type="button" class="btn btn-default btn-circle disabled" disabled="disabled">4</a>
                            <p><small>Booking Details</small></p>
                        </div>
                        <div class="stepwizard-step col-md-2 col-xs-4">
                            <a href="#step-5" type="button" class="btn btn-default btn-circle disabled" disabled="disabled">5</a>
                            <p><small>Interpreter Preferences</small></p>
                        </div>
                    </div>
                </div>

                <form class="sky-form" action="" method="post">
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
                                    <td style="padding-top: 18px;" align="center"><?php echo $row_selected['buildingName'] . ' ' . $row_selected['line1'] . ' ' . $row_selected['line2'] . ' ' . $row_selected['streetRoad'] . ' ' . $row_selected['postCode'] . ' ' . $row_selected['city']; ?></td>
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
                                <br><button onclick="set_language()" class="btn btn-info btn_add_language" type="button" style="margin-top: 5px;">Add More Languages</button>
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
                                <label class="control-label">Select Assignment Category</label>
                                <select name="interp_cat" id="interp_cat" class="form-control" onchange="get_interp_type($(this));" required>
                                    <?php $q_interp_cat = $acttObj->read_all("ic_id,ic_title", "interp_cat", "ic_status=1 ORDER BY ic_title ASC");
                                    $opt_ic = "";
                                    while ($row_ic = $q_interp_cat->fetch_assoc()) {
                                        $ic_id = $row_ic["ic_id"];
                                        $ic_title = $row_ic["ic_title"];
                                        $opt_ic .= "<option value='$ic_id'>" . $ic_title . "</option>";
                                    } ?>
                                    <?php if (isset($interp_cat)) { ?>
                                        <option selected value="<?php echo $interp_cat; ?>"><?php echo $acttObj->read_specific("ic_title", "interp_cat", "ic_id=" . $interp_cat)['ic_title']; ?></option>
                                    <?php } ?>
                                    <option disabled value="">Select Assignment Category</option>
                                    <?php echo $opt_ic; ?>
                                </select>
                            </div>
                            <div class="form-group col-md-4 col-sm-6" id="div_it" <?php if ($interp_cat == '12') {
                                                                                        echo "style='display:none;'";
                                                                                    } ?>>
                                <label class="control-label">Select Assignment Type(s)</label>
                                <select name="interp_type[]" multiple="multiple" id="interp_type" class="form-control multi_class" <?php if ($interp_cat != '12') {
                                                                                                                                        echo "required";
                                                                                                                                    } ?>>
                                    <?php $q_it = $acttObj->read_all('it_id,it_title', 'interp_types', "ic_id='$interp_cat' AND it_id NOT IN ($interp_type) ORDER BY it_title ASC");
                                    $arr_interp_type = explode(',', $interp_type);
                                    for ($it_i = 0; $it_i < count($arr_interp_type); $it_i++) {
                                        $option_it .= "<option selected value='$arr_interp_type[$it_i]'>" . $acttObj->read_specific("it_title", "interp_types", "it_id=" . $arr_interp_type[$it_i])['it_title'] . "</option>";
                                    }
                                    echo $option_it;
                                    while ($row_it = $q_it->fetch_assoc()) {
                                        echo '<option value="' . $row_it['it_id'] . '">' . $row_it['it_title'] . '</option>';
                                    } ?>
                                </select>
                            </div>
                            <div class="form-group col-md-8 col-sm-6" id="div_assignIssue" <?php if ($interp_cat != '12') {
                                                                                                echo "style='display:none;'";
                                                                                            } ?>>
                                <textarea title="Assignment Issue" placeholder="Write Assignment Issue Here ..." name="assignIssue" class="form-control" id="assignIssue" style="height:60px;"><?php echo $assignIssue; ?></textarea>
                            </div>
                            <div class="form-group col-md-4 col-sm-6">
                                <label class="control-label input2">Assignment Date*</label>
                                <input class="form-control date_picker" type="date" id="assignDate" name="assignDate" required='' value='<?php echo $assignDate; ?>' />
                            </div>
                            <script type="text/javascript">
                                function dur_finder() {
                                    var datetime = $('#assignDate').val() + ' ' + $('#assignTime').val();
                                    var duration = $('#assignDur').val();
                                    $.ajax({
                                        url: 'ajax_client_portal.php',
                                        method: 'post',
                                        data: {
                                            'datetime': datetime,
                                            'duration': duration,
                                            val: 'dur_finder'
                                        },
                                        success: function(data) {
                                            $('#assignEndTime').val(data);
                                        },
                                        error: function(xhr) {
                                            alert("An error occured: " + xhr.status + " " + xhr.statusText);
                                        }
                                    });
                                }
                            </script>
                            <div class="form-group col-md-4 col-sm-6">
                                <label class="control-label">Assignment Time *</label>
                                <input onkeyup="dur_finder();" name="assignTime" id="assignTime" type="time" step="300" class="form-control time_picker" required='' value='<?php echo $assignTime; ?>' />
                            </div>
                            <?php
                            function SetValueAsTime($data)
                            {
                                if (!isset($data))
                                    return "";
                                $mins = $data % 60;
                                $hours = $data / 60;
                                $data = sprintf("%02d:%02d", $hours, $mins);
                                return $data;
                            }
                            $input_time = date($assignDate . ' ' . $assignTime);
                            $newTime = date("m/d/Y H:i", strtotime("+$assignDur minutes", strtotime($input_time))); ?>
                            <div class="form-group col-md-4 col-sm-6">
                                <label class="control-label">Assignment Duration * (Hours:Minutes)</label>
                                <input id="assignDur" onkeyup="dur_finder();" name="assignDur" type="text" pattern="[0-9 :]{5}" maxlength="5" class="form-control" value="<?php echo isset($assignDur) ? SetValueAsTime($assignDur) : ''; ?>" required='' placeholder="Hours : Minutes" />
                            </div>
                            <div class="form-group col-md-4 col-sm-6">
                                <label class="control-label">Assignment End Time</label>
                                <input id="assignEndTime" readonly="readonly" name="assignEndTime" type="text" class="form-control" value="<?php echo $newTime; ?>" />
                            </div>
                            <script>
                                $('#assignDur').keyup(function() {
                                    var cctlength = $(this).val().length; // get character length
                                    switch (cctlength) {
                                        case 2:
                                            var cctVal = $(this).val();
                                            var cctNewVal = cctVal + ':';
                                            $(this).val(cctNewVal);
                                            break;
                                        case 5:
                                            break;
                                        default:
                                            break;
                                    }
                                });
                                $("#assignDur").bind('keypress paste', function(e) {
                                    var regex = new RegExp(/[0-9]/);
                                    var str = String.fromCharCode(!e.charCode ? e.which : e.charCode);
                                    if (!regex.test(str)) {
                                        e.preventDefault();
                                        return false;
                                    }
                                });
                            </script>
                            <div class="form-group col-md-12">
                                <button class="btn btn-primary nextBtn pull-right" type="button">Next <i class="fa fa-angle-right"></i><i class="fa fa-angle-right"></i></button>
                            </div>
                        </div>
                    </div>

                    <div class="panel panel-primary setup-content" id="step-2">
                        <div class="panel-heading">
                            <h3 class="panel-title">Assignment Reference</h3>
                        </div>
                        <div class="panel-body">
                            <div class="form-group col-md-4 col-sm-6 search-box">
                                <label class="control-label input">Your Reference <i class="fa fa-question-circle" title="(Name, Initials or File Ref. Number)"></i></label>
                                <input class="form-control" name="orgRef" id="orgRef" type="text" required='' autocomplete="off" placeholder="Type your reference" value="<?php echo $orgRef; ?>" />
                                <i id="confirm_value" style="display:none;position: absolute;right: 25px;top: 35px;cursor:pointer;" onclick="$(this).hide();$('.result').empty();" class="glyphicon glyphicon-ok-sign text-success" title="Confirm this reference"></i>
                                <div class="result"></div>
                            </div>
                            <div class="form-group col-md-3 col-sm-6" title="System generated ID by LSUK">
                                <label class="input">Booking Reference (LSUK)</label>
                                <input class="form-control" name="nameRef" type="text" required='' readonly="readonly" value="<?php echo $nameRef; ?>" />
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
                            <div class="form-group col-md-3 col-sm-6 <?php if (($po_req == 0) || ($po_req == 1 && empty($porder))) {
                                                                            echo 'hidden';
                                                                        } ?> search-box" id="div_po_number">
                                <label class="optional">Enter purchase order number</label>
                                <input name="purchase_order_number" id="purchase_order_number" type="text" class="form-control" autocomplete="off" placeholder="Search purchase order number" <?php if ($po_req == 1 && !empty($porder)) { ?> value="<?php echo $porder; ?>" <?php } ?> />
                                <i id="confirm_value" style="position: absolute; right: 25px; top: 35px; display: block;cursor:pointer;" onclick="$(this).hide();$(this).next('.result').empty();" class="glyphicon glyphicon-ok-sign text-success confirm_element" title="Confirm this purchase order number"></i>
                                <div class="result"></div>
                            </div>
                            <div id="div_po_req" class="form-group <?php if (($po_req == 0) || ($po_req == 1 && !empty($porder))) {
                                                                        echo 'hidden';
                                                                    } ?> col-md-3 col-sm-6">
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
                            <h3 class="panel-title">Assignment Location</h3>
                        </div>
                        <div class="panel-body">
                            <div class="form-group col-md-4">
                                <label class="control-label optional">Post Code</label>
                                <div class="input-group">
                                    <input id="postCode" class="form-control" name="postCode" type="text" placeholder="Search Post Code" class="form-control" value="<?php echo $postCode ?>">
                                    <div class="input-group-btn">
                                        <button onclick="return PostCodeChanged();" class="btn btn-info">Look Up</button>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group col-md-4">
                                <label class="control-label input">Building No / Name</label>
                                <div class="input-group">
                                    <input placeholder="Building No / Name" id="buildingName" name="buildingName" class="form-control" readonly="readonly" type="text" value="<?php echo $buildingName ?>">
                                    <div class="input-group-btn">
                                        <button onclick="EditStreet();" type="button" class="btn btn-info">Edit Street</button>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group col-md-4">
                                <label class="control-label select">City / Town <i class="fa fa-question-circle" title="Please Select From The List Below"></i></label>
                                <select class="form-control" id="assignCity" name="assignCity" required>
                                    <?php if (!empty($assignCity)) { ?>
                                        <option><?php echo $assignCity; ?></option>
                                    <?php } else { ?>
                                        <option value="">--Select City--</option>
                                    <?php } ?>
                                    <?php include 'lsuk_system/assigncityselect.php'; ?>
                            </div>
                            <div class="form-group col-md-8">
                                <label class="control-label input">Street / Road and Area</label>
                                <input type="text" class="form-control" id="street" name="street" value="<?php echo $street ?>" />
                            </div>
                            <div class="form-group col-md-12">
                                <button class="btn btn-primary nextBtn pull-right" type="button">Next <i class="fa fa-angle-right"></i><i class="fa fa-angle-right"></i></button>
                            </div>
                        </div>
                    </div>

                    <div class="panel panel-primary setup-content" id="step-4">
                        <div class="panel-heading">
                            <h3 class="panel-title">Booking Details</h3>
                        </div>
                        <div class="panel-body">
                            <div class="form-group col-md-4">
                                <label class="control-label input">Booking Person Name</label>
                                <input class="long form-control" name="inchPerson" type="text" value="<?php echo $row_selected['contactPerson']; ?>" />
                            </div>
                            <div class="form-group col-md-4">
                                <label class="control-label input">Contact Number</label>
                                <input name="inchContact" id="inchContact" type="text" class="long form-control" value="<?php echo $row_selected['contactNo1']; ?>" />
                            </div>
                            <div class="form-group col-md-4">
                                <label class="control-label input">Interpreter Contact Name&nbsp;* <i class="fa fa-question-circle" title="Assignment in-Charge"></i></label>
                                <input class="form-control" name="orgContact" id="orgContact" type="text" placeholder='' required='' />
                            </div>
                            <div class="form-group col-md-4">
                                <label class="control-label input">Email Address (For Booking Confirmation)</label>
                                <input name="inchEmail" id="inchEmail" type="email" class="long form-control" value="<?php echo $row_selected['email']; ?>" placeholder='' required />
                            </div>
                            <div class="form-group col-md-4">
                                <label class="control-label input">Booking Date&nbsp;*</label>
                                <input onchange="OnDateChgAjax();" type="date" name="bookeddate" id="bookeddate" required placeholder='Booked Date' class="form-control date_picker" value="<?php echo @$bookedDate ?>" />
                            </div>
                            <div class="form-group col-md-4">
                                <label class="control-label input">Booking Time&nbsp;*</label>
                                <input onchange="OnTimeChgAjax();" type="time" name="bookedtime" id="bookedtime" required placeholder='Booked Time' step="300" class="form-control time_picker2" value="<?php echo @$bookedTime ?>" />
                            </div>
                            <div class="form-group col-md-4 hidden">
                                <label class="input">Building Number / Name (Business Name)</label>
                                <input class="" name="inchNo" id="inchNo" type="text" value="<?php echo @$inchNo; ?>" placeholder='' readonly />
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
                                <input class="" name="inchCity" id="inchCity" type="text" value="<?php echo @$inchCity; ?>" placeholder='' readonly />
                            </div>
                            <div class="form-group col-md-4 hidden">
                                <label class="input">Post Code</label>
                                <input class="" name="inchPcode" id="inchPcode" type="text" value="<?php echo @$inchPcode; ?>" readonly />
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
                                <label class="control-label optional">DBS Checked Interpreter Required?</label><br>
                                <div class="radio-inline ri"><label><input name="dbs_checked" type="radio" value="0" <?php if ($dbs_checked == '0') { ?> checked="checked" <?php } ?> />
                                        <span class="label label-primary">Yes <i class="fa fa-check-circle"></i></span></label></div>
                                <div class="radio-inline ri"><label><input type="radio" name="dbs_checked" value="1" <?php if ($dbs_checked == '1') { ?> checked="checked" <?php } ?> />
                                        <span class="label label-info">No <i class="fa fa-remove"></i></span></label></div>
                            </div>
                            <div class="form-group col-sm-7">
                                <label class="control-label optional">Interpreter Gender:</label><br>
                                <div class="radio-inline ri"><label><input name="gender" type="radio" value="Male" <?php if ($gender == 'Male') { ?> checked="checked" <?php } ?> />
                                        <span class="label label-primary">Male</span></label></div>
                                <div class="radio-inline ri"><label><input type="radio" name="gender" value="Female" <?php if ($gender == 'Female') { ?> checked="checked" <?php } ?> />
                                        <span class="label label-info">Female</span></label></div>
                                <div class="radio-inline ri"><label><input type="radio" name="gender" value="No Preference" <?php if ($gender == 'No Preference') { ?> checked="checked" <?php } ?> />
                                        <span class="label label-default">No Preference</span></label></div>
                            </div>

                            <div class="form-group col-sm-5">
                                <label class="control-label optional">Booking Status:</label><br>
                                <div class="radio-inline ri"><label><input type="radio" name="jobStatus" value="1" <?php if ($jobStatus == '1') { ?> checked="checked" <?php } ?> />
                                        <span class="label label-primary">Confirmed <i class="fa fa-check-circle"></i></span></label></div>
                                <div class="radio-inline ri"><label><input name="jobStatus" type="radio" value="0" <?php if ($jobStatus == '0') { ?> checked="checked" <?php } ?> />
                                        <span class="label label-info">Enquiry <i class="fa fa-question"></i></span></label></div>
                            </div>
                            <div class="form-group col-sm-7">
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
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.13/css/bootstrap-multiselect.css" rel="stylesheet" type="text/css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.13/js/bootstrap-multiselect.js" type="text/javascript"></script>
    <script type="text/javascript">
        function get_interp_type(elem) {
            var ic_id = elem.val();
            $.ajax({
                url: 'ajax_client_portal.php',
                method: 'post',
                data: {
                    ic_id: ic_id
                },
                success: function(data) {
                    if (data) {
                        $('#div_it').css('display', 'block');
                        $('#div_assignIssue').css('display', 'none');
                        $('#assignIssue').css('display', 'none');
                        $('#div_it').html(data);
                    } else {
                        $('#div_it').html(data);
                        $('#div_it').css('display', 'none');
                        $('#div_assignIssue').css('display', 'block');
                        $('#assignIssue').css('display', 'block');
                    }
                    $(function() {
                        $('.multi_class').multiselect({
                            includeSelectAllOption: true,
                            numberDisplayed: 1,
                            enableFiltering: true,
                            enableCaseInsensitiveFiltering: true
                        });
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
</body>
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