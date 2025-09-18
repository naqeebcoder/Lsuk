<?php
//check the attachments 
$attachment_string="";
if (isset($_POST['submit']) && !empty($_FILES['supporting_files']['name'][0])) {
    $allowedExtensions = ["gif", "jpeg", "jpg", "png", "pdf", "doc", "docx", "rtf", "odt", "txt", "tiff", "bmp", "xls", "xlsx", "mp3", "wav", "ogg", "mp4", "avi", "mov", "mkv"];
    $maxSize = 20 * 1024 * 1024; // 20MB
    $errors = [];

    if (!empty($_FILES['supporting_files']['name'][0])) {
        foreach ($_FILES['supporting_files']['name'] as $key => $filename) {
            $tmpName = $_FILES['supporting_files']['tmp_name'][$key];
            $size = $_FILES['supporting_files']['size'][$key];
            $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

            if (!in_array($ext, $allowedExtensions)) {
                $errors[] = "File type not allowed: $filename";
            }

            if ($size > $maxSize) {
                $errors[] = "<p>File too large: $filename. Please email it to support@example.com.</p>";
            }
        }
    }

    if (!empty($errors)) {
        foreach ($errors as $err) {
            echo "<div style='color:red;'>$err</div>";
        }
        die();
    }else{
      //adding attachment String 
      $attachment_string ="<p><strong>Please find the attachments below.</strong><p>";
    } 
}
include 'source/db.php';
include 'source/class.php';
include 'source/setup_email.php'; 

  ?>
<!DOCTYPE HTML>
<html class="no-js">

<head>
  <link rel="stylesheet" href="https://netdna.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">
  <link href="new_theme/css/bootstrap.min.css" rel="stylesheet">
  <?php include 'source/header.php'; ?>
  <title>Translation Booking Request Form</title>
  <script src="source/jquery-2.1.3.min.js"></script>
  <script type="text/javascript" src="new_theme/js/bootstrap.min.js"></script>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" />


  <script src="source/jquery-2.1.3.min.js"></script>
  <script type="text/javascript" src="new_theme/js/bootstrap.min.js"></script>

  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <?php if ((strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== FALSE) || (strpos($_SERVER['HTTP_USER_AGENT'], 'Trident') !== FALSE)) { ?>


    <script>
      $(function() {
        $(".date_picker").datepicker({
          dateFormat: 'yy-mm-dd'
        });
      });
    </script>
  <?php } else { ?>
    <script src="lsuk_system/js/jquery-1.11.3.min.js"></script>
  <?php } ?>
  <style>
    .multiselect {
      min-width: 250px;
    }

    .multiselect-container {
      max-height: 400px;
      overflow-y: auto;
      max-width: 380px;
    }

    .sky-form select {
      -webkit-appearance: auto !important;
    }

    .multiselect-container li.active label.checkbox {
      color: white;
    }
  </style>
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
        <h1>Place an Order (Translation)</h1>
        <nav id="breadcrumbs">
          <ul>
            <li><a href="index.php">Home</a> &rsaquo;</li>
            <li><a href="<?php echo basename($_SERVER['HTTP_REFERER']); ?>"><?php echo ucwords(basename($_SERVER['HTTP_REFERER'], '.php')); ?></a> &rsaquo;</li>
            <li><?php echo ucwords(basename($_SERVER['REQUEST_URI'], '.php')); ?></li>
          </ul>
        </nav>
      </div>
  </div>
  <!-- begin page title -->

  <!-- begin content -->
  <section id="content" class="container-fluid clearfix">
    <form class="col-md-10 col-md-offset-1" action="#" method="post" enctype="multipart/form-data">
      <div class="bg-info col-xs-12 form-group">
        <h4>Work Details</h4>
      </div>
      <div class="form-group col-md-3 col-sm-6">
        <label class="select">Source Language * (Select from the list)</label>
        <select class="form-control" name="source" id="source" required=''>
          <?php
          $sql_opt = "SELECT lang FROM lang ORDER BY lang ASC";
          $result_opt = mysqli_query($con, $sql_opt);
          $options = "";
          while ($row_opt = mysqli_fetch_array($result_opt)) {
            $code = $row_opt["lang"];
            $name_opt = $row_opt["lang"];
            $options .= "<OPTION value='$code'>" . $name_opt;
          }
          ?>
          <option>--Select--</option>
          <?php echo $options; ?>
        </select>
        <?php if (isset($_POST['submit'])) {
          $source = $_POST['source'];
        } ?>
      </div>
      <div class="form-group col-md-3 col-sm-6">
        <label class="select">Target Language * (Select from the list)</label>
        <select class="form-control" name="target" id="target" required=''>
          <?php
          $sql_opt = "SELECT lang FROM lang ORDER BY lang ASC";
          $result_opt = mysqli_query($con, $sql_opt);
          $options = "";
          while ($row_opt = mysqli_fetch_array($result_opt)) {
            $code = $row_opt["lang"];
            $name_opt = $row_opt["lang"];
            $options .= "<OPTION value='$code'>" . $name_opt;
          }
          ?>
          <option>English</option>
          <option>--Select--</option>
          <?php echo $options; ?>
          </option>
        </select>
        <?php if (isset($_POST['submit'])) {
          $target = $_POST['target'];
        } ?>
      </div>
      <div class="form-group col-md-3 col-sm-6" id="div_tc">
        <label>Select Document Type *</label>
        <select name="docType" id="docType" class="form-control" onchange="get_trans_types($(this));" required>
          <?php
          $q_trans_cat = $acttObj->read_all("tc_id,tc_title", "trans_cat", "tc_status=1 ORDER BY tc_title ASC");
          $opt_tc = "";
          while ($row_tc = $q_trans_cat->fetch_assoc()) {
            $tc_id = $row_tc["tc_id"];
            $tc_title = $row_tc["tc_title"];
            $opt_tc .= "<option value='$tc_id'>" . $tc_title . "</option>";
          }
          ?>
          <option disabled selected value="">Select Document Type</option>
          <?php echo $opt_tc; ?>
        </select>
      </div>
      <div class="form-group col-md-3 col-sm-6" id="div_tt" style="display:none;">
      </div>
      <div class="form-group col-md-3 col-sm-6" id="div_td" style="display:none;">
      </div>
      <div class="form-group col-md-3 col-sm-6" id="trans_docs" style="display:none;">
          <label for="supporting_files">Upload Files</label>
          <input type="file" name="supporting_files[]" id="supporting_files" class="form-control" multiple>
      </div>
      <?php if (isset($_POST['submit'])) {
        $docType = $_POST['docType'];
      }
      if (isset($_POST['submit'])) {
        $transType = implode(",", $_POST['transType']);
      }
      if (isset($_POST['submit'])) {
        $trans_detail = implode(",", $_POST['trans_detail']);
      } ?>
      <div class="form-group col-md-3 col-sm-6">
        <label class="input">Booking Reference*</label>
        <input name="nameRef" type="text" required='' value="" required class="form-control" />
        <?php if (isset($_POST['submit'])) {
          $nameRef = $_POST['nameRef'];
        } ?>
      </div>
      <div class="bg-info col-xs-12 form-group">
        <h4>Translation Delivery / Deadline Information</h4>
      </div>
      <div class="form-group col-md-4 col-sm-6">
        <label class="input">Assignment Date*</label>
        <input name="asignDate" type="date" class="form-control date_picker" value="" required />
        <?php if (isset($_POST['submit'])) {
          $asignDate = $_POST['asignDate'];
        } ?>
      </div>
      <div class="form-group col-md-4 col-sm-6">
        <label class="select">Delivery Type * (Please Select)</label>
        <select name="deliveryType" id="deliveryType" required class="form-control">
          <option value="">--Select--</option>
          <option>Standard Service (1 -2 Weeks)</option>
          <option>Quick Service (2-3 Days)</option>
          <option>Emergency Service (1-2 Days)</option>
        </select>
        <?php if (isset($_POST['submit'])) {
          $deliveryType = $_POST['deliveryType'];
        } ?>
      </div>
      <div class="form-group col-md-4 col-sm-6">
        <label class="input">Delivery Date*</label>
        <input name="deliverDate" type="date" class="form-control date_picker" value="" required />
        <?php if (isset($_POST['submit'])) {
          $deliverDate = $_POST['deliverDate'];
        } ?>
      </div>
      <div class="bg-info col-xs-12 form-group">
        <h4>Booking Details</h4>
      </div>
      <div class="form-group col-md-3 col-sm-6">
        <label class="input">Booking Person Name*</label>
        <input name="orgContact" id="orgContact" type="text" value="<?php echo @$contactPerson; ?>" placeholder='' required='' class="form-control" />
        <?php if (isset($_POST['submit'])) {
          $orgContact = $_POST['orgContact'];
        } ?>
      </div>
      <div class="form-group col-md-3 col-sm-6">
        <label class="input">Contact Number</label>
        <input name="inchContact" id="inchContact" type="text" class="form-control long" required='' value="<?php echo @$contactNo1; ?>" />
        <?php if (isset($_POST['submit'])) {
          $inchContact = $_POST['inchContact'];
        } ?>
      </div>
      <div class="form-group col-md-3 col-sm-6">
        <label class="input">Email Address for Booking Confirmation</label>
        <input name="inchEmail" id="inchEmail" type="email" class="form-control long" placeholder='' required='' />
        <?php if (isset($_POST['submit'])) {
          $inchEmail = $_POST['inchEmail'];
        } ?>
      </div>
      <div class="form-group col-md-3 col-sm-6">
        <label class="input">Booking Refernce Name / Number / Initials </label>
        <input name="orgRef" type="text" value="" placeholder='' class="form-control" />
        <?php if (isset($_POST['submit'])) {
          $orgRef = $_POST['orgRef'];
        } ?>
      </div>
      <div class="bg-info col-xs-12 form-group">
        <h4>Booking Organization Details</h4>
      </div>
      <div class="form-group col-md-4 col-sm-6">
        <label class="input">Company / Organisation (Team / Unit Title if Part of an Organisation or Trust) <i class="fa fa-question-circle" title="Select Company / Organisation Team / Unit Name or Number"></i></label>
        <input class="form-control" name="orgName" id="orgName" type="text" value="<?php echo @$orgName; ?>" placeholder='' required />
        <?php if (isset($_POST['submit'])) {
          $orgName = $_POST['orgName'];
        } ?>
      </div>
      <div class="form-group col-md-4 col-sm-6">
        <label class="input">Building Number / Name</label>
        <input name="inchNo" id="inchNo" type="text" placeholder='' class="form-control" />
        <?php if (isset($_POST['submit'])) {
          $c14 = $_POST['inchNo'];
        } ?>
      </div>
      <div class="form-group col-md-4 col-sm-6">
        <label class="input">Address Line 1</label>
        <input name="line1" id="line1" type="text" placeholder='' value="<?php echo @$line1; ?>" class="form-control" />
        <?php if (isset($_POST['submit'])) {
          $line1 = $_POST['line1'];
        } ?>
      </div>
      <div class="form-group col-md-3 col-sm-6">
        <label class="input">Address Line 2 </label>
        <input name="line2" id="line2" type="text" placeholder='' value="<?php echo @$line2; ?>" class="form-control" />
        <?php if (isset($_POST['submit'])) {
          $line2 = $_POST['line2'];
        } ?>
      </div>
      <div class="form-group col-md-3 col-sm-6">
        <label class="input">Address Line 3 </label>
        <input name="inchRoad" id="inchRoad" type="text" value="<?php echo @$inchRoad; ?>" placeholder='' class="form-control" />
        <?php if (isset($_POST['submit'])) {
          $inchRoad = $_POST['inchRoad'];
        } ?>
      </div>
      <div class="form-group col-md-3 col-sm-6">
        <label class="select">City / Town (Please Select from List)</label>
        <select name="inchCity" class="form-control" required>
          <option>--Select--</option>
          <optgroup label="England">
            <option>Bath</option>
            <option>Birmingham</option>
            <option>Bradford</option>
            <option>Bridgwater</option>
            <option>Bristol</option>
            <option>Buckinghamshire</option>
            <option>Cambridge</option>
            <option>Canterbury</option>
            <option>Carlisle</option>
            <option>Chippenham</option>
            <option>Cheltenham</option>
            <option>Cheshire</option>
            <option>Coventry</option>
            <option>Derby</option>
            <option>Dorset</option>
            <option>Exeter</option>
            <option>Frome</option>
            <option>Gloucester</option>
            <option>Hereford</option>
            <option>Leeds</option>
            <option>Leicester</option>
            <option>Liverpool</option>
            <option>London</option>
            <option>Manchester</option>
            <option>Newcastle</option>
            <option>Northampton</option>
            <option>Norwich</option>
            <option>Nottingham</option>
            <option>Oxford</option>
            <option>Plymouth</option>
            <option>Pool</option>
            <option>Portsmouth</option>
            <option>Salford</option>
            <option>Shefield</option>
            <option>Somerset</option>
            <option>Southampton</option>
            <option>Swindon</option>
            <option>Suffolk</option>
            <option>Surrey</option>
            <option>Taunton</option>
            <option>Trowbridge</option>
            <option>Truro</option>
            <option>Warwick</option>
            <option>Wiltshire</option>
            <option>Winchester</option>
            <option>Wells</option>
            <option>Weston Super Mare</option>
            <option>Worcester</option>
            <option>Wolverhampton</option>
            <option>York</option>
          </optgroup>
          <optgroup label="Scotland">
            <option>Dundee</option>
            <option>Edinburgh</option>
            <option>Glasgow</option>
          </optgroup>
          <optgroup label="Wales">
            <option>Cardiff</option>
            <option>Newport</option>
            <option>Swansea</option>
          </optgroup>
        </select>

        <?php if (isset($_POST['submit'])) {
          $inchCity = $_POST['inchCity'];
        } ?>
        </label>
      </div>
      <div class="form-group col-md-3 col-sm-6">
        <label class="input">Post Code</label>
        <input name="inchPcode" id="inchPcode" type="text" value="<?php echo @$postCode; ?>" class="form-control" />
        <?php if (isset($_POST['submit'])) {
          $inchPcode = $_POST['inchPcode'];
        } ?>
      </div>
      <div class="bg-info col-xs-12 form-group">
        <h4>Booking Preferences</h4>
      </div>
      <div class="form-group col-md-4 col-sm-6">
        <label class="optional">Booking Status: </label><br>
        <div class="radio-inline ri"><label><input name="jobStatus" type="radio" value="0" required />
            <span class="label label-danger" style="font-size:100%;padding: .5em 0.6em 0.5em;">Enquiry <i class="fa fa-question"></i></span></label></div>
        <div class="radio-inline ri"><label><input type="radio" name="jobStatus" value="1" />
            <span class="label label-success" style="font-size:100%;padding: .5em 0.6em 0.5em;">Confirmed <i class="fa fa-check-circle"></i></span></label></div>
        <?php if (isset($_POST['submit'])) {
          $jobStatus = $_POST['jobStatus'];
          if ($jobStatus == 0) {
            $jobStatus = 'Enquiry';
          } else {
            $jobStatus = 'Confirmed';
          }
        } ?>
      </div>
      <div class="form-group col-sm-12">
        <label>NOTES (if Any):</label><br>
        <textarea class="form-control col-sm-6" name="I_Comments" rows="3"></textarea>
        <?php if (isset($_POST['submit'])) {
          $I_Comments = $_POST['I_Comments'];
        } ?>
      </div>
      <div class="form-group col-sm-12">
        <script src='https://www.google.com/recaptcha/api.js'></script>
        <div class="g-recaptcha" data-sitekey="6LextRoUAAAAAGSGzslurL5xeNDw3lDDVkxM9rZe"></div>
      </div>
      <div class="form-group col-sm-12">
        <input type="submit" name="submit" class="btn btn-lg btn-primary" value="Submit" />
      </div>
    </form>

    </div>
    <!-- end content -->

    <hr>

    <!-- begin clients -->
    <?php include 'source/our_client.php'; ?>
    <!-- end clients -->
    </div>
    <!-- end content -->

    <!-- begin footer -->
    <?php include 'source/footer.php'; ?>
    <!-- end footer -->
    </div>
    <?php
    if (isset($_POST['g-recaptcha-response']) && $_POST['g-recaptcha-response']) { //var_dump($_POST);
      $secret = '6LextRoUAAAAAPvBF31eiYCmVP7Ne8a6mSez83zl';
      $ip = $_SERVER['REMOTE_ADDR'];
      $captcha = $_POST['g-recaptcha-response'];
      $rsp = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=$secret&response=$captcha&remoteip=$ip");
      //var_dump($rsp);
      $arr = json_decode($rsp, TRUE);
      if ($arr['success']) {
        $captcha_flag = 1;
      } else {
        echo 'Spam';
      }
    }
    ?>
    <?php
    //$captcha_flag = 1;//added only for testing  please remove on production 
    if (isset($_POST['submit']) && $captcha_flag == 1) {
      $from_add = 'translationservice@lsuk.org';
      $to_client_add = $inchEmail; //<-- put your yahoo/gmail email address here
      $subject =  $source . ' Translation Request'; //"Order for Translation";
      $message =
        "<style type='text/css'>
table.myTable { 
  border-collapse: collapse; 
  }
table.myTable td, 
table.myTable th { 
  border: 1px solid yellowgreen;
  padding: 5px; 
  }
</style>

<p>Hi " . $orgContact . "</p>

<p>Below is an automatic acknowledgement of the order you place with Language Service UK Limited</p>


<p>Booking Request for Document Translation </p>

<table class='myTable'>
<tr>
<td style='border: 1px solid yellowgreen;padding:5px;'>Source Language</td>
<td style='border: 1px solid yellowgreen;padding:5px;'>" . $source . "</td>
<td style='border: 1px solid yellowgreen;padding:5px;'>Target Language</td>
<td style='border: 1px solid yellowgreen;padding:5px;'>" . $target . "</td>
</tr>

<tr>
<td style='border: 1px solid yellowgreen;padding:5px;'>Document Type</td>
<td style='border: 1px solid yellowgreen;padding:5px;'>" . $acttObj->read_specific("tc_title", "trans_cat", "tc_id=" . $docType)['tc_title'] . "</td>
<td style='border: 1px solid yellowgreen;padding:5px;'>Translation Category</td>
<td style='border: 1px solid yellowgreen;padding:5px;'>" . $acttObj->read_specific("GROUP_CONCAT(CONCAT(td_title)  SEPARATOR '<br>') as td_title", "trans_dropdown", "td_id IN (" . $transType . ")")['td_title'] . "</td>
</tr>

<tr>
<td style='border: 1px solid yellowgreen;padding:5px;' colspan='3'>Translation Type(s)</td>
<td style='border: 1px solid yellowgreen;padding:5px;'>" . $acttObj->read_specific("GROUP_CONCAT(CONCAT(tt_title)  SEPARATOR '<br>') as tt_title", "trans_types", "tt_id IN (" . $trans_detail . ")")['tt_title'] . "</td>
</tr>

<tr>
<td style='border: 1px solid yellowgreen;padding:5px;'>Assignment Date</td>
<td style='border: 1px solid yellowgreen;padding:5px;'>" . $asignDate . "</td>
<td style='border: 1px solid yellowgreen;padding:5px;'></td>
<td style='border: 1px solid yellowgreen;padding:5px;'></td>
</tr>
<tr>
<td colspan='4' align='center' style='color: black;'>More Information</td>
</tr>
<tr>
<td style='border: 1px solid yellowgreen;padding:5px;'>Develivery Type</td>
<td style='border: 1px solid yellowgreen;padding:5px;'>" . $deliveryType . "</td>
<td style='border: 1px solid yellowgreen;padding:5px;'>Delivery Date</td>
<td style='border: 1px solid yellowgreen;padding:5px;'>" . $deliverDate . "</td>
</tr>
<tr>
<td style='border: 1px solid yellowgreen;padding:5px;'>Company Name (Team / Unit Title if Part of an Organisation or Trust)</td>
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
<td style='border: 1px solid yellowgreen;padding:5px;'></td>
<td style='border: 1px solid yellowgreen;padding:5px;'></td>
</tr>
<tr>
<td colspan='4' align='center' style='color: black;'>Contact Details</td>
</tr>
<tr>
<td style='border: 1px solid yellowgreen;padding:5px;'>Contact Number</td>
<td style='border: 1px solid yellowgreen;padding:5px;'>" . $inchContact . "</td>
<td style='border: 1px solid yellowgreen;padding:5px;'>Email</td>
<td style='border: 1px solid yellowgreen;padding:5px;'>" . $inchEmail . "</td>
</tr>

<tr>
<td style='border: 1px solid yellowgreen;padding:5px;'>Notes if Any 1000 alphabets</td>
<td colspan='4' align='center' style='border: 1px solid yellowgreen;padding:5px;'>" . $I_Comments . "</td>
</tr>
</table>".$attachment_string;
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
        $mail->setFrom(setupEmail::INFO_EMAIL, 'LSUK');
        $mail->addReplyTo($from_add, 'LSUK');
        if (!empty($_FILES['supporting_files']['name'][0])) {
            foreach ($_FILES['supporting_files']['tmp_name'] as $key => $tmp_name) {
                if ($_FILES['supporting_files']['error'][$key] === UPLOAD_ERR_OK) {
                    $mail->addAttachment($tmp_name, $_FILES['supporting_files']['name'][$key]);
                }
            }
        }
		    $mail->addAddress($to_client_add);
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $message;
        if ($mail->send()) {
          $mail->ClearAllRecipients();
        }
      } catch (Exception $e) {?>
        <script>
          alert("Message could not be sent! Mailer library error.");
        </script>
      <?php }
      ?>
    <?php }?>
    <?php
    if (isset($_POST['submit']) && $captcha_flag == 1) {
      $from_add = 'translationservice@lsuk.org';
      $to_add = 'translationservice@lsuk.org';
      $subject =  $source . ' Translation Request'; //"Order for Translation";
      $message = "<p>Dear LSUK Team</p>

<p>You have received the following booking request from your website </p>

<p>Form for Translation Booking request</p>" .
        "<style type='text/css'>
table.myTable { 
  border-collapse: collapse; 
  }
table.myTable td, 
table.myTable th { 
  border: 1px solid yellowgreen;
  padding: 5px; 
  }
</style>
<table class='myTable'>
<tr>
<td style='border: 1px solid yellowgreen;padding:5px;'>Source Language</td>
<td style='border: 1px solid yellowgreen;padding:5px;'>" . $source . "</td>
<td style='border: 1px solid yellowgreen;padding:5px;'>Target Language</td>
<td style='border: 1px solid yellowgreen;padding:5px;'>" . $target . "</td>
</tr>

<tr>
<td style='border: 1px solid yellowgreen;padding:5px;'>Document Type</td>
<td style='border: 1px solid yellowgreen;padding:5px;'>" . $acttObj->read_specific("tc_title", "trans_cat", "tc_id=" . $docType)['tc_title'] . "</td>
<td style='border: 1px solid yellowgreen;padding:5px;'>Translation Category</td>
<td style='border: 1px solid yellowgreen;padding:5px;'>" . $acttObj->read_specific("GROUP_CONCAT(CONCAT(td_title)  SEPARATOR '<br>') as td_title", "trans_dropdown", "td_id IN (" . $transType . ")")['td_title'] . "</td>
<td style='border: 1px solid yellowgreen;padding:5px;'>Translation Type(s)</td>
<td style='border: 1px solid yellowgreen;padding:5px;'>" . $acttObj->read_specific("GROUP_CONCAT(CONCAT(tt_title)  SEPARATOR '<br>') as tt_title", "trans_types", "tt_id IN (" . $trans_detail . ")")['tt_title'] . "</td>
</tr>

<tr>
<td style='border: 1px solid yellowgreen;padding:5px;'>Assignment Date</td>
<td style='border: 1px solid yellowgreen;padding:5px;'>" . $asignDate . "</td>
<td style='border: 1px solid yellowgreen;padding:5px;'></td>
<td style='border: 1px solid yellowgreen;padding:5px;'></td>
</tr>
<tr>
<td colspan='4' align='center' style='color: black;'>More Information</td>
</tr>
<tr>
<td style='border: 1px solid yellowgreen;padding:5px;'>Develivery Type</td>
<td style='border: 1px solid yellowgreen;padding:5px;'>" . $deliveryType . "</td>
<td style='border: 1px solid yellowgreen;padding:5px;'>Delivery Date</td>
<td style='border: 1px solid yellowgreen;padding:5px;'>" . $deliverDate . "</td>
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
<td style='border: 1px solid yellowgreen;padding:5px;'></td>
<td style='border: 1px solid yellowgreen;padding:5px;'></td>
</tr>
<tr>
<td colspan='4' align='center' style='color: black;'>Contact Details</td>
</tr>
<tr>
<td style='border: 1px solid yellowgreen;padding:5px;'>Contact Number</td>
<td style='border: 1px solid yellowgreen;padding:5px;'>" . $inchContact . "</td>
<td style='border: 1px solid yellowgreen;padding:5px;'>Email</td>
<td style='border: 1px solid yellowgreen;padding:5px;'>" . $inchEmail . "</td>
</tr>

<tr>
<td style='border: 1px solid yellowgreen;padding:5px;'>Notes if Any 1000 alphabets</td>
<td colspan='4' align='center' style='border: 1px solid yellowgreen;padding:5px;'>" . $I_Comments . "</td>
</tr>
</table>".$attachment_string;
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
        $mail->setFrom(setupEmail::INFO_EMAIL, 'LSUK');
        $mail->addReplyTo($from_add, 'LSUK');
        $mail->addAddress("translationservice@lsuk.org");
        if (!empty($_FILES['supporting_files']['name'][0])) {
            foreach ($_FILES['supporting_files']['tmp_name'] as $key => $tmp_name) {
                if ($_FILES['supporting_files']['error'][$key] === UPLOAD_ERR_OK) {
                    $mail->addAttachment($tmp_name, $_FILES['supporting_files']['name'][$key]);
                }
            }
        }
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $message;
        if ($mail->send()) {
          $mail->ClearAllRecipients();
          echo "<script>alert('Thanks for booking with LSUK Limited. You have successfully submitted the form. You will shortly receive a confirmation email of the request. Please check your email. Any problem please get in touch with LSUK Booking Team on 01173290610.');</script>";
        } else {
          echo "<script>alert('Email not submited to LSUK!');</script>";
        }
      } catch (Exception $e) {  ?>
        <script>
          alert("Message could not be sent! Mailer library error.");
        </script>
      <?php }
      ?>
    <?php } ?>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.13/css/bootstrap-multiselect.css" rel="stylesheet" type="text/css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.13/js/bootstrap-multiselect.js" type="text/javascript"></script>
    <script>
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
            $('#trans_docs').css('display', 'block');
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
<script>
  document.getElementById('supporting_files').addEventListener('change', function (e) {
      const allowedExtensions = ["gif", "jpeg", "jpg", "png", "pdf", "doc", "docx", "rtf", "odt", "txt", "tiff", "bmp", "xls", "xlsx", "mp3", "wav", "ogg", "mp4", "avi", "mov", "mkv"];
      const maxSizeMB = 20;
      const maxSizeBytes = maxSizeMB * 1024 * 1024;

      for (const file of e.target.files) {
          const ext = file.name.split('.').pop().toLowerCase();

          if (!allowedExtensions.includes(ext)) {
              alert(`File "${file.name}" has an unsupported file type.`);
              e.target.value = ''; // reset file input
              return;
          }

          if (file.size > maxSizeBytes) {
              alert(`"${file.name}" exceeds 20MB. Please email it to us at support@example.com.`);
              e.target.value = '';
              return;
          }
      }
  });
</script>

</html>