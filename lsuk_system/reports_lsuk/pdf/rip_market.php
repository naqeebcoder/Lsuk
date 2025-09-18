<?php include '../../db.php';include_once ('../../class.php'); $excel=@$_GET['excel'];$search_1=@$_GET['search_1'];$search_2=@$_GET['search_2'];$search_3=@$_GET['search_3'];
$i=1;$table='comp_reg';
//...................................................For Multiple Selection...................................\\
 $arr_compType = explode(',', $search_1);$_words_compType = implode("' OR compType like '", $arr_compType);
//......................................\\//\\//\\//\\//........................................................\\
if(!empty($search_1)){
$query="SELECT * FROM $table
	   where dated between '$search_2' and '$search_3' and ($table.compType like '%$_words_compType%')";}
	   else{$query="SELECT * FROM $table
	   where dated between '$search_2' and '$search_3'";}
$result = mysqli_query($con, $query);

// Include the main TCPDF library (search for installation path).
require_once('tcpdf_include.php');
// create new PDF document
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetSubject('TCPDF Tutorial');

// set default header data
include'rip_header.php';
include'rip_footer.php';// set header and footer fonts
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
table {border-collapse: collapse; width:100%;word-wrap: break-word;}
th {border: 1px solid #999; padding: 0.5rem;text-align: left;background-color:#039; color:#FFF;font-weight:bold;word-wrap: break-word;}
td {border: 1px solid #999; padding: 0.5rem;text-align: left;word-wrap: break-word;}
</style>
EOD;

$tbl.=<<<EOD
<div>
<h2 align="center"><u>Client Marketing Report</u></h2>
<p align="right">Report  Date: {$misc->sys_date()}<br />
  Date  Range:  Date From [{$misc->dated($search_2)}] Date To [{$misc->dated($search_3)}]</p>
</div><div aligen='center'>
EOD;
$tbl.=<<<EOD
<table>
<thead>
<tr>
 	<th style="width:35px;">Sr.No</th>
	<th>Client Name</th>
    <th>Company Type </th>
    <th>Contact Name</th>
    <th>Contact No.</th>
    <th>Email address</th>
 </tr>

</thead>
EOD;
while($row = mysqli_fetch_assoc($result)){$contacts=$row["contactNo1"].' , '.$row["contactNo2"].' , '.$row["contactNo3"];
$tbl.=<<<EOD
    <tr>
      	<td style="width:35px;">{$i}</td>
<td>{$row["name"]}</td>
<td>{$row["compType"]}</td>
<td>{$row["contactPerson"]}</td>
<td>{$contacts}</td>
<td>{$row["email"]}</td>
    </tr>
EOD;
 $i++;}
$tbl.=<<<EOD
	  
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
