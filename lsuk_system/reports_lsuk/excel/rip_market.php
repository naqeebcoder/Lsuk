<?php include '../../db.php';include_once ('../../class.php'); $excel=@$_GET['excel'];$search_1=@$_GET['search_1'];$search_2=@$_GET['search_2'];$search_3=@$_GET['search_3'];
$i=1;$table='comp_reg';
//...................................................For Multiple Selection...................................\\
 $arr_compType = explode(',', $search_1);$_words_compType = implode("' OR compType like '", $arr_compType);
//......................................\\//\\//\\//\\//........................................................\\
if(!empty($search_1)){
$query="SELECT * FROM $table
	   where dated between '$search_2' and '$search_3' and ($table.compType like '%$_words_compType%')";}
	   else{$query="SELECT * FROM $table
	   where dated between '$search_2' and '$search_3'";}
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
$htmlTable .='<h2 style="text-decoration:underline; text-align:center">Client Marketing Report</h2>
<p>Marketing Report<br/>Date Range:' .$misc->dated($search_2). 'to' .$misc->dated($search_3). '</p>
</div>

<table>
<thead>
<tr>
	<th style="background-color:#039;color:#FFF;">Sr.No</th>
	<th style="background-color:#039;color:#FFF;">Client Name</th>
    <th style="background-color:#039;color:#FFF;">Company Type </th>
    <th style="background-color:#039;color:#FFF;">Contact Name</th>
    <th style="background-color:#039;color:#FFF;">Contact #</th>
    <th style="background-color:#039;color:#FFF;">Email address</th>';
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
$htmlTable.='</table>';

list($a,$b)=explode('.',basename(__FILE__));
header("Content-Type: application/xls");
header("Content-Disposition: attachment; filename=".$a.".xls");  
echo $htmlTable;
?>