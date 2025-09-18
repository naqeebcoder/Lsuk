<?php include '../../db.php';
include_once ('../../class.php'); 
$excel=@$_GET['excel'];
$search_1=@$_GET['search_1']; 
$search_2=@$_GET['search_2']; 
$search_3=@$_GET['search_3'];
$i=1;
$table='expence';
$amoun=0;
$netamount=0;
$vat=0;
$nonvat=0;

// $expence_vat=$acttObj->read_specific("round(sum($table.vat),2) as expence_vat","$table","$table.billDate between '$search_2' and '$search_3' and $table.deleted_flag=0");
$expence_vat=$acttObj->read_specific("ROUND(IFNULL(SUM(CASE WHEN ROUND(vat,2)>0 THEN vat ELSE 0 END),0),2) as expence_vat,ROUND(IFNULL(SUM(CASE WHEN type_id = 32 THEN -vat ELSE 0 END),0),2) as vat_rental_income","expence"," (billDate BETWEEN '$search_2' and '$search_3') and deleted_flag=0");

$int_vat=$acttObj->read_specific("sum(int_vat) as int_vat","(SELECT round(sum((interpreter.chargInterp+interpreter.chargeTravel+interpreter.chargeTravelTime)*interpreter.int_vat),2) as int_vat FROM interpreter","interpreter.assignDate between '$search_2' and '$search_3' and interpreter.deleted_flag=0 and interpreter.order_cancel_flag=0
UNION ALL
SELECT round(sum((telephone.chargInterp)*telephone.int_vat),2) as int_vat FROM telephone where telephone.assignDate between '$search_2' and '$search_3' and telephone.deleted_flag=0 and telephone.order_cancel_flag=0
UNION ALL
SELECT round(sum((translation.numberUnit*translation.rpU)*translation.int_vat),2) as int_vat FROM translation where translation.asignDate between '$search_2' and '$search_3' and translation.deleted_flag=0 and translation.order_cancel_flag=0) as grp");

$comp_vat=$acttObj->read_specific("sum(comp_vat) as comp_vat","(SELECT round(IFNULL(sum(interpreter.total_charges_comp*interpreter.cur_vat),0),2) as comp_vat FROM interpreter","interpreter.assignDate between '$search_2' and '$search_3' and interpreter.deleted_flag=0 and interpreter.order_cancel_flag=0 UNION ALL SELECT round(IFNULL(sum(telephone.total_charges_comp*telephone.cur_vat),0),2) as comp_vat FROM telephone where telephone.assignDate between '$search_2' and '$search_3' and telephone.deleted_flag=0 and telephone.order_cancel_flag=0 UNION ALL SELECT round(IFNULL(sum(translation.total_charges_comp*translation.cur_vat),0),2) as comp_vat FROM translation where translation.asignDate between '$search_2' and '$search_3' and translation.deleted_flag=0 and translation.order_cancel_flag=0) as grp");

$credit_notes=$acttObj->read_specific("count(*) as counter","credit_notes"," dated between '$search_2' and '$search_3' ")['counter'];
$total_credit_note_vat = $total_credit_note_charges = $total_credit_note_value = 0;
$get_credit_notes = $acttObj->read_all("order_type,data", "credit_notes", " dated between '$search_2' and '$search_3' ");
while($row_credit_note = $get_credit_notes->fetch_assoc()){
    $json_data=json_decode($row_credit_note['data'], true);
    $credit_note_vat = $json_data['cur_vat'] * $json_data['total_charges_comp'];
    $credit_note_charges = $json_data['order_type'] == 'f2f' ? $json_data['total_charges_comp'] + $json_data['C_otherexpns'] : $json_data['total_charges_comp'];
    $credit_note_value = $credit_note_charges + $credit_note_vat;
    $total_credit_note_vat += $credit_note_vat;
    $total_credit_note_charges += $credit_note_charges;
    $total_credit_note_value += $credit_note_value;
}

$get_bad_debtQ=mysqli_query($con,"SELECT SUM(vat_bad_debt) as net_vat_bad_debt FROM (SELECT round(sum(IFNULL(interpreter.total_charges_comp,0)*interpreter.cur_vat),2) as vat_bad_debt FROM interpreter WHERE interpreter.disposed_of='1' and ROUND(interpreter.total_charges_comp,2)>0 and interpreter.assignDate between '$search_2' and '$search_3' UNION ALL SELECT round(sum(IFNULL(telephone.total_charges_comp,0)*telephone.cur_vat),2) as vat_bad_debt FROM telephone WHERE telephone.disposed_of='1' and ROUND(telephone.total_charges_comp,2)>0  and telephone.assignDate between '$search_2' and '$search_3' UNION ALL SELECT round(sum(IFNULL(translation.total_charges_comp,0)*translation.cur_vat),2) as vat_bad_debt FROM translation WHERE translation.disposed_of='1' and ROUND(translation.total_charges_comp,2)>0 and translation.asignDate between '$search_2' and '$search_3') as grp");
$get_bad_debt = mysqli_fetch_assoc($get_bad_debtQ);

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
<h2 align="center"><u>LSUK Overall VAT Report</u></h2>
<p align="right">Report  Date: {$misc->sys_date()}<br />
  Date  Range:  Date From [{$misc->dated($search_2)}] Date To [{$misc->dated($search_3)}]</p>
</div>
<p>VAT No(s) Selected</p>
<table class="aa" border="1" cellspacing="0" cellpadding="0" style="width:250px">
  <tr>
    <td width="200" valign="top">{$search_1}</td>
  </tr>
</table><br/><br/>

EOD;
$tbl.=<<<EOD
<table>
<thead>
<tr>
 	<th style="width:35px;">Sr.No</th>
    <th>VAT Paid on Expenses</th>
    <th>VAT Paid to Interpreters</th>
    <th>VAT Collected from Sales</th>
    <th>VAT Claimed on Rental income</th>
    <th>VAT reversal on Credit Notes & Bad Debt</th>
    <th>Total VAT Payable</th>
 </tr>
</thead>
<tbody>
EOD;
$creditNote_badDebt_vat = $total_credit_note_vat+$get_bad_debt['net_vat_bad_debt'];
$net_paid=$expence_vat['expence_vat']+$int_vat['int_vat'];
$total_get=$comp_vat['comp_vat']+$expence_vat['vat_rental_income']-$creditNote_badDebt_vat-$net_paid;
$tbl.=<<<EOD
    <tr>
      	<td style="width:35px;">{$i}</td>
		<td>{$expence_vat['expence_vat']}</td>
		<td>{$int_vat['int_vat']}</td>
		<td>{$comp_vat['comp_vat']}</td>

    <td>{$expence_vat['vat_rental_income']}</td>
    <td>{$creditNote_badDebt_vat}</td>

		<td>{$total_get}</td>
    </tr>
EOD;

$tbl.=<<<EOD
<tbody>
</table>

EOD;
$tbl.=<<<EOD
EOD;

$pdf->writeHTML($tbl, true, false, false, false, '');


// -----------------------------------------------------------------------------

//Close and output PDF document
list($a,$b)=explode('.',basename(__FILE__));
$pdf->Output($a.'.pdf', 'I');

