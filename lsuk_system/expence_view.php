<?php if (session_id() == '' || !isset($_SESSION)) {
  session_start();
}
include 'db.php';
include 'class.php';
$table = 'expence';

$allowed_type_idz = "94";
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
$comp = $row['comp'];
$amoun = $row['amoun'];
$billDate = $row['billDate'];
$details = $row['details'];

// Generating Invoice No for Old records using Voucher No, if invoice no is empty
if (empty($row['invoice_no']) && !empty($row['voucher']) && !empty($row['dated'])) {
  $invoice_no = $acttObj->generate_expense_invoice_no($row['voucher'], $row['dated']);
} else {
  $invoice_no = $row['invoice_no'];
}

$voucher = $row['voucher'];
$netamount = $row['netamount'];
$vat = $row['vat'];
$nonvat = $row['nonvat'];
$inv_ref_num = $row['inv_ref_num'];
$pay_by = $row['pay_by'];

if ($row['pay_by'] == 'bacs') {
  $pay_by = 'BACS';
} else {
  $pay_by = $row['pay_by'] ? ucwords(strtolower($row['pay_by'])) : 'N/A';
}

$exp_receipt = $row['exp_receipt'];
$exp_vat_no = $row['exp_vat_no'];
$type_id = $row['type_id'];

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
  <title>View Expense</title>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" />
  <link rel="stylesheet" type="text/css" href="css/util.css" />
  <script src="https://code.jquery.com/jquery-3.7.0.min.js" integrity="sha256-2Pmvv0kuTBOenSvLm6bvfBSSHrUJ+3A7x6P5Ebd07/g=" crossorigin="anonymous"></script>
</head>

<body>
  <div class="container">

    <h1 class="m-b-30">Expense Details (Track# <?php echo $invoice_no; ?>)</h1>

    <div class="row">
      <div class="form-group col-sm-3">
        <label> Voucher No </label>
        <div class="form-control">
          <?php echo $voucher; ?>
        </div>
      </div>
      <div class="form-group col-sm-3">
        <label>Invoice/Reference #</label>
        <div class="form-control">
          <?php echo ($inv_ref_num) ? $inv_ref_num : 'N/A'; ?>
        </div>
      </div>
      <?php if($row['is_prepayment'] == 0){ ?>
      <div class="form-group col-sm-6">
        <label>Payment By </label>
        <div class="form-control">
          <?php echo $pay_by; ?>
        </div>
      </div>
      <?php } else { ?>
        <div class="form-group col-sm-6">
        <label>Prepayment Reference</label>
        <div class="form-control">
          <?php 
              $prepayments_infos = $acttObj->full_fetch_array("SELECT p.invoice_no, p.voucher, p.total_amount, c.title as category, r.title as receiver
              FROM pre_payments p
              LEFT JOIN prepayment_categories c ON c.id = p.category_id
              LEFT JOIN prepayment_receivers r ON r.id = p.receiver_id
              WHERE p.invoice_no = {$row['prepayment_id']}
              ");
              if(count($prepayments_infos) > 0){
                foreach($prepayments_infos as $prepayments_info){
                  $selected = ($row['prepayment_id'] == $prepayments_info['invoice_no']) ? 'selected' : '';
                  echo '<option value="'.$prepayments_info['invoice_no'].'" '.$selected.'>
                  [# '.$prepayments_info['invoice_no'] . '] 
                  Receiver: '.$prepayments_info['receiver'].' -  
                  Category: '.$prepayments_info['category'].' 
                  </option>';
                }
              } 
            ?>
        </div>
      </div>
      <?php } ?>
      <?php
      if (!empty($row['payment_method_id'])) {
        $bank_info = $acttObj->read_specific("name as bank_name, account_no, sort_code, iban_no", "account_payment_modes", " id = " . $row['payment_method_id']);
      ?>
        <div class="form-group col-sm-6">
          <label>Payment Method </label>
          <div class="form-control">
            <?php echo $bank_info['bank_name'];  
            if($bank_info['account_no']){
              echo ' - ' . $bank_info['account_no']; 
            }
          ?>
          </div>
        </div>
      <?php } ?>
      <div class="form-group col-sm-3">
        <label>Bill Date </label>
        <div class="form-control">
          <?php echo $misc->dated($billDate); ?>
        </div>
      </div>
      <div class="form-group col-sm-3">
        <label>Paid On </label>
        <div class="form-control">
          <?php echo $misc->dated($acttObj->read_specific('payment_date', 'expence_partial_payments', 'expence_id = ' . $view_id)['payment_date']); ?>
        </div>
      </div>
      <div class="form-group col-sm-6">
        <label>Expenses Type </label>
        <?php $row_exp_type = $acttObj->read_specific('id,title', 'expence_list', 'id=' . $type_id);
        ?>
        <div class="form-control">
          <?php echo $row_exp_type['title']; ?>
        </div>
      </div>
      <div class="form-group col-sm-6">
        <label>Company Name </label>
        <div class="form-control">
          <?php echo $comp; ?>
        </div>
      </div>
      <div class="form-group col-sm-12">
        <label>Description </label>
        <div class="form-control border-1" style="min-height: 50px;">
          <?php echo ($details) ? $details : 'N/A'; ?>
        </div>
      </div>
      <div class="form-group col-sm-3 col-xs-6">
        <label>Net Amount</label>
        <div class="form-control">
          <?php echo $netamount ?: 0; ?>
        </div>
      </div>
      <div class="form-group col-sm-3 col-xs-6">
        <label>VAT </label>
        <div class="form-control">
          <?php echo $vat ?: 0; ?>
        </div>
      </div>
      <div class="form-group col-sm-3 col-xs-6">
        <label>Non VAT</label>
        <div class="form-control">
          <?php echo $nonvat ?: 0; ?>
        </div>
      </div>
      <div class="form-group col-sm-3 col-xs-6">
        <label>Total Amount </label>
        <div class="form-control">
          <?php echo $amoun ?: 0; ?>
        </div>
      </div>
      <div class="form-group col-sm-3 col-xs-6 <?php echo (!empty($vat) && $vat != 0) ? '' : 'hide'; ?>" id="div_vat_no">
        <label>VAT Number *</label>
        <div class="form-control">
          <?php echo $exp_vat_no ?: 'No VAT Number !'; ?>
        </div>
      </div>
      <div class="form-group col-sm-4 col-xs-12 receipt_view <?php echo (empty($exp_receipt)) ? 'hidden' : ''; ?>">
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

          <?php if ($row['paid_by']) { ?>
            <div class="col-sm-3">
              Paid by: <?php echo $row['paid_by']; ?>
            </div>
            <div class="col-sm-3">
              Paid on: <?php echo $misc->date_time($row['paid_on']); ?>
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
      window.open('exp_receipt_view.php?v_id=<?php echo $view_id; ?>', "popupWindow", "width=600,height=600,scrollbars=yes");
    });
  });
</script>