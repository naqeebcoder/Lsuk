<?php if (session_id() == '' || !isset($_SESSION)) {
  session_start();
}
include 'db.php';
include 'class.php';
$allowed_type_idz = "52";
//Check if user has current action allowed
if ($_SESSION['is_root'] == 0) {
    $get_page_access = $acttObj->read_specific("id", "action_permissions", "user_id=" . $_SESSION['userId'] . " AND action_id IN (" . $allowed_type_idz . ")")['id'];
    if (empty($get_page_access)) {
        die("<center><h2 class='text-center text-danger'>You do not have access to <u>Activate/De-activate Account</u> action!<br>Kindly contact admin for further process.</h2></center>");
    }
}
$table = 'interpreter_reg';
$view_id = @$_GET['interpreter_id'];
$activate = $acttObj->unique_data($table, 'active', 'id', $view_id);
?>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
  <title>Activate/De-Activate Interpreter</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="css/bootstrap.css">
  <style>
    .b {
      color: #fff;
    }

    a:link,
    a:visited {
      color: #337ab7;
    }
  </style>
</head>

<body>
  <br />
  <script type="text/javascript">
    function MM_openBrWindow(theURL, winName, features) {
      window.open(theURL, winName, features);
    }
  </script>
  </head>

  <body>
    <form action="#" method="post">

      <center>
        <h1>Status of Interpreter: <?php echo $activate == '0' ? '<span class="label label-success"><b>Active</b></span>' : '<span class="label label-danger"><b>Not Active</b></span>'; ?></h1><br />
        <fieldset class="row1"><br>
          <h3>Are you sure to change to <span> <?php echo $activate == '1' ? '<span class="text-success"><b>Activate</b></span>' : '<span class="text-danger"><b>De Activate</b></span>'; ?></span> ?</h3>
          <input type="submit" name="active" value="Update Status" class="btn btn-primary" />
        </fieldset>
      </center>
    </form>
  </body>
  <?php if (isset($_POST['active'])) {
    $return_status = $activate == '0' ? '1' : '0';
    if ($acttObj->editFun($table, $view_id, 'active', $return_status)) {
      $acttObj->editFun($table, $view_id, 'edited_by', $_SESSION['UserName']);
      $acttObj->editFun($table, $view_id, 'edited_date', date("Y-m-d H:i:s"));
      // $acttObj->new_old_table('hist_' . $table, $table, $view_id); ?>
      <script type="text/javascript">
        var on_hold = '<?php echo $activate == "1" ? "(Activated)" : "(De Activated)"; ?>';
        alert('Interpreter account has been ' + on_hold + '.');
        window.close();
        window.onunload = refreshParent;

        function refreshParent() {
          window.opener.location.reload();
        }
      </script>
  <?php }
  } ?>

</html>