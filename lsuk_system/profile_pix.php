<?php include'db.php';session_start(); include'class.php'; $table='interpreter_reg';$interp_code=$_SESSION['interp_code'];
  $query="SELECT * FROM interpreter_reg
		
		  	where code='$interp_code'";
			$result = mysqli_query($con,$query);
			$row = mysqli_fetch_array($result);
			$interp_id=$row['id'];
			$interp_pix=$row['interp_pix']?:"profile.png"; ?>


  <link rel="stylesheet" type="text/css" href="css/layout.css" /><br /><br /><br />
<div align="center">
  <span style="font-weight:bold; color:#09F;">Record ID: <?php echo @$interp_id; ?></span><br /><br />
 <p><form action="" method="post" class="register"  enctype="multipart/form-data" name="maxform">
      <label class="optional">Upload your Photo </label>
      <input name="file" type="file" class="long" id="file" style="border:1px solid #CCC" placeholder='' />
<?php if(isset($_POST['submit'])){
	error_reporting(0); 
	if(!empty($_FILES["file"]["name"])){
		$old_file='file_folder/interp_photo/'.$interp_pix;
		if(file_exists($old_file) && $interp_pix!="profile.png"){
			unlink($old_file);
		}
		$profile_photo=$acttObj->upload_file("interp_photo",$_FILES["file"]["name"],$_FILES["file"]["type"],$_FILES["file"]["tmp_name"],round(microtime(true)));
		$acttObj->editFun($table,$interp_id,'interp_pix',$profile_photo);
		$acttObj->editFun($table,$interp_id,'pic_updated',1);
		echo "<script>window.close();</script>";
	}
} ?>
        <button class="button" type="submit" name="submit">Submit &raquo;</button></form>
    </p>
<script>window.onunload = refreshParent;function refreshParent() {window.opener.location.reload();}</script>