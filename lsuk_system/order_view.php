<?php if (session_id() == '' || !isset($_SESSION)) {
    session_start();
}
include 'actions.php';
$table = $_GET['table'];
//Check if user has current action allowed
$allowed_type_idz = "1,15,28,70,82,113,173";
if ($_SESSION['is_root'] == 0) {
    $get_page_access = $obj->read_specific("id", "action_permissions", "user_id=" . $_SESSION['userId'] . " AND action_id IN (" . $allowed_type_idz . ")")['id'];
    if (empty($get_page_access)) {
        die("<center><h2 class='text-center text-danger'>You do not have access to <u>View Order Details</u> action for jobs!<br>Kindly contact admin for further process.</h2></center>");
    }
}
$view_id = @$_GET['view_id'];
if (isset($_GET['edited_date'])) {
    $append_hist = " and $table.edited_date='" . $_GET['edited_date'] . "'";
}
if ($table == 'translation' || $table == 'hist_translation') {
    $tbl = 'tr';
} else if ($table == 'telephone' || $table == 'hist_telephone') {
    $tbl = 'tp';
} else {
    $tbl = 'int';
}
if ($table != 'telephone') {
    $row = $obj->read_specific("$table.*,comp_reg.buildingName as c_buildingName,comp_reg.line1 as c_line1,comp_reg.line2 as c_line2,comp_reg.streetRoad as c_streetRoad,comp_reg.city as c_city,comp_reg.postCode as c_postCode", "$table,comp_reg", "$table.orgName=comp_reg.abrv AND $table.id=$view_id" . $append_hist);
} else {
    $row = $obj->read_specific("$table.*,comunic_types.*,comp_reg.buildingName as c_buildingName,comp_reg.line1 as c_line1,comp_reg.line2 as c_line2,comp_reg.streetRoad as c_streetRoad,comp_reg.city as c_city,comp_reg.postCode as c_postCode", "telephone,comp_reg,comunic_types", "$table.orgName=comp_reg.abrv AND $table.comunic=comunic_types.c_id AND $table.id=$view_id" . $append_hist);
}
$source = $row['source'];
$target = $row['target'];
$assignDate = $tbl != 'tr' ? $row['assignDate'] : $row['asignDate'];
$assignTime = $row['assignTime'];
$assignDur = $row['assignDur'];
$nameRef = $row['nameRef'];
$noClient = $row['noClient'];
$contactNo = $row['contactNo'];
$inchPerson = $row['inchPerson'];
$inchContact = $row['inchContact'];
$inchEmail = $row['inchEmail'];
$inchEmail2 = $row['inchEmail2'];
$inchNo = $row['inchNo'];
$line1 = $row['line1'];
$line2 = $row['line2'];
$inchRoad = $row['inchRoad'];
$inchCity = $row['inchCity'];
$inchPcode = $row['inchPcode'];
$orgName = $row['orgName'];
$orgRef = $row['orgRef'];
$orgContact = $row['orgContact'];
$remrks = $row['remrks'];
$gender = $row['gender'];
$jobStatus = $row['jobStatus'];
$company_rate_id = $row['company_rate_id'];
$company_rate_data = !empty($row['company_rate_data']) ? (array) json_decode($row['company_rate_data']) : array();
if (!empty($company_rate_data['title'])) {
    $extra_title_parts = explode("-", $company_rate_data['title']);
    $bookinType = trim($extra_title_parts[0]);
} else {
    $bookinType = $row['bookinType'];
}
$I_Comments = $row['I_Comments'];
$comunic = $row['comunic'];
$c_title = $row['c_title'];
$c_image = $row['c_image'];
$assignIssue = $row['assignIssue'];
$jobDisp = $row['jobDisp'];
$invoiceNo = $row['invoiceNo'];
$bookedVia = $row['bookedVia'];
$docType = $row['docType'];
$transType = $row['transType'];
$trans_detail = $row['trans_detail'];
$deliverDate = $row['deliverDate'];
$deliverDate_int = $row['deliverDate2'];
$deliveryType = $row['deliveryType'];
$dbs_checked = $row['dbs_checked'];
$buildingName = $row['buildingName'];
$street = $row['street'];
$assignCity = $row['assignCity'];
$postCode = $row['postCode'];
$c_buildingName = $row['c_buildingName'];
$c_line1 = $row['c_line1'];
$hostedBy = '';
if ($tbl == 'tp')
    $hostedBy = $row['hostedBy'];
$c_line2 = $row['c_line2'];
$c_streetRoad = $row['c_streetRoad'];
$c_city = $row['c_city'];
$c_postCode = $row['c_postCode'];
$disp_org = $obj->read_specific("name", "comp_reg", "abrv='" . $orgName . "'");
if ($row['new_comp_id'] != 0) {
    $private_company = $obj->read_specific("*", "private_company", "id=" . $row['new_comp_id']);
    $orgName = $private_company['name'];
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
} ?>
<!doctype html>
<html lang="en">

<head>
    <title>View Order</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" />
</head>

<body>
    <div class="container"><br>
        <div class="row">
            <div class="col-sm-5">
                <div class="well">
                    <h4><?php echo $orgName ?></h4>
                    <p>Client Ref/Name: <b><?php echo $orgRef ?></b></p>
                    <p>Contact Name: <b><?php echo $orgContact ?></b></p>
                    <?php if ($tbl == 'tp') { ?>
                        <p>Client: <b><?php echo $noClient ?></b></p>
                        <p>Service User: <b><?php echo $contactNo ?: 'NIL' ?></b></p>
                    <?php } ?>
                </div>
                <div>
                    <table class="table table-bordered table-hover">
                        <tbody>
                            <tr>
                                <td colspan="2" class="text-center bg-info"><b>WORK DETAILS</b></td>
                            </tr>
                            <tr>
                                <td width="45%">Source Language</td>
                                <td class="text-right"><span class="label label-default" style="font-size:16px;"><?php echo $source; ?></span></td>
                            </tr>
                            <tr>
                                <td>Target Language</td>
                                <td class="text-right"><span class="label label-success" style="font-size:16px;"><?php echo $target; ?></span></td>
                            </tr>
                            <tr>
                                <td>Assignment Date</td>
                                <td class="text-right"><?php echo $assignDate; ?></td>
                            </tr>
                            <?php if ($tbl != 'tr') { ?>
                                <tr>
                                    <td>Assignment Time</td>
                                    <td class="text-right"><?php echo $assignTime; ?></td>
                                </tr>
                                <tr>
                                    <td>Assignment Duration</td>
                                    <td class="text-right"><?php
                                                            if ($assignDur > 60) {
                                                                $hours = $assignDur / 60;
                                                                if (floor($hours) > 1) {
                                                                    $hr = "hours";
                                                                } else {
                                                                    $hr = "hour";
                                                                }
                                                                $mins = $assignDur % 60;
                                                                if ($mins == 00) {
                                                                    $get_dur = sprintf("%2d $hr", $hours);
                                                                } else {
                                                                    $get_dur = sprintf("%2d $hr %02d minutes", $hours, $mins);
                                                                }
                                                            } else if ($assignDur == 60) {
                                                                $get_dur = "1 Hour";
                                                            } else {
                                                                $get_dur = $assignDur . " minutes";
                                                            }
                                                            echo $get_dur; ?>
                                    </td>
                                </tr>
                            <?php } ?>
                            <?php if ($tbl == 'tr') { ?>
                                <tr>
                                    <td>Delivery Date (Client)</td>
                                    <td class="text-right"><?php echo $deliverDate ?: 'NIL' ?></td>
                                </tr>
                                <tr>
                                    <td>Delivery Date (Interpreter)</td>
                                    <td class="text-right"><?php echo $deliverDate_int ?: 'NIL' ?></td>
                                </tr>
                                <tr>
                                    <td>Document Type</td>
                                    <td class="text-right"><?php echo $obj->read_specific("tc_title", "trans_cat", "tc_id=" . $docType)['tc_title']; ?></td>
                                </tr>
                                <tr>
                                    <td>Translation Type</td>
                                    <td class="text-right"><?php echo $obj->read_specific("CONCAT(GROUP_CONCAT(CONCAT('{',td_title)  SEPARATOR '} '),'}') as td_title", "trans_dropdown", "td_id IN (" . $transType . ")")['td_title']; ?></td>
                                </tr>
                                <?php if (!empty($trans_detail)) { ?>
                                    <tr>
                                        <td>Translation Details</td>
                                        <td class="text-right"><?php echo $obj->read_specific("CONCAT(GROUP_CONCAT(CONCAT('{',tt_title)  SEPARATOR '} '),'}') as tt_title", "trans_types", "tt_id IN (" . $trans_detail . ")")['tt_title']; ?></td>
                                    </tr>
                                <?php } ?>
                                <tr>
                                    <td>Delivery Type</td>
                                    <td class="text-right"><?php echo $deliveryType ?: 'NIL' ?></td>
                                </tr>
                            <?php } ?>
                            <tr>
                                <td>Invoice Number</td>
                                <td class="text-right"><?php echo $invoiceNo ?: 'Not created' ?></td>
                            </tr>
                            <tr>
                                <td>Our Reference</td>
                                <td class="text-right"><?php echo $nameRef ?: 'NIL' ?></td>
                            </tr>
                            <?php if ($tbl != 'tr') { ?>
                                <?php if ($tbl == 'tp') { ?>
                                    <tr>
                                        <td>Type</td>
                                        <td class="text-right"><img width="30" src="images/comunic_types/<?php echo $c_image ?>" /><?php echo $c_title ?></td>
                                    </tr>
                                <?php } ?>
                                <tr>
                                    <td>Assignment Category</td>
                                    <td class="text-right text-danger"><?php echo $tbl == 'tp' ? $obj->read_specific("tpc_title", "telep_cat", "tpc_id=" . $row['telep_cat'])['tpc_title'] : $obj->read_specific("ic_title", "interp_cat", "ic_id=" . $row['interp_cat'])['ic_title'] ?></td>
                                </tr>
                                <tr>
                                    <td>Assignment Details</td>
                                    <td class="text-right text-danger"><?php if ($tbl == 'tp') {
                                                                            echo $row['telep_cat'] == '11' ? $assignIssue : $obj->read_specific("GROUP_CONCAT(CONCAT(tpt_title)  SEPARATOR ' <b> & </b> ') as tpt_title", "telep_types", "tpt_id IN (" . $row['telep_type'] . ")")['tpt_title'];
                                                                        } else {
                                                                            echo $row['interp_cat'] == '12' ? $assignIssue : $obj->read_specific("GROUP_CONCAT(CONCAT(it_title)  SEPARATOR ' <b> & </b> ') as it_title", "interp_types", "it_id IN (" . $row['interp_type'] . ")")['it_title'];
                                                                        } ?></td>
                                </tr>
                            <?php } ?>
                            <?php if ($tbl == 'int') { ?>
                                <tr>
                                    <td class="text-center" colspan="2" title="Building No / Name"><i class="fa fa-map-marker"></i> <b>Address</td>
                                </tr>
                                <tr>
                                    <td class="text-left" colspan="2" title="Street / Road Address"><i class="fa fa-road"></i> <?php echo !empty(trim($buildingName)) ?$buildingName: '' ?><?php echo !empty(trim($street))?(', '.$street):''; ?><?php echo !empty(trim($assignCity))?(', '.$assignCity):''; ?><?php echo !empty(trim($postCode))?(', '.$postCode):''; ?></td>
                                </tr>
                                <tr>
                                    <td class="text-left" colspan="2" title="City Name"><i class="fa fa-road"></i> <b>City:</b> <?php echo $assignCity; ?></td>
                                </tr>
                                <tr>
                                    <td class="text-left" colspan="2" title="Post Code"><i class="fa fa-map-pin"></i> <b>Post Code:</b> <?php echo $postCode; ?></td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="col-sm-7">

                <div class="row">
                    <div class="col-sm-12">
                        <div class="panel panel-default text-center">
                            <div class="panel-body">
                                <h4><?php echo $disp_org['name']; ?></h4>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-12">
                        <table class="table table-bordered table-hover">
                            <tbody class="text-left">
                                <tr>
                                    <td colspan="4" class="text-center bg-info"><b>ASSIGNMENT IN-CHARGE</b></td>
                                </tr>
                                <?php if ($tbl != 'tr') { ?>
                                    <tr>
                                        <td>Contact Person</td>
                                        <td colspan="3"><?php echo $inchPerson; ?></td>
                                    </tr>
                                <?php } ?>
                                <tr>
                                    <td>Link</td>
                                    <td colspan="3"><?php echo ($row['communication_link']) ? $row['communication_link'] : "Nil" ?></td>
                                </tr>
                                <tr>
                                    <td>Assign By</td>
                                    <td colspan="3"><?php echo ($row['hostedBy'] == '1') ? '<span class="label label-primary">LSUK</span>' : "<span class='label label-info'>Client</span>" ?></td>
                                </tr>
                                <tr>
                                    <td width="26%">Contact Number</td>
                                    <td colspan="3"><?php echo $inchContact; ?></td>
                                </tr>
                                <tr>
                                    <td>Email Address</td>
                                    <td colspan="3"><?php echo $inchEmail; ?></td>
                                </tr>
                                <?php if (!empty($inchEmail2)) { ?>
                                    <tr>
                                        <td>Email Address 2</td>
                                        <td colspan="3"><?php echo $inchEmail2; ?></td>
                                    </tr>
                                <?php } ?>
                                <tr>
                                    <td colspan="4" class="text-center" title="In-charge Building No / Name"><i class="fa fa-map-marker"></i> <b>Address</td>
                                </tr>
                                <tr>
                                    <td class="text-center" colspan="4"><?php echo !empty(trim($c_buildingName)) ?$c_buildingName: '' ?><?php echo !empty(trim($c_line1)) ?(', '.$c_line1): ''; ?><?php echo !empty(trim($c_line2)) ?(', '.$c_line2): ''; ?><?php echo !empty(trim($c_streetRoad)) ?(', '.$c_streetRoad): ''; ?><?php echo !empty(trim($c_city)) ?(', '.$c_city): ''; ?><?php echo !empty(trim($c_postCode)) ?(', '.$c_postCode): ''; ?></td>
                                </tr>
                                <tr>
                                    <td>City</td>
                                    <td colspan="3"><?php echo $c_city; ?></td>
                                </tr>
                                <tr>
                                    <td>Post Code</td>
                                    <td colspan="3"><?php echo $c_postCode; ?></td>
                                </tr>
                                <?php if ($tbl == 'tp') {
                                    $call_types = array('', 'LSUK to Host', 'Client to Host', 'Client to call LSUK');
                                ?>
                                    <tr>
                                        <td><b>Hosted By:</b></td>
                                        <td class="text-left" colspan="3" title="Hosted By"> <?php echo $call_types[$hostedBy]; ?></td>
                                    </tr>
                                <?php } ?>
                                <tr>
                                    <td colspan="4" class="text-center bg-info"><b>INTERPRETER DETAILS</b></td>
                                </tr>
                                <tr>
                                    <td>Booking Type</td>
                                    <td><?php echo $bookinType ?: 'NIL'; ?></td>
                                    <td>Gender</td>
                                    <td><?php echo $gender ?: 'NIL'; ?></td>
                                </tr>
                                <tr>
                                    <td>Booked Date</td>
                                    <td><?php echo date('Y-m-d', strtotime($row['bookeddate'])) ?></td>
                                    <td>Booked Time</td>
                                    <td><?php echo date('H:i a', strtotime($row['bookedtime'])) ?></td>
                                </tr>
                                <tr>
                                    <td>Booked Via</td>
                                    <td><?php echo $bookedVia ?: 'NIL'; ?></td>
                                    <td>Status</td>
                                    <td><?php echo $jobStatus == '0' ? '<span class="text-danger label label-danger" style="font-size: 14px;"> Enquiry <i class="fa fa-question"></i></span>' : '<span class="label label-success" style="font-size: 14px;"> <i class="fa fa-check-circle"></i> Confirmed</span>'; ?></td>
                                </tr>
                                <tr>
                                    <td>Auto Reminder ?</td>
                                    <td><?php echo $jobDisp == '0' ? '<span class="label label-danger" style="font-size: 14px;"> <i class="fa fa-remove"></i> No</span>' : '<span class="label label-success" style="font-size: 14px;"> <i class="fa fa-check-circle"></i> Yes</span>'; ?></td>
                                    <?php if ($tbl == 'int') { ?>
                                        <td>DBS checked ?</td>
                                        <td><?php echo $dbs_checked == '1' ? '<span class="label label-danger" style="font-size: 14px;"> <i class="fa fa-remove"></i> No</span>' : '<span class="label label-success" style="font-size: 14px;"> <i class="fa fa-check-circle"></i> Yes</span>'; ?></td>
                                </tr>
                            <?php } ?>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-sm-12">
                <div class="panel panel-default">
                    <ul class="list-group">
                        <li class="list-group-item list-group-item-info text-center"><b>NOTES</b></li>
                        <li class="list-group-item">
                            <label>NOTES FOR INTERPRETER </label>
                            <p><?php echo $remrks ?: 'NIL' ?></p>
                            <label>NOTES FOR CLIENT </label>
                            <p><?php echo $I_Comments ?: 'NIL' ?></p>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div><br><br><br><br>
</body>

</html>