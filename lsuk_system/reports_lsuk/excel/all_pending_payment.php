<?php include '../../db.php';
include_once('../../class.php');
$multi = @$_GET['multi'];
$over_dues = @$_GET['over_dues'];
$over_due_days = @$_GET['over_due_days'];
$assignDate = @$_GET['assignDate'];
$invoic_date = @$_GET['invoic_date'];
$interp = @$_GET['interp'];
$p_org = @$_GET['p_org'];
$org = @$_GET['org'];
$job = @$_GET['job'];
$inov = @$_GET['inov'];
$type = @$_GET['type'];
$po = @$_GET['po'];
$string = $_GET['str'];

$org_arr = array();
$all_awp = array();
$comp_cz = array();
$comp_full_cz = array();
$tot_pending_grand=$tot_paid_grand=$tot_rem_grand=0;

if (isset($_GET['p_org'])) {
    $p_org = $_GET['p_org'];
    $p_org_q = $acttObj->read_specific("GROUP_CONCAT(child_comp) as ch_ids", "subsidiaries", " parent_comp=$p_org")['ch_ids'] ?: '0';
    $p_org_ad = ($p_org_q != 0 ? " and comp_reg.id IN ($p_org_q) " : "");
} else {
    $p_org_ad = $p_org;
}

$semi = "\"'\"";
if (isset($org) && $org != "") {
    $org_arr = explode(",", $org);
    if (in_array(380, $org_arr)) {
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
    $comp_full_cz = $acttObj->query_extra(
        "DISTINCT GROUP_CONCAT($semi,comp_reg.name,$semi) as all_cz",
        "comp_reg",
        "comp_reg.id IN ($org)",
        "set SESSION group_concat_max_len=10000"
    );
    if ($all_awp['all_cz'] != '') {
        $all_cz['all_cz'] = $comp_cz['all_cz'] . ',' . $all_awp['all_cz'];
    } else {
        $all_cz['all_cz'] = $comp_cz['all_cz'];
    }
}

if (isset($multi) && $multi == "on") {
    $append_multi_int = "and interpreter.multInv_flag=1";
    $append_multi_tp = "and telephone.multInv_flag=1";
    $append_multi_tr = "and translation.multInv_flag=1";
    $append_multi_all = "and multInv_flag=1";
} else {
    $append_multi_int = "and interpreter.multInv_flag=0";
    $append_multi_tp = "and telephone.multInv_flag=0";
    $append_multi_tr = "and translation.multInv_flag=0";
    $append_multi_all = "and multInv_flag=0";
}
if (isset($over_dues) && $over_dues == "on") {
    $current_date = date('Y-m-d');
    $append_over_dues_int = $over_due_days ? " AND interpreter.dueDate < '" . $current_date . "' AND ABS(DATEDIFF(interpreter.dueDate,'" . $current_date . "')) <= " . $over_due_days : " and interpreter.dueDate < '" . $current_date . "'";
    $append_over_dues_tp = $over_due_days ? " AND telephone.dueDate < '" . $current_date . "' AND ABS(DATEDIFF(telephone.dueDate,'" . $current_date . "')) <= " . $over_due_days : " and telephone.dueDate < '" . $current_date . "'";
    $append_over_dues_tr = $over_due_days ? " AND translation.dueDate < '" . $current_date . "' AND ABS(DATEDIFF(translation.dueDate,'" . $current_date . "')) <= " . $over_due_days : " and translation.dueDate < '" . $current_date . "'";
    $append_over_dues_all = $over_due_days ? " AND dueDate < '" . $current_date . "' AND ABS(DATEDIFF(dueDate,'" . $current_date . "')) <= " . $over_due_days : " and dueDate < '" . $current_date . "'";
} else {
    $append_over_dues_int = "";
    $append_over_dues_tp = "";
    $append_over_dues_tr = "";
    $append_over_dues_all = "";
}


if (!empty($po) && $po == 'rs') {
    $po_string_int = "and comp_reg.po_req=1 and interpreter.porder!=''";
    $po_string_tp = "and comp_reg.po_req=1 and telephone.porder!=''";
    $po_string_tr = "and comp_reg.po_req=1 and translation.porder!=''";
} else if (!empty($po) && $po == 'rm') {
    $po_string_int = "and comp_reg.po_req=1 and (interpreter.porder='' OR interpreter.porder='Nil')";
    $po_string_tp = "and comp_reg.po_req=1 and (telephone.porder='' OR telephone.porder='Nil')";
    $po_string_tr = "and comp_reg.po_req=1 and (translation.porder='' OR translation.porder='Nil')";
} else if (!empty($po) && $po == 'nr') {
    $po_string_int = "and comp_reg.po_req=0";
    $po_string_tp = "and comp_reg.po_req=0";
    $po_string_tr = "and comp_reg.po_req=0";
} else {
    // if(isset($string) && !empty($string)){
    //     $po_string_int=" ";
    //     $po_string_tp=" ";
    //     $po_string_tr=" ";
    // }else{
    //     $po_string_int="and comp_reg.po_req=1 and interpreter.porder!=''";
    //     $po_string_tp="and comp_reg.po_req=1 and telephone.porder!=''";
    //     $po_string_tr="and comp_reg.po_req=1 and translation.porder!=''";
    // }
    $po_string_int = " ";
    $po_string_tp = " ";
    $po_string_tr = " ";
}
function formatTwoDecimal($value) {
    return is_float($value) ? number_format($value, 2, '.', '') : $value;
}
$arr = explode(',', $org);
$_words = implode("' OR orgName like '", $arr);
$arr_intrp = explode(',', $interp);
$_words_intrp = implode("' OR name like '", $arr_intrp);
$table = '';
$counter = 1;
if (!empty($type) && $type == 'Interpreter') {
    //                 $query =
    // "SELECT * from (SELECT interpreter.porder,comp_reg.po_req,'Interpreter' as type,interpreter.id,interpreter.intrpName,interpreter.orgName,interpreter_reg.name,interpreter.source,interpreter.invoic_date,interpreter.assignDate,interpreter.assignTime,interpreter.orgContact,interpreter.submited, interpreter.aloct_by,interpreter.aloct_date,interpreter.dated,interpreter.hrsubmited,interpreter.comp_hrsubmited,interpreter.interp_hr_date, interpreter.comp_hr_date,interpreter.hoursWorkd,interpreter.C_hoursWorkd,interpreter.total_charges_comp,interpreter.cur_vat,interpreter.C_otherexpns, interpreter.credit_note,interpreter.C_admnchargs,interpreter.rAmount,interpreter.rDate,interpreter.sentemail,interpreter.printed,interpreter.printedby,interpreter.deleted_flag,interpreter.order_cancel_flag,interpreter.nameRef,interpreter.orgRef,interpreter.invoiceNo,interpreter.commit,comp_reg.email,comp_reg.abrv as comp_abrv FROM interpreter,interpreter_reg,comp_reg WHERE interpreter.intrpName=interpreter_reg.id AND interpreter.orgName=comp_reg.abrv AND interpreter.multInv_flag=0 AND interpreter.deleted_flag = 0 AND interpreter.disposed_of = 0 and interpreter.order_cancel_flag=0 and interpreter.commit=1 $po_string_int and (round(interpreter.rAmount,2) < round((interpreter.total_charges_comp+(interpreter.total_charges_comp*interpreter.cur_vat)),2) or interpreter.total_charges_comp =0) and interpreter.invoic_date like '$invoic_date%' and interpreter.assignDate like '$assignDate%' and interpreter.source like '%$job%' and interpreter_reg.name like '%$interp%' and interpreter.orgName = '$org' and interpreter.invoiceNo like '%$inov%') as grp WHERE type like '%$type%' ORDER BY CONCAT(assignDate,' ',assignTime)";

    $query =
        'SELECT * from (SELECT interpreter.porder,comp_reg.po_req,"Interpreter" as type,interpreter.id,interpreter.intrpName,interpreter.orgName,interpreter_reg.name,interpreter.source,interpreter.invoic_date,interpreter.assignDate,interpreter.assignTime,interpreter.orgContact,interpreter.submited, interpreter.aloct_by,interpreter.aloct_date,interpreter.dated,interpreter.hrsubmited,interpreter.comp_hrsubmited,interpreter.interp_hr_date, interpreter.comp_hr_date,interpreter.hoursWorkd,interpreter.C_hoursWorkd,interpreter.total_charges_comp,interpreter.cur_vat,interpreter.C_otherexpns, interpreter.credit_note,interpreter.C_admnchargs,interpreter.rAmount,interpreter.rDate,interpreter.sentemail,interpreter.printed,interpreter.printedby,interpreter.deleted_flag,interpreter.order_cancel_flag,interpreter.nameRef,interpreter.orgRef,interpreter.invoiceNo,interpreter.commit,interpreter.new_comp_id,comp_reg.email,comp_reg.abrv as comp_abrv FROM interpreter,interpreter_reg,comp_reg WHERE interpreter.intrpName=interpreter_reg.id AND interpreter.orgName=comp_reg.abrv AND interpreter.deleted_flag = 0 AND interpreter.disposed_of = 0 and interpreter.order_cancel_flag=0 and interpreter.commit=1 ' . $po_string_int . ' ' . $append_multi_int . ' ' . $append_over_dues_int . ' and (round(interpreter.rAmount,2) < round((interpreter.total_charges_comp+(interpreter.total_charges_comp*interpreter.cur_vat)),2) or interpreter.total_charges_comp =0) ' . (!empty($invoic_date) && $invoic_date != "" ? '  and interpreter.invoic_date like "' . $invoic_date . '%" ' : '') . (!empty($assignDate) && $assignDate != "" ? '  and interpreter.assignDate like "' . $assignDate . '%" ' : '') . (!empty($job) && $job != "" ? '  and  ((interpreter.source="' . $job . '" OR interpreter.target="' . $job . '") OR (interpreter.source="' . $job . '" AND interpreter.target="English") OR (interpreter.source="English" AND interpreter.target="' . $job . '") ) ' : '') . (!empty($interp) && $interp != "" ? '  and interpreter_reg.name like "%' . $interp . '%" ' : '') . $p_org_ad . (!empty($org) && $org != "" ? '  and interpreter.orgName IN (' . $all_cz["all_cz"] . ') ' : '') . (!empty($inov) && $inov != "" ? '  and interpreter.invoiceNo like "%' . $inov . '%" ' : '') . ' ) as grp WHERE type like "%' . $type . '%" ORDER BY CONCAT(assignDate," ",assignTime)';
} else if (!empty($type) && $type == 'Telephone') {
    //                 $query =
    // "SELECT * from (SELECT telephone.porder,comp_reg.po_req,'Telephone' as type,telephone.id,telephone.intrpName,telephone.orgName,interpreter_reg.name,telephone.source,telephone.invoic_date,telephone.assignDate,telephone.assignTime,telephone.orgContact,telephone.submited, telephone.aloct_by,telephone.aloct_date,telephone.dated,telephone.hrsubmited,telephone.comp_hrsubmited,telephone.interp_hr_date,telephone.comp_hr_date, telephone.hoursWorkd,telephone.C_hoursWorkd,telephone.total_charges_comp,telephone.cur_vat,0 as C_otherexpns,telephone.credit_note,telephone.C_admnchargs, telephone.rAmount,telephone.rDate,telephone.sentemail,telephone.printed,telephone.printedby,telephone.deleted_flag,telephone.order_cancel_flag,telephone.nameRef,telephone.orgRef,telephone.invoiceNo,telephone.commit,comp_reg.email,comp_reg.abrv as comp_abrv FROM telephone,interpreter_reg,comp_reg WHERE telephone.intrpName=interpreter_reg.id AND telephone.orgName=comp_reg.abrv AND telephone.multInv_flag=0 AND telephone.deleted_flag = 0 AND telephone.disposed_of = 0 and telephone.order_cancel_flag=0 and telephone.commit=1 $po_string_tp and (round(telephone.rAmount,2) < round((telephone.total_charges_comp+(telephone.total_charges_comp*telephone.cur_vat)),2) or telephone.total_charges_comp =0) and telephone.invoic_date like '$invoic_date%' and telephone.assignDate like '$assignDate%' and telephone.source like '%$job%' and interpreter_reg.name like '%$interp%' and telephone.orgName = '$org' and telephone.invoiceNo like '%$inov%') as grp WHERE type like '%$type%' ORDER BY CONCAT(assignDate,' ',assignTime)";

    // $query =
    // 'SELECT * from (SELECT telephone.porder,comp_reg.po_req,"Telephone" as type,telephone.id,telephone.intrpName,telephone.orgName,interpreter_reg.name,telephone.source,telephone.invoic_date,telephone.assignDate,telephone.assignTime,telephone.orgContact,telephone.submited, telephone.aloct_by,telephone.aloct_date,telephone.dated,telephone.hrsubmited,telephone.comp_hrsubmited,telephone.interp_hr_date,telephone.comp_hr_date, telephone.hoursWorkd,telephone.C_hoursWorkd,telephone.total_charges_comp,telephone.cur_vat,0 as C_otherexpns,telephone.credit_note,telephone.C_admnchargs, telephone.rAmount,telephone.rDate,telephone.sentemail,telephone.printed,telephone.printedby,telephone.deleted_flag,telephone.order_cancel_flag,telephone.nameRef,telephone.orgRef,telephone.invoiceNo,telephone.commit,comp_reg.email,comp_reg.abrv as comp_abrv FROM telephone,interpreter_reg,comp_reg WHERE telephone.intrpName=interpreter_reg.id AND telephone.orgName=comp_reg.abrv AND telephone.multInv_flag=0 AND telephone.deleted_flag = 0 AND telephone.disposed_of = 0 and telephone.order_cancel_flag=0 and telephone.commit=1 '.$po_string_tp.' and (round(telephone.rAmount,2) < round((telephone.total_charges_comp+(telephone.total_charges_comp*telephone.cur_vat)),2) or telephone.total_charges_comp =0) '.(!empty($invoic_date) && $invoic_date!="" ? '  and telephone.invoic_date like "'.$invoic_date.'%" ': '').' '.(!empty($assignDate) && $assignDate!="" ? '  and telephone.assignDate like "'.$assignDate.'%" ': '').' '.(!empty($job) && $job!="" ? '  and telephone.source="'.$job.'" ': '').' '.(!empty($interp) && $interp!="" ? '  and interpreter_reg.name like "%'.$interp.'%" ': '').' '.(!empty($org) && $org!="" ? '  and telephone.orgName IN ('.$all_cz["all_cz"].') ': '').' '.(!empty($inov) && $inov!="" ? '  and telephone.invoiceNo like "%'.$inov.'%" ': '').' ) as grp WHERE type like "%'.$type.'%" ORDER BY CONCAT(assignDate," ",assignTime)';



    $query =
        'SELECT * from (SELECT telephone.porder,comp_reg.po_req,"Telephone" as type,telephone.id,telephone.intrpName,telephone.orgName,interpreter_reg.name,telephone.source,telephone.invoic_date,telephone.assignDate,telephone.assignTime,telephone.orgContact,telephone.submited, telephone.aloct_by,telephone.aloct_date,telephone.dated,telephone.hrsubmited,telephone.comp_hrsubmited,telephone.interp_hr_date,telephone.comp_hr_date, telephone.hoursWorkd,telephone.C_hoursWorkd,telephone.total_charges_comp,telephone.cur_vat,telephone.calCharges as C_otherexpns,telephone.credit_note,telephone.C_admnchargs, telephone.rAmount,telephone.rDate,telephone.sentemail,telephone.printed,telephone.printedby,telephone.deleted_flag,telephone.order_cancel_flag,telephone.nameRef,telephone.orgRef,telephone.invoiceNo,telephone.commit,telephone.new_comp_id,comp_reg.email,comp_reg.abrv as comp_abrv FROM telephone,interpreter_reg,comp_reg WHERE telephone.intrpName=interpreter_reg.id AND telephone.orgName=comp_reg.abrv AND telephone.deleted_flag = 0 AND telephone.disposed_of = 0 and telephone.order_cancel_flag=0 and telephone.commit=1 ' . $po_string_tp . ' ' . $append_multi_tp . ' ' . $append_over_dues_tp . ' and (round(telephone.rAmount,2) < round((telephone.total_charges_comp+(telephone.total_charges_comp*telephone.cur_vat)),2) or telephone.total_charges_comp =0) ' . (!empty($invoic_date) && $invoic_date != "" ? '  and telephone.invoic_date like "' . $invoic_date . '%" ' : '') . (!empty($assignDate) && $assignDate != "" ? '  and telephone.assignDate like "' . $assignDate . '%" ' : '') . (!empty($job) && $job != "" ? '  and ((telephone.source="' . $job . '" OR telephone.target="' . $job . '") OR (telephone.source="' . $job . '" AND telephone.target="English") OR (telephone.source="English" AND telephone.target="' . $job . '") ) ' : '') . (!empty($interp) && $interp != "" ? '  and interpreter_reg.name like "%' . $interp . '%" ' : '') . $p_org_ad . (!empty($org) && $org != "" ? '  and telephone.orgName IN (' . $all_cz["all_cz"] . ') ' : '') . (!empty($inov) && $inov != "" ? '  and telephone.invoiceNo like "%' . $inov . '%" ' : '') . ' ) as grp WHERE type like "%' . $type . '%" ORDER BY CONCAT(assignDate," ",assignTime)';
} else if (!empty($type) && $type == 'Translation') {
    //                 $query =
    // "SELECT * from (SELECT translation.porder,comp_reg.po_req,'Translation' as type,translation.id,translation.intrpName,translation.orgName,interpreter_reg.name,translation.source,translation.invoic_date,translation.asignDate as assignDate,'00:00:00' as assignTime,translation.orgContact,translation.submited, translation.aloct_by,translation.aloct_date,translation.dated,translation.hrsubmited,translation.comp_hrsubmited,translation.interp_hr_date,translation.comp_hr_date, translation.numberUnit as hoursWorkd,translation.C_numberUnit as C_hoursWorkd,translation.total_charges_comp,translation.cur_vat,0 as C_otherexpns,translation.credit_note,translation.C_admnchargs, translation.rAmount,translation.rDate,translation.sentemail,translation.printed,translation.printedby,translation.deleted_flag,translation.order_cancel_flag,translation.nameRef,translation.orgRef,translation.invoiceNo,translation.commit,comp_reg.email,comp_reg.abrv as comp_abrv FROM translation,interpreter_reg,comp_reg WHERE translation.intrpName=interpreter_reg.id AND translation.orgName=comp_reg.abrv AND translation.multInv_flag=0 AND translation.deleted_flag = 0 AND interpreter.disposed_of = 0 and translation.order_cancel_flag=0 and translation.commit=1 $po_string_tr and (round(translation.rAmount,2) < round((translation.total_charges_comp+(translation.total_charges_comp*translation.cur_vat)),2) or translation.total_charges_comp =0) and translation.invoic_date like '$invoic_date%' and translation.asignDate like '$assignDate%' and translation.source like '%$job%' and interpreter_reg.name like '%$interp%' and translation.orgName = '$org' and translation.invoiceNo like '%$inov%') as grp WHERE type like '%$type%' ORDER BY CONCAT(assignDate,' ',assignTime)";

    $query =
        'SELECT * from (SELECT translation.porder,comp_reg.po_req,"Translation" as type,translation.id,translation.intrpName,translation.orgName,interpreter_reg.name,translation.source,translation.invoic_date,translation.asignDate as assignDate,"00:00:00" as assignTime,translation.orgContact,translation.submited, translation.aloct_by,translation.aloct_date,translation.dated,translation.hrsubmited,translation.comp_hrsubmited,translation.interp_hr_date,translation.comp_hr_date, translation.numberUnit as hoursWorkd,translation.C_numberUnit as C_hoursWorkd,translation.total_charges_comp,translation.cur_vat,0 as C_otherexpns,translation.credit_note,translation.C_admnchargs, translation.rAmount,translation.rDate,translation.sentemail,translation.printed,translation.printedby,translation.deleted_flag,translation.order_cancel_flag,translation.nameRef,translation.orgRef,translation.invoiceNo,translation.commit,translation.new_comp_id,comp_reg.email,comp_reg.abrv as comp_abrv FROM translation,interpreter_reg,comp_reg WHERE translation.intrpName=interpreter_reg.id AND translation.orgName=comp_reg.abrv AND translation.deleted_flag = 0 AND translation.disposed_of = 0 and translation.order_cancel_flag=0 and translation.commit=1 ' . $po_string_tr . ' ' . $append_multi_tr . ' ' . $append_over_dues_tr . ' and (round(translation.rAmount,2) < round((translation.total_charges_comp+(translation.total_charges_comp*translation.cur_vat)),2) or translation.total_charges_comp =0) ' . (!empty($invoic_date) && $invoic_date != "" ? '  and translation.invoic_date like "' . $invoic_date . '%" ' : '') . (!empty($assignDate) && $assignDate != "" ? '  and translation.asignDate like "' . $assignDate . '%" ' : '') . (!empty($job) && $job != "" ? '  and ((translation.source="' . $job . '" OR translation.target="' . $job . '") OR (translation.source="' . $job . '" AND translation.target="English") OR (translation.source="English" AND translation.target="' . $job . '")) ' : '') . (!empty($interp) && $interp != "" ? '  and interpreter_reg.name like "%' . $interp . '%" ' : '') . $p_org_ad . (!empty($org) && $org != "" ? '  and translation.orgName IN (' . $all_cz["all_cz"] . ') ' : '') . (!empty($inov) && $inov != "" ? '  and translation.invoiceNo like "%' . $inov . '%" ' : '') . ' ) as grp WHERE type like "%' . $type . '%" ORDER BY CONCAT(assignDate," ",assignTime)';
} else {
    if (isset($string) && !empty($string)) {
         $query =
            "SELECT * from (SELECT interpreter.porder,comp_reg.po_req,'Interpreter' as type,interpreter.id,interpreter.intrpName,interpreter.orgName,interpreter_reg.name,interpreter.source,interpreter.invoic_date,interpreter.assignDate,interpreter.assignTime,interpreter.orgContact,interpreter.submited, interpreter.aloct_by,interpreter.aloct_date,interpreter.dated,interpreter.hrsubmited,interpreter.comp_hrsubmited,interpreter.interp_hr_date, interpreter.comp_hr_date,interpreter.hoursWorkd,interpreter.C_hoursWorkd,interpreter.total_charges_comp,interpreter.cur_vat,interpreter.C_otherexpns, interpreter.credit_note,interpreter.C_admnchargs,interpreter.rAmount,interpreter.rDate,interpreter.sentemail,interpreter.printed,interpreter.printedby,interpreter.deleted_flag,interpreter.order_cancel_flag,interpreter.nameRef,interpreter.orgRef,interpreter.invoiceNo,interpreter.commit,interpreter.new_comp_id,comp_reg.email,comp_reg.abrv as comp_abrv FROM interpreter,interpreter_reg,comp_reg WHERE interpreter.intrpName=interpreter_reg.id AND interpreter.orgName=comp_reg.abrv AND interpreter.multInv_flag=0 AND interpreter.deleted_flag = 0 AND interpreter.disposed_of = 0 and interpreter.order_cancel_flag=0 and interpreter.commit=1 and (round(interpreter.rAmount,2) < round((interpreter.total_charges_comp+(interpreter.total_charges_comp*interpreter.cur_vat)),2) or interpreter.total_charges_comp =0) and (interpreter.orgRef like '%$string%' OR interpreter.porder like '%$string%' OR interpreter.nameRef like '%$string%' OR interpreter.invoiceNo like '%$string%' OR interpreter.id like '$string%' OR interpreter.reference_no like '$string%')
               UNION ALL SELECT telephone.porder,comp_reg.po_req,'Telephone' as type,telephone.id,telephone.intrpName,telephone.orgName,interpreter_reg.name,telephone.source,telephone.invoic_date,telephone.assignDate,telephone.assignTime,telephone.orgContact,telephone.submited, telephone.aloct_by,telephone.aloct_date,telephone.dated,telephone.hrsubmited,telephone.comp_hrsubmited,telephone.interp_hr_date,telephone.comp_hr_date, telephone.hoursWorkd,telephone.C_hoursWorkd,telephone.total_charges_comp,telephone.cur_vat,telephone.calCharges as C_otherexpns,telephone.credit_note,telephone.C_admnchargs, telephone.rAmount,telephone.rDate,telephone.sentemail,telephone.printed,telephone.printedby,telephone.deleted_flag,telephone.order_cancel_flag,telephone.nameRef,telephone.orgRef,telephone.invoiceNo,telephone.commit,telephone.new_comp_id,comp_reg.email,comp_reg.abrv as comp_abrv FROM telephone,interpreter_reg,comp_reg WHERE telephone.intrpName=interpreter_reg.id AND telephone.orgName=comp_reg.abrv AND telephone.multInv_flag=0 AND telephone.deleted_flag = 0 AND telephone.disposed_of = 0 and telephone.order_cancel_flag=0 and telephone.commit=1 and (round(telephone.rAmount,2) < round((telephone.total_charges_comp+(telephone.total_charges_comp*telephone.cur_vat)),2) or telephone.total_charges_comp =0) and (telephone.orgRef like '%$string%' OR telephone.porder like '%$string%' OR telephone.nameRef like '%$string%' OR telephone.invoiceNo like '%$string%' OR telephone.id like '$string%' OR telephone.reference_no like '$string%')
               UNION ALL SELECT translation.porder,comp_reg.po_req,'Translation' as type,translation.id,translation.intrpName,translation.orgName,interpreter_reg.name,translation.source,translation.invoic_date,translation.asignDate as assignDate,'00:00:00' as assignTime,translation.orgContact,translation.submited, translation.aloct_by,translation.aloct_date,translation.dated,translation.hrsubmited,translation.comp_hrsubmited,translation.interp_hr_date,translation.comp_hr_date, translation.numberUnit as hoursWorkd,translation.C_numberUnit as C_hoursWorkd,translation.total_charges_comp,translation.cur_vat,0 as C_otherexpns,translation.credit_note,translation.C_admnchargs, translation.rAmount,translation.rDate,translation.sentemail,translation.printed,translation.printedby,translation.deleted_flag,translation.order_cancel_flag,translation.nameRef,translation.orgRef,translation.invoiceNo,translation.commit,translation.new_comp_id,comp_reg.email,comp_reg.abrv as comp_abrv FROM translation,interpreter_reg,comp_reg WHERE translation.intrpName=interpreter_reg.id AND translation.orgName=comp_reg.abrv AND translation.multInv_flag=0 AND translation.deleted_flag = 0 AND translation.disposed_of = 0 and translation.order_cancel_flag=0 and translation.commit=1 and (round(translation.rAmount,2) < round((translation.total_charges_comp+(translation.total_charges_comp*translation.cur_vat)),2) or translation.total_charges_comp =0) and (translation.orgRef like '%$string%' OR translation.porder like '%$string%' OR translation.nameRef like '%$string%' OR translation.invoiceNo like '%$string%')) as grp ORDER BY CONCAT(assignDate,' ',assignTime)";
    } else {
        //                     $query =
        // "SELECT * from (SELECT interpreter.porder,comp_reg.po_req,'Interpreter' as type,interpreter.id,interpreter.intrpName,interpreter.orgName,interpreter_reg.name,interpreter.source,interpreter.invoic_date,interpreter.assignDate,interpreter.assignTime,interpreter.orgContact,interpreter.submited, interpreter.aloct_by,interpreter.aloct_date,interpreter.dated,interpreter.hrsubmited,interpreter.comp_hrsubmited,interpreter.interp_hr_date, interpreter.comp_hr_date,interpreter.hoursWorkd,interpreter.C_hoursWorkd,interpreter.total_charges_comp,interpreter.cur_vat,interpreter.C_otherexpns, interpreter.credit_note,interpreter.C_admnchargs,interpreter.rAmount,interpreter.rDate,interpreter.sentemail,interpreter.printed,interpreter.printedby,interpreter.deleted_flag,interpreter.order_cancel_flag,interpreter.nameRef,interpreter.orgRef,interpreter.invoiceNo,interpreter.commit,comp_reg.email,comp_reg.abrv as comp_abrv FROM interpreter,interpreter_reg,comp_reg WHERE interpreter.intrpName=interpreter_reg.id AND interpreter.orgName=comp_reg.abrv AND interpreter.multInv_flag=0 AND interpreter.deleted_flag = 0 AND interpreter.disposed_of = 0 and interpreter.order_cancel_flag=0 and interpreter.commit=1 $po_string_int and (round(interpreter.rAmount,2) < round((interpreter.total_charges_comp+(interpreter.total_charges_comp*interpreter.cur_vat)),2) or interpreter.total_charges_comp =0) and interpreter.invoic_date like '$invoic_date%' and interpreter.assignDate like '$assignDate%' and interpreter.source like '%$job%' and interpreter_reg.name like '%$interp%' and interpreter.orgName = '$org' and interpreter.invoiceNo like '%$inov%'
        //            UNION ALL SELECT telephone.porder,comp_reg.po_req,'Telephone' as type,telephone.id,telephone.intrpName,telephone.orgName,interpreter_reg.name,telephone.source,telephone.invoic_date,telephone.assignDate,telephone.assignTime,telephone.orgContact,telephone.submited, telephone.aloct_by,telephone.aloct_date,telephone.dated,telephone.hrsubmited,telephone.comp_hrsubmited,telephone.interp_hr_date,telephone.comp_hr_date, telephone.hoursWorkd,telephone.C_hoursWorkd,telephone.total_charges_comp,telephone.cur_vat,0 as C_otherexpns,telephone.credit_note,telephone.C_admnchargs, telephone.rAmount,telephone.rDate,telephone.sentemail,telephone.printed,telephone.printedby,telephone.deleted_flag,telephone.order_cancel_flag,telephone.nameRef,telephone.orgRef,telephone.invoiceNo,telephone.commit,comp_reg.email,comp_reg.abrv as comp_abrv FROM telephone,interpreter_reg,comp_reg WHERE telephone.intrpName=interpreter_reg.id AND telephone.orgName=comp_reg.abrv AND telephone.multInv_flag=0 AND telephone.deleted_flag = 0 AND telephone.disposed_of = 0 and telephone.order_cancel_flag=0 and telephone.commit=1 $po_string_tp and (round(telephone.rAmount,2) < round((telephone.total_charges_comp+(telephone.total_charges_comp*telephone.cur_vat)),2) or telephone.total_charges_comp =0) and telephone.invoic_date like '$invoic_date%' and telephone.assignDate like '$assignDate%' and telephone.source like '%$job%' and interpreter_reg.name like '%$interp%' and telephone.orgName = '$org' and telephone.invoiceNo like '%$inov%'
        //            UNION ALL SELECT translation.porder,comp_reg.po_req,'Translation' as type,translation.id,translation.intrpName,translation.orgName,interpreter_reg.name,translation.source,translation.invoic_date,translation.asignDate as assignDate,'00:00:00' as assignTime,translation.orgContact,translation.submited, translation.aloct_by,translation.aloct_date,translation.dated,translation.hrsubmited,translation.comp_hrsubmited,translation.interp_hr_date,translation.comp_hr_date, translation.numberUnit as hoursWorkd,translation.C_numberUnit as C_hoursWorkd,translation.total_charges_comp,translation.cur_vat,0 as C_otherexpns,translation.credit_note,translation.C_admnchargs, translation.rAmount,translation.rDate,translation.sentemail,translation.printed,translation.printedby,translation.deleted_flag,translation.order_cancel_flag,translation.nameRef,translation.orgRef,translation.invoiceNo,translation.commit,comp_reg.email,comp_reg.abrv as comp_abrv FROM translation,interpreter_reg,comp_reg WHERE translation.intrpName=interpreter_reg.id AND translation.orgName=comp_reg.abrv AND translation.multInv_flag=0 AND translation.deleted_flag = 0 AND interpreter.disposed_of = 0 and translation.order_cancel_flag=0 and translation.commit=1 $po_string_tr and (round(translation.rAmount,2) < round((translation.total_charges_comp+(translation.total_charges_comp*translation.cur_vat)),2) or translation.total_charges_comp =0) and translation.invoic_date like '$invoic_date%' and translation.asignDate like '$assignDate%' and translation.source like '%$job%' and interpreter_reg.name like '%$interp%' and translation.orgName = '$org' and translation.invoiceNo like '%$inov%') as grp WHERE type like '%$type%' ORDER BY CONCAT(assignDate,' ',assignTime)";




        $query =
            'SELECT * from (SELECT interpreter.porder,interpreter.assignDur,interpreter.C_travelTimeRate,interpreter.C_travelTimeHour,interpreter.C_chargeTravelTime,interpreter.C_chargeTravel,interpreter.C_travelCost,interpreter.C_chargInterp,interpreter.C_otherCost as C_otherCharges,interpreter.inchPerson,comp_reg.po_req,interpreter.C_rateHour,"Interpreter" as type,interpreter.id,interpreter.intrpName,interpreter.orgName,interpreter_reg.name,interpreter.source,interpreter.invoic_date,interpreter.assignDate,interpreter.assignTime,interpreter.orgContact,interpreter.submited, interpreter.aloct_by,interpreter.aloct_date,interpreter.dated,interpreter.hrsubmited,interpreter.comp_hrsubmited,interpreter.interp_hr_date, interpreter.comp_hr_date,interpreter.hoursWorkd,interpreter.C_hoursWorkd,interpreter.total_charges_comp,interpreter.cur_vat,interpreter.C_otherexpns, interpreter.credit_note,interpreter.C_admnchargs,interpreter.rAmount,interpreter.rDate,interpreter.sentemail,interpreter.printed,interpreter.printedby,interpreter.deleted_flag,interpreter.order_cancel_flag,interpreter.nameRef,interpreter.orgRef,interpreter.invoiceNo,interpreter.commit,interpreter.new_comp_id,comp_reg.email,comp_reg.abrv as comp_abrv FROM interpreter,interpreter_reg,comp_reg WHERE interpreter.intrpName=interpreter_reg.id AND interpreter.orgName=comp_reg.abrv AND interpreter.deleted_flag = 0 AND interpreter.disposed_of = 0 and interpreter.order_cancel_flag=0 and interpreter.commit=1 ' . $po_string_int . ' ' . $append_multi_int . ' ' . $append_over_dues_int . ' and (round(interpreter.rAmount,2) < round((interpreter.total_charges_comp+(interpreter.total_charges_comp*interpreter.cur_vat)),2) or interpreter.total_charges_comp =0) ' . (!empty($invoic_date) && $invoic_date != "" ? '  and interpreter.invoic_date like "' . $invoic_date . '%" ' : '') . (!empty($assignDate) && $assignDate != "" ? '  and interpreter.assignDate like "' . $assignDate . '%" ' : '') . (!empty($job) && $job != "" ? '  and ((interpreter.source="' . $job . '" OR interpreter.target="' . $job . '") OR (interpreter.source="' . $job . '" AND interpreter.target="English") OR (interpreter.source="English" AND interpreter.target="' . $job . '")) ' : '') . (!empty($interp) && $interp != "" ? '  and interpreter_reg.name like "%' . $interp . '%" ' : '') . $p_org_ad . (!empty($org) && $org != "" ? '  and interpreter.orgName IN (' . $all_cz["all_cz"] . ') ' : '') . (!empty($inov) && $inov != "" ? '  and interpreter.invoiceNo like "%' . $inov . '%" ' : '') . '
               UNION ALL SELECT telephone.porder,telephone.assignDur,0 as C_travelTimeHour,0 as C_travelTimeRate,0 as C_chargeTravelTime,0 as C_chargeTravel,0 as C_travelCost,telephone.C_chargInterp,telephone.C_otherCharges,telephone.inchPerson,comp_reg.po_req,telephone.C_rateHour,"Telephone" as type,telephone.id,telephone.intrpName,telephone.orgName,interpreter_reg.name,telephone.source,telephone.invoic_date,telephone.assignDate,telephone.assignTime,telephone.orgContact,telephone.submited, telephone.aloct_by,telephone.aloct_date,telephone.dated,telephone.hrsubmited,telephone.comp_hrsubmited,telephone.interp_hr_date,telephone.comp_hr_date, telephone.hoursWorkd,telephone.C_hoursWorkd,telephone.total_charges_comp,telephone.cur_vat,telephone.calCharges as C_otherexpns,telephone.credit_note,telephone.C_admnchargs, telephone.rAmount,telephone.rDate,telephone.sentemail,telephone.printed,telephone.printedby,telephone.deleted_flag,telephone.order_cancel_flag,telephone.nameRef,telephone.orgRef,telephone.invoiceNo,telephone.commit,telephone.new_comp_id,comp_reg.email,comp_reg.abrv as comp_abrv FROM telephone,interpreter_reg,comp_reg WHERE telephone.intrpName=interpreter_reg.id AND telephone.orgName=comp_reg.abrv AND telephone.deleted_flag = 0 AND telephone.disposed_of = 0 and telephone.order_cancel_flag=0 and telephone.commit=1 ' . $po_string_tp . ' ' . $append_multi_tp . ' ' . $append_over_dues_tp . ' and (round(telephone.rAmount,2) < round((telephone.total_charges_comp+(telephone.total_charges_comp*telephone.cur_vat)),2) or telephone.total_charges_comp =0) ' . (!empty($invoic_date) && $invoic_date != "" ? '  and telephone.invoic_date like "' . $invoic_date . '%" ' : '') . (!empty($assignDate) && $assignDate != "" ? '  and telephone.assignDate like "' . $assignDate . '%" ' : '') . (!empty($job) && $job != "" ? '  and ((telephone.source="' . $job . '" OR telephone.target="' . $job . '") OR (telephone.source="' . $job . '" AND telephone.target="English") OR (telephone.source="English" AND telephone.target="' . $job . '")) ' : '') . (!empty($interp) && $interp != "" ? '  and interpreter_reg.name like "%' . $interp . '%" ' : '') . $p_org_ad . (!empty($org) && $org != "" ? '  and telephone.orgName IN (' . $all_cz["all_cz"] . ') ' : '') . (!empty($inov) && $inov != "" ? '  and telephone.invoiceNo like "%' . $inov . '%" ' : '') . '
               UNION ALL SELECT translation.porder,"N/A" as assignDur,0 as C_travelTimeHour,0 as C_travelTimeRate,0 as C_chargeTravelTime,0 as C_chargeTravel,0 as C_travelCost,0 as C_chargInterp,0 as C_otherCharges,translation.orgContact as inchPerson,comp_reg.po_req,translation.C_rpU as C_rateHour,"Translation" as type,translation.id,translation.intrpName,translation.orgName,interpreter_reg.name,translation.source,translation.invoic_date,translation.asignDate as assignDate,"00:00:00" as assignTime,translation.orgContact,translation.submited, translation.aloct_by,translation.aloct_date,translation.dated,translation.hrsubmited,translation.comp_hrsubmited,translation.interp_hr_date,translation.comp_hr_date, translation.numberUnit as hoursWorkd,translation.C_numberUnit as C_hoursWorkd,translation.total_charges_comp,translation.cur_vat,0 as C_otherexpns,translation.credit_note,translation.C_admnchargs, translation.rAmount,translation.rDate,translation.sentemail,translation.printed,translation.printedby,translation.deleted_flag,translation.order_cancel_flag,translation.nameRef,translation.orgRef,translation.invoiceNo,translation.commit,translation.new_comp_id,comp_reg.email,comp_reg.abrv as comp_abrv FROM translation,interpreter_reg,comp_reg WHERE translation.intrpName=interpreter_reg.id AND translation.orgName=comp_reg.abrv AND translation.deleted_flag = 0 AND translation.disposed_of = 0 and translation.order_cancel_flag=0 and translation.commit=1 ' . $po_string_tr . ' ' . $append_multi_tr . ' ' . $append_over_dues_tr . 'and (round(translation.rAmount,2) < round((translation.total_charges_comp+(translation.total_charges_comp*translation.cur_vat)),2) or translation.total_charges_comp =0) ' . (!empty($invoic_date) && $invoic_date != "" ? '  and translation.invoic_date like "' . $invoic_date . '%" ' : '') . (!empty($assignDate) && $assignDate != "" ? '  and translation.asignDate like "' . $assignDate . '%" ' : '') . (!empty($job) && $job != "" ? '  and ((translation.source="' . $job . '" OR translation.target="' . $job . '") OR (translation.source="' . $job . '" AND translation.target="English") OR (translation.source="English" AND translation.target="' . $job . '")) ' : '') . (!empty($interp) && $interp != "" ? '  and interpreter_reg.name like "%' . $interp . '%" ' : '') . $p_org_ad . (!empty($org) && $org != "" ? '  and translation.orgName IN (' . $all_cz["all_cz"] . ') ' : '') . (!empty($inov) && $inov != "" ? '  and translation.invoiceNo like "%' . $inov . '%" ' : '') . ') as grp WHERE type like "%' . $type . '%" ORDER BY CONCAT(assignDate," ",assignTime)';
    }
}
//echo $query;exit;
$query =
            'SELECT * from (SELECT interpreter.porder,interpreter.assignDur,interpreter.C_travelTimeRate,interpreter.C_travelTimeHour,interpreter.C_chargeTravelTime,interpreter.C_chargeTravel,interpreter.C_travelCost,interpreter.C_chargInterp,interpreter.C_otherCost as C_otherCharges,interpreter.inchPerson,comp_reg.po_req,interpreter.C_rateHour,"Interpreter" as type,interpreter.id,interpreter.intrpName,interpreter.orgName,interpreter_reg.name,interpreter.source,interpreter.invoic_date,interpreter.assignDate,interpreter.assignTime,interpreter.orgContact,interpreter.submited, interpreter.aloct_by,interpreter.aloct_date,interpreter.dated,interpreter.hrsubmited,interpreter.comp_hrsubmited,interpreter.interp_hr_date, interpreter.comp_hr_date,interpreter.hoursWorkd,interpreter.C_hoursWorkd,interpreter.total_charges_comp,interpreter.cur_vat,interpreter.C_otherexpns, interpreter.credit_note,interpreter.C_admnchargs,interpreter.rAmount,interpreter.rDate,interpreter.sentemail,interpreter.printed,interpreter.printedby,interpreter.deleted_flag,interpreter.order_cancel_flag,interpreter.nameRef,interpreter.orgRef,interpreter.invoiceNo,interpreter.commit,interpreter.new_comp_id,comp_reg.email,comp_reg.abrv as comp_abrv FROM interpreter,interpreter_reg,comp_reg WHERE interpreter.intrpName=interpreter_reg.id AND interpreter.orgName=comp_reg.abrv AND interpreter.deleted_flag = 0 AND interpreter.disposed_of = 0 and interpreter.order_cancel_flag=0 and interpreter.commit=1 ' . $po_string_int . ' ' . $append_multi_int . ' ' . $append_over_dues_int . ' and (round(interpreter.rAmount,2) < round((interpreter.total_charges_comp+(interpreter.total_charges_comp*interpreter.cur_vat)),2) or interpreter.total_charges_comp =0) ' . (!empty($invoic_date) && $invoic_date != "" ? '  and interpreter.invoic_date like "' . $invoic_date . '%" ' : '') . (!empty($assignDate) && $assignDate != "" ? '  and interpreter.assignDate like "' . $assignDate . '%" ' : '') . (!empty($job) && $job != "" ? '  and ((interpreter.source="' . $job . '" OR interpreter.target="' . $job . '") OR (interpreter.source="' . $job . '" AND interpreter.target="English") OR (interpreter.source="English" AND interpreter.target="' . $job . '")) ' : '') . (!empty($interp) && $interp != "" ? '  and interpreter_reg.name like "%' . $interp . '%" ' : '') . $p_org_ad . (!empty($org) && $org != "" ? '  and interpreter.orgName IN (' . $all_cz["all_cz"] . ') ' : '') . (!empty($inov) && $inov != "" ? '  and interpreter.invoiceNo like "%' . $inov . '%" ' : '') . '
               UNION ALL SELECT telephone.porder,telephone.assignDur,0 as C_travelTimeHour,0 as C_travelTimeRate,0 as C_chargeTravelTime,0 as C_chargeTravel,0 as C_travelCost,telephone.C_chargInterp,telephone.C_otherCharges,telephone.inchPerson,comp_reg.po_req,telephone.C_rateHour,"Telephone" as type,telephone.id,telephone.intrpName,telephone.orgName,interpreter_reg.name,telephone.source,telephone.invoic_date,telephone.assignDate,telephone.assignTime,telephone.orgContact,telephone.submited, telephone.aloct_by,telephone.aloct_date,telephone.dated,telephone.hrsubmited,telephone.comp_hrsubmited,telephone.interp_hr_date,telephone.comp_hr_date, telephone.hoursWorkd,telephone.C_hoursWorkd,telephone.total_charges_comp,telephone.cur_vat,telephone.calCharges as C_otherexpns,telephone.credit_note,telephone.C_admnchargs, telephone.rAmount,telephone.rDate,telephone.sentemail,telephone.printed,telephone.printedby,telephone.deleted_flag,telephone.order_cancel_flag,telephone.nameRef,telephone.orgRef,telephone.invoiceNo,telephone.commit,telephone.new_comp_id,comp_reg.email,comp_reg.abrv as comp_abrv FROM telephone,interpreter_reg,comp_reg WHERE telephone.intrpName=interpreter_reg.id AND telephone.orgName=comp_reg.abrv AND telephone.deleted_flag = 0 AND telephone.disposed_of = 0 and telephone.order_cancel_flag=0 and telephone.commit=1 ' . $po_string_tp . ' ' . $append_multi_tp . ' ' . $append_over_dues_tp . ' and (round(telephone.rAmount,2) < round((telephone.total_charges_comp+(telephone.total_charges_comp*telephone.cur_vat)),2) or telephone.total_charges_comp =0) ' . (!empty($invoic_date) && $invoic_date != "" ? '  and telephone.invoic_date like "' . $invoic_date . '%" ' : '') . (!empty($assignDate) && $assignDate != "" ? '  and telephone.assignDate like "' . $assignDate . '%" ' : '') . (!empty($job) && $job != "" ? '  and ((telephone.source="' . $job . '" OR telephone.target="' . $job . '") OR (telephone.source="' . $job . '" AND telephone.target="English") OR (telephone.source="English" AND telephone.target="' . $job . '")) ' : '') . (!empty($interp) && $interp != "" ? '  and interpreter_reg.name like "%' . $interp . '%" ' : '') . $p_org_ad . (!empty($org) && $org != "" ? '  and telephone.orgName IN (' . $all_cz["all_cz"] . ') ' : '') . (!empty($inov) && $inov != "" ? '  and telephone.invoiceNo like "%' . $inov . '%" ' : '') . '
               UNION ALL SELECT translation.porder,"N/A" as assignDur,0 as C_travelTimeHour,0 as C_travelTimeRate,0 as C_chargeTravelTime,0 as C_chargeTravel,0 as C_travelCost,0 as C_chargInterp,0 as C_otherCharges,translation.orgContact as inchPerson,comp_reg.po_req,translation.C_rpU as C_rateHour,"Translation" as type,translation.id,translation.intrpName,translation.orgName,interpreter_reg.name,translation.source,translation.invoic_date,translation.asignDate as assignDate,"00:00:00" as assignTime,translation.orgContact,translation.submited, translation.aloct_by,translation.aloct_date,translation.dated,translation.hrsubmited,translation.comp_hrsubmited,translation.interp_hr_date,translation.comp_hr_date, translation.numberUnit as hoursWorkd,translation.C_numberUnit as C_hoursWorkd,translation.total_charges_comp,translation.cur_vat,0 as C_otherexpns,translation.credit_note,translation.C_admnchargs, translation.rAmount,translation.rDate,translation.sentemail,translation.printed,translation.printedby,translation.deleted_flag,translation.order_cancel_flag,translation.nameRef,translation.orgRef,translation.invoiceNo,translation.commit,translation.new_comp_id,comp_reg.email,comp_reg.abrv as comp_abrv FROM translation,interpreter_reg,comp_reg WHERE translation.intrpName=interpreter_reg.id AND translation.orgName=comp_reg.abrv AND translation.deleted_flag = 0 AND translation.disposed_of = 0 and translation.order_cancel_flag=0 and translation.commit=1 ' . $po_string_tr . ' ' . $append_multi_tr . ' ' . $append_over_dues_tr . 'and (round(translation.rAmount,2) < round((translation.total_charges_comp+(translation.total_charges_comp*translation.cur_vat)),2) or translation.total_charges_comp =0) ' . (!empty($invoic_date) && $invoic_date != "" ? '  and translation.invoic_date like "' . $invoic_date . '%" ' : '') . (!empty($assignDate) && $assignDate != "" ? '  and translation.asignDate like "' . $assignDate . '%" ' : '') . (!empty($job) && $job != "" ? '  and ((translation.source="' . $job . '" OR translation.target="' . $job . '") OR (translation.source="' . $job . '" AND translation.target="English") OR (translation.source="English" AND translation.target="' . $job . '")) ' : '') . (!empty($interp) && $interp != "" ? '  and interpreter_reg.name like "%' . $interp . '%" ' : '') . $p_org_ad . (!empty($org) && $org != "" ? '  and translation.orgName IN (' . $all_cz["all_cz"] . ') ' : '') . (!empty($inov) && $inov != "" ? '  and translation.invoiceNo like "%' . $inov . '%" ' : '') . ') as grp WHERE type like "%' . $type . '%" ORDER BY CONCAT(assignDate," ",assignTime)';
    
$result = mysqli_query($con, $query);
// echo "$query <br><br>rows are ".mysqli_num_rows($result);
// die(); exit();
if (!empty($po) && $po == 'rm') {
    $po_status = ' with Required & Missing Purchase Orders';
} else if (!empty($po) && $po == 'rs') {
    $po_status = ' with Required & Updated Purchase Orders';
} else if (!empty($po) && $po == 'nr') {
    $po_status = ' with Purchase Orders Not Required';
} else {
    $po_status = '';
}
if (isset($string) && !empty($string)) {
    $po_status = '';
}
// if (!empty($type)) {
//     $put_var = (strtolower($type) === 'telephone' ? 'Remote' : $type) . ' statement of Outstanding Invoices' . $po_status;
// } else {
//     $put_var = 'Statement of Outstanding Invoices' . $po_status;
// }
$put_var = ' Statement Of Outstanding Invoices';
$htmlTable = '';
$pound_symbol = mb_convert_encoding("£", 'UTF-16LE', 'UTF-8');
$htmlTable .= '<style>
table {border-collapse: collapse; width:670px;}
th {border: 1px solid #999; padding: 0.5rem;text-align: left;background-color:#039; color:#FFF; font-weight:bold;}
td {border: 1px solid #999; padding: 0.5rem;text-align: left;}
</style>
<div>';
$put_org = !empty($org) ? $comp_full_cz['all_cz'] : 'All Companies';
$orgNames = [];
$orgGroups = []; // base => array of full names
$rows = [];

while ($row = mysqli_fetch_assoc($result)) {
    $raw = $row['orgName'];

    // Extract base: before first dash or space
    $base = strtoupper(trim(preg_replace('/[^A-Za-z0-9]/', '', strtok($raw, '- '))));

    if (!isset($orgGroups[$base])) {
        $orgGroups[$base] = [];
    }

    $orgGroups[$base][] = $raw;
    $rows[] = $row;
}

// Build final display names
foreach ($orgGroups as $base => $names) {
    $unique = array_unique($names);

    if (count($unique) === 1) {
        $orgNames[] = $unique[0]; // Only one unique entry, keep original
    } else {
        $orgNames[] = $base . ' (multiple units)';
    }
}

$put_org = implode(', ', $orgNames);
$htmlTable .= '<h2 style="text-align:center;background:grey">' . $put_var . '</h2>
<p align="right"> Report Date: ' . $misc->sys_date() . '</p>
<p>Organization Name : ' . $put_org . '</p>
</div>

<table>
<thead>
<tr>
	<th style="background-color:#039;color:#FFF;">S.No</th>
    <th style="background-color:#039;color:#FFF;">Invoice</th>
    <th style="background-color:#039;color:#FFF;">Assignment Date</th>
    <th style="background-color:#039;color:#FFF;">Type</th>
    <th style="background-color:#039;color:#FFF;">Language</th>
    <th style="background-color:#039;color:#FFF;">Organization</th>
    <th style="background-color:#039;color:#FFF;">Interpreter</th>
    <th style="background-color:#039;color:#FFF;">Booking Person</th>
    <th style="background-color:#039;color:#FFF;">Client Ref</th>
    <th style="background-color:#039;color:#FFF;">Length</th>
    <th style="background-color:#039;color:#FFF;">Price Per Unit (' . $pound_symbol . ')</th>
    <th style="background-color:#039;color:#FFF;">Cost of Interpreting/Translation (' . $pound_symbol . ')</th>
    <th style="background-color:#039;color:#FFF;">Travel Time Duration in Hours</th>
    <th style="background-color:#039;color:#FFF;">Travel Time Rate per Hour (' . $pound_symbol . ')</th>
    <th style="background-color:#039;color:#FFF;">Cost of Travel Time (' . $pound_symbol . ')</th>
    <th style="background-color:#039;color:#FFF;">Mileage</th>
    <!--th style="background-color:#039;color:#FFF;">Price per Mile (' . $pound_symbol . ')</th-->
    <th style="background-color:#039;color:#FFF;">Cost of Mileage (' . $pound_symbol . ')</th>
    <th style="background-color:#039;color:#FFF;">Public Transport Cost (' . $pound_symbol . ')</th>
    <th style="background-color:#039;color:#FFF;">Admin Cost</th>
    <th style="background-color:#039;color:#FFF;">Other Expenses Non-Vatable (' . $pound_symbol . ')</th>
    <th style="background-color:#039;color:#FFF;">Subtotal  (' . $pound_symbol . ')</th>
    <th style="background-color:#039;color:#FFF;">VAT  (20%) (' . $pound_symbol . ')</th>
    <th style="background-color:#039;color:#FFF;">Total Cost (' . $pound_symbol . ')</th>
    <th style="background-color:#039;color:#FFF;">Purchase Order#</th> 
</tr>';
$i=1;
foreach ($rows as $row){
    if (!empty($type) && strtolower($row['type']) !== strtolower($type)) {
        continue;
    }
    if ($row['po_req'] != 0) {
        $row['porder'] = empty($row['porder']) ? '<span style="color:red;">Missing!</span>' : $row['porder'];
    } else {
        $row['porder'] = 'N/A';
    }
    if ($row['type'] == 'Interpreter') {
        $totalforvat = $row['total_charges_comp'];
        $vatpay = $totalforvat * $row['cur_vat'];
        $totinvnow = $totalforvat + $vatpay + $row['C_otherexpns'];

        $withou_VAT_interp = $row["C_otherCharges"];
		$C_hoursWorkd_C_rateHour = $row["C_hoursWorkd"] * $row["C_rateHour"];
		$total_charges = $row['total_charges_comp'] + $row['int_vat'];
		$sub_total = $row['C_chargInterp'] + $row["C_chargeTravelTime"] + $row['C_chargeTravel'] + $row['C_travelCost'] + $row["C_otherCost"] + $row["C_admnchargs"];
		$vat_percent = ($sub_total - $withou_VAT_interp) * 0.2;
		$total_cost = $sub_total + $vat_percent;

		$grand_sub_total += $sub_total;
		$grand_total_vat += $vat_percent;
		$grand_non_vat += $withou_VAT_interp; // other expenses
        $total_invoice = $grand_sub_total + $grand_total_vat;

    } else if ($row['type'] == 'Telephone') {
        $totalforvat = $row['total_charges_comp'] + $row['C_otherexpns'];
        $vatpay = $totalforvat * $row['cur_vat'];
        $totinvnow = $totalforvat + $vatpay;

        $withou_VAT_telp = $row["C_otherCharges"];
		$non_vat_tlep = $row["total_charges_comp"] - $row["C_otherCharges"];

		$C_otherCharges_CallCharges = $row['C_callcharges'] + $row['C_otherCharges'];

		$sub_total = $row['C_chargInterp'] + $C_otherCharges_CallCharges;
		$vat_percent = (($sub_total - $C_otherCharges_CallCharges) * 0.2);
		$total_cost = $sub_total + $vat_percent;

		$grand_sub_total += $sub_total;
		$grand_total_vat += $vat_percent;
		$grand_non_vat += $C_otherCharges_CallCharges;
        $total_invoice = $grand_sub_total + $grand_total_vat;

    } else {
        $totalforvat = $row['total_charges_comp'];
        $vatpay = $totalforvat * $row['cur_vat'];
        $totinvnow = $totalforvat + $vatpay;

        $withou_VAT = $row["C_otherCharg"];
		$non_vat = $row["total_charges_comp"] - $row["C_otherCharg"];

		$sub_total = ($row["C_hoursWorkd"] * $row["C_rateHour"]) + $row["C_otherCharg"]; //$row["total_units"];
		$vat_percent = ($sub_total - $row["C_otherCharg"]) * 0.2;
		$total_cost = $sub_total + $vat_percent;

		$grand_sub_total += $sub_total;
		$grand_total_vat += $vat_percent;
		$grand_non_vat += $withou_VAT;
        $total_invoice = $grand_sub_total + $grand_total_vat; 


    }

    $gotcreditnote = false;
    $append_invoiceNo = '';
    if (!empty($row['credit_note']) && $row['type'] == "Interpreter") {
        $append_invoiceNo = "-0" . $acttObj->read_specific("count(*) as counter", "credit_notes", "order_id=" . $row['id'] . " and order_type='f2f'")['counter'];
    } elseif (!empty($row['credit_note']) && $row['type'] == "Telephone") {
        $append_invoiceNo = "-0" . $acttObj->read_specific("count(*) as counter", "credit_notes", "order_id=" . $row['id'] . " and order_type='tp'")['counter'];
    } elseif (!empty($row['credit_note']) && $row['type'] == "Translation") {
        $append_invoiceNo = "-0" . $acttObj->read_specific("count(*) as counter", "credit_notes", "order_id=" . $row['id'] . " and order_type='tr'")['counter'];
    }
    // print_r($row);
    // die(); exit();
    if (isset($row['credit_note']) && $row['credit_note'] != "") {
        // $totinvnow = 0;
        $gotcreditnote = true;
    }
    if ($row['po_req'] == 1 && $row['porder'] != '') {
        $table_po = $row['porder'];
    } else if ($row['po_req'] == 1 && $row['porder'] == '') {
        $table_po = '<b style="color:red;">Missing!</b>';
    } else {
        $table_po = '<b>Not required!</b>';
    }
    $prv_org='';
    if($row['orgName']=="LSUK_Private Client" && $row['new_comp_id']!=0){
        $prv_org = $acttObj->read_specific("name", "private_company", " id={$row['new_comp_id']}")['name'];
        $prv_org = "LSUK_".$prv_org;
    }
    if($row['type']== 'Interpreter'){
        $htmlTable .= '<tr>';
		$htmlTable .= '<td>' . $i . '</td>';
        $htmlTable .= '<td>' . $row['invoiceNo'] . '</td>';
		$htmlTable .= '<td>' . $misc->dated($row["assignDate"]) . '</td>';
		$htmlTable .= '<td>Face to Face</td>';
		$htmlTable .= '<td>' . $row["source"] . '</td>';
        $htmlTable .= '<td>' . $row["orgName"] . '</td>';
		$htmlTable .= '<td>' . $row["name"] . '</td>';
		$htmlTable .= '<td>' . $row["inchPerson"] . '</td>';
		$htmlTable .= '<td>' . $row["orgRef"] . '</td>';
		$htmlTable .= '<td>' . $row["C_hoursWorkd"] . ' hours</td>';
		$htmlTable .= '<td>' . $row["C_rateHour"] . '/hour</td>';
		$htmlTable .= '<td>' . formatTwoDecimal($row["C_chargInterp"]). '</td>';
		$htmlTable .= '<td>' . $row["C_travelTimeHour"] . '</td>';
		$htmlTable .= '<td>' . formatTwoDecimal($row["C_travelTimeRate"]) . '</td>';
		$htmlTable .= '<td>' . $row["C_chargeTravelTime"] . '</td>';
		$htmlTable .= '<td>' . $row["C_travelMile"] . '</td>';
		//$htmlTable .= '<td>' . $row["C_rateMile"] . '</td>';
		$htmlTable .= '<td>' . $row["C_chargeTravel"] . '</td>';
		$htmlTable .= '<td>' . formatTwoDecimal($row["C_travelCost"]) . '</td>';
		$htmlTable .= '<td>' . formatTwoDecimal($row["C_admnchargs"]) . '</td>';
		$htmlTable .= '<td>' . formatTwoDecimal($withou_VAT_interp) . '</td>';
		$htmlTable .= '<td>' . formatTwoDecimal($sub_total) . '</td>';
		$htmlTable .= '<td>' . formatTwoDecimal($vat_percent) . '</td>';
		$htmlTable .= '<td>' . formatTwoDecimal($total_cost) . '</td>';
		$htmlTable .= '<td>' . $row["porder"] . '</td>';
		$htmlTable .= '</tr>';
    }else if($row['type']== 'Telephone'){
        $htmlTable .= '<tr>';
		$htmlTable .= '<td>' . $i . '</td>';
        $htmlTable .= '<td>' . $row['invoiceNo'] . '</td>';
		$htmlTable .= '<td>' . $misc->dated($row["assignDate"]) . '</td>';
		$htmlTable .= '<td>Remote</td>';
		$htmlTable .= '<td>' . $row["source"] . '</td>';
        $htmlTable .= '<td>' . $row["orgName"] . '</td>';
		$htmlTable .= '<td>' . $row["name"] . '</td>';
		$htmlTable .= '<td>' . $row["inchPerson"] . '</td>';
		$htmlTable .= '<td>' . $row["orgRef"] . '</td>';
		$htmlTable .= '<td>' . $row["C_hoursWorkd"] . ' mins</td>';
		$htmlTable .= '<td>' . $row["C_rateHour"] . '/min</td>';
		$htmlTable .= '<td>' . formatTwoDecimal($row['C_chargInterp']) . '</td>';
		$htmlTable .= '<td>N/A</td>';
		$htmlTable .= '<td>N/A</td>';
		$htmlTable .= '<td>N/A</td>';
		$htmlTable .= '<td>N/A</td>';
		//$htmlTable .= '<td>N/A</td>';
		$htmlTable .= '<td>N/A</td>';
		$htmlTable .= '<td>N/A</td>';
		$htmlTable .= '<td>N/A</td>';
		$htmlTable .= '<td>' . formatTwoDecimal($C_otherCharges_CallCharges) . '</td>';
		$htmlTable .= '<td>' . formatTwoDecimal($sub_total) . '</td>';
		$htmlTable .= '<td>' . formatTwoDecimal($vat_percent) . '</td>';
		$htmlTable .= '<td>' . formatTwoDecimal($total_cost) . '</td>';
		$htmlTable .= '<td>' . $row["porder"] . '</td>';
		$htmlTable .= '</tr>';
    }else{

        $htmlTable .= '<tr>';
		$htmlTable .= '<td>' . $i . '</td>';
        $htmlTable .= '<td>' . $row['invoiceNo'] . '</td>';
		$htmlTable .= '<td>' . $misc->dated($row["assignDate"]) . '</td>';
		$htmlTable .= '<td>Translation</td>';
		$htmlTable .= '<td>' . $row["source"] . '</td>';
        $htmlTable .= '<td>' . $row["orgName"] . '</td>';
		$htmlTable .= '<td>' . $row["name"] . '</td>';
		$htmlTable .= '<td>' . $row["inchPerson"] . '</td>';
		$htmlTable .= '<td>' . $row["orgRef"] . '</td>';
		$htmlTable .= '<td>' . $row["C_hoursWorkd"] . ' words</td>';
		$htmlTable .= '<td>' . $row["C_rateHour"] . '/word</td>';
		$htmlTable .= '<td>' . formatTwoDecimal($sub_total) . '</td>';
		$htmlTable .= '<td>N/A</td>';
		$htmlTable .= '<td>N/A</td>';
		$htmlTable .= '<td>N/A</td>';
		$htmlTable .= '<td>N/A</td>';
		//$htmlTable .= '<td>N/A</td>';
		$htmlTable .= '<td>N/A</td>';
		$htmlTable .= '<td>N/A</td>';
		$htmlTable .= '<td>N/A</td>';
		$htmlTable .= '<td>' .formatTwoDecimal( $row["C_otherCharg"]) . '</td>';
		$htmlTable .= '<td>' . formatTwoDecimal($sub_total) . '</td>';
		$htmlTable .= '<td>' . formatTwoDecimal($vat_percent) . '</td>';
		$htmlTable .= '<td>' . formatTwoDecimal($total_cost) . '</td>';
		$htmlTable .= '<td>' . $row["porder"] . '</td>';
		$htmlTable .= '</tr>';
    }

    $i++;
}

$htmlTable .= '<tfoot>
		<tr class="summary">
			<td colspan="22" align="right" style="text-right:right;"><b>Total Cost before VAT</b></td>
			<td colspan="2"><b>' . $misc->numberFormat_fun($grand_sub_total - $grand_non_vat) . '</b></td>
		</tr>

		<tr class="summary">
			<td colspan="22" align="right style="text-right:right;"b>VAT @20%</b></td>
			<td colspan="2"><b>' . $misc->numberFormat_fun($grand_total_vat) . '</b></td>
		</tr>

        <tr class="summary">
			<td colspan="22" align="right" style="text-right:right;"><b>Total Non-VAT Cost</b></td>
			<td colspan="2"><b>' . $misc->numberFormat_fun($grand_non_vat) . '</b></td>
		</tr>

		<tr class="summary">
			<td colspan="22" align="right" style="text-right:right;"><b>Total Invoice</b></td>
			<td colspan="2"><b>' . $misc->numberFormat_fun($total_invoice) . '</b></td>
		</tr>
	</tfoot>';
$htmlTable .= '</table>';

list($a, $b) = explode('.', basename(__FILE__));
header("Content-Type: application/xls");
header("Content-Disposition: attachment; filename=" . $a . ".xls");
echo $htmlTable;

