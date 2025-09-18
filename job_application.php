<?php if(session_id() == '' || !isset($_SESSION)){session_start();} ?> 
<?php include'source/db.php'; include'source/class.php'; if(isset($_POST['submit'])){$table=@$_GET['table'];$edit_id= $acttObj->get_id('bid');}?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
        <title>Sign Up Form</title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <link rel="stylesheet" type="text/css" href="css/default.css"/>   
	<meta name="viewport" content="width=device-width, initial-scale=1.0">	

</head>
<body>
<form action="" method="post" class="register" id="signup_form" name="signup_form" onsubmit="return formSubmit()">
  <h1>Job Bid</h1>
  <fieldset class="row1">
    <legend>Please Proceed your Bid for <?php if(@$_GET['table']=='interpreter'){echo 'Face to Face'; } if(@$_GET['table']=='telephone'){echo 'Voice Over'; }if(@$_GET['table']=='translation'){echo 'Translation / Transcription'; }?></legend>
    <p>
      <label>Full Name * </label>
      <input name="interp" type="text" placeholder='' required='' id="interp"/>
      <label>Amount &pound; * </label>
      <input name="amount" type="text" placeholder='' required='' id="amount" pattern="[0-9]+([\.|,][0-9]+)?" step="0.01"/>    
      <?php if(isset($_POST['submit'])){$data=$_POST['interp']; $acttObj->editFun('bid',$edit_id,'interp',$data);} ?>   
      <?php if(isset($_POST['submit'])){$data=$_POST['amount']; $acttObj->editFun('bid',$edit_id,'amount',$data);} ?>
      
    </p>
    <p>
      <label>Contact No. * </label>
      <input name="contact" type="text" placeholder='' required='' id="contact"/>
      <label>Email * </label>
      <input name="email" type="text" placeholder='' required='' id="email"/>    
      <?php if(isset($_POST['submit'])){$data=$_POST['contact']; $acttObj->editFun('bid',$edit_id,'contact',$data);} ?>   
      <?php if(isset($_POST['submit'])){$data=$_POST['email']; $acttObj->editFun('bid',$edit_id,'email',$data);} ?>
      
    </p> 
        </fieldset><fieldset class="row3">
      <legend>
                </legend>
      <div class="infobox">Location
        <p>
                    <textarea name="location" cols="51" rows="5" id="location"></textarea>
 <?php if(isset($_POST['submit'])){$data=$_POST['location']; $acttObj->editFun('bid',$edit_id,'location',$data); $acttObj->editFun('bid',$edit_id,'job',$_GET['tracking']);$acttObj->editFun('bid',$edit_id,'tabName',$_GET['table']);} ?>
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