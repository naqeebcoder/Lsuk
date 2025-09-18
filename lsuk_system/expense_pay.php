<?php

include 'db.php';
include 'class.php';
include 'inc_functions.php';

$allowed_type_idz = "238";
//Check if user has current action allowed
if ($_SESSION['is_root'] == 0) {
    $get_page_access = $acttObj->read_specific("id", "action_permissions", "user_id=" . $_SESSION['userId'] . " AND action_id IN (" . $allowed_type_idz . ")")['id'];
    if (empty($get_page_access)) {
        die("<center><h2 class='text-center text-danger'>You do not have access to <u>Add Payment</u> action for jobs!<br>Kindly contact admin for further process.</h2></center>");
    }
}


$table = 'expence';
$edit_id = $_GET['pay_id'];
$payment_rec_type = $action = $_GET['action'];

if (empty($edit_id) || !is_numeric($edit_id)) {
    die("<center><h2 class='text-center text-danger'>Invalid Request!</h2></center>");
}
?>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
    <title>Pay Expense Amount</title>
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
    $query = "SELECT * FROM $table WHERE id = $edit_id";
    $result = mysqli_query($con, $query);
    $row = mysqli_fetch_array($result);

    $final_sum = $row['amoun'];

    if ($payment_rec_type == 'partial') {
        $qu = "SELECT SUM(amount) as total_received_partial_payments
        FROM expence_partial_payments 
        WHERE is_partial = 1 AND expence_id = $edit_id AND deleted_flag = 0";
        $res = $acttObj->db_query($qu);
        $partial_payment = $acttObj->full_fetch_assoc($res);

        $partial_received_amount = ($partial_payment['total_received_partial_payments']) ? $partial_payment['total_received_partial_payments'] : 0;
        $remAmount = ($final_sum - $partial_received_amount);
    }

    if (isset($_POST['submit'])) {

        $rAmount = mysqli_real_escape_string($con, $_POST['rAmount']);
        $rDate = mysqli_real_escape_string($con, $_POST['rDate']);

        $payment_type = mysqli_real_escape_string($con, $_POST['payment_type']);
        $payment_method_id = mysqli_real_escape_string($con, $_POST['payment_through']);

        if ($payment_type == 'cash') {
            $voucher_label = 'CPV';
            $is_bank = '0';
        } else {
            $voucher_label = 'BPV';
            $is_bank = '1';
        }

        if ($payment_rec_type == 'full') {

            if (!empty($_POST['rAmount']) && $_POST['rAmount'] > 0 && is_numeric($_POST['rAmount'])) {

                if (empty($payment_type)) {
                    $msg = '<div class="alert alert-danger col-md-6 col-md-offset-3 text-center h4"><b>You must fill atleast one payment method !</b></div>';
                } else {
                    if (bccomp($rAmount, $final_sum, 2) == 0) {

                        $expense_type = $acttObj->read_specific('title', 'expence_list', ' id = ' . $row['type_id'])['title'];
                        $description = '[Paid][Expense] ' . $expense_type;
                        if (!empty($row['details'])) {
                            $description .= '<br>' . $row['details'];
                        }

                        $update_values = array(
                            'status' => 'full_paid',
                            'amountPaid' => $rAmount,
                            'pay_by' => $payment_type,
                            'payment_type' => $payment_type,
                            'payment_method_id' => $payment_method_id,
                            'is_paid' => 1,
                            'paid_by' => $_SESSION['UserName'],
                            'paid_on' => date('Y-m-d H:i:s')
                        );

                        $acttObj->update($table, $update_values, array("id" => "'$edit_id'"));

                        $expence_partial_payments = array(
                            'is_partial' => 0,
                            'expence_id' => $edit_id,
                            'amount' => $rAmount,
                            'payment_date' => $rDate, // paid_on
                            'payment_type' => $payment_type,
                            'payment_method_id' => $payment_method_id,
                            'description' => $description,
                            'posted_by' => $_SESSION['UserName'],
                            'posted_on' => date("Y-m-d H:i:s"),
                        );
                        $acttObj->insert('expence_partial_payments', $expence_partial_payments, false);

                        $acttObj->insert("daily_logs", array("action_id" => 25, "user_id" => $_SESSION['userId'], "details" => "Expense ID: " . $edit_id));

                        // ************** Means Expense is Paid *********************

                        // Single Entry in tbl account_payables (Debit, Balance - Credit_amount) -- Reversal
                        // Single Entry in tbl account_journal_ledger (Debit, Balance - Credit_amount)

                        $credit_amount = $final_sum;

                        // getting balance amount
                        $balance_res = getCurrentBalances($con);

                        // Getting New Voucher Counter
                        $voucher_counter = getNextVoucherCount($voucher_label);

                        // Updating the new Voucher Counter
                        updateVoucherCounter($voucher_label, $voucher_counter);

                        $voucher = $voucher_label . '-' . $voucher_counter;

                        // Insertion in tbl account_receivable
                        $insert_data_payable = array(
                            'voucher' => $voucher,
                            'invoice_no' => $row['invoice_no'],
                            'dated' => date('Y-m-d'),
                            'company' => $row['comp'],
                            'description' => $description,
                            'debit' => $credit_amount,
                            'balance' => ($balance_res['payable_balance'] - $credit_amount),
                            'posted_by' => $_SESSION['userId'],
                            'tbl' => $table
                        );

                        $re_result = insertAccountPayables($insert_data_payable);
                        $new_payable_id = $re_result['new_voucher_id'];

                        // Insertion in tbl account_journal_ledger
                        $insert_data_journal = array(
                            'is_receivable' => 0,
                            'receivable_payable_id' => $new_payable_id,
                            'voucher' => $voucher,
                            'invoice_no' => $row['invoice_no'],
                            'company' => $row['comp'],
                            'description' => $description,
                            'is_bank' => $is_bank,
                            'payment_type' => $payment_type,
                            'account_id' => $payment_method_id,
                            'dated' => date('Y-m-d'),
                            'credit' => $credit_amount,
                            'balance' => ($balance_res['journal_balance'] - $credit_amount),
                            'posted_by' => $_SESSION['userId'],
                            'posted_on' => date('Y-m-d H:i:s'),
                            'tbl' => $table
                        );

                        insertJournalLedger($insert_data_journal, $payment_type);

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
                    } else {
                    ?>
                        <script>
                            alert('Failed: Paid Amount did not matched the invoice amount');
                        </script>
                    <?php
                    }
                }
            } else {
                $msg = '<div class="alert alert-danger col-md-6 col-md-offset-3 text-center h4"><b>Fill up valid amount value & greater then 0.</b></div>';
            }
        } else { // if payment receive type = partial

            $title = mysqli_real_escape_string($con, $_POST['title']);

            if (!empty($_POST['rAmount']) && $_POST['rAmount'] > 0 && is_numeric($_POST['rAmount'])) {

                if (empty($payment_type)) {
                    $msg = '<div class="alert alert-danger col-md-6 col-md-offset-3 text-center h4"><b>You must fill atleast one payment method !</b></div>';
                } else {

                    if (bccomp($rAmount, $remAmount, 2) <= 0) {

                        $expense_type = $acttObj->read_specific('title', 'expence_list', ' id = ' . $row['type_id'])['title'];
                        $description = '[Partial Payment][Expense] ' . $expense_type;
                        if (!empty($row['details'])) {
                            $description .= '<br>' . $row['details'];
                        }

                        $expence_partial_payments = array(
                            'is_partial' => 1,
                            'expence_id' => $edit_id,
                            'title' => mysqli_real_escape_string($con, $title),
                            'amount' => $rAmount,
                            'payment_date' => $rDate, // paid_on
                            'payment_type' => $payment_type,
                            'payment_method_id' => $payment_method_id,
                            'description' => $description,
                            'posted_by' => $_SESSION['UserName'],
                            'posted_on' => date("Y-m-d H:i:s"),
                        );
                        $acttObj->insert('expence_partial_payments', $expence_partial_payments, false);

                        // getting all records from partial table
                        $sum_of_total_partial_received_amount = $acttObj->read_specific("SUM(amount) as total_partial_received_amount", "expence_partial_payments", "is_partial = 1 AND expence_id = '" . $edit_id . "' AND deleted_flag = 0")['total_partial_received_amount'];

                        $update_values = array(
                            'pay_by' => $payment_type,
                            'payment_type' => $payment_type,
                            'payment_method_id' => $payment_method_id,
                            'amountPaid' => ($row['amountPaid'] + $rAmount),
                        );

                        if ($sum_of_total_partial_received_amount >= $row['amoun']) {
                            $update_values['status'] = 'full_partial';
                            $update_values['is_paid'] = 1;
                            $update_values['paid_by'] = $_SESSION['UserName'];
                            $update_values['paid_on'] = date('Y-m-d H:i:s');
                        } else {
                            $update_values['status'] = 'partial';
                        }

                        $acttObj->update($table, $update_values, array("id" => "'$edit_id'"));

                        $acttObj->insert("daily_logs", array("action_id" => 25, "user_id" => $_SESSION['userId'], "details" => "Expense ID: " . $edit_id));

                        // ************** Means Expense is Paid *********************

                        // Single Entry in tbl account_payables (Debit, Balance - Credit_amount) -- Reversal
                        // Single Entry in tbl account_journal_ledger (Debit, Balance - Credit_amount)

                        $credit_amount = $rAmount;

                        // Getting New Voucher Counter
                        $voucher_counter = getNextVoucherCount($voucher_label);

                        // Updating the new Voucher Counter
                        updateVoucherCounter($voucher_label, $voucher_counter);

                        $voucher = $voucher_label . '-' . $voucher_counter;

                        // getting balance amount
                        $balance_res = getCurrentBalances($con);

                        // Insertion in tbl account_receivable
                        $insert_data_payable = array(
                            'voucher' => $voucher,
                            'invoice_no' => $row['invoice_no'],
                            'dated' => date('Y-m-d'),
                            'company' => $row['comp'],
                            'description' => $description,
                            'debit' => $credit_amount,
                            'balance' => ($balance_res['payable_balance'] - $credit_amount),
                            'posted_by' => $_SESSION['userId'],
                            'tbl' => $table
                        );

                        $re_result = insertAccountPayables($insert_data_payable);
                        $new_payable_id = $re_result['new_voucher_id'];

                        // Insertion in tbl account_journal_ledger
                        $insert_data_journal = array(
                            'is_receivable' => 0,
                            'receivable_payable_id' => $new_payable_id,
                            'voucher' => $voucher,
                            'invoice_no' => $row['invoice_no'],
                            'company' => $row['comp'],
                            'description' => $description,
                            'is_bank' => $is_bank,
                            'payment_type' => $payment_type,
                            'account_id' => $payment_method_id,
                            'dated' => date('Y-m-d'),
                            'credit' => $credit_amount,
                            'balance' => ($balance_res['journal_balance'] - $credit_amount),
                            'posted_by' => $_SESSION['userId'],
                            'posted_on' => date('Y-m-d H:i:s'),
                            'tbl' => $table
                        );

                        insertJournalLedger($insert_data_journal, $payment_type);
                    }
                    ?>
                    <script>
                        alert('Amount Successfully updated for this invoice. Thank you!');
                        window.close();
                        window.onunload = refreshParent;

                        function refreshParent() {
                            window.opener.location.reload();
                        }
                    </script>

    <?php }
            }
        }
    }
    ?>

    <?php
    if (isset($_GET['del']) && isset($_GET['id'])) {

        $query = "SELECT e.*, e.id as main_id, pe.amount as partial_amount, DATE(pe.posted_on) as partial_dated, pe.payment_type as partial_payment_type, pe.payment_method_id as partial_payment_method_id
			FROM expence e
			LEFT JOIN expence_partial_payments pe ON pe.expence_id = e.id
			WHERE e.id = " . $edit_id;

        $result = mysqli_query($con, $query);
        $row = mysqli_fetch_array($result);

        if (count($row) > 0) {

            $expense_type = $acttObj->read_specific('title', 'expence_list', ' id = ' . $row['type_id'])['title'];
            $description = '[Deleted][Expense] ' . $expense_type;
            if (!empty($row['details'])) {
                $description .= '<br>' . $row['details'];
            }

            $credit_amount = $row['partial_amount'];

            if ($credit_amount > 0) {

                // getting balance amount
                $res = getCurrentBalances($con);

                if ($row['partial_payment_type'] == 'cash') {
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
                $insert_data_payable = array(
                    'voucher' => $voucher,
                    'invoice_no' => $row['invoice_no'],
                    'dated' => date('Y-m-d'),
                    'company' => $row['comp'],
                    'description' => $description,
                    'credit' => $credit_amount,
                    'balance' => ($res['payable_balance'] + $credit_amount),
                    'posted_by' => $_SESSION['userId'],
                    'tbl' => $table
                );

                $re_result = insertAccountPayables($insert_data_payable);
                $new_payable_id = $re_result['new_voucher_id'];

                // Insertion in tbl account_journal_ledger
                $insert_data_journal = array(
                    'is_receivable' => 0,
                    'receivable_payable_id' => $new_payable_id,
                    'voucher' => $voucher,
                    'invoice_no' => $row['invoice_no'],
                    'company' => $row['comp'],
                    'description' => $description,
                    'is_bank' => $is_bank,
                    'payment_type' => $row['partial_payment_type'],
                    'account_id' => $row['partial_payment_method_id'],
                    'dated' => date('Y-m-d'),
                    'debit' => $credit_amount,
                    'balance' => ($balance_res['journal_balance'] + $credit_amount),
                    'posted_by' => $_SESSION['userId'],
                    'posted_on' => date('Y-m-d H:i:s'),
                    'tbl' => $table
                );

                insertJournalLedger($insert_data_journal);

                // Getting id of journal table for the specific record to update the status
                $select_journal_rec = $acttObj->read_specific(
                    "id",
                    "account_journal_ledger",
                    "is_receivable = 0 AND credit = '" . $credit_amount . "' 
					AND invoice_no = '" . $row['invoice_no'] . "' 
					AND dated = '" . $row['partial_dated'] . "' 
					AND payment_type = '" . $row['partial_payment_type'] . "' 
					AND account_id = '" . $row['partial_payment_method_id'] . "' 
					AND status = 'paid'"
                );

                // it will update the journal record for future, as we are not inserting any reversal record for specific parital rAmount
                updateJournalLedgerSingleRecordStatus('deleted', 'is_receivable = 0 AND id = ' . $select_journal_rec['id']);
            }

            // Delete Main Partial Record
            mysqli_query($con, "UPDATE expence_partial_payments SET deleted_flag = 1, deleted_by = '" . $_SESSION['UserName'] . "', deleted_on = '" . date('Y-m-d H:i:s') . "' WHERE is_partial = 1 AND id = " . $_GET['id']);

            // Deduction of Received Amount from main table
            mysqli_query($con, "UPDATE expence SET amountPaid = (amountPaid-$credit_amount) WHERE id = " . $edit_id);

            $acttObj->insert('daily_logs', ['action_id' => 51, 'user_id' => $_SESSION['userId'], 'details' => "Expense ID: " . $edit_id]);

    ?>

            <script>
                alert('Record successfully deleted.');
                window.location.href = "?action=partial&pay_id=<?php echo $edit_id; ?>";
                window.onunload = refreshParent;

                function refreshParent() {
                    window.opener.location.reload();
                }
            </script>

    <?php
        } else {
            $msg = '<div class="alert alert-danger col-md-6 col-md-offset-3 text-center h4"><b>Failed to deleted this record !</b></div>';
        }
    }

    ?>



    <div class="container-fluid">
        <?php
        $in_array = array(
            'Payable',
            'payable',
            'PAYABLE'
        );
        if ($row['is_paid'] == 1) {
            die('<div class="m-t-20 alert alert-danger text-center">
                <h4 class="m-b-0">Amount already paid for this invoice on ' . $misc->dated($row['paid_on']) . (($row['paid_by']) ? ' by ' . $row['paid_by'] : '') . '.</h4>
                </div>');
        }
        ?>
        <h3 class="text-center">Update Expense</h3>
        <p class="text-center text-danger">
            Total Amount for this Invoice/Voucher: <?php echo '<b>' . $final_sum . '</b>'; ?> <br>
            <?php
            if ($partial_received_amount) {
                $final_sum = ($final_sum - $partial_received_amount);
            ?>
                Received Amount: <b><?php echo $partial_received_amount; ?></b> <br>
                Balance/Payable Amount: <b><?php echo $remAmount; ?></b>
            <?php } ?>
        </p>
        <form action="" method="post" class="col-md-12">
            <span id="display_msg">
                <?php if (isset($msg) && !empty($msg)) {
                    echo $msg;
                } ?>
            </span>

            <?php if ($payment_rec_type == 'partial') { ?>
                <div class="row">
                    <div class="form-group col-sm-6 col-sm-offset-3">
                        <input name="title" id="title" class="form-control" type="text" value="<?php echo $title; ?>" placeholder="Enter title of amount (optional)" />
                    </div>
                </div>
            <?php } ?>

            <div class="form-group col-sm-6">
                <label>Amount Received *</label>
                <input oninput="value_amount()" name="rAmount" class="form-control" type="text" pattern="[0-9]+([\.|,][0-9]+)?"
                    step="0.01" id="rAmount" required='' value="<?php echo $final_sum; ?>" />
            </div>
            <div class="form-group col-sm-6">
                <label>Date *</label>
                <input name="rDate" type="date" class="form-control" required='' value="" />
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
                <label class="pull-right">
                    <a href="javascript:void(0)" onclick="return addNewPaymentMode()" title="Add New Detail" data-toggle="tooltip" class="btn btn-info btn-xs text-white" style="color: #fff;">
                        <i class="fa fa-plus"></i> New
                    </a>
                </label>
                <select class="form-control" id="payment_through" name="payment_through">
                </select>
            </div>

            <div class="col-sm-12 text-right">
                <button class="btn btn-primary" type="submit" id="btn_submit" name="submit">Submit &raquo;</button>
            </div>
        </form>
    </div>

    <?php if ($payment_rec_type == 'partial') { ?>
        <div class="container-fluid m-t-50">
            <?php
            $row_part = $acttObj->read_all('*', 'expence_partial_payments', 'deleted_flag = 0 AND is_partial = 1 AND expence_id = ' . $edit_id);

            if (mysqli_num_rows($row_part) > 0) { ?>
                <table class="table table-bordered table-hover table-condensed table-striped">
                    <thead>
                        <th>Title</th>
                        <th>Amount</th>
                        <th>Paid Date</th>
                        <th>Method</th>
                        <th class="text-center">Action</th>
                    </thead>
                    <tbody>
                        <?php while ($row_data = mysqli_fetch_assoc($row_part)) { ?>
                            <tr <?php if ((isset($_GET['edit']) || isset($_GET['del'])) && ($_GET['id'] == $row_data['id'])) {
                                    echo 'class="bg-success"';
                                } ?>>
                                <td title="<?php echo $row_data['title']; ?>">
                                    <?php echo substr($row_data['title'], 0, 30) ?: 'NIL'; ?>
                                </td>
                                <td>
                                    <?php echo $misc->numberFormat_fun($row_data['amount']); ?>
                                </td>
                                <td>
                                    <?php echo $misc->dated($row_data['payment_date']); ?>
                                </td>
                                <td>
                                    <?php
                                    if (!empty($row_data['payment_type'])) {

                                        $sql = "SELECT name, account_no, sort_code, iban_no FROM account_payment_modes WHERE id = " . $row_data['payment_method'];
                                        $result = mysqli_query($con, $sql);
                                        $row = mysqli_fetch_assoc($result);

                                        if ($row_data['payment_type'] == 'bacs') {
                                            $pyment_type = strtoupper($row_data['payment_type']);
                                        } else {
                                            $pyment_type = ucwords($row_data['payment_type']);
                                        }

                                        $pm =  $pyment_type . '<br><i style="font-size: 11px;">' . $row['name'];

                                        if ($row['account_no']) {
                                            $pm .=  ' <br> A/C: ' . $row['account_no'];
                                        }

                                        $pm .= '</i>';
                                    } else {
                                        $pm = 'N/A';
                                    }
                                    echo $pm;
                                    ?>
                                </td>
                                <td class="text-center">
                                    <a onclick='return confirm_delete();' href="<?php echo basename(__FILE__) . '?action=partial&pay_id=' . $edit_id . '&id=' . $row_data['id'] . '&del=1'; ?>" title="Trash Record" class="btn btn-danger btn-sm">
                                        <i class="fa fa-trash text-white"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            <?php } else {
                echo '<h3 class="text-danger text-center col-sm-12"> <span class="label label-danger">No partials added yet !</span></h3>';
            } ?>
        </div>
    <?php } ?>

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

<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/fontawesome.min.css" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
<script src="js/income_receive_amount.js"></script>

<script>
    <?php if ($payment_rec_type == 'full') { ?>

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

    <?php } ?>


    <?php if ($payment_rec_type == 'partial') { ?>

        function value_amount() {
            var amount_val = document.getElementById('rAmount');
            var display_msg = document.getElementById('display_msg');
            var btn_submit = document.getElementById('btn_submit');
            if (!(/^[-+]?\d*\.?\d*$/.test(amount_val.value))) {
                btn_submit.disabled = true;
                display_msg.innerHTML = '<div class="alert alert-danger col-md-6 col-md-offset-3 text-center h4"><b>Entered value is not a number !</b></div>';
            } else {
                if (amount_val.value > <?php echo $remAmount; ?>) {
                    btn_submit.disabled = true;
                    display_msg.innerHTML = '<div class="alert alert-danger col-md-6 col-md-offset-3 text-center h4"><b>Entered value is greater than remaining amount <?php echo $remAmount; ?></b></div>';
                } else if (amount_val.value < <?php echo $remAmount; ?>) {
                    btn_submit.disabled = false;
                    display_msg.innerHTML = '<div class="alert alert-warning col-md-6 col-md-offset-3 text-center h4"><b>Entered value is less than remaining amount <?php echo $remAmount; ?></b></div>';
                } else {
                    btn_submit.disabled = false;
                    display_msg.innerHTML = '';
                }
            }
        }

        function e_value_amount() {
            var e_amount_val = document.getElementById('e_rAmount');
            var e_display_msg = document.getElementById('e_display_msg');
            var e_btn_submit = document.getElementById('e_btn_submit');
            if (!(/^[-+]?\d*\.?\d*$/.test(e_amount_val.value))) {
                e_btn_submit.disabled = true;
                e_display_msg.innerHTML = '<div class="alert alert-danger col-md-6 col-md-offset-3 text-center h4"><b>Entered value is not a number !</b></div>';
            } else {
                if (e_amount_val.value > <?php echo $remAmount; ?>) {
                    e_btn_submit.disabled = true;
                    e_display_msg.innerHTML = '<div class="alert alert-warning col-md-6 col-md-offset-3 text-center h4"><b>Entered value is greater  than remaining amount <?php echo $remAmount; ?></b></div>';
                } else if (e_amount_val.value < <?php echo $remAmount; ?>) {
                    e_btn_submit.disabled = false;
                    e_display_msg.innerHTML = '<div class="alert alert-danger col-md-6 col-md-offset-3 text-center h4"><b>Entered value is less than remaining amount <?php echo $remAmount; ?></b></div>';
                } else {
                    e_btn_submit.disabled = false;
                    e_display_msg.innerHTML = '';
                }
            }
        }

        function confirm_delete() {
            var result = confirm("Are you sure to delete this record ?");
            if (result == true) {
                return true;
            } else {
                return false;
            }
        }

    <?php } ?>
</script>

</html>