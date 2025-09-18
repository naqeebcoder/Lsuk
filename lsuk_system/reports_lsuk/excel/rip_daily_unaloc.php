<?php include '../../db.php';
include_once('../../class.php');
$excel = @$_GET['excel'];
session_start();
$UserName = $_SESSION['UserName'];
$prv = $_SESSION['prv'];
$search_1 = @$_GET['search_1'];
$search_2 = @$_GET['search_2'];
$search_3 = @$_GET['search_3'];
$i = 1;
$table = 'interpreter';
$total_charges_comp = 0;
$C_otherCost = 0;
$g_total = 0;
$g_vat = 0;
$C_otherCost = 0;
$non_vat = 0;
$comp_name = $acttObj->unique_data('comp_reg', 'name', 'abrv', $search_1);
$call_types = array(1 => 'LSUK to Host', 2 => 'Client to Host', 3 => 'Client to call LSUK');
$query = "SELECT
   interpreter.assignDate,
   interpreter.assignTime,
   interpreter.source,
   interpreter.orgName,
   interpreter.intrpName,
   interpreter.inchPerson,
   interpreter.orgContact,  
   interpreter.inchEmail, 
   interpreter.inchEmail2,  
   interpreter.orgRef,  
   interpreter.nameRef,  
   '' as hostedBy,  
   '' as noClient,  
   interpreter.buildingName,
   interpreter.street,
   interpreter.assignCity,
   interpreter.postCode,
   interpreter.dated,   
    interpreter.inchNo,
    interpreter.line1,
    interpreter.line2,
    interpreter.inchRoad,
    interpreter.inchCity,
   interpreter.submited,
   interpreter.bookedVia,   
   interpreter.aloct_by,
   interpreter.aloct_date,
   'Interpreter' as table_name  
   
FROM
   interpreter 
where

	interpreter.deleted_flag = 0  
   and interpreter.order_cancel_flag=0
   and interpreter.orgName like '$search_1%'
   and interpreter.intrpName = ''
   and interpreter.orgName like '$search_1%' 
   and interpreter.dated between '$search_2' and '$search_3' 
   
   union
SELECT
   telephone.assignDate,
   telephone.assignTime,
   telephone.source,
   telephone.orgName,
   telephone.intrpName,
   telephone.inchPerson,
   telephone.orgContact,   
   telephone.inchEmail, 
   telephone.inchEmail2,
   telephone.orgRef,  
   telephone.nameRef,  
   telephone.hostedBy,  
   telephone.noClient, 
   '' as buildingName,
   '' as street,
   '' as assignCity,
   '' as postCode, 
   telephone.dated,   
    telephone.inchNo,
    telephone.line1,
    telephone.line2,
    telephone.inchRoad,
    telephone.inchCity, 
   telephone.submited,
   telephone.bookedVia, 
   telephone.aloct_by,
   telephone.aloct_date,
   'Telephone' as table_name
FROM
   telephone
where
	telephone.deleted_flag = 0 
   and telephone.order_cancel_flag=0
   and telephone.intrpName = ''
	and	telephone.orgName like '$search_1%' 
   	and telephone.dated between '$search_2' and '$search_3' 
   
union
SELECT
   translation.asignDate  as assignDate,
   'Nil' as assignTime,
   translation.source,
   translation.orgName,
   translation.intrpName,   
   'Nil' as inchPerson,
   translation.orgContact,
   translation.inchEmail, 
   translation.inchEmail2,
   translation.orgRef,  
   translation.nameRef,  
   '' as hostedBy,
   '' as noClient,  
   '' as buildingName,
   '' as street,
   '' as assignCity,
   '' as postCode, 
   translation.dated, 
    translation.inchNo,
    translation.line1,
    translation.line2,
    translation.inchRoad,
    translation.inchCity, 
   translation.submited,
   translation.bookedVia, 
   translation.aloct_by,
   translation.aloct_date,
   'Translation' as table_name
FROM
   translation 
   
where
	translation.deleted_flag = 0 
   and translation.order_cancel_flag=0
   and translation.intrpName = ''
   and translation.orgName like '$search_1%' 
   and translation.dated between '$search_2' and '$search_3' 
   
";
$result = mysqli_query($con, $query);

//...................................................................................................................................../////
$htmlTable = '';
$htmlTable .= '<style>
table {border-collapse: collapse; width:670px;}
th {border: 1px solid #999; padding: 0.5rem;text-align: left;background-color:#039; color:#FFF; font-weight:bold;}
td {border: 1px solid #999; padding: 0.5rem;text-align: left;}
</style>
<div>';
$htmlTable .= '<span align="right"> Date: ' . $misc->sys_date() . '</span>';
$htmlTable .= '<h2 style="text-decoration:underline; text-align:center">Daily Booking Report for "' . $comp_name . '"</h2>
<p>Unallocated Booking Report<br/>Date Range:' . $misc->dated($search_2) . 'to' . $misc->dated($search_3) . '</p>
</div>

<table>
<thead>
<tr>
	<th style="background-color:#039;color:#FFF;">Sr.No</th>
	<th style="background-color:#039;color:#FFF;">Mode</th>
	<th style="background-color:#039;color:#FFF;">Assigenment Date</th>
    <th style="background-color:#039;color:#FFF;">Time</th>
    <th style="background-color:#039;color:#FFF;">Language</th>
    <th style="background-color:#039;color:#FFF;">Company</th>
    <th style="background-color:#039;color:#FFF;">Booking</th>
    <th style="background-color:#039;color:#FFF;">Contact</th>
    <th style="background-color:#039;color:#FFF;">Email</th>
    <th style="background-color:#039;color:#FFF;">Email 2</th>
    <th style="background-color:#039;color:#FFF;">Client Ref</th>
    <th style="background-color:#039;color:#FFF;">LSUK Ref</th>
    <th style="background-color:#039;color:#FFF;">Hosted By</th>
    <th style="background-color:#039;color:#FFF;">Client Contact/Address</th>
    <th style="background-color:#039;color:#FFF;">Booked Via</th>
    <th style="background-color:#039;color:#FFF;">Booked By</th>
    <th style="background-color:#039;color:#FFF;">Allocated By</th>';
while ($row = mysqli_fetch_assoc($result)) {
   $hostedBy =  $row['hostedBy'] ? $call_types[$row['hostedBy']] : "";
   if ($row['table_name'] != "Translation") {
      if ($row['table_name'] == "Telephone") {
         $noClient =  $row['hostedBy'] == 1 && $row['noClient'] ? $row['noClient'] : "";
      } else {
         $noClient =  $row['buildingName'] . ', ' . $row['street'] . ', ' . $row['assignCity'] . ', ' . $row['postCode'];
      }
   } else {
      $noClient = "";
   }
   $htmlTable .= '<tr>';
   $htmlTable .= '<td>' . $i . '</td>';
   $htmlTable .= '<td>' . $row["table_name"] . '</td>';
   $htmlTable .= '<td>' . $misc->dated($row["assignDate"]) . '</td>';
   $htmlTable .= '<td>' . $row["assignTime"] . '</td>';
   $htmlTable .= '<td>' . $row["source"] . '</td>';
   $htmlTable .= '<td>' . $row["orgName"] . '</td>';
   $htmlTable .= '<td>' . $row["inchPerson"] . '</td>';
   $htmlTable .= '<td>' . $row["orgContact"] . '</td>';
   $htmlTable .= '<td>' . $row["inchEmail"] . '</td>';
   $htmlTable .= '<td>' . $row["inchEmail2"] . '</td>';
   $htmlTable .= '<td>' . $row["orgRef"] . '</td>';
   $htmlTable .= '<td>' . $row["nameRef"] . '</td>';
   $htmlTable .= '<td>' . $hostedBy . '</td>';
   $htmlTable .= '<td>' . $noClient . '</td>';
   $htmlTable .= '<td>' . $row["bookedVia"] . '</td>';
   //$htmlTable .='<td>'.$row['inchNo'].$row['line1'].$row['line2'].$row['inchRoad'].$row['inchCity'].'</td>';
   $htmlTable .= '<td>' . $row['submited'] . '(' . $row['dated'] . ')' . '</td>';
   $htmlTable .= '<td>' . $row['aloct_by'] . '(' . $misc->dated($row['aloct_date']) . ')' . '</td>
</tr>';
   $i++;
}
$htmlTable .= '</table>';

list($a, $b) = explode('.', basename(__FILE__));
header("Content-Type: application/xls");
header("Content-Disposition: attachment; filename=" . $a.'_'.time() . ".xls");
echo $htmlTable;
