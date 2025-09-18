<?php
include 'db.php';include 'class.php';
$edit_id =$_GET['edit_id'];$table = $_GET['table'];$col = $_GET['col'];$pattern = @$_GET['pattern'];
$query="SELECT $col FROM $table where id=$edit_id";$result = mysqli_query($con,$query);
while($row = mysqli_fetch_array($result)){$data=$row[$col];}
if(isset($_POST['yes'])){
$acttObj->editFun($table,$edit_id,$col,$_POST[$col]); 
echo "<script>window.close();</script>";}
if(isset($_POST['no'])){echo "<script>window.close();</script>";};
?>

        <link rel="stylesheet" type="text/css" href="css/default.css"/><br /><br /><br />
<div align="center"><?php $col=@$_GET['col']; ?>
  <span style="font-weight:bold; color:#09F;">Record ID: <?php echo @$_GET['edit_id']; ?></span><br /><br />
<form action="" method="post"enctype="multipart/form-data">
 <input name="<?php echo $col; ?>" type="text" class="long" id="<?php echo $col; ?>" style="border:1px solid #CCC" placeholder='' value="<?php echo $data; ?>" <?php if(!empty($pattern)){?> pattern="<?php echo $pattern; ?>" <?php } ?> /><br/><br/>
Are you sure you want to modify this record&nbsp;&nbsp;<input type="submit" name="yes" value="Yes" />&nbsp;&nbsp;<input type="submit" name="no" value="No" />
</form>
</div>

<script>window.onunload = refreshParent;function refreshParent(){window.opener.location.reload();}</script>