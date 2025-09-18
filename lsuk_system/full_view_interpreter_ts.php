<?php if (isset($_GET['view_id'])) {
    if (session_id() == '' || !isset($_SESSION)) {
        session_start();
    }
    include 'db.php';
    include_once('function.php');
    include 'class.php';
    $allowed_type_idz = "41,139,140,162";
    //Check if user has current action allowed
    if ($_SESSION['is_root'] == 0) {
        $get_page_access = $acttObj->read_specific("id", "action_permissions", "user_id=" . $_SESSION['userId'] . " AND action_id IN (" . $allowed_type_idz . ")")['id'];
        if (empty($get_page_access)) {
            die("<center><h2 class='text-center text-danger'>You do not have access to <u>View Interpreter Profile</u> action for jobs!<br>Kindly contact admin for further process.</h2></center>");
        }
    }
    // Get some actions for this page from registered interpreter list
    $get_actions = explode(",", $acttObj->read_specific("GROUP_CONCAT(action_permissions.action_id) as actions", "action_permissions,route_actions", "action_permissions.action_id=route_actions.id AND route_actions.route_id=28 AND action_permissions.user_id=" . $_SESSION['userId'])['actions']);
    $action_activate_de_activate_account = $_SESSION['is_root'] == 1 || in_array(52, $get_actions);
    $action_edit_profile = $_SESSION['is_root'] == 1 || in_array(42, $get_actions);
    $action_view_documents = $_SESSION['is_root'] == 1 || in_array(186, $get_actions);
    $action_edit_documents = $_SESSION['is_root'] == 1 || in_array(187, $get_actions);
    $action_addnotes = $_SESSION['is_root'] == 1 || in_array(205, $get_actions);
    $view_id = $_GET['view_id'];
    $table = 'interpreter_reg';
    $query = "SELECT * FROM $table where id=$view_id";
    $result = mysqli_query($con, $query);
    $row = mysqli_fetch_assoc($result);
    $name = $row['name'];
    $email = $row['email'];
    $profile_pic = $row['interp_pix'];
    $contactNo = $row['contactNo'];
    $contactNo2 = $row['contactNo2'];
    $other_number = $row['other_number'];
    $rph = $row['rph'];
    $interp = $row['interp'];
    $telep = $row['telep'];
    $trans = $row['trans'];
    $gender = $row['gender'];
    $city = $row['city'];
    $address = $row['address'];
    $interp_code = $row['code'] ?: "id-" . $row['id'];
    $applicationForm = $row['applicationForm'];
    $agreement = $row['agreement'];
    $crbDbs = $row['crbDbs'];
    $identityDocument = $row['identityDocument'];
    if($row['uk_citizen'] == 0){ $identityDocument = 'NA';}
    $nin = $row['nin'];
    $cv = $row['cv'];
    $dps = $row['dps'];
    $anyOther = $row['anyOther'];
    $anyCertificate = $row['anyCertificate'];
    $rpm = $row['rpm'];
    $rpu = $row['rpu'];
    $ratetravelexpmile = $row['ratetravelexpmile'];
    $ratetravelworkmile = $row['ratetravelworkmile'];
    $ni = $row['ni'];
    $buildingName = $row['buildingName'];
    $line1 = $row['line1'];
    $line2 = $row['line2'];
    $line3 = $row['line3'];
    $postCode = $row['postCode'];
    $bnakName = $row['bnakName'];
    $acName = $row['acName'];
    $acntCode = str_replace("-", "", $row['acntCode']);
    $acNo = $row['acNo'];
    $dob = $row['dob'];
    $reg_date = $row['reg_date'];
    $interp = $row['interp'];
    $telep = $row['telep'];
    $trans = $row['trans'];
    $dbs_file = $row['dbs_file'];
    $dbs_auto_number = $row['dbs_auto_number'];
    $id_doc_file = $row['id_doc_file'];
    $application_file = $row['applicationForm_file'];
    $agreement_file = $row['agreement_file'];
    $work_evid_file = $row['work_evid_file'];
    if ($work_evid_file) {
        $work_evid_issue_date = $row['work_evid_issue_date'];
        $work_evid_expiry_date = $row['work_evid_expiry_date'];
    }
    if ($row['uk_citizen'] == 1){ $work_evid_file = 'NA'; }
    $interp_pix = $row['interp_pix'] ?: "profile.png";
    $qint = '';
    $reg_num = '';
    if ($row['is_nrpsi'] != 0) {
        $qint = 'nrpsi';
    } elseif ($row['is_ciol'] != 0) {
        $qint = 'ciol';
    } elseif ($row['is_iti'] != 0) {
        $qint = 'iti';
    } elseif ($row['is_asli'] != 0) {
        $qint = 'asli';
    }
    $array_order_types = array(1 => "Face.To.Face", 2 => "Telephone", 3 => "Translation");
    $array_notice_period = array(1 => "24 hrs", 2 => "48 hrs", 3 => "48+ hrs");
    $array_notice_period_sign = array(1 => "7 days", 2 => "14 days", 3 => "14+ days");
?>
    <html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

    <head>
        <title>Interpreter Details</title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="css/bootstrap.css">
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
        <style>
            .fa-custom {
                font-size: 1.2em;
            }

            .b {
                color: #fff;
            }

            a:link,
            a:visited {
                color: #337ab7;
            }

            .rm:hover {
                color: #fff;
                background-color: #ef3737;
                cursor: pointer;
            }
            
        </style>
    </head>

    <body>
        <div class="col-md-12">
            <h2 class="text-center">Linguist's Information for <span class="label label-primary b"><?php echo ucwords($name); ?></span></h2>
            <ul class="nav nav-tabs">
                <li <?= !isset($_GET['show_notes']) && !isset($_GET['show_lateness']) && !isset($_GET['show_cancellation']) && !isset($_GET['show_amendments']) ? "class='active'" : "" ?>><a data-toggle="tab" href="#profile_details"><i class="fa fa-user"></i> Profile Details</a></li>
                <?php if ($action_view_documents) {
                    echo '<li><a data-toggle="tab" href="#int_documents"><i class="fa fa-briefcase"></i> Interpreter Documents</a></li>';
                }
                if ($_SESSION['prv'] == "Management") {
                    echo '<li><a data-toggle="tab" href="#feed_record"> <b>* Feedback Record *</b></a></li>';
                }
                if ($action_addnotes) {
                    echo '<li' . (isset($_GET['show_notes']) ? " class='active'" : "") . '><a data-toggle="tab" href="#tab_interpreter_notes"><i class="fa fa-edit"></i> Interpreter Notes</li>';
                }
                    echo '<li' . (isset($_GET['show_lateness']) ? " class='active'" : "") . '><a data-toggle="tab" href="#tab_interpreter_lateness"><i class="fa fa-exclamation-circle text-warning"></i> Lateness Summary</a></li>
                    <li' . (isset($_GET['show_cancellation']) ? " class='active'" : "") . '><a data-toggle="tab" href="#tab_interpreter_cancellation"><i class="fa fa-remove text-danger"></i> Cancellations</a></li>
                    <li' . (isset($_GET['show_amendments']) ? " class='active'" : "") . '><a data-toggle="tab" href="#tab_interpreter_amendments"><i class="fa fa-edit text-primary"></i> Amendments</a></li>';
             ?>
            </ul>
            <div class="tab-content">
                <div id="profile_details" class="tab-pane fade <?= !isset($_GET['show_notes']) && !isset($_GET['show_lateness']) && !isset($_GET['show_cancellation']) && !isset($_GET['show_amendments']) ? 'active in' : '' ?>">
                    <br>
                    <table class="table table-bordered">
                        <tbody>
                            <tr class="bg-primary text-center">
                                <td colspan="4"><b>Personal Details</b></td>
                            </tr>
                            <tr>
                                <td width="30%">
                                    <img width="100%" class="img img-responsive img-thumbnail" src="file_folder/interp_photo/<?php echo $interp_pix; ?>" alt="Profile Picture" title="Profile Picture for <?php echo $row['name']; ?>">
                                    <?php if ($action_edit_profile) { ?>
                                        <br><button style="margin-top:2px;" type="button" onClick="popupwindow('crop_photo.php?id=<?php echo $row['id'] ?>', 'Crop Interpreter Photo', 1100, 910);" class="btn btn-sm btn-info">Update Photo</button>
                                    <?php } ?>
                                    <br><i><?php echo $row['week_remarks']; ?></i>
                                </td>
                                <td colspan="3">
                                    <style>
                                        .a {
                                            color: #000;
                                        }
                                    </style>
                                    <table class="table">
                                        <tbody>
                                            <tr>
                                                <td>Date of Birth</td>
                                                <td>
                                                    <h3 style="display: inline;"><span class="label a pull-right"><?php echo $misc->dated($row['dob']) ?: '- - -'; ?></span></h3>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Mobile Number</td>
                                                <td>
                                                    <h3 style="display: inline;"><span class="label a pull-right"><?php echo $contactNo ?: '- - -'; ?></span></h3>
                                                </td>
                                            </tr>
                                            <?php if ($contactNo2) { ?>
                                                <tr>
                                                    <td>Landline Number</td>
                                                    <td>
                                                        <h3 style="display: inline;"><span class="label a pull-right"><?php echo $contactNo2 ?: '- - -'; ?></span></h3>
                                                    </td>
                                                </tr>
                                            <?php } ?>
                                            <?php if ($other_number) { ?>
                                                <tr>
                                                    <td>Other Number</td>
                                                    <td>
                                                        <h3 style="display: inline;"><span class="label a pull-right"><?php echo $other_number ?: '- - -'; ?></span></h3>
                                                    </td>
                                                </tr>
                                            <?php } ?>
                                            <tr>
                                                <td>Email Address</td>
                                                <td>
                                                    <h3 style="display: inline;"><span class="label a pull-right"><?php echo $email ?: '- - -'; ?></span></h3>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>National Insurance #</td>
                                                <td>
                                                    <h3 style="display: inline;"><span class="label a pull-right"><?php echo $ni ?: 'Not Provided'; ?></span></h3>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Registered Since</td>
                                                <td>
                                                    <h3 style="display: inline;"><span class="label a pull-right"><?php echo $misc->dated($row['dated']) ?: '- - -'; ?></span></h3>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Subscribe Status</td>
                                                <td>
                                                    <h3 style="display: inline;"><span class="label a pull-right"><?php echo $row['subscribe'] == '1' ? '<span class="label label-success">Subscribed <i class="fa fa-check-circle"></i></span>' : '<span class="label label-danger">Unsubscribed <i class="fa fa-remove"></i></span>'; ?></span></h3>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="2" align="center"><b><i class="fa fa-map-marker"></i> <?php echo $buildingName . ' ' . $line1 . ' ' . $line2 . ' ' . $line3 . ' ' . $city . ' ' . $postCode; ?></b></td>
                                            </tr>
                                            <?php if ($_SESSION['prv'] == 'Management' || $_SESSION['userId'] == 13) {
                                                if ($_SESSION['prv'] == 'Management') { ?>
                                                    <tr>
                                                        <td colspan="2" align="center">
                                                            <?php if ($action_activate_de_activate_account) {
                                                                if ($row['on_hold'] == 'No') { ?>
                                                                    <a href="javascript:void(0)" onClick="MM_openBrWindow('onhold.php?interpreter_id=<?php echo $row['id']; ?>','_blank','scrollbars=yes,resizable=yes,width=600,height=300,left=540,top=283')"><input type="image" width="25" src="images/stop.png" title="Hold this interpreter"></a>
                                                                <?php } else { ?>
                                                                    <a href="javascript:void(0)" onClick="MM_openBrWindow('onhold.php?interpreter_id=<?php echo $row['id']; ?>','_blank','scrollbars=yes,resizable=yes,width=600,height=300,left=540,top=283')"><input type="image" width="25" src="images/play.png" title="Unhold this interpreter"></a>
                                                            <?php }
                                                            } ?>
                                                        </td>
                                                    </tr>
                                                <?php } ?>
                                                <tr>
                                                    <td colspan="2" align="center">
                                                        <?php if ($_SESSION['prv'] == 'Management') {
                                                            if ($row['active'] == '0') { ?>
                                                                <a style="text-decoration:none;" href="javascript:void(0)" onClick="MM_openBrWindow('activate.php?interpreter_id=<?php echo $row['id']; ?>','_blank','scrollbars=yes,resizable=yes,width=600,height=300,left=540,top=283')"><span class="btn-sm btn-danger">De-Activate <i class="fa fa-exclamation" title="De-Activate this interpreter"></i></span></a>
                                                            <?php } else { ?>
                                                                <a style="text-decoration:none;" href="javascript:void(0)" onClick="MM_openBrWindow('activate.php?interpreter_id=<?php echo $row['id']; ?>','_blank','scrollbars=yes,resizable=yes,width=600,height=300,left=540,top=283')"><span class="btn-sm btn-success">Activate <i class="fa fa-check-circle" title="Activate this interpreter"></i></span></a>
                                                            <?php } ?>
                                                            <?php if ($row['specific_agreed'] == '1') { ?>
                                                                <a style="text-decoration:none;" href="javascript:void(0)" onClick="update_action_interpreter(0,<?php echo $row['id']; ?>,'specific_agreed')"><span class="btn-sm btn-danger">Remove Travel Cost <i class="fa fa-exclamation" title="Remove travel cost"></i></span></a>
                                                            <?php } else { ?>
                                                                <a style="text-decoration:none;" href="javascript:void(0)" onClick="update_action_interpreter(1,<?php echo $row['id']; ?>,'specific_agreed')"><span class="btn-sm btn-success">Agree For Travel Cost <i class="fa fa-check-circle" title="Agree with travel cost"></i></span></a>
                                                        <?php }
                                                        } ?>
                                                        <?php if ($row['availability_option'] == '1') { ?>
                                                            <a style="text-decoration:none;" href="javascript:void(0)" onClick="update_action_interpreter(0,<?php echo $row['id']; ?>,'availability')"><span class="btn-sm btn-warning">Remove availability notification <i class="fa fa-remove" title="Remove availability notifications"></i></span></a>
                                                        <?php } else { ?>
                                                            <a style="text-decoration:none;" href="javascript:void(0)" onClick="update_action_interpreter(1,<?php echo $row['id']; ?>,'availability')"><span class="btn-sm btn-info">Notify availability <i class="fa fa-check-circle" title="Send availability notifications"></i></span></a>
                                                        <?php } ?>
                                                    </td>
                                                </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                            <tr class="bg-primary text-center">
                                <td colspan="4"><b>Work Details</b></td>
                            </tr>
                            <tr>
                                <td width="200" align="left">Face to Face Interpreting</td>
                                <td width="200" align="left"><?php echo $interp == 'Yes' ? '<span class="label label-success">Yes <i class="fa fa-check-circle"></i></span>' : '<span class="label label-danger">No <i class="fa fa-remove"></i></span>'; ?></td>
                                <td width="200" align="left">Rate per Hour</td>
                                <td width="200" align="left"><?php echo $rph; ?></td>
                            </tr>
                            <tr>
                                <td width="200" align="left">Telephone Interpreting</td>
                                <td width="200" align="left"><?php echo $telep == 'Yes' ? '<span class="label label-success">Yes <i class="fa fa-check-circle"></i></span>' : '<span class="label label-danger">No <i class="fa fa-remove"></i></span>'; ?></td>
                                <td width="200" align="left">Rate per Minute</td>
                                <td width="200" align="left"><?php echo $rpm; ?></td>
                            </tr>
                            <tr>
                                <td align="left">Translation</td>
                                <td align="left"><?php echo $trans == 'Yes' ? '<span class="label label-success">Yes <i class="fa fa-check-circle"></i></span>' : '<span class="label label-danger">No <i class="fa fa-remove"></i></span>'; ?></td>
                                <td align="left">Rate per Unit</td>
                                <td align="left"><?php echo $rpu; ?></td>
                            </tr>
                           <?php if ($interp == 'Yes'): ?>
                                <tr>
                                    <td align="left">Travel Expenses Rate</td>
                                    <td><?php echo $ratetravelexpmile; ?></td>
                                    <td align="left">Travel Time Rate</td>
                                    <td><?php echo $ratetravelworkmile; ?></td>
                                </tr>
                            <?php endif; ?>
                            <?php if ($_SESSION['prv'] == 'Management' || $_SESSION['prv'] == 'Finance') { ?>
                                <tr class="bg-primary text-center">
                                    <td colspan="4"><b>Bank Details</b></td>
                                </tr>
                                <tr>
                                    <td align="left">Account Name</td>
                                    <td align="left"><?php echo $acName ?: '- - -'; ?></td>
                                    <td align="left">Sort Code</td>
                                    <td align="left">
                                        <?php if (preg_match('/^\d{6}$/', $acntCode)) {
                                            echo preg_replace('/(\d{2})(\d{2})(\d{2})/', '$1-$2-$3', $acntCode) . " <i title='Correct sort code!' class='fa fa-check-circle text-success'></i>";
                                        } else {
                                            echo "<span class='text-danger'>" . $acntCode . " <i title='Invalid sort code!' class='fa fa-exclamation-circle text-danger'></i></span>";
                                        }?>
                                    </td>
                                </tr>
                                <tr>
                                    <td align="left">Bank Name</td>
                                    <td align="left"><?php echo $bnakName ?: '- - -'; ?></td>
                                    <td align="left">Account Number</td>
                                    <td align="left">
                                        <?php if (preg_match('/^\d{8}$/', $formattedAccountNumber = preg_replace('/\D/', '', $acNo))) {
                                            echo $formattedAccountNumber . " <i title='Correct account number!' class='fa fa-check-circle text-success'></i>";
                                        } else {
                                            echo "<span class='text-danger'>" . $acNo . " <i title='Invalid account number!' class='fa fa-exclamation-circle text-danger'></i></span>";
                                        }?>
                                    </td>
                                </tr>
                            <?php } ?>
                            <style>
                                .badge {
                                    font-size: 13px;
                                    color: #050505;
                                    margin-top: 3px;
                                    padding: 7px;
                                    background-color: #e5e2e2;

                                    border-radius: 0;
                                    text-align:left;
                                }
                                .no-display{
                                    display:none;
                                }
                            </style>
                            <tr class="bg-primary">
                                <td colspan="2" width="50%">Interpreting Languages
                                    <?php if ($_SESSION['prv'] == 'Management' || $_SESSION['prv'] == 'Operator') { ?>
                                        <select class="form-control" id="language_selector" onchange="upd_langs(this)">
                                            <option disabled selected value="">Add New Language</option>
                                            <?php //$langs_q=$acttObj->read_all('DISTINCT lang','lang',"lang NOT IN (SELECT lang from interp_lang WHERE code='$interp_code') ORDER BY lang ASC");
                                            $langs_q = $acttObj->read_all('DISTINCT lang', 'lang', " 1=1  ORDER BY lang ASC");

                                            while ($row_langs = mysqli_fetch_assoc($langs_q)) { ?>
                                                <option value="<?php echo $row_langs['lang']; ?>"><?php echo $row_langs['lang']; ?></option>
                                            <?php } ?>
                                        </select>
                                        <script>
                                            function update_action_interpreter(option, id, type) {
                                                $.ajax({
                                                    url: 'ajax_add_interp_data.php',
                                                    method: 'post',
                                                    data: {
                                                        option: option,
                                                        id: id,
                                                        update_action_interpreter: type
                                                    },
                                                    success: function(data) {
                                                        if (type == 'specific_agreed') {
                                                            if (data == '1') {
                                                                alert('LSUK is specifically agreed for travel cost.');
                                                            } else {
                                                                alert('Travel Cost has been set to default (Not Agreed).');
                                                            }
                                                        } else {
                                                            if (data == '1') {
                                                                alert('Reminders for availability will be send onwards.');
                                                            } else {
                                                                alert('Reminders for availability are disabled!');
                                                            }
                                                        }

                                                        location.reload();
                                                    },
                                                    error: function(xhr) {
                                                        alert("An error occured: " + xhr.status + " " + xhr.statusText);
                                                    }
                                                });
                                            }

                                            function upd_langs(elem) {
                                                $('#view_modal_data').find('#selected_language').val(elem.value);
                                                $('#view_modal').modal("show");
                                            }

                                            function add_language() {
                                                var code_selected = '<?php echo $interp_code; ?>';
                                                var lang_selected = $('#language_selector').val();
                                                var level_selected = $('#selected_level').val();
                                                if ($('#interpreting_type').val() == undefined) {
                                                    alert('Please select Interpreting Type');
                                                    return;
                                                }
                                                var arr = new Array();
                                                $('#interpreting_type:checked').each(function(e) {
                                                    arr.push($(this).val());
                                                });
                                                var interpreting_type = arr;
                                                $.ajax({
                                                    url: 'ajax_add_interp_data.php',
                                                    method: 'post',
                                                    data: {
                                                        code: code_selected,
                                                        lang: lang_selected,
                                                        level: level_selected,
                                                        interpreting_type: interpreting_type
                                                    },
                                                    success: function(data) {
                                                        $('.lang_data').html(data);
                                                        location.reload();
                                                        // $("#language_selector").prop("selectedIndex", 0).change();
                                                        // $('#selected_level').prop("selectedIndex", 0).change();
                                                        // cancel_language();
                                                    },
                                                    error: function(xhr) {
                                                        alert("An error occured: " + xhr.status + " " + xhr.statusText);
                                                    }
                                                });
                                            }

                                            function cancel_language() {
                                                $("#language_selector").prop("selectedIndex", 0).change();
                                                $('#selected_level').prop("selectedIndex", 0).change();
                                                $('#view_modal').modal("hide");
                                            }

                                            function remove_lang(elem) {
                                                var remove_lang_id = elem.id;
                                                $.ajax({
                                                    url: 'ajax_add_interp_data.php',
                                                    method: 'post',
                                                    data: {
                                                        remove_lang_id: remove_lang_id
                                                    },
                                                    success: function(data) {
                                                        elem.remove();
                                                        $('.lang_data').html(data);
                                                        
                                                    },
                                                    error: function(xhr) {
                                                        alert("An error occured: " + xhr.status + " " + xhr.statusText);
                                                    }
                                                });
                                            }
                                        </script>
                                    <?php } ?>
                                </td>
                                <td colspan="2" width="50%">Interpreting Skills
                                    <?php if ($_SESSION['prv'] == 'Management' || $_SESSION['prv'] == 'Operator') { ?>
                                        <select class="form-control" id="<?php echo $interp_code; ?>" onchange="upd_skills(this)">
                                            <option disabled selected value="">Add New Skill</option>
                                            <?php $skills_q = $acttObj->read_all('DISTINCT skill', 'skill', "skill NOT IN (SELECT skill from interp_skill WHERE code='$interp_code') ORDER BY skill ASC");
                                            while ($row_skills = mysqli_fetch_assoc($skills_q)) { ?>
                                                <option value="<?php echo $row_skills['skill']; ?>"><?php echo $row_skills['skill']; ?></option>
                                            <?php } ?>
                                        </select>
                                        <script>
                                            function upd_skills(elem) {
                                                var code = elem.id;
                                                var skill = elem.value;
                                                $.ajax({
                                                    url: 'ajax_add_interp_data.php',
                                                    method: 'post',
                                                    data: {
                                                        code: code,
                                                        skill: skill
                                                    },
                                                    success: function(data) {
                                                        $('#append_skills').html(data);
                                                    },
                                                    error: function(xhr) {
                                                        alert("An error occured: " + xhr.status + " " + xhr.statusText);
                                                    }
                                                });
                                            }

                                            function remove_skill(elem) {
                                                var remove_skill_id = elem.id;
                                                $.ajax({
                                                    url: 'ajax_add_interp_data.php',
                                                    method: 'post',
                                                    data: {
                                                        remove_skill_id: remove_skill_id
                                                    },
                                                    success: function(data) {
                                                        $('#append_skills').html(data);
                                                    },
                                                    error: function(xhr) {
                                                        alert("An error occured: " + xhr.status + " " + xhr.statusText);
                                                    }
                                                });
                                            }
                                        </script>
                                    <?php } ?>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2" align="left" id="append_langs">
                                    <?php
                                    $query_lang = "SELECT id,lang,`type`,level FROM interp_lang where code='$interp_code'  ORDER BY lang ASC";
                                    $result_lang = mysqli_query($con, $query_lang);
                                    if (mysqli_num_rows($result_lang) == 0) { ?>
                                        <span class="badge badge-primary">No Languages Currently!</span>
                                    <?php } else {
                                        // $level_1=array();$level_2=array();
                                        $levelz = array("1" => "Native", "2" => "Fluent", "3" => "Intermediate", "4" => "Basic");
                                        //     while($row_lang = mysqli_fetch_assoc($result_lang)){
                                        //         if($row_lang['level']<3){
                                        //             array_push($level_1,$row_lang);
                                        //         }else{
                                        //             array_push($level_2,$row_lang);
                                        //         }


                                        //     }
function groupLanguages($type, $interp_code, $acttObj, $levelz) {
    $sql = "SELECT id, lang, level FROM interp_lang WHERE code='$interp_code' AND type='$type' ORDER BY id ASC";
    $result = mysqli_query($GLOBALS['con'], $sql);
    $langs = mysqli_fetch_all($result, MYSQLI_ASSOC);

    $grouped = [];
    foreach ($langs as $row) {
        $lang = $row['lang'];
        $level = $levelz[$row['level']] ?? '';
        $id = $row['id'];
        if (!$lang) continue;

        $parts = explode('-', $lang, 2);
        $main = $parts[0];
        $dialect = $parts[1] ?? null;

        $entryText = $dialect ? "$dialect ($level)" : "$main ($level)";
        if ($_SESSION['prv'] == 'Management' || $_SESSION['prv'] == 'Operator') {
            $entryHtml = "<span class='rm badge badge-light ml-1' 
                            title='Double Click to remove this language!'
                            id='$id|$interp_code' 
                            ondblclick=\"if(confirm('Are you sure to remove this language?')){ remove_lang(this); }\">
                            $lang
                        </span>";
        } else {
            $entryHtml = "<span class='badge badge-light ml-1'>$lang</span>";
        }
        $grouped[$main][] = $entryHtml;
    }
    return $grouped;
}


$interpreting = groupLanguages('interp', $interp_code, $acttObj, $levelz);
$telephone = groupLanguages('telep', $interp_code, $acttObj, $levelz);
$translation = groupLanguages('trans', $interp_code, $acttObj, $levelz);

function renderLangGroups($groups, $type) {
    $html = '';
    $counter = 0;
    foreach ($groups as $main => $dialects) {
        $class = $counter < 1 ? 'visible-group' : 'hidden-group no-display';

        $html .= "<div class='$class'>";
        //$html .= "<strong style='width: 100%; display: inline-block; background: #f7f7f7; padding: 2px; margin-top: 5px;'>$main</strong><br>";
        $html .= "<span class='badge-group mx-1'>" . implode(' ', $dialects) . "</span><br>";
        $html .= "</div>";

        $counter++;
    }

    if ($counter > 1) {
        $html .= "<br><a href='javascript:void(0)' class='text-info toggle-link' data-type='$type' onclick=\"toggleGroups(this)\">View more</a>";

    }

    return $html;
}
?>

<table class="table table-sm">
    <thead>
        <tr>
            <th>Interpreting</th>
            <th>Telephone</th>
            <th>Translation</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td id="interp_col"><?= renderLangGroups($interpreting, 'interp') ?></td>
            <td id="telep_col"><?= renderLangGroups($telephone, 'telep') ?></td>
            <td id="trans_col"><?= renderLangGroups($translation, 'trans') ?></td>
        </tr>
    </tbody>
</table>

<script>
function toggleGroups(link) {
    const type = link.getAttribute('data-type');
    const groups = document.querySelectorAll(`#${type}_col .hidden-group`);
    const isHidden = Array.from(groups).some(el => el.classList.contains('no-display'));

    groups.forEach(el => el.classList.toggle('no-display'));

    link.textContent = isHidden ? 'View less' : 'View more';
}
</script>
                                        <?php
                                        //if(!empty($level_1)){
                                        // foreach($level_1 as $key){ 
                                        //     $type = "";

                                        //     switch($key['type']){
                                        //         case 'interp' :
                                        //             $type = '<small>(Interpreting)</small>';
                                        //             break;
                                        //         case 'telep' :
                                        //             $type = '<small>(Telephone)</small>';
                                        //             break;
                                        //         case 'trans' :
                                        //             $type = '<small>(Translation)</small>';
                                        //             break;
                                        //     }

                                        ?>
                                        <!-- <span class="badge badge-primary rm" <?php //if($_SESSION['prv']=='Management' || $_SESSION['prv']=='Operator'){ 
                                                                                    ?> title="Double Click to remove this language!" id="<?php //echo $key['id'].'|'.$interp_code; 
                                                                                                                                            ?>" ondblclick="if(confirm('Are you sure to remove this language?')){remove_lang(this);}" <?php // } 
                                                                                                                                                                                                                                        ?>><?php //echo $key['lang'].' | '.$levelz[$key['level']]." | ".$type; 
                                                                                                                                                                                                                                                                                                                    ?></span> -->
                                        <?php //}
                                        //} 
                                        ?>
                                        <hr>
                                        <?php //if(!empty($level_2)){
                                        // foreach($level_2 as $key){ 
                                        //     $type = "";

                                        //     switch($key['type']){
                                        //         case 'interp' :
                                        //             $type = '<small>(Interpreting)</small>';
                                        //             break;
                                        //         case 'telep' :
                                        //             $type = '<small>(Telephone)</small>';
                                        //             break;
                                        //         case 'trans' :
                                        //             $type = '<small>(Translation)</small>';
                                        //             break;
                                        //     }
                                        ?>
                                        <!-- <span class="badge badge-primary rm" <?php //if($_SESSION['prv']=='Management' || $_SESSION['prv']=='Operator'){ 
                                                                                    ?> title="Double Click to remove this language!" id="<?php //echo $key['id'].'|'.$interp_code; 
                                                                                                                                            ?>" ondblclick="if(confirm('Are you sure to remove this language?')){remove_lang(this);}" <?php //} 
                                                                                                                                                                                                                                        ?>><?php //echo $key['lang'].' | '.$levelz[$key['level']]." | ".$type; 
                                                                                                                                                                                                                                                                                                                    ?></span> -->
                                        <?php //}
                                        //} 
                                        ?>
                                    <?php } ?>
                                </td>
                                <td colspan="2" align="left" id="append_skills">
                                    <?php $query_exp = "SELECT id,skill FROM interp_skill where code='$interp_code' ORDER BY skill ASC";
                                    $result_exp = mysqli_query($con, $query_exp);
                                    if (mysqli_num_rows($result_exp) == 0) {
                                        echo '<span class="badge badge-primary">No Skills Currently!</span>';
                                    } else {
                                        while ($row_exp = mysqli_fetch_assoc($result_exp)) { ?>
                                            <span class="badge badge-primary rm" <?php if ($_SESSION['prv'] == 'Management' || $_SESSION['prv'] == 'Operator') { ?> title="Double Click to remove this Skill!" id="<?php echo $row_exp['id'] . '|' . $interp_code; ?>" ondblclick="if(confirm('Are you sure to remove this skill?')){remove_skill(this);}" <?php } ?>><?php echo $row_exp['skill']; ?></span>&nbsp; &nbsp;
                                    <?php }
                                    } ?>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2" valign="top" class="bg-primary">
                                    <p><strong>Jobs Completed</strong></p>
                                </td>
                                <td colspan="2" valign="top" class="bg-primary">
                                    <p><strong>Jobs Durations</strong></p>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2" valign="top">
                                    <p><?php $query_interp = "select count(interpreter.id) as jobs,round(IFNULL(sum(interpreter.hoursWorkd),0),2) as hours from interpreter WHERE interpreter.intrpName ='$view_id' and interpreter.deleted_flag=0 and interpreter.order_cancel_flag=0 AND orderCancelatoin = 0
                                    UNION ALL select count(telephone.id) as jobs,round(IFNULL(sum(telephone.hoursWorkd),0),2) as hours from telephone WHERE telephone.intrpName ='$view_id' and telephone.deleted_flag=0 and telephone.order_cancel_flag=0 AND orderCancelatoin = 0
                                    UNION ALL select count(translation.id) as jobs,round(IFNULL(sum(translation.numberUnit),0),2) as hours from translation WHERE translation.intrpName ='$view_id' and translation.deleted_flag=0 and translation.order_cancel_flag=0 AND orderCancelatoin = 0";
                                        $result_interp = mysqli_query($con, $query_interp);
                                        $array = array();
                                        while ($row_interp = mysqli_fetch_assoc($result_interp)) {
                                            array_push($array, $row_interp);
                                        } ?>
                                    <ul class="list-group">
                                        <li class="list-group-item">
                                            <a href="javascript:void(0)" class="" data-toggle="modal" data-target="#showWorkedHoursModal" data-title="face_to_face_jobs" data-interp-id="<?php echo $view_id; ?>" title="View Face to Face jobs">
                                                Face to Face<span class="hidden-xs"> jobs </span>
                                            </a>
                                            <span class="badge badge-default pull-right"><?php echo $array[0]['jobs']; ?></span>
                                        </li>
                                        <li class="list-group-item">
                                            <a href="javascript:void(0)" class="" data-toggle="modal" data-target="#showWorkedHoursModal" data-title="telephone_jobs" data-interp-id="<?php echo $view_id; ?>" title="View Telephone jobs">
                                                Telephone<span class="hidden-xs"> jobs </span>
                                            </a>
                                            <span class="badge badge-default pull-right"><?php echo $array[1]['jobs']; ?></span>
                                        </li>
                                        <li class="list-group-item">
                                            <a href="javascript:void(0)" class="" data-toggle="modal" data-target="#showWorkedHoursModal" data-title="translation_jobs" data-interp-id="<?php echo $view_id; ?>" title="View Translation jobs">
                                                Translation <span class="hidden-xs">jobs</span>
                                            </a>
                                            <span class="badge badge-default pull-right"><?php echo $array[2]['jobs']; ?></span>
                                        </li>
                                    </ul>
                                    </p>
                                </td>
                                <td colspan="2" valign="top">
                                    <p>
                                    <ul class="list-group">
                                        <li class="list-group-item">Face to Face Hours <span class="badge badge-default"><?php echo $array[0]['hours']; ?></span></li>
                                        <li class="list-group-item">Telephone Hours<span class="hidden-xs">/Minutes </span><span class="badge badge-default"><?php echo $array[1]['hours']; ?></span></li>
                                        <li class="list-group-item">Translation Units <span class="badge badge-default"><?php echo $array[2]['hours']; ?></span></li>
                                    </ul>
                                    </p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <br><br><br>
                </div>
                <?php if ($action_view_documents) { ?>
                    <div id="int_documents" class="tab-pane fade">
                        <br>
                        <form class="register col-md-12" action="javascript:void(0);">
                            <table width="50%" class="table table-bordered table-hover">
                                <?php $table = 'interpreter_reg';
                                $allowedExtns = array("gif", "jpeg", "jpg", "png", "pdf", "doc", "docx", "xlsx"); ?>
                                <?php if($profile_pic == ''): ?>
                                <tr>
                                    <td>Profile Picture <label class="label label-danger pull-right"><b>Profile Photo not Set</label></b></td>
                                    <td><span style="color:red;">Missing</span></td>
                                    <td></td>
                                </tr>
                                <?php endif; ?>
                                <?php if($work_evid_file  != 'NA'): ?>
                                <tr>
                                    <td align="left">Right to work evidence
                                        <?php if ($work_evid_file != '') {
                                            if ($action_edit_documents) { ?>
                                                <a href="javascript:void(0)" onClick="MM_openBrWindow('update_expiry_date_updater.php?edit_id=<?php echo $row['id']; ?>&col=<?php echo 'work_evid'; ?>&text=<?php echo 'Right to work evidence'; ?>','_blank','scrollbars=yes,resizable=yes,width=700,height=500,left=500,top=255')" class="btn btn-success btn-xs pull-right" style="color:#fdfdfd;"><b>Update <?php echo 'work evidence document'; ?> <i class="fa fa-refresh"></i></b></a>
                                            <?php }
                                        } else { ?>
                                            <label class="label label-danger pull-right"><b>Work evidence is not uploaded</label></b>
                                        <?php } ?>
                                    </td>
                                    <td align="left"><?php echo $work_evid_file ? 'Uploaded' : 'Not Provided'; ?></td>
                                    <td align="left">
                                        <?php if ($work_evid_file != '') { ?>
                                            <label>
                                                <a href="javascript:void(0)" onClick="MM_openBrWindow('doc_view.php?v_id=<?php echo $row['id']; ?>&col=<?php echo 'work_evid'; ?>&text=<?php echo 'Right to work evidence'; ?>','_blank','scrollbars=yes,resizable=yes,width=1200,height=900,left=200,top=10')">
                                                    <span class="btn btn-success btn-xs" style="color:#fdfdfd;" title="View <?php echo 'Right to work evidence document'; ?>"><b>View File <i class="fa fa-eye"></i></b></a>
                                            </label>
                                            <?php } else {
                                            if ($action_edit_documents) { ?>
                                                <a href="javascript:void(0)" onClick="MM_openBrWindow('update_expiry_date_updater.php?edit_id=<?php echo $row['id']; ?>&col=<?php echo 'work_evid'; ?>&text=<?php echo 'Right to work evidence'; ?>','_blank','scrollbars=yes,resizable=yes,width=700,height=500,left=500,top=255')" class="btn btn-danger btn-xs" style="color:#fdfdfd;"><b>Upload File <i class="fa fa-upload"></i></b></a>
                                        <?php }
                                        } ?>
                                    </td>
                                </tr>
                                <?php endif; ?>
                                <tr>
                                    <td align="left"> Application Form
                                        <?php if ($application_file != '') {
                                            if ($action_edit_documents) { ?>
                                                <a href="javascript:void(0)" onClick="MM_openBrWindow('update_expiry_date_updater.php?edit_id=<?php echo $row['id']; ?>&col=<?php echo 'applicationForm'; ?>&text=<?php echo 'Application Form'; ?>','_blank','scrollbars=yes,resizable=yes,width=700,height=500,left=500,top=255')" class="btn btn-success btn-xs pull-right" style="color:#fdfdfd;"><b>Update <?php echo 'Application Form'; ?> <i class="fa fa-refresh"></i></b></a>
                                            <?php }
                                        } else {
                                            if ($row['sbmtd_by'] == 'Online') { ?>
                                                <label class="label label-success pull-right"><b>Application Form uploaded online</label></b>
                                            <?php } else { ?>
                                                <label class="label label-danger pull-right"><b>Application Form not uploaded!</label></b>
                                            <?php } ?>
                                        <?php } ?>
                                    </td>
                                    <td align="left"><?php echo $row['applicationForm'] ?: 'Not Provided'; ?></td>
                                    <td align="left" width="20%">
                                        <?php if ($application_file != '') { ?>
                                            <label>
                                                <a href="javascript:void(0)" onClick="MM_openBrWindow('doc_view.php?v_id=<?php echo $row['id']; ?>&col=<?php echo 'applicationForm'; ?>&text=<?php echo 'Application Form'; ?>','_blank','scrollbars=yes,resizable=yes,width=1200,height=900,left=200,top=10')">
                                                    <span class="btn btn-success btn-xs" style="color:#fdfdfd;" title="View <?php echo 'Application Form'; ?>"><b>View File <i class="fa fa-eye"></i></b></a>
                                            </label>
                                            <?php } else {
                                            if ($row['sbmtd_by'] == 'Online') { ?>
                                                <a href="javascript:void(0)" onClick="popupwindow('reports_lsuk/pdf/view_application_form.php?view_id=<?php echo $row['id']; ?>', 'View online application form', 1100, 900);" class="btn btn-success btn-xs" style="color:#fdfdfd;"><b>View Application Form</label></b></a>
                                                <?php } else {
                                                if ($action_edit_documents) { ?>
                                                    <a href="javascript:void(0)" onClick="MM_openBrWindow('update_expiry_date_updater.php?edit_id=<?php echo $row['id']; ?>&col=<?php echo 'applicationForm'; ?>&text=<?php echo 'Application Form'; ?>','_blank','scrollbars=yes,resizable=yes,width=700,height=500,left=500,top=255')" class="btn btn-danger btn-xs" style="color:#fdfdfd;"><b>Upload File <i class="fa fa-upload"></i></b></a>
                                            <?php }
                                            } ?>
                                        <?php } ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td align="left">Agreement
                                        <?php if ($agreement_file != '') {
                                            if ($action_edit_documents) { ?>
                                                <a href="javascript:void(0)" onClick="MM_openBrWindow('update_expiry_date_updater.php?edit_id=<?php echo $row['id']; ?>&col=<?php echo 'agreement'; ?>&text=<?php echo 'Agreement Form'; ?>','_blank','scrollbars=yes,resizable=yes,width=700,height=500,left=500,top=255')" class="btn btn-success btn-xs pull-right" style="color:#fdfdfd;"><b>Update <?php echo 'Agreement Form'; ?> <i class="fa fa-refresh"></i></b></a>
                                            <?php }
                                        } else {
                                            if ($row['sbmtd_by'] == 'Online') { ?>
                                                <label class="label label-success pull-right"><b>Agreement uploaded online</label></b>
                                            <?php } else { ?>
                                                <label class="label label-danger pull-right"><b>Agreement is not uploaded!</label></b>
                                            <?php } ?>
                                        <?php } ?>
                                    </td>
                                    <td align="left"><?php echo $row['agreement'] ?: 'Not Provided'; ?></td>
                                    <td align="left">
                                        <?php if ($agreement_file != '') { ?>
                                            <label>
                                                <a href="javascript:void(0)" onClick="MM_openBrWindow('doc_view.php?v_id=<?php echo $row['id']; ?>&col=<?php echo 'agreement'; ?>&text=<?php echo 'Agreement Form'; ?>','_blank','scrollbars=yes,resizable=yes,width=1200,height=900,left=200,top=10')">
                                                    <span class="btn btn-success btn-xs" style="color:#fdfdfd;" title="View <?php echo 'Agreement Form'; ?>"><b>View File <i class="fa fa-eye"></i></b></a>
                                            </label>
                                            <?php } else {
                                            if ($row['sbmtd_by'] == 'Online') { ?>
                                                <a href="javascript:void(0)" onClick="popupwindow('view_agreement.php?view_id=<?php echo $row['id']; ?>', 'View online agreement form', 1100, 900);" class="btn btn-success btn-xs" style="color:#fdfdfd;"><b>View Agreement</label></b></a>
                                                <?php } else {
                                                if ($action_edit_documents) { ?>
                                                    <a href="javascript:void(0)" onClick="MM_openBrWindow('update_expiry_date_updater.php?edit_id=<?php echo $row['id']; ?>&col=<?php echo 'agreement'; ?>&text=<?php echo 'Agreement Form'; ?>','_blank','scrollbars=yes,resizable=yes,width=700,height=500,left=500,top=255')" class="btn btn-danger btn-xs" style="color:#fdfdfd;"><b>Upload File <i class="fa fa-upload"></i></b></a>
                                            <?php }
                                            } ?>
                                        <?php } ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td align="left">CRB/DBS
                                        <?php if ($dbs_file != '') { ?>
                                            <a href="javascript:void(0)" onClick="MM_openBrWindow('update_expiry_date_updater.php?edit_id=<?php echo $row['id']; ?>&col=<?php echo 'dbs'; ?>&text=<?php echo 'DBS Document'; ?>','_blank','scrollbars=yes,resizable=yes,width=700,height=500,left=500,top=255')" class="btn btn-success btn-xs pull-right" style="color:#fdfdfd;"><b>Update <?php echo 'DBS Document'; ?> <i class="fa fa-refresh"></i></b></a>
                                        <?php } elseif (empty($dbs_auto_number)) { ?>
                                            <label class="label <?php echo (!empty($qint) ? "label-primary" : "label-danger"); ?> pull-right"><b><?php echo (!empty($qint) ? "(NOT REQUIRED) " : "") . ' DBS Document'; ?> is not uploaded!</label></b>
                                        <?php } ?>
                                    </td>
                                    <td align="left">
                                        <?php
                                        if (empty($row['crbDbs']) && empty($dbs_auto_number))
                                            echo 'Not Provided';
                                        elseif (!empty($row['crbDbs']) && empty($dbs_auto_number)) {
                                            echo $row['crbDbs'];
                                        } else {
                                            echo 'Auto Renewal';
                                        }
                                        // echo $row['crbDbs'] && !empty($dbs_auto_number)?:'Not Provided'; 
                                        ?>
                                    </td>
                                    <td align="left">
                                        <?php if ($dbs_file != '') { ?>
                                            <label>
                                                <a href="javascript:void(0)" onClick="MM_openBrWindow('doc_view.php?v_id=<?php echo $row['id']; ?>&col=<?php echo 'dbs'; ?>&text=<?php echo 'DBS Document'; ?>','_blank','scrollbars=yes,resizable=yes,width=1200,height=900,left=200,top=10')">
                                                    <span class="btn btn-success btn-xs" style="color:#fdfdfd;" title="View <?php echo 'DBS Document'; ?>"><b>View File <i class="fa fa-eye"></i></b></a>
                                            </label>
                                        <?php } elseif (empty($dbs_auto_number)) { ?>
                                            <a href="javascript:void(0)" onClick="MM_openBrWindow('update_expiry_date_updater.php?edit_id=<?php echo $row['id']; ?>&col=<?php echo 'dbs'; ?>&text=<?php echo 'DBS Document'; ?>','_blank','scrollbars=yes,resizable=yes,width=700,height=500,left=500,top=255')" class="btn <?php echo (!empty($qint) ? "btn-primary" : "btn-danger"); ?> btn-xs" style="color:#fdfdfd;"><b>Upload File <i class="fa fa-upload"></i></b></a>
                                        <?php } else {
                                            echo $dbs_auto_number;
                                        } ?>
                                    </td>
                                </tr>
                                <?php if($identityDocument != 'NA'): ?>
                                <tr>
                                    <td align="left">Identity Document
                                        <?php if ($id_doc_file != '') { ?>
                                            <a href="javascript:void(0)" onClick="MM_openBrWindow('update_expiry_date_updater.php?edit_id=<?php echo $row['id']; ?>&col=<?php echo 'id_doc'; ?>&text=<?php echo 'Identity Document'; ?>','_blank','scrollbars=yes,resizable=yes,width=700,height=500,left=500,top=255')" class="btn btn-success btn-xs pull-right" style="color:#fdfdfd;"><b>Update <?php echo 'Identity Document'; ?> <i class="fa fa-refresh"></i></b></a>
                                        <?php } else { ?>
                                            <label class="label <?php echo (!empty($qint) ? "label-primary" : "label-danger"); ?> pull-right"><b><?php echo (!empty($qint) ? "(NOT REQUIRED) " : "") . ' Identity Document'; ?> is not uploaded!</b></label>
                                        <?php } ?>
                                    </td>
                                    <td align="left"><?php echo $row['identityDocument'] ?: 'Not Provided'; ?></td>
                                    <td align="left">
                                        <?php if ($id_doc_file != '') { ?>
                                            <label>
                                                <a href="javascript:void(0)" onClick="MM_openBrWindow('doc_view.php?v_id=<?php echo $view_id; ?>&col=<?php echo 'id_doc'; ?>&text=<?php echo 'Identity Document'; ?>','_blank','scrollbars=yes,resizable=yes,width=1200,height=900,left=200,top=10')">
                                                    <span class="btn btn-success btn-xs" style="color:#fdfdfd;" title="View <?php echo 'Identity Document'; ?>"><b>View File <i class="fa fa-eye"></i></b></a>
                                            </label>
                                        <?php } else { ?>
                                            <a href="javascript:void(0)" onClick="MM_openBrWindow('update_expiry_date_updater.php?edit_id=<?php echo $row['id']; ?>&col=<?php echo 'id_doc'; ?>&text=<?php echo 'Identity Document'; ?>','_blank','scrollbars=yes,resizable=yes,width=700,height=500,left=500,top=255')" class="btn <?php echo (!empty($qint) ? "btn-primary" : "btn-danger"); ?>  btn-xs" style="color:#fdfdfd;"><b>Upload File <i class="fa fa-upload"></i></b></a>
                                        <?php } ?>
                                    </td>
                                </tr>
                                <?php endif; ?>
                                <tr>
                                    <td align="left">National Insurance Number / UTR
                                        <?php if ($nin != '') { ?>
                                            <a href="javascript:void(0)" onClick="MM_openBrWindow('update_expiry_date_updater.php?edit_id=<?php echo $row['id']; ?>&col=<?php echo 'nin'; ?>&text=<?php echo 'National Insurance Number'; ?>','_blank','scrollbars=yes,resizable=yes,width=700,height=500,left=500,top=255')" class="btn btn-success btn-xs pull-right" style="color:#fdfdfd;"><b>Update <?php echo 'National Insurance No#'; ?> <i class="fa fa-refresh"></i></b></a>
                                        <?php } else { ?>
                                            <label class="label <?php echo (!empty($qint) ? "label-primary" : "label-danger"); ?> pull-right"><b><?php echo (!empty($qint) ? "(NOT REQUIRED) " : "") . ' National Insurance Number'; ?> is missing!</label></b>
                                        <?php } ?>
                                    </td>
                                    <td align="left"><?php echo $row['ni'] ? '<b>' . $row['ni'] . '</b>' : 'Not Provided'; ?></td>
                                    <td align="left">
                                        <?php if ($nin != '') { ?>
                                            <label>
                                                <a href="javascript:void(0)" onClick="MM_openBrWindow('doc_view.php?v_id=<?php echo $view_id; ?>&col=<?php echo 'nin'; ?>&text=<?php echo 'National Insurace'; ?>','_blank','scrollbars=yes,resizable=yes,width=1200,height=900,left=200,top=10')">
                                                    <span class="btn btn-success btn-xs" style="color:#fdfdfd;" title="View <?php echo 'National Insurace'; ?>"><b>View File <i class="fa fa-eye"></i></b></a>
                                            </label>
                                        <?php } else { ?>
                                            <a href="javascript:void(0)" onClick="MM_openBrWindow('update_expiry_date_updater.php?edit_id=<?php echo $row['id']; ?>&col=<?php echo 'nin'; ?>&text=<?php echo 'National Insurace'; ?>','_blank','scrollbars=yes,resizable=yes,width=700,height=500,left=500,top=255')" class="btn <?php echo (!empty($qint) ? "btn-primary" : "btn-danger"); ?> btn-xs" style="color:#fdfdfd;"><b>Upload NI No # <i class="fa fa-upload"></i></b></a>
                                        <?php } ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td align="left">Bank Details
                                        <?php if ($acNo != '') {
                                            if ($action_edit_documents) { ?>
                                                <a href="javascript:void(0)" onClick="MM_openBrWindow('update_expiry_date_updater.php?edit_id=<?php echo $row['id']; ?>&col=<?php echo 'acNo'; ?>&text=<?php echo 'Bank Details'; ?>','_blank','scrollbars=yes,resizable=yes,width=700,height=500,left=500,top=255')" class="btn btn-success btn-xs pull-right" style="color:#fdfdfd;"><b>Update <?php echo 'Bank Details'; ?> <i class="fa fa-refresh"></i></b></a>
                                            <?php }
                                        } else { ?>
                                            <label class="label label-danger pull-right"><b><?php echo 'Bank Details'; ?> are missing!</label></b>
                                        <?php } ?>
                                    </td>
                                    <td align="left"><?php echo $acNo ? 'Added' : 'Not Provided'; ?></td>
                                    <td align="left">
                                        <?php if ($acNo != '') {
                                            if ($action_edit_documents) { ?>
                                                <label>
                                                    <a href="javascript:void(0)" onClick="MM_openBrWindow('update_expiry_date_updater.php?edit_id=<?php echo $row['id']; ?>&col=<?php echo 'acNo'; ?>&text=<?php echo 'Bank Details'; ?>','_blank','scrollbars=yes,resizable=yes,width=700,height=500,left=500,top=255')">
                                                        <span class="btn btn-success btn-xs" style="color:#fdfdfd;" title="View <?php echo 'Bank Details'; ?>"><b>View Details</b></a>
                                                </label>
                                            <?php }
                                        } else { ?>
                                            <a href="javascript:void(0)" onClick="MM_openBrWindow('update_expiry_date_updater.php?edit_id=<?php echo $row['id']; ?>&col=<?php echo 'acNo'; ?>&text=<?php echo 'Bank Details'; ?>','_blank','scrollbars=yes,resizable=yes,width=700,height=500,left=500,top=255')" class="btn btn-danger btn-xs" style="color:#fdfdfd;"><b>Add Bank Details <i class="fa fa-refresh"></i></b></a>
                                        <?php } ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td align="left">DPSI
                                        <?php if ($dps != '') {
                                            if ($action_edit_documents) { ?>
                                                <a href="javascript:void(0)" onClick="MM_openBrWindow('update_expiry_date_updater.php?edit_id=<?php echo $row['id']; ?>&col=<?php echo 'dps'; ?>&text=<?php echo 'DPSI'; ?>','_blank','scrollbars=yes,resizable=yes,width=700,height=500,left=500,top=255')" class="btn btn-success btn-xs pull-right" style="color:#fdfdfd;"><b>Update <?php echo 'DPSI'; ?> <i class="fa fa-refresh"></i></b></a>
                                            <?php }
                                        } else { ?>
                                            <label class="label label-danger pull-right"><b><?php echo 'DPSI'; ?> is not uploaded!</label></b>
                                        <?php } ?>
                                    </td>
                                    <td align="left"><?php echo $dps ? 'Added' : 'Not Provided'; ?></td>
                                    <td align="left">
                                        <?php if ($dps != '') { ?>
                                            <label>
                                                <a href="javascript:void(0)" onClick="MM_openBrWindow('doc_view.php?v_id=<?php echo $view_id; ?>&col=<?php echo 'dps'; ?>&text=<?php echo 'DPSI'; ?>','_blank','scrollbars=yes,resizable=yes,width=1200,height=900,left=200,top=10')">
                                                    <span class="btn btn-success btn-xs" style="color:#fdfdfd;" title="View <?php echo 'DPSI'; ?>"><b>View File <i class="fa fa-eye"></i></b></a>
                                            </label>
                                            <?php } else {
                                            if ($action_edit_documents) { ?>
                                                <a href="javascript:void(0)" onClick="MM_openBrWindow('update_expiry_date_updater.php?edit_id=<?php echo $row['id']; ?>&col=<?php echo 'dps'; ?>&text=<?php echo 'DPSI'; ?>','_blank','scrollbars=yes,resizable=yes,width=700,height=500,left=500,top=255')" class="btn btn-danger btn-xs" style="color:#fdfdfd;"><b>Upload DPSI <i class="fa fa-upload"></i></b></a>
                                        <?php }
                                        } ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td align="left">CV</td>
                                    <td align="left"><?php echo $row['cv'] ? 'Added' : 'Not Provided'; ?></td>
                                    <td align="left"><?php if ($row['cv'] == 'Soft Copy' || $row['cv'] == 'Hard Copy') { ?>
                                            <a href="#" onClick="MM_openBrWindow('interp_doc_updater.php?edit_id=<?php echo $row['id']; ?>&table=<?php echo $table; ?>&text=<?php echo 'CV'; ?>&col=<?php echo 'cv'; ?>&data=<?php echo $cv; ?>','_blank','scrollbars=yes,resizable=yes,width=700,height=400,left=500,top=160')"><i class="fa fa-check-circle  text-success fa-custom"></i></a><?php } else { ?><a href="#" onClick="MM_openBrWindow('interp_doc_updater.php?edit_id=<?php echo $row['id']; ?>&table=<?php echo $table; ?>&text=<?php echo 'CV'; ?>&col=<?php echo 'cv'; ?>&data=<?php echo $cv; ?>','_blank','scrollbars=yes,resizable=yes,width=700,height=400,left=500,top=160')"><i class="fa fa-remove text-danger fa-custom"></i></a>
                                    </td><?php } ?>
                                </tr>
                                <tr>
                                    <td align="left">Any Certificate</td>
                                    <td align="left"><?php echo $row['anyCertificate'] ? 'Added' : 'Not Provided'; ?></td>
                                    <td align="left"><?php if ($row['anyCertificate'] == 'Soft Copy' || $row['anyCertificate'] == 'Hard Copy') { ?>
                                            <a href="#" onclick="MM_openBrWindow('interp_doc_updater.php?edit_id=<?php echo $row['id']; ?>&amp;table=<?php echo $table; ?>&amp;text=<?php echo 'Any Certificate'; ?>&amp;col=<?php echo 'anyCertificate'; ?>&amp;data=<?php echo $anyCertificate; ?>','_blank','scrollbars=yes,resizable=yes,width=700,height=400,left=500,top=160')"><i class="fa fa-check-circle  text-success fa-custom"></i></a>
                                        <?php } else { ?>
                                            <a href="#" onclick="MM_openBrWindow('interp_doc_updater.php?edit_id=<?php echo $row['id']; ?>&amp;table=<?php echo $table; ?>&amp;text=<?php echo 'Any Certificate'; ?>&amp;col=<?php echo 'anyCertificate'; ?>&amp;data=<?php echo $anyCertificate; ?>','_blank','scrollbars=yes,resizable=yes,width=700,height=400,left=500,top=160')"><i class="fa fa-remove text-danger fa-custom"></i></a>
                                    </td>
                                <?php } ?>
                                </tr>
                                <tr>
                                    <td align="left"> Other Document(s)
                                        <?php if ($anyOther != '') {
                                            if ($action_edit_documents) { ?>
                                                <a href="javascript:void(0)" onClick="MM_openBrWindow('update_expiry_date_updater.php?edit_id=<?php echo $row['id']; ?>&col=<?php echo 'anyOther'; ?>&text=<?php echo 'Other Document(s)'; ?>','_blank','scrollbars=yes,resizable=yes,width=700,height=500,left=500,top=255')" class="btn btn-success btn-xs pull-right" style="color:#fdfdfd;"><b>Update <?php echo 'Other Document(s)'; ?> <i class="fa fa-refresh"></i></b></a>
                                            <?php }
                                        } else { ?>
                                            <label class="label label-danger pull-right"><b><?php echo 'No other document(s)'; ?></label></b>
                                        <?php } ?>
                                    </td>
                                    <td align="left"><?php echo $anyOther ? 'Added' : 'Not Provided'; ?></td>
                                    <td align="left" width="20%">
                                        <?php if ($anyOther != '') { ?>
                                            <label>
                                                <a href="javascript:void(0)" onClick="MM_openBrWindow('doc_view.php?v_id=<?php echo $row['id']; ?>&col=<?php echo 'anyOther'; ?>&text=<?php echo 'Other Document(s)'; ?>','_blank','scrollbars=yes,resizable=yes,width=1200,height=900,left=200,top=10')">
                                                    <span class="btn btn-success btn-xs" style="color:#fdfdfd;" title="View <?php echo 'Other Document(s)'; ?>"><b>View File <i class="fa fa-eye"></i></b></a>
                                            </label>
                                            <?php } else {
                                            if ($action_edit_documents) { ?>
                                                <a href="javascript:void(0)" onClick="MM_openBrWindow('update_expiry_date_updater.php?edit_id=<?php echo $row['id']; ?>&col=<?php echo 'anyOther'; ?>&text=<?php echo 'Other Document(s)'; ?>','_blank','scrollbars=yes,resizable=yes,width=700,height=500,left=500,top=255')" class="btn btn-danger btn-xs" style="color:#fdfdfd;"><b>Upload File <i class="fa fa-upload"></i></b></a>
                                        <?php }
                                        } ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td align="left">CPD Document</td>
                                    <td align="left"><?php echo $row['cpd'] ? 'Added' : 'Not Provided'; ?></td>
                                    <td align="left"><?php if ($row['cpd'] == 'Soft Copy' || $row['cpd'] == 'Hard Copy') { ?>
                                            <a href="#" onClick="MM_openBrWindow('interp_doc_updater.php?edit_id=<?php echo $row['id']; ?>&table=<?php echo $table; ?>&text=<?php echo 'CPD Document'; ?>&col=<?php echo 'cpd'; ?>&data=<?php echo $cpd; ?>','_blank','scrollbars=yes,resizable=yes,width=700,height=400,left=500,top=160')"><i class="fa fa-check-circle  text-success fa-custom"></i></a><?php } else { ?><a href="#" onClick="MM_openBrWindow('interp_doc_updater.php?edit_id=<?php echo $row['id']; ?>&table=<?php echo $table; ?>&text=<?php echo 'CPD Document'; ?>&col=<?php echo 'cpd'; ?>&data=<?php echo $cpd; ?>','_blank','scrollbars=yes,resizable=yes,width=700,height=400,left=500,top=160')"><i class="fa fa-remove text-danger fa-custom"></i></a>
                                    </td><?php } ?>
                                </tr>
                                <tr>
                                    <td align="left">Interpreting Qualification Document
                                        <?php if ($row['int_qualification'] != '') {
                                            if ($action_edit_documents) { ?>
                                                <a href="javascript:void(0)" onClick="MM_openBrWindow('update_expiry_date_updater.php?edit_id=<?php echo $row['id']; ?>&col=<?php echo 'int_qualification'; ?>&text=<?php echo 'Interpreting Qualification'; ?>','_blank','scrollbars=yes,resizable=yes,width=700,height=500,left=500,top=255')" class="btn btn-success btn-xs pull-right" style="color:#fdfdfd;"><b>Update <?php echo 'Interpreting Qualification'; ?> <i class="fa fa-refresh"></i></b></a>
                                            <?php }
                                        } else { ?>
                                            <label class="label label-danger pull-right"><b><?php echo 'Interpreting Qualification'; ?> is not uploaded!</label></b>
                                        <?php } ?>
                                    </td>
                                    <td align="left"><?php echo $row['int_qualification'] ? 'Added' : 'Not Provided'; ?></td>
                                    <td align="left">
                                        <?php if ($row['int_qualification'] != '') { ?>
                                            <label>
                                                <a href="javascript:void(0)" onClick="MM_openBrWindow('doc_view.php?v_id=<?php echo $view_id; ?>&col=<?php echo 'int_qualification'; ?>&text=<?php echo 'Interpreting Qualification'; ?>','_blank','scrollbars=yes,resizable=yes,width=1200,height=900,left=200,top=10')">
                                                    <span class="btn btn-success btn-xs" style="color:#fdfdfd;" title="View <?php echo 'Interpreting Qualification'; ?>"><b>View File <i class="fa fa-eye"></i></b></a>
                                            </label>
                                            <?php } else {
                                            if ($action_edit_documents) { ?>
                                                <a href="javascript:void(0)" onClick="MM_openBrWindow('update_expiry_date_updater.php?edit_id=<?php echo $row['id']; ?>&col=<?php echo 'int_qualification'; ?>&text=<?php echo 'Interpreting Qualification'; ?>','_blank','scrollbars=yes,resizable=yes,width=700,height=500,left=500,top=255')" class="btn btn-danger btn-xs" style="color:#fdfdfd;"><b>Upload Interpreting Qualification <i class="fa fa-upload"></i></b></a>
                                        <?php }
                                        } ?>
                                    </td>
                                </tr>
                            </table>

                            </fieldset>
                        </form>
                    </div>
                <?php }
                if ($_SESSION['prv'] == 'Management') { ?>
                    <div id="feed_record" class="tab-pane fade">
                        <br>
                        <h3>Job <span class="hidden-xs">Profile </span>Rating : <span>
                                <?php
                                $row_st = $acttObj->read_specific(
                                    "( CASE WHEN (record<0) THEN '-1' WHEN ((record>=0 AND record<=5) OR record IS NULL) THEN '0' WHEN (record>5 AND record<=20) THEN '1' WHEN (record>20 AND record<=40) THEN '2' WHEN (record>40 AND record<=60) THEN '3' WHEN (record>60 AND record<=80) THEN '4' ELSE '5' END) as record from (SELECT (sum(punctuality)+sum(appearance)+sum(professionalism)+sum(confidentiality)+sum(impartiality)+sum(accuracy)+sum(rapport)+sum(communication))/COUNT(interp_assess.id) as record",
                                    "interp_assess,interpreter_reg",
                                    "interp_assess.interpName=interpreter_reg.code AND interp_assess.interpName='$interp_code') as record"
                                );
                                if ($row_st['record'] == -1) {
                                    echo 'Negative Feedback';
                                } elseif ($row_st['record'] == 0) {
                                    echo 'No Feedback Received';
                                } elseif ($row_st['record'] == 1) {
                                    echo '<i class="fa fa-star text-danger"></i> ';
                                } elseif ($row_st['record'] == 2) {
                                    echo '<i class="fa fa-star text-warning"></i> <i class="fa fa-star text-warning"></i> ';
                                } elseif ($row_st['record'] == 3) {
                                    echo '<i class="fa fa-star text-info"></i> <i class="fa fa-star text-info"></i> <i class="fa fa-star text-info"></i> ';
                                } elseif ($row_st['record'] == 4) {
                                    echo '<i class="fa fa-star text-success"></i> <i class="fa fa-star text-success"></i> <i class="fa fa-star text-success"></i> <i class="fa fa-star text-success"></i> ';
                                } else {
                                    echo '<i class="fa fa-star text-success"></i> <i class="fa fa-star text-success"></i> <i class="fa fa-star text-success"></i> <i class="fa fa-star text-success"></i> <i class="fa fa-star text-success"></i> ';
                                }
                                ?></span></h3>
                        <?php $query = "SELECT * from interp_assess where interp_assess.interpName='$interp_code'";
                        $result = mysqli_query($con, $query);
                        if (mysqli_num_rows($result) == 0) {
                            echo "<center><h2><span class='label label-info'> NO FEEDBACKS GIVEN YET !</span></h2></center>";
                        } else { ?>
                            <table class="table table-bordered table-hover">
                                <thead class="bg-primary">
                                    <tr>
                                        <th>Organization</th>
                                        <th>Feedback By</th>
                                        <th>Positive Remarks</th>
                                        <th>Negative Remarks</th>
                                        <th>Submitted By</th>
                                        <th>Dated</th>
                                        <?php if ($_SESSION['prv'] == 'Management') { ?><th width="230" align="center">Actions</th> <?php } ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                                        <tr title="Feedback for Invoice No: <?php echo $row['order_id'] == 0 ? 'Nil' : $row['order_id']; ?>">
                                            <td><?php echo $row['orgName']; ?></td>
                                            <td><?php echo $row['p_feedbackby']; ?></td>
                                            <td><?php echo $row['p_reason']; ?></td>
                                            <td><?php echo $row['n_reason']; ?></td>
                                            <td><?php echo $row['submittedBy']; ?></td>
                                            <td><?php echo $row['dated']; ?></td>
                                            <td align="center">
                                                <?php if ($_SESSION['prv'] == 'Management') { ?>
                                                    <a href="javascript:void(0)" onClick="MM_openBrWindow('interp_assessment_edit.php?edit_id=<?php echo $row['id']; ?>&code_qs=<?php echo $row['interpName']; ?>&name=<?php echo $name; ?>','_blank','scrollbars=yes,resizable=yes,width=1000,height=750,left=350,top=140')"><input type="image" src="images/icn_edit.png" title="Edit"></a>
                                                    <a href="javascript:void(0)" onClick="MM_openBrWindow('del.php?del_id=<?php echo $row['id']; ?>&table=<?php echo $table; ?>','_blank','scrollbars=yes,resizable=yes,width=600,height=300,left=540,top=283')"><input type="image" src="images/icn_trash.png" title="Trash"></a>
                                                <?php } ?>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        <?php } ?>
                    </div>
                <?php }
                if ($action_addnotes) { ?>
                    <div id="tab_interpreter_notes" class="tab-pane fade <?= isset($_GET['show_notes']) ? 'active in' : '' ?>">
                        <br>
                        <?php if ($_SESSION['returned_message']) {
                                echo $_SESSION['returned_message'];
                                unset($_SESSION['returned_message']);
                            } ?>
                        <div class="panel-group">
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h4 class="panel-title">
                                        <button class="btn btn-primary" data-toggle="collapse" href="#collapse_notes"><b>Click to add new interpreter notes</b></button>
                                    </h4>
                                </div>
                                <div id="collapse_notes" class="panel-collapse collapse">
                                    <div class="panel-body">
                                        <form action="process/update_interpreter_profile.php" method="post">
                                            <input type="hidden" name="interpreter_id" value='<?= $view_id ?>' />
                                            <input type="hidden" name="redirect_url" value='<?= 'http' . (isset($_SERVER['HTTPS']) ? 's' : '') . '://' . "{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}" ?>' />
                                            <table class="table table_todo">
                                                <tbody>
                                                    <tr class="new_row">
                                                        <td>
                                                            <div class="input-group col-md-12">
                                                                <span class="serial-number text-muted" style="position: absolute;margin: 6px -16px;font-size: 15px;">1</span><input type="text" class="form-control" name="details[]" placeholder="Write notes details here ..." required>
                                                                <div class="input-group-btn">
                                                                    <button class="btn btn-danger removeRowBtn" type="button">Remove</button>
                                                                </div>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <tr class="new_row">
                                                        <td>
                                                            <div class="input-group col-md-12">
                                                                <span class="serial-number text-muted" style="position: absolute;margin: 6px -16px;font-size: 15px;">2</span><input type="text" class="form-control" name="details[]" placeholder="Write notes details here ..." required>
                                                                <div class="input-group-btn">
                                                                    <button class="btn btn-danger removeRowBtn" type="button">Remove</button>
                                                                </div>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                            <div class="form-group" style="margin-top:-15px;">
                                                <button type="button" id="addRowBtn" class="btn btn-info btn-sm" title="Click to Add New Notes"><i class="fa fa-plus"></i> Add More Notes</button>
                                                <br><br>
                                                <button type="submit" name="btn_add_notes" id="btn_add_notes" class="btn btn-success btn-lg" title="Click to save all notes"><i class="fa fa-save"></i> Save All Notes</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <h3>Added Notes For Interpreter</h3>
                        <table class="table table-bordered">
                            <thead class="bg-info">
                                <tr>
                                    <td align="center" width="2%"><b>S.No#</b></td>
                                    <td width="60%"><b>Notes Details</b></td>
                                    <td align="center"><b>Added By</b></td>
                                    <td align="center" width="10%"><b>Added Date</b></td>
                                    <td align="center" width="5%"><b>Action</b></td>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $get_notes = $acttObj->read_all("*", "interpreter_notes", "interpreter_notes.interpreter_id=" . $view_id);
                                if ($get_notes->num_rows > 0) {
                                    $counter_notes = 1;
                                    while ($row_notes = $get_notes->fetch_assoc()) {
                                        if ($row_notes['created_by']) {
                                            $operator_name = $acttObj->read_specific("name", "login", "id=" . $row_notes['created_by'])['name'];
                                        } else {
                                            $operator_name = "Default Imported";
                                        } ?>
                                        <tr>
                                            <td align="center"><b><?= $counter_notes++; ?></b></td>
                                            <td><?= $row_notes['details']; ?></td>
                                            <td align="center"><?= ucwords($operator_name); ?> </td>
                                            <td><?php echo $misc->dated($row_notes['created_date']); ?> </td>
                                            <td align="center">
                                                <button onclick="delete_interpreter_note(this, <?= $row_notes['id'] ?>);" title="Delete this note" class="btn btn-sm btn-danger"><i class="fa fa-trash"></i></button>
                                            </td>
                                        </tr>
                                <?php }
                                } else {
                                    echo "<tr><td colspan='5' align='center'>There are no notes added for this interpreter yet! Thank you</td></tr>";
                                } ?>
                            </tbody>
                        </table>
                    </div>
                    <script>
                        function delete_interpreter_note(element, note_id) {
                            if (confirm('Are you sure to delete this note?')) {
                                $.ajax({
                                    url: 'process/update_interpreter_profile.php',
                                    method: 'post',
                                    dataType: 'json',
                                    data: {
                                        note_id: note_id,
                                        delete_interpreter_note: 1
                                    },
                                    success: function(data) {
                                        if (data['status'] == 1) {
                                            $(element).parents("td").parents("tr").remove();
                                        } else {
                                            alert(data['message']);
                                        }
                                    },
                                    error: function(data) {
                                        alert("Failed to perform this action! try again refreshing this page");
                                    }
                                });
                            }
                        }
                    </script>
                <?php } ?>
                <div id="tab_interpreter_lateness" class="tab-pane fade <?= isset($_GET['show_lateness']) ? 'active in' : '' ?>">
                    <h4>Interpreter Lateness Summary</h4>
                    <div class="row">
                        <div class="form-group col-sm-2">
                            <small>From Date</small>
                            <input type="date" name="l_date_from" id="l_date_from" class="form-control form-control-sm" value="<?php echo $_GET['l_date_from']; ?>" />
                        </div>
                        <div class="form-group col-sm-2">
                            <small>To Date</small>
                            <input type="date" name="l_date_to" id="l_date_to" class="form-control form-control-sm" value="<?php echo $_GET['l_date_to']; ?>" />
                        </div>
                        <div class="form-group col-sm-2">
                            <small>Filter Job Type</small>
                            <select class="form-control" name="l_job_type" id="l_job_type">
                            <option value="">-- Filter Job Type --</option>
                            <?php foreach ($array_order_types as $key_l => $job_type_l) {
                                $selected_l = $_GET['l_job_type'] == $key_l ? " selected" : "";
                                echo "<option " . $selected_l . " value='" . $key_l . "'>" . $job_type_l . "</option>";
                            } ?>
                            </select>
                        </div>
                        <div class="form-group col-sm-3">
                            <small>Filter By Late Minutes</small>
                            <input type="text" placeholder="Enter minutes like 5,10,15" name="l_minutes" id="l_minutes" class="form-control form-control-sm" value="<?php echo $_GET['l_minutes']; ?>" />
                        </div>
                        <div class="form-group col-md-2"><br>
                            <a href="javascript:void(0)" title="Click to Filter Lateness summary" onclick="filter_list('show_lateness')"><span class="btn btn-danger">Filter List</span></a>
                            <a href="full_view_interpreter.php?view_id=<?=$view_id?>&show_lateness=1" title="Click to reset filters"><span class="btn btn-warning">Clear</span></a>
                        </div>
                    </div>
                    <table class="table table-bordered">
                        <thead class="bg-danger">
                            <tr>
                                <td width="1%"><b>S.No</b></td>
                                <td><b>Job Type</b></td>
                                <td><b>Job ID</b></td>
                                <td><b>Late Minutes</b></td>
                                <td><b>Added By</b></td>
                                <td><b>Reason</b></td>
                                <td><b>Created Date</b></td>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            if ($_GET['l_date_from'] && $_GET['l_date_to']) {
                                $append_date_range_l = " AND DATE(job_late_minutes.created_date) BETWEEN '" . $_GET['l_date_from'] . "' AND '" . $_GET['l_date_to'] . "'";
                            }
                            if (isset($_GET['l_job_type']) && is_numeric($_GET['l_job_type'])) {
                                $append_job_type_l = " and job_late_minutes.job_type=" . $_GET['l_job_type'];
                            }
                            if (isset($_GET['l_minutes']) && is_numeric($_GET['l_minutes'])) {
                                $append_minutes_l = " and job_late_minutes.minutes=" . $_GET['l_minutes'];
                            }
                            $get_lateness = $acttObj->read_all("*", "job_late_minutes", "interpreter_id=" . $view_id . $append_date_range_l . $append_job_type_l . $append_minutes_l);
                            if ($get_lateness->num_rows > 0) {
                                $counter_lateness = 1;
                                $lateness_array = array(0 => "Client LSUK App", 1 => "Interpreter informed LSUK", 2 => "LSUK phoned interpreter");
                                while ($row_lateness = $get_lateness->fetch_assoc()) { ?>
                                    <tr>
                                        <td align="center"><b><?= $counter_lateness++; ?></b></td>
                                        <td><?= $array_order_types[$row_lateness['job_type']]; ?></td>
                                        <td><?= $row_lateness['job_id']; ?></td>
                                        <td><?= $row_lateness['minutes']; ?></td>
                                        <td><?= $lateness_array[$row_lateness['created_by']]; ?></td>
                                        <td><small><?= $row_lateness['reason'] ?: '---'; ?></small></td>
                                        <td><small><?= $row_lateness['created_date'] ? date("d-m-Y H:i:s", strtotime($row_lateness['created_date'])) : "---"; ?></small></td>
                                    </tr>
                            <?php }
                            } else {
                                echo "<tr><td colspan='7' align='center'>There are no records available in this list! Thank you</td></tr>";
                            } ?>
                        </tbody>
                    </table>
                </div>
                <div id="tab_interpreter_cancellation" class="tab-pane fade <?= isset($_GET['show_cancellation']) ? 'active in' : '' ?>">
                    <h4>Interpreter Cancellation Summary</h4>
                    <div class="row">
                        <div class="form-group col-sm-3">
                            <small>From Date</small>
                            <input type="date" name="date_from" id="date_from" class="form-control form-control-sm" value="<?php echo $_GET['date_from']; ?>" />
                        </div>
                        <div class="form-group col-sm-3">
                            <small>To Date</small>
                            <input type="date" name="date_to" id="date_to" class="form-control form-control-sm" value="<?php echo $_GET['date_to']; ?>" />
                        </div>
                        <div class="form-group col-sm-2">
                            <small>Filter Job Type</small>
                            <select class="form-control" name="job_type" id="job_type">
                            <option value="">-- Filter Job Type --</option>
                            <?php foreach ($array_order_types as $key_jb => $job_type) {
                                $selected = $_GET['job_type'] == $key_jb ? " selected" : "";
                                echo "<option " . $selected . " value='" . $key_jb . "'>" . $job_type . "</option>";
                            } ?>
                            </select>
                        </div>
                        <!-- <div class="form-group col-sm-2">
                            <small>Filter Cancalled By</small>
                            <select class="form-control" name="canceled_by" id="canceled_by">
                            <option <?=!isset($_GET['canceled_by']) ? 'selected' : ''?> value="">-- Filter Cancalled By --</option>
                            <option <?=$_GET['canceled_by'] == 1 ? 'selected' : ''?> value='1'>LSUK</option>
                            <option <?=$_GET['canceled_by'] == 2 ? 'selected' : ''?> value='2'>Client</option>
                            </select>
                        </div> -->
                        <div class="form-group col-sm-2">
                            <small>Search</small>
                            <input type="text" name="keywo" id="keywo" class="form-control form-control-sm" />
                        </div>
                        <div class="form-group col-md-2"><br>
                            <a href="javascript:void(0)" title="Click to Filter Cancelled jobs" onclick="filter_list('show_cancellation')"><span class="btn btn-danger">Filter List</span></a>
                            <a href="full_view_interpreter.php?view_id=<?=$view_id?>&show_cancellation=1" title="Click to reset filters"><span class="btn btn-warning">Clear</span></a>
                        </div>
                    </div>
                    <table class="table table-bordered">
                        <thead class="bg-danger">
                            <tr>
                                <td width="1%"><b>S.No</b></td>
                                <td width="8%"><b>Job Info</b></td>
                                <td><b>Job Reference</b></td>
                                <td><b>Source/Target</b></td>
                                <td><b>Cancelled By</b></td>
                                <td><b>Cancelled Date</b></td>
                                <td><b>Notice</b></td>
                                <td width="30%"><b>Reason</b></td>
                                <td><b>Created</b></td>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            if ($_GET['date_from'] && $_GET['date_to']) {
                                $append_date_range = " AND DATE(canceled_orders.canceled_date) BETWEEN '" . $_GET['date_from'] . "' AND '" . $_GET['date_to'] . "'";
                            }
                            if (isset($_GET['job_type']) && is_numeric($_GET['job_type'])) {
                                $append_job_type = " and canceled_orders.job_type=" . $_GET['job_type'];
                            }
                            if (isset($_GET['canceled_by']) && is_numeric($_GET['canceled_by'])) {
                                $append_canceled_by = " and canceled_orders.canceled_by=" . $_GET['canceled_by'];
                            }
                            if(isset($_GET['job_id'])){
                               
                                $append_job= " and canceled_orders.job_id=" . $_GET['job_id'];
                                
                            }
                            
                            if(isset($append_job_type) && !empty($append_job_type)){
                                $get_cancelled = $acttObj->read_all("*", "canceled_orders", "interpreter_id=" . $view_id . $append_date_range . $append_job_type . $append_canceled_by. $append_job . " AND canceled_by = 1");
                            }else{
                                $get_cancelled = $acttObj->read_custom_query($view_id, $append_date_range, $append_canceled_by, $append_job);
                            }
                            
                            if ($get_cancelled->num_rows > 0) {
                                $counter_cancelled = 1;
                                while ($row_cancelled = $get_cancelled->fetch_assoc()) { 
                                       // Fetch nameRef based on job_type
                                        $nameRef = "";
                                        switch ($row_cancelled['job_type']) {
                                            case 1:
                                                // Job Type 1 -> Interpreter
                                                $nameRefQuery = $acttObj->read_all("nameRef", "interpreter", "id = " . $row_cancelled['job_id']);
                                                if ($nameRefQuery->num_rows > 0) {
                                                    $nameRefData = $nameRefQuery->fetch_assoc();
                                                    $nameRef = $nameRefData['nameRef'];
                                                }
                                                break;
                                            case 2:
                                                // Job Type 2 -> Telephone
                                                $nameRefQuery = $acttObj->read_all("nameRef", "telephone", "id = " . $row_cancelled['job_id']);
                                                if ($nameRefQuery->num_rows > 0) {
                                                    $nameRefData = $nameRefQuery->fetch_assoc();
                                                    $nameRef = $nameRefData['nameRef'];
                                                }
                                                break;
                                            case 3:
                                                // Job Type 3 -> Translation
                                                $nameRefQuery = $acttObj->read_all("nameRef", "translation", "id = " . $row_cancelled['job_id']);
                                                if ($nameRefQuery->num_rows > 0) {
                                                    $nameRefData = $nameRefQuery->fetch_assoc();
                                                    $nameRef = $nameRefData['nameRef'];
                                                }
                                                break;
                                            default:
                                                // In case of an unknown job type, leave nameRef empty
                                                $nameRef = "Unknown";
                                        }
                                    ?>
                                    <tr>
                                        <td align="center"><b><?= $counter_cancelled++; ?></b></td>
                                        <td><?= $array_order_types[$row_cancelled['job_type']] . " ID#" . $row_cancelled['job_id']; ?></td>
                                        <td><?= $nameRef; ?></td>
                                        <td><?= $row_cancelled['source_language'] . "/" . $row_cancelled['target_language']; ?></td>
                                        <!-- <td><?= $row_cancelled['canceled_by'] == 1 ? 'LSUK' : 'Client'; ?></td> -->
                                        <td>LSUK</td>
                                        <td><small><?= $row_cancelled['canceled_date'] ? date("d-m-Y H:i:s", strtotime($row_cancelled['canceled_date'])) : "---"; ?></small></td>
                                        <td><?= strpos($string, "sign") !== false ? $array_notice_period_sign[$row_cancelled['notice_period']] : $array_notice_period[$row_cancelled['notice_period']]; ?></td>
                                        <td><small><?= strlen($row_cancelled['canceled_reason']) < 80 ? $row_cancelled['canceled_reason'] : substr($row_cancelled['canceled_reason'], 0, 80) . " ..."; ?></small></td>
                                        <td><?php echo $misc->dated($row_cancelled['created_date']); ?> </td>
                                    </tr>
                            <?php }
                            } else {
                                echo "<tr><td colspan='8' align='center'>There are no records available in this list! Thank you</td></tr>";
                            } ?>
                        </tbody>
                    </table>
                </div>
                <div id="tab_interpreter_amendments" class="tab-pane fade <?= isset($_GET['show_amendments']) ? 'active in' : '' ?>">
                    <h4>Interpreter Amendments Summary</h4>
                    <div class="row">
                        <div class="form-group col-sm-2">
                            <small>From Date</small>
                            <input type="date" name="d_from" id="d_from" class="form-control form-control-sm" value="<?php echo $_GET['d_from']; ?>" />
                        </div>
                        <div class="form-group col-sm-2">
                            <small>To Date</small>
                            <input type="date" name="d_to" id="d_to" class="form-control form-control-sm" value="<?php echo $_GET['d_to']; ?>" />
                        </div>
                       
                       
                        <div class="form-group col-sm-2">
                            <small>Search</small>
                            <input type="text" name="keyw" id="keyw" class="form-control form-control-sm" value="<?php echo $_GET['keyw']; ?>" />
                        </div>
                        <div class="form-group col-md-2"><br>
                            <a href="javascript:void(0)" title="Click to Filter Cancelled jobs" onclick="filter_list('show_amendments')"><span class="btn btn-danger">Filter List</span></a>
                            <a href="full_view_interpreter.php?view_id=<?=$view_id?>&show_amendments=1" title="Click to reset filters"><span class="btn btn-warning">Clear</span></a>
                        </div>
                    </div>
                    <table class="table table-bordered">
                        <thead class="bg-danger">
                            <tr>
                                <td width="1%"><b>S.No</b></td>
                                <td width="8%"><b>Job Info</b></td>
                                <td><b>Job Reference</b></td>
                                <td><b>Amended By</b></td>
                                <td><b>Amended Date</b></td>
                                <td><b>Reason</b></td>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            if ($_GET['d_from'] && $_GET['d_to']) {
                               
                                $append_date_rangee = " AND DATE(amended_records.dated) BETWEEN '" . $_GET['d_from'] . "' AND '" . $_GET['d_to'] . "'";
                            }
                           
                           
                            if (isset($_GET['keyw'])) {
                                $keyw = $_GET['keyw']; 
                                $append_jobb = " AND amended_records.order_id = " . (int)$keyw;  // Ensure it's an integer
                            }
                            
                            $get_amendmends = $acttObj->read_all("*", "amended_records", "interpreter_id=" . $view_id . $append_date_rangee . $append_jobb);
                            if ($get_amendmends->num_rows > 0) {
                                $counter_amended = 1;
                                while ($row_amended = $get_amendmends->fetch_assoc()) { 
                                        
                                       // Fetch nameRef based on job_type
                                        $nameReff = "";
                                        switch ($row_amended['type']) {
                                            case "interpreter":
                                                // Job Type 1 -> Interpreter
                                                $nameRefQueryAmend = $acttObj->read_all("nameRef", "interpreter", "id = " . $row_amended['order_id']);
                                                
                                                
                                                if ($nameRefQueryAmend->num_rows > 0) {
                                                    $nameRefDataa = $nameRefQueryAmend->fetch_assoc();
                                                    $nameReff = $nameRefDataa['nameRef'];
                                                  
                                                }
                                                break;
                                            case "telephone":
                                                // Job Type 2 -> Telephone
                                                $nameRefQueryAmend = $acttObj->read_all("nameRef", "telephone", "id = " . $row_amended['order_id']);
                                                if ($nameRefQueryAmend->num_rows > 0) {
                                                    $nameRefDataa = $nameRefQueryAmend->fetch_assoc();
                                                    $nameReff = $nameRefDataa['nameRef'];
                                                    
                                                }
                                                break;
                                            case "translation":
                                                // Job Type 3 -> Translation
                                                $nameRefQueryAmend = $acttObj->read_all("nameRef", "translation", "id = " . $row_amended['order_id']);
                                                if ($nameRefQueryAmend->num_rows > 0) {
                                                    $nameRefDataa = $nameRefQueryAmend->fetch_assoc();
                                                    $nameReff = $nameRefDataa['nameRef'];
                                                    
                                                }
                                                break;
                                            default:
                                                // In case of an unknown job type, leave nameRef empty
                                                $nameReff = "N/A";
                                        }
                                    ?>
                                    <tr>
                                    <td align="center"><b><?= $counter_amended++; ?></b></td>
                                    <td><?= $array_order_types[$row_amended['type']] . " ID#" . $row_amended['order_id']; ?></td>
                                    <td><?= $nameReff; ?></td>
                                    <td><?= $row_amended['amended_by'] ?></td>
                                    <td><small><?= $row_amended['dated'] ? date("d-m-Y", strtotime($row_amended['dated'])) : "---"; ?></small></td>
                                    <td width="40%">
                                        <?php 
                                            $reason = $acttObj->read_specific("amend_note", $row_amended['type'], "id = " . $row_amended['order_id']);
                                            if($reason){
                                                echo $reason['amend_note'];
                                            } else {
                                                echo "N/A";
                                            }
                                        ?>
                                    </td>
                                    
                                </tr>
                            <?php }
                            } else {
                                echo "<tr><td colspan='8' align='center'>There are no records available in this list! Thank you</td></tr>";
                            } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <!-- Modal to display record -->
        <div class="modal modal-info fade col-md-8 col-md-offset-2" data-toggle="modal" data-target=".bs-example-modal-lg" id="view_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;">
            <div class="modal-dialog" role="document" style="width:auto;">
                <div class="modal-content">
                    <div class="modal-header bg-default bg-light-ltr">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="myModalLabel">Record Details</h4>
                    </div>
                    <div class="modal-body" id="view_modal_data" style="overflow-x:auto;">
                        <table class="table table-bordered">
                            <tr>
                                <td><input readonly type="text" class="form-control" id="selected_language" style='width:165px;'></td>
                                <td>
                                    <label><input checked type="checkbox" id='interpreting_type' name="interpreting_type[]" value="interp"><span class="label label-info" checked>Interpreting</span></label>
                                    <label><input type="checkbox" id='interpreting_type' name="interpreting_type[]" value="telep"><span class="label label-primary">Telephone </span></label>
                                    <label><input type="checkbox" id='interpreting_type' name="interpreting_type[]" value="trans"><span class="label label-success">Translation </span></label>
                                </td>
                                <td><select class='form-control' id='selected_level' style='width:165px;'>
                                        <option value='1' selected>Native</option>
                                        <option value='2'>Fluent</option>
                                        <option value='3'>Intermediate</option>
                                        <option value='4'>Basic</option>
                                    </select></td>
                                <td><button type='button' class='btn btn-success btn-sm' onclick='add_language()'>Add</button>
                                    <button type='button' class='btn btn-danger btn-sm' onclick='cancel_language()'>Cancel</button>
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div class="modal-footer bg-default">
                        <button type="button" class="btn  btn-primary pull-right" data-dismiss="modal"> Close</button>
                    </div>
                </div>
            </div>
        </div>
        <!--End of modal-->


        <!-- Modal -->
        <div id="showWorkedHoursModal" class="modal fade" role="dialog">
            <div class="modal-dialog modal-lg">

                <!-- Modal content-->
                <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Modal Header</h4>
                </div>
                <div class="modal-body">
                    <p>Some text in the modal.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
                </div>

            </div>
        </div>


        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/js/bootstrap.min.js"></script>
    </body>
    <script type="text/javascript">
        function MM_openBrWindow(theURL, winName, features) {
            window.open(theURL, winName, features);
        }

        function popupwindow(url, title, w, h) {
            var left = (screen.width / 2) - (w / 2);
            var top = (screen.height / 2) - (h / 2);
            return window.open(url, title, 'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=yes, resizable=yes, copyhistory=no, width=' + w + ', height=' + h + ', top=' + top + ', left=' + left);
        }
        $(document).ready(function() {
            <?php if ($action_addnotes) { ?>
                var rowCounter = 1;

                function updateSerialNumbers() {
                    // Update serial numbers for all rows
                    $('.table_todo tbody tr').each(function(index) {
                        $(this).find('.serial-number').text(index + 1);
                    });
                }

                $("#addRowBtn").click(function() {
                    var newRow = $(".new_row:first").clone();
                    newRow.find('input[name="details[]"]').val('');
                    newRow.find('input[name="details[]"]').attr('required', 'required');
                    newRow.find('.removeRowBtn').removeClass('hidden');
                    newRow.find('.removeRowBtn').show();
                    newRow.find(':checkbox').prop('checked', false);

                    // Add a serial number
                    newRow.find('.serial-number').text(rowCounter);

                    $(".table_todo tbody").append(newRow);
                    rowCounter++;
                    $(".removeRowBtn").removeClass('hidden');
                    $(".removeRowBtn").show();

                    // Update serial numbers after adding a new row
                    updateSerialNumbers();
                });

                $(".table_todo").on('click', '.removeRowBtn', function() {
                    if (rowCounter > 0) {
                        $(this).closest('tr').remove();
                        rowCounter--;
                        updateSerialNumbers();
                        if (rowCounter === 0) {
                            $(".removeRowBtn").addClass('hidden');
                            $(".removeRowBtn").hide();
                        }
                    }
                });
            <?php } ?>
        });
        function filter_list(tab_name = "") {
                var append_url = "full_view_interpreter.php?view_id=<?=$view_id?>";
                if (tab_name == "show_lateness") {
                    var l_date_from = $('#l_date_from').val();
                    var l_date_to = $('#l_date_to').val();
                    if (l_date_from && l_date_to) {
                        append_url += '&l_date_from=' + l_date_from + '&l_date_to=' + l_date_to;
                    }
                    var l_job_type = $('#l_job_type').val();
                    if (l_job_type) {
                        append_url += '&l_job_type=' + l_job_type;
                    }
                    var l_minutes = $('#l_minutes').val();
                    if (l_minutes) {
                        append_url += '&l_minutes=' + l_minutes;
                    }
                }else if(tab_name == "show_amendments"){
                    var d_from = $('#d_from').val();
                    var d_to = $('#d_to').val();
                    var keyw = $('#keyw').val();
                    if (keyw) {
                        append_url += '&keyw=' + keyw;
                    }
                    if (d_from && d_to) {
                        append_url += '&d_from=' + d_from + '&d_to=' + d_to;
                    }
                   
                   
                }else {
                    var date_from = $('#date_from').val();
                    var date_to = $('#date_to').val();
                    var job_id = $('#keywo').val();
                    if (job_id) {
                        append_url += '&job_id=' + job_id;
                    }
                    if (date_from && date_to) {
                        append_url += '&date_from=' + date_from + '&date_to=' + date_to;
                    }
                    var job_type = $('#job_type').val();
                    if (job_type) {
                        append_url += '&job_type=' + job_type;
                    }
                    var canceled_by = $('#canceled_by').val();
                    if (canceled_by) {
                        append_url += '&canceled_by=' + canceled_by;
                    }
                }
                if (tab_name) {
                    append_url += '&' + tab_name + '=1';
                }
                window.location.href = append_url;
            }
    </script>

    <script>
        $(document).ready(function() {
            $('#showWorkedHoursModal').on('show.bs.modal', function (e) {
                var button = $(e.relatedTarget); // link clicked
                var jobType = button.data('title'); // face2face_jobs / telephone_jobs / translation_jobs
                var interpId = button.data('interp-id');
                var modal = $(this);

                modal.find('.modal-title').text("Loading..."); 
                modal.find('.modal-body').html("<p>Loading data...</p>");

                $.ajax({
                    url: 'ajax_functions.php', // PHP handler
                    type: 'GET',
                    data: { jobType: jobType, interpId: interpId, action: 'get_interpreter_jobs' },
                    success: function(response) {
                        modal.find('.modal-title').text(jobType.replace(/_/g, ' ').toUpperCase());
                        modal.find('.modal-body').html(response);
                    },
                    error: function() {
                        modal.find('.modal-body').html("<p class='text-danger'>Error loading data</p>");
                    }
                });
            });
        });
    </script>

    </html>
<?php } ?>