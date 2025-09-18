<?php
include "userhaspage.php";
SysPermiss::UserHasPage(__FILE__);
if (session_id() == '' || !isset($_SESSION)) {
    session_start();
}
include 'db.php';
include 'class.php';
//Access actions
$get_actions = explode(",", $acttObj->read_specific("GROUP_CONCAT(action_permissions.action_id) as actions", "action_permissions,route_actions", "action_permissions.action_id=route_actions.id AND route_actions.route_id=5 AND action_permissions.user_id=" . $_SESSION['userId'])['actions']);
$action_view_job = $_SESSION['is_root'] == 1 || in_array(15, $get_actions);
$action_edit_job = $_SESSION['is_root'] == 1 || in_array(16, $get_actions);
$action_delete_job = $_SESSION['is_root'] == 1 || in_array(17, $get_actions);
$action_restore_job = $_SESSION['is_root'] == 1 || in_array(18, $get_actions);
$action_duplicate = $_SESSION['is_root'] == 1 || in_array(19, $get_actions);
$action_resume_job = $_SESSION['is_root'] == 1 || in_array(20, $get_actions);
$action_assign_interpreter = $_SESSION['is_root'] == 1 || in_array(21, $get_actions);
$action_view_applicants = $_SESSION['is_root'] == 1 || in_array(22, $get_actions);
$action_confirm_enquiry = $_SESSION['is_root'] == 1 || in_array(23, $get_actions);
$action_confirm_temporary = $_SESSION['is_root'] == 1 || in_array(24, $get_actions);
$action_reallocate_operator = $_SESSION['is_root'] == 1 || in_array(25, $get_actions);
$action_check_job = $_SESSION['is_root'] == 1 || in_array(26, $get_actions);
$action_job_note = $_SESSION['is_root'] == 1 || in_array(27, $get_actions);
$action_force_assign_interpreter = $_SESSION['is_root'] == 1 || in_array(145, $get_actions);
$action_can_view_all_jobs = $_SESSION['is_root'] == 1 || in_array(146, $get_actions);
$action_dropdown_trashed_jobs_filter = $_SESSION['is_root'] == 1 || in_array(151, $get_actions);
$action_dropdown_cancelled_jobs_filter = $_SESSION['is_root'] == 1 || in_array(152, $get_actions);
$action_dropdown_enquiry_jobs_filter = $_SESSION['is_root'] == 1 || in_array(227, $get_actions);
$action_edited_history = $_SESSION['is_root'] == 1 || in_array(156, $get_actions);
$action_shift_order = $_SESSION['is_root'] == 1 || in_array(189, $get_actions);
//Access actions
$tp = @$_GET['tp'];
$table = 'telephone';
$deleted_flag = $tp == 'tr' ? ' AND deleted_flag = 1 ' : ' AND deleted_flag = 0 ';
$order_cancel_flag = $tp == 'c' ? ' AND order_cancel_flag = 1 ' : ' AND order_cancel_flag = 0 ';
$array_tp = array('a' => 'Active', 'tr' => 'Trashed', 'c' => 'Cancelled');
function EchoFieldTd($strCol, $map)
{
    try {
        $val = "";
        if ($map && isset($map[$strCol])) {
            $val = $map[$strCol];
        }

        $val = strip_tags($val);

        echo "<td>" . $val . "</td>";
    } catch (Exception $e) {
        echo "<td>/td>";
    }
}
function mak_dated($val)
{
    if ($val == '0000-00-00' || $val == "30-11--0001") {
        return 'Not yet fixed!';
    } else {
        return $dated = date_format(date_create($val), 'd-m-Y');
    }
}

function EchoValdTd($val)
{
    try {
        $valis = "";
        if (isset($val)) {
            $valis = $val;
        }

        echo "<td>" . $valis . "</td>";
    } catch (Exception $e) {
        echo "<td>/td>";
    }
}
function GetMapped($map, $named, $default)
{
    try {
        if (!isset($map[$named])) {
            return $default;
        }

        return $map[$named];
    } catch (Exception $e) {
        return $default;
    }
}
?>
<!doctype html>
<html lang="en">
<?php include 'header.php'; ?>
<style>
    .tablesorter thead tr {
        background: none;
    }

    ul.pagination li a {
        background: none;
        border-radius: 0px;
    }

    .fw {
        color: black !important;
    }

    .tbl_data td {
        border-bottom: 1px solid #337ab7 !important;
    }

    html,
    body {
        background: none;
    }
    .modal-open {
        overflow: initial !important;
    }
</style>
<link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" />
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.20/css/dataTables.bootstrap.min.css" />

<body>
    <?php include 'nav2.php'; ?>
    <section class="container-fluid" style="overflow-x:auto">
        <div class="col-sm-12">
            <div class="">
                <?php include 'inc_home_link.php'; ?>
                <div class="form-group col-md-2 col-sm-4 pull-right">
                    <?php if ($action_dropdown_trashed_jobs_filter || $action_dropdown_cancelled_jobs_filter) { ?>
                        <select id="tp" onChange="filter_list()" name="tp" class="form-control">
                            <option <?= !$tp ? 'selected' : '' ?> value="" <?=empty($tp)?'selected':''?>>Filter By Action</option>
                            <option <?= $tp == 'a' ? 'selected' : '' ?> value="a">Active Jobs</option>
                            <option <?=$action_dropdown_trashed_jobs_filter?'':'hidden'?> <?= $tp == 'tr' ? 'selected' : '' ?> value="tr">Trashed Jobs</option>
                            <option <?=$action_dropdown_cancelled_jobs_filter?'':'hidden'?> <?= $tp == 'c' ? 'selected' : '' ?> value="c">Cancelled Jobs</option>
                            <option <?=$action_dropdown_enquiry_jobs_filter?'':'hidden'?> <?= $tp == 'en' ? 'selected' : '' ?> value="en">Enquiry Jobs</option>
                        </select>
                    <?php } else { ?>
                        <input type="hidden" value='' id="tp" onChange="myFunction()" name="tp" class="form-control" />
                    <?php } ?>
                </div>
                <br><br>
                <table class="tablesorter table table-bordered tbl_data" cellspacing="0" cellpadding="0">
                    <thead class="bg-primary">
                        <tr>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>

                        <?php 
                        if (isset($_GET['tp']) && $_GET['tp'] == 'en'){
                            $result = $acttObj->read_all("$table.*", "$table", "(assignDate >= CURDATE() or  bookeddate >= CURDATE() ) and(jobStatus = 0)" . $deleted_flag . $order_cancel_flag . " ORDER BY $table.assignDate ASC LIMIT 100");
                        }
                        else if (isset($_GET['tp']) && $_GET['tp'] != 'a') {
                            $result = $acttObj->read_all("$table.*,comunic_types.*", "telephone,comunic_types", "$table.comunic=comunic_types.c_id AND ($table.intrpName='' OR $table.intrpName IS NULL) " . $deleted_flag . $order_cancel_flag . " ORDER BY $table.dated DESC LIMIT 100");
                        } else {
                            $result = $acttObj->read_all("$table.*,comunic_types.*", "telephone,comunic_types", "$table.comunic=comunic_types.c_id AND ($table.intrpName='' OR $table.intrpName IS NULL) " . $deleted_flag . $order_cancel_flag . " ORDER BY $table.assignDate ASC LIMIT 100");
                        }
                        $rowcount = 0;
                        $req_date1 = date('Y-m-d', strtotime(date('Y-m-d') . ' +1 day'));
                        $req_date2 = date('Y-m-d', strtotime(date('Y-m-d') . ' +2 days'));
                        $req_date3 = date('Y-m-d', strtotime(date('Y-m-d') . ' +3 days'));
                        $req_date4 = date('Y-m-d', strtotime(date('Y-m-d') . ' +4 days'));
                        while ($row = mysqli_fetch_array($result)) {
                            $row_assigned = $acttObj->read_specific("assigned_jobs_users.id as assigned_row_id,assigned_jobs_users.user_id,assigned_jobs_users.assigned_date,login.name as assigned_to", "assigned_jobs_users,login", "assigned_jobs_users.user_id=login.id AND assigned_jobs_users.order_type=2 AND assigned_jobs_users.order_id=" . $row['id']);
                            $row = $row_assigned ? array_merge($row, $row_assigned) : $row;
                            if ($_SESSION['is_root'] == 0 && !$action_can_view_all_jobs) {
                                if (!empty($row['user_id'])) {
                                    if ($_SESSION['userId'] != $row['user_id']) {
                                        if (stripos($row['submited'], $_SESSION['UserName']) === FALSE) {
                                            continue;
                                        }
                                    }
                                }
                            }
                            $tracking = GetMapped($row, 'id', "");
                            $g_row = &$row;
                            $urgent = (date('Y-m-d') == $row['assignDate'] || $req_date1 == $row['assignDate'] || $req_date2 == $row['assignDate'] || $req_date3 == $row['assignDate'] || $req_date4 == $row['assignDate']) ? 'class="bg-danger"' : '';
                            if (isset($row['bookedVia']) && $row['bookedVia'] == 'Online Portal') {
                                echo '<tr style="color:#F00" ' . $urgent . '>';
                            } else {
                                echo '<tr ' . $urgent . '>';
                            }

                            $map = $row; ?>

                            <td <?php if ($row['is_temp'] == 1) { ?>title="This job is booked by Temporary Role. Kindly confirm to process." style="background-color:#cbda78;" <?php } ?>>
                                <div class="col-lg-4 col-md-4 col-sm-4">
                                    <ul class="w3-ul">
                                        <li>Language <?php echo '<span class="label label-default w3-large w3-right">' . $row['source'] . ' <i class="fa fa-refresh"></i> ' . $row['target'] . '</span>'; ?></li>
                                        <li>Booking Person <span class="w3-right"><?php echo $row['inchPerson']; ?></span></li>
                                        <li>Booking Ref/Name <span class="w3-right <?php echo strlen($row['orgRef']) > 15 ? 'w3-small' : ''; ?>"><?php echo $row['orgRef']; ?></span></li>
                                        <?php
                                        //convert assignDuration to hours and minutes 
                                        $assignDuration = (int)$row['assignDur']; // total minutes
                                        $hours = intdiv($assignDuration, 60);
                                        $minutes = $assignDuration % 60;

                                        $parts = [];
                                        if ($hours > 0)   $parts[] = $hours . ' hour'  . ($hours > 1 ? 's' : '');
                                        if ($minutes > 0) $parts[] = $minutes . ' minute' . ($minutes > 1 ? 's' : '');

                                        $assignDuration = $parts ? implode(' ', $parts) : '0 minutes';
                                        $redLable = ($hours > 3) ? 'w3-red' : 'w3-green';
                                        ?>
                                        <li style="font-size: 12px;">Duration <span class="w3-right"> <span class="label <?= $redLable ?>"><?= $assignDuration ?></span> </span> </li>
                                        <li>Assigned To <?php if ($action_reallocate_operator) { ?><button data-operator-id="<?= $row['user_id'] ?>" data-operator-name="<?= $row['assigned_to'] ?: 'None' ?>" data-assigned-row-id="<?= $row['assigned_row_id'] ?>" data-order-id="<?= $row['id'] ?>" onclick="re_allocate_job(this)" type="button" class="btn btn-primary btn-xs" title="Click to re-allocate"><i class="fa fa-edit"></i></button> <?php } ?><span class="w3-right"><?php echo $row['assigned_to'] ? '<span class="label label-primary show_operator_name">' . $row['assigned_to'] . '</span>' : '<span class="label label-danger show_operator_name">Not Assigned Yet!</span>'; ?></span></li>
                                    </ul>
                                </div>
                                <div class="col-lg-4 col-md-4 col-sm-4">
                                    <ul class="w3-ul">
                                        <li><?php $put_class = strlen($row['orgName']) > 19 ? 'w3-small' : 'w3-medium';
                                            echo '<span class="label ' . $put_class . ' w3-blue">' . $row['orgName'] . '</span>'; ?><span class="w3-medium w3-right" style="font-weight:bold"><?php echo date('d-m-Y', strtotime($row['assignDate'])) . ' ' . $row['assignTime']; ?></li>
                                        <!--<li>Contact Number <span class="w3-large w3-right"><?php echo $row['inchContact']; ?></span></li>-->
                                        <li>Booked Via <span class="w3-right"><?php echo $row['bookedVia']; ?></span></li>
                                        <li title="<?php echo $row['c_title']; ?>">Type <span class="w3-right"><img src="images/comunic_types/<?php echo $row['c_image']; ?>" width="40" style="margin-top: -12px;" /></span></li>
                                        <li>Edited By <span class="w3-right"><?php echo $row['edited_by'] ?: "Not edited"; ?></span></li>
                                        <li>Assigned Time <span class="w3-right"><?php echo $row['assigned_date'] ? '<span class="label label-primary">' . $misc->time_elapsed_string($row['assigned_date']) . '</span>' : '<span class="label label-danger">Not Updated Yet!</span>'; ?></span></li>
                                    </ul>

                                </div>
                                <div class="col-sm-3 col-xs-12">
                                    <ul class="w3-ul">
                                        <li>Gender <span class="w3-right"><?php echo $row['gender']; ?></span></li>
                                        <li>Applicants <span class="w3-right w3-large">
                                                <?php
                                                if ($row['jobDisp'] == 1) {
                                                    $row_aps = $arr_n = $acttObj->read_specific("count(*) as applicants", "bid", "bid.tabName='$table' and bid.job=$tracking");
                                                    if ($row_aps['applicants']) {
                                                        echo $row_aps['applicants'];
                                                    } else {
                                                        echo '0';
                                                    }
                                                } else {
                                                    echo 'N/A';
                                                }
                                                ?>
                                            </span></li>
                                        <li>Client <span class="w3-right <?php echo strlen($row['noClient']) > 15 ? 'w3-small' : ''; ?>"><?php echo $row['noClient'] ?: '- - -'; ?></span></li>
                                        <li>Submitted By <span class="w3-right"><?php echo $row['submited']; ?></span></li>
                                        <li><b>DB Reference <span class="w3-right"><?php echo $row['reference_no'] . ($row['is_shifted'] == 1 ? ' <small class="label label-danger">Job Shifted</small>' : ''); ?></span></b></li>
                                    </ul>
                                </div>

                                <style>
                                    .action_buttons .w3-button {
                                        padding: 7px 11px;
                                    }

                                    .action_buttons .fa {
                                        font-size: 20px;
                                    }

                                    .w3-ul li {
                                        border-bottom: none;
                                    }
                                </style>
                                <div class="col-sm-12 text-center action_buttons">
                                    <?php if ($row['approve_portal_mngt']>0){ if ($action_view_job) { ?>
                                        <a data-toggle="tooltip" data-placement="top" href="javascript:void(0)" class="w3-button w3-small w3-circle w3-white w3-border w3-border-blue" title="View Job" onClick="popupwindow('order_view.php?view_id=<?php echo $row['id']; ?>&table=<?php echo $table; ?>', 'View Telephone Order', 1000, 1000);"><i class="fa fa-eye"></i></a>
                                    <?php }
                                    if ($row['deleted_flag'] == 0 && $row['order_cancel_flag'] == 0) {
                                        if ($action_confirm_temporary) {
                                            if ($row['is_temp'] == 1) { ?>
                                                <a data-toggle="tooltip" data-placement="top" href="javascript:void(0)" class="w3-button w3-small w3-circle w3-yellow w3-border w3-border-blue" title="This job must be confirmed by Manager or Supervisor" onClick="popupwindow('confirm_record.php?id=<?php echo $row['id']; ?>&table=<?php echo $table; ?>', 'Confirm Telephone Record', 520,350);"><i class="fa fa-check-circle"></i></a>
                                            <?php }
                                        }
                                        if ($action_duplicate) {
                                            if ($row['is_temp'] == 0) { ?>
                                                <a data-toggle="tooltip" data-placement="top" href="javascript:void(0)" class="w3-button w3-small w3-circle w3-white w3-border w3-border-blue" title="Create Duplicate" onClick="popupwindow('telep_edit.php?edit_id=<?php echo $row['id']; ?>&duplicate=<?php echo 'yes'; ?>','Duplicate Telephone Order',1250, 730)"><i class="fa fa-clone"></i></a>
                                            <?php }
                                        }
                                    }
                                    if ($action_job_note) {
                                        $arr_n = $acttObj->read_specific("(select count(id)", "jobnotes", "tbl='$table' and fid=" . $row['id'] . " and (readcount is null or readcount=0)) as unread ,(select count(id) from jobnotes where tbl='$table' and fid=" . $row['id'] . " and (readcount is not null and readcount!=0)) as yes_read"); ?>
                                        <a title="<b>JOB NOTES</b>" data-toggle="popover" data-trigger="hover" data-placement="top" style="text-decoration:none;" data-content="<?php echo 'Unread Job Notes are <b>' . $arr_n['unread'] . '</b> <br> Read Job Notes are <b>' . $arr_n['yes_read'] . '</b>'; ?>" href="javascript:void(0)" onclick="popupwindow('jobnote.php?fid=<?php echo $row['id']; ?>&table=<?php echo $table; ?>&orgName=<?php echo $row['orgName']; ?>','Telephone Job Notes',900,800)" <?php echo $arr_n['unread'] > 0 ? 'class="w3-button w3-small w3-circle w3-blue"' : 'class="w3-button w3-small w3-circle w3-grey"'; ?>>
                                            <?php echo $arr_n['unread'] > 0 ? $arr_n['unread'] : $arr_n['yes_read']; ?>
                                        </a>
                                    <?php }
                                    if ($row['deleted_flag'] == 0 && $row['order_cancel_flag'] == 0) {
                                        if ($action_edit_job) { ?>
                                            <a data-toggle="tooltip" data-placement="top" href="javascript:void(0)" class="w3-button w3-small w3-circle w3-white w3-border w3-border-blue" title="Edit Job" onClick="popupwindow('telep_edit.php?edit_id=<?= $row['id'] ?>&is_home=1','Edit Telephone Order',1250, 730)">
                                                <i class="fa fa-pencil-square-o"></i></a>
                                            <?php }
                                        if ($row['jobStatus'] == 0) { //enquiry job
                                            if ($action_confirm_enquiry) {
                                                if ($row['is_temp'] == 0) { ?>
                                                    <a data-toggle="tooltip" data-placement="top" title="Enquiry-<?php echo $row['id']; ?>" href="javascript:void(0)" class="w3-button w3-small w3-circle w3-white w3-border w3-border-red" onClick="popupwindow('status.php?status_id=<?php echo $row['id']; ?>&table=<?php echo $table; ?>&srcLang=<?php echo $row['source']; ?>','Order Status Telephone',520,350)"><i class="fa fa-question text-danger"></i></a>
                                                <?php }
                                            }
                                        } else {
                                            if ($action_assign_interpreter) {
                                                if (($row['is_temp'] == 0 && ($_SESSION['is_root'] == 1 || $action_force_assign_interpreter || empty($row['user_id']) || (!empty($row['user_id']) && $_SESSION['userId'] == $row['user_id'])))) { ?>
                                                    <a data-toggle="tooltip" data-placement="top" href="javascript:void(0)" class="w3-button w3-small w3-circle w3-white w3-border w3-border-green btn_assign_interpreter" title="Assign Interpreter" <?php if (empty($row['checked_by']) && $action_check_job) {?>onclick="alert('You must Check this job before assigning to an interpreter!')" data-onclick="popupwindow('interp_assign.php?assign_id=<?php echo $row['id']; ?>&table=<?php echo $table; ?>&srcLang=<?php echo $row['source']; ?>&gender=<?php echo $row['gender']; ?>&assignDate=<?php echo $row['assignDate']; ?>&assignTime=<?php echo $row['assignTime']; ?>','Telephone Assign Interpreter',1100,650)"<?php } else { ?> onClick="popupwindow('interp_assign.php?assign_id=<?php echo $row['id']; ?>&table=<?php echo $table; ?>&srcLang=<?php echo $row['source']; ?>&gender=<?php echo $row['gender']; ?>&assignDate=<?php echo $row['assignDate']; ?>&assignTime=<?php echo $row['assignTime']; ?>','Telephone Assign Interpreter',1100,650)" <?php } ?>><i class="fa fa-user text-success"></i></a>
                                                <?php }
                                            }
                                        }
                                    }
                                    if ($row['deleted_flag'] == 0) {
                                        if ($action_delete_job) { ?>
                                            <a data-toggle="tooltip" data-placement="top" href="javascript:void(0)" class="w3-button w3-small w3-circle w3-red w3-border w3-border-red" title="Trash Job" onClick="popupwindow('del_trash.php?del_id=<?php echo $row['id']; ?>&table=<?php echo $table; ?>&is_home=1','Delete Record',520,350)"><i class="fa fa-trash-o"></i></a>
                                            <?php }
                                    } else {
                                        if ($action_restore_job) {
                                            if ($tp == 'tr') { ?>
                                                <a class="w3-button w3-small w3-circle w3-green w3-border w3-border-green" title="Restore Order" href="javascript:void(0)" onClick="popupwindow('trash_restore.php?del_id=<?php echo $row['id']; ?>&table=<?php echo $table; ?>','Restore this order',520,350)"><i class="fa fa-refresh"></i></a>
                                            <?php }
                                        }
                                    }
                                    if ($row['order_cancel_flag'] == 1 && $tp == 'c') {
                                        if ($action_resume_job) { ?>
                                            <a class="w3-button w3-small w3-circle w3-green w3-border w3-border-green" title="Resume this cancelled Order" href="javascript:void(0)" onClick="popupwindow('email_resume.php?email_id=<?php echo $row['id']; ?>&table=<?= $table; ?>&orgName=<?= $row['orgName'] ?>','Resume this cancelled order',620,450)"><i class="fa fa-undo"></i></a>
                                        <?php }
                                    }
                                    if ($row['deleted_flag'] == 0 && $row['order_cancel_flag'] == 0) {
                                        if ($row['jobDisp'] == 1 && $row_aps['applicants'] != 0) {
                                            if ($action_view_applicants) { ?>
                                                <a data-toggle="tooltip" data-placement="top" href="javascript:void(0)" class="w3-button w3-small w3-circle w3-white w3-border w3-border-green btn_view_applicants" title="Applicants" <?php if (empty($row['checked_by']) && $action_check_job) {?>onclick="alert('You must Check this job before assigning to an interpreter!')" data-onclick="popupwindow('../no_of_applicants.php?tracking=<?php echo $row['id']; ?>&table=<?php echo $table; ?>','No of Applicants',1200,900)"<?php } else { ?> onClick="popupwindow('../no_of_applicants.php?tracking=<?php echo $row['id']; ?>&table=<?php echo $table; ?>','No of Applicants',1200,900)" <?php } ?>><i class="fa fa-users text-success"></i></a>
                                            <?php }
                                        }
                                        if ($action_shift_order) {?>
                                            <a data-toggle="tooltip" data-placement="top" href="javascript:void(0)" class="w3-button w3-small w3-circle w3-brown w3-border w3-border-black" title="Shift this order to Face To Face" onClick="popupwindow('interp_edit.php?edit_id=<?php echo $row['id']; ?>&duplicate=yes&is_shift=1','Shift Order',1250, 730)"><i class="fa fa-refresh"></i></a>
                                        <?php }
                                    }
                                    if ($action_edited_history) {?>
                                        <a data-toggle="tooltip" data-placement="top" href="javascript:void(0)" class="w3-button w3-small w3-circle w3-white w3-border w3-border-red" data-table-name="<?= strtolower($table) ?>" data-record-id="<?= $row['id'] ?>" onclick="view_log_changes(this)" href="javascript:void(0)" title="View Log Edited History"><i class="fa fa-list text-danger"></i></a>
                                    <?php }
                                    if (empty($row['checked_by'])) {
                                        if ($action_check_job) { ?>
                                            <a href="javascript:void(0)" class="w3-button w3-small w3-circle w3-blue w3-border w3-border-black pull-right" title="Check this job" onclick="check_the_job(this, <?= $row['id'] ?>)" style="margin: 0px 12px;"><i class="fa fa-check text-white"></i></a>
                                    <?php }
                                    } else {
                                        $get_checked_user = $acttObj->read_specific("name", "login", "id=" . $row['checked_by'])['name'];
                                        $get_checked_date = $misc->dated($row['checked_date']);
                                        echo '<small class="w3-border w3-border-black text-primary pull-right" title="This job has been checked by ' . $get_checked_user . " on " . $get_checked_date . '" style="margin: 8px 10px;padding:2px;"><b>' . $get_checked_user . " on " . $get_checked_date . '</b></small>';
                                    } 
                                }else{
                                    if ($action_view_job) { ?>
                                        <a data-toggle="tooltip" data-placement="top" href="javascript:void(0)" class="w3-button w3-small w3-circle w3-white w3-border w3-border-blue" title="View Job" onClick="popupwindow('order_view.php?view_id=<?php echo $row['id']; ?>&table=<?php echo $table; ?>', 'View Telephone Order', 1000, 1000);"><i class="fa fa-eye"></i></a>
                                        <h4 class="text-danger" >This Job is yet to be approved by the Client from their portal</h4>
                                    <?php }
                                }?>

                                </div>
                            </td>
                            </tr>
                        <?php
                            $rowcount++;
                        } ?>

                    </tbody>
                </table>
            </div>
    </section>

    <!--operator re allocate modal-->
    <div class="modal" id="re_allocate_modal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header alert-success">
                    <button type="button" class="close" data-dismiss="modal">×</button>
                    <h4 class="modal-title">Re-Allocate Job To Another Operator</h4>
                </div>
                <div class="modal-body re_allocate_modal_attach">
                    <input type="hidden" id="assigned_row_id" disabled readonly>
                    <input type="hidden" id="job_id" disabled readonly>
                    <div class="row">
                        <div class="form-group col-md-6">
                            <label for="assigned_operator">Current Assigned Operator</label>
                            <input class="form-control" name="assigned_operator" id="assigned_operator" disabled readonly>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="operator_id">Select an Operator</label>
                            <select class="form-control" name="operator_id" id="operator_id" required>
                                <option value="">--- Select an Operator ---</option>
                                <?php $get_operators = $acttObj->read_all("id, name", "login", "login.user_status=1 AND (login.is_allocation_member=1 OR login.prv='Operator')");
                                while ($row_operator = $get_operators->fetch_assoc()) { ?>
                                    <option value="<?= $row_operator['id'] ?>"><?= $row_operator['name'] ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" onclick="update_assigned_operator(this);">Assign Operator</button>
                </div>
            </div>
        </div>
    </div>
    <!--Ajax processing modal-->
    <div class="modal" id="process_modal" data-backdrop="static">
        <div class="modal-dialog modal-lg" style="width: 85%;">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="btn btn-xs btn-danger pull-right" data-dismiss="modal">×</button>
                </div>
                <div class="modal-body process_modal_attach">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script src="js/jquery-1.11.3.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.20/js/dataTables.bootstrap.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.tbl_data').DataTable({
                drawCallback: function() {
                    $('[data-toggle="popover"]').popover({
                        html: true
                    });
                    $('[data-toggle="tooltip"]').tooltip();
                },
                "bSort": false
            });
        });

        function filter_list() {
            var append_url = "<?php echo basename(__FILE__); ?>";
            var tp = $("#tp").val();
            if (tp) {
                append_url += '?tp=' + tp;
            }
            window.location.href = append_url;
        }
    </script>

    <script src="js/realtime_notify.js?v=2"></script>
    <script>
        function re_allocate_job(element) {
            $(element).attr("id", "selected_row");
            $("#job_id").val($(element).attr("data-order-id"));
            $("#assigned_row_id").val($(element).attr("data-assigned-row-id"));
            $("#assigned_operator").val($(element).attr("data-operator-name"));
            $("#operator_id option[value='" + $(element).attr("data-operator-id") + "']").prop("selected", true);
            $('#re_allocate_modal').modal('show');
        }

        function update_assigned_operator(element) {
            if ($('#job_id').val() && $('#operator_id').val()) {
                $.ajax({
                    url: 'ajax_add_interp_data.php',
                    method: 'post',
                    dataType: 'json',
                    data: {
                        assigned_row_id: $('#assigned_row_id').val(),
                        job_id: $('#job_id').val(),
                        operator_id: $('#operator_id').val(),
                        operator_name: $('#operator_id option:selected').text(),
                        order_type: 2,
                        update_assigned_operator: 1
                    },
                    success: function(data) {
                        alert(data['message']);
                        if (!$('#assigned_row_id').val()) {
                            $('#selected_row').attr("data-assigned-row-id", data['assigned_row_id']);
                        }
                        $('#selected_row').attr("data-operator-id", $('#operator_id').val());
                        $('#selected_row').attr("data-operator-name", $('#operator_id option:selected').text());
                        if ($('#selected_row').parents('li').find('span.show_operator_name').hasClass("label-danger")) {
                            $('#selected_row').parents('li').find('span.show_operator_name').removeClass("label-danger");
                            $('#selected_row').parents('li').find('span.show_operator_name').addClass("label-primary");
                        }
                        $('#selected_row').parents('li').find('span.show_operator_name').text($('#operator_id option:selected').text());
                        $('#re_allocate_modal').modal('hide');
                        $(element).removeAttr("id");
                    },
                    error: function(data) {
                        alert("Error code : " + data.status + " , Error message : " + data.statusText);
                    }
                });
            } else {
                $('#operator_id').focus();
            }
        }

        function check_the_job(element, job_id) {
            if (confirm("Did you checked this job details completely?")) {
                var current_element = $(element);
                $.ajax({
                    url: 'ajax_add_interp_data.php',
                    method: 'post',
                    dataType: 'json',
                    data: {
                        job_id: job_id,
                        job_type: "TP",
                        check_the_job: 1
                    },
                    success: function(data) {
                        if (data['status'] == 1) {
                            current_element.parents("div.action_buttons").find(".btn_assign_interpreter").attr("onclick", current_element.parents("div.action_buttons").find(".btn_assign_interpreter").attr("data-onclick"));
                            current_element.parents("div.action_buttons").find(".btn_view_applicants").attr("onclick", current_element.parents("div.action_buttons").find(".btn_view_applicants").attr("data-onclick"));
                            $(element).replaceWith(data['message']);
                        }
                    },
                    error: function(data) {
                        console.log("Error code : " + data.status + " , Error message : " + data.statusText);
                    }
                });
            }
        }

        function view_log_changes(element) {
            var table_name = $(element).attr("data-table-name");
            var table_name_array = {"interpreter" : "Face To Face Booking", "telephone":"Telephone Booking", "translation" : "Translation Booking"};
            var record_id = $(element).attr("data-record-id");
            if (record_id && table_name) {
                $('.process_modal_attach').html("<center><i class='fa fa-circle fa-2x'></i> <i class='fa fa-circle fa-2x'></i> <i class='fa fa-circle fa-2x'></i><br><h3>Loading ...<br><br>Please Wait !!!</h3></center>");
                $('#process_modal').modal('show');
                $('body').removeClass('modal-open');
                $.ajax({
                    url: 'ajax_add_interp_data.php',
                    method: 'post',
                    dataType: 'json',
                    data: {
                        record_id: record_id,
                        table_name: table_name,
                        table_name_label: table_name_array[table_name],
                        record_label: "Job",
                        view_log_changes: 1
                    },
                    success: function(data) {
                        if (data['status'] == 1) {
                            $('.process_modal_attach').html(data['body']);
                        } else {
                            alert("Cannot load requested response. Please try again!");
                        }
                    },
                    error: function(data) {
                        alert("Error: Please select valid record for log details or refresh the page! Thank you");
                    }
                });
            } else {
                alert("Error: Please select valid record for log details or refresh the page! Thank you");
            }
        }
    </script>
</body>

</html>