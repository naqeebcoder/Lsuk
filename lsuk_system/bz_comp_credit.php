<?php
include "userhaspage.php";
SysPermiss::UserHasPage(__FILE__);
?>
<?php 
include'db.php';

if(session_id() == '' || !isset($_SESSION))
{
	session_start();
} 

include'class.php';

$table='bz_credit'; 

if(isset($_POST['submit']))
{
  $edit_id= $acttObj->get_id($table);
}
?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
        <title>Sign Up Form</title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <link rel="stylesheet" type="text/css" href="css/default.css"/>    
<?php include'ajax_uniq_fun.php'; ?>
</head>
<body>
<form action="" method="post" class="register" id="signup_form" name="signup_form" onsubmit="return formSubmit()">
  <h1>Credit Information</h1>
  <fieldset class="row1">
    <legend>Credit Details </legend>
    <p>
      <label>Company * </label>
      <select id="orgName" name="orgName">
                    <?php 			
$sql_opt=
"SELECT name,abrv FROM comp_reg ORDER BY name ASC";
$result_opt=mysqli_query($con,$sql_opt);
$options="";
while ($row_opt=mysqli_fetch_array($result_opt)) {
    $code=$row_opt["abrv"];
    $name_opt=$row_opt["name"];
    $options.="<OPTION value='$code'>".$name_opt;}
?>
                    <option value="0">--Select--</option>
                    <?php echo $options; ?>
                    </option>
      </select>
      <label>Credit &pound;* </label>
      <input name="bz_credit" type="text" title='Must be a number' placeholder='' required='' id="bz_credit" 
        pattern="[0-9]+([\.|,][0-9]+)?" step="0.01" />
      <?php if(isset($_POST['submit'])){$data=$_POST['orgName']; $acttObj->editFun($table,$edit_id,'orgName',$data);} ?>
      <?php if(isset($_POST['submit'])){$data=$_POST['bz_credit']; $acttObj->editFun($table,$edit_id,'bz_credit',$data);} ?>
    </p>
     <p> <label><strong><em>Credit Id.</em></strong></label>
                  <input name="creditId" type="text" id="creditId"  required='' readonly="readonly" 
                  value="<?php $month=date('M');$month=substr($month,0,3); $lastid=$acttObj->max_id($table) + 1; echo 'LSUK/'.$month.'/'.$lastid; ?>"/>
            <?php if(isset($_POST['submit'])){$data=$_POST['creditId'];$acttObj->editFun($table,$edit_id,'creditId',$data);} ?></p>
            <div class="infobox"><h4>Notes if Any 1000 alphabets</h4>
            <p> <textarea name="comments" cols="51" rows="5"></textarea>
            <?php if(isset($_POST['submit'])){$data=$_POST['comments'];$acttObj->editFun($table,$edit_id,'comments',$data);} ?>
                  </p>
    <div></div></div>
      <button class="button" type="submit" name="submit" style="margin-left:450px;" onclick="return formSubmit(); return false">Submit &raquo;</button>
</div>
  </fieldset>
</form>
</body>
</html>

<?php 
if(isset($_POST['submit']))
{
  $acttObj->editFun($table,$edit_id,'mode','Credit');
  $acttObj->editFun($table,$edit_id,'bz_credit_date',date("Y-m-d"));

  echo "<script>alert('Successful!');</script>";

  $acttObj->editFun($table,$edit_id,'edited_by',$_SESSION['UserName']);
  $acttObj->editFun($table,$edit_id,'edited_date',date("Y-m-d H:i:s"));
  $acttObj->new_old_table('hist_'.$table,$table,$edit_id);?>
<script>window.onunload = refreshParent;function refreshParent() {window.opener.location.reload();}</script>
<?php } ?>