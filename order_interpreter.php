<?php
include 'source/setup_email.php';
include 'source/db.php';
include 'source/class.php'; ?>

<!DOCTYPE HTML>
<html class="no-js">

<head>


  <link rel="stylesheet" href="https://netdna.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">
  <link href="new_theme/css/bootstrap.min.css" rel="stylesheet">



  <script src="prefixfree.min.js"></script>
  <script src="source/modernizr.min.js"></script>
  <script type="text/javascript" src="https://code.jquery.com/jquery-2.2.3.min.js"></script>
  <script type="text/javascript" src="new_theme/js/bootstrap.min.js"></script>
  <script type="text/javascript" src="new_theme/js/mdb.min.js"></script>



  <!-- <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" />
<link rel="stylesheet" href="http://netdna.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">
<link href="new_theme/css/bootstrap.min.css" rel="stylesheet">   -->
  <?php include 'source/header.php'; ?>
  <title>Face To Face Booking Request Form</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <?php if ((strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== FALSE) || (strpos($_SERVER['HTTP_USER_AGENT'], 'Trident') !== FALSE) || (strpos($_SERVER['HTTP_USER_AGENT'], 'Firefox') !== FALSE)) { ?>
    <!-- <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/timepicker/1.3.5/jquery.timepicker.min.css">
<script src="//cdnjs.cloudflare.com/ajax/libs/timepicker/1.3.5/jquery.timepicker.min.js"></script> -->
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
  <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" />
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
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

        <h1>Place an Order (Face to Face Interpreter)</h1>

        <nav id="breadcrumbs">

          <ul>

            <li><a href="index.php">Home</a> &rsaquo;</li>

            <li><a href="<?php echo basename($_SERVER['HTTP_REFERER']); ?>">

                <?php echo ucwords(basename($_SERVER['HTTP_REFERER'], '.php')); ?></a> &rsaquo;</li>

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
        <h4>Assignment Details</h4>
      </div>
      <div class="form-group col-md-3 col-sm-6">
        <label class="select">Source Language *</label>
        <select class="form-control" name="source" id="source" required=''>
          <?php $sql_opt = "SELECT lang FROM lang ORDER BY lang ASC";

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

        <label class="select">Target Language *</label>
        <select name="target" id="target" required='' class="form-control">

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
        <label>Select Assignment Category</label>
        <select name="interp_cat" id="interp_cat" class="form-control" onchange="get_interp_type($(this));" required>
          <?php
          $q_interp_cat = $acttObj->read_all("ic_id,ic_title", "interp_cat", "ic_status=1 ORDER BY ic_title ASC");
          $opt_ic = "";
          while ($row_ic = $q_interp_cat->fetch_assoc()) {
            $ic_id = $row_ic["ic_id"];
            $ic_title = $row_ic["ic_title"];
            $opt_ic .= "<option value='$ic_id'>" . $ic_title . "</option>";
          }
          ?>
          <option disabled selected value="">Select Assignment Category</option>
          <?php echo $opt_ic; ?>
        </select>
      </div>
      <div class="form-group col-md-3 col-sm-6" id="div_it" style="display:none;">
      </div>
      <div class="form-group col-sm-6" id="div_assignIssue" style="display:none;">
        <textarea style="display:none;" title="(Purpose Of The Meeting / Appointment)" placeholder="Write Assignment Issue Here ..." name="assignIssue" class="form-control" id="assignIssue"></textarea>
      </div>
      <div class="form-group col-md-3 col-sm-6">

        <label class="input">Assignment Date *</label>

        <input type="date" name="assignDate" id="assignDate" required='' value='' class="form-control date_picker" />

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
      <div class="form-group col-md-3 col-sm-6">
        <label>Assignment Time *
        </label>
        <input onkeyup="dur_finder();" name="assignTime" id="assignTime" type="time" step="300" class="form-control time_picker" required='' />
      </div>
      <div class="form-group col-md-3 col-sm-6">
        <!-- <label class="input">Assignment Duration <i class="fa fa-question-circle" title="(Minimum 1 hour, Additional time in incremental units example 1 hour 15 minutes = 1.25)"></i></label> -->
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
      <div class="form-group col-md-3 col-sm-6">
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
        <h4>Assignment Details</h4>
      </div>
      <div class="form-group col-md-3 col-sm-6">

        <label class="input">Building No / Name </label>

        <input name="buildingName" type="text" class="form-control" />
        <?php if (isset($_POST['submit'])) {
          $buildingName = $_POST['buildingName'];
        } ?>

      </div>

      <div class="form-group col-md-3 col-sm-6">

        <label class="input">Street / Road / Area </label>

        <input name="street" type="text" class="form-control" />

        <?php if (isset($_POST['submit'])) {
          $street = $_POST['street'];
        } ?>

      </div>

      <div class="form-group col-md-3 col-sm-6">

        <label class="select">City (please select from the list)</label>

        <select name="assignCity" class="form-control">

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
          $assignCity = $_POST['assignCity'];
        } ?>

      </div>

      <div class="form-group col-md-3 col-sm-6">

        <label class="input">Post Code </label>

        <input name="postCode" type="text" class="form-control" />
        <?php if (isset($_POST['submit'])) {
          $postCode = $_POST['postCode'];
        } ?>

      </div>
      <div class="bg-info col-xs-12 form-group">
        <h4>Assignment In-Charge</h4>
      </div>
      <div class="form-group col-md-3 col-sm-6">

        <label class="input">Booking Person Name if Different</label>

        <input name="inchPerson" type="text" class="form-control long" value="<?php echo @$contactPerson; ?>" />

        <?php if (isset($_POST['submit'])) {
          $inchPerson = $_POST['inchPerson'];
        } ?>
      </div>

      <div class="form-group col-md-3 col-sm-6">

        <label class="input">Contact Number</label>

        <input name="inchContact" id="inchContact" type="text" class="form-control long" value="<?php echo @$contactNo1; ?>" />

        <?php if (isset($_POST['submit'])) {
          $inchContact = $_POST['inchContact'];
        } ?>
      </div>

      <div class="form-group col-md-3 col-sm-6">

        <label class="input">Assignment Incharge Name&nbsp;*</label>

        <input name="orgContact" id="orgContact" type="text" class="form-control" placeholder='' required='' />

        <?php if (isset($_POST['submit'])) {
          $orgContact = $_POST['orgContact'];
        } ?>
      </div>

      <div class="form-group col-md-3 col-sm-6">

        <label class="input">Email Address For Booking Confirmation</label>

        <input name="inchEmail" id="inchEmail" type="email" class="form-control long" placeholder='' required />

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
        <label class="input">Company / Organization (Team / Unit Title if Part of an Organisation or Trust) <i class="fa fa-question-circle" title="Team / Unit Name or Number"></i></label>
        <input name="orgName" id="orgName" type="text" value="" placeholder='' class="form-control" required />
      </div>
      <div class="form-group col-md-4 col-sm-6">
        <label class="input">Building Number / Name (Business Name)</label>
        <input class="form-control" name="inchNo" id="inchNo" type="text" placeholder='' />
      </div>
      <div class="form-group col-md-4 col-sm-6">
        <label class="input">Address Line 1</label>
        <input class="form-control" name="line1" id="line1" type="text" placeholder='' />
      </div>
      <div class="form-group col-md-4 col-sm-6">
        <label class="input">Address Line 2</label>
        <input class="form-control" name="line2" id="line2" type="text" placeholder='' />
      </div>
      <div class="form-group col-md-4 col-sm-6">
        <label class="input">Address Line 3</label>
        <input class="form-control" name="inchRoad" id="inchRoad" type="text" />
      </div>
      <div class="form-group col-md-4 col-sm-6">
        <label class="input">City / Town</label>
        <select name="inchCity" class="form-control">
          <option disabled selected>--Select--</option>
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
      </div>
      <div class="form-group col-md-4 col-sm-6">
        <label class="input">Post Code</label>
        <input class="form-control" name="inchPcode" id="inchPcode" type="text" />
      </div>
      <?php if (isset($_POST['submit'])) {
        $orgName = $_POST['orgName'];
      } ?>
      <?php if (isset($_POST['submit'])) {
        $inchNo = $_POST['inchNo'];
      } ?>
      <?php if (isset($_POST['submit'])) {
        $line1 = $_POST['line1'];
      } ?>
      <?php if (isset($_POST['submit'])) {
        $line2 = $_POST['line2'];
      } ?>
      <?php if (isset($_POST['submit'])) {
        $inchRoad = $_POST['inchRoad'];
      } ?>
      <?php if (isset($_POST['submit'])) {
        $inchCity = $_POST['inchCity'];
      } ?>
      <?php if (isset($_POST['submit'])) {
        $inchPcode = $_POST['inchPcode'];
      } ?>
      <div class="bg-info col-xs-12 form-group">
        <h4>Interpreter Preferences</h4>
      </div>
      <div class="form-group col-md-4 col-sm-6">
        <label class="optional">DBS Checked Interpreter Required?</label><br>
        <div class="radio-inline ri"><label><input name="dbs_checked" type="radio" value="Yes" required />
            <span class="label label-success">Yes <i class="fa fa-check-circle"></i></span></label></div>
        <div class="radio-inline ri"><label><input type="radio" name="dbs_checked" value="No" />
            <span class="label label-danger">No <i class="fa fa-remove"></i></span></label></div>
      </div>
      <!-- <div class="form-group col-sm-6">
        <label class="optional">Interpreter Gender:</label><br>
        <div class="radio-inline ri"><label><input name="gender" type="radio" value="Male" required  />
            <span class="label label-success">Male</span></label></div>
        <div class="radio-inline ri"><label><input type="radio" name="gender" value="Female" />
            <span class="label label-danger">Female</span></label></div>
        <div class="radio-inline ri"><label><input type="radio" name="gender" value="No Preference" />
            <span class="label label-warning">No Preference</span></label></div>
      </div> -->
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
        <label class="optional">Booking Status:</label><br>
        <div class="radio-inline ri"><label><input name="jobStatus" type="radio" value="0" />
            <span class="label label-danger">Enquiry <i class="fa fa-question"></i></span></label></div>
        <div class="radio-inline ri"><label><input type="radio" name="jobStatus" value="1" />
            <span class="label label-success">Confirmed <i class="fa fa-check-circle"></i></span></label></div>
      </div>

      <?php if (isset($_POST['submit'])) {
        $dbs_checked = $_POST['dbs_checked'];
        $gender = $_POST['gender'];
        $jobStatus = $_POST['jobStatus'];
        if ($jobStatus == 0) {
          $jobStatus = 'Enquiry';
        } else {
          $jobStatus = 'Confirmed';
        }
      } ?>

      <div class="form-group col-sm-12">
        <label>NOTES (if Any):</label><br>
        <textarea class="form-control col-sm-6" name="I_Comments" rows="5"></textarea>
      </div>
      <?php if (isset($_POST['submit'])) {
        $I_Comments = $_POST['I_Comments'];
      } ?>
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
    if (isset($_POST['submit']) && !empty($_POST['interp_cat']) && $_POST['interp_cat'] == '12') {
      $assignIssue = $_POST['assignIssue'];
    }

    if (isset($_POST['submit']) && !empty($_POST['interp_cat'])) {
      $interp_cat = $_POST['interp_cat'];
    }
    if (isset($_POST['submit']) && $_POST['interp_cat'] != '12') {
      $interp_type = implode(",", $_POST['interp_type']);
      $write_interp_cat = $interp_cat == '12' ? $assignIssue : $acttObj->read_specific("ic_title", "interp_cat", "ic_id=" . $interp_cat)['ic_title'];
      $write_interp_type = $interp_cat == '12' ? '' : $acttObj->read_specific("GROUP_CONCAT(CONCAT(it_title)  SEPARATOR ' <b> & </b> ') as it_title", "interp_types", "it_id IN (" . $interp_type . ")")['it_title'];
      if ($interp_cat == '12') {
        $append_issue = "<tr><td style='border: 1px solid yellowgreen;padding:5px;'>Assignment Category</td><td style='border: 1px solid yellowgreen;padding:5px;'>Other</td><td style='border: 1px solid yellowgreen;padding:5px;'>Assignment Details</td><td style='border: 1px solid yellowgreen;padding:5px;'>" . $assignIssue . "</td></tr>";
      } else {
        $append_issue = "<tr><td style='border: 1px solid yellowgreen;padding:5px;'>Assignment Category</td><td style='border: 1px solid yellowgreen;padding:5px;'>" . $write_interp_cat . "</td><td style='border: 1px solid yellowgreen;padding:5px;'>Assignment Details</td><td style='border: 1px solid yellowgreen;padding:5px;'>" . $write_interp_type . "</td></tr>";
      }
    }
    if (isset($_POST['submit']) && $captcha_flag == 1) {

      $from_add = "info@lsuk.org";

      $to_add = $inchEmail; //<-- put your yahoo/gmail email address here

      $subject = $source . ' Interpreter ' . $assignDate . ' ' . $assignTime; //"Order for Interpreter (F 2 F)";

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

<p>Hi " . $inchPerson . "</p>

<p>Below is an automatic acknowledgement of the order you place with Language Service UK Limited</p>

<caption align='center' style='background: grey;color: white;padding: 5px;'>Booking Request for Face to Face Interpreter</caption>
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

<td style='border: 1px solid yellowgreen;padding:5px;'>" . $assignDur . "</td>

</tr>

" . $append_issue . "

<tr>

<td style='border: 1px solid yellowgreen;padding:5px;'>DBS checked interpreter Requested ?</td>

<td style='border: 1px solid yellowgreen;padding:5px;'>" . $dbs_checked . "</td>

<td style='border: 1px solid yellowgreen;padding:5px;'>Gender</td>

<td style='border: 1px solid yellowgreen;padding:5px;'>" . $gender . "</td>

</tr>



<tr>

<td colspan='4' align='center' style='color: black;'>Assignment Location</td>

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

<td style='border: 1px solid yellowgreen;padding:5px;'>Booking Status</td>

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
        }
      } catch (Exception $e) { ?>
        <script>
          alert("Message could not be sent! Mailer library error.");
        </script>
      <?php }
    }

    if (isset($_POST['submit']) && $captcha_flag == 1) {

      $from_add = "info@lsuk.org";

      $to_add = "info@lsuk.org"; //<-- put your yahoo/gmail email address here

      $subject = $source . ' Interpreter ' . $assignDate . ' ' . $assignTime; //"Order for Interpreter (F 2 F)";

      $message = "<p>Dear LSUK Team</p>



<p>You have received the following booking request from your website </p>


<caption align='center' style='background: grey;color: white;padding: 5px;'>Form for Face to Face Interpreter Booking request</caption>
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
<td style='border: 1px solid yellowgreen;padding:5px;'>" . $assignDur . "</td>
</tr>



" . $append_issue . "



<tr>

<td style='border: 1px solid yellowgreen;padding:5px;'>DBS checked interpreter Requested ?</td>

<td style='border: 1px solid yellowgreen;padding:5px;'>" . $dbs_checked . "</td>

<td style='border: 1px solid yellowgreen;padding:5px;'>Gender</td>

<td style='border: 1px solid yellowgreen;padding:5px;'>" . $gender . "</td>

</tr>



<tr>

<td colspan='4' align='center' style='color: black;'>Assignment Location</td>

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

<td style='border: 1px solid yellowgreen;padding:5px;'>Booking Status</td>

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
        $mail->isSMTP();
        $mail->Host = setupEmail::EMAIL_HOST;
        $mail->SMTPAuth   = true;
        $mail->Username   = setupEmail::INFO_EMAIL;
        $mail->Password   = setupEmail::INFO_PASSWORD;
        $mail->SMTPSecure = setupEmail::SECURE_TYPE;
        $mail->Port       = setupEmail::SENDING_PORT;
        $mail->setFrom(setupEmail::INFO_EMAIL, setupEmail::FROM_NAME);
        $mail->addAddress(setupEmail::INFO_EMAIL);
        $mail->addReplyTo(setupEmail::INFO_EMAIL, setupEmail::FROM_NAME);
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
    } ?>
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

  $(function() {
    var dtToday = new Date();

    var month = dtToday.getMonth() + 1;
    var day = dtToday.getDate();
    var year = dtToday.getFullYear();
    if (month < 10)
      month = '0' + month.toString();
    if (day < 10)
      day = '0' + day.toString();

    var maxDate = year + '-' + month + '-' + day;
    $('.date_picker').attr('min', maxDate);
  });
</script>

</html>