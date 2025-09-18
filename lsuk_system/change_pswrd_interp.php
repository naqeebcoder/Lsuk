<?php if (session_id() == '' || !isset($_SESSION)) {
  session_start();
}
include 'db.php';
include 'class.php';
$allowed_type_idz = "48";
//Check if user has current action allowed
if ($_SESSION['is_root'] == 0) {
    $get_page_access = $acttObj->read_specific("id", "action_permissions", "user_id=" . $_SESSION['userId'] . " AND action_id IN (" . $allowed_type_idz . ")")['id'];
    if (empty($get_page_access)) {
        die("<center><h2 class='text-center text-danger'>You do not have access to <u>Change Interpreter Password</u> action!<br>Kindly contact admin for further process.</h2></center>");
    }
}
$table = 'interpreter_reg';
$edit_id = $_GET['ref_frn_key'];
$get_password = $acttObj->read_specific("password", $table, "id=" . $edit_id)['password']; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
  <title>Change Password</title>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="css/bootstrap.css">
</head>

<body class="container">
  <form action="" method="post" class="register" id="signup_form" name="signup_form" onsubmit="return formSubmit()">
    <h1>Change Password of <span class="label label-primary"><?php echo @$_GET['name']; ?></span></h1>
    <div class="row">
      <div class="form-group col-sm-12">
        <h3>Interpreter Current Password =<span class="label label-danger pull-right"><?= $get_password ?></span></h3>
      </div>
      <div class="form-group col-sm-6">
        <label>Password * </label>
        <input class="form-control" name="pass" type="password" placeholder='' required='' id="pass" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{6,}" onchange="form.repass.pattern = this.value;" />
      </div>
      <div class="form-group col-sm-6">
        <label>Confirm Password * </label>
        <input class="form-control" name="repass" type="password" placeholder='' required='' id="repass" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{6,}" />
      </div>
      <div class="form-group col-sm-6">
        <button class="btn btn-primary" type="submit" name="submit" onclick="return formSubmit(); return false">Submit &raquo;</button>
      </div>
    </div>
  </form>
</body>

</html>

<?php
if (isset($_POST['submit'])) {
  $password = $_POST['pass'];
  $acttObj->editFun($table, $edit_id, 'password', $password);
  $acttObj->editFun($table, $edit_id, 'edited_by', $_SESSION['UserName']);
  $acttObj->editFun($table, $edit_id, 'edited_date', date("Y-m-d H:i:s"));
  $acttObj->new_old_table('hist_' . $table, $table, $edit_id); ?>
  <script>
    alert('Password successfully changed!');
    window.onunload = refreshParent;

    function refreshParent() {
      window.opener.location.reload();
    }
  </script>
<?php }
?>