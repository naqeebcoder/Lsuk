<?php include '../../db.php';include_once ('../../class.php'); $excel=@$_GET['excel'];$search_1=$_GET['search_1'];$search_2=$_GET['search_2'];$counter=0;$x=0; $source_num=0;$table='translation';$org='';
//...................................................For Multiple Selection...................................\\
$counter=0; $arr = explode(',', $search_1);$_words = implode("' OR orgName = '", $arr);$arr_Month = array();
//......................................\\//\\//\\//\\//........................................................\\
//................................................................................................................
$htmlTable='';
$htmlTable.='<style>
table {border-collapse: collapse; width:670px;}
th {border: 1px solid #999; padding: 0.5rem;text-align: left;background-color:#039; color:#FFF; font-weight:bold;}
td {border: 1px solid #999; padding: 0.5rem;text-align: left;}
</style>
<div>';
$htmlTable .='<span align="right"> Date: '.$misc->sys_date(). '</span>';
$htmlTable .='<h2 style="text-decoration:underline; text-align:center">Consolidate Order Cancelation Report(Translation)</h2>
<p>Translation Interpreting<br/>Date Range:' .$search_2. '</p>
</div>

<table>';
$htmlTable.='<tr>';
$htmlTable.='<th>Cancelled By</th>';
$query="SELECT distinct(month(asignDate)) as asignDate FROM $table					
	   			where order_cancel_flag=1 and year(asignDate) = '$search_2' and (orgName = '$_words') order by $table.asignDate";
	   $result = mysqli_query($con, $query); $result = mysqli_query($con, $query);while($row = mysqli_fetch_assoc($result)){ $asignDate=$row['asignDate']; $arr_Month[]=$asignDate;
$htmlTable.='<th>'.date('F', mktime(0, 0, 0, $asignDate, 10)).'</th>';
$counter++;}
$htmlTable.='</tr>';
$arr_Month=array_unique($arr_Month);$query="SELECT distinct ($table.order_cancelledby) FROM $table					
	   			where order_cancel_flag=1 and year(asignDate) = '$search_2' and (orgName = '$_words')";
	   $result = mysqli_query($con, $query); $result = mysqli_query($con, $query);while($row = mysqli_fetch_assoc($result)){ $order_cancelledby=$row['order_cancelledby'];
$htmlTable.='<tr><td>'.$order_cancelledby.'</td>';
   
foreach($arr_Month as $month){
  
$x=$counter;$u=0; while($x>$u){ 
   $query_inner="SELECT count(order_cancelledby) as order_cancelledby_num FROM $table
  				INNER JOIN invoice ON $table.invoiceNo=invoice.invoiceNo
	   			where order_cancel_flag=1 and year(asignDate) = '$search_2' and month(asignDate)= '$month' and (orgName = '$_words') and order_cancelledby='$order_cancelledby'";
	   $result_inner = mysqli_query($con, $query_inner);while($row_inner = mysqli_fetch_assoc($result_inner)){
    
$htmlTable.='<td>'.$row_inner["order_cancelledby_num"].'</td>';
    
$u++;}break;}}
$htmlTable.='</tr>';
     
$x++;}
 
$htmlTable.='<tr>';
 
$htmlTable.='<td>Total</td>';
foreach($arr_Month as $month){
	 
  $query_total_inner="SELECT count(order_cancelledby) as order_cancelledby_num FROM $table
						INNER JOIN invoice ON $table.invoiceNo=invoice.invoiceNo  
	   					where order_cancel_flag=1 and year(asignDate) = '$search_2' and month(asignDate)= '$month' and (orgName = '$_words')";
	   $result_total_inner = mysqli_query($con, $query_total_inner);while($row_total_inner = mysqli_fetch_assoc($result_total_inner)){
    
$htmlTable.='<td>'.$row_total_inner["order_cancelledby_num"].'</td>';
    
}} 
$htmlTable.='</tr>';
$htmlTable.='<tr>';
 
$htmlTable.='<td>Total Cost</td>';
foreach($arr_Month as $month){
	 
  $query_total_inner="SELECT sum($table.total_charges_comp) as total_charges_comp, sum($table.total_charges_comp * $table.cur_vat) as total_charges_comp_vat FROM $table
						INNER JOIN invoice ON $table.invoiceNo=invoice.invoiceNo  
	   					where order_cancel_flag=1 and year(asignDate) = '$search_2' and month(asignDate)= '$month' and (orgName = '$_words')";
	   $result_total_inner = mysqli_query($con, $query_total_inner);while($row_total_inner = mysqli_fetch_assoc($result_total_inner)){
    
$htmlTable.='<td>'.round ($row_total_inner["total_charges_comp_vat"] + $row_total_inner["total_charges_comp"], 2).'</td>';
    
 }}
$htmlTable.='</tr>';
$htmlTable.='</table>';

list($a,$b)=explode('.',basename(__FILE__));
header("Content-Type: application/xls");
header("Content-Disposition: attachment; filename=".$a.".xls");  
echo $htmlTable;
echo 'Oganization(s):'.$search_1;
?>