<?php include '../../db.php';include_once ('../../class.php'); $excel=@$_GET['excel'];$search_1=@$_GET['search_1'];$search_2=@$_GET['search_2'];$search_3=@$_GET['search_3'];
$i=1;$table='interpreter';$total_charges_comp=0;$C_otherCost=0;$g_total=0;$g_vat=0;$C_otherCost=0;$non_vat=0;$comp_name=$acttObj->unique_data('comp_reg','name','abrv',$search_1);
$query="SELECT
   interpreter.dated,
   interpreter.assignTime,
   interpreter.orgName,
   interpreter.source,
   interpreter.hoursWorkd,
   interpreter.total_charges_comp,
   interpreter.invoiceNo,
   interpreter.porder,
   'Interpreter' as tble,
   IF(interpreter.rAmount>0,'Paid','Unpaid') as inv_status
FROM
   interpreter 
  
where
   interpreter.deleted_flag = 0 
   and interpreter.order_cancel_flag=0
   and interpreter.orgName like '$search_1%' 
   and interpreter.dated between '$search_2' and '$search_3' 
   
   union
SELECT
   telephone.dated,
   telephone.assignTime,
   telephone.orgName,
   telephone.source,
   telephone.hoursWorkd,
   telephone.total_charges_comp,
   telephone.invoiceNo,
   telephone.porder,
   'Telephone' as tble,
   IF(telephone.rAmount>0,'Paid','Unpaid') as inv_status
FROM
   telephone 
where
 	telephone.deleted_flag = 0 
   and telephone.order_cancel_flag=0
   and telephone.orgName like '$search_1%' 
   	and telephone.dated between '$search_2' and '$search_3' 
   
union
SELECT
   translation.dated,
   'Nil' as assignTime,
   translation.orgName,
   translation.source,
   translation.C_numberUnit as hoursWorkd,
   translation.total_charges_comp,
   translation.invoiceNo,
   translation.porder,
   'Translation' as tble,
   IF(translation.rAmount>0,'Paid','Unpaid') as inv_status
FROM
   translation 
where
   translation.deleted_flag = 0 
   and translation.order_cancel_flag=0
   and translation.orgName like '$search_1%' 
   and translation.asignDate between '$search_2' and '$search_3' 
   
";

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
<h2 align="center"><u>SLA Report - {$comp_name}</u></h2>
<p align="right">Report  Date: {$misc->sys_date()}<br />
  Date  Range:  Date From [{$misc->dated($search_2)}] Date To [{$misc->dated($search_3)}]</p>
</div>
EOD;
$tbl.=<<<EOD
<table>
<thead>
<tr>
 	<th style="width:35px;">Sr.No</th>
	<th>Invoice Number</th>
	<th>Mode</th>
	<th>Date</th>
    <th>Time</th>
    <th>Company</th>
    <th>Language</th>
    <th>Interp Time</th>
    <th>Cost</th>
    <th>Purchase Order Number</th>	
    <th>Invoice Status </th>
 </tr>

</thead>
EOD;
while($row = mysqli_fetch_assoc($result)){
$tbl.=<<<EOD
    <tr>
      	<td style="width:35px;">{$i}</td>
<td>{$row["invoiceNo"]}</td>
<td>{$row["tble"]}</td>
<td>{$row["dated"]}</td>
<td>{$row["assignTime"]}</td>
<td>{$row["orgName"]}</td>
<td>{$row["source"]}</td>
<td>{$row["hoursWorkd"]}</td>
<td>{$row['total_charges_comp']}</td>
<td>{$row['porder']}</td>
<td>{$row['inv_status']}</td>
    </tr>
EOD;
 $i++;}
$tbl.=<<<EOD
	  
</table>

EOD;

	



$pdf->writeHTML($tbl, true, false, false, false, '');


// -----------------------------------------------------------------------------

//Close and output PDF document
list($a,$b)=explode('.',basename(__FILE__));
$pdf->Output($a.'.pdf', 'I');
//============================================================+
// END OF FILE
//==========================================================EXCEL FORMAT=========================================================+
