<?php if (session_id() == '' || !isset($_SESSION)) {
  session_start();
}
include 'source/db.php';
include 'source/class.php';
$table = 'interpreter_reg';
function decryptAuthCode($authcode) {
    $key = "12345678901234567890123456789012"; // 32 chars
    $iv  = str_repeat("\0", 16);
    $ciphertext = base64_decode($authcode);

    return openssl_decrypt(
        $ciphertext,
        'AES-256-CBC',
        $key,
        OPENSSL_RAW_DATA,
        $iv
    );
}

if (isset($_SESSION['web_userId'])) {
    // ✅ Session has priority
    $edit_id = $_SESSION['web_userId'];

} elseif (!empty($_GET['authcode']) && !empty($_GET['org_code'])) {
    $decoded = decryptAuthCode($_GET['authcode']);
    // ✅ check if decrypted is numeric and matches org_code
    if (ctype_digit($decoded) && $decoded === $_GET['org_code']) {
        $edit_id = $decoded;
        //$_SESSION['web_userId'] = $edit_id; // set session for later use
    } else {
        echo "Authentication Failed (Invalid Token)";
        die();
    }

} else {
    echo "Authentication Failed (Missing Keys)";
    die();
}

$row = $acttObj->read_specific("*", $table, "id=" . $edit_id);
$agreement_validity = $acttObj->read_specific(
    "*,DATE(created_date) as date_group",
    "audit_logs",
    "table_name='email_format' AND record_id=41 ORDER BY id DESC LIMIT 1"
)['date_group'];$qint = '';
$reg_num = '';
if ($row['is_nrpsi'] != 0) {
  $qint = 'nrpsi';
  $column_label = "NRPSI.No";
} elseif ($row['is_ciol'] != 0) {
  $qint = 'ciol';
  $column_label = "CIOL.No";
} elseif ($row['is_iti'] != 0) {
  $qint = 'iti';
  $column_label = "ITI.No";
} elseif ($row['is_asli'] != 0) {
  $qint = 'asli';
  $column_label = "ASLI.No";
}
$reg_num = $qint . '_number';
// validation runs before submit logic
if (isset($_POST['submit'])) {
    $signature_date     = $row['signature_date'];   // from DB
    $agreement_validity = $agreement_validity;      // from query

    if (strtotime($signature_date) < strtotime($agreement_validity) && empty($_POST['disclaimer'])) {
        echo "<script>alert('You must accept the updated agreement before continuing.');</script>";
        unset($_POST['submit']); // cancel the submit flag
    }
}
if (isset($_POST['submit'])) {

  $update_array = array(
    "name" => trim($_POST['name']),
    "code" => "id-" . $edit_id,
    "email" => trim($_POST['email']),
    "signature_name" => $_POST['name'],
    "signature_date" => date("Y-m-d H:i:s"),
    "agreement" => 'Soft Copy',
    "contactNo" => $_POST['contactNo'],
    "email2" => trim($_POST['email2']),
    "contactNo2" => $_POST['contactNo2'],
    "bnakName" => $_POST['bnakName'],
    "acName" => $_POST['acName'],
    "acntCode" => str_replace("-", "", $_POST['acntCode']),
    "acNo" => $_POST['acNo'],
    "ni" => @$_POST['ni1'] . @$_POST['ni2'] . @$_POST['ni3'] . @$_POST['ni4'] . @$_POST['ni5'] . @$_POST['ni6'] . @$_POST['ni7'] . @$_POST['ni8'] . @$_POST['ni9'] . @$_POST['ni10'],
    "is_ni" => $_POST['is_ni'],
    "dob" => $_POST['dob'],
    "gender" => $_POST['gender'],
    "uk_citizen" => $_POST['uk_citizen'],
    "country" => $_POST['selected_country'],
    "city" => $_POST['city'],
    "postCode" => $_POST['postCode'],
    "buildingName" => $_POST['buildingName'],
    "line1" => $_POST['line1'],
    "line2" => $_POST['line2'],
    "line3" => $_POST['line3'],
    "edited_by" => $_SESSION['UserName'],
    "edited_date" => date("Y-m-d H:i:s"),
    "country_of_origin" => $_POST['country_of_origin'],
  );
  if (empty($_POST['password'])) {
    $new_password = '@' . strtok($_POST['name'], " ") . substr(str_shuffle('0123456789abcdwxyz'), 0, 5) . substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 3);
    $update_array['password'] = $new_password;
  }
    if (isset($_POST['interp'])) {
    if (isset($_POST['is_dbs_auto'])) {
      if ($_POST['dbs_auto_number']) {
        $update_array['interp'] = "Yes";
        $update_array['crbDbs'] = "Soft Copy";
        $update_array['is_dbs_auto'] = 1;
        $update_array['dbs_checked'] = 0; //0 for Yes
        $update_array['dbs_auto_number'] = trim($_POST['dbs_auto_number']);
      }
    } else {
      $update_array['is_dbs_auto'] = 0;
      //DBS Document
      if ($_FILES["dbs_file"]["name"] != NULL || $_POST['dbs_no']) {
        $update_array['interp'] = "Yes";
        $update_array['crbDbs'] = "Soft Copy";
        $update_array['dbs_checked'] = 0; //0 for Yes
        $update_array['dbs_no'] = trim($_POST['dbs_no']);
        $update_array['dbs_issue_date'] = $_POST['dbs_issue_date'];
        $update_array['dbs_expiry_date'] = $_POST['dbs_expiry_date'];
        if ($_FILES["dbs_file"]["name"] != NULL) {
          $update_array['dbs_file'] = $obj->upload_file("file_folder/issue_expiry_docs", $_FILES["dbs_file"]["name"], $_FILES["dbs_file"]["type"], $_FILES["dbs_file"]["tmp_name"], round(microtime(true)));
          if ($row['dbs_file'] && file_exists('file_folder/issue_expiry_docs/' . $row['dbs_file'])) {
            unlink('file_folder/issue_expiry_docs/' . $row['dbs_file']);
          }
        }
      } else {
        $update_array['dbs_checked'] = 1; //1 for No
        $update_array['interp'] = "No";
      }
    }
  } else {
    $update_array['interp'] = "No";
  }
  if ($qint) {
    $update_array[$reg_num] = $_POST[$reg_num];
  }
  if ($_POST['uk_citizen'] == 1) {
    //Identity / passport document
    if ($_FILES["passport_file"]["name"] != NULL || $_POST['passport_number']) {
      $update_array['uk_citizen'] = 1;
      $update_array['identityDocument'] = "Soft Copy";
      $update_array['id_doc_no'] = trim($_POST['passport_number']);
      $update_array['id_doc_issue_date'] = $_POST['passport_issue_date'];
      $update_array['id_doc_expiry_date'] = $_POST['passport_expiry_date'];
      if ($_FILES["passport_file"]["name"] != NULL) {
        $update_array['id_doc_file'] = $acttObj->upload_file("lsuk_system/file_folder/issue_expiry_docs", $_FILES["passport_file"]["name"], $_FILES["passport_file"]["type"], $_FILES["passport_file"]["tmp_name"], round(microtime(true)));
        if ($row['id_doc_file'] && file_exists('file_folder/issue_expiry_docs/' . $row['id_doc_file'])) {
          //unlink('file_folder/issue_expiry_docs/' . $row['id_doc_file']);
        }
      }
    } else {
      $update_array['uk_citizen'] = 0;
      $update_array['identityDocument'] = "";
    }
  } else {
    $update_array['uk_citizen'] = 0;
  }
  //Right to work evidence
  if ($update_array['uk_citizen'] == 0) {
    $update_array['work_evid_issue_date'] = $_POST['work_evid_issue_date'];
    $update_array['work_evid_expiry_date'] = $_POST['work_evid_expiry_date'];
    $update_array['right_to_work_no'] = $_POST['right_to_work_no'];

    if (!empty($_FILES["work_evid_file"]["name"]) && $_FILES["work_evid_file"]["name"] != NULL) {
      $update_array['work_evid_file'] = $acttObj->upload_file("lsuk_system/file_folder/issue_expiry_docs", $_FILES["work_evid_file"]["name"], $_FILES["work_evid_file"]["type"], $_FILES["work_evid_file"]["tmp_name"], round(microtime(true)));
     
      if ($row['work_evid_file'] && file_exists('file_folder/issue_expiry_docs/' . $row['work_evid_file'])) {
        //unlink('file_folder/issue_expiry_docs/' . $row['work_evid_file']);
      }
    }

    if (!empty($_FILES["country_of_origin_passport"]["name"])) {
      $update_array['country_of_origin_passport'] = $acttObj->upload_file("file_folder/issue_expiry_docs", $_FILES["country_of_origin_passport"]["name"], $_FILES["country_of_origin_passport"]["type"], $_FILES["country_of_origin_passport"]["tmp_name"], round(microtime(true)));

      if ($row['country_of_origin_passport'] && file_exists('file_folder/issue_expiry_docs/' . $row['country_of_origin_passport'])) {
        unlink('file_folder/issue_expiry_docs/' . $row['country_of_origin_passport']);
      }
    }
  }
  // if ($_FILES["file"]["name"] != NULL) {
  //   unlink('file_folder/interp_photo/' . $row['interp_pix']);
  //   $picName = $acttObj->upload_file("file_folder/interp_photo", $_FILES["file"]["name"], $_FILES["file"]["type"], $_FILES["file"]["tmp_name"], $edit_id);
  //   $update_array['interp_pix'] = $picName;
  // }
  $acttObj->update($table, $update_array, "id=" . $edit_id);
  // $acttObj->new_old_table('hist_' . $table, $table, $edit_id);
  //$acttObj->insert("daily_logs", array("action_id" => 10, "user_id" => $_SESSION['userId'], "details" => "Interpreter ID: " . $edit_id));
  echo "<script>alert('Record of interpreter updated successfully.');</script>"; ?>
 <script>

    window.onload = function() {
        console.log("closing");
        window.opener.location.reload(); // refresh parent
        window.close(); // close child
    };
</script>

<?php }
// Log changes start
$index_mapping = array(
  'Name' => 'name', 'Email' => 'email', 'Email.2' => 'email2', 'Contact' => 'contactNo', 'Contact.2' => 'contactNo2', 'Work.Type' => 'work_type',
  'Bank.Name' => 'bnakName', 'Acc.Name' => 'acName', 'Sortcode' => 'acntCode', 'Acc.Number' => 'acNo', 'NI' => 'ni', 'DOB' => 'dob', 'Reg.Date' => 'reg_date', 'DBS' => 'dbs_checked', 'Gender' => 'gender', 'UK.Citizen' => 'uk_citizen',
  'Country' => 'country', 'City' => 'city', 'PostCode' => 'postCode', 'Building.Name' => 'buildingName', 'Line 1' => 'line1', 'Line 2' => 'line2', 'Line 3' => 'line3', 'Is.F2F' => 'interp', 'Is.Telephone' => 'telep', 'Is.Translation' => 'trans',
  'DBS.Update.Service' => 'is_dbs_auto', 'Auto.DBS.Number' => 'dbs_auto_number', 'DBS.No' => 'dbs_no', 'DBS.Issued' => 'dbs_issue_date', 'DBS.Expiry' => 'dbs_expiry_date', 'Identity.Number' => 'id_doc_no', 'Identity.Doc.Issued' => 'id_doc_issue_date', 
  'Identity.Doc.Expiry' => 'id_doc_expiry_date', 'Evidence.Issued' => 'work_evid_issue_date', 'Evidence.Expiry' => 'work_evid_expiry_date', 'Is.Adhoc?' => 'isAdhoc','country_of_origin' => 'country_of_origin'
);
if ($qint) {
  $index_mapping[$column_label] = $reg_num;
}

$old_values = $new_values = array();
$get_new_data = $acttObj->read_specific("*", "$table", "id=" . $edit_id);

foreach ($index_mapping as $key => $value) {
  if (isset($get_new_data[$value])) {
    $old_values[$key] = $row[$value];
    $new_values[$key] = $get_new_data[$value];
  }
}

// keys to check
$check_keys = [
    'id_doc_file', 'id_doc_expiry_date', 'id_doc_issue_date',
    'work_evid_file', 'right_to_work_no', 'work_evid_issue_date', 'work_evid_expiry_date'
];

$changed_keys = [];

foreach ($check_keys as $key) {
    $oldVal = isset($row[$key]) ? trim((string)$row[$key]) : null;
    $newVal = isset($get_new_data[$key]) ? trim((string)$get_new_data[$key]) : null;

    if ($oldVal !== $newVal) {
        $changed_keys[$key] = [
            'old' => $oldVal,
            'new' => $newVal
        ];
    }
}

// only log if something actually changed
if (!empty($changed_keys)) {

    // decode existing log (if any)
    $existing_log = [];
    if (!empty($row['pending_approvals'])) {
        $decoded = json_decode($row['pending_approvals'], true);
        if (is_array($decoded)) {
            $existing_log = $decoded;
        }
    }

    // create new log entry
    $new_entry = [
        'changed_fields' => $changed_keys,
        'action'         => 'pending', // or approved/rejected
        'action_by'      => $current_user_id ?? null,
        'action_time'    => date('Y-m-d H:i:s'),
        'reason'         => null
    ];

    // append
    $existing_log[] = $new_entry;

    // re-encode
    $update_array['pending_approvals'] = json_encode($existing_log, JSON_PRETTY_PRINT);
    // encode as JSON
    $verification_arr = array();
    $verification_arr ['pending_approvals'] = $update_array['pending_approvals'];
    $acttObj->update($table, $verification_arr, "id=" . $edit_id);
}
$acttObj->log_changes(json_encode($old_values), json_encode($new_values), $edit_id, $table, "update", $_SESSION['userId'], "SELF", "edit_interpreter_account",2);
// Log changes end
$row = $acttObj->read_specific("*", $table, "id=" . $edit_id);
$g_rowTable = $row;
$rowID = $row['id'];
$password = $row['password'];
$name = $row['name'];
$email = $row['email'];
$email2 = $row['email2'];
$contactNo = $row['contactNo'];
$contactNo2 = $row['contactNo2'];
$interp_pix = $row['interp_pix'];
$rph = $row['rph'];
$interp = $row['interp'];
$telep = $row['telep'];
$trans = $row['trans'];
$gender = $row['gender'];
$country = $row['country'];
$country_of_origin = $row['country_of_origin'];
$city = $row['city'];
$address = $row['address'];
$bnakName = $row['bnakName'];
$acName = $row['acName'];
$acntCode = $row['acntCode'];
$acNo = $row['acNo'];
$rpu = $row['rpu'];
$ni = $row['ni'];
$reg_date = $row['reg_date'];
$work_type = $row['work_type'];
$dob = $row['dob'];
$buildingName = $row['buildingName'];
$line1 = $row['line1'];
$line2 = $row['line2'];
$line3 = $row['line3'];
$postCode = $row['postCode'];
$dbs_checked = $row['dbs_checked'];
$uk_citizen = $row['uk_citizen']; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
  <title>Interpreter Registration Form</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <link rel="stylesheet" type="text/css" href="lsuk_system/css/bootstrap.css" />
  <link rel="stylesheet" type="text/css" href="lsuk_system/css/util.css" />
  <?php include 'ajax_uniq_fun.php'; 
  ?>
  <style>
    .ri {
      margin-top: 7px !important;
    }

    .ri .label {
      font-size: 100%;
      padding: .5em 0.6em 0.5em;
    }

    .multiselect {
      min-width: 230px;
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
  </style>
</head>

<body>

  <div class="container-fluid">
    <form action="" method="post" class="register" enctype="multipart/form-data" name="maxform">
      <input type="hidden" name="password" value="<?= $password ?>" />
      <div class="bg-info col-xs-12 form-group">
        <h4>Interpreter Personal Details</h4>
      </div>
      <!--
      <div class="form-group col-md-3 col-sm-6">
        <label class="optional">Interpreter Photo *</label>
        <input class="form-control long" name="file" type="file" id="file" <?= $row['interp_pix'] ? '' : 'required' ?> />
      </div>
      -->
      <div class="form-group col-md-3 col-sm-6">
        <label style="color:red">Please Use LSUK Mobile App to update profile Photo</label>
      </div>
      <div class="form-group col-md-3 col-sm-6">
        <label>Name *</label>
        <input placeholder="Name *" class="form-control valid" name="name" type="text" id="name" required='' value="<?php echo $name; ?>" onBlur="uniqueFun(this.value,'interpreter_reg','name',$(this).attr('id'),'editFlag',<?php echo $rowID; ?> );" tabindex="1" />
      </div>
      <div class="form-group col-md-3 col-sm-6">
        <label>Email Address 1 *</label>
        <input placeholder="Email Address 1 *" class="form-control" name="email" type="text" id="email" required='' onBlur="uniqueFun(this.value,'interpreter_reg','email',$(this).attr('id'),'editFlag',<?php echo $rowID; ?> );" tabindex="3" value="<?php echo $email; ?>" />
      </div>
      <div class="form-group col-md-3 col-sm-6">
        <label>Mobile No *</label>
        <input placeholder="Mobile No *" class="form-control validate_number" name="contactNo" type="text" id="contactNo" required='' tabindex="4" value="<?php echo $contactNo; ?>" />
      </div>
      <div class="form-group col-md-3 col-sm-6">
        <label>Email Address 2</label>
        <input placeholder="Email Address 2" class="form-control" name="email2" type="text" id="email2" onBlur="uniqueFun(this.value,'interpreter_reg','email2',$(this).attr('id'),'editFlag',<?php echo $rowID; ?> );" tabindex="5" value="<?php echo $email2; ?>" />
      </div>
      <div class="form-group col-md-3 col-sm-6">
        <label>Landline *</label>
        <input placeholder="Landline *" class="form-control validate_number" name="contactNo2" type="text" id="contactNo2" tabindex="6" value="<?php echo $contactNo2; ?>" />
      </div>
      <?php if ($qint != '') { ?>
        <div class="form-group col-md-3 col-sm-6">
          <label><?php echo strtoupper($qint); ?> Registration Number</label>
          <input class="form-control" type="text" name="<?php echo $reg_num; ?>" id="<?php echo $reg_num; ?>" value="<?php echo $row[$reg_num]; ?>" required />
        </div>
      <?php } ?>
      <div class="bg-info col-xs-12 form-group">
        <h4>Bank Account Details</h4>
      </div>
      <div class="form-group col-md-3 col-sm-6">
        <label>Bank Name</label>
        <input placeholder="Bank Name" class="form-control" name="bnakName" type="text" id="bnakName" tabindex="9" value="<?php echo $bnakName; ?>" />
      </div>
      <div class="form-group col-md-3 col-sm-6">
        <label>Account Name</label>
        <input placeholder="Account Name" class="form-control" name="acName" type="text" id="acName" tabindex="10" value="<?php echo $acName; ?>" />
      </div>
      <div class="form-group col-md-3 col-sm-6">
        <label>Account Sort Code</label>
        <input placeholder="Account Sort Code" class="form-control" name="acntCode" type="text" id="acntCode" onchange="checkAccountSortCode(this)" oninput="this.value = this.value.replace(/[^0-9-.]/g, '').replace(/(\..*)\./g, '$1');" maxlength="8" minlength="8" tabindex="11" value="<?php echo $acntCode; ?>" />
      </div>
      <div class="form-group col-md-3 col-sm-6">
        <label>Account Number</label>
        <input placeholder="Account Number" class="form-control" name="acNo" type="text" id="acNo" tabindex="13" onchange="checkAccountNumber(this)" oninput="this.value = this.value.replace(/[^\d]/g, '').replace(/(\..*)\./g, '$1');" maxlength="8" minlength="8" value="<?php echo $acNo; ?>" />
      </div>
      <div class="form-group col-sm-6">
        <label class="" for="for-ni">
          <input id="ni" name="is_ni" type="radio" value="NI" <?php echo ($row['is_ni'] == 'NI') ? 'checked' : ''; ?> required /> National Insurance #
        </label>
        <label class="" for="for-ni">
          <input id="utr" name="is_ni" type="radio" value="UTR"  <?php echo ($row['is_ni'] == 'UTR') ? 'checked' : ''; ?> /> UTR
        </label> <br>
        <script type="text/javascript">
          function moveNextPrev(elmnt, event) {
            if (elmnt.value.length === elmnt.maxLength && event.key !== "Backspace") {
              let next = document.querySelector(`[tabindex="${elmnt.tabIndex + 1}"]`);
              if (next) next.focus();
            } 
            else if (elmnt.value.length === 0 && event.key === "Backspace") {
              let prev = document.querySelector(`[tabindex="${elmnt.tabIndex - 1}"]`);
              if (prev) prev.focus();
            }
          }
        </script>
        <?php $ni_ar = ($ni); ?>
        <span class="inlineinput">
          <input name="ni1" type='text' style='display: inline;width: 32px;padding: 8px;' maxlength="1" onkeyup="moveNextPrev(this, event)" tabindex="13" value="<?php echo $ni_ar[0]; ?>" />
        </span>
        <span class="inlineinput">
          <input name="ni2" type='text' style='display: inline;width: 32px;padding: 8px;' maxlength="1" onkeyup="moveNextPrev(this, event)" tabindex="14" value="<?php echo $ni_ar[1]; ?>" />
        </span><span class="inlineinput">
          <input name="ni3" type='text' style='display: inline;width: 32px;padding: 8px;' maxlength="1" onkeyup="moveNextPrev(this, event)" tabindex="15" value="<?php echo $ni_ar[2]; ?>" />
        </span><span class="inlineinput">
          <input name="ni4" type='text' style='display: inline;width: 32px;padding: 8px;' maxlength="1" onkeyup="moveNextPrev(this, event)" tabindex="16" value="<?php echo $ni_ar[3]; ?>" />
        </span><span class="inlineinput">
          <input name="ni5" type='text' style='display: inline;width: 32px;padding: 8px;' maxlength="1" onkeyup="moveNextPrev(this, event)" tabindex="17" value="<?php echo $ni_ar[4]; ?>" />
        </span><span class="inlineinput">
          <input name="ni6" type='text' style='display: inline;width: 32px;padding: 8px;' maxlength="1" onkeyup="moveNextPrev(this, event)" tabindex="18" value="<?php echo $ni_ar[5]; ?>" />
        </span><span class="inlineinput">
          <input name="ni7" type='text' style='display: inline;width: 32px;padding: 8px;' maxlength="1" onkeyup="moveNextPrev(this, event)" tabindex="19" value="<?php echo $ni_ar[6]; ?>" />
        </span><span class="inlineinput">
          <input name="ni8" type='text' style='display: inline;width: 32px;padding: 8px;' maxlength="1" onkeyup="moveNextPrev(this, event)" tabindex="20" value="<?php echo $ni_ar[7]; ?>" />
        </span><span class="inlineinput">
          <input name="ni9" type='text' style='display: inline;width: 32px;padding: 8px;' maxlength="1" onkeyup="moveNextPrev(this, event)" tabindex="21" value="<?php echo $ni_ar[8]; ?>" />
        </span><span class="inlineinput">
          <input name="ni10" type='text' style='display: inline;width: 32px;padding: 8px;' maxlength="1" onkeyup="moveNextPrev(this, event)" tabindex="22" value="<?php echo $ni_ar[9]; ?>" />
        </span>
      </div>
      <div class="bg-info col-xs-12 form-group">
        <h4>Other Information</h4>
      </div>
      <div class="form-group col-md-3 col-sm-6">
        <label>Date of Birth *</label>
        <input class="form-control" required name="dob" type="date" id="dob" tabindex="9" value="<?php echo $dob; ?>" />
      </div>
      <div class="form-group col-md-3 col-sm-6">
        <label class="optional">Gender</label>
        <select name="gender" required class="form-control">
          <option <?= $gender == 'Male' ? 'selected' : '' ?> value="Male">Male</option>
          <option <?= $gender == 'Female' ? 'selected' : '' ?> value="Female">Female</option>
          <option <?= $gender == 'No Preference' ? 'selected' : '' ?> value="No Preference">No Preference</option>
        </select>
      </div>
      <div class="form-group col-md-3 col-sm-6">
        <label class="optional">DBS checked ?</label>
        <select name="dbs_checked" required class="form-control">
          <option <?= $dbs_checked == 0 ? 'selected' : '' ?> value="0">Yes</option>
          <option <?= $dbs_checked == 1 ? 'selected' : '' ?> value="1">No</option>
        </select>
      </div>
      <div class="col-md-12"></div>
      <div class="form-group col-md-3 uk_citizen">
        <label class="optional">Is interprter a UK Citizen?</label>
        <select name="uk_citizen" required class="form-control" onchange="changer(this);">
          <option <?= $uk_citizen == 1 ? 'selected' : '' ?> value="1">Yes - UK Citizen</option>
          <option <?= $uk_citizen == 0 ? 'selected' : '' ?> value="0">No - Not UK Citizen</option>
        </select>
      </div>
      <div class="form-group col-md-3 col-sm-6 div_passport_file <?= $uk_citizen == 1 ? '' : 'hidden' ?>">
        <label><small>British Passport / Identity Document</small></label>
        <input name="passport_file" type="file" class="form-control" onchange="max_upload(this);">
      </div>
      <div class="form-group col-md-2 col-sm-6 div_passport_file <?= $uk_citizen == 1 ? '' : 'hidden' ?>">
        <label>Passport Number</label>
        <input placeholder="Enter Passport Number" type="text" value="<?= $row['id_doc_no'] ?>" name="passport_number" class="form-control uk_citizen_fields mt">
      </div>
      <div class="form-group col-md-2 col-sm-6 div_passport_file <?= $uk_citizen == 1 ? '' : 'hidden' ?>">
        <label>Select Issue Date</label>
        <input placeholder="Enter Issue Date" type="text" value="<?= $row['id_doc_issue_date'] && $row['id_doc_issue_date'] != '1001-01-01' ? $row['id_doc_issue_date'] : '' ?>" onfocus="(this.type='date')" onblur="(this.type='text')" name="passport_issue_date" class="form-control uk_citizen_fields" />
      </div>
      <div class="form-group col-md-2 col-sm-6 div_passport_file <?= $uk_citizen == 1 ? '' : 'hidden' ?>">
        <label>Select Expiry Date</label>
        <input placeholder="Enter Expiry Date" type="text" value="<?= $row['id_doc_expiry_date'] && $row['id_doc_expiry_date'] != '1001-01-01' ? $row['id_doc_expiry_date'] : '' ?>" onfocus="(this.type='date')" onblur="(this.type='text')" name="passport_expiry_date" class="form-control uk_citizen_fields mt" />
      </div>
      <div class="form-group col-md-3 col-sm-6 div_work_evid_file <?= $uk_citizen == 0 ? '' : 'hidden' ?>">
        <label>Upload (<small>UK Right to work evidence</small>)</label>
        <input name="work_evid_file" type="file" class="form-control" onchange="max_upload(this);">
      </div>
      <div class="form-group col-md-3 col-sm-6 div_work_evid_file <?= $uk_citizen == 0 ? '' : 'hidden' ?>">
        <label>Right to Work No.</label>
        <input placeholder="Right to Work No." type="text" value="<?php echo $row['right_to_work_no']; ?>" name="right_to_work_no" class="form-control work_evid_fields">
      </div>
      <div class="form-group col-md-3 col-sm-6 div_work_evid_file <?= $uk_citizen == 0 ? '' : 'hidden' ?>">
        <label>Country of Origin (<small>Upload Passport</small>)</label>
        <input name="country_of_origin_passport" type="file" class="form-control" onchange="max_upload(this);">
      </div>
      <div class="form-group col-md-3 col-sm-6 div_work_evid_file <?= $uk_citizen == 0 ? '' : 'hidden' ?>">
        <label>Select Issue Dates</label>
        <input placeholder="Select Issue Date" type="text" value="<?= $row['work_evid_issue_date'] && $row['work_evid_issue_date'] != '1001-01-01' ? $row['work_evid_issue_date'] : '' ?>" onfocus="(this.type='date')" onblur="(this.type='text')" name="work_evid_issue_date" class="form-control work_evid_fields" />
      </div>
      <div class="form-group col-md-3 col-sm-6 div_work_evid_file <?= $uk_citizen == 0 ? '' : 'hidden' ?>">
        <label>Select Expiry Dates</label>
        <input placeholder="Select Expiry Date" type="text" value="<?= $row['work_evid_expiry_date'] && $row['work_evid_expiry_date'] != '1001-01-01' ? $row['work_evid_expiry_date'] : '' ?>" onfocus="(this.type='date')" onblur="(this.type='text')" name="work_evid_expiry_date" class="form-control work_evid_fields mt" />
      </div>
      <div class="form-group col-sm-12">
        <label class="optional">Mode of Job *</label><br>
        <div class="radio-inline ri">
          <label><input onchange="show_dbs()" type="checkbox" name="interp" id="interp" value="Yes" <?php if ($interp == 'Yes') { ?>checked="checked" <?php } ?>>
            <span class="label label-primary">Face To Face Interpreting <i class="fa fa-user"></i></span></label>
        </div>
        <div class="radio-inline ri">
          <label><input type="checkbox" name="telep" value="Yes" <?php if ($telep == 'Yes') { ?>checked="checked" <?php } ?>>
            <span class="label label-info">Telephone Interpreting <i class="fa fa-phone"></i></span></label>
        </div>
        <div class="radio-inline ri">
          <label><input type="checkbox" name="trans" value="Yes" <?php if ($trans == 'Yes') { ?>checked="checked" <?php } ?>>
            <span class="label label-success">Translator <i class="fa fa-language"></i></span></label>
        </div>
        <div class="row">
          <br>
          <div class="form-group col-md-3 col-sm-6 div_dbs_file <?= ($interp == 'Yes' || $dbs_checked == 0) && $row['is_dbs_auto'] == 0 ? '' : 'hidden' ?>">
            <label>Upload File (<small>DBS Document</small>)</label>
            <input name="dbs_file" type="file" class="form-control" onchange="max_upload(this);">
          </div>
          <div class="form-group col-md-3 col-sm-6 div_dbs_file <?= ($interp == 'Yes' || $dbs_checked == 0) && $row['is_dbs_auto'] == 0 ? '' : 'hidden' ?>">
            <label>Enter DBS Number</label>
            <input placeholder="Enter DBS Number" type="text" value="<?= $row['dbs_no'] ?>" name="dbs_no" class="form-control dbs_fields mt" <?= ($interp == 'Yes' || $dbs_checked == 0) && $row['is_dbs_auto'] == 0 ? 'required' : '' ?>>
          </div>
          <div class="form-group col-md-3 col-sm-6 div_dbs_file <?= ($interp == 'Yes' || $dbs_checked == 0) && $row['is_dbs_auto'] == 0 ? '' : 'hidden' ?>">
            <label>Select Issue Date</label>
            <input placeholder="Enter Issue Date" type="text" value="<?= $row['dbs_issue_date'] && $row['dbs_issue_date'] != '1001-01-01' ? $row['dbs_issue_date'] : '' ?>" onfocus="(this.type='date')" onblur="(this.type='text')" name="dbs_issue_date" class="form-control dbs_fields" <?= ($interp == 'Yes' || $dbs_checked == 0) && $row['is_dbs_auto'] == 0 ? 'required' : '' ?> />
          </div>
          <div class="form-group col-md-3 col-sm-6 div_dbs_file <?= ($interp == 'Yes' || $dbs_checked == 0) && $row['is_dbs_auto'] == 0 ? '' : 'hidden' ?>">
            <label>Select Expiry Date</label>
            <input placeholder="Enter Expiry Date" type="text" value="<?= $row['dbs_expiry_date'] && $row['dbs_expiry_date'] != '1001-01-01' ? $row['dbs_expiry_date'] : '' ?>" onfocus="(this.type='date')" onblur="(this.type='text')" name="dbs_expiry_date" class="form-control dbs_fields mt" <?= ($interp == 'Yes' || $dbs_checked == 0) && $row['is_dbs_auto'] == 0 ? 'required' : '' ?> />
          </div>
          <div class="form-group col-md-3 col-sm-6 div_dbs_auto_number <?= ($interp == 'Yes' || $dbs_checked == 0) && $row['is_dbs_auto'] == 1 ? '' : 'hidden' ?>">
            <label>Enter DBS Number</label>
            <input placeholder="Enter DBS Number" type="text" value="<?= $row['dbs_auto_number'] ?>" name="dbs_auto_number" class="form-control dbs_auto_number mt" <?= ($interp == 'Yes' || $dbs_checked == 0) && $row['is_dbs_auto'] == 1 ? 'required' : '' ?> />
          </div>
          <div class="col-md-12 div_auto_dbs">
            <label class="btn btn-warning"><input onchange="toggle_auto_dbs()" type="checkbox" name="is_dbs_auto" id="is_dbs_auto" value="1" <?= $row['is_dbs_auto'] == 1 ? "checked" : ''; ?>> DBS on update service?</label>
          </div>
        </div>
      </div>
        
      </div>
      <div class="bg-info col-xs-12 form-group">
        <h4>Address Details</h4>
      </div>
      <?php
        $country_array = array(
          "Afghanistan" => "Afghanistan (افغانستان)", "Aland Islands" => "Aland Islands (Åland)", "Albania" => "Albania (Shqipëria)", "Algeria" => "Algeria (الجزائر)", "American Samoa" => "American Samoa (American Samoa)", "Andorra" => "Andorra (Andorra)", "Angola" => "Angola (Angola)", "Anguilla" => "Anguilla (Anguilla)", "Antarctica" => "Antarctica (Antarctica)", "Antigua And Barbuda" => "Antigua And Barbuda (Antigua and Barbuda)", "Argentina" => "Argentina (Argentina)", "Armenia" => "Armenia (Հայաստան)", "Aruba" => "Aruba (Aruba)", "Australia" => "Australia (Australia)", "Austria" => "Austria (Österreich)", "Azerbaijan" => "Azerbaijan (Azərbaycan)", "Bahamas The" => "Bahamas The (Bahamas)", "Bahrain" => "Bahrain (‏البحرين)", "Bangladesh" => "Bangladesh (Bangladesh)", "Barbados" => "Barbados (Barbados)", "Belarus" => "Belarus (Белару́сь)", "Belgium" => "Belgium (België)", "Belize" => "Belize (Belize)", "Benin" => "Benin (Bénin)", "Bermuda" => "Bermuda (Bermuda)", "Bhutan" => "Bhutan (ʼbrug-yul)", "Bolivia" => "Bolivia (Bolivia)", "Bonaire, Sint Eustatius and Saba" => "Bonaire, Sint Eustatius and Saba (Caribisch Nederland)", "Bosnia and Herzegovina" => "Bosnia and Herzegovina (Bosna i Hercegovina)", "Botswana" => "Botswana (Botswana)", "Bouvet Island" => "Bouvet Island (Bouvetøya)", "Brazil" => "Brazil (Brasil)", "British Indian Ocean Territory" => "British Indian Ocean Territory (British Indian Ocean Territory)", "Brunei" => "Brunei (Negara Brunei Darussalam)", "Bulgaria" => "Bulgaria (България)", "Burkina Faso" => "Burkina Faso (Burkina Faso)", "Burundi" => "Burundi (Burundi)", "Cambodia" => "Cambodia (Kâmpŭchéa)", "Cameroon" => "Cameroon (Cameroon)", "Canada" => "Canada (Canada)", "Cape Verde" => "Cape Verde (Cabo Verde)", "Cayman Islands" => "Cayman Islands (Cayman Islands)", "Central African Republic" => "Central African Republic (Ködörösêse tî Bêafrîka)", "Chad" => "Chad (Tchad)", "Chile" => "Chile (Chile)", "China" => "China (中国)", "Christmas Island" => "Christmas Island (Christmas Island)", "Cocos (Keeling) Islands" => "Cocos (Keeling) Islands (Cocos (Keeling) Islands)", "Colombia" => "Colombia (Colombia)", "Comoros" => "Comoros (Komori)", "Congo" => "Congo (République du Congo)", "Congo The Democratic Republic Of The" => "Congo The Democratic Republic Of The (République démocratique du Congo)", "Cook Islands" => "Cook Islands (Cook Islands)", "Costa Rica" => "Costa Rica (Costa Rica)", "Cote D'Ivoire (Ivory Coast)" => "Cote D'Ivoire (Ivory Coast) ()", "Croatia (Hrvatska)" => "Croatia (Hrvatska) (Hrvatska)", "Cuba" => "Cuba (Cuba)", "Curaçao" => "Curaçao (Curaçao)", "Cyprus" => "Cyprus (Κύπρος)", "Czech Republic" => "Czech Republic (Česká republika)", "Denmark" => "Denmark (Danmark)", "Djibouti" => "Djibouti (Djibouti)", "Dominica" => "Dominica (Dominica)", "Dominican Republic" => "Dominican Republic (República Dominicana)", "East Timor" => "East Timor (Timor-Leste)", "Ecuador" => "Ecuador (Ecuador)", "Egypt" => "Egypt (مصر‎)", "El Salvador" => "El Salvador (El Salvador)", "Equatorial Guinea" => "Equatorial Guinea (Guinea Ecuatorial)", "Eritrea" => "Eritrea (ኤርትራ)", "Estonia" => "Estonia (Eesti)", "Ethiopia" => "Ethiopia (ኢትዮጵያ)", "Falkland Islands" => "Falkland Islands (Falkland Islands)", "Faroe Islands" => "Faroe Islands (Føroyar)", "Fiji Islands" => "Fiji Islands (Fiji)", "Finland" => "Finland (Suomi)", "France" => "France (France)", "French Guiana" => "French Guiana (Guyane française)", "French Polynesia" => "French Polynesia (Polynésie française)", "French Southern Territories" => "French Southern Territories (Territoire des Terres australes et antarctiques fr)", "Gabon" => "Gabon (Gabon)", "Gambia The" => "Gambia The (Gambia)", "Georgia" => "Georgia (საქართველო)", "Germany" => "Germany (Deutschland)", "Ghana" => "Ghana (Ghana)", "Gibraltar" => "Gibraltar (Gibraltar)", "Greece" => "Greece (Ελλάδα)", "Greenland" => "Greenland (Kalaallit Nunaat)", "Grenada" => "Grenada (Grenada)", "Guadeloupe" => "Guadeloupe (Guadeloupe)", "Guam" => "Guam (Guam)", "Guatemala" => "Guatemala (Guatemala)", "Guernsey and Alderney" => "Guernsey and Alderney (Guernsey)", "Guinea" => "Guinea (Guinée)", "Guinea-Bissau" => "Guinea-Bissau (Guiné-Bissau)", "Guyana" => "Guyana (Guyana)", "Haiti" => "Haiti (Haïti)", "Heard Island and McDonald Islands" => "Heard Island and McDonald Islands (Heard Island and McDonald Islands)", "Honduras" => "Honduras (Honduras)", "Hong Kong S.A.R." => "Hong Kong S.A.R. (香港)", "Hungary" => "Hungary (Magyarország)", "Iceland" => "Iceland (Ísland)", "India" => "India (भारत)", "Indonesia" => "Indonesia (Indonesia)", "Iran" => "Iran (ایران)", "Iraq" => "Iraq (العراق)", "Ireland" => "Ireland (Éire)", "Israel" => "Israel (יִשְׂרָאֵל)", "Italy" => "Italy (Italia)", "Jamaica" => "Jamaica (Jamaica)", "Japan" => "Japan (日本)", "Jersey" => "Jersey (Jersey)", "Jordan" => "Jordan (الأردن)", "Kazakhstan" => "Kazakhstan (Қазақстан)", "Kenya" => "Kenya (Kenya)", "Kiribati" => "Kiribati (Kiribati)", "Korea North" => "Korea North (북한)", "Korea South" => "Korea South (대한민국)", "Kosovo" => "Kosovo (Republika e Kosovës)", "Kuwait" => "Kuwait (الكويت)", "Kyrgyzstan" => "Kyrgyzstan (Кыргызстан)", "Laos" => "Laos (ສປປລາວ)", "Latvia" => "Latvia (Latvija)", "Lebanon" => "Lebanon (لبنان)", "Lesotho" => "Lesotho (Lesotho)", "Liberia" => "Liberia (Liberia)", "Libya" => "Libya (‏ليبيا)", "Liechtenstein" => "Liechtenstein (Liechtenstein)", "Lithuania" => "Lithuania (Lietuva)", "Luxembourg" => "Luxembourg (Luxembourg)", "Macau S.A.R." => "Macau S.A.R. (澳門)", "Macedonia" => "Macedonia (Северна Македонија)", "Madagascar" => "Madagascar (Madagasikara)", "Malawi" => "Malawi (Malawi)", "Malaysia" => "Malaysia (Malaysia)", "Maldives" => "Maldives (Maldives)", "Mali" => "Mali (Mali)", "Malta" => "Malta (Malta)", "Man (Isle of)" => "Man (Isle of) (Isle of Man)", "Marshall Islands" => "Marshall Islands (M̧ajeļ)", "Martinique" => "Martinique (Martinique)", "Mauritania" => "Mauritania (موريتانيا)", "Mauritius" => "Mauritius (Maurice)", "Mayotte" => "Mayotte (Mayotte)", "Mexico" => "Mexico (México)", "Micronesia" => "Micronesia (Micronesia)", "Moldova" => "Moldova (Moldova)", "Monaco" => "Monaco (Monaco)", "Mongolia" => "Mongolia (Монгол улс)", "Montenegro" => "Montenegro (Црна Гора)", "Montserrat" => "Montserrat (Montserrat)", "Morocco" => "Morocco (المغرب)", "Mozambique" => "Mozambique (Moçambique)", "Myanmar" => "Myanmar (မြန်မာ)", "Namibia" => "Namibia (Namibia)", "Nauru" => "Nauru (Nauru)", "Nepal" => "Nepal (नपल)", "Netherlands The" => "Netherlands The (Nederland)", "New Caledonia" => "New Caledonia (Nouvelle-Calédonie)", "New Zealand" => "New Zealand (New Zealand)", "Nicaragua" => "Nicaragua (Nicaragua)", "Niger" => "Niger (Niger)", "Nigeria" => "Nigeria (Nigeria)", "Niue" => "Niue (Niuē)", "Norfolk Island" => "Norfolk Island (Norfolk Island)", "Northern Mariana Islands" => "Northern Mariana Islands (Northern Mariana Islands)", "Norway" => "Norway (Norge)", "Oman" => "Oman (عمان)", "Pakistan" => "Pakistan (پاکستان)", "Palau" => "Palau (Palau)", "Palestinian Territory Occupied" => "Palestinian Territory Occupied (فلسطين)", "Panama" => "Panama (Panamá)", "Papua new Guinea" => "Papua new Guinea (Papua Niugini)", "Paraguay" => "Paraguay (Paraguay)", "Peru" => "Peru (Perú)", "Philippines" => "Philippines (Pilipinas)", "Pitcairn Island" => "Pitcairn Island (Pitcairn Islands)", "Poland" => "Poland (Polska)", "Portugal" => "Portugal (Portugal)", "Puerto Rico" => "Puerto Rico (Puerto Rico)", "Qatar" => "Qatar (قطر)", "Reunion" => "Reunion (La Réunion)", "Romania" => "Romania (România)", "Russia" => "Russia (Россия)", "Rwanda" => "Rwanda (Rwanda)", "Saint Helena" => "Saint Helena (Saint Helena)", "Saint Kitts And Nevis" => "Saint Kitts And Nevis (Saint Kitts and Nevis)", "Saint Lucia" => "Saint Lucia (Saint Lucia)", "Saint Pierre and Miquelon" => "Saint Pierre and Miquelon (Saint-Pierre-et-Miquelon)", "Saint Vincent And The Grenadines" => "Saint Vincent And The Grenadines (Saint Vincent and the Grenadines)", "Saint-Barthelemy" => "Saint-Barthelemy (Saint-Barthélemy)", "Saint-Martin (French part)" => "Saint-Martin (French part) (Saint-Martin)", "Samoa" => "Samoa (Samoa)", "San Marino" => "San Marino (San Marino)", "Sao Tome and Principe" => "Sao Tome and Principe (São Tomé e Príncipe)", "Saudi Arabia" => "Saudi Arabia (العربية السعودية)", "Senegal" => "Senegal (Sénégal)", "Serbia" => "Serbia (Србија)", "Seychelles" => "Seychelles (Seychelles)", "Sierra Leone" => "Sierra Leone (Sierra Leone)", "Singapore" => "Singapore (Singapore)", "Sint Maarten (Dutch part)" => "Sint Maarten (Dutch part) (Sint Maarten)", "Slovakia" => "Slovakia (Slovensko)", "Slovenia" => "Slovenia (Slovenija)", "Solomon Islands" => "Solomon Islands (Solomon Islands)", "Somalia" => "Somalia (Soomaaliya)", "South Africa" => "South Africa (South Africa)", "South Georgia" => "South Georgia (South Georgia)", "South Sudan" => "South Sudan (South Sudan)", "Spain" => "Spain (España)", "Sri Lanka" => "Sri Lanka (śrī laṃkāva)", "Sudan" => "Sudan (السودان)", "Suriname" => "Suriname (Suriname)", "Svalbard And Jan Mayen Islands" => "Svalbard And Jan Mayen Islands (Svalbard og Jan Mayen)", "Swaziland" => "Swaziland (Swaziland)", "Sweden" => "Sweden (Sverige)", "Switzerland" => "Switzerland (Schweiz)", "Syria" => "Syria (سوريا)", "Taiwan" => "Taiwan (臺灣)", "Tajikistan" => "Tajikistan (Тоҷикистон)", "Tanzania" => "Tanzania (Tanzania)", "Thailand" => "Thailand (ประเทศไทย)", "Togo" => "Togo (Togo)", "Tokelau" => "Tokelau (Tokelau)", "Tonga" => "Tonga (Tonga)", "Trinidad And Tobago" => "Trinidad And Tobago (Trinidad and Tobago)", "Tunisia" => "Tunisia (تونس)", "Turkey" => "Turkey (Türkiye)", "Turkmenistan" => "Turkmenistan (Türkmenistan)", "Turks And Caicos Islands" => "Turks And Caicos Islands (Turks and Caicos Islands)", "Tuvalu" => "Tuvalu (Tuvalu)", "Uganda" => "Uganda (Uganda)", "Ukraine" => "Ukraine (Україна)", "United Arab Emirates" => "United Arab Emirates (دولة الإمارات العربية المتحدة)", "United Kingdom" => "United Kingdom (United Kingdom)", "United States" => "United States (United States)", "United States Minor Outlying Islands" => "United States Minor Outlying Islands (United States Minor Outlying Islands)", "Uruguay" => "Uruguay (Uruguay)", "Uzbekistan" => "Uzbekistan (O‘zbekiston)", "Vanuatu" => "Vanuatu (Vanuatu)", "Vatican City State (Holy See)" => "Vatican City State (Holy See) (Vaticano)", "Venezuela" => "Venezuela (Venezuela)",
          "Vietnam" => "Vietnam (Việt Nam)", "Virgin Islands (British)" => "Virgin Islands (British) (British Virgin Islands)", "Virgin Islands (US)" => "Virgin Islands (US) (United States Virgin Islands)", "Wallis And Futuna Islands" => "Wallis And Futuna Islands (Wallis et Futuna)", "Western Sahara" => "Western Sahara (الصحراء الغربية)", "Yemen" => "Yemen (اليَمَن)", "Zambia" => "Zambia (Zambia)", "Zimbabwe" => "Zimbabwe (Zimbabwe)"
        ); ?>
      <div class="form-group col-md-3 col-sm-6">
          <label>Select a country of Origin *</label><br>
          <?php 
          $select_countries = "<select name='country_of_origin' id='country_of_origin' class='form-control multi_class mt'>
                <option value='' disabled selected>--- Select a country ---</option>";
          foreach ($country_array as $key => $val) {
            $selected = (!empty($country_of_origin) && $country_of_origin == $key) ? "selected" : "";
            $select_countries .= "<option value='" . htmlspecialchars($key) . "' " . $selected . ">" . htmlspecialchars($val) . "</option>";
          }
          $select_countries .= "</select>";
          echo $select_countries; 
          ?>
        </div>

      <div class="form-group col-md-3 col-sm-6">
        <label>Select a Residence country *</label><br>
        <?php 
        $select_countries = "<select onchange='get_cities(this)' name='selected_country' id='selected_country' class='form-control multi_class mt'>
              <option value='" . $country . "' selected>" . $country . "</option>
              <option value='' disabled>--- Select a country ---</option>";
        foreach ($country_array as $key => $val) {
          $select_countries .= "<option value='" . $key . "'>" . $val . "</option>";
        }
        $select_countries .= "<select>";
        echo $select_countries; ?>
      </div>
      <div class="form-group col-md-3 append_cities">
        <?php 
        if (isset($country)) {
          $url = 'https://countriesnow.space/api/v0.1/countries/cities';
          $data = [
            "country" => $country
          ];
      
          $ch = curl_init($url);
          curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
          curl_setopt($ch, CURLOPT_POST, true);
          curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
          curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); // Follow redirects
      
          $response = curl_exec($ch);
          $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
      
          if ($httpcode === 200) {
              $cities_array = json_decode($response)->data;
              $select_cities = "";
              if (!isset($_POST['without_label'])) {
                  $select_cities .= "<label>Select City Name *</label>";
              }
              $select_cities .= "<select onchange='other_city(this)' name='selected_city' id='selected_city' class='form-control mt' required>
                <option value='" . $city . "' selected>" . $city . "</option>
                  <option value='' disabled>--- Select a city ---</option>";
              if (count($cities_array) > 0) {
                  foreach ($cities_array as $key => $val) {
                      $select_cities .= "<option value='" . $val . "'>" . $val . "</option>";
                  }
                  $select_cities .= "<option value='Not in List'>Not in List</option>";
              } else {
                  $select_cities .= "<option value='Not in List'>No City Found</option>";
              }
              $select_cities .= "<select>";
              echo $select_cities;
              $other_city_class = "hidden";
          } else {
              $other_city_class = "";
          }
        }
         ?>
      </div>
      <div class="form-group col-md-3 div_other_city_field">
        <label>Enter City Name *</label>
        <input name="city" type="text" class="form-control mt other_city_field" value="<?php echo $city; ?>" placeholder="Enter City Name" />
      </div>
      <div class="form-group col-md-3 col-sm-6">
        <label class="optional">Post Code</label>
        <input placeholder="Post Code" class="form-control" name="postCode" type="text" value="<?php echo $postCode; ?>" />
      </div>
      <div class="form-group col-md-3 col-sm-6">
        <label class="row1">Building Number / Name * </label>
        <input placeholder="Building Number / Name *" class="form-control" name="buildingName" type="text" required value="<?php echo $buildingName; ?>" />
      </div>
      <div class="form-group col-md-3 col-sm-6">
        <label class="optional">Address Line 1 </label>
        <input placeholder="Address Line 1" class="form-control" name="line1" type="text" value="<?php echo $line1; ?>" />
      </div>
      <div class="form-group col-md-3 col-sm-6">
        <label class="optional">Address Line 2</label>
        <input placeholder="Address Line 2" class="form-control" name="line2" type="text" value="<?php echo $line2; ?>" />
      </div>
      <div class="form-group col-md-3 col-sm-6">
        <label class="optional">Address Line 3 </label>
        <input placeholder="Address Line 3" class="form-control" name="line3" type="text" id="line3" value="<?php echo $line3; ?>" />
      </div>
      <div class="bg-info col-xs-12 form-group">
            <h4>DISCLAIMER AND SIGNATURE</h4>
      </div>
      <div class="">
         <div class="form-group col-md-12">
            <label><input type="checkbox" name="disclaimer" id="disclaimer" <?php echo (strtotime($row['signature_date']) >= strtotime($agreement_validity)) ? 'checked' : ''; ?> style="margin-bottom: 4px;" required data-backdrop="static" data-keyboard="false" data-target="#modal_terms" data-toggle="modal">
                    I accept <a href="javascript:void(0)" data-backdrop="static" data-keyboard="false" data-target="#modal_terms" data-toggle="modal" title="Click to read Terms"><b>Terms & Conditions</b></a> and certify that
                                    my answers are true and complete to the best of my knowledge. If this
                                    application leads to employment, I understand that false or misleading
                                    information in my application or interview may result in my release. I will
                                    update my details with LSUK if it changes</label>
         </div>
        </div>
        <div class="form-group col-md-12 col-sm-12 text-right p-b-50">
            <br><button class="btn btn-info pull-right" style="border-color: #000000;color: black;font-size: 20px;font-weight: bold;box-shadow: 2px 2px 2px #c5c5a3;" type="submit" name="submit">UPDATE NOW &raquo;</button>
        </div>
    </form>

                    <!-- Terms & conditions Modal Starts -->
                <div class="modal fade" id="modal_terms" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-body">
                                <div style="border: 1px solid grey;padding: 10px;">
                                    <?php
                                        $template = $acttObj->read_specific("em_format", "email_format", "id=41")["em_format"];

                                        // replace placeholders
                                        $template = str_replace(
                                            ["[INTERPRETER_NAME]", "[SIGNED_DATED]"],
                                            [$row['name'], $row['signature_date']],
                                            $template
                                        );

                                        echo $template;
                                        ?>
                                </div>
                            </div>
                            <div class="modal-footer justify-content-center">
                                <a onclick="$('#disclaimer').prop('checked', true);" type="button" class="btn btn-primary" data-dismiss="modal">Accept & Close</a>
                            </div>
                        </div>
                    </div>
                </div>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.13/css/bootstrap-multiselect.css" rel="stylesheet" type="text/css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.13/js/bootstrap-multiselect.js" type="text/javascript"></script>
    <script>
      $(document).ready(function() {
        // Show or hide the cities
        <?php if ($other_city_class == 'hidden') { ?>
          $('.div_other_city_field, .other_city_field').addClass('hidden');
          $('.append_cities').removeClass('hidden');
        <?php } else { ?>
          $('.div_other_city_field, .other_city_field').removeClass('hidden');
          $('.append_cities').addClass('hidden');
        <?php } ?>

        show_dbs();toggle_auto_dbs();
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

      });
      $(function() {
        var foo = $('#acntCode').val().split("-").join(""); // remove hyphens
        if (foo.length > 0) {
          foo = foo.match(new RegExp('.{1,2}', 'g')).join("-");
          $('#acntCode').val(foo);
        }
      });
      $(function() {
        $('.multi_class').multiselect({
          buttonWidth: '100px',
          includeSelectAllOption: true,
          numberDisplayed: 1,
          enableFiltering: true,
          enableCaseInsensitiveFiltering: true
        });
      });
      $(".valid").bind('keypress paste', function(e) {
        var regex = new RegExp(/[a-z A-Z 0-9 ()]/);
        var str = String.fromCharCode(!e.charCode ? e.which : e.charCode);
        if (!regex.test(str)) {
          e.preventDefault();
          return false;
        }
      });

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
                url: 'ajax_add_interp_data.php',
                method: 'post',
                dataType: 'json',
                data: {
                    country_name: country_name,
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
      $(".validate_number").on("input", function() {
        if (/^0/.test(this.value)) {
          this.value = this.value.replace(/^0/, "");
        }
        $(this).val($(this).val().replace(/[^0-9]/gi, ''));
      });

      function show_dbs() {
        if ($("#interp").is(":checked")) {
          $('.dbs_fields').attr('required', "required");
          $('.div_dbs_file, .div_auto_dbs').removeClass('hidden');
        } else {
          $('.div_dbs_file, .div_auto_dbs').addClass('hidden');
          $('.dbs_fields').removeAttr("required");
          if ($("#is_dbs_auto").is(":checked")) {
            $("#is_dbs_auto").prop("checked", false);
            $('.dbs_auto_number').removeAttr('required');
            $('.div_dbs_auto_number').addClass('hidden');
          }
        }
      }

      function toggle_auto_dbs() {
        if ($("#interp").is(":checked")) {
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
      }

      function max_upload($element) {
        if ($element.files[0].size > 26214400) {
          alert("File is too big ! Upload upto 25 MB file");
          $element.value = "";
        } else {
          return 1;
        }
      }

      function changer(elem) {
        var value = $(elem).val();
        if (value == 0) {
          $('.div_passport_file').addClass('hidden');
          $('.uk_citizen_fields').removeAttr("required");
          $('.work_evid_fields').attr('required', "required");
          $('.div_work_evid_file').removeClass('hidden');
        } else {
          $('.uk_citizen_fields').attr('required', "required");
          $('.div_passport_file').removeClass('hidden');
          $('.div_work_evid_file').addClass('hidden');
          $('.work_evid_fields').removeAttr("required");
        }
      }
      function checkAccountNumber(input) {
          var inputValue = input.value;
          if (inputValue.length === 8 && !/(\d)\1{7}/.test(inputValue)) {
              // If the input has exactly 8 digits and not all digits are the same, do nothing
              return;
          } else {
              alert("System can not accept this type of Account Number, please contact admin for more details.\nAccount number needs to be entered as a non-unique and exact 8 digits!");
              input.value = "";
              input.focus();
          }
      }
      function checkAccountSortCode(input) {
          var inputValue = input.value.replace(/[^0-9]/g, ''); // Remove non-digit characters
          if (/^(\d)\1{5}$/.test(inputValue)) {
              // If the input has exactly 6 digits and all digits are the same
              alert("System can not accept this type of SortCode, please contact admin for more details.\nSortCode needs to be entered as a non-unique and exact 6 digits!");
              input.value = ""; // Clear the input field
              input.focus(); // Focus on the input field
          }
      }
    </script>
</body>

</html>