<?php
include "userhaspage.php";
SysPermiss::UserHasPage(__FILE__);
if (session_id() == '' || !isset($_SESSION)) {
    session_start();
}
include 'db.php';
include 'class.php';?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
  <title>VAT COLLECTED REPORT</title>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" />
</head>

<body>
  <script>
    function myFunction() {
      var x = document.getElementById("fdate").value;
      var y = document.getElementById("tdate").value;
      window.location.href = "vat_query.php?fdate=" + x + "&tdate=" + y;
    }
  </script>
  <div class="container">
    <form action="" method="post" class="register">
      <h1>Total VAT Collected Between Two Dates</h1>
      <fieldset class="row1">
        <legend>Select Dates</legend>
        <div class="form-group col-sm-6"><label>From Date *</label>
          <input type="date" name="fdate" class="form-control" id="fdate" />
        </div>
        <div class="form-group col-sm-6"><label>To Date *</label>
          <input type="date" name="tdate" id="tdate" class="form-control" onchange="myFunction()" />
        </div>
    </form>
  </div>
  <?php
  if (!empty($_GET['fdate']) && !empty($_GET['tdate'])) {
    $fdate = $_GET['fdate'];
    $tdate = $_GET['tdate'];
    $query = "SELECT round(IFNULL(sum(interpreter.total_charges_comp*interpreter.cur_vat),0),2) as interp FROM interpreter where assignDate BETWEEN '$fdate' AND '$tdate' and deleted_flag = 0 and order_cancel_flag=0";
    $result = mysqli_query($con, $query);
    while ($row = mysqli_fetch_array($result)) {
      $interp = $row['interp'];
    }

    $query = "SELECT round(IFNULL(sum(telephone.total_charges_comp*telephone.cur_vat),0),2) as telep FROM telephone where assignDate BETWEEN '$fdate' AND '$tdate' and deleted_flag = 0 and order_cancel_flag=0";
    $result = mysqli_query($con, $query);
    while ($row = mysqli_fetch_array($result)) {
      $telep = $row['telep'];
    }

    $query = "SELECT round(IFNULL(sum(translation.total_charges_comp*translation.cur_vat),0),2) as trans FROM translation where asignDate BETWEEN '$fdate' AND '$tdate' and deleted_flag = 0 and order_cancel_flag=0";
    $result = mysqli_query($con, $query);
    while ($row = mysqli_fetch_array($result)) {
      $trans = $row['trans'];
    }
  ?>

    <div class="container">
      <legend>Total VAT Received Between <?php echo $fdate . ' to ' . $tdate; ?></legend>
      <table class="table table-bordered table-hover">
        <tr>
          <th width="200" align="left">Interpreter</th>
          <td width="250"><?php echo number_format($interp, 2); ?></td>
        </tr>
        <tr>
          <th width="200" align="left">Telephone Interpreter</th>
          <td width="250"><?php echo number_format($telep, 2); ?></td>
        </tr>
        <tr>
          <th width="200" align="left">Translation Interpreter</th>
          <td width="250"><?php echo number_format($trans, 2); ?></td>
        </tr>
        <tr class="bg-info">
          <th width="200" align="left">Total Sum</th>
          <th width="250"><?php echo number_format($interp + $telep + $trans, 2); ?></th>
        </tr>
      </table>
    </div>
  <?php } ?>
</body>

</html>