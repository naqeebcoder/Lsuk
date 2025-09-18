<?php
session_start();
include 'source/db.php';
include 'source/class.php';
// Unset these when page is refershed
if (isset($_SESSION['new_otp']) || isset($_SESSION['verified_otp'])) {
    unset($_SESSION['new_otp']);
    unset($_SESSION['verified_otp']);
}
// Generate authentication token
$_SESSION["signin_token"] = bin2hex(openssl_random_pseudo_bytes(32));
$_SESSION["signin_token_expiry"] = time() + 1800;//Set to 10 min (We will wait for 10 minutes to complete registration form)

?>
<!DOCTYPE HTML>
<html class="no-js">
<head>
    <?php include 'source/header.php'; ?>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="lsuk_system/css/bootstrap.css" />

    <style>
        .can_do {
            margin: 3px !important;
        }

        .ri {
            margin-top: 7px !important;
        }

        .ri .label {
            font-size: 100%;
            padding: .5em 0.6em 0.5em;
        }

        .mt {
            margin-top: 2px;
        }

        select {
            -webkit-appearance: auto !important;
        }

        .multiselect {
            min-width: 218px;
        }

        .multiselect-container {
            max-height: 400px;
            overflow-y: auto;
            max-width: 380px;
        }

        .multiselect-native-select {
            display: block;
        }

        .multiselect-container li.active label.radio,
        .multiselect-container li.active label.checkbox {
            color: white;
        }

        .hidden_online {
            display: none;
        }

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
            min-width: 295px;
        }

        .multiselect-container {
            max-height: 400px;
            overflow-y: auto;
            max-width: 380px;
        }

        .multiselect-native-select {
            display: block;
        }

        .multiselect-container li.active label.checkbox {
            color: white;
        }

        .sky-form select {
            -webkit-appearance: auto !important;
        }

        .stepwizard-step p {
            margin-top: 0px;
            color: #666;
        }

        .stepwizard-row {
            display: table-row;
        }

        .stepwizard {
            display: table;
            width: 100%;
            position: relative;
        }

        .stepwizard-step button[disabled] {
            /*opacity: 1 !important;
            filter: alpha(opacity=100) !important;*/
        }

        .stepwizard .btn.disabled,
        .stepwizard .btn[disabled],
        .stepwizard fieldset[disabled] .btn {
            opacity: 1 !important;
            color: #bbb;
        }

        .stepwizard-row:before {
            top: 14px;
            bottom: 0;
            position: absolute;
            content: " ";
            width: 100%;
            height: 1px;
            background-color: #ccc;
            z-index: 0;
        }

        .stepwizard-step {
            display: table-cell;
            text-align: center;
            position: relative;
        }

        .btn-circle {
            width: 30px;
            height: 30px;
            text-align: center;
            padding: 6px 0;
            font-size: 12px;
            line-height: 1.428571429;
            border-radius: 15px;
        }

        .btn-group.open ul.multiselect-container {
            position: relative;
        }
        .is-invalid {
            border: 1px solid #dc3545 !important;
        }
    </style>
</head>

<body class="boxed">
    <!-- begin container -->
    <div id="wrap">
        <!-- begin header -->
        <?php include 'source/top_nav.php'; ?>
        <section id="page-title">
            <div class="container clearfix">
                <h1>Interpreter Registration Form</h1>
                <nav id="breadcrumbs">
                    <ul>
                        <li><a href="index.php">Home</a> &rsaquo;</li>
                        <li><a href="<?php echo basename($_SERVER['HTTP_REFERER']); ?>"><?php echo ucwords(basename($_SERVER['HTTP_REFERER'], '.php')); ?></a>
                            &rsaquo;</li>
                        <li><?php echo ucwords(basename($_SERVER['REQUEST_URI'], '.php')); ?></li>
                    </ul>
                </nav>
            </div>
        </section>
        <!-- begin content -->
        <section id="content" class="container-fluid clearfix">
            <?php
            if ($_SESSION['returned_message']) {
                echo "<div class='row'>" . $_SESSION['returned_message'] . "</div>";
                unset($_SESSION['returned_message']);
            } ?>
            <div class="stepwizard">
                <div class="stepwizard-row setup-panel">
                    <div class="stepwizard-step col-md-2">
                        <a href="#step-1" type="button" class="btn btn-primary btn-circle">1</a>
                        <p><small>Registration Type</small></p>
                    </div>
                    <div class="stepwizard-step col-md-1 col-xs-1">
                        <a href="#step-2" type="button" class="btn btn-primary btn-circle disabled" disabled>2</a>
                        <p><small>Personal Details</small></p>
                    </div>
                    <div class="stepwizard-step col-md-2">
                        <a href="#step-3" type="button" class="btn btn-default btn-circle disabled" disabled>3</a>
                        <p><small>Address Details</small></p>
                    </div>
                    <div class="stepwizard-step col-md-2">
                        <a href="#step-4" type="button" class="btn btn-default btn-circle disabled" disabled>4</a>
                        <p><small>Languages & Education</small></p>
                    </div>
                    <div class="stepwizard-step col-md-1 col-xs-1">
                        <a href="#step-5" type="button" class="btn btn-default btn-circle disabled" disabled>5</a>
                        <p><small>Experience</small></p>
                    </div>
                    <div class="stepwizard-step col-md-2">
                        <a href="#step-6" type="button" class="btn btn-default btn-circle disabled" disabled>6</a>
                        <p><small>Bank Details</small></p>
                    </div>
                    <div class="stepwizard-step col-md-2">
                        <a href="#step-7" type="button" class="btn btn-default btn-circle disabled" disabled>7</a>
                        <p><small>References & Disclaimer</small></p>
                    </div>
                </div>
            </div>

            <form class="col-md-12" action="process/interp_reg.php" method="post" enctype="multipart/form-data">
                <input type="hidden" name="signin_token" value="<?=$_SESSION['signin_token']?>"/>
                <div class="panel panel-info setup-content" id="step-1">
                    <div class="panel-heading">
                        <h3 class="panel-title">Registration Type</h3>
                    </div>
                    <div class="panel-body">
                        <div class='row'>
                            <div class="form-group col-lg-12">
                                <div class="form-group col-md-6 col-sm-6">
                                    <label class="optional">What Role are you applying for? <span class="label label-danger">Required</span></label><br>
                                    <div class="radio-inline ri">
                                        <label><input type="radio" name="work_type" value="in-house">
                                            <span class="label label-primary">In-House Interpreter</span></label>
                                    </div>
                                    <div class="radio-inline ri">
                                        <label><input type="radio" name="work_type" value="freelance">
                                            <span class="label label-info">Freelance Interpreter</span></label>
                                    </div>
                                </div>
                                <hr>
                                <label class="optional">Which of the following interpreter category do you belong to? <span class="label label-danger">Select atleast one option</span></label><br>
                                <div class="ri">
                                    <label class="btn btn-default"><input type="radio" name="int_type" value="ci" onclick="type_check(this);">
                                        <span>Community Interpreter</span></label>
                                </div>
                                <div class="ri">
                                    <label class="btn btn-default"><input type="radio" name="int_type" value="nrpsi" onclick="type_check(this);">
                                        <span>NRPSI Registered interpreter</span></label>
                                </div>
                                <div class="ri">
                                    <label class="btn btn-default"><input type="radio" name="int_type" value="ciol" onclick="type_check(this);">
                                        <span>CIOL Registered Interpreter</span></label>
                                </div>
                                <div class="ri">
                                    <label class="btn btn-default"><input type="radio" name="int_type" value="iti" onclick="type_check(this);">
                                        <span>ITI Registered Interpreter</span></label>
                                </div>
                                <div class="ri">
                                    <label class="btn btn-default"><input type="radio" name="int_type" value="bsl" onclick="type_check(this);">
                                        <span>BSL Interpreter</span></label>
                                </div>
                            </div>
                            <div class="member_ships_check p-4 nrpsi_mem hidden">
                                <!-- NRPSI Section Starts -->
                                <div class="row">
                                    <div style="padding:3rem;" class="col-md-4 div_nrpsi">
                                        <label>Enter NRPSI MemberShip Number</label>
                                        <input name="nrpsi_number" maxlength="15" type="text" class="form-control nrpsi_fields">
                                    </div>
                                </div>
                                <!-- NRPSI Section Ends -->
                            </div>
                            <div class="member_ships_check p-4 bsl_mem hidden">
                                <!-- ASLI Section Starts -->
                                <div class="row">
                                    <div style="padding:3rem;" class="col-md-4 div_bsl ">
                                        <label>Enter ASLI or NRCPD MemberShip Number</label>
                                        <input name="asli_number" maxlength="15" type="text" class="form-control asli_fields">
                                    </div>
                                </div>
                                <!-- ASLI Section Ends -->
                            </div>
                            <div class="member_ships_check p-4 ciol_mem hidden">
                                <!-- NRPSI Section Starts -->
                                <div class="row">
                                    <div style="padding:3rem;" class="col-md-4 div_ciol ">
                                        <label>Enter CIOL MemberShip Number</label>
                                        <input name="ciol_number" maxlength="15" type="text" class="form-control ciol_fields">
                                    </div>
                                </div>
                                <!-- NRPSI Section Ends -->
                            </div>
                            <div class="member_ships_check p-4 iti_mem hidden">
                                <!-- NRPSI Section Starts -->
                                <div class="row">
                                    <div style="padding:3rem;" class="col-md-4 div_iti ">
                                        <label>Enter ITI MemberShip Number</label>
                                        <input name="iti_number" maxlength="15" type="text" class="form-control iti_fields">
                                    </div>
                                </div>
                                <!-- NRPSI Section Ends -->
                            </div>
                            <div class="form-group col-lg-12 uk_citizen hidden">
                                <label class="optional">Are you a UK Citizen?</label><br>
                                <div class="radio-inline ri">
                                    <label><input type="radio" name="citizen" value="Yes" onclick="changer(this);">
                                        <span class="label label-success">Yes <i class="fa fa-check-circle"></i></span></label>
                                </div>
                                <div class="radio-inline ri">
                                    <label><input type="radio" name="citizen" value="No" onclick="changer(this);">
                                        <span class="label label-danger">No <i class="fa fa-remove"></i></span></label>
                                </div>
                            </div>
                            <div class="form-group col-md-3 col-sm-6 div_passport_file hidden">
                                <label>Upload File (<small>British Passport</small>)</label>
                                <input name="passport_file" type="file" class="form-control uk_citizen_fields" onchange="max_upload(this);">
                            </div>
                            <div class="form-group col-md-3 col-sm-6 div_passport_file hidden">
                                <label>Enter Passport Number</label>
                                <input placeholder="Enter Passport Number" type="text" name="passport_number" class="form-control uk_citizen_fields mt">
                            </div>
                            <div class="form-group col-md-3 col-sm-6 div_passport_file hidden">
                                <label>Select Issue Date</label>
                                <input placeholder="Enter Issue Date" type="date"    name="passport_issue_date" class="form-control uk_citizen_fields" />
                            </div>
                            <div class="form-group col-md-3 col-sm-6 div_passport_file hidden">
                                <label>Select Expiry Date</label>
                                <input placeholder="Enter Expiry Date" type="date"   name="passport_expiry_date" class="form-control uk_citizen_fields mt" />
                            </div>
                        </div>
                        <div class="row permit-section hidden">
                            <div class="form-group col-lg-12">
                                <label class="optional">Has got Right to Work in the UK?</label><br>
                                <div class="radio-inline ri">
                                    <label><input type="radio" name="work_evid" value="Yes" onclick="work_evid_change(this);">
                                        <span class="label label-success">Yes <i class="fa fa-check-circle"></i></span></label>
                                </div>
                                <div class="radio-inline ri">
                                    <label><input type="radio" name="work_evid" value="No" onclick="work_evid_change(this);">
                                        <span class="label label-danger">No <i class="fa fa-remove"></i></span></label>
                                </div>
                            </div>
                            <div class="form-group col-lg-3 col-md-6 col-sm-6 div_work_evid_file hidden">
                                <label>Upload (<small>UK Right to work evidence</small>)</label>
                                <input name="work_evid_file" type="file" class="form-control work_evid_fields" onchange="max_upload(this);">
                            </div>
                            <div class="form-group col-lg-3 col-md-6 col-sm-6 div_work_evid_file hidden">
                                <label>Select Issue Dates</label>
                                <input placeholder="Select Issue Date" type="date"   name="work_evid_issue_date" class="form-control work_evid_fields" />
                            </div>
                            <div class="form-group col-lg-3 col-md-6 col-sm-6 div_work_evid_file hidden">
                                <label>Select Expiry Dates</label>
                                <input placeholder="Select Expiry Date" type="date"      name="work_evid_expiry_date" class="form-control work_evid_fields mt" />
                            </div>
                        </div>
                        <div class='col-md-6 col-md-offset-3 text-center alert alert-danger no-registration hidden'><b>Unfortunately, you cannot proceed from this stage onwards!</b></div>
                        <div class="form-group col-md-12">
                            <button class="btn btn-primary nextBtn pull-right" type="button">Next <i class="fa fa-angle-right"></i><i class="fa fa-angle-right"></i></button>
                        </div>
                    </div>
                </div>
                <div class="panel panel-info setup-content" id="step-2">
                    <div class="panel-heading">
                        <h3 class="panel-title">Personal Details</h3>
                    </div>
                    <div class="panel-body">
                        <div class="row col-md-12">
                            <div class="form-group col-lg-3 col-md-4 col-sm-6">
                                <img id="output" src="lsuk_system/file_folder/interp_photo/profile.png" title="Interpreter picture" name="output" class="img-thumbnail" style="max-width: 140px;max-height: 140px;min-width: 140px;min-height: 140px;" />
                                <br><label style="margin-top:8px;" for="profile_photo">Upload Profile Photo <i title="Select a clear square photo of yourself facing towards camera" class="fa fa-question-circle"></i></label>
                                <input name="profile_photo" id="profile_photo" type="file" class="form-control" accept="image/*" required>
                            </div>
                            <div class="form-group col-lg-3 col-md-4 col-sm-6">
                                <label>First Name <span class="label label-danger">Required</span></label>
                                <input name="first_name" id="first_name" type="text" class="form-control" required />
                            </div>
                            <div class="form-group col-lg-3 col-md-4 col-sm-6">
                                <label>Last Name <span class="label label-danger">Required</span></label>
                                <input name="last_name" id="last_name" type="text" class="form-control" required />
                            </div>
                            <div class="form-group col-lg-3 col-md-4 col-sm-6">
                                <label>Date of Birth <span class="label label-danger">Required</span></label>
                                <input name="dob" id="dob" type="date" class="form-control" required />
                            </div>
                            <div class="form-group col-lg-3 col-md-4 col-sm-6">
                                <label class="optional">Gender <span class="label label-danger">Required</span></label>
                                <select name="gender" id="gender" class="form-control">
                                    <option>Male</option>
                                    <option>Female</option>
                                    <option>No Preference</option>
                                </select>
                            </div>
                            <div class="form-group col-lg-3 col-md-4 col-sm-6">
                                <label>Contact Number <i title="Enter contact number starting from 0 e.g:07923456789" class="fa fa-question-circle"></i> <span class="label label-danger">Required</span></label>
                                <input name="contact_no" type="text" class="form-control validate_phone" maxlength="11" pattern="^0[0-9]{10}$" onpaste="return false" required placeholder="Enter Number e.g: 07923456789"/>
                            </div>
                            <div class="form-group col-lg-3 col-md-4 col-sm-6">
                                <label>Mobile Number <i title="Enter contact number starting from 0 e.g:07923456789" class="fa fa-question-circle"></i> <span class="label label-danger">Required</span></label>
                                <input name="mobile_no" type="text" class="form-control validate_phone" maxlength="11" pattern="^0[0-9]{10}$" onpaste="return false" required placeholder="Enter Number e.g: 07923456789"/>
                            </div>
                            <div class="form-group col-lg-3 col-md-4 col-sm-6">
                                <label>Email Address <span class="label label-danger">Required</span></label>
                                <input onfocus="check_fields()" name="email" type="email" id="email" class="form-control" required onblur="check_existing(this)" />
                            </div>
                            <div class="form-group ni_utr_sec col-lg-3 col-md-4 col-sm-6">
                                <label>NI / UTR Number</label>
                                <input name="utr" type="text" class="form-control" />
                            </div>
                            <div class="form-group ni_utr_sec col-lg-3 col-md-4 col-sm-6">
                                <label>Upload NI/UTR File</label>
                                <input name="nin" id="nin" type="file" class="form-control" onchange="max_upload(this);">
                            </div>
                            <div class="form-group col-sm-12 div_interpreting_types" style="padding:8px">
                                <label class="optional">Choose types of interpreting you can do <i title="Select atleast one option from the checkboxes!" class="fa fa-question-circle"></i></label><br>
                                <div class="radio-inline ri">
                                    <label><input style="margin: 4px;" onchange="show_dbs(this)" type="checkbox" name="interp" value="Interpreter">
                                        <span class="label label-primary">Face To Face Interpreting <i class="fa fa-user"></i></span></label>
                                </div>
                                <div class="radio-inline ri">
                                    <label><input style="margin: 4px;" type="checkbox" name="telep" value="Telephone Interpreter">
                                        <span class="label label-info">Telephone Interpreting <i class="fa fa-phone"></i></span></label>
                                </div>
                                <div class="radio-inline ri">
                                    <label><input style="margin: 4px;" type="checkbox" name="trans" value="Translator">
                                        <span class="label label-success">Translator <i class="fa fa-language"></i></span></label>
                                </div>
                                <div class="row">
                                    <br>
                                    <div class="form-group col-md-3 col-sm-6 div_dbs_file hidden">
                                        <label>Upload File (<small>DBS Document</small>)</label>
                                        <input name="dbs_file" type="file" class="form-control" onchange="max_upload(this);">
                                    </div>
                                    <div class="form-group col-md-3 col-sm-6 div_dbs_file hidden">
                                        <label>Enter DBS Number</label>
                                        <input placeholder="Enter DBS Number" type="text" name="dbs_no" class="form-control dbs_fields mt">
                                    </div>
                                    <div class="form-group col-md-3 col-sm-6 div_dbs_file hidden">
                                        <label>Select Issue Date</label>
                                        <input placeholder="Enter Issue Date" type="date"    name="dbs_issue_date" class="form-control dbs_fields" />
                                    </div>
                                    <div class="form-group col-md-3 col-sm-6 div_dbs_file hidden">
                                        <label>Select Expiry Date</label>
                                        <input placeholder="Enter Expiry Date" type="date"   name="dbs_expiry_date" class="form-control dbs_fields mt" />
                                    </div>
                                    <div class="form-group col-md-3 col-sm-6 div_dbs_auto_number hidden">
                                        <label>Enter DBS Number</label>
                                        <input placeholder="Enter DBS Number" type="text" name="dbs_auto_number" class="form-control dbs_auto_number mt" <?= ($interp == 'Yes' || $dbs_checked == 0) && $row['is_dbs_auto'] == 1 ? 'required' : '' ?> />
                                    </div>
                                    <div class="row"></div>
                                    <div class="col-md-3 div_auto_dbs hidden">
                                        <label class="btn btn-warning"><input style="margin: 4px;" onchange="toggle_auto_dbs()" type="checkbox" name="is_dbs_auto" id="is_dbs_auto" value="1"> I am on Update Service?</label>
                                    </div>
                                    <div title="If you don't have DBS, then check this box to ask LSUK to apply for you!" class="col-md-3 div_lsuk_dbs hidden">
                                        <label class="btn btn-default"><input style="margin: 4px;" onchange="toggle_lsuk_dbs()" type="checkbox" name="request_lsuk_dbs" id="request_lsuk_dbs" value="1"> Request LSUK to apply for DBS</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div id="response_message" class="col-md-12 hidden"></div>
                        <div style="margin-top:10px" class="col-md-3 div_otp hidden">
                            <h3 for="otp"><b>Enter Your OTP CODE</b></h3>
                            <input autocomplete="off" minlength="4" maxlength="4" type="text" pattern="\d*" inputmode="numeric" class="form-control p-1 hidden" id="otp" name="otp" placeholder="CODE">
                            <button onclick="verify_otp(this)" class="btn btn-success btn_verify_otp hidden" type="button" style="margin-top: 5px;">Verify OTP</button>
                        </div>
                        <div class="col-md-12 div_resend_timer" style="margin-top:10px">
                            <p class="text-primary" id="resend_timer"></p>
                        </div>
                        <div class="form-group col-md-12 div_otp_actions">
                            <button data-sent="0" onclick="send_otp(this)" class="btn btn-primary pull-right" type="button">Next <i class="fa fa-angle-right"></i><i class="fa fa-angle-right"></i></button>
                            <button class="btn btn-primary nextBtn pull-right hidden" type="button">Next <i class="fa fa-angle-right"></i><i class="fa fa-angle-right"></i></button>
                        </div>
                    </div>
                </div>
                <div class="panel panel-info setup-content" id="step-3">
                    <div class="panel-heading">
                        <h3 class="panel-title">Address Details</h3>
                    </div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="form-group col-lg-3 col-md-4 col-sm-6">
                                <?php
                                $country_array = array(
                                    "Afghanistan" => "Afghanistan (افغانستان)", "Aland Islands" => "Aland Islands (Åland)", "Albania" => "Albania (Shqipëria)", "Algeria" => "Algeria (الجزائر)", "American Samoa" => "American Samoa (American Samoa)", "Andorra" => "Andorra (Andorra)", "Angola" => "Angola (Angola)", "Anguilla" => "Anguilla (Anguilla)", "Antarctica" => "Antarctica (Antarctica)", "Antigua And Barbuda" => "Antigua And Barbuda (Antigua and Barbuda)", "Argentina" => "Argentina (Argentina)", "Armenia" => "Armenia (Հայաստան)", "Aruba" => "Aruba (Aruba)", "Australia" => "Australia (Australia)", "Austria" => "Austria (Österreich)", "Azerbaijan" => "Azerbaijan (Azərbaycan)", "Bahamas The" => "Bahamas The (Bahamas)", "Bahrain" => "Bahrain (‏البحرين)", "Bangladesh" => "Bangladesh (Bangladesh)", "Barbados" => "Barbados (Barbados)", "Belarus" => "Belarus (Белару́сь)", "Belgium" => "Belgium (België)", "Belize" => "Belize (Belize)", "Benin" => "Benin (Bénin)", "Bermuda" => "Bermuda (Bermuda)", "Bhutan" => "Bhutan (ʼbrug-yul)", "Bolivia" => "Bolivia (Bolivia)", "Bonaire, Sint Eustatius and Saba" => "Bonaire, Sint Eustatius and Saba (Caribisch Nederland)", "Bosnia and Herzegovina" => "Bosnia and Herzegovina (Bosna i Hercegovina)", "Botswana" => "Botswana (Botswana)", "Bouvet Island" => "Bouvet Island (Bouvetøya)", "Brazil" => "Brazil (Brasil)", "British Indian Ocean Territory" => "British Indian Ocean Territory (British Indian Ocean Territory)", "Brunei" => "Brunei (Negara Brunei Darussalam)", "Bulgaria" => "Bulgaria (България)", "Burkina Faso" => "Burkina Faso (Burkina Faso)", "Burundi" => "Burundi (Burundi)", "Cambodia" => "Cambodia (Kâmpŭchéa)", "Cameroon" => "Cameroon (Cameroon)", "Canada" => "Canada (Canada)", "Cape Verde" => "Cape Verde (Cabo Verde)", "Cayman Islands" => "Cayman Islands (Cayman Islands)", "Central African Republic" => "Central African Republic (Ködörösêse tî Bêafrîka)", "Chad" => "Chad (Tchad)", "Chile" => "Chile (Chile)", "China" => "China (中国)", "Christmas Island" => "Christmas Island (Christmas Island)", "Cocos (Keeling) Islands" => "Cocos (Keeling) Islands (Cocos (Keeling) Islands)", "Colombia" => "Colombia (Colombia)", "Comoros" => "Comoros (Komori)", "Congo" => "Congo (République du Congo)", "Congo The Democratic Republic Of The" => "Congo The Democratic Republic Of The (République démocratique du Congo)", "Cook Islands" => "Cook Islands (Cook Islands)", "Costa Rica" => "Costa Rica (Costa Rica)", "Cote D'Ivoire (Ivory Coast)" => "Cote D'Ivoire (Ivory Coast) ()", "Croatia (Hrvatska)" => "Croatia (Hrvatska) (Hrvatska)", "Cuba" => "Cuba (Cuba)", "Curaçao" => "Curaçao (Curaçao)", "Cyprus" => "Cyprus (Κύπρος)", "Czech Republic" => "Czech Republic (Česká republika)", "Denmark" => "Denmark (Danmark)", "Djibouti" => "Djibouti (Djibouti)", "Dominica" => "Dominica (Dominica)", "Dominican Republic" => "Dominican Republic (República Dominicana)", "East Timor" => "East Timor (Timor-Leste)", "Ecuador" => "Ecuador (Ecuador)", "Egypt" => "Egypt (مصر‎)", "El Salvador" => "El Salvador (El Salvador)", "Equatorial Guinea" => "Equatorial Guinea (Guinea Ecuatorial)", "Eritrea" => "Eritrea (ኤርትራ)", "Estonia" => "Estonia (Eesti)", "Ethiopia" => "Ethiopia (ኢትዮጵያ)", "Falkland Islands" => "Falkland Islands (Falkland Islands)", "Faroe Islands" => "Faroe Islands (Føroyar)", "Fiji Islands" => "Fiji Islands (Fiji)", "Finland" => "Finland (Suomi)", "France" => "France (France)", "French Guiana" => "French Guiana (Guyane française)", "French Polynesia" => "French Polynesia (Polynésie française)", "French Southern Territories" => "French Southern Territories (Territoire des Terres australes et antarctiques fr)", "Gabon" => "Gabon (Gabon)", "Gambia The" => "Gambia The (Gambia)", "Georgia" => "Georgia (საქართველო)", "Germany" => "Germany (Deutschland)", "Ghana" => "Ghana (Ghana)", "Gibraltar" => "Gibraltar (Gibraltar)", "Greece" => "Greece (Ελλάδα)", "Greenland" => "Greenland (Kalaallit Nunaat)", "Grenada" => "Grenada (Grenada)", "Guadeloupe" => "Guadeloupe (Guadeloupe)", "Guam" => "Guam (Guam)", "Guatemala" => "Guatemala (Guatemala)", "Guernsey and Alderney" => "Guernsey and Alderney (Guernsey)", "Guinea" => "Guinea (Guinée)", "Guinea-Bissau" => "Guinea-Bissau (Guiné-Bissau)", "Guyana" => "Guyana (Guyana)", "Haiti" => "Haiti (Haïti)", "Heard Island and McDonald Islands" => "Heard Island and McDonald Islands (Heard Island and McDonald Islands)", "Honduras" => "Honduras (Honduras)", "Hong Kong S.A.R." => "Hong Kong S.A.R. (香港)", "Hungary" => "Hungary (Magyarország)", "Iceland" => "Iceland (Ísland)", "India" => "India (भारत)", "Indonesia" => "Indonesia (Indonesia)", "Iran" => "Iran (ایران)", "Iraq" => "Iraq (العراق)", "Ireland" => "Ireland (Éire)", "Israel" => "Israel (יִשְׂרָאֵל)", "Italy" => "Italy (Italia)", "Jamaica" => "Jamaica (Jamaica)", "Japan" => "Japan (日本)", "Jersey" => "Jersey (Jersey)", "Jordan" => "Jordan (الأردن)", "Kazakhstan" => "Kazakhstan (Қазақстан)", "Kenya" => "Kenya (Kenya)", "Kiribati" => "Kiribati (Kiribati)", "Korea North" => "Korea North (북한)", "Korea South" => "Korea South (대한민국)", "Kosovo" => "Kosovo (Republika e Kosovës)", "Kuwait" => "Kuwait (الكويت)", "Kyrgyzstan" => "Kyrgyzstan (Кыргызстан)", "Laos" => "Laos (ສປປລາວ)", "Latvia" => "Latvia (Latvija)", "Lebanon" => "Lebanon (لبنان)", "Lesotho" => "Lesotho (Lesotho)", "Liberia" => "Liberia (Liberia)", "Libya" => "Libya (‏ليبيا)", "Liechtenstein" => "Liechtenstein (Liechtenstein)", "Lithuania" => "Lithuania (Lietuva)", "Luxembourg" => "Luxembourg (Luxembourg)", "Macau S.A.R." => "Macau S.A.R. (澳門)", "Macedonia" => "Macedonia (Северна Македонија)", "Madagascar" => "Madagascar (Madagasikara)", "Malawi" => "Malawi (Malawi)", "Malaysia" => "Malaysia (Malaysia)", "Maldives" => "Maldives (Maldives)", "Mali" => "Mali (Mali)", "Malta" => "Malta (Malta)", "Man (Isle of)" => "Man (Isle of) (Isle of Man)", "Marshall Islands" => "Marshall Islands (M̧ajeļ)", "Martinique" => "Martinique (Martinique)", "Mauritania" => "Mauritania (موريتانيا)", "Mauritius" => "Mauritius (Maurice)", "Mayotte" => "Mayotte (Mayotte)", "Mexico" => "Mexico (México)", "Micronesia" => "Micronesia (Micronesia)", "Moldova" => "Moldova (Moldova)", "Monaco" => "Monaco (Monaco)", "Mongolia" => "Mongolia (Монгол улс)", "Montenegro" => "Montenegro (Црна Гора)", "Montserrat" => "Montserrat (Montserrat)", "Morocco" => "Morocco (المغرب)", "Mozambique" => "Mozambique (Moçambique)", "Myanmar" => "Myanmar (မြန်မာ)", "Namibia" => "Namibia (Namibia)", "Nauru" => "Nauru (Nauru)", "Nepal" => "Nepal (नपल)", "Netherlands The" => "Netherlands The (Nederland)", "New Caledonia" => "New Caledonia (Nouvelle-Calédonie)", "New Zealand" => "New Zealand (New Zealand)", "Nicaragua" => "Nicaragua (Nicaragua)", "Niger" => "Niger (Niger)", "Nigeria" => "Nigeria (Nigeria)", "Niue" => "Niue (Niuē)", "Norfolk Island" => "Norfolk Island (Norfolk Island)", "Northern Mariana Islands" => "Northern Mariana Islands (Northern Mariana Islands)", "Norway" => "Norway (Norge)", "Oman" => "Oman (عمان)", "Pakistan" => "Pakistan (پاکستان)", "Palau" => "Palau (Palau)", "Palestinian Territory Occupied" => "Palestinian Territory Occupied (فلسطين)", "Panama" => "Panama (Panamá)", "Papua new Guinea" => "Papua new Guinea (Papua Niugini)", "Paraguay" => "Paraguay (Paraguay)", "Peru" => "Peru (Perú)", "Philippines" => "Philippines (Pilipinas)", "Pitcairn Island" => "Pitcairn Island (Pitcairn Islands)", "Poland" => "Poland (Polska)", "Portugal" => "Portugal (Portugal)", "Puerto Rico" => "Puerto Rico (Puerto Rico)", "Qatar" => "Qatar (قطر)", "Reunion" => "Reunion (La Réunion)", "Romania" => "Romania (România)", "Russia" => "Russia (Россия)", "Rwanda" => "Rwanda (Rwanda)", "Saint Helena" => "Saint Helena (Saint Helena)", "Saint Kitts And Nevis" => "Saint Kitts And Nevis (Saint Kitts and Nevis)", "Saint Lucia" => "Saint Lucia (Saint Lucia)", "Saint Pierre and Miquelon" => "Saint Pierre and Miquelon (Saint-Pierre-et-Miquelon)", "Saint Vincent And The Grenadines" => "Saint Vincent And The Grenadines (Saint Vincent and the Grenadines)", "Saint-Barthelemy" => "Saint-Barthelemy (Saint-Barthélemy)", "Saint-Martin (French part)" => "Saint-Martin (French part) (Saint-Martin)", "Samoa" => "Samoa (Samoa)", "San Marino" => "San Marino (San Marino)", "Sao Tome and Principe" => "Sao Tome and Principe (São Tomé e Príncipe)", "Saudi Arabia" => "Saudi Arabia (العربية السعودية)", "Senegal" => "Senegal (Sénégal)", "Serbia" => "Serbia (Србија)", "Seychelles" => "Seychelles (Seychelles)", "Sierra Leone" => "Sierra Leone (Sierra Leone)", "Singapore" => "Singapore (Singapore)", "Sint Maarten (Dutch part)" => "Sint Maarten (Dutch part) (Sint Maarten)", "Slovakia" => "Slovakia (Slovensko)", "Slovenia" => "Slovenia (Slovenija)", "Solomon Islands" => "Solomon Islands (Solomon Islands)", "Somalia" => "Somalia (Soomaaliya)", "South Africa" => "South Africa (South Africa)", "South Georgia" => "South Georgia (South Georgia)", "South Sudan" => "South Sudan (South Sudan)", "Spain" => "Spain (España)", "Sri Lanka" => "Sri Lanka (śrī laṃkāva)", "Sudan" => "Sudan (السودان)", "Suriname" => "Suriname (Suriname)", "Svalbard And Jan Mayen Islands" => "Svalbard And Jan Mayen Islands (Svalbard og Jan Mayen)", "Swaziland" => "Swaziland (Swaziland)", "Sweden" => "Sweden (Sverige)", "Switzerland" => "Switzerland (Schweiz)", "Syria" => "Syria (سوريا)", "Taiwan" => "Taiwan (臺灣)", "Tajikistan" => "Tajikistan (Тоҷикистон)", "Tanzania" => "Tanzania (Tanzania)", "Thailand" => "Thailand (ประเทศไทย)", "Togo" => "Togo (Togo)", "Tokelau" => "Tokelau (Tokelau)", "Tonga" => "Tonga (Tonga)", "Trinidad And Tobago" => "Trinidad And Tobago (Trinidad and Tobago)", "Tunisia" => "Tunisia (تونس)", "Turkey" => "Turkey (Türkiye)", "Turkmenistan" => "Turkmenistan (Türkmenistan)", "Turks And Caicos Islands" => "Turks And Caicos Islands (Turks and Caicos Islands)", "Tuvalu" => "Tuvalu (Tuvalu)", "Uganda" => "Uganda (Uganda)", "Ukraine" => "Ukraine (Україна)", "United Arab Emirates" => "United Arab Emirates (دولة الإمارات العربية المتحدة)", "United Kingdom" => "United Kingdom (United Kingdom)", "United States" => "United States (United States)", "United States Minor Outlying Islands" => "United States Minor Outlying Islands (United States Minor Outlying Islands)", "Uruguay" => "Uruguay (Uruguay)", "Uzbekistan" => "Uzbekistan (O‘zbekiston)", "Vanuatu" => "Vanuatu (Vanuatu)", "Vatican City State (Holy See)" => "Vatican City State (Holy See) (Vaticano)", "Venezuela" => "Venezuela (Venezuela)",
                                    "Vietnam" => "Vietnam (Việt Nam)", "Virgin Islands (British)" => "Virgin Islands (British) (British Virgin Islands)", "Virgin Islands (US)" => "Virgin Islands (US) (United States Virgin Islands)", "Wallis And Futuna Islands" => "Wallis And Futuna Islands (Wallis et Futuna)", "Western Sahara" => "Western Sahara (الصحراء الغربية)", "Yemen" => "Yemen (اليَمَن)", "Zambia" => "Zambia (Zambia)", "Zimbabwe" => "Zimbabwe (Zimbabwe)"
                                );
                                $select_countries = "<select onchange='get_cities(this)' name='selected_country' id='selected_country' class='form-control multi_class mt'>
                                    <option value='' disabled selected>Select a country</option>";
                                foreach ($country_array as $key => $val) {
                                    $select_countries .= "<option value='" . $key . "'>" . $val . "</option>";
                                }
                                $select_countries .= "<select>";
                                echo $select_countries; ?>
                            </div>
                            <div class="form-group col-lg-3 col-md-4 col-sm-6 append_cities hidden"></div>
                            <div class="form-group col-lg-3 col-md-4 col-sm-6 div_other_city_field hidden">
                                <input name="city" type="text" class="form-control mt other_city_field hidden" placeholder="Enter your City Name" />
                            </div>
                            <div class="form-group col-lg-3 col-md-4 col-sm-6">
                                <input name="post_code" type="text" class="form-control mt" placeholder="Enter your Post Code" required />
                            </div>
                            <div class="form-group col-lg-3 col-md-4 col-sm-6">
                                <input name="building_name" type="text" class="form-control mt" placeholder="Enter Building Name" required />
                            </div>
                            <div class="form-group col-lg-3 col-md-4 col-sm-6">
                                <input type="text" name="line1" class="form-control mt" placeholder="Line 1" />
                            </div>
                            <div class="form-group col-lg-3 col-md-4 col-sm-6">
                                <input type="text" name="line2" class="form-control mt" placeholder="Line 2" />
                            </div>
                            <div class="form-group col-lg-3 col-md-4 col-sm-6">
                                <input type="text" name="line3" class="form-control mt" placeholder="Line 3" />
                            </div>
                        </div>
                        <div class="form-group col-md-12">
                            <button class="btn btn-primary nextBtn pull-right" type="button">Next <i class="fa fa-angle-right"></i><i class="fa fa-angle-right"></i></button>
                        </div>
                    </div>
                </div>
                <div class="panel panel-info setup-content" id="step-4">
                    <div class="panel-heading">
                        <h3 class="panel-title">Languages & Education</h3>
                    </div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="form-group col-md-4 col-sm-6">
                                <select onchange="set_language()" class="form-control" name="language" id="language" required>
                                    <option selected>Select a Language</option>
                                    <?php $lang_query = $acttObj->read_all("lang", "lang", "1 ORDER BY lang");
                                    $lang_counter = 1;
                                    while ($row_lang = $lang_query->fetch_assoc()) { ?>
                                        <option value="<?php echo $lang_counter; ?>"><?php echo $row_lang['lang']; ?>
                                        </option>
                                    <?php $lang_counter++;
                                    } ?>
                                </select>
                            </div>
                            <div class="form-group col-md-12 col-sm-6" id="append_language">
                                <table align="center" class="table table-bordered hidden">
                                    <tr class="bg-primary add_tr">
                                        <th>Language</th>
                                        <th>Speaking Level</th>
                                        <th>Choose Interpreting options you can do</th>
                                        <th>Action</th>
                                    </tr>
                                </table>
                            </div>
                            <hr>
                            <div class="bg-info col-xs-12 form-group">
                                <h4>EDUCATION DETAILS</h4>
                            </div>
                            <div class="">
                                <div class="form-group col-md-4 col-sm-6">
                                    <label>Higher Level of Education <span class="label label-danger">Required</span></label>
                                    <input type="text" class="form-control" name="institute" placeholder="Enter Institute Details" required>
                                </div>
                                <div class="form-group col-md-4 col-sm-6">
                                    <label>Qualification <span class="label label-danger">Required</span></label>
                                    <input type="text" class="form-control" name="qualification" placeholder="Bachelors in CS, ACCA, MBA etc" required>
                                </div>
                                <div class="form-group col-md-2 col-sm-6">
                                    <label>From Date <span class="label label-danger">Required</span></label>
                                    <input type="date" class="form-control" name="from_date" required>
                                </div>
                                <div class="form-group col-md-2 col-sm-6">
                                    <label>To Date <span class="label label-danger">Required</span></label>
                                    <input type="date" class="form-control" name="to_date" required>
                                </div>
                            </div>
                        </div>
                        <div class="form-group col-md-12">
                            <button class="btn btn-primary nextBtn pull-right" type="button">Next <i class="fa fa-angle-right"></i><i class="fa fa-angle-right"></i></button>
                        </div>
                    </div>
                </div>

                <div class="panel panel-info setup-content" id="step-5">
                    <div class="panel-heading">
                        <h3 class="panel-title">Experience</h3>
                    </div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="form-group col-md-6 col-sm-6">
                                <label class="optional">Do You Drive?</label><br>
                                <div class="radio-inline ri">
                                    <label><input type="radio" name="is_drive" value="Yes" onclick="drive(this);">
                                        <span class="label label-success">Yes <i class="fa fa-check-circle"></i></span></label>
                                </div>
                                <div class="radio-inline ri">
                                    <label><input type="radio" name="is_drive" value="No" onclick="drive(this);" checked>
                                        <span class="label label-danger">No <i class="fa fa-remove"></i></span></label>
                                </div>
                                <div class="row">
                                    <div class="col-md-8 div_driving_license hidden"><br>
                                        <label>Upload File (<small>Driving License</small>)</label>
                                        <input name="driving_license_file" type="file" class="form-control" onchange="max_upload(this);">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-6 col-sm-6">
                                <label class="optional">Any recognized Translation Degree/Qualification?</label><br>
                                <div class="radio-inline ri">
                                    <label><input type="radio" name="is_master" value="Yes" onclick="master(this);">
                                        <span class="label label-success">Yes <i class="fa fa-check-circle"></i></span></label>
                                </div>
                                <div class="radio-inline ri">
                                    <label><input type="radio" name="is_master" value="No" onclick="master(this);">
                                        <span class="label label-danger">No <i class="fa fa-remove"></i></span></label>
                                </div>
                                <div class="row">
                                    <div class="col-lg-8 col-md-12 div_master hidden"><br>
                                        <label>Upload File (<small>Translation Certificate</small>)</label>
                                        <input name="master_file" type="file" class="form-control" onchange="max_upload(this);">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="form-group col-md-6 col-sm-6">
                                <label class="optional">Do you hold a recognised Interpreting / Translation
                                    Qualification?</label><br>
                                <div class="radio-inline ri">
                                    <label><input type="radio" name="translation_qualifications" value="Yes" onclick="translation_qualification(this);">
                                        <span class="label label-success">Yes <i class="fa fa-check-circle"></i></span></label>
                                </div>
                                <div class="radio-inline ri">
                                    <label><input type="radio" name="translation_qualifications" value="No" onclick="translation_qualification(this);">
                                        <span class="label label-danger">No <i class="fa fa-remove"></i></span></label>
                                </div>
                                <div class="row">
                                    <div class="col-lg-8 col-md-12 div_translation_qualification hidden"><br>
                                        <label>Upload File (<small>Qualification Document</small>)</label>
                                        <input name="int_qualification_file" type="file" class="form-control" onchange="max_upload(this);">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group col-md-6 col-sm-6">
                                <label class="optional">Do you have professional experience in Interpreting /
                                    Translation?</label><br>
                                <div class="radio-inline ri">
                                    <label><input type="radio" name="is_experience" value="Yes" onclick="experience(this);">
                                        <span class="label label-success">Yes <i class="fa fa-check-circle"></i></span></label>
                                </div>
                                <div class="radio-inline ri">
                                    <label><input type="radio" name="is_experience" value="No" onclick="experience(this);">
                                        <span class="label label-danger">No <i class="fa fa-remove"></i></span></label>
                                </div>
                                <div class="row">
                                    <div class="col-lg-8 col-md-12 div_experience hidden"><br>
                                        <label>How many years?</label>
                                        <input name="experience_years" type="number" class="form-control" value="1" min="1">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="">
                            <div class="form-group col-md-6 col-sm-6">
                                <label>Choose your main areas of specialization from the list</label><br>
                                <select class="multi_class form-control" id="skills" name="skills[]" multiple="multiple">
                                    <?php $skills_q = $acttObj->read_all('DISTINCT skill', 'skill', "id IN (1,2,3,4,5,6,7) ORDER BY skill ASC");
                                    while ($row_skills = mysqli_fetch_assoc($skills_q)) { ?>
                                        <option value="<?php echo $row_skills['skill']; ?>">
                                            <?php echo $row_skills['skill']; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <hr>
                            <div class=" hidden">
                                <div class="form-group col-md-3 col-sm-4">
                                    <label>Any other qualifications?</label>
                                    <select class="form-control" id="other_qualifications" name="other_qualifications">
                                        <option disabled selected>--- Select from options ---</option>
                                        <option value="c2">Community Interpreting Level 2</option>
                                        <option value="c3">Community Interpreting Level 3</option>
                                        <option value="c4">Community Interpreting Level 4</option>
                                        <option value="c5">Community Interpreting Level 5</option>
                                        <option value="c6">Community Interpreting Level 6</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form-group col-md-12">
                            <button class="btn btn-primary nextBtn pull-right" type="button">Next <i class="fa fa-angle-right"></i><i class="fa fa-angle-right"></i></button>
                        </div>
                    </div>
                </div>
                <div class="panel panel-info setup-content" id="step-6">
                    <div class="panel-heading">
                        <h3 class="panel-title">Bank Details</h3>
                    </div>
                    <div class="panel-body">
                        <div class="bg-info col-xs-12 form-group">
                            <h4>Bank Details for BACS payments</h4>
                        </div>
                        <div class="">
                            <div class="form-group col-md-4 col-sm-6">
                                <label>Full Name <small>(As it appears on your Bank Account)</small> <span class="label label-danger">Required</span></label>
                                <input type="text" class="form-control" name="account_name" placeholder="Enter Account Full Name" required>
                            </div>
                            <div class="form-group col-md-4 col-sm-6">
                                <label>Bank Name <span class="label label-danger">Required</span></label>
                                <input type="text" class="form-control" name="bank_name" placeholder="Enter Bank Name" required>
                            </div>
                            <div class="form-group col-md-4 col-sm-6">
                                <label>Branch <span class="label label-danger">Required</span></label>
                                <input type="text" class="form-control" name="branch" placeholder="Enter Branch Name" required>
                            </div>
                            <div class="form-group col-md-4 col-sm-6">
                                <label>Account Number <span class="label label-danger">Required</span></label>
                                <input type="text" class="form-control" name="account_number" id="acNo" onchange="checkAccountNumber(this)" oninput="this.value = this.value.replace(/[^\d]/g, '').replace(/(\..*)\./g, '$1');" maxlength="8" minlength="8" placeholder="Enter Account Number (8 digits)" required>
                            </div>
                            <div class="form-group col-md-4 col-sm-6">
                                <label>Sort Code <span class="label label-danger">Required</span></label>
                                <input type="text" class="form-control" name="sort_code" id="acntCode" onchange="checkAccountSortCode(this)" oninput="this.value = this.value.replace(/[^0-9-.]/g, '').replace(/(\..*)\./g, '$1');" maxlength="8" minlength="8" placeholder="Enter Sort Code (6 digits)" required>
                            </div>
                        </div>
                        <div class="form-group col-md-12">
                            <button class="btn btn-primary nextBtn pull-right" type="button">Next <i class="fa fa-angle-right"></i><i class="fa fa-angle-right"></i></button>
                        </div>
                    </div>
                </div>
                <div class="panel panel-info setup-content" id="step-7">
                    <div class="panel-heading">
                        <h3 class="panel-title">Speaking Languages</h3>
                    </div>
                    <div class="panel-body">
                        <div class="bg-info col-xs-12 form-group">
                            <h4>REFERENCES</h4> (<i>Please list professional references:</i>)
                        </div>
                        <div class="">
                            <div class="form-group col-md-12 col-sm-12">
                                <u><b>Reference 1 :</b></u>
                            </div>
                            <div class="form-group col-md-4 col-sm-6">
                                <label>Full Name <span class="label label-danger">Required</span></label>
                                <input type="text" class="form-control" name="ref_name1" placeholder="Full Name" required>
                            </div>
                            <div class="form-group col-md-4 col-sm-6">
                                <label>Relationship <span class="label label-danger">Required</span></label>
                                <input type="text" class="form-control" name="ref_relationship1" placeholder="Relationship" required>
                            </div>
                            <div class="form-group col-md-4 col-sm-6">
                                <label>Company <span class="label label-danger">Required</span></label>
                                <input type="text" class="form-control" name="ref_company1" placeholder="Company" required>
                            </div>
                            <div class="form-group col-md-4 col-sm-6">
                                <label>Phone <span class="label label-danger">Required</span></label>
                                <input type="text" class="form-control" name="ref_phone1" placeholder="Phone" required>
                            </div>
                            <div class="form-group col-md-4 col-sm-6">
                                <label>Email <span class="label label-danger">Required</span></label>
                                <input type="text" class="form-control" name="ref_email1" placeholder="Email" required>
                            </div>
                            <div class="form-group col-md-12 col-sm-12"><u><b>Reference 2 :</b></u></div>
                            <div class="form-group col-md-4 col-sm-6">
                                <label>Full Name</label>
                                <input type="text" class="form-control" name="ref_name2" placeholder="Full Name">
                            </div>
                            <div class="form-group col-md-4 col-sm-6">
                                <label>Relationship</label>
                                <input type="text" class="form-control" name="ref_relationship2" placeholder="Relationship">
                            </div>
                            <div class="form-group col-md-4 col-sm-6">
                                <label>Company</label>
                                <input type="text" class="form-control" name="ref_company2" placeholder="Company">
                            </div>
                            <div class="form-group col-md-4 col-sm-6">
                                <label>Phone</label>
                                <input type="text" class="form-control" name="ref_phone2" placeholder="Phone">
                            </div>
                            <div class="form-group col-md-4 col-sm-6">
                                <label>Email</label>
                                <input type="text" class="form-control" name="ref_email2" placeholder="Email">
                            </div>
                            <div class="form-group col-sm-12">
                                <label><input type="checkbox" name="referee_permission" id="referee_permission" style="margin-bottom: 4px;" required>
                                    Do you have the referee's permission for us to contact regarding your
                                    employment?</label>
                            </div>
                        </div>
                        <div class="bg-info col-xs-12 form-group">
                            <h4>DISCLAIMER AND SIGNATURE</h4>
                        </div>
                        <div class="">
                            <div class="form-group col-md-12">
                                <label><input type="checkbox" name="disclaimer" id="disclaimer" style="margin-bottom: 4px;" required data-backdrop="static" data-keyboard="false" data-target="#modal_terms" data-toggle="modal">
                                    I accept <a href="javascript:void(0)" data-backdrop="static" data-keyboard="false" data-target="#modal_terms" data-toggle="modal" title="Click to read Terms"><b>Terms & Conditions</b></a> and certify that
                                    my answers are true and complete to the best of my knowledge. If this
                                    application leads to employment, I understand that false or misleading
                                    information in my application or interview may result in my release. I will
                                    update my details with LSUK if it changes</label>
                            </div>
                            <div class="form-group col-md-4 col-sm-6">
                                <label>Signature <small>Write your name</small> <span class="label label-danger">Required</span></label>
                                <input type="text" class="form-control" name="signature_name" placeholder="Write your name" required>
                            </div>
                            <div class="form-group col-md-4 col-sm-6">
                                <label>Signature Date <span class="label label-danger">Required</span></label>
                                <input type="date" class="form-control" name="signature_date" required>
                            </div>
                            <div class="form-group col-md-6 col-sm-6">
                                <?php if (1 == 2) { ?>
                                    <script src='https://www.google.com/recaptcha/api.js'></script>
                                    <div class="g-recaptcha" data-sitekey="6LextRoUAAAAAGSGzslurL5xeNDw3lDDVkxM9rZe"></div>
                                    <br>
                                <?php } ?>
                                <button class="btn btn-primary" class="button" type="submit" name="btn_submit_registration">Submit
                                    &raquo;</button>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Terms & conditions Modal Starts -->
                <div class="modal fade" id="modal_terms" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-body">
                                <div style="border: 1px solid grey;padding: 10px;">
                                    <?php echo $acttObj->read_specific("em_format", "email_format", "id=41")["em_format"]; ?>
                                </div>
                            </div>
                            <div class="modal-footer justify-content-center">
                                <a onclick="$('#disclaimer').prop('checked', true);" type="button" class="btn btn-primary" data-dismiss="modal">Accept & Close</a>
                            </div>
                        </div>
                    </div>
                </div>

            </form>
        </section>
        <!-- end content -->
        <hr>
        <!-- begin footer -->
        <?php include 'source/footer.php'; ?>
        <!-- end footer -->
    </div>
    <!-- end container -->
</body>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.13/css/bootstrap-multiselect.css" rel="stylesheet" type="text/css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.13/js/bootstrap-multiselect.js" type="text/javascript"></script>
<script>
    function check_fields() {
        var fn = $("#first_name").val();
        var ln = $("#last_name").val();
        var dob = $("#dob").val();
        if (!fn) {
            $("#first_name").focus();
        } else if (!ln) {
            $("#last_name").focus();
        } else if (!dob) {
            $("#dob").focus();
        }
    }

    function check_existing($elem) {
        var fn = $("#first_name").val();
        var ln = $("#last_name").val();
        var nm = fn + " " + ln;
        var dob = $("#dob").val();
        var em = $($elem).val();
        if (nm && dob && em) {
            $.ajax({
                url: 'ajax_client_portal.php',
                method: 'post',
                dataType: 'json',
                data: {
                    'em': em,
                    'nm': nm,
                    'dob': dob,
                    'action': 'check_em'
                },
                success: function(data) {
                    if (data['status'] == "exist" && data['is_temp'] == "1") {
                        alert(data['msg']);
                        window.location.href = "interp_reg.php";
                    } else if (data['status'] == "exist" && data['is_temp'] == "0") {
                        alert(data['msg']);
                        $("#email").val("");
                        $("#email").focus();
                    } else if (data['status'] == "same_exist") {
                        alert(data['msg']);
                        $("#first_name").val("");
                        $("#last_name").val("");
                        $("#dob").val("");
                        $("#email").val("");
                        $("#first_name").focus();
                    }
                },
                error: function(xhr) {
                    alert("An error occured: " + xhr.status + " " + xhr.statusText);
                }
            });
        }
    }

    $(function() {
        $('.multi_class').multiselect({
            buttonWidth: '100px',
            includeSelectAllOption: true,
            numberDisplayed: 1,
            enableFiltering: true,
            enableCaseInsensitiveFiltering: true
        });
    });

    function max_upload($element) {
        if ($element.files[0].size > 26214400) {
            alert("File is too big ! Upload upto 25 MB file");
            $element.value = "";
        } else {
            return "1";
        }
    }
    var _URL = window.URL || window.webkitURL;
    $("#profile_photo").change(function(e) {
        var file, img;
        var output = document.getElementById('output');
        if ((file = this.files[0])) {
            img = new Image();
            img.onload = function() {
                output.src = _URL.createObjectURL(file);
            };
            img.src = _URL.createObjectURL(file);
        }

    });

    function pop_language(arr, value) {
        var index = arr.indexOf(value);
        if (index > -1) {
            arr.splice(index, 1);
        }
        return arr;
    }
    var selected_languages = [];
    var old_index;

    function set_language() {
        var element = $("#language");
        var text = $.trim($('#language option:selected').text());
        var value = $('#language option:selected').val();
        $("#append_language table").removeClass("hidden");
        var f2f_is_checked = $('input[name="interp"]').is(":checked") ? '' : 'hidden';
        $("#append_language table tr:last").after(
            "<tr class='tr_languages' id='tr_" + value + "'><td class='" + value + "'>" + text + "<input type='hidden' name='array_languages[]' value='" + text + "'/></td>" + 
            "<td><select onclick='old_level(this)' onchange='update_level(this)' class='form-control' name='selected_level[]' id='selected_language_" + value + "' style='width:165px;'>" +
            "<option value='1'>Native</option><option value='2'>Fluent</option><option value='3'>Intermediate</option><option value='4'>Basic</option>" +
            "</select></td>" +
            "<td><label class='btn btn-sm btn-default " + f2f_is_checked + "'><input class='can_do can_do_f2f' type='checkbox' name='can_do_f2f[]' value='interp'/> Face To Face</label> <label class='btn btn-sm btn-default'><input class='can_do' type='checkbox' name='can_do_tp[]' value='telep'/> Telephone</label> <label class='btn btn-sm btn-default'><input class='can_do' type='checkbox' name='can_do_tr[]' value='trans'/> Translation</label></td><td><button type='button' class='btn btn-danger btn-sm' onclick='remove_language(this)'>Remove</button></td>" +
            "</tr>"
        );
        $('#language option:selected').remove();
        selected_languages.push(text);
    }

    function remove_language(elem) {
        $(elem).closest('tr').remove();
        var old_text = $(elem).closest('tr').find("td:first").text();
        var old_value = $(elem).closest('tr').find("td:first").attr("class");
        $("#language option").eq($(elem).closest('tr').find("td:first").attr("class")).before($("<option></option>")
            .val(old_value).text(old_text));
        pop_language(selected_languages, old_text);
    }

    function old_level(elem) {
        old_index = $(elem).closest('tr').find("td:first").text();
    }

    function update_level(elem) {
        selected_languages[selected_languages.indexOf(old_index)] = $(elem).closest('tr').find("td:first").text();
    }

    function type_check(elem) {
        var type_val = $(elem).val();
        if (type_val != 'ci') {
            $('.ni_utr_sec').addClass('hidden');
            $('.uk_citizen').addClass('hidden');
            $('.div_passport_file').addClass('hidden');
            $('.permit-section').addClass('hidden');
            $('.personal-details').removeClass('hidden');
            $('.no-registration').addClass('hidden');
            $(elem).parents(".setup-content").find(".nextBtn").removeClass('hidden');
        }

        if (type_val == 'ci') {
            $('.nrpsi_mem').addClass('hidden');
            $('.ciol_mem').addClass('hidden');
            $('.iti_mem').addClass('hidden');
            $('.bsl_mem').addClass('hidden');
            $('.personal-details').addClass('hidden');

            $('.uk_citizen').removeClass('hidden');
            $('.ni_utr_sec').removeClass('hidden');
        } else if (type_val == 'nrpsi') {
            $('.ciol_mem').addClass('hidden');
            $('.iti_mem').addClass('hidden');
            $('.bsl_mem').addClass('hidden');

            $('.nrpsi_mem').removeClass('hidden');
            $('.uk_citizen').addClass('hidden');
            $('.div_passport_file').addClass('hidden');
            $('input[name="nrpsi_number"]').focus();
        } else if (type_val == 'ciol') {
            $('.nrpsi_mem').addClass('hidden');
            $('.iti_mem').addClass('hidden');
            $('.bsl_mem').addClass('hidden');

            $('.ciol_mem').removeClass('hidden');
            $('.uk_citizen').addClass('hidden');
            $('.div_passport_file').addClass('hidden');
            $('input[name="ciol_number"]').focus();
        } else if (type_val == 'iti') {
            $('.nrpsi_mem').addClass('hidden');
            $('.ciol_mem').addClass('hidden');
            $('.bsl_mem').addClass('hidden');

            $('.iti_mem').removeClass('hidden');
            $('.uk_citizen').addClass('hidden');
            $('.div_passport_file').addClass('hidden');
            $('input[name="iti_number"]').focus();
        } else if (type_val == 'bsl') {
            $('.nrpsi_mem').addClass('hidden');
            $('.ciol_mem').addClass('hidden');
            $('.iti_mem').addClass('hidden');

            $('.bsl_mem').removeClass('hidden');
            $('.uk_citizen').addClass('hidden');
            $('.div_passport_file').addClass('hidden');
            $('input[name="asli_number"]').focus();
        }
        // Show and hide options based on BSL selection
        var $language = $("#language");
        var $skills = $("#skills");
        if (type_val == 'bsl') {
            $language.find("[value='141'], [value='142']").prop('disabled', false);
            $skills.find("[value='BSL']").prop('disabled', false);
        } else {
            $language.find("[value='141'], [value='142']").prop('disabled', true);
            $skills.find("[value='BSL']").prop('checked', false).prop('disabled', true);
        }

        $skills.multiselect('refresh');
    }

    function changer(elem) {
        var value = $(elem).val();
        if (value == 'No') {
            $('.div_permit').removeClass('hidden');
            $('.div_passport_file').addClass('hidden');
            $('.uk_citizen_fields').removeAttr("required");
            $('.permit-section').removeClass('hidden');
            $('.personal-details').addClass('hidden');
        } else {
            $('.permit_fields').removeAttr("required");
            $('.uk_citizen_fields').attr('required', "required");
            $('.div_permit').addClass('hidden');
            $("input[name='permit']").prop('checked', false);
            $('.div_permit_file').addClass('hidden');
            $('.div_passport_file').removeClass('hidden');
            $('.permit-section').addClass('hidden');
            $('.personal-details').removeClass('hidden');
            $('.no-registration').addClass('hidden');
            $(elem).parents(".setup-content").find(".nextBtn").removeClass('hidden');
        }
    }

    function toggle_auto_dbs() {
        if ($("#is_dbs_auto").is(":checked")) {
            $('.div_dbs_file').addClass('hidden');
            $('.dbs_fields').removeAttr("required");
            $('.dbs_auto_number').attr('required', "required");
            $('.div_dbs_auto_number').removeClass('hidden');
            $('.dbs_auto_number').focus();
        } else {
            $('.dbs_fields').attr('required', "required");
            $('.div_dbs_file').removeClass('hidden');
            $('.dbs_auto_number').removeAttr('required');
            $('.div_dbs_auto_number').addClass('hidden');
        }
    }

    function toggle_lsuk_dbs() {
        if ($("#request_lsuk_dbs").is(":checked")) {
            $('.div_dbs_file, .div_dbs_auto_number').addClass('hidden');
            $('.dbs_fields, .dbs_auto_number').removeAttr("required");
        } else {
            $('.dbs_fields').attr('required', "required");
            $('.div_dbs_file').removeClass('hidden');
            $('.dbs_auto_number').removeAttr('required');
            $('.div_dbs_auto_number').addClass('hidden');
        }
    }

    function work_evid_change(elem) {
        var value = $(elem).val();
        if (value == 'No') {
            $('.div_work_evid_file').addClass('hidden');
            $('.work_evid_fields').removeAttr("required");
            $('.personal-details').addClass('hidden');
            $('.no-registration').removeClass('hidden');
            $(elem).parents(".setup-content").find(".nextBtn").addClass('hidden');
        } else {
            $('.work_evid_fields').attr('required', "required");
            $('.div_work_evid_file').removeClass('hidden');
            $('.personal-details').removeClass('hidden');
            $('.no-registration').addClass('hidden');
            $(elem).parents(".setup-content").find(".nextBtn").removeClass('hidden');
        }
    }

    function permit_upload(elem) {
        var value = $(elem).val();
        if (value == 'No') {
            $('.div_permit_file').addClass('hidden');
            $('.permit_fields').removeAttr("required");
        } else {
            $('.permit_fields').attr('required', "required");
            $('.div_permit_file').removeClass('hidden');
        }
    }

    function translation_qualification(elem) {
        var value = $(elem).val();
        if (value == 'Yes') {
            $('.div_translation_qualification').removeClass('hidden');
            $('input[name="int_qualification_file"]').attr('required', "required");
        } else {
            $('.div_translation_qualification').addClass('hidden');
            $('input[name="int_qualification_file"]').removeAttr("required");
        }
    }

    function experience(elem) {
        var value = $(elem).val();
        if (value == 'Yes') {
            $('.div_experience').removeClass('hidden');
        } else {
            $('.div_experience').addClass('hidden');
        }
    }

    function drive(elem) {
        var value = $(elem).val();
        if (value == 'Yes') {
            $('.div_driving_license').removeClass('hidden');
            $('input[name="driving_license_file"]').attr('required', "required");
        } else {
            $('.div_driving_license').addClass('hidden');
            $('input[name="driving_license_file"]').removeAttr("required");
        }
    }

    function master(elem) {
        var value = $(elem).val();
        if (value == 'Yes') {
            $('.div_master').removeClass('hidden');
            $('input[name="master_file"]').attr('required', "required");
        } else {
            $('.div_master').addClass('hidden');
            $('input[name="master_file"]').removeAttr("required");
        }
    }

    function other_city(elem = '', use_custom = 0) {
        var selected_city = $("#selected_city option:selected").val();
        if (selected_city != 'Not in List') {
            $('.other_city_field').val(selected_city);
        }
        if (use_custom == 1 || !selected_city || selected_city == 'Not in List') {
            $('.other_city_field').val('');
            $("#selected_country").removeAttr("required");
            $('.div_other_city_field,.other_city_field').removeClass('hidden');
            $('.other_city_field').attr('required', "required");
            $('.other_city_field').focus();
        } else {
            $("#selected_country").attr('required', "required");
            $('.div_other_city_field,.other_city_field').addClass('hidden');
            $('.other_city_field').removeAttr("required");
            $('#selected_city').focus();
        }
    }

    function get_cities(elem) {
        $('.div_other_city_field,.other_city_field').addClass('hidden');
        $('.other_city_field').val("");
        var country_name = $(elem).val();
        if (country_name) {
            $.ajax({
                url: 'lsuk_system/ajax_add_interp_data.php',
                method: 'post',
                dataType: 'json',
                data: {
                    country_name: country_name,
                    without_label: 1,
                    type: 'get_cities_of_country'
                },
                success: function(data) {
                    if (data['cities']) {
                        $('.append_cities').removeClass('hidden');
                        $('.append_cities').html(data['cities']);
                        //$("#selected_city").multiselect('rebuild');
                    } else {
                        $('.append_cities').addClass('hidden');
                        other_city('', 1);
                    }
                },
                error: function(xhr) {
                    alert("An error occured: " + xhr.status + " " + xhr.statusText);
                }
            });
        }
    }
    $(document).on('change', 'input[type=radio][name=int_type]', function() {
        if ($(this).val() != "ci") {
            $('input[type=radio][name=citizen]').prop('checked', false);
            $('input[type=radio][name=work_evid]').prop('checked', false);
        }
    });
    $(document).ready(function() {

        $('#acntCode').keyup(function() {
            var lengthT = $(this).val().length;
            var foo = $(this).val().split("-").join("");
            if (foo.length > 0) {
                foo = foo.match(new RegExp('.{1,2}', 'g')).join("-");
                $(this).val(foo);
            }
            if (foo.length > 8) {
                $(this).val($(this).val().substring(0, 7));
            }

        });
        $('#acNo').keydown(function() {
            var acNo_length = $(this).val().length;
            if (acNo_length > 8) {
                $(this).val($(this).val().substring(0, 7));
            }

        });

        var navListItems = $('div.setup-panel div a'),
            allWells = $('.setup-content'),
            allNextBtn = $('.nextBtn');
        allWells.hide();
        navListItems.click(function(e) {
            e.preventDefault();
            var $target = $($(this).attr('href')),
                $item = $(this);
            if (!$item.hasClass('disabled')) {
                navListItems.removeClass('btn-primary').addClass('btn-default');
                $item.addClass('btn-primary');
                allWells.hide();
                $target.show();
                $target.find('.form-control:eq(0)').focus();
            }
        });
        allNextBtn.click(function() {
            var curStep = $(this).closest(".setup-content"),
                curStepBtn = curStep.attr("id"),
                nextStepWizard = $('div.setup-panel div a[href="#' + curStepBtn + '"]').parent().next()
                .children("a"),
                curInputs = curStep.find(".form-control"),
                isValid = true;
                $(".form-control").removeClass("is-invalid");
                $(".form-group").find("label").removeClass("text-danger");
                for (var i = 0; i < curInputs.length; i++) {
                    if (!curInputs[i].validity.valid) {
                        isValid = false;
                        $(curInputs[i]).closest(".form-control").addClass("is-invalid");
                        $(curInputs[i]).closest(".form-group").find("label").addClass("text-danger");
                    }
                }

                if (curStepBtn == 'step-1') {
                    // console.log("step-1");
                    var int_type = $("input[name='int_type']:checked").val();
                    if ((!$('input[name="work_type"]:checked').val() || $('input[name="work_type"]:checked').val().length == 0)) {
                        isValid = false;
                    }
                    if (int_type && int_type != "") {
                        if (int_type == "ci") {
                            if ($('input[name="citizen"]:checked').val()) {
                                if ($('input[name="citizen"]:checked').val() == "No") {
                                    if ($('input[name="work_evid"]:checked').val()) {
                                        if ($('input[name="work_evid"]:checked').val() == "No") {
                                            isValid = false;
                                        } else {
                                            if ($('input[name="work_evid_file"]').val() && $('input[name="work_evid_issue_date"]').val() && $('input[name="work_evid_expiry_date"]').val()) {
                                                isValid = true;
                                            } else {
                                                isValid = false;
                                            }
                                        }
                                    } else {
                                        isValid = false;
                                    }
                                } else {
                                    if ($('input[name="passport_file"]').val() && $('input[name="passport_number"]').val() && $('input[name="passport_issue_date"]').val() && $('input[name="passport_expiry_date"]').val()) {
                                        isValid = true;
                                    } else {
                                        isValid = false;
                                    }
                                }
                            } else {
                                isValid = false;
                            }
                        } else if (int_type == "nrpsi") {
                            if ($('input[name="nrpsi_number"]').val()) {
                                $('input[name="nrpsi_number"]').removeClass("is-invalid");
                                $('input[name="nrpsi_number"]').prev("label").removeClass("text-danger");
                                isValid = true;
                            } else {
                                $('input[name="nrpsi_number"]').addClass("is-invalid");
                                $('input[name="nrpsi_number"]').prev("label").addClass("text-danger");
                                isValid = false;
                            }
                        } else if (int_type == "ciol") {
                            if ($('input[name="ciol_number"]').val()) {
                                $('input[name="ciol_number"]').removeClass("is-invalid");
                                $('input[name="ciol_number"]').prev("label").removeClass("text-danger");
                                isValid = true;
                            } else {
                                $('input[name="ciol_number"]').addClass("is-invalid");
                                $('input[name="ciol_number"]').prev("label").addClass("text-danger");
                                isValid = false;
                            }
                        } else if (int_type == "iti") {
                            if ($('input[name="iti_number"]').val()) {
                                $('input[name="iti_number"]').removeClass("is-invalid");
                                $('input[name="iti_number"]').prev("label").removeClass("text-danger");
                                isValid = true;
                            } else {
                                $('input[name="iti_number"]').addClass("is-invalid");
                                $('input[name="iti_number"]').prev("label").addClass("text-danger");
                                isValid = false;
                            }
                        } else if (int_type == "bsl") {
                            if ($('input[name="asli_number"]').val()) {
                                $('input[name="asli_number"]').removeClass("is-invalid");
                                $('input[name="asli_number"]').prev("label").removeClass("text-danger");
                                isValid = true;
                            } else {
                                $('input[name="asli_number"]').addClass("is-invalid");
                                $('input[name="asli_number"]').prev("label").addClass("text-danger");
                                isValid = false;
                            }
                        }

                    } else {
                        isValid = false;
                    }
                } else if (curStepBtn == 'step-2') {
                    if ($('input[type="checkbox"]:checked').length == 0) {
                        isValid = false;
                    } else {
                        isValid = true;
                    }
                } else if (curStepBtn == 'step-3') {
                    if (!$('select[name="selected_country"] option').filter(':selected').val() || $('select[name="selected_country"] option').filter(':selected').val().length == 0) {
                        isValid = false;
                    } else {
                        isValid = true;
                    }
                } else if (curStepBtn == 'step-4') {
                    if ($('tr.tr_languages').length > 0) {
                        isValid = true;
                    } else {
                        isValid = false;
                    }
                    // if((!$('input[name="work_type"]:checked').val() || $('input[name="work_type"]:checked').val().length==0)){
                    //     isValid = false;
                    // }   
                } else if (curStepBtn == 'step-5') {
                    if ((!$('input[name="is_master"]:checked').val() || $('input[name="is_master"]:checked').val().length == 0) || (!$('input[name="translation_qualifications"]:checked').val() || $('input[name="translation_qualifications"]:checked').val().length == 0) || (!$('input[name="is_experience"]:checked').val() || $('input[name="is_experience"]:checked').val().length == 0)) {
                        isValid = false;
                    } else {
                        isValid = true;
                    }
                }

                // console.log(isValid);
                // console.log(int_type);
                if (isValid) nextStepWizard.removeAttr('disabled').removeClass('disabled').trigger('click');
                // nextStepWizard.removeAttr('disabled').removeClass('disabled').trigger('click')
            });

        $('div.setup-panel div a.btn-primary').trigger('click');
    });

    function show_dbs(element) {
        if ($(element).is(":checked")) {
            $('.dbs_fields').attr('required', "required");
            $('.div_dbs_file, .div_auto_dbs, .div_lsuk_dbs').removeClass('hidden');
            if ($("#is_dbs_auto").is(":checked")) {
                $('.div_dbs_auto_number').removeClass('hidden');
            }
            $('.can_do_f2f').parents("label").removeClass("hidden");
        } else {
            $('.div_dbs_file, .div_auto_dbs, .div_dbs_auto_number, .div_lsuk_dbs').addClass('hidden');
            $('.dbs_fields').removeAttr("required");
            $('.can_do_f2f').prop("checked", false);
            $('.can_do_f2f').parents("label").addClass("hidden");
        }
    }

    var countdown;
    function start_count_down(duration) {
        var timer = duration, minutes, seconds;
        countdown = setInterval(function() {
        minutes = parseInt(timer / 60, 10);
        seconds = parseInt(timer % 60, 10);
        minutes = minutes < 10 ? "0" + minutes : minutes;
        seconds = seconds < 10 ? "0" + seconds : seconds;
        $("#resend_timer").html("<p class='text-success lh'><i class='bi bi-check-circle'></i> OTP code sent successfully!</p><p class='lh'>Resend OTP in: <i>" + minutes + " minutes " + seconds + " seconds</i></p>");
        $("#otp").focus();
        if (--timer < 0) {
            clearInterval(countdown);
            $("#resend_timer").html('<i class="bi bi-info-circle"></i> <i>Didn\'t received OTP code?</i> <a class="btn btn-sm btn-warning bg-theme text-white border" href="javascript:void(0)" onclick="resend_otp(this)">Resend OTP</a>');
        }
        }, 1000);
    }

    function resend_otp(element) {
        send_otp(element, 1);
    }
    
    function send_otp(element, is_resent_otp = 0) {
        var current_button = $(element);
        if (is_resent_otp == 1 || current_button.attr("data-sent") == 0) {
            var curInputs = $("#step-2").find(".form-control"),
            isValid = true;
            $(".form-control").removeClass("is-invalid");
            $(".form-group").find("label").removeClass("text-danger");
            for (var i = 0; i < curInputs.length; i++) {
                if (!curInputs[i].validity.valid) {
                    isValid = false;
                    console.log($(curInputs[i]).closest(".form-control").attr("name"));
                    $(curInputs[i]).closest(".form-control").addClass("is-invalid");
                    $(curInputs[i]).closest(".form-group").find("label").addClass("text-danger");
                }
            }
            if (!$('input[name="interp"]').is(":checked") && !$('input[name="telep"]').is(":checked") && !$('input[name="trans"]').is(":checked")) {
                $(".div_interpreting_types").addClass("is-invalid");
                isValid = false;
            } else {
                $(".div_interpreting_types").removeClass("is-invalid");
            }
            if (isValid) {
                $("#step-2").find(".form-control").removeClass("is-invalid");
                $("#step-2").find(".form-group").find("label").removeClass("text-danger");
                var email = $("#email").val();
                if (email) {
                    current_button.html("Please Wait ...");
                    current_button.removeAttr("onclick");
                    $.ajax({
                        url: 'process/interp_reg.php',
                        method: 'post',
                        dataType: 'json',
                        data: {
                            redirect_url: window.location.href,
                            send_otp: email
                        },
                        success: function(res) {
                            $('#response_message').removeClass("hidden");
                            $('#response_message').html(res['message']);
                            if (res['status'] == 1) {
                                if (is_resent_otp == 0) {
                                    current_button.html('Next <i class="fa fa-angle-right"></i><i class="fa fa-angle-right"></i>');
                                    current_button.attr("data-sent", "1");
                                } else {
                                    current_button.text('Resend OTP');
                                }
                                current_button.addClass("hidden");
                                $(".div_otp, .btn_verify_otp, #otp").removeClass("hidden");
                                $("#otp").focus();
                                $("#otp").attr("required", "required");
                                start_count_down(60);
                            } else {
                                $("#otp").removeAttr("required");
                                if (is_resent_otp == 0) {
                                    current_button.html('Next <i class="fa fa-angle-right"></i><i class="fa fa-angle-right"></i>');
                                    current_button.attr("onclick", "send_otp(this)");
                                } else {
                                    current_button.text('Resend OTP');
                                    current_button.attr("onclick", "resend_otp(this)");
                                }
                                $("html,body").animate({
                                scrollTop: 0
                                }, "slow");
                            }
                        },
                        error: function(data) {
                            $("#otp").removeAttr("required");
                            $('#response_message').removeClass("hidden");
                            $('#response_message').html('<div class="alert alert-warning alert-dismissible show" role="alert">OTP Sending Failed: Failed to send OTP to your Email Address! Please try again later</div>');
                            current_button.html('Next <i class="fa fa-angle-right"></i><i class="fa fa-angle-right"></i>');
                            current_button.attr("onclick", "send_otp(this)");
                            $("html,body").animate({
                                scrollTop: 0
                            }, "slow");
                        }
                    });
                } else {
                    $("#otp").removeAttr("required");
                    alert("Please enter a valid email and try again!");
                    $("#email").focus();
                    current_button.html('Next <i class="fa fa-angle-right"></i><i class="fa fa-angle-right"></i>');
                    current_button.attr("onclick", "send_otp(this)");
                }
            }
        }
    }
    function verify_otp(element) {
        var payload = {redirect_url: window.location.href, email: $("#email").val(), verify_otp: $("#otp").val()};
        var current_element = $(element);
        $.ajax({
            url: 'process/interp_reg.php',
            method: 'post',
            dataType: 'json',
            data: payload,
            success: function(response) {
                $('#response_message').removeClass("hidden");
                $('#response_message').html(response['message']);
                if (response['status'] == 1) {
                    current_element.addClass("hidden");
                    $('.div_otp, .div_resend_timer').addClass("hidden");
                    $(".div_otp_actions").find(".nextBtn").removeClass("hidden");
                }
            },
            error: function(data) {
                $('#response_message').removeClass("hidden");
                $('response_message').html('<div class="alert alert-danger alert-dismissible show col-md-7 p-2" role="alert">Server Error : We cannot verify your OTP now! Please try again later.<button type="button" class="btn btn-sm p-3 btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div></div>');
            }
        });
    }
    // $('.validate_phone').on('input', function() {
    //     var phoneNumber = $(this).val();
    //     var isValid = /^0[0-9]{0,10}$/.test(phoneNumber);

    //     if (!isValid) {
    //     $(this).val(function(_, value) {
    //         // return value.slice(0, -1);
    //     });
    //     }
    // });
    $('.validate_phone').on('input change', function() {
        var phoneNumber = $(this).val();
        var isValid = /^0[0-9]*$/.test(phoneNumber);

        if (!isValid) {
            $(this).val(function(_, value) {
                return value.replace(/[^0-9]/g, '').slice(0, 11);
            });
        }
    });
    function checkAccountNumber(input) {
        var inputValue = input.value;
        if (inputValue.length === 8 && !/(\d)\1{7}/.test(inputValue)) {
            // If the input has exactly 8 digits and not all digits are the same, do nothing
            return;
        } else {
            alert("System can not accept this type of Account Number, please contact LSUK for more details.\nAccount number needs to be entered as a non-unique and exact 8 digits!");
            input.value = "";
            input.focus();
        }
    }
    function checkAccountSortCode(input) {
        var inputValue = input.value.replace(/[^0-9]/g, ''); // Remove non-digit characters
        if (/^(\d)\1{5}$/.test(inputValue)) {
            alert("System can not accept this type of SortCode, please contact LSUK for more details.\nSortCode needs to be entered as a non-unique and exact 6 digits!");
            input.value = ""; // Clear the input field
            input.focus(); // Focus on the input field
        }
    }
</script>

</html>