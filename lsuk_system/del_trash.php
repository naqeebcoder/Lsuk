<?php
session_start();

if (session_id() == '' || !isset($_SESSION['UserName'])) {
    header("location: index.php?unauth=1");
    exit;
}

include 'db.php';
include 'class.php';
include 'inc_functions.php';

$del_id = @$_GET['del_id'];
$table = @$_GET['table'];
$is_home = @$_GET['is_home'];
$allowed_type_idz = "3,17,30,45,63,96,104,110,115";
//Check if user has current action allowed
if ($_SESSION['is_root'] == 0) {
    $get_page_access = $acttObj->read_specific("id", "action_permissions", "user_id=" . $_SESSION['userId'] . " AND action_id IN (" . $allowed_type_idz . ")")['id'];
    if (empty($get_page_access)) {
        die("<center><h2 class='text-center text-danger'>You do not have access to <u>Delete Record</u> action!<br>Kindly contact admin for further process.</h2></center>");
    }
}
?>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
    <title>Delete Record</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="css/bootstrap.css">
    <style>
        .b {
            color: #fff;
        }

        a:link,
        a:visited {
            color: #337ab7;
        }
    </style>
    <?php
    if (isset($_POST['yes'])) {
        $array_update = array("deleted_flag" => 1, "deleted_by" => $_SESSION['UserName'], "deleted_date" => date("Y-m-d H:i:s"), "deleted_reason" => mysqli_real_escape_string($con, trim($_POST['deleted_reason'])));

        if ($_POST['deleted_reason'] == "Other" && $_POST['note']) {
            $array_update["deleted_reason"] = mysqli_real_escape_string($con, trim($_POST['note']));
        }
        $acttObj->update($table, $array_update, "id=" . $del_id);

        $array_table = array("interpreter" => "F2F Job ID: ", "telephone" => "TP Job ID: ", "translation" => "TR Job ID: ", "interpreter_reg" => "Interpreter ID: ", "comp_reg" => "Company ID: ", "expence" => "Expense ID: ", "pre_payments" => "Prepayment ID: ");
        $array_values = array("interpreter_reg" => 26, "comp_reg" => 27, "expence" => 32, "pre_payments" => 56);

        if (isset($is_home)) {
            $array_values['interpreter'] = 38;
            $array_values['telephone'] = 38;
            $array_values['translation'] = 38;
        } else {
            $array_values['interpreter'] = 14;
            $array_values['telephone'] = 14;
            $array_values['translation'] = 14;
        }

        $acttObj->insert("daily_logs", array("action_id" => $array_values[$table], "user_id" => $_SESSION['userId'], "details" => $array_table[$table] . $del_id));

        if (!empty($_POST['note'])) {
            $acttObj->insert('jobnotes', array('jobNote' => mysqli_real_escape_string($con, $_POST['note']), 'tbl' => $table, 'time' => $misc->sys_datetime_db(), 'fid' => $del_id, 'submitted' => $_SESSION['UserName'], 'dated' => date('Y-m-d')));
        }

        $acttObj->editFun($table, $del_id, 'edited_by', $_SESSION['UserName']);
        $acttObj->editFunNowDateTime($table, $del_id, 'edited_date');

        if($table !== 'pre_payments'){
            $acttObj->new_old_table('hist_' . $table, $table, $del_id);
        }

        /* =======================================  Account Statment for Expences only ============================
        # Do not change anything in below scripts ----- By Khurshid
        */
        if ($table == 'expence') {

            $invoice_infos = $acttObj->read_specific("*", $table, " id = " . $del_id);

            $expense_type = $acttObj->read_specific('title', 'expence_list', ' id = ' . $invoice_infos['type_id'])['title'];
            $current_date = date("Y-m-d");
            $description = '[Deleted][Expense] ' . $expense_type;
            if (!empty($_POST['note'])) {
                $description .= '<br>' . mysqli_real_escape_string($con, trim($_POST['note']));
            }
            $credit_amount = $invoice_infos['amoun'];

            // getting balance amount
            $res = getCurrentBalances($con);

            if($invoice_infos['payment_type']){
                if($invoice_infos['payment_type'] == 'cash'){
                    $voucher_label = 'CPV';
                } else {
                    $voucher_label = 'BPV';
                }                
            } else {
                $voucher_label = 'JV';
            }

            // Getting New Voucher Counter
            $voucher_counter = getNextVoucherCount($voucher_label);

            // Updating the new Voucher Counter
            updateVoucherCounter($voucher_label, $voucher_counter);

            $voucher = $voucher_label . '-' . $voucher_counter;

            // Insertion in tbl account_expenses
            $insert_data = array(
                'voucher' => $voucher,
                'invoice_no' => $invoice_infos['invoice_no'],
                'dated' => $current_date,
                'company' => $invoice_infos['comp'],
                'description' => $description,
                'credit' => $credit_amount,
                'balance' => ($res['expense_balance'] - $credit_amount),
                'posted_by' => $_SESSION['userId'],
                'tbl' => $table
            );

            $expense_res = insertAccountExpenses($insert_data);
            $jv_voucher = $expense_res['voucher'];

            // if Full Paid Expense
            if ($invoice_infos['status'] != 'unpaid' && ($invoice_infos['status'] == 'full_paid' || $invoice_infos['status'] == 'full_partial') && $invoice_infos['amountPaid'] > 0) {

                updateJournalLedgerStatus('deleted', 0, $invoice_infos['invoice_no']);

                if ($invoice_infos['payment_type'] == 'cash') {
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

                // Insertion in tbl account_journal_ledger
                $insert_data_journal = array(
                    'is_receivable' => 0,
                    'receivable_payable_id' => 0,
                    'voucher' => $voucher,
                    'invoice_no' => $invoice_infos['invoice_no'],
                    'company' => $invoice_infos['comp'],
                    'description' => $description,
                    'is_bank' => $is_bank,
                    'payment_type' => $invoice_infos['payment_type'],
                    'account_id' => $invoice_infos['payment_method_id'],
                    'dated' => date('Y-m-d'),
                    'debit' => $invoice_infos['amoun'], //OR can be use : $invoice_infos['amountPaid']
                    'balance' => ($balance_res['journal_balance'] + $invoice_infos['amoun']),
                    'posted_by' => $_SESSION['userId'],
                    'posted_on' => date('Y-m-d H:i:s'),
                    'tbl' => $table
                );

                insertJournalLedger($insert_data_journal);
            }

            // if Partial Payments
            if ($invoice_infos['status'] != 'unpaid' && $invoice_infos['status'] == 'partial' && $invoice_infos['amountPaid'] > 0) {

                // getting balance amount
                $res = getCurrentBalances($con);

                updateJournalLedgerStatus('deleted', 0, $invoice_infos['invoice_no']);

                $partial_payments = $acttObj->read_specific("SUM(amount) as recieved_partial_amount, expence_partial_payments.*", "expence_partial_payments", "expence_id = '" . $del_id . "' AND deleted_flag = 0 AND is_partial = 1");

                $recieved_partial_amount = ($partial_payments['recieved_partial_amount']) ? $partial_payments['recieved_partial_amount'] : 0;

                $remaining_invoice_amount = ($invoice_infos['amoun'] - $recieved_partial_amount);

                if ($partial_payments['payment_type'] == 'cash') {
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

                $insert_data_payable = array(
                    'voucher' => $voucher,
                    'invoice_no' => $invoice_infos['invoice_no'],
                    'dated' => $current_date,
                    'company' => $invoice_infos['comp'],
                    'description' => $description,
                    'debit' => $remaining_invoice_amount,
                    'balance' => ($res['payable_balance'] - $remaining_invoice_amount),
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
                    'invoice_no' => $invoice_infos['invoice_no'],
                    'company' => $invoice_infos['comp'],
                    'description' => $description,
                    'is_bank' => $is_bank,
                    'payment_type' => $invoice_infos['payment_type'],
                    'account_id' => $invoice_infos['payment_method_id'],
                    'dated' => date('Y-m-d'),
                    'debit' => $recieved_partial_amount,
                    'balance' => ($balance_res['journal_balance'] + $recieved_partial_amount),
                    'posted_by' => $_SESSION['userId'],
                    'posted_on' => date('Y-m-d H:i:s'),
                    'tbl' => $table
                );

                insertJournalLedger($insert_data_journal);
            }

            if ($invoice_infos['status'] == 'unpaid' && $invoice_infos['amountPaid'] < 1) {
                $insert_data_payable = array(
                    'voucher' => $voucher,
                    'invoice_no' => $invoice_infos['invoice_no'],
                    'dated' => $current_date,
                    'company' => $invoice_infos['comp'],
                    'description' => $description,
                    'debit' => $credit_amount,
                    'balance' => ($res['payable_balance'] - $credit_amount),
                    'posted_by' => $_SESSION['userId'],
                    'tbl' => $table
                );

                $re_result = insertAccountPayables($insert_data_payable);
                $new_payable_id = $re_result['new_voucher_id'];
            }

            // Delete Main Partial Record
            mysqli_query($con, "UPDATE expence_partial_payments SET deleted_flag = 1, deleted_by = '" . $_SESSION['UserName'] . "', deleted_on = '" . date('Y-m-d H:i:s') . "' WHERE deleted_flag = 0 AND expence_id = " . $del_id);

            // Deduction of Received Amount from main table
            mysqli_query($con, "UPDATE expence SET amountPaid = 0, status = 'unpaid', pay_by = '', payment_type = '', payment_method_id = '', is_paid = 0, paid_on = '', paid_by = '' WHERE id = " . $del_id);
        }


        /* =======================================  Account Statment for Prepayments only ============================
        # Do not change anything in below scripts ----- By Khurshid
        */
        if ($table === 'pre_payments') {

            $row = $acttObj->read_specific("*", $table, " id = " . $del_id);

            $category_title = $acttObj->read_specific('title', 'prepayment_categories', ' id = ' . $row['category_id'])['title'];
            $receiver_name = $acttObj->read_specific('title', 'prepayment_receivers', ' id = ' . $row['receiver_id'])['title'];

              $current_date = date("Y-m-d");
              $description = '[Deleted][Prepayment] ' . $category_title;
              if (!empty($row['description'])) {
                $description .= '<br>Details: ' . $row['description'];
              }
            $credit_amount = $row['total_amount'];

            // getting balance amount
            $res = getCurrentBalances($con);

            if($row['is_payable'] == 1){
                
                // Getting New Voucher Counter
                $voucher_counter = getNextVoucherCount('JV');
                $voucher = $voucher_label . '-' . $voucher_counter;
                updateVoucherCounter($voucher_label, $voucher_counter);
            
                $insert_data_payable = array(
                    'voucher' => $voucher,
                    'invoice_no' => $row['invoice_no'],
                    'dated' => $current_date,
                    'company' => $receiver_name,
                    'description' => $description,
                    'debit' => $credit_amount,
                    'balance' => ($res['payable_balance'] - $credit_amount),
                    'posted_by' => $_SESSION['userId'],
                    'tbl' => $table
                );

                $re_result = insertAccountPayables($insert_data_payable);
                $new_payable_id = $re_result['new_voucher_id'];
            }

            if($row['is_payable'] == 0){

                if ($row['payment_type'] == 'cash') {
                    $voucher_label = 'CPV';
                    $is_bank = '0';
                } else {
                    $voucher_label = 'BPV';
                    $is_bank = '1';
                }

                // Getting New Voucher Counter
                $voucher_counter = getNextVoucherCount($voucher_label);
                $voucher = $voucher_label . '-' . $voucher_counter;
                updateVoucherCounter($voucher_label, $voucher_counter);
            
                // Insertion in tbl account_journal_ledger
                $insert_data_journal = array(
                    'is_receivable' => 2,
                    'receivable_payable_id' => $del_id,
                    'voucher' => $voucher,
                    'invoice_no' => $row['invoice_no'],
                    'company' => $receiver_name,
                    'description' => $description,
                    'is_bank' => $is_bank,
                    'payment_type' => $row['payment_type'],
                    'account_id' => $row['payment_method_id'],
                    'dated' => date('Y-m-d'),
                    'debit' => $credit_amount,
                    'balance' => ($balance_res['journal_balance'] + $credit_amount),
                    'posted_by' => $_SESSION['userId'],
                    'posted_on' => date('Y-m-d H:i:s'),
                    'tbl' => $table
                );

                insertJournalLedger($insert_data_journal);

                updateJournalLedgerStatus('deleted', 2, $row['invoice_no']); // 2 == pre_payments tbl (is_receivable column)

                mysqli_query($con, "UPDATE ".$table." SET is_payable = 1, status = '', is_paid = '', paid_by = '', paid_on = '' WHERE id = " . $del_id);
            }        
        }

    ?>

        <script>
            window.onunload = refreshParent;

            function refreshParent() {
                window.opener.location.reload();
            }
            window.close();
        </script>
    <?php }
    if (isset($_POST['no'])) {
        echo "<script>window.close();</script>";
    }
    ?>
</head>

<body>
    <center>
        <h3>Record ID: <span class="label label-danger"><?php echo @$_GET['del_id']; ?></span></h3><br />
        <h3>Are you sure to <span class="text-danger"><b>Delete</b></span> this record?</h3>
    </center>
    <form action="" method="post">
        <div class="form-group col-sm-6">
            
            <?php
            $reasons = [];
            $tables = ['interpreter', 'translation', 'telephone'];
            if (in_array($table, $tables)) {
                $cur_table = 'orders';
            } else {
                $cur_table = $table;
            }
            $reasons_q = $acttObj->read_all("*", "reasons_job_delete", "deleted_flag = 0 AND for_table = '$cur_table'");
            while ($row = mysqli_fetch_assoc($reasons_q)) {
                $reasons[] = $row;
            }
            ?>
            <!-- Selector: client or LSUK -->
            <div class="form-group <?php if($cur_table != 'orders') echo 'hidden' ?>">
                <select class="form-control" id="reason_type_selector" onchange="filterReasons()" required>
                    <option value="" disabled selected>-- Select Type --</option>
                    <option value="cl">Client</option>
                    <option value="lsuk">LSUK</option>
                </select>
            </div>
            <div class="form-group">
                <!-- Reason list (initially empty) -->
                <select class="form-control" required name="deleted_reason" id="deleted_reason" onchange="change()">
                    <option value="" disabled selected>-- Select Deletion Reason --</option>
                </select>
            </div>
            <br>
            <textarea placeholder="Write removal note for this record ..." name="note" id="note" rows="3" class="form-control hidden"></textarea>
        </div>
        <div class="form-group col-sm-6">
            <input type="submit" class="btn btn-primary" name="yes" value="Yes >" />&nbsp;&nbsp;<input class="btn btn-warning" type="submit" name="no" value="No" />
        </div>
    </form>
</body>
<script src="js/jquery-1.11.3.min.js"></script>
<script>
    function change() {
        if ($('#deleted_reason').val() == "Other") {
            $('#note').val("");
            $('#note').removeClass("hidden");
            $('#note').attr("required", "required");
        } else {
            $('#note').val($('#deleted_reason').val());
            $('#note').addClass("hidden");
            $('#note').removeAttr("required");
        }
    }
</script>
<script>
    const allReasons = <?= json_encode($reasons) ?>;

    function filterReasons() {
        const type = document.getElementById("reason_type_selector").value;
        const reasonSelect = document.getElementById("deleted_reason");

        // Clear existing options except 'Other'
        reasonSelect.innerHTML = '<option value="" disabled selected>-- Select Deletion Reason --</option>';

        // Add matching reasons
        allReasons.forEach(r => {
            if (r.text_for === type) {
                const opt = document.createElement("option");
                opt.value = r.detail;
                opt.textContent = r.detail;
                reasonSelect.appendChild(opt);
            }
        });

        // Add 'Other' at the end
        reasonSelect.appendChild(new Option("Other", "Other"));
    }
    <?php if($cur_table != 'orders') {?> 
    document.addEventListener("DOMContentLoaded", function() {
        document.getElementById("reason_type_selector").value = "lsuk";
        filterReasons();
    });
    <?php } ?>
</script>

</html>