<?php include '../../db.php';include_once ('../../class.php');$excel=@$_GET['excel']; $search_1=@$_GET['search_1'];$search_2=@$_GET['search_2'];$search_3=@$_GET['search_3'];
$i=1;$table='translation';
//...................................................For Multiple Selection...................................\\
 $arr_source = explode(',', $search_1);$_words_source = implode("' OR source like '", $arr_source);
//......................................\\//\\//\\//\\//........................................................\\
if(!empty($search_1)){
$query="SELECT $table.*, interpreter_reg.name,interpreter_reg.city FROM $table
	   inner join interpreter_reg on $table.intrpName = interpreter_reg.id
	   where $table.deleted_flag = 0 and $table.order_cancel_flag=0 and asignDate between '$search_2' and '$search_3' 
	   and ($table.source like '%$_words_source%')
	   order by source";
	   }
	   else
	   {
		   $query="SELECT $table.*, interpreter_reg.name,interpreter_reg.city FROM $table
	   inner join interpreter_reg on $table.intrpName = interpreter_reg.id
	   where $table.deleted_flag = 0 and $table.order_cancel_flag=0 and asignDate between '$search_2' and '$search_3'
	   order by source";
	   }
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
$htmlTable .='<h2 style="text-decoration:underline; text-align:center">Language Report from "Translation" Interpreter</h2>
<p>Interpreting Language Report<br/>Date Range:' .$misc->dated($search_2). 'to' .$misc->dated($search_3). '</p>
</div>

<table>
<thead>
<tr>
	<th style="background-color:#039;color:#FFF;">Sr.No</th>
	<th style="background-color:#039;color:#FFF;">Job Date </th>
    <th style="background-color:#039;color:#FFF;">Language</th>
    <th style="background-color:#039;color:#FFF;">Client Name</th>
    <th style="background-color:#039;color:#FFF;">Interpreter Name</th>
    <th style="background-color:#039;color:#FFF;">Interpreter City</th>';

	$runcompany="";
	$nowcompany="";
	$mapCoTotals=array();
	ZeroCompTotal($mapCoTotals);
	
	$loop=0;
	
	while($row = mysqli_fetch_assoc($result))
	{
		$nowcompany=$row["source"];
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
$htmlTable .='<td>'.date_format(date_create($row["asignDate"]), 'd-m-Y').'</td>';
$htmlTable .='<td>'.$row["source"].'</td>';
$htmlTable .='<td>'.$row["orgName"].'</td>';
$htmlTable .='<td>'.$row["name"].'</td>';
$htmlTable .='<td>'.$row["city"].'</td>
</tr>';
$i++;
}
if ($loop!=0)
  ShowCompTotal($mapCoTotals,$htmlTable);

$htmlTable.='</table>';

list($a,$b)=explode('.',basename(__FILE__));
header("Content-Type: application/xls");
header("Content-Disposition: attachment; filename=".$a.".xls");  
echo $htmlTable;

function ZeroCompTotal(&$map)
{
	$map["i"]=0;
}

function UpdateCompTotal(&$map,&$row,$i)
{
	$map["i"]++;
}

function ShowCompTotal(&$map,&$tbl)
{
	global $misc;	

	$tbl.=<<<EOD
	<tr>
  
  <td style="font-weight:bold;" colspan="4" align="right"> Interp. Tot:</td>
  <td>{$map["i"]}</td>
  <td></td>
  </tr>				
EOD;
}
?>
