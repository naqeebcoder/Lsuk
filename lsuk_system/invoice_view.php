<?php
if (session_id() == '' || !isset($_SESSION)) {
    session_start();
}
include 'db.php'; // Your DB connection file
include 'class.php';
$allowed_type_idz = "224";
//Check if user has current action allowed
if ($_SESSION['is_root'] == 0) {
    $get_page_access = $acttObj->read_specific("id", "action_permissions", "user_id=" . $_SESSION['userId'] . " AND action_id IN (" . $allowed_type_idz . ")")['id'];
    if (empty($get_page_access)) {
        die("<center><h2 class='text-center text-danger'>You do not have access to this action!<br>Kindly contact admin for further process.</h2></center>");
    }
}
$id = (int) $_GET['id'];

// Get invoice + company
$invoice_sql = "SELECT i.*, c.company_name, c.contact_person, c.email, c.address, c.payment_terms 
                FROM income_invoices i 
                JOIN income_company c ON i.company_id = c.id 
                WHERE i.id = $id";
$invoice = mysqli_fetch_assoc(mysqli_query($con, $invoice_sql));

// Get items
$items_sql = "SELECT * FROM income_invoice_items WHERE invoice_id = $id";
$items = mysqli_query($con, $items_sql);
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
    <meta charset="UTF-8">
    <title>Invoice #<?= $invoice['id'] ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <style>
        .invoice-header {
            color: #00bcd4;
            font-weight: bold;
        }

        .invoice-summary-box {
            padding: 1rem;
            border-radius: 6px;
        }

        .table th,
        .table td {
            vertical-align: middle;
        }

        .footer-col {
            font-size: 0.85rem;
        }

        .total-section td {
            font-weight: bold;
            text-align: right;
        }

        .amount-due {
            color: #00bcd4;
            font-size: 1.3rem;
            font-weight: bold;
        }

        @media print {
            button {
                display: none;
            }
        }

        #block_container {
            text-align: center;
        }

        #block_container>div {
            display: inline-block;
            vertical-align: middle;
        }
    </style>
</head>

<body class="" id="content">
    <div id="pdfLoading" style="
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0,0,0,0.5);
  color: white;
  display: none;
  justify-content: center;
  align-items: center;
  font-size: 24px;
  z-index: 1000;
  ">
        Generating PDF, please wait...
    </div>
    <div class="container-fluid">
        <header>
            <div id="block_container">

                <div id="bloc1">
                    <h3>Language Services UK Limited</h1>
                </div>
                <div id="bloc2">
                    <img alt="" src="img/logo.png" width="50">
                </div>
                <h5 align="center">Translation | Interpreting | Transcription | Cross-Cultural Training & Development</h5>
                <div class="justify-content-between align-items-center text-center">
                    <h3 class="invoice-header">Invoice</h3>
                </div>
                <hr style="border-top: 1px solid #8c8b8b; width:100%">
            </div>
        </header>
        <div class="mb-4">
            <strong>Language Services UK Limited</strong><br>
            Suite 3, Davis House,<br>
            Lodge Causeway Trading Estate,<br>
            Lodge Causeway, Fishponds,<br>
            Bristol, BS16 3JB<br>
        </div>
        <div class="row mb-4">
            <div class="col-md-8" style="padding-right:40%">
                <strong>To</strong><br>
                <strong><?= $invoice['company_name'] ?></strong><br>
                <?= nl2br($invoice['address']) ?>
            </div>
            <div class="col-md-4 text-end invoice-summary-box">
                <div id="summary-box" style="background-color: #f8f9fa; padding: 15px;">
                    <div>Invoice Number: <strong><?= $invoice['voucher_no'] ?></strong></div>
                    <div>Invoice Date: <?= $misc->dated($invoice['created_at']); ?></div>
                    <div>Client Reference: <?= ($invoice['client_reference']) ? $invoice['client_reference'] : 'N/A'; ?></div>
                    <div>Purchase Order: <?= ($invoice['p_order']) ? $invoice['p_order'] : 'N/A'; ?></div>
                    <div class="fw-bold">DUE DATE: <?= $misc->dated($invoice['due_date']); ?></div>
                </div>
            </div>
        </div>
        <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px; font-family: Arial, sans-serif;">
            <thead>
                <tr style="background-color: #00bcd4; color: white;">
                    <th style="padding: 10px; text-align: left; border: 1px solid #ddd; font-weight: bold;">Description</th>
                    <th style="padding: 10px; text-align: center; border: 1px solid #ddd; font-weight: bold; width: 50px;">Qty</th>
                    <th style="padding: 10px; text-align: center; border: 1px solid #ddd; font-weight: bold; width: 60px;">Unit</th>
                    <th style="padding: 10px; text-align: right; border: 1px solid #ddd; font-weight: bold; width: 80px;">£ Unit Price</th>
                    <th style="padding: 10px; text-align: center; border: 1px solid #ddd; font-weight: bold; width: 60px;">VAT %</th>
                    <th style="padding: 10px; text-align: right; border: 1px solid #ddd; font-weight: bold; width: 80px;">£ VAT</th>
                    <th style="padding: 10px; text-align: right; border: 1px solid #ddd; font-weight: bold; width: 80px;">£ Non-VAT</th>
                    <th style="padding: 10px; text-align: right; border: 1px solid #ddd; font-weight: bold; width: 90px;">£ Total</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($item = mysqli_fetch_assoc($items)): ?>
                    <tr>
                        <td style="padding: 8px 10px; border: 1px solid #ddd; text-align: left;"><?= $item['description'] ?></td>
                        <td style="padding: 8px 10px; border: 1px solid #ddd; text-align: center;"><?= $item['qty'] ?></td>
                        <td style="padding: 8px 10px; border: 1px solid #ddd; text-align: center;"><?= $item['unit'] ?></td>
                        <td style="padding: 8px 10px; border: 1px solid #ddd; text-align: right;"><?= number_format($item['unit_price'], 2) ?></td>
                        <td style="padding: 8px 10px; border: 1px solid #ddd; text-align: center;"><?= $item['vat_percent'] ?>%</td>
                        <td style="padding: 8px 10px; border: 1px solid #ddd; text-align: right;"><?= number_format($item['vat_amount'], 2) ?></td>
                        <td style="padding: 8px 10px; border: 1px solid #ddd; text-align: right;"><?= number_format($item['non_vat'], 2) ?></td>
                        <td style="padding: 8px 10px; border: 1px solid #ddd; text-align: right;"><?= number_format($item['total'], 2) ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <table style="width: 100%; border-collapse: collapse; font-family: Arial, sans-serif; margin-top: 20px;">
            <tr>
                <td colspan="6" style="padding: 10px; text-align: right; font-weight: bold;">Sub Total</td>
                <td width="15%" style="padding: 10px; text-align: right; font-weight: bold;">£ <?= number_format($invoice['subtotal'], 2) ?></td>
            </tr>
            <tr>
                <td colspan="6" style="padding: 10px; text-align: right; font-weight: bold;">Total VAT</td>
                <td style="padding: 10px; text-align: right; font-weight: bold;">£ <?= number_format($invoice['total_vat'], 2) ?></td>
            </tr>
            <tr>
                <td colspan="6" style="padding: 10px; text-align: right; font-weight: bold;">Total Non-VAT</td>
                <td style="padding: 10px; text-align: right; font-weight: bold;">£ <?= number_format($invoice['non_vat'], 2) ?></td>
            </tr>
            <tr>
                <td colspan="6" style="padding: 10px; text-align: right; font-weight: bold; color: #00bcd4; font-size: 1.1em;">Total Amount Due</td>
                <td style="padding: 10px; text-align: right; font-weight: bold; color: #00bcd4; font-size: 1.1em;">£ <?= number_format($invoice['total_amount'], 2) ?></td>
            </tr>
        </table>
        <button id="exportBtn" class="btn btn-primary">Export to PDF</button>
        <p class="mt-4 text-center">
            Please pay your invoice before the due date. Compensation fee and interest charges at 1.5% per day will be
            added to invoice total in accordance with the "Late Payment of Commercial Debts Interests Act 1998" if no payment was made within reasonable time frame.
        </p>

        <hr>

        <div class="row footer-col">
            <div class="col-md-4">
                <strong>Registered Address</strong><br>
                Suite 3, Davis House,<br>
                Lodge Causeway Trading Estate,<br>
                Lodge Causeway, Fishponds,
            </div>
            <div class="col-md-4">
                <strong>Contact Information</strong><br>
                Ayub Sabir<br>
                01172445838<br>
                Email: accounts@lsuk.org
            </div>
            <div class="col-md-4">
                <strong>Payment details</strong><br>
                Bank Name: Barclays PLC<br>
                Sort-Code: 20-13-34<br>
                Account No.: 33161234
            </div>
        </div>
    </div>
    <div class="mt-4" style="
      height: 40px;
      background-color: #00bcd4;
  "></div>
    <!-- Include the libraries -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>

    <script>
        document.getElementById('exportBtn').addEventListener('click', exportToPDF);

        async function exportToPDF() {
            try {
                const btn = document.getElementById('exportBtn');
                btn.style.display = 'none';
                const loading = document.getElementById('pdfLoading');
                //loading.style.display = 'flex';

                // Store original styles and temporarily modify for PDF generation
                const element = document.getElementById('content');
                const originalOverflow = element.style.overflow;
                const originalWidth = element.style.width;

                // Force full width and show all content
                element.style.overflow = 'visible';
                element.style.width = '1240px'; // Wider than A4 to ensure all content fits

                // Add slight delay to allow DOM to update
                await new Promise(resolve => setTimeout(resolve, 100));

                const {
                    jsPDF
                } = window.jspdf;

                const options = {
                    scale: 3,
                    useCORS: true,
                    backgroundColor: '#ffffff',
                    scrollX: 0,
                    scrollY: 0,
                    windowWidth: element.scrollWidth,
                    windowHeight: element.scrollHeight,
                    allowTaint: true,
                    logging: true
                };

                const canvas = await html2canvas(element, options);

                // PDF settings (A4 dimensions in mm)
                const pdfWidth = 210; // A4 width in mm
                const pdfHeight = 297; // A4 height in mm
                const imgWidth = pdfWidth - 20; // Adding margins
                const imgHeight = (canvas.height * imgWidth) / canvas.width;

                const pdf = new jsPDF('p', 'mm', 'a4');

                // Calculate how many pages we need
                let heightLeft = imgHeight;
                let position = 10; // Top margin
                const pageHeight = pdfHeight - 20; // Account for margins

                // Add first page
                pdf.addImage(canvas, 'JPEG', 10, position, imgWidth, imgHeight, undefined, 'FAST');
                heightLeft -= pageHeight;

                // Add additional pages if needed
                while (heightLeft >= 0) {
                    position = heightLeft - imgHeight;
                    pdf.addPage();
                    pdf.addImage(canvas, 'JPEG', 10, position, imgWidth, imgHeight, undefined, 'FAST');
                    heightLeft -= pageHeight;
                }
                const urlParams = new URLSearchParams(window.location.search);
                const invoiceId = urlParams.get('id'); // assuming ?id=123 is in the URL

                pdf.save(`income_invoice#${invoiceId}.pdf`);

            } catch (e) {
                console.error('PDF generation failed', e);
                alert('PDF generation failed. Please try again.');
            } finally {
                // Restore original styles
                const element = document.getElementById('content');
                //element.style.overflow = originalOverflow;
                //element.style.width = originalWidth;

                document.getElementById('exportBtn').style.display = 'block';
                document.getElementById('pdfLoading').style.display = 'none';
            }
        }
    </script>



</body>

</html>