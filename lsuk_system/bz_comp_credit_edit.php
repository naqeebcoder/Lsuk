<?php include'db.php'; session_start();include'class.php'; $table='bz_credit';$edit_id= @$_GET['edit_id'];
$query="SELECT * FROM $table where id=$edit_id";			
$result = mysqli_query($con,$query);
while($row = mysqli_fetch_array($result)){$orgName=$row['orgName'];$bz_credit=$row['bz_credit'];$creditId=$row['creditId'];$comments=$row['comments'];}?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
        <title>Sign Up Form</title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <link rel="stylesheet" type="text/css" href="css/default.css"/>    
<?php include'ajax_uniq_fun.php'; ?>
</head>
<body>
<form action="" method="post" class="register" id="signup_form" name="signup_form" onsubmit="return formSubmit()">
  <h1>Credit Information</h1>
  <fieldset class="row1">
    <legend>Credit Details </legend>
    <p>
      <label>Company * </label>
      <select id="orgName" name="orgName">
                    <?php 			
$sql_opt="SELECT name,abrv FROM comp_reg ORDER BY name ASC";
$result_opt=mysqli_query($con,$sql_opt);
$options="";
while ($row_opt=mysqli_fetch_array($result_opt)) {
    $code=$row_opt["abrv"];
    $name_opt=$row_opt["name"];
	
	if($orgName==$code){$fulname=$name_opt;$abrivation=$code;}
    $options.="<OPTION value='$code'>".$name_opt;}
?>
					<option value="<?php echo $abrivation ?>"><?php echo $fulname ?></option>
                    <option value="0">--Select--</option>
                    <?php echo $options; ?>
                    </option>
                  </select>
      <label>Credit &pound;* </label>
      <input name="bz_credit" type="text" placeholder='' required='' id="bz_credit" pattern="[0-9]+([\.|,][0-9]+)?" step="0.01" value="<?php echo $bz_credit ; ?>" />
      <?php if(isset($_POST['submit'])){$data=$_POST['orgName']; $acttObj->editFun($table,$edit_id,'orgName',$data);} ?>
      <?php if(isset($_POST['submit'])){$data=$_POST['bz_credit']; $acttObj->editFun($table,$edit_id,'bz_credit',$data);} ?>
    </p>
    <p> <label><strong><em>Credit Id.</em></strong></label>
                  <input name="creditId" type="text" id="creditId"  placeholder='' value="<?php echo $creditId ; ?>" readonly="readonly" /></p>
  <div class="infobox"><h4>Notes if Any 1000 alphabets</h4>
            <p> <textarea name="comments" cols="51" rows="5"><?php echo $comments ; ?></textarea>
            <?php if(isset($_POST['submit'])){$data=$_POST['comments'];$acttObj->editFun($table,$edit_id,'comments',$data);} ?>
                  </p>
    <div></div></div>
    <div>
      <button class="button" type="submit" name="submit" style="margin-left:450px;" onclick="return formSubmit(); return false">Submit &raquo;</button>
</div>
  </fieldset>
</form>
</body>
</html>
<?php if(isset($_POST['submit'])){echo "<script>alert('Successful!');</script>";$acttObj->editFun($table,$edit_id,'edited_by',$_SESSION['UserName']);$acttObj->editFun($table,$edit_id,'edited_date',date("Y-m-d H:i:s"));$acttObj->new_old_table('hist_'.$table,$table,$edit_id);?>
<script>window.onunload = refreshParent;function refreshParent() {window.opener.location.reload();}</script>
<?php } ?>