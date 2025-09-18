<?php 
//php mailer library
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
require 'lsuk_system/phpmailer/vendor/autoload.php';
$mail = new PHPMailer(true);

include'source/db.php'; include'source/class.php';?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
        <title>Place Order</title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <link rel="stylesheet" type="text/css" href="css/default.css"/>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">

</head>
<body>
<form action="" method="post" class="register">
  <h1>Edit Interpreter Booking Form</h1>
  <fieldset class="row1">
    <legend>Work Details </legend>
    <p>
      <label>Source  Language * </label>
      <select name="source" id="source" required=''>
        <?php 			
$sql_opt="SELECT lang FROM lang ORDER BY lang ASC";
$result_opt=mysqli_query($con,$sql_opt);
$options="";
while ($row_opt=mysqli_fetch_array($result_opt)) {
    $code=$row_opt["lang"];
    $name_opt=$row_opt["lang"];
    $options.="<OPTION value='$code'>".$name_opt;}
?>
        <option>--Select--</option>
        <?php echo $options; ?>
        </option>
      </select>
      <label>Target  Language * </label>
      <select name="target" id="target" required=''>
        <?php 			
$sql_opt="SELECT lang FROM lang ORDER BY lang ASC";
$result_opt=mysqli_query($con,$sql_opt);
$options="";
while ($row_opt=mysqli_fetch_array($result_opt)) {
    $code=$row_opt["lang"];
    $name_opt=$row_opt["lang"];
    $options.="<OPTION value='$code'>".$name_opt;}
?>
        <option>English</option>
        <option>--Select--</option>
        <?php echo $options; ?>
        </option>
      </select>
      <?php if(isset($_POST['submit'])){$source=@$_POST['source'];} ?>
      <?php if(isset($_POST['submit'])){$target=@$_POST['target'];} ?>
    </p>
    <p>
      <label>Assignment  Date * </label>
      <input type="date" name="assignDate" required='' style="border:1px solid #CCC" value=''/>
      <label>Assignment  Time * </label>
      <input name="assignTime" type="time" style="border:1px solid #CCC" value="" required='' />
      <?php if(isset($_POST['submit'])){$assignDate=@$_POST['assignDate'];} ?>
      <?php if(isset($_POST['submit'])){$assignTime=@$_POST['assignTime'];} ?>
    </p>
    <p>
      <label>Assignment  Duration * </label>
      <input name="assignDur" type="number"  pattern="[0-9]+([\.|,][0-9]+)?" step="0.01" style="border:1px solid #CCC" value="" required='' />
      <label>Booking Ref * </label>
      <input name="nameRef" type="text"  required=''readonly="readonly" value=""/>
      <!--
                  <label class="obinfo">* obligatory fields-->
      </label>
      <?php if(isset($_POST['submit'])){$assignDur=@$_POST['assignDur'];} ?>
      <?php if(isset($_POST['submit'])){$nameRef=@$_POST['nameRef'];} ?>
    </p>
    <p>
      <label>Assignment  Issue </label>
      <input name="assignIssue" type="text" style="border:1px solid #CCC" value="" id="assignIssue" />
      <!--
                  <label class="obinfo">* obligatory fields-->
      </label>
      <?php if(isset($_POST['submit'])){$assignIssue=@$_POST['assignIssue'];} ?>
    </p>
  </fieldset>
  <fieldset class="row1">
    <legend>Assignment Location </legend>
    <p>
      <label class="optional">Number of the Client to be Called </label>
      <input name="noClient" type="text" id="noClient" value=""/>
      <label class="optional">Contact No for Ph. Interpreting </label>
      <input name="contactNo" type="text" id="contactNo" value=""/>
      <?php if(isset($_POST['submit'])){$noClient=@$_POST['noClient'];} ?>
      <?php if(isset($_POST['submit'])){$contactNo=@$_POST['contactNo'];} ?>
    </p>
  </fieldset>
  <fieldset class="row2">
    <legend>Assignment in-Charge </legend>
    <p>
      <label class="optional"> Booking Person Name if Different </label>
      <input name="inchPerson" type="text" class="long" value="" pattern="[a-zA-Z][a-zA-Z ]{2,30}" />
      <?php if(isset($_POST['submit'])){$inchPerson=@$_POST['inchPerson'];} ?>
    </p>
    <p>
      <label class="optional">Contact Number&nbsp;</label>
      <input name="inchContact" id="inchContact" type="text" class="long" value=""/>
      <?php if(isset($_POST['submit'])){$inchContact=@$_POST['inchContact'];} ?>
    </p>
    <p>
      <label class="optional"> Email Address&nbsp;</label>
      <input name="inchEmail" id="inchEmail" type="email" class="long" style="border:1px solid #CCC" value="" placeholder='' />
      <?php if(isset($_POST['submit'])){$inchEmail=@$_POST['inchEmail'];} ?>
    </p>
    <p>
      <label class="optional">Building Number / Name </label>
      <input name="inchNo" id="inchNo" type="text" value="" placeholder='' />
      <?php if(isset($_POST['submit'])){$inchNo=@$_POST['inchNo'];} ?>
    </p>
    <p>
      <label class="optional">Address Line 1 </label>
      <input name="line1" id="line1" type="text" placeholder='' />
      <?php if(isset($_POST['submit'])){$line1=@$_POST['line1'];} ?>
    </p>
    <p>
      <label class="optional">Street / Road </label>
      <input name="inchRoad" id="inchRoad" type="text" value="" placeholder='' />
      <?php if(isset($_POST['submit'])){$inchRoad=@$_POST['inchRoad'];} ?>
    </p>
    <p>
      <label class="optional">City </label>
      <select name="inchCity" id="inchCity">
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
      <?php if(isset($_POST['submit'])){$inchCity=@$_POST['inchCity'];} ?>
    </p>
    <p>
      <label class="optional">Post Code </label>
      <input name="inchPcode" id="inchPcode" type="text" value="" />
      <?php if(isset($_POST['submit'])){$inchPcode=@$_POST['inchPcode'];} ?>
    </p>
  </fieldset>
  <fieldset class="row3">
    <legend>Booking Organisation Details </legend>
    <p>
      <label> Company Name* </label>
      <input type="text" id="orgName" name="orgName" pattern="[a-zA-Z][a-zA-Z ]{3,30}" required=''/>
      <?php if(isset($_POST['submit'])){$orgName=@$_POST['orgName'];} ?>
    </p>
    <p>
      <label class="optional">Booking Ref/Name </label>
      <input name="orgRef" type="text" value="" placeholder='' />
      <?php if(isset($_POST['submit'])){$orgRef=@$_POST['orgRef'];} ?>
    </p>
    <p>
      <label>Contact Name&nbsp;* </label>
      <input name="orgContact" id="orgContact" type="text" value="" pattern="[a-zA-Z][a-zA-Z ]{2,30}" placeholder='' required=''/>
      <?php if(isset($_POST['submit'])){$orgContact=@$_POST['orgContact'];} ?>
    </p>
    <div class="infobox">
      <h4>Notes if Any 1000 alphabets</h4>
      <p>
        <textarea name="remrks" cols="51" rows="5"></textarea>
        <?php if(isset($_POST['submit'])){$remrks=@$_POST['remrks'];} ?>
      </p>
    </div>
  </fieldset>
  <fieldset class="row4">
    <legend>Interpreter </legend>
    <p>
      <label class="optional">Booking Type </label>
      <select id="bookinType" name="bookinType">
        <?php 			
$sql_opt="SELECT * FROM booking_type where type='Telephone' ORDER BY title ASC";
$result_opt=mysqli_query($con,$sql_opt);
$options="";
while ($row_opt=mysqli_fetch_array($result_opt)) {
    $rate=$row_opt["rate"];
    $name_opt=$row_opt["title"];
    $options.="<OPTION value='$name_opt'>".$name_opt;}
?>
        <option value="0">--Select--</option>
        <?php echo $options; ?>
        </option>
      </select>
      <?php if(isset($_POST['submit'])){$bookinType=@$_POST['bookinType'];} ?>
    </p>
    <p>
      <label class="optional">Gender</label>
      <input name="gender" type="radio" value="Male" required="required"/>
      <label class="gender">Male</label>
      <input type="radio" name="gender" value="Female"/>
      <label class="gender">Female</label>
      <?php if(isset($_POST['submit'])){$gender=@$_POST['gender'];} ?>
    </p>
    <p>
      <label class="optional">Status</label>
      <input name="jobStatus" type="radio" value="0" required="required"/>
      <label class="gender">Enquiry</label>
      <input type="radio" name="jobStatus" value="1"/>
      <label class="gender">Confirmed </label>
      <?php if(isset($_POST['submit'])){$jobStatus=@$_POST['jobStatus'];} ?>
    </p>
  </fieldset>
  <div>
    <button class="button" type="submit" name="submit">Submit &raquo;</button>
  </div>
</form>
</body>
</html>
<?php
if(isset($_POST['submit'])){	
	$from_add = $inchEmail; 
	$to_add = "sabihkhanafridi@outlook.com"; //<-- put your yahoo/gmail email address here
	$subject = "Order for Telephone Interpreting";
	echo $message ="<caption align='center'>Order for Telephone Interpreting</caption>".
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
<td>Source Language</td>
<td>".$source."</td>
<td>Target Language</td>
<td>".$target."</td>
</tr>

<tr>
<td>Assignment Date</td>
<td>".$assignDate."</td>
<td>Assignment Time</td>
<td>".$assignTime."</td>
</tr>

<tr>
<td>Assignment Duration</td>
<td>".$assignDur."</td>
<td>Assignment Issue</td>
<td>".$assignIssue."</td>
</tr>
<tr>
<td colspan='4' align='center'>Assignment Location</td>
</tr>
<tr>
<td>Number of the Client to be Called</td>
<td>".$noClient."</td>
<td>Contact No for Ph. Interpreting</td>
<td>".$contactNo."</td>
</tr>
<tr>
<td colspan='4' align='center'>Booking Organisation Details</td>
</tr>
<tr>
<td>Company Name</td>
<td>".$orgName."</td>
<td>Booking Ref/Name</td>
<td>".$orgRef."</td>
</tr>
<tr>
<td>Contact Name</td>
<td>".$orgContact."</td>
<td>&nbsp;</td>
<td>&nbsp;</td>
</tr>
<tr>
<tr>
<td colspan='4' align='center'>Assignment in-Charge</td>
</tr>
<tr>
<td>Booking Person Name if Different</td>
<td>".$inchPerson."</td>
<td>Contact Number</td>
<td>".$inchContact."</td>
</tr>
<tr>
<td>Email Address</td>
<td>".$inchEmail."</td>
<td>Building Number / Name</td>
<td>".$inchNo."</td>
</tr>
<tr>
<td>Address Line 1</td>
<td>".$line1."</td>
<td>Address Line 2</td>
<td>".$inchRoad."</td>
</tr>
<tr>
<td>City</td>
<td>".$inchCity."</td>
<td>Post Code</td>
<td>".$inchPcode."</td>
</tr>
<tr>
<td colspan='4' align='center'>Interpreter</td>
</tr>
<tr>
<td>Gender</td>
<td>".$gender."</td>
<td>Status</td>
<td>".$jobStatus."</td>
</tr>

<tr>
<td>Notes if Any 1000 alphabets</td>
<td colspan='4' align='center'>".$remrks."</td>
</tr>
</table>";
try {
    $mail->SMTPDebug = 0;
    //$mail->isSMTP(); 
    //$mailer->Host = 'smtp.office365.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'info@lsuk.org';
    $mail->Password   = 'LangServ786';
    $mail->SMTPSecure = 'tls';
    $mail->Port       = 587;
    $mail->setFrom('info@lsuk.org', 'LSUK');
    $mail->addAddress($to_add);
    $mail->addReplyTo($from_add);
    $mail->isHTML(true);
    $mail->Subject = $subject;
    $mail->Body    = $message;
    if($mail->send()){
    $mail->ClearAllRecipients(); ?>
<script>alert("Email submited to client!");</script>
<?php }else{?>
<script>alert("Email not submited to client!");</script>
<?php }
} catch (Exception $e) { ?>
<script>alert("Mailer library error!");</script>
<?php }
?>
<?php } ?>


