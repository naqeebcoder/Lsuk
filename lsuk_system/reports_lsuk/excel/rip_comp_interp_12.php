<?php include '../../db.php';
include_once ('../../class.php'); 
$excel=@$_GET['excel'];
$search_1=$_GET['search_1'];
$search_2=@$_GET['search_2'];
$search_3=@$_GET['search_3'];
if(!empty($search_1)){
$arr = explode(',', $search_1);
    $_words = " AND orgName IN ('".implode("','", $arr)."')";
}else{
    $_words='';
}
$x=0;
error_reporting(0);
$query="SELECT interpreter.orderCancelatoin,interpreter.porder,interpreter.nameRef,interpreter.rDate,interpreter.inchPerson,interpreter.inchEmail,interpreter.bookeddate,interpreter.bookedVia,interpreter.bookedtime,interpreter.orgRef,interpreter.id,interpreter.invoiceNo,interpreter.assignDate,interpreter.source,round(interpreter.C_otherexpns,2) as other_expenses,interpreter.rAmount,interpreter.postCode as postCode,round(interpreter.total_charges_comp*cur_vat,2) as cur_vatt,interpreter.total_charges_comp,round(((interpreter.total_charges_comp*cur_vat)+interpreter.total_charges_comp+interpreter.C_otherexpns),2) as invoice_cost, interpreter_reg.name, comp_reg.name as orgNam,'Face to Face' as type FROM interpreter
	   inner join interpreter_reg on interpreter.intrpName = interpreter_reg.id 
	   inner join comp_reg on interpreter.orgName = comp_reg.abrv
	   where interpreter.deleted_flag = 0 and interpreter.order_cancel_flag=0 and 
	   interpreter.assignDate between '$search_2' and '$search_3' $_words
	   UNION ALL 
	   SELECT telephone.orderCancelatoin,telephone.porder,telephone.nameRef,telephone.rDate,telephone.inchPerson,telephone.inchEmail,telephone.bookeddate,telephone.bookedVia,telephone.bookedtime,telephone.orgRef,telephone.id,telephone.invoiceNo,telephone.assignDate,telephone.source,'0' as other_expenses,telephone.rAmount,'' as postCode,round(telephone.total_charges_comp*cur_vat,2) as cur_vatt,telephone.total_charges_comp,round(((telephone.total_charges_comp*cur_vat)+telephone.total_charges_comp),2) as invoice_cost, interpreter_reg.name, comp_reg.name as orgNam,'Telephone' as type FROM telephone
	   inner join interpreter_reg on telephone.intrpName = interpreter_reg.id 
	   inner join comp_reg on telephone.orgName = comp_reg.abrv
	   where telephone.deleted_flag = 0 and telephone.order_cancel_flag=0 and 
	   telephone.assignDate between '$search_2' and '$search_3' $_words
	   UNION ALL 
	   SELECT translation.orderCancelatoin,translation.porder,translation.nameRef,translation.rDate,translation.inchContact,translation.inchEmail,translation.bookeddate,translation.bookedVia,translation.bookedtime,translation.orgRef,translation.id,translation.invoiceNo,translation.asignDate,translation.source,'0' as other_expenses,translation.rAmount,'' as postCode,round(translation.total_charges_comp*cur_vat,2) as cur_vatt,translation.total_charges_comp,round(((translation.total_charges_comp*cur_vat)+translation.total_charges_comp),2) as invoice_cost, interpreter_reg.name, comp_reg.name as orgNam,'Translation' as type FROM translation
	   inner join interpreter_reg on translation.intrpName = interpreter_reg.id 
	   inner join comp_reg on translation.orgName = comp_reg.abrv
	   where translation.deleted_flag = 0 and translation.order_cancel_flag=0 and 
	   translation.asignDate between '$search_2' and '$search_3' $_words";
$result = mysqli_query($con, $query);
$htmlTable='';
$htmlTable.='<style>
table {border-collapse: collapse; width:100%;word-wrap: break-word;}
th {border: 1px solid #999; padding: 0.5rem;text-align: left;background-color:#039; color:#FFF;font-weight:bold;word-wrap: break-word;}
td {border: 1px solid #999; padding: 0.5rem;text-align: left;word-wrap: break-word;}
</style>
<div>
<h2 align="center"><u>Overall Interpreting General Report ('.$search_1.')</u></h2>
<p align="right">Report  Date: '.$misc->sys_date().'<br />
  Date  Range: Date From '.$misc->dated($search_2).' Date To '.$misc->dated($search_3).'</p>
</div>
<p>Orgnaization(s) Selected</p>
<table class="aa" border="1" cellspacing="1" style="width:700px">
  <tr>
    <td valign="top">'.$search_1.'</td>
  </tr>
</table><br/><br/>
<table>';
$htmlTable .='  <tr>
 	<th style="width:45px;" style="background-color:#003399; color:#FFF;">Sr. No.</th>
    <th style="background-color:#003399; color:#FFF;">Assignment Date</th>
    <th style="background-color:#003399; color:#FFF;">Job Type</th>
    <th style="background-color:#003399; color:#FFF;">Language</th>
	<th style="background-color:#003399; color:#FFF;">Job Ref</th>
	<th style="background-color:#003399; color:#FFF;">Client Ref</th>
    <th style="background-color:#003399; color:#FFF;">Post Code</th>
    <th style="background-color:#003399; color:#FFF;">Linguistic</th>
    <th width="10%" style="background-color:#003399; color:#FFF;">Invoice No</th>
    <th style="background-color:#003399; color:#FFF;">Name</th>
    <th style="background-color:#003399; color:#FFF;">Email</th>
    <th style="background-color:#003399; color:#FFF;">B Date</th>
	<th style="background-color:#003399; color:#FFF;">B Time</th>
	<th style="background-color:#003399; color:#FFF;">Via</th>
    <th style="background-color:#003399; color:#FFF;">Invoice Cost</th>
    <th style="background-color:#003399; color:#FFF;">Purch.Order</th>
	<th style="background-color:#003399; color:#FFF;">Pay Date</th>
  </tr>';


  $runcompany="";
  $nowcompany="";
	
  $mapCoTotals=array();
  ZeroCompTotal($mapCoTotals);

  $f2f_count=0;
$tel_count=0;
$tr_count=0;
$f2f_cancel_count=0;
$tel_cancel_count=0;
$tr_cancel_count=0;
$loop=0;
$cancel_row='';
  while($row = mysqli_fetch_assoc($result))
  {
	 	$counter++;
	if($row['type']=='Face to face'){
	    $f2f_count++;
	}else if($row['type']=='Telephone'){
	    $tel_count++;
	}else{
	    $tr_count++;
	}
	if($row['type']=='Face to face' && $row["orderCancelatoin"]==1){
	    $f2f_cancel_count++;
	}
	if($row['type']=='Telephone' && $row["orderCancelatoin"]==1){
	    $tel_cancel_count++;
	}
	if($row['type']=='Translation' && $row["orderCancelatoin"]==1){
	    $tr_cancel_count++;
	}
	$other_expenses=$row["other_expenses"] + $other_expenses;
	$total_charges_comp=$row["total_charges_comp"] + $total_charges_comp;
	$cur_vatt=$row["cur_vatt"] + $cur_vatt;
	$invoice_cost=$row["invoice_cost"] + $invoice_cost;
	$porder=$row["porder"];
	$pay_date = '';
	if($row["rAmount"]>0)
	{
		$invstst='Paid';
		$pay_date = $row['rDate'];
	}
	else
	{
		$invstst='Un-Paid';
	}
	  
	  $nowcompany=$row["orgName"];
	  if ($loop==0)
		  OrgOutput::WriteTR($nowcompany,$runcompany,$htmlTable);

	  $loop++;
	  if ($nowcompany<>$runcompany)
	  {
		ShowCompTotal($mapCoTotals,$htmlTable);
		ZeroCompTotal($mapCoTotals);
	  }
	  OrgOutput::WriteTR($nowcompany,$runcompany,$htmlTable);
  
	  UpdateCompTotal($mapCoTotals,$row);
      if($row["orderCancelatoin"]==1){
        $cancel_row=' style="background-color:#ff9898;"';
    }else{
        $cancel_row=' ';
    }
	$b_time = date('H:i a',strtotime($row['bookedtime']));
$htmlTable .='<tr '.$cancel_row.'>';
$htmlTable .='<td width="5%">'.$i.'</td>';
		$htmlTable .='<td>'.$row["assignDate"].'</td>';
		$htmlTable .='<td>'.$row["type"].'</td>';
		$htmlTable .='<td>'.$row["source"].'</td>';
		$htmlTable .='<td>'.$row["nameRef"].'</td>';
		$htmlTable .='<td>'.$row["orgRef"].'</td>';
		$htmlTable .='<td>'.$row["postCode"].'</td>';
		$htmlTable .='<td>'.$row["name"].'</td>';
		$htmlTable .='<td width="10%">'.$row["invoiceNo"].'</td>';
		$htmlTable .='<td>'.$row["inchPerson"].'</td>';
		$htmlTable .='<td>'.$row["inchEmail"].'</td>';
		$htmlTable .='<td>'.$misc->dated($row["bookeddate"]).'</td>';
		$htmlTable .='<td>'.$b_time.'</td>';
		$htmlTable .='<td>'.$row["bookedVia"].'</td>';
		$htmlTable .='<td>'.$row["invoice_cost"].'</td>';
		$htmlTable .='<td>'.$porder.'</td>';
		$htmlTable .='<td>'.$pay_date .'</td>
		
</tr>';
$i++;
}
ShowCompTotal($mapCoTotals,$htmlTable);
$total_jobs=$f2f_count+$tel_count+$tr_count;
$total_cencelled=$f2f_cancel_count+$tel_cancel_count+$tr_cancel_count;
$htmlTable .='<tr>';
$htmlTable .='<td></td>';
$htmlTable .='<td></td>';
$htmlTable .='<td></td>';
$htmlTable .='<td></td>';
$htmlTable .='<td></td>';
$htmlTable .='<td></td>';
$htmlTable .='<td></td>';
$htmlTable .='<td><b>'.$misc->numberFormat_fun($total_charges_comp).'</b></td>';
$htmlTable .='<td><b>'.$misc->numberFormat_fun($cur_vatt).'</b></td>';
$htmlTable .='<td><b>'.$misc->numberFormat_fun($other_expenses).'</b></td>';
$htmlTable .='<td><b>'.$misc->numberFormat_fun($invoice_cost).'</b></td>';	
$htmlTable .='<td></td><td></td>
</tr>';

$htmlTable .='</table><br><br>';
$htmlTable .='<table class="aa" border="1">
  <tr>
    <td width="125">Total Face to face Jobs</td>
    <td width="78">'.$f2f_count.'</td>
    <td width="160" border="0"></td>
    <td width="170">Total Face to face Cancelled Jobs</td>
    <td width="80">'.$f2f_cancel_count.'</td>
  </tr>
  <tr>
    <td width="125">Total Telephone Jobs</td>
    <td width="78">'.$tel_count.'</td>
    <td width="160" border="0"></td>
    <td width="170">Total Telephone Cancelled Jobs</td>
    <td width="80">'.$tel_cancel_count.'</td>
  </tr>
  <tr>
    <td width="125">Total Translation Jobs</td>
    <td width="78">'.$tr_count.'</td>    
    <td width="160" border="0"></td>
    <td width="170">Total Translation Cancelled Jobs</td>
    <td width="80">'.$tr_cancel_count.'</td>
  </tr><tr>
    <td width="125">Total Jobs</td>
    <td width="78"><b>'.$total_jobs.'</b></td>
    <td width="160" border="0"></td>
    <td width="170">Total Cancelled Jobs</td>
    <td width="80"><b>'.$total_cencelled.'</b></td>
  </tr>
</table>';
list($a,$b)=explode('.',basename(__FILE__));
//$new_name=$a.'_'.implode('_',$arr);
header("Content-Type: application/xls");
header("Content-Disposition: attachment; filename=".$a.".xls"); 
echo $htmlTable;

function ZeroCompTotal(&$map)
{
	$map["invoice_cost"] = 0;
	$map["other_expenses"] =0;
	$map["total_charges_comp"] =0;
	$map["cur_vatt"] =0;
}

function UpdateCompTotal(&$map,&$row)
{
	$map["invoice_cost"]+=$row["invoice_cost"];
	$map["other_expenses"]+=$row["other_expenses"];
	$map["total_charges_comp"]+=$row["total_charges_comp"];
	$map["cur_vatt"]+=$row["cur_vatt"]; 
}

function ShowCompTotal(&$map,&$tbl)
{
	global $misc;

	$tbl.=<<<EOD
	<tr></tr>				
EOD;

}


?>