<?php include '../../db.php';include_once ('../../class.php'); $excel=@$_GET['excel'];session_start();$UserName=$_SESSION['UserName'];$prv=$_SESSION['prv']; $search_1=@$_GET['search_1'];$search_2=@$_GET['search_2'];$search_3=@$_GET['search_3'];$search_4=$_GET['search_4'];
$i=1;$table='interpreter';$total_charges_comp=0;$C_otherCost=0;$g_total=0;$g_vat=0;$C_otherCost=0;$non_vat=0;$comp_name=$acttObj->unique_data('comp_reg','name','abrv',$search_1);
if($search_4=='Management'){
	$query_outer="SELECT bz_credit.creditId, bz_credit.mode, bz_credit.bz_credit from bz_credit
	where bz_credit.orgName='$search_1' and bz_credit.creditId='$search_4' and bz_credit.creditId<>'' 
	";
}else{$query_outer="SELECT bz_credit.creditId, bz_credit.mode, bz_credit.bz_credit from bz_credit
	where bz_credit.orgName='$search_1' and bz_credit.creditId<>'' 
	";}
$result_outer = mysqli_query($con, $query_outer);

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
table {border-collapse: collapse; width:100%;}
th {border: 1px solid #999; padding: 0.5rem;text-align left; background-color:#039; color:#FFF;font-weight:bold;}
td {border: 1px solid #999; padding: 0.5rem;text-align: left; word-wrap: break-word;}
</style>
EOD;

$tbl.=<<<EOD
<div>
<h2 align="center"><u>Business Credit Report for {$comp_name}</u></h2>
<p align="right">Report  Date: {$misc->sys_date()}<br />
  Date  Range:  Date From [{$misc->dated($search_2)}] Date To [{$misc->dated($search_3)}]</p>
</div>
EOD;
$tbl.=<<<EOD
<table>
<thead>
<tr>
 	<th style="width:35px;">Sr.No</th>
    <th>Category</th>
	<th>Business Credit Order # </th>
	<th>Amount </th>
	<th>Invoice #</th>
    <th>Amount</th>
 </tr>

</thead>
EOD;
while($row_outer = mysqli_fetch_assoc($result_outer)){$creditId=$row_outer["creditId"];$mode=$row_outer["mode"];$bz_credit=$row_outer["bz_credit"];

$tbl.=<<<EOD
    <tr style="background-color:#FF0;">
		<td style="width:35px;"></td>
		<td></td>
		<td >{$creditId}</td>
		<td>{$bz_credit}</td>
		<td></td>
		<td></td>
    </tr>
EOD;

$query="SELECT interpreter.invoiceNo,(interpreter.total_charges_comp + interpreter.C_otherexpns + interpreter.total_charges_comp * interpreter.cur_vat) as total_charges_comp, 'Interpreter' as tbl from interpreter	
	where interpreter.orgName='$search_1' and interpreter.creditId='$creditId' and interpreter.porder <>'' and interpreter.assignDate BETWEEN '$search_2' AND '$search_3'
	union
	SELECT telephone.invoiceNo,(telephone.total_charges_comp + telephone.C_otherCharges + telephone.total_charges_comp * telephone.cur_vat) as total_charges_comp, 'Telephone' as tbl from telephone	
	where telephone.orgName='$search_1' and telephone.creditId='$creditId' and telephone.porder <>'' and telephone.assignDate BETWEEN '$search_2' AND '$search_3'
	union
	SELECT translation.invoiceNo,(translation.total_charges_comp + translation.otherCharg + translation.total_charges_comp * translation.cur_vat) as total_charges_comp, 'translation' as tbl from translation	
	where translation.orgName='$search_1' and translation.creditId='$creditId' and translation.porder <>'' and translation.asignDate BETWEEN '$search_2' AND '$search_3'
	
	";

$result = mysqli_query($con, $query);
while($row = mysqli_fetch_assoc($result)){
$tbl.=<<<EOD
    <tr>
      	<td style="width:35px;">{$i}</td>
		<td>{$row["tbl"]}</td>
		<td></td>
		<td></td>
		<td>{$row["invoiceNo"]}</td>
		<td>{$misc->numberFormat_fun($row["total_charges_comp"])}</td>
    </tr>
EOD;
 $i++;}}
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
