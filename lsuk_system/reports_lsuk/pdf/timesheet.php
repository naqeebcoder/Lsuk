<?php if (session_id() == '' || !isset($_SESSION)) {
    session_start();
}
//php mailer library
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
require '../../phpmailer/vendor/autoload.php';
$mail = new PHPMailer(true);
if (isset($_SESSION['web_UserName']) && !empty($_SESSION['web_UserName'])) {
    $_SESSION['UserName'] = $_SESSION['web_UserName'];
}
include '../../db.php';
include_once '../../class.php';
$from_name = "LSUK";
$from_add = 'payroll@lsuk.org';
$to_lsuk = 'imran.lsukltd@gmail.com';
$update_id = @$_GET['update_id'];
$table = $_GET['table'];
$down = @$_GET['down'];
$emailto = @$_GET['emailto'];
$append_table = "";
$heading = "";
$timesheet_data = "";
$signature = "";
$red_line = "";
$query = "SELECT $table.*, interpreter_reg.name, comp_reg.name as orgName FROM $table
	   inner join interpreter_reg on $table.intrpName = interpreter_reg.id
	   inner join comp_reg on $table.orgName = comp_reg.abrv
	   where $table.id=$update_id";
$result = mysqli_query($con, $query);
$row = mysqli_fetch_assoc($result);
if ((isset($_SESSION['web_userId']) && $row['intrpName'] == $_SESSION['web_userId']) || isset($_SESSION['userId'])) {
    $source = $row['source'];
    $target = $row['target'];
    $assignIssue = $row['assignIssue'];
    $orgRef = $row['orgRef'];
    $orgName = $row['orgName'];
    $nameRef = $row['nameRef'];
    $name = $row['name'];
    $orgContact = $row['orgContact'];
    $remrks = $row['remrks'] ?: '';
    $inchPerson = $row['inchPerson'];
    // if($table=='translation'){
    //     $signature='<div style="font-size:12px"><span style="font-weight:bold;color:#066;">Signature of the interpreter:</span><u><i>'.$name.'</i></u> <span style="font-weight:bold;color:#066;">Date:</span><span style="font-size:7px">______________________________________________________</span></div>';
    // }else{
    $signature = '<div style="font-size:12px"><span style="font-weight:bold;color:#066;">Signature of the interpreter:</span><span style="font-size:7px">____________________________________________________________</span> <span style="font-weight:bold;color:#066;">Date:</span><span style="font-size:7px">______________________________________________________</span></div>';
    //}
    if ($table == 'interpreter') {
        $heading = "Interpreter Timesheet";
        $assignDate = $misc->dated($row['assignDate']);
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
            list($partdur1, $partdur2) = explode(':', $guess_dur);
            $total_guess_dur = $partdur1 * 60 + $partdur2;
            if ($total_guess_dur > 60) {
                $guess_hours = $total_guess_dur / 60;
                if (floor($guess_hours) > 1) {
                    $guess_hr = "hours";
                } else {
                    $guess_hr = "hour";
                }
                $guess_mins = $total_guess_dur % 60;
                if ($guess_mins == 0) {
                    $get_guess_dur = sprintf("%2d $guess_hr", $guess_hours);
                } else {
                    $get_guess_dur = sprintf("%2d $guess_hr %02d minutes", $guess_hours, $guess_mins);
                }
            } else if ($total_guess_dur == 60) {
                $get_guess_dur = "1 Hour";
            } else {
                $get_guess_dur = $total_guess_dur . " minutes";
            }
        }
        $assignTime = $row['assignTime'];
        $buildingName = $row['buildingName'];
        $street = $row['street'];
        $assignCity = $row['assignCity'];
        $postCode = $row['postCode'];
        $interp_cat = $row['interp_cat'];
        $interp_type = $row['interp_type'];
        $write_interp_cat = $interp_cat == '12' ? $assignIssue : $acttObj->read_specific("ic_title", "interp_cat", "ic_id=" . $interp_cat)['ic_title'];
        $write_interp_type = $interp_cat == '12' ? '' : $acttObj->read_specific("GROUP_CONCAT(CONCAT(it_title)  SEPARATOR ' <b> & </b> ') as it_title", "interp_types", "it_id IN (" . $interp_type . ")")['it_title'];
        if ($interp_cat == '12') {
            $append_issue = "<tr><td style='border: 1px solid yellowgreen;padding:5px;'>Assignment Category</td><td style='border: 1px solid yellowgreen;padding:5px;'>Other</td></tr><tr><td style='border: 1px solid yellowgreen;padding:5px;'>Assignment Details</td><td style='border: 1px solid yellowgreen;padding:5px;'>" . $assignIssue . "</td></tr>";
        } else {
            $append_issue = "<tr><td style='border: 1px solid yellowgreen;padding:5px;'>Assignment Category</td><td style='border: 1px solid yellowgreen;padding:5px;'>" . $write_interp_cat . "</td></tr><tr><td style='border: 1px solid yellowgreen;padding:5px;'>Assignment Details</td><td style='border: 1px solid yellowgreen;padding:5px;'>" . $write_interp_type . "</td></tr>";
        }
        $append_table = "<table>
            <tr>
            <td style='border: 1px solid yellowgreen;padding:5px;'>Source Language</td>
            <td style='border: 1px solid yellowgreen;padding:5px;'>{$source}</td>
            </tr>
            <tr>
            <td style='border: 1px solid yellowgreen;padding:5px;'>Target Language</td>
            <td style='border: 1px solid yellowgreen;padding:5px;'>{$target}</td>
            </tr>
            <tr>
            <td style='border: 1px solid yellowgreen;padding:5px;'>Assignment Date</td>
            <td style='border: 1px solid yellowgreen;padding:5px;'>{$assignDate}</td>
            </tr>
            <tr>
            <td style='border: 1px solid yellowgreen;padding:5px;'>Assignment Time</td>
            <td style='border: 1px solid yellowgreen;padding:5px;'>{$assignTime}</td>
            </tr>
            <tr>
            <td style='border: 1px solid yellowgreen;padding:5px;'>Assignment Duration</td>
            <td style='border: 1px solid yellowgreen;padding:5px;'>{$assignDur}</td>
            </tr>
            <tr>
            <td style='border: 1px solid yellowgreen;padding:5px;'>Assignment Location</td>
            <td style='border: 1px solid yellowgreen;padding:5px;'>{$buildingName}{$street}{$assignCity}{$postCode}</td>
            </tr>
            {$append_issue}
            <tr>
            <td style='border: 1px solid yellowgreen;padding:5px;'>Report to</td>
            <td style='border: 1px solid yellowgreen;padding:5px;'>{$inchPerson}</td>
            </tr>
            <tr>
            <td style='border: 1px solid yellowgreen;padding:5px;'>Case Worker or Person Incharge</td>
            <td style='border: 1px solid yellowgreen;padding:5px;'>{$orgContact}</td>
            </tr>
            <tr>
            <td style='border: 1px solid yellowgreen;padding:5px;'>Client Name</td>
            <td style='border: 1px solid yellowgreen;padding:5px;'>{$orgRef}</td>
            </tr>
            </table>";
        if ($db_assignDur != $guess_dur) {
            $append_table .= "<br><u><b>NOTES FOR THIS JOB:</b></u><br>
                This session is booked for {$assignDur}, however it can take  up to {$get_guess_dur} or longer.<br>
                Therefore please consider your unrestricted availability before bidding / accepting this job.
                In cases of short notice cancellation, you will be paid the booked time ({$assignDur}).<br>";
            if (!empty($remrks)) {
                $append_table .= "{$remrks}<br>";
            }
        } else {
            if (!empty($remrks)) {
                $append_table .= "<br><u><b>NOTES FOR THIS JOB:</b></u><br>{$remrks}<br>";
            }
        }
        $orgContact = $row['orgContact'];
        $timesheet_data = '<div style="font-size:12px"><span style="font-weight:bold;color:#066;">Linguist Name:</span><u>&nbsp;&nbsp;&nbsp;' . $name . '&nbsp;&nbsp;</u><span style="color:#FFF; font-size:7px">_________</span><span style="font-weight:bold;color:#066">Language:</span><u>&nbsp;&nbsp;&nbsp;' . $source . '&nbsp;&nbsp;</u><span style="color:#FFF; font-size:7px">_________</span><span style="font-weight:bold;color:#066">Case Worker:</span><u>&nbsp;&nbsp;&nbsp;' . $orgContact . '&nbsp;&nbsp;</u>&nbsp;</div>
                <div style="font-size:12px"><span style="font-weight:bold;color:#066;">Client Name or Ref:</span> <u>' . $orgRef . ' </u> <span style="color:#FFF; font-size:7px"></span><span style="font-weight:bold;color:#066;"> Date: </span><u>' . $assignDate . '</u><span style="color:#FFF; font-size:7px">_</span>  <span style="font-weight:bold;color:#066;"> Time:</span> <u>' . $assignTime . '</u><span style="font-weight:bold;color:#000;font-size:7px"> PM/AM</span> <span style="font-size:7px">________</span></div>
                <div style="font-size:12px"><span style="font-weight:bold;color:#066;">Place of Assignment: </span><u> ' . $buildingName . '  ' . $street . '  ' . $assignCity . '  ' . $postCode . '</u>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span style="font-weight:bold;color:#066;"> Duration</span> <span style="font-size:10px"><u>' . $assignDur . '</u></span></div>
                <div style="font-size:12px"><span style="font-weight:bold;color:#066;">Start Time:</span><span style="font-size:7px">________________________ AM/PM </span><span style="font-weight:bold;color:#066;"> Finish Time: </span><span style="font-size:7px">_________________________ AM/PM </span> <span style="font-weight:bold;color:#066;"> Total Duration:</span><span style="font-size:7px">______________________________</span><span style="font-weight:bold;color:#000;font-size:7px"> Hours</span></div>
                <div style="font-size:12px"><span style="font-weight:bold;color:#066;">Travel Duration (if Applicable):Hours</span><span style="font-size:7px">__________________</span> <span style="font-weight:bold;color:#066;">Minutes:</span><span style="font-size:7px">__________________</span></div>
                <div style="font-size:12px"><span style="font-weight:bold;color:#066;">Travel Mileage (If Applicable) :</span><span style="font-size:7px">_________________Miles</span> <span style="font-weight:bold;color:#066;"> Miles Other Expenses (Parking, Bridge Toll) £:</span><span style="font-size:7px">_______________________ </span></div>
                <div style="font-size:12px"><span style="font-weight:bold;color:#066;">Travel Costs (If Applicable):</span><span style="font-size:7px">________________________________________________________</span><span style="font-weight:bold;color:#066;">Please Attach Receipts:</span><span style="font-size:7px">______________________ Yes/No</span></div>
            <div>
            <h2 align="center">Client Feedback</h2>
            <style>
                table, td, th {
                border: 1px solid black;
                }
                table {
                border-collapse: collapse;
                width: 100%;
                }
            </style>
            <table cellpadding="10">
                <tbody>
                    <tr>
                    <th></th><th>Poor</th><th>Good</th><th>Excellent</th>
                    </tr>
                    <tr>
                    <th>Professionalism</th><td></td><td></td><td></td>
                    </tr>
                    <tr>
                    <th>Impartiality</th><td></td><td></td><td></td>
                    </tr>
                    <tr>
                    <th>Appearance</th><td></td><td></td><td></td>
                    </tr>
                    <tr>
                    <th>Punctuality</th><td>Late? Yes / No</td><td colspan="2">Minutes?</td>
                    </tr>
                    <tr>
                    <th>Comments</th><td colspan="3"></td>
                    </tr>
                </tbody>
            </table>
            <br>
            <div style="border:1px solid #C30">
                <div style="font-size:12px"><span style="color:#066;"> Company Name: </span> <u>' . $orgName . ' </u></div>
                <div style="font-size:12px"><span style="color:#066;"> Assignment In charge (Case Worker / Fee Earner):</span><span style="font-size:7px">______________________________________________________________________________________</span></div>
                <div style="font-size:12px"><span style="color:#066;"> Signature:</span><span style="font-size:7px">__________________________________________________________________________</span> <span style="color:#066;">Date:</span><span style="font-size:7px">___________________________________________________________</span></div>
                <div style="font-size:12px"><span style="color:#066;"> Future Booking Date (If Known):</span><span style="font-size:7px"></span><span style="font-weight:bold;color:#066;"> Date:</span><span style="font-size:7px">_______________________________________________</span> <span style="font-weight:bold;color:#066;">Time:</span><span style="font-size:7px">_______________________________________________</span></div>
                <div style="font-size:12px;font-weight:bold;"><span style="color:#066;"> Location (if different):</span><span style="font-weight:normal;font-size:7px">____________________________________________________________________________________________________________________________</span></div>
            </div>
            <div style="font-size:12px"><span style="font-weight:bold;color:#066;">Interpreter! Can you do the future session?</span><span style="font-size:7px">_______________________________________________________________________________________</span><span style="font-weight:normal;">Yes / No</span></div>';
        $red_line = "Please return this form to Language Services UK Limited on the day of the assignment";
        $timesheet_body = $acttObj->read_specific("em_format", "email_format", "id=2")['em_format'];
        $data   = ["[NAME]", "[SOURCE]", "[BUILDINGNAME]", "[STREET]", "[ASSIGNCITY]", "[POSTCODE]", "[ASSIGNDATE]", "[ASSIGNTIME]", "[APPENDTABLE]"];
        $to_replace  = ["$name", "$source", "$buildingName", "$street", "$assignCity", "$postCode", "$assignDate", "$assignTime", "$append_table"];
    } else if ($table == 'telephone') {
        $heading = "Telephone Interpreting Timesheet";
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
        $assignDate = $misc->dated($row['assignDate']);
        $assignTime = $row['assignTime'];
        $telep_cat = $row['telep_cat'];
        $telep_type = $row['telep_type'];
        $write_telep_cat = $telep_cat == '11' ? $assignIssue : $acttObj->read_specific("tpc_title", "telep_cat", "tpc_id=" . $telep_cat)['tpc_title'];
        $write_telep_type = $telep_cat == '11' ? '' : $acttObj->read_specific("GROUP_CONCAT(CONCAT(tpt_title)  SEPARATOR ' <b> & </b> ') as tpt_title", "telep_types", "tpt_id IN (" . $telep_type . ")")['tpt_title'];
        if ($telep_cat == '11') {
            $append_issue = "<tr><td style='border: 1px solid yellowgreen;padding:5px;'>Assignment Category</td><td style='border: 1px solid yellowgreen;padding:5px;'>Other</td></tr><tr><td style='border: 1px solid yellowgreen;padding:5px;'>Assignment Details</td><td style='border: 1px solid yellowgreen;padding:5px;'>" . $assignIssue . "</td></tr>";
        } else {
            $append_issue = "<tr><td style='border: 1px solid yellowgreen;padding:5px;'>Assignment Category</td><td style='border: 1px solid yellowgreen;padding:5px;'>" . $write_telep_cat . "</td></tr><tr><td style='border: 1px solid yellowgreen;padding:5px;'>Assignment Details</td><td style='border: 1px solid yellowgreen;padding:5px;'>" . $write_telep_type . "</td></tr>";
        }
        $comunic = $acttObj->read_specific("c_title","comunic_types","c_id=".$row['comunic'])['c_title'];
        // $channel_img = file_exists('../../images/comunic_types/' . $get_channel['c_image']) ? '<img src="../../images/comunic_types/' . $get_channel['c_image'] . '" width="36"/> ' : '';
        $append_table = "<table>
            <tr>
            <td style='border: 1px solid yellowgreen;padding:5px;'>Communication Type</td>
            <td style='border: 1px solid yellowgreen;padding:5px;'>{$comunic}</td>
            </tr>
            <tr>
            <td style='border: 1px solid yellowgreen;padding:5px;'>Source Language</td>
            <td style='border: 1px solid yellowgreen;padding:5px;'>{$source}</td>
            </tr>
            <tr>
            <td style='border: 1px solid yellowgreen;padding:5px;'>Target Language</td>
            <td style='border: 1px solid yellowgreen;padding:5px;'>{$target}</td>
            </tr>
            <tr>
            <td style='border: 1px solid yellowgreen;padding:5px;'>Assignment Date</td>
            <td style='border: 1px solid yellowgreen;padding:5px;'>{$assignDate}</td>
            <tr>
            <td style='border: 1px solid yellowgreen;padding:5px;'>Assignment Time</td>
            <td style='border: 1px solid yellowgreen;padding:5px;'>{$assignTime}</td>
            </tr>
            <tr>
            <td style='border: 1px solid yellowgreen;padding:5px;'>Assignment Duration</td>
            <td style='border: 1px solid yellowgreen;padding:5px;'>{$assignDur}</td>
            </tr>
            {$append_issue}
            <tr>
            <td style='border: 1px solid yellowgreen;padding:5px;'>Report to</td>
            <td style='border: 1px solid yellowgreen;padding:5px;'>{$inchPerson}</td>
            </tr>
            <tr>
            <td style='border: 1px solid yellowgreen;padding:5px;'>Case Worker</td>
            <td style='border: 1px solid yellowgreen;padding:5px;'>{$orgContact}</td>
            </tr>
            </table>";
        if ($db_assignDur != $guess_dur) {
            $append_table .= "<br><u><b>NOTES FOR THIS JOB:</b></u><br>
                This session is booked for {$assignDur}, however it can take  up to {$get_guess_dur} or longer.<br>
                Therefore please consider your unrestricted availability before bidding / accepting this job.
                In cases of short notice cancellation, you will be paid the booked time ({$assignDur}).<br>";
            if (!empty($remrks)) {
                $append_table .= "{$remrks}<br>";
            }
        } else {
            if (!empty($remrks)) {
                $append_table .= "<br><u><b>NOTES FOR THIS JOB:</b></u><br>{$remrks}<br>";
            }
        }
        $timesheet_data = '<div style="font-size:12px"><span style="font-weight:bold;color:#066;">Linguist Name:</span><u>&nbsp;&nbsp;&nbsp;' . $name . '&nbsp;&nbsp;</u><span style="color:#FFF; font-size:7px">_________</span><span style="font-weight:bold;color:#066">&nbsp;&nbsp;Language:</span><u>&nbsp;&nbsp;&nbsp;' . $source . '&nbsp;&nbsp;</u><span style="color:#FFF; font-size:7px">_________</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span style="font-weight:bold;color:#066;"> Duration</span> <span style="font-size:10px"><u>' . $assignDur . '</u></span><br></div>
            <div style="font-size:12px"><span style="font-weight:bold;color:#066;">Date of Assignment: </span><u>' . $assignDate . '  </u><span style="color:#FFF; font-size:7px">_________</span><span style="font-weight:bold;color:#066;">Time of Assignment:</span> <u>' . $assignTime . '</u><span style="font-weight:bold;color:#066">&nbsp;&nbsp;&nbsp;&nbsp;Service User Name:</span><u>&nbsp;&nbsp;&nbsp;' . $orgContact . '&nbsp;&nbsp;</u>&nbsp;<br></div>
                <div style="font-size:12px"><span style="font-weight:bold;color:#066;">Call Start Time:</span><span style="font-size:7px">_____________ </span><span style="font-weight:bold;color:#066;"> Call Finish Time: </span><span style="font-size:7px">_____________ </span> <span style="font-weight:bold;color:#066;"> Total Call Duration: Hours</span><span style="font-size:7px">______________ </span><span style="font-weight:bold;color:#066;"> Minutes</span><span style="font-size:7px">______________ </span><br><br></div>
                <div style="border:1px solid #C30">
                <div style="font-size:12px"><span style="font-weight:bold;color:#066;"> Company / Team / Unit Name or Title: </span> <u>' . $orgName . ' </u></div><br>
                <div style="font-size:12px"><span style="font-weight:bold;color:#066;"> Contact Name:</span><span style="font-size:7px">_________________________________________________________________________________________________________________________________________</span></div><br>
                <div style="font-size:12px"><span style="font-weight:bold;color:#066;"> Future / Follow Up Booking (If Any):</div><br>
                <div style="font-size:12px"><span style="font-weight:bold;color:#066;"> Will it be over:</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span style="font-size:7px"></span> <span style="font-weight:bold;color:#066;">Telephone: </span><span >Yes / No</span></span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span style="font-weight:bold;color:#066;">Face to Face: </span><span>Yes / No</span></div><br>
                <div style="font-size:12px"><span style="font-weight:bold;color:#066;"> Date:</span><span style="font-size:7px">__________________________________________________________________</span> <span style="font-weight:bold;color:#066;">Time:</span><span style="font-size:7px">___________________________________________________________________________</span></div><br>
                <div style="font-size:12px"><span style="font-weight:bold;color:#066;"> Location (If Applicable)</span><span style="font-size:7px">___________________________________________________________________________________________________________________________</span></div><br>
                <div style="font-size:12px"><span style="font-weight:bold;color:#066;"> Interpreter! Can you Do It?</span><span style="font-size:7px">____________________________</span> <span style="font-weight:bold;color:#066;">Yes / No</span><span style="font-size:7px">______________________________________________________________________________</span></div><br>
                </div>
            <br/>';
        $red_line = "Please return this form to Language Services UK Limited straight after the assignment";
        $timesheet_body = $acttObj->read_specific("em_format", "email_format", "id=51")['em_format'];
        $data   = ["[NAME]", "[SOURCE]", "[ASSIGNDATE]", "[ASSIGNTIME]", "[APPENDTABLE]"];
        $to_replace  = ["$name", "$source", "$assignDate", "$assignTime", "$append_table"];
    } else {
        $heading = "Translation Timesheet";
        $assignDate = $misc->dated($row['asignDate']);
        $docType = $acttObj->read_specific("tc_title", "trans_cat", "tc_id=" . $row['docType'])['tc_title'];
        $transType = $acttObj->read_specific("GROUP_CONCAT(CONCAT(td_title)  SEPARATOR ' <b> & </b> ') as td_title", "trans_dropdown", "td_id IN (" . $row['transType'] . ")")['td_title'];
        $trans_detail = $acttObj->read_specific("GROUP_CONCAT(CONCAT(tt_title)  SEPARATOR ' <b> & </b> ') as tt_title", "trans_types", "tt_id IN (" . $row['trans_detail'] . ")")['tt_title'];
        $deliveryType = $row['deliveryType'];
        $deliverDate2 = $misc->dated($row['deliverDate2']);
        $append_table = "<table>
            <tr>
            <td style='border: 1px solid yellowgreen;padding:5px;'>Source Language</td>
            <td style='border: 1px solid yellowgreen;padding:5px;'>{$source}</td>
            </tr>
            <tr>
            <td style='border: 1px solid yellowgreen;padding:5px;'>Target Language</td>
            <td style='border: 1px solid yellowgreen;padding:5px;'>{$target}</td>
            </tr>
            <tr>
            <td style='border: 1px solid yellowgreen;padding:5px;'>Document Type</td>
            <td style='border: 1px solid yellowgreen;padding:5px;'>{$docType}</td>
            </tr>
            <tr>
            <td style='border: 1px solid yellowgreen;padding:5px;'>Translation Type(s)</td>
            <td style='border: 1px solid yellowgreen;padding:5px;'>{$trans_detail}</td>
            </tr>
            <tr>
            <td style='border: 1px solid yellowgreen;padding:5px;'>Translation Category</td>
            <td style='border: 1px solid yellowgreen;padding:5px;'>{$transType}</td>
            </tr>
            <tr>
            <td style='border: 1px solid yellowgreen;padding:5px;'>Delivery Date</td>
            <td style='border: 1px solid yellowgreen;padding:5px;'>{$deliverDate2}</td>
            </tr>
            <tr>
            <td style='border: 1px solid yellowgreen;padding:5px;'>Delivery Type</td>
            <td style='border: 1px solid yellowgreen;padding:5px;'>{$deliveryType}</td>
            </tr>
            </table>";
        if (!empty($remrks)) {
            $append_table .= "<br>{$remrks}<br>";
        }
        $timesheet_data = '<div style="font-size:12px"><span style="font-weight:bold;color:#066;">Name:</span><u>&nbsp;&nbsp;&nbsp;' . $name . '&nbsp;&nbsp;</u><span style="color:#FFF; font-size:7px">_________</span><span style="font-weight:bold;color:#066">Source Language:</span><u>&nbsp;&nbsp;&nbsp;' . $source . '&nbsp;&nbsp;</u><span style="color:#FFF; font-size:7px">_________</span><span style="font-weight:bold;color:#066">Target Language:</span><u>&nbsp;&nbsp;&nbsp;' . $target . '&nbsp;&nbsp;</u><span style="color:#FFF; font-size:7px">_________</span></div><br>
                <div style="font-size:12px"><span style="font-weight:bold;color:#066;">Date of Assignment:</span><u>&nbsp;&nbsp;&nbsp;' . $assignDate . '&nbsp;&nbsp;</u><span style="color:#FFF; font-size:7px">_________</span><span style="font-weight:bold;color:#066">Delivery Date:</span><u>&nbsp;&nbsp;&nbsp;' . $deliverDate2 . '&nbsp;&nbsp;</u><span style="color:#FFF; font-size:7px">_________</span><span style="font-weight:bold;color:#066">Submission Date:</span><span style="font-size:7px">________________________</span></div><br>
                <div style="font-size:12px"><span style="font-weight:bold;color:#066;">Project ID:</span><u>&nbsp;' . $nameRef . '&nbsp;</u><span style="color:#FFF; font-size:7px">_____</span><span style="font-weight:bold;color:#066">Assignment Type:</span><u>&nbsp;' . $docType . '&nbsp;</u><span style="color:#FFF; font-size:7px">_____</span><br><br><span style="font-weight:bold;color:#066">Translation Category:</span><u>&nbsp;&nbsp;&nbsp;' . $transType . '&nbsp;&nbsp;</u><span style="color:#FFF; font-size:7px">_______</span><br><br><span style="font-weight:bold;color:#066">Translation Type(s):</span><u>&nbsp;&nbsp;&nbsp;' . $trans_detail . '&nbsp;&nbsp;</u><span style="color:#FFF; font-size:7px">_______</span></div><br>
                <div style="font-size:12px"><span style="font-weight:bold;color:#066;">Source Language Word Count:</span><span style="font-size:7px">______________________________________ </span><span style="font-weight:bold;color:#066;"> Target Language Word Count: </span><span style="font-size:7px">______________________________</span></div><br>
                <div style="border:1px solid #C30">
                <div style="font-size:12px"><span style="font-weight:bold;color:#066;"> Company / Team / Unit Name or Title: </span> <u>' . $orgName . ' </u></div><br>
                <div style="font-size:12px"><span style="font-weight:bold;color:#066;"> Contact Name:</span><u>' . $orgContact . '</u></div><br>
            </div>
            <br/>
            <div style="font-size:14px;line-height:30px">I hereby certify that I, [<u><i> ' . $name . ' </i></u>], am a professional translator to LSUK Limited. I hereby declare that I, am
            fully conversant with the [ ' . $source . ' ] and the [ ' . $target . ' ] translation languages and that the
            attached is translation in [ ' . $target . ' ] from [ ' . $source . ' ].
            I can confirm that this translation is to the best of my knowledge and belief, a true and faithful rendering of
            the original [ ' . $source . ' ] document and is translated to the best of my ability as a professional translator.
            Nothing is added or omitted to / from this document.<br><br>Executed on</span></div>';
        $red_line = "Please return this form to Language Services UK Limited along with the translation. Thank You";
        $timesheet_body = $acttObj->read_specific("em_format", "email_format", "id=30")['em_format'];
        $data   = ["[NAME]", "[SOURCE]", "[ASSIGNDATE]", "[APPENDTABLE]"];
        $to_replace  = ["$name", "$source", "$assignDate", "$append_table"];
    }

    // Include the main TCPDF library (search for installation path).
    require_once 'tcpdf_include.php';
    // create new PDF document
    $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

    // set document information
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetSubject('TCPDF Tutorial');

    // set default header data
    include 'rip_header.php';
    include 'rip_footer.php'; // set header and footer fonts
    $pdf->setHeaderFont(array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
    $pdf->setFooterFont(array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

    // set default monospaced font
    $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

    // set margins
    $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
    $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
    $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

    // set auto page breaks
    $pdf->SetAutoPageBreak(true, PDF_MARGIN_BOTTOM);

    // set image scale factor
    $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

    // set some language-dependent strings (optional)
    if (@file_exists(dirname(__FILE__) . '/lang/eng.php')) {
        require_once dirname(__FILE__) . '/lang/eng.php';
        $pdf->setLanguageArray($l);
    }

    // set font
    $pdf->SetFont('helvetica', 'B', 12);

    // add a page
    $pdf->AddPage();
    $pdf->SetFont('helvetica', '', 8);

$tbl = <<<EOD
    <div>
    <span align="right"> Date: {$misc->sys_date()} </span>
    <h1 style="text-decoration:underline; text-align:center">{$heading}</h1></div>
    {$timesheet_data}
    <br/><br/>
    $signature
    <br/>
    <h2 style="color:red;">{$red_line}</h2>
EOD;

    $pdf->writeHTML($tbl, true, false, false, false, '');

    // Name of pdf file
    $new_name = explode(':', $assignTime);
    $new_filename = $new_name[0] . '_' . $new_name[1] . '_' . $new_name[2];
    $name_file = $table != 'translation' ? "Timesheet $assignDate at $new_filename.pdf" : "Timesheet $assignDate.pdf";

    if (!isset($down) || isset($emailto)) {
        $pdfhere = $pdf->Output('', 'S');
        //echo $pdfhere;
        $to_add = $emailto;
        $strMsg = str_replace($data, $to_replace, $timesheet_body);
        $strSubject = $table != 'translation' ? 'Timesheet ' . $assignDate . ' at ' . $assignTime . ' for ' . $table . ' project id ' . $update_id : 'Timesheet ' . $assignDate . ' for ' . $table . ' project id ' . $update_id;

        try {
            //echo $pdfhere;
            $mail->SMTPDebug = 0;
            //$mail->isSMTP(); 
            //$mailer->Host = 'smtp.office365.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'info@lsuk.org';
            $mail->Password   = 'LangServ786';
            $mail->SMTPSecure = 'tls';
            $mail->Port       = 587;
            $mail->setFrom($from_add, $from_name);
            $mail->addAddress($to_add);
            $mail->addReplyTo($from_add, $from_name);
            $mail->addStringAttachment($pdfhere, $name_file);
            $mail->isHTML(true);
            $mail->Subject = $strSubject;
            $mail->Body    = $strMsg;
            if ($mail->send()) {
                $mail->ClearAllRecipients();
                $mail->ClearAttachments();
                $mail->addAddress($to_lsuk);
                $mail->addReplyTo($from_add, $from_name);
                $mail->addStringAttachment($pdfhere, $name_file);
                $mail->isHTML(true);
                $mail->Subject = $strSubject;
                $mail->Body    = $strMsg;
                $mail->send();
                $mail->ClearAllRecipients();
                $mail->ClearAttachments();
            }
            list($a, $b) = explode('.', basename(__FILE__));
            $pdf->Output('', 'I');
        } catch (Exception $e) {
            echo "<center><h3>The timesheet could not be sent via email.<br>Kindly send it manually.<br><a href='timesheet.php?update_id=" . $update_id . "&table=" . $table . "&down'><button type='button'>Click For TimeSheet</button></a>";
            // echo "Mailer Error: {$mail->ErrorInfo}</h3></center>";
        }
    } else {
        $pdf->Output($name_file, 'I');
    }
}
