<?php if (session_id() == '' || !isset($_SESSION)) {
    session_start();
}
include 'actions.php';
$allowed_type_idz = "80,92,126,183";
//Check if user has current action allowed
if ($_SESSION['is_root'] == 0) {
  $get_page_access = $obj->read_specific("id", "action_permissions", "user_id=" . $_SESSION['userId'] . " AND action_id IN (" . $allowed_type_idz . ")")['id'];
  if (empty($get_page_access)) {
    die("<center><h2 class='text-center text-danger'>You do not have access to <u>View Job Earnings</u> action for jobs!<br>Kindly contact admin for further process.</h2></center>");
  }
}
$table = 'telephone';
$view_id = @$_GET['view_id'];
$row = $obj->read_specific("*", "$table", "id=$view_id");
$hoursWorkd = $row['hoursWorkd'];
$chargInterp = $row['chargInterp'];
$rateHour = $row['rateHour'];
$total_charges_interp = $row['total_charges_interp'];
$C_hoursWorkd = $row['C_hoursWorkd'];
$C_chargInterp = $row['C_chargInterp'];
$C_rateHour = $row['C_rateHour'];
$total_charges_comp = $row['total_charges_comp'];
$C_admnchargs = $row['C_admnchargs'];
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
  <title>Telephone Interpreter Company Earning</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="css/bootstrap.css">
</head>

<body>
  <form class="container-fluid">
    <h3>Company's Earning Record of Telephone Interpreter</h3>
    <fieldset class="row1">
      <table width="98%" border="4" class="table table-bordered table-hover">
        <tr bgcolor="#006666" style="color:#FFF">
          <th align="left"><strong>Interpreter Charges</strong></th>
          <td align="left"><strong>Expences</strong></td>
          <th align="left"><strong>LSUK Charges</strong></th>
          <td align="left"><strong>Expences</strong></td>
        </tr>
        <tr>
          <th align="left">Hours Worked</th>
          <td width="100" align="left"><?php echo $hoursWorkd; ?></td>
          <th align="left">Hours Worked</th>
          <td width="100" align="left"><?php echo $C_hoursWorkd; ?></td>
        </tr>
        <tr>
          <th align="left">Rate Per Hour</th>
          <td align="left"><?php echo $rateHour; ?></td>
          <th align="left">Rate Per Hour</th>
          <td align="left"><?php echo $C_rateHour; ?></td>
        </tr>
        <tr>
          <th align="left">Charge for Interpreting Time</th>
          <td align="left"><?php echo $chargInterp; ?></td>
          <th align="left">Charge for Interpreting Time</th>
          <td align="left"><?php echo $C_chargInterp; ?></td>
        </tr>
        <tr>
          <th align="left">&nbsp;</th>
          <td align="left">&nbsp;</td>
          <th align="left">Admin Charges</th>
          <td align="left"><?php echo $C_admnchargs; ?></td>
        </tr>
        <tr bgcolor="#FF6600" style="color:#000">
          <th align="left"><strong>Total</strong></th>
          <td align="left"><strong><?php echo $total_charges_interp; ?></strong></td>
          <th align="left"><strong>Total</strong></th>
          <td align="left"><strong><?php echo $total_charges_comp; ?></strong></td>
        </tr>
      </table>
    </fieldset>

  </form>

</body>

</html>