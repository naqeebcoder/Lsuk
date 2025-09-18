<?php include '../../db.php';include_once ('../../class.php'); $excel=@$_GET['excel'];$search_1=@$_GET['search_1'];$search_2=@$_GET['search_2'];$search_3=@$_GET['search_3'];
$i=1;$table='telephone';$total_charges_interp=0; 
//...................................................For Multiple Selection...................................\\
 $arr_intrp = explode(',', $search_1);$_words_intrp = implode("' OR name like '", $arr_intrp);
//......................................\\//\\//\\//\\//........................................................\\
if($search_1){
$query="SELECT $table.*, interpreter_reg.name FROM $table
	   inner join interpreter_reg on $table.intrpName = interpreter_reg.id 
	   where $table.deleted_flag = 0 and $table.order_cancel_flag=0 and $table.assignDate between '$search_2' and '$search_3' 
	   and (interpreter_reg.name like '%$_words_intrp%')
	   order by name";
	   }
else{$query="SELECT $table.*, interpreter_reg.name  FROM $table
	   inner join interpreter_reg on $table.intrpName = interpreter_reg.id 
	   where $table.deleted_flag = 0 and $table.order_cancel_flag=0 and assignDate between '$search_2' and '$search_3'
	   order by name";
	   }
$result = mysqli_query($con, $query);

//...................................................................................................................................../////
$htmlTable='';
$htmlTable.='<style>
table {border-collapse: collapse; width:100%;word-wrap: break-word;}
th {border: 1px solid #999; padding: 0.5rem;text-align: left;background-color:#039; color:#FFF;font-weight:bold;word-wrap: break-word;}
td {border: 1px solid #999; padding: 0.5rem;text-align: left;word-wrap: break-word;}
</style>
<div>
<h2 align="center"><u>Telephone Interpreting General Report (Account Purpose)</u></h2>
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
 	<th style="width:45px;">Sr. No.</th>
    <th>Interpreter Name</th> 
    <th>Assignment Date</th>
    <th>Assignment Time</th>
    <th>Amount Paid to Interpreter</th>
    <th>Payment Date </th>
    <th>Language</th>
    <th>Invoice Number</th>
    <th>Company Name</th> 
 </tr>
</thead>';

$runcompany="";
$nowcompany="";
$mapCoTotals=array();
ZeroCompTotal($mapCoTotals);

$loop=0;


while($row = mysqli_fetch_assoc($result))
{
	$total_charges_interp=$row["total_charges_interp"] + $total_charges_interp;

	$nowcompany=$row["name"];
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
$htmlTable .='<td>'.$row['name'].'</td>';
$htmlTable .='<td>'.$misc->dated($row['assignDate']).'</td>';
$htmlTable .='<td>'.$row['assignTime'].'</td>';
$htmlTable .='<td>'.$row['total_charges_interp'].'</td>';
$htmlTable .='<td>'.$misc->dated($row['dueDate']).'</td>';
$htmlTable .='<td>'.$row['invoiceNo'].'</td>';
$htmlTable .='<td>'.$row['source'].'</td>';

$htmlTable .='<td>'.$row['orgName'].'</td>
</tr>';

 $i++;
}
if ($loop!=0)
  ShowCompTotal($mapCoTotals,$htmlTable);

	$htmlTable .='<tr>';      
	$htmlTable .='<td style="font-weight:bold;"  colspan="4" align="right">Total</td>';
	$htmlTable .=' <td style="font-weight:bold;">'.$misc->numberFormat_fun($total_charges_interp).'</td>';
	$htmlTable .='<td></td>';
	$htmlTable .='<td></td>';
	$htmlTable .='<td></td>';
	$htmlTable .='<td></td>
	  </tr>';
$htmlTable.='</table>';

list($a,$b)=explode('.',basename(__FILE__));
header("Content-Type: application/xls");
header("Content-Disposition: attachment; filename=".$a.".xls");  
echo $htmlTable;

function ZeroCompTotal(&$map)
{
	$map["total_charges_interp"]=0;
}

function UpdateCompTotal(&$map,&$row,$i)
{
	$map["total_charges_interp"]+=$row["total_charges_interp"];

	$map["i"]=$i;
}

function ShowCompTotal(&$map,&$tbl)
{
	global $misc;	

	$tbl.=<<<EOD
	<tr>
  
  <td style="font-weight:bold;" colspan="4" align="right"> Interp. Tot:</td>
  <td>{$misc->numberFormat_fun($map["total_charges_interp"])}</td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  </tr>				
EOD;
}
?>
