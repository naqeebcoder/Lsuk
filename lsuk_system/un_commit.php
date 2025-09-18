<?php if (session_id() == '' || !isset($_SESSION)) {
    session_start();
}
include 'db.php';
include 'class.php';
include 'inc_functions.php';

$allowed_type_idz = "78,90,181";
//Check if user has current action allowed
if ($_SESSION['is_root'] == 0) {
    $get_page_access = $acttObj->read_specific("id", "action_permissions", "user_id=" . $_SESSION['userId'] . " AND action_id IN (" . $allowed_type_idz . ")")['id'];
    if (empty($get_page_access)) {
        die("<center><h2 class='text-center text-danger'>You do not have access to <u>Un-commit Payment</u> action!<br>Kindly contact admin for further process.</h2></center>");
    }
}
?>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
    <title>Un-commit Record</title>
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
    <script>
        function refreshParent() {
            window.opener.location.reload();
        }
    </script>
    <?php if (isset($_POST['yes'])) {

        //............. paid to unpaid ..............//
        $array_types = array("interpreter" => "F2F", "telephone" => "TP", "translation" => "TR");

        // used for account statement description
        $array_types_statement = array("interpreter" => "Face to Face", "telephone" => "Remote", "translation" => "Translation");

        // Getting Existing records
        $row = $acttObj->read_specific(
            "count(id) as total_rec, " . $_GET['table'] . ".*",
            $_GET['table'],
            "commit = 1 AND id = " . $_GET['com_id']
        );

        $current_date = date("Y-m-d");
        
        if ($_GET['table'] == 'interpreter') {
            $totalforvat = $row['total_charges_comp'];
            $vatpay = $totalforvat * $row['cur_vat'];
            $totinvnow = $totalforvat + $vatpay + $row['C_otherexpns'];

            $assignDate = $misc->dated($row['assignDate']);

        } else if ($_GET['table'] == 'telephone') {
            $sub_total = $row['calCharges'] + $row['C_otherCharges'] + ($row['C_rateHour'] * $row['C_hoursWorkd']) + $row['C_admnchargs'];

            $makVat = $sub_total * $row['cur_vat'];
            $totinvnow = $sub_total + $makVat;

            $assignDate = $misc->dated($row['assignDate']);

        } else {
            $totalforvat = $row['total_charges_comp'];
            $vatpay = $totalforvat * $row['cur_vat'];
            $totinvnow = $totalforvat + $vatpay;

            $assignDate = $misc->dated($row['asignDate']);
        }

        $description = '[Un-commit][' . $array_types_statement[$_GET['table']] . '] ' . $row['source'] . " to " . $row['target'] . " on " . $assignDate;
        if ($_POST['note']) {
            $description .= "<br>Reason: " . mysqli_real_escape_string($con, $_POST['note']);
        }
        $credit_amount = $misc->numberFormat_fun($totinvnow);

        /* Insertion Query to Accounts: Income & Receivable Table
            - account_income : As Debit (balance - DueAmount)
            - account_receivable : As Credit (balance - DueAmount)
        */

        if ($_GET['st'] == 1) {

            if ($row['commit'] == 1 && $credit_amount > 0) {

                // getting balance amount
                $res = getCurrentBalances($con);

                $payment_type = ($row['payment_type']) ? $row['payment_type'] : 'JV';
                if ($payment_type == 'JV') {
                    $voucher_label = 'JV';
                } else {
                    if ($payment_type == 'cash') {
                        $voucher_label = 'CPV';
                        $is_bank = '0';
                    } else {
                        $voucher_label = 'BPV';
                        $is_bank = '1';
                    }
                }

                // Getting New Voucher Counter
                $voucher_counter = getNextVoucherCount($voucher_label);

                // Updating the new Voucher Counter
                updateVoucherCounter($voucher_label, $voucher_counter);

                $voucher = $voucher_label . '-' . $voucher_counter;

                // Insertion in tbl account_receivable
                $insert_data_rec = array(
                    'voucher' => $voucher,
                    'invoice_no' => $row['invoiceNo'],
                    'dated' => $current_date,
                    'company' => $row['orgName'],
                    'description' => $description,
                    'debit' => $credit_amount,
                    'balance' => ($res['recv_balance'] + $credit_amount),
                    'posted_by' => $_SESSION['userId'],
                    'tbl' => $_GET['table']
                );

                $re_result = insertAccountReceivable($insert_data_rec);
                //$voucher = $re_result['voucher'];
                $new_voucher_id = $re_result['new_voucher_id'];

                // check if Invoice Already paid or having partial payments
                if ($row['rAmount'] > 0) {
                    // Update the journal record for future use, as we are not inserting any reversal entry for specific rAmount
                    updateJournalLedgerStatus('uncommit', 1, $row['invoiceNo']);
                }
            } // end if record exists

            $acttObj->editFun($_GET['table'], $_GET['com_id'], 'rAmount', 0);

            $acttObj->insert("daily_logs", array("action_id" => 20, "user_id" => $_SESSION['userId'], "details" => $array_types[$_GET['table']] . " Job ID: " . $_GET['com_id']));
        } else {

            if ($row['commit'] == 1 && $credit_amount > 0) {

                // getting balance amount
                $res = getCurrentBalances($con);

                // Getting New Voucher Counter
                $voucher_counter = getNextVoucherCount('JV');

                // Updating the new Voucher Counter
                updateVoucherCounter('JV', $voucher_counter);

                $voucher = 'JV-' . $voucher_counter;

                $insert_data = array(
                    'voucher' => $voucher,
                    'invoice_no' => $row['invoiceNo'],
                    'dated' => $current_date,
                    'company' => $row['orgName'],
                    'description' => $description,
                    'debit' => $credit_amount,
                    'balance' => ($res['balance'] - $credit_amount),
                    'posted_by' => $_SESSION['userId'],
                    'tbl' => $_GET['table']
                );

                // Insertion in Account Income
                $jv_voucher = insertAccountIncome($insert_data);

                // Insertion in tbl account_receivable
                $insert_data_rec = array(
                    'voucher' => $voucher,
                    'invoice_no' => $row['invoiceNo'],
                    'dated' => $current_date,
                    'company' => $row['orgName'],
                    'description' => $description,
                    'credit' => $credit_amount,
                    'balance' => ($res['recv_balance'] - $credit_amount),
                    'posted_by' => $_SESSION['userId'],
                    'tbl' => $_GET['table']
                );

                $re_result = insertAccountReceivable($insert_data_rec);
                //$voucher = $re_result['voucher'];
                $new_voucher_id = $re_result['new_voucher_id'];
            } // end if record exists

            $acttObj->editFun($_GET['table'], $_GET['com_id'], 'commit', 0);

            $acttObj->insert("daily_logs", array("action_id" => 19, "user_id" => $_SESSION['userId'], "details" => $array_types[$_GET['table']] . " Job ID: " . $_GET['com_id']));
        }

        $acttObj->editFun($_GET['table'], $_GET['com_id'], 'dueDate', '');
        if (!empty($_POST['note'])) {
            $acttObj->insert('jobnotes', array('jobNote' => mysqli_real_escape_string($con, $_POST['note']), 'tbl' => $_GET['table'], 'time' => $misc->sys_datetime_db(), 'fid' => $_GET['com_id'], 'submitted' => $_SESSION['UserName'], 'dated' => date('Y-m-d')));
        } ?>
        <script>
            window.close();
            window.onunload = refreshParent;
        </script>
    <?php }
    if (isset($_POST['no'])) {
        echo "<script>window.close();</script>";
    } ?>
</head>

<body>
    <center>
        <h3>Record ID: <span class="label label-danger"><?php echo @$_GET['com_id']; ?></span></h3><br />
        <h3>Are you sure to <span class="text-warning"><b>Un-commit</b></span> this record ?</h3>
    </center>
    <form action="" method="post">
        <div class="form-group col-sm-6">
            <label>Mark un-commit note</label>
            <select class="form-control" required id="dropdown" onchange="change();">
                <option value="" selected disabled>Choose a reason</option>
                <option value="Wrong entry submitted">Wrong entry submitted</option>
                <option value="Other">Other</option>
            </select>
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
        if ($('#dropdown').val() == "Other") {
            $('#note').val("");
            $('#note').removeClass("hidden");
            $('#note').attr("required", "required");
        } else {
            $('#note').val($('#dropdown').val());
            $('#note').addClass("hidden");
            $('#note').removeAttr("required");
        }
    }
</script>

</html>