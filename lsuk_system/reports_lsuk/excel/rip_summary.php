<?php 

include '../../db.php';
include_once ('../../class.php'); 
$excel=@$_GET['excel'];
$search_1=@$_GET['search_1'];
$search_2=@$_GET['search_2'];
$search_3=@$_GET['search_3'];
$i=1;$payment_to_interpreters=0;$total_comp=0;$total_otherCharg=0;
$gross=0;$total_paid=0;$total_pending=0;$total_otherCharg_pending_intrp=0;
$total_otherCharg_pending_telep=0;$total_otherCharg_pending_trans=0;
$total_otherCharg_paid_interp=0;$total_otherCharg_paid_telep=0;
$total_otherCharg_paid_trans=0;$g_total_comp=0;
$total_interp_credit=0;$total_comp_credit=0;$total_otherCharg_credit=0;
$total_charges_comp_pending_vat_interp=0; $total_charges_comp_pending_vat_telep=0;
$total_charges_comp_pending_vat_trans=0;$total_charges_comp_vat_interp=0; 
$total_charges_comp_vat_telep=0; $total_charges_comp_vat_trans=0;
//Staff salaries
$staff_salary=0;
// $query_emp="SELECT emp.*,  rolcal.*  FROM emp
// join rolcal on emp.id = rolcal.empId 
// where rolcal.dated between '$search_2' and '$search_3' ##emp_active##";
// $query_emp=SqlUtils::ModfiySql($query_emp);

// $result_emp = mysqli_query($con, $query_emp);
// while($row_emp = mysqli_fetch_assoc($result_emp)){$staff_salary=$row_emp["salary"] + $staff_salary; }
// $staff_salary=$staff_salary;


$exp_qy=$acttObj->read_specific("count((CASE WHEN ROUND(vat,2)>0 THEN id END)) as count_vat_sp,ROUND(IFNULL(SUM(CASE WHEN ROUND(vat,2)>0 THEN vat ELSE 0 END),0),2) as vat_sp_inv,ROUND(IFNULL(SUM(CASE WHEN type_id NOT IN (16,17,32,35,36) THEN amoun ELSE 0 END),0),2) as total_expenses, ROUND(IFNULL(SUM(CASE WHEN type_id IN (16,17,35,36) THEN amoun ELSE 0 END),0),2) as tot_forbidden_expenses,ROUND(IFNULL(SUM(CASE WHEN type_id NOT IN (1,3,6,7,10,11,13,15,16,17,18,19,22,24,25,32,35,36,38,39,40,41,49,50) THEN amoun ELSE 0 END),0),2) as misc_exp,ROUND(IFNULL(SUM(CASE WHEN type_id = 1 THEN amoun ELSE 0 END),0),2) as transport_expenses,ROUND(IFNULL(SUM(CASE WHEN type_id = 3 THEN amoun ELSE 0 END),0),2) as utility_bills_exp,ROUND(IFNULL(SUM(CASE WHEN type_id = 24 THEN amoun ELSE 0 END),0),2) as prof_fee_exp,ROUND(IFNULL(SUM(CASE WHEN type_id = 7 OR type_id = 41 THEN amoun ELSE 0 END),0),2) as stf_trn_dev_exp,ROUND(IFNULL(SUM(CASE WHEN type_id=6 THEN amoun ELSE 0 END),0),2) as marketing_exp,ROUND(IFNULL(SUM(CASE WHEN type_id=10 THEN amoun ELSE 0 END),0),2) as computing_costs_exp,ROUND(IFNULL(SUM(CASE WHEN type_id IN (11,50) THEN amoun ELSE 0 END),0),2) as insurance_exp, ROUND(IFNULL(SUM(CASE WHEN type_id = 13 THEN amoun ELSE 0 END),0),2) as bank_charges, ROUND(IFNULL(SUM(CASE WHEN type_id IN (15,49) THEN amoun ELSE 0 END),0),2) as office_rent,ROUND(IFNULL(SUM(CASE WHEN type_id = 16 THEN amoun ELSE 0 END),0),2) as corporation_tax, ROUND(IFNULL(SUM(CASE WHEN type_id = 17 THEN amoun ELSE 0 END),0),2) as vat_exp_tax, ROUND(IFNULL(SUM(CASE WHEN type_id IN (18,19) THEN amoun ELSE 0 END),0),2) as paye_ni_tax, ROUND(IFNULL(SUM(CASE WHEN type_id=22 THEN amoun ELSE 0 END),0),2) as sub_contractor_exp, ROUND(IFNULL(SUM(CASE WHEN type_id=25 THEN amoun ELSE 0 END),0),2) as pension_exp, ROUND(IFNULL(SUM(CASE WHEN type_id = 32 THEN -vat ELSE 0 END),0),2) as vat_rental_income,ROUND(IFNULL(SUM(CASE WHEN type_id = 32 THEN amoun ELSE 0 END),0),2) as rental_income, ROUND(IFNULL(SUM(CASE WHEN type_id = 35 OR type_id = 36 THEN amoun ELSE 0 END),0),2) as loans_exp, ROUND(IFNULL(SUM(CASE WHEN type_id = 38 THEN amoun ELSE 0 END),0),2) as software_cost,ROUND(IFNULL(SUM(CASE WHEN type_id=39 THEN amoun ELSE 0 END),0),2) as admin_costs_exp, ROUND(IFNULL(SUM(CASE WHEN type_id = 40 THEN amoun ELSE 0 END),0),2) as staff_salary","expence"," (billDate BETWEEN '$search_2' and '$search_3') and deleted_flag=0");

$count_vat_sp=$exp_qy['count_vat_sp'];
$vat_sp_inv=$exp_qy['vat_sp_inv'];
$corporation_tax=$exp_qy['corporation_tax'];
$vat_exp_tax=$exp_qy['vat_exp_tax'];
$hmrc_py_vt = $corporation_tax+$vat_exp_tax;
$paye_ni_tax=$exp_qy['paye_ni_tax'];

$rental_income=str_replace("-","",$exp_qy['rental_income']);
$software_cost=$exp_qy['software_cost'];
$bank_charges=$exp_qy['bank_charges'];
$office_rent=$exp_qy['office_rent'];
$transport_expenses=$exp_qy['transport_expenses'];
$vat_rental_income=$exp_qy['vat_rental_income'];

$sub_contractor_exp=$exp_qy['sub_contractor_exp'];
$admin_costs_exp=$exp_qy['admin_costs_exp'];
$marketing_exp=$exp_qy['marketing_exp'];
$pension_exp=$exp_qy['pension_exp'];
$computing_costs_exp=$exp_qy['computing_costs_exp'];

$insurance_exp=$exp_qy['insurance_exp'];
$utility_bills_exp=$exp_qy['utility_bills_exp'];
$prof_fee_exp=$exp_qy['prof_fee_exp'];
$stf_trn_dev_exp=$exp_qy['stf_trn_dev_exp'];
$loans_exp=$exp_qy['loans_exp'];
$staff_salary=$exp_qy['staff_salary'];
$total_expnce=$exp_qy['total_expenses'];
$tot_forbidden_expenses=$exp_qy['tot_forbidden_expenses'];
$misc_exp=$exp_qy['misc_exp'];


$get_bad_debt=mysqli_query($con,"SELECT SUM(bad_debt_amount) as bd_amount, SUM(bd_count) as bad_debt_count, SUM(vat_bad_debt) as net_vat_bad_debt FROM (SELECT count(interpreter.id) as bd_count,round(IFNULL(SUM(interpreter.total_charges_comp),0),2) as bad_debt_amount, round(sum(IFNULL(interpreter.total_charges_comp,0)*interpreter.cur_vat),2) as vat_bad_debt FROM interpreter WHERE interpreter.disposed_of='1' and ROUND(interpreter.total_charges_comp,2)>0 and interpreter.assignDate between '$search_2' and '$search_3' UNION ALL SELECT count(telephone.id) as bd_count,round(IFNULL(SUM(telephone.total_charges_comp),0),2) as bad_debt_amount,round(sum(IFNULL(telephone.total_charges_comp,0)*telephone.cur_vat),2) as vat_bad_debt FROM telephone WHERE telephone.disposed_of='1' and ROUND(telephone.total_charges_comp,2)>0 and telephone.assignDate between '$search_2' and '$search_3' UNION ALL SELECT count(translation.id) as bd_count,round(IFNULL(SUM(translation.total_charges_comp),0),2) as bad_debt_amount,round(sum(IFNULL(translation.total_charges_comp,0)*translation.cur_vat),2) as vat_bad_debt FROM translation WHERE translation.disposed_of='1' and ROUND(translation.total_charges_comp,2)>0 and translation.asignDate between '$search_2' and '$search_3') as grp");

$fetch_bad_debt = mysqli_fetch_assoc($get_bad_debt);
$bad_debt_amount = $fetch_bad_debt['bd_amount'];
$net_vat_bad_debt = $fetch_bad_debt['net_vat_bad_debt'];
$bad_debt_count = $fetch_bad_debt['bad_debt_count'];

//interpreter paid salaries
$src2=substr($search_2,0,7);
$src3=substr($search_3,0,7);
// $int_sal=$acttObj->read_specific("sum(total_charges_interp) as int_sal","( SELECT round(IFNULL(sum(interpreter.total_charges_interp+(interpreter.total_charges_interp*interpreter.int_vat)),0),2) as total_charges_interp FROM interpreter,interpreter_reg, invoice","interpreter.invoiceNo=invoice.invoiceNo and interpreter.intrpName=interpreter_reg.id AND interpreter.commit=1 AND interpreter.deleted_flag = 0 and interpreter.order_cancel_flag=0 and interpreter.invoiceNo<>'' and interpreter.assignDate between '$search_2' and '$search_3'
// UNION ALL
// SELECT round(IFNULL(sum(telephone.total_charges_interp+(telephone.total_charges_interp*telephone.int_vat)),0),2) as total_charges_interp FROM telephone,interpreter_reg, invoice WHERE telephone.invoiceNo=invoice.invoiceNo and telephone.intrpName=interpreter_reg.id AND telephone.commit=1 AND telephone.deleted_flag = 0 and telephone.order_cancel_flag=0 and telephone.invoiceNo<>'' and telephone.assignDate between '$search_2' and '$search_3'
// UNION ALL
// SELECT round(IFNULL(sum(translation.total_charges_interp+(translation.total_charges_interp*translation.int_vat)),0),2) as total_charges_interp FROM translation,interpreter_reg, invoice WHERE translation.invoiceNo=invoice.invoiceNo and translation.intrpName=interpreter_reg.id AND translation.commit=1 AND translation.deleted_flag = 0 and translation.order_cancel_flag=0 and translation.invoiceNo<>'' and translation.asignDate between '$search_2' and '$search_3') as grp");
$payment_to_interpreters = $acttObj->read_specific("round(IFNULL(SUM((salry-ni_dedu-tax_dedu-payback_deduction)+given_amount),0),2) as salary", "interp_salary", "DATE(frm) BETWEEN '$search_2' and '$search_3' AND DATE(todate) BETWEEN '$search_2' and '$search_3' AND deleted_flag=0")['salary'];

$q_pd="SELECT sum(tot_reg) as total_reg_paid,sum(paid_i) as paid_i,sum(total_charges_comp) as total_charges_comp,sum(C_otherCharg) as C_otherCharg,
sum(total_charges_comp_vat) as total_charges_comp_vat,sum(net_total) as net_total from (SELECT count(interpreter.id) as tot_reg,count((CASE WHEN (interpreter.multInv_flag=0) OR (SELECT id from mult_inv WHERE m_inv=interpreter.multInvoicNo and mult_inv.status='Received') THEN interpreter.id END)) as paid_i,
round(IFNULL(sum((CASE WHEN (interpreter.multInv_flag=0) OR (SELECT id from mult_inv WHERE m_inv=interpreter.multInvoicNo and mult_inv.status='Received') THEN interpreter.total_charges_comp END)),0),2) total_charges_comp ,round(IFNULL(sum((CASE WHEN (interpreter.multInv_flag=0) OR (SELECT id from mult_inv WHERE m_inv=interpreter.multInvoicNo and mult_inv.status='Received') THEN interpreter.C_otherexpns END)),0),2) as C_otherCharg, 
round(IFNULL(sum((CASE WHEN (interpreter.multInv_flag=0) OR (SELECT id from mult_inv WHERE m_inv=interpreter.multInvoicNo and mult_inv.status='Received') THEN interpreter.total_charges_comp * interpreter.cur_vat END)),0),2) as total_charges_comp_vat,
round(IFNULL(sum((CASE WHEN (interpreter.multInv_flag=0) OR (SELECT id from mult_inv WHERE m_inv=interpreter.multInvoicNo and mult_inv.status='Received') THEN interpreter.total_charges_comp END)),0),2)+round(IFNULL(sum((CASE WHEN (interpreter.multInv_flag=0) OR (SELECT id from mult_inv WHERE m_inv=interpreter.multInvoicNo and mult_inv.status='Received') THEN interpreter.C_otherexpns END)),0),2)+round(IFNULL(sum((CASE WHEN (interpreter.multInv_flag=0) OR (SELECT id from mult_inv WHERE m_inv=interpreter.multInvoicNo and mult_inv.status='Received') THEN interpreter.total_charges_comp * interpreter.cur_vat END)),0),2) as net_total
 FROM interpreter inner join interpreter_reg on interpreter.intrpName = interpreter_reg.id 
 where interpreter.deleted_flag = 0 and interpreter.order_cancel_flag=0 
 and assignDate between '$search_2' and '$search_3' 
 AND (
        (round(interpreter.rAmount, 2) >= round((interpreter.total_charges_comp + (interpreter.total_charges_comp * interpreter.cur_vat)), 2) and interpreter.total_charges_comp >0 AND interpreter.commit=1) 
        OR interpreter.multInv_flag = 1
    )
AND (
        (interpreter.multInv_flag = 0 AND interpreter.invoiceNo<>'') 
        OR  (interpreter.multInv_flag = 1 ) 
    )

UNION ALL
SELECT count(telephone.id) as tot_reg,count((CASE WHEN (telephone.multInv_flag=0) OR (SELECT id from mult_inv WHERE m_inv=telephone.multInvoicNo and mult_inv.status='Received') THEN telephone.id END)) as paid_i,round(IFNULL(sum((CASE WHEN (telephone.multInv_flag=0) OR (SELECT id from mult_inv WHERE m_inv=telephone.multInvoicNo and mult_inv.status='Received') THEN telephone.total_charges_comp END)),0),2) total_charges_comp ,round(IFNULL(sum((CASE WHEN (telephone.multInv_flag=0) OR (SELECT id from mult_inv WHERE m_inv=telephone.multInvoicNo and mult_inv.status='Received') THEN telephone.C_otherCharges END)),0),2) as C_otherCharg, round(IFNULL(sum((CASE WHEN (telephone.multInv_flag=0) OR (SELECT id from mult_inv WHERE m_inv=telephone.multInvoicNo and mult_inv.status='Received') THEN telephone.total_charges_comp * telephone.cur_vat END)),0),2) as total_charges_comp_vat,round(IFNULL(sum((CASE WHEN (telephone.multInv_flag=0) OR (SELECT id from mult_inv WHERE m_inv=telephone.multInvoicNo and mult_inv.status='Received') THEN telephone.total_charges_comp END)),0),2)+round(IFNULL(sum((CASE WHEN (telephone.multInv_flag=0) OR (SELECT id from mult_inv WHERE m_inv=telephone.multInvoicNo and mult_inv.status='Received') THEN telephone.C_otherCharges END)),0),2)+round(IFNULL(sum((CASE WHEN (telephone.multInv_flag=0) OR (SELECT id from mult_inv WHERE m_inv=telephone.multInvoicNo and mult_inv.status='Received') THEN telephone.total_charges_comp * telephone.cur_vat END)),0),2) as net_total FROM telephone inner join interpreter_reg on telephone.intrpName = interpreter_reg.id where telephone.deleted_flag = 0 and telephone.order_cancel_flag=0 and assignDate between '$search_2' and '$search_3' 
 AND (
        (round(telephone.rAmount, 2) >= round((telephone.total_charges_comp + (telephone.total_charges_comp * telephone.cur_vat)), 2) and telephone.total_charges_comp > 0 AND telephone.commit=1) 
        OR telephone.multInv_flag = 1
    )
AND (
        (telephone.multInv_flag = 0 AND telephone.invoiceNo<>'') 
        OR  (telephone.multInv_flag = 1 ) 
    )

UNION ALL
SELECT count(translation.id) as tot_reg,count((CASE WHEN (translation.multInv_flag=0) OR (SELECT id from mult_inv WHERE m_inv=translation.multInvoicNo and mult_inv.status='Received') THEN translation.id END)) as paid_i,round(IFNULL(sum((CASE WHEN (translation.multInv_flag=0) OR (SELECT id from mult_inv WHERE m_inv=translation.multInvoicNo and mult_inv.status='Received') THEN translation.total_charges_comp END)),0),2) total_charges_comp ,round(IFNULL(sum((CASE WHEN (translation.multInv_flag=0) OR (SELECT id from mult_inv WHERE m_inv=translation.multInvoicNo and mult_inv.status='Received') THEN translation.C_otherCharg END)),0),2) as C_otherCharg, round(IFNULL(sum((CASE WHEN (translation.multInv_flag=0) OR (SELECT id from mult_inv WHERE m_inv=translation.multInvoicNo and mult_inv.status='Received') THEN translation.total_charges_comp * translation.cur_vat END)),0),2) as total_charges_comp_vat,round(IFNULL(sum((CASE WHEN (translation.multInv_flag=0) OR (SELECT id from mult_inv WHERE m_inv=translation.multInvoicNo and mult_inv.status='Received') THEN translation.total_charges_comp END)),0),2)+round(IFNULL(sum((CASE WHEN (translation.multInv_flag=0) OR (SELECT id from mult_inv WHERE m_inv=translation.multInvoicNo and mult_inv.status='Received') THEN translation.C_otherCharg END)),0),2)+round(IFNULL(sum((CASE WHEN (translation.multInv_flag=0) OR (SELECT id from mult_inv WHERE m_inv=translation.multInvoicNo and mult_inv.status='Received') THEN translation.total_charges_comp * translation.cur_vat END)),0),2) as net_total FROM translation inner join interpreter_reg on translation.intrpName = interpreter_reg.id where translation.deleted_flag = 0 and translation.order_cancel_flag=0 and asignDate between '$search_2' and '$search_3'
  AND (
        (round(translation.rAmount, 2) >= round((translation.total_charges_comp + (translation.total_charges_comp * translation.cur_vat)), 2) and translation.total_charges_comp >0 AND translation.commit=1) 
        OR translation.multInv_flag = 1
    )
AND (
        (translation.multInv_flag = 0 AND translation.invoiceNo<>'') 
        OR  (translation.multInv_flag = 1 ) 
    )

 ) as grp";
// echo $q_pd;exit;
$res_pd = mysqli_query($con, $q_pd);
$row_pd = mysqli_fetch_assoc($res_pd);
$paid_i=$row_pd["paid_i"];
$total_charges_pd=$row_pd["total_charges_comp"];
$C_otherCharg_pd=$row_pd["C_otherCharg"];
$total_vat_pd=$row_pd["total_charges_comp_vat"];
$net_total_pd=$row_pd["net_total"];

$q_pe="SELECT sum(tot_reg) as total_reg_pend,sum(pend_i) as pend_i,sum(total_charges_comp) as total_charges_comp,sum(C_otherCharg) as C_otherCharg,sum(total_charges_comp_vat) as total_charges_comp_vat,sum(net_total) as net_total from (SELECT count(interpreter.id) as tot_reg,count( (CASE WHEN (interpreter.multInv_flag=0) OR (SELECT id from mult_inv WHERE m_inv=interpreter.multInvoicNo and mult_inv.status='') THEN interpreter.id END)) as pend_i,round(IFNULL(sum((CASE WHEN (interpreter.multInv_flag=0) OR (SELECT id from mult_inv WHERE m_inv=interpreter.multInvoicNo and mult_inv.status='') THEN interpreter.total_charges_comp END)),0),2) as total_charges_comp ,round(IFNULL(sum((CASE WHEN (interpreter.multInv_flag=0) OR (SELECT id from mult_inv WHERE m_inv=interpreter.multInvoicNo and mult_inv.status='') THEN interpreter.C_otherexpns END)),0),2) as C_otherCharg, round(IFNULL(sum((CASE WHEN (interpreter.multInv_flag=0) OR (SELECT id from mult_inv WHERE m_inv=interpreter.multInvoicNo and mult_inv.status='') THEN interpreter.total_charges_comp * interpreter.cur_vat END)),0),2) as total_charges_comp_vat,round(IFNULL(sum((CASE WHEN (interpreter.multInv_flag=0) OR (SELECT id from mult_inv WHERE m_inv=interpreter.multInvoicNo and mult_inv.status='') THEN interpreter.total_charges_comp END)),0),2)+round(IFNULL(sum((CASE WHEN (interpreter.multInv_flag=0) OR (SELECT id from mult_inv WHERE m_inv=interpreter.multInvoicNo and mult_inv.status='') THEN C_otherexpns END)),0),2)+round(IFNULL(sum((CASE WHEN (interpreter.multInv_flag=0) OR (SELECT id from mult_inv WHERE m_inv=interpreter.multInvoicNo and mult_inv.status='') THEN interpreter.total_charges_comp * interpreter.cur_vat END)),0),2) as net_total FROM interpreter inner join interpreter_reg on interpreter.intrpName = interpreter_reg.id where interpreter.deleted_flag = 0 and interpreter.order_cancel_flag=0  and assignDate between '$search_2' and '$search_3'  
  AND (
    (round(interpreter.rAmount,2) < round((interpreter.total_charges_comp+(interpreter.total_charges_comp*interpreter.cur_vat)),2) AND interpreter.total_charges_comp >0)
        OR interpreter.multInv_flag = 1
    )
AND (
        (interpreter.multInv_flag = 0 AND interpreter.invoiceNo<>'') 
        OR  (interpreter.multInv_flag = 1 ) 
    )
UNION ALL
SELECT count(telephone.id) as tot_reg,count((CASE WHEN (telephone.multInv_flag=0) OR (SELECT id from mult_inv WHERE m_inv=telephone.multInvoicNo and mult_inv.status='') THEN telephone.id END)) as pend_i,round(IFNULL(sum((CASE WHEN (telephone.multInv_flag=0) OR (SELECT id from mult_inv WHERE m_inv=telephone.multInvoicNo and mult_inv.status='') THEN telephone.total_charges_comp END)),0),2) total_charges_comp ,round(IFNULL(sum((CASE WHEN (telephone.multInv_flag=0) OR (SELECT id from mult_inv WHERE m_inv=telephone.multInvoicNo and mult_inv.status='') THEN telephone.C_otherCharges END)),0),2) as C_otherCharg, round(IFNULL(sum((CASE WHEN (telephone.multInv_flag=0) OR (SELECT id from mult_inv WHERE m_inv=telephone.multInvoicNo and mult_inv.status='') THEN telephone.total_charges_comp * telephone.cur_vat END)),0),2) as total_charges_comp_vat,round(IFNULL(sum((CASE WHEN (telephone.multInv_flag=0) OR (SELECT id from mult_inv WHERE m_inv=telephone.multInvoicNo and mult_inv.status='') THEN telephone.total_charges_comp END)),0),2)+round(IFNULL(sum((CASE WHEN (telephone.multInv_flag=0) OR (SELECT id from mult_inv WHERE m_inv=telephone.multInvoicNo and mult_inv.status='') THEN telephone.C_otherCharges END)),0),2)+round(IFNULL(sum((CASE WHEN (telephone.multInv_flag=0) OR (SELECT id from mult_inv WHERE m_inv=telephone.multInvoicNo and mult_inv.status='') THEN telephone.total_charges_comp * telephone.cur_vat END)),0),2) as net_total FROM telephone inner join interpreter_reg on telephone.intrpName = interpreter_reg.id where telephone.deleted_flag = 0 and telephone.order_cancel_flag=0 and assignDate between '$search_2' and '$search_3' 
AND (
    (round(telephone.rAmount,2) < round((telephone.total_charges_comp+(telephone.total_charges_comp*telephone.cur_vat)),2) and telephone.total_charges_comp > 0 )
    OR telephone.multInv_flag = 1
)
AND (
    (telephone.multInv_flag = 0 AND telephone.invoiceNo<>'') 
    OR  (telephone.multInv_flag = 1 ) 
)
UNION ALL
SELECT count(translation.id) as tot_reg,count((CASE WHEN (translation.multInv_flag=0) OR (SELECT id from mult_inv WHERE m_inv=translation.multInvoicNo and mult_inv.status='') THEN translation.id END)) as pend_i,round(IFNULL(sum((CASE WHEN (translation.multInv_flag=0) OR (SELECT id from mult_inv WHERE m_inv=translation.multInvoicNo and mult_inv.status='') THEN translation.total_charges_comp END)),0),2) total_charges_comp ,round(IFNULL(sum((CASE WHEN (translation.multInv_flag=0) OR (SELECT id from mult_inv WHERE m_inv=translation.multInvoicNo and mult_inv.status='') THEN translation.C_otherCharg END)),0),2) as C_otherCharg, round(IFNULL(sum((CASE WHEN (translation.multInv_flag=0) OR (SELECT id from mult_inv WHERE m_inv=translation.multInvoicNo and mult_inv.status='') THEN translation.total_charges_comp * translation.cur_vat END)),0),2) as total_charges_comp_vat,round(IFNULL(sum((CASE WHEN (translation.multInv_flag=0) OR (SELECT id from mult_inv WHERE m_inv=translation.multInvoicNo and mult_inv.status='') THEN translation.total_charges_comp END)),0),2)+round(IFNULL(sum((CASE WHEN (translation.multInv_flag=0) OR (SELECT id from mult_inv WHERE m_inv=translation.multInvoicNo and mult_inv.status='') THEN translation.C_otherCharg END)),0),2)+round(IFNULL(sum((CASE WHEN (translation.multInv_flag=0) OR (SELECT id from mult_inv WHERE m_inv=translation.multInvoicNo and mult_inv.status='') THEN translation.total_charges_comp * translation.cur_vat END)),0),2) as net_total FROM translation inner join interpreter_reg on translation.intrpName = interpreter_reg.id where translation.deleted_flag = 0 and translation.order_cancel_flag=0 and asignDate between '$search_2' and '$search_3' 
AND (
    (round(translation.rAmount,2) < round((translation.total_charges_comp+(translation.total_charges_comp*translation.cur_vat)),2) AND translation.total_charges_comp >0 )
    OR translation.multInv_flag = 1
)
AND (
    (translation.multInv_flag = 0 AND translation.invoiceNo<>'') 
    OR  (translation.multInv_flag = 1 ) 
)
) as grp";
// echo $q_pe;exit;
$res_pe = mysqli_query($con, $q_pe);
$row_pe = mysqli_fetch_assoc($res_pe);
$pend_i=$row_pe["pend_i"];
$total_charges_pe=$row_pe["total_charges_comp"];
$C_otherCharg_pe=$row_pe["C_otherCharg"];
$total_vat_pe=$row_pe["total_charges_comp_vat"];
$net_total_pe=$row_pe["net_total"];

$credit_notes=$acttObj->read_specific("count(*) as counter","credit_notes"," dated between '$search_2' and '$search_3' ")['counter'];
$total_credit_note_vat = $total_credit_note_charges = $total_credit_note_value = 0;
$get_credit_notes = $acttObj->read_all("order_type,data", "credit_notes", " dated between '$search_2' and '$search_3' ");
while($row_credit_note = $get_credit_notes->fetch_assoc()){
    $json_data=json_decode($row_credit_note['data'], true);
    $credit_note_vat = $json_data['cur_vat'] * $json_data['total_charges_comp'];
    $credit_note_charges = $json_data['order_type'] == 'f2f' ? $json_data['total_charges_comp'] + $json_data['C_otherexpns'] : $json_data['total_charges_comp'];
    $credit_note_value = $credit_note_charges + $credit_note_vat;
    $total_credit_note_vat += $credit_note_vat;
    $total_credit_note_charges += $credit_note_charges;
    $total_credit_note_value += $credit_note_value;
}

/*Summaries Calculation*/
$total_invoices=$paid_i+$pend_i;
$total_charges=$total_charges_pd+$total_charges_pe;
$C_otherCharg=$C_otherCharg_pd+$C_otherCharg_pe;
$total_vat=$total_vat_pd+$total_vat_pe;

$total_vat=$total_vat+$vat_rental_income;
$tot_fvat = $total_vat-$vat_sp_inv-$total_credit_note_vat-$net_vat_bad_debt; 

// $net_total=$net_total_pd+$net_total_pe; 
// $total_expnce = $total_expnce+$total_credit_note_charges+$bad_debt_amount;
$net_total=$net_total_pd+$net_total_pe-$total_credit_note_charges; 
$total_expnce = $total_expnce+$bad_debt_amount;

$net_tot_income = $net_total+$rental_income;
$tot_revenue_after_vat = $net_total+$rental_income-$tot_fvat;
$tot_revenue=$tot_revenue_after_vat-$payment_to_interpreters;
//...................................................................................................................................../////
$htmlTable='';
$htmlTable.='<style>
table {border-collapse: collapse; width:670px;}
th {border: 1px solid #999; padding: 0.5rem;text-align: left;background-color:#039; color:#FFF; font-weight:bold;}
td {border: 1px solid #999; padding: 0.5rem;text-align: left;}
.bg-primary{background-color: #337ab7;color:white;}
.bg-info{background-color: #d9edf7;}
</style>
<div>
<h2 align="center"><u>Profit and Loss Summary</u></h2>
<p align="right">Report  Date: '.$misc->sys_date().'<br />
  Date  Range: Date From ['.$misc->dated($search_2).'] Date To ['.$misc->dated($search_3).']</p>
</div>

<table>
<thead>
    <th></th>
    <th>Description</th>
    <th>Count</th>
    <th>Values in Pounds</th>
    <th>Aggreagate Values in Pounds</th>
</thead>
<tbody>';
$htmlTable .='<tr>
<th>REVENUE</th>
<td></td>
<td></td>
<td></td>
<th></th>
</tr>';
$htmlTable .='<tr>
<th>Sales</th>
<td></td>
<td></td>
<td></td>
<td></td>
</tr>';
$htmlTable .='<tr>
<td></td>
<td>Total Registered invoices</td>
<th>'.$total_invoices.'</th>
<td></td>
<td></td>
</tr>
<tr>
<td></td>
<td>Total Paid invoices</td>
<th>'.$paid_i.'</th>
<td></td>
<td></td>
</tr>
<tr>
<td></td>
<td>Cash received (Total Value of Paid Invoices)</td>
<td></td>
<th>'.$net_total_pd.'</th>
<td></td>
</tr>
<tr>
<td></td>
<td>Total Pending invoices</td>
<th>'.$pend_i.'</th>
<td></td>
<td></td>
</tr>
<tr>
<td></td>
<td>Receivables (Total Value of Pending Invoices)</td>
<td></td>
<th>'.$net_total_pe.'</th>
<td></td>
</tr>
<tr>
    <td></td>
    <td>Credit Notes</td>
    <td>'.$credit_notes.'</td>
    <th>'.($total_credit_note_charges>0?"-".$total_credit_note_charges:$total_credit_note_charges).'</th>
    <td></td>
</tr>
<tr style="border-bottom: 3px solid #000000;">
<th>Total From Sales</th>
<td></td>
<td></td>
<td></td>
<th>'.$net_total.' (Non-VAT = '.$C_otherCharg.')</th>
</tr>
<tr>
<th>Other Operating Revenue</th>
<td></td>
<td></td>
<td></td>
<th></th>
</tr>
<tr>
<td></td>
<td>Rental Income</td>
<td></td>
<th>'.$rental_income.'</th>
<td></td>
</tr>
<tr>
<th>Total Other Operating Revenue</th>
<td></td>
<td></td>
<td></td>
<th>'.$rental_income.'</th>
</tr>
<tr class="bg-info" style="border-top: 3px solid #000000;border-bottom: 3px solid #000000;">
<th>Total Revenue</th>
<td></td>
<td></td>
<td></td>
<th>'. $net_tot_income.'</th>
</tr>
';

$htmlTable .='
<tr>
    <th>VAT</th>
    <td></td>
    <td></td>
    <td></td>
    <th></th>
</tr>
<tr>
    <td></td>
    <td>VAT collected on PAID invoices</td>
    <td></td>
    <th>'.$total_vat_pd.'</th>
    <td></td>
</tr>
<tr>
    <td></td>
    <td>VAT to be collected from PENDING invoices</td>
    <td></td>
    <th>'.$total_vat_pe.'</th>
    <td></td>
</tr>
<tr>
    <td></td>
    <td>VAT collected from Rental income</td>
    <td></td>
    <th>'.$vat_rental_income.'</th>
    <td></td>
</tr>
<tr>
    <td></td>
    <td>VAT Paid on supplier invoices</td>
    <td>'.$count_vat_sp.'</td>
    <th>'.'-'.$vat_sp_inv.'</th>
    <td></td>
</tr>
<tr>
    <td></td>
    <td>VAT reversal on credit notes (all time)</td>
    <td></td>
    <th>'.'-'.$total_credit_note_vat.'</th>
    <td></td>
</tr>
<tr>
    <td></td>
    <td>VAT reversal on bad debt (all time)</td>
    <td></td>
    <th>'.'-'.$net_vat_bad_debt.'</th>
    <td></td>
</tr>
<tr  style="border-bottom: 3px solid #000000;">
    <th>Total VAT</th>
    <td></td>
    <td></td>
    <td></td>
    <th>'.$tot_fvat.'</th>
</tr>
<tr  style="border-top: 3px solid #000000;border-bottom: 3px solid #000000;">
    <th>Net Revenue after deducting VAT</th>
    <td></td>
    <td></td>
    <td></td>
    <th>'.($tot_revenue_after_vat).'</th>
</tr>
<tr>
    <th>Cost of Sales</th>
    <td></td>
    <td></td>
    <td></td>
    <th></th>
</tr>
<tr>
    <td></td>
    <td>Payment to interpreters</td>
    <td></td>
    <th>'.$payment_to_interpreters.'</th>
    <td></td>
</tr>
<tr style="border-bottom: 3px solid #000000;">
    <th>Total Cost of Sales</th>
    <td></td>
    <td></td>
    <td></td>
    <th>'.$payment_to_interpreters.'</th>
</tr>
<tr style="border-bottom: 3px solid #000000;">
    <th>Gross Profit (after deducting cost of sales)</th>
    <td></td>
    <td></td>
    <td></td>
    <th>'.$tot_revenue.'</th>
</tr>
<tr>
    <th>Expenses</th>
    <td></td>
    <td></td>
    <td></td>
    <th></th>
</tr>
<tr>
    <td></td>
    <td>Staff Wages</td>
    <td></td>
    <th>'.$staff_salary.'</th>
    <td></td>
</tr>
<tr>
    <td></td>
    <td>Staff training & Development</td>
    <td></td>
    <th>'.$stf_trn_dev_exp.'</th>
    <td></td>
</tr>
<tr>
    <td></td>
    <td>Insurance</td>
    <td></td>
    <th>'.$insurance_exp.'</th>
    <td></td>
</tr>
<tr>
    <td></td>
    <td>PAYE and NI Taxes</td>
    <td></td>
    <th>'.$paye_ni_tax.'</th>
    <td></td>
</tr>
<tr>
    <td></td>
    <td>Software Cost</td>
    <td></td>
    <th>'.$software_cost.'</th>
    <td></td>
</tr>
<tr>
    <td></td>
    <td>Computing Costs</td>
    <td></td>
    <th>'.$computing_costs_exp.'</th>
    <td></td>
</tr>
<tr>
    <td></td>
    <td>Admin Cost</td>
    <td></td>
    <th>'.$admin_costs_exp.'</th>
    <td></td>
</tr>
<tr>
    <td></td>
    <td>Rent</td>
    <td></td>
    <th>'.$office_rent.'</th>
    <td></td>
</tr>
<tr>
    <td></td>
    <td>Transport Expenses</td>
    <td></td>
    <th>'.$transport_expenses.'</th>
    <td></td>
</tr>

<tr>
    <td></td>
    <td>Bank Charges</td>
    <td></td>
    <th>'.$bank_charges.'</th>
    <td></td>
</tr>
<tr>
    <td></td>
    <td>Sub Contractor</td>
    <td></td>
    <th>'.$sub_contractor_exp.'</th>
    <td></td>
</tr>
<tr>
    <td></td>
    <td>Marketing</td>
    <td></td>
    <th>'.$marketing_exp.'</th>
    <td></td>
</tr>
<tr>
    <td></td>
    <td>Pension</td>
    <td></td>
    <th>'.$pension_exp.'</th>
    <td></td>
</tr>
<tr>
    <td></td>
    <td>Utility bills</td>
    <td></td>
    <th>'.$utility_bills_exp.'</th>
    <td></td>
</tr>
<tr>
    <td></td>
    <td>Professional Fees</td>
    <td></td>
    <th>'.$prof_fee_exp.'</th>
    <td></td>
</tr>
<tr>
    <td></td>
    <td>Bad debt (all time)</td>
    <td>'.$bad_debt_count.'</td>
    <th>'.$bad_debt_amount.'</th>
    <td></td>
</tr>
<tr>
    <td></td>
    <td>Misc. Expenses</td>
    <td></td>
    <th>'.$misc_exp.'</th>
    <td></td>
</tr>
<tr style="border-bottom: 3px solid #000000;">
    <th>Total Expenses</th>
    <td></td>
    <td></td>
    <td></td>
    <th>'.$total_expnce.'</th>
</tr>
<tr class="bg-info">
    <th>NET REVENUE (Profit/Loss)</th>
    <td></td>
    <td></td>
    <td></td>
    <th>'.$pr_bf_taxes = $tot_revenue-$total_expnce.'</th>
</tr>';
$htmlTable.='</tbody></table>';

$htmlTable.="</tbody>
</table>
<h1 style='text-align:center;margin-top:5rem;'>Exluded Expenses / Management Figures</h1>
<table class='table table-bordered table-hover'>
<thead class='bg-primary'>
    <th></th>
    <th>Description</th>
    <th>Count</th>
    <th>Values in Pounds</th>
    <th>Aggreagate Values in Pounds</th>
</thead>
<tbody>
<tr>
    <th>Excluded Expenses</th>
    <td></td>
    <td></td>
    <td></td>
    <th></th>
</tr>
<tr>
    <td></td>
    <td>HMRC (Paymnets & VAT)</td>
    <td></td>
    <th>$hmrc_py_vt</th>
    <td></td>
</tr>
<tr>
    <td></td>
    <td>Loans (Grants & Repayments)</td>
    <td></td>
    <th>$loans_exp</th>
    <td></td>
</tr>
<tr style='border-top: 3px solid #000000; border-bottom: 3px solid #000000;'>
    <th>Total Excluded Expenses</th>
    <td></td>
    <td></td>
    <td></td>
    <th>$tot_forbidden_expenses</th>
</tr>
</tbody>
</table>";

list($a,$b)=explode('.',basename(__FILE__));
header("Content-Type: application/xls");
header("Content-Disposition: attachment; filename=".$a.".xls");  
echo $htmlTable;
