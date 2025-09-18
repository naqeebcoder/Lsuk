<?php
include '../../db.php';
include_once('../../class.php');
include_once('../../inc_functions.php');

$excel = @$_GET['excel'];
$proceed = @$_GET['proceed'];

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
$multInvoicNo = '';
if (!empty($search_1) || !empty($p_org)) {
	//get company details
	$query = "SELECT  * FROM comp_reg	where id IN " . (!empty($p_org) ? "($p_org)" : "($search_1)");
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
}

//........................................//\\//\\Invoice #//\\//\\//\\...........................................//
$comp_abrv_str = "";
if (!empty($comp_abrv)) {
	$comp_abrv_str = implode("_", $comp_abrv);
}
if ($proceed == 'Yes') {

	//gen multi invoice, set in mult_inv table
	$nmbr = $acttObj->get_id('mult_inv');
	if ($nmbr == NULL) {
		$nmbr = 0;
	}
	$new_nmbr = str_pad($nmbr, 5, "0", STR_PAD_LEFT);
	$multInvoicNo = 'LSUK' . $new_nmbr . '' . $comp_abrv_str;
	$maxId = $nmbr;
	$acttObj->editFun('mult_inv', $maxId, 'm_inv', $multInvoicNo);

	$comp_ids = !empty($p_org) ? $p_org : $search_1;
	$acttObj->editFun('mult_inv', $maxId, 'comp_id', $comp_ids);
	$acttObj->editFun('mult_inv', $maxId, 'comp_name', implode(",", $name));
	$acttObj->editFun('mult_inv', $maxId, 'comp_abrv', implode(",", $comp_abrv));
	$due = $misc->add_in_date(date("Y-m-d"), 15);
	$acttObj->editFun('mult_inv', $maxId, 'due_date', $due);
	$acttObj->insert("daily_logs", array("action_id" => 23, "user_id" => $_SESSION['userId'], "details" => "Mult.Invoice ID: " . $maxId));
}

//$acttObj->editFun($table,$edit_id,'invoiceNo',$invoice);
//.....................................................//\\//\\//\\//\\//\\//\\//\\//\\//\\..........................
$comp_abrv_str = implode(",", $comp_abrv_q);
if (!empty($search_1) || !empty($p_org)) {
	//company specified:
	//interpreter:

	$query = "SELECT interpreter.*, interpreter_reg.name, canceled_orders.cancel_type_id, canceled_orders.cancel_reason_id, 
	canceled_orders.canceled_by, canceled_orders.canceled_date, canceled_orders.canceled_reason,
	(SELECT count(id) FROM canceled_orders WHERE job_id = interpreter.id AND job_type = 1 AND interpreter.orderCancelatoin = 1) as has_cancelled_reason
	FROM interpreter 
	INNER JOIN interpreter_reg ON interpreter.intrpName = interpreter_reg.id 
	LEFT JOIN canceled_orders ON canceled_orders.job_id = interpreter.id AND canceled_orders.job_type = 1 
	WHERE interpreter.deleted_flag = 0 AND interpreter.order_cancel_flag = 0 AND interpreter.commit = 0 AND interpreter.multInv_flag = 0 
	AND interpreter.assignDate BETWEEN '$search_2' AND '$search_3' AND interpreter.orgName IN " . (!empty($p_org_ad) ? "($p_org_ad)" : "($comp_abrv_str)") . " 
	ORDER BY interpreter.assignDate ASC"; //orderCancelatoin
	// echo $query;die();exit();
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
<h2 style="text-decoration:underline; text-align:center">Invoice ({$multInvoicNo})</h2>
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
		$vat_percent = ($sub_total - $withou_VAT_interp) * $row['cur_vat'];
		//$vat_percent = ($row['C_cur_vat']) ? $row['C_cur_vat'] : '0.2';
		$total_cost = $sub_total + $vat_percent;

		$grand_sub_total += $sub_total;
		$grand_total_vat += $vat_percent;
		$grand_non_vat += $withou_VAT_interp; // other expenses

		$shw_sub_total = ($sub_total > 0) ? $misc->numberFormat_fun($sub_total) : 0;
		$shw_vat_percent = ($vat_percent > 0) ? $misc->numberFormat_fun($vat_percent) : 0;
		$shw_total_cost = ($total_cost > 0) ? $misc->numberFormat_fun($total_cost) : 0;

		if ($proceed == 'Yes') {

			$acttObj->editFun('interpreter', $row['id'], 'multInvoicNo', $multInvoicNo);
			$acttObj->editFun('interpreter', $row['id'], 'multInv_flag', 1);

			// keeping history of mult invoice items --- for view, pdf and print if mult invoice is canceled or credit note is generated
			$insert_data = array(
				'invoice_no' => $multInvoicNo,
				'main_job_id' => $row['id'],
				'category_type' => 'Face to Face',
				'assign_date' => $row['assignDate'],
				'language_source' => $row['source'],
				'reg_name' => mysqli_real_escape_string($con, $row['name']),
				'inch_person' => mysqli_real_escape_string($con, $row['inchPerson']),
				'client_ref' => mysqli_real_escape_string($con, $row['orgRef']),
				'units' => $row['C_hoursWorkd'],
				'price_per_unit' => $row['C_rateHour'],
				'cost_of_intrp' => $row['C_chargInterp'],
				'travel_time_duration' => $row['C_travelTimeHour'],
				'travel_time_rate_per_hr' => $row['C_travelTimeRate'],
				'cost_of_travel_time' => $row['C_chargeTravelTime'],
				'mileage' => $row['C_travelMile'],
				'cost_of_mileage' => $row['C_chargeTravel'],
				'transport_cost' => $row['C_travelCost'],
				'admin_cost' => $row['C_admnchargs'],
				'other_expense' => $withou_VAT_interp,
				'sub_total' => $sub_total,
				'vat' => $vat_percent,
				'total_cost' => $total_cost,
				'po_order' => $row['porder'],
				'orderCancelation' => $row['orderCancelatoin'],
				'canceled_date_time' => $row['canceled_date'],
				'canceled_reason' => mysqli_real_escape_string($con, $row['canceled_reason'])
			);

			$acttObj->insert('mult_inv_items', $insert_data, false);

			if ($total_cost > 0) {
				// Insertion in Comp_credit as debit amount
				$insert_values = array(
					'invoiceNo' => $multInvoicNo,
					'porder' => $row['porder'],
					'orgName' => $row['orgName'],
					'mode' => 'interpreter',
					'debit' => $total_cost,
					'debit_date' => date('Y-m-d'),
					'dated' => date('Y-m-d'),
					'mult_inv_flag' => 1
				);

				$acttObj->insert('comp_credit', $insert_values, false);

				// Insertion in bz_credit as debit amount
				$bz_insert_values = array(
					'invoiceNo' => $multInvoicNo,
					'orgName' => $row['orgName'],
					'mode' => 'interpreter',
					'bz_debit' => $total_cost,
					'bz_debit_date' => date('Y-m-d'),
					'dated' => date('Y-m-d'),
					'mult_inv_flag' => 1
				);

				$acttObj->insert('bz_credit', $bz_insert_values, false);
			}
		}


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
	$query_telep = "SELECT telephone.*, interpreter_reg.name, 
	canceled_orders.cancel_type_id, canceled_orders.cancel_reason_id, canceled_orders.canceled_by, 
	canceled_orders.canceled_date, canceled_orders.canceled_reason,
	(SELECT count(id) FROM canceled_orders WHERE job_id = telephone.id AND job_type = 2 AND telephone.orderCancelatoin = 1) as has_cancelled_reason
	FROM telephone 
	INNER JOIN interpreter_reg ON telephone.intrpName = interpreter_reg.id 
	LEFT JOIN canceled_orders ON canceled_orders.job_id = telephone.id AND canceled_orders.job_type = 2
	WHERE telephone.deleted_flag = 0  AND telephone.order_cancel_flag = 0 AND 
	telephone.commit = 0 AND telephone.multInv_flag = 0 AND
	telephone.assignDate BETWEEN '$search_2' AND '$search_3' AND
	telephone.orgName IN " . (!empty($p_org_ad) ? "($p_org_ad)" : "($comp_abrv_str)") . " 
	ORDER BY telephone.assignDate ASC";

	$result_telep = mysqli_query($con, $query_telep);
	while ($row = mysqli_fetch_assoc($result_telep)) {
		$g_total_telep = $row["total_charges_comp"] + $g_total_telep;
		$g_total_vat_telep = $row["total_charges_comp"] * $row["cur_vat"] + $g_total_vat_telep;
		$non_vat_tlep = $row["total_charges_comp"] - $row["C_otherCharges"] + $non_vat_tlep;

		if ($proceed == 'Yes') {
			$acttObj->editFun('telephone', $row['id'], 'multInvoicNo', $multInvoicNo);
			$C_admnchargs_telep = $row["C_admnchargs"] + $C_admnchargs_telep;
			$acttObj->editFun('telephone', $row['id'], 'multInv_flag', 1);
		}

		$withou_VAT_telp = $row["C_otherCharges"];
		$non_vat_tlep = $row["total_charges_comp"] - $row["C_otherCharges"];


		$C_otherCharges_CallCharges = $row['C_callcharges'] + $row['C_otherCharges'];

		$telep_sub_total = $row['C_chargInterp'] + $C_otherCharges_CallCharges;
		$telep_vat_percent = (($telep_sub_total - $C_otherCharges_CallCharges) * $row['cur_vat']);
		$telep_total_cost = $telep_sub_total + $telep_vat_percent;


		$grand_sub_total += $telep_sub_total;
		$grand_total_vat += $telep_vat_percent;
		$grand_non_vat += $C_otherCharges_CallCharges;

		$shw_telep_sub_total = ($telep_sub_total > 0) ? $misc->numberFormat_fun($telep_sub_total) : 0;
		$shw_telep_vat_percent = ($telep_vat_percent > 0) ? $misc->numberFormat_fun($telep_vat_percent) : 0;
		$shw_telep_total_cost = ($telep_total_cost > 0) ? $misc->numberFormat_fun($telep_total_cost) : 0;

		if ($proceed == 'Yes') {

			if ($telep_total_cost > 0) {
				// Insertion in Comp_credit as debit amount
				$insert_values = array(
					'invoiceNo' => $multInvoicNo,
					'porder' => $row['porder'],
					'orgName ' => $row['orgName'],
					'mode' => 'telephone',
					'debit' => $telep_total_cost,
					'debit_date' => date('Y-m-d'),
					'dated' => date('Y-m-d'),
					'mult_inv_flag' => 1
				);

				$acttObj->insert('comp_credit', $insert_values, false);

				// Insertion in bz_credit as debit amount
				$bz_insert_values = array(
					'invoiceNo' => $multInvoicNo,
					'orgName ' => $row['orgName'],
					'mode' => 'telephone',
					'bz_debit' => $telep_total_cost,
					'bz_debit_date' => date('Y-m-d'),
					'dated' => date('Y-m-d'),
					'mult_inv_flag' => 1
				);

				$acttObj->insert('bz_credit', $bz_insert_values, false);
			}

			$insert_data = array(
				'invoice_no' => $multInvoicNo,
				'main_job_id' => $row['id'],
				'category_type' => "Remote",
				'assign_date' => $row["assignDate"],
				'language_source' => $row["source"],
				'reg_name' => mysqli_real_escape_string($con, $row["name"]),
				'inch_person' => mysqli_real_escape_string($con, $row["inchPerson"]),
				"client_ref" => mysqli_real_escape_string($con, $row["orgRef"]),
				"units" => $row["C_hoursWorkd"],
				"price_per_unit" => $row["C_rateHour"],
				"cost_of_intrp" => $row["C_chargInterp"],
				"travel_time_duration" => "N/A",
				"travel_time_rate_per_hr" => "N/A",
				"cost_of_travel_time" => "N/A",
				"mileage" => "N/A",
				"cost_of_mileage" => "N/A",
				"transport_cost" => "N/A",
				"admin_cost" => "N/A",
				"other_expense" => $C_otherCharges_CallCharges,
				"sub_total" => $telep_sub_total,
				"vat" => $telep_vat_percent,
				"total_cost" => $telep_total_cost,
				"po_order" => $row["porder"],
				"orderCancelation" => $row['orderCancelatoin'],
				"canceled_date_time" => $row["canceled_date"],
				"canceled_reason" => mysqli_real_escape_string($con, $row["canceled_reason"])
			);
			$acttObj->insert('mult_inv_items', $insert_data, false);
		}

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
	$query_trans = "SELECT translation.*, interpreter_reg.name, 
	canceled_orders.cancel_type_id, canceled_orders.cancel_reason_id, canceled_orders.canceled_by, 
	canceled_orders.canceled_date, canceled_orders.canceled_reason,
	(SELECT count(id) FROM canceled_orders WHERE job_id = translation.id AND job_type = 3 AND translation.orderCancelatoin = 1) as has_cancelled_reason
	FROM translation
	INNER JOIN interpreter_reg on translation.intrpName = interpreter_reg.id 
	LEFT JOIN canceled_orders ON canceled_orders.job_id = translation.id AND canceled_orders.job_type = 3
	WHERE translation.deleted_flag = 0 AND translation.order_cancel_flag=0 AND
	translation.commit=0 and translation.multInv_flag=0 and 
	translation.asignDate BETWEEN '$search_2' AND '$search_3' AND 
	translation.orgName IN " . (!empty($p_org_ad) ? "($p_org_ad)" : "($comp_abrv_str)") . " 
	ORDER BY translation.asignDate ASC";

	$result_trans = mysqli_query($con, $query_trans);
	while ($row = mysqli_fetch_assoc($result_trans)) {
		$g_total_trans = $row["total_charges_comp"] + $g_total_trans;
		$g_total_vat_trans = $row["total_charges_comp"] * $row["cur_vat"] + $g_total_vat_trans;
		$C_admnchargs_trans = $row["C_admnchargs"] + $C_admnchargs_trans;
		$non_vat_trans = $row["total_charges_comp"] - $row["C_otherCharg"] + $non_vat_trans;

		if ($proceed == 'Yes') {
			$acttObj->editFun('translation', $row['id'], 'multInvoicNo', $multInvoicNo);
			$acttObj->editFun('translation', $row['id'], 'multInv_flag', 1);
		}

		if ($proceed == 'Cancel') {
			$acttObj->editFun('translation', $row['id'], 'multInvoicNo', '');
			$acttObj->editFun('translation', $row['id'], 'multInv_flag', 0);
			$acttObj->del_comp('mult_inv', 'm_inv', $row['multInvoicNo']);
		}

		$withou_VAT_trans = $row["C_otherCharg"];
		$non_vat_trans = $row["total_charges_comp"] - $row["C_otherCharg"];

		$trans_sub_total = ($row["C_numberUnit"] * $row["C_rpU"]) + $row["C_otherCharg"]; //$row["total_units"];
		$trans_vat_percent = ($trans_sub_total - $row["C_otherCharg"]) * $row['cur_vat'];
		$trans_total_cost = $trans_sub_total + $trans_vat_percent;

		$grand_sub_total += $trans_sub_total;
		$grand_total_vat += $trans_vat_percent;
		$grand_non_vat += $withou_VAT_trans;

		$shw_trans_sub_total = ($trans_sub_total > 0) ? $misc->numberFormat_fun($trans_sub_total) : 0;
		$shw_trans_vat_percent = ($trans_vat_percent > 0) ? $misc->numberFormat_fun($trans_vat_percent) : 0;
		$shw_trans_total_cost = ($trans_total_cost > 0) ? $misc->numberFormat_fun($trans_total_cost) : 0;

		if ($proceed == 'Yes') {

			if ($trans_total_cost > 0) {
				// Insertion in Comp_credit as debit amount
				$insert_values = array(
					'invoiceNo' => $multInvoicNo,
					'porder' => $row['porder'],
					'orgName ' => $row['orgName'],
					'mode' => 'translation',
					'debit' => $trans_total_cost,
					'debit_date' => date('Y-m-d'),
					'dated' => date('Y-m-d'),
					'mult_inv_flag' => 1
				);

				$acttObj->insert('comp_credit', $insert_values, false);

				// Insertion in bz_credit as debit amount
				$bz_insert_values = array(
					'invoiceNo' => $multInvoicNo,
					'orgName ' => $row['orgName'],
					'mode' => 'translation',
					'bz_debit' => $trans_total_cost,
					'bz_debit_date' => date('Y-m-d'),
					'dated' => date('Y-m-d'),
					'mult_inv_flag' => 1
				);

				$acttObj->insert('bz_credit', $bz_insert_values, false);
			}

			$insert_data = array(
				'invoice_no' => $multInvoicNo,
				'main_job_id' => $row['id'],
				'category_type' => "Translation",
				'assign_date' => $row["asignDate"],
				'language_source' => $row["source"],
				'reg_name' => mysqli_real_escape_string($con, $row["name"]),
				'inch_person' => mysqli_real_escape_string($con, $row["orgContact"]),
				"client_ref" => mysqli_real_escape_string($con, $row["orgRef"]),
				"units" => $row["C_numberUnit"],
				"price_per_unit" => $row["C_rpU"],
				"cost_of_intrp" => $trans_sub_total,
				"travel_time_duration" => "N/A",
				"travel_time_rate_per_hr" => "N/A",
				"cost_of_travel_time" => "N/A",
				"mileage" => "N/A",
				"cost_of_mileage" => "N/A",
				"transport_cost" => "N/A",
				"admin_cost" => "N/A",
				"other_expense" => $row["C_otherCharg"],
				"sub_total" => $trans_sub_total,
				"vat" => $trans_vat_percent,
				"total_cost" => $trans_total_cost,
				"po_order" => $row["porder"],
				"orderCancelation" => $row['orderCancelatoin'],
				"canceled_date_time" => $row["canceled_date"],
				"canceled_reason" => mysqli_real_escape_string($con, $row["canceled_reason"])
			);
			$acttObj->insert('mult_inv_items', $insert_data, false);
		}


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


	if ($proceed == 'Yes') {
		//.....................................................................................	
		$acttObj->editFun_comp('mult_inv', 'mult_amount', $total_invoice, 'm_inv', $multInvoicNo);
		$acttObj->editFun_comp('mult_inv', 'from_date', $search_2, 'm_inv', $multInvoicNo);
		$acttObj->editFun_comp('mult_inv', 'to_date', $search_3, 'm_inv', $multInvoicNo);
		
		$acttObj->editFun_comp('mult_inv', 'vat', $grand_total_vat, 'm_inv', $multInvoicNo);
		$acttObj->editFun_comp('mult_inv', 'non_vat', $grand_non_vat, 'm_inv', $multInvoicNo);

		$voucher_counter = getNextVoucherCount('JV');
		updateVoucherCounter('JV', $voucher_counter);
		$voucher = 'JV-' . $voucher_counter;

		$acttObj->editFun_comp('mult_inv', 'voucher', $voucher, 'm_inv', $multInvoicNo);

		/* Insertion Query to Accounts: Income & Receivable Table
          - account_income : As Credit
          - account_receivable : As Debit
        */

		$company_name_abrv = implode(",", $comp_abrv); //$comp_abrv_str
		$credit_amount = $total_invoice;
		$voucher_no = $multInvoicNo;
		$description = '[Collective Invoice] Company: ' . $company_name_abrv . ', Invoice No: ' . $voucher_no;

		// Check if record already exists
		$parameters = " invoice_no = '" . $multInvoicNo . "' AND dated = '" . date('Y-m-d') . "' AND company = '" . $company_name_abrv . "' AND credit = '" . $credit_amount . "'";
		$chk_exist = isIncomeRecordExists($parameters);

		if ($chk_exist < 1 && $credit_amount > 0) {

			// getting balance amount
			$res = getCurrentBalances($con);

			// Insertion in tbl account_income
			$insert_data = array(
				'voucher' => $voucher,
				'invoice_no' => $voucher_no,
				'dated' => date('Y-m-d'),
				'company' => $company_name_abrv,
				'description' => $description,
				'credit' => $credit_amount,
				'balance' => ($res['balance'] + $credit_amount),
				'posted_by' => $_SESSION['userId'],
				'tbl' => 'mult_inv'
			);

			// Insertion in Account Income
			$jv_voucher = insertAccountIncome($insert_data);

			// Insertion in account_receivable
			$insert_data_rec = array(
				'voucher' => $voucher,
				'invoice_no' => $voucher_no,
				'dated' => date('Y-m-d'),
				'company' => $company_name_abrv,
				'description' => $description,
				'debit' => $credit_amount,
				'balance' => ($res['recv_balance'] + $credit_amount),
				'posted_by' => $_SESSION['userId'],
				'tbl' => 'mult_inv'
			);

			insertAccountReceivable($insert_data_rec);
		} // end if record exists


	}

	$pdf->writeHTML($tbl, true, false, false, false, '');

	// -----------------------------------------------------------------------------

	//Close and output PDF document
	list($a, $b) = explode('.', basename(__FILE__));
	$pdf->Output($a . '.pdf', 'I');
	//============================================================+
	// END OF FILE
	//==========================================================EXCEL FORMAT=========================================================+

}//if company spec
