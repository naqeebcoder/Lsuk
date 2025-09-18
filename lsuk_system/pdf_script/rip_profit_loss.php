<?php include '../db.php';include_once ('../class.php'); $search_2=@$_GET['search_2'];$search_3=@$_GET['search_3'];
$i=1;$total_interp=0;$total_comp=0;$total_non_vat=0;$total_vat=0;$total_all=0;$total_profit=0;
$query="SELECT invoiceNo,invoic_date,orgName,intrpName,source,assignDate,paid_interp,C_otherexpns, total_charges_comp,total_charges_interp,interpreter_reg.name FROM interpreter 
inner join interpreter_reg on interpreter.intrpName = interpreter_reg.id
where interpreter.deleted_flag = 0 and interpreter.order_cancel_flag=0 and assignDate between '$search_2' and '$search_3'
union
SELECT invoiceNo,invoic_date,orgName,intrpName,source,assignDate,paid_interp,C_otherCharges as C_otherexpns,total_charges_comp,total_charges_interp,interpreter_reg.name FROM telephone 
inner join interpreter_reg on telephone.intrpName = interpreter_reg.id
where telephone.deleted_flag = 0 and telephone.order_cancel_flag=0 and assignDate between '$search_2' and '$search_3'
union
SELECT invoiceNo,invoic_date,orgName,intrpName,source,asignDate as assignDate ,paid_interp,C_otherCharg as C_otherexpns,total_charges_comp,total_charges_interp,interpreter_reg.name FROM translation
inner join interpreter_reg on translation.intrpName = interpreter_reg.id
where translation.deleted_flag = 0 and translation.order_cancel_flag=0 and asignDate between '$search_2' and '$search_3'
order by invoiceNo";

$result = mysqli_query($con, $query);
require('WriteHTML.php');

$pdf=new PDF_HTML();

$pdf->AliasNbPages();
$pdf->SetAutoPageBreak(true, 15);

$pdf->AddPage();
$pdf->Image('logo.png',18,12,23);
$pdf->SetFont('Arial','B',14);
$pdf->WriteHTML('<para><h1>            Language Services UK Limited<br>         Translation and Interpreting Service</h1><br><u>Suite 2 Davis House Lodge Causeway Trading Estate<br>Lodge Causeway - Fishponds Bristol BS163JB</u></para><br><br><br><br><u>Profit and Loss Report ('.$misc->dated($search_2). ' to ' .$misc->dated($search_3).')<br><br>Date: '.$misc->sys_date().'</u>');

$pdf->SetFont('Arial','B',7); 
$htmlTable='<table>';
$htmlTable .='<tr>

    <td>#</td>
	<td>Invoice Number</td>
	<td>Invoice Date</td>
    <td>Assignment Date</td>
    <td>Client Name</td>
    <td>Interpreter Name</td>
    <td>Language</td>
    <td>Amount Paid to Interpreter</td>
    <td>Amount Invoiced to the Client (Net)</td>
    <td>Non Vat able</td>
    <td>VAT</td>
    <td>Total</td>
    <td>Profit or Loss</td>';
while($row = mysqli_fetch_assoc($result)){
$htmlTable .='<tr>';
$htmlTable .='<td>'.$i.'</td>';
$htmlTable .='<td>'.$row["invoiceNo"].'</td>';
$htmlTable .='<td>'.$row["invoic_date"].'</td>';
$htmlTable .='<td>'.$row["assignDate"].'</td>';
$htmlTable .='<td>'.$row["orgName"].'</td>';
$htmlTable .='<td>'.$row["name"].'</td>';
$htmlTable .='<td>'.$row["source"].'</td>';
$htmlTable .='<td>'.round($row["total_charges_interp"]).'</td>';
$htmlTable .='<td>'.$row["total_charges_comp"].'</td>';
$htmlTable .='<td>'.$row["C_otherexpns"].'</td>';
$htmlTable .='<td>'.round($row["total_charges_comp"]*.2).'</td>';
$htmlTable .='<td>'.round($row["total_charges_comp"] + $row["C_otherexpns"]).'</td>';
$htmlTable .='<td>'.round($row["total_charges_comp"] + $row["C_otherexpns"] - $row["total_charges_interp"]).'</td>
</tr>';
$i++;$total_interp=round($row["total_charges_interp"] + $total_interp);$total_comp=round($row["total_charges_comp"] + $total_comp);$total_non_vat=$row["C_otherexpns"]+$total_non_vat;$total_vat=round($row["total_charges_comp"]*.2+$total_vat);$total_all=round($row["total_charges_comp"] + $row["C_otherexpns"])+$total_all;$total_profit=round($row["total_charges_comp"] + $row["C_otherexpns"] - $row["total_charges_interp"]) + $total_profit;}

$htmlTable .='<tr>';
$htmlTable .='<td></td>';
$htmlTable .='<td></td>';
$htmlTable .='<td></td>';
$htmlTable .='<td></td>';
$htmlTable .='<td></td>';
$htmlTable .='<td></td>';
$htmlTable .='<td>TOTAL</td>';
$htmlTable .='<td>'.number_format($total_interp, 2).'</td>';
$htmlTable .='<td>'.number_format($total_comp, 2).'</td>';
$htmlTable .='<td>'.number_format($total_non_vat, 2).'</td>';
$htmlTable .='<td>'.number_format($total_vat, 2).'</td>';
$htmlTable .='<td>'.number_format($total_all, 2).'</td>';
$htmlTable .='<td>'.number_format($total_profit, 2).'</td>
</tr>';
$htmlTable .='</TABLE>';
$pdf->WriteHTML2("<br><br><br>$htmlTable");
$pdf->SetFont('Arial','B',6);
$pdf->Output(); 
?>