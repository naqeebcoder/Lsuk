<?php if (session_id() == '' || !isset($_SESSION)) {
  session_start();
}
include 'db.php';
include 'class.php';
$table = 'pre_payments';

$allowed_type_idz = "244";
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

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
  <title>Edit Prepayment</title>
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
      $table = 'pre_payments';
      
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

      $no_of_installment = trim($_POST['no_of_installment']);
      $frequency = trim($_POST['frequency']);
      $payment_date = trim($_POST['payment_date']);
      $details = mysqli_real_escape_string($con, trim($_POST['description']));
      $net_amount = trim($_POST['netamount']);
      $vat = trim($_POST['vat']) ?: 0;
      $nonvat = trim($_POST['nonvat']) ?: 0;
      //$total_amount = trim($_POST['total_amount']) ?: 0;

        $insert_data = array(
          'category_id' => $category_id,
          'receiver_id' => $receiver_id,
          'no_of_installment' => $no_of_installment,
          'frequency' => $frequency,
          'payment_date' => $payment_date,
          'description' => $details,
          'net_amount' => $net_amount,
          'vat' => $vat,
          'nonvat' => $nonvat,
          'edited_date' => date("Y-m-d H:i:s"),
          'edited_by' => $_SESSION['UserName'],
        );

      $update_res = $acttObj->update($table, $insert_data, ' id = ' . $edit_id);

      if (isset($_FILES["exp_receipt"]) && !empty($_FILES["exp_receipt"]["name"]) && $_FILES["exp_receipt"]["error"] === 0) {
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

        // getting old attachment to remove
        $old_attachment = $acttObj->read_specific('attachment', $table, ' id = '.$edit_id)['attachment'];
        if(file_exists($uploadDir . $old_attachment)){
          unlink($uploadDir . $old_attachment);
        }

        // update with new attachment
        $acttObj->editFun($table, $edit_id, 'attachment', $picName);

      }

      $acttObj->insert("daily_logs", array("action_id" => 55, "user_id" => $_SESSION['userId'], "details" => "Prepayment ID: " . $edit_id));

    ?>
      <script>
        alert('Record successfully updated.');

      // Refresh parent and close current window
      if (window.opener) {
          window.opener.location.reload();
          window.close();
      }

      </script>

    <?php } ?>

    <form action="" method="post" class="register" id="signup_form" name="signup_form" onsubmit="return formSubmit()" enctype="multipart/form-data">
      <h1 class="m-b-30">Update Prepayment Details (Track# <?php echo $row['invoice_no']; ?>)</h1>

      <div class="row">
       <div class="form-group col-sm-3">
          <label> Voucher No * </label>
          <div class="form-control"><?php echo $row['voucher']; ?></div>
        </div>
        <div class="form-group col-sm-3">
          <label>No of Installment</label>
          <input class="form-control" name="no_of_installment" type="number" placeholder="No of Installment" id="no_of_installment" value="<?php echo $row['no_of_installment']; ?>" />
        </div>
        <div class="form-group col-sm-3">
          <label>Frequency</label>
            <?php $frequencies = ['daily', 'weekly', 'biweekly', 'semimonthly', 'monthly', 'quarterly', 'annually']; ?>

          <select class="form-control" id="frequency" name="frequency">
            <option value="">- Frequency -</option>
              <?php foreach ($frequencies as $freq): ?>
                  <option value="<?php echo $freq; ?>" <?php echo ($row['frequency'] == $freq) ? 'selected' : ''; ?>>
                      <?php echo ucfirst($freq); ?>
                  </option>
              <?php endforeach; ?>
          </select>

        </div>
        <div class="form-group col-sm-3">
          <label>Payment Date * </label>
          <input class="form-control" name="payment_date" type="date" placeholder="Payment Date" required id="payment_date" value="<?php echo $row['payment_date']; ?>" />
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
          <label>Category * </label><br>
          <select id="category_id" name="category_id" class="form-control searchable multi_class" onchange="checkCategory(this)">
            <option value="">Select or Add New</option>
            <option value="new">Add New Category</option>
            <?php
              $sql_opt = "SELECT id, title FROM prepayment_categories ORDER BY title ASC";
              $result_opt = mysqli_query($con, $sql_opt);
              while ($row_opt = mysqli_fetch_array($result_opt)) {
                $selected = ($row['category_id'] == $row_opt['id']) ? 'selected' : '';
                echo "<option value='{$row_opt['id']}' {$selected}>
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
            <option value="new">Add New Receiver</option>
            <?php
              $sql_opt = "SELECT * FROM prepayment_receivers ORDER BY title ASC";
              $result_opt = mysqli_query($con, $sql_opt);
              while ($row_opt = mysqli_fetch_array($result_opt)) {
                $select = ($row['receiver_id'] == $row_opt['id']) ? 'selected' : '';
                echo "<option value='{$row_opt['id']}' {$select}>
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
            </div>
          </div>          
        </div>

        <div class="form-group col-sm-12">
          <textarea class="form-control" name="description" rows="3" placeholder='Description' id="description"><?php echo $row['description']; ?></textarea>
        </div>
        <div class="form-group col-sm-3 col-xs-6">
          <label>Net Amount *</label>
          <input class="form-control" name="netamount" oninput="CalcTotal();" type="text" placeholder='' required='' id="netamount" value="<?php echo $row['net_amount']; ?>" />
        </div>
        <div class="form-group col-sm-3 col-xs-6">
          <label>VAT * </label>
          <input class="form-control " name="vat" oninput="CalcTotal();" type="text" placeholder='' id="vat" value="<?php echo $row['vat']; ?>" />
        </div>
        <div class="form-group col-sm-3 col-xs-6">
          <label>Non VAT *</label>
          <input class="form-control" name="nonvat" oninput="CalcTotal();" type="text" placeholder='' id="nonvat" value="<?php echo $row['nonvat']; ?>" />
        </div>
        <div class="form-group col-sm-3 col-xs-6">
          <label>Total Amount</label>
          <div class="form-control"><?php echo $row['total_amount']; ?></div>
        </div>
        
        <div class="form-group col-sm-6 col-xs-12 form-inline" id="div_receipt">
          <input class="form-control <?php echo ($row['attachment'] != '' ? 'hidden' : ''); ?>" name="exp_receipt" type="file" id="exp_receipt" />
          <?php if ($row['attachment'] != '') { ?>
            <a href="javascript:void(0);" onClick="popupwindow('prepayment_receipt_view.php?v_id=<?php echo $edit_id; ?>', 'title', 1000,700);" class='btn btn-primary div_receipt_name' id="view_receipt"> View Receipt</a>
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
<script src="js/income_receive_amount.js"></script>
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


    // if ($('#is_payable').is(':checked')) {
    //     generateVoucherNo(1, $('#payment_type').val());
    //     $('#is_payable').val('1');
    //     $('#payment_type').attr('disabled', true);
    //   }else {
    //     $('#payment_type').prop('selectedIndex', 1).trigger('change');
    //     generateVoucherNo(0, 'bank');
    //   }

    //   $('#is_payable').change(function() {
    //     if ($(this).is(':checked')) {
    //       generateVoucherNo(1, $('#payment_type').val());
    //       $(this).val('1');
    //       $('#payment_type').attr('disabled', true);
    //       $('.payment_through_wrap').addClass('hide').css('display', 'none');
    //       $('#payment_through').attr("required", false);
    //       $('#payment_type').val('').prop("selectedIndex", 0);
    //     } else {
    //       generateVoucherNo(0, $('#payment_type').val());
    //       $(this).val('0');
    //       $('#payment_type').attr('disabled', false).prop("selectedIndex", 1);
    //       $('.payment_through_wrap').removeClass('hide').css('display', 'block');
    //       $('#payment_through').attr("required", true);
    //     }
    //   });

    //   $('#payment_type').change(function(e) {
    //     generateVoucherNo(0, $('#payment_type').val());
    //   });



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

</html>