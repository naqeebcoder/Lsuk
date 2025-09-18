<?php include '../db.php';include_once ('../class.php'); $search_1=@$_GET['search_1'];$search_2=@$_GET['search_2'];$search_3=@$_GET['search_3'];
$i=1;$table='interpreter';$total_charges_interp=0; 
//...................................................For Multiple Selection...................................\\
$counter=0; $arr_intrp = explode(',', $search_1);$_words_intrp = implode("' OR name like '", $arr_intrp);
//......................................\\//\\//\\//\\//........................................................\\
if($search_1){
$query="SELECT $table.*, interpreter_reg.name  FROM $table
	   inner join interpreter_reg on $table.intrpName = interpreter_reg.id 
	   where $table.deleted_flag = 0 and $table.order_cancel_flag=0 and assignDate between '$search_2' and '$search_3' and (interpreter_reg.name like '%$_words_intrp%')";}
else{$query="SELECT $table.*, interpreter_reg.name  FROM $table
	   inner join interpreter_reg on $table.intrpName = interpreter_reg.id 
	   where $table.deleted_flag = 0 and $table.order_cancel_flag=0 and assignDate between '$search_2' and '$search_3'";}
$result = mysqli_query($con, $query);
require('WriteHTML.php');

$pdf=new PDF_HTML();

$pdf->AliasNbPages();
$pdf->SetAutoPageBreak(true, 15);

$pdf->AddPage();
$pdf->Image('logo.png',18,12,23);
$pdf->SetFont('Arial','B',14);
$pdf->WriteHTML('<para><h1>            Language Services UK Limited<br>         Translation and Interpreting Service</h1><br><u>Suite 2 Davis House Lodge Causeway Trading Estate<br>Lodge Causeway - Fishponds Bristol BS163JB</u></para><br><br><br><br><u>Interpreter General Report (Account Purpose) ('.$misc->dated($search_2). ' to ' .$misc->dated($search_3).')<br><br>Date: '.$misc->sys_date().'</u>');

$pdf->SetFont('Arial','B',7); 
$htmlTable='<table>';
$htmlTable .='<tr>

    <td>#</td>
    <td>Invoice No.</td>
    <td>Company</td>
    <td>Interpreter Name</td>
    <td>Language</td>
    <td>Assignment Date</td>
    <td>Amount Paid to the Interrpeter</td>
    <td>Interpreter Payment Date</td>';


while($row = mysqli_fetch_assoc($result)){$counter++; $total_charges_interp=$row["total_charges_interp"] + $total_charges_interp;


$htmlTable .='<tr>';
$htmlTable .='<td>'.$i.'</td>';
$htmlTable .='<td>'.$row["invoiceNo"].'</td>';
$htmlTable .='<td>'.$row["orgName"].'</td>';
$htmlTable .='<td>'.$row["name"].'</td>';
$htmlTable .='<td>'.$row["source"].'</td>';
$htmlTable .='<td>'.$misc->dated($row["assignDate"]).'</td>';
$htmlTable .='<td>'.$row["total_charges_interp"].'</td>';

$htmlTable .='<td>'.$misc->dated($row["paid_date"]).'</td>
</tr>';
$i++;}

$htmlTable .='<tr>';
$htmlTable .='<td></td>';
$htmlTable .='<td></td>';
$htmlTable .='<td></td>';
$htmlTable .='<td></td>';
$htmlTable .='<td></td>';
$htmlTable .='<td>Total</td>';
$htmlTable .='<td>'.$total_charges_interp.'</td>';

$htmlTable .='<td></td>
</tr>';


$htmlTable .='</TABLE>';



$pdf->WriteHTML2("<br><br><br>$htmlTable");
$pdf->WriteHTML('Interpreter(s):  ');for($i=0;$i<$counter;$i++){$pdf->WriteHTML(@$arr_intrp[$i].', ');}
$pdf->SetFont('Arial','B',6);
$pdf->Output(); 
?>