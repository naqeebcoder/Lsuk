<?php 
//php mailer library
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
require 'lsuk_system/phpmailer/vendor/autoload.php';
$mail = new PHPMailer(true);
include 'source/db.php';
include'source/class.php';
if(isset($_POST['submit']) && isset($_POST['disclaimer'])){
  if(isset($_POST['g-recaptcha-response']) && !empty($_POST['g-recaptcha-response'])){
    $secret='6LextRoUAAAAAPvBF31eiYCmVP7Ne8a6mSez83zl';
    $ip=$_SERVER['REMOTE_ADDR'];
    $captcha=$_POST['g-recaptcha-response'];
    $rsp=file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=$secret&response=$captcha&remoteip=$ip");
    $arr=json_decode($rsp,TRUE);
    if($arr['success']){
      $table="interpreter_reg";
      $int_id = $acttObj->get_id($table);
      //Personal data fields
      $first_name=$_POST['first_name'];$last_name=$_POST['last_name'];$dob=$_POST['dob'];$country=$_POST['selected_country'];
      $gender=$_POST['gender'];$email=$_POST['email'];$contact_no=$_POST['contact_no'];$mobile_no=$_POST['mobile_no'];
      $utr=$_POST['utr'];$building_name=$_POST['building_name'];$line1=$_POST['line1'];$line2=$_POST['line2'];$line3=$_POST['line3'];
      $city=$_POST['city'];$post_code=$_POST['post_code'];$interp=$_POST['interp'];$telep=$_POST['telep'];$trans=$_POST['trans'];
      $is_dbs=$_POST['is_dbs'];$dated=date('Y-m-d');$array_types=array();
      if(isset($interp) && !empty($interp)){array_push($array_types,$interp);}
      if(isset($telep) && !empty($telep)){array_push($array_types,$telep);}
      if(isset($trans) && !empty($trans)){array_push($array_types,$trans);}
      if(!empty($array_types)){
        $array_types=" <h3 style='display:inline-block;border:1px solid grey;padding: 4px;border-radius: 4px;'>".implode("</h3> <h3 style='display:inline-block;border:1px solid grey;padding: 4px;border-radius: 4px;'>",$array_types)."</h3> ";
        $array_types_label=$array_types;
      }else{
        $array_types="Not selected!";
        $array_types_label="Interpreter";
      }
      //Attendance fields
      $actnow=$_POST['actnow'];$actnow_time=$_POST['actnow_time'];$actnow_to=$_POST['actnow_to'];
      $monday=$_POST['monday'];$monday_time=$_POST['monday_time'];$monday_to=$_POST['monday_to'];
      $tuesday=$_POST['tuesday'];$tuesday_time=$_POST['tuesday_time'];$tuesday_to=$_POST['tuesday_to'];
      $wednesday=$_POST['wednesday'];$wednesday_time=$_POST['wednesday_time'];$wednesday_to=$_POST['wednesday_to'];
      $thursday=$_POST['thursday'];$thursday_time=$_POST['thursday_time'];$thursday_to=$_POST['thursday_to'];
      $friday=$_POST['friday'];$friday_time=$_POST['friday_time'];$friday_to=$_POST['friday_to'];
      $saturday=$_POST['saturday'];$saturday_time=$_POST['saturday_time'];$saturday_to=$_POST['saturday_to'];
      $sunday=$_POST['sunday'];$sunday_time=$_POST['sunday_time'];$sunday_to=$_POST['sunday_to'];
      $week_remarks=$_POST['week_remarks'];

      //Languages fields
      if(!empty($_POST['array_languages'])){
        $array_languages=explode(",",$_POST['array_languages']);
        $levelz = array("Native"=>"1", "Fluent"=>"2", "Intermediate"=>"3", "Basic"=>"4");
        foreach($array_languages as $int_lang){
          $language=explode(":",$int_lang);
          $acttObj->insert("interp_lang",array("lang"=>$language[0],"code"=>"id-".$int_id,"dated"=>date('Y-m-d'),"level"=>$levelz[$language[1]]));
        }
        $array_languages=" <h3 style='display:inline-block;border:1px solid grey;padding: 4px;border-radius: 4px;'>".implode("</h3> <h3 style='display:inline-block;border:1px solid grey;padding: 4px;border-radius: 4px;'>",$array_languages)."</h3> ";
        $array_languages=str_replace(":"," : ",$array_languages);
      }else{
        $array_languages="Not selected!";
      }
      //Upload profile photo
      if($_FILES["profile_photo"]["name"]!= NULL){
        $profile_photo=$acttObj->upload_file("lsuk_system/file_folder/interp_photo",$_FILES["profile_photo"]["name"],$_FILES["profile_photo"]["type"],$_FILES["profile_photo"]["tmp_name"],round(microtime(true)));
        $acttObj->update($table,array("interp_pix"=>$profile_photo),array("id"=>$int_id));
      }
      //Upload nin file
      if($_FILES["nin"]["name"]!= NULL){
        $nin=$acttObj->upload_file("lsuk_system/file_folder/nin",$_FILES["nin"]["name"],$_FILES["nin"]["type"],$_FILES["nin"]["tmp_name"],round(microtime(true)));
        $acttObj->update($table,array("nin"=>$nin),array("id"=>$int_id));
      }
      //Upload nrcpd file
      if($_FILES["nrcpd_file"]["name"]!= NULL){
        $nrcpd_file=$acttObj->upload_file("lsuk_system/file_folder/nrcpd_file",$_FILES["nrcpd_file"]["name"],$_FILES["nrcpd_file"]["type"],$_FILES["nrcpd_file"]["tmp_name"],round(microtime(true)));
        $acttObj->update($table,array("nrcpd_file"=>$nrcpd_file),array("id"=>$int_id));
      }
      //UK citizen
      $citizen=$_POST['citizen'];$permit=$_POST['citizen']=="No"?$_POST['permit']:"No";
      if($permit=="Yes" && $_FILES["permit_file"]["name"]!= NULL){
        $id_doc_no=$_POST['permit_number'];$id_doc_issue_date=$_POST['permit_issue_date'];$id_doc_expiry_date=$_POST['permit_expiry_date'];
        $id_doc_file=$acttObj->upload_file("lsuk_system/file_folder/issue_expiry_docs",$_FILES["permit_file"]["name"],$_FILES["permit_file"]["type"],$_FILES["permit_file"]["tmp_name"],round(microtime(true)));
      }
      if($citizen=="Yes" && $_FILES["passport_file"]["name"]!= NULL){
        $id_doc_no=$_POST['passport_number'];$id_doc_issue_date=$_POST['passport_issue_date'];$id_doc_expiry_date=$_POST['passport_expiry_date'];
        $id_doc_file=$acttObj->upload_file("lsuk_system/file_folder/issue_expiry_docs",$_FILES["passport_file"]["name"],$_FILES["passport_file"]["type"],$_FILES["passport_file"]["tmp_name"],round(microtime(true)));
      }
      if($citizen=="Yes"){
        $acttObj->update($table,array("uk_citizen"=>1),array("id"=>$int_id));
      }else{
        $acttObj->update($table,array("uk_citizen"=>0),array("id"=>$int_id));
      }
      if(isset($id_doc_file) && !empty($id_doc_file)){
        $acttObj->update($table,array("identityDocument"=>"Soft Copy","id_doc_file"=>$id_doc_file,"id_doc_no"=>$id_doc_no,"id_doc_issue_date"=>$id_doc_issue_date,"id_doc_expiry_date"=>$id_doc_expiry_date),array("id"=>$int_id));
      }
      //Evidence right to work document
      if($_POST['work_evid']=="Yes" && $_FILES["work_evid_file"]["name"]!= NULL){
        $work_evid_issue_date=$_POST['work_evid_issue_date'];$work_evid_expiry_date=$_POST['work_evid_expiry_date'];
        $work_evid_file=$acttObj->upload_file("lsuk_system/file_folder/issue_expiry_docs",$_FILES["work_evid_file"]["name"],$_FILES["work_evid_file"]["type"],$_FILES["work_evid_file"]["tmp_name"],"wef".round(microtime(true)));
      }
      if(isset($work_evid_file) && !empty($work_evid_file)){
        $acttObj->update($table,array("work_evid_file"=>$work_evid_file,"work_evid_issue_date"=>$work_evid_issue_date,"work_evid_expiry_date"=>$work_evid_expiry_date),array("id"=>$int_id));
      }
      //Driving license
      $is_drive=$_POST['is_drive'];
      if($is_drive=="Yes" && $_FILES["driving_license_file"]["name"]!= NULL){
        $driving_license_file=$acttObj->upload_file("lsuk_system/file_folder/anyOther",$_FILES["driving_license_file"]["name"],$_FILES["driving_license_file"]["type"],$_FILES["driving_license_file"]["tmp_name"],round(microtime(true)));
        $acttObj->update($table,array("anyOther"=>$driving_license_file),array("id"=>$int_id));
      }
      //Master Translation Document
      $is_master=$_POST['is_master'];
      if($is_master=="Yes" && $_FILES["master_file"]["name"]!= NULL){
        $master_file=$acttObj->upload_file("lsuk_system/file_folder/master_file",$_FILES["master_file"]["name"],$_FILES["master_file"]["type"],$_FILES["master_file"]["tmp_name"],round(microtime(true)));
        $acttObj->update($table,array("master_file"=>$master_file),array("id"=>$int_id));
      }
      //DPSI Document
      $is_dpsi=$_POST['is_dpsi'];
      if($is_dpsi=="Yes" && $_FILES["dpsi_file"]["name"]!= NULL){
        $dpsi_file=$acttObj->upload_file("lsuk_system/file_folder/dps",$_FILES["dpsi_file"]["name"],$_FILES["dpsi_file"]["type"],$_FILES["dpsi_file"]["tmp_name"],round(microtime(true)));
        $acttObj->update($table,array("dps"=>$dpsi_file),array("id"=>$int_id));
      }
      //DBS Document
      if($is_dbs=="Yes" && $_FILES["dbs_file"]["name"]!= NULL){
        $dbs_no=$_POST['dbs_no'];$dbs_issue_date=$_POST['dbs_issue_date'];$dbs_expiry_date=$_POST['dbs_expiry_date'];
        $dbs_file=$acttObj->upload_file("lsuk_system/file_folder/issue_expiry_docs",$_FILES["dbs_file"]["name"],$_FILES["dbs_file"]["type"],$_FILES["dbs_file"]["tmp_name"],round(microtime(true)));
        $acttObj->update($table,array("crbDbs"=>"Soft Copy","dbs_file"=>$dbs_file,"dbs_no"=>$dbs_no,"dbs_issue_date"=>$dbs_issue_date,"dbs_expiry_date"=>$dbs_expiry_date),array("id"=>$int_id));
      }
      //recongnized qualification
      $translation_qualifications=$_POST['translation_qualifications'];
      if($translation_qualifications=="Yes" && $_FILES["int_qualification_file"]["name"]!= NULL){
        $int_qualification_file=$acttObj->upload_file("lsuk_system/file_folder/int_qualification",$_FILES["int_qualification_file"]["name"],$_FILES["int_qualification_file"]["type"],$_FILES["int_qualification_file"]["tmp_name"],round(microtime(true)));
        $acttObj->update($table,array("int_qualification"=>$int_qualification_file),array("id"=>$int_id));
      }
      //experience years
      $is_experience=$_POST['is_experience'];
      $experience_years=$is_experience=="Yes"?$_POST['experience_years']:"No experience";
      //skills selected
      if(!empty($_POST['skills'])){
        foreach($_POST['skills'] as $int_skill){
          $acttObj->insert("interp_skill",array("skill"=>$int_skill,"code"=>"id-".$int_id,"dated"=>date('Y-m-d')));
        }
        $skills=" <h3 style='display:inline-block;border:1px solid grey;padding: 4px;border-radius: 4px;'>".implode("</h3> <h3 style='display:inline-block;border:1px solid grey;padding: 4px;border-radius: 4px;'>",$_POST['skills'])."</h3> ";
      }else{
        $skills="Not selected!";
      }
      //bank details
      $account_name=$_POST['account_name'];$bank_name=$_POST['bank_name'];$account_name=$_POST['account_name'];
      $branch=$_POST['branch'];$account_number=$_POST['account_number'];$sort_code=$_POST['sort_code'];
      //education details
      $institute=$_POST['institute'];$qualification=$_POST['qualification'];$from_date=$_POST['from_date'];$to_date=$_POST['to_date'];
      //references details
      $ref_name1=$_POST['ref_name1'];$ref_relationship1=$_POST['ref_relationship1'];$ref_company1=$_POST['ref_company1'];
      $ref_phone1=$_POST['ref_phone1'];$ref_email1=$_POST['ref_email1'];
      $ref_name2=$_POST['ref_name2'];$ref_relationship2=$_POST['ref_relationship2'];$ref_company2=$_POST['ref_company2'];
      $ref_phone2=$_POST['ref_phone2'];$ref_email2=$_POST['ref_email2'];
      //signatures
      $signature_name=$_POST['signature_name'];$signature_date=$_POST['signature_date'];
      //Insert into database
      $extra_data['is_drive']=$_POST['is_drive'];
      $extra_data['is_master']=$_POST['is_master'];
      $extra_data['is_dpsi']=$_POST['is_dpsi'];
      $extra_data['translation_qualifications']=$_POST['translation_qualifications'];
      if($_POST['is_experience']=="Yes"){$extra_data['experience_years']=$_POST['experience_years'];}
      $extra_data['is_nrcpd']=$_POST['is_nrcpd'];
      if($_POST['institute']){$extra_data['institute']=$_POST['institute'];}
      if($_POST['qualification']){$extra_data['qualification']=$_POST['qualification'];}
      if($_POST['from_date']){$extra_data['from_date']=$_POST['from_date'];}
      if($_POST['to_date']){$extra_data['to_date']=$_POST['to_date'];}
      $extra_data=json_encode($extra_data);
      $new_password='@'.strtok($row['name'], " ").substr(str_shuffle('0123456789abcdwxyz') , 0 , 5 ).substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0 , 3 );
      $acttObj->update($table,array("name"=>$first_name.' '.$last_name,"dob"=>$dob,"reg_date"=>$dated,"code"=>"id-".$int_id,"email"=>$email
      ,"password"=>$new_password,"contactNo"=>$contact_no,"contactNo2"=>$mobile_no,"interp_pix"=>$profile_photo,"interp"=>isset($interp)?"Yes":"No","telep"=>isset($telep)?"Yes":"No","trans"=>isset($trans)?"Yes":"No","gender"=>$gender,"dbs_checked"=>$is_dbs=="Yes"?0:1,"buildingName"=>$building_name
      ,"country"=>$country,"city"=>$city,"line1"=>$line1,"line2"=>$line2,"line3"=>$line3,"postCode"=>$post_code,"applicationForm"=>"Soft Copy","agreement"=>"Soft Copy","ni"=>$utr
      ,"bnakName"=>$bank_name." (".$branch.")","acName"=>$account_name,"acntCode"=>$sort_code,"acNo"=>$account_number,"sbmtd_by"=>"Online"
      ,"actnow"=>$actnow,"actnow_time"=>$actnow_time,"actnow_to"=>$actnow_to,"monday"=>$monday,"monday_time"=>$monday_time,"monday_to"=>$monday_to
      ,"tuesday"=>$tuesday,"tuesday_time"=>$tuesday_time,"tuesday_to"=>$tuesday_to,"wednesday"=>$wednesday,"wednesday_time"=>$wednesday_time,"wednesday_to"=>$wednesday_to
      ,"thursday"=>$thursday,"thursday_time"=>$thursday_time,"thursday_to"=>$thursday_to,"friday"=>$friday,"friday_time"=>$friday_time,"friday_to"=>$friday_to
      ,"saturday"=>$saturday,"saturday_time"=>$saturday_time,"saturday_to"=>$saturday_to,"sunday"=>$sunday,"sunday_time"=>$sunday_time,"sunday_to"=>$sunday_to,"week_remarks"=>$week_remarks,"is_temp"=>1,"extra_data"=>$extra_data),
      array("id"=>$int_id));
      //Email format to send
      if(!empty($ref_name1)){
        $append_ref1="<tr><td colspan='4' align='center' style='background: grey; color: white;font-size: 18px;'>Reference 1 Details</td></tr>
        <tr>
        <td style='border: 1px solid grey;padding:5px;background:gainsboro;font-weight:bold;'>Full Name (Relationship)</td>
        <td style='border: 1px solid yellowgreen;padding:5px;'>".$ref_name1." (".$ref_relationship1.")</td>
        <td style='border: 1px solid grey;padding:5px;background:gainsboro;font-weight:bold;'>Company</td>
        <td style='border: 1px solid yellowgreen;padding:5px;'>".$ref_company1."</td>
        </tr>
        <tr>
        <td style='border: 1px solid grey;padding:5px;background:gainsboro;font-weight:bold;'>Phone</td>
        <td style='border: 1px solid yellowgreen;padding:5px;'>".$ref_phone1."</td>
        <td style='border: 1px solid grey;padding:5px;background:gainsboro;font-weight:bold;'>Email</td>
        <td style='border: 1px solid yellowgreen;padding:5px;'>".$ref_email1."</td>
        </tr>";
        //Add reference 1 record if any
        $last_id_1 = $acttObj->get_id("int_references");
        $acttObj->update("int_references",array("name"=>$ref_name1,"relation"=>$ref_relationship1,"company"=>$ref_company1,"phone"=>$ref_phone1,"email"=>$ref_email1,"int_id"=>$int_id),array("id"=>$last_id_1));
        $name_of_int=$acttObj->read_specific("name","interpreter_reg","id=".$int_id)['name'];
        $ref_message1="Hello & good day ".$ref_name1.",<br>
        ".$name_of_int." has given us the permission to contact you for employment opportunity at LSUK.<br>
        We would like to confirm the profile of <b>".$int_name.        
        "</b>, who mentioned you as a referee while applying as a ".$array_types_label."<br>
        Please fill out the feedback form regarding to his/her past work history using this link <b><a style='text-decoration: none;font-size: 16px;border: 1px solid;padding: 4px;border-radius: 4px;background: #618cd6;color: white;' href='https://lsuk.org/reference_confirmation.php?id=".base64_encode($last_id_1)."'>CLICK HERE</a></b>
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
      }
      if(!empty($ref_name2)){
        $append_ref2="<tr><td colspan='4' align='center' style='background: grey; color: white;font-size: 18px;'>Reference 2 Details</td></tr>
        <tr>
        <td style='border: 1px solid grey;padding:5px;background:gainsboro;font-weight:bold;'>Full Name (Relationship)</td>
        <td style='border: 1px solid yellowgreen;padding:5px;'>".$ref_name2." (".$ref_relationship2.")</td>
        <td style='border: 1px solid grey;padding:5px;background:gainsboro;font-weight:bold;'>Company</td>
        <td style='border: 1px solid yellowgreen;padding:5px;'>".$ref_company2."</td>
        </tr>
        <tr>
        <td style='border: 1px solid grey;padding:5px;background:gainsboro;font-weight:bold;'>Phone</td>
        <td style='border: 1px solid yellowgreen;padding:5px;'>".$ref_phone2."</td>
        <td style='border: 1px solid grey;padding:5px;background:gainsboro;font-weight:bold;'>Email</td>
        <td style='border: 1px solid yellowgreen;padding:5px;'>".$ref_email2."</td>
        </tr>";
        //Add reference 2 record if any
        $last_id_2 = $acttObj->get_id("int_references");
        $acttObj->update("int_references",array("name"=>$ref_name2,"relation"=>$ref_relationship2,"company"=>$ref_company2,"phone"=>$ref_phone2,"email"=>$ref_email2,"int_id"=>$int_id),array("id"=>$last_id_2));
        $ref_message2="Hello & good day ".$ref_name2.",<br>
        ".$name_of_int." has given us the permission to contact you for employment opportunity at LSUK.<br>
        We would like to confirm the profile of <b>".$int_name.        
        "</b>, who mentioned you as a referee while applying as a ".$array_types_label."<br>
        Please fill out the feedback form regarding to his/her past work history using this link <b><a style='text-decoration: none;font-size: 16px;border: 1px solid;padding: 4px;border-radius: 4px;background: #618cd6;color: white;' href='https://lsuk.org/reference_confirmation.php?id=".base64_encode($last_id_2)."'>CLICK HERE</a></b>
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
      }
      $message = "<style type='text/css'>
        table.myTable{border-collapse: collapse;}
        table.myTable td, table.myTable th {border: 1px solid yellowgreen;padding: 5px;}
        </style>
        <table class='myTable' width='80%'>
        <tr><td colspan='4' align='center' style='background: grey; color: white;font-size: 18px;'>PERSONAL DETAILS</td></tr>
        <tr>
        <td style='border: 1px solid grey;padding:5px;background:gainsboro;font-weight:bold;'>First Name</td>
        <td style='border: 1px solid yellowgreen;padding:5px;'>".$first_name."</td>
        <td style='border: 1px solid grey;padding:5px;background:gainsboro;font-weight:bold;'>last Name</td>
        <td style='border: 1px solid yellowgreen;padding:5px;'>".$last_name."</td>
        </tr>
        <tr>
        <td style='border: 1px solid grey;padding:5px;background:gainsboro;font-weight:bold;'>Date of Birth</td>
        <td style='border: 1px solid yellowgreen;padding:5px;'>".$dob."</td>
        <td style='border: 1px solid grey;padding:5px;background:gainsboro;font-weight:bold;'>Gender</td>
        <td style='border: 1px solid yellowgreen;padding:5px;'>".$gender."</td>
        </tr>
        <tr>
        <td style='border: 1px solid grey;padding:5px;background:gainsboro;font-weight:bold;'>Contact Number</td>
        <td style='border: 1px solid yellowgreen;padding:5px;'>".$contact_no."</td>
        <td style='border: 1px solid grey;padding:5px;background:gainsboro;font-weight:bold;'>Mobile Number</td>
        <td style='border: 1px solid yellowgreen;padding:5px;'>".$mobile_no."</td>
        </tr>
        <tr>
        <td style='border: 1px solid grey;padding:5px;background:gainsboro;font-weight:bold;'>Email</td>
        <td style='border: 1px solid yellowgreen;padding:5px;'>".$email."</td>
        <td style='border: 1px solid grey;padding:5px;background:gainsboro;font-weight:bold;'>NI / UTR #</td>
        <td style='border: 1px solid yellowgreen;padding:5px;'>".$utr."</td>
        </tr>
        <tr>
        <td colspan='1' style='border: 1px solid grey;padding:5px;background:gainsboro;font-weight:bold;'>Working As</td>
        <td colspan='3' style='border: 1px solid yellowgreen;padding:5px;'>".$array_types."</td>
        </tr>
        <tr><td colspan='4' align='center' style='background: grey; color: white;font-size: 18px;'>ADDRESS DETAILS</td></tr>
        <tr>
        <td style='border: 1px solid grey;padding:5px;background:gainsboro;font-weight:bold;'>Building Name</td>
        <td style='border: 1px solid yellowgreen;padding:5px;'>".$building_name."</td>
        <td style='border: 1px solid grey;padding:5px;background:gainsboro;font-weight:bold;'>Line 1</td>
        <td style='border: 1px solid yellowgreen;padding:5px;'>".$line1."</td>
        </tr>
        <tr>
        <td style='border: 1px solid grey;padding:5px;background:gainsboro;font-weight:bold;'>Line 2</td>
        <td style='border: 1px solid yellowgreen;padding:5px;'>".$line2."</td>
        <td style='border: 1px solid grey;padding:5px;background:gainsboro;font-weight:bold;'>Line 3</td>
        <td style='border: 1px solid yellowgreen;padding:5px;'>".$line3."</td>
        </tr>
        <tr>
        <td style='border: 1px solid grey;padding:5px;background:gainsboro;font-weight:bold;'>Country (City)</td>
        <td style='border: 1px solid yellowgreen;padding:5px;'>".$country." (".$city.")</td>
        <td style='border: 1px solid grey;padding:5px;background:gainsboro;font-weight:bold;'>Post Code</td>
        <td style='border: 1px solid yellowgreen;padding:5px;'>".$post_code."</td>
        </tr>
        <tr><td colspan='4' align='center' style='background: grey; color: white;font-size: 18px;'>WORK AVAILABILITY</td></tr>
        <tr>
        <td style='border: 1px solid grey;padding:5px;background:gainsboro;font-weight:bold;'>Day</td>
        <td style='border: 1px solid yellowgreen;padding:5px;background:gainsboro;font-weight:bold;'>Availability</td>
        <td style='border: 1px solid grey;padding:5px;background:gainsboro;font-weight:bold;'>From</td>
        <td style='border: 1px solid yellowgreen;padding:5px;background:gainsboro;font-weight:bold;'>To</td>
        </tr>
        <tr>
        <td style='border: 1px solid grey;padding:5px;background:gainsboro;font-weight:bold;'>Active Dates*</td>
        <td style='border: 1px solid yellowgreen;padding:5px;'>".$actnow."</td>
        <td style='border: 1px solid grey;padding:5px;background:gainsboro;font-weight:bold;'>".$actnow_time."</td>
        <td style='border: 1px solid yellowgreen;padding:5px;'>".$actnow_to."</td>
        </tr>
        <tr>
        <td style='border: 1px solid grey;padding:5px;background:gainsboro;font-weight:bold;'>Monday</td>
        <td style='border: 1px solid yellowgreen;padding:5px;'>".$monday."</td>
        <td style='border: 1px solid grey;padding:5px;background:gainsboro;font-weight:bold;'>".$monday_time."</td>
        <td style='border: 1px solid yellowgreen;padding:5px;'>".$monday_to."</td>
        </tr>
        <tr>
        <td style='border: 1px solid grey;padding:5px;background:gainsboro;font-weight:bold;'>Tuesday</td>
        <td style='border: 1px solid yellowgreen;padding:5px;'>".$tuesday."</td>
        <td style='border: 1px solid grey;padding:5px;background:gainsboro;font-weight:bold;'>".$tuesday_time."</td>
        <td style='border: 1px solid yellowgreen;padding:5px;'>".$tuesday_to."</td>
        </tr>
        <tr>
        <td style='border: 1px solid grey;padding:5px;background:gainsboro;font-weight:bold;'>Wednesday</td>
        <td style='border: 1px solid yellowgreen;padding:5px;'>".$wednesday."</td>
        <td style='border: 1px solid grey;padding:5px;background:gainsboro;font-weight:bold;'>".$wednesday_time."</td>
        <td style='border: 1px solid yellowgreen;padding:5px;'>".$wednesday_to."</td>
        </tr>
        <tr>
        <td style='border: 1px solid grey;padding:5px;background:gainsboro;font-weight:bold;'>Thursday</td>
        <td style='border: 1px solid yellowgreen;padding:5px;'>".$thursday."</td>
        <td style='border: 1px solid grey;padding:5px;background:gainsboro;font-weight:bold;'>".$thursday_time."</td>
        <td style='border: 1px solid yellowgreen;padding:5px;'>".$thursday_to."</td>
        </tr>
        <tr>
        <td style='border: 1px solid grey;padding:5px;background:gainsboro;font-weight:bold;'>Friday</td>
        <td style='border: 1px solid yellowgreen;padding:5px;'>".$friday."</td>
        <td style='border: 1px solid grey;padding:5px;background:gainsboro;font-weight:bold;'>".$friday_time."</td>
        <td style='border: 1px solid yellowgreen;padding:5px;'>".$friday_to."</td>
        </tr>
        <tr>
        <td style='border: 1px solid grey;padding:5px;background:gainsboro;font-weight:bold;'>Saturday</td>
        <td style='border: 1px solid yellowgreen;padding:5px;'>".$saturday."</td>
        <td style='border: 1px solid grey;padding:5px;background:gainsboro;font-weight:bold;'>".$saturday_time."</td>
        <td style='border: 1px solid yellowgreen;padding:5px;'>".$saturday_to."</td>
        </tr>
        <tr>
        <td style='border: 1px solid grey;padding:5px;background:gainsboro;font-weight:bold;'>Sunday</td>
        <td style='border: 1px solid yellowgreen;padding:5px;'>".$sunday."</td>
        <td style='border: 1px solid grey;padding:5px;background:gainsboro;font-weight:bold;'>".$sunday_time."</td>
        <td style='border: 1px solid yellowgreen;padding:5px;'>".$sunday_to."</td>
        </tr>
        <tr>
        <td colspan='1' style='border: 1px solid grey;padding:5px;background:gainsboro;font-weight:bold;'>Weekly Remarks</td>
        <td colspan='3' style='border: 1px solid yellowgreen;padding:5px;'>".$week_remarks."</td>
        </tr>
        <tr><td colspan='4' align='center' style='background: grey; color: white;font-size: 18px;'>LANGUAGES DETAILS</td></tr>
        <tr>
        <td colspan='1' style='border: 1px solid grey;padding:5px;background:gainsboro;font-weight:bold;'>Speaking Languages</td>
        <td colspan='3' style='border: 1px solid yellowgreen;padding:5px;'>".$array_languages."</td>
        </tr>
        <tr>
        <td style='border: 1px solid grey;padding:5px;background:gainsboro;font-weight:bold;'>UK Citizen?</td>
        <td style='border: 1px solid yellowgreen;padding:5px;'>".$citizen."</td>
        <td style='border: 1px solid grey;padding:5px;background:gainsboro;font-weight:bold;'>Permit</td>
        <td style='border: 1px solid yellowgreen;padding:5px;'>".$permit."</td>
        </tr>
        <tr>
        <td style='border: 1px solid grey;padding:5px;background:gainsboro;font-weight:bold;'>Driving?</td>
        <td style='border: 1px solid yellowgreen;padding:5px;'>".$is_drive."</td>
        <td style='border: 1px solid grey;padding:5px;background:gainsboro;font-weight:bold;'>Recognised Interpreting / Translation Qualification?</td>
        <td style='border: 1px solid yellowgreen;padding:5px;'>".$translation_qualifications."</td>
        </tr>
        <tr>
        <td style='border: 1px solid grey;padding:5px;background:gainsboro;font-weight:bold;'>Professional Experience?</td>
        <td style='border: 1px solid yellowgreen;padding:5px;'>".$is_experience."</td>
        <td style='border: 1px solid grey;padding:5px;background:gainsboro;font-weight:bold;'>Experience Years</td>
        <td style='border: 1px solid yellowgreen;padding:5px;'>".$experience_years."</td>
        </tr>
        <tr>
        <td colspan='1' style='border: 1px solid grey;padding:5px;background:gainsboro;font-weight:bold;'>Areas of specialization</td>
        <td colspan='3' style='border: 1px solid yellowgreen;padding:5px;'>".$skills."</td>
        </tr>
        <tr><td colspan='4' align='center' style='background: grey; color: white;font-size: 18px;'>BANK DETAILS</td></tr>
        <tr>
        <td style='border: 1px solid grey;padding:5px;background:gainsboro;font-weight:bold;'>Account Name</td>
        <td style='border: 1px solid yellowgreen;padding:5px;'>".$account_name."</td>
        <td style='border: 1px solid grey;padding:5px;background:gainsboro;font-weight:bold;'>Bank Name (Branch)</td>
        <td style='border: 1px solid yellowgreen;padding:5px;'>".$bank_name." (".$branch.")</td>
        </tr>
        <tr>
        <td style='border: 1px solid grey;padding:5px;background:gainsboro;font-weight:bold;'>Account Number</td>
        <td style='border: 1px solid yellowgreen;padding:5px;'>".$account_number."</td>
        <td style='border: 1px solid grey;padding:5px;background:gainsboro;font-weight:bold;'>Sort Code</td>
        <td style='border: 1px solid yellowgreen;padding:5px;'>".$sort_code."</td>
        </tr>
        <tr><td colspan='4' align='center' style='background: grey; color: white;font-size: 18px;'>EDUCATIONAL DETAILS</td></tr>
        <tr>
        <td style='border: 1px solid grey;padding:5px;background:gainsboro;font-weight:bold;'>Institute Name</td>
        <td style='border: 1px solid yellowgreen;padding:5px;'>".$institute."</td>
        <td style='border: 1px solid grey;padding:5px;background:gainsboro;font-weight:bold;'>Qualification</td>
        <td style='border: 1px solid yellowgreen;padding:5px;'>".$qualification."</td>
        </tr>
        <tr>
        <td style='border: 1px solid grey;padding:5px;background:gainsboro;font-weight:bold;'>From Date</td>
        <td style='border: 1px solid yellowgreen;padding:5px;'>".$from_date."</td>
        <td style='border: 1px solid grey;padding:5px;background:gainsboro;font-weight:bold;'>To Date</td>
        <td style='border: 1px solid yellowgreen;padding:5px;'>".$to_date."</td>
        </tr>
        <tr><td colspan='4' align='center' style='background: grey; color: white;font-size: 18px;'>REFERENCES</td></tr>
        ".$append_ref1.$append_ref2."
        <tr><td colspan='4' align='center' style='background: grey; color: white;font-size: 18px;'>DISCLAIMER AND SIGNATURE</td></tr>
        <tr>
        <td style='border: 1px solid grey;padding:5px;background:gainsboro;font-weight:bold;'>Signature Name</td>
        <td style='border: 1px solid yellowgreen;padding:5px;'>".$signature_name."</td>
        <td style='border: 1px solid grey;padding:5px;background:gainsboro;font-weight:bold;'>Signature Date</td>
        <td style='border: 1px solid yellowgreen;padding:5px;'>".$signature_date."</td>
        </tr>
        </table>";
      try {
        $mail->SMTPDebug = 2;
        $mail->SMTPAuth   = true;
        $mail->Username   = 'info@lsuk.org';
        $mail->Password   = 'LangServ786';
        $mail->SMTPSecure = 'tls';
        $mail->Port       = 587;
        $from_add='info@lsuk.org';
		$from_name='LSUK';
		$mail->setFrom($from_add, $from_name);
        $mail->addAddress('hr@lsuk.org');
        //$mail->addAddress('waqarecp1992@gmail.com');
        $mail->addReplyTo('hr@lsuk.org', $from_name);
        $mail->isHTML(true);
        $mail->Subject = 'New Interpreter Registration Request';
        $mail->Body = $message;
		if($_FILES["profile_photo"]["name"]!= NULL){
	        $mail->AddAttachment("lsuk_system/file_folder/interp_photo/".$profile_photo, "Profile Photo");
        }
        if(isset($work_evid_file) && !empty($work_evid_file)){
	        $mail->AddAttachment("lsuk_system/file_folder/issue_expiry_docs/".$work_evid_file, "Right to work evidence");
        }
        if(isset($id_doc_file) && !empty($id_doc_file)){
	        $mail->AddAttachment("lsuk_system/file_folder/issue_expiry_docs/".$id_doc_file, "Identity Document");
        }
        if($is_dbs=="Yes" && $_FILES["dbs_file"]["name"]!= NULL){
          $mail->AddAttachment("lsuk_system/file_folder/issue_expiry_docs/".$dbs_file, "DBS Document");
        }
        if($is_drive=="Yes" && $_FILES["driving_license_file"]["name"]!= NULL){
	        $mail->AddAttachment("lsuk_system/file_folder/anyOther/".$driving_license_file, "Driving License Attachment");
        }
        if($is_master=="Yes" && $_FILES["master_file"]["name"]!= NULL){
	        $mail->AddAttachment("lsuk_system/file_folder/master_file/".$dpsi_file, "Translation Master Attachment");
        }
        if($is_dpsi=="Yes" && $_FILES["dpsi_file"]["name"]!= NULL){
	        $mail->AddAttachment("lsuk_system/file_folder/dps/".$dpsi_file, "DPSI Attachment");
        }
        if($translation_qualifications=="Yes" && $_FILES["int_qualification_file"]["name"]!= NULL){
	        $mail->AddAttachment("lsuk_system/file_folder/int_qualification/".$int_qualification_file, "Translation Qualification Document");
        }
        if($_FILES["nin"]["name"]!= NULL){
	        $mail->AddAttachment("lsuk_system/file_folder/nin/".$nin, "NI / UTR Attachment");
        }
        if($_FILES["nrcpd_file"]["name"]!= NULL){
	        $mail->AddAttachment("lsuk_system/file_folder/nrcpd_file/".$nrcpd_file, "NRCPD Attachment");
        }
		if($mail->send()){
            $mail->ClearAllRecipients();
          	$mail->clearAttachments();
            if(!empty($email)){
              $mail->addAddress($email);
              $mail->addReplyTo('hr@lsuk.org', $from_name);
              $mail->isHTML(true);
              $mail->Subject = "LSUK account registration notification";
              $mail->Body    = "Hello ".$first_name." ".$last_name.",<br>
              We have received your below details for your account registration request.<br>
              We will approve your account since we verify all your details.<br>
              You can then login to your account at LSUK using below credentials:<br>
              <table>
              <tbody>
              <tr><td style='border: 1px solid black;padding:5px;'>Username/Email:</td>
              <td style='border:1px solid black;padding:5px'><h3>".$email."</h3></td>
              </tr>
              <tr><td style='border: 1px solid black;padding:5px;'>Password:</td>
              <td style='border:1px solid black;padding:5px'><h3>".$new_password."</h3></td>
              </tr>
              </tbody></table><br>
              <b><a style='text-decoration: none;font-size: 16px;border: 1px solid;padding: 4px;border-radius: 4px;background: #618cd6;color: white;' href='https://lsuk.org/login.php'>LOGIN TO LSUK NOW</a></b><br>
              If you want to change your password you can use this link <a href='https://lsuk.org/update_password.php'>HERE</a><br>
              Here are your submitted details:<br>
              ".$message."
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
            }
            if(isset($_POST['referee_permission'])){
                $ref_subject="Reference confirmation of interpreter profile at LSUK";
                if(!empty($ref_email1)){
                $mail->addAddress($ref_email1);
                $mail->addReplyTo('hr@lsuk.org', $from_name);
                $mail->isHTML(true);
                $mail->Subject = $ref_subject;
                $mail->Body    = $ref_message1;
                $mail->send();
                $mail->ClearAllRecipients();
                }
                if(!empty($ref_email2)){
                $mail->addAddress($ref_email2);
                $mail->addReplyTo('hr@lsuk.org', $from_name);
                $mail->isHTML(true);
                $mail->Subject = $ref_subject;
                $mail->Body    = $ref_message2;
                $mail->send();
                $mail->ClearAllRecipients();
                }
            }
            $msg='<div class="alert alert-success alert-dismissible col-md-6 col-md-offset-3 text-center">
            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times; </a>
            <i class="glyphicon glyphicon-check"></i> Success: Your request has been sent successfully.<br>
            We will respond within 24 hours.Thank you
            </div>';
            //echo '<script>setTimeout(function(){ window.location="interp_reg.php"; }, 2500); </script>';
        }else{
            $msg='<div class="alert alert-danger alert-dismissible col-md-6 col-md-offset-3 text-center">
            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times; </a>
            <i class="glyphicon glyphicon-check"></i> Failed: Failed to submit your request !<br>
            kindly try again.
            </div>';
        }
      } catch (Exception $e) {
        $msg='<div class="alert alert-danger alert-dismissible col-md-6 col-md-offset-3 text-center">
              <a href="#" class="close" data-dismiss="alert" aria-label="close">&times; </a>
              <i class="glyphicon glyphicon-check"></i> Failed: Mailer library error occured!.
              </div>';
      }
    }else{
      echo '<script>alert("Kindly verify your catpahca. Kindly try again.");</script>';
    }
  }else{
    echo '<script>alert("Kindly verify your catpahca. Kindly try again.");window.history.back(-1);</script>';
  }
} ?>
<!DOCTYPE HTML>
<html class="no-js">
   <head>
      <?php include'source/header.php'; ?>
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" />
      <style>
        .ri {margin-top: 7px !important;}
        .ri .label {font-size: 100% ;padding: .5em 0.6em 0.5em;}
        .mt{margin-top: 2px;}
        select{-webkit-appearance: auto !important;}
        .multiselect {min-width: 218px;}.multiselect-container {max-height: 400px;overflow-y: auto;max-width: 380px;}.multiselect-native-select{display:block;}.multiselect-container li.active label.radio,.multiselect-container li.active label.checkbox{color:white;}
        .hidden_online{display:none;}
      <style>.ri{margin-top: 7px;}
.ri .label{font-size:100%;padding: .5em 0.6em 0.5em;}
.checkbox-inline+.checkbox-inline, .radio-inline+.radio-inline {
    margin-top: 4px;}
    .multiselect {min-width: 295px;}.multiselect-container {max-height: 400px;overflow-y: auto;max-width: 380px;}.multiselect-native-select{display:block;}.multiselect-container li.active label.checkbox{color:white;}
    .sky-form select{-webkit-appearance: auto !important;}

.stepwizard-step p {
    margin-top: 0px;
    color:#666;
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
.stepwizard .btn.disabled, .stepwizard .btn[disabled], .stepwizard fieldset[disabled] .btn {
    opacity:1 !important;
    color:#bbb;
}
.stepwizard-row:before {
    top: 14px;
    bottom: 0;
    position: absolute;
    content:" ";
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
</style>
   </head>
   <body class="boxed">
      <!-- begin container -->
      <div id="wrap">
      <!-- begin header -->
      <?php include'source/top_nav.php'; ?>
      <!-- end header -->
      <!-- begin page title -->
      <section id="page-title">
         <div class="container clearfix">
            <h1>Interpreter Registration Form</h1>
            <nav id="breadcrumbs">
               <ul>
                  <li><a href="index.php">Home</a> &rsaquo;</li>
                  <li><a href="<?php echo basename($_SERVER['HTTP_REFERER']);?>"><?php echo ucwords(basename($_SERVER['HTTP_REFERER'], '.php'));?></a> &rsaquo;</li>
                  <li><?php echo ucwords(basename($_SERVER['REQUEST_URI'], '.php'));?></li>
               </ul>
            </nav>
         </div>
      </section>
         <!-- begin content -->
         <section id="content" class="container-fluid clearfix">
         <?php if(isset($msg) && !empty($msg)){echo $msg;} ?>
          <div class="stepwizard">
              <div class="stepwizard-row setup-panel">
                  <div class="stepwizard-step col-md-3 col-xs-4"> 
                      <a href="#step-1" type="button" class="btn btn-primary btn-circle">1</a>
                      <p><small>Personal Details</small></p>
                  </div>
                  <div class="stepwizard-step col-md-2 col-xs-4"> 
                      <a href="#step-2" type="button" class="btn btn-default btn-circle disabled" disabled="disabled">2</a>
                      <p><small>Address Details</small></p>
                  </div>
                  <div class="stepwizard-step col-md-2 col-xs-4"> 
                      <a href="#step-3" type="button" class="btn btn-default btn-circle disabled" disabled="disabled">3</a>
                      <p><small>Work Availability</small></p>
                  </div>
                  <div class="stepwizard-step col-md-2 col-xs-4"> 
                      <a href="#step-4" type="button" class="btn btn-default btn-circle disabled" disabled="disabled">4</a>
                      <p><small>Interpreting Profile</small></p>
                  </div>
                  <div class="stepwizard-step col-md-2 col-xs-4"> 
                      <a href="#step-5" type="button" class="btn btn-default btn-circle disabled" disabled="disabled">5</a>
                      <p><small>References & Disclaimer</small></p>
                  </div>
              </div>
          </div>

            <form class="col-md-12" action="#" method="post" enctype="multipart/form-data">
              <div class="panel panel-info setup-content" id="step-1">
                <div class="panel-heading">
                    <h3 class="panel-title">Personal Details</h3>
                </div>
                <div class="panel-body">
                  <div class="row">
                      <div class="form-group col-lg-3 col-md-4 col-sm-6">
                        <img id="output" src="lsuk_system/file_folder/interp_photo/profile.png" title="Interpreter picture" name="output" class="img-thumbnail" style="max-width: 140px;max-height: 140px;min-width: 140px;min-height: 140px;" />
                        <br><label style="margin-top:8px;" for="profile_photo">Upload Profile Photo <i title="Select a clear square photo of yourself facing towards camera" class="fa fa-question-circle"></i></label>
                        <input name="profile_photo" id="profile_photo" type="file" class="form-control" accept="image/*" required> 
                      </div>
                      <div class="form-group col-lg-3 col-md-4 col-sm-6">
                        <label>First Name</label>
                        <input name="first_name" id="first_name" type="text" class="form-control" required />
                      </div>
                      <div class="form-group col-lg-3 col-md-4 col-sm-6">
                        <label>Last Name</label>
                        <input name="last_name" id="last_name" type="text" class="form-control" required />
                      </div>
                      <div class="form-group col-lg-3 col-md-4 col-sm-6">
                        <label>Date of Birth</label>     
                        <input name="dob" id="dob" type="date" class="form-control" required />
                      </div>
                      <div class="form-group col-lg-3 col-md-4 col-sm-6">
                        <label class="optional">Gender</label>
                        <select name="gender" id="gender" class="form-control">
                          <option>Male</option>
                          <option>Female</option>
                          <option>Rather Not Say</option>
                        </select>
                      </div>
                      <div class="form-group col-lg-3 col-md-4 col-sm-6">
                        <label>Contact Number</label>
                        <input name="contact_no" type="text" class="form-control" required />
                      </div>
                      <div class="form-group col-lg-3 col-md-4 col-sm-6">
                        <label>Mobile Number</label>     
                        <input name="mobile_no" type="text" class="form-control" required />
                      </div>
                      <div class="form-group col-lg-3 col-md-4 col-sm-6">
                        <label>Email Address</label>     
                        <input onfocus="check_fields()" name="email" type="email" id="email" class="form-control" required onblur="check_existing(this)"/>
                      </div>
                      <div class="form-group col-lg-3 col-md-4 col-sm-6">
                        <label>NI / UTR Number</label>     
                        <input name="utr" type="text" class="form-control" required />
                      </div>
                      <div class="form-group col-lg-3 col-md-4 col-sm-6">
                        <label>Upload NI/UTR File</label>
                        <input name="nin" id="nin" type="file" class="form-control" onchange="max_upload(this);" required> 
                      </div>
                      <div class="form-group col-sm-12">
                        <label class="optional">You Are</label><br>
                        <div class="radio-inline ri">
                          <label><input type="checkbox" name="interp" value="Interpreter">
                          <span class="label label-primary">Interpreter <i class="fa fa-user"></i></span></label>
                        </div>
                        <div class="radio-inline ri">
                          <label><input type="checkbox" name="telep" value="Telephone Interpreter">
                          <span class="label label-info">Telephone Interpreter <i class="fa fa-phone"></i></span></label>
                        </div>
                        <div class="radio-inline ri">
                          <label><input type="checkbox" name="trans" value="Translator">
                          <span class="label label-success">Translator <i class="fa fa-language"></i></span></label>
                        </div>
                      </div>
                  </div>
                  <div class="form-group col-md-12">
                      <button class="btn btn-primary nextBtn pull-right" type="button">Next <i class="fa fa-angle-right"></i><i class="fa fa-angle-right"></i></button>
                  </div>
                  </div>
              </div>
              <div class="panel panel-info setup-content" id="step-2">
                <div class="panel-heading">
                    <h3 class="panel-title">Address Details</h3>
                </div>
                <div class="panel-body">
                <div class="row">
                    <div class="form-group col-lg-3 col-md-4 col-sm-6">
                      <?php
                      $country_array = array("Afghanistan"=>"Afghanistan (افغانستان)", "Aland Islands"=>"Aland Islands (Åland)", "Albania"=>"Albania (Shqipëria)", "Algeria"=>"Algeria (الجزائر)", "American Samoa"=>"American Samoa (American Samoa)", "Andorra"=>"Andorra (Andorra)", "Angola"=>"Angola (Angola)", "Anguilla"=>"Anguilla (Anguilla)", "Antarctica"=>"Antarctica (Antarctica)", "Antigua And Barbuda"=>"Antigua And Barbuda (Antigua and Barbuda)", "Argentina"=>"Argentina (Argentina)", "Armenia"=>"Armenia (Հայաստան)", "Aruba"=>"Aruba (Aruba)", "Australia"=>"Australia (Australia)", "Austria"=>"Austria (Österreich)", "Azerbaijan"=>"Azerbaijan (Azərbaycan)", "Bahamas The"=>"Bahamas The (Bahamas)", "Bahrain"=>"Bahrain (‏البحرين)", "Bangladesh"=>"Bangladesh (Bangladesh)", "Barbados"=>"Barbados (Barbados)", "Belarus"=>"Belarus (Белару́сь)", "Belgium"=>"Belgium (België)", "Belize"=>"Belize (Belize)", "Benin"=>"Benin (Bénin)", "Bermuda"=>"Bermuda (Bermuda)", "Bhutan"=>"Bhutan (ʼbrug-yul)", "Bolivia"=>"Bolivia (Bolivia)", "Bonaire, Sint Eustatius and Saba"=>"Bonaire, Sint Eustatius and Saba (Caribisch Nederland)", "Bosnia and Herzegovina"=>"Bosnia and Herzegovina (Bosna i Hercegovina)", "Botswana"=>"Botswana (Botswana)", "Bouvet Island"=>"Bouvet Island (Bouvetøya)", "Brazil"=>"Brazil (Brasil)", "British Indian Ocean Territory"=>"British Indian Ocean Territory (British Indian Ocean Territory)", "Brunei"=>"Brunei (Negara Brunei Darussalam)", "Bulgaria"=>"Bulgaria (България)", "Burkina Faso"=>"Burkina Faso (Burkina Faso)", "Burundi"=>"Burundi (Burundi)", "Cambodia"=>"Cambodia (Kâmpŭchéa)", "Cameroon"=>"Cameroon (Cameroon)", "Canada"=>"Canada (Canada)", "Cape Verde"=>"Cape Verde (Cabo Verde)", "Cayman Islands"=>"Cayman Islands (Cayman Islands)", "Central African Republic"=>"Central African Republic (Ködörösêse tî Bêafrîka)", "Chad"=>"Chad (Tchad)", "Chile"=>"Chile (Chile)", "China"=>"China (中国)", "Christmas Island"=>"Christmas Island (Christmas Island)", "Cocos (Keeling) Islands"=>"Cocos (Keeling) Islands (Cocos (Keeling) Islands)", "Colombia"=>"Colombia (Colombia)", "Comoros"=>"Comoros (Komori)", "Congo"=>"Congo (République du Congo)", "Congo The Democratic Republic Of The"=>"Congo The Democratic Republic Of The (République démocratique du Congo)", "Cook Islands"=>"Cook Islands (Cook Islands)", "Costa Rica"=>"Costa Rica (Costa Rica)", "Cote D'Ivoire (Ivory Coast)"=>"Cote D'Ivoire (Ivory Coast) ()", "Croatia (Hrvatska)"=>"Croatia (Hrvatska) (Hrvatska)", "Cuba"=>"Cuba (Cuba)", "Curaçao"=>"Curaçao (Curaçao)", "Cyprus"=>"Cyprus (Κύπρος)", "Czech Republic"=>"Czech Republic (Česká republika)", "Denmark"=>"Denmark (Danmark)", "Djibouti"=>"Djibouti (Djibouti)", "Dominica"=>"Dominica (Dominica)", "Dominican Republic"=>"Dominican Republic (República Dominicana)", "East Timor"=>"East Timor (Timor-Leste)", "Ecuador"=>"Ecuador (Ecuador)", "Egypt"=>"Egypt (مصر‎)", "El Salvador"=>"El Salvador (El Salvador)", "Equatorial Guinea"=>"Equatorial Guinea (Guinea Ecuatorial)", "Eritrea"=>"Eritrea (ኤርትራ)", "Estonia"=>"Estonia (Eesti)", "Ethiopia"=>"Ethiopia (ኢትዮጵያ)", "Falkland Islands"=>"Falkland Islands (Falkland Islands)", "Faroe Islands"=>"Faroe Islands (Føroyar)", "Fiji Islands"=>"Fiji Islands (Fiji)", "Finland"=>"Finland (Suomi)", "France"=>"France (France)", "French Guiana"=>"French Guiana (Guyane française)", "French Polynesia"=>"French Polynesia (Polynésie française)", "French Southern Territories"=>"French Southern Territories (Territoire des Terres australes et antarctiques fr)", "Gabon"=>"Gabon (Gabon)", "Gambia The"=>"Gambia The (Gambia)", "Georgia"=>"Georgia (საქართველო)", "Germany"=>"Germany (Deutschland)", "Ghana"=>"Ghana (Ghana)", "Gibraltar"=>"Gibraltar (Gibraltar)", "Greece"=>"Greece (Ελλάδα)", "Greenland"=>"Greenland (Kalaallit Nunaat)", "Grenada"=>"Grenada (Grenada)", "Guadeloupe"=>"Guadeloupe (Guadeloupe)", "Guam"=>"Guam (Guam)", "Guatemala"=>"Guatemala (Guatemala)", "Guernsey and Alderney"=>"Guernsey and Alderney (Guernsey)", "Guinea"=>"Guinea (Guinée)", "Guinea-Bissau"=>"Guinea-Bissau (Guiné-Bissau)", "Guyana"=>"Guyana (Guyana)", "Haiti"=>"Haiti (Haïti)", "Heard Island and McDonald Islands"=>"Heard Island and McDonald Islands (Heard Island and McDonald Islands)", "Honduras"=>"Honduras (Honduras)", "Hong Kong S.A.R."=>"Hong Kong S.A.R. (香港)", "Hungary"=>"Hungary (Magyarország)", "Iceland"=>"Iceland (Ísland)", "India"=>"India (भारत)", "Indonesia"=>"Indonesia (Indonesia)", "Iran"=>"Iran (ایران)", "Iraq"=>"Iraq (العراق)", "Ireland"=>"Ireland (Éire)", "Israel"=>"Israel (יִשְׂרָאֵל)", "Italy"=>"Italy (Italia)", "Jamaica"=>"Jamaica (Jamaica)", "Japan"=>"Japan (日本)", "Jersey"=>"Jersey (Jersey)", "Jordan"=>"Jordan (الأردن)", "Kazakhstan"=>"Kazakhstan (Қазақстан)", "Kenya"=>"Kenya (Kenya)", "Kiribati"=>"Kiribati (Kiribati)", "Korea North"=>"Korea North (북한)", "Korea South"=>"Korea South (대한민국)", "Kosovo"=>"Kosovo (Republika e Kosovës)", "Kuwait"=>"Kuwait (الكويت)", "Kyrgyzstan"=>"Kyrgyzstan (Кыргызстан)", "Laos"=>"Laos (ສປປລາວ)", "Latvia"=>"Latvia (Latvija)", "Lebanon"=>"Lebanon (لبنان)", "Lesotho"=>"Lesotho (Lesotho)", "Liberia"=>"Liberia (Liberia)", "Libya"=>"Libya (‏ليبيا)", "Liechtenstein"=>"Liechtenstein (Liechtenstein)", "Lithuania"=>"Lithuania (Lietuva)", "Luxembourg"=>"Luxembourg (Luxembourg)", "Macau S.A.R."=>"Macau S.A.R. (澳門)", "Macedonia"=>"Macedonia (Северна Македонија)", "Madagascar"=>"Madagascar (Madagasikara)", "Malawi"=>"Malawi (Malawi)", "Malaysia"=>"Malaysia (Malaysia)", "Maldives"=>"Maldives (Maldives)", "Mali"=>"Mali (Mali)", "Malta"=>"Malta (Malta)", "Man (Isle of)"=>"Man (Isle of) (Isle of Man)", "Marshall Islands"=>"Marshall Islands (M̧ajeļ)", "Martinique"=>"Martinique (Martinique)", "Mauritania"=>"Mauritania (موريتانيا)", "Mauritius"=>"Mauritius (Maurice)", "Mayotte"=>"Mayotte (Mayotte)", "Mexico"=>"Mexico (México)", "Micronesia"=>"Micronesia (Micronesia)", "Moldova"=>"Moldova (Moldova)", "Monaco"=>"Monaco (Monaco)", "Mongolia"=>"Mongolia (Монгол улс)", "Montenegro"=>"Montenegro (Црна Гора)", "Montserrat"=>"Montserrat (Montserrat)", "Morocco"=>"Morocco (المغرب)", "Mozambique"=>"Mozambique (Moçambique)", "Myanmar"=>"Myanmar (မြန်မာ)", "Namibia"=>"Namibia (Namibia)", "Nauru"=>"Nauru (Nauru)", "Nepal"=>"Nepal (नपल)", "Netherlands The"=>"Netherlands The (Nederland)", "New Caledonia"=>"New Caledonia (Nouvelle-Calédonie)", "New Zealand"=>"New Zealand (New Zealand)", "Nicaragua"=>"Nicaragua (Nicaragua)", "Niger"=>"Niger (Niger)", "Nigeria"=>"Nigeria (Nigeria)", "Niue"=>"Niue (Niuē)", "Norfolk Island"=>"Norfolk Island (Norfolk Island)", "Northern Mariana Islands"=>"Northern Mariana Islands (Northern Mariana Islands)", "Norway"=>"Norway (Norge)", "Oman"=>"Oman (عمان)", "Pakistan"=>"Pakistan (پاکستان)", "Palau"=>"Palau (Palau)", "Palestinian Territory Occupied"=>"Palestinian Territory Occupied (فلسطين)", "Panama"=>"Panama (Panamá)", "Papua new Guinea"=>"Papua new Guinea (Papua Niugini)", "Paraguay"=>"Paraguay (Paraguay)", "Peru"=>"Peru (Perú)", "Philippines"=>"Philippines (Pilipinas)", "Pitcairn Island"=>"Pitcairn Island (Pitcairn Islands)", "Poland"=>"Poland (Polska)", "Portugal"=>"Portugal (Portugal)", "Puerto Rico"=>"Puerto Rico (Puerto Rico)", "Qatar"=>"Qatar (قطر)", "Reunion"=>"Reunion (La Réunion)", "Romania"=>"Romania (România)", "Russia"=>"Russia (Россия)", "Rwanda"=>"Rwanda (Rwanda)", "Saint Helena"=>"Saint Helena (Saint Helena)", "Saint Kitts And Nevis"=>"Saint Kitts And Nevis (Saint Kitts and Nevis)", "Saint Lucia"=>"Saint Lucia (Saint Lucia)", "Saint Pierre and Miquelon"=>"Saint Pierre and Miquelon (Saint-Pierre-et-Miquelon)", "Saint Vincent And The Grenadines"=>"Saint Vincent And The Grenadines (Saint Vincent and the Grenadines)", "Saint-Barthelemy"=>"Saint-Barthelemy (Saint-Barthélemy)", "Saint-Martin (French part)"=>"Saint-Martin (French part) (Saint-Martin)", "Samoa"=>"Samoa (Samoa)", "San Marino"=>"San Marino (San Marino)", "Sao Tome and Principe"=>"Sao Tome and Principe (São Tomé e Príncipe)", "Saudi Arabia"=>"Saudi Arabia (العربية السعودية)", "Senegal"=>"Senegal (Sénégal)", "Serbia"=>"Serbia (Србија)", "Seychelles"=>"Seychelles (Seychelles)", "Sierra Leone"=>"Sierra Leone (Sierra Leone)", "Singapore"=>"Singapore (Singapore)", "Sint Maarten (Dutch part)"=>"Sint Maarten (Dutch part) (Sint Maarten)", "Slovakia"=>"Slovakia (Slovensko)", "Slovenia"=>"Slovenia (Slovenija)", "Solomon Islands"=>"Solomon Islands (Solomon Islands)", "Somalia"=>"Somalia (Soomaaliya)", "South Africa"=>"South Africa (South Africa)", "South Georgia"=>"South Georgia (South Georgia)", "South Sudan"=>"South Sudan (South Sudan)", "Spain"=>"Spain (España)", "Sri Lanka"=>"Sri Lanka (śrī laṃkāva)", "Sudan"=>"Sudan (السودان)", "Suriname"=>"Suriname (Suriname)", "Svalbard And Jan Mayen Islands"=>"Svalbard And Jan Mayen Islands (Svalbard og Jan Mayen)", "Swaziland"=>"Swaziland (Swaziland)", "Sweden"=>"Sweden (Sverige)", "Switzerland"=>"Switzerland (Schweiz)", "Syria"=>"Syria (سوريا)", "Taiwan"=>"Taiwan (臺灣)", "Tajikistan"=>"Tajikistan (Тоҷикистон)", "Tanzania"=>"Tanzania (Tanzania)", "Thailand"=>"Thailand (ประเทศไทย)", "Togo"=>"Togo (Togo)", "Tokelau"=>"Tokelau (Tokelau)", "Tonga"=>"Tonga (Tonga)", "Trinidad And Tobago"=>"Trinidad And Tobago (Trinidad and Tobago)", "Tunisia"=>"Tunisia (تونس)", "Turkey"=>"Turkey (Türkiye)", "Turkmenistan"=>"Turkmenistan (Türkmenistan)", "Turks And Caicos Islands"=>"Turks And Caicos Islands (Turks and Caicos Islands)", "Tuvalu"=>"Tuvalu (Tuvalu)", "Uganda"=>"Uganda (Uganda)", "Ukraine"=>"Ukraine (Україна)", "United Arab Emirates"=>"United Arab Emirates (دولة الإمارات العربية المتحدة)", "United Kingdom"=>"United Kingdom (United Kingdom)", "United States"=>"United States (United States)", "United States Minor Outlying Islands"=>"United States Minor Outlying Islands (United States Minor Outlying Islands)", "Uruguay"=>"Uruguay (Uruguay)", "Uzbekistan"=>"Uzbekistan (O‘zbekiston)", "Vanuatu"=>"Vanuatu (Vanuatu)", "Vatican City State (Holy See)"=>"Vatican City State (Holy See) (Vaticano)", "Venezuela"=>"Venezuela (Venezuela)",
                      "Vietnam"=>"Vietnam (Việt Nam)", "Virgin Islands (British)"=>"Virgin Islands (British) (British Virgin Islands)", "Virgin Islands (US)"=>"Virgin Islands (US) (United States Virgin Islands)", "Wallis And Futuna Islands"=>"Wallis And Futuna Islands (Wallis et Futuna)", "Western Sahara"=>"Western Sahara (الصحراء الغربية)", "Yemen"=>"Yemen (اليَمَن)", "Zambia"=>"Zambia (Zambia)", "Zimbabwe"=>"Zimbabwe (Zimbabwe)");
                      $select_countries="<select onchange='get_cities(this)' name='selected_country' id='selected_country' class='form-control multi_class mt'>
                      <option value='' disabled selected>Select a country</option>";
                      foreach($country_array as $key=>$val){
                        $select_countries.="<option value='".$key."'>".$val."</option>";
                      }
                      $select_countries.="<select>";
                      echo $select_countries;?>
                    </div>
                    <div class="form-group col-lg-3 col-md-4 col-sm-6 append_cities hidden"></div>
                    <div class="form-group col-lg-3 col-md-4 col-sm-6 div_other_city_field hidden">
                      <input name="city" type="text" class="form-control mt other_city_field hidden" placeholder="Enter your City Name"/>
                    </div>
                    <div class="form-group col-lg-3 col-md-4 col-sm-6">
                      <input name="post_code" type="text" class="form-control mt" placeholder="Enter your Post Code" required/>
                    </div>
                    <div class="form-group col-lg-3 col-md-4 col-sm-6">
                      <input name="building_name" type="text" class="form-control mt" placeholder="Enter Building Name" required/>
                    </div>
                    <div class="form-group col-lg-3 col-md-4 col-sm-6">
                      <input type="text" name="line1" class="form-control mt" placeholder="Line 1"/>
                    </div>
                    <div class="form-group col-lg-3 col-md-4 col-sm-6">
                      <input type="text" name="line2" class="form-control mt" placeholder="Line 2"/>
                    </div>
                    <div class="form-group col-lg-3 col-md-4 col-sm-6">
                      <input type="text" name="line3" class="form-control mt" placeholder="Line 3"/>
                    </div>
                </div>
                <div class="form-group col-md-12">
                    <button class="btn btn-primary nextBtn pull-right" type="button">Next <i class="fa fa-angle-right"></i><i class="fa fa-angle-right"></i></button>
                </div>
                </div>
              </div>
              <div class="panel panel-info setup-content" id="step-3">
                <div class="panel-heading">
                    <h3 class="panel-title">Work Availability</h3>
                </div>
                <div class="panel-body">
                <div class="row">
                    <div style="overflow-x: auto;width: 100%;">
                      <table align="center" class="table table-bordered">
                        <tr class="bg-primary">
                          <th><strong>Day</strong></th>
                          <th><strong>Action</strong></th>
                          <th><strong>From</strong></th>
                          <th><strong>To</strong></th>
                        </tr>
                        <tr class="bg-info">
                          <td>
                            <strong>Active Dates*</strong>
                          </td>
                          <td>
                            <select class="form-control" name="actnow" id="actnow" style="width:165px;">
                            <option>Always</option>
                            <option>Never</option>
                            <option>Inactive</option>
                            <option>Active</option>
                            </select>
                          </td>
                          <td>
                            <input class="form-control" type="date" name="actnow_time" id="actnow_time" style="width:165px;"/>
                          </td>
                          <td>
                            <input class="form-control" type="date" name="actnow_to" id="actnow_to" style="width:165px;"/>    
                          </td>
                        </tr>
                        <tr>
                          <td><strong>Monday *</strong></td>
                          <td>                    
                            <select class="form-control" name="monday" id="monday" style="width:165px;">
                            <option>Yes</option><option>No</option>
                            </select>
                          </td>
                          <td>
                            <input class="form-control" type="time" name="monday_time" id="monday_time" style="width:165px;"/>
                          </td>
                          <td>
                            <input class="form-control" type="time" name="monday_to" id="monday_to" style="width:165px;"/>    
                          </td>
                        </tr>
                        <tr>
                          <td><strong>Tuesday *</strong></td>
                          <td>
                            <select class="form-control" name="tuesday" id="tuesday" style="width:165px;">
                            <option>Yes</option><option>No</option>
                            </select>
                          </td>
                          <td>
                            <input class="form-control" type="time" name="tuesday_time" id="tuesday_time" style="width:165px;"/>
                          </td>
                          <td><input class="form-control" type="time" name="tuesday_to" id="tuesday_to" style="width:165px;"/>     
                          </td>
                        </tr>
                        <tr>
                          <td><strong>Wednesday *</strong></td>
                          <td><select class="form-control" name="wednesday" id="wednesday" style="width:165px;">
                              <option>Yes</option>
                              <option>No</option>
                            </select>
                          </td>
                          <td>
                            <input class="form-control" type="time" name="wednesday_time" id="wednesday_time" style="width:165px;"/>
                          </td>
                          <td><input class="form-control" type="time" name="wednesday_to" id="wednesday_to" style="width:165px;"/>
                          </td>
                        </tr>
                        <tr>
                          <td><strong>Thursday *</strong></td>
                          <td><select class="form-control" name="thursday" id="thursday" style="width:165px;">
                            <option>Yes</option>
                            <option>No</option>
                            </select>
                          </td>
                          <td>
                            <input class="form-control" type="time" name="thursday_time" id="thursday_time" style="width:165px;"/>
                          </td>
                          <td><input class="form-control" type="time" name="thursday_to" id="thursday_to" style="width:165px;"/>
                          </td>
                        </tr>
                        <tr>
                          <td><strong>Friday * </strong></td>
                          <td><select class="form-control" name="friday" id="friday" style="width:165px;">
                            <option>Yes</option>
                            <option>No</option>
                            </select>
                          </td>
                          <td>
                            <input class="form-control" type="time" name="friday_time" id="friday_time" style="width:165px;"/>
                          </td>
                          <td><input class="form-control" type="time" name="friday_to" id="friday_to" style="width:165px;"/>
                          </td>
                        </tr>
                        <tr>
                          <td><strong>Weekend *</strong></td>
                          <td><select class="form-control" name="saturday" id="saturday" style="width:165px;">
                            <option>Yes</option>
                            <option>No</option>
                            </select>
                          </td>
                          <td>
                            <input class="form-control" type="time" name="saturday_time" id="saturday_time" style="width:165px;"/>
                          </td>
                          <td><input class="form-control" type="time" name="saturday_to" id="saturday_to" style="width:165px;"/>
                          </td>
                        </tr>
                        <tr>
                          <td><strong>Out Of Hours *</strong></td>
                          <td><select class="form-control" name="sunday" id="sunday" style="width:165px;">
                            <option>Yes</option>
                            <option>No</option>
                            </select>
                          </td>
                          <td>
                            <input class="form-control" type="time" name="sunday_time" id="sunday_time" style="width:165px;"/>
                          </td>
                          <td><input class="form-control" type="time" name="sunday_to" id="sunday_to" style="width:165px;"/>
                          </td>
                        </tr>
                        <tr>
                            <td><strong>Remarks</strong>:</td>
                            <td colspan="3"><textarea class="form-control" name="week_remarks" rows="3"></textarea></td>
                        </tr>
                      </table>
                    </div>
                </div>
                <div class="form-group col-md-12">
                    <button class="btn btn-primary nextBtn pull-right" type="button">Next <i class="fa fa-angle-right"></i><i class="fa fa-angle-right"></i></button>
                </div>
                </div>
              </div>
              <div class="panel panel-info setup-content" id="step-4">
                <div class="panel-heading">
                    <h3 class="panel-title">Speaking Languages</h3>
                </div>
                <div class="panel-body">
                  <div class="row">
                      <div class="form-group col-md-4 col-sm-6">     
                        <select onchange="set_language()" class="form-control" name="language" id="language" required>
                        <option selected>Select a Language</option>
                          <?php $lang_query=$acttObj->read_all("lang","lang","1 ORDER BY lang");$lang_counter=1;
                          while($row_lang=$lang_query->fetch_assoc()){ ?>
                          <option value="<?php echo $lang_counter; ?>"><?php echo $row_lang['lang']; ?></option>
                          <?php $lang_counter++; } ?>
                        </select>
                        <input type="hidden" id="array_languages" name="array_languages"/>
                      </div>
                      <div class="form-group col-md-12 col-sm-6" id="append_language">
                        <table align="center" class="table table-bordered hidden">
                          <tr class="bg-primary add_tr">
                            <th>Language</th>
                            <th>Speaking Level</th>
                            <th>Action</th>
                          </tr>
                        </table>
                      </div>
                      <hr>
                      <div class="form-group col-lg-3 col-md-6 col-sm-6">
                          <label class="optional">Have right to work evidence?</label><br>
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
                        <label>Upload (<small>Right to work evidence</small>)</label>
                        <input name="work_evid_file" type="file" class="form-control work_evid_fields" onchange="max_upload(this);">
                        </div>
                      <div class="form-group col-lg-3 col-md-6 col-sm-6 div_work_evid_file hidden">
                        <label>Select Issue Dates</label>
                        <input placeholder="Select Issue Date" type="text" onfocus="(this.type='date')" onblur="(this.type='text')" name="work_evid_issue_date" class="form-control work_evid_fields"/>
                        </div>
                      <div class="form-group col-lg-3 col-md-6 col-sm-6 div_work_evid_file hidden">
                        <label>Select Expiry Dates</label>
                        <input placeholder="Select Expiry Date" type="text" onfocus="(this.type='date')" onblur="(this.type='text')" name="work_evid_expiry_date" class="form-control work_evid_fields mt"/>
                      </div>
                      <hr>
                      <div class="form-group col-lg-4 col-md-4 col-sm-6">
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
                      <div class="form-group col-sm-6 div_permit hidden">
                          <label class="optional">Do you have a permit to work in the UK?</label><br>
                          <div class="radio-inline ri">
                            <label><input type="radio" name="permit" value="Yes" onclick="permit_upload(this);">
                            <span class="label label-success">Yes <i class="fa fa-check-circle"></i></span></label>
                          </div>
                          <div class="radio-inline ri">
                            <label><input type="radio" name="permit" value="No" onclick="permit_upload(this);">
                            <span class="label label-danger">No <i class="fa fa-remove"></i></span></label>
                          </div>
                      </div>
                      <div class="form-group col-lg-4 col-md-6 col-sm-6 div_permit_file hidden">
                        <label>Upload File (<small>Work Permit / Status Document</small>)</label>
                        <input name="permit_file" type="file" class="form-control permit_fields" onchange="max_upload(this);">
                        <input placeholder="Enter Permit Number" type="text" name="permit_number" class="form-control permit_fields mt">
                      </div>
                      <div class="form-group col-lg-4 col-md-6 col-sm-6 div_permit_file hidden">
                        <label>Select Dates</label>
                        <input placeholder="Enter Issue Date" type="text" onfocus="(this.type='date')" onblur="(this.type='text')" name="permit_issue_date" class="form-control permit_fields"/>
                        <input placeholder="Enter Expiry Date" type="text" onfocus="(this.type='date')" onblur="(this.type='text')" name="permit_expiry_date" class="form-control permit_fields mt"/>
                      </div>
                      <div class="form-group col-md-4 col-sm-6 div_passport_file hidden">
                        <label>Upload File (<small>British Passport</small>)</label>
                        <input name="passport_file" type="file" class="form-control uk_citizen_fields" onchange="max_upload(this);">
                        <input placeholder="Enter Passport Number" type="text" name="passport_number" class="form-control uk_citizen_fields mt">
                      </div>
                      <div class="form-group col-md-4 col-sm-6 div_passport_file hidden">
                        <label>Select Dates</label>
                        <input placeholder="Enter Issue Date" type="text" onfocus="(this.type='date')" onblur="(this.type='text')" name="passport_issue_date" class="form-control uk_citizen_fields"/>
                        <input placeholder="Enter Expiry Date" type="text" onfocus="(this.type='date')" onblur="(this.type='text')" name="passport_expiry_date" class="form-control uk_citizen_fields mt"/>
                      </div>
                  </div>
                  <div class="form-group col-md-12">
                    <button class="btn btn-primary nextBtn pull-right" type="button">Next <i class="fa fa-angle-right"></i><i class="fa fa-angle-right"></i></button>
                </div>
                </div>
              </div>
              <div class="panel panel-info setup-content" id="step-5">
                  <div class="panel-heading">
                      <h3 class="panel-title">Speaking Languages</h3>
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
                    <div class="form-group col-md-6 col-sm-6">
                      <label class="optional">Are You DBS Checked?</label><br>
                      <div class="radio-inline ri">
                        <label><input type="radio" name="is_dbs" value="Yes" onclick="dbs(this);">
                        <span class="label label-success">Yes <i class="fa fa-check-circle"></i></span></label>
                      </div>
                      <div class="radio-inline ri">
                        <label><input type="radio" name="is_dbs" value="No" onclick="dbs(this);">
                        <span class="label label-danger">No <i class="fa fa-remove"></i></span></label>
                      </div>
                    </div>
                    <div class="form-group col-lg-3 col-sm-6 div_dbs_file hidden">
                      <label>Upload File (<small>DBS Document</small>)</label>
                      <input name="dbs_file" type="file" class="form-control" onchange="max_upload(this);"> 
                      <input placeholder="Enter DBS Number" type="text" name="dbs_no" class="form-control dbs_fields mt">
                    </div>
                    <div class="form-group col-lg-3 col-sm-6 div_dbs_file hidden">
                      <label>Select Dates</label>
                      <input placeholder="Enter Issue Date" type="text" onfocus="(this.type='date')" onblur="(this.type='text')" name="dbs_issue_date" class="form-control dbs_fields"/>
                      <input placeholder="Enter Expiry Date" type="text" onfocus="(this.type='date')" onblur="(this.type='text')" name="dbs_expiry_date" class="form-control dbs_fields mt"/>
                    </div>
                  </div>
                <div class="row">
                  <div class="form-group col-md-6 col-sm-6">
                    <label class="optional">Master in Translation?</label><br>
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

                  <div class="form-group col-md-6 col-sm-6">
                    <label class="optional">Are You DPSI Qualified?</label><br>
                    <div class="radio-inline ri">
                      <label><input type="radio" name="is_dpsi" value="Yes" onclick="dpsi(this);">
                      <span class="label label-success">Yes <i class="fa fa-check-circle"></i></span></label>
                    </div>
                    <div class="radio-inline ri">
                      <label><input type="radio" name="is_dpsi" value="No" onclick="dpsi(this);" checked>
                      <span class="label label-danger">No <i class="fa fa-remove"></i></span></label>
                  </div>
                  <div class="row">
                    <div class="col-lg-8 col-md-12 div_dpsi hidden"><br>
                      <label>Upload File (<small>DPSI</small>)</label>
                      <input name="dpsi_file" type="file" class="form-control" onchange="max_upload(this);">
                    </div>
                  </div>
                </div>
                </div>
                <hr>
                <div class="row">
                <div class="form-group col-md-6 col-sm-6">
                      <label class="optional">Do you hold a recognised Interpreting / Translation Qualification?</label><br>
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
                    <label class="optional">Do you have professional experience in Interpreting / Translation?</label><br>
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
                    <select class="multi_class form-control" id="skills" name="skills[]"  multiple="multiple">
                        <?php $skills_q=$acttObj->read_all('DISTINCT skill','skill',"id IN (1,2,3,4,5,6,7) ORDER BY skill ASC");
                        while($row_skills=mysqli_fetch_assoc($skills_q)){ ?>
                        <option value="<?php echo $row_skills['skill']; ?>"><?php echo $row_skills['skill']; ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="form-group col-md-6 col-sm-6">
                    <label class="optional">Do you hold NRCPD qualification?</label><br>
                    <div class="radio-inline ri">
                      <label><input type="radio" name="is_nrcpd" value="Yes" onclick="nrcpd(this);">
                      <span class="label label-success">Yes <i class="fa fa-check-circle"></i></span></label>
                    </div>
                    <div class="radio-inline ri">
                      <label><input type="radio" name="is_nrcpd" value="No" onclick="nrcpd(this);">
                      <span class="label label-danger">No <i class="fa fa-remove"></i></span></label>
                  </div>
              </div>
              <div class="col-md-6 div_nrcpd hidden">
                  <label>Upload File (<small>NRCPD qualification</small>)</label>
                  <input name="nrcpd_file" type="file" class="form-control" onchange="max_upload(this);">
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
              <div class="bg-info col-xs-12 form-group"><h4>Bank Details for BACS payments</h4></div>
              <div class="">
                <div class="form-group col-md-4 col-sm-6">
                    <label>Full Name <small>(As it appears on your Bank Account)</small></label>
                    <input type="text" class="form-control" name="account_name" placeholder="Enter Account Full Name" required>
                </div>
                <div class="form-group col-md-4 col-sm-6">
                    <label>Bank Name</label>
                    <input type="text" class="form-control" name="bank_name" placeholder="Enter Bank Name" required>
                </div>
                <div class="form-group col-md-4 col-sm-6">
                    <label>Branch</label>
                    <input type="text" class="form-control" name="branch" placeholder="Enter Branch Name" required>
                </div>
                <div class="form-group col-md-4 col-sm-6">
                    <label>Account Number</label>
                    <input type="text" class="form-control" name="account_number" placeholder="Enter Account Number" required>
                </div>
                <div class="form-group col-md-4 col-sm-6">
                    <label>Sort Code</label>
                    <input type="text" class="form-control" name="sort_code" placeholder="Enter Sort Code" required>
                </div>
              </div>
              <div class="bg-info col-xs-12 form-group"><h4>EDUCATION DETAILS</h4></div>
              <div class="">
                <div class="form-group col-md-4 col-sm-6">
                    <label>Higher Level of Education</label>
                    <input type="text" class="form-control" name="institute" placeholder="Enter Institute Details" required>
                </div>
                <div class="form-group col-md-4 col-sm-6">
                    <label>Qualification</label>
                    <input type="text" class="form-control" name="qualification" placeholder="Bachelors in CS, ACCA, MBA etc" required>
                </div>
                <div class="form-group col-md-2 col-sm-6">
                    <label>From Date</label>
                    <input type="date" class="form-control" name="from_date" required>
                </div>
                <div class="form-group col-md-2 col-sm-6">
                    <label>To Date</label>
                    <input type="date" class="form-control" name="to_date" required>
                </div>
              </div>

              <div class="bg-info col-xs-12 form-group"><h4>REFERENCES</h4> (<i>Please list professional references:</i>)</div>
              <div class="">
                <div class="form-group col-md-12 col-sm-12">
                  <u><b>Reference 1 :</b></u>
                </div>
                <div class="form-group col-md-4 col-sm-6">
                    <label>Full Name</label>
                    <input type="text" class="form-control" name="ref_name1" placeholder="Full Name" required>
                </div>
                <div class="form-group col-md-4 col-sm-6">
                    <label>Relationship</label>
                    <input type="text" class="form-control" name="ref_relationship1" placeholder="Relationship" required>
                </div>
                <div class="form-group col-md-4 col-sm-6">
                    <label>Company</label>
                    <input type="text" class="form-control" name="ref_company1" placeholder="Company" required>
                </div>
                <div class="form-group col-md-4 col-sm-6">
                    <label>Phone</label>
                    <input type="text" class="form-control" name="ref_phone1" placeholder="Phone" required>
                </div>
                <div class="form-group col-md-4 col-sm-6">
                    <label>Email</label>
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
                Do you have the referee's permission for us to contact regarding your employment?</label>
                </div>
              </div>
              <div class="bg-info col-xs-12 form-group"><h4>DISCLAIMER AND SIGNATURE</h4></div>
              <div class="">
                <div class="form-group col-md-12">
                <label><input type="checkbox" name="disclaimer" id="disclaimer" style="margin-bottom: 4px;" required data-backdrop="static" data-keyboard="false"  data-target="#modal_terms" data-toggle="modal">
                I accept <a href="javascript:void(0)" data-backdrop="static" data-keyboard="false"  data-target="#modal_terms" data-toggle="modal" title="Click to read Terms"><b>Terms & Conditions</b></a> and certify that my answers are true and complete to the best of my knowledge. If this application leads to employment, I understand that false or misleading information in my application or interview may result in my release. I will update my details with LSUK if it changes</label>
                </div>
                <div class="form-group col-md-4 col-sm-6">
                    <label>Signature <small>Write your name</small></label>
                    <input type="text" class="form-control" name="signature_name" placeholder="Write your name" required>
                </div>
                <div class="form-group col-md-4 col-sm-6">
                    <label>Date</label>
                    <input type="date" class="form-control" name="signature_date" required>
                </div>
                <div class="form-group col-md-6 col-sm-6">
                  <script src='https://www.google.com/recaptcha/api.js'></script>
                  <div class="g-recaptcha" data-sitekey="6LextRoUAAAAAGSGzslurL5xeNDw3lDDVkxM9rZe"></div>
                  <br><button class="btn btn-primary" class="button" type="submit" name="submit">Submit &raquo;</button>
                </div>
              </div>
                </div>
              </div>
              <!-- Terms & conditions Modal Starts -->
              <div class="modal fade" id="modal_terms" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
              aria-hidden="true">
                <div class="modal-dialog modal-lg" role="document">
                  <div class="modal-content">
                    <div class="modal-body">
                      <div style="border: 1px solid grey;padding: 10px;">
                        <?php echo $acttObj->read_specific("em_format","email_format","id=41")["em_format"]; ?> 
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
         <!-- begin clients -->
         <?php include'source/our_client.php'; ?>
      <!-- begin footer -->
      <?php include'source/footer.php'; ?>
      <!-- end footer -->  
      </div>
      <!-- end container -->
   </body>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.13/css/bootstrap-multiselect.css"rel="stylesheet" type="text/css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.13/js/bootstrap-multiselect.js"type="text/javascript"></script>
<script>
function check_fields(){
    var fn=$("#first_name").val();
    var ln=$("#last_name").val();
    var dob=$("#dob").val();
    if(!fn){
        $("#first_name").focus();
    }else if(!ln){
        $("#last_name").focus();
    }else if(!dob){
        $("#dob").focus();
    }else{}
}
function check_existing($elem){
var fn=$("#first_name").val();
var ln=$("#last_name").val();
var nm=fn+" "+ln;
var dob=$("#dob").val();
var em=$($elem).val();
if(nm && dob && em){
    $.ajax({
        url:'ajax_client_portal.php',
        method:'post',
        dataType:'json',
        data:{'em':em,'nm':nm,'dob':dob,'action':'check_em'},
        success:function(data){
            if(data['status']=="exist" && data['is_temp']=="1"){
                alert(data['msg']);
                window.location.href="interp_reg.php";
            }else if(data['status']=="exist" && data['is_temp']=="0"){
                alert(data['msg']);
                $("#email").val("");$("#email").focus();
            }else if(data['status']=="same_exist"){
                alert(data['msg']);
                $("#first_name").val("");$("#last_name").val("");$("#dob").val("");$("#email").val("");$("#first_name").focus();
            }
    }, error: function(xhr){
        alert("An error occured: " + xhr.status + " " + xhr.statusText);
    }
    });
  }
}

   $(function() {
    	$('.multi_class').multiselect({buttonWidth: '100px',includeSelectAllOption: true,numberDisplayed: 1,enableFiltering: true,enableCaseInsensitiveFiltering: true});
    });
    function max_upload($element){
        if($element.files[0].size > 1572864){
          alert("File is too big ! Upload upto 1.5 MB file");
          $element.value = "";
        }else{
          return "1";
        }
    }
    var _URL = window.URL || window.webkitURL;
    $("#profile_photo").change(function(e) {
        if(max_upload(this)=='1'){
            var file, img;
            var output = document.getElementById('output');
            if ((file = this.files[0])) {
                img = new Image();
                img.onload = function() {
                    if(this.width!=this.height){
                        alert("You must upload a square passport size photo like 200x200 or 400x400");
						$("#profile_photo").val('');
                    }else{
                        output.src = _URL.createObjectURL(file);
                    }
                };
                img.onerror = function() {
                    alert("Uploaded file not a valid photo !");
					$("#profile_photo").val('');
                };
                img.src = _URL.createObjectURL(file);
            }
        }
    });

    function pop_language(arr, value) {
      var index = arr.indexOf(value);
      if (index > -1) {
        arr.splice(index, 1);
      }
      return arr;
    }
   var selected_languages = [];var old_index;
   function set_language(){
     var element=$("#language");
     var text=$('#language option:selected').text();
     var value=$('#language option:selected').val();
     $("#append_language table").removeClass("hidden");
     $("#append_language table tr:last").after("<tr id='tr_"+value+"'><td class='"+value+"'>"+text+"</td><td><select onclick='old_level(this)' onchange='update_level(this)' class='form-control' name='selected_language_"+value+"' id='selected_language_"+value+"' style='width:165px;'><option value='1'>Native</option><option value='2'>Fluent</option><option value='3'>Intermediate</option><option value='4'>Basic</option></select></td><td><button type='button' class='btn btn-danger btn-sm' onclick='remove_language(this)'>Remove</button></td></tr>");
     $('#language option:selected').remove();
     selected_languages.push(text+":Native");
     $('#array_languages').val(selected_languages);
   }
   function remove_language(elem){
     $(elem).closest('tr').remove();
     var old_text=$(elem).closest('tr').find("td:first").text();
     var old_value=$(elem).closest('tr').find("td:first").attr("class");
     $("#language option").eq($(elem).closest('tr').find("td:first").attr("class")).before($("<option></option>").val(old_value).text(old_text)); 
     pop_language(selected_languages,old_text+":"+$(elem).closest('tr').find("td:nth-child(2) select option:selected").text());
     $('#array_languages').val(selected_languages);
  }
  function old_level(elem){
    old_index=$(elem).closest('tr').find("td:first").text()+":"+$(elem).find("option:selected").text();
  }
  function update_level(elem){
    selected_languages[selected_languages.indexOf(old_index)]=$(elem).closest('tr').find("td:first").text()+":"+$(elem).find("option:selected").text();
    $('#array_languages').val(selected_languages);
    $('#array_languages').focusout();
  }
   function changer(elem){
			var value=$(elem).val();
			if (value=='No'){
          $('.div_permit').removeClass('hidden');
          $('.div_passport_file').addClass('hidden');
          $('.uk_citizen_fields').removeAttr("required");
      } else {
          $('.permit_fields').removeAttr("required");
          $('.uk_citizen_fields').attr('required',"required");
          $('.div_permit').addClass('hidden');
          $("input[name='permit']").prop('checked', false);
          $('.div_permit_file').addClass('hidden');
          $('.div_passport_file').removeClass('hidden');
      }
	}
   function work_evid_change(elem){
        var value=$(elem).val();
        if (value=='No'){
          $('.div_work_evid_file').addClass('hidden');
          $('.work_evid_fields').removeAttr("required");
      } else {
          $('.work_evid_fields').attr('required',"required");
          $('.div_work_evid_file').removeClass('hidden');
      }
		}
   function permit_upload(elem){
			var value=$(elem).val();
			if (value=='No'){
          $('.div_permit_file').addClass('hidden');
          $('.permit_fields').removeAttr("required");
      } else {
          $('.permit_fields').attr('required',"required");
          $('.div_permit_file').removeClass('hidden');
      }
		}
   function dbs(elem){
			var value=$(elem).val();
			if (value=='No'){
          $('.div_dbs_file').addClass('hidden');
          $('.dbs_fields').removeAttr("required");
      } else {
          $('.dbs_fields').attr('required',"required");
          $('.div_dbs_file').removeClass('hidden');
      }
		}
    function translation_qualification(elem){
			var value=$(elem).val();
			if (value=='Yes'){
          $('.div_translation_qualification').removeClass('hidden');
          $('input[name="int_qualification_file"]').attr('required',"required");
      } else {
          $('.div_translation_qualification').addClass('hidden');
          $('input[name="int_qualification_file"]').removeAttr("required");
      }
	}
    function experience(elem){
			var value=$(elem).val();
			if (value=='Yes'){
          $('.div_experience').removeClass('hidden');
      } else {
          $('.div_experience').addClass('hidden');
      }
	}
  function nrcpd(elem){
	var value=$(elem).val();
		if (value=='Yes'){
          $('.div_nrcpd').removeClass('hidden');
          $('input[name="nrcpd_file"]').attr('required',"required");
      } else {
          $('.div_nrcpd').addClass('hidden');
          $('input[name="nrcpd_file"]').removeAttr("required");
      }
	}
    function drive(elem){
			var value=$(elem).val();
			if (value=='Yes'){
          $('.div_driving_license').removeClass('hidden');
          $('input[name="driving_license_file"]').attr('required',"required");
      } else {
          $('.div_driving_license').addClass('hidden');
          $('input[name="driving_license_file"]').removeAttr("required");
      }
	}
  function master(elem){
	var value=$(elem).val();
		if (value=='Yes'){
          $('.div_master').removeClass('hidden');
          $('input[name="master_file"]').attr('required',"required");
      } else {
          $('.div_master').addClass('hidden');
          $('input[name="master_file"]').removeAttr("required");
      }
	}
  function dpsi(elem){
	var value=$(elem).val();
		if (value=='Yes'){
          $('.div_dpsi').removeClass('hidden');
          $('input[name="dpsi_file"]').attr('required',"required");
      } else {
          $('.div_dpsi').addClass('hidden');
          $('input[name="dpsi_file"]').removeAttr("required");
      }
	}
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
            url:'ajax_client_portal.php',
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
    //Stepper
    $('.nextBtn:eq(0)').click(function() {
    var check_languages=$('#array_languages').val();
    var source_language=$('#source').val();
    var target_language=$('#target').val();
    if(!check_languages && source_language && target_language){
        set_language();
    }
});
$(document).ready(function () {
    var navListItems = $('div.setup-panel div a'),
    allWells = $('.setup-content'),
    allNextBtn = $('.nextBtn');
    allWells.hide();
    navListItems.click(function (e) {
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

    allNextBtn.click(function () {
        var curStep = $(this).closest(".setup-content"),
            curStepBtn = curStep.attr("id"),
            nextStepWizard = $('div.setup-panel div a[href="#' + curStepBtn + '"]').parent().next().children("a"),
            curInputs = curStep.find(".form-control"),
            isValid = true;

        $(".form-group").removeClass("has-error");
        for (var i = 0; i < curInputs.length; i++) {
            if (!curInputs[i].validity.valid) {
                isValid = false;
                $(curInputs[i]).closest(".form-group").addClass("has-error");
            }
        }

        if (isValid) nextStepWizard.removeAttr('disabled').removeClass('disabled').trigger('click');
    });

    $('div.setup-panel div a.btn-primary').trigger('click');
});
   </script>
   </html>