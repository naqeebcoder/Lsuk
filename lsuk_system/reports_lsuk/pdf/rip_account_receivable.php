<?php
include '../../db.php';
include_once '../../class.php';
include_once '../../function.php';

//$excel=@$_GET['excel'];
$excel = SafeVar::GetVar('excel', '');

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
$total_charges_comp = 0;
$C_otherCharges = 0;
$g_total = 0;
$g_vat = 0;
$C_otherCharges = 0;
$non_vat = 0;
$vated_cost = 0;

//...................................................For Multiple Selection...................................\\
$counter = 0;

$mak_query="SELECT id,voucher,dated,company,description,credit,debit,balance,deleted_flag FROM
                    account_receivable where deleted_flag=0 ".(!empty($search_2)?" AND dated BETWEEN '$search_2' AND '$search_3' ":"")." 
                     LIMIT {$mak_limit} As t";

$result = mysqli_query($con, $mak_query);
$mak_results = mysqli_fetch_array($result);

$mak_non_vat = $mak_results['Company_Total_Charges'];
$mak_total_vat = $mak_results['Total_Vat'];
$mak_Other_Expence = $mak_results['Other_Expence'];
$mak_total_invoice = $mak_results['net_total'];
//$mak_total_invoice = $mak_non_vat + $mak_total_vat + $mak_Other_Expence;

$query="SELECT id,voucher,dated,company,description,credit,debit,balance,deleted_flag FROM account_receivable where deleted_flag=0 ".(!empty($search_2)?" AND dated BETWEEN '$search_2' AND '$search_3' ":"")." LIMIT {$startpoint} , {$limit}";
// echo $_words."words<br>";
// echo $query;die();exit;
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
<div>
<h2 align="center"><u>Receivable Account</u></h2>
<p align="right">Report  Date: {$misc->sys_date()}<br />
  Date  Range:  Date From [{$misc->dated($search_2)}] Date To [{$misc->dated($search_3)}]</p>
</div>
<br/><br/>

EOD;
$multiExtColumnHead = ($multi==1?"<th>Invoice Type</th>":""); 
$tbl .= <<<EOD
<table>
<thead>
<tr>
    <th style="width:35px;">Sr.No</th>
    <th>Date</th>
    <th>Voucher</th>
    <th>Company</th>
    <th>Description</th>
    <th>Credit</th>
    <th>Debit</th>
    <th>Balance </th>
 </tr>

</thead>
EOD;

$runcompany = "";
$nowcompany = "";
$mapCoTotals = array();
ZeroCompTotal($mapCoTotals);

$loop = 0;

while ($row = mysqli_fetch_assoc($result)) {

    $voucher = $row['voucher'];
    $assignDate = $row['dated'];
    $company = $row['company'];
    $credit = $row['credit'];
    $debit = $row['debit'];
    $description = $row['description'];
    $balance = $row['balance'];

    $loop++;


    $tbl .= <<<EOD
    <tr>
      <td style="width:35px;">{$i}</td>
        <td>{$assignDate}</td>
        <td>{$voucher}</td>
        <td>{$company}</td>
        <td>{$description}</td>
        <td>{$credit}</td>
        <td>{$debit}</td>
        <td>{$balance}</td>
    </tr>
EOD;

    $i++;
}
// ShowCompTotal($mapCoTotals, $tbl,$multi);

// $mak_non_vat = $mak_results['Company_Total_Charges'];
// $mak_total_vat = $mak_results['Total_Vat'];
// $mak_Other_Expence = $mak_results['Other_Expence'];
// $mak_total_invoice

$multiExtColumnSpace = ($multi==1?"<td></td>":""); 

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

function ZeroCompTotal(&$map)
{
    // $map["non_vat"] = 0;
    // $map["vat"] = 0;
    // $map["vated_cost"] = 0;
    // $map["total"] = 0;
}

function UpdateCompTotal(&$map, &$row)
{
    // $total = $row["total_charges_comp"] + $row["total_charges_comp"] * $row["cur_vat"] + $row["C_otherCost"];

    // $map["non_vat"] += $row["total_charges_comp"];
    // $map["vat"] += $row["vat"];
    // $map["vated_cost"] += $row["C_otherCost"];
    // $map["total"] += $total;
}

function ShowCompTotal(&$map, &$tbl, $multi)
{
//   global $misc;
//   $multiExtColumnSpace = ($multi==1?"<td></td>":""); 
//   $tbl .= <<<EOD
// 	<tr>
// 	<td></td>
// 	<td></td>
// 	<td></td>
// 	<td></td>
// 	<td></td>
// 	<td></td>
// 	<td></td>
// 	<td></td>
// 	<td></td>
//   $multiExtColumnSpace
// 	<td></td>
// 	<td>Comp Total</td>
// 	<td>{$misc->numberFormat_fun($map["non_vat"])}</td>
// 	<td>{$misc->numberFormat_fun($map["vat"])}</td>
// 	<td>{$misc->numberFormat_fun($map["vated_cost"])}</td>
// 	<td>{$misc->numberFormat_fun($map["total"])}</td>
// 	</tr>
// EOD;
}