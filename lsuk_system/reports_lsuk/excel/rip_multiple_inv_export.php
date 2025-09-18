<?php include '../../db.php';
include_once('../../class.php');
$excel = @$_GET['excel'];
$proceed = @$_GET['proceed'];

$multInvoicNo = @$_GET['multInvoiceNo'];

$search_1 = @$_GET['search_1'];
$orgs = array();
if (isset($search_1) && $search_1 != "") {
    $orgs = explode(",", $search_1);
}
$search_2 = @$_GET['search_2'];
$search_3 = @$_GET['search_3'];
$p_org = @$_GET['p_org'];
if (isset($p_org) && (!empty($p_org))) {
    $p_org_ad = $acttObj->query_extra("GROUP_CONCAT(CONCAT('''', comp_reg.abrv, '''' )) as ch_ids", "comp_reg,subsidiaries", "subsidiaries.child_comp=comp_reg.id AND subsidiaries.parent_comp=$p_org", "set SESSION group_concat_max_len=10000")['ch_ids'];
} else {
    $p_org_ad = "";
}
$i = 1;
$g_total_interp = 0;
$g_total_telep = 0;
$g_total_trans = 0;
$g_total_vat_interp = 0;
$g_total_vat_telep = 0;
$g_total_vat_trans = 0;
$non_vat = 0;
$non_vat_tlep = 0;
$non_vat_trans = 0;
$non_vat_interp = 0;
$withou_VAT_interp = 0;
$withou_VAT_telp = 0;
$withou_VAT_trans = 0;
$C_travelCost = 0;
$C_rateMile_cost = 0;
$C_admnchargs_interp = 0;
$C_admnchargs_telep = 0;
$C_admnchargs_trans = 0;
$C_otherexpns = 0;
$total = 0;
$C_chargeTravelTime = 0;
//$multInvoicNo = '';

$multi_invoice_info = $acttObj->read_specific("*", "mult_inv", "m_inv = '$multInvoicNo' ");
if ($multi_invoice_info) {
    $id = $multi_invoice_info['id'];
}

$query = "SELECT  * FROM comp_reg WHERE id IN (" . $multi_invoice_info['comp_id'] . ")";
$comps = 0;
$name = $comp_abrv = $comp_abrv_q = $buildingName = $line1 = $line2 = $streetRoad = $postCode = $city = array();
$result = mysqli_query($con, $query);
while ($row = mysqli_fetch_assoc($result)) {
    array_push($name, $row["name"]);
    array_push($comp_abrv, $row["abrv"]);
    array_push($comp_abrv_q, "'" . $row["abrv"] . "'");
    array_push($buildingName, $row["buildingName"]);
    array_push($line1, $row["line1"]);
    array_push($line2, $row["line2"]);
    array_push($streetRoad, $row["streetRoad"]);
    array_push($postCode, $row["postCode"]);
    array_push($city, $row["city"]);
    $comps = $comps + 1;
}

//........................................//\\//\\Invoice #//\\//\\//\\...........................................//
$comp_abrv_str = "";
if (!empty($comp_abrv)) {
    $comp_abrv_str = implode("_", $comp_abrv);
}

$new_credit_note = $acttObj->read_specific("id , CONCAT(DATE_FORMAT(posted_on,'%y%m'),'_',LPAD(COUNT(id), 2, '0')) as credit_note_no", "credit_notes_income_invoices", "income_invoice_id = " . $id . " AND tbl = 'mult_inv'");

if ((isset($new_credit_note['id']) && !empty($new_credit_note['id']))) {
    $credit_note_number = $new_credit_note['credit_note_no'];
} else {
    $credit_note_number = "";
}

/*if ($proceed == 'Yes') {
    $nmbr = $acttObj->get_id('mult_inv');
    if ($nmbr == NULL) {
        $nmbr = 0;
    }
    $new_nmbr = str_pad($nmbr, 5, "0", STR_PAD_LEFT);
    $multInvoicNo = 'LSUK' . $new_nmbr . '' . $comp_abrv_str;
    $maxId = $nmbr;
    $acttObj->editFun('mult_inv', $maxId, 'm_inv', $multInvoicNo);
    $comp_ids = !empty($p_org) ? $p_org : $search_1;
    $acttObj->editFun('mult_inv', $maxId, 'comp_id', implode(",", $comp_ids));
    $acttObj->editFun('mult_inv', $maxId, 'comp_name', implode(",", $name));
    $acttObj->editFun('mult_inv', $maxId, 'comp_abrv', implode(",", $comp_abrv));
    $due = $misc->add_in_date(date("Y-m-d"), 15);
    $acttObj->editFun('mult_inv', $maxId, 'due_date', $due);
}*/
//$acttObj->editFun($table,$edit_id,'invoiceNo',$invoice);
//.....................................................//\\//\\//\\//\\//\\//\\//\\//\\//\\..........................
$comp_abrv_str = implode(",", $comp_abrv_q);

if (!empty($multInvoicNo)) {
    $check_exists = $acttObj->read_specific("COUNT(*) as counter", "mult_inv_items", "invoice_no = '$multInvoicNo'")['counter'];

    if ($check_exists < 1) {
        $query = "SELECT interpreter.*, interpreter_reg.name, canceled_orders.cancel_type_id, 
        canceled_orders.cancel_reason_id, canceled_orders.canceled_by, canceled_orders.canceled_date, 
        canceled_orders.canceled_reason, (SELECT count(id) FROM canceled_orders WHERE job_id = interpreter.id AND job_type = 1 AND interpreter.orderCancelatoin = 1) as has_cancelled_reason
        FROM interpreter 
        INNER JOIN interpreter_reg ON interpreter.intrpName = interpreter_reg.id 
        LEFT JOIN canceled_orders ON canceled_orders.job_id = interpreter.id AND canceled_orders.job_type = 1 
        WHERE interpreter.multInvoicNo = '$multInvoicNo' AND interpreter.multInv_flag = 1
        ORDER BY interpreter.assignDate ASC ";
    } else {
        $query = "SELECT mi.category_type, mi.assign_date as assignDate, mi.language_source as source, mi.reg_name as name, mi.inch_person as inchPerson, mi.client_ref as orgRef, mi.units as C_hoursWorkd, mi.price_per_unit as C_rateHour, mi.cost_of_intrp as C_chargInterp, mi.travel_time_duration as C_travelTimeHour, mi.travel_time_rate_per_hr as C_travelTimeRate, mi.cost_of_travel_time as C_chargeTravelTime, mi.mileage as C_travelMile, mi.cost_of_mileage as C_chargeTravel, mi.transport_cost as C_travelCost, mi.admin_cost as C_admnchargs, mi.other_expense as C_otherCost, mi.po_order as porder, mi.orderCancelation as orderCancelatoin, mi.canceled_date_time	as canceled_date, mi.canceled_reason, (SELECT count(id) FROM canceled_orders WHERE job_id = mi.main_job_id AND job_type = 1 AND mi.orderCancelation = 1) as has_cancelled_reason                      
        FROM mult_inv_items mi 
        WHERE mi.invoice_no = '" . $multInvoicNo . "' AND mi.category_type = 'Face to Face'
        ORDER BY mi.assign_date ASC";
    }
    /*$query = "SELECT interpreter.*, interpreter_reg.name  FROM interpreter
	   inner join interpreter_reg on interpreter.intrpName = interpreter_reg.id 
	   where interpreter.deleted_flag = 0 and interpreter.order_cancel_flag=0 and  interpreter.commit=0 and interpreter.multInv_flag=0 and assignDate between '$search_2' and '$search_3' and interpreter.orgName IN " . (!empty($p_org_ad) ? "($p_org_ad)" : "($comp_abrv_str)") . " order by assignDate Asc";*/
    $result = mysqli_query($con, $query);
    //...................................................................................................................................../////
    $htmlTable = $invoice_to = "";
    for ($kj = 0; $kj < count($name); $kj++) {
        $invoice_to .= "<p><strong>Invoice To: " . $name[$kj] . "</strong><br/>" . $buildingName[$kj] . "<br/>" . $line1[$kj] . "<br/>" . $line2[$kj] . "<br/>" . $streetRoad[$kj] . "<br/>" . $city[$kj] . "<br/>" . $postCode[$kj] . "<br/></p>";
    }

    $pound_symbol = mb_convert_encoding("£", 'UTF-16LE', 'UTF-8');

    $htmlTable .= '<style>
table {border-collapse: collapse; width:670px;}
th {border: 1px solid #999; padding: 0.5rem;text-align: left;background-color:#039; color:#FFF; font-weight:bold;}
td {border: 1px solid #999; padding: 0.5rem;text-align: left;}
</style>
<div>';

    if ((!isset($new_credit_note['id']) && empty($new_credit_note['id']))) {
        $htmlTable .= '<h2 style="text-decoration:underline; text-align:center">Invoice: (' . $multInvoicNo . ')</h2>';
    } else {
        $htmlTable .= '<h2 style="text-decoration:underline; text-align:center">Credit Note: <strong>' . $credit_note_number . '</strong></h2>
           <p style="text-align:center;">Invoice: (' . $multInvoicNo . ')</p>';
    }


    $htmlTable .= $invoice_to . '<br> Dated: ' . $misc->dated($multi_invoice_info['dated']) . '<br> Date Range:' . $misc->dated($multi_invoice_info['from_date']) . ' to ' . $misc->dated($multi_invoice_info['to_date']) . '</p>
</div>

<table>
<thead>
<tr>
 	<th style="background-color:#039;color:#FFF;">S.No</th>
    <th style="background-color:#039;color:#FFF;">Assignment Date</th>
    <th style="background-color:#039;color:#FFF;">Type</th>
    <th style="background-color:#039;color:#FFF;">Language</th>
    <th style="background-color:#039;color:#FFF;">Interpreter</th>
    <th style="background-color:#039;color:#FFF;">Booking Person</th>
    <th style="background-color:#039;color:#FFF;">Client Ref</th>
    <th style="background-color:#039;color:#FFF;">Length</th>
    <th style="background-color:#039;color:#FFF;">Price Per Unit (' . $pound_symbol . ')</th>
    <th style="background-color:#039;color:#FFF;">Cost of Interpreting (' . $pound_symbol . ')</th>
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
 </tr>
</thead>';
    while ($row = mysqli_fetch_assoc($result)) {
        $withou_VAT_interp = $row["C_otherCost"];
        $C_hoursWorkd_C_rateHour = $row["C_hoursWorkd"] * $row["C_rateHour"];
        $total_charges = $row['total_charges_comp'] + $row['int_vat'];
        $sub_total = $row['C_chargInterp'] + $row["C_chargeTravelTime"] + $row['C_chargeTravel'] + $row['C_travelCost'] + $row["C_otherCost"] + $row["C_admnchargs"];
        $vat_percent = ($sub_total - $withou_VAT_interp) * 0.2;
        //$vat_percent = ($row['C_cur_vat']) ? $row['C_cur_vat'] : '0.2';
        $total_cost = $sub_total + $vat_percent;

        $grand_sub_total += $sub_total;
        $grand_total_vat += $vat_percent;
        $grand_non_vat += $withou_VAT_interp; // other expenses

        $shw_sub_total = ($sub_total > 0) ? $misc->numberFormat_fun($sub_total) : 0;
        $shw_vat_percent = ($vat_percent > 0) ? $misc->numberFormat_fun($vat_percent) : 0;
        $shw_total_cost = ($total_cost > 0) ? $misc->numberFormat_fun($total_cost) : 0;

        $htmlTable .= '<tr>';
        $htmlTable .= '<td>' . $i . '</td>';
        $htmlTable .= '<td>' . $misc->dated($row["assignDate"]) . '</td>';
        $htmlTable .= '<td>Face to Face</td>';
        $htmlTable .= '<td>' . $row["source"] . '</td>';
        $htmlTable .= '<td>' . $row["name"] . '</td>';
        $htmlTable .= '<td>' . $row["inchPerson"] . '</td>';
        $htmlTable .= '<td>' . $row["orgRef"] . '</td>';
        $htmlTable .= '<td>' . $row["C_hoursWorkd"] . '</td>';
        $htmlTable .= '<td>' . $row["C_rateHour"] . '</td>';
        $htmlTable .= '<td>' . $row["C_chargInterp"] . '</td>';
        $htmlTable .= '<td>' . $row["C_travelTimeHour"] . '</td>';
        $htmlTable .= '<td>' . $row["C_travelTimeRate"] . '</td>';
        $htmlTable .= '<td>' . $row["C_chargeTravelTime"] . '</td>';
        $htmlTable .= '<td>' . $row["C_travelMile"] . '</td>';
        //$htmlTable .= '<td>' . $row["C_rateMile"] . '</td>';
        $htmlTable .= '<td>' . $row["C_chargeTravel"] . '</td>';
        $htmlTable .= '<td>' . $row["C_travelCost"] . '</td>';
        $htmlTable .= '<td>' . $row["C_admnchargs"] . '</td>';
        $htmlTable .= '<td>' . $withou_VAT_interp . '</td>';
        $htmlTable .= '<td>' . $shw_sub_total . '</td>';
        $htmlTable .= '<td>' . $shw_vat_percent . '</td>';
        $htmlTable .= '<td>' . $shw_total_cost . '</td>';
        $htmlTable .= '<td>' . $row["porder"] . '</td>';
        $htmlTable .= '</tr>';

        if ($row['orderCancelatoin'] == 1 && $row['has_cancelled_reason'] > 0) {
            $cancelled_date_time = date("d-m-Y", strtotime($row["canceled_date"])) . " " . date("H:i", strtotime($row["canceled_date"]));
            $htmlTable .= '<tr>';
            $htmlTable .= '<td colspan="22">#' . $i . '
					<strong>Cancellation Notes: </strong>
					<strong>Dated:</strong> ' . $cancelled_date_time . ', 
					<strong>Reason:</strong> ' . $row["canceled_reason"] . '</td>';
            $htmlTable .= '</tr>';
        }

        $i++;
    }

    if ($check_exists < 1) {
        $query_telep = "SELECT telephone.*, interpreter_reg.name, canceled_orders.cancel_type_id, 
        canceled_orders.cancel_reason_id, canceled_orders.canceled_by, canceled_orders.canceled_date, 
        canceled_orders.canceled_reason, (SELECT count(id) FROM canceled_orders WHERE job_id = telephone.id AND job_type = 2 AND telephone.orderCancelatoin = 1) as has_cancelled_reason
        FROM telephone 
        INNER JOIN interpreter_reg ON telephone.intrpName = interpreter_reg.id 
        LEFT JOIN canceled_orders ON canceled_orders.job_id = telephone.id AND canceled_orders.job_type = 2
        WHERE telephone.multInvoicNo = '$multInvoicNo' AND telephone.multInv_flag = 1
        ORDER BY telephone.assignDate ASC";
    } else {
        $query_telep = "SELECT mi.category_type, mi.assign_date as assignDate, mi.language_source as source, mi.reg_name as name, mi.inch_person as inchPerson, mi.client_ref as orgRef, mi.units as C_hoursWorkd, mi.price_per_unit as C_rateHour, mi.cost_of_intrp as C_chargInterp, mi.travel_time_duration as C_travelTimeHour, mi.travel_time_rate_per_hr as C_travelTimeRate, mi.cost_of_travel_time as C_chargeTravelTime, mi.mileage as C_travelMile, mi.cost_of_mileage as C_chargeTravel, mi.transport_cost as C_travelCost, mi.admin_cost as C_admnchargs, mi.other_expense as C_otherCharges, mi.po_order as porder, mi.orderCancelation as orderCancelatoin, mi.canceled_date_time as canceled_date, mi.canceled_reason, (SELECT count(id) FROM canceled_orders WHERE job_id = mi.main_job_id AND job_type = 2 AND mi.orderCancelation = 1) as has_cancelled_reason                            
        FROM mult_inv_items mi 
        WHERE mi.invoice_no = '" . $multInvoicNo . "' AND mi.category_type = 'Remote'
        ORDER BY mi.assign_date ASC";
    }

    /*$query_telep = "SELECT telephone.*, interpreter_reg.name  FROM telephone
	   inner join interpreter_reg on telephone.intrpName = interpreter_reg.id 
	   where telephone.deleted_flag = 0  and telephone.order_cancel_flag=0 and telephone.commit=0 and telephone.multInv_flag=0 and assignDate between '$search_2' and '$search_3' and telephone.orgName IN " . (!empty($p_org_ad) ? "($p_org_ad)" : "($comp_abrv_str)") . " order by assignDate Asc";*/

    $result_telep = mysqli_query($con, $query_telep);
    while ($row = mysqli_fetch_assoc($result_telep)) {
        $without_VAT_telp = $C_otherCharges_CallCharges = $row["C_otherCharges"];
        //$non_vat_tlep = $row["total_charges_comp"] - $row["C_otherCharges"];


        //$C_otherCharges_CallCharges = $row['C_otherCharges'];

        $telep_sub_total = $row['C_chargInterp'] + $C_otherCharges_CallCharges;
        $telep_vat_percent = (($telep_sub_total - $C_otherCharges_CallCharges) * 0.2);
        $telep_total_cost = $telep_sub_total + $telep_vat_percent;


        $grand_sub_total += $telep_sub_total;
        $grand_total_vat += $telep_vat_percent;

        $shw_telep_sub_total = ($telep_sub_total > 0) ? $misc->numberFormat_fun($telep_sub_total) : 0;
        $shw_telep_vat_percent = ($telep_vat_percent > 0) ? $misc->numberFormat_fun($telep_vat_percent) : 0;
        $shw_telep_total_cost = ($telep_total_cost > 0) ? $misc->numberFormat_fun($telep_total_cost) : 0;

        $htmlTable .= '<tr>';
        $htmlTable .= '<td>' . $i . '</td>';
        $htmlTable .= '<td>' . $misc->dated($row["assignDate"]) . '</td>';
        $htmlTable .= '<td>Remote</td>';
        $htmlTable .= '<td>' . $row["source"] . '</td>';
        $htmlTable .= '<td>' . $row["name"] . '</td>';
        $htmlTable .= '<td>' . $row["inchPerson"] . '</td>';
        $htmlTable .= '<td>' . $row["orgRef"] . '</td>';
        $htmlTable .= '<td>' . $row["C_hoursWorkd"] . '</td>';
        $htmlTable .= '<td>' . $row["C_rateHour"] . '</td>';
        $htmlTable .= '<td>' . $row['C_chargInterp'] . '</td>';
        $htmlTable .= '<td>N/A</td>';
        $htmlTable .= '<td>N/A</td>';
        $htmlTable .= '<td>N/A</td>';
        $htmlTable .= '<td>N/A</td>';
        //$htmlTable .= '<td>N/A</td>';
        $htmlTable .= '<td>N/A</td>';
        $htmlTable .= '<td>N/A</td>';
        $htmlTable .= '<td>N/A</td>';
        $htmlTable .= '<td>' . $C_otherCharges_CallCharges . '</td>';
        $htmlTable .= '<td>' . $shw_telep_sub_total . '</td>';
        $htmlTable .= '<td>' . $shw_telep_vat_percent . '</td>';
        $htmlTable .= '<td>' . $shw_telep_total_cost . '</td>';
        $htmlTable .= '<td>' . $row["porder"] . '</td>';
        $htmlTable .= '</tr>';

        if ($row['orderCancelatoin'] == 1 && $row['has_cancelled_reason'] > 0) {
            $cancelled_date_time = date("d-m-Y", strtotime($row["canceled_date"])) . " " . date("H:i", strtotime($row["canceled_date"]));
            $htmlTable .= '<tr>';
            $htmlTable .= '<td colspan="22">#' . $i . '
					<strong>Cancellation Notes: </strong>
					<strong>Dated:</strong> ' . $cancelled_date_time . ', 
					<strong>Reason:</strong> ' . $row["canceled_reason"] . '</td>';
            $htmlTable .= '</tr>';
        }

        $i++;
    }

    /*$query_trans = "SELECT translation.*, interpreter_reg.name  FROM translation
	   inner join interpreter_reg on translation.intrpName = interpreter_reg.id 
	   where translation.deleted_flag = 0 and translation.order_cancel_flag=0 and translation.commit=0 and translation.multInv_flag=0 and asignDate between '$search_2' and '$search_3' and translation.orgName IN " . (!empty($p_org_ad) ? "($p_org_ad)" : "($comp_abrv_str)") . " order by asignDate Asc";*/

    if ($check_exists < 1) {
        $query_trans = "SELECT translation.*, interpreter_reg.name, canceled_orders.cancel_type_id, 
        canceled_orders.cancel_reason_id, canceled_orders.canceled_by, canceled_orders.canceled_date, 
        canceled_orders.canceled_reason, (SELECT count(id) FROM canceled_orders WHERE job_id = translation.id AND job_type = 3 AND translation.orderCancelatoin = 1) as has_cancelled_reason  
        FROM translation 
        INNER JOIN interpreter_reg ON translation.intrpName = translation.id 
        LEFT JOIN canceled_orders ON canceled_orders.job_id = translation.id AND canceled_orders.job_type = 3
        WHERE translation.multInvoicNo = '$multInvoicNo' AND translation.multInv_flag = 1
        ORDER BY translation.asignDate ASC";
    } else {
        $query_trans = "SELECT mi.category_type, mi.assign_date as asignDate, mi.language_source as source, mi.reg_name as name, mi.inch_person as orgContact, mi.client_ref as orgRef, mi.units as C_numberUnit, mi.price_per_unit as C_rpU, mi.cost_of_intrp as C_chargInterp, mi.travel_time_duration as C_travelTimeHour, mi.travel_time_rate_per_hr as C_travelTimeRate, mi.cost_of_travel_time as C_chargeTravelTime, mi.mileage as C_travelMile, mi.cost_of_mileage as C_chargeTravel, mi.transport_cost as C_travelCost, mi.admin_cost as C_admnchargs, mi.other_expense as C_otherCharg, mi.po_order as porder, mi.orderCancelation as orderCancelatoin, mi.canceled_date_time	as canceled_date, mi.canceled_reason, (SELECT count(id) FROM canceled_orders WHERE job_id = mi.main_job_id AND job_type = 3 AND mi.orderCancelation = 1) as has_cancelled_reason                            
        FROM mult_inv_items mi 
        WHERE mi.invoice_no = '" . $multInvoicNo . "' AND mi.category_type = 'Translation'
        ORDER BY mi.assign_date ASC";
    }

    $result_trans = mysqli_query($con, $query_trans);
    while ($row = mysqli_fetch_assoc($result_trans)) {
        $withou_VAT_trans = $row["C_otherCharg"];
        //$non_vat_trans = $row["total_charges_comp"] - $row["C_otherCharg"];

        $trans_sub_total = ($row["C_numberUnit"] * $row["C_rpU"]) + $row["C_otherCharg"]; //$row["total_units"];
        $trans_vat_percent = ($trans_sub_total - $row["C_otherCharg"]) * 0.2;
        $trans_total_cost = $trans_sub_total + $trans_vat_percent;

        $grand_sub_total += $trans_sub_total;
        $grand_total_vat += $trans_vat_percent;
        $grand_non_vat += $withou_VAT_trans;

        $shw_trans_sub_total = ($trans_sub_total > 0) ? $misc->numberFormat_fun($trans_sub_total) : 0;
        $shw_trans_vat_percent = ($trans_vat_percent > 0) ? $misc->numberFormat_fun($trans_vat_percent) : 0;
        $shw_trans_total_cost = ($trans_total_cost > 0) ? $misc->numberFormat_fun($trans_total_cost) : 0;

        $htmlTable .= '<tr>';
        $htmlTable .= '<td>' . $i . '</td>';
        $htmlTable .= '<td>' . $misc->dated($row["asignDate"]) . '</td>';
        $htmlTable .= '<td>Translation</td>';
        $htmlTable .= '<td>' . $row["source"] . '</td>';
        $htmlTable .= '<td>' . $row["name"] . '</td>';
        $htmlTable .= '<td>' . $row["orgContact"] . '</td>';
        $htmlTable .= '<td>' . $row["orgRef"] . '</td>';
        $htmlTable .= '<td>' . $row["C_numberUnit"] . '</td>';
        $htmlTable .= '<td>' . $row["C_rpU"] . '</td>';
        $htmlTable .= '<td>' . $trans_sub_total . '</td>';
        $htmlTable .= '<td>N/A</td>';
        $htmlTable .= '<td>N/A</td>';
        $htmlTable .= '<td>N/A</td>';
        $htmlTable .= '<td>N/A</td>';
        //$htmlTable .= '<td>N/A</td>';
        $htmlTable .= '<td>N/A</td>';
        $htmlTable .= '<td>N/A</td>';
        $htmlTable .= '<td>N/A</td>';
        $htmlTable .= '<td>' . $row["C_otherCharg"] . '</td>';
        $htmlTable .= '<td>' . $shw_trans_sub_total . '</td>';
        $htmlTable .= '<td>' . $shw_trans_vat_percent . '</td>';
        $htmlTable .= '<td>' . $shw_trans_total_cost . '</td>';
        $htmlTable .= '<td>' . $row["porder"] . '</td>';
        $htmlTable .= '</tr>';

        if ($row['orderCancelatoin'] == 1 && $row['has_cancelled_reason'] > 0) {
            $cancelled_date_time = date("d-m-Y", strtotime($row["canceled_date"])) . " " . date("H:i", strtotime($row["canceled_date"]));
            $htmlTable .= '<tr>';
            $htmlTable .= '<td colspan="22">#' . $i . '
					<strong>Cancellation Notes: </strong>
					<strong>Dated:</strong> ' . $cancelled_date_time . ', 
					<strong>Reason:</strong> ' . $row["canceled_reason"] . '</td>';
            $htmlTable .= '</tr>';
        }

        $i++;
    }
    $g_total_all = $g_total_interp  + $g_total_telep + $g_total_trans;
    $vat_all = ($g_total_vat_interp + $g_total_vat_telep + $g_total_vat_trans);
    $grand_total = $C_otherexpns + ($g_total_interp  + $g_total_telep + $g_total_trans) + $vat_all;
    $total_invoice = $grand_sub_total + $grand_total_vat;

    $htmlTable .= '<tfoot>
		<tr class="summary">
			<td colspan="20" align="right" style="text-right:right;"><b>Total Cost before VAT</b></td>
			<td colspan="2"><b>' . $misc->numberFormat_fun($grand_sub_total - $grand_non_vat) . '</b></td>
		</tr>

		<tr class="summary">
			<td colspan="20" align="right style="text-right:right;"b>VAT @20%</b></td>
			<td colspan="2"><b>' . $misc->numberFormat_fun($grand_total_vat) . '</b></td>
		</tr>

        <tr class="summary">
			<td colspan="20" align="right" style="text-right:right;"><b>Total Non-VAT Cost</b></td>
			<td colspan="2"><b>' . $misc->numberFormat_fun($grand_non_vat) . '</b></td>
		</tr>

		<tr class="summary">
			<td colspan="20" align="right" style="text-right:right;"><b>Total Invoice</b></td>
			<td colspan="2"><b>' . $misc->numberFormat_fun($total_invoice) . '</b></td>
		</tr>
	</tfoot>';

    $htmlTable .= '</TABLE>';
    $htmlTable .= '<br><br> Please make all cheques payable to Language Services UK Limited for BACS payment Sort Code 20-13-34 Account Number 33161234.
Company                                                     Registration Number 7760366 VAT Number 198427362
Thank You For Business With Us
<br><br>
Please pay your invoice within 21 days from the date of invoice. <u>Compensation fee and interest charges at 1.5% per day will be added to invoice total in accordance with the "Late Payment of Commercial Debts Interests Act 1998"</u> if no payment was made within reasonable time frame
';

    list($a, $b) = explode('.', basename(__FILE__));

    header("Content-Type: application/xls");
    header("Content-Disposition: attachment; filename=" . $a . ".xls");
    echo $htmlTable;
}
