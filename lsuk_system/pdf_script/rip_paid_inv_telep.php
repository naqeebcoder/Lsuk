<?php include '../db.php';include_once ('../class.php'); $search_1=@$_GET['search_1'];$search_2=@$_GET['search_2'];$search_3=@$_GET['search_3'];
$i=1;$table='telephone';$total_charges_comp=0;$C_otherCharges=0;$g_total=0;$g_vat=0;$C_otherCharges=0;$non_vat=0;$comp_name=$acttObj->unique_data('comp_reg','name','abrv',$search_1);
$query="SELECT *, total_charges_comp * 0.2 as vat, interpreter_reg.name  FROM $table
	   inner join interpreter_reg on $table.intrpName = interpreter_reg.id
	   where  $table.orgName like '$search_1%' and assignDate between '$search_2' and '$search_3' and  rAmount > 0";
$result = mysqli_query($con, $query);
require('WriteHTML.php');

$pdf=new PDF_HTML();

$pdf->AliasNbPages();
$pdf->SetAutoPageBreak(true, 15);

$pdf->AddPage();
$pdf->Image('logo.png',18,12,23);
$pdf->SetFont('Arial','B',14);
$pdf->WriteHTML('<para><h1>            Language Services UK Limited<br>         Translation and Interpreting Service</h1><br><u>Suite 2 Davis House Lodge Causeway Trading Estate<br>Lodge Causeway - Fishponds Bristol BS163JB</u></para><br><br><br><br><u>Account Statement Report for "'.$comp_name.'"<br>Paid Invoices Telephone  Interpreting<br><br>Date Statement: '.$misc->dated($search_2).' to '.$misc->dated($search_3).'<br><br>Date Range: '.$misc->dated($search_2).' to '.$misc->dated($search_3).'<br><br>Report Date: '.$misc->sys_date().'</u>');
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
    <td>Invoice Total</td>
    <td>Paid Date</td>
    <td>Payment Method</td> ';
while($row = mysqli_fetch_assoc($result)){$g_total=$row["total_charges_comp"] + round($row["total_charges_comp"]*.2,2) + $C_otherCost=$row["C_otherCharges"] + $g_total;$g_vat=round($row["vat"],2) +$g_vat;$C_otherCharges=$row["C_otherCharges"] + $C_otherCharges;$non_vat=$row["total_charges_comp"] + $non_vat;
$htmlTable .='<tr>';
$htmlTable .='<td>'.$i.'</td>';
$htmlTable .='<td>'.$row["invoiceNo"].'</td>';
$htmlTable .='<td>'.date_format(date_create($row["assignDate"]), 'd-m-Y').'</td>';
$htmlTable .='<td>'.$row["source"].'</td>';
$htmlTable .='<td>'.$row["orgName"].'</td>';
$htmlTable .='<td>'.$row["name"].'</td>';
$htmlTable .='<td>'.$row["total_charges_comp"].'</td>';
$htmlTable .='<td>'.round($row["vat"],2).'</td>';
$htmlTable .='<td>'.$row["C_otherCharges"].'</td>';

$htmlTable .='<td>'.round( $row["total_charges_comp"] + $row["total_charges_comp"] *0.2 + $row["C_otherCharges"],2).'</td>';
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
$htmlTable .='<td>Total</td>';
$htmlTable .='<td>'.$non_vat.'</td>';
$htmlTable .='<td>'.$g_vat.'</td>';
$htmlTable .='<td>'.$C_otherCharges.'</td>';

$htmlTable .='<td>'.$g_total.'</td>';
$htmlTable .='<td></td>';
$htmlTable .='<td></td>
</tr>';
$htmlTable .='</TABLE>';
$pdf->WriteHTML2("<br><br><br>$htmlTable");
$pdf->SetFont('Arial','B',6);
$pdf->Output(); 
?>