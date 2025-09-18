<?php if(session_id() == '' || !isset($_SESSION)){session_start();} ?> 
<?php include'source/db.php'; include'source/class.php'; if(isset($_POST['submit'])){$table='staff_job_applicants';$edit_id= $acttObj->get_id($table);}?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
        <title>Sign Up Form</title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <link rel="stylesheet" type="text/css" href="css/default.css"/>   
	<meta name="viewport" content="width=device-width, initial-scale=1.0">	

</head>
<body>
<form action="" method="post" class="register" id="signup_form" name="signup_form" enctype="multipart/form-data">
  <h1>Job Application</h1>
  <fieldset class="row1">
    <legend>Please Proceed your Job Application for the Post of  <?php echo 'Translation / Transcription';?></legend>
    <p>
      <label>Full Name * </label>
      <input name="name" type="text" placeholder='' required='' id="name"/>
      <label>Picture * </label>
      <input name="pic" type="file" id="pic"  required=''/>
   
      <?php if(isset($_POST['submit'])){$data=$_POST['name']; $acttObj->editFun($table,$edit_id,'name',$data);} ?>
<?php if(isset($_POST['submit'])){error_reporting(0); $picName=$acttObj->upload_file('face_pic',$_FILES["pic"]["name"],$_FILES["pic"]["type"],$_FILES["pic"]["tmp_name"],$edit_id); $acttObj->editFun($table,$edit_id,'pic',$picName);} ?> 
      
    </p>
    <p>
      <label>Contact No. * </label>
      <input name="contact" type="text" placeholder='' required='' id="contact"/>
      <label>Email * </label>
      <input name="email" type="text" placeholder='' required='' id="email"/>    
      <?php if(isset($_POST['submit'])){$data=$_POST['contact']; $acttObj->editFun($table,$edit_id,'contact',$data);} ?>   
      <?php if(isset($_POST['submit'])){$data=$_POST['email']; $acttObj->editFun($table,$edit_id,'email',$data);} ?>
      
    </p> 
        </fieldset><fieldset class="row3">
      <legend>
                </legend><p>
         <label>CV * </label>
         <input name="cv" type="file" id="cv" required=''/>
<?php if(isset($_POST['submit'])){error_reporting(0); $picName=$acttObj->upload_file('cv',$_FILES["cv"]["name"],$_FILES["cv"]["type"],$_FILES["cv"]["tmp_name"],$edit_id); $acttObj->editFun($table,$edit_id,'cv',$picName);} ?> 
       </p>
<div class="infobox">Address
  <p>
                    <textarea name="location" cols="51" rows="5" id="location"></textarea>
                          <?php if(isset($_POST['submit'])){$data=$_POST['location']; $acttObj->editFun($table,$edit_id,'location',$data); $acttObj->editFun($table,$edit_id,'title',$_GET['tracking']);} ?>
        </p>
      </div>
            </fieldset>
    <div>
   
    <button class="button" type="submit" name="submit" onclick="return formSubmit(); return false">Proceed &raquo;</button>
</div>
 
  
</form>
</body>
</html>
<?php if(isset($_POST['submit'])  && $dbemail==0){echo "<script>alert('Successful!');</script>";}?>
<script>window.onunload = refreshParent;function refreshParent() {window.opener.location.reload();}</script>