<?php if (session_id() == '' || !isset($_SESSION)) {
  session_start();
}
include 'actions.php';
$allowed_type_idz = "44";
//Check if user has current action allowed
if ($_SESSION['is_root'] == 0) {
    $get_page_access = $obj->read_specific("id", "action_permissions", "user_id=" . $_SESSION['userId'] . " AND action_id IN (" . $allowed_type_idz . ")")['id'];
    if (empty($get_page_access)) {
        die("<center><h2 class='text-center text-danger'>You do not have access to <u>Update Interpreter Rates</u> action!<br>Kindly contact admin for further process.</h2></center>");
    }
}
$table = 'interpreter_reg';
$array_types = array(1 => "F2F", 2 => "Telephone", 3 => "Translation", 4 => "Transcription", 5 => "BSL Video");
$array_types_colored = array(1 => "<span class='label label-success'>F2F</span>", 2 => "<span class='label label-info'>Telephone</span>", 3 => "<span class='label label-primary'>Translation</span>", 4 => "<span class='label label-warning'>Transcription</span>", 5 => "<span class='label label-danger'>BSL Video</span>");
$array_yes_no = array(1 => "Yes", 0 => "No");
if (isset($_POST['btn_update_self_rates'])) {
  $data_array = array("rate_group_id" => $_POST['rate_group_id'], "interpreter_id" => $_GET['interpreter_id']);
  if (!empty($_POST['id'])) {
    $data_array['updated_by'] = $_SESSION['userId'];
    $data_array['updated_date'] = date('Y-m-d H:i:s');
    $done = $obj->update("individual_interpreter_rates", $data_array, "id = " . $_POST['id']);
  } else {
    $data_array['created_by'] = $_SESSION['userId'];
    $data_array['created_date'] = date('Y-m-d H:i:s');
    $done = $obj->insert("individual_interpreter_rates", $data_array);
  }
  if ($done) {
    $msg = "<div class='alert alert-success alert-dismissible'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a>Rate group saved for <b>".$_GET['name']."</b> has been updated successfully.</div>";
  } else {
    $msg = "<div class='alert alert-danger alert-dismissible'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a>Failed to save rate group for <b>".$_GET['name']."</b>! Try again later</div>";
  }
}
if (isset($_GET['interpreter_id'])) {
  $get_group = $obj->read_specific("*", "individual_interpreter_rates", "interpreter_id=" . $_GET['interpreter_id'] . " ORDER BY id DESC LIMIT 1");
  if (empty($get_group['id'])) {
    $rate_saved = 0;
  } else {
    $rate_saved = 1;
  }
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
  <title>Update Interpreter Booking Rates</title>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" />
  <style>
    .table>tbody>tr>td,
    .table>tbody>tr>th,
    .table>tfoot>tr>td,
    .table>tfoot>tr>th,
    .table>thead>tr>td,
    .table>thead>tr>th {
      padding: 3px;
    }
    .tr_rates input readonly disabled[type='text']{
      width: 100px;
      height: 30px;
    }
    label.btn{
      padding: 4px;
    }
    .form-control[disabled], fieldset[disabled] .form-control {
      max-width: 100px;
  }
  </style>
  <script type="text/javascript">
    function MM_openBrWindow(theURL, winName, features) { //v2.0
      window.open(theURL, winName, features);
    }
  </script>
</head>

<body class="container-fluid">
  <form action="" method="post" id="rates_form" autocomplete="off" enctype="application/x-www-form-urlencoded">
    <?php if (isset($_GET['interpreter_id'])) {
      $saved_label = $rate_saved == 1 ? '<label class="label label-success">Rates Saved</label>' : '<label class="label label-warning">Not Saved Yet</label>' ?>
      <h3><?="Rates for <b>".$_GET['name']."</b>&nbsp;&nbsp;&nbsp;" . $saved_label?>
      <div class="col-md-6 pull-right">
        <div class="form-group col-md-6">
          <input type="hidden" name="id" value="<?=$get_group['id']?>"/>
          <select name="rate_group_id" class="form-control" required='' id="rate_group_id">
            <option value="">---Select Rate Group---</option>
            <?php $get_rate_groups = $obj->read_all("*", "interpreter_groups", "1");
              while($row_group = $get_rate_groups->fetch_assoc()){ ?>
              <option <?=$rate_saved == 1 && $get_group['rate_group_id'] == $row_group['id']?'selected':''?> value="<?=$row_group['id']?>" <?=$row_group['bsl_group']==1?'style="color:red"':''?>><?=$row_group['title']?></option>
            <?php } ?>
          </select>
        </div>
        <div class="form-group col-md-6">
          <button class="btn btn-primary" type="submit" name="btn_update_self_rates">Update Rate Group &raquo;</button>
          <a onclick="window.close();" class="btn btn-warning" href="#">Go Back</a>
        </div>
      </div>
      </h3>
      <div class="col-md-12 hidden">
        <label onchange="toggle_rates_visibility('tr_rates')" for="show_all" class="btn bnt-xs btn-default"><input readonly disabled name="tr_show" type="radio" id="show_all" checked> ALL Rates</label>
        <label onchange="toggle_rates_visibility('tr_standard')" for="show_standard" class="btn bnt-xs btn-default"><input readonly disabled name="tr_show" type="radio" id="show_standard"> Standard Rates</label>
        <label onchange="toggle_rates_visibility('tr_bsl')" for="show_bsl" class="btn bnt-xs btn-default"><input readonly disabled name="tr_show" type="radio" id="show_bsl"> BSL Rates</label>
        <label onchange="toggle_rates_visibility('tr_rare')" for="show_rare" class="btn bnt-xs btn-default"><input readonly disabled name="tr_show" type="radio" id="show_rare"> Rare Language Rates</label>
      </div>
      <div class="form-group col-sm-6 col-sm-offset-3">
        <?=!empty($msg) ? $msg : '' ?>
      </div>
      <?php $get_interpreter_rates = $obj->read_all("interpreter_rates.*,rate_categories.title,rate_categories.is_bsl,rate_categories.is_rare", "interpreter_rates,rate_categories", "interpreter_rates.rate_category_id=rate_categories.id AND interpreter_rates.group_id=" . $get_group['rate_group_id'] . " ORDER BY interpreter_rates.id");
      if ($get_interpreter_rates->num_rows > 0) { ?>
      <table class="table table-bordered">
        <thead class="bg-primary">
        <th>Title</th>
          <th>Order Type</th>
          <th width="12%">Minimum Charges</th>
          <th width="12%">Incremental Charges</th>
          <th onclick="get_val()">Admin Charges</th>
          <th>Manage Rates</th>
          <th>Travel.Time CH</th>
          <th>Mileage Charges</th>
          <th>Parking Charges</th>
        </thead>
        <?php
        $counter=1;
        while ($row_rates = $get_interpreter_rates->fetch_assoc()) {
          $tr_bsl_color = $row_rates['is_bsl'] == 1 ? 'bg-info tr_bsl' : '';
          $tr_rare_color = $row_rates['is_rare'] == 1 ? 'bg-danger tr_rare' : '';
          $label_bsl = $row_rates['is_bsl'] == 1 ? "<br><label class='label label-primary'>BSL<label>" : "";
          $label_rare = $row_rates['is_rare'] == 1 ? "<br><label class='label label-danger'>Rare<label>" : ""; ?>
          <tr class="tr_rates <?=!$tr_bsl_color && !$tr_rare_color ? 'tr_standard' : $tr_bsl_color.$tr_rare_color?>">
            <td align="left"><b><?php echo $row_rates['title'];
              echo $label_bsl.$label_rare;?>
            </b></td>
            <td align="left"><?=$array_types_colored[$row_rates['order_type']]?></td>
            <td align="left">
              <p class="hidden">Face To Face:</p>
              <input readonly disabled required autocomplete="off" style="display: inline;" class="form-control <?=$row_rates['order_type']==1?:'hidden'?>" type="number" name="minimum_charge_interpreting[]" value="<?=$row_rates['minimum_charge_interpreting']?>" placeholder="Min Hours"/>
              <p class="hidden">Telephone:</p>
              <input readonly disabled required autocomplete="off" style="display: inline;" class="form-control <?=$row_rates['order_type']==2?:'hidden'?>" type="number" name="minimum_charge_telephone[]" value="<?=$row_rates['minimum_charge_telephone']?>" placeholder="Min Minutes"/>
              <p class="hidden">Translation:</p>
              <input readonly disabled required autocomplete="off" style="display: inline;" class="form-control <?=$row_rates['order_type']>2?:'hidden'?>" type="number" name="minimum_charge_translation[]" value="<?=$row_rates['minimum_charge_translation']?>" placeholder="Min Units"/>
            </td>
            <td align="left">
              <p class="hidden">Face To Face:</p>
              <input readonly disabled required autocomplete="off" style="display: inline;" class="form-control <?=$row_rates['order_type']==1?:'hidden'?>" type="text" name="incremental_charge_f2f[]" value="<?=$row_rates['incremental_charge_f2f']?>"/>
              <p  class="hidden">Telephone:</p>
              <input readonly disabled required autocomplete="off" style="display: inline;" class="form-control <?=$row_rates['order_type']==2?:'hidden'?>" type="text" name="incremental_charge_tp[]" value="<?=$row_rates['incremental_charge_tp']?>"/>
              <p class="hidden">Translation:</p>
              <input readonly disabled required autocomplete="off" style="display: inline;" class="form-control <?=$row_rates['order_type']>2?:'hidden'?>" type="text" name="incremental_charge_tr[]" value="<?=$row_rates['incremental_charge_tr']?>"/>
            </td>
            <td align="left">
              <label for="admin_charge_yes_<?=$counter?>" class="btn bnt-xs btn-default"><input readonly disabled type="checkbox" id="admin_charge_yes_<?=$counter?>" name="admin_charge[]" value="1" <?=$row_rates['admin_charge']==1?'checked':''?>/> Yes</label>
              <label for="admin_charge_no_<?=$counter?>" class="btn bnt-xs btn-default"><input readonly disabled type="checkbox" id="admin_charge_no_<?=$counter?>" name="admin_charge[]" value="0" <?=$row_rates['admin_charge']==0?'checked':''?>/> No</label>
              <input readonly disabled autocomplete="off" style="margin: 4px 0px;" class="form-control <?=$row_rates['admin_charge']==0?'hidden':''?>" type="text" name="admin_charge_rate[]" value="<?=$row_rates['admin_charge_rate']?>" placeholder="Enter Value"/>
            </td>
            <td align="left">
              <p class="hidden">Rate Per Hour:</p>
              <input readonly disabled autocomplete="off" style="display: inline;" class="form-control <?=$row_rates['order_type']==1?:'hidden'?>" type="text" name="rate_value_f2f[]" value="<?=$row_rates['rate_value_f2f']?>" placeholder="Enter rpH"/>
              <p class="hidden">Rate Per Min:</p>
              <input readonly disabled autocomplete="off" style="display: inline;" class="form-control <?=$row_rates['order_type']==2?:'hidden'?>" type="text" name="rate_value_tp[]" value="<?=$row_rates['rate_value_tp']?>" placeholder="Enter rpM"/>
              <p class="hidden">Rate Per Unit:</p>
              <input readonly disabled autocomplete="off" style="display: inline;" class="form-control <?=$row_rates['order_type']>2?:'hidden'?>" type="text" name="rate_value_tr[]" value="<?=$row_rates['rate_value_tr']?>" placeholder="Enter rpU"/>
            </td>
            <td align="left">
              <label for="travel_time_charges_yes_<?=$counter?>" class="btn bnt-xs btn-default <?=$row_rates['order_type']==1?:'hidden'?>"><input readonly disabled type="checkbox" id="travel_time_charges_yes_<?=$counter?>" name="travel_time_charges[]" value="1" <?=$row_rates['travel_time_charges']==1?'checked':''?>/> Yes</label>
              <label for="travel_time_charges_no_<?=$counter?>" class="btn bnt-xs btn-default <?=$row_rates['order_type']==1?:'hidden'?>"><input readonly disabled type="checkbox" id="travel_time_charges_no_<?=$counter?>" name="travel_time_charges[]" value="0" <?=$row_rates['travel_time_charges']==0?'checked':''?>/> No</label>
              <input readonly disabled autocomplete="off" style="margin: 4px 0px;" class="form-control <?=$row_rates['travel_time_rate']==0?'hidden':''?>" type="text" name="travel_time_rate[]" value="<?=$row_rates['travel_time_rate']?>" placeholder="Enter Value"/>
            </td>
            <td align="left"> 
              <label for="mileage_charge_yes_<?=$counter?>" class="btn bnt-xs btn-default <?=$row_rates['order_type']==1?:'hidden'?>"><input readonly disabled type="checkbox" id="mileage_charge_yes_<?=$counter?>" name="mileage_charge[]" value="1" <?=$row_rates['mileage_charge']==1?'checked':''?>/> Yes</label>
              <label for="mileage_charge_no_<?=$counter?>" class="btn bnt-xs btn-default <?=$row_rates['order_type']==1?:'hidden'?>"><input readonly disabled type="checkbox" id="mileage_charge_no_<?=$counter?>" name="mileage_charge[]" value="0" <?=$row_rates['mileage_charge']==0?'checked':''?>/> No</label>
              <input readonly disabled autocomplete="off" style="margin: 4px 0px;" class="form-control <?=$row_rates['mileage_charge_rate']==0?'hidden':''?>" type="text" name="mileage_charge_rate[]" value="<?=$row_rates['mileage_charge_rate']?>" placeholder="Enter Value"/>
            </td>
            <td align="left">
              <label for="parking_charges_yes_<?=$counter?>" class="btn bnt-xs btn-default <?=$row_rates['order_type']==1?:'hidden'?>"><input readonly disabled type="checkbox" id="parking_charges_yes_<?=$counter?>" name="parking_charges[]" value="1" <?=$row_rates['parking_charges']==1?'checked':''?>/> Yes</label>
              <label for="parking_charges_no_<?=$counter?>" class="btn bnt-xs btn-default <?=$row_rates['order_type']==1?:'hidden'?>"><input readonly disabled type="checkbox" id="parking_charges_no_<?=$counter?>" name="parking_charges[]" value="0" <?=$row_rates['parking_charges']==0?'checked':''?>/> No</label>
            </td>
          </tr>
        <?php $counter++;
        } ?>
      </table>
    <?php } else {
        echo "<div class='col-md-12'><center><h3 class='text-danger'>Group Rates for " . $_GET['name'] . " are not been set yet!</h3></center></div>";
      }
     } ?>
  </form>
</body>
<script src="js/jquery-1.11.3.min.js"></script>
<script src="js/bootstrap.js"></script>
<script>
  $("input readonly disabled[type='checkbox']").change(function() {
    if ($(this).prop("checked")) {
      $(this).parents('td').find("input readonly disabled[type='checkbox']").not(this).prop("checked", false);
    }else{
      $(this).parents('td').find("input readonly disabled[type='checkbox']").not(this).prop("checked", true);
    }
    if ($(this).val() == 1 && $(this).prop("checked")) {
      $(this).parents('td').find("input readonly disabled[type='text']").removeClass('hidden');
      $(this).parents('td').find("input readonly disabled[type='text']").attr('required', 'required');
    } else {
      $(this).parents('td').find("input readonly disabled[type='text']").addClass('hidden');
      $(this).parents('td').find("input readonly disabled[type='text']").removeAttr('required');
    }
  });
  function toggle_rates_visibility(tr_class = 'tr_standard') {
    $('.tr_rates.' + tr_class).removeClass('hidden');
    $('.tr_rates:not(.' + tr_class + ")").addClass('hidden');
  }
</script>
</html>