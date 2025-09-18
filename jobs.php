<?php if (session_id() == '' || !isset($_SESSION)) {
    session_start();
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['alt_date'], $_POST['alt_message']) && $_POST['pf'] == 0) {
    $_SESSION['alt_date'] = $_POST['alt_date'];
    $_SESSION['alt_message'] = $_POST['alt_message'];
    $val = $_POST['val'];
    $tracking = $_POST['tracking'];
    $bid_type = 3;

    // Redirect with pf=1
    header("Location: jobs.php?val=$val&tracking=$tracking&bid_type=$bid_type");
    exit;
}
error_reporting(0);
//php mailer library
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require 'lsuk_system/phpmailer/vendor/autoload.php';
$mail = new PHPMailer(true);
include 'source/db.php';
include 'source/class.php';
include_once('source/function.php');
$name = SafeVar::GetVar('name', '');
$gender = SafeVar::GetVar('gender', '');
$city = SafeVar::GetVar('city', '');
$page = (int) (!isset($_GET["page"]) ? 1 : $_GET["page"]);
$limit = 20;
$startpoint = ($page * $limit) - $limit;
$array_gender = array("Male" => 1, "Female" => 2, "No Preference" => 3);

//New Functionality
$interp_code = $_SESSION['interp_code'];
$query = "SELECT * FROM interpreter_reg where code='$interp_code'";
$result = mysqli_query($con, $query);
$row = mysqli_fetch_array($result);
$interp_id = $row['id'];
$user_id = $_SESSION['web_userId'];
$user_name = $_SESSION['web_UserName'];
$check_noty = $acttObj->read_specific("status", "notify_new_doc", "interpreter_id = '$interp_id'")['status'];
$picture = $row['interp_pix'] ? $row['interp_pix'] : 'profile.png';
$photo_path = "lsuk_system/file_folder/interp_photo/" . $picture;
if (!file_exists($photo_path)) {
    $photo_path = "lsuk_system/file_folder/interp_photo/profile.png";
  }
?>
<!DOCTYPE HTML>
<html class="no-js">

<head>
    <?php include 'source/header.php'; ?>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" />
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.21/css/dataTables.bootstrap.min.css" />
    <style>
        select.input-sm {
            line-height: 22px;
        }

        .glyphicon {
            color: #fff;
        }

        input,
        textarea,
        select {
            -webkit-appearance: button;
        }

        .dataTables_wrapper .row {
            margin: 0px !important;
        }

        .alert-warning {
            background-color: #f7f1d0;
            border-color: #d4ccbc;
        }
        h5{
            color: black ;
            font-weight: bold;
        }
    </style>
</head>

<body class="boxed">
<?php
//Report runtime errors
// error_reporting(E_ERROR | E_WARNING | E_PARSE);

// // Report all errors
// error_reporting(E_ALL);

// // Same as error_reporting(E_ALL);
// ini_set("error_reporting", E_ALL);
require "./lsuk_system/post_format_job.php";
?>
    <!-- begin container -->
    <div id="wrap">
        <!-- begin header -->
        <?php include 'source/top_nav.php'; ?>
        <!-- end header -->
        <?php
        if (isset($_POST['btn_forgot'])) {
            $forgotEmail_f = $_POST['forgotEmail'];
            if ($forgotEmail_f) {
                $query_f = "SELECT count(*) num,interpreter_reg.id, interpreter_reg.name,interpreter_reg.contactNo,interpreter_reg.password, interpreter_reg.email,interpreter_reg.deleted_flag,interpreter_reg.active FROM interpreter_reg where email='$forgotEmail_f'";
                $result_f = mysqli_query($con, $query_f);
                $row_f = mysqli_fetch_assoc($result_f);
                $flag_f = $row_f['num'];
                $UserName_f = $row_f['name'];
                $id_f = $row_f['id'];
                $email_f = $row_f['email'];
                $contactNo_f = $row_f['contactNo'];
                $password_f = $row_f['password'];
                $deleted_flag_f = $row_f['deleted_flag'];
            }
            if ($flag_f == 0 || is_null($deleted_flag_f)) {
                $msg = '<div class="alert alert-danger col-md-4 col-md-offset-4 text-center"><b>Entered Email Not Found at LSUK!</b></div><br><br>';
            } else if ($deleted_flag_f == '1' || $row_f['active'] == 1) {
                $get_msg_db = $acttObj->read_specific('message', 'auto_replies', 'id=2');
                $msg = '<div class="alert alert-danger col-md-4 col-md-offset-4 text-center"><b>' . $get_msg_db['message'] . '</b></div><br><br>';
            } else {
                if ($flag_f == 1) {
                    if (empty($password_f) || is_null($password_f)) {
                        $new_password_f = '@' . strtok($row_f['name'], " ") . substr(str_shuffle('0123456789abcdwxyz'), 0, 3) . substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 3);
                        $acttObj->editFun('interpreter_reg', $id_f, 'password', $new_password_f);
                        $password_f = $acttObj->read_specific('password', 'interpreter_reg', 'id=' . $id_f)['password'];
                    }
                    try {
                        $to_add = $forgotEmail_f;
                        $from_add = "hr@lsuk.org";
                        $em_format = $acttObj->read_specific("em_format", "email_format", "id=35")['em_format'];
                        $data   = ["[PASSWORD]"];
                        $to_replace  = [$password_f];
                        $message = str_replace($data, $to_replace, $em_format);
                        $mail->SMTPDebug = 0;
                        //$mail->isSMTP(); 
                        //$mailer->Host = 'smtp.office365.com';
                        $mail->SMTPAuth   = true;
                        $mail->Username   = 'info@lsuk.org';
                        $mail->Password   = 'LangServ786';
                        $mail->SMTPSecure = 'tls';
                        $mail->Port       = 587;
                        $mail->setFrom($from_add, 'LSUK Account Security');
                        $mail->addAddress($to_add);
                        $mail->addReplyTo($from_add, 'LSUK');
                        $mail->isHTML(true);
                        $mail->Subject = 'Password recovery for LSUK online portal';
                        $mail->Body = $message;
                        $mail->send();
                        $mail->ClearAllRecipients();
                        $msg = '<div class="alert alert-success col-md-6 col-md-offset-3 text-center">Thanks <b>' . ucwords($UserName_f) . ' !</b> We have sent password to your email.<br>Kindly check and try login again.<br>If still you have problem with login than Contact LSUK at Ph: 01173290610</div><br><br>';
                    } catch (Exception $e) {
                        $msg = '<div class="alert alert-danger col-md-6 col-md-offset-3 text-center">There was problem sending email.Try again.</div><br><br>';
                    }
                }
            }
        }
        $val = $_GET['val'];
        if (isset($_POST['login'])) {
            $Pswrd = $_POST['loginPass'];
            $UserNam = $_POST['loginEmail'];
            if ($UserNam && $Pswrd) {
                $query = "SELECT count(*) num,interpreter_reg.id, interpreter_reg.is_temp, interpreter_reg.name,interpreter_reg.contactNo, interpreter_reg.email,interpreter_reg.code,interpreter_reg.gender,interpreter_reg.address,interpreter_reg.deleted_flag,interpreter_reg.active FROM interpreter_reg where  email='$UserNam' AND BINARY password='$Pswrd'";
                $result = mysqli_query($con, $query);
                $row = mysqli_fetch_assoc($result);
                $flag = $row['num'];
                $UserName = $row['name'];
                $id = $row['id'];
                $email = $row['email'];
                $contactNo = $row['contactNo'];
                $gender = $row['gender'];
                $interp_code = $row['code'];
                $is_temp = $row['is_temp'];
                $deleted_flag = $row['deleted_flag'];
            }
            if ($is_temp == 1) {
                $get_msg_db = $obj->read_specific('message', 'auto_replies', 'id=7');
                $json->msg = $get_msg_db['message'];
            } else if ($flag == 0 || is_null($deleted_flag)) {
                $get_msg_db = $acttObj->read_specific('message', 'auto_replies', 'id=1');
                $msg = '<div class="alert alert-danger col-md-6 col-md-offset-3 text-center h4"><b>' . $get_msg_db['message'] . '</b></div><br><br>';
            } else if ($deleted_flag == '1' || $row['active'] == 1) {
                $get_msg_db = $acttObj->read_specific('message', 'auto_replies', 'id=2');
                $msg = '<div class="alert alert-danger col-md-6 col-md-offset-3 text-center h4"><b>' . $get_msg_db['message'] . '</b></div><br><br>';
            } else {
                if ($flag == 0) {
                    $get_msg_db = $acttObj->read_specific('message', 'auto_replies', 'id=1');
                    $msg = '<div class="alert alert-danger col-md-6 col-md-offset-3 text-center h4"><b>' . $get_msg_db['message'] . '</b></div><br><br>';
                }
                if ($flag == 1) {
                    $_SESSION['UserName'] = $UserName;
                    $_SESSION['web_UserName'] = $UserName;
                    $_SESSION['web_userId'] = $id;
                    $_SESSION['email'] = $email;
                    $_SESSION['web_contactNo'] = $contactNo;
                    $_SESSION['web_address'] = $address;
                    $_SESSION['gender'] = $gender;
                    $_SESSION['interp_code'] = $interp_code;
                    $url = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
                    //header('location:$url'); 
                    $result_url = "https://lsuk.org/jobs.php?val=$val";
                    if ($val = 'interpreter') {
                        $go_to = "time_sheet_interp.php";
                    } elseif ($val = 'telephone') {
                        $go_to = "time_sheet_telep.php";
                    } else {
                        $go_to = "time_sheet_trans.php";
                    }
                    if ($url != $result_url) {
                        echo "<script>setTimeout(function(){ window.location.href='$go_to'; }, 15000);</script>";
                    } else {
                        echo "<script>window.location.href='$result_url';</script>";
                    }
                }
            }
        }
        $interp_id = $_SESSION['web_userId'];
        //job bid starts here
        if (isset($_GET['val']) && isset($_GET['tracking']) && isset($_SESSION['web_userId'])) {
            
                
            $data1 = $_SESSION['web_UserName'];
            $check_id = $_GET['tracking'];
            $val = $_GET['val'];
            $bid_type = $_GET['bid_type'] ? $_GET['bid_type'] : 1;
            $blocked_for = array("interpreter" => 1, "telephone" => 1, "translation" => 2);
            $msg_cont_office = '';
            $check_res = $acttObj->read_specific('id', 'bid', 'job=' . $check_id . ' and interpreter_id=' . $_SESSION['web_userId'] . ' and tabName="' . $val . '"');
            $check_bid_booked = $acttObj->read_specific('id', 'bid', 'job=' . $check_id . ' and allocated=1 and tabName="' . $val . '"');
            $check_booked = $acttObj->unique_data($val, 'intrpName', 'id', $check_id);
            if ($check_res['id'] != '') {
                $get_msg_db = $acttObj->read_specific('message', 'auto_replies', 'id=3');
                $msg = "<div class='alert alert-warning col-md-10 col-md-offset-1 text-center h4'><b>" . $get_msg_db['message'] . "</b></div>";
            } else if ($acttObj->read_specific("id", $val, "deleted_flag=0 AND order_cancel_flag=0 AND id=" . $check_id)['id'] == '') {
                $msg = "<div class='alert alert-warning col-md-10 col-md-offset-1 text-center h4'><b>Sorry ! This job is no longer available.<br>Thank you</b></div>";
            } else {
                if ($check_booked != '' || $check_bid_booked['id'] != '') {
                    $get_msg_db = $acttObj->read_specific('message', 'auto_replies', 'id=4');
                    $msg = "<div class='alert alert-warning col-md-10 col-md-offset-1 text-center h4'><b>" . $get_msg_db['message'] . "</b></div>";
                } else {
                    //code to place 
                    $codeid = "id-$interp_id";
                    $check_id = $_GET['tracking'];
                    $query_job = "SELECT *  FROM $val  where id='$check_id'";
                    $result_job = mysqli_query($con, $query_job);
                    $row_job = mysqli_fetch_assoc($result_job);
                    $assignDate = $val == 'translation' ? $row_job['asignDate'] : $row_job['assignDate'];
                    $sourceForJob = $row_job['source'];
                    $targetForJob = $row_job['target'];
                    $orgNameForJob = $row_job['orgName'];
                    $temporary_job = $row_job['is_temp'];
                    $query_booked = "";
                    if ($val != 'translation') {
                        $gender_required = $row_job['gender'];
                        $assignTime = $row_job['assignTime'];
                        $assignDur = $row_job['assignDur'];
                        $dur_in_hr = $assignDur / 60;
                        $assignTime_req = substr($assignTime, 0, 5);
                        $replaced_time = str_replace(':', '.', $assignTime_req);
                        $query_booked = "SELECT id,assignDate,assignTime,assignDur,REPLACE(substr(assignTime,1,5),':','.') as new_time FROM $val where intrpName='$interp_id' and assignDate='$assignDate' and (REPLACE(substr(assignTime,1,5),':','.')=($replaced_time) OR REPLACE(substr(assignTime,1,5),':','.')=($replaced_time+$dur_in_hr)) AND deleted_flag=0 AND order_cancel_flag=0 and orderCancelatoin=0";
                    } else {
                        $gender_required = '';
                        $query_booked = "SELECT id FROM $val where intrpName='$interp_id' and asignDate='$assignDate' AND deleted_flag=0 AND order_cancel_flag=0 and orderCancelatoin=0";
                    }
                    $result_booked = mysqli_query($con, $query_booked);
                    if (mysqli_num_rows($result_booked) > 0) {
                        $allot = 'no';
                    } else {
                        if ($val == 'translation') {
                            $allot = 'yes';
                        } else {
                            $query_booked = "SELECT id,assignDate,assignTime,assignDur/60 as assignDur,REPLACE(substr(assignTime,1,5),':','.') as new_time FROM $val where intrpName='$interp_id' and assignDate='$assignDate' AND deleted_flag=0 AND order_cancel_flag=0 and orderCancelatoin=0";
                            $result_booked = mysqli_query($con, $query_booked);
                            if (mysqli_num_rows($result_booked) == 0) {
                                $allot = 'yes';
                            } else {
                                $allot_array = array();
                                while ($row_booked = mysqli_fetch_assoc($result_booked)) {
                                    if ($replaced_time > $row_booked['new_time']) {
                                        $get_dur = $replaced_time - ($row_booked['new_time'] + $row_booked['assignDur']);
                                        if ($get_dur >= 0.30) {
                                            array_push($allot_array, "yes");
                                        } else {
                                            array_push($allot_array, "no");
                                        }
                                    } else {
                                        $get_dur = $row_booked['new_time'] - ($replaced_time + $dur_in_hr);
                                        if ($get_dur >= 0.30) {
                                            array_push($allot_array, "yes");
                                        } else {
                                            array_push($allot_array, "no");
                                        }
                                    }
                                }
                                if (in_array("no", $allot_array) && !in_array("yes", $allot_array)) {
                                    $allot = "no";
                                } else if (!in_array("no", $allot_array) && in_array("yes", $allot_array)) {
                                    $allot = "yes";
                                } else if (!in_array("no", $allot_array) && !in_array("yes", $allot_array)) {
                                    $allot = "yes";
                                } else if (in_array("no", $allot_array) && in_array("yes", $allot_array)) {
                                    $allot = "no and yes";
                                } else {
                                    $allot = "yes";
                                    $msg_cont_office = '<br>Contact with LSUK office';
                                }
                            }
                        }
                    }

                    $black_listed = $acttObj->read_specific("count(*) as black_listed", "interp_blacklist", "interpName='" . $codeid . "' AND orgName='" . $orgNameForJob . "' AND deleted_flag=0 AND blocked_for=" . $blocked_for[$val])['black_listed']; //black_listed==0;
                    $check_on_hold = $acttObj->unique_data('interpreter_reg', 'on_hold', 'id', $interp_id); //check_on_hold=='No';
                    if ($sourceForJob != 'English' && $targetForJob != 'English') {
                        $put_lang = "";
                        $query_style = '1';
                    } else if ($sourceForJob == 'English' && $targetForJob != 'English') {
                        $put_lang = "AND interp_lang.lang='$targetForJob' and interp_lang.level<3";
                        $query_style = '2';
                    } else if ($sourceForJob != 'English' && $targetForJob == 'English') {
                        $put_lang = "AND interp_lang.lang='$sourceForJob' and interp_lang.level<3";
                        $query_style = '2';
                    } else {
                        $put_lang = "";
                        $query_style = '3';
                    }
                    if ($query_style == '1') {
                        $check_lang = $acttObj->read_specific("count(interp_lang.id) as counter", "interpreter_reg,interp_lang", "interpreter_reg.code=interp_lang.code AND (SELECT COUNT(DISTINCT interp_lang.lang) FROM interp_lang WHERE interp_lang.lang IN ('" . $sourceForJob . "','" . $targetForJob . "') and interp_lang.level<3 and interp_lang.code='$codeid')=2");
                    } else if ($query_style == '2') {
                        $check_lang = $acttObj->read_specific("count(*) as counter", "interp_lang", "code='$codeid' $put_lang");
                    } else {
                        $check_lang = $acttObj->read_specific("count(*) as counter", $val, "id=$check_id");
                    }
                    //$check_lang['counter']!='';
                    $query_feedback = "SELECT ((sum(punctuality) + sum(appearance) + sum(professionalism) + sum(confidentiality) + sum(impartiality) + sum(accuracy) + sum(rapport) + sum(communication))*100) /(COUNT(interp_assess.id)*120) as result FROM interp_assess where interp_assess.interpName='$codeid'";
                    $result_feedback = mysqli_query($con, $query_feedback);
                    $row_feedback = mysqli_fetch_assoc($result_feedback); //row_feedback['result']>=60 || null;
                    $today_date = date('Y-m-d');
                    $today_plus_7 = date('Y-m-d', strtotime("+7 day"));
                    $firstday = date('Y-m-d', strtotime("this week"));
                    $more_jobs = 0;
                    if ($val == "translation") {
                        $row_count_jobs = $acttObj->read_specific("count(*) as jobs_done", "$val", "asignDate BETWEEN '" . $firstday . "' AND '" . $today_plus_7 . "' and intrpName='" . $interp_id . "' AND deleted_flag=0 AND order_cancel_flag=0 and orderCancelatoin=0");
                    } else {
                        $row_count_jobs = $acttObj->read_specific("count(*) as jobs_done", "$val", "assignDate BETWEEN '" . $firstday . "' AND '" . $today_plus_7 . "' and intrpName='" . $interp_id . "' AND deleted_flag=0 AND order_cancel_flag=0 and orderCancelatoin=0");
                    }
                    //less then 2
                    if ($row_count_jobs['jobs_done'] >= 2) {
                        $more_jobs = 1;
                    }
                    //amend counts for interpreter
                    $row_count_amend = $acttObj->read_specific("count(*) as amend_counts", "amended_records", "dated BETWEEN '" . $firstday . "' AND '" . $today_date . "' and interpreter_id='$interp_id'"); //less then 5
                    $empty_doc = 'No';
                    $query_docs = "SELECT (CASE WHEN ((interpreter_reg.active='0' AND interpreter_reg.actnow='Active') OR (interpreter_reg.active='0' AND interpreter_reg.actnow='Inactive' AND CURRENT_DATE() NOT BETWEEN interpreter_reg.actnow_time AND interpreter_reg.actnow_to)) THEN 'Yes' ELSE 'No' END) as activeness,applicationForm,agreement,crbDbs,identityDocument,gender 
                        FROM interpreter_reg where id='$interp_id'";
                    $result_docs = mysqli_query($con, $query_docs);
                    $row_docs = mysqli_fetch_assoc($result_docs);
                    $activeness = $row_docs['activeness'];
                    if (
                        empty($row_docs['applicationForm']) || empty($row_docs['agreement']) || empty($row_docs['crbDbs']) ||
                        empty($row_docs['identityDocument']) || $row_docs['identityDocument'] == 'Not Provided' ||
                        $row_docs['applicationForm'] == 'Not Provided' || $row_docs['agreement'] == 'Not Provided' ||
                        $row_docs['crbDbs'] == 'Not Provided'
                    ) {
                        $empty_doc = 'Yes';
                    } //empty_doc=='No'; and $activeness='Yes';
                    $allow_gender = 0;
                    if ($gender_required == '' || $gender_required == 'No Preference') {
                        $allow_gender = 1;
                    } else {
                        if ($row_docs['gender'] == $gender_required) {
                            $allow_gender = 1;
                        }
                    }
                    if ($val == 'interpreter') {
                        $find_string = "Face To Face";
                        $check_col = 'interp';
                    } else if ($val == 'telephone') {
                        $find_string = "Telephone";
                        $check_col = 'telep';
                    } else {
                        $find_string = "Translation";
                        $check_col = 'trans';
                    }
                    $check_ability = $acttObj->read_specific("$check_col as check_col", "interpreter_reg", "id=" . $interp_id)['check_col'];
                    $check_jobdDisp = $row_job['jobDisp'];
                    $check_jobStatus = $row_job['jobStatus'];
                    //echo $query_booked;
                    //echo ' on hold='.$check_on_hold.' and can allot='.$allot.' and black list='.$black_listed.' and feedback='.$row_feedback['result'].' and Empty docs='.$empty_doc.' and Activeness='.$activeness.' and Act range='.$act_range.' and JOBS more: '.$more_jobs.' and Job Display:'.$check_jobdDisp.' and job confirm:'.$check_jobStatus;

                    //code to place ends here
                    $bid_counter = $acttObj->read_specific('count(*) as bid_counter', 'bid', "job=" . $check_id . " and tabName='" . $val . "'")['bid_counter'];
                    if ($temporary_job == 1) {
                        $get_msg_db = $acttObj->read_specific('message', 'auto_replies', 'id=10');
                        $msg = "<div class='alert alert-warning col-md-10 col-md-offset-1 text-center h4'><b>" . $get_msg_db['message'] . "</b></div>";
                        //Check to remove auto allocation
                        // }else if($bid_counter>0 || $check_on_hold=='Yes'){
                    } else if ($bid_counter == 0 || ($bid_counter > 0 || $check_on_hold == 'Yes')) {
                        if (isset($bid_type) && $bid_type == 3){
                            $msg = "<div class='alert alert-info col-md-6 col-md-offset-3 text-center h4'><b>Alternative Availability submitted successfully!.</b></div>";
                        }  
                        if (isset($bid_type) && $bid_type == 2) {
                            $msg = "<div class='alert alert-danger col-md-6 col-md-offset-3 text-center h4'><b>Your have declined your bid on this job successfully.</b></div>";
                        } else if (isset($bid_type) && $bid_type == 1){
                            $get_msg_db = $acttObj->read_specific('message', 'auto_replies', 'id=8');
                            $replaced_msg = str_replace("NUMBER", $bid_counter + 2, $get_msg_db['message']);
                            $msg = "<div class='alert alert-info col-md-4 col-md-offset-4 text-center h4'><b>" . $replaced_msg . "</b></div>";
                        }
                        $edit_id = $acttObj->get_id('bid');
                        $acttObj->editFun('bid', $edit_id, 'job', $_GET['tracking']);
                        $acttObj->editFun('bid', $edit_id, 'tabName', $_GET['val']);
                        $acttObj->editFun('bid', $edit_id, 'bid_type', $bid_type);
                        $acttObj->editFun('bid', $edit_id, 'allocated', '0');
                        $acttObj->editFun('bid', $edit_id, 'interpreter_id', $_SESSION['web_userId']);
                        if($bid_type == 3){
                            $acttObj->editFun('bid', $edit_id, 'alternate_date', $_SESSION['alt_date']);
                            $acttObj->editFun('bid', $edit_id, 'message', $_SESSION['alt_message']);
                        }
                        if ($_GET['val'] != "translation" && !empty($gender_required)) {
                            $acttObj->editFun('bid', $edit_id, 'gender_status', $array_gender[$gender_required]);
                        }
                    } else {
                        // if($bid_type != 2 && $allow_gender==1 && $check_ability=='Yes' && $row_count_amend['amend_counts'] <= 2 && $check_jobdDisp=='1' && $check_jobStatus=='1' && $temporary_job==0 && $allot=='yes' && $black_listed==0 && $check_lang['counter']!='0' && $check_on_hold=='No' && ($row_feedback['result']>=40 || is_null($row_feedback['result'])) && $empty_doc=='No' && $activeness=='Yes' &&  $more_jobs==0){
                        // This is auto allocation code and will always false to stop auto assigning jobs
                        if (1 == 2) {
                            $edit_id = $acttObj->get_id('bid');
                            $acttObj->editFun('bid', $edit_id, 'job', $_GET['tracking']);
                            $acttObj->editFun('bid', $edit_id, 'tabName', $_GET['val']);
                            $acttObj->editFun('bid', $edit_id, 'allocated', '1');
                            $acttObj->editFun('bid', $edit_id, 'interpreter_id', $_SESSION['web_userId']);
                            if ($_GET['val'] != "translation" && !empty($gender_required)) {
                                $acttObj->editFun('bid', $edit_id, 'gender_status', $array_gender[$gender_required]);
                            }
                            if (isset($bid_type) && $bid_type == 2) {
                                $msg = "<div class='alert alert-danger col-md-6 col-md-offset-3 text-center h4'><b>Your have declined your bid on this job successfully.</b></div>";
                            } else {
                                $get_msg_db = $acttObj->read_specific('message', 'auto_replies', 'id=5');
                                $msg = "<div class='alert alert-success col-md-10 col-md-offset-1 text-center h4'><b>" . $get_msg_db['message'] . "</b></div>";
                            }
                            //$acttObj->editFun($val,$_GET['tracking'],'intrpName',$_SESSION['web_userId']);
                            if (isset($_SESSION['web_UserName'])) {
                                $auto_allocated = "Auto Allocated";
                            } else {
                                $auto_allocated = $_SESSION['UserName'];
                            }

                            $auto_date = date("Y-m-d");
                            $acttObj->editFun($val, $_GET['tracking'], 'pay_int', '1');
                            $acttObj->editFun($val, $_GET['tracking'], 'aloct_by', $auto_allocated);
                            $acttObj->editFun($val, $_GET['tracking'], 'aloct_date', $auto_date);
                            $acttObj->editFun($val, $_GET['tracking'], 'intrpName', $_SESSION['web_userId']);
                            //Update notification counter
                            $get_removals = $acttObj->read_all("*", "app_notifications", "title LIKE '%" . $_GET['tracking'] . "%' and type_key='nj' AND LOCATE('" . $find_string . "',title)>0");
                            if ($get_removals->num_rows > 0) {
                                while ($row_removals = $get_removals->fetch_assoc()) {
                                    //Update notification counter on APP
                                    $check_int_id = $acttObj->read_specific('id', 'notify_new_doc', 'interpreter_id=' . $row_removals['int_ids'])['id'];
                                    if (!empty($check_int_id) && $check_int_id > 0) {
                                        $existing_notification = $acttObj->read_specific("new_notification", "notify_new_doc", "interpreter_id=" . $row_removals['int_ids'])['new_notification'];
                                        $acttObj->update('notify_new_doc', array("new_notification" => $existing_notification - 1), array("interpreter_id" => $row_removals['int_ids']));
                                    }
                                    $acttObj->delete("app_notifications", "id=" . $row_removals['id']);
                                }
                            } ?>
                            <script src="https://code.jquery.com/jquery-1.11.3.min.js"></script>
                            <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/js/bootstrap.min.js"></script>
                            <script>
                                SendAssignEmails('<?php echo $interp_id; ?>');

                                function MM_openBrWindow(theURL, winName, features) {
                                    window.open(theURL, winName, features);
                                }

                                function SendAssignEmails(inter_id) {
                                    formURL = "/lsuk_system/ajaxsendassignemails.php?jobid=<?php echo $check_id; ?>&table=<?php echo $val; ?>&int_id=" + inter_id;

                                    $.ajax({
                                        url: formURL,
                                        type: "GET",

                                        success: function(strData, textStatus, jqXHR) {
                                            if (strData) {
                                                if (strData = 'sent') {
                                                    $('#timesheet_modal').modal("show");
                                                    var iframe = $('<iframe " height="750px" width="100%">');
                                                    iframe.attr('src', "lsuk_system/reports_lsuk/pdf/timesheet.php?update_id=<?php echo $check_id; ?>&table=<?php echo $val; ?>&emailto=<?php echo $_SESSION['email']; ?>");
                                                    $('#timesheet_modal_data').append(iframe);
                                                } else if (strData = 'not_sent') {
                                                    alert('You have wrong email. Kindly verify. Job has still assigned to you.');
                                                    ('#timesheet_modal').modal("show");
                                                    var iframe = $('<iframe " height="750px" width="100%">');
                                                    iframe.attr('src', "lsuk_system/reports_lsuk/pdf/timesheet.php?update_id=<?php echo $check_id; ?>&table=<?php echo $val; ?>&emailto=<?php echo $_SESSION['email']; ?>");
                                                    $('#timesheet_modal_data').append(iframe);
                                                } else {
                                                    alert('Error in Mailer Library. But Job has still assigned to you.Thank you');
                                                }
                                            } else {
                                                alert("SendAssignEmails: no data OK");
                                            }
                                        },
                                        error: function(jqXHR, textStatus, errorThrown) {
                                            alert("SendAssignEmails()- Something wrong with Jquery");
                                        }
                                    });


                                }
                            </script>
                            <?php } else {
                            if ($allow_gender == 0) {
                                if (isset($bid_type) && $bid_type == 2) {
                                    $msg = "<div class='alert alert-danger col-md-6 col-md-offset-3 text-center h4'><b>Your have declined your bid on this job successfully.</b></div>";
                                } else {
                                    $msg = "<div class='alert alert-warning col-md-10 col-md-offset-1 text-center h4'><b>Sorry! Only " . $gender_required . " interpreters are allowed to bid on this job. Thank you</b></div>";
                                }
                            } else if ($check_ability == 'No' || $black_listed > 0 || $check_lang['counter'] == '0' || $activeness == 'No') {
                                if (isset($bid_type) && $bid_type == 2) {
                                    $msg = "<div class='alert alert-danger col-md-6 col-md-offset-3 text-center h4'><b>Your have declined your bid on this job successfully.</b></div>";
                                } else {
                                    $get_msg_db = $acttObj->read_specific('message', 'auto_replies', 'id=9');
                                    $msg = "<div class='alert alert-warning col-md-10 col-md-offset-1 text-center h4'><b>" . $get_msg_db['message'] . "</b></div>";
                                }
                            } else {
                                $edit_id = $acttObj->get_id('bid');
                                $acttObj->editFun('bid', $edit_id, 'job', $_GET['tracking']);
                                $acttObj->editFun('bid', $edit_id, 'tabName', $_GET['val']);
                                $acttObj->editFun('bid', $edit_id, 'bid_type', $bid_type);
                                $acttObj->editFun('bid', $edit_id, 'allocated', '0');
                                $acttObj->editFun('bid', $edit_id, 'interpreter_id', $_SESSION['web_userId']);
                                if ($_GET['val'] != "translation" && !empty($gender_required)) {
                                    $acttObj->editFun('bid', $edit_id, 'gender_status', $array_gender[$gender_required]);
                                }
                                if (isset($bid_type) && $bid_type == 2) {
                                    $msg = "<div class='alert alert-danger col-md-6 col-md-offset-3 text-center h4'><b>Your have declined your bid on this job successfully.</b></div>";
                                } else {
                                    $get_msg_db = $acttObj->read_specific('message', 'auto_replies', 'id=6');
                                    $msg = "<div class='alert alert-info col-md-10 col-md-offset-1 text-center h4'><b>" . $get_msg_db['message'] . $msg_cont_office . "</b></div>";
                                }
                            }
                        }
                    }
                }
            }
        }
        //job bid ends here
        ?>

       

        <!-- begin content -->
        <section class="container-fluid">
            <!-- begin table -->
            <div class="row">
                <!-- Sidebar -->
                <?php include "account_sidebar.php"; ?>
                <div class="col-md-8 col-lg-9">
                
                 

                <?php if (isset($_SESSION['web_userId'])) { ?>
                    <div colspan="3" class="card-head-sec bg-info" align="center" style="padding: 2px; margin-bottom:5px;">
                    <h3 class="text-center">
                        <?php if (@$_GET['val'] == 'interpreter') {
                            echo 'List of Face to Face Jobs';
                        } else if (@$_GET['val'] == 'telephone') {
                            echo 'List of Voice Over Jobs';
                        } else if (@$_GET['val'] == 'translation') {
                            echo 'List of Translation Jobs';
                        } else {
                            echo 'List of all available jobs';
                        } ?>
                    </h3></div> <?php }


                        if (isset($msg) && !empty($msg)) {
                            echo '<br>' . $msg;
                        }
                         if (!empty($_SESSION['missing_docs'])): ?>
                            <div class="alert alert-danger " role="alert">
                            <p><b  class="text-danger"><i class="fa fa-exclamation-triangle"></i>You are not able to bid at the moment please update your missing documents</b></p>
                                <?php echo $_SESSION['missing_docs']; ?>
                            </div>
                            <?php unset($_SESSION['missing_docs']); 
                             endif; 
                        if (isset($_SESSION['web_userId']) || (@$_SESSION['email'] == "imran@lsuk.org" || @$_SESSION['email'] == "interpreting@lsuk.org"  ||
                            @$_SESSION['email'] == "translation@lsuk.org" || $_SESSION['interp_code'] == 'id-13')) { ?>
                    <table class="table table-bordered table-hover jobs-table">
                        <!--<caption>
                    Table Caption
                    </caption>-->
                        <thead class="bg-primary">
                            <tr>
                                <th>Job Type</th>
                                <th>Source</th>
                                <th>Target</th>
                                <th>City</th>
                                <th>Date</th>
                                <th>Time</th>
                                <th>Aprox Duration</th>
                                <?php if ($_SESSION['web_userId'] == 13) { ?><th>No of Applicants</th><?php } ?>
                                <th>Action</th>
                            </tr>
                        </thead>

                        <tbody>
                        <?php } else { ?>
                            <div class="col-md-12">
                                <h2 class="text-center">Kindly login to proceed</h2>
                                <hr />
                            </div>
                            <div class="col-md-4 col-md-offset-4">
                                <form id="login" method="post" action="#" enctype="multipart/form-data">
                                    <div class="form-group">
                                        <input type="text" name="loginEmail" id="loginEmail" value="" placeholder='Email' class="form-control" required />
                                    </div>
                                    <div class="form-group">
                                        <input type="password" name="loginPass" id="loginPass" placeholder="Password" required class="form-control" />
                                        <i id="shower" onclick="$('#loginPass').prop('type','text');$(this).hide();$('#hider').show();" class="glyphicon glyphicon-eye-open" title="Show Password" style="position:absolute;top:60px;right: 25px;"></i>
                                        <i id="hider" onclick="$('#loginPass').prop('type','password');$(this).hide();$('#shower').show();" class="glyphicon glyphicon-eye-close" title="Hide Password" style="display:none;position:absolute;top:60px;right: 25px;"></i>
                                    </div>
                                    <div class="form-group">
                                        <input type="submit" class="btn btn-primary" name="login" value="Log in" />
                                        <input type="reset" class="btn btn-warning" value="Clear" />
                                    </div>
                                    <div class="form-group ">
                                        <br>
                                        <strong><a class="text-danger" href="javascript:void(0)" onclick="document.getElementById('id01').style.display='block'">Forgot Your Password ?</a></strong>
                                    </div>
                                </form>
                            </div>
                            <!-- Modal to display record -->
                            <link rel="stylesheet" href="../lsuk_system/css/w3.css">
                            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
                            <div id="id01" class="w3-modal">
                                <div class="w3-modal-content w3-card-4 w3-animate-zoom" style="max-width:600px">
                                    <header class="w3-container w3-blue">
                                        <span onclick="document.getElementById('id01').style.display='none'" class="w3-button w3-blue  w3-display-topright">&times;</span>
                                        <h4>Recover your password</h4>
                                    </header>
                                    <div class="w3-container"><br>
                                        <h5>Enter your email to get your password</h5>
                                        <form id="frm_forgot" method="post" action="#" enctype="multipart/form-data">
                                            <div class="form-group">
                                                <input type="text" name="forgotEmail" id="forgotEmail" value="" placeholder='Enter your Email to get your password' class="form-control" required />
                                            </div>
                                            <button class="w3-button w3-blue" type="submit" name="btn_forgot">Send Now<i class="w3-margin-left fa fa-lock"></i></button><br><br>
                                    </div>


                                    <div class="w3-container w3-light-grey w3-padding">
                                        <button class="w3-button w3-right w3-white w3-border" onclick="document.getElementById('id01').style.display='none'">Close</button>
                                    </div>
                                </div>
                            </div>
                            <script>
                                // Get the modal
                                var modal = document.getElementById('id01');

                                // When the user clicks anywhere outside of the modal, close it
                                window.onclick = function(event) {
                                    if (event.target == modal) {
                                        modal.style.display = "none";
                                    }
                                }
                            </script>
                            <?php }
                        $table = @$_GET['val'];
                        $interp_code = $_SESSION['interp_code'];
                        $gender = $_SESSION['gender'];
                        if ($gender == 'Female') {
                            $gender_req = 'Male';
                        } else {
                            $gender_req = 'Female';
                        }

                        if (
                            @$_SESSION['email'] == "imran@lsuk.org" || @$_SESSION['email'] == "interpreting@lsuk.org"  ||
                            @$_SESSION['email'] == "translation@lsuk.org" || $_SESSION['interp_code'] == 'id-13'
                        ) {
                            //staff login
                            $query = "SELECT * FROM $table where $table.deleted_flag=0 and $table.order_cancel_flag=0 and $table.orderCancelatoin=0 and jobStatus= 1 and intrpName= '' and jobDisp= 1 and is_temp=0 LIMIT {$startpoint} , {$limit}";
                        } else {
                            if ($table != 'translation') {
                                if ($table == 'interpreter') {
                                    $append_extra_f2f_check = " AND ((interpreter_reg.uk_citizen=1 AND interpreter_reg.id_doc_expiry_date != '1001-01-01' AND interpreter_reg.id_doc_expiry_date > CURRENT_DATE()) OR (interpreter_reg.uk_citizen=0 AND interpreter_reg.work_evid_expiry_date != '1001-01-01' AND interpreter_reg.work_evid_expiry_date > CURRENT_DATE())) 
                                    AND (interpreter_reg.is_dbs_auto=1 OR (interpreter_reg.is_dbs_auto=0 AND interpreter_reg.dbs_expiry_date != '1001-01-01' AND interpreter_reg.dbs_expiry_date > CURRENT_DATE())) ";
                                    $wk_type = 'interp';
                                    $table_name = "'Face To Face' as job_type";
                                    $query_details = "'' as comunic,$table.assignDate,$table.assignTime,$table.assignDur,$table.noty,$table.assignCity";
                                } else {
                                    $wk_type = 'telep';
                                    $table_name = "'" . ucwords($table) . "' as job_type";
                                    $query_details = "$table.comunic,$table.assignDate,$table.assignTime,$table.assignDur,$table.noty";
                                }
                                $put = isset($_GET['val']) && $_GET['val'] != "translation" ? "and $table.gender!= '$gender_req'" : " ";
                            } else {
                                $wk_type = 'trans';
                                $table_name = "'" . ucwords($table) . "' as job_type";
                                $query_details = "'' as comunic,$table.asignDate as assignDate,'' as noty,(SELECT trans_cat.tc_title from trans_cat WHERE trans_cat.tc_id=docType) as document_type";
                                $put = " ";
                            }
                            if (isset($table)) {
                                $dateCol = $table == 'translation' ? 'asignDate' : 'assignDate';
                                $query = "SELECT $table_name,$table.id,$table.nameRef,$table.source,$table.target,$table.orgName,$query_details FROM $table,interpreter_reg where $table.deleted_flag=0 and $table.order_cancel_flag=0 and $table.orderCancelatoin=0 and $table.jobStatus= 1 and $table.intrpName= '' " . $put . " and $table.jobDisp= 1 and interpreter_reg.code='$interp_code' AND interpreter_reg.$wk_type='Yes' $append_extra_f2f_check and interpreter_reg.active='0' AND (interpreter_reg.actnow='Active' OR (interpreter_reg.actnow='Inactive' AND $table.$dateCol NOT BETWEEN interpreter_reg.actnow_time AND interpreter_reg.actnow_to)) AND interpreter_reg.deleted_flag=0";
                            } else {

                                $query = "SELECT '' as comunic,'Face To Face' as job_type,interpreter.id,interpreter.nameRef,interpreter.source,interpreter.target,interpreter.assignDate,interpreter.assignTime,interpreter.assignDur,interpreter.orgName,interpreter.noty,interpreter.assignCity,'' as document_type FROM interpreter,interpreter_reg where interpreter.deleted_flag=0 and interpreter.order_cancel_flag=0 and interpreter.orderCancelatoin=0 and interpreter.jobStatus= 1 and interpreter.is_temp= 0 and interpreter.intrpName= '' and interpreter.gender!= '$gender_req' and interpreter.jobDisp= 1 and interpreter_reg.code='$interp_code' AND interpreter_reg.interp='Yes' $append_extra_f2f_check and interpreter_reg.active='0' AND (interpreter_reg.actnow='Active' OR (interpreter_reg.actnow='Inactive' AND interpreter.assignDate NOT BETWEEN interpreter_reg.actnow_time AND interpreter_reg.actnow_to)) AND interpreter_reg.deleted_flag=0 UNION 
                                SELECT telephone.comunic,'Telephone' as job_type,telephone.id,telephone.nameRef,telephone.source,telephone.target,telephone.assignDate,telephone.assignTime,telephone.assignDur,telephone.orgName,telephone.noty,'' as assignCity,'' as document_type FROM telephone,interpreter_reg where telephone.deleted_flag=0 and telephone.order_cancel_flag=0 and telephone.orderCancelatoin=0 and telephone.jobStatus= 1 and telephone.is_temp= 0 and telephone.intrpName= '' and telephone.gender!= '$gender_req' and telephone.jobDisp= 1 and interpreter_reg.code='$interp_code' AND interpreter_reg.telep='Yes' and interpreter_reg.active='0' AND (interpreter_reg.actnow='Active' OR (interpreter_reg.actnow='Inactive' AND telephone.assignDate NOT BETWEEN interpreter_reg.actnow_time AND interpreter_reg.actnow_to)) AND interpreter_reg.deleted_flag=0 UNION 
                                SELECT '' as comunic,'Translation' as job_type,translation.id,translation.nameRef,translation.source,translation.target,translation.asignDate as assignDate,'' as assignTime,'' as assignDur,translation.orgName,'' as noty,'' as assignCity,(SELECT trans_cat.tc_title from trans_cat WHERE trans_cat.tc_id=docType) as document_type FROM translation,interpreter_reg where translation.deleted_flag=0 and translation.order_cancel_flag=0 and translation.orderCancelatoin=0 and translation.jobStatus= 1 and translation.is_temp= 0 and translation.intrpName='' and translation.jobDisp= 1 and interpreter_reg.code='$interp_code' AND interpreter_reg.trans='Yes' and interpreter_reg.active='0' AND (interpreter_reg.actnow='Active' OR (interpreter_reg.actnow='Inactive' AND translation.asignDate NOT BETWEEN interpreter_reg.actnow_time AND interpreter_reg.actnow_to)) AND interpreter_reg.deleted_flag=0";
                            }
                        }
                        $result = mysqli_query($con, $query);
                        if (!isset($_GET['val']) || !isset($_GET['tracking'])) {
                            echo $postformat_rows; // Job from post format look for Post_format.php in lsuk_system
                        }
                        
                        while ($row = mysqli_fetch_assoc($result)) {

                            //34060 61 62
                            if ($row['job_type'] == 'Translation') {
                                $job_cat = 'translation';
                            } else if ($row['job_type'] == 'Telephone') {
                                $job_cat = 'telephone';
                                $get_channel = $acttObj->read_specific("c_title,c_image", "comunic_types", "c_id=" . $row['comunic']);
                                $communication_type = empty($row['comunic']) || $row['comunic'] == 11 ? "Telephone" : $get_channel['c_title'];
                                $channel_img = file_exists('lsuk_system/images/comunic_types/' . $get_channel['c_image']) ? '<img src="lsuk_system/images/comunic_types/' . $get_channel['c_image'] . '" width="25" class="pull-right"/>' : '';
                            } else {
                                $job_cat = 'interpreter';
                            }
                            if ($_SESSION['web_userId'] != 13) {
                                $check_apply = $acttObj->read_specific('id,bid_type,job', 'bid', "job= " . $row['id'] . " AND tabName='" . $job_cat . "' AND interpreter_id='" . $_SESSION['web_userId'] . "' ");
                                if ($check_apply['bid_type'] == 2 ) {
                                    continue;
                                }
                                else if($check_apply['bid_type'] == 2)
                                    echo "hold on";
                            }
                            if (!empty($row['noty'])) {
                                $noty = explode(",", $row['noty']);
                                if (!in_array($_SESSION['web_userId'], $noty)) {
                                    continue;
                                }
                            }

                            $chk_blk = 0;
                            $blk = 0;
                            if (trim(strtolower($row['job_type'])) == strtolower('Face to Face')) {
                                $blk = 1;
                                $check_col="interp";
                            }
                            if (trim(strtolower($row['job_type'])) == strtolower('Telephone')) {
                                $blk = 1;
                                $check_col="telep";
                            } else if (trim(strtolower($row['job_type'])) == strtolower('Translation')) {
                                $blk = 2;
                                $check_col="trans";
                            }
                            $chk_blk = $acttObj->read_specific("count(*) as black_listed", "interp_blacklist", "interpName='id-" . $_SESSION['web_userId'] . "' AND orgName='" . $row['orgName'] . "' AND deleted_flag=0 AND blocked_for=$blk ")['black_listed'];
                            if ($chk_blk > 0) {
                                continue;
                            }

                            if ($row['source'] != $row['target'] && $row['source'] != 'English' && $row['target'] != 'English') {
                                $lang_checker = $acttObj->read_specific("COUNT(DISTINCT interp_lang.lang) as counter", "interp_lang", "interp_lang.lang IN ('" . $row['source'] . "','" . $row['target'] . "') and interp_lang.level<3 AND interp_lang.type='$chcek_col' and interp_lang.code='$interp_code'")['counter'];
                                $allow_int = $lang_checker >= 2 ? "yes" : "no";
                            } else if ($row['source'] == 'English' && $row['target'] != 'English') {
                                $lang_checker = $acttObj->read_specific("COUNT(DISTINCT interp_lang.lang) as counter", "interp_lang", "interp_lang.lang='" . $row['target'] . "' and interp_lang.level<3 AND interp_lang.type='$check_col' and interp_lang.code='$interp_code'")['counter'];
                                $allow_int = $lang_checker == 1 ? "yes" : "no";
                            } else if ($row['source'] != 'English' && $row['target'] == 'English') {
                                $lang_checker = $acttObj->read_specific("COUNT(DISTINCT interp_lang.lang) as counter", "interp_lang", "interp_lang.lang='" . $row['source'] . "' and interp_lang.level<3 AND interp_lang.type='$check_col' and interp_lang.code='$interp_code'")['counter'];
                                $allow_int = $lang_checker == 1 ? "yes" : "no";
                            } else if ($row['source'] == $row['target'] && $row['source'] != 'English') {
                                $lang_checker = $acttObj->read_specific("COUNT(DISTINCT interp_lang.lang) as counter", "interp_lang", "interp_lang.lang='" . $row['source'] . "' and interp_lang.level<3 AND interp_lang.type='$check_col' and interp_lang.code='$interp_code'")['counter'];
                                $allow_int = $lang_checker >= 1 ? "yes" : "no";
                            } else {
                                $lang_checker = 0;
                                $allow_int = "no";
                            }
                            if ($allow_int == "yes") {
                                $job_trck = $row['id'];
                                $job_count = $acttObj->read_specific('count(*) as job', 'bid', "job= $job_trck AND tabName='" . $job_cat . "'")['job'];
                            ?>

                                <tr title="Tracking:<?php echo $row['id']; ?>">
                                    <td><?php echo $communication_type ? "<span style='font-size: 100%;'>" . $communication_type . "</span>" . $channel_img : $row['job_type']; ?></td>
                                    <td><?php echo $row['source']; ?></td>
                                    <td><?php echo $row['target']; ?></td>
                                    <th><?php echo $row['assignCity'] ?: 'Nil'; ?></th>
                                    <td><?php echo $misc->dated($row['assignDate']); ?></td>
                                    <td><?php echo $row['assignTime'] ?: 'Nil'; ?></td>
                                    <td><?php $assignDur = $row['assignDur'];
                                        if ($assignDur > 60) {
                                            $hours = $assignDur / 60;
                                            if (floor($hours) > 1) {
                                                $hr = "hours";
                                            } else {
                                                $hr = "hour";
                                            }
                                            $mins = $assignDur % 60;
                                            if ($mins == 0) {
                                                $get_dur = sprintf("%2d $hr", $hours);
                                            } else {
                                                $get_dur = sprintf("%2d $hr %02d minutes", $hours, $mins);
                                            }
                                        } else if ($assignDur == 60) {
                                            $get_dur = "1 Hour";
                                        } else {
                                            $get_dur = $assignDur . " minutes";
                                        }
                                        echo $row['job_type'] == 'Translation' ? '---' : $get_dur; ?></td>
                                    <?php if ($_SESSION['web_userId'] == 13) { ?><td><?php echo $job_count; ?></td><?php } ?>
<td>
    <?php if ($check_apply['bid_type'] == "1"): ?>
        <span class="btn btn-secondary">Applied</span>
    <?php elseif ($check_apply['bid_type'] == "3"): ?>
        <span class="">Alternative Availability Given</span>
    <?php else: ?>
        <a class="btn btn-success" onclick="return confirm('Are you sure you want to apply on this job?')" 
           href="jobs.php?val=<?php echo $job_cat; ?>&tracking=<?php echo $row['id']; ?>&bid_type=1">Apply</a>
        
        <a class='btn btn-info altAvailabilityBtn' 
           href='#' 
           data-toggle='modal' 
           data-target='#altAvailabilityModal' 
           data-table='<?php echo $job_cat; ?>'
           data-pf='0' 
           data-jobid='<?php echo $row['id']; ?>'>Alternative Availability</a>
                   <a class="btn btn-danger" onclick="return confirm('Are you sure you want to decline this job?')" 
           href="jobs.php?val=<?php echo $job_cat; ?>&tracking=<?php echo $row['id']; ?>&bid_type=2">Decline</a>

        <?php if (
            @$_SESSION['email'] == "imran@lsuk.org" || @$_SESSION['email'] == "interpreting@lsuk.org" ||
            @$_SESSION['email'] == "translation@lsuk.org" || $_SESSION['interp_code'] == 'id-13'
        ) { ?>
            <a class="button blue-2" href="no_of_applicants.php?tracking=<?php echo $row['id']; ?>&table=<?php echo $table; ?>">Applicants</a>
        <?php } ?>
    <?php endif; ?>
</td>
                                </tr>
                        <?php }
                        } ?>
                        </tbody>
                    </table>
                    </div>
            </div>
            <!-- Modal to display timesheet -->
            <div class="modal modal-info fade col-md-12" data-toggle="modal" data-target=".bs-example-modal-lg" id="timesheet_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;">
                <div class="modal-dialog" role="document" style="width:55%;">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title" id="myModalLabel">Timesheet Details</h4>
                        </div>
                        <div class="modal-body" id="timesheet_modal_data">
                        </div>
                        <div class="modal-footer bg-default bg-light-ltr">
                            <button type="button" class="btn  btn-primary pull-right" data-dismiss="modal"> Close</button>
                        </div>
                    </div>
                </div>
            </div>
            <script>
                $("#timesheet_modal").on('hide.bs.modal', function() {
                    $('#timesheet_modal_data').html(' ');
                    window.location.href = 'jobs.php?val=' + '<?php echo $val; ?>';
                });
            </script>
            <!--End of modal-->
            <hr>

            <!-- begin clients -->
            <?php //include 'source/our_client.php'; 
            ?>
            <!-- end clients -->
        </section>
        <!-- end content -->

        <!-- begin footer -->
        <?php include 'source/footer.php'; ?>
        <!-- end footer -->
    </div>
    <!-- end container -->
</body>
<script src="lsuk_system/js/jquery-1.11.3.min.js"></script>
<script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.20/js/dataTables.bootstrap.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
<script>
    $(document).ready(function() {
        $('.jobs-table').DataTable();
    });
</script>

</html> 