<?php
//php mailer library
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require 'lsuk_system/phpmailer/vendor/autoload.php';
$mail = new PHPMailer(true);

if (session_id() == '' || !isset($_SESSION)) {
  session_start();
}
if (empty($_SESSION['cust_UserName'])) {
  echo '<script type="text/javascript">' . "\n";
  echo 'window.location="index.php";';
  echo '</script>';
}
include 'source/db.php';
include 'source/class.php';
$table = 'translation';
$company_id = base64_decode($_GET['company_id']);
$query_get_info = $acttObj->read_specific("abrv,name", "comp_reg", "id=" . $company_id);
$orgName = $query_get_info['abrv'];
$name = $query_get_info['name'];

//.........................................captcha........................................//

// if (isset($_POST['g-recaptcha-response']) && $_POST['g-recaptcha-response']) { //var_dump($_POST);

//   $secret = '6LextRoUAAAAAPvBF31eiYCmVP7Ne8a6mSez83zl';
//   $ip = $_SERVER['REMOTE_ADDR'];
//   $captcha = $_POST['g-recaptcha-response'];
//   $rsp = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=$secret&response=$captcha&remoteip=$ip");
//   //var_dump($rsp);
//   $arr = json_decode($rsp, true);
//   if ($arr['success']) {
//     $captcha_flag = 1;
//   } else {
//     $captcha_flag = 0;
//     echo 'Spam';
//   }
// } else if (SafeVar::IsLocal() == true) {
//   $captcha_flag = 1;
// }
$captcha_flag = 1;

//........................................//\\//\\//\\..........................................//

if (isset($_POST['submit']) && isset($captcha_flag) && $captcha_flag == 1) {
  $edit_id = $acttObj->get_id($table);
  //Create & save new reference no
  $reference_no = $acttObj->generate_reference(3, $table, $edit_id);
}
?>

<?php

$query = "SELECT name,contactNo1,contactPerson,email,city,buildingName,streetRoad,postCode,line1,line2 FROM comp_reg
WHERE status <> 'Company Seized trading in' and status <> 'Company Blacklisted' and abrv='$orgName' limit 1";
$result = mysqli_query($con, $query);
$row_selected = mysqli_fetch_array($result);
$name_comp = $row_selected['name'];
$contactNo1 = $row_selected['contactNo1'];
$contactPerson = $row_selected['contactPerson'];
$email = $row_selected['email'];
$city = $row_selected['city'];
$streetRoad = $row_selected['streetRoad'];
$buildingName = $row_selected['buildingName'];
$postCode = $row_selected['postCode'];
$line1 = $row_selected['line1'];
$line2 = $row_selected['line2'];
?>

<!DOCTYPE HTML>
<html class="no-js">

<head>
  <?php include 'source/header.php'; ?>
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
  <?php } ?>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" />
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
  <style>
    .ri {
      margin-top: 10px;
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

    /* Formatting search box */
    .search-box {
      width: 300px;
      position: relative;
      display: inline-block;
      font-size: 14px;
      margin-top: 2px;
    }

    .search-box input[type="text"] {
      height: 32px;
      padding: 5px 10px;
      border: 1px solid #CCCCCC;
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
            <li><a href="customer_area.php">Home</a> &rsaquo;</li>
          </ul>
        </nav>
      </div>
    </section>
    <form class="sky-form" action="#" method="post" enctype="multipart/form-data">
      <section id="content" class="container_fluid clearfix">
        <div class="col-lg-6">
          <table class="table table-bordered">
            <tr class="bg-info">
              <td align="center" colspan="4">
                <h4 style="text-transform: uppercase;font-weight: 600;"><?php echo $name; ?></h4>
              </td>
            </tr>
            <tr>
              <td><b>Building No / Name:</b></td>
              <td><?php echo $buildingName; ?></td>
              <td><b>Address:</b></td>
              <td><?php echo $line1 . ' ' . $line2 . ' ' . $line3; ?></td>
            </tr>
            <tr>
              <td><b>City:</b></td>
              <td><?php echo $city; ?></td>
              <td><b>Post Code:</b></td>
              <td><?php echo $postCode; ?></td>
            </tr>
          </table>
          <div class="bg-info col-xs-12 form-group">
            <h4>Work Details</h4>
          </div>
          <div class="form-group col-md-6 col-sm-6">
            <label class="select">Select Source Language *</label>
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
              <option value="">--Select--</option>
              <?php echo $options; ?>
            </select>
            <?php if (isset($_POST['submit'])) {
              $source = $_POST['source'];
              $acttObj->editFun($table, $edit_id, 'source', $source);
            } ?>
          </div>
          <div class="form-group col-md-6 col-sm-6">
            <label class="select">Select Target Language *</label>
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
              <option value="">--Select--</option>
              <?php echo $options; ?>
              </option>
            </select>
            <?php if (isset($_POST['submit'])) {
              $target = $_POST['target'];
              $acttObj->editFun($table, $edit_id, 'target', $target);
            } ?>
          </div>
          <div class="form-group col-md-6 col-sm-6" id="div_tc">
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
          <div class="form-group col-md-6 col-sm-6" id="div_tt" style="display:none;">
          </div>
          <div class="form-group col-md-6 col-sm-6" id="div_td" style="display:none;">
          </div>
          <?php if (isset($_POST['submit'])) {
            $docType = $_POST['docType'];
            $acttObj->editFun($table, $edit_id, 'docType', $docType);
          }
          if (isset($_POST['submit'])) {
            $transType = implode(",", $_POST['transType']);
            $acttObj->editFun($table, $edit_id, 'transType', $transType);
          }
          if (isset($_POST['submit'])) {
            $trans_detail = implode(",", $_POST['trans_detail']);
            $acttObj->editFun($table, $edit_id, 'trans_detail', $trans_detail);
          } ?>
          <div class="form-group col-md-6 col-sm-6">
            <label class="input">Booking Reference*</label>
            <input class="form-control" name="nameRef" type="text" required='' readonly="readonly" value="<?php $month = date('M');
                                                                                                          $month = substr($month, 0, 3);
                                                                                                          $lastid = $acttObj->max_id("global_reference_no") + 1;
                                                                                                          echo 'LSUK/' . $month . '/' . $lastid; ?>" />
            <?php if (isset($_POST['submit'])) {
              $month = substr($month, 0, 3);
              $lastid = $acttObj->max_id("global_reference_no") + 1;
              $nameRef = 'LSUK/' . $month . '/' . $edit_id;
              $acttObj->editFun($table, $edit_id, 'nameRef', $nameRef);
            } ?>
          </div>
          <div class="bg-info col-xs-12 form-group">
            <h4>Translation Delivery / Deadline Information</h4>
          </div>
          <div class="form-group col-md-4 col-sm-6">
            <label class="input">Assignment Date*</label>
            <input name="asignDate" required type="date" class="form-control date_picker" value="" />
            <?php if (isset($_POST['submit'])) {
              $asignDate = $_POST['asignDate'];
              $acttObj->editFun($table, $edit_id, 'asignDate', $asignDate);
            } ?>
          </div>
          <div class="form-group col-md-4 col-sm-6">
            <label class="select">Select Delivery Type</label>
            <select class="form-control" name="deliveryType" id="deliveryType" required>
              <option value="">--Select--</option>
              <option>Standard Service (1 -2 Weeks)</option>
              <option>Quick Service (2-3 Days)</option>
              <option>Emergency Service (1-2 Days)</option>
            </select>
            <?php if (isset($_POST['submit'])) {
              $deliveryType = $_POST['deliveryType'];
              $acttObj->editFun($table, $edit_id, 'deliveryType', $deliveryType);
            } ?>
          </div>
          <div class="form-group col-md-4 col-sm-6">
            <label class="input">Delivery Date*</label>
            <input name="deliverDate" type="date" class="form-control date_picker" value="" required />
            <?php if (isset($_POST['submit'])) {
              $deliverDate = $_POST['deliverDate'];
              $acttObj->editFun($table, $edit_id, 'deliverDate', $deliverDate);
            } ?>
          </div>
          <div class="bg-info col-xs-12 form-group">
            <h4>Booking Details</h4>
          </div>
          <div class="form-group col-md-6 col-sm-6">
            <label class="input">Booking Person Name*</label>
            <input class="form-control" name="orgContact" id="orgContact" type="text" value="<?php echo @$contactPerson; ?>" placeholder='' required='' />
            <?php if (isset($_POST['submit'])) {
              $orgContact = $_POST['orgContact'];
              $acttObj->editFun($table, $edit_id, 'orgContact', $orgContact);
            } ?>
          </div>
          <div class="form-group col-md-6 col-sm-6">
            <label class="input">Contact Number</label>
            <input name="inchContact" id="inchContact" type="text" class="long form-control" required='' value="<?php echo @$contactNo1; ?>" />
            <?php if (isset($_POST['submit'])) {
              $inchContact = $_POST['inchContact'];
              $acttObj->editFun($table, $edit_id, 'inchContact', $inchContact);
            } ?>
          </div>
          <div class="form-group col-md-6 col-sm-6">
            <label class="input">Email Address for Booking Confirmation</label>
            <input name="inchEmail" id="inchEmail" type="email" class="long form-control" placeholder='' value="<?php echo @$email; ?>" required='' />
            <?php if (isset($_POST['submit'])) {
              $inchEmail = $_POST['inchEmail'];
              $acttObj->editFun($table, $edit_id, 'inchEmail', $inchEmail);
            } ?>
          </div>
          <div class="form-group col-md-6 col-sm-6 search-box">
            <label class="input">Booking Reference Number <i class="fa fa-question-circle" title="(Name, Initials or File Ref. Number)"></i></label>
            <input class="form-control" name="orgRef" id="orgRef" type="text" required='' autocomplete="off" placeholder="Type Reference ..." />
            <i id="confirm_orgRef" style="display:none;position: absolute;right: 25px;top: 35px;" onclick="$(this).hide();$('.result').empty();" class="glyphicon glyphicon-ok-sign text-success" title="Confirm this reference"></i>
            <div class="result"></div>
            <?php if (isset($_POST['submit'])) {
              $orgRef = $_POST['orgRef'];
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
            } ?>
          </div>
          <div class="form-group col-md-6 col-sm-6">
            <label class="input">Booking Date*</label>
            <input class="form-control date_picker" onchange="OnDateChgAjax();" type="date" name="bookeddate" id="bookeddate" required='Booked Date' value="<?php echo @$bookedDate ?>" />
            <?php
            if (isset($_POST['submit'])) {
              $bookedDate = $_POST['bookeddate'];
              $acttObj->editFun($table, $edit_id, 'bookeddate', $bookedDate);
            }
            ?>
          </div>
          <div class="form-group col-md-6 col-sm-6">
            <label class="input">Booking Time*</label>
            <input onchange="OnTimeChgAjax();" type="time" name="bookedtime" id="bookedtime" required='Booked Time' step="300" class="form-control time_picker2" value="<?php echo @$bookedTime ?>" />
            <?php
            if (isset($_POST['submit'])) {
              $bookedTime = $_POST['bookedtime'];
              $acttObj->editFun($table, $edit_id, 'bookedtime', $bookedTime);
            }
            ?>
          </div>
        </div>
        <div class="col-lg-6">
          <div class="bg-info col-xs-12 form-group hidden">
            <h4>Booking Organization Details</h4>
          </div>
          <div class="form-group col-md-4 col-sm-6 hidden">
            <label class="input">Company / Organization <i class="fa fa-question-circle" title="Select Company / Organization Team / Unit Name or Number"></i></label>
            <input name="orgName" id="orgName" type="text" value="<?php echo @$name_comp; ?>" placeholder='' class="form-control" readonly />
            <?php if (isset($_POST['submit'])) {
              $acttObj->editFun($table, $edit_id, 'orgName', $orgName);
              $acttObj->editFun($table, $edit_id, 'order_company_id', $company_id);
            } ?>
          </div>
          <div class="form-group col-md-4 col-sm-6 hidden">
            <label class="input">Building Number / Name</label>
            <input class="form-control" name="inchNo" id="inchNo" type="text" value="<?php echo @$buildingName; ?>" placeholder='' readonly />
            <?php if (isset($_POST['submit'])) {
              $inchNo = $_POST['inchNo'];
              $acttObj->editFun($table, $edit_id, 'inchNo', $inchNo);
            } ?>
          </div>
          <div class="form-group col-md-4 col-sm-6 hidden">
            <label class="input">Address Line</label>
            <input class="form-control" name="line1" id="line1" type="text" placeholder='' value="<?php echo @$line1; ?>" readonly />
            <?php if (isset($_POST['submit'])) {
              $line1 = $_POST['line1'];
              $acttObj->editFun($table, $edit_id, 'line1', $line1);
            } ?>
          </div>
          <div class="form-group col-md-4 col-sm-6 hidden">
            <label class="input">Address Line 2</label>
            <input class="form-control" name="line2" id="line2" type="text" placeholder='' value="<?php echo @$line2; ?>" readonly />
            <?php if (isset($_POST['submit'])) {
              $line2 = $_POST['line2'];
              $acttObj->editFun($table, $edit_id, 'line2', $line2);
            } ?>
          </div>
          <div class="form-group col-md-4 col-sm-6 hidden">
            <label class="input">Address Line 3</label>
            <input class="form-control" name="inchRoad" id="inchRoad" type="text" value="<?php echo @$inchRoad; ?>" placeholder='' readonly />
            <?php if (isset($_POST['submit'])) {
              $inchRoad = $_POST['inchRoad'];
              $acttObj->editFun($table, $edit_id, 'inchRoad', $inchRoad);
            } ?>
          </div>
          <div class="form-group col-md-4 col-sm-6 hidden">
            <label class="input">City</label>
            <input class="form-control" name="inchCity" id="inchCity" type="text" value="<?php echo @$city; ?>" placeholder='' readonly />
            <?php if (isset($_POST['submit'])) {
              $inchCity = $_POST['inchCity'];
              $acttObj->editFun($table, $edit_id, 'inchCity', $inchCity);
            } ?>
          </div>
          <div class="form-group col-md-4 col-sm-6 hidden">
            <label class="input">Post Code</label>
            <input class="form-control" name="inchPcode" id="inchPcode" type="text" value="<?php echo @$postCode; ?>" readonly />
            <?php if (isset($_POST['submit'])) {
              $inchPcode = $_POST['inchPcode'];
              $acttObj->editFun($table, $edit_id, 'inchPcode', $inchPcode);
            } ?>
          </div>
          <div class="bg-info col-xs-12 form-group">
            <h4>Upload Documents (if any)</h4>
          </div>
          <div class="form-group col-sm-12" id="dvPreview"></div>
          <div class="form-group col-md-6 col-sm-6">
            <label class="input">Upload Document <i class="fa fa-question-circle" title='Acceptable formats: ("gif", "jpeg", "jpg", "png", "pdf", "doc", "docx", "xlsx")'></i></label>
            <input title='Acceptable formats: ("gif", "jpeg", "jpg", "png", "pdf", "doc", "docx", "xlsx")' name="file[]" onchange="loadFiles(event)" multiple="multiple" type="file" size="60" multiple="multiple" class="long form-control" id="file" style="border:1px solid #CCC" />
            <script language="javascript" type="text/javascript">
              window.onload = function() {
                var fileUpload = document.getElementById("file");
                fileUpload.onchange = function() {
                  if (typeof(FileReader) != "undefined") {
                    var dvPreview = document.getElementById("dvPreview");
                    dvPreview.innerHTML = "";
                    var regex = /^([a-zA-Z0-9\s_\\.\-:()])+(.jpg|.jpeg|.gif|.png|.pdf|.rtf|.JPG|.JPEG|.GIF|.PNG|.PDF|.RTF|.doc|.docx|.xlsx)$/;
                    var i;
                    for (i = 0; i < fileUpload.files.length; i++) {
                      var file = fileUpload.files[i];
                      if (regex.test(file.name.toLowerCase())) {
                        var file_name = file.name.toLowerCase().split(".");
                        var accepted_types = ['jpg', 'gif', 'png', 'jpeg'];
                        //alert(ext);

                        /*if( file_name.length === 1 || ( file_name[0] === "" && file_name.length === 2 ) ) {
                            return "";
                        }else{
                            var ext=file_name.pop();
                        }*/
                        var reader = new FileReader();
                        reader.onload = function(e) {
                          //if (accepted_types.indexOf(file_name[1]) > 0) {
                          var img = document.createElement("IMG");
                          img.height = "100";
                          img.width = "100";
                          img.title = "Document Attachment";
                          img.style.display = 'inline';
                          img.style.margin = '0px 2px 0px 0px';
                          img.style.padding = '0px 2px';
                          img.src = e.target.result;
                          /*}else{
                              var img = document.createElement("DIV");
                              img.setAttribute("class", "img-thumbnail");
                              img.setAttribute("style", "margin:1px;height:100px;width:100px;text-align:center;");
                              img.innerHTML = "Doc "+i;
                          }*/
                          dvPreview.appendChild(img);
                        }
                        reader.readAsDataURL(file);
                      } else {
                        alert(file.name + " is not a valid file.");
                        dvPreview.innerHTML = "";
                        return false;
                      }
                    }
                  } else {
                    alert("This browser does not support HTML5 FileReader.");
                  }
                }
              };
            </script>
          </div>
          <div class="bg-info col-xs-12 form-group">
            <h4>Booking Preferences</h4>
          </div>
          <div class="form-group col-md-6 col-sm-6">
            <label class="optional">Booking Status: </label><br>
            <div class="radio-inline ri"><label><input name="jobStatus" type="radio" value="0" />
                <span class="label label-danger" style="font-size:100%;padding: .5em 0.6em 0.5em;">Enquiry <i class="fa fa-question"></i></span></label></div>
            <div class="radio-inline ri"><label><input type="radio" name="jobStatus" value="1" checked />
                <span class="label label-success" style="font-size:100%;padding: .5em 0.6em 0.5em;">Confirmed <i class="fa fa-check-circle"></i></span></label></div>
            <?php if (isset($_POST['submit'])) {
              $jobStatus = $_POST['jobStatus'];
              $acttObj->editFun($table, $edit_id, 'jobStatus', $jobStatus);
            } ?>
          </div>
          <div class="form-group col-md-4 col-sm-6">
            <label class="optional">Display job on website ?</label><br>
            <div class="radio-inline ri"><label><input name="jobDisp" type="radio" value="1" checked />
                <span class="label label-success" style="font-size:100%;padding: .5em 0.6em 0.5em;">Yes <i class="fa fa-check-circle"></i></span></label></div>
            <div class="radio-inline ri"><label><input type="radio" name="jobDisp" value="0" />
                <span class="label label-danger" style="font-size:100%;padding: .5em 0.6em 0.5em;">No <i class="fa fa-remove"></i></span></label></div>
            <?php if (isset($_POST['submit'])) {
              $jobDisp = $_POST['jobDisp'];
              $acttObj->editFun($table, $edit_id, 'jobDisp', $jobDisp);
            } ?>
          </div>
          <div class="form-group col-sm-12">
            <b>NOTES (if Any):</b>
            <textarea class="form-control col-sm-6" name="I_Comments" rows="3"></textarea>
            <?php if (isset($_POST['submit'])) {
              $I_Comments = $_POST['I_Comments'];
              $acttObj->editFun($table, $edit_id, 'I_Comments', $I_Comments);
            } ?>
          </div>
          <?php if (1 == 2) { ?>
              <div class="form-group col-sm-12">
                  <script src='https://www.google.com/recaptcha/api.js'></script>
                  <div class="g-recaptcha" data-sitekey="6LextRoUAAAAAGSGzslurL5xeNDw3lDDVkxM9rZe"></div>
              </div>
          <?php } ?>
          <div class="form-group col-sm-12">
            <input type="submit" name="submit" class="btn btn-lg btn-primary" value="Submit" />
          </div>
        </div>
    </form>
  </div>
  </section>
  <!-- end content -->

  <hr>
  </section>
  <!-- end content -->

  <!-- begin footer -->
  <?php include 'source/footer.php'; ?>
  <!-- end footer -->
  </div>
  <?php
  if (isset($_POST['submit'])) {
    $asignDate = $misc->dated($asignDate);
    $from_add = 'translationservice@lsuk.org';
    $to_add = $inchEmail;
    $subject = 'Acknowledgment of your booking request'; //"Order for Interpreter (F 2 F)";
    $message = "<p>Dear " . $orgContact . "</p>
<p>
Thanks for booking with LSUK. This is an acknowledgment of the following booking
</p>
<p>Language (" . $source . ")</p>
<p>Date (" . $asignDate . ")</p>

<p>We will write to you once again when the job is allocated to the interpreter.</p>


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
<caption align='center' style='background: grey;color: white;padding: 5px;'>Order for Translation</caption>
<table class='myTable'>
<tr>
<td style='border: 1px solid yellowgreen;padding:5px;'>Source Language</td>
<td style='border: 1px solid yellowgreen;padding:5px;'>" . $source . "</td>
<td style='border: 1px solid yellowgreen;padding:5px;'>Target Language</td>
<td style='border: 1px solid yellowgreen;padding:5px;'>" . $target . "</td>
</tr>

<tr>
<td style='border: 1px solid yellowgreen;padding:5px;'>Assignment Date</td>
<td style='border: 1px solid yellowgreen;padding:5px;'>" . $asignDate . "</td>
<td style='border: 1px solid yellowgreen;padding:5px;'>Document Type</td>
<td style='border: 1px solid yellowgreen;padding:5px;'>" . $acttObj->read_specific("tc_title", "trans_cat", "tc_id=" . $docType)['tc_title'] . "</td>
</tr>
<tr>
<td style='border: 1px solid yellowgreen;padding:5px;'>Translation Type(s)</td>
<td style='border: 1px solid yellowgreen;padding:5px;'>" . $acttObj->read_specific("GROUP_CONCAT(CONCAT(tt_title)  SEPARATOR '<br>') as tt_title", "trans_types", "tt_id IN (" . $trans_detail . ")")['tt_title'] . "</td>
<td style='border: 1px solid yellowgreen;padding:5px;'>Translation Category</td>
<td style='border: 1px solid yellowgreen;padding:5px;'>" . $acttObj->read_specific("GROUP_CONCAT(CONCAT(td_title)  SEPARATOR '<br>') as td_title", "trans_dropdown", "td_id IN (" . $transType . ")")['td_title'] . "</td>
</tr>
<tr>
<td colspan='4' align='center' style='background: grey; color: white;'>More Information</td>
</tr>
<tr>
<td style='border: 1px solid yellowgreen;padding:5px;'>Develivery Type</td>
<td style='border: 1px solid yellowgreen;padding:5px;'>" . $deliveryType . "</td>
<td style='border: 1px solid yellowgreen;padding:5px;'>Delivery Date</td>
<td style='border: 1px solid yellowgreen;padding:5px;'>" . $misc->dated($deliverDate) . "</td>
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
<td colspan='4' align='center' style='background: grey; color: white;'>Contact Details</td>
</tr>
<tr>
<td style='border: 1px solid yellowgreen;padding:5px;'>Contact Number</td>
<td style='border: 1px solid yellowgreen;padding:5px;'>" . $inchContact . "</td>
<td style='border: 1px solid yellowgreen;padding:5px;'>Email</td>
<td style='border: 1px solid yellowgreen;padding:5px;'>" . $inchEmail . "</td>
</tr>

<tr>
<td style='border: 1px solid yellowgreen;padding:5px;'>Notes if Any 1000 alphabets</td>
<td style='border: 1px solid yellowgreen;padding:5px;' colspan='4' align='center'>" . $I_Comments . "</td>
</tr>
</table>

<p>Kindest Regards </p>

<p>Admin Team</p>

<p>Language Services UK Limited</p>
";
    $ack_message = 'Hi <b>Admin</b>
<p>This is an email acknowledgement for ' . $source . ' Translation Job requested by ' . $orgName . ' booked on ' . $misc->dated($bookedDate) . ' ' . $bookedTime . ' for assignment date ' . $asignDate . '.</p>
<p>Kindly verify at LSUK system.</p>
<p>Thank you</p>';
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

      if (isset($_POST['submit']) && $_FILES["file"]["name"] != NULL) {
        for ($i = 0; $i < count($_FILES['file']['tmp_name']); $i++) {
          $picName = $acttObj->upload_file("file_folder/trans_dox", $_FILES["file"]["name"][$i], $_FILES["file"]["type"][$i], $_FILES["file"]["tmp_name"][$i], round(microtime(true)) . $i);
          $data = array('tbl' => $table, 'file_name' => $picName, 'order_id' => $edit_id, 'dated' => date('Y-m-d h:i:s'), 'file_type' => 'c_portal', 'orgName' => $orgName);
          $acttObj->insert('job_files', $data);
          $mail->AddAttachment("file_folder/trans_dox/" . $picName, "Translation Attachment");
        }
      }
      if ($mail->send()) {
        $mail->ClearAllRecipients();
        $mail->clearAttachments();
        //$mail->addAddress('inf@lsuk.org');
        $mail->addAddress($from_add);
        $mail->addReplyTo($from_add, 'LSUK');
        $mail->isHTML(true);
        $mail->Subject = 'Acknowledgement for new Translation Online Portal Job';
        $mail->Body    = $ack_message;
        $mail->send();
        $mail->ClearAllRecipients();
        $mail->clearAttachments();
        //Invoice //
        if ($_POST['jobStatus'] == 1) {
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
        $jobDisp_req = $_POST['jobDisp'];
        $jobStatus_req = $_POST['jobStatus'];
        if ($jobDisp_req == '1' && $jobStatus_req == '1') {
          $source_lang_req = $_POST['source'];
          $assignDate_req = $misc->dated($_POST['asignDate']);
          $append_table = "
<table>
<tr>
<td style='border: 1px solid yellowgreen;padding:5px;'>Source Language</td>
<td style='border: 1px solid yellowgreen;padding:5px;'>" . $source_lang_req . "</td>
</tr>
<tr>
<td style='border: 1px solid yellowgreen;padding:5px;'>Target Language</td>
<td style='border: 1px solid yellowgreen;padding:5px;'>" . $target . "</td>
</tr>
<tr>
<td style='border: 1px solid yellowgreen;padding:5px;'>Assignment Date</td>
<td style='border: 1px solid yellowgreen;padding:5px;'>" . $assignDate_req . "</td>
</tr>
<tr>
<td style='border: 1px solid yellowgreen;padding:5px;'>Document Type</td>
<td style='border: 1px solid yellowgreen;padding:5px;'>" . $acttObj->read_specific("tc_title", "trans_cat", "tc_id=" . $docType)['tc_title'] . "</td>
</tr>
<tr>
<td style='border: 1px solid yellowgreen;padding:5px;'>Translation Type(s)</td>
<td style='border: 1px solid yellowgreen;padding:5px;'>" . $acttObj->read_specific("GROUP_CONCAT(CONCAT(tt_title)  SEPARATOR ' <b> & </b> ') as tt_title", "trans_types", "tt_id IN (" . $trans_detail . ")")['tt_title'] . "</td>
</tr>
<tr>
<td style='border: 1px solid yellowgreen;padding:5px;'>Translation Category</td>
<td style='border: 1px solid yellowgreen;padding:5px;'>" . $acttObj->read_specific("GROUP_CONCAT(CONCAT(td_title)  SEPARATOR ' <b> & </b> ') as td_title", "trans_dropdown", "td_id IN (" . $transType . ")")['td_title'] . "</td>
</tr>
<tr>
<td style='border: 1px solid yellowgreen;padding:5px;'>Delivery Date</td>
<td style='border: 1px solid yellowgreen;padding:5px;'>" . $misc->dated($deliverDate) . "</td>
</tr>
<tr>
<td style='border: 1px solid yellowgreen;padding:5px;'>Delivery Type</td>
<td style='border: 1px solid yellowgreen;padding:5px;'>" . $deliveryType . "</td>
</tr>
</table>";
          if ($source_lang_req == $target) {
            $put_lang = "";
            $query_style = '0';
          } else if ($source_lang_req != 'English' && $target != 'English') {
            $put_lang = "";
            $query_style = '1';
          } else if ($source_lang_req == 'English' && $target != 'English') {
            $put_lang = "interp_lang.lang='$target' and interp_lang.level<3";
            $query_style = '2';
          } else if ($source_lang_req != 'English' && $target == 'English') {
            $put_lang = "interp_lang.lang='$source_lang_req' and interp_lang.level<3";
            $query_style = '2';
          } else {
            $put_lang = "";
            $query_style = '3';
          }
          if ($query_style == '0') {
            $query_emails = "SELECT DISTINCT interpreter_reg.name, interpreter_reg.email, interpreter_reg.id FROM interpreter_reg,interp_lang WHERE interpreter_reg.code=interp_lang.code AND (SELECT COUNT(DISTINCT interp_lang.lang) FROM interp_lang WHERE interp_lang.lang IN ('" . $source_lang_req . "') and interp_lang.level<3 and interp_lang.code=interpreter_reg.code)=1 and 
            interpreter_reg.active='0' AND (interpreter_reg.actnow='Active' OR (interpreter_reg.actnow='Inactive' AND CURRENT_DATE() NOT BETWEEN interpreter_reg.actnow_time AND interpreter_reg.actnow_to)) AND interpreter_reg.trans='Yes' AND interpreter_reg.deleted_flag=0 AND interpreter_reg.subscribe=1 AND interpreter_reg.is_temp=0 AND interpreter_reg.isAdhoc=0 AND interpreter_reg.on_hold='No'";
          } else if ($query_style == '1') {
            $query_emails = "SELECT DISTINCT interpreter_reg.name, interpreter_reg.email, interpreter_reg.id FROM interpreter_reg,interp_lang WHERE interpreter_reg.code=interp_lang.code AND (SELECT COUNT(DISTINCT interp_lang.lang) FROM interp_lang WHERE interp_lang.lang IN ('" . $source_lang_req . "','" . $target . "') and interp_lang.level<3 and interp_lang.code=interpreter_reg.code)=2 and 
            interpreter_reg.active='0' AND (interpreter_reg.actnow='Active' OR (interpreter_reg.actnow='Inactive' AND CURRENT_DATE() NOT BETWEEN interpreter_reg.actnow_time AND interpreter_reg.actnow_to)) AND interpreter_reg.trans='Yes' AND interpreter_reg.deleted_flag=0 AND interpreter_reg.subscribe=1 AND interpreter_reg.is_temp=0 AND interpreter_reg.isAdhoc=0 AND interpreter_reg.on_hold='No'";
          } else if ($query_style == '2') {
            $query_emails = "SELECT DISTINCT interpreter_reg.name, interpreter_reg.email, interpreter_reg.id FROM interpreter_reg,interp_lang WHERE interpreter_reg.code=interp_lang.code AND $put_lang and 
            interpreter_reg.active='0' AND (interpreter_reg.actnow='Active' OR (interpreter_reg.actnow='Inactive' AND CURRENT_DATE() NOT BETWEEN interpreter_reg.actnow_time AND interpreter_reg.actnow_to)) AND interpreter_reg.trans='Yes' AND interpreter_reg.deleted_flag=0 AND interpreter_reg.subscribe=1 AND interpreter_reg.is_temp=0 AND interpreter_reg.isAdhoc=0 AND interpreter_reg.on_hold='No'";
          } else {
            $query_emails = "SELECT DISTINCT interpreter_reg.name, interpreter_reg.email, interpreter_reg.id FROM interpreter_reg WHERE 
            interpreter_reg.active='0' AND (interpreter_reg.actnow='Active' OR (interpreter_reg.actnow='Inactive' AND CURRENT_DATE() NOT BETWEEN interpreter_reg.actnow_time AND interpreter_reg.actnow_to)) AND interpreter_reg.trans='Yes' AND interpreter_reg.deleted_flag=0 AND interpreter_reg.subscribe=1 AND interpreter_reg.is_temp=0 AND interpreter_reg.isAdhoc=0 AND interpreter_reg.on_hold='No'";
          }
          $res_emails = mysqli_query($con, $query_emails);
          //Getting bidding email from em_format table
          $row_format = $acttObj->read_specific("em_format", "email_format", "id=27");
          $subject_int = "New Translation Project " . $edit_id;
          $sub_title = "Translation job of " . $source_lang_req . " language on " . $assignDate_req . " is available for you to bid.";
          $type_key = "nj";
          //$app_int_ids=array();
          while ($row_emails = mysqli_fetch_assoc($res_emails)) {
            if ($acttObj->read_specific("COUNT(*) as blacklisted", "interp_blacklist", "interpName='id-" . $row_emails['id'] . "' AND orgName='" . $orgName . "' AND deleted_flag=0 AND blocked_for=2")["blacklisted"] == 0) {
              $to_int_address = $row_emails['email'];
              //Send notification on APP
              $check_id = $acttObj->read_specific('id', 'notify_new_doc', 'interpreter_id=' . $row_emails['id'])['id'];
              if (empty($check_id)) {
                $acttObj->insert('notify_new_doc', array("interpreter_id" => $row_emails['id'], "status" => '1'));
              } else {
                $acttObj->update('notify_new_doc', array("new_notification" => '0'), array("interpreter_id" => $row_emails['id']));
              }
              $array_tokens = explode(',', $acttObj->read_specific("GROUP_CONCAT( DISTINCT token) as tokens", "int_tokens", "int_id=" . $row_emails['id'])['tokens']);
              if (!empty($array_tokens)) {
                $acttObj->insert('app_notifications', array("title" => $subject_int, "sub_title" => $sub_title, "dated" => date('Y-m-d'), "int_ids" => $row_emails['id'], "read_ids" => $row_emails['id'], "type_key" => $type_key));
                //array_push($app_int_ids,$row_emails['id']);
                foreach ($array_tokens as $token) {
                  if (!empty($token)) {
                    $acttObj->notify($token, $subject_int, $sub_title, array("type_key" => $type_key, "job_type" => "Translation"));
                  }
                }
              }
              //Replace date in email bidding 
              $data   = ["[NAME]", "[ASSIGNDATE]", "[TABLE]", "[EDIT_ID]"];
              $to_replace  = [$row_emails['name'], "$assignDate_req", "$append_table", "$edit_id"];
              $message_int = str_replace($data, $to_replace, $row_format['em_format']);
              $mail->setFrom($from_add, 'LSUK');
              $mail->addAddress($to_int_address);
              $mail->addReplyTo($from_add, 'LSUK');
              $mail->isHTML(true);
              $mail->Subject = $subject_int;
              $mail->Body    = $message_int;
              $mail->send();
              $mail->ClearAllRecipients();
            }
          }
        }
        echo "<script>alert('Thanks for booking with LSUK Limited. You have successfully submitted the form. You will shortly receive a confirmation email of the request. Please check your email. Any problem please get in touch with LSUK Booking Team on 01173290610.');</script>";
      } else {
        echo "<script>alert('OOps..Email not submited!');</script>";
      }
    } catch (Exception $e) { ?>
      <script>
        alert("Message could not be sent! Mailer library error.");
      </script>
  <?php }
    $acttObj->editFun($table, $edit_id, 'submited', 'Online');
    $acttObj->editFun($table, $edit_id, 'edited_by', 'Online');
    $acttObj->editFun($table, $edit_id, 'edited_date', date("Y-m-d H:i:s"));
    // $acttObj->new_old_table('hist_' . $table, $table, $edit_id);
    $acttObj->editFun($table, $edit_id, 'bookedVia', 'Online Portal');
  } ?>
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

    $('.search-box input[type="text"]').on("keyup input", function() {
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
          $('#confirm_orgRef').show();
        });
      } else {
        resultDropdown.empty();
        $('#confirm_orgRef').show();
      }
    });
    // Set search input value on click of result item
    $(document).on("click", ".result p.click", function() {
      $(this).parents(".search-box").find('input[type="text"]').val($(this).text());
      $(this).parent(".result").empty();
      $('#confirm_orgRef').hide();
    });
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