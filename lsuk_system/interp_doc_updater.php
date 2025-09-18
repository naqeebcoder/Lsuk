<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<title>Update Interpreter Document</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="css/bootstrap.css">
<script>function refreshParent() {
window.opener.location.reload();
}
</script>
</head>
<body>
<div align="center"><?php 
include 'class.php';
 $edit_id =$_GET['edit_id'];
 $table = $_GET['table'];
 $data = @$_GET['data'];
 $col=$_GET['col']; 
$name=$acttObj->unique_data($table,'name','id',$edit_id);
?>
  <h2>Update <?php echo @$_GET['text']; ?> for  <span class="label label-primary"><?php echo ucwords($name); ?></span></h2><br/>
<form action="" method="post"enctype="multipart/form-data" class="col-xs-12">
<center>
<div class="col-xs-6 col-xs-offset-3">
<select name="rec_mode" class="form-control">
<?php if(isset($data)){?>
<option value="<?php echo $data; ?>"><?php echo $data?:'Not Provided';?></option>
<?php } ?>
<option value="" disabled>--- Choose Document Type ---</option>
<option value="Soft Copy">Soft Copy</option>
<option value="Hard Copy">Hard Copy</option>
<option value="">Not Provided</option>
</select>
</div>
<div class="form-group col-xs-12">
<h3>Are you sure you want to <span class="text-success"><b>Edit</b></span> this record ?</h3>
</div>
<div class="form-group col-xs-12">
<input type="submit" name="yes" class="btn btn-primary" name="yes" value="Yes >" />&nbsp;&nbsp;<input type="submit" class="btn btn-warning" name="no" value="No" />
</div></center>
</form>
</div>
<?php

if(isset($_POST['yes'])){
    //error_reporting(0);
 $allowedExtns = array("gif", "jpeg", "jpg", "png", "pdf", "doc", "docx", "xlsx");
 $temp_flag = explode(".", $col);$extensions = end($temp_flag);
 if (in_array($extensions, $allowedExtns)){unlink('file_folder/'.$col.'/'.$data);}
 $acttObj->editFun($_GET['table'],$edit_id,$col,$_POST['rec_mode']);
 echo "<script>window.close();window.onunload = refreshParent;</script>";
}
if(isset($_POST['no'])){
    echo "<script>window.close();</script>";
}
?>
</body></html>