<?php
include "userhaspage.php";
SysPermiss::UserHasPage(__FILE__);
if (session_id() == '' || !isset($_SESSION)) {
    session_start();
}
include 'db.php';
include 'class.php';
//Access actions
$get_actions = explode(",", $acttObj->read_specific("GROUP_CONCAT(action_permissions.action_id) as actions", "action_permissions,route_actions", "action_permissions.action_id=route_actions.id AND route_actions.route_id=135 AND action_permissions.user_id=" . $_SESSION['userId'])['actions']);
$action_view_job = $_SESSION['is_root'] == 1 || in_array(113, $get_actions);
$action_edit_job = $_SESSION['is_root'] == 1 || in_array(114, $get_actions);
$action_delete_job = $_SESSION['is_root'] == 1 || in_array(115, $get_actions);
$action_restore_job = $_SESSION['is_root'] == 1 || in_array(116, $get_actions);
$action_duplicate = $_SESSION['is_root'] == 1 || in_array(117, $get_actions);
$action_amend_job = $_SESSION['is_root'] == 1 || in_array(118, $get_actions);
$action_update_expenses = $_SESSION['is_root'] == 1 || in_array(119, $get_actions);
$action_view_invoice = $_SESSION['is_root'] == 1 || in_array(120, $get_actions);
$action_confirm_temporary_expenses = $_SESSION['is_root'] == 1 || in_array(121, $get_actions);
$action_cancel_job = $_SESSION['is_root'] == 1 || in_array(122, $get_actions);
$action_resume_job = $_SESSION['is_root'] == 1 || in_array(123, $get_actions);
$action_check_job = $_SESSION['is_root'] == 1 || in_array(124, $get_actions);
$action_job_note = $_SESSION['is_root'] == 1 || in_array(125, $get_actions);
$action_view_earnings = $_SESSION['is_root'] == 1 || in_array(126, $get_actions);
$action_edited_history = $_SESSION['is_root'] == 1 || in_array(127, $get_actions);
$action_view_applicants = $_SESSION['is_root'] == 1 || in_array(128, $get_actions);
$action_interpreter_uploaded_timesheet = $_SESSION['is_root'] == 1 || in_array(129, $get_actions);
$action_connect_telephone_call = $_SESSION['is_root'] == 1 || in_array(130, $get_actions);
$action_download_lsuk_timesheet = $_SESSION['is_root'] == 1 || in_array(131, $get_actions);
$action_view_text_messages = $_SESSION['is_root'] == 1 || in_array(132, $get_actions);
$action_dropdown_trashed_jobs = $_SESSION['is_root'] == 1 || in_array(133, $get_actions);
$action_dropdown_cancelled_jobs = $_SESSION['is_root'] == 1 || in_array(134, $get_actions);
$action_dropdown_multi_invoice_jobs = $_SESSION['is_root'] == 1 || in_array(135, $get_actions);
$action_purchase_order = $_SESSION['is_root'] == 1 || in_array(158, $get_actions);
$action_request_client_feedback = $_SESSION['is_root'] == 1 || in_array(159, $get_actions);
$action_update_expenses_new = $_SESSION['is_root'] == 1 || in_array(160, $get_actions);
$action_newly_processed_jobs = $_SESSION['is_root'] == 1 || in_array(161, $get_actions);
$action_view_interpreter_profile_booking = $_SESSION['is_root'] == 1 || in_array(162, $get_actions);
$action_view_credit_note = $_SESSION['is_root'] == 1 || in_array(163, $get_actions);
$action_view_client_translation_document = $_SESSION['is_root'] == 1 || in_array(164, $get_actions);
$action_hide_client_cancelled_jobs = $_SESSION['is_root'] == 1 || in_array(165, $get_actions);
$action_hide_updated_hours_jobs = $_SESSION['is_root'] == 1 || in_array(166, $get_actions);
$action_can_force_update = $_SESSION['is_root'] == 1 || in_array(167, $get_actions);
$action_add_lateness = $_SESSION['is_root'] == 1 || in_array(213, $get_actions);
include_once 'function.php';
$new_pr = @$_GET['new_pr'];
$needs_approval = @$_GET['needs_approval'];
$assignDate = @$_GET['assignDate'];
$interp = @$_GET['interp'];
$org = @$_GET['org'];
$p_org = '';
$job = @$_GET['job'];
$inov = @$_GET['inov'];
$type = @$_GET['type'];
$tp = @$_GET['tp'];
$aT = @$_GET['aT'];
$string = @$_GET['str'];
$array_order_labels = array("Interpreter" => "F2F", "Telephone" => "TP", "Translation" => "TR");
$array_order_types = array("Interpreter" => 1, "Telephone" => 2, "Translation" => 3);
$call_types = array(1 => '<span class="label label-primary pull-right">LSUK to Host</span>', 2 => '<span class="label label-warning pull-right">Client to Host</span>', 3 => '<span class="label label-primary pull-right">Client to call LSUK</span>');
if (!empty($assignDate)) {
    $bg_aD = ' style="background: #ffff0075;"';
}
if (!empty($aT)) {
    $bg_aT = ' style="background: #ffff0075;"';
}
// Set query attributes according to job types
if ($action_newly_processed_jobs && isset($new_pr)) {
    $newly_processed_int = "and interpreter.is_temp = 1";
    $newly_processed_tp = "and telephone.is_temp = 1";
    $newly_processed_tr = "and translation.is_temp = 1";
}
if (isset($needs_approval)) {
    $needs_approval_int = "and interpreter.hrsubmited='Self' AND interpreter.approved_flag = 0";
    $needs_approval_tp = "and telephone.hrsubmited='Self' AND telephone.approved_flag = 0";
    $needs_approval_tr = "and translation.hrsubmited='Self' AND translation.approved_flag = 0";
}
if (isset($_GET['p_org'])) {
    $p_org = $_GET['p_org'];
    $p_org_q = $acttObj->read_specific("GROUP_CONCAT(child_comp) as ch_ids", "subsidiaries", " parent_comp=$p_org")['ch_ids'] ?: '0';
    $p_org_ad = ($p_org_q != 0 ? " and comp_reg.id IN ($p_org_q) " : "");
} else {
    $p_org_ad = $p_org;
}
$deleted_flag = $action_dropdown_trashed_jobs && $tp == 'tr' ? 'deleted_flag = 1' : 'deleted_flag = 0';
$order_cancel_flag = $action_dropdown_cancelled_jobs && $tp == 'c' ? 'order_cancel_flag = 1' : 'order_cancel_flag = 0';
$order_cancel_flag_paid =$action_dropdown_cancelled_jobs && $tp == 'c' ? 'orderCancelatoin = 1' : 'orderCancelatoin = 0';
$add_order_cancel_condition =$action_dropdown_cancelled_jobs && $tp == 'c' ? 1 : 0;
$multInv_flag = $action_dropdown_multi_invoice_jobs && $tp == 'ml' ? 'multInv_flag=1' : 'multInv_flag=0';
$page = (int) (!isset($_GET["page"]) ? 1 : $_GET["page"]);
$limit = 50;
$startpoint = ($page * $limit) - $limit;
$page_count = $startpoint;
$array_tp = array('a' => 'Active', 'tr' => 'Trashed', 'c' => 'Cancelled', 'ml' => 'Multi Invoice');
// Set page title according to job types
$title = $array_tp[$tp] == 'Active' ? '' : $array_tp[$tp];
$title_type = $type == 'Interpreter' ? 'Face To Face' : $type;
$class = 'label-primary';
if ($action_dropdown_trashed_jobs && $tp == 'tr') {
    $class = 'label-danger';
}
if ($action_dropdown_cancelled_jobs && $tp == 'c') {
    $class = 'label-warning';
}
$f2f_append = $tp_append = $tr_append = "";
if ($_SESSION['is_root'] == 0) {
    //Hide Client Cancelled Jobs
    $f2f_append .= $action_hide_client_cancelled_jobs ? " and interpreter.orderCancelatoin=0 " : "";
    $tp_append .= $action_hide_client_cancelled_jobs ? " and telephone.orderCancelatoin=0 " : "";
    $tr_append .= $action_hide_client_cancelled_jobs ? " and translation.orderCancelatoin=0 " : "";
    //Hide Interpreter Hours Updated Jobs
    $f2f_append .= $action_hide_updated_hours_jobs ? " and interpreter.hoursWorkd=0 " : "";
    $tp_append .= $action_hide_updated_hours_jobs ? " and telephone.hoursWorkd=0 " : "";
    $tr_append .= $action_hide_updated_hours_jobs ? " and translation.numberUnit=0 " : "";
}
$string_f2f_append = $string ? " and (interpreter.orgRef like '$string%' OR interpreter.porder like '$string%' OR interpreter.nameRef like '$string%' OR interpreter.invoiceNo like '$string%' OR interpreter.id = '$string' OR interpreter.reference_no like '$string%')" : "";
$string_tp_append = $string ? " and (telephone.orgRef like '$string%' OR telephone.porder like '$string%' OR telephone.nameRef like '$string%' OR telephone.invoiceNo like '$string%' OR telephone.id = '$string' OR telephone.reference_no like '$string%')" : "";
$string_tr_append = $string ? " and (translation.orgRef like '$string%' OR translation.porder like '$string%' OR translation.nameRef like '$string%' OR translation.invoiceNo like '$string%' OR translation.id = '$string' OR translation.reference_no like '$string%')" : "";

?>
<!doctype html>
<html lang="en">

<head>
    <title><?php echo empty($type) ? 'All ' . $title_type . ' ' . $title . ' jobs list' : $title_type . ' ' . $title . ' list' ?></title>
    <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" />
    <style>
        html,
        body {
            background: white !important;
        }

        .modal {
            overflow-y: auto !important;
        }

        .action_buttons .w3-button {
            padding: 7px 11px;
        }

        .dropdown_actions2 .dropdown-menu {
            left: auto;
            right: 0;
        }

        .action_buttons .fa,
        .action_buttons2 .fa {
            font-size: 16px;
        }

        .w3-ul li {
            border-bottom: none;
        }

        .dropdown_actions a,
        .dropdown_actions2 a {
            padding: 2px 4px !important
        }

        .dropdown_actions .dropdown-menu {
            width: max-content;
            padding: 7px 7px 0px 2px;
            bottom: -4px !important;
            top: auto;
            right: 64px !important;
            left: auto;
        }

        .dropdown_actions,
        .dropdown_actions2 {
            display: inline-block;
        }

        .lbl {
            border-radius: 0px !important;
            margin: -9px -8px !important;
            font-size: 12px;
            bottom: 0;
            right: 0;
            position: absolute;
        }

        .p3 {
            padding: 3px;
        }

        .w3-ul li {
            margin: -6px -25px;
        }

        .multiselect {
            min-width: 190px;
        }

        .multiselect-container {
            max-height: 400px;
            overflow-y: auto;
            max-width: 380px;
        }

        .tab_container {
            min-height: 700px;
        }

        .w3-small {
            padding: 1px 5px !important;
            margin-top: -6px !important;
        }

        .badge-counter {
            border-radius: 0px !important;
            margin: -9px -9px !important;
            font-size: 10px;
            float: left;
        }

        .tablesorter thead tr {
            background: none;
        }

        .w3-hoverable tbody tr:hover {
            background-color: #2196f30d !important;
        }

        .is_temp {
            background-color: #cbda78;
        }

        .form-group {
            margin-bottom: 2px;
        }

        .modal-open {
            overflow: initial !important;
        }
    </style>
</head>
<?php include 'header.php'; ?>

<body>
    <script>
        function myFunction() {
            var append_url = "<?php echo basename(__FILE__) . "?1"; ?>";
            if ($("#new_pr").is(':checked')) {
                append_url += '&new_pr=1';
            }
            if ($("#needs_approval").is(':checked')) {
                append_url += '&needs_approval=1';
            }
            var inov = $("#inov").val();
            if (inov) {
                append_url += '&inov=' + inov;
            }
            var assignDate = $("#assignDate").val();
            if (assignDate) {
                append_url += '&assignDate=' + assignDate;
            }
            var aT = $("#aT").val();
            if (aT) {
                append_url += '&aT=' + aT;
            }
            var interp = $("#interp").val();
            if (interp) {
                append_url += '&interp=' + encodeURIComponent(interp);
            }
            var org = $("#org").val();
            if (org) {
                append_url += '&org=' + encodeURIComponent(org);
            }
            var p_org = $("#p_org").val();
            if (p_org) {
                append_url += '&p_org=' + p_org;
            }
            var job = $("#job").val();
            if (job) {
                append_url += '&job=' + job;
            }
            var type = $("#type").val();
            if (type) {
                append_url += '&type=' + type;
            }
            var tp = $("#tp").val();
            if (tp) {
                append_url += '&tp=' + tp;
            }
            var search = $("#search").val();
            if (search) {
                append_url += '&str=' + search;
            }
            window.location.href = append_url;
        }
    </script>
    <?php include 'nav2.php'; ?>
    <section class="container-fluid" style="overflow-x:auto;margin-top: -10px;">
        <div class="">
            <header>
                <div class="row" style="margin-top: -10px;">
                    <h2 class="text-center"><a style="color:white" class="label <?php echo $class; ?>" href="<?php echo basename(__FILE__); ?>"><?php echo empty($type) ? 'All ' . $title_type . ' ' . $title . ' jobs list' : $title_type . ' ' . $title . ' list' ?></a></h2>
                </div>
                <?php if (!empty($type) && $type == "Telephone") { ?>
                    <div class="form-group col-md-2 col-sm-4">
                        <?php if (!empty($type) && $type == 'Telephone') {
                            $append_tp_assignDate = $assignDate ? " AND telephone.assignDate LIKE '$assignDate%'" : "";
                            $sql_opt_aT = "SELECT distinct telephone.assignTime FROM telephone WHERE 1 $append_tp_assignDate and $multInv_flag and commit=0 and $deleted_flag and $order_cancel_flag ORDER BY assignTime";
                        } ?>
                        <select name="aT" id="aT" onChange="myFunction()" class="form-control">
                            <?php $result_opt_aT = mysqli_query($con, $sql_opt_aT);
                            $options_aT = "";
                            while ($row_opt_aT = mysqli_fetch_array($result_opt_aT)) {
                                $name_opt_aT = $row_opt_aT["assignTime"];
                                $options_aT .= "<option>" . $name_opt_aT . '</option>';
                            } ?>
                            <?php if (!empty($aT)) { ?>
                                <option><?php echo $aT; ?></option>
                            <?php } else { ?>
                                <option value="" selected>Select Time</option>
                            <?php } ?>
                            <?php echo $options_aT; ?>
                        </select>
                    </div>
                <?php } else { ?>
                    <div class="form-group col-md-2 col-sm-4 pull-right">
                        <input type="hidden" id="aT" name="aT" value="">
                    </div>
                <?php } ?>
                <div class="form-group col-md-2 col-sm-4">
                    <label class="<?= $action_newly_processed_jobs ? '' : 'hidden' ?>" style="margin-top: -25px;position: absolute;">
                        <input <?= isset($new_pr) ? 'checked' : '' ?> type="checkbox" id="new_pr" onchange="myFunction()"> Newly Processed
                    </label>
                    <select id="type" onChange="myFunction()" name="type" class="form-control">
                        <?php if (!empty($type)) { ?>
                            <option value="<?php echo $type; ?>" selected><?php echo $type; ?></option>
                        <?php } ?>
                        <option value="" disabled <?= empty($type) ? 'selected' : '' ?>>Filter Job Type</option>
                        <option value="Interpreter">Interpreter</option>
                        <option value="Telephone">Telephone</option>
                        <option value="Translation">Translation</option>
                    </select>
                </div>
                <div class="form-group col-md-2 col-sm-4">
                    <label title="Filter job's timesheets uplaoded by interpreter & needs approval" class="text-danger" style="margin-top: -25px;position: absolute;">
                        <input <?= isset($needs_approval) ? 'checked' : '' ?> type="checkbox" id="needs_approval" onchange="myFunction()"> Needs Approval
                    </label>
                    <select id="tp" onChange="myFunction()" name="tp" class="form-control">
                        <option value="" disabled <?= empty($tp) ? 'selected' : '' ?>>Booking Status</option>
                        <option value="a">Active</option>
                        <?php if ($action_dropdown_trashed_jobs || $action_dropdown_cancelled_jobs || $action_dropdown_multi_invoice_jobs) { ?>
                            <option <?= $action_dropdown_trashed_jobs ? '' : 'hidden' ?> <?= $action_dropdown_trashed_jobs && $tp == "tr" ? "selected" : "" ?> value="tr">Trashed</option>
                            <option <?= $action_dropdown_cancelled_jobs ? '' : 'hidden' ?> <?= $action_dropdown_cancelled_jobs && $tp == "c" ? "selected" : "" ?> value="c">Cancelled</option>
                            <option <?= $action_dropdown_multi_invoice_jobs ? '' : 'hidden' ?> <?= $action_dropdown_multi_invoice_jobs && $tp == "ml" ? "selected" : "" ?> value="ml">Multi Invoice</option>
                            <option <?= $action_dropdown_cancelled_jobs ? '' : 'hidden' ?> <?= $action_dropdown_cancelled_jobs && $_GET['paid_flag'] == '1' ? "selected" : "" ?> value="c&paid_flag=1">Paid Cancelled</option>

                            
                        <?php } ?>
                    </select>
                </div>
                <div class="form-group col-md-2 col-sm-4">
                    <input type="text" name="search" id="search" class="form-control" placeholder="Search ..." onChange="myFunction()" value="<?php echo $string; ?>" />
                </div>
                <div class="form-group col-md-2 col-sm-4">
                    <input type="text" name="inov" id="inov" class="form-control" placeholder="Invoice No" onChange="myFunction()" value="<?php echo $inov; ?>" />
                </div>
                <div class="form-group col-md-2 col-sm-4">
                    <?php if (!empty($type) && $type == 'Interpreter') {
                        $sql_opt = "SELECT DISTINCT name,id,gender,city from (SELECT distinct interpreter_reg.name,interpreter_reg.id,interpreter_reg.gender, interpreter_reg.city,interpreter.multInv_flag,interpreter.commit,interpreter.total_charges_comp,interpreter.rAmount,interpreter.orgName,interpreter.deleted_flag,interpreter.order_cancel_flag,
            interpreter.porder,comp_reg.po_req FROM interpreter_reg,interp_lang,interpreter,comp_reg WHERE interpreter_reg.code=interp_lang.code
            AND interpreter.intrpName=interpreter_reg.id AND interpreter.orgName=comp_reg.abrv AND interpreter.$deleted_flag and interpreter.$order_cancel_flag and interpreter.$multInv_flag and interpreter.commit=0 and interpreter.assignDate like '$assignDate%' and (interpreter.orgName like '%$_words%')) as grp  ORDER BY name ASC";
                    } else if (!empty($type) && $type == 'Telephone') {
                        $sql_opt = "SELECT DISTINCT name,id,gender,city from (SELECT distinct interpreter_reg.name,interpreter_reg.id,interpreter_reg.gender, interpreter_reg.city,telephone.multInv_flag,telephone.commit,telephone.total_charges_comp,telephone.rAmount,telephone.orgName,telephone.deleted_flag,telephone.order_cancel_flag,telephone.mark_join_time,telephone.porder,comp_reg.po_req FROM interpreter_reg,interp_lang,telephone,comp_reg WHERE interpreter_reg.code=interp_lang.code
            AND telephone.intrpName=interpreter_reg.id AND telephone.orgName=comp_reg.abrv AND telephone.$deleted_flag and telephone.$order_cancel_flag and telephone.$multInv_flag and telephone.commit=0 and telephone.assignDate like '$assignDate%' and telephone.assignTime like '$aT%' and (telephone.orgName like '%$_words%')) as grp  ORDER BY name ASC";
                    } else if (!empty($type) && $type == 'Translation') {
                        $sql_opt = "SELECT DISTINCT name,id,gender,city from (SELECT distinct interpreter_reg.name,interpreter_reg.id,interpreter_reg.gender, interpreter_reg.city,translation.multInv_flag,translation.commit,translation.total_charges_comp,translation.rAmount,translation.orgName,translation.deleted_flag,translation.order_cancel_flag,
            translation.porder,comp_reg.po_req FROM interpreter_reg,interp_lang,translation,comp_reg WHERE interpreter_reg.code=interp_lang.code
            AND translation.intrpName=interpreter_reg.id AND translation.orgName=comp_reg.abrv AND translation.$deleted_flag and translation.$order_cancel_flag and translation.$multInv_flag and translation.commit=0 and translation.asignDate like '$assignDate%' and (translation.orgName like '%$_words%')) as grp  ORDER BY name ASC";
                    } else {
                        $sql_opt = "SELECT DISTINCT name,id,gender,city from (SELECT distinct interpreter_reg.name,interpreter_reg.id,interpreter_reg.gender, interpreter_reg.city,interpreter.multInv_flag,interpreter.commit,interpreter.total_charges_comp,interpreter.rAmount,interpreter.orgName,interpreter.deleted_flag,interpreter.order_cancel_flag,
            interpreter.porder,comp_reg.po_req FROM interpreter_reg,interp_lang,interpreter,comp_reg WHERE interpreter_reg.code=interp_lang.code
            AND interpreter.intrpName=interpreter_reg.id AND interpreter.orgName=comp_reg.abrv AND interpreter.$deleted_flag and interpreter.$order_cancel_flag and interpreter.$multInv_flag and interpreter.commit=0 and interpreter.assignDate like '$assignDate%' and (interpreter.orgName like '%$_words%')
                        UNION ALL SELECT distinct interpreter_reg.name,interpreter_reg.id,interpreter_reg.gender, interpreter_reg.city,telephone.multInv_flag,telephone.commit,telephone.total_charges_comp,telephone.rAmount,telephone.orgName,telephone.deleted_flag,telephone.order_cancel_flag,
            telephone.porder,comp_reg.po_req FROM interpreter_reg,interp_lang,telephone,comp_reg WHERE interpreter_reg.code=interp_lang.code
            AND telephone.intrpName=interpreter_reg.id AND telephone.orgName=comp_reg.abrv AND telephone.$deleted_flag and telephone.$order_cancel_flag and telephone.$multInv_flag and telephone.commit=0 and telephone.assignDate like '$assignDate%' and telephone.assignTime like '$aT%' and (telephone.orgName like '%$_words%')
                        UNION ALL SELECT distinct interpreter_reg.name,interpreter_reg.id,interpreter_reg.gender, interpreter_reg.city,translation.multInv_flag,translation.commit,translation.total_charges_comp,translation.rAmount,translation.orgName,translation.deleted_flag,translation.order_cancel_flag,
            translation.porder,comp_reg.po_req FROM interpreter_reg,interp_lang,translation,comp_reg WHERE interpreter_reg.code=interp_lang.code
            AND translation.intrpName=interpreter_reg.id AND translation.orgName=comp_reg.abrv AND translation.$deleted_flag and translation.$order_cancel_flag and translation.$multInv_flag and translation.commit=0 and translation.asignDate like '$assignDate%' and (translation.orgName like '%$_words%')) as grp  ORDER BY name ASC";
                    }
                    ?>
                    <select id="interp" onChange="myFunction()" name="interp" class="form-control searchable">
                        <?php $result_opt = mysqli_query($con, $sql_opt);
                        $options_int = "";
                        while ($row_opt = mysqli_fetch_array($result_opt)) {
                            $code = $row_opt["name"];
                            $name_opt = $row_opt["name"];
                            $city_opt = $row_opt["city"];
                            $gender = $row_opt["gender"];
                            $options_int .= "<option value='$code' " . (trim($interp) == trim($code) ? 'selected' : '') . ">" . $name_opt . ' (' . $gender . ')' . ' (' . $city_opt . ')</option>';
                        } ?>
                        <?php if (empty($interp)) { ?>
                            <option value="">Select Interpreter</option>
                        <?php } ?>
                        <?php echo $options_int; ?>
                    </select>
                </div>
                <div class="form-group col-md-2 col-sm-4">
                    <?php
                    $sql_opt = "SELECT DISTINCT id,name,abrv from comp_reg WHERE comp_nature=1 ORDER BY name ASC"; ?>
                    <select id="p_org" name="p_org" onChange="myFunction()" class="form-control searchable">
                        <?php $result_opt = mysqli_query($con, $sql_opt);
                        $options = "";
                        while ($row_opt = mysqli_fetch_array($result_opt)) {
                            $comp_id = $row_opt["id"];
                            $code = $row_opt["abrv"];
                            $name_opt = $row_opt["name"];
                            $options .= "<OPTION value='$comp_id' " . ($comp_id == $p_org ? 'selected' : '') . ">" . $name_opt . ' (' . $code . ')';
                        }
                        ?>
                        <option value="">Select Parent/Head Units</option>
                        <?php echo $options; ?>
                        </option>
                    </select>
                </div>
                <div class="form-group col-md-2 col-sm-4">
                    <?php
                    if (!empty($type) && $type == 'Interpreter') {
                        $sql_opt = "SELECT DISTINCT name,abrv from (SELECT DISTINCT comp_reg.name,comp_reg.abrv,comp_reg.po_req,interpreter.porder,interpreter.multInv_flag,interpreter.commit,interpreter.total_charges_comp,interpreter.rAmount,interpreter.orgName,interpreter.deleted_flag,interpreter.order_cancel_flag FROM comp_reg,interpreter,interpreter_reg WHERE interpreter.intrpName=interpreter_reg.id AND interpreter.orgName=comp_reg.abrv AND interpreter.$deleted_flag and interpreter.$order_cancel_flag AND interpreter.$multInv_flag and interpreter.commit=0 and interpreter.assignDate like '$assignDate%' ) as grp 
                                ORDER BY name ASC";
                    } else if (!empty($type) && $type == 'Telephone') {
                        $sql_opt = "SELECT DISTINCT name,abrv from (SELECT DISTINCT comp_reg.name,comp_reg.abrv,comp_reg.po_req,telephone.porder,telephone.multInv_flag,telephone.commit,telephone.total_charges_comp,telephone.rAmount,telephone.orgName,telephone.deleted_flag,telephone.order_cancel_flag FROM comp_reg,telephone,interpreter_reg WHERE telephone.intrpName=interpreter_reg.id AND telephone.orgName=comp_reg.abrv AND telephone.$deleted_flag and telephone.$order_cancel_flag AND telephone.$multInv_flag and telephone.commit=0 and telephone.assignDate like '$assignDate%' and telephone.assignTime like '$aT%' ) as grp 
                                ORDER BY name ASC";
                    } else if (!empty($type) && $type == 'Translation') {
                        $sql_opt = "SELECT DISTINCT name,abrv from (SELECT DISTINCT comp_reg.name,comp_reg.abrv,comp_reg.po_req,translation.porder,translation.multInv_flag,translation.commit,translation.total_charges_comp,translation.rAmount,translation.orgName,translation.deleted_flag,translation.order_cancel_flag FROM comp_reg,translation,interpreter_reg WHERE translation.intrpName=interpreter_reg.id AND translation.orgName=comp_reg.abrv AND translation.$deleted_flag and translation.$order_cancel_flag AND translation.$multInv_flag and translation.commit=0 and translation.asignDate like '$assignDate%' ) as grp 
                                ORDER BY name ASC";
                    } else {
                        $sql_opt = "SELECT DISTINCT name,abrv from (SELECT DISTINCT comp_reg.name,comp_reg.abrv,comp_reg.po_req,interpreter.porder,interpreter.multInv_flag,interpreter.commit,interpreter.total_charges_comp,interpreter.rAmount,interpreter.orgName,interpreter.deleted_flag,interpreter.order_cancel_flag FROM comp_reg,interpreter,interpreter_reg WHERE interpreter.intrpName=interpreter_reg.id AND interpreter.orgName=comp_reg.abrv AND interpreter.$deleted_flag and interpreter.$order_cancel_flag AND interpreter.$multInv_flag and interpreter.commit=0 and interpreter.assignDate like '$assignDate%' 
                                UNION SELECT DISTINCT comp_reg.name,comp_reg.abrv,comp_reg.po_req,telephone.porder,telephone.multInv_flag,telephone.commit,telephone.total_charges_comp,telephone.rAmount,telephone.orgName,telephone.deleted_flag,telephone.order_cancel_flag FROM comp_reg,telephone,interpreter_reg WHERE telephone.intrpName=interpreter_reg.id AND telephone.orgName=comp_reg.abrv AND telephone.$deleted_flag and telephone.$order_cancel_flag AND telephone.$multInv_flag and telephone.commit=0 and telephone.assignDate like '$assignDate%' and telephone.assignTime like '$aT%'  
                                UNION SELECT DISTINCT comp_reg.name,comp_reg.abrv,comp_reg.po_req,translation.porder,translation.multInv_flag,translation.commit,translation.total_charges_comp,translation.rAmount,translation.orgName,translation.deleted_flag,translation.order_cancel_flag FROM comp_reg,translation,interpreter_reg WHERE translation.intrpName=interpreter_reg.id AND translation.orgName=comp_reg.abrv AND translation.$deleted_flag and translation.$order_cancel_flag AND translation.$multInv_flag and translation.commit=0 and translation.asignDate like '$assignDate%'  ) as grp 
                                ORDER BY name ASC";
                    } ?>
                    <select id="org" name="org" onChange="myFunction()" class="form-control searchable">
                        <?php $result_opt = mysqli_query($con, $sql_opt);
                        $options = "";
                        while ($row_opt = mysqli_fetch_array($result_opt)) {
                            $code = $row_opt["abrv"];
                            $name_opt = $row_opt["name"];
                            $options .= "<OPTION value='$code'>" . $name_opt . ' (' . $code . ')';
                        }
                        ?>
                        <?php if (!empty($org)) { ?>
                            <option><?php echo $org; ?></option>
                        <?php } else { ?>
                            <option value="">Select Company</option>
                        <?php } ?>
                        <?php echo $options; ?>
                        </option>
                    </select>
                </div>
                <div class="form-group col-md-2 col-sm-4">
                    <?php if (!empty($type) && $type == 'Interpreter') {
                        $sql_opt = "SELECT distinct lang.lang FROM lang,interpreter WHERE interpreter.source=lang.lang and interpreter.assignDate LIKE '$assignDate%' and $multInv_flag and commit=0 and $deleted_flag and $order_cancel_flag $f2f_append ORDER BY lang ASC";
                    } else if (!empty($type) && $type == 'Telephone') {
                        $sql_opt = "SELECT distinct lang.lang FROM lang,telephone WHERE telephone.source=lang.lang and telephone.assignDate LIKE '$assignDate%' and telephone.assignTime like '$aT%' and $multInv_flag and commit=0 and $deleted_flag and $order_cancel_flag $tp_append ORDER BY lang ASC";
                    } else if (!empty($type) && $type == 'Translation') {
                        $sql_opt = "SELECT distinct lang.lang FROM lang,translation WHERE translation.source=lang.lang and translation.asignDate LIKE '$assignDate%' and $multInv_flag and commit=0 and $deleted_flag and $order_cancel_flag $tr_append ORDER BY lang ASC";
                    } else {
                        $sql_opt = "SELECT DISTINCT lang from (SELECT distinct lang.lang,interpreter.multInv_flag,interpreter.commit,interpreter.deleted_flag,interpreter.order_cancel_flag,interpreter.assignDate,interpreter.assignTime FROM lang,interpreter WHERE interpreter.source=lang.lang and interpreter.assignDate LIKE '$assignDate%' and $multInv_flag and commit=0 and $deleted_flag and $order_cancel_flag $f2f_append
               UNION ALL SELECT distinct lang.lang,telephone.multInv_flag,telephone.commit,telephone.deleted_flag,telephone.order_cancel_flag,telephone.assignDate,telephone.assignTime FROM lang,telephone WHERE telephone.source=lang.lang and telephone.assignDate LIKE '$assignDate%' and telephone.assignTime like '$aT%' and $multInv_flag and commit=0 and $deleted_flag and $order_cancel_flag $tp_append
               UNION ALL SELECT distinct lang.lang,translation.multInv_flag,translation.commit,translation.deleted_flag,translation.order_cancel_flag,translation.asignDate as assignDate,'00:00:00' as 'assignTime' FROM lang,translation WHERE translation.source=lang.lang and translation.asignDate LIKE '$assignDate%' and $multInv_flag and commit=0 and $deleted_flag and $order_cancel_flag $tr_append) as grp  ORDER BY lang ASC";
                    } ?>
                    <select name="job" id="job" onChange="myFunction()" class="form-control searchable">
                        <?php $result_opt = mysqli_query($con, $sql_opt);
                        $options = "";
                        while ($row_opt = mysqli_fetch_array($result_opt)) {
                            $code = $row_opt["lang"];
                            $name_opt = $row_opt["lang"];
                            $options .= "<option value='$code'>" . $name_opt . "</option>";
                        }
                        ?>
                        <?php if (!empty($job)) { ?>
                            <option><?php echo $job; ?></option>
                        <?php } else { ?>
                            <option value="">Language</option>
                        <?php } ?>
                        <?php echo $options; ?>
                    </select>
                </div>
                <div class="form-group col-md-2 col-sm-4">
                    <input type="date" name="assignDate" id="assignDate" placeholder='' class="form-control" onChange="myFunction()" value="<?php echo $assignDate; ?>" />
                </div>
            </header>
            <?php $arr = explode(',', $org);
            $_words = implode("' OR orgName like '", $arr);
            $arr_intrp = explode(',', $interp);
            $_words_intrp = implode("' OR name like '", $arr_intrp); ?>
            <?php $table = '';
            $cancelOperator = 'OR';
            if (isset($_GET['paid_flag']) && $_GET['paid_flag'] == 1) {
               $order_cancel_flag =  'order_cancel_flag = 0';
               $cancelOperator ='AND';
            }
            $cancelConditionInterpreter = $add_order_cancel_condition 
                ? "(interpreter.$order_cancel_flag $cancelOperator interpreter.$order_cancel_flag_paid)" 
                : "(interpreter.$order_cancel_flag)";

            $cancelConditionTelephone = $add_order_cancel_condition 
                ? "(telephone.$order_cancel_flag $cancelOperator telephone.$order_cancel_flag_paid)" 
                : "(telephone.$order_cancel_flag)";

            $cancelConditionTranslation = $add_order_cancel_condition 
                ? "(translation.$order_cancel_flag $cancelOperator translation.$order_cancel_flag_paid)" 
                : "(translation.$order_cancel_flag)";
            if (!empty($type) && $type == 'Interpreter') {
                $query = "SELECT * from (SELECT interpreter.deleted_reason,reference_no,is_shifted,company_rate_data,checked_by,checked_date,NULL as connected_by,NULL as connected_date,NULL as hostedBy,interpreter.assignDur,'' as comunic,interpreter.orderCancelatoin,interpreter.porder,comp_reg.po_req,'Interpreter' as type,interpreter.bookinType,interpreter.time_sheet,interpreter.jobDisp,interpreter.id,interpreter.intrpName,interpreter.orgName,interpreter_reg.name,interpreter_reg.contactNo,interpreter_reg.city,interpreter_reg.country,interpreter_reg.id as int_id,interpreter.source,interpreter.target,interpreter.assignDate,interpreter.assignTime,interpreter.orgContact,interpreter.submited, interpreter.aloct_by,interpreter.aloct_date,interpreter.dated,interpreter.hrsubmited,interpreter.approved_flag,interpreter.comp_hrsubmited,interpreter.interp_hr_date, interpreter.comp_hr_date,interpreter.hoursWorkd,interpreter.C_hoursWorkd,interpreter.total_charges_comp,interpreter.total_charges_interp,interpreter.admnchargs, interpreter.cur_vat,interpreter.C_otherexpns, interpreter.credit_note,interpreter.C_admnchargs,interpreter.rAmount,interpreter.rDate,interpreter.sentemail,interpreter.printed,interpreter.printedby,interpreter.deleted_flag,interpreter.order_cancel_flag,interpreter.nameRef,interpreter.orgRef,interpreter.invoiceNo,interpreter.commit,interpreter.is_temp,comp_reg.email,comp_reg.abrv as comp_abrv FROM interpreter,interpreter_reg,comp_reg WHERE interpreter.intrpName=interpreter_reg.id AND interpreter.orgName=comp_reg.abrv AND interpreter.$multInv_flag AND interpreter.$deleted_flag $newly_processed_int $needs_approval_int and $cancelConditionInterpreter and interpreter.commit=0  $f2f_append $string_f2f_append and interpreter.assignDate like '$assignDate%' and ((interpreter.source like '$job%' OR interpreter.target like '$job%') OR (interpreter.source like '$job%' AND interpreter.target='English') OR (interpreter.source='English' AND interpreter.target like '$job%')) and interpreter_reg.name like '%$interp%' $p_org_ad " . (!empty($org) ? "and interpreter.orgName like '$org'" : "") . "  and interpreter.invoiceNo like '%$inov%') as grp WHERE type like '%$type%' ORDER BY CONCAT(assignDate,' ',assignTime) LIMIT {$startpoint} , {$limit}";
            } else if (!empty($type) && $type == 'Telephone') {
                $query = "SELECT * from (SELECT telephone.deleted_reason,reference_no,is_shifted,company_rate_data,checked_by,checked_date,telephone.connected_by,telephone.mark_join_time,telephone.connected_date,telephone.hostedBy,telephone.assignDur,telephone.comunic,telephone.orderCancelatoin,telephone.porder,comp_reg.po_req,'Telephone' as type,telephone.id,telephone.bookinType,telephone.time_sheet,telephone.jobDisp,telephone.intrpName,telephone.orgName,interpreter_reg.name,interpreter_reg.contactNo,interpreter_reg.city,interpreter_reg.country,interpreter_reg.id as int_id,telephone.source,telephone.target,telephone.assignDate,telephone.assignTime,telephone.orgContact,telephone.submited, telephone.aloct_by,telephone.aloct_date,telephone.dated,telephone.hrsubmited,telephone.approved_flag,telephone.comp_hrsubmited,telephone.interp_hr_date,telephone.comp_hr_date, telephone.hoursWorkd,telephone.C_hoursWorkd,telephone.total_charges_comp, telephone.total_charges_interp,telephone.admnchargs, telephone.cur_vat,0 as C_otherexpns,telephone.credit_note,telephone.C_admnchargs, telephone.rAmount,telephone.rDate,telephone.sentemail,telephone.printed,telephone.printedby,telephone.deleted_flag,telephone.order_cancel_flag,telephone.nameRef,telephone.orgRef,telephone.invoiceNo,telephone.commit,telephone.is_temp,comp_reg.email,comp_reg.abrv as comp_abrv FROM telephone,interpreter_reg,comp_reg WHERE telephone.intrpName=interpreter_reg.id AND telephone.orgName=comp_reg.abrv AND telephone.$multInv_flag AND telephone.$deleted_flag $newly_processed_tp $needs_approval_tp and $cancelConditionTelephone and telephone.commit=0 $tp_append $string_tp_append  and telephone.assignDate like '$assignDate%' and telephone.assignTime like '$aT%' and ((telephone.source like '$job%' OR telephone.target like '$job%') OR (telephone.source like '$job%' AND telephone.target='English') OR (telephone.source='English' AND telephone.target like '$job%')) and interpreter_reg.name like '%$interp%' $p_org_ad " . (!empty($org) ? "and telephone.orgName like '$org'" : "") . " and telephone.invoiceNo like '%$inov%') as grp WHERE type like '%$type%' ORDER BY CONCAT(assignDate,' ',assignTime) LIMIT {$startpoint} , {$limit}";
            } else if (!empty($type) && $type == 'Translation') {
                $query = "SELECT * from (SELECT translation.deleted_reason,reference_no,null as is_shifted,company_rate_data,checked_by,checked_date,NULL as connected_by,NULL as connected_date,NULL as hostedBy,0 as assignDur,'' as comunic,translation.orderCancelatoin,translation.porder,comp_reg.po_req,'Translation' as type,translation.id,translation.bookinType,translation.time_sheet,translation.jobDisp,translation.intrpName,translation.orgName,interpreter_reg.name,interpreter_reg.contactNo,interpreter_reg.city,interpreter_reg.country,interpreter_reg.id as int_id,translation.source,translation.target,translation.asignDate as assignDate,'00:00:00' as assignTime,translation.orgContact,translation.submited, translation.aloct_by,translation.aloct_date,translation.dated,translation.hrsubmited,translation.approved_flag,translation.comp_hrsubmited,translation.interp_hr_date,translation.comp_hr_date, translation.numberUnit as hoursWorkd,translation.C_numberUnit as C_hoursWorkd,translation.total_charges_comp,translation.total_charges_interp,translation.admnchargs, translation.cur_vat,0 as C_otherexpns,translation.credit_note,translation.C_admnchargs, translation.rAmount,translation.rDate,translation.sentemail,translation.printed,translation.printedby,translation.deleted_flag,translation.order_cancel_flag,translation.nameRef,translation.orgRef,translation.invoiceNo,translation.commit,translation.is_temp,comp_reg.email,comp_reg.abrv as comp_abrv FROM translation,interpreter_reg,comp_reg WHERE translation.intrpName=interpreter_reg.id AND translation.orgName=comp_reg.abrv AND translation.$multInv_flag AND translation.$deleted_flag $newly_processed_tr $needs_approval_tr and $cancelConditionTranslation and translation.commit=0 $tr_append $string_tr_append  and translation.asignDate like '$assignDate%' and ((translation.source like '$job%' OR translation.target like '$job%') OR (translation.source like '$job%' AND translation.target='English') OR (translation.source='English' AND translation.target like '$job%')) and interpreter_reg.name like '%$interp%' $p_org_ad " . (!empty($org) ? "and translation.orgName like '$org'" : "") . " and translation.invoiceNo like '%$inov%') as grp WHERE type like '%$type%' ORDER BY CONCAT(assignDate,' ',assignTime) LIMIT {$startpoint} , {$limit}";
            } else {
                $query = "SELECT * from (
                        SELECT interpreter.deleted_reason,reference_no,is_shifted,company_rate_data,checked_by,checked_date,NULL as connected_by,NULL as mark_join_time,NULL as connected_date,NULL as hostedBy,interpreter.assignDur,'' as comunic,interpreter.orderCancelatoin,interpreter.porder,comp_reg.po_req,'Interpreter' as type,interpreter.bookinType,interpreter.time_sheet,interpreter.jobDisp,interpreter.id,interpreter.intrpName,interpreter.orgName,interpreter_reg.name,interpreter_reg.contactNo,interpreter_reg.city,interpreter_reg.country,interpreter_reg.id as int_id,interpreter.source,interpreter.target,interpreter.assignDate,interpreter.assignTime,interpreter.orgContact,interpreter.submited, interpreter.aloct_by,interpreter.aloct_date,interpreter.dated,interpreter.hrsubmited,interpreter.approved_flag,interpreter.comp_hrsubmited,interpreter.interp_hr_date, interpreter.comp_hr_date,interpreter.hoursWorkd,interpreter.C_hoursWorkd,interpreter.total_charges_comp,interpreter.total_charges_interp,interpreter.admnchargs,interpreter.cur_vat,interpreter.C_otherexpns, interpreter.credit_note,interpreter.C_admnchargs,interpreter.rAmount,interpreter.rDate,interpreter.sentemail,interpreter.printed,interpreter.printedby,interpreter.deleted_flag,interpreter.order_cancel_flag,interpreter.nameRef,interpreter.orgRef,interpreter.invoiceNo,interpreter.commit,interpreter.is_temp,comp_reg.email,comp_reg.abrv as comp_abrv FROM interpreter,interpreter_reg,comp_reg WHERE interpreter.intrpName=interpreter_reg.id AND interpreter.orgName=comp_reg.abrv AND interpreter.$multInv_flag AND interpreter.$deleted_flag $newly_processed_int $needs_approval_int and $cancelConditionInterpreter and interpreter.commit=0 $f2f_append and interpreter.assignDate like '$assignDate%' and ((interpreter.source like '$job%' OR interpreter.target like '$job%') OR (interpreter.source like '$job%' AND interpreter.target='English') OR (interpreter.source='English' AND interpreter.target like '$job%')) and interpreter_reg.name like '%$interp%' $p_org_ad " . (!empty($org) ? "and interpreter.orgName like '$org'" : "") . " and interpreter.invoiceNo like '%$inov%' $string_f2f_append
                        UNION ALL SELECT telephone.deleted_reason,reference_no,is_shifted,company_rate_data,checked_by,checked_date,telephone.connected_by,telephone.mark_join_time,telephone.connected_date,telephone.hostedBy,telephone.assignDur,telephone.comunic,telephone.orderCancelatoin,telephone.porder,comp_reg.po_req,'Telephone' as type,telephone.bookinType,telephone.time_sheet,telephone.jobDisp,telephone.id,telephone.intrpName,telephone.orgName,interpreter_reg.name,interpreter_reg.contactNo,interpreter_reg.city,interpreter_reg.country,interpreter_reg.id as int_id,telephone.source,telephone.target,telephone.assignDate,telephone.assignTime,telephone.orgContact,telephone.submited, telephone.aloct_by,telephone.aloct_date,telephone.dated,telephone.hrsubmited,telephone.approved_flag,telephone.comp_hrsubmited,telephone.interp_hr_date,telephone.comp_hr_date, telephone.hoursWorkd,telephone.C_hoursWorkd,telephone.total_charges_comp,telephone.total_charges_interp,telephone.admnchargs, telephone.cur_vat,0 as C_otherexpns,telephone.credit_note,telephone.C_admnchargs, telephone.rAmount,telephone.rDate,telephone.sentemail,telephone.printed,telephone.printedby,telephone.deleted_flag,telephone.order_cancel_flag,telephone.nameRef,telephone.orgRef,telephone.invoiceNo,telephone.commit,telephone.is_temp,comp_reg.email,comp_reg.abrv as comp_abrv FROM telephone,interpreter_reg,comp_reg WHERE telephone.intrpName=interpreter_reg.id AND telephone.orgName=comp_reg.abrv AND telephone.$multInv_flag AND telephone.$deleted_flag $newly_processed_tp $needs_approval_tp and $cancelConditionTelephone and telephone.commit=0 $tp_append and telephone.assignDate like '$assignDate%' and telephone.assignTime like '$aT%' and ((telephone.source like '$job%' OR telephone.target like '$job%') OR (telephone.source like '$job%' AND telephone.target='English') OR (telephone.source='English' AND telephone.target like '$job%')) and interpreter_reg.name like '%$interp%' $p_org_ad " . (!empty($org) ? "and telephone.orgName like '$org'" : "") . " and telephone.invoiceNo like '%$inov%' $string_tp_append
                        UNION ALL SELECT translation.deleted_reason,reference_no,null as is_shifted,company_rate_data,checked_by,checked_date,NULL as connected_by, NULL as mark_join_time,NULL as connected_date,NULL as hostedBy,0 as assignDur,'' as comunic,translation.orderCancelatoin,translation.porder,comp_reg.po_req,'Translation' as type,translation.bookinType,translation.time_sheet,translation.jobDisp,translation.id,translation.intrpName,translation.orgName,interpreter_reg.name,interpreter_reg.contactNo,interpreter_reg.city,interpreter_reg.country,interpreter_reg.id as int_id,translation.source,translation.target,translation.asignDate as assignDate,'00:00:00' as assignTime,translation.orgContact,translation.submited, translation.aloct_by,translation.aloct_date,translation.dated,translation.hrsubmited,translation.approved_flag,translation.comp_hrsubmited,translation.interp_hr_date,translation.comp_hr_date, translation.numberUnit as hoursWorkd,translation.C_numberUnit as C_hoursWorkd,translation.total_charges_comp,translation.total_charges_interp,translation.admnchargs,translation.cur_vat,0 as C_otherexpns,translation.credit_note,translation.C_admnchargs, translation.rAmount,translation.rDate,translation.sentemail,translation.printed,translation.printedby,translation.deleted_flag,translation.order_cancel_flag,translation.nameRef,translation.orgRef,translation.invoiceNo,translation.commit,translation.is_temp,comp_reg.email,comp_reg.abrv as comp_abrv FROM translation,interpreter_reg,comp_reg WHERE translation.intrpName=interpreter_reg.id AND translation.orgName=comp_reg.abrv AND translation.$multInv_flag AND translation.$deleted_flag $newly_processed_tr $needs_approval_tr and $cancelConditionTranslation and translation.commit=0 $tr_append and translation.asignDate like '$assignDate%' and ((translation.source like '$job%' OR translation.target like '$job%') OR (translation.source like '$job%' AND translation.target='English') OR (translation.source='English' AND translation.target like '$job%')) and interpreter_reg.name like '%$interp%' $p_org_ad " . (!empty($org) ? "and translation.orgName like '$org'" : "") . " and translation.invoiceNo like '%$inov%' $string_tr_append
                    ) as grp ORDER BY CONCAT(assignDate,' ',assignTime) LIMIT {$startpoint} , {$limit}";
            // die($query);
            }
            // echo $query;
            // die();exit();
            ?>
            <div class="tab_container" id="put_data">
                <center>
                    <div class="col-md-12"><?php echo pagination($con, $table, $query, $limit, $page); ?></div>
                </center>
                <table class="table table-bordered tbl_data" cellspacing="0" cellpadding="0">
                    <thead class="bg-primary">
                        <tr>
                            <td>Interpreter</td>
                            <td>Language</td>
                            <td width="8%">Assign-Date</td>
                            <td>Interpreter City</td>
                            <td>Company</td>
                            <td>Contact Name</td>
                            <td>Ref Name</td>
                            <?php if ($tp == 'tr' || $tp == 'c') { ?>
                                <td>Interpreter Payment</td>
                                <td>Client Charges</td>
                            <?php } ?>
                            <td width="10%">Booking Type</td>
                            <td width="18%">Details</td>
                            <td>Actions</td>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // echo $query;exit; 
                        $result = mysqli_query($con, $query);
                        if (mysqli_num_rows($result) == 0) {
                            echo '<tr>
                  <td colspan="9"><h4 class="text-danger text-center"><b>Sorry ! There are no records.</b></h4></td></tr>';
                        } else {
                            while ($row = mysqli_fetch_array($result)) {
                                // debug($row['orderCancelatoin']);continue;
                                $alert_hours = "";
                                if ($row['type'] == 'Interpreter') {

                                    // shows only for deleted & cancelled
                                    $total_charges_interp = $row['total_charges_interp'];
                                    $total_charges_comp = $row['total_charges_comp'] - $row['C_deduction'];

                                    $field_to_check = $row['hoursWorkd'];
                                    $edit_page = 'interp_edit.php';
                                    $exp_page = 'expenses_f2f.php';
                                    $exp_page_new = 'expenses_f2f_new.php';
                                    $inv_page = 'invoice.php';
                                    $comp_earning = 'comp_earning.php';
                                    $credit_page = 'credit_interp.php';
                                    $history_page = 'interp_list_edited.php';
                                    $diff = round($row['hoursWorkd'] * 60) - $row['assignDur'];
                                    if ($diff > 15) {
                                        $alert_hours = "<br><i title='" . $diff . " minutes difference. Please verify in expenses!' class='w3-button w3-small w3-circle w3-red'>" . $diff . "</i>";
                                    }
                                } else if ($row['type'] == 'Telephone') {

                                    // shows only for deleted & cancelled
                                    $total_charges_interp = $row['total_charges_interp'];
                                    $total_charges_comp = $row['total_charges_comp'];

                                    $field_to_check = $row['hoursWorkd'];
                                    $edit_page = 'telep_edit.php';
                                    $exp_page = 'expenses_tp.php';
                                    $exp_page_new = 'expenses_tp_new.php';
                                    $inv_page = 'telep_invoice.php';
                                    $comp_earning = 'comp_earning_telep.php';
                                    $credit_page = 'credit_telep.php';
                                    $history_page = 'telep_list_edited.php';
                                    $diff = round($row['hoursWorkd'] - $row['assignDur']);
                                    if ($diff > 10) {
                                        $alert_hours = "<br><i title='" . $diff . " minutes difference. Please verify in expenses!' class='w3-button w3-small w3-circle w3-red'>" . $diff . "</i>";
                                    }
                                } else {

                                    // shows only for deleted & cancelled
                                    $total_charges_interp = $row['total_charges_interp'] + $row['admnchargs'];
                                    $total_charges_comp = $row['total_charges_comp'];

                                    $field_to_check = $row['numberUnit'];
                                    $edit_page = 'trans_edit.php';
                                    $exp_page = 'expenses_tr.php';
                                    $exp_page_new = 'expenses_tr_new.php';
                                    $inv_page = 'trans_invoice.php';
                                    $comp_earning = 'comp_earning_trans.php';
                                    $credit_page = 'credit_trans.php';
                                    $history_page = 'trans_list_edited.php';
                                }
                                $page_count++; ?>
                                <tr data-at="<?= $row['hrsubmited'] . ' - ' .  $row['approved_flag'] ?>" 
                                    <?php 
                                        if ($row['is_temp'] == 1) {
                                            echo "class='is_temp' title='Expenses has been updated by temprory role!'";
                                        } 
                                    ?> >
                                    <td>
    
                                        <?php echo '<span class="w3-badge w3-blue badge-counter">' . $page_count . '</span>'; ?>
                                        <span style="cursor:pointer;" <?php if ($action_view_interpreter_profile_booking) { ?>onClick="popupwindow('full_view_interpreter.php?view_id=<?php echo $row['int_id']; ?>', 'View profile of interpreter', 1100, 900);" <?php } ?> class="w3-small">
                                            <?php if ($row['hoursWorkd'] == 0) {  ?>
                                                <span class="w3-text-red"><?php echo $row['name']; ?></span>
                                            <?php } else { ?>
                                                <span class="w3-text-black" title="Interpreter Hours: <?= $row['hrsubmited'] . ' (' . $misc->dated($row['interp_hr_date']) . ')'; ?>"> <?= $row['name'] . ($row['hrsubmited'] == "Self" && $row['approved_flag'] == 0 ? "<br><small title='Please approve interpreter uploaded hours first!' class='label label-danger'>Needs Approval</small>" : "") . $alert_hours; ?></span>
                                            <?php } ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="w3-small p3"><?php echo $row['source'] . ' to ' . $row['target']; ?></span>
                                        <br><b class="text-danger" style="font-size: 14px;"><?= "Job ID " . $row['id']; ?></b>
                                    </td>
                                    <td>
                                        <span><b><?php echo '<span ' . $bg_aD . '>' . date('d-m-Y', strtotime($row['assignDate'])) . '</span> ' . ' <span ' . $bg_aT . '>' . substr($row['assignTime'], 0, 5) . '</span>'; ?></b></span>
                                    </td>
                                    <td>
                                        <?php echo $row['city']; ?>
                                    </td>
                                    <td>
                                        <?php if ($row['C_hoursWorkd'] == 0) { ?>
                                            <span class="w3-text-red"><?php echo $row['orgName']; ?></span><span class="w3-medium" style="font-weight:bold;margin-top:-10px;font-size:14px!important;"></span>
                                        <?php } else { ?>
                                            <span class="w3-text-black" title="<?php echo $row['comp_hrsubmited'] . ' (' . $misc->dated($row['comp_hr_date']) . ')'; ?>"><?php echo $row['orgName']; ?></span>
                                        <?php }
                                        echo ($row['is_shifted'] == 1 ? '<br><small class="label label-danger">Job Shifted</small>' : ''); ?>
                                    </td>
                                    <td><span>
                                            <?php echo $row['orgContact'];
                                            if (empty($row['checked_by'])) {
                                                if ($action_check_job) {
                                                    $job_type = "'" . $array_order_labels[$row['type']] . "'"; ?>
                                                    <a href="javascript:void(0)" class="w3-button w3-small w3-circle w3-blue w3-border w3-border-black pull-right" title="Check this job" onclick="check_the_job(this, <?= $row['id'] . ',' . $job_type ?>)"><i class="fa fa-check text-white"></i></a>
                                            <?php }
                                            } else {
                                                $get_checked_user = $acttObj->read_specific("name", "login", "id=" . $row['checked_by'])['name'];
                                                $get_checked_date = $misc->dated($row['checked_date']);
                                                echo '<br><small class="text-primary" title="This job has been checked by ' . $get_checked_user . " on " . $get_checked_date . '"><b>' . $get_checked_user . " <i class='fa fa-thumbs-up'></i> <br> " . $get_checked_date . '</b></small>';
                                            } ?>
                                        </span></td>
                                    <td width="15%">
                                        <span class="<?php echo strlen($row['orgRef']) > 20 ? 'w3-small' : 'w3-medium'; ?>"><?php echo $row['orgRef']; ?></span>
                                        <?php if ($row['type'] == "Telephone" && $row['hostedBy'] == 1) {
                                            echo "<div class='div_connect_call'>";
                                            if (!is_null($row['connected_by'])) {
                                                $connected_user = $acttObj->read_specific("name", "login", "id=" . $row['connected_by'])['name'];
                                                echo "<small title='Call connected by $connected_user' class='text-success'><b>" . $connected_user . " <i class='fa fa-check-circle text-success'></i><br>" . $row['connected_date'] . "</b></small>";
                                            } else {
                                                if ($action_connect_telephone_call) {
                                                    $current_datetime = date("Y-m-d H:i");
                                                    $assignment_datetime = date('Y-m-d H:i', strtotime($row['assignDate'] . ' ' . substr($row['assignTime'], 0, 5)));
                                                    $fifteen_minutes_before = date('Y-m-d H:i', strtotime('-15 minutes', strtotime($assignment_datetime)));
                                                    if ($current_datetime >= $fifteen_minutes_before) { ?>
                                                        <button data-now="<?= $current_datetime ?>" data-assignment-time="<?= $assignment_datetime ?>" type="button" onclick="connect_the_call(this, <?= $row['id'] ?>)" class="btn btn-sm btn-success" title="Click to connect the call">Connect The Call</button>
                                                        <?php
                                                        $job_id = $row['id'];
                                                        $job = $acttObj->read_all('source, target, assignDur, assignTime, inchContact, inchEmail, inchPerson, contactNo, noClient, intrpName', 'telephone', "id = '$job_id'");
                                                        $job = mysqli_fetch_assoc($job);
                                                        $interpreter_id = $job['intrpName'];

                                                        $minutes = $job['assignDur'];

                                                        if ($minutes >= 60) {
                                                            $hours = floor($minutes / 60);
                                                            $remainingMinutes = $minutes % 60;

                                                            $hourText = $hours . ' hour' . ($hours == 1 ? '' : 's');
                                                            if ($remainingMinutes > 0) {
                                                                $minuteText = $remainingMinutes . ' minute' . ($remainingMinutes == 1 ? '' : 's');
                                                                $duration = "$hourText $minuteText";
                                                            } else {
                                                                $duration = $hourText;
                                                            }
                                                        } else {
                                                            $duration = $minutes . ' minute' . ($minutes == 1 ? '' : 's');
                                                        }

                                                        $interpreter = $acttObj->read_all('contactNo, contactNo2, other_number, email', 'interpreter_reg', "id = '$interpreter_id'");
                                                        $interpreter = mysqli_fetch_assoc($interpreter); ?>
                                                        <button type="button" class="btn btn-sm btn-info view-connection"
                                                            data-inch-contact="<?= $job['inchContact'] ?>"
                                                            data-inch-email="<?= $job['inchEmail'] ?>"
                                                            data-inch-person="<?= $job['inchPerson'] ?>"
                                                            data-client-contact="<?= $job['contactNo'] ?>"
                                                            data-client-source="<?= $job['source'] ?>"
                                                            data-client-target="<?= $job['target'] ?>"
                                                            data-client-duration="<?= $duration ?>"
                                                            data-client-time="<?= date('H:i', strtotime($job['assignTime'])) ?>"
                                                            data-noclient="<?= $job['noClient'] ?>"
                                                            data-interp-contact1="<?= $interpreter['contactNo'] ?>"
                                                            data-interp-contact2="<?= $interpreter['contactNo2'] ?>"
                                                            data-interp-other="<?= $interpreter['other_number'] ?>"
                                                            data-interp-email="<?= $interpreter['email'] ?>">
                                                            Connection Info
                                                        </button>
                                                        <div class="modal fade" id="connectionInfoModal" tabindex="-1" aria-hidden="true">
                                                            <div class="modal-dialog modal-md">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <h5 class="modal-title">Connection Info</h5>
                                                                    </div>
                                                                    <div class="modal-body" id="connectionInfoBody">
                                                                        <!-- Content will be injected -->
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                        <?php } else {
                                                        echo "<small title='Expected time of connecting this call set to " . date('d-m-Y H:i', strtotime($assignment_datetime)) . "'><i class='fa fa-exclamation-circle text-danger'></i> Expecting Connect Time:<br>" . date('d-m-Y H:i', strtotime($assignment_datetime)) . "</small>";
                                                    }
                                                }
                                            }
                                            echo "</div>";
                                        }
                                        if ($row['type'] == "Telephone" && $row['hostedBy'] == 2) //client to host
                                        {

                                            if (!is_null($row['connected_by'])) {
                                                echo "<small class='text-success'><b><br>Session Started: " . $row['mark_join_time'] . "<br>by: " . $acttObj->read_specific("name", "interpreter_reg", "id=" . $row['connected_by'])['name'] . "<i class='fa fa-check-circle text-success'></i></b></small>";
                                            } else {
                                                echo "<br><small><b class='text-danger'>Session Not Yet Started<b></small>";
                                            }
                                        }
                                        ?>
                                    </td>
                                    <?php if ($tp == 'tr' || $tp == 'c') { ?>
                                        <td>
                                            <?php echo $total_charges_interp; ?>
                                        </td>
                                        <td>
                                            <?php echo $total_charges_comp; ?>
                                        </td>
                                    <?php } ?>
                                    <td>
                                        <?php if (!empty($row['company_rate_data']) && $row['company_rate_data'] != null) {
                                            $booking_type_array = json_decode($row['company_rate_data'], true);
                                            // $booking_type = explode("-", $booking_type_array['title']);
                                            echo "<small>" . trim($booking_type_array['title']) . "</small>";
                                        } else {
                                            echo ucwords($row['bookinType']) ?: 'Nil';
                                        }
                                        echo ($row['deleted_reason'] ? ' <i class="fa fa-exclamation-circle" title="' . $row['deleted_reason'] . '"></i>' : ''); ?></td>
                                    <td><span>
                                            <?php if ($row['type'] == 'Telephone') {
                                                echo $row['hostedBy'] ? $call_types[$row['hostedBy']] : '';
                                            }
                                            $get_type = $acttObj->read_specific("c_title,c_image", "comunic_types", "c_id=" . $row['comunic']);
                                            echo $row['type'] == "Telephone" ? '<img data-toggle="popover" data-trigger="hover" data-placement="left" data-content="' . $get_type['c_title'] . '" src="images/comunic_types/' . $get_type['c_image'] . '" width="36"/> ' : ""; ?></span>
                                        <span title="<b>JOB SUBMISSIONS</b>" data-toggle="popover" data-trigger="hover" data-placement="top" style="text-decoration:none;" data-content="<?php echo 'Job submitted by:<br><b>' . $row['submited'] . ' (' . $misc->dated($row['dated']) . ')</b><br>Job allocated By:<br><b>' . ucwords($row['aloct_by']) . ' (' . $misc->dated($row['aloct_date']) . ')<b>'; ?>" class="w3-badge w3-blue">?</span>
                                        <span title="<b>HOURS SUBMISSIONS</b>" data-toggle="popover" data-trigger="hover" data-placement="top" style="text-decoration:none;" data-content="<?php echo 'Interp Hrz submitted by:<br><b>' . $row['hrsubmited'] . ' (' . $misc->dated($row['interp_hr_date']) . ')</b><br>Comp Hrz submitted by:<br><b>' . $row['comp_hrsubmited'] . ' (' . $misc->dated($row['comp_hr_date']) . ')<b>'; ?>"><i class="fa fa-clock-o" style="font-size: 20px;"></i></span>
                                        <?php if ($action_download_lsuk_timesheet) { ?>
                                            <button class="btn btn-default btn-xs pull-right" onclick="popupwindow('reports_lsuk/pdf/new_timesheet.php?update_id=<?php echo $row['id']; ?>&table=<?php echo strtolower($row['type']); ?>&down', 'title', 1000, 1000);" href="javascript:void(0)" data-toggle="popover" data-trigger="hover" data-placement="top" data-content="Download Timesheet"><i class="glyphicon glyphicon-download"></i> Download</button>
                                        <?php }
                                        if ($row['po_req'] == 1 && $row['porder'] != '') {
                                            echo '<br><b class="pull-left" title="Purch.Order# is updated">' . $row['porder'] . '</b>';
                                        } else if ($row['po_req'] == 1 && $row['porder'] == '') {
                                            echo '<span class="w3-badge w3-red" data-content="Purchase Order No missing !" data-toggle="popover" data-trigger="hover" data-placement="left"><i class="fa fa-remove"></i></span>';
                                        } else {
                                            echo '';
                                        } ?>
                                    </td>

                                    <?php
                                    //##gotcreditnote
                                    if ($row['type'] == 'Interpreter') {
                                        $totalforvat = $row['total_charges_comp'];
                                        $vatpay = $totalforvat * $row['cur_vat'];
                                        $totinvnow = $totalforvat + $vatpay + $row['C_otherexpns'];
                                    } else if ($row['type'] == 'Telephone') {
                                        $totalforvat = $row['total_charges_comp'];
                                        $vatpay = $totalforvat * $row['cur_vat'];
                                        $totinvnow = $totalforvat + $vatpay;
                                    } else {
                                        $totalforvat = $row['total_charges_comp'];
                                        $vatpay = $totalforvat * $row['cur_vat'];
                                        $totinvnow = $totalforvat + $vatpay;
                                    }

                                    $gotcreditnote = false;
                                    // echo "<pre>";print_r($row); die;
                                    if (isset($row['credit_note']) && $row['credit_note'] != "") {
                                        $totinvnow = 0;
                                        $gotcreditnote = true;
                                    } ?>
                                    <td <?= ($tp == 'tr' || $tp == 'c') ? "width='11%'" : "";
                                        if ($_SESSION['is_root'] == 0) {
                                            echo "width='20%'";
                                        } ?>>
                                        <div class="col-sm-12 action_buttons" style="min-height: 30px;">
                                            <?php if ($action_dropdown_trashed_jobs && $tp == 'tr') {
                                                if ($action_view_job) { ?>
                                                    <a href="javascript:void(0)" onClick="popupwindow('order_view.php?view_id=<?php echo $row['id']; ?>&table=<?php echo strtolower($row['type']); ?>', 'View Order booking', 1000, 1000);"><input type="image" src="images/icn_new_article.png" title="View Order"></a>
                                                <?php }
                                                if ($action_restore_job) { ?>
                                                    <a class="w3-button w3-small w3-circle w3-white w3-border w3-border-green" title="Restore Order" href="javascript:void(0)" onClick="popupwindow('trash_restore.php?del_id=<?php echo $row['id']; ?>&table=<?php echo strtolower($row['type']); ?>','Restore order booking',520,350)"><i class="fa fa-undo"></i></a>
                                                <?php }
                                                if ($action_job_note) {
                                                    $arr_n = $acttObj->read_specific("(select count(id)", "jobnotes", " deleted_flag = 0 And tbl='" . strtolower($row['type']) . "' and fid=" . $row['id'] . " and (readcount is null or readcount=0)) as unread ,(select count(id) from jobnotes where deleted_flag = 0 And tbl='" . strtolower($row['type']) . "' and fid=" . $row['id'] . " and (readcount is not null and readcount!=0)) as yes_read"); ?>
                                                    <a href="javascript:void(0)" title="JOB NOTES" data-toggle="popover" data-trigger="hover" data-placement="top" style="text-decoration:none;" data-content="<?php echo '<b>' . $arr_n['unread'] . '</b> unread <b>' . $arr_n['yes_read'] . '</b> read job notes'; ?>" onclick="popupwindow('jobnote.php?fid=<?php echo $row['id']; ?>&table=<?php echo strtolower($row['type']); ?>&orgName=<?php echo urlencode($row['orgName']); ?>','Job notes booking',900,800)" <?php echo $arr_n['unread'] > 0 ? 'class="w3-button w3-small w3-circle w3-blue"' : 'class="w3-button w3-small w3-circle w3-grey"'; ?>><?php echo $arr_n['unread'] > 0 ? $arr_n['unread'] : $arr_n['yes_read']; ?></a>
                                                <?php }
                                                if ($row['type'] == 'Interpreter') {
                                                    echo "<span class='label label-success lbl'>F2F</span>";
                                                } else if ($row['type'] == 'Telephone') {
                                                    echo "<span class='label label-info lbl'>TP</span>";
                                                } else {
                                                    echo "<span class='label label-warning lbl'>TR</span>";
                                                }
                                            } else if ($action_dropdown_cancelled_jobs && $tp == 'c') {
                                                if ($action_view_job) { ?>
                                                    <a href="javascript:void(0)" onClick="popupwindow('order_view.php?view_id=<?php echo $row['id']; ?>&table=<?php echo strtolower($row['type']); ?>', 'View Order booking', 1000, 1000);"><input type="image" src="images/icn_new_article.png" title="View Order"></a>
                                                <?php }
                                                if ($action_resume_job) { ?>
                                                    <a class="w3-button w3-small w3-circle w3-white w3-border w3-border-green" title="Restore Order" href="javascript:void(0)" onClick="popupwindow('email_resume.php?email_id=<?php echo $row['id']; ?>&table=<?php echo strtolower($row['type']); ?>&orgName=<?php echo urlencode($row['orgName']); ?>','Resume order booking',620,450)"><i class="fa fa-undo"></i></a>
                                                <?php }
                                                if ($action_job_note) {
                                                    $arr_n = $acttObj->read_specific("(select count(id)", "jobnotes", "deleted_flag = 0 And tbl='" . strtolower($row['type']) . "' and fid=" . $row['id'] . " and (readcount is null or readcount=0)) as unread ,(select count(id) from jobnotes where deleted_flag = 0 And tbl='" . strtolower($row['type']) . "' and fid=" . $row['id'] . " and (readcount is not null and readcount!=0)) as yes_read"); ?>
                                                    <a href="javascript:void(0)" title="JOB NOTES" data-toggle="popover" data-trigger="hover" data-placement="top" style="text-decoration:none;" data-content="<?php echo '<b>' . $arr_n['unread'] . '</b> unread <b>' . $arr_n['yes_read'] . '</b> read job notes'; ?>" onclick="popupwindow('jobnote.php?fid=<?php echo $row['id']; ?>&table=<?php echo strtolower($row['type']); ?>&orgName=<?php echo urlencode($row['orgName']); ?>','Job notes booking',900,800)" <?php echo $arr_n['unread'] > 0 ? 'class="w3-button w3-small w3-circle w3-blue"' : 'class="w3-button w3-small w3-circle w3-grey"'; ?>><?php echo $arr_n['unread'] > 0 ? $arr_n['unread'] : $arr_n['yes_read']; ?></a>
                                                <?php }
                                                if ($row['type'] == 'Interpreter') {
                                                    echo "<span class='label label-success lbl'>F2F</span>";
                                                } else if ($row['type'] == 'Telephone') {
                                                    echo "<span class='label label-info lbl'>TP</span>";
                                                } else {
                                                    echo "<span class='label label-warning lbl'>TR</span>";
                                                }
                                            } else if ($action_dropdown_multi_invoice_jobs && $tp == 'ml') {
                                                if ($action_view_job) { ?>
                                                    <a href="javascript:void(0)" onClick="popupwindow('order_view.php?view_id=<?php echo $row['id']; ?>&table=<?php echo strtolower($row['type']); ?>', 'View Order booking', 1000, 1000);"><input type="image" src="images/icn_new_article.png" title="View Order"></a>
                                                <?php }
                                                if ($action_job_note) {
                                                    $arr_n = $acttObj->read_specific("(select count(id)", "jobnotes", "deleted_flag = 0 And tbl='" . strtolower($row['type']) . "' and fid=" . $row['id'] . " and (readcount is null or readcount=0)) as unread ,(select count(id) from jobnotes where deleted_flag = 0 And tbl='" . strtolower($row['type']) . "' and fid=" . $row['id'] . " and (readcount is not null and readcount!=0)) as yes_read"); ?>
                                                    <a href="javascript:void(0)" title="JOB NOTES" data-toggle="popover" data-trigger="hover" data-placement="top" style="text-decoration:none;" data-content="<?php echo '<b>' . $arr_n['unread'] . '</b> unread <b>' . $arr_n['yes_read'] . '</b> read job notes'; ?>" onclick="popupwindow('jobnote.php?fid=<?php echo $row['id']; ?>&table=<?php echo strtolower($row['type']); ?>&orgName=<?php echo urlencode($row['orgName']); ?>','Job notes booking',900,800)" <?php echo $arr_n['unread'] > 0 ? 'class="w3-button w3-small w3-circle w3-blue"' : 'class="w3-button w3-small w3-circle w3-grey"'; ?>><?php echo $arr_n['unread'] > 0 ? $arr_n['unread'] : $arr_n['yes_read']; ?></a>
                                                <?php }
                                                if ($action_duplicate) { ?>
                                                    <a href="javascript:void(0)" onClick="popupwindow('<?php echo $edit_page; ?>?edit_id=<?php echo $row['id']; ?>&duplicate=<?php echo 'yes'; ?>', 'Duplicate Order booking', 1250, 730);" title="Create Duplicate of this order"><i class="fa fa-copy"></i></a></a>
                                                <?php }
                                                if ($action_edit_job && ($field_to_check == 0 || ($field_to_check > 0 && $action_can_force_update))) { ?>
                                                    <a href="javascript:void(0)" onClick="popupwindow('<?php echo $edit_page; ?>?edit_id=<?php echo $row['id']; ?>', 'Edit Order booking', 1250, 730);"><input type="image" src="images/icn_edit.png" title="Edit"></a>
                                                <?php }
                                                if ($action_amend_job && ($field_to_check == 0 || ($field_to_check > 0 && $_SESSION['is_root'] == 1))) { ?>
                                                    <a href="javascript:void(0)" onClick="MM_openBrWindow('email_emend.php?email_id=<?php echo $row['id']; ?>&table=<?php echo strtolower($row['type']); ?>&col=intrpName','Amend order booking','scrollbars=yes,resizable=yes,width=400,height=200')"><input type="image" src="images/icn_jump_back.png" title="Go Home Screen"></a>
                                                <?php }
                                                if ($action_update_expenses && ($field_to_check == 0 || ($field_to_check > 0 && $action_can_force_update))) { ?>
                                                    <a href="javascript:void(0)" onClick="popupwindow('<?php echo $exp_page; ?>?update_id=<?php echo $row['id']; ?>', 'Update expense booking', 1150, 620);"><i class="fa fa-refresh text-primary" title="Update Job Expenses"></i></a>
                                                    <?php }
                                                if ($row['hrsubmited'] != "Self" || ($row['hrsubmited'] == "Self" && $row['approved_flag'] == 1)) {
                                                    if ($action_view_invoice) { ?>
                                                        <a href="javascript:void(0)" onClick="popupwindow('<?php echo $inv_page; ?>?invoice_id=<?php echo $row['id']; ?>','View invoice booking', 1000, 1000);"><input type="image" src="images/invoice.png" title="Invoice"></a>
                                                    <?php }
                                                }
                                                if ($action_view_credit_note && $row['credit_note']) { ?>
                                                    <a href="javascript:void(0)" onClick="MM_openBrWindow('<?php echo $credit_page; ?>?invoice_id=<?php echo $row['id']; ?>','Credit note booking','scrollbars=yes,resizable=yes,width=900,height=800,left=450,top=10')"><i class="fa fa-exclamation-circle text-danger" title="Credit Note"></i></a>
                                                    <?php }
                                                if ($row['orderCancelatoin'] == 0) {
                                                    if ($action_cancel_job) { ?>
                                                        <a href="javascript:void(0)" onClick="popupwindow('cancel_order.php?job_id=<?php echo $row['id']; ?>&table=<?php echo strtolower($row['type']); ?>&orgName=<?php echo urlencode($row['orgName']); ?>&lang=<?php echo $row['source']; ?>','Cancel order booking',900, 600)"><input type="image" src="images/top_icon.png" title="Order Cancelation"></a>
                                                    <?php }
                                                } else {
                                                    if ($action_resume_job) { ?>
                                                        <a href="javascript:void(0)" onClick="popupwindow('email_resume.php?email_id=<?php echo $row['id']; ?>&table=<?php echo strtolower($row['type']); ?>&orgName=<?php echo urlencode($row['orgName']); ?>','Resume order booking',650, 450)"><input type="image" src="images/icn_alert_error.png" title="Order Canceled"></a>
                                                    <?php }
                                                }
                                                if (($row['po_req'] == 1 && $row['porder'] != '') || ($row['po_req'] == 1 && $row['porder'] == '')) {
                                                    if ($action_purchase_order) { ?>
                                                        <a href="javascript:void(0)" onClick="popupwindow('purch_update.php?purch_id=<?php echo $row['id']; ?>&table=<?php echo strtolower($row['type']); ?>&orgName=<?php echo urlencode($row['orgName']); ?>','Update purchase order booking',600,550)"><input type="image" src="images/icn_tags.png" title="Update Purchase Order #"></a>
                                                    <?php }
                                                }
                                                if ($action_view_earnings) { ?>
                                                    <a href="javascript:void(0)" onClick="popupwindow('<?php echo $comp_earning; ?>?view_id=<?php echo $row['id']; ?>', 'View company earning booking', 800, 600);"><input type="image" src="images/earning.png" title="Earning"></a>
                                                <?php }
                                                if ($action_edited_history) { ?>
                                                    <a data-table-name="<?= strtolower($row['type']) ?>" data-record-id="<?= $row['id'] ?>" onclick="view_log_changes(this)" href="javascript:void(0)" title="View Log Edited History"><i class="fa fa-list text-danger"></i></a>
                                                <?php }
                                                if ($row['type'] == 'Interpreter') {
                                                    echo "<span class='label label-success lbl'>F2F</span>";
                                                } else if ($row['type'] == 'Telephone') {
                                                    echo "<span class='label label-info lbl'>TP</span>";
                                                } else {
                                                    echo "<span class='label label-warning lbl'>TR</span>";
                                                }
                                            } else { ?>
                                                <div class="dropdown dropdown_actions">
                                                    <button class="btn btn-primary btn-xs dropdown-toggle" type="button" id="menu1" data-toggle="dropdown">Action <span class="caret"></span></button>
                                                    <ul class="dropdown-menu list-inline" role="menu" aria-labelledby="menu1">
                                                        <?php if ($action_view_job) { ?>
                                                            <li><a class="w3-button w3-small w3-circle w3-white w3-border w3-border-blue" title="View Order" href="javascript:void(0)" onClick="popupwindow('order_view.php?view_id=<?php echo $row['id']; ?>&table=<?php echo strtolower($row['type']); ?>', 'View order booking', 1200, 650);"><i class="fa fa-eye"></i></a></li>
                                                        <?php }
                                                        if ($action_duplicate) { ?>
                                                            <li><a class="w3-button w3-small w3-circle w3-white w3-border w3-border-blue" title="Create Duplicate" href="javascript:void(0)" onClick="popupwindow('<?php echo $edit_page; ?>?edit_id=<?php echo $row['id']; ?>&duplicate=<?php echo 'yes'; ?>', 'Duplicate order booking', 1250, 730);"><i class="fa fa-copy"></i></a></li>
                                                        <?php }
                                                        if ($action_edit_job && ($field_to_check == 0 || ($field_to_check > 0 && $action_can_force_update))) { ?>
                                                            <li><a class="w3-button w3-small w3-circle w3-white w3-border w3-border-blue" title="Edit" href="javascript:void(0)" onClick="popupwindow('<?php echo $edit_page; ?>?edit_id=<?php echo $row['id']; ?>', 'Edit order booking', 1250, 730);"><i class="fa fa-pencil"></i></a></li>
                                                        <?php }
                                                        if ($action_amend_job && ($field_to_check == 0 || ($field_to_check > 0 && $_SESSION['is_root'] == 1))) { ?>
                                                            <li><a title="Amend this Job" href="javascript:void(0)" class="w3-button w3-small w3-circle w3-white w3-border w3-border-blue" onClick="popupwindow('email_emend.php?email_id=<?php echo $row['id']; ?>&table=<?php echo strtolower($row['type']); ?>&col=intrpName','Amend email booking', 950, 650)"><i class="fa fa-undo"></i></a></li>
                                                        <?php }
                                                        if ($action_delete_job) { ?>
                                                            <li title="Trash Record"><a href="javascript:void(0)" class="w3-button w3-small w3-circle w3-white w3-border w3-border-red" onClick="popupwindow('del_trash.php?del_id=<?php echo $row['id']; ?>&table=<?php echo strtolower($row['type']); ?>','Delete order booking', 500,350);"><i class="fa fa-trash"></i></a></li>
                                                        <?php } ?>
                                                    </ul>
                                                </div>
                                                <div class="dropdown dropdown_actions2">
                                                    <button class="btn btn-default btn-xs dropdown-toggle" type="button" id="menu2" data-toggle="dropdown">Update <span class="caret"></span></button>
                                                    <ul class="dropdown-menu" role="menu" aria-labelledby="menu2">
                                                        <?php if ($action_update_expenses && ($field_to_check == 0 || ($field_to_check > 0 && $action_can_force_update))) { ?>
                                                            <li><a title="Update Expenses" href="javascript:void(0)" onClick="popupwindow('<?php echo $exp_page; ?>?update_id=<?php echo $row['id']; ?>', 'Update expenses booking', 1150, 620);"><i class="fa fa-refresh"></i> Update All Expenses</a></li>
                                                        <?php }
                                                        if ($action_add_lateness && $row['type'] != "Translation") { ?>
                                                            <li><a class="text-danger" title="Update Interpreter Lateness" href="javascript:void(0)" onClick="popupwindow('interpreter_lateness.php?job_id=<?php echo $row['id']; ?>&job_type=<?= $array_order_types[$row['type']] ?>', 'Interpreter Lateness', 1150, 620);"><i class="fa fa-exclamation-circle"></i> Interpreter Lateness</a></li>
                                                        <?php }
                                                        if ($action_update_expenses_new && ($field_to_check == 0 || ($field_to_check > 0 && $action_can_force_update)) && $row['type'] != "Translation") { ?>
                                                            <li class="w3-border bg-success hidden"><a title="Update Expenses New" href="javascript:void(0)" onClick="popupwindow('<?php echo $exp_page_new; ?>?update_id=<?php echo $row['id']; ?>', 'Update expenses booking New', 1150, 620);"><i class="fa fa-star text-success"></i> Update Expenses (New)</a></li>
                                                            <?php }
                                                        if (($row['po_req'] == 1 && $row['porder'] != '') || ($row['po_req'] == 1 && $row['porder'] == '')) {
                                                            if ($action_purchase_order) { ?>
                                                                <li><a title="Update Purchase Order #" href="javascript:void(0)" onClick="popupwindow('purch_update.php?purch_id=<?php echo $row['id']; ?>&table=<?php echo strtolower($row['type']); ?>&orgName=<?php echo urlencode($row['orgName']); ?>&porder=<?php echo $row['porder']; ?>','Update purchase order booking',600,550)"><i class="fa fa-barcode"></i> Update Purchase Order #</a></li>
                                                                <?php }
                                                        }
                                                        if ($row['C_hoursWorkd'] > 0) {
                                                            if ($row['is_temp'] == 0) {
                                                                if ($row['hrsubmited'] != "Self" || ($row['hrsubmited'] == "Self" && $row['approved_flag'] == 1)) {
                                                                    if ($action_view_invoice) { ?>
                                                                        <li><a title="View Invoice" href="javascript:void(0)" onClick="popupwindow('<?php echo $inv_page; ?>?invoice_id=<?php echo $row['id']; ?>', 'View invoice booking', 1100,900);"><i class="fa fa-file-o"></i> View Invoice</a></li>
                                                                    <?php }
                                                                } else {
                                                                    echo "<li title='Please approve interpreter uploaded hours first!' class='bg-danger text-center'><a href='javascript:void(0)'>Hours not approved!</a></li>";
                                                                }
                                                            } else {
                                                                if ($action_confirm_temporary_expenses) { ?>
                                                                    <li><a style="background: #dbde1e;" title="Click to confirm" href="javascript:void(0)" onClick="popupwindow('confirm_record.php?id=<?php echo $row['id']; ?>&table=<?php echo strtolower($row['type']); ?>&type=1', 'Confirm record booking', 520,350);"><i class="fa fa-check-circle"></i> Confirm for invoice</a>
                                                                <?php }
                                                            }
                                                        } else {
                                                            echo "<h5><span class='label label-info'><i class='fa fa-exclamation-circle'></i> Company hours are not filled</span></h5>";
                                                        }
                                                        if ($action_view_credit_note && $row['credit_note']) { ?>
                                                                    <li><a href="javascript:void(0)" onClick="MM_openBrWindow('<?php echo $credit_page; ?>?invoice_id=<?php echo $row['id']; ?>','Credit note booking','scrollbars=yes,resizable=yes,width=900,height=800,left=450,top=10')"><i class="fa fa-exclamation-circle text-danger" title="Credit Note"></i> Credit Note</a></li>
                                                                <?php } ?>
                                                                <!--<li><a title="View Document Status" href="javascript:void(0)" onClick="popupwindow('file_status.php?id=<?php echo $row['id']; ?>', 'Document status booking', 1150, 620);"><i class="fa fa-eye"></i> View Doc Status</a></li>-->
                                                    </ul>
                                                </div>
                                                <?php if ($action_job_note) {
                                                    $arr_n = $acttObj->read_specific("(select count(id)", "jobnotes", "deleted_flag = 0 And tbl='" . strtolower($row['type']) . "' and fid=" . $row['id'] . " and (readcount is null or readcount=0)) as unread ,(select count(id) from jobnotes where deleted_flag = 0 And tbl='" . strtolower($row['type']) . "' and fid=" . $row['id'] . " and (readcount is not null and readcount!=0)) as yes_read"); ?>
                                                    <a href="javascript:void(0)" title="JOB NOTES" data-toggle="popover" data-trigger="hover" data-placement="top" style="text-decoration:none;" data-content="<?php echo '<b>' . $arr_n['unread'] . '</b> unread <b>' . $arr_n['yes_read'] . '</b> read job notes'; ?>" onclick="popupwindow('jobnote.php?fid=<?php echo $row['id']; ?>&table=<?php echo strtolower($row['type']); ?>&orgName=<?php echo urlencode($row['orgName']); ?>','Job notes booking',900,800)" <?php echo $arr_n['unread'] > 0 ? 'class="w3-button w3-small w3-circle w3-blue"' : 'class="w3-button w3-small w3-circle w3-grey"'; ?>><?php echo $arr_n['unread'] > 0 ? $arr_n['unread'] : $arr_n['yes_read']; ?></a>
                                                    <?php }
                                                if ($row['orderCancelatoin'] == 0) {
                                                    if ($action_cancel_job) { ?>
                                                        <a href="javascript:void(0)" onClick="popupwindow('cancel_order.php?job_id=<?php echo $row['id']; ?>&table=<?php echo strtolower($row['type']); ?>&orgName=<?php echo urlencode($row['orgName']); ?>&lang=<?php echo $row['source']; ?>','Cancel order booking',900, 600)"><input type="image" src="images/top_icon.png" title="Order Cancelation"></a>
                                                    <?php }
                                                } else {
                                                    if ($action_resume_job) { ?>
                                                        <a href="javascript:void(0)" title="Order Cancelled" data-toggle="popover" data-trigger="hover" data-placement="top" style="text-decoration:none;" data-content="Click to resmue this job" onClick="popupwindow('email_resume.php?email_id=<?php echo $row['id']; ?>&table=<?php echo strtolower($row['type']); ?>&orgName=<?php echo urlencode($row['orgName']); ?>','Resume order booking',620,450)"><input type="image" src="images/icn_alert_error.png"></a>
                                                    <?php }
                                                }
                                                if ($action_view_earnings) { ?>
                                                    <a href="javascript:void(0)" onClick="popupwindow('<?php echo $comp_earning; ?>?view_id=<?php echo $row['id']; ?>', 'Company Earnings booking', 800, 600);"><input type="image" src="images/earning.png" title="Earning"></a>
                                                <?php }
                                                if ($action_edited_history) { ?>
                                                    <a data-table-name="<?= strtolower($row['type']) ?>" data-record-id="<?= $row['id'] ?>" onclick="view_log_changes(this)" href="javascript:void(0)" title="View Log Edited History"><i class="fa fa-list text-danger"></i></a>
                                                <?php }
                                                if ($action_interpreter_uploaded_timesheet && $row['time_sheet']) { ?>
                                                    <a href="javascript:void(0)" onClick="popupwindow('timesheet_view.php?t_id=<?php echo $row['id']; ?>&table=<?php echo strtolower($row['type']); ?>','View timesheet booking',620,450)"><input type="image" src="images/images.jpg" title="View Time Sheet"></a>
                                                    <?php }
                                                if ($_SESSION['is_root'] == 1) {
                                                    $job_counter = $acttObj->read_specific('count(id) as counter', 'job_files', 'status=1 and tbl="' . strtolower($row['type']) . '" and file_type="timesheet" and order_id=' . $row['id']);
                                                    if ($job_counter['counter'] > 0) { ?>
                                                        <a href="javascript:void(0)" onClick="popupwindow('extra_file_view.php?order_id=<?php echo $row['id']; ?>&table=<?php echo strtolower($row['type']); ?>','View extra files booking',620,450)" title="View Extra Files"><i class="fa fa-plus fa-2x"></i></a>
                                                    <?php }
                                                }
                                                if ($action_view_applicants && $row['jobDisp'] == 1) { ?>
                                                    <a href="javascript:void(0)" onClick="MM_openBrWindow('../no_of_applicants.php?tracking=<?php echo $row['id']; ?>&table=<?php echo strtolower($row['type']); ?>','No of applicants booking','scrollbars=yes,resizable=yes,width=1200,height=900,left=200,top=10')">
                                                        <input type="image" src="images/aplcnts.png" title="View Applicants">
                                                    </a>
                                                    <?php }
                                                if ($action_request_client_feedback && $row['type'] != 'Translation') {
                                                    $feedback_counter = $acttObj->read_specific('count(*) as counter', 'feedback_requests', 'table_name="' . strtolower($row['type']) . '" and order_id=' . $row['id']);
                                                    $feedback_done = $acttObj->read_specific('count(*) as counter', 'interp_assess', 'table_name="' . strtolower($row['type']) . '" and order_id=' . $row['id']);
                                                    if ($feedback_counter['counter'] == 0 && $feedback_done['counter'] == 0) { ?>
                                                        <a style="color: #eb8a00;" class="pull-right" href="javascript:void(0)" onClick="popupwindow('request_client_feedback.php?order_id=<?php echo $row['id']; ?>&table=<?php echo strtolower($row['type']); ?>','Request For Feedback',620,450)" title="Request For Feedback"><i class="fa fa-star"></i></a>
                                                    <?php }
                                                }
                                                if ($row['type'] == 'Translation' && $action_view_client_translation_document) {
                                                    $dox_counter = $acttObj->read_specific('count(id) as file_counter', 'job_files', 'status=1 and tbl="' . strtolower($row['type']) . '" and file_type="c_portal" and order_id=' . $row['id']);
                                                    if ($dox_counter['file_counter'] > 0) { ?>
                                                        <a data-toggle="tooltip" data-placement="top" href="javascript:void(0)" onClick="popupwindow('trans_dox_view.php?order_id=<?php echo $row['id']; ?>&table=<?php echo strtolower($row['type']); ?>','View translation documents booking',620,450)" title="View Translation Document(s)"><i class="fa fa-file"></i></a>
                                                <?php }
                                                }
                                                if ($row['type'] == 'Interpreter') {
                                                    echo "<span class='label label-success lbl'>F2F</span>";
                                                } else if ($row['type'] == 'Telephone') {
                                                    echo "<span class='label label-info lbl'>TP</span>";
                                                } else {
                                                    echo "<span class='label label-warning lbl'>TR</span>";
                                                } ?>
                                            <?php } ?>
                                            <?php if ($row['type'] != 'Translation' && $action_view_text_messages) { ?>
                                                <br><button data-job-type="<?= $array_order_types[$row['type']] ?>" data-job-id="<?= $row['id'] ?>" data-interpreter-id="<?= $row['intrpName'] ?>" data-interpreter-name="<?= $row['name'] ?>" data-contact-no="<?= $row['contactNo'] ?>" data-country-name="<?= $row['country'] ?>" onclick="view_text_messages(this)" type="button" class="btn btn-xs btn-info">View Messages</button>
                                            <?php } ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php } ?>
                    </tbody>
                </table><br>
            </div>
        <?php } ?>
    </section>
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
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.0.3/js/bootstrap.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.13/css/bootstrap-multiselect.css" rel="stylesheet" type="text/css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.13/js/bootstrap-multiselect.js" type="text/javascript"></script>
    <script>
        $(function() {
            $('.searchable').multiselect({
                includeSelectAllOption: true,
                numberDisplayed: 1,
                enableFiltering: true,
                enableCaseInsensitiveFiltering: true
            });

            $('[data-toggle="popover"]').popover({
                html: true
            });
            $('[data-toggle="tooltip"]').tooltip();
        });

        function connect_the_call(element, job_id) {
            if (confirm("Are you sure you want to connect this call?")) {
                $.ajax({
                    url: 'ajax_add_interp_data.php',
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        connect_the_call: job_id
                    },
                    success: function(data) {
                        if (data['status'] == 1) {
                            $(element).parents(".div_connect_call").html(data['message']);
                        } else {
                            alert("Sorry! Failed to connect the call. Please try again");
                        }
                    },
                    error: function() {
                        console.log("Error fetching data!");
                    }
                });
            }
        }

        function check_the_job(element, job_id, job_type) {
            if (confirm("Did you checked this job details completely?")) {
                $.ajax({
                    url: 'ajax_add_interp_data.php',
                    method: 'post',
                    dataType: 'json',
                    data: {
                        job_id: job_id,
                        job_type: job_type,
                        screen: "booking",
                        check_the_job: 1
                    },
                    success: function(data) {
                        if (data['status'] == 1) {
                            $(element).replaceWith(data['message']);
                        }
                    },
                    error: function(data) {
                        console.log("Error code : " + data.status + " , Error message : " + data.statusText);
                    }
                });
            }
        }

        function view_text_messages(element) {
            var job_type = $(element).attr("data-job-type");
            var job_id = $(element).attr("data-job-id");
            var interpreter_id = $(element).attr("data-interpreter-id");
            var interpreter_name = $(element).attr("data-interpreter-name");
            var contact_no = $(element).attr("data-contact-no");
            var country = $(element).attr("data-country-name");
            if (job_type && job_id && interpreter_id) {
                $('.process_modal_attach').html("<center><i class='fa fa-circle fa-2x'></i> <i class='fa fa-circle fa-2x'></i> <i class='fa fa-circle fa-2x'></i><br><h3>Loading ...<br><br>Please Wait !!!</h3></center>");
                $('#process_modal').modal('show');
                $('body').removeClass('modal-open');
                $.ajax({
                    url: 'ajax_add_interp_data.php',
                    method: 'post',
                    dataType: 'json',
                    data: {
                        job_id: job_id,
                        job_type: job_type,
                        interpreter_id: interpreter_id,
                        interpreter_name: interpreter_name,
                        contact_no: contact_no,
                        country: country,
                        screen: "booking",
                        view_text_messages: 1
                    },
                    success: function(data) {
                        if (data['status'] == 1) {
                            $('.process_modal_attach').html(data['body']);
                        } else {
                            alert("Cannot load job response. Please try again!");
                        }
                    },
                    error: function(data) {
                        console.log("Error code : " + data.status + " , Error message : " + data.statusText);
                    }
                });
            } else {
                alert("Error: Please select valid job details or refresh the page! Thank you");
            }
        }

        function send_text_message(element) {
            if ($('#write_interpreter_phone').val() && $('#message_body').val()) {
                $(element).addClass("hidden");
                $.ajax({
                    url: 'process/third_party_apis.php',
                    method: 'post',
                    dataType: 'json',
                    data: {
                        order_id: $('#write_order_id').val(),
                        order_type: $('#write_order_type').val(),
                        interpreter_id: $('#write_interpreter_id').val(),
                        interpreter_phone: $('#write_interpreter_phone').val(),
                        interpreter_country: $('#write_interpreter_country').text(),
                        interpreter_email: "",
                        message_body: $('#message_body').val(),
                        send_text_message: 1
                    },
                    success: function(data) {
                        if (data['status'] == 1) {
                            alert(data['message']);
                            $('#process_modal').modal("hide");
                        } else {
                            alert(data['message']);
                            $(element).removeClass("hidden");
                        }
                    },
                    error: function(data) {
                        alert("Error code : " + data.status + " , Error message : " + data.statusText);
                    }
                });
            } else {
                if (!$('#write_interpreter_phone').val()) {
                    $('#write_interpreter_phone').focus();
                } else {
                    $('#message_body').focus();
                }
            }
        }

        function view_log_changes(element) {
            var table_name = $(element).attr("data-table-name");
            var table_name_array = {
                "interpreter": "Face To Face Booking",
                "telephone": "Telephone Booking",
                "translation": "Translation Booking"
            };
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
    <script>
        $(document).ready(function() {
            $('.view-connection').on('click', function() {
                var html = '<p style="word-wrap:break-word;"><strong>Client:</strong><br>' +
                    'Contact Person: ' + $(this).data('inch-person') + '<br>' +
                    'Contact Number: ' + $(this).data('noclient') + '<br>' +
                    'Email: ' + $(this).data('inch-email') + '<br>' +
                    'Language: ' + $(this).data('client-source') + ' to ' + $(this).data('client-target') + '<br>' +
                    'Start Time: ' + $(this).data('client-time') + '<br>' +
                    'Duration: ' + $(this).data('client-duration') + '<br>' +
                    '<hr>' +
                    '<strong>Service User: </strong>' + $(this).data('client-contact') + '<br>' +
                    '<hr>' +
                    '<strong>Interpreter:</strong><br>' +
                    'Contact No 1: ' + $(this).data('interp-contact1') + '<br>' +
                    'Contact No 2: ' + $(this).data('interp-contact2') + '<br>' +
                    'Email: ' + $(this).data('interp-email') + '</p>';

                $('#connectionInfoBody').html(html);
                $('#connectionInfoModal').modal('show');
            });
        });
    </script>

</body>

</html>