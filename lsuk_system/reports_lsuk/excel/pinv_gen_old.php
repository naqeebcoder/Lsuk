<?php 
ob_start();
session_start();
include '../../db.php';include_once ('../../class.php'); 
$semi="\"'\"";
$p_org = @$_GET['p_org'];

if(isset($_GET['p_org'])){
    $p_org = $_GET['p_org'];
    $p_org_q = $acttObj->read_specific("GROUP_CONCAT(child_comp) as ch_ids", "subsidiaries", " parent_comp=$p_org")['ch_ids']?:'0';
    $p_org_ad = ($p_org_q!=0?" and comp_reg.id IN ($p_org_q) ":" and comp_reg.id IN ($p_org) ");
    $get_pname = $acttObj->read_specific("name,abrv","comp_reg"," id=$p_org ");
    $p_name = $get_pname['name'];
    $p_abrv = $get_pname['abrv'];

}else{
    $p_org_ad = $p_org;
    die();exit();
}

$query =
    'SELECT * from (SELECT interpreter.porder,comp_reg.po_req,"Interpreter" as type,interpreter.id,interpreter.intrpName,interpreter.orgName,interpreter_reg.name,interpreter.source,interpreter.invoic_date,interpreter.assignDate,interpreter.assignTime,interpreter.orgContact,interpreter.submited, interpreter.aloct_by,interpreter.aloct_date,interpreter.dated,interpreter.hrsubmited,interpreter.comp_hrsubmited,interpreter.interp_hr_date, interpreter.comp_hr_date,interpreter.hoursWorkd,interpreter.C_hoursWorkd,interpreter.total_charges_comp,interpreter.cur_vat,interpreter.C_otherexpns, interpreter.credit_note,interpreter.C_admnchargs,interpreter.rAmount,interpreter.rDate,interpreter.sentemail,interpreter.printed,interpreter.printedby,interpreter.deleted_flag,interpreter.order_cancel_flag,interpreter.nameRef,interpreter.orgRef,interpreter.invoiceNo,interpreter.commit,comp_reg.email,comp_reg.abrv as comp_abrv FROM interpreter,interpreter_reg,comp_reg WHERE interpreter.intrpName=interpreter_reg.id AND interpreter.orgName=comp_reg.abrv AND interpreter.deleted_flag = 0 AND interpreter.disposed_of = 0 and interpreter.order_cancel_flag=0 and interpreter.commit=1  and (round(interpreter.rAmount,2) < round((interpreter.total_charges_comp+(interpreter.total_charges_comp*interpreter.cur_vat)),2) or interpreter.total_charges_comp =0) '.$p_org_ad.'
    UNION ALL SELECT telephone.porder,comp_reg.po_req,"Telephone" as type,telephone.id,telephone.intrpName,telephone.orgName,interpreter_reg.name,telephone.source,telephone.invoic_date,telephone.assignDate,telephone.assignTime,telephone.orgContact,telephone.submited, telephone.aloct_by,telephone.aloct_date,telephone.dated,telephone.hrsubmited,telephone.comp_hrsubmited,telephone.interp_hr_date,telephone.comp_hr_date, telephone.hoursWorkd,telephone.C_hoursWorkd,telephone.total_charges_comp,telephone.cur_vat,0 as C_otherexpns,telephone.credit_note,telephone.C_admnchargs, telephone.rAmount,telephone.rDate,telephone.sentemail,telephone.printed,telephone.printedby,telephone.deleted_flag,telephone.order_cancel_flag,telephone.nameRef,telephone.orgRef,telephone.invoiceNo,telephone.commit,comp_reg.email,comp_reg.abrv as comp_abrv FROM telephone,interpreter_reg,comp_reg WHERE telephone.intrpName=interpreter_reg.id AND telephone.orgName=comp_reg.abrv AND telephone.deleted_flag = 0 AND telephone.disposed_of = 0 and telephone.order_cancel_flag=0 and telephone.commit=1 and (round(telephone.rAmount,2) < round((telephone.total_charges_comp+(telephone.total_charges_comp*telephone.cur_vat)),2) or telephone.total_charges_comp =0) '.$p_org_ad.'
    UNION ALL SELECT translation.porder,comp_reg.po_req,"Translation" as type,translation.id,translation.intrpName,translation.orgName,interpreter_reg.name,translation.source,translation.invoic_date,translation.asignDate as assignDate,"00:00:00" as assignTime,translation.orgContact,translation.submited, translation.aloct_by,translation.aloct_date,translation.dated,translation.hrsubmited,translation.comp_hrsubmited,translation.interp_hr_date,translation.comp_hr_date, translation.numberUnit as hoursWorkd,translation.C_numberUnit as C_hoursWorkd,translation.total_charges_comp,translation.cur_vat,0 as C_otherexpns,translation.credit_note,translation.C_admnchargs, translation.rAmount,translation.rDate,translation.sentemail,translation.printed,translation.printedby,translation.deleted_flag,translation.order_cancel_flag,translation.nameRef,translation.orgRef,translation.invoiceNo,translation.commit,comp_reg.email,comp_reg.abrv as comp_abrv FROM translation,interpreter_reg,comp_reg WHERE translation.intrpName=interpreter_reg.id AND translation.orgName=comp_reg.abrv AND translation.deleted_flag = 0 AND translation.disposed_of = 0 and translation.order_cancel_flag=0 and translation.commit=1 and (round(translation.rAmount,2) < round((translation.total_charges_comp+(translation.total_charges_comp*translation.cur_vat)),2) or translation.total_charges_comp =0) '.$p_org_ad.') as grp ORDER BY CONCAT(assignDate," ",assignTime)';


                    
                
                // echo $query;exit;
$result = mysqli_query($con, $query);

$put_var= 'Outstanding invoices list for '.$p_abrv;

$htmlTable='';
$htmlTable.='<style>
table {border-collapse: collapse; width:670px;}
th {border: 1px solid #999; padding: 0.5rem;text-align: left;background-color:#039; color:#FFF; font-weight:bold;}
td {border: 1px solid #999; padding: 0.5rem;text-align: left;}
</style>
<div>';
$htmlTable .='<h2 style="text-align:center;background:grey">'.$put_var.'</h2>
<p align="right"> Report Date: '.$misc->sys_date(). '</p>
<p>Organization Name : ' .$p_name. '</p>
</div>

<table>
<thead>
<tr>
    <th style="background-color:#039;color:#FFF;">Sr.No</th>
    <th style="background-color:#039;color:#FFF;">Company Name</th>
    <th style="background-color:#039;color:#FFF;">Type</th>
    <th style="background-color:#039;color:#FFF;">Assignment Date</th>
    <th style="background-color:#039;color:#FFF;">Invoice Number</th>
    <th style="background-color:#039;color:#FFF;">Contact Name</th>
    <th style="background-color:#039;color:#FFF;">Client Reference</th>
    <th style="background-color:#039;color:#FFF;">Linguist</th>
    <th style="background-color:#039;color:#FFF;">Purchase order #</th>
    <th style="background-color:#039;color:#FFF;">Net Amount</th>
    <th style="background-color:#039;color:#FFF;">VAT</th>
    <th style="background-color:#039;color:#FFF;">NON VAT</th>
    <th style="background-color:#039;color:#FFF;">Total Amount</th>
    </tr></thead>';
    $i=1;
    $tot_amount = 0;
    while($row = mysqli_fetch_assoc($result)){
        $append_invoiceNo='';
        if(!empty($row['credit_note']) && $row['type']=="Interpreter"){
          $append_invoiceNo="-0".$acttObj->read_specific("count(*) as counter","credit_notes","order_id=".$row['id']." and order_type='f2f'")['counter'];
        }elseif(!empty($row['credit_note']) && $row['type']=="Telephone"){
            $append_invoiceNo="-0".$acttObj->read_specific("count(*) as counter","credit_notes","order_id=".$row['id']." and order_type='tp'")['counter'];
        }elseif(!empty($row['credit_note']) && $row['type']=="Translation"){
            $append_invoiceNo="-0".$acttObj->read_specific("count(*) as counter","credit_notes","order_id=".$row['id']." and order_type='tr'")['counter'];
        }
        $table_po='';
        if($row['po_req']==1 && $row['porder']!=''){ $table_po= $row['porder'];}else if($row['po_req']==1 && $row['porder']==''){$table_po= '<b style="color:red;">Missing!</b>';}else{$table_po= '<b>Not required!</b>';}
    
        if($row['type']=='Interpreter'){
            $totalforvat = $row['total_charges_comp'];
            $vatpay = $totalforvat * $row['cur_vat'];
            $totinvnow = $totalforvat + $vatpay + $row['C_otherexpns'];
        }else if($row['type']=='Telephone'){
            $totalforvat=$row['total_charges_comp'];
            $vatpay=$totalforvat*$row['cur_vat'];
            $totinvnow=$totalforvat+$vatpay;
        }else{
            $totalforvat=$row['total_charges_comp'];
            $vatpay=$totalforvat*$row['cur_vat'];
            $totinvnow=$totalforvat+$vatpay;
        }
    if($totinvnow<=0){ continue; }
    $tot_amount = $tot_amount+$misc->numberFormat_fun($totinvnow);
    $htmlTable.="<tr>
              <td>".$i."</td>
            <td>".$row["orgName"]."</td>
            <td>".($row["type"]=='Interpreter'?'Face 2 Face':$row["type"])."</td>
            <td>".$row["assignDate"]."</td>
            <td>".$row["invoiceNo"].$append_invoiceNo."</td>
            <td>".$row["orgContact"]."</td>
            <td>".$row["orgRef"]."</td>
            <td>".$row["name"]."</td>
            <td>".$table_po."</td>
            <td>".$row["total_charges_comp"]."</td>
            <td>".$row["total_charges_comp"]*$row["cur_vat"]."</td>
            <td>".$row["C_otherexpns"]."</td>
            <td>".$misc->numberFormat_fun($totinvnow)."</td>
        </tr>";
     $i++;
    }
    $htmlTable.="<tr>
              <td>Net Total</td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td><b>".$tot_amount."</b></td>
        </tr>";
    $htmlTable.="</table>";

// list($a,$b)=explode('.',basename(__FILE__));
header("Content-Type: application/xls");
header("Content-Disposition: attachment; filename=".$p_abrv."_pending_invoices.xls");  
echo $htmlTable;
$file_name = "/home/customer/www/lsuk.org/public_html/lsuk_system/file_folder/pinv_list/".$p_abrv."_pending_invoices.xls";
$content = ob_get_clean();
file_put_contents($file_name, $content);
?>