  <link rel="stylesheet" type="text/css" href="css/layout.css" /><br /><br /><br />
<div align="center"><?php $col=$_GET['col']; ?>
  <span style="font-weight:bold; color:#09F;">Record ID: <?php echo @$_GET['edit_id']; ?></span><br /><br />
<form action="" method="post"enctype="multipart/form-data">

<label class="optional"><?php echo @$_GET['text']; ?></label>
<select name="rec_mode" required style="height:25px; width:155px"><option value="">..Select..</option><option>Not Provided</option><option>Soft Copy</option><option>Hard Copy</option></select><br/><br/>
 <input name="<?php echo $col; ?>" type="file" class="long" id="<?php echo $col; ?>" style="border:1px solid #CCC" placeholder='' /><br/></br/>
Are you sure you want to edit this record&nbsp;&nbsp;<input type="submit" name="yes" value="Yes" />&nbsp;&nbsp;<input type="submit" name="no" value="No" />
</form>
</div>
<?php

if(isset($_POST['yes'])){error_reporting(0);
 $edit_id =$_GET['edit_id'];$table = $_GET['table'];$data = @$_GET['data'];$col=$_GET['col'];
 include 'class.php';if(!empty($data)){unlink('file_folder/'.$col.'/'.$data);}
 if(!empty($_POST['rec_mode'])){$acttObj->editFun($_GET['table'],$edit_id,$col,$_POST['rec_mode']);}else{
 $picName=$acttObj->upload_file($col,$_FILES[$col]["name"],$_FILES[$col]["type"],$_FILES[$col]["tmp_name"],$edit_id);
 $acttObj->editFun($_GET['table'],$edit_id,$col,$picName);}
 
 echo "<script>window.close();</script>";}
if(isset($_POST['no'])){echo "<script>window.close();</script>";};
?>
<script>
  window.onunload = refreshParent;
 function refreshParent() {
    window.opener.location.reload();
}</script>