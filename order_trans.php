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
<link rel="stylesheet" href="http://netdna.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">
<link href="new_theme/css/bootstrap.min.css" rel="stylesheet">
        <title>Place Order</title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <link rel="stylesheet" type="text/css" href="css/default.css"/>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
  <script src="source/jquery-2.1.3.min.js"></script> 
  <script type="text/javascript" src="new_theme/js/bootstrap.min.js"></script>
</head>
<body>
<form action="" method="post" class="register" enctype="multipart/form-data">
  <h1>Place an Order for Document Translation</h1>
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
      <?php if(isset($_POST['submit'])){$source=$_POST['source']; } ?>
      <?php if(isset($_POST['submit'])){$target=$_POST['target'];} ?>
    </p>
    <p>
      <label>Document Type * </label>
      <select name="docType">
        <option value="Nil">--Select--</option>
        <option>General Document</option>
        <option>Legal Document(Court Summons etc)</option>
        <option>Technical(Manual)</option>
        <option>Medial</option>
        <option>Financial</option>
        <option>Transcription</option>
      </select>
      <label>Translation Type </label>
      <select name="transType" id="transType">
        <option value="Nil">--Select--</option>
        <option>Translation</option>
        <option>Translation + Proof-Reading</option>
        <option>Proof-Reading</option>
        <option>Certified Translation</option>
        <option>Audio Transcription</option>
        <option>Video Transcription</option>
      </select>
      <?php if(isset($_POST['submit'])){$docType=$_POST['docType'];} ?>
      <?php if(isset($_POST['submit'])){$transType=$_POST['transType'];} ?>
    </p>
    <p>
      <label>Assignment Date </label>
      <input name="asignDate" type="date" style="border:1px solid #CCC" value=""  />
      <label>Booking Ref * </label>
      <input name="nameRef" type="text" required='' readonly="readonly" value=""/>
      <!--
                  <label class="obinfo">* obligatory fields-->
      </label>
      <?php if(isset($_POST['submit'])){$nameRef=$_POST['nameRef'];} ?>
      <?php if(isset($_POST['submit'])){$asignDate=$_POST['asignDate'];} ?>
    </p>
  </fieldset>
  <fieldset class="row1">
    <legend>More Information </legend>
    <p>
      <label class="optional">Delivery Type </label>
      <select name="deliveryType" id="deliveryType">
        <option value="Nil">--Select--</option>
        <option>Standard Service (1 -2 Weeks)</option>
        <option>Quick Service (2-3 Days)</option>
        <option>Emergency Service (1-2 Days)</option>
      </select>
      <label class="optional">Delivery Date </label>
      <input name="deliverDate" type="date" style="border:1px solid #CCC" value=""  />
      <?php if(isset($_POST['submit'])){$deliveryType=$_POST['deliveryType'];} ?>
      <?php if(isset($_POST['submit'])){$deliverDate=$_POST['deliverDate'];} ?>
    </p>
  </fieldset>
  <fieldset class="row2">
    <legend>Contact Details </legend>
    <p>
      <label class="optional">Contact Number&nbsp;</label>
      <input name="inchContact" id="inchContact" type="text" class="long"/>
      <?php if(isset($_POST['submit'])){$inchContact=$_POST['inchContact'];} ?>
    </p>
    <p>
      <label class="optional"> Email Address&nbsp;</label>
      <input name="inchEmail" id="inchEmail" type="email" class="long" style="border:1px solid #CCC" placeholder='' required />
      <?php if(isset($_POST['submit'])){$inchEmail=$_POST['inchEmail'];} ?>
    </p>
  </fieldset>
  <fieldset class="row3">
    <legend> Organisation Details </legend>
    <p>
      <label> Company Name* </label>
      <input type="text" id="orgName" name="orgName" pattern="[a-zA-Z][a-zA-Z ]{3,30}" required=''/>
      <?php if(isset($_POST['submit'])){$orgName=$_POST['orgName'];} ?>
    </p>
    <p>
      <label class="optional">Booking Ref/Name&nbsp;&nbsp;</label>
      <input name="orgRef" type="text" placeholder='' />
      <?php if(isset($_POST['submit'])){$orgRef=$_POST['orgRef'];} ?>
    </p>
    <p>
      <label>Contact Name&nbsp;* </label>
      <input name="orgContact" id="orgContact" type="text" pattern="[a-zA-Z][a-zA-Z ]{3,30}" placeholder='' required=''/>
      <?php if(isset($_POST['submit'])){$orgContact=$_POST['orgContact'];} ?>
    </p>
    <div class="infobox">
      <h4>Notes if Any 1000 alphabets</h4>
      <p>
        <textarea name="remrks" cols="51" rows="5"></textarea>
        <?php if(isset($_POST['submit'])){$remrks=$_POST['remrks'];} ?>
      </p>
    </div>
  </fieldset>
  <fieldset class="row4">
    <legend> Invoice </legend>
    <p>
      <label class="optional">Booking Type </label>
      <select id="bookinType" name="bookinType">
        <?php 			
$sql_opt="SELECT * FROM booking_type where type='Translation' ORDER BY title ASC";
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
      <?php if(isset($_POST['submit'])){$bookinType=$_POST['bookinType'];} ?>
    </p>
    <p>
      <label class="optional">Status</label>
      <input name="jobStatus" type="radio" value="0" required="required"/>
      <label class="gender">Enquiry</label>
      <input type="radio" name="jobStatus" value="1"/>
      <label class="gender">Confirmed </label>
      <?php if(isset($_POST['submit'])){$jobStatus=$_POST['jobStatus'];} ?>
    </p>
  </fieldset>
  <div>
    <button class="button" type="submit" name="submit" style="margin-left:450px;">Submit &raquo;</button>
  </div>
</form>
</body>
</html>
<?php
if(isset($_POST['submit'])){	
	$from_add = $inchEmail;
	$to_add = "info@lsuk.org"; //<-- put your yahoo/gmail email address here
	$subject = "Order for Translation";
	$message ="<caption align='center'>Order for Translation</caption>".
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
<td>Documnet Type</td>
<td>".$docType."</td>
<td>Translation Type</td>
<td>".$transType."</td>
</tr>

<tr>
<td>Assignment Date</td>
<td>".$asignDate."</td>
<td>Name Ref</td>
<td>".$nameRef."</td>
</tr>
<tr>
<td colspan='4' align='center'>More Information</td>
</tr>
<tr>
<td>Develivery Type</td>
<td>".$deliveryType."</td>
<td>Delivery Date</td>
<td>".$deliverDate."</td>
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
<td></td>
<td></td>
</tr>
<tr>
<td colspan='4' align='center'>Contact Details</td>
</tr>
<tr>
<td>Contact Number</td>
<td>".$inchContact."</td>
<td>Email</td>
<td>".$inchEmail."</td>
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


