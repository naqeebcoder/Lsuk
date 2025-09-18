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
            <div class="tab-content text-right">
            <a href="print_interp_card.php" target="_blank" type="button" class="btn btn-primary"><i class="fa fa-print"></i> Print</a>
              <div id="home" class="col-md-12 tab-pane fade in active" style="background-image: url(images/interpreter/card22.png);height: 30rem;background-repeat: no-repeat;background-size: contain;background-position-x: center;">
                <div class="row">
                    <div class="col-md-6">
                        <div class="img_and_name" style="margin: 0 auto;width: 10rem;position: relative;top: 8rem;left: 5.5rem;">
                            <img src="<?php echo $photo_path; ?>" alt="Profile_Picture" class="text-center" style="border-radius: 5rem;width:100%;">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="description" style="position: relative;top: 12rem;right: 7rem;width: 50%;text-align: center;">
                            <p class="intrpName" style="font-size: 1.8rem;line-height: 1.5rem;color: #3d519b;line-height: 2rem;"><?php echo strtoupper($row['name']); ?></p>
                        </div>
                    </div>
                </div>
                
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
</body>
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
</html>