<?php 
include '../db.php';
include_once ('../class.php'); 

$search_1=@$_GET['search_1'];
$search_2=@$_GET['search_2'];
$search_3=@$_GET['search_3'];

$i=1;
$table='emp';
$salary=0;
$duration=0;

if(!empty($search_1))
{
    $query="SELECT $table.*,  rolcal.*  
        FROM $table
	    join rolcal on $table.id = rolcal.empId 
        where rolcal.dated between '$search_2' and '$search_3' and $table.name='$search_1' ##emp_active##";
}
else
{
    $query="SELECT $table.*,  rolcal.*  FROM $table
	   join rolcal on $table.id = rolcal.empId 
       where rolcal.dated between '$search_2' and '$search_3' ##emp_active##";
}
$query=SqlUtils::ModfiySql($query);

$result = mysqli_query($con, $query);
require('WriteHTML.php');

$pdf=new PDF_HTML();

$pdf->AliasNbPages();
$pdf->SetAutoPageBreak(true, 15);

$pdf->AddPage();
$pdf->Image('logo.png',18,12,23);
$pdf->SetFont('Arial','B',14);
$pdf->WriteHTML('<para><h1>            Language Services UK Limited<br>         Translation and Interpreting Service</h1><br><u>Suite 2 Davis House Lodge Causeway Trading Estate<br>Lodge Causeway - Fishponds Bristol BS163JB</u></para><br><br><br><br><u>Salary Report<br><br>Date: '.$misc->sys_date().'</u>');

$pdf->SetFont('Arial','B',7); 
$htmlTable='<table>';
$htmlTable .='<tr>

    <td>#</td>
    <td>Name</td>
    <td>Designation</td>
    <td>Start</td>
    <td>Finish</td>
    <td>Duration</td>
    <td>RPH</td>
    <td>Total Amount</td>
    <td>Salary Date</td>
    <td>Dated</td>';
while($row = mysqli_fetch_assoc($result)){$salary=$row["salary"] + $salary;$duration=$row["duration"] + $duration;
$htmlTable .='<tr>';
$htmlTable .='<td>'.$i.'</td>';
$htmlTable .='<td>'.$row["name"].'</td>';
$htmlTable .='<td>'.$row["desig"].'</td>';
$htmlTable .='<td>'.$row["start"].'</td>';
$htmlTable .='<td>'.$row["finish"].'</td>';
$htmlTable .='<td>'.round($row["duration"]).'</td>';
$htmlTable .='<td>'.$row["rph"].'</td>';
$htmlTable .='<td>'.round($row["salary"]).'</td>';
$htmlTable .='<td>'.$misc->dated($row['entry_date']).'</td>';

$htmlTable .='<td>'.date_format(date_create($row["dated"]), 'd-m-Y').'</td>
</tr>';
$i++;}


$htmlTable .='<tr>';
$htmlTable .='<td></td>';
$htmlTable .='<td></td>';
$htmlTable .='<td></td>';
$htmlTable .='<td></td>';
$htmlTable .='<td>Total</td>';
$htmlTable .='<td>'.round($duration).'</td>';
$htmlTable .='<td></td>';
$htmlTable .='<td>'.number_format(round($salary),2).'</td>';

$htmlTable .='<td></td>';
$htmlTable .='<td></td>
</tr>';

$htmlTable .='</TABLE>';
$pdf->WriteHTML2("<br><br><br>$htmlTable");
$pdf->SetFont('Arial','B',6);
$pdf->Output(); 
?>