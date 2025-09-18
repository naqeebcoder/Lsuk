<?php include'db.php'; include'class.php';$fdate= @$_GET['fdate'];$tdate= @$_GET['tdate'];?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
        <title>Assign Interpreter</title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <link rel="stylesheet" type="text/css" href="css/default.css"/>

</head>
<body>    

        <form action="" method="post" class="register">
        
          <h1>Total Collected</h1>
          <fieldset class="row1">
            <legend>Select Dates
          </legend>
 <script>
function myFunction() {
    var x = document.getElementById("fdate").value;var y = document.getElementById("tdate").value;
	window.location.href = "invoice_query.php?fdate=" + x + "&tdate=" + y ;}
</script>
<p><label>From Date *</label>
<input type="date" name="fdate" id="fdate" />
<label>To Date *</label>
<input type="date" name="tdate" id="tdate"  onchange="myFunction()" />
</p></fieldset>
<p style="display:none" id="demo"></p>
<br /><br />
<?php
if(!empty($fdate) && !empty($tdate)){
$query="SELECT count(*) as interp FROM interpreter where dated BETWEEN '$fdate' AND '$tdate'";			
$result = mysqli_query($con,$query);
while($row = mysqli_fetch_array($result)){$interp=$row['interp'];}

$query="SELECT count(*) as telep FROM telephone where dated BETWEEN '$fdate' AND '$tdate'";		
$result = mysqli_query($con,$query);
while($row = mysqli_fetch_array($result)){$telep=$row['telep'];}

$query="SELECT count(*) as trans FROM translation where dated BETWEEN '$fdate' AND '$tdate'";
$result = mysqli_query($con,$query);
while($row = mysqli_fetch_array($result)){$trans=$row['trans'];}
?>
          
          <fieldset class="row1">
            <legend>Total Record</legend>
            <table width="100%" border="1">
              <tr>
                <th width="200" align="left">Interpreter</th>
                <td width="250"><?php echo $interp; ?></td>
                <th width="200" align="left">Translation Interpreter</th>
                <td width="250"><?php echo $trans; ?></td>
              </tr>
              <tr>
                <th width="200" align="left">Telephone Interpreter</th>
                <td width="250"><?php echo $telep; ?></td>
                <th width="200" align="left">&nbsp;</th>
                <td width="250">&nbsp;</td>
              </tr>
            </table>
          </fieldset>
          <?php } ?>
        </form>
</body>
</html>




