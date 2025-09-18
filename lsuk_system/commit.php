  <link rel="stylesheet" type="text/css" href="css/layout.css" /><br /><br /><br />
<div align="center">
  <span style="font-weight:bold; color:#09F;">Record ID: <?php echo @$_GET['com_id']; ?></span><br /><br />
<form action="" method="post">
Are you sure you want to commit this record&nbsp;&nbsp;
<input type="submit" name="yes" value="Yes" />&nbsp;&nbsp;<input type="submit" name="no" value="No" />
</form>
</div>
<?php

if(isset($_POST['yes'])){include 'class.php';
 $com_id = $_GET['com_id'];$table = $_GET['table'];
$acttObj->editFun($table,$com_id,'commit',1);
 echo "<script>window.close();</script>";}
if(isset($_POST['no'])){echo "<script>window.close();</script>";};
?>
<script>
  window.onunload = refreshParent;
 function refreshParent() {
    window.opener.location.reload();
}</script>