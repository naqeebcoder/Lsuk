<?php include '../db.php';session_start();$UserName=$_SESSION['UserName'];$prv=$_SESSION['prv'];include_once ('../class.php'); $search_1=@$_GET['search_1'];$search_2=@$_GET['search_2'];$search_3=@$_GET['search_3'];
$i=1;$table='interpreter';$total_charges_comp=0;$C_otherCost=0;$g_total=0;$g_vat=0;$C_otherCost=0;$non_vat=0;$comp_name=$acttObj->unique_data('comp_reg','name','abrv',$search_1);
if($prv=='Management'){
$query="SELECT
   interpreter.assignDate,
   interpreter.assignTime,
   interpreter.source,
   interpreter.orgName,
   interpreter.intrpName,
   interpreter.inchPerson,
   interpreter.orgContact,
   
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
   'Interpreter' as tble
   
FROM
   interpreter 
   inner join
      interpreter_reg 
      on interpreter.intrpName = interpreter_reg.id 
where

	interpreter.deleted_flag = 0 
   and interpreter.order_cancel_flag=0
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
   telephone.submited,
   telephone.bookedVia,
   
   
    telephone.inchNo,
    telephone.line1,
    telephone.line2,
    telephone.inchRoad,
    telephone.inchCity,
   
   telephone.dated,
   telephone.aloct_by,
   telephone.aloct_date,
   'Telephone' as tble
FROM
   telephone
   inner join
      interpreter_reg 
      on telephone.intrpName = interpreter_reg.id 
where
	telephone.deleted_flag = 0 
   and telephone.order_cancel_flag=0
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
   translation.bookedVia,
   
   'Nil' as inchNo,
   '' as line1,
   '' as line2,
   '' as inchRoad,
   '' as inchCity,
   translation.submited,
   translation.dated,
   translation.aloct_by,
   translation.aloct_date,
   'Translation' as tble
FROM
   translation 
   inner join
      interpreter_reg 
      on translation.intrpName = interpreter_reg.id 
   
where
	translation.deleted_flag = 0 
   and translation.order_cancel_flag=0
   and translation.orgName like '$search_1%' 
   and translation.dated between '$search_2' and '$search_3' 
   
";}
else{
$query="SELECT
   interpreter.assignDate,
   interpreter.assignTime,
   interpreter.source,
   interpreter.orgName,
   interpreter.intrpName,
   interpreter.inchPerson,
   interpreter.orgContact,
   
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
   'Interpreter' as tble
   
FROM
   interpreter 
   inner join
      interpreter_reg 
      on interpreter.intrpName = interpreter_reg.id 
where

	interpreter.deleted_flag = 0 
   and interpreter.order_cancel_flag=0
   and interpreter.submited='$UserName' 
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
   telephone.submited,
   telephone.bookedVia,
   
   
    telephone.inchNo,
    telephone.line1,
    telephone.line2,
    telephone.inchRoad,
    telephone.inchCity,
   
   telephone.dated,
   telephone.aloct_by,
   telephone.aloct_date,
   'Telephone' as tble
FROM
   telephone
   inner join
      interpreter_reg 
      on telephone.intrpName = interpreter_reg.id 
where
	telephone.deleted_flag = 0 
   and telephone.order_cancel_flag=0
   and telephone.submited='$UserName' 
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
   translation.bookedVia,
   
   'Nil' as inchNo,
   '' as line1,
   '' as line2,
   '' as inchRoad,
   '' as inchCity,
   translation.submited,
   translation.dated,
   translation.aloct_by,
   translation.aloct_date,
   'Translation' as tble
FROM
   translation 
   inner join
      interpreter_reg 
      on translation.intrpName = interpreter_reg.id 
   
where
	translation.deleted_flag = 0 
   and translation.order_cancel_flag=0
   and translation.submited='$UserName' 
   and translation.orgName like '$search_1%' 
   and translation.dated between '$search_2' and '$search_3' 
   
"; }
$result = mysqli_query($con, $query);
require('WriteHTML.php');

$pdf=new PDF_HTML();

$pdf->AliasNbPages();
$pdf->SetAutoPageBreak(true, 15);

$pdf->AddPage();
$pdf->Image('logo.png',18,12,23);
$pdf->SetFont('Arial','B',14);
$pdf->WriteHTML('<para><h1>            Language Services UK Limited<br>         Translation and Interpreting Service</h1><br><u>Suite 2 Davis House Lodge Causeway Trading Estate<br>Lodge Causeway - Fishponds Bristol BS163JB</u></para><br><br><br><br><u>Daily Booking Report for "'.$comp_name.'"<br><br><br>Date Range: '.$misc->dated($search_2).' to '.$misc->dated($search_3).'<br><br>Report Date: '.$misc->sys_date().'</u>');

$pdf->SetFont('Arial','B',7); 
$htmlTable='<table>';
$htmlTable .='<tr>
    <td>#</td>
	<td>Mode</td>
	<td>Assigenment Date</td>
    <td>Time</td>
    <td>Language</td>
    <td>Company</td>
    <td>Contact Name</td>
    <td>Contact Person</td>
    <td>Booked Via</td>
    <td>Booked By</td>
    <td>Allocated By</td>';
while($row = mysqli_fetch_assoc($result)){
$htmlTable .='<tr>';
$htmlTable .='<td>'.$i.'</td>';
$htmlTable .='<td>'.$row["tble"].'</td>';
$htmlTable .='<td>'.$misc->dated($row["assignDate"]).'</td>';
$htmlTable .='<td>'.$row["assignTime"].'</td>';
$htmlTable .='<td>'.$row["source"].'</td>';
$htmlTable .='<td>'.$row["orgName"].'</td>';
$htmlTable .='<td>'.$row["inchPerson"].'</td>';
$htmlTable .='<td>'.$row["orgContact"].'</td>';
$htmlTable .='<td>'.$row["bookedVia"].'</td>';
//$htmlTable .='<td>'.$row['inchNo'].$row['line1'].$row['line2'].$row['inchRoad'].$row['inchCity'].'</td>';
$htmlTable .='<td>'.$row['submited'].'('.$row['dated'].')'.'</td>';
$htmlTable .='<td>'.$row['aloct_by'].'('.$misc->dated($row['aloct_date']).')'.'</td>
</tr>';
$i++;}

$htmlTable .='</TABLE>';
$pdf->WriteHTML2("<br><br><br>$htmlTable");
$pdf->SetFont('Arial','B',6);
$pdf->Output(); 
?>