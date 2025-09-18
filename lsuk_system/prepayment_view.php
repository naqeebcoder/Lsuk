<?php if (session_id() == '' || !isset($_SESSION)) {
  session_start();
}
include 'db.php';
include 'class.php';
$table = 'pre_payments';

$allowed_type_idz = "245";
//Check if user has current action allowed
if ($_SESSION['is_root'] == 0) {
  $get_page_access = $acttObj->read_specific("id", "action_permissions", "user_id=" . $_SESSION['userId'] . " AND action_id IN (" . $allowed_type_idz . ")")['id'];
  if (empty($get_page_access)) {
    die("<center><h2 class='text-center text-danger'>You do not have access to <u>View Company Expense</u> action!<br>Kindly contact admin for further process.</h2></center>");
  }
}
$view_id = @$_GET['view_id'];
$query = "SELECT * FROM $table where id=$view_id";
$result = mysqli_query($con, $query);
$row = mysqli_fetch_array($result);

$voucher = $row['voucher'];
$net_amount = $row['net_amount'];
$vat = $row['vat'];
$nonvat = $row['nonvat'];
$exp_receipt = $row['attachment'];
$category_id = $row['category_id'];

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
  <title>View Prepayment</title>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" />
  <link rel="stylesheet" type="text/css" href="css/util.css" />
  <script src="https://code.jquery.com/jquery-3.7.0.min.js" integrity="sha256-2Pmvv0kuTBOenSvLm6bvfBSSHrUJ+3A7x6P5Ebd07/g=" crossorigin="anonymous"></script>
</head>

<body>
  <div class="container">

    <h1 class="m-b-30">Prepayment Details (Track# <?php echo $row['invoice_no']; ?>)</h1>

    <div class="row">
      <div class="form-group col-sm-3">
        <label> Voucher No </label>
        <div class="form-control">
          <?php echo $voucher; ?>
        </div>
      </div>
      <div class="form-group col-sm-3">
        <label>No. of Installment</label>
        <div class="form-control">
          <?php echo ($row['no_of_installment']) ? $row['no_of_installment'] : 'N/A'; ?>
        </div>
      </div>
      <div class="form-group col-sm-3">
        <label>Frequency</label>
        <div class="form-control">
          <?php echo ($row['frequency']) ? ucwords($row['frequency']) : 'N/A'; ?>
        </div>
      </div>
      <div class="form-group col-sm-3">
        <label>Payment Date</label>
        <div class="form-control">
          <?php echo $misc->dated($row['payment_date']); ?>
        </div>
      </div>
      <div class="form-group col-sm-6">
        <label>Payment Type</label>
        <div class="form-control">
          <?php echo ($row['payment_type'] == 'bacs') ? 'BACs' : ucwords($row['payment_type']); ?>
        </div>
      </div>
      <?php
      if (!empty($row['payment_method_id'])) {
        $bank_info = $acttObj->read_specific("name as bank_name, account_no, sort_code, iban_no", "account_payment_modes", " id = " . $row['payment_method_id']);
      ?>
        <div class="form-group col-sm-6">
          <label>Payment Method </label>
          <div class="form-control">
            <?php echo $bank_info['bank_name'] . (($bank_info['account_no']) ? ' - ' . $bank_info['account_no'] : ''); ?>
          </div>
        </div>
      <?php } ?>
      
      <div class="form-group col-sm-6">
        <label>Category</label>
        <div class="form-control">
          <?php echo $acttObj->read_specific('title', 'prepayment_categories', 'id=' . $row['category_id'])['title']; ?>
        </div>
      </div>
      <div class="form-group col-sm-6">
        <label>Receiver</label>
        <div class="form-control">
          <?php echo $acttObj->read_specific('title', 'prepayment_receivers', 'id=' . $row['receiver_id'])['title']; ?>
        </div>
      </div>
      <div class="form-group col-sm-12">
        <label>Description </label>
        <div class="form-control border-1" style="min-height: 50px;">
          <?php echo ($row['description']) ? $row['description'] : 'N/A'; ?>
        </div>
      </div>
      <div class="form-group col-sm-3 col-xs-6">
        <label>Net Amount</label>
        <div class="form-control">
          <?php echo $row['net_amount'] ?: 0; ?>
        </div>
      </div>
      <div class="form-group col-sm-3 col-xs-6">
        <label>VAT </label>
        <div class="form-control">
          <?php echo $row['vat'] ?: 0; ?>
        </div>
      </div>
      <div class="form-group col-sm-3 col-xs-6">
        <label>Non VAT</label>
        <div class="form-control">
          <?php echo $row['nonvat'] ?: 0; ?>
        </div>
      </div>
      <div class="form-group col-sm-3 col-xs-6">
        <label>Total Amount </label>
        <div class="form-control">
          <?php echo $row['total_amount'] ?: 0; ?>
        </div>
      </div>
      <div class="form-group col-sm-4 col-xs-12 receipt_view <?php echo (empty($row['attachment'])) ? 'hidden' : ''; ?>">
        <button class='btn btn-primary' id="view_receipt"> View Receipt</button>
      </div>

      <div class="col-sm-12 fs-12 m-t-30">
        <div class="row">
          <div class="col-sm-3">
            Created by: <?php echo $row['posted_by']; ?>
          </div>
          <div class="col-sm-3">
            Created on: <?php echo $misc->date_time($row['dated']); ?>
          </div>

          <?php if ($row['edited_by']) { ?>
            <div class="col-sm-3">
              Updated by: <?php echo $row['edited_by']; ?>
            </div>
            <div class="col-sm-3">
              Updated on: <?php echo $misc->date_time($row['edited_date']); ?>
            </div>
          <?php } ?>

          <?php if ($row['deleted_by']) { ?>
            <div class="col-sm-3">
              Deleted by: <?php echo $row['deleted_by']; ?>
            </div>
            <div class="col-sm-3">
              Deleted on: <?php echo $misc->date_time($row['deleted_date']); ?>
            </div>
          <?php } ?>
        </div>

      </div>

    </div>
  </div>
</body>

</html>
<script>
  $(document).ready(function() {
    $(document).on('click', '#view_receipt', function(e) {
      e.preventDefault();
      window.open('prepayment_receipt_view.php?v_id=<?php echo $view_id; ?>', "popupWindow", "width=600,height=600,scrollbars=yes");
    });
  });
</script>