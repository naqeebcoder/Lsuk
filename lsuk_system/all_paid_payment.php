<?php
include "userhaspage.php";
SysPermiss::UserHasPage(__FILE__);
if (session_id() == '' || !isset($_SESSION)) {
    session_start();
}
include 'db.php';
include 'class.php';
//Access actions
$get_actions = explode(",", $acttObj->read_specific("GROUP_CONCAT(action_permissions.action_id) as actions", "action_permissions,route_actions", "action_permissions.action_id=route_actions.id AND route_actions.route_id=133 AND action_permissions.user_id=" . $_SESSION['userId'])['actions']);
$action_view_job = $_SESSION['is_root'] == 1 || in_array(82, $get_actions);
$action_edit_job = $_SESSION['is_root'] == 1 || in_array(83, $get_actions);
$action_update_expenses = $_SESSION['is_root'] == 1 || in_array(84, $get_actions);
$action_job_note = $_SESSION['is_root'] == 1 || in_array(85, $get_actions);
$action_receive_payment = $_SESSION['is_root'] == 1 || in_array(86, $get_actions);
$action_receive_partial_payment = $_SESSION['is_root'] == 1 || in_array(87, $get_actions);
$action_view_invoice = $_SESSION['is_root'] == 1 || in_array(88, $get_actions);
$action_edited_history = $_SESSION['is_root'] == 1 || in_array(127, $get_actions);
$action_make_credit_note = $_SESSION['is_root'] == 1 || in_array(89, $get_actions);
$action_uncommit_payment = $_SESSION['is_root'] == 1 || in_array(90, $get_actions);
$action_purchase_order = $_SESSION['is_root'] == 1 || in_array(91, $get_actions);
$action_view_earnings = $_SESSION['is_root'] == 1 || in_array(92, $get_actions);
$action_export_to_excel = $_SESSION['is_root'] == 1 || in_array(93, $get_actions);
include_once 'function.php';
$page = (int) (!isset($_GET["page"]) ? 1 : $_GET["page"]);
$limit = 20;
$startpoint = ($page * $limit) - $limit;
$page_count = $startpoint;
$multi = @$_GET['multi'];
$assignDate = @$_GET['assignDate'];
$interp = @$_GET['interp'];
$org = @$_GET['org'];
$p_org = '';
$job = @$_GET['job'];
$rDate = @$_GET['rD'];
$inov = @$_GET['inov'];
$type = @$_GET['type'];
$po = @$_GET['po'];
$string = $_GET['str'];
$invoic_date = @$_GET['invoic_date'];

$org_arr = array();
$all_awp = array();
$all_awp_comp = array();
$comp_cz = array();

if (isset($_GET['p_org'])) {
    $p_org = $_GET['p_org'];
    $p_org_q = $acttObj->read_specific("GROUP_CONCAT(child_comp) as ch_ids", "subsidiaries", " parent_comp=$p_org")['ch_ids'] ?: '0';
    $p_org_ad = ($p_org_q != 0 ? " and comp_reg.id IN ($p_org_q) " : "");
} else {
    $p_org_ad = $p_org;
}


$semi = "\"'\"";
if (isset($org) && $org != "") {
    $org_arr = explode(",", $org);
    if (in_array(380, $org_arr)) {
        $data1 = $acttObj->read_specific(
            "DISTINCT GROUP_CONCAT(parent_companies.sup_child_comp) as data1",
            "parent_companies",
            "parent_companies.sup_parent_comp IN (380)"
        );
        $all_awp = $acttObj->query_extra(
            "DISTINCT GROUP_CONCAT($semi,(SELECT comp_reg.abrv from comp_reg WHERE id=child_companies.child_comp),$semi) as all_cz",
            "child_companies",
            "child_companies.parent_comp IN (" . $data1["data1"] . ")",
            "set SESSION group_concat_max_len=10000"
        );
    }
    $comp_cz = $acttObj->query_extra(
        "DISTINCT GROUP_CONCAT($semi,comp_reg.abrv,$semi) as all_cz",
        "comp_reg",
        "comp_reg.id IN ($org)",
        "set SESSION group_concat_max_len=10000"
    );
    if ($all_awp['all_cz'] != '') {
        $all_awp_comp = explode(',', $all_awp['all_cz']);
        $all_cz['all_cz'] = $comp_cz['all_cz'] . ',' . $all_awp['all_cz'];
    } else {
        $all_cz['all_cz'] = $comp_cz['all_cz'];
    }
}

if (!empty($assignDate)) {
    $bg_aD = ' style="background: #ffff0075;"';
}
if (!empty($invoic_date)) {
    $bg_iD = ' style="background: #ffff0075;"';
}
if (!empty($rDate)) {
    $bg_pD = ' style="background: #ffff0075;"';
}
$append_type = $type ? " AND type like '%$type%'" : "";
$append_assignDate_all = $assignDate ? " and assignDate LIKE '$assignDate%' " : "";
$append_assignDate_f2f = $assignDate ? " and interpreter.assignDate like '$assignDate%' " : "";
$append_assignDate_tp = $assignDate ? " and telephone.assignDate like '$assignDate%' " : "";
$append_assignDate_tr = $assignDate ? " and translation.asignDate like '$assignDate%' " : "";
$append_invoice_date_all = $invoic_date ? " and invoic_date LIKE '$invoic_date%' " : "";
$append_invoice_date_f2f = $invoic_date ? " and interpreter.invoic_date like '$invoic_date%' " : "";
$append_invoice_date_tp = $invoic_date ? " and telephone.invoic_date like '$invoic_date%' " : "";
$append_invoice_date_tr = $invoic_date ? " and translation.invoic_date like '$invoic_date%' " : "";
$append_lang_f2f = $job ? " and ((interpreter.source='$job' OR interpreter.target='$job') OR (interpreter.source='$job' AND interpreter.target='English') OR (interpreter.source='English' AND interpreter.target='$job')) " : "";
$append_lang_tp = $job ? " and ((telephone.source='$job' OR telephone.target='$job') OR (telephone.source='$job' AND telephone.target='English') OR (telephone.source='English' AND telephone.target='$job')) " : "";
$append_lang_tr = $job ? " and ((translation.source='$job' OR translation.target='$job') OR (translation.source='$job' AND translation.target='English') OR (translation.source='English' AND translation.target='$job')) " : "";
$append_interp = $interp ? " and interpreter_reg.name like '%$interp%' " : "";
$append_orgName_f2f = $org ? " and interpreter.orgName IN (" . $all_cz['all_cz'] . ") " : "";
$append_orgName_tp = $org ? " and telephone.orgName IN (" . $all_cz['all_cz'] . ") " : "";
$append_orgName_tr = $org ? " and translation.orgName IN (" . $all_cz['all_cz'] . ") " : "";
$append_rDate_f2f = $rDate ? " and interpreter.rDate like '%$rDate%' " : "";
$append_rDate_tp = $rDate ? " and telephone.rDate like '%$rDate%' " : "";
$append_rDate_tr = $rDate ? " and translation.rDate like '%$rDate%' " : "";
$append_invoiceNo_f2f = $inov ? " and interpreter.invoiceNo like '%$inov%' " : "";
$append_invoiceNo_tp = $inov ? " and telephone.invoiceNo like '%$inov%' " : "";
$append_invoiceNo_tr = $inov ? " and translation.invoiceNo like '%$inov%' " : "";
$append_multi_int = isset($multi) && $multi == "on" ? " and interpreter.multInv_flag=1 " : " and interpreter.multInv_flag=0 ";
$append_multi_tp = isset($multi) && $multi == "on" ? " and telephone.multInv_flag=1 " : " and telephone.multInv_flag=0 ";
$append_multi_tr = isset($multi) && $multi == "on" ? " and translation.multInv_flag=1 " : " and translation.multInv_flag=0 ";
$append_multi_all = isset($multi) && $multi == "on" ? " and multInv_flag=1 " : " and multInv_flag=0 ";

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
    // if(isset($string) && !empty($string)){
    //     $po_string_int=" ";
    //     $po_string_tp=" ";
    //     $po_string_tr=" ";
    // }else{
    //     $po_string_int="and comp_reg.po_req=1 and interpreter.porder!=''";
    //     $po_string_tp="and comp_reg.po_req=1 and telephone.porder!=''";
    //     $po_string_tr="and comp_reg.po_req=1 and translation.porder!=''";
    // }
    $po_string_int = " ";
    $po_string_tp = " ";
    $po_string_tr = " ";
}
?>
<!doctype html>
<html lang="en">

<head>
    <title>PAID INVOICES LIST</title>
    <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" />
    <style>
        .modal { overflow-y: auto !important; }
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

        /* .dropdown-menu button.dropdown-item{
    background: none !important;
    border: none !important;
    text-align: left !important;
    width: 100% !important;}
    .multiselect-option{
        background: none !important;
    border: none !important;
    text-align: left !important;
    width: 100% !important;
    } */
    </style>
</head>
<?php include 'header.php'; ?>

<body>
    <script>
        function myFunction() {
            var append_url = "<?php echo basename(__FILE__) . "?1"; ?>";
            if ($("#multi").is(':checked')) {
                append_url += '&multi_flag=on';
            }
            var inov = $("#inov").val();
            if (inov) {
                append_url += '&inov=' + inov;
            }
            var rD = $("#rDate").val();
            if (rD) {
                append_url += '&rD=' + rD;
            }
            var assignDate = $("#assignDate").val();
            if (assignDate) {
                append_url += '&assignDate=' + assignDate;
            }
            var invoic_date = $("#invoic_date").val();
            if (invoic_date) {
                append_url += '&invoic_date=' + invoic_date;
            }
            var interp = $("#interp").val();
            if (interp) {
                append_url += '&interp=' + interp;
            }
            var p_org = $("#p_org").val();
            if (p_org) {
                append_url += '&p_org=' + p_org;
            }
            var org = $("#org").val();
            if (org) {
                append_url += '&org=' + org;
            }
            var job = $("#job").val();
            if (job) {
                append_url += '&job=' + job;
            }
            var type = $("#type").val();
            if (type) {
                append_url += '&type=' + type;
            }
            var po = $("#po").val();
            if (po) {
                append_url += '&po=' + po;
            }
            window.location.href = append_url;
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
    <section class="container-fluid" style="overflow-x:auto">
        <div class="col-md-12">
            <header>
                <div class="form-group col-md-2 col-sm-4">
                    <input style="margin-top: 15px;" type="text" name="search" id="search" class="form-control" placeholder="Search ..." onChange="runtime_search()" value="<?php echo $string; ?>" />
                </div>
                <center><a href="<?php echo basename(__FILE__); ?>">
                        <h2 class="col-md-3 text-center"><span class="label label-success">PAID INVOICES LIST</span></h2>
                    </a>
                    <label class="col-md-2 pull-right" style="margin-top: 20px;"><input <?php if (isset($multi) && $multi == "on") {
                                                                                            echo 'checked';
                                                                                        } ?> type="checkbox" id="multi"> Multi invoices</label>
                </center>
                <div class="form-group col-md-2 col-sm-4 pull-right" style="margin-top: 15px;">
                    <select id="po" name="po" class="form-control">
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
                    <select id="type" name="type" class="form-control">
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
                        <input type="text" name="inov" id="inov" class="form-control" placeholder="Invoice No" value="<?php echo $inov; ?>" />
                    </div>
                    <div class="form-group col-md-2 col-sm-4">
                        <?php
                        $sql_opt = "SELECT DISTINCT id,name,abrv from comp_reg WHERE comp_nature=1 AND deleted_flag=0 ORDER BY name ASC"; ?>
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
                        if (isset($type) && !empty($type)) {
                            if ($type == 'Interpreter') {
                                $sql_opt = "SELECT DISTINCT name,id,gender,city from (SELECT distinct interpreter_reg.name,interpreter_reg.id,interpreter_reg.gender, interpreter_reg.city,interpreter.multInv_flag,interpreter.commit,interpreter.total_charges_comp,interpreter.rAmount,interpreter.orgName,interpreter.deleted_flag,interpreter.order_cancel_flag,
        interpreter.porder,comp_reg.po_req FROM interpreter_reg,interpreter,comp_reg 
        WHERE interpreter.intrpName=interpreter_reg.id AND interpreter.orgName=comp_reg.abrv AND interpreter.deleted_flag = 0 and interpreter.order_cancel_flag=0 $append_multi_int and interpreter.commit=1 $po_string_int and (round(interpreter.rAmount,2) >= round((interpreter.total_charges_comp+(interpreter.total_charges_comp*interpreter.cur_vat)),2) and interpreter.rAmount>0) " . $append_assignDate_f2f . $append_invoice_date_f2f . " and (interpreter.orgName like '%$_words%')) as grp  ORDER BY name ASC";
                            } else if ($type == 'Telephone') {
                                $sql_opt = "SELECT DISTINCT name,id,gender,city from (SELECT distinct interpreter_reg.name,interpreter_reg.id,interpreter_reg.gender, interpreter_reg.city,telephone.multInv_flag,telephone.commit,telephone.total_charges_comp,telephone.rAmount,telephone.orgName,telephone.deleted_flag,telephone.order_cancel_flag,
        telephone.porder,comp_reg.po_req FROM interpreter_reg,telephone,comp_reg 
        WHERE telephone.intrpName=interpreter_reg.id AND telephone.orgName=comp_reg.abrv AND telephone.deleted_flag = 0 and telephone.order_cancel_flag=0 $append_multi_tp and telephone.commit=1 $po_string_tp and (round(telephone.rAmount,2) >= round((telephone.total_charges_comp+(telephone.total_charges_comp*telephone.cur_vat)),2) and telephone.rAmount>0) " . $append_assignDate_tp . $append_invoice_date_tp . " and (telephone.orgName like '%$_words%')) as grp  ORDER BY name ASC";
                            } else {
                                $sql_opt = "SELECT DISTINCT name,id,gender,city from (SELECT distinct interpreter_reg.name,interpreter_reg.id,interpreter_reg.gender, interpreter_reg.city,translation.multInv_flag,translation.commit,translation.total_charges_comp,translation.rAmount,translation.orgName,translation.deleted_flag,translation.order_cancel_flag,
        translation.porder,comp_reg.po_req FROM interpreter_reg,translation,comp_reg 
        WHERE translation.intrpName=interpreter_reg.id AND translation.orgName=comp_reg.abrv AND translation.deleted_flag = 0 and translation.order_cancel_flag=0 $append_multi_tr and translation.commit=1 $po_string_tr and (round(translation.rAmount,2) >= round((translation.total_charges_comp+(translation.total_charges_comp*translation.cur_vat)),2) and translation.rAmount>0) " . $append_assignDate_tr . $append_invoice_date_tr . " and (translation.orgName like '%$_words%')) as grp  ORDER BY name ASC";
                            }
                        } else {
                            $sql_opt = "SELECT DISTINCT name,id,gender,city from (SELECT distinct interpreter_reg.name,interpreter_reg.id,interpreter_reg.gender, interpreter_reg.city,interpreter.multInv_flag,interpreter.commit,interpreter.total_charges_comp,interpreter.rAmount,interpreter.orgName,interpreter.deleted_flag,interpreter.order_cancel_flag,
    interpreter.porder,comp_reg.po_req FROM interpreter_reg,interpreter,comp_reg 
    WHERE interpreter.intrpName=interpreter_reg.id AND interpreter.orgName=comp_reg.abrv AND interpreter.deleted_flag = 0 and interpreter.order_cancel_flag=0 $append_multi_int and interpreter.commit=1 $po_string_int and (round(interpreter.rAmount,2) >= round((interpreter.total_charges_comp+(interpreter.total_charges_comp*interpreter.cur_vat)+interpreter.c_otherexpns),2) and interpreter.rAmount>0) " . $append_assignDate_f2f . $append_invoice_date_f2f . " and (interpreter.orgName like '%$_words%')
    UNION SELECT distinct interpreter_reg.name,interpreter_reg.id,interpreter_reg.gender, interpreter_reg.city,telephone.multInv_flag,telephone.commit,telephone.total_charges_comp,telephone.rAmount,telephone.orgName,telephone.deleted_flag,telephone.order_cancel_flag,
    telephone.porder,comp_reg.po_req FROM interpreter_reg,telephone,comp_reg 
    WHERE 
    telephone.intrpName=interpreter_reg.id AND telephone.orgName=comp_reg.abrv AND telephone.deleted_flag = 0 and telephone.order_cancel_flag=0 $append_multi_tp and telephone.commit=1 $po_string_tp and (round(telephone.rAmount,2) >= round((telephone.total_charges_comp+(telephone.total_charges_comp*telephone.cur_vat)),2) and telephone.rAmount>0) " . $append_assignDate_tp . $append_invoice_date_tp . " and (telephone.orgName like '%$_words%')
    UNION SELECT distinct interpreter_reg.name,interpreter_reg.id,interpreter_reg.gender, interpreter_reg.city,translation.multInv_flag,translation.commit,translation.total_charges_comp,translation.rAmount,translation.orgName,translation.deleted_flag,translation.order_cancel_flag,
    translation.porder,comp_reg.po_req FROM interpreter_reg,translation,comp_reg 
    WHERE translation.intrpName=interpreter_reg.id AND translation.orgName=comp_reg.abrv AND translation.deleted_flag = 0 and translation.order_cancel_flag=0 $append_multi_tr and translation.commit=1 $po_string_tr and (round(translation.rAmount,2) >= round((translation.total_charges_comp+(translation.total_charges_comp*translation.cur_vat)),2) and translation.rAmount>0) " . $append_assignDate_tr . $append_invoice_date_tr . " and (translation.orgName like '%$_words%')) as grp  ORDER BY name ASC";
                        }
                        ?>
                        <select id="interp" name="interp" class="form-control searchable">
                            <?php
                            $result_opt = mysqli_query($con, $sql_opt);
                            $options_int = "";
                            while ($row_opt = mysqli_fetch_array($result_opt)) {
                                $code = $row_opt["name"];
                                $name_opt = $row_opt["name"];
                                $city_opt = $row_opt["city"];
                                $gender = $row_opt["gender"];
                                $options_int .= "<option value='$code'>" . $name_opt . ' (' . $gender . ')' . ' (' . $city_opt . ')</option>';
                            } ?>
                            <?php if (!empty($interp)) { ?>
                                <option><?php echo $interp; ?></option>
                            <?php } else { ?>
                                <option value="">Select Interpreter</option>
                            <?php } ?>
                            <?php echo $options_int; ?>
                        </select>
                    </div>
                    <div class="form-group col-md-2 col-sm-4">
                        <?php
                        if (!empty($type) && $type == 'Interpreter') {
                            $sql_opt = "SELECT DISTINCT id,name,abrv from (SELECT DISTINCT comp_reg.id,comp_reg.name,comp_reg.abrv,comp_reg.po_req,interpreter.porder,interpreter.multInv_flag,interpreter.commit,interpreter.total_charges_comp,interpreter.rAmount,interpreter.orgName,interpreter.deleted_flag,interpreter.order_cancel_flag FROM comp_reg,interpreter WHERE interpreter.orgName=comp_reg.abrv AND interpreter.deleted_flag=0 AND interpreter.order_cancel_flag=0 $append_multi_int and interpreter.commit=1 " . $append_assignDate_f2f . $append_invoice_date_f2f . " $po_string_int and (round(interpreter.rAmount,2) >= round((interpreter.total_charges_comp+(interpreter.total_charges_comp*interpreter.cur_vat)),2) and interpreter.rAmount>0)) as grp 
                                ORDER BY name ASC";
                        } else if (!empty($type) && $type == 'Telephone') {
                            $sql_opt = "SELECT DISTINCT id,name,abrv from (SELECT DISTINCT comp_reg.id,comp_reg.name,comp_reg.abrv,comp_reg.po_req,telephone.porder,telephone.multInv_flag,telephone.commit,telephone.total_charges_comp,telephone.rAmount,telephone.orgName,telephone.deleted_flag,telephone.order_cancel_flag FROM comp_reg,telephone WHERE telephone.orgName=comp_reg.abrv AND telephone.deleted_flag=0 AND telephone.order_cancel_flag=0 $append_multi_tp and telephone.commit=1 " . $append_assignDate_tp . $append_invoice_date_tp . " $po_string_tp and (round(telephone.rAmount,2) >= round((telephone.total_charges_comp+(telephone.total_charges_comp*telephone.cur_vat)),2) and telephone.rAmount>0)) as grp 
                                ORDER BY name ASC";
                        } else if (!empty($type) && $type == 'Translation') {
                            $sql_opt = "SELECT DISTINCT id,name,abrv from (SELECT DISTINCT comp_reg.id,comp_reg.name,comp_reg.abrv,comp_reg.po_req,translation.porder,translation.multInv_flag,translation.commit,translation.total_charges_comp,translation.rAmount,translation.orgName,translation.deleted_flag,translation.order_cancel_flag FROM comp_reg,translation WHERE translation.orgName=comp_reg.abrv AND translation.deleted_flag=0 AND translation.order_cancel_flag=0 $append_multi_tr and translation.commit=1 " . $append_assignDate_tr . $append_invoice_date_tr . " $po_string_tr and (round(translation.rAmount,2) >= round((translation.total_charges_comp+(translation.total_charges_comp*translation.cur_vat)),2) and translation.rAmount>0)) as grp 
                                ORDER BY name ASC";
                        } else {
                            $sql_opt = "SELECT DISTINCT id,name,abrv from (SELECT DISTINCT comp_reg.id,comp_reg.name,comp_reg.abrv,comp_reg.po_req,interpreter.porder,interpreter.multInv_flag,interpreter.commit,interpreter.total_charges_comp,interpreter.rAmount,interpreter.orgName,interpreter.deleted_flag,interpreter.order_cancel_flag FROM comp_reg,interpreter WHERE interpreter.orgName=comp_reg.abrv AND interpreter.deleted_flag=0 AND interpreter.order_cancel_flag=0 $append_multi_int and interpreter.commit=1 " . $append_assignDate_f2f . $append_invoice_date_f2f . " $po_string_int and (round(interpreter.rAmount,2) >= round((interpreter.total_charges_comp+(interpreter.total_charges_comp*interpreter.cur_vat)),2) and interpreter.rAmount>0)
                                UNION SELECT DISTINCT comp_reg.id,comp_reg.name,comp_reg.abrv,comp_reg.po_req,telephone.porder,telephone.multInv_flag,telephone.commit,telephone.total_charges_comp,telephone.rAmount,telephone.orgName,telephone.deleted_flag,telephone.order_cancel_flag FROM comp_reg,telephone WHERE telephone.orgName=comp_reg.abrv AND telephone.deleted_flag=0 AND telephone.order_cancel_flag=0 $append_multi_tp and telephone.commit=1 " . $append_assignDate_tp . $append_invoice_date_tp . " $po_string_tp and (round(telephone.rAmount,2) >= round((telephone.total_charges_comp+(telephone.total_charges_comp*telephone.cur_vat)),2) and telephone.rAmount>0)
                                UNION SELECT DISTINCT comp_reg.id,comp_reg.name,comp_reg.abrv,comp_reg.po_req,translation.porder,translation.multInv_flag,translation.commit,translation.total_charges_comp,translation.rAmount,translation.orgName,translation.deleted_flag,translation.order_cancel_flag FROM comp_reg,translation WHERE translation.orgName=comp_reg.abrv AND translation.deleted_flag=0 AND translation.order_cancel_flag=0 $append_multi_tr and translation.commit=1 " . $append_assignDate_tr . $append_invoice_date_tr . " $po_string_tr and (round(translation.rAmount,2) >= round((translation.total_charges_comp+(translation.total_charges_comp*translation.cur_vat)),2) and translation.rAmount>0)) as grp 
                                ORDER BY name ASC";
                        };
                        ?>
                        <select id="org" name="org" multiple class="form-control searchable">
                            <?php $result_opt = mysqli_query($con, $sql_opt);
                            $options = "";
                            $count_comp = 0;
                            // $options .= "<option value='380'>AWP (All Delivery Unit) (AWP) <option>";
                            if (in_array(380, $org_arr)) {
                                $options .= "<option value='380' selected >AWP (All Delivery Unit) (AWP)";
                            } else {
                                $options .= "<option value='380'>AWP (All Delivery Unit) (AWP) ";
                            }
                            while ($row_opt = mysqli_fetch_array($result_opt)) {
                                $code = $row_opt["id"];
                                $abrv = $row_opt["abrv"];
                                $name_opt = $row_opt["name"];
                                if ($code == 380) {
                                    continue;
                                }
                                if (in_array($code, $org_arr) || in_array("'$abrv'", $all_awp_comp)) {
                                    $options .= "<OPTION value='$code' selected>" . $name_opt . ' (' . $abrv . ')';
                                } else {
                                    $options .= "<OPTION value='$code'>" . $name_opt . ' (' . $abrv . ')';
                                }
                            }
                            ?>
                            <option value="">Select Company</option>
                            <?php echo $options; ?>
                            </option>
                        </select>
                    </div>
                    <div class="form-group col-md-2 col-sm-4">
                        <?php if (!empty($type) && $type == 'Interpreter') {
                            $sql_opt = "SELECT distinct lang.lang,interpreter.total_charges_comp,interpreter.rAmount,interpreter.cur_vat FROM lang,interpreter WHERE interpreter.source=lang.lang " . $append_assignDate_f2f . $append_invoice_date_f2f . " $append_multi_all and commit=1 and deleted_flag=0 and order_cancel_flag=0 and (round(rAmount,2) >= round((total_charges_comp+(total_charges_comp*cur_vat)),2) and rAmount>0) ORDER BY lang ASC";
                        } else if (!empty($type) && $type == 'Telephone') {
                            $sql_opt = "SELECT distinct lang.lang,telephone.total_charges_comp,telephone.rAmount,telephone.cur_vat FROM lang,telephone WHERE telephone.source=lang.lang " . $append_assignDate_tp . $append_invoice_date_tp . " $append_multi_all and commit=1 and deleted_flag=0 and order_cancel_flag=0 and (round(rAmount,2) >= round((total_charges_comp+(total_charges_comp*cur_vat)),2) and rAmount>0) ORDER BY lang ASC";
                        } else if (!empty($type) && $type == 'Translation') {
                            $sql_opt = "SELECT distinct lang.lang,translation.total_charges_comp,translation.rAmount,translation.cur_vat FROM lang,translation WHERE translation.source=lang.lang " . $append_assignDate_tr . $append_invoice_date_tr . " $append_multi_all and commit=1 and deleted_flag=0 and order_cancel_flag=0 and (round(rAmount,2) >= round((total_charges_comp+(total_charges_comp*cur_vat)),2) and rAmount>0) ORDER BY lang ASC";
                        } else {
                            $sql_opt = "SELECT DISTINCT lang from (SELECT distinct lang,interpreter.multInv_flag,interpreter.commit,interpreter.deleted_flag,interpreter.order_cancel_flag,interpreter.assignDate,interpreter.invoic_date,interpreter.total_charges_comp,interpreter.rAmount,interpreter.cur_vat FROM lang,interpreter WHERE interpreter.source=lang.lang 
                            UNION ALL SELECT distinct lang,telephone.multInv_flag,telephone.commit,telephone.deleted_flag,telephone.order_cancel_flag,telephone.assignDate,telephone.invoic_date,telephone.total_charges_comp,telephone.rAmount,telephone.cur_vat FROM lang,telephone WHERE telephone.source=lang.lang 
                            UNION ALL SELECT distinct lang,translation.multInv_flag,translation.commit,translation.deleted_flag,translation.order_cancel_flag,translation.asignDate as 'assignDate',translation.invoic_date,translation.total_charges_comp,translation.rAmount,translation.cur_vat FROM lang,translation WHERE translation.source=lang.lang) as grp 
                            WHERE commit=1 $append_multi_all and deleted_flag=0 and order_cancel_flag=0 and (round(rAmount,2) >= round((total_charges_comp+(total_charges_comp*cur_vat)),2) and rAmount>0) " . $append_assignDate_all . $append_invoice_date_all . " ORDER BY lang ASC";
                        } ?>
                        <select name="job" id="job" class="form-control searchable">
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
                        <input placeholder="Assignment Date" type="text" onfocus="(this.type='date')" onblur="(this.type='text')" name="assignDate" id="assignDate" class="form-control" value="<?php echo $assignDate; ?>" />
                    </div>
                    <div class="form-group col-md-2 col-sm-4">
                        <input placeholder="Invoice Date" type="text" onfocus="(this.type='date')" onblur="(this.type='text')" name="invoic_date" id="invoic_date" class="form-control" value="<?php echo $invoic_date; ?>" />
                    </div>
                    <div class="form-group col-md-2 col-sm-4">
                        <input placeholder="Paid Date" type="text" onfocus="(this.type='date')" onblur="(this.type='text')" name="rDate" id="rDate" class="form-control" value="<?php echo $rDate; ?>" />
                    </div>
                    <div class="form-group col-md-12 text-right">
                        <a href="javascript:void(0)" title="Click to Get Result" onclick="myFunction()"><span class="btn btn-sm btn-primary">Get Result</span></a>
                    </div>
            </header>
            <?php $arr = explode(',', $org);
            $_words = implode("' OR orgName like '", $arr);
            $arr_intrp = explode(',', $interp);
            $_words_intrp = implode("' OR name like '", $arr_intrp); ?>
            <?php $table = '';
            $counter = 0;
            if (isset($string) && !empty($string)) {
                $query = "SELECT * from (SELECT interpreter.porder,comp_reg.po_req,'Interpreter' as type,interpreter.id,interpreter.intrpName,interpreter.orgName,interpreter.inchEmail,interpreter_reg.name,interpreter.source,interpreter.target,interpreter.invoic_date,interpreter.assignDate,interpreter.assignTime,interpreter.orgContact,interpreter.submited, interpreter.aloct_by,interpreter.aloct_date,interpreter.dated,interpreter.hrsubmited,interpreter.comp_hrsubmited,interpreter.interp_hr_date, interpreter.comp_hr_date,interpreter.hoursWorkd,interpreter.C_hoursWorkd,interpreter.total_charges_comp,interpreter.cur_vat,interpreter.C_otherexpns, interpreter.credit_note,interpreter.C_admnchargs,interpreter.rAmount,interpreter.rDate,interpreter.sentemail,interpreter.printed,interpreter.printedby,interpreter.deleted_flag,interpreter.order_cancel_flag,interpreter.nameRef,interpreter.orgRef,interpreter.invoiceNo,interpreter.commit,interpreter.new_comp_id,comp_reg.email,comp_reg.abrv as comp_abrv FROM interpreter,interpreter_reg,comp_reg WHERE interpreter.intrpName=interpreter_reg.id AND interpreter.orgName=comp_reg.abrv $append_multi_int AND interpreter.deleted_flag = 0 and interpreter.order_cancel_flag=0 and interpreter.commit=1 $po_string_int and (round(interpreter.rAmount,2) >= round((interpreter.total_charges_comp+(interpreter.total_charges_comp*interpreter.cur_vat)+interpreter.c_otherexpns),2) and interpreter.rAmount>0) and (interpreter.orgRef like '%$string%' OR interpreter.porder like '%$string%' OR interpreter.nameRef like '%$string%' OR interpreter.invoiceNo like '%$string%' OR interpreter.id like '$string%' OR interpreter.reference_no like '$string%')
                UNION ALL SELECT telephone.porder,comp_reg.po_req,'Telephone' as type,telephone.id,telephone.intrpName,telephone.orgName,telephone.inchEmail,interpreter_reg.name,telephone.source,telephone.target,telephone.invoic_date,telephone.assignDate,telephone.assignTime,telephone.orgContact,telephone.submited, telephone.aloct_by,telephone.aloct_date,telephone.dated,telephone.hrsubmited,telephone.comp_hrsubmited,telephone.interp_hr_date,telephone.comp_hr_date, telephone.hoursWorkd,telephone.C_hoursWorkd,telephone.total_charges_comp,telephone.cur_vat,0 as C_otherexpns,telephone.credit_note,telephone.C_admnchargs, telephone.rAmount,telephone.rDate,telephone.sentemail,telephone.printed,telephone.printedby,telephone.deleted_flag,telephone.order_cancel_flag,telephone.nameRef,telephone.orgRef,telephone.invoiceNo,telephone.commit,telephone.new_comp_id,comp_reg.email,comp_reg.abrv as comp_abrv FROM telephone,interpreter_reg,comp_reg WHERE telephone.intrpName=interpreter_reg.id AND telephone.orgName=comp_reg.abrv $append_multi_tp AND telephone.deleted_flag = 0 and telephone.order_cancel_flag=0 and telephone.commit=1 $po_string_tp and (round(telephone.rAmount,2) >= round((telephone.total_charges_comp+(telephone.total_charges_comp*telephone.cur_vat)),2) and telephone.rAmount>0) and (telephone.orgRef like '%$string%' OR telephone.porder like '%$string%' OR telephone.nameRef like '%$string%' OR telephone.invoiceNo like '%$string%' OR telephone.id like '$string%' OR telephone.reference_no like '$string%')
                UNION ALL SELECT translation.porder,comp_reg.po_req,'Translation' as type,translation.id,translation.intrpName,translation.orgName,translation.inchEmail,interpreter_reg.name,translation.source,translation.target,translation.invoic_date,translation.asignDate as assignDate,'00:00:00' as assignTime,translation.orgContact,translation.submited, translation.aloct_by,translation.aloct_date,translation.dated,translation.hrsubmited,translation.comp_hrsubmited,translation.interp_hr_date,translation.comp_hr_date, translation.numberUnit as hoursWorkd,translation.C_numberUnit as C_hoursWorkd,translation.total_charges_comp,translation.cur_vat,0 as C_otherexpns,translation.credit_note,translation.C_admnchargs, translation.rAmount,translation.rDate,translation.sentemail,translation.printed,translation.printedby,translation.deleted_flag,translation.order_cancel_flag,translation.nameRef,translation.orgRef,translation.invoiceNo,translation.commit,translation.new_comp_id,comp_reg.email,comp_reg.abrv as comp_abrv FROM translation,interpreter_reg,comp_reg WHERE translation.intrpName=interpreter_reg.id AND translation.orgName=comp_reg.abrv $append_multi_tr AND translation.deleted_flag = 0 and translation.order_cancel_flag=0 and translation.commit=1 $po_string_tr and (round(translation.rAmount,2) >= round((translation.total_charges_comp+(translation.total_charges_comp*translation.cur_vat)),2) and translation.rAmount>0) and (translation.orgRef like '%$string%' OR translation.porder like '%$string%' OR translation.nameRef like '%$string%' OR translation.invoiceNo like '%$string%')) as grp ORDER BY CONCAT(assignDate,' ',assignTime) LIMIT {$startpoint} , {$limit}";
            } else {
                if (isset($type) && !empty($type)) {
                    if ($type == 'Interpreter') {
                        $query = "SELECT * from (SELECT interpreter.porder,comp_reg.po_req,'Interpreter' as type,interpreter.id,interpreter.intrpName,interpreter.inchEmail,interpreter.orgName,interpreter_reg.name,interpreter.source,interpreter.target,interpreter.invoic_date,interpreter.assignDate,interpreter.assignTime,interpreter.orgContact,interpreter.submited, interpreter.aloct_by,interpreter.aloct_date,interpreter.dated,interpreter.hrsubmited,interpreter.comp_hrsubmited,interpreter.interp_hr_date, interpreter.comp_hr_date,interpreter.hoursWorkd,interpreter.C_hoursWorkd,interpreter.total_charges_comp,interpreter.cur_vat,interpreter.C_otherexpns, interpreter.credit_note,interpreter.C_admnchargs,interpreter.rAmount,interpreter.rDate,interpreter.sentemail,interpreter.printed,interpreter.printedby,interpreter.deleted_flag,interpreter.order_cancel_flag,interpreter.nameRef,interpreter.orgRef,interpreter.invoiceNo,interpreter.commit,interpreter.new_comp_id,comp_reg.email,comp_reg.abrv as comp_abrv FROM interpreter,interpreter_reg,comp_reg WHERE interpreter.intrpName=interpreter_reg.id AND interpreter.orgName=comp_reg.abrv $append_multi_int AND interpreter.deleted_flag = 0 and interpreter.order_cancel_flag=0 and interpreter.commit=1 $po_string_int and (round(interpreter.rAmount,2) >= round((interpreter.total_charges_comp+(interpreter.total_charges_comp*interpreter.cur_vat)+interpreter.c_otherexpns),2) and interpreter.rAmount>0) " . $append_invoice_date_f2f . $append_assignDate_f2f . $append_lang_f2f . $append_interp . $p_org_ad . $append_orgName_f2f . $append_rDate_f2f . $append_invoiceNo_f2f . ") as grp WHERE type like '%$type%' ORDER BY CONCAT(assignDate,' ',assignTime) LIMIT {$startpoint} , {$limit}";
                    } else if ($type == 'Telephone') {
                        $query = "SELECT * from (SELECT telephone.porder,comp_reg.po_req,'Telephone' as type,telephone.id,telephone.intrpName,telephone.orgName,telephone.inchEmail,interpreter_reg.name,telephone.source,telephone.target,telephone.invoic_date,telephone.assignDate,telephone.assignTime,telephone.orgContact,telephone.submited, telephone.aloct_by,telephone.aloct_date,telephone.dated,telephone.hrsubmited,telephone.comp_hrsubmited,telephone.interp_hr_date,telephone.comp_hr_date, telephone.hoursWorkd,telephone.C_hoursWorkd,telephone.total_charges_comp,telephone.cur_vat,0 as C_otherexpns,telephone.credit_note,telephone.C_admnchargs, telephone.rAmount,telephone.rDate,telephone.sentemail,telephone.printed,telephone.printedby,telephone.deleted_flag,telephone.order_cancel_flag,telephone.nameRef,telephone.orgRef,telephone.invoiceNo,telephone.commit,telephone.new_comp_id,comp_reg.email,comp_reg.abrv as comp_abrv FROM telephone,interpreter_reg,comp_reg WHERE telephone.intrpName=interpreter_reg.id AND telephone.orgName=comp_reg.abrv $append_multi_tp AND telephone.deleted_flag = 0 and telephone.order_cancel_flag=0 and telephone.commit=1 $po_string_tp and (round(telephone.rAmount,2) >= round((telephone.total_charges_comp+(telephone.total_charges_comp*telephone.cur_vat)+interpreter.c_otherexpns),2) and telephone.rAmount>0) " . $append_invoice_date_tp . $append_assignDate_tp . $append_lang_tp . $append_interp . $p_org_ad . $append_orgName_tp . $append_rDate_tp . $append_invoiceNo_tp . ") as grp WHERE type like '%$type%' ORDER BY CONCAT(assignDate,' ',assignTime) LIMIT {$startpoint} , {$limit}";
                    } else {
                        $query = "SELECT * from (SELECT translation.porder,comp_reg.po_req,'Translation' as type,translation.id,translation.intrpName,translation.orgName,translation.inchEmail,interpreter_reg.name,translation.source,translation.target,translation.invoic_date,translation.asignDate as assignDate,'00:00:00' as assignTime,translation.orgContact,translation.submited, translation.aloct_by,translation.aloct_date,translation.dated,translation.hrsubmited,translation.comp_hrsubmited,translation.interp_hr_date,translation.comp_hr_date, translation.numberUnit as hoursWorkd,translation.C_numberUnit as C_hoursWorkd,translation.total_charges_comp,translation.cur_vat,0 as C_otherexpns,translation.credit_note,translation.C_admnchargs, translation.rAmount,translation.rDate,translation.sentemail,translation.printed,translation.printedby,translation.deleted_flag,translation.order_cancel_flag,translation.nameRef,translation.orgRef,translation.invoiceNo,translation.commit,translation.new_comp_id,comp_reg.email,comp_reg.abrv as comp_abrv FROM translation,interpreter_reg,comp_reg WHERE translation.intrpName=interpreter_reg.id AND translation.orgName=comp_reg.abrv $append_multi_tr AND translation.deleted_flag = 0 and translation.order_cancel_flag=0 and translation.commit=1 $po_string_tr and (round(translation.rAmount,2) >= round((translation.total_charges_comp+(translation.total_charges_comp*translation.cur_vat)),2) and translation.rAmount>0) " . $append_invoice_date_tr . $append_assignDate_tr . $append_lang_tr . $append_interp . $p_org_ad . $append_orgName_tr . $append_rDate_tr . $append_invoiceNo_tr . ") as grp WHERE type like '%$type%' ORDER BY CONCAT(assignDate,' ',assignTime) LIMIT {$startpoint} , {$limit}";
                    }
                } else {
                    $query =
                        "SELECT * from (SELECT interpreter.porder,comp_reg.po_req,'Interpreter' as type,interpreter.id,interpreter.intrpName,interpreter.orgName,interpreter.inchEmail,interpreter_reg.name,interpreter.source,interpreter.target,interpreter.invoic_date,interpreter.assignDate,interpreter.assignTime,interpreter.orgContact,interpreter.submited, interpreter.aloct_by,interpreter.aloct_date,interpreter.dated,interpreter.hrsubmited,interpreter.comp_hrsubmited,interpreter.interp_hr_date, interpreter.comp_hr_date,interpreter.hoursWorkd,interpreter.C_hoursWorkd,interpreter.total_charges_comp,interpreter.cur_vat,interpreter.C_otherexpns, interpreter.credit_note,interpreter.C_admnchargs,interpreter.rAmount,interpreter.rDate,interpreter.sentemail,interpreter.printed,interpreter.printedby,interpreter.deleted_flag,interpreter.order_cancel_flag,interpreter.nameRef,interpreter.orgRef,interpreter.invoiceNo,interpreter.commit,interpreter.new_comp_id,comp_reg.email,comp_reg.abrv as comp_abrv FROM interpreter,interpreter_reg,comp_reg WHERE interpreter.intrpName=interpreter_reg.id AND interpreter.orgName=comp_reg.abrv $append_multi_int AND interpreter.deleted_flag = 0 and interpreter.order_cancel_flag=0 and interpreter.commit=1 $po_string_int and (round(interpreter.rAmount,2) >= round((interpreter.total_charges_comp+(interpreter.total_charges_comp*interpreter.cur_vat)+interpreter.c_otherexpns),2) and interpreter.rAmount>0) " . $append_invoice_date_f2f . $append_assignDate_f2f . $append_lang_f2f . $append_interp . $p_org_ad . $append_orgName_f2f . $append_rDate_f2f . $append_invoiceNo_f2f . " 
                        UNION ALL SELECT telephone.porder,comp_reg.po_req,'Telephone' as type,telephone.id,telephone.intrpName,telephone.orgName,telephone.inchEmail,interpreter_reg.name,telephone.source,telephone.target,telephone.invoic_date,telephone.assignDate,telephone.assignTime,telephone.orgContact,telephone.submited, telephone.aloct_by,telephone.aloct_date,telephone.dated,telephone.hrsubmited,telephone.comp_hrsubmited,telephone.interp_hr_date,telephone.comp_hr_date, telephone.hoursWorkd,telephone.C_hoursWorkd,telephone.total_charges_comp,telephone.cur_vat,0 as C_otherexpns,telephone.credit_note,telephone.C_admnchargs, telephone.rAmount,telephone.rDate,telephone.sentemail,telephone.printed,telephone.printedby,telephone.deleted_flag,telephone.order_cancel_flag,telephone.nameRef,telephone.orgRef,telephone.invoiceNo,telephone.commit,telephone.new_comp_id,comp_reg.email,comp_reg.abrv as comp_abrv FROM telephone,interpreter_reg,comp_reg WHERE telephone.intrpName=interpreter_reg.id AND telephone.orgName=comp_reg.abrv $append_multi_tp AND telephone.deleted_flag = 0 and telephone.order_cancel_flag=0 and telephone.commit=1 $po_string_tp and (round(telephone.rAmount,2) >= round((telephone.total_charges_comp+(telephone.total_charges_comp*telephone.cur_vat)),2) and telephone.rAmount>0) " . $append_invoice_date_tp . $append_assignDate_tp . $append_lang_tp . $append_interp . $p_org_ad . $append_orgName_tp . $append_rDate_tp . $append_invoiceNo_tp . " 
                        UNION ALL SELECT translation.porder,comp_reg.po_req,'Translation' as type,translation.id,translation.intrpName,translation.orgName,translation.inchEmail,interpreter_reg.name,translation.source,translation.target,translation.invoic_date,translation.asignDate as assignDate,'00:00:00' as assignTime,translation.orgContact,translation.submited, translation.aloct_by,translation.aloct_date,translation.dated,translation.hrsubmited,translation.comp_hrsubmited,translation.interp_hr_date,translation.comp_hr_date, translation.numberUnit as hoursWorkd,translation.C_numberUnit as C_hoursWorkd,translation.total_charges_comp,translation.cur_vat,0 as C_otherexpns,translation.credit_note,translation.C_admnchargs, translation.rAmount,translation.rDate,translation.sentemail,translation.printed,translation.printedby,translation.deleted_flag,translation.order_cancel_flag,translation.nameRef,translation.orgRef,translation.invoiceNo,translation.commit,translation.new_comp_id,comp_reg.email,comp_reg.abrv as comp_abrv FROM translation,interpreter_reg,comp_reg WHERE translation.intrpName=interpreter_reg.id AND translation.orgName=comp_reg.abrv $append_multi_tr AND translation.deleted_flag = 0 and translation.order_cancel_flag=0 and translation.commit=1 $po_string_tr and (round(translation.rAmount,2) >= round((translation.total_charges_comp+(translation.total_charges_comp*translation.cur_vat)),2) and translation.rAmount>0) " . $append_invoice_date_tr . $append_assignDate_tr . $append_lang_tr . $append_interp . $p_org_ad . $append_orgName_tr . $append_rDate_tr . $append_invoiceNo_tr . ") as grp 
                        WHERE 1 " . $append_type . " ORDER BY CONCAT(assignDate,' ',assignTime) LIMIT {$startpoint} , {$limit}";
                }
            } ?>
            <div class="tab_container">
                <table class="table table-bordered tbl_data" cellspacing="0" cellpadding="0">
                    <thead class="bg-info">
                        <tr>
                            <td align="center">
                                <?php 
                                if ($action_export_to_excel) {
                                    $append_url_export = substr($_SERVER['REQUEST_URI'], (strrpos($_SERVER['REQUEST_URI'], basename(__FILE__)) ?: -1));
                                    $export_link = isset($string) && !empty($string) ? 'reports_lsuk/excel/' . basename(__FILE__) . '?str=' . $string : 'reports_lsuk/excel/' . $append_url_export; ?>
                                    <a id="btn_export" style="position: absolute;left: 21px;" href="<?= $export_link ?>" title="Download Excel Report"><span class="btn btn-xs btn-success"><span class="hidden-sm hidden-xs">Export To </span>Excel <i class="glyphicon glyphicon-download"></i></span></a>
                                <?php } ?>
                                <div class="">
                                    <?php echo pagination($con, $table, $query, $limit, $page); ?>
                                    <b class="text-right h4 pull-right">
                                        <?php if (!empty($type)) {
                                            echo $type . ' paid jobs list';
                                        } else {
                                            echo 'All paid jobs list';
                                        } ?>
                                    </b>
                                </div>
                            </td>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $result = mysqli_query($con, $query);
                        if (mysqli_num_rows($result) == 0) {
                            echo '<tr>
                      <td><h4 class="text-danger text-center">Sorry ! There are no records.</h4></td></tr>';
                        } else {
                            while ($row = mysqli_fetch_array($result)) {
                                $page_count++;
                                $counter++; 
                                $prv_org='';
                                if($row['orgName']=="LSUK_Private Client" && $row['new_comp_id']!=0){
                                    $prv_org = $acttObj->read_specific("name", "private_company", " id={$row['new_comp_id']}")['name'];
                                    $prv_org = "LSUK_".$prv_org;
                                }
                                ?>
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
                                                <li><?php echo '<span class="label label-default w3-large w3-right">' . $row['C_hoursWorkd'] == 0 ? '<span class="label w3-large  w3-red">' . ($prv_org!=''?$prv_org:$row['orgName']) . '</span><span class="w3-large w3-right text-right" style="font-weight:bold;margin-top:-10px;font-size:14px!important;"><span ' . $bg_aD . '>' . $row['assignDate'] . '</span><br> ' . $row['assignTime'] . '</span>' : '<span class="label w3-large  w3-blue">' . $row['orgName'] . '</span><span class="w3-large w3-right text-right" style="font-weight:bold;margin-top:-10px;font-size:14px;"><span ' . $bg_aD . '>' . $row['assignDate'] . '</span> ' . $row['assignTime'] . '</span>'; ?></li>
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
                                                <li>Paid Date<span class="w3-right"><?php echo '<span ' . $bg_pD . '>' . $misc->dated($row['rDate']) . '</span>'; ?></span></li>
                                                <li>Purch.Order#<span class="w3-right">
                                                <?php if ($row['po_req'] == 1 && $row['porder'] != '') {
                                                    echo $row['porder'];
                                                } else if ($row['po_req'] == 1 && $row['porder'] == '') {
                                                    echo '<span class="text-danger"><b>Missing!</b></span>';
                                                } else {
                                                    echo '<span class="text-info">Not required!</span>';
                                                } ?></span></li>
                                            </ul>
                                        </div>
                                        <div class="col-sm-12 text-center action_buttons">
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
                                                <a href="javascript:void(0)" onClick="popupwindow('order_view.php?view_id=<?php echo $row['id']; ?>&table=<?php echo strtolower($row['type']); ?>', 'View order paid', 1000, 1000);"><input type="image" src="images/icn_new_article.png" title="View Order"></a>
                                            <?php }
                                            if ($action_edit_job) { ?>
                                                <a href="javascript:void(0)" onClick="popupwindow('<?php echo $edit_page; ?>?edit_id=<?php echo $row['id']; ?>', 'Edit order paid', 1250, 730);"><i class="fa fa-edit text-primary" title="Edit job"></i></a>
                                            <?php }
                                            if ($action_update_expenses) { ?>
                                                <a href="javascript:void(0)" onClick="popupwindow('<?php echo $exp_page; ?>?update_id=<?php echo $row['id']; ?>', 'Update Expenses', 1150, 620);"><i class="fa fa-refresh text-primary" title="Update Expenses"></i></a>
                                            <?php }
                                            if ($action_job_note) {
                                                $arr_n = $acttObj->read_specific("(select count(id)", "jobnotes", "tbl='" . strtolower($row['type']) . "' and fid=" . $row['id'] . " and (readcount is null or readcount=0)) as unread ,(select count(id) from jobnotes where tbl='" . strtolower($row['type']) . "' and fid=" . $row['id'] . " and (readcount is not null and readcount!=0)) as yes_read"); ?>
                                                <a href="javascript:void(0)" title="JOB NOTES" data-toggle="popover" data-trigger="hover" data-placement="top" style="text-decoration:none;" data-content="<?php echo '<b>' . $arr_n['unread'] . '</b> unread <b>' . $arr_n['yes_read'] . '</b> read job notes'; ?>" onclick="popupwindow('jobnote.php?fid=<?php echo $row['id']; ?>&table=<?php echo strtolower($row['type']); ?>&orgName=<?php echo $row['orgName']; ?>','Job notes paid',900,800)" <?php echo $arr_n['unread'] > 0 ? 'class="w3-button w3-jb w3-small w3-circle w3-blue"' : 'class="w3-button w3-jb w3-small w3-circle w3-grey"'; ?>><?php echo $arr_n['unread'] > 0 ? $arr_n['unread'] : $arr_n['yes_read']; ?></a>
                                            <?php }
                                            if ($action_receive_payment) { ?>
                                                <a href="javascript:void(0)" onClick="popupwindow('receive_amount.php?row_id=<?php echo $row['id']; ?>&table=<?php echo strtolower($row['type']) ?>', 'Receive amount paid', 800,450);"><input type="image" src="images/Cash.png" title="Payment Received"></a>
                                            <?php }
                                            if ($action_receive_partial_payment) { ?>
                                                <a href="javascript:void(0)" onClick="popupwindow('receive_part.php?row_id=<?php echo $row['id']; ?>&table=<?php echo strtolower($row['type']) ?>','Partial amount paid', 800,450);"><i class="fa fa-money" title="Receive Partial Payment"></i></a>
                                            <?php }
                                            if ($action_view_invoice) { ?>
                                                <a href="javascript:void(0)" onClick="popupwindow('<?php echo $inv_page; ?>?invoice_id=<?php echo $row['id']; ?>', 'View invoice paid', 1000, 1000);"><input type="image" src="images/invoice.png" title="Invoice"></a>
                                            <?php }
                                            if ($action_edited_history) { ?>
                                                <a data-table-name="<?php echo strtolower($row['type']) ?>" data-record-id="<?php echo $row['id'] ?>" onclick="investigate_order(this)" href="javascript:void(0)" title="View Edited Log History"><i class="fa fa-list text-danger"></i></a>
                                            <?php }
                                            if ($action_make_credit_note) { ?>
                                                <a href="javascript:void(0)" onClick="popupwindow('<?php echo $credit_page; ?>?invoice_id=<?php echo $row['id']; ?>','Credit note paid', 1000, 1000);"><i class="fa fa-exclamation-circle  <?=!empty($row['credit_note'])?'text-danger':''?>" title="Credit Note"></i></a>
                                            <?php }
                                            if ($action_purchase_order) { ?>
                                                <a class="hidden" href="javascript:void(0)" onClick="popupwindow('purch_update.php?purch_id=<?php echo $row['id']; ?>&table=<?php echo strtolower($row['type']); ?>&orgName=<?php echo $row['orgName']; ?>','Update Purchase order', 550,450);"><input type="image" src="images/icn_tags.png" title="Update Purchase Order #"></a>
                                            <?php }
                                            if ($action_view_earnings) { ?>
                                                <a href="javascript:void(0)" onClick="popupwindow('<?php echo $comp_earning; ?>?view_id=<?php echo $row['id']; ?>','View earning paid', 900, 400);"><input type="image" src="images/earning.png" title="Earning"></a>
                                            <?php }
                                            if ($action_uncommit_payment) { ?>
                                                <a href="javascript:void(0)" onClick="popupwindow('un_commit.php?com_id=<?php echo $row['id']; ?>&table=<?php echo strtolower($row['type']); ?>&st=1', 'Uncommit payment paid', 550,350);"><input type="image" src="images/icn_jump_back.png" title="Un-commit payment"></a>
                                            <?php } ?>
                                            <a href="javascript:void(0)" title="Download Timesheet" onclick="popupwindow('reports_lsuk/pdf/new_timesheet.php?update_id=<?php echo $row['id']; ?>&table=<?php echo strtolower($row['type']); ?>&down', 'title', 1000, 1000);"><i class="glyphicon glyphicon-download"></i></a>
                                            <?php if ($row['type'] == 'Interpreter') {
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
        function investigate_order(element) {
            // var table_name_array = {"interpreter" : "Face To Face Booking", "telephone":"Telephone Booking", "translation" : "Translation Booking"};
            var investigate_order_id = $(element).attr("data-record-id");
            // var investigate_order_id = $("#investigate_order_id").val();
            var investigate_order_type = $(element).attr("data-table-name");
            // var investigate_order_type = $("#investigate_order_type").val();
            console.log(investigate_order_type);
            var table_name_array = {
                "interpreter": "Face To Face Order",
                "telephone": "Telephone Order",
                "translation": "Translation Order"
            };
            var order_type_array = {
                "interpreter": 1,
                "telephone": 2,
                "translation": 3
            };
            if (investigate_order_id && investigate_order_type) {
                $('.process_modal_attach').html("<center><i class='fa fa-circle fa-2x'></i> <i class='fa fa-circle fa-2x'></i> <i class='fa fa-circle fa-2x'></i><br><h3>Loading ...<br><br>Please Wait !!!</h3></center>");
                $('#process_modal').modal('show');
                $('body').removeClass('modal-open');
                $.ajax({
                    url: 'process/investigate_order.php',
                    method: 'post',
                    dataType: 'json',
                    data: {
                        investigate_order_id: investigate_order_id,
                        investigate_order_type: order_type_array[investigate_order_type],
                        table_name: table_name_array[investigate_order_type],
                        investigate_order: 1
                    },
                    success: function(data) {
                        if (data['status'] == 1) {
                            $('.process_modal_attach').html(data['body']);
                        } else {
                            $('.process_modal_attach').html("<div class='col-md-10 col-md-offset-1 alert alert-danger'>Cannot load requested response. Please try again!</div>");
                        }
                    },
                    error: function(data) {
                        $('.process_modal_attach').html("<div class='col-md-10 col-md-offset-1 alert alert-danger'>Error: Please select valid Order ID and Order Type for order history details or refresh the page! Thank you</div>");
                    }
                });
            } else {
                $('.process_modal_attach').html("<div class='col-md-10 col-md-offset-1 alert alert-danger'>Error: Please select valid Order ID for history details or refresh the page! Thank you</div>");
                $("#investigate_order_id").focus();
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
        $(function() {
            $('.searchable').multiselect({
                includeSelectAllOption: true,
                numberDisplayed: 1,
                enableFiltering: true,
                enableCaseInsensitiveFiltering: true
            });
        });
    </script>
</body>

</html>