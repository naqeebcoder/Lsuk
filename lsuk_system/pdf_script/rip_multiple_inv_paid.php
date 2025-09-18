<?php include '../db.php';include_once ('../class.php'); $proceed=$_GET['proceed']; $search_1=@$_GET['search_1'];$search_2=@$_GET['search_2'];$search_3=@$_GET['search_3'];$i=1;$table='interpreter';$g_total_interp=0;$g_total_telep=0;$g_total_trans=0;$non_vat=0;$non_vat_tlep=0;$non_vat_trans=0;$non_vat_interp=0;$withou_VAT_interp=0;$withou_VAT_telp=0;$withou_VAT_trans=0;$C_travelCost=0;$C_rateMile_cost=0;$C_admnchargs_interp=0;$C_admnchargs_telep=0;$C_admnchargs_trans=0;$C_otherexpns=0;$total=0;
if(!empty($search_1)){
	$query="SELECT interpreter.multInvoicNo, interpreter_reg.name  FROM interpreter
	   inner join interpreter_reg on interpreter.intrpName = interpreter_reg.id 
	   where interpreter.deleted_flag = 0 and interpreter.order_cancel_flag=0 and  interpreter.commit=0 and interpreter.multInv_flag=1 
	   and assignDate between '$search_2' and '$search_3' and interpreter.orgName='$search_1'
	   union
	   SELECT telephone.multInvoicNo, interpreter_reg.name  FROM telephone
	   inner join interpreter_reg on telephone.intrpName = interpreter_reg.id 
	   where telephone.deleted_flag = 0 and telephone.order_cancel_flag=0 and  telephone.commit=0 and telephone.multInv_flag=1 and assignDate between '$search_2' and '$search_3' and telephone.orgName='$search_1'
	   union
	   SELECT translation.multInvoicNo, interpreter_reg.name  FROM translation
	   inner join interpreter_reg on translation.intrpName = interpreter_reg.id 
	   where translation.deleted_flag = 0 and translation.order_cancel_flag=0 and  translation.commit=0 and translation.multInv_flag=1 and asignDate between '$search_2' and '$search_3' and translation.orgName='$search_1'   
	   
	   ";
$result = mysqli_query($con, $query);
while($row = mysqli_fetch_assoc($result)){$multInvoicNo=$row["multInvoicNo"];}

$query="SELECT  * FROM comp_reg	   
	   where abrv='$search_1'";	  
$result = mysqli_query($con, $query);
while($row = mysqli_fetch_assoc($result)){$name=$row["name"];$buildingName=$row["buildingName"];$line1=$row["line1"];$line2=$row["line2"];$streetRoad=$row["streetRoad"];$postCode=$row["postCode"];$city=$row["city"];}}
//........................................//\\//\\Invoice #//\\//\\//\\...........................................//

	//$acttObj->editFun($table,$edit_id,'invoiceNo',$invoice);
//.....................................................//\\//\\//\\//\\//\\//\\//\\//\\//\\..........................
if(!empty($search_1)){
$query="SELECT interpreter.*, interpreter_reg.name  FROM interpreter
	   inner join interpreter_reg on interpreter.intrpName = interpreter_reg.id 
	   where interpreter.deleted_flag = 0 and interpreter.commit=0 and interpreter.multInv_flag=1 and assignDate between '$search_2' and '$search_3' and interpreter.orgName='$search_1'";
	  
$result = mysqli_query($con, $query);
require('WriteHTML.php');

$pdf=new PDF_HTML();

$pdf->AliasNbPages();
$pdf->SetAutoPageBreak(true, 15);

$pdf->AddPage();
$pdf->Image('logo.png',18,12,23);
$pdf->SetFont('Arial','B',14);
$pdf->WriteHTML('<para><h1>            Language Services UK Limited<br>         Translation and Interpreting Service</h1><br><u>Suite 2 Davis House Lodge Causeway Trading Estate<br>Lodge Causeway - Fishponds Bristol BS163JB</u></para><br><br><br><br><u>Invoice ('.@$multInvoicNo.') <br>'.@$name .' '. @$buildingName .' '. @$streetRoad .' '. @$line1 .' '. @$line2 .' '. @$city .' '. @$postCode.'<br><br>Date Range: '.$misc->dated($search_2).' to '.$misc->dated($search_3).'<br><br>Date: '.$misc->sys_date().'</u>');

$pdf->SetFont('Arial','B',7); 
$htmlTable='<table>';
$htmlTable .='<tr>

    <td>#</td>
    <td>Job Date</td>
    <td>Type</td>
    <td>Language</td>
    <td>Client Name</td>
    <td>Units</td>
    <td>Unit Cost</td>
    <td>Job Cost</td>
    <td>Travel Cost</td>      
    <td>Travel Expenses</td>             
    <td>Other Expenses</td>  
    <td>Total Cost</td>         
    <td>Job Notes</td>';
while($row = mysqli_fetch_assoc($result)){$g_total_interp=$row["total_charges_comp"] + $g_total_interp;$C_admnchargs_interp=$row["C_admnchargs"]+$C_admnchargs_interp; $C_otherexpns=$row["C_otherexpns"]+$C_otherexpns;

if($proceed=='Yes'){ $acttObj->editFun('interpreter',$row['id'],'multInvoicNo',$invoice);
$acttObj->editFun('interpreter',$row['id'],'multInv_flag',1);}

if($proceed=='Cancel'){ $acttObj->editFun('interpreter',$row['id'],'multInvoicNo','');
$acttObj->editFun('interpreter',$row['id'],'multInv_flag',0);$acttObj->del_comp('mult_inv','m_inv',$row['multInvoicNo']);}

$htmlTable .='<tr>';
$htmlTable .='<td>'.$i.'</td>';
$htmlTable .='<td>'.date_format(date_create($row["assignDate"]), 'd-m-Y').'</td>';
$htmlTable .='<td>Interpreting</td>';
$htmlTable .='<td>'.$row["source"].'</td>';
$htmlTable .='<td>'.$row["inchPerson"].'</td>';
$htmlTable .='<td>'.$row["C_hoursWorkd"].'</td>';
$htmlTable .='<td>'.$row["C_rateHour"].'</td>';
$htmlTable .='<td>'.$row["total_charges_comp"] - $row["C_otherexpns"].'</td>';
$htmlTable .='<td>'.$row["C_rateMile"] * $row["C_travelMile"].'</td>';
$htmlTable .='<td>'.$row["C_travelCost"].'</td>';
$htmlTable .='<td>'.$withou_VAT_interp=$row["C_otherexpns"].'</td>';
$htmlTable .='<td>'.$row["total_charges_comp"].'</td>';

$htmlTable .='<td>'.$row["bookinType"].'</td>
</tr>';
$i++;}
$query_telep="SELECT telephone.*, interpreter_reg.name  FROM telephone
	   inner join interpreter_reg on telephone.intrpName = interpreter_reg.id 
	   where telephone.deleted_flag = 0 and telephone.commit=0 and telephone.multInv_flag=1 and assignDate between '$search_2' and '$search_3' and telephone.orgName='$search_1'";
	  
$result_telep = mysqli_query($con, $query_telep);
while($row = mysqli_fetch_assoc($result_telep)){$g_total_telep=$row["total_charges_comp"] + $g_total_telep;$non_vat_tlep=$row["total_charges_comp"] - $row["C_otherCharges"]+$non_vat_tlep;

if($proceed=='Yes'){ $acttObj->editFun('telephone',$row['id'],'multInvoicNo',$invoice);$C_admnchargs_telep=$row["C_admnchargs"]+$C_admnchargs_telep;
$acttObj->editFun('telephone',$row['id'],'multInv_flag',1);}

if($proceed=='Cancel'){ $acttObj->editFun('telephone',$row['id'],'multInvoicNo','');
$acttObj->editFun('telephone',$row['id'],'multInv_flag',0);$acttObj->del_comp('mult_inv','m_inv',$row['multInvoicNo']);}

$htmlTable .='<tr>';
$htmlTable .='<td>'.$i.'</td>';
$htmlTable .='<td>'.date_format(date_create($row["assignDate"]), 'd-m-Y').'</td>';
$htmlTable .='<td>Telephone</td>';
$htmlTable .='<td>'.$row["source"].'</td>';
$htmlTable .='<td>'.$row["inchPerson"].'</td>';
$htmlTable .='<td>'.$row["C_hoursWorkd"].'</td>';
$htmlTable .='<td>'.$row["C_rateHour"].'</td>';
$htmlTable .='<td>'.$non_vat_tlep=$row["total_charges_comp"] - $row["C_otherCharges"].'</td>';
$htmlTable .='<td>N/A</td>';
$htmlTable .='<td>N/A</td>';
$htmlTable .='<td>'.$withou_VAT_telp=$row["C_otherCharges"].'</td>';
$htmlTable .='<td>'.$row["total_charges_comp"].'</td>';

$htmlTable .='<td>'.$row["bookinType"].'</td>
</tr>';
$i++;}

$query_trans="SELECT translation.*, interpreter_reg.name  FROM translation
	   inner join interpreter_reg on translation.intrpName = interpreter_reg.id 
	   where translation.deleted_flag = 0 and translation.commit=0 and translation.multInv_flag=1 and asignDate between '$search_2' and '$search_3' and translation.orgName='$search_1'";
	  
$result_trans = mysqli_query($con, $query_trans);
while($row = mysqli_fetch_assoc($result_trans)){ $g_total_trans=$row["total_charges_comp"] + $g_total_trans ;$C_admnchargs_trans=$row["C_admnchargs"]+$C_admnchargs_trans;$non_vat_trans=$row["total_charges_comp"] - $row["C_otherCharg"]+$non_vat_trans;
if($proceed=='Yes'){ $acttObj->editFun('translation',$row['id'],'multInvoicNo',$invoice);
$acttObj->editFun('translation',$row['id'],'multInv_flag',1);}

if($proceed=='Cancel'){ $acttObj->editFun('translation',$row['id'],'multInvoicNo','');
$acttObj->editFun('translation',$row['id'],'multInv_flag',0);$acttObj->del_comp('mult_inv','m_inv',$row['multInvoicNo']);}

$htmlTable .='<tr>';
$htmlTable .='<td>'.$i.'</td>';
$htmlTable .='<td>'.date_format(date_create($row["asignDate"]), 'd-m-Y').'</td>';
$htmlTable .='<td>Translation</td>';
$htmlTable .='<td>'.$row["source"].'</td>';
$htmlTable .='<td>'.$row["nameRef"].'</td>';
$htmlTable .='<td>'.$row["numberUnit"].'</td>';
$htmlTable .='<td>'.$row["rpU"].'</td>';
$htmlTable .='<td>'.$non_vat_trans=$row["total_charges_comp"] - $row["C_otherCharg"].'</td>';
$htmlTable .='<td>N/A</td>';
$htmlTable .='<td>N/A</td>';
$htmlTable .='<td>'.$withou_VAT_trans=$row["C_otherCharg"].'</td>';
$htmlTable .='<td>'.$row["total_charges_comp"].'</td>';

$htmlTable .='<td>'.$row["bookinType"].'</td>
</tr>';
$i++;}

$htmlTable .='<tr>';
$htmlTable .='<td></td>';
$htmlTable .='<td></td>';
$htmlTable .='<td></td>';
$htmlTable .='<td></td>';
$htmlTable .='<td></td>';
$htmlTable .='<td></td>';
$htmlTable .='<td></td>';
$htmlTable .='<td></td>';
$htmlTable .='<td></td>';
$htmlTable .='<td></td>';
$htmlTable .='<td></td>';
$htmlTable .='<td>Total</td>';

$htmlTable .='<td>'.number_format($g_total_interp - $C_otherexpns  + $g_total_telep + $g_total_trans,2).'</td>
</tr>';

$htmlTable .='<tr>';
$htmlTable .='<td></td>';
$htmlTable .='<td></td>';
$htmlTable .='<td></td>';
$htmlTable .='<td></td>';
$htmlTable .='<td></td>';
$htmlTable .='<td></td>';
$htmlTable .='<td></td>';
$htmlTable .='<td></td>';
$htmlTable .='<td></td>';
$htmlTable .='<td></td>';
$htmlTable .='<td></td>';
$htmlTable .='<td>VAT @20%</td>';
$htmlTable .='<td>'.($g_total_interp - $C_otherexpns  + $g_total_telep + $g_total_trans)*.2.'</td>
</tr>';

$htmlTable .='<tr>';
$htmlTable .='<td></td>';
$htmlTable .='<td></td>';
$htmlTable .='<td></td>';
$htmlTable .='<td></td>';
$htmlTable .='<td></td>';
$htmlTable .='<td></td>';
$htmlTable .='<td></td>';
$htmlTable .='<td></td>';
$htmlTable .='<td></td>';
$htmlTable .='<td></td>';
$htmlTable .='<td></td>';
$htmlTable .='<td>Non VAT</td>';

$htmlTable .='<td>'.$C_otherexpns.'</td>
</tr>';

$htmlTable .='<tr>';
$htmlTable .='<td></td>';
$htmlTable .='<td></td>';
$htmlTable .='<td></td>';
$htmlTable .='<td></td>';
$htmlTable .='<td></td>';
$htmlTable .='<td></td>';
$htmlTable .='<td></td>';
$htmlTable .='<td></td>';
$htmlTable .='<td></td>';
$htmlTable .='<td></td>';
$htmlTable .='<td></td>';
$htmlTable .='<td>Total Invoice</td>';

$htmlTable .='<td>'. $C_otherexpns +($g_total_interp  + $g_total_telep + $g_total_trans)+($g_total_interp - $C_otherexpns  + $g_total_telep + $g_total_trans)*.2.'</td>
</tr>';

$htmlTable .='</TABLE>';

$pdf->WriteHTML2("<br><br><br>$htmlTable");
$pdf->WriteHTML('<br><br> Please make all cheques payable to Language Services UK Limited for BACS payment Sort Code 20-13-34 Account Number 33161234.
Company                                                     Registration Number 7760366 VAT Number 198427362
Thank You For Business With Us
<br><br>
Please pay your invoice within 21 days from the date of invoice. <u>Compensation fee and interest charges at 1.5% per day will be added to invoice total in accordance with the "Late Payment of Commercial Debts Interests Act 1998"</u> if no payment was made within reasonable time frame
');
$pdf->SetFont('Arial','B',6);

$pdf->Output(); } 
?>