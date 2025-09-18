<?php

if (session_id() == '' || !isset($_SESSION)) {
    session_start();
}

//php mailer library
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

set_include_path('/home/customer/www/lsuk.com/public_html/lsuk_system');
require '../../phpmailer/vendor/autoload.php';
// include 'phpmailer/vendor/autoload.php';
$mail = new PHPMailer(true);
if (isset($_SESSION['web_UserName']) && !empty($_SESSION['web_UserName'])) {
    $_SESSION['UserName'] = $_SESSION['web_UserName'];
}

include '../../db.php';
include_once '../../class.php';
// include 'db.php';
// include_once 'class.php';
error_reporting(0);
$from_name = "LSUK";
$from_add = 'payroll@lsuk.com';
$to_lsuk = 'imran.lsukltd@gmail.com';
$update_id = @$_GET['update_id'];
$table = strtolower($_GET['table']);
$down = @$_GET['down'];
$emailto = @$_GET['emailto'];
$orgzName = @$_GET['orgName'];
$append_table = "";
$heading = "";
$timesheet_data = "";
$red_line = "";
$query = "SELECT $table.*, interpreter_reg.name, interpreter_reg.interp_pix, interpreter_reg.pic_updated, comp_reg.name as orgName  FROM $table
     inner join interpreter_reg on $table.intrpName = interpreter_reg.id
     inner join comp_reg on $table.orgName = comp_reg.abrv
     where $table.id=$update_id";
//  echo $query;exit;
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
    $row['interp_pix'] = empty($row['interp_pix']) || $row['pic_updated'] == 0 ? "profile.png" : $row['interp_pix'];
    $image  = 'https://lsuk.org/lsuk_system/file_folder/interp_photo/' . $row['interp_pix'];
    $photo = $row['interp_pix'] ? '<img width="200" height="200" src="' . $image . '"/>' : "<br><br><br><p>Interpreter photo</p><br><br><br><br>";
    if ($table == 'translation') {
        $signature = '<tr><td class="td_no_border" width="100%"><span style="color:#000;font-size:14px;">Signature: </span>______________________________________ <span style="color:#000;font-size:14px;">Date: __________________________________</span></td></tr>';
    }
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
        if ($db_assignDur != $guess_dur && $guess_dur > 0) {
            //   list($partdur1, $partdur2) = explode(':', $guess_dur);
            //   $total_guess_dur = $partdur1 * 60 + $partdur2;
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
        $strSubject = 'Timesheet ' . $assignDate . ' at ' . $assignTime . ' for Face To Face project id ' . $update_id;
        $append_table = "
<table>
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
        if ($db_assignDur != $guess_dur && $guess_dur > 0) {
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
        $red_line = "Please return this form to Language Services UK Limited on the day of the assignment";
        $timesheet_data = '<h2 align="center" style="color:#0070c0;">Interpreter Timesheet – Face to Face Assignments</h2>
<table class="table_first" width="100%" style="height:400px;border:none !important;" cellspacing="0" cellpadding="0">
<tbody>
<tr>
    <td width="27%" height="100%" class="td_no_border">' . $photo . '</td>
    <td style="border:none !important;" width="75%">
        <table width="100%" style="border:none !important;" cellspacing="2" cellpadding="2"><tbody>
        <tr>
            <td class="td_no_border" width="20%"><span style="color:#000;font-size:14px;">Linguist Name:</span></td><td class="td_no_border thin_border" width="75%" align="center"><span>' . $name . '</span></td>
        </tr>
        <tr>
            <td class="td_no_border" width="15%"><span style="color:#000;font-size:14px;">LSUK - Ref:</span></td><td class="td_no_border thin_border" width="30%"><span>' . $nameRef . '</span></td>
            <td class="td_no_border" width="15%"><span style="color:#000;font-size:14px;">Client - Ref:</span></td><td class="td_no_border thin_border" width="35%"><span>' . $orgRef . '</span></td>
        </tr>
        <tr>
            <td class="td_no_border" width="15%"><span style="color:#000;font-size:14px;">Language:</span></td><td class="td_no_border thin_border" width="30%"><span>' . $source . '</span></td>
            <td class="td_no_border" width="10%"><span style="color:#000;font-size:14px;">Dialect:</span></td><td class="td_no_border thin_border" width="40%"><span>' . $target . '</span></td>
        </tr>
        <tr>
            <td class="td_no_border" width="21%"><span style="color:#000;font-size:14px;">Assignment Date:</span></td><td class="td_no_border thin_border" width="25%"><span>' . $assignDate . '</span></td>
            <td class="td_no_border" width="22%"><span style="color:#000;font-size:14px;">Assignment Time:</span></td><td class="td_no_border thin_border" width="18%"><span>' . $assignTime . '</span></td><td class="td_no_border" width="11%"><span>PM/AM</span></td>
        </tr>
        <tr>
            <td class="td_no_border" width="48%"><span style="color:#000;font-size:14px;">Assignment Duration (Minimum Duration):</span></td><td class="td_no_border thin_border" width="48%"><span>' . $assignDur . '</span></td>
        </tr>
        <tr>
            <td class="td_no_border" width="23%"><span style="color:#000;font-size:14px;">Interpreter Contact:</span></td><td class="td_no_border thin_border" width="73%"><span>' . $orgContact . '</span></td>
        </tr>
        <tr>
            <td class="td_no_border" width="23%"><span style="color:#000;font-size:14px;">Organization Name:</span></td><td class="td_no_border thin_border" width="73%"><span>' . $orgName . '</span></td>
        </tr>
        <tr>
            <td class="td_no_border" width="24%"><span style="color:#000;font-size:14px;">Place of Assignment:</span></td><td class="td_no_border thin_border" width="73%"><span>' . $buildingName . '  ' . $street . '  ' . $assignCity . '  ' . $postCode . '</span></td>
        </tr>
        </tbody>
        </table>
    </td>
</tr>
</tbody></table><br><h3 style="color:#0070c0;">Actual Hours</h3>
<table class="table_first" width="100%" style="height:400px;border:none !important;" cellspacing="0" cellpadding="0">
<tbody>
    <tr>
    <td style="border:none !important;" width="100%">
        <table style="border:none !important;" cellspacing="2" cellpadding="1"><tbody>
        <tr>
            <td class="td_no_border" width="13%"><span style="color:#000;font-size:14px;"><b>Arrival Time:</b></span></td><td class="td_no_border thin_border" width="16%"><span></span></td><td class="td_no_border" width="15%"><span><b>PM/AM</b></span></td>
            <td class="td_no_border" width="20%"><span style="color:#000;font-size:14px;"><b>Waiting Time (if any):</b></span></td><td class="td_no_border thin_border" width="17%"><span></span></td><td class="td_no_border" width="16%"><span><b>Hours / Minutes</b></span></td>
        </tr>
        <tr>
            <td class="td_no_border" width="10%"><span style="color:#000;font-size:14px;">Start Time:</span></td><td class="td_no_border thin_border" width="11%"><span></span></td><td class="td_no_border" width="12%"><span>PM/AM</span></td>
            <td class="td_no_border" width="12%"><span style="color:#000;font-size:14px;">Finish Time:</span></td><td class="td_no_border thin_border" width="11%"><span></span></td><td class="td_no_border" width="12%"><span>PM/AM</span></td>
            <td class="td_no_border" width="13%"><span style="color:#000;font-size:14px;">Total Duration:</span></td><td class="td_no_border thin_border" width="15%"><span></span></td>
        </tr>
        <tr>
            <td class="td_no_border" width="27%"><span style="color:#000;font-size:14px;">Travel Duration (if Applicable):</span></td><td class="td_no_border thin_border" width="10%"><span></span></td><td class="td_no_border" width="17%"><span>Hours / Minutes</span></td>
            <td class="td_no_border" width="17%"><span style="color:#000;font-size:14px;">Mode of Transport:</span></td><td class="td_no_border thin_border" width="26%"><span></span></td>
        </tr>
        <tr>
            <td class="td_no_border" width="27%"><span style="color:#000;font-size:14px;">Travel Mileage (if Applicable):</span></td><td class="td_no_border thin_border" width="10%"><span></span></td><td class="td_no_border" width="17%"><span>Miles</span></td>
            <td class="td_no_border" width="34%"><span style="color:#000;font-size:14px;">Other Expenses (Parking, Bridge Toll) £:</span></td><td class="td_no_border thin_border" width="9%"><span></span></td>
        </tr>
        <tr>
            <td class="td_no_border" width="27%"><span style="color:#000;font-size:14px;">Travel Costs (If Applicable) £:</span></td><td class="td_no_border thin_border" width="10%"><span></span></td>
            <td class="td_no_border" width="30%"><span style="color:#000;font-size:14px;">Please Attach Receipts</span></td>
        </tr>
        </tbody>
        </table>
    </td>
</tr>
</tbody></table><br><br>
<style>table, td, th {border: 1px solid grey;}</style>
<table width="100%" style="height:400px;" cellspacing="0" cellpadding="4">
<tbody>
    <tr>
        <td class="td_no_border" width="100%"><span style="color:#000;font-size:14px;"><b>Authorised Signatory</b></span></td>
    </tr>
    <tr>
        <td class="td_no_border" width="100%"><span style="color:#000;font-size:14px;">Client Signature: ___________________________________________</span></td>
    </tr>
    <tr>
        <td class="td_no_border" width="100%"><span style="color:#000;font-size:14px;">Name: ___________________________________________</span> <span style="color:#000;font-size:14px;">Designation: _______________________________________</span></td>
    </tr>
    <tr>
        <td class="td_no_border" width="100%"><span style="color:#000;font-size:14px;">“I am an authorised signatory for my department. I am signing to confirm that the Interpreter and the hours that I am
        authorising are accurate and I approve payment. I am signing to confirm that I have checked and verified the photo
        identification of the interpreter with the booking form/job card. I understand that if I knowingly provide false
        information this may result in disciplinary action and I may be liable to prosecution and civil recovery proceedings. I
        consent to the disclosure of information from this form to and by the Approved Organisation for the purpose of
        verification of this claim and the investigation, prevention, detection and prosecution of fraud.”</span></td>
    </tr>
    ' . $signature . '
    <tr>
        <td class="td_no_border" width="100%"></td>
    </tr>
</tbody>
</table>
<table width="100%" style="height:400px;border:none;" cellspacing="0" cellpadding="12">
<tbody>
    <tr>
        <td class="td_no_border" width="100%"><span style="color:#000;font-size:14px;">“I declare that the information I have given on this form is correct and complete and that I have not claimed elsewhere
        under this agreement for the hours/shifts detailed on this timesheet. I understand that if I knowingly provide false
        information this may result in disciplinary action and I may be liable to prosecution and civil recovery proceedings. I
        consent to the disclosure of information from this form to and by the Approved Organisation for the purpose of verification
        of this claim and the investigation, prevention, detection and prosecution of fraud.”</span></td>
    </tr>
</tbody>
</table>
<b>Interpreter Signature:</b></span><span>____________________________________</span> <b>Date:</b><span>__________________________________</span><br>
Future Booking Date (if Known) <b>Date: </b></span><span>_______________________</span> <b>Time:</b><span>________________________</span><br>
<b>Location (if different):</b></span><span>_________________________________________________________________</span><br>
<b>I can / can’t Attend the future session?</b></span><span> Yes / No</span><br>
<span style="color:red;font-size:14px;" align="center"><br><b>' . $red_line . '</b></span>';
        $timesheet_body = $acttObj->read_specific("em_format", "email_format", "id=2")['em_format'];
        $data   = ["[NAME]", "[SOURCE]", "[BUILDINGNAME]", "[STREET]", "[ASSIGNCITY]", "[POSTCODE]", "[ASSIGNDATE]", "[ASSIGNTIME]", "[APPENDTABLE]"];
        $to_replace  = ["$name", "$source", "$buildingName", "$street", "$assignCity", "$postCode", "$assignDate", "$assignTime", "$append_table"];
    } else if ($table == 'telephone') {
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
        $comunic = $acttObj->read_specific("c_title", "comunic_types", "c_id=" . $row['comunic'])['c_title'];
        // $channel_img = file_exists('../../images/comunic_types/' . $get_channel['c_image']) ? '<img src="../../images/comunic_types/' . $get_channel['c_image'] . '" width="36"/> ' : '';
        $communication_type = empty($row['comunic']) || $row['comunic'] == 11 ? "Telephone interpreting" : $comunic;
        $heading = $communication_type . " Timesheet";
        $strSubject = 'Timesheet ' . $assignDate . ' at ' . $assignTime . ' for ' . $communication_type . ' project id ' . $update_id;
        $append_table = "
<table>
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
        if ($db_assignDur != $guess_dur && $guess_dur > 0) {
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
        $red_line = "Please return this form to Language Services UK Limited straight after the assignment";
        $timesheet_data = '<br><h2 align="center" style="color:#0070c0;">Interpreter Timesheet – ' . $communication_type . ' Assignments</h2><br>
<table class="table_first" width="100%" style="height:400px;border:none !important;" cellspacing="8" cellpadding="4">
<tbody>
<tr>
    <td style="border:none !important;" width="100%">
        <table width="100%" style="border:none !important;" cellspacing="8" cellpadding="4"><tbody>
        <tr>
            <td class="td_no_border" width="17%"><span style="color:#000;font-size:14px;">Linguist Name:</span></td><td class="td_no_border thin_border" width="76%" align="center"><span>' . $name . '</span></td>
        </tr>
        <tr>
            <td class="td_no_border" width="13%"><span style="color:#000;font-size:14px;">LSUK - Ref:</span></td><td class="td_no_border thin_border" width="30%"><span>' . $nameRef . '</span></td>
            <td class="td_no_border" width="13%"><span style="color:#000;font-size:14px;">Client - Ref:</span></td><td class="td_no_border thin_border" width="35%"><span>' . $orgRef . '</span></td>
        </tr>
        <tr>
            <td class="td_no_border" width="13%"><span style="color:#000;font-size:14px;">Language:</span></td><td class="td_no_border thin_border" width="30%"><span>' . $source . '</span></td>
            <td class="td_no_border" width="13%"><span style="color:#000;font-size:14px;">Dialect:</span></td><td class="td_no_border thin_border" width="35%"><span>' . $target . '</span></td>
        </tr>
        <tr>
            <td class="td_no_border" width="18%"><span style="color:#000;font-size:14px;">Assignment Date:</span></td><td class="td_no_border thin_border" width="25%"><span>' . $assignDate . '</span></td>
            <td class="td_no_border" width="20%"><span style="color:#000;font-size:14px;">Assignment Time:</span></td><td class="td_no_border thin_border" width="19%"><span>' . $assignTime . '</span></td><td class="td_no_border" width="11%"><span>PM/AM</span></td>
        </tr>
        <tr>
            <td class="td_no_border" width="40%"><span style="color:#000;font-size:14px;">Assignment Duration (Minimum Duration):</span></td><td class="td_no_border thin_border" width="55%"><span>' . $assignDur . '</span></td>
        </tr>
        <tr>
            <td class="td_no_border" width="23%"><span style="color:#000;font-size:14px;">Interpreter Contact:</span></td><td class="td_no_border thin_border" width="73%"><span>' . $orgContact . '</span></td>
        </tr>
        <tr>
            <td class="td_no_border" width="23%"><span style="color:#000;font-size:14px;">Organization Name:</span></td><td class="td_no_border thin_border" width="73%"><span>' . $orgName . '</span></td>
        </tr>
        </tbody>
        </table>
    </td>
</tr>
</tbody></table><br><h3 style="color:#0070c0;">&nbsp;&nbsp;&nbsp;Actual Hours</h3><br>
<table class="table_first" width="100%" style="height:400px;border:none !important;" cellspacing="0" cellpadding="0">
<tbody>
    <tr>
    <td style="border:none !important;" width="100%">
        <table style="border:none !important;" cellspacing="2" cellpadding="2">
        <tbody>
        <tr>
            <td class="td_no_border" width="20%"><span style="color:#000;font-size:14px;"><b>Waiting Time (if any):</b></span></td><td class="td_no_border thin_border" width="17%"><span></span></td><td class="td_no_border" width="16%"><span><b>Hours / Minutes</b></span></td>
        </tr>
        <tr>
            <td class="td_no_border" width="20%"></td>
        </tr>
        <tr>
            <td class="td_no_border" width="10%"><span style="color:#000;font-size:14px;">Start Time:</span></td><td class="td_no_border thin_border" width="8%"><span></span></td><td class="td_no_border" width="12%"><span>PM/AM</span></td>
            <td class="td_no_border" width="11%"><span style="color:#000;font-size:14px;">Finish Time:</span></td><td class="td_no_border thin_border" width="8%"><span></span></td><td class="td_no_border" width="12%"><span>PM/AM</span></td>
            <td class="td_no_border" width="13%"><span style="color:#000;font-size:14px;">Total Duration:</span></td><td class="td_no_border thin_border" width="10%"><span></span></td><td class="td_no_border" width="8%"><span><b>Hours</b></span></td>
        </tr>
        </tbody>
        </table>
    </td>
</tr>
</tbody></table><br><br>
<style>table, td, th {border: 1px solid grey;}</style>
<table width="100%" style="height:400px;border:none;" cellspacing="0" cellpadding="12">
<tbody>
    <tr>
        <td class="td_no_border" width="100%"><span style="color:#000;font-size:16px;">“I declare that the information I have given on this form is correct and complete and that I have not claimed elsewhere 
        under this agreement for the hourr detailed on this timesheet. I understand that if I knowingly provide false information 
        this may result in disciplinary action and I may be liable to prosecution and civil recovery proceedings. I consent to the 
        disclosure of information from this form to and by the Approved Organisation for the purpose of verification of this claim 
        and the investigation, prevention, detection and prosecution of fraud.”</span></td>
    </tr>
</tbody>
</table><br><br>
<b>&nbsp;&nbsp;&nbsp;Interpreter Signature:</b></span><span>____________________________________</span> <b>Date:</b><span>______________________________</span><br><br>
&nbsp;&nbsp;&nbsp;Future Booking Date (if Known) <b>Date: </b></span><span>_______________________</span> <b>Time:</b><span>________________________</span><br><br><br>
&nbsp;&nbsp;&nbsp;<b>I can / can’t Attend the future session?</b></span><span> Yes / No</span><br><br>
<span style="color:red;font-size:14px;" align="center"><br><b>' . $red_line . '</b></span>';
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
        $strSubject = 'Timesheet ' . $assignDate . ' for Translation project id ' . $update_id;
        $append_table = "
<table>
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
        $timesheet_data = '<br><h2 align="center" style="color:#0070c0;">Interpreter Timesheet – Translation Assignments</h2><br>
<table class="table_first" width="100%" style="height:400px;border:none !important;" cellspacing="8" cellpadding="4">
<tbody>
<tr>
    <td style="border:none !important;" width="100%">
        <table width="100%" style="border:none !important;" cellspacing="8" cellpadding="4"><tbody>
        <tr>
            <td class="td_no_border" width="17%"><span style="color:#000;font-size:14px;">Linguist Name:</span></td><td class="td_no_border thin_border" width="76%" align="center"><span>' . $name . '</span></td>
        </tr>
        <tr>
            <td class="td_no_border" width="18%"><span style="color:#000;font-size:14px;">LSUK - Ref:</span></td><td class="td_no_border thin_border" width="27%"><span>' . $nameRef . '</span></td>
            <td class="td_no_border" width="14%"><span style="color:#000;font-size:14px;">Client - Ref:</span></td><td class="td_no_border thin_border" width="32%"><span>' . $orgRef . '</span></td>
        </tr>
        <tr>
            <td class="td_no_border" width="18%"><span style="color:#000;font-size:14px;">Language:</span></td><td class="td_no_border thin_border" width="27%"><span>' . $source . '</span></td>
            <td class="td_no_border" width="14%"><span style="color:#000;font-size:14px;">Dialect:</span></td><td class="td_no_border thin_border" width="32%"><span>' . $target . '</span></td>
        </tr>
        <tr>
            <td class="td_no_border" width="18%"><span style="color:#000;font-size:14px;">Assignment Date:</span></td><td class="td_no_border thin_border" width="27%"><span>' . $assignDate . '</span></td>
            <td class="td_no_border" width="17%"><span style="color:#000;font-size:14px;">Delivery Date:</span></td><td class="td_no_border thin_border" width="29%"><span>' . $deliverDate2 . '</span></td>
        </tr>
        <tr>
            <td class="td_no_border" width="23%"><span style="color:#000;font-size:14px;">Submission Date:</span></td><td class="td_no_border thin_border" width="70%"><span></span></td>
        </tr>
        <tr>
            <td class="td_no_border" width="23%"><span style="color:#000;font-size:14px;">Assignment Type:</span></td><td class="td_no_border thin_border" width="70%"><span>' . $docType . '</span></td>
        </tr>
        <tr>
            <td class="td_no_border" width="23%"><span style="color:#000;font-size:14px;">Translation Category:</span></td><td class="td_no_border thin_border" width="70%"><span>' . $transType . '</span></td>
        </tr>
        <tr>
            <td class="td_no_border" width="23%"><span style="color:#000;font-size:14px;">Translation Type(s):</span></td><td class="td_no_border thin_border" width="70%"><span>' . $trans_detail . '</span></td>
        </tr>
        <tr>
            <td class="td_no_border" width="23%"><span style="color:#000;font-size:14px;">Interpreter Contact:</span></td><td class="td_no_border thin_border" width="70%"><span>' . $orgContact . '</span></td>
        </tr>
        <tr>
            <td class="td_no_border" width="23%"><span style="color:#000;font-size:14px;">Organization Name:</span></td><td class="td_no_border thin_border" width="70%"><span>' . $orgName . '</span></td>
        </tr>
        </tbody>
        </table>
    </td>
</tr>
</tbody></table>
<br><h3 style="color:#0070c0;">&nbsp;&nbsp;&nbsp;Actual Units / Words Count</h3><br>
<table class="table_first" width="100%" style="height:400px;border:none !important;" cellspacing="0" cellpadding="0">
<tbody>
    <tr>
        <td class="td_no_border" width="20%"></td>
    </tr>
    <tr>
        <td class="td_no_border" width="30%"><span style="color:#000;font-size:15px;"><b>&nbsp;&nbsp;&nbsp;Source Language Word Count:</b></span></td><td class="td_no_border thin_border" width="18%"><span></span></td>
        <td class="td_no_border" width="29%"><span style="color:#000;font-size:15px;"><b>Target Language Word Count:</b></span></td><td class="td_no_border thin_border" width="18%"><span></span></td>
    </tr>
    <tr>
        <td class="td_no_border" width="20%"></td>
    </tr>
</tbody></table>
<br><br>
<style>table, td, th {border: 1px solid grey;}</style>
<table width="100%" style="border:none;" cellspacing="0" cellpadding="8">
<tbody>
    <tr>
        <td class="td_no_border" width="100%"><span style="color:#000;font-size:16px;">“I hereby certify that I, [<u><i> ' . $name . ' </i></u>], am a professional translator to LSUK Limited. I hereby declare that I, am
fully conversant with the [ ' . $source . ' ] and the [ ' . $target . ' ] translation languages and that the
attached is translation in [ ' . $target . ' ] from [ ' . $source . ' ].
I can confirm that this translation is to the best of my knowledge and belief, a true and faithful rendering of
the original [ ' . $source . ' ] document and is translated to the best of my ability as a professional translator.
Nothing is added or omitted to / from this document.”<br><br>Executed on</span></td>
    </tr>
</tbody>
</table><br><br>
<b>&nbsp;&nbsp;&nbsp;Interpreter Signature:</b></span><span>____________________________________</span> <b>Date:</b><span>______________________________</span><br><br>
<span style="color:red;font-size:14px;" align="center"><br><b>' . $red_line . '</b></span>';
        $timesheet_body = $acttObj->read_specific("em_format", "email_format", "id=30")['em_format'];
        $data   = ["[NAME]", "[SOURCE]", "[ASSIGNDATE]", "[APPENDTABLE]"];
        $to_replace  = ["$name", "$source", "$assignDate", "$append_table"];
    }
    require_once 'tcpdf_include.php';
    // create new PDF document
    $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
    // set document information
    $pdf->SetCreator(PDF_CREATOR);
    // set default header data
    //include 'rip_header.php';
    $PDF_HEADER_LOGO = "lsuk_logo.png"; //any image file. check correct path.
    $PDF_HEADER_LOGO_WIDTH = "10";
    $PDF_HEADER_TITLE = "                                        \t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\tLanguage Services UK Limited";
    $PDF_HEADER_STRING = "                     \t\t\t\t\t\t\t\tInterpreting | Translation | Telephone Interpreting | Cross-Cultural Training & More";
    $pdf->SetHeaderData($PDF_HEADER_LOGO, $PDF_HEADER_LOGO_WIDTH, $PDF_HEADER_TITLE, $PDF_HEADER_STRING);
    $pdf->setHeaderFont(array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
    include 'rip_footer.php'; // set header and footer fonts
    $pdf->setHeaderFont(array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
    $pdf->setFooterFont(array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
    // set default monospaced font
    $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
    // set margins
    $pdf->SetMargins(4, 14, 4);
    $pdf->SetHeaderMargin(1);
    $pdf->SetFooterMargin(8);

    // set auto page breaks
    $pdf->SetAutoPageBreak(true, PDF_MARGIN_BOTTOM);

    // set image scale factor
    $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

    // set some language-dependent strings (optional)
    if (@file_exists(dirname(__FILE__) . '/lang/eng.php')) {
        require_once dirname(__FILE__) . '/lang/eng.php';
        $pdf->setLanguageArray($l);
    }
    //$pdf->SetFont('helvetica', 'B', 12);
    $pdf->SetFont('Times');
    $pdf->AddPage();
    $tbl = <<<EOD
<style>.td_no_border{border:none !important;}.thin_border{border-bottom:1px;border-bottom-color:grey;border-bottom-width:1px;font-style:italic;}</style>
{$timesheet_data}
EOD;

    $pdf->writeHTML($tbl, true, false, false, false, '');
    // $pdf->Output('', 'I'); die;
    // Footer new data
    //$this->SetX($this->original_lMargin);
    // $this->Cell(0, 0, 'Address: Suite 3, Davis House, Lodge Causeway Trading Estate, Lodge Causeway, Fishponds, Bristol BS16 3JB.', 'T', 0, 'C');
    // $this->Ln();
    // $this->Cell(0, 0, 'Phone 01173290610, Mob: 07915177068 Fax: 0333 800 5785 Email: PAYROLL@LSUK.ORG', 'T', 0, 'C');

    $new_name = explode(':', $assignTime);
    $new_filename = $new_name[0] . '_' . $new_name[1] . '_' . $new_name[2];
    $name_file = $table != 'translation' ? "Timesheet $assignDate at $new_filename.pdf" : "Timesheet $assignDate.pdf";
    //     var_dump($down,$emailto);
    // var_dump(!isset($down) || isset($emailto)); die;
    if (!isset($down) || isset($emailto)) {
        $pdfhere = $pdf->Output('', 'S');
        //echo $pdfhere;
        $to_add = $emailto;
        $strMsg = str_replace($data, $to_replace, $timesheet_body);
        try {
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
            if ($orgName != "" && substr($orgName, 0, 3) == "VHS"):
                $mail->addAttachment('files/Questionnaires_MDS_for_VHS.pdf', 'Questionnaire.pdf');
            endif;
            $mail->isHTML(true);
            $mail->Subject = $strSubject;
            $mail->Body    = $strMsg;
            if ($mail->send()) {
                $mail->ClearAllRecipients();
                $mail->ClearAttachments();
                $mail->addAddress($to_lsuk);
                $mail->addReplyTo($from_add, $from_name);
                $mail->addStringAttachment($pdfhere, $name_file);
                if ($orgName != "" && substr($orgName, 0, 3) == "VHS"):
                    $mail->addAttachment('files/Questionnaires_MDS_for_VHS.pdf', 'Questionnaire.pdf');
                endif;
                $mail->isHTML(true);
                $mail->Subject = $strSubject;
                $mail->Body    = $strMsg;
                if ($mail->send()):
                    $mail->ClearAllRecipients();
                    $mail->ClearAttachments();
                    list($a, $b) = explode('.', basename(__FILE__));
                    $pdf->Output('', 'I');
                else:
                    echo "Mailer Error: " . $mail->ErrorInfo;
                endif;
            }
        } catch (Exception $e) {
            echo 'Error  = ' . $e->errorMessage();
            exit;
            echo "<center><h3>The timesheet could not be sent via email.<br>Kindly send it manually.<br><a href='timesheet.php?update_id=" . $update_id . "&table=" . $table . "&down'><button type='button'>Click For TimeSheet</button></a>";
            // echo "Mailer Error: {$mail->ErrorInfo}</h3></center>";
        }
    } else {
        $pdf->Output($name_file, 'I');
    }
}
