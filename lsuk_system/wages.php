<?php 
include'db.php'; 
include'class.php';
$empId= @$_GET['empId']; 
if(isset($_POST['submit']))
{
  $table='rolcal';
}
?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
        <title>Employee Attendence / Wages</title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <link rel="stylesheet" type="text/css" href="css/default.css"/>    
<?php include'ajax_uniq_fun.php'; ?>
	

</head>
<body>
<form action="" method="post" class="register" id="signup_form" name="signup_form" onsubmit="return formSubmit()">
  <h1>Attendance Form</h1>
  <fieldset class="row1">
    <legend>Staff Attendance </legend>
    <p>
    
    <table width="80%" border="1" style="margin-left:50px;">
 
  <tr>
    <td align="left" bgcolor="#F4F4F4">#</td>
    <td align="left" bgcolor="#F4F4F4"><strong>Employee Name</strong></td>
    <td bgcolor="#F4F4F4"><strong>Designation</strong></td>
    <td bgcolor="#F4F4F4"><strong>Dated</strong></td>
    <td width="10" bgcolor="#F4F4F4"><strong>Start Time</strong></td>
    <td width="10" bgcolor="#F4F4F4"><strong>Finish Time</strong></td>
  </tr>
  <?php 
  $table_emp='emp';
  $i=1;
  $query=
  "SELECT * 
  FROM $table_emp 
  where id=$empId";
  
  $result = mysqli_query($con,$query);
	while($row = mysqli_fetch_array($result)){ $empId=$row['id'];$phs=$row['phs'];if(!empty($empId) && isset($_POST['submit'])){$edit_id= $acttObj->get_id($table);}?>   
  <tr>
    <td><?php echo $i; ?></td>
     <td><?php echo $row['name']; ?></td>
    <td><?php echo $row['desig']; ?></td>
    <td><input type="date" id="entry_date" name="entry_date" required="required"/>
      <?php if(isset($_POST['submit']) && !empty($empId)){$entry_date=$_POST['entry_date'];$acttObj->editFun($table,$edit_id,'entry_date',$entry_date);} ?></td>
    <td>
      <input type="time" id="start<?php echo $i; ?>" name="start<?php echo $i; ?>"required="required"/>
	  <?php if(isset($_POST['submit']) && !empty($empId)){$start=$_POST['start'.$i];$acttObj->editFun($table,$edit_id,'start',$start);} ?></td>
      
    <td> <input type="time" id="finish<?php echo $i; ?>" name="finish<?php echo $i; ?>"required="required"/>
	<?php if(isset($_POST['submit']) && !empty($empId)){$finish=$_POST['finish'.$i];$acttObj->editFun($table,$edit_id,'finish',$finish);} ?></td>
   </td>
  </tr>
  <?php    if(isset($_POST['submit']) && !empty($empId)){$acttObj->editFun($table,$edit_id,'empId',$empId);}
  if(isset($_POST['submit']) && !empty($empId)){$duration=round((strtotime($finish) - strtotime($start)) /60)/60;$acttObj->editFun($table,$edit_id,'duration',$duration);$acttObj->editFun($table,$edit_id,'rph',$phs);$acttObj->editFun($table,$edit_id,'salary',$duration * $phs);}
   $i++;} ?>
    </table>

         
    </p>
  </fieldset>
  <div>
    <button class="button" type="submit" name="submit" style="margin-left:450px;" onclick="return formSubmit(); return false">Submit &raquo;</button>
  </div>
</form>
</body>
</html>

<?php if(isset($_POST['submit'])  && $dbemail==0){session_start();$acttObj->editFun($table,$edit_id,'sbmtd_by',ucwords($_SESSION['UserName']));echo "<script>alert('Successful!');</script>";}?>
<script>window.onunload = refreshParent;function refreshParent() {window.opener.location.reload();}</script>