<?php if(session_id() == '' || !isset($_SESSION)){session_start();}
include "userhaspage.php";
SysPermiss::UserHasPage(__FILE__);
include'db.php'; 
include'class.php';
$table='interpreter_reg';
$edit_id= @$_GET['edit_id'];
$query="SELECT * FROM $table where id=$edit_id";			
$result = mysqli_query($con,$query);
$row = mysqli_fetch_assoc($result);
  $g_rowTable=$row;
  $rowID=$row['id'];
  $password=$row['password'];
  $name=$row['name'];
  $email=$row['email'];
  $email2=$row['email2'];
  $contactNo=$row['contactNo'];
  $contactNo2=$row['contactNo2'];
  $interp_pix=$row['interp_pix'];
  $rph=$row['rph'];
  $interp=$row['interp'];
  $telep=$row['telep'];
  $trans=$row['trans'];
  $gender=$row['gender'];
  $country=$row['country'];
  $city=$row['city'];
  $address=$row['address'];
  $bnakName=$row['bnakName'];
  $acName=$row['acName'];
  $acntCode=$row['acntCode'];
  $acNo=$row['acNo'];
  $rpm=$row['rpm'];
  $rpu=$row['rpu'];
  $ni=$row['ni'];
  $reg_date=$row['reg_date'];
  $dob=$row['dob'];
  $buildingName=$row['buildingName'];
  $line1=$row['line1'];
  $line2=$row['line2'];
  $line3=$row['line3'];
  $postCode=$row['postCode'];
  $dbs_checked=$row['dbs_checked'];
  $ratetravelexpmile=$row['ratetravelexpmile'];
  $ratetravelworkmile=$row['ratetravelworkmile'];
  $uk_citizen=$row['uk_citizen'];?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
  <title>Interpreter Registration Form</title>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
  <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" />
    <?php include'ajax_uniq_fun.php'; ?>
    <style>
    .multiselect {min-width: 230px;}.multiselect-container {max-height: 400px;overflow-y: auto;max-width: 380px;}.multiselect-native-select{display:block;}.multiselect-container li.active label.radio,.multiselect-container li.active label.checkbox{color:white;}
    </style>
</head>
<body>
<div class="container-fluid">
        <form action="" method="post" class="register"  enctype="multipart/form-data" name="maxform">
          <div class="bg-info col-xs-12 form-group"><h4>Interpreter Personal Details</h4></div>
            <div class="form-group col-md-3 col-sm-6">
                    <label>Name *</label>
                    <input placeholder="Name *" class="form-control valid" name="name" type="text" id="name" required='' value="<?php echo $name; ?>" onBlur="uniqueFun(this.value,'interpreter_reg','name',$(this).attr('id'),'editFlag',<?php echo $rowID; ?> );" tabindex="1"/>
            </div>
            <div class="form-group col-md-3 col-sm-6">
                    <label>Rate per Hour *</label>
               <input placeholder="Rate per Hour *" class="form-control" name="rph" type="text" id="rph" required='' value="<?php echo $rph; ?>" 
                  pattern="[0-9]+([\.|,][0-9]+)?" step="0.01"tabindex="2"/>  
               <?php if(isset($_POST['submit'])){$c1=$_POST['rph']; $acttObj->editFun($table,$edit_id,'rph',$c1);} ?>
              <?php if(isset($_POST['submit'])){$c1=$_POST['name']; $acttObj->editFun($table,$edit_id,'name',$c1);}
              if(empty($password)){
                $new_password='@'.strtok($_POST['name'], " ").substr(str_shuffle('0123456789abcdwxyz') , 0 , 5 ).substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0 , 3 );
                $acttObj->editFun($table,$edit_id,'password',$new_password);
              } ?>
            </div>
            <div class="form-group col-md-3 col-sm-6">
                    <label>Email Address 1 *</label>
                    <input placeholder="Email Address 1 *" class="form-control" name="email" type="text" id="email" required='' onBlur="uniqueFun(this.value,'interpreter_reg','email',$(this).attr('id'),'editFlag',<?php echo $rowID; ?> );" tabindex="3" value="<?php echo $email; ?>"/>
            </div>
            <div class="form-group col-md-3 col-sm-6">
                    <label>Mobile No *</label>
                  <input placeholder="Mobile No *" class="form-control validate_number" name="contactNo" type="text" id="contactNo" required='' tabindex="4" value="<?php echo $contactNo; ?>"/>
               <?php if(isset($_POST['submit'])){$c1=$_POST['email']; $acttObj->editFun($table,$edit_id,'email',$c1);} ?>
              <?php if(isset($_POST['submit'])){$c1=$_POST['contactNo']; $acttObj->editFun($table,$edit_id,'contactNo',$c1);} ?>
            </div>
            <div class="form-group col-md-3 col-sm-6">
                    <label>Email Address 2</label>
                    <input placeholder="Email Address 2" class="form-control" name="email2" type="text" id="email2" onBlur="uniqueFun(this.value,'interpreter_reg','email2',$(this).attr('id'),'editFlag',<?php echo $rowID; ?> );" tabindex="5" value="<?php echo $email2; ?>"/>
            </div>
            <div class="form-group col-md-3 col-sm-6">
                    <label>Landline *</label>
                  <input placeholder="Landline *" class="form-control validate_number" name="contactNo2" type="text" id="contactNo2" tabindex="6" value="<?php echo $contactNo2; ?>"/>
               <?php if(isset($_POST['submit'])){$c1=$_POST['email2']; $acttObj->editFun($table,$edit_id,'email2',$c1);} ?>
              <?php if(isset($_POST['submit'])){$c1=$_POST['contactNo2']; $acttObj->editFun($table,$edit_id,'contactNo2',$c1);} ?>
             </div>
            <div class="form-group col-md-3 col-sm-6">
                <label>Rate per Min (Telephone)* </label>
                    <input placeholder="Rate per Min (Telephone)*" class="form-control" name="rpm" type="text" id="rpm" required='' pattern="[0-9]+([\.|,][0-9]+)?" step="0.01" tabindex="7" value="<?php echo $rpm; ?>"/>
            </div>
            <div class="form-group col-md-3 col-sm-6">
                <label>Rate per Unit (Translation)</label>
                  <input placeholder="Rate per Unit (Translation)" class="form-control" name="rpu" type="text" id="rpu" required='' pattern="[0-9]+([\.|,][0-9]+)?" step="0.01" tabindex="8" value="<?php echo $rpu; ?>"/>
            </div>
              <?php if(isset($_POST['submit'])){$c1=$_POST['rpm']; $acttObj->editFun($table,$edit_id,'rpm',$c1);} ?>
              <?php if(isset($_POST['submit'])){$c1=$_POST['rpu']; $acttObj->editFun($table,$edit_id,'rpu',$c1);} ?>
        <?php if(isset($_POST['submit'])){
          TestCode::ModifyHtmlDB("interp_reg_travel.html",$table,$edit_id);
        }else{
          TestCode::AddHtmlFieldsDB("interp_reg_travel.html",$g_rowTable);
        } ?>
          <div class="bg-info col-xs-12 form-group"><h4>Bank Account Details</h4></div>
            <div class="form-group col-md-3 col-sm-6">
              <label>Bank Name</label>
                    <input placeholder="Bank Name" class="form-control" name="bnakName" type="text" id="bnakName" tabindex="9" value="<?php echo $bnakName; ?>"/>
            </div>
            <div class="form-group col-md-3 col-sm-6">
              <label>Account Name</label>
               <input placeholder="Account Name" class="form-control" name="acName" type="text" id="acName" tabindex="10" value="<?php echo $acName; ?>"/>
               <?php if(isset($_POST['submit'])){$c1=$_POST['bnakName']; $acttObj->editFun($table,$edit_id,'bnakName',$c1);} ?>        
        		<?php if(isset($_POST['submit'])){$c1=$_POST['acName']; $acttObj->editFun($table,$edit_id,'acName',$c1);} ?>
            </div>
            <div class="form-group col-md-3 col-sm-6">
              <label>Account Sort Code</label>
                    <input placeholder="Account Sort Code" class="form-control" name="acntCode" type="text" id="acntCode"  oninput="this.value = this.value.replace(/[^0-9-.]/g, '').replace(/(\..*)\./g, '$1');" maxlength="8" minlength="8" tabindex="11" value="<?php echo $acntCode; ?>"/>
            </div>
            <div class="form-group col-md-3 col-sm-6">
              <label>Account Number</label>
                  <input placeholder="Account Number" class="form-control" name="acNo" type="text" id="acNo" tabindex="13" oninput="this.value = this.value.replace(/[^0-9-.]/g, '').replace(/(\..*)\./g, '$1');" maxlength="8" minlength="8" value="<?php echo $acNo; ?>"/>
                  <?php if(isset($_POST['submit'])){$c1=str_replace("-","",$_POST['acntCode']); $acttObj->editFun($table,$edit_id,'acntCode',$c1);} ?>
               <?php if(isset($_POST['submit'])){$c1=$_POST['acNo']; $acttObj->editFun($table,$edit_id,'acNo',$c1);} ?>
           </div>
            <div class="form-group col-sm-12">
              <label> National Insurance # </label>
        <script type="text/javascript">
function toUnicode(elmnt,content){
    if(content.length==elmnt.maxLength){next=elmnt.tabIndex+2
	if (next<document.maxform.elements.length){document.maxform.elements[next].focus()}}}
</script>
<?php $ni_ar=($ni);?>
<span class="inlineinput">
        <input name="ni1" type='text' style='display: inline;width: 32px;padding: 8px;' maxlength="1" onkeyup="toUnicode(this,this.value)" tabindex="13" value="<?php echo $ni_ar[0]; ?>"/>
    </span>
    <span class="inlineinput">
        <input name="ni2" type='text' style='display: inline;width: 32px;padding: 8px;' maxlength="1" onkeyup="toUnicode(this,this.value)" tabindex="14" value="<?php echo $ni_ar[1]; ?>"/>
    </span><span class="inlineinput">
      <input name="ni3" type='text' style='display: inline;width: 32px;padding: 8px;' maxlength="1" onkeyup="toUnicode(this,this.value)" tabindex="15" value="<?php echo $ni_ar[2]; ?>"/>
    </span><span class="inlineinput">
      <input name="ni4" type='text' style='display: inline;width: 32px;padding: 8px;' maxlength="1" onkeyup="toUnicode(this,this.value)" tabindex="16" value="<?php echo $ni_ar[3]; ?>"/>
    </span><span class="inlineinput">
      <input name="ni5" type='text' style='display: inline;width: 32px;padding: 8px;' maxlength="1" onkeyup="toUnicode(this,this.value)" tabindex="17" value="<?php echo $ni_ar[4]; ?>"/>
    </span><span class="inlineinput">
      <input name="ni6" type='text' style='display: inline;width: 32px;padding: 8px;' maxlength="1" onkeyup="toUnicode(this,this.value)" tabindex="18" value="<?php echo $ni_ar[5]; ?>"/>
    </span><span class="inlineinput">
      <input name="ni7" type='text' style='display: inline;width: 32px;padding: 8px;' maxlength="1" onkeyup="toUnicode(this,this.value)" tabindex="19" value="<?php echo $ni_ar[6]; ?>"/>
    </span><span class="inlineinput">
      <input name="ni8" type='text' style='display: inline;width: 32px;padding: 8px;' maxlength="1" onkeyup="toUnicode(this,this.value)" tabindex="20" value="<?php echo $ni_ar[7]; ?>"/>
    </span><span class="inlineinput">
      <input name="ni9" type='text' style='display: inline;width: 32px;padding: 8px;' maxlength="1" onkeyup="toUnicode(this,this.value)" tabindex="21" value="<?php echo $ni_ar[8]; ?>"/>
    </span><span class="inlineinput">
      <input name="ni10" type='text' style='display: inline;width: 32px;padding: 8px;' maxlength="1" onkeyup="toUnicode(this,this.value)" tabindex="22" value="<?php echo $ni_ar[9]; ?>"/>
    </span>
    <?php $ni=@$_POST['ni1'].@$_POST['ni2'].@$_POST['ni3'].@$_POST['ni4'].@$_POST['ni5'].@$_POST['ni6'].@$_POST['ni7'].@$_POST['ni8'].@$_POST['ni9'].@$_POST['ni10'];
			    if(isset($_POST['submit'])){$acttObj->editFun($table,$edit_id,'ni',$ni);} ?>
            </div>
           <div class="bg-info col-xs-12 form-group"><h4>Other Information</h4></div>
            <div class="form-group col-md-3 col-sm-6">
             <label>Mode of Job *</label>
            <table width="20%" class="table table-bordered">
  <tr>
    <td width="100">Face To Face Interpreting</td>
    <td width="1"><input type="checkbox" name="interp" id="interp" value="Yes" <?php if($interp=='Yes'){ ?>checked="checked" <?php } ?>/>
      <?php if(isset($_POST['submit'])){$c1=@$_POST['interp']; if(empty($c1)){$c1='No';}$acttObj->editFun($table,$edit_id,'interp',$c1);} ?></td>
  </tr>
  <tr>
    <td width="100">Telephone Interpreting</td>
    <td width="1"><input type="checkbox" name="telep" value="Yes" <?php if($telep=='Yes'){ ?>checked="checked" <?php  } ?>/>
      <?php if(isset($_POST['submit'])){$c1=@$_POST['telep']; if(empty($c1)){$c1='No';}$acttObj->editFun($table,$edit_id,'telep',$c1);} ?></td>
  </tr>
  <tr>
    <td width="100">Translation</td>
    <td width="1"><input type="checkbox" name="trans" value="Yes" <?php if($trans=='Yes'){ ?>checked="checked" <?php } ?>/>
      <?php if(isset($_POST['submit'])){$c1=@$_POST['trans']; if(empty($c1)){$c1='No';}$acttObj->editFun($table,$edit_id,'trans',$c1);} ?></td>
  </tr>
  </table>
    </div>
    <div class="form-group col-md-3 col-sm-6">
      <label class="optional">Interpreter Photo </label>
      <input class="form-control long" name="file" type="file" id="file" />
      <?php if(isset($_POST['submit'])){error_reporting(0); if(!empty($_FILES["file"]["name"])){unlink('file_folder/interp_photo/'.$interp_pix);
	    $picName=$acttObj->upload_file("interp_photo",$_FILES["file"]["name"],$_FILES["file"]["type"],$_FILES["file"]["tmp_name"],$edit_id);$acttObj->editFun($table,$edit_id,'interp_pix',$picName);}} ?>
</div>
            <div class="form-group col-md-3 col-sm-6">
             <label>Date of birth</label>
                    <input class="form-control" name="dob" type="date" id="dob" tabindex="9" value="<?php echo $dob; ?>"/>  
        		<?php if(isset($_POST['submit'])){$c1=$_POST['dob']; $acttObj->editFun($table,$edit_id,'dob',$c1);} ?>
                   </div>
            <div class="form-group col-md-3 col-sm-6">
             <label>Registration Date</label>
               <input class="form-control" name="reg_date" type="date" id="reg_date" tabindex="10" value="<?php echo $reg_date; ?>"/>
               <?php if(isset($_POST['submit'])){$c1=$_POST['reg_date']; $acttObj->editFun($table,$edit_id,'reg_date',$c1);} ?>  
            </div>
            <div class="form-group col-md-3 col-sm-6">
<label class="optional">DBS checked ?</label><br>
<table width="20%" class="table table-bordered">
  <tr>
    <td width="1"><input name="dbs_checked" type="radio" value="0" <?php if($dbs_checked=='0'){?> checked="checked"<?php } ?>/>
                  <label class="gender">Yes</label></td>
    <td width="1"><input type="radio" name="dbs_checked" value="1" <?php if($dbs_checked=='1'){?> checked="checked"<?php } ?>/>
                  <label class="gender">No </label></td>
  </tr>
  </table>
<?php if(isset($_POST['submit'])){
$c22=$_POST['dbs_checked'];
$acttObj->editFun($table,$edit_id,'dbs_checked',$c22);} ?>
 </div>
            <div class="form-group col-md-3 col-sm-6">
                <label class="optional">Gender</label><br>
                  <table width="20%" class="table table-bordered">
                    <tr>
                        <td width="1"><input name="gender" type="radio" value="Male" <?php if($gender=='Male'){?> checked="checked"<?php } ?>/>
                                    <label class="gender">Male</label></td>
                        <td width="1"><input type="radio" name="gender" value="Female" <?php if($gender=='Female'){?> checked="checked"<?php } ?>/>
                                    <label class="gender">Female</label></td>
                    </tr>
                </table>
                <?php if(isset($_POST['submit'])){$c22=$_POST['gender'];$acttObj->editFun($table,$edit_id,'gender',$c22);} ?>
            </div>
            <div class="form-group col-md-3 col-sm-6">
                <label class="optional">UK Citizen?</label><br>
                  <table width="20%" class="table table-bordered">
                    <tr>
                        <td width="1"><input name="uk_citizen" type="radio" value="1" <?php if($uk_citizen==1){?> checked="checked"<?php } ?>/>
                        <label class="uk_citizen">Yes</label></td>
                        <td width="1"><input type="radio" name="uk_citizen" value="0" <?php if($uk_citizen==0){?> checked="checked"<?php } ?>/>
                        <label class="uk_citizen">No</label></td>
                    </tr>
                </table>
                <?php if(isset($_POST['submit'])){
                        $uk_citizen=$_POST['uk_citizen'];
                        $acttObj->editFun($table,$edit_id,'uk_citizen',$uk_citizen);
                        $country=$_POST['selected_country'];
                        $acttObj->editFun($table,$edit_id,'country',$country);
                        $city=$_POST['city'];
                        $acttObj->editFun($table,$edit_id,'city',$city);
                    } ?>
            </div>
            <div class="bg-info col-xs-12 form-group"><h4>Address Details</h4></div>
            <div class="form-group col-md-3">
              <label>Select a country *</label><br>
              <?php
              $country_array = array("Afghanistan"=>"Afghanistan (افغانستان)", "Aland Islands"=>"Aland Islands (Åland)", "Albania"=>"Albania (Shqipëria)", "Algeria"=>"Algeria (الجزائر)", "American Samoa"=>"American Samoa (American Samoa)", "Andorra"=>"Andorra (Andorra)", "Angola"=>"Angola (Angola)", "Anguilla"=>"Anguilla (Anguilla)", "Antarctica"=>"Antarctica (Antarctica)", "Antigua And Barbuda"=>"Antigua And Barbuda (Antigua and Barbuda)", "Argentina"=>"Argentina (Argentina)", "Armenia"=>"Armenia (Հայաստան)", "Aruba"=>"Aruba (Aruba)", "Australia"=>"Australia (Australia)", "Austria"=>"Austria (Österreich)", "Azerbaijan"=>"Azerbaijan (Azərbaycan)", "Bahamas The"=>"Bahamas The (Bahamas)", "Bahrain"=>"Bahrain (‏البحرين)", "Bangladesh"=>"Bangladesh (Bangladesh)", "Barbados"=>"Barbados (Barbados)", "Belarus"=>"Belarus (Белару́сь)", "Belgium"=>"Belgium (België)", "Belize"=>"Belize (Belize)", "Benin"=>"Benin (Bénin)", "Bermuda"=>"Bermuda (Bermuda)", "Bhutan"=>"Bhutan (ʼbrug-yul)", "Bolivia"=>"Bolivia (Bolivia)", "Bonaire, Sint Eustatius and Saba"=>"Bonaire, Sint Eustatius and Saba (Caribisch Nederland)", "Bosnia and Herzegovina"=>"Bosnia and Herzegovina (Bosna i Hercegovina)", "Botswana"=>"Botswana (Botswana)", "Bouvet Island"=>"Bouvet Island (Bouvetøya)", "Brazil"=>"Brazil (Brasil)", "British Indian Ocean Territory"=>"British Indian Ocean Territory (British Indian Ocean Territory)", "Brunei"=>"Brunei (Negara Brunei Darussalam)", "Bulgaria"=>"Bulgaria (България)", "Burkina Faso"=>"Burkina Faso (Burkina Faso)", "Burundi"=>"Burundi (Burundi)", "Cambodia"=>"Cambodia (Kâmpŭchéa)", "Cameroon"=>"Cameroon (Cameroon)", "Canada"=>"Canada (Canada)", "Cape Verde"=>"Cape Verde (Cabo Verde)", "Cayman Islands"=>"Cayman Islands (Cayman Islands)", "Central African Republic"=>"Central African Republic (Ködörösêse tî Bêafrîka)", "Chad"=>"Chad (Tchad)", "Chile"=>"Chile (Chile)", "China"=>"China (中国)", "Christmas Island"=>"Christmas Island (Christmas Island)", "Cocos (Keeling) Islands"=>"Cocos (Keeling) Islands (Cocos (Keeling) Islands)", "Colombia"=>"Colombia (Colombia)", "Comoros"=>"Comoros (Komori)", "Congo"=>"Congo (République du Congo)", "Congo The Democratic Republic Of The"=>"Congo The Democratic Republic Of The (République démocratique du Congo)", "Cook Islands"=>"Cook Islands (Cook Islands)", "Costa Rica"=>"Costa Rica (Costa Rica)", "Cote D'Ivoire (Ivory Coast)"=>"Cote D'Ivoire (Ivory Coast) ()", "Croatia (Hrvatska)"=>"Croatia (Hrvatska) (Hrvatska)", "Cuba"=>"Cuba (Cuba)", "Curaçao"=>"Curaçao (Curaçao)", "Cyprus"=>"Cyprus (Κύπρος)", "Czech Republic"=>"Czech Republic (Česká republika)", "Denmark"=>"Denmark (Danmark)", "Djibouti"=>"Djibouti (Djibouti)", "Dominica"=>"Dominica (Dominica)", "Dominican Republic"=>"Dominican Republic (República Dominicana)", "East Timor"=>"East Timor (Timor-Leste)", "Ecuador"=>"Ecuador (Ecuador)", "Egypt"=>"Egypt (مصر‎)", "El Salvador"=>"El Salvador (El Salvador)", "Equatorial Guinea"=>"Equatorial Guinea (Guinea Ecuatorial)", "Eritrea"=>"Eritrea (ኤርትራ)", "Estonia"=>"Estonia (Eesti)", "Ethiopia"=>"Ethiopia (ኢትዮጵያ)", "Falkland Islands"=>"Falkland Islands (Falkland Islands)", "Faroe Islands"=>"Faroe Islands (Føroyar)", "Fiji Islands"=>"Fiji Islands (Fiji)", "Finland"=>"Finland (Suomi)", "France"=>"France (France)", "French Guiana"=>"French Guiana (Guyane française)", "French Polynesia"=>"French Polynesia (Polynésie française)", "French Southern Territories"=>"French Southern Territories (Territoire des Terres australes et antarctiques fr)", "Gabon"=>"Gabon (Gabon)", "Gambia The"=>"Gambia The (Gambia)", "Georgia"=>"Georgia (საქართველო)", "Germany"=>"Germany (Deutschland)", "Ghana"=>"Ghana (Ghana)", "Gibraltar"=>"Gibraltar (Gibraltar)", "Greece"=>"Greece (Ελλάδα)", "Greenland"=>"Greenland (Kalaallit Nunaat)", "Grenada"=>"Grenada (Grenada)", "Guadeloupe"=>"Guadeloupe (Guadeloupe)", "Guam"=>"Guam (Guam)", "Guatemala"=>"Guatemala (Guatemala)", "Guernsey and Alderney"=>"Guernsey and Alderney (Guernsey)", "Guinea"=>"Guinea (Guinée)", "Guinea-Bissau"=>"Guinea-Bissau (Guiné-Bissau)", "Guyana"=>"Guyana (Guyana)", "Haiti"=>"Haiti (Haïti)", "Heard Island and McDonald Islands"=>"Heard Island and McDonald Islands (Heard Island and McDonald Islands)", "Honduras"=>"Honduras (Honduras)", "Hong Kong S.A.R."=>"Hong Kong S.A.R. (香港)", "Hungary"=>"Hungary (Magyarország)", "Iceland"=>"Iceland (Ísland)", "India"=>"India (भारत)", "Indonesia"=>"Indonesia (Indonesia)", "Iran"=>"Iran (ایران)", "Iraq"=>"Iraq (العراق)", "Ireland"=>"Ireland (Éire)", "Israel"=>"Israel (יִשְׂרָאֵל)", "Italy"=>"Italy (Italia)", "Jamaica"=>"Jamaica (Jamaica)", "Japan"=>"Japan (日本)", "Jersey"=>"Jersey (Jersey)", "Jordan"=>"Jordan (الأردن)", "Kazakhstan"=>"Kazakhstan (Қазақстан)", "Kenya"=>"Kenya (Kenya)", "Kiribati"=>"Kiribati (Kiribati)", "Korea North"=>"Korea North (북한)", "Korea South"=>"Korea South (대한민국)", "Kosovo"=>"Kosovo (Republika e Kosovës)", "Kuwait"=>"Kuwait (الكويت)", "Kyrgyzstan"=>"Kyrgyzstan (Кыргызстан)", "Laos"=>"Laos (ສປປລາວ)", "Latvia"=>"Latvia (Latvija)", "Lebanon"=>"Lebanon (لبنان)", "Lesotho"=>"Lesotho (Lesotho)", "Liberia"=>"Liberia (Liberia)", "Libya"=>"Libya (‏ليبيا)", "Liechtenstein"=>"Liechtenstein (Liechtenstein)", "Lithuania"=>"Lithuania (Lietuva)", "Luxembourg"=>"Luxembourg (Luxembourg)", "Macau S.A.R."=>"Macau S.A.R. (澳門)", "Macedonia"=>"Macedonia (Северна Македонија)", "Madagascar"=>"Madagascar (Madagasikara)", "Malawi"=>"Malawi (Malawi)", "Malaysia"=>"Malaysia (Malaysia)", "Maldives"=>"Maldives (Maldives)", "Mali"=>"Mali (Mali)", "Malta"=>"Malta (Malta)", "Man (Isle of)"=>"Man (Isle of) (Isle of Man)", "Marshall Islands"=>"Marshall Islands (M̧ajeļ)", "Martinique"=>"Martinique (Martinique)", "Mauritania"=>"Mauritania (موريتانيا)", "Mauritius"=>"Mauritius (Maurice)", "Mayotte"=>"Mayotte (Mayotte)", "Mexico"=>"Mexico (México)", "Micronesia"=>"Micronesia (Micronesia)", "Moldova"=>"Moldova (Moldova)", "Monaco"=>"Monaco (Monaco)", "Mongolia"=>"Mongolia (Монгол улс)", "Montenegro"=>"Montenegro (Црна Гора)", "Montserrat"=>"Montserrat (Montserrat)", "Morocco"=>"Morocco (المغرب)", "Mozambique"=>"Mozambique (Moçambique)", "Myanmar"=>"Myanmar (မြန်မာ)", "Namibia"=>"Namibia (Namibia)", "Nauru"=>"Nauru (Nauru)", "Nepal"=>"Nepal (नपल)", "Netherlands The"=>"Netherlands The (Nederland)", "New Caledonia"=>"New Caledonia (Nouvelle-Calédonie)", "New Zealand"=>"New Zealand (New Zealand)", "Nicaragua"=>"Nicaragua (Nicaragua)", "Niger"=>"Niger (Niger)", "Nigeria"=>"Nigeria (Nigeria)", "Niue"=>"Niue (Niuē)", "Norfolk Island"=>"Norfolk Island (Norfolk Island)", "Northern Mariana Islands"=>"Northern Mariana Islands (Northern Mariana Islands)", "Norway"=>"Norway (Norge)", "Oman"=>"Oman (عمان)", "Pakistan"=>"Pakistan (پاکستان)", "Palau"=>"Palau (Palau)", "Palestinian Territory Occupied"=>"Palestinian Territory Occupied (فلسطين)", "Panama"=>"Panama (Panamá)", "Papua new Guinea"=>"Papua new Guinea (Papua Niugini)", "Paraguay"=>"Paraguay (Paraguay)", "Peru"=>"Peru (Perú)", "Philippines"=>"Philippines (Pilipinas)", "Pitcairn Island"=>"Pitcairn Island (Pitcairn Islands)", "Poland"=>"Poland (Polska)", "Portugal"=>"Portugal (Portugal)", "Puerto Rico"=>"Puerto Rico (Puerto Rico)", "Qatar"=>"Qatar (قطر)", "Reunion"=>"Reunion (La Réunion)", "Romania"=>"Romania (România)", "Russia"=>"Russia (Россия)", "Rwanda"=>"Rwanda (Rwanda)", "Saint Helena"=>"Saint Helena (Saint Helena)", "Saint Kitts And Nevis"=>"Saint Kitts And Nevis (Saint Kitts and Nevis)", "Saint Lucia"=>"Saint Lucia (Saint Lucia)", "Saint Pierre and Miquelon"=>"Saint Pierre and Miquelon (Saint-Pierre-et-Miquelon)", "Saint Vincent And The Grenadines"=>"Saint Vincent And The Grenadines (Saint Vincent and the Grenadines)", "Saint-Barthelemy"=>"Saint-Barthelemy (Saint-Barthélemy)", "Saint-Martin (French part)"=>"Saint-Martin (French part) (Saint-Martin)", "Samoa"=>"Samoa (Samoa)", "San Marino"=>"San Marino (San Marino)", "Sao Tome and Principe"=>"Sao Tome and Principe (São Tomé e Príncipe)", "Saudi Arabia"=>"Saudi Arabia (العربية السعودية)", "Senegal"=>"Senegal (Sénégal)", "Serbia"=>"Serbia (Србија)", "Seychelles"=>"Seychelles (Seychelles)", "Sierra Leone"=>"Sierra Leone (Sierra Leone)", "Singapore"=>"Singapore (Singapore)", "Sint Maarten (Dutch part)"=>"Sint Maarten (Dutch part) (Sint Maarten)", "Slovakia"=>"Slovakia (Slovensko)", "Slovenia"=>"Slovenia (Slovenija)", "Solomon Islands"=>"Solomon Islands (Solomon Islands)", "Somalia"=>"Somalia (Soomaaliya)", "South Africa"=>"South Africa (South Africa)", "South Georgia"=>"South Georgia (South Georgia)", "South Sudan"=>"South Sudan (South Sudan)", "Spain"=>"Spain (España)", "Sri Lanka"=>"Sri Lanka (śrī laṃkāva)", "Sudan"=>"Sudan (السودان)", "Suriname"=>"Suriname (Suriname)", "Svalbard And Jan Mayen Islands"=>"Svalbard And Jan Mayen Islands (Svalbard og Jan Mayen)", "Swaziland"=>"Swaziland (Swaziland)", "Sweden"=>"Sweden (Sverige)", "Switzerland"=>"Switzerland (Schweiz)", "Syria"=>"Syria (سوريا)", "Taiwan"=>"Taiwan (臺灣)", "Tajikistan"=>"Tajikistan (Тоҷикистон)", "Tanzania"=>"Tanzania (Tanzania)", "Thailand"=>"Thailand (ประเทศไทย)", "Togo"=>"Togo (Togo)", "Tokelau"=>"Tokelau (Tokelau)", "Tonga"=>"Tonga (Tonga)", "Trinidad And Tobago"=>"Trinidad And Tobago (Trinidad and Tobago)", "Tunisia"=>"Tunisia (تونس)", "Turkey"=>"Turkey (Türkiye)", "Turkmenistan"=>"Turkmenistan (Türkmenistan)", "Turks And Caicos Islands"=>"Turks And Caicos Islands (Turks and Caicos Islands)", "Tuvalu"=>"Tuvalu (Tuvalu)", "Uganda"=>"Uganda (Uganda)", "Ukraine"=>"Ukraine (Україна)", "United Arab Emirates"=>"United Arab Emirates (دولة الإمارات العربية المتحدة)", "United Kingdom"=>"United Kingdom (United Kingdom)", "United States"=>"United States (United States)", "United States Minor Outlying Islands"=>"United States Minor Outlying Islands (United States Minor Outlying Islands)", "Uruguay"=>"Uruguay (Uruguay)", "Uzbekistan"=>"Uzbekistan (O‘zbekiston)", "Vanuatu"=>"Vanuatu (Vanuatu)", "Vatican City State (Holy See)"=>"Vatican City State (Holy See) (Vaticano)", "Venezuela"=>"Venezuela (Venezuela)",
              "Vietnam"=>"Vietnam (Việt Nam)", "Virgin Islands (British)"=>"Virgin Islands (British) (British Virgin Islands)", "Virgin Islands (US)"=>"Virgin Islands (US) (United States Virgin Islands)", "Wallis And Futuna Islands"=>"Wallis And Futuna Islands (Wallis et Futuna)", "Western Sahara"=>"Western Sahara (الصحراء الغربية)", "Yemen"=>"Yemen (اليَمَن)", "Zambia"=>"Zambia (Zambia)", "Zimbabwe"=>"Zimbabwe (Zimbabwe)");
              $select_countries="<select onchange='get_cities(this)' name='selected_country' id='selected_country' class='form-control multi_class mt'>
              <option value='".$country."' selected>".$country."</option>
              <option value='' disabled>--- Select a country ---</option>";
              foreach($country_array as $key=>$val){
                $select_countries.="<option value='".$key."'>".$val."</option>";
              }
              $select_countries.="<select>";
              echo $select_countries;?>
            </div>
            <div class="form-group col-md-3 append_cities">
            <?php if(isset($country)){
              $ch = curl_init();
              $postData = [
                  "country"=>$country
              ];
              curl_setopt($ch, CURLOPT_URL,"https://countriesnow.space/api/v0.1/countries/cities");
              curl_setopt($ch, CURLOPT_POST, 1);
              curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
              curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
              curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData));
              $cities_array=json_decode(curl_exec ($ch));
              $cities_array=$cities_array->data;
              $select_cities="<label>Select a city *</label>
              <select onchange='other_city(this)' name='selected_city' id='selected_city' class='form-control mt' required>
              <option value='".$city."' selected>".$city."</option>
              <option value='' disabled>--- Select a city ---</option>";
              if(count($cities_array)>0){
                foreach($cities_array as $key=>$val){
                  $select_cities.="<option value='".$val."'>".$val."</option>";
                }
                $select_cities.="<option value='Not in List'>Not in List</option>";
              }else{
                $select_cities.="<option value='Not in List'>No City Found</option>";
              }
              $select_cities.="<select>";
              echo $select_cities;
            } ?>
            </div>
            <div class="form-group col-md-3 div_other_city_field hidden">
              <label>Enter City Name *</label>
              <input name="city" type="text" class="form-control mt other_city_field hidden" value="<?php echo $city; ?>" placeholder="Enter City Name"/>
            </div>
            <div class="form-group col-md-3 col-sm-6">
                <label class="optional">Post Code</label>
                <input placeholder="Post Code" class="form-control" name="postCode" type="text" value="<?php echo $postCode ; ?>"/>
              <?php if(isset($_POST['submit'])){$c17=$_POST['postCode'];$acttObj->editFun($table,$edit_id,'postCode',$c17);} ?>
            </div>
            <div class="form-group col-md-3 col-sm-6">
                <label class="row1">Building Number / Name * </label>
                <input placeholder="Building Number / Name *" class="form-control" name="buildingName" type="text"  required value="<?php echo $buildingName; ?>"/>
              <?php if(isset($_POST['submit'])){$c14=$_POST['buildingName'];$acttObj->editFun($table,$edit_id,'buildingName',$c14);} ?>
            </div>
            <div class="form-group col-md-3 col-sm-6">
              <label class="optional">Address Line 1 </label>
              <input placeholder="Address Line 1" class="form-control" name="line1" type="text" value="<?php echo $line1; ?>"/>
            <?php if(isset($_POST['submit'])){$c14=$_POST['line1'];$acttObj->editFun($table,$edit_id,'line1',$c14);} ?>
            </div>
            <div class="form-group col-md-3 col-sm-6">
              <label class="optional">Address Line 2</label>
              <input placeholder="Address Line 2" class="form-control" name="line2" type="text" value="<?php echo $line2; ?>"/>
                      <?php if(isset($_POST['submit'])){$c14=$_POST['line2'];$acttObj->editFun($table,$edit_id,'line2',$c14);} ?>
          </div>
          <div class="form-group col-md-3 col-sm-6">
            <label class="optional">Address Line 3 </label>
            <input placeholder="Address Line 3" class="form-control" name="line3" type="text" id="line3" value="<?php echo $line3; ?>"/>
            <?php if(isset($_POST['submit'])){$c15=$_POST['line3'];$acttObj->editFun($table,$edit_id,'line3',$c15);} ?>
          </div>
          <div class="form-group col-md-6 col-sm-6">
                <br><button class="btn btn-info pull-right" style="border-color: #000000;color: black;font-size: 20px;font-weight: bold;box-shadow: 2px 2px 2px #c5c5a3;" type="submit" name="submit">UPDATE NOW &raquo;</button>
          </div>
        </form>
<?php if(isset($_POST['submit'])){
  echo "<script>alert('Record of interpreter updated successfully.');</script>";
  $acttObj->editFun($table,$edit_id,'edited_by',$_SESSION['UserName']);
  $acttObj->editFun($table,$edit_id,'edited_date',date("Y-m-d H:i:s"));
  $acttObj->new_old_table('hist_'.$table,$table,$edit_id);
  $acttObj->insert("daily_logs",array("action_id"=>10,"user_id"=>$_SESSION['userId'],"details"=>"Interpreter ID: ".$edit_id));?>
<script>  window.onunload = refreshParent;
function refreshParent() { window.opener.location.reload();}
</script>
<?php } ?>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.13/css/bootstrap-multiselect.css"rel="stylesheet" type="text/css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.13/js/bootstrap-multiselect.js"type="text/javascript"></script>
<script>
    $(document).ready(function(){
    $('#acntCode').keyup(function(){
      var lengthT = $(this).val().length;
      // console.log(lengthT);
      // if(((lengthT+1)>1 && (lengthT+1)<8 && (lengthT+1))%3==0){
      //   console.log("here");
      //   $(this).val($(this).val()+"-");
      // }
      // if($(this).val().length>6){
      //   $(this).val($(this).val().substring(0,7));
      // }
      var foo = $(this).val().split("-").join(""); // remove hyphens
      if (foo.length > 0) {
        foo = foo.match(new RegExp('.{1,2}', 'g')).join("-");
        $(this).val(foo);
      }
      if (foo.length > 8) {
        $(this).val($(this).val().substring(0,7));
      }

    });
    $('#acNo').keydown(function(){
      var lengthTac = $(this).val().length;
      if (foo.length > 8) {
        $(this).val($(this).val().substring(0,7));
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
  $('.multi_class').multiselect({buttonWidth: '100px',includeSelectAllOption: true,numberDisplayed: 1,enableFiltering: true,enableCaseInsensitiveFiltering: true});
});
$(".valid").bind('keypress paste',function (e) {
  var regex = new RegExp(/[a-z A-Z 0-9 ()]/);
  var str = String.fromCharCode(!e.charCode ? e.which : e.charCode);
  if (!regex.test(str)) {
    e.preventDefault();
    return false;
  }
});
    function other_city(elem){
			var selected_city=$(elem).val();
      if (selected_city!='Not in List'){
        $('.other_city_field').val(selected_city);
      }
			if (selected_city=='Not in List'){
          $('.other_city_field').val('');
          $(elem).removeAttr("required");
          $('.div_other_city_field,.other_city_field').removeClass('hidden');
          $('.other_city_field').attr('required',"required");
          $('.other_city_field').focus();
      } else {
          $(elem).attr('required',"required");
          $('.div_other_city_field,.other_city_field').addClass('hidden');
          $('.other_city_field').removeAttr("required");
          $('#selected_city').focus();
      }
		}
    function get_cities(elem){
      $('.div_other_city_field,.other_city_field').addClass('hidden');
      $('.other_city_field').val("");
      var country_name=$(elem).val();
      if(country_name){
        $.ajax({
            url:'ajax_add_interp_data.php',
            method:'post',
            dataType:'json',
            data:{country_name:country_name,type:'get_cities_of_country'},
            success:function(data){
                if(data['cities']){
                    $('.append_cities').removeClass('hidden');
                    $('.append_cities').html(data['cities']);
                    //$("#selected_city").multiselect('rebuild');
                }else{
                  $('.append_cities').addClass('hidden');
                  alert("Something went wrong. Try again!");
                }
        }, error: function(xhr){
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
</script>
</body>
</html>