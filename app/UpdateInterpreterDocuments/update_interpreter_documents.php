<?php
include '../action.php';
/* Section for updating missing documents from app starts */
if (isset($_POST["update_work_evidence"])) {
    $json = (object) null;
    $json->ap_status = "failed";
    // error_reporting(E_ALL);
    if (isset($_POST['ap_user_id']) && !empty($_POST['ap_user_id'])) {
        $update_array = array("uk_citizen" => 0);
        // Check if file is selected then remove previous file from folder and put new one
        if (isset($_FILES['file_name']) && !empty($_FILES['file_name']['name'])) {
            $target_dir = "../../lsuk_system/file_folder/issue_expiry_docs/";
            $tmp_file_name = $_FILES["file_name"]["tmp_name"];
            $new_file_name = round(microtime(true)) . "." . strtolower(end(explode(".", $_FILES["file_name"]["name"])));
            $get_old_file = $obj->read_specific('work_evid_file', 'interpreter_reg', 'id=' . $_POST['ap_user_id'])['work_evid_file'];
            if (!empty($get_old_file) && file_exists($target_dir . $get_old_file)) {
                unlink($target_dir . $get_old_file);
            }
            if (file_put_contents($target_dir . $new_file_name, file_get_contents($tmp_file_name))) {
                $update_array['work_evid_file'] = $new_file_name;
            }
        }
        // update issue date
        if (isset($_POST['work_evid_issue_date']) && !empty($_POST['work_evid_issue_date'])) {
            $update_array['work_evid_issue_date'] = date('Y-m-d', strtotime($_POST['work_evid_issue_date']));
        }
        if (isset($_POST['work_evid_expiry_date']) && !empty($_POST['work_evid_expiry_date'])) {
            $update_array['work_evid_expiry_date'] = date('Y-m-d', strtotime($_POST['work_evid_expiry_date']));
        }
        $done = $obj->update('interpreter_reg', $update_array, 'id=' . $_POST['ap_user_id']);
        if ($done) {
            $json->ap_status = "success";
            $json->msg = "Work evidence details have been updated successfully. Thank you";
        }
    } else {
        $json->msg = "not_logged_in";
        $json->ap_status = "failed";
    }
    header('Content-Type: application/json');
    echo json_encode($json, JSON_UNESCAPED_UNICODE);
}

if (isset($_POST["update_application_form"])) {
    $json = (object) null;
    $json->ap_status = "failed";
    if (isset($_POST['ap_user_id']) && !empty($_POST['ap_user_id'])) {
        // Check if file is selected then remove previous file from folder and put new one
        if (isset($_FILES['file_name']) && !empty($_FILES['file_name']['name'])) {
            $target_dir = "../../lsuk_system/file_folder/applicationForm/";
            $tmp_file_name = $_FILES["file_name"]["tmp_name"];
            $new_file_name = round(microtime(true)) . "." . strtolower(end(explode(".", $_FILES["file_name"]["name"])));
            $get_old_file = $obj->read_specific('applicationForm_file', 'interpreter_reg', 'id=' . $_POST['ap_user_id'])['applicationForm_file'];
            if (!empty($get_old_file) && file_exists($target_dir . $get_old_file)) {
                unlink($target_dir . $get_old_file);
            }
            if (file_put_contents($target_dir . $new_file_name, file_get_contents($tmp_file_name))) {
                $done = $obj->update("interpreter_reg", array('applicationForm_file' => $new_file_name, 'applicationForm' => 'Soft Copy'), "id=" . $_POST['ap_user_id']);
                if ($done) {
                    $json->ap_status = "success";
                }
            }
        }
    } else {
        $json->msg = "not_logged_in";
        $json->ap_status = "failed";
    }
    header('Content-Type: application/json');
    echo json_encode($json, JSON_UNESCAPED_UNICODE);
}

if (isset($_POST["update_agreement"])) {
    $json = (object) null;
    $json->ap_status = "failed";
    if (isset($_POST['ap_user_id']) && !empty($_POST['ap_user_id'])) {
        // Check if file is selected then remove previous file from folder and put new one
        if (isset($_FILES['file_name']) && !empty($_FILES['file_name']['name'])) {
            $target_dir = "../../lsuk_system/file_folder/agreement/";
            $tmp_file_name = $_FILES["file_name"]["tmp_name"];
            $new_file_name = round(microtime(true)) . "." . strtolower(end(explode(".", $_FILES["file_name"]["name"])));
            $get_old_file = $obj->read_specific('agreement_file', 'interpreter_reg', 'id=' . $_POST['ap_user_id'])['agreement_file'];
            if (!empty($get_old_file) && file_exists($target_dir . $get_old_file)) {
                unlink($target_dir . $get_old_file);
            }
            if (file_put_contents($target_dir . $new_file_name, file_get_contents($tmp_file_name))) {
                $done = $obj->update("interpreter_reg", array('agreement_file' => $new_file_name), "id=" . $_POST['ap_user_id']);
                if ($done) {
                    $json->ap_status = "success";
                }
            }
        }
    } else {
        $json->msg = "not_logged_in";
        $json->ap_status = "failed";
    }
    header('Content-Type: application/json');
    echo json_encode($json, JSON_UNESCAPED_UNICODE);
}

if (isset($_POST["update_passport"])) {
    $json = (object) null;
    $json->ap_status = "failed";
    // error_reporting(E_ALL);
    if (isset($_POST['ap_user_id']) && !empty($_POST['ap_user_id'])) {
        $update_array = array("uk_citizen" => 1);
        // Check if file is selected then remove previous file from folder and put new one
        if (isset($_FILES['file_name']) && !empty($_FILES['file_name']['name'])) {
            $target_dir = "../../lsuk_system/file_folder/issue_expiry_docs/";
            $tmp_file_name = $_FILES["file_name"]["tmp_name"];
            $new_file_name = round(microtime(true)) . "." . strtolower(end(explode(".", $_FILES["file_name"]["name"])));
            $get_old_file = $obj->read_specific('id_doc_file', 'interpreter_reg', 'id=' . $_POST['ap_user_id'])['id_doc_file'];
            if (!empty($get_old_file) && file_exists($target_dir . $get_old_file)) {
                unlink($target_dir . $get_old_file);
            }
            if (file_put_contents($target_dir . $new_file_name, file_get_contents($tmp_file_name))) {
                $update_array['id_doc_file'] = $new_file_name;
            }
        }
        if (isset($_POST['id_doc_no']) && !empty($_POST['id_doc_no'])) {
            $update_array['id_doc_no'] = $_POST['id_doc_no'];
        }
        // update issue & expiry date
        if (isset($_POST['id_doc_issue_date']) && !empty($_POST['id_doc_issue_date'])) {
            $update_array['id_doc_issue_date'] = date('Y-m-d', strtotime($_POST['id_doc_issue_date']));
        }
        if (isset($_POST['id_doc_expiry_date']) && !empty($_POST['id_doc_expiry_date'])) {
            $update_array['id_doc_expiry_date'] = date('Y-m-d', strtotime($_POST['id_doc_expiry_date']));
        }
        $done = $obj->update("interpreter_reg", $update_array, "id=" . $_POST['ap_user_id']);
        if ($done) {
            $json->ap_status = "success";
        }
    } else {
        $json->msg = "not_logged_in";
        $json->ap_status = "failed";
    }
    header('Content-Type: application/json');
    echo json_encode($json, JSON_UNESCAPED_UNICODE);
}

if (isset($_POST["update_dps"])) {
    $json = (object) null;
    $json->ap_status = "failed";
    // error_reporting(E_ALL);
    if (isset($_POST['ap_user_id']) && !empty($_POST['ap_user_id'])) {
        // Check if file is selected then remove previous file from folder and put new one
        if (isset($_FILES['file_name']) && !empty($_FILES['file_name']['name'])) {
            $target_dir = "../../lsuk_system/file_folder/dps/";
            $tmp_file_name = $_FILES["file_name"]["tmp_name"];
            $new_file_name = round(microtime(true)) . "." . strtolower(end(explode(".", $_FILES["file_name"]["name"])));
            $get_old_file = $obj->read_specific('dps', 'interpreter_reg', 'id=' . $_POST['ap_user_id'])['dps'];
            if (!empty($get_old_file) && file_exists($target_dir . $get_old_file)) {
                unlink($target_dir . $get_old_file);
            }
            if (file_put_contents($target_dir . $new_file_name, file_get_contents($tmp_file_name))) {
                $done = $obj->update("interpreter_reg", array('dps' => $new_file_name), "id=" . $_POST['ap_user_id']);
                if ($done) {
                    $json->ap_status = "success";
                }
            }
        }
    } else {
        $json->msg = "not_logged_in";
        $json->ap_status = "failed";
    }
    header('Content-Type: application/json');
    echo json_encode($json, JSON_UNESCAPED_UNICODE);
}

if (isset($_POST["update_translation_qualification"])) {
    $json = (object) null;
    $json->ap_status = "failed";
    if (isset($_POST['ap_user_id']) && !empty($_POST['ap_user_id'])) {
        // Check if file is selected then remove previous file from folder and put new one
        if (isset($_FILES['file_name']) && !empty($_FILES['file_name']['name'])) {
            $target_dir = "../../lsuk_system/file_folder/int_qualification/";
            $tmp_file_name = $_FILES["file_name"]["tmp_name"];
            $new_file_name = round(microtime(true)) . "." . strtolower(end(explode(".", $_FILES["file_name"]["name"])));
            $get_old_file = $obj->read_specific('int_qualification', 'interpreter_reg', 'id=' . $_POST['ap_user_id'])['int_qualification'];
            if (!empty($get_old_file) && file_exists($target_dir . $get_old_file)) {
                unlink($target_dir . $get_old_file);
            }
            if (file_put_contents($target_dir . $new_file_name, file_get_contents($tmp_file_name))) {
                $done = $obj->update("interpreter_reg", array('int_qualification' => $new_file_name), "id=" . $_POST['ap_user_id']);
                if ($done) {
                    $json->ap_status = "success";
                }
            }
        }
    } else {
        $json->msg = "not_logged_in";
        $json->ap_status = "failed";
    }
    header('Content-Type: application/json');
    echo json_encode($json, JSON_UNESCAPED_UNICODE);
}

if (isset($_POST["update_nin"])) {
    $json = (object) null;
    $json->ap_status = "failed";
    // error_reporting(E_ALL);
    if (isset($_POST['ap_user_id']) && !empty($_POST['ap_user_id'])) {
        // Check if file is selected then remove previous file from folder and put new one
        if (isset($_FILES['file_name']) && !empty($_FILES['file_name']['name'])) {
            $target_dir = "../../lsuk_system/file_folder/nin/";
            $tmp_file_name = $_FILES["file_name"]["tmp_name"];
            $new_file_name = round(microtime(true)) . "." . strtolower(end(explode(".", $_FILES["file_name"]["name"])));
            $get_old_file = $obj->read_specific('nin', 'interpreter_reg', 'id=' . $_POST['ap_user_id'])['nin'];
            if (!empty($get_old_file) && file_exists($target_dir . $get_old_file)) {
                unlink($target_dir . $get_old_file);
            }
            if (file_put_contents($target_dir . $new_file_name, file_get_contents($tmp_file_name))) {
                $done = $obj->update("interpreter_reg", array('nin' => $new_file_name), "id=" . $_POST['ap_user_id']);
                if ($done) {
                    $json->ap_status = "success";
                }
            }
        }
        if (isset($_POST['ni_number']) && !empty($_POST['ni_number'])) {
            $obj->update('interpreter_reg', ['ni' => $_POST['ni_number']], 'id=' . $_POST['ap_user_id']);
        }
    } else {
        $json->msg = "not_logged_in";
        $json->ap_status = "failed";
    }
    header('Content-Type: application/json');
    echo json_encode($json, JSON_UNESCAPED_UNICODE);
}
if (isset($_POST["update_dbs"])) {
    $json = (object) null;
    $json->ap_status = "failed";
    // error_reporting(E_ALL);
    if (isset($_POST['ap_user_id']) && !empty($_POST['ap_user_id'])) {
        //check if dbs is not on auto renewal
        if (isset($_POST['is_auto_renewal']) && empty($_POST['is_auto_renewal'])) {
            if (isset($_FILES['file_name']) && !empty($_FILES['file_name']['name'])) {
                $target_dir = "../../lsuk_system/file_folder/issue_expiry_docs/";
                $tmp_file_name = $_FILES["file_name"]["tmp_name"];
                $new_file_name = round(microtime(true)) . "." . strtolower(end(explode(".", $_FILES["file_name"]["name"])));
                $get_old_file = $obj->read_specific('dbs_file', 'interpreter_reg', 'id=' . $_POST['ap_user_id'])['dbs_file'];
                if (!empty($get_old_file) && file_exists($target_dir . $get_old_file)) {
                    unlink($target_dir . $get_old_file);
                }
                if (file_put_contents($target_dir . $new_file_name, file_get_contents($tmp_file_name))) {
                    $done = $obj->update("interpreter_reg", array('dbs_file' => $new_file_name, 'is_dbs_auto' => 0, 'dbs_no' => $_POST['dbs_no'], 'dbs_issue_date' => date('Y-m-d', strtotime($_POST['dbs_issue_date'])), 'dbs_expiry_date' => date('Y-m-d', strtotime($_POST['dbs_expiry_date']))), "id=" . $_POST['ap_user_id']);
                    if ($done) {
                        $json->ap_status = "success";
                    }
                }
            }
        } else {
            $obj->update('interpreter_reg', ['is_dbs_auto' => 1, 'dbs_no' => $_POST['dbs_no']], 'id=' . $_POST['ap_user_id']);
        }
    } else {
        $json->msg = "not_logged_in";
        $json->ap_status = "failed";
    }
    header('Content-Type: application/json');
    echo json_encode($json, JSON_UNESCAPED_UNICODE);
}

if (isset($_POST["update_nrcpd"])) {
    $json = (object) null;
    $json->ap_status = "failed";
    // error_reporting(E_ALL);
    if (isset($_POST['ap_user_id']) && !empty($_POST['ap_user_id'])) {
        // Check if file is selected then remove previous file from folder and put new one
        if (isset($_FILES['file_name']) && !empty($_FILES['file_name']['name'])) {
            $target_dir = "../../lsuk_system/file_folder/nrcpd_file/";
            $tmp_file_name = $_FILES["file_name"]["tmp_name"];
            $new_file_name = round(microtime(true)) . "." . strtolower(end(explode(".", $_FILES["file_name"]["name"])));
            $get_old_file = $obj->read_specific('nrcpd_file', 'interpreter_reg', 'id=' . $_POST['ap_user_id'])['nrcpd_file'];
            if (!empty($get_old_file) && file_exists($target_dir . $get_old_file)) {
                unlink($target_dir . $get_old_file);
            }
            if (file_put_contents($target_dir . $new_file_name, file_get_contents($tmp_file_name))) {
                $done = $obj->update("interpreter_reg", array('nrcpd_file' => $new_file_name), "id=" . $_POST['ap_user_id']);
                if ($done) {
                    $json->ap_status = "success";
                }
            }
        }
    } else {
        $json->msg = "not_logged_in";
        $json->ap_status = "failed";
    }
    header('Content-Type: application/json');
    echo json_encode($json, JSON_UNESCAPED_UNICODE);
}

if (isset($_POST["update_asli"])) {
    $json = (object) null;
    // error_reporting(E_ALL);
    if (isset($_POST['ap_user_id']) && !empty($_POST['ap_user_id'])) {
        $json->ap_status = "success";
        if (isset($_POST['asli_number']) && !empty($_POST['asli_number'])) {
            $obj->update('interpreter_reg', ['asli_number' => $_POST['asli_number'], 'is_asli' => 1], 'id=' . $_POST['ap_user_id']);
        }
    } else {
        $json->msg = "not_logged_in";
        $json->ap_status = "failed";
    }
    header('Content-Type: application/json');
    echo json_encode($json, JSON_UNESCAPED_UNICODE);
}

if (isset($_POST["update_nrpsi"])) {
    $json = (object) null;
    // error_reporting(E_ALL);
    if (isset($_POST['ap_user_id']) && !empty($_POST['ap_user_id'])) {
        $json->ap_status = "success";
        if (isset($_POST['nrpsi_number']) && !empty($_POST['nrpsi_number'])) {
            $obj->update('interpreter_reg', ['nrpsi_number' => $_POST['nrpsi_number'], 'is_nrpsi' => 1], 'id=' . $_POST['ap_user_id']);
        }
    } else {
        $json->msg = "not_logged_in";
        $json->ap_status = "failed";
    }
    header('Content-Type: application/json');
    echo json_encode($json, JSON_UNESCAPED_UNICODE);
}