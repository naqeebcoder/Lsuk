<?php
session_start();
include "../source/setup_email.php";
include '../lsuk_system/actions.php';
$dated = date('Y-m-d');

if (isset($_POST['btn_submit_registration']) && isset($_POST['disclaimer'])) {
    $captcha_verified = 1;
    if ($captcha_verified == 1) {
        // if (isset($_POST['g-recaptcha-response']) && !empty($_POST['g-recaptcha-response'])) {
        // $secret = '6LextRoUAAAAAPvBF31eiYCmVP7Ne8a6mSez83zl';
        // $ip = $_SERVER['REMOTE_ADDR'];
        // $captcha = $_POST['g-recaptcha-response'];
        // $rsp = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=$secret&response=$captcha&remoteip=$ip");
        // $arr = json_decode($rsp, TRUE);
        if ($captcha_verified == 1) {
            if ($_SESSION["signin_token"] == $_POST["signin_token"]) {
                if (time() >= $_SESSION["signin_token_expiry"]) {
                    $_SESSION['returned_message'] = '<div class="alert alert-danger alert-dismissible col-md-6 col-md-offset-3 text-center">
                        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times; </a>
                        <i class="glyphicon glyphicon-exclamation-circle"></i> Sorry! It took longer to submit your request. Please fill your form & try again. Thank you
                        </div>';
                } else {
                    $table = "interpreter_reg";
                    $email = strtolower(trim(str_replace(" ", "", $_POST['email'])));
                    $check_email = $obj->read_specific("id", $table, "email='" . $obj->con->real_escape_string($email) . "'")['id'];
                    if (empty($check_email)) {
                        if (isset($_SESSION['verified_otp']) && $_SESSION['verified_otp'] == $email) {
                            $first_name = $_POST['first_name'];
                            $last_name = $_POST['last_name'];
                            $dob = $_POST['dob'];
                            $country = $_POST['selected_country'];
                            $gender = $_POST['gender'];
                            $contact_no = $_POST['contact_no'];
                            $mobile_no = $_POST['mobile_no'];
                            $utr = $_POST['utr'];
                            $building_name = $_POST['building_name'];
                            $line1 = $_POST['line1'];
                            $line2 = $_POST['line2'];
                            $line3 = $_POST['line3'];
                            $city = $_POST['city'];
                            $post_code = $_POST['post_code'];
                            $interp = $_POST['interp'];
                            $telep = $_POST['telep'];
                            $trans = $_POST['trans'];
                            $work_type = $_POST['work_type'];
                            $int_type = $_POST['int_type'];
                            $new_password = '@' . strtok($first_name, " ") . substr(str_shuffle('0123456789abcdwxyz'), 0, 5) . substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 3);
                            // Do validation here
                            $invalid_dob = $invalid_gender = $invalid_criteria = false;
                            $dateString = $dob;
                            $date = DateTime::createFromFormat('Y-m-d', $dateString);
                            if (!$date || $date->format('Y-m-d') !== $dateString) {
                                $invalid_dob = true;                        
                            }
                            $array_gender = array("Male", "Female", "No Preference");
                            if (!in_array($gender, $array_gender)) {
                                $invalid_gender = true;
                            }
                            if (!isset($interp) && !isset($telep) && !isset($trans)) {
                                $invalid_criteria = true;
                            }
                            if (!$invalid_dob && !$invalid_gender && !$invalid_criteria) {
                                $insert_array = array(
                                    "name" => $obj->con->real_escape_string(trim($_POST['first_name']) . ' ' . trim($_POST['last_name'])),
                                    "dob" => $obj->con->real_escape_string($_POST['dob']),
                                    "reg_date" => $dated,
                                    "dated" => $dated,
                                    "created_date" => date("Y-m-d H:i:s"),
                                    "email" => $obj->con->real_escape_string($email),
                                    "password" => $new_password,
                                    "contactNo" => $obj->con->real_escape_string(ltrim($_POST['contact_no'], '0')),
                                    "contactNo2" => $obj->con->real_escape_string($_POST['mobile_no']),
                                    "gender" => $obj->con->real_escape_string($_POST['gender']),
                                    "buildingName" => $obj->con->real_escape_string($_POST['building_name']),
                                    "country" => $obj->con->real_escape_string($_POST['selected_country']),
                                    "city" => $obj->con->real_escape_string($_POST['city']),
                                    "line1" => $obj->con->real_escape_string($_POST['line1']),
                                    "line2" => $obj->con->real_escape_string($_POST['line2']),
                                    "line3" => $obj->con->real_escape_string($_POST['line3']),
                                    "postCode" => $obj->con->real_escape_string($_POST['post_code']),
                                    "applicationForm" => "Soft Copy",
                                    "agreement" => "Soft Copy",
                                    "ni" => $obj->con->real_escape_string($_POST['utr']),
                                    "bnakName" => $obj->con->real_escape_string($_POST['bank_name']) . " (" . $obj->con->real_escape_string($_POST['branch']) . ")",
                                    "acName" => $obj->con->real_escape_string($_POST['account_name']),
                                    "acntCode" => $obj->con->real_escape_string(str_replace("-", "", trim($_POST['sort_code']))),
                                    "acNo" => $obj->con->real_escape_string($_POST['account_number']),
                                    "sbmtd_by" => "Online",
                                    "work_type" => $obj->con->real_escape_string($_POST['work_type']),
                                    "is_temp" => 1
                                );
                                $obj->insert($table, $insert_array);
                                $int_id = $obj->con->insert_id;
                                // Start array for new fields now
                                $update_array = array("code" => "id-" . $int_id);
                                if ($int_type == "nrpsi") {
                                    $update_array['is_nrpsi'] = 1;
                                    $update_array['nrpsi_number'] = $obj->con->real_escape_string($_POST['nrpsi_number']);
                                } else {
                                    $update_array['is_nrpsi'] = 0;
                                    $update_array['nrpsi_number'] = '';
                                }
                                if ($int_type == "ciol") {
                                    $update_array['is_ciol'] = 1;
                                    $update_array['ciol_number'] = $obj->con->real_escape_string($_POST['ciol_number']);
                                } else {
                                    $update_array['is_ciol'] = 0;
                                    $update_array['ciol_number'] = '';
                                }
                                if ($int_type == "iti") {
                                    $update_array['is_iti'] = 1;
                                    $update_array['iti_number'] = $obj->con->real_escape_string($_POST['iti_number']);
                                } else {
                                    $update_array['is_iti'] = 0;
                                    $update_array['iti_number'] = '';
                                }
                                if ($int_type == "bsl") {
                                    $update_array['is_asli'] = 1;
                                    $update_array['asli_number'] = $obj->con->real_escape_string($_POST['asli_number']);
                                } else {
                                    $update_array['is_asli'] = 0;
                                    $update_array['asli_number'] = '';
                                }

                                $array_types = array();
                                if (isset($interp) && !empty($interp)) {
                                    if (isset($_POST['is_dbs_auto'])) {
                                        if ($_POST['dbs_auto_number']) {
                                            array_push($array_types, $interp);
                                            $update_array['interp'] = "Yes";
                                            $update_array['crbDbs'] = "Soft Copy";
                                            $update_array['is_dbs_auto'] = 1;
                                            $update_array['dbs_checked'] = 0; //0 for Yes
                                            $update_array['dbs_auto_number'] = trim($_POST['dbs_auto_number']);
                                        }
                                    } else {
                                        //DBS Document
                                        if ($_FILES["dbs_file"]["name"] != NULL) {
                                            array_push($array_types, $interp);
                                            $update_array['interp'] = "Yes";
                                            $update_array['crbDbs'] = "Soft Copy";
                                            $update_array['dbs_checked'] = 0; //0 for Yes
                                            $update_array['dbs_no'] = trim($_POST['dbs_no']);
                                            $update_array['dbs_issue_date'] = $_POST['dbs_issue_date'];
                                            $update_array['dbs_expiry_date'] = $_POST['dbs_expiry_date'];
                                            if ($_FILES["dbs_file"]["name"] != NULL) {
                                                $update_array['dbs_file'] = $obj->upload_file("../lsuk_system/file_folder/issue_expiry_docs", $_FILES["dbs_file"]["name"], $_FILES["dbs_file"]["type"], $_FILES["dbs_file"]["tmp_name"], round(microtime(true)));
                                            }
                                        } else {
                                            $update_array['dbs_checked'] = 1;
                                            $update_array['dbs_issue_date'] = "1001-01-01";
                                            $update_array['dbs_expiry_date'] = "1001-01-01";
                                        }
                                    }
                                } else {
                                    $update_array['dbs_checked'] = 1;
                                    $update_array['interp'] = "No";
                                }
                                $obj->update($table, $update_array, "id=" . $int_id);
                                if (isset($telep) && !empty($telep)) {
                                    array_push($array_types, $telep);
                                    $update_array['telep'] = "Yes";
                                } else {
                                    $update_array['telep'] = "No";
                                }
                                if (isset($trans) && !empty($trans)) {
                                    array_push($array_types, $trans);
                                    $update_array['trans'] = "Yes";
                                } else {
                                    $update_array['trans'] = "No";
                                }
                                if (!empty($array_types)) {
                                    $array_types = " <h3 style='display:inline-block;border:1px solid grey;padding: 4px;border-radius: 4px;'>" . implode("</h3> <h3 style='display:inline-block;border:1px solid grey;padding: 4px;border-radius: 4px;'>", $array_types) . "</h3> ";
                                    $array_types_label = $array_types;
                                } else {
                                    $array_types = "Not selected!";
                                    $array_types_label = "Interpreter";
                                }

                                //Languages fields
                                if ($_POST['array_languages']) {
                                    $selected_languages = array();
                                    foreach ($_POST['array_languages'] as $key_language => $language) {
                                        array_push($selected_languages, $language);
                                        if (isset($_POST['can_do_f2f'][$key_language])) {
                                            $obj->insert("interp_lang", array("lang" => trim($language), "code" => "id-" . $int_id, "dated" => $dated, "level" => $_POST['selected_level'][$key_language], "added_via" => 2, "type" => "interp"));
                                        }
                                        if (isset($_POST['can_do_tp'][$key_language])) {
                                            $obj->insert("interp_lang", array("lang" => trim($language), "code" => "id-" . $int_id, "dated" => $dated, "level" => $_POST['selected_level'][$key_language], "added_via" => 2, "type" => "telep"));
                                        }
                                        if (isset($_POST['can_do_tr'][$key_language])) {
                                            $obj->insert("interp_lang", array("lang" => trim($language), "code" => "id-" . $int_id, "dated" => $dated, "level" => $_POST['selected_level'][$key_language], "added_via" => 2, "type" => "trans"));
                                        }
                                    }
                                    $selected_languages = " <h3 style='display:inline-block;border:1px solid grey;padding: 4px;border-radius: 4px;'>" . implode("</h3> <h3 style='display:inline-block;border:1px solid grey;padding: 4px;border-radius: 4px;'>", $selected_languages) . "</h3> ";
                                } else {
                                    $selected_languages = "No language selected!";
                                }
                                //Upload profile photo
                                if ($_FILES["profile_photo"]["name"] != NULL) {
                                    $update_array["interp_pix"] = $obj->upload_file("../lsuk_system/file_folder/interp_photo", $_FILES["profile_photo"]["name"], $_FILES["profile_photo"]["type"], $_FILES["profile_photo"]["tmp_name"], round(microtime(true)));
                                }
                                //Upload nin file
                                if ($_FILES["nin"]["name"] != NULL) {
                                    $update_array["nin"] = $obj->upload_file("../lsuk_system/file_folder/nin", $_FILES["nin"]["name"], $_FILES["nin"]["type"], $_FILES["nin"]["tmp_name"], round(microtime(true)));
                                }
                                //UK citizen
                                $citizen = $_POST['citizen'];
                                $work_evid = $_POST['work_evid'];
                                if ($citizen == "Yes") {
                                    //Identity / passport document
                                    $update_array["uk_citizen"] = 1;
                                    if ($_FILES["passport_file"]["name"] != NULL) {
                                        $update_array['identityDocument'] = "Soft Copy";
                                        $update_array['id_doc_no'] = trim($_POST['passport_number']);
                                        $update_array['id_doc_issue_date'] = $_POST['passport_issue_date'];
                                        $update_array['id_doc_expiry_date'] = $_POST['passport_expiry_date'];
                                        if ($_FILES["passport_file"]["name"] != NULL) {
                                            $update_array['id_doc_file'] = $obj->upload_file("../lsuk_system/file_folder/issue_expiry_docs", $_FILES["passport_file"]["name"], $_FILES["passport_file"]["type"], $_FILES["passport_file"]["tmp_name"], round(microtime(true)));
                                        }
                                    } else {
                                        $update_array['uk_citizen'] = 0;
                                        $update_array['identityDocument'] = "";
                                        $update_array['id_doc_issue_date'] = "1001-01-01";
                                        $update_array['id_doc_expiry_date'] = "1001-01-01";
                                    }
                                } else {
                                    $update_array['uk_citizen'] = 0;
                                }
                                //Evidence right to work document
                                if ($update_array['uk_citizen'] == 0) {
                                    $update_array['work_evid_issue_date'] = $_POST['work_evid_issue_date'];
                                    $update_array['work_evid_expiry_date'] = $_POST['work_evid_expiry_date'];
                                    if ($_FILES["work_evid_file"]["name"] != NULL) {
                                        $update_array['work_evid_file'] = $obj->upload_file("../lsuk_system/file_folder/issue_expiry_docs", $_FILES["work_evid_file"]["name"], $_FILES["work_evid_file"]["type"], $_FILES["work_evid_file"]["tmp_name"], round(microtime(true)));
                                    }
                                } else {
                                    $update_array['work_evid_issue_date'] = "1001-01-01";
                                    $update_array['work_evid_expiry_date'] = "1001-01-01";
                                }

                                //Driving license
                                $is_drive = $_POST['is_drive'];
                                if ($is_drive == "Yes" && $_FILES["driving_license_file"]["name"] != NULL) {
                                    $update_array['anyOther'] = $obj->upload_file("../lsuk_system/file_folder/anyOther", $_FILES["driving_license_file"]["name"], $_FILES["driving_license_file"]["type"], $_FILES["driving_license_file"]["tmp_name"], round(microtime(true)));
                                }
                                //Master Translation Document
                                $is_master = $_POST['is_master'];
                                if ($is_master == "Yes" && $_FILES["master_file"]["name"] != NULL) {
                                    $update_array['master_file'] = $obj->upload_file("../lsuk_system/file_folder/master_file", $_FILES["master_file"]["name"], $_FILES["master_file"]["type"], $_FILES["master_file"]["tmp_name"], round(microtime(true)));
                                }

                                //recongnized qualification
                                $translation_qualifications = $_POST['translation_qualifications'];
                                if ($translation_qualifications == "Yes" && $_FILES["int_qualification_file"]["name"] != NULL) {
                                    $update_array['int_qualification'] = $obj->upload_file("lsuk_system/file_folder/int_qualification", $_FILES["int_qualification_file"]["name"], $_FILES["int_qualification_file"]["type"], $_FILES["int_qualification_file"]["tmp_name"], round(microtime(true)));
                                }
                                //bank details
                                $account_name = $_POST['account_name'];
                                $bank_name = $_POST['bank_name'];
                                $branch = $_POST['branch'];
                                $account_number = $_POST['account_number'];
                                $sort_code = $_POST['sort_code'];
                                //education details
                                $institute = $_POST['institute'];
                                $qualification = $_POST['qualification'];
                                $from_date = $_POST['from_date'];
                                $to_date = $_POST['to_date'];
                                //references details
                                $ref_name1 = $_POST['ref_name1'];
                                $ref_relationship1 = $_POST['ref_relationship1'];
                                $ref_company1 = $_POST['ref_company1'];
                                $ref_phone1 = $_POST['ref_phone1'];
                                $ref_email1 = $_POST['ref_email1'];
                                $ref_name2 = $_POST['ref_name2'];
                                $ref_relationship2 = $_POST['ref_relationship2'];
                                $ref_company2 = $_POST['ref_company2'];
                                $ref_phone2 = $_POST['ref_phone2'];
                                $ref_email2 = $_POST['ref_email2'];
                                //signatures
                                $signature_name = $_POST['signature_name'];
                                $signature_date = $_POST['signature_date'];
                                //Insert into database
                                $extra_data['is_drive'] = $obj->con->real_escape_string($_POST['is_drive']);
                                $extra_data['is_master'] = $obj->con->real_escape_string($_POST['is_master']);
                                $extra_data['translation_qualifications'] = $obj->con->real_escape_string($_POST['translation_qualifications']);
                                if ($_POST['is_experience'] == "Yes") {
                                    $extra_data['experience_years'] = $_POST['experience_years'];
                                }
                                if ($_POST['institute']) {
                                    $extra_data['institute'] = $obj->con->real_escape_string($_POST['institute']);
                                }
                                if ($_POST['qualification']) {
                                    $extra_data['qualification'] = $obj->con->real_escape_string($_POST['qualification']);
                                }
                                if ($_POST['from_date']) {
                                    $extra_data['from_date'] = $obj->con->real_escape_string($_POST['from_date']);
                                }
                                if ($_POST['to_date']) {
                                    $extra_data['to_date'] = $obj->con->real_escape_string($_POST['to_date']);
                                }
                                if (isset($_POST['request_lsuk_dbs'])) {
                                    $extra_data['requested_lsuk_dbs'] = "Yes";
                                }
                                $update_array["extra_data"] = json_encode($extra_data);
                                $obj->update($table, $update_array, "id=" . $int_id);
                                //experience years
                                $is_experience = $_POST['is_experience'];
                                $experience_years = $is_experience == "Yes" ? $_POST['experience_years'] : "No experience";
                                //skills selected
                                if (!empty($_POST['skills'])) {
                                    foreach ($_POST['skills'] as $int_skill) {
                                        $obj->insert("interp_skill", array("skill" => $int_skill, "code" => "id-" . $int_id, "dated" => $dated));
                                    }
                                    $skills = " <h3 style='display:inline-block;border:1px solid grey;padding: 4px;border-radius: 4px;'>" . implode("</h3> <h3 style='display:inline-block;border:1px solid grey;padding: 4px;border-radius: 4px;'>", $_POST['skills']) . "</h3> ";
                                } else {
                                    $skills = "Not selected!";
                                }
                                //Email format to send
                                if (!empty($ref_name1)) {
                                    $append_ref1 = "<tr><td colspan='4' align='center' style='background: grey; color: white;font-size: 18px;'>Reference 1 Details</td></tr>
                                        <tr>
                                        <td style='border: 1px solid grey;padding:5px;background:gainsboro;font-weight:bold;'>Full Name (Relationship)</td>
                                        <td style='border: 1px solid yellowgreen;padding:5px;'>" . $ref_name1 . " (" . $ref_relationship1 . ")</td>
                                        <td style='border: 1px solid grey;padding:5px;background:gainsboro;font-weight:bold;'>Company</td>
                                        <td style='border: 1px solid yellowgreen;padding:5px;'>" . $ref_company1 . "</td>
                                        </tr>
                                        <tr>
                                        <td style='border: 1px solid grey;padding:5px;background:gainsboro;font-weight:bold;'>Phone</td>
                                        <td style='border: 1px solid yellowgreen;padding:5px;'>" . $ref_phone1 . "</td>
                                        <td style='border: 1px solid grey;padding:5px;background:gainsboro;font-weight:bold;'>Email</td>
                                        <td style='border: 1px solid yellowgreen;padding:5px;'>" . $ref_email1 . "</td>
                                        </tr>";
                                    //Add reference 1 record if any
                                    $obj->insert("int_references", array("name" => $obj->con->real_escape_string($_POST['ref_name1']), "relation" => $obj->con->real_escape_string($_POST['ref_relationship1']), "company" => $obj->con->real_escape_string($_POST['ref_company1']), "phone" => $obj->con->real_escape_string($_POST['ref_phone1']), "email" => $obj->con->real_escape_string($_POST['ref_email1']), "int_id" => $int_id, "dated" => $dated));
                                    $last_id_1 = $obj->con->insert_id;
                                    $name_of_int = $obj->read_specific("name", "interpreter_reg", "id=" . $int_id)['name'];

                                    // Email Template
                                    $email_template = $obj->read_specific("em_format", 'email_format', "id = 60")['em_format'];

                                    $confirmation_url = "<a style='text-decoration: none;font-size: 16px;border: 1px solid;padding: 4px;border-radius: 4px;background: #618cd6;color: white;' href='https://lsuk.org/reference_confirmation.php?id=" . base64_encode($last_id_1) . "'>CLICK HERE</a>";

                                    $ref_infos_replace_with = [$ref_name1, $name_of_int, $array_types_label, $confirmation_url];
                                    $ref_infos_replace = [
                                        '[REF_NAME]', '[INTERPRETER_NAME]', '[LANGUAGE_TYPE]', '[CLICK HERE]'
                                    ];

                                    $ref_message1 = str_replace($ref_infos_replace, $ref_infos_replace_with, $email_template);                                    
                                }
                                if (!empty($ref_name2)) {
                                    $append_ref2 = "<tr><td colspan='4' align='center' style='background: grey; color: white;font-size: 18px;'>Reference 2 Details</td></tr>
                                        <tr>
                                        <td style='border: 1px solid grey;padding:5px;background:gainsboro;font-weight:bold;'>Full Name (Relationship)</td>
                                        <td style='border: 1px solid yellowgreen;padding:5px;'>" . $ref_name2 . " (" . $ref_relationship2 . ")</td>
                                        <td style='border: 1px solid grey;padding:5px;background:gainsboro;font-weight:bold;'>Company</td>
                                        <td style='border: 1px solid yellowgreen;padding:5px;'>" . $ref_company2 . "</td>
                                        </tr>
                                        <tr>
                                        <td style='border: 1px solid grey;padding:5px;background:gainsboro;font-weight:bold;'>Phone</td>
                                        <td style='border: 1px solid yellowgreen;padding:5px;'>" . $ref_phone2 . "</td>
                                        <td style='border: 1px solid grey;padding:5px;background:gainsboro;font-weight:bold;'>Email</td>
                                        <td style='border: 1px solid yellowgreen;padding:5px;'>" . $ref_email2 . "</td>
                                        </tr>";
                                    //Add reference 2 record if any
                                    $obj->insert("int_references", array("name" => $obj->con->real_escape_string($_POST['ref_name2']), "relation" => $obj->con->real_escape_string($_POST['ref_relationship2']), "company" => $obj->con->real_escape_string($_POST['ref_company2']), "phone" => $obj->con->real_escape_string($_POST['ref_phone2']), "email" => $obj->con->real_escape_string($_POST['ref_email2']), "int_id" => $int_id, "dated" => $dated));
                                    $last_id_2 = $obj->con->insert_id;
                                    $rates_array = array("rate_group_id" => 1, "interpreter_id" => $int_id, "created_date" => date('Y-m-d H:i:s'));
                                    $done = $obj->insert("individual_interpreter_rates", $rates_array);

                                    // Email Template
                                    $email_template_2 = $obj->read_specific("em_format", 'email_format', "id = 60")['em_format'];

                                    $confirmation_url_2 = "<a style='text-decoration: none;font-size: 16px;border: 1px solid;padding: 4px;border-radius: 4px;background: #618cd6;color: white;' href='https://lsuk.org/reference_confirmation.php?id=" . base64_encode($last_id_2) . "'>CLICK HERE</a>";

                                    $ref_infos_replace_with_2 = [$ref_name2, $name_of_int, $array_types_label, $confirmation_url_2];
                                    $ref_infos_replace = [
                                        '[REF_NAME]', '[INTERPRETER_NAME]', '[LANGUAGE_TYPE]', '[CLICK HERE]'
                                    ];

                                    $ref_message2 = str_replace($ref_infos_replace, $ref_infos_replace_with_2, $email_template_2);
                                }
                                $message = "<style type='text/css'>
                                    table.myTable{border-collapse: collapse;}
                                    table.myTable td, table.myTable th {border: 1px solid yellowgreen;padding: 5px;}
                                    </style>
                                    <table class='myTable' width='80%'>
                                    <tr><td colspan='4' align='center' style='background: grey; color: white;font-size: 18px;'>PERSONAL DETAILS</td></tr>
                                    <tr>
                                    <td style='border: 1px solid grey;padding:5px;background:gainsboro;font-weight:bold;'>First Name</td>
                                    <td style='border: 1px solid yellowgreen;padding:5px;'>" . $first_name . "</td>
                                    <td style='border: 1px solid grey;padding:5px;background:gainsboro;font-weight:bold;'>last Name</td>
                                    <td style='border: 1px solid yellowgreen;padding:5px;'>" . $last_name . "</td>
                                    </tr>
                                    <tr>
                                    <td style='border: 1px solid grey;padding:5px;background:gainsboro;font-weight:bold;'>Date of Birth</td>
                                    <td style='border: 1px solid yellowgreen;padding:5px;'>" . $dob . "</td>
                                    <td style='border: 1px solid grey;padding:5px;background:gainsboro;font-weight:bold;'>Gender</td>
                                    <td style='border: 1px solid yellowgreen;padding:5px;'>" . $gender . "</td>
                                    </tr>
                                    <tr>
                                    <td style='border: 1px solid grey;padding:5px;background:gainsboro;font-weight:bold;'>Contact Number</td>
                                    <td style='border: 1px solid yellowgreen;padding:5px;'>" . $contact_no . "</td>
                                    <td style='border: 1px solid grey;padding:5px;background:gainsboro;font-weight:bold;'>Mobile Number</td>
                                    <td style='border: 1px solid yellowgreen;padding:5px;'>" . $mobile_no . "</td>
                                    </tr>
                                    <tr>
                                    <td style='border: 1px solid grey;padding:5px;background:gainsboro;font-weight:bold;'>Email</td>
                                    <td style='border: 1px solid yellowgreen;padding:5px;'>" . $email . "</td>
                                    <td style='border: 1px solid grey;padding:5px;background:gainsboro;font-weight:bold;'>NI / UTR #</td>
                                    <td style='border: 1px solid yellowgreen;padding:5px;'>" . $utr . "</td>
                                    </tr>
                                    <tr>
                                    <td colspan='1' style='border: 1px solid grey;padding:5px;background:gainsboro;font-weight:bold;'>Working As</td>
                                    <td colspan='3' style='border: 1px solid yellowgreen;padding:5px;'>" . $array_types . "</td>
                                    </tr>
                                    <tr><td colspan='4' align='center' style='background: grey; color: white;font-size: 18px;'>ADDRESS DETAILS</td></tr>
                                    <tr>
                                    <td style='border: 1px solid grey;padding:5px;background:gainsboro;font-weight:bold;'>Building Name</td>
                                    <td style='border: 1px solid yellowgreen;padding:5px;'>" . $building_name . "</td>
                                    <td style='border: 1px solid grey;padding:5px;background:gainsboro;font-weight:bold;'>Line 1</td>
                                    <td style='border: 1px solid yellowgreen;padding:5px;'>" . $line1 . "</td>
                                    </tr>
                                    <tr>
                                    <td style='border: 1px solid grey;padding:5px;background:gainsboro;font-weight:bold;'>Line 2</td>
                                    <td style='border: 1px solid yellowgreen;padding:5px;'>" . $line2 . "</td>
                                    <td style='border: 1px solid grey;padding:5px;background:gainsboro;font-weight:bold;'>Line 3</td>
                                    <td style='border: 1px solid yellowgreen;padding:5px;'>" . $line3 . "</td>
                                    </tr>
                                    <tr>
                                    <td style='border: 1px solid grey;padding:5px;background:gainsboro;font-weight:bold;'>Country (City)</td>
                                    <td style='border: 1px solid yellowgreen;padding:5px;'>" . $country . " (" . $city . ")</td>
                                    <td style='border: 1px solid grey;padding:5px;background:gainsboro;font-weight:bold;'>Post Code</td>
                                    <td style='border: 1px solid yellowgreen;padding:5px;'>" . $post_code . "</td>
                                    </tr>
                                    <tr><td colspan='4' align='center' style='background: grey; color: white;font-size: 18px;'>LANGUAGES DETAILS</td></tr>
                                    <tr>
                                    <td colspan='1' style='border: 1px solid grey;padding:5px;background:gainsboro;font-weight:bold;'>Speaking Languages</td>
                                    <td colspan='3' style='border: 1px solid yellowgreen;padding:5px;'>" . $selected_languages . "</td>
                                    </tr>
                                    <tr>
                                    <td style='border: 1px solid grey;padding:5px;background:gainsboro;font-weight:bold;'>UK Citizen?</td>
                                    <td style='border: 1px solid yellowgreen;padding:5px;'>" . $citizen . "</td>
                                    <td style='border: 1px solid grey;padding:5px;background:gainsboro;font-weight:bold;'>Right To Work</td>
                                    <td style='border: 1px solid yellowgreen;padding:5px;'>" . $work_evid . "</td>
                                    </tr>";
                                if (isset($_POST['is_dbs_auto'])) {
                                    $message .= "<tr>
                                        <td colspan='1' style='border: 1px solid grey;padding:5px;background:gainsboro;font-weight:bold;'>DBS on update service</td>
                                        <td colspan='3' style='border: 1px solid yellowgreen;padding:5px;'>Yes - DBS Number : <b>" . $update_array['dbs_auto_number'] . "</b></td>
                                        </tr>";
                                }
                                if (isset($_POST['request_lsuk_dbs'])) {
                                    $message .= "<tr><td colspan='4' style='border:1px solid grey;background: red;font-weight:bold;color: white;' align='center'><h4 style='margin: 12px;font-size: 20px;'>Requested LSUK to apply for DBS</h4></td></tr>";
                                }
                                $message .= "<tr>
                                    <td style='border: 1px solid grey;padding:5px;background:gainsboro;font-weight:bold;'>Driving?</td>
                                    <td style='border: 1px solid yellowgreen;padding:5px;'>" . $is_drive . "</td>
                                    <td style='border: 1px solid grey;padding:5px;background:gainsboro;font-weight:bold;'>Recognised Interpreting / Translation Qualification?</td>
                                    <td style='border: 1px solid yellowgreen;padding:5px;'>" . $translation_qualifications . "</td>
                                    </tr>
                                    <tr>
                                    <td style='border: 1px solid grey;padding:5px;background:gainsboro;font-weight:bold;'>Professional Experience?</td>
                                    <td style='border: 1px solid yellowgreen;padding:5px;'>" . $is_experience . "</td>
                                    <td style='border: 1px solid grey;padding:5px;background:gainsboro;font-weight:bold;'>Experience Years</td>
                                    <td style='border: 1px solid yellowgreen;padding:5px;'>" . $experience_years . "</td>
                                    </tr>
                                    <tr>
                                    <td colspan='1' style='border: 1px solid grey;padding:5px;background:gainsboro;font-weight:bold;'>Areas of specialization</td>
                                    <td colspan='3' style='border: 1px solid yellowgreen;padding:5px;'>" . $skills . "</td>
                                    </tr>
                                    <tr><td colspan='4' align='center' style='background: grey; color: white;font-size: 18px;'>BANK DETAILS</td></tr>
                                    <tr>
                                    <td style='border: 1px solid grey;padding:5px;background:gainsboro;font-weight:bold;'>Account Name</td>
                                    <td style='border: 1px solid yellowgreen;padding:5px;'>" . $account_name . "</td>
                                    <td style='border: 1px solid grey;padding:5px;background:gainsboro;font-weight:bold;'>Bank Name (Branch)</td>
                                    <td style='border: 1px solid yellowgreen;padding:5px;'>" . $bank_name . " (" . $branch . ")</td>
                                    </tr>
                                    <tr>
                                    <td style='border: 1px solid grey;padding:5px;background:gainsboro;font-weight:bold;'>Account Number</td>
                                    <td style='border: 1px solid yellowgreen;padding:5px;'>" . $account_number . "</td>
                                    <td style='border: 1px solid grey;padding:5px;background:gainsboro;font-weight:bold;'>Sort Code</td>
                                    <td style='border: 1px solid yellowgreen;padding:5px;'>" . $sort_code . "</td>
                                    </tr>
                                    <tr><td colspan='4' align='center' style='background: grey; color: white;font-size: 18px;'>EDUCATIONAL DETAILS</td></tr>
                                    <tr>
                                    <td style='border: 1px solid grey;padding:5px;background:gainsboro;font-weight:bold;'>Institute Name</td>
                                    <td style='border: 1px solid yellowgreen;padding:5px;'>" . $institute . "</td>
                                    <td style='border: 1px solid grey;padding:5px;background:gainsboro;font-weight:bold;'>Qualification</td>
                                    <td style='border: 1px solid yellowgreen;padding:5px;'>" . $qualification . "</td>
                                    </tr>
                                    <tr>
                                    <td style='border: 1px solid grey;padding:5px;background:gainsboro;font-weight:bold;'>From Date</td>
                                    <td style='border: 1px solid yellowgreen;padding:5px;'>" . $from_date . "</td>
                                    <td style='border: 1px solid grey;padding:5px;background:gainsboro;font-weight:bold;'>To Date</td>
                                    <td style='border: 1px solid yellowgreen;padding:5px;'>" . $to_date . "</td>
                                    </tr>
                                    <tr><td colspan='4' align='center' style='background: grey; color: white;font-size: 18px;'>REFERENCES</td></tr>
                                    " . $append_ref1 . $append_ref2 . "
                                    <tr><td colspan='4' align='center' style='background: grey; color: white;font-size: 18px;'>DISCLAIMER AND SIGNATURE</td></tr>
                                    <tr>
                                    <td style='border: 1px solid grey;padding:5px;background:gainsboro;font-weight:bold;'>Signature Name</td>
                                    <td style='border: 1px solid yellowgreen;padding:5px;'>" . $signature_name . "</td>
                                    <td style='border: 1px solid grey;padding:5px;background:gainsboro;font-weight:bold;'>Signature Date</td>
                                    <td style='border: 1px solid yellowgreen;padding:5px;'>" . $signature_date . "</td>
                                    </tr>
                                    </table>";
                                try {
                                    $mail->SMTPDebug = 0;
                                    $mail->isSMTP();
                                    $mail->Host = setupEmail::EMAIL_HOST;
                                    $mail->SMTPAuth   = true;
                                    $mail->Username   = setupEmail::HR_EMAIL;
                                    $mail->Password   = setupEmail::HR_PASSWORD;
                                    $mail->SMTPSecure = setupEmail::SECURE_TYPE;
                                    $mail->Port       = setupEmail::SENDING_PORT;
                                    $mail->setFrom(setupEmail::HR_EMAIL, setupEmail::FROM_NAME);
                                    $mail->addAddress(setupEmail::HR_EMAIL);
                                    $mail->addReplyTo(setupEmail::HR_EMAIL, setupEmail::FROM_NAME);
                                    $mail->isHTML(true);
                                    $mail->Subject = 'New Interpreter Registration Request';
                                    $mail->Body = $message;
                                    if ($update_array['interp_pix']) {
                                        if (file_exists(actionsClass::URL . "/lsuk_system/file_folder/interp_photo/" . $update_array['interp_pix'])) {
                                            $mail->AddAttachment(actionsClass::URL . "/lsuk_system/file_folder/interp_photo/" . $update_array['interp_pix'], "Profile Photo");
                                        }
                                    }
                                    if ($citizen == "No" && $update_array['work_evid_file']) {
                                        if (file_exists(actionsClass::URL . "/lsuk_system/file_folder/issue_expiry_docs/" . $update_array['work_evid_file'])) {
                                            $mail->AddAttachment(actionsClass::URL . "/lsuk_system/file_folder/issue_expiry_docs/" . $update_array['work_evid_file'], "Right to work evidence");
                                        }
                                    }
                                    if ($citizen == "Yes" && $update_array['id_doc_file']) {
                                        if (file_exists(actionsClass::URL . "/lsuk_system/file_folder/issue_expiry_docs/" . $update_array['id_doc_file'])) {
                                            $mail->AddAttachment(actionsClass::URL . "/lsuk_system/file_folder/issue_expiry_docs/" . $update_array['id_doc_file'], "Identity Document");
                                        }
                                    }
                                    if ($update_array['dbs_file']) {
                                        if (file_exists(actionsClass::URL . "/lsuk_system/file_folder/issue_expiry_docs/" . $update_array['dbs_file'])) {
                                            $mail->AddAttachment(actionsClass::URL . "/lsuk_system/file_folder/issue_expiry_docs/" . $update_array['dbs_file'], "DBS Document");
                                        }
                                    }
                                    if ($is_drive == "Yes" && $update_array['anyOther']) {
                                        if (file_exists(actionsClass::URL . "/lsuk_system/file_folder/anyOther/" . $update_array['anyOther'])) {
                                            $mail->AddAttachment(actionsClass::URL . "/lsuk_system/file_folder/anyOther/" . $update_array['anyOther'], "Driving License Attachment");
                                        }
                                    }
                                    if ($is_master == "Yes" && $update_array['master_file']) {
                                        if (file_exists(actionsClass::URL . "/lsuk_system/file_folder/master_file/" . $update_array['master_file'])) {
                                            $mail->AddAttachment(actionsClass::URL . "/lsuk_system/file_folder/master_file/" . $update_array['master_file'], "Translation Master Attachment");
                                        }
                                    }
                                    if ($translation_qualifications == "Yes" && $update_array['int_qualification']) {
                                        if (file_exists(actionsClass::URL . "/lsuk_system/file_folder/int_qualification/" . $update_array['int_qualification'])) {
                                            $mail->AddAttachment(actionsClass::URL . "/lsuk_system/file_folder/int_qualification/" . $update_array['int_qualification'], "Translation Qualification Document");
                                        }
                                    }
                                    if ($update_array["nin"]) {
                                        if (file_exists(actionsClass::URL . "/lsuk_system/file_folder/nin/" . $update_array['nin'])) {
                                            $mail->AddAttachment(actionsClass::URL . "/lsuk_system/file_folder/nin/" . $update_array["nin"], "NI / UTR Attachment");
                                        }
                                    }
                                    if ($mail->send()) {
                                        unset($_SESSION['verified_otp']);
                                        $mail->ClearAllRecipients();
                                        $mail->clearAttachments();
                                        if (!empty($email)) {
                                            $mail->addAddress($email);
                                            $mail->addReplyTo(setupEmail::HR_EMAIL, setupEmail::FROM_NAME);
                                            $mail->isHTML(true);
                                            $mail->Subject = "LSUK account registration notification";
                                            $mail->Body    = "Hello " . $first_name . " " . $last_name . ",<br>
                                                We have received your below details for your account registration request.<br>
                                                We will approve your account since we verify all your details.<br>
                                                You can then login to your account at LSUK using below credentials:<br>
                                                <table>
                                                <tbody>
                                                <tr><td style='border: 1px solid black;padding:5px;'>Username/Email:</td>
                                                <td style='border:1px solid black;padding:5px'><h3>" . $email . "</h3></td>
                                                </tr>
                                                <tr><td style='border: 1px solid black;padding:5px;'>Password:</td>
                                                <td style='border:1px solid black;padding:5px'><h3>" . $new_password . "</h3></td>
                                                </tr>
                                                </tbody></table><br>
                                                <b><a style='text-decoration: none;font-size: 16px;border: 1px solid;padding: 4px;border-radius: 4px;background: #618cd6;color: white;' href='https://lsuk.org/login.php'>LOGIN TO LSUK NOW</a></b><br>
                                                If you want to change your password you can use this link <a href='https://lsuk.org/update_password.php'>HERE</a><br>
                                                Here are your submitted details:<br>
                                                " . $message . "
                                                <br>Thank you<br>
                                                Kindest regards,<br>
                                                LSUK Admin Team<br><br>
                                                <span style='color: #2f5496;'>Working hours:<br>
                                                Monday, Tuesday 9AM – 1PM<br>
                                                Thursday and Friday 9AM - 5PM</span>
                                                <br><br>
                                                <span style='color: #002060;'><b><i>Language Services UK Limited<br>
                                                M/O Association of Translation Companies<br>
                                                M/O Institute of Translation and Interpreting<br>
                                                Phone: 01173290610     07915177068 – 0333 7005785<br>
                                                Fax: 0333 800 5785<br>
                                                Email: INFO@LSUK.ORG</i></b><br><br></span>
                                                <small>This message contains confidential information and is intended only for the individual named. If you are not the intended recipient you are notified that disclosing, copying, distributing or taking any action in reliance on the contents of this information is strictly prohibited. If you are not the intended recipient, please notify the sender immediately by reply e-mail and delete this message instantly. Computer viruses can be transmitted via email. he recipient should check this email and any attachments for the presence of viruses. The company accepts no liability for any damage caused by any virus transmitted by this email or for any errors or omissions in the contents of this message, which arise as a result of e-mail transmission. E-mail transmission cannot be guaranteed to be secure or error-free as information could be intercepted, corrupted, lost, destroyed, arrive late or incomplete, or contain viruses. No employee or agent is authorized to conclude any binding agreement on behalf of LanguageServicesUK Limited with another party by email without express written confirmation by Director. Any views or opinions presented in this email are solely those of the author and do not necessarily represent those of the company. Employees of the company are expressly required not to make defamatory statements and not to infringe or authorize any infringement of copyright or any other legal right by email communications. Any such communication is contrary to company policy and outside the scope of the employment of the individual concerned. The company will not accept any liability in respect of such communication, and the employee responsible will be personally liable for any damages or other liability arising. LSUK Limited  or Language Services UK Limited are trading names of LanguageServicesUK Limited – registered in England and Wales (7760366) to provide Interpreting and Translation Services.<small>";
                                            $mail->send();
                                            $mail->ClearAllRecipients();
                                            $mail->clearAttachments();
                                        }
                                        if (isset($_POST['referee_permission'])) {
                                            $ref_subject = "Reference confirmation of interpreter profile at LSUK";
                                            if (!empty($ref_email1)) {
                                                $mail->addAddress($ref_email1);
                                                $mail->addReplyTo(setupEmail::HR_EMAIL, setupEmail::FROM_NAME);
                                                $mail->isHTML(true);
                                                $mail->Subject = $ref_subject;
                                                $mail->Body    = $ref_message1;
                                                $mail->send();
                                                $mail->ClearAllRecipients();
                                            }
                                            if (!empty($ref_email2)) {
                                                $mail->addAddress($ref_email2);
                                                $mail->addReplyTo(setupEmail::HR_EMAIL, setupEmail::FROM_NAME);
                                                $mail->isHTML(true);
                                                $mail->Subject = $ref_subject;
                                                $mail->Body    = $ref_message2;
                                                $mail->send();
                                                $mail->ClearAllRecipients();
                                                $mail->clearAttachments();
                                            }
                                        }
                                        $_SESSION['returned_message'] = '<div class="alert alert-success alert-dismissible col-md-6 col-md-offset-3 text-center">
                                            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times; </a>
                                            <i class="glyphicon glyphicon-check"></i> Success: Your interpreter registration request has been sent successfully.<br>
                                            We will respond within 24 hours.Thank you
                                            </div>';
                                    } else {
                                        $_SESSION['returned_message'] = '<div class="alert alert-danger alert-dismissible col-md-6 col-md-offset-3 text-center">
                                            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times; </a>
                                            <i class="glyphicon glyphicon-check"></i> Failed: Failed to submit your interpreter registration application ! Please try again later
                                            </div>';
                                    }
                                } catch (Exception $e) {
                                    $_SESSION['returned_message'] = '<div class="alert alert-danger alert-dismissible col-md-6 col-md-offset-3 text-center">
                                        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times; </a>
                                        <i class="glyphicon glyphicon-check"></i> Failed sending your email! Please contact LSUK support. Thank you
                                        </div>';
                                }
                            } else {
                                $_SESSION['returned_message'] = '<div class="alert alert-danger alert-dismissible col-md-6 col-md-offset-3 text-center">
                                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times; </a>
                                    <i class="glyphicon glyphicon-check"></i> Invalid Data Submitted: Please enter valid fields and submit interpreter registration form again. Thank you
                                    </div>';
                            }        
                        } else {
                            $_SESSION['returned_message'] = '<div class="alert alert-danger alert-dismissible col-md-6 col-md-offset-3 text-center">
                                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times; </a>
                                <i class="glyphicon glyphicon-exclamation-circle"></i> Email Verification Failed: Please verify your email OTP and submit interpreter registration form again. Thank you
                                </div>';
                        }
                    } else {
                        $_SESSION['returned_message'] = '<div class="alert alert-danger alert-dismissible col-md-6 col-md-offset-3 text-center">
                            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times; </a>
                            <i class="glyphicon glyphicon-exclamation-circle"></i> Invalid Email: Please add a valid email and try again. Thank you
                            </div>';
                    }
                }
            } else {
                $_SESSION['returned_message'] = '<div class="alert alert-danger alert-dismissible col-md-6 col-md-offset-3 text-center">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times; </a>
                    <i class="glyphicon glyphicon-exclamation-circle"></i> Sorry! It took longer to submit your request. Please fill your form & try again. Thank you
                    </div>';
            }
        } else {
            $_SESSION['returned_message'] = '<div class="alert alert-danger alert-dismissible col-md-6 col-md-offset-3 text-center">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times; </a>
                    <i class="glyphicon glyphicon-exclamation-circle"></i> Captcha Failed: Please verify your Captcha and try again. Thank you
                    </div>';
        }
    } else {
        $_SESSION['returned_message'] = '<div class="alert alert-danger alert-dismissible col-md-6 col-md-offset-3 text-center">
                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times; </a>
                <i class="glyphicon glyphicon-exclamation-circle"></i> Captcha Failed: Please verify your Captcha and try again. Thank you
                </div>';
    }
    unset($_SESSION["signin_token"]);
    unset($_SESSION["signin_token_expiry"]);
    header('Location: ../interp_reg.php');
}

if (isset($_POST['send_otp'])) {
    $email_address = strtolower(trim(str_replace(" ", "", $_POST['send_otp'])));
    $response = array("status" => 0, "message" => '<div class="alert alert-danger alert-dismissible show" role="alert">Invalid Email Address: Failed to send OTP to <b>' . $email_address . '</b>! Please try valid Email Address</div>');
    if (!empty($email_address)) {
        $get_account = $obj->read_specific("*", "interpreter_reg", "email='" . $obj->con->real_escape_string($email_address) . "'");
        if ($get_account['id']) {
            $response['message'] = '<div class="alert alert-danger alert-dismissible show" role="alert">This email address is already registered. Please login to interpreter portal or Use different email address!</div>';
        } else {
            $generated_otp = rand(1000, 9999);
            try {
                $mail->SMTPDebug = 0;
                $mail->isSMTP();
                $mail->Host = setupEmail::EMAIL_HOST;
                $mail->SMTPAuth   = true;
                $mail->Username   = setupEmail::HR_EMAIL;
                $mail->Password   = setupEmail::HR_PASSWORD;
                $mail->SMTPSecure = setupEmail::SECURE_TYPE;
                $mail->Port       = setupEmail::SENDING_PORT;
                $mail->setFrom(setupEmail::HR_EMAIL, setupEmail::FROM_NAME);
                $mail->addAddress($email_address);
                $mail->addReplyTo(setupEmail::HR_EMAIL, setupEmail::FROM_NAME);
                $mail->isHTML(true);
                $mail->Subject = 'LSUK Registration OTP';
                $mail->Body = "Dear Interpreter,<br>Please use below OTP code to complete your registration:<br>
                <h1>$generated_otp</h1><br><p>Feel free to reach our support if there is any issue.<br>
                Thank you.<br>Regards<br>LSUK Admin Team</p>";
                if ($mail->send()) {
                    $mail_status = true;
                    $mail->ClearAllRecipients();
                }
            } catch (Exception $e) {
                $response['message'] = '<div class="alert alert-danger alert-dismissible show" role="alert">OTP sending failed: Failed to send OTP to <b>' . $email_address . '</b>! Please try valid Email Address</div>';
            }
            if ($mail_status) {
                $_SESSION['new_otp'] = $generated_otp;
                $response['status'] = 1;
                $response['message'] = '<div class="alert alert-success alert-dismissible show" role="alert"><i class="bi bi-check-circle"></i> OTP Sent: OTP has been successfully sent to email: ' . $email_address . ' Please verify to proceed';
            }
        }
    }
    echo json_encode($response);
}

if (isset($_POST['verify_otp'])) {
    $entered_otp = trim(str_replace(" ", "", $_POST['verify_otp']));
    $email_address = strtolower(trim(str_replace(" ", "", $_POST['email'])));
    $response = array("status" => 0, "message" => '<div class="alert alert-danger alert-dismissible show" role="alert">Invalid OTP Entered: You have entered an invalid OTP for verification. Please try valid OTP</div>');
    if (!empty($entered_otp)) {
        if (!empty($email_address)) {
            $get_account = $obj->read_specific("*", "interpreter_reg", "email='" . $obj->con->real_escape_string($email_address) . "'");
            if ($get_account['id']) {
                $response['message'] = '<div class="alert alert-warning alert-dismissible show" role="alert">This email address is already registered. Please login to interpreter portal or Use different email address!</div>';
            } else {
                if (is_numeric($entered_otp) && $_SESSION['new_otp'] == $entered_otp) {
                    unset($_SESSION['new_otp']);
                    $_SESSION['verified_otp'] = $email_address;
                    $response['status'] = 1;
                    $response['message'] = '<div class="alert alert-success alert-dismissible show" role="alert">Success! Your OTP code has been verified successfully. Please proceed to next step. Thank you</div>';
                }
            }
        } else {
            $response['message'] = '<div class="alert alert-danger alert-dismissible show" role="alert">Invalid Email Address: You must provide an email address for interpreter registration!</div>';
        }
    }
    echo json_encode($response);
}
