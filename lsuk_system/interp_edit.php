<?php if (session_id() == '' || !isset($_SESSION)) {
    session_start();
}
include '../source/setup_email.php';
include 'db.php';
include 'class.php';

$table = 'interpreter';
$edit_id = SafeVar::GetVar('edit_id', '');
$duplicate = SafeVar::GetVar('duplicate', '');

$is_shift = isset($_GET['is_shift']) ? true : false;
if ($is_shift) {
    $allowed_type_idz = "189";
    $action_name = "Shift Telephone Order To F2F";
    $row = $acttObj->read_specific("telephone.*", "telephone", "telephone.id=" . $edit_id);
    $shift_from_id = $edit_id;
    $shift_from_ref = $row['nameRef'];
} else {
    $allowed_type_idz = "2,16,29,71,83,114,174";
    $action_name = "Edit Order";
    $row = $acttObj->read_specific("*", "$table", "id=" . $edit_id);
}

//Check if user has current action allowed
if ($_SESSION['is_root'] == 0) {
    $get_actions = explode(",", $acttObj->read_specific("GROUP_CONCAT(action_permissions.action_id) as actions", "action_permissions,route_actions", "action_permissions.action_id=route_actions.id AND route_actions.route_id=4 AND action_permissions.user_id=" . $_SESSION['userId'])['actions']);
    if(!(in_array(5, $get_actions) && $duplicate=="yes")){
        $get_page_access = $acttObj->read_specific("id", "action_permissions", "user_id=" . $_SESSION['userId'] . " AND action_id IN (" . $allowed_type_idz . ")")['id'];
        if (empty($get_page_access)) {
            die("<center><h2 class='text-center text-danger'>You do not have access to <u>" . $action_name . "</u> action for jobs!<br>Kindly contact admin for further process.</h2></center>");
        }
    }
}
function array_equal_values(array $a, array $b) {
    return !array_diff($a, $b) && !array_diff($b, $a);
}

$source = $row['source'];
$new_company_id = $row['new_comp_id'];
if ($new_company_id != 0) {
    $private_company = $acttObj->read_specific("*", "private_company", "id=" . $new_company_id);
    $private_company_name = $private_company['name'];
}
$target = $row['target'];
$interp_cat = $row['interp_cat'];
$interp_type = $row['interp_type'];
$assignDate = $row['assignDate'];
$assignTime = $row['assignTime'];
$assignDur = $row['assignDur'];
$guess_dur = $row['guess_dur'];
$nameRef = $row['nameRef'];
$buildingName = $row['buildingName'];
$line1 = $row['line1'];
$line2 = $row['line2'];
$street = $row['street'];
$assignCity = $row['assignCity'];
$postCode = $row['postCode'];
$inchPerson = $row['inchPerson'];
$inchContact = $row['inchContact'];
$inchEmail = $row['inchEmail'];
$inchEmail2 = $row['inchEmail2'];
$inchNo = $row['inchNo'];
$inchRoad = $row['inchRoad'];
$inchCity = $row['inchCity'];
$inchPcode = $row['inchPcode'];
$orgName = $row['orgName'];
$orgRef = $row['orgRef'];
$orgContact = $row['orgContact'];
$remrks = $row['remrks'];
$gender = $row['gender'];
$intrpName = $row['intrpName'];
$jobStatus = $row['jobStatus'];
$bookinType = $row['bookinType'];
$company_rate_id = $row['company_rate_id']?:NULL;
$company_rate_data = !empty($row['company_rate_data']) ? (array) json_decode($row['company_rate_data']) : array();
$I_Comments = $row['I_Comments'];
$snote = $row['snote'];
$jobDisp = $row['jobDisp'];
$invoiceNo = $row['invoiceNo'];
$bookedVia = $row['bookedVia'];
$assignIssue = $row['assignIssue'];
$dbs_checked = $row['dbs_checked'];
$noty = $row['noty'];
$noty_reason = $row['noty_reason'];
$dbs_bookeddate = $row['bookeddate'];
$dbs_bookedtime = $row['bookedtime'];
$dbs_bookednamed = $row['namedbooked'];
$is_temp = $row['is_temp'];
$porder = $row['porder'];
$po_req = $acttObj->read_specific("po_req", "comp_reg", "abrv='" . $orgName . "'")['po_req'];
$porder_email = $row['porder_email'];
if (isset($_POST['submit'])) {
    if ($duplicate == 'yes') { //..................for validation of duplication of order............//
        $v_source = @$_POST['source'];
        $v_assignDate = @$_POST['assignDate'];
        $v_assignTime = @$_POST['assignTime'];
        $v_orgName = @$_POST['orgName'];
        $v_orgContact = @$_POST['orgContact'];
        $v_orgRef = @$_POST['orgRef'];
    
        $get_duplicate = $acttObj->read_specific(
            "count(id) as val", 
            "$table", 
            "source='$v_source' and assignDate='$v_assignDate' and assignTime='$v_assignTime' and 
            orgName='$v_orgName' and orgContact='$v_orgContact' and orgRef='$v_orgRef'"
        );
        $val = $get_duplicate['val'];
        if ($val == 0) {
            $edit_id = $acttObj->get_id($table);
            //Create & save new reference no
            $reference_no = $acttObj->generate_reference(1, $table, $edit_id);
            //Create nameRef for new duplicate order
            $month = substr(date('M'), 0, 3);
            if ($is_shift) {
                $c5 = 'LSUK/' . $month . '/' . $reference_no;
                $acttObj->editFun($table, $edit_id, 'nameRef', $c5);
                //Update global_reference_no TABLE about shifting data
                $acttObj->update("telephone", array("deleted_flag" => 1, "is_shifted" => 1), "id=$shift_from_id");
                $acttObj->insert('jobnotes', array('jobNote' => "This order is now shifted to Face To Face Job ID # " . $edit_id, 'tbl' => "telephone", 'time' => $misc->sys_datetime_db(), 'fid' => $shift_from_id, 'submitted' => $_SESSION['UserName'], 'dated' => date('Y-m-d')));
                                $jobnote_new = "This order was initially booked as remote (telephone or teams or zoom) REF #
                <strong><a href=\"#\" onClick=\"popupwindow('order_view.php?view_id=$shift_from_id&table=telephone', 'View Order', 1000, 1000); return false;\">$shift_from_ref</a></strong> 
                changed to Face to Face REF # 
                <strong><a href=\"#\" onClick=\"popupwindow('order_view.php?view_id=$edit_id&table=interpreter', 'View Order', 1000, 1000); return false;\">$c5</a></strong>";

                // Escape for SQL
                $jobnote_new = mysqli_real_escape_string($con, $jobnote_new);

                $acttObj->insert('jobnotes', array('jobNote' => $jobnote_new, 'tbl' => $table, 'time' => $misc->sys_datetime_db(), 'fid' => $edit_id, 'submitted' => $_SESSION['UserName'], 'dated' => date('Y-m-d')));
                $acttObj->update("global_reference_no", array("is_shifted" => 1, "shifted_from" => $shift_from_id, "shifted_to" => $edit_id, "updated_date" => date('Y-m-d H:i:s')), "reference_no='" . $row['reference_no'] . "'");
                //Log history for shifted order
                $old_values = array('Shifted Order' => 'From Telephone #' . $shift_from_id, 'Deleted' => 'No', 'Ref ID' => $row['reference_no']);
                $new_values = array('Shifted Order' => 'To F2F #' . $edit_id, 'Deleted' => 'Yes', 'Ref ID' => $reference_no);
                $acttObj->log_changes(json_encode($old_values), json_encode($new_values), $shift_from_id, "telephone", "update", $_SESSION['userId'], $_SESSION['UserName'], "shift_job_telephone");
            } else {
                $c5 = 'LSUK/' . $month . '/' . $reference_no;
                $acttObj->editFun($table, $edit_id, 'nameRef', $c5);
            }
        } else {
            $edit_id = '';
            echo "<script>alert('oops..This job is already booked!');</script>";
            echo '<script type="text/javascript">' . "\n";
            echo 'window.history.back()';
            echo '</script>';
        }
    }
    $c1 = $_POST['source'];
    $acttObj->editFun($table, $edit_id, 'source', $c1);
    $c2 = $_POST['target'];
    $acttObj->editFun($table, $edit_id, 'target', $c2);
    $c_interp_cat = $_POST['interp_cat'];
    $acttObj->editFun($table, $edit_id, 'interp_cat', $c_interp_cat);
    if ($_POST['interp_cat'] != '12') {
        $c_interp_type = implode(",", $_POST['interp_type']);
        $acttObj->editFun($table, $edit_id, 'interp_type', $c_interp_type);
        $c_assignIssue = $_POST['assignIssue'];
        $acttObj->editFun($table, $edit_id, 'assignIssue', $c_assignIssue);
    }
    $c_buildingName = $_POST['buildingName'];
    $acttObj->editFun($table, $edit_id, 'buildingName', $c_buildingName);
    $c9 = $_POST['assignCity'];
    $acttObj->editFun($table, $edit_id, 'assignCity', $c9);
    $c10 = $_POST['postCode'];
    $acttObj->editFun($table, $edit_id, 'postCode', $c10);
    $acttObj->editFun($table, $edit_id, 'postcode_data', $_POST['postcode_data']);
    $c_street = $_POST['street'];
    $acttObj->editFun($table, $edit_id, 'street', $c_street);
    if (trim($_POST['orgName']) != "") {
        $c18 = $_POST['orgName'];
        $acttObj->editFun($table, $edit_id, 'orgName', $c18);
        $acttObj->editFun($table, $edit_id, 'order_company_id', $_POST['order_company_id']);
    }
    $c_orgRef = $_POST['orgRef'];
    $acttObj->editFun($table, $edit_id, 'orgRef', $c_orgRef);
    $ref_counter = $acttObj->read_specific("count(*) as counter", "comp_ref", "company='" . $_POST['orgName'] . "' AND reference='" . $c_orgRef . "'")['counter'];
    if ($ref_counter == 0 && !empty($c_orgRef)) {
        $get_reference_id = $acttObj->get_id("comp_ref");
        $acttObj->update("comp_ref", array("company" => $_POST['orgName'], "reference" => $c_orgRef), array("id" => $get_reference_id));
        $acttObj->editFun($table, $edit_id, 'reference_id', $get_reference_id);
    } else {
        $existing_ref_id = $acttObj->read_specific("id", "comp_ref", "company='" . $_POST['orgName'] . "' AND reference='" . $c_orgRef . "'")['id'];
        $acttObj->editFun($table, $edit_id, 'reference_id', $existing_ref_id);
    }
    $c_orgContact = $_POST['orgContact'];
    $acttObj->editFun($table, $edit_id, 'orgContact', $c_orgContact);
    if (!empty($_POST['remrks'])) {
        $c_remrks = $_POST['remrks'];
        $acttObj->editFun($table, $edit_id, 'remrks', $c_remrks);
        if (isset($_POST['job_note']) && !empty($_POST['job_note']) && !empty($_POST['remrks'])) {
            $acttObj->insert('jobnotes', array('jobNote' => $c_remrks, 'tbl' => $table, 'time' => $misc->sys_datetime_db(), 'fid' => $edit_id, 'submitted' => $_SESSION['UserName'], 'dated' => date('Y-m-d')));
        }
    }
    if (!empty($_POST['I_Comments'])) {
        $c_I_Comments = $_POST['I_Comments'];
        $acttObj->editFun($table, $edit_id, 'I_Comments', $c_I_Comments);
        if (isset($_POST['job_note_c']) && !empty($_POST['job_note_c']) && !empty($_POST['I_Comments'])) {
            $acttObj->insert('jobnotes', array('jobNote' => $c_I_Comments, 'tbl' => $table, 'time' => $misc->sys_datetime_db(), 'fid' => $edit_id, 'submitted' => $_SESSION['UserName'], 'dated' => date('Y-m-d')));
        }
    }
    if (isset($_POST['new_company_checkbox']) && $_POST['orgName'] == "LSUK_Private Client") {
        if ($new_company_id != 0) {
            $acttObj->update("private_company", array("name" => $_POST['new_company_name'], "inchPerson" => $_POST['inchPerson'], "inchContact" => $_POST['inchContact'], "inchEmail" => $_POST['inchEmail'], "inchEmail2" => $_POST['inchEmail2'], "inchNo" => $_POST['inchNo'], "line1" => $_POST['line1'], "line2" => $_POST['line2'], "inchRoad" => $_POST['inchRoad'], "inchCity" => $_POST['inchCity'], "inchPcode" => $_POST['inchPcode']), array("id" => $new_company_id));
        } else {
            $new_company_id = $acttObj->get_id("private_company");
            $acttObj->update("private_company", array("name" => $_POST['new_company_name'], "inchPerson" => $_POST['inchPerson'], "inchContact" => $_POST['inchContact'], "inchEmail" => $_POST['inchEmail'], "inchEmail2" => $_POST['inchEmail2'], "inchNo" => $_POST['inchNo'], "line1" => $_POST['line1'], "line2" => $_POST['line2'], "inchRoad" => $_POST['inchRoad'], "inchCity" => $_POST['inchCity'], "inchPcode" => $_POST['inchPcode']), array("id" => $new_company_id));
            $acttObj->editFun($table, $edit_id, 'new_comp_id', $new_company_id);
        }
    }
    $c14 = $_POST['inchNo'];
    $acttObj->editFun($table, $edit_id, 'inchNo', $c14);
    $c14 = $_POST['line1'];
    $acttObj->editFun($table, $edit_id, 'line1', $c14);
    $c14 = $_POST['line2'];
    $acttObj->editFun($table, $edit_id, 'line2', $c14);
    $c15 = $_POST['inchRoad'];
    $acttObj->editFun($table, $edit_id, 'inchRoad', $c15);
    $c16 = $_POST['inchCity'];
    $acttObj->editFun($table, $edit_id, 'inchCity', $c16);
    $c17 = $_POST['inchPcode'];
    $acttObj->editFun($table, $edit_id, 'inchPcode', $c17);
    $c_inchPerson = $_POST['inchPerson'];
    $acttObj->editFun($table, $edit_id, 'inchPerson', $c_inchPerson);
    $c12 = $_POST['inchContact'];
    $acttObj->editFun($table, $edit_id, 'inchContact', $c12);
    $c13 = $_POST['inchEmail'];
    $acttObj->editFun($table, $edit_id, 'inchEmail', $c13);
    $strEmail2 = $_POST['inchEmail2'];
    $acttObj->editFun($table, $edit_id, 'inchEmail2', $strEmail2);
    $porder_email = $_POST['po_req'];
    $acttObj->editFun($table, $edit_id, 'porder_email', $porder_email);
    if (isset($_POST['po_number']) && isset($_POST['purchase_order_number'])) {
        $purchase_order_number = trim($_POST['purchase_order_number']);
        if(!empty($purchase_order_number)){
            $porder_inv = $acttObj->read_specific(" SUM(num_inv) as no_inv,SUM(total_cost) as used_credit ","(SELECT COUNT(interpreter.id) as num_inv,round(IFNULL(sum(interpreter.total_charges_comp),0)+ IFNULL(sum(interpreter.total_charges_comp * interpreter.cur_vat),0) +IFNULL(sum(interpreter.C_otherexpns),0),2) as total_cost FROM interpreter where interpreter.porder='$purchase_order_number' AND interpreter.deleted_flag=0 and interpreter.order_cancel_flag=0 AND interpreter.commit=1 and interpreter.invoic_date!='1001-01-01'  UNION ALL SELECT COUNT(telephone.id) as num_inv,round(IFNULL(sum(telephone.total_charges_comp),0)+ IFNULL(sum(telephone.total_charges_comp * telephone.cur_vat),0),2) as total_cost FROM telephone WHERE telephone.porder='$purchase_order_number' AND telephone.deleted_flag=0 and telephone.order_cancel_flag=0 AND telephone.commit=1 and telephone.invoic_date!='1001-01-01' UNION ALL SELECT COUNT(translation.id) as num_inv,round(IFNULL(sum(translation.total_charges_comp),0)+ IFNULL(sum(translation.total_charges_comp * translation.cur_vat),0),2) as total_cost FROM translation ","translation.porder='$purchase_order_number' AND translation.deleted_flag=0 and translation.order_cancel_flag=0 AND translation.commit=1 and translation.invoic_date!='1001-01-01') as grp");
            $no_inv = $porder_inv['no_inv'];
            $used_credit = $porder_inv['used_credit'];
            $sum_amount = $acttObj->read_specific(" MAX(credit) as po_balance ","comp_credit"," porder='$purchase_order_number' AND deleted_flag=0")['po_balance'];
            $rem_balance=$sum_amount-$used_credit;   
            $po_counter = $acttObj->read_specific("count(*) as counter", "porder_details", "company='" . $_POST['orgName'] . "' AND porder='" . $purchase_order_number . "'")['counter'];
            if ($po_counter == 0 && !empty($purchase_order_number)) {
                $acttObj->insert('jobnotes', array('jobNote' => 'Add purchase order #' . $purchase_order_number . ' for job reference:' . $c6, 'tbl' => $table, 'time' => $misc->sys_datetime_db(), 'fid' => $edit_id, 'submitted' => $_SESSION['UserName'], 'dated' => date('Y-m-d')));
            }elseif($rem_balance<=1){
                $acttObj->insert('jobnotes', array('jobNote' => 'NO REMAINING BALANCE in the Added purchase order #' . $purchase_order_number . ' for job reference:' . $c6, 'tbl' => $table, 'time' => $misc->sys_datetime_db(), 'fid' => $edit_id, 'submitted' => $_SESSION['UserName'], 'dated' => date('Y-m-d')));
            } else {
                $acttObj->editFun($table, $edit_id, 'porder', $purchase_order_number);
            }
        }else{
            $acttObj->editFun($table, $edit_id, 'porder', $purchase_order_number);
        }
    }
    $c3 = $_POST['assignDate'];
    $acttObj->editFun($table, $edit_id, 'assignDate', $c3);
    $c4 = $_POST['assignTime'];
    $acttObj->editFun($table, $edit_id, 'assignTime', $c4);
    $c5 = $_POST['assignDur'];
    $acttObj->editFunTimeAsMins($table, $edit_id, 'assignDur', $c5);
    $guess_dur = $_POST['guess_dur'];
    $acttObj->editFunTimeAsMins($table, $edit_id, 'guess_dur', $guess_dur);
    $c22 = $_POST['bookinType'];
    $acttObj->editFun($table, $edit_id, 'bookinType', $c22);
    if ($_POST['company_rate_id']) {
        $company_rate_id = $_POST['company_rate_id'];
        $acttObj->editFun($table, $edit_id, 'company_rate_id', $company_rate_id);
        $company_rate_data = $_POST['company_rate_data'];
        $acttObj->editFun($table, $edit_id, 'company_rate_data', $company_rate_data);
    }
    $c22 = $_POST['jobStatus'];
    if($c22!=$jobStatus){
        if($c22=='0'){
            $acttObj->insert("daily_logs", array("action_id" => 43, "user_id" => $_SESSION['userId'], "details" => "Status Shifted to Enquiry: " . $edit_id));
        }else if($c22=='1'){
            $acttObj->insert("daily_logs", array("action_id" => 34, "user_id" => $_SESSION['userId'], "details" => "Job Confirmed: " . $edit_id));
        }
    }
    $acttObj->editFun($table, $edit_id, 'jobStatus', $c22);
    $c22 = $_POST['dbs_checked'];
    $acttObj->editFun($table, $edit_id, 'dbs_checked', $c22);
    $c22 = $_POST['jobDisp'];
    $acttObj->editFun($table, $edit_id, 'jobDisp', $c22);
    $noty_reason_post = $_POST['selector'] == 'sc' ? $_POST['selector_reason'] : '';
    $acttObj->editFun($table, $edit_id, 'noty_reason', $noty_reason_post);
    $noty_post = $_POST['selector'] == 'sc' ? implode(',', $_POST['selected_interpreters']) : '';
    if ($row['noty'] != $noty_post && $_POST['selected_interpreters']) {
        $reason_title = $_POST['selector'] == 'sc' && $_POST['selector_reason'] ? '<br>Reason: ' . $_POST['selector_reason'] : '';
        $interpreter_names = $acttObj->read_specific("GROUP_CONCAT(name) as names", "interpreter_reg", "id IN (" . $noty_post . ")")['names'];
        $acttObj->insert('jobnotes', array('jobNote' => "Notified interpreters: " . $interpreter_names . $reason_title, 'tbl' => $table, 'time' => $misc->sys_datetime_db(), 'fid' => $edit_id, 'submitted' => $_SESSION['UserName'], 'dated' => date('Y-m-d')));
    }
    $acttObj->editFun($table, $edit_id, 'noty', $noty_post);
}

if ($duplicate == 'yes' && !$is_shift) {
    $month = date('M');
    $month = substr($month, 0, 3);
    $lastid = $acttObj->max_id("global_reference_no") + 1;
    $nameRef = 'LSUK/' . $month . '/' . $lastid;
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
    <title>Edit F2F Booking Form</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" />
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <style>
        .ri {
            margin-top: 7px;
        }

        .ri .label {
            font-size: 100%;
            padding: .5em 0.6em 0.5em;
        }

        .checkbox-inline+.checkbox-inline,
        .radio-inline+.radio-inline {
            margin-top: 4px;
        }

        .multiselect {
            min-width: 300px;
        }

        .multiselect-container {
            max-height: 400px;
            overflow-y: auto;
            max-width: 380px;
        }

        #div_specific .btn-group .dropdown-menu {
            top: unset;
            bottom: 100%;
        }

        /* Formatting search box */
        .search-box {
            width: 300px;
            position: relative;
            display: inline-block;
            font-size: 14px;
        }

        .search-box input[type="text"] {
            height: 32px;
            padding: 5px 10px;
            border: 1px solid #CCCCCC;
            font-size: 14px;
        }

        .result {
            position: absolute;
            z-index: 1;
            top: 100%;
            width: 90% !important;
            background: white;
            max-height: 246px;
            overflow-y: auto;
        }

        .search-box input[type="text"],
        .result {
            width: 100%;
            box-sizing: border-box;
        }

        /* Formatting result items */
        .result p {
            margin: 0;
            padding: 7px 10px;
            border: 1px solid #CCCCCC;
            border-top: none;
            cursor: pointer;
        }

        .result p:hover {
            background: #f2f2f2;
        }
    </style>
    <script type="text/javascript" src="js/debug.js"></script>
    <script type="text/javascript" src="js/postcodelookup.js"></script>
    <!--        for auto fil text boxez...........................-->
    <script type="text/javascript">
        function refreshParent() {
            window.opener.location.reload();
        }

        function MM_openBrWindow(theURL, winName, features) {
            window.open(theURL, winName, features);
        }
    </script>
    <script src="js/jquery-1.11.3.min.js"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            $('#orgName').on('change', function(e) {
                GetOrganizationFields();
            });
        });
    </script>
</head>

<body>
    <?php //if job already booked or not deleted/cancelled
    if (isset($_GET['is_home']) && !empty($intrpName)) {
        $check_booked = $acttObj->read_specific("interpreter_reg.name", "interpreter_reg", "id=" . $intrpName);
        $via = $row['aloct_by'] == 'Auto Allocated' ? ' Via system auto allocation' : ' by ' . $row['aloct_by'];
        echo "<div class='alert alert-warning col-md-10 col-md-offset-1 text-center h4'>This job is already assigned to <b>" . $check_booked['name'] . "</b>" . $via . " !</b></div>";
        exit;
    } else if (isset($_GET['is_home']) && ($row['deleted_flag'] == 1 || $row['order_cancel_flag'] == 1)) {
        echo "<div class='alert alert-warning col-md-10 col-md-offset-1 text-center h4'><b>Sorry ! This job is no longer available. It is either Cancelled or Deleted.<br>Thank you</b></div>";
        exit;
    } else { ?>
        <form action="" method="post" class="register">
            <div style="background: #8c8c86;padding: 6px;position: fixed;z-index: 999999999999;width: 100%;margin-top: -10px;color: white;">
                <b><h3 style="display: inline-block;"><?= $duplicate == 'yes' ? ($is_shift ? 'Shift Telephone To Face To Face Order' : 'Create Duplicate (F2F)') : 'Edit Interpreter Booking';?> <?=$is_shift && $row['is_shifted'] == 1 ? '<br><label class="label label-danger">This order # ' . $shift_from_id . ' was initially booked as remote (telephone or teams or zoom) changed to Face to Face' : ''?></h3></b>
                <?php if (!$is_shift || ($is_shift && $row['is_shifted'] == 0)) {?>
                    <button id="btn_confirm" class="btn btn-warning pull-right hidden" style="border-color: #000000;color: black;text-transform: uppercase;margin: 13px 34px;font-size: 20px;font-weight: bold;box-shadow: 2px 2px 2px #c5c5a3;" type="button" name="btn_confirm" onclick="if( ($('#po_req').is(':required') && $('#po_req').val()) || (!$('#po_req').is(':required') && $('#inchEmail').val()) ){$('#po_confirm_modal').modal('show');}else{ if($('#po_req').is(':required') && !$('#po_req').val()){alert('You must enter purchase order email!');$('#po_req').focus();}else{$('#btn_confirm').addClass('hidden');$('#btn_submit').removeClass('hidden');}}">Confirm Job &raquo;</button>
                    <button id="btn_submit" class="btn btn-info pull-right hidden" style="border-color: #000000;color: black;text-transform: uppercase;margin: 13px 34px;font-size: 20px;font-weight: bold;box-shadow: 2px 2px 2px #c5c5a3;" type="submit" name="submit" onclick="return confirm('Are you sure to submit this booking?');"><?= $duplicate == 'yes' ? ($is_shift ? 'Shift To F2F' : 'Duplicate Job') : 'Edit Job';?> &raquo;</button>
                    <button class="btn btn-warning pull-right" style="border-color: #000000;color: black;text-transform: uppercase;margin: 13px 34px;font-size: 20px;font-weight: bold;box-shadow: 2px 2px 2px #c5c5a3;" type="button" name="btn_compare" id="btn_compare">Check Duplicates &raquo;</button>
                <?php } ?>
            </div><br><br><br><br>
            <div class="bg-info col-xs-12 form-group">
                <h4>BOOKING DETAILS</h4>
            </div>
            <div class="form-group col-md-3 col-sm-6">
                <label>Source Language * </label>
                <select name="source" id="source" required='' class="form-control">
                    <option disabled value="">Select Source Language</option>
                    <?php
                    $get_languages = $acttObj->read_all("lang,language_type", "lang", "1 ORDER BY lang ASC");
                    while ($row_language = $get_languages->fetch_assoc()) {
                        $selected_source = $row['source'] == $row_language["lang"] ? "selected" : "";
                        echo "<option ".$selected_source." data-type='" . $row_language['language_type'] . "' value='" . $row_language["lang"] . "'>" . $row_language["lang"] . "</option>";
                    } ?>
                </select>
            </div>
            <div class="form-group col-md-3 col-sm-6">
                <label>Target Language * </label>
                <select name="target" id="target" required='' class="form-control">
                    <option disabled value="">Select Target Language</option>
                    <?php
                    $get_languages = $acttObj->read_all("lang,language_type", "lang", "1 ORDER BY lang ASC");
                    while ($row_language = $get_languages->fetch_assoc()) {
                        $selected_target = $row['target'] == $row_language["lang"] ? "selected" : "";
                        echo "<option ".$selected_target." data-type='" . $row_language['language_type'] . "' value='" . $row_language["lang"] . "'>" . $row_language["lang"] . "</option>";
                    } ?>
                </select>
            </div>
            <div class="form-group col-md-3 col-sm-6" id="div_ic">
                <label>Select Assignment Category</label>
                <select name="interp_cat" id="interp_cat" class="form-control" onchange="get_interp_type($(this));" required>
                    <option <?=isset($interp_cat) ? "disabled" : "disabled selected"?> value="">Select Assignment Category</option>
                    <?php
                    $q_interp_cat = $acttObj->read_all("ic_id,ic_title", "interp_cat", "ic_status=1 ORDER BY ic_title ASC");
                    while ($row_ic = $q_interp_cat->fetch_assoc()) {
                        $ic_id = $row_ic["ic_id"];
                        $ic_title = $row_ic["ic_title"];
                        $selected_ic = $interp_cat == $ic_id ? "selected" : "";
                        echo "<option $selected_ic value='$ic_id'>" . $ic_title . "</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="form-group col-md-3 col-sm-6" id="div_it" <?=$interp_cat == '12' ? "style='display:none;'" : ""?>>
                <label>Select Assignment Type(s)</label>
                <select name="interp_type[]" multiple="multiple" id="interp_type" class="form-control multi_class" <?=$interp_cat != '12' ? "required" : ""?>>
                    <?php $q_it = $acttObj->read_all('it_id,it_title', 'interp_types', "ic_id='$interp_cat' ORDER BY it_title ASC");
                    $array_interp_type = explode(',', $interp_type);
                    while ($row_it = $q_it->fetch_assoc()) {
                        $selected_interp_type = in_array($row_it['it_id'], $array_interp_type) ? "selected" : "";
                        echo '<option ' . $selected_interp_type . ' value="' . $row_it['it_id'] . '">' . $row_it['it_title'] . '</option>';
                    } ?>
                </select>
            </div>
            <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
            <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.13/css/bootstrap-multiselect.css" rel="stylesheet" type="text/css" />
            <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.13/js/bootstrap-multiselect.js" type="text/javascript"></script>
            <link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
            <script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>
            <script>
                $(function() {
                    $('.multi_class').multiselect({
                        includeSelectAllOption: true,
                        numberDisplayed: 1,
                        enableFiltering: true,
                        enableCaseInsensitiveFiltering: true
                    });
                });

                function get_interp_type(elem) {
                    var ic_id = elem.val();
                    $.ajax({
                        url: 'ajax_add_interp_data.php',
                        method: 'post',
                        data: {
                            ic_id: ic_id
                        },
                        success: function(data) {
                            if (data) {
                                $('#div_it').css('display', 'block');
                                $('#div_assignIssue').css('display', 'none');
                                $('#assignIssue').css('display', 'none');
                                $('#div_it').html(data);
                            } else {
                                $('#div_it').html(data);
                                $('#div_it').css('display', 'none');
                                $('#div_assignIssue').css('display', 'block');
                                $('#assignIssue').css('display', 'block');
                            }
                            $('.multi_class').multiselect({
                                includeSelectAllOption: true,
                                numberDisplayed: 1,
                                enableFiltering: true,
                                enableCaseInsensitiveFiltering: true
                            });
                        },
                        error: function(xhr) {
                            alert("An error occured: " + xhr.status + " " + xhr.statusText);
                        }
                    });
                }
            </script>
            <div class="form-group col-md-3 col-sm-6 hidden">
                <label>LSUK Booking Reference * </label>
                <input name="nameRef" type="text" value="<?php echo $nameRef; ?>" required='' readonly="readonly" class="form-control" />
            </div>
            <div class="form-group col-sm-9" id="div_assignIssue" <?=$interp_cat != '12' ? "style='display:none;'" : ""?>>
                <textarea title="Assignment Issue" placeholder="Write Assignment Issue Here ..." name="assignIssue" class="form-control" id="assignIssue"><?php echo $assignIssue; ?></textarea>
            </div>
            <div class="bg-info col-xs-12 form-group">
                <h4>ASSIGNMENT LOCATION</h4>
            </div>
            <div class="form-group col-md-4 col-sm-6">
                <label class="optional">Post Code
                </label>
                <div class="input-group">
                    <input id="postCode" class="form-control" name="postCode" type="text" placeholder="Search Post Code" class="form-control" value="<?php echo $postCode ?>">
                    <div class="input-group-btn">
                        <button onclick="return PostCodeChanged();" class="btn btn-success">Look Up Postcode</button>
                    </div>
                </div>
            </div>
            <div class="form-group col-md-4 col-sm-6">
                <label>Building No / Name</label>
                <div class="input-group">
                    <input placeholder="Building No / Name" data-toggle="popover" data-trigger="hover" data-placement="top" style="text-decoration:none;" data-content="For Business Addresses Enter Organisation Name" id="buildingName" name="buildingName" class="form-control" readonly="readonly" type="text" value="<?php echo $buildingName ?>">
                    <div class="input-group-btn">
                        <button onclick="EditStreet();" type="button" class="btn btn-info">Edit Street</button>
                    </div>
                </div>
            </div>
            <div class="form-group col-md-4 col-sm-6">
                <label class="optional">Street / Road / Area</label>
                <input type="hidden" class="form-control" id="postcode_data" value="<?php echo $row['postcode_data'] ?>" />
                <select class="form-control" onchange="return PostCodeListChanged(this);" id="postcodelist"></select>
            </div>
            <div class="form-group col-md-4 col-sm-6">
                <label class="optional">City</label>
                <select name="assignCity" class="form-control">
                    <?php
                    $sql_opt = "SELECT city FROM cities ORDER BY city ASC";
                    $result_opt = mysqli_query($con, $sql_opt);
                    $options = "";
                    while ($row_opt = mysqli_fetch_assoc($result_opt)) {
                        $code = $row_opt["city"];
                        $name_opt = $row_opt["city"];
                        $options .= "<OPTION value='$code'>" . $name_opt;
                    }
                    ?>
                    <?php if (!empty($assignCity)) { ?>
                        <option><?php echo $assignCity; ?></option>
                    <?php } else { ?>
                        <option value="">--Select City--</option>
                    <?php } ?>
                    <?php echo $options; ?>
                    </option>
                </select>
            </div>
            <div class="form-group col-sm-8">
                <label class="optional">Street Details</label>
                <textarea id="street" name="street" rows="2" placeholder="Street Details" type="text" class="form-control"><?php echo $street ?></textarea>
            </div>
            <div class="bg-info col-xs-12 form-group">
                <h4>BOOKING ORGANIZATION DETAILS</h4>
            </div>
            <div class="form-group col-md-4 col-sm-6">
                <label>Company / Team / Unit Name*</label>
                <select onchange="new_company(this)" id="orgName" name="orgName" class="form-control multi_class">
                    <?php
                    $get_companies = $acttObj->read_all("comp_reg.id,comp_reg.name,comp_reg.abrv,comp_reg.status,comp_type.company_type_id", "comp_reg,comp_type", "comp_reg.type_id=comp_type.id AND comp_reg.comp_nature!=1 AND comp_reg.status <> 'Company Seized trading in' and comp_reg.status <> 'Company Blacklisted' ORDER BY comp_reg.name ASC");
                    while ($row_company = $get_companies->fetch_assoc()) {
                        $selected_company = $row['order_company_id'] == $row_company["id"] || $orgName == $row_company['abrv'] ? "selected" : "";
                        echo "<option ".$selected_company." data-id='" . $row_company["id"] . "' data-type-id='" . $row_company["company_type_id"] . "' value='" . $row_company['abrv'] . "'>" . $row_company['name'] . "<span style='color:#F00;'>(" . $row_company["status"] . ")</span></option>";
                    }
                    ?>
                </select>
                <input type="hidden" name="order_company_id" id="order_company_id" value="<?= $row['order_company_id'] ?>" />
                <label class="new_company <?php echo $orgName == 'LSUK_Private Client' ? '' : 'hidden' ?>" style="margin-top: 12px;"><input onchange="new_company_fields(this)" name="new_company_checkbox" class="new_company_checkbox" type="checkbox" value="1" <?php echo $new_company_id != 0 ? 'checked' : '' ?>> Register as new company</label>
            </div>
            <?php TestCode::LoadHtml("joblistcreditlimit.html"); ?>
            <div class="form-group col-md-3 col-sm-6 search-box">
                <label class="optional">Client Booking Ref/Name</label>
                <input value="<?php echo $orgRef ?>" name="orgRef" id="orgRef" type="text" required='' class="form-control" autocomplete="off" placeholder="Search Org Reference" />
                <i id="confirm_orgRef" style="display:none;position: absolute;right: 25px;top: 35px;" onclick="$(this).hide();$(this).next('.result').empty();" class="btn btn-info btn-sm glyphicon glyphicon-ok-sign text-success confirm_element" title="Confirm this reference"></i>
                <div class="result"></div>
            </div>
            <div class="form-group col-md-3 col-sm-6">
                <label>Contact Name&nbsp;* </label>
                <input name="orgContact" id="orgContact" type="text" value="<?php echo $orgContact ?>" placeholder='' class="form-control" required='' />
            </div>
            <div class="bg-info col-xs-12 form-group">
                <h4>BOOKING PERSON DETAILS</h4>
            </div>
            <!--Purchase order on off-->
            <div class="form-group col-md-4 col-sm-6 <?=$po_req == 0 ? 'hidden' : ''?>" id="div_check_po">
                <label>Do you have purchase order number?</label>
                <br><span class="col-md-offset-2">
                    <label class="checkbox-inline" style="margin-top: 4px;border: 1px solid lightgrey;padding: 2px 10px;"><input <?=$po_req == 1 && !empty($porder) ? 'checked' : '';?> style="transform: scale(1.2);" onchange="booking_purch_order();" type="radio" name="po_number" value="1"> Yes</label>
                    <label class="checkbox-inline" style="border: 1px solid lightgrey;padding: 2px 10px;"><input <?=$po_req == 1 && empty($porder) ? 'checked' : '';?> style="transform: scale(1.2);" onchange="booking_purch_order();" type="radio" name="po_number" value="0"> No</label>
                </span>
            </div>
            <div class="form-group col-md-4 col-sm-6 <?=($po_req == 0) || ($po_req == 1 && empty($porder)) ? 'hidden' : '';?> search-box" id="div_po_number">
                <label class="optional">Enter purchase order number</label>
                <input name="purchase_order_number" id="purchase_order_number" type="text" class="form-control" autocomplete="off" placeholder="Search purchase order number" <?php if ($po_req == 1 && !empty($porder)) { ?> value="<?php echo $porder; ?>" <?php } ?> />
                <i id="confirm_po" style="display:none;position: absolute;right: 15px;top: 26px;" onclick="$(this).hide();$(this).next('.result').empty();" class="btn btn-info btn-sm glyphicon glyphicon-ok-sign text-success confirm_element" title="Confirm this purchase order number"></i>
                <div class="result"></div>
            </div>
            <div id="div_po_req" class="form-group <?=($po_req == 0) || ($po_req == 1 && !empty($porder)) ? 'hidden' : '';?> col-md-4 col-sm-6">
                <label>Purchase Order Email Address </label>
                <input oninput="$('#write_po_email').html($(this).val());if($(this).val()){$('.tr_po_email').removeClass('hidden');}" name="po_req" id="po_req" type="text" class="long form-control" placeholder='Fill email for purchase order' <?php if ($po_req == 1 && $porder == "") { ?>required value="<?php echo $porder_email; ?>" data-value="<?php echo $porder_email; ?>" <?php } ?> />
            </div>
            <div class="row"></div>
            <div class="form-group col-md-4 col-sm-6 div_new_company <?php echo $new_company_id == 0 ? 'hidden' : '' ?>">
                <label class="optional"> Company Name </label>
                <input name="new_company_name" id="new_company_name" type="text" class="form-control" value="<?php echo $private_company_name ?>" data-value="<?php echo $private_company_name ?>" />
            </div>
            <div class="form-group col-md-4 col-sm-6">
                <label class="optional"> Booking Person Name </label>
                <input name="inchPerson" id="inchPerson" type="text" class="form-control long" value="<?php echo $inchPerson ?>" data-value="<?php echo $inchPerson ?>" />
            </div>
            <div class="form-group col-md-4 col-sm-6">
                <label class="optional">Contact Number&nbsp;</label>
                <input name="inchContact" id="inchContact" type="text" class="long form-control" value="<?php echo $inchContact ?>" data-value="<?php echo $inchContact ?>" />
            </div>
            <div class="form-group col-md-4 col-sm-6">
                <label class="optional">Booking Confirmation Email #1</label>
                <input oninput="$('#write_booking_email').html($(this).val());" name="inchEmail" id="inchEmail" type="text" value="<?php echo $inchEmail ?>" data-value="<?php echo $inchEmail ?>" class="long form-control" />
            </div>
            <div class="form-group col-md-4 col-sm-6">
                <label class="optional">Booking Confirmation Email #2</label>
                <input name="inchEmail2" id="inchEmail2" type="text" value="<?php echo $inchEmail2 ?>" data-value="<?php echo $inchEmail2 ?>" placeholder='' class="long form-control" />
            </div>
            <!--<div id="div_po_req" class="form-group col-md-4 col-sm-6 <?=$po_req == 0 ?  'hidden' : ''; ?>">
            <label>Purchase Order Email Address </label>
            <input oninput="$('#write_po_email').html($(this).val());" name="po_req" id="po_req" type="text" class="long form-control bg-success" placeholder='Fill email for purchase order' <?php if ($po_req == 1) { ?>required value="<?php echo $porder_email; ?>" data-value="<?php echo $porder_email; ?>" <?php } ?> style="background: #4bc14b7d;font-weight: bold;"/>
            </div>-->
            <div class="form-group col-md-4 col-sm-6">
                <label class="optional">Building Number / Name</label>
                <input name="inchNo" id="inchNo" type="text" value="<?php echo $inchNo ?>" data-value="<?php echo $inchNo ?>" placeholder='' class="form-control" />
            </div>
            <div class="form-group col-md-4 col-sm-6">
                <label class="optional">Address Line 1 </label>
                <input name="line1" id="line1" type="text" class="form-control" placeholder='' value="<?php echo $line1 ?>" data-value="<?php echo $line1 ?>" />
            </div>
            <div class="form-group col-md-4 col-sm-6">
                <label class="optional">Address Line 2 </label>
                <input name="line2" id="line2" class="form-control" type="text" placeholder='' value="<?php echo $line2 ?>" data-value="<?php echo $line2 ?>" />
            </div>
            <div class="form-group col-md-4 col-sm-6">
                <label class="optional">Address Line 3</label>
                <input name="inchRoad" class="form-control" id="inchRoad" type="text" value="<?php echo $inchRoad ?>" data-value="<?php echo $inchRoad ?>" placeholder='' readonly="readonly" />
            </div>
            <div class="form-group col-md-4 col-sm-6">
                <label class="optional">City</label>
                <select name="inchCity" id="inchCity" required class="form-control">
                    <?php
                    $sql_opt = "SELECT city FROM cities ORDER BY city ASC";
                    $result_opt = mysqli_query($con, $sql_opt);
                    $options = "";
                    while ($row_opt = mysqli_fetch_assoc($result_opt)) {
                        $code = $row_opt["city"];
                        $name_opt = $row_opt["city"];
                        $options .= "<OPTION value='$code'>" . $name_opt;
                    }
                    ?>
                    <?php if (!empty($inchCity)) { ?>
                        <option><?php echo $inchCity; ?></option>
                    <?php } else { ?>
                        <option value="">--Select City--</option>
                    <?php } ?>
                    <?php echo $options; ?>
                    </option>
                </select>
            </div>
            <div class="form-group col-md-4 col-sm-6">
                <label class="optional">Post Code</label>
                <input name="inchPcode" class="form-control" id="inchPcode" type="text" value="<?php echo $inchPcode ?>" data-value="<?php echo $inchPcode ?>" />
            </div>
            <div id="po_confirm_modal" class="modal fade" role="dialog" style="margin-top: 90px;">
                <div class="modal-dialog modal-md">
                    <div class="modal-content">
                        <div class="modal-body text-center">
                            <h4>Emails confirmation for this booking</h4>
                            <table class="table table-bordered">
                                <tr>
                                    <td>Booking Email</td>
                                    <td><b id="write_booking_email"><?php echo $row['inchEmail']; ?></b></td>
                                    <td><a onclick="$('#po_confirm_modal').modal('hide');$('#inchEmail').focus();$(this).removeClass('btn-info');$(this).addClass('btn-default');" href="javascript:void(0)" class="btn btn-info btn-sm"><i class="fa fa-remove-circle"></i>Update</a></td>
                                </tr>
                                <tr class="tr_po_email <?=$po_req == 0 ? 'hidden' : ''; ?>">
                                    <td>Purchase Order Email</td>
                                    <td><b id="write_po_email"><?php echo $row['porder_email']; ?></b></td>
                                    <td><a onclick="$('#po_confirm_modal').modal('hide');$('#po_req').focus();$(this).removeClass('btn-info');$(this).addClass('btn-default');" href="javascript:void(0)" class="btn btn-info btn-sm"><i class="fa fa-remove-circle"></i>Update</a></td>
                                </tr>
                            </table>
                            <p class="text-left"><span class="text-danger"><b>Important Note: </b></span><br>These emails will be used to send invoice reminders & purchase order requests to the client. So make sure that entered emails are correct. Click on <u>Update Now button</u> if you want to change these emails.</p>
                            <a onclick="$('#po_confirm_modal').modal('hide');$('#btn_confirm').addClass('hidden');$('#btn_submit').removeClass('hidden');" href="javascript:void(0)" class="btn btn-primary"><i class="fa fa-check-circle"></i>Yes</a>
                            <a onclick="$('#po_confirm_modal').modal('hide');$('#po_req').focus();" href="javascript:void(0)" class="btn btn-default"><i class="fa fa-remove-circle"></i>Update Now</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-12"></div>
            <div class="form-group col-sm-6">
                <label><small>Notes for Interpreter</small></label>
                <textarea name="remrks" id="remrks" rows="3" class="form-control"><?php echo !$duplicate ? $remrks : ""; ?></textarea>
                <input name="job_note" type="checkbox" value="1" /> Check to save as Job Note ?
            </div>
            </div>
            <div class="form-group col-sm-6">
                <label><small>Notes for Client</small></label>
                <textarea name="I_Comments" class="form-control" rows="3" id="I_Comments"><?php echo !$duplicate ? $I_Comments : "" ?></textarea>
                <input name="job_note_c" type="checkbox" value="1" /> Check to save as Job Note ?
            </div>
            <div class="bg-info col-xs-12 form-group">
                <h4>INTERPRETER DETAILS</h4>
            </div>
            <div class="form-group col-md-3 col-sm-6">
                <label>Assignment Date *</label>
                <input id="assignDate" type="date" name="assignDate" required='' class="form-control" value='<?php echo $assignDate; ?>' />
            </div>
            <?php include "jobtimeduration.php";
                include 'jobformbookedvia.php';
            ?>
            <div class="form-group col-md-4 col-sm-6">
                <input type="hidden" name="company_rate_id" id="company_rate_id" class="form-control" value="<?=$company_rate_id?>"/>
                <input type="hidden" name="company_rate_data" id="company_rate_data" class="form-control" value='<?=json_encode($company_rate_data)?>'/>
                <?php $selected_rate_title = !empty($company_rate_data['title']) ? $company_rate_data['title'] : $bookinType; ?>
                <label class="optional">Booking Type </label>
                <select name="bookinType" id="bookinType" class="form-control">
                    <option data-rate='<?=json_encode($company_rate_data)?>' value='<?=$company_rate_id?>'><?=$selected_rate_title?></option>
                </select>
            </div>
            <div class="form-group col-md-3 col-sm-6">
                <label class="optional">Gender</label>
                <select name="gender" id="gender" required class="form-control">
                    <option><?php echo $gender; ?></option>
                    <option value="">--Select--</option>
                    <option>Male</option>
                    <option>Female</option>
                    <option>No Preference</option>
                </select>
            </div>
            <div class="form-group col-md-3 col-sm-6 text-center">
                <label class="optional">STATUS: </label><br>
                <div class="radio-inline ri"><label><input name="jobStatus" type="radio" value="0" <?php if ($jobStatus == '0') { ?> checked="checked" <?php } ?> />
                        <span class="label label-danger">Enquiry <i class="fa fa-question"></i></span></label></div>
                <div class="radio-inline ri"><label><input type="radio" name="jobStatus" value="1" <?php if ($jobStatus == '1') { ?> checked="checked" <?php } ?> />
                        <span class="label label-success">Confirmed <i class="fa fa-check-circle"></i></span></label></div>
            </div>
            <div class="form-group col-md-3 col-sm-6 text-center">
                <label class="optional">DBS checked ?</label><br>
                <div class="radio-inline ri"><label><input name="dbs_checked" type="radio" value="0" <?php if ($dbs_checked == '0') { ?> checked="checked" <?php } ?> />
                        <span class="label label-success">Yes <i class="fa fa-check-circle"></i></span></label></div>
                <div class="radio-inline ri"><label><input type="radio" name="dbs_checked" value="1" <?php if ($dbs_checked == '1') { ?> checked="checked" <?php } ?> />
                        <span class="label label-danger">No <i class="fa fa-remove"></i></span></label></div>
            </div>
            <div class="form-group col-md-3 col-sm-6 text-center" style="margin-top: 2px;">
                <label class="optional">SEND AUTO REMINDER ?</label><br>
                <div class="radio-inline ri" onclick="disabler(1);"><label><input name="jobDisp" type="radio" value="1" <?php if ($jobDisp == '1') { ?> checked="checked" <?php } ?> />
                        <span class="label label-success" style="font-size:100%;padding: .5em 0.6em 0.5em;">Yes <i class="fa fa-check-circle"></i></span></label></div>
                <div class="radio-inline ri" onclick="disabler(0);"><label><input type="radio" name="jobDisp" value="0" <?php if ($jobDisp == '0') { ?> checked="checked" <?php } ?> />
                        <span class="label label-danger" style="font-size:100%;padding: .5em 0.6em 0.5em;">No <i class="fa fa-remove"></i></span></label></div>
            </div>
            <div id="div_selector" class="form-group col-md-3 col-sm-6 selector <?=$jobDisp == 0 ? 'hidden' : ''; ?>">
                <label>Send reminders to</label>
                <select id="selector" onchange="changable()" class="form-control" name="selector">
                    <option value='all'>All Interpreters</option>
                    <option value='sc' <?=!empty($noty) ? 'selected' : '';?>>Specific Interpreters</option>
                </select>
            </div>
            <div id="div_selector_reason" class="t form-group col-md-3 col-sm-6 <?=empty($noty_reason) || $jobDisp == '0' ? 'hidden' : '';?>">
                <label>Reason For Specific Selection</label>
                <select id="selector_reason" class="form-control" name="selector_reason">
                    <?php if (!empty($noty_reason)) { ?><option value='<?php echo $noty_reason; ?>' selected><?php echo $noty_reason; ?></option><?php } ?>
                    <option value='' disabled <?=empty($noty_reason) ? 'selected' : '';?>> --- Choose Reason --- </option>
                    <option value='Regular Job'>Regular Job</option>
                    <option value='Requested Job'>Requested Job</option>
                    <option value='Other'>Other</option>
                </select>
            </div>
            <div id="div_specific" class="form-group col-md-3 col-sm-6 <?=empty($noty) || $jobDisp == 0 ? 'hidden' : '';?>">
                <label class="optional" sttyle="display:block;">Selected Interpreters</label>
                <select class="multi_class" id="selected_interpreters" name="selected_interpreters[]" multiple="multiple">
                    <?php if (!empty($noty)) {
                        $res_noty = $acttObj->read_all("id,name,gender,city", "interpreter_reg", "id IN ($noty)");
                        while ($row_noty = mysqli_fetch_assoc($res_noty)) { ?>
                            <option selected value="<?php echo $row_noty['id']; ?>"><?php echo $row_noty['name'] . ' (' . $row_noty['gender'] . ')' . ' (' . $row_noty['city'] . ')'; ?></option>
                    <?php }
                    } ?>
                    <?php if (!empty($noty)) {
                        $append_noty = "AND id NOT IN ($noty)";
                    }
                    $append_dbs = isset($dbs_checked) && !empty($dbs_checked) && $dbs_checked == 0 ? 'AND interpreter_reg.dbs_checked=0' : '';
                    if ($gender == '' || $gender == 'No Preference') {
                        $append_gender = "";
                    } else {
                        $append_gender = "AND interpreter_reg.gender='$gender'";
                    }
                    if ($source == $target) {
                        $append_lang = "";
                        $q_style = '0';
                    } else if ($source != 'English' && $target != 'English') {
                        $append_lang = "";
                        $q_style = '1';
                    } else if ($source == 'English' && $target != 'English') {
                        $append_lang = "interp_lang.lang='$target' and interp_lang.level<3";
                        $q_style = '2';
                    } else if ($source != 'English' && $target == 'English') {
                        $append_lang = "interp_lang.lang='$source' and interp_lang.level<3";
                        $q_style = '2';
                    } else {
                        $append_lang = "";
                        $q_style = '3';
                    }
                    if ($q_style == '0') {
                        $query_ints = "SELECT DISTINCT interpreter_reg.id,interpreter_reg.name,interpreter_reg.gender,interpreter_reg.city FROM interpreter_reg,interp_lang WHERE interpreter_reg.code=interp_lang.code AND (SELECT COUNT(DISTINCT interp_lang.lang) FROM interp_lang WHERE interp_lang.type='interp' AND interp_lang.lang IN ('" . $source . "') and interp_lang.level<3 and interp_lang.code=interpreter_reg.code)=1 and 
                        ((interpreter_reg.uk_citizen=1 AND interpreter_reg.id_doc_expiry_date != '1001-01-01' AND interpreter_reg.id_doc_expiry_date > CURRENT_DATE()) OR (interpreter_reg.uk_citizen=0 AND interpreter_reg.work_evid_expiry_date != '1001-01-01' AND interpreter_reg.work_evid_expiry_date > CURRENT_DATE())) 
                                    AND (interpreter_reg.is_dbs_auto=1 OR (interpreter_reg.is_dbs_auto=0 AND interpreter_reg.dbs_expiry_date != '1001-01-01' AND interpreter_reg.dbs_expiry_date > CURRENT_DATE())) and 
                        interpreter_reg.active='0' AND (interpreter_reg.actnow='Active' OR (interpreter_reg.actnow='Inactive' AND CURRENT_DATE() NOT BETWEEN interpreter_reg.actnow_time AND interpreter_reg.actnow_to)) AND interpreter_reg.interp='Yes' $append_dbs $append_gender AND interpreter_reg.deleted_flag=0 AND interpreter_reg.subscribe=1 AND interpreter_reg.is_temp=0 AND interpreter_reg.isAdhoc=0 $append_noty ORDER BY name ASC";
                    } else if ($q_style == '1') {
                        $query_ints = "SELECT DISTINCT interpreter_reg.id,interpreter_reg.name,interpreter_reg.gender,interpreter_reg.city FROM interpreter_reg,interp_lang WHERE interpreter_reg.code=interp_lang.code AND (SELECT COUNT(DISTINCT interp_lang.lang) FROM interp_lang WHERE interp_lang.type='interp' AND interp_lang.lang IN ('" . $source . "','" . $target . "') and interp_lang.level<3 and interp_lang.code=interpreter_reg.code)=2 and 
                        ((interpreter_reg.uk_citizen=1 AND interpreter_reg.id_doc_expiry_date != '1001-01-01' AND interpreter_reg.id_doc_expiry_date > CURRENT_DATE()) OR (interpreter_reg.uk_citizen=0 AND interpreter_reg.work_evid_expiry_date != '1001-01-01' AND interpreter_reg.work_evid_expiry_date > CURRENT_DATE())) 
                                    AND (interpreter_reg.is_dbs_auto=1 OR (interpreter_reg.is_dbs_auto=0 AND interpreter_reg.dbs_expiry_date != '1001-01-01' AND interpreter_reg.dbs_expiry_date > CURRENT_DATE())) and 
                        interpreter_reg.active='0' AND (interpreter_reg.actnow='Active' OR (interpreter_reg.actnow='Inactive' AND CURRENT_DATE() NOT BETWEEN interpreter_reg.actnow_time AND interpreter_reg.actnow_to)) AND interpreter_reg.interp='Yes' $append_dbs $append_gender AND interpreter_reg.deleted_flag=0 AND interpreter_reg.subscribe=1 AND interpreter_reg.is_temp=0 AND interpreter_reg.isAdhoc=0 $append_noty ORDER BY name ASC";
                    } else if ($q_style == '2') {
                        $query_ints = "SELECT DISTINCT interpreter_reg.id,interpreter_reg.name,interpreter_reg.gender,interpreter_reg.city FROM interpreter_reg,interp_lang WHERE interpreter_reg.code=interp_lang.code AND interp_lang.type='interp' AND $append_lang and 
                        ((interpreter_reg.uk_citizen=1 AND interpreter_reg.id_doc_expiry_date != '1001-01-01' AND interpreter_reg.id_doc_expiry_date > CURRENT_DATE()) OR (interpreter_reg.uk_citizen=0 AND interpreter_reg.work_evid_expiry_date != '1001-01-01' AND interpreter_reg.work_evid_expiry_date > CURRENT_DATE())) 
                                    AND (interpreter_reg.is_dbs_auto=1 OR (interpreter_reg.is_dbs_auto=0 AND interpreter_reg.dbs_expiry_date != '1001-01-01' AND interpreter_reg.dbs_expiry_date > CURRENT_DATE())) and 
                        interpreter_reg.active='0' AND (interpreter_reg.actnow='Active' OR (interpreter_reg.actnow='Inactive' AND CURRENT_DATE() NOT BETWEEN interpreter_reg.actnow_time AND interpreter_reg.actnow_to)) AND interpreter_reg.interp='Yes' $append_dbs $append_gender AND interpreter_reg.deleted_flag=0 AND interpreter_reg.subscribe=1 AND interpreter_reg.is_temp=0 AND interpreter_reg.isAdhoc=0 $append_noty ORDER BY name ASC";
                    } else {
                        $query_ints = "SELECT DISTINCT interpreter_reg.id,interpreter_reg.name,interpreter_reg.gender,interpreter_reg.city FROM interpreter_reg WHERE 
                        interpreter_reg.active='0' and ((interpreter_reg.uk_citizen=1 AND interpreter_reg.id_doc_expiry_date != '1001-01-01' AND interpreter_reg.id_doc_expiry_date > CURRENT_DATE()) OR (interpreter_reg.uk_citizen=0 AND interpreter_reg.work_evid_expiry_date != '1001-01-01' AND interpreter_reg.work_evid_expiry_date > CURRENT_DATE())) AND (interpreter_reg.is_dbs_auto=1 OR (interpreter_reg.is_dbs_auto=0 AND interpreter_reg.dbs_expiry_date != '1001-01-01' AND interpreter_reg.dbs_expiry_date > CURRENT_DATE())) AND (interpreter_reg.actnow='Active' OR (interpreter_reg.actnow='Inactive' AND CURRENT_DATE() NOT BETWEEN interpreter_reg.actnow_time AND interpreter_reg.actnow_to)) AND interpreter_reg.interp='Yes' $append_dbs $append_gender AND interpreter_reg.deleted_flag=0 AND interpreter_reg.subscribe=1 AND interpreter_reg.is_temp=0 AND interpreter_reg.isAdhoc=0 $append_noty ORDER BY name ASC";
                    }
                    $res_ints = mysqli_query($con, $query_ints);
                    while ($row_ints = mysqli_fetch_assoc($res_ints)) { ?>
                        <option value="<?php echo $row_ints['id']; ?>"><?php echo $row_ints['name'] . ' (' . $row_ints['gender'] . ')' . ' (' . $row_ints['city'] . ')'; ?></option>
                    <?php } ?>
                </select>
            </div>
        <div class="form-group col-xs-12"><br><br><br></div>
        </form>
        <!-- Duplication Records Modal -->
        <div class="modal fade" id="compare_modal" tabindex="-1" role="dialog" aria-labelledby="compare_modalLabel" aria-hidden="true">
            <div class="modal-dialog" style="width:80%;" role="document">
                <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="compare_modalLabel">Duplication Check</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="compare_modal_body" >
                    ...
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" data-dismiss="modal" id="proceed_bk">Proceed Anyway</button>
                </div>
                </div>
            </div>
        </div>
        <script type="text/javascript">
            g_strJobTableIs = "<?php echo $table; ?>";
        </script>
        <script type="text/javascript" src="ajax.js"></script>
        <script src="ckeditor/ckeditor/ckeditor.js"></script>
        <script type="text/javascript">
            CKEDITOR.replace('remrks', {
                height: '150px',
            });
            CKEDITOR.replace('I_Comments', {
                height: '150px',
            });
        </script>
        <?php
        if (isset($_POST['submit']) && $duplicate == 'yes') {
            $nmbr = $acttObj->get_id('invoice');
            if ($nmbr == NULL) {
                $nmbr = 0;
            }
            $new_nmbr = str_pad($nmbr, 5, "0", STR_PAD_LEFT);
            $invoice_new = date("my") . $new_nmbr;
            $maxId = $nmbr;
            $acttObj->editFun('invoice', $maxId, 'invoiceNo', $invoice_new);
            $acttObj->editFun($table, $edit_id, 'invoiceNo', $invoice_new);
            $acttObj->editFun($table, $edit_id, 'submited', ucwords($_SESSION['UserName']));
            //Email notification to related interpreters
            $jobDisp_req = $_POST['jobDisp'];
            $jobStatus_req = $_POST['jobStatus'];
            if ($jobDisp_req == '1' && $jobStatus_req == '1' && $_SESSION['Temp'] == 0) {
                $source_lang_req = $_POST['source'];
                $dbs_checked_req = isset($_POST['dbs_checked']) && !empty($_POST['dbs_checked']) && $_POST['dbs_checked'] == 0 ? 'AND interpreter_reg.dbs_checked=0' : '';
                $assignCity_name = explode(',', $_POST['assignCity']);
                $assignCity_req = $assignCity_name[0];
                $assignDate_req = $misc->dated($_POST['assignDate']);
                $assignTime_req = $_POST['assignTime'];
                $duration = $acttObj->read_specific("assignDur,guess_dur", "$table", "id=" . $edit_id);
                $total_dur = $duration["assignDur"];
                $total_guess_dur = $duration["guess_dur"];
                if ($total_dur > 60) {
                    $hours = $total_dur / 60;
                    if (floor($hours) > 1) {
                        $hr = "hours";
                    } else {
                        $hr = "hour";
                    }
                    $mins = $total_dur % 60;
                    if ($mins == 00) {
                        $get_dur = sprintf("%2d $hr", $hours);
                    } else {
                        $get_dur = sprintf("%2d $hr %02d minutes", $hours, $mins);
                    }
                } else if ($total_dur == 60) {
                    $get_dur = "1 Hour";
                } else {
                    $get_dur = $total_dur . " minutes";
                }
                if ($total_dur != $total_guess_dur) {
                    if ($total_guess_dur > 60) {
                        $guess_hours = $total_guess_dur / 60;
                        if (floor($guess_hours) > 1) {
                            $guess_hr = "hours";
                        } else {
                            $guess_hr = "hour";
                        }
                        $guess_mins = $total_guess_dur % 60;
                        if ($guess_mins == 0) {
                            $get_guess_dur = sprintf("%2d $guess_hr", $guess_hours);
                        } else {
                            $get_guess_dur = sprintf("%2d $guess_hr %02d minutes", $guess_hours, $guess_mins);
                        }
                    } else if ($total_guess_dur == 60) {
                        $get_guess_dur = "1 Hour";
                    } else {
                        $get_guess_dur = $total_guess_dur . " minutes";
                    }
                }
                $postCode_req = $_POST['postCode'];
                $gender_req = $_POST['gender'];
                $target_lang_req = $_POST['target'];
                $write_interp_cat = $c_interp_cat == '12' ? $c_assignIssue : $acttObj->read_specific("ic_title", "interp_cat", "ic_id=" . $c_interp_cat)['ic_title'];
                $write_interp_type = $c_interp_cat == '12' ? '' : $acttObj->read_specific("GROUP_CONCAT(CONCAT(it_title)  SEPARATOR ' <b> & </b> ') as it_title", "interp_types", "it_id IN (" . $c_interp_type . ")")['it_title'];
                if ($c_interp_cat == '12') {
                    $append_issue = "<tr><td style='border: 1px solid yellowgreen;padding:5px;'>Assignment Category</td><td style='border: 1px solid yellowgreen;padding:5px;'>Other</td></tr><tr><td style='border: 1px solid yellowgreen;padding:5px;'>Assignment Details</td><td style='border: 1px solid yellowgreen;padding:5px;'>" . $c_assignIssue . "</td></tr>";
                } else {
                    $append_issue = "<tr><td style='border: 1px solid yellowgreen;padding:5px;'>Assignment Category</td><td style='border: 1px solid yellowgreen;padding:5px;'>" . $write_interp_cat . "</td></tr><tr><td style='border: 1px solid yellowgreen;padding:5px;'>Assignment Details</td><td style='border: 1px solid yellowgreen;padding:5px;'>" . $write_interp_type . "</td></tr>";
                }
                $c_remrks = $c_remrks ?: '';
                $append_table = "
                    <table>
                    <tr>
                    <td style='border: 1px solid yellowgreen;padding:5px;'>Source Language</td>
                    <td style='border: 1px solid yellowgreen;padding:5px;'>" . $source_lang_req . "</td>
                    </tr>
                    <tr>
                    <td style='border: 1px solid yellowgreen;padding:5px;'>Target Language</td>
                    <td style='border: 1px solid yellowgreen;padding:5px;'>" . $target_lang_req . "</td>
                    </tr>
                    <tr>
                    <td style='border: 1px solid yellowgreen;padding:5px;'>Assignment Date</td>
                    <td style='border: 1px solid yellowgreen;padding:5px;'>" . $assignDate_req . "</td>
                    </tr>
                    <tr>
                    <td style='border: 1px solid yellowgreen;padding:5px;'>Assignment Time</td>
                    <td style='border: 1px solid yellowgreen;padding:5px;'>" . $assignTime_req . "</td>
                    </tr>
                    <tr>
                    <td style='border: 1px solid yellowgreen;padding:5px;'>Assignment Duration</td>
                    <td style='border: 1px solid yellowgreen;padding:5px;'>" . $get_dur . "</td>
                    </tr>
                    <tr>
                    <td style='border: 1px solid yellowgreen;padding:5px;'>Assignment Location</td>
                    <td style='border: 1px solid yellowgreen;padding:5px;'>To be informed after successful allocation</td>
                    </tr>
                    " . $append_issue . "
                    <tr>
                    <td style='border: 1px solid yellowgreen;padding:5px;'>Report to</td>
                    <td style='border: 1px solid yellowgreen;padding:5px;'>" . $c_inchPerson . "</td>
                    </tr>
                    <tr>
                    <td style='border: 1px solid yellowgreen;padding:5px;'>Case Worker or Person Incharge</td>
                    <td style='border: 1px solid yellowgreen;padding:5px;'>" . $c_orgContact . "</td>
                    </tr>
                    <tr>
                    <td style='border: 1px solid yellowgreen;padding:5px;'>Client Name</td>
                    <td style='border: 1px solid yellowgreen;padding:5px;'>" . $c_orgRef . "</td>
                    </tr>
                    </table>";
                if ($total_dur != $total_guess_dur) {
                    $append_table .= "<br><u><b>NOTES FOR THIS JOB:</b></u><br>
                    This session is booked for " . $get_dur . ", however it can take  up to " . $get_guess_dur . " or longer.<br>
                    Therefore please consider your unrestricted availability before bidding / accepting this job. In cases of short notice cancellation, you will be paid the booked time (" . $get_dur . ").<br>";
                    if (!empty($c_remrks)) {
                        $append_table .= $c_remrks . "<br>";
                    }
                } else {
                    if (!empty($c_remrks)) {
                        $append_table .= "<br><u><b>NOTES FOR THIS JOB:</b></u><br>" . $c_remrks . "<br>";
                    }
                }
                if ($gender_req == '' || $gender_req == 'No Preference') {
                    $put_gender = "";
                } else {
                    $put_gender = "AND interpreter_reg.gender='$gender_req'";
                }
                if ($source_lang_req == $target_lang_req) {
                    $put_lang = "";
                    $query_style = '0';
                } else if ($source_lang_req != 'English' && $target_lang_req != 'English') {
                    $put_lang = "";
                    $query_style = '1';
                } else if ($source_lang_req == 'English' && $target_lang_req != 'English') {
                    $put_lang = "interp_lang.lang='$target_lang_req' and interp_lang.level<3";
                    $query_style = '2';
                } else if ($source_lang_req != 'English' && $target_lang_req == 'English') {
                    $put_lang = "interp_lang.lang='$source_lang_req' and interp_lang.level<3";
                    $query_style = '2';
                } else {
                    $put_lang = "";
                    $query_style = '3';
                }
                if ($query_style == '0') {
                    $query_emails = "SELECT DISTINCT interpreter_reg.name, interpreter_reg.email, interpreter_reg.id FROM interpreter_reg,interp_lang WHERE interpreter_reg.code=interp_lang.code AND (SELECT COUNT(DISTINCT interp_lang.lang) FROM interp_lang WHERE interp_lang.type='interp' AND interp_lang.lang IN ('" . $source_lang_req . "') and interp_lang.level<3 and interp_lang.code=interpreter_reg.code)=1 and 
                    ((interpreter_reg.uk_citizen=1 AND interpreter_reg.id_doc_expiry_date != '1001-01-01' AND interpreter_reg.id_doc_expiry_date > CURRENT_DATE()) OR (interpreter_reg.uk_citizen=0 AND interpreter_reg.work_evid_expiry_date != '1001-01-01' AND interpreter_reg.work_evid_expiry_date > CURRENT_DATE())) 
                                AND (interpreter_reg.is_dbs_auto=1 OR (interpreter_reg.is_dbs_auto=0 AND interpreter_reg.dbs_expiry_date != '1001-01-01' AND interpreter_reg.dbs_expiry_date > CURRENT_DATE())) and 
                    interpreter_reg.active='0' AND (interpreter_reg.actnow='Active' OR (interpreter_reg.actnow='Inactive' AND CURRENT_DATE() NOT BETWEEN interpreter_reg.actnow_time AND interpreter_reg.actnow_to)) AND interpreter_reg.interp='Yes' $dbs_checked_req AND interpreter_reg.city LIKE '$assignCity_req' $put_gender AND interpreter_reg.deleted_flag=0 AND interpreter_reg.subscribe=1 AND interpreter_reg.is_temp=0 AND interpreter_reg.isAdhoc=0";
                } else if ($query_style == '1') {
                    $query_emails = "SELECT DISTINCT interpreter_reg.name, interpreter_reg.email, interpreter_reg.id FROM interpreter_reg,interp_lang WHERE interpreter_reg.code=interp_lang.code AND (SELECT COUNT(DISTINCT interp_lang.lang) FROM interp_lang WHERE interp_lang.type='interp' AND interp_lang.lang IN ('" . $source_lang_req . "','" . $target_lang_req . "') and interp_lang.level<3 and interp_lang.code=interpreter_reg.code)=2 and 
                    ((interpreter_reg.uk_citizen=1 AND interpreter_reg.id_doc_expiry_date != '1001-01-01' AND interpreter_reg.id_doc_expiry_date > CURRENT_DATE()) OR (interpreter_reg.uk_citizen=0 AND interpreter_reg.work_evid_expiry_date != '1001-01-01' AND interpreter_reg.work_evid_expiry_date > CURRENT_DATE())) 
                                AND (interpreter_reg.is_dbs_auto=1 OR (interpreter_reg.is_dbs_auto=0 AND interpreter_reg.dbs_expiry_date != '1001-01-01' AND interpreter_reg.dbs_expiry_date > CURRENT_DATE())) and 
                    interpreter_reg.active='0' AND (interpreter_reg.actnow='Active' OR (interpreter_reg.actnow='Inactive' AND CURRENT_DATE() NOT BETWEEN interpreter_reg.actnow_time AND interpreter_reg.actnow_to)) AND interpreter_reg.interp='Yes' $dbs_checked_req AND interpreter_reg.city LIKE '$assignCity_req' $put_gender AND interpreter_reg.deleted_flag=0 AND interpreter_reg.subscribe=1 AND interpreter_reg.is_temp=0 AND interpreter_reg.isAdhoc=0";
                } else if ($query_style == '2') {
                    $query_emails = "SELECT DISTINCT interpreter_reg.name, interpreter_reg.email, interpreter_reg.id FROM interpreter_reg,interp_lang WHERE interpreter_reg.code=interp_lang.code AND interp_lang.type='interp' AND $put_lang and 
                    ((interpreter_reg.uk_citizen=1 AND interpreter_reg.id_doc_expiry_date != '1001-01-01' AND interpreter_reg.id_doc_expiry_date > CURRENT_DATE()) OR (interpreter_reg.uk_citizen=0 AND interpreter_reg.work_evid_expiry_date != '1001-01-01' AND interpreter_reg.work_evid_expiry_date > CURRENT_DATE())) 
                                AND (interpreter_reg.is_dbs_auto=1 OR (interpreter_reg.is_dbs_auto=0 AND interpreter_reg.dbs_expiry_date != '1001-01-01' AND interpreter_reg.dbs_expiry_date > CURRENT_DATE())) and 
                    interpreter_reg.active='0' AND (interpreter_reg.actnow='Active' OR (interpreter_reg.actnow='Inactive' AND CURRENT_DATE() NOT BETWEEN interpreter_reg.actnow_time AND interpreter_reg.actnow_to)) AND interpreter_reg.interp='Yes' $dbs_checked_req AND interpreter_reg.city LIKE '$assignCity_req' $put_gender AND interpreter_reg.deleted_flag=0 AND interpreter_reg.subscribe=1 AND interpreter_reg.is_temp=0 AND interpreter_reg.isAdhoc=0";
                } else {
                    $query_emails = "SELECT DISTINCT interpreter_reg.name, interpreter_reg.email, interpreter_reg.id FROM interpreter_reg WHERE 
                    interpreter_reg.active='0' and ((interpreter_reg.uk_citizen=1 AND interpreter_reg.id_doc_expiry_date != '1001-01-01' AND interpreter_reg.id_doc_expiry_date > CURRENT_DATE()) OR (interpreter_reg.uk_citizen=0 AND interpreter_reg.work_evid_expiry_date != '1001-01-01' AND interpreter_reg.work_evid_expiry_date > CURRENT_DATE())) AND (interpreter_reg.is_dbs_auto=1 OR (interpreter_reg.is_dbs_auto=0 AND interpreter_reg.dbs_expiry_date != '1001-01-01' AND interpreter_reg.dbs_expiry_date > CURRENT_DATE())) AND (interpreter_reg.actnow='Active' OR (interpreter_reg.actnow='Inactive' AND CURRENT_DATE() NOT BETWEEN interpreter_reg.actnow_time AND interpreter_reg.actnow_to)) AND interpreter_reg.interp='Yes' $dbs_checked_req AND interpreter_reg.city LIKE '$assignCity_req' $put_gender AND interpreter_reg.deleted_flag=0 AND interpreter_reg.subscribe=1 AND interpreter_reg.is_temp=0 AND interpreter_reg.isAdhoc=0";
                }
                if ($_POST['selector'] == 'sc') {
                    $selected_interpreters = implode(',', $_POST['selected_interpreters']);
                    $query_emails .= ' and interpreter_reg.id IN (' . $selected_interpreters . ')';
                }
                $res_emails = mysqli_query($con, $query_emails);
                //Getting bidding email from em_format table
                $row_format = $acttObj->read_specific("em_format", "email_format", "id=28");
                $subject = "Bidding Invitation For Face To Face Project " . $edit_id;
                $sub_title = "New Face To Face job of " . $source_lang_req . " language is available for you to bid.";
                $type_key = "nj";
                while ($row_emails = mysqli_fetch_assoc($res_emails)) {
                    if ($acttObj->read_specific("COUNT(*) as blacklisted", "interp_blacklist", "interpName='id-" . $row_emails['id'] . "' AND orgName='" . $_POST['orgName'] . "' AND deleted_flag=0 AND blocked_for=1")["blacklisted"] == 0) {
                        $to_address = $row_emails['email'];
                        //Send notification on APP
                        $check_id = $acttObj->read_specific('id', 'notify_new_doc', 'interpreter_id=' . $row_emails['id'])['id'];
                        if (empty($check_id)) {
                            $acttObj->insert('notify_new_doc', array("interpreter_id" => $row_emails['id'], "status" => '1'));
                        } else {
                            $existing_notification = $acttObj->read_specific("new_notification", "notify_new_doc", "interpreter_id=" . $row_emails['id'])['new_notification'];
                            $acttObj->update('notify_new_doc', array("new_notification" => $existing_notification + 1), array("interpreter_id" => $row_emails['id']));
                        }
                        $array_tokens = explode(',', $acttObj->read_specific("GROUP_CONCAT( DISTINCT token) as tokens", "int_tokens", "int_id=" . $row_emails['id'])['tokens']);
                        if (!empty($array_tokens)) {
                            $acttObj->insert('app_notifications', array("title" => $subject, "sub_title" => $sub_title, "dated" => date('Y-m-d'), "int_ids" => $row_emails['id'], "read_ids" => $row_emails['id'], "type_key" => $type_key));
                            foreach ($array_tokens as $token) {
                                if (!empty($token)) {
                                    $acttObj->notify($token, $subject, $sub_title, array("type_key" => $type_key, "job_type" => "Face To Face"));
                                }
                            }
                        }
                        //Replace date in email bidding
                        $data   = ["[NAME]", "[ASSIGNTIME]", "[ASSIGNDATE]", "[POSTCODE]", "[TABLE]", "[EDIT_ID]"];
                        $to_replace  = [$row_emails['name'], "$assignTime_req", "$assignDate_req", "$postCode_req", "$append_table", "$edit_id"];
                        $message = str_replace($data, $to_replace, $row_format['em_format']);
                        try {
                            $mail->SMTPDebug = 0;
                            $mail->isSMTP();
                            $mail->Host = setupEmail::EMAIL_HOST;
                            $mail->SMTPAuth   = true;
                            $mail->Username   = setupEmail::INFO_EMAIL;
                            $mail->Password   = setupEmail::INFO_PASSWORD;
                            $mail->SMTPSecure = setupEmail::SECURE_TYPE;
                            $mail->Port       = setupEmail::SENDING_PORT;
                            $mail->setFrom(setupEmail::INFO_EMAIL, setupEmail::FROM_NAME);
                            $mail->addAddress($to_address);
                            $mail->addReplyTo(setupEmail::INFO_EMAIL, setupEmail::FROM_NAME);
                            $mail->isHTML(true);
                            $mail->Subject = $subject;
                            $mail->Body    = $message;
                            $mail->send();
                            $mail->ClearAllRecipients();
                        } catch (Exception $e) { ?>
                            <script>
                                alert("Message could not be sent! Mailer library error.");
                            </script>
                    <?php }
                    }
                }
            } ?>
            <script type="text/javascript">
                alert("<?=$is_shift ? 'Job Shifted To Face To Face Successfully!' : 'Job Submitted Successfully!'?>");
                window.close();
                window.onunload = refreshParent;
            </script>
            <?php
            $acttObj->insert("daily_logs", array("action_id" => 1, "user_id" => $_SESSION['userId'], "details" => "F2F Job ID: " . $edit_id));
        }

        if (isset($_POST['submit'])) {
            if ($duplicate != 'yes') {
                if (empty($invoiceNo)) {
                    $acttObj->UpdateInvoiceNo($invoiceNo, $table, $edit_id);
                }
                //Email notification to related interpreters
                $jobDisp_req = $_POST['jobDisp'];
                $gender_req = $_POST['gender'];
                $gender_check = $acttObj->unique_data('interpreter', 'gender', 'id', $edit_id);
                $jobStatus_req = $_POST['jobStatus'];
                $jobStatus_check = $acttObj->unique_data('interpreter', 'jobStatus', 'id', $edit_id);
                if ($_POST['selector'] == 'sc' && !empty($_POST['selected_interpreters'])) {
                    $noty_intz = implode(',', $_POST['selected_interpreters']);
                    $result_noty = array_equal_values([$noty], [$noty_intz]);
                    if (!$result_noty) {
                        $noty_diff = 1;
                    } else {
                        $noty_diff = 0;
                    }
                }
                if ($_POST['selector'] == 'all' && !empty($noty)) {
                    $noty_diff = 1;
                }
                if ($_POST['selector'] == 'sc' && empty($noty)) {
                    $noty_diff = 1;
                }
                if ($_SESSION['Temp'] == 0 && ($jobDisp_req == '1' && $is_temp == '0') && (($jobStatus_check != $jobStatus_req && $jobStatus_req == '1') || ($gender_check != $gender_req && $jobStatus_req == '1')) || ($noty_diff == 1 && $jobStatus_req == '1')) {
                    $source_lang_req = $_POST['source'];
                    $dbs_checked_req = isset($_POST['dbs_checked']) && !empty($_POST['dbs_checked']) && $_POST['dbs_checked'] == 0 ? 'AND interpreter_reg.dbs_checked=0' : '';
                    $assignCity_name = explode(',', $_POST['assignCity']);
                    $assignCity_req = $assignCity_name[0];
                    $assignDate_req = $misc->dated($_POST['assignDate']);
                    $assignTime_req = $_POST['assignTime'];
                    $duration = $acttObj->read_specific("assignDur,guess_dur", "$table", "id=" . $edit_id);
                    $total_dur = $duration["assignDur"];
                    $total_guess_dur = $duration["guess_dur"];
                    if ($total_dur > 60) {
                        $hours = $total_dur / 60;
                        if (floor($hours) > 1) {
                            $hr = "hours";
                        } else {
                            $hr = "hour";
                        }
                        $mins = $total_dur % 60;
                        if ($mins == 00) {
                            $get_dur = sprintf("%2d $hr", $hours);
                        } else {
                            $get_dur = sprintf("%2d $hr %02d minutes", $hours, $mins);
                        }
                    } else if ($total_dur == 60) {
                        $get_dur = "1 Hour";
                    } else {
                        $get_dur = $total_dur . " minutes";
                    }
                    if ($total_dur != $total_guess_dur) {
                        if ($total_guess_dur > 60) {
                            $guess_hours = $total_guess_dur / 60;
                            if (floor($guess_hours) > 1) {
                                $guess_hr = "hours";
                            } else {
                                $guess_hr = "hour";
                            }
                            $guess_mins = $total_guess_dur % 60;
                            if ($guess_mins == 0) {
                                $get_guess_dur = sprintf("%2d $guess_hr", $guess_hours);
                            } else {
                                $get_guess_dur = sprintf("%2d $guess_hr %02d minutes", $guess_hours, $guess_mins);
                            }
                        } else if ($total_guess_dur == 60) {
                            $get_guess_dur = "1 Hour";
                        } else {
                            $get_guess_dur = $total_guess_dur . " minutes";
                        }
                    }
                    $postCode_req = $_POST['postCode'];
                    $target_lang_req = $_POST['target'];
                    $write_interp_cat = $c_interp_cat == '12' ? $c_assignIssue : $acttObj->read_specific("ic_title", "interp_cat", "ic_id=" . $c_interp_cat)['ic_title'];
                    $write_interp_type = $c_interp_cat == '12' ? '' : $acttObj->read_specific("GROUP_CONCAT(CONCAT(it_title)  SEPARATOR ' <b> & </b> ') as it_title", "interp_types", "it_id IN (" . $c_interp_type . ")")['it_title'];
                    if ($c_interp_cat == '12') {
                        $append_issue = "<tr><td style='border: 1px solid yellowgreen;padding:5px;'>Assignment Category</td><td style='border: 1px solid yellowgreen;padding:5px;'>Other</td></tr><tr><td style='border: 1px solid yellowgreen;padding:5px;'>Assignment Details</td><td style='border: 1px solid yellowgreen;padding:5px;'>" . $c_assignIssue . "</td></tr>";
                    } else {
                        $append_issue = "<tr><td style='border: 1px solid yellowgreen;padding:5px;'>Assignment Category</td><td style='border: 1px solid yellowgreen;padding:5px;'>" . $write_interp_cat . "</td></tr><tr><td style='border: 1px solid yellowgreen;padding:5px;'>Assignment Details</td><td style='border: 1px solid yellowgreen;padding:5px;'>" . $write_interp_type . "</td></tr>";
                    }
                    $c_remrks = $c_remrks ?: '';
                    $append_table = "
                        <table>
                        <tr>
                        <td style='border: 1px solid yellowgreen;padding:5px;'>Source Language</td>
                        <td style='border: 1px solid yellowgreen;padding:5px;'>" . $source_lang_req . "</td>
                        </tr>
                        <tr>
                        <td style='border: 1px solid yellowgreen;padding:5px;'>Target Language</td>
                        <td style='border: 1px solid yellowgreen;padding:5px;'>" . $target_lang_req . "</td>
                        </tr>
                        <tr>
                        <td style='border: 1px solid yellowgreen;padding:5px;'>Assignment Date</td>
                        <td style='border: 1px solid yellowgreen;padding:5px;'>" . $assignDate_req . "</td>
                        </tr>
                        <tr>
                        <td style='border: 1px solid yellowgreen;padding:5px;'>Assignment Time</td>
                        <td style='border: 1px solid yellowgreen;padding:5px;'>" . $assignTime_req . "</td>
                        </tr>
                        <tr>
                        <td style='border: 1px solid yellowgreen;padding:5px;'>Assignment Duration</td>
                        <td style='border: 1px solid yellowgreen;padding:5px;'>" . $get_dur . "</td>
                        </tr>
                        <tr>
                        <td style='border: 1px solid yellowgreen;padding:5px;'>Assignment Location</td>
                        <td style='border: 1px solid yellowgreen;padding:5px;'>To be informed after successful allocation</td>
                        </tr>
                        " . $append_issue . "
                        <tr>
                        <td style='border: 1px solid yellowgreen;padding:5px;'>Report to</td>
                        <td style='border: 1px solid yellowgreen;padding:5px;'>" . $c_inchPerson . "</td>
                        </tr>
                        <tr>
                        <td style='border: 1px solid yellowgreen;padding:5px;'>Case Worker or Person Incharge</td>
                        <td style='border: 1px solid yellowgreen;padding:5px;'>" . $c_orgContact . "</td>
                        </tr>
                        <tr>
                        <td style='border: 1px solid yellowgreen;padding:5px;'>Client Name</td>
                        <td style='border: 1px solid yellowgreen;padding:5px;'>" . $c_orgRef . "</td>
                        </tr>
                        </table>";
                    if ($total_dur != $total_guess_dur) {
                        $append_table .= "<br><u><b>NOTES FOR THIS JOB:</b></u><br>
                        This session is booked for " . $get_dur . ", however it can take  up to " . $get_guess_dur . " or longer.<br>
                        Therefore please consider your unrestricted availability before bidding / accepting this job. In cases of short notice cancellation, you will be paid the booked time (" . $get_dur . ").<br>";
                        if (!empty($c_remrks)) {
                            $append_table .= $c_remrks . "<br>";
                        }
                    } else {
                        if (!empty($c_remrks)) {
                            $append_table .= "<br><u><b>NOTES FOR THIS JOB:</b></u><br>" . $c_remrks . "<br>";
                        }
                    }
                    if ($gender_req == '' || $gender_req == 'No Preference') {
                        $put_gender = "";
                    } else {
                        $put_gender = "AND interpreter_reg.gender='$gender_req'";
                    }
                    if ($source_lang_req == $target_lang_req) {
                        $put_lang = "";
                        $query_style = '0';
                    } else if ($source_lang_req != 'English' && $target_lang_req != 'English') {
                        $put_lang = "";
                        $query_style = '1';
                    } else if ($source_lang_req == 'English' && $target_lang_req != 'English') {
                        $put_lang = "interp_lang.lang='$target_lang_req' and interp_lang.level<3";
                        $query_style = '2';
                    } else if ($source_lang_req != 'English' && $target_lang_req == 'English') {
                        $put_lang = "interp_lang.lang='$source_lang_req' and interp_lang.level<3";
                        $query_style = '2';
                    } else {
                        $put_lang = "";
                        $query_style = '3';
                    }
                    if ($query_style == '0') {
                        $query_emails = "SELECT DISTINCT interpreter_reg.name, interpreter_reg.email, interpreter_reg.id FROM interpreter_reg,interp_lang WHERE interpreter_reg.code=interp_lang.code AND (SELECT COUNT(DISTINCT interp_lang.lang) FROM interp_lang WHERE interp_lang.type='interp' AND interp_lang.lang IN ('" . $source_lang_req . "') and interp_lang.level<3 and interp_lang.code=interpreter_reg.code)=1 and 
                        ((interpreter_reg.uk_citizen=1 AND interpreter_reg.id_doc_expiry_date != '1001-01-01' AND interpreter_reg.id_doc_expiry_date > CURRENT_DATE()) OR (interpreter_reg.uk_citizen=0 AND interpreter_reg.work_evid_expiry_date != '1001-01-01' AND interpreter_reg.work_evid_expiry_date > CURRENT_DATE())) 
                                    AND (interpreter_reg.is_dbs_auto=1 OR (interpreter_reg.is_dbs_auto=0 AND interpreter_reg.dbs_expiry_date != '1001-01-01' AND interpreter_reg.dbs_expiry_date > CURRENT_DATE())) and 
                        interpreter_reg.active='0' AND (interpreter_reg.actnow='Active' OR (interpreter_reg.actnow='Inactive' AND CURRENT_DATE() NOT BETWEEN interpreter_reg.actnow_time AND interpreter_reg.actnow_to)) AND interpreter_reg.interp='Yes' $dbs_checked_req AND interpreter_reg.city LIKE '$assignCity_req' $put_gender AND interpreter_reg.deleted_flag=0 AND interpreter_reg.subscribe=1 AND interpreter_reg.is_temp=0 AND interpreter_reg.isAdhoc=0";
                    } else if ($query_style == '1') {
                        $query_emails = "SELECT DISTINCT interpreter_reg.name, interpreter_reg.email, interpreter_reg.id FROM interpreter_reg,interp_lang WHERE interpreter_reg.code=interp_lang.code AND (SELECT COUNT(DISTINCT interp_lang.lang) FROM interp_lang WHERE interp_lang.type='interp' AND interp_lang.lang IN ('" . $source_lang_req . "','" . $target_lang_req . "') and interp_lang.level<3 and interp_lang.code=interpreter_reg.code)=2 and 
                        ((interpreter_reg.uk_citizen=1 AND interpreter_reg.id_doc_expiry_date != '1001-01-01' AND interpreter_reg.id_doc_expiry_date > CURRENT_DATE()) OR (interpreter_reg.uk_citizen=0 AND interpreter_reg.work_evid_expiry_date != '1001-01-01' AND interpreter_reg.work_evid_expiry_date > CURRENT_DATE())) 
                                    AND (interpreter_reg.is_dbs_auto=1 OR (interpreter_reg.is_dbs_auto=0 AND interpreter_reg.dbs_expiry_date != '1001-01-01' AND interpreter_reg.dbs_expiry_date > CURRENT_DATE())) and 
                        interpreter_reg.active='0' AND (interpreter_reg.actnow='Active' OR (interpreter_reg.actnow='Inactive' AND CURRENT_DATE() NOT BETWEEN interpreter_reg.actnow_time AND interpreter_reg.actnow_to)) AND interpreter_reg.interp='Yes' $dbs_checked_req AND interpreter_reg.city LIKE '$assignCity_req' $put_gender AND interpreter_reg.deleted_flag=0 AND interpreter_reg.subscribe=1 AND interpreter_reg.is_temp=0 AND interpreter_reg.isAdhoc=0";
                    } else if ($query_style == '2') {
                        $query_emails = "SELECT DISTINCT interpreter_reg.name, interpreter_reg.email, interpreter_reg.id FROM interpreter_reg,interp_lang WHERE interpreter_reg.code=interp_lang.code AND interp_lang.type='interp' AND $put_lang and 
                        ((interpreter_reg.uk_citizen=1 AND interpreter_reg.id_doc_expiry_date != '1001-01-01' AND interpreter_reg.id_doc_expiry_date > CURRENT_DATE()) OR (interpreter_reg.uk_citizen=0 AND interpreter_reg.work_evid_expiry_date != '1001-01-01' AND interpreter_reg.work_evid_expiry_date > CURRENT_DATE())) 
                                    AND (interpreter_reg.is_dbs_auto=1 OR (interpreter_reg.is_dbs_auto=0 AND interpreter_reg.dbs_expiry_date != '1001-01-01' AND interpreter_reg.dbs_expiry_date > CURRENT_DATE())) and 
                        interpreter_reg.active='0' AND (interpreter_reg.actnow='Active' OR (interpreter_reg.actnow='Inactive' AND CURRENT_DATE() NOT BETWEEN interpreter_reg.actnow_time AND interpreter_reg.actnow_to)) AND interpreter_reg.interp='Yes' $dbs_checked_req AND interpreter_reg.city LIKE '$assignCity_req' $put_gender AND interpreter_reg.deleted_flag=0 AND interpreter_reg.subscribe=1 AND interpreter_reg.is_temp=0 AND interpreter_reg.isAdhoc=0";
                    } else {
                        $query_emails = "SELECT DISTINCT interpreter_reg.name, interpreter_reg.email, interpreter_reg.id FROM interpreter_reg WHERE 
                        interpreter_reg.active='0' and ((interpreter_reg.uk_citizen=1 AND interpreter_reg.id_doc_expiry_date != '1001-01-01' AND interpreter_reg.id_doc_expiry_date > CURRENT_DATE()) OR (interpreter_reg.uk_citizen=0 AND interpreter_reg.work_evid_expiry_date != '1001-01-01' AND interpreter_reg.work_evid_expiry_date > CURRENT_DATE())) AND (interpreter_reg.is_dbs_auto=1 OR (interpreter_reg.is_dbs_auto=0 AND interpreter_reg.dbs_expiry_date != '1001-01-01' AND interpreter_reg.dbs_expiry_date > CURRENT_DATE())) AND (interpreter_reg.actnow='Active' OR (interpreter_reg.actnow='Inactive' AND CURRENT_DATE() NOT BETWEEN interpreter_reg.actnow_time AND interpreter_reg.actnow_to)) AND interpreter_reg.interp='Yes' $dbs_checked_req AND interpreter_reg.city LIKE '$assignCity_req' $put_gender AND interpreter_reg.deleted_flag=0 AND interpreter_reg.subscribe=1 AND interpreter_reg.is_temp=0 AND interpreter_reg.isAdhoc=0";
                    }
                    if ($_POST['selector'] == 'sc') {
                        if (!empty($noty)) {
                            $sppend_noty = ' and interpreter_reg.id NOT IN (' . $noty . ')';
                        }
                        $selected_interpreters = implode(',', $_POST['selected_interpreters']);
                        $query_emails .= ' and interpreter_reg.id IN (' . $selected_interpreters . ') ' . $sppend_noty;
                    }
                    if ($_POST['selector'] == 'all') {
                        if (!empty($noty)) {
                            $query_emails .= ' and interpreter_reg.id NOT IN (' . $noty . ')';
                        }
                    }
                    if ($_POST['selector'] == 'sc') {
                        if (empty($noty)) {
                            $selected_interpreters = implode(',', $_POST['selected_interpreters']);
                            $query_emails .= ' and interpreter_reg.id IN (' . $selected_interpreters . ') ';
                        }
                    }
                    $res_emails = mysqli_query($con, $query_emails);
                    //Getting bidding email from em_format table
                    $row_format = $acttObj->read_specific("em_format", "email_format", "id=28");
                    $subject = "Bidding Invitation For Face To Face Project " . $edit_id;
                    $sub_title = "New Face To Face job of " . $source_lang_req . " language is available for you to bid.";
                    $type_key = "nj";
                    while ($row_emails = mysqli_fetch_assoc($res_emails)) {
                        if ($acttObj->read_specific("COUNT(*) as blacklisted", "interp_blacklist", "interpName='id-" . $row_emails['id'] . "' AND orgName='" . $_POST['orgName'] . "' AND deleted_flag=0 AND blocked_for=1")["blacklisted"] == 0) {
                            $to_address = $row_emails['email'];
                            //Send notification on APP
                            $check_id = $acttObj->read_specific('id', 'notify_new_doc', 'interpreter_id=' . $row_emails['id'])['id'];
                            if (empty($check_id)) {
                                $acttObj->insert('notify_new_doc', array("interpreter_id" => $row_emails['id'], "status" => '1'));
                            } else {
                                $existing_notification = $acttObj->read_specific("new_notification", "notify_new_doc", "interpreter_id=" . $row_emails['id'])['new_notification'];
                                $acttObj->update('notify_new_doc', array("new_notification" => $existing_notification + 1), array("interpreter_id" => $row_emails['id']));
                            }
                            $array_tokens = explode(',', $acttObj->read_specific("GROUP_CONCAT( DISTINCT token) as tokens", "int_tokens", "int_id=" . $row_emails['id'])['tokens']);
                            if (!empty($array_tokens)) {
                                $acttObj->insert('app_notifications', array("title" => $subject, "sub_title" => $sub_title, "dated" => date('Y-m-d'), "int_ids" => $row_emails['id'], "read_ids" => $row_emails['id'], "type_key" => $type_key));
                                foreach ($array_tokens as $token) {
                                    if (!empty($token)) {
                                        $acttObj->notify($token, $subject, $sub_title, array("type_key" => $type_key, "job_type" => "Face To Face"));
                                    }
                                }
                            }
                            //Replace date in email bidding
                            $data   = ["[NAME]", "[ASSIGNTIME]", "[ASSIGNDATE]", "[POSTCODE]", "[TABLE]", "[EDIT_ID]"];
                            $to_replace  = [$row_emails['name'], "$assignTime_req", "$assignDate_req", "$postCode_req", "$append_table", "$edit_id"];
                            $message = str_replace($data, $to_replace, $row_format['em_format']);
                            try {
                                $mail->SMTPDebug = 0;
                                $mail->isSMTP();
                                $mail->Host = setupEmail::EMAIL_HOST;
                                $mail->SMTPAuth   = true;
                                $mail->Username   = setupEmail::INFO_EMAIL;
                                $mail->Password   = setupEmail::INFO_PASSWORD;
                                $mail->SMTPSecure = setupEmail::SECURE_TYPE;
                                $mail->Port       = setupEmail::SENDING_PORT;
                                $mail->setFrom(setupEmail::INFO_EMAIL, setupEmail::FROM_NAME);
                                $mail->addAddress($to_address);
                                $mail->addReplyTo(setupEmail::INFO_EMAIL, setupEmail::FROM_NAME);
                                $mail->isHTML(true);
                                $mail->Subject = $subject;
                                $mail->Body    = $message;
                                $mail->send();
                                $mail->ClearAllRecipients();
                            } catch (Exception $e) { ?>
                                <script>
                                    alert("Message could not be sent! Mailer library error.");
                                </script>
                        <?php }
                        }
                    }
                } ?>
                <script type="text/javascript">
                    alert('Job Submitted Successfully!');
                    window.close();
                    window.onunload = refreshParent;
                </script>
                <?php
                $acttObj->insert("daily_logs", array("action_id" => 2, "user_id" => $_SESSION['userId'], "details" => "F2F Job ID: " . $edit_id));
            }
            $c22 = $_POST['gender'];
            $acttObj->editFun($table, $edit_id, 'gender', $c22);
            //New history function to record history
            if ($duplicate != 'yes') {
                $acttObj->editFun($table, $edit_id, 'edited_by', $_SESSION['UserName']);
                $acttObj->editFun($table, $edit_id, 'edited_date', date("Y-m-d H:i:s"));
                $index_mapping = array(
                    'Company ID' => 'order_company_id', 'Source Language' => 'source', 'Target Language' => 'target', 'Assignment Date' => 'assignDate', 'Assignment Time' => 'assignTime', 'Job Duration' => 'assignDur', 'Job Info' => 'assignIssue', 'Building Name' => 'buildingName', 'Street' => 'street',
                    'Job City' => 'assignCity', 'Postcode' => 'postCode', 'Postcode Data' => 'postcode_data', 'Incharge Person' => 'inchPerson', 'Incharge Contact' => 'inchContact', 'Incharge Email' => 'inchEmail', 'Incharge No' => 'inchNo', 'Line 1' => 'line1', 'Incharge Road' => 'inchRoad', 'Line 2' => 'line2', 
                    'Incharge City' => 'inchCity', 'Incharge Postcode' => 'inchPcode', 'Company' => 'orgName', 'Organization Reference' => 'orgRef', 'Organization Contact' => 'orgContact', 'Interpreter Notes' => 'remrks', 'Gender' => 'gender', 'Submitted By' => 'submited', 'Job Status' => 'jobStatus', 
                    'Purchase Order' => 'porder', 'Display Job' => 'jobDisp', 'DBS Checked' => 'dbs_checked', 'Client Notes' => 'I_Comments', 'Booked Via' => 'bookedVia', 'Incharge Email 2' => 'inchEmail2',
                    'Booked Date' => 'bookeddate', 'Booked Time' => 'bookedtime', 'Named Booked' => 'namedbooked', 'Purchase Order Email' => 'porder_email', 'Is Temporary' => 'is_temp', 'Assignment Category' => 'interp_cat', 'Assignment Type' => 'interp_type', 'Specific Interpreters' => 'noty', 'Specific Reason' => 'noty_reason', 'Reference ID' => 'reference_id',
                    'Expected Duration' => 'guess_dur', 'New Company ID' => 'new_comp_id', 'Booking Type ID' => 'company_rate_id', 'Booking Type Data' => 'company_rate_data', 'Interpreter Rate ID' => 'interpreter_rate_id', 'Interpreter Rate Data' => 'interpreter_rate_data'
                );
                
                $old_values = array();
                $new_values = array();
                $get_new_data = $acttObj->read_specific("*", "$table", "id=" . $edit_id);
                
                foreach ($index_mapping as $key => $value) {
                    if (isset($get_new_data[$value])) {
                        $old_values[$key] = $row[$value];
                        $new_values[$key] = $get_new_data[$value];
                    }
                }
                $acttObj->log_changes($old_values, $new_values, $edit_id, $table, "update", $_SESSION['userId'], $_SESSION['UserName'], "edit_job_f2f");
            }
            //This needs to be removed soon
            // $acttObj->new_old_table('hist_' . $table, $table, $edit_id);
        } ?>
</body>
<script>
    $(document).ready(function() {
        $('[data-toggle="popover"]').popover();
        $('.search-box input[type="text"]').on("keyup", function() {
            var element = $(this);
            var runtime_action;
            var inputVal = element.val();
            var orgName = $('#orgName').val();
            var resultDropdown = element.siblings(".result");
            if (inputVal.length) {
                if (element.attr('id') == "orgRef") {
                    runtime_action = "orgRef";
                } else {
                    runtime_action = "purchase_order";
                }
                $.get("ajax_add_interp_data.php", {
                    term: inputVal,
                    orgName: orgName,
                    runtime_action: runtime_action
                }).done(function(data) {
                    resultDropdown.html(data);
                    element.next('.confirm_element').show();
                });
            } else {
                resultDropdown.empty();
                element.next('.confirm_element').show();
            }
        });
        $(document).on('click','#btn_compare',function(e){
                var bk_source = $('#source').val();
                var bk_assignDate = $('#assignDate').val();
                var bk_assignTime = $('#assignTime').val();
                var bk_type="interpreter";
                dp_mdl = 0;
                if(bk_source && bk_assignDate && bk_assignTime){
                    dp_mdl = 1;
                    $.post("ajax_add_interp_data.php", {
                        bk_source: bk_source,
                        bk_assignDate: bk_assignDate,
                        bk_assignTime: bk_assignTime,
                        bk_type:bk_type
                },function(data) {
                    var json_data = JSON.parse(data);
                    console.log(json_data['matches']);
                    $('#compare_modal_body').html(json_data['body']);
                    if(json_data['matches']>0){
                        $('#proceed_bk').html('Proceed Anyway');
                        $('#proceed_bk').removeClass('btn-primary');
                        $('#proceed_bk').addClass('btn-danger');
                        $("#compare_modal").modal('show');
                    }else{
                        alert('No Duplicates Found! Proceed to Confirm Job.');
                        $('#proceed_bk').html('Proceed');
                        $('#proceed_bk').removeClass('btn-danger');
                        $('#proceed_bk').addClass('btn-primary');
                        $('#btn_confirm').removeClass('hidden');
                        $('#btn_compare').addClass('hidden');
                    }
                    // $("#compare_modal").modal('show');
                });
            }else{
                alert("Please fill source language, Assignment date and time to check possible duplicates!");
            }
            
        });
        $(document).on('click','#proceed_bk', function(){
            $('#btn_confirm').removeClass('hidden');
        });
        $(document).on("click", ".result p.click", function() {
            var element = $(this);
            element.parents(".search-box").find('input[type="text"]').val(element.text());
            element.parent(".result").empty();
            element.parents('div').prev('.confirm_element').show();
        });
    });

    function booking_purch_order() {
        if ($('input[name="po_number"]:checked').val() == 1) {
            $('.tr_po_email,#div_po_req').addClass('hidden');
            $('#purchase_order_number').attr('required', 'required');
            $('#po_req').removeAttr('required');
            var orgName = $('#orgName').val();
            if (orgName) {
                $('#div_po_number').removeClass('hidden');
            } else {
                $('#div_po_number').addClass('hidden');
            }
        } else {
            $('.tr_po_email,#div_po_req').removeClass('hidden');
            $('#purchase_order_number').removeAttr('required');
            $('#po_req').attr('required', 'required');
            $('#div_po_number').addClass('hidden');
        }
    }

    function disabler(val) {
        var value = val;
        if (value == '1') {
            $('#div_selector').removeClass('hidden');
            if ($('#selector').val() == 'all') {
                $('#div_specific').addClass('hidden');
                $('#div_selector_reason').addClass('hidden');
            } else {
                $('#div_specific').removeClass('hidden');
                $('#div_selector_reason').removeClass('hidden');
            }
        } else {
            $('#div_selector').addClass('hidden');
            $('#div_specific').addClass('hidden');
            $('#div_selector_reason').addClass('hidden');
        }
    }

    function changable() {
        var value = document.getElementById("selector").value;
        if (value == 'all') {
            $('#div_specific').addClass('hidden');
            $('#div_selector_reason').addClass('hidden');
        } else {
            var get_specific = 1;
            var get_type = 'interpreter';
            var source = $('#source').val();
            var target = $('#target').val();
            var dbs_checked = $('input[name=dbs_checked]:checked').val();
            var gender = $('#gender').val();
            var noty_array = [<?php echo $noty; ?>];
            if (!source || !target) {
                alert('Select source & target language first!');
                $("#selector option[value='all']")[0].selected = true;
            } else {
                $('#div_specific').removeClass('hidden');
                $('#div_selector_reason').removeClass('hidden');
                $.ajax({
                    url: 'ajax_add_interp_data.php',
                    method: 'post',
                    data: {
                        get_specific: get_specific,
                        get_type: get_type,
                        source: source,
                        target: target,
                        gender: gender,
                        dbs_checked: dbs_checked
                    },
                    success: function(data) {
                        if (data) {
                            $('#selected_interpreters').html(data);
                            $("#selected_interpreters").multiselect('rebuild');
                            $('#selected_interpreters').multiselect('select', noty_array);
                        }
                    },
                    error: function(xhr) {
                        alert("An error occured: " + xhr.status + " " + xhr.statusText);
                    }
                });
            }
        }
    }
    $(function() {
        $('.multi_class , #selected_interpreters').multiselect({
            includeSelectAllOption: true,
            numberDisplayed: 1,
            enableFiltering: true,
            enableCaseInsensitiveFiltering: true
        });
    });

    function new_company(elem) {
        $('#order_company_id').val($("#orgName option:selected").attr('data-id'));
        var orgName = $(elem).val();
        if (orgName == "LSUK_Private Client") {
            $('.new_company').removeClass('hidden');
        } else {
            $('.new_company').addClass('hidden');
        }
    }

    function find_company_rates() {
        var select = $('select#bookinType');
        var selected_company_id = $("#orgName option:selected").attr('data-id');
        var selected_company_type = $("#orgName option:selected").attr('data-type-id');
        var selected_language_type = $("#source option:selected").attr('data-type');
        var selected_booked_date = $("#bookedDate").val();
        var selected_booked_time = $("#bookedTime").val();
        var selected_assignment_date = $("#assignDate").val();
        var selected_assignment_time = $("#assignTime").val();
        $.ajax({
            url: 'ajax_add_interp_data.php',
            method: 'post',
            dataType: 'json',
            data: {
                find_company_id: selected_company_id,
                find_company_type: selected_company_type,
                find_language_type: selected_language_type,
                find_booked_date: selected_booked_date,
                find_booked_time: selected_booked_time,
                find_assignment_date: selected_assignment_date,
                find_assignment_time: selected_assignment_time,
                find_order_type: 1,
                find_company_rates: 1
            },
            success: function(data) {
                if (data['status'] == 1) {
                    select.empty();
                    if (data.company_rates.length === 1) {
                        $("#company_rate_id").val(data.company_rates[0].id);
                        $("#company_rate_data").val(JSON.stringify(data.company_rates[0]));
                        select.append($("<option data-rate='" + JSON.stringify(data.company_rates[0]) + "'>").attr('value', data.company_rates[0].id).text(data.company_rates[0].title)).val(data.company_rates[0].id);
                    } else {
                        select.append($('<option value="">').attr('value', '').text(" --- Select From List ---"));
                        $.each(data.company_rates, function(index, item) {
                            var style = "";
                            if (item.is_bsl == 1) {
                                style = "style='color:blue'";
                            }
                            if (item.is_rare == 1) {
                                style = "style='color:red'";
                            }
                            var option = $("<option data-rate='" + JSON.stringify(item) + "' " + style + ">").attr('value', item.id).text(item.title);
                            select.append(option);
                        });
                    }
                }
            },
            error: function(xhr) {
                console.log("An error occured: " + xhr.status + " " + xhr.statusText);
            }
        });
    }
    $("#source, #orgName, #assignDate, #assignTime, #bookedDate, #bookedTime").on("change", function() {
        find_company_rates();
    });
    $("#bookinType").on("change", function() {
        $("#company_rate_id").val($(this).val());
        $("#company_rate_data").val($(this).find("option:selected").attr("data-rate"));
    });
    // Incase if booking type is not set at all
    var already_set_booking_type = '<?=$company_rate_id?>';
    if (!already_set_booking_type) {
        find_company_rates();
    }

    function new_company_fields(elem) {
        var old_orgContact = $('#orgContact').attr('data-value');
        var old_inchPerson = $('#inchPerson').attr('data-value');
        var old_inchContact = $('#inchContact').attr('data-value');
        var old_inchEmail = $('#inchEmail').attr('data-value');
        var old_inchEmail2 = $('#inchEmail2').attr('data-value');
        var old_inchNo = $('#inchNo').attr('data-value');
        var old_line1 = $('#line1').attr('data-value');
        var old_line2 = $('#line2').attr('data-value');
        var old_inchRoad = $('#inchRoad').attr('data-value');
        var old_inchPcode = $('#inchPcode').attr('data-value');
        if ($(elem).is(':checked')) {
            //$('#orgContact,#inchPerson,#inchContact,#inchEmail,#inchEmail2,#inchNo,#line1,#line2,#inchRoad,#inchPcode').val('');
            $('#inchNo,#line1,#line2,#inchRoad,#inchPcode,#inchCity').removeAttr('readonly');
            $('.div_new_company').removeClass('hidden');
        } else {
            //$('#orgContact').val(old_orgContact);$('#inchPerson').val(old_inchPerson);$('#inchContact').val(old_inchContact);
            //$('#inchEmail').val(old_inchEmail);$('#inchEmail2').val(old_inchEmail2);$('#inchNo').val(old_inchNo);
            //$('#line1').val(old_line1);$('#line2').val(old_line2);$('#inchRoad').val(old_inchRoad);$('#inchPcode').val(old_inchPcode);
            $('#inchNo,#line1,#line2,#inchRoad,#inchPcode,#inchCity').attr('readonly', 'readonly');
            $('.div_new_company').addClass('hidden');
        }
    }

    function valid_email(element) {
        var expr = /^([\w-\.']+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([\w-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$/;

        if (!expr.test($(element).val())) {
            alert('Kindly enter a valid email!');
            $(element).val("");
            $(element).focus();
        }
    }
    $('#po_req,#inchEmail,#inchEmail2').keyup(function() {
        this.value = this.value.replace(/\s/g, '');
    });
    $("#po_req,#inchEmail,#inchEmail2").change(function() {
        valid_email(this);
    });
</script>
<?php } ?>

</html>