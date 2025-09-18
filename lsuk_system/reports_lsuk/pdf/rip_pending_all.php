<?php

include '../../db.php';
include_once '../../class.php';
include_once '../../function.php';

//$excel=@$_GET['excel'];
$excel = SafeVar::GetVar('excel', '');

$search_1 = @$_GET['search_1'];
$search_2 = @$_GET['search_2'];
$search_3 = @$_GET['search_3'];
$mak_page = @$_GET['page'];
$startpoint = @$_GET['startpoint'];
$limit = @$_GET['limit'];
$multi = @$_GET['multi'];


if($multi==1){
  $mult_ext_interp = "and (interpreter.multInv_flag=1 AND (SELECT id from mult_inv WHERE m_inv=interpreter.multInvoicNo and mult_inv.status='') OR (interpreter.multInv_flag=0 AND interpreter.invoiceNo<>'' AND  (round(interpreter.rAmount,2) < round((interpreter.total_charges_comp+(interpreter.total_charges_comp*interpreter.cur_vat)),2) AND interpreter.total_charges_comp >0) ))";
  $mult_ext_telep = "and (telephone.multInv_flag=1 AND (SELECT id from mult_inv WHERE m_inv=telephone.multInvoicNo and mult_inv.status='') OR (telephone.multInv_flag=0 AND telephone.invoiceNo<>'' and (round(telephone.rAmount,2) < round((telephone.total_charges_comp+(telephone.total_charges_comp*telephone.cur_vat)),2) AND telephone.total_charges_comp > 0) ))";
  $mult_ext_trans = "and (translation.multInv_flag=1 AND (SELECT id from mult_inv WHERE m_inv=translation.multInvoicNo and mult_inv.status='') OR (translation.multInv_flag=0 AND translation.invoiceNo<>'' and (round(translation.rAmount,2) < round((translation.total_charges_comp+(translation.total_charges_comp*translation.cur_vat)),2) AND translation.total_charges_comp > 0)))";
}else{
  $mult_ext_interp = "and ((interpreter.multInv_flag=0 AND interpreter.invoiceNo<>'' AND  (round(interpreter.rAmount,2) < round((interpreter.total_charges_comp+(interpreter.total_charges_comp*interpreter.cur_vat)),2) AND interpreter.total_charges_comp >0) ))";
  $mult_ext_telep = "and ((telephone.multInv_flag=0 AND telephone.invoiceNo<>'' and (round(telephone.rAmount,2) < round((telephone.total_charges_comp+(telephone.total_charges_comp*telephone.cur_vat)),2) AND telephone.total_charges_comp > 0) ))";
  $mult_ext_trans = "and ((translation.multInv_flag=0 AND translation.invoiceNo<>'' and (round(translation.rAmount,2) < round((translation.total_charges_comp+(translation.total_charges_comp*translation.cur_vat)),2) AND translation.total_charges_comp > 0)))";
}

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
$table = 'interpreter';
$total_charges_comp = 0;
$C_otherCharges = 0;
$g_total = 0;
$g_vat = 0;
$C_otherCharges = 0;
$non_vat = 0;
$vated_cost = 0;

$comp_name = $acttObj->unique_data('comp_reg', 'name', 'abrv', $search_1);
//...................................................For Multiple Selection...................................\\
$counter = 0;
if(!empty($search_1)){
  $arr = explode(',', $search_1);
  // $_words = implode("' OR orgName like '", $arr);
  $_words = "'".implode("','", $arr)."'";
}else{
  $_words = "";
}
//echo $search_2.'<br/>'.$search_3.'<br/>'.$comp_name;

//......................................\\//\\//\\//\\//........................................................\\
//total_charges_comp,cur_vat,C_otherCost,sum(total_charges_comp) as Company_Total_Charges, sum(total_charges_comp*cur_vat) as Total_Vat,sum(C_otherCost) as Other_Expence
// $mak_query = "SELECT sum(total_charges_comp) as Company_Total_Charges,sum(C_otherCost) as Other_Expence,sum(total_charges_comp_vat) as Total_Vat,sum(net_total) as net_total
// FROM (
//     (SELECT cur_vat,round(interpreter.total_charges_comp,2) as total_charges_comp ,round(interpreter.C_otherexpns,2) as C_otherCost, round(IFNULL((interpreter.total_charges_comp * interpreter.cur_vat),0),2) as total_charges_comp_vat,round(IFNULL((interpreter.total_charges_comp),0),2)+round(IFNULL((C_otherexpns),0),2)+round(IFNULL((interpreter.total_charges_comp * interpreter.cur_vat),0),2) as net_total
//     FROM
//    interpreter,interpreter_reg where interpreter.intrpName = interpreter_reg.id and interpreter.deleted_flag = 0 
//    and interpreter.order_cancel_flag=0 and interpreter.invoiceNo<>''  
//    and (round(interpreter.rAmount,2) < round((interpreter.total_charges_comp+(interpreter.total_charges_comp*interpreter.cur_vat)),2) or interpreter.total_charges_comp =0) 
//    and (interpreter.orgName IN ($_words)) ".(!empty($search_2)?"and interpreter.assignDate between '$search_2' and '$search_3'":"").")
//    union all
//     (SELECT cur_vat,round(telephone.total_charges_comp,2) as total_charges_comp ,round(telephone.C_otherCharges,2) as C_otherCost, round(IFNULL((telephone.total_charges_comp * telephone.cur_vat),0),2) as total_charges_comp_vat,round(IFNULL((telephone.total_charges_comp),0),2)+round(IFNULL((telephone.total_charges_comp * telephone.cur_vat),0),2) as net_total
//      FROM 
// telephone,interpreter_reg where telephone.intrpName = interpreter_reg.id and telephone.deleted_flag = 0 
// and telephone.order_cancel_flag=0 and telephone.invoiceNo<>'' 
// and (round(telephone.rAmount,2) < round((telephone.total_charges_comp+(telephone.total_charges_comp*telephone.cur_vat)),2) or telephone.total_charges_comp =0) 
// and (telephone.orgName IN ($_words)) ".(!empty($search_2)?"and telephone.assignDate between '$search_2' and '$search_3'":"").")
//  union all
//     (SELECT cur_vat,round(translation.total_charges_comp,2) as total_charges_comp ,round(translation.C_otherCharg,2) as C_otherCost, round(IFNULL((translation.total_charges_comp * translation.cur_vat),0),2) as total_charges_comp_vat,round(IFNULL((translation.total_charges_comp),0),2)+round(IFNULL((translation.total_charges_comp * translation.cur_vat),0),2) as net_total
//      FROM 
// translation,interpreter_reg where translation.intrpName = interpreter_reg.id and translation.deleted_flag = 0 
// and translation.order_cancel_flag=0 and translation.invoiceNo<>''
// and (round(translation.rAmount,2) < round((translation.total_charges_comp+(translation.total_charges_comp*translation.cur_vat)),2) or translation.total_charges_comp =0) 
// and (translation.orgName IN ($_words)) ".(!empty($search_2)?"and translation.asignDate between '$search_2' and '$search_3'":"").")

//    LIMIT {$mak_limit} ) As t";

$mak_query="SELECT sum(total_charges_comp) as Company_Total_Charges,sum(C_otherCost) as Other_Expence,sum(total_charges_comp_vat) as Total_Vat,sum(net_total) as net_total
FROM (
    (SELECT cur_vat,round(interpreter.total_charges_comp,2) as total_charges_comp ,round(interpreter.C_otherexpns,2) as C_otherCost, round(IFNULL((interpreter.total_charges_comp * interpreter.cur_vat),0),2) as total_charges_comp_vat,round(IFNULL((interpreter.total_charges_comp),0),2)+round(IFNULL((C_otherexpns),0),2)+round(IFNULL((interpreter.total_charges_comp * interpreter.cur_vat),0),2) as net_total
    FROM
   interpreter,interpreter_reg where interpreter.intrpName = interpreter_reg.id $mult_ext_interp and interpreter.deleted_flag = 0 
   and interpreter.order_cancel_flag=0 ".(!empty($_words)?" and (interpreter.orgName IN ($_words)) ":"")." ".(!empty($search_2)?"and interpreter.assignDate between '$search_2' and '$search_3'":"").")
   union all
    (SELECT cur_vat,round(telephone.total_charges_comp,2) as total_charges_comp ,round(telephone.C_otherCharges,2) as C_otherCost, round(IFNULL((telephone.total_charges_comp * telephone.cur_vat),0),2) as total_charges_comp_vat,round(IFNULL((telephone.total_charges_comp),0),2)+round(IFNULL((telephone.total_charges_comp * telephone.cur_vat),0),2) as net_total
     FROM 
telephone,interpreter_reg where telephone.intrpName = interpreter_reg.id $mult_ext_telep and telephone.deleted_flag = 0 
and telephone.order_cancel_flag=0 ".(!empty($_words)?" and (telephone.orgName IN ($_words)) ":"")." ".(!empty($search_2)?"and telephone.assignDate between '$search_2' and '$search_3'":"").")
 union all
    (SELECT cur_vat,round(translation.total_charges_comp,2) as total_charges_comp ,round(translation.C_otherCharg,2) as C_otherCost, round(IFNULL((translation.total_charges_comp * translation.cur_vat),0),2) as total_charges_comp_vat,round(IFNULL((translation.total_charges_comp),0),2)+round(IFNULL((translation.total_charges_comp * translation.cur_vat),0),2) as net_total
     FROM 
translation,interpreter_reg where translation.intrpName = interpreter_reg.id $mult_ext_trans and translation.deleted_flag = 0 
and translation.order_cancel_flag=0 ".(!empty($_words)?" and (translation.orgName IN ($_words)) ":"")." ".(!empty($search_2)?"and translation.asignDate between '$search_2' and '$search_3'":"").")

   LIMIT {$mak_limit} ) As t";

$result = mysqli_query($con, $mak_query);
$mak_results = mysqli_fetch_array($result);

$mak_non_vat = $mak_results['Company_Total_Charges'];
$mak_total_vat = $mak_results['Total_Vat'];
$mak_Other_Expence = $mak_results['Other_Expence'];
$mak_total_invoice = $mak_results['net_total'];
//$mak_total_invoice = $mak_non_vat + $mak_total_vat + $mak_Other_Expence;

// $query = "(SELECT
//    interpreter.porder,
//    interpreter.orgRef,
//    interpreter.invoiceNo,
//    interpreter.assignDate,
//    interpreter.assignTime,
//    interpreter.source,
//    interpreter.orgName,
//    interpreter.total_charges_comp,
//    interpreter.C_otherexpns as C_otherCost,
//    interpreter.cur_vat,
//    interpreter.total_charges_comp * interpreter.cur_vat as vat,
//    interpreter_reg.name,
//    'Interpreter' as tble
// FROM
//    interpreter,interpreter_reg where interpreter.intrpName = interpreter_reg.id and interpreter.commit=1 and interpreter.deleted_flag = 0 
//    and interpreter.order_cancel_flag=0 and interpreter.invoiceNo<>''  
//    and (round(interpreter.rAmount,2) < round((interpreter.total_charges_comp+(interpreter.total_charges_comp*interpreter.cur_vat)),2) or interpreter.total_charges_comp =0) 
//    and (interpreter.orgName IN ($_words)) ".(!empty($search_2)?"and interpreter.assignDate between '$search_2' and '$search_3'":"").")
// union all
// (SELECT
//    telephone.porder,
//    telephone.orgRef,
//    telephone.invoiceNo,
//    telephone.assignDate,
//    telephone.assignTime,
//    telephone.source,
//    telephone.orgName,
//    telephone.total_charges_comp,
//    telephone.C_otherCharges as C_otherCost,
//    telephone.cur_vat,
//    telephone.total_charges_comp * telephone.cur_vat as vat,
//    interpreter_reg.name ,
//    'Telephone' as tble
// FROM 
// telephone,interpreter_reg where telephone.intrpName = interpreter_reg.id and telephone.commit=1 and telephone.deleted_flag = 0 
// and telephone.order_cancel_flag=0 and telephone.invoiceNo<>'' 
// and (round(telephone.rAmount,2) < round((telephone.total_charges_comp+(telephone.total_charges_comp*telephone.cur_vat)),2) or telephone.total_charges_comp =0) 
// and (telephone.orgName IN ($_words)) ".(!empty($search_2)?"and telephone.assignDate between '$search_2' and '$search_3'":"").")
// union all
// (SELECT
//    translation.porder,
//    translation.orgRef,
//    translation.invoiceNo,
//    translation.asignDate as assignDate,
//    'Nil' as assignTime,
//    translation.source,
//    translation.orgName,
//    translation.total_charges_comp,
//    translation.C_otherCharg as C_otherCost,
//    translation.cur_vat,
//    translation.total_charges_comp * translation.cur_vat as vat,
//    interpreter_reg.name ,
//    'Translation' as tble
// FROM 
// translation,interpreter_reg where translation.intrpName = interpreter_reg.id and translation.deleted_flag = 0 
// and translation.order_cancel_flag=0 and translation.invoiceNo<>''
// and (round(translation.rAmount,2) < round((translation.total_charges_comp+(translation.total_charges_comp*translation.cur_vat)),2) or translation.total_charges_comp =0) 
// and (translation.orgName IN ($_words)) ".(!empty($search_2)?"and translation.asignDate between '$search_2' and '$search_3'":"").")
//   ORDER BY orgName ASC
//   LIMIT {$startpoint} , {$limit}";

$query="(SELECT
interpreter.porder,
interpreter.orgRef,
interpreter.invoiceNo,
interpreter.assignDate,
interpreter.assignTime,
interpreter.source,
interpreter.orgName,
interpreter.total_charges_comp,
interpreter.C_otherexpns as C_otherCost,
interpreter.cur_vat,
interpreter.commit,
interpreter.multInv_flag,
interpreter.total_charges_comp * interpreter.cur_vat as vat,
interpreter_reg.name,
'Interpreter' as tble
FROM
interpreter,interpreter_reg where interpreter.intrpName = interpreter_reg.id $mult_ext_interp and interpreter.deleted_flag = 0 
and interpreter.order_cancel_flag=0  ".(!empty($_words)?" and (interpreter.orgName IN ($_words)) ":"")." 
 ".(!empty($search_2)?"and interpreter.assignDate between '$search_2' and '$search_3'":"").")
union all
(SELECT
telephone.porder,
telephone.orgRef,
telephone.invoiceNo,
telephone.assignDate,
telephone.assignTime,
telephone.source,
telephone.orgName,
telephone.total_charges_comp,
telephone.C_otherCharges as C_otherCost,
telephone.cur_vat,
telephone.commit,
telephone.multInv_flag,
telephone.total_charges_comp * telephone.cur_vat as vat,
interpreter_reg.name ,
'Telephone' as tble
FROM 
telephone,interpreter_reg where telephone.intrpName = interpreter_reg.id $mult_ext_telep and telephone.deleted_flag = 0 
and telephone.order_cancel_flag=0 ".(!empty($_words)?" and (telephone.orgName IN ($_words)) ":"")." 
".(!empty($search_2)?"and telephone.assignDate between '$search_2' and '$search_3'":"").")
union all
(SELECT
translation.porder,
translation.orgRef,
translation.invoiceNo,
translation.asignDate as assignDate,
'Nil' as assignTime,
translation.source,
translation.orgName,
translation.total_charges_comp,
translation.C_otherCharg as C_otherCost,
translation.cur_vat,
translation.commit,
translation.multInv_flag,
translation.total_charges_comp * translation.cur_vat as vat,
interpreter_reg.name ,
'Translation' as tble
FROM 
translation,interpreter_reg where translation.intrpName = interpreter_reg.id $mult_ext_trans and translation.deleted_flag = 0 
and translation.order_cancel_flag=0  ".(!empty($_words)?" and (translation.orgName IN ($_words)) ":"")."
".(!empty($search_2)?"and translation.asignDate between '$search_2' and '$search_3'":"").")
ORDER BY orgName ASC
LIMIT {$startpoint} , {$limit}";
// echo $_words."words<br>";
// echo $query;die();exit;
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
<h2 align="center"><u>Account Statement Report – Pending Invoices Report</u></h2>
<p align="right">Report  Date: {$misc->sys_date()}<br />
  Date  Range:  Date From [{$misc->dated($search_2)}] Date To [{$misc->dated($search_3)}]</p>
</div>
<p>Organization(s) Selected</p>
<table class="aa" border="1" cellspacing="0" cellpadding="0">
  <tr>
    <td colspan="5" valign="top">{$search_1}</td>
  </tr>
</table><br/><br/>

EOD;
$multiExtColumnHead = ($multi==1?"<th>Invoice Type</th>":""); 
$tbl .= <<<EOD
<table>
<thead>
<tr>
    <th style="width:35px;">Sr.No</th>
    <th>Mode</th>
    <th>Invoice Number</th>
    <th>Job Date</th>
    <th>Job Time</th>
    <th>Language</th>
    <th>Client Name</th>
    <th>Client Ref</th>
    <th>Purch.Order</th>
    <th>Interpreter</th>
    <th>Print Status</th>
    $multiExtColumnHead
    <th>Without VAT</th>
    <th>VAT</th>
    <th>Non-VAT Costs</th>
    <th>Invoice Total</th>
 </tr>

</thead>
EOD;

$runcompany = "";
$nowcompany = "";
$mapCoTotals = array();
ZeroCompTotal($mapCoTotals);

$loop = 0;

while ($row = mysqli_fetch_assoc($result)) {

    $vat = $row["total_charges_comp"] * $row["cur_vat"];
    $total = $row["total_charges_comp"] + $vat + $row["C_otherCost"];
    $g_total = $total + $g_total;
    $non_vat = $row["total_charges_comp"] + $non_vat;
    $g_vat = $vat + $g_vat;
    $vated_cost = $row["C_otherCost"] + $vated_cost;
    $C_otherCost = $row["C_otherCost"];

    $nowcompany = $row["orgName"];
    if ($loop == 0) {
        OrgOutput::WriteTR($nowcompany, $runcompany, $tbl);
    }

    $loop++;
    if ($runcompany != $nowcompany) {
        ShowCompTotal($mapCoTotals, $tbl,$multi);
        ZeroCompTotal($mapCoTotals);
    }
    OrgOutput::WriteTR($nowcompany, $runcompany, $tbl);

    UpdateCompTotal($mapCoTotals, $row);
    $issueStatus = $row["multInv_flag"]==0 ? ($row["commit"]==1 ? "Issued" : "Not Issued") : "Issued";
    $typeStatus = $multi==1 ? ($row["multInv_flag"]==1 ? "<td>Multi</td>" : "<td>Single</td>") : "";


    $tbl .= <<<EOD
    <tr>
      <td style="width:35px;">{$i}</td>
<td>{$row["tble"]}</td>
<td>{$row["invoiceNo"]}</td>
<td>{$misc->dated($row['assignDate'])}</td>
<td>{$row["assignTime"]}</td>
<td>{$row["source"]}</td>
<td>{$row["orgName"]}</td>
<td>{$row["orgRef"]}</td>
<td>{$row["porder"]}</td>
<td>{$row["name"]}</td>
<td>{$issueStatus}</td>
$typeStatus
<td>{$row["total_charges_comp"]}</td>
<td>{$vat}</td>
<td>{$C_otherCost}</td>
<td>{$total}</td>
    </tr>
EOD;

    $i++;
}
ShowCompTotal($mapCoTotals, $tbl,$multi);

// $mak_non_vat = $mak_results['Company_Total_Charges'];
// $mak_total_vat = $mak_results['Total_Vat'];
// $mak_Other_Expence = $mak_results['Other_Expence'];
// $mak_total_invoice

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
		<td>Total</td>
		<td>{$misc->numberFormat_fun($mak_non_vat)}</td>
		<td>{$misc->numberFormat_fun($mak_total_vat)}</td>
		<td>{$misc->numberFormat_fun($mak_Other_Expence)}</td>
		<td>{$misc->numberFormat_fun($mak_total_invoice)}</td>
	  </tr>

</table>

EOD;

$pdf->writeHTML($tbl, true, false, false, false, '');

// -----------------------------------------------------------------------------

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