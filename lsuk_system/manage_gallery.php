<?php
include "userhaspage.php";
SysPermiss::UserHasPage(__FILE__);
?>
<?php 
if(session_id() == '' || !isset($_SESSION))
{
	session_start();
} 

include 'db.php';
include 'class.php';
include_once ('function.php');

$table='images';

 if(isset($_GET['edit_id']) && isset($_GET['status'])){
  $dated=date('Y-m-d');
  $status=$_GET['status']=='active'?'De-active':'active';
      $queryedit="update images set dated='$dated',status='$status' where id=".$_GET['edit_id'];
      if(mysqli_query($con,$queryedit)){
       echo '<script>alert("Gallery Successfully updated!");window.location.href="manage_gallery.php";</script>';
      }else{
      echo '<script>alert("Sorry, There is some error!");</script>';
      }
 }
 if(isset($_POST['submit'])){
 $file=basename($_FILES["interpreterphoto"]["name"]);
 $title=mysqli_real_escape_string($con,$_POST['title']);
  $dated=date('Y-m-d');$status='active';
      $queryinsert="INSERT INTO images VALUES (NULL,'$file','$title','$status','$dated')";
      if(mysqli_query($con,$queryinsert)){
          move_uploaded_file($_FILES['interpreterphoto']['tmp_name'], '../gallery/'.$file);
       echo '<script>alert("New Item Successfully inserted!");window.location.href="manage_gallery.php";</script>';
      }else{
      echo '<script>alert("Sorry, There is some error!");</script>';
      }
 } ?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
        <title>Manage Gallery</title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <link rel="stylesheet" type="text/css" href="css/default.css"/>  
        <link rel="stylesheet" type="text/css" href="css/bootstrap.css"/>    
<?php $table='images'; ?>
	
  <script type="text/javascript">
function MM_openBrWindow(theURL,winName,features) { //v2.0
  window.open(theURL,winName,features);}</script>
  <style>
  form.register fieldset.row1 {
    width: 135%;}
    .cke_chrome {
    min-width: 800px;
}
  </style>
</head>
<?php include 'header.php'; ?>
<body>    
<?php include 'horz_nav.php'; ?>
<?php include 'nav.php'; ?>
<form action="" method="post" class="register" id="signup_form" name="signup_form" onsubmit="return formSubmit()" enctype="multipart/form-data">
  <h1>Gallery Management</h1>
  <fieldset class="row1">
    <?php if(!isset($_GET['edit_id'])){ ?><legend>Add New Item</legend><?php } ?>
    <p>
      
            <div style="margin-top: 20px;padding: 5px;">
            <div class="container">
                  <p><div class="row">
        			<div class="col-md-10 nopadding">
                          <p>
                        <img id="output" src="images/default.png" name="output" class="img-thumbnail img-responsive" style="max-width: 140px;max-height: 140px;min-width: 140px;min-height: 140px;" /><p>
                        </p>
                        <input type="file" required='' name="interpreterphoto" value="images/default.png" accept="image/*" onchange="loadFile(event)" id="interpreterphoto" style='width: 25%;float: none;'>
                        </p>   
                    </div>
                    <div class="col-md-6">
                          <p>
                            <input name="title" style="padding: 18px;" type="text" class="form-control" placeholder='Title for Gallery item' id="title" />
                        </p>   
                    </div>
	    </div>
		<script>
	initSample();
</script>
    </p><div>
    
    <button class="button" type="submit" name="submit">Add New &raquo;</button>
  </div>
  </fieldset><fieldset class="row1">
     <legend>Gallery List in database
     </legend>
            <table class="table table-bordered">
  <thead>
    <tr>
      <th scope="col">S.No</th>
      <th scope="col">Item</th>
      <th scope="col">Title</th>
      <th scope="col">Dated</th>
      <th scope="col">Status</th>
      <th scope="col">Action</th>
    </tr>
  </thead>
  <tbody>
      <?php 
	   $query="SELECT * FROM $table";			
			$result = mysqli_query($con,$query); while($row = mysqli_fetch_array($result)){?>
  <tr><td align="left"><?php echo $row['id']; ?> </td>
  <td align="left"><img src="../gallery/<?php echo $row['file']; ?>" width="60" height="60"/></td>
  <td align="left"><?php echo substr($row['title'],0,200); ?> </td>
    <td align="left"><?php echo $row['dated']; ?> </td>
    <td align="left"><?php echo $row['status']=='active'?'<span style="color:white;background:green;padding:2px;">'.$row['status'].'</span>':'<span style="color:white;background:red;padding:2px;">'.$row['status'].'</span>'; ?> </td>
    <td align="left"> 
   <a href="manage_gallery.php?edit_id=<?php echo $row['id']; ?>&status=<?php echo $row['status']; ?>" style="color:#F00;"><img src="images/icn_edit.png" title="Update Status" height="14" width="16" /></a>
</td>
    </tr>
    <?php } ?>
  </tbody>
</table>
           
     </fieldset>
  
</form>
</body>
</html>


<script>
    var loadFile = function (event) {
      var output = document.getElementById('output');
      output.src = URL.createObjectURL(event.target.files[0]);
    };
    window.onunload = refreshParent;function refreshParent() {window.opener.location.reload();}</script>
