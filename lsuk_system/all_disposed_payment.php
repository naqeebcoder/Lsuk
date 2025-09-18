<?php
include "userhaspage.php";
SysPermiss::UserHasPage(__FILE__);
if (session_id() == '' || !isset($_SESSION)) {
    session_start();
}
include 'db.php';
include 'class.php';
//Access actions
$get_actions = explode(",", $acttObj->read_specific("GROUP_CONCAT(action_permissions.action_id) as actions", "action_permissions,route_actions", "action_permissions.action_id=route_actions.id AND route_actions.route_id=188 AND action_permissions.user_id=" . $_SESSION['userId'])['actions']);
$action_view_job = $_SESSION['is_root'] == 1 || in_array(173, $get_actions);
$action_edit_job = $_SESSION['is_root'] == 1 || in_array(174, $get_actions);
$action_update_expenses = $_SESSION['is_root'] == 1 || in_array(175, $get_actions);
$action_job_note = $_SESSION['is_root'] == 1 || in_array(176, $get_actions);
$action_receive_payment = $_SESSION['is_root'] == 1 || in_array(177, $get_actions);
$action_receive_partial_payment = $_SESSION['is_root'] == 1 || in_array(178, $get_actions);
$action_view_invoice = $_SESSION['is_root'] == 1 || in_array(179, $get_actions);
$action_make_credit_note = $_SESSION['is_root'] == 1 || in_array(180, $get_actions);
$action_uncommit_payment = $_SESSION['is_root'] == 1 || in_array(181, $get_actions);
$action_purchase_order = $_SESSION['is_root'] == 1 || in_array(182, $get_actions);
$action_view_earnings = $_SESSION['is_root'] == 1 || in_array(183, $get_actions);
$action_export_to_excel = $_SESSION['is_root'] == 1 || in_array(184, $get_actions);
include_once 'function.php';
$multi = @$_GET['multi'];
$assignDate = @$_GET['assignDate'];
$interp = @$_GET['interp'];
$org = @$_GET['org'];
$job = @$_GET['job'];
$inov = @$_GET['inov'];
$type = @$_GET['type'];
$po = @$_GET['po'];
$string = $_GET['str'];
$invoic_date = @$_GET['invoic_date'];
if (!empty($assignDate)) {
    $bg_aD = ' style="background: #ffff0075;"';
}
if (!empty($invoic_date)) {
    $bg_iD = ' style="background: #ffff0075;"';
}
$page = (int) (!isset($_GET["page"]) ? 1 : $_GET["page"]);
$limit = 20;
$startpoint = ($page * $limit) - $limit;
$page_count = $startpoint;

//check for disposed submit
if (isset($_POST['restore_disposed_submit'])) {
    $id = $_POST['id'];
    $table = $_POST['type'];
    $submitted_by = $_SESSION['UserName'];

    if (!empty($table) && $table == 'Interpreter') {
        $sql = "UPDATE interpreter SET disposed_of = 0 WHERE id = $id";
        mysqli_query($con, $sql);
        $sql = "DELETE FROM jobnotes WHERE fid = $id AND tbl = 'interpreter' AND (jobNote = 'Bad Debt' || jobNote = 'Not Chargeable')";
        mysqli_query($con, $sql);
    }
    if (!empty($table) && $table == 'Telephone') {
        $sql = "UPDATE telephone SET disposed_of = 0 WHERE id = $id";
        mysqli_query($con, $sql);
        $sql = "DELETE FROM jobnotes WHERE fid = $id AND tbl = 'telephone' AND (jobNote = 'Bad Debt' || jobNote = 'Not Chargeable')";
        mysqli_query($con, $sql);
    }
    if (!empty($table) && $table == 'Translation') {
        $sql = "UPDATE translation SET disposed_of = 0 WHERE id = $id";
        mysqli_query($con, $sql);
        $sql = "DELETE FROM jobnotes WHERE fid = $id AND tbl = 'translation' AND (jobNote = 'Bad Debt' || jobNote = 'Not Chargeable')";
        mysqli_query($con, $sql);
    }
}
?>
<!doctype html>
<html lang="en">

<head>
    <title>Disposed Of</title>
    <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" />
    <style>
        html,
        body {
            background: none !important;
        }

        .lbl {
            vertical-align: text-top;
        }

        .badge-counter {
            border-radius: 0px !important;
            margin: -9px -9px !important;
            font-size: 10px;
            float: left;
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

        @media screen and (max-width:425px) {
            #btn_export {
                margin: -11px -4px;
            }
        }

        .action_buttons .w3-button {
            padding: 7px 11px;
        }

        .action_buttons .w3-jb {
            padding: 3px 7px;
            margin-top: -6px;
        }

        .action_buttons .fa {
            font-size: 20px;
        }

        .w3-ul li {
            border-bottom: none !important;
        }
    </style>
</head>
<?php include 'header.php'; ?>

<body>
    <script>
        function myFunction() {
            var multi_flag = "";
            var multi = document.getElementById("multi");
            if (multi.checked == true) {
                multi_flag = "on";
            } else {
                multi_flag = "";
            }
            var o = document.getElementById("inov").value;
            if (!o) {
                o = "<?php echo $inov; ?>";
            }
            var w = document.getElementById("assignDate").value;
            if (!w) {
                w = "<?php echo $assignDate; ?>";
            }
            var invoic_date = document.getElementById("invoic_date").value;
            if (!invoic_date) {
                invoic_date = "<?php echo $invoic_date; ?>";
            }
            var x = document.getElementById("interp").value;
            if (!x) {
                x = "<?php echo $interp; ?>";
            }
            var y = document.getElementById("org").value;
            if (!y) {
                y = "<?php echo $org; ?>";
            }
            var z = document.getElementById("job").value;
            if (!z) {
                z = "<?php echo $job; ?>";
            }
            var type = document.getElementById("type").value;
            if (!type) {
                type = "<?php echo $type; ?>";
            }
            var po = document.getElementById("po").value;
            if (!po) {
                po = "<?php echo $po; ?>";
            }
            window.location.href = "<?php echo basename(__FILE__); ?>" + '?interp=' + x + '&org=' + y + '&job=' + z + '&assignDate=' + w + '&inov=' + o + '&type=' + type + '&po=' + po + '&invoic_date=' + invoic_date + '&multi=' + multi_flag;
        }

        function runtime_search() {
            var string = document.getElementById("search").value;
            if (!search) {
                search = "<?php echo $string; ?>";
            }
            window.location.href = "<?php echo basename(__FILE__); ?>" + '?str=' + string;
        }
    </script>
    <?php include 'nav2.php'; ?>
    <!-- end of sidebar -->
    <style>
        .tablesorter thead tr {
            background: none;
        }
    </style>
    <section class="container-fluid" style="overflow-x:auto">
        <div class="col-md-12">
            <header>
                <center>
                    <div style="margin-top: 15px;" class="form-group col-md-2 col-sm-4">
                        <input type="text" name="search" id="search" class="form-control" placeholder="Search ..." onChange="runtime_search()" value="<?php echo $string; ?>" />
                    </div><a href="<?php echo basename(__FILE__); ?>">
                        <h2 class="col-md-3 text-center"><span class="label label-primary">Disposed Of</span></h2>
                    </a>
                    <label class="col-md-2 pull-right" style="margin-top: 20px;"><input <?php if (isset($multi) && $multi == "on") {
                                                                                            echo 'checked';
                                                                                        } ?> type="checkbox" id="multi" onChange="myFunction()"> Multi invoices</label>
                </center>
                <div class="form-group col-md-2 col-sm-4 pull-right" style="margin-top: 15px;">
                    <select id="po" onChange="myFunction()" name="po" class="form-control">
                        <?php
                        $array_po = array('rs' => 'Required & Set', 'rm' => 'Required & Missing', 'nr' => 'Not Required');
                        if (!empty($po)) { ?>
                            <option value="<?php echo key($array_po[$po]); ?>" selected><?php echo $array_po[$po]; ?></option>
                        <?php } ?>
                        <option value="" disabled <?php if (empty($po)) {
                                                        echo 'selected';
                                                    } ?>>Filter by porder</option>
                        <option value="rs">Required & Set</option>
                        <option value="rm">Required & Missing</option>
                        <option value="nr">Not Required</option>
                    </select>
                </div>
                <div class="form-group col-md-2 col-sm-4 pull-right" style="margin-top: 15px;">
                    <select id="type" onChange="myFunction()" name="type" class="form-control">
                        <?php if (!empty($type)) { ?>
                            <option value="<?php echo $type; ?>" selected><?php echo $type; ?></option>
                        <?php } ?>
                        <option value="" disabled <?php if (empty($type)) {
                                                        echo 'selected';
                                                    } ?>>Filter Job Type</option>
                        <option value="Interpreter">Interpreter</option>
                        <option value="Telephone">Telephone</option>
                        <option value="Translation">Translation</option>
                    </select>
                </div>
                <div class="col-md-12"><br>
                    <div class="form-group col-md-2 col-sm-4">
                        <input type="text" name="inov" id="inov" class="form-control" placeholder="Invoice No" onChange="myFunction()" value="<?php echo $inov; ?>" />
                    </div>
                    <div class="form-group col-md-2 col-sm-4">

                        <?php
                        if (isset($multi) && $multi == "on") {
                            $append_multi_int = "and interpreter.multInv_flag=1";
                            $append_multi_tp = "and telephone.multInv_flag=1";
                            $append_multi_tr = "and translation.multInv_flag=1";
                            $append_multi_all = "and multInv_flag=1";
                        } else {
                            $append_multi_int = "and interpreter.multInv_flag=0";
                            $append_multi_tp = "and telephone.multInv_flag=0";
                            $append_multi_tr = "and translation.multInv_flag=0";
                            $append_multi_all = "and multInv_flag=0";
                        }
                        if (!empty($po) && $po == 'rs') {
                            $po_string_int = "and comp_reg.po_req=1 and interpreter.porder!=''";
                            $po_string_tp = "and comp_reg.po_req=1 and telephone.porder!=''";
                            $po_string_tr = "and comp_reg.po_req=1 and translation.porder!=''";
                        } else if (!empty($po) && $po == 'rm') {
                            $po_string_int = "and comp_reg.po_req=1 and (interpreter.porder='' OR interpreter.porder='Nil')";
                            $po_string_tp = "and comp_reg.po_req=1 and (telephone.porder='' OR telephone.porder='Nil')";
                            $po_string_tr = "and comp_reg.po_req=1 and (translation.porder='' OR translation.porder='Nil')";
                        } else if (!empty($po) && $po == 'nr') {
                            $po_string_int = "and comp_reg.po_req=0";
                            $po_string_tp = "and comp_reg.po_req=0";
                            $po_string_tr = "and comp_reg.po_req=0";
                        } else {
                            if (isset($string) && !empty($string)) {
                                $po_string_int = " ";
                                $po_string_tp = " ";
                                $po_string_tr = " ";
                            } else {
                                $po_string_int = "";//"and comp_reg.po_req=1 and interpreter.porder!=''";
                                $po_string_tp = "";//"and comp_reg.po_req=1 and telephone.porder!=''";
                                $po_string_tr = "";//"and comp_reg.po_req=1 and translation.porder!=''";
                            }
                        }
                        if (!empty($type) && $type == 'Interpreter') {
                            $sql_opt = "SELECT DISTINCT name,id,gender,city from (SELECT distinct interpreter_reg.name,interpreter_reg.id,interpreter_reg.gender, interpreter_reg.city,interpreter.multInv_flag,interpreter.commit,interpreter.total_charges_comp,interpreter.rAmount,interpreter.orgName,interpreter.deleted_flag,interpreter.order_cancel_flag,
interpreter.porder,comp_reg.po_req FROM interpreter_reg,interp_lang,interpreter,comp_reg WHERE interpreter_reg.code=interp_lang.code
AND interpreter.intrpName=interpreter_reg.id AND interpreter.orgName=comp_reg.abrv AND interpreter.deleted_flag = 0 AND interpreter.disposed_of = 1 and interpreter.order_cancel_flag=0 $append_multi_int and interpreter.commit=1 $po_string_int and (round(interpreter.rAmount,2) < round((interpreter.total_charges_comp+(interpreter.total_charges_comp*interpreter.cur_vat)),2) or interpreter.total_charges_comp =0) and interpreter.assignDate like '$assignDate%' and interpreter.invoic_date like '$invoic_date%' and (interpreter.orgName like '%$_words%')) as grp  ORDER BY name ASC";
                        } else if (!empty($type) && $type == 'Telephone') {
                            $sql_opt = "SELECT DISTINCT name,id,gender,city from (SELECT distinct interpreter_reg.name,interpreter_reg.id,interpreter_reg.gender, interpreter_reg.city,telephone.multInv_flag,telephone.commit,telephone.total_charges_comp,telephone.rAmount,telephone.orgName,telephone.deleted_flag,telephone.order_cancel_flag,
telephone.porder,comp_reg.po_req FROM interpreter_reg,interp_lang,telephone,comp_reg WHERE interpreter_reg.code=interp_lang.code
AND telephone.intrpName=interpreter_reg.id AND telephone.orgName=comp_reg.abrv AND telephone.deleted_flag = 0 AND telephone.disposed_of = 1 and telephone.order_cancel_flag=0 $append_multi_tp and telephone.commit=1 $po_string_tp and (round(telephone.rAmount,2) < round((telephone.total_charges_comp+(telephone.total_charges_comp*telephone.cur_vat)),2) or telephone.total_charges_comp =0) and telephone.assignDate like '$assignDate%' and telephone.invoic_date like '$invoic_date%' and (telephone.orgName like '%$_words%')) as grp  ORDER BY name ASC";
                        } else if (!empty($type) && $type == 'Translation') {
                            $sql_opt = "SELECT DISTINCT name,id,gender,city from (SELECT distinct interpreter_reg.name,interpreter_reg.id,interpreter_reg.gender, interpreter_reg.city,translation.multInv_flag,translation.commit,translation.total_charges_comp,translation.rAmount,translation.orgName,translation.deleted_flag,translation.order_cancel_flag,
translation.porder,comp_reg.po_req FROM interpreter_reg,interp_lang,translation,comp_reg WHERE interpreter_reg.code=interp_lang.code
AND translation.intrpName=interpreter_reg.id AND translation.orgName=comp_reg.abrv AND translation.deleted_flag = 0 AND translation.disposed_of = 1 and translation.order_cancel_flag=0 $append_multi_tr and translation.commit=1 $po_string_tr and (round(translation.rAmount,2) < round((translation.total_charges_comp+(translation.total_charges_comp*translation.cur_vat)),2) or translation.total_charges_comp =0) and translation.asignDate like '$assignDate%' and translation.invoic_date like '$invoic_date%' and (translation.orgName like '%$_words%')) as grp  ORDER BY name ASC";
                        } else {
                            $sql_opt = "SELECT DISTINCT name,id,gender,city from (SELECT distinct interpreter_reg.name,interpreter_reg.id,interpreter_reg.gender, interpreter_reg.city,interpreter.multInv_flag,interpreter.commit,interpreter.total_charges_comp,interpreter.rAmount,interpreter.orgName,interpreter.deleted_flag,interpreter.order_cancel_flag,
interpreter.porder,comp_reg.po_req FROM interpreter_reg,interp_lang,interpreter,comp_reg WHERE interpreter_reg.code=interp_lang.code
AND interpreter.intrpName=interpreter_reg.id AND interpreter.orgName=comp_reg.abrv AND interpreter.deleted_flag = 0 AND interpreter.disposed_of = 1 and interpreter.order_cancel_flag=0 $append_multi_int and interpreter.commit=1 $po_string_int and (round(interpreter.rAmount,2) < round((interpreter.total_charges_comp+(interpreter.total_charges_comp*interpreter.cur_vat)),2) or interpreter.total_charges_comp =0) and interpreter.assignDate like '$assignDate%' and interpreter.invoic_date like '$invoic_date%' and (interpreter.orgName like '%$_words%')
               UNION ALL SELECT distinct interpreter_reg.name,interpreter_reg.id,interpreter_reg.gender, interpreter_reg.city,telephone.multInv_flag,telephone.commit,telephone.total_charges_comp,telephone.rAmount,telephone.orgName,telephone.deleted_flag,telephone.order_cancel_flag,
telephone.porder,comp_reg.po_req FROM interpreter_reg,interp_lang,telephone,comp_reg WHERE interpreter_reg.code=interp_lang.code
AND telephone.intrpName=interpreter_reg.id AND telephone.orgName=comp_reg.abrv AND telephone.deleted_flag = 0 AND telephone.disposed_of = 1 and telephone.order_cancel_flag=0 $append_multi_tp and telephone.commit=1 $po_string_tp and (round(telephone.rAmount,2) < round((telephone.total_charges_comp+(telephone.total_charges_comp*telephone.cur_vat)),2) or telephone.total_charges_comp =0) and telephone.assignDate like '$assignDate%' and telephone.invoic_date like '$invoic_date%' and (telephone.orgName like '%$_words%')
               UNION ALL SELECT distinct interpreter_reg.name,interpreter_reg.id,interpreter_reg.gender, interpreter_reg.city,translation.multInv_flag,translation.commit,translation.total_charges_comp,translation.rAmount,translation.orgName,translation.deleted_flag,translation.order_cancel_flag,
translation.porder,comp_reg.po_req FROM interpreter_reg,interp_lang,translation,comp_reg WHERE interpreter_reg.code=interp_lang.code
AND translation.intrpName=interpreter_reg.id AND translation.orgName=comp_reg.abrv AND translation.deleted_flag = 0 AND translation.disposed_of = 1 and translation.order_cancel_flag=0 $append_multi_tr and translation.commit=1 $po_string_tr and (round(translation.rAmount,2) < round((translation.total_charges_comp+(translation.total_charges_comp*translation.cur_vat)),2) or translation.total_charges_comp =0) and translation.asignDate like '$assignDate%' and translation.invoic_date like '$invoic_date%' and (translation.orgName like '%$_words%')) as grp  ORDER BY name ASC";
                        
                        }
                        ?>
                        <select id="interp" onChange="myFunction()" name="interp" class="form-control searchable">
                            <?php
                            $result_opt = mysqli_query($con, $sql_opt);
                            $options_int = "";
                            while ($row_opt = mysqli_fetch_array($result_opt)) {
                                $code = $row_opt["name"];
                                $name_opt = $row_opt["name"];
                                $city_opt = $row_opt["city"];
                                $gender = $row_opt["gender"];
                                $options_int .= "<OPTION value='$code'>" . $name_opt . ' (' . $gender . ')' . ' (' . $city_opt . ')';
                            }
                            ?>
                            <?php if (!empty($interp)) { ?>
                                <option><?php echo $interp; ?></option>
                            <?php } else { ?>
                                <option value="">Select Interpreter</option>
                            <?php } ?>
                            <?php echo $options_int; ?>
                            </option>
                        </select>
                    </div>
                    <div class="form-group col-md-2 col-sm-4">
                        <?php
                        if (!empty($type) && $type == 'Interpreter') {
                            $sql_opt = "SELECT DISTINCT name,abrv from (SELECT DISTINCT comp_reg.name,comp_reg.abrv,comp_reg.po_req,interpreter.porder,interpreter.multInv_flag,interpreter.commit,interpreter.total_charges_comp,interpreter.rAmount,interpreter.orgName,interpreter.deleted_flag,interpreter.order_cancel_flag FROM comp_reg,interpreter WHERE interpreter.orgName=comp_reg.abrv AND interpreter.deleted_flag=0 AND interpreter.order_cancel_flag=0 $append_multi_int and interpreter.commit=1 and interpreter.assignDate like '$assignDate%' and interpreter.invoic_date like '$invoic_date%' $po_string_int and (round(interpreter.rAmount,2) < round((interpreter.total_charges_comp+(interpreter.total_charges_comp*interpreter.cur_vat)),2) or interpreter.total_charges_comp =0)) as grp 
                                ORDER BY name ASC";
                        } else if (!empty($type) && $type == 'Telephone') {
                            $sql_opt = "SELECT DISTINCT name,abrv from (SELECT DISTINCT comp_reg.name,comp_reg.abrv,comp_reg.po_req,telephone.porder,telephone.multInv_flag,telephone.commit,telephone.total_charges_comp,telephone.rAmount,telephone.orgName,telephone.deleted_flag,telephone.order_cancel_flag FROM comp_reg,telephone WHERE telephone.orgName=comp_reg.abrv AND telephone.deleted_flag=0 AND telephone.order_cancel_flag=0 $append_multi_tp and telephone.commit=1 and telephone.assignDate like '$assignDate%' and telephone.invoic_date like '$invoic_date%' $po_string_tp and (round(telephone.rAmount,2) < round((telephone.total_charges_comp+(telephone.total_charges_comp*telephone.cur_vat)),2) or telephone.total_charges_comp =0)) as grp 
                                ORDER BY name ASC";
                        } else if (!empty($type) && $type == 'Translation') {
                            $sql_opt = "SELECT DISTINCT name,abrv from (SELECT DISTINCT comp_reg.name,comp_reg.abrv,comp_reg.po_req,translation.porder,translation.multInv_flag,translation.commit,translation.total_charges_comp,translation.rAmount,translation.orgName,translation.deleted_flag,translation.order_cancel_flag FROM comp_reg,translation WHERE translation.orgName=comp_reg.abrv AND translation.deleted_flag=0 AND translation.order_cancel_flag=0 $append_multi_tr and translation.commit=1 and translation.asignDate like '$assignDate%' and translation.invoic_date like '$invoic_date%' $po_string_tr and (round(translation.rAmount,2) < round((translation.total_charges_comp+(translation.total_charges_comp*translation.cur_vat)),2) or translation.total_charges_comp =0)) as grp 
                                ORDER BY name ASC";
                        } else {
                            $sql_opt = "SELECT DISTINCT name,abrv from (SELECT DISTINCT comp_reg.name,comp_reg.abrv,comp_reg.po_req,interpreter.porder,interpreter.multInv_flag,interpreter.commit,interpreter.total_charges_comp,interpreter.rAmount,interpreter.orgName,interpreter.deleted_flag,interpreter.order_cancel_flag FROM comp_reg,interpreter WHERE interpreter.orgName=comp_reg.abrv AND interpreter.deleted_flag=0 AND interpreter.order_cancel_flag=0 $append_multi_int and interpreter.commit=1 and interpreter.assignDate like '$assignDate%' and interpreter.invoic_date like '$invoic_date%' $po_string_int and (round(interpreter.rAmount,2) < round((interpreter.total_charges_comp+(interpreter.total_charges_comp*interpreter.cur_vat)),2) or interpreter.total_charges_comp =0)
                                UNION SELECT DISTINCT comp_reg.name,comp_reg.abrv,comp_reg.po_req,telephone.porder,telephone.multInv_flag,telephone.commit,telephone.total_charges_comp,telephone.rAmount,telephone.orgName,telephone.deleted_flag,telephone.order_cancel_flag FROM comp_reg,telephone WHERE telephone.orgName=comp_reg.abrv AND telephone.deleted_flag=0 AND telephone.order_cancel_flag=0 $append_multi_tp and telephone.commit=1 and telephone.assignDate like '$assignDate%' and telephone.invoic_date like '$invoic_date%' $po_string_tp and (round(telephone.rAmount,2) < round((telephone.total_charges_comp+(telephone.total_charges_comp*telephone.cur_vat)),2) or telephone.total_charges_comp =0)
                                UNION SELECT DISTINCT comp_reg.name,comp_reg.abrv,comp_reg.po_req,translation.porder,translation.multInv_flag,translation.commit,translation.total_charges_comp,translation.rAmount,translation.orgName,translation.deleted_flag,translation.order_cancel_flag FROM comp_reg,translation WHERE translation.orgName=comp_reg.abrv AND translation.deleted_flag=0 AND translation.order_cancel_flag=0 $append_multi_tr and translation.commit=1 and translation.asignDate like '$assignDate%' and translation.invoic_date like '$invoic_date%' $po_string_tr and (round(translation.rAmount,2) < round((translation.total_charges_comp+(translation.total_charges_comp*translation.cur_vat)),2) or translation.total_charges_comp =0)) as grp 
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
                            $sql_opt = "SELECT distinct lang.lang,interpreter.total_charges_comp,interpreter.rAmount,interpreter.cur_vat FROM lang,interpreter WHERE interpreter.source=lang.lang and interpreter.assignDate LIKE '$assignDate%' and interpreter.invoic_date LIKE '$invoic_date%' $append_multi_all and commit=1 and deleted_flag=0 and disposed_of=1 and order_cancel_flag=0 and (round(rAmount,2) < round((total_charges_comp+(total_charges_comp*cur_vat)),2) or total_charges_comp =0) ORDER BY lang ASC";
                        } else if (!empty($type) && $type == 'Telephone') {
                            $sql_opt = "SELECT distinct lang.lang,telephone.total_charges_comp,telephone.rAmount,telephone.cur_vat FROM lang,telephone WHERE telephone.source=lang.lang and telephone.assignDate LIKE '$assignDate%' and telephone.invoic_date LIKE '$invoic_date%' $append_multi_all and commit=1 and deleted_flag=0 and disposed_of=1 and order_cancel_flag=0 and (round(rAmount,2) < round((total_charges_comp+(total_charges_comp*cur_vat)),2) or total_charges_comp =0) ORDER BY lang ASC";
                        } else if (!empty($type) && $type == 'Translation') {
                            $sql_opt = "SELECT distinct lang.lang,translation.total_charges_comp,translation.rAmount,translation.cur_vat FROM lang,translation WHERE translation.source=lang.lang and translation.asignDate LIKE '$assignDate%' and translation.invoic_date LIKE '$invoic_date%' $append_multi_all and commit=1 and deleted_flag=0 and disposed_of=1 and order_cancel_flag=0 and (round(rAmount,2) < round((total_charges_comp+(total_charges_comp*cur_vat)),2) or total_charges_comp =0) ORDER BY lang ASC";
                        } else {
                            $sql_opt = "SELECT DISTINCT lang from (SELECT distinct lang,interpreter.multInv_flag,interpreter.commit,interpreter.deleted_flag,interpreter.order_cancel_flag,interpreter.assignDate,interpreter.invoic_date,interpreter.total_charges_comp,interpreter.rAmount,interpreter.cur_vat FROM lang,interpreter WHERE interpreter.source=lang.lang
               UNION ALL SELECT distinct lang,telephone.multInv_flag,telephone.commit,telephone.deleted_flag,telephone.order_cancel_flag,telephone.assignDate,telephone.invoic_date,telephone.total_charges_comp,telephone.rAmount,telephone.cur_vat FROM lang,telephone WHERE telephone.source=lang.lang
               UNION ALL SELECT distinct lang,translation.multInv_flag,translation.commit,translation.deleted_flag,translation.order_cancel_flag,translation.asignDate as 'assignDate',translation.invoic_date,translation.total_charges_comp,translation.rAmount,translation.cur_vat FROM lang,translation WHERE translation.source=lang.lang) as grp 
WHERE commit=1 $append_multi_all and deleted_flag=0 and order_cancel_flag=0 and disposed_of=1 and (round(rAmount,2) < round((total_charges_comp+(total_charges_comp*cur_vat)),2) or total_charges_comp =0) and assignDate LIKE '$assignDate%' and invoic_date LIKE '$invoic_date%' ORDER BY lang ASC";
                        } ?>
                        <select name="job" id="job" onChange="myFunction()" class="form-control searchable">
                            <?php $result_opt = mysqli_query($con, $sql_opt);
                            $options = "";
                            while ($row_opt = mysqli_fetch_array($result_opt)) {
                                $code = $row_opt["lang"];
                                $name_opt = $row_opt["lang"];
                                $options .= "<OPTION value='$code'>" . $name_opt;
                            }
                            ?>
                            <?php if (!empty($job)) { ?>
                                <option><?php echo $job; ?></option>
                            <?php } else { ?>
                                <option value="">Language</option>
                            <?php } ?>
                            <?php echo $options; ?>
                            </option>
                        </select>
                    </div>
                    <div class="form-group col-md-2 col-sm-4">
                        <input placeholder="Assignment Date" type="text" onfocus="(this.type='date')" onblur="(this.type='text')" name="assignDate" id="assignDate" class="form-control" onChange="myFunction()" value="<?php echo $assignDate; ?>" />
                    </div>
                    <div class="form-group col-md-2 col-sm-4">
                        <input placeholder="Invoice Date" type="text" onfocus="(this.type='date')" onblur="(this.type='text')" name="invoic_date" id="invoic_date" class="form-control" onChange="myFunction()" value="<?php echo $invoic_date; ?>" />
                    </div>
            </header>
            <?php $arr = explode(',', $org);
            $_words = implode("' OR orgName like '", $arr);
            $arr_intrp = explode(',', $interp);
            $_words_intrp = implode("' OR name like '", $arr_intrp); ?>
            <?php $table = '';
            $counter = 0;
            if (!empty($type) && $type == 'Interpreter') {
                $query =
                    "SELECT * from (SELECT interpreter.porder,comp_reg.po_req,'Interpreter' as type,interpreter.id,interpreter.intrpName,interpreter.orgName,interpreter.inchEmail,interpreter_reg.name,interpreter.source,interpreter.target,interpreter.invoic_date,interpreter.assignDate,interpreter.assignTime,interpreter.orgContact,interpreter.submited, interpreter.aloct_by,interpreter.aloct_date,interpreter.dated,interpreter.hrsubmited,interpreter.comp_hrsubmited,interpreter.interp_hr_date, interpreter.comp_hr_date,interpreter.hoursWorkd,interpreter.C_hoursWorkd,interpreter.total_charges_comp,interpreter.cur_vat,interpreter.C_otherexpns, interpreter.credit_note,interpreter.C_admnchargs,interpreter.rAmount,interpreter.rDate,interpreter.sentemail,interpreter.printed,interpreter.printedby,interpreter.deleted_flag,interpreter.order_cancel_flag,interpreter.nameRef,interpreter.orgRef,interpreter.invoiceNo,interpreter.commit,comp_reg.email,comp_reg.abrv as comp_abrv FROM interpreter,interpreter_reg,comp_reg WHERE interpreter.intrpName=interpreter_reg.id AND interpreter.orgName=comp_reg.abrv $append_multi_int AND interpreter.deleted_flag = 0 AND interpreter.disposed_of = 1 and interpreter.order_cancel_flag=0 and interpreter.commit=1 $po_string_int and (round(interpreter.rAmount,2) < round((interpreter.total_charges_comp+(interpreter.total_charges_comp*interpreter.cur_vat)),2) or interpreter.total_charges_comp =0) and interpreter.invoic_date like '$invoic_date%' and interpreter.assignDate like '$assignDate%' and ((interpreter.source like '%$job%' OR interpreter.target like '%$job%') OR (interpreter.source like '%$job%' AND interpreter.target='English') OR (interpreter.source='English' AND interpreter.target like '%$job%')) and interpreter_reg.name like '%$interp%' and interpreter.orgName = '$org' and interpreter.invoiceNo like '%$inov%') as grp WHERE type like '%$type%' ORDER BY CONCAT(assignDate,' ',assignTime) LIMIT {$startpoint} , {$limit}";
            } else if (!empty($type) && $type == 'Telephone') {
                $query =
                    "SELECT * from (SELECT telephone.porder,comp_reg.po_req,'Telephone' as type,telephone.id,telephone.intrpName,telephone.orgName,telephone.inchEmail,interpreter_reg.name,telephone.source,telephone.target,telephone.invoic_date,telephone.assignDate,telephone.assignTime,telephone.orgContact,telephone.submited, telephone.aloct_by,telephone.aloct_date,telephone.dated,telephone.hrsubmited,telephone.comp_hrsubmited,telephone.interp_hr_date,telephone.comp_hr_date, telephone.hoursWorkd,telephone.C_hoursWorkd,telephone.total_charges_comp,telephone.cur_vat,0 as C_otherexpns,telephone.credit_note,telephone.C_admnchargs, telephone.rAmount,telephone.rDate,telephone.sentemail,telephone.printed,telephone.printedby,telephone.deleted_flag,telephone.order_cancel_flag,telephone.nameRef,telephone.orgRef,telephone.invoiceNo,telephone.commit,comp_reg.email,comp_reg.abrv as comp_abrv FROM telephone,interpreter_reg,comp_reg WHERE telephone.intrpName=interpreter_reg.id AND telephone.orgName=comp_reg.abrv $append_multi_tp AND telephone.deleted_flag = 0 AND telephone.disposed_of = 1 and telephone.order_cancel_flag=0 and telephone.commit=1 $po_string_tp and (round(telephone.rAmount,2) < round((telephone.total_charges_comp+(telephone.total_charges_comp*telephone.cur_vat)),2) or telephone.total_charges_comp =0) and telephone.invoic_date like '$invoic_date%' and telephone.assignDate like '$assignDate%' and ((telephone.source like '%$job%' OR telephone.target like '%$job%') OR (telephone.source like '%$job%' AND telephone.target='English') OR (telephone.source='English' AND telephone.target like '%$job%')) and interpreter_reg.name like '%$interp%' and telephone.orgName = '$org' and telephone.invoiceNo like '%$inov%') as grp WHERE type like '%$type%' ORDER BY CONCAT(assignDate,' ',assignTime) LIMIT {$startpoint} , {$limit}";
            } else if (!empty($type) && $type == 'Translation') {
                $query =
                    "SELECT * from (SELECT translation.porder,comp_reg.po_req,'Translation' as type,translation.id,translation.intrpName,translation.orgName,translation.inchEmail,interpreter_reg.name,translation.source,translation.target,translation.invoic_date,translation.asignDate as assignDate,'00:00:00' as assignTime,translation.orgContact,translation.submited, translation.aloct_by,translation.aloct_date,translation.dated,translation.hrsubmited,translation.comp_hrsubmited,translation.interp_hr_date,translation.comp_hr_date, translation.numberUnit as hoursWorkd,translation.C_numberUnit as C_hoursWorkd,translation.total_charges_comp,translation.cur_vat,0 as C_otherexpns,translation.credit_note,translation.C_admnchargs, translation.rAmount,translation.rDate,translation.sentemail,translation.printed,translation.printedby,translation.deleted_flag,translation.order_cancel_flag,translation.nameRef,translation.orgRef,translation.invoiceNo,translation.commit,comp_reg.email,comp_reg.abrv as comp_abrv FROM translation,interpreter_reg,comp_reg WHERE translation.intrpName=interpreter_reg.id AND translation.orgName=comp_reg.abrv $append_multi_tr AND translation.deleted_flag = 0 and translation.order_cancel_flag=0 and translation.order_cancel_flag=1 and translation.commit=1 $po_string_tr and (round(translation.rAmount,2) < round((translation.total_charges_comp+(translation.total_charges_comp*translation.cur_vat)),2) or translation.total_charges_comp =0) and translation.invoic_date like '$invoic_date%' and translation.asignDate like '$assignDate%' and ((translation.source like '%$job%' OR translation.target like '%$job%') OR (translation.source like '%$job%' AND translation.target='English') OR (translation.source='English' AND translation.target like '%$job%')) and interpreter_reg.name like '%$interp%' and translation.orgName = '$org' and translation.invoiceNo like '%$inov%') as grp WHERE type like '%$type%' ORDER BY CONCAT(assignDate,' ',assignTime) LIMIT {$startpoint} , {$limit}";
            } else {
                if (isset($string) && !empty($string)) {
                    $query =
                        "SELECT * from (SELECT interpreter.porder,comp_reg.po_req,'Interpreter' as type,interpreter.id,interpreter.intrpName,interpreter.orgName,interpreter.inchEmail,interpreter_reg.name,interpreter.source,interpreter.target,interpreter.invoic_date,interpreter.assignDate,interpreter.assignTime,interpreter.orgContact,interpreter.submited, interpreter.aloct_by,interpreter.aloct_date,interpreter.dated,interpreter.hrsubmited,interpreter.comp_hrsubmited,interpreter.interp_hr_date, interpreter.comp_hr_date,interpreter.hoursWorkd,interpreter.C_hoursWorkd,interpreter.total_charges_comp,interpreter.cur_vat,interpreter.C_otherexpns, interpreter.credit_note,interpreter.C_admnchargs,interpreter.rAmount,interpreter.rDate,interpreter.sentemail,interpreter.printed,interpreter.printedby,interpreter.deleted_flag,interpreter.order_cancel_flag,interpreter.nameRef,interpreter.orgRef,interpreter.invoiceNo,interpreter.commit,comp_reg.email,comp_reg.abrv as comp_abrv FROM interpreter,interpreter_reg,comp_reg WHERE interpreter.intrpName=interpreter_reg.id AND interpreter.orgName=comp_reg.abrv $append_multi_int AND interpreter.deleted_flag = 0 AND interpreter.disposed_of = 1 and interpreter.order_cancel_flag=0 and interpreter.commit=1 and (round(interpreter.rAmount,2) < round((interpreter.total_charges_comp+(interpreter.total_charges_comp*interpreter.cur_vat)),2) or interpreter.total_charges_comp =0) and (interpreter.orgRef like '%$string%' OR interpreter.porder like '%$string%' OR interpreter.nameRef like '%$string%' OR interpreter.invoiceNo like '%$string%' OR interpreter.id like '$string%' OR interpreter.reference_no like '$string%')
               UNION ALL SELECT telephone.porder,comp_reg.po_req,'Telephone' as type,telephone.id,telephone.intrpName,telephone.orgName,telephone.inchEmail,interpreter_reg.name,telephone.source,telephone.target,telephone.invoic_date,telephone.assignDate,telephone.assignTime,telephone.orgContact,telephone.submited, telephone.aloct_by,telephone.aloct_date,telephone.dated,telephone.hrsubmited,telephone.comp_hrsubmited,telephone.interp_hr_date,telephone.comp_hr_date, telephone.hoursWorkd,telephone.C_hoursWorkd,telephone.total_charges_comp,telephone.cur_vat,0 as C_otherexpns,telephone.credit_note,telephone.C_admnchargs, telephone.rAmount,telephone.rDate,telephone.sentemail,telephone.printed,telephone.printedby,telephone.deleted_flag,telephone.order_cancel_flag,telephone.nameRef,telephone.orgRef,telephone.invoiceNo,telephone.commit,comp_reg.email,comp_reg.abrv as comp_abrv FROM telephone,interpreter_reg,comp_reg WHERE telephone.intrpName=interpreter_reg.id AND telephone.orgName=comp_reg.abrv $append_multi_tp AND telephone.deleted_flag = 0 AND telephone.disposed_of = 1 and telephone.order_cancel_flag=0 and telephone.commit=1 and (round(telephone.rAmount,2) < round((telephone.total_charges_comp+(telephone.total_charges_comp*telephone.cur_vat)),2) or telephone.total_charges_comp =0) and (telephone.orgRef like '%$string%' OR telephone.porder like '%$string%' OR telephone.nameRef like '%$string%' OR telephone.invoiceNo like '%$string%' OR telephone.id like '$string%' OR telephone.reference_no like '$string%')
               UNION ALL SELECT translation.porder,comp_reg.po_req,'Translation' as type,translation.id,translation.intrpName,translation.orgName,translation.inchEmail,interpreter_reg.name,translation.source,translation.target,translation.invoic_date,translation.asignDate as assignDate,'00:00:00' as assignTime,translation.orgContact,translation.submited, translation.aloct_by,translation.aloct_date,translation.dated,translation.hrsubmited,translation.comp_hrsubmited,translation.interp_hr_date,translation.comp_hr_date, translation.numberUnit as hoursWorkd,translation.C_numberUnit as C_hoursWorkd,translation.total_charges_comp,translation.cur_vat,0 as C_otherexpns,translation.credit_note,translation.C_admnchargs, translation.rAmount,translation.rDate,translation.sentemail,translation.printed,translation.printedby,translation.deleted_flag,translation.order_cancel_flag,translation.nameRef,translation.orgRef,translation.invoiceNo,translation.commit,comp_reg.email,comp_reg.abrv as comp_abrv FROM translation,interpreter_reg,comp_reg WHERE translation.intrpName=interpreter_reg.id AND translation.orgName=comp_reg.abrv $append_multi_tr AND translation.deleted_flag = 0 AND translation.disposed_of = 1 and translation.order_cancel_flag=0 and translation.commit=1 and (round(translation.rAmount,2) < round((translation.total_charges_comp+(translation.total_charges_comp*translation.cur_vat)),2) or translation.total_charges_comp =0) and (translation.orgRef like '%$string%' OR translation.porder like '%$string%' OR translation.nameRef like '%$string%' OR translation.invoiceNo like '%$string%')) as grp ORDER BY CONCAT(assignDate,' ',assignTime) LIMIT {$startpoint} , {$limit}";
                } else {
                    $query =
                        "SELECT * from (SELECT interpreter.porder,comp_reg.po_req,'Interpreter' as type,interpreter.id,interpreter.intrpName,interpreter.orgName,interpreter.inchEmail,interpreter_reg.name,interpreter.source,interpreter.target,interpreter.invoic_date,interpreter.assignDate,interpreter.assignTime,interpreter.orgContact,interpreter.submited, interpreter.aloct_by,interpreter.aloct_date,interpreter.dated,interpreter.hrsubmited,interpreter.comp_hrsubmited,interpreter.interp_hr_date, interpreter.comp_hr_date,interpreter.hoursWorkd,interpreter.C_hoursWorkd,interpreter.total_charges_comp,interpreter.cur_vat,interpreter.C_otherexpns, interpreter.credit_note,interpreter.C_admnchargs,interpreter.rAmount,interpreter.rDate,interpreter.sentemail,interpreter.printed,interpreter.printedby,interpreter.deleted_flag,interpreter.order_cancel_flag,interpreter.nameRef,interpreter.orgRef,interpreter.invoiceNo,interpreter.commit,comp_reg.email,comp_reg.abrv as comp_abrv FROM interpreter,interpreter_reg,comp_reg WHERE interpreter.intrpName=interpreter_reg.id AND interpreter.orgName=comp_reg.abrv $append_multi_int AND interpreter.deleted_flag = 0 AND interpreter.disposed_of = 1 and interpreter.order_cancel_flag=0 and interpreter.commit=1 $po_string_int and (round(interpreter.rAmount,2) < round((interpreter.total_charges_comp+(interpreter.total_charges_comp*interpreter.cur_vat)),2) or interpreter.total_charges_comp =0) and interpreter.invoic_date like '$invoic_date%' and interpreter.assignDate like '$assignDate%' and ((interpreter.source like '%$job%' OR interpreter.target like '%$job%') OR (interpreter.source like '%$job%' AND interpreter.target='English') OR (interpreter.source='English' AND interpreter.target like '%$job%')) and interpreter_reg.name like '%$interp%' and interpreter.orgName like '%$org%' and interpreter.invoiceNo like '%$inov%'
               UNION ALL SELECT telephone.porder,comp_reg.po_req,'Telephone' as type,telephone.id,telephone.intrpName,telephone.orgName,telephone.inchEmail,interpreter_reg.name,telephone.source,telephone.target,telephone.invoic_date,telephone.assignDate,telephone.assignTime,telephone.orgContact,telephone.submited, telephone.aloct_by,telephone.aloct_date,telephone.dated,telephone.hrsubmited,telephone.comp_hrsubmited,telephone.interp_hr_date,telephone.comp_hr_date, telephone.hoursWorkd,telephone.C_hoursWorkd,telephone.total_charges_comp,telephone.cur_vat,0 as C_otherexpns,telephone.credit_note,telephone.C_admnchargs, telephone.rAmount,telephone.rDate,telephone.sentemail,telephone.printed,telephone.printedby,telephone.deleted_flag,telephone.order_cancel_flag,telephone.nameRef,telephone.orgRef,telephone.invoiceNo,telephone.commit,comp_reg.email,comp_reg.abrv as comp_abrv FROM telephone,interpreter_reg,comp_reg WHERE telephone.intrpName=interpreter_reg.id AND telephone.orgName=comp_reg.abrv $append_multi_tp AND telephone.deleted_flag = 0 AND telephone.disposed_of = 1 and telephone.order_cancel_flag=0 and telephone.commit=1 $po_string_tp and (round(telephone.rAmount,2) < round((telephone.total_charges_comp+(telephone.total_charges_comp*telephone.cur_vat)),2) or telephone.total_charges_comp =0) and telephone.invoic_date like '$invoic_date%' and telephone.assignDate like '$assignDate%' and ((telephone.source like '%$job%' OR telephone.target like '%$job%') OR (telephone.source like '%$job%' AND telephone.target='English') OR (telephone.source='English' AND telephone.target like '%$job%')) and interpreter_reg.name like '%$interp%' and telephone.orgName like '%$org%' and telephone.invoiceNo like '%$inov%'
               UNION ALL SELECT translation.porder,comp_reg.po_req,'Translation' as type,translation.id,translation.intrpName,translation.orgName,translation.inchEmail,interpreter_reg.name,translation.source,translation.target,translation.invoic_date,translation.asignDate as assignDate,'00:00:00' as assignTime,translation.orgContact,translation.submited, translation.aloct_by,translation.aloct_date,translation.dated,translation.hrsubmited,translation.comp_hrsubmited,translation.interp_hr_date,translation.comp_hr_date, translation.numberUnit as hoursWorkd,translation.C_numberUnit as C_hoursWorkd,translation.total_charges_comp,translation.cur_vat,0 as C_otherexpns,translation.credit_note,translation.C_admnchargs, translation.rAmount,translation.rDate,translation.sentemail,translation.printed,translation.printedby,translation.deleted_flag,translation.order_cancel_flag,translation.nameRef,translation.orgRef,translation.invoiceNo,translation.commit,comp_reg.email,comp_reg.abrv as comp_abrv FROM translation,interpreter_reg,comp_reg WHERE translation.intrpName=interpreter_reg.id AND translation.orgName=comp_reg.abrv $append_multi_tr AND translation.deleted_flag = 0 AND translation.disposed_of = 1 and translation.order_cancel_flag=0 and translation.commit=1 $po_string_tr and (round(translation.rAmount,2) < round((translation.total_charges_comp+(translation.total_charges_comp*translation.cur_vat)),2) or translation.total_charges_comp =0) and translation.invoic_date like '$invoic_date%' and translation.asignDate like '$assignDate%' and ((translation.source like '%$job%' OR translation.target like '%$job%') OR (translation.source like '%$job%' AND translation.target='English') OR (translation.source='English' AND translation.target like '%$job%')) and interpreter_reg.name like '%$interp%' and translation.orgName like '%$org%' and translation.invoiceNo like '%$inov%') as grp 
               
               WHERE type like '%$type%' ORDER BY CONCAT(assignDate,' ',assignTime) LIMIT {$startpoint} , {$limit}";
                }
            } ?>
            <div class="tab_container" id="put_data">
                <table class="tablesorter table table-bordered tbl_data" cellspacing="0" cellpadding="0">
                    <thead class="bg-info">
                        <tr>
                            <td align="center">
                                <?php if ($action_export_to_excel) {
                                    $export_link = isset($string) && !empty($string) ? 'reports_lsuk/excel/' . basename(__FILE__) . '?str=' . $string : 'reports_lsuk/excel/' . basename(__FILE__) . '?interp=' . $interp . '&org=' . $org . '&job=' . $job . '&assignDate=' . $assignDate . '&inov=' . $inov . '&type=' . $type . '&po=' . $po . '&invoic_date=' . $invoic_date; ?>
                                    <!--There is no URL created for EXPOR yet, so let's hide it for now-->
                                    <a id="btn_export" style="position: absolute;left: 21px;" href="<?= $export_link ?>" title="Download Excel Report"><span class="btn btn-xs btn-success hidden"><span class="hidden-sm hidden-xs">Export To </span>Excel <i class="glyphicon glyphicon-download"></i></span></a>
                                <?php } ?>
                                <div class=""><?php echo pagination($con, $table, $query, $limit, $page); ?><b class="text-right h4 pull-right"><?php if (!empty($type)) {
                                                                                                                                                    echo $type . ' pending jobs list';
                                                                                                                                                } else {
                                                                                                                                                    echo 'All pending jobs list';
                                                                                                                                                } ?></b></div>
                            </td>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // echo $query;exit;
                        $result = mysqli_query($con, $query);
                        if (mysqli_num_rows($result) == 0) {
                            echo '<tr>
            		  <td><h4 class="text-danger text-center">Sorry ! There are no records.</h4></td></tr>';
                        } else {
                            while ($row = mysqli_fetch_array($result)) {
                                $page_count++;
                                $counter++; ?>
                                <tr>
                                    <td><?php echo '<span class="w3-badge w3-blue badge-counter">' . $page_count . '</span>'; ?>
                                        <div class="col-sm-4 col-xs-12">
                                            <ul class="w3-ul">
                                                <li><span class="w3-large w3-right"><span class="label label-default"><?php echo $row['source'] . ' to ' . $row['target']; ?></span></span><?php echo '<span class="label label-default w3-large w3-right">' . $row['hoursWorkd'] == 0 ? '<span class="text-danger"><b>' . $row['name'] . '</b></span>' : '<span><b>' . $row['name'] . '</b></span>'; ?></li>
                                                <li><i class="fa fa-question-circle" title="Allocated By"></i><span class="w3-right"><?php echo ucwords($row['aloct_by']) . ' (' . $misc->dated($row['aloct_date']) . ')'; ?></span></li>
                                                <li><i class="fa fa-question-circle" title="Interpreter Hours Submission"></i><span class="w3-right"><?php echo $row['hrsubmited'] . ' (' . $misc->dated($row['interp_hr_date']) . ')'; ?></span></li>
                                                <li><i class="fa fa-question-circle" title="Company Hours Submission"></i><span class="w3-right"><?php echo $row['comp_hrsubmited'] . ' (' . $misc->dated($row['comp_hr_date']) . ')'; ?></span></li>
                                                <li><i class="fa fa-question-circle" title="Client Reference Name"></i><span class="w3-right"><?php echo $row['orgRef']; ?></span></li>
                                            </ul>
                                        </div>
                                        <div class="col-sm-4 col-xs-12">
                                            <ul class="w3-ul">
                                                <li><?php echo '<span class="label label-default w3-large w3-right">' . $row['C_hoursWorkd'] == 0 ? '<span class="label w3-large  w3-red">' . $row['orgName'] . '</span><span class="w3-large w3-right text-right" style="font-weight:bold;margin-top:-10px;font-size:14px!important;"><span ' . $bg_aD . '>' . $row['assignDate'] . '</span><br> ' . $row['assignTime'] . '</span>' : '<span class="label w3-large  w3-blue">' . $row['orgName'] . '</span><span class="w3-large w3-right text-right" style="font-weight:bold;margin-top:-10px;font-size:14px;"><span ' . $bg_aD . '>' . $row['assignDate'] . '</span> ' . $row['assignTime'] . '</span>'; ?></li>
                                                <li><i class="fa fa-question-circle" title="Contact Name"></i><span class="w3-right"><?php echo $row['orgContact']; ?></span></li>
                                                <li><i class="fa fa-question-circle" title="Entered By"></i><span class="w3-right"><?php echo $row['submited'] . ' (' . $misc->dated($row['dated']) . ')'; ?></span></li>
                                                <li>Printed By <span class="w3-right"><?php echo ucwords($row['printedby']) ?: 'Nil'; ?></span></li>
                                                <li>Invoice Date <span class="w3-right"><?php echo '<span ' . $bg_iD . '>' . $row['invoic_date'] . '</span>'; ?></span></li>
                                            </ul>
                                        </div>
                                        <div class="col-sm-4 col-xs-12">
                                            <ul class="w3-ul">
                                            </ul>
                                        </div>

                                        <?php
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

                                        /*$totinvnow2=$row['total_charges_comp']* $row["cur_vat"] + $row['total_charges_comp'] + $row['C_otherexpns'] + $row['C_admnchargs'];*/
                                        ?>
                                        <div class="col-sm-4 col-xs-12">
                                            <ul class="w3-ul">
                                                <li><i class="fa fa-question-circle" title="Emailed By"></i><span class="w3-right"><?php echo $row['inchEmail']; ?></span></li>
                                                <li>Invoice Amount<span class="w3-large w3-right"><?php echo $misc->numberFormat_fun($totinvnow); ?></span></li>
                                                <li>Received Amount<span class="w3-large w3-right"><?php echo $row['rAmount'] != 0 ? $misc->numberFormat_fun($row['rAmount']) : 0; ?></span></li>
                                                <li>Dated<span class="w3-right"><?php echo $misc->dated($row['rDate']); ?></span></li>
                                                <li>Purch.Order#<span class="w3-right"><?php if ($row['po_req'] == 1 && $row['porder'] != '') {
                                                                                            echo $row['porder'];
                                                                                        } else if ($row['po_req'] == 1 && $row['porder'] == '') {
                                                                                            echo '<span class="text-danger"><b>Missing!</b></span>';
                                                                                        } else {
                                                                                            echo '<span class="text-info">Not required!</span>';
                                                                                        } ?></span></li>
                                            </ul>
                                        </div>
                                        <div class="col-sm-12 text-center action_buttons">
                                            <?php if ($_SESSION['userId'] == 27) { ?>
                                                <button type="button" class="btn btn-primary" id='res_disposed_btn' onclick="restore_disposed(<?php echo $row['id']; ?> , '<?php echo $row['type'] ?>')">
                                                    Restore
                                                </button>
                                            <?php } ?>
                                            <?php if ($row['type'] == 'Interpreter') {
                                                $edit_page = 'interp_edit.php';
                                                $exp_page = 'expenses_f2f.php';
                                                $inv_page = 'invoice.php';
                                                $comp_earning = 'comp_earning.php';
                                                $credit_page = 'credit_interp.php';
                                            } else if ($row['type'] == 'Telephone') {
                                                $edit_page = 'telep_edit.php';
                                                $exp_page = 'expenses_tp.php';
                                                $inv_page = 'telep_invoice.php';
                                                $comp_earning = 'comp_earning_telep.php';
                                                $credit_page = 'credit_telep.php';
                                            } else {
                                                $edit_page = 'trans_edit.php';
                                                $exp_page = 'expenses_tr.php';
                                                $inv_page = 'trans_invoice.php';
                                                $comp_earning = 'comp_earning_trans.php';
                                                $credit_page = 'credit_trans.php';
                                            }
                                            if ($action_view_job) { ?>
                                                <a href="javascript:void(0)" onClick="popupwindow('order_view.php?view_id=<?php echo $row['id']; ?>&table=<?php echo strtolower($row['type']); ?>', 'View Order', 1000, 1000);"><input type="image" src="images/icn_new_article.png" title="View Order"></a>
                                            <?php }
                                            if ($action_edit_job) { ?>
                                                <a href="javascript:void(0)" onClick="popupwindow('<?php echo $edit_page; ?>?edit_id=<?php echo $row['id']; ?>', 'Edit Order', 1250, 730);"><i class="fa fa-edit text-primary" title="Edit job"></i></a>
                                            <?php }
                                            if ($action_update_expenses) { ?>
                                                <a href="javascript:void(0)" onClick="popupwindow('<?php echo $exp_page; ?>?update_id=<?php echo $row['id']; ?>', 'Update Expenses', 1150, 620);"><i class="fa fa-refresh text-primary" title="Update Expenses"></i></a>
                                            <?php }
                                            if ($action_job_note) {
                                                $arr_n = $acttObj->read_specific("(select count(id)", "jobnotes", "tbl='" . strtolower($row['type']) . "' and fid=" . $row['id'] . " and (readcount is null or readcount=0)) as unread ,(select count(id) from jobnotes where tbl='" . strtolower($row['type']) . "' and fid=" . $row['id'] . " and (readcount is not null and readcount!=0)) as yes_read"); ?>
                                                <a href="javascript:void(0)" title="JOB NOTES" data-toggle="popover" data-trigger="hover" data-placement="top" style="text-decoration:none;" data-content="<?php echo '<b>' . $arr_n['unread'] . '</b> unread <b>' . $arr_n['yes_read'] . '</b> read job notes'; ?>" onclick="popupwindow('jobnote.php?fid=<?php echo $row['id']; ?>&table=<?php echo strtolower($row['type']); ?>&orgName=<?php echo $row['orgName']; ?>','Job Notes',900,800)" <?php echo $arr_n['unread'] > 0 ? 'class="w3-button w3-jb w3-small w3-circle w3-blue"' : 'class="w3-button w3-jb w3-small w3-circle w3-grey"'; ?>><?php echo $arr_n['unread'] > 0 ? $arr_n['unread'] : $arr_n['yes_read']; ?></a>
                                            <?php }
                                            if ($action_receive_payment) { ?>
                                                <a href="javascript:void(0)" onClick="popupwindow('receive_amount.php?row_id=<?php echo $row['id']; ?>&table=<?php echo strtolower($row['type']) ?>','Update Payment', 800,450);"><input type="image" src="images/Cash.png" title="Payment Received"></a>
                                            <?php }
                                            if ($action_receive_partial_payment) { ?>
                                                <a href="javascript:void(0)" onClick="popupwindow('receive_part.php?row_id=<?php echo $row['id']; ?>&table=<?php echo strtolower($row['type']) ?>','Partial Payment', 800,450);"><i class="fa fa-money" title="Receive Partial Payment"></i></a>
                                            <?php }
                                            if ($action_view_invoice) { ?>
                                                <a href="javascript:void(0)" onClick="popupwindow('<?php echo $inv_page; ?>?invoice_id=<?php echo $row['id']; ?>','View Invoice', 1000, 1000);"><input type="image" src="images/invoice.png" title="Invoice"></a>
                                            <?php }
                                            if ($action_make_credit_note) { ?>
                                                <a href="javascript:void(0)" onClick="popupwindow('<?php echo $credit_page; ?>?invoice_id=<?php echo $row['id']; ?>','Credit Note', 1000, 1000);"><i class="fa fa-exclamation-circle <?= !empty($row['credit_note']) ? 'text-danger' : '' ?>" title="Credit Note"></i></a>
                                            <?php }
                                            if ($action_uncommit_payment) { ?>
                                                <a href="javascript:void(0)" onClick="popupwindow('un_commit.php?com_id=<?php echo $row['id']; ?>&table=<?php echo strtolower($row['type']); ?>','Rollback Payment', 550,350);"><input type="image" src="images/icn_jump_back.png" title="Un-commit payment"></a>
                                            <?php }
                                            if ($action_purchase_order) { ?>
                                                <a href="javascript:void(0)" onClick="popupwindow('purch_update.php?purch_id=<?php echo $row['id']; ?>&table=<?php echo strtolower($row['type']); ?>&orgName=<?php echo $row['orgName']; ?>','Update Purchase order', 550,450);"><input type="image" src="images/icn_tags.png" title="Update Purchase Order #"></a>
                                            <?php }
                                            if ($action_view_earnings) { ?>
                                                <a href="javascript:void(0)" onClick="popupwindow('<?php echo $comp_earning; ?>?view_id=<?php echo $row['id']; ?>','View Earnings', 900, 400);"><input type="image" src="images/earning.png" title="Earning"></a>
                                            <?php }
                                            if ($row['type'] == 'Interpreter') {
                                                echo "<span class='label label-success lbl'>" . $row['type'] . "</span>";
                                            } else if ($row['type'] == 'Telephone') {
                                                echo "<span class='label label-info lbl'>" . $row['type'] . "</span>";
                                            } else {
                                                echo "<span class='label label-warning lbl'>" . $row['type'] . "</span>";
                                            } ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php } ?>
                    </tbody>
                </table>
            </div>
        <?php } ?>
        <style>
            #ajax_loader {
                visibility: hidden;
                background-color: #f8f8f8;
                position: absolute;
                width: auto;
                height: auto;
                left: 18%;
                top: 0%;
                overflow: hidden;
            }

            #ajax_loader img {
                position: relative;
                left: 2%;
            }
        </style>
        <div id="ajax_loader">
            <img src="../images/ajax_loader.gif" width="70" class="img-responsive" />
        </div>
    </section>

    <!-- Bootsrap modal for Disposed Of -->

    <!-- Modal -->
    <div class="modal fade" id="disposed_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Disposed Of</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="#">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="disposed_reason" id="disposed_reason" value="Bad Debt" checked>
                            <label class="form-check-label" for="bad_debt">
                                Bad Debt
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="disposed_reason" id="disposed_reason" value="Not Chargeable">
                            <label class="form-check-label" for="not_chargeable">
                                Not Chargeable
                            </label>
                            <input type="hidden" id="disposed_id" />
                            <input type="hidden" id="disposed_type" />
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" onClick="save_restore_disposed_of()" class="btn btn-primary">Save changes</button>
                </div>
            </div>
        </div>
    </div>

    <!-- End of Bootsrap modal for Disposed Of -->
    <script src="js/jquery-1.11.3.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.0.3/js/bootstrap.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.13/css/bootstrap-multiselect.css" rel="stylesheet" type="text/css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.13/js/bootstrap-multiselect.js" type="text/javascript"></script>
    <script>
        function restore_disposed(id, type) {
            $("#disposed_id").val(id);
            $("#disposed_type").val(type);
            $("#res_disposed_btn").prop('disabled', true);
            $.ajax({
                type: "POST",
                url: "all_disposed_payment.php",
                data: {
                    restore_disposed_submit: 1,
                    id,
                    type
                },
                cache: false,
                success: function(data) {
                    location.reload();
                }
            });
        }

        function save_restore_disposed_of() {
            let id = $("#disposed_id").val();
            let type = $("#disposed_type").val();
            let disposed_reason = $("#disposed_reason").val();
            $.ajax({
                type: "POST",
                url: "all_disposed_payment.php",
                data: {
                    disposed_submit: 1,
                    id,
                    type,
                    disposed_reason
                },
                cache: false,
                success: function(data) {
                    location.reload();
                }
            });
        }
        $(function() {
            $('.searchable').multiselect({
                includeSelectAllOption: true,
                numberDisplayed: 1,
                enableFiltering: true,
                enableCaseInsensitiveFiltering: true
            });
        });
        // function ajax_search(elem){
        //     var string=$(elem).val();
        //     if(string.length != 0){
        //         $.ajax({
        //         url : 'runtime_search.php',
        //         type: "POST",
        //         data: {string:string},
        //           beforeSend: function(){
        //             $('#ajax_loader').css("visibility", "visible");
        //           },
        //         success:function(strData, textStatus, jqXHR){
        //           if(strData){
        // 			$('#put_data').html(strData);
        //           }else{
        //             alert("No result found!");
        //           }
        //         },
        //       complete: function(){
        //         $('#ajax_loader').css("visibility", "hidden");
        //       },
        //         error: function(jqXHR, textStatus, errorThrown){
        //           alert("DoCountNotes()- Something wrong with Jquery");
        //         }
        //       });
        //     }
        // }
    </script>
</body>

</html>