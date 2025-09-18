<?php include '../../db.php';
include_once '../../class.php'; 
$assignDate = @$_GET['assignDate'];
$interp = @$_GET['interp'];
$p_org = @$_GET['p_org'];
$org = @$_GET['org'];
$job = @$_GET['job'];
$inov = @$_GET['inov'];
$type = @$_GET['type'];
$po = @$_GET['po'];
$string=$_GET['str'];
$invoic_date = @$_GET['invoic_date'];
$multi = @$_GET['multi'];
$rDate = @$_GET['rD'];

$org_arr = array();
$all_awp=array();
$comp_cz=array();

if(isset($_GET['p_org'])){
    $p_org = $_GET['p_org'];
    $p_org_q = $acttObj->read_specific("GROUP_CONCAT(child_comp) as ch_ids", "subsidiaries", " parent_comp=$p_org")['ch_ids']?:'0';
    $p_org_ad = ($p_org_q!=0?" and comp_reg.id IN ($p_org_q) ":"");
}else{
    $p_org_ad = $p_org;
}

$semi = "\"'\"";
if(isset($org) && $org!=""){
    $org_arr = explode(",",$org);
    if (in_array(380,$org_arr)) {
        $data1 = $acttObj->read_specific(
            "DISTINCT GROUP_CONCAT(parent_companies.sup_child_comp) as data1",
            "parent_companies",
            "parent_companies.sup_parent_comp IN (380)"
        );
        $all_awp = $acttObj->query_extra(
            "DISTINCT GROUP_CONCAT($semi,(SELECT comp_reg.abrv from comp_reg WHERE id=child_companies.child_comp),$semi) as all_cz",
            "child_companies",
            "child_companies.parent_comp IN (" . $data1["data1"] . ")",
            "set SESSION group_concat_max_len=10000"
        );
    }
    $comp_cz = $acttObj->query_extra(
        "DISTINCT GROUP_CONCAT($semi,comp_reg.abrv,$semi) as all_cz",
        "comp_reg",
        "comp_reg.id IN ($org)",
        "set SESSION group_concat_max_len=10000"
    );
    if($all_awp['all_cz']!=''){
        $all_cz['all_cz'] = $comp_cz['all_cz'].','.$all_awp['all_cz'];
    }else{
        $all_cz['all_cz'] = $comp_cz['all_cz'];
    }
}


$append_type=$type?" AND type like '%$type%'":"";
$append_assignDate_all=$assignDate?" and assignDate LIKE '$assignDate%' ":"";
$append_assignDate_f2f=$assignDate?" and interpreter.assignDate like '$assignDate%' ":"";
$append_assignDate_tp=$assignDate?" and telephone.assignDate like '$assignDate%' ":"";
$append_assignDate_tr=$assignDate?" and translation.asignDate like '$assignDate%' ":"";
$append_invoice_date_all=$invoic_date?" and invoic_date LIKE '$invoic_date%' ":"";
$append_invoice_date_f2f=$invoic_date?" and interpreter.invoic_date like '$invoic_date%' ":"";
$append_invoice_date_tp=$invoic_date?" and telephone.invoic_date like '$invoic_date%' ":"";
$append_invoice_date_tr=$invoic_date?" and translation.invoic_date like '$invoic_date%' ":"";
$append_lang_f2f=$job?" and ((interpreter.source='$job' OR interpreter.target='$job') OR (interpreter.source='$job' AND interpreter.target='English') OR (interpreter.source='English' AND interpreter.target='$job')) ":"";
$append_lang_tp=$job?" and ((telephone.source='$job' OR telephone.target='$job') OR (telephone.source='$job' AND telephone.target='English') OR (telephone.source='English' AND telephone.target='$job')) ":"";
$append_lang_tr=$job?" and ((translation.source='$job' OR translation.target='$job') OR (translation.source='$job' AND translation.target='English') OR (translation.source='English' AND translation.target='$job')) ":"";
$append_interp=$interp?" and interpreter_reg.name like '%$interp%' ":"";
$append_orgName_f2f=$org?" and interpreter.orgName IN (".$all_cz['all_cz'].") ":"";
$append_orgName_tp=$org?" and telephone.orgName IN (".$all_cz['all_cz'].") ":"";
$append_orgName_tr=$org?" and translation.orgName IN (".$all_cz['all_cz'].") ":"";
$append_rDate_f2f=$rDate?" and interpreter.rDate like '%$rDate%' ":"";
$append_rDate_tp=$rDate?" and telephone.rDate like '%$rDate%' ":"";
$append_rDate_tr=$rDate?" and translation.rDate like '%$rDate%' ":"";
$append_invoiceNo_f2f=$inov?" and interpreter.invoiceNo like '%$inov%' ":"";
$append_invoiceNo_tp=$inov?" and telephone.invoiceNo like '%$inov%' ":"";
$append_invoiceNo_tr=$inov?" and translation.invoiceNo like '%$inov%' ":"";
$append_multi_int=isset($multi) && $multi=="on"?" and interpreter.multInv_flag=1 ":" and interpreter.multInv_flag=0 ";
$append_multi_tp=isset($multi) && $multi=="on"?" and telephone.multInv_flag=1 ":" and telephone.multInv_flag=0 ";
$append_multi_tr=isset($multi) && $multi=="on"?" and translation.multInv_flag=1 ":" and translation.multInv_flag=0 ";
$append_multi_all=isset($multi) && $multi=="on"?" and multInv_flag=1 ":" and multInv_flag=0 ";

if(!empty($po) && $po=='rs'){
    $po_string_int="and comp_reg.po_req=1 and interpreter.porder!=''";
    $po_string_tp="and comp_reg.po_req=1 and telephone.porder!=''";
    $po_string_tr="and comp_reg.po_req=1 and translation.porder!=''";
}else if(!empty($po) && $po=='rm'){
    $po_string_int="and comp_reg.po_req=1 and (interpreter.porder='' OR interpreter.porder='Nil')";
    $po_string_tp="and comp_reg.po_req=1 and (telephone.porder='' OR telephone.porder='Nil')";
    $po_string_tr="and comp_reg.po_req=1 and (translation.porder='' OR translation.porder='Nil')";
}else if(!empty($po) && $po=='nr'){
    $po_string_int="and comp_reg.po_req=0";
    $po_string_tp="and comp_reg.po_req=0";
    $po_string_tr="and comp_reg.po_req=0";
}else{
    // if(isset($string) && !empty($string)){
    //     $po_string_int=" ";
    //     $po_string_tp=" ";
    //     $po_string_tr=" ";
    // }else{
    //     $po_string_int="and comp_reg.po_req=1 and interpreter.porder!=''";
    //     $po_string_tp="and comp_reg.po_req=1 and telephone.porder!=''";
    //     $po_string_tr="and comp_reg.po_req=1 and translation.porder!=''";
    // }
    $po_string_int=" ";
    $po_string_tp=" ";
    $po_string_tr=" ";
}
$arr = explode(',', $org);
$_words = implode("' OR orgName like '", $arr);
$arr_intrp = explode(',', $interp);
$_words_intrp = implode("' OR name like '", $arr_intrp);
$table = '';
$counter = 1;
if(isset($string) && !empty($string)){
    $query =
        "SELECT * from (SELECT interpreter.porder,comp_reg.po_req,'Interpreter' as type,interpreter.id,interpreter.intrpName,interpreter.orgName,interpreter.inchEmail,interpreter_reg.name,interpreter.source,interpreter.target,interpreter.invoic_date,interpreter.assignDate,interpreter.assignTime,interpreter.orgContact,interpreter.submited, interpreter.aloct_by,interpreter.aloct_date,interpreter.dated,interpreter.hrsubmited,interpreter.comp_hrsubmited,interpreter.interp_hr_date, interpreter.comp_hr_date,interpreter.hoursWorkd,interpreter.C_hoursWorkd,interpreter.total_charges_comp,interpreter.cur_vat,interpreter.C_otherexpns, interpreter.credit_note,interpreter.C_admnchargs,interpreter.rAmount,interpreter.rDate,interpreter.sentemail,interpreter.printed,interpreter.printedby,interpreter.deleted_flag,interpreter.order_cancel_flag,interpreter.nameRef,interpreter.orgRef,interpreter.invoiceNo,interpreter.commit,interpreter.new_comp_id,comp_reg.email,comp_reg.abrv as comp_abrv FROM interpreter,interpreter_reg,comp_reg 
        WHERE 
        interpreter.intrpName=interpreter_reg.id AND interpreter.orgName=comp_reg.abrv $append_multi_int AND interpreter.deleted_flag = 0 and interpreter.order_cancel_flag=0 and interpreter.commit=1 $po_string_int and (round(interpreter.rAmount,2) >= round((interpreter.total_charges_comp+(interpreter.total_charges_comp*interpreter.cur_vat)),2) and interpreter.rAmount>0) and (interpreter.orgRef like '%$string%' OR interpreter.porder like '%$string%' OR interpreter.nameRef like '%$string%' OR interpreter.invoiceNo like '%$string%' OR interpreter.id like '$string%' OR interpreter.reference_no like '$string%')
        UNION ALL SELECT telephone.porder,comp_reg.po_req,'Telephone' as type,telephone.id,telephone.intrpName,telephone.orgName,telephone.inchEmail,interpreter_reg.name,telephone.source,telephone.target,telephone.invoic_date,telephone.assignDate,telephone.assignTime,telephone.orgContact,telephone.submited, telephone.aloct_by,telephone.aloct_date,telephone.dated,telephone.hrsubmited,telephone.comp_hrsubmited,telephone.interp_hr_date,telephone.comp_hr_date, telephone.hoursWorkd,telephone.C_hoursWorkd,telephone.total_charges_comp,telephone.cur_vat,0 as C_otherexpns,telephone.credit_note,telephone.C_admnchargs, telephone.rAmount,telephone.rDate,telephone.sentemail,telephone.printed,telephone.printedby,telephone.deleted_flag,telephone.order_cancel_flag,telephone.nameRef,telephone.orgRef,telephone.invoiceNo,telephone.commit,telephone.new_comp_id,comp_reg.email,comp_reg.abrv as comp_abrv FROM telephone,interpreter_reg,comp_reg 
        WHERE 
        telephone.intrpName=interpreter_reg.id AND telephone.orgName=comp_reg.abrv $append_multi_tp AND telephone.deleted_flag = 0 and telephone.order_cancel_flag=0 and telephone.commit=1 $po_string_tp and (round(telephone.rAmount,2) >= round((telephone.total_charges_comp+(telephone.total_charges_comp*telephone.cur_vat)),2) and telephone.rAmount>0) and (telephone.orgRef like '%$string%' OR telephone.porder like '%$string%' OR telephone.nameRef like '%$string%' OR telephone.invoiceNo like '%$string%' OR telephone.id like '$string%' OR telephone.reference_no like '$string%')
        UNION ALL SELECT translation.porder,comp_reg.po_req,'Translation' as type,translation.id,translation.intrpName,translation.orgName,translation.inchEmail,interpreter_reg.name,translation.source,translation.target,translation.invoic_date,translation.asignDate as assignDate,'00:00:00' as assignTime,translation.orgContact,translation.submited, translation.aloct_by,translation.aloct_date,translation.dated,translation.hrsubmited,translation.comp_hrsubmited,translation.interp_hr_date,translation.comp_hr_date, translation.numberUnit as hoursWorkd,translation.C_numberUnit as C_hoursWorkd,translation.total_charges_comp,translation.cur_vat,0 as C_otherexpns,translation.credit_note,translation.C_admnchargs, translation.rAmount,translation.rDate,translation.sentemail,translation.printed,translation.printedby,translation.deleted_flag,translation.order_cancel_flag,translation.nameRef,translation.orgRef,translation.invoiceNo,translation.commit,translation.new_comp_id,comp_reg.email,comp_reg.abrv as comp_abrv FROM translation,interpreter_reg,comp_reg 
        WHERE 
        translation.intrpName=interpreter_reg.id AND translation.orgName=comp_reg.abrv $append_multi_tr AND translation.deleted_flag = 0 and translation.order_cancel_flag=0 and translation.commit=1 $po_string_tr and (round(translation.rAmount,2) >= round((translation.total_charges_comp+(translation.total_charges_comp*translation.cur_vat)),2) and translation.rAmount>0) and (translation.orgRef like '%$string%' OR translation.porder like '%$string%' OR translation.nameRef like '%$string%' OR translation.invoiceNo like '%$string%')) as grp ORDER BY CONCAT(assignDate,' ',assignTime)"; 
}else{
    if(isset($type) && !empty($type)){
        if ($type=='Interpreter') {
            $query =
            "SELECT * from (SELECT interpreter.porder,comp_reg.po_req,'Interpreter' as type,interpreter.id,interpreter.intrpName,interpreter.inchEmail,interpreter.orgName,interpreter_reg.name,interpreter.source,interpreter.target,interpreter.invoic_date,interpreter.assignDate,interpreter.assignTime,interpreter.orgContact,interpreter.submited, interpreter.aloct_by,interpreter.aloct_date,interpreter.dated,interpreter.hrsubmited,interpreter.comp_hrsubmited,interpreter.interp_hr_date, interpreter.comp_hr_date,interpreter.hoursWorkd,interpreter.C_hoursWorkd,interpreter.total_charges_comp,interpreter.cur_vat,interpreter.C_otherexpns, interpreter.credit_note,interpreter.C_admnchargs,interpreter.rAmount,interpreter.rDate,interpreter.sentemail,interpreter.printed,interpreter.printedby,interpreter.deleted_flag,interpreter.order_cancel_flag,interpreter.nameRef,interpreter.orgRef,interpreter.invoiceNo,interpreter.commit,interpreter.new_comp_id,comp_reg.email,comp_reg.abrv as comp_abrv FROM interpreter,interpreter_reg,comp_reg 
            WHERE 
            interpreter.intrpName=interpreter_reg.id AND interpreter.orgName=comp_reg.abrv $append_multi_int AND interpreter.deleted_flag = 0 and interpreter.order_cancel_flag=0 and interpreter.commit=1 $po_string_int and (round(interpreter.rAmount,2) >= round((interpreter.total_charges_comp+(interpreter.total_charges_comp*interpreter.cur_vat)),2) and interpreter.rAmount>0) ".$append_invoice_date_f2f.$append_assignDate_f2f.$append_lang_f2f.$append_interp.$p_org_ad.$append_orgName_f2f.$append_rDate_f2f.$append_invoiceNo_f2f.") as grp WHERE type like '%$type%' ORDER BY CONCAT(assignDate,' ',assignTime)";
        }else if ($type=='Telephone') {
            $query =
            "SELECT * from (SELECT telephone.porder,comp_reg.po_req,'Telephone' as type,telephone.id,telephone.intrpName,telephone.orgName,telephone.inchEmail,interpreter_reg.name,telephone.source,telephone.target,telephone.invoic_date,telephone.assignDate,telephone.assignTime,telephone.orgContact,telephone.submited, telephone.aloct_by,telephone.aloct_date,telephone.dated,telephone.hrsubmited,telephone.comp_hrsubmited,telephone.interp_hr_date,telephone.comp_hr_date, telephone.hoursWorkd,telephone.C_hoursWorkd,telephone.total_charges_comp,telephone.cur_vat,0 as C_otherexpns,telephone.credit_note,telephone.C_admnchargs, telephone.rAmount,telephone.rDate,telephone.sentemail,telephone.printed,telephone.printedby,telephone.deleted_flag,telephone.order_cancel_flag,telephone.nameRef,telephone.orgRef,telephone.invoiceNo,telephone.commit,telephone.new_comp_id,comp_reg.email,comp_reg.abrv as comp_abrv FROM telephone,interpreter_reg,comp_reg 
            WHERE 
            telephone.intrpName=interpreter_reg.id AND telephone.orgName=comp_reg.abrv $append_multi_tp AND telephone.deleted_flag = 0 and telephone.order_cancel_flag=0 and telephone.commit=1 $po_string_tp and (round(telephone.rAmount,2) >= round((telephone.total_charges_comp+(telephone.total_charges_comp*telephone.cur_vat)),2) and telephone.rAmount>0) ".$append_invoice_date_tp.$append_assignDate_tp.$append_lang_tp.$append_interp.$p_org_ad.$append_orgName_tp.$append_rDate_tp.$append_invoiceNo_tp.") as grp WHERE type like '%$type%' ORDER BY CONCAT(assignDate,' ',assignTime)";
        }else{
            $query =
            "SELECT * from (SELECT translation.porder,comp_reg.po_req,'Translation' as type,translation.id,translation.intrpName,translation.orgName,translation.inchEmail,interpreter_reg.name,translation.source,translation.target,translation.invoic_date,translation.asignDate as assignDate,'00:00:00' as assignTime,translation.orgContact,translation.submited, translation.aloct_by,translation.aloct_date,translation.dated,translation.hrsubmited,translation.comp_hrsubmited,translation.interp_hr_date,translation.comp_hr_date, translation.numberUnit as hoursWorkd,translation.C_numberUnit as C_hoursWorkd,translation.total_charges_comp,translation.cur_vat,0 as C_otherexpns,translation.credit_note,translation.C_admnchargs, translation.rAmount,translation.rDate,translation.sentemail,translation.printed,translation.printedby,translation.deleted_flag,translation.order_cancel_flag,translation.nameRef,translation.orgRef,translation.invoiceNo,translation.commit,translation.new_comp_id,comp_reg.email,comp_reg.abrv as comp_abrv FROM translation,interpreter_reg,comp_reg 
            WHERE 
            translation.intrpName=interpreter_reg.id AND translation.orgName=comp_reg.abrv $append_multi_tr AND translation.deleted_flag = 0 and translation.order_cancel_flag=0 and translation.commit=1 $po_string_tr and (round(translation.rAmount,2) >= round((translation.total_charges_comp+(translation.total_charges_comp*translation.cur_vat)),2) and translation.rAmount>0) ".$append_invoice_date_tr.$append_assignDate_tr.$append_lang_tr.$append_interp.$p_org_ad.$append_orgName_tr.$append_rDate_tr.$append_invoiceNo_tr.") as grp WHERE type like '%$type%' ORDER BY CONCAT(assignDate,' ',assignTime)";
        }
    }else{
        $query =
        "SELECT * from (SELECT interpreter.porder,comp_reg.po_req,'Interpreter' as type,interpreter.id,interpreter.intrpName,interpreter.orgName,interpreter.inchEmail,interpreter_reg.name,interpreter.source,interpreter.target,interpreter.invoic_date,interpreter.assignDate,interpreter.assignTime,interpreter.orgContact,interpreter.submited, interpreter.aloct_by,interpreter.aloct_date,interpreter.dated,interpreter.hrsubmited,interpreter.comp_hrsubmited,interpreter.interp_hr_date, interpreter.comp_hr_date,interpreter.hoursWorkd,interpreter.C_hoursWorkd,interpreter.total_charges_comp,interpreter.cur_vat,interpreter.C_otherexpns, interpreter.credit_note,interpreter.C_admnchargs,interpreter.rAmount,interpreter.rDate,interpreter.sentemail,interpreter.printed,interpreter.printedby,interpreter.deleted_flag,interpreter.order_cancel_flag,interpreter.nameRef,interpreter.orgRef,interpreter.invoiceNo,interpreter.commit,interpreter.new_comp_id,comp_reg.email,comp_reg.abrv as comp_abrv FROM interpreter,interpreter_reg,comp_reg 
        WHERE 
        interpreter.intrpName=interpreter_reg.id AND interpreter.orgName=comp_reg.abrv $append_multi_int AND interpreter.deleted_flag = 0 and interpreter.order_cancel_flag=0 and interpreter.commit=1 $po_string_int and (round(interpreter.rAmount,2) >= round((interpreter.total_charges_comp+(interpreter.total_charges_comp*interpreter.cur_vat)),2) and interpreter.rAmount>0) ".$append_invoice_date_f2f.$append_assignDate_f2f.$append_lang_f2f.$append_interp.$p_org_ad.$append_orgName_f2f.$append_rDate_f2f.$append_invoiceNo_f2f."
        UNION ALL SELECT telephone.porder,comp_reg.po_req,'Telephone' as type,telephone.id,telephone.intrpName,telephone.orgName,telephone.inchEmail,interpreter_reg.name,telephone.source,telephone.target,telephone.invoic_date,telephone.assignDate,telephone.assignTime,telephone.orgContact,telephone.submited, telephone.aloct_by,telephone.aloct_date,telephone.dated,telephone.hrsubmited,telephone.comp_hrsubmited,telephone.interp_hr_date,telephone.comp_hr_date, telephone.hoursWorkd,telephone.C_hoursWorkd,telephone.total_charges_comp,telephone.cur_vat,0 as C_otherexpns,telephone.credit_note,telephone.C_admnchargs, telephone.rAmount,telephone.rDate,telephone.sentemail,telephone.printed,telephone.printedby,telephone.deleted_flag,telephone.order_cancel_flag,telephone.nameRef,telephone.orgRef,telephone.invoiceNo,telephone.commit,telephone.new_comp_id,comp_reg.email,comp_reg.abrv as comp_abrv FROM telephone,interpreter_reg,comp_reg 
        WHERE 
        telephone.intrpName=interpreter_reg.id AND telephone.orgName=comp_reg.abrv $append_multi_tp AND telephone.deleted_flag = 0 and telephone.order_cancel_flag=0 and telephone.commit=1 $po_string_tp and (round(telephone.rAmount,2) >= round((telephone.total_charges_comp+(telephone.total_charges_comp*telephone.cur_vat)),2) and telephone.rAmount>0) ".$append_invoice_date_tp.$append_assignDate_tp.$append_lang_tp.$append_interp.$p_org_ad.$append_orgName_tp.$append_rDate_tp.$append_invoiceNo_tp."
        UNION ALL SELECT translation.porder,comp_reg.po_req,'Translation' as type,translation.id,translation.intrpName,translation.orgName,translation.inchEmail,interpreter_reg.name,translation.source,translation.target,translation.invoic_date,translation.asignDate as assignDate,'00:00:00' as assignTime,translation.orgContact,translation.submited, translation.aloct_by,translation.aloct_date,translation.dated,translation.hrsubmited,translation.comp_hrsubmited,translation.interp_hr_date,translation.comp_hr_date, translation.numberUnit as hoursWorkd,translation.C_numberUnit as C_hoursWorkd,translation.total_charges_comp,translation.cur_vat,0 as C_otherexpns,translation.credit_note,translation.C_admnchargs, translation.rAmount,translation.rDate,translation.sentemail,translation.printed,translation.printedby,translation.deleted_flag,translation.order_cancel_flag,translation.nameRef,translation.orgRef,translation.invoiceNo,translation.commit,translation.new_comp_id,comp_reg.email,comp_reg.abrv as comp_abrv FROM translation,interpreter_reg,comp_reg 
        WHERE 
        translation.intrpName=interpreter_reg.id AND translation.orgName=comp_reg.abrv $append_multi_tr AND translation.deleted_flag = 0 and translation.order_cancel_flag=0 and translation.commit=1 $po_string_tr and (round(translation.rAmount,2) >= round((translation.total_charges_comp+(translation.total_charges_comp*translation.cur_vat)),2) and translation.rAmount>0) ".$append_invoice_date_tr.$append_assignDate_tr.$append_lang_tr.$append_interp.$p_org_ad.$append_orgName_tr.$append_rDate_tr.$append_invoiceNo_tr.") as grp 
        WHERE 1 
        ".$append_type." ORDER BY CONCAT(assignDate,' ',assignTime)";
    }  
}
$result = mysqli_query($con, $query);
// echo $query."<br><br><br>";
// echo "rows are: ".mysqli_num_rows($result);
if (!empty($po) && $po=='rm') {$po_status=' with Required & Missing Purchase Orders';}
else if (!empty($po) && $po=='rs') {$po_status=' with Required & Updated Purchase Orders';}else if (!empty($po) && $po=='nr') {$po_status=' with Purchase Orders Not Required';}else{$po_status='';}
if(isset($string) && !empty($string)){$po_status='';}
if (!empty($type)) {
    $put_var= $type.' paid jobs list'.$po_status; 
}else{
    $put_var= 'All paid jobs list'.$po_status;
}
$htmlTable='';
$htmlTable.='<style>
table {border-collapse: collapse; width:670px;}
th {border: 1px solid #999; padding: 0.5rem;text-align: left;background-color:#039; color:#FFF; font-weight:bold;}
td {border: 1px solid #999; padding: 0.5rem;text-align: left;}
</style>
<div>';
$put_org=!empty($org)?$org:'All Companies';
$htmlTable .='<h2 style="text-align:center;background:grey">'.$put_var.'</h2>
<p align="right"> Report Date: '.$misc->sys_date(). '</p>
<p>Organization Name : ' .$put_org. '</p>
</div>

<table>
<thead>
<tr>
	<th style="background-color:#039;color:#FFF;">S.No</th>
	<th style="background-color:#039;color:#FFF;">Company</th>
	<th style="background-color:#039;color:#FFF;">Assignment Date</th>
	<th style="background-color:#039;color:#FFF;">Invoice No</th>
	<th style="background-color:#039;color:#FFF;">Client Reference</th>
    <th style="background-color:#039;color:#FFF;">Linguist</th>
    <th style="background-color:#039;color:#FFF;">Purch.Order#</th>
    <th style="background-color:#039;color:#FFF;">Invoice Amount</th>
    <th style="background-color:#039;color:#FFF;">Received Amount</th>
    <th style="background-color:#039;color:#FFF;">Paid Date</th>';
while($row = mysqli_fetch_assoc($result)){
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
    $append_invoiceNo='';
    if(!empty($row['credit_note']) && $row['type']=="Interpreter"){
      $append_invoiceNo="-0".$acttObj->read_specific("count(*) as counter","credit_notes","order_id=".$row['id']." and order_type='f2f'")['counter'];
    }elseif(!empty($row['credit_note']) && $row['type']=="Telephone"){
        $append_invoiceNo="-0".$acttObj->read_specific("count(*) as counter","credit_notes","order_id=".$row['id']." and order_type='tp'")['counter'];
    }elseif(!empty($row['credit_note']) && $row['type']=="Translation"){
        $append_invoiceNo="-0".$acttObj->read_specific("count(*) as counter","credit_notes","order_id=".$row['id']." and order_type='tr'")['counter'];
    }
    $gotcreditnote = false;
    if (isset($row['credit_note']) && $row['credit_note'] != "") {
        // $totinvnow = 0;
        $gotcreditnote = true;
    }
    if($row['po_req']==1 && $row['porder']!=''){ $table_po= $row['porder'];}else if($row['po_req']==1 && $row['porder']==''){$table_po= '<b style="color:red;">Missing!</b>';}else{$table_po= '<b>Not required!</b>';}
    $reveived_amount=$row['rAmount']!=0?$misc->numberFormat_fun($row['rAmount']):0;
    $prv_org='';
    if($row['orgName']=="LSUK_Private Client" && $row['new_comp_id']!=0){
        $prv_org = $acttObj->read_specific("name", "private_company", " id={$row['new_comp_id']}")['name'];
        $prv_org = "LSUK_".$prv_org;
    }
$htmlTable .='<tr>';
$htmlTable .='<td>'.$counter++.'</td>';
$htmlTable .='<td>'.($prv_org!=''?$prv_org:$row['orgName']).'</td>';
$htmlTable .='<td>'.$row["assignDate"].'</td>';
$htmlTable .='<td>'.$row["invoiceNo"].$append_invoiceNo.'</td>';
$htmlTable .='<td>'.$row["orgRef"].'</td>';
$htmlTable .='<td>'.$row["name"].'</td>';
$htmlTable .='<td>'.$table_po.'</td>';
$htmlTable .='<td>'.$misc->numberFormat_fun($totinvnow).'</td>';
$htmlTable .='<td>'.$reveived_amount.'</td>';
$htmlTable .='<td>'.$misc->dated($row['rDate']).'</td>';
$htmlTable .='</tr>';
$i++;}
$htmlTable.='</table>';

list($a,$b)=explode('.',basename(__FILE__));
header("Content-Type: application/xls");
header("Content-Disposition: attachment; filename=".$a.".xls");  
echo $htmlTable;
?>