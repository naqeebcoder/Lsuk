<?php include '../../db.php';include_once ('../../class.php'); $excel=@$_GET['excel'];session_start();$UserName=$_SESSION['UserName'];$prv=$_SESSION['prv']; $search_1=@$_GET['search_1'];$search_2=@$_GET['search_2'];$search_3=@$_GET['search_3'];$search_4=$_GET['search_4'];
$i=1;$table='interpreter';$total_charges_comp=0;$C_otherCost=0;$g_total=0;$g_vat=0;$C_otherCost=0;$non_vat=0;$comp_name=$acttObj->unique_data('comp_reg','name','abrv',$search_1);
if($search_4=='Management'){
	$query_outer="SELECT bz_credit.creditId, bz_credit.mode, bz_credit.bz_credit from bz_credit
	where bz_credit.orgName='$search_1' and bz_credit.creditId='$search_4' and bz_credit.creditId<>'' 
	";
}else{$query_outer="SELECT bz_credit.creditId, bz_credit.mode, bz_credit.bz_credit from bz_credit
	where bz_credit.orgName='$search_1' and bz_credit.creditId<>'' 
	";}
$result_outer = mysqli_query($con, $query_outer);

$htmlTable='';
$htmlTable.='<style>
table {border-collapse: collapse; width:670px;}
th {border: 1px solid #999; padding: 0.5rem;text-align: left;background-color:#039; color:#FFF; font-weight:bold;}
td {border: 1px solid #999; padding: 0.5rem;text-align: left;}
</style>
<div>';
$htmlTable .='<span align="right"> Date: '.$misc->sys_date(). '</span>';
$htmlTable .='<h2 style="text-decoration:underline; text-align:center">Business Credit Report for "'.$comp_name.'</h2>
<p>Order Report<br/>Date Range:' .$misc->dated($search_2). 'to' .$misc->dated($search_3). '</p>
</div>

<table>
<thead>
<tr>
 	<th style="background-color:#039;color:#FFF;">Sr.No</th>
    <th style="background-color:#039;color:#FFF;">Category</th>
	<th style="background-color:#039;color:#FFF;">Business Credit Order # </th>
	<th style="background-color:#039;color:#FFF;">Amount </th>
	<th style="background-color:#039;color:#FFF;">Invoice #</th>
    <th style="background-color:#039;color:#FFF;">Amount</th>
 </tr>

</thead>';
while($row_outer = mysqli_fetch_assoc($result_outer)){$creditId=$row_outer["creditId"];$mode=$row_outer["mode"];$bz_credit=$row_outer["bz_credit"];
   
		$htmlTable .=' <tr><td></td>
		<td></td>';
		$htmlTable .='<td>'.$creditId.'</td>';
		$htmlTable .='<td>'.$bz_credit.'</td>';
		$htmlTable .='<td></td>';
		$htmlTable .='<td></td>
    </tr>';

$query="SELECT interpreter.invoiceNo,(interpreter.total_charges_comp + interpreter.C_otherexpns + interpreter.total_charges_comp * interpreter.cur_vat) as total_charges_comp, 'Interpreter' as tbl from interpreter	
	where interpreter.orgName='$search_1' and interpreter.creditId='$creditId' and interpreter.porder <>'' and interpreter.assignDate BETWEEN '$search_2' AND '$search_3'
	union
	SELECT telephone.invoiceNo,(telephone.total_charges_comp + telephone.C_otherCharges + telephone.total_charges_comp * telephone.cur_vat) as total_charges_comp, 'Telephone' as tbl from telephone	
	where telephone.orgName='$search_1' and telephone.creditId='$creditId' and telephone.porder <>'' and telephone.assignDate BETWEEN '$search_2' AND '$search_3'
	union
	SELECT translation.invoiceNo,(translation.total_charges_comp + translation.otherCharg + translation.total_charges_comp * translation.cur_vat) as total_charges_comp, 'translation' as tbl from translation	
	where translation.orgName='$search_1' and translation.creditId='$creditId' and translation.porder <>'' and translation.asignDate BETWEEN '$search_2' AND '$search_3'
	
	";

$result = mysqli_query($con, $query);
while($row = mysqli_fetch_assoc($result)){
	
   $htmlTable .='<tr>';
      	$htmlTable .='<td>'.$i.'</td>';
		$htmlTable .='<td>'.$row["tbl"].'</td>';
		$htmlTable .='<td></td>';
		$htmlTable .='<td></td>';
		$htmlTable .='<td>'.$row["invoiceNo"].'</td>';
		$htmlTable .='<td>'.$misc->numberFormat_fun($row["total_charges_comp"]).'</td>
   </tr>';
 $i++;}}
$htmlTable.='</table>';
list($a,$b)=explode('.',basename(__FILE__));
header("Content-Type: application/xls");
header("Content-Disposition: attachment; filename=".$a.".xls");  
echo $htmlTable;