<?php include '../db.php';include_once ('../class.php'); $search_1=@$_GET['search_1'];$search_2=@$_GET['search_2'];$search_3=@$_GET['search_3'];
$i=1;$table='telephone';
if(!empty($search_1)){
$query="SELECT *, total_charges_comp * 0.2 as vat, interpreter_reg.name  FROM $table
	   inner join interpreter_reg on $table.intrpName = interpreter_reg.id
	   where $table.deleted_flag = 0 and $table.order_cancel_flag=0 and assignDate between '$search_2' and '$search_3' and (total_charges_comp <> rAmount or rAmount is null or rAmount =0) and interpreter_reg.name='$search_1'";}
	   else{$query="SELECT *, total_charges_comp * 0.2 as vat, interpreter_reg.name  FROM $table
	   inner join interpreter_reg on $table.intrpName = interpreter_reg.id
	   where $table.deleted_flag = 0 and $table.order_cancel_flag=0 and assignDate between '$search_2' and '$search_3' and (total_charges_comp <> rAmount or rAmount is null or rAmount =0)";}
$result = mysqli_query($con, $query);
require('WriteHTML.php');

$pdf=new PDF_HTML();

$pdf->AliasNbPages();
$pdf->SetAutoPageBreak(true, 15);

$pdf->AddPage();
$pdf->Image('logo.png',18,12,23);
$pdf->SetFont('Arial','B',14);
$pdf->WriteHTML('<para><h1>            Language Services UK Limited<br>         Translation and Interpreting Service</h1><br><u>Suite 2 Davis House Lodge Causeway Trading Estate<br>Lodge Causeway - Fishponds Bristol BS163JB</u></para><br><br><br><br><u>Interpreter Pending Invoices Report (12)<br><br>Date: '.$misc->sys_date().'</u>');

$pdf->SetFont('Arial','B',7); 
$htmlTable='<table>';
$htmlTable .='<tr>

    <td>#</td>
	<td>Invoice Number</td>
    <td>Job Date</td>
    <td>Language</td>
    <td>Client Name</td>
    <td>Interpreter Name</td>
    <td>Witdout VAT</td>
    <td>VAT</td>
    <td>Non-VAT Costs</td>
    <td>Invoice Total</td>';
while($row = mysqli_fetch_assoc($result)){
$htmlTable .='<tr>';
$htmlTable .='<td>'.$i.'</td>';
$htmlTable .='<td>'.$row["invoiceNo"].'</td>';
$htmlTable .='<td>'.date_format(date_create($row["assignDate"]), 'd-m-Y').'</td>';
$htmlTable .='<td>'.$row["source"].'</td>';
$htmlTable .='<td>'.$row["orgName"].'</td>';
$htmlTable .='<td>'.$row["name"].'</td>';
$htmlTable .='<td>'.$row["total_charges_comp"].'</td>';
$htmlTable .='<td>'.$vat=round($row["vat"],2).'</td>';
$htmlTable .='<td>'.$row["C_otherCharges"].'</td>';

$htmlTable .='<td>'.$row["total_charges_comp"]+$vat.'</td>
</tr>';
$i++;}
$htmlTable .='</TABLE>';
$pdf->WriteHTML2("<br><br><br>$htmlTable");
$pdf->SetFont('Arial','B',6);
$pdf->Output(); 
?>