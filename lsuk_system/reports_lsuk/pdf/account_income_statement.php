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

$table = 'account_income';

$counter = 0;

$mak_query = "SELECT * 
FROM `account_income` 
WHERE 1 AND dated BETWEEN '".$search_2."' AND '".$search_3."'
ORDER BY id ASC
LIMIT $limit";

$result = mysqli_query($con, $mak_query);
$mak_results = mysqli_fetch_array($result);

$query="SELECT * 
FROM `account_income` 
WHERE 1 AND dated BETWEEN '".$search_2."' AND '".$search_3."'
ORDER BY id ASC
LIMIT {$startpoint} , {$limit}";

$result = mysqli_query($con, $query);

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
		<br><u>Income / Profit Statement</u>
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