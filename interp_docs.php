<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require 'lsuk_system/phpmailer/vendor/autoload.php';
if (session_id() == '' || !isset($_SESSION)) {
  session_start();
}
include 'secure.php';
$interp_code = $_SESSION['interp_code'];
include 'source/db.php';
include 'source/class.php';
include_once('source/function.php');
$page = (int) (!isset($_GET["page"]) ? 1 : $_GET["page"]);
$limit = 20;
$startpoint = ($page * $limit) - $limit;
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
$acttObj->update("notify_new_doc", array("status" => 1), "interpreter_id='$interp_id'");
$check_noty = 1;
$query = "SELECT * FROM interpreter_reg where code='$interp_code'";
$result = mysqli_query($con, $query);
$row = mysqli_fetch_array($result);
$interp_id = $row['id'];
$user_id = $_SESSION['web_userId'];
$user_name = $_SESSION['web_UserName'];
$picture = $row['interp_pix'] ?: 'profile.png';
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
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
  <link href="style.css" type="text/css" rel="stylesheet" id="main-style">
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
  <style>
    .nav-tabs>li {
      min-width: 156px;
      border: 1px solid lightgrey;
    }

    .nav-tabs>li.active>a,
    .nav-tabs>li.active>a:focus,
    .nav-tabs>li.active>a:hover {
      color: #fff;
      background-color: #337ab7;
      border: none;
      border-bottom-color: transparent;
    }

    .nav-tabs>li>a {
      margin-right: 0px;
      border: none;
      border-radius: 0px;
    }

    nav.list-group a img {
      vertical-align: middle;
      display: inline;
    }

    .d-inline-block {
      display: inline-block !important;
    }

    .border {
      border: 2px solid #04346a;
    }

    .p-0 {
      padding: 0px;
    }

    .p-2 {
      padding: 2px;
    }

    .p-5 {
      padding: 5px;
    }

    @keyframes label-blinking {
      0% {
        opacity: 1;
      }

      50% {
        opacity: 0;
      }

      100% {
        opacity: 1;
      }
    }

    .label-blinking {
      font-size: 12px;
      animation: label-blinking 1s infinite;
    }
  </style>
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
          <div class="row" style="max-height: 500px;overflow-y: auto;">
            <div id="menu2">
              <br>
              <?php
              $query = "SELECT post_format.*,notify_new_doc_data.interpreters FROM post_format,notify_new_doc_data WHERE post_format.id=notify_new_doc_data.post_id AND post_format.status='Active' ORDER by post_format.id DESC LIMIT {$startpoint} , {$limit}";
              $result = mysqli_query($con, $query);
              while ($row = mysqli_fetch_array($result)) {
                if (empty($row['interpreters']) || (!empty($row['interpreters']) && in_array($_SESSION['web_userId'], explode(',', $row['interpreters'])))) {
                  $dated = $row['em_date'];
                  $title = $row['em_type'];
                  $em_format = $row['em_format']; ?>
                  <section class="col-md-12">
                    <article class="entry clearfix">
                      <div class="entry-date">
                        <div class="entry-day"><?php echo date('d', strtotime($dated)); ?>
                        </div>
                        <div class="entry-month"><?php echo substr(date('F', strtotime($dated)), 0, 3); ?></div>
                      </div>
                      <div class="entry-meta">
                        <span class="category">Posted on <?php echo $dated; ?></span>
                      </div>
                      <div class="entry-body">
                        <div="entry-title">
                          <h3><?php echo $title; ?></h3>
                      </div>
                      <div class="entry-content">
                        <?php echo $em_format; ?>
                      </div>
                      <hr>
                    </article>
                  </section>
              <?php }
              }
              ?>
            </div>
          </div>
          <div><?php echo pagination($con, $table, $query, $limit, $page); ?></div>
        </div>
      </div>
    </section>
    <!-- begin footer -->
    <?php include 'source/footer.php'; ?>
    <!-- end footer -->
  </div>
  <!-- end container -->
</body>

</html>