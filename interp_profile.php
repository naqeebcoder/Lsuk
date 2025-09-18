<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
$json = '[
  {
    "id": "agreement",
    "label": "Agreement",
    "fields": [
      { "type": "file", "name": "agreement_file", "label": "Upload Agreement" }
    ]
  },
  {
    "id": "photo",
    "label": "Photo",
    "fields": [
      { "type": "file", "name": "photo_file", "label": "Upload Profile Photo" }
    ]
  },
  {
    "id": "right_to_work",
    "label": "Right to Work",
    "fields": [
      { "type": "file", "name": "rtw_file", "label": "Upload Document" },
      { "type": "date", "name": "valid_from", "label": "Valid From" },
      { "type": "date", "name": "valid_to", "label": "Valid To" }
    ]
  },
  {
    "id": "identity_document",
    "label": "Identity Document",
    "fields": [
      { "type": "file", "name": "id_file", "label": "Upload Identity Document" },
      { "type": "select", "name": "country_of_issue", "label": "Country of Issue", "options": "countries" },
      { "type": "date", "name": "valid_from", "label": "Valid From" },
      { "type": "date", "name": "valid_to", "label": "Valid To" }
    ]
  },
  {
    "id": "dbs",
    "label": "DBS Document",
    "fields": [
      { "type": "file", "name": "dbs_file", "label": "Upload DBS Certificate" },
      { "type": "text", "name": "dbs_auto_number", "label": "DBS Auto Number" }
    ]
  },
  {
    "id": "country_of_origin",
    "label": "Country of Origin",
    "fields": [
      { "type": "select", "name": "country_of_origin", "label": "Select Country", "options": "countries" }
    ]
  },
  {
    "id": "address",
    "label": "Address",
    "fields": [
      { "type": "textarea", "name": "address", "label": "Enter Address" }
    ]
  },
  {
    "id": "dob",
    "label": "Date of Birth",
    "fields": [
      { "type": "date", "name": "dob", "label": "Date of Birth" }
    ]
  },
  {
    "id": "bank_details",
    "label": "Bank Details",
    "fields": [
      { "type": "text", "name": "account_name", "label": "Account Holder Name" },
      { "type": "text", "name": "account_number", "label": "Account Number" },
      { "type": "text", "name": "sort_code", "label": "Sort Code" }
    ]
  },
  {
    "id": "ni_or_utr",
    "label": "NI or UTR Number",
    "fields": [
      { "type": "text", "name": "ni_utr", "label": "NI or UTR Number", "maxlength": 10, "auto_tab": true }
    ]
  }
]
';

// Filter only the missing requirements
$missing = array_filter($requirements, fn($req) => in_array($req['id'], $missing_ids));
require 'lsuk_system/phpmailer/vendor/autoload.php';
if (session_id() == '' || !isset($_SESSION)) {
  session_start();
}
include 'secure.php';
$interp_code = $_SESSION['interp_code'];
include 'source/db.php';
include 'source/class.php';
$requirements = json_decode($json, true);
$agreement_validity = $acttObj->read_specific(
    "*,DATE(created_date) as date_group",
    "audit_logs",
    "table_name='email_format' AND record_id=41 ORDER BY id DESC LIMIT 1"
)['date_group'] ?? '2010-01-01';
$missing_docs = $acttObj->read_specific(
                "ir.id AS interpreter_id,
                    ir.name,
                    ir.email,
                    ir.interp AS face_to_face,
                    TRIM(BOTH ', ' FROM CONCAT(
                        CASE WHEN ir.agreement = 'Hard Copy' OR ir.signature_date < '$agreement_validity'  THEN 'agreement,'  ELSE '' END,
                        CASE WHEN ir.interp_pix IS NULL OR ir.interp_pix = '' THEN 'photo,' ELSE '' END,
                        CASE WHEN ir.interp = 'Yes' AND (ir.crbDbs IS NULL OR ir.crbDbs = '') THEN 'dbs,' ELSE '' END,
                        CASE WHEN ir.interp = 'Yes' AND ir.dbs_expiry_date IS NOT NULL AND ir.dbs_expiry_date < CURDATE() THEN 'dbs,' ELSE '' END,
                        CASE WHEN ir.uk_citizen = 1 AND (ir.id_doc_no IS NULL OR ir.id_doc_no = '' 
                                OR ir.id_doc_issue_date IS NULL 
                                OR ir.id_doc_expiry_date IS NULL) THEN 'identity_document, ' ELSE '' END,
                        CASE WHEN ir.uk_citizen = 1 AND ir.id_doc_expiry_date IS NOT NULL AND ir.id_doc_expiry_date < CURDATE() THEN 'identity_document,' ELSE '' END,
                        CASE 
                              WHEN ir.uk_citizen = 0 AND (
                                  ir.right_to_work_no IS NULL OR ir.right_to_work_no = '' 
                                  OR ir.work_evid_file IS NULL OR ir.work_evid_file = '' 
                                  OR ir.work_evid_issue_date IS NULL 
                                  OR ir.work_evid_expiry_date IS NULL 
                                  OR (ir.work_evid_expiry_date IS NOT NULL AND ir.work_evid_expiry_date < CURDATE())
                              )
                              THEN 'right_to_work,' 
                              ELSE '' 
                          END,
                        CASE WHEN (ir.country_of_origin IS NULL OR ir.country_of_origin = '') THEN 'country_of_origin,' ELSE '' END,
                        CASE WHEN (ir.postCode IS NULL OR ir.postCode = '' OR ir.city = '' OR ir.city IS NULL OR ir.buildingName IS NULL OR ir.buildingName = '') THEN 'address,' ELSE '' END,
                        CASE WHEN (ir.acNo IS NULL OR ir.acNo = '' OR ir.acntCode = '' OR ir.acntCode IS NULL OR ir.acName IS NULL OR ir.acName = '' OR ir.bnakName IS NULL OR ir.bnakName = '') THEN 'bank_details,' ELSE '' END,
                        CASE WHEN (ir.dob = 0000-00-00) THEN 'dob,' ELSE '' END
                    )) AS reasons",
                "interpreter_reg ir",
                "(
                    ir.interp_pix IS NULL OR ir.interp_pix = '' OR ir.agreement = ''
                    OR (ir.interp = 'Yes' AND (ir.crbDbs IS NULL OR ir.crbDbs = ''))
                    OR (ir.interp = 'Yes' AND ir.dbs_expiry_date IS NOT NULL AND ir.dbs_expiry_date < CURDATE())
                    OR (ir.uk_citizen = 1 AND (ir.id_doc_no IS NULL OR ir.id_doc_no = '' OR ir.id_doc_issue_date IS NULL OR ir.id_doc_expiry_date IS NULL))
                    OR (ir.uk_citizen = 1 AND ir.id_doc_expiry_date IS NOT NULL AND ir.id_doc_expiry_date < CURDATE())
                    OR (ir.uk_citizen = 0 AND (ir.right_to_work_no IS NULL OR ir.right_to_work_no = ''
                            OR ir.work_evid_file IS NULL OR ir.work_evid_file = ''
                            OR ir.work_evid_issue_date IS NULL
                            OR ir.work_evid_expiry_date IS NULL
                            OR ir.postCode IS NULL OR ir.postCode = '' OR ir.city = '' OR ir.city IS NULL OR ir.buildingName IS NULL OR ir.buildingName = ''
                            OR ir.acNo IS NULL OR ir.acNo = '' OR ir.acntCode = '' OR ir.acntCode IS NULL OR ir.acName IS NULL OR ir.acName = '' OR ir.bnakName IS NULL OR ir.bnakName = ''
                            OR ir.work_evid_expiry_date < CURDATE()
                            OR ir.country_of_origin IS NULL OR ir.country_of_origin = '' OR ir.dob = 0000-00-00))
                )AND ir.id =".$_SESSION['web_userId']);
$missing_from_db = $missing_docs['reasons']; 
$missing_ids = array_map('trim', explode(',', $missing_from_db));
$missing = array_filter($requirements, fn($req) => in_array($req['id'], $missing_ids));
$level_array = array("1" => "Native", "2" => "Fluent", "3" => "Intermediate", "4" => "Basic");
if (isset($_POST['btn_submit_ticket'])) {
  $title = mysqli_real_escape_string($con, $_POST['title']);
  $details = mysqli_real_escape_string($con, $_POST['details']);
  if (!empty($title) & !empty($details)) {
    $ticket_sent = $acttObj->insert("tickets", array("interpreter_id" => $_SESSION['web_userId'], "title" => $title, "details" => $details));
    if ($ticket_sent) {
      $msg = "<div class='alert alert-success h4 col-md-6'><h4>Your ticket has been sent. We will respond back soon.</h4></div>";
      $mail = new PHPMailer(true);
      try {
        $interpeter_email = $acttObj->read_specific("name,email", "interpreter_reg", "id=" . $_SESSION['web_userId']);
        $from_add = "hr@lsuk.org";
        $from_name = "LSUK Admin Team";
        $mail->SMTPDebug = 1;
        //$mail->isSMTP(); 
        //$mailer->Host = 'smtp.office365.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'info@lsuk.org';
        $mail->Password   = 'LangServ786';
        $mail->SMTPSecure = 'tls';
        $mail->Port       = 587;
        $mail->setFrom($from_add, $from_name);
        $mail->addAddress($interpeter_email["email"]);
        $mail->addReplyTo($from_add, $from_name);
        $mail->isHTML(true);
        $mail->Subject = "We have received your ticket.";
        $mail->Body = "Hello " . $interpeter_email["name"] . ",<br>
            We have received your ticket from LSUK App.<br>
            Title: " . $_POST['title'] . "<br>
            Details: " . $_POST['details'] . "<br>
            Submitted on: " . date('Y-m-d h:i:s') . "<br><br>
            We will resolve your issue and will respond back soon.<br>
            You can find an update to your ticket status in your tickets screen.<br>
            Thank you.<br>
            Best regards,<br>
            LSUK";
        if ($mail->send()) {
          $mail->ClearAllRecipients();
        } else {
          $msg .= "<br>Failed to send your email!";
        }
      } catch (Exception $e) {
        $msg .= "<br>Email Library Error!";
      }
      echo "<script>setTimeout(function(){ window.location.href='interp_profile.php'; }, 3000);</script>";
    } else {
      $msg = "<div class='alert alert-danger h4 col-md-6'><h4>Failed to submit your ticket! Try again.</h4></div>";
    }
  } else {
    $msg = "<div class='alert alert-danger h4 col-md-5'><h4>Kindly enter ticket title and details of your ticket!</h4></div>";
  }
}
$query = "SELECT * FROM interpreter_reg where code='$interp_code'";
$result = mysqli_query($con, $query);
$row = mysqli_fetch_array($result);
$interp_id = $row['id'];
$user_id = $_SESSION['web_userId'];
$user_name = $_SESSION['web_UserName'];
$check_noty = $acttObj->read_specific("status", "notify_new_doc", "interpreter_id = '$interp_id'")['status'];
$picture = $row['interp_pix'] ? $row['interp_pix'] : 'profile.png';
$photo_path = "lsuk_system/file_folder/interp_photo/" . $picture;
if (isset($_POST['subscribe'])) {
  $acttObj->editFun('interpreter_reg', $interp_id, 'subscribe', '1');
  $msg = "<div class='alert alert-success h4 col-md-6'>Thank you! You have successfully subscribed for bidding reminders.</div>";
  echo "<script>setTimeout(function(){ window.location.href='interp_profile.php'; }, 3000);</script>";
}
if (isset($_POST['unsubscribe'])) {
  $acttObj->editFun('interpreter_reg', $interp_id, 'subscribe', '0');
  $msg = "<div class='alert alert-warning h4 col-md-6'>You have successfully unsubscribed from bidding reminders.</div>";
  echo "<script>setTimeout(function(){ window.location.href='interp_profile.php'; }, 3000);</script>";
}
if (!file_exists($photo_path)) {
  $photo_path = "lsuk_system/file_folder/interp_photo/profile.png";
}
$interpreter_languages = array();
$result_lang = $acttObj->read_all("DISTINCT interp_lang.lang,interp_lang.level,lang.parent_id", "interp_lang, lang", "interp_lang.lang=lang.lang AND interp_lang.code='$interp_code' group by interp_lang.lang");
$lct=0;
if ($result_lang->num_rows > 0) {
  while ($row_lang = $result_lang->fetch_assoc()) {
    if (!empty($row_lang['parent_id'])) {
      $get_parent_lang = $acttObj->read_specific("lang", "lang", "id=" . $row_lang['parent_id'])['lang'];
      if (!empty($get_parent_lang)) {
        $row_lang['lang'] = $get_parent_lang;
      }
    }
    $interpreter_languages[$lct]['lang'] = $row_lang['lang'];
    $interpreter_languages[$lct]['level'] = $row_lang['level'];
    $lct++;
  }
  // $interpreter_languages = array_column($interpreter_languages, 'lang', 'level');
}
?>
<!DOCTYPE HTML>
<html class="no-js">

<head>
  <meta name="google-site-verification" content="FD3pfiOXrr6D1lGvNWqseAJrL1PMPj1nguqXAd5mFkY" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://netdna.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">
  <script src="prefixfree.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/modernizr/2.8.3/modernizr.min.js"></script>

  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>

  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <title>LSUK Limited the name to trust for Certified Translation,Professional Interpreter and Translator Bristol</title>
  <meta name="robots" content="index, follow" />
  <link rel="canonical" href="http://www.lsuk.org" />
  <link href="style.css" type="text/css" rel="stylesheet" id="main-style">
  <link href="css/responsive.css" type="text/css" rel="stylesheet">
  <link href="images/favicon.ico" type="image/x-icon" rel="shortcut icon">
  <meta http-equiv="Content-Language" content="en" />
  <meta name="description" content="Professional Interpreting and Certified Document Translation only a click away" />
  <meta name="keywords" content="Professional Interprete and translator" ,"Interpreting","Court Interpreter","Medical Interpreter","Certified Document Translation","Technical Translation","Audio and Video Transcription","Telephone Interpreting","Sign Language Interpreter","BSL Interpreter","Translation Company","Interepting Service Bristol","Professional Intereprter Bristol","Bath","Cardiff", "Newport" ,"Gloucester","Swindon","Somerset","Plymouth","Exeter">
  <meta property="og:title" content="Certified Translation and Professional Interpreter" />
  <meta property="og:description" content="Language Services UK is leading translation service provider that has and will provide certifed translator and professional Interpreter each time you would want to communicate with global markets and audiences. we will meet your expectations" />
  <meta property="fb:app_id" content="" />
  <meta property="og:image" content="http://www.lsuk.org/images/logo.png" />
  <meta property="og:type" content="website" />
  <meta property="og:url" content="http://www.lsuk.org" />
  <meta property="og:site_name" content="lsuk.org" />
  <!-- begin JS -->
  <script src="js/jquery.jcarousel.min.js" type="text/javascript"></script>


  <script type="text/javascript">
    function popupwindow(url, title, w, h) {
      var left = (screen.width / 2) - (w / 2);
      var top = (screen.height / 2) - (h / 2);
      return window.open(url, title, 'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=yes, resizable=yes, copyhistory=no, width=' + w + ', height=' + h + ', top=' + top + ', left=' + left);
    }

    function MM_openBrWindow(theURL, winName, features) {
      window.open(theURL, winName, features);
    }
  </script>
</head>

<body class="boxed">
  <!-- begin container -->
  <div id="wrap">
    <!-- begin header -->
    <?php include 'source/top_nav.php'; ?>
    <!-- end header -->
    <style>
      .container {
        width: auto;
      }
    </style>
    <!-- begin content -->
    <section class="container" style="overflow-x:auto;">
      <div class="col-md-12">
        <div class="row">
          <?= isset($msg) && !empty($msg) ? $msg : ""; ?>
        </div>
        <div class="row text-danger h4"></div>
        <?php include "account_sidebar.php"; ?>
        <div class="col-sm-8 col-md-9 col-lg-9 col-xl-9">
          <div class="row">
            <div class="tab-content">
              <div id="home" class="col-md-12 tab-pane fade in active">
                <div class="text-danger h4"><?php echo $row['pic_updated'] == 0 ? '<button onClick="popupwindow(\'profile_pix.php\',\'_blank\',950,750)" class="btn btn-danger"><span class="fa fa-warning"></span> Your profile picture needs to be fixed!</button>' : '' ?></div>
                <table class="table table-bordered table-hover">
                  <thead>
                    <td colspan="3" class="bg-info" align="center">
                      <a href="interp_assessment.php" class="btn btn-sm btn-default pull-right">View Feedbacks</a>
                      <?php if ($row['subscribe'] == '1') { ?>
                        <form action="#" method="POST"><input style="margin-right: 2px;" name="unsubscribe" title="Click if you do not want to receive email notifications!" class="btn btn-sm btn-warning pull-right" type="submit" value="Unsubscribe Notifications"></form>
                      <?php } else { ?>
                        <form action="#" method="POST"><input style="margin-right: 2px;" name="subscribe" class="btn btn-sm btn-success pull-right" title="Click to receive email notifications." type="submit" value="Subscribe Notifications"></form>
                      <?php } ?>
                      <h3>Interpreting Account Details</h3>
                    </td>
                  </thead>
                  <tbody>
                    <tr>
                      <td><strong>Date of Birth</strong></td>
                      <td>
                        <?php echo $misc->dated($row['dob']); ?>
                        <b class="pull-right">Job Profile Rating : <span>
                            <?php
                            $row_st = $acttObj->read_specific(
                              "( CASE WHEN (record<0) THEN '-1' WHEN ((record>=0 AND record<=5) OR record IS NULL) THEN '0' WHEN (record>5 AND record<=20) THEN '1' WHEN (record>20 AND record<=40) THEN '2' WHEN (record>40 AND record<=60) THEN '3' WHEN (record>60 AND record<=80) THEN '4' ELSE '5' END) as record from (SELECT (sum(punctuality)+sum(appearance)+sum(professionalism)+sum(confidentiality)+sum(impartiality)+sum(accuracy)+sum(rapport)+sum(communication))/COUNT(interp_assess.id) as record",
                              "interp_assess,interpreter_reg",
                              "interp_assess.interpName=interpreter_reg.code AND interp_assess.interpName='$interp_code') as record"
                            );
                            if ($row_st['record'] == -1) {
                              echo 'Negative Feedback';
                            }
                            if ($row_st['record'] == 0) {
                              echo 'No Feedback Received';
                            }
                            if ($row_st['record'] == 1) {
                              echo '<i class="fa fa-star text-danger"></i> ';
                            }
                            if ($row_st['record'] == 2) {
                              echo '<i class="fa fa-star text-warning"></i> <i class="fa fa-star text-warning"></i> ';
                            }
                            if ($row_st['record'] == 3) {
                              echo '<i class="fa fa-star text-info"></i> <i class="fa fa-star text-info"></i> <i class="fa fa-star text-info"></i> ';
                            }
                            if ($row_st['record'] == 4) {
                              echo '<i class="fa fa-star text-success"></i> <i class="fa fa-star text-success"></i> <i class="fa fa-star text-success"></i> <i class="fa fa-star text-success"></i> ';
                            }
                            if ($row_st['record'] == 5) {
                              echo '<i class="fa fa-star text-success"></i> <i class="fa fa-star text-success"></i> <i class="fa fa-star text-success"></i> <i class="fa fa-star text-success"></i> <i class="fa fa-star text-success"></i> ';
                            }
                            ?></span></b>
                      </td>
                    </tr>
                    <tr>
                      <td><strong>Contact Information</strong></td>
                      <td>
                        <i class="fa fa-phone"></i> <?php echo $row['contactNo']; ?>
                        <i class="fa fa-envelope col-md-offset-1"></i> <?php echo $row['email']; ?>
                        <?php echo $row['email2'] ? " | " . $row['email2'] : ""; ?>
                      </td>
                    </tr>
                    <tr>
                      <td><strong>Address</strong></td>
                      <td><?php echo $row['buildingName']; ?> <?php echo $row['line1']; ?> <?php echo $row['line2']; ?> <?php echo $row['line3']; ?> <?php echo $row['city']; ?> <?php echo $row['postCode']; ?></td>
                    </tr>
                  </tbody>
                </table>
                <table align="left" width="100%" class="table table-bordered">
                  <tr>
                    <td width="200" align="left">Face to Face Interpreting</td>
                    <td width="200" align="left"><?php echo $row['interp'] == 'Yes' ? '<span class="label label-success">Yes <i class="fa fa-check-circle"></i></span>' : '<span class="label label-danger">No <i class="fa fa-remove"></i></span>'; ?></td>
                    <td width="200" align="left">Rate per Hour</td>
                    <td width="200" align="left"><?php echo $row['rph']; ?></td>
                  </tr>
                  <tr>
                    <td width="200" align="left">Telephone Interpreting</td>
                    <td width="200" align="left"><?php echo $row['telep'] == 'Yes' ? '<span class="label label-success">Yes <i class="fa fa-check-circle"></i></span>' : '<span class="label label-danger">No <i class="fa fa-remove"></i></span>'; ?></td>
                    <td width="200" align="left">Rate per Minute</td>
                    <td width="200" align="left"><?php echo $row['rpm']; ?></td>
                  </tr>
                  <tr>
                    <td align="left">Translation</td>
                    <td align="left"><?php echo $row['trans'] == 'Yes' ? '<span class="label label-success">Yes <i class="fa fa-check-circle"></i></span>' : '<span class="label label-danger">No <i class="fa fa-remove"></i></span>'; ?></td>
                    <td align="left">Rate per Unit</td>
                    <td align="left"><?php echo $row['rpu']; ?></td>
                  </tr>
                  <style>
                    .rm {
                      font-size: 13px;
                      color: #050505;
                      margin-top: 3px;
                      padding: 7px;
                      background-color: #e5e2e2;
                    }
                  </style>
                  <tr class="bg-info hidden">
                    <td align="center" colspan="2" width="50%"><b>Interpreting Languages</b></td>
                    <td align="center" colspan="2" width="50%"><b>Interpreting Skills</b></td>
                  </tr>
                  <tr>
                    <td colspan="2" align="left">
                      <p class="text-danger"><b>Note:</b> Jobs will be awarded to <u>Fluent</u> and <u>Native</u> language speakers only</p>
                      <?php
                      if (count($interpreter_languages) == 0) { ?>
                        <span class="badge badge-info">No Languages Currently!</span>
                        <?php } else {
                          for($i=0;$i<count($interpreter_languages);$i++){?>
                          <span class="badge badge-info rm"><?php echo $interpreter_languages[$i]['lang'] . ((!empty($interpreter_languages[$i]['lang'])) ? " | " . $level_array[$interpreter_languages[$i]['level']] : ""); ?></span>&nbsp; &nbsp;
                      <?php }
                      } ?>
                    </td>
                    <td colspan="2" align="left">
                      <?php
                      $result_exp = $acttObj->read_all("id,skill", "interp_skill", "code='$interp_code'");
                      if ($result_exp->num_rows == 0) {
                        echo '<span class="badge badge-info">No Skills Currently!</span>';
                      } else {
                        while ($row_exp = $result_exp->fetch_assoc()) { ?>
                          <span class="badge badge-info rm"><?php echo $row_exp['skill']; ?></span>&nbsp; &nbsp;
                      <?php }
                      } ?>
                    </td>
                  </tr>

                  <tr class="hidden">
                    <td colspan="4" align="center" class="bg-info">
                      <span><strong>Jobs Summary</strong></span>
                    </td>
                  </tr>
                  <tr>
                    <td colspan="4" valign="top">
                      <p><?php $query_interp = "select count(interpreter.id) as jobs,round(IFNULL(sum(interpreter.hoursWorkd),0),2) as hours from interpreter WHERE interpreter.intrpName ='$interp_id' and interpreter.deleted_flag=0 and interpreter.order_cancel_flag=0 
                      UNION ALL 
                        select count(telephone.id) as jobs,round(IFNULL(sum(telephone.hoursWorkd),0),2) as hours from telephone WHERE telephone.intrpName ='$interp_id' and telephone.deleted_flag=0 and telephone.order_cancel_flag=0
                      UNION ALL
                        select count(translation.id) as jobs,round(IFNULL(sum(translation.numberUnit),0),2) as hours from translation WHERE translation.intrpName ='$interp_id' and translation.deleted_flag=0 and translation.order_cancel_flag=0";
                          $result_interp = mysqli_query($con, $query_interp);
                          $array = array();
                          while ($row_interp = mysqli_fetch_array($result_interp)) {
                            array_push($array, $row_interp);
                          } ?>
                      <ul class="list-group col-md-4">
                        <li class="list-group-item">
                          Face to Face jobs<span class="badge badge-info pull-right"><?php echo $array[0]['jobs']; ?></span>
                          <br><br>
                          Face to Face Hours <span class="badge badge-info pull-right"><?php echo $array[0]['hours']; ?></span>
                        </li>
                      </ul>
                      <ul class="list-group col-md-4">
                        <li class="list-group-item">
                          Telephone jobs <span class="badge badge-info pull-right"><?php echo $array[1]['jobs']; ?></span>
                          <br><br>
                          Telephone Hours<span class="hidden-xs">/Minutes </span><span class="badge badge-info pull-right"><?php echo $array[1]['hours']; ?></span>
                        </li>
                      </ul>
                      <ul class="list-group col-md-4">
                        <li class="list-group-item">
                          Translation jobs <span class="badge badge-info pull-right"><?php echo $array[2]['jobs']; ?></span>
                          <br><br>
                          Translation Units <span class="badge badge-info pull-right"><?php echo $array[2]['hours']; ?></span>
                        </li>
                      </ul>
                      </p>
                    </td>
                  </tr>
                </table>
                </div>
                <?php if($missing): ?>
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <div class="alert alert-danger">
                  <h3>Missing / Expired Documents</h3>
                    <ul class="list-group">
                      <?php foreach ($missing as $doc): ?>
                        <li class="list-group-item">
                          ❌ <?= htmlspecialchars($doc['label']) ?> 
                          <button class="btn btn-xs btn-primary pull-right open-modal"
                                   onclick="popupwindow('intrp_portal_edit.php',
       'Document Approval',1250,730)">
                              Update
                          </button>
                          
                        </li>
                      <?php endforeach; ?>
                    </ul>
                </div>
                </div>
                  <?php endif; ?>

              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
    <!-- begin footer -->
    <?php include 'source/footer.php'; ?>
    <!-- end footer -->
  </div>
  <!-- end container -->
<!-- Modal -->
<div id="docModal" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <form class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Upload Document</h4>
      </div>
      <div class="modal-body" id="modal-body-fields">
        <!-- fields go here -->
      </div>
      <div class="modal-footer">
        <button type="submit" class="btn btn-success">Save</button>
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </form>
  </div>
</div>

<script>
$(function(){
  $(".open-modal").on("click", function(){
    let doc = $(this).data("doc");
    let fields = "";
    doc.fields.forEach(f => {
      if(f.type === "file"){
        fields += `<div class="form-group"><label>${f.label}</label>
                   <input type="file" name="${f.name}" class="form-control"></div>`;
      } else if(f.type === "date"){
        fields += `<div class="form-group"><label>${f.label}</label>
                   <input type="date" name="${f.name}" class="form-control"></div>`;
      } else if(f.type === "text"){
        let max = f.maxlength ? `maxlength="${f.maxlength}"` : "";
        fields += `<div class="form-group"><label>${f.label}</label>
                   <input type="text" name="${f.name}" ${max} class="form-control"></div>`;
      } else if(f.type === "textarea"){
        fields += `<div class="form-group"><label>${f.label}</label>
                   <textarea name="${f.name}" class="form-control"></textarea></div>`;
      }
    });
    $("#modal-body-fields").html(fields);
    $(".modal-title").text(doc.label);
  });
});
</script>
<script>
  $(document).ready(function() {
    // $('#cardModal').modal('show');
  });
  function print_card(div_class_name) {
    $('#cardModal').modal('hide');
    var content = $("." + div_class_name).html();
    var print_window = window.open("", "Print LSUK Interpreting Card", "width=600,height=630");
    var screenWidth = screen.width;
    var screenHeight = screen.height;
    var windowWidth = 600;
    var windowHeight = 630;
    var leftPosition = (screenWidth - windowWidth) / 2;
    var topPosition = (screenHeight - windowHeight) / 2;
    print_window.moveTo(leftPosition, topPosition);
    print_window.document.write("<html><head><title>LSUK Interpreting Card</title></head><body>");
    print_window.document.write('<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">');
    print_window.document.write('<center><br><button onclick="window.print();" class="btn btn_print btn-primary btn_print">Click to Print Card</button><br><br></center>');
    print_window.document.write(content);
    print_window.document.write("</body></html>");
    print_window.document.close();
  }
</script>
<script>
document.addEventListener("DOMContentLoaded", function () {
  const interpreterId = <?php echo $_SESSION['web_userId'] ?> // Replace dynamically

  fetch(`/app/events/check_pending_invites.php?interpreter_id=${interpreterId}`)
    .then(res => res.json())
    .then(events => {
      console.log(events);
      if (events.length > 0) {
        const e = events[0]; // show first only
        document.getElementById("event_id").value = e.id;
        document.getElementById("interpreter_id").value = interpreterId;

        document.getElementById("eventDetails").innerHTML = `
          <div class="alert alert-info text-center">
            <h2 class="p-0 m-0" style="margin: 0;">${e.title}</h2>
          </div>

          <div class="row">
            <div class="col-sm-6">
              <p><strong>Date:</strong> ${formatDate(e.from_date)} to ${formatDate(e.to_date)}</p>
            </div>
            <div class="col-sm-6">
              <p><strong>Time:</strong> ${formatTime(e.from_date)} to ${formatTime(e.to_date)}</p>
            </div>
            <div class="col-sm-6">
              <p><strong>Venue:</strong> ${e.venue}</p>
            </div>
          </div>

          <div class="" style="">
           <strong>Description:</strong>${e.description}
          </div>
        `;

        $('#inviteModal').modal('show');
      }
    });
});

function formatDate(dt) {
  const d = new Date(dt);
  return d.toLocaleDateString();
}

function formatTime(dt) {
  const d = new Date(dt);
  return d.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
}
</script>
<script>
let clickedResponse = null;

// Track which response button was clicked
document.querySelectorAll("#inviteResponseForm button[type=submit]").forEach(btn => {
  btn.addEventListener("click", function () {
    clickedResponse = this.value;
  });
});

document.getElementById("inviteResponseForm").addEventListener("submit", function (e) {
  e.preventDefault();

  const formData = new FormData(this);
  formData.append("response", clickedResponse); // Explicitly add clicked button value

  fetch("/app/events/submit_invite_response.php", {
    method: "POST",
    body: formData
  })
    .then(res => res.json())
    .then(data => {
      if (data.success) {
        alert("Your response has been recorded.");
        $('#inviteModal').modal('hide'); // Bootstrap 3 way
      } else {
        alert("Something went wrong.");
      }
    })
    .catch(() => {
      alert("Server error.");
    });
});
</script>

</body>
</html>