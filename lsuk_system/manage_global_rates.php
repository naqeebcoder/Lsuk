<?php
include "userhaspage.php";
SysPermiss::UserHasPage(__FILE__);

if (session_id() == '' || !isset($_SESSION)) {
  session_start();
}

include 'actions.php';
$table = 'company_types';
$array_types = array(1 => "F2F", 2 => "Telephone", 3 => "Translation", 4 => "Transcription", 5 => "BSL Video");
$array_types_colored = array(1 => "<span class='label label-success'>F2F</span>", 2 => "<span class='label label-info'>Telephone</span>", 3 => "<span class='label label-primary'>Translation</span>", 4 => "<span class='label label-warning'>Transcription</span>", 5 => "<span class='label label-danger'>BSL Video</span>");
$array_yes_no = array(1 => "Yes", 0 => "No");

if (isset($_POST['btn_update_global_rates'])) {
  $ok=0;
  $row = $_POST['row_id'];
  foreach ($row as $key => $row_id) {
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
    $purchase_order = $_POST['purchase_order'][$key]?:0;
    $parking_charges = $_POST['parking_charges'][$key]?:0;
    $done = $obj->update("company_rates", 
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
      "purchase_order" => $purchase_order,
      "parking_charges" => $parking_charges
    ), 
    "id=" . $row_id);
    if ($done) {
      $ok=1;
    }
  }
  if ($ok == 1) {
    $msg = "<div class='alert alert-success alert-dismissible'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a>Rates for <b>".$_POST['group_name']."</b> has been updated successfully.</div>";
  } else {
    $msg = "<div class='alert alert-danger alert-dismissible'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a>Failed to update rates for <b>".$_POST['group_name']."</b>! Try again later</div>";
  }
}
if (isset($_GET['id'])) {
  $get_group = $obj->read_specific("*", $table, "id=" . $_GET['id']);
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
  <title>Update Interpreter Rates</title>
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

<body class="container-fluid">
  <form action="" method="post" class="register" id="signup_form" name="signup_form" onsubmit="return formSubmit()" autocomplete="off">
    <?php if (isset($_GET['id'])) { ?>
      <input type="hidden" name="id" value="<?=$_GET['id']?>" />
      <input type="hidden" name="group_name" value="<?=$get_group['title']?>" />
    <?php }
    if (isset($_GET['id']) && isset($_GET['view_rates'])) { ?>
      <h3><?="Rates for <b>".$get_group['title']."</b>"?>
        <a class="btn btn-warning pull-right" href="manage_global_rates.php" style="margin-left: 5px;">Go Back</a>
        <button class="btn btn-primary pull-right" type="submit" name="btn_update_global_rates" onclick="return formSubmit(); return false">Update Rates &raquo;</button>
      </h3>
      <div class="col-md-12">
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
          <th>Manage Rates</th>
          <th>Travel.Time CH</th>
          <th>Mileage Charges</th>
          <th class="hidden">Purchase Order</th>
          <th>Parking Charges</th>
        </thead>
        <?php $get_group_rates = $obj->read_all("company_rates.*,rate_categories.title,rate_categories.is_bsl,rate_categories.is_rare", "company_rates,rate_categories", "company_rates.rate_category_id=rate_categories.id AND company_rates.company_type_id=" . $_GET['id'] . " ORDER BY company_rates.id");
        $counter=1;
        while ($row_group_rates = $get_group_rates->fetch_assoc()) {
          if ($row_group_rates['is_bsl'] == 0 && $row_group_rates['order_type'] == 5) {
            continue;
          }
          $tr_bsl_color = $row_group_rates['is_bsl'] == 1 ? 'bg-info tr_bsl' : '';
          $tr_rare_color = $row_group_rates['is_rare'] == 1 ? 'bg-danger tr_rare' : '';
          $label_bsl = $row_group_rates['is_bsl'] == 1 ? "<br><label class='label label-primary'>BSL<label>" : "";
          $label_rare = $row_group_rates['is_rare'] == 1 ? "<br><label class='label label-danger'>Rare<label>" : ""; ?>
          <tr class="tr_rates <?=!$tr_bsl_color && !$tr_rare_color ? 'tr_standard' : $tr_bsl_color.$tr_rare_color?>">
            <input type="hidden" name="row_id[]" value="<?=$row_group_rates['id']?>"/>
            <td align="left"><b><?php echo $row_group_rates['title'];
              echo $label_bsl.$label_rare;?>
            </b></td>
            <td align="left"><?=$array_types_colored[$row_group_rates['order_type']]?></td>
            <td align="left">
              <p class="hidden">Face To Face:</p>
              <input required autocomplete="off" style="display: inline;" class="form-control <?=$row_group_rates['order_type']==1?:'hidden'?>" type="text" name="minimum_charge_interpreting[]" value="<?=$row_group_rates['minimum_charge_interpreting']?>"/>
              <p class="hidden">Telephone:</p>
              <input required autocomplete="off" style="display: inline;" class="form-control <?=$row_group_rates['order_type']==2?:'hidden'?>" type="text" name="minimum_charge_telephone[]" value="<?=$row_group_rates['minimum_charge_telephone']?>"/>
              <p class="hidden">Translation:</p>
              <input required autocomplete="off" style="display: inline;" class="form-control <?=$row_group_rates['order_type']>2?:'hidden'?>" type="text" name="minimum_charge_translation[]" value="<?=$row_group_rates['minimum_charge_translation']?>"/>
            </td>
            <td align="left">
              <select title="Select Face To Face Incremental Charge" autocomplete="off" class="form-control <?=$row_group_rates['order_type']==1?:'hidden'?>" name="incremental_charge_f2f[]">
                <option <?=$row_group_rates['incremental_charge_f2f']==0?'selected':''?> value="0">0</option>
                <option <?=$row_group_rates['incremental_charge_f2f']==15?'selected':''?> value="15">15</option>
                <option <?=$row_group_rates['incremental_charge_f2f']==30?'selected':''?> value="30">30</option>
                <option <?=$row_group_rates['incremental_charge_f2f']==45?'selected':''?> value="45">45</option>
                <option <?=$row_group_rates['incremental_charge_f2f']==60?'selected':''?> value="60">60</option>
              </select>
              <select title="Select Telephone Incremental Charge" autocomplete="off" class="form-control <?=$row_group_rates['order_type']==2?:'hidden'?>" name="incremental_charge_tp[]">
                <option <?=$row_group_rates['incremental_charge_tp']==0?'selected':''?> value="0">0</option>
                <option <?=$row_group_rates['incremental_charge_tp']==15?'selected':''?> value="15">15</option>
                <option <?=$row_group_rates['incremental_charge_tp']==30?'selected':''?> value="30">30</option>
                <option <?=$row_group_rates['incremental_charge_tp']==45?'selected':''?> value="45">45</option>
                <option <?=$row_group_rates['incremental_charge_tp']==60?'selected':''?> value="60">60</option>
              </select>
              <select title="Select Translation Incremental Charge" autocomplete="off" class="form-control <?=$row_group_rates['order_type']>2?:'hidden'?>" name="incremental_charge_tr[]">
                <option <?=$row_group_rates['incremental_charge_tr']==0?'selected':''?> value="0">0</option>
                <option <?=$row_group_rates['incremental_charge_tr']==15?'selected':''?> value="15">15</option>
                <option <?=$row_group_rates['incremental_charge_tr']==30?'selected':''?> value="30">30</option>
                <option <?=$row_group_rates['incremental_charge_tr']==45?'selected':''?> value="45">45</option>
                <option <?=$row_group_rates['incremental_charge_tr']==60?'selected':''?> value="60">60</option>
              </select>
            </td>
            <td align="left">
              <label for="admin_charge_yes_<?=$counter?>" class="btn bnt-xs btn-default"><input type="checkbox" id="admin_charge_yes_<?=$counter?>" name="admin_charge[]" value="1" <?=$row_group_rates['admin_charge']==1?'checked':''?>/> Yes</label>
              <label for="admin_charge_no_<?=$counter?>" class="btn bnt-xs btn-default"><input type="checkbox" id="admin_charge_no_<?=$counter?>" name="admin_charge[]" value="0" <?=$row_group_rates['admin_charge']==0?'checked':''?>/> No</label>
              <input autocomplete="off" style="margin: 4px 0px;" class="form-control <?=$row_group_rates['admin_charge']==0?'hidden':''?>" type="text" name="admin_charge_rate[]" value="<?=$row_group_rates['admin_charge_rate']?>" placeholder="Enter Value"/>
            </td>
            <td align="left">
              <?=$row_group_rates['order_type']==1?'<small>Rate Per Hour:</small><br>':''?>
              <input autocomplete="off" style="display: inline;" class="form-control <?=$row_group_rates['order_type']==1?:'hidden'?>" type="text" name="rate_value_f2f[]" value="<?=$row_group_rates['rate_value_f2f']?>" placeholder="Enter rpH" title="Enter rpH"/>
              <?=$row_group_rates['order_type']==2?'<small>Rate Per Min:</small><br>':''?>
              <input autocomplete="off" style="display: inline;" class="form-control <?=$row_group_rates['order_type']==2?:'hidden'?>" type="text" name="rate_value_tp[]" value="<?=$row_group_rates['rate_value_tp']?>" placeholder="Enter rpM" title="Enter rpM"/>
              <?=$row_group_rates['order_type']>2?'<small>Rate Per Unit:</small><br>':''?>
              <input autocomplete="off" style="display: inline;" class="form-control <?=$row_group_rates['order_type']>2?:'hidden'?>" type="text" name="rate_value_tr[]" value="<?=$row_group_rates['rate_value_tr']?>" placeholder="Enter rpU" title="Enter rpU"/>
            </td>
            <td align="left">
              <label for="travel_time_charges_yes_<?=$counter?>" class="btn bnt-xs btn-default <?=$row_group_rates['order_type']==1?:'hidden'?>"><input type="checkbox" id="travel_time_charges_yes_<?=$counter?>" name="travel_time_charges[]" value="1" <?=$row_group_rates['travel_time_charges']==1?'checked':''?>/> Yes</label>
              <label for="travel_time_charges_no_<?=$counter?>" class="btn bnt-xs btn-default <?=$row_group_rates['order_type']==1?:'hidden'?>"><input type="checkbox" id="travel_time_charges_no_<?=$counter?>" name="travel_time_charges[]" value="0" <?=$row_group_rates['travel_time_charges']==0?'checked':''?>/> No</label>
              <input autocomplete="off" style="margin: 4px 0px;" class="form-control <?=$row_group_rates['travel_time_rate']==0?'hidden':''?>" type="text" name="travel_time_rate[]" value="<?=$row_group_rates['travel_time_rate']?>" placeholder="Enter Value"/>
            </td>
            <td align="left"> 
              <label for="mileage_charge_yes_<?=$counter?>" class="btn bnt-xs btn-default <?=$row_group_rates['order_type']==1?:'hidden'?>"><input type="checkbox" id="mileage_charge_yes_<?=$counter?>" name="mileage_charge[]" value="1" <?=$row_group_rates['mileage_charge']==1?'checked':''?>/> Yes</label>
              <label for="mileage_charge_no_<?=$counter?>" class="btn bnt-xs btn-default <?=$row_group_rates['order_type']==1?:'hidden'?>"><input type="checkbox" id="mileage_charge_no_<?=$counter?>" name="mileage_charge[]" value="0" <?=$row_group_rates['mileage_charge']==0?'checked':''?>/> No</label>
              <input autocomplete="off" style="margin: 4px 0px;" class="form-control <?=$row_group_rates['mileage_charge_rate']==0?'hidden':''?>" type="text" name="mileage_charge_rate[]" value="<?=$row_group_rates['mileage_charge_rate']?>" placeholder="Enter Value"/>
            </td>
            <td align="left" class="hidden">
              <label for="purchase_order_yes_<?=$counter?>" class="btn bnt-xs btn-default"><input type="checkbox" id="purchase_order_yes_<?=$counter?>" name="purchase_order[]" value="1" <?=$row_group_rates['purchase_order']==1?'checked':''?>/> Yes</label>
              <label for="purchase_order_no_<?=$counter?>" class="btn bnt-xs btn-default"><input type="checkbox" id="purchase_order_no_<?=$counter?>" name="purchase_order[]" value="0" <?=$row_group_rates['purchase_order']==0?'checked':''?>/> No</label>
            </td>
            <td align="left">
              <label for="parking_charges_yes_<?=$counter?>" class="btn bnt-xs btn-default <?=$row_group_rates['order_type']==1?:'hidden'?>"><input type="checkbox" id="parking_charges_yes_<?=$counter?>" name="parking_charges[]" value="1" <?=$row_group_rates['parking_charges']==1?'checked':''?>/> Yes</label>
              <label for="parking_charges_no_<?=$counter?>" class="btn bnt-xs btn-default <?=$row_group_rates['order_type']==1?:'hidden'?>"><input type="checkbox" id="parking_charges_no_<?=$counter?>" name="parking_charges[]" value="0" <?=$row_group_rates['parking_charges']==0?'checked':''?>/> No</label>
            </td>
          </tr>
        <?php $counter++;
        } ?>
      </table>
    <?php } else { ?>
      <h3>Company Group Information</h3>
      <fieldset class="row">
        <div class="form-group col-sm-12">
          <?= !empty($msg) ? $msg : '' ?>
        </div>
        <div class="form-group col-sm-4 <?=empty($get_group['id'])?'hidden':''?>">
          <label>Group Title * </label>
          <input name="title" type="text" placeholder="Write group title" class="form-control" required='' id="title" value="<?= $get_group['title'] ?>" />
        </div>
        <div class="form-group col-sm-3">
          <?php if (empty($get_group['id'])) { ?>
            <br><button class="btn btn-primary hidden" type="submit" name="btn_insert_group" onclick="return formSubmit(); return false">Add New Group &raquo;</button>
          <?php } else { ?>
            <br><button class="btn btn-primary" type="submit" name="btn_update_group" onclick="return formSubmit(); return false">Update Group &raquo;</button>
            <a class="btn btn-warning" href="manage_global_rates.php">Cancel</a>
          <?php } ?>
        </div>
      </fieldset>
      <fieldset class="row1">
        <h4>All Groups List</h4>
        <table class="table table-bordered">
          <thead class="bg-info">
            <th>S.No</th>
            <th>Company Type</th>
            <th>Dated</th>
            <th>Action</th>
          </thead>
          <?php $result = $obj->read_all("*,DATE(created_date) as created_date", $table, "1 ORDER BY title ASC");
          $counter = 1;
          while ($row = $result->fetch_assoc()) { ?>
            <tr>
              <td align="left"><?php echo $counter++; ?> </td>
              <td align="left"><?php echo $row['title']; ?> </td>
              <td align="left"><?php echo $misc->dated($row['created_date']); ?> </td>
              <td align="left">
                <?php if ($_SESSION['prv'] == 'Management' || $_SESSION['prv'] == 'Finance') { ?>
                  <a href="?id=<?= $row['id'] ?>" title="Edit this group" class="btn btn-sm btn-info"><i class="glyphicon glyphicon-edit"></i></a>
                  <a href="?id=<?= $row['id'] ?>&view_rates=1" title="View group rates" class="btn btn-sm btn-primary">Manage Rates</a>
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