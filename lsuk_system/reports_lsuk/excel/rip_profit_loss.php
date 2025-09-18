<?php include '../../db.php';include_once ('../../class.php'); $excel=@$_GET['excel'];$search_1=@$_GET['search_1'];$search_2=@$_GET['search_2'];$search_3=@$_GET['search_3'];
$i=1;$total_interp=0;$total_comp=0;$total_non_vat=0;$total_vat=0;$total_all=0;$total_profit=0;$vat_total_charges_comp=0;
$query="SELECT invoiceNo, 
       invoic_date, 
       orgName, 
       intrpname, 
       source, 
       assignDate, 
       paid_interp, 
       C_otherexpns, 
       total_charges_comp, 
       total_charges_interp, 
	   cur_vat,
       interpreter_reg.name 
FROM   interpreter 
       INNER JOIN interpreter_reg 
               ON interpreter.intrpname = interpreter_reg.id 
WHERE  interpreter.deleted_flag = 0 
       AND interpreter.order_cancel_flag = 0 
       AND assignDate BETWEEN '$search_2' AND '$search_3' 
UNION 
SELECT invoiceNo, 
       invoic_date, 
       orgName, 
       intrpname, 
       source, 
       assignDate, 
       paid_interp, 
       C_othercharges AS C_otherexpns, 
       total_charges_comp, 
       total_charges_interp, 
	   cur_vat,
       interpreter_reg.name 
FROM   telephone 
       INNER JOIN interpreter_reg 
               ON telephone.intrpname = interpreter_reg.id 
WHERE  telephone.deleted_flag = 0 
       AND telephone.order_cancel_flag = 0 
       AND assignDate BETWEEN '$search_2' AND '$search_3' 
UNION 
SELECT invoiceNo, 
       invoic_date, 
       orgName, 
       intrpname, 
       source, 
       asigndate    AS assignDate, 
       paid_interp, 
       C_othercharg AS C_otherexpns, 
       total_charges_comp, 
       total_charges_interp, 
	   cur_vat,
       interpreter_reg.name 
FROM   translation 
       INNER JOIN interpreter_reg 
               ON translation.intrpname = interpreter_reg.id 
WHERE  translation.deleted_flag = 0 
       AND translation.order_cancel_flag = 0 
       AND asigndate BETWEEN '$search_2' AND '$search_3' 
ORDER  BY invoiceNo ";

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
$htmlTable .='<h2 style="text-decoration:underline; text-align:center">LSUK Profit & Loss Report</h2>
<p>Profit & Loss Report<br/>Date Range:' .$misc->dated($search_2). 'to' .$misc->dated($search_3). '</p>
</div>

<table>
<thead>
<tr>
	<th style="background-color:#039;color:#FFF;">Sr.No</th>
	<th style="background-color:#039;color:#FFF;">Invoice Number</th>
	<th style="background-color:#039;color:#FFF;">Invoice Date</th>
    <th style="background-color:#039;color:#FFF;">Assignment Date</th>
    <th style="background-color:#039;color:#FFF;">Client Name</th>
    <th style="background-color:#039;color:#FFF;">Interpreter Name</th>
    <th style="background-color:#039;color:#FFF;">Language</th>
    <th style="background-color:#039;color:#FFF;">Amount Paid to Interpreter</th>
    <th style="background-color:#039;color:#FFF;">Amount Invoiced to the Client (Net)</th>
    <th style="background-color:#039;color:#FFF;">Non Vat able</th>
    <th style="background-color:#039;color:#FFF;">VAT</th>
    <th style="background-color:#039;color:#FFF;">Total</th>
    <th style="background-color:#039;color:#FFF;">Profit or Loss</th>';
while($row = mysqli_fetch_assoc($result)){
$total_charges_interp=$row["total_charges_interp"];
$vat_total_charges_comp=$row["total_charges_comp"]*$row["cur_vat"];
$total_charges_comp_C_otherexpns=round($row["total_charges_comp"] + $row["C_otherexpns"]);
$total=$row["total_charges_comp"] + $row["C_otherexpns"] - $row["total_charges_interp"];
$htmlTable .='<tr>';
$htmlTable .='<td>'.$i.'</td>';
$htmlTable .='<td>'.$row["invoiceNo"].'</td>';
$htmlTable .='<td>'.$row["invoic_date"].'</td>';
$htmlTable .='<td>'.$row["assignDate"].'</td>';
$htmlTable .='<td>'.$row["orgName"].'</td>';
$htmlTable .='<td>'.$row["name"].'</td>';
$htmlTable .='<td>'.$row["source"].'</td>';
$htmlTable .='<td>'.$row["total_charges_interp"].'</td>';
$htmlTable .='<td>'.$row["total_charges_comp"].'</td>';
$htmlTable .='<td>'.$row["C_otherexpns"].'</td>';
$htmlTable .='<td>'.$vat_total_charges_comp.'</td>';
$htmlTable .='<td>'.$total_charges_comp_C_otherexpns.'</td>';
$htmlTable .='<td>'.$misc->numberFormat_fun($total).'</td>
</tr>';
$i++;
 $total_interp=$row["total_charges_interp"] + $total_interp;
 $total_comp=$row["total_charges_comp"] + $total_comp;
 $total_non_vat=$row["C_otherexpns"]+$total_non_vat;
 $total_vat=$vat_total_charges_comp + $total_vat;
 $total_all=$total_charges_comp_C_otherexpns+$total_all;
 $total_profit=$total + $total_profit;
 }


$htmlTable .='<tr>';
$htmlTable .='<td></td>';
$htmlTable .='<td></td>';
$htmlTable .='<td></td>';
$htmlTable .='<td></td>';
$htmlTable .='<td></td>';
$htmlTable .='<td></td>';
$htmlTable .='<td>TOTAL</td>';
$htmlTable .='<td>'.$misc->numberFormat_fun($total_interp).'</td>';
$htmlTable .='<td>'.$misc->numberFormat_fun($total_comp).'</td>';
$htmlTable .='<td>'.$misc->numberFormat_fun($total_non_vat).'</td>';
$htmlTable .='<td>'.$misc->numberFormat_fun($total_vat).'</td>';
$htmlTable .='<td>'.$misc->numberFormat_fun($total_all).'</td>';
$htmlTable .='<td>'.$misc->numberFormat_fun($total_profit).'</td>
</tr>';
$htmlTable.='</table>';

list($a,$b)=explode('.',basename(__FILE__));
header("Content-Type: application/xls");
header("Content-Disposition: attachment; filename=".$a.".xls");  
echo $htmlTable;
?>