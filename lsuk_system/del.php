<?php echo "<br><br><br><h4>Permanent delete is no more available! Contact Admin</h4>";exit;
if(session_id() == '' || !isset($_SESSION)){session_start();} ?>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<title>Delete Record</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="css/bootstrap.css">
<style>.b{color: #fff;}a:link, a:visited {color: #337ab7;}</style>
</head>
<body>
<br />

<?php
//management
if($_SESSION['prv']<>'Management')
{
  ?>
  <div>
  <h3>Only management can delete Job notes</h3>
  </div>
  <?php
  return;
}
?>

<div align="center">
<h3>Record ID: <span class="label label-danger"><?php echo @$_GET['del_id']; ?></span></h3><br/>

<form action="#" method="post" class="col-xs-12">
  <h3 class="h4">Are you sure you want to <span class="text-danger"><b>Permanentaly Delete</b></span> this record ?</h3>
  <input type="submit" class="btn btn-primary" name="yes" value="Yes >" />&nbsp;&nbsp;<input class="btn btn-warning" type="submit" name="no" value="No" />
</form>

</div>

<?php
if(isset($_POST['yes'])){
  $del_id = @$_GET['del_id'];
  $table = @$_GET['table'];
  $hist_table='hist_'.$table;
  
  include 'db.php';
 
  mysqli_query($con,"DELETE FROM $table WHERE id=$del_id");
  mysqli_query($con,"DELETE FROM $hist_table WHERE id=$del_id");
  mysqli_close($con);
  if($table=='interpreter_reg')
  {
    $del_id='id-'.$del_id;
    mysqli_query($con,"DELETE FROM  interp_lang WHERE code=$del_id");
    mysqli_close($con);
  } ?>
  <script>window.onunload = refreshParent;
    function refreshParent(){
      window.opener.location.reload();
    }
    window.close();</script>
<?php }
  
if(isset($_POST['no']))
{
  echo "<script>window.close();</script>";
} ?>
</body>
</html>