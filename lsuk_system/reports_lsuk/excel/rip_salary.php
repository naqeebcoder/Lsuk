<?php 
include '../../db.php';
include_once ('../../class.php'); 
$excel=@$_GET['excel'];
$search_1=@$_GET['search_1'];
$search_2=@$_GET['search_2'];
$search_3=@$_GET['search_3'];

$i=1;
$table='emp';
$salary=0;
$duration=0;

if(!empty($search_1))
{
    $query="SELECT $table.*,  rolcal.*  FROM $table
	   join rolcal on $table.id = rolcal.empId 
       where rolcal.dated between '$search_2' and '$search_3' and $table.name='$search_1' ##emp_active## ";
}
else
{
    $query="SELECT $table.*,  rolcal.*  FROM $table
	   join rolcal on $table.id = rolcal.empId 
       where rolcal.dated between '$search_2' and '$search_3' ##emp_active## ";
}
$query=SqlUtils::ModfiySql($query);
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
$htmlTable .='<h2 style="text-decoration:underline; text-align:center">Employees Salary Report</h2>
<p>Salary Report<br/>Date Range:' .$misc->dated($search_2). 'to' .$misc->dated($search_3). '</p>
</div>

<table>
<thead>
<tr>

<th style="background-color:#039;color:#FFF;">Sr.No</th>
    <th style="background-color:#039;color:#FFF;">Name</th>
    <th style="background-color:#039;color:#FFF;">Designation</th>
    <th style="background-color:#039;color:#FFF;">Start</th>
    <th style="background-color:#039;color:#FFF;">Finish</th>
    <th style="background-color:#039;color:#FFF;">Duration</th>
    <th style="background-color:#039;color:#FFF;">RPH</th>
    <th style="background-color:#039;color:#FFF;">Total Amount</th>
    <th style="background-color:#039;color:#FFF;">Salary Date</th>
    <th style="background-color:#039;color:#FFF;">Dated</th>';
while($row = mysqli_fetch_assoc($result)){$salary=$row["salary"] + $salary;$duration=$row["duration"] + $duration;
$htmlTable .='<tr>';
$htmlTable .='<td>'.$i.'</td>';
$htmlTable .='<td>'.$row["name"].'</td>';
$htmlTable .='<td>'.$row["desig"].'</td>';
$htmlTable .='<td>'.$row["start"].'</td>';
$htmlTable .='<td>'.$row["finish"].'</td>';
$htmlTable .='<td>'.round($row["duration"]).'</td>';
$htmlTable .='<td>'.$row["rph"].'</td>';
$htmlTable .='<td>'.round($row["salary"]).'</td>';
$htmlTable .='<td>'.$misc->dated($row['entry_date']).'</td>';

$htmlTable .='<td>'.date_format(date_create($row["dated"]), 'd-m-Y').'</td>
</tr>';
$i++;}


$htmlTable .='<tr>';
$htmlTable .='<td></td>';
$htmlTable .='<td></td>';
$htmlTable .='<td></td>';
$htmlTable .='<td></td>';
$htmlTable .='<td>Total</td>';
$htmlTable .='<td>'.round($duration).'</td>';
$htmlTable .='<td></td>';
$htmlTable .='<td>'.number_format(round($salary),2).'</td>';

$htmlTable .='<td></td>';
$htmlTable .='<td></td>
</tr>';

$htmlTable .='</TABLE>';
list($a,$b)=explode('.',basename(__FILE__));
header("Content-Type: application/xls");
header("Content-Disposition: attachment; filename=".$a.".xls");  
echo $htmlTable;
?>