<?php if (session_id() == '' || !isset($_SESSION)) {
  session_start();
}
include 'db.php';
include 'class.php';
$allowed_type_idz = "60";
//Check if user has current action allowed
if ($_SESSION['is_root'] == 0) {
  $get_page_access = $acttObj->read_specific("id", "action_permissions", "user_id=" . $_SESSION['userId'] . " AND action_id IN (" . $allowed_type_idz . ")")['id'];
  if (empty($get_page_access)) {
    die("<center><h2 class='text-center text-danger'>You do not have access to <u>Update Company Status</u> action!<br>Kindly contact admin for further process.</h2></center>");
  }
}
?>
<link rel="stylesheet" type="text/css" href="css/layout.css" />
<title>Update Company Password</title>
<br />
<br />
<br />

<div align="center">
  <span style="font-weight:bold; color:#09F;">Record ID:
    <?php echo $edit_id = $_GET['edit_id']; ?>
  </span><br /><br />

  <form id="passwordForm" method="post" action="">
    New Password:<br> <input style="margin-top:15px" type="password" id="newPassword" name="newPassword" required /><br>
    Confirm Password:<br> <input style="margin-top:15px" type="password" id="confirmPassword" name="confirmPassword" required /><br>
    <input style="margin-top:15px" type="submit" name="submit" value="Update Password" />
  </form>

<script>
  document.getElementById('passwordForm').addEventListener('submit', function(e) {
    const newPassword = document.getElementById('newPassword').value;
    const confirmPassword = document.getElementById('confirmPassword').value;

    if (newPassword !== confirmPassword) {
      alert("Passwords do not match.");
      e.preventDefault(); // Stop form submission
    }
  });
</script>
</div>
<?php

if (isset($_POST['submit'])) {
  $newPassword = $_POST['newPassword'] ?? '';
  $confirmPassword = $_POST['confirmPassword'] ?? '';

  if ($newPassword !== $confirmPassword) {
      die('Error: Passwords do not match.');
  }
  $acttObj->update(
        'company_login',
        ['paswrd' => $newPassword],
        ['company_id' => $edit_id]
    );
  $table = "comp_reg";
  // $acttObj->editFun($table, $edit_id, 'edited_by', $_SESSION['UserName']);
  // $acttObj->editFun($table, $edit_id, 'edited_date', date("Y-m-d H:i:s"));
  // $acttObj->new_old_table('hist_' . $table, $table, $edit_id);
?>

  <script>
    window.onunload = refreshParent;

    function refreshParent() {
      window.opener.location.reload();
    }
  </script>
<?php echo "<script>window.close();</script>";
}
?>

<script>
  window.onunload = refreshParent;

  function refreshParent() {
    window.opener.location.reload();
  }
</script>