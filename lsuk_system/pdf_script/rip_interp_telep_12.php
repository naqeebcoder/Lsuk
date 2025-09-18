<?php include '../db.php';include_once ('../class.php'); $search_1=@$_GET['search_1'];$search_2=@$_GET['search_2'];$search_3=@$_GET['search_3'];
$i=1;$table='telephone';$otherCharges=0;$chargInterp=0;$total_charges_comp=0;
if(!empty($search_1)){
$query="SELECT  * FROM comp_reg	   
	   where abrv='$search_1'";	  
$result = mysqli_query($con, $query);
while($row = mysqli_fetch_assoc($result)){$name=$row["name"];$buildingName=$row["buildingName"];$line1=$row["line1"];$line2=$row["line2"];$streetRoad=$row["streetRoad"];$postCode=$row["postCode"];$city=$row["city"];}}
if(!empty($search_1)){
$query="SELECT $table.*, interpreter_reg.name  FROM $table
	   inner join interpreter_reg on $table.intrpName = interpreter_reg.id
	   inner join comp_reg on $table.orgName = comp_reg.abrv 
	   where $table.deleted_flag = 0 and $table.order_cancel_flag=0 and assignDate between '$search_2' and '$search_3' and comp_reg.abrv='$search_1'  order by assignDate";}
	   else{$query="SELECT $table.*, interpreter_reg.name  FROM $table
	   inner join interpreter_reg on $table.intrpName = interpreter_reg.id 
	   where $table.deleted_flag = 0 and $table.order_cancel_flag=0 and assignDate between '$search_2' and '$search_3'  order by assignDate";}
$result = mysqli_query($con, $query);
require('WriteHTML.php');

$pdf=new PDF_HTML();

$pdf->AliasNbPages();
$pdf->SetAutoPageBreak(true, 15);

$pdf->AddPage();
$pdf->Image('logo.png',18,12,23);
$pdf->SetFont('Arial','B',14);
$pdf->WriteHTML('<para><h1>            Language Services UK Limited<br>         Translation and Interpreting Service</h1><br><u>Suite 2 Davis House Lodge Causeway Trading Estate<br>Lodge Causeway - Fishponds Bristol BS163JB</u></para><br><br><br><br><u>Interpreter General Report (12)) for '.@$name .' '. @$streetRoad.' '.@$line1.' '.@$line2.' '.@$city.' '.@$postCode.'<br><br>Date Range: '.$misc->dated($search_2).' to '.$misc->dated($search_3).'<br><br>Date: '.$misc->sys_date().'</u>');

$pdf->SetFont('Arial','B',7); 
$htmlTable='<table>';
$htmlTable .='<tr>

    <td>#</td>
    <td>Interpreter Name</td>
    <td>Job Date</td>
    <td>Interpreting Hours Worked</td>
    <td>Rate Per Hour</td>
    <td>Job Payment</td>     
    <td>Other Expenses</td>          
    <td>Total Job Payment</td>';


while($row = mysqli_fetch_assoc($result)){$chargInterp=$row["chargInterp"] + $chargInterp;$otherCharges=$row["otherCharges"] + $otherCharges;$total_charges_comp=$row["total_charges_comp"] + $total_charges_comp;


$htmlTable .='<tr>';
$htmlTable .='<td>'.$i.'</td>';
$htmlTable .='<td>'.$row["name"].'</td>';
$htmlTable .='<td>'.$misc->dated($row["assignDate"]).'</td>';
$htmlTable .='<td>'.$row["hoursWorkd"].'</td>';
$htmlTable .='<td>'.$row["rateHour"].'</td>';
$htmlTable .='<td>'.$row["chargInterp"].'</td>';
$htmlTable .='<td>'.$row["otherCharges"].'</td>';

$htmlTable .='<td>'.$row["total_charges_interp"].'</td>
</tr>';
$i++;}
$htmlTable .='<tr>';
$htmlTable .='<td></td>';
$htmlTable .='<td></td>';
$htmlTable .='<td></td>';
$htmlTable .='<td></td>';
$htmlTable .='<td>Total</td>';
$htmlTable .='<td>'.$chargInterp.'</td>';
$htmlTable .='<td>'.$otherCharges.'</td>';

$htmlTable .='<td>'.$total_charges_comp.'</td>
</tr>';
$htmlTable .='</TABLE>';
$pdf->WriteHTML2("<br><br><br>$htmlTable");
$pdf->SetFont('Arial','B',6);
$pdf->Output(); 
?>