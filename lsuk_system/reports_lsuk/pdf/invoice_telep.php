<?php
include '../../db.php';include_once ('../../class.php'); $excel=@$_GET['excel']; $table='telephone';$invoice_id= $_GET['invoice_id'];
$query="SELECT $table.*,invoice.dated, interpreter_reg.name,comp_reg.name as orgzName,comp_reg.abrv FROM $table
INNER JOIN invoice ON $table.invoiceNo=invoice.invoiceNo
INNER JOIN interpreter_reg ON $table.intrpName=interpreter_reg.id
INNER JOIN comp_reg ON $table.orgName=comp_reg.abrv

 where multInv_flag=0 and $table.id=$invoice_id";		
$result = mysqli_query($con,$query);
while($row = mysqli_fetch_array($result)){$assignDate=$row['assignDate'];$source=$row['source'];$orgzName=$row['orgzName'];$assignCity=$row['assignCity'];$street=$row['street'];$inchCity=$row['inchCity'];$intrpName=$row['name'];$buildingName=$row['buildingName'];$inchRoad=$row['inchRoad'];$invoiceNo=$row['invoiceNo'];$line1=$row['line1'];$line2=$row['line2'];$inchNo=$row['inchNo'];$nameRef=$row['nameRef'];$inchEmail=$row['inchEmail'];$inchRoad=$row['inchRoad'];$hoursWorkd=$row['C_hoursWorkd'];$calCharges=$row['calCharges'];$C_otherCharges=$row['C_otherCharges'];$chargInterp=$row['C_chargInterp'];$rateHour=$row['C_rateHour'];$dueDate=$row['dueDate'];$dated=date_format(date_create($row['dated']), 'd-m-Y');$line1=$row['line1'];$inchNo=$row['inchNo'];$inchPcode=$row['inchPcode'];$bookinType=$row['bookinType'];$orgRef=$row['orgRef'];$C_admnchargs=$row['C_admnchargs'];$porder=$row['porder'];$C_comments=$row['C_comments'];$orgContact=$row['orgContact'];$commit=$row['commit']; $invoic_date=@$row['invoic_date'];$abrv=@$row['abrv'];}

$total1=$rateHour * $hoursWorkd;$sub_total=$calCharges + $C_otherCharges + ($rateHour * $hoursWorkd)+$C_admnchargs;$vat=$sub_total * .2;$grand=$sub_total + $vat;

require_once('tcpdf_include.php');
$i='Sabih Khan Afridi ';
// create new PDF document
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetSubject('TCPDF Tutorial');

// set default header data
$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, 'Language Services UK Limited'.' Translation and Interpreting Service', 'Suite 3 Davis House Lodge Causeway Trading Estate Lodge Causeway - Fishponds Bristol BS163JB, www.lsuk.org');

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

  <table width="100%" class="second">
    <tr>
      <td class="second"><strong>Job Address:</strong> {$inchNo}<br/><span style="color:#FFF;"><strong>.......................</strong></span> {$line1} {$line2} {$inchRoad} {$inchCity}</td>
      <td align="right" class="second">Date: {$misc->dated(@$invoic_date)}</td>
    </tr>
  </table>
<br/><br/><br/>
<table width="100%" cellpadding="2" class="second">
  <tr>
    <td class="second"><strong>Invoice No.</strong></td>
    <td class="second">{$invoiceNo}</td>
    <td class="second"><strong>Assignment Date</strong></td>
    <td class="second">{$misc->dated($assignDate)}</td>
  </tr>
  <tr>
    <td  class="second"><strong>Job</strong></td>
    <td  class="second">Interpreting</td>
    <td  class="second"><strong>Job Type</strong></td>
    <td  class="second">Face to Face</td>
  </tr>
  <tr>
    <td  class="second"><strong>Invoice Due Date</strong></td>
    <td  class="second">{$misc->dated(date("Y-m-d", strtotime("+15 days")))}</td> 
    <td  class="second"><strong>Booking Ref / Name</strong></td>
    <td  class="second">{$nameRef}</td>
  </tr>
  <tr>
    <td  class="second"><strong>Purchase Order No.</strong></td>
    <td  class="second">{$porder}</td> 
    <td  class="second"><strong>Linguist</strong></td>
    <td  class="second">{$intrpName}</td>
  </tr>
  <tr>
    <td  class="second"><strong>Language</strong></td>
    <td  class="second">{$source}</td> 
    <td  class="second"><strong>Case Worker Name</strong></td>
    <td  class="second">{$orgContact}</td>
  </tr>
  <tr>
    <td  class="second"><strong>File Ref (Client Ref)</strong></td>
    <td  class="second">{$orgRef}</td>  
    <td  class="second"><strong>Booking Type</strong></td>
    <td  class="second">{$bookinType}</td>
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
    <td align="center" class="first">{$hoursWorkd}</td>
    <td align="center" class="first">{$rateHour}</td>
    <td align="center" class="first">{$total1}</td>
  </tr>
  <tr>
    <td align="center" class="first">2</td>
    <td class="first">Other Expanses</td>
    <td align="center" class="first">-</td>
    <td align="center" class="first">-</td>
    <td align="center" class="first">{$C_otherCharges}</td>
  </tr>
  <tr>
    <td align="center" class="first">3</td>
    <td class="first">Call Length Cost</td>
    <td align="center" class="first">-</td>
    <td align="center" class="first">-</td>
    <td align="center" class="first">{$calCharges}</td>
  </tr>
  <tr>
    <td align="center" class="first">7</td>
    <td class="first">Admin Charges</td>
    <td align="center" class="first">-</td>
    <td align="center" class="first">-</td>
    <td align="center" class="first">{$C_admnchargs}</td>
  </tr>
  <tr>
    <td align="center" class="first"><img src="images/bullet-tick.gif" alt="" width="6" height="6" /></td>
    <td align="right" class="first"><strong>Sub Total</strong></td>
    <td align="center" class="first">-</td>
    <td align="center" class="first">-</td>
    <td align="center" class="first"><strong>{$sub_total}</strong></td>
  </tr>
  <tr>
    <td align="center" class="first"><img src="images/bullet-tick.gif" alt="" width="6" height="6" /></td>
    <td align="right" class="first">Vat @20%</td>
    <td align="center" class="first">-</td>
    <td align="center" class="first">-</td>
    <td align="center" class="first">{$vat}</td>
  </tr>
  <tr>
    <td align="center" class="first"><img src="images/bullet-tick.gif" width="6" height="6" /></td>
    <td align="right" class="first">Non Vat-able Cost</td>
    <td align="center" class="first">-</td>
    <td align="center" class="first">-</td>
    <td align="center" class="first">{$C_otherCharges}</td>
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
    <td align="center" bgcolor="#D5D5D5"><strong>{$grand}</strong></td>
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
