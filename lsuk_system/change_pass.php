<?php
include "userhaspage.php";
SysPermiss::UserHasPage(__FILE__);
?>
<?php 
if(session_id() == '' || !isset($_SESSION))
{
	session_start();
} 
include'db.php'; 
include'class.php'; $table='login';$edit_id= $_SESSION['userId'];?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
        <title>Change Password</title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <link rel="stylesheet" type="text/css" href="css/default.css"/>
    
	

    </head>
<body>
<form action="" method="post" class="register" id="signup_form" name="signup_form" onsubmit="return formSubmit()">
  <h1>Change Password</h1>
  <fieldset class="row1">
    <legend>Edit Password </legend>
    <p>
     
      <label>Password * </label>
      <input name="pass" type="password" placeholder='' required='' id="pass" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{6,}"  onchange="form.repass.pattern = this.value;"/>
      
    <label>Confirm Password * </label>
      <input name="repass" type="password" placeholder='' required='' id="repass" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{6,}" />
       <?php if(isset($_POST['submit'])){$c1=$_POST['pass']; $acttObj->editFun($table,$edit_id,'pass',$c1);} ?>
     
     
    </p>
  </fieldset>
  <div>
    <button class="button" type="submit" name="submit" style="margin-left:450px;" onclick="return formSubmit(); return false">Submit &raquo;</button>
  </div>
</form>
</body>
</html>
<?php if(isset($_POST['submit'])){echo "<script>alert('Successful!');</script>";}?>
<script> window.onunload = refreshParent; function refreshParent() {window.opener.location.reload();}</script>


