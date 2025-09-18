<?php include '../../db.php';include_once ('../../class.php'); $excel=@$_GET['excel'];$search_1=@$_GET['search_1'];$search_2=@$_GET['search_2'];$search_3=@$_GET['search_3'];
$i=1;$table='interpreter';$total_charges_interp=0; 
//...................................................For Multiple Selection...................................\\
 $arr_intrp = explode(',', $search_1);$_words_intrp = implode("' OR name like '", $arr_intrp);
//......................................\\//\\//\\//\\//........................................................\\
if($search_1){
$query="SELECT $table.*, interpreter_reg.name FROM $table
	   inner join interpreter_reg on $table.intrpName = interpreter_reg.id 
	   where $table.deleted_flag = 0 and $table.order_cancel_flag=0 and $table.assignDate between '$search_2' and '$search_3' and (interpreter_reg.name like '%$_words_intrp%')";}
else{$query="SELECT $table.*, interpreter_reg.name  FROM $table
	   inner join interpreter_reg on $table.intrpName = interpreter_reg.id 
	   where $table.deleted_flag = 0 and $table.order_cancel_flag=0 and assignDate between '$search_2' and '$search_3'";}
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
$htmlTable .='<h2 style="text-decoration:underline; text-align:center">Interpreter General Report (Account Purpose)</h2>
<p>Paid Invoices Face to Face Interpreting<br/>Date Range:' .$misc->dated($search_2). 'to' .$misc->dated($search_3). '</p>
</div>

<table>
<thead>
<tr>
 	<th style="background-color:#039;color:#FFF;">Sr.No</th>
    <th style="background-color:#039;color:#FFF;">Invoice No.</th>
    <th style="background-color:#039;color:#FFF;">Company</th>
    <th style="background-color:#039;color:#FFF;">Interpreter Name</th>
    <th style="background-color:#039;color:#FFF;">Language</th>
    <th style="background-color:#039;color:#FFF;">Assignment Date</th>
    <th style="background-color:#039;color:#FFF;">Amount Paid to the Interrpeter</th>
    <th style="background-color:#039;color:#FFF;">Interpreter Payment Date</th> 
 </tr>
</thead>';

while($row = mysqli_fetch_assoc($result)){$total_charges_interp=$row["total_charges_interp"] + $total_charges_interp;

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
	$htmlTable .='<td style="font-weight:bold;"  colspan="6" align="right">Total</td>';
	$htmlTable .=' <td style="font-weight:bold;">'.$total_charges_interp.'</td>';
	$htmlTable .='<td></td>
	  </tr>';
$htmlTable.='</table>';

list($a,$b)=explode('.',basename(__FILE__));
header("Content-Type: application/xls");
header("Content-Disposition: attachment; filename=".$a.".xls");  
echo $htmlTable;
?>