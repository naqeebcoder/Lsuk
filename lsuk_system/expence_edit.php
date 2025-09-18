<?php if (session_id() == '' || !isset($_SESSION)) {
  session_start();
}
include 'db.php';
include 'class.php';
$table = 'expence';

$allowed_type_idz = "95";
//Check if user has current action allowed
if ($_SESSION['is_root'] == 0) {
  $get_page_access = $acttObj->read_specific("id", "action_permissions", "user_id=" . $_SESSION['userId'] . " AND action_id IN (" . $allowed_type_idz . ")")['id'];
  if (empty($get_page_access)) {
    die("<center><h2 class='text-center text-danger'>You do not have access to <u>Edit Company Expense</u> action!<br>Kindly contact admin for further process.</h2></center>");
  }
}
$edit_id = @$_GET['edit_id'];
$query = "SELECT * FROM $table WHERE id = $edit_id";
$result = mysqli_query($con, $query);
$row = mysqli_fetch_array($result);
$comp = $row['comp'];
$amoun = $row['amoun'];
$billDate = $row['billDate'];
$details = $row['details'];
$invoice_no = $row['invoice_no'];

if ($row['status'] == 'full_paid') {
  $payment_paid_date = $acttObj->read_specific('payment_date', 'expence_partial_payments', 'expence_id = ' . $edit_id)['payment_date'];
  if (!empty($payment_paid_date)) {
    $payment_paid_date = $payment_paid_date;
  } else {
    $payment_paid_date = date('Y-m-d', strtotime($row['paid_on']));
  }
}


// Generating Invoice No for Old records using Voucher No, if invoice no is empty
// if (empty($row['invoice_no']) && !empty($row['voucher']) && !empty($row['dated'])) {
//   $invoice_no = $acttObj->generate_expense_invoice_no($row['voucher'], $row['dated']);
// } else {
//   $invoice_no = $row['invoice_no'];
// }

$voucher = $row['voucher'];
$netamount = $row['netamount'];
$vat = $row['vat'];
$nonvat = $row['nonvat'];
$inv_ref_num = $row['inv_ref_num'];

if ($row['pay_by'] == 'bacs') {
  $pay_by = 'BACS';
} else {
  $pay_by = $row['pay_by'] ? ucwords(strtolower($row['pay_by'])) : 'N/A';
}

$exp_receipt = $row['exp_receipt'];
$exp_vat_no = $row['exp_vat_no'];
$type_id = $row['type_id'];
$vat_sup_num = '';
$sup_reg_id = 0;

// $check= "SELECT uk_citizen_vatNum,country_vatNum FROM sup_reg where TRIM(LOWER(sp_name))='".mysqli_real_escape_string($con,trim(strtolower($comp))) ."' ";
$query2 = mysqli_query($con, "SELECT id,uk_citizen_vatNum,country_vatNum FROM sup_reg where TRIM(LOWER(sp_name))='" . mysqli_real_escape_string($con, trim(strtolower($comp))) . "' ");
// echo $check;die();exit();
if (mysqli_num_rows($query2) > 0) {
  $result2 = mysqli_fetch_array($query2);
  $sup_reg_id = $result2['id'];
  if ($result2['uk_citizen_vatNum'] != '') {
    $vat_sup_num = $result2['uk_citizen_vatNum'];
  } elseif ($result2['country_vatNum'] != '') {
    $vat_sup_num = $result2['country_vatNum'];
  }
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
  <title>Edit Expense</title>
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
    // function fun_vat_no(){
    //     var vat=document.getElementById("vat").value;
    //     var exp_vat_no=document.getElementById("exp_vat_no");
    //     var div_vat_no=document.getElementById("div_vat_no");
    //     if (!isNaN(vat) && vat!=0){
    //         div_vat_no.style.display='inline';
    //         exp_vat_no.setAttribute("required", "required");
    //     }else{
    //         div_vat_no.style.display='none';
    //         exp_vat_no.removeAttribute("required", "required");
    //     }
    // }
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

      $('#pay_by').change(function(e) {
        // e.preventDefault();
        var typ = this.value;
        var vch_ext = '';
        if (typ == 'CASH') {
          vch_ext = 'CPV';
        } else if (typ == 'BANK') {
          vch_ext = 'BPV';
        } else if (typ == 'PAYABLE') {
          vch_ext = 'JV';
        } else if (typ == 'CREDIT_CARD') {
          vch_ext = 'JV';
        }
        var vch = $('#voucher').val().split('-');
        var new_vch = vch[0] + '-' + vch[1] + '-' + vch_ext;
        $('#voucher').val(new_vch);
      });

    });
  </script>
</head>

<body>
  <div class="container">
    <?php
    if (isset($_POST['submit'])) {
      $table = 'expence';

      $type_id = $_POST['type_id'];
      $comp = trim($_POST['comp']);
      $billDate = $_POST['billDate'];
      $inv_ref_num = mysqli_real_escape_string($con, trim($_POST['inv_ref_num']));
      $details = mysqli_real_escape_string($con, trim($_POST['details']));
      $netamount = $_POST['netamount'];
      $vat = $_POST['vat'] ?: 0;
      $exp_vat_no = $_POST['exp_vat_no'];
      $nonvat = $_POST['nonvat'] ?: 0;
      $amoun = $_POST['amoun'] ?: 0;
      $prepayment_id = $_POST['prepayment_id'];

      $old_prepayment_id = $row['prepayment_id']; // old one from DB

      if ($old_prepayment_id != $prepayment_id) {

          // 1. Remove from OLD prepayment history
          if (!empty($old_prepayment_id)) {
              $old_history_json = $acttObj->read_specific(
                  "history", 
                  "pre_payments", 
                  "invoice_no = '$old_prepayment_id'"
              )['history'];

              $old_history_array = json_decode($old_history_json, true) ?: [];

              $old_history_array = array_filter($old_history_array, function ($item) use ($table, $edit_id) {
                  return !($item['tbl'] == $table && $item['exp_id'] == $edit_id);
              });

              $escaped_old_history = mysqli_real_escape_string(
                  $con, 
                  json_encode(array_values($old_history_array), JSON_UNESCAPED_UNICODE)
              );

              $acttObj->db_query(
                  "UPDATE pre_payments SET history = '$escaped_old_history' WHERE invoice_no = '$old_prepayment_id'"
              );
          }

          // 2. Add to NEW prepayment history (always, no is_prepayment check)
          if (!empty($prepayment_id)) {
              $new_history_json = $acttObj->read_specific(
                  "history", 
                  "pre_payments", 
                  "invoice_no = '$prepayment_id'"
              )['history'];

              $new_history_array = json_decode($new_history_json, true) ?: [];

              $new_history_array[] = [
                  'tbl'    => $table,
                  'exp_id' => $edit_id
              ];

              $escaped_new_history = mysqli_real_escape_string(
                  $con, 
                  json_encode($new_history_array, JSON_UNESCAPED_UNICODE)
              );

              $acttObj->db_query(
                  "UPDATE pre_payments SET history = '$escaped_new_history' WHERE invoice_no = '$prepayment_id'"
              );
          }
      }

      $insert_data = array(
        //'invoice_no' => $row['invoice_no'],
        'type_id' => $type_id,
        'comp' => $comp,
        'billDate' => $billDate,
        'details' => $details,
        'exp_vat_no' => $exp_vat_no,
        'inv_ref_num' => $inv_ref_num,
        'netamount' => $netamount,
        'vat' => $vat,
        'nonvat' => $nonvat,
        'prepayment_id' => $prepayment_id,
        'edited_by' => $_SESSION['UserName'],
        'edited_date' => date("Y-m-d H:i:s"),
      );

      $update_res = $acttObj->update($table, $insert_data, ' id = ' . $edit_id);
      $acttObj->editFun('sup_reg', $sup_reg_id, 'uk_citizen_vatNum', $exp_vat_no);

      if ($row['status'] == 'full_paid') {
        $acttObj->update('expence_partial_payments', array('payment_date' => $_POST['payment_date']), ' is_partial = 0 AND expence_id = ' . $edit_id);
      }

      if (isset($_FILES["exp_receipt"]) && !empty($_FILES["exp_receipt"]["name"]) && $_FILES["exp_receipt"]["error"] === 0) {
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
      } else {
        $picName = $exp_receipt;
      }

      $acttObj->editFun($table, $edit_id, 'exp_receipt', $picName);

      $acttObj->new_old_table('hist_' . $table, $table, $edit_id);

      $acttObj->insert("daily_logs", array("action_id" => 4, "user_id" => $_SESSION['userId'], "details" => "Expense ID: " . $edit_id));

    ?>
      <script>
        alert('Expense has been updated successfuly !');
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

    <?php } ?>

    <form action="" method="post" class="register" id="signup_form" name="signup_form" onsubmit="return formSubmit()" enctype="multipart/form-data">
      <h1 class="m-b-30">Update Expense Details (Track# <?php echo $invoice_no; ?>)</h1>

      <div class="row">
        <div class="form-group col-sm-3">
          <label> Voucher No * </label>
          <div class="form-control">
            <?php echo $voucher; ?>
          </div>
        </div>
        <div class="form-group col-sm-3">
          <label>Invoice/Reference #</label>
          <input class="form-control " name="inv_ref_num" type="text" placeholder='Invoice/Reference #' id="inv_ref_num" value="<?php echo $inv_ref_num; ?>" />
        </div>
        <div class="form-group col-sm-6">
          <label><?php echo $row['is_prepayment'] ? 'Prepayment Reference' : 'Payment By'; ?></label>
          <div class="form-control <?php echo $row['is_prepayment'] ? 'hide' : ''; ?>">
            <?php echo $pay_by; ?>
          </div>
          <select class="form-control <?php echo $row['is_prepayment'] ? '' : 'hide'; ?>" id="prepayment_id" name="prepayment_id">
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
                  $selected = ($row['prepayment_id'] == $prepayments_info['invoice_no']) ? 'selected' : '';
                  echo '<option value="'.$prepayments_info['invoice_no'].'" '.$selected.'>
                  [# '.$prepayments_info['invoice_no'] . '] 
                  Receiver: '.$prepayments_info['receiver'].' -  
                  Category: '.$prepayments_info['category'].' 
                  [Bal. Amount: '.$prepayments_info['balance_amount'] .']</option>';
                }
              }
            ?>
          </select>
        </div>
        <?php
        if (!empty($row['payment_method_id'])) {
          $bank_info = $acttObj->read_specific("name as bank_name, account_no, sort_code, iban_no", "account_payment_modes", " id = " . $row['payment_method_id']);
        ?>
          <div class="form-group col-sm-6 <?php echo $row['is_prepayment'] ? 'hide' : ''; ?>">
            <label>Payment Method </label>
            <div class="form-control">
              <?php echo $bank_info['bank_name'] . ' - ' . $bank_info['account_no']; ?>
            </div>
          </div>
        <?php } ?>
        <!-- <div class="form-group col-sm-6">
        <label>Payment By </label>

        <select class="form-control" name="pay_by" id="pay_by" required>
          <option value="<?php echo $pay_by; ?>"><?php echo $pay_by; ?></option>
          <option value=""></option>
          <option value="CASH">CASH</option>
          <option value="BANK">BANK</option>
          <option value="PAYABLE">PAYABLE</option>
          <option value="CREDIT_CARD">CREDIT CARD</option>
        </select>
      </div> -->
        <div class="form-group col-sm-6 bill_date_wrap">
          <label>Bill Date * </label>
          <input class="form-control" name="billDate" type="date" placeholder='' required='' id="billDate" value="<?php echo $billDate; ?>" />
        </div>
        <div class="form-group col-sm-3 payment_date_wrap hide">
          <label>Paid On *</label>
          <input class="form-control" name="payment_date" type="date" placeholder='' id="payment_date" value="<?php echo ($payment_paid_date) ? $payment_paid_date : ''; ?>" />
        </div>
        <div class="form-group col-sm-6">
          <label>Expenses Type * </label>

          <select class="form-control" name="type_id" id="type_id" required>
            <?php
            $sql_opt = "SELECT id,title FROM expence_list ORDER BY title ASC";
            $result_opt = mysqli_query($con, $sql_opt);
            $options = "";
            while ($row_opt = mysqli_fetch_array($result_opt)) {
              $exp_id = $row_opt["id"];
              $name_opt = $row_opt["title"];
              $options .= "<OPTION value='$exp_id'>" . $name_opt;
            }
            $row_exp_type = $acttObj->read_specific('id,title', 'expence_list', 'id=' . $type_id);
            ?>
            <option value="<?php echo $row_exp_type['id']; ?>"><?php echo $row_exp_type['title']; ?></option>
            <option value="">Select Expense Type</option>
            <?php echo $options; ?>
            </option>
          </select>
        </div>
        <div class="form-group col-sm-6">
          <label>Company Name * </label>
          <!-- <input class="form-control" name="comp" type="text" placeholder='' required='' id="comp" value="<?php echo $comp; ?>"/> -->
          <select id="comp" name="comp" class="form-control searchable multi_class">
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

              $options .= '<OPTION value="' . $name_opt . '" class="vat_' . ($tax_reg == 1 ? "yes" : "no") . '" data-id="' . (trim($uk_citizen_vatNum) != "" ? $uk_citizen_vatNum : $country_vatNum) . '" ' . (trim(strtolower($name_opt)) == trim(strtolower($comp)) ? "selected" : "") . '>' . $name_opt;
            }
            ?>
            <option value="">Select Supplier</option>
            <?php echo $options; ?>
            </option>
          </select>
        </div>
        <div class="form-group col-sm-12">
          <textarea class="form-control" name="details" rows="3" placeholder='Write expense details here ...' id="details"> <?php echo $details; ?></textarea>
        </div>

        <div class="form-group col-sm-3 col-xs-6">
          <label>Net Amount *</label>
          <input class="form-control" name="netamount" type="text" placeholder='' required='required' id="netamount" value="<?php echo $netamount ?: 0; ?>" />
        </div>
        <div class="form-group col-sm-3 col-xs-6 div_vat hidden">
          <label>VAT * </label>
          <input class="form-control " name="vat" type="text" required='required' id="vat" value="<?php echo $vat ?: 0; ?>" />
        </div>
        <div class="form-group col-sm-3 col-xs-6">
          <label>Non VAT *</label>
          <input class="form-control" name="nonvat" type="text" required='required' id="nonvat" value="<?php echo $nonvat ?: 0; ?>" />
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
        <div class="form-group col-sm-6 col-xs-12 form-inline" id="div_receipt">
          <input class="form-control <?php echo ($exp_receipt != '' ? 'hidden' : ''); ?>" name="exp_receipt" type="file" id="exp_receipt" />
          <?php if ($exp_receipt != '') { ?>
            <!-- <div class="div_receipt_name"><?php echo $exp_receipt; ?> -->
            <a href="javascript:void(0);" onClick="popupwindow('exp_receipt_view.php?v_id=<?php echo $edit_id; ?>', 'title', 1000,700);" class='btn btn-primary div_receipt_name' id="view_receipt"> View Receipt</a>
            <button class="btn btn-secondary edit_attach"><i class="glyphicon glyphicon-edit"> </i> Edit</button>
          <?php } ?>
        </div>
        <div class="form-group col-sm-6 col-xs-12 text-right"><br>
          <button class="btn btn-primary" type="submit" name="submit" onclick="return formSubmit(); return false">Update</button>
        </div>
      </div>
    </form>
  </div>
</body>
<script src="js/jquery-1.11.3.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.13/css/bootstrap-multiselect.css" rel="stylesheet" type="text/css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.13/js/bootstrap-multiselect.js" type="text/javascript"></script>
<script>
  function popupwindow(url, title, w, h) {
    var left = (screen.width / 2) - (w / 2);
    var top = (screen.height / 2) - (h / 2);
    return window.open(url, title, 'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=yes, resizable=yes, copyhistory=no, width=' + w + ', height=' + h + ', top=' + top + ', left=' + left);
  }

  $(function() {
    $('.searchable').multiselect({
      includeSelectAllOption: true,
      numberDisplayed: 1,
      enableFiltering: true,
      enableCaseInsensitiveFiltering: true
    });
  });

  $(document).on('click', '.edit_attach', function(e) {
    e.preventDefault();

    // Toggle visibility
    $('.div_receipt_name').toggleClass('hidden');
    $('#exp_receipt').toggleClass('hidden');

    // Toggle button text
    const $btn = $(this);
    const currentText = $btn.text().trim().toLowerCase();

    if (currentText === 'edit') {
      $btn.removeClass('btn-secondary').addClass('btn-danger');
      $btn.html('<i class="glyphicon glyphicon-remove"></i> Cancel');
    } else {
      $btn.html('<i class="glyphicon glyphicon-edit"></i> Edit');
      $btn.removeClass('btn-danger').addClass('btn-secondary');
    }
  });

  <?php if ($row['status'] == 'full_paid') { ?>
    $(document).ready(function() {
      if ('<?php echo $pay_by; ?>' == 'PAYABLE' || '<?php echo $pay_by; ?>' == 'Payable' || '<?php echo $pay_by; ?>' == 'payable') {
        $('.payment_date_wrap').addClass('hide');
        $('#payment_date').attr('required', false);

        $('.bill_date_wrap').removeClass('col-sm-3').addClass('col-sm-6');
      } else {
        $('.payment_date_wrap').removeClass('hide');
        $('#payment_date').attr('required', true);

        $('.bill_date_wrap').removeClass('col-sm-6').addClass('col-sm-3');
      }
    });
  <?php } ?>


  $(document).ready(function() {
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


  // $(document).ready(function() {
  //   $(document).on('click', '#view_receipt', function(e) {
  //     e.preventDefault();
  //     window.open('exp_receipt_view.php?v_id=<?php echo $edit_id; ?>', "popupWindow", "width=600,height=600,scrollbars=yes");
  //   });
  // });
</script>

</html>