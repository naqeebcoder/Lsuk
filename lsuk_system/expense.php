<?php
include "userhaspage.php";
SysPermiss::UserHasPage(__FILE__);
if (session_id() == '' || !isset($_SESSION)) {
  session_start();
}
include 'db.php';
include 'class.php';
include 'inc_functions.php';

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
  <title>Add New Expense</title>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" />
  <link rel="stylesheet" type="text/css" href="css/util.css" />
  <style>
    .btn-group {
      display: block !important;
    }

    .btn-group button {
      width: 100% !important;
    }
  </style>
  <?php include 'ajax_uniq_fun.php'; ?>

  <script>
    function CalcTotal() {

      var elamount = document.getElementById("netamount");
      var elvat = document.getElementById("vat");
      var elnonvat = document.getElementById("nonvat");

      var elamoun = document.getElementById("amoun");

      var dblamount = parseFloat(elamount.value);
      var dblvat = parseFloat(elvat.value);
      var dblnonvat = parseFloat(elnonvat.value);

      if (isNaN(dblamount))
        dblamount = 0;
      if (isNaN(dblvat))
        dblvat = 0;
      if (isNaN(dblnonvat))
        dblnonvat = 0;

      var dblAmt = dblamount + dblvat + dblnonvat;
      elamoun.value = dblAmt;
    }
    
    $(document).ready(function() {
      $('#comp').change(function() {
        if ($("#comp option:selected").attr('class') == "vat_yes") {
          $('.div_vat').removeClass('hidden');
          var vat_num = $("#comp option:selected").attr('data-id');
          $('#div_vat_no').removeClass('hidden');
          $('#exp_vat_no').val(vat_num);
        } else {
          $('#div_vat_no').addClass('hidden');
          $('.div_vat').addClass('hidden');
        }
      });
    });
  </script>
</head>

<body>
  <div class="container">
    <?php

    if (isset($_POST['submit'])) {
      $table = 'expence';

      $inv_lastid = $acttObj->max_id('expence') + 1;
      $invoice_no = substr(date('Y'), 2) . date('m') . $inv_lastid;

      $payment_type = $pay_by = trim($_POST['payment_type']);
      $payment_method_id = trim($_POST['payment_through']); // tbl: account_payment_modes.id (bank id OR cash id)
      $type_id = trim($_POST['type_id']);
      $comp = trim($_POST['comp']);
      $billDate = trim($_POST['billDate']);
      $inv_ref_num = mysqli_real_escape_string($con, trim($_POST['inv_ref_num']));
      $details = mysqli_real_escape_string($con, trim($_POST['details']));
      $netamount = trim($_POST['netamount']);
      $vat = trim($_POST['vat']) ?: 0;
      $exp_vat_no = trim($_POST['exp_vat_no']);
      $nonvat = trim($_POST['nonvat']) ?: 0;
      $amoun = trim($_POST['amoun']) ?: 0;
      $payment_date = trim($_POST['payment_date']);
      $is_prepayment = (isset($_POST['is_prepayment']) && $_POST['is_prepayment'] == 1) ? 1 : 0;
      $prepayment_id = $_POST['prepayment_id'];

      $vch = '';
      
      if (isset($_POST['is_payable']) && $_POST['is_payable'] == 1) {
        $pay_by = 'PAYABLE';
        $vch .= 'JV';
        $status = 'unpaid';
      } else {

        // check if its prepayment entry, it will get the payment type from prepayment table
        if (isset($_POST['is_prepayment']) && $_POST['is_prepayment'] == 1) {
          // $prepayment_invoice_no = mysqli_real_escape_string($con, $_POST['prepayment_id']); // pre_payments main ID/Track ID
          // $payment_infos = $acttObj->read_specific('payment_type, payment_method_id', 'pre_payments', 'invoice_no = '.$prepayment_invoice_no);

          // $payment_infos['payment_type'];
          // $payment_method_id = $payment_infos['payment_method_id'];
          $vch .= 'JV';
          $pay_by = $payment_type = '';
        } else {

          $pay_by = $payment_type;
          $status = 'full_paid';

          if($payment_type == 'cash') {
            $vch .= 'CPV';
            $is_bank = '0';
          } else {
            $is_bank = '1';
            $vch .= 'BPV';
          }
        }

      }

      // Getting New Voucher Counter
      $voucher_counter = getNextVoucherCount($vch);
      $voucher = $vch . '-' . $voucher_counter;

      $cond = "invoice_no = '$invoice_no' AND voucher = '$voucher' AND pay_by = '$pay_by' AND type_id = '$type_id' AND comp = '$comp' AND billDate = '$billDate' AND netamount = '$netamount' AND vat = '$vat' AND nonvat = '$nonvat' AND amoun = '$amoun' AND exp_vat_no = '$exp_vat_no'";

      if (isset($_POST['is_prepayment']) && $_POST['is_prepayment'] == 1) {
        $cond .= " AND is_prepayment = '$is_prepayment' AND prepayment_id = '$prepayment_id'";
      }

      // $cond = "(voucher='$voucher' OR TRIM(Replace(Replace(Replace(voucher,'\t',''),'\n',''),'\r',''))='$voucher') AND (pay_by='$pay_by' OR trim(pay_by)='$pay_by') AND (type_id=$type_id OR trim(type_id)='$type_id') AND (comp='$comp' OR TRIM(Replace(Replace(Replace(comp,'\t',''),'\n',''),'\r',''))='$comp') AND (billDate='$billDate' OR trim(billDate)='$billDate') AND (details='$details' OR TRIM(Replace(Replace(Replace(details,'\t',''),'\n',''),'\r',''))='$details') AND (netamount='$netamount' OR trim(netamount)='$netamount') AND (vat='$vat' OR trim(vat)='$vat') AND (nonvat='$nonvat' OR trim(nonvat)='$nonvat') AND (amoun='$amoun' OR trim(amoun)='$amoun') AND (exp_vat_no='$exp_vat_no' OR TRIM(Replace(Replace(Replace(exp_vat_no,'\t',''),'\n',''),'\r',''))='$exp_vat_no')";

      // Check if the expense already exists
      $ch_ex = $acttObj->read_all('*', $table, $cond);
      
      if (mysqli_num_rows($ch_ex) > 0) {
        echo "<div style='text-align:center;margin-top:4rem;' class='alert alert-danger' role='alert'>Record already Exists</div><br>";
        exit;
      } else {

        $insert_data = array(
          'invoice_no' => $invoice_no, // can be use as tracking no
          'voucher' => $voucher,
          'pay_by' => $pay_by,
          'payment_type' => $payment_type,
          'payment_method_id' => $payment_method_id,
          'type_id' => $type_id,
          'comp' => $comp,
          'billDate' => $billDate,
          'details' => $details,
          'netamount' => $netamount,
          'vat' => $vat,
          'nonvat' => $nonvat,
          'amoun' => $amoun,
          'status' => $status,
          'exp_vat_no' => $exp_vat_no,
          'inv_ref_num' => $inv_ref_num,
          'deleted_flag' => 0,
          'posted_by' => $_SESSION['UserName'],
          'dated' => date("Y-m-d H:i:s"),
        );

        // Add conditionally
        if (!isset($_POST['is_payable']) || $_POST['is_payable'] != 1) {
          $insert_data['status'] = 'full_paid';
          $insert_data['amountPaid'] = $amoun;
          $insert_data['is_paid'] = 1;
          $insert_data['paid_by'] = $_SESSION['UserName'];
          $insert_data['paid_on'] = date('Y-m-d H:i:s');
        }

        if (isset($_POST['is_prepayment']) && $_POST['is_prepayment'] == 1 && !empty($prepayment_id)) {
          $insert_data['is_prepayment'] = 1;
          $insert_data['prepayment_id'] = $prepayment_id;
        }

        // Insertion in tbl expence
        $edit_id = $insertion = $acttObj->insert($table, $insert_data, true);
        $acttObj->new_old_table('hist_' . $table, $table, $edit_id);

        // update voucher
        updateVoucherCounter($vch, $voucher_counter);

        if (!isset($_POST['is_payable']) || $_POST['is_payable'] != 1) {
          $expence_partial_payments = array(
            'is_partial' => 0,
            'expence_id' => $insertion,
            'amount' => $amoun,
            'payment_date' => $payment_date, // paid_on
            'payment_type' => $payment_type,
            'payment_method_id' => $payment_method_id,
            'description' => $details,
            'posted_by' => $_SESSION['UserName'],
            'posted_on' => date("Y-m-d H:i:s"),
          );
          $acttObj->insert('expence_partial_payments', $expence_partial_payments, false);
        }

          if ($edit_id && isset($_FILES["exp_receipt"]) && $_FILES["exp_receipt"]["error"] === 0) {
            //$picName = $acttObj->upload_file("expense_receipts", $_FILES["exp_receipt"]["name"], $_FILES["exp_receipt"]["type"], $_FILES["exp_receipt"]["tmp_name"], $edit_id);
            $uploadDir = 'file_folder/expense_receipts/';
            $filename = basename($_FILES['exp_receipt']['name']);
            $targetPath = $uploadDir . $filename;

            if (!is_dir($uploadDir)) {
              mkdir($uploadDir, 0755, true);
            }

            $tmp = $_FILES['exp_receipt']['tmp_name'];

            $allowedExts = array("gif", "jpeg", "jpg", "png", "pdf", "doc", "docx", "rtf", "odt", "txt", "tiff", "bmp", "xls", "xlsx");
            $temp = explode(".", $filename);
            $extension = strtolower(end($temp));

            if (in_array($extension, $allowedExts)) {
              if (move_uploaded_file($tmp, $targetPath)) {
                $picName = $filename;
              } else {
                echo "File upload failed.";
              }
            }
            $acttObj->editFun($table, $edit_id, 'exp_receipt', $picName);
          }
        
        if (isset($_POST['is_prepayment']) && $_POST['is_prepayment'] == 1 && !empty($prepayment_id)) {
          // Build new entry
          $new_entry = array(
              'tbl' => $table,
              'exp_id' => $edit_id
          );

          // Fetch existing history
          $existing_history = $acttObj->read_specific("history", "pre_payments", "invoice_no = '$prepayment_id'")['history'];

          // Decode existing or start fresh array
          $history_array = json_decode($existing_history, true);
          if (!is_array($history_array)) {
              $history_array = [];
          }

          // Append new entry
          $history_array[] = $new_entry;

          // Encode back to JSON
          $updated_history = json_encode($history_array, JSON_UNESCAPED_UNICODE);

          // Update in DB
          $escaped_history = mysqli_real_escape_string($con, $updated_history); // escape if needed
          $acttObj->db_query("UPDATE pre_payments SET history = '$escaped_history' WHERE invoice_no = '$prepayment_id'");
      }


        // this will submit account statements if its not prepayment entry
        if (!isset($_POST['is_prepayment']) && $is_prepayment == 0) {

          if ($edit_id && $amoun > 0) {

            // Account Statement Insertion
            $expense_type = $acttObj->read_specific('title', 'expence_list', ' id = ' . $type_id)['title'];

            $current_date = date("Y-m-d");
            $description = '[Expense] ' . $expense_type;
            if (!empty($details)) {
              $description .= '<br>' . $details;
            }

            $credit_amount = $amoun;

            // getting balance amount
            $res = getCurrentBalances($con);

            // Insertion in tbl account_expenses
            $insert_data = array(
              'invoice_no' => $invoice_no,
              'voucher' => $voucher,
              'dated' => $current_date,
              'company' => $comp,
              'description' => $description,
              'debit' => $credit_amount,
              'balance' => ($res['expense_balance'] + $credit_amount),
              'posted_by' => $_SESSION['userId'],
              'tbl' => $table
            );
            $expense_res = insertAccountExpenses($insert_data);
            //$voucher = $expense_res['voucher'];
            $account_expense_id = $expense_res['account_expense_id'];

            if (isset($_POST['is_payable']) && $_POST['is_payable'] == 1) {

              // Single Entry in tbl account_expenses  (Debit, Balance + Credit_amount)
              // Single Entry in tbl account_payables (Credit, Balance + Credit_amount)

              // getting balance amount
              //$res = getCurrentBalances($con);

              // Insertion in tbl account_receivable
              $insert_data_rec = array(
                'voucher' => $voucher,
                'invoice_no' => $invoice_no,
                'dated' => $current_date,
                'company' => $comp,
                'description' => $description,
                'credit' => $credit_amount,
                'balance' => ($res['payable_balance'] + $credit_amount),
                'posted_by' => $_SESSION['userId'],
                'tbl' => $table
              );

              insertAccountPayables($insert_data_rec);
            } else {

              // ************** Expense is Paid *********************

              // Single Entry in tbl account_expenses  (Debit, Balance + Credit_amount)
              // Single Entry in tbl account_payables (Credit, Balance + Credit_amount)

              // Insertion in tbl account_journal_ledger
              $insert_data_journal = array(
                'is_receivable' => 0,
                'receivable_payable_id' => $account_expense_id,
                'voucher' => $voucher,
                'invoice_no' => $invoice_no,
                'company' => $comp,
                'description' => $description,
                'is_bank' => $is_bank,
                'payment_type' => $payment_type,
                'account_id' => $payment_method_id,
                'dated' => $current_date,
                'credit' => $credit_amount,
                'balance' => ($balance_res['journal_balance'] - $credit_amount),
                'posted_by' => $_SESSION['userId'],
                'posted_on' => date('Y-m-d H:i:s'),
                'tbl' => $table
              );

              insertJournalLedger($insert_data_journal);
            }
          } // If expense insertion successful
      
      } // end if : NOT Prepayment


        // Log the action
        $acttObj->insert("daily_logs", array("action_id" => 3, "user_id" => $_SESSION['userId'], "details" => "Expense ID: " . $edit_id));

    ?>

        <script>
          alert('New expense has been added successfuly.');
          var loadFile = function(event) {
            var output = document.getElementById('output');
            output.src = URL.createObjectURL(event.target.files[0]);
          };
          window.onload = CalcTotal;
          window.onunload = refreshParent;

          function refreshParent() {
            window.opener.location.reload();
          }
        </script>
    <?php }
    } ?>

    <div class="row">
      <form action="" method="post" class="register" id="signup_form" name="signup_form" onsubmit="return formSubmit()" enctype="multipart/form-data">
        <div class="col-sm-12">
          <h1>Enter Expense Details</h1>
        </div>
        <?php
        $inv_lastid = $acttObj->max_id('expence') + 1;
        $invoice_no = substr(date('Y'), 2) . date('m') . $inv_lastid;
        ?>
        <div class="form-group col-sm-3">
          <label> Voucher No * </label>
          <input name="voucher" type="text" class="form-control" placeholder="Voucher No" id="voucher" value="" readonly required />
        </div>
        <div class="form-group col-sm-3">
          <label>Invoice/Reference #</label>
          <input class="form-control " name="inv_ref_num" type="text" placeholder='Enter Invoice or Reference Number' id="inv_ref_num" />
        </div>
        <div class="form-group col-sm-6 payment_type_wrap">
          <label class="pull-left for_payment_type">Payment Type</label>
          <label class="pull-right" style="margin-top: -3px;">
            <input type="checkbox" name="is_prepayment" id="is_prepayment" value="0" /> Prepayment? &nbsp;
            <span id="is_payable_container">
              <input type="checkbox" name="is_payable" id="is_payable" value="1" checked />
              Payable
            </span>
          </label>
          <select class="form-control" id="payment_type" name="payment_type" required>
            <option value="">- Select -</option>
            <option value="bacs" <?php echo ($payment_type == 'bacs') ? 'selected' : ''; ?>>BACS</option>
            <option value="cheque" <?php echo ($payment_type == 'cheque') ? 'selected' : ''; ?>>Cheque</option>
            <option value="card" <?php echo ($payment_type == 'card') ? 'selected' : ''; ?>>Credit/Debit Card</option>
            <option value="cash" <?php echo ($payment_type == 'cash') ? 'selected' : ''; ?>>Cash</option>
          </select>
          <select class="form-control hide" id="prepayment_id" name="prepayment_id">
            <option value="">- Select -</option>
            <?php 
              $prepayments_infos = $acttObj->full_fetch_array("SELECT p.invoice_no, p.voucher, p.total_amount, c.title as category, r.title as receiver,
              (p.total_amount - (SELECT COALESCE(SUM(e.amoun), 0) FROM expence e WHERE e.prepayment_id = p.invoice_no AND e.deleted_flag = 0)) as balance_amount
              FROM pre_payments p
              LEFT JOIN prepayment_categories c ON c.id = p.category_id
              LEFT JOIN prepayment_receivers r ON r.id = p.receiver_id
              WHERE p.deleted_flag = 0 AND p.is_payable = 0 AND 
                p.total_amount > (SELECT COALESCE(SUM(e.amoun), 0) FROM expence e WHERE e.prepayment_id = p.invoice_no AND e.deleted_flag = 0)
              ");
              if(count($prepayments_infos) > 0){
                foreach($prepayments_infos as $prepayments_info){
                  echo '<option value="'.$prepayments_info['invoice_no'].'">
                  [# '.$prepayments_info['invoice_no'] . '] 
                  Receiver: '.$prepayments_info['receiver'].' -  
                  Category: '.$prepayments_info['category'].' 
                  [Bal. Amount: '.$prepayments_info['balance_amount'] .']</option>';
                }
              }
            ?>
          </select>
        </div>
        <div class="form-group col-sm-6 payment_through_wrap hide">
          <label class="pull-left">Payment Method</label>
          <select class="form-control" id="payment_through" name="payment_through">
          </select>
        </div>
        <div class="form-group col-sm-6 bill_date_wrap">
          <label>Bill Date * </label>
          <input class="form-control" name="billDate" type="date" placeholder='' required='' id="billDate" />
        </div>
        <div class="form-group col-sm-3 payment_date_wrap hide">
          <label>Paid On * </label>
          <input class="form-control" name="payment_date" type="date" placeholder='' id="payment_date" />
        </div>
        <div class="form-group col-sm-6">
          <label>Expenses Type * </label><br>

          <select class="form-control" name="type_id" id="type_id" required>
            <?php
            $sql_opt = "SELECT id,title FROM expence_list ORDER BY title ASC";
            $result_opt = mysqli_query($con, $sql_opt);
            $options = "";
            while ($row_opt = mysqli_fetch_array($result_opt)) {
              $exp_id = $row_opt["id"];
              $name_opt = $row_opt["title"];
              $options .= "<OPTION value='$exp_id'>" . $name_opt;
            } ?>
            <option value="">Select Expense Type</option>
            <?php echo $options; ?>
            </option>
          </select>
        </div>
        <div class="form-group col-sm-6">
          <label>Company Name * </label>
          <!-- <input class="form-control" name="comp" type="text" placeholder='' required='' id="comp" /> -->
          <select id="comp" name="comp" class="form-control searchable multi_class" required>
            <?php
            $sql_opt = "SELECT id,sp_name,sp_abrv,tax_reg,uk_citizen,uk_citizen_vatNum,country_vat,country_vatNum FROM sup_reg where deleted_flag=0 ORDER BY sp_name ASC";
            $result_opt = mysqli_query($con, $sql_opt);
            $options = "";
            while ($row_opt = mysqli_fetch_array($result_opt)) {
              $code = $row_opt["id"];
              $name_opt = $row_opt["sp_name"];
              $tax_reg = $row_opt["tax_reg"];
              $uk_citizen = $row_opt["uk_citizen"];
              $uk_citizen_vatNum = $row_opt["uk_citizen_vatNum"];
              $country_vat = $row_opt["country_vat"];
              $country_vatNum = $row_opt["country_vatNum"];

              $options .= "<OPTION value='$name_opt' class='vat_" . ($tax_reg == 1 ? 'yes' : 'no') . "' data-id='" . (trim($uk_citizen_vatNum) != '' ? $uk_citizen_vatNum : $country_vatNum) . "'>" . $name_opt;
            }
            ?>
            <option value="">Select Supplier</option>
            <?php echo $options; ?>
            </option>
          </select>
        </div>

        <div class="form-group col-sm-12">
          <textarea class="form-control" name="details" rows="3" placeholder='Write expense details here ...' id="details"></textarea>
        </div>
        <div class="form-group col-sm-3 col-xs-6">
          <label>Net Amount *</label>
          <input class="form-control" name="netamount" oninput="CalcTotal();" type="text" placeholder='' required='' id="netamount" value="0" />
        </div>
        <div class="form-group col-sm-3 col-xs-6 div_vat hidden">
          <label>VAT * </label>
          <input class="form-control " name="vat" oninput="CalcTotal();" type="text" placeholder='' id="vat" value="0" />
        </div>
        <div class="form-group col-sm-3 col-xs-6">
          <label>Non VAT *</label>
          <input class="form-control" name="nonvat" oninput="CalcTotal();" type="text" placeholder='' id="nonvat" value="0" />
        </div>
        <div class="form-group col-sm-3 col-xs-6">
          <label>Total Amount </label>
          <input class="form-control" name="amoun" type="text" placeholder='' readonly="readonly" required='' id="amoun" value="0" />
        </div>
        <div class="form-group col-sm-4 col-xs-12 hidden" id="div_vat_no">
          <label>VAT Number / Tax Number </label>
          <input class="form-control" name="exp_vat_no" type="text" placeholder='Enter VAT Number' id="exp_vat_no" />
        </div>
        <div class="form-group col-sm-4 col-xs-12 " id="div_receipt">
          <label>Attach Receipt </label>
          <input class="form-control" name="exp_receipt" type="file" id="exp_receipt" />
        </div>
        <div class="form-group col-sm-12 col-xs-12 text-right">
          <button class="btn btn-primary" type="submit" name="submit" onclick="return formSubmit(); return false">Submit Expense</button>
        </div>
      </form>
    </div>

  </div>

  <!-- Modal --- Used for Payment Methods (dropdown) -->
  <div id="myModal2" class="modal fade" role="dialog">
    <div class="modal-dialog">

      <!-- Modal content-->
      <div class="modal-content modal-md">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Add New Payment Method</h4>
        </div>
        <div class="modal-body">
          <div class="modal_details"></div>
        </div>
      </div>

    </div>
  </div>


  <script src="js/jquery-1.11.3.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.13/css/bootstrap-multiselect.css" rel="stylesheet" type="text/css" />
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.13/js/bootstrap-multiselect.js" type="text/javascript"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>

  <script src="js/income_receive_amount.js"></script>

  <script>
    $(document).ready(function() {
      

      // prepayment_id change event: call AJAX to get payment_type
      // $('#prepayment_id').change(function () {
      //   const prepaymentId = $(this).val();

      //   if (prepaymentId !== '') {
      //     $.ajax({
      //       url: './ajax_functions.php',
      //       type: 'GET',
      //       data: {
      //         action: 'get_prepayment_payment_type',
      //         id: prepaymentId
      //       },
      //       success: function (response) {
      //         if (response) {
      //           generateVoucherNo(0, response);
      //         } else {
      //           alert("Invalid response from server.");
      //         }
      //       },
      //       error: function () {
      //         alert("Failed to fetch prepayment payment type.");
      //       }
      //     });
      //   }
      // });

      // onpage load
      if ($('#is_payable').is(':checked')) {
        generateVoucherNo(1, $('#payment_type').val());
        $('#is_payable').val('1');
        $('#payment_type').attr('disabled', true);
      }
      // else {
      //   generateVoucherNo(0, $('#payment_type').val());
      //   $('#is_payable').val('0');
      //   $('#payment_type').attr('disabled', false);
      // }

      $('#is_payable').change(function() {
        if ($(this).is(':checked')) {
          generateVoucherNo(1, $('#payment_type').val());
          $(this).val('1');
          $('#payment_type').attr('disabled', true);
          $('.payment_through_wrap').addClass('hide').css('display', 'none');
          $('#payment_through').attr("required", false);
          $('#payment_type').val('').prop("selectedIndex", 0);

          $('.payment_date_wrap').addClass('hide');
          $('#payment_date').attr('required', false);

          $('.bill_date_wrap').removeClass('col-sm-3').addClass('col-sm-6');
        } else {
          generateVoucherNo(0, $('#payment_type').val());
          $(this).val('0');
          $('#payment_type').attr('disabled', false);
          $('.payment_through_wrap').removeClass('hide').css('display', 'block');
          $('#payment_through').attr("required", true);

          $('.payment_date_wrap').removeClass('hide');
          $('#payment_date').attr('required', true);

          $('.bill_date_wrap').removeClass('col-sm-6').addClass('col-sm-3');
        }
      });

      // is_prepayment change event
      $('#is_prepayment').change(function () {
        if ($(this).is(':checked')) {
          $(this).val('1');

          // Hide is_payable and trigger change
          $('#is_payable').prop('checked', false).val('0');//.trigger('change');
          $('#is_payable_container').hide();

          // Hide and unrequire payment_type
          $('#payment_type').addClass('hide').removeAttr('required');

          // Show and require prepayment_id
          $('#prepayment_id').removeClass('hide').attr('required', true);

          // Change label text
          $('.for_payment_type').text('Prepayment Reference');

          // Hide payment_through_wrap and remove required
          $('.payment_through_wrap').addClass('hide').css('display', 'none');
          $('#payment_through').removeAttr('required');

          // Optional: Generate a placeholder voucher with type '-'
          generateVoucherNo(1, '-');
          
        } else {
          $(this).val('0');

          // Restore is_payable
          $('#is_payable').prop('checked', true).val('1');//.trigger('change');
          $('#is_payable_container').show();

          // Show and require payment_type
          $('#payment_type').removeClass('hide').attr('required', true);

          // Hide and unrequire prepayment_id
          $('#prepayment_id').addClass('hide').removeAttr('required');

          // Reset label text
          $('.for_payment_type').text('Payment Type');
        }
      });

      $('#payment_type').change(function(e) {
        generateVoucherNo(0, $('#payment_type').val());
      });

    });
    
    function formSubmit() {
      if ($('#comp').val() == '') {
        alert('Please select Company/Supplier');
        return false;
      }
      $("#signup_form").submit();
    }

    $(function() {
      $('.searchable').multiselect({
        includeSelectAllOption: true,
        numberDisplayed: 1,
        enableFiltering: true,
        enableCaseInsensitiveFiltering: true
      });
    });
  </script>
</body>

</html>