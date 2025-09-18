<?php
if (session_id() == '' || !isset($_SESSION)) {
  session_start();
}
include 'db.php';
include 'class.php';
include 'inc_functions.php';

$allowed_type_idz = "221";
//Check if user has current action allowed
if ($_SESSION['is_root'] == 0) {
  $get_page_access = $acttObj->read_specific("id", "action_permissions", "user_id=" . $_SESSION['userId'] . " AND action_id IN (" . $allowed_type_idz . ")")['id'];
  if (empty($get_page_access)) {
    die("<center><h2 class='text-center text-danger'>You do not have access to Perform this action!<br>Kindly contact admin for further process.</h2></center>");
  }
}
$id = (int) $_GET['id'];


// Generating New Invoice + Voucher
$lastid = $acttObj->max_id('income_invoices') + 1;
$invoice_no = substr(date('Y'), 2) . date('md') . $lastid;
$voucher_no = 'JV-' . $lastid;

// Get invoice + company
$invoice = mysqli_fetch_assoc(mysqli_query($con, "
  SELECT i.*, c.company_name, c.contact_person, c.email, c.address, c.payment_terms, c.id AS comp_id
  FROM income_invoices i
  JOIN income_company c ON c.id = i.company_id
  WHERE i.id = $id
"));

// Get items
$items = mysqli_query($con, "SELECT * FROM income_invoice_items WHERE invoice_id = $id");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  // Form base data
  //$voucher_no = mysqli_real_escape_string($con, $_POST['voucher']); // Invoice No
  $client_ref = mysqli_real_escape_string($con, $_POST['inv_ref']);
  $p_order = mysqli_real_escape_string($con, $_POST['p_order']);
  $description = mysqli_real_escape_string($con, $_POST['invoice_description']);
  $subtotal = (float) $_POST['subtotal'];
  $vat_total = (float) $_POST['vatTotal'];
  $non_vat_total = (float) $_POST['nonVatTotal'];
  $total_due = (float) $_POST['totalAmountDue'];
  $created_date = date('Y-m-d'); // current date
  $due_date = null;

  // Handle company selection
  if ($_POST['comp'] === "new") {
    // New company fields
    $new_comp = mysqli_real_escape_string($con, $_POST['new_comp']);
    $contact_person = mysqli_real_escape_string($con, $_POST['contact_person']);
    $email = mysqli_real_escape_string($con, $_POST['email']);
    $address = mysqli_real_escape_string($con, $_POST['address']);
    $paydays = (int) $_POST['paydays'];

    // Insert new company
    $insert_company = "INSERT INTO income_company (company_name, contact_person, email, address, payment_terms) 
    VALUES ('$new_comp', '$contact_person', '$email', '$address', '$paydays')";

    if (mysqli_query($con, $insert_company)) {
      $company_id = mysqli_insert_id($con);
      $due_date = date('Y-m-d', strtotime("+$paydays days"));
    } else {
      die("Company creation failed: " . mysqli_error($con));
    }
  } else {
    $company_id = (int) $_POST['comp'];
    // Get payment terms of selected company
    $comp_query = mysqli_query($con, "SELECT payment_terms FROM income_company WHERE id = $company_id");
    $comp = mysqli_fetch_assoc($comp_query);
    $paydays = (int) $comp['payment_terms'];
    $due_date = $_POST['due_date'];
  }

  // Insert invoice
  $data = [
    'voucher_no' => $invoice_no, // Invoice No
    'voucher' => $voucher_no,
    'client_reference' => $client_ref,
    'p_order' => $p_order,
    'company_id' => $company_id,
    'description' => $description,
    'subtotal' => $subtotal,
    'non_vat' => $non_vat_total,
    'total_vat' => $vat_total,
    'total_amount' => $total_due,
    'due_date' => $due_date,
    'created_by' => $_SESSION['userId']
  ];

  $insertId = $acttObj->insert('income_invoices', $data, true);
  if ($insertId) {
    $invoice_id = $insertId;

    // Insert each item
    foreach ($_POST['items'] as $item) {
      $desc = mysqli_real_escape_string($con, $item['description']);
      $qty = (float) $item['qty'];
      $unit = mysqli_real_escape_string($con, $item['unit']);
      $unit_price = (float) $item['unit_price'];
      $vat_percent = (float) $item['vat_percent'];
      $vat = (float) $item['vat'];
      $non_vat = (float) $item['non_vat'];
      $total = (float) $item['total'];
      $data = [
        'invoice_id' => $invoice_id,
        'description' => $desc,
        'qty' => $qty,
        'unit' => $unit,
        'unit_price' => $unit_price,
        'vat_percent' => $vat_percent,
        'vat_amount' => $vat,
        'non_vat' => $non_vat,
        'total' => $total
      ];

      $acttObj->insert('income_invoice_items', $data);
    }

    $acttObj->insert('daily_logs', ['action_id' => 49, 'user_id' => $_SESSION['userId'], 'details' => "Invoice ID: $invoice_no"]);

    /* Insertion Query to Accounts: Income & Receivable Table
      - account_income : As Credit
      - account_receivable : As Debit
    */

    $current_date = date("Y-m-d");
    $credit_amount = $total_due;
    $company_name_abrv = $acttObj->read_specific("company_name", "income_company", " id = " . $company_id)['company_name'];
    $description = '[Recreated][Manual Invoice] Company: ' . $company_name_abrv . ', Invoice No: ' . $invoice_no;

    // Check if record already exists
    $parameters = " invoice_no = '" . $invoice_no . "' AND dated = '" . $current_date . "' AND company = '" . $company_name_abrv . "' AND credit = '" . $credit_amount . "'";
    $chk_exist = 0; //isIncomeRecordExists($parameters);

    if ($chk_exist < 1 && $credit_amount > 0) {

      // getting balance amount
      $res = getCurrentBalances($con);

      // Insertion in tbl account_income
      $insert_data = array(
        'voucher' => $voucher_no,
        'invoice_no' => $invoice_no,
        'dated' => $current_date,
        'company' => $company_name_abrv,
        'description' => $description,
        'credit' => $credit_amount,
        'balance' => ($res['balance'] + $credit_amount),
        "posted_by" => $_SESSION['userId']
      );

      $jv_voucher = insertAccountIncome($insert_data);

      // Insertion in tbl account_receivable
      $insert_data_rec = array(
        'voucher' => $voucher_no,
        'invoice_no' => $invoice_no,
        'dated' => $current_date,
        'company' => $company_name_abrv,
        'description' => $description,
        'debit' => $credit_amount,
        'balance' => ($res['recv_balance'] + $credit_amount),
        "posted_by" => $_SESSION['userId']
      );

      $re_result = insertAccountReceivable($insert_data_rec);
      //$voucher = $re_result['voucher'];
      $new_voucher_id = $re_result['new_voucher_id'];
    } // end if record exists


    //....................................Credit Note.........................................
    // $invoiceNo = $voucher_no;
    // $abrv = $company_name_abrv;
    // if ($credit_amount > 0) {

    //   $flag_inv = $acttObj->uniqueFun('comp_credit', 'invoiceNo', $invoiceNo);

    //   if ($flag_inv == 0) {
    //     $credit_id = $acttObj->get_id('comp_credit');
    //   } else {
    //     $credit_id = $acttObj->unique_data('comp_credit', 'id', 'invoiceNo', $invoiceNo);
    //   }

    //   $acttObj->editFun('comp_credit', $credit_id, 'orgName', $abrv);
    //   $acttObj->editFun('comp_credit', $credit_id, 'invoiceNo', $invoiceNo);
    //   $acttObj->editFun('comp_credit', $credit_id, 'mode', 'manual_invoice');
    //   $acttObj->editFun('comp_credit', $credit_id, 'debit', $credit_amount);
    //   $acttObj->editFun('comp_credit', $credit_id, 'debit_date', date("Y-m-d"));
    // }
    // //.......................................//\\//\\//\\..Credit Note.//\\//\\//\\.................................

    // //....................................Business Credit Note.........................................
    // if ($credit_amount > 0) {
    //   $flag_inv = $acttObj->uniqueFun('bz_credit', 'invoiceNo', $invoiceNo);
    //   if ($flag_inv == 0) {
    //     $bz_credit_id = $acttObj->get_id('bz_credit');
    //   } else {
    //     $bz_credit_id = $acttObj->unique_data('bz_credit', 'id', 'invoiceNo', $invoiceNo);
    //   }

    //   $acttObj->editFun('bz_credit', $bz_credit_id, 'orgName', $abrv);
    //   $acttObj->editFun('bz_credit', $bz_credit_id, 'invoiceNo', $invoiceNo);
    //   $acttObj->editFun('bz_credit', $bz_credit_id, 'mode', 'manual_invoice');
    //   $acttObj->editFun('bz_credit', $bz_credit_id, 'bz_debit', $credit_amount);
    //   $acttObj->editFun('bz_credit', $bz_credit_id, 'bz_debit_date', date("Y-m-d"));
    // }
    //.......................................//\\//\\//\\..Business Credit Note.//\\//\\//\\.................................


    echo "<script>
            alert('Invoice created successfully!');
            window.close();
            window.onunload = refreshParent;

            function refreshParent() {
              window.opener.location.reload();
            }
           // window.location.href = 'invoice_view.php?id=$invoice_id';
        </script>";
  } else {
    die("Invoice creation failed: " . mysqli_error($con));
  }
}

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
  <title>Edit Invoice</title>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" />
  <link rel="stylesheet" type="text/css" href="css/util.css" />
  <style>
    #company_id_select button {
      width: 100%;
    }

    #company_id_select .btn-group {
      width: 100%;
    }
  </style>
</head>

<?php
//$clean_voucher_no = substr($invoice['voucher_no'], 0, 7);

// $parts = explode('-', $invoice['voucher_no']);
// $counter = ($acttObj->read_specific("count(id)+1 as count", "income_invoices", "SUBSTRING(voucher_no,1,7) = '" . $parts[0] . "'")['count']);

// $new_invoice_no = $parts[0] . '-' . $counter;

?>

<body class="container py-5">
  <h2>Re-create Invoice # <?= $invoice_no; ?></h2>
  <form action="" method="post" class="register" id="signup_form" name="signup_form">
    <input type="hidden" name="invoice_id" value="<?= $id ?>">

    <div class="row">
      <div class="form-group col-sm-3">
        <label> Invoice No * </label>
        <input name="invoice_no" type="text" class="form-control" required id="invoice_no" value="<?php echo $invoice_no; ?>" readonly />
      </div>
      <div class="form-group col-sm-3">
        <label>Voucher</label>
        <input type="text" class="form-control" name="voucher" id="voucher" placeholder="Voucher" value="<?php echo $voucher_no; ?>" readonly />
      </div>
      <div class="form-group col-sm-6">
        <label>Client Reference</label>
        <input class="form-control" name="inv_ref" type="text" id="inv_ref_num" value="<?= $invoice['client_reference'] ?>" />
      </div>
    </div>

    <div class="row">
      <div class="form-group col-sm-6" id="company_id_select">
        <label>Company Name *</label>
        <select id="comp" name="comp" class="form-control searchable multi_class" onchange="checkCompany(this)">
          <option value="">Select Company or Add New</option>
          <option value="new">Add New Company</option>
          <?php
          $sql_opt = "SELECT id, company_name, contact_person, email, address,payment_terms FROM income_company ORDER BY company_name ASC";
          $result_opt = mysqli_query($con, $sql_opt);
          while ($row_opt = mysqli_fetch_array($result_opt)) {
            // Check if this company is the selected one based on the invoice's company ID
            $selected = ($invoice['comp_id'] == $row_opt['id']) ? 'selected' : '';
            echo "<option value='{$row_opt['id']}' 
                        data-contact='" . htmlspecialchars($row_opt['contact_person'], ENT_QUOTES) . "' 
                        data-email='" . htmlspecialchars($row_opt['email'], ENT_QUOTES) . "' 
                        data-address='" . htmlspecialchars($row_opt['address'], ENT_QUOTES) . "' 
                        data-payment-terms='" . htmlspecialchars($row_opt['payment_terms'], ENT_QUOTES) . "'
                        $selected>
                        {$row_opt['company_name']}
                      </option>";
          }
          ?>
        </select>
      </div>

    </div>

    <!-- New Company Fields -->
    <div id="newCompanyFields" style="display: none;">
      <div class="row">
        <div class="form-group col-sm-6">
          <label>Company Name *</label>
          <input type="text" class="form-control" name="new_comp" id="new_comp">
        </div>
        <div class="form-group col-sm-6">
          <label>Contact Person *</label>
          <input type="text" class="form-control" name="contact_person" id="contact_person">
        </div>
      </div>
      <div class="row">
        <div class="form-group col-sm-6">
          <label>Email *</label>
          <input type="email" class="form-control" name="email" id="email">
        </div>
        <div class="form-group col-sm-6">
          <label>Payment Terms (Days) *</label>
          <input class="form-control" name="paydays" type="number" id="paydays">
        </div>
      </div>
      <div class="row">
        <div class="form-group col-sm-12">
          <label>Address *</label>
          <textarea class="form-control" name="address" id="address" rows="2"></textarea>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="form-group col-sm-6">
        <label>Purchase Order</label>
        <input type="text" class="form-control" name="p_order" id="p_order" value="<?= $invoice['p_order'] ?? '' ?>">
      </div>
      <div class="form-group col-sm-6">
        <label>Due Date</label>
        <input type="date" class="form-control" name="due_date" id="due_date" value="<?= $invoice['due_date'] ?? '' ?>">
      </div>
    </div>

    <div class="row">
      <div class="form-group col-sm-12">
        <label>Description</label>
        <textarea class="form-control" name="invoice_description" rows="3" id="invoice_description"><?= $invoice['description'] ?></textarea>
      </div>
    </div>

    <div class="row">
      <div class="col-sm-12">
        <h3>Invoice Items</h3>
        <table class="table table-bordered" id="invoiceTable">
          <thead>
            <tr>
              <th>Description</th>
              <th>Qty</th>
              <th>Unit</th>
              <th>Unit Price</th>
              <th>VAT %</th>
              <th>VAT</th>
              <th>Non-VAT</th>
              <th>Total</th>
              <!--th>Action</th-->
            </tr>
          </thead>
          <tbody>
            <?php while ($item = mysqli_fetch_assoc($items)): ?>
              <tr>
                <td><input type="text" class="form-control description" name="description[]" value="<?= $item['description'] ?>" required></td>
                <td><input type="number" class="form-control qty" name="qty[]" min="1" value="<?= number_format($item['qty']) ?>" oninput="updateRow(this)" required></td>
                <td><input type="text" class="form-control unit" name="unit[]" value="<?= $item['unit'] ?>" required></td>
                <td><input type="number" class="form-control unitPrice" name="unitPrice[]" min="0" step="0.01" oninput="updateRow(this)" value="<?= number_format($item['unit_price']) ?>" required></td>
                <td><input type="number" class="form-control vatPercent" name="vatPercent[]" min="0" step="0.01" value="<?= $item['vat_percent'] ?>" oninput="updateRow(this)"></td>
                <td><input type="text" class="form-control vatAmount" name="vatAmount[]" value="<?= $item['vat_amount'] ?>" readonly></td>
                <td><input type="number" class="form-control nonVat" name="nonVat[]" min="0" step="0.01" value="<?= $item['non_vat'] ?>" oninput="updateTotal()"></td>
                <td><input type="text" class="form-control total" name="total[]" value="<?= $item['total'] ?>" readonly></td>
                <td><button type="button" class="btn btn-danger" onclick="removeRow(this)">Delete</button></td>
              </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
        <button type="button" class="btn btn-success m-b-15" onclick="addRow()">Add Row</button>
      </div>
    </div>

    <div class="row">
      <div class="col-sm-3">
        <label>Subtotal</label>
        <input class="form-control" id="subtotal" name="subtotal" type="text" readonly value="<?= $invoice['subtotal'] - $invoice['total_vat'] - $invoice['non_vat'] ?>">
      </div>
      <div class="col-sm-3">
        <label>VAT Total</label>
        <input class="form-control" id="vatTotal" name="vatTotal" type="text" readonly value="<?= $invoice['total_vat'] ?>">
      </div>
      <div class="col-sm-3">
        <label>Non-VAT Total</label>
        <input class="form-control" id="nonVatTotal" name="nonVatTotal" type="text" readonly value="<?= $invoice['non_vat'] ?>">
      </div>
      <div class="col-sm-3">
        <label>Total Amount Due</label>
        <input class="form-control" id="totalAmountDue" name="totalAmountDue" type="text" readonly value="<?= $invoice['total_amount'] ?>">
      </div>
    </div>

    <div class="row">
      <div class="col-sm-12 text-right" style="margin-top:20px;">
        <button class="btn btn-primary" type="submit" name="submit" id="btn_submit" onclick="return formSubmit(); return false">Save Invoice</button>
      </div>
    </div>
  </form>
  <br>

  <script src="js/jquery-1.11.3.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.13/css/bootstrap-multiselect.css" rel="stylesheet" type="text/css" />
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.13/js/bootstrap-multiselect.js" type="text/javascript"></script>
  <script>
    $(function() {
      $('.searchable').multiselect({
        includeSelectAllOption: true,
        numberDisplayed: 1,
        enableFiltering: true,
        enableCaseInsensitiveFiltering: true
      });
    });
  </script>

  <script>
    function addRow() {
      let table = document.getElementById("invoiceTable").getElementsByTagName('tbody')[0];
      let row = table.insertRow();

      row.innerHTML = `
      <td><input type="text" class="form-control description" name="description[]" required></td>
      <td><input type="number" class="form-control qty" name="qty[]" min="1" value="1" oninput="updateRow(this)" required></td>
      <td><input type="text" class="form-control unit" name="unit[]" required></td>
      <td><input type="number" class="form-control unitPrice" name="unitPrice[]" min="0" step="0.01" oninput="updateRow(this)" required></td>
      <td><input type="number" class="form-control vatPercent" name="vatPercent[]" min="0" step="0.01" value="0" oninput="updateRow(this)"></td>
      <td><input type="text" class="form-control vatAmount" name="vatAmount[]" readonly></td>
      <td><input type="number" class="form-control nonVat" name="nonVat[]" min="0" step="0.01" value="0" oninput="updateTotal()"></td>
      <td><input type="text" class="form-control total" name="total[]" readonly></td>
      <td><button type="button" class="btn btn-danger" onclick="removeRow(this)">Delete</button></td>
    `;
      updateRow(row.querySelector('.qty'));
    }

    function removeRow(button) {
      button.closest('tr').remove();
      updateTotal();
    }

    function updateRow(input) {
      let row = input.closest('tr');
      let qty = parseFloat(row.querySelector('.qty').value) || 0;
      let unitPrice = parseFloat(row.querySelector('.unitPrice').value) || 0;
      let vatPercent = parseFloat(row.querySelector('.vatPercent').value) || 0;

      let total = qty * unitPrice;
      let vat = (total * vatPercent) / 100;

      row.querySelector('.vatAmount').value = vat.toFixed(2);
      row.querySelector('.total').value = total.toFixed(2);

      updateTotal();
    }

    function updateTotal() {
      let subtotal = 0,
        vatTotal = 0,
        nonVatTotal = 0,
        totalAmountDue = 0;

      document.querySelectorAll('.total').forEach(input => {
        subtotal += parseFloat(input.value) || 0;
      });

      document.querySelectorAll('.vatAmount').forEach(input => {
        vatTotal += parseFloat(input.value) || 0;
      });

      document.querySelectorAll('.nonVat').forEach(input => {
        nonVatTotal += parseFloat(input.value) || 0;
      });

      totalAmountDue = subtotal + vatTotal + nonVatTotal;

      document.getElementById('subtotal').value = (subtotal - vatTotal - nonVatTotal).toFixed(2);
      document.getElementById('vatTotal').value = vatTotal.toFixed(2);
      document.getElementById('nonVatTotal').value = nonVatTotal.toFixed(2);
      document.getElementById('totalAmountDue').value = totalAmountDue.toFixed(2);
    }
  </script>
  <script>
    function formSubmit() {

      const comp = document.getElementById('comp').value;
      if (!comp) {
        alert('Please select or add a company.');
        return false;
      }
      // Remove previus hidden items (if any)
      document.querySelectorAll("input[name^='items']").forEach(el => el.remove());

      // Loop through rows
      const rows = document.querySelectorAll("#invoiceTable tbody tr");
      if (rows.length === 0) {
        alert('Please add at least one invoice item.');
        return false;
      }
      rows.forEach((row, index) => {
        const fields = {
          description: row.querySelector(".description").value,
          qty: row.querySelector(".qty").value,
          unit: row.querySelector(".unit").value,
          unit_price: row.querySelector(".unitPrice").value,
          vat_percent: row.querySelector(".vatPercent").value,
          vat: row.querySelector(".vatAmount").value,
          non_vat: row.querySelector(".nonVat").value,
          total: row.querySelector(".total").value
        };
        for (const [key, val] of Object.entries(fields)) {
          const input = document.createElement('input');
          input.type = 'hidden';
          input.name = `items[${index}][${key}]`;
          input.value = val;
          document.getElementById('signup_form').appendChild(input);
        }
      });

      //$('#btn_submit').attr('readonly', true).attr('disabled', true).text('Please wait...');

      return true;
    }
  </script>
  <script>
    function checkCompany(select) {
      let selectedOption = select.options[select.selectedIndex];
      let newCompanyFields = document.getElementById("newCompanyFields");

      let contactPerson = document.getElementById("contact_person");
      let email = document.getElementById("email");
      let address = document.getElementById("address");
      let paydays = document.getElementById("paydays");
      let newComp = document.getElementById("new_comp");
      let dueDateInput = document.getElementById("due_date");

      if (selectedOption.value === "new") {
        // Show manual entry fields
        newCompanyFields.style.display = "block";
        // Make fields required
        contactPerson.setAttribute("required", "required");
        email.setAttribute("required", "required");
        address.setAttribute("required", "required");
        paydays.setAttribute("required", "required");
        newComp.setAttribute("required", "required");

      } else if (selectedOption.value !== "") {
        // Hide manual entry fields
        newCompanyFields.style.display = "none";
        contactPerson.removeAttribute("required");
        email.removeAttribute("required");
        address.removeAttribute("required");
        paydays.removeAttribute("required");
        newComp.removeAttribute("required");
      }
      let days = parseInt(selectedOption.getAttribute("data-payment-terms") || "0");
      if (!isNaN(days)) {
        let currentDate = new Date();
        currentDate.setDate(currentDate.getDate() + days);
        dueDateInput.value = currentDate.toISOString().split('T')[0];
      } else {
        dueDateInput.value = ""; // fallback if no valid days
      }
    }

    document.getElementById("paydays").addEventListener("input", function() {
      let days = parseInt(this.value);
      let dueDateInput = document.getElementById("due_date");

      if (!isNaN(days)) {
        let currentDate = new Date();
        currentDate.setDate(currentDate.getDate() + days);
        dueDateInput.value = currentDate.toISOString().split("T")[0];
      } else {
        dueDateInput.value = "";
      }
    });
  </script>
</body>

</html>