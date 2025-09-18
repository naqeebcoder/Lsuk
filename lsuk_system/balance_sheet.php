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

  <div class="container-fluid m-b-100">
    
    <div class="row">

        <div class="page-header text-center m-t-0">
            <h1 class="m-t-0">Balance Sheet</h1>
        </div>

        <div class="col-md-8 col-md-offset-2">
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
            
        <?php 
        
            // Assets
            $assets_mult = $acttObj->full_fetch_array("
                SELECT 'Collective Invoice' as source, m_inv AS invoice_no, mult_amount, rAmount, (mult_amount - rAmount) AS pending_amount, dated
                FROM mult_inv
                WHERE is_deleted = 0 AND commit = 1
                AND (status = '' OR status = 'pending' OR status = 'Partially Received')
                AND mult_amount > 0
                AND dated BETWEEN '".$start_date."' AND '".$end_date."'");

            $assets_income = $acttObj->full_fetch_array("
                SELECT 'Manual invoice' as source, voucher_no AS invoice_no, total_amount, 
                (total_amount - IFNULL(received_amount,0)) AS pending_amount, 
                created_at AS dated
                FROM income_invoices
                WHERE deleted_flag = 0 AND commit = 1
                AND payment_status IN ('unpaid','partial')
                AND created_at BETWEEN '".$start_date."' AND '".$end_date."'"
            );

            $assets_receivables = $acttObj->full_fetch_array("
                SELECT source, invoice_no, dated, pending_amount FROM (
                    -- Interpreter Receivables
                    SELECT 
                        'Interpreter' AS source,
                        interpreter.invoiceNo AS invoice_no,
                        interpreter.assignDate AS dated,
                        ROUND(
                            ((interpreter.total_charges_comp + (interpreter.total_charges_comp * interpreter.cur_vat)) + interpreter.C_otherexpns), 
                            2
                        ) - ROUND(interpreter.rAmount, 2) AS pending_amount
                    FROM interpreter
                    WHERE interpreter.deleted_flag = 0
                    AND interpreter.disposed_of = 0
                    AND interpreter.multInv_flag = 0
                    AND interpreter.order_cancel_flag = 0
                    AND interpreter.commit = 1
                    AND (
                        ROUND(interpreter.rAmount, 2) < ROUND(
                            ((interpreter.total_charges_comp + (interpreter.total_charges_comp * interpreter.cur_vat)) + interpreter.C_otherexpns), 
                            2
                        )
                        OR interpreter.total_charges_comp = 0
                    )
                    AND interpreter.assignDate BETWEEN '".$start_date."' AND '".$end_date."'

                    UNION ALL

                    -- Telephone Receivables
                    SELECT 
                        'Telephone' AS source,
                        telephone.invoiceNo AS invoice_no,
                        telephone.assignDate AS dated,
                        ROUND(
                            ((telephone.total_charges_comp + (telephone.total_charges_comp * telephone.cur_vat))), 
                            2
                        ) - ROUND(telephone.rAmount, 2) AS pending_amount
                    FROM telephone
                    WHERE telephone.deleted_flag = 0
                    AND telephone.disposed_of = 0
                    AND telephone.order_cancel_flag = 0
                    AND telephone.commit = 1
                    AND telephone.multInv_flag = 0
                    AND (
                        ROUND(telephone.rAmount, 2) < ROUND(
                            ((telephone.total_charges_comp + (telephone.total_charges_comp * telephone.cur_vat))), 
                            2
                        )
                        OR telephone.total_charges_comp = 0
                    )
                    AND telephone.assignDate BETWEEN '".$start_date."' AND '".$end_date."'

                    UNION ALL

                    -- Translation Receivables
                    SELECT 
                        'Translation' AS source,
                        translation.invoiceNo AS invoice_no,
                        translation.asignDate AS dated,
                        ROUND(
                            ((translation.total_charges_comp + (translation.total_charges_comp * translation.cur_vat))), 
                            2
                        ) - ROUND(translation.rAmount, 2) AS pending_amount
                    FROM translation
                    WHERE translation.deleted_flag = 0
                    AND translation.disposed_of = 0
                    AND translation.order_cancel_flag = 0
                    AND translation.commit = 1
                    AND translation.multInv_flag = 0
                    AND (
                        ROUND(translation.rAmount, 2) < ROUND(
                            ((translation.total_charges_comp + (translation.total_charges_comp * translation.cur_vat))), 
                            2
                        )
                        OR translation.total_charges_comp = 0
                    )
                    AND translation.asignDate BETWEEN '".$start_date."' AND '".$end_date."'
                ) AS receivables
                ORDER BY dated ASC
            ");

            // --- Advance & Deduction (Loans Receivable) ---
            // $assets_loans = $acttObj->full_fetch_array("
            //     SELECT 'Advance & Deductions' as source, lr.id AS loan_id,
            //         lr.id AS invoice_no,
            //         lr.given_amount,
            //         (lr.given_amount - IFNULL(SUM(rp.paid_amount), 0)) AS pending_amount,
            //         lr.created_date AS dated
            //     FROM loan_requests lr
            //     LEFT JOIN request_paybacks rp 
            //         ON rp.request_id = lr.id AND rp.deleted_flag = 0
            //     WHERE lr.status = 2
            //     AND (lr.given_amount - IFNULL((SELECT SUM(paid_amount) FROM request_paybacks WHERE request_id = lr.id AND deleted_flag = 0),0)) > 0
            //     AND DATE(lr.created_date) BETWEEN '".$start_date."' AND '".$end_date."'
            //     GROUP BY lr.id
            // ");

            // Liabilities
            $liabilities_expense = $acttObj->full_fetch_array("
                SELECT 'Expense' as source, invoice_no, amoun, (amoun - amountPaid) AS pending_amount, dated
                FROM expence
                WHERE deleted_flag = 0 AND status IN ('unpaid','partial')
                AND dated BETWEEN '".$start_date."' AND '".$end_date."'"
            );

            $liabilities_pre = $acttObj->full_fetch_array("
                SELECT 'Prepayment' as source, invoice_no, total_amount, (total_amount - IFNULL(paid_amount,0)) AS pending_amount, dated
                FROM pre_payments
                WHERE deleted_flag = 0 AND is_payable = 1 AND status = 'payable'
                AND dated BETWEEN '".$start_date."' AND '".$end_date."'"
            );

            $liabilities_salary = $acttObj->full_fetch_array("
                SELECT 'Interp. Salary' as source, invoice AS invoice_no, salry as total_amount, salry AS pending_amount, dated
                FROM interp_salary
                WHERE deleted_flag = 0 AND is_paid = 0
                AND dated BETWEEN '".$start_date."' AND '".$end_date."'"
            );

            $cash_in_hand = $acttObj->full_fetch_array("
                SELECT 'Bank' AS source,
                    ROUND(
                        IFNULL((SELECT SUM(debit - credit) FROM account_journal_ledger WHERE is_bank = 1 AND (is_receivable = 0 OR is_receivable = 1 OR is_receivable = 2) AND DATE(dated) < '".$start_date."'), 0)  +
                        IFNULL((SELECT SUM(debit - credit) FROM account_journal_ledger WHERE is_bank = 1 AND (is_receivable = 0 OR is_receivable = 1 OR is_receivable = 2) AND DATE(dated) BETWEEN '".$start_date."' AND '".$end_date."'), 0)
                    , 2) AS amount

                UNION ALL

                SELECT 'Cash' AS source,
                    ROUND(
                        IFNULL((SELECT SUM(debit - credit) FROM account_journal_ledger WHERE is_bank = 0 AND DATE(dated) < '".$start_date."'), 0) +
                        IFNULL((SELECT SUM(debit - credit) FROM account_journal_ledger WHERE is_bank = 0 AND DATE(dated) BETWEEN '".$start_date."' AND '".$end_date."'), 0)
                    , 2) AS amount

                UNION ALL

                SELECT 'Retained Earnings' AS source,
                    ROUND(
                        IFNULL((SELECT SUM(credit - debit) FROM account_journal_ledger WHERE is_receivable = 2 AND DATE(dated) < '".$start_date."'), 0) +
                        IFNULL((SELECT SUM(credit - debit) FROM account_journal_ledger WHERE is_receivable = 2 AND DATE(dated) BETWEEN '".$start_date."' AND '".$end_date."'), 0)
                    , 2) AS amount
            ");

            $prepayments = $acttObj->full_fetch_array("
                SELECT 
                    p.invoice_no, p.paid_amount AS prepayment_amount, COALESCE(SUM(e.amountPaid), 0) AS used_amount,
                    (p.paid_amount - COALESCE(SUM(e.amountPaid), 0)) AS remaining_amount,
                    SUM(p.paid_amount - COALESCE(SUM(e.amountPaid), 0)) OVER() AS total_remaining_amount, p.dated
                FROM pre_payments p
                LEFT JOIN expence e ON e.prepayment_id = p.invoice_no AND e.is_prepayment = 1 AND e.is_paid = 1 AND e.deleted_flag = 0 AND e.status IN ('full_paid', 'partial', 'full_partial')
                WHERE p.is_paid = 1 AND p.is_payable = 0 AND p.status = 'paid' AND p.deleted_flag = 0 
                AND p.dated BETWEEN '".$start_date."' AND '".$end_date."'
                GROUP BY p.invoice_no, p.paid_amount, p.dated"
            );

            // debug($prepayments);

            // Totals
            $total_assets = 
                array_sum(array_column($assets_mult, 'pending_amount')) + 
                array_sum(array_column($assets_income, 'pending_amount')) + 
                // array_sum(array_column($assets_loans, 'pending_amount')) +
                array_sum(array_column($assets_receivables, 'pending_amount'));

            $total_liabilities = 
                array_sum(array_column($liabilities_expense, 'pending_amount')) + 
                array_sum(array_column($liabilities_pre, 'pending_amount'))  + 
                array_sum(array_column($liabilities_salary, 'pending_amount'));

            $equity = $total_assets - $total_liabilities;

        ?>

        <div class="row">
            <!-- Assets -->
            <div class="col-lg-6">
                
                <div class="alert alert-info">
                    <h3 class="m-t-0 m-b-0 assets_heading text-uppercase">Receivables</h3>
                </div>

                <table class="table table-bordered table-striped datatable" id="assetsTable">
                    <thead>
                        <tr>
                            <th>Source</th>
                            <th>Invoice/Track#</th>
                            <th>Date</th>
                            <th>Pending Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach(array_merge($assets_mult, $assets_income, $assets_receivables) as $row): ?>
                        <tr>
                            <td><?= $row['source'] ?></td>
                            <td><?= $row['invoice_no'] ?></td>
                            <td><?= $misc->dated($row['dated']) ?></td>
                            <td><?= number_format($row['pending_amount'], 2) ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr class="bg-primary">
                            <th colspan="3" style="text-align: right;">Total Assets: </th>
                            <th><strong>£ <?= $misc->numberFormat_fun($total_assets, 2) ?></strong></th>
                        </tr>
                    </tfoot>
                </table>

            </div>

            <!-- Liabilities -->
            <div class="col-lg-6">
                <div class="alert alert-info">
                    <h3 class="m-t-0 m-b-0 liabilities_heading text-uppercase">Payables</h3>
                </div>
                    <table class="table table-bordered table-striped datatable" id="liabilitiesTable">
                        <thead>
                            <tr>
                                <th>Source</th>
                                <th>Invoice/Track#</th>
                                <th>Date</th>
                                <th>Pending Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach(array_merge($liabilities_expense,$liabilities_pre) as $row): ?>
                            <tr>
                                <td><?= $row['source'] ?></td>
                                <td><?= $row['invoice_no'] ?></td>
                                <td><?= $misc->dated($row['dated']) ?></td>
                                <td><?= number_format($row['pending_amount'], 2) ?></td>
                            </tr>
                        <?php endforeach; ?>
                        <?php foreach($liabilities_salary as $salary): ?>
                            <tr>
                                <td><?= $salary['source'] ?></td>
                                <td><?= $salary['invoice_no'] ?></td>
                                <td><?= $misc->dated($salary['dated']) ?></td>
                                <td><?= number_format($salary['pending_amount'], 2) ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                        <tr class="bg-primary">
                            <th colspan="3" style="text-align: right;">Total Liabilities: </th>
                            <th><strong>£ <?= $misc->numberFormat_fun($total_liabilities, 2) ?></strong></th>
                        </tfoot>
                    </table>
            </div>
        </div>

        <div class="row m-t-50 m-b-50">
            <div class="col-lg-6">
                <div class="alert alert-info">
                    <h3 class="m-t-0 m-b-0 cash_in_hand_heading text-uppercase">Cash in Hand</h3>
                </div>
                    <table class="table table-bordered table-striped datatable" id="liabilitiesTable">
                        <thead>
                            <tr>
                                <th>Source</th>
                                <th>Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php 
                            $total_cash_in_hand = 0;
                            foreach($cash_in_hand as $row): 
                                $total_cash_in_hand += $row['amount'];
                        ?>
                            <tr>
                                <td><?= $row['source'] ?></td>
                                <td><?= number_format($row['amount'], 2) ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                        <tr class="bg-primary">
                            <th style="text-align: right;">Total: </th>
                            <th><strong>£ <?= $misc->numberFormat_fun($total_cash_in_hand, 2) ?></strong></th>
                        </tfoot>
                    </table>
            </div>

            <div class="col-lg-6">
                <div class="alert alert-info">
                    <h3 class="m-t-0 m-b-0 prepayments_heading text-uppercase">Prepayments</h3>
                </div>
                    <table class="table table-bordered table-striped datatable" id="prepaymentsTable">
                        <thead>
                            <tr>
                                <th>Invoice/Track#</th>
                                <th>Date</th>
                                <th>Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php 
                            $total_prepayments = 0;
                            foreach($prepayments as $row): 
                                $total_prepayments += $row['remaining_amount'];
                        ?>
                            <tr>
                                <td><?= $row['invoice_no'] ?></td>
                                <td><?= $misc->dated($row['dated']) ?></td>
                                <td><?= number_format($row['remaining_amount'], 2) ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                        <tr class="bg-primary">
                            <th colspan="2" style="text-align: right;">Total: </th>
                            <th><strong>£ <?= $misc->numberFormat_fun($total_prepayments, 2) ?></strong></th>
                        </tfoot>
                    </table>
            </div>
        </div>

        <div class="row">
            <!-- Equity -->
            <div class="col-md-6 col-md-offset-3">
                <div class="well text-center">
                    <h1 class="m-t-0 m-b-0">
                        Equity: £ <?= number_format($equity, 2) ?>
                    </h1>
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