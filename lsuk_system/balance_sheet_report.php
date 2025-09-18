<?php 
include "userhaspage.php";
SysPermiss::UserHasPage(__FILE__);

if (session_id() == '' || !isset($_SESSION)) {
  session_start();
}
include 'db.php';
include 'class.php';

// $date_from = $_GET['date_from'];
// $date_to = $_GET['date_to'];

$start_date = $date_from = $_GET['date_from'];
$end_date = $date_to = $_GET['date_to'];

// $allowed_type_idz = "245";
// //Check if user has current action allowed
// if ($_SESSION['is_root'] == 0) {
//   $get_page_access = $acttObj->read_specific("id", "action_permissions", "user_id=" . $_SESSION['userId'] . " AND action_id IN (" . $allowed_type_idz . ")")['id'];
//   if (empty($get_page_access)) {
//     die("<center><h2 class='text-center text-danger'>You do not have access to <u>Edit Company Expense</u> action!<br>Kindly contact admin for further process.</h2></center>");
//   }
// }
?>
<?php

    
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
  <title>Balance Sheet</title>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" />
  <link rel="stylesheet" type="text/css" href="css/util.css" />

  <script>
	function myFunction() {
		var y = document.getElementById("date_from").value;
        if (!y) {
			y = "<?php echo $date_from; ?>";
		}
		var z = document.getElementById("date_to").value;
		if (!z) {
			z = "<?php echo $date_to; ?>";
		}
		
        window.location.href = '?date_from=' + y + '&date_to=' + z;
	}
</script>

</head>

<body>
    <?php include 'nav2.php';?>

  <div class="container m-b-100">
    
    <div class="row">

        <div class="page-header text-center m-t-0">
            <h1 class="m-t-0">Balance Sheet Report</h1>
        </div>

        <div class="">
            <div class="well ">
                <div class="row">
                    <div class="form-group col-md-3">
                        <label>Date (From)</label>
                        <input type="date" name="date_from" id="date_from" placeholder="" class="form-control" onchange="myFunction()" value="<?php echo $date_from ?>">
                    </div>
                    <div class="form-group col-md-3">
                        <label>Date (To)</label>
                        <input type="date" name="date_to" id="date_to" placeholder="" class="form-control" onchange="myFunction()" value="<?php echo $date_to ?>">
                    </div>
                    <div class="form-group col-md-2 m-t-28">
                        <button type="button" class="btn btn-warning" onclick="window.location.href='balance_sheet.php'">
                            Clear Filter
                        </button>
                    </div>

                    <div class="form-group col-md-4 m-t-28 text-right">
                        <a href="reports_lsuk/pdf/<?php echo basename(__FILE__); ?>?date_from=<?= $_GET['date_from'] ?>&date_to=<?= $_GET['date_to'] ?>" class="btn btn-success" target="_blank">Export as PDF</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

        <?php if (!empty($_GET['date_from']) && !empty($_GET['date_to'])) { ?>
            
        <div class="row">
            <div class="m-t-40">

                <?php 
                    // Collective Invoices sum
                    $assets_collective_invoices_sum = $acttObj->full_fetch_array("
                        SELECT IFNULL(SUM(mult_amount - rAmount),0) AS total_pending
                        FROM mult_inv
                        WHERE is_deleted = 0 AND commit = 1
                        AND (status = '' OR status = 'pending' OR status = 'Partially Received')
                        AND mult_amount > 0
                        AND dated BETWEEN '".$start_date."' AND '".$end_date."'
                    ");

                    // Manual Invoices sum
                    $assets_manual_invoices_sum = $acttObj->full_fetch_array("
                        SELECT IFNULL(SUM(total_amount - IFNULL(received_amount,0)),0) AS total_pending
                        FROM income_invoices
                        WHERE deleted_flag = 0 AND commit = 1
                        AND payment_status IN ('unpaid','partial')
                        AND created_at BETWEEN '".$start_date."' AND '".$end_date."'
                    ");

                    $prepayments_total = $acttObj->full_fetch_array("
                        SELECT 
                            IFNULL(SUM(p.paid_amount - COALESCE(e_sum.used_amount, 0)), 0) AS total_remaining_amount
                        FROM pre_payments p
                        LEFT JOIN (
                            SELECT prepayment_id, SUM(amountPaid) AS used_amount
                            FROM expence
                            WHERE is_prepayment = 1 AND is_paid = 1 AND deleted_flag = 0 AND status IN ('full_paid', 'partial', 'full_partial')
                            GROUP BY prepayment_id
                        ) e_sum ON e_sum.prepayment_id = p.invoice_no
                        WHERE p.is_paid = 1 AND p.is_payable = 0 AND p.status = 'paid' AND p.deleted_flag = 0
                        AND p.dated BETWEEN '".$start_date."' AND '".$end_date."'
                    ");

                    // Receivables sum (Interpreter + Telephone + Translation)
                    $assets_interp_tele_trans_sum = $acttObj->full_fetch_array("
                        SELECT IFNULL(SUM(pending_amount),0) AS total_pending
                        FROM (
                            SELECT ROUND(((i.total_charges_comp + (i.total_charges_comp * i.cur_vat)) + i.C_otherexpns),2) 
                                - ROUND(i.rAmount,2) AS pending_amount
                            FROM interpreter i
                            WHERE i.deleted_flag = 0 AND i.disposed_of = 0 AND i.multInv_flag = 0 
                            AND i.order_cancel_flag = 0 AND i.commit = 1
                            AND (ROUND(i.rAmount,2) < ROUND(((i.total_charges_comp + (i.total_charges_comp * i.cur_vat)) + i.C_otherexpns),2)
                                OR i.total_charges_comp = 0)
                            AND i.assignDate BETWEEN '".$start_date."' AND '".$end_date."'

                            UNION ALL

                            SELECT ROUND(((t.total_charges_comp + (t.total_charges_comp * t.cur_vat))),2) 
                                - ROUND(t.rAmount,2) AS pending_amount
                            FROM telephone t
                            WHERE t.deleted_flag = 0 AND t.disposed_of = 0 AND t.order_cancel_flag = 0 
                            AND t.commit = 1 AND t.multInv_flag = 0
                            AND (ROUND(t.rAmount,2) < ROUND(((t.total_charges_comp + (t.total_charges_comp * t.cur_vat))),2)
                                OR t.total_charges_comp = 0)
                            AND t.assignDate BETWEEN '".$start_date."' AND '".$end_date."'

                            UNION ALL

                            SELECT ROUND(((tr.total_charges_comp + (tr.total_charges_comp * tr.cur_vat))),2) 
                                - ROUND(tr.rAmount,2) AS pending_amount
                            FROM translation tr
                            WHERE tr.deleted_flag = 0 AND tr.disposed_of = 0 AND tr.order_cancel_flag = 0 
                            AND tr.commit = 1 AND tr.multInv_flag = 0
                            AND (ROUND(tr.rAmount,2) < ROUND(((tr.total_charges_comp + (tr.total_charges_comp * tr.cur_vat))),2)
                                OR tr.total_charges_comp = 0)
                            AND tr.asignDate BETWEEN '".$start_date."' AND '".$end_date."'
                        ) AS receivables
                    ");

                    $cash_in_hand = $acttObj->full_fetch_array("

                    -- Bank Balance
                    SELECT 'Bank' AS source,
                        ROUND(IFNULL(SUM(debit - credit),0),2) AS amount
                    FROM account_journal_ledger
                    WHERE is_bank = 1
                    AND DATE(dated) <= '".$end_date."'

                    UNION ALL

                    -- Cash in Hand
                    SELECT 'Cash' AS source,
                        ROUND(IFNULL(SUM(debit - credit),0),2) AS amount
                    FROM account_journal_ledger
                    WHERE is_bank = 0
                    AND DATE(dated) <= '".$end_date."'

                    UNION ALL

                    -- Retained Earnings (Income - Expenses)
                    SELECT 'Retained Earnings' AS source,
                        ROUND(
                            IFNULL((SELECT SUM(credit - debit) 
                                    FROM account_journal_ledger 
                                    WHERE is_receivable > 0 
                                    AND DATE(dated) <= '".$end_date."'),0)
                            -
                            IFNULL((SELECT SUM(debit - credit) 
                                    FROM account_journal_ledger 
                                    WHERE is_receivable = 0 
                                    AND DATE(dated) <= '".$end_date."'),0)
                        ,2) AS amount
                ");


                ?>

                <!-- Assets -->
                <div class="panel panel-primary">
                    <div class="panel-heading">
                    A. Assets
                    </div>
                    <div class="panel-body">
                    <!-- Current Assets -->
                    <h4>Current Assets  (≤ 1 year)</h4>
                    <table class="table table-bordered table-condensed">
                        <tbody>
                            <tr>
                                <td>Cash at Bank</td>
                                <td class="text-right">
                                    <?php 
                                        $total_cash_at_bank = (float) $cash_in_hand[0]['amount'];
                                        echo number_format($total_cash_at_bank, 2); 
                                    ?>
                                </td>
                            </tr>
                            <tr>
                                <td>Cash in Hand</td>
                                <td class="text-right">
                                    <?php 
                                        $total_cash_in_hand = (float) $cash_in_hand[1]['amount'];
                                        echo number_format($total_cash_in_hand, 2); 
                                    ?>
                                </td>
                            </tr>
                            <tr>
                                <td>Trade Receivables (Face2Face, Telephone, Translation)</td>
                                <td class="text-right">
                                    <?php 
                                        $total_interp_tele_trans = (float) $assets_interp_tele_trans_sum[0]['total_pending'];
                                        echo number_format($total_interp_tele_trans, 2); 
                                    ?>
                                </td>
                            </tr>
                            <tr>
                                <td>Collective Invoices (Receivables)</td>
                                <td class="text-right">
                                    <?php 
                                        $total_collective_invoices = (float) $assets_collective_invoices_sum[0]['total_pending'];
                                        echo number_format($total_collective_invoices, 2); 
                                    ?>
                                </td>
                            </tr>
                            <tr>
                                <td>Prepayments (Office Rent, Insurance, Loans, etc.)</td>
                                <td class="text-right">
                                    <?php 
                                        $total_remaining_prepayments = (float) $prepayments_total[0]['total_remaining_amount'];
                                        echo number_format($total_remaining_prepayments, 2); 
                                    ?>
                                </td>
                            </tr>
                            <tr>
                                <td>Other Receivables (Manual Invoices)</td>
                                <td class="text-right">
                                    <?php 
                                        $total_manual_invoices = (float) $assets_manual_invoices_sum[0]['total_pending'];
                                        echo number_format($total_manual_invoices, 2); 
                                    ?>
                                </td>
                            </tr>
                            <tr class="info">
                                <th>Total Current Assets</th>
                                <th class="text-right">
                                    £ <?php 
                                        $total_current_assets = $total_cash_at_bank 
                                                                    + $total_cash_in_hand 
                                                                    + $total_interp_tele_trans 
                                                                    + $total_collective_invoices
                                                                    + $total_manual_invoices
                                                                    + $total_remaining_prepayments;
                                        echo number_format($total_current_assets, 2);
                                    ?>
                                </th>
                            </tr>
                        </tbody>

                    </table>

                    <!-- Non-Current Assets -->
                    <h4>Non-Current Assets (&gt; 1 year)</h4>
                    <table class="table table-bordered table-condensed">
                        <tbody>
                        <tr><td>Office Equipment</td><td class="text-right">__________</td></tr>
                        <tr><td>Software Licenses (Intangible)</td><td class="text-right">__________</td></tr>
                        <tr><td>Other Long-term Assets</td><td class="text-right">__________</td></tr>
                        <tr class="info">
                            <th>Total Non-Current Assets</th>
                            <th class="text-right">
                                £ <?php echo $total_none_current_assets = 0; ?>
                            </th>
                        </tr>
                        </tbody>
                    </table>

                        <div class="text-right">
                            <strong>➡️ Total Assets = £ 
                                <?php 
                                    $grand_total_current_assets = $total_current_assets + $total_none_current_assets;
                                echo number_format($grand_total_current_assets, 2); ?>
                            </strong>
                        </div>
                    </div>
                </div>


                <?php
                    // ************************************* Expense and Payable Balances *************************************

                    $total_liabilities = $acttObj->full_fetch_array("
                        SELECT 
                            IFNULL(
                                (
                                    -- Sum of Expense pending
                                    SELECT SUM(amoun - amountPaid)
                                    FROM expence
                                    WHERE deleted_flag = 0 
                                    AND status IN ('unpaid','partial')
                                    AND dated BETWEEN '".$start_date."' AND '".$end_date."'
                                ),0
                            ) +
                            IFNULL(
                                (
                                    -- Sum of Prepayment pending
                                    SELECT SUM(total_amount - IFNULL(paid_amount,0))
                                    FROM pre_payments
                                    WHERE deleted_flag = 0 
                                    AND is_payable = 1 
                                    AND status = 'payable'
                                    AND dated BETWEEN '".$start_date."' AND '".$end_date."'
                                ),0
                            ) +
                            IFNULL(
                                (
                                    -- Sum of Interpreter Salaries
                                    SELECT SUM(salry)
                                    FROM interp_salary
                                    WHERE deleted_flag = 0 
                                    AND is_paid = 0
                                    AND dated BETWEEN '".$start_date."' AND '".$end_date."'
                                ),0
                            ) AS total_pending
                    ");

                    $sql_total_vat = $acttObj->full_fetch_array("
                        SELECT 
                            IFNULL(
                                (
                                    -- VAT from Expenses
                                    SELECT SUM(e.vat)
                                    FROM expence e
                                    WHERE e.deleted_flag = 0 
                                    AND e.vat > 0
                                    AND (e.status = 'unpaid' OR e.status = 'partial')
                                    AND DATE(e.dated) BETWEEN '".$start_date."' AND '".$end_date."'
                                ), 0
                            ) +
                            IFNULL(
                                (
                                    -- VAT from Prepayments
                                    SELECT SUM(p.vat)
                                    FROM pre_payments p
                                    WHERE p.is_payable = 1 
                                    AND p.vat > 0
                                    AND DATE(p.dated) BETWEEN '".$start_date."' AND '".$end_date."'
                                ), 0
                            ) AS total_vat
                    ");


                    $sql_total_accruals = $acttObj->full_fetch_array("
                        SELECT 
                            IFNULL(
                                (
                                    -- Outstanding from Expenses
                                    SELECT SUM(e.amoun - COALESCE(e.amountPaid,0))
                                    FROM expence e
                                    WHERE e.deleted_flag = 0
                                    AND (e.status = 'unpaid' OR e.status = 'partial')
                                    AND DATE(e.dated) BETWEEN '$start_date' AND '$end_date'
                                ), 0
                            ) +
                            IFNULL(
                                (
                                    -- Outstanding from Prepayments
                                    SELECT SUM(p.total_amount)
                                    FROM pre_payments p
                                    WHERE p.is_payable = 1
                                    AND p.deleted_flag = 0
                                    AND DATE(p.dated) BETWEEN '$start_date' AND '$end_date'
                                ), 0
                            ) AS total_accruals
                    ");
                ?>

                <!-- Liabilities -->
                <div class="panel panel-danger">
                    <div class="panel-heading">
                    B. Liabilities
                    </div>
                    <div class="panel-body">
                    <!-- Current Liabilities -->
                    <h4>1. Current Liabilities (≤ 1 year)</h4>
                    <table class="table table-bordered table-condensed">
                        <tbody>
                        <tr>
                            <td>Trade Payables (Expense, Interp. Salaries, Prepayments)</td>
                            <td class="text-right">
                                <?php 
                                    $total_liabilities_pending = (float) $total_liabilities[0]['total_pending'];
                                    echo number_format($total_liabilities_pending, 2);
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td>Taxes Payable (VAT, Corporation Tax)</td>
                            <td class="text-right">
                                <?php 
                                    $total_taxes_payable = (float) $sql_total_vat[0]['total_vat'];
                                    echo number_format($total_taxes_payable, 2);
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td>Accruals (Unpaid Salaries, Utilities)</td>
                            <td class="text-right">
                                <?php 
                                    $total_accruals = (float) $sql_total_accruals[0]['total_accruals'];
                                    echo number_format($total_accruals, 2);
                                ?>
                            </td>
                        </tr>
                        <tr class="info">
                            <th>Total Current Liabilities</th>
                            <th class="text-right">
                                £ <?php 
                                    $total_current_liabilities = $total_liabilities_pending 
                                                                        + $total_taxes_payable 
                                                                        + $total_accruals;
                                    echo number_format($total_current_liabilities, 2);
                                ?>
                            </th>
                        </tr>
                        </tbody>
                    </table>

                    <!-- Non-Current Liabilities -->
                    <h4>2. Non-Current Liabilities (&gt; 1 year)</h4>
                    <table class="table table-bordered table-condensed">
                        <tbody>
                            <tr>
                                <td>Long-term Loans or Leases</td>
                                <td class="text-right">__________</td>
                            </tr>
                        <tr class="info">
                            <th>Total Non-Current Liabilities</th>
                            <th class="text-right">
                                £ <?php echo $total_none_current_liabilities = 0; ?>
                            </th>
                        </tr>
                        </tbody>
                    </table>

                        <div class="text-right">
                            <strong>➡️ Total Liabilities = £ 
                                <?php 
                                    $grand_total_liabilities = $total_current_liabilities + $total_none_current_liabilities;
                                    echo number_format($grand_total_liabilities, 2); ?>
                            </strong>
                        </div>
                    </div>
                </div>

                <!-- Equity -->
                <div class="panel panel-success">
                    <div class="panel-heading">
                    C. Equity
                    </div>
                    <div class="panel-body">
                    <table class="table table-bordered table-condensed">
                        <tbody>
                            <tr>
                                <td>Share Capital</td><td class="text-right">__________</td>
                            </tr>
                            <tr>
                                <td>Retained Earnings (Carried Forward)</td>
                                <td class="text-right">
                                    <?php 
                                        $total_retained_earnings = (float) $cash_in_hand[2]['amount'];
                                        echo number_format($total_retained_earnings, 2); 
                                    ?>
                                </td>
                            </tr>
                            <tr>
                                <td>Net Profit / Loss (Current Year)</td><td class="text-right">__________</td>
                            </tr>
                            <tr>
                                <td>Other Reserves</td><td class="text-right">__________</td>
                            </tr>
                            <tr class="info">
                                <th>Total Equity</th>
                                <th class="text-right">
                                    £ <?php 
                                        $grand_total_equity = $total_retained_earnings; 
                                        echo number_format($total_retained_earnings, 2);
                                    ?>    
                                </th>
                            </tr>
                        </tbody>
                    </table>
                    </div>
                </div>

                    <!-- Final Balance -->
                    <div class="alert alert-success text-center">
                        <strong>
                            <!-- Assets = Liabilities + Equity -->
                             <?php 
                                $grand_total_liabilities_equity =  $grand_total_liabilities + $grand_total_equity; ?>
                            Assets (<?php echo number_format($grand_total_current_assets, 2); ?>) 
                            = Liabilities (<?php echo number_format($grand_total_liabilities, 2); ?>) 
                            + Equity (<?php echo number_format($grand_total_equity, 2); ?>) 
                        </strong>

                        <br>

                        <h2>
                            <strong>
                                <?php echo number_format($grand_total_current_assets, 2); ?> 
                                = <?php echo number_format($grand_total_liabilities + $grand_total_equity, 2); ?> 
                            </strong>
                        </h2>

                    </div>

            </div>    
        </div>

    <?php } ?>

  </div>

    <script src="js/jquery-1.11.3.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>

    <link href="https://cdn.datatables.net/2.3.1/css/dataTables.dataTables.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/buttons/3.2.3/css/buttons.dataTables.min.css" rel="stylesheet">


    <style>
        .dt-button {
            padding: 0px 5px !important;
        }

        div.dt-button-collection .active:after {
            position: absolute;
            top: 50%;
            margin-top: -10px;
            right: 1em;
            display: inline-block;
            content: "\2713";
            color: inherit;
        }
        .dataTables_paginate {
            float: right;
            text-align: right;
        }
        .dataTables_info {
            float: left;
            margin: 25px 0;
        }

        /* DT Page Filters, Buttons, Search */
        .dataTables_filter {
            float: right;
            text-align: right;
        }
        .dataTables_length {
            float: left;
        }
        div.dt-buttons {
            position: absolute;
            text-align: center;
        }
    </style>

    <script src="https://cdn.datatables.net/fixedcolumns/5.0.4/js/fixedColumns.dataTables.js"></script>

    <script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.20/js/dataTables.bootstrap.min.js"></script>

    <script src="https://cdn.datatables.net/buttons/1.5.2/js/dataTables.buttons.min.js" type="text/javascript"></script>
    <script src="https://cdn.datatables.net/buttons/1.5.2/js/buttons.colVis.min.js" type="text/javascript"></script>
    <script src="https://cdn.datatables.net/buttons/1.5.6/js/buttons.flash.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.5.6/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.5.6/js/buttons.print.min.js"></script>

    <script>
        $(document).ready(function(){
            $('#assetsTable').DataTable({
                paging: true,
                info: true,
                searching: true,
                ordering: true,
                pageLength: 10,
                dom: 'Bfrtip',
                buttons: [
                    {
                        extend: 'colvis',
                        text: '<i class="fa fa-bars"></i>',
                        titleAttr: 'Show/Hide Columns',
                        className: 'myShowHideActive',
                        columnText: function(dt, idx, title) {
                            return (idx + 1) + ': ' + title;
                        }
                    },
                    {
                        extend: 'copyHtml5',
                        text: '<i class="fa fa-files-o"></i>',
                        titleAttr: 'Copy',
                        exportOptions: {
                            // columns: ':not(:last-child)',
                        }
                    },
                    {
                        extend: 'excelHtml5',
                        text: '<i class="fa fa-file-excel-o"></i>',
                        titleAttr: 'Excel',
                        exportOptions: {
                            // columns: ':not(:last-child)',
                        }
                    },
                    {
                        extend: 'csvHtml5',
                        text: '<i class="fa fa-file-text-o"></i>',
                        titleAttr: 'CSV',
                        exportOptions: {
                            // columns: ':not(:last-child)',
                        }
                    },
                    {
                        extend: 'pdfHtml5',
                        text: '<i class="fa fa-file-pdf-o"></i>',
                        titleAttr: 'PDF',
                        exportOptions: {
                            // columns: ':not(:last-child)',
                        }
                    },
                    {
                        extend: 'print',
                        text: '<i class="fa fa-print"></i>',
                        titleAttr: 'Print',
                        title: function () {
                            return $('.assets_heading').text().trim();
                        },
                        exportOptions: {
                            footer: true
                        },
                        customize: function (win) {
                            // Add dynamic title styling
                            $(win.document.body).find('h1')
                                .css({
                                    'font-size': '18px',
                                    'font-weight': 'bold',
                                    'text-align': 'center',
                                    'margin-bottom': '20px'
                                });

                            // Footer formatting
                            let footerText = $('.assets_heading tfoot th').text();
                            let formattedFooter = "£ " + footerText.replace(/[^\d.-]/g, '');
                            $(win.document.body).find('tfoot th').text(formattedFooter);

                            // Table styling
                            $(win.document.body).find('table').addClass('compact').css('font-size', '12px');
                            $(win.document.body).find('tfoot th').css({
                                'background-color': '#f2f2f2',
                                'font-weight': 'bold',
                                'font-size': '14px',
                                'text-align': 'right'
                            });
                        }
                    },
                    
                ]
            });

            $('#liabilitiesTable').DataTable({
                paging: true,
                searching: true,
                ordering: true,
                pageLength: 10,
                dom: 'Bfrtip',
                buttons: [
                    {
                        extend: 'colvis',
                        text: '<i class="fa fa-bars"></i>',
                        titleAttr: 'Show/Hide Columns',
                        className: 'myShowHideActive',
                        columnText: function(dt, idx, title) {
                            return (idx + 1) + ': ' + title;
                        }
                    },
                    {
                        extend: 'copyHtml5',
                        text: '<i class="fa fa-files-o"></i>',
                        titleAttr: 'Copy',
                        exportOptions: {
                            // columns: ':not(:last-child)',
                        }
                    },
                    {
                        extend: 'excelHtml5',
                        text: '<i class="fa fa-file-excel-o"></i>',
                        titleAttr: 'Excel',
                        exportOptions: {
                            // columns: ':not(:last-child)',
                        }
                    },
                    {
                        extend: 'csvHtml5',
                        text: '<i class="fa fa-file-text-o"></i>',
                        titleAttr: 'CSV',
                        exportOptions: {
                            // columns: ':not(:last-child)',
                        }
                    },
                    {
                        extend: 'pdfHtml5',
                        text: '<i class="fa fa-file-pdf-o"></i>',
                        titleAttr: 'PDF',
                        exportOptions: {
                            // columns: ':not(:last-child)',
                        }
                    },
                    {
                        extend: 'print',
                        text: '<i class="fa fa-print"></i>',
                        titleAttr: 'Print',
                        title: function () {
                            return $('.liabilities_heading').text().trim();
                        },
                        exportOptions: {
                            footer: true
                        },
                        customize: function (win) {
                            // Add dynamic title styling
                            $(win.document.body).find('h1')
                                .css({
                                    'font-size': '18px',
                                    'font-weight': 'bold',
                                    'text-align': 'center',
                                    'margin-bottom': '20px'
                                });

                            // Footer formatting
                            let footerText = $('.liabilities_heading tfoot th').text();
                            let formattedFooter = "£ " + footerText.replace(/[^\d.-]/g, '');
                            $(win.document.body).find('tfoot th').text(formattedFooter);

                            // Table styling
                            $(win.document.body).find('table').addClass('compact').css('font-size', '12px');
                            $(win.document.body).find('tfoot th').css({
                                'background-color': '#f2f2f2',
                                'font-weight': 'bold',
                                'font-size': '14px',
                                'text-align': 'right'
                            });
                        }
                    },
                    
                ]
            });

            $('#prepaymentsTable').DataTable({
                paging: true,
                searching: true,
                ordering: true,
                pageLength: 10,
                dom: 'Bfrtip',
                buttons: [
                    {
                        extend: 'colvis',
                        text: '<i class="fa fa-bars"></i>',
                        titleAttr: 'Show/Hide Columns',
                        className: 'myShowHideActive',
                        columnText: function(dt, idx, title) {
                            return (idx + 1) + ': ' + title;
                        }
                    },
                    {
                        extend: 'copyHtml5',
                        text: '<i class="fa fa-files-o"></i>',
                        titleAttr: 'Copy',
                        exportOptions: {
                            // columns: ':not(:last-child)',
                        }
                    },
                    {
                        extend: 'excelHtml5',
                        text: '<i class="fa fa-file-excel-o"></i>',
                        titleAttr: 'Excel',
                        exportOptions: {
                            // columns: ':not(:last-child)',
                        }
                    },
                    {
                        extend: 'csvHtml5',
                        text: '<i class="fa fa-file-text-o"></i>',
                        titleAttr: 'CSV',
                        exportOptions: {
                            // columns: ':not(:last-child)',
                        }
                    },
                    {
                        extend: 'pdfHtml5',
                        text: '<i class="fa fa-file-pdf-o"></i>',
                        titleAttr: 'PDF',
                        exportOptions: {
                            // columns: ':not(:last-child)',
                        }
                    },
                    {
                        extend: 'print',
                        text: '<i class="fa fa-print"></i>',
                        titleAttr: 'Print',
                        title: function () {
                            return $('.prepayments_heading').text().trim();
                        },
                        exportOptions: {
                            footer: true
                        },
                        customize: function (win) {
                            // Add dynamic title styling
                            $(win.document.body).find('h1')
                                .css({
                                    'font-size': '18px',
                                    'font-weight': 'bold',
                                    'text-align': 'center',
                                    'margin-bottom': '20px'
                                });

                            // Footer formatting
                            let footerText = $('.prepayments_heading tfoot th').text();
                            let formattedFooter = "£ " + footerText.replace(/[^\d.-]/g, '');
                            $(win.document.body).find('tfoot th').text(formattedFooter);

                            // Table styling
                            $(win.document.body).find('table').addClass('compact').css('font-size', '12px');
                            $(win.document.body).find('tfoot th').css({
                                'background-color': '#f2f2f2',
                                'font-weight': 'bold',
                                'font-size': '14px',
                                'text-align': 'right'
                            });
                        }
                    },
                    
                ]
            });

        });
    </script>

</body>

</html>