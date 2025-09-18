<?php include '../../db.php';include_once ('../../class.php'); $excel=@$_GET['excel'];$search_1=@$_GET['search_1']; $search_2=@$_GET['search_2']; $search_3=@$_GET['search_3'];
$i=1;$table='expence';$amoun=0;
$query="SELECT * FROM $table 
	   where title like '$search_1%' and billDate between '$search_2' and '$search_3'";
$result = mysqli_query($con, $query);

// Include the main TCPDF library (search for installation path).
require_once('tcpdf_include.php');
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
$tbl = <<<EOD
<style>
table {border-collapse: collapse; width:670px;}
th {border: 1px solid #999; padding: 0.5rem;text-align: left;background-color:#039; color:#FFF;font-weight:bold;}
td {border: 1px solid #999; padding: 0.5rem;text-align: left;}
</style>
EOD;

$tbl.=<<<EOD
<div>
<span align="right"> Date: {$misc->sys_date()} </span>
<h2 style="text-decoration:underline; text-align:center">LSUK Expanses Report for {$search_1}</h2>
<p>Expanses Report<br/>Date Range: {$misc->dated($search_2)} to {$misc->dated($search_3)} </p>
</div>
EOD;
$tbl.=<<<EOD
<table>
<thead>
<tr>
 	<th style="width:35px;">Sr.No</th>
    <th>Title</th>
    <th>Amount</th>
    <th>Details</th>
    <th>Voucher No.</th>
    <th>Company</th>
    <th>Bill Date</th>
 </tr>

</thead>
EOD;
while($row = mysqli_fetch_assoc($result)){$amoun=$row["amoun"] + $amoun;
$tbl.=<<<EOD
    <tr>
      	<td style="width:35px;">{$i}</td>
		<td>{$row["title"]}</td>
		<td>{$row["amoun"]}</td>
		<td>{$row["details"]}</td>
		<td>{$row["voucher"]}</td>
		<td>{$row["comp"]}</td>
		<td>{$misc->dated($row["billDate"])}</td>
    </tr>
EOD;
 $i++;}
$tbl.=<<<EOD
	  
<tr>
<td></td>
<td>Total</td>
<td>{$amoun}</td>
<td></td>
<td></td>
<td></td>
<td></td>
</tr>
</table>

EOD;
$tbl.=<<<EOD
EOD;
	



$pdf->writeHTML($tbl, true, false, false, false, '');


// -----------------------------------------------------------------------------

//Close and output PDF document
list($a,$b)=explode('.',basename(__FILE__));
$pdf->Output($a.'.pdf', 'I');
//============================================================+
// END OF FILE
//==========================================================EXCEL FORMAT=========================================================+
