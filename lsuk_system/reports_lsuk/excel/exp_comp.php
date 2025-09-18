<?php ini_set('max_execution_time', 0);include '../../db.php';include_once ('../../class.php'); 
$htmlTable='';
$htmlTable.='<style>
table {border-collapse: collapse; width:670px;}
th {border: 1px solid #999; padding: 0.5rem;text-align: left;background-color:#039; color:#FFF; font-weight:bold;}
td {border: 1px solid #999; padding: 0.5rem;text-align: left;}
</style>
<div>';

$htmlTable .='
<table>
<thead>
<tr>
    <th>Comp Name</th>
    <th>Contact Person</th>
    <th>Company Type</th>
    <th>Phone#</th>
    <th>Email</th>
    <th>City</th>
    <th>Address</th>
    <th>Submitted By</th>
    <th>Status</th>
</tr>
</thead>
<tbody>';
    $query = "SELECT  *  FROM comp_reg
	   where deleted_flag=0 ORDER BY name ASC";
    $result = mysqli_query($con, $query);
    while ($row = mysqli_fetch_assoc($result)) { 
            $htmlTable.="<tr>";
            $htmlTable.="<td>".$row['name']."</td>";
            $htmlTable.="<td>".$row['contactPerson']."</td>";
            $htmlTable.="<td>".$row['compType']."</td>";
            $htmlTable.="<td>".$row['contactNo1']."</td>";
            $htmlTable.="<td>".$row['email']."</td>";
            $htmlTable.="<td>".$row['city']."</td>";
            $htmlTable.="<td>".$row['buildingName']."  ".$row['line1']."  ".$row['streetRoad']."</td>";
            $htmlTable.="<td>".$row['sbmtd_by']."</td>";
            $htmlTable.="<td>".$row['status']."</td>";
            $htmlTable.="</tr>";
 } 

 $htmlTable.='</tbody></table>';
// list($a,$b)=explode('.',basename(__FILE__));
// header("Content-Type: application/xls");
// header("Content-Disposition: attachment; filename=".$a.".xls");  
echo $htmlTable;