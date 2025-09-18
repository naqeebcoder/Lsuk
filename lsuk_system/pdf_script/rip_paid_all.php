<?php include '../db.php';include_once ('../class.php'); $search_1=@$_GET['search_1'];$search_2=@$_GET['search_2'];$search_3=@$_GET['search_3'];
$i=1;$table='interpreter';$total_charges_comp=0;$C_otherCost=0;$g_total=0;$g_vat=0;$C_otherCost=0;$non_vat=0;$vated_cost=0;$comp_name=$acttObj->unique_data('comp_reg','name','abrv',$search_1);
$query="SELECT
   interpreter.invoiceNo,
   interpreter.assignDate,
   interpreter.assignTime,
   interpreter.source,
   interpreter.orgName,
   interpreter.total_charges_comp,
   interpreter.C_otherCost,
   interpreter.rDate,
   interpreter.card_payment,
   interpreter.bacs,
   interpreter.cheque,
   interpreter.total_charges_comp * 0.2 as vat,
   interpreter_reg.name  ,
   'Interpreter' as tble
FROM
   interpreter 
   inner join
      interpreter_reg 
      on interpreter.intrpName = interpreter_reg.id 
where
	 interpreter.deleted_flag = 0 
	 and interpreter.order_cancel_flag=0 
	 and interpreter.orgName like '$search_1%' 
   and interpreter.assignDate between '$search_2' and '$search_3' 
   and interpreter.rAmount >0 
union
SELECT
   telephone.invoiceNo,
   telephone.assignDate,
   telephone.assignTime,
   telephone.source,
   telephone.orgName,
   telephone.total_charges_comp,
   telephone.C_otherCharges as C_otherCost,
   telephone.rDate,
   telephone.card_payment,
   telephone.bacs,
   telephone.cheque,
   telephone.total_charges_comp * 0.2 as vat,
   interpreter_reg.name  ,
   'Telephone' as tble
FROM
   telephone 
   inner join
      interpreter_reg 
      on telephone.intrpName = interpreter_reg.id 
where
	telephone.deleted_flag = 0 
	 and telephone.order_cancel_flag=0 
	 and telephone.orgName like '$search_1%' 
   and telephone.assignDate between '$search_2' and '$search_3' 
   and telephone.rAmount >0 
union
SELECT
   translation.invoiceNo,
   translation.asignDate as assignDate,
   'Nil' as assignTime,
   translation.source,
   translation.orgName,
   translation.total_charges_comp,
   translation.C_otherCharg as C_otherCost,
   translation.rDate,
   translation.card_payment,
   translation.bacs,
   translation.cheque,
   translation.total_charges_comp * 0.2 as vat,
   interpreter_reg.name  ,
   'Translation' as tble
FROM
   translation 
   inner join
      interpreter_reg 
      on translation.intrpName = interpreter_reg.id 
where
	translation.deleted_flag = 0 
	 and translation.order_cancel_flag=0 
	 and translation.orgName like '$search_1%' 
   and translation.asignDate between '$search_2' and '$search_3' 
   and translation.rAmount >0";
$result = mysqli_query($con, $query);
require('WriteHTML.php');

$pdf=new PDF_HTML();

$pdf->AliasNbPages();
$pdf->SetAutoPageBreak(true, 15);

$pdf->AddPage();
$pdf->Image('logo.png',18,12,23);
$pdf->SetFont('Arial','B',14);
$pdf->WriteHTML('<para><h1>            Language Services UK Limited<br>         Translation and Interpreting Service</h1><br><u>Suite 2 Davis House Lodge Causeway Trading Estate<br>Lodge Causeway - Fishponds Bristol BS163JB</u></para><br><br><br><br><u>Account Statement Report for "'.$comp_name.'"<br>Paid Invoices<br><br>Date Range: '.$misc->dated($search_2).' to '.$misc->dated($search_3).'<br><br>Report Date: '.$misc->sys_date().'</u>');

$pdf->SetFont('Arial','B',7); 
$htmlTable='<table>';
$htmlTable .='<tr>

    <td>#</td>
	<td>Invoice Number</td>
	<td>Mode</td>
    <td>Job Date</td>
    <td>Job Time</td>
    <td>Language</td>
    <td>Client Name</td>
    <td>Interpreter Name</td>
    <td>Witdout VAT</td>
    <td>VAT</td>
    <td>Non-VAT Costs</td>
    <td>Invoice Total</td>
    <td>Paid Date</td>
    <td>Payment Method</td> ';
while($row = mysqli_fetch_assoc($result)){$g_total=$row["total_charges_comp"] + round($row["total_charges_comp"]*.2,2) + $C_otherCost=$row["C_otherCost"] + $g_total;$g_vat=$row["total_charges_comp"]*0.2 +$g_vat;$C_otherCost=$row["C_otherCost"] + $C_otherCost;$non_vat=$row["total_charges_comp"] + $non_vat;$vated_cost=$row["C_otherCost"] + $vated_cost;
$htmlTable .='<tr>';
$htmlTable .='<td>'.$i.'</td>';
$htmlTable .='<td>'.$row["invoiceNo"].'</td>';
$htmlTable .='<td>'.$row["tble"].'</td>';
$htmlTable .='<td>'.date_format(date_create($row["assignDate"]), 'd-m-Y').'</td>';
$htmlTable .='<td>'.$row["assignTime"].'</td>';
$htmlTable .='<td>'.$row["source"].'</td>';
$htmlTable .='<td>'.$row["orgName"].'</td>';
$htmlTable .='<td>'.$row["name"].'</td>';
$htmlTable .='<td>'.$row["total_charges_comp"].'</td>';
$htmlTable .='<td>'.round($row["total_charges_comp"]*0.2,2).'</td>';
$htmlTable .='<td>'.$row["C_otherCost"].'</td>';

$htmlTable .='<td>'.round( $row["total_charges_comp"] + $row["total_charges_comp"] *0.2 + $row["C_otherCost"],2).'</td>';
$htmlTable .='<td>'.$misc->dated($row['rDate']).'</td>';
$htmlTable .='<td>'.$row["card_payment"].$row['bacs'].$row['cheque'].'</td>
</tr>';
$i++;}
$htmlTable .='<tr>';
$htmlTable .='<td></td>';
$htmlTable .='<td></td>';
$htmlTable .='<td></td>';
$htmlTable .='<td></td>';
$htmlTable .='<td></td>';
$htmlTable .='<td></td>';
$htmlTable .='<td></td>';
$htmlTable .='<td>Total</td>';
$htmlTable .='<td>'.$non_vat.'</td>';
$htmlTable .='<td>'.round($g_vat).'</td>';
$htmlTable .='<td>'.round($vated_cost).'</td>';

$htmlTable .='<td>'.$g_total.'</td>';
$htmlTable .='<td></td>';
$htmlTable .='<td></td>
</tr>';
$htmlTable .='</TABLE>';
$pdf->WriteHTML2("<br><br><br>$htmlTable");
$pdf->SetFont('Arial','B',6);
$pdf->Output(); 
?>