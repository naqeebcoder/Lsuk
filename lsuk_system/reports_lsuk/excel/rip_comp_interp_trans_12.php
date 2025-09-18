<?php include '../../db.php';include_once ('../../class.php'); $excel=@$_GET['excel'];$search_1=@$_GET['search_1'];$search_2=@$_GET['search_2'];$search_3=@$_GET['search_3'];
$i=1;$table='translation';$rpuXnumb=0;$C_otherCharg=0;$total_charges_comp=0;;$C_admnchargs=0;

//...................................................For Multiple Selection...................................\\
$counter=0; $arr = explode(',', $search_1);$_words = implode("' OR orgName like '", $arr);
//......................................\\//\\//\\//\\//........................................................\\
if(!empty($search_1)){
$query="SELECT  * FROM comp_reg	   
	   where abrv='$search_1'";	  
$result = mysqli_query($con, $query);
while($row = mysqli_fetch_assoc($result)){$name=$row["name"];$buildingName=$row["buildingName"];$line1=$row["line1"];$line2=$row["line2"];$streetRoad=$row["streetRoad"];$postCode=$row["postCode"];$city=$row["city"];}}
if(!empty($search_1)){
$query="SELECT $table.*, interpreter_reg.name, comp_reg.name as orgNam  FROM $table
	   inner join interpreter_reg on $table.intrpName = interpreter_reg.id 
	   inner join comp_reg on $table.orgName = comp_reg.abrv
	   where $table.deleted_flag = 0 and $table.order_cancel_flag=0 and asignDate between '$search_2' and '$search_3' and (orgName like '%$_words%') 
	   order by orgName";}
	   else{$query="SELECT $table.*, interpreter_reg.name, comp_reg.name as orgNam  FROM $table
	   inner join interpreter_reg on $table.intrpName = interpreter_reg.id 
	   inner join comp_reg on $table.orgName = comp_reg.abrv
	   where $table.deleted_flag = 0 and $table.order_cancel_flag=0 and asignDate between '$search_2' and '$search_3' 
	   order by orgName";}

$result = mysqli_query($con, $query);
$htmlTable='';
$htmlTable.='<style>
table {border-collapse: collapse; width:100%;word-wrap: break-word;}
th {border: 1px solid #999; padding: 0.5rem;text-align: left;background-color:#039; color:#FFF;font-weight:bold;word-wrap: break-word;}
td {border: 1px solid #999; padding: 0.5rem;text-align: left;word-wrap: break-word;}
</style>
<div>
<h2 align="center"><u>Translation Interpreting General Report ('.$search_1.')</u></h2>
<p align="right">Report  Date: '.$misc->sys_date().'<br />
  Date  Range: Date From '.$misc->dated($search_2).' Date To '.$misc->dated($search_3).'</p>
</div>
<p>Interpreter(s) Selected</p>
<table class="aa" border="1" cellspacing="0" cellpadding="0" style="width:250px">
  <tr>
    <td width="200" valign="top">'.$search_1.'</td>
  </tr>
</table>
<table>';
$htmlTable .='<tr>
 	<th style="width:45px;">Sr. No.</th>
    <th>Org</th>
	<th>Invoice Number</th>
    <th>Invoice Date</th>
    <th>Assgn Date and Time</th>
    <th>Language</th>
    <th>Interp Name</th>
    <th>Number of Unit</th>
    <th>Rate per Unit</th>
    <th>Interp Time Cost</th>
    <th>Other Expenses</th>
    <th>Admin Charge</th>
    <th>Total Cost</th>
    <th>Invoice Status Paid/Unpaid </th>';

	$runcompany="";
	$nowcompany="";
	  
	$mapCoTotals=array();
	ZeroCompTotal($mapCoTotals);
  
	$loop=0;

	while($row = mysqli_fetch_assoc($result))
	{
		$rpuXnumb=$row["rpU"]*$row["numberUnit"] + $rpuXnumb;$C_otherCharg=$row["C_otherCharg"] + $C_otherCharg;
		$total_charges_comp=$row["total_charges_comp"] + $total_charges_comp;
		$rpu_number=$row["rpU"]*$row["numberUnit"];
		$C_admnchargs=$row["C_admnchargs"] + $C_admnchargs; 
		if($row["rAmount"]>0)
		{
			$invstst='Paid';
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


$htmlTable .='<tr>';
$htmlTable .='<td>'.$i.'</td>';
		$htmlTable .='<td>'.$row["orgNam"].'</td>';
		$htmlTable .='<td>'.$row["invoiceNo"].'</td>';
		$htmlTable .='<td>'.$row["dated"].'</td>';
		$htmlTable .='<td>'.$misc->dated($row["asignDate"]).'</td>';
		$htmlTable .='<td>'.$row["source"].'</td>';
		$htmlTable .='<td>'.$row["name"].'</td>';
		$htmlTable .='<td>'.$row["C_rpU"].'</td>';
		$htmlTable .='<td>'.$row["C_numberUnit"].'</td>';
		$htmlTable .='<td>'.$rpu_number.'</td>';
		$htmlTable .='<td>'.$row["C_otherCharg"].'</td>';	
		$htmlTable .='<td>'.$row["C_admnchargs"].'</td>';		
		$htmlTable .='<td>'.$row["total_charges_comp"].'</td>';
		$htmlTable .='<td>'.$invstst.'</td>
</tr>';
$i++;
}
ShowCompTotal($mapCoTotals,$htmlTable);

$htmlTable .='<tr>';
$htmlTable .='<td></td>';
$htmlTable .='<td></td>';
$htmlTable .='<td></td>';
$htmlTable .='<td></td>';
$htmlTable .='<td></td>';
$htmlTable .='<td></td>';
$htmlTable .='<td></td>';
$htmlTable .='<td></td>';
$htmlTable .='<td>Total</td>';
$htmlTable .='<td>'.$misc->numberFormat_fun($rpuXnumb).'</td>';
$htmlTable .='<td>'.$misc->numberFormat_fun($C_otherCharg).'</td>';
$htmlTable .='<td>'.$misc->numberFormat_fun($C_admnchargs).'</td>';	
$htmlTable .='<td>'.$misc->numberFormat_fun($total_charges_comp).'</td>';
$htmlTable .='<td></td>
</tr>';
$htmlTable .='</TABLE>';
list($a,$b)=explode('.',basename(__FILE__));
header("Content-Type: application/xls");
header("Content-Disposition: attachment; filename=".$a.".xls");  
echo $htmlTable;

function ZeroCompTotal(&$map)
{
	$map["rpuXnumb"] =0;
	$map["C_otherCharg"] =0;
	$map["C_admnchargs"] =0;
	$map["total_charges_comp"] =0; 
}

function UpdateCompTotal(&$map,&$row)
{
	$rpuXnumb=$row["rpU"]*$row["numberUnit"] + $map["rpuXnumb"];
	$map["rpuXnumb"]=$rpuXnumb;

	$map["C_otherCharg"]+=$row["C_otherCharg"];
	$map["C_admnchargs"]+=$row["C_admnchargs"];
	$map["total_charges_comp"]+=$row["total_charges_comp"]; 
}

function ShowCompTotal(&$map,&$tbl)
{
	global $misc;

	$tbl.=<<<EOD
	<tr>
	<td></td>
	<td></td>
	<td></td>
	<td></td>
	<td></td>
	<td></td>
	<td></td>
	<td></td>
	<td>Comp Total</td>
	<td>{$misc->numberFormat_fun($map["rpuXnumb"])}</td>
	<td>{$misc->numberFormat_fun($map["C_otherCharg"])}</td>
	<td>{$misc->numberFormat_fun($map["C_admnchargs"])}</td>	
	<td>{$misc->numberFormat_fun($map["total_charges_comp"])}</td>
	<td></td>
	</tr>				
EOD;

}

?>