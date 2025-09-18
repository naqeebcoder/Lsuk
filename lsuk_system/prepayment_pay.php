<?php

include 'db.php';
include 'class.php';
include 'inc_functions.php';

$allowed_type_idz = "248";
//Check if user has current action allowed
if ($_SESSION['is_root'] == 0) {
    $get_page_access = $acttObj->read_specific("id", "action_permissions", "user_id=" . $_SESSION['userId'] . " AND action_id IN (" . $allowed_type_idz . ")")['id'];
    if (empty($get_page_access)) {
        die("<center><h2 class='text-center text-danger'>You do not have access to <u>Add Payment</u> action for jobs!<br>Kindly contact admin for further process.</h2></center>");
    }
}


$table = 'pre_payments';
$edit_id = $_GET['pay_id'];
$payment_rec_type = $action = $_GET['action'];

if (empty($edit_id) || !is_numeric($edit_id)) {
    die("<center><h2 class='text-center text-danger'>Invalid Request!</h2></center>");
}
?>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
    <title>Pay Prepayment Amount</title>
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

    $final_sum = $row['total_amount'];

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

                        $category_title = $acttObj->read_specific('title', 'prepayment_categories', ' id = ' . $row['category_id'])['title'];
                        $receiver_name = $acttObj->read_specific('title', 'prepayment_receivers', ' id = ' . $row['receiver_id'])['title'];

                        $description = '[Paid][Prepayment] ' . $category_title;
                        if (!empty($row['description'])) {
                            $description .= '<br>Details: ' . $row['description'];
                        }

                        $acttObj->db_query("UPDATE pre_payments SET paid_amount = '".$_POST['rAmount']."', status = 'paid', is_payable = 0, payment_type = '".$payment_type."', payment_method_id = '".$payment_method_id."', is_paid = 1, paid_by = '".$_SESSION['UserName']."', paid_on = '".date('Y-m-d H:i:s')."' WHERE id = '$edit_id'");

                        $acttObj->insert("daily_logs", array("action_id" => 58, "user_id" => $_SESSION['userId'], "details" => "Prepayment ID: " . $edit_id));

                        // ************** Means Expense is Paid *********************

                        // Single Entry in tbl account_payables (Debit, Balance - Credit_amount) -- Reversal
                        // Single Entry in tbl account_journal_ledger (Debit, Balance - Credit_amount)

                        $credit_amount = $_POST['rAmount'];

                        // getting balance amount
                        $balance_res = getCurrentBalances($con);

                        // Getting New Voucher Counter
                        $voucher_counter = getNextVoucherCount($voucher_label);

                        $voucher = $voucher_label . '-' . $voucher_counter;

                        // Insertion in tbl account_receivable
                        $insert_data_payable = array(
                            'voucher' => $voucher,
                            'invoice_no' => $row['invoice_no'],
                            'dated' => date('Y-m-d'),
                            'company' => $receiver_name,
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
                            'is_receivable' => 2,
                            'receivable_payable_id' => $edit_id,
                            'voucher' => $voucher,
                            'invoice_no' => $row['invoice_no'],
                            'company' => $receiver_name,
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

                        // Updating the new Voucher Counter
                        updateVoucherCounter($voucher_label, $voucher_counter);

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
        } 
    }
    ?>


    <div class="container-fluid">
        <?php
        if ($row['is_paid'] == 1) {
            die('<div class="m-t-20 alert alert-danger text-center">
                <h4 class="m-b-0">Amount already paid for this invoice on ' . $misc->dated($row['paid_on']) . (($row['paid_by']) ? ' by ' . $row['paid_by'] : '') . '.</h4>
                </div>');
        }
        ?>
        <h3 class="text-center">Update/Pay Prepayment</h3>
        <p class="text-center text-danger">
            Total Amount for this Invoice/Voucher: <?php echo '<b>' . $final_sum . '</b>'; ?> <br>
        </p>
        <form action="" method="post" class="col-md-12">
            <span id="display_msg">
                <?php if (isset($msg) && !empty($msg)) {
                    echo $msg;
                } ?>
            </span>

            <div class="form-group col-sm-6">
                <label>Amount Received *</label>
                <input oninput="value_amount()" name="rAmount" class="form-control" type="text" pattern="[0-9]+([\.|,][0-9]+)?"
                    step="0.01" id="rAmount" required='' value="<?php echo $final_sum; ?>" />
            </div>
            <div class="form-group col-sm-6">
                <label>Date *</label>
                <input name="rDate" type="date" class="form-control" required value="<?php echo $row['payment_date']; ?>" />
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

</body>

<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/fontawesome.min.css" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
<script src="js/income_receive_amount.js"></script>

<script>
    <?php //if ($payment_rec_type == 'full') { ?>

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

    <?php //} ?>


</script>

</html>