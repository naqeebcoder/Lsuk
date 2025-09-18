<?php include '../../db.php';
include_once ('../../class.php'); 
$excel=@$_GET['excel'];
$search_1=@$_GET['search_1']; 
$search_2=@$_GET['search_2']; 
$search_3=@$_GET['search_3'];
$mngFg_check=@$_GET['mng_check'];
$i=1;
$table='expence';
$amoun=0;
$netamount=0;
$vat=0;
$nonvat=0;
$exp_titles=$acttObj->read_specific('GROUP_CONCAT(title) as exp_titles','expence_list','id IN ('.$search_1.')');

$query="SELECT $table.*,expence_list.title as exp_title FROM expence,expence_list where $table.type_id=expence_list.id AND expence.type_id IN ($search_1) and $table.billDate between '$search_2' and '$search_3' and $table.deleted_flag=0 order by expence_list.title";
$result = mysqli_query($con, $query);
if($mngFg_check==1){
  $exp_titles2=$acttObj->read_specific('GROUP_CONCAT(title) as exp_titles','expence_list','id IN (16,17,35,36)');
  $query2="SELECT $table.*,expence_list.title as exp_title FROM expence,expence_list where $table.type_id=expence_list.id AND expence.type_id IN (16,17,35,36) and $table.billDate between '$search_2' and '$search_3' and $table.deleted_flag=0 order by expence_list.title";
  $result2 = mysqli_query($con, $query2);
}

$extra_exp_rows = array();

$credit_notes=$acttObj->read_all("*","credit_notes"," dated between '$search_2' and '$search_3'");
$extra_exp_index=0;
while($row_crd_note = $credit_notes->fetch_assoc()){
  $json_data=json_decode($row_crd_note['data'], true);
  $extra_exp_rows[$extra_exp_index]['exp_title'] =  'Credit Note';
  $extra_exp_rows[$extra_exp_index]['billDate'] = $row_crd_note['dated'];
  $extra_exp_rows[$extra_exp_index]['details'] = $row_crd_note['order_type'];
  $comp_id = $json_data['order_company_id'];
  $extra_exp_rows[$extra_exp_index]['comp'] = $acttObj->read_specific("abrv","comp_reg"," id='$comp_id' ")['abrv'];
  $extra_exp_rows[$extra_exp_index]['voucher'] = $json_data['invoiceNo'];
  $extra_exp_rows[$extra_exp_index]['netamount'] = $json_data['order_type'] == 'f2f' ? $json_data['total_charges_comp'] + $json_data['C_otherexpns'] : $json_data['total_charges_comp'];
  $extra_exp_rows[$extra_exp_index]['nonvat'] =0;
  $extra_exp_rows[$extra_exp_index]['vat'] = $json_data['cur_vat'] * $json_data['total_charges_comp'];
  $extra_exp_rows[$extra_exp_index]['amoun'] = $extra_exp_rows[$extra_exp_index]['netamount'] + $extra_exp_rows[$extra_exp_index]['vat'];
  $extra_exp_index++;
}
$query="SELECT ROUND(interpreter.total_charges_comp,2) as bad_debt_amount, ROUND((interpreter.total_charges_comp*interpreter.cur_vat),2) as vat_bad_debt,'face 2 face' as detail,assignDate as billdate,C_otherexpns as others,orgName,invoiceNo FROM interpreter WHERE interpreter.disposed_of='1' and ROUND(interpreter.total_charges_comp,2)>0 and interpreter.assignDate between '$search_2' and '$search_3' UNION ALL SELECT ROUND(telephone.total_charges_comp,2) as bad_debt_amount,ROUND((telephone.total_charges_comp*telephone.cur_vat),2) as vat_bad_debt,'telephone' as detail,assignDate as billdate,C_otherCharges as others,orgName,invoiceNo FROM telephone WHERE telephone.disposed_of='1' and ROUND(telephone.total_charges_comp,2)>0  and telephone.assignDate between '$search_2' and '$search_3' UNION ALL SELECT ROUND(translation.total_charges_comp,2) as bad_debt_amount,ROUND((translation.total_charges_comp*translation.cur_vat),2) as vat_bad_debt,'translation' as detail,asignDate as billdate,C_otherCharg as others,orgName,invoiceNo FROM translation WHERE translation.disposed_of='1' and ROUND(translation.total_charges_comp,2)>0 and translation.asignDate between '$search_2' and '$search_3'";
$get_bad_debt=mysqli_query($con,$query);
while($bd_row = mysqli_fetch_assoc($get_bad_debt)){
  $extra_exp_rows[$extra_exp_index]['exp_title'] =  'Bad Debt';
  $extra_exp_rows[$extra_exp_index]['billDate'] = $bd_row['billdate'];
  $extra_exp_rows[$extra_exp_index]['details'] = $bd_row['detail'];
  $extra_exp_rows[$extra_exp_index]['comp'] = $bd_row['orgName'];
  $extra_exp_rows[$extra_exp_index]['voucher'] = $bd_row['invoiceNo'];
  $extra_exp_rows[$extra_exp_index]['netamount'] = $bd_row['detail'] == 'face 2 face' ? $bd_row['bad_debt_amount'] + $bd_row['others'] : $bd_row['bad_debt_amount'];
  $extra_exp_rows[$extra_exp_index]['nonvat'] =0;
  $extra_exp_rows[$extra_exp_index]['vat'] = $bd_row['vat_bad_debt'];
  $extra_exp_rows[$extra_exp_index]['amoun'] = $extra_exp_rows[$extra_exp_index]['netamount']+$extra_exp_rows[$extra_exp_index]['vat'];
  $extra_exp_index++;
}



$htmlTable='';
$htmlTable.='<style>
table {border-collapse: collapse; width:670px;}
th {border: 1px solid #999; padding: 0.5rem;text-align: left;background-color:#039; color:#FFF; font-weight:bold;}
td {border: 1px solid #999; padding: 0.5rem;text-align: left;}
</style>
<div>';
$htmlTable .='<span align="right"> Date: '.$misc->sys_date(). '</span>';
$htmlTable .='<h2 style="text-decoration:underline; text-align:center">LSUK Expanses Report for '.$exp_titles['exp_titles'].'</h2>
<p>Expanses Report<br/>Date Range:' .$misc->dated($search_2). 'to' .$misc->dated($search_3). '</p>
</div>

<table>
<thead>
<tr>
	<th style="background-color:#039;color:#FFF;">Sr.No</th>
    <th style="background-color:#039;color:#FFF;">Bill Date</th>
    <th style="background-color:#039;color:#FFF;">Type</th>
    <th style="background-color:#039;color:#FFF;">Details</th>
    <th style="background-color:#039;color:#FFF;">Voucher #</th>
    <th style="background-color:#039;color:#FFF;">Company</th>
    <th style="background-color:#039;color:#FFF;">Net Amount</th>
    <th style="background-color:#039;color:#FFF;">VAT</th>
    <th style="background-color:#039;color:#FFF;">Non VAT</th>
    <th style="background-color:#039;color:#FFF;">Total Amount</th>';

    $runcompany="";
    $nowcompany="";
    $mapCoTotals=array();
    ZeroCompTotal($mapCoTotals);
    $loop=0;
    
    
while($row = mysqli_fetch_assoc($result))
{ 
    $amoun=$row["amoun"] + $amoun;$netamount=$row["netamount"] + $netamount;
    $vat=$row["vat"] + $vat;
    $nonvat=$row["nonvat"] + $nonvat;

	$nowcompany=$row["title"];
	if ($loop==0)
	OrgOutput::WriteTR($nowcompany,$runcompany,$htmlTable);
  
  $loop++;
  if ($runcompany!=$nowcompany)
  {
	ShowCompTotal($mapCoTotals,$htmlTable);
	ZeroCompTotal($mapCoTotals);
  }
  OrgOutput::WriteTR($nowcompany,$runcompany,$htmlTable);
  
  UpdateCompTotal($mapCoTotals,$row,$i);




$htmlTable .='<tr>';
$htmlTable .='<td>'.$i.'</td>';
$htmlTable .='<td>'.$misc->dated($row["billDate"]).'</td>';
$htmlTable .="<td>".$row["exp_title"]."</td>";
$htmlTable .='<td>'.$row["details"].'</td>';
$htmlTable .='<td>'.$row["voucher"].'</td>';
$htmlTable .='<td>'.$row["comp"].'</td>';
$htmlTable .='<td>'.$row["netamount"].'</td>';
$htmlTable .='<td>'.$row["vat"].'</td>';
$htmlTable .='<td>'.$row["nonvat"].'</td>';
$htmlTable .='<td>'.$row["amoun"].'</td>
</tr>';
$i++;
}
// ShowExtraExp($mapCoTotals,$extra_exp_rows,$i,$htmlTable,$amoun,$netamount,$vat,$nonvat);
if ($loop!=0)
  ShowCompTotal($mapCoTotals,$htmlTable);


$htmlTable .='<tr style="font-weight:bold;">';
$htmlTable .='<td></td>';
$htmlTable .='<td>Total</td>';
$htmlTable .='<td></td>';
$htmlTable .='<td></td>';
$htmlTable .='<td></td>';
$htmlTable .='<td></td>';
$htmlTable .='<td>'.$misc->numberFormat_fun($netamount).'</td>';
$htmlTable .='<td>'.$misc->numberFormat_fun($vat).'</td>';
$htmlTable .='<td>'.$misc->numberFormat_fun($nonvat).'</td>';
$htmlTable .='<td>'.$misc->numberFormat_fun($amoun).'</td>
</tr>';
$htmlTable.='</table>';


if($mngFg_check==1){
  $htmlTable.="
  <p></p>
  <div>
  <h1 align='center'><u>Exluded Expenses / Management Figures</u></h1>
  </div>
  <p>Exluded Expense(s) are Below:</p>
  <table class='aa' border='1' cellspacing='0' cellpadding='0' style='width:250px'>
    <tr>
      <td width='200' valign='top'>".$exp_titles2['exp_titles']."</td>
    </tr>
  </table><br/><br/>";
  $htmlTable.="
  <table>
  <thead>
  <tr>
    <th style='width:35px;'>Sr.No</th>
    <th>Bill Date</th>
    <th>Type</th>
    <th>Details</th>
    <th>Voucher No.</th>
    <th>Company</th>
    <th>Net Amount</th>
    <th>VAT</th>
    <th>Non VAT</th>
    <th>Total Amount</th>
  </tr>
  
  </thead>";
  
  $runcompany="";
  $nowcompany="";
  $mapCoTotals=array();
  ZeroCompTotal($mapCoTotals);
  $i=1;
  $loop=0;
  
  
  while($row2 = mysqli_fetch_assoc($result2))
  {
    $amoun=$row2["amoun"] + $amoun;$netamount=$row2["netamount"] + $netamount;$vat=$row2["vat"] + $vat;
    $nonvat=$row2["nonvat"] + $nonvat;
  
    $nowcompany=$row2["title"];
    if ($loop==0)
    OrgOutput::WriteTR($nowcompany,$runcompany,$htmlTable);
    
    $loop++;
    if ($runcompany!=$nowcompany)
    {
    ShowCompTotal($mapCoTotals,$htmlTable);
    ZeroCompTotal($mapCoTotals);
    }
    OrgOutput::WriteTR($nowcompany,$runcompany,$htmlTable);
    
    UpdateCompTotal($mapCoTotals,$row2,$i);
  
  
  
  $htmlTable.="
      <tr>
          <td style='width:35px;'>".$i."</td>
      <td>".$misc->dated($row2["billDate"])."</td>
      <td>".$row2["exp_title"]."</td>
      <td>".$row2["details"]."</td>
      <td>".$row2["voucher"]."</td>
      <td>".$row2["comp"]."</td>
      <td>".$row2["netamount"]."</td>
      <td>".$row2["vat"]."</td>
      <td>".$row2["nonvat"]."</td>
      <td>".$row2["amoun"]."</td>
      </tr>";
   $i++;
  }
  
  if ($loop!=0)
    ShowCompTotal($mapCoTotals,$htmlTable);
  
  $htmlTable.="
      
  <tr style='font-weight:bold;'>
  <td></td>
  <td>Total</td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td>".$misc->numberFormat_fun($netamount)."</td>
  <td>".$misc->numberFormat_fun($vat)."</td>
  <td>".$misc->numberFormat_fun($nonvat)."</td>
  <td>".$misc->numberFormat_fun($amoun)."</td>
  </tr>
  </table>";
  }


list($a,$b)=explode('.',basename(__FILE__));
header("Content-Type: application/xls");
header("Content-Disposition: attachment; filename=".$a.".xls");  
echo $htmlTable;

function ZeroCompTotal(&$map)
{
	$map["netamount"]=0;
	$map["vat"]=0;
	$map["nonvat"]=0;
	$map["amoun"]=0;
}

function UpdateCompTotal(&$map,&$row,$i)
{
	$map["netamount"]+=$row["netamount"];
	$map["vat"]+=$row["vat"];
	$map["nonvat"]+=$row["nonvat"];
	$map["amoun"]+=$row["amoun"];

}

function UpdateCompTotalExtra(&$map,&$ext_row,$i)
{
	$map["netamount"]+=$ext_row["netamount"];
	// $map["vat"]+=$ext_row["vat"];
	$map["nonvat"]+=$ext_row["nonvat"];
	// $map["amoun"]+=$ext_row["amoun"];
	$map["amoun"]+=$ext_row["netamount"];

}

function ShowExtraExp(&$mapCoTotals,$extra_exp_rows,$i,&$htmlTable,&$amoun,&$netamount,&$vat,&$nonvat)
{
  $counter = $i;
	global $misc;	
  for($i=0;$i<count($extra_exp_rows);$i++){
    $ext_index = $extra_exp_rows[$i];
    $htmlTable.="
    <tr>
    <td style='width:35px;'>".$counter."</td>
		<td>".$extra_exp_rows[$i]["billDate"]."</td>
		<td>".$extra_exp_rows[$i]["exp_title"]."</td>
    <td>".$extra_exp_rows[$i]["details"]."</td>
		<td>".$extra_exp_rows[$i]["voucher"]."</td>
		<td>".$extra_exp_rows[$i]["comp"]."</td>
		<td>".$extra_exp_rows[$i]["netamount"]."</td>
		<td>".$extra_exp_rows[$i]["vat"]."</td>
		<td>".$extra_exp_rows[$i]["nonvat"]."</td>
		<td>".$extra_exp_rows[$i]["netamount"]."</td>
    </tr>";
    UpdateCompTotalExtra($mapCoTotals,$ext_index,$counter);
    $amoun=$ext_index["netamount"] + $amoun;$netamount=$ext_index["netamount"] + $netamount;
    $nonvat=$ext_index["nonvat"] + $nonvat;
    $counter++;
  }  
}

function ShowCompTotal(&$map,&$tbl)
{
	global $misc;	

	$tbl.=<<<EOD
	<tr style="font-weight:bold;">
  <td></td>
  <td>Exp. Tot:</td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td>{$misc->numberFormat_fun($map["netamount"])}</td>
  <td>{$misc->numberFormat_fun($map["vat"])}</td>
  <td>{$misc->numberFormat_fun($map["nonvat"])}</td>
  <td>{$misc->numberFormat_fun($map["amoun"])}</td>
  </tr>				
EOD;
}

?>
