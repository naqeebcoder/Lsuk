<?php

include '../../db.php';
include_once '../../class.php';
include_once '../../function.php';

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

$query="SELECT interpreter.id,interpreter.nameRef,interpreter.orgName,interpreter.orgRef,interpreter.invoiceNo,interpreter.assignDate,interpreter.source,'F2F' as job_type,global_reference_no.shifted_from,global_reference_no.shifted_to FROM interpreter,global_reference_no WHERE interpreter.id=global_reference_no.shifted_from AND interpreter.assignDate between '$search_2' and '$search_3' AND interpreter.is_shifted = 1 
UNION ALL
SELECT telephone.id,telephone.nameRef,telephone.orgName,telephone.orgRef,telephone.invoiceNo,telephone.assignDate,telephone.source,'Telephone' as job_type,global_reference_no.shifted_from,global_reference_no.shifted_to FROM telephone,global_reference_no WHERE telephone.id=global_reference_no.shifted_from AND telephone.assignDate between '$search_2' and '$search_3' AND telephone.is_shifted = 1 
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
<div>
<h2 align="center"><u>Shifted Jobs Report</u></h2>
<p align="right">Report  Date: {$misc->sys_date()}<br />
  Date  Range:  Date From [{$misc->dated($search_2)}] Date To [{$misc->dated($search_3)}]</p>
</div>

EOD;
$tbl .= <<<EOD
<table>
<thead>
<tr>
 	<th style="width:35px;">Sr.No</th>
    <th>Source</th>
	<th>Old Job id</th>
	<th>New Job id</th>
	<th>Old Reference</th>
	<th>New Reference</th>
	<th>Old Job Type</th>
	<th>New Job Type</th>
    <th>Company Name</th>
    <th>Company Reference</th>
    <th>Assign Date</th>
 </tr>

</thead>
EOD;


while ($row = mysqli_fetch_assoc($result)) {
    $new_job_type = $row["job_type"]=='F2F'?'Telephone':'F2F';
    $new_nameRef = $row["job_type"]=='F2F'?$acttObj->read_specific("telephone.nameRef", "telephone", "id=" . $row["shifted_to"]):$acttObj->read_specific("interpreter.nameRef", "interpreter", "id=" . $row["shifted_to"]);

    $tbl .= <<<EOD
        <tr>
<td style="width:35px;">{$i}</td>
<td>{$row["source"]}</td>
<td>{$row["shifted_from"]}</td>
<td>{$row["shifted_to"]}</td>
<td>{$row["nameRef"]}</td>
<td>{$new_nameRef["nameRef"]}</td>
<td>{$row["job_type"]}</td>
<td>{$new_job_type}</td>
<td>{$row["orgName"]}</td>
<td>{$row["orgRef"]}</td>
<td>{$misc->dated($row["assignDate"])}</td>
    </tr>
EOD;

    $i++;
}
$tbl.=<<<EOD
	  
</table>

EOD;
$tbl.=<<<EOD
EOD;
$pdf->writeHTML($tbl, true, false, false, false, '');

//Close and output PDF document
list($a, $b) = explode('.', basename(__FILE__));
$pdf->Output($a . '.pdf', 'I');
//============================================================+
// END OF FILE
//==========================================================EXCEL FORMAT=========================================================+