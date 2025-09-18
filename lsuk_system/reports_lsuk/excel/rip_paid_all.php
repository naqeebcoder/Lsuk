<?php include '../../db.php';include_once '../../class.php';
$excel = @$_GET['excel'];
$search_1 = @$_GET['search_1'];
$search_2 = @$_GET['search_2'];
$search_3 = @$_GET['search_3'];
$multi = @$_GET['multi'];
if($multi==1){
  $mult_ext_interp = "and (interpreter.multInv_flag=1 AND (SELECT id from mult_inv WHERE m_inv=interpreter.multInvoicNo and mult_inv.status='Received') OR (interpreter.multInv_flag=0 AND interpreter.invoiceNo<>'' AND  (round(interpreter.rAmount,2) >= round((interpreter.total_charges_comp+(interpreter.total_charges_comp*interpreter.cur_vat)),2) AND interpreter.total_charges_comp >0) ))";
  $mult_ext_telep = "and (telephone.multInv_flag=1 AND (SELECT id from mult_inv WHERE m_inv=telephone.multInvoicNo and mult_inv.status='Received') OR (telephone.multInv_flag=0 AND telephone.invoiceNo<>'' and (round(telephone.rAmount,2) >= round((telephone.total_charges_comp+(telephone.total_charges_comp*telephone.cur_vat)),2) AND telephone.total_charges_comp > 0) ))";
  $mult_ext_trans = "and (translation.multInv_flag=1 AND (SELECT id from mult_inv WHERE m_inv=translation.multInvoicNo and mult_inv.status='Received') OR (translation.multInv_flag=0 AND translation.invoiceNo<>'' and (round(translation.rAmount,2) >= round((translation.total_charges_comp+(translation.total_charges_comp*translation.cur_vat)),2) AND translation.total_charges_comp > 0)))";
}else{
  $mult_ext_interp = "and ((interpreter.multInv_flag=0 AND interpreter.invoiceNo<>'' AND  (round(interpreter.rAmount,2) >= round((interpreter.total_charges_comp+(interpreter.total_charges_comp*interpreter.cur_vat)),2) AND interpreter.total_charges_comp >0) ))";
  $mult_ext_telep = "and ((telephone.multInv_flag=0 AND telephone.invoiceNo<>'' and (round(telephone.rAmount,2) >= round((telephone.total_charges_comp+(telephone.total_charges_comp*telephone.cur_vat)),2) AND telephone.total_charges_comp > 0) ))";
  $mult_ext_trans = "and ((translation.multInv_flag=0 AND translation.invoiceNo<>'' and (round(translation.rAmount,2) >= round((translation.total_charges_comp+(translation.total_charges_comp*translation.cur_vat)),2) AND translation.total_charges_comp > 0)))";
}
$i = 1;
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
$arr = explode(',', $search_1);
$_words = implode("' OR orgName like '", $arr);
//......................................\\//\\//\\//\\//........................................................\\
$query = "(SELECT
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
and interpreter.order_cancel_flag=0  
and (interpreter.orgName like '%$_words%')
 and interpreter.assignDate between '$search_2' and '$search_3')
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
and telephone.order_cancel_flag=0 
and (telephone.orgName like '%$_words%')
and telephone.assignDate between '$search_2' and '$search_3')
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
and translation.order_cancel_flag=0  
and (translation.orgName like '%$_words%')
and translation.asignDate between '$search_2' and '$search_3')
ORDER BY orgName ASC";

$result = mysqli_query($con, $query);

//...................................................................................................................................../////
$multiExtColumnHead = ($multi==1?"<th>Invoice Type</th>":"");
$htmlTable = '';
$htmlTable .= '<style>
table {border-collapse: collapse; width:100%;word-wrap: break-word;}
th {border: 1px solid #999; padding: 0.5rem;text-align: left;background-color:#039; color:#FFF;font-weight:bold;word-wrap: break-word;}
td {border: 1px solid #999; padding: 0.5rem;text-align: left;word-wrap: break-word;}
</style>
<div>
<h2 align="center"><u>Account Statement Report – Pending Invoices Report</u></h2>
<p align="right">Report  Date: ' . $misc->sys_date() . '<br />
  Date  Range: Date From ' . $misc->dated($search_2) . ' Date To ' . $misc->dated($search_3) . '</p>
</div>
<p>Organization(s) Selected</p>
<table class="aa" border="1" cellspacing="0" cellpadding="0" style="width:250px">
  <tr>
    <td width="200" valign="top"></td>
  </tr>
</table>
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
    <th>Purch.Order#</th>
    <th>Interpreter</th>
    <th>Print Status</th>
    '.$multiExtColumnHead.'
    <th>Without VAT</th>
    <th>VAT</th>
    <th>Non-VAT Costs</th>
    <th>Invoice Total</th>';

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
        OrgOutput::WriteTR($nowcompany, $runcompany, $htmlTable);
    }

    $loop++;
    if ($runcompany != $nowcompany) {
        ShowCompTotal($mapCoTotals, $htmlTable,$multi);
        ZeroCompTotal($mapCoTotals);
    }
    OrgOutput::WriteTR($nowcompany, $runcompany, $htmlTable);

    UpdateCompTotal($mapCoTotals, $row);
    $issueStatus = $row["multInv_flag"]==0 ? ($row["commit"]==1 ? "Issued" : "Not Issued") : "Issued";
    $typeStatus = $multi==1 ? ($row["multInv_flag"]==1 ? "<td>Multi</td>" : "<td>Single</td>") : "";

    $htmlTable .= '<tr>';
    $htmlTable .= '<td>' . $i . '</td>';
    $htmlTable .= '<td>' . $row["tble"] . '</td>';
    $htmlTable .= '<td>' . $row["invoiceNo"] . '</td>';
    $htmlTable .= '<td>' . $misc->dated($row['assignDate']) . '</td>';
    $htmlTable .= '<td>' . $row["assignTime"] . '</td>';
    $htmlTable .= '<td>' . $row["source"] . '</td>';
    $htmlTable .= '<td>' . $row["orgName"] . '</td>';
    $htmlTable .= '<td>' . $row["orgRef"] . '</td>';
    $htmlTable .= '<td>' . $row["porder"] . '</td>';
    $htmlTable .= '<td>' . $row["name"] . '</td>';

    $htmlTable .= '<td>' . $issueStatus . '</td>';
    $htmlTable .= $typeStatus;


    $htmlTable .= '<td>' . $row["total_charges_comp"] . '</td>';
    $htmlTable .= '<td>' . $vat . '</td>';
    $htmlTable .= '<td>' . $C_otherCost . '</td>';

    $htmlTable .= '<td>' . $total . '</td>
</tr>';
    $i++;
}
ShowCompTotal($mapCoTotals, $htmlTable,$multi);
$multiExtColumnSpace = ($multi==1?"<td></td>":"");
$htmlTable .= '<tr>';
$htmlTable .= '<td></td>';
$htmlTable .= '<td></td>';
$htmlTable .= '<td></td>';
$htmlTable .= '<td></td>';
$htmlTable .= '<td></td>';
$htmlTable .= '<td></td>';
$htmlTable .= '<td></td>';
$htmlTable .= '<td></td>';
$htmlTable .= '<td></td>';
$htmlTable .= $multiExtColumnSpace;
$htmlTable .= '<td></td>';
$htmlTable .= '<td>Total</td>';
$htmlTable .= '<td>' . $misc->numberFormat_fun($non_vat) . '</td>';
$htmlTable .= '<td>' . $misc->numberFormat_fun($g_vat) . '</td>';
$htmlTable .= '<td>' . $misc->numberFormat_fun($vated_cost) . '</td>';
$htmlTable .= '<td>' . $misc->numberFormat_fun($g_total) . '</td>
</tr>';
$htmlTable .= '</table>';

list($a, $b) = explode('.', basename(__FILE__));
header("Content-Type: application/xls");
header("Content-Disposition: attachment; filename=" . $a . ".xls");
echo $htmlTable;

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

function ShowCompTotal(&$map, &$tbl,$multi)
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