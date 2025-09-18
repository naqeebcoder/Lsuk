<?php
// $query = "SELECT interpreter.*,invoice.dated, interpreter_reg.name,
//     comp_reg.name as orgzName,comp_reg.abrv,comp_reg.id as comp_id,comp_reg.email,comp_reg.invEmail,comp_reg.buildingName as c_buildingName,comp_reg.line1 as c_line1,comp_reg.line2 as c_line2,comp_reg.streetRoad as c_streetRoad,comp_reg.city as c_city,comp_reg.postCode as c_postCode,comp_reg.payment_terms as c_payment_terms,(CASE WHEN rAmount=0 THEN 'UNPAID' WHEN (rAmount>0 && (round(interpreter.rAmount,2) < round((interpreter.total_charges_comp+(interpreter.total_charges_comp*interpreter.cur_vat)),2)) THEN 'PARTIAL PAID' WHEN (rAmount>0 && (round(interpreter.rAmount,2) >= round((interpreter.total_charges_comp+(interpreter.total_charges_comp*interpreter.cur_vat)),2)) THEN 'PAID' END) AS payment_status
// FROM interpreter
// INNER JOIN invoice ON interpreter.invoiceNo=invoice.invoiceNo
// INNER JOIN interpreter_reg ON interpreter.intrpName=interpreter_reg.id
// INNER JOIN comp_reg ON $table.orgName=comp_reg.abrv
// where multInv_flag=0 and interpreter.id=$invoice_id";
$query = "SELECT 
    interpreter.*,
    invoice.dated, 
    interpreter_reg.name, 
    comp_reg.name AS orgzName,
    comp_reg.abrv, 
    comp_reg.id AS comp_id, 
    comp_reg.email,
    comp_reg.payment_terms, 
    comp_reg.invEmail, 
    comp_reg.buildingName AS c_buildingName, 
    comp_reg.line1 AS c_line1, 
    comp_reg.line2 AS c_line2, 
    comp_reg.streetRoad AS c_streetRoad, 
    comp_reg.city AS c_city, 
    comp_reg.postCode AS c_postCode, 
    comp_reg.payment_terms AS c_payment_terms, 
    (CASE 
        WHEN rAmount = 0 THEN 'UNPAID' 
        WHEN rAmount > 0 
            AND ROUND(interpreter.rAmount, 2) < ROUND(interpreter.total_charges_comp + (interpreter.total_charges_comp * interpreter.cur_vat), 2) 
            THEN 'PARTIAL PAID' 
        WHEN rAmount > 0 
            AND ROUND(interpreter.rAmount, 2) >= ROUND(interpreter.total_charges_comp + (interpreter.total_charges_comp * interpreter.cur_vat), 2) 
            THEN 'PAID' 
     END) AS payment_status 
FROM interpreter 
INNER JOIN invoice ON interpreter.invoiceNo = invoice.invoiceNo 
INNER JOIN interpreter_reg ON interpreter.intrpName = interpreter_reg.id 
INNER JOIN comp_reg ON $table.orgName = comp_reg.abrv 
WHERE multInv_flag = 0 
AND interpreter.id = $invoice_id";
$result = mysqli_query($con, $query);
$payment_terms = "";
$row = mysqli_fetch_assoc($result);
$company_rate_data = !empty($row['company_rate_data']) ? (array) json_decode($row['company_rate_data']) : array();
$_POST['company_rate_id'] = "";
$assignDate = $row['assignDate'];
$payment_terms = $row['payment_terms'];
$assignTime = $row['assignTime'];
$source = $row['source'];
$target = $row['target'];
$buildingName = $row['buildingName'];
$street = $row['street'];
$assignCity = $row['assignCity'];
$postCode = $row['postCode'];
$nameRef = $row['nameRef'];
$orgzName = $row['orgzName'];
$comp_id = $row['comp_id'];
$inchCity = $row['inchCity'];
$inchPcode = $row['inchPcode'];
$invoiceNo = $row['invoiceNo'];
$credit_note = !empty($row['credit_note']) ? "credit_note_" . $row['credit_note'] : "";
$intrpName = $row['name'];
$inchEmail = $row['inchEmail'];
$inchRoad = $row['inchRoad'];
$line1 = $row['line1'];
$line2 = $row['line2'];
$c_buildingName = $row['c_buildingName'];
$c_line1 = $row['c_line1'];
$c_line2 = $row['c_line2'];
$c_streetRoad = $row['c_streetRoad'];
$c_city = $row['c_city'];
$c_postCode = $row['c_postCode'];
$c_payment_terms = $row['c_payment_terms'];
$inchNo = $row['inchNo'];
$orgContact = $row['orgContact'];
$coEmail = $row['email'];
$makCoEmail = $row['invEmail'];
if ($row['new_comp_id'] != 0 && $row['order_company_id'] == 410) {
    $private_company = $acttObj->read_specific("*", "private_company", "id=" . $row['new_comp_id']);
    $orgzName = $private_company['name'];
    $orgContact = $private_company['orgContact'];
    $inchPerson = $private_company['inchPerson'];
    $inchContact = $private_company['inchContact'];
    $c_buildingName = $private_company['inchNo'];
    $c_line1 = $private_company['line1'];
    $c_line2 = $private_company['line2'];
    $c_streetRoad = $private_company['inchRoad'];
    $c_city = $private_company['inchCity'];
    $inchCity = $private_company['inchCity'];
    $c_postCode = $private_company['inchPcode'];
    $inchNo = $private_company['inchNo'];
    $inchEmail = $private_company['inchEmail'];
    $inchEmail2 = $private_company['inchEmail2'];
    $coEmail = $private_company['inchEmail'];
    $makCoEmail = $private_company['inchEmail'];
}
$hoursWorkd = $row['C_hoursWorkd'];
$chargInterp = $row['C_chargInterp'];
$rateHour = $row['C_rateHour'];
$travelMile = $row['C_travelMile'];
$rateMile = $row['C_rateMile'];
$chargeTravel = $row['C_chargeTravel'];
$travelCost = $row['C_travelCost'];
$otherCost = $row['C_otherCost'];
$travelTimeHour = $row['C_travelTimeHour'];
$travelTimeRate = $row['C_travelTimeRate'];
$chargeTravelTime = $row['C_chargeTravelTime'];
$dueDate = $row['dueDate'];
$dated = $row['dated'];
$company_rate_id = $row['company_rate_id'];
$company_rate_data = !empty($row['company_rate_data']) ? (array) json_decode($row['company_rate_data']) : array();
if (!empty($company_rate_data['title'])) {
    $bookinType = $company_rate_data['title'];
} else {
    $bookinType = $row['bookinType'];
}

$get_companies = $acttObj->read_all("comp_reg.id,comp_reg.name,comp_reg.abrv,comp_reg.status,comp_type.company_type_id", "comp_reg,comp_type", "comp_reg.type_id=comp_type.id AND comp_reg.comp_nature!=1 AND comp_reg.status <> 'Company Seized trading in' and comp_reg.status <> 'Company Blacklisted' ORDER BY comp_reg.name ASC");
while ($row_company = $get_companies->fetch_assoc()) {
    if ($row['order_company_id'] == $row_company["id"] || $orgName == $row_company['abrv']) {
        $find_company_type = $row_company["company_type_id"];
    }
}
$get_languages = $acttObj->read_all("lang,language_type", "lang", "1 ORDER BY lang ASC");
while ($row_language = $get_languages->fetch_assoc()) {
    if ($row['source'] == $row_language["lang"]) {
        $find_language_type = $row_language["language_type"];
    }
}
$edit_id = SafeVar::GetVar('edit_id', '');
$row1 = $acttObj->read_specific("*", "telephone", "id=" . $edit_id);
$selected_option_id = $row1['bookinType'];
$postData = [
    'find_company_id' => $row['order_company_id'],
    'find_company_type' => $find_company_type,
    'find_language_type' => $find_language_type,
    'find_booked_date' => $row['bookeddate'],
    'find_booked_time' => $row['bookedtime'],
    'find_assignment_date' => $row['assignDate'],
    'find_assignment_time' => $row['assignTime'],
    'find_order_type' => 1,
    'find_company_rates' => 1
];
// print_r($postData);
// die();
$base_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'];
$api_url = $base_url . "/lsuk_system/ajax_add_interp_data.php";
// Send request to get available options
$ch = curl_init($api_url); // Replace with actual URL
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $postData); // Send the data

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curl_error = curl_error($ch);
curl_close($ch);

$result = @unserialize($response); // If response is serialized
if (!$result) {
    $result = json_decode($response, true); // If response is JSON
}
if (!empty($result['company_rates'])) {
    foreach ($result['company_rates'] as $item) {
        if (true) {
            $bookinType = $item['title']; // Get the correct text
        }
    }
}


$row['bookinType'] = $bookinType;
$orgRef = $row['orgRef'];
$C_admnchargs = $row['C_admnchargs'];
$C_otherexpns = $row['C_otherexpns'];
$porder = $row['porder'];
$payment_status = $row['payment_status'];
$rAmount = $row['rAmount'];
$C_comments = $row['C_comments'];
$commit = $row['commit'];
$invoic_date = @$row['invoic_date'];
$abrv = @$row['abrv'];
$cur_vat = @$row['cur_vat'];
$orderCancelatoin = $row['orderCancelatoin'];
$order_cancel_flag = $row['order_cancel_flag'];
$cn_t_id = $row['cn_t_id'];
$cn_r_id = $row['cn_r_id'];
$cn_date = $row['cn_date'];
$append_invoiceNo = '';
if (!empty($row['credit_note'])) {
    $append_invoiceNo = "-0" . $acttObj->read_specific("count(*) as counter", "credit_notes", "order_id=" . $invoice_id . " and order_type='f2f'")['counter'];
}
$order_cancel_remarks = $row['order_cancel_remarks'];
$write_cancel_type = str_replace("[DATE]", $cn_date, $acttObj->read_specific("cd_title", "cancellation_drops", "cd_id=" . $row['cn_t_id'])['cd_title']);
$write_cancel_remarks = $cn_t_id == 6 ? $order_cancel_remarks : $acttObj->read_specific("cr_title", "cancel_reasons", "cr_id=" . $cn_r_id)['cr_title'];
if ($orderCancelatoin == 1 || $order_cancel_flag == 1) {
    $write_cancellation = '<span style="margin-left:10px">Cancellation Type:  ' . $write_cancel_type . '</span><br><br><span style="margin-left:10px">Cancellation Reason:  ' . $write_cancel_remarks . '</span><br><br>';
}
$g_row = $row;
$g_row['intrpName'] = @$intrpName;
$g_row['payment_terms'] = @$payment_terms;
$g_row["_totalhoursworked"] = $misc->numberFormat_fun(@$hoursWorkd);
$g_row["_totalhourspaid"] = $misc->numberFormat_fun(@$rateHour * @$hoursWorkd);
$g_row["_rateHour"] = $misc->numberFormat_fun(@$rateHour);
$g_row["_travelMile"] = $misc->numberFormat_fun(@$travelMile);

$g_row["_C_admnchargs"] = $misc->numberFormat_fun(@$C_admnchargs);

$total2 = @$travelTimeHour * @$travelTimeRate;

$total4 = $misc->numberFormat_fun(@$rateMile * @$travelMile);
$g_row["_total4"] = $total4;

$total1 = 0;
$total1 = @$rateHour * @$hoursWorkd;
$total5 = $misc->numberFormat_fun(@$total1 + @$total2 + @$total4 + @$C_admnchargs);
$g_row["_total5"] = $total5;
$makTotal5 = floatval(preg_replace('/[^\d.]/', '', $total5));
$vat = $misc->numberFormat_fun(@$makTotal5 * @$cur_vat);
$g_row["_vat"] = $vat;

$g_row["_travelTimeHour"] = $misc->numberFormat_fun(@$travelTimeHour);
$g_row["_travelTimeCost"] = $misc->numberFormat_fun(@$travelTimeRate * @$travelTimeHour);
$g_row["_C_otherexpns"] = $misc->numberFormat_fun(@$C_otherexpns);
$makVat = floatval(preg_replace('/[^\d.]/', '', $vat));
$g_row["_vattotalexp"] = $misc->numberFormat_fun(@$makVat + @$makTotal5 + @$C_otherexpns);

if (@$invoic_date == '0000-00-00') {
    $invoiceDated = $misc->dated(date("Y-m-d"));
} else {
    $invoiceDated = $misc->dated(@$invoic_date);
}
$g_row["_invoiceDated"] = $invoiceDated;

if (@$C_comments) {
    $_comments = @$C_comments;
} else {
    $_comments = 'Nil';
}
$g_row["_comments"] = $_comments;
if ($orderCancelatoin == 1 || $order_cancel_flag == 1) {
    $g_row["_write_cancel_type"] = 'Cancellation Type : ' . $write_cancel_type;
    $g_row["_write_cancel_remarks"] = 'Cancellation Reason : ' . $write_cancel_remarks;
} else {
    $g_row["_write_cancel_type"] = '';
    $g_row["_write_cancel_remarks"] = '';
}
$g_row["_assignDate"] = $misc->dated(@$assignDate);
$pay_terms = "+" . @$c_payment_terms . " days";
$g_row["_15DaysTime"] = $misc->dated(date("Y-m-d", strtotime($pay_terms)));
$g_row["_otherexpns"] = $misc->numberFormat_fun(@$C_otherexpns);
$crednoted = $g_row["credit_note"];
$bCredNoted = false;
if (isset($crednoted) && $crednoted != "") {
    $bCredNoted = true;
}
$g_row['invoiceNo'] = $invoiceNo . $append_invoiceNo;
$g_row['credit_note'] = $credit_note;
$g_row['_total_other_expenses'] = $g_row["_travelTimeCost"]+ $g_row["_total4"]+ $g_row["_otherexpns"];
