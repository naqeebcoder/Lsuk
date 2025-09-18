<?php
if (session_id() == '' || !isset($_SESSION)) {
  session_start();
}
include 'db.php';
include 'class.php';
$allowed_type_idz = "61";
//Check if user has current action allowed
if ($_SESSION['is_root'] == 0) {
    $get_page_access = $acttObj->read_specific("id", "action_permissions", "user_id=" . $_SESSION['userId'] . " AND action_id IN (" . $allowed_type_idz . ")")['id'];
    if (empty($get_page_access)) {
        die("<center><h2 class='text-center text-danger'>You do not have access to <u>Update Booking Rates</u> action!<br>Kindly contact admin for further process.</h2></center>");
    }
}
$table = 'booking_fixed';
if (isset($_POST['submit'])) {
  $edit_id = $acttObj->get_id($table);
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
  <title>Update Rates Old</title>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <link rel="stylesheet" type="text/css" href="css/default.css" />
  <?php include 'ajax_uniq_fun.php'; ?>
  <script type="text/javascript">
    function MM_openBrWindow(theURL, winName, features) { //v2.0
      window.open(theURL, winName, features);
    }
  </script>
</head>

<body>

  <form action="" method="post" class="register" id="signup_form" name="signup_form" onsubmit="return formSubmit()">
    <h1>Booking Information</h1>
    <span style="font-weight:bold; color:#09F;">Record ID:
      <?php echo $orgName = @$_GET['orgName']; ?></span></span>

    <br /><br />

    <fieldset class="row1">
      <legend>Update Booking Type for This record</legend>
      <p>Booking Type&nbsp;&nbsp;</p>

      <select name="bookingType" id="bookingType" required>
        <?php

        $sql_opt =
          "SELECT distinct booking_type.title FROM booking_type
where booking_type.title NOT IN 
  (SELECT $table.title FROM $table where $table.orgName='$orgName')    ";
        $result_opt = mysqli_query($con, $sql_opt);

        $options = "";
        while ($row_opt = mysqli_fetch_array($result_opt)) {
          $code = $row_opt["title"];
          $name_opt = $row_opt["title"];
          $options .= "<OPTION value='$code'>" . $name_opt;
        }
        ?>
        <?php if (!empty($bookingType)) { ?>
          <option><?php echo $bookingType; ?></option>
        <?php } else { ?>
          <option value="">--Select Booking Type--</option>
        <?php } ?>
        <?php echo $options; ?>
        </option>
      </select>

      <div>
        <button class="button" type="submit" name="submit" style="margin-left:450px;" onclick="return formSubmit(); return false">Submit &raquo;
        </button>
      </div>
    </fieldset>

    <fieldset class="row1">
      <legend>Booking Types of a Company
      </legend>

      <table width="30%" border="1">
        <?php
        $query = "SELECT * FROM $table where orgName='$orgName'";
        $result = mysqli_query($con, $query);
        while ($row = mysqli_fetch_array($result)) { ?>
          <tr>
            <td align="left"><?php echo $row['title']; ?> </td>
            <td align="left"> <?php if ($_SESSION['prv'] == 'Management' || $_SESSION['prv'] == 'Finance') { ?>


                <a href="#" onClick="MM_openBrWindow('del.php?del_id=<?php echo $row['id']; ?>&table=<?php echo $table; ?>','_blank','scrollbars=yes,resizable=yes,width=400,height=200')"><img src="images/icn_trash.png" title="Trash" height="14" width="16" /></a><?php } ?>
            </td>
          </tr>
        <?php } ?>
      </table>

    </fieldset>
  </form>
  </div>

  <?php

  if (isset($_POST['submit'])) {
    $bookingType = $_POST['bookingType'];
    $acttObj->editFun($table, $edit_id, 'title', $bookingType);
    $acttObj->editFun($table, $edit_id, 'orgName', $orgName);
    echo "<script>alert('Successful!');</script>";
  ?>

    <script>
      window.onunload = refreshParent;

      function refreshParent() {
        window.opener.location.reload();
      }
    </script>
  <?php
  } ?>