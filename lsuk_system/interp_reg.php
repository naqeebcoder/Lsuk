<?php if (session_id() == '' || !isset($_SESSION)) {
  session_start();
}
include "userhaspage.php";
SysPermiss::UserHasPage(__FILE__);
include 'db.php';
include 'class.php';
if (isset($_POST['submit'])) {
  
  $table = 'interpreter_reg';
  $edit_id = $acttObj->get_id($table);
  $acttObj->editFun($table, $edit_id, 'code', 'id-' . $edit_id);
  $name = $_POST['name'];
  $acttObj->editFun($table, $edit_id, 'name', $name);
  $email = $_POST['email'];
  $acttObj->editFun($table, $edit_id, 'email', $email);
  $contactNo = $_POST['contactNo'];
  $acttObj->editFun($table, $edit_id, 'contactNo', $contactNo);
  $contactNo2 = $_POST['contactNo2'];
  $acttObj->editFun($table, $edit_id, 'contactNo2', $contactNo2);
  $email2 = $_POST['email2'];
  $acttObj->editFun($table, $edit_id, 'email2', $email2);
  $dob = $_POST['dob'];
  $acttObj->editFun($table, $edit_id, 'dob', $dob);
  $rph = $_POST['rph'];
  $acttObj->editFun($table, $edit_id, 'rph', $rph);
  $rpm = $_POST['rpm'];
  $acttObj->editFun($table, $edit_id, 'rpm', $rpm);
  $rpu = $_POST['rpu'];
  $acttObj->editFun($table, $edit_id, 'rpu', $rpu);
  $bnakName = $_POST['bnakName'];
  $acttObj->editFun($table, $edit_id, 'bnakName', $bnakName);
  $acName = $_POST['acName'];
  $acttObj->editFun($table, $edit_id, 'acName', $acName);
  $acntCode = str_replace("-", "", $_POST['acntCode']);
  $acttObj->editFun($table, $edit_id, 'acntCode', $acntCode);
  $acNo = $_POST['acNo'];
  $acttObj->editFun($table, $edit_id, 'acNo', $acNo);
  $ni = @$_POST['ni1'] . @$_POST['ni2'] . @$_POST['ni3'] . @$_POST['ni4'] . @$_POST['ni5'] . @$_POST['ni6'] . @$_POST['ni7'] . @$_POST['ni8'] . @$_POST['ni9'] . @$_POST['ni10'];
  $acttObj->editFun($table, $edit_id, 'ni', $ni);
  $acttObj->editFun($table, $edit_id, 'is_ni', $_POST['is_ni']);
  $buildingName = $_POST['buildingName'];
  $acttObj->editFun($table, $edit_id, 'buildingName', $buildingName);
  $line1 = $_POST['line1'];
  $acttObj->editFun($table, $edit_id, 'line1', $line1);
  $line2 = $_POST['line2'];
  $acttObj->editFun($table, $edit_id, 'line2', $line2);
  $line3 = $_POST['line3'];
  $acttObj->editFun($table, $edit_id, 'line3', $line3);
  $country = $_POST['selected_country'];
  $acttObj->editFun($table, $edit_id, 'country', $country);
  $city = $_POST['city'];
  $acttObj->editFun($table, $edit_id, 'city', $city);
  $postCode = $_POST['postCode'];
  $acttObj->editFun($table, $edit_id, 'postCode', $postCode);
  $interp = @$_POST['interp'];
  if (empty($interp)) {
    $interp = 'No';
  }
  $acttObj->editFun($table, $edit_id, 'interp', $interp);
  $telep = @$_POST['telep'];
  if (empty($telep)) {
    $telep = 'No';
  }
  $acttObj->editFun($table, $edit_id, 'telep', $telep);
  $trans = @$_POST['trans'];
  if (empty($trans)) {
    $trans = 'No';
  }
  $acttObj->editFun($table, $edit_id, 'trans', $trans);
  $picName = $acttObj->upload_file("interp_photo", $_FILES["file"]["name"], $_FILES["file"]["type"], $_FILES["file"]["tmp_name"], $edit_id);
  $acttObj->editFun($table, $edit_id, 'interp_pix', $picName);
  $reg_date = $_POST['reg_date'];
  $acttObj->editFun($table, $edit_id, 'reg_date', $reg_date);
  $acttObj->editFun($table, $edit_id, 'dated', date("Y-m-d"));
  $new_password = '@' . strtok($_POST['name'], " ") . substr(str_shuffle('0123456789abcdwxyz'), 0, 5) . substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 3);
  $acttObj->editFun($table, $edit_id, 'password', $new_password);
  $dbs_checked = $_POST['dbs_checked'];
  $acttObj->editFun($table, $edit_id, 'dbs_checked', $dbs_checked);
  $gender = $_POST['gender'];
  $acttObj->editFun($table, $edit_id, 'gender', $gender);
 
  $uk_citizen = $_POST['uk_citizen'];
  $acttObj->editFun($table, $edit_id, 'uk_citizen', $uk_citizen);

  if ($_POST['uk_citizen'] == 1) {
    //Identity / passport document
    $acttObj->editFun($table, $edit_id, 'identityDocument', 'Soft Copy');
    $acttObj->editFun($table, $edit_id, 'id_doc_no', trim($_POST['passport_number']));
    $acttObj->editFun($table, $edit_id, 'id_doc_issue_date', trim($_POST['passport_issue_date']));
    $acttObj->editFun($table, $edit_id, 'id_doc_expiry_date', trim($_POST['passport_expiry_date']));

    if ($_FILES["passport_file"]["name"] != NULL || $_POST['passport_number']) {
      $id_doc_file = $acttObj->upload_file("file_folder/issue_expiry_docs", $_FILES["passport_file"]["name"], $_FILES["passport_file"]["type"], $_FILES["passport_file"]["tmp_name"], round(microtime(true)));
      $acttObj->editFun($table, $edit_id, 'id_doc_file', $id_doc_file);
    }
  }

  if ($_POST['uk_citizen'] == 0) {
    $acttObj->editFun($table, $edit_id, 'work_evid_issue_date', $_POST['work_evid_issue_date']);
    $acttObj->editFun($table, $edit_id, 'work_evid_expiry_date', $_POST['work_evid_expiry_date']);
    $acttObj->editFun($table, $edit_id, 'right_to_work_no', $_POST['right_to_work_no']);

    if (!empty($_FILES["work_evid_file"]["name"])) {
      $work_evid_file = $acttObj->upload_file("file_folder/issue_expiry_docs", $_FILES["work_evid_file"]["name"], $_FILES["work_evid_file"]["type"], $_FILES["work_evid_file"]["tmp_name"], round(microtime(true)));
      $acttObj->editFun($table, $edit_id, 'work_evid_file', $work_evid_file);
    }

    if (!empty($_FILES["country_of_origin_passport"]["name"])) {
      $country_of_origin_passport = $acttObj->upload_file("file_folder/issue_expiry_docs", $_FILES["country_of_origin_passport"]["name"], $_FILES["country_of_origin_passport"]["type"], $_FILES["country_of_origin_passport"]["tmp_name"], round(microtime(true)));
      $acttObj->editFun($table, $edit_id, 'country_of_origin_passport', $country_of_origin_passport);
    }
  }
  
  if ($_SESSION['Temp'] == 1) {
    $acttObj->editFun($table, $edit_id, 'is_temp', '1');
  }
  $isAdhoc = ($_POST['isAdhoc'] == "1") ? 1 : NULL;
  $workType = ($isAdhoc === NULL) ? $_POST['isAdhoc'] : NULL; 
  $acttObj->editFun($table, $edit_id, 'isAdhoc', $isAdhoc);
  $acttObj->editFun($table, $edit_id, 'work_type', $workType);
  $acttObj->editFun($table, $edit_id, 'created_by', $_SESSION['userId']);
  $acttObj->editFun($table, $edit_id, 'created_date', date("Y-m-d H:i:s"));
  $acttObj->editFun($table, $edit_id, 'sbmtd_by', ucwords($_SESSION['UserName']));
  $acttObj->editFun($table, $edit_id, 'edited_by', $_SESSION['UserName']);
  $acttObj->editFun($table, $edit_id, 'edited_date', date("Y-m-d H:i:s"));
  // Add interpreter rates as well
  if ($_POST['rate_group_id']) {
    $data_array = array("rate_group_id" => $_POST['rate_group_id'], "interpreter_id" => $edit_id, "created_by" => $_SESSION['userId'], "created_date" => date('Y-m-d H:i:s'));
    $acttObj->insert("individual_interpreter_rates", $data_array);
  }
  echo "<script>alert('New interpreter successfully registered. Thank you');window.onunload = refreshParent;</script>";
}
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
  <title>Interpreter Registration Form</title>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" />
  <link rel="stylesheet" type="text/css" href="css/util.css" />
  <?php include 'ajax_uniq_fun.php'; ?>
  <style>
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
      <div class="bg-info col-xs-12 form-group">
        <h4>Interpreter Personal Details</h4>
      </div>
      <div class="form-group col-md-3 col-sm-6">
        <label>Interpreter Name</label>
        <input placeholder="Name *" class="form-control" name="name" type="text" id="name" required='' tabindex="1" />
      </div>
      <div class="form-group col-md-3 col-sm-6">
        <label>Date of Birth</label>
        <input placeholder="Date of Birth" type="text" onfocus="(this.type='date')" onblur="(this.type='text')" class="form-control" name="dob" id="dob" tabindex="2" />
      </div>
      <div class="form-group col-md-3 col-sm-6">
        <label>Rate per Hour *</label>
        <input placeholder="Rate per Hour *" class="form-control" name="rph" type="text" id="rph" required='' pattern="[0-9]+([\.|,][0-9]+)?" step="0.01" tabindex="3" />
      </div>
      <div class="form-group col-md-3 col-sm-6">
        <label>Email Address 1 *</label>
        <input placeholder="Email Address 1 *" class="form-control" name="email" type="text" id="email" required='' onfocus="check_fields()" onblur="check_existing(this)" tabindex="4" />
      </div>
      <div class="form-group col-md-3 col-sm-6">
        <label>Mobile No *</label>
        <input placeholder="Mobile No *" class="form-control validate_number" name="contactNo" type="text" id="contactNo" required='' tabindex="5" />
      </div>
      <div class="form-group col-md-3 col-sm-6">
        <label>Email Address 2</label>
        <input placeholder="Email Address 2" class="form-control" name="email2" type="text" id="email2" required='' onBlur="uniqueFun(this.value,'interpreter_reg','email2',$(this).attr('id') );" tabindex="6" />
      </div>
      <div class="form-group col-md-3 col-sm-6">
        <label>Landline *</label>
        <input placeholder="Landline *" class="form-control validate_number" name="contactNo2" type="text" id="contactNo2" required='' tabindex="7" />
      </div>
      <div class="form-group col-md-3 col-sm-6">
        <label>Rate per Min (Telephone)*</label>
        <input placeholder="Rate per Min (Telephone)*" class="form-control" name="rpm" type="text" id="rpm" required='' pattern="[0-9]+([\.|,][0-9]+)?" step="0.01" tabindex="8" />
      </div>
      <div class="form-group col-md-3 col-sm-6">
        <label>Rate per Unit(Translation)</label>
        <input placeholder="Rate per Unit(Translation)" class="form-control" name="rpu" type="text" id="rpu" required='' pattern="[0-9]+([\.|,][0-9]+)?" step="0.01" tabindex="9" />
      </div>
      <?php if (isset($_POST['submit'])) {
        TestCode::ModifyHtmlDB("interp_reg_travel.html", $table, $edit_id);
      } else {
        TestCode::AddHtmlFieldsDB("interp_reg_travel.html", null);
      } ?>
      <div class="form-group col-md-3 col-sm-6">
        <label for="rate_group_id">Select Rate Group *</label>
        <select name="rate_group_id" class="form-control" required id="rate_group_id">
          <option value="">---Select Rate Group---</option>
          <?php $get_rate_groups = $acttObj->read_all("*", "interpreter_groups", "1");
          while ($row_group = $get_rate_groups->fetch_assoc()) { ?>
            <option value="<?= $row_group['id'] ?>" <?= $row_group['bsl_group'] == 1 ? 'style="color:red"' : '' ?>><?= $row_group['title'] ?></option>
          <?php } ?>
        </select>
      </div>
      <div class="bg-info col-xs-12 form-group">
        <h4>Bank Account Details</h4>
      </div>
      <div class="form-group col-md-3 col-sm-6">
        <label>Bank Name</label>
        <input placeholder="Bank Name" class="form-control" name="bnakName" type="text" id="bnakName" tabindex="10" />
      </div>
      <div class="form-group col-md-3 col-sm-6">
        <label>Account Name</label>
        <input placeholder="Account Name" class="form-control" name="acName" type="text" id="acName" tabindex="11" />
      </div>
      <div class="form-group col-md-3 col-sm-6">
        <label>Account Sort Code</label>
        <input placeholder="Account Sort Code (6 digits)" class="form-control" name="acntCode" type="text" id="acntCode" onchange="checkAccountSortCode(this)" oninput="this.value = this.value.replace(/[^0-9-.]/g, '').replace(/(\..*)\./g, '$1');" maxlength="8" minlength="8" tabindex="12" />
      </div>
      <div class="form-group col-md-3 col-sm-6">
        <label>Account Number</label>
        <input placeholder="Account Number (8 digits)" class="form-control" name="acNo" type="text" id="acNo" onchange="checkAccountNumber(this)" oninput="this.value = this.value.replace(/[^\d]/g, '').replace(/(\..*)\./g, '$1');" maxlength="8" minlength="8" tabindex="13" />
      </div>
      <div class="form-group col-sm-6">
        <label class="" for="for-ni">
            <input id="ni" name="is_ni" type="radio" value="NI" checked required /> National Insurance #
          </label>
          <label class="" for="for-ni">
            <input id="utr" name="is_ni" type="radio" value="UTR" /> UTR
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

        <span class="inlineinput">
          <input name="ni1" type='text' style='display: inline;width: 32px;padding: 8px;' maxlength="1" onkeyup="moveNextPrev(this, event)" tabindex="13" />
        </span>
        <span class="inlineinput">
          <input name="ni2" type='text' style='display: inline;width: 32px;padding: 8px;' maxlength="1" onkeyup="moveNextPrev(this, event)" tabindex="14" />
        </span><span class="inlineinput">
          <input name="ni3" type='text' style='display: inline;width: 32px;padding: 8px;' maxlength="1" onkeyup="moveNextPrev(this, event)" tabindex="15" />
        </span><span class="inlineinput">
          <input name="ni4" type='text' style='display: inline;width: 32px;padding: 8px;' maxlength="1" onkeyup="moveNextPrev(this, event)" tabindex="16" />
        </span><span class="inlineinput">
          <input name="ni5" type='text' style='display: inline;width: 32px;padding: 8px;' maxlength="1" onkeyup="moveNextPrev(this, event)" tabindex="17" />
        </span><span class="inlineinput">
          <input name="ni6" type='text' style='display: inline;width: 32px;padding: 8px;' maxlength="1" onkeyup="moveNextPrev(this, event)" tabindex="18" />
        </span><span class="inlineinput">
          <input name="ni7" type='text' style='display: inline;width: 32px;padding: 8px;' maxlength="1" onkeyup="moveNextPrev(this, event)" tabindex="19" />
        </span><span class="inlineinput">
          <input name="ni8" type='text' style='display: inline;width: 32px;padding: 8px;' maxlength="1" onkeyup="moveNextPrev(this, event)" tabindex="20" />
        </span><span class="inlineinput">
          <input name="ni9" type='text' style='display: inline;width: 32px;padding: 8px;' maxlength="1" onkeyup="moveNextPrev(this, event)" tabindex="21" />
        </span><span class="inlineinput">
          <input name="ni10" type='text' style='display: inline;width: 32px;padding: 8px;' maxlength="1" onkeyup="moveNextPrev(this, event)" tabindex="22" />
        </span>
      </div>
      <div class="form-group col-sm-6">
        <label class="text-danger"><b>Register Interpreter As</b></label><br>
          <label class="btn btn-default" for="for-freelance">
            <input id="for-freelance" name="isAdhoc" type="radio" value="freelance" checked required /> Freelance
          </label>
          <label class="btn btn-default" for="for-in-house">
            <input id="for-in-house" name="isAdhoc" type="radio" value="in-house" /> In-House
          </label>
          <label style="background-color: #0000005e;" class="btn btn-success" for="is_temp_yes">
            <input id="is_temp_yes" name="isAdhoc" type="radio" value="1"/> Adhoc Interpreter
          </label>
      </div>
      <div class="bg-info col-xs-12 form-group">
        <h4>Address Details</h4>
      </div>
      <div class="form-group col-md-3">
        <label>Select a country *</label><br>
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
          <label>Enter City Name *</label>
          <input name="city" type="text" class="form-control mt other_city_field hidden" placeholder="Enter your City Name" />
      </div>
      <div class="form-group col-md-3 col-sm-6">
        <label>Post Code</label>
        <input placeholder="Post Code" class="form-control" name="postCode" type="text" />
      </div>
      <div class="form-group col-md-3 col-sm-6">
        <label class="row1">Building Number / Name *</label>
        <input placeholder="Building Number / Name *" class="form-control" name="buildingName" type="text" required />
      </div>
      <div class="form-group col-md-3 col-sm-6">
        <label class="optional">Address Line 1</label>
        <input placeholder="Address Line 1" class="form-control" name="line1" type="text" />
      </div>
      <div class="form-group col-md-3 col-sm-6">
        <label class="optional">Address Line 2</label>
        <input placeholder="Address Line 2" class="form-control" name="line2" type="text" />
      </div>
      <div class="form-group col-md-3 col-sm-6">
        <label class="optional">Address Line 3</label>
        <input placeholder="Address Line 3" class="form-control" name="line3" type="text" id="line3" />
      </div>
      <div class="bg-info col-xs-12 form-group">
        <h4>Other Information</h4>
      </div>
      <div class="form-group col-md-3 col-sm-6" style="display: none;">
        <label>Mode of Job *</label>
        <table width="20%" class="table table-bordered">
          <tr>
            <td width="100">Face To Face Interpreting</td>
            <td width="1"><input type="checkbox" name="interp" id="interp" value="Yes" checked="checked" /></td>
          </tr>
          <tr>
            <td width="100">Telephone Interpreting</td>
            <td width="1"><input type="checkbox" name="telep" value="Yes" /></td>
          </tr>
          <tr>
            <td width="100">Translation</td>
            <td width="1"><input type="checkbox" name="trans" value="Yes" /></td>
          </tr>
        </table>
      </div>
      <div class="form-group col-md-3 col-sm-6">
        <label class="optional">Interpreter Photo </label>
        <input class="form-control long" name="file" type="file" id="file" />
      </div>
      <div class="form-group col-md-3 col-sm-6">
        <label>Registration Date</label>
        <input class="form-control" name="reg_date" type="date" id="reg_date" tabindex="10" />
      </div>
      <div class="form-group col-md-3 col-sm-6">
        <label class="optional">DBS checked ?</label><br>
        <table width="20%" class="table table-bordered">
          <tr>
            <td width="1"><input name="dbs_checked" type="radio" value="0" required checked="checked" />
              <label class="gender">Yes</label>
            </td>
            <td width="1"><input type="radio" name="dbs_checked" value="1" />
              <label class="gender">No </label>
            </td>
          </tr>
        </table>
      </div>
      <div class="form-group col-md-3 col-sm-6" style="margin-top: -0px;">
        <label class="optional">Gender</label><br>
        <table width="20%" class="table table-bordered">
          <tr>
            <td width="1"><input name="gender" type="radio" value="Male" checked="checked" />
              <label class="gender">Male</label>
            </td>
            <td width="1"><input type="radio" name="gender" value="Female" />
              <label class="gender">Female</label>
            </td>
            <td width="1"><input type="radio" name="gender" value="No Preference" />
              <label class="gender">Other</label>
            </td>
          </tr>
        </table>
      </div>
      <div class="form-group col-md-3 uk_citizen">
        <label class="optional">Is interprter a UK Citizen?</label>
        <select name="uk_citizen" required class="form-control" onchange="changer(this);">
          <option value="1">Yes - UK Citizen</option>
          <option value="0">No - Not UK Citizen</option>
        </select>
      </div>
      <div class="form-group col-md-3 col-sm-6 div_passport_file">
        <label><small>British Passport / Identity Document</small></label>
        <input name="passport_file" type="file" class="form-control" onchange="max_upload(this);">
      </div>
      <div class="form-group col-md-2 col-sm-6 div_passport_file">
        <label>Passport Number</label>
        <input placeholder="Enter Passport Number" type="text" value="<?= $row['id_doc_no'] ?>" name="passport_number" class="form-control uk_citizen_fields mt">
      </div>
      <div class="form-group col-md-2 col-sm-6 div_passport_file">
        <label>Select Issue Date</label>
        <input placeholder="Enter Issue Date" type="text" value="<?= $row['id_doc_issue_date'] && $row['id_doc_issue_date'] != '1001-01-01' ? $row['id_doc_issue_date'] : '' ?>" onfocus="(this.type='date')" onblur="(this.type='text')" name="passport_issue_date" class="form-control uk_citizen_fields" />
      </div>
      <div class="form-group col-md-2 col-sm-6 div_passport_file">
        <label>Select Expiry Date</label>
        <input placeholder="Enter Expiry Date" type="text" value="<?= $row['id_doc_expiry_date'] && $row['id_doc_expiry_date'] != '1001-01-01' ? $row['id_doc_expiry_date'] : '' ?>" onfocus="(this.type='date')" onblur="(this.type='text')" name="passport_expiry_date" class="form-control uk_citizen_fields mt" />
      </div>
      <div class="form-group col-md-3 col-sm-6 div_work_evid_file hidden">
        <label>Upload (<small>UK Right to work evidence</small>)</label>
        <input name="work_evid_file" type="file" class="form-control" onchange="max_upload(this);">
      </div>
      <div class="form-group col-md-3 col-sm-6 div_work_evid_file hidden">
        <label>Right to Work No.</label>
        <input placeholder="Right to Work No." type="text" value="" name="right_to_work_no" class="form-control work_evid_fields">
      </div>
      <div class="form-group col-md-3 col-sm-6 div_work_evid_file hidden">
        <label>Country of Origin (<small>Upload Passport</small>)</label>
        <input name="country_of_origin_passport" type="file" class="form-control" onchange="max_upload(this);">
      </div>
      <div class="form-group col-md-3 col-sm-6 div_work_evid_file hidden">
        <label>Select Issue Dates</label>
        <input placeholder="Select Issue Date" type="text" value="<?= $row['work_evid_issue_date'] && $row['work_evid_issue_date'] != '1001-01-01' ? $row['work_evid_issue_date'] : '' ?>" onfocus="(this.type='date')" onblur="(this.type='text')" name="work_evid_issue_date" class="form-control work_evid_fields" />
      </div>
      <div class="form-group col-md-3 col-sm-6 div_work_evid_file hidden">
        <label>Select Expiry Dates</label>
        <input placeholder="Select Expiry Date" type="text" value="<?= $row['work_evid_expiry_date'] && $row['work_evid_expiry_date'] != '1001-01-01' ? $row['work_evid_expiry_date'] : '' ?>" onfocus="(this.type='date')" onblur="(this.type='text')" name="work_evid_expiry_date" class="form-control work_evid_fields mt" />
      </div>
      <!-- <div class="form-group col-md-3 col-sm-6" style="margin-top: -30px;">
        <label class="optional">UK Citizen?</label><br>
        <table width="20%" class="table table-bordered">
          <tr>
            <td width="1"><input name="uk_citizen" type="radio" value="1" checked="checked" />
              <label class="uk_citizen">Yes</label>
            </td>
            <td width="1"><input type="radio" name="uk_citizen" value="0" />
              <label class="uk_citizen">No</label>
            </td>
          </tr>
        </table>
      </div> -->
      <div class="form-group col-md-12 col-sm-12 m-t-20 text-right p-b-30">
        <button class="btn btn-info" style="border-color: #000000;color: black;font-size: 20px;font-weight: bold;box-shadow: 2px 2px 2px #c5c5a3;" type="submit" name="submit">REGISTER NOW &raquo;</button>
      </div>
    </form>
  </div>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.13/css/bootstrap-multiselect.css" rel="stylesheet" type="text/css" />
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.13/js/bootstrap-multiselect.js" type="text/javascript"></script>
  <script>
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

    function check_fields() {
      var name = $("#name").val();
      var dob = $("#dob").val();
      if (!name) {
        $("#name").focus();
      } else if (!dob) {
        $("#dob").focus();
      } else {}
    }

    function check_existing($elem) {
      var nm = $("#name").val();
      var dob = $("#dob").val();
      var em = $($elem).val();
      if (nm && dob && em) {
        $.ajax({
          url: 'ajax_add_interp_data.php',
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
            } else if (data['status'] == "exist" && data['is_temp'] == "0") {
              alert(data['msg']);
              $("#email").val("");
              $("#email").focus();
            } else if (data['status'] == "same_exist") {
              alert(data['msg']);
              $("#name").val("");
              $("#dob").val("");
              $("#email").val("");
              $("#name").focus();
            }
          },
          error: function(xhr) {
            alert("An error occured: " + xhr.status + " " + xhr.statusText);
          }
        });
      }
    }

    function refreshParent() {
      window.opener.location.reload();
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

      function max_upload($element) {
        if ($element.files[0].size > 26214400) {
          alert("File is too big ! Upload upto 25 MB file");
          $element.value = "";
        } else {
          return 1;
        }
      }
  </script>
</body>

</html>