<?php if (session_id() == '' || !isset($_SESSION)) {
    session_start();
}
include 'secure.php'; ?>
<?php include 'source/db.php';
include 'source/class.php';
$name = @$_GET['name'];
$gender = @$_GET['gender'];
$city = @$_GET['city'];
$interpreter_id = $_SESSION['web_userId']; ?>
<!DOCTYPE HTML>
<html class="no-js">

<head>
    <?php include 'source/header.php'; ?>
    <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" />
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.21/css/dataTables.bootstrap.min.css" />
    <style>
        select.input-sm {
            line-height: 22px;
        }

        .glyphicon {
            color: #fff;
        }

        input,
        textarea,
        select {
            -webkit-appearance: button;
        }

        .dataTables_wrapper .row {
            margin: 0px !important;
        }
    </style>
</head>

<body class="boxed">
    <div id="wrap">
        <?php include 'source/top_nav.php'; ?>
        <section id="page-title">
            <div class="container clearfix">
                <h1>Un-Paid Jobs List</h1>
                <nav id="breadcrumbs">
                    <ul>
                        <li><a href="interp_profile.php">Home</a> &rsaquo;</li>
                    </ul>
                </nav>
            </div>
        </section>

        <section>
            <center>
                <section style="overflow-x:auto;">
                    <?php $json = array("f2f" => array(), "tp" => array(), "tr" => array());
            //F2F
            $result = $acttObj->read_all("*", "(SELECT interpreter.orderCancelatoin as 'order_cancelled',comp_reg.name as company_name,interpreter.travelTimeRate,interpreter.rateMile,interpreter.rateHour,pay_int,interpreter.porder,interpreter.deduction,interpreter.total_charges_interp,comp_reg.po_req,'Interpreter' as type,interpreter.id,interpreter.chargInterp,interpreter.st_tm,interpreter.fn_tm,interpreter.target,interpreter_reg.rph,interpreter_reg.rpm,interpreter_reg.rpu,interpreter.travelTimeHour,interpreter.chargeTravelTime,interpreter.travelMile,interpreter.chargeTravel,interpreter.travelCost,interpreter.otherCost,interpreter.intrpName,interpreter_reg.name,interpreter_reg.id as interpreter_id,interpreter.source,interpreter.invoic_date,interpreter.assignDate,interpreter.assignTime,interpreter.orgContact,interpreter.submited, interpreter.aloct_by,interpreter.aloct_date,interpreter.dated,interpreter.hrsubmited,interpreter.comp_hrsubmited,interpreter.interp_hr_date, interpreter.comp_hr_date,interpreter.hoursWorkd,interpreter.C_hoursWorkd,interpreter.total_charges_comp,interpreter.cur_vat,interpreter.C_otherexpns, interpreter.credit_note,interpreter.admnchargs,interpreter.rAmount,interpreter.rDate,interpreter.sentemail,interpreter.printed,interpreter.printedby,interpreter.deleted_flag,interpreter.order_cancel_flag,interpreter.nameRef,interpreter.orgRef,interpreter.invoiceNo,interpreter.commit,comp_reg.email,comp_reg.abrv as comp_abrv FROM interpreter,interpreter_reg,comp_reg WHERE interpreter.intrpName=interpreter_reg.id AND interpreter.orgName=comp_reg.abrv and interpreter_reg.id = $interpreter_id AND interpreter.deleted_flag = 0 and interpreter.order_cancel_flag=0 AND ((interpreter.orderCancelatoin=1 and interpreter.pay_int=1) OR interpreter.orderCancelatoin=0) and interpreter.salary_id=0 and interpreter.pay_int=1 and interpreter.hoursWorkd>0) as grp", "1 ORDER BY CONCAT(assignDate,' ',assignTime)");
            while ($row = $result->fetch_assoc()) {
                $row['order_cancelled'] = $row['order_cancelled'] == 1 && $row['pay_int'] == 1 ? "1" : "0";
                $row['order_cancelled_message'] = $row['order_cancelled'] == 1 && $row['pay_int'] == 1 ? "This order has been cancelled. You will be paid" : "";
                $job_start_time = new DateTime($row['st_tm']);
                $diff = $job_start_time->diff(new DateTime($row['fn_tm']));
                array_push($json['f2f'], [
                    'assignDate' => date('d-m-Y', strtotime($row['assignDate'])),
                    'jobKey' => $row['nameRef'],
                    'company_name' => $row['company_name'],
                    'source' => $row['source'],
                    'target' => $row['target'],
                    'hours_worked' => $row['hoursWorkd'],
                    'rate_per_hour' => $row['rateHour'],
                    'interpreting_time_payment' => $row['chargInterp'],
                    'travel_hours' => $row['travelTimeHour'],
                    'travel_rate_per_hour' => $row['travelTimeRate'],
                    'travel_time_payment' => $row['chargeTravelTime'],
                    'travel_miles' => $row['travelMile'],
                    'travel_rate_per_mile' => $row['rateMile'],
                    'mileage_cost' => $row['chargeTravel'],
                    'other_costs' => $row['otherCost'],
                    'additional_payment' => $row['admnchargs'],
                    'deduction' => $row['deduction'],
                    'total_charges' => $row['total_charges_interp'],
                    'order_cancelled' => $row['order_cancelled'],
                    'order_cancelled_message' => $row['order_cancelled_message']
                ]);
            }
            //TP
            $result = $acttObj->read_all("*", "(SELECT telephone.orderCancelatoin as 'order_cancelled',comp_reg.name as company_name,telephone.chargInterp,telephone.calCharges,telephone.otherCharges,telephone.rateHour,pay_int,telephone.porder,telephone.deduction,telephone.total_charges_interp,comp_reg.po_req,'Telephone' as type,telephone.id,telephone.st_tm,telephone.fn_tm,telephone.target,interpreter_reg.rph,interpreter_reg.rpm,interpreter_reg.rpu,telephone.intrpName,interpreter_reg.name,interpreter_reg.id as interpreter_id,telephone.source,telephone.invoic_date,telephone.assignDate,telephone.assignTime,telephone.orgContact,telephone.submited, telephone.aloct_by,telephone.aloct_date,telephone.dated,telephone.hrsubmited,telephone.comp_hrsubmited,telephone.interp_hr_date,telephone.comp_hr_date, telephone.hoursWorkd,telephone.C_hoursWorkd,telephone.total_charges_comp,telephone.cur_vat,0 as C_otherexpns,telephone.credit_note,telephone.admnchargs, telephone.rAmount,telephone.rDate,telephone.sentemail,telephone.printed,telephone.printedby,telephone.deleted_flag,telephone.order_cancel_flag,telephone.nameRef,telephone.orgRef,telephone.invoiceNo,telephone.commit,comp_reg.email,comp_reg.abrv as comp_abrv FROM telephone,interpreter_reg,comp_reg WHERE telephone.intrpName=interpreter_reg.id AND telephone.orgName=comp_reg.abrv AND interpreter_reg.id = $interpreter_id AND telephone.deleted_flag = 0 and telephone.order_cancel_flag=0 AND ((telephone.orderCancelatoin=1 and telephone.pay_int=1) OR telephone.orderCancelatoin=0) and telephone.salary_id=0 and telephone.pay_int=1 and telephone.hoursWorkd>0) as grp", "1 ORDER BY CONCAT(assignDate,' ',assignTime)");
            while ($row = $result->fetch_assoc()) {
                $row['order_cancelled'] = $row['order_cancelled'] == 1 && $row['pay_int'] == 1 ? "1" : "0";
                $row['order_cancelled_message'] = $row['order_cancelled'] == 1 && $row['pay_int'] == 1 ? "This order has been cancelled. You will be paid" : "";
                $job_start_time = new DateTime($row['st_tm']);
                $diff = $job_start_time->diff(new DateTime($row['fn_tm']));
                array_push($json['tp'], [
                    'assignDate' => date('d-m-Y', strtotime($row['assignDate'])),
                    'jobKey' => $row['nameRef'],
                    'company_name' => $row['company_name'],
                    'source' => $row['source'],
                    'target' => $row['target'],
                    'total_minutes' => $row['hoursWorkd'],
                    'rate_per_minute' => $row['rateHour'],
                    'interpreting_time_payment' => $row['chargInterp'],
                    'call_charges' => $row['calCharges'],
                    'other_costs' => $row['otherCharges'],
                    'additional_payment' => $row['admnchargs'],
                    'deduction' => $row['deduction'],
                    'total_charges' => $row['total_charges_interp'],
                    'order_cancelled' => $row['order_cancelled'],
                    'order_cancelled_message' => $row['order_cancelled_message']
                ]);
            }
            //TR
            $result = $acttObj->read_all("*", "(SELECT translation.orderCancelatoin as 'order_cancelled',comp_reg.name as company_name,translation.numberUnit,translation.otherCharg,pay_int,translation.porder,translation.deduction,comp_reg.po_req,'Translation' as type,translation.id,translation.docType,translation.target,interpreter_reg.rpu,translation.total_charges_interp,translation.intrpName,interpreter_reg.name,interpreter_reg.id as interpreter_id,translation.source,translation.invoic_date,translation.asignDate as assignDate,'00:00:00' as assignTime,translation.orgContact,translation.submited, translation.aloct_by,translation.aloct_date,translation.dated,translation.hrsubmited,translation.comp_hrsubmited,translation.interp_hr_date,translation.comp_hr_date,translation.rpU as translation_rpu, translation.numberUnit as hoursWorkd,translation.C_numberUnit as C_hoursWorkd,translation.total_charges_comp,translation.cur_vat,0 as C_otherexpns,translation.credit_note,translation.admnchargs, translation.rAmount,translation.rDate,translation.sentemail,translation.printed,translation.printedby,translation.deleted_flag,translation.order_cancel_flag,translation.nameRef,translation.orgRef,translation.invoiceNo,translation.commit,comp_reg.email,comp_reg.abrv as comp_abrv FROM translation,interpreter_reg,comp_reg WHERE translation.intrpName=interpreter_reg.id AND translation.orgName=comp_reg.abrv AND interpreter_reg.id = $interpreter_id AND translation.deleted_flag = 0 and translation.order_cancel_flag=0 AND ((translation.orderCancelatoin=1 and translation.pay_int=1) OR translation.orderCancelatoin=0) and translation.salary_id=0 and translation.pay_int=1 and translation.numberUnit>0) as grp", "1 ORDER BY CONCAT(assignDate,' ',assignTime)");
            while ($row = $result->fetch_assoc()) {
                $row['order_cancelled'] = $row['order_cancelled'] == 1 && $row['pay_int'] == 1 ? "1" : "0";
                $row['order_cancelled_message'] = $row['order_cancelled'] == 1 && $row['pay_int'] == 1 ? "This order has been cancelled. You will be paid" : "";
                $docType = $row['docType'];
                $row['document_type'] = $acttObj->read_specific("trans_cat.tc_title as document_type", "trans_cat", "trans_cat.tc_id IN (" . $row['docType'] . ")")['document_type'];
                $translation_rpu = $row['translation_rpu'];
                $interp_rpu = $row['rpu'];
                if ($translation_rpu != 0) {
                    $row['rate_per_unit'] = $translation_rpu;
                } else {
                    $row['rate_per_unit'] = $interp_rpu;
                }
                array_push($json['tr'], [
                    'assignDate' => date('d-m-Y', strtotime($row['assignDate'])),
                    'jobKey' => $row['nameRef'],
                    'company_name' => $row['company_name'],
                    'document_type' => $row['document_type'],
                    'source' => $row['source'],
                    'target' => $row['target'],
                    'total_units' => $row['numberUnit'],
                    'rate_per_unit' => $row['rate_per_unit'],
                    'other_costs' => $row['otherCharg'],
                    'additional_payment' => $row['admnchargs'],
                    'deduction' => $row['deduction'],
                    'total_charges' => strval($row['total_charges_interp'] + $row['admnchargs']),
                    'order_cancelled' => $row['order_cancelled'],
                    'order_cancelled_message' => $row['order_cancelled_message']
                ]);
            }
                    if (count($json['f2f']) > 0 || count($json['tp']) > 0 || count($json['tr']) > 0) { ?>
                        <table class="table table-bordered table-hover">
                            <thead class="bg-primary">
                                <tr>
                                    <th>LSUK JOB ID</th>
                                    <th>Source</th>
                                    <th>Target</th>
                                    <th>Company</th>
                                    <th>Hours/Units</th>
                                    <th>Interpreting Charges</th>
                                    <th>Other Charges</th>
                                    <th>Additional</th>
                                    <th>Total Charges</th>
                                    <th>Job Type</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($json['f2f'] as $row_f2f) {?>
                                    <tr>
                                        <td><?php echo $row_f2f['jobKey']; ?></td>
                                        <td><?php echo $row_f2f['source']; ?></td>
                                        <td><?php echo $row_f2f['target']; ?></td>
                                        <td><?php echo $row_f2f['assignDate']; ?></td>
                                        <td><?php echo $row_f2f['hours_worked']; ?></td>
                                        <td><?php echo $row_f2f['interpreting_time_payment']; ?></td>
                                        <td><?php echo "Travel Charges: " . $row_f2f['travel_time_payment']; ?></td>
                                        <td><?php echo $row_f2f['additional_payment']; ?></td>
                                        <td><b><?php echo $row_f2f['total_charges']; ?></b></td>
                                        <td><span class="label label-success">Face To Face</span></td>
                                    </tr>
                                <?php }
                                    foreach ($json['tp'] as $row_tp) {?>
                                      <tr>
                                          <td><?php echo $row_tp['jobKey']; ?></td>
                                          <td><?php echo $row_tp['source']; ?></td>
                                          <td><?php echo $row_tp['target']; ?></td>
                                          <td><?php echo $row_tp['assignDate']; ?></td>
                                          <td><?php echo $row_tp['total_minutes']; ?></td>
                                          <td><?php echo $row_tp['interpreting_time_payment']; ?></td>
                                          <td><?php echo "Call Charges: " . $row_tp['call_charges']; ?></td>
                                          <td><?php echo $row_tp['additional_payment']; ?></td>
                                          <td><b><?php echo $row_tp['total_charges']; ?></b></td>
                                          <td><span class="label label-primary">Telephone</span></td>
                                      </tr>
                                  <?php }
                                      foreach ($json['tr'] as $row_tr) {?>
                                        <tr>
                                            <td><?php echo $row_tr['jobKey']; ?></td>
                                            <td><?php echo $row_tr['source']; ?></td>
                                            <td><?php echo $row_tr['target']; ?></td>
                                            <td><?php echo $row_tr['assignDate']; ?></td>
                                            <td><?php echo $row_tr['total_units']; ?></td>
                                            <td><?php echo $row_tr['rate_per_unit'] * $row_tr['total_units']; ?></td>
                                            <td><?php echo "Type: " . $row_tr['document_type']; ?></td>
                                            <td><?php echo $row_tr['additional_payment']; ?></td>
                                            <td><b><?php echo $row_tr['total_charges']; ?></b></td>
                                            <td><span class="label label-info">Translation</span></td>
                                        </tr>
                                    <?php }
                            } else { ?>
                                <div class="alert alert-warning text-center h3 col-sm-6 col-sm-offset-3">Sorry ! There are no unpaid jobs yet.
                                    <br><br><a class="btn btn-info" href="interp_profile.php"><i class="glyphicon glyphicon-home"></i> Go to Profile Page</a>
                                </div>
                            <?php } ?>
                            </tbody>

                        </table>
                </section>
            </center>
            <hr>

            <!-- begin clients -->
            <?php //include'source/our_client.php'; 
            ?>
            <!-- end clients -->
        </section>
        <!-- end content -->

        <!-- begin footer -->
        <?php include 'source/footer.php'; ?>
        <!-- end footer -->
    </div>
    <script src="lsuk_system/js/jquery-1.11.3.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.20/js/dataTables.bootstrap.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.table').DataTable({
                drawCallback: function() {
                    $('[data-toggle="popover"]').popover({
                        html: true
                    });
                    $('[data-toggle="tooltip"]').tooltip();
                },
                "bSort": false
            });
        });
    </script>
</body>

</html>