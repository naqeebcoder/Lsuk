<?php include '../../db.php';include_once ('../../class.php'); $excel=@$_GET['excel'];session_start();$UserName=$_SESSION['UserName'];$prv=$_SESSION['prv'];$search_1=@$_GET['search_1'];$search_2=@$_GET['search_2'];$search_3=@$_GET['search_3'];
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

//...................................................................................................................................../////
$htmlTable='';
$htmlTable.='<style>
table {border-collapse: collapse; width:670px;}
th {border: 1px solid #999; padding: 0.5rem;text-align: left;background-color:#039; color:#FFF; font-weight:bold;}
td {border: 1px solid #999; padding: 0.5rem;text-align: left;}
</style>
<div>';
$htmlTable .='<span align="right"> Date: '.$misc->sys_date(). '</span>';
$htmlTable .='<h2 style="text-decoration:underline; text-align:center">SLA Report"'.$comp_name.'"</h2>
<p>SLA Report<br/>Date Range:' .$misc->dated($search_2). 'to' .$misc->dated($search_3). '</p>
</div>

<table>
<thead>
<tr>
	<th style="background-color:#039;color:#FFF;">Sr.No</th>
	<th style="background-color:#039;color:#FFF;">Invoice Number</th>
	<th style="background-color:#039;color:#FFF;">Mode</th>
	<th style="background-color:#039;color:#FFF;">Date</th>
    <th style="background-color:#039;color:#FFF;">Time</th>
    <th style="background-color:#039;color:#FFF;">Company</th>
    <th style="background-color:#039;color:#FFF;">Language</th>
    <th style="background-color:#039;color:#FFF;">Interpreting Time</th>
    <th style="background-color:#039;color:#FFF;">Cost</th>
    <th style="background-color:#039;color:#FFF;">Purchase Order Number</th>	
    <th style="background-color:#039;color:#FFF;">Invoice Status </th>';
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

$htmlTable.='</table>';

list($a,$b)=explode('.',basename(__FILE__));
header("Content-Type: application/xls");
header("Content-Disposition: attachment; filename=".$a.".xls");  
echo $htmlTable;
?>