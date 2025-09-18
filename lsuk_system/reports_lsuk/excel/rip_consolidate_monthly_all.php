<?php include '../../db.php';include_once ('../../class.php'); $excel=@$_GET['excel'];$search_1=$_GET['search_1'];$search_2=@$_GET['search_2'];$counter=0;$x=0; $source_num=0;$table='interpreter';$org='';
//...................................................For Multiple Selection...................................\\
$counter=0; $arr = explode(',', $search_1);$_words = implode("' OR orgName = '", $arr);$arr_Month = array();$arr_Month_trans = array();$arr_Month_telep = array();
//......................................\\//\\//\\//\\//........................................................\\
//................................................................................................................
$htmlTable='';
$htmlTable.='<style>
table {border-collapse: collapse; width:670px;}
th {border: 1px solid #999; padding: 0.5rem;text-align: left;background-color:#039; color:#FFF; font-weight:bold;}
td {border: 1px solid #999; padding: 0.5rem;text-align: left;}
</style>
<div>';
$htmlTable .='<h2 style="text-decoration:underline; text-align:center">Company Wise Consolidate Report(Interpreter, Telphone and Translation)</h2>
<p>Report Date : ' .$misc->sys_date(). '<br>
All Type Interpreting<br/>Date Range (Year) : ' .$search_2. '</p>
</div>

<table>';
$htmlTable.='<tr>';
$htmlTable.='<th style="background-color: #039;color: #FFF;">Interpreter</th>';
  $query="SELECT distinct(month(assignDate)) as assignDate FROM $table 
	   inner join interpreter_reg on $table.intrpName = interpreter_reg.id 
	   inner join comp_reg on $table.orgName = comp_reg.abrv 					
	   			where year(assignDate) = '$search_2' and (orgName = '$_words')  and $table.deleted_flag = 0 and $table.order_cancel_flag=0 order by $table.assignDate";
	   $result = mysqli_query($con, $query); $result = mysqli_query($con, $query);while($row = mysqli_fetch_assoc($result)){ $assignDate=$row['assignDate']; $arr_Month[]=$assignDate; 
$htmlTable.='<th style="background-color: #039;color: #FFF;">'.date('F', mktime(0, 0, 0, $assignDate, 10)).'</th>';
 }
$htmlTable.='</tr>';
$htmlTable.='<tr>';
 
$htmlTable.='<td>Total Jobs</td>';
foreach($arr_Month as $month){ 
 
  $query_total_inner="SELECT count(source) as source_num FROM $table 
	   inner join interpreter_reg on $table.intrpName = interpreter_reg.id 
	   inner join comp_reg on $table.orgName = comp_reg.abrv 
						INNER JOIN invoice ON interpreter.invoiceNo=invoice.invoiceNo  
	   					where year(assignDate) = '$search_2' and month(assignDate)= '$month' and (orgName = '$_words') and $table.deleted_flag = 0 and $table.order_cancel_flag=0";
	   $result_total_inner = mysqli_query($con, $query_total_inner);while($row_total_inner = mysqli_fetch_assoc($result_total_inner)){
    
$htmlTable.='<td>'.$row_total_inner["source_num"].'</td>';
    
 }} 
$htmlTable.='</tr>';
$htmlTable.='<tr>';
 
$htmlTable.='<td>Total VAT</td>';
foreach($arr_Month as $month){
	 
  $query_total_inner="SELECT sum(interpreter.total_charges_comp * interpreter.cur_vat) as total_charges_comp_vat FROM $table 
	   inner join interpreter_reg on $table.intrpName = interpreter_reg.id 
	   inner join comp_reg on $table.orgName = comp_reg.abrv 
						INNER JOIN invoice ON interpreter.invoiceNo=invoice.invoiceNo  
	   					where year(assignDate) = '$search_2' and month(assignDate)= '$month' and (orgName = '$_words') and $table.deleted_flag = 0 and $table.order_cancel_flag=0";
	   $result_total_inner = mysqli_query($con, $query_total_inner);while($row_total_inner = mysqli_fetch_assoc($result_total_inner)){
    
$htmlTable.='<td>'.round ($row_total_inner["total_charges_comp_vat"] , 2).'</td>';
    
}}
$htmlTable.='</tr>';
$htmlTable.='<tr>';
 
$htmlTable.='<td>Total Non-VAT</td>';
foreach($arr_Month as $month){
	 
  $query_total_inner="SELECT sum(interpreter.C_otherexpns) as other_expenses FROM $table 
	   inner join interpreter_reg on $table.intrpName = interpreter_reg.id 
	   inner join comp_reg on $table.orgName = comp_reg.abrv 
						INNER JOIN invoice ON interpreter.invoiceNo=invoice.invoiceNo  
	   					where year(assignDate) = '$search_2' and month(assignDate)= '$month' and (orgName = '$_words') and $table.deleted_flag = 0 and $table.order_cancel_flag=0";
	   $result_total_inner = mysqli_query($con, $query_total_inner);while($row_total_inner = mysqli_fetch_assoc($result_total_inner)){
    
$htmlTable.='<td>'.round ($row_total_inner["other_expenses"], 2).'</td>';
    
}}
$htmlTable.='</tr>';
$htmlTable.='<tr>';
 
$htmlTable.='<td>Total Job Cost</td>';
foreach($arr_Month as $month){
	 
  $query_total_inner="SELECT sum($table.total_charges_comp) as total_charges_comp FROM $table 
	   inner join interpreter_reg on $table.intrpName = interpreter_reg.id 
	   inner join comp_reg on $table.orgName = comp_reg.abrv 
						INNER JOIN invoice ON interpreter.invoiceNo=invoice.invoiceNo  
	   					where year(assignDate) = '$search_2' and month(assignDate)= '$month' and (orgName = '$_words') and $table.deleted_flag = 0 and $table.order_cancel_flag=0";
	   $result_total_inner = mysqli_query($con, $query_total_inner);while($row_total_inner = mysqli_fetch_assoc($result_total_inner)){
    
$htmlTable.='<td>'.round ( $row_total_inner["total_charges_comp"], 2).'</td>';
    
}}
$htmlTable.='</tr>';
$htmlTable.='<tr>';
 
$htmlTable.='<td style="background-color: #cacaca;color: black;">Total Invoice Cost</td>';
foreach($arr_Month as $month){
	 
  $query_total_inner="SELECT sum($table.total_charges_comp) as total_charges_comp, sum(interpreter.total_charges_comp * interpreter.cur_vat) as total_charges_comp_vat,sum(interpreter.C_otherexpns) as other_expenses FROM $table 
	   inner join interpreter_reg on $table.intrpName = interpreter_reg.id 
	   inner join comp_reg on $table.orgName = comp_reg.abrv 
						INNER JOIN invoice ON interpreter.invoiceNo=invoice.invoiceNo  
	   					where year(assignDate) = '$search_2' and month(assignDate)= '$month' and (orgName = '$_words') and $table.deleted_flag = 0 and $table.order_cancel_flag=0";
	   $result_total_inner = mysqli_query($con, $query_total_inner);while($row_total_inner = mysqli_fetch_assoc($result_total_inner)){
    
$htmlTable.='<td style="background-color: #cacaca;color: black;">'.round ($row_total_inner["total_charges_comp_vat"] + $row_total_inner["total_charges_comp"]+ $row_total_inner["other_expenses"], 2).'</td>';
    
}}
$htmlTable.='</tr>';
     
$htmlTable.='</table><br/><br/>';
/*<!--.......................................................................//\\//Telephone\\//\\................................................-->
*/
$htmlTable.='<table>';
$htmlTable.='<tr>';
$htmlTable.='<th style="background-color: #039;color: #FFF;">Telephone</th>';
$table='telephone';
 $query_telep="SELECT distinct(month(assignDate)) as assignDate FROM telephone 
	   inner join interpreter_reg on $table.intrpName = interpreter_reg.id 
	   inner join comp_reg on $table.orgName = comp_reg.abrv 					
	   			where year(assignDate) = '$search_2' and (orgName = '$_words') and telephone.deleted_flag = 0 and telephone.order_cancel_flag=0 order by telephone.assignDate";
	   $result_telep = mysqli_query($con, $query_telep); $result_telep = mysqli_query($con, $query_telep);while($row_telep = mysqli_fetch_assoc($result_telep)){ $assignDate_telep=$row_telep['assignDate']; $arr_Month_telep[]=$assignDate_telep; 
$htmlTable.='<th style="background-color: #039;color: #FFF;">'.date('F', mktime(0, 0, 0, $assignDate_telep, 10)).'</th>';
} 
$htmlTable.='</tr>';

$htmlTable.='<tr>';
 
$htmlTable.='<td>Total Jobs</td>';
foreach($arr_Month_telep as $month_telep){
	  	 
  $query_total_inner_telep="SELECT count(source) as source_num FROM telephone 
	   inner join interpreter_reg on $table.intrpName = interpreter_reg.id 
	   inner join comp_reg on $table.orgName = comp_reg.abrv 
						INNER JOIN invoice ON telephone.invoiceNo=invoice.invoiceNo  
	   					where year(assignDate) = '$search_2' and month(assignDate)= '$month_telep' and (orgName = '$_words') and telephone.deleted_flag = 0 and telephone.order_cancel_flag=0";
	   $result_total_inner_telep = mysqli_query($con, $query_total_inner_telep);while($row_total_inner_telep = mysqli_fetch_assoc($result_total_inner_telep)){
    
$htmlTable.='<td>'.$row_total_inner_telep["source_num"].'</td>';
    
}}
$htmlTable.='</tr>';
$htmlTable.='<tr>';
 
$htmlTable.='<td>Total VAT</td>';
foreach($arr_Month_telep as $month_telep){
	 
  $query_total_inner_telep="SELECT sum(telephone.total_charges_comp * telephone.cur_vat) as total_charges_comp_vat FROM telephone 
	   inner join interpreter_reg on $table.intrpName = interpreter_reg.id 
	   inner join comp_reg on $table.orgName = comp_reg.abrv 
						INNER JOIN invoice ON telephone.invoiceNo=invoice.invoiceNo  
	   					where year(assignDate) = '$search_2' and month(assignDate)= '$month_telep' and (orgName = '$_words') and telephone.deleted_flag = 0 and telephone.order_cancel_flag=0";
	   $result_total_inner_telep = mysqli_query($con, $query_total_inner_telep);while($row_total_inner_telep = mysqli_fetch_assoc($result_total_inner_telep)){
    
$htmlTable.='<td>'.round ($row_total_inner_telep["total_charges_comp_vat"] , 2).'</td>';
    
}}
$htmlTable.='</tr>';
$htmlTable.='<tr>';
 
$htmlTable.='<td>Total Job Cost</td>';
foreach($arr_Month_telep as $month_telep){
	 
  $query_total_inner_telep="SELECT sum(telephone.total_charges_comp) as total_charges_comp FROM telephone 
	   inner join interpreter_reg on $table.intrpName = interpreter_reg.id 
	   inner join comp_reg on $table.orgName = comp_reg.abrv 
						INNER JOIN invoice ON telephone.invoiceNo=invoice.invoiceNo  
	   					where year(assignDate) = '$search_2' and month(assignDate)= '$month_telep' and (orgName = '$_words') and telephone.deleted_flag = 0 and telephone.order_cancel_flag=0";
	   $result_total_inner_telep = mysqli_query($con, $query_total_inner_telep);while($row_total_inner_telep = mysqli_fetch_assoc($result_total_inner_telep)){
    
$htmlTable.='<td>'.round ($row_total_inner_telep["total_charges_comp"], 2).'</td>';
    
}}
$htmlTable.='</tr>';
$htmlTable.='<tr>';
 
$htmlTable.='<td style="background-color: #cacaca;color: black;">Total Invoice Cost</td>';
foreach($arr_Month_telep as $month_telep){
	 
  $query_total_inner_telep="SELECT sum(telephone.total_charges_comp) as total_charges_comp, sum(telephone.total_charges_comp * telephone.cur_vat) as total_charges_comp_vat FROM telephone 
	   inner join interpreter_reg on $table.intrpName = interpreter_reg.id 
	   inner join comp_reg on $table.orgName = comp_reg.abrv 
						INNER JOIN invoice ON telephone.invoiceNo=invoice.invoiceNo  
	   					where year(assignDate) = '$search_2' and month(assignDate)= '$month_telep' and (orgName = '$_words') and telephone.deleted_flag = 0 and telephone.order_cancel_flag=0";
	   $result_total_inner_telep = mysqli_query($con, $query_total_inner_telep);while($row_total_inner_telep = mysqli_fetch_assoc($result_total_inner_telep)){
    
$htmlTable.='<td style="background-color: #cacaca;color: black;">'.round ($row_total_inner_telep["total_charges_comp_vat"] + $row_total_inner_telep["total_charges_comp"], 2).'</td>';
    
}}
$htmlTable.='</tr>';
     
$htmlTable.='</table>
<br/><br/>';
/*<!--......................................//.\\//Translation\\//\\.............................................................................-->*/

$htmlTable.='<table>';
$htmlTable.='<tr>';
$htmlTable.='<th style="background-color: #039;color: #FFF;">Translation</th>';
$table='translation';
$query_trans="SELECT distinct(month(asignDate)) as assignDate FROM translation 
	   inner join interpreter_reg on $table.intrpName = interpreter_reg.id 
	   inner join comp_reg on $table.orgName = comp_reg.abrv 					
	   			where year(asignDate) = '$search_2' and (orgName = '$_words') and translation.deleted_flag = 0 and translation.order_cancel_flag=0 order by translation.asignDate";
	   $result_trans = mysqli_query($con, $query_trans); $result_trans = mysqli_query($con, $query_trans);while($row_trans = mysqli_fetch_assoc($result_trans)){ $assignDate_trans=$row_trans['assignDate']; $arr_Month_trans[]=$assignDate_trans;
$htmlTable.='<th style="background-color: #039;color: #FFF;">'.date('F', mktime(0, 0, 0, $assignDate_trans, 10)).'</th>';
 } 
$htmlTable.='</tr>';
$htmlTable.='<tr>';
 
$htmlTable.='<td>Total Jobs</td>';
 foreach($arr_Month_trans as $month_trans){ 
	  	 
  $query_total_inner_trans="SELECT count(source) as source_num FROM translation 
	   inner join interpreter_reg on $table.intrpName = interpreter_reg.id 
	   inner join comp_reg on $table.orgName = comp_reg.abrv 
						INNER JOIN invoice ON translation.invoiceNo=invoice.invoiceNo  
	   					where year(asignDate) = '$search_2' and month(asignDate)= '$month_trans' and (orgName = '$_words') and translation.deleted_flag = 0 and translation.order_cancel_flag=0";
	   $result_total_inner_trans = mysqli_query($con, $query_total_inner_trans);while($row_total_inner_trans = mysqli_fetch_assoc($result_total_inner_trans)){
    
$htmlTable.='<td>'.$row_total_inner_trans["source_num"].'</td>';
    
}} 
$htmlTable.='</tr>';
$htmlTable.='<tr>';
 
$htmlTable.='<td>Total VAT</td>';
foreach($arr_Month_trans as $month_trans){ 
	   
  $query_total_inner_trans="SELECT sum(translation.total_charges_comp * translation.cur_vat) as total_charges_comp_vat FROM translation 
	   inner join interpreter_reg on $table.intrpName = interpreter_reg.id 
	   inner join comp_reg on $table.orgName = comp_reg.abrv 
						INNER JOIN invoice ON translation.invoiceNo=invoice.invoiceNo  
	   					where year(asignDate) = '$search_2' and month(asignDate)= '$month_trans' and (orgName = '$_words') and translation.deleted_flag = 0 and translation.order_cancel_flag=0";
	   $result_total_inner_trans = mysqli_query($con, $query_total_inner_trans);while($row_total_inner_trans = mysqli_fetch_assoc($result_total_inner_trans)){
    
$htmlTable.='<td>'.round ($row_total_inner_trans["total_charges_comp_vat"] , 2).'</td>';
    
 }} 
$htmlTable.='</tr>';
$htmlTable.='<tr>';
 
$htmlTable.='<td>Total Job Cost</td>';
foreach($arr_Month_trans as $month_trans){ 
	   
  $query_total_inner_trans="SELECT sum(translation.total_charges_comp) as total_charges_comp FROM translation 
	   inner join interpreter_reg on $table.intrpName = interpreter_reg.id 
	   inner join comp_reg on $table.orgName = comp_reg.abrv 
						INNER JOIN invoice ON translation.invoiceNo=invoice.invoiceNo  
	   					where year(asignDate) = '$search_2' and month(asignDate)= '$month_trans' and (orgName = '$_words') and translation.deleted_flag = 0 and translation.order_cancel_flag=0";
	   $result_total_inner_trans = mysqli_query($con, $query_total_inner_trans);while($row_total_inner_trans = mysqli_fetch_assoc($result_total_inner_trans)){
    
$htmlTable.='<td>'.round ( $row_total_inner_trans["total_charges_comp"], 2).'</td>';
    
 }} 
$htmlTable.='</tr>';
$htmlTable.='<tr>';
 
$htmlTable.='<td style="background-color: #cacaca;color: black;">Total Invoice Cost</td>';
foreach($arr_Month_trans as $month_trans){ 
	   
  $query_total_inner_trans="SELECT sum(translation.total_charges_comp) as total_charges_comp, sum(translation.total_charges_comp * translation.cur_vat) as total_charges_comp_vat FROM translation 
	   inner join interpreter_reg on $table.intrpName = interpreter_reg.id 
	   inner join comp_reg on $table.orgName = comp_reg.abrv 
						INNER JOIN invoice ON translation.invoiceNo=invoice.invoiceNo  
	   					where year(asignDate) = '$search_2' and month(asignDate)= '$month_trans' and (orgName = '$_words') and translation.deleted_flag = 0 and translation.order_cancel_flag=0";
	   $result_total_inner_trans = mysqli_query($con, $query_total_inner_trans);while($row_total_inner_trans = mysqli_fetch_assoc($result_total_inner_trans)){
    
$htmlTable.='<td style="background-color: #cacaca;color: black;">'.round ($row_total_inner_trans["total_charges_comp_vat"] + $row_total_inner_trans["total_charges_comp"], 2).'</td>';
    
 }} 
$htmlTable.='</tr>';
$htmlTable.='</table>';

list($a,$b)=explode('.',basename(__FILE__));
//$new_name=$a.'_'.implode('_',$arr);
header("Content-Type: application/xls");
header("Content-Disposition: attachment; filename=".$a.".xls"); 
echo $htmlTable;
?>