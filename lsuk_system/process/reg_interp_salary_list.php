<?php
session_start();

// Undo salary slip
if (isset($_POST['undo_salary_slip'])) {
    //include '../actions.php';
    include '../class.php';
    include('../inc_functions.php');

    $get_data = $acttObj->read_specific('*', 'interp_salary', 'id=' . $_POST['slip_id']);
    if ($get_data['deleted_flag'] == 0) {
        $interpreter_id = $get_data['interp'];
        $acttObj->update('interp_salary', array("deleted_flag" => 1, "deleted_by" => $_SESSION['userId'], "deleted_date" => date('Y-m-d H:i:s'), "deleted_reason" => trim($_POST['deleted_reason'])), "id=" . $_POST['slip_id']);
        $acttObj->update('request_paybacks', array("deleted_flag" => 1, "deleted_by" => $_SESSION['userId'], "deleted_date" => date('Y-m-d H:i:s')), "salary_id=" . $_POST['slip_id']);
        $get_query = $acttObj->read_all("id,total_charges_interp,'interpreter' as tbl", 'interpreter', "deleted_flag=0 and intrp_salary_comit=1 and salary_id=" . $_POST['slip_id'] . " and intrpName='$interpreter_id'
        UNION ALL
        select id,total_charges_interp,'telephone' as tbl from telephone WHERE deleted_flag=0 and intrp_salary_comit=1 and salary_id=" . $_POST['slip_id'] . " and intrpName='$interpreter_id'
        UNION ALL
        select id,total_charges_interp,'translation' as tbl from translation WHERE deleted_flag=0 and intrp_salary_comit=1 and salary_id=" . $_POST['slip_id'] . " and intrpName='$interpreter_id'");
        while ($row = $get_query->fetch_assoc()) {
            $acttObj->insert('hist_interp_salary', array('salary_id' => $_POST['slip_id'], 'tbl' => $row['tbl'], 'job_id' => $row['id'], 'job_amount' => $row['total_charges_interp'], 'dated' => date('Y-m-d H:i:s')));
            $acttObj->update($row['tbl'], array("intrp_salary_comit" => 0, "paid_date" => '1001-01-01', "salary_id" => 0), "id=" . $row['id']);
        }
        $done = $acttObj->insert("daily_logs", array("action_id" => 31, "user_id" => $_SESSION['userId'], "details" => "Salary ID: " . $_POST['slip_id']));
    }
    if ($done) {

        // Getting New Voucher Counter
        $v_label = 'JV';
        $voucher_counter = getNextVoucherCount($v_label);
        $voucher = $v_label . '-' . $voucher_counter;

        $current_date = date("Y-m-d");
        $interp_name = $acttObj->read_specific('name', 'interpreter_reg', 'id = ' . $interpreter_id)['name'];
        $description = '[Salary Slip] Undo/Rollback';
        $credit_amount = $get_data['salry'];

        /* ********************** Account Payables ********************** */
        
        // getting balance amount
        $res = getCurrentBalances($con);

        // Insertion in tbl account_receivable
        $insert_data_rec = array(
            'voucher' => $voucher,
            'invoice_no' => $get_data['invoice'],
            'dated' => $current_date,
            'company' => $interp_name,
            'description' => $description,
            'debit' => $credit_amount,
            'balance' => ($res['payable_balance'] - $credit_amount),
            'posted_by' => $_SESSION['userId'],
            'tbl' => 'interp_salary'
        );

        insertAccountPayables($insert_data_rec);

        // Updating the new Voucher Counter
        updateVoucherCounter($v_label, $voucher_counter);

        $_SESSION['returned_message'] = '<center><div class="alert alert-success alert-dismissible show col-md-8 col-md-offset-2" role="alert">
            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
            <strong>Success!</strong> Salary record has been successfully undone. Thank you
            </div></center>';
    } else {
        $_SESSION['returned_message'] = '<center><div class="alert alert-danger alert-dismissible show col-md-8 col-md-offset-2" role="alert">
            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
            <strong>Sorry!</strong> Failed to undo this salary slip! Please try again
            </div></center>';
    }
    header('Location: ' . $_POST['redirect_url']);
}

// Undo salary slip
if (isset($_POST['paid_salary_slip']) && !empty($_POST['paid_slip_id'])) {
    //include '../actions.php';
    include '../class.php';
    include('../inc_functions.php');

    $get_data = $acttObj->read_specific('*', 'interp_salary', 'id=' . $_POST['paid_slip_id']);
    
    if ($get_data['deleted_flag'] == 0) {
        $interpreter_id = $get_data['interp'];
        $acttObj->update('interp_salary', array("is_paid" => 1, "paid_by" => $_POST['paid_by'], "paid_date" => $_POST['paid_date'], "paid_action_by" => $_SESSION['userId'], "paid_action_date" => date('Y-m-d H:i:s'), "payment_type" => $_POST['payment_type'], "payment_method_id" => $_POST['payment_through']), "id=" . $_POST['paid_slip_id']);
        $done = $acttObj->insert("daily_logs", array("action_id" => 40, "user_id" => $_SESSION['userId'], "details" => "Salary ID: " . $_POST['paid_slip_id']));
    }
    if ($done) {

        if($_POST['payment_type'] === 'cash'){
            $v_label = 'CPV';
            $is_bank = 0;
        }else {
            $v_label = 'BPV';
            $is_bank = 1;
        }

        // Getting New Voucher Counter
        $voucher_counter = getNextVoucherCount($v_label);
        $voucher = $v_label . '-' . $voucher_counter;

        $current_date = date("Y-m-d");
        $interp_name = $acttObj->read_specific('name', 'interpreter_reg', 'id = ' . $interpreter_id)['name'];
        $description = '[Paid Salary Slip] ' . $interp_name;
        $credit_amount = $get_data['salry'];

        /* ********************** Account Payables ********************** */
        
        // getting balance amount
        $res = getCurrentBalances($con);

        // Insertion in tbl account_receivable
        $insert_data_rec = array(
            'voucher' => $voucher,
            'invoice_no' => $get_data['invoice'],
            'dated' => $current_date,
            'company' => $interp_name,
            'description' => $description,
            'debit' => $credit_amount,
            'balance' => ($res['payable_balance'] - $credit_amount),
            'posted_by' => $_SESSION['userId'],
            'tbl' => 'interp_salary'
        );

        insertAccountPayables($insert_data_rec);

        // Insertion in tbl account_journal_ledger
        $insert_data_journal = array(
            'is_receivable' => 3, // interp_salary
            'receivable_payable_id' => $get_data['id'],
            'voucher' => $voucher,
            'invoice_no' => $get_data['invoice'],
            'company' => $interp_name,
            'description' => $description,
            'is_bank' => $is_bank,
            'payment_type' => $_POST['payment_type'],
            'account_id' => $_POST['payment_through'],
            'dated' => $current_date,
            'credit' => $credit_amount,
            'balance' => ($balance_res['journal_balance'] - $credit_amount),
            'posted_by' => $_SESSION['userId'],
            'posted_on' => date('Y-m-d H:i:s'),
            'tbl' => 'interp_salary'
        );

        insertJournalLedger($insert_data_journal);

        // Updating the new Voucher Counter
        updateVoucherCounter($v_label, $voucher_counter);

        $_SESSION['returned_message'] = '<center><div class="alert alert-success alert-dismissible show col-md-8 col-md-offset-2" role="alert">
            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
            <strong>Success!</strong> Salary slip has been successfully marked as Paid. Thank you
            </div></center>'; 
    } else {
        $_SESSION['returned_message'] = '<center><div class="alert alert-danger alert-dismissible show col-md-8 col-md-offset-2" role="alert">
            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
            <strong>Sorry!</strong> Failed to mark this salary slip as Paid! Please try again
            </div></center>';
    }

    header('Location: ' . $_POST['redirect_url']);
}
