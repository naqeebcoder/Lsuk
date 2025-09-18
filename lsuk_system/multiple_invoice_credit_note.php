<?php
if (session_id() == '' || !isset($_SESSION)) {
    session_start();
}

include 'db.php';
include 'class.php';
include 'inc_functions.php';

$allowed_type_idz = "233";
//Check if user has current action allowed
if ($_SESSION['is_root'] == 0) {
    $get_page_access = $acttObj->read_specific("id", "action_permissions", "user_id=" . $_SESSION['userId'] . " AND action_id IN (" . $allowed_type_idz . ")")['id'];
    if (empty($get_page_access)) {
        die("<center><h2 class='text-center text-danger'>You do not have access to <u>Credit Note</u> action for jobs!<br>Kindly contact admin for further process.</h2></center>");
    }
}

$multInvoicNo = @$_GET['multInvoiceNo'];
$proceed = @$_GET['proceed'];

$orgs = array();
if (isset($search_1) && $search_1 != "") {
    $orgs = explode(",", $search_1);
}
$search_2 = @$_GET['search_2'];
$search_3 = @$_GET['search_3'];
$p_org = @$_GET['p_org'];
if (isset($p_org) && (!empty($p_org))) {
    $p_org_ad = $acttObj->read_specific("GROUP_CONCAT(CONCAT('''', comp_reg.abrv, '''' )) as ch_ids", "subsidiaries,comp_reg", " subsidiaries.child_comp=comp_reg.id AND subsidiaries.parent_comp=$p_org")['ch_ids'] ?: '';
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

/**  Credit Note Submission ***/

if (isset($_POST['submit'])) {

    $invoices_data = $acttObj->read_specific("*", "mult_inv", "id=" . $id);

    $chk_mult_items_exists = $acttObj->read_specific("COUNT(id) as counter", "mult_inv_items", "invoice_no = '" . $invoices_data['m_inv'] . "'")['counter'];

    if ($chk_mult_items_exists < 1) {

        $inter_res = $acttObj->full_fetch_array("SELECT interpreter.*, interpreter_reg.name, canceled_orders.cancel_type_id, 
        canceled_orders.cancel_reason_id, canceled_orders.canceled_by, canceled_orders.canceled_date, 
        canceled_orders.canceled_reason
        FROM interpreter 
        INNER JOIN interpreter_reg ON interpreter.intrpName = interpreter_reg.id 
        LEFT JOIN canceled_orders ON canceled_orders.job_id = interpreter.id AND canceled_orders.job_type = 1 
        WHERE interpreter.multInvoicNo = '" . $invoices_data['m_inv'] . "' AND interpreter.multInv_flag = 1
        ORDER BY interpreter.assignDate ASC");

        foreach ($inter_res as $row) {

            $withou_VAT_interp = $row["C_otherCost"];
            $C_hoursWorkd_C_rateHour = $row["C_hoursWorkd"] * $row["C_rateHour"];
            $total_charges = $row['total_charges_comp'] + $row['int_vat'];
            $sub_total = $row['C_chargInterp'] + $row["C_chargeTravelTime"] + $row['C_chargeTravel'] + $row['C_travelCost'] + $row["C_otherCost"] + $row["C_admnchargs"];
            $vat_percent = ($sub_total - $withou_VAT_interp) * 0.2;
            $total_cost = $sub_total + $vat_percent;

            $insert_data = array(
                'invoice_no' => $invoices_data['m_inv'],
                'main_job_id' => $row['id'],
                'category_type' => 'Face to Face',
                'assign_date' => $row["assignDate"],
                'language_source' => mysqli_real_escape_string($con, $row["source"]),
                'reg_name' => mysqli_real_escape_string($con, $row["name"]),
                'inch_person' => mysqli_real_escape_string($con, $row["inchPerson"]),
                "client_ref" => mysqli_real_escape_string($con, $row["orgRef"]),
                "units" => $row["C_hoursWorkd"],
                "price_per_unit" => $row["C_rateHour"],
                "cost_of_intrp" => $row["C_chargInterp"],
                "travel_time_duration" => $row["C_travelTimeHour"],
                "travel_time_rate_per_hr" => $row["C_travelTimeRate"],
                "cost_of_travel_time" => $row["C_chargeTravelTime"],
                "mileage" => $row["C_travelMile"],
                "cost_of_mileage" => $row["C_chargeTravel"],
                "transport_cost" => $row["C_travelCost"],
                "admin_cost" => $row["C_admnchargs"],
                "other_expense" => $withou_VAT_interp,
                "sub_total" => $sub_total,
                "vat" => $vat_percent,
                "total_cost" => $total_cost,
                "po_order" => $row["porder"],
                "orderCancelation" => $row['orderCancelatoin'],
                "canceled_date_time" => $row["canceled_date"],
                "canceled_reason" => mysqli_real_escape_string($con, $row["canceled_reason"])
            );
            $acttObj->insert('mult_inv_items', $insert_data, false);
        }

        // Telephone
        $query_telep = $acttObj->full_fetch_array("SELECT telephone.*, interpreter_reg.name, canceled_orders.cancel_type_id, 
        canceled_orders.cancel_reason_id, canceled_orders.canceled_by, canceled_orders.canceled_date, 
        canceled_orders.canceled_reason  
        FROM telephone 
        INNER JOIN interpreter_reg ON telephone.intrpName = interpreter_reg.id 
        LEFT JOIN canceled_orders ON canceled_orders.job_id = telephone.id AND canceled_orders.job_type = 2
        WHERE telephone.multInvoicNo = '" . $invoices_data['m_inv'] . "' AND telephone.multInv_flag = 1
        ORDER BY telephone.assignDate ASC");

        foreach ($query_telep as $row) {
            $withou_VAT_telp = $row["C_otherCharges"];
            $non_vat_tlep = $row["total_charges_comp"] - $row["C_otherCharges"];
            $C_otherCharges_CallCharges = $row['C_callcharges'] + $row['C_otherCharges'];
            $telep_sub_total = $row['C_chargInterp'] + $C_otherCharges_CallCharges;
            $telep_vat_percent = (($telep_sub_total - $C_otherCharges_CallCharges) * 0.2);
            $telep_total_cost = $telep_sub_total + $telep_vat_percent;

            $insert_data = array(
                'invoice_no' => $invoices_data['m_inv'],
                'main_job_id' => $row['id'],
                'category_type' => "Remote",
                'assign_date' => $row["assignDate"],
                'language_source' => mysqli_real_escape_string($con, $row["source"]),
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

        // Translation
        $query_trans = $acttObj->full_fetch_array("SELECT translation.*, interpreter_reg.name, canceled_orders.cancel_type_id, 
        canceled_orders.cancel_reason_id, canceled_orders.canceled_by, canceled_orders.canceled_date, 
        canceled_orders.canceled_reason   
        FROM translation 
        INNER JOIN interpreter_reg ON translation.intrpName = translation.id 
        LEFT JOIN canceled_orders ON canceled_orders.job_id = translation.id AND canceled_orders.job_type = 3
        WHERE translation.multInvoicNo = '" . $invoices_data['m_inv'] . "' AND translation.multInv_flag = 1
        ORDER BY translation.asignDate ASC");

        foreach ($query_trans as $row) {
            $withou_VAT_trans = $row["C_otherCharg"];
            $non_vat_trans = $row["total_charges_comp"] - $row["C_otherCharg"];

            $trans_sub_total = ($row["C_numberUnit"] * $row["C_rpU"]) + $row["C_otherCharg"]; //$row["total_units"];
            $trans_vat_percent = ($trans_sub_total - $row["C_otherCharg"]) * 0.2;
            $trans_total_cost = $trans_sub_total + $trans_vat_percent;

            $insert_data = array(
                'invoice_no' => $invoices_data['m_inv'],
                'main_job_id' => $row['id'],
                'category_type' => "Translation",
                'assign_date' => $row["asignDate"],
                'language_source' => mysqli_real_escape_string($con, $row["source"]),
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
    }

    $data = json_encode($invoices_data);

    // Inactive All active Notes if any
    $inactive_previous_credit_notes = mysqli_query($con, "UPDATE credit_notes_income_invoices SET status = 0 WHERE status = 1 AND income_invoice_id = " . $id . " AND tbl = 'mult_inv'");

    // Insert New Credit note
    $is_inserted = $acttObj->insert("credit_notes_income_invoices", array("income_invoice_id" => $id, "invoice_no" => $invoices_data['m_inv'], "data" => $data, "tbl" => "mult_inv", "posted_by" => $_SESSION['userId']), true);

    $acttObj->update("mult_inv", array("credit_note_id" => $is_inserted, "commit" => 0), array("id" => $id));

    $update_int = $acttObj->db_query("UPDATE interpreter SET multInvoicNo = '', multInv_flag = 0 WHERE multInvoicNo = '" . $invoices_data['m_inv'] . "'");
    $update_tele = $acttObj->db_query("UPDATE telephone SET multInvoicNo = '', multInv_flag = 0 WHERE multInvoicNo = '" . $invoices_data['m_inv'] . "'");
    $update_trans = $acttObj->db_query("UPDATE translation SET multInvoicNo = '', multInv_flag = 0 WHERE multInvoicNo = '" . $invoices_data['m_inv'] . "'");

    // $upd_values = array(
    //     'deleted_flag' => 1,
    //     'deleted_by' => $_SESSION['UserName'],
    //     'deleted_date' => date('Y-m-d')
    // );

    // $upd_para = array(
    //     'invoiceNo' => "'$multInvoicNo'",
    //     'mult_inv_flag' => 1
    // );
    // $acttObj->update("comp_credit", $upd_values, $upd_para);
    // $acttObj->update("bz_credit", $upd_values, $upd_para);

    $acttObj->db_query("UPDATE comp_credit SET deleted_flag = 1, deleted_by = '" . $_SESSION['UserName'] . "', deleted_date = '" . date('Y-m-d') . "' WHERE invoiceNo = '" . $multInvoicNo . "' AND mult_inv_flag = 1");

    $acttObj->db_query("UPDATE bz_credit SET deleted_flag = 1, deleted_by = '" . $_SESSION['UserName'] . "', deleted_date = '" . date('Y-m-d') . "' WHERE invoiceNo = '" . $multInvoicNo . "' AND mult_inv_flag = 1");

    if ($is_inserted) {

        /* Insertion Query to Accounts: Income & Receivable Table
            - account_income : As Debit (balance - Invoice Amount)
            - account_receivable : As Credit (balance - Credit Amount) -- parital payments only
        */

        if ($invoices_data && $invoices_data['mult_amount'] > 0) {

            $description = '[Credit Note][Collective Invoice] ' . $invoices_data['comp_name'] . ', Invoice No: ' . $invoices_data['m_inv'];

            // getting balance amount
            $res = getCurrentBalances($con);

            // Getting New Voucher Counter
            $voucher_counter = getNextVoucherCount('JV');

            // Updating the new Voucher Counter
            updateVoucherCounter('JV', $voucher_counter);

            $voucher = 'JV-' . $voucher_counter;

            // Insertion in tbl account_income
            $insert_data = array(
                'invoice_no' => $invoices_data['m_inv'],
                'voucher' => $voucher,
                'dated' => date('Y-m-d'),
                'company' => $invoices_data['comp_abrv'],
                'description' => $description,
                'debit' => $invoices_data['mult_amount'],
                'balance' => ($res['balance'] - $invoices_data['mult_amount']),
                'posted_by' => $_SESSION['userId'],
                'tbl' => 'mult_inv'
            );

            $jv_voucher = insertAccountIncome($insert_data);

            if ($invoices_data['rAmount'] > 0) {

                // it will update the journal record for future use, as we are not inserting any reversal entry for specific rAmount
                updateJournalLedgerStatus('credit_note', 1, $invoices_data['m_inv']);

                // checking for partial payments amount
                $partial_payments = $acttObj->read_specific("SUM(amount) as recieved_partial_amount, partial_amounts.*", "partial_amounts", "order_id = '" . $invoices_data['m_inv'] . "' AND tbl = 'mult_inv' AND status = 1");

                if ($partial_payments['recieved_partial_amount'] > 0) {

                    $remaining_invoice_amount = ($invoices_data['mult_amount'] - $partial_payments['recieved_partial_amount']);

                    // getting balance amount
                    $res = getCurrentBalances($con);

                    if ($partial_payments['payment_type'] == 'cash') {
                        $voucher_label = 'CPV';
                        $is_bank = '0';
                    } else {
                        $voucher_label = 'BPV';
                        $is_bank = '1';
                    }

                    // Getting New Voucher Counter
                    $voucher_counter = getNextVoucherCount($voucher_label);

                    // Updating the new Voucher Counter
                    updateVoucherCounter($voucher_label, $voucher_counter);

                    $voucher = $voucher_label . '-' . $voucher_counter;

                    $insert_data_rec = array(
                        'voucher' => $voucher,
                        'invoice_no' => $invoices_data['m_inv'],
                        'dated' => date('Y-m-d'),
                        'company' => $invoices_data['comp_abrv'],
                        'description' => $description,
                        'credit' => $remaining_invoice_amount,
                        'balance' => ($res['recv_balance'] - $remaining_invoice_amount),
                        'posted_by' => $_SESSION['userId'],
                        'tbl' => 'mult_inv'
                    );

                    $re_result = insertAccountReceivable($insert_data_rec);
                }
            } else {
                // Insertion in tbl account_receivable - single entry for invoice_total_amount reversal
                $insert_data_rec = array(
                    'voucher' => $voucher,
                    'invoice_no' => $invoices_data['m_inv'],
                    'dated' => date('Y-m-d'),
                    'company' => $invoices_data['comp_abrv'],
                    'description' => $description,
                    'credit' => $invoices_data['mult_amount'],
                    'balance' => ($res['recv_balance'] - $invoices_data['mult_amount']),
                    'posted_by' => $_SESSION['userId'],
                    'tbl' => 'mult_inv'
                );

                $re_result = insertAccountReceivable($insert_data_rec);
            }
        } // end if record exists

    }

    // this will trash the current invoice paid records if any (tbl: paid_income_invoices) 
    $delete_invoice_payments = mysqli_query($con, "UPDATE partial_amounts SET status = 0 WHERE status = 1 AND tbl = 'mult_inv' AND order_id = '" . $invoices_data['m_inv'] . "'");

    $acttObj->insert('daily_logs', ['action_id' => 17, 'user_id' => $_SESSION['userId'], 'details' => "Collective Invoice No: " . $invoices_data['id']]);

    echo "<script>
			alert('Credit note successfully created!');
			window.close();
			window.onunload = refreshParent;

			function refreshParent() {
				window.opener.location.reload();
			}
		</script>";
}
/**  Credit Note Submission End ***/

if (!empty($multInvoicNo)) {

    $check_exists = $acttObj->read_specific("COUNT(*) as counter", "mult_inv_items", "invoice_no = '$multInvoicNo'")['counter'];

    if ($check_exists < 1) {
        $query = "SELECT interpreter.*, interpreter_reg.name, canceled_orders.cancel_type_id, 
        canceled_orders.cancel_reason_id, canceled_orders.canceled_by, canceled_orders.canceled_date, 
        canceled_orders.canceled_reason
        FROM interpreter 
        INNER JOIN interpreter_reg ON interpreter.intrpName = interpreter_reg.id 
        LEFT JOIN canceled_orders ON canceled_orders.job_id = interpreter.id AND canceled_orders.job_type = 1 
        WHERE interpreter.multInvoicNo = '$multInvoicNo'
        ORDER BY interpreter.assignDate ASC ";
    } else {
        $query = "SELECT mi.category_type, mi.assign_date as assignDate, mi.language_source as source, mi.reg_name as name, mi.inch_person as inchPerson, mi.client_ref as orgRef, mi.units as C_hoursWorkd, mi.price_per_unit as C_rateHour, mi.cost_of_intrp as C_chargInterp, mi.travel_time_duration as C_travelTimeHour, mi.travel_time_rate_per_hr as C_travelTimeRate, mi.cost_of_travel_time as C_chargeTravelTime, mi.mileage as C_travelMile, mi.cost_of_mileage as C_chargeTravel, mi.transport_cost as C_travelCost, mi.admin_cost as C_admnchargs, mi.other_expense as C_otherCost, mi.po_order as porder, mi.orderCancelation as orderCancelatoin, mi.canceled_date_time	as canceled_date, mi.canceled_reason                            
        FROM mult_inv_items mi 
        WHERE mi.invoice_no = '" . $multInvoicNo . "' AND mi.category_type = 'Face to Face'
        ORDER BY mi.assign_date ASC";
    }

    $result = mysqli_query($con, $query);

    $invoice_to = "";
    for ($kj = 0; $kj < count($name); $kj++) {
        $invoice_to .= "<p><strong>Invoice To: " . $name[$kj] . "</strong><br/>" . $buildingName[$kj] . "<br/>" . $line1[$kj] . "<br/>" . $line2[$kj] . "<br/>" . $streetRoad[$kj] . "<br/>" . $city[$kj] . "<br/>" . $postCode[$kj] . "<br/></p>";
    }


    $new_credit_note = $acttObj->read_specific("id , CONCAT(DATE_FORMAT(posted_on,'%y%m'),'_',LPAD(COUNT(id), 2, '0')) as credit_note_no", "credit_notes_income_invoices", "income_invoice_id = " . $id . " AND tbl = 'mult_inv'");

    if ((isset($new_credit_note['id']) && !empty($new_credit_note['id']))) {
        $credit_note_number = $new_credit_note['credit_note_no'];
    } else {
        $credit_note_number = "Not created yet";
    }


?>

    <html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

    <head>
        <title>View Collective Invoice</title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
        <link rel="stylesheet" type="text/css" href="css/util.css" />
        <style>
            .table-condensed>tbody>tr>td,
            .table-condensed>tbody>tr>th,
            .table-condensed>tfoot>tr>td,
            .table-condensed>tfoot>tr>th,
            .table-condensed>thead>tr>td,
            .table-condensed>thead>tr>th {
                font-size: 12px;
            }
        </style>
        <script>
            function popupwindow(url, title, w, h) {
                var left = (screen.width / 2) - (w / 2);
                var top = (screen.height / 2) - (h / 2);
                return window.open(url, title, 'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=yes, resizable=yes, copyhistory=no, width=' + w + ', height=' + h + ', top=' + top + ', left=' + left);
            }

            function myFunction() {
                var a = "<?php echo $multInvoicNo; ?>";
                var p = $("#proceed").val();
                if (p != '') {
                    var result = confirm("Are you sure to " + p + " Invoice# " + a);

                    if (result == true) {
                        p = p;
                    } else {
                        return false;
                    }
                } else {
                    p = '';
                }

                window.location.href = '?multInvoiceNo=' + a + '&proceed=' + p;

            }
        </script>

    </head>

    <body>
        <div class="container-fluid">
            <div>
                <div class="col-sm-8 col-md-8 text-left">
                    <h2 class="m-t-10">
                        <?php if ((!isset($new_credit_note['id']) && empty($new_credit_note['id']))) { ?>
                            <u>Invoice: (<?php echo $multInvoicNo; ?>)</u>
                        <?php } else { ?>
                            Credit Note: <strong><?php echo $credit_note_number; ?></strong>
                        <?php }  ?>
                        <?php if ((isset($new_credit_note['id']) && !empty($new_credit_note['id']))) { ?>
                            <h5>
                                Invoice: (<?php echo $multInvoicNo; ?>)
                            </h5>
                        <?php } ?>
                    </h2>
                </div>

                <div class="col-md-4 col-sm-4 form-inline text-right m-t-20">
                    <div class="form-group">
                        <form action="" method="post" class="form-inline" id="frmCreateCreditNote">
                            <div class="form-group">
                                <?php if (!isset($_POST['submit']) && $multi_invoice_info['commit'] == 1) { ?>
                                    <input type="submit" class='prnt btn btn-primary hd btn_credit_note' name="submit" value="Create Credit Note" />
                                <?php }
                                if (!empty($multi_invoice_info['credit_note_id'])) { ?>
                                    <a href="javascript:void(0)" onClick="popupwindow('reports_lsuk/pdf/rip_multiple_inv_pdf.php?multInvoiceNo=<?php echo $multInvoicNo; ?>', 'Print Invoice', 1200, 800);" title="Print" class="btn btn-sm btn-primary">
                                        <i class="fa fa-print"></i> Print
                                    </a>
                            </div>
                        <?php } ?>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-sm-12">
                <p>
                    <?php echo $invoice_to ?>
                </p>
                <p class="text-right">
                    Dated: <?php echo $misc->dated($multi_invoice_info['dated']); ?>
                    <br>
                    Date Range: <?php echo $misc->dated($multi_invoice_info['from_date']); ?>
                    to <?php echo $misc->dated($multi_invoice_info['to_date']); ?>
                </p>
            </div>

        </div>

        <div class="col-sm-12">
            <table width="105%" cellpadding="2" class="table table-bordered table-striped table-hover table-condensed">
                <thead>
                    <tr class="bg-primary">
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
                        <th>Subtotal (£)</th>
                        <th>VAT (20%) (£)</th>
                        <th>Total Cost (£)</th>
                        <th width="8%">P. Order</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
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

                        echo '<tr>
                                <td style="width:30px;">' . $i . '</td>
                                <td>' . $misc->dated($row["assignDate"]) . '</td>
                                <td>Face to Face</td>
                                <td>' . $row["source"] . '</td>
                                <td>' . $row["name"] . '</td>
                                <td>' . $row["inchPerson"] . '</td>
                                <td>' . $row["orgRef"] . '</td>
                                <td>' . $row["C_hoursWorkd"] . '</td>
                                <td>' . $row["C_rateHour"] . '</td>
                                <td>' . $row["C_chargInterp"] . '</td>
                                <td>' . $row["C_travelTimeHour"] . '</td>
                                <td>' . $row["C_travelTimeRate"] . '</td>
                                <td>' . $row["C_chargeTravelTime"] . '</td>
                                <td>' . $row["C_travelMile"] . '</td>
                                <!--td>' . $row["C_rateMile"] . '</td-->
                                <td>' . $row["C_chargeTravel"] . '</td>
                                <td>' . $row["C_travelCost"] . '</td>
                                <td>' . $row["C_admnchargs"] . '</td>
                                <td>' . $withou_VAT_interp . '</td>
                                <td>' . $shw_sub_total . '</td>
                                <td>' . $shw_vat_percent . '</td>
                                <td>' . $shw_total_cost . '</td>
                                <td width="8%">' . $row["porder"] . '</td>
                            </tr>';

                        if ($row['orderCancelatoin'] == 1) {
                            $cancelled_date_time = date("d-m-Y", strtotime($row["canceled_date"])) . " " . date("H:i", strtotime($row["canceled_date"]));
                            echo '<tr>
                                        <td colspan="22">#' . $i . '
                                            <strong>Cancellation Notes: </strong>
                                            <strong>Dated:</strong> ' . $cancelled_date_time . ', 
                                            <strong>Reason:</strong> ' . $row["canceled_reason"] . '
                                        </td>
                                </tr>';
                        }

                        $i++;
                    } // end while loop 

                    // Telephone / Remote 
                    if ($check_exists < 1) {
                        $query_telep = "SELECT telephone.*, interpreter_reg.name, canceled_orders.cancel_type_id, 
                            canceled_orders.cancel_reason_id, canceled_orders.canceled_by, canceled_orders.canceled_date, 
                            canceled_orders.canceled_reason  
                            FROM telephone 
                            INNER JOIN interpreter_reg ON telephone.intrpName = interpreter_reg.id 
                            LEFT JOIN canceled_orders ON canceled_orders.job_id = telephone.id AND canceled_orders.job_type = 2
                            WHERE telephone.multInvoicNo = '$multInvoicNo' AND telephone.multInv_flag = 1
                            ORDER BY telephone.assignDate ASC";
                    } else {
                        $query_telep = "SELECT mi.category_type, mi.assign_date as assignDate, mi.language_source as source, mi.reg_name as name, mi.inch_person as inchPerson, mi.client_ref as orgRef, mi.units as C_hoursWorkd, mi.price_per_unit as C_rateHour, mi.cost_of_intrp as C_chargInterp, mi.travel_time_duration as C_travelTimeHour, mi.travel_time_rate_per_hr as C_travelTimeRate, mi.cost_of_travel_time as C_chargeTravelTime, mi.mileage as C_travelMile, mi.cost_of_mileage as C_chargeTravel, mi.transport_cost as C_travelCost, mi.admin_cost as C_admnchargs, mi.other_expense as C_otherCharges, mi.po_order as porder, mi.orderCancelation as orderCancelatoin, mi.canceled_date_time as canceled_date, mi.canceled_reason                            
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

                        echo '<tr>
                                <td style="width:30px;">' . $i . '</td>
                                <td>' . $misc->dated($row["assignDate"]) . '</td>
                                <td>Remote</td>
                                <td>' . $row["source"] . '</td>
                                <td>' . $row["name"] . '</td>
                                <td>' . $row["inchPerson"] . '</td>
                                <td>' . $row["orgRef"] . '</td>
                                <td>' . $row["C_hoursWorkd"] . '</td>
                                <td>' . $row["C_rateHour"] . '</td>
                                <td>' . $row["C_chargInterp"] . '</td>
                                <td>N/A</td>
                                <td>N/A</td>
                                <td>N/A</td>
                                <td>N/A</td>
                                <!--td>N/A</td-->
                                <td>N/A</td>
                                <td>N/A</td>
                                <td>N/A</td>
                                <td>' . $C_otherCharges_CallCharges . '</td>
                                <td>' . $shw_telep_sub_total . '</td>
                                <td>' . $shw_telep_vat_percent . '</td>
                                <td>' . $shw_telep_total_cost . '</td>
                                <td>' . $row["porder"] . '</td>
                            </tr>';

                        if ($row['orderCancelatoin'] == 1) {
                            $cancelled_date_time = date("d-m-Y", strtotime($row["canceled_date"])) . " " . date("H:i", strtotime($row["canceled_date"]));
                            echo '<tr>
                                        <td colspan="22">#' . $i . '
                                            <strong>Cancellation Notes: </strong>
                                            <strong>Dated:</strong> ' . $cancelled_date_time . ', 
                                            <strong>Reason:</strong> ' . $row["canceled_reason"] . '
                                        </td>
                                </tr>';
                        }

                        $i++;
                    }


                    // Translation
                    if ($check_exists < 1) {
                        $query_trans = "SELECT translation.*, interpreter_reg.name, canceled_orders.cancel_type_id, 
                            canceled_orders.cancel_reason_id, canceled_orders.canceled_by, canceled_orders.canceled_date, 
                            canceled_orders.canceled_reason   
                            FROM translation 
                            INNER JOIN interpreter_reg ON translation.intrpName = translation.id 
                            LEFT JOIN canceled_orders ON canceled_orders.job_id = translation.id AND canceled_orders.job_type = 3
                            WHERE translation.multInvoicNo = '$multInvoicNo' AND translation.multInv_flag = 1
                            ORDER BY translation.asignDate ASC";
                    } else {
                        $query_trans = "SELECT mi.category_type, mi.assign_date as asignDate, mi.language_source as source, mi.reg_name as name, mi.inch_person as orgContact, mi.client_ref as orgRef, mi.units as C_numberUnit, mi.price_per_unit as C_rpU, mi.cost_of_intrp as C_chargInterp, mi.travel_time_duration as C_travelTimeHour, mi.travel_time_rate_per_hr as C_travelTimeRate, mi.cost_of_travel_time as C_chargeTravelTime, mi.mileage as C_travelMile, mi.cost_of_mileage as C_chargeTravel, mi.transport_cost as C_travelCost, mi.admin_cost as C_admnchargs, mi.other_expense as C_otherCharg, mi.po_order as porder, mi.orderCancelation as orderCancelatoin, mi.canceled_date_time	as canceled_date, mi.canceled_reason                            
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

                        $shw_trans_sub_total = ($trans_sub_total > 0) ? $misc->numberFormat_fun($trans_sub_total) : 0;
                        $shw_trans_vat_percent = ($trans_vat_percent > 0) ? $misc->numberFormat_fun($trans_vat_percent) : 0;
                        $shw_trans_total_cost = ($trans_total_cost > 0) ? $misc->numberFormat_fun($trans_total_cost) : 0;

                        echo '<tr>
                                <td style="width:30px;">' . $i . '</td>
                                <td>' . $misc->dated($row["asignDate"]) . '</td>
                                <td>Translation</td>
                                <td>' . $row["source"] . '</td>
                                <td>' . $row["name"] . '</td>
                                <td>' . $row["orgContact"] . '</td>
                                <td>' . $row["orgRef"] . '</td>
                                <td>' . $row["C_numberUnit"] . '</td>
                                <td>' . $row["C_rpU"] . '</td>
                                <td>' . $trans_sub_total . '</td>
                                <td>N/A</td>
                                <td>N/A</td>
                                <td>N/A</td>
                                <td>N/A</td>
                                <!--td>N/A</td-->
                                <td>N/A</td>
                                <td>N/A</td>
                                <td>N/A</td>
                                <td>' . $row["C_otherCharg"] . '</td>
                                <td>' . $shw_trans_sub_total . '</td>
                                <td>' . $shw_trans_vat_percent . '</td>
                                <td>' . $shw_trans_total_cost . '</td>
                                <td>' . $row["porder"] . '</td>
                            </tr>';

                        if ($row['orderCancelatoin'] == 1) {
                            $cancelled_date_time = date("d-m-Y", strtotime($row["canceled_date"])) . " " . date("H:i", strtotime($row["canceled_date"]));
                            echo '<tr>
                                        <td colspan="22">
                                            <strong>Cancellation Notes: </strong>
                                            <strong>Dated:</strong> ' . $cancelled_date_time . ', 
                                            <strong>Reason:</strong> ' . $row["canceled_reason"] . '
                                        </td>
                                </tr>';
                        }

                        $i++;
                    }

                    $g_total_all = $g_total_interp  + $g_total_telep + $g_total_trans;
                    $vat_all = ($g_total_vat_interp + $g_total_vat_telep + $g_total_vat_trans);
                    $grand_total = $C_otherexpns + ($g_total_interp  + $g_total_telep + $g_total_trans) + $vat_all;


                    $total_invoice = $grand_sub_total + $grand_total_vat;

                    ?>
                </tbody>
                <tfoot>
                    <tr class="summary">
                        <td colspan="20" align="right"><b>Total Cost before VAT</b></td>
                        <td colspan="2"><b><?php echo $misc->numberFormat_fun($grand_sub_total - $grand_non_vat); ?></b></td>
                    </tr>

                    <tr class="summary">
                        <td colspan="20" align="right"><b>VAT @20%</b></td>
                        <td colspan="2"><b><?php echo $misc->numberFormat_fun($grand_total_vat); ?></b></td>
                    </tr>

                    <tr class="summary">
                        <td colspan="20" align="right"><b>Total Non-VAT Cost</b></td>
                        <td colspan="2"><b><?php echo $misc->numberFormat_fun($grand_non_vat); ?></b></td>
                    </tr>

                    <tr class="summary">
                        <td colspan="20" align="right"><b>Total Invoice</b></td>
                        <td colspan="2"><b><?php echo $misc->numberFormat_fun($total_invoice); ?></b></td>
                    </tr>
                </tfoot>
            </table>
        </div>



    <?php } ?>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>

    <script>
        $(document).ready(function(e) {
            $('.btn_credit_note').click(function(e) {
                if (confirm("Are you sure to create Credit Note?") == true) {
                    $('#frmCreateCreditNote').submit();
                } else {
                    return false;
                }
            });
        });
    </script>

    </body>

    </html>