<?php

include '../../db.php';
include_once '../../class.php';
include_once '../../function.php';

//$excel=@$_GET['excel'];
$excel = SafeVar::GetVar('excel', '');

$search_1 = @$_GET['search_1'];
$search_2 = @$_GET['search_2'];
$search_3 = @$_GET['search_3'];
$mak_page = @$_GET['page'];
$startpoint = @$_GET['startpoint'];
$limit = @$_GET['limit'];

$page = (int) (!isset($mak_page) ? 1 : $mak_page);
$limit = 50;
$maki = ($page * $limit) - $limit;

$mak_limit;
if ($maki == 0) {
    $mak_limit = 50;
} else {
    $mak_limit = $maki + 50;
}

$i = $maki + 1;
$table = 'account_receivable';

$counter = 0;

/*$mak_query = "SELECT * 
FROM `account_receivable` 
WHERE 1 AND dated BETWEEN '".$search_2."' AND '".$search_3."'
ORDER BY id ASC";
//LIMIT $limit";*/

$mak_query = "WITH opening_balance_cte AS (
			SELECT 
				ROUND(IFNULL(SUM(debit), 0) - IFNULL(SUM(credit), 0), 2) AS opening_balance
			FROM 
				account_journal_ledger
			WHERE 
				is_receivable = 1 
				AND is_bank = 1 
				AND DATE(dated) < '".$search_2."'
		),

		transaction_data AS (
			SELECT 
				id,
				dated,
				credit,
				debit,
				posted_by,
				posted_on,
				voucher,
				invoice_no,
				company,
				balance,
				SUM(debit - credit) OVER (
					ORDER BY DATE(dated), id 
					ROWS BETWEEN UNBOUNDED PRECEDING AND CURRENT ROW
				) AS transaction_running_balance
			FROM 
				account_journal_ledger
			WHERE 
				is_receivable = 1 
				AND is_bank = 1 
				AND DATE(dated) BETWEEN '".$search_2."' AND '".$search_3."'
		)

		SELECT 
			t.*,
			ob.opening_balance,
			ROUND(ob.opening_balance + t.transaction_running_balance, 2) AS running_balance
		FROM 
			transaction_data t
		CROSS JOIN 
			opening_balance_cte ob
		ORDER BY 
			DATE(t.dated), t.id
		LIMIT {$startpoint}, {$limit}";

$result = mysqli_query($con, $mak_query);
$mak_results = mysqli_fetch_array($result);
//debug($mak_results);

$query = "WITH opening_balance_cte AS (
    SELECT 
        ROUND(IFNULL(SUM(debit), 0) - IFNULL(SUM(credit), 0), 2) AS opening_balance
    FROM 
        account_receivable
    WHERE 
        DATE(dated) < '".$search_2."'
),

transaction_data AS (
    SELECT 
        *,
        SUM(debit - credit) OVER (ORDER BY DATE(dated), id ROWS BETWEEN UNBOUNDED PRECEDING AND CURRENT ROW) AS transaction_running_balance
    FROM 
        account_receivable
    WHERE 
        DATE(dated) BETWEEN '".$search_2."' AND '".$search_3."'
)

SELECT 
    *,
    ob.opening_balance,
    ROUND(ob.opening_balance + t.transaction_running_balance, 2) AS running_balance
FROM 
    transaction_data t
CROSS JOIN 
    opening_balance_cte ob
ORDER BY 
    DATE(t.dated), t.id"; 
//LIMIT {$startpoint} , {$limit}";

$result = mysqli_query($con, $query);

$opening_balance_query = "SELECT 
    IFNULL(SUM(credit), 0) - IFNULL(SUM(debit), 0) AS opening_balance
FROM 
    account_receivable
WHERE 
    dated < '".$search_2."'";
$opb_result = mysqli_query($con, $opening_balance_query);
$opening_balance = mysqli_fetch_assoc($opb_result);

// Include the main TCPDF library (search for installation path).
require_once 'tcpdf_include.php';
// create new PDF document
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetSubject('TCPDF Tutorial');

// set default header data
include 'rip_header_lndscp.php';
include 'rip_footer.php'; // set header and footer fonts
$pdf->setHeaderFont(array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

// set auto page breaks
$pdf->SetAutoPageBreak(true, PDF_MARGIN_BOTTOM);

// set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// set some language-dependent strings (optional)
if (@file_exists(dirname(__FILE__) . '/lang/eng.php')) {
    require_once dirname(__FILE__) . '/lang/eng.php';
    $pdf->setLanguageArray($l);
}

// ---------------------------------------------------------

// set font
$pdf->SetFont('helvetica', 'B', 12);

// add a page
$pdf->AddPage('L', 'A4');
$pdf->SetFont('helvetica', '', 8);

// Table with rowspans and THEAD
$tbl = <<<EOD
<style>
table {border-collapse: collapse; width:100%;word-wrap: break-word;}
th {border: 1px solid #999; padding: 0.5rem;text-align: left;background-color:#039;
  color:#FFF;font-weight:bold;word-wrap: break-word;}
td {border: 1px solid #999; padding: 0.5rem;text-align: left;word-wrap: break-word;}
</style>
EOD;

$tbl .= <<<EOD
	
	<h2 align="center">
		<br><u>Receivable Statement</u>
	</h2>
		<p align="right">Report Date: {$misc->sys_date()} <br>
		From: {$misc->dated($search_2)} To: {$misc->dated($search_3)}</p>

EOD;

$tbl .= <<<EOD
	<table>
		<thead>
			<tr>
				<th>Voucher</th>
				<th>Invoice No.</th>
				<th>Dated</th>
				<th>Company</th>
				<th>Description</th>
				<th>Credit</th>
				<th>Debit</th>
				<th>Balance</th>
			 </tr>
		</thead>
EOD;

$tbl .= <<<EOD
    <tr>
		<td colspan="7" align="right">Opening Balance</td>
		<td>{$misc->numberFormat_fun($mak_results["opening_balance"])}</td>
	</tr>
EOD;

while ($row = mysqli_fetch_assoc($result)) {

    $tbl .= <<<EOD
    <tr>
		<td>{$row["voucher"]}</td>
		<td>{$row["invoice_no"]}</td>
		<td>{$misc->dated($row['dated'])}</td>
		<td>{$row["company"]}</td>
		<td>{$row["description"]}</td>
		<td>{$row["credit"]}</td>
		<td>{$row["debit"]}</td>
		<td>{$row["balance"]}</td>
	</tr>
EOD;

}

$tbl .= <<<EOD
 
</table>

EOD;

$pdf->writeHTML($tbl, true, false, false, false, '');

// -----------------------------------------------------------------------------

//Close and output PDF document
list($a, $b) = explode('.', basename(__FILE__));
$pdf->Output($a . '.pdf', 'I');
//============================================================+
// END OF FILE
//==========================================================EXCEL FORMAT=========================================================+

?>