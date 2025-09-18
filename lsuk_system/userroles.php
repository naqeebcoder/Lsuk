<?php 

session_start();
include'db.php'; 
include'class.php';
$table='userrole'; 

if (isset($_GET['edit_id']))
{
  //userroles.php?edit_id=13
  $usersid=$_GET['edit_id'];
}


if(isset($_POST['submit']))
{
  $edit_id= $acttObj->get_id($table);
}
?>

<?php
if(isset($_POST['submit']))
{
  $sysroles=$_POST['sysroles'];
  $acttObj->editFun($table,$edit_id,'userid',$usersid);
  $acttObj->editFun($table,$edit_id,'roleid',$sysroles); 
}
?>


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
  <h1>User Information</h1> 
  <span style="font-weight:bold; color:#09F;">Record ID: 
    <?php echo $usersid;?>
    </span>
    </span><br /><br />

  <fieldset class="row1">
    <legend>Update Roles this user</legend>

<p>Possible Roles&nbsp;&nbsp;</p>
<select name="sysroles" id="sysroles"  required>
       <?php 			

//complete list to add
$sql_opt=
"SELECT *  
FROM rolenamed
order by named";

$result_opt=mysqli_query($con,$sql_opt);
$options="";
while ($row_opt=mysqli_fetch_array($result_opt)) 
{
    $code=$row_opt["id"];
    $name_opt=$row_opt["named"];
    $options.="<OPTION value='$code'>".$name_opt;
}

?>
<?php 
if(!empty($sysroles))
{ 
  $rolenamed=$acttObj->unique_data('rolenamed','named','id',$sysroles);    
  ?>
	<option><?php echo $rolenamed; ?></option>
  <?php 
} 
else
{
  ?>
	<option value="">--Select Role--</option>
  <?php 
} 
?>
<?php echo $options; ?>
</option>
</select><div>

    <button class="button" type="submit" name="submit" 
      style="margin-left:450px;" onclick="return formSubmit(); return false">Submit &raquo;</button>
  </div>
</fieldset>

<fieldset class="row1">
    <legend>Users Roles Assigned
    </legend>
            
    <table width="30%" border="1">
    <?php 
    //main list
 	  $query="SELECT * FROM $table where userid=$usersid";			
		$result = mysqli_query($con,$query);
    while($row = mysqli_fetch_array($result))
    {
      $rolenamed=$acttObj->unique_data('rolenamed','named','id',$row['roleid']);  
      
      ?>
      <tr>
      <td align="left"><?php echo $rolenamed; ?> </td>
      <td align="left"> 
        <?php if($_SESSION['prv']=='Management' || $_SESSION['prv']=='Finance' )
        {
          ?>
          <a href="#" onClick="MM_openBrWindow('del.php?del_id=<?php echo $row['id']; ?>&table=<?php echo $table; ?>','_blank','scrollbars=yes,resizable=yes,width=400,height=200')">
            <img src="images/icn_trash.png" title="Trash" height="14" width="16" />
          </a>
          <?php 
        } 
        ?>
      </td>
    </tr>
    <?php 
    } ?>
  </table>
           
     </fieldset></form>
</div>

<?php
if(isset($_POST['submit']))
{
  echo "<script>alert('Successful!');</script>"; 
  ?>
  <script> 
  window.onunload = refreshParent;
  function refreshParent() 
  {
    window.opener.location.reload();
  }
  </script>
  <?php 
} 
?>
