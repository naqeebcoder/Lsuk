<?php
include "userhaspage.php";
SysPermiss::UserHasPage(__FILE__);

if (session_id() == '' || !isset($_SESSION)) {
  session_start();
}

include('class.php');
//include 'actions.php';
$table = 'account_payment_modes';

if (isset($_POST['btn_insert_comp_type'])) {
  $check_existing = $obj->read_specific('id', $table, "title='" . $_POST['title'] . "' AND company_type_id='" . $_POST['company_type_id'] . "'")['id'];
  if (empty($check_existing)) {
    $done = $obj->insert($table, array("title" => trim($_POST['title']), "company_type_id" => $_POST['company_type_id'], "dated" => date('Y-m-d')));
    if ($done) {
      $msg = "<div class='alert alert-success'>New company type has been added.</div>";
    } else {
      $msg = "<div class='alert alert-danger'>Failed to add New company type!</div>";
    }
  } else {
    $msg = "<div class='alert alert-danger'>Same company type already exists!</div>";
  }
}
if (isset($_POST['btn_update_comp_type'])) {
  $ok = 1;
  $check_existing = $obj->read_specific('id', $table, "title='" . $_POST['title'] . "' AND company_type_id='" . $_POST['company_type_id'] . "'")['id'];
  if (!empty($check_existing)) {
    if ($check_existing != $_POST['id']) {
      $ok = 0;
      $msg = "<div class='alert alert-danger'>Same company type already exists!</div>";
    }
  }
  if ($ok == 1) {
    $done = $obj->update($table, array("title" => trim($_POST['title']), "company_type_id" => $_POST['company_type_id'], "dated" => date('Y-m-d')), "id=" . $_POST['id']);
    if ($done) {
      $msg = "<div class='alert alert-success'>Company type has been updated.</div>";
    } else {
      $msg = "<div class='alert alert-danger'>Failed to update this company type!</div>";
    }
  }
}
if (isset($_GET['id'])) {
  $get_comp_type = $obj->read_specific("*", $table, "id=" . $_GET['id']);
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
  <title>Manage Banks</title>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" />
  <link rel="stylesheet" type="text/css" href="css/util.css" />

  <style>
    .hidden-field {
      display: none;
    }
  </style>
  <script type="text/javascript">
    function MM_openBrWindow(theURL, winName, features) { //v2.0
      window.open(theURL, winName, features);
    }
  </script>
</head>

<body>

  <div class="container m-t-30">

    <h2>Bank Details</h2>

    <div class="panel panel-primary main_panel_wrapper">
      <div class="panel-heading">
        <h4 class="panel-title new_label_wrapper">Add New Bank Details</h4>
      </div>
      <div class="panel-body">
        <form id="bankForm" name="frmRegisterBank" method="POST">
          <input type="hidden" name="record_id" id="record_id" value="">
          <input type="hidden" name="MM_is_validated" id="MM_is_validated" value="0">
          <input type="hidden" name="MM_update" id="MM_update" value="0">
          <div class="row">
            <div class="col-md-3">
              <div class="form-group">
                <label for="isBank">Type <sup class="text-danger">*</sup></label>
                <select class="form-control" id="MM_is_cash" name="MM_is_cash" required>
                  <option value="0">Bank</option>
                  <option value="1">Cash</option>
                </select>
              </div>
            </div>
            <div class="col-md-5">
              <div class="form-group">
                <label for="bankName">Bank Name <sup class="text-danger">*</sup></label>
                <input type="text" class="form-control" id="bank_title" name="bank_title" placeholder="Enter Bank Name" required>
              </div>
            </div>
            <div class="col-md-4 hidden-field" id="accountNoWrapper">
              <div class="form-group">
                <label for="accountNo">Account No <sup class="text-danger">*</sup></label>
                <input type="text" class="form-control" id="account_no" name="account_no" placeholder="Enter Account Number" required>
              </div>
            </div>
            <div class="col-md-3 hidden-field" id="sortCodeWrapper">
              <div class="form-group">
                <label for="sortCode">Sort Code <sup class="text-danger">*</sup></label>
                <input type="text" class="form-control" id="sort_code" name="sort_code" placeholder="Enter Sort Code" required>
              </div>
            </div>
            <div class="col-md-3 hidden-field" id="ibanNoWrapper">
              <div class="form-group">
                <label for="ibanNo">IBAN No</label>
                <input type="text" class="form-control" id="iban_no" name="iban_no" placeholder="Enter IBAN Number">
              </div>
            </div>
            <div class="col-md-2">
              <div class="form-group">
                <label for="status">Status</label>
                <select class="form-control" id="status" name="status" required>
                  <option value="1">Active</option>
                  <option value="0">Inactive</option>
                </select>
              </div>
            </div>

            <div class="col-md-12 text-right">
              <button type="button" class="btn btn-danger btn_cancel hide">Cancel</button>
              <button type="submit" class="btn btn-primary btn_add_bank_details">Add New</button>
            </div>

          </div>

        </form>
      </div>
    </div>
  </div>

  <div class="container m-t-30">
    <h3>Bank List</h3>
    <?php
    // Fetch records
    $banks = $acttObj->read_all("*", "account_payment_modes", " 1 ORDER BY status DESC");
    ?>

    <table class="table table-bordered table-striped table-hover">
      <thead>
        <tr>
          <th>#</th>
          <th>Type</th>
          <th>Bank Name</th>
          <th>Account No</th>
          <th>Sort Code</th>
          <th>IBAN</th>
          <th>Status</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php
        $i = 1;
        if (mysqli_num_rows($banks) > 0) {
          while ($row = mysqli_fetch_assoc($banks)) {
        ?>
            <tr>
              <td><?= $i++; ?></td>
              <td><?= $row['is_bank'] ? 'Bank' : 'Cash'; ?></td>
              <td><?= htmlspecialchars($row['name']); ?></td>
              <td><?= $row['is_bank'] ? htmlspecialchars($row['account_no']) : '-'; ?></td>
              <td><?= $row['is_bank'] ? htmlspecialchars($row['sort_code']) : '-'; ?></td>
              <td><?= $row['is_bank'] ? htmlspecialchars($row['iban_no']) : '-'; ?></td>
              <td>
                <span class="label label-<?= ($row['is_deleted'] == 1) ? 'danger' : ($row['status'] ? 'success' : 'warning'); ?>">
                  <?= ($row['is_deleted'] == 1) ? 'Deleted' : (($row['is_deleted'] == 0 && $row['status']) ? 'Active' : 'Inactive'); ?>
                </span>
              </td>
              <td>
                <button class="btn btn-sm btn-primary btn-edit btn-xs" data-id="<?= $row['id']; ?>">Edit</button>
                <button class="btn btn-sm btn-danger btn-delete btn-xs" data-id="<?= $row['id']; ?>">Delete</button>
                <?php if ($row['is_deleted'] == 1) { ?>
                  <button class="btn btn-sm btn-warning btn-restore btn-xs" data-id="<?= $row['id']; ?>">Restore</button>
                <?php } ?>
              </td>
            </tr>
        <?php
          }
        } else {
          echo '<tr><td colspan="8" class="text-center">No bank info found.</td></tr>';
        }
        ?>
      </tbody>
    </table>

  </div>

  <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
  <script src="js/income_receive_amount.js"></script>

  <script>
    $(document).ready(function() {

      $("#sort_code").mask("00-000000");

      function toggleBankFields() {
        var isCash = $('#MM_is_cash').val();

        if (isCash === "0") { // Bank
          $('#accountNoWrapper, #sortCodeWrapper, #ibanNoWrapper').removeClass('hidden-field');
          $('#account_no, #sort_code').prop('required', true);
        } else {
          $('#accountNoWrapper, #sortCodeWrapper, #ibanNoWrapper').addClass('hidden-field');
          $('#account_no, #sort_code').prop('required', false);
        }
      }

      // Initial call on page load
      toggleBankFields();

      // Change event
      $('#MM_is_cash').change(function() {
        toggleBankFields();
      });

      // Submit form via AJAX
      $('#bankForm').on('submit', function(e) {
        e.preventDefault();
        $('#MM_is_validated').val(1);

        var formData = $(this).serialize();

        $.ajax({
          url: './ajax_functions.php',
          type: 'POST',
          data: formData,
          success: function(response) {
            if (response.trim() === '1001') {
              alert('Duplicate bank name or account number.');
            } else if (response.trim() === 'updated') {
              alert('Bank details updated successfully.');
              location.reload(); // or reload table only
            } else {
              $('#account_payment_mode_select').html(response);
              alert('Bank added successfully.');
              $('#bankForm')[0].reset();
              $('#submitBtn').text('Add New');
              $('#MM_update').val(0);
              $('#MM_is_validated').val(0);
              location.reload();
            }
          },
          error: function() {
            alert('Error submitting the form.');
          }
        });
      });


      $(document).on('click', '.btn-edit', function() {
        const id = $(this).data('id');
        $('.new_label_wrapper').text('Update Bank Details');
        $('.main_panel_wrapper').removeClass('panel-primary').addClass('panel-danger');

        $.ajax({
          url: './ajax_functions.php',
          type: 'POST',
          dataType: 'json',
          data: {
            action: 'get_bank_by_id',
            id: id
          },
          success: function(data) {
            if (!data || !data.id) {
              alert("Record not found.");
              return;
            }

            if (data.is_deleted == "1") {
              alert("This record is deleted. Please restore it before editing.");
              return;
            }

            // Populate form
            $('#record_id').val(data.id);
            $('#MM_is_cash').val(data.is_bank === "0" ? 1 : 0).trigger('change'); // 0 = bank, 1 = cash
            $('#bank_title').val(data.name);
            $('#account_no').val(data.account_no);
            $('#sort_code').val(data.sort_code);
            $('#iban_no').val(data.iban_no);
            $('#status').val(data.status);
            $('#MM_update').val(1);
            $('#MM_is_validated').val(1);

            $('.btn_add_bank_details').text('Update').removeClass('btn-primary').addClass('btn-warning');
            $('.btn_cancel').removeClass('hide');
          },
          error: function() {
            alert('Failed to fetch bank info.');
          }
        });
      });


      $(document).on('click', '.btn-delete', function() {
        var id = $(this).data('id');
        if (confirm('Are you sure you want to delete this bank info?')) {
          $.post('./ajax_functions.php', {
            action: 'delete_bank',
            id: id
          }, function(response) {
            if (response.trim() == 'deleted') {
              alert('Deleted successfully');
              location.reload();
            } else {
              alert('Error while deleting');
            }
          });
        }
      });

      $(document).on('click', '.btn-restore', function() {
        var id = $(this).data('id');
        if (confirm('Are you sure you want to restore this record?')) {
          $.ajax({
            url: './ajax_functions.php',
            type: 'POST',
            data: {
              action: 'restore_bank',
              id: id
            },
            success: function(response) {
              if (response.trim() === 'restored') {
                alert('Record restored successfully!');
                location.reload();
              } else {
                alert('Error restoring record.');
              }
            },
            error: function() {
              alert('AJAX error occurred.');
            }
          });
        }
      });

      $(document).on('click', '.btn_cancel', function() {
        location.reload();
      });



    });
  </script>

</body>

</html>