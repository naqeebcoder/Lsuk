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
$html = <<<EOF
<!-- EXAMPLE OF CSS STYLE -->
<style>
	h1 {
		font-family: times;
		font-size: 16pt;
		text-decoration: underline;
	}

	table.first {
		font-family: helvetica;
		font-size: 8pt;
		border-left: 1px solid #ABABAB;
		border-right: 1px solid #ABABAB;
		border-top: 1px solid #ABABAB;
		border-bottom: 1px solid #ABABAB;
	}
	td.first {border: 1px solid #ABABAB;}
	td.second {}
	table..second{}
</style>
<br/>
<h1 class="title" align="center">INVOICE</h1>

  <table width="628" class="second">
    <tr>
      <td class="second">Invoice No.: LSUK00278AIS 2000 (Bilingo)</td>
      <td align="right" class="second">Date: 01-10-2015</td>
    </tr>
  </table>
<br/><br/><br/>
<table width="440" height="78" cellpadding="2" class="second">
  <tr>
    <td width="126"  class="second"><strong>Assignment Date</strong></td>
    <td width="208"  class="second">01-10-2015</td>
  </tr>
  <tr>
    <td  class="second"><strong>Job</strong></td>
    <td  class="second">Interpreting</td>
  </tr>
  <tr>
    <td  class="second"><strong>Job Type</strong></td>
    <td  class="second">Face to Face</td>
  </tr>
  <tr>
    <td  class="second"><strong>Invoice Due Date</strong></td>
    <td  class="second">07-03-2017</td>
  </tr>
  <tr>
    <td  class="second"><strong>Booking Ref / Name</strong></td>
    <td  class="second">LSUK/Jan/1</td>
  </tr>
  <tr>
    <td  class="second"><strong>Purchase Order No.</strong></td>
    <td  class="second">XX124X</td>
  </tr>
</table>
<br/><br/>
<table width="664" height="282" cellpadding="2" class="first">
  <tr>
  <td width="43" align="center" bgcolor="#D5D5D5"><b>No.</b></td>
  <td width="369" bgcolor="#D5D5D5"><b>Description</b></td>
  <td width="72" align="center" bgcolor="#D5D5D5"> <b>Unit</b></td>
  <td width="77" align="center" bgcolor="#D5D5D5"><b>Unit Cost (&pound;)</b></td>
  <td width="69" align="center" bgcolor="#D5D5D5"><b>Total (&pound;)</b></td>
 </tr>
  <tr>
    <td align="center" class="first">1</td>
    <td class="first">Time for Interpreting</td>
    <td align="center" class="first">4</td>
    <td align="center" class="first">32</td>
    <td align="center" class="first">128</td>
  </tr>
  <tr>
    <td align="center" class="first">2</td>
    <td class="first">Other Expanses</td>
    <td align="center" class="first">-</td>
    <td align="center" class="first">-</td>
    <td align="center" class="first">0</td>
  </tr>
  <tr>
    <td align="center" class="first">3</td>
    <td class="first">Travel Time if Applicable</td>
    <td align="center" class="first">6</td>
    <td align="center" class="first">-</td>
    <td align="center" class="first">0</td>
  </tr>
  <tr>
    <td align="center" class="first">4</td>
    <td class="first">Travel Cost</td>
    <td align="center" class="first">-</td>
    <td align="center" class="first">-</td>
    <td align="center" class="first">96</td>
  </tr>
  <tr>
    <td align="center" class="first">5</td>
    <td class="first">Milage</td>
    <td align="center" class="first">-</td>
    <td align="center" class="first">-</td>
    <td align="center" class="first">194</td>
  </tr>
  <tr>
    <td align="center" class="first">6</td>
    <td class="first">Milage Cost</td>
    <td align="center" class="first">-</td>
    <td align="center" class="first">-</td>
    <td align="center" class="first">87.3</td>
  </tr>
  <tr>
    <td align="center" class="first">7</td>
    <td class="first">Admin Charges</td>
    <td align="center" class="first">-</td>
    <td align="center" class="first">-</td>
    <td align="center" class="first">0</td>
  </tr>
  <tr>
    <td align="center" class="first"><img src="images/bullet-tick.gif" alt="" width="6" height="6" /></td>
    <td align="right" class="first"><strong>Sub Total</strong></td>
    <td align="center" class="first">-</td>
    <td align="center" class="first">-</td>
    <td align="center" class="first"><strong>311.3</strong></td>
  </tr>
  <tr>
    <td align="center" class="first"><img src="images/bullet-tick.gif" alt="" width="6" height="6" /></td>
    <td align="right" class="first">Vat @20%</td>
    <td align="center" class="first">-</td>
    <td align="center" class="first">-</td>
    <td align="center" class="first">62.26</td>
  </tr>
  <tr>
    <td align="center" class="first"><img src="images/bullet-tick.gif" width="6" height="6" /></td>
    <td align="right" class="first">Non Vat-able Cost</td>
    <td align="center" class="first">-</td>
    <td align="center" class="first">-</td>
    <td align="center" class="first">0</td>
  </tr>
  <tr>
    <td align="center" class="first"><img src="images/bullet-tick.gif" alt="" width="6" height="6" /></td>
    <td align="right" class="first">Discount</td>
    <td align="center" class="first">-</td>
    <td align="center" class="first">-</td>
    <td align="center" class="first">0</td>
  </tr>
  <tr>
    <td align="center" class="first">&nbsp;</td>
    <td align="right" class="first"><strong>Total Invoice Cost</strong></td>
    <td align="center" bgcolor="#D5D5D5"><strong>-</strong></td>
    <td align="center" bgcolor="#D5D5D5"><strong>-</strong></td>
    <td align="center" bgcolor="#D5D5D5"><strong>373.56</strong></td>
  </tr>
</table>
<br/><br/><br/><br/>
<h5 align="justify">
Please make all cheques payable to Language Services UK Limited for BACS payment Sort Code 20-13-34 Account Number 33161234. Company Registration Number 7760366 VAT Number 198427362 Thank You For Business With Us</h5>
<h5>Please pay your invoice within 21 days from the date of invoice.<span style="color:#F00"> Compensation fee and interest charges at 1.5% per day will be added to invoice total in accordance with the "Late Payment of Commercial Debts Interests Act 1998"</span> if no payment was made within reasonable time frame</h5>
EOF;

// output the HTML content
$pdf->writeHTML($html, true, false, true, false, '');



//Close and output PDF document
$pdf->Output('example_061.pdf', 'I');

//============================================================+
// END OF FILE
//============================================================+
