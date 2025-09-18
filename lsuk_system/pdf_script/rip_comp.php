<?php include '../db.php';include_once ('../class.php'); $search_1=@$_GET['search_1'];$search_2=@$_GET['search_2'];$search_3=@$_GET['search_3'];$i=1;

$query="(SELECT interpreter.assignDate,interpreter.paid_date,interpreter.source,interpreter.orgName,interpreter.invoiceNo,interpreter.C_otherexpns as C_otherCost,interpreter.rDate,interpreter.card_payment,interpreter.cheque,interpreter.bacs, interpreter.total_charges_comp ,interpreter.total_charges_interp , interpreter_reg.name  FROM interpreter
	   inner join interpreter_reg on interpreter.intrpName = interpreter_reg.id
	   where interpreter.intrp_salary_comit=1 and interpreter.orgName like '$search_1%' and interpreter.assignDate between '$search_2' and '$search_3')
	   
	   union
	   
	   (SELECT telephone.assignDate,telephone.paid_date,telephone.source,telephone.orgName,telephone.invoiceNo,telephone.C_otherCharges as C_otherCost,telephone.rDate,telephone.card_payment,telephone.cheque,telephone.bacs, telephone.total_charges_comp , telephone.total_charges_interp ,interpreter_reg.name  FROM telephone
	   inner join interpreter_reg on telephone.intrpName = interpreter_reg.id
	   where telephone.intrp_salary_comit=1 and telephone.orgName like '$search_1%' and telephone.assignDate between '$search_2' and '$search_3' )
	   
	   union
	   
	   (SELECT translation.asignDate as assignDate,translation.paid_date,translation.source,translation.orgName,translation.invoiceNo,translation.C_otherCharg as C_otherCost,translation.rDate,translation.card_payment,translation.cheque,translation.bacs, translation.total_charges_comp , translation.total_charges_interp ,interpreter_reg.name  FROM translation
	   inner join interpreter_reg on translation.intrpName = interpreter_reg.id
	   where translation.intrp_salary_comit=1 and translation.orgName like '$search_1%' and translation.asignDate between '$search_2' and '$search_3')
	   
	   
	    order by assignDate ASC

   
	   
	   
	   
	   
	   ";
$result = mysqli_query($con, $query);
require('WriteHTML.php');

$pdf=new PDF_HTML();

$pdf->AliasNbPages();
$pdf->SetAutoPageBreak(true, 15);

$pdf->AddPage();
$pdf->Image('logo.png',18,12,23);
$pdf->SetFont('Arial','B',14);
$pdf->WriteHTML('<para><h1>            Language Services UK Limited<br>         Translation and Interpreting Service</h1><br><u>Suite 3 Davis House Lodge Causeway Trading Estate<br>Lodge Causeway - Fishponds Bristol BS163JB</u></para><br><br><br><br><u>Report for Company<br><br>Date: '.$misc->sys_date().'</u>');

$pdf->SetFont('Arial','B',7); 
$htmlTable='<table>';
$htmlTable .='<tr>

    <td>#</td>
    <td>Job Date</td>
    <td>Language</td>
    <td>Client Name</td>
    <td>Invoice Number</td>
    <td>Interpreter Name</td>
    <td>Paid to Interrpeter</td>
    <td>Payment Date</td>
    <td>Witdout VAT</td>
    <td>VAT</td>
    <td>Non-VAT Costs</td>
    <td>Invoice Total</td>
    <td>Paid Date</td>
    <td>Payment Method</td> ';


while($row = mysqli_fetch_assoc($result)){


$htmlTable .='<tr>';
$htmlTable .='<td>'.$i.'</td>';
$htmlTable .='<td>'.date_format(date_create($row["assignDate"]), 'd-m-Y').'</td>';
$htmlTable .='<td>'.$row["source"].'</td>';
$htmlTable .='<td>'.$row["orgName"].'</td>';
$htmlTable .='<td>'.$row["invoiceNo"].'</td>';
$htmlTable .='<td>'.$row["name"].'</td>';
$htmlTable .='<td>'.round($row["total_charges_interp"],2).'</td>';
$htmlTable .='<td>'.$misc->dated($row["paid_date"]).'</td>';
$htmlTable .='<td>'.round($row["total_charges_comp"],2).'</td>';
$htmlTable .='<td>'.$vat=$row["total_charges_comp"]*.2.'</td>';
$htmlTable .='<td>'.$row["C_otherCost"].'</td>';
$htmlTable .='<td>'.round($row["total_charges_comp"] + $row["C_otherCost"] + $vat,2).'</td>';
$htmlTable .='<td>'.$misc->dated($row['rDate']).'</td>';
$htmlTable .='<td>'.$row["card_payment"].$row['bacs'].$row['cheque'].'</td>
</tr>';
$i++;}
$htmlTable .='</TABLE>';
$pdf->WriteHTML2("<br><br><br>$htmlTable");
$pdf->SetFont('Arial','B',6);
$pdf->Output(); 
?>