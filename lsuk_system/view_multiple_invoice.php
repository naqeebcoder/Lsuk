<?php
if (session_id() == '' || !isset($_SESSION)) {
    session_start();
}

include 'db.php';
include 'class.php';

$get_actions = explode(",", $acttObj->read_specific("GROUP_CONCAT(action_permissions.action_id) as actions", "action_permissions,route_actions", "action_permissions.action_id=route_actions.id AND route_actions.route_id=223 AND action_permissions.user_id=" . $_SESSION['userId'])['actions']);

$action_view_invoice = $_SESSION['is_root'] == 1 || in_array(229, $get_actions);
$action_export_to_excel = $_SESSION['is_root'] == 1 || in_array(229, $get_actions);

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

$query = "SELECT * FROM comp_reg WHERE id IN (" . $multi_invoice_info['comp_id'] . ")";
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
if (!empty($multInvoicNo)) {

    $check_exists = $acttObj->read_specific("COUNT(*) as counter", "mult_inv_items", "invoice_no = '$multInvoicNo'")['counter'];

    if ($check_exists < 1) {
        $query = "SELECT interpreter.*, interpreter.id as main_id, interpreter_reg.name, canceled_orders.cancel_type_id, 
        canceled_orders.cancel_reason_id, canceled_orders.canceled_by, canceled_orders.canceled_date, 
        canceled_orders.canceled_reason, cr.po_req, (SELECT count(id) FROM canceled_orders WHERE job_id = interpreter.id AND job_type = 1 AND interpreter.orderCancelatoin = 1) as has_cancelled_reason
        FROM interpreter 
        INNER JOIN interpreter_reg ON interpreter.intrpName = interpreter_reg.id 
        LEFT JOIN canceled_orders ON canceled_orders.job_id = interpreter.id AND canceled_orders.job_type = 1 
        LEFT JOIN comp_reg cr ON cr.abrv = interpreter.orgName
        WHERE interpreter.multInvoicNo = '$multInvoicNo'
        ORDER BY interpreter.assignDate ASC";
    } else {
        $query = "SELECT mi.main_job_id as main_id, mi.category_type, mi.assign_date as assignDate, mi.language_source as source, mi.reg_name as name, mi.inch_person as inchPerson, mi.client_ref as orgRef, mi.units as C_hoursWorkd, mi.price_per_unit as C_rateHour, mi.cost_of_intrp as C_chargInterp, mi.travel_time_duration as C_travelTimeHour, mi.travel_time_rate_per_hr as C_travelTimeRate, mi.cost_of_travel_time as C_chargeTravelTime, mi.mileage as C_travelMile, mi.cost_of_mileage as C_chargeTravel, mi.transport_cost as C_travelCost, mi.admin_cost as C_admnchargs, mi.other_expense as C_otherCost, mi.po_order, mi.orderCancelation as orderCancelatoin, mi.canceled_date_time as canceled_date, mi.canceled_reason,cr.po_req, it.orgName, (SELECT count(id) FROM canceled_orders WHERE job_id = mi.main_job_id AND job_type = 1 AND mi.orderCancelation = 1) as has_cancelled_reason
        FROM mult_inv_items mi 
        LEFT JOIN interpreter AS it ON it.id   = mi.main_job_id
        LEFT JOIN comp_reg AS cr ON cr.abrv = it.orgName
        WHERE mi.invoice_no = '" . $multInvoicNo . "' AND mi.category_type = 'Face to Face'
        ORDER BY mi.assign_date ASC";
    }

    $result = mysqli_query($con, $query);

    $invoice_to = "";
    for ($kj = 0; $kj < count($name); $kj++) {
        $invoice_to .= "<p><strong>Invoice To: " . $name[$kj] . "</strong><br/>" . $buildingName[$kj] . "<br/>" . $line1[$kj] . "<br/>" . $line2[$kj] . "<br/>" . $streetRoad[$kj] . "<br/>" . $city[$kj] . "<br/>" . $postCode[$kj] . "<br/></p>";
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

    <?php
    $new_credit_note = $acttObj->read_specific("id , CONCAT(DATE_FORMAT(posted_on,'%y%m'),'_',LPAD(COUNT(id), 2, '0')) as credit_note_no", "credit_notes_income_invoices", "income_invoice_id = " . $id . " AND tbl = 'mult_inv'");

    if ((isset($new_credit_note['id']) && !empty($new_credit_note['id']))) {
        $credit_note_number = $new_credit_note['credit_note_no'];
    } else {
        $credit_note_number = "";
    }
    ?>

    <body>
        <div class="container-fluid">
            <div>
                <div class="col-sm-8 text-left">
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
                        <h4>
                            <label class="label label-info">
                                <?php echo ($multi_invoice_info['status'] != '') ? $multi_invoice_info['status'] : 'Pending' ?>
                            </label>
                        </h4>
                    </h2>
                    </h2>
                </div>

                <div class="col-md-4 col-sm-4 form-inline text-right m-t-20">
                    <div class="form-group">
                        <?php
                        if (($action_receive_payment || $action_receive_partial_payment)) { ?>

                            <!--select name="proceed" id="proceed" onchange="myFunction()" class="form-control">
                                <option value="">- Select Action -</option>
                                <option value="Received" <?php echo ($proceed == 'Received') ? 'selected' : ''; ?>>Payment Received</option>
                                <option value="Undo" <?php echo ($proceed == 'Undo') ? 'selected' : ''; ?>>Payment Undo</option>
                                <option value="Cancel" <?php echo ($proceed == 'Cancel') ? 'selected' : ''; ?>>Cancel</option>
                            </select-->
                        <?php } ?>
                        <a href="javascript:void(0)" onClick="popupwindow('reports_lsuk/pdf/rip_multiple_inv_pdf.php?multInvoiceNo=<?php echo $multInvoicNo; ?>', 'Print Invoice', 1200, 800);" title="Print" class="btn btn-sm btn-primary">
                            <i class="fa fa-print"></i> Print
                        </a>
                        <a href="reports_lsuk/excel/rip_multiple_inv_export.php?multInvoiceNo=<?php echo $multInvoicNo; ?>" title="Download Excel Report" class="btn btn-sm btn-success">
                            <i class="fa fa-download"></i> Download To Excel
                        </a>
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

            <div class="">
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
                                <td width="8%">' . $row["porder"];

                            if ($row['po_req']) {
                                echo '<p>
                                            <a href="javascript:void(0);" 
                                            onClick="popupwindow(\'purch_update.php?purch_id=' . $row['main_id'] . '&table=interpreter&orgName=' . $row['orgName'] . '&porder=' . $row["porder"] . '\', \'Update Purchase Order\', 600, 650);" 
                                            class="btn btn-primary btn-xs">
                                            Update Purchase Order
                                            </a>
                                        </p>';
                            } else {
                                echo "N/A";
                            }


                            echo '</td>
                                </tr>';

                            if ($row['orderCancelatoin'] == 1 && $row['has_cancelled_reason'] > 0) {
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
                        // if ($multi_invoice_info['commit'] == 1 || $multi_invoice_info['is_deleted'] == 0) {
                        //     $query_telep = "SELECT telephone.*, interpreter_reg.name, canceled_orders.cancel_type_id, 
                        //     canceled_orders.cancel_reason_id, canceled_orders.canceled_by, canceled_orders.canceled_date, 
                        //     canceled_orders.canceled_reason  
                        //     FROM telephone 
                        //     INNER JOIN interpreter_reg ON telephone.intrpName = interpreter_reg.id 
                        //     LEFT JOIN canceled_orders ON canceled_orders.job_id = telephone.id AND canceled_orders.job_type = 2
                        //     WHERE telephone.multInvoicNo = '$multInvoicNo'
                        //     ORDER BY telephone.assignDate ASC";
                        // } else {
                        //     $main_ids = json_decode($multi_invoice_info['main_ids_data'], true);

                        //     $query_telep = "SELECT telephone.*, interpreter_reg.name, canceled_orders.cancel_type_id, 
                        //     canceled_orders.cancel_reason_id, canceled_orders.canceled_by, canceled_orders.canceled_date, 
                        //     canceled_orders.canceled_reason  
                        //     FROM telephone 
                        //     INNER JOIN interpreter_reg ON telephone.intrpName = interpreter_reg.id 
                        //     LEFT JOIN canceled_orders ON canceled_orders.job_id = telephone.id AND canceled_orders.job_type = 2
                        //     WHERE telephone.id IN (" . $main_ids['TP'] . ")
                        //     ORDER BY telephone.assignDate ASC";
                        // }

                        if ($check_exists < 1) {
                            $query_telep = "SELECT 
                                                telephone.*, 
                                                telephone.id AS main_id,
                                                interpreter_reg.name, 
                                                canceled_orders.cancel_type_id, 
                                                canceled_orders.cancel_reason_id, 
                                                canceled_orders.canceled_by, 
                                                canceled_orders.canceled_date, 
                                                canceled_orders.canceled_reason,
                                                cr.po_req,
                                                telephone.orgName,
                                                (SELECT count(id) FROM canceled_orders WHERE job_id = telephone.id AND job_type = 1 AND telephone.orderCancelatoin = 1) as has_cancelled_reason
                                            FROM telephone 
                                            INNER JOIN interpreter_reg ON telephone.intrpName = interpreter_reg.id 
                                            LEFT JOIN canceled_orders ON canceled_orders.job_id = telephone.id AND canceled_orders.job_type = 2
                                            LEFT JOIN comp_reg AS cr ON cr.abrv = telephone.orgName
                                            WHERE telephone.multInvoicNo = '$multInvoicNo'
                                            ORDER BY telephone.assignDate ASC";
                        } else {
                            $query_telep = "SELECT 
                                                mi.main_job_id AS main_id,
                                                mi.category_type, 
                                                mi.assign_date AS assignDate, 
                                                mi.language_source AS source, 
                                                mi.reg_name AS name, 
                                                mi.inch_person AS inchPerson, 
                                                mi.client_ref AS orgRef, 
                                                mi.units AS C_hoursWorkd, 
                                                mi.price_per_unit AS C_rateHour, 
                                                mi.cost_of_intrp AS C_chargInterp, 
                                                mi.travel_time_duration AS C_travelTimeHour, 
                                                mi.travel_time_rate_per_hr AS C_travelTimeRate, 
                                                mi.cost_of_travel_time AS C_chargeTravelTime, 
                                                mi.mileage AS C_travelMile, 
                                                mi.cost_of_mileage AS C_chargeTravel, 
                                                mi.transport_cost AS C_travelCost, 
                                                mi.admin_cost AS C_admnchargs, 
                                                mi.other_expense AS C_otherCost, 
                                                mi.po_order, 
                                                mi.orderCancelation AS orderCancelatoin, 
                                                mi.canceled_date_time AS canceled_date, 
                                                mi.canceled_reason,
                                                cr.po_req,
                                                it.orgName,
                                                (SELECT count(id) FROM canceled_orders WHERE job_id = mi.main_job_id AND job_type = 1 AND mi.orderCancelation = 1) as has_cancelled_reason
                                            FROM mult_inv_items mi 
                                            LEFT JOIN telephone AS it ON it.id = mi.main_job_id
                                            LEFT JOIN comp_reg AS cr ON cr.abrv = it.orgName
                                            WHERE mi.invoice_no = '$multInvoicNo' 
                                            AND mi.category_type = 'Remote'
                                            ORDER BY mi.assign_date ASC";
                        }




                        $result_telep = mysqli_query($con, $query_telep);
                        while ($row = mysqli_fetch_assoc($result_telep)) {

                            $withou_VAT_telp = $row["C_otherCharges"];
                            $non_vat_tlep = $row["total_charges_comp"] - $row["C_otherCharges"];


                            $C_otherCharges_CallCharges = $row['C_callcharges'] + $row['C_otherCharges'];

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
                                <td width="8%">' . $row["porder"];

                            if ($row['po_req']) {
                                echo '<p>
                                            <a href="javascript:void(0);" 
                                            onClick="popupwindow(\'purch_update.php?purch_id=' . $row['main_id'] . '&table=telephone&orgName=' . $row['orgName'] . '&porder=' . $row["porder"] . '\', \'Update Purchase Order\', 600, 650);" 
                                            class="btn btn-primary btn-xs">
                                            Update Purchase Order
                                            </a>
                                        </p>';
                            } else {
                                echo "N/A";
                            }


                            echo '</td>
                                </tr>';
                            if ($row['orderCancelatoin'] == 1 && $row['has_cancelled_reason'] > 0) {
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
                        // if ($multi_invoice_info['commit'] == 1 || $multi_invoice_info['is_deleted'] == 0) {
                        //     $query_trans = "SELECT translation.*, interpreter_reg.name, canceled_orders.cancel_type_id, 
                        //     canceled_orders.cancel_reason_id, canceled_orders.canceled_by, canceled_orders.canceled_date, 
                        //     canceled_orders.canceled_reason   
                        //     FROM translation 
                        //     INNER JOIN interpreter_reg ON translation.intrpName = translation.id 
                        //     LEFT JOIN canceled_orders ON canceled_orders.job_id = translation.id AND canceled_orders.job_type = 3
                        //     WHERE translation.multInvoicNo = '$multInvoicNo' 
                        //     ORDER BY translation.asignDate ASC";
                        // } else {
                        //     $main_ids = json_decode($multi_invoice_info['main_ids_data'], true);
                        //     $query_trans = "SELECT translation.*, interpreter_reg.name, canceled_orders.cancel_type_id, 
                        //     canceled_orders.cancel_reason_id, canceled_orders.canceled_by, canceled_orders.canceled_date, 
                        //     canceled_orders.canceled_reason   
                        //     FROM translation 
                        //     INNER JOIN interpreter_reg ON translation.intrpName = translation.id 
                        //     LEFT JOIN canceled_orders ON canceled_orders.job_id = translation.id AND canceled_orders.job_type = 3
                        //     WHERE translation.id IN (" . $main_ids['TR'] . ")
                        //     ORDER BY translation.asignDate ASC";
                        // }

                        if ($check_exists < 1) {
                            $query_trans = "SELECT 
                                                translation.*, 
                                                translation.id AS main_id,
                                                interpreter_reg.name, 
                                                canceled_orders.cancel_type_id, 
                                                canceled_orders.cancel_reason_id, 
                                                canceled_orders.canceled_by, 
                                                canceled_orders.canceled_date, 
                                                canceled_orders.canceled_reason,
                                                cr.po_req,
                                                translation.orgName,
                                                (SELECT count(id) FROM canceled_orders WHERE job_id = translation.id AND job_type = 1 AND translation.orderCancelatoin = 1) as has_cancelled_reason
                                            FROM translation 
                                            INNER JOIN interpreter_reg ON translation.intrpName = interpreter_reg.id 
                                            LEFT JOIN canceled_orders ON canceled_orders.job_id = translation.id AND canceled_orders.job_type = 3
                                            LEFT JOIN comp_reg AS cr ON cr.abrv = translation.orgName
                                            WHERE translation.multInvoicNo = '$multInvoicNo' 
                                            ORDER BY translation.asignDate ASC";
                        } else {
                            $query_trans = "SELECT 
                                                mi.main_job_id AS main_id,
                                                mi.category_type, 
                                                mi.assign_date AS assignDate, 
                                                mi.language_source AS source, 
                                                mi.reg_name AS name, 
                                                mi.inch_person AS orgContact, 
                                                mi.client_ref AS orgRef, 
                                                mi.units AS C_numberUnit, 
                                                mi.price_per_unit AS C_rpU, 
                                                mi.cost_of_intrp AS C_chargInterp, 
                                                mi.travel_time_duration AS C_travelTimeHour, 
                                                mi.travel_time_rate_per_hr AS C_travelTimeRate, 
                                                mi.cost_of_travel_time AS C_chargeTravelTime, 
                                                mi.mileage AS C_travelMile, 
                                                mi.cost_of_mileage AS C_chargeTravel, 
                                                mi.transport_cost AS C_travelCost, 
                                                mi.admin_cost AS C_admnchargs, 
                                                mi.other_expense AS C_otherCharg, 
                                                mi.po_order, 
                                                mi.orderCancelation AS orderCancelatoin, 
                                                mi.canceled_date_time AS canceled_date, 
                                                mi.canceled_reason,
                                                cr.po_req,
                                                tr.orgName,
                                                (SELECT count(id) FROM canceled_orders WHERE job_id = mi.main_job_id AND job_type = 1 AND mi.orderCancelation = 1) as has_cancelled_reason
                                            FROM mult_inv_items mi 
                                            LEFT JOIN translation AS tr ON tr.id = mi.main_job_id
                                            LEFT JOIN comp_reg AS cr ON cr.abrv = tr.orgName
                                            WHERE mi.invoice_no = '$multInvoicNo' 
                                            AND mi.category_type = 'Translation'
                                            ORDER BY mi.assign_date ASC";
                        }


                        $result_trans = mysqli_query($con, $query_trans);
                        while ($row = mysqli_fetch_assoc($result_trans)) {

                            $withou_VAT_trans = $row["C_otherCharg"];
                            $non_vat_trans = $row["total_charges_comp"] - $row["C_otherCharg"];

                            $trans_sub_total = ($row["C_numberUnit"] * $row["C_rpU"]) + $row["C_otherCharg"]; //$row["total_units"];
                            $trans_vat_percent = ($trans_sub_total - $row["C_otherCharg"]) * 0.2;
                            $trans_total_cost = $trans_sub_total + $trans_vat_percent;

                            $grand_sub_total += $trans_sub_total;
                            $grand_total_vat += $trans_vat_percent;
                            $grand_non_vat += $withou_VAT_trans;

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
                                <td width="8%">' . $row["porder"];

                            if ($row['po_req']) {
                                echo '<p>
                                            <a href="javascript:void(0);" 
                                            onClick="popupwindow(\'purch_update.php?purch_id=' . $row['main_id'] . '&table=translation&orgName=' . $row['orgName'] . '&porder=' . $row["porder"] . '\', \'Update Purchase Order\', 600, 650);" 
                                            class="btn btn-primary btn-xs">
                                            Update Purchase Order
                                            </a>
                                        </p>';
                            } else {
                                echo "N/A";
                            }


                            echo '</td>
                                </tr>';

                            if ($row['orderCancelatoin'] == 1 && $row['has_cancelled_reason'] > 0) {
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

    </body>

    </html>