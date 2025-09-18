<?php 
include '../../db.php';
include_once ('../../class.php'); 
$excel=@$_GET['excel'];
$search_1=@$_GET['search_1']; 
$search_2=@$_GET['search_2']; 
$search_3=@$_GET['search_3'];
$mng_check=@$_GET['mng_check'];
$i=1;
$table='expence';
$amoun=0;
$netamount=0;
$vat=0;
$nonvat=0;

  $exp_titles=$acttObj->read_specific('GROUP_CONCAT(title) as exp_titles','expence_list','id IN ('.$search_1.')');
$query="SELECT $table.*,expence_list.title as exp_title FROM expence,expence_list where $table.type_id=expence_list.id AND expence.type_id IN ($search_1) and $table.billDate between '$search_2' and '$search_3' and $table.deleted_flag=0 order by expence_list.title";
$result = mysqli_query($con, $query);

$extra_exp_rows = array();

$credit_notes=$acttObj->read_all("*","credit_notes"," dated between '$search_2' and '$search_3'");
$extra_exp_index=0;
while($row_credit_note = $credit_notes->fetch_assoc()){
  $json_data=json_decode($row_credit_note['data'], true);
  
  // $cr_type = 'Credit Note';
  // $credit_note_issue_date =$row_credit_note['dated'];
  // $credit_note_order_type = $json_data['order_type'];
  // $credit_note_company_id = $json_data['order_company_id']?$acttObj->read_specific("abrv","comp_reg"," id=".$json_data['order_company_id']." ")['abrv']:"";
  // $credit_note_invoiceno = $json_data['invoiceNo'];
  // $credit_note_charges = $json_data['total_charges_comp'];
  // $credit_note_vat = $json_data['cur_vat'] * $json_data['total_charges_comp'];
  // $credit_note_charges = $json_data['order_type'] == 'f2f' ? $json_data['total_charges_comp'] + $json_data['C_otherexpns'] : $json_data['total_charges_comp'];
  // $credit_note_total_value = $credit_note_charges + $credit_note_vat;

  $extra_exp_rows[$extra_exp_index]['exp_title'] =  'Credit Note';
  $extra_exp_rows[$extra_exp_index]['billDate'] = $row_credit_note['dated'];
  $extra_exp_rows[$extra_exp_index]['details'] = $row_credit_note['order_type'];
  $comp_id = $json_data['order_company_id'];
  $extra_exp_rows[$extra_exp_index]['comp'] = $acttObj->read_specific("abrv","comp_reg"," id='$comp_id' ")['abrv'];
  $extra_exp_rows[$extra_exp_index]['voucher'] = $json_data['invoiceNo'];
  $extra_exp_rows[$extra_exp_index]['netamount'] = $json_data['order_type'] == 'f2f' ? $json_data['total_charges_comp'] + $json_data['C_otherexpns'] : $json_data['total_charges_comp'];
  $extra_exp_rows[$extra_exp_index]['nonvat'] =0;
  $extra_exp_rows[$extra_exp_index]['vat'] = $json_data['cur_vat'] * $json_data['total_charges_comp'];
  $extra_exp_rows[$extra_exp_index]['amoun'] = $extra_exp_rows[$extra_exp_index]['netamount'] + $extra_exp_rows[$extra_exp_index]['vat'];
  
  $extra_exp_index++;
}


// $get_bad_debt=$acttObj->read_all("*","credit_notes"," dated between '$search_2' and '$search_3'");
// $query="SELECT round(IFNULL(SUM(interpreter.total_charges_comp),0),2) as bad_debt_amount, round(sum(IFNULL(interpreter.total_charges_comp,0)*interpreter.cur_vat),2) as vat_bad_debt,'Bad Debt' as type,'face 2 face' as detail,orgName,invoiceNo,assignDate FROM interpreter WHERE interpreter.disposed_of='1' and ROUND(interpreter.total_charges_comp,2)>0 and interpreter.assignDate between '$search_2' and '$search_3' UNION ALL SELECT round(IFNULL(SUM(telephone.total_charges_comp),0),2) as bad_debt_amount,round(sum(IFNULL(telephone.total_charges_comp,0)*telephone.cur_vat),2) as vat_bad_debt,'Bad Debt' as type,'telephone' as detail,orgName,invoiceNo,assignDate FROM telephone WHERE telephone.disposed_of='1' and ROUND(telephone.total_charges_comp,2)>0  and telephone.assignDate between '$search_2' and '$search_3' UNION ALL SELECT round(IFNULL(SUM(translation.total_charges_comp),0),2) as bad_debt_amount,round(sum(IFNULL(translation.total_charges_comp,0)*translation.cur_vat),2) as vat_bad_debt,'Bad Debt' as type,'translation' as detail,orgName,invoiceNo,asignDate FROM translation WHERE translation.disposed_of='1' and ROUND(translation.total_charges_comp,2)>0 and translation.asignDate between '$search_2' and '$search_3'";
$query="SELECT ROUND(interpreter.total_charges_comp,2) as bad_debt_amount, ROUND((interpreter.total_charges_comp*interpreter.cur_vat),2) as vat_bad_debt,'face 2 face' as detail,assignDate as billdate,C_otherexpns as others,orgName,invoiceNo FROM interpreter WHERE interpreter.disposed_of='1' and ROUND(interpreter.total_charges_comp,2)>0 and interpreter.assignDate between '$search_2' and '$search_3' UNION ALL SELECT ROUND(telephone.total_charges_comp,2) as bad_debt_amount,ROUND((telephone.total_charges_comp*telephone.cur_vat),2) as vat_bad_debt,'telephone' as detail,assignDate as billdate,C_otherCharges as others,orgName,invoiceNo FROM telephone WHERE telephone.disposed_of='1' and ROUND(telephone.total_charges_comp,2)>0  and telephone.assignDate between '$search_2' and '$search_3' UNION ALL SELECT ROUND(translation.total_charges_comp,2) as bad_debt_amount,ROUND((translation.total_charges_comp*translation.cur_vat),2) as vat_bad_debt,'translation' as detail,asignDate as billdate,C_otherCharg as others,orgName,invoiceNo FROM translation WHERE translation.disposed_of='1' and ROUND(translation.total_charges_comp,2)>0 and translation.asignDate between '$search_2' and '$search_3'";
$get_bad_debt=mysqli_query($con,$query);
while($bd_row = mysqli_fetch_assoc($get_bad_debt)){
  $extra_exp_rows[$extra_exp_index]['exp_title'] =  'Bad Debt';
  $extra_exp_rows[$extra_exp_index]['billDate'] = $bd_row['billdate'];
  $extra_exp_rows[$extra_exp_index]['details'] = $bd_row['detail'];
  $extra_exp_rows[$extra_exp_index]['comp'] = $bd_row['orgName'];
  $extra_exp_rows[$extra_exp_index]['voucher'] = $bd_row['invoiceNo'];
  $extra_exp_rows[$extra_exp_index]['netamount'] = $bd_row['detail'] == 'face 2 face' ? $bd_row['bad_debt_amount'] + $bd_row['others'] : $bd_row['bad_debt_amount'];
  $extra_exp_rows[$extra_exp_index]['nonvat'] =0;
  $extra_exp_rows[$extra_exp_index]['vat'] = $bd_row['vat_bad_debt'];
  $extra_exp_rows[$extra_exp_index]['amoun'] = $extra_exp_rows[$extra_exp_index]['netamount']+$extra_exp_rows[$extra_exp_index]['vat'];
  
  $extra_exp_index++;
}

if($mng_check==1){
  $exp_titles2=$acttObj->read_specific('GROUP_CONCAT(title) as exp_titles','expence_list','id IN (16,17,35,36)');
  $query2="SELECT $table.*,expence_list.title as exp_title FROM expence,expence_list where $table.type_id=expence_list.id AND expence.type_id IN (16,17,35,36) and $table.billDate between '$search_2' and '$search_3' and $table.deleted_flag=0 order by expence_list.title";
  $result2 = mysqli_query($con, $query2);
}


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
$tbl = "
<style>
table {border-collapse: collapse; width:100%;word-wrap: break-word;}
th {border: 1px solid #999; padding: 0.5rem;text-align: left;background-color:#039; color:#FFF;font-weight:bold;word-wrap: break-word;}
td {border: 1px solid #999; padding: 0.5rem;text-align: left;word-wrap: break-word;}
</style>";

$tbl.="
<div>
<h2 align='center'><u>LSUK Expenses Report</u></h2>
<p align='right'>Report  Date: ".$misc->sys_date()."<br />
  Date  Range: Date From [".$misc->dated($search_2)."] Date To [".$misc->dated($search_3)."]</p>
</div>
<p>Expense(s) Selected</p>
<table class='aa' border='1' cellspacing='0' cellpadding='0' style='width:250px'>
  <tr>
    <td width='200' valign='top'>".$exp_titles['exp_titles']."</td>
  </tr>
</table><br/><br/>";
$tbl.="
<table>
<thead>
<tr>
 	<th style='width:35px;'>Sr.No</th>
    <th>Bill Date</th>
    <th>Type</th>
    <th>Details</th>
    <th>Voucher No.</th>
    <th>Company</th>
    <th>Net Amount</th>
    <th>VAT</th>
    <th>Non VAT</th>
    <th>Total Amount</th>
 </tr>

</thead>";

$runcompany="";
$nowcompany="";
$mapCoTotals=array();
ZeroCompTotal($mapCoTotals);

$loop=0;


while($row = mysqli_fetch_assoc($result))
{
  $amoun=$row["amoun"] + $amoun;$netamount=$row["netamount"] + $netamount;$vat=$row["vat"] + $vat;
  $nonvat=$row["nonvat"] + $nonvat;

	$nowcompany=$row["title"];
	if ($loop==0)
	OrgOutput::WriteTR($nowcompany,$runcompany,$tbl);
  
  $loop++;
  if ($runcompany!=$nowcompany)
  {
	ShowCompTotal($mapCoTotals,$tbl);
	ZeroCompTotal($mapCoTotals);
  }
  OrgOutput::WriteTR($nowcompany,$runcompany,$tbl);
  
  UpdateCompTotal($mapCoTotals,$row,$i);


$tbl.="
    <tr>
      	<td style='width:35px;'>".$i."</td>
		<td>".$misc->dated($row["billDate"])."</td>
		<td>".$row["exp_title"]."</td>
		<td>".$row["details"]."</td>
		<td>".$row["voucher"]."</td>
		<td>".$row["comp"]."</td>
		<td>".$row["netamount"]."</td>
		<td>".$row["vat"]."</td>
		<td>".$row["nonvat"]."</td>
		<td>".$row["amoun"]."</td>
    </tr>";
 $i++;
}
// ShowExtraExp($mapCoTotals,$extra_exp_rows,$i,$tbl,$amoun,$netamount,$vat,$nonvat);
if ($loop!=0)
  ShowCompTotal($mapCoTotals,$tbl);
// echo "real: $netamount <br> $vat <br> $amoun<br>";
$tbl.="
	  
<tr style='font-weight:bold;'>
<td></td>
<td>Total</td>
<td></td>
<td></td>
<td></td>
<td></td>
<td>".$misc->numberFormat_fun($netamount)."</td>
<td>".$misc->numberFormat_fun($vat)."</td>
<td>".$misc->numberFormat_fun($nonvat)."</td>
<td>".$misc->numberFormat_fun($amoun)."</td>
</tr>
</table>";

if($mng_check==1){
$tbl.='<br pagebreak="true"/>';


$tbl.="
<p></p>
<div>
<h1 align='center'><u>Exluded Expenses / Management Figures</u></h1>
</div>
<p>Exluded Expense(s) are Below:</p>
<table class='aa' border='1' cellspacing='0' cellpadding='0' style='width:250px'>
  <tr>
    <td width='200' valign='top'>".$exp_titles2['exp_titles']."</td>
  </tr>
</table><br/><br/>";
$tbl.="
<table>
<thead>
<tr>
 	<th style='width:35px;'>Sr.No</th>
    <th>Bill Date</th>
    <th>Type</th>
    <th>Details</th>
    <th>Voucher No.</th>
    <th>Company</th>
    <th>Net Amount</th>
    <th>VAT</th>
    <th>Non VAT</th>
    <th>Total Amount</th>
 </tr>

</thead>";

$runcompany="";
$nowcompany="";
$mapCoTotals=array();
ZeroCompTotal($mapCoTotals);
$i=1;
$loop=0;


while($row2 = mysqli_fetch_assoc($result2))
{
  $amoun=$row2["amoun"] + $amoun;$netamount=$row2["netamount"] + $netamount;$vat=$row2["vat"] + $vat;
  $nonvat=$row2["nonvat"] + $nonvat;

	$nowcompany=$row2["title"];
	if ($loop==0)
	OrgOutput::WriteTR($nowcompany,$runcompany,$tbl);
  
  $loop++;
  if ($runcompany!=$nowcompany)
  {
	ShowCompTotal($mapCoTotals,$tbl);
	ZeroCompTotal($mapCoTotals);
  }
  OrgOutput::WriteTR($nowcompany,$runcompany,$tbl);
  
  UpdateCompTotal($mapCoTotals,$row2,$i);



$tbl.="
    <tr>
      	<td style='width:35px;'>".$i."</td>
		<td>".$misc->dated($row2["billDate"])."</td>
		<td>".$row2["exp_title"]."</td>
    <td>".$row2["details"]."</td>
		<td>".$row2["voucher"]."</td>
		<td>".$row2["comp"]."</td>
		<td>".$row2["netamount"]."</td>
		<td>".$row2["vat"]."</td>
		<td>".$row2["nonvat"]."</td>
		<td>".$row2["amoun"]."</td>
    </tr>";
 $i++;
}

if ($loop!=0)
  ShowCompTotal($mapCoTotals,$tbl);

$tbl.="
	  
<tr style='font-weight:bold;'>
<td></td>
<td>Total</td>
<td></td>
<td></td>
<td></td>
<td></td>
<td>".$misc->numberFormat_fun($netamount)."</td>
<td>".$misc->numberFormat_fun($vat)."</td>
<td>".$misc->numberFormat_fun($nonvat)."</td>
<td>".$misc->numberFormat_fun($amoun)."</td>
</tr>
</table>";
}





$pdf->writeHTML($tbl, true, false, false, false, '');


// -----------------------------------------------------------------------------

//Close and output PDF document
list($a,$b)=explode('.',basename(__FILE__));
$pdf->Output($a.'.pdf', 'I');
//============================================================+
// END OF FILE
//==========================================================EXCEL FORMAT=========================================================+

function ZeroCompTotal(&$map)
{
	$map["netamount"]=0;
	$map["vat"]=0;
	$map["nonvat"]=0;
	$map["amoun"]=0;
}

function UpdateCompTotal(&$map,&$row,$i)
{
  // echo $map["amoun"]."<br>";
	$map["netamount"]+=$row["netamount"];
	$map["vat"]+=$row["vat"];
	$map["nonvat"]+=$row["nonvat"];
	$map["amoun"]+=$row["amoun"];

}

function UpdateCompTotalExtra(&$map,&$ext_row,$i)
{
	$map["netamount"]+=$ext_row["netamount"];
	// $map["vat"]+=$ext_row["vat"];
	$map["nonvat"]+=$ext_row["nonvat"];
	// $map["amoun"]+=$ext_row["amoun"];
	$map["amoun"]+=$ext_row["netamount"];

}

function ShowExtraExp(&$mapCoTotals,$extra_exp_rows,$i,&$tbl,&$amoun,&$netamount,&$vat,&$nonvat)
{
  $counter = $i;
	global $misc;	
  for($i=0;$i<count($extra_exp_rows);$i++){
    $ext_index = $extra_exp_rows[$i];
    $tbl.="
    <tr>
      	<td style='width:35px;'>".$counter."</td>
		<td>".$extra_exp_rows[$i]["billDate"]."</td>
		<td>".$extra_exp_rows[$i]["exp_title"]."</td>
    <td>".$extra_exp_rows[$i]["details"]."</td>
		<td>".$extra_exp_rows[$i]["voucher"]."</td>
		<td>".$extra_exp_rows[$i]["comp"]."</td>
		<td>".$extra_exp_rows[$i]["netamount"]."</td>
		<td>".$extra_exp_rows[$i]["vat"]."</td>
		<td>".$extra_exp_rows[$i]["nonvat"]."</td>
		<td>".$extra_exp_rows[$i]["netamount"]."</td>
    </tr>";
    UpdateCompTotalExtra($mapCoTotals,$ext_index,$counter);
    $amoun=$ext_index["netamount"] + $amoun;$netamount=$ext_index["netamount"] + $netamount;
    // $vat=$ext_index["vat"] + $vat;
    $nonvat=$ext_index["nonvat"] + $nonvat;
    $counter++;
  }
  // echo "changed: $netamount <br> $vat <br> $amoun<br>";
  
	
}

function ShowCompTotal(&$map,&$tbl)
{
	global $misc;	

	$tbl.="
	<tr style='font-weight:bold;'>
  <td></td>
  <td>Exp. Total:</td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td>".$misc->numberFormat_fun($map["netamount"])."</td>
  <td>".$misc->numberFormat_fun($map["vat"])."</td>
  <td>".$misc->numberFormat_fun($map["nonvat"])."</td>
  <td>".$misc->numberFormat_fun($map["amoun"])."</td>
  </tr>";
}
