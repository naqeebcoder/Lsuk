<?php
include "userhaspage.php";
SysPermiss::UserHasPage(__FILE__);
?>
<?php include'db.php'; include'class.php';$table='interp_skill';$name=$_GET['name']; $reqcode=$_GET['code'];if(isset($_POST['submit'])){$edit_id= $acttObj->get_id($table);}?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
        <title>Order Form</title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <link rel="stylesheet" type="text/css" href="css/default.css"/>
    </head>
<body>    
<form action="" method="post" class="register">
  <h1>Update Languages Status of <span style="color:#09C"><?php echo $name; ?></span></h1>
  <fieldset class="row1">
    <legend>Language Details
          </legend>
    <p>
      <label>Update Skill *
                  </label>
      <select name="skill" id="skill" required=''>
        <?php 			
$sql_opt="SELECT skill FROM skill ORDER BY skill ASC";
$result_opt=mysqli_query($con,$sql_opt);
$options="";
while ($row_opt=mysqli_fetch_array($result_opt)) {
    $code=$row_opt["skill"];
    $name_opt=$row_opt["skill"];
    $options.="<OPTION value='$code'>".$name_opt;}
?>
        <option value="0">--Select--</option>
        <?php echo $options; ?>
        </option>
      </select>
      <!--
                  <label class="obinfo">* obligatory fields
                  </label>-->
              <?php if(isset($_POST['submit'])){$c3=$_POST['skill'];$acttObj->editFun($table,$edit_id,'code',$reqcode);$acttObj->editFun($table,$edit_id,'skill',$c3);} ?>
            </p>
          </fieldset>
          
  <div><button class="button" type="submit" name="submit">Submit &raquo;</button></div>
</form>
</body>
</html>
<?php
if(isset($_POST['submit'])){echo "<script>alert('Successful!');</script>";}

?>

<script>
  window.onunload = refreshParent;
 function refreshParent() {
    window.opener.location.reload();
	
}</script>





