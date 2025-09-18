<?php
include '../../db.php';
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

$multi_invoice_info = $acttObj->read_specific("*", "mult_inv", "m_inv = '$multInvoicNo' ");
if ($multi_invoice_info) {
    $id = $multi_invoice_info['id'];
}

//get company details
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
        $query = "SELECT mi.category_type, mi.assign_date as assignDate, mi.language_source as source, mi.reg_name as name, mi.inch_person as inchPerson, mi.client_ref as orgRef, mi.units as C_hoursWorkd, mi.price_per_unit as C_rateHour, mi.cost_of_intrp as C_chargInterp, mi.travel_time_duration as C_travelTimeHour, mi.travel_time_rate_per_hr as C_travelTimeRate, mi.cost_of_travel_time as C_chargeTravelTime, mi.mileage as C_travelMile, mi.cost_of_mileage as C_chargeTravel, mi.transport_cost as C_travelCost, mi.admin_cost as C_admnchargs, mi.other_expense as C_otherCost, mi.po_order as porder, mi.orderCancelation as orderCancelatoin, mi.canceled_date_time as canceled_date, mi.canceled_reason, (SELECT count(id) FROM canceled_orders WHERE job_id = mi.main_job_id AND job_type = 1 AND mi.orderCancelation = 1) as has_cancelled_reason
        FROM mult_inv_items mi 
        WHERE mi.invoice_no = '" . $multInvoicNo . "' AND mi.category_type = 'Face to Face'
        ORDER BY mi.assign_date ASC";
    }
    $result = mysqli_query($con, $query);

    // Include the main TCPDF library (search for installation path).
    require_once('tcpdf_include.php');
    // create new PDF document
    $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

    // set document information
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetSubject('TCPDF Tutorial');

    // set default header data
    include 'rip_header.php';
    include 'rip_footer.php';

    // set header and footer fonts
    $pdf->setHeaderFont(array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
    $pdf->setFooterFont(array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

    // set default monospaced font
    $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

    // set margins --- PDF_MARGIN_LEFT -- PDF_MARGIN_RIGHT
    $pdf->SetMargins(8, PDF_MARGIN_TOP, 10);
    $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
    $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

    // set auto page breaks
    $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

    // set image scale factor
    $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

    // set some language-dependent strings (optional)
    if (@file_exists(dirname(__FILE__) . '/lang/eng.php')) {
        require_once(dirname(__FILE__) . '/lang/eng.php');
        $pdf->setLanguageArray($l);
    }

    // ---------------------------------------------------------

    // set font
    $pdf->SetFont('helvetica', 'B', 12);

    // add a page
    $pdf->AddPage('L');
    $pdf->SetFont('helvetica', '', 8);

    $invoice_to = "";
    for ($kj = 0; $kj < count($name); $kj++) {
        $invoice_to .= "<p><br>Invoice To: " . $name[$kj] . "<br/>" . $buildingName[$kj] . "<br/>" . $line1[$kj] . "<br/>" . $line2[$kj] . "<br/>" . $streetRoad[$kj] . "<br/>" . $city[$kj] . "<br/>" . $postCode[$kj] . "<br/></p>";
    }

    $new_credit_note = $acttObj->read_specific("id , CONCAT(DATE_FORMAT(posted_on,'%y%m'),'_',LPAD(COUNT(id), 2, '0')) as credit_note_no", "credit_notes_income_invoices", "income_invoice_id = " . $id . " AND tbl = 'mult_inv'");

    if ((isset($new_credit_note['id']) && !empty($new_credit_note['id']))) {
        $credit_note_number = $new_credit_note['credit_note_no'];
    } else {
        $credit_note_number = "Not created yet";
    }

    // Table with rowspans and THEAD
    $tbl = <<<EOD
<style>
table {border-collapse: collapse; width:670px;}
th {border: 1px solid #999; padding: 0.5rem;text-align: left;background-color:#039; color:#FFF;font-weight:bold; font-size: 8px;}
td {border: 1px solid #999; padding: 0.5rem;text-align: left; font-size: 8px;}
</style>
EOD;

    $tbl .= <<<EOD
<div>
<span align="right"> Date: {$misc->sys_date()} </span>
EOD;

    if ((!isset($new_credit_note['id']) && empty($new_credit_note['id']))) {
        $tbl .= <<<EOD
            <h2 style="text-decoration:underline; text-align:center;">Invoice: ({$multInvoicNo})</h2>
        EOD;
    } else {
        $tbl .= <<<EOD
           <h2 style="text-decoration:underline; text-align:center;">Credit Note: <strong>{$credit_note_number}</strong></h2>
           <p style="text-align:center;">Invoice: ({$multInvoicNo})</p>
        EOD;
    }

    $tbl .= <<<EOD
$invoice_to
<p>Date Range: {$misc->dated($search_2)} to {$misc->dated($search_3)} </p>
</div>
EOD;
    $tbl .= <<<EOD
<table width="100%" cellpadding="2" id="collective_table_dt">
<thead>
<tr>
 	<th style="width:20px;">#</th>
    <th>Assignment Date</th>
    <th>Type</th>
    <th>Language</th>
    <th>Interpreter</th>
    <th>Booking Person</th>
    <th>Client Ref</th>
    <th>Length</th>
    <th>Price Per Unit (£)</th>
    <th>Cost of Interpreting (£)</th>
    <th>Travel Time Duration in Hours</th>
    <th>Travel Time Rate per Hour (£)</th>
    <th>Cost of Travel Time (£)</th>
    <th>Mileage</th>
    <!--th>Price per Mile (£)</th-->
    <th>Cost of Mileage (£)</th>
    <th>Public Transport Cost (£)</th>
    <th>Admin Cost</th>
    <th>Other Expenses Non-Vatable (£)</th>
    <th>Subtotal  (£)</th>
    <th>VAT  (20%) (£)</th>
    <th>Total Cost (£)</th>
    <th width="8%">P. Order</th>
 </tr>

</thead> 
<tbody>
EOD;

    while ($row = mysqli_fetch_assoc($result)) {
        $g_total_interp = $row["total_charges_comp"] + $g_total_interp;
        $g_total_vat_interp = $row["total_charges_comp"] * $row["cur_vat"] + $g_total_vat_interp;
        $C_admnchargs_interp = $row["C_admnchargs"] + $C_admnchargs_interp;
        $C_otherexpns = $row["C_otherexpns"] + $C_otherexpns;
        $C_chargeTravelTime = $row["C_chargeTravelTime"] + $C_chargeTravelTime;

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

        $tbl .= <<<EOD
		<tr>
			<td style="width:20px;">{$i}</td>
			<td>{$misc->dated($row["assignDate"])}</td>
			<td>Face to Face</td>
			<td>{$row["source"]}</td>
			<td>{$row["name"]}</td>
			<td>{$row["inchPerson"]}</td>
			<td>{$row["orgRef"]}</td>
			<td>{$row["C_hoursWorkd"]}</td>
			<td>{$row["C_rateHour"]}</td>
			<td>{$row["C_chargInterp"]}</td>
			<td>{$row["C_travelTimeHour"]}</td>
			<td>{$row["C_travelTimeRate"]}</td>
			<td>{$row["C_chargeTravelTime"]}</td>
			<td>{$row["C_travelMile"]}</td>
			<!--td>{$row["C_rateMile"]}</td-->
			<td>{$row["C_chargeTravel"]}</td>
			<td>{$row["C_travelCost"]}</td>
			<td>{$row["C_admnchargs"]}</td>
			<td>{$withou_VAT_interp}</td>
			<td>{$shw_sub_total}</td>
			<td>{$shw_vat_percent}</td>
			<td>{$shw_total_cost}</td>
			<td width="8%">{$row["porder"]}</td>
    	</tr>
EOD;

        if ($row['orderCancelatoin'] == 1 && $row['has_cancelled_reason'] > 0) {
            $cancelled_date_time = date("d-m-Y", strtotime($row["canceled_date"])) . " " . date("H:i", strtotime($row["canceled_date"]));
            $tbl .= <<<EOD
				<tr>
					<td colspan="22">#{$i}.
						<strong>Cancellation Notes: </strong> 
						<strong>Dated:</strong> {$cancelled_date_time}, 
						<strong>Reason:</strong> {$row["canceled_reason"]}
					</td>
				</tr>
			EOD;
        }

        $i++;
    }

    //telephone:
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

        $tbl .= <<<EOD
    <tr>
      	<td style="width:20px;">{$i}</td>
		<td>{$misc->dated($row["assignDate"])}</td>
		<td>Remote</td>
		<td>{$row["source"]}</td>
		<td>{$row["name"]}</td>
		<td>{$row["inchPerson"]}</td>
		<td>{$row["orgRef"]}</td>
		<td>{$row["C_hoursWorkd"]}</td>
		<td>{$row["C_rateHour"]}</td>
		<td>{$row["C_chargInterp"]}</td>
		<td>N/A</td>
		<td>N/A</td>
		<td>N/A</td>
		<td>N/A</td>
		<!--td>N/A</td-->
		<td>N/A</td>
		<td>N/A</td>
		<td>N/A</td>
		<td>{$C_otherCharges_CallCharges}</td>
		<td>{$shw_telep_sub_total}</td>
		<td>{$shw_telep_vat_percent}</td>
		<td>{$shw_telep_total_cost}</td>
		<td width="8%">{$row["porder"]}</td>
    </tr>
EOD;

        if ($row['orderCancelatoin'] == 1 && $row['has_cancelled_reason'] > 0) {
            $cancelled_date_time = date("d-m-Y", strtotime($row["canceled_date"])) . " " . date("H:i", strtotime($row["canceled_date"]));
            $tbl .= <<<EOD
				<tr>
					<td colspan="22">#{$i}.
						<strong>Cancellation Notes: </strong> 
						<strong>Dated:</strong> {$cancelled_date_time}, 
						<strong>Reason:</strong> {$row["canceled_reason"]}
					</td>
				</tr>
			EOD;
        }

        $i++;
    }

    //translation:
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


        $tbl .= <<<EOD
    <tr>
      	<td style="width:20px;">{$i}</td>
		<td>{$misc->dated($row["asignDate"])}</td>
		<td>Translation</td>
		<td>{$row["source"]}</td>
		<td>{$row["name"]}</td>
		<td>{$row["orgContact"]}</td>
		<td>{$row["orgRef"]}</td>	
		<td>{$row["C_numberUnit"]}</td>
		<td>{$row["C_rpU"]}</td>
		<td>{$trans_sub_total}</td>
		<td>N/A</td>
		<td>N/A</td>
		<td>N/A</td>
		<td>N/A</td>
		<!--td>N/A</td-->
		<td>N/A</td>
		<td>N/A</td>
		<td>N/A</td>
		<td>{$row["C_otherCharg"]}</td>
		<td>{$shw_trans_sub_total}</td>
		<td>{$shw_trans_vat_percent}</td>
		<td>{$shw_trans_total_cost}</td>
		<td width="8%">{$row["porder"]}</td>
    </tr>
	 
EOD;

        if ($row['orderCancelatoin'] == 1 && $row['has_cancelled_reason'] > 0) {
            $cancelled_date_time = date("d-m-Y", strtotime($row["canceled_date"])) . " " . date("H:i", strtotime($row["canceled_date"]));
            $tbl .= <<<EOD
				<tr>
					<td colspan="22">#{$i}.
						<strong>Cancellation Notes: </strong> 
						<strong>Dated:</strong> {$cancelled_date_time}, 
						<strong>Reason:</strong> {$row["canceled_reason"]}
					</td>
				</tr>
			EOD;
        }

        $i++;
    }

    $g_total_all = $g_total_interp  + $g_total_telep + $g_total_trans;
    $vat_all = ($g_total_vat_interp + $g_total_vat_telep + $g_total_vat_trans);
    $grand_total = $C_otherexpns + ($g_total_interp  + $g_total_telep + $g_total_trans) + $vat_all;


    $total_invoice = $grand_sub_total + $grand_total_vat;

    $tbl .= <<<EOD

</tbody>
<tfoot>
		<tr class="summary">
			<td colspan="20" align="right"><b>Total Cost before VAT</b></td>
			<td colspan="2"><b>{$misc->numberFormat_fun($grand_sub_total -$grand_non_vat)}</b></td>
		</tr>

		<tr class="summary">
			<td colspan="20" align="right"><b>VAT @20%</b></td>
			<td colspan="2"><b>{$misc->numberFormat_fun($grand_total_vat)}</b></td>
		</tr>

        <tr class="summary">
			<td colspan="20" align="right"><b>Total Non-VAT Cost</b></td>
			<td colspan="2"><b>{$misc->numberFormat_fun($grand_non_vat)}</b></td>
		</tr>

		<tr class="summary">
			<td colspan="20" align="right"><b>Total Invoice</b></td>
			<td colspan="2"><b>{$misc->numberFormat_fun($total_invoice)}</b></td>
		</tr>
	</tfoot>

</table>

EOD;
    $tbl .= <<<EOD
<br><br>
Please make all cheques payable to Language Services UK Limited for BACS payment Sort Code 20-13-34 Account Number 33161234.
Company                                                     Registration Number 7760366 VAT Number 198427362
Thank You For Business With Us
<br><br>
Please pay your invoice within 21 days from the date of invoice. <u>Compensation fee and interest charges at 1.5% per day will be added to invoice total in accordance with the "Late Payment of Commercial Debts Interests Act 1998"</u> if no payment was made within reasonable time frame
EOD;


    // if ($proceed == 'Yes') {
    //     //.....................................................................................	
    //     $acttObj->editFun_comp('mult_inv', 'mult_amount', $total_invoice, 'm_inv', $multInvoicNo);
    //     $acttObj->editFun_comp('mult_inv', 'from_date', $search_2, 'm_inv', $multInvoicNo);
    //     $acttObj->editFun_comp('mult_inv', 'to_date', $search_3, 'm_inv', $multInvoicNo);
    //     //......................................................................................
    // }

    $pdf->writeHTML($tbl, true, false, false, false, '');

    // -----------------------------------------------------------------------------

    //Close and output PDF document
    list($a, $b) = explode('.', basename(__FILE__));
    $pdf->Output($a . '.pdf', 'I');
    //============================================================+
    // END OF FILE
    //==========================================================EXCEL FORMAT=========================================================+

}//if company spec
