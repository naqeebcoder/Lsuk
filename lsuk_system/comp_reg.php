<?php if (session_id() == '' || !isset($_SESSION)) {
  session_start();
}
include "userhaspage.php";
SysPermiss::UserHasPage(__FILE__);
include 'db.php';
include 'class.php';
$edit_id = "";
if (isset($_POST['submit'])) {
  $table = 'comp_reg';
  $edit_id = $acttObj->get_id($table);
  $c1 = $_POST['name'];
  $acttObj->editFun($table, $edit_id, 'name', $c1);
  $c2 = $_POST['contactPerson'];
  $acttObj->editFun($table, $edit_id, 'contactPerson', $c2);
  $payment_terms = $_POST['payment_terms'];
  $acttObj->editFun($table, $edit_id, 'payment_terms', $payment_terms);
  $data = $_POST['type_id'];
  $acttObj->editFun($table, $edit_id, 'type_id', $data);
  $bod = $_POST['bod'];
  $acttObj->editFun($table, $edit_id, 'bod', $bod);
  $crn = $_POST['crn'];
  $acttObj->editFun($table, $edit_id, 'crn', $crn);
  $vn = $_POST['vn'];
  $acttObj->editFun($table, $edit_id, 'vn', $vn);
  $c1 = $_POST['abrv'];
  $acttObj->editFun($table, $edit_id, 'abrv', $c1);
  $c2 = $_POST['contactNo1'];
  $acttObj->editFun($table, $edit_id, 'contactNo1', $c2);
  $c2 = $_POST['contactNo2'];
  $acttObj->editFun($table, $edit_id, 'contactNo2', $c2);
  $c2 = $_POST['contactNo3'];
  $acttObj->editFun($table, $edit_id, 'contactNo3', $c2);
  $web = $_POST['web'];
  $acttObj->editFun($table, $edit_id, 'web', $web);
  $credit_limit = $_POST['credit_limit'];
  $acttObj->editFun($table, $edit_id, 'credit_limit', $credit_limit);  
  $mileage = $_POST['mileage'];
  $acttObj->editFun($table, $edit_id, 'mileage', $mileage); 
  $travel_time = $_POST['travel_time'];
  $acttObj->editFun($table, $edit_id, 'travel_time', $travel_time);   
  $c1 = $_POST['email'];
  $acttObj->editFun($table, $edit_id, 'email', $c1);
  $c14 = $_POST['buildingName'];
  $acttObj->editFun($table, $edit_id, 'buildingName', $c14);
  $c14 = $_POST['line1'];
  $acttObj->editFun($table, $edit_id, 'line1', $c14);
  $c14 = $_POST['line2'];
  $acttObj->editFun($table, $edit_id, 'line2', $c14);
  $c15 = $_POST['streetRoad'];
  $acttObj->editFun($table, $edit_id, 'streetRoad', $c15);
  $country = $_POST['selected_country'];
  $acttObj->editFun($table, $edit_id, 'country', $country);
  $c1 = $_POST['city'];
  $acttObj->editFun($table, $edit_id, 'city', $c1);
  $c17 = $_POST['postCode'];
  $acttObj->editFun($table, $edit_id, 'postCode', $c17);
  $invEmail = $_POST['invEmail'];
  $acttObj->editFun($table, $edit_id, 'invEmail', $invEmail);
  $taupn = $_POST['taupn'];
  $acttObj->editFun($table, $edit_id, 'taupn', $taupn);
  $tpitc = $_POST['tpitc'];
  $acttObj->editFun($table, $edit_id, 'tpitc', $tpitc);
  $tbuildingName = $_POST['tbuildingName'];
  $acttObj->editFun($table, $edit_id, 'tbuildingName', $tbuildingName);
  $tline1 = $_POST['tline1'];
  $acttObj->editFun($table, $edit_id, 'tline1', $tline1);
  $tline2 = $_POST['tline2'];
  $acttObj->editFun($table, $edit_id, 'tline2', $tline2);
  $tstreetRoad = $_POST['tstreetRoad'];
  $acttObj->editFun($table, $edit_id, 'tstreetRoad', $tstreetRoad);
  $tcity = $_POST['tcity'];
  $acttObj->editFun($table, $edit_id, 'tcity', $tcity);
  $tpostCode = $_POST['tpostCode'];
  $acttObj->editFun($table, $edit_id, 'tpostCode', $tpostCode);
  $tcn = $_POST['tcn'];
  $acttObj->editFun($table, $edit_id, 'tcn', $tcn);
  $rff = $_POST['rff'];
  $acttObj->editFun($table, $edit_id, 'rff', $rff);
  if(isset($_POST['p_org'])){
    $p_org = $_POST['p_org'];
    if($p_org!=''){
      $acttObj->insert('subsidiaries', array("parent_comp" => $p_org, "child_comp" => $edit_id));
      $acttObj->editFun($table, $edit_id, 'comp_nature', 3);  
    }
  }
  if (isset($_POST['po_req'])) {
    $acttObj->editFun($table, $edit_id, 'po_req', 1);
    if ($_POST['po_email']) {
      $po_email = $_POST['po_email'];
    } else {
      $po_email = $_POST['email'];
    }
    $acttObj->editFun($table, $edit_id, 'po_email', $po_email);
  } else {
    $acttObj->editFun($table, $edit_id, 'po_req', 0);
    $acttObj->editFun($table, $edit_id, 'po_email', '');
  }
  $data = $_POST['note'];
  $acttObj->editFun($table, $edit_id, 'note', $data);
  if (isset($_POST['admin_ch'])) {
    $acttObj->editFun($table, $edit_id, 'admin_ch', '1');
    $admin_rate = $_POST['admin_rate'];
    $acttObj->editFun($table, $edit_id, 'admin_rate', $admin_rate);
  }
  if (isset($_POST['tr_time'])) {
    $acttObj->editFun($table, $edit_id, 'tr_time', '1');
  }
  if (isset($_POST['interp_time'])) {
    $acttObj->editFun($table, $edit_id, 'interp_time', '1');
  }
  if (isset($_POST['wait_time'])) {
    $acttObj->editFun($table, $edit_id, 'wait_time', '1');
  }

  if ($_SESSION['Temp'] == 1) {
    $acttObj->editFun($table, $edit_id, 'is_temp', '1');
  }
  $acttObj->editFun($table, $edit_id, 'sbmtd_by', ucwords($_SESSION['UserName']));
  echo "<script>alert('New company registered successfully.');</script>";
  $acttObj->editFun($table, $edit_id, 'edited_by', $_SESSION['UserName']);
  $acttObj->editFun($table, $edit_id, 'edited_date', date("Y-m-d H:i:s"));
  $acttObj->new_old_table('hist_' . $table, $table, $edit_id);
  $comp_id = $edit_id;
  $table = "company_login";
  $edit_id = $acttObj->get_id($table);
  $acttObj->editFun($table, $edit_id, 'company_id', $comp_id);
  $acttObj->editFun($table, $edit_id, 'orgName', $_POST['abrv']);
  $acttObj->editFun($table, $edit_id, 'email', $_POST['email']);
  $g_password =  substr(md5(time()), 0, 8);
  $acttObj->editFun($table, $edit_id, 'paswrd', $g_password);
  $acttObj->editFun($table, $edit_id, 'prvlg', 0);
  $acttObj->editFun($table, $edit_id, 'dated', date('Y-m-d'));
  $subject = "Welcome to LSUK - Account Details";
  $message = "
            <p>Hi</p>
            <p>Please use the below credentials to login into your LSUK portal:</p>
            <p>Email: " . $_POST['email'] . "<br />Password: " . $g_password . " </p>

            <!-- Button Table for Maximum Email Client Compatibility -->
            <table border='0' cellspacing='0' cellpadding='0'>
            <tr>
            <td align='center' bgcolor='#007BFF' style='border-radius:5px;'>
                <a href='https://lsuk.org/cust_login.php' 
                  target='_blank' 
                  style='font-size:16px;font-family:Arial,sans-serif;color:#ffffff;
                          text-decoration:none;padding:12px 24px;display:inline-block;'>
                  Login Now
                </a>
            </td>
            </tr>
            </table>

            <p style='margin-top:15px;'>
            If the button above does not work, please use this link:<br />
            <a href='https://lsuk.org/cust_login.php'>https://lsuk.org/cust_login.php</a>
            </p>

            <p>Best Regards,<br /><br /></p>
            <p><strong>LSUK Limited</strong></p>
            <p>
            Landline: 01173290610<br />
            Mobile: 07915177068<br />
            Office Address: Suite 3 Davis House<br />
            Lodge Causeway Trading estate<br />
            Lodge Causeway - Fishponds<br />
            Bristol BS16 3JB<br />
            Opening Hours: Monday - Friday 09AM to 5PM
            </p>";

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
    $mail->addAddress($_POST['email']);
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
  <?php
  }
  //$acttObj->editFun($table, $edit_id, 'comp_type', );
  //$acttObj->editFun($table, $edit_id, 'deleted_flag', );
  ?>
 <script>
    window.onunload = refreshParent;

    function refreshParent() {
      window.opener.location.href += "?abrv=<?php echo $_POST['abrv']; ?>&orgname=<?php echo $_POST['name']; ?>&refreshedid=<?php echo $edit_id; ?>";
      window.opener.location.reload();
    }
  </script>
<?php } ?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
  <title>Company Registration Form</title>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
  <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" />
  <link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.13/css/bootstrap-multiselect.css" rel="stylesheet" type="text/css" />
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
    <form action="" method="post" class="register" enctype="multipart/form-data">
      <div style="background: #8c8c86;padding: 6px;position: fixed;z-index: 999999999999;width: 100%;color: white;">
        <b>
          <h4 style="display: inline-block;">Company Registration <span class="hidden-xs">Details</span></h4>
        </b>
        <button class="btn btn-info pull-right" style="border-color: #000000;color: black;text-transform: uppercase;margin: 7px 21px;font-size: 16px;font-weight: bold;box-shadow: 2px 2px 2px #c5c5a3;" type="submit" name="submit">REGISTER NOW &raquo;</button>
      </div><br><br><br><br>
      <div class="form-group col-md-3 col-sm-6">
        <input placeholder="Company Name *" name="name" class="form-control " type="text" required='' id="name" onchange="uniqueFun(this.value,'comp_reg','name',$(this).attr('id') );" onBlur="uniqueFun($('#abrv').val(),'comp_reg','abrv',$('#abrv').attr('id') );" oninput="short_name();" />
      </div>

      <div class="form-group col-md-3 col-sm-6">
          <?php
          $sql_opt = "SELECT DISTINCT id,name,abrv from comp_reg WHERE comp_nature IN (1,4) ORDER BY name ASC"; ?>
          <select id="p_org" name="p_org" class="form-control searchable">
              <?php $result_opt = mysqli_query($con, $sql_opt);
              $options = "";
              while ($row_opt = mysqli_fetch_array($result_opt)) {
                  $comp_id = $row_opt["id"];
                  $code = $row_opt["abrv"];
                  $name_opt = $row_opt["name"];
                  $options .= "<OPTION value='$comp_id' " . ($comp_id == $p_org ? 'selected' : '') . ">" . $name_opt . ' (' . $code . ')';
              }
              ?>
              <option value="">Select Parent/Head Units (if any)</option>
              <?php echo $options; ?>
              </option>
          </select>
        </div>
      
      <div class="form-group col-md-3 col-sm-6">
        <input placeholder="Contact Person *" name="contactPerson" class="form-control " type="text" id="contactPerson" required='' />

      </div>
      <div class="form-group col-md-3 col-sm-6">
        <select name="payment_terms" id="payment_terms" class="form-control">
          <option selected required disabled value="">Select Payment Terms</option>
          <option value="0">Pay Now</option>
          <option value="7">7 Days</option>
          <option value="14">14 Days</option>
          <option value="21">21 Days</option>
          <option value="28">28 Days</option>
          <option value="30">30 Days</option>
          <option value="35">35 Days</option>
          <option value="42">42 Days</option>
          <option value="49">49 Days</option>
          <option value="56">56 Days</option>
          <option value="63">63 Days</option>
          <option value="63">63 Days</option>
          <option value="70">70 Days</option>
          <option value="77">77 Days</option>
          <option value="84">84 Days</option>
          <option value="91">91 Days</option>
          <option value="98">98 Days</option>
        </select>
      </div>
      <div class="form-group col-md-3 col-sm-6">
        <input placeholder="Branch or Department" name="bod" type="text" id="bod" class="form-control" />
      </div>
      <div class="form-group col-md-3 col-sm-6">
        <select name="type_id" id="type_id" class="form-control" required>
          <?php
          $get_types = $acttObj->read_all("comp_type.id,comp_type.title,company_types.title as group_name", "comp_type,company_types", "comp_type.company_type_id=company_types.id ORDER BY comp_type.title ASC");?>
          <option value="" disabled>Select Company Type</option>
          <?php while ($row_opt = $get_types->fetch_assoc()) {
            echo "<option value='" . $row_opt['id'] . "'>" . $row_opt['title'] . " (" . $row_opt['group_name'] . ")</option>";
          }
          ?>
        </select>
      </div>
      <div class="form-group col-md-3 col-sm-6">
        <input name="crn" type="text" id="crn" class="form-control" required='' placeholder="Company Registration Number" />
      </div>
      <div class="form-group col-md-3 col-sm-6">
        <input name="vn" type="text" id="vn" class="form-control" placeholder="VAT Number *" />
      </div>
      <div class="form-group col-md-3 col-sm-6">
        <input placeholder="Abrivaiton of Company  *" name="abrv" class="form-control valid2" type="text" id="abrv" required='' onBlur="uniqueFun(this.value,'comp_reg','abrv',$(this).attr('id') );" minlength="4" maxlength="7" />
      </div>
      <div class="form-group col-md-3 col-sm-6">
        <input name="contactNo1" type="text" id="contactNo1" required='' class="form-control" placeholder="Contact No 1 *" />
      </div>
      <div class="form-group col-md-3 col-sm-6">
        <input placeholder="Contact No 2" name="contactNo2" type="text" id="contactNo2" class="form-control" />
      </div>
      <div class="form-group col-md-3 col-sm-6">
        <input placeholder="Company Fax No" name="contactNo3" type="text" id="contactNo3" class="form-control" />
      </div>
      <div class="form-group col-md-3 col-sm-6">
        <input name="web" type="text" id="web" class="form-control" placeholder="Company Website" />
      </div>
      <div class="form-group col-md-3 col-sm-6">
        <input name="email" type="text" id="email" class="form-control" required='' placeholder="Email Address *" />
      </div>
      <div class="form-group col-md-3 col-sm-6">
        <input name="credit_limit" type="number" id="credit_limit" class="form-control" required='' placeholder="Credit Limit in £ *" />
      </div>
      <div class="form-group col-md-3 col-sm-6">
          <label>Mileage </label>
            <input name="mileage" type="text" id="mileage" class="form-control" required='' placeholder="Enter Mileage for Hourly Cost Package" />
          </div>
          
          <div class="form-group col-md-3 col-sm-6">
          <label>Travel Time (in hours)</label>
            <input name="travel_time" type="text" id="travel_time" class="form-control" required='' placeholder="Enter Travel Time for Hourly Cost Package" />
          </div>
      <div class="bg-info col-xs-12 form-group">
        <h4>Invoicing Address (Team or Unit Address)</h4>
      </div>
      <!-- <p>
      <label class="row1"><em>Authorised Person Name
      </em></label>
<input name="aupn" type="text" placeholder='' required />
                <?php //if(isset($_POST['submit'])){$aupn=$_POST['aupn'];$acttObj->editFun($table,$edit_id,'aupn',$aupn);} 
                ?>
    </p>
        <p>
      <label>Position in the Company
      </label>
                    <input name="pitc" type="text" placeholder='' />
                <?php //if(isset($_POST['submit'])){$pitc=$_POST['pitc'];$acttObj->editFun($table,$edit_id,'pitc',$pitc);} 
                ?>
    </p>-->
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
      <div class="form-group col-md-3 append_cities hidden"></div>
      <div class="form-group col-md-3 div_other_city_field hidden">
        <label>Enter City Name *</label>
        <input name="city" type="text" class="form-control mt other_city_field hidden" placeholder="Enter City Name" />
      </div>
      <div class="form-group col-md-3 col-sm-6">
        <label>Post Code</label>
        <input name="postCode" type="text" class="form-control" placeholder="Post Code" />
      </div>
      <div class="form-group col-md-3 col-sm-6">
        <label>Building Number / Name</label>
        <input name="buildingName" type="text" class="form-control" placeholder="Building Number / Name" />
      </div>
      <div class="form-group col-md-3 col-sm-6">
        <label>Address Line 1</label>
        <input name="line1" type="text" required='' class="form-control" placeholder="Address Line 1" />
      </div>
      <div class="form-group col-md-3 col-sm-6">
        <label>Address Line 2</label>
        <input name="line2" type="text" class="form-control" placeholder="Address Line 2" />
      </div>
      <div class="form-group col-md-3 col-sm-6">
        <label>Address Line 3</label>
        <input name="streetRoad" type="text" class="form-control" placeholder="Address Line 3" />
      </div>
      <div class="form-group col-md-3 col-sm-6">
        <label>Invoicing Email Address</label>
        <input name="invEmail" type="text" id="invEmail" class="form-control" required='' placeholder="Invoicing Email Address" />
      </div>
      <!-- <p>
                  <label class="optional">Company Invoicing Address 
           	  </label>
                  <input name="invAddrs" type="text"  required='' />
              <?php //if(isset($_POST['submit'])){$c17=$_POST['invAddrs'];$acttObj->editFun($table,$edit_id,'invAddrs',$c17);} 
              ?>
    </p>-->
      <div class="bg-info col-xs-12 form-group">
        <h4>Trading Address (Team or Unit Address)</h4>
      </div>
      <div class="form-group col-md-3 col-sm-6">
        <input class="form-control " name="taupn" type="text" id="taupn" placeholder="Authorised Person Name" />
      </div>
      <div class="form-group col-md-3 col-sm-6">
        <input name="tpitc" type="text" id="tpitc" class="form-control" placeholder="Position in the Company" />
      </div>
      <div class="form-group col-md-3 col-sm-6">
        <select onchange="other_tcity(this)" name="selected_tcity" id="selected_tcity" class="form-control"></select>
      </div>
      <div class="form-group col-md-3 div_other_tcity_field hidden">
        <input name="tcity" type="text" class="form-control mt other_tcity_field hidden" placeholder="Enter City Name" />
      </div>
      <div class="form-group col-md-3 col-sm-6">
        <input name="tpostCode" type="text" id="tpostCode" class="form-control" placeholder="Post Code" />
      </div>
      <div class="form-group col-md-3 col-sm-6">
        <input name="tbuildingName" type="text" id="tbuildingName" class="form-control" placeholder="Building Number / Name" />
      </div>
      <div class="form-group col-md-3 col-sm-6">
        <input class="form-control" name="tline1" type="text" id="tline1" placeholder="Address Line 1" />
      </div>
      <div class="form-group col-md-3 col-sm-6">
        <input class="form-control" name="tline2" type="text" id="tline2" placeholder="Address Line 2" />
      </div>
      <div class="form-group col-md-3 col-sm-6">
        <input class="form-control" name="tstreetRoad" type="text" id="tstreetRoad" placeholder="Address Line 3" />
      </div>
      <div class="form-group col-md-3 col-sm-6">
        <input name="tcn" type="text" id="tcn" class="form-control " placeholder="Contact Name" />
      </div>
      <div class="form-group col-md-3 col-sm-6">
        <input name="rff" type="text" id="rff" class="form-control" placeholder="Registration Form Filled" onfocus="(this.type='date')" onblur="if(!this.value){this.type='text';}" />
      </div>
      <div class="form-group col-sm-6">
        <textarea placeholder="Notes for Company ..." class="form-control" name="note" rows="2"></textarea>
      </div>
      <div class="bg-info col-xs-12 form-group">
        <h4>Extra Attributes For Company</h4>
      </div>
      <div class="form-group col-md-3 col-sm-4">
        <label class="checkbox-inline">
          <input onchange="if(this.checked) {$('#div_po_email').removeClass('hidden');}else{$('#div_po_email').addClass('hidden');}" type="checkbox" id="po_req" name="po_req" value="1" data-toggle="toggle" data-on="Yes" data-off="No"> <b>Purchase Order ?</b>
        </label>
      </div>
      <div class="form-group col-md-3 col-sm-4 hidden" id="div_po_email">
        <input name="po_email" type="text" id="po_email" class="form-control" placeholder="Enter Purchase Order Email" />
      </div>
      <div class="form-group col-md-3 col-sm-4">
        <label class="checkbox-inline">
          <input onchange="if(this.checked) {$('#div_admin_rate').removeClass('hidden');}else{$('#div_admin_rate').addClass('hidden');}" type="checkbox" id="admin_ch" name="admin_ch" value="1" data-toggle="toggle" data-on="Yes" data-off="No"> <b>Admin Charge ?</b></label>
      </div>
      <div class="form-group col-md-3 col-sm-4 hidden" id="div_admin_rate">
        <input name="admin_rate" type="text" id="admin_rate" class="form-control" placeholder="Rate For Admin Charge" />
      </div>
      <div class="form-group col-md-3 col-sm-4">
        <label class="checkbox-inline">
          <input type="checkbox" id="tr_time" name="tr_time" value="1" data-toggle="toggle" data-on="Yes" data-off="No"> <b>Travel Time ?</b></label>
      </div>
      <div class="form-group col-md-3 col-sm-4">
        <label class="checkbox-inline">
          <input type="checkbox" id="interp_time" name="interp_time" value="1" data-toggle="toggle" data-on="Yes" data-off="No"> <b>Interpreting Time ?</b></label>
      </div>
      <div class="form-group col-md-3 col-sm-4">
        <label class="checkbox-inline">
          <input type="checkbox" id="wait_time" name="wait_time" value="1" data-toggle="toggle" data-on="Yes" data-off="No"> <b>Waiting Time ?</b></label>
      </div>
    </form>
  </div>
  <script src="js/jquery-1.11.3.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
  <script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.13/js/bootstrap-multiselect.js" type="text/javascript"></script>
  <script>
    $(function() {
      $('.searchable').multiselect({
          includeSelectAllOption: true,
          numberDisplayed: 1,
          enableFiltering: true,
          enableCaseInsensitiveFiltering: true
      });

      $('.multi_class').multiselect({
        buttonWidth: '100px',
        includeSelectAllOption: true,
        numberDisplayed: 1,
        enableFiltering: true,
        enableCaseInsensitiveFiltering: true
      });
    });
    $(".valid").bind('keypress paste', function(e) {
      var regex = RegExp(/[a-z A-Z 0-9 ()]/);
      var str = String.fromCharCode(!e.charCode ? e.which : e.charCode);
      if (!regex.test(str)) {
        e.preventDefault();
        return false;
      }
    });
    $(".valid2").bind('keypress paste', function(e) {
      var regex = new RegExp(/[a-zA-Z0-9()]/);
      var str = String.fromCharCode(!e.charCode ? e.which : e.charCode);
      if (!regex.test(str)) {
        e.preventDefault();
        return false;
      }
    });

    function short_name() {
      var arrValues = $('#name').val().split(' ');
      var short_name = '';
      // Loop over each value in the array.
      $.each(arrValues, function(intIndex, objValue) {
        if (short_name.length < 7) {
          short_name += objValue.substring(0, 1);
        }
      })
      $('#abrv').val(short_name.toUpperCase());
    }

    function other_city(elem) {
      var selected_city = $(elem).val();
      if (selected_city != 'Not in List') {
        $('.other_city_field').val(selected_city);
      }
      if (selected_city == 'Not in List') {
        $('.other_city_field').val('');
        $(elem).removeAttr("required");
        $('.div_other_city_field,.other_city_field').removeClass('hidden');
        $('.other_city_field').attr('required', "required");
        $('.other_city_field').focus();
      } else {
        $(elem).attr('required', "required");
        $('.div_other_city_field,.other_city_field').addClass('hidden');
        $('.other_city_field').removeAttr("required");
        $('#selected_city').focus();
      }
    }

    function get_cities(elem) {
      $('.div_other_city_field,.other_city_field,.div_other_tcity_field,.other_tcity_field').addClass('hidden');
      $('.other_city_field,.other_tcity_field').val("");
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
              $("#selected_tcity").html($("#selected_city").html());
            } else {
              $('.append_cities').addClass('hidden');
              alert("Something went wrong. Try again!");
            }
          },
          error: function(xhr) {
            alert("An error occured: " + xhr.status + " " + xhr.statusText);
          }
        });
      }
    }

    function other_tcity(elem) {
      var selected_city = $(elem).val();
      if (selected_city != 'Not in List') {
        $('.other_tcity_field').val(selected_city);
      }
      if (selected_city == 'Not in List') {
        $('.other_tcity_field').val('');
        $(elem).removeAttr("required");
        $('.div_other_tcity_field,.other_tcity_field').removeClass('hidden');
        $('.other_tcity_field').attr('required', "required");
        $('.other_tcity_field').focus();
      } else {
        $(elem).attr('required', "required");
        $('.div_other_tcity_field,.other_tcity_field').addClass('hidden');
        $('.other_tcity_field').removeAttr("required");
        $('#selected_tcity').focus();
      }
    }
  </script>
</body>

</html>