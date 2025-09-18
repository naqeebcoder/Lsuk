<?php session_start();
include 'actions.php';
$table = 'login';
$edit_id = @$_GET['edit_id'];

if (isset($_POST['submit'])) {
  $array_update = array("name" => $_POST['name'], "email" => $_POST['email'], "pass" => $_POST['pass'],
  "pasport" => $_POST['pasport'], "prv" => $_POST['prv'], "user_status" => $_POST['user_status']);
  $array_update['is_allocation_member'] = isset($_POST['is_allocation_member']) ? 1 : 0;
  $obj->update("login", $array_update, "id=" . $edit_id);
  //Update work timings
  $check_timing = $obj->read_specific("count(*) as counter", "users_timings", "user_id=" . $edit_id)['counter'];
  $array_timing = array('user_id' => $edit_id);
  if ($_POST['monday']) {
    $array_timing['monday'] = 1;
    $array_timing['monday_time'] = $_POST['monday_time'];
    $array_timing['monday_to'] = $_POST['monday_to'];
  } else {
    $array_timing['monday'] = 0;
  }
  if ($_POST['tuesday']) {
    $array_timing['tuesday'] = 1;
    $array_timing['tuesday_time'] = $_POST['tuesday_time'];
    $array_timing['tuesday_to'] = $_POST['tuesday_to'];
  } else {
    $array_timing['tuesday'] = 0;
  }
  if ($_POST['wednesday']) {
    $array_timing['wednesday'] = 1;
    $array_timing['wednesday_time'] = $_POST['wednesday_time'];
    $array_timing['wednesday_to'] = $_POST['wednesday_to'];
  } else {
    $array_timing['wednesday'] = 0;
  }
  if ($_POST['thursday']) {
    $array_timing['thursday'] = 1;
    $array_timing['thursday_time'] = $_POST['thursday_time'];
    $array_timing['thursday_to'] = $_POST['thursday_to'];
  } else {
    $array_timing['thursday'] = 0;
  }
  if ($_POST['friday']) {
    $array_timing['friday'] = 1;
    $array_timing['friday_time'] = $_POST['friday_time'];
    $array_timing['friday_to'] = $_POST['friday_to'];
  } else {
    $array_timing['friday'] = 0;
  }
  if ($_POST['saturday']) {
    $array_timing['saturday'] = 1;
    $array_timing['saturday_time'] = $_POST['saturday_time'];
    $array_timing['saturday_to'] = $_POST['saturday_to'];
  } else {
    $array_timing['saturday'] = 0;
  }
  if ($_POST['sunday']) {
    $array_timing['sunday'] = 1;
    $array_timing['sunday_time'] = $_POST['sunday_time'];
    $array_timing['sunday_to'] = $_POST['sunday_to'];
  } else {
    $array_timing['sunday'] = 0;
  }
  if ($check_timing == 0) {
    $obj->insert("users_timings", $array_timing);
  } else {
    $obj->update("users_timings", $array_timing, "user_id=" . $edit_id);
  }
  $roleid = $_POST['prv'] == 'Operator' ? '1832' : '1833';
  $counter = $obj->read_specific("count(userid) as counter", "userrole", "userid=" . $edit_id)['counter'];
  if ($counter == 0) {
    $obj->insert("userrole", array('userid' => $edit_id, 'roleid' => $roleid, 'dated' => date('Y-m-d')));
  } else {
    $obj->update("userrole", array('roleid' => $roleid, 'dated' => date('Y-m-d')), array('userid' => $edit_id));
  }
}

$row = $obj->read_specific("*", $table, "id=" . $edit_id);
$name = $row['name'];
$email = $row['email'];
$pass = $row['pass'];
$pasport = $row['pasport'];
$prv = $row['prv'];
$Temp = $row['Temp'];
$user_status = $row['user_status'];
$timing = $obj->read_specific("*", "users_timings", "user_id=" . $edit_id);
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
  <title>Update User Account</title>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" />
  <?php include 'ajax_uniq_fun.php'; ?>
</head>

<body>
  <div class="container">
    <form action="" method="post" class="register" id="signup_form" name="signup_form">
      <br>
      <div class="bg-info col-xs-12 form-group">
        <h4>Update User Account Details</h4>
      </div>
      <div class="form-group col-md-4 col-sm-6">
        <label>Name * </label>
        <input name="name" type="text" placeholder='' required='' id="name" value="<?php echo $name; ?>" class="form-control valid" />
      </div>
      <div class="form-group col-md-4 col-sm-6">
        <label>Email *</label>
        <input name="email" type="text" id="unique" onBlur="uniqueFun();" value="<?php echo $email; ?>" placeholder='' required='' class="form-control" />
      </div>
      <div class="form-group col-md-4 col-sm-6">
        <label>Passport # * </label>
        <input name="pasport" type="text" placeholder='' required='' id="pasport" value="<?php echo $pasport; ?>" class="form-control" />
      </div>
      <div class="form-group col-md-4 col-sm-6">
        <label>Password * </label>
        <input name="pass" type="password" id="pass" onchange="form.repass.pattern = this.value;" value="<?php echo $pass; ?>" placeholder='' required='' pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{6,}" class="form-control" />
      </div>
      <div class="form-group col-md-4 col-sm-6">
        <label>Confirm Password * </label>
        <input name="repass" type="password" id="repass" value="<?php echo $pass; ?>" placeholder='' required='' pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{6,}" class="form-control" />
      </div>
      <div class="form-group col-md-4 col-sm-6">
        <label>Role Name *</label>
        <select name="prv" id="prv" class="form-control" onchange="toggle_allocation()">
          <?php
          $user_roles=$obj->read_all('id,named','rolenamed'," 1 ");
          while($row_td=$user_roles->fetch_assoc()){
            $rolename = $row_td['named'];
            ?>
            <option value="<?php echo $row_td['named']; ?>" <?php if(strcmp($prv,$rolename)==0){ echo "selected"; }?> ><?php echo $rolename; ?></option>
            <?php
          }
          ?>
        </select>
      </div>
      <div class="form-group col-md-4 col-sm-6">
        <label>Role Status</label>
        <select name="Temp" required class="form-control">
          <option value="<?php echo $Temp; ?>"><?php echo $Temp == 1 ? 'Temporary' : 'Normal'; ?></option>
          <option value="1">Temporary</option>
          <option value="0">Normal</option>
        </select>
      </div>
      <div class="form-group col-md-4 col-sm-6">
        <label>Account Status</label>
        <select name="user_status" required class="form-control">
          <option value="<?php echo $user_status; ?>"><?php echo $user_status == 1 ? 'Active' : 'Blocked'; ?></option>
          <option value="1">Active</option>
          <option value="0">Blocked</option>
        </select>
      </div>
      <div class="form-group col-md-4 col-sm-6 div_is_allocation_member">
        <br><label class="btn btn-warning"> <input type="checkbox" value="1" name="is_allocation_member" class="is_allocation_member" <?=$row['is_allocation_member']?'checked':''?>> Is Allocation Staff Member ?</label>
      </div>
      <table width="70%" align="center" class="table table-bordered">
        <tr class="bg-info">
          <th><strong>Day</strong></th>
          <th><strong>Status</strong></th>
          <th><strong>From</strong></th>
          <th><strong>To</strong></th>
        </tr>
        <tr>
          <td><strong>Monday</strong></td>
          <td>
            <select class="form-control" name="monday" id="monday" style="width:165px;">
              <option value="1" <?= $timing['monday'] ? 'selected' : '' ?>>Yes</option>
              <option value="0" <?= $timing && !$timing['monday'] ? 'selected' : '' ?>>No</option>
            </select>
          </td>
          <td>
            <input class="form-control" type="time" name="monday_time" id="monday_time" style="width:165px;" value="<?=$timing['monday_time']?:'08:00:00'; ?>" />
          </td>
          <td>
            <input class="form-control" type="time" name="monday_to" id="monday_to" style="width:165px;" value="<?=$timing['monday_to']?:'17:00:00'; ?>" />
          </td>
        </tr>
        <tr>
          <td><strong>Tuesday</strong></td>
          <td>
            <select class="form-control" name="tuesday" id="tuesday" style="width:165px;">
              <option value="1" <?= $timing['tuesday'] ? 'selected' : '' ?>>Yes</option>
              <option value="0" <?= $timing && !$timing['tuesday'] ? 'selected' : '' ?>>No</option>
            </select>
          </td>
          <td>
            <input class="form-control" type="time" name="tuesday_time" id="tuesday_time" style="width:165px;" value="<?=$timing['tuesday'] && $timing['tuesday_time']?$timing['tuesday_time']:'08:00:00'; ?>" />
          </td>
          <td>
            <input class="form-control" type="time" name="tuesday_to" id="tuesday_to" style="width:165px;" value="<?=$timing['tuesday'] && $timing['tuesday_to']?$timing['tuesday_to']:'08:00:00'; ?>" />
          </td>
        </tr>
        <tr>
          <td><strong>Wednesday</strong></td>
          <td>
            <select class="form-control" name="wednesday" id="wednesday" style="width:165px;">
              <option value="1" <?= $timing['wednesday'] ? 'selected' : '' ?>>Yes</option>
              <option value="0" <?= $timing && !$timing['wednesday'] ? 'selected' : '' ?>>No</option>
            </select>
          </td>
          <td>
            <input class="form-control" type="time" name="wednesday_time" id="wednesday_time" style="width:165px;" value="<?=$timing['wednesday_time']?:'08:00:00'; ?>" />
          </td>
          <td>
            <input class="form-control" type="time" name="wednesday_to" id="wednesday_to" style="width:165px;" value="<?=$timing['wednesday_to']?:'17:00:00'; ?>" />
          </td>
        </tr>
        <tr>
          <td><strong>Thursday</strong></td>
          <td>
            <select class="form-control" name="thursday" id="thursday" style="width:165px;">
              <option value="1" <?= $timing['thursday'] ? 'selected' : '' ?>>Yes</option>
              <option value="0" <?= $timing && !$timing['thursday'] ? 'selected' : '' ?>>No</option>
            </select>
          </td>
          <td>
            <input class="form-control" type="time" name="thursday_time" id="thursday_time" style="width:165px;" value="<?=$timing['thursday_time']?:'08:00:00'; ?>" />
          </td>
          <td>
            <input class="form-control" type="time" name="thursday_to" id="thursday_to" style="width:165px;" value="<?=$timing['thursday_to']?:'17:00:00'; ?>" />
          </td>
        </tr>
        <tr>
          <td><strong>Friday</strong></td>
          <td>
            <select class="form-control" name="friday" id="friday" style="width:165px;">
              <option value="1" <?= $timing['friday'] ? 'selected' : '' ?>>Yes</option>
              <option value="0" <?= $timing && !$timing['friday'] ? 'selected' : '' ?>>No</option>
            </select>
          </td>
          <td>
            <input class="form-control" type="time" name="friday_time" id="friday_time" style="width:165px;" value="<?=$timing['friday_time']?:'08:00:00'; ?>" />
          </td>
          <td>
            <input class="form-control" type="time" name="friday_to" id="friday_to" style="width:165px;" value="<?=$timing['friday_to']?:'17:00:00'; ?>" />
          </td>
        </tr>
        <tr>
          <td><strong>Saturday</strong></td>
          <td>
            <select class="form-control" name="saturday" id="saturday" style="width:165px;">
              <option value="0" <?= $timing && !$timing['saturday'] ? 'selected' : '' ?>>No</option>
              <option value="1" <?= $timing['saturday'] ? 'selected' : '' ?>>Yes</option>
            </select>
          </td>
          <td>
            <input class="form-control" type="time" name="saturday_time" id="saturday_time" style="width:165px;" value="<?=$timing['saturday'] && $timing['saturday_time']?$timing['saturday_time']:''; ?>" />
          </td>
          <td>
            <input class="form-control" type="time" name="saturday_to" id="saturday_to" style="width:165px;" value="<?=$timing['saturday'] && $timing['saturday_to']?$timing['saturday_to']:''; ?>" />
          </td>
        </tr>
        <tr>
          <td><strong>Sunday</strong></td>
          <td>
            <select class="form-control" name="sunday" id="sunday" style="width:165px;">
              <option value="0" <?= $timing && !$timing['sunday'] ? 'selected' : '' ?>>No</option>
              <option value="1" <?= $timing['sunday'] ? 'selected' : '' ?>>Yes</option>
            </select>
          </td>
          <td>
            <input class="form-control" type="time" name="sunday_time" id="sunday_time" style="width:165px;" value="<?=$timing['sunday'] && $timing['sunday_time']?$timing['sunday_time']:''; ?>" />
          </td>
          <td>
            <input class="form-control" type="time" name="sunday_to" id="sunday_to" style="width:165px;" value="<?=$timing['sunday'] && $timing['sunday_to']?$timing['sunday_to']:''; ?>" />
          </td>
        </tr>
        <tr>
          <td colspan="4"><button type="submit" style="font-weight:bold;font-size:16px;" name="submit" class="btn btn-primary">Submit &raquo;</button></td>
        </tr>
      </table>
    </form>
  </div>
  <?php if (isset($_POST['submit'])) {
    $obj->editFun($table, $edit_id, 'edited_by', $_SESSION['UserName']);
    $obj->editFun($table, $edit_id, 'edited_date', date("Y-m-d H:i:s"));
    $obj->new_old_table('hist_' . $table, $table, $edit_id); ?>
    <script>
      alert('Account has been updated successfully. Thank you');
      window.onunload = refreshParent;

      function refreshParent() {
        window.opener.location.reload();
      }
      window.close();
    </script>
  <?php } ?>
</body>
<script>
  $(".valid").bind('keypress paste', function(e) {
    var regex = new RegExp(/[a-z A-Z 0-9 ()]/);
    var str = String.fromCharCode(!e.charCode ? e.which : e.charCode);
    if (!regex.test(str)) {
      e.preventDefault();
      return false;
    }
  });
  function toggle_allocation() {
    if (true) {
      $('.div_is_allocation_member').removeClass('hidden');
    } else {
      //$('.div_is_allocation_member').addClass('hidden');
      $('.is_allocation_member').prop('checked', false);
    }
  }
  $(document).ready(function(){
    toggle_allocation();
  });
</script>

</html>