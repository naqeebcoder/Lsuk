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
$id=base64_decode($_GET['id']);
$table = 'interpreter';
$row=$acttObj->read_specific("*","$table","id=".$id);
$query_get_info = $acttObj->read_specific("name","comp_reg","abrv='".$row['orgName']."'");
$orgName = $row['orgName'];$name = $query_get_info['name'];
$source=$row['source'];
$target=$row['target'];$interp_cat=$row['interp_cat'];$interp_type=$row['interp_type'];
$assignDate=$row['assignDate'];$assignTime=$row['assignTime'];$assignDur=$row['assignDur'];
$nameRef=$row['nameRef'];$buildingName=$row['buildingName'];$line1=$row['line1'];
$line2=$row['line2'];$street=$row['street'];$assignCity=$row['assignCity'];
$postCode=$row['postCode'];$inchPerson=$row['inchPerson'];$inchContact=$row['inchContact'];$inchEmail=$row['inchEmail'];
$inchEmail2=$row['inchEmail2'];$inchNo=$row['inchNo'];$inchRoad=$row['inchRoad'];
$inchCity=$row['inchCity'];$inchPcode=$row['inchPcode'];$orgName=$row['orgName'];
$orgRef=$row['orgRef'];$orgContact=$row['orgContact'];$remrks=$row['remrks'];
$gender=$row['gender'];$intrpName=$row['intrpName']; $jobStatus=$row['jobStatus'];
$bookinType=$row['bookinType'];$I_Comments=$row['I_Comments'];$snote=$row['snote'];
$jobDisp=$row['jobDisp'];$invoiceNo=$row['invoiceNo'];$bookedVia=$row['bookedVia'];$assignIssue=$row['assignIssue'];
$dbs_checked=$row['dbs_checked'];$noty=$row['noty'];
$noty_reason=$row['noty_reason'];$bookeddate=$row['bookeddate'];
$bookedtime=$row['bookedtime'];
$dbs_bookednamed=$row['namedbooked'];$is_temp=$row['is_temp'];
$po_req=$acttObj->read_specific("po_req","comp_reg","abrv='".$orgName."'")['po_req'];
$porder_email=$row['porder_email'];
$month=date('M');$month=substr($month,0,3); 
$lastid=$acttObj->max_id($table) + 1; $nameRef='LSUK/'.$month.'/'.$lastid;
//.........................................captcha........................................//
if (isset($_POST['g-recaptcha-response']) && $_POST['g-recaptcha-response']) { //var_dump($_POST);
    $secret = '6LextRoUAAAAAPvBF31eiYCmVP7Ne8a6mSez83zl';
    $ip = $_SERVER['REMOTE_ADDR'];
    $captcha = $_POST['g-recaptcha-response'];
    $rsp = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=$secret&response=$captcha&remoteip=$ip");
    //var_dump($rsp);
    $arr = json_decode($rsp, true);
    if ($arr['success']) {
        $captcha_flag = 1;
    } else {
        echo 'Spam';
    }
} else if (SafeVar::IsLocal() == true) {
    $captcha_flag = 1;
}
//........................................//\\//\\//\\..........................................//
if (isset($_POST['submit']) && isset($captcha_flag) && $captcha_flag == 1) {
    //if post AND recaptcha OK

    $v_source = @$_POST['source'];

    $v_assignDate = @$_POST['assignDate'];
    $v_assignTime = @$_POST['assignTime'];
    $v_orgName = @$_POST['orgName'];
    $v_orgContact = @$_POST['orgContact'];
    $v_orgRef = @$_POST['orgRef'];

    $query = "SELECT count(id) as val FROM $table where source='$v_source' and assignDate='$v_assignDate'
    and assignTime='$v_assignTime' and orgName='$v_orgName' and orgContact='$v_orgContact' and orgRef='$v_orgRef'";
    $result = mysqli_query($con, $query);
    while ($row = mysqli_fetch_array($result)) {
        $val = $row['val'];
    }

    if ($val == 0) {
        $edit_id = $acttObj->get_id($table);
    } else {
        echo "<script>alert('oops..This job is already booked!');window.history.back()</script>";
    }
}

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
    <?php include 'source/header.php';?>
  <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" />
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php if((strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== FALSE) || (strpos($_SERVER['HTTP_USER_AGENT'], 'Trident') !== FALSE)){ ?>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/timepicker/1.3.5/jquery.timepicker.min.css">
<script src="//cdnjs.cloudflare.com/ajax/libs/timepicker/1.3.5/jquery.timepicker.min.js"></script>
<script>
  $( function() {
    $( ".date_picker" ).datepicker({
    dateFormat: 'yy-mm-dd'
});
    $( ".time_picker" ).timepicker({
    timeFormat: 'HH:mm',
    interval: 5,
    defaultTime: '08',
    dropdown: true,
    scrollbar: true
});
    $( ".time_picker2" ).timepicker({
    timeFormat: 'HH:mm',
    interval: 1,
    defaultTime: '08',
    dropdown: true,
    scrollbar: true
});
});
  </script>
<?php }else{ ?>
    <script src="lsuk_system/js/jquery-1.11.3.min.js"></script>
<?php } ?>

    <script type="text/javascript" src="lsuk_system/js/debug.js"></script>
    <script type="text/javascript" src="lsuk_system/js/postcodelookup.js"></script>
<style>.ri{margin-top: 7px;}
.ri .label{font-size:100%;padding: .5em 0.6em 0.5em;}
.checkbox-inline+.checkbox-inline, .radio-inline+.radio-inline {
    margin-top: 4px;}
    .multiselect {min-width: 295px;}.multiselect-container {max-height: 400px;overflow-y: auto;max-width: 380px;}.multiselect-native-select{display:block;}.multiselect-container li.active label.checkbox{color:white;}
    .sky-form select{-webkit-appearance: auto !important;}
    /* Formatting search box */
.search-box{
    width: 300px;
    position: relative;
    display: inline-block;
    font-size: 14px;
}
.search-box input[type="text"]{
    height: 32px;
    padding: 5px 10px;
    border: 1px solid #CCCCCC;
    font-size: 14px;
}
.result{
    position: absolute;
    z-index: 1000;
    top: 100%;
    width: 90% !important;
    background: white;
    max-height: 246px;
    overflow-y: auto;
}
.search-box input[type="text"], .result{
    width: 100%;
    box-sizing: border-box;
}
/* Formatting result items */
.result p{
    margin: 0;
    padding: 7px 10px;
    border: 1px solid #CCCCCC;
    border-top: none;
    cursor: pointer;
}
.result p:hover{
    background: #f2f2f2;
}</style>
</head>

<body class="boxed">
    <!-- begin container -->
    <div id="wrap">
        <!-- begin header -->
        <?php include 'source/top_nav.php';?>
        <!-- end header -->

        <!-- begin page title -->
        <section id="page-title">
            <div class="container clearfix">
                <h1>Place an Order (Face to Face Interpreter)</h1>
                <nav id="breadcrumbs">
                    <ul>
                        <li><a href="index.php">Home</a> &rsaquo;</li>

                    </ul>
                </nav>
            </div>
        </section>

    <form class="sky-form" action="#" method="post">
        <section id="content" class="container_fluid clearfix">
            <div class="col-lg-6">
            <table class="table table-bordered">
                <tr class="bg-info"><td align="center" colspan="4"><h4 style="text-transform: uppercase;font-weight: 600;"><?php echo $name;?></h4></td></tr>
                <tr><td><b>Building No / Name:</b></td><td><?php echo $buildingName;?></td><td><b>Address:</b></td><td><?php echo $line1.' '.$line2.' '.$line3; ?></td></tr>
                <tr><td><b>City:</b></td><td><?php echo $city; ?></td><td><b>Post Code:</b></td><td><?php echo $postCode;?></td></tr>
            </table>
                <div class="bg-info col-xs-12 form-group"><h4>Assignment Details</h4></div>
                <div class="form-group col-md-6 col-sm-6">
                    <label class="select">Select Source Language *</label>
                    <select class="form-control" name="source" id="source" required=''>
                    <?php $sql_opt = "SELECT lang FROM lang ORDER BY lang ASC";
                        $result_opt = mysqli_query($con, $sql_opt);
                        $options = "";
                        while ($row_opt = mysqli_fetch_array($result_opt)) {
                            $code = $row_opt["lang"];
                            $name_opt = $row_opt["lang"];
                            $options .= "<option value='$code'>" . $name_opt . "</option>";
                        } ?>
                        <option value="<?php echo $row['source'];?>"><?php echo $row['source'];?></option>
                        <option value="">--Select--</option>
                        <?php echo $options; ?>
                    </select>
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
    $options .= "<option value='$code'>" . $name_opt . "</option>";
}
?>      
                        <option value="<?php echo $row['target'];?>"><?php echo $row['target'];?></option>
                                    <option value="">--Select--</option>
                                    <?php echo $options; ?>
                                </select>
            </div>
            <div class="form-group col-md-6 col-sm-6" id="div_tc">
                <label>Select Assignment Category</label>
              <select name="interp_cat" id="interp_cat"  class="form-control" onchange="get_interp_type($(this));" required>
<?php           
$q_interp_cat=$acttObj->read_all("ic_id,ic_title","interp_cat","ic_status=1 ORDER BY ic_title ASC");
$opt_ic="";
while ($row_ic=$q_interp_cat->fetch_assoc()) {
    $ic_id=$row_ic["ic_id"];
    $ic_title=$row_ic["ic_title"];
    $opt_ic.="<option value='$ic_id'>".$ic_title."</option>";}
?>
        <?php if(isset($interp_cat)){ ?>
        <option selected value="<?php echo $interp_cat; ?>"><?php echo $acttObj->read_specific("ic_title","interp_cat","ic_id=".$interp_cat)['ic_title']; ?></option>
        <?php } ?>
        <option disabled value="">Select Assignment Category</option>
        <?php echo $opt_ic; ?>
        </select>
    </div>
    <div class="form-group col-md-6 col-sm-6" id="div_it" <?php if($interp_cat=='12'){ echo "style='display:none;'";} ?>>
        <label>Select Assignment Type(s)</label>
        <select name="interp_type[]"  multiple="multiple" id="interp_type" class="form-control multi_class" <?php if($interp_cat!='12'){ echo "required";} ?>>
            <?php $q_it=$acttObj->read_all('it_id,it_title','interp_types',"ic_id='$interp_cat' AND it_id NOT IN ($interp_type) ORDER BY it_title ASC");
            $arr_interp_type=explode(',',$interp_type);
            for($it_i=0;$it_i<count($arr_interp_type);$it_i++){
                $option_it.="<option selected value='$arr_interp_type[$it_i]'>".$acttObj->read_specific("it_title","interp_types","it_id=".$arr_interp_type[$it_i])['it_title']."</option>";
            }
            echo $option_it;
            while($row_it=$q_it->fetch_assoc()){
            echo '<option value="'.$row_it['it_id'].'">'.$row_it['it_title'].'</option>';
                } ?>
        </select>
    </div>
          <div class="form-group col-md-8 col-sm-6" id="div_assignIssue" <?php if($interp_cat!='12'){echo "style='display:none;'";} ?>>
            <textarea title="Assignment Issue" placeholder="Write Assignment Issue Here ..." name="assignIssue" class="form-control" id="assignIssue" ><?php echo $assignIssue; ?></textarea>
                </div>
            <div class="form-group col-md-6 col-sm-6">
                            <label class="input2">Assignment Date*</label>
                                <input class="form-control date_picker" type="date" id="assignDate" name="assignDate" required='' value='<?php echo $assignDate; ?>'/>
            </div>
<script type="text/javascript">
function dur_finder(){

var datetime=$('#assignDate').val()+' '+$('#assignTime').val();
var duration=$('#assignDur').val();
        $.ajax({
            url:'ajax_client_portal.php',
            method:'post',
            data:{'datetime':datetime,'duration':duration,val:'dur_finder'},
            success:function(data){
                $('#assignEndTime').val(data);
        }, error: function(xhr){
            alert("An error occured: " + xhr.status + " " + xhr.statusText);
        }
        });

}
</script>
<div class="form-group col-md-6 col-sm-6">
<label>Assignment  Time *
</label>
<input onkeyup="dur_finder();" name="assignTime" id="assignTime" type="time"  step="300" class="form-control time_picker" required='' value='<?php echo $assignTime; ?>'/>
</div>
<div class="form-group col-md-6 col-sm-6">
<label>Assignment Duration * (Hours:Minutes)
</label>
<input id="assignDur" onkeyup="dur_finder();" name="assignDur" type="text" pattern="[0-9 :]{5}" maxlength="5" class="form-control" value="<?php echo isset($assignDur)?SetValueAsTime($assignDur):''; ?>" required='' placeholder="Hours : Minutes"/>
</div>
<?php 
function SetValueAsTime($data){
  if (!isset($data))
    return "";
  $mins=$data % 60;
  $hours=$data / 60;
  $data=sprintf("%02d:%02d",$hours,$mins);
  return $data;
}
$input_time = date($assignDate.' '.$assignTime);
$newTime = date("m/d/Y H:i",strtotime("+$assignDur minutes", strtotime($input_time))); ?>
<div class="form-group col-md-6 col-sm-6">
<label>Assignment End Time
</label>
<input id="assignEndTime" readonly="readonly" name="assignEndTime" type="text" class="form-control" value="<?php echo $newTime; ?>"/> 
</div>
<script>
    $('#assignDur').keyup(function () {
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
$("#assignDur").bind('keypress paste',function (e) {
  var regex = new RegExp(/[0-9]/);
  var str = String.fromCharCode(!e.charCode ? e.which : e.charCode);
  if (!regex.test(str)) {
    e.preventDefault();
    return false;
  }
});
</script>
                    <div class="bg-info col-xs-12 form-group"><h4>Assignment Reference</h4></div>
            <div class="form-group col-md-6 col-sm-6 search-box">
                <label class="input">Your Reference <i class="fa fa-question-circle" title="(Name, Initials or File Ref. Number)"></i></label>
                <input class="form-control" name="orgRef" id="orgRef" type="text" required='' autocomplete="off" placeholder="Type your reference" value="<?php echo $orgRef; ?>"/>
                <i id="confirm_orgRef" style="display:none;position: absolute;right: 25px;top: 35px;" onclick="$(this).hide();$('.result').empty();" class="glyphicon glyphicon-ok-sign text-success" title="Confirm this reference"></i>
                <div class="result"></div>
            </div>
            <div class="form-group col-md-6 col-sm-6" title="System generated ID by LSUK">
                            <label class="input">Booking Reference (LSUK)</label>
                                <input class="form-control" name="nameRef" type="text" required='' readonly="readonly" value="<?php echo $nameRef; ?>" />
            </div>
                    <div class="bg-info col-xs-12 form-group"><h4>Assignment Location (Enter Full Address)</h4></div>
                    
              <div class="form-group col-md-6 col-sm-6">
                <label class="optional">Post Code
                </label>
                <div class="input-group">
                    <input id="postCode" class="form-control" name="postCode" type="text" placeholder="Search Post Code" class="form-control" value="<?php echo $postCode ?>">
                    <div class="input-group-btn">
                      <button onclick="return PostCodeChanged();" class="btn btn-success">Look Up</button>
                    </div>
                  </div>
              </div>
                    <div class="form-group col-md-6 col-sm-6">
                    <label class="input">Building No / Name</label>
                <div class="input-group">
                    <input placeholder="Building No / Name" id="buildingName" name="buildingName" class="form-control" readonly="readonly" type="text" value="<?php echo $buildingName ?>">
                    <div class="input-group-btn">
                      <button onclick="EditStreet();" type="button" class="btn btn-info">Edit Street</button>
                    </div>
                  </div>
              </div>
            <div class="form-group col-md-4 col-sm-6">
                            <label class="select">City / Town <i class="fa fa-question-circle" title="Please Select From The List Below"></i></label>
                                <select class="form-control" id="assignCity" name="assignCity">
                                    <?php if(!empty($assignCity)){ ?>
                                      <option><?php echo $assignCity; ?></option>
                                      <?php } else{?>
                                      <option value="">--Select City--</option>
                                      <?php } ?>
                                    <?php
include 'lsuk_system/assigncityselect.php';
?>
            </div>
            <div class="form-group col-md-8 col-sm-6">
                            <label class="input">Street / Road and Area</label>
                                <input type="text" class="form-control" id="street" name="street" value="<?php echo $street ?>" />
            </div>

            </div>
            <div class="col-lg-6">

                    <div class="bg-info col-xs-12 form-group"><h4>Assignment Details</h4></div>
            <div class="form-group col-md-6 col-sm-6">
                            <label class="input">Booking Person Name</label>
                                <input class="long form-control" name="inchPerson" type="text"
                                    value="<?php echo @$contactPerson; ?>" />
            </div>
            <div class="form-group col-md-6 col-sm-6">
                            <label class="input">Contact Number</label>
                                <input name="inchContact" id="inchContact" type="text" class="long form-control"
                                    value="<?php echo @$contactNo1; ?>" />
            </div>
            <div class="form-group col-md-6 col-sm-6">
                            <label class="input">Interpreter Contact Name&nbsp;* <i class="fa fa-question-circle" title="Assignment in-Charge"></i></label>
                                <input class="form-control" name="orgContact" id="orgContact" type="text" placeholder='' required='' />
            </div>
            <div class="form-group col-md-6 col-sm-6">
                            <label class="input">Email Address (For Booking Confirmation)</label>
                                <input name="inchEmail" id="inchEmail" type="email" class="long form-control" value="<?php echo @$email; ?>" placeholder=''
                                    required />
            </div>
            <div class="form-group col-md-6 col-sm-6">
                            <label class="input">Booking Date&nbsp;*</label>
                                <input onchange="OnDateChgAjax();" type="date" name="bookeddate" id="bookeddate"
                                    required='Booked Date' class="form-control date_picker"
                                    value="<?php echo @$bookeddate ?>" />
            </div>
            <div class="form-group col-md-6 col-sm-6">
                            <label class="input">Booking Time&nbsp;*</label>
                                <input onchange="OnTimeChgAjax();" type="time" name="bookedtime" id="bookedtime"
                                    required='Booked Time' step="300" class="form-control time_picker2"
                                    value="<?php echo @$bookedtime ?>" />
            </div>
            <div class="form-group col-md-4 col-sm-6 hidden">
                            <label class="input">Company / Organization <i class="fa fa-question-circle" title="Team / Unit Name or Number"></i></label>
                                <input class="form-control" name="orgName" id="orgName" type="text" value="<?php echo @$name_comp; ?>"
                                    placeholder='' readonly />
            </div>
            <div class="form-group col-md-4 col-sm-6 hidden">
                            <label class="input">Building Number / Name (Business Name)</label>
                                <input class="form-control" name="inchNo" id="inchNo" type="text" value="<?php echo @$buildingName; ?>"
                                    placeholder='' readonly />
            </div>
            <div class="form-group col-md-4 col-sm-6 hidden">
                            <label class="input">Address Line 1</label>
                                <input class="form-control" name="line1" id="line1" type="text" placeholder='' value="<?php echo @$line1; ?>"
                                    readonly />
            </div>
            <div class="form-group col-md-4 col-sm-6 hidden">
                            <label class="input">Address Line 2</label>
                                <input class="form-control" name="line2" id="line2" type="text" placeholder='' value="<?php echo @$line2; ?>"
                                    readonly />
            </div>
            <div class="form-group col-md-4 col-sm-6 hidden">
                            <label class="input">Address Line 3</label>
                                <input class="form-control" name="inchRoad" id="inchRoad" type="text"
                                    value="<?php echo isset($inchRoad) ? @$inchRoad : ""; ?>" placeholder='' readonly />
            </div>
            <div class="form-group col-md-4 col-sm-6 hidden">
                            <label class="input">City / Town</label>
                                <input class="form-control" name="inchCity" id="inchCity" type="text" value="<?php echo @$city; ?>"
                                    placeholder='' readonly />
            </div>
            <div class="form-group col-md-4 col-sm-6 hidden">
                            <label class="input">Post Code</label>
                                <input class="form-control" name="inchPcode" id="inchPcode" type="text" value="<?php echo @$postCode; ?>"
                                    readonly />
            </div>
            <div class="bg-info col-xs-12 form-group"><h4>Interpreter Preferences</h4></div>
            <div class="form-group col-sm-6">
                  <label class="optional">DBS Checked Interpreter Required?</label><br>
                  <div class="radio-inline ri"><label><input name="dbs_checked" type="radio" value="0" required <?php if($dbs_checked=='0'){?> checked="checked"<?php } ?>/>
                  <span class="label label-success">Yes <i class="fa fa-check-circle"></i></span></label></div>
                  <div class="radio-inline ri"><label><input type="radio" name="dbs_checked" value="1" <?php if($dbs_checked=='1'){?> checked="checked"<?php } ?>/>
                  <span class="label label-danger" >No <i class="fa fa-remove"></i></span></label></div>
                </div>
                <div class="form-group col-sm-4">
                <label class="optional">Gender</label>
                  <select name="gender" id="gender" required class="form-control">
                  <option><?php echo $gender; ?></option>
                  <option disabled="">--- Select ---</option>
                  <option>Male</option>
                  <option>Female</option>
                  <option>No Preference</option>
                  </select>
                </div>
            <div class="form-group col-sm-6">
                  <label class="optional">Booking Status:</label><br>
                  <div class="radio-inline ri"><label><input name="jobStatus" type="radio" value="0" <?php if($jobStatus=='0'){?> checked="checked"<?php } ?>/>
                  <span class="label label-danger">Enquiry <i class="fa fa-question"></i></span></label></div>
                  <div class="radio-inline ri"><label><input type="radio" name="jobStatus" value="1" <?php if($jobStatus=='1'){?> checked="checked"<?php } ?>/>
                  <span class="label label-success">Confirmed <i class="fa fa-check-circle"></i></span></label></div>
                </div>
                            <div class="form-group col-sm-6">
                <label class="optional">Display job on website ?</label><br>
                  <div class="radio-inline ri"><label><input name="jobDisp" type="radio" value="1" required <?php if($jobDisp=='1'){?> checked="checked"<?php } ?>/>
                  <span class="label label-success" style="font-size:100%;padding: .5em 0.6em 0.5em;">Yes <i class="fa fa-check-circle"></i></span></label></div>
                  <div class="radio-inline ri"><label><input type="radio" name="jobDisp" value="0" <?php if($jobDisp=='0'){?> checked="checked"<?php } ?>/>
                  <span class="label label-danger" style="font-size:100%;padding: .5em 0.6em 0.5em;">No <i class="fa fa-remove"></i></span></label></div>
</div>
 <div class="form-group col-sm-12">
                    <b>NOTES (if Any):</b>
                    <textarea class="form-control col-sm-6" name="I_Comments" rows="4"></textarea>
</div>  
<div class="form-group col-sm-12">
                <script src='https://www.google.com/recaptcha/api.js'></script>
                <div class="g-recaptcha" data-sitekey="6LextRoUAAAAAGSGzslurL5xeNDw3lDDVkxM9rZe"></div>
</div>  
                        
            <div class="form-group col-sm-12">
                <input type="submit" name="submit" class="btn btn-lg btn-primary" value="Submit"/>
</div>

</div>
</form>
<hr>
    </section>
    <?php include 'source/footer.php';?>
</div>

<?php
if (isset($_POST['submit']) && $val == 0 && !empty($_POST['interp_cat'])) {
    $interp_cat=$_POST['interp_cat']; 
    $acttObj->editFun($table,$edit_id,'interp_cat',$interp_cat);}
if (isset($_POST['submit']) && $val == 0  && $_POST['interp_cat']!='12') {
    $interp_type=implode(",",$_POST['interp_type']);
    $acttObj->editFun($table,$edit_id,'interp_type',$interp_type);}

if (isset($_POST['submit']) && $val == 0) {
    $source = $_POST['source'];
    $acttObj->editFun($table, $edit_id, 'source', $source);}
?>

<?php if (isset($_POST['submit']) && $val == 0) {
    $target = $_POST['target'];
    $acttObj->editFun($table, $edit_id, 'target', $target);}
?>

<?php
if (isset($_POST['submit']) && $val == 0) {
    $assignDate = $_POST['assignDate'];
    $acttObj->editFun($table, $edit_id, 'assignDate', $assignDate);
    //$acttObj->editFunDate($table,$edit_id,'assignDate',$assignDate);
}
?>
<?php
if (isset($_POST['submit']) && $val == 0) {
    $bookedDate = $_POST['bookeddate'];
    $acttObj->editFun($table, $edit_id, 'bookeddate', $bookedDate);
}
?>
<?php
if (isset($_POST['submit']) && $val == 0) {
    $bookedTime = $_POST['bookedtime'];
    $acttObj->editFun($table, $edit_id, 'bookedtime', $bookedTime);
}
?>
<?php
if (isset($_POST['submit']) && $val == 0) {
    $assignTime = $_POST['assignTime'];
    $acttObj->editFun($table, $edit_id, 'assignTime', $assignTime);

}
?>
<?php
if (isset($_POST['submit']) && $val == 0) {
    $assignDur = $_POST['assignDur'];
    $acttObj->editFunTimeAsMins($table, $edit_id, 'assignDur', $assignDur);
    list($part1, $part2) = explode(':', $assignDur);
    $total_dur = $part1 * 60 + $part2;
    if($total_dur>60){
        $hours=$total_dur / 60;
        if(floor($hours)>1){$hr="hours";}else{$hr="hour";}
        $mins=$total_dur % 60;
            if($mins==00){
                $get_dur=sprintf("%2d $hr",$hours);  
            }else{
                $get_dur=sprintf("%2d $hr %02d minutes",$hours,$mins);  
            }
    }else if($total_dur==60){
        $get_dur="1 Hour";
    }else{
        $get_dur=$total_dur." minutes";
    }
}
?>

<?php
if (isset($_POST['submit']) && $val == 0 && !empty($_POST['interp_cat']) && $_POST['interp_cat']=='12') {
    $assignIssue = $_POST['assignIssue'];
    $acttObj->editFun($table, $edit_id, 'assignIssue', $assignIssue);
}
?>

<?php
if (isset($_POST['submit']) && $val == 0) {
    $orgRef = $_POST['orgRef'];
    $acttObj->editFun($table, $edit_id, 'orgRef', $orgRef);
    $ref_counter=$acttObj->read_specific("count(*) as counter","comp_ref","company='".$orgName."' AND reference='".$orgRef."'")['counter'];
    if($ref_counter==0 && !empty($orgRef)){
      $get_reference_id = $acttObj->get_id("comp_ref");
      $acttObj->update("comp_ref",array("company"=>$orgName,"reference"=>$orgRef),array("id"=>$get_reference_id));
      $acttObj->editFun($table, $edit_id, 'reference_id', $get_reference_id);
    }else{
      $existing_ref_id=$acttObj->read_specific("id","comp_ref","company='".$orgName."' AND reference='".$orgRef."'")['id'];
      $acttObj->editFun($table, $edit_id, 'reference_id', $existing_ref_id);
    }
}
?>
<?php
if (isset($_POST['submit']) && $val == 0) {
    $month = substr($month, 0, 3);
    $lastid = $acttObj->max_id($table) + 1;
    $nameRef = 'LSUK/' . $month . '/' . $edit_id;
    $acttObj->editFun($table, $edit_id, 'nameRef', $nameRef);

}
?>
<?php
if (isset($_POST['submit']) && $val == 0) {
    $buildingName = $_POST['buildingName'];
    $acttObj->editFun($table, $edit_id, 'buildingName', $buildingName);
}
?>
<?php
if (isset($_POST['submit']) && $val == 0) {
    $street = $_POST['street'];
    $acttObj->editFun($table, $edit_id, 'street', $street);
}
?>
<?php if (isset($_POST['submit']) && $val == 0) {
    $assignCity = $_POST['assignCity'];
    $acttObj->editFun($table, $edit_id, 'assignCity', $assignCity);}
?>
<?php if (isset($_POST['submit']) && $val == 0) {
    $postCode = $_POST['postCode'];
    $acttObj->editFun($table, $edit_id, 'postCode', $postCode);}
?>
<?php if (isset($_POST['submit']) && $val == 0) {$inchPerson = $_POST['inchPerson'];
    $acttObj->editFun($table, $edit_id, 'inchPerson', $inchPerson);}?>
<?php if (isset($_POST['submit']) && $val == 0) {$inchContact = $_POST['inchContact'];
    $acttObj->editFun($table, $edit_id, 'inchContact', $inchContact);}?>
<?php if (isset($_POST['submit']) && $val == 0) {$orgContact = $_POST['orgContact'];
    $acttObj->editFun($table, $edit_id, 'orgContact', $orgContact);}?>
<?php if (isset($_POST['submit']) && $val == 0) {$inchEmail = $_POST['inchEmail'];
    $acttObj->editFun($table, $edit_id, 'inchEmail', $inchEmail);}?>

<?php if (isset($_POST['submit']) && $val == 0) {$acttObj->editFun($table, $edit_id, 'orgName', $orgName);}?>
<?php if (isset($_POST['submit']) && $val == 0) {$inchNo = $_POST['inchNo'];
    $acttObj->editFun($table, $edit_id, 'inchNo', $inchNo);}?>
<?php if (isset($_POST['submit']) && $val == 0) {$line1 = $_POST['line1'];
    $acttObj->editFun($table, $edit_id, 'line1', $line1);}?>
<?php if (isset($_POST['submit']) && $val == 0) {$line2 = $_POST['line2'];
    $acttObj->editFun($table, $edit_id, 'line2', $line2);}?>


<?php if (isset($_POST['submit']) && $val == 0) {$inchRoad = $_POST['inchRoad'];
    $acttObj->editFun($table, $edit_id, 'inchRoad', $inchRoad);}?>
<?php if (isset($_POST['submit']) && $val == 0) {$inchCity = $_POST['inchCity'];
    $acttObj->editFun($table, $edit_id, 'inchCity', $inchCity);}?>
<?php if (isset($_POST['submit']) && $val == 0) {$inchPcode = $_POST['inchPcode'];
    $acttObj->editFun($table, $edit_id, 'inchPcode', $inchPcode);}?>
<?php if (isset($_POST['submit']) && $val == 0) {$dbs_checked = $_POST['dbs_checked'];
    $acttObj->editFun($table, $edit_id, 'dbs_checked', $dbs_checked);}?>

<?php if (isset($_POST['submit']) && $val == 0) {$jobStatus = $_POST['jobStatus'];
    $acttObj->editFun($table, $edit_id, 'jobStatus', $jobStatus);}?>
<?php if (isset($_POST['submit']) && $val == 0) {$jobDisp = $_POST['jobDisp'];
    $acttObj->editFun($table, $edit_id, 'jobDisp', $jobDisp);}?>
<?php if (isset($_POST['submit']) && $val == 0) {$I_Comments = $_POST['I_Comments'];
    $acttObj->editFun($table, $edit_id, 'I_Comments', $I_Comments);}?>
<?php if (isset($_POST['submit']) && $val == 0) {$gender = $_POST['gender'];
    $acttObj->editFun($table, $edit_id, 'gender', $gender);}?>
<?php
if (isset($_POST['submit']) && $val == 0) {
    $assignDate=$misc->dated($assignDate);
    $assignCity_name=explode(',',$_POST['assignCity']);
    $assignCity_req=$assignCity_name[0];
    $from_add = "info@lsuk.org";
    $to_add = $inchEmail;
    $subject = 'Acknowledgment of your booking request'; //"Order for Interpreter (F 2 F)";
    $write_interp_cat=$interp_cat=='12'?$assignIssue:$acttObj->read_specific("ic_title","interp_cat","ic_id=".$interp_cat)['ic_title'];
    $write_interp_type=$interp_cat=='12'?'':$acttObj->read_specific("GROUP_CONCAT(CONCAT(it_title)  SEPARATOR ' <b> & </b> ') as it_title","interp_types","it_id IN (".$interp_type.")")['it_title'];
    if($interp_cat=='12'){
        $append_issue="<tr><td style='border: 1px solid yellowgreen;padding:5px;'>Assignment Category</td><td style='border: 1px solid yellowgreen;padding:5px;'>Other</td><td style='border: 1px solid yellowgreen;padding:5px;'>Assignment Details</td><td style='border: 1px solid yellowgreen;padding:5px;'>".$assignIssue."</td></tr>";
    }else{
        $append_issue="<tr><td style='border: 1px solid yellowgreen;padding:5px;'>Assignment Category</td><td style='border: 1px solid yellowgreen;padding:5px;'>".$write_interp_cat."</td><td style='border: 1px solid yellowgreen;padding:5px;'>Assignment Details</td><td style='border: 1px solid yellowgreen;padding:5px;'>".$write_interp_type."</td></tr>";
    }
    $message = "<p>Dear " . $inchPerson . "</p>
<p>
Thanks for booking with LSUK. This is an acknowledgment of the following booking
</p>
<p>Language (" . $source . ")</p>
<p>Date (" . $assignDate . ")</p>
<p>Time (" . $assignTime . ")</p>
<p>At (" . $buildingName . " " . $street . " " . $assignCity . " " . $postCode . ")</p>

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
<caption align='center' style='background: grey;color: white;padding: 5px;'>Order for Interpreter (F 2 F)</caption>
<table class='myTable'>
<tr>
<td style='border: 1px solid yellowgreen;padding:5px;'>Source Language</td>
<td style='border: 1px solid yellowgreen;padding:5px;'>" . $source . "</td>
<td style='border: 1px solid yellowgreen;padding:5px;'>Target Language</td>
<td style='border: 1px solid yellowgreen;padding:5px;'>" . $target . "</td>
</tr>

<tr>
<td style='border: 1px solid yellowgreen;padding:5px;'>Assignment Date/Time</td>
<td style='border: 1px solid yellowgreen;padding:5px;'>" . $assignDate." ".$assignTime . "</td>
<td style='border: 1px solid yellowgreen;padding:5px;'>Assignment Duration</td>
<td style='border: 1px solid yellowgreen;padding:5px;'>" . $get_dur . "</td>
</tr>

".$append_issue."

<tr>
<td style='border: 1px solid yellowgreen;padding:5px;'>DBS checked interpreter Requested ?</td>
<td style='border: 1px solid yellowgreen;padding:5px;'>" . $dbs_checked==0?'Yes':'No' . "</td>
<td style='border: 1px solid yellowgreen;padding:5px;'>Booking Reference</td>
<td style='border: 1px solid yellowgreen;padding:5px;'>" . $nameRef . "</td>
</tr>

<tr>
<td colspan='4' align='center' style='background: grey; color: white;'>Assignment Location</td>
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
<td colspan='4' align='center' style='background: grey; color: white;'>Booking Organization Details</td>
</tr>
<tr>
<td style='border: 1px solid yellowgreen;padding:5px;'>Company Name</td>
<td style='border: 1px solid yellowgreen;padding:5px;'>" . $orgName . "</td>
<td style='border: 1px solid yellowgreen;padding:5px;'>Booking Ref/Name</td>
<td style='border: 1px solid yellowgreen;padding:5px;'>" . $orgRef . "</td>
</tr>
<tr>
<td style='border: 1px solid yellowgreen;padding:5px;'>Interpreter Contact Name</td>
<td style='border: 1px solid yellowgreen;padding:5px;'>" . $orgContact . "</td>
<td style='border: 1px solid yellowgreen;padding:5px;'>&nbsp;</td>
<td style='border: 1px solid yellowgreen;padding:5px;'>&nbsp;</td>
</tr>
<tr>
<td colspan='4' align='center' style='background: grey; color: white;'>Assignment in-Charge</td>
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
<td style='border: 1px solid yellowgreen;padding:5px;'>Booked Date</td>
<td style='border: 1px solid yellowgreen;padding:5px;'>" . $misc->dated($bookedDate) . "</td>
<td style='border: 1px solid yellowgreen;padding:5px;'>Booked Time</td>
<td style='border: 1px solid yellowgreen;padding:5px;'>" . $bookedTime . "</td>
</tr>
<tr>
<td colspan='4' align='center' style='background: grey; color: white;'>Interpreter Details</td>
</tr>
<tr>
<td style='border: 1px solid yellowgreen;padding:5px;'>Gender</td>
<td style='border: 1px solid yellowgreen;padding:5px;'>" . $gender . "</td>
<td style='border: 1px solid yellowgreen;padding:5px;'>Status</td>
<td style='border: 1px solid yellowgreen;padding:5px;'>" . $jobStatus==0?'Enquiry':'Confirmed' . "</td>
</tr>

<tr>
<td style='border: 1px solid yellowgreen;padding:5px;'>Notes if Any 1000 alphabets</td>
<td colspan='4' align='center' style='border: 1px solid yellowgreen;padding:5px;'>" . $I_Comments . "</td>
</tr>
</table>

<p>Kindest Regards </p>

<p>Admin Team</p>

<p>Language Services UK Limited</p>
";
$ack_message='Hi <b>Admin</b>
<p>This is an email acknowledgement for '.$source.' Face to Face Job of '.$get_dur.' requested by '.$orgName.' booked on '.$misc->dated($bookedDate).' '.$bookedTime.' for assignment date '.$assignDate.' '.$assignTime.'.</p>
<p>Kindly verify at LSUK system.</p>
<p>Thank you</p>';
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
    if($mail->send()){
        $mail->ClearAllRecipients();
        //$mail->addAddress('info@lsuk.org');
        $mail->addAddress('infolsuk786@gmail.com');
        $mail->addReplyTo($from_add, 'LSUK');
        $mail->isHTML(true);
        $mail->Subject = 'Acknowledgement for new Face to Face Online Portal Job';
        $mail->Body    = $ack_message;
        $mail->send();
        $mail->ClearAllRecipients();
        //Invoice //
        if ($_POST['jobStatus'] == 1) {
            $nmbr = $acttObj->get_id('invoice');
            if ($nmbr == null) {
                $nmbr = 0;
            }
            $new_nmbr = str_pad($nmbr, 5, "0", STR_PAD_LEFT);
            $invoice = date("my").$new_nmbr;
            $maxId = $nmbr;
            $acttObj->editFun('invoice', $maxId, 'invoiceNo', $invoice);
            $acttObj->editFun($table, $edit_id, 'invoiceNo', $invoice);
        }
        //Email notification to related interpreters
    if($jobDisp=='1' && $jobStatus=='1'){
    if($interp_cat=='12'){
        $append_issue_bid="<tr><td style='border: 1px solid yellowgreen;padding:5px;'>Assignment Category</td><td style='border: 1px solid yellowgreen;padding:5px;'>Other</td></tr><tr><td style='border: 1px solid yellowgreen;padding:5px;'>Assignment Details</td><td style='border: 1px solid yellowgreen;padding:5px;'>".$assignIssue."</td></tr>";
    }else{
        $append_issue_bid="<tr><td style='border: 1px solid yellowgreen;padding:5px;'>Assignment Category</td><td style='border: 1px solid yellowgreen;padding:5px;'>".$write_interp_cat."</td></tr><tr><td style='border: 1px solid yellowgreen;padding:5px;'>Assignment Details</td><td style='border: 1px solid yellowgreen;padding:5px;'>".$write_interp_type."</td></tr>";
    }
                $append_table="
<table>
<tr>
<td style='border: 1px solid yellowgreen;padding:5px;'>Source Language</td>
<td style='border: 1px solid yellowgreen;padding:5px;'>".$source."</td>
</tr>
<tr>
<td style='border: 1px solid yellowgreen;padding:5px;'>Target Language</td>
<td style='border: 1px solid yellowgreen;padding:5px;'>".$target."</td>
</tr>
<tr>
<td style='border: 1px solid yellowgreen;padding:5px;'>Assignment Date</td>
<td style='border: 1px solid yellowgreen;padding:5px;'>".$assignDate."</td>
</tr>
<tr>
<td style='border: 1px solid yellowgreen;padding:5px;'>Assignment Time</td>
<td style='border: 1px solid yellowgreen;padding:5px;'>".$assignTime."</td>
</tr>
<tr>
<td style='border: 1px solid yellowgreen;padding:5px;'>Assignment Duration</td>
<td style='border: 1px solid yellowgreen;padding:5px;'>".$get_dur."</td>
</tr>
<tr>
<td style='border: 1px solid yellowgreen;padding:5px;'>Assignment Location</td>
<td style='border: 1px solid yellowgreen;padding:5px;'>To be informed after successful allocation</td>
</tr>
".$append_issue_bid."
<tr>
<td style='border: 1px solid yellowgreen;padding:5px;'>Report to</td>
<td style='border: 1px solid yellowgreen;padding:5px;'>".$inchPerson."</td>
</tr>
<tr>
<td style='border: 1px solid yellowgreen;padding:5px;'>Case Worker or Person Incharge</td>
<td style='border: 1px solid yellowgreen;padding:5px;'>".$orgContact."</td>
</tr>
<tr>
<td style='border: 1px solid yellowgreen;padding:5px;'>Client Name</td>
<td style='border: 1px solid yellowgreen;padding:5px;'>".$orgRef."</td>
</tr>

</table>";
    if($gender=='' || $gender=='No Preference'){
        $put_gender="";
    }else{
        $put_gender="AND interpreter_reg.gender='$gender'";
    }
    if($source== $target){
    $put_lang="";$query_style='0';
}else if($source!='English' && $target!='English'){
    $put_lang="";$query_style='1';
}else if($source=='English' && $target!='English'){
    $put_lang="interp_lang.lang='$target' and interp_lang.level<3";$query_style='2';
}else if($source!='English' && $target=='English'){
    $put_lang="interp_lang.lang='$source' and interp_lang.level<3";$query_style='2';
}else{
    $put_lang="";$query_style='3';
}
if($query_style=='0'){
    $query_emails="SELECT DISTINCT interpreter_reg.name, interpreter_reg.email, interpreter_reg.id FROM interpreter_reg,interp_lang WHERE interpreter_reg.code=interp_lang.code AND (SELECT COUNT(DISTINCT interp_lang.lang) FROM interp_lang WHERE interp_lang.lang IN ('".$source."') and interp_lang.level<3 and interp_lang.code=interpreter_reg.code)=1 and 
    ((interpreter_reg.uk_citizen=1 AND interpreter_reg.id_doc_expiry_date != '1001-01-01' AND interpreter_reg.id_doc_expiry_date > CURRENT_DATE()) OR (interpreter_reg.uk_citizen=0 AND interpreter_reg.work_evid_expiry_date != '1001-01-01' AND interpreter_reg.work_evid_expiry_date > CURRENT_DATE())) 
                AND (interpreter_reg.is_dbs_auto=1 OR (interpreter_reg.is_dbs_auto=0 AND interpreter_reg.dbs_expiry_date != '1001-01-01' AND interpreter_reg.dbs_expiry_date > CURRENT_DATE())) AND interpreter_reg.active='0' AND (interpreter_reg.actnow='Active' OR (interpreter_reg.actnow='Inactive' AND CURRENT_DATE() NOT BETWEEN interpreter_reg.actnow_time AND interpreter_reg.actnow_to)) AND interpreter_reg.interp='Yes' AND interpreter_reg.dbs_checked='$dbs_checked' AND interpreter_reg.city LIKE '$assignCity_req' $put_gender AND interpreter_reg.deleted_flag=0 AND interpreter_reg.subscribe=1 AND interpreter_reg.is_temp=0 AND interpreter_reg.on_hold='No'";
}else if($query_style=='1'){
    $query_emails="SELECT DISTINCT interpreter_reg.name, interpreter_reg.email, interpreter_reg.id FROM interpreter_reg,interp_lang WHERE interpreter_reg.code=interp_lang.code AND (SELECT COUNT(DISTINCT interp_lang.lang) FROM interp_lang WHERE interp_lang.lang IN ('".$source."','".$target."') and interp_lang.level<3 and interp_lang.code=interpreter_reg.code)=2 and 
    ((interpreter_reg.uk_citizen=1 AND interpreter_reg.id_doc_expiry_date != '1001-01-01' AND interpreter_reg.id_doc_expiry_date > CURRENT_DATE()) OR (interpreter_reg.uk_citizen=0 AND interpreter_reg.work_evid_expiry_date != '1001-01-01' AND interpreter_reg.work_evid_expiry_date > CURRENT_DATE())) 
                AND (interpreter_reg.is_dbs_auto=1 OR (interpreter_reg.is_dbs_auto=0 AND interpreter_reg.dbs_expiry_date != '1001-01-01' AND interpreter_reg.dbs_expiry_date > CURRENT_DATE())) AND interpreter_reg.active='0' AND (interpreter_reg.actnow='Active' OR (interpreter_reg.actnow='Inactive' AND CURRENT_DATE() NOT BETWEEN interpreter_reg.actnow_time AND interpreter_reg.actnow_to)) AND interpreter_reg.interp='Yes' AND interpreter_reg.dbs_checked='$dbs_checked' AND interpreter_reg.city LIKE '$assignCity_req' $put_gender AND interpreter_reg.deleted_flag=0 AND interpreter_reg.subscribe=1 AND interpreter_reg.is_temp=0 AND interpreter_reg.on_hold='No'";
}else if($query_style=='2'){
    $query_emails="SELECT DISTINCT interpreter_reg.name, interpreter_reg.email, interpreter_reg.id FROM interpreter_reg,interp_lang WHERE interpreter_reg.code=interp_lang.code AND $put_lang and 
    ((interpreter_reg.uk_citizen=1 AND interpreter_reg.id_doc_expiry_date != '1001-01-01' AND interpreter_reg.id_doc_expiry_date > CURRENT_DATE()) OR (interpreter_reg.uk_citizen=0 AND interpreter_reg.work_evid_expiry_date != '1001-01-01' AND interpreter_reg.work_evid_expiry_date > CURRENT_DATE())) 
                AND (interpreter_reg.is_dbs_auto=1 OR (interpreter_reg.is_dbs_auto=0 AND interpreter_reg.dbs_expiry_date != '1001-01-01' AND interpreter_reg.dbs_expiry_date > CURRENT_DATE())) AND interpreter_reg.active='0' AND (interpreter_reg.actnow='Active' OR (interpreter_reg.actnow='Inactive' AND CURRENT_DATE() NOT BETWEEN interpreter_reg.actnow_time AND interpreter_reg.actnow_to)) AND interpreter_reg.interp='Yes' AND interpreter_reg.dbs_checked='$dbs_checked' AND interpreter_reg.city LIKE '$assignCity_req' $put_gender AND interpreter_reg.deleted_flag=0 AND interpreter_reg.subscribe=1 AND interpreter_reg.is_temp=0 AND interpreter_reg.on_hold='No'";
}else{
    $query_emails="SELECT DISTINCT interpreter_reg.name, interpreter_reg.email, interpreter_reg.id FROM interpreter_reg WHERE 
    ((interpreter_reg.uk_citizen=1 AND interpreter_reg.id_doc_expiry_date != '1001-01-01' AND interpreter_reg.id_doc_expiry_date > CURRENT_DATE()) OR (interpreter_reg.uk_citizen=0 AND interpreter_reg.work_evid_expiry_date != '1001-01-01' AND interpreter_reg.work_evid_expiry_date > CURRENT_DATE())) 
                AND (interpreter_reg.is_dbs_auto=1 OR (interpreter_reg.is_dbs_auto=0 AND interpreter_reg.dbs_expiry_date != '1001-01-01' AND interpreter_reg.dbs_expiry_date > CURRENT_DATE())) AND interpreter_reg.active='0' AND (interpreter_reg.actnow='Active' OR (interpreter_reg.actnow='Inactive' AND CURRENT_DATE() NOT BETWEEN interpreter_reg.actnow_time AND interpreter_reg.actnow_to)) AND interpreter_reg.interp='Yes' AND interpreter_reg.dbs_checked='$dbs_checked' AND interpreter_reg.city LIKE '$assignCity_req' $put_gender AND interpreter_reg.deleted_flag=0 AND interpreter_reg.subscribe=1 AND interpreter_reg.is_temp=0 AND interpreter_reg.on_hold='No'";
}
    $res_emails=mysqli_query($con,$query_emails);
    //Getting bidding email from em_format table
    $row_format=$acttObj->read_specific("em_format","email_format","id=28");
    $subject_int = "Bidding Invitation For Face To Face Project ".$edit_id;
    $sub_title = "New Face To Face job of ".$source." language is available for you to bid.";
    $type_key="nj";
    //$app_int_ids=array();
    while($row_emails=mysqli_fetch_assoc($res_emails)){
        if($acttObj->read_specific("COUNT(*) as blacklisted","interp_blacklist","interpName='id-".$row_emails['id']."' AND orgName='".$orgName."' AND deleted_flag=0 AND blocked_for=1")["blacklisted"]==0){
        $to_int_address = $row_emails['email'];
        //Send notification on APP
        $check_id=$acttObj->read_specific('id','notify_new_doc','interpreter_id='.$row_emails['id'])['id'];
        if(empty($check_id)){
            $acttObj->insert('notify_new_doc',array("interpreter_id"=>$row_emails['id'],"status"=>'1'));
        }else{
            $acttObj->update('notify_new_doc',array("new_notification"=>'0'),array("interpreter_id"=>$row_emails['id']));
        }
        $array_tokens=explode(',',$acttObj->read_specific("GROUP_CONCAT( DISTINCT token) as tokens","int_tokens","int_id=".$row_emails['id'])['tokens']);
        if(!empty($array_tokens)){
            $acttObj->insert('app_notifications',array("title"=>$subject_int,"sub_title"=>$sub_title,"dated"=>date('Y-m-d'),"int_ids"=>$row_emails['id'],"read_ids"=>$row_emails['id'],"type_key"=>$type_key));
            //array_push($app_int_ids,$row_emails['id']);
            foreach($array_tokens as $token){
                if(!empty($token)){
                    $full_data="{ \"notification\": {    \"title\": \"$subject_int\",     \"text\": \"$sub_title\"   }, \"data\": { \"click_action\": \"FLUTTER_NOTIFICATION_CLICK\",\"status\": \"done\" },    \"to\" : \"$token\"}";
                    $acttObj->notification($token,$subject_int,$sub_title,$full_data);
                }
            }
        }
        //Replace date in email bidding
        $data   = ["[NAME]", "[ASSIGNTIME]", "[ASSIGNDATE]", "[POSTCODE]", "[TABLE]", "[EDIT_ID]"];
        $to_replace  = [$row_emails['name'], "$assignTime", "$assignDate", "$postCode", "$append_table", "$edit_id"];
        $message_int = str_replace($data, $to_replace,$row_format['em_format']);
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
        //Email ot interpreters ends here
        $acttObj->editFun($table, $edit_id, 'bookedVia', 'Online Portal');
        echo "<script>alert('Thanks for booking with LSUK Limited. You have successfully submitted the form. " .
            "You will shortly receive a confirmation email of the request. Please check your email. " .
            "Any problem please get in touch with LSUK Booking Team on 01173290610.');</script>";
    }else{
        echo "<script>alert('Oops..Email not submited!');</script>";
    }
} catch (Exception $e) {?>
<script>alert("Message could not be sent! Mailer library error.");</script>
<?php }
    $acttObj->editFun($table, $edit_id, 'submited', 'Online');
    $acttObj->editFun($table, $edit_id, 'edited_by', 'Online');
    $acttObj->editFun($table, $edit_id, 'edited_date', date("Y-m-d H:i:s"));
    // $acttObj->new_old_table('hist_' . $table, $table, $edit_id);
}
?>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
      <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.13/css/bootstrap-multiselect.css"rel="stylesheet" type="text/css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.13/js/bootstrap-multiselect.js"type="text/javascript"></script>
<script type="text/javascript">
$(function() {
  $('.multi_class').multiselect({includeSelectAllOption: true,numberDisplayed: 1,enableFiltering: true,enableCaseInsensitiveFiltering: true});
});
function get_interp_type(elem){
    var ic_id=elem.val();
    $.ajax({
        url:'ajax_client_portal.php',
        method:'post',
        data:{ic_id:ic_id},
        success:function(data){
            if(data){
                $('#div_it').css('display','block');
                $('#div_assignIssue').css('display','none');
                $('#assignIssue').css('display','none');
                $('#div_it').html(data);
            }else{
                $('#div_it').html(data);
                $('#div_it').css('display','none');
                $('#div_assignIssue').css('display','block');
                $('#assignIssue').css('display','block');
            }
    $(function() {
        $('.multi_class').multiselect({includeSelectAllOption: true,numberDisplayed: 1,enableFiltering: true,enableCaseInsensitiveFiltering: true});
        });
    }, error: function(xhr){
        alert("An error occured: " + xhr.status + " " + xhr.statusText);
    }
    });
}
$(document).ready(function(){
    $('.search-box input[type="text"]').on("keyup input", function(){
        /* Get input value on change */
        var inputVal = $(this).val();var orgName = '<?php echo $orgName; ?>';
        var resultDropdown = $(this).siblings(".result");
        if(inputVal.length){
            $.get("ajax_client_portal.php", {term: inputVal,orgName:orgName}).done(function(data){
                // Display the returned data in browser
                resultDropdown.html(data);
                $('#confirm_orgRef').show();
            });
        }else{
            resultDropdown.empty();
            $('#confirm_orgRef').show();
        }
    });
    // Set search input value on click of result item
    $(document).on("click", ".result p.click", function(){
        $(this).parents(".search-box").find('input[type="text"]').val($(this).text());
        $(this).parent(".result").empty();
        $('#confirm_orgRef').hide();
    });
});
</script>
</body>
</html>