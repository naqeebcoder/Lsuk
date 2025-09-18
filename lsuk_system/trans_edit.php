<?php if (session_id() == '' || !isset($_SESSION)) {
    session_start();
}
include '../source/setup_email.php';
include 'db.php';
include 'class.php';

$allowed_type_idz = "2,16,29,71,83,114,174";
//Check if user has current action allowed
if ($_SESSION['is_root'] == 0) {
    $get_actions = explode(",", $acttObj->read_specific("GROUP_CONCAT(action_permissions.action_id) as actions", "action_permissions,route_actions", "action_permissions.action_id=route_actions.id AND route_actions.route_id=6 AND action_permissions.user_id=" . $_SESSION['userId'])['actions']);
    if(!(in_array(32, $get_actions) && $duplicate=="yes")){
        $get_page_access = $acttObj->read_specific("id", "action_permissions", "user_id=" . $_SESSION['userId'] . " AND action_id IN (" . $allowed_type_idz . ")")['id'];
        if (empty($get_page_access)) {
            die("<center><h2 class='text-center text-danger'>You do not have access to <u>Edit Order</u> action for jobs!<br>Kindly contact admin for further process.</h2></center>");
        }
    }
}

$table = 'translation';
$edit_id = @$_GET['edit_id'];
$duplicate = @$_GET['duplicate'];
$row = $acttObj->read_specific("*", "$table", "id=" . $edit_id);
$source = $row['source'];
$new_company_id = $row['new_comp_id'];
if ($new_company_id != 0) {
    $private_company = $acttObj->read_specific("*", "private_company", "id=" . $new_company_id);
    $private_company_name = $private_company['name'];
}
$target = $row['target'];
$docType = $row['docType'];
$transType = $row['transType'];
$trans_detail = $row['trans_detail'];
$deliveryType = $row['deliveryType'];
$inchContact = $row['inchContact'];
$inchEmail = $row['inchEmail'];
$inchEmail2 = $row['inchEmail2'];
$file = $row['file'];
$orgName = $row['orgName'];
$orgRef = $row['orgRef'];
$orgContact = $row['orgContact'];
$asignDate = $row['asignDate'];
$deliverDate = $row['deliverDate'];
$deliverDate2 = $row['deliverDate2'];
$deliveryType = $row['deliveryType'];
$remrks = $row['remrks'];
$gender = $row['gender'];
$intrpName = $row['intrpName'];
$dated = $row['dated'];
$invoiceNo = $row['invoiceNo'];
$jobStatus = $row['jobStatus'];
$bookinType = $row['bookinType'];
$company_rate_id = $row['company_rate_id']?:NULL;
$company_rate_data = !empty($row['company_rate_data']) ? (array) json_decode($row['company_rate_data']) : array();
$nameRef = $row['nameRef'];
$I_Comments = $row['I_Comments'];
$snote = $row['snote'];
$jobDisp = $row['jobDisp'];
$noty = $row['noty'];
$noty_reason = $row['noty_reason'];
$bookedVia = $row['bookedVia'];
$inchNo = $row['inchNo'];
$line1 = $row['line1'];
$line2 = $row['line2'];
$inchRoad = $row['inchRoad'];
$inchCity = $row['inchCity'];
$inchPcode = $row['inchPcode'];
$dbs_bookeddate = $row['bookeddate'];
$dbs_bookedtime = $row['bookedtime'];
$dbs_bookednamed = $row['namedbooked'];
$is_temp = $row['is_temp'];
$porder = $row['porder'];
$po_req = $acttObj->read_specific("po_req", "comp_reg", "abrv='" . $orgName . "'")['po_req'];
$porder_email = $row['porder_email']; ?>

<?php if (isset($_POST['submit']) && $duplicate == 'yes') {
    $edit_id = $acttObj->get_id($table);
    //Create & save new reference no
    $reference_no = $acttObj->generate_reference(3, $table, $edit_id);
}
if ($duplicate == 'yes') {
    $month = date('M');
    $month = substr($month, 0, 3);
    $lastid = $acttObj->max_id("global_reference_no") + 1;
    $nameRef = 'LSUK/' . $month . '/' . $lastid;
} ?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<title>Edit Translation Booking Form</title>
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
        min-width: 250px;
    }

    .multiselect-container {
        max-height: 400px;
        overflow-y: auto;
        max-width: 380px;
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
                <b>
                    <h4 style="display: inline-block;"><?php if ($duplicate == 'yes') {
                                                            echo 'Create Duplicate (Translation)';
                                                        } else {
                                                            echo 'Edit Translation Booking';
                                                        } ?></h4>
                </b>
                <button id="btn_confirm" class="btn btn-warning pull-right" style="border-color: #000000;color: black;text-transform: uppercase;margin: 13px 34px;font-size: 20px;font-weight: bold;box-shadow: 2px 2px 2px #c5c5a3;" type="button" name="btn_confirm" onclick="if( ($('#po_req').is(':required') && $('#po_req').val()) || (!$('#po_req').is(':required') && $('#inchEmail').val()) ){$('#po_confirm_modal').modal('show');}else{ if($('#po_req').is(':required') && !$('#po_req').val()){alert('You must enter purchase order email!');$('#po_req').focus();}else{$('#btn_confirm').addClass('hidden');$('#btn_submit').removeClass('hidden');}}">Confirm Job &raquo;</button>
                <button id="btn_submit" class="btn btn-info pull-right hidden" style="border-color: #000000;color: black;text-transform: uppercase;margin: 13px 34px;font-size: 20px;font-weight: bold;box-shadow: 2px 2px 2px #c5c5a3;" type="submit" name="submit" onclick="return confirm('Are you sure to submit this booking?');"><?php if ($duplicate == 'yes') {
                                                                                                                                                                                                                                                                                                                                            echo 'Duplicate Job';
                                                                                                                                                                                                                                                                                                                                        } else {
                                                                                                                                                                                                                                                                                                                                            echo 'Edit Job';
                                                                                                                                                                                                                                                                                                                                        } ?> &raquo;</button>
            </div><br><br><br><br>
            <div class="bg-info col-xs-12 form-group">
                <h4>WORK DETAILS</h4>
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
                <label>Target Language </label>
                <select name="target" id="target" required='' class="form-control">
                    <option disabled value="">Select Target Language</option>
                    <?php
                    $get_languages = $acttObj->read_all("lang,language_type", "lang", "1 ORDER BY lang ASC");
                    while ($row_language = $get_languages->fetch_assoc()) {
                        $selected_target = $row['target'] == $row_language["lang"] ? "selected" : "";
                        echo "<option ".$selected_target." data-type='" . $row_language['language_type'] . "' value='" . $row_language["lang"] . "'>" . $row_language["lang"] . "</option>";
                    } ?>
                </select>
                <?php if (isset($_POST['submit'])) {
                    $c1 = $_POST['source'];
                    $acttObj->editFun($table, $edit_id, 'source', $c1);
                } ?>
                <?php if (isset($_POST['submit'])) {
                    $c2 = $_POST['target'];
                    $acttObj->editFun($table, $edit_id, 'target', $c2);
                } ?>
            </div>
            <div class="form-group col-md-3 col-sm-6" id="div_tc">
                <label>Select Document Type</label>
                <select name="docType" id="docType" class="form-control" onchange="get_trans_types($(this));">
                    <?php
                    $q_trans_cat = $acttObj->read_all("tc_id,tc_title", "trans_cat", "tc_status=1 ORDER BY tc_title ASC");
                    $opt_tc = "";
                    while ($row_tc = $q_trans_cat->fetch_assoc()) {
                        $tc_id = $row_tc["tc_id"];
                        $tc_title = $row_tc["tc_title"];
                        $opt_tc .= "<option value='$tc_id'>" . $tc_title . "</option>";
                    }
                    ?>
                    <?php if (isset($docType) && $docType != '8') { ?>
                        <option selected value="<?php echo $docType; ?>"><?php echo $acttObj->read_specific("tc_title", "trans_cat", "tc_id=" . $docType)['tc_title']; ?></option>
                    <?php } ?>
                    <option disabled value="8">Select Translation Category</option>
                    <?php echo $opt_tc; ?>
                </select>
            </div>
            <div class="form-group col-md-3 col-sm-6" id="div_tt">
                <label>Select Translation Type</label>
                <select name="trans_detail[]" multiple="multiple" id="trans_detail" class="form-control multi_class" required>
                    <?php $q_tt = $acttObj->read_all('tt_id,tt_title', 'trans_types', "tc_id='$docType' AND tt_id NOT IN ($trans_detail) ORDER BY tt_title ASC");
                    $arr_trans_detail = explode(',', $trans_detail);
                    for ($tt_i = 0; $tt_i < count($arr_trans_detail); $tt_i++) {
                        $option_tt .= "<option selected value='$arr_trans_detail[$tt_i]'>" . $acttObj->read_specific("tt_title", "trans_types", "tt_id=" . $arr_trans_detail[$tt_i])['tt_title'] . "</option>";
                    }
                    echo $option_tt;
                    while ($row_tt = $q_tt->fetch_assoc()) {
                        echo '<option value="' . $row_tt['tt_id'] . '">' . $row_tt['tt_title'] . '</option>';
                    } ?>
                </select>
            </div>
            <div class="form-group col-md-3 col-sm-6" id="div_td">
                <label>Select Translation Category</label>
                <select name="transType[]" multiple="multiple" id="transType" class="form-control multi_class" required>
                    <?php $q_td = $acttObj->read_all('td_id,td_title', 'vw_translation', "tc_id='$docType' AND td_id NOT IN ($transType) ORDER BY td_title ASC");
                    $arr_transType = explode(',', $transType);
                    for ($td_i = 0; $td_i < count($arr_transType); $td_i++) {
                        $option_td .= "<option selected value='$arr_transType[$td_i]'>" . $acttObj->read_specific("td_title", "trans_dropdown", "td_id=" . $arr_transType[$td_i])['td_title'] . "</option>";
                    }
                    echo $option_td;
                    while ($row_td = $q_td->fetch_assoc()) {
                        echo '<option value="' . $row_td['td_id'] . '">' . $row_td['td_title'] . '</option>';
                    } ?>
                </select>
            </div>
            <?php if (isset($_POST['submit'])) {
                $c_docType = $_POST['docType'];
                $acttObj->editFun($table, $edit_id, 'docType', $c_docType);
            }
            if (isset($_POST['submit'])) {
                $c_transType = implode(",", $_POST['transType']);
                $acttObj->editFun($table, $edit_id, 'transType', $c_transType);
            }
            if (isset($_POST['submit'])) {
                $c_trans_detail = implode(",", $_POST['trans_detail']);
                $acttObj->editFun($table, $edit_id, 'trans_detail', $c_trans_detail);
            } ?>
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

                function get_trans_types(elem) {
                    var tc_id = elem.val();
                    $.ajax({
                        url: 'ajax_add_interp_data.php',
                        method: 'post',
                        dataType: "json",
                        data: {
                            tc_id: tc_id
                        },
                        success: function(data) {
                            $('#div_tt').css('display', 'block');
                            $('#div_td').css('display', 'block');
                            $('#div_tt').html(data[0]);
                            $('#div_td').html(data[1]);
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
                <label>Booking Ref * </label>
                <input title="LSUK Booking Reference" class="form-control" name="nameRef" type="text" value="<?php echo $nameRef; ?>" required='' readonly="readonly" />
                <?php if (isset($_POST['submit']) && $duplicate == "yes") {
                    $month = substr($month, 0, 3);
                    $c5 = 'LSUK/' . $month . '/' . $reference_no;
                    $acttObj->editFun($table, $edit_id, 'nameRef', $c5);
                }
                ?>
            </div>

            <div class="form-group col-sm-6">
                <label>Old Values</label>
                <table class="table table-bordered">
                    <tr>
                        <td><small><?php echo $acttObj->read_specific("tc_title", "trans_cat", "tc_id=" . $docType)['tc_title']; ?></small></td>
                        <td><small><?php echo $acttObj->read_specific("GROUP_CONCAT(CONCAT(td_title)  SEPARATOR '<br>') as td_title", "trans_dropdown", "td_id IN (" . $transType . ")")['td_title']; ?></small></td>
                        <td><small><?php echo $acttObj->read_specific("GROUP_CONCAT(CONCAT(tt_title)  SEPARATOR '<br>') as tt_title", "trans_types", "tt_id IN (" . $trans_detail . ")")['tt_title']; ?></small></td>
                    </tr>
                </table>
            </div>
            <div class="bg-info col-xs-12 form-group">
                <h4>MORE INFORMATION</h4>
            </div>
            <div class="form-group col-md-4 col-sm-6">
                <label class="optional">Delivery Type </label>
                <select name="deliveryType" id="deliveryType" class="form-control">
                    <option><?php echo $deliveryType; ?></option>
                    <option value="Nil">--Select--</option>
                    <option>Standard Service (1 -2 Weeks)</option>
                    <option>Quick Service (2-3 Days)</option>
                    <option>Emergency Service (1-2 Days)</option>
                </select>
            </div>
            <div class="form-group col-md-4 col-sm-6">
                <label class="optional">Delivery Date (For Client)
                </label>
                <input name="deliverDate" required type="date" class="form-control" value="<?php echo $deliverDate; ?>" />
                <?php if (isset($_POST['submit'])) {
                    $post_deliveryType = $_POST['deliveryType'];
                    $acttObj->editFun($table, $edit_id, 'deliveryType', $post_deliveryType);
                } ?>
                <?php if (isset($_POST['submit'])) {
                    $cdc = $_POST['deliverDate'];
                    $acttObj->editFun($table, $edit_id, 'deliverDate', $cdc);
                } ?>
            </div>
            <div class="form-group col-md-4 col-sm-6">
                <label class="optional">Delivery Date (For Interpreter)</label>
                <input name="deliverDate2" required type="date" class="form-control" value="<?php echo $deliverDate2; ?>" />
                <?php if (isset($_POST['submit'])) {
                    $cdi = $_POST['deliverDate2'];
                    $acttObj->editFun($table, $edit_id, 'deliverDate2', $cdi);
                } ?>
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
                <?php if (isset($_POST['submit']) && trim($_POST['orgName']) != "") {
                    $c1 = $_POST['orgName'];
                    $acttObj->editFun($table, $edit_id, 'orgName', $c1);
                    $acttObj->editFun($table, $edit_id, 'order_company_id', $_POST['order_company_id']);
                } ?>
                <label class="new_company <?php echo $orgName == 'LSUK_Private Client' ? '' : 'hidden' ?>" style="margin-top: 12px;"><input onchange="new_company_fields(this)" name="new_company_checkbox" class="new_company_checkbox" type="checkbox" value="1" <?php echo $new_company_id != 0 ? 'checked' : '' ?>> Register as new company</label>
            </div>
            <?php TestCode::LoadHtml("joblistcreditlimit.html"); ?>
            <div class="form-group col-md-3 col-sm-6 search-box">
                <label class="optional">Client Booking Ref/Name</label>
                <input value="<?php echo $orgRef ?>" name="orgRef" id="orgRef" type="text" required='' class="form-control" autocomplete="off" placeholder="Search Org Reference" />
                <i id="confirm_orgRef" style="display:none;position: absolute;right: 25px;top: 35px;" onclick="$(this).hide();$('.result').empty();" class="glyphicon glyphicon-ok-sign text-success" title="Confirm this reference"></i>
                <div class="result"></div>
                <?php if (isset($_POST['submit'])) {
                    $c1 = $_POST['orgRef'];
                    $acttObj->editFun($table, $edit_id, 'orgRef', $c1);
                    $ref_counter = $acttObj->read_specific("count(*) as counter", "comp_ref", "company='" . $_POST['orgName'] . "' AND reference='" . $c1 . "'")['counter'];
                    if ($ref_counter == 0 && !empty($c1)) {
                        $get_reference_id = $acttObj->get_id("comp_ref");
                        $acttObj->update("comp_ref", array("company" => $_POST['orgName'], "reference" => $c1), array("id" => $get_reference_id));
                        $acttObj->editFun($table, $edit_id, 'reference_id', $get_reference_id);
                    } else {
                        $existing_ref_id = $acttObj->read_specific("id", "comp_ref", "company='" . $_POST['orgName'] . "' AND reference='" . $c1 . "'")['id'];
                        $acttObj->editFun($table, $edit_id, 'reference_id', $existing_ref_id);
                    }
                } ?>
            </div>
            <div class="form-group col-md-3 col-sm-6">
                <label>Booking Person Name&nbsp;* </label>
                <input name="orgContact" id="orgContact" type="text" value="<?php echo $orgContact; ?>" placeholder='' required='' class="form-control" />
                <?php if (isset($_POST['submit'])) {
                    $c1 = $_POST['orgContact'];
                    $acttObj->editFun($table, $edit_id, 'orgContact', $c1);
                } ?>
            </div>
            <div class="bg-info col-xs-12 form-group">
                <h4>CONTACT DETAILS</h4>
            </div>

            <!--Purchase order on off-->
            <div class="form-group col-md-4 col-sm-6 <?php if ($po_req == 0) {
                                                            echo 'hidden';
                                                        } ?>" id="div_check_po">
                <label>Do you have purchase order number?</label>
                <br><span class="col-md-offset-2">
                    <label class="checkbox-inline" style="margin-top: 4px;border: 1px solid lightgrey;padding: 2px 10px;"><input <?php if ($po_req == 1 && !empty($porder)) {
                                                                                                                                        echo 'checked';
                                                                                                                                    } ?> style="transform: scale(1.2);" onchange="booking_purch_order();" type="radio" name="po_number" value="1"> Yes</label>
                    <label class="checkbox-inline" style="border: 1px solid lightgrey;padding: 2px 10px;"><input <?php if ($po_req == 1 && empty($porder)) {
                                                                                                                        echo 'checked';
                                                                                                                    } ?> style="transform: scale(1.2);" onchange="booking_purch_order();" type="radio" name="po_number" value="0"> No</label>
                </span>
            </div>
            <div class="form-group col-md-4 col-sm-6 <?php if (($po_req == 0) || ($po_req == 1 && empty($porder))) {
                                                            echo 'hidden';
                                                        } ?> search-box" id="div_po_number">
                <label class="optional">Enter purchase order number</label>
                <input name="purchase_order_number" id="purchase_order_number" type="text" class="form-control" autocomplete="off" placeholder="Search purchase order number" <?php if ($po_req == 1 && !empty($porder)) { ?> value="<?php echo $porder; ?>" <?php } ?> />
                <i id="confirm_po" style="display:none;position: absolute;right: 15px;top: 26px;" onclick="$(this).hide();$(this).next('.result').empty();" class="btn btn-info btn-sm glyphicon glyphicon-ok-sign text-success confirm_element" title="Confirm this purchase order number"></i>
                <div class="result"></div>
            </div>
            <div id="div_po_req" class="form-group <?php if (($po_req == 0) || ($po_req == 1 && !empty($porder))) {
                                                        echo 'hidden';
                                                    } ?> col-md-4 col-sm-6">
                <label>Purchase Order Email Address </label>
                <input oninput="$('#write_po_email').html($(this).val());if($(this).val()){$('.tr_po_email').removeClass('hidden');}" name="po_req" id="po_req" type="text" class="long form-control" placeholder='Fill email for purchase order' <?php if ($po_req == 1) { ?>required value="<?php echo $porder_email; ?>" data-value="<?php echo $porder_email; ?>" <?php } ?> />
            </div>
            <div class="row"></div>

            <div class="form-group col-md-4 col-sm-6 div_new_company <?php echo $new_company_id == 0 ? 'hidden' : '' ?>">
                <label class="optional"> Company Name </label>
                <input name="new_company_name" id="new_company_name" type="text" class="form-control" value="<?php echo $private_company_name ?>" data-value="<?php echo $private_company_name ?>" />
            </div>
            <div class="form-group col-md-4 col-sm-6">
                <label class="optional">Contact Number&nbsp;</label>
                <input name="inchContact" id="inchContact" type="text" class="form-control long" value="<?php echo $inchContact; ?>" data-value="<?php echo $inchContact ?>" />
                <?php if (isset($_POST['submit'])) {
                    $c1 = $_POST['inchContact'];
                    $acttObj->editFun($table, $edit_id, 'inchContact', $c1);
                } ?>
            </div>
            <div class="form-group col-md-4 col-sm-6">
                <label class="optional">Booking Confirmation Email #1</label>
                <input oninput="$('#write_booking_email').html($(this).val());" name="inchEmail" id="inchEmail" type="text" value="<?php echo $inchEmail ?>" data-value="<?php echo $inchEmail ?>" class="long form-control" />
                <?php if (isset($_POST['submit'])) {
                    $c1 = $_POST['inchEmail'];
                    $acttObj->editFun($table, $edit_id, 'inchEmail', $c1);
                } ?>
            </div>
            <div class="form-group col-md-4 col-sm-6">
                <label class="optional">Booking Confirmation Email #2</label>
                <input name="inchEmail2" id="inchEmail2" type="text" value="<?php echo $inchEmail2 ?>" data-value="<?php echo $inchEmail2 ?>" placeholder='' class="long form-control" />
                <?php if (isset($_POST['submit'])) {
                    $cem2 = $_POST['inchEmail2'];
                    $acttObj->editFun($table, $edit_id, 'inchEmail2', $cem2);
                } ?>
            </div>
            <div class="form-group col-md-4 col-sm-6">
                <label class="optional">Building Number / Name
                </label>
                <input name="inchNo" id="inchNo" type="text" value="<?php echo $inchNo ?>" data-value="<?php echo $inchNo ?>" placeholder='' class="form-control" />
                <?php if (isset($_POST['submit'])) {
                    $c14 = $_POST['inchNo'];
                    $acttObj->editFun($table, $edit_id, 'inchNo', $c14);
                } ?>
            </div>
            <div class="form-group col-md-4 col-sm-6">
                <label class="optional">Address Line 1 </label>
                <input name="line1" id="line1" type="text" placeholder='' value="<?php echo $line1 ?>" data-value="<?php echo $line1 ?>" class="form-control" />
                <?php if (isset($_POST['submit'])) {
                    $c14 = $_POST['line1'];
                    $acttObj->editFun($table, $edit_id, 'line1', $c14);
                } ?>
            </div>
            <div class="form-group col-md-4 col-sm-6">
                <label class="optional">Address Line 2 </label>
                <input name="line2" id="line2" type="text" placeholder='' value="<?php echo $line2 ?>" data-value="<?php echo $line2 ?>" class="form-control" />
                <?php if (isset($_POST['submit'])) {
                    $c14 = $_POST['line2'];
                    $acttObj->editFun($table, $edit_id, 'line2', $c14);
                } ?>
            </div>
            <div class="form-group col-md-4 col-sm-6">
                <label class="optional">Address Line 3</label>
                <input name="inchRoad" id="inchRoad" type="text" value="<?php echo $inchRoad ?>" data-value="<?php echo $inchRoad ?>" placeholder='' class="form-control" />
                <?php if (isset($_POST['submit'])) {
                    $c15 = $_POST['inchRoad'];
                    $acttObj->editFun($table, $edit_id, 'inchRoad', $c15);
                } ?>
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
                <?php if (isset($_POST['submit'])) {
                    $c16 = $_POST['inchCity'];
                    $acttObj->editFun($table, $edit_id, 'inchCity', $c16);
                } ?>
            </div>
            <div class="form-group col-md-4 col-sm-6">
                <label class="optional">Post Code </label>
                <input name="inchPcode" id="inchPcode" type="text" value="<?php echo $inchPcode ?>" data-value="<?php echo $inchPcode ?>" class="form-control" />
                <?php if (isset($_POST['submit'])) {
                    $c17 = $_POST['inchPcode'];
                    $acttObj->editFun($table, $edit_id, 'inchPcode', $c17);
                } ?>
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
                                <tr class="tr_po_email <?php if ($po_req == 0) {
                                                            echo 'hidden';
                                                        } ?>">
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
                <textarea placeholder="Notes for Interpreter ..." id="remrks" name="remrks" class="form-control" rows="3"><?php echo !$duplicate ? $remrks : ""; ?></textarea>
                <input name="job_note" type="checkbox" value="1" /> Check to save as Job Note ?
                <?php if (isset($_POST['submit']) && !empty($_POST['remrks'])) {
                    $post_remrks = $_POST['remrks'];
                    $acttObj->editFun($table, $edit_id, 'remrks', $post_remrks);
                    if (isset($_POST['job_note']) && !empty($_POST['job_note']) && !empty($_POST['remrks'])) {
                        $acttObj->insert('jobnotes', array('jobNote' => $post_remrks, 'tbl' => $table, 'time' => $misc->sys_datetime_db(), 'fid' => $edit_id, 'submitted' => $_SESSION['UserName'], 'dated' => date('Y-m-d')));
                    }
                } ?>
            </div>
            <div class="form-group col-sm-6">
                <label><small>Notes for Client</small></label>
                <textarea class="form-control" name="I_Comments" rows="3" id="I_Comments"><?php echo !$duplicate ? $I_Comments : ""; ?></textarea>
                <input name="job_note_c" type="checkbox" value="1" /> Check to save as Job Note ?
                <?php if (isset($_POST['submit']) && !empty($_POST['I_Comments'])) {
                    $post_I_Comments = $_POST['I_Comments'];
                    $acttObj->editFun($table, $edit_id, 'I_Comments', $post_I_Comments);
                    if (isset($_POST['job_note_c']) && !empty($_POST['job_note_c']) && !empty($_POST['I_Comments'])) {
                        $acttObj->insert('jobnotes', array('jobNote' => $post_I_Comments, 'tbl' => $table, 'time' => $misc->sys_datetime_db(), 'fid' => $edit_id, 'submitted' => $_SESSION['UserName'], 'dated' => date('Y-m-d')));
                    }
                } ?>
            </div>
            <?php
            if (isset($_POST['submit'])) {
                if (isset($_POST['new_company_checkbox']) && $_POST['orgName'] == "LSUK_Private Client") {
                    if ($new_company_id != 0) {
                        $acttObj->update("private_company", array("name" => $_POST['new_company_name'], "inchPerson" => $_POST['orgContact'], "inchContact" => $_POST['inchContact'], "inchEmail" => $_POST['inchEmail'], "inchEmail2" => $_POST['inchEmail2'], "inchNo" => $_POST['inchNo'], "line1" => $_POST['line1'], "line2" => $_POST['line2'], "inchRoad" => $_POST['inchRoad'], "inchCity" => $_POST['inchCity'], "inchPcode" => $_POST['inchPcode']), array("id" => $new_company_id));
                    } else {
                        $new_company_id = $acttObj->get_id("private_company");
                        $acttObj->update("private_company", array("name" => $_POST['new_company_name'], "inchPerson" => $_POST['orgContact'], "inchContact" => $_POST['inchContact'], "inchEmail" => $_POST['inchEmail'], "inchEmail2" => $_POST['inchEmail2'], "inchNo" => $_POST['inchNo'], "line1" => $_POST['line1'], "line2" => $_POST['line2'], "inchRoad" => $_POST['inchRoad'], "inchCity" => $_POST['inchCity'], "inchPcode" => $_POST['inchPcode']), array("id" => $new_company_id));
                        $acttObj->editFun($table, $edit_id, 'new_comp_id', $new_company_id);
                    }
                }
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
                        }else {
                            $acttObj->editFun($table, $edit_id, 'porder', $purchase_order_number);
                        }
                    }else{
                        $acttObj->editFun($table, $edit_id, 'porder', $purchase_order_number);
                    }
                }
            } ?>
            <div class="bg-info col-xs-12 form-group">
                <h4>INVOICE DETAILS</h4>
            </div>
            <div class="form-group col-md-4 col-sm-6">
                <label>Assignment Date </label>
                <input name="asignDate" id="assignDate" type="date" class="form-control" value="<?php echo $asignDate; ?>" />
                <?php
                if (isset($_POST['submit'])) {
                    $c3 = $_POST['asignDate'];
                    $acttObj->editFun($table, $edit_id, 'asignDate', $c3);
                }
                ?>

            </div>
            <?php
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
                <?php
                if (isset($_POST['submit'])) {
                    $c22 = $_POST['bookinType'];
                    $acttObj->editFun($table, $edit_id, 'bookinType', $c22);
                    if ($_POST['company_rate_id']) {
                        $company_rate_id = $_POST['company_rate_id'];
                        $acttObj->editFun($table, $edit_id, 'company_rate_id', $company_rate_id);
                        $company_rate_data = $_POST['company_rate_data'];
                        $acttObj->editFun($table, $edit_id, 'company_rate_data', $company_rate_data);
                    }
                }
                ?>
            </div>
            <div class="form-group col-md-4 col-sm-6">
                <label class="optional">Gender</label>
                <select name="gender" id="gender" required class="form-control">
                    <option><?php echo empty($gender)?'No Preference':$gender; ?></option>
                    <option value="">--Select--</option>
                    <option>Male</option>
                    <option>Female</option>
                    <option>No Preference</option>
                </select>
            </div>
            <?php if (isset($_POST['submit'])) {
                    $c290 = $_POST['gender'];
                    $acttObj->editFun($table, $edit_id, 'gender', $c290);
                } ?>
            <div class="form-group col-md-4 col-sm-6">
                <label class="optional">STATUS: </label><br>
                <div class="radio-inline ri"><label><input name="jobStatus" type="radio" value="0" <?php if ($jobStatus == '0') { ?> checked="checked" <?php } ?> />
                        <span class="label label-danger">Enquiry <i class="fa fa-question"></i></span></label></div>
                <div class="radio-inline ri"><label><input type="radio" name="jobStatus" value="1" <?php if ($jobStatus == '1') { ?> checked="checked" <?php } ?> />
                        <span class="label label-success">Confirmed <i class="fa fa-check-circle"></i></span></label></div>
                <?php if (isset($_POST['submit'])) {
                    echo $c22 = $_POST['jobStatus'];
                    if($c22!=$jobStatus){
                        if($c22=='0'){
                            $acttObj->insert("daily_logs", array("action_id" => 43, "user_id" => $_SESSION['userId'], "details" => "Status Shifted to Enquiry: " . $edit_id));
                        }else if($c22=='1'){
                            $acttObj->insert("daily_logs", array("action_id" => 34, "user_id" => $_SESSION['userId'], "details" => "Job Confirmed: " . $edit_id));
                        }
                    }
                    $acttObj->editFun($table, $edit_id, 'jobStatus', $c22);
                    $noty_reason_post = $_POST['selector'] == 'sc' ? $_POST['selector_reason'] : '';
                    $acttObj->editFun($table, $edit_id, 'noty_reason', $noty_reason_post);
                    $noty_post = $_POST['selector'] == 'sc' ? implode(',', $_POST['selected_interpreters']) : '';
                    if ($row['noty'] != $noty_post && $_POST['selected_interpreters']) {
                        $reason_title = $_POST['selector'] == 'sc' && $_POST['selector_reason'] ? '<br>Reason: ' . $_POST['selector_reason'] : '';
                        $interpreter_names = $acttObj->read_specific("GROUP_CONCAT(name) as names", "interpreter_reg", "id IN (" . $noty_post . ")")['names'];
                        $acttObj->insert('jobnotes', array('jobNote' => "Notified interpreters: " . $interpreter_names . $reason_title, 'tbl' => $table, 'time' => $misc->sys_datetime_db(), 'fid' => $edit_id, 'submitted' => $_SESSION['UserName'], 'dated' => date('Y-m-d')));
                    }
                    $acttObj->editFun($table, $edit_id, 'noty', $noty_post);
                } ?>
            </div>
            <div class="form-group col-md-4 col-sm-6">
                <label class="optional">SEND AUTO REMINDER ?</label><br>
                <div class="radio-inline ri" onclick="disabler(1);"><label><input name="jobDisp" type="radio" value="1" <?php if ($jobDisp == '1') { ?> checked="checked" <?php } ?> />
                        <span class="label label-success">Yes <i class="fa fa-check-circle"></i></span></label></div>
                <div class="radio-inline ri" onclick="disabler(0);"><label><input type="radio" name="jobDisp" value="0" <?php if ($jobDisp == '0') { ?> checked="checked" <?php } ?> />
                        <span class="label label-danger">No <i class="fa fa-remove"></i></span></label></div>
                <?php if (isset($_POST['submit'])) {
                    $c22 = $_POST['jobDisp'];
                    $acttObj->editFun($table, $edit_id, 'jobDisp', $c22);
                } ?>
                <br><br><br><br><br><br>
            </div>
            <div id="div_selector" class="form-group col-md-3 col-sm-6 selector <?php if ($jobDisp == '0') { echo 'hidden';} ?>">
                <label>Send reminders to</label>
                <select id="selector" onchange="changable()" class="form-control" name="selector">
                    <option value='all'>All Interpreters</option>
                    <option value='sc' <?php if (!empty($noty)) { echo 'selected';} ?>>Specific Interpreters</option>
                </select>
            </div>
            <div id="div_selector_reason" class="t form-group col-md-3 col-sm-6 <?php if (empty($noty_reason) || $jobDisp == '0') { echo 'hidden';} ?>">
                <label>Reason For Specific Selection</label>
                <select id="selector_reason" class="form-control" name="selector_reason">
                    <?php if (!empty($noty_reason)) { ?><option value='<?php echo $noty_reason; ?>' selected><?php echo $noty_reason; ?></option><?php } ?>
                    <option value='' disabled <?php if (empty($noty_reason)) { echo 'selected';} ?>> --- Choose Reason --- </option>
                    <option value='Regular Job'>Regular Job</option>
                    <option value='Requested Job'>Requested Job</option>
                    <option value='Other'>Other</option>
                </select>
            </div>
            <div id="div_specific" class="form-group col-md-3 col-sm-6 <?php if (empty($noty) || $jobDisp == '0') { echo 'hidden';} ?>">
                <label class="optional" sttyle="display:block;">Selected Interpreters</label>
                <select class="multi_class" id="selected_interpreters" name="selected_interpreters[]" multiple="multiple">
                    <?php if (!empty($noty)) {
                        $res_noty = $acttObj->read_all("id,name,gender,city", "interpreter_reg", "id IN ($noty)");
                        while ($row_noty = mysqli_fetch_assoc($res_noty)) { ?>
                            <option selected value="<?php echo $row_noty['id']; ?>"><?php echo $row_noty['name'] . ' (' . $row_noty['gender'] . ')' . ' (' . $row_noty['city'] . ')'; ?></option>
                    <?php }
                    } ?>
                    <?php 
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
                        $query_ints = "SELECT DISTINCT interpreter_reg.id,interpreter_reg.name,interpreter_reg.gender,interpreter_reg.city FROM interpreter_reg,interp_lang WHERE interpreter_reg.code=interp_lang.code AND (SELECT COUNT(DISTINCT interp_lang.lang) FROM interp_lang WHERE interp_lang.type='trans' AND interp_lang.lang IN ('" . $source . "') and interp_lang.level<3 and interp_lang.code=interpreter_reg.code)=1 and 
                        interpreter_reg.active='0' AND (interpreter_reg.actnow='Active' OR (interpreter_reg.actnow='Inactive' AND CURRENT_DATE() NOT BETWEEN interpreter_reg.actnow_time AND interpreter_reg.actnow_to)) AND interpreter_reg.trans='Yes' $append_gender AND interpreter_reg.deleted_flag=0 AND interpreter_reg.subscribe=1 AND interpreter_reg.is_temp=0 AND interpreter_reg.isAdhoc=0 ORDER BY name ASC";
                    } else if ($q_style == '1') {
                        $query_ints = "SELECT DISTINCT interpreter_reg.id,interpreter_reg.name,interpreter_reg.gender,interpreter_reg.city FROM interpreter_reg,interp_lang WHERE interpreter_reg.code=interp_lang.code AND (SELECT COUNT(DISTINCT interp_lang.lang) FROM interp_lang WHERE interp_lang.type='trans' AND interp_lang.lang IN ('" . $source . "','" . $target . "') and interp_lang.level<3 and interp_lang.code=interpreter_reg.code)=2 and 
                        interpreter_reg.active='0' AND (interpreter_reg.actnow='Active' OR (interpreter_reg.actnow='Inactive' AND CURRENT_DATE() NOT BETWEEN interpreter_reg.actnow_time AND interpreter_reg.actnow_to)) AND interpreter_reg.trans='Yes' $append_gender AND interpreter_reg.deleted_flag=0 AND interpreter_reg.subscribe=1 AND interpreter_reg.is_temp=0 AND interpreter_reg.isAdhoc=0 ORDER BY name ASC";
                    } else if ($q_style == '2') {
                        $query_ints = "SELECT DISTINCT interpreter_reg.id,interpreter_reg.name,interpreter_reg.gender,interpreter_reg.city FROM interpreter_reg,interp_lang WHERE interpreter_reg.code=interp_lang.code AND interp_lang.type='trans' AND $append_lang and 
                        interpreter_reg.active='0' AND (interpreter_reg.actnow='Active' OR (interpreter_reg.actnow='Inactive' AND CURRENT_DATE() NOT BETWEEN interpreter_reg.actnow_time AND interpreter_reg.actnow_to)) AND interpreter_reg.trans='Yes' $append_gender AND interpreter_reg.deleted_flag=0 AND interpreter_reg.subscribe=1 AND interpreter_reg.is_temp=0 AND interpreter_reg.isAdhoc=0 ORDER BY name ASC";
                    } else {
                        $query_ints = "SELECT DISTINCT interpreter_reg.id,interpreter_reg.name,interpreter_reg.gender,interpreter_reg.city FROM interpreter_reg WHERE 
                        interpreter_reg.active='0' AND (interpreter_reg.actnow='Active' OR (interpreter_reg.actnow='Inactive' AND CURRENT_DATE() NOT BETWEEN interpreter_reg.actnow_time AND interpreter_reg.actnow_to)) AND interpreter_reg.trans='Yes' $append_gender AND interpreter_reg.deleted_flag=0 AND interpreter_reg.subscribe=1 AND interpreter_reg.is_temp=0 AND interpreter_reg.isAdhoc=0 ORDER BY name ASC";
                    }
                    $res_ints = mysqli_query($con, $query_ints);
                    while ($row_ints = mysqli_fetch_assoc($res_ints)) { ?>
                        <option value="<?php echo $row_ints['id']; ?>"><?php echo $row_ints['name'] . ' (' . $row_ints['gender'] . ')' . ' (' . $row_ints['city'] . ')'; ?></option>
                    <?php } ?>
                </select>
            </div>

        </form>
        <script src="ckeditor/ckeditor/ckeditor.js"></script>
        <script type="text/javascript">
            g_strJobTableIs = "<?php echo $table; ?>";
            CKEDITOR.replace('remrks', {
                height: '150px',
            });
            CKEDITOR.replace('I_Comments', {
                height: '150px',
            });
        </script>
        <script type="text/javascript" src="ajax.js"></script>
        <?php
        if (isset($_POST['submit']) && empty($invoiceNo) && $duplicate != 'yes') {
            $acttObj->UpdateInvoiceNo($invoiceNo, $table, $edit_id);
        }
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
            //... Who Created this duplicate entry..........//
            $acttObj->editFun($table, $edit_id, 'submited', ucwords($_SESSION['UserName']));
            //Email notification to related interpreters
            $jobDisp_req = $_POST['jobDisp'];
            $jobStatus_req = $_POST['jobStatus'];
            if ($jobDisp_req == '1' && $jobStatus_req == '1' && $is_temp == '0') {
                $source_lang_req = $_POST['source'];
                $assignDate_req = $misc->dated($_POST['asignDate']);
                $target_lang_req = $_POST['target'];
                $post_remrks = $post_remrks ?: '';
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
<td style='border: 1px solid yellowgreen;padding:5px;'>Document Type</td>
<td style='border: 1px solid yellowgreen;padding:5px;'>" . $acttObj->read_specific("tc_title", "trans_cat", "tc_id=" . $c_docType)['tc_title'] . "</td>
</tr>
<tr>
<td style='border: 1px solid yellowgreen;padding:5px;'>Translation Type(s)</td>
<td style='border: 1px solid yellowgreen;padding:5px;'>" . $acttObj->read_specific("GROUP_CONCAT(CONCAT(tt_title)  SEPARATOR ' <b> & </b> ') as tt_title", "trans_types", "tt_id IN (" . $c_trans_detail . ")")['tt_title'] . "</td>
</tr>
<tr>
<td style='border: 1px solid yellowgreen;padding:5px;'>Translation Category</td>
<td style='border: 1px solid yellowgreen;padding:5px;'>" . $acttObj->read_specific("GROUP_CONCAT(CONCAT(td_title)  SEPARATOR ' <b> & </b> ') as td_title", "trans_dropdown", "td_id IN (" . $c_transType . ")")['td_title'] . "</td>
</tr>
<tr>
<td style='border: 1px solid yellowgreen;padding:5px;'>Delivery Date</td>
<td style='border: 1px solid yellowgreen;padding:5px;'>" . $misc->dated($cdi) . "</td>
</tr>
<tr>
<td style='border: 1px solid yellowgreen;padding:5px;'>Delivery Type</td>
<td style='border: 1px solid yellowgreen;padding:5px;'>" . $post_deliveryType . "</td>
</tr>
</table>";
                if (!empty($post_remrks)) {
                    $append_table .= "<br><u><b>NOTES FOR THIS JOB:</b></u><br>" . $post_remrks . "<br>";
                }
                if ($gender == '' || $gender == 'No Preference') {
                    $append_gender = "";
                } else {
                    $append_gender = " AND interpreter_reg.gender='$gender'";
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
                    $query_emails = "SELECT DISTINCT interpreter_reg.name,interpreter_reg.gender, interpreter_reg.email, interpreter_reg.id FROM interpreter_reg,interp_lang WHERE interpreter_reg.code=interp_lang.code AND (SELECT COUNT(DISTINCT interp_lang.lang) FROM interp_lang WHERE interp_lang.type='trans' AND interp_lang.lang IN ('" . $source_lang_req . "') and interp_lang.level<3 and interp_lang.code=interpreter_reg.code)=1 and 
                    interpreter_reg.active='0' AND (interpreter_reg.actnow='Active' OR (interpreter_reg.actnow='Inactive' AND CURRENT_DATE() NOT BETWEEN interpreter_reg.actnow_time AND interpreter_reg.actnow_to)) AND interpreter_reg.trans='Yes' $append_gender AND interpreter_reg.deleted_flag=0 AND interpreter_reg.subscribe=1 AND interpreter_reg.is_temp=0 AND interpreter_reg.isAdhoc=0";
                } else if ($query_style == '1') {
                    $query_emails = "SELECT DISTINCT interpreter_reg.name,interpreter_reg.gender, interpreter_reg.email, interpreter_reg.id FROM interpreter_reg,interp_lang WHERE interpreter_reg.code=interp_lang.code AND (SELECT COUNT(DISTINCT interp_lang.lang) FROM interp_lang WHERE interp_lang.type='trans' AND interp_lang.lang IN ('" . $source_lang_req . "','" . $target_lang_req . "') and interp_lang.level<3 and interp_lang.code=interpreter_reg.code)=2 and 
                    interpreter_reg.active='0' AND (interpreter_reg.actnow='Active' OR (interpreter_reg.actnow='Inactive' AND CURRENT_DATE() NOT BETWEEN interpreter_reg.actnow_time AND interpreter_reg.actnow_to)) AND interpreter_reg.trans='Yes' $append_gender AND interpreter_reg.deleted_flag=0 AND interpreter_reg.subscribe=1 AND interpreter_reg.is_temp=0 AND interpreter_reg.isAdhoc=0";
                } else if ($query_style == '2') {
                    $query_emails = "SELECT DISTINCT interpreter_reg.name,interpreter_reg.gender, interpreter_reg.email, interpreter_reg.id FROM interpreter_reg,interp_lang WHERE interpreter_reg.code=interp_lang.code AND interp_lang.type='trans' AND $put_lang and 
                    interpreter_reg.active='0' AND (interpreter_reg.actnow='Active' OR (interpreter_reg.actnow='Inactive' AND CURRENT_DATE() NOT BETWEEN interpreter_reg.actnow_time AND interpreter_reg.actnow_to)) AND interpreter_reg.trans='Yes' $append_gender AND interpreter_reg.deleted_flag=0 AND interpreter_reg.subscribe=1 AND interpreter_reg.is_temp=0 AND interpreter_reg.isAdhoc=0";
                } else {
                    $query_emails = "SELECT DISTINCT interpreter_reg.name,interpreter_reg.gender, interpreter_reg.email, interpreter_reg.id FROM interpreter_reg WHERE 
                    interpreter_reg.active='0' AND (interpreter_reg.actnow='Active' OR (interpreter_reg.actnow='Inactive' AND CURRENT_DATE() NOT BETWEEN interpreter_reg.actnow_time AND interpreter_reg.actnow_to)) AND interpreter_reg.trans='Yes' $append_gender AND interpreter_reg.deleted_flag=0 AND interpreter_reg.subscribe=1 AND interpreter_reg.is_temp=0 AND interpreter_reg.isAdhoc=0";
                }
                if ($_POST['selector'] == 'sc') {
                    $selected_interpreters = implode(',', $_POST['selected_interpreters']);
                    $query_emails .= ' and interpreter_reg.id IN (' . $selected_interpreters . ')';
                }
                $res_emails = mysqli_query($con, $query_emails);
                //Getting bidding email from em_format table
                $row_format = $acttObj->read_specific("em_format", "email_format", "id=27");
                $subject = "New Translation Project " . $edit_id;
                $sub_title = "Translation job of " . $source_lang_req . " language on " . $assignDate_req . " is available for you to bid.";
                $type_key = "nj";
                while ($row_emails = mysqli_fetch_assoc($res_emails)) {
                    if ($acttObj->read_specific("COUNT(*) as blacklisted", "interp_blacklist", "interpName='id-" . $row_emails['id'] . "' AND orgName='" . $_POST['orgName'] . "' AND deleted_flag=0 AND blocked_for=2")["blacklisted"] == 0) {
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
                            //array_push($app_int_ids,$row_emails['id']);
                            foreach ($array_tokens as $token) {
                                if (!empty($token)) {
                                    $acttObj->notify($token, $subject, $sub_title, array("type_key" => $type_key, "job_type" => "Translation"));
                                }
                            }
                        }
                        //Replace date in email bidding 
                        $data   = ["[NAME]", "[ASSIGNDATE]", "[TABLE]", "[EDIT_ID]"];
                        $to_replace  = [$row_emails['name'], "$assignDate_req", "$append_table", "$edit_id"];
                        $message = str_replace($data, $to_replace, $row_format['em_format']);
                        try {
                            $mail->SMTPDebug = 0;
                            $mail->isSMTP();
                            $mail->Host = setupEmail::EMAIL_HOST;
                            $mail->SMTPAuth   = true;
                            $mail->Username   = setupEmail::TRANSLATION_EMAIL;
                            $mail->Password   = setupEmail::TRANSLATION_PASSWORD;
                            $mail->SMTPSecure = setupEmail::SECURE_TYPE;
                            $mail->Port       = setupEmail::SENDING_PORT;
                            $mail->setFrom(setupEmail::TRANSLATION_EMAIL, setupEmail::FROM_NAME);
                            $mail->addAddress($to_address);
                            $mail->addReplyTo(setupEmail::TRANSLATION_EMAIL, setupEmail::FROM_NAME);
                            $mail->isHTML(true);
                            $mail->Subject = $subject;
                            $mail->Body    = $message;
                            $mail->send();
                            $mail->ClearAllRecipients();
                        } catch (Exception $e) { ?>
                            <script>
                                alert("Message could not be sent! Mailer library error.");
                            </script>
                <?php   }
                    }
                }
            } ?>
            <script>
                alert('Job Successfully Submitted!');
                window.close();
                window.onunload = refreshParent;
            </script>
            <?php
            $acttObj->insert("daily_logs", array("action_id" => 1, "user_id" => $_SESSION['userId'], "details" => "TR Job ID: " . $edit_id));
        }
        if (isset($_POST['submit'])  && $duplicate != 'yes') {
            //Email notification to related interpreters
            $jobDisp_req = $_POST['jobDisp'];
            $gender_req = $_POST['gender'];
            $jobStatus_req = $_POST['jobStatus'];
            $jobStatus_check = $acttObj->unique_data('translation', 'jobStatus', 'id', $edit_id);
            if (($jobDisp_req == '1' && $is_temp == '0') && (($jobStatus_check != $jobStatus_req && $jobStatus_req == '1') || ($jobStatus_req == 1 && $jobDisp != $jobDisp_req && $jobDisp_req == '1'))) {
                $source_lang_req = $_POST['source'];
                $assignDate_req = $misc->dated($_POST['asignDate']);
                $target_lang_req = $_POST['target'];
                $post_remrks = $post_remrks ?: '';
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
<td style='border: 1px solid yellowgreen;padding:5px;'>Document Type</td>
<td style='border: 1px solid yellowgreen;padding:5px;'>" . $acttObj->read_specific("tc_title", "trans_cat", "tc_id=" . $c_docType)['tc_title'] . "</td>
</tr>
<tr>
<td style='border: 1px solid yellowgreen;padding:5px;'>Translation Type(s)</td>
<td style='border: 1px solid yellowgreen;padding:5px;'>" . $acttObj->read_specific("GROUP_CONCAT(CONCAT(tt_title)  SEPARATOR ' <b> & </b> ') as tt_title", "trans_types", "tt_id IN (" . $c_trans_detail . ")")['tt_title'] . "</td>
</tr>
<tr>
<td style='border: 1px solid yellowgreen;padding:5px;'>Translation Category</td>
<td style='border: 1px solid yellowgreen;padding:5px;'>" . $acttObj->read_specific("GROUP_CONCAT(CONCAT(td_title)  SEPARATOR ' <b> & </b> ') as td_title", "trans_dropdown", "td_id IN (" . $c_transType . ")")['td_title'] . "</td>
</tr>
<tr>
<td style='border: 1px solid yellowgreen;padding:5px;'>Delivery Date</td>
<td style='border: 1px solid yellowgreen;padding:5px;'>" . $misc->dated($cdi) . "</td>
</tr>
<tr>
<td style='border: 1px solid yellowgreen;padding:5px;'>Delivery Type</td>
<td style='border: 1px solid yellowgreen;padding:5px;'>" . $post_deliveryType . "</td>
</tr>
</table>";
                if (!empty($post_remrks)) {
                    $append_table .= "<br><u><b>NOTES FOR THIS JOB:</b></u><br>" . $post_remrks . "<br>";
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
                    $query_emails = "SELECT DISTINCT interpreter_reg.name, interpreter_reg.email, interpreter_reg.id FROM interpreter_reg,interp_lang WHERE interpreter_reg.code=interp_lang.code AND (SELECT COUNT(DISTINCT interp_lang.lang) FROM interp_lang WHERE interp_lang.type='trans' AND interp_lang.lang IN ('" . $source_lang_req . "') and interp_lang.level<3 and interp_lang.code=interpreter_reg.code)=1 and 
                    interpreter_reg.active='0' AND (interpreter_reg.actnow='Active' OR (interpreter_reg.actnow='Inactive' AND CURRENT_DATE() NOT BETWEEN interpreter_reg.actnow_time AND interpreter_reg.actnow_to)) AND interpreter_reg.trans='Yes' $put_gender AND interpreter_reg.deleted_flag=0 AND interpreter_reg.subscribe=1 AND interpreter_reg.is_temp=0 AND interpreter_reg.isAdhoc=0";
                } else if ($query_style == '1') {
                    $query_emails = "SELECT DISTINCT interpreter_reg.name, interpreter_reg.email, interpreter_reg.id FROM interpreter_reg,interp_lang WHERE interpreter_reg.code=interp_lang.code AND (SELECT COUNT(DISTINCT interp_lang.lang) FROM interp_lang WHERE interp_lang.type='trans' AND interp_lang.lang IN ('" . $source_lang_req . "','" . $target_lang_req . "') and interp_lang.level<3 and interp_lang.code=interpreter_reg.code)=2 and 
                    interpreter_reg.active='0' AND (interpreter_reg.actnow='Active' OR (interpreter_reg.actnow='Inactive' AND CURRENT_DATE() NOT BETWEEN interpreter_reg.actnow_time AND interpreter_reg.actnow_to)) AND interpreter_reg.trans='Yes' $put_gender AND interpreter_reg.deleted_flag=0 AND interpreter_reg.subscribe=1 AND interpreter_reg.is_temp=0 AND interpreter_reg.isAdhoc=0";
                } else if ($query_style == '2') {
                    $query_emails = "SELECT DISTINCT interpreter_reg.name, interpreter_reg.email, interpreter_reg.id FROM interpreter_reg,interp_lang WHERE interpreter_reg.code=interp_lang.code AND interp_lang.type='trans' AND $put_lang and 
                    interpreter_reg.active='0' AND (interpreter_reg.actnow='Active' OR (interpreter_reg.actnow='Inactive' AND CURRENT_DATE() NOT BETWEEN interpreter_reg.actnow_time AND interpreter_reg.actnow_to)) AND interpreter_reg.trans='Yes' $put_gender AND interpreter_reg.deleted_flag=0 AND interpreter_reg.subscribe=1 AND interpreter_reg.is_temp=0 AND interpreter_reg.isAdhoc=0";
                } else {
                    $query_emails = "SELECT DISTINCT interpreter_reg.name, interpreter_reg.email, interpreter_reg.id FROM interpreter_reg WHERE 
                    interpreter_reg.active='0' AND (interpreter_reg.actnow='Active' OR (interpreter_reg.actnow='Inactive' AND CURRENT_DATE() NOT BETWEEN interpreter_reg.actnow_time AND interpreter_reg.actnow_to)) AND interpreter_reg.trans='Yes' $put_gender AND interpreter_reg.deleted_flag=0 AND interpreter_reg.subscribe=1 AND interpreter_reg.is_temp=0 AND interpreter_reg.isAdhoc=0";
                }
                if ($_POST['selector'] == 'sc') {
                    if (!empty($noty)) {
                        $append_noty = ' and interpreter_reg.id NOT IN (' . $noty . ')';
                    }
                    $selected_interpreters = implode(',', $_POST['selected_interpreters']);
                    $query_emails .= ' and interpreter_reg.id IN (' . $selected_interpreters . ') ' . $append_noty;
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
                $row_format = $acttObj->read_specific("em_format", "email_format", "id=27");
                $subject = "New Translation Project " . $edit_id;
                $sub_title = "Translation job of " . $source_lang_req . " language on " . $assignDate_req . " is available for you to bid.";
                $type_key = "nj";
                while ($row_emails = mysqli_fetch_assoc($res_emails)) {
                    if ($acttObj->read_specific("COUNT(*) as blacklisted", "interp_blacklist", "interpName='id-" . $row_emails['id'] . "' AND orgName='" . $_POST['orgName'] . "' AND deleted_flag=0 AND blocked_for=2")["blacklisted"] == 0) {
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
                            //array_push($app_int_ids,$row_emails['id']);
                            foreach ($array_tokens as $token) {
                                if (!empty($token)) {
                                    $acttObj->notify($token, $subject, $sub_title, array("type_key" => $type_key, "job_type" => "Translation"));
                                }
                            }
                        }
                        //Replace date in email bidding 
                        $data   = ["[NAME]", "[ASSIGNDATE]", "[TABLE]", "[EDIT_ID]"];
                        $to_replace  = [$row_emails['name'], "$assignDate_req", "$append_table", "$edit_id"];
                        $message = str_replace($data, $to_replace, $row_format['em_format']);
                        try {
                            $mail->SMTPDebug = 0;
                            $mail->isSMTP();
                            $mail->Host = setupEmail::EMAIL_HOST;
                            $mail->SMTPAuth   = true;
                            $mail->Username   = setupEmail::TRANSLATION_EMAIL;
                            $mail->Password   = setupEmail::TRANSLATION_PASSWORD;
                            $mail->SMTPSecure = setupEmail::SECURE_TYPE;
                            $mail->Port       = setupEmail::SENDING_PORT;
                            $mail->setFrom(setupEmail::TRANSLATION_EMAIL, setupEmail::FROM_NAME);
                            $mail->addAddress($to_address);
                            $mail->addReplyTo(setupEmail::TRANSLATION_EMAIL, setupEmail::FROM_NAME);
                            $mail->isHTML(true);
                            $mail->Subject = $subject;
                            $mail->Body    = $message;
                            $mail->send();
                            $mail->ClearAllRecipients();
                        } catch (Exception $e) { ?>
                            <script>
                                alert("Message could not be sent! Mailer library error.");
                            </script>
                <?php   }
                    }
                }
            }
            $acttObj->insert("daily_logs", array("action_id" => 2, "user_id" => $_SESSION['userId'], "details" => "TR Job ID: " . $edit_id));
        }

        if (isset($_POST['submit'])) {
            $acttObj->editFun($table, $edit_id, 'edited_by', $_SESSION['UserName']);
            $acttObj->editFun($table, $edit_id, 'edited_date', date("Y-m-d H:i:s"));
            //New history function to record history
            if ($duplicate != 'yes') {
                $index_mapping = array(
                    'Company ID' => 'order_company_id', 'Source Language' => 'source', 'Target Language' => 'target', 'Assignment Date' => 'asignDate', 'Job Duration' => 'assignDur',
                    'Incharge Contact' => 'inchContact', 'Incharge Email' => 'inchEmail', 'Incharge No' => 'inchNo', 'Company' => 'orgName', 'Organization Reference' => 'orgRef', 
                    'Organization Contact' => 'orgContact', 'Interpreter Notes' => 'remrks', 'Submitted By' => 'submited', 'Job Status' => 'jobStatus', 
                    'Purchase Order' => 'porder', 'Display Job' => 'jobDisp', 'Client Notes' => 'I_Comments', 'Booked Via' => 'bookedVia', 'Incharge Email 2' => 'inchEmail2', 
                    'Booked Date' => 'bookeddate', 'Booked Time' => 'bookedtime', 'Named Booked' => 'namedbooked', 'Purchase Order Email' => 'porder_email', 'Is Temporary' => 'is_temp', 
                    'Document Type' => 'docType', 'Translation Type' => 'trans_detail', 'Translation Category' => 'transType', 'Delivery Type' => 'deliveryType', 'Delivery Date Client' => 'deliverDate', 
                    'Delivery Date Interpreter' => 'deliverDate2', 'Delivery Type' => 'deliveryType', 'Reference ID' => 'reference_id', 'New Company ID' => 'new_comp_id', 
                    'Company Rate ID' => 'company_rate_id', 'Company Rate Data' => 'company_rate_data', 'Booking Type ID' => 'interpreter_rate_id', 'Booking Type Data' => 'interpreter_rate_data'
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
                $acttObj->log_changes($old_values, $new_values, $edit_id, $table, "update", $_SESSION['userId'], $_SESSION['UserName'], "edit_job_tr");
            }
            //This needs to be removed soon
            // $acttObj->new_old_table('hist_' . $table, $table, $edit_id);
        }
        if (isset($_POST['submit']) && $duplicate != 'yes') { ?>
            <script>
                alert('Job Successfully Submitted!');
                window.close();
                window.onunload = refreshParent;
            </script>
        <?php } ?>
        <script>
            function new_company(elem) {
                $('#order_company_id').val($("#orgName option:selected").attr('data-id'));
                var orgName = $(elem).val();
                if (orgName == "LSUK_Private Client") {
                    $('.new_company').removeClass('hidden');
                } else {
                    $('.new_company').addClass('hidden');
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
                                gender: gender
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

            function find_company_rates() {
                var select = $('select#bookinType');
                var selected_company_id = $("#orgName option:selected").attr('data-id');
                var selected_company_type = $("#orgName option:selected").attr('data-type-id');
                var selected_language_type = $("#source option:selected").attr('data-type');
                var selected_booked_date = $("#bookedDate").val();
                var selected_booked_time = $("#bookedTime").val();
                var selected_assignment_date = $("#assignDate").val();
                var selected_assignment_time = "08:00:00";
                var find_order_type = 3;
                if ($('#docType').val() == 7) {//Transcription:7, selected_language_type:3 means BSL
                    find_order_type = 4;
                }
                if ($('#docType').val() == 7 && selected_language_type == 3) {//Transcription & BSL
                    find_order_type = 5;
                }
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
                        find_order_type: find_order_type,
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
            $("#source, #docType, #orgName, #assignDate, #bookedDate, #bookedTime").on("change", function() {
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
</body>
<?php } ?>

</html>