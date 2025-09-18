<?php
include "userhaspage.php";
SysPermiss::UserHasPage(__FILE__);
?>
<?php if(session_id() == '' || !isset($_SESSION)){session_start();} ?>
<?php include'db.php'; include'class.php'; if(isset($_POST['submit'])){$table='expence_list';$dbtitle=$acttObj->uniqueFun($table,'title',$_POST['title']);
if($dbtitle==0){$edit_id= $acttObj->get_id($table);}}?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
        <title>LSUK</title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <link rel="stylesheet" type="text/css" href="css/default.css"/>    
<?php include'ajax_uniq_fun.php'; ?>
	
  <script type="text/javascript">
function MM_openBrWindow(theURL,winName,features) { //v2.0
  window.open(theURL,winName,features);}</script>
</head>
<body>
<form action="" method="post" class="register" id="signup_form" name="signup_form" onsubmit="return formSubmit()">
  <h1>Expenses Information</h1>
  <fieldset class="row1">
    <legend>Expenses</legend>
    <p>
      <label>Expense Title * </label>
      <input name="title" type="text" placeholder='' required='' id="title" onBlur="uniqueFun(this.value,'expence_list','title',$(this).attr('id') );" />
      <?php if(isset($_POST['submit']) && $dbtitle==0){$c2=$_POST['title']; $acttObj->editFun($table,$edit_id,'title',$c2);} ?>
    </p><div>
    <button class="button" type="submit" name="submit" style="margin-left:450px;" onclick="return formSubmit(); return false">Submit &raquo;</button>
  </div>
  </fieldset><fieldset class="row1">
     <legend>List of Company Expenses
     </legend>
            
     <table width="30%" border="1">
      <?php $table='expence_list';
	   $query="SELECT * FROM $table";			
			$result = mysqli_query($con,$query);
			while($row = mysqli_fetch_array($result)){?>
  <tr>
    <td align="left"><?php echo $row['title']; ?> </td>
    <td align="left">
     <a href="#" onClick="MM_openBrWindow('edit_general.php?edit_id=<?php echo @$row['id']; ?>&table=<?php echo $table; ?>&col=title&pattern=','_blank','scrollbars=yes,resizable=yes,width=400,height=200')" style="color:#F00;"><img src="images/icn_edit.png" title="Trash" height="14" width="16" /></a>
    <?php if($_SESSION['prv']=='Management'){ ?>
     <a href="#" onClick="MM_openBrWindow('del.php?del_id=<?php echo $row['id']; ?>&table=<?php echo $table; ?>','_blank','scrollbars=yes,resizable=yes,width=400,height=200')"><img src="images/icn_trash.png" title="Trash" height="14" width="16" /></a><?php } ?></td>
    </tr>
    <?php } ?>
  </table>
           
     </fieldset>
  
</form>
</body>
</html>
<?php if(isset($_POST['submit'])){
echo "<script>alert('Successful!');</script>";?>
<script>window.onunload = refreshParent;
function refreshParent() {window.opener.location.reload();}</script>
<?php } ?>