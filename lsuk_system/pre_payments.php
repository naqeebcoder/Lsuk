<?php 
if (session_id() == '' || !isset($_SESSION)) {
  session_start();
}

include 'db.php';
include 'class.php';
include 'inc_functions.php';

$allowed_type_idz = "243";
//Check if user has current action allowed
if ($_SESSION['is_root'] == 0) {
  $get_page_access = $acttObj->read_specific("id", "action_permissions", "user_id=" . $_SESSION['userId'] . " AND action_id IN (" . $allowed_type_idz . ")")['id'];
  if (empty($get_page_access)) {
    die("<center><h2 class='text-center text-danger'>You do not have access to <u>Edit Company Expense</u> action!<br>Kindly contact admin for further process.</h2></center>");
  }
}

$action = $_GET['act'];

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
  <title>Prepayments</title>
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

      var elamoun = document.getElementById("total_amount");

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
  </script>
</head>

<body>
  <div class="container">
    <?php

    if (isset($_POST['submit'])) {
      $table = 'pre_payments';

      $inv_lastid = $acttObj->max_id($table) + 1;
      $invoice_no =  substr(date('Y'), 2) . date('m') . $inv_lastid;

      $is_payable = (isset($_POST['is_payable'])) ? 1 : 0;

      $payment_type = trim($_POST['payment_type']);
      $payment_method_id = ($is_payable == 0) ? trim($_POST['payment_through']) : ''; // tbl: account_payment_modes.id (bank id OR cash id)
      
      $category_id = trim($_POST['category_id']);
      if ($category_id === "new") {
        // New company fields
        $new_category_title = mysqli_real_escape_string($con, $_POST['new_category_title']);

        $check_exists = $acttObj->read_all('COUNT(*) as count', 'prepayment_categories', ' title = "'.$new_category_title.'" AND status = 1')['count'];
        if($check_exists > 0){
          die("Category already exists.");
          exit;
        }
        
        // Insert new company
        $insert_category = "INSERT INTO prepayment_categories (title, status) VALUES ('$new_category_title', 1)";
        if (mysqli_query($con, $insert_category)) {
          $category_id = mysqli_insert_id($con);
        } else {
          die("Category creation failed: " . mysqli_error($con));
        }
      }

      $receiver_id = trim($_POST['receiver_id']);
      if ($receiver_id === "new") {
        // New company fields
        $new_receiver_name = mysqli_real_escape_string($con, $_POST['new_receiver_name']);
        $new_receiver_email = mysqli_real_escape_string($con, $_POST['new_receiver_email']);
        $new_receiver_phone = mysqli_real_escape_string($con, $_POST['new_receiver_phone']);
        $new_receiver_address = mysqli_real_escape_string($con, $_POST['new_receiver_address']);
        $new_receiver_note = mysqli_real_escape_string($con, $_POST['new_receiver_note']);

        $check_exists_recr = $acttObj->read_all('COUNT(*) as count', 'prepayment_receivers', ' title = "'.$new_receiver_name.'", email = "'.$new_receiver_email.'", contact = "'.$new_receiver_phone.'" AND deleted_flag = 0')['count'];
        if($check_exists_recr > 0){
          die("Receiver already exists.");
          exit;
        }
        
        // Insert new company
        $insert_receiver = "INSERT INTO prepayment_receivers (title, contact, email, address, details, submitted_by, dated) VALUES ('$new_receiver_name', '$new_receiver_phone', '$new_receiver_email','$new_receiver_address', '".$new_receiver_note."', '".$_SESSION['UserName']."', '".date('Y-m-d H:i:s')."')";
        if (mysqli_query($con, $insert_receiver)) {
          $receiver_id = mysqli_insert_id($con);
        } else {
          die("Receiver creation failed: " . mysqli_error($con));
        }
      }

      $no_of_installment = ($_POST['no_of_installment']) ? trim($_POST['no_of_installment']) : '';
      $frequency = trim($_POST['frequency']);
      $payment_date = trim($_POST['payment_date']);
      $details = mysqli_real_escape_string($con, trim($_POST['description']));
      $net_amount = trim($_POST['netamount']);
      $vat = trim($_POST['vat']) ?: 0;
      $nonvat = trim($_POST['nonvat']) ?: 0;
      $total_amount = trim($_POST['total_amount']) ?: 0;
      
      $vch = '';
      if (isset($_POST['is_payable']) && $_POST['is_payable'] == 1) {
        $vch .= 'JV';
        $status = 'payable';
      } else {
        $status = 'paid';
        if ($_POST['payment_type'] == 'cash') {
          $vch .= 'CPV';
          $is_bank = '0';
        } else {
          $is_bank = '1';
          $vch .= 'BPV';
        }
      }
      
      // Getting New Voucher Counter
      $voucher_counter = getNextVoucherCount($vch);
      $voucher = $vch . '-' . $voucher_counter;
      
      $cond = "category_id = '$category_id' AND receiver_id = '$receiver_id' AND payment_date = '$payment_date' AND no_of_installment = '$no_of_installment' AND frequency = '$frequency' AND net_amount = '$net_amount' AND vat = '$vat' AND nonvat = '$nonvat' AND total_amount = '$total_amount' AND payment_type = '$payment_type' AND payment_method_id = '$payment_method_id'";

      // Check if the expense already exists
      $ch_ex = $acttObj->read_all('*', $table, $cond);
      
      if (mysqli_num_rows($ch_ex) > 0) {
        echo "<div style='text-align:center;margin-top:4rem;' class='alert alert-danger' role='alert'>Record already Exists</div><br>";
        exit;
      } else {

        $insert_data = array(
          'invoice_no' => $invoice_no, // can be use as tracking no
          'voucher' => $voucher,
          'is_payable' => $is_payable,
          'payment_type' => $payment_type,
          'payment_method_id' => $payment_method_id,
          'category_id' => $category_id,
          'receiver_id' => $receiver_id,
          'no_of_installment' => $no_of_installment,
          'frequency' => $frequency,
          'payment_date' => $payment_date,
          'description' => $details,
          'net_amount' => $net_amount,
          'vat' => $vat,
          'nonvat' => $nonvat,
          'total_amount' => $total_amount,
          'status' => $status,
          'dated' => date("Y-m-d H:i:s"),
          'posted_by' => $_SESSION['UserName'],
        );

        if($is_payable == 0){
          $insert_data['paid_amount'] = $total_amount;
          $insert_data['is_paid'] = 1;
          $insert_data['paid_by'] = $_SESSION['UserName'];
          $insert_data['paid_on'] = date('Y-m-d H:i:s');
        }

        // Insertion in tbl expence
        $edit_id = $insertion = $acttObj->insert($table, $insert_data, true);

        if ($edit_id) {

          // Updating the new Voucher Counter
          updateVoucherCounter($vch, $voucher_counter);

          if (isset($_FILES["exp_receipt"]) && $_FILES["exp_receipt"]["error"] === 0) {
            $uploadDir = 'file_folder/pre_payments/';
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
            $acttObj->editFun($table, $edit_id, 'attachment', $picName);
          }

          if ($total_amount > 0) {

              // Account Statement Insertion
              $category_title = $acttObj->read_specific('title', 'prepayment_categories', ' id = ' . $category_id)['title'];
              $receiver_name = $acttObj->read_specific('title', 'prepayment_receivers', ' id = ' . $receiver_id)['title'];

              $current_date = date("Y-m-d");
              $description = '[Prepayment] ' . $category_title;
              if (!empty($details)) {
                $description .= '<br>Details: ' . $details;
              }

              $credit_amount = $total_amount;

              // getting balance amount
              $res = getCurrentBalances($con);

              if (isset($_POST['is_payable']) && $_POST['is_payable'] == 1) {

                // Single Entry in tbl account_expenses  (Debit, Balance + Credit_amount)
                // Single Entry in tbl account_payables (Credit, Balance + Credit_amount)

                // Insertion in tbl account_receivable
                $insert_data_rec = array(
                  'voucher' => $voucher,
                  'invoice_no' => $invoice_no,
                  'dated' => $current_date,
                  'company' => $receiver_name,
                  'description' => $description,
                  'credit' => $credit_amount,
                  'balance' => ($res['payable_balance'] + $credit_amount),
                  'posted_by' => $_SESSION['userId'],
                  'tbl' => $table
                );

                insertAccountPayables($insert_data_rec);

              } else {
              
                // Insertion in tbl account_journal_ledger
                $insert_data_journal = array(
                  'is_receivable' => 2, // prepayments
                  'receivable_payable_id' => $edit_id,
                  'voucher' => $voucher,
                  'invoice_no' => $invoice_no,
                  'company' => $receiver_name,
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
        }

        // Log the action
        $acttObj->insert("daily_logs", array("action_id" => 54, "user_id" => $_SESSION['userId'], "details" => "Prepayment ID: " . $edit_id));

    ?>

        <script>
          alert('Record successfuly Added.');
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
        <div class="col-sm-12 m-b-20">
          <h1>Enter Pre-payment Details</h1>
        </div>
        <div class="form-group col-sm-3">
          <label> Voucher No * </label>
          <input name="voucher" type="text" class="form-control" placeholder="Voucher No" id="voucher" value="" readonly required />
        </div>
        <div class="form-group col-sm-3">
          <label>Payment Date * </label>
          <input class="form-control" name="payment_date" type="date" placeholder="Payment Date" required id="payment_date" />
        </div>
        <div class="form-group col-sm-6 payment_type_wrap">
          <label class="pull-left">Payment Type *</label>
          <label class="pull-right" style="margin-top: -3px;">
            <input type="checkbox" name="is_payable" id="is_payable" value="0" />
            Payable
            </a>
          </label>
          <select class="form-control" id="payment_type" name="payment_type" required>
            <option value="">- Select -</option>
            <option value="bacs" <?php echo ($payment_type == 'bacs') ? 'selected' : ''; ?>>BACS</option>
            <option value="cheque" <?php echo ($payment_type == 'cheque') ? 'selected' : ''; ?>>Cheque</option>
            <option value="card" <?php echo ($payment_type == 'card') ? 'selected' : ''; ?>>Credit/Debit Card</option>
            <option value="cash" <?php echo ($payment_type == 'cash') ? 'selected' : ''; ?>>Cash</option>
          </select>
        </div>
        <div class="form-group col-sm-6 payment_through_wrap">
          <label class="pull-left">Payment Method *</label>
          <select class="form-control" id="payment_through" name="payment_through">
          </select>
        </div>
        
        <div class="form-group col-sm-6">
          <label>Category * </label><br>
          <select id="category_id" name="category_id" class="form-control searchable multi_class" onchange="checkCategory(this)">
            <option value="">Select or Add New</option>
            <option value="new">➕ Add New Category</option>
            <?php
              $sql_opt = "SELECT id, title FROM prepayment_categories ORDER BY title ASC";
              $result_opt = mysqli_query($con, $sql_opt);
              while ($row_opt = mysqli_fetch_array($result_opt)) {
                echo "<option value='{$row_opt['id']}'>
                    {$row_opt['title']}
                  </option>";
              }
            ?>
          </select>
        </div>
        
        <!-- Manual Entry Fields (Hidden Initially) -->
        <div id="newCompanyFields" style="display:none;">
          <div class="form-group col-sm-6">
            <label>Category Title *</label>
            <input type="text" class="form-control" name="new_category_title" id="new_category_title" placeholder="Enter Category Title">
          </div>
        </div>

        <div class="form-group col-sm-6">
          <label>Receiver  * </label>
          <select id="receiver_id" name="receiver_id" class="form-control searchable multi_class" onchange="checkReceiver(this)">
            <option value="">Select or Add New</option>
            <option value="new">➕ Add New Receiver</option>
            <?php
              $sql_opt = "SELECT * FROM prepayment_receivers ORDER BY title ASC";
              $result_opt = mysqli_query($con, $sql_opt);
              while ($row_opt = mysqli_fetch_array($result_opt)) {
                echo "<option value='{$row_opt['id']}'>
                    {$row_opt['title']}
                  </option>";
              }
            ?>
          </select>
        </div>
        <!-- Manual Entry Fields (Hidden Initially) -->
        <div id="newReceiverFields" style="display:none;" class="col-sm-12">
          <div class="panel panel-default">
            <div class="panel-heading">
              <h3 class="m-t-0 m-b-0">Receiver Details</h3>
            </div>
            <div class="panel-body">
              <div class="form-group col-sm-4">
                <label>Full Name *</label>
                <input type="text" class="form-control" name="new_receiver_name" id="new_receiver_name" placeholder="Enter Full Name">
              </div>
              <div class="form-group col-sm-4">
                <label>Email *</label>
                <input type="text" class="form-control" name="new_receiver_email" id="new_receiver_email" placeholder="Enter Email">
              </div>
              <div class="form-group col-sm-4">
                <label>Phone *</label>
                <input type="text" class="form-control" name="new_receiver_phone" id="new_receiver_phone" placeholder="Enter Phone">
              </div>
              <div class="form-group col-sm-12">
                <label>Address *</label>
                <input type="text" class="form-control" name="new_receiver_address" id="new_receiver_address" placeholder="Enter Address">
              </div>
              <div class="form-group col-sm-12">
                <label>Note <em class="small">(optional)</em></label>
                <textarea class="form-control" name="new_receiver_note" id="new_receiver_note" placeholder="Note"></textarea>
              </div>
            </div>
          </div>          
        </div>

        <div class="form-group col-sm-12">
          <textarea class="form-control" name="description" rows="3" placeholder='Description' id="description"></textarea>
        </div>
        <div class="col-sm-12">
          <div class="panel panel-default">
            <div class="panel-heading">
              <label>Recovery Details</label>
            </div>
            <div class="panel-body">
              <div class="row">
                <div class="form-group col-sm-6">
                  <label>No of Installment</label>
                  <input class="form-control" name="no_of_installment" type="number" placeholder="No of Installment" id="no_of_installment" />
                </div>
                <div class="form-group col-sm-6">
                  <label>Frequency</label>
                    <?php $frequencies = ['daily', 'weekly', 'biweekly', 'semimonthly', 'monthly', 'quarterly', 'annually']; ?>

                  <select class="form-control" id="frequency" name="frequency">
                    <option value="">- Frequency -</option>
                      <?php foreach ($frequencies as $freq): ?>
                          <option value="<?php echo $freq; ?>" <?php echo ($frequency == $freq) ? 'selected' : ''; ?>>
                              <?php echo ucfirst($freq); ?>
                          </option>
                      <?php endforeach; ?>
                  </select>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="form-group col-sm-3 col-xs-6">
          <label>Net Amount *</label>
          <input class="form-control" name="netamount" oninput="CalcTotal();" type="text" placeholder='' required='' id="netamount" value="0" />
        </div>
        <div class="form-group col-sm-3 col-xs-6">
          <label>VAT * </label>
          <input class="form-control " name="vat" oninput="CalcTotal();" type="text" placeholder='' id="vat" value="0" />
        </div>
        <div class="form-group col-sm-3 col-xs-6">
          <label>Non VAT *</label>
          <input class="form-control" name="nonvat" oninput="CalcTotal();" type="text" placeholder='' id="nonvat" value="0" />
        </div>
        <div class="form-group col-sm-3 col-xs-6">
          <label>Total Amount </label>
          <input class="form-control" name="total_amount" type="text" placeholder='' readonly="readonly" required='' id="total_amount" value="0" />
        </div>
        <div class="form-group col-sm-6 col-xs-12 " id="div_receipt">
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

      if ($('#is_payable').is(':checked')) {
        generateVoucherNo(1, $('#payment_type').val());
        $('#is_payable').val('1');
        $('#payment_type').attr('disabled', true);
      }else {
        $('#payment_type').prop('selectedIndex', 1).trigger('change');
        generateVoucherNo(0, 'bank');
      }

      $('#is_payable').change(function() {
        if ($(this).is(':checked')) {
          generateVoucherNo(1, $('#payment_type').val());
          $(this).val('1');
          $('#payment_type').attr('disabled', true);
          $('.payment_through_wrap').addClass('hide').css('display', 'none');
          $('#payment_through').attr("required", false);
          $('#payment_type').val('').prop("selectedIndex", 0);
        } else {
          generateVoucherNo(0, $('#payment_type').val());
          $(this).val('0');
          $('#payment_type').attr('disabled', false).prop("selectedIndex", 1);
          $('.payment_through_wrap').removeClass('hide').css('display', 'block');
          $('#payment_through').attr("required", true);
        }
      });

      $('#payment_type').change(function(e) {
        generateVoucherNo(0, $('#payment_type').val());
      });

    });
    
    function formSubmit() {
      if ($('#comp').val() == '') {
        alert('Please select Receiver');
        return false;
      }
      $("#signup_form").submit();
    }

    $(function() {

      $('.searchable option[value="add_new"]').each(function () {
        $(this).html($(this).text() + ' <i class="fa fa-plus"></i>');
    });

      $('.searchable').multiselect({
        includeSelectAllOption: true,
        numberDisplayed: 1,
        enableFiltering: true,
        enableCaseInsensitiveFiltering: true
      });
    });

    function checkCategory(select) {
      let selectedOption = select.options[select.selectedIndex];
      let newCompanyFields = document.getElementById("newCompanyFields");

      let newComp = document.getElementById("new_category_title");
      
      if (selectedOption.value === "new") {
        // Show manual entry fields
        newCompanyFields.style.display = "block";
        // Make fields required
        newComp.setAttribute("required", "required");

      } else {
        // Hide manual entry fields
        newCompanyFields.style.display = "none";
        newComp.removeAttribute("required");
      }
    }
    

    function checkReceiver(select) {
      let selectedOption = select.options[select.selectedIndex];
      let newCompanyFields = document.getElementById("newReceiverFields");

      let receiverName = document.getElementById("new_receiver_name");
      let receiverEmail = document.getElementById("new_receiver_email");
      let receiverPhone = document.getElementById("new_receiver_phone");
      let receiverAddress = document.getElementById("new_receiver_address");

      if (selectedOption.value === "new") {
        // Show manual entry fields
        newCompanyFields.style.display = "block";
        // Make fields required
        receiverName.setAttribute("required", "required");
        receiverEmail.setAttribute("required", "required");
        receiverPhone.setAttribute("required", "required");
        receiverAddress.setAttribute("required", "required");
      } else {
        // Hide manual entry fields
        newCompanyFields.style.display = "none";
        receiverName.removeAttribute("required");
        receiverEmail.removeAttribute("required");
        receiverPhone.removeAttribute("required");
        receiverAddress.removeAttribute("required");
      }
    }
  </script>
</body>

</html>