<?php include '../../db.php';include_once ('../../class.php'); $excel=@$_GET['excel'];$search_1=@$_GET['search_1'];$search_2=@$_GET['search_2'];$search_3=@$_GET['search_3'];
$i=1;$table='interpreter';$chargInterp=0;$travelCost=0;$C_otherexpns=0;$total_charges_interp=0;$chargeTravelTime=0;
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
	   where $table.deleted_flag = 0 and $table.order_cancel_flag=0 and assignDate between '$search_2' and '$search_3' and (orgName like '%$_words%')  order by assignDate";}
	   else{$query="SELECT $table.*, interpreter_reg.name, comp_reg.name as orgNam  FROM $table
	   inner join interpreter_reg on $table.intrpName = interpreter_reg.id 
	   inner join comp_reg on $table.orgName = comp_reg.abrv
	   where $table.deleted_flag = 0 and $table.order_cancel_flag=0 and assignDate between '$search_2' and '$search_3'  order by assignDate";}
$result = mysqli_query($con, $query);

$htmlTable='';
$htmlTable.='<style>
table {border-collapse: collapse; width:100%;word-wrap: break-word;}
th {border: 1px solid #999; padding: 0.5rem;text-align: left;background-color:#039; color:#FFF;font-weight:bold;word-wrap: break-word;}
td {border: 1px solid #999; padding: 0.5rem;text-align: left;word-wrap: break-word;}
</style>
<div>
<h2 align="center"><u>Interpreter General Report - Face to Face Interpreting</u></h2>
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
    <th>Language</th>
    <th>Company Name</th>
    <th>Hours (Units) Worked</th>
    <th>Rate per Hour</th>
    <th>Interpreting Time Paid</th>
    <th>Travel Time </th>
    <th>Rate Per Hour</th>
    <th>Travel Time Paid</th>
    <th>Travel Mileage </th>
    <th>Rate Per Mile</th>
    <th>Travel Mileage Paid</th>
    <th>Other Expenses</th>
    <th>Total payment</th>
    <th>Payment Status Paid / Unpaid</th>';

while($row = mysqli_fetch_assoc($result)){$chargInterp=$row["chargInterp"] + $chargInterp;$travelCost=$row["travelCost"] + $travelCost;$C_otherexpns=$row["C_otherexpns"] + $C_otherexpns;$total_charges_interp=$row["total_charges_interp"] + $total_charges_interp;$chargeTravelTime=$row["chargeTravelTime"] + $chargeTravelTime;if($row["intrp_salary_comit"]==1){$invstst='Paid';}else{$invstst='Un-Paid';}

$htmlTable .='<tr>';
$htmlTable .='<td>'.$i.'</td>';
		$htmlTable .='<td>'.$row["source"].'</td>';
		$htmlTable .='<td>'.$row["orgNam"].'</td>';
		$htmlTable .='<td>'.$row["C_hoursWorkd"].'</td>';
		$htmlTable .='<td>'.$row["C_rateHour"].'</td>';
		$htmlTable .='<td>'.$row["C_chargInterp"].'</td>';
		$htmlTable .='<td>'.$row["C_travelMile"].'</td>';
		$htmlTable .='<td>'.$row["C_rateMile"].'</td>';
		$htmlTable .='<td>'.$row["C_travelCost"].'</td>';
		$htmlTable .='<td>'.$row["C_travelTimeHour"].'</td>';
		$htmlTable .='<td>'.$row["C_travelTimeRate"].'</td>';
		$htmlTable .='<td>'.$row["C_chargeTravelTime"].'</td>';
		$htmlTable .='<td>'.$row["C_otherexpns"].'</td>';		
		$htmlTable .='<td>'.$row["total_charges_comp"].'</td>';	
		$htmlTable .='<td>'.$invstst.'</td>
</tr>';
$i++;}
$htmlTable .='<tr>';
$htmlTable .='<td></td>';
$htmlTable .='<td></td>';
$htmlTable .='<td></td>';
$htmlTable .='<td></td>';
$htmlTable .='<td>Total</td>';
$htmlTable .='<td>'.$misc->numberFormat_fun($chargInterp).'</td>';	
$htmlTable .='<td></td>';
$htmlTable .='<td></td>';
$htmlTable .='<td>'.$misc->numberFormat_fun($travelCost).'</td>';	
$htmlTable .='<td></td>';
$htmlTable .='<td></td>';
$htmlTable .='<td>'.$misc->numberFormat_fun($chargeTravelTime).'</td>';	
$htmlTable .='<td>'.$C_otherexpns.'</td>';	

$htmlTable .='<td>'.$misc->numberFormat_fun($total_charges_interp).'</td>';	
$htmlTable .='<td></td>
</tr>';

$htmlTable.='</table>';

list($a,$b)=explode('.',basename(__FILE__));
header("Content-Type: application/xls");
header("Content-Disposition: attachment; filename=".$a.".xls");  
echo $htmlTable;
?>