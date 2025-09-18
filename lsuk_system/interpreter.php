<?php
include "userhaspage.php";
SysPermiss::UserHasPage(__FILE__);
include '../source/setup_email.php';
include 'db.php';
include 'class.php';
if (session_id() == '' || !isset($_SESSION)) {
    session_start();
}
$table = 'interpreter';
$is_valid_p_order = 1;
if (isset($_POST['submit'])) {
    // Block if a request is already in progress and started less than 15 seconds ago
    if (!empty($_SESSION['is_proceeding']) && time() - $_SESSION['is_proceeding'] < 15) {
        exit('Duplicate submission blocked. This may be caused by double clicking the submit or network issue. Please check job before adding another booking.');
    }

    // Mark this request as in progress
    $_SESSION['is_proceeding'] = time();
    $v_source = @$_POST['source'];
    $v_assignDate = @$_POST['assignDate'];
    $v_assignTime = @$_POST['assignTime'];
    $v_orgName = @$_POST['orgName'];
    $v_orgContact = @$_POST['orgContact'];
    $v_orgRef = @$_POST['orgRef'];

    $query = "
    SELECT count(id) as val
    FROM $table
    where source='$v_source' and assignDate='$v_assignDate' and assignTime='$v_assignTime' and
      orgName='$v_orgName' and orgContact='$v_orgContact' and orgRef='$v_orgRef'";

    $result = mysqli_query($con, $query);
    while ($row = mysqli_fetch_assoc($result)) {
        $val = $row['val'];
    }

    if ($val == 0) {
        $edit_id = $acttObj->get_id($table);
        //Create & save new reference no
        $reference_no = $acttObj->generate_reference(1, $table, $edit_id);
    } else {
        echo "<script>alert('oops..This job is already booked!');</script>";
        echo '<script type="text/javascript">' . "\n";
        echo 'window.history.back()';
        echo '</script>';
    }
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
    <title>Face To Face Booking Form</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
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

        .p40 {
            padding: 40px !important;
        }

        #ajax_loader {
            visibility: hidden;
            background-color: #ffffff;
            position: absolute;
            width: 7%;
            height: 12%;
            left: 44%;
            top: 10%;
            overflow: hidden;
            z-index: 9999;
            border-radius: 100%;
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
    <div id="ajax_loader"><img src="../images/ajax_loader.gif" width="100" class="img-responsive" /></div>
    <?php include 'ajax_uniq_fun.php'; ?>
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
    <div class="container-fluid">
        <form action="" method="post" class="register">
            <div style="background: #8c8c86;padding: 6px;position: fixed;z-index: 999999999999;width: 100%;margin-top: -10px;color: white;">
                <b>
                    <h4 style="display: inline-block;">Face to Face Booking Form</h4>
                </b>
                <button class="btn btn-warning pull-right hidden" style="border-color: #000000;color: black;text-transform: uppercase;margin: 13px 34px;font-size: 20px;font-weight: bold;box-shadow: 2px 2px 2px #c5c5a3;" type="button" name="btn_confirm" id="btn_confirm" onclick="if( ($('#po_req').is(':required') && $('#po_req').val()) || (!$('#po_req').is(':required') && $('#inchEmail').val()) ){$('#po_confirm_modal').modal('show');}else{ if($('#po_req').is(':required') && !$('#po_req').val()){alert('You must enter purchase order email!');$('#po_req').focus();}else{confirm_job();}}">Confirm Job &raquo;</button>
                <button id="btn_submit" class="btn btn-info pull-right hidden" style="border-color: #000000;color: black;text-transform: uppercase;margin: 13px 34px;font-size: 20px;font-weight: bold;box-shadow: 2px 2px 2px #c5c5a3;" type="submit" name="submit" onclick="return confirm('Are you sure to create this booking?');">Submit Job &raquo;</button>
                <button class="btn btn-warning pull-right" style="border-color: #000000;color: black;text-transform: uppercase;margin: 13px 34px;font-size: 20px;font-weight: bold;box-shadow: 2px 2px 2px #c5c5a3;" type="button" name="btn_compare" id="btn_compare">Check Duplicates &raquo;</button>
            </div><br><br><br><br>
            <div class="bg-info col-xs-12 form-group">
                <h4>BOOKING DETAILS</h4>
            </div>
            <div class="form-group col-md-3 col-sm-6">
                <label>Select Source Language</label>
                <select name="source" id="source" required='' class="form-control">
                    <option disabled selected value="">Select Source Language</option>
                    <?php
                    $get_languages = $acttObj->read_all("lang,language_type", "lang", "1 ORDER BY lang ASC");
                    while ($row_language = $get_languages->fetch_assoc()) {
                        echo "<option data-type='" . $row_language['language_type'] . "' value='" . $row_language["lang"] . "'>" . $row_language["lang"] . "</option>";
                    } ?>
                </select>
            </div>
            <div class="form-group col-md-3 col-sm-6">
                <label>Select Target Language</label>
                <select name="target" id="target" required='' class="form-control">
                    <option disabled value="">Select Target Language</option>
                    <?php
                    $get_languages = $acttObj->read_all("lang,language_type", "lang", "1 ORDER BY lang ASC");
                    while ($row_language = $get_languages->fetch_assoc()) {
                        $selected_target = $row_language["lang"] == "English" ? "selected" : "";
                        echo "<option ".$selected_target." data-type='" . $row_language['language_type'] . "' value='" . $row_language["lang"] . "'>" . $row_language["lang"] . "</option>";
                    } ?>
                </select>
                <?php
                if (isset($_POST['submit'])) {
                    $c1 = $_POST['source'];
                    $acttObj->editFun($table, $edit_id, 'source', $c1);
                }
                ?>
                <?php if (isset($_POST['submit'])) {
                    $c2 = $_POST['target'];
                    $acttObj->editFun($table, $edit_id, 'target', $c2);
                } ?>
            </div>
            <div class="form-group col-md-3 col-sm-6" id="div_tc">
                <label>Select Assignment Category</label>
                <select name="interp_cat" id="interp_cat" class="form-control" onchange="get_interp_type($(this));" required>
                    <?php
                    $q_interp_cat = $acttObj->read_all("ic_id,ic_title", "interp_cat", "ic_status=1 ORDER BY ic_title ASC");
                    $opt_ic = "";
                    while ($row_ic = $q_interp_cat->fetch_assoc()) {
                        $ic_id = $row_ic["ic_id"];
                        $ic_title = $row_ic["ic_title"];
                        $opt_ic .= "<option value='$ic_id'>" . $ic_title . "</option>";
                    }
                    ?>
                    <option disabled selected value="">Select Assignment Category</option>
                    <?php echo $opt_ic; ?>
                </select>
            </div>
            <div class="form-group col-md-3 col-sm-6" id="div_it" style="display:none;">
            </div>
            <?php if (isset($_POST['submit']) && !empty($_POST['interp_cat'])) {
                $interp_cat = $_POST['interp_cat'];
                $acttObj->editFun($table, $edit_id, 'interp_cat', $interp_cat);
            }
            if (isset($_POST['submit']) && $_POST['interp_cat'] != '12') {
                $interp_type = implode(",", $_POST['interp_type']);
                $acttObj->editFun($table, $edit_id, 'interp_type', $interp_type);
            } ?>
            <div class="form-group col-md-3 col-sm-6 hidden">
                <label>LSUK Booking Reference</label>
                <input title="LSUK Booking Reference" class="form-control" name="nameRef" type="text" required='' readonly="readonly" value="<?php $month = date('M');
                                                                                                                                                $month = substr($month, 0, 3);
                                                                                                                                                $lastid = $acttObj->max_id("global_reference_no") + 1;
                                                                                                                                                echo 'LSUK/' . $month . '/' . $lastid; ?>" />
                <?php if (isset($_POST['submit'])) {
                    $c5 = $_POST['assignDur'];
                    $acttObj->editFunTimeAsMins($table, $edit_id, 'assignDur', $c5);
                }
                ?>
                <?php
                if (isset($_POST['submit'])) {
                    $month = substr($month, 0, 3);
                    $lastid = $reference_no;
                    $c6 = 'LSUK/' . $month . '/' . $lastid;
                    $email_ref_No = $c6;
                    $acttObj->editFun($table, $edit_id, 'nameRef', $c6);
                }
                ?>
            </div>
            <div class="form-group col-md-9 col-sm-6" id="div_assignIssue" style="display:none;">
                <textarea style="display:none;" placeholder="Write Assignment Issue Here ..." name="assignIssue" class="form-control" id="assignIssue"></textarea>
                <?php if (isset($_POST['submit']) && !empty($_POST['interp_cat']) && $_POST['interp_cat'] == '12') {
                    $assignIssue = $_POST['assignIssue'];
                    $acttObj->editFun($table, $edit_id, 'assignIssue', $assignIssue);
                }
                ?>
            </div>
            <div class="bg-info col-xs-12 form-group">
                <h4>ASSIGNMENT LOCATION</h4>
            </div>
            <div class="form-group col-md-4 col-sm-6">
                <label class="optional">Post Code</label>
                <div class="input-group">
                    <input id="postCode" class="form-control" name="postCode" type="text" placeholder="Search Post Code" class="form-control">
                    <div class="input-group-btn">
                        <button onclick="return PostCodeChanged();" class="btn btn-success">Look Up Postcode</button>
                    </div>
                </div>
            </div>
            <div class="form-group col-md-4 col-sm-6"><br>
                <div class="input-group">
                    <input placeholder="Building No / Name" data-toggle="popover" data-trigger="hover" data-placement="top" style="text-decoration:none;" data-content="For Business Addresses Enter Organisation Name" id="buildingName" name="buildingName" class="form-control" readonly="readonly" type="text">
                    <div class="input-group-btn">
                        <button onclick="EditStreet();" type="button" class="btn btn-info">Edit Street</button>
                    </div>
                </div>
            </div>
            <div class="form-group col-md-4 col-sm-6">
                <label class="optional">Street / Road / Area</label>
                <input type="hidden" class="form-control" id="postcode_data" />
                <select class="form-control" onchange="return PostCodeListChanged(this);" id="postcodelist"></select>
            </div>
            <div class="form-group col-md-4 col-sm-6">
                <label class="optional">City or Town</label>
                <select id="assignCity" name="assignCity" class="form-control">
                    <?php include 'assigncityselect.php'; ?>
                    <?php if (isset($_POST['submit'])) {
                        $c9 = $_POST['assignCity'];
                        $acttObj->editFun($table, $edit_id, 'assignCity', $c9);
                        $acttObj->editFun($table, $edit_id, 'postcode_data', $_POST['postcode_data']);
                    } ?>
                    <?php if (isset($_POST['submit'])) {
                        $c10 = $_POST['postCode'];
                        $acttObj->editFun($table, $edit_id, 'postCode', $c10);
                    } ?>
            </div>
            <div class="form-group col-md-8 col-sm-6">
                <textarea id="street" name="street" rows="2" placeholder="Street Details" type="text" class="form-control"></textarea>
                <?php if (isset($_POST['submit'])) {
                    $buildingName = $_POST['buildingName'];
                    $acttObj->editFun($table, $edit_id, 'buildingName', $buildingName);
                }
                ?>
                <?php
                if (isset($_POST['submit'])) {
                    $street = $_POST['street'];
                    $acttObj->editFun($table, $edit_id, 'street', $street);
                } ?>
            </div>
            <div class="bg-info col-xs-12 form-group">
                <h4>BOOKING ORGANIZATION DETAILS</h4>
            </div>
            <div class="form-group col-md-4 col-sm-6">
                <label>Company / Team / Unit Name*</label>
                <select onchange="new_company(this)" id="orgName" name="orgName" class="form-control multi_class">
                    <option data-id='' data-type-id='' value="">--- Select a Company---</option>
                    <?php
                    $get_companies = $acttObj->read_all("comp_reg.id,comp_reg.name,comp_reg.abrv,comp_reg.status,comp_type.company_type_id", "comp_reg,comp_type", "comp_reg.type_id=comp_type.id AND comp_reg.comp_nature!=1 AND comp_reg.status <> 'Company Seized trading in' and comp_reg.status <> 'Company Blacklisted' ORDER BY comp_reg.name ASC");
                    while ($row_company = $get_companies->fetch_assoc()) {
                        $selected_company = $row['order_company_id'] == $row_company["id"] || $orgName == $row_company['abrv'] ? "selected" : "";
                        echo "<option data-id='" . $row_company["id"] . "' data-type-id='" . $row_company["company_type_id"] . "' value='" . $row_company['abrv'] . "'>" . $row_company['name'] . "<span style='color:#F00;'>(" . $row_company["status"] . ")</span></option>";
                    }
                    ?>
                </select>
                <input type="hidden" name="order_company_id" id="order_company_id" />
                <?php if (isset($_POST['submit'])) {
                    $c18 = $_POST['orgName'];
                    $acttObj->editFun($table, $edit_id, 'orgName', $c18);
                    $acttObj->editFun($table, $edit_id, 'order_company_id', $_POST['order_company_id']);
                } ?>
                <label class="new_company hidden" style="margin-top: 12px;"><input onchange="new_company_fields(this)" name="new_company_checkbox" id="new_company_checkbox" class="new_company_checkbox" type="checkbox" value="1"> Register as new company</label>

           </div>
            <?php TestCode::LoadHtml("joblistcreditlimit.html"); ?>
            <div class="form-group col-md-3 col-sm-6 search-box">
                <label class="optional">Client Booking Ref/Name</label>
                <input name="orgRef" id="orgRef" type="text" required='' class="form-control" autocomplete="off" placeholder="Search Org Reference" />
                <i id="confirm_orgRef" style="display:none;position: absolute;right: 15px;top: 26px;" onclick="$(this).hide();$(this).next('.result').empty();" class="btn btn-info btn-sm glyphicon glyphicon-ok-sign text-success confirm_element" title="Confirm this reference"></i>
                <div class="result"></div>
                <?php if (isset($_POST['submit'])) {
                    $orgRef = $_POST['orgRef'];
                    $acttObj->editFun($table, $edit_id, 'orgRef', $orgRef);
                    $ref_counter = $acttObj->read_specific("count(*) as counter", "comp_ref", "company='" . $_POST['orgName'] . "' AND reference='" . $orgRef . "'")['counter'];
                    if ($ref_counter == 0 && !empty($orgRef)) {
                        $get_reference_id = $acttObj->get_id("comp_ref");
                        $acttObj->update("comp_ref", array("company" => $_POST['orgName'], "reference" => $orgRef), array("id" => $get_reference_id));
                        $acttObj->editFun($table, $edit_id, 'reference_id', $get_reference_id);
                    } else {
                        $existing_ref_id = $acttObj->read_specific("id", "comp_ref", "company='" . $_POST['orgName'] . "' AND reference='" . $orgRef . "'")['id'];
                        $acttObj->editFun($table, $edit_id, 'reference_id', $existing_ref_id);
                    }
                } ?>
            </div>
            <div class="form-group col-md-3 col-sm-6">
                <label>Assignment Incharge Name * </label>
                <input name="orgContact" id="orgContact" type="text" placeholder='' required='' class="form-control" />
                <?php if (isset($_POST['submit'])) {
                    $orgContact = $_POST['orgContact'];
                    $acttObj->editFun($table, $edit_id, 'orgContact', $orgContact);
                } ?>
            </div>
            <div class="bg-info col-xs-12 form-group">
                <h4>BOOKING PERSON DETAILS</h4>
            </div>
            <!--Purchase order on off-->
            <div class="form-group col-md-4 col-sm-6 hidden" id="div_check_po">
                <label>Do you have purchase order number?</label>
                <br><span class="col-md-offset-2">
                    <label class="checkbox-inline" style="margin-top: 4px;border: 1px solid lightgrey;padding: 2px 10px;"><input style="transform: scale(1.2);" onchange="booking_purch_order();" type="radio" name="po_number" value="1"> Yes</label>
                    <label class="checkbox-inline" style="border: 1px solid lightgrey;padding: 2px 10px;"><input style="transform: scale(1.2);" onchange="booking_purch_order();" type="radio" name="po_number" value="0" checked> No</label>
                </span>
            </div>
            <div class="form-group col-md-4 col-sm-6 hidden search-box" id="div_po_number">
                <label class="optional">Enter purchase order number</label>
                <input name="purchase_order_number" id="purchase_order_number" type="text" class="form-control" autocomplete="off" placeholder="Search purchase order number" />
                <i id="confirm_po" style="display:none;position: absolute;right: 15px;top: 26px;" onclick="$(this).hide();$(this).next('.result').empty();" class="btn btn-info btn-sm glyphicon glyphicon-ok-sign text-success confirm_element" title="Confirm this purchase order number"></i>
                <div class="result"></div>
            </div>
            <div id="div_po_req" class="form-group col-md-4 col-sm-6">
                <label>Purchase Order Email Address </label>
                <input oninput="$('#write_po_email').html($(this).val());if($(this).val()){$('.tr_po_email').removeClass('hidden');}" name="po_req" id="po_req" type="text" class="long form-control bg-success" placeholder='Fill email for purchase order' required='required' />
            </div>
            <div class="row"></div>
            <div class="form-group col-md-4 col-sm-6 div_new_company hidden">
                <label class="optional"> Company Name </label>
                <input name="new_company_name" id="new_company_name" type="text" class="form-control" />
            </div>
            <div class="form-group col-md-4 col-sm-6">
                <label class="optional"> Booking Person Name </label>
                <input name="inchPerson" id="inchPerson" type="text" class="long form-control" />
            </div>
            <div class="form-group col-md-4 col-sm-6">
                <label class="optional">Contact Number</label>
                <input name="inchContact" id="inchContact" type="text" class="long form-control" />
            </div>
            <div class="form-group col-md-4 col-sm-6">
                <label class="optional">Booking Confirmation Email #1</label>
                <input oninput="$('#write_booking_email').html($(this).val());" name="inchEmail" id="inchEmail" type="text" class="long form-control" />
            </div>
            <div class="form-group col-md-4 col-sm-6">
                <label class="optional">Booking Confirmation Email #2</label>
                <input name="inchEmail2" id="inchEmail2" type="text" class="long form-control" placeholder='' />
            </div>
            <?php if (isset($_POST['submit'])) {
                if (isset($_POST['new_company_checkbox']) && $_POST['orgName'] == "LSUK_Private Client") {
                    $new_company_id = $acttObj->get_id("private_company");
                    $acttObj->update("private_company", array("name" => $_POST['new_company_name'], "inchPerson" => $_POST['inchPerson'], "inchContact" => $_POST['inchContact'], "inchEmail" => $_POST['inchEmail'], "inchEmail2" => $_POST['inchEmail2'], "inchNo" => $_POST['inchNo'], "line1" => $_POST['line1'], "line2" => $_POST['line2'], "inchRoad" => $_POST['inchRoad'], "inchCity" => $_POST['inchCity'], "inchPcode" => $_POST['inchPcode']), array("id" => $new_company_id));
                    $acttObj->editFun($table, $edit_id, 'new_comp_id', $new_company_id);
                }
                $inchPerson = $_POST['inchPerson'];
                $acttObj->editFun($table, $edit_id, 'inchPerson', $inchPerson);
                $c12 = $_POST['inchContact'];
                $acttObj->editFun($table, $edit_id, 'inchContact', $c12);
                $c13 = $_POST['inchEmail'];
                $acttObj->editFun($table, $edit_id, 'inchEmail', $c13);
                $strEmail2 = $_POST['inchEmail2'];
                $acttObj->editFun($table, $edit_id, 'inchEmail2', $strEmail2);
                $porder_email = $_POST['po_req'];
                $acttObj->editFun($table, $edit_id, 'porder_email', $porder_email);
                if (isset($_POST['po_number']) && $_POST['po_number'] == 1 && isset($_POST['purchase_order_number'])) {
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
                            $is_valid_p_order = 0;
                            $acttObj->insert('jobnotes', array('jobNote' => 'NO REMAINING BALANCE in the Added purchase order #' . $purchase_order_number . ' for job reference:' . $c6, 'tbl' => $table, 'time' => $misc->sys_datetime_db(), 'fid' => $edit_id, 'submitted' => $_SESSION['UserName'], 'dated' => date('Y-m-d')));
                        } else {
                            $acttObj->editFun($table, $edit_id, 'porder', $purchase_order_number);
                        }
                    }
                    
                    
                }
            } ?>

            <div id="new_company_section" style="display: none; margin-top: 10px;">
                <div class="form-group col-md-4 col-sm-6">
                    <?php include 'jobformpostcode.php'; ?>
                </div>
           </div>
            <div id="po_confirm_modal" class="modal fade" role="dialog" style="margin-top: 90px;">
                <div class="modal-dialog modal-md">
                    <div class="modal-content">
                        <div class="modal-body text-center">
                            <h4>Emails confirmation for this booking</h4>
                            <table class="table table-bordered">
                                <tr>
                                    <td>Booking Email</td>
                                    <td><b id="write_booking_email"></b></td>
                                    <td><a onclick="$('#po_confirm_modal').modal('hide');$('#inchEmail').focus();$(this).removeClass('btn-info');$(this).addClass('btn-default');" href="javascript:void(0)" class="btn btn-info btn-sm"><i class="fa fa-remove-circle"></i>Update</a></td>
                                </tr>
                                <tr class="tr_po_email">
                                    <td>Purchase Order Email</td>
                                    <td><b id="write_po_email"></b></td>
                                    <td><a onclick="$('#po_confirm_modal').modal('hide');$('#po_req').focus();$(this).removeClass('btn-info');$(this).addClass('btn-default');" href="javascript:void(0)" class="btn btn-info btn-sm"><i class="fa fa-remove-circle"></i>Update</a></td>
                                </tr>
                            </table>
                            <p class="text-left"><span class="text-danger"><b>Important Note: </b></span><br>These emails will be used to send invoice reminders & purchase order requests to the client. So make sure that entered emails are correct. Click on <u>Update Now button</u> if you want to change these emails.</p>
                            <a onclick="$('#po_confirm_modal').modal('hide');confirm_job();" href="javascript:void(0)" class="btn btn-primary"><i class="fa fa-check-circle"></i>Yes</a>
                            <a onclick="$('#po_confirm_modal').modal('hide');$('#po_req').focus();" href="javascript:void(0)" class="btn btn-default"><i class="fa fa-remove-circle"></i>Update Now</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-12"></div>
            <div class="form-group col-sm-6">
                <label><small>Notes for Interpreter</small></label>
                <textarea placeholder="Notes for Interpreter ..." name="remrks" id="remrks" rows="3" class="form-control"></textarea>
                <input name="job_note" type="checkbox" value="1" /> Check to save as Job Note ?
                <?php if (isset($_POST['submit']) && !empty($_POST['remrks'])) {
                    $remrks = $_POST['remrks'];
                    $acttObj->editFun($table, $edit_id, 'remrks', $remrks);
                    if (isset($_POST['job_note']) && !empty($_POST['job_note']) && !empty($_POST['remrks'])) {
                        $acttObj->insert('jobnotes', array('jobNote' => $con->real_escape_string($remrks), 'tbl' => $table, 'time' => $misc->sys_datetime_db(), 'fid' => $edit_id, 'submitted' => $_SESSION['UserName'], 'dated' => date('Y-m-d')));
                    }
                } ?>
            </div>
            <div class="form-group col-sm-6">
                <label><small>Notes for Client</small></label>
                <textarea placeholder="Notes for Clients ..." name="I_Comments" rows="3" id="I_Comments" class="form-control"></textarea>
                <input name="job_note_c" type="checkbox" value="1" /> Check to save as Job Note ?
                <?php if (isset($_POST['submit']) && !empty($_POST['I_Comments'])) {
                    $I_Comments = $_POST['I_Comments'];
                    $acttObj->editFun($table, $edit_id, 'I_Comments', $I_Comments);
                    if (isset($_POST['job_note_c']) && !empty($_POST['job_note_c']) && !empty($_POST['I_Comments'])) {
                        $acttObj->insert('jobnotes', array('jobNote' => $con->real_escape_string($I_Comments), 'tbl' => $table, 'time' => $misc->sys_datetime_db(), 'fid' => $edit_id, 'submitted' => $_SESSION['UserName'], 'dated' => date('Y-m-d')));
                    }
                } ?>
            </div>
            <div class="form-group col-sm-12">
                <label><small>Notes For LSUK(Staff Only)</small></label>
                <textarea placeholder="Notes for Lsuk ..." name="lsuk_Comments" rows="3" id="lsuk_Comments" class="form-control"></textarea>
                <?php if (isset($_POST['submit']) && !empty($_POST['lsuk_Comments'])) {
                    $lsuk_Comments = $_POST['lsuk_Comments'];
                    $acttObj->insert('jobnotes', array('jobNote' => $con->real_escape_string($lsuk_Comments), 'tbl' => $table, 'time' => $misc->sys_datetime_db(), 'fid' => $edit_id, 'submitted' => $_SESSION['UserName'], 'dated' => date('Y-m-d')));
                } ?>
            </div>
            <div class="bg-info col-xs-12 form-group">
                <h4>MORE DETAILS</h4>
            </div>
            <script type="text/javascript">
                g_strJobTableIs = "<?php echo $table; ?>";
            </script>
            <script type="text/javascript" src="ajax.js"></script>
            <div class="form-group col-md-3 col-sm-6">
                <label>Assignment Date *</label>
                <input type="date" name="assignDate" id="assignDate" required='' class="form-control" value='' />
            </div>
            <?php TestCode::LoadHtml("jobtimeduration.html");
            if (isset($_POST['submit'])) {
                $c3 = $_POST['assignDate'];
                $acttObj->editFun($table, $edit_id, 'assignDate', $c3);
            }
            if (isset($_POST['submit'])) {
                $c4 = $_POST['assignTime'];
                $acttObj->editFun($table, $edit_id, 'assignTime', $c4);
                $guess_dur = $_POST['guess_dur'];
                $acttObj->editFunTimeAsMins($table, $edit_id, 'guess_dur', $guess_dur);
            }
            include 'jobformbookedvia.php'; ?>
            <div class="form-group col-md-4 col-sm-6">
                <input type="hidden" name="company_rate_id" id="company_rate_id" class="form-control"/>
                <input type="hidden" name="company_rate_data" id="company_rate_data" class="form-control"/>
                <label class="optional">Booking Type </label>
                <select name="bookinType" id="bookinType" required class="form-control"></select>
                <?php if (isset($_POST['submit'])) {
                    $c22 = $_POST['bookinType'];
                    $acttObj->editFun($table, $edit_id, 'bookinType', $c22);
                    $company_rate_id = $_POST['company_rate_id'];
                    $acttObj->editFun($table, $edit_id, 'company_rate_id', $company_rate_id);
                    $company_rate_data = $_POST['company_rate_data'];
                    $acttObj->editFun($table, $edit_id, 'company_rate_data', $company_rate_data);
                } ?>
            </div>
            <div class="form-group col-md-3 col-sm-6">
                <label class="optional">Gender</label>
                <select name="gender" id="gender" class="form-control" required>
                    <option value="">--Select--</option>
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                    <option value="No Preference">No Preference</option>
                </select>
                <?php if (isset($_POST['submit'])) {
                    $c22 = $_POST['gender'];
                    $acttObj->editFun($table, $edit_id, 'gender', $c22);
                } ?>
            </div>
            <div class="form-group col-md-3 col-sm-6 text-center">
                <label class="optional">STATUS: </label><br>
                <div class="radio-inline ri"><label><input name="jobStatus" type="radio" value="0" required />
                        <span class="label label-danger">Enquiry <i class="fa fa-question"></i></span></label></div>
                <div class="radio-inline ri"><label><input type="radio" name="jobStatus" value="1" />
                        <span class="label label-success">Confirmed <i class="fa fa-check-circle"></i></span></label></div>
                <?php if (isset($_POST['submit'])) {
                    $data = $_POST['jobStatus'];
                    $acttObj->editFun($table, $edit_id, 'jobStatus', $data);
                } ?>
            </div>
            <div class="form-group col-md-3 col-sm-6 text-center">
                <label class="optional">DBS checked ?</label><br>
                <div class="radio-inline ri"><label><input name="dbs_checked" type="radio" value="0" required />
                        <span class="label label-success">Yes <i class="fa fa-check-circle"></i></span></label></div>
                <div class="radio-inline ri"><label><input type="radio" name="dbs_checked" value="1" disabled />
                        <span class="label label-danger">No <i class="fa fa-remove"></i></span></label></div>
                <?php if (isset($_POST['submit'])) {
                    $data = $_POST['dbs_checked'];
                    $acttObj->editFun($table, $edit_id, 'dbs_checked', $data);
                } ?>
            </div>
            <div class="form-group col-md-3 col-sm-6 text-center <?php echo $_SESSION['prv'] == 'Test' ? 'hidden' : ''; ?>" style="margin-top: 2px;">
                <label class="optional">SEND AUTO REMINDER ?</label><br>
                <div class="radio-inline ri" onclick="disabler(1);"><label><input name="jobDisp" type="radio" value="1" />
                        <span class="label label-success" style="font-size:100%;padding: .5em 0.6em 0.5em;">Yes <i class="fa fa-check-circle"></i></span></label></div>
                <div class="radio-inline ri" onclick="disabler(0);"><label><input type="radio" name="jobDisp" value="0"  />
                        <span class="label label-danger" style="font-size:100%;padding: .5em 0.6em 0.5em;">No <i class="fa fa-remove"></i></span></label></div>
            </div>
            <?php if (isset($_POST['submit']) && $_SESSION['prv'] != 'Test') {
                $data = $_POST['jobDisp'];
                $acttObj->editFun($table, $edit_id, 'jobDisp', $data);
            } ?>
            <div id="div_selector" class="form-group col-md-3 col-sm-6 hidden selector">
                <label>Send reminders to</label>
                <select id="selector" onchange="changable()" class="form-control" name="selector">
                    <option value='all'>All Interpreters</option>
                    <option value='sc'>Specific Interpreters</option>
                </select>
            </div>
            <div id="div_selector_reason" class="form-group col-md-3 col-sm-6 hidden">
                <label>Reason For Specific Selection</label>
                <select id="selector_reason" class="form-control" name="selector_reason">
                    <option value='' disabled> --- Choose Reason --- </option>
                    <option value='Regular Job' selected>Regular Job</option>
                    <option value='Requested Job'>Requested Job</option>
                    <option value='Other'>Other</option>
                </select>
            </div>
            <?php if (isset($_POST['submit']) && $_POST['selector'] == 'sc') {
                $noty_reason = $_POST['selector_reason'];
                $acttObj->editFun($table, $edit_id, 'noty_reason', $noty_reason);
            } ?>
            <div id="div_specific" class="form-group col-md-3 col-sm-6 hidden">
                <label class="optional" style="display:block;">Selected Interpreters</label>
                <select class="multi_class" id="selected_interpreters" name="selected_interpreters[]" multiple="multiple">
                    <?php $res_ints = $acttObj->read_all("id,name,gender,city", "interpreter_reg", "interpreter_reg.active='0' AND (interpreter_reg.actnow='Active' OR (interpreter_reg.actnow='Inactive' AND CURRENT_DATE() NOT BETWEEN interpreter_reg.actnow_time AND interpreter_reg.actnow_to)) 
                    AND ((interpreter_reg.uk_citizen=1 AND interpreter_reg.id_doc_expiry_date != '1001-01-01' AND interpreter_reg.id_doc_expiry_date > CURRENT_DATE()) OR (interpreter_reg.uk_citizen=0 AND interpreter_reg.work_evid_expiry_date != '1001-01-01' AND interpreter_reg.work_evid_expiry_date > CURRENT_DATE())) 
                    AND (interpreter_reg.is_dbs_auto=1 OR (interpreter_reg.is_dbs_auto=0 AND interpreter_reg.dbs_expiry_date != '1001-01-01' AND interpreter_reg.dbs_expiry_date > CURRENT_DATE())) 
                     AND interp='Yes' AND deleted_flag=0 AND is_temp=0 AND isAdhoc=0 AND subscribe=1 ORDER BY name ASC");
                    while ($row_ints = mysqli_fetch_assoc($res_ints)) { ?>
                        <option value="<?php echo $row_ints['id']; ?>"><?php echo $row_ints['name'] . ' (' . $row_ints['gender'] . ')' . ' (' . $row_ints['city'] . ')'; ?></option>
                    <?php } ?>
                </select>
                <div id='interpreter_msg'></div>
            </div>
            <?php if (isset($_POST['submit']) && $_POST['selector'] == 'sc') {
                $noty = implode(',', $_POST['selected_interpreters']);
                $reason_title = $_POST['selector_reason'] ? '<br>Reason: ' . $_POST['selector_reason'] : '';
                $interpreter_names = $acttObj->read_specific("GROUP_CONCAT(name) as names", "interpreter_reg", "id IN (" . $noty . ")")['names'];
                $acttObj->insert('jobnotes', array('jobNote' => "Notified interpreters: " . $interpreter_names . $reason_title, 'tbl' => $table, 'time' => $misc->sys_datetime_db(), 'fid' => $edit_id, 'submitted' => $_SESSION['UserName'], 'dated' => date('Y-m-d')));
                $acttObj->editFun($table, $edit_id, 'noty', $noty);
            } ?>
            <div class="form-group col-xs-12">
                <div class="alert alert-danger fade in alert-dismissible">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close" title="close">×</a>
                    <strong>INFORMATION! </strong> <b>Use Job Notes Action Button.</b>
                    <li>For Telephone Orders => Enter Name, Number, Date and Time </li>
                    <li>For Timesheets Entries => Enter Interpreter Name and Date of the Assignment </li>
                    <li><b>To Ensure Accuracy and Minimise Errors, Always Double Check Entries Before Form Submission</b> </li>
                </div><br><br><br>
            </div>
        </form>
    </div>
    <?php
    if (isset($_POST['submit'])) {
        $acttObj->editFun($table, $edit_id, 'submited', ucwords($_SESSION['UserName']));

        //....Invoice #.........//
        if ($_POST['jobStatus'] == 1) {
            $nmbr = $acttObj->get_id('invoice');
            if ($nmbr == null) {
                $nmbr = 0;
            }
            // $abrv = $_POST['orgName'];
            $new_nmbr = str_pad($nmbr, 5, "0", STR_PAD_LEFT);
            $invoice = date("my") . $new_nmbr;
            // $invoice = 'LSUK' . $new_nmbr . '' . $abrv;
            $maxId = $nmbr;
            $acttObj->editFun('invoice', $maxId, 'invoiceNo', $invoice);
            $acttObj->editFun($table, $edit_id, 'invoiceNo', $invoice);
        }
    }

    if (isset($_POST['submit'])) {
        if ($_SESSION['Temp'] == 1) {
            $acttObj->editFun($table, $edit_id, 'is_temp', '1');
            //Assign temprory role bookings to Nadia
            $acttObj->insert("assigned_jobs_users", array("user_id" => 2, "order_id" => $edit_id, "order_type" => 1, "assigned_by" => 1, "assigned_date" => date('Y-m-d H:i:s'), "created_date" => date('Y-m-d H:i:s')));
        }
        //Email notification to related interpreters
        if($_SESSION['prv'] != 'Test'){
        $jobDisp_req = $_POST['jobDisp'];
        $jobStatus_req = $_POST['jobStatus'];
        if ($jobDisp_req == '1' && $jobStatus_req == '1' && $_SESSION['Temp'] == 0) {
            $source_lang_req = $_POST['source'];
            
            //Assign it to an operator randomly, check if an operator already has same job today
            $get_same_job_user = $acttObj->read_specific(
                "assigned_jobs_users.user_id", 
                "assigned_jobs_users,interpreter", 
                "assigned_jobs_users.order_id = interpreter.id 
                AND assigned_jobs_users.order_type=1 
                AND interpreter.source='".$source_lang_req."' 
                AND interpreter.order_company_id='".$_POST['order_company_id']."' 
                AND interpreter.dated='".date('Y-m-d')."' 
                ORDER BY assigned_jobs_users.id DESC
                LIMIT 1")['user_id'];
            if (!empty($get_same_job_user)) {
                $acttObj->insert("assigned_jobs_users", array("user_id" => $get_same_job_user, "order_id" => $edit_id, "order_type" => 1, "assigned_by" => 1, "assigned_date" => date('Y-m-d H:i:s'), "created_date" => date('Y-m-d H:i:s')));
            } else {
                //Pick random operator with low jobs
                $get_random_job_user = $acttObj->read_specific(
                    "login.id", 
                    "login LEFT JOIN assigned_jobs_users ON login.id=assigned_jobs_users.user_id 
                    LEFT JOIN users_timings ON login.id=users_timings.user_id", 
                    "login.prv='Operator' AND login.user_status=1 AND login.is_allocation_member=1 
                    AND (assigned_jobs_users.order_type = 1 OR assigned_jobs_users.order_type IS NULL)
                    AND users_timings.".strtolower(date('l'))." = 1 AND '".date('H:i:s')."' BETWEEN users_timings.".strtolower(date('l'))."_time AND users_timings.".strtolower(date('l'))."_to
                    GROUP BY login.id
                    ORDER BY COUNT(assigned_jobs_users.order_id) ASC, RAND() LIMIT 1")['id'];
                if (!empty($get_random_job_user)) {
                    $acttObj->insert("assigned_jobs_users", array("user_id" => $get_random_job_user, "order_id" => $edit_id, "order_type" => 1, "assigned_by" => 1, "assigned_date" => date('Y-m-d H:i:s'), "created_date" => date('Y-m-d H:i:s')));   
                }
            }
            //Assign to operator ends

            $acttObj->editFun($table, $edit_id, 'submited', ucwords($_SESSION['UserName']));
            $dbs_checked_req = isset($_POST['dbs_checked']) && !empty($_POST['dbs_checked']) && $_POST['dbs_checked'] == 0 ? 'AND interpreter_reg.dbs_checked=0' : '';
            $assignCity_name = explode(',', $_POST['assignCity']);
            $assignCity_req = $assignCity_name[0];
            $assignDate_req = $misc->dated($_POST['assignDate']);
            $assignTime_req = $_POST['assignTime'];
            $assignDur_req = $_POST['assignDur'];
            list($part1, $part2) = explode(':', $assignDur_req);
            $total_dur = $part1 * 60 + $part2;
            if ($total_dur > 60) {
                $hours = $total_dur / 60;
                if (floor($hours) > 1) {
                    $hr = "hours";
                } else {
                    $hr = "hour";
                }
                $mins = $total_dur % 60;
                if ($mins == 0) {
                    $get_dur = sprintf("%2d $hr", $hours);
                } else {
                    $get_dur = sprintf("%2d $hr %02d minutes", $hours, $mins);
                }
            } else if ($total_dur == 60) {
                $get_dur = "1 Hour";
            } else {
                $get_dur = $total_dur . " minutes";
            }
            if ($assignDur_req != $guess_dur) {
                list($partdur1, $partdur2) = explode(':', $guess_dur);
                $total_guess_dur = $partdur1 * 60 + $partdur2;
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
            $write_interp_cat = $interp_cat == '12' ? $assignIssue : $acttObj->read_specific("ic_title", "interp_cat", "ic_id=" . $interp_cat)['ic_title'];
            $write_interp_type = $interp_cat == '12' ? '' : $acttObj->read_specific("GROUP_CONCAT(CONCAT(it_title)  SEPARATOR ' <b> & </b> ') as it_title", "interp_types", "it_id IN (" . $interp_type . ")")['it_title'];
            if ($interp_cat == '12') {
                $append_issue = "<tr><td style='border: 1px solid yellowgreen;padding:5px;'>Assignment Category</td><td style='border: 1px solid yellowgreen;padding:5px;'>Other</td></tr><tr><td style='border: 1px solid yellowgreen;padding:5px;'>Assignment Details</td><td style='border: 1px solid yellowgreen;padding:5px;'>" . $assignIssue . "</td></tr>";
            } else {
                $append_issue = "<tr><td style='border: 1px solid yellowgreen;padding:5px;'>Assignment Category</td><td style='border: 1px solid yellowgreen;padding:5px;'>" . $write_interp_cat . "</td></tr><tr><td style='border: 1px solid yellowgreen;padding:5px;'>Assignment Details</td><td style='border: 1px solid yellowgreen;padding:5px;'>" . $write_interp_type . "</td></tr>";
            }
            $remrks = $remrks ?: '';
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
<td style='border: 1px solid yellowgreen;padding:5px;'>" . $inchPerson . "</td>
</tr>
<tr>
<td style='border: 1px solid yellowgreen;padding:5px;'>Case Worker or Person Incharge</td>
<td style='border: 1px solid yellowgreen;padding:5px;'>" . $orgContact . "</td>
</tr>
<tr>
<td style='border: 1px solid yellowgreen;padding:5px;'>Client Name</td>
<td style='border: 1px solid yellowgreen;padding:5px;'>" . $orgRef . "</td>
</tr>
</table>";
            if ($assignDur_req != $guess_dur) {
                $append_table .= "<br><u><b>NOTES FOR THIS JOB:</b></u><br>
  This session is booked for " . $get_dur . ", however it can take  up to " . $get_guess_dur . " or longer.<br>
  Therefore please consider your unrestricted availability before bidding / accepting this job.
  In cases of short notice cancellation, you will be paid the booked time (" . $get_dur . ").<br>";
                if (!empty($remrks)) {
                    $append_table .= $remrks . "<br>";
                }
            } else {
                if (!empty($remrks)) {
                    $append_table .= "<br><u><b>NOTES FOR THIS JOB:</b></u><br>" . $remrks . "<br>";
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
                $query_emails = "SELECT DISTINCT interpreter_reg.name, interpreter_reg.email, interpreter_reg.id FROM interpreter_reg,interp_lang WHERE interpreter_reg.code=interp_lang.code AND (SELECT COUNT(DISTINCT interp_lang.lang) FROM interp_lang WHERE interp_lang.type='interp' AND interp_lang.lang IN ('" . $source_lang_req . "')  and interp_lang.code=interpreter_reg.code ".(($_POST['selector'] != 'sc')?"  and interp_lang.level<3)=1 and 
                ((interpreter_reg.uk_citizen=1 AND interpreter_reg.id_doc_expiry_date != '1001-01-01' AND interpreter_reg.id_doc_expiry_date > CURRENT_DATE()) OR (interpreter_reg.uk_citizen=0 AND interpreter_reg.work_evid_expiry_date != '1001-01-01' AND interpreter_reg.work_evid_expiry_date > CURRENT_DATE())) 
                AND (interpreter_reg.is_dbs_auto=1 OR (interpreter_reg.is_dbs_auto=0 AND interpreter_reg.dbs_expiry_date != '1001-01-01' AND interpreter_reg.dbs_expiry_date > CURRENT_DATE())) AND interpreter_reg.active='0' AND (interpreter_reg.actnow='Active' OR (interpreter_reg.actnow='Inactive' AND CURRENT_DATE() NOT BETWEEN interpreter_reg.actnow_time AND interpreter_reg.actnow_to)) AND interpreter_reg.interp='Yes' $dbs_checked_req AND interpreter_reg.city LIKE '$assignCity_req' $put_gender AND interpreter_reg.deleted_flag=0 AND interpreter_reg.subscribe=1 AND interpreter_reg.is_temp=0 AND interpreter_reg.isAdhoc=0":")=1");
            } else if ($query_style == '1') {
                $query_emails = "SELECT DISTINCT interpreter_reg.name, interpreter_reg.email, interpreter_reg.id FROM interpreter_reg,interp_lang WHERE interpreter_reg.code=interp_lang.code AND (SELECT COUNT(DISTINCT interp_lang.lang) FROM interp_lang WHERE interp_lang.type='interp' AND interp_lang.lang IN ('" . $source_lang_req . "','" . $target_lang_req . "')  and interp_lang.code=interpreter_reg.code ".(($_POST['selector'] != 'sc')?"  and interp_lang.level<3 )=2 and 
                ((interpreter_reg.uk_citizen=1 AND interpreter_reg.id_doc_expiry_date != '1001-01-01' AND interpreter_reg.id_doc_expiry_date > CURRENT_DATE()) OR (interpreter_reg.uk_citizen=0 AND interpreter_reg.work_evid_expiry_date != '1001-01-01' AND interpreter_reg.work_evid_expiry_date > CURRENT_DATE())) 
                AND (interpreter_reg.is_dbs_auto=1 OR (interpreter_reg.is_dbs_auto=0 AND interpreter_reg.dbs_expiry_date != '1001-01-01' AND interpreter_reg.dbs_expiry_date > CURRENT_DATE())) AND interpreter_reg.active='0' AND (interpreter_reg.actnow='Active' OR (interpreter_reg.actnow='Inactive' AND CURRENT_DATE() NOT BETWEEN interpreter_reg.actnow_time AND interpreter_reg.actnow_to)) AND interpreter_reg.interp='Yes' $dbs_checked_req AND interpreter_reg.city LIKE '$assignCity_req' $put_gender AND interpreter_reg.deleted_flag=0 AND interpreter_reg.subscribe=1 AND interpreter_reg.is_temp=0 AND interpreter_reg.isAdhoc=0":")=2");
            } else if ($query_style == '2') {
                $query_emails = "SELECT DISTINCT interpreter_reg.name, interpreter_reg.email, interpreter_reg.id FROM interpreter_reg,interp_lang WHERE interpreter_reg.code=interp_lang.code AND interp_lang.type='interp' AND interp_lang.lang='$source_lang_req' ".(($_POST['selector'] != 'sc')?"  and interp_lang.level<3 and 
                ((interpreter_reg.uk_citizen=1 AND interpreter_reg.id_doc_expiry_date != '1001-01-01' AND interpreter_reg.id_doc_expiry_date > CURRENT_DATE()) OR (interpreter_reg.uk_citizen=0 AND interpreter_reg.work_evid_expiry_date != '1001-01-01' AND interpreter_reg.work_evid_expiry_date > CURRENT_DATE())) 
                AND (interpreter_reg.is_dbs_auto=1 OR (interpreter_reg.is_dbs_auto=0 AND interpreter_reg.dbs_expiry_date != '1001-01-01' AND interpreter_reg.dbs_expiry_date > CURRENT_DATE())) AND interpreter_reg.active='0' AND (interpreter_reg.actnow='Active' OR (interpreter_reg.actnow='Inactive' AND CURRENT_DATE() NOT BETWEEN interpreter_reg.actnow_time AND interpreter_reg.actnow_to)) AND interpreter_reg.interp='Yes' $dbs_checked_req AND interpreter_reg.city LIKE '$assignCity_req' $put_gender AND interpreter_reg.deleted_flag=0 AND interpreter_reg.subscribe=1 AND interpreter_reg.is_temp=0 AND interpreter_reg.isAdhoc=0":"");
            } else {
                $query_emails = "SELECT DISTINCT interpreter_reg.name, interpreter_reg.email, interpreter_reg.id FROM interpreter_reg WHERE ".(($_POST['selector'] != 'sc')?" ((interpreter_reg.uk_citizen=1 AND interpreter_reg.id_doc_expiry_date != '1001-01-01' AND interpreter_reg.id_doc_expiry_date > CURRENT_DATE()) OR (interpreter_reg.uk_citizen=0 AND interpreter_reg.work_evid_expiry_date != '1001-01-01' AND interpreter_reg.work_evid_expiry_date > CURRENT_DATE())) 
                AND (interpreter_reg.is_dbs_auto=1 OR (interpreter_reg.is_dbs_auto=0 AND interpreter_reg.dbs_expiry_date != '1001-01-01' AND interpreter_reg.dbs_expiry_date > CURRENT_DATE())) AND interpreter_reg.active='0' AND (interpreter_reg.actnow='Active' OR (interpreter_reg.actnow='Inactive' AND CURRENT_DATE() NOT BETWEEN interpreter_reg.actnow_time AND interpreter_reg.actnow_to)) AND interpreter_reg.interp='Yes' $dbs_checked_req AND interpreter_reg.city LIKE '$assignCity_req' $put_gender AND interpreter_reg.deleted_flag=0 AND interpreter_reg.subscribe=1 AND interpreter_reg.is_temp=0 AND interpreter_reg.isAdhoc=0":" ");
            }
            if ($_POST['selector'] == 'sc') {
                $selected_interpreters = implode(',', $_POST['selected_interpreters']);
                if($query_style!='3'){
                    $query_emails .= ' and interpreter_reg.id IN (' . $selected_interpreters . ') ';
                }else if($query_style=='3'){
                    $query_emails .= ' interpreter_reg.id IN (' . $selected_interpreters . ') ';
                }
            }
            // echo $query_style."<br>".$query_emails; die(); exit();

            $res_emails = mysqli_query($con, $query_emails);
            //Getting bidding email from em_format table
            $row_format = $acttObj->read_specific("em_format", "email_format", "id=28");
            $subject = "New Face To Face Project " . $edit_id;
            $sub_title = "Face To Face job of " . $source_lang_req . " language on " . $assignDate_req . " at " . $assignTime_req . " is available for you to bid.";
            $type_key = "nj";
            //$app_int_ids=array();
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
                    $array_tokens = explode(',', $acttObj->read_specific("GROUP_CONCAT( DISTINCT token) as tokens", "int_tokens", "int_id=" . $row_emails['id']." ORDER BY id DESC")['tokens']);
                    if (!empty($array_tokens)) {
                        $acttObj->insert('app_notifications', array("title" => $subject, "sub_title" => $sub_title, "dated" => date('Y-m-d'), "int_ids" => $row_emails['id'], "read_ids" => $row_emails['id'], "type_key" => $type_key));
                        //array_push($app_int_ids,$row_emails['id']);
                        // foreach ($array_tokens as $token) {
                            if (!empty($array_tokens[0])) {
                                $acttObj->notify($array_tokens[0], $subject, $sub_title, array("type_key" => $type_key, "job_type" => "Face To Face"));
                            }
                        // }
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
                            alert("Message could not be sent! Mailer library error."
                                <?php echo $e->getMessage() ?>);
                        </script>
        <?php   }
                }
            }
            //$int_distinct_ids=implode(',',array_unique($app_int_ids));
            //$acttObj->insert('app_notifications',array("title"=>$subject,"sub_title"=>$sub_title,"dated"=>date('Y-m-d'),"int_ids"=>$int_distinct_ids,"read_ids"=>$int_distinct_ids,"type_key"=>$type_key));
        }
        //booking acknolement mail start from here 
        //collecting email date
        $source = $_POST['source'] ?? '';
        $target = $_POST['target'] ?? '';
        $noty = implode(',', $_POST['selected_interpreters'] ?? []);
        if (!empty($noty)) {
            $names = $acttObj->read_specific(
                "GROUP_CONCAT(name SEPARATOR '||') as names",
                "interpreter_reg",
                "id IN ($noty)"
            )['names'];
        
            $nameArray = explode('||', $names);
            $count = count($nameArray);
        
            if ($count === 1) {
                $formattedNames = $nameArray[0];
            } elseif ($count === 2) {
                $formattedNames = $nameArray[0] . ' and ' . $nameArray[1];
            } else {
                $last = array_pop($nameArray);
                $formattedNames = implode(', ', $nameArray) . ' and ' . $last;
            }
        
            $replacementText = "We will be checking the availability of $formattedNames for this assignment";
        } else {
            $replacementText = "We will be checking our interpreters&rsquo; availability for this assignment";
        }
        $languagePair = "$source to $target";
        $emailSubject = "Booking Acknowledgment - $languagePair - $email_ref_No";
        $email_formate = $acttObj->read_specific("em_format", "email_format", "id=56");
        $email_formate = $email_formate['em_format']; 
        $serviceType = 'Face-to-Face'; // Replace with actual service type if needed
        $languagePair = "$_POST[source] to $_POST[target]";
        $referenceNo = $email_ref_No;
        // $purOrder = trim($_POST['purchase_order_number'] ?? '');
        // $purOrderFormatted = $purOrder !== '' 
        //     ? $purOrder 
        //     : '<span style="color:red;">Missing</span>';
        $purOrder_email="";
        $check_po = $acttObj->read_specific("po_req", "comp_reg", "abrv='" . $_POST['orgName']."'")['po_req'];
        if ($check_po) {
            if (!empty($_POST['purchase_order_number'])) {
                // If PO is given, only modify label if it's invalid
                $purOrderFormatted = ($is_valid_p_order == 0)
                    ? '<span style="color:red;">low balance</span>'
                    : $_POST['purchase_order_number'];
            } else {
                $purOrderFormatted = '<span style="color:red;">Missing</span>';
                $purOrder_email = "<li><strong>Purchase Order Request Email:</strong> " . 
                    (!empty($_POST['po_req']) ? htmlspecialchars($_POST['po_req']) : 'Missing') . 
                "</li>";
            }
        } else {
            $purOrderFormatted = 'N/A';
        }
        $bookedDate = $_POST['bookedDate'] ?? ''; // expected format: YYYY-MM-DD
        $bookedTime = $_POST['bookedTime'] ?? ''; // expected format: HH:MM
        $datetimeFormatted = '';
        if ($bookedDate && $bookedTime) {
            $datetime = DateTime::createFromFormat('Y-m-d H:i', "$bookedDate $bookedTime");
            $datetimeFormatted = $datetime ? $datetime->format('d/m/Y, H:i') : '';
        }
        if ($_POST['assignDate'] && $_POST['assignTime']) {
            $datetime_a = DateTime::createFromFormat('Y-m-d H:i', $_POST['assignDate'] .' '. $_POST['assignTime']);
            $datetimeFormatted_a = $datetime_a ? $datetime_a->format('d/m/Y, H:i') : '';
        }
        $venueOrLink = implode(', ', array_filter(array_map('trim', [
            $_POST['buildingName'] ?? '',
            $_POST['street'] ?? '',
            $_POST['assignCity'] ?? '',
            $_POST['postCode'] ?? ''
        ])));
        $email_formate = str_replace('[DD/MM/YYYY, HH:MM]', $datetimeFormatted, $email_formate);
        $email_formate = str_replace('<li>pur_email_req</li>', $purOrder_email, $email_formate);
        $email_formate = str_replace('[A_DD/MM/YYYY, HH:MM]', $datetimeFormatted_a, $email_formate);
        $email_formate = str_replace('Booking and Scheduling Team', $_SESSION['UserName'], $email_formate);
        $email_formate = str_replace(
            ['[Interp_requested]','[Language Pair - Reference Number]','[Client&rsquo;s Name]','[Reference Number]', '[Face-to-Face / Telephone / Video / Translation]', '[service type]', '[Requested Language Pair]', '[Venue / Online Meeting Link]', '[Number if provided/Missing]'],
            [$replacementText,"$languagePair - $referenceNo",$_POST['inchPerson'],$referenceNo, $serviceType, $serviceType, $languagePair, $venueOrLink, $purOrderFormatted],
            $email_formate
        );
        $jobStatus = $_POST['jobStatus'];
        if ($jobStatus == 0) {
          $jobStatus = 'Enquiry';
        } else {
          $jobStatus = 'Confirmed';
        }
        $table =  "<style type='text/css'>

        table.myTable { 
        
          border-collapse: collapse; 
        
          }
        
        table.myTable td, 
        
        table.myTable th { 
        
          border: 1px solid yellowgreen;
        
          padding: 5px; 
        
          }
        
        </style>
        
        
        <caption align='center' style='background: grey;color: white;padding: 5px;'>Booking Request for Face to Face Interpreter</caption>
        <table class='myTable'>
        
        <tr>
        
        <td style='border: 1px solid yellowgreen;padding:5px;'>Source Language</td>
        
        <td style='border: 1px solid yellowgreen;padding:5px;'>" . $source . "</td>
        
        <td style='border: 1px solid yellowgreen;padding:5px;'>Target Language</td>
        
        <td style='border: 1px solid yellowgreen;padding:5px;'>" . $target . "</td>
        
        </tr>
        
        
        
        <tr>
        
        <td style='border: 1px solid yellowgreen;padding:5px;'>Assignment Date/Time</td>
        
        <td style='border: 1px solid yellowgreen;padding:5px;'>" . $datetimeFormatted. "</td>
        
        <td style='border: 1px solid yellowgreen;padding:5px;'>Assignment Duration</td>
        
        <td style='border: 1px solid yellowgreen;padding:5px;'>" . $get_dur . "</td>
        
        </tr>
        
        " . $append_issue . "
        
        <tr>
        
        <td style='border: 1px solid yellowgreen;padding:5px;'>DBS checked interpreter Requested ?</td>
        
        <td style='border: 1px solid yellowgreen;padding:5px;'>" . $_POST['dbs_checked'] . "</td>
        
        <td style='border: 1px solid yellowgreen;padding:5px;'>Gender</td>
        
        <td style='border: 1px solid yellowgreen;padding:5px;'>" . $gender_req . "</td>
        
        </tr>
        
        
        
        <tr>
        
        <td colspan='4' align='center' style='color: black;'>Assignment Location</td>
        
        </tr>
        
        <tr>
        
        <td style='border: 1px solid yellowgreen;padding:5px;'>Building No / Name</td>
        
        <td style='border: 1px solid yellowgreen;padding:5px;'>" . $buildingName . "</td>
        
        <td style='border: 1px solid yellowgreen;padding:5px;'>Street / Road</td>
        
        <td style='border: 1px solid yellowgreen;padding:5px;'>" . $street . "</td>
        
        </tr>
        
        <tr>
        
        <td style='border: 1px solid yellowgreen;padding:5px;'>City</td>
        
        <td style='border: 1px solid yellowgreen;padding:5px;'>" . $assignCity . "</td>
        
        <td style='border: 1px solid yellowgreen;padding:5px;'>City / Town Post Code</td>
        
        <td style='border: 1px solid yellowgreen;padding:5px;'>" . $postCode . "</td>
        
        </tr>
        
        <tr>
        
        <td colspan='4' align='center' style='color: black;'>Booking Organization Details</td>
        
        </tr>
        
        <tr>
        
        <td style='border: 1px solid yellowgreen;padding:5px;'>Company Name (Team / Unit Title if Part of an Organisation or Trust)</td>
        
        <td style='border: 1px solid yellowgreen;padding:5px;'>" . $_POST['orgName'] . "</td>
        
        <td style='border: 1px solid yellowgreen;padding:5px;'>Booking Ref/Name</td>
        
        <td style='border: 1px solid yellowgreen;padding:5px;'>"  . (!empty(trim($_POST['orgRef'] ?? '')) ? $_POST['orgRef'] : "<span style='color:red;'>Missing</span>"). "</td>
        
        </tr>
        
        <tr>
        
        <td style='border: 1px solid yellowgreen;padding:5px;'>Interpreter Contact Name</td>
        
        <td style='border: 1px solid yellowgreen;padding:5px;'>" . $orgContact . "</td>
        
        <td style='border: 1px solid yellowgreen;padding:5px;'>Purchase Order Number</td>
        
        <td style='border: 1px solid yellowgreen;padding:5px;'>$purOrderFormatted</td>
        
        </tr>
        
        <tr>
        
        <td colspan='4' align='center' style='color: black;'>Assignment in-Charge</td>
        
        </tr>
        
        <tr>
        
        <td style='border: 1px solid yellowgreen;padding:5px;'>Booking Person Name if Different</td>
        
        <td style='border: 1px solid yellowgreen;padding:5px;'>" . $inchPerson . "</td>
        
        <td style='border: 1px solid yellowgreen;padding:5px;'>Contact Number</td>
        
        <td style='border: 1px solid yellowgreen;padding:5px;'>" . $_POST['inchContact'] . "</td>
        
        </tr>
        
        <tr>
        
        <td style='border: 1px solid yellowgreen;padding:5px;'>Email Address</td>
        
        <td style='border: 1px solid yellowgreen;padding:5px;'>" . $_POST['inchEmail'] . "</td>
        
        <td style='border: 1px solid yellowgreen;padding:5px;'>Building Number / Name</td>
        
        <td style='border: 1px solid yellowgreen;padding:5px;'>" . $_POST['inchNo'] . "</td>
        
        </tr>
        
        <tr>
        
        <td style='border: 1px solid yellowgreen;padding:5px;'>Address Line</td>
        
        <td style='border: 1px solid yellowgreen;padding:5px;'>" . $_POST['line1'] . "</td>
        
        <td style='border: 1px solid yellowgreen;padding:5px;'>Address Line 2</td>
        
        <td style='border: 1px solid yellowgreen;padding:5px;'>" . $_POST['inchRoad'] . "</td>
        
        </tr>
        
        <tr>
        
        <td style='border: 1px solid yellowgreen;padding:5px;'>City</td>
        
        <td style='border: 1px solid yellowgreen;padding:5px;'>" . $_POST['inchCity'] . "</td>
        
        <td style='border: 1px solid yellowgreen;padding:5px;'>City / Town Post Code</td>
        
        <td style='border: 1px solid yellowgreen;padding:5px;'>" . $_POST['inchPcode'] . "</td>
        
        </tr>
        
        <tr>
        
        <td colspan='4' align='center' style='color: black;'>Interpreter Details</td>
        
        </tr>
        
        <tr>
        
        <td style='border: 1px solid yellowgreen;padding:5px;'>Gender</td>
        
        <td style='border: 1px solid yellowgreen;padding:5px;'>" . $gender_req . "</td>
        
        <td style='border: 1px solid yellowgreen;padding:5px;'>Booking Status</td>
        
        <td style='border: 1px solid yellowgreen;padding:5px;'>" . $jobStatus . "</td>
        
        </tr>
        
        
        
        <tr>
        
        <td style='border: 1px solid yellowgreen;padding:5px;'>Notes if Any 1000 alphabets</td>
        
        <td colspan='4' align='center' style='border: 1px solid yellowgreen;padding:5px;'>" . $I_Comments . "</td>
        
        </tr>
        
        </table>";
        $email_formate = str_replace('[Details_table]', $table, $email_formate);
        $inchargeEmails = array_filter([$_POST['inchEmail'], $_POST['inchEmail2']]);
        try {
            if (!empty($inchargeEmails)) {
                foreach ($inchargeEmails as $email) {
                    $acttObj->insert('cron_emails', array(
                        "order_id" => $edit_id,
                        "order_type" => 1,
                        "user_id" => 0,
                        "user_type" => 2,
                        "send_from" => setupEmail::INFO_EMAIL,
                        "send_password" => setupEmail::INFO_PASSWORD,
                        "send_to" => $email,
                        "subject" => $emailSubject,
                        "template_type" => 8,
                        "template_data" => "{}",
                        "template_body" => mysqli_real_escape_string($con, $email_formate),
                        "created_date" => date("Y-m-d H:i:s")
                    ));
                    
                }
            }
        } catch (Exception $e) {
            echo "mail_failed";
        }
        ?>

        <script>
            alert('Job Submitted Successfully!');
            window.close();
            window.onunload = refreshParent;
        </script>
    <?php
        // $acttObj->editFun($table, $edit_id, 'edited_by', $_SESSION['UserName']);
        // $acttObj->editFun($table, $edit_id, 'edited_date', date("Y-m-d H:i:s"));
        // $acttObj->new_old_table('hist_' . $table, $table, $edit_id);
        $acttObj->insert("daily_logs", array("action_id" => 1, "user_id" => $_SESSION['userId'], "details" => "F2F Job ID: " . $edit_id));
    }
}
    include 'jobformrefresh.php'; ?>
    <script type="text/javascript" src="js/postcodelookup.js"></script>
    <script src="js/jquery-1.11.3.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.13/css/bootstrap-multiselect.css" rel="stylesheet" type="text/css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.13/js/bootstrap-multiselect.js" type="text/javascript"></script>
    <link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
    <script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>
    <script src="ckeditor/ckeditor/ckeditor.js"></script>
    <script type="text/javascript">
        CKEDITOR.replace('remrks', {
            height: '150px',
        });
        CKEDITOR.replace('I_Comments', {
            height: '150px',
        });
        CKEDITOR.replace('lsuk_Comments', {
            height: '150px',
        });
        DebugHere();
        $(document).ready(function() {
            var dp_mdl = 0;
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
            // $('#compare_modal').on('show.bs.modal', function (e) {
            //     if(dp_mdl>0) {
            //         e.stopPropagation();
            //     }  
            // });
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

        function get_interp_type(elem) {
            var ic_id = elem.val();
            $.ajax({
                url: 'ajax_add_interp_data.php',
                method: 'post',
                data: {
                    ic_id: ic_id
                },
                success: function(data) {
                    if (data.trim()) {
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
                    $(function() {
                        $('.multi_class').multiselect({
                            includeSelectAllOption: true,
                            numberDisplayed: 1,
                            enableFiltering: true,
                            enableCaseInsensitiveFiltering: true
                        });
                    });
                },
                error: function(xhr) {
                    alert("An error occured: " + xhr.status + " " + xhr.statusText);
                }
            });
        }

        function confirm_job() {
            var action = 'job_confirmation';
            var type = 'interpreter';
            var source = $('#source').val();
            var target = $('#target').val();
            var orgName = $('#orgName').val();
            var assignDate = $('#assignDate').val();
            var assignTime = $('#assignTime').val();
            var orgRef = $('#orgRef').val();
            var orgContact = $('#orgContact').val();
            $.ajax({
                url: 'ajax_add_interp_data.php',
                method: 'post',
                data: {
                    action: action,
                    type: type,
                    source: source,
                    target: target,
                    orgName: orgName,
                    assignDate: assignDate,
                    assignTime: assignTime,
                    orgRef: orgRef,
                    orgContact: orgContact
                },
                beforeSend: function() {
                    $('#ajax_loader').css("visibility", "visible");
                },
                success: function(data) {
                    if (data) {
                        $('#confirm_modal_body').html(data);
                        $('#confirm_modal').modal('show');
                    }
                    $('#btn_confirm').addClass('hidden');
                    $('#btn_submit').removeClass('hidden');
                },
                complete: function() {
                    $('#ajax_loader').css("visibility", "hidden");
                },
                error: function(xhr) {
                    alert("An error occured: " + xhr.status + " " + xhr.statusText);
                }
            });
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
                            // if(data){
                            console.log(data);
                            $('#selected_interpreters').html(data);
                            $("#selected_interpreters").multiselect('rebuild');
                            // }
                            if (data) {
                                $('#interpreter_msg').html('');
                            } else {
                                $('#interpreter_msg').html('No interpreter found');
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
            if ($('#po_req').is(':required') && $('input[name="po_number"]:checked').val() == 0) {
                $('.tr_po_email,#div_po_req').removeClass('hidden');
            } else {
                $('.tr_po_email,#div_po_req').addClass('hidden');
            }
            if (orgName == "LSUK_Private Client") {
                $('.new_company').removeClass('hidden');
            } else {
                $('.new_company').addClass('hidden');
            }
            var checkComany = ['WK', 'Mlink', 'NL'];
            if ($.inArray(orgName, checkComany) != -1) {
                $('#gender').val('Female').attr("selected", "selected");
            } else {
                $('#gender').val('').attr("selected", "selected");
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
                $('#orgContact,#inchPerson,#inchContact,#inchEmail,#inchEmail2,#inchNo,#line1,#line2,#inchRoad,#inchPcode').val('');
                $('#inchNo,#line1,#line2,#inchRoad,#inchPcode,#inchCity').removeAttr('readonly');
                $('.div_new_company').removeClass('hidden');
            } else {
                $('#orgContact').val(old_orgContact);
                $('#inchPerson').val(old_inchPerson);
                $('#inchContact').val(old_inchContact);
                $('#inchEmail').val(old_inchEmail);
                $('#inchEmail2').val(old_inchEmail2);
                $('#inchNo').val(old_inchNo);
                $('#line1').val(old_line1);
                $('#line2').val(old_line2);
                $('#inchRoad').val(old_inchRoad);
                $('#inchPcode').val(old_inchPcode);
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
        $(function() {
            var dtToday = new Date();

            var month = dtToday.getMonth() + 1;
            var day = dtToday.getDate();
            var year = dtToday.getFullYear();
            if (month < 10)
                month = '0' + month.toString();
            if (day < 10)
                day = '0' + day.toString();

            var maxDate = year + '-' + month + '-' + day;

            $('#assignDate').attr('min', maxDate);
        });
    </script>

    <script>
        // On Register checked display columns
        $("#new_company_checkbox").on("change", function() {
            if (this.checked) {
                $("#new_company_section").show();
            } else {
                $("#new_company_section").hide();
            }
        });
    </script>

    <!-- Confirmation Job Modal -->
    <div class="modal fade text-center p40" id="confirm_modal" tabindex="-1" role="dialog" aria-labelledby="confirm_modalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document" style="width:100%;">
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <h4 class="modal-title" id="confirm_modalLabel">Assignment Related Information</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="margin-top: -30px;border: 1px solid;border-radius: 100%;width: 25px;">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="confirm_modal_body" style="overflow-x:auto;">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

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

</body>

</html>