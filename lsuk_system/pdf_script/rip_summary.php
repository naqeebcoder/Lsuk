<?php include '../db.php';include_once ('../class.php'); $search_2=@$_GET['search_2'];$search_3=@$_GET['search_3'];
$i=1;$total_interp=0;$total_comp=0;$total_otherCharg=0;$gross=0;$total_paid=0;$total_pending=0;$total_otherCharg_pending=0;$total_otherCharg_paid=0;$g_total_comp=0;
$total_interp_credit=0;$total_comp_credit=0;$total_otherCharg_credit=0;
//................................................................................................................

		
$query="SELECT count(interpreter.id) as paid_i,sum(total_charges_comp) total_charges_comp ,sum(C_otherCost) as C_otherCharg FROM interpreter 
   inner join interpreter_reg on interpreter.intrpName = interpreter_reg.id 
	   	where interpreter.deleted_flag = 0 and interpreter.order_cancel_flag=0 and assignDate between '$search_2'	and '$search_3' and  interpreter.rAmount > 0
		";
$result = mysqli_query($con, $query);
while($row = mysqli_fetch_assoc($result)){$paid_i=$row["paid_i"];$total_paid=$row["total_charges_comp"];$total_otherCharg_paid=$row["C_otherCharg"];}
$total_paid_int=$total_paid + $total_otherCharg_paid + $total_paid * 0.2;

$query="SELECT count(telephone.id) as paid_i,sum(total_charges_comp) total_charges_comp ,sum(C_otherCharges) as C_otherCharg FROM telephone
	inner join interpreter_reg on telephone.intrpName = interpreter_reg.id 
	   	where telephone.deleted_flag = 0 and telephone.order_cancel_flag=0 and assignDate between '$search_2' and '$search_3' and  rAmount > 0
		";
$result = mysqli_query($con, $query);
while($row = mysqli_fetch_assoc($result)){$paid_i=$row["paid_i"]+$paid_i;$total_paid=$row["total_charges_comp"];$total_otherCharg_paid=$row["C_otherCharg"];}
$total_paid_telep=$total_paid + $total_otherCharg_paid + $total_paid * 0.2;

$query="SELECT count(translation.id) as paid_i,sum(total_charges_comp) total_charges_comp ,sum(C_otherCharg) as C_otherCharg FROM translation 
   inner join interpreter_reg on translation.intrpName = interpreter_reg.id 
	   	where translation.deleted_flag = 0 and translation.order_cancel_flag=0 and asignDate between '$search_2' and '$search_3' and  rAmount > 0
		";
$result = mysqli_query($con, $query);
while($row = mysqli_fetch_assoc($result)){$paid_i=$row["paid_i"]+$paid_i;$total_paid=$row["total_charges_comp"];$total_otherCharg_paid=$row["C_otherCharg"];}
$total_paid_trans=$total_paid + $total_otherCharg_paid + $total_paid * 0.2;
$total_paid = $total_paid_trans + $total_paid_telep + $total_paid_int;
//...........................................................................................................
$query="SELECT count(interpreter.id) as pend_i,sum(total_charges_comp) total_charges_comp ,sum(C_otherCost) as C_otherCharg FROM interpreter	
   inner join interpreter_reg on interpreter.intrpName = interpreter_reg.id 
	   	where interpreter.deleted_flag = 0 and interpreter.order_cancel_flag=0 and assignDate between '$search_2' and '$search_3' and  rAmount <= 0
		";
$result = mysqli_query($con, $query);
while($row = mysqli_fetch_assoc($result)){$pend_i=$row["pend_i"];$total_pending=$row["total_charges_comp"];$total_otherCharg_pending=$row["C_otherCharg"];}
$total_pending_int=$total_pending + $total_otherCharg_pending + $total_pending * 0.2;
		
$query="SELECT  count(telephone.id) as pend_i,sum(total_charges_comp) total_charges_comp ,sum(C_otherCharges) as C_otherCharg FROM telephone	
	inner join interpreter_reg on telephone.intrpName = interpreter_reg.id 
	   	where telephone.deleted_flag = 0 and telephone.order_cancel_flag=0 and assignDate between '$search_2' and '$search_3' and  rAmount <= 0
		";
$result = mysqli_query($con, $query);
while($row = mysqli_fetch_assoc($result)){$pend_i=$row["pend_i"]+$pend_i;$total_pending=$row["total_charges_comp"];$total_otherCharg_pending=$row["C_otherCharg"];}
$total_pending_telep=$total_pending + $total_otherCharg_pending + $total_pending * 0.2;
		
$query="SELECT  count(translation.id) as pend_i,sum(total_charges_comp) total_charges_comp ,sum(C_otherCharg) as C_otherCharg FROM translation	
   inner join interpreter_reg on translation.intrpName = interpreter_reg.id 
	   	where translation.deleted_flag = 0 and translation.order_cancel_flag=0 and asignDate between '$search_2' and '$search_3' and  rAmount <= 0
		";
$result = mysqli_query($con, $query);
while($row = mysqli_fetch_assoc($result)){$pend_i=$row["pend_i"]+$pend_i;$total_pending=$row["total_charges_comp"];$total_otherCharg_pending=$row["C_otherCharg"];}
$total_pending_trans=$total_pending + $total_otherCharg_pending + $total_pending * 0.2;
$total_pending = $total_pending_trans + $total_pending_telep + $total_pending_int;
//.....................................................................................................................

$query="SELECT total_charges_comp,C_otherCost as C_otherCharg FROM interpreter	
	   	where interpreter.deleted_flag = 0 and interpreter.order_cancel_flag=0 and credit_note <>'' and assignDate between '$search_2' and '$search_3'
		union
		SELECT total_charges_comp,C_otherCharges FROM telephone	
	   	where telephone.deleted_flag = 0 and telephone.order_cancel_flag=0 and credit_note <>'' and assignDate between '$search_2' and '$search_3'
		union
		SELECT total_charges_comp,C_otherCharg FROM translation	
	   	where translation.deleted_flag = 0 and translation.order_cancel_flag=0 and credit_note <>'' and asignDate between '$search_2' and '$search_3'
		";

$result = mysqli_query($con, $query);
while($row = mysqli_fetch_assoc($result)){$total_comp_credit=$row["total_charges_comp"] + $total_comp_credit;$total_otherCharg_credit=$row["C_otherCharg"] + $total_otherCharg_credit;}
$gross_credit=$total_comp_credit + $total_otherCharg_credit + $total_comp_credit * 0.2;


//.........................................................................................................................

$query="SELECT sum(total_charges_comp) total_charges_comp , sum(total_charges_interp) total_charges_interp,sum(C_otherCost) as C_otherCharg FROM interpreter	
	   	where interpreter.deleted_flag = 0 and interpreter.order_cancel_flag=0 and assignDate between '$search_2' and '$search_3'
		";
$result = mysqli_query($con, $query);
while($row = mysqli_fetch_assoc($result)){$total_interp_int=$row["total_charges_interp"];$total_comp=$row["total_charges_comp"];$total_otherCharg=$row["C_otherCharg"] + $total_otherCharg;}
$gross=$total_comp + $gross;$g_total_comp=$total_comp + $g_total_comp;

$query="SELECT sum(total_charges_comp) total_charges_comp , sum(total_charges_interp) total_charges_interp,sum(C_otherCharges) as C_otherCharg FROM telephone	
	   	where telephone.deleted_flag = 0 and telephone.order_cancel_flag=0 and assignDate between '$search_2' and '$search_3'
		";
$result = mysqli_query($con, $query);
while($row = mysqli_fetch_assoc($result)){$total_interp_telp=$row["total_charges_interp"];$total_comp=$row["total_charges_comp"];$total_otherCharg=$row["C_otherCharg"] + $total_otherCharg;}
$gross=$total_comp + $gross;$g_total_comp=$total_comp + $g_total_comp;

$query="SELECT sum(total_charges_comp) total_charges_comp , sum(total_charges_interp) total_charges_interp,sum(C_otherCharg) as C_otherCharg FROM translation	
	   	where translation.deleted_flag = 0 and translation.order_cancel_flag=0 and asignDate between '$search_2' and '$search_3'
		";
$result = mysqli_query($con, $query);
while($row = mysqli_fetch_assoc($result)){$total_interp_trans=$row["total_charges_interp"];$total_comp=$row["total_charges_comp"];$total_otherCharg=$row["C_otherCharg"] + $total_otherCharg;}
$gross=$total_comp + $gross;$g_total_comp=$total_comp + $g_total_comp;

 $total_interp= $total_interp_trans + $total_interp_telp + $total_interp_int;

$query_expnce="SELECT SUM(amoun) as amoun FROM expence where billDate between '$search_2' and '$search_3'";$result_expnce = mysqli_query($con, $query_expnce);while($row_expnce = mysqli_fetch_assoc($result_expnce)){$total_expnce=$row_expnce['amoun'];}
$numb_of_inv=$pend_i + $paid_i;
require('WriteHTML.php');

$pdf=new PDF_HTML();

$pdf->AliasNbPages();
$pdf->SetAutoPageBreak(true, 15);

$pdf->AddPage();
$pdf->Image('logo.png',18,12,23);
$pdf->SetFont('Arial','B',14);
$pdf->WriteHTML('<para><h1>            Language Services UK Limited<br>         Translation and Interpreting Service</h1><br><u>Suite 2 Davis House Lodge Causeway Trading Estate<br>Lodge Causeway - Fishponds Bristol BS163JB</u></para><br><br><br><br><u>Profit and Loss Summary ('.$misc->dated($search_2). ' to ' .$misc->dated($search_3).')<br><br>Date: '.$misc->sys_date().'</u>');

$pdf->SetFont('Arial','B',7); 
$htmlTable='<table>';
$htmlTable .='<tr>
    <td>Total Registered invoices</td>';
$htmlTable .='<td>'.$numb_of_inv.'</td>
</tr>';
$htmlTable .='<tr>
    <td>Total Value of the Pending invoices</td>';
$htmlTable .='<td>'.number_format($total_pending, 2).'('.$pend_i.')</td>
</tr>';
$htmlTable .='<tr>
    <td>Total Value of the Paid invoices</td>';
$htmlTable .='<td>'.number_format($total_paid, 2).'('.$paid_i.')</td>
</tr>';
$htmlTable .='<tr>
    <td>Total Net Value of the invoices</td>';
$htmlTable .='<td>'.number_format($g_total_comp, 2).'</td>
</tr>';

$htmlTable .='<tr>
    <td>Total of VAT Collected on the invoices</td>';
$htmlTable .='<td>'.$vat=number_format($g_total_comp * 0.2, 2).'</td>
</tr>';

$htmlTable .='<tr>
    <td>Total of Non-Vat able charges</td>';
$htmlTable .='<td>'.number_format($total_otherCharg, 2).'</td>
</tr>';

$htmlTable .='<tr>
    <td>Total of Gross total of Invoices</td>';
$htmlTable .='<td>'.number_format($gross + $total_otherCharg + $gross * 0.2, 2).'</td>
</tr>';

$htmlTable .='<tr>
    <td>Total of Payments made to the Interpreters</td>';
$htmlTable .='<td>'.number_format($total_interp, 2).'</td>
</tr>';

$htmlTable .='<tr>
    <td>Total of Company Expenses</td>';
$htmlTable .='<td>'.number_format($total_expnce, 2).'</td>
</tr>';
$htmlTable .='<tr>
    <td> Total of Credit Notes </td>';
$htmlTable .='<td>'.number_format($gross_credit, 2).'</td>
</tr>';
$htmlTable .='<tr>
    <td>Total Revenue</td>';
$htmlTable .='<td>'.number_format($g_total_comp + $total_otherCharg - $total_interp - $total_expnce, 2).'</td>
</tr>';

$htmlTable .='</TABLE>';
$pdf->WriteHTML2("<br><br><br>$htmlTable");
$pdf->SetFont('Arial','B',6);
$pdf->Output(); 
?>