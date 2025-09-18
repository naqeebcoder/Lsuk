<?php
//php mailer library
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
require 'phpmailer/vendor/autoload.php';
$mail = new PHPMailer(true);

include "userhaspage.php";
SysPermiss::UserHasPage(__FILE__);
if(session_id() == '' || !isset($_SESSION)){
	session_start();
} ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
    <title>Send Custom Email</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" />
    <style>.multiselect {min-width: 350px;}.multiselect-container {max-height: 400px;overflow-y: auto;max-width: 380px;}</style>
</head>
<body>  
<?php 
include 'db.php';
include 'class.php';
if(isset($_POST['btn_submit'])){
    $selected_interpreters=implode(",", $_POST['selected_interpreters']);
    $get_interpreters=$acttObj->read_all("name,email","interpreter_reg","id IN (".$selected_interpreters.")");
    //$get_interpreters=$acttObj->read_all("name,email","interpreter_reg","id IN (874,876)");
    $subject=$_POST['subject'];
    $message=$_POST['em_format'];
    $from_email = "info@lsuk.org";
    $from_name='LSUK';$count=0;
    $admin_message="Hello Admin,<br>Email has been sent to below list:<br>";
    while($row_emails=$get_interpreters->fetch_assoc()){
        $count++;
        $admin_message.=$count.". ".$row_emails['name']." : ".$row_emails['email']."<br>";
        try {
            $mail->SMTPDebug = 0;
            //$mail->isSMTP(); 
            //$mailer->Host = 'lsuk-org.mail.protection.outlook.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = $from_email;
            $mail->Password   = 'LangServ786';
            $mail->SMTPSecure = 'tls';
            $mail->Port       = 587;//25 working
            $mail->setFrom($from_email,$from_name);
            $mail->addAddress($row_emails['email']);
            $mail->addReplyTo($from_email,$from_name);
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = $message;
            $mail->send();
            $mail->ClearAllRecipients();
        } catch (Exception $e) {
            echo '<script>alert("Message could not be sent! Mailer library error.");</script>';
        }
    }
    try {
            $mail->SMTPDebug = 0;
            //$mail->isSMTP(); 
            //$mailer->Host = 'lsuk-org.mail.protection.outlook.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = $from_email;
            $mail->Password   = 'LangServ786';
            $mail->SMTPSecure = 'tls';
            $mail->Port       = 587;//25 working
            $mail->setFrom($from_email,$from_name);
            //$mail->addAddress("imran@lsuk.org");
            $mail->addAddress("waqarecp1992@gmail.com");
            $mail->addReplyTo($from_email,$from_name);
            $mail->isHTML(true);
            $mail->Subject = "Email list for custom email";
            $mail->Body    = $admin_message;
            $mail->send();
            $mail->ClearAllRecipients();
        } catch (Exception $e) {
            echo '<script>alert("Message could not be sent! Mailer library error.");</script>';
        }
    echo '<script>alert("Custom email has been sent successfully !");</script>';
    echo '<script>window.location.href="custom_email.php";</script>';
}
include 'nav2.php';
?>
<!-- end of sidebar -->
<section class="container-fluid">
    <div class="row">
        <div class="col-sm-12">
            <center><a href="custom_email.php" style="padding: 12px;" class="alert-link h4 bg-info">Send custom email to interpreters</a></center>
        </div>
    </div><br>
    <div class="col-md-6">
<form action="<?php echo htmlentities($_SERVER['PHP_SELF']);?>" method="post" class="register" enctype="multipart/form-data">
          <div id="div_interpreters" class="form-group col-sm-6">
                <select class="multi_class" id="selected_interpreters" name="selected_interpreters[]"  multiple="multiple">
                    <?php $res_int=$acttObj->read_all("interpreter_reg.id,interpreter_reg.name,interpreter_reg.email","interp_salary,interpreter_reg","interp_salary.interp=interpreter_reg.id and interp_salary.dated='2021-03-01' and interp_salary.deleted_flag=0 ORDER BY interpreter_reg.name ASC");
                    while($row_int=mysqli_fetch_assoc($res_int)){ ?>
                    <option value="<?php echo $row_int['id']; ?>"><?php echo $row_int['name']; ?></option>
                    <?php } ?>
                </select>
            </div>
             <div class="form-group col-sm-12">
			    <input type="text" name="subject" placeholder="Enter title for post" required="required" class="form-control"/>
			</div>
             <div class="form-group col-sm-12">
                <textarea name="em_format" id="mytextarea" cols="51" rows="4" placeholder="Write message details here ..."></textarea>
           </div>
             <div class="form-group col-sm-12">
                <button class="btn btn-primary" type="submit" name="btn_submit" >Send Email &raquo;</button>
                <a class="btn btn-warning" href="custom_email.php">Close <i class="glyphicon glyphicon-remove-circle"></i></a>
           </div>
  </form>
  </div>

</div>
<script src="js/jquery-1.11.3.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.13/css/bootstrap-multiselect.css"rel="stylesheet" type="text/css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.13/js/bootstrap-multiselect.js"type="text/javascript"></script>
<script src="https://cdn.tiny.cloud/1/1cuurlhdv50ndxckpjk52wu6i868lluhxe90y7xesmawusin/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script>
  <script type="text/javascript">
tinymce.init({
  selector: "#mytextarea",
  height:   400,
  plugins: 'print preview   searchreplace autolink autosave save directionality  visualblocks visualchars fullscreen image link media  template codesample table charmap hr pagebreak nonbreaking anchor toc insertdatetime advlist lists  wordcount   imagetools textpattern noneditable help  ',
  toolbar: 'undo redo | link image | code',
  image_title: true,
  automatic_uploads: true,
  file_picker_types: 'image media',
  file_picker_callback: function (cb, value, meta) {
    var input = document.createElement('input');
    input.setAttribute('type', 'file');
    input.setAttribute('accept', 'image/*');
    input.onchange = function () {
    var file = this.files[0];
    var reader = new FileReader();
    reader.onload = function () {
    var id = 'blobid' + (new Date()).getTime();
        var blobCache =  tinymce.activeEditor.editorUpload.blobCache;
        var base64 = reader.result.split(',')[1];
        var blobInfo = blobCache.create(id, file, base64);
        blobCache.add(blobInfo);
        cb(blobInfo.blobUri(), { title: file.name });
      };
      reader.readAsDataURL(file);
    };
    input.click();
  }
});
$(function() {
	    $('.multi_class').multiselect({includeSelectAllOption: true,numberDisplayed: 1,enableFiltering: true,enableCaseInsensitiveFiltering: true});
    });
</script>
</body>
</html>