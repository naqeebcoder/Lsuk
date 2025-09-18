<?php include '../db.php';include_once ('../class.php'); $search_2=@$_GET['search_2'];$search_3=@$_GET['search_3'];
$i=1;$total_interp=0;$total_comp=0;$total_otherCharg=0;$gross=0;
$query="SELECT invoiceNo,orgName,intrpName,total_charges_comp,total_charges_interp,interpreter_reg.name,C_otherCost as C_otherCharg FROM interpreter 
inner join interpreter_reg on interpreter.intrpName = interpreter_reg.id
where assignDate between '$search_2' and '$search_3'
union
SELECT invoiceNo,orgName,intrpName,total_charges_comp,total_charges_interp,interpreter_reg.name,C_otherCharges as C_otherCharg FROM telephone 
inner join interpreter_reg on telephone.intrpName = interpreter_reg.id
where assignDate between '$search_2' and '$search_3'
union
SELECT invoiceNo,orgName,intrpName,total_charges_comp,total_charges_interp,interpreter_reg.name,C_otherCharg FROM translation
inner join interpreter_reg on translation.intrpName = interpreter_reg.id
where asignDate between '$search_2' and '$search_3'
";

$result = mysqli_query($con, $query);
while($row = mysqli_fetch_assoc($result)){$i=$i + 1;$total_interp=$row["total_charges_interp"] + $total_interp;$total_comp=$row["total_charges_comp"] + $total_comp;$total_otherCharg=$row["C_otherCharg"] + $total_otherCharg;} $gross=$total_comp + $total_otherCharg + $total_comp * 0.2;

$query_expnce="SELECT SUM(amoun) as amoun FROM expence where billDate between '$search_2' and '$search_3'";$result_expnce = mysqli_query($con, $query_expnce);while($row_expnce = mysqli_fetch_assoc($result_expnce)){$total_expnce=$row_expnce['amoun'];}

require('WriteHTML.php');

$pdf=new PDF_HTML();

$pdf->AliasNbPages();
$pdf->SetAutoPageBreak(true, 15);

$pdf->AddPage();
$pdf->Image('logo.png',18,12,23);
$pdf->SetFont('Arial','B',14);
$pdf->WriteHTML('<para><h1>Language Services Uk (LSUK)</h1><br>
<u>www.lsuk.org</u></para><br><br><br><br><u>Profit and Loss Summary</u>');

$pdf->SetFont('Arial','B',7); 
$htmlTable='<table>';
$htmlTable .='<tr>
    <td>Total Registered invoices</td>';
$htmlTable .='<td>'.$i.'</td>
</tr>';
$htmlTable .='<tr>
    <td>Total of the Net Value of the invoices</td>';
$htmlTable .='<td>'.round($total_comp).'</td>
</tr>';

$htmlTable .='<tr>
    <td>Total of VAT Collected on the invoices</td>';
$htmlTable .='<td>'.round($total_comp * 0.2).'</td>
</tr>';

$htmlTable .='<tr>
    <td>Total of Non-Vat able charges</td>';
$htmlTable .='<td>'.round($total_otherCharg).'</td>
</tr>';

$htmlTable .='<tr>
    <td>Total of Gross total of Invoices</td>';
$htmlTable .='<td>'.round($gross).'</td>
</tr>';

$htmlTable .='<tr>
    <td>Total of Payments made to the Interpreters</td>';
$htmlTable .='<td>'.round($total_interp).'</td>
</tr>';

$htmlTable .='<tr>
    <td>Total of Company Expenses</td>';
$htmlTable .='<td>'.round($total_expnce).'</td>
</tr>';


$htmlTable .='</TABLE>';
$pdf->WriteHTML2("<br><br><br>$htmlTable");
$pdf->SetFont('Arial','B',6);
$pdf->Output(); 
?>