<?php
// ini_set('display_errors', '1');
// ini_set('display_startup_errors', '1');
// error_reporting(E_ALL);

// Get the company's name abbreviation
function getCompanyName($company_id)
{
    global $acttObj;
    return $acttObj->read_specific("company_name", "income_company", " id = " . $company_id)['company_name'];
}

// Check if the income record already exists
function isIncomeRecordExists($parameters)
{
    global $acttObj;
    $chk_exist = $acttObj->read_specific(
        "count(id) as counter",
        "account_income",
        $parameters
    )['counter'];
    return $chk_exist;
}

// Check if the receivable record already exists
function isReceivableRecordExists($parameters)
{
    global $acttObj;
    $chk_exist = $acttObj->read_specific(
        "count(id) as counter",
        "account_receivable",
        $parameters
    )['counter'];
    return $chk_exist;
}

// Get the current balances
function getCurrentBalances($con)
{
    $sql = "SELECT 
        (SELECT IFNULL(balance, 0) FROM account_income ORDER BY id DESC LIMIT 1) AS balance,
        (SELECT IFNULL(balance, 0) FROM account_receivable ORDER BY id DESC LIMIT 1) AS recv_balance,
        (SELECT IFNULL(balance, 0) FROM account_journal_ledger ORDER BY id DESC LIMIT 1) AS journal_balance,
        (SELECT IFNULL(balance, 0) FROM account_journal_ledger WHERE is_bank = 1 ORDER BY id DESC LIMIT 1) AS journal_balance_bank,
        (SELECT IFNULL(balance, 0) FROM account_journal_ledger WHERE is_bank = 0 ORDER BY id DESC LIMIT 1) AS journal_balance_cash,
        (SELECT IFNULL(balance, 0) FROM account_expenses ORDER BY id DESC LIMIT 1) AS expense_balance,
        (SELECT IFNULL(balance, 0) FROM account_payables ORDER BY id DESC LIMIT 1) AS payable_balance";

    $result = mysqli_query($con, $sql);
    return mysqli_fetch_assoc($result);
}

// Expenses
function insertAccountExpenses($insert_data)
{
    global $acttObj;
    $new_voucher_id = $acttObj->insert('account_expenses', $insert_data, true);

    // if (preg_match('/\d+$/', $insert_data['voucher'], $matches)) { // get numbers only
    //     $voucher = 'JV-' . $matches[0];
    // } else {
    //     $voucher = $insert_data['voucher'];
    // }

    // $acttObj->editFun('account_expenses', $new_voucher_id, 'voucher', $voucher);

    return [
        'voucher' => $insert_data['voucher'],
        'account_expense_id' => $new_voucher_id
    ];
}

// Expenses
function insertAccountPayables($insert_data_rec, $payment_type = NULL)
{
    global $acttObj;
    $new_voucher_id = $acttObj->insert('account_payables', $insert_data_rec, true);

    // if (!empty($payment_type)) {
    //     if ($payment_type == 'cash') {
    //         $is_bank = '0';
    //         $voucher_label = 'CPV-';
    //     } else {
    //         $is_bank = '1';
    //         $voucher_label = 'BPV-';
    //     }

    //     if (preg_match('/\d+$/', $insert_data_rec['voucher'], $matches)) { // get numbers only
    //         $new_voucher = $voucher_label . $matches[0];
    //     } else {
    //         $new_voucher = $insert_data_rec['voucher'];
    //     }

    //     $acttObj->editFun('account_payables', $new_voucher_id, 'voucher', $new_voucher);
    // }

    return [
        'voucher' => $insert_data_rec['voucher'],
        'new_voucher_id' => $new_voucher_id,
    ];
}

// Insert into account_income table
function insertAccountIncome($insert_data)
{
    global $acttObj;
    $new_voucher_id = $acttObj->insert('account_income', $insert_data, true);
    return [
        'voucher' => $insert_data['voucher'],
        'account_income_id' => $new_voucher_id
    ];
}

// Insert into account_receivable table
function insertAccountReceivable($insert_data_rec, $payment_type = NULL)
{
    global $acttObj;
    $new_voucher_id = $acttObj->insert('account_receivable', $insert_data_rec, true);

    return [
        'voucher' => $insert_data_rec['voucher'],
        'new_voucher_id' => $new_voucher_id,
    ];
}

function insertJournalLedger($insert_data_journal, $payment_type = NULL)
{
    global $acttObj;

    // if ($payment_type == 'cash') {
    //     $voucher_label = 'CPV-';
    // } else {
    //     $voucher_label = 'BPV-';
    // }

    return $journal_voucher_id = $acttObj->insert('account_journal_ledger', $insert_data_journal, true);

    //$journal_voucher = $voucher_label . $journal_voucher_id;

    // updating voucher No 
    //$acttObj->editFun('account_journal_ledger', $journal_voucher_id, 'voucher', $journal_voucher);
}

// Main function to handle the overall logic
function processIncomeAndReceivable($con, $IncomeRecExistsParameters, $InsertAccountIncomeData, $AccountReceableData)
{
    global $acttObj;
    //$company_name_abrv = getCompanyName($company_id);

    if (!isIncomeRecordExists($IncomeRecExistsParameters)) {
        $balances = getCurrentBalances($con);

        $voucher = insertAccountIncome($InsertAccountIncomeData);
        insertAccountReceivable($AccountReceableData);
    }
}

function updateJournalLedgerStatus($status, $is_receivable, $invoice_no)
{
    global $acttObj;
    $acttObj->db_query(
        "UPDATE account_journal_ledger 
        SET status = '" . $status . "', updated_by = '" . $_SESSION['userId'] . "', updated_on = '" . date("Y-m-d H:i:s") . "'
        WHERE is_receivable = '" . $is_receivable . "' AND invoice_no = '" . $invoice_no . "'"
    );
}

function updateJournalLedgerSingleRecordStatus($status, $str_where)
{
    global $acttObj;
    // echo "UPDATE account_journal_ledger 
    //     SET status = '" . $status . "' 
    //     WHERE $str_where 
    // ";
    $acttObj->db_query(
        "UPDATE account_journal_ledger 
        SET status = '" . $status . "', updated_by = '" . $_SESSION['userId'] . "', updated_on = '" . date("Y-m-d H:i:s") . "'
        WHERE $str_where 
    "
    );
}


function getNextVoucherCount($type)
{
    global $acttObj;

    // Convert to uppercase to match DB values like 'JV', 'BPV', 'CPV'
    $type = strtoupper($type);

    // Prepare the SQL statement to fetch the current count
    $nextCount = $acttObj->read_specific(
        "(counts)+1 as nextCount",
        "invoice_vouchers",
        "title = '" . $type . "'"
    )['nextCount'];

    return $nextCount;
}

function updateVoucherCounter($type, $count_val)
{
    global $acttObj;

    // Convert to uppercase to match DB values like 'JV', 'BPV', 'CPV'
    $type = strtoupper($type);
    if ($acttObj->db_query("UPDATE invoice_vouchers SET counts = " . $count_val . " WHERE title = '" . $type . "'")) {
        return 1;
    } else {
        return 0;
    }
}
