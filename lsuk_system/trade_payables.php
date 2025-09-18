<?php 
include "userhaspage.php";
SysPermiss::UserHasPage(__FILE__);

if (session_id() == '' || !isset($_SESSION)) {
  session_start();
}
include 'db.php';
include 'class.php';

$date_from = $_GET['date_from'];
$date_to = $_GET['date_to'];

// $allowed_type_idz = "245";
// //Check if user has current action allowed
// if ($_SESSION['is_root'] == 0) {
//   $get_page_access = $acttObj->read_specific("id", "action_permissions", "user_id=" . $_SESSION['userId'] . " AND action_id IN (" . $allowed_type_idz . ")")['id'];
//   if (empty($get_page_access)) {
//     die("<center><h2 class='text-center text-danger'>You do not have access to <u>Edit Company Expense</u> action!<br>Kindly contact admin for further process.</h2></center>");
//   }
// }
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
  <title>Trade Payables</title>
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

        <div class="page-header text-center">
            <h1>Trade Payables Overview</h1>
            <p>This page provides an overview of all trade payables, including unpaid invoices, taxes payable, and accruals.</p>
        </div>

        <section class="col-md-12">
            <div class="well">
                <div class="row">
                    <div class="form-group col-md-2">
                        <label>Date (From)</label>
                        <input type="date" name="date_from" id="date_from" placeholder="" class="form-control" onchange="myFunction()" value="<?php echo $date_from ?>">
                    </div>
                    <div class="form-group col-md-2">
                        <label>Date (To)</label>
                        <input type="date" name="date_to" id="date_to" placeholder="" class="form-control" onchange="myFunction()" value="<?php echo $date_to ?>">
                    </div>
                    <div class="form-group col-md-2 m-t-28">
                        <button type="button" class="btn btn-warning" onclick="window.location.href='trade_payables.php'">
                            Clear Filter
                        </button>
                    </div>
                </div>
            </div>

            <?php if (!empty($_GET['date_from']) && !empty($_GET['date_to'])): ?>
                <div class="alert alert-info p-5">
                    <small>
                        Showing liabilities from 
                        <strong><?= htmlspecialchars($misc->dated($_GET['date_from'])) ?></strong> 
                        to 
                        <strong><?= htmlspecialchars($misc->dated($_GET['date_to'])) ?></strong>
                    </small>
                </div>
            <?php endif; ?>


		</section>

        <?php if (!empty($_GET['date_from']) && !empty($_GET['date_to'])) { ?>

        <?php /* <div class="col-md-12">
            <?php  
                // Trade Payables: unpaid invoices
                // $sql_trade = "
                //     SELECT 
                //         invoice_no,
                //         comp,
                //         amoun AS total_invoice,
                //         COALESCE(amountPaid, 0) AS paid_amount,
                //         (amoun - COALESCE(amountPaid,0)) AS outstanding,
                //         CASE WHEN status = 'unpaid' OR status = 'partial' THEN 'Payable' ELSE 'Paid' END AS status
                //     FROM expence
                //     WHERE (status = 'unpaid' OR status = 'partial') AND deleted_flag = 0
                // ";
                // $result_trade = $con->query($sql_trade);
            ?>

            <h3>Trade Payables</h3>
            <table border="1" cellpadding="6" cellspacing="0" class="table table-bordered table-striped table-condensed table-hover">
                <thead
                <tr>
                    <th>Invoice No</th>
                    <th>Company</th>
                    <th>Total Invoice</th>
                    <th>Paid Amount</th>
                    <th>Outstanding</th>
                    <th>Status</th>
                </tr>
                </thead>
                <tbody>
                <?php 
                // $total_trade = 0;
                // while($row = $result_trade->fetch_assoc()) { 
                //     $total_trade += $row['outstanding'];
                ?>
                <tr>
                    <td><?= $row['invoice_no'] ?></td>
                    <td><?= $row['comp'] ?></td>
                    <td><?= number_format($row['total_invoice'],2) ?></td>
                    <td><?= number_format($row['paid_amount'],2) ?></td>
                    <td><?= number_format($row['outstanding'],2) ?></td>
                    <td><?= $row['status'] ?></td>
                </tr>
                <?php } ?>
                </tbody>  
            </table>

            <div class="page-header m-t-0">
                <p><b>Total Trade Payables (Outstanding):</b> <?= number_format($total_trade,2) ?></p>
            </div>
        </div> ********* */ ?>

        <div class="col-md-6">
            <div class="page-header alert alert-info">
                <h3 class="m-t-0 m-b-0 accrualsTable">Accruals</h3>
            </div>

                <?php
                    // Initialize conditions
                    $conditions_e = $conditions_p = $conditions_m = "";

                    // Check if date range is provided
                    if (!empty($_GET['date_from']) && !empty($_GET['date_to'])) {
                        
                        // Apply conditions for each table alias
                        $conditions_e = " AND DATE(e.dated) BETWEEN '$date_from' AND '$date_to' ";
                        $conditions_p = " AND DATE(p.dated) BETWEEN '$date_from' AND '$date_to' ";
                    }

                    $sql_accruals = "
                        SELECT 
                            e.invoice_no,
                            e.comp,
                            l.title AS expense_type,
                            e.amoun AS total_amount,
                            COALESCE(e.amountPaid,0) AS paid_amount,
                            (e.amoun - COALESCE(e.amountPaid,0)) AS outstanding,
                            CASE WHEN (e.status = 'unpaid' OR e.status = 'partial') THEN 'Payable' ELSE 'Paid' END AS status,
                            'Expense' AS source
                        FROM expence e
                        LEFT JOIN expence_list l ON e.type_id = l.id
                        WHERE (e.status = 'unpaid' OR e.status = 'partial') 
                        AND e.deleted_flag = 0
                        $conditions_e

                        UNION ALL

                        SELECT 
                            p.invoice_no,
                            r.title AS comp,
                            c.title AS expense_type,
                            p.total_amount AS total_amount,
                            0 AS paid_amount,
                            p.total_amount AS outstanding,
                            'Payable' AS status,
                            'Prepayment' AS source
                        FROM pre_payments p
                        LEFT JOIN prepayment_categories c ON p.category_id = c.id
                        LEFT JOIN prepayment_receivers r ON p.receiver_id = r.id
                        WHERE p.is_payable = 1 
                        AND p.deleted_flag = 0
                        $conditions_p
                    ";


                    // echo $sql_accruals;
                    $result_accruals = $con->query($sql_accruals);
                    $total_records = $result_accruals->num_rows;
                ?>

                <table border="1" cellpadding="6" cellspacing="0" class="table table-bordered table-striped table-condensed table-hover" id="accrualsTable">
                    <thead>
                        <tr class="bg-primary">
                            <th>Source</th>
                            <th>Type</th>
                            <th>Company</th>
                            <th>Invoice No/Track#</th>
                            <th>Outstanding</th>
                        </tr>
                    </thead>
                    <tbody>
                <?php 
                if($total_records > 0) {
                    $total_accruals = 0;
                    while($row = $result_accruals->fetch_assoc()) { 
                        $total_accruals += $row['outstanding'];
                    ?>
                    <tr>
                        <td><?= $row['source'] ?></td>
                        <td><?= $row['expense_type'] ?></td>
                        <td><?= $row['comp'] ?></td>
                        <td><?= $row['invoice_no'] ?></td>
                        <td><?= number_format($row['outstanding'],2) ?></td>
                    </tr>
                    <?php } ?>
                <?php } else { ?>
                    <tr>
                    <td colspan="5">No record found.</td>
                </tr>
                <?php }  ?>
                </tbody>
            </table>

            <div class="page-header m-t-20">
                <p><b>Total Accruals:</b> <?= number_format($total_accruals,2) ?></p>
            </div>
        </div>


        <div class="col-md-6">
            <div class="page-header alert alert-info taxTable">
                <h3 class="m-t-0 m-b-0">Taxes Payable (VAT)</h3>
            </div>
            
                <?php
                    // Taxes Payable (VAT)
                    $sql_tax = "
                        SELECT 
                            e.invoice_no,
                            e.comp,
                            e.vat,
                            l.title AS expense_type,
                            CASE WHEN (e.status = 'unpaid' OR e.status = 'partial') THEN 'Payable' ELSE 'Paid' END AS status,
                            'Expense' AS source
                        FROM expence e
                        LEFT JOIN expence_list l ON e.type_id = l.id
                        WHERE (e.status = 'unpaid' OR e.status = 'partial') 
                        AND e.deleted_flag = 0 
                        AND e.vat > 0
                        $conditions_e

                        UNION ALL

                        SELECT 
                            p.invoice_no,
                            r.title AS comp,
                            p.vat,
                            c.title AS expense_type,
                            'Payable' AS status,
                            'Prepayment' AS source
                        FROM pre_payments p
                        LEFT JOIN prepayment_categories c ON p.category_id = c.id
                        LEFT JOIN prepayment_receivers r ON p.receiver_id = r.id
                        WHERE p.is_payable = 1 
                        AND p.vat > 0
                        $conditions_p
                    ";
                
                    $result_tax = $con->query($sql_tax);
                    $total_tax_records = $result_tax->num_rows;
                ?>

                <table border="1" cellpadding="6" cellspacing="0" class="table table-bordered table-striped table-condensed table-hover" id="taxTable">
                    <thead>
                        <tr class="bg-primary">
                        <th>Source</th>
                        <th>Type</th>
                        <th>Company</th>
                        <th>Invoice No/Track#</th>
                        <th>Outstanding (VAT)</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php 
                    if($total_tax_records > 0) {
                        $total_vat = 0;
                        while($row = $result_tax->fetch_assoc()) { 
                            $total_vat += $row['vat'];
                        ?>
                        <tr>
                            <td><?= $row['source'] ?></td>
                            <td><?= $row['expense_type'] ?></td>
                            <td><?= $row['comp'] ?></td>
                            <td><?= $row['invoice_no'] ?></td>
                            <td><?= number_format($row['vat'],2) ?></td>
                        </tr>
                        <?php } ?>
                    <?php } else { ?>
                        <tr>
                        <td colspan="5">No record found.</td>
                    </tr>
                    <?php }  ?>
                    </tbody>
                </table>

                <div class="page-header m-t-20">
                    <p><b>Total Taxes Payable (VAT):</b> <?= number_format($total_vat,2) ?></p>
                </div>               
            
        </div>

        <div class="col-md-6 col-md-offset-3">
            <h2 class="text-center">Liabilities Summary</h2>
            <table border="1" cellpadding="6" cellspacing="0" class="table table-bordered table-striped table-condensed table-hover">
                <thead>
                    <tr class="bg-primary">
                        <th width="50%">Category</th>
                        <th>Total Outstanding</th>
                    </tr>
                </thead>
                <!-- <tr>
                    <td>Trade Payables</td>
                    <td><?= number_format($total_trade,2) ?></td>
                </tr> -->
                <tr>
                    <td>Accruals</td>
                    <td>£ <?= number_format($total_accruals,2) ?></td>
                </tr>
                <tr>
                    <td>Taxes Payable (VAT)</td>
                    <td>£ <?= number_format($total_vat,2) ?></td>
                </tr>
                <tr>
                    <th>Grand Total Liabilities</th>
                    <!-- <th><?= number_format($total_trade + $total_vat + $total_accruals,2) ?></th> -->
                    <th>£ <?= number_format($total_vat + $total_accruals,2) ?></th>
                </tr>
            </table>
        </div>

        <?php } ?>

    </div>
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
            width: 50%;
            float: right;
            text-align: right;
        }
        .dataTables_info {
            width: 50%;
            float: left;
            padding: 15px 0;
        }

        /* DT Page Filters, Buttons, Search */
        .dataTables_filter {
            float: right;
            width: 33.33%;
            text-align: right;
        }
        .dataTables_length {
            width: 33.33%;
            float: left;
        }
        div.dt-buttons {
            position: absolute;
            width: 33.33%;
            left: 33.33%;
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
        $(document).ready(function() {
            $('#accrualsTable').DataTable({
                paging: true,
                info: false,
                pageLength: 10,
                lengthMenu: [
                    [10, 25, 50, 100, -1],
                    [10, 25, 50, 100, "All"]
                ],
                order: [
                    // [0, 'asc']
                ],
                dom: 'Blfrtip',
                layout: {
                    topStart: {
                        buttons: ['colvis']
                    }
                },
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
                            return $('.accrualsTable').text().trim();
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
                            let footerText = $('.accrualsTable tfoot th').text();
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
                    }
                ]
            });


            $('#taxTable').DataTable({
                paging: true,
                info: false,
                pageLength: 10,
                lengthMenu: [
                    [10, 25, 50, 100, -1],
                    [10, 25, 50, 100, "All"]
                ],
                dom: 'Blfrtip',
                buttons: [
                    {
                        extend: 'colvis',
                        text: '<i class="fa fa-bars"></i>',
                        titleAttr: 'Show/Hide Columns',
                        className: 'myShowHideActive',
                        columnText: function (dt, idx, title) {
                            return (idx + 1) + ': ' + title;
                        }
                    },
                    {
                        extend: 'copyHtml5',
                        text: '<i class="fa fa-files-o"></i>',
                        titleAttr: 'Copy',
                        title: function () {
                            return $('.taxTable').text().trim();
                        },
                        exportOptions: {
                            // columns: ':not(:last-child)',
                        }
                    },
                    {
                        extend: 'print',
                        text: '<i class="fa fa-print"></i>',
                        titleAttr: 'Print',
                        title: function () {
                            return $('.taxTable').text().trim();
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
                            let footerText = $('#taxTable tfoot th').text();
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
                    {
                        extend: 'excelHtml5',
                        text: '<i class="fa fa-file-excel-o"></i>',
                        titleAttr: 'Excel',
                        title: function () {
                            return $('.taxTable').text().trim();
                        },
                        exportOptions: {
                            footer: true,
                            format: {
                                footer: function (data) {
                                    return '£ ' + data.replace(/[^\d.-]/g, '');
                                }
                            }
                        }
                    },
                    {
                        extend: 'csvHtml5',
                        text: '<i class="fa fa-file-text-o"></i>',
                        titleAttr: 'CSV',
                        title: function () {
                            return $('.taxTable').text().trim();
                        },
                        exportOptions: {
                            footer: true,
                            format: {
                                footer: function (data) {
                                    return '£ ' + data.replace(/[^\d.-]/g, '');
                                }
                            }
                        }
                    },
                    {
                        extend: 'pdfHtml5',
                        text: '<i class="fa fa-file-pdf-o"></i>',
                        titleAttr: 'PDF',
                        title: function () {
                            return $('.taxTable').text().trim();
                        },
                        exportOptions: {
                            footer: true,
                            format: {
                                footer: function (data) {
                                    return '£ ' + data.replace(/[^\d.-]/g, '');
                                }
                            }
                        }
                    }
                ]
            });



        });
    </script>

</body>

</html>