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

// $mak_query="SELECT sum(total_charges_comp) as Company_Total_Charges,sum(C_otherCost) as Other_Expence,sum(total_charges_comp_vat) as Total_Vat,sum(net_total) as net_total
// FROM (
//     (SELECT cur_vat,round(interpreter.total_charges_comp,2) as total_charges_comp ,round(interpreter.C_otherexpns,2) as C_otherCost, round(IFNULL((interpreter.total_charges_comp * interpreter.cur_vat),0),2) as total_charges_comp_vat,round(IFNULL((interpreter.total_charges_comp),0),2)+round(IFNULL((C_otherexpns),0),2)+round(IFNULL((interpreter.total_charges_comp * interpreter.cur_vat),0),2) as net_total
//     FROM
//    interpreter,interpreter_reg where interpreter.intrpName = interpreter_reg.id $mult_ext_interp and interpreter.deleted_flag = 0 
//    and interpreter.order_cancel_flag=0 
//    and (interpreter.orgName like '%$_words%') and interpreter.assignDate between '$search_2' and '$search_3')
//    union all
//     (SELECT cur_vat,round(telephone.total_charges_comp,2) as total_charges_comp ,round(telephone.C_otherCharges,2) as C_otherCost, round(IFNULL((telephone.total_charges_comp * telephone.cur_vat),0),2) as total_charges_comp_vat,round(IFNULL((telephone.total_charges_comp),0),2)+round(IFNULL((telephone.total_charges_comp * telephone.cur_vat),0),2) as net_total
//      FROM 
// telephone,interpreter_reg where telephone.intrpName = interpreter_reg.id $mult_ext_telep and telephone.deleted_flag = 0 
// and telephone.order_cancel_flag=0 
// and (telephone.orgName like '%$_words%') and telephone.assignDate between '$search_2' and '$search_3')
//  union all
//     (SELECT cur_vat,round(translation.total_charges_comp,2) as total_charges_comp ,round(translation.C_otherCharg,2) as C_otherCost, round(IFNULL((translation.total_charges_comp * translation.cur_vat),0),2) as total_charges_comp_vat,round(IFNULL((translation.total_charges_comp),0),2)+round(IFNULL((translation.total_charges_comp * translation.cur_vat),0),2) as net_total
//      FROM 
// translation,interpreter_reg where translation.intrpName = interpreter_reg.id $mult_ext_trans and translation.deleted_flag = 0 
// and translation.order_cancel_flag=0 
// and (translation.orgName like '%$_words%') and translation.asignDate between '$search_2' and '$search_3')

//    LIMIT {$mak_limit} ) As t";

// $result = mysqli_query($con, $mak_query);
// $mak_results = mysqli_fetch_array($result);

// $mak_non_vat = $mak_results['Company_Total_Charges'];
// $mak_total_vat = $mak_results['Total_Vat'];
// $mak_Other_Expence = $mak_results['Other_Expence'];
// $mak_total_invoice = $mak_results['net_total'];
//$mak_total_invoice = $mak_non_vat + $mak_total_vat + $mak_Other_Expence;

$query="SELECT interpreter.id,interpreter.nameRef,interpreter.orgName,interpreter.orgRef,interpreter.invoiceNo,interpreter.assignDate,interpreter.source,'F2F' as job_type FROM interpreter WHERE interpreter.assignDate between '$search_2' and '$search_3' AND interpreter.is_shifted = 0 and interpreter.deleted_flag = 1 AND interpreter.intrpName=''
UNION ALL
SELECT telephone.id,telephone.nameRef,telephone.orgName,telephone.orgRef,telephone.invoiceNo,telephone.assignDate,telephone.source,'Telephone' as job_type FROM telephone WHERE telephone.assignDate between '$search_2' and '$search_3' AND telephone.is_shifted = 0 and telephone.deleted_flag = 1 AND telephone.intrpName=''
UNION ALL 
SELECT translation.id,translation.nameRef,translation.orgName,translation.orgRef,translation.invoiceNo,translation.asignDate as assignDate,translation.source,'Translation' as job_type FROM translation WHERE translation.asignDate between '$search_2' and '$search_3' AND translation.deleted_flag = 1 AND translation.intrpName='' 
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
<h2 align="center"><u>Home Screen Jobs Deletion Report</u></h2>
<p align="right">Report  Date: {$misc->sys_date()}<br />
  Date  Range:  Date From [{$misc->dated($search_2)}] Date To [{$misc->dated($search_3)}]</p>
</div>

EOD;
$tbl .= <<<EOD
<table>
<thead>
<tr>
 	<th style="width:35px;">Sr.No</th>
	<th>Job id</th>
	<th>LSUK Reference</th>
	<th>Job Type</th>
    <th>Company Name</th>
    <th>Company Reference</th>
    <th>Source</th>
    <th>Invoice Number</th>
    <th>Assign Date</th>
 </tr>

</thead>
EOD;


while ($row = mysqli_fetch_assoc($result)) {

    $tbl .= <<<EOD
        <tr>
<td style="width:35px;">{$i}</td>
<td>{$row["id"]}</td>
<td>{$row["nameRef"]}</td>
<td>{$row["job_type"]}</td>
<td>{$row["orgName"]}</td>
<td>{$row["orgRef"]}</td>
<td>{$row["source"]}</td>
<td>{$row["invoiceNo"]}</td>
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

function ZeroCompTotal(&$map)
{
    $map["non_vat"] = 0;
    $map["vat"] = 0;
    $map["vated_cost"] = 0;
    $map["total"] = 0;
}

function UpdateCompTotal(&$map, &$row)
{
    $total = $row["total_charges_comp"] + $row["total_charges_comp"] * $row["cur_vat"] + $row["C_otherCost"];

    $map["non_vat"] += $row["total_charges_comp"];
    $map["vat"] += $row["vat"];
    $map["vated_cost"] += $row["C_otherCost"];
    $map["total"] += $total;
}

function ShowCompTotal(&$map, &$tbl, $multi)
{
  global $misc;
  $multiExtColumnSpace = ($multi==1?"<td></td>":""); 
  $tbl .= <<<EOD
	<tr>
	<td></td>
	<td></td>
	<td></td>
	<td></td>
	<td></td>
	<td></td>
	<td></td>
	<td></td>
	<td></td>
  $multiExtColumnSpace
	<td></td>
	<td>Comp Total</td>
	<td>{$misc->numberFormat_fun($map["non_vat"])}</td>
	<td>{$misc->numberFormat_fun($map["vat"])}</td>
	<td>{$misc->numberFormat_fun($map["vated_cost"])}</td>
	<td>{$misc->numberFormat_fun($map["total"])}</td>
	</tr>
EOD;
}