<?php include '../db.php';include_once ('../class.php'); $search_1=@$_GET['search_1'];$search_2=@$_GET['search_2'];$search_3=@$_GET['search_3'];
$i=1;$table='interpreter';
if(!empty($search_1)){
$query="SELECT *, 0.2*total_charges_comp as vat FROM interpreter
	   where assignDate between '$search_2' and '$search_3' and interpreter_reg.name='$search_1'";}
	   else{$query="SELECT *, 0.2*total_charges_comp as vat FROM interpreter
	   where assignDate between '$search_2' and '$search_3'";}
$result = mysqli_query($con, $query);
require('WriteHTML.php');

$pdf=new PDF_HTML();

$pdf->AliasNbPages();
$pdf->SetAutoPageBreak(true, 15);

$pdf->AddPage();
$pdf->Image('logo.png',18,12,23);
$pdf->SetFont('Arial','B',14);
$pdf->WriteHTML('<para><h1>Language Services Uk (LSUK)</h1><br>
<u>www.lsuk.org</u></para><br><br><br><br><u>Monthly Job Report</u>');

$pdf->SetFont('Arial','B',7); 
$htmlTable='<table>';
$htmlTable .='<tr>

    <td>#</td>
    <td>Invoice No.</td>
    <td>Job Date</td>
    <td>Interpreter Name</td>
    <td>Language</td>
    <td>Amount Paid to the Interrpeter</td>
    <td>Language</td>
    <td>Amount Paid to the Interrpeter</td>
    <td>Interpreter Payment Date</td>';


while($row = mysqli_fetch_assoc($result)){


$htmlTable .='<tr>';
$htmlTable .='<td>'.$i.'</td>';
$htmlTable .='<td>'.$row["invoiceNo"].'</td>';
$htmlTable .='<td>'.date_format(date_create($row["assignDate"]), 'd-m-Y').'</td>';
$htmlTable .='<td>'.$row["name"].'</td>';
$htmlTable .='<td>'.$row["source"].'</td>';
$htmlTable .='<td>'.$row["total_charges_interp"].'</td>';
$htmlTable .='<td>'.$row["source"].'</td>';
$htmlTable .='<td>'.$row["total_charges_interp"].'</td>';

$htmlTable .='<td>'.date_format(date_create($row["paid_date"]), 'd-m-Y').'</td>
</tr>';
$i++;}
$htmlTable .='</TABLE>';
$pdf->WriteHTML2("<br><br><br>$htmlTable");
$pdf->SetFont('Arial','B',6);
$pdf->Output(); 
?>