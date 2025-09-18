<?php if (session_id() == '' || !isset($_SESSION)) {
  session_start();
}
include 'source/db.php';
include 'source/class.php';
$row = $acttObj->read_specific("*", "interpreter_reg", "id=" . $_SESSION['web_userId']);
$name = ucwords($row['name']);
$week_remarks = $row['week_remarks'];
if ($row['actnow'] == "Inactive" && $row['actnow_time'] != "1001-01-01" && $row['actnow_to'] != "1001-01-01") {
  $actnow = "Inactive";
  $actnow_time = $row['actnow_time'];
  $actnow_to = $row['actnow_to'];
  if ((date("Y-m-d") < $row['actnow_time'] || date("Y-m-d") > $row['actnow_to'])) {
    $actnow = "Active";
    $label_active = "<h4 class='text-danger'><i>You profile was set to un-available from " . $misc->dated($actnow_time) . " to " . $misc->dated($actnow_to) . " last time</i></h4>";
    $actnow_time = "";
    $actnow_to = "";
  } else {
    $label_active = "<h4 class='text-danger'><i>You profile is set to un-available from " . $misc->dated($actnow_time) . " to " . $misc->dated($actnow_to) . "</i></h4>";
  }
} else {
  $actnow = "Active";
  $actnow_time = null;
  $actnow_to = null;
  $label_active = "<h3 class='text-success'><b>You are available now <i class='glyphicon glyphicon-check'></i></b></h3>";
}
if ($row['actnow'] == "Inactive") {
  if ((date("Y-m-d") < $row['actnow_time'] || date("Y-m-d") > $row['actnow_to'])) {
    $label_current_status = "<h3 class='text-success'><span class='label label-success'><b>You are available now <i class='glyphicon glyphicon-check'></i></span></b></h3>";
  } else {
    $label_current_status = "<h3><span class='label label-danger'><b>You are not available now <i class='glyphicon glyphicon-remove'></i></span></b></h3>";
  }
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en" <head>
<title>Interpreter Availability Form</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" />
<script src="lsuk_system/js/jquery-1.11.3.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
<style>
  .form-control {
    margin-bottom: 3px;
  }
</style>
</head>

<body>

  <form action="process/update_interpreter_profile.php" method="post" class="register" enctype="multipart/form-data">
    <input type="hidden" name="actnow" value="<?= $actnow ?>" />
    <input type="hidden" name="redirect_url" value='<?= 'http' . (isset($_SERVER['HTTPS']) ? 's' : '') . '://' . "{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}" ?>' />
    <div>
      <div class="col-md-9 col-md-offset-2">
        <?php
        if ($_SESSION['returned_message']) {
          echo "<div class='row'>" . $_SESSION['returned_message'] . "</div>";
          unset($_SESSION['returned_message']);
        } ?>
        <h3 class="text-center">Schedule your unavailable dates</h3>
        <p><i>While unavailable, You will not receive new jobs. During this time you can still make your profile available again!</i></p>
        <?=$label_current_status . $label_active?>
        <table width="50%" align="center" class="table table-bordered">
          <tr class="bg-primary">
            <th colspan="2"><strong>Choose Un-availability Dates</strong></th>
          </tr>
          <?php if ($actnow == "Inactive") { ?>
            <tr title="Click if you want to make yourself as available!">
              <td width="30%"><strong>Set Back To Available</strong></td>
              <td>
                <label for="set_available" class="btn btn-info">
                  <input onchange="toggle_set_available(this)" type="checkbox" name="set_available" id="set_available"/> Click To Set Yourself Available
                </label>
              </td>
            </tr>
            <?php } ?>
          <tr>
            <td width="30%"><strong>Un-available Date From</strong></td>
            <td>
              <input class="form-control input-lg" type="date" name="actnow_time" id="actnow_time" style="width:200px;" value="<?= $actnow_time ?>" <?=$actnow_time == "" ? "required" : ""?>/>
            </td>
          </tr>
          <tr>
            <td><strong>Un-available Date To</strong></td>
            <td>
              <input class="form-control input-lg" type="date" name="actnow_to" id="actnow_to" style="width:200px;" value="<?= $actnow_to ?>" <?=$actnow_to == "" ? "required" : ""?>/>
            </td>
          </tr>
          <tr>
            <td colspan="2">
              <strong>Add a message</strong> (Optional):<br><br>
              <textarea class="form-control" name="week_remarks" rows="3" placeholder="An optional note about why are you making yourself as unavailable ..."><?= $week_remarks ?></textarea>
            </td>
          </tr>
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
      $('.form-control').attr("disabled", "disabled");
    } else {
      $('.form-control').removeAttr("disabled");
    }
  }
</script>
</html>