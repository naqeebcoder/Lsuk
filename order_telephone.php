<?php
//php mailer library
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require 'lsuk_system/phpmailer/vendor/autoload.php';
$mail = new PHPMailer(true);

include 'source/db.php';
include 'source/class.php'; ?>
<!DOCTYPE HTML>
<html class="no-js">

<head>
  <?php include 'source/header.php'; ?>
  <title>Telephone Booking Request Form</title>
  <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" />
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <?php if ((strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== FALSE) || (strpos($_SERVER['HTTP_USER_AGENT'], 'Trident') !== FALSE) || (strpos($_SERVER['HTTP_USER_AGENT'], 'Firefox') !== FALSE)) { ?>
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
        $(".time_picker_dur").timepicker({
          timeFormat: 'HH:mm',
          interval: 5,
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
        <h1>Place a Telephone Interpreter Request</h1>
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
    <form class="col-md-10 col-md-offset-1" action="#" method="post">
      <div class="bg-info col-xs-12 form-group">
        <h4>Work Details</h4>
      </div>
      <center>
        <div class="form-group">
          <div class="radio-inline ri"><label><input onchange="av(this);" name="cls_checker" type="radio" value="a" />
              <span class="label label-info">Audio Interpreting</span></label></div>
          <div class="radio-inline ri"><label><input onchange="av(this);" name="cls_checker" type="radio" value="v" />
              <span class="label label-warning">Video Interpreting</span></label></div>
          <div class="radio-inline ri"><label><input onchange="av(this);" name="cls_checker" type="radio" value="b" />
              <span class="label label-success">Both</span></label></div>
        </div>
      </center>
      <div class="form-group col-md-4 col-sm-6">
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
      <div class="form-group col-md-4 col-sm-6">
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
      <div class="form-group col-md-4 col-sm-6" id="div_comunic">
        <label>Select Communication Type</label>
        <select class="form-control" name="comunic" id="comunic" required="">
          <?php
          $q_types = $acttObj->read_all("c_id,c_title,c_image", "comunic_types", "c_status=1 ORDER BY c_title");
          $options = "";
          while ($row_types = $q_types->fetch_assoc()) {
            $c_id = $row_types["c_id"];
            $c_title = $row_types["c_title"];
            $c_image = $row_types["c_image"];
            $options .= "<option value='$c_id'>" . $c_title . "</option>";
          } ?>
          <option value="">Select Type</option>
          <?php echo $options; ?>
        </select>
        <?php if (isset($_POST['submit'])) {
          $comunic = $_POST['comunic'];
        } ?>
      </div>
      <div class="form-group col-md-4 col-sm-6" id="div_tpc">
        <label>Select Telephone Category</label>
        <select name="telep_cat" id="telep_cat" class="form-control" onchange="get_telep_type($(this));" required>
          <?php
          $q_telep_cat = $acttObj->read_all("tpc_id,tpc_title", "telep_cat", "tpc_status=1 ORDER BY tpc_title ASC");
          $opt_tpc = "";
          while ($row_tpc = $q_telep_cat->fetch_assoc()) {
            $tpc_id = $row_tpc["tpc_id"];
            $tpc_title = $row_tpc["tpc_title"];
            $opt_tpc .= "<option value='$tpc_id'>" . $tpc_title . "</option>";
          }
          ?>
          <option disabled selected value="">Select Telephone Category</option>
          <?php echo $opt_tpc; ?>
        </select>
      </div>
      <div class="form-group col-md-4 col-sm-6" id="div_tpt" style="display:none;">
      </div>
      <?php if (isset($_POST['submit']) && !empty($_POST['telep_cat'])) {
        $telep_cat = $_POST['telep_cat'];
      }
      if (isset($_POST['submit']) && $_POST['telep_cat'] != '11') {
        $telep_type = implode(",", $_POST['telep_type']);
      } ?>
      <div class="form-group col-sm-8" id="div_assignIssue" style="display:none;">
        <textarea rows="3" style="display:none;" placeholder="Write Assignment Issue Here ..." name="assignIssue" class="form-control" id="assignIssue"></textarea>
        <?php if (isset($_POST['submit']) && !empty($_POST['telep_cat']) && $_POST['telep_cat'] == '11') {
          $assignIssue = $_POST['assignIssue'];
        } ?>
      </div>
      <div class="form-group col-md-4 col-sm-6">
        <label class="input">Assignment Date *</label>
        <input class="form-control date_picker" type="date" name="assignDate" id="assignDate" required='' value='' />
        <?php if (isset($_POST['submit'])) {
          $assignDate = $_POST['assignDate'];
        } ?>
      </div>
      <script type="text/javascript">
        function dur_finder() {
          var duration = '';
          var datetime = $('#assignDate').val() + ' ' + $('#assignTime').val();
          var assignH = $("#assignHour").find(":selected").val();
          var assignM = $("#assignMin").find(":selected").val();
          // console.log(assignH+":"+assignM);
          if(assignH !='' && assignM!=''){
            duration = assignH+":"+assignM;
            $('#assignDur').val(duration);
          }
          // var duration = $('#assignDur').val();
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
        <label>Assignment Time *
        </label>
        <input onkeyup="dur_finder();" name="assignTime" id="assignTime" type="time" step="300" class="form-control time_picker" required='' />
      </div>
      <div class="form-group col-md-4 col-sm-6">
        <!-- <label class="input">Assignment Duration* <i class="fa fa-question-circle" title="(Minimum 1 hour, Additional time in incremental units example 1 hour 15 minutes = 1.25)"></i></label> -->
          <label class="input">Assignment Duration <i class="fa fa-question-circle" title="Select the hours and minutes separately"></i></label>
          <div class="row">
            <div class="col-md-6 col-sm-6">
              <select onchange="dur_finder();" id="assignHour" name="assignHour" class="form-control" required>
                <option value="">--Select Hours--</option>
                <option value="00">00 Hour</option>
                <option value="01">01 Hour</option>
                <option value="02">02 Hours</option>
                <option value="03">03 Hours</option>
                <option value="04">04 Hours</option>
                <option value="05">05 Hours</option>
                <option value="06">06 Hours</option>
                <option value="07">07 Hours</option>
                <option value="08">08 Hours</option>
                <option value="09">09 Hours</option>
                <option value="10">10 Hours</option>
                <option value="11">11 Hours</option>
                <option value="12">12 Hours</option>
              </select>
            </div>
            <div class="col-md-6 col-sm-6">
              <select onchange="dur_finder();" id="assignMin" name="assignMin" class="form-control" required>
                <option value="">--Select Minutes--</option>
                <option value="00">00 Minute</option>
                <option value="01">01 Minute</option>
                <option value="02">02 Minutes</option>
                <option value="03">03 Minutes</option>
                <option value="04">04 Minutes</option>
                <option value="05">05 Minutes</option>
                <option value="06">06 Minutes</option>
                <option value="07">07 Minutes</option>
                <option value="08">08 Minutes</option>
                <option value="09">09 Minutes</option>
                <option value="10">10 Minutes</option>
                <option value="11">11 Minutes</option>
                <option value="12">12 Minutes</option>
                <option value="13">13 Minutes</option>
                <option value="14">14 Minutes</option>
                <option value="15">15 Minutes</option>
                <option value="16">16 Minutes</option>
                <option value="17">17 Minutes</option>
                <option value="18">18 Minutes</option>
                <option value="19">19 Minutes</option>
                <option value="20">20 Minutes</option>
                <option value="21">21 Minutes</option>
                <option value="22">22 Minutes</option>
                <option value="23">23 Minutes</option>
                <option value="24">24 Minutes</option>
                <option value="25">25 Minutes</option>
                <option value="26">26 Minutes</option>
                <option value="27">27 Minutes</option>
                <option value="28">28 Minutes</option>
                <option value="29">29 Minutes</option>
                <option value="30">30 Minutes</option>
                <option value="31">31 Minutes</option>
                <option value="32">32 Minutes</option>
                <option value="33">33 Minutes</option>
                <option value="34">34 Minutes</option>
                <option value="35">35 Minutes</option>
                <option value="36">36 Minutes</option>
                <option value="37">37 Minutes</option>
                <option value="38">38 Minutes</option>
                <option value="39">39 Minutes</option>
                <option value="40">40 Minutes</option>
                <option value="41">41 Minutes</option>
                <option value="42">42 Minutes</option>
                <option value="43">43 Minutes</option>
                <option value="44">44 Minutes</option>
                <option value="45">45 Minutes</option>
                <option value="46">46 Minutes</option>
                <option value="47">47 Minutes</option>
                <option value="48">48 Minutes</option>
                <option value="49">49 Minutes</option>
                <option value="50">50 Minutes</option>
                <option value="51">51 Minutes</option>
                <option value="52">52 Minutes</option>
                <option value="53">53 Minutes</option>
                <option value="54">54 Minutes</option>
                <option value="55">55 Minutes</option>
                <option value="56">56 Minutes</option>
                <option value="57">57 Minutes</option>
                <option value="58">58 Minutes</option>
                <option value="59">59 Minutes</option>

              </select>
            </div>
          </div>
          <input id="assignDur"  name="assignDur" type="hidden" pattern="[0-9 :]{5}" maxlength="5" class="form-control" value=""  placeholder="Hours : Minutes" required />
      </div>
      <div class="form-group col-md-4 col-sm-6">
        <label>Assignment End Time
        </label>
        <input id="assignEndTime" readonly="readonly" name="assignEndTime" type="text" class="form-control" value="" />
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
      <?php if (isset($_POST['submit'])) {
        $assignTime = $_POST['assignTime'];
      } ?>
      <?php if (isset($_POST['submit'])) {
        $post_dur = $_POST['assignDur'];
        list($part1, $part2) = explode(':', $post_dur);
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
            $assignDur = sprintf("%2d $hr", $hours);
          } else {
            $assignDur = sprintf("%2d $hr %02d minutes", $hours, $mins);
          }
        } else if ($total_dur == 60) {
          $assignDur = "1 Hour";
        } else {
          $assignDur = $total_dur . " minutes";
        }
      } ?>
      <div class="bg-info col-xs-12 form-group">
        <h4>Telephone Interpreting Contact Details</h4>
      </div>
      <div class="form-group col-md-4 col-sm-6">
        <label class="input">Your Contact Number <i class="fa fa-question-circle" title="IF Service User Is Not Present"></i></label>
        <input type="text" class="form-control" id="contactNo" name="contactNo" required='' />
        <?php if (isset($_POST['submit'])) {
          $contactNo = $_POST['contactNo'];
        } ?>
      </div>
      <div class="form-group col-md-4 col-sm-6">
        <label class="input">Client / Service User *</label>
        <input type="text" class="form-control" id="noClient" name="noClient" required='' />
        <?php if (isset($_POST['submit'])) {
          $noClient = $_POST['noClient'];
        } ?>
      </div>
      <div class="bg-info col-xs-12 form-group">
        <h4>Assignment Details</h4>
      </div>
      <div class="form-group col-md-4 col-sm-6">
        <label class="input">Booking Person Name if Different</label>
        <input name="inchPerson" type="text" class="long form-control" value="<?php echo @$contactPerson; ?>" />
        <?php if (isset($_POST['submit'])) {
          $inchPerson = $_POST['inchPerson'];
        } ?>
      </div>
      <div class="form-group col-md-4 col-sm-6">
        <label class="input">Assignment Incharge Name&nbsp;*</label>
        <input name="orgContact" id="orgContact" type="text" value="" placeholder='' required='' class="form-control" />
        <?php if (isset($_POST['submit'])) {
          $orgContact = $_POST['orgContact'];
        } ?>
      </div>
      <div class="form-group col-md-4 col-sm-6">
        <label class="input">Contact Number</label>
        <input name="inchContact" id="inchContact" type="text" class="long form-control" value="<?php echo @$contactNo1; ?>" />
        <?php if (isset($_POST['submit'])) {
          $inchContact = $_POST['inchContact'];
        } ?>
      </div>
      <div class="form-group col-md-4 col-sm-6">
        <label class="input">Email Address&nbsp;</label>
        <input name="inchEmail" id="inchEmail" type="email" class="long form-control" value="<?php echo @$email; ?>" placeholder='' required />
        <?php if (isset($_POST['submit'])) {
          $inchEmail = $_POST['inchEmail'];
        } ?>
      </div>
      <div class="form-group col-md-4 col-sm-6">
        <label class="input">Booking Reference Name <i class="fa fa-question-circle" title="Reference Number / Initials or Number"></i></label>
        <input class="form-control" name="orgRef" type="text" value="" placeholder='' />
        <?php if (isset($_POST['submit'])) {
          $orgRef = $_POST['orgRef'];
        } ?>
      </div>
      <div class="bg-info col-xs-12 form-group">
        <h4>Booking organization Details</h4>
      </div>
      <div class="form-group col-md-4 col-sm-6">
        <label class="input">Company / Organization (Team / Unit Title if Part of an Organisation or Trust)<i class="fa fa-question-circle" title="Team / Unit Name or Number"></i></label>
        <input name="orgName" id="orgName" type="text" value="<?php echo @$orgName; ?>" placeholder='' class="form-control" required />
        <?php if (isset($_POST['submit'])) {
          $orgName = $_POST['orgName'];
        } ?>
        </label>
      </div>
      <div class="form-group col-md-4 col-sm-6">
        <label class="input">Building Number / Name</label>
        <input name="inchNo" class="form-control" id="inchNo" type="text" value="<?php echo @$buildingName; ?>" placeholder='' />
        <?php if (isset($_POST['submit'])) {
          $inchNo = $_POST['inchNo'];
        } ?>
      </div>
      <div class="form-group col-md-4 col-sm-6">
        <label class="input">Address Line</label>
        <input name="line1" class="form-control" id="line1" type="text" placeholder='' value="<?php echo @$line1; ?>" />
        <?php if (isset($_POST['submit'])) {
          $line1 = $_POST['line1'];
        } ?>
      </div>
      <div class="form-group col-md-4 col-sm-6">
        <label class="input">Address Line 2</label>
        <input name="line2" class="form-control" id="line2" type="text" placeholder='' value="<?php echo @$line2; ?>" />
        <?php if (isset($_POST['submit'])) {
          $line2 = $_POST['line2'];
        } ?>
      </div>
      <div class="form-group col-md-4 col-sm-6">
        <label class="input">Address Line 3</label>
        <input name="inchRoad" class="form-control" id="inchRoad" type="text" value="<?php echo @$inchRoad; ?>" placeholder='' />
        <?php if (isset($_POST['submit'])) {
          $inchRoad = $_POST['inchRoad'];
        } ?>
      </div>
      <div class="form-group col-md-4 col-sm-6">
        <label class="select">City / Town (Please Select from List)</label>
        <select name="inchCity" class="form-control">
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
      </div>
      <div class="form-group col-md-4 col-sm-6">
        <label class="input">Post Code</label>
        <input name="inchPcode" class="form-control" id="inchPcode" type="text" value="<?php echo @$postCode; ?>" />
        <?php if (isset($_POST['submit'])) {
          $inchPcode = $_POST['inchPcode'];
        } ?>
      </div>
      </div>
      <div class="bg-info col-xs-12 form-group">
        <h4>Interpreter Preferences</h4>
      </div>
      <div class="form-group col-md-4 col-sm-6">
        <label class="optional">Interpreter Gender</label>
        <select name="gender" class="form-control" required>
          <option class='hidden' selected disabled>--Select--</option>
          <option value="Male">Male</option>
          <option value="Female">Female</option>
          <option value="No Preference">No Preference</option>
        </select>
        <?php if (isset($_POST['submit'])) {
          $gender = $_POST['gender'];
        } ?>
      </div>

      <div class="form-group col-md-4 col-sm-6">
        <label class="optional">Booking Status: </label><br>
        <div class="radio-inline ri"><label><input name="jobStatus" type="radio" value="0" />
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
        <b>NOTES (if Any):</b>
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
  </section>
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
  <!-- end container -->
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
  if (isset($_POST['submit']) && $captcha_flag == 1) {
    $bookedDate = date('Y-m-d');
    $from_add = "info@lsuk.org";
    $to_add = $inchEmail; //<-- put your yahoo/gmail email address here
    $subject = $source . ' Telephone Interpreter ' . $assignDate . ' ' . $assignTime; //"Order for Interpreter (F 2 F)";
    $write_telep_cat = $telep_cat == '11' ? $assignIssue : $acttObj->read_specific("tpc_title", "telep_cat", "tpc_id=" . $telep_cat)['tpc_title'];
    $write_telep_type = $telep_cat == '11' ? '' : $acttObj->read_specific("GROUP_CONCAT(CONCAT(tpt_title)  SEPARATOR ' <b> & </b> ') as tpt_title", "telep_types", "tpt_id IN (" . $telep_type . ")")['tpt_title'];
    if ($telep_cat == '11') {
      $append_issue = "<tr><td style='border: 1px solid yellowgreen;padding:5px;'>Assignment Category</td><td style='border: 1px solid yellowgreen;padding:5px;'>Other</td><td style='border: 1px solid yellowgreen;padding:5px;'>Assignment Details</td><td style='border: 1px solid yellowgreen;padding:5px;'>" . $assignIssue . "</td></tr>";
    } else {
      $append_issue = "<tr><td style='border: 1px solid yellowgreen;padding:5px;'>Assignment Category</td><td style='border: 1px solid yellowgreen;padding:5px;'>" . $write_telep_cat . "</td><td style='border: 1px solid yellowgreen;padding:5px;'>Assignment Details</td><td style='border: 1px solid yellowgreen;padding:5px;'>" . $write_telep_type . "</td></tr>";
    }
    echo $message =
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
<p>Hi " . $inchPerson . "</p>

<p>Below is an automatic acknowledgement of the order you place with Language Service UK Limited</p>

<p>Booking Request for Telephone Interpreting</p>

<table class='myTable'>
<tr>
<td style='border: 1px solid yellowgreen;padding:5px;'>Source Language</td>
<td style='border: 1px solid yellowgreen;padding:5px;'>" . $source . "</td>
<td style='border: 1px solid yellowgreen;padding:5px;'>Target Language</td>
<td style='border: 1px solid yellowgreen;padding:5px;'>" . $target . "</td>
</tr>

<tr>
<td style='border: 1px solid yellowgreen;padding:5px;'>Assignment Date / Time</td>
<td style='border: 1px solid yellowgreen;padding:5px;'>" . $assignDate . " " . $assignTime . "</td>
<td style='border: 1px solid yellowgreen;padding:5px;'>Assignment Duration</td>
<td style='border: 1px solid yellowgreen;padding:5px;'>" . $assignDur . "</td>
</tr>
<tr>
<td style='border: 1px solid yellowgreen;padding:5px;'>Booking Type</td>
<td style='border: 1px solid yellowgreen;padding:5px;'>" . $acttObj->read_specific("c_title", "comunic_types", "c_id=" . $comunic)['c_title'] . "</td>
<td style='border: 1px solid yellowgreen;padding:5px;'>Booking Date</td>
<td style='border: 1px solid yellowgreen;padding:5px;'>" . $bookedDate . "</td>
</tr>
" . $append_issue . "
<tr>
<td colspan='4' align='center' style='color: black;'>Assignment Location</td>
</tr>
<tr>
<td style='border: 1px solid yellowgreen;padding:5px;'>Number of the Client to be Called</td>
<td style='border: 1px solid yellowgreen;padding:5px;'>" . $noClient . "</td>
<td style='border: 1px solid yellowgreen;padding:5px;'>Contact No for Ph. Interpreting</td>
<td style='border: 1px solid yellowgreen;padding:5px;'>" . $contactNo . "</td>
</tr>
<tr>
<td colspan='4' align='center' style='color: black;'>Booking Organization Details</td>
</tr>
<tr>
<td style='border: 1px solid yellowgreen;padding:5px;'>Company Name (Team / Unit Title if Part of an Organisation or Trust)</td>
<td style='border: 1px solid yellowgreen;padding:5px;'>" . $orgName . "</td>
<td style='border: 1px solid yellowgreen;padding:5px;'>Booking Ref/Name</td>
<td style='border: 1px solid yellowgreen;padding:5px;'>" . $orgRef . "</td>
</tr>
<tr>
<td style='border: 1px solid yellowgreen;padding:5px;'>Assignment Incharge Name</td>
<td style='border: 1px solid yellowgreen;padding:5px;'>" . $orgContact . "</td>
<td style='border: 1px solid yellowgreen;padding:5px;'>&nbsp;</td>
<td style='border: 1px solid yellowgreen;padding:5px;'>&nbsp;</td>
</tr>
<tr>
<tr>
<td colspan='4' align='center' style='color: black;'>Assignment in-Charge</td>
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
<td colspan='4' align='center' style='color: black;'>Interpreter Details</td>
</tr>
<tr>
<td style='border: 1px solid yellowgreen;padding:5px;'>Gender</td>
<td style='border: 1px solid yellowgreen;padding:5px;'>" . $gender . "</td>
<td style='border: 1px solid yellowgreen;padding:5px;'>Status</td>
<td style='border: 1px solid yellowgreen;padding:5px;'>" . $jobStatus . "</td>
</tr>

<tr>
<td style='border: 1px solid yellowgreen;padding:5px;'>Notes if Any 1000 alphabets</td>
<td colspan='4' align='center' style='border: 1px solid yellowgreen;padding:5px;'>" . $I_Comments . "</td>
</tr>
</table>";
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
      if ($mail->send()) {
        $mail->ClearAllRecipients();
        // $mail->addAddress("waqarecp1992@gmail.com");
        // $mail->addReplyTo($from_add, 'LSUK');
        // $mail->isHTML(true);
        // $mail->Subject = $subject;
        // $mail->Body    = $message;
        // $mail->send();
        // $mail->ClearAllRecipients();
      }
    } catch (Exception $e) { ?>
      <script>
        alert("Message could not be sent! Mailer library error.");
      </script>
    <?php }
    ?>
  <?php } ?>
  <?php
  if (isset($_POST['submit']) && $captcha_flag == 1) {
    $from_add = "info@lsuk.org";
    $to_add = "info@lsuk.org"; //<-- put your yahoo/gmail email address here
    $subject = "Order for Telephone Interpreting";
    echo $message = "<p>Dear LSUK Team</p>

<p>You have received the following booking request from your website </p>

<p>Form for Telephone Interpreter Booking request</p>" .
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
<td style='border: 1px solid yellowgreen;padding:5px;'>Assignment Date / Time</td>
<td style='border: 1px solid yellowgreen;padding:5px;'>" . $assignDate . " " . $assignTime . "</td>
<td style='border: 1px solid yellowgreen;padding:5px;'>Assignment Duration</td>
<td style='border: 1px solid yellowgreen;padding:5px;'>" . $assignDur . "</td>
</tr>
<tr>
<td style='border: 1px solid yellowgreen;padding:5px;'>Booking Type</td>
<td style='border: 1px solid yellowgreen;padding:5px;'>" . $acttObj->read_specific("c_title", "comunic_types", "c_id=" . $comunic)['c_title'] . "</td>
<td style='border: 1px solid yellowgreen;padding:5px;'>Booking Date</td>
<td style='border: 1px solid yellowgreen;padding:5px;'>" . $bookedDate . "</td>
</tr>
" . $append_issue . "
<tr>
<td colspan='4' align='center' style='color: black;'>Assignment Location</td>
</tr>
<tr>
<td style='border: 1px solid yellowgreen;padding:5px;'>Client / Service User</td>
<td style='border: 1px solid yellowgreen;padding:5px;'>" . $noClient . "</td>
<td style='border: 1px solid yellowgreen;padding:5px;'>Your Contact Nunber</td>
<td style='border: 1px solid yellowgreen;padding:5px;'>" . $contactNo . "</td>
</tr>
<tr>
<td colspan='4' align='center' style='color: black;'>Booking Organization Details</td>
</tr>
<tr>
<td style='border: 1px solid yellowgreen;padding:5px;'>Company Name</td>
<td style='border: 1px solid yellowgreen;padding:5px;'>" . $orgName . "</td>
<td style='border: 1px solid yellowgreen;padding:5px;'>Booking Ref/Name</td>
<td style='border: 1px solid yellowgreen;padding:5px;'>" . $orgRef . "</td>
</tr>
<tr>
<td style='border: 1px solid yellowgreen;padding:5px;'>Assignment Incharge Name</td>
<td style='border: 1px solid yellowgreen;padding:5px;'>" . $orgContact . "</td>
<td style='border: 1px solid yellowgreen;padding:5px;'>&nbsp;</td>
<td style='border: 1px solid yellowgreen;padding:5px;'>&nbsp;</td>
</tr>
<tr>
<tr>
<td colspan='4' align='center' style='color: black;'>Assignment in-Charge</td>
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
<td colspan='4' align='center' style='color: black;'>Interpreter Details</td>
</tr>
<tr>
<td style='border: 1px solid yellowgreen;padding:5px;'>Gender</td>
<td style='border: 1px solid yellowgreen;padding:5px;'>" . $gender . "</td>
<td style='border: 1px solid yellowgreen;padding:5px;'>Status</td>
<td style='border: 1px solid yellowgreen;padding:5px;'>" . $jobStatus . "</td>
</tr>

<tr>
<td style='border: 1px solid yellowgreen;padding:5px;'>Notes if Any 1000 alphabets</td>
<td colspan='4' align='center' style='border: 1px solid yellowgreen;padding:5px;'>" . $I_Comments . "</td>
</tr>
</table>";
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
      //$mail->addAddress($to_add);
      $mail->addAddress('info@lsuk.org');
      $mail->addReplyTo($from_add, 'LSUK');
      $mail->isHTML(true);
      $mail->Subject = $subject;
      $mail->Body    = $message;
      if ($mail->send()) {
        $mail->ClearAllRecipients();
        echo "<script>alert('Thanks for booking with LSUK Limited. You have successfully submitted the form. You will shortly receive a confirmation email of the request. Please check your email. Any problem please get in touch with LSUK Booking Team on 01173290610.');</script>";
      } else {
        echo "<script>alert('Email not submited to LSUK!');</script>";
      }
    } catch (Exception $e) { ?>
      <script>
        alert("Message could not be sent! Mailer library error.");
      </script>
    <?php }
    ?>
  <?php } ?>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.13/css/bootstrap-multiselect.css" rel="stylesheet" type="text/css" />
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.13/js/bootstrap-multiselect.js" type="text/javascript"></script>
  <script type="text/javascript">
    function get_telep_type(elem) {
      var tpc_id = elem.val();
      $.ajax({
        url: 'ajax_client_portal.php',
        method: 'post',
        data: {
          tpc_id: tpc_id
        },
        success: function(data) {
          if (data) {
            $('#div_tpt').css('display', 'block');
            $('#div_assignIssue').css('display', 'none');
            $('#assignIssue').css('display', 'none');
            $('#div_tpt').html(data);
          } else {
            $('#div_tpt').html(data);
            $('#div_tpt').css('display', 'none');
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

    function av(elem) {
      var val = $(elem).val();
      var telep_checker = '1';
      if ($(elem).prop('checked')) {
        $.ajax({
          url: 'ajax_client_portal.php',
          method: 'post',
          data: {
            val: val,
            telep_checker: telep_checker
          },
          success: function(data) {
            if (data) {
              $('#div_comunic').html(data);
            }
          },
          error: function(xhr) {
            alert("An error occured: " + xhr.status + " " + xhr.statusText);
          }
        });
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