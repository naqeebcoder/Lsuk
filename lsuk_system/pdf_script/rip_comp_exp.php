<?php include '../db.php';include_once ('../class.php'); ;$search_1=@$_GET['search_1']; $search_2=@$_GET['search_2']; $search_3=@$_GET['search_3'];
$i=1;$table='expence';$amoun=0;
$query="SELECT * FROM $table 
	   where title like '$search_1%' and billDate between '$search_2' and '$search_3'";
$result = mysqli_query($con, $query);
require('WriteHTML.php');

$pdf=new PDF_HTML();

$pdf->AliasNbPages();
$pdf->SetAutoPageBreak(true, 15);

$pdf->AddPage();
$pdf->Image('logo.png',18,12,23);
$pdf->SetFont('Arial','B',14);
$pdf->WriteHTML('<para><h1>            Language Services UK Limited<br>         Translation and Interpreting Service</h1><br><u>Suite 2 Davis House Lodge Causeway Trading Estate<br>Lodge Causeway - Fishponds Bristol BS163JB</u></para><br><br><br><br><u>LSUK Expanses Report for '.$search_1.' <br>Date Range: ('.$misc->dated($search_2). ' to ' .$misc->dated($search_3).')<br><br>Date: '.$misc->sys_date().'</u>');

$pdf->SetFont('Arial','B',7); 
$htmlTable='<table>';
$htmlTable .='<tr>

    <td>#</td>
    <td>Title</td>
    <td>Amount</td>
    <td>Details</td>
    <td>Voucher #</td>
    <td>Company</td>
    <td>Bill Date</td>';


while($row = mysqli_fetch_assoc($result)){ $amoun=$row["amoun"] + $amoun;


$htmlTable .='<tr>';
$htmlTable .='<td>'.$i.'</td>';
$htmlTable .='<td>'.$row["title"].'</td>';
$htmlTable .='<td>'.$row["amoun"].'</td>';
$htmlTable .='<td>'.$row["details"].'</td>';
$htmlTable .='<td>'.$row["voucher"].'</td>';
$htmlTable .='<td>'.$row["comp"].'</td>';
$htmlTable .='<td>'.$misc->dated($row["billDate"]).'</td>
</tr>';
$i++;}
$htmlTable .='<tr>';
$htmlTable .='<td></td>';
$htmlTable .='<td>Total</td>';
$htmlTable .='<td>'.$amoun.'</td>';
$htmlTable .='<td></td>';
$htmlTable .='<td></td>';
$htmlTable .='<td></td>';
$htmlTable .='<td></td>
</tr>';
$htmlTable .='</TABLE>';
$pdf->WriteHTML2("<br><br><br>$htmlTable");
$pdf->SetFont('Arial','B',6);
$pdf->Output(); 
?>