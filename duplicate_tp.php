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
    echo '<script>window.location="index.php";</script>';
}

include 'source/db.php';
include 'source/class.php';
$id=base64_decode($_GET['id']);
$table = 'telephone';
$row=$acttObj->read_specific("$table.*,comunic_types.*","$table,comunic_types","$table.comunic=comunic_types.c_id AND $table.id=".$id);
$query_get_info = $acttObj->read_specific("name","comp_reg","abrv='".$row['orgName']."'");
$orgName = $row['orgName'];$name = $query_get_info['name'];
  $source=$row['source'];$target=$row['target'];$assignDate=$row['assignDate'];
  $assignTime=$row['assignTime'];$assignDur=$row['assignDur'];$nameRef=$row['nameRef'];$telep_cat=$row['telep_cat'];$telep_type=$row['telep_type'];
  $noClient=$row['noClient'];$contactNo=$row['contactNo'];$inchPerson=$row['inchPerson'];$inchEmail2=$row['inchEmail2'];
  $inchContact=$row['inchContact'];$inchEmail=$row['inchEmail'];$inchNo=$row['inchNo'];
  $line1=$row['line1'];$line2=$row['line2'];$inchRoad=$row['inchRoad'];$inchCity=$row['inchCity'];
  $inchPcode=$row['inchPcode'];$orgName=$row['orgName'];$orgRef=$row['orgRef'];$orgContact=$row['orgContact'];
  $remrks=$row['remrks'];$gender=$row['gender'];$intrpName=$row['intrpName'];$jobStatus=$row['jobStatus'];
  $bookinType=$row['bookinType'];$I_Comments=$row['I_Comments'];$comunic=$row['comunic'];$c_title_edit=$row['c_title'];$c_image_edit=$row['c_image'];$check=$acttObj->read_specific("c_cat","comunic_types","c_id=".$comunic)['c_cat'];
  $assignIssue=$row['assignIssue'];$snote=$row['snote'];$jobDisp=$row['jobDisp'];
  $invoiceNo=$row['invoiceNo'];$bookedVia=$row['bookedVia'];
  $bookeddate=$row['bookeddate'];$noty=$row['noty'];
  $noty_reason=$row['noty_reason'];$bookedtime=$row['bookedtime'];
  $dbs_bookednamed=$row['namedbooked'];$is_temp=$row['is_temp'];
  $po_req=$acttObj->read_specific("po_req","comp_reg","abrv='".$orgName."'")['po_req'];$porder_email=$row['porder_email'];
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
    $v_source = @$_POST['source'];
    $v_assignDate = @$_POST['assignDate'];
    $v_assignTime = @$_POST['assignTime'];
    $v_orgName = @$_POST['orgName'];
    $v_orgContact = @$_POST['orgContact'];
    $v_orgRef = @$_POST['orgRef'];
    $query = "SELECT count(id) as val FROM $table where source='$v_source' and assignDate='$v_assignDate' and
        assignTime='$v_assignTime' and orgName='$v_orgName' and orgContact='$v_orgContact' and orgRef='$v_orgRef'";

    $result = mysqli_query($con, $query);
    while ($row = mysqli_fetch_array($result)) {
        $val = $row['val'];
    }

    if ($val == 0) {
        $edit_id = $acttObj->get_id($table);
    } else {
        echo "<script>alert('oops..This job is already booked!');window.history.back();";
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
$line2 = $row_selected['line2'];?>

<!DOCTYPE HTML>
<html class="no-js"> <!--<![endif]-->
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
  } );
  </script>
<?php }else{ ?>
    <script src="lsuk_system/js/jquery-1.11.3.min.js"></script>
<?php } ?>
<style>.ri{margin-top: 10px;}
.ri .label{font-size:100%;padding: .5em 0.6em 0.5em;}
.checkbox-inline+.checkbox-inline, .radio-inline+.radio-inline {
    margin-top: 4px;}
    .multiselect {min-width: 295px;}.multiselect-container {max-height: 400px;overflow-y: auto;max-width: 380px;}.multiselect-native-select{display:block;}.multiselect-container li.active label.checkbox{color:white;}
    .sky-form select{-webkit-appearance: auto !important;}
    /* Formatting search box */
.search-box{
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
    width: 91% !important;
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
            <h1>Place an Order (Telephone)</h1>
          <nav id="breadcrumbs">
               <ul>
                    <li><a href="index.php">Home</a> &rsaquo;</li>

                </ul>
            </nav>
        </div>
    </section>
    <!-- begin content -->
 <form class="sky-form" action="#" method="post">
<section id="content" class="container_fluid clearfix">
    <div class="col-lg-6">
    <table class="table table-bordered">
        <tr class="bg-info"><td align="center" colspan="4"><h4 style="text-transform: uppercase;font-weight: 600;"><?php echo $name;?></h4></td></tr>
        <tr><td><b>Building No / Name:</b></td><td><?php echo $buildingName;?></td><td><b>Address:</b></td><td><?php echo $line1.' '.$line2.' '.$line3; ?></td></tr>
        <tr><td><b>City:</b></td><td><?php echo $city; ?></td><td><b>Post Code:</b></td><td><?php echo $postCode;?></td></tr>
    </table>
<div class="bg-info col-xs-12 form-group"><h4>Work Details</h4></div>
<center><div class="form-group">
            <div class="radio-inline ri"><label><input onchange="av(this);" name="cls_checker" type="radio" value="a" <?php if($check=='a'){echo "checked";} ?>/>
            <span class="label label-info">Audio Interpreting</span></label></div>
            <div class="radio-inline ri"><label><input onchange="av(this);" name="cls_checker" type="radio" value="v" <?php if($check=='v'){echo "checked";} ?>/>
            <span class="label label-warning">Video Interpreting</span></label></div>
            <div class="radio-inline ri"><label><input onchange="av(this);" name="cls_checker" type="radio" value="b" <?php if($check=='b'){echo "checked";} ?>/>
            <span class="label label-success">Both</span></label></div>
        </div></center>
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
    $options .= "<OPTION value='$code'>" . $name_opt;}
?>
        <option value="<?php echo $row['source'];?>"><?php echo $row['source'];?></option>
          <option value="">--Select--</option>
          <?php echo $options; ?>
        </select>
              <?php if (isset($_POST['submit']) && $val == 0) {$source = $_POST['source'];
    $acttObj->editFun($table, $edit_id, 'source', $source);}?>
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
    $options .= "<OPTION value='$code'>" . $name_opt;}
?>
                            <option value="<?php echo $row['target'];?>"><?php echo $row['target'];?></option>
                            <option value="">--Select--</option>
                            <?php echo $options; ?>
                            </option>
                          </select>
              <?php if (isset($_POST['submit']) && $val == 0) {$target = $_POST['target'];
    $acttObj->editFun($table, $edit_id, 'target', $target);}?>
</div>
<div class="form-group col-md-6 col-sm-6" id="div_comunic">
                <label>Select Communication Type</label>
              <select class="form-control" name="comunic" id="comunic" required="">
       <?php $put_var=$check!='b'?"and c_cat='$check'":"";
        $q_types=$acttObj->read_all("c_id,c_title,c_image","comunic_types","c_status=1 $put_var ORDER BY c_title");
        $options="";
        while ($row_types=$q_types->fetch_assoc()) {
            $c_id=$row_types["c_id"];
            $c_title=$row_types["c_title"];
            $c_image=$row_types["c_image"];
            $options.="<option value='$c_id'>".$c_title."</option>";
            } ?>
                    <?php if(empty($comunic)){ ?>
                  <option selected value="">Select Type</option>
                  <?php }else{ ?>
                  <option value="<?php echo $comunic; ?>"><?php echo $c_title_edit; ?></option>
                  <?php } ?>
                    <?php echo $options; ?>
                  </select>
               <?php if(isset($_POST['submit'])){$comunic=$_POST['comunic'];$acttObj->editFun($table,$edit_id,'comunic',$comunic);} ?>
            </div>
            <div class="form-group col-md-6 col-sm-6" id="div_tpc">
                <label>Select Telephone Category</label>
              <select name="telep_cat" id="telep_cat"  class="form-control" onchange="get_telep_type($(this));" required>
<?php       
$q_telep_cat=$acttObj->read_all("tpc_id,tpc_title","telep_cat","tpc_status=1 ORDER BY tpc_title ASC");
$opt_tpc="";
while ($row_tpc=$q_telep_cat->fetch_assoc()) {
    $tpc_id=$row_tpc["tpc_id"];
    $tpc_title=$row_tpc["tpc_title"];
    $opt_tpc.="<option value='$tpc_id'>".$tpc_title."</option>";}
?>
        <?php if(empty($telep_cat)){ ?>
        <option disabled selected value="">Select Telephone Category</option>
                  <?php }else{ ?>
                  <option value="<?php echo $telep_cat; ?>"><?php echo utf8_encode($acttObj->read_specific("tpc_title","telep_cat","tpc_id=".$telep_cat)['tpc_title']); ?></option>
                  <?php } ?>
        <?php echo $opt_tpc; ?>
        </select>
    </div>
    <div class="form-group col-md-6 col-sm-6" id="div_tpt" <?php if($telep_cat=='11'){ echo "style='display:none;'";} ?>>
        <label>Select Telephone Details</label>
        <select name="telep_type[]"  multiple="multiple" id="telep_type" class="form-control multi_class" <?php if($telep_cat!='11'){ echo "required";} ?>>
                    <?php $q_tpt=$acttObj->read_all('tpt_id,tpt_title','telep_types',"tpc_id='$telep_cat' AND tpt_id NOT IN ($telep_type) ORDER BY tpt_title ASC");
                    $arr_telep_type=explode(',',$telep_type);
                    for($tpt_i=0;$tpt_i<count($arr_telep_type);$tpt_i++){
                        $option_tpt.="<option selected value='$arr_telep_type[$tpt_i]'>".utf8_encode($acttObj->read_specific("tpt_title","telep_types","tpt_id=".$arr_telep_type[$tpt_i])['tpt_title'])."</option>";
                    }
                    echo $option_tpt;
                    while($row_tpt=$q_tpt->fetch_assoc()){
                    echo '<option value="'.$row_tpt['tpt_id'].'">'.utf8_encode($row_tpt['tpt_title']).'</option>';
                        } ?>
                </select>
    <?php if(isset($_POST['submit']) && !empty($_POST['telep_cat'])){$post_telep_cat=$_POST['telep_cat']; $acttObj->editFun($table,$edit_id,'telep_cat',$post_telep_cat);}
      if(isset($_POST['submit']) && $_POST['telep_cat']!='11'){$post_telep_type=implode(",",$_POST['telep_type']); $acttObj->editFun($table,$edit_id,'telep_type',$post_telep_type);} ?>
      </div>
    <?php if(isset($_POST['submit']) && $val == 0 && !empty($_POST['telep_cat'])){$telep_cat=$_POST['telep_cat']; $acttObj->editFun($table,$edit_id,'telep_cat',$telep_cat);}
      if(isset($_POST['submit']) && $val == 0 && $_POST['telep_cat']!='11'){$telep_type=implode(",",$_POST['telep_type']); $acttObj->editFun($table,$edit_id,'telep_type',$telep_type);} ?>
            <div class="form-group col-sm-6" id="div_assignIssue" <?php if($telep_cat!='11'){echo "style='display:none;'";} ?>>
                  <textarea title="Assignment Issue" placeholder="Write Assignment Issue Here ..." name="assignIssue" type="text" class="form-control" id="assignIssue" row="3"><?php echo $assignIssue; ?></textarea>
                </div>
                 <?php if(isset($_POST['submit']) && $val == 0 && !empty($_POST['telep_cat']) && $_POST['telep_cat']=='11') {$assignIssue=$_POST['assignIssue'];$acttObj->editFun($table,$edit_id,'assignIssue',$assignIssue);} ?>
      <div class="form-group col-md-6 col-sm-6">
                            <label class="input">Assignment  Date *</label>
                 <input class="form-control date_picker" type="date" name="assignDate" id="assignDate" required='' value='<?php echo $assignDate; ?>'/>
                   </label>
              <?php if (isset($_POST['submit']) && $val == 0) {$assignDate = $_POST['assignDate'];
    $acttObj->editFun($table, $edit_id, 'assignDate', $assignDate);}?>
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
<input onkeyup="dur_finder();" name="assignTime" id="assignTime" type="time"  step="300" class="form-control time_picker" required='' value="<?php echo $assignTime; ?>" />
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
    <?php if (isset($_POST['submit']) && $val == 0) {$assignTime = $_POST['assignTime'];
    $acttObj->editFun($table, $edit_id, 'assignTime', $assignTime);}?>

    <?php if (isset($_POST['submit']) && $val == 0) {
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
}?>

      <div class="form-group col-md-6 col-sm-6" title="System generated ID by LSUK">
                            <label class="input">Booking Reference *</label>
     <input name="nameRef" type="text" required='' readonly="readonly" class="form-control" value="<?php echo $nameRef; ?>"/>
                <?php if (isset($_POST['submit']) && $val == 0) {
                     $month = substr($month, 0, 3);
        $lastid = $acttObj->max_id($table) + 1;
        $nameRef = 'LSUK/' . $month . '/' . $edit_id;
    $acttObj->editFun($table, $edit_id, 'nameRef', $nameRef);}?>
</div>
      <div class="form-group col-md-6 col-sm-6">
                            <label class="input">Telephone Number (Client)*</label>
      <input type="text" class="form-control" id="noClient" name="noClient" required='' value="<?php echo $noClient; ?>"/>
              <?php if (isset($_POST['submit']) && $val == 0) {$noClient = $_POST['noClient'];
    $acttObj->editFun($table, $edit_id, 'noClient', $noClient);$contactNo = $_POST['contactNo'];
    $acttObj->editFun($table, $edit_id, 'contactNo', $contactNo);}?>
</div>
      <div class="form-group col-md-6 col-sm-6">
            <label class="input">Telephone Number <i class="fa fa-question-circle" title="IF Service User Is Not Present"></i></label>
      <input type="text" class="form-control" id="contactNo" name="contactNo" required='' value="<?php echo $contactNo ?>"/>
               <?php if (isset($_POST['submit']) && $val == 0) {$contactNo = $_POST['contactNo'];
    $acttObj->editFun($table, $edit_id, 'contactNo', $contactNo);}?>
</div>
<div class="form-group col-sm-12">
    <script src='https://www.google.com/recaptcha/api.js'></script>
    <div class="g-recaptcha" data-sitekey="6LextRoUAAAAAGSGzslurL5xeNDw3lDDVkxM9rZe"></div>
</div>
<div class="form-group col-sm-12">
    <input type="submit" name="submit" class="btn btn-lg btn-primary" value="Submit"/>
</div>
</div>
<div class="col-lg-6">
<div class="bg-info col-xs-12 form-group"><h4>Assignment Details</h4></div>
      <div class="form-group col-md-6 col-sm-6">
                            <label class="input">Booking Person Name if Different</label>
                            <input name="inchPerson" type="text" class="long form-control" value="<?php echo @$contactPerson; ?>"/>
        <?php if (isset($_POST['submit']) && $val == 0) {$inchPerson = $_POST['inchPerson'];
    $acttObj->editFun($table, $edit_id, 'inchPerson', $inchPerson);}?>
</div>
      <div class="form-group col-md-6 col-sm-6">
                            <label class="input">Interpreter Contact Name&nbsp;*</label>
      <input name="orgContact" id="orgContact" type="text" value=""  placeholder='' required='' class="form-control"/>
                <?php if (isset($_POST['submit']) && $val == 0) {$orgContact = $_POST['orgContact'];
    $acttObj->editFun($table, $edit_id, 'orgContact', $orgContact);}?>
</div>
      <div class="form-group col-md-6 col-sm-6">
                            <label class="input">Contact Number</label>
      <input name="inchContact" id="inchContact" type="text" class="long form-control"  value="<?php echo @$contactNo1; ?>"/>
                    <?php if (isset($_POST['submit']) && $val == 0) {$inchContact = $_POST['inchContact'];
    $acttObj->editFun($table, $edit_id, 'inchContact', $inchContact);}?>
</div>
      <div class="form-group col-md-6 col-sm-6">
                            <label class="input">Email Address&nbsp;</label>
      <input name="inchEmail" id="inchEmail" type="email" class="long form-control" value="<?php echo @$email; ?>" placeholder='' required/>
                  <?php if (isset($_POST['submit']) && $val == 0) {$inchEmail = $_POST['inchEmail'];
    $acttObj->editFun($table, $edit_id, 'inchEmail', $inchEmail);}?>
</div>
      <div class="form-group col-md-6 col-sm-6 search-box">
                <label class="input">Booking Reference Number <i class="fa fa-question-circle" title="(Name, Initials or File Ref. Number)"></i></label>
                <input class="form-control" name="orgRef" id="orgRef" type="text" required='' autocomplete="off" placeholder="Type Reference Number/Name" value="<?php echo $orgRef; ?>"/>
                <i id="confirm_orgRef" style="display:none;position: absolute;right: 25px;top: 35px;" onclick="$(this).hide();$('.result').empty();" class="glyphicon glyphicon-ok-sign text-success" title="Confirm this reference"></i>
                <div class="result"></div>
            <?php if (isset($_POST['submit']) && $val == 0) {
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
                } ?>
</div>

      <div class="form-group col-md-6 col-sm-6">
                            <label class="input">Booking Date*</label>
      <input onchange="OnDateChgAjax();" type="date" name="bookeddate" id="bookeddate" required='' class="form-control date_picker" value="<?php echo @$bookeddate ?>" />
                                    <?php
if (isset($_POST['submit'])) {
    $bookedDate = $_POST['bookeddate'];
    $acttObj->editFun($table, $edit_id, 'bookeddate', $bookedDate);
}
?>
</div>
      <div class="form-group col-md-6 col-sm-6">
                            <label class="input">Booking Time*</label>
      <input onchange="OnTimeChgAjax();" type="time" name="bookedtime" id="bookedtime" required='' step="300" class="form-control time_picker2"
                                    value="<?php echo @$bookedtime ?>" />
                                    <?php
if (isset($_POST['submit'])) {
    $bookedTime = $_POST['bookedtime'];
    $acttObj->editFun($table, $edit_id, 'bookedtime', $bookedTime);
}
?>
              </div>
              <div class="bg-info col-xs-12 form-group hidden"><h4>Booking organization Details</h4></div>
      <div class="form-group col-md-4 col-sm-6 hidden">
                            <label class="input">Company / Organization <i class="fa fa-question-circle" title="Team / Unit Name or Number"></i></label>
   <input name="orgName" id="orgName" type="text" value="<?php echo @$name_comp; ?>"  placeholder='' class="form-control" readonly />
       <?php if (isset($_POST['submit']) && $val == 0) {$acttObj->editFun($table, $edit_id, 'orgName', $orgName);}?>
                            </div>
      <div class="form-group col-md-4 col-sm-6 hidden">
                            <label class="input">Building Number / Name</label>
      <input name="inchNo" class="form-control" id="inchNo" type="text" value="<?php echo @$buildingName; ?>" placeholder='' readonly />
                <?php if (isset($_POST['submit']) && $val == 0) {$inchNo = $_POST['inchNo'];
    $acttObj->editFun($table, $edit_id, 'inchNo', $inchNo);}?>
    </div>
      <div class="form-group col-md-4 col-sm-6 hidden">
                            <label class="input">Address Line</label>
      <input name="line1" class="form-control" id="line1" type="text" placeholder='' value="<?php echo @$line1; ?>" readonly/>
                <?php if (isset($_POST['submit']) && $val == 0) {$line1 = $_POST['line1'];
    $acttObj->editFun($table, $edit_id, 'line1', $line1);}?>
    </div>
      <div class="form-group col-md-4 col-sm-6 hidden">
                            <label class="input">Address Line 2</label>
                <input name="line2" class="form-control" id="line2" type="text" placeholder='' value="<?php echo @$line2; ?>" readonly/>
                <?php if (isset($_POST['submit']) && $val == 0) {$line2 = $_POST['line2'];
    $acttObj->editFun($table, $edit_id, 'line2', $line2);}?>
    </div>
      <div class="form-group col-md-4 col-sm-6 hidden">
                            <label class="input">Address Line 3</label>
                <input name="inchRoad" class="form-control" id="inchRoad" type="text" value="<?php echo @$inchRoad; ?>" placeholder='' readonly/>
              <?php if (isset($_POST['submit']) && $val == 0) {$inchRoad = $_POST['inchRoad'];
    $acttObj->editFun($table, $edit_id, 'inchRoad', $inchRoad);}?>
    </div>
      <div class="form-group col-md-4 col-sm-6 hidden">
                            <label class="input">City</label>
      <input name="inchCity" id="inchCity" class="form-control" type="text" value="<?php echo @$city; ?>" placeholder='' readonly/>
                <?php if (isset($_POST['submit']) && $val == 0) {$inchCity = $_POST['inchCity'];
    $acttObj->editFun($table, $edit_id, 'inchCity', $inchCity);}?>
    </div>
      <div class="form-group col-md-4 col-sm-6 hidden">
                            <label class="input">Post Code</label>
      <input name="inchPcode" class="form-control" id="inchPcode" type="text" value="<?php echo @$postCode; ?>" readonly/>
              <?php if (isset($_POST['submit']) && $val == 0) {$inchPcode = $_POST['inchPcode'];
    $acttObj->editFun($table, $edit_id, 'inchPcode', $inchPcode);}?>
    </div>
<div class="bg-info col-xs-12 form-group"><h4>Interpreter Preferences</h4></div>        
            <div class="form-group col-lg-4 col-md-6 col-sm-6">
                  <label class="optional">Interpreter Gender</label>
                  <select name="gender" class="form-control" required>
                      <option><?php echo $gender; ?></option>
                      <option value="">--Select--</option>
                      <option value="Male">Male</option>
                      <option value="Female">Female</option>
                      <option value="No Preference">No Preference</option>
                  </select>
                  <?php if (isset($_POST['submit']) && $val == 0) {$gender = $_POST['gender'];
    $acttObj->editFun($table, $edit_id, 'gender', $gender);}?>
                </div>
                        
            <div class="form-group col-md-8 col-sm-8">
                  <label class="optional">Booking Status: </label><br>
                  <div class="radio-inline ri"><label><input name="jobStatus" type="radio" value="0" <?php if($jobStatus=='0'){?> checked="checked"<?php } ?>/>
                  <span class="label label-danger" style="font-size:100%;padding: .5em 0.6em 0.5em;">Enquiry <i class="fa fa-question"></i></span></label></div>
                  <div class="radio-inline ri"><label><input type="radio" name="jobStatus" value="1" <?php if($jobStatus=='1'){?> checked="checked"<?php } ?>/>
                  <span class="label label-success" style="font-size:100%;padding: .5em 0.6em 0.5em;">Confirmed <i class="fa fa-check-circle"></i></span></label></div>
                  <?php if (isset($_POST['submit']) && $val == 0) {$jobStatus = $_POST['jobStatus'];
    $acttObj->editFun($table, $edit_id, 'jobStatus', $jobStatus);}?>
                </div>

                 <div class="form-group col-lg-4 col-md-6 col-sm-6">
                <label class="optional">Display job on website ?</label><br>
                  <div class="radio-inline ri"><label><input name="jobDisp" type="radio" value="1" <?php if($jobDisp=='1'){?> checked="checked"<?php } ?>/>
                  <span class="label label-success" style="font-size:100%;padding: .5em 0.6em 0.5em;">Yes <i class="fa fa-check-circle"></i></span></label></div>
                  <div class="radio-inline ri"><label><input type="radio" name="jobDisp" value="0" <?php if($jobDisp=='0'){?> checked="checked"<?php } ?>/>
                  <span class="label label-danger" style="font-size:100%;padding: .5em 0.6em 0.5em;">No <i class="fa fa-remove"></i></span></label></div>
                  <?php if (isset($_POST['submit']) && $val == 0) {$jobDisp = $_POST['jobDisp'];
    $acttObj->editFun($table, $edit_id, 'jobDisp', $jobDisp);}?>
</div>              
            <div class="form-group col-sm-12">
                    <b>NOTES (if Any):</b>
                    <textarea class="form-control col-sm-6" name="I_Comments" rows="3"></textarea>
                        <?php if (isset($_POST['submit']) && $val == 0) {$I_Comments = $_POST['I_Comments'];
    $acttObj->editFun($table, $edit_id, 'I_Comments', $I_Comments);}?>
</div>
</div>
</form>
    </section>
        <hr>
    <?php include 'source/footer.php';?>
</div>
<?php
if (isset($_POST['submit']) && $val == 0) {
    $assignDate=$misc->dated($assignDate);
    $from_add = "info@lsuk.org";
    $to_add = $inchEmail;
    $subject = 'Acknowledgment of your booking request'; //"Order for Interpreter (TP)";
    $write_telep_cat=$telep_cat=='11'?$assignIssue:$acttObj->read_specific("tpc_title","telep_cat","tpc_id=".$telep_cat)['tpc_title'];
    $write_telep_type=$telep_cat=='11'?'':$acttObj->read_specific("GROUP_CONCAT(CONCAT(tpt_title)  SEPARATOR ' <b> & </b> ') as tpt_title","telep_types","tpt_id IN (".$telep_type.")")['tpt_title'];
    if($telep_cat=='11'){
        $append_issue="<tr><td style='border: 1px solid yellowgreen;padding:5px;'>Assignment Category</td><td style='border: 1px solid yellowgreen;padding:5px;'>Other</td><td style='border: 1px solid yellowgreen;padding:5px;'>Assignment Details</td><td style='border: 1px solid yellowgreen;padding:5px;'>".$assignIssue."</td></tr>";
    }else{
        $append_issue="<tr><td style='border: 1px solid yellowgreen;padding:5px;'>Assignment Category</td><td style='border: 1px solid yellowgreen;padding:5px;'>".$write_telep_cat."</td><td style='border: 1px solid yellowgreen;padding:5px;'>Assignment Details</td><td style='border: 1px solid yellowgreen;padding:5px;'>".$write_telep_type."</td></tr>";
    }
    $write_comunic = $acttObj->read_specific("c_title","comunic_types","c_id=".$comunic)['c_title'];
    $message = "<p>Dear " . $inchPerson . "</p>
<p>
Thanks for booking with LSUK. This is an acknowledgment of the following booking
</p>
<p>Language (" . $source . ")</p>
<p>Date (" . $assignDate . ")</p>
<p>Time (" . $assignTime . ")</p>

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
<caption align='center' style='background: grey;color: white;padding: 5px;'>Order for Telephone Interpreting</caption>
<table class='myTable'>
<tr>
<td style='border: 1px solid yellowgreen;padding:5px;'>Source Language</td>
<td style='border: 1px solid yellowgreen;padding:5px;'>" . $source . "</td>
<td style='border: 1px solid yellowgreen;padding:5px;'>Target Language</td>
<td style='border: 1px solid yellowgreen;padding:5px;'>" . $target . "</td>
</tr>

<tr>
<td style='border: 1px solid yellowgreen;padding:5px;'>Assignment Date / Time</td>
<td style='border: 1px solid yellowgreen;padding:5px;'>" . $assignDate." ".$assignTime . "</td>
<td style='border: 1px solid yellowgreen;padding:5px;'>Assignment Duration</td>
<td style='border: 1px solid yellowgreen;padding:5px;'>" . $get_dur . "</td>
</tr>
<tr>
<td style='border: 1px solid yellowgreen;padding:5px;'>Communication Type</td>
<td style='border: 1px solid yellowgreen;padding:5px;'>" . $write_comunic . "</td>
<td style='border: 1px solid yellowgreen;padding:5px;'>Booking Date</td>
<td style='border: 1px solid yellowgreen;padding:5px;'>".$misc->dated($bookedDate)."</td>
</tr>
".$append_issue."
<tr>
<td colspan='4' align='center' style='background: grey; color: white;'>Assignment Location</td>
</tr>
<tr>
<td style='border: 1px solid yellowgreen;padding:5px;'>Service user Contact Number</td>
<td style='border: 1px solid yellowgreen;padding:5px;'>" . $noClient . "</td>
<td style='border: 1px solid yellowgreen;padding:5px;'>Contact No for Ph. Interpreting</td>
<td style='border: 1px solid yellowgreen;padding:5px;'>" . $contactNo . "</td>
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
<td colspan='4' align='center' style='background: grey; color: white;'>Interpreter Details</td>
</tr>
<tr>
<td style='border: 1px solid yellowgreen;padding:5px;'>Gender</td>
<td style='border: 1px solid yellowgreen;padding:5px;'>" . $gender . "</td>
<td style='border: 1px solid yellowgreen;padding:5px;'>Status</td>
<td style='border: 1px solid yellowgreen;padding:5px;'>" . $jobStatus . "</td>
</tr>

<tr>
<td style='border: 1px solid yellowgreen;padding:5px;'>Notes if Any 1000 alphabets</td>
<td colspan='4' style='border: 1px solid yellowgreen;padding:5px;'>" . $I_Comments . "</td>
</tr>
</table>

<p>Kindest Regards </p>

<p>Admin Team</p>

<p>Language Services UK Limited</p>
";
$ack_message='Hi <b>Admin</b>
<p>This is an email acknowledgement for '.$source.' Telephone Job of '.$get_dur.' requested by '.$orgName.' booked on '.$misc->dated($bookedDate).' '.$bookedTime.' for assignment date '.$assignDate.' '.$assignTime.'.</p>
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
        $mail->Subject = 'Acknowledgement for new Telephone Online Portal Job';
        $mail->Body    = $ack_message;
        $mail->send();
        $mail->ClearAllRecipients();
        //Invoice//
        if ($_POST['jobStatus'] == 1) {
            $nmbr = $acttObj->get_id('invoice');if ($nmbr == null) {$nmbr = 0;}
            $new_nmbr = str_pad($nmbr, 5, "0", STR_PAD_LEFT);
            $invoice = date("my").$new_nmbr;
            $maxId = $nmbr;
            $acttObj->editFun('invoice', $maxId, 'invoiceNo', $invoice);
            $acttObj->editFun($table, $edit_id, 'invoiceNo', $invoice);
        }
        //Email notification to related interpreters
        $jobDisp_req=$_POST['jobDisp'];
        $jobStatus_req=$_POST['jobStatus'];
        if($jobDisp_req=='1' && $jobStatus_req=='1'){
            $source_lang_req=$_POST['source'];
            $assignDate_req=$misc->dated($_POST['assignDate']);
            $assignTime_req=$_POST['assignTime']; 
            $assignDur_req=$_POST['assignDur'];
            $gender_req=$_POST['gender'];
            $target_lang_req=$_POST['target'];
            if($telep_cat=='11'){
        $append_issue_bid="<tr><td style='border: 1px solid yellowgreen;padding:5px;'>Assignment Category</td><td style='border: 1px solid yellowgreen;padding:5px;'>Other</td></tr><tr><td style='border: 1px solid yellowgreen;padding:5px;'>Assignment Details</td><td style='border: 1px solid yellowgreen;padding:5px;'>".$assignIssue."</td></tr>";
    }else{
        $append_issue_bid="<tr><td style='border: 1px solid yellowgreen;padding:5px;'>Assignment Category</td><td style='border: 1px solid yellowgreen;padding:5px;'>".$write_telep_cat."</td></tr><tr><td style='border: 1px solid yellowgreen;padding:5px;'>Assignment Details</td><td style='border: 1px solid yellowgreen;padding:5px;'>".$write_telep_type."</td></tr>";
    }
    $append_table="
<table>
<tr>
<td style='border: 1px solid yellowgreen;padding:5px;'>Communication Type</td>
<td style='border: 1px solid yellowgreen;padding:5px;'>".$write_comunic."</td>
</tr>
<tr>
<td style='border: 1px solid yellowgreen;padding:5px;'>Source Language</td>
<td style='border: 1px solid yellowgreen;padding:5px;'>".$source_lang_req."</td>
</tr>
<tr>
<td style='border: 1px solid yellowgreen;padding:5px;'>Target Language</td>
<td style='border: 1px solid yellowgreen;padding:5px;'>".$target_lang_req."</td>
</tr>
<tr>
<td style='border: 1px solid yellowgreen;padding:5px;'>Assignment Date</td>
<td style='border: 1px solid yellowgreen;padding:5px;'>".$assignDate_req."</td>
<tr>
<td style='border: 1px solid yellowgreen;padding:5px;'>Assignment Time</td>
<td style='border: 1px solid yellowgreen;padding:5px;'>".$assignTime_req."</td>
</tr>
<tr>
<td style='border: 1px solid yellowgreen;padding:5px;'>Assignment Duration</td>
<td style='border: 1px solid yellowgreen;padding:5px;'>".$get_dur."</td>
</tr>
".$append_issue_bid."
<tr>
<td style='border: 1px solid yellowgreen;padding:5px;'>Report to</td>
<td style='border: 1px solid yellowgreen;padding:5px;'>".$inchPerson."</td>
</tr>
<tr>
<td style='border: 1px solid yellowgreen;padding:5px;'>Case Worker</td>
<td style='border: 1px solid yellowgreen;padding:5px;'>".$orgContact."</td>
</tr>

</table>";
    if($gender_req=='' || $gender_req=='No Preference'){
        $put_gender="";
    }else{
        $put_gender="AND interpreter_reg.gender='$gender_req'";
    }
    if($source_lang_req== $target_lang_req){
    $put_lang="";$query_style='0';
}else if($source_lang_req!='English' && $target_lang_req!='English'){
    $put_lang="";$query_style='1';
}else if($source_lang_req=='English' && $target_lang_req!='English'){
    $put_lang="interp_lang.lang='$target_lang_req' and interp_lang.level<3";$query_style='2';
}else if($source_lang_req!='English' && $target_lang_req=='English'){
    $put_lang="interp_lang.lang='$source_lang_req' and interp_lang.level<3";$query_style='2';
}else{
    $put_lang="";$query_style='3';
}
if($query_style=='0'){
    $query_emails="SELECT DISTINCT interpreter_reg.name, interpreter_reg.email, interpreter_reg.id FROM interpreter_reg,interp_lang WHERE interpreter_reg.code=interp_lang.code AND (SELECT COUNT(DISTINCT interp_lang.lang) FROM interp_lang WHERE interp_lang.lang IN ('".$source_lang_req."') and interp_lang.level<3 and interp_lang.code=interpreter_reg.code)=1 and 
    interpreter_reg.active='0' AND (interpreter_reg.actnow='Active' OR (interpreter_reg.actnow='Inactive' AND CURRENT_DATE() NOT BETWEEN interpreter_reg.actnow_time AND interpreter_reg.actnow_to)) AND interpreter_reg.telep='Yes' $put_gender AND interpreter_reg.deleted_flag=0 AND interpreter_reg.subscribe=1 AND interpreter_reg.is_temp=0 AND interpreter_reg.on_hold='No'";
}else if($query_style=='1'){
    $query_emails="SELECT DISTINCT interpreter_reg.name, interpreter_reg.email, interpreter_reg.id FROM interpreter_reg,interp_lang WHERE interpreter_reg.code=interp_lang.code AND (SELECT COUNT(DISTINCT interp_lang.lang) FROM interp_lang WHERE interp_lang.lang IN ('".$source_lang_req."','".$target_lang_req."') and interp_lang.level<3 and interp_lang.code=interpreter_reg.code)=2 and 
    interpreter_reg.active='0' AND (interpreter_reg.actnow='Active' OR (interpreter_reg.actnow='Inactive' AND CURRENT_DATE() NOT BETWEEN interpreter_reg.actnow_time AND interpreter_reg.actnow_to)) AND interpreter_reg.telep='Yes' $put_gender AND interpreter_reg.deleted_flag=0 AND interpreter_reg.subscribe=1 AND interpreter_reg.is_temp=0 AND interpreter_reg.on_hold='No'";
}else if($query_style=='2'){
    $query_emails="SELECT DISTINCT interpreter_reg.name, interpreter_reg.email, interpreter_reg.id FROM interpreter_reg,interp_lang WHERE interpreter_reg.code=interp_lang.code AND $put_lang and 
    interpreter_reg.active='0' AND (interpreter_reg.actnow='Active' OR (interpreter_reg.actnow='Inactive' AND CURRENT_DATE() NOT BETWEEN interpreter_reg.actnow_time AND interpreter_reg.actnow_to)) AND interpreter_reg.telep='Yes' $put_gender AND interpreter_reg.deleted_flag=0 AND interpreter_reg.subscribe=1 AND interpreter_reg.is_temp=0 AND interpreter_reg.on_hold='No'";
}else{
    $query_emails="SELECT DISTINCT interpreter_reg.name, interpreter_reg.email, interpreter_reg.id FROM interpreter_reg WHERE 
    interpreter_reg.active='0' AND (interpreter_reg.actnow='Active' OR (interpreter_reg.actnow='Inactive' AND CURRENT_DATE() NOT BETWEEN interpreter_reg.actnow_time AND interpreter_reg.actnow_to)) AND interpreter_reg.telep='Yes' $put_gender AND interpreter_reg.deleted_flag=0 AND interpreter_reg.subscribe=1 AND interpreter_reg.is_temp=0 AND interpreter_reg.on_hold='No'";
}
            $res_emails=mysqli_query($con,$query_emails);
    //Getting bidding email from em_format table
    $row_format=$acttObj->read_specific("em_format","email_format","id=29");
    $subject_int = "Bidding Invitation For Telephone Interpreting Project ".$edit_id;
    $sub_title = "New Telephone Interpreting job of ".$source_lang_req." language is available for you to bid.";
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
        $data   = ["[NAME]", "[ASSIGNTIME]", "[ASSIGNDATE]", "[TABLE]", "[EDIT_ID]"];
          $to_replace  = [$row_emails['name'], "$assignTime_req", "$assignDate_req", "$append_table", "$edit_id"];
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
        //job alert ends here
        echo "<script>alert('Thanks for booking with LSUK Limited. You have successfully submitted the form. You will shortly receive a confirmation email of the request. Please check your email. Any problem please get in touch with LSUK Booking Team on 01173290610.');</script>";
    }else{
        echo "<script>alert('OOps..Email not submited!');</script>";
    }
} catch (Exception $e) {?>
<script>alert("Message could not be sent! Mailer library error.");</script>
<?php }
        $acttObj->editFun($table, $edit_id, 'bookedVia', 'Online Portal');
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
function get_telep_type(elem){
    var tpc_id=elem.val();
    $.ajax({
        url:'ajax_client_portal.php',
        method:'post',
        data:{tpc_id:tpc_id},
        success:function(data){
            if(data){
                $('#div_tpt').css('display','block');
                $('#div_assignIssue').css('display','none');
                $('#assignIssue').css('display','none');
                $('#div_tpt').html(data);
            }else{
                $('#div_tpt').html(data);
                $('#div_tpt').css('display','none');
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
function av(elem){
    var val=$(elem).val();
    var telep_checker='1';
    if($(elem).prop('checked')){
        $.ajax({
        url:'ajax_client_portal.php',
        method:'post',
        data:{val:val,telep_checker:telep_checker},
        success:function(data){
            if(data){
                $('#div_comunic').html(data);
            }
    }, error: function(xhr){
        alert("An error occured: " + xhr.status + " " + xhr.statusText);
    }
    });
    }
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