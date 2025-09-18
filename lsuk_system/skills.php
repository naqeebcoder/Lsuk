<?php include "userhaspage.php";
SysPermiss::UserHasPage(__FILE__);
session_start();include'db.php'; include'class.php'; if(isset($_POST['submit'])){$table='skill';$dbskill=$acttObj->uniqueFun($table,'skill',$_POST['skill']);
if($dbskill==0){$edit_id= $acttObj->get_id($table);}}?>

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
  <h1>Skill Information</h1>
  <fieldset class="row1">
    <legend>Skill</legend>
    <p>
      <label>Skill * </label>
      <input name="skill" type="text" placeholder='' required='' id="skill" onBlur="uniqueFun(this.value,'skill','skill',$(this).attr('id') );" />
      <?php if(isset($_POST['submit']) && $dbskill==0){$c2=$_POST['skill']; $acttObj->editFun($table,$edit_id,'skill',$c2);} ?>
    </p><div>
    <button class="button" type="submit" name="submit" style="margin-left:450px;" onclick="return formSubmit(); return false">Submit &raquo;</button>
  </div>
  </fieldset><fieldset class="row1">
     <legend>skill can interpret
     </legend>
            
     <table width="30%" border="1">
      <?php $table='skill';
	   $query="SELECT * FROM $table";			
			$result = mysqli_query($con,$query);
			while($row = mysqli_fetch_array($result)){?>
  <tr>
    <td align="left"><?php echo $row['skill']; ?> </td>
    <td align="left"> <?php if($_SESSION['prv']=='Management' || $_SESSION['prv']=='Finance' ){?>
   <a href="#" onClick="MM_openBrWindow('edit_general.php?edit_id=<?php echo @$row['id']; ?>&table=<?php echo $table; ?>&col=skill&pattern=','_blank','scrollbars=yes,resizable=yes,width=400,height=200')" style="color:#F00;"><img src="images/icn_edit.png" title="Trash" height="14" width="16" /></a>
    
     <a href="#" onClick="MM_openBrWindow('del.php?del_id=<?php echo $row['id']; ?>&table=<?php echo $table; ?>','_blank','scrollbars=yes,resizable=yes,width=400,height=200')"><img src="images/icn_trash.png" title="Trash" height="14" width="16" /></a><?php } ?></td>
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