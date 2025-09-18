<?php include 'db.php';
include 'class.php';
include 'inc_functions.php';

$table = $_GET['table'];
$edit_id = $_GET['row_id'];
$allowed_type_idz = "231";
//Check if user has current action allowed
if ($_SESSION['is_root'] == 0) {
    $get_page_access = $acttObj->read_specific("id", "action_permissions", "user_id=" . $_SESSION['userId'] . " AND action_id IN (" . $allowed_type_idz . ")")['id'];
    if (empty($get_page_access)) {
        die("<center><h2 class='text-center text-danger'>You do not have access to <u>Add Partial Payment</u> action for jobs!<br>Kindly contact admin for further process.</h2></center>");
    }
}
?>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
    <title>Receive Partial Amount</title>
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
<script>
    function confirm_delete() {
        var result = confirm("Are you sure to delete this record ?");
        if (result == true) {
            return true;
        } else {
            return false;
        }
    }
</script>

<body>
    <?php
    $edit = $_GET['edit'];
    $del = $_GET['del'];

    $query = "SELECT * FROM $table WHERE id = $edit_id";
    $result = mysqli_query($con, $query);
    $row = mysqli_fetch_array($result);
    $rAmount = $misc->numberFormat_fun($row['rAmount']);
    $rDate = $row['rDate'];

    $tbl = 'mult_inv';
    if (isset($_GET['del']) && isset($_GET['id'])) {

        $q_delete = "UPDATE partial_amounts SET status = 0 WHERE id = " . $_GET['id'];

        if (mysqli_query($con, $q_delete)) {
            // if (1 == 1) {

            $partial_details = $acttObj->read_specific('*', 'partial_amounts', ' tbl = "' . $tbl . '" AND id = ' . $_GET['id']);

            $acttObj->db_query("UPDATE " . $table . " SET rAmount = (rAmount - " . $partial_details['amount'] . ") WHERE id = '" . $edit_id . "'");

            $mult_inv_amount = $acttObj->read_specific('mult_amount, rAmount', $table, ' id = ' . $edit_id);

            if ($mult_inv_amount['rAmount'] == 0) {
                $update_values = array(
                    'status' => '',
                    'paid_date' => '1001-01-01',
                    'paid_by' => '',
                    'paid_on' => '',
                    'payment_type' => '',
                    'payment_method_id' => ''
                );

                $acttObj->update($table, $update_values, array("id" => $edit_id));
            }

            /* Insertion Query to Accounts: Receivable & account_journal_ledger Table
				- account_receivable : As Debit (balance + credit)
				- account_journal_ledger : As Credit (balance - credit)
			*/

            $current_date = date("Y-m-d");
            $description = '[Deleted][Collective Invoice] Company: ' . $row['comp_abrv'] . ", Invoice# " . $row['m_inv'];

            // Checking if record already exists
            $parameters = " invoice_no = '" . $row['m_inv'] . "' AND dated = '" . $current_date . "' AND company = '" . $row['comp_abrv'] . "' AND debit = '" . $partial_details['amount'] . "'";

            $chk_exist = 0; //isReceivableRecordExists($parameters);

            $credit_amount = $partial_details['amount'];

            if ($chk_exist < 1 && $credit_amount > 0) {

                // getting balance amount
                $res = getCurrentBalances($con);

                if ($partial_details['payment_type'] == 'cash') {
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
                    'debit' => $credit_amount,
                    'balance' => ($res['recv_balance'] + $credit_amount),
                    'posted_by' => $_SESSION['userId'],
                    'posted_on' => date('Y-m-d H:i:s'),
                    'tbl' => $table
                );

                $re_result = insertAccountReceivable($insert_data);
                //$voucher = $re_result['voucher'];
                $new_voucher_id = $re_result['new_voucher_id'];

                // Getting id of journal table for the specific record to update the status
                $select_journal_rec = $acttObj->read_specific(
                    "id",
                    "account_journal_ledger",
                    "is_receivable = 1 AND debit = '" . $credit_amount . "' 
                    AND invoice_no = '" . $partial_details['order_id'] . "' 
                    AND payment_type = '" . $partial_details['payment_type'] . "' 
                    AND account_id = '" . $partial_details['payment_method_id'] . "' 
                    AND status = 'paid'"
                );

                // it will update the journal record for future, as we are not inserting any reversal record for specific parital rAmount
                updateJournalLedgerSingleRecordStatus('deleted', 'is_receivable = 1 AND id = ' . $select_journal_rec['id']);

                // Insertion in tbl account_journal_ledger --- No reversal entry in journal for paid amount.
                $insert_data_journal = array(
                    'is_receivable' => 1,
                    'receivable_payable_id' => $new_voucher_id,
                    'voucher' => $voucher,
                    'invoice_no' => $row['m_inv'],
                    'company' => $row['comp_abrv'],
                    'description' => $description,
                    'is_bank' => $is_bank,
                    'payment_type' => $partial_details['payment_type'],
                    'account_id' => $partial_details['payment_method_id'],
                    'dated' => $current_date,
                    'credit' => $credit_amount,
                    'balance' => ($res['journal_balance'] - $credit_amount),
                    'posted_by' => $_SESSION['userId'],
                    'posted_on' => date('Y-m-d H:i:s'),
                    'tbl' => $table
                );

                insertJournalLedger($insert_data_journal);
            } // end if record exists

            $msg = '<div class="alert alert-success col-md-6 col-md-offset-3 text-center h4"><b>Record successfully deleted.</b></div>'; ?>
            <script>
                alert('Amount Successfully updated for this job. Thank you!');
                window.location.href = "multi_receive_part.php?row_id=<?php echo $edit_id; ?>&table=<?php echo $table; ?>";
                /*window.onunload = refreshParent;

                function refreshParent() {
                    window.opener.location.reload();
                }*/
            </script>
            <?php
        } else {
            $msg = '<div class="alert alert-danger col-md-6 col-md-offset-3 text-center h4"><b>Failed to deleted this record !</b></div>';
        }
    }

    $sum_total_partial_amount = $acttObj->read_specific('ROUND(SUM(amount), 2) as partial_amount', 'partial_amounts', ' status="1" AND tbl = "' . $tbl . '" AND order_id = "' . $row['m_inv'] . '"')['partial_amount'];
    if ($sum_total_partial_amount > 0) {
        $final_sum = ($row['mult_amount'] - $sum_total_partial_amount);
    } else {
        $final_sum = $row['mult_amount'];
    }


    if (isset($_POST['partial_submit'])) {

        if (!empty($_POST['rAmount']) && $_POST['rAmount'] > 0 && is_numeric($_POST['rAmount'])) {
            $title = $_POST['title'];
            $rAmount = $_POST['rAmount'];
            $rDate = $_POST['paid_date'];
            $payment_type = $_POST['payment_type'];
            $payment_method_id = $_POST['payment_through'];

            $insert_array = array('payment_type' => $payment_type, 'payment_method_id' => $payment_method_id, 'order_id' => $row['m_inv'], 'amount' => $rAmount, 'dated' => $rDate, 'title' => $title, 'tbl' => $tbl);

            $row_part = $acttObj->read_specific('ROUND(SUM(amount), 2) as amount', 'partial_amounts', ' status="1" and tbl="' . $tbl . '" and order_id="' . $row['m_inv'] . '"');

            if ($rAmount > $final_sum) {
                $msg = '<div class="alert alert-danger col-md-6 col-md-offset-3 text-center h4"><b>Amount already reached to multi invoice value !</b></div>';
            } else {
                if ($acttObj->insert('partial_amounts', $insert_array)) {
                    $acttObj->editFun($table, $edit_id, 'rAmount', $row_part['amount'] + $rAmount);
                    $acttObj->editFun($table, $edit_id, 'paid_date', $rDate);

                    $final_sum <= ($row_part['amount'] + $rAmount) ? $acttObj->editFun_comp('mult_inv', 'status', 'Received', 'm_inv', $row['m_inv']) : $acttObj->editFun_comp('mult_inv', 'status', 'Partially Received', 'm_inv', $row['m_inv']);

                    $array_types = array("interpreter" => "F2F", "telephone" => "TP", "translation" => "TR", "mult_inv" => "multi_invoice");
                    $acttObj->insert("daily_logs", array("action_id" => 29, "user_id" => $_SESSION['userId'], "details" => $array_types[$table] . " ID: " . $row['m_inv']));


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
                        alert('Amount Successfully updated for this job. Thank you!');
                        window.close();
                        window.onunload = refreshParent;

                        function refreshParent() {
                            window.opener.location.reload();
                        }
                    </script>
                <?php
                }
            }
            // }
        } else {
            $msg = '<div class="alert alert-danger col-md-6 col-md-offset-3 text-center h4"><b>Fill up valid amount value & greater then 0.</b></div>';
        }
    }

    // Not In Use
    if (isset($_POST['partial_edit'])) {

        if (!empty($_POST['rAmount']) && $_POST['rAmount'] > 0 && is_numeric($_POST['rAmount'])) {
            $edit_hidden_id = $_POST['edit_hidden_id'];
            $edit_hidden_oid = $_POST['edit_hidden_oid'];
            $title = $_POST['title'];
            $rAmount = $_POST['rAmount'];
            $rDate = $_POST['paid_date'];

            $row_part = $acttObj->read_specific('sum(amount) as amount,order_id', 'partial_amounts', 'status="1" and id !=' . $edit_hidden_id . ' and order_id=' . $edit_hidden_oid);
            if ($row_part['amount'] >= $final_sum) {
                $msg = '<div class="alert alert-danger col-md-6 col-md-offset-3 text-center h4"><b>Amount already reached to invoice value !</b></div>';
            } else {

                $update_array = array('amount' => $rAmount, 'dated' => $rDate, 'title' => $title);
                $update_param = array('id' => $edit_hidden_id);
                $acttObj->update('partial_amounts', $update_array, $update_param);
                $acttObj->editFun($table, $row_part['order_id'], 'rAmount', $row_part['amount'] + $rAmount);
                $acttObj->editFun($table, $row_part['order_id'], 'paid_date', $rDate);
                $final_sum <= ($row_part['amount'] + $rAmount) ? $acttObj->editFun_comp('mult_inv', 'status', 'Received', 'm_inv', $row['m_inv']) : $acttObj->editFun_comp('mult_inv', 'status', 'Partially Received', 'm_inv', $row['m_inv']);

                ?>
                <script>
                    alert('Partial amount Successfully updated for this job. Thank you!');
                    window.onunload = refreshParent;

                    function refreshParent() {
                        window.opener.location.reload();
                    }
                </script>
    <?php
            }
            // }
        } else {
            $msg = '<div class="alert alert-danger col-md-6 col-md-offset-3 text-center h4"><b>Fill up valid amount value & greater then 0.</b></div>';
        }
    }

    if (isset($edit) && !empty($edit)) {
        $row_partials = $acttObj->read_specific('*', 'partial_amounts', 'id=' . $_GET['id']);
        $rAmount = $row_partials['amount'];
        $rDate = $row_partials['dated'];
        $edit_hidden_oid = $row_partials['order_id'];
        $edit_hidden_id = $row_partials['id'];
    }

    ?>
    <div class="container">
        <form action="" method="post" class="col-md-12">
            <h3 class="text-center">Update Received Amount from Customer</h3>
            <p class="text-center text-danger m-b-0">
                <b>NOTE :</b> Total Amount for this Invoice: <?php echo '<b>' . $misc->numberFormat_fun($row['mult_amount']) . '</b>'; ?>
            </p>

            <?php if ($sum_total_partial_amount > 0) { ?>
                <p class="text-center text-danger">
                    Amount Received:<?php echo '<b>' . $misc->numberFormat_fun($sum_total_partial_amount) . '</b>'; ?>
                    ||
                    Remaining Amount: <?php echo '<b>' . $misc->numberFormat_fun($final_sum) . '</b>'; ?>
                </p>
            <?php } ?>

            <span <?php if (isset($edit) && !empty($edit)) {
                        echo ' id="e_display_msg"';
                    } else { ?> id="display_msg" <?php } ?>><?php if (isset($msg) && !empty($msg)) {
                                                                echo $msg;
                                                            } ?></span>
            <div class="form-group col-sm-6 col-sm-offset-2">
                <input name="title" class="form-control" type="text" id="title" placeholder="Enter title of amount (optional)" />
            </div>
            <div class="form-group col-sm-6">
                <label>Amount Received *</label>
                <input oninput="value_amount()" name="rAmount" class="form-control" type="text" pattern="[0-9]+([\.|,][0-9]+)?" step="0.01" <?php if (isset($edit) && !empty($edit)) {
                                                                                                                                                echo 'id="e_rAmount"';
                                                                                                                                            } else { ?> id="rAmount" <?php } ?> required='' value="<?php echo $misc->numberFormat_fun($final_sum); ?>" />
            </div>
            <div class="form-group col-sm-6">
                <label>Date *</label>
                <input name="paid_date" type="date" class="form-control" required='' value="<?php echo !empty($rDate) && $rDate != '1001-01-01' ? $rDate : ''; ?>" />
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

            <?php if (isset($edit) && !empty($edit)) { ?>
                <div class="form-group col-sm-6">
                    <input name="edit_hidden_id" class="form-control" type="hidden" id="edit_hidden_id" value="<?php echo $edit_hidden_id; ?>" />
                    <input name="edit_hidden_oid" class="form-control" type="hidden" id="edit_hidden_oid" value="<?php echo $edit_hidden_oid; ?>" />
                </div>
            <?php } ?>
            <div class="col-sm-12 text-right">
                <button class="btn btn-primary" type="submit" <?php if (isset($edit) && !empty($edit)) { ?> id="e_btn_submit" name="partial_edit" <?php } else { ?> id="btn_submit" name="partial_submit" <?php } ?>>Submit &raquo;</button>
            </div>
        </form>

    </div>

    <div class="container m-t-20">
        <?php $row_part = $acttObj->read_all('*', 'partial_amounts', 'status="1" and tbl="' . $tbl . '" and order_id="' . $row['m_inv'] . '" ');
        if (mysqli_num_rows($row_part) > 0) { ?>
            <table class="table table-bordered table-hover table-striped">
                <thead>
                    <th>Title</th>
                    <th>Amount</th>
                    <th>Paid Date</th>
                    <!-- <th>Method</th> -->
                    <th class="text-center">Action</th>
                </thead>
                <tbody>
                    <?php while ($row_data = mysqli_fetch_assoc($row_part)) {
                        if ($row_data['tbl'] == 'int') {
                            $tab = 'interpreter';
                        } else if ($row_data['tbl'] == 'tp') {
                            $tab = 'telephone';
                        } else if ($row_data['tbl'] == 'mult_inv') {
                            $tab = 'mult_inv';
                        } else {
                            $tab = 'translation';
                        }
                    ?>
                        <tr <?php if ((isset($_GET['edit']) || isset($_GET['del'])) && ($_GET['id'] == $row_data['id'])) {
                                echo 'class="bg-success"';
                            } ?>>
                            <td title="<?php echo $row_data['title']; ?>"><?php echo substr($row_data['title'], 0, 30) ?: 'NIL'; ?></td>
                            <td><?php echo $row_data['amount']; ?></td>
                            <td><?php echo $row_data['dated']; ?></td>
                            <td align="center">
                                <!--a href="<?php //echo basename(__FILE__) . '?table=' . $tab . '&row_id=' . $row_data['order_id'] . '&id=' . $row_data['id'] . '&edit=1'; 
                                            ?>" title="Edit Record"><input type="image" src="images/icn_edit.png" title="Edit"></a-->
                                <a onclick='return confirm_delete();' href="<?php echo basename(__FILE__) . '?table=' . $tab . '&row_id=' . $row['id'] . '&id=' . $row_data['id'] . '&del=1'; ?>" title="Trash Record"><input type="image" src="images/icn_trash.png" title="Trash"></a< /td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        <?php } else {
            echo '<h3 class="text-danger text-center col-sm-12"> <span class="label label-danger">No partials added yet !</span></h3>';
        } ?>
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
                btn_submit.disabled = false;
                display_msg.innerHTML = '<div class="alert alert-danger col-md-6 col-md-offset-3 text-center h4"><b>Entered value is less than invoice amount <?php echo $final_sum; ?></b></div>';
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
            if (e_amount_val.value > <?php echo $final_sum; ?>) {
                e_btn_submit.disabled = true;
                e_display_msg.innerHTML = '<div class="alert alert-warning col-md-6 col-md-offset-3 text-center h4"><b>Entered value is greater  than invoice amount <?php echo $final_sum; ?></b></div>';
            } else if (e_amount_val.value < <?php echo $final_sum; ?>) {
                e_btn_submit.disabled = false;
                e_display_msg.innerHTML = '<div class="alert alert-danger col-md-6 col-md-offset-3 text-center h4"><b>Entered value is less than invoice amount <?php echo $final_sum; ?></b></div>';
            } else {
                e_btn_submit.disabled = false;
                e_display_msg.innerHTML = '';
            }
        }
    }
</script>

</html>