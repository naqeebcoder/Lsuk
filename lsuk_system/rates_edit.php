<?php include'db.php'; include'class.php';$table='booking_type'; $edit_id= @$_GET['edit_id'];
	   $query="SELECT * FROM $table where id=$edit_id";			
			$result = mysqli_query($con,$query);
			$row = mysqli_fetch_array($result); $title =$row['title']; $type = $row['type']; $rate = number_format($row['rate'],2); ?>


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
  <h1>Booking Rates Information</h1>
  <fieldset class="row1">
    <legend>Rates Details </legend>
    <p>
      <label>Title * </label>
      <input name="title" type="text" placeholder='' required='' id="title" value="<?php echo $title; ?>"/>
      <label>Rate &pound;* </label>
      <input name="rate" type="text" placeholder='' required='' id="rate" pattern="[0-9]+([\.|,][0-9]+)?" step="0.01" value="<?php echo $rate; ?>" />
      <?php if(isset($_POST['submit'])){$data=$_POST['title']; $acttObj->editFun($table,$edit_id,'title',$data);} ?>
      <?php if(isset($_POST['submit'])){$data=$_POST['rate']; $acttObj->editFun($table,$edit_id,'rate',$data);} ?>
    </p>
        <p>
      <label>Type * </label>
      <select name="type" id="type" required>
      <option><?php echo $type; ?></option>
      <option>Interpreter</option>
      <option>Telephone</option>
      <option>Translation</option>
      
      </select>
      <?php if(isset($_POST['submit'])){$data=$_POST['type']; $acttObj->editFun($table,$edit_id,'type',$data);} ?>
    </p>
    <div>
    <button class="button" type="submit" name="submit" style="margin-left:450px;" onclick="return formSubmit(); return false">Submit &raquo;</button>
  </div>
  </fieldset>
  
  
</form>
</body>
</html>
<?php if(isset($_POST['submit'])  && $dbemail==0){
echo "<script>alert('Successful!');</script>";?>
<script>window.onunload = refreshParent;
function refreshParent() {window.opener.location.reload();}</script>
<?php } ?>