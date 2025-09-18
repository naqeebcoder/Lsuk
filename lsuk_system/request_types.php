<?php
include "userhaspage.php";
SysPermiss::UserHasPage(__FILE__);

if (session_id() == '' || !isset($_SESSION)) {
  session_start();
}

include 'actions.php';
$table = 'loan_dropdowns';
$array_types = array(0 => "Non Payable", 1 => "Payable");
$array_types_colors = array(0 => "<span class='label label-success pull-right'>Non Payable</span>", 1 => "<span class='label label-warning pull-right'>Payable</span>");

if (isset($_POST['btn_insert_request_type'])) {
  $check_existing = $obj->read_specific('id', $table, "title='" . $_POST['title'] . "' AND is_payable='" . $_POST['is_payable'] . "'")['id'];
  if (empty($check_existing)) {
    $is_payable = $_POST['is_payable'] ?: 0;
    $done = $obj->insert($table, array("title" => trim($_POST['title']), "is_payable" => $_POST['is_payable'], "created_by" => $_SESSION['userId'], "created_date" => date('Y-m-d H:i:s')));
    if ($done) {
      $msg = "<div class='alert alert-success'>New request type has been added.</div>";
    } else {
      $msg = "<div class='alert alert-danger'>Failed to add New request type: " . trim($_POST['title']) . "!</div>";
    }
  } else {
    $msg = "<div class='alert alert-danger'>Same request type : <b>" . trim($_POST['title']) . "</b> already exists! Try different one</div>";
  }
}
if (isset($_POST['btn_update_request_type'])) {
  $ok = 1;
  $is_payable = $_POST['is_payable'] ?: 0;
  $check_existing = $obj->read_specific('id', $table, "title='" . $_POST['title'] . "' AND is_payable='" . $_POST['is_payable'] . "'")['id'];
  if (!empty($check_existing)) {
    if ($check_existing != $_POST['id']) {
      $ok = 0;
      $msg = "<div class='alert alert-danger'>Same request type already exists! Try different one</div>";
    }
  }
  if ($ok == 1) {
    $update_array = array("title" => trim($_POST['title']), "is_payable" => $_POST['is_payable'], "updated_by" => $_SESSION['userId'], "updated_date" => date('Y-m-d H:i:s'));
    $update_array['deleted_flag'] = $_POST['deleted_flag'] ?: 0;
    if ($_POST['deleted_flag'] == 1) {
      $update_array['deleted_by'] = $_SESSION['userId'];
      $update_array['deleted_date'] = date('Y-m-d H:i:s');
    }
    $done = $obj->update($table, $update_array, "id=" . $_POST['id']);
    if ($done) {
      $msg = "<div class='alert alert-success'>Request type has been updated successfully.</div>";
    } else {
      $msg = "<div class='alert alert-danger'>Failed to update this request type! Please try again</div>";
    }
  }
}
if (isset($_GET['id'])) {
  $get_type = $obj->read_specific("*", $table, "id=" . $_GET['id']);
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
  <title>Update Request Types</title>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" />
  <style>
    .label {
      font-size: 100% !important;
    }
  </style>
  <script type="text/javascript">
    function MM_openBrWindow(theURL, winName, features) { //v2.0
      window.open(theURL, winName, features);
    }
  </script>
</head>

<body class="container-fluid">
  <form action="" method="post" class="register" id="signup_form" name="signup_form" onsubmit="return formSubmit()">
    <?php if (isset($_GET['id'])) { ?>
      <input type="hidden" name="id" value="<?= $_GET['id'] ?>" />
    <?php } ?>
    <h3>Money Request Types Information</h3>
    <fieldset class="row">
      <div class="form-group col-sm-12">
        <?= !empty($msg) ? $msg : '' ?>
      </div>
      <div class="form-group col-md-4 col-sm-6">
        <label>Request Type Title * </label>
        <input name="title" type="text" placeholder="Write Request Type Title" class="form-control" required id="title" value="<?= $get_type['title'] ?>" />
      </div>
      <?php if (!empty($get_type['id'])) {
        $check_data = $obj->read_specific("count(*) as counter", "loan_requests", "type_id=" . $get_type['id'])['counter'];
       } ?>
      <div class="form-group col-md-2 col-sm-6">
        <label>Payable Deduction Mode * </label>
        <select name="is_payable" class="form-control" required <?= $check_data > 0 ? 'disabled readonly title="There are money requests already added. Cannot update payable mode!"' : '' ?>>
          <option <?= $get_type['is_payable'] == 0 ? 'selected' : '' ?> value="0" style="color:green;">Non Payable</option>
          <option <?= $get_type['is_payable'] == 1 ? 'selected' : '' ?> value="1" style="color:red;">Payable</option>
        </select>
      </div>
      <?php if (!empty($get_type['id'])) { ?>
        <div class="form-group col-md-2 col-sm-6">
          <label>Select Status</label>
          <select name="deleted_flag" class="form-control" required>
            <option <?= $get_type['deleted_flag'] == 0 ? 'selected' : '' ?> value="0" style="color:green;">Active</option>
            <option <?= $get_type['deleted_flag'] == 1 ? 'selected' : '' ?> value="1" style="color:red;">Trashed</option>
          </select>
        </div>
      <?php } ?>
      <div class="form-group col-md-3 col-sm-6"><br>
        <?php if (empty($get_type['id'])) { ?>
          <button class="btn btn-primary" type="submit" name="btn_insert_request_type" onclick="return formSubmit(); return false">Add Request Type &raquo;</button>
        <?php } else { ?>
          <button class="btn btn-primary" type="submit" name="btn_update_request_type" onclick="return formSubmit(); return false">Update Request Type &raquo;</button>
          <a class="btn btn-warning" href="request_types.php">Cancel</a>
        <?php } ?>
      </div>
    </fieldset>
    <fieldset class="row1">
      <h4>All Request Types List</h4>
      <table class="table table-bordered">
        <thead class="bg-info">
          <th>Request Type ID</th>
          <th>Title</th>
          <th>Payment Mode</th>
          <th>Dated</th>
          <th>Action</th>
        </thead>
        <?php $result = $obj->read_all("*", $table, "1");
        $counter = 1;
        while ($row = $result->fetch_assoc()) { ?>
          <tr>
            <td align="left"><b><?php echo $counter++; ?></b></td>
            <td align="left"><?php echo $row['title'] . ($row['deleted_flag'] == 1 ? "<span class='label label-danger pull-right'>Trashed</span>" : ""); ?> </td>
            <td align="left"><?php echo $array_types_colors[$row['is_payable']]; ?> </td>
            <td align="left"><?php echo $misc->dated($row['created_date']); ?> </td>
            <td align="left">
              <a href="?id=<?= $row['id'] ?>" title="Edit this request type" class="btn btn-sm btn-info"><i class="glyphicon glyphicon-edit"></i></a>
            </td>
          </tr>
        <?php } ?>
      </table>
    </fieldset>
  </form>
</body>
<script src="js/jquery-1.11.3.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.20/css/dataTables.bootstrap.min.css" />
<script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.20/js/dataTables.bootstrap.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
<script>
  $(document).ready(function() {
    $('.table').DataTable({
      "bSort": true,
      "order": []
    });
    $('#title').keyup(function() {
      var inputVal = $(this).val();
      inputVal = inputVal.replace(/[^a-zA-Z\s]/g, '');
      $(this).val(inputVal);
    });
  });
</script>

</html>