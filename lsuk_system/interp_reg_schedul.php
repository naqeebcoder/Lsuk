<?php if (session_id() == '' || !isset($_SESSION)) {
  session_start();
}
include 'actions.php';
$allowed_type_idz = "43,205,223";
//Check if user has current action allowed
if ($_SESSION['is_root'] == 0) {
  if (!empty($allowed_type_idz)) {
      $data = $obj->read_specific("id", "action_permissions", "user_id=" . $_SESSION['userId'] . " AND action_id IN (" . $allowed_type_idz . ")");
      $get_page_access = !empty($data['id']) ? $data['id'] : null;
  } else {
      $get_page_access = null;
  }
  if (empty($get_page_access)) {
    die("<center><h2 class='text-center text-danger'>You do not have access to <u>Update Availability</u> action for jobs!<br>Kindly contact admin for further process.</h2></center>");
  }
}
$name = ucwords($_GET['name']);
$interpreter_id = @$_GET['edit_id'];
$row = $obj->read_specific("*", "interpreter_reg", "id=" . $interpreter_id);
$monday = $row['monday'];
$monday_time = $row['monday_time'];
$monday_to = $row['monday_to'];
$tuesday = $row['tuesday'];
$tuesday_time = $row['tuesday_time'];
$tuesday_to = $row['tuesday_to'];
$wednesday = $row['wednesday'];
$wednesday_time = $row['wednesday_time'];
$wednesday_to = $row['wednesday_to'];
$thursday = $row['thursday'];
$thursday_time = $row['thursday_time'];
$thursday_to = $row['thursday_to'];
$friday = $row['friday'];
$friday_time = $row['friday_time'];
$friday_to = $row['friday_to'];
$saturday = $row['saturday'];
$saturday_time = $row['saturday_time'];
$saturday_to = $row['saturday_to'];
$sunday = $row['sunday'];
$sunday_time = $row['sunday_time'];
$sunday_to = $row['sunday_to'];
$week_remarks = $row['week_remarks'];
$actnow = $row['actnow'];
$actnow_time = $row['actnow_time'];
$actnow_to = $row['actnow_to'];
if ($row['actnow'] == "Inactive" && $row['actnow_time'] != "1001-01-01" && $row['actnow_to'] != "1001-01-01") {
  $actnow = "Inactive";
  $actnow_time = $row['actnow_time'];
  $actnow_to = $row['actnow_to'];
  if ((date("Y-m-d") < $row['actnow_time'] || date("Y-m-d") > $row['actnow_to'])) {
    $actnow = "Active";
    $label_active = "<h4 class='text-danger'><i>" . $name . " was un-available from " . $misc->dated($actnow_time) . " to " . $misc->dated($actnow_to) . " last time</i></h4>";
    $actnow_time = "";
    $actnow_to = "";
  } else {
    $label_active = "<h4 class='text-danger'><i>" . $name . " will be un-available from " . $misc->dated($actnow_time) . " to " . $misc->dated($actnow_to) . "</i></h4>"; 
  }
} else {
  $actnow = "Active";
  $actnow_time = null;
  $actnow_to = null;
  $label_active = "<h3 class='text-success'><b>" . $name . " is available now <i class='glyphicon glyphicon-check'></i></b></h3>";
}
if ($row['actnow'] == "Inactive") {
  if ((date("Y-m-d") < $row['actnow_time'] || date("Y-m-d") > $row['actnow_to'])) {
    $label_current_status = "<h3 class='text-success'><span class='label label-success'><b>" . $name . " is available now <i class='glyphicon glyphicon-check'></i></span></b></h3>";
  } else {
    $label_current_status = "<h3><span class='label label-danger'><b>" . $name . " is not available now <i class='glyphicon glyphicon-remove'></i></span></b></h3>";
  }
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en" <head>
<title>Availability Form of Interpreter</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" />
<script src="js/jquery-1.11.3.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
<style>
  .form-control {
    margin-bottom: 3px;
  }
</style>
</head>

<body>

  <form action="process/update_interpreter_profile.php" method="post" class="register" enctype="multipart/form-data">
    <input type="hidden" name="interpreter_id" value="<?= $interpreter_id ?>" />
    <input type="hidden" name="actnow" value="<?= $actnow ?>" />
    <input type="hidden" name="redirect_url" value='<?= 'http' . (isset($_SERVER['HTTPS']) ? 's' : '') . '://' . "{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}" ?>' />
    <div>
      <div class="col-md-8 col-md-offset-2">
        <?php
        if ($_SESSION['returned_message']) {
          echo $_SESSION['returned_message'];
          unset($_SESSION['returned_message']);
        } ?>
        <h3 class="text-center">Schedule unavailable dates for (<span class="text-danger"><?php echo $name; ?></span>)</h3>
        <p><i>While unavailable, interpreter will not receive new jobs. During this time interpreter can still make his/her profile available again!</i></p>
          <?php
          $has_permission = ($_SESSION['is_root'] != 0);

          if (!$has_permission && !empty($allowed_type_idz)) {
              $permission_check = $obj->read_specific("id", "action_permissions", "user_id=" . $_SESSION['userId'] . " AND action_id=43");
              $has_permission = !empty($permission_check['id']);
          }
          ?>
          <span <?= !$has_permission ? 'style="display:none;"' : '' ?> ><?= $label_current_status . $label_active ?></span>
          <table width="50%" align="center" class="table table-bordered" <?= !$has_permission ? 'style="display:none;"' : '' ?>>
            <tr class="bg-primary">
              <th colspan="2"><strong>Choose Un-availability Dates for <?= $name ?></strong></th>
            </tr>
            <?php if ($actnow == "Inactive") { ?>
              <tr title="Click if you want to make this Interpreter as available!">
                <td colspan="2" width="30%">
                  <label for="set_available" class="btn btn-info">
                    <input onchange="toggle_set_available(this)" type="checkbox" name="set_available" id="set_available" /> Click To Set Interpreter Available
                  </label>
                </td>
              </tr>
            <?php } ?>
            <tr>
              <td width="30%">
                <strong>Un-available Date From</strong>
                <input class="form-control date_field" type="date" name="actnow_time" id="actnow_time" style="width:200px;" value="<?= $actnow_time ?>" />
              </td>
              <td width="30%">
                <strong>Un-available Date To</strong>
                <input class="form-control date_field" type="date" name="actnow_to" id="actnow_to" style="width:200px;" value="<?= $actnow_to ?>" />
              </td>
            </tr>
          </table>
        <?php
          $has_permission = ($_SESSION['is_root'] != 0);

          if (!$has_permission && !empty($allowed_type_idz)) {
              $permission_check = $obj->read_specific("id", "action_permissions", "user_id=" . $_SESSION['userId'] . " AND action_id=217");
              $has_permission = !empty($permission_check['id']);
          }
          ?>
        <table width="70%" align="center" class="table table-bordered" <?= !$has_permission ? 'style="display:none;"' : '' ?>>
          <tr class="bg-primary">
            <th colspan="4"><strong>Choose Daily working schedule for <?= $name ?></strong></th>
          </tr>
          <tr class="bg-info">
            <th><strong>Day</strong></th>
            <th><strong>Availability Status</strong></th>
            <th><strong>Works From</strong></th>
            <th><strong>Works To</strong></th>
          </tr>
          <tr>
            <td><strong>Monday *</strong></td>
            <td><select class="form-control" name="monday" id="monday" style="width:165px;">
                <?php if (!empty($monday)) { ?><option><?php echo $monday; ?></option> <?php } ?>
                <option>Yes</option>
                <option>No</option>
              </select></td>
            <td>
              <input class="form-control" type="time" name="monday_time" id="monday_time" style="width:165px;" value="<?php echo @$monday_time; ?>" />
            </td>
            <td> <input class="form-control" type="time" name="monday_to" id="monday_to" style="width:165px;" value="<?php echo @$monday_to; ?>" />
            </td>
          </tr>

          <tr>
            <td><strong>Tuesday *</strong></td>
            <td><select class="form-control" name="tuesday" id="tuesday" style="width:165px;"><?php if (!empty($tuesday)) { ?><option><?php echo $tuesday; ?></option> <?php } ?><option>Yes</option>
                <option>No</option>
              </select></td>
            <td><input class="form-control" type="time" name="tuesday_time" id="tuesday_time" style="width:165px;" value="<?php echo @$tuesday_time; ?>" />
            </td>
            <td><input class="form-control" type="time" name="tuesday_to" id="tuesday_to" style="width:165px;" value="<?php echo @$tuesday_to; ?>" />
            </td>
          </tr>
          <tr>
            <td><strong>Wednesday *</strong></td>
            <td><select class="form-control" name="wednesday" id="wednesday" style="width:165px;">
                <?php if (!empty($wednesday)) { ?>
                  <option><?php echo $wednesday; ?></option>
                <?php } ?>
                <option>Yes</option>
                <option>No</option>
              </select></td>
            <td>
              <input class="form-control" type="time" name="wednesday_time" id="wednesday_time" style="width:165px;" value="<?php echo @$wednesday_time; ?>" />
            </td>
            <td><input class="form-control" type="time" name="wednesday_to" id="wednesday_to" style="width:165px;" value="<?php echo @$wednesday_to; ?>" />
            </td>
          </tr>
          <tr>
            <td><strong>Thursday *</strong></td>
            <td><select class="form-control" name="thursday" id="thursday" style="width:165px;">
                <?php if (!empty($thursday)) { ?>
                  <option><?php echo $thursday; ?></option>
                <?php } ?>
                <option>Yes</option>
                <option>No</option>
              </select></td>
            <td>
              <input class="form-control" type="time" name="thursday_time" id="thursday_time" style="width:165px;" value="<?php echo @$thursday_time; ?>" />
            </td>
            <td><input class="form-control" type="time" name="thursday_to" id="thursday_to" style="width:165px;" value="<?php echo @$thursday_to; ?>" />
            </td>
          </tr>
          <tr>
            <td><strong>Friday * </strong></td>
            <td><select class="form-control" name="friday" id="friday" style="width:165px;">
                <?php if (!empty($friday)) { ?>
                  <option><?php echo $friday; ?></option>
                <?php } ?>
                <option>Yes</option>
                <option>No</option>
              </select></td>
            <td>
              <input class="form-control" type="time" name="friday_time" id="friday_time" style="width:165px;" value="<?php echo @$friday_time; ?>" />
            </td>
            <td><input class="form-control" type="time" name="friday_to" id="friday_to" style="width:165px;" value="<?php echo @$friday_to; ?>" /></td>
          </tr>
          <tr>
            <td><strong>Weekend *</strong></td>
            <td><select class="form-control" name="saturday" id="saturday" style="width:165px;">
                <?php if (!empty($saturday)) { ?>
                  <option><?php echo $saturday; ?></option>
                <?php } ?>
                <option>Yes</option>
                <option>No</option>
              </select></td>
            <td>
              <input class="form-control" type="time" name="saturday_time" id="saturday_time" style="width:165px;" value="<?php echo @$saturday_time; ?>" />
            </td>
            <td><input class="form-control" type="time" name="saturday_to" id="saturday_to" style="width:165px;" value="<?php echo @$saturday_to; ?>" />
            </td>
          </tr>
          <tr>
            <td><strong>Out Of Hours *</strong></td>
            <td>
              <select class="form-control" name="sunday" id="sunday" style="width:165px;">
                <?php if (!empty($sunday)) { ?>
                  <option><?php echo $sunday; ?></option>
                <?php } ?>
                <option>Yes</option>
                <option>No</option>
              </select>
            </td>
            <td>
              <input class="form-control" type="time" name="sunday_time" id="sunday_time" style="width:165px;" value="<?php echo @$sunday_time; ?>" />
            </td>
            <td><input class="form-control" type="time" name="sunday_to" id="sunday_to" style="width:165px;" value="<?php echo @$sunday_to; ?>" />
            </td>
          </tr>
          
        </table>
        <?php
          $has_permission = ($_SESSION['is_root'] != 0);

          if (!$has_permission && !empty($allowed_type_idz)) {
              $permission_check = $obj->read_specific("id", "action_permissions", "user_id=" . $_SESSION['userId'] . " AND action_id=205");
              $has_permission = !empty($permission_check['id']);
          }
          ?>
        <table <?= !$has_permission ? 'style="display:none;"' : '' ?>>
          <tr>
              <td colspan="4">
                <strong>Add a message/Remarks</strong> (Optional):<br>
                <textarea class="form-control" name="week_remarks" rows="3" placeholder="An optional note about this interpreter schedule ..."><?= $week_remarks ?></textarea>
              </td>
            </tr>
        </table>
        <table>
            <tr>
              <td colspan="2"><button class="btn btn-primary" class="button" type="submit" name="update_availability">Update Schedule &raquo;</button></td>
            </tr>
        </table>
      </div>
    </div>
  </form>
</body>
<script>
  function toggle_set_available(element) {
    if ($(element).is(":checked")) {
      $('.date_field').attr("disabled", "disabled");
    } else {
      $('.date_field').removeAttr("disabled");
    }
  }
</script>
<script>
document.addEventListener("DOMContentLoaded", function () {
    const fromInput = document.getElementById("actnow_time");
    const toInput = document.getElementById("actnow_to");

    const today = new Date().toISOString().split("T")[0];
    fromInput.min = today;
    toInput.min = today;

    fromInput.addEventListener("change", function () {
        toInput.min = fromInput.value;
        if (toInput.value < fromInput.value) {
            toInput.value = fromInput.value;
        }
    });

    toInput.addEventListener("change", function () {
        if (toInput.value < fromInput.value) {
            toInput.value = fromInput.value;
        }
    });
});
</script>

</html>