<?php 
include '../source/setup_email.php';
if (session_id() == '' || !isset($_SESSION)) {
  session_start();
}

include 'actions.php';
$allowed_type_idz = "203";
//Check if user has current action allowed
if ($_SESSION['is_root'] == 0) {
  $get_page_access = $obj->read_specific("id", "action_permissions", "user_id=" . $_SESSION['userId'] . " AND action_id IN (" . $allowed_type_idz . ")")['id'];
  if (empty($get_page_access)) {
    die("<center><h2 class='text-center text-danger'>You do not have access to <u>Send Invites</u> action!<br>Kindly contact admin for further process.</h2></center>");
  }
}
$table = 'send_invites';
$interpreter_id = $_GET['interpreter_id'];
$array_response_status = array(0 => "Invite Sent", 1 => "Invite Accepted", 2 => "Invite Rejected");
$array_response_status_colors = array(0 => "<span class='label label-warning'>Invite Sent</span>", 1 => "<span class='label label-success'>Invite Accepted</span>", 2 => "<span class='label label-danger'>Invite Rejected</span>");

if (isset($_POST['btn_insert_send_invite'])) {
  $invite_date = $_POST['invite_date'] ?: date("Y-m-d");
  $invite_time = $_POST['invite_time'] ?: date("H:i:s");
  $response_status = $_POST['response_status'] ?: 1;
  $invite_type = $_POST['invite_type'] ?: 1;
  $get_interpreter_data = $obj->read_specific("name,email", "interpreter_reg", "id=" . $interpreter_id);
  if ($get_interpreter_data['email']) {
    if (isset($_POST['notify_interpreter'])) {
      try {
        $meeting_details = "<b>Meeting Date:</b> " . $misc->dated($invite_date) . "<br><b>Meeting Time:</b> " . $invite_time;
        if ($_POST['meeting_notes']) {
          $meeting_details .= "<br><b>Meeting Notes:</b><br></i>" . nl2br($_POST['meeting_notes']) . "</i>";
        }
        $row_format_update = $obj->read_specific("em_format", "email_format", "id=48");
        $data_replace   = ["[INTERPRETER]", "[MEETING_DETAILS]", "[LSUK_ADMIN_TEAM]"];
        $to_replace  = [ucwords($get_interpreter_data['name']), $meeting_details, $_SESSION['UserName']];
        $message_body = str_replace($data_replace, $to_replace, $row_format_update['em_format']);
        $mail->SMTPDebug = 0;
        $mail->isSMTP();
        $mail->Host = setupEmail::EMAIL_HOST;
        $mail->SMTPAuth   = true;
        $mail->Username   = setupEmail::HR_EMAIL;
        $mail->Password   = setupEmail::HR_PASSWORD;
        $mail->SMTPSecure = setupEmail::SECURE_TYPE;
        $mail->Port       = setupEmail::SENDING_PORT;
        $mail->setFrom(setupEmail::HR_EMAIL, setupEmail::FROM_NAME);
        $mail->addAddress($get_interpreter_data['email']);
        $mail->addReplyTo(setupEmail::HR_EMAIL, setupEmail::FROM_NAME);
        $mail->isHTML(true);
        $mail->Subject = "LSUK " . ($invite_type == 1 ? "Induction" : "Assessment") . " Invite";
        $mail->Body    = $message_body;
        if ($mail->send()) {
          $mail->ClearAllRecipients();
          $is_int_notified = " Email invite has been sent to interpreter";
        } else {
          $is_int_notified = " Email invite could not be sent to interpreter";
        }
      } catch (Exception $e) {
        $is_int_notified = " Email invite could not be sent to interpreter due to Mailer";
      }
    }
  }
  $array_insert = array("invite_type" => $invite_type, "response_status" => $response_status, "interpreter_id" => $interpreter_id, "invite_date" => $invite_date, "invite_time" => $invite_time, "created_by" => $_SESSION['userId'], "created_date" => date('Y-m-d H:i:s'));
  if ($_POST['meeting_notes']) {
    $array_insert['meeting_notes'] = $obj->con->real_escape_string(trim($_POST['meeting_notes']));
  }
  $done = $obj->insert($table, $array_insert);
  if ($done) {
    $msg = "<div class='alert alert-success'>New invite has been sent. Thank you</div>";
  } else {
    $msg = "<div class='alert alert-danger'>Failed to add New invite! Please try again</div>";
  }
}
if (isset($_POST['btn_update_send_invite'])) {
  $invite_date = $_POST['invite_date'] ?: date("Y-m-d");
  $invite_time = $_POST['invite_time'] ?: date("H:i:s");
  $response_status = $_POST['response_status'] ?: 1;
  $invite_type = $_POST['invite_type'] ?: 1;
  $update_array = array("invite_type" => $invite_type, "response_status" => $response_status, "invite_date" => $invite_date, "invite_time" => $invite_time, "updated_by" => $_SESSION['userId'], "updated_date" => date('Y-m-d H:i:s'));
  if ($_POST['meeting_notes']) {
    $update_array['meeting_notes'] = trim($_POST['meeting_notes']);
  }
  if ($_POST['response_date']) {
    $update_array['response_date'] = $_POST['response_date'];
  }
  $update_array['deleted_flag'] = $_POST['deleted_flag'] ?: 0;
  if ($_POST['deleted_flag'] == 1) {
    $update_array['deleted_by'] = $_SESSION['userId'];
    $update_array['deleted_date'] = date('Y-m-d H:i:s');
  }
  $done = $obj->update($table, $update_array, "id=" . $_POST['id']);
  if ($done) {
    $msg = "<div class='alert alert-success'>Invite has been updated successfully.</div>";
  } else {
    $msg = "<div class='alert alert-danger'>Failed to update this Invite! Please try again</div>";
  }
}
if (isset($_GET['id'])) {
  $get_data = $obj->read_specific("*", $table, "id=" . $_GET['id']);
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
  <title>Update Interpreter Invites</title>
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
  <form action="" method="post" class="register">
    <?php if (isset($_GET['id'])) { ?>
      <input type="hidden" name="id" value="<?= $_GET['id'] ?>" />
    <?php } ?>
    <h3>Interpreter Invites Information</h3>
    <fieldset class="row">
      <div class="form-group col-sm-12">
        <?= !empty($msg) ? $msg : '' ?>
      </div>
      <div class="form-group col-sm-2">
        <label>Meeting Invite Date * </label>
        <input name="invite_date" type="date" class="form-control" required id="invite_date" value="<?= $get_data['invite_date'] ?>" />
      </div>
      <div class="form-group col-sm-2">
        <label>Meeting Invite Time * </label>
        <input name="invite_time" type="time" class="form-control" required id="invite_time" value="<?= $get_data['invite_time'] ?>" />
      </div>
      <div class="form-group col-sm-2">
        <label for="invite_type">Select Invite Type </label>
        <select name="invite_type" id="invite_type" class="form-control" required>
          <option <?= $get_data['invite_type'] == 1 ? 'selected' : '' ?> value="0">Induction Invite</option>
          <option <?= $get_data['invite_type'] == 2 ? 'selected' : '' ?> value="1">Assessment Invite</option>
        </select>
      </div>
      <div class="form-group col-sm-3">
        <label for="response_status">Invite Response Status</label>
        <select name="response_status" id="response_status" class="form-control" required>
          <option <?= $get_data['response_status'] == 0 ? 'selected' : '' ?> value="0">Send Invite</option>
          <option <?= $get_data['response_status'] == 1 ? 'selected' : '' ?> value="1" style="color:green;">Invite Accepted</option>
          <option <?= $get_data['response_status'] == 2 ? 'selected' : '' ?> value="2" style="color:red;">Invite Rejected</option>
        </select>
      </div>
      <?php if (!empty($get_data['id'])) { ?>
        <div class="form-group col-sm-3">
          <label>Select Status</label>
          <select name="deleted_flag" class="form-control" required>
            <option <?= $get_data['deleted_flag'] == 0 ? 'selected' : '' ?> value="0" style="color:green;">Active</option>
            <option <?= $get_data['deleted_flag'] == 1 ? 'selected' : '' ?> value="1" style="color:red;">Trashed</option>
          </select>
        </div>
      <?php } ?>
      <div class="form-group col-md-9">
        <label for="meeting_notes">Meeting Notes For Interpreter</label>
        <textarea rows="5" maxlength="250" placeholder="Write meeting notes ..." class="form-control" name="meeting_notes" id="meeting_notes"><?= $get_data['meeting_notes'] ?></textarea>
      </div>
      <?php if (!empty($get_data['id'])) { ?>
        <div class="form-group col-sm-3 text-danger">
          <label>Interpreter Response Date </label>
          <input name="response_date" type="date" class="form-control" id="response_date" value="<?= $get_data['response_date'] ?>" />
        </div>
      <?php } ?>
      <div class="form-group col-sm-12">
        <?php if (empty($get_data['id'])) { ?>
          <label class="btn btn-default btn-sm" for="notify_interpreter"><input type="checkbox" value="1" name="notify_interpreter" id="notify_interpreter"> Notify Interpreter on email</label>
          <br><br>
          <button class="btn btn-primary" type="submit" name="btn_insert_send_invite">Send new Invite &raquo;</button>
        <?php } else { ?>
          <button class="btn btn-primary" type="submit" name="btn_update_send_invite">Update Invite &raquo;</button>
          <a class="btn btn-warning" href="send_invites.php?interpreter_id=<?=$interpreter_id?>">Cancel</a>
        <?php } ?>
      </div>
    </fieldset>
    <fieldset class="row1">
      <h4>All Meeting Invites List</h4>
      <table class="table table-bordered">
        <thead class="bg-info">
          <th>S.No</th>
          <th>Invite Date</th>
          <th>Invite Time</th>
          <th>Interpreter Response</th>
          <th>Invite Type</th>
          <th>Action</th>
        </thead>
        <?php $result = $obj->read_all("$table.*,login.name", "$table,login", "$table.created_by=login.id");
        $counter = 1;
        while ($row = $result->fetch_assoc()) { ?>
          <tr>
            <td align="center"><b><?php echo $counter++  . ($row['deleted_flag'] == 1 ? "<span class='label label-danger pull-right'>Trashed</span>" : ""); ?></b></td>
            <td align="center"><?php echo $misc->dated($row['invite_date']) . "<br><small>By " . ucwords($row['name']) . "</small>"; ?> </td>
            <td align="center"><?= $row['invite_time']; ?></td>
            <td><?php echo $array_response_status_colors[$row['response_status']] . "<span class='pull-right'>Response Date:" . $misc->dated($row['response_date']) . "</span>"; ?> </td>
            <td><?=$row['invite_type'] == 1 ? "<span class='label label-success'>Induction Invite</span>" : "<span class='label label-primary'>Assessment Invite</span>"?></td>
            <td align="center">
              <a href="?interpreter_id=<?= $interpreter_id ?>&id=<?= $row['id'] ?>" title="Edit this invite" class="btn btn-sm btn-info"><i class="glyphicon glyphicon-edit"></i></a>
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
  });
</script>

</html>