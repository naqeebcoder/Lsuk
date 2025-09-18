<?php include '../../db.php';include_once ('../../class.php'); $excel=@$_GET['excel'];$search_1=$_GET['search_1'];$search_2=@$_GET['search_2'];$search_3=@$_GET['search_3'];$counter=0;$x=0; $source_num=0;$table='telephone';$org='';
//...................................................For Multiple Selection...................................\\
$counter=0; $arr = explode(',', $search_1);$_words = implode("' OR orgName = '", $arr);
//......................................\\//\\//\\//\\//........................................................\\
//................................................................................................................
//...................................................................................................................................../////
$htmlTable='';
$htmlTable.='<style>
table {border-collapse: collapse; width:670px;}
th {border: 1px solid #999; padding: 0.5rem;text-align: left;background-color:#039; color:#FFF; font-weight:bold;}
td {border: 1px solid #999; padding: 0.5rem;text-align: left;}
</style>
<div>';
$htmlTable .='<span align="right"> Date: '.$misc->sys_date(). '</span>';
$htmlTable .='<h2 style="text-decoration:underline; text-align:center">Company Wise Consolidate Report(Telephone)</h2>
<p>Face to Face Interpreting<br/>Date Range:' .$misc->dated($search_2). 'to' .$misc->dated($search_3). '</p>
</div>

<table>';
$htmlTable.='<tr>';
$htmlTable.='<th>Source_Lang</th>';
foreach($arr as $orgName){
$htmlTable.='<th>'.$orgName.'</th>';
    $counter++;}
$htmlTable.='</tr>';
$query="SELECT distinct ($table.source) FROM $table					
	   			where (orgName = '$_words') order by $table.source and assignDate between '$search_2' and '$search_3'";
	   $result = mysqli_query($con, $query); $result = mysqli_query($con, $query);while($row = mysqli_fetch_assoc($result)){ $lang=$row['source']; 
$htmlTable.='<tr><td>'.$lang.'</td>';
foreach($arr as $orgName){$orgName=$orgName;$x=$counter;$u=0; while($x>$u){ 
  $query_inner="SELECT count(source) as source_num FROM $table
  				INNER JOIN invoice ON $table.invoiceNo=invoice.invoiceNo
	   			where orgName = '$orgName' and source='$lang' and assignDate between '$search_2' and '$search_3'";
	   $result_inner = mysqli_query($con, $query_inner);while($row_inner = mysqli_fetch_assoc($result_inner)){
    
$htmlTable.='<td>'.$row_inner["source_num"].'</td>';

 $u++;}break;} }
$htmlTable.='</tr>';
     
 $x++;}
 
 
 $htmlTable.='<tr>';
 
$htmlTable.='<td>Total</td>';
 foreach($arr as $orgName){$orgName=$orgName;	 
  $query_total_inner="SELECT count(source) as source_num FROM $table
						INNER JOIN invoice ON $table.invoiceNo=invoice.invoiceNo  
	   					where orgName = '$orgName' and assignDate between '$search_2' and '$search_3'";
	   $result_total_inner = mysqli_query($con, $query_total_inner);while($row_total_inner = mysqli_fetch_assoc($result_total_inner)){
    
$htmlTable.='<td>'.$row_total_inner["source_num"].'</td>';
}}
$htmlTable.='</tr>';
     
 
 
 $htmlTable.='<tr>';
 
$htmlTable.='<td>Total Cost</td>';
 foreach($arr as $orgName){$orgName=$orgName;
  $query_total_inner="SELECT sum($table.total_charges_comp) as total_charges_comp, sum($table.total_charges_comp * $table.cur_vat) as total_charges_comp_vat FROM $table
						INNER JOIN invoice ON $table.invoiceNo=invoice.invoiceNo  
	   					where orgName = '$orgName' and assignDate between '$search_2' and '$search_3'";
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