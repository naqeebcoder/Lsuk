<?php include 'db.php';
include 'class.php';
include 'inc_functions.php';

$table = $_GET['table'];
$edit_id = $_GET['row_id'];
$allowed_type_idz = "230";
//Check if user has current action allowed
if ($_SESSION['is_root'] == 0) {
    $get_page_access = $acttObj->read_specific("id", "action_permissions", "user_id=" . $_SESSION['userId'] . " AND action_id IN (" . $allowed_type_idz . ")")['id'];
    if (empty($get_page_access)) {
        die("<center><h2 class='text-center text-danger'>You do not have access to <u>Add Payment</u> action for jobs!<br>Kindly contact admin for further process.</h2></center>");
    }
}
?>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
    <title>Receive Order Amount</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="css/bootstrap.css">
    <link rel="stylesheet" href="css/util.css">
    <style>
        .b {
            color: #fff;
        }

        a:link,
        a:visited {
            color: #337ab7;
        }
    </style>
</head>

<body>
    <?php
    $query = "SELECT * FROM $table where id=$edit_id";
    $result = mysqli_query($con, $query);
    $row = mysqli_fetch_array($result);
    $rAmount = $misc->numberFormat_fun($row['mult_amount']);
    $rDate = $row['paid_date'];
    $payment_type = $row['payment_type'];
    $payment_method_id = $row['payment_method_id'];

    // if(!empty($rDate) && $rDate!='0000-00-00' && $rDate!='1001-01-01'){
    //     $previous_date=date('Y-m-d',strtotime($rDate.'-31 days'));
    // }else{
    //     $previous_date=date('Y-m-d',strtotime(date('Y-m-d').'-31 days'));
    // }
    // $future_date=date('Y-m-d',strtotime(date('Y-m-d').'+1 days'));

    // if($table=='interpreter'){
    //     $alltotal_charges_comp=$row['total_charges_comp']* $row["cur_vat"] +
    //     $row['total_charges_comp'] + $row['C_otherexpns'];
    // }elseif($table=='telephone'){
    //     $alltotal_charges_comp=($row['total_charges_comp']+$row['calCharges'])* $row["cur_vat"] +
    //     $row['total_charges_comp']+$row['calCharges'];
    // }else{
    //     $alltotal_charges_comp=$row['total_charges_comp']* $row["cur_vat"] +
    //     $row['total_charges_comp'];
    // }
    // $final_sum=$misc->numberFormat_fun($alltotal_charges_comp);

    $final_sum = $row['mult_amount'];
    if (isset($_POST['submit'])) {
        if (!empty($_POST['rAmount']) && $_POST['rAmount'] > 0) {

            $rAmount = $_POST['rAmount'];
            $rDate = $_POST['rDate'];
            $payment_type = $_POST['payment_type'];
            $payment_method_id = $_POST['payment_through'];

            // $update_values = array(
            //     'status' => 'Received',
            //     'rAmount' => $rAmount,
            //     'paid_date' => $rDate,
            //     'paid_by' => $_SESSION['userId'],
            //     'paid_on' => date('Y-m-d H:i:s'),
            //     'payment_type' => $payment_type,
            //     'payment_method_id' => $payment_method_id
            // );

            $acttObj->db_query("UPDATE " . $table . " SET status = 'Received', rAmount = " . $rAmount . ", paid_date = '" . $rDate . "', paid_by = '" . $_SESSION['userId'] . "', paid_on = '" . date('Y-m-d H:i:s') . "', payment_type = '" . $payment_type . "', payment_method_id = '" . $payment_method_id . "' WHERE id = " . $edit_id);

            $array_types = array("interpreter" => "F2F", "telephone" => "TP", "translation" => "TR");

            $acttObj->insert("daily_logs", array("action_id" => 25, "user_id" => $_SESSION['userId'], "details" => "Mult.Invoice ID: " . $row['m_inv']));

            /* Insertion Query to Accounts: Receivable & account_journal_ledger Table
                - account_receivable : As Credit (balance - credit)
                - account_journal_ledger : As Debit (balance + credit)
            */

            $current_date = date("Y-m-d");
            $description = '[Collective Invoice] Company: ' . $row['comp_abrv'] . ", Invoice# " . $row['m_inv'];
            $credit_amount = $rAmount;

            // Checking if record already exists
            $parameters = " invoice_no = '" . $row['m_inv'] . "' AND dated = '" . $current_date . "' AND company = '" . $row['comp_abrv'] . "' AND credit = '" . $credit_amount . "'";
            $chk_exist = 0; //isReceivableRecordExists($parameters);

            if ($chk_exist < 1) {

                // getting balance amount
                $res = getCurrentBalances($con);

                if ($payment_type == 'cash') {
                    $voucher_label = 'CPV';
                    $is_bank = '0';
                } else {
                    $voucher_label = 'BPV';
                    $is_bank = '1';
                }

                // Getting New Voucher Counter
                $voucher_counter = getNextVoucherCount($voucher_label);

                // Updating the new Voucher Counter
                updateVoucherCounter($voucher_label, $voucher_counter);

                $voucher = $voucher_label . '-' . $voucher_counter;

                // Insertion in tbl account_receivable
                $insert_data = array(
                    'voucher' => $voucher,
                    'invoice_no' => $row['m_inv'],
                    'dated' => $current_date,
                    'company' => $row['comp_abrv'],
                    'description' => $description,
                    'credit' => $credit_amount,
                    'balance' => ($res['recv_balance'] - $credit_amount),
                    'posted_by' => $_SESSION['userId'],
                    'posted_on' => date('Y-m-d H:i:s'),
                    'tbl' => $table
                );

                $re_result = insertAccountReceivable($insert_data);
                //$voucher = $re_result['voucher'];
                $new_voucher_id = $re_result['new_voucher_id'];

                /********** Insertion in Account Journal Ledger *********/

                $insert_data_journal = array(
                    'is_receivable' => 1,
                    'receivable_payable_id' => $new_voucher_id,
                    'voucher' => $voucher,
                    'invoice_no' => $row['m_inv'],
                    'company' => $row['comp_abrv'],
                    'description' => $description,
                    'is_bank' => $is_bank,
                    'payment_type' => $payment_type,
                    'account_id' => $payment_method_id,
                    'dated' => $current_date,
                    'debit' => $credit_amount,
                    'balance' => ($res['journal_balance'] + $credit_amount),
                    'posted_by' => $_SESSION['userId'],
                    'posted_on' => date('Y-m-d H:i:s'),
                    'tbl' => $table
                );

                insertJournalLedger($insert_data_journal);
            } // end if record exists

    ?>
            <script>
                alert('Amount Successfully updated for this invoice. Thank you!');
                window.close();
                window.onunload = refreshParent;

                function refreshParent() {
                    window.opener.location.reload();
                }
            </script>
    <?php
            // }
        } else {
            $msg = '<div class="alert alert-danger col-md-6 col-md-offset-3 text-center h4"><b>Fill up valid amount value & greater then 0.</b></div>';
        }
    }


    ?>
    <div class="container">
        <?php
        if ($row['mult_amount'] == $row['rAmount']) {
            die('<div class="m-t-20 alert alert-danger">
                <h4 class="m-b-0">Amount already received for this invoice on ' . $misc->dated($row['paid_date']) . '.</h4>
                </div>');
        }
        ?>
        <h3 class="text-center">Update Received Amount from Customer</h3>
        <p class="text-center text-danger"><b>NOTE :</b> Total Amount for this Invoice: <?php echo '<b>' . $final_sum . '</b>'; ?></p>
        <form action="" method="post" class="col-md-12">
            <span id="display_msg">
                <?php if (isset($msg) && !empty($msg)) {
                    echo $msg;
                } ?>
            </span>
            <div class="form-group col-sm-6">
                <label>Amount Received *</label>
                <input oninput="value_amount()" name="rAmount" class="form-control" type="text" pattern="[0-9]+([\.|,][0-9]+)?"
                    step="0.01" id="rAmount" required='' value="<?php echo $row['mult_amount']; ?>" />
            </div>
            <div class="form-group col-sm-6">
                <label>Date *</label>
                <!--<input name="rDate" type="date" min="<?= $previous_date ?>" max="<?= $future_date ?>" class="form-control" required='' value="<?php echo $rDate; ?>" />-->
                <input name="rDate" type="date" class="form-control" required='' value="<?php echo !empty($rDate) && $rDate != '1001-01-01' ? $rDate : ''; ?>" />
            </div>
            <div class="form-group col-sm-6">
                <label>Payment Type</label>
                <select class="form-control" id="payment_type" name="payment_type" required>
                    <option value="">- Select -</option>
                    <option value="bacs" <?php echo ($payment_type == 'bacs') ? 'selected' : ''; ?>>BACS</option>
                    <option value="cheque" <?php echo ($payment_type == 'cheque') ? 'selected' : ''; ?>>Cheque</option>
                    <option value="card" <?php echo ($payment_type == 'card') ? 'selected' : ''; ?>>Credit/Debit Card</option>
                    <option value="cash" <?php echo ($payment_type == 'cash') ? 'selected' : ''; ?>>Cash</option>
                </select>
            </div>
            <div class="form-group col-sm-6 payment_through_wrap hide">
                <label class="pull-left">Payment Method</label>
                <?php if ($_SESSION['is_root'] == 1 || $_SESSION['userId'] == 27) { // UserID 27 == Ayub Sabir 
                ?>
                    <label class="pull-right">
                        <a href="javascript:void(0)" onclick="return addNewPaymentMode()" title="Add New Detail" data-toggle="tooltip" class="btn btn-info btn-xs text-white">
                            <i class="fa fa-plus"></i> New
                        </a>
                    </label>
                <?php } ?>
                <select class="form-control" id="payment_through" name="payment_through">
                </select>
            </div>

            <div class="col-sm-12 text-right">
                <button class="btn btn-primary" type="submit" id="btn_submit" name="submit">Submit &raquo;</button>
            </div>
        </form>
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

</body>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
<script src="js/income_receive_amount.js"></script>

<script>
    function value_amount() {
        var amount_val = document.getElementById('rAmount');
        var display_msg = document.getElementById('display_msg');
        var btn_submit = document.getElementById('btn_submit');
        if (!(/^[-+]?\d*\.?\d*$/.test(amount_val.value))) {
            btn_submit.disabled = true;
            display_msg.innerHTML = '<div class="alert alert-danger col-md-6 col-md-offset-3 text-center h4"><b>Entered value is not a number !</b></div>';
        } else {
            if (amount_val.value > <?php echo $final_sum; ?>) {
                btn_submit.disabled = true;
                display_msg.innerHTML = '<div class="alert alert-warning col-md-6 col-md-offset-3 text-center h4"><b>Entered value is greater  than invoice amount <?php echo $final_sum; ?></b></div>';
            } else if (amount_val.value < <?php echo $final_sum; ?>) {
                btn_submit.disabled = true;
                display_msg.innerHTML = '<div class="alert alert-danger col-md-6 col-md-offset-3 text-center h4"><b>Entered value is less than invoice amount <?php echo $final_sum; ?></b></div>';
            } else {
                btn_submit.disabled = false;
                display_msg.innerHTML = '';
            }
        }
    }
</script>

</html>