<?php
if (session_id() == '' || !isset($_SESSION)) {
  session_start();
}
include "userhaspage.php";
SysPermiss::UserHasPage(__FILE__);
include 'db.php';
include 'class.php';
$edit_id = "";
if (isset($_POST['submit'])) {
  $table = 'sup_reg';
  $edit_id = $acttObj->get_id($table);
  //BASIC
  $sp_name = $_POST['sp_name'];
  $acttObj->editFun($table, $edit_id, 'sp_name', $sp_name);
  $sp_abrv = $_POST['sp_abrv'];
  $acttObj->editFun($table, $edit_id, 'sp_abrv', $sp_abrv);
  $sp_rnum = $_POST['sp_rnum'];
  $acttObj->editFun($table, $edit_id, 'sp_rnum', $sp_rnum);
  $sp_contact = $_POST['sp_contact'];
  $acttObj->editFun($table, $edit_id, 'sp_contact', $sp_contact);
  $sp_email = $_POST['sp_email'];
  $acttObj->editFun($table, $edit_id, 'sp_email', $sp_email);
  $sp_type = $_POST['sp_type'];
  $acttObj->editFun($table, $edit_id, 'sp_type', $sp_type);
  $sp_web = $_POST['sp_web'];
  $acttObj->editFun($table, $edit_id, 'sp_web', $sp_web);
  $sp_web = $_POST['sp_web'];
  $acttObj->editFun($table, $edit_id, 'sp_web', $sp_web);


  //VAT INFO
  $tax_reg = $_POST['tax_reg'];
  $acttObj->editFun($table, $edit_id, 'tax_reg', $tax_reg);
  $uk_citizen = $_POST['uk_citizen'];
  $acttObj->editFun($table, $edit_id, 'uk_citizen', $uk_citizen);
  $uk_citizen_vatNum = $_POST['uk_citizen_vatNum'];
  $acttObj->editFun($table, $edit_id, 'uk_citizen_vatNum', $uk_citizen_vatNum);
  $country_vat = $_POST['country_vat'];
  $acttObj->editFun($table, $edit_id, 'country_vat', $country_vat);
  $country_vatNum = $_POST['country_vatNum'];
  $acttObj->editFun($table, $edit_id, 'country_vatNum', $country_vatNum);

  //ADDRESS
  $sp_country = $_POST['sp_country'];
  $acttObj->editFun($table, $edit_id, 'sp_country', $sp_country);
  $sp_city = $_POST['sp_city'];
  $acttObj->editFun($table, $edit_id, 'sp_city', $sp_city);
  $sp_postCode = $_POST['sp_postCode'];
  $acttObj->editFun($table, $edit_id, 'sp_postCode', $sp_postCode);
  $sp_buildingName = $_POST['sp_buildingName'];
  $acttObj->editFun($table, $edit_id, 'sp_buildingName', $sp_buildingName);
  $sp_streetRoad = $_POST['sp_streetRoad'];
  $acttObj->editFun($table, $edit_id, 'sp_streetRoad', $sp_streetRoad);
  $sp_line1 = $_POST['sp_line1'];
  $acttObj->editFun($table, $edit_id, 'sp_line1', $sp_line1);
  $sp_line2 = $_POST['sp_line2'];
  $acttObj->editFun($table, $edit_id, 'sp_line2', $sp_line2);

  //BANK DETAILS
  $sp_bnkName = $_POST['sp_bnkName'];
  $acttObj->editFun($table, $edit_id, 'sp_bnkName', $sp_bnkName);
  $sp_acName = $_POST['sp_acName'];
  $acttObj->editFun($table, $edit_id, 'sp_acName', $sp_acName);
  $sp_acNum = $_POST['sp_acNum'];
  $acttObj->editFun($table, $edit_id, 'sp_acNum', $sp_acNum);
  $sp_sCode = $_POST['sp_sCode'];
  $acttObj->editFun($table, $edit_id, 'sp_sCode', $sp_sCode);

  // CONTACT PERSON DETAILS
  $sp_cpName = $_POST['sp_cpName'];
  $acttObj->editFun($table, $edit_id, 'sp_cpName', $sp_cpName);
  $sp_cppos = $_POST['sp_cppos'];
  $acttObj->editFun($table, $edit_id, 'sp_cppos', $sp_cppos);
  $sp_cpNum = $_POST['sp_cpNum'];
  $acttObj->editFun($table, $edit_id, 'sp_cpNum', $sp_cpNum);
  $sp_cpEmail = $_POST['sp_cpEmail'];
  $acttObj->editFun($table, $edit_id, 'sp_cpEmail', $sp_cpEmail);
  $acttObj->editFun($table, $edit_id, 'sbmtd_by', ucwords($_SESSION['UserName']));


  //   if ($_SESSION['Temp'] == 1) {
  //     $acttObj->editFun($table, $edit_id, 'is_temp', '1');
  //   }

  echo "<script>alert('New Supplier registered successfully.');</script>";
  //   $acttObj->editFun($table, $edit_id, 'edited_by', $_SESSION['UserName']);
  //   $acttObj->editFun($table, $edit_id, 'edited_date', date("Y-m-d H:i:s"));
  // $acttObj->new_old_table('hist_' . $table, $table, $edit_id); 
?>
  <script>
    window.onunload = refreshParent;

    function refreshParent() {
      window.opener.location.href += "?abrv=<?php echo $_POST['abrv']; ?>&orgname=<?php echo $_POST['name']; ?>&refreshedid=<?php echo $edit_id; ?>";
      window.opener.location.reload();
    }
  </script>
<?php
}
$country_array = array(
  "Afghanistan" => "Afghanistan (افغانستان)", "Aland Islands" => "Aland Islands (Åland)", "Albania" => "Albania (Shqipëria)", "Algeria" => "Algeria (الجزائر)", "American Samoa" => "American Samoa (American Samoa)", "Andorra" => "Andorra (Andorra)", "Angola" => "Angola (Angola)", "Anguilla" => "Anguilla (Anguilla)", "Antarctica" => "Antarctica (Antarctica)", "Antigua And Barbuda" => "Antigua And Barbuda (Antigua and Barbuda)", "Argentina" => "Argentina (Argentina)", "Armenia" => "Armenia (Հայաստան)", "Aruba" => "Aruba (Aruba)", "Australia" => "Australia (Australia)", "Austria" => "Austria (Österreich)", "Azerbaijan" => "Azerbaijan (Azərbaycan)", "Bahamas The" => "Bahamas The (Bahamas)", "Bahrain" => "Bahrain (‏البحرين)", "Bangladesh" => "Bangladesh (Bangladesh)", "Barbados" => "Barbados (Barbados)", "Belarus" => "Belarus (Белару́сь)", "Belgium" => "Belgium (België)", "Belize" => "Belize (Belize)", "Benin" => "Benin (Bénin)", "Bermuda" => "Bermuda (Bermuda)", "Bhutan" => "Bhutan (ʼbrug-yul)", "Bolivia" => "Bolivia (Bolivia)", "Bonaire, Sint Eustatius and Saba" => "Bonaire, Sint Eustatius and Saba (Caribisch Nederland)", "Bosnia and Herzegovina" => "Bosnia and Herzegovina (Bosna i Hercegovina)", "Botswana" => "Botswana (Botswana)", "Bouvet Island" => "Bouvet Island (Bouvetøya)", "Brazil" => "Brazil (Brasil)", "British Indian Ocean Territory" => "British Indian Ocean Territory (British Indian Ocean Territory)", "Brunei" => "Brunei (Negara Brunei Darussalam)", "Bulgaria" => "Bulgaria (България)", "Burkina Faso" => "Burkina Faso (Burkina Faso)", "Burundi" => "Burundi (Burundi)", "Cambodia" => "Cambodia (Kâmpŭchéa)", "Cameroon" => "Cameroon (Cameroon)", "Canada" => "Canada (Canada)", "Cape Verde" => "Cape Verde (Cabo Verde)", "Cayman Islands" => "Cayman Islands (Cayman Islands)", "Central African Republic" => "Central African Republic (Ködörösêse tî Bêafrîka)", "Chad" => "Chad (Tchad)", "Chile" => "Chile (Chile)", "China" => "China (中国)", "Christmas Island" => "Christmas Island (Christmas Island)", "Cocos (Keeling) Islands" => "Cocos (Keeling) Islands (Cocos (Keeling) Islands)", "Colombia" => "Colombia (Colombia)", "Comoros" => "Comoros (Komori)", "Congo" => "Congo (République du Congo)", "Congo The Democratic Republic Of The" => "Congo The Democratic Republic Of The (République démocratique du Congo)", "Cook Islands" => "Cook Islands (Cook Islands)", "Costa Rica" => "Costa Rica (Costa Rica)", "Cote D'Ivoire (Ivory Coast)" => "Cote D'Ivoire (Ivory Coast) ()", "Croatia (Hrvatska)" => "Croatia (Hrvatska) (Hrvatska)", "Cuba" => "Cuba (Cuba)", "Curaçao" => "Curaçao (Curaçao)", "Cyprus" => "Cyprus (Κύπρος)", "Czech Republic" => "Czech Republic (Česká republika)", "Denmark" => "Denmark (Danmark)", "Djibouti" => "Djibouti (Djibouti)", "Dominica" => "Dominica (Dominica)", "Dominican Republic" => "Dominican Republic (República Dominicana)", "East Timor" => "East Timor (Timor-Leste)", "Ecuador" => "Ecuador (Ecuador)", "Egypt" => "Egypt (مصر‎)", "El Salvador" => "El Salvador (El Salvador)", "Equatorial Guinea" => "Equatorial Guinea (Guinea Ecuatorial)", "Eritrea" => "Eritrea (ኤርትራ)", "Estonia" => "Estonia (Eesti)", "Ethiopia" => "Ethiopia (ኢትዮጵያ)", "Falkland Islands" => "Falkland Islands (Falkland Islands)", "Faroe Islands" => "Faroe Islands (Føroyar)", "Fiji Islands" => "Fiji Islands (Fiji)", "Finland" => "Finland (Suomi)", "France" => "France (France)", "French Guiana" => "French Guiana (Guyane française)", "French Polynesia" => "French Polynesia (Polynésie française)", "French Southern Territories" => "French Southern Territories (Territoire des Terres australes et antarctiques fr)", "Gabon" => "Gabon (Gabon)", "Gambia The" => "Gambia The (Gambia)", "Georgia" => "Georgia (საქართველო)", "Germany" => "Germany (Deutschland)", "Ghana" => "Ghana (Ghana)", "Gibraltar" => "Gibraltar (Gibraltar)", "Greece" => "Greece (Ελλάδα)", "Greenland" => "Greenland (Kalaallit Nunaat)", "Grenada" => "Grenada (Grenada)", "Guadeloupe" => "Guadeloupe (Guadeloupe)", "Guam" => "Guam (Guam)", "Guatemala" => "Guatemala (Guatemala)", "Guernsey and Alderney" => "Guernsey and Alderney (Guernsey)", "Guinea" => "Guinea (Guinée)", "Guinea-Bissau" => "Guinea-Bissau (Guiné-Bissau)", "Guyana" => "Guyana (Guyana)", "Haiti" => "Haiti (Haïti)", "Heard Island and McDonald Islands" => "Heard Island and McDonald Islands (Heard Island and McDonald Islands)", "Honduras" => "Honduras (Honduras)", "Hong Kong S.A.R." => "Hong Kong S.A.R. (香港)", "Hungary" => "Hungary (Magyarország)", "Iceland" => "Iceland (Ísland)", "India" => "India (भारत)", "Indonesia" => "Indonesia (Indonesia)", "Iran" => "Iran (ایران)", "Iraq" => "Iraq (العراق)", "Ireland" => "Ireland (Éire)", "Israel" => "Israel (יִשְׂרָאֵל)", "Italy" => "Italy (Italia)", "Jamaica" => "Jamaica (Jamaica)", "Japan" => "Japan (日本)", "Jersey" => "Jersey (Jersey)", "Jordan" => "Jordan (الأردن)", "Kazakhstan" => "Kazakhstan (Қазақстан)", "Kenya" => "Kenya (Kenya)", "Kiribati" => "Kiribati (Kiribati)", "Korea North" => "Korea North (북한)", "Korea South" => "Korea South (대한민국)", "Kosovo" => "Kosovo (Republika e Kosovës)", "Kuwait" => "Kuwait (الكويت)", "Kyrgyzstan" => "Kyrgyzstan (Кыргызстан)", "Laos" => "Laos (ສປປລາວ)", "Latvia" => "Latvia (Latvija)", "Lebanon" => "Lebanon (لبنان)", "Lesotho" => "Lesotho (Lesotho)", "Liberia" => "Liberia (Liberia)", "Libya" => "Libya (‏ليبيا)", "Liechtenstein" => "Liechtenstein (Liechtenstein)", "Lithuania" => "Lithuania (Lietuva)", "Luxembourg" => "Luxembourg (Luxembourg)", "Macau S.A.R." => "Macau S.A.R. (澳門)", "Macedonia" => "Macedonia (Северна Македонија)", "Madagascar" => "Madagascar (Madagasikara)", "Malawi" => "Malawi (Malawi)", "Malaysia" => "Malaysia (Malaysia)", "Maldives" => "Maldives (Maldives)", "Mali" => "Mali (Mali)", "Malta" => "Malta (Malta)", "Man (Isle of)" => "Man (Isle of) (Isle of Man)", "Marshall Islands" => "Marshall Islands (M̧ajeļ)", "Martinique" => "Martinique (Martinique)", "Mauritania" => "Mauritania (موريتانيا)", "Mauritius" => "Mauritius (Maurice)", "Mayotte" => "Mayotte (Mayotte)", "Mexico" => "Mexico (México)", "Micronesia" => "Micronesia (Micronesia)", "Moldova" => "Moldova (Moldova)", "Monaco" => "Monaco (Monaco)", "Mongolia" => "Mongolia (Монгол улс)", "Montenegro" => "Montenegro (Црна Гора)", "Montserrat" => "Montserrat (Montserrat)", "Morocco" => "Morocco (المغرب)", "Mozambique" => "Mozambique (Moçambique)", "Myanmar" => "Myanmar (မြန်မာ)", "Namibia" => "Namibia (Namibia)", "Nauru" => "Nauru (Nauru)", "Nepal" => "Nepal (नपल)", "Netherlands The" => "Netherlands The (Nederland)", "New Caledonia" => "New Caledonia (Nouvelle-Calédonie)", "New Zealand" => "New Zealand (New Zealand)", "Nicaragua" => "Nicaragua (Nicaragua)", "Niger" => "Niger (Niger)", "Nigeria" => "Nigeria (Nigeria)", "Niue" => "Niue (Niuē)", "Norfolk Island" => "Norfolk Island (Norfolk Island)", "Northern Mariana Islands" => "Northern Mariana Islands (Northern Mariana Islands)", "Norway" => "Norway (Norge)", "Oman" => "Oman (عمان)", "Pakistan" => "Pakistan (پاکستان)", "Palau" => "Palau (Palau)", "Palestinian Territory Occupied" => "Palestinian Territory Occupied (فلسطين)", "Panama" => "Panama (Panamá)", "Papua new Guinea" => "Papua new Guinea (Papua Niugini)", "Paraguay" => "Paraguay (Paraguay)", "Peru" => "Peru (Perú)", "Philippines" => "Philippines (Pilipinas)", "Pitcairn Island" => "Pitcairn Island (Pitcairn Islands)", "Poland" => "Poland (Polska)", "Portugal" => "Portugal (Portugal)", "Puerto Rico" => "Puerto Rico (Puerto Rico)", "Qatar" => "Qatar (قطر)", "Reunion" => "Reunion (La Réunion)", "Romania" => "Romania (România)", "Russia" => "Russia (Россия)", "Rwanda" => "Rwanda (Rwanda)", "Saint Helena" => "Saint Helena (Saint Helena)", "Saint Kitts And Nevis" => "Saint Kitts And Nevis (Saint Kitts and Nevis)", "Saint Lucia" => "Saint Lucia (Saint Lucia)", "Saint Pierre and Miquelon" => "Saint Pierre and Miquelon (Saint-Pierre-et-Miquelon)", "Saint Vincent And The Grenadines" => "Saint Vincent And The Grenadines (Saint Vincent and the Grenadines)", "Saint-Barthelemy" => "Saint-Barthelemy (Saint-Barthélemy)", "Saint-Martin (French part)" => "Saint-Martin (French part) (Saint-Martin)", "Samoa" => "Samoa (Samoa)", "San Marino" => "San Marino (San Marino)", "Sao Tome and Principe" => "Sao Tome and Principe (São Tomé e Príncipe)", "Saudi Arabia" => "Saudi Arabia (العربية السعودية)", "Senegal" => "Senegal (Sénégal)", "Serbia" => "Serbia (Србија)", "Seychelles" => "Seychelles (Seychelles)", "Sierra Leone" => "Sierra Leone (Sierra Leone)", "Singapore" => "Singapore (Singapore)", "Sint Maarten (Dutch part)" => "Sint Maarten (Dutch part) (Sint Maarten)", "Slovakia" => "Slovakia (Slovensko)", "Slovenia" => "Slovenia (Slovenija)", "Solomon Islands" => "Solomon Islands (Solomon Islands)", "Somalia" => "Somalia (Soomaaliya)", "South Africa" => "South Africa (South Africa)", "South Georgia" => "South Georgia (South Georgia)", "South Sudan" => "South Sudan (South Sudan)", "Spain" => "Spain (España)", "Sri Lanka" => "Sri Lanka (śrī laṃkāva)", "Sudan" => "Sudan (السودان)", "Suriname" => "Suriname (Suriname)", "Svalbard And Jan Mayen Islands" => "Svalbard And Jan Mayen Islands (Svalbard og Jan Mayen)", "Swaziland" => "Swaziland (Swaziland)", "Sweden" => "Sweden (Sverige)", "Switzerland" => "Switzerland (Schweiz)", "Syria" => "Syria (سوريا)", "Taiwan" => "Taiwan (臺灣)", "Tajikistan" => "Tajikistan (Тоҷикистон)", "Tanzania" => "Tanzania (Tanzania)", "Thailand" => "Thailand (ประเทศไทย)", "Togo" => "Togo (Togo)", "Tokelau" => "Tokelau (Tokelau)", "Tonga" => "Tonga (Tonga)", "Trinidad And Tobago" => "Trinidad And Tobago (Trinidad and Tobago)", "Tunisia" => "Tunisia (تونس)", "Turkey" => "Turkey (Türkiye)", "Turkmenistan" => "Turkmenistan (Türkmenistan)", "Turks And Caicos Islands" => "Turks And Caicos Islands (Turks and Caicos Islands)", "Tuvalu" => "Tuvalu (Tuvalu)", "Uganda" => "Uganda (Uganda)", "Ukraine" => "Ukraine (Україна)", "United Arab Emirates" => "United Arab Emirates (دولة الإمارات العربية المتحدة)", "United Kingdom" => "United Kingdom (United Kingdom)", "United States" => "United States (United States)", "United States Minor Outlying Islands" => "United States Minor Outlying Islands (United States Minor Outlying Islands)", "Uruguay" => "Uruguay (Uruguay)", "Uzbekistan" => "Uzbekistan (O‘zbekiston)", "Vanuatu" => "Vanuatu (Vanuatu)", "Vatican City State (Holy See)" => "Vatican City State (Holy See) (Vaticano)", "Venezuela" => "Venezuela (Venezuela)",
  "Vietnam" => "Vietnam (Việt Nam)", "Virgin Islands (British)" => "Virgin Islands (British) (British Virgin Islands)", "Virgin Islands (US)" => "Virgin Islands (US) (United States Virgin Islands)", "Wallis And Futuna Islands" => "Wallis And Futuna Islands (Wallis et Futuna)", "Western Sahara" => "Western Sahara (الصحراء الغربية)", "Yemen" => "Yemen (اليَمَن)", "Zambia" => "Zambia (Zambia)", "Zimbabwe" => "Zimbabwe (Zimbabwe)"
);
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
  <title>Supplier Registration Form</title>
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
          <h4 style="display: inline-block;">Supplier Registration <span class="hidden-xs">Details</span></h4>
        </b>
        <button class="btn btn-info pull-right" style="border-color: #000000;color: black;text-transform: uppercase;margin: 7px 21px;font-size: 16px;font-weight: bold;box-shadow: 2px 2px 2px #c5c5a3;" type="submit" name="submit">REGISTER NOW &raquo;</button>
      </div><br><br><br><br>
      <div class="row">
        <div class="form-group col-md-3 col-sm-6">
          <input placeholder="Supplier Name " name="sp_name" class="form-control " type="text" id="sp_name" onchange="uniqueFun(this.value,'sup_reg','sp_name',$(this).attr('id') );" onBlur="uniqueFun($('#sp_abrv').val(),'sup_reg','abrv',$('#sp_abrv').attr('id') );" oninput="short_name();" required />
        </div>
        <div class="form-group col-md-3 col-sm-6">
          <input placeholder="Abrivaiton of Supplier  " name="sp_abrv" class="form-control valid2" type="text" id="sp_abrv" onBlur="uniqueFun(this.value,'sup_reg','sp_abrv',$(this).attr('id') );" minlength="4" maxlength="7" required />
        </div>
        <div class="form-group col-md-3 col-sm-6">
          <input name="sp_rnum" type="text" id="sp_rnum" class="form-control" placeholder="Supplier Registration Number" />
        </div>
        <!-- <div class="form-group col-md-3 col-sm-6">
          <input placeholder="Supplier Code" name="sp_code" type="text" id="sp_code" class="form-control" />
        </div> -->
        <div class="form-group col-md-3 col-sm-6">
          <input placeholder="Supplier Contact " name="sp_contact" class="form-control " type="text" id="sp_contact" />
        </div>
        <div class="form-group col-md-3 col-sm-6">
          <input name="sp_email" type="text" id="sp_email" class="form-control" placeholder="Supplier Email Address " />
        </div>
        <div class="form-group col-md-3 col-sm-6">
          <input name="sp_type" type="text" id="sp_type" class="form-control" placeholder="Supplier Type" />
        </div>
        <div class="form-group col-md-3 col-sm-6">
          <input name="sp_web" type="text" id="sp_web" class="form-control" placeholder="Supplier Website" />
        </div>
      </div>
      <div class="bg-info col-xs-12 form-group">
        <h4>VAT INFO</h4>
      </div>
      <div class="row">
        <div class="form-group col-md-3 col-sm-4">
          <label>
            <b>Registered for Taxes ?</b></label><br>
          <input onchange="if(this.checked) {$('#tax_details').removeClass('hidden');$('input#uk_citizen').val(1);$('input#uk_citizen').prop('checked',true); }else{$('#tax_details').addClass('hidden');$('input#uk_citizen').val(0);$('input#uk_citizen').prop('checked',false);}" type="checkbox" id="tax_reg" name="tax_reg" value="1" data-toggle="toggle" data-on="Yes" data-off="No">
        </div>
        <div id="tax_details" class=" hidden">
          <div class="form-group col-md-3 col-sm-4  " id="div_uk_citizen">
            <label>
              <b>UK Based ?</b></label><br>
            <input onchange="if(this.checked) {$('#div_uk_citizen_vatNum').removeClass('hidden');$('#div_country_vat').addClass('hidden');$('#div_country_vatNum').addClass('hidden');}else{$('#div_uk_citizen_vatNum').addClass('hidden');$('#div_country_vat').removeClass('hidden');$('#div_country_vatNum').removeClass('hidden');}" type="checkbox" id="uk_citizen" name="uk_citizen" value="0" data-toggle="toggle" data-on="Yes" data-off="No" checked>

          </div>
          <div class="form-group col-md-3 col-sm-4 " id="div_uk_citizen_vatNum">
            <label>Enter UK VAT number</label><br>
            <input name="uk_citizen_vatNum" type="text" id="uk_citizen_vatNum" class="form-control" placeholder="Enter UK VAT Number" />
          </div>
          <div class="form-group col-md-3 col-sm-4 hidden" id="div_country_vat">
            <label>Select the country in which you have registered for VAT</label><br>
            <?php
            $select_countries = "<select name='country_vat' id='country_vat' class='form-control multi_class mt'>
          <option value='' disabled selected>Select a country</option>";
            foreach ($country_array as $key => $val) {
              $select_countries .= "<option value='" . $key . "'>" . $val . "</option>";
            }
            $select_countries .= "<select>";
            echo $select_countries;
            ?>
            <!-- <input name="country_vat" type="text" id="country_vat" class="form-control" placeholder="Country in which you have registered for VAT" /> -->
          </div>
          <div class="form-group col-md-3 col-sm-4 hidden" id="div_country_vatNum">
            <label>Enter TAX number</label><br>
            <input name="country_vatNum" type="text" id="country_vatNum" class="form-control" placeholder="TAX Number" />
          </div>
        </div>
      </div>
      <div class="bg-info col-xs-12 form-group">
        <h4>Supplier Address</h4>
      </div>
      <div class="row">
        <div class="form-group col-md-3">
          <label>Select a country </label><br>
          <?php

          $select_countries = "<select onchange='get_cities(this)' name='sp_country' id='sp_country' class='form-control multi_class mt'>
              <option value='' disabled selected>Select a country</option>";
          foreach ($country_array as $key => $val) {
            $select_countries .= "<option value='" . $key . "'>" . $val . "</option>";
          }
          $select_countries .= "<select>";
          echo $select_countries; ?>
        </div>
        <div class="form-group col-md-3 append_cities hidden"></div>
        <div class="form-group col-md-3 div_other_city_field hidden">
          <label>Enter City Name </label>
          <input name="sp_city" type="text" class="form-control mt other_city_field hidden" placeholder="Enter City Name" />
        </div>
        <div class="form-group col-md-3 col-sm-6">
          <label>Post Code</label>
          <input name="sp_postCode" type="text" class="form-control" placeholder="Post Code" />
        </div>
        <div class="form-group col-md-3 col-sm-6">
          <label>Building Number / Name</label>
          <input name="sp_buildingName" type="text" class="form-control" placeholder="Building Number / Name" />
        </div>
        <div class="form-group col-md-3 col-sm-6">
          <label>Street / Road</label>
          <input name="sp_streetRoad" type="text" class="form-control" placeholder="Address Line 3" />
        </div>
        <div class="form-group col-md-3 col-sm-6">
          <label>Address Line 1</label>
          <input name="sp_line1" type="text" class="form-control" placeholder="Address Line 1" />
        </div>
        <div class="form-group col-md-3 col-sm-6">
          <label>Address Line 2</label>
          <input name="sp_line2" type="text" class="form-control" placeholder="Address Line 2" />
        </div>
      </div>
      <div class="bg-info col-xs-12 form-group">
        <h4>Supplier Bank Details</h4>
      </div>
      <div class="row">
        <div class="form-group col-md-3 col-sm-6">
          <input class="form-control " name="sp_bnkName" type="text" id="sp_bnkName" placeholder="Bank Name " />
        </div>
        <div class="form-group col-md-3 col-sm-6">
          <input class="form-control " name="sp_acName" type="text" id="sp_acName" placeholder="Account Holder Name " />
        </div>
        <div class="form-group col-md-3 col-sm-6">
          <input type="text" class="form-control" name="account_number" id="acNo" oninput="this.value = this.value.replace(/[^0-9-.]/g, '').replace(/(\..)\./g, '$1');" maxlength="8" minlength="8" placeholder="Enter Account Number (8 digits) ">
        </div>
        <div class="form-group col-md-3 col-sm-6">
          <input type="text" class="form-control" name="sort_code" id="acntCode" oninput="this.value = this.value.replace(/[^0-9-.]/g, '').replace(/(\..)\./g, '$1');" maxlength="8" minlength="8" placeholder="Enter Sort Code (6 digits) ">
        </div>
      </div>
      <div class="bg-info col-xs-12 form-group">
        <h4>Authorization (Contact Person) Details</h4>
      </div>
      <div class="row">
        <div class="form-group col-md-3 col-sm-6">
          <input placeholder="Contact Person Name " name="sp_cpName" class="form-control " type="text" id="sp_cpName" />
        </div>
        <div class="form-group col-md-3 col-sm-6">
          <input placeholder="Position in Business" name="sp_cppos" class="form-control " type="text" id="sp_cppos" />
        </div>
        <div class="form-group col-md-3 col-sm-6">
          <input placeholder="Contact " name="sp_cpNum" class="form-control " type="text" id="sp_cpNum" />
        </div>
        <div class="form-group col-md-3 col-sm-6">
          <input name="sp_cpEmail" type="text" id="sp_cpEmail" class="form-control" placeholder="Email Address " />
        </div>
      </div>
    </form>
  </div>
  <script src="js/jquery-1.11.3.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
  <script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.13/js/bootstrap-multiselect.js" type="text/javascript"></script>
  <script>
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
      var arrValues = $('#sp_name').val().split(' ');
      var short_name = '';
      // Loop over each value in the array.
      $.each(arrValues, function(intIndex, objValue) {
        if (short_name.length < 7) {
          short_name += objValue.substring(0, 1);
        }
      })
      $('#sp_abrv').val(short_name.toUpperCase());
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
              //   alert("Something went wrong. Try again!");
            }
          },
          error: function(xhr) {
            // alert("An error occured: " + xhr.status + " " + xhr.statusText);
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