<?php
//============================================================+
// File name   : example_048.php
// Begin       : 2009-03-20
// Last Update : 2013-05-14
//
// Description : Example 048 for TCPDF class
//               HTML tables and table headers
//
// Author: Nicola Asuni
//
// (c) Copyright:
//               Nicola Asuni
//               Tecnick.com LTD
//               www.tecnick.com
//               info@tecnick.com
//============================================================+

/**
 * Creates an example PDF TEST document using TCPDF
 * @package com.tecnick.tcpdf
 * @abstract TCPDF - Example: HTML tables and table headers
 * @author Nicola Asuni
 * @since 2009-03-20
 */

// Include the main TCPDF library (search for installation path).
require_once('tcpdf_include.php');
$i='Sabih Khan Afridi ';
// create new PDF document
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetSubject('TCPDF Tutorial');

// set default header data
$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, 'Language Services UK Limited'.' Translation and Interpreting Service', 'Suite 2 Davis House Lodge Causeway Trading Estate Lodge Causeway - Fishponds Bristol BS163JB, www.lsuk.org');

// set header and footer fonts
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

// set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// set some language-dependent strings (optional)
if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
	require_once(dirname(__FILE__).'/lang/eng.php');
	$pdf->setLanguageArray($l);
}

// ---------------------------------------------------------

// set font
$pdf->SetFont('helvetica', 'B', 12);

// add a page
$pdf->AddPage();
$pdf->SetFont('helvetica', '', 8);

// Table with rowspans and THEAD
$tbl = <<<EOD
<style>
table {border-collapse: collapse; width:670px;}
th {border: 1px solid #999; padding: 0.5rem;text-align: left;}
td {border: 1px solid #999; padding: 0.5rem;text-align: left;}
</style>
<div>
<span align="right"> Date: 19-02-2017 </span>
<h2 style="text-decoration:underline; text-align:center">Account Statement Report for "Albany Solictors - Bristol Office"</h2>
<p>Paid Invoices Face to Face Interpreting<br/>Date Range: 19-02-2017 to 19-02-2017 </p>

</div>
<table>
<thead>
<tr style="background-color:#000; color:#FFF; font-weight:bold;">
<td width="35" align="center" bgcolor="#330099">Sr.#</td>
  <td bgcolor="#330099">Job Date</td>
    <td bgcolor="#330099">Type</td>
    <td bgcolor="#330099">Source</td>
    <td bgcolor="#330099">Client Name</td>
    <td bgcolor="#330099">Units</td>
    <td bgcolor="#330099">Unit Cost</td>
    <td bgcolor="#330099">Job Cost</td>
    <td bgcolor="#330099">Travel Cost</td>      
    <td bgcolor="#330099">Travel Expens</td>        
    <td bgcolor="#330099">Non Vatable</td>            
    <td bgcolor="#330099">Admn Charges</td>  
    <td bgcolor="#330099">Total Cost</td>         
    <td bgcolor="#330099">Job<br>Notes</td>
 </tr>

</thead>

 <tr>
  <td width="35" align="center">1.</td>
  <td>19-02-17</td>
  <td>XXXX</td>
  <td>XXXX</td>
  <td>XXXX</td>
  <td>XXXX</td>
  <td>XXXX</td>
  <td>XXXX</td>
  <td>XXXX</td>
  <td>XXXX</td>
  <td>XXXX</td>
  <td>XXXX</td>
  <td>XXXX</td>
  <td>XXXX</td>
 </tr> <tr>
  <td width="35" align="center">2.</td>
  <td>22-02-17</td>
  <td>XXXX</td>
  <td>XXXX</td>
  <td>XXXX</td>
  <td>XXXX</td>
  <td>XXXX</td>
  <td>XXXX</td>
  <td>XXXX</td>
  <td>XXXX</td>
  <td>XXXX</td>
  <td>XXXX</td>
  <td>XXXX</td>
  <td>XXXX</td>
 </tr>
 <tr>
  <td width="35" align="center">3.</td>
  <td>23-02-17</td>
  <td>XXXX</td>
  <td>XXXX</td>
  <td>XXXX</td>
  <td>XXXX</td>
  <td>XXXX</td>
  <td>XXXX</td>
  <td>XXXX</td>
  <td>XXXX</td>
  <td>XXXX</td>
  <td>XXXX</td>
  <td>XXXX</td>
  <td>XXXX</td>
 </tr> <tr>
  <td width="35" align="center">4.</td>
  <td>24-02-17</td>
  <td>XXXX</td>
  <td>XXXX</td>
  <td>XXXX</td>
  <td>XXXX</td>
  <td>XXXX</td>
  <td>XXXX</td>
  <td>XXXX</td>
  <td>XXXX</td>
  <td>XXXX</td>
  <td>XXXX</td>
  <td>XXXX</td>
  <td>XXXX</td>
 </tr>
</table>
EOD;

$pdf->writeHTML($tbl, true, false, false, false, '');

// -----------------------------------------------------------------------------

//Close and output PDF document
$pdf->Output('example_048.pdf', 'I');

//============================================================+
// END OF FILE
//============================================================+
