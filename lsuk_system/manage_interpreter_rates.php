<?php
include "userhaspage.php";
SysPermiss::UserHasPage(__FILE__);

if (session_id() == '' || !isset($_SESSION)) {
  session_start();
}

include 'actions.php';
$table = 'interpreter_rates';
$array_types = array(1 => "F2F", 2 => "Telephone", 3 => "Translation", 4 => "Transcription", 5 => "BSL Video");
$array_types_colored = array(1 => "<span class='label label-success'>F2F</span>", 2 => "<span class='label label-info'>Telephone</span>", 3 => "<span class='label label-primary'>Translation</span>", 4 => "<span class='label label-warning'>Transcription</span>", 5 => "<span class='label label-danger'>BSL Video</span>");
$array_yes_no = array(1 => "Yes", 0 => "No");

if (isset($_POST['btn_update_interpreter_rates'])) {
  $ok=0;
  $all_rows = $_POST['row_id'];
  foreach ($all_rows as $key => $row_id) {
    $group_id = $_POST['group_id'][$key];
    $order_type = $_POST['order_type'][$key];
    $rate_category_id = $_POST['rate_category_id'][$key];
    $minimum_charge_interpreting = $_POST['minimum_charge_interpreting'][$key];
    $minimum_charge_telephone = $_POST['minimum_charge_telephone'][$key];
    $minimum_charge_translation = $_POST['minimum_charge_translation'][$key];
    $incremental_charge_f2f = $_POST['incremental_charge_f2f'][$key];
    $incremental_charge_tp = $_POST['incremental_charge_tp'][$key];
    $incremental_charge_tr = $_POST['incremental_charge_tr'][$key];
    $interpreting_time = $_POST['interpreting_time'][$key]?:0;
    $rate_value_f2f = $_POST['rate_value_f2f'][$key];
    $rate_value_tp = $_POST['rate_value_tp'][$key];
    $rate_value_tr = $_POST['rate_value_tr'][$key];
    $admin_charge = $_POST['admin_charge'][$key]?:0;
    $admin_charge_rate = $_POST['admin_charge_rate'][$key];
    $travel_time_charges = $_POST['travel_time_charges'][$key]?:0;
    $travel_time_rate = $_POST['travel_time_rate'][$key];
    $mileage_charge = $_POST['mileage_charge'][$key]?:0;
    $mileage_charge_rate = $_POST['mileage_charge_rate'][$key];
    $parking_charges = $_POST['parking_charges'][$key]?:0;
    if (!empty($group_id)) {
      $done = $obj->update("interpreter_rates", 
      array(
        "minimum_charge_interpreting" => $minimum_charge_interpreting,
        "minimum_charge_telephone" => $minimum_charge_telephone,
        "minimum_charge_translation" => $minimum_charge_translation,
        "incremental_charge_f2f" => $incremental_charge_f2f,
        "incremental_charge_tp" => $incremental_charge_tp,
        "incremental_charge_tr" => $incremental_charge_tr,
        "interpreting_time" => $interpreting_time,
        "rate_value_f2f" => $rate_value_f2f,
        "rate_value_tp" => $rate_value_tp,
        "rate_value_tr" => $rate_value_tr,
        "admin_charge" => $admin_charge,
        "admin_charge_rate" => $admin_charge_rate,
        "travel_time_charges" => $travel_time_charges,
        "travel_time_rate" => $travel_time_rate,
        "mileage_charge" => $mileage_charge,
        "mileage_charge_rate" => $mileage_charge_rate,
        "parking_charges" => $parking_charges
      ), 
      "id=" . $row_id);
      $ok = $done ? 1 : 0;
    } else {
      $done = $obj->insert("interpreter_rates", 
      array(
        "group_id" => $_GET['group_id'],
        "order_type" => $order_type,
        "rate_category_id" => $rate_category_id,
        "minimum_charge_interpreting" => $minimum_charge_interpreting,
        "minimum_charge_telephone" => $minimum_charge_telephone,
        "minimum_charge_translation" => $minimum_charge_translation,
        "incremental_charge_f2f" => $incremental_charge_f2f,
        "incremental_charge_tp" => $incremental_charge_tp,
        "incremental_charge_tr" => $incremental_charge_tr,
        "interpreting_time" => $interpreting_time,
        "rate_value_f2f" => $rate_value_f2f,
        "rate_value_tp" => $rate_value_tp,
        "rate_value_tr" => $rate_value_tr,
        "admin_charge" => $admin_charge,
        "admin_charge_rate" => $admin_charge_rate,
        "travel_time_charges" => $travel_time_charges,
        "travel_time_rate" => $travel_time_rate,
        "mileage_charge" => $mileage_charge,
        "mileage_charge_rate" => $mileage_charge_rate,
        "parking_charges" => $parking_charges
      ));
      $ok = $done ? 1 : 0;
    }
  }
  if ($ok == 1) {
    $msg = "<div class='alert alert-success alert-dismissible'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a>Interpreter group rates has been saved successfully.</div>";
  } else {
    $msg = "<div class='alert alert-danger alert-dismissible'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a>Failed to save Interpreter group rates! Try again later</div>";
  }
}

if (isset($_POST['btn_insert_group'])) {
  $group_ok = $obj->insert("interpreter_groups", array("title" => $_POST['title'], "bsl_group" => $_POST['bsl_group']));
  if ($group_ok) {
    $msg = "<div class='alert alert-success alert-dismissible'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a>Global Interpreter New group has been added successfully.</div>";
  } else {
    $msg = "<div class='alert alert-danger alert-dismissible'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a>Failed to add new Interpreter Group! Try again later</div>";
  }
}

if (isset($_POST['btn_update_group'])) {
  $group_ok = $obj->update("interpreter_groups", array("title" => $_POST['title'], "bsl_group" => $_POST['bsl_group']), "id=" . $_POST['group_id']);
  if ($group_ok) {
    $msg = "<div class='alert alert-success alert-dismissible'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a>Global Interpreter group has been updated successfully.</div>";
  } else {
    $msg = "<div class='alert alert-danger alert-dismissible'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a>Failed to update Interpreter Group! Try again later</div>";
  }
}

if (isset($_GET['group_id'])) {
  $get_group = $obj->read_specific("*", "interpreter_groups", "id=" . $_GET['group_id']);
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
  <title>Update Interpreter Global Rates</title>
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
    .tr_rates input[type='text'], .tr_rates select{
      width: 100px;
      height: 30px;
    }
    label.btn{
      padding: 4px;
    }
  </style>
  <script type="text/javascript">
    function MM_openBrWindow(theURL, winName, features) { //v2.0
      window.open(theURL, winName, features);
    }
  </script>
</head>
<?php $get_global_rates = $obj->read_all("interpreter_rates.*,rate_categories.title,rate_categories.is_bsl,rate_categories.is_rare", "interpreter_rates,rate_categories", "interpreter_rates.rate_category_id=rate_categories.id AND rate_categories.for_interpreter=1 AND rate_categories.is_bsl=" . $get_group['bsl_group'] . " AND interpreter_rates.group_id=" . $get_group['id'] . " ORDER BY interpreter_rates.id");
if ($get_global_rates->num_rows == 0) {
  $rate_saved = 0;
  $get_global_rates = $obj->read_all("interpreter_rates.*,rate_categories.title,rate_categories.is_bsl,rate_categories.is_rare", "interpreter_rates,rate_categories", "interpreter_rates.rate_category_id=rate_categories.id AND rate_categories.for_interpreter=1 AND rate_categories.is_bsl=" . $get_group['bsl_group'] . " ORDER BY interpreter_rates.id");
} else {
  $rate_saved = 1;
}?>
<body class="container-fluid">
  <form action="" method="post" onsubmit="return formSubmit()" autocomplete="off">
    <?php if (isset($_GET['group_id'])) { ?>
        <input type="hidden" name="group_id" value="<?=$_GET['group_id']?>" />
        <input type="hidden" name="group_name" value="<?=$get_group['title']?>" />
      <?php }
      if (isset($_GET['group_id']) && isset($_GET['view_rates'])) {
        $saved_label = $rate_saved == 1 ? '<label class="label label-success">Rates Saved</label>' : '<label class="label label-warning">Not Saved Yet</label>';
        $group_bsl = $get_group['bsl_group'] == 1 ? " (<span style='color:red'>BSL Group</span>)" : " (Non-BSL Group)"; ?>
        <h3><?="Rates for <b>".$get_group['title'] . $group_bsl . "</b>&nbsp;&nbsp;&nbsp;" . $saved_label?>
        <a class="btn btn-warning pull-right" href="manage_interpreter_rates.php" style="margin-left: 5px;">Go Back</a>
        <button class="btn btn-primary pull-right" type="submit" name="btn_update_interpreter_rates" onclick="return formSubmit(); return false">Update Rates &raquo;</button>
      </h3>
      <div class="col-md-12 hidden">
        <label onchange="toggle_rates_visibility('tr_rates')" for="show_all" class="btn bnt-xs btn-default"><input name="tr_show" type="radio" id="show_all" checked> ALL Rates</label>
        <label onchange="toggle_rates_visibility('tr_standard')" for="show_standard" class="btn bnt-xs btn-default"><input name="tr_show" type="radio" id="show_standard"> Standard Rates</label>
        <label onchange="toggle_rates_visibility('tr_bsl')" for="show_bsl" class="btn bnt-xs btn-default"><input name="tr_show" type="radio" id="show_bsl"> BSL Rates</label>
        <label onchange="toggle_rates_visibility('tr_rare')" for="show_rare" class="btn bnt-xs btn-default"><input name="tr_show" type="radio" id="show_rare"> Rare Language Rates</label>
      </div>
      <div class="form-group col-sm-6 col-sm-offset-3">
        <?=!empty($msg) ? $msg : '' ?>
      </div>
      <table class="table table-bordered">
        <thead class="bg-primary">
          <th>Title</th>
          <th>Order Type</th>
          <th width="12%">Minimum Charges</th>
          <th width="12%">Incremental Charges</th>
          <th>Admin Charges</th>
          <th>Rate Per Unit</th>
          <th>Travel.Time CH</th>
          <th>Mileage Charges</th>
          <th>Parking Charges</th>
        </thead>
        <?php $counter=1;
        while ($row_rates = $get_global_rates->fetch_assoc()) {
          $tr_bsl_color = $row_rates['is_bsl'] == 1 ? 'bg-info tr_bsl' : '';
          $tr_rare_color = $row_rates['is_rare'] == 1 ? 'bg-danger tr_rare' : '';
          $label_bsl = $row_rates['is_bsl'] == 1 ? "<br><label class='label label-primary'>BSL<label>" : "";
          $label_rare = $row_rates['is_rare'] == 1 ? "<br><label class='label label-danger'>Rare<label>" : ""; ?>
          <tr class="tr_rates <?=!$tr_bsl_color && !$tr_rare_color ? 'tr_standard' : $tr_bsl_color.$tr_rare_color?>">
            <td align="left">
              <input type="hidden" name="row_id[]" value="<?=$row_rates['id']?>"/>
              <input type="hidden" name="group_id[]" value="<?=$rate_saved == 1?$row_rates['group_id']:''?>"/>
              <input type="hidden" name="order_type[]" value="<?=isset($row_rates['order_type'])?$row_rates['order_type']:1?>"/>
              <input type="hidden" name="rate_category_id[]" value="<?=isset($row_rates['rate_category_id'])?$row_rates['rate_category_id']:''?>"/>
              <b><?php echo $row_rates['title'];
              echo $label_bsl.$label_rare;?>
            </b></td>
            <td align="left"><?=$array_types_colored[$row_rates['order_type']]?></td>
            <td align="left">
              <input required autocomplete="off" style="display: inline;" class="form-control <?=$row_rates['order_type']==1?:'hidden'?>" type="text" name="minimum_charge_interpreting[]" value="<?=$row_rates['minimum_charge_interpreting']?>"/>
              <input required autocomplete="off" style="display: inline;" class="form-control <?=$row_rates['order_type']==2?:'hidden'?>" type="text" name="minimum_charge_telephone[]" value="<?=$row_rates['minimum_charge_telephone']?>"/>
              <input required autocomplete="off" style="display: inline;" class="form-control <?=$row_rates['order_type']>2?:'hidden'?>" type="text" name="minimum_charge_translation[]" value="<?=$row_rates['minimum_charge_translation']?>"/>
            </td>
            <td align="left">
              <select autocomplete="off" class="form-control <?=$row_rates['order_type']==1?:'hidden'?>" name="incremental_charge_f2f[]">
                <option <?=$row_rates['incremental_charge_f2f']==0?'selected':''?> value="0">0</option>
                <option <?=$row_rates['incremental_charge_f2f']==15?'selected':''?> value="15">15</option>
                <option <?=$row_rates['incremental_charge_f2f']==30?'selected':''?> value="30">30</option>
                <option <?=$row_rates['incremental_charge_f2f']==45?'selected':''?> value="45">45</option>
                <option <?=$row_rates['incremental_charge_f2f']==60?'selected':''?> value="60">60</option>
              </select>
              <select autocomplete="off" class="form-control <?=$row_rates['order_type']==2?:'hidden'?>" name="incremental_charge_tp[]">
                <option <?=$row_rates['incremental_charge_tp']==0?'selected':''?> value="0">0</option>
                <option <?=$row_rates['incremental_charge_tp']==15?'selected':''?> value="15">15</option>
                <option <?=$row_rates['incremental_charge_tp']==30?'selected':''?> value="30">30</option>
                <option <?=$row_rates['incremental_charge_tp']==45?'selected':''?> value="45">45</option>
                <option <?=$row_rates['incremental_charge_tp']==60?'selected':''?> value="60">60</option>
              </select>
              <select autocomplete="off" class="form-control <?=$row_rates['order_type']>2?:'hidden'?>" name="incremental_charge_tr[]">
                <option <?=$row_rates['incremental_charge_tr']==0?'selected':''?> value="0">0</option>
                <option <?=$row_rates['incremental_charge_tr']==15?'selected':''?> value="15">15</option>
                <option <?=$row_rates['incremental_charge_tr']==30?'selected':''?> value="30">30</option>
                <option <?=$row_rates['incremental_charge_tr']==45?'selected':''?> value="45">45</option>
                <option <?=$row_rates['incremental_charge_tr']==60?'selected':''?> value="60">60</option>
              </select>
            </td>
            <td align="left">
              <label for="admin_charge_yes_<?=$counter?>" class="btn bnt-xs btn-default"><input type="checkbox" id="admin_charge_yes_<?=$counter?>" name="admin_charge[]" value="1" <?=$row_rates['admin_charge']==1?'checked':''?>/> Yes</label>
              <label for="admin_charge_no_<?=$counter?>" class="btn bnt-xs btn-default"><input type="checkbox" id="admin_charge_no_<?=$counter?>" name="admin_charge[]" value="0" <?=$row_rates['admin_charge']==0?'checked':''?>/> No</label>
              <input autocomplete="off" style="margin: 4px 0px;" class="form-control <?=$row_rates['admin_charge']==0?'hidden':''?>" type="text" name="admin_charge_rate[]" value="<?=$row_rates['admin_charge_rate']?>" placeholder="Enter Value"/>
            </td>
            <td align="left">
              <p class="hidden">Rate Per Hour:</p>
              <input autocomplete="off" style="display: inline;" class="form-control <?=$row_rates['order_type']==1?:'hidden'?>" type="text" name="rate_value_f2f[]" value="<?=$row_rates['rate_value_f2f']?>" placeholder="Enter rpH"/>
              <p class="hidden">Rate Per Min:</p>
              <input autocomplete="off" style="display: inline;" class="form-control <?=$row_rates['order_type']==2?:'hidden'?>" type="text" name="rate_value_tp[]" value="<?=$row_rates['rate_value_tp']?>" placeholder="Enter rpM"/>
              <p class="hidden">Rate Per Unit:</p>
              <input autocomplete="off" style="display: inline;" class="form-control <?=$row_rates['order_type']>2?:'hidden'?>" type="text" name="rate_value_tr[]" value="<?=$row_rates['rate_value_tr']?>" placeholder="Enter rpU"/>
            </td>
            <td align="left">
              <label for="travel_time_charges_yes_<?=$counter?>" class="btn bnt-xs btn-default <?=$row_rates['order_type']==1?:'hidden'?>"><input type="checkbox" id="travel_time_charges_yes_<?=$counter?>" name="travel_time_charges[]" value="1" <?=$row_rates['travel_time_charges']==1?'checked':''?>/> Yes</label>
              <label for="travel_time_charges_no_<?=$counter?>" class="btn bnt-xs btn-default <?=$row_rates['order_type']==1?:'hidden'?>"><input type="checkbox" id="travel_time_charges_no_<?=$counter?>" name="travel_time_charges[]" value="0" <?=$row_rates['travel_time_charges']==0?'checked':''?>/> No</label>
              <input autocomplete="off" style="margin: 4px 0px;" class="form-control <?=$row_rates['travel_time_rate']==0?'hidden':''?>" type="text" name="travel_time_rate[]" value="<?=$row_rates['travel_time_rate']?>" placeholder="Enter Value"/>
            </td>
            <td align="left"> 
              <label for="mileage_charge_yes_<?=$counter?>" class="btn bnt-xs btn-default <?=$row_rates['order_type']==1?:'hidden'?>"><input type="checkbox" id="mileage_charge_yes_<?=$counter?>" name="mileage_charge[]" value="1" <?=$row_rates['mileage_charge']==1?'checked':''?>/> Yes</label>
              <label for="mileage_charge_no_<?=$counter?>" class="btn bnt-xs btn-default <?=$row_rates['order_type']==1?:'hidden'?>"><input type="checkbox" id="mileage_charge_no_<?=$counter?>" name="mileage_charge[]" value="0" <?=$row_rates['mileage_charge']==0?'checked':''?>/> No</label>
              <input autocomplete="off" style="margin: 4px 0px;" class="form-control <?=$row_rates['mileage_charge_rate']==0?'hidden':''?>" type="text" name="mileage_charge_rate[]" value="<?=$row_rates['mileage_charge_rate']?>" placeholder="Enter Value"/>
            </td>
            <td align="left">
              <label for="parking_charges_yes_<?=$counter?>" class="btn bnt-xs btn-default <?=$row_rates['order_type']==1?:'hidden'?>"><input type="checkbox" id="parking_charges_yes_<?=$counter?>" name="parking_charges[]" value="1" <?=$row_rates['parking_charges']==1?'checked':''?>/> Yes</label>
              <label for="parking_charges_no_<?=$counter?>" class="btn bnt-xs btn-default <?=$row_rates['order_type']==1?:'hidden'?>"><input type="checkbox" id="parking_charges_no_<?=$counter?>" name="parking_charges[]" value="0" <?=$row_rates['parking_charges']==0?'checked':''?>/> No</label>
            </td>
          </tr>
        <?php $counter++;
        } ?>
      </table>
      <?php } else { ?>
        <h3>Interpreter Rate Groups Information</h3>
        <fieldset class="row">
          <div class="form-group col-sm-12">
            <?= !empty($msg) ? $msg : '' ?>
          </div>
          <?php if (empty($get_group['id'])) { ?>
            <div class="form-group col-sm-12">
              <button data-toggle="collapse" data-target="#insert_new" class="btn btn-primary" type="button">Add New Group &raquo;</button>
              <div id="insert_new" class="collapse">
                <div class="form-group col-sm-4">
                  <br><label>Group Title * </label>
                  <input name="title" type="text" placeholder="Write group title" class="form-control" required='' id="title"/>
                </div>
              <div class="form-group col-sm-4">
                <br><label>Group For * </label>
                <select name="bsl_group" class="form-control" required='' id="bsl_group">
                  <option value="0">Non-BSL Group</option>
                  <option value="1" style="color:red">BSL Group</option>
                </select>
              </div>
                <div class="form-group col-sm-3">
                <br><br><button class="btn btn-primary" type="submit" name="btn_insert_group">Insert New Group &raquo;</button>
                  <a class="btn btn-warning" href="manage_interpreter_rates.php">Cancel</a>
                </div>
              </div>
            </div>
          <?php } else { ?>
            <div class="form-group col-sm-4 <?=empty($get_group['id'])?'hidden':''?>">
              <label>Group Title * </label>
              <input name="title" type="text" placeholder="Write group title" class="form-control" required='' id="title" value="<?= $get_group['title'] ?>" />
            </div>
            <div class="form-group col-sm-4 <?=empty($get_group['id'])?'hidden':''?>">
              <label>Group For * </label>
              <select name="bsl_group" class="form-control" required='' id="bsl_group">
                <option value="0" <?= $get_group['bsl_group']==0?'selected':'' ?>>Non-BSL Group</option>
                <option value="1" style="color:red" <?= $get_group['bsl_group']==1?'selected':'' ?>>BSL Group</option>
              </select>
            </div>
            <div class="form-group col-sm-3">
              <br><button class="btn btn-primary" type="submit" name="btn_update_group" onclick="return formSubmit(); return false">Update Group &raquo;</button>
              <a class="btn btn-warning" href="manage_interpreter_rates.php">Cancel</a>
            </div>
          <?php } ?>
        </fieldset>
        <fieldset class="row1">
          <h4>All Groups List</h4>
          <table class="table table-bordered">
            <thead class="bg-info">
              <th>S.No</th>
              <th>Group Title</th>
              <th>Group For</th>
              <th>Dated</th>
              <th>Action</th>
            </thead>
            <?php $result = $obj->read_all("*,DATE(created_date) as created_date", "interpreter_groups", "1 ORDER BY title ASC");
            $counter = 1;
            while ($row = $result->fetch_assoc()) {
              $is_rate_saved = $obj->read_specific("id", "interpreter_rates", "group_id=" . $row['id'])['id']; ?>
              <tr>
                <td align="left"><?php echo $counter++; ?> </td>
                <td align="left"><?php echo $row['title'];
                echo !empty($is_rate_saved) ? "<label class='label label-success pull-right'>Rates Saved</label>" : "<label class='label label-danger pull-right'>Rates Not Saved</label>"; ?> </td>
                <td align="left"><?php echo $row['bsl_group'] == 1 ? '<span style="color:red">BSL Group</span>' : 'Non-BSL Group'; ?> </td>
                <td align="left"><?php echo $misc->dated($row['created_date']); ?> </td>
                <td align="left">
                  <?php if ($_SESSION['prv'] == 'Management' || $_SESSION['prv'] == 'Finance') { ?>
                    <a href="?group_id=<?= $row['id'] ?>" title="Edit this group" class="btn btn-sm btn-info"><i class="glyphicon glyphicon-edit"></i></a>
                    <a href="?group_id=<?= $row['id'] ?>&view_rates=1" title="View group rates" class="btn btn-sm btn-primary">Manage Rates</a>
                  <?php } ?>
                </td>
              </tr>
            <?php } ?>
          </table>
        </fieldset>
      <?php } ?>
  </form>
</body>
<script src="js/jquery-1.11.3.min.js"></script>
<script src="js/bootstrap.js"></script>
<script>
  $("input[type='checkbox']").change(function() {
    if ($(this).prop("checked")) {
      $(this).parents('td').find("input[type='checkbox']").not(this).prop("checked", false);
    }else{
      $(this).parents('td').find("input[type='checkbox']").not(this).prop("checked", true);
    }
    if ($(this).val() == 1 && $(this).prop("checked")) {
      $(this).parents('td').find("input[type='text']").removeClass('hidden');
      $(this).parents('td').find("input[type='text']").attr('required', 'required');
    } else {
      $(this).parents('td').find("input[type='text']").addClass('hidden');
      $(this).parents('td').find("input[type='text']").removeAttr('required');
    }
  });
  function toggle_rates_visibility(tr_class = 'tr_standard') {
    $('.tr_rates.' + tr_class).removeClass('hidden');
    $('.tr_rates:not(.' + tr_class + ")").addClass('hidden');
  }
</script>
</html>