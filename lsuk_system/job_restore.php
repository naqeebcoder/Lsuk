<html>
<body style="background-color:#099">
 <link rel="stylesheet" type="text/css" href="css/layout.css" /><br /><br /><br />
<div align="center" style="font-weight:bold">
  <span style="font-weight:bold; color:#9F0;">Record ID: <?php echo @$_GET['rest_id']; ?></span><br /><br />
<form action="" method="post">
Are you sure you want to <span style="color:#930; font-weight:bold;"><u>Restore</u></span> this Cancelled Order&nbsp;&nbsp;<input type="submit" name="yes" value="Yes" />&nbsp;&nbsp;<input type="submit" name="no" value="No" />
</form>
</div>
<?php

 include 'db.php';include'class.php';
if(isset($_POST['yes'])){
session_start();
$rest_id = @$_GET['rest_id'];
$table = @$_GET['table'];
$acttObj->editFun($table,$rest_id,'order_cancel_flag',0);?>
<script>window.close();
window.onunload = refreshParent;
function refreshParent() {window.opener.location.reload();}</script>
<?php }
if(isset($_POST['no'])){
    echo "<script>window.close();</script>";
}?>
</body>
</html>