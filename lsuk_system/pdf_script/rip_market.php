<?php include '../db.php';include_once ('../class.php'); $search_1=@$_GET['search_1'];$search_2=@$_GET['search_2'];$search_3=@$_GET['search_3'];
$i=1;$table='comp_reg';
if(!empty($search_1)){
$query="SELECT * FROM $table
	   where dated between '$search_2' and '$search_3' and compType='$search_1'";}
	   else{$query="SELECT * FROM $table
	   where dated between '$search_2' and '$search_3'";}
$result = mysqli_query($con, $query);
require('WriteHTML.php');

$pdf=new PDF_HTML();

$pdf->AliasNbPages();
$pdf->SetAutoPageBreak(true, 15);

$pdf->AddPage();
$pdf->Image('logo.png',18,12,23);
$pdf->SetFont('Arial','B',14);
$pdf->WriteHTML('<para><h1>            Language Services UK Limited<br>         Translation and Interpreting Service</h1><br><u>Suite 2 Davis House Lodge Causeway Trading Estate<br>Lodge Causeway - Fishponds Bristol BS163JB</u></para><br><br><br><br><u>Client Marketing Report<br><br>Date: '.$misc->sys_date().'</u>');

$pdf->SetFont('Arial','B',7); 
$htmlTable='<table>';
$htmlTable .='<tr>

    <td>#</td>
	<td>Client Name</td>
    <td>Company Type </td>
    <td>Contact Name</td>
    <td>Contact #</td>
    <td>Email address</td>';
while($row = mysqli_fetch_assoc($result)){
$htmlTable .='<tr>';
$htmlTable .='<td>'.$i.'</td>';
$htmlTable .='<td>'.$row["name"].'</td>';
$htmlTable .='<td>'.$row["compType"].'</td>';
$htmlTable .='<td>'.$row["contactPerson"].'</td>';
$htmlTable .='<td>'.$row["contactNo1"].' , '.$row["contactNo2"].' , '.$row["contactNo3"].'</td>';
$htmlTable .='<td>'.$row["email"].'</td>
</tr>';
$i++;}
$htmlTable .='</TABLE>';
$pdf->WriteHTML2("<br><br><br>$htmlTable");
$pdf->SetFont('Arial','B',6);
$pdf->Output(); 
?>