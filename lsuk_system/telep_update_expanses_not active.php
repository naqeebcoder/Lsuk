<?php include'db.php'; include'class.php'; $update_id=$_GET['update_id']; $table='telephone';?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
        <title>Order Form</title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <link rel="stylesheet" type="text/css" href="css/default.css"/>
    </head>
<body>    
<form action="" method="post" class="register">
  <h1>Rate Per Hour</h1>
  <fieldset class="row1">
    <legend>Amount Details
          </legend>
    <p>
      <label>Rate Per Hour *
                  </label>
                  <input name="rateHour" type="number" pattern="[0-9]+([\.|,][0-9]+)?" step="0.01" id="rateHour" required=''/>
                  <!--
                  <label class="obinfo">* obligatory fields
                  </label>-->
                  
  
      
               <?php if(isset($_POST['submit'])){$c1=$_POST['rateHour']; $acttObj->editFun($table,$update_id,'rateHour',$c1);} ?>
            </p>
          </fieldset>
  <div><button class="button" type="submit" name="submit">Submit &raquo;</button></div>
</form>
</body>
</html>
<?php
if(isset($_POST['submit'])){echo "<script>alert('Successful!');</script>";}

?>

<script>
  window.onunload = refreshParent;
 function refreshParent() {
    window.opener.location.reload();
	
}</script>





