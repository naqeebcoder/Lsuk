<?php if (session_id() == '' || !isset($_SESSION)) {
  session_start();
}
include 'actions.php';
$allowed_type_idz = "62";
//Check if user has current action allowed
if ($_SESSION['is_root'] == 0) {
  $get_page_access = $obj->read_specific("id", "action_permissions", "user_id=" . $_SESSION['userId'] . " AND action_id IN (" . $allowed_type_idz . ")")['id'];
  if (empty($get_page_access)) {
    die("<center><h2 class='text-center text-danger'>You do not have access to <u>Update Booking Rates</u> action!<br>Kindly contact admin for further process.</h2></center>");
  }
}
$table = 'comp_reg';
$array_types = array(1 => "F2F", 2 => "Telephone", 3 => "Translation", 4 => "Transcription", 5 => "BSL Video");
$array_types_colored = array(1 => "<span class='label label-success'>F2F</span>", 2 => "<span class='label label-info'>Telephone</span>", 3 => "<span class='label label-primary'>Translation</span>", 4 => "<span class='label label-warning'>Transcription</span>", 5 => "<span class='label label-danger'>BSL Video</span>");
$array_yes_no = array(1 => "Yes", 0 => "No");

$check_parent_company = $obj->read_specific("*", "child_companies", "child_comp=" . $_GET['company_id']);

if (isset($_POST['btn_update_global_rates'])) {
  $ok = 0;
  $row_ids = $_POST['row_id'];
  foreach ($row_ids as $key => $index) {
    $count_indexes++;
    $order_type = $_POST['order_type'][$key];
    $company_type_id = $_POST['company_type_id'][$key];
    $rate_category_id = $_POST['rate_category_id'][$key];
    $minimum_charge_interpreting = $_POST['minimum_charge_interpreting'][$key];
    $minimum_charge_telephone = $_POST['minimum_charge_telephone'][$key];
    $minimum_charge_translation = $_POST['minimum_charge_translation'][$key];
    $incremental_charge_f2f = $_POST['incremental_charge_f2f'][$key];
    $incremental_charge_tp = $_POST['incremental_charge_tp'][$key];
    $incremental_charge_tr = $_POST['incremental_charge_tr'][$key];
    $rate_value_f2f = $_POST['rate_value_f2f'][$key];
    $rate_value_tp = $_POST['rate_value_tp'][$key];
    $rate_value_tr = $_POST['rate_value_tr'][$key];
    $admin_charge = $_POST['admin_charge'][$key] ?: 0;
    $admin_charge_rate = $_POST['admin_charge_rate'][$key];
    $travel_time_charges = $_POST['travel_time_charges'][$key] ?: 0;
    $travel_time_rate = $_POST['travel_time_rate'][$key];
    $mileage_charge = $_POST['mileage_charge'][$key] ?: 0;
    $mileage_charge_rate = $_POST['mileage_charge_rate'][$key];
    $purchase_order = $_POST['purchase_order'][$key] ?: 0;
    $parking_charges = $_POST['parking_charges'][$key] ?: 0;

    if (!empty($_POST['company_id'][$key])) {
      $ok = 1;
      $done = $obj->update(
        "individual_company_rates",
        array(
          "minimum_charge_interpreting" => $minimum_charge_interpreting,
          "minimum_charge_telephone" => $minimum_charge_telephone,
          "minimum_charge_translation" => $minimum_charge_translation,
          "incremental_charge_f2f" => $incremental_charge_f2f,
          "incremental_charge_tp" => $incremental_charge_tp,
          "incremental_charge_tr" => $incremental_charge_tr,
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
        "id=" . $index
      );
    } else {
      $ok = 1;
      $insert_company_id = !empty($check_parent_company['parent_comp']) ? $check_parent_company['parent_comp'] : $_GET['company_id'];
      $done = $obj->insert(
        "individual_company_rates",
        array(
          "company_id" => $_GET['company_id'],
          "order_type" => $order_type,
          "company_type_id" => $company_type_id,
          "rate_category_id" => $rate_category_id,
          "minimum_charge_interpreting" => $minimum_charge_interpreting,
          "minimum_charge_telephone" => $minimum_charge_telephone,
          "minimum_charge_translation" => $minimum_charge_translation,
          "incremental_charge_f2f" => $incremental_charge_f2f,
          "incremental_charge_tp" => $incremental_charge_tp,
          "incremental_charge_tr" => $incremental_charge_tr,
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
          "parking_charges" => $parking_charges,
          "created_by" => $_SESSION['userId'],
          "created_date" => date('Y-m-d H:i:s'),
        )
      );
    }
  }
  if ($ok == 1) {
    $msg = "<div class='alert alert-success alert-dismissible'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a>Rates for <b>" . $_POST['company_name'] . "</b> has been updated successfully.</div>";
  } else {
    $msg = "<div class='alert alert-danger alert-dismissible'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a>Failed to update rates for <b>" . $_POST['company_name'] . "</b>! Try again later</div>";
  }
}
if (isset($_GET['company_id'])) {
  $company_id = $_GET['company_id'];
  $get_original_company = $obj->read_specific("$table.*,comp_type.company_type_id,comp_type.title as type_name,company_types.title as group_name", "$table,comp_type,company_types", "comp_reg.type_id=comp_type.id AND comp_type.company_type_id=company_types.id AND comp_reg.id=" . $company_id . " LIMIT 1");
  if (!empty($check_parent_company['parent_comp'])) {
    $company_id = $check_parent_company['parent_comp'];
    $get_company = $obj->read_specific("$table.*,comp_type.company_type_id,comp_type.title as type_name,company_types.title as group_name", "$table,comp_type,company_types", "comp_reg.type_id=comp_type.id AND comp_type.company_type_id=company_types.id AND comp_reg.id=" . $company_id . " LIMIT 1");
  } else {
    $get_company = $get_original_company;
  }
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
  <title>Update Company Booking Rates</title>
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

    .tr_rates input[type='text'],
    .tr_rates select {
      width: 100px;
      height: 30px;
    }

    label.btn {
      padding: 4px;
    }
  </style>
  <script type="text/javascript">
    function MM_openBrWindow(theURL, winName, features) { //v2.0
      window.open(theURL, winName, features);
    }
  </script>
</head>
<?php // Fetch company self rates if they are set else fetch global rates for selected group of company
$get_group_rates = $obj->read_all("individual_company_rates.*,rate_categories.title,rate_categories.is_bsl,rate_categories.is_rare", "individual_company_rates,rate_categories", "individual_company_rates.rate_category_id=rate_categories.id AND individual_company_rates.company_id=" . $get_company['id'] . " AND individual_company_rates.company_type_id=" . $get_company['company_type_id'] . " ORDER BY individual_company_rates.id ASC");
if ($get_group_rates->num_rows == 0) {
  $rate_saved = 0;
  $get_group_rates = $obj->read_all("company_rates.*,rate_categories.title,rate_categories.is_bsl,rate_categories.is_rare", "company_rates,rate_categories", "company_rates.rate_category_id=rate_categories.id AND company_rates.company_type_id=" . $get_company['company_type_id'] . " ORDER BY company_rates.id ASC");
} else {
  $rate_saved = 1;
} ?>

<body class="container-fluid">
  <form action="" method="post" class="register" id="signup_form" name="signup_form" onsubmit="return formSubmit()" autocomplete="off">
    <?php if (isset($_GET['company_id'])) {
      $saved_label = $rate_saved == 1 ? '<label class="label label-success">Rates Saved</label>' : '<label class="label label-warning">Not Saved Yet</label>' ?>
      <input type="hidden" name="company_name" value="<?= $get_company['name'] ?>" />
      <h4><?= "Rates for <b>" . $get_company['name'] . " [" . $get_company['group_name'] . " => " . $get_company['type_name'] . "]</b>&nbsp;&nbsp;&nbsp;" . $saved_label ?>
        <a class="btn btn-warning pull-right" href="manage_global_rates.php" style="margin-left: 5px;">Go Back</a>
        <button class="btn btn-primary pull-right" type="submit" name="btn_update_global_rates" onclick="return formSubmit(); return false">Update Rates &raquo;</button>
      </h4>
      <?php if (!empty($check_parent_company['id'])) {
        echo '<h4 class="text-danger">NOTE: <b>' . $get_company['name'] . '</b> rates will be applied for <i>' . $get_original_company['name'] . '</i></h4>';
      } ?>
      <div class="col-md-12">
        <label onchange="toggle_rates_visibility('tr_rates')" for="show_all" class="btn bnt-xs btn-default"><input name="tr_show" type="radio" id="show_all" checked> ALL Rates</label>
        <label onchange="toggle_rates_visibility('tr_standard')" for="show_standard" class="btn bnt-xs btn-default"><input name="tr_show" type="radio" id="show_standard"> Standard Rates</label>
        <label onchange="toggle_rates_visibility('tr_bsl')" for="show_bsl" class="btn bnt-xs btn-default"><input name="tr_show" type="radio" id="show_bsl"> BSL Rates</label>
        <label onchange="toggle_rates_visibility('tr_rare')" for="show_rare" class="btn bnt-xs btn-default"><input name="tr_show" type="radio" id="show_rare"> Rare Language Rates</label>
      </div>
      <div class="form-group col-sm-6 col-sm-offset-3">
        <?= !empty($msg) ? $msg : '' ?>
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
        <?php
        $counter = 1;
        while ($row_group_rates = $get_group_rates->fetch_assoc()) {
          if ($row_group_rates['is_bsl'] == 0 && $row_group_rates['order_type'] == 5) {
            continue;
          }
          $tr_bsl_color = $row_group_rates['is_bsl'] == 1 ? 'bg-info tr_bsl' : '';
          $tr_rare_color = $row_group_rates['is_rare'] == 1 ? 'bg-danger tr_rare' : '';
          $label_bsl = $row_group_rates['is_bsl'] == 1 ? "<br><label class='label label-primary'>BSL<label>" : "";
          $label_rare = $row_group_rates['is_rare'] == 1 ? "<br><label class='label label-danger'>Rare<label>" : ""; ?>
          <tr class="tr_rates <?= !$tr_bsl_color && !$tr_rare_color ? 'tr_standard' : $tr_bsl_color . $tr_rare_color ?>">
            <div class="hidden">
              <input type="text" name="row_id[]" value="<?= $row_group_rates['id'] ?>" />
              <input type="text" name="company_id[]" value="<?= isset($row_group_rates['company_id']) ? $row_group_rates['company_id'] : '' ?>" />
              <input type="text" name="order_type[]" value="<?= isset($row_group_rates['order_type']) ? $row_group_rates['order_type'] : 1 ?>" />
              <input type="text" name="company_type_id[]" value="<?= isset($row_group_rates['company_type_id']) ? $row_group_rates['company_type_id'] : '' ?>" />
              <input type="text" name="rate_category_id[]" value="<?= isset($row_group_rates['rate_category_id']) ? $row_group_rates['rate_category_id'] : '' ?>" />
            </div>
            <td align="left"><b><?= $row_group_rates['title'];
                                echo $label_bsl . $label_rare; ?></b></td>
            <td align="left"><?= $array_types_colored[$row_group_rates['order_type']] ?></td>
            <td align="left">
              <input required autocomplete="off" style="display: inline;" class="form-control <?= $row_group_rates['order_type'] == 1 ?: 'hidden' ?>" type="text" name="minimum_charge_interpreting[]" value="<?= $row_group_rates['minimum_charge_interpreting'] ?>" />
              <input required autocomplete="off" style="display: inline;" class="form-control <?= $row_group_rates['order_type'] == 2 ?: 'hidden' ?>" type="text" name="minimum_charge_telephone[]" value="<?= $row_group_rates['minimum_charge_telephone'] ?>" />
              <input required autocomplete="off" style="display: inline;" class="form-control <?= $row_group_rates['order_type'] > 2 ?: 'hidden' ?>" type="text" name="minimum_charge_translation[]" value="<?= $row_group_rates['minimum_charge_translation'] ?>" />
            </td>
            <td align="left">
              <select title="Select Face To Face Incremental Charge" autocomplete="off" class="form-control <?= $row_group_rates['order_type'] == 1 ?: 'hidden' ?>" name="incremental_charge_f2f[]">
                <option <?= $row_group_rates['incremental_charge_f2f'] == 0 ? 'selected' : '' ?> value="0">0</option>
                <option <?= $row_group_rates['incremental_charge_f2f'] == 15 ? 'selected' : '' ?> value="15">15</option>
                <option <?= $row_group_rates['incremental_charge_f2f'] == 30 ? 'selected' : '' ?> value="30">30</option>
                <option <?= $row_group_rates['incremental_charge_f2f'] == 45 ? 'selected' : '' ?> value="45">45</option>
                <option <?= $row_group_rates['incremental_charge_f2f'] == 60 ? 'selected' : '' ?> value="60">60</option>
              </select>
              <select title="Select Telephone Incremental Charge" autocomplete="off" class="form-control <?= $row_group_rates['order_type'] == 2 ?: 'hidden' ?>" name="incremental_charge_tp[]">
                <option <?= $row_group_rates['incremental_charge_tp'] == 0 ? 'selected' : '' ?> value="0">0</option>
                <option <?= $row_group_rates['incremental_charge_tp'] == 15 ? 'selected' : '' ?> value="15">15</option>
                <option <?= $row_group_rates['incremental_charge_tp'] == 30 ? 'selected' : '' ?> value="30">30</option>
                <option <?= $row_group_rates['incremental_charge_tp'] == 45 ? 'selected' : '' ?> value="45">45</option>
                <option <?= $row_group_rates['incremental_charge_tp'] == 60 ? 'selected' : '' ?> value="60">60</option>
              </select>
              <select title="Select Translation Incremental Charge" autocomplete="off" class="form-control <?= $row_group_rates['order_type'] > 2 ?: 'hidden' ?>" name="incremental_charge_tr[]">
                <option <?= $row_group_rates['incremental_charge_tr'] == 0 ? 'selected' : '' ?> value="0">0</option>
                <option <?= $row_group_rates['incremental_charge_tr'] == 15 ? 'selected' : '' ?> value="15">15</option>
                <option <?= $row_group_rates['incremental_charge_tr'] == 30 ? 'selected' : '' ?> value="30">30</option>
                <option <?= $row_group_rates['incremental_charge_tr'] == 45 ? 'selected' : '' ?> value="45">45</option>
                <option <?= $row_group_rates['incremental_charge_tr'] == 60 ? 'selected' : '' ?> value="60">60</option>
              </select>
            </td>
            <td align="left">
              <label for="admin_charge_yes_<?= $counter ?>" class="btn bnt-xs btn-default"><input type="checkbox" id="admin_charge_yes_<?= $counter ?>" name="admin_charge[]" value="1" <?= $row_group_rates['admin_charge'] == 1 ? 'checked' : '' ?> /> Yes</label>
              <label for="admin_charge_no_<?= $counter ?>" class="btn bnt-xs btn-default"><input type="checkbox" id="admin_charge_no_<?= $counter ?>" name="admin_charge[]" value="0" <?= $row_group_rates['admin_charge'] == 0 ? 'checked' : '' ?> /> No</label>
              <input autocomplete="off" style="margin: 4px 0px;" class="form-control <?= $row_group_rates['admin_charge'] == 0 ? 'hidden' : '' ?>" type="text" name="admin_charge_rate[]" value="<?= $row_group_rates['admin_charge_rate'] ?>" placeholder="Enter Value" />
            </td>
            <td align="left">
              <?= $row_group_rates['order_type'] == 1 ? '<small>Rate Per Hour:</small><br>' : '' ?>
              <input autocomplete="off" style="display: inline;" class="form-control <?= $row_group_rates['order_type'] == 1 ?: 'hidden' ?>" type="text" name="rate_value_f2f[]" value="<?= $row_group_rates['rate_value_f2f'] ?>" placeholder="Enter rpH" title="Enter rpH" />
              <?= $row_group_rates['order_type'] == 2 ? '<small>Rate Per Min:</small><br>' : '' ?>
              <input autocomplete="off" style="display: inline;" class="form-control <?= $row_group_rates['order_type'] == 2 ?: 'hidden' ?>" type="text" name="rate_value_tp[]" value="<?= $row_group_rates['rate_value_tp'] ?>" placeholder="Enter rpM" title="Enter rpM" />
              <?= $row_group_rates['order_type'] > 2 ? '<small>Rate Per Unit:</small><br>' : '' ?>
              <input autocomplete="off" style="display: inline;" class="form-control <?= $row_group_rates['order_type'] > 2 ?: 'hidden' ?>" type="text" name="rate_value_tr[]" value="<?= $row_group_rates['rate_value_tr'] ?>" placeholder="Enter rpU" title="Enter rpU" />
            </td>
            <td align="left">
              <label for="travel_time_charges_yes_<?= $counter ?>" class="btn bnt-xs btn-default"><input type="checkbox" id="travel_time_charges_yes_<?= $counter ?>" name="travel_time_charges[]" value="1" <?= $row_group_rates['travel_time_charges'] == 1 ? 'checked' : '' ?> /> Yes</label>
              <label for="travel_time_charges_no_<?= $counter ?>" class="btn bnt-xs btn-default"><input type="checkbox" id="travel_time_charges_no_<?= $counter ?>" name="travel_time_charges[]" value="0" <?= $row_group_rates['travel_time_charges'] == 0 ? 'checked' : '' ?> /> No</label>
              <input autocomplete="off" style="margin: 4px 0px;" class="form-control <?= $row_group_rates['travel_time_rate'] == 0 ? 'hidden' : '' ?>" type="text" name="travel_time_rate[]" value="<?= $row_group_rates['travel_time_rate'] ?>" placeholder="Enter Value" />
            </td>
            <td align="left">
              <label for="mileage_charge_yes_<?= $counter ?>" class="btn bnt-xs btn-default"><input type="checkbox" id="mileage_charge_yes_<?= $counter ?>" name="mileage_charge[]" value="1" <?= $row_group_rates['mileage_charge'] == 1 ? 'checked' : '' ?> /> Yes</label>
              <label for="mileage_charge_no_<?= $counter ?>" class="btn bnt-xs btn-default"><input type="checkbox" id="mileage_charge_no_<?= $counter ?>" name="mileage_charge[]" value="0" <?= $row_group_rates['mileage_charge'] == 0 ? 'checked' : '' ?> /> No</label>
              <input autocomplete="off" style="margin: 4px 0px;" class="form-control <?= $row_group_rates['mileage_charge_rate'] == 0 ? 'hidden' : '' ?>" type="text" name="mileage_charge_rate[]" value="<?= $row_group_rates['mileage_charge_rate'] ?>" placeholder="Enter Value" />
            </td>
            <td align="left" class="hidden">
              <label for="purchase_order_yes_<?= $counter ?>" class="btn bnt-xs btn-default"><input type="checkbox" id="purchase_order_yes_<?= $counter ?>" name="purchase_order[]" value="1" <?= $row_group_rates['purchase_order'] == 1 ? 'checked' : '' ?> /> Yes</label>
              <label for="purchase_order_no_<?= $counter ?>" class="btn bnt-xs btn-default"><input type="checkbox" id="purchase_order_no_<?= $counter ?>" name="purchase_order[]" value="0" <?= $row_group_rates['purchase_order'] == 0 ? 'checked' : '' ?> /> No</label>
            </td>
            <td align="left">
              <label for="parking_charges_yes_<?= $counter ?>" class="btn bnt-xs btn-default"><input type="checkbox" id="parking_charges_yes_<?= $counter ?>" name="parking_charges[]" value="1" <?= $row_group_rates['parking_charges'] == 1 ? 'checked' : '' ?> /> Yes</label>
              <label for="parking_charges_no_<?= $counter ?>" class="btn bnt-xs btn-default"><input type="checkbox" id="parking_charges_no_<?= $counter ?>" name="parking_charges[]" value="0" <?= $row_group_rates['parking_charges'] == 0 ? 'checked' : '' ?> /> No</label>
            </td>
          </tr>
        <?php $counter++;
        } ?>
      </table>
    <?php } ?>
  </form>
</body>
<script src="js/jquery-1.11.3.min.js"></script>
<script src="js/bootstrap.js"></script>
<script>
  $("input[type='checkbox']").change(function() {
    if ($(this).prop("checked")) {
      $(this).parents('td').find("input[type='checkbox']").not(this).prop("checked", false);
    } else {
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