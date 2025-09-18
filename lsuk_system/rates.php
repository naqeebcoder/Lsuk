<?php include "userhaspage.php";
SysPermiss::UserHasPage(__FILE__);
if(session_id() == '' || !isset($_SESSION)){session_start();}
include 'db.php';
include 'class.php';
$table='booking_type';
if(isset($_POST['submit'])){$edit_id= $acttObj->get_id($table);}?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
        <title>Sign Up Form</title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <link rel="stylesheet" type="text/css" href="css/default.css"/>    
<?php include'ajax_uniq_fun.php'; ?>
	      <script type="text/javascript">
function MM_openBrWindow(theURL,winName,features) { //v2.0
  window.open(theURL,winName,features);}</script>

</head>
<body>
<form action="" method="post" class="register" id="signup_form" name="signup_form" onsubmit="return formSubmit()">
  <h1>Booking Rates Information</h1>
  <fieldset class="row1">
    <legend>Rates Details </legend>
    <p>
      <label>Title * </label>
      <input name="title" type="text" placeholder='' required='' id="title"/>
      <label>Rate &pound;* </label>
      <input name="rate" type="text" placeholder='' required='' id="rate" pattern="[0-9]+([\.|,][0-9]+)?" step="0.01" />
      <?php if(isset($_POST['submit'])){$data=$_POST['title']; $acttObj->editFun($table,$edit_id,'title',$data);} ?>
      <?php if(isset($_POST['submit'])){$data=$_POST['rate']; $acttObj->editFun($table,$edit_id,'rate',$data);} ?>
    </p>
        <p>
      <label>Type * </label>
      <select name="type" id="type" required>
      <option>Interpreter</option>
      <option>Telephone</option>
      <option>Translation</option>
      
      </select>
      <?php if(isset($_POST['submit'])){$data=$_POST['type']; $acttObj->editFun($table,$edit_id,'type',$data);} ?>
    </p>
    <div>
    <button class="button" type="submit" name="submit" style="margin-left:450px;" onclick="return formSubmit(); return false">Submit &raquo;</button>
  </div>
  </fieldset>
  <fieldset class="row1">
     <legend>Types and rates
     </legend>
            
     <table width="60%" border="1">
      <?php 
	   $query="SELECT * FROM $table order by type";			
			$result = mysqli_query($con,$query);
			while($row = mysqli_fetch_array($result)){?>
  <tr>
    <td align="left"><?php echo $row['title']; ?> </td>
    <td align="left"><?php echo $row['type']; ?> </td>
    <td align="left"><?php echo  number_format($row['rate'],2); ?></td>
    <td align="left" width="50"> 
       <a href="#" onClick="MM_openBrWindow('rates_edit.php?edit_id=<?php echo $row['id']; ?>&table=<?php echo $table; ?>','_blank','scrollbars=yes,resizable=yes,width=600,height=500')"><img src="images/icn_edit.png" title="Trash" height="14" width="16" /></a>
       
    
    <a href="#" onClick="MM_openBrWindow('del.php?del_id=<?php echo $row['id']; ?>&table=<?php echo $table; ?>','_blank','scrollbars=yes,resizable=yes,width=400,height=200')"><img src="images/icn_trash.png" title="Trash" height="14" width="16" /></a>
    
    </td>
    </tr>
    <?php } ?>
 </table>
           
     </fieldset>
  
</form>
</body>
</html>
<?php if(isset($_POST['submit'])  && $dbemail==0){
echo "<script>alert('Successful!');</script>";?>
<script>window.onunload = refreshParent;
function refreshParent() {window.opener.location.reload();}</script>
<?php } ?>