<?php include '../../db.php';include_once ('../../class.php'); $excel=@$_GET['excel'];$search_1=@$_GET['search_1'];$search_2=@$_GET['search_2'];$search_3=@$_GET['search_3'];
$i=1;$table='telephone';$total_charges_comp=0;$C_otherCharges=0;$g_total=0;$g_vat=0;$C_otherCharges=0;$non_vat=0;$vated_cost=0;$total_vat_total_charges_comp=0;
$comp_name=$acttObj->unique_data('comp_reg','name','abrv',$search_1);
//...................................................For Multiple Selection...................................\\
 $arr_intrp = explode(',', $search_1);$_words_intrp = implode("' OR name like '", $arr_intrp);
//......................................\\//\\//\\//\\//........................................................\\

if(!empty($search_1)){
$query="SELECT *,
       total_charges_comp * cur_vat AS vat,
       interpreter_reg.name
FROM $table
INNER JOIN interpreter_reg ON $table.intrpName = interpreter_reg.id
WHERE $table.deleted_flag = 0
  AND $table.order_cancel_flag=0
  AND assignDate BETWEEN '$search_2' AND '$search_3'
  AND (total_charges_comp > rAmount
       OR total_charges_comp =0)
  AND (interpreter_reg.name LIKE '%$_words_intrp%')";}
	   else{$query="SELECT *,
       total_charges_comp * cur_vat AS vat,
       interpreter_reg.name
FROM $table
INNER JOIN interpreter_reg ON $table.intrpName = interpreter_reg.id
WHERE $table.deleted_flag = 0
  AND $table.order_cancel_flag=0
  AND assignDate BETWEEN '$search_2' AND '$search_3'
  AND (total_charges_comp > rAmount
  OR total_charges_comp =0)";}
$result = mysqli_query($con, $query);

//...................................................................................................................................../////
$htmlTable='';
$htmlTable.='<style>
table {border-collapse: collapse; width:100%;word-wrap: break-word;}
th {border: 1px solid #999; padding: 0.5rem;text-align: left;background-color:#039; color:#FFF;font-weight:bold;word-wrap: break-word;}
td {border: 1px solid #999; padding: 0.5rem;text-align: left;word-wrap: break-word;}
</style>
<div>
<h2 align="center"><u>Interpreter Pending Invoices Report</u></h2>
<p align="right">Report  Date: '.$misc->sys_date().'<br />
  Date  Range: Date From '.$misc->dated($search_2).' Date To '.$misc->dated($search_3).'</p>
</div>
<p>Interpreter(s) Selected</p>
<table class="aa" border="1" cellspacing="0" cellpadding="0" style="width:250px">
  <tr>
    <td width="200" valign="top">'.$search_1.'</td>
  </tr>
</table>
<table>
<thead>
<tr>
 	<th style="width:35px;">Sr.No</th>
    <th>Interpreter Name</th>
	<th>Invoice Number</th>
    <th>Assignment Date</th>
    <th>Language</th>
    <th>Invoice Total</th>	
	</tr>';
while($row = mysqli_fetch_assoc($result)){$vat=$row["vat"];$vat_total_charges_comp=$row["total_charges_comp"]+$vat;$total_vat_total_charges_comp=$vat_total_charges_comp + $total_vat_total_charges_comp;
$htmlTable .='<tr>';
$htmlTable .='<td>'.$i.'</td>';
		$htmlTable .='<td>'.$row["name"].'</td>';
		$htmlTable .='<td>'.$row["invoiceNo"].'</td>';
      	$htmlTable .='<td>'.$misc->dated($row['assignDate']).'</td>';
		$htmlTable .='<td>'.$row["source"].'</td>';
		$htmlTable .='<td>'.$misc->numberFormat_fun($vat_total_charges_comp).'</td>
</tr>';
$i++;}

$htmlTable .='<tr>';
		$htmlTable .='<td></td>';
		$htmlTable .='<td></td>';
		$htmlTable .='<td></td>';
		$htmlTable .='<td></td>';
		$htmlTable .='<td>Total</td>';
		$htmlTable .='<td>'.$misc->numberFormat_fun($total_vat_total_charges_comp).'</td>
		</tr>';
		
$htmlTable.='</table>';

list($a,$b)=explode('.',basename(__FILE__));
header("Content-Type: application/xls");
header("Content-Disposition: attachment; filename=".$a.".xls");  
echo $htmlTable;
?>