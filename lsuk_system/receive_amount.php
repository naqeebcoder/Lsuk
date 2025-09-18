<?php include 'db.php';
include 'class.php';
include 'inc_functions.php';

$table = $_GET['table'];
$tbl = array("interpreter" => "int", "telephone" => "tp", "translation" => "tr");
$edit_id = $_GET['row_id'];
$allowed_type_idz = "74,86,177";
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

    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/fontawesome.min.css" />

    <style>
        .b {
            color: #fff;
        }

        a:link,
        a:visited {
            color: #337ab7;
        }

        .text-white {
            color: #fff !important;
        }
    </style>

</head>

<body>
    <?php $query = "SELECT * FROM $table where id=$edit_id";
    $result = mysqli_query($con, $query);
    $row = mysqli_fetch_array($result);
    $rAmount = $misc->numberFormat_fun($row['rAmount']);
    $rDate = $row['rDate'];

    $payment_type = $row['payment_type'];
    $payment_method_id = $row['payment_method_id'];

    if (!empty($rDate) && $rDate != '0000-00-00' && $rDate != '1001-01-01') {
        $previous_date = date('Y-m-d', strtotime($rDate . '-31 days'));
    } else {
        $previous_date = date('Y-m-d', strtotime(date('Y-m-d') . '-31 days'));
    }
    $future_date = date('Y-m-d', strtotime(date('Y-m-d') . '+1 days'));

    if ($table == 'interpreter') {
        $alltotal_charges_comp = $row['total_charges_comp'] * $row["cur_vat"] +
        $row['total_charges_comp'] + $row['C_otherexpns'];

        $desc = 'Face to Face';
        $assignDate = $misc->dated($row['assignDate']);
        $assignTime = $misc->timeFormat($row['assignTime']);

    } elseif ($table == 'telephone') {
        $alltotal_charges_comp = ($row['total_charges_comp'] + $row['calCharges']) * $row["cur_vat"] +
        $row['total_charges_comp'] + $row['calCharges'];

        $desc = 'Remote';
        $assignDate = $misc->dated($row['assignDate']);
        $assignTime = $misc->timeFormat($row['assignTime']);

    } else {
        $alltotal_charges_comp = $row['total_charges_comp'] * $row["cur_vat"] +
        $row['total_charges_comp'];

        $desc = 'Translation';
        $assignTime = '';
        if($row['dueDate'] == '' || $row['dueDate'] == '0000-00-00'){
            $pay_terms = $acttObj->read_specific("payment_terms" , "comp_reg", "comp_reg.abrv = (SELECT orgName FROM $table WHERE id = $edit_id)")['payment_terms'];
            $pay_terms = "+".$pay_terms." days";
        }
        $assignDate = (($row['dueDate'] == '' || $row['dueDate'] == '0000-00-00') ? $misc->dated(date("Y-m-d", strtotime($pay_terms))) : $misc->dated($row['dueDate']));
    }

    $final_sum = $alltotal_charges_comp;

    if (isset($_POST['submit'])) {

        if (!empty($_POST['rAmount']) && $_POST['rAmount'] > 0 && is_numeric($_POST['rAmount'])) {

            $rAmount = $_POST['rAmount'];
            $rDate = $_POST['rDate'];

            $payment_type = $_POST['payment_type'];
            $payment_method_id = $_POST['payment_through'];

            if (empty($payment_type)) {
                $msg = '<div class="alert alert-danger col-md-6 col-md-offset-3 text-center h4"><b>You must fill atleast one payment method !</b></div>';
            } else {
                if (bccomp($rAmount, $final_sum, 2) == 0) {

                    $acttObj->editFun($table, $edit_id, 'rAmount', $rAmount);
                    $acttObj->editFun($table, $edit_id, 'rDate', $rDate);

                    $acttObj->editFun($table, $edit_id, 'payment_type', $payment_type);
                    $acttObj->editFun($table, $edit_id, 'payment_method_id', $payment_method_id);

                    $array_types = array("interpreter" => "F2F", "telephone" => "TP", "translation" => "TR");
                    $acttObj->insert("daily_logs", array("action_id" => 21, "user_id" => $_SESSION['userId'], "details" => $array_types[$table] . " Job ID: " . $edit_id));


                    /* Insertion Query to Accounts: Receivable & account_journal_ledger Table
                        - account_receivable : As Credit (balance - credit)
                        - account_journal_ledger : As Debit (balance + credit)
                    */

                    $current_date = date("Y-m-d");
                    $description = '[Received Payment]['.$desc.'] ' . $row['source'] . " to " . $row['target'] . " on " . $assignDate . " " . $assignTime;
                    $credit_amount = $rAmount;

                    // Checking if record already exists
                    $parameters = " invoice_no = '" . $row['invoiceNo'] . "' AND dated = '" . $current_date . "' AND company = '" . $row['orgName'] . "' AND credit = '" . $credit_amount . "'";
                    $chk_exist = 0; //isReceivableRecordExists($parameters);

                    if ($chk_exist < 1 && $credit_amount > 0) {

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
                            'invoice_no' => $row['invoiceNo'],
                            'dated' => $current_date,
                            'company' => $row['orgName'],
                            'description' => $description,
                            'credit' => $credit_amount,
                            'balance' => ($res['recv_balance'] - $credit_amount),
                            'posted_by' => $_SESSION['userId'],
                            'posted_on' => date('Y-m-d H:i:s'),
                            'tbl' => $table
                        );

                        $re_result = insertAccountReceivable($insert_data, $payment_type);
                        //$voucher = $re_result['voucher'];
                        $new_voucher_id = $re_result['new_voucher_id'];

                        // Insertion in tbl account_journal_ledger
                        $insert_data_journal = array(
                            'is_receivable' => 1,
                            'receivable_payable_id' => $new_voucher_id,
                            'voucher' => $voucher,
                            'invoice_no' => $row['invoiceNo'],
                            'company' => $row['orgName'],
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

                        insertJournalLedger($insert_data_journal, $payment_type);
                    } // end if record exists


                    if (!empty($table) && !empty($edit_id)) {
                        $po_requested_ids = $acttObj->read_specific("GROUP_CONCAT(id) as po_requested", "po_requested", "order_id=" . $edit_id . " and order_type='" . $array_types[$table] . "'")["po_requested"];
                        if (!empty($po_requested_ids)) {
                            $acttObj->delete("po_requested", "id IN (" . $po_requested_ids . ")");
                        }
                    } ?>
                    <script>
                        alert('Amount Successfully updated for this job. Thank you!');
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


    ?>
    <div class="container">
        <?php
        $get_pr_st = $acttObj->read_specific("id", "partial_amounts", "order_id=$edit_id AND tbl='" . $tbl[$table] . "' AND status=1")['id'] ?: 0;
        if ($get_pr_st > 0) { ?>
            <div class="alert alert-warning col-md-6 col-md-offset-3 text-center h4"><b>This invoice is Partially Paid, new payment must be made through Partial Payment section. </b></div>
        <?php
            die();
            exit;
        }
        ?>

        <?php
        /*if ($row['commit'] == 1) { ?>
				<div class="alert alert-warning col-md-6 col-md-offset-3 text-center h4">
					The Invoice# <b><?php echo $row['invoiceNo']; ?></b> is already Paid. 
				</div>
			<?php
				die();
				exit;
			}*/
        ?>


        <form action="" method="post" class="col-md-12">
            <h3 class="text-center">Update Received Amount from Customer</h3>
            <p class="text-center text-danger"><b>NOTE :</b> Total Invoice Amount for this job is : <?php echo '<b>' . $final_sum . '</b>'; ?></p>

            <span id="display_msg"><?php if (isset($msg) && !empty($msg)) {
                                        echo $msg;
                                    } ?></span>

            <div class="form-group col-sm-6">
                <label>Amount Received *</label>
                <input oninput="value_amount()" name="rAmount" class="form-control" type="text" pattern="[0-9]+([\.|,][0-9]+)?"
                    step="0.01" id="rAmount" required='' value="<?php echo $rAmount; ?>" />
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
            <div class="form-group col-sm-12 text-right">
                <?php if ($rAmount < $final_sum) { ?>
                    <button class="btn btn-primary" type="submit" id="btn_submit" name="submit">Submit &raquo;</button>
                <?php } ?>
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