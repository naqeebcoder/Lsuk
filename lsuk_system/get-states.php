<?php include("db.php");
if (isset($_POST['o_id'])) {
  $strJobTab = "";
  if (isset($_POST['jobtab']))
    $strJobTab = $_POST['jobtab'];

  $abrc = mysqli_real_escape_string($con, $_POST['o_id']);
  $sql = "select * from `booking_fixed` where `orgName`='$abrc'";
  $res = mysqli_query($con, $sql);
  if (mysqli_num_rows($res) > 0) {
    $strTitlesIn = "";
    while ($row = mysqli_fetch_object($res)) {
      if ($strTitlesIn <> "")
        $strTitlesIn .= ",";
      $strTitlesIn .= "'" . $row->title . "'";
    }

    $sql = "select * from booking_fixed INNER join booking_type ON booking_fixed.title=booking_type.title where orgname	='$abrc' and type='$strJobTab'";
    $res = mysqli_query($con, $sql);
    if (mysqli_num_rows($res) > 0) {
      echo "<option value='all'>------- Select --------</option>";
      while ($row = mysqli_fetch_object($res)) {
        echo "<option value='" . $row->ratecat . "'>" . $row->title . "</option>";
      }
    }
  }
} else {
  header('location: ./');
}
?>