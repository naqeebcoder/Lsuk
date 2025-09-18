<?php include '../db.php';session_start();$UserName=$_SESSION['UserName'];$prv=$_SESSION['prv'];include_once ('../class.php'); $search_1=@$_GET['search_1'];$search_2=@$_GET['search_2'];$search_3=@$_GET['search_3'];
$i=1;$table='interpreter';$total_charges_comp=0;$C_otherCost=0;$g_total=0;$g_vat=0;$C_otherCost=0;$non_vat=0;$comp_name=$acttObj->unique_data('comp_reg','name','abrv',$search_1);
$query="SELECT
   interpreter.dated,
   interpreter.assignTime,
   interpreter.orgName,
   interpreter.source,
   interpreter.hoursWorkd,
   interpreter.total_charges_comp,
   interpreter.invoiceNo,
   interpreter.porder,
   'Interpreter' as tble,
   IF(interpreter.rAmount>0,'Paid','Unpaid') as inv_status
FROM
   interpreter 
  
where
   interpreter.deleted_flag = 0 
   and interpreter.order_cancel_flag=0
   and interpreter.orgName like '$search_1%' 
   and interpreter.dated between '$search_2' and '$search_3' 
   
   union
SELECT
   telephone.dated,
   telephone.assignTime,
   telephone.orgName,
   telephone.source,
   telephone.hoursWorkd,
   telephone.total_charges_comp,
   telephone.invoiceNo,
   telephone.porder,
   'Telephone' as tble,
   IF(telephone.rAmount>0,'Paid','Unpaid') as inv_status
FROM
   telephone 
where
 	telephone.deleted_flag = 0 
   and telephone.order_cancel_flag=0
   and telephone.orgName like '$search_1%' 
   	and telephone.dated between '$search_2' and '$search_3' 
   
union
SELECT
   translation.dated,
   'Nil' as assignTime,
   translation.orgName,
   translation.source,
   translation.C_numberUnit as hoursWorkd,
   translation.total_charges_comp,
   translation.invoiceNo,
   translation.porder,
   'Translation' as tble,
   IF(translation.rAmount>0,'Paid','Unpaid') as inv_status
FROM
   translation 
where
   translation.deleted_flag = 0 
   and translation.order_cancel_flag=0
   and translation.orgName like '$search_1%' 
   and translation.asignDate between '$search_2' and '$search_3' 
   
";

$result = mysqli_query($con, $query);
require('WriteHTML.php');

$pdf=new PDF_HTML();

$pdf->AliasNbPages();
$pdf->SetAutoPageBreak(true, 15);

$pdf->AddPage();
$pdf->Image('logo.png',18,12,23);
$pdf->SetFont('Arial','B',14);
$pdf->WriteHTML('<para><h1>            Language Services UK Limited<br>         Translation and Interpreting Service</h1><br><u>Suite 2 Davis House Lodge Causeway Trading Estate<br>Lodge Causeway - Fishponds Bristol BS163JB</u></para><br><br><br><br><u>SLA Report"'.$comp_name.'"<br><br><br>Date Range: '.$misc->dated($search_2).' to '.$misc->dated($search_3).'<br><br>Report Date: '.$misc->sys_date().'</u>');

$pdf->SetFont('Arial','B',7); 
$htmlTable='<table>';
$htmlTable .='<tr>
    <td>#</td>
	<td>Invoice Number</td>
	<td>Mode</td>
	<td>Date</td>
    <td>Time</td>
    <td>Company</td>
    <td>Language</td>
    <td>Interpreting Time</td>
    <td>Cost</td>
    <td>Purchase Order Number</td>	
    <td>Invoice Status </td>';
while($row = mysqli_fetch_assoc($result)){
$htmlTable .='<tr>';
$htmlTable .='<td>'.$i.'</td>';
$htmlTable .='<td>'.$row["invoiceNo"].'</td>';
$htmlTable .='<td>'.$row["tble"].'</td>';
$htmlTable .='<td>'.$row["dated"].'</td>';
$htmlTable .='<td>'.$row["assignTime"].'</td>';
$htmlTable .='<td>'.$row["orgName"].'</td>';
$htmlTable .='<td>'.$row["source"].'</td>';
$htmlTable .='<td>'.$row["hoursWorkd"].'</td>';
$htmlTable .='<td>'.$row['total_charges_comp'].'</td>';
$htmlTable .='<td>'.$row['porder'].'</td>';
$htmlTable .='<td>'.$row['inv_status'].'</td>
</tr>';
$i++;}

$htmlTable .='</TABLE>';
$pdf->WriteHTML2("<br><br><br>$htmlTable");
$pdf->SetFont('Arial','B',6);
$pdf->Output(); 
?>