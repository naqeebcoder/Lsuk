<?php include '../../db.php';include_once ('../../class.php'); $excel=@$_GET['excel'];$search_1=@$_GET['search_1'];$search_2=@$_GET['search_2'];$search_3=@$_GET['search_3'];
$i=1;$total_interp=0;$total_comp=0;$total_non_vat=0;$total_vat=0;$total_all=0;$total_profit=0;$vat_total_charges_comp=0;
$query="SELECT invoiceNo, 
       invoic_date, 
       orgName, 
       intrpname, 
       source, 
       assignDate, 
       paid_interp, 
       C_otherexpns, 
       total_charges_comp, 
       total_charges_interp, 
	   cur_vat,
       interpreter_reg.name 
FROM   interpreter 
       INNER JOIN interpreter_reg 
               ON interpreter.intrpname = interpreter_reg.id 
WHERE  interpreter.deleted_flag = 0 
       AND interpreter.order_cancel_flag = 0 
       AND assignDate BETWEEN '$search_2' AND '$search_3' 
UNION 
SELECT invoiceNo, 
       invoic_date, 
       orgName, 
       intrpname, 
       source, 
       assignDate, 
       paid_interp, 
       C_othercharges AS C_otherexpns, 
       total_charges_comp, 
       total_charges_interp, 
	   cur_vat,
       interpreter_reg.name 
FROM   telephone 
       INNER JOIN interpreter_reg 
               ON telephone.intrpname = interpreter_reg.id 
WHERE  telephone.deleted_flag = 0 
       AND telephone.order_cancel_flag = 0 
       AND assignDate BETWEEN '$search_2' AND '$search_3' 
UNION 
SELECT invoiceNo, 
       invoic_date, 
       orgName, 
       intrpname, 
       source, 
       asigndate    AS assignDate, 
       paid_interp, 
       C_othercharg AS C_otherexpns, 
       total_charges_comp, 
       total_charges_interp, 
	   cur_vat,
       interpreter_reg.name 
FROM   translation 
       INNER JOIN interpreter_reg 
               ON translation.intrpname = interpreter_reg.id 
WHERE  translation.deleted_flag = 0 
       AND translation.order_cancel_flag = 0 
       AND asigndate BETWEEN '$search_2' AND '$search_3' 
ORDER  BY invoiceNo ";

$result = mysqli_query($con, $query);

// Include the main TCPDF library (search for installation path).
require_once('tcpdf_include.php');
// create new PDF document
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetSubject('TCPDF Tutorial');

// set default header data
include'rip_header_lndscp.php';
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
$pdf->AddPage('L', 'A4');
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
<h2 align="center"><u>LSUK Profit & Loss Report</u></h2>
<p align="right">Report  Date: {$misc->sys_date()}<br />
  Date  Range:  Date From [{$misc->dated($search_2)}] Date To [{$misc->dated($search_3)}]</p>
</div><div aligen='center'>
EOD;
$tbl.=<<<EOD
<table>
<thead>
<tr>
 	<th style="width:35px;">Sr.No</th>

	<th>Invoice Number</th>
	<th>Invoice Date</th>
    <th>Assignment Date</th>
    <th>Client Name</th>
    <th>Interpreter Name</th>
    <th>Language</th>
    <th>Amount Paid to Interpreter</th>
    <th>Amount Invoiced to the Client (Net)</th>
    <th>Non Vat able</th>
    <th>VAT</th>
    <th>Total</th>
    <th>Profit or Loss</th>
 </tr>

</thead>
EOD;
while($row = mysqli_fetch_assoc($result)){
$total_charges_interp=$row["total_charges_interp"];
$vat_total_charges_comp=$row["total_charges_comp"]*$row["cur_vat"];
$total_charges_comp_C_otherexpns=round($row["total_charges_comp"] + $row["C_otherexpns"]);
$total=$row["total_charges_comp"] + $row["C_otherexpns"] - $row["total_charges_interp"];
$tbl.=<<<EOD
    <tr>
      	<td style="width:35px;">{$i}</td>
<td>{$row["invoiceNo"]}</td>
<td>{$row["invoic_date"]}</td>
<td>{$row["assignDate"]}</td>
<td>{$row["orgName"]}</td>
<td>{$row["name"]}</td>
<td>{$row["source"]}</td>
<td>{$total_charges_interp}</td>
<td>{$row["total_charges_comp"]}</td>
<td>{$row["C_otherexpns"]}</td>
<td>{$vat_total_charges_comp}</td>
<td>{$total_charges_comp_C_otherexpns}</td>
<td>{$misc->numberFormat_fun($total)}</td>
    </tr>
EOD;
 $i++;
 $total_interp=$row["total_charges_interp"] + $total_interp;
 $total_comp=$row["total_charges_comp"] + $total_comp;
 $total_non_vat=$row["C_otherexpns"]+$total_non_vat;
 $total_vat=$vat_total_charges_comp + $total_vat;
 $total_all=$total_charges_comp_C_otherexpns+$total_all;
 $total_profit=$total + $total_profit;
 }
 $tbl.=<<<EOD
    <tr>
      	<td style="width:35px;"></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td>TOTAL</td>
<td>{$misc->numberFormat_fun($total_interp)}</td>
<td>{$misc->numberFormat_fun($total_comp)}</td>
<td>{$misc->numberFormat_fun($total_non_vat)}</td>
<td>{$misc->numberFormat_fun($total_vat)}</td>
<td>{$misc->numberFormat_fun($total_all)}</td>
<td>{$misc->numberFormat_fun($total_profit)}</td>
    </tr>
EOD;
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
