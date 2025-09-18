<?php
include "userhaspage.php";
SysPermiss::UserHasPage(__FILE__);
if (session_id() == '' || !isset($_SESSION)) {
    session_start();
}
include 'db.php';
include 'class.php';
$fdate = @$_GET['fdate'];
$tdate = @$_GET['tdate'];
$comp = @$_GET['comp']; ?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
  <title>Assign Interpreter</title>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <link rel="stylesheet" type="text/css" href="css/default.css" />

</head>

<body>

  <form action="" method="post" class="register">

    <h1>Total Invoices Registered One Company Between Two Dates</h1>
    <fieldset class="row1">
      <legend>Select Company &amp; Dates
      </legend>
      <script>
        function myFunction() {
          var x = document.getElementById("fdate").value;
          var y = document.getElementById("tdate").value;
          var z = document.getElementById("company").value;
          window.location.href = "comp_query.php?fdate=" + x + "&tdate=" + y + "&comp=" + z;
        }
      </script>
      <p><label>Select Interpreter</label>
        <select id="company" name="company">
          <?php
          $sql_opt = "SELECT * FROM comp_reg ORDER BY name ASC";
          $result_opt = mysqli_query($con, $sql_opt);
          $options = "";
          while ($row_opt = mysqli_fetch_array($result_opt)) {
            $code = $row_opt["abrv"];
            $name_opt = $row_opt["name"];
            $options .= "<OPTION value='$code'>" . $name_opt;
          }
          ?>
          <option value="0">--Select--</option>
          <?php echo $options; ?>
          </option>
        </select>
      </p>
      <p><label>From Date *</label>
        <input type="date" name="fdate" id="fdate" />
        <label>To Date *</label>
        <input type="date" name="tdate" id="tdate" onchange="myFunction()" />
      </p>
    </fieldset>
    <p style="display:none" id="demo"></p>
    <br /><br />
    <?php
    if (!empty($fdate) && !empty($tdate)) {
      $query = "SELECT count(*) as interp FROM interpreter where orgName= '$comp' and dated BETWEEN '$fdate' AND '$tdate'";
      $result = mysqli_query($con, $query);
      while ($row = mysqli_fetch_array($result)) {
        $interp = $row['interp'];
      }

      $query = "SELECT count(*) as telep FROM telephone where orgName= '$comp' and dated BETWEEN '$fdate' AND '$tdate'";
      $result = mysqli_query($con, $query);
      while ($row = mysqli_fetch_array($result)) {
        $telep = $row['telep'];
      }

      $query = "SELECT count(*) as trans FROM translation where orgName= '$comp' and dated BETWEEN '$fdate' AND '$tdate'";
      $result = mysqli_query($con, $query);
      while ($row = mysqli_fetch_array($result)) {
        $trans = $row['trans'];
      }
    ?>

      <fieldset class="row1">
        <legend>Total Invoices Registered of <?php echo $comp; ?> Between <?php echo $fdate . ' to ' . $tdate; ?></legend>
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