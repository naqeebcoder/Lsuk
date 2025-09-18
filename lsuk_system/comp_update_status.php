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
<title>Update Company Status</title>
<br />
<br />
<br />

<div align="center">
  <span style="font-weight:bold; color:#09F;">Record ID:
    <?php echo $edit_id = $_GET['edit_id']; ?>
  </span><br /><br />

  <form action="" method="post">
    Are you sure you want to amending this Record&nbsp;&nbsp;<select name="status" id="status" style="width:150px;">
      <option>Company Seized trading in this name or Company closed</option>
      <option>Company Blacklisted</option>
      <option value="">Reinstate</option>
    </select>&nbsp;&nbsp;<input type="submit" name="submit" value="Submit" />
  </form>
</div>
<?php

if (isset($_POST['submit'])) {
  $c1 = $_POST['status'];
  $acttObj->editFun("comp_reg", $edit_id, 'status', $c1);

  $table = "comp_reg";
  $acttObj->editFun($table, $edit_id, 'edited_by', $_SESSION['UserName']);
  $acttObj->editFun($table, $edit_id, 'edited_date', date("Y-m-d H:i:s"));
  $acttObj->new_old_table('hist_' . $table, $table, $edit_id);
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