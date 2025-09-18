<?php
session_start();
include 'db.php';
include 'class.php';
include 'inc_functions.php';

if (isset($_POST['yes'])) {
  $del_id = @$_GET['del_id'];
  $table = @$_GET['table'];

  if ($acttObj->update($table, array('deleted_flag' => 0), "id=" . $del_id)) {

    /* =======================================  Account Statment for Expences only ============================
    # Do not change anything in below scripts ----- By Khurshid
    */
    if ($table == 'expence' && $del_id) {

      $invoice_infos = $acttObj->read_specific("*", $table, " id = " . $del_id);

      // Account Statement Insertion
      $expense_type = $acttObj->read_specific('title', 'expence_list', ' id = ' . $invoice_infos['type_id'])['title'];
      $current_date = date("Y-m-d");
      $description = '[Restored][Expense] ' . $expense_type . ', Company: ' . $invoice_infos['comp'];

      $credit_amount = $invoice_infos['amoun'];

      // getting balance amount
      $res = getCurrentBalances($con);

      // Getting New Voucher Counter
      $voucher_counter = getNextVoucherCount('JV');

      // Updating the new Voucher Counter
      updateVoucherCounter('JV', $voucher_counter);

      $voucher = 'JV-' . $voucher_counter;

      // Insertion in tbl account_expenses
      $insert_data = array(
        'voucher' => $voucher,
        'invoice_no' => $invoice_infos['invoice_no'],
        'dated' => $current_date,
        'company' => $invoice_infos['comp'],
        'description' => $description,
        'debit' => $credit_amount,
        'balance' => ($res['expense_balance'] + $credit_amount),
        'posted_by' => $_SESSION['userId'],
        'tbl' => $table
      );
      $jv_voucher = insertAccountExpenses($insert_data);

      //   Insertion in tbl account_receivable
      $insert_data_payable = array(
        'voucher' => $voucher,
        'invoice_no' => $invoice_infos['invoice_no'],
        'dated' => $current_date,
        'company' => $invoice_infos['comp'],
        'description' => $description,
        'credit' => $credit_amount,
        'balance' => ($res['payable_balance'] + $credit_amount),
        'posted_by' => $_SESSION['userId'],
        'tbl' => $table
      );

      insertAccountPayables($insert_data_payable);
      // }

      mysqli_query($con, "UPDATE expence SET pay_by = 'PAYABLE' WHERE id = " . $del_id);
  
      // action_id = 53 ---- Restored Expense
      $acttObj->insert("daily_logs", array("action_id" => 53, "user_id" => $_SESSION['userId'], "details" => 'Expense# ' . $del_id));

    }


    /* =======================================  Account Statment for Prepayments only ============================
        # Do not change anything in below scripts ----- By Khurshid
        */
        if ($table === 'pre_payments') {

            $row = $acttObj->read_specific("*", $table, " id = " . $del_id);

            $category_title = $acttObj->read_specific('title', 'prepayment_categories', ' id = ' . $row['category_id'])['title'];
            $receiver_name = $acttObj->read_specific('title', 'prepayment_receivers', ' id = ' . $row['receiver_id'])['title'];

              $current_date = date("Y-m-d");
              $description = '[Restored][Prepayment] ' . $category_title;
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
                    'credit' => $credit_amount,
                    'balance' => ($res['payable_balance'] + $credit_amount),
                    'posted_by' => $_SESSION['userId'],
                    'tbl' => $table
                );

                $re_result = insertAccountPayables($insert_data_payable);
                $new_payable_id = $re_result['new_voucher_id'];

                mysqli_query($con, "UPDATE ".$table." SET status = 'payable' WHERE id = " . $del_id);
            }


            // This will not execute, its for security purpose incase, as is_payable will always be 1 after deletion.
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
                    'credit' => $credit_amount,
                    'balance' => ($balance_res['journal_balance'] - $credit_amount),
                    'posted_by' => $_SESSION['userId'],
                    'posted_on' => date('Y-m-d H:i:s'),
                    'tbl' => $table
                );

                insertJournalLedger($insert_data_journal);

                mysqli_query($con, "UPDATE ".$table." SET is_payable = 0, status = 'paid', is_paid = 1, paid_by = '".$_SESSION['UserName']."', paid_on = '".date('Y-m-d H:i:s')."' WHERE id = " . $del_id);
            }        
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
};
?>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
  <title>Restore Record</title>
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
</head>

<body>
  <br />
  <div align="center">
    <h1>Record ID: <span class="label label-info"><?php echo @$_GET['del_id']; ?></span></h1><br />
    <form action="" method="post" class="col-xs-12">
      <h3 class="h4">Are you sure you want to <span class="text-success"><b>Restore</b></span> this record ?</h3>
      <input type="submit" class="btn btn-primary" name="yes" value="Yes >" />&nbsp;&nbsp;<input class="btn btn-warning" type="submit" name="no" value="No" />
    </form>
  </div>