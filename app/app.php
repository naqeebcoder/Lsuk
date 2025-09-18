<?php use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
require '../lsuk_system/phpmailer/vendor/autoload.php';
include 'action.php';
include 'db.php';

if(isset($_POST['ap_login'])){
    $json=(object) null;
    $username=trim($_POST['ap_username']);
    $password=trim($_POST['ap_password']);
    if($username && $password){       
        $row = $obj->read_specific("interpreter_reg.id,interpreter_reg.interp,interpreter_reg.telep,interpreter_reg.trans, interpreter_reg.name, interpreter_reg.is_temp,interpreter_reg.interp_pix as photo,interpreter_reg.email,interpreter_reg.deleted_flag,interpreter_reg.active,interpreter_reg.id_doc_no,
        interpreter_reg.id_doc_file,interpreter_reg.id_doc_issue_date,interpreter_reg.id_doc_expiry_date,interpreter_reg.uk_citizen,
        interpreter_reg.work_evid_file,interpreter_reg.work_evid_issue_date,interpreter_reg.work_evid_expiry_date","interpreter_reg","TRIM(email)='".$username."' AND BINARY TRIM(password)='".$password."'");
        $row['photo']=$row['photo']?:"profile.png";
		$row['photo']=URL."/lsuk_system/file_folder/interp_photo/".$row['photo'];
        $available_jobs=array();
        if($row['interp']=="Yes"){
            array_push($available_jobs,"Face To Face");
        }
        if($row['telep']=="Yes"){
            array_push($available_jobs,"Telephone");
        }
        if($row['trans']=="Yes"){
            array_push($available_jobs,"Translation");
        }
    }
    if($row['is_temp']==1){
        $get_msg_db=$obj->read_specific('message','auto_replies','id=7');
        $json->msg=$get_msg_db['message'];
        $json->ap_status="failed";
    }else if(empty($row['id']) || is_null($row['deleted_flag'])){
        $get_msg_db=$obj->read_specific('message','auto_replies','id=1');
        $json->msg=$get_msg_db['message'];
        $json->ap_status="failed";
    }else if($row['deleted_flag']=='1' || $row['active'] == 1){
        $get_msg_db=$obj->read_specific('message','auto_replies','id=2');
        $json->msg=$get_msg_db['message'];
        $json->ap_status="failed";
    }else{
        $json->ap_user_id=$row['id'];$json->ap_email=$row['email'];$json->ap_status=$row['status'];
        $json->ap_name=$row['name'];$json->ap_photo=$row['photo'];$json->available_jobs=$available_jobs;
        $json->uk_citizen=$row['uk_citizen'];$json->passport_image=$row['id_doc_file'];$json->passport_number=$row['id_doc_no'];
        $json->passport_issue_date=$row['id_doc_issue_date'];$json->passport_expiry_date=$row['id_doc_expiry_date'];
        $json->work_evid=$row['work_evid_file'];$json->work_evid_issue_date=$row['work_evid_issue_date'];$json->work_evid_expiry_date=$row['work_evid_expiry_date'];
        $json->msg='Greetings '.ucwords($row['name']).'! Welcome to LSUK.';
    }
    header('Content-Type: application/json');
    echo json_encode($json, JSON_UNESCAPED_UNICODE);
}

//Update availability today
if(isset($_POST['update_availability'])){
    $json=(object) null;
    $update_done=0;
    if(isset($_POST['ap_user_id'])){
        $label=$_POST['update_availability']==1?"Yes":"No";
        $update_array=array(strtolower(date("l"))=>$label);
        if($_POST['update_availability']==1){
            $update_array[strtolower(date("l"))."_time"]=$_POST['availability_from'];
            $update_array[strtolower(date("l"))."_to"]=$_POST['availability_to'];
        }else{
            $update_array[strtolower(date("l"))."_time"]="00:00:00";
            $update_array[strtolower(date("l"))."_to"]="00:00:00";
        }
        $update_array['is_marked']=1;
        $obj->update("interpreter_reg",$update_array,"id=".$_POST['ap_user_id']);
        $update_done=1;
        if($update_done==1){
            $json->status="1";
            $json->msg="Your today's availability has been updated. Thank you";
        }else{
            $json->status="0";
            $json->msg="Failed to update your today's availability. Try again";
        }
    }else{
        $json->status="0";
        $json->msg="You must login to perform this action!";
    }
    header('Content-Type: application/json');
    echo json_encode($json, JSON_UNESCAPED_UNICODE);
}
if(isset($_POST['get_available_jobs'])){
    $json=(object) null;
    if(isset($_POST['ap_user_id']) && !empty($_POST['ap_user_id'])){
        $row=$obj->read_specific("interp,telep,trans,availability_option,is_marked","interpreter_reg","id=".$_POST['ap_user_id']);
        $available_jobs=array();
        if($row['interp']=="Yes"){
            array_push($available_jobs,"Face To Face");
        }
        if($row['telep']=="Yes"){
            array_push($available_jobs,"Telephone");
        }
        if($row['trans']=="Yes"){
            array_push($available_jobs,"Translation");
        }
        $json->availability_note="Goog morning! \nCan you mark your presence for the day. This doesn't guarantee a job but makes easy for LSUK to allocate a job. \nThank you";
        if($row['availability_option']=="1" && $row['is_marked']=="0"){
            $json->show_availability="1";
        }else{
            $json->show_availability="0";
        }
        $json->ap_status="success";
        $json->available_jobs=$available_jobs;
    }else{
        $json->msg="not_logged_in";
        $json->ap_status="failed";
    }
    header('Content-Type: application/json');
    echo json_encode($json, JSON_UNESCAPED_UNICODE);
}

if(isset($_POST['ap_get_notification'])){
    $json=(object) null;
    if(isset($_POST['ap_user_id']) && !empty($_POST['ap_user_id'])){
        $json->ap_status="success";
        $json=$obj->read_specific("(CASE WHEN notify_new_doc.status=1 THEN 0 ELSE 1 END) as doc_notify","interpreter_reg,notify_new_doc","interpreter_reg.id=notify_new_doc.interpreter_id AND interpreter_reg.id=".$_POST['ap_user_id']);
    }else{
        $json->msg="not_logged_in";
        $json->ap_status="failed";
    }
    header('Content-Type: application/json');
    echo json_encode($json, JSON_UNESCAPED_UNICODE);
}
//forgot password
if(isset($_POST['ap_forgot_email'])){
    $json=(object) null;
    if(!empty($_POST['ap_forgot_email'])){
        $forgot_email=trim($_POST['ap_forgot_email']);
        $reg=0;
        $check_data=$obj->read_specific("id,password,deleted_flag,active","interpreter_reg","TRIM(email)='".$forgot_email."'");
        $id=$check_data['id'];
        $password=$check_data['password'];
        $deleted_flag=$check_data['deleted_flag'];
        if(!empty($id) && $deleted_flag==0 && $check_data['active'] == 0){
            $mail = new PHPMailer(true);
            try {
                    $from_add = "hr@lsuk.org";$from_name = "LSUK Account Security";
                    $em_format=$obj->read_specific("em_format","email_format","id=35")['em_format'];
                    $data   = ["[PASSWORD]"];
                    $to_replace  = [$password];
                    $message=str_replace($data, $to_replace,$em_format);
                    $mail->SMTPDebug = 1;
                    //$mail->isSMTP(); 
                    //$mailer->Host = 'smtp.office365.com';
                    $mail->SMTPAuth   = true;
                    $mail->Username   = 'info@lsuk.org';
                    $mail->Password   = 'LangServ786';
                    $mail->SMTPSecure = 'tls';
                    $mail->Port       = 587;
                    $mail->setFrom($from_add,$from_name);
                    $mail->addAddress($forgot_email);
                    $mail->addReplyTo($from_add,$from_name);
                    $mail->isHTML(true);
                    $mail->Subject = 'Password recovery for LSUK online portal';
                    $mail->Body = $message;
                if($mail->send()){
                    $mail->ClearAllRecipients();
                    $json->msg="forgot_success";
                }else{
                    $json->msg="forgot_failed";
                }
            } catch (Exception $e) {$json->mailer="failed";}
        }else{
            $json->msg="no_record";
        }
}else{
    $json->msg="empty_email";
}
    header('Content-Type: application/json');
    echo json_encode($json, JSON_UNESCAPED_UNICODE);
}
//Application form
if(isset($_POST["application_form"])){
    echo '<pre>';
    print_r($_POST);exit;
}

//Api for missing documents
if(isset($_POST['ap_missing_documents'])){
    $json=(object) null;
        if(isset($_POST['ap_user_id']) && !empty($_POST['ap_user_id'])){
            $json->ap_status="success";
            $record =$obj->read_all("*","interpreter_reg","id=".$_POST['ap_user_id']);
            $interpreter = $record->fetch_assoc();
            $json = [
                'missing' => [
                    'work_evid' => [
                        'file' => $interpreter['work_evid_file'],
                        'label' => 'Right to work evidence',
                        'work_evid_issue_date' => $interpreter['work_evid_issue_date'],
                        'work_evid_expiry_date' => $interpreter['work_evid_expiry_date']
                    ],
                    'applicationForm' => [
                        'file' => $interpreter['applicationForm_file'],
                        'label' => 'Application Form'
                    ],
                    'agreement' => [
                        'file' => $interpreter['agreement_file'],
                        'label' => 'Agreement'
                    ],
                    'dbs' => [
                        'file' => $interpreter['dbs_file'],
                        'label' => 'CRB/DBS',
                        'dbs_no' => $interpreter['dbs_no'],
                        'dbs_issue_date' => $interpreter['dbs_issue_date'],
                        'dbs_expiry_date' => $interpreter['dbs_expiry_date'],
                        'is_auto_renewal' => $interpreter['is_dbs_auto']
                    ],
                    'nrpsi' => [
                        'label' => 'NRPSI',
                        'nrpsi_number' => $interpreter['nrpsi_number']
                    ],
                    'translation_qualification' => [
                        'file' => $interpreter['int_qualification'],
                        'label' => 'Translation Qualification',
                    ],
                    'nrcpd' => [
                        'file' => $interpreter['nrcpd_file'],
                        'label' => 'NRCPD',
                    ],
                    'asli' => [
                        'label' => 'ASLI',
                        'asli_number' => $interpreter['asli_number']
                    ],
                    'id_doc' => [
                        'file' => $interpreter['id_doc_file'],
                        'label' => 'Identity Document',
                        'id_doc_no' => $interpreter['id_doc_no'],
                        'id_doc_issue_date' => $interpreter['id_doc_issue_date'],
                        'id_doc_expiry_date' => $interpreter['id_doc_expiry_date']
                    ],
                    'nin' => [
                        'file' => $interpreter['nin'],
                        'label' => 'National Insurance Number / UTR',
                        'ni' => $interpreter['ni']
                    ],
                    'acNo' => [
                        'acNo' => $interpreter['acNo'],
                        'label' => 'Bank Details',
                        'bnakName' => $interpreter['bnakName'],
                        'acName' => $interpreter['acName'],
                        'acntCode' => $interpreter['acntCode']
                    ],
                    'dps' => [
                        'file' => $interpreter['dps'],
                        'label' => 'DPSI'
                    ]

                ]
            ];

            
        }else{
            $json->msg="not_logged_in";
            $json->ap_status="failed";
        }
        header('Content-Type: application/json');
        echo json_encode($json, JSON_UNESCAPED_UNICODE);
}

/* Section for updating missing documents ends */
//app token creation
if(isset($_POST['ap_device_id']) && isset($_POST['ap_add_token']) && isset($_POST['ap_user_id'])){
$json=(object) null;
    if(isset($_POST['ap_user_id']) && !empty($_POST['ap_user_id'])){
        $token_done=0;
		$idz=$obj->read_specific("GROUP_CONCAT(id) as idz","int_tokens","device_id='".$_POST['ap_device_id']."' OR token='".$_POST['ap_add_token']."' OR (int_id=".$_POST['ap_user_id']." and dated < DATE_SUB(NOW(), INTERVAL 30 DAY))")["idz"];
        if(!is_null($idz)){
			$obj->delete("int_tokens","id IN (".$idz.")");
		}
        $obj->insert("int_tokens",array("device_id"=>$_POST['ap_device_id'],"int_id"=>$_POST['ap_user_id'],"token"=>$_POST['ap_add_token'],"dated"=>date('Y-m-d h:i:s')));
        $token_done=1;
        if($token_done==1){
            $json->msg="token_success";
        }else{
            $json->msg="token_failed";
        }
    }else{
        $json->msg="not_logged_in";
    }
    header('Content-Type: application/json');
    echo json_encode($json, JSON_UNESCAPED_UNICODE);
}
//app update token
if(isset($_POST['ap_update_token']) && isset($_POST['ap_token']) && isset($_POST['ap_device_id'])){
    $json=(object) null;
    if($obj->update("int_tokens",array("token"=>$_POST['ap_token'],"dated"=>date('Y-m-d h:i:s')),"device_id='".$_POST['ap_device_id']."'")){
        $json->msg="token_updated";
    }else{
        $json->msg="token_not_updated";
    }
    header('Content-Type: application/json');
    echo json_encode($json, JSON_UNESCAPED_UNICODE);
}
//app delete token
if(isset($_POST['ap_delete_token']) && isset($_POST['ap_device_id'])){
    $json=(object) null;
    
    if($obj->delete("int_tokens","device_id='".$_POST['ap_device_id']."'")){
        $json->msg="token_deleted";
    }else{
        $json->msg="token_not_deleted";
    }
    header('Content-Type: application/json');
    echo json_encode($json, JSON_UNESCAPED_UNICODE);
}
if(isset($_POST['ap_profile'])){
    $json=(object) null;
    if(isset($_POST['ap_user_id']) && !empty($_POST['ap_user_id'])){
        $json=$obj->read_specific("id,name,interp_pix as photo,email,contactNo,contactNo2,dob,reg_date,rph,rpm,rpu,gender,interp,telep,trans,city,CONCAT(interpreter_reg.buildingName,' ',interpreter_reg.line1,' ',interpreter_reg.line2,' ',interpreter_reg.line3,' ',interpreter_reg.city,' ',interpreter_reg.postCode) as address,postCode,IF(dbs_checked=0, 'Yes','No') as dbs_checked, IF(subscribe=1,'Yes','No') as subscribe,code","interpreter_reg","id=".$_POST['ap_user_id']);
        $json['photo']=$json['photo']?:"profile.png";
        $json['photo']=URL."/lsuk_system/file_folder/interp_photo/".$json['photo'];
        $json['rating']=$obj->read_specific("( CASE WHEN (record<0) THEN '-1' WHEN ((record>=0 AND record<=5) OR record IS NULL) THEN '0' WHEN (record>5 AND record<=20) THEN '1' WHEN (record>20 AND record<=40) THEN '2' 
        WHEN (record>40 AND record<=60) 
        THEN '3' WHEN (record>60 AND record<=80) THEN '4' ELSE '5' END) as record from (SELECT (sum(punctuality)+sum(appearance)+sum(professionalism)+sum(confidentiality)+sum(impartiality)+sum(accuracy)+sum(rapport)+sum(communication))/COUNT(interp_assess.id) as record","interp_assess,interpreter_reg","interp_assess.interpName=interpreter_reg.code AND interp_assess.interpName='".$json['code']."') as record")['record'];
        $query_jobs=$obj->read_all("count(interpreter.id) as jobs,round(IFNULL(sum(interpreter.hoursWorkd),0),2) as hours", "interpreter","interpreter.intrpName =".$_POST['ap_user_id']." and interpreter.deleted_flag=0 and interpreter.order_cancel_flag=0 UNION ALL select count(telephone.id) as jobs,round(IFNULL(sum(telephone.hoursWorkd),0),2) as hours from telephone WHERE telephone.intrpName =".$_POST['ap_user_id']." and telephone.deleted_flag=0 and telephone.order_cancel_flag=0 UNION ALL select count(translation.id) as jobs,round(IFNULL(sum(translation.numberUnit),0),2) as hours from translation WHERE translation.intrpName =".$_POST['ap_user_id']." and translation.deleted_flag=0 and translation.order_cancel_flag=0");
    $jobs_array=array();
    while($jobs_row = $query_jobs->fetch_assoc()){
        array_push($jobs_array,$jobs_row);
    }
    $json['f2f_jobs']=$jobs_array[0]['jobs'];
    $json['f2f_hours']=$jobs_array[0]['hours'];
    $json['telep_jobs']=$jobs_array[1]['jobs'];
    $json['telep_hours']=$jobs_array[1]['hours'];
    $json['trans_jobs']=$jobs_array[2]['jobs'];
    $json['trans_units']=$jobs_array[2]['hours'];
    //$json['language_edit']="0";
        $array_level=array('1'=>'Native','2'=>'Fluent','3'=>'Intermediate','4'=>'Basic');
        $q_lang=$obj->read_all("distinct lang,level","interp_lang","code='".$json['code']."' ORDER BY lang");
        $array_lang=array();
        while($row_lang=$q_lang->fetch_assoc()){
            array_push($array_lang,$row_lang['lang'].' | '.$array_level[$row_lang['level']]);
            /*if($row_lang['level']>2){
                $json['language_edit']="1";
            }*/
        }
        $json['languages']=$array_lang;
        $q_skill=$obj->read_all("distinct skill","interp_skill","code='".$json['code']."'");
        $array_skill=array();
        while($row_skill=$q_skill->fetch_assoc()){
            array_push($array_skill,$row_skill['skill']);
        }
        $json['skills']=$array_skill;
    }else{
             $json->msg="not_logged_in";
    }
    header('Content-Type: application/json');
    echo json_encode($json, JSON_UNESCAPED_UNICODE);
}
//show bank details
if(isset($_POST['show_bank_details'])){
    $json=(object) null;
    if(isset($_POST['ap_user_id']) && !empty($_POST['ap_user_id'])){
        $json=$obj->read_specific("bnakName as bank_name,acName as account_name,acntCode as sort_code,acNo as account_number,(CASE WHEN acNo='' THEN '0' ELSE '1' END) as fill","interpreter_reg","id=".$_POST['ap_user_id']);
    }else{
        $json->msg="not_logged_in";
    }
    header('Content-Type: application/json');
    echo json_encode($json, JSON_UNESCAPED_UNICODE);
}
//profile edit
if(isset($_POST['ap_profile_edit'])){
    $json=(object) null;
    if(isset($_POST['ap_user_id']) && !empty($_POST['ap_user_id'])){
        $ap_user_id=$_POST['ap_user_id'];
        if(isset($_POST['ap_photo']) && !empty($_POST['ap_photo'])){
            $image_base64 = base64_decode($_POST['ap_photo']);
            $image_type = $_POST['ap_type'];
            $old_photo=$obj->read_specific("interp_pix","interpreter_reg","id=".$ap_user_id)['interp_pix'];
            $old_file='../lsuk_system/file_folder/interp_photo/'.$old_photo;
            if(file_exists($old_file) && !empty($old_photo)){
                unlink($old_file);
            }
            $file = round(microtime(true)).$image_type;
            if(file_put_contents("../lsuk_system/file_folder/interp_photo/".$file, $image_base64)){
                $obj->editFun("interpreter_reg",$ap_user_id,'interp_pix',$file);
				$obj->editFun("interpreter_reg",$ap_user_id,'pic_updated',1);
                $json->msg="photo_updated";
            }else{
                $json->msg="photo_failed";
            }
        }
        if(!isset($_POST['ap_photo']) && (isset($_POST['ap_name']) || isset($_POST['ap_email']) || isset($_POST['ap_contact']))){
                if(isset($_POST['ap_name'])){
                    $obj->editFun("interpreter_reg",$ap_user_id,'name',$_POST['ap_name']);
                }
                if(isset($_POST['ap_email'])){
                    $obj->editFun("interpreter_reg",$ap_user_id,'email',$_POST['ap_email']);
                }
                if(isset($_POST['ap_contact'])){
                    $obj->editFun("interpreter_reg",$ap_user_id,'contactNo',$_POST['ap_contact']);
                }
                if(isset($_POST['ap_contact2'])){
                    $obj->editFun("interpreter_reg",$ap_user_id,'contactNo2',$_POST['ap_contact2']);
                }
                $json->msg="profile_updated";
        }
        if(isset($_POST['ap_password_update'])){
            $ap_old_password=$_POST['ap_old_password'];
            $ap_new_password=$_POST['ap_new_password'];
            $db_old_password=$obj->read_specific("password","interpreter_reg","id=".$ap_user_id)['password'];
            if($db_old_password==$ap_old_password){
                    if(isset($ap_new_password) && $obj->editFun("interpreter_reg",$ap_user_id,'password',$ap_new_password)){
                        $json->status="1";
                        $json->msg="your passord has been updated successfully.";
                    }else{
                        $json->status="0";
                        $json->msg="Failed to update your passord ! Try again";
                    }
            }else{
                $json->status="0";
                $json->msg="Wrong old passord entered. try the valid one";
            }
        }
        if(isset($_POST['language_update'])){
            $languages=json_decode($_POST['languages']);
            $array_level=array('Native'=>'1','Fluent'=>'2','Intermediate'=>'3','Basic'=>'4');
            $lang_update=0;
            foreach($languages as $key){
                $lang_name=$key->language;
                $level_name=trim($key->level);
                $obj->update("interp_lang",array('level'=>$array_level[$level_name]),"code='id-".$ap_user_id."' AND lang='".$lang_name."'");
                $lang_update=1;
            }
            if($lang_update==1){
                $json->status="1";
                $json->msg="Proficiencies in selected languages have been updated.";
            }else{
                $json->status="0";
                $json->msg="Failed to update proficiencies in selected languages!";
            }
        }
    }else{
             $json->msg="not_logged_in";
    }
    header('Content-Type: application/json');
    echo json_encode($json, JSON_UNESCAPED_UNICODE);
}
//get interpreter documents request
if(isset($_POST['ap_docs']) && !isset($_POST['ap_doc_id'])){
    $json=(object) null;
    if(isset($_POST['ap_user_id']) && !empty($_POST['ap_user_id'])){
        $obj->update("notify_new_doc",array("status"=>1),"interpreter_id=".$_POST['ap_user_id']);
        $result = $obj->read_all("post_format.id,post_format.em_type as title,post_format.em_date as date,notify_new_doc_data.cities,notify_new_doc_data.languages,notify_new_doc_data.interpreters","post_format,notify_new_doc_data","post_format.id=notify_new_doc_data.post_id AND post_format.status='Active' ORDER by post_format.id DESC");
        $json=array();
        while($row = $result->fetch_assoc()){
            if(empty($row['interpreters']) ||(!empty($row['interpreters']) && in_array($_POST['ap_user_id'], explode(',',$row['interpreters'])))){
                unset($row['interpreters']);
                unset($row['cities']);
                unset($row['languages']);
                array_push($json,$row);
            }
        }
    }else{
        $json->msg="not_logged_in";
    }
    header('Content-Type: application/json');
    echo json_encode($json, JSON_UNESCAPED_UNICODE);
}
//get interpreter document details request
if(isset($_POST['ap_docs']) && isset($_POST['ap_doc_id'])){
    $json=(object) null;
    if(isset($_POST['ap_user_id']) && !empty($_POST['ap_user_id'])){
        $row_doc=$obj->read_specific("em_type as title,em_date as date,em_format as content","post_format","id=".$_POST['ap_doc_id']);
        $json=$row_doc;
    }else{
        $json->msg="not_logged_in";
    }
    header('Content-Type: application/json');
    echo json_encode($json, JSON_UNESCAPED_UNICODE);
}
//get interpreter salaries records
if(isset($_POST['ap_salary']) && !isset($_POST['ap_salary_id'])){
    $json=(object) null;
    if(isset($_POST['ap_user_id']) && !empty($_POST['ap_user_id'])){
        $ap_user_id=$_POST['ap_user_id'];
        //$get_dated=$_POST["ap_date"];
        $table='interp_salary';$append_date="";
        /*if(isset($get_dated) && !empty($get_dated)){
            $append_date="and dated='$get_dated'";
        }*/
        $query_salary=$obj->read_all("$table.invoice as 'invoice_number',$table.frm as 'invoice_from',$table.todate as 'invoice_to',$table.deduction,$table.salry as 'salary',$table.dated as 'paid_date'","$table","dated > '2020-02-01' and deleted_flag=0 $append_date and interp=".$ap_user_id);
        $json=array();
        while($row = $query_salary->fetch_assoc()){
            array_push($json,$row);
        }
        if(count($json)==0){
            $json->msg="no_salary_slips";
        }
    }else{
        $json->msg="not_logged_in";
    }
    header('Content-Type: application/json');
    echo json_encode($json, JSON_UNESCAPED_UNICODE);
}
//View active job edtails
if(isset($_POST['ap_value']) && isset($_POST['ap_job_id']) && !isset($_POST['ap_update'])){
    $json=(object) null;
    $json->skip_client_signature="0";
    if(isset($_POST['ap_user_id']) && !empty($_POST['ap_user_id'])){
        $table=$_POST['ap_value'];$update_id=$_POST['ap_job_id'];
        $row = $obj->read_specific("interpreter_reg.specific_agreed,$table.*,interpreter_reg.postCode as int_postcode,interpreter_reg.city as int_city,interpreter_reg.rph,interpreter_reg.rpm,interpreter_reg.rpu,interpreter_reg.email as interp_email","$table,interpreter_reg","$table.intrpName=interpreter_reg.id and $table.id=".$update_id);
        //echo '<pre>';
       // print_r($row);exit;
        //put interpreter post code
        $json->interpreter_postcode=$row['int_postcode'];
        $json->job_postcode=$row['postCode'];
        $interp_rpm=$row['rpm'];$interp_rpu=$row['rpu'];
        $intrpName=$row['intrpName'];$interp_rph=$row['rph'];$interp_email=$row['interp_email'];
        $bookinType=$row['bookinType'];
        $hoursWorkd=$table=='translation'?$row['numberUnit']:number_format($row['hoursWorkd'],2);
        if($table!='translation'){
            $rateHour=$row['rateHour'];
        }
        if($table=='interpreter'){
            $json->same_day_completed=$obj->read_specific("count(*) as counter","$table","cost_type IN ('dp','wp','mp') and assignDate='".date('Y-m-d')."' and deleted_flag=0 and order_cancel_flag=0")['counter'];
            if($row['specific_agreed']=='1' || ($row['int_city']!=$row['assignCity'])){
                $json->is_travel_time="1";
            }else{
                if ($row['int_city']==$row['assignCity']){$json->interpreter_postcode=$row['int_postcode'];$json->job_postcode=$row['postCode'];}
                $json->is_travel_time="0";
            }
            if($row['postCode'] && !empty($row['postcode_data'])){
                $postcode_data=explode(',',$row['postcode_data']);
                $json->latitude=$postcode_data[0];
                $json->longitude=$postcode_data[1];
            }else{
                $json->api_key="JwJX4MXnkEihbeI4wAPTIg14351";
                $json->latitude="";
                $json->longitude="";
            }
            $travelMile=$row['travelMile'];$rateMile=$row['rateMile'];$chargeTravel=$row['chargeTravel'];
            $travelCost=$row['travelCost'];$otherCost=$row['otherCost'];$travelTimeHour=$row['travelTimeHour'];
            $travelTimeRate=$row['travelTimeRate'];$chargeTravelTime=$row['chargeTravelTime'];
            if($rateHour!=0){$rate_per_hour=$rateHour;}else{$rate_per_hour=$interp_rph;}
        }
        if($table=='telephone'){
            $calCharges=$row['calCharges'];$otherCharges=$row['otherCharges'];
            if($rateHour!=0){$rate_per_minute=$rateHour;}else{$rate_per_minute=$interp_rpm;}
        }
        if($table=='translation'){
            $docType=$row['docType'];$numberUnit=$row['numberUnit'];$rpU=$row['rpU'];$otherCharg=$row['otherCharg'];
            if($rpU!=0){$rate_per_unit=$rpU;}else{$rate_per_unit=$interp_rpu;}
        }
        $chargInterp=$table=='translation'?number_format($numberUnit*$rpU):$row['chargInterp'];
        $deduction=$row['deduction'];$total_charges_interp=$row['total_charges_interp'];
        $wt_tm=$row['wt_tm'];$st_tm=$row['st_tm'];$fn_tm=$row['fn_tm'];
        $assignDate=$table=='translation'?$row['asignDate']:$row['assignDate'];
        $assignTime=$table=='translation'?'00:00:00':$row['assignTime'];
        $assignDur=$table=='translation'?'0':$row['assignDur'];
        $expected_start = date($assignDate.' '.substr($assignTime,0,5));
        $expected_end = date("Y-m-d H:i",strtotime("+$assignDur minutes", strtotime($expected_start)));
        if($assignDur>60){
            $hours=$assignDur / 60;
            if(floor($hours)>1){$hr="hours";}else{$hr="hour";}
            $mins=$assignDur % 60;
            if($mins==00){
                $get_dur=sprintf("%2d $hr",$hours);  
            }else{
                $get_dur=sprintf("%2d $hr %02d minutes",$hours,$mins);  
            }
        }else if($assignDur==60){
            $get_dur="1 Hour";
        }else{
            $get_dur=$assignDur." minutes";
        }
        $first_time=$row['wt_tm']!='1001-01-01 00:00:00'?$row['wt_tm']:$row['st_tm'];
        if(($table!='translation' && $hoursWorkd==0) || ($table=='translation' && $numberUnit==0)){
            $row['hours_filled']=0;
        }else{
           $row['hours_filled']=1;
        }
        $row['wait_time_filled']=$row['wt_tm']=='1001-01-01 00:00:00'?0:$row['wt_tm'];
        $row['start_time_filled']=$row['st_tm']=='1001-01-01 00:00:00'?0:$row['st_tm'];
        
         if($row['fn_tm']=='1001-01-01 00:00:00'){
            $row['finish_time_filled']=0;
            $hour_calculated=0;
        }else{
            $row['finish_time_filled']=$row['fn_tm'];
            //when uploaded start and end then display calculated hours
            $last_time=$row['fn_tm'];
            $hour_calculated = $hoursWorkd;
        }
        $valid_check_q=$obj->read_specific("id","$table","intrpName=".$_POST['ap_user_id']." and id=".$update_id);
        $valid_check=$valid_check_q!=''?'yes':'no';
        if($valid_check=='no'){
            $row['valid']=0;
            $json->msg='You are not allowed to open this job!';
        }else{
            $row['valid']=1;
        }
        if(date('Y-m-d H:i',strtotime($row['assignDate'].' '.$row['assignTime']))>date('Y-m-d H:i')){
            $row['problem_hours']=1;
            $json->msg="This job can't be started before ".date("d-m-Y H:i", strtotime($expected_start))." ! Thank you";
        }else if($row['deleted_flag']==1 || $row['order_cancel_flag']==1 || $row['orderCancelatoin']==1 || $row['intrp_salary_comit']==1){
            $row['problem_hours']=1;
            $json->msg='This job is not available now! Thank you';
        }else{
            $row['problem_hours']=0;
            $json->msg='';
        }
        if($table=='interpreter'){
            $json->assignCity=$row['assignCity']." (".$row['postCode'].")";
            $json->assignDate=$misc->dated($row['assignDate']);$json->job_key=$row['nameRef'];$json->feedback=$obj->read_specific("count(*)","interp_assess","interpName='id-".$_POST['ap_user_id']."' AND table_name='interpreter' AND order_id=".$update_id)['count(*)'];
            $json->hours_worked=($hoursWorkd);$json->rate_per_hour=($rate_per_hour);$json->charge_for_interpreting_time=($chargInterp);
            $json->travel_time_hours=($travelTimeHour);$json->travel_time_rate_per_hour=($travelTimeRate);$json->charge_for_travel_time=($chargeTravelTime);
            $json->travel_mile=($travelMile);$json->rate_per_mileage=($rateMile);$json->charge_for_travel_cost=($chargeTravel);
            $json->travel_cost=($travelCost);$json->other_cost=($otherCost);$json->deduction=($deduction);$json->job_type='Face To Face';
            $json->job_id=$row['id'];$json->max_rate=$row['source']!="Sign Language (BSL)"?"40":"1000";
        }
        if($table=='telephone'){
            $json->assignDate=$misc->dated($row['assignDate']);$json->job_type='Telephone';$json->job_id=$row['id'];$json->job_key=$row['nameRef'];
            $json->hours_worked=($hoursWorkd);$json->rate_per_minute=$rate_per_minute;$json->charge_for_interpreting_time=$chargInterp;
            $json->call_charges=($calCharges);$json->other_charges=($otherCharges);
            $json->deduction=($deduction);$json->max_rate=$row['source']!="Sign Language (BSL)"?"0.75":"1000";
        }
        if($table=='translation'){
            $json->assignDate=$misc->dated($row['asignDate']);$json->job_type='Translation';$json->job_id=$row['id'];$json->job_key=$row['nameRef'];
            $json->units=($numberUnit);$json->rate_per_unit=($rate_per_unit);$json->total_cost=$chargInterp;$json->any_other_charges=($otherCharg);
            $json->deduction=($deduction);$json->docType=($docType);$json->delivery_date=$misc->dated($row['deliverDate2']);
            $json->document_type=$obj->read_specific("trans_cat.tc_title as document_type","trans_cat","trans_cat.tc_id IN (".$row['docType'].")")['document_type'];
            $json->category=$obj->read_specific("GROUP_CONCAT(trans_types.tt_title SEPARATOR ', ') as category","trans_types","trans_types.tt_id IN (".$row['transType'].")")['category'];
            $json->max_rate=$row['source']!="Sign Language (BSL)"?"0.20":"1000";
        }
        $json->total_charges=($total_charges_interp);
        $json->interpreter_email=$interp_email;
        $json->source=$row['source'];
        $json->target=$row['target'];
        if($table!='translation'){
            $json->expected_start=date("d-m-Y H:i", strtotime($expected_start));$json->expected_end=date("d-m-Y H:i", strtotime($expected_end));$json->expected_duration=$get_dur;
            $json->hour_calculated=number_format($hour_calculated);$json->wait_time_filled=$row['wait_time_filled']!=0?date("d-m-Y H:i", strtotime($row['wait_time_filled'])):strval($row['wait_time_filled']);
            $json->start_time_filled=$row['start_time_filled']!=0?date("d-m-Y H:i", strtotime($row['start_time_filled'])):strval($row['start_time_filled']);$json->finish_time_filled=$row['finish_time_filled']!=0?date("d-m-Y H:i", strtotime($row['finish_time_filled'])):strval($row['finish_time_filled']);
            if($hour_calculated!=0){
                if($table=='telephone'){
                    $hours_done=$hour_calculated;
                }else{
                    $hours_done=$hour_calculated*60;
                }
                if($hours_done>60){
                    $hours_c=$hours_done / 60;
                    if(floor($hours_c)>1){$hr_c="hours";}else{$hr_c="hour";}
                    $mins_c=$hours_done % 60;
                    if($mins_c==00){
                        $get_dur_c=sprintf("%2d $hr_c",$hours_c);  
                    }else{
                        $get_dur_c=sprintf("%2d $hr_c %02d minutes",$hours_c,$mins_c);  
                    }
                }else if($hours_done==60){
                    $get_dur_c="1 Hour";
                }else{
                    $get_dur_c=number_format($hours_done)." minutes";
                }
            $json->duration_worked=$get_dur_c;
            }else{
                $json->duration_worked="Not filled yet";
            }
        }
        $step_1_completed="0";$step_1_enabled="0";
        $step_2_completed="0";$step_2_enabled="0";
        $step_3_completed="0";$step_3_enabled="0";
        $step_4_completed="0";$step_4_enabled="0";
        $json->assignment_expired="1";//To be reverted back to 0
        if($table=="translation"){
            $json->skip_client_signature="1";
            if($row['hours_filled']==0 || $row['total_charges_interp']=="0"){
                $step_1_completed="0";
                $step_1_enabled="1";
            }else if(($row['hours_filled']!=0 && $row['int_sig']=="") || ($row['int_sig']=="" && $json->skip_client_signature=="1")){
                $step_1_completed="1";
                $step_2_completed="1";
                $step_3_enabled="1";
            }else{
                $step_1_completed="1";
                $step_2_completed="1";
                $step_3_completed="1";
                $step_4_completed="1";
                $step_4_enabled="1";
            }
        }else if($table=="telephone"){
            $json->skip_client_signature="1";
            $expected_new_duration=$assignDur*2;
            $expected_duration_end = date("Y-m-d H:i",strtotime("+$expected_new_duration minutes", strtotime($expected_start)));
            if($row['assignDate']<date('Y-m-d') || ($row['assignDate']==date('Y-m-d') && $expected_duration_end<date('Y-m-d H:i'))){
               $json->assignment_expired="1";
            }
            if($row['finish_time_filled']==0 || $row['hours_filled']==0 || $row['total_charges_interp']=="0"){
                $step_1_completed="0";
                $step_1_enabled="1";
            }else if(($row['hours_filled']!=0 && $row['int_sig']=="") || ($row['int_sig']=="" && $json->skip_client_signature=="1")){
                $step_1_completed="1";
                $step_2_completed="1";
                $step_3_enabled="1";
            }else{
                $step_1_completed="1";
                $step_2_completed="1";
                $step_3_completed="1";
                $step_4_completed="1";
                $step_4_enabled="1";
            }
        }else{
            $json->skip_client_signature="0";
            $expected_new_duration=$assignDur*2;
            $expected_duration_end = date("Y-m-d H:i",strtotime("+$expected_new_duration minutes", strtotime($expected_start)));
            if($row['assignDate']<date('Y-m-d') || ($row['assignDate']==date('Y-m-d') && $expected_duration_end<date('Y-m-d H:i'))){
               $json->skip_client_signature="1";
               $json->assignment_expired="1";
            }
            if($row['finish_time_filled']=="0" || $row['hours_filled']=="0" || $row['total_charges_interp']=="0"){
                $step_1_completed="0";
                $step_1_enabled="1";
            }else if($row['cl_sig']=="" && $json->skip_client_signature=="0"){
                $step_1_completed="1";
                $step_2_enabled="1";
            }else if(($row['cl_sig']!="" && $row['int_sig']=="") || ($row['int_sig']=="" && $json->skip_client_signature=="1")){
                $step_1_completed="1";
                $step_2_completed="1";
                $step_3_enabled="1";
            }else{
                $step_1_completed="1";
                $step_2_completed="1";
                $step_3_completed="1";
                $step_4_completed="1";
                $step_4_enabled="1";
            }
        }
        
        $json->steps=array(
            "step 1"=>array("is_completed"=>$step_1_completed,"is_enabled"=>$step_1_enabled),
            "step 2"=>array("is_completed"=>$step_2_completed,"is_enabled"=>$step_2_enabled),
            "step 3"=>array("is_completed"=>$step_3_completed,"is_enabled"=>$step_3_enabled),
            "step 4"=>array("is_completed"=>$step_4_completed,"is_enabled"=>$step_4_enabled)
        );
        $json->hours_filled=strval($row['hours_filled']);if($table=='interpreter'){$json->client_signature=$row['cl_sig'];$json->cl_sign_date=$row['cl_sign_date']?:"";}$json->interpreter_signature=$row['int_sig'];$json->int_sign_date=$row['int_sign_date']?:"";
        $json->problem_hours=strval($row['problem_hours']);$json->valid=strval($row['valid']);$json->bonus_amount=strval(0.5);
    }else{
        $json->msg="not_logged_in";
    }
    header('Content-Type: application/json');
    echo json_encode($json, JSON_UNESCAPED_UNICODE);
}
//update hours for active job
if(isset($_POST['ap_value']) && isset($_POST['ap_job_id']) && isset($_POST['ap_update'])){
$json=(object) null;
    if(isset($_POST['ap_user_id']) && !empty($_POST['ap_user_id'])){
        $table=$_POST['ap_value'];$update_id=$_POST['ap_job_id'];
        if($table=='interpreter'){$append_cl=",$table.cl_sig";}
        $get_fields=$obj->read_specific("interpreter_reg.email,$table.orgName $append_cl,$table.int_sig","interpreter_reg,$table","$table.intrpName=interpreter_reg.id and $table.id=".$update_id);
        $int_email=$get_fields['email'];$orgName=$get_fields['orgName'];
        $old_cs=$get_fields['cl_sig'];$old_is=$get_fields['int_sig'];
        if(!isset($_POST['ap_fields']) && $table!='translation'){
            $get_rec=$obj->read_specific("assignDate,assignTime,assignDur,wt_tm,st_tm,fn_tm","$table","id=".$update_id);
            $assignment_start_date=date('Y-m-d H:i',strtotime($get_rec['assignDate'].' '.$get_rec['assignTime']));
        }
        // waiting and start time will updated when user click on arrived and start job button
        
        if(isset($_POST['ap_wait_time']) && !empty($_POST['ap_wait_time'])){
            $wait_time=$_POST['ap_wait_time']==1?date('Y-m-d H:i'):date("Y-m-d H:i", strtotime($_POST['ap_wait_time']));
            if($wait_time>=$assignment_start_date){
               $obj->update("$table",array("wt_tm"=>$wait_time),"id=".$update_id);
            }
            $json->msg=date("d-m-Y H:i", strtotime($wait_time));
        }
        if(isset($_POST['ap_start_time']) && !empty($_POST['ap_start_time'])){
            $start_time=$_POST['ap_start_time']==1?date('Y-m-d H:i'):date("Y-m-d H:i", strtotime($_POST['ap_start_time']));
            if($start_time<$assignment_start_date){
               $start_time=$assignment_start_date;
            }
            $obj->update("$table",array("st_tm"=>$start_time),"id=".$update_id);
            if($table!='translation'){
                $json->msg=date("d-m-Y H:i", strtotime($start_time));
            }
        }
        
        if(isset($_POST['ap_finish_time']) && !empty($_POST['ap_finish_time'])){
            $finish_time=$_POST['ap_finish_time']==1?date('Y-m-d H:i'):date("Y-m-d H:i", strtotime($_POST['ap_finish_time']));
            $obj->update("$table",array("fn_tm"=>$finish_time),"id=".$update_id);
            $get_rec=$obj->read_specific("assignDate,assignTime,assignDur,wt_tm,st_tm,fn_tm","$table","id=".$update_id);
            $first_time=($get_rec['wt_tm']!='1001-01-01 00:00:00' && $get_rec['wt_tm']!='0000-00-00 00:00:00')?$get_rec['wt_tm']:$get_rec['st_tm'];
            $last_time=$get_rec['fn_tm'];
            $t1 = strtotime($first_time);
            $t2 = strtotime($last_time);
            $diff = $t2 - $t1;
            $hours = $diff / 3600;
            $rounded_value=$misc->round_quarter($hours,4);
            if($table=='interpreter'){
                $new_hour_val = $hours<$get_rec['assignDur']/60?$get_rec['assignDur']/60:$misc->round_quarter($hours,4);
            }else{
                $new_hour_val = ($hours*60)<$get_rec['assignDur']?$get_rec['assignDur']:round($hours*60);
            }
            $json->hour_uploaded = strval($new_hour_val);
            $obj->update("$table",array("hoursWorkd"=>$new_hour_val),"id=".$update_id);
            $json->msg=date("d-m-Y H:i", strtotime($finish_time));
            $row=$obj->read_specific("$table.hoursWorkd,$table.chargInterp","$table","$table.id=".$update_id);
            $json->hours_worked=$row['hoursWorkd'];
            $json->charge_for_interpreting_time=$row['chargInterp'];
            $hour_calculated=$row['hoursWorkd'];
            $json->hour_calculated=$hour_calculated;
            if($hour_calculated!=0){
                if($table=='telephone'){
                    $hours_done=$hour_calculated;
                }else{
                    $hours_done=$hour_calculated*60;
                }
                if($hours_done>60){
                    $hours_c=$hours_done / 60;
                    if(floor($hours_c)>1){$hr_c="hours";}else{$hr_c="hour";}
                    $mins_c=$hours_done % 60;
                    if($mins_c==00){
                        $get_dur_c=sprintf("%2d $hr_c",$hours_c);  
                    }else{
                        $get_dur_c=sprintf("%2d $hr_c %02d minutes",$hours_c,$mins_c);  
                    }
                }else if($hours_done==60){
                    $get_dur_c="1 Hour";
                }else{
                    $get_dur_c=number_format($hours_done)." minutes";
                }
                $json->duration_worked=$get_dur_c;
            }else{
                $json->duration_worked="Not filled yet";
            }
        }
        if(isset($_POST['ap_fields'])){
          $_POST['deduction']=0;
            if($table=='interpreter'){
                $data_update=array("hoursWorkd"=>$_POST['hours_worked'],"rateHour"=>$_POST['rate_per_hour'],"chargInterp"=>$_POST['charge_for_interpreting_time'],
                "travelTimeHour"=>$_POST['travel_time_hours'],"travelTimeRate"=>$_POST['travel_time_rate_per_hour'],"chargeTravelTime"=>$_POST['charge_for_travel_time'],
                "travelMile"=>$_POST['travel_mile'],"rateMile"=>$_POST['rate_per_mileage'],"chargeTravel"=>$_POST['charge_for_travel_cost'],
                "travelCost"=>$_POST['travel_cost'],"admnchargs"=>0.50,"otherCost"=>$_POST['other_cost'],"deduction"=>$_POST['deduction'],
                "total_charges_interp"=>$_POST['total_charges'],
                "tm_by"=>'i',"cost_type"=>$_POST['cost_type']);
            }else if($table=='telephone'){
                $data_update=array("hoursWorkd"=>$_POST['hours_worked'],"rateHour"=>$_POST['rate_per_hour'],"chargInterp"=>$_POST['charge_for_interpreting_time'],"calCharges"=>$_POST['call_charges'],"otherCharges"=>$_POST['other_charges'],"admnchargs"=>0.50,"deduction"=>$_POST['deduction'],
                "total_charges_interp"=>$_POST['total_charges'],
                "tm_by"=>'i');
            }else{
                $data_update=array("numberUnit"=>$_POST['units'],"rpU"=>$_POST['rate_per_unit'],"otherCharg"=>$_POST['any_other_charges'],"admnchargs"=>0.50,"deduction"=>$_POST['deduction'],
                "total_charges_interp"=>$_POST['total_charges'],
                "tm_by"=>'i');
            }
            $form_option=$obj->update("$table",$data_update,"id=".$update_id);
            $obj->update($table,array('hrsubmited'=>'Self','interp_hr_date'=>date("Y-m-d")),"id=".$update_id);
            if($form_option){
                $json->status="1";
                $json->msg="Expenses have been updated for this job. Thank you";
            }else{
                $json->status="0";
                $json->msg="Failed to uypdate expenses for this job. Try again";
            }
            $mail = new PHPMailer(true);
            try {
                if($table=='interpreter'){
                    $email_append="Face To Face Interpreting";
                }else if($table=='telephone'){
                    $email_append="Telephone Interpreting";
                }else{
                    $email_append="Translation";
                }
                $from_add = "info@lsuk.org";
                $mail->SMTPDebug = 0;
                //$mail->isSMTP(); 
                //$mailer->Host = 'smtp.office365.com';
                $mail->SMTPAuth   = true;
                $mail->Username   = 'info@lsuk.org';
                $mail->Password   = 'LangServ786';
                $mail->SMTPSecure = 'tls';
                $mail->Port       = 587;
                $mail->setFrom($from_add, 'LSUK Timehseet');
                $mail->addAddress($int_email);
                $mail->addReplyTo($from_add, 'LSUK');
                $mail->isHTML(true);
                $mail->Subject = "Confirmation of Timesheet upload for ".$email_append." Project ID ". $update_id;
                $mail->Body    = "Dear Linguist,<br>You have successfully uploaded your timesheet for ".$email_append." Assignment.<br>
                Thank you<br>Best Regards<br>LSUK Limited";
                if($mail->send()){
                    $mail->ClearAllRecipients();
                }
            } catch (Exception $e) {}
        }
            if(isset($_POST['attachment']) && !empty($_POST['attachment'])){
                $at_done=0;
                if($table=='interpreter'){
                    $at_append="i_";
                }else if($table=='telephone'){
                    $at_append="tp_";
                }else{
                    $at_append="tr_";
                }
                $decoded=json_decode($_POST['attachment']);
                $i=0;
                foreach ($decoded as $value){
                    $i++;
                    $at_done=1;
                    $file_name = $at_append.round(microtime(true)).$i.'.png';
                    $file = base64_decode($value->encoded);
                    if(file_put_contents("../file_folder/job_files/".$file_name, $file)){
                        $obj->insert('job_files',array('tbl' => $table,'file_name'=>$file_name,'order_id'=>$update_id,'interpreter_id'=>$_POST['ap_user_id'], 'dated'=>date('Y-m-d h:i:s')));
                    }
                }
                if($at_done==1){
                    $json->status="1";
                    $json->msg="Attachment have been uploaded for this job. Thank you";
                }else{
                    $json->status="0";
                    $json->msg="Failed to upload attachment for this job. Try again";
                }
            }
            if(isset($_POST['client_signature']) && !empty($_POST['client_signature'])){
                if($table=='interpreter'){
                    $sig_append_c="i_";
                }else if($table=='telephone'){
                    $sig_append_c="tp_";
                }else{
                    $sig_append_c="tr_";
                }
                $cs = $_POST['client_signature'];
                $old_cs_file="../file_folder/client_signatures/".$old_cs;
                if(file_exists($old_cs_file) && !empty($old_cs)){
                    unlink($old_cs_file);
                }
                $cs_img = base64_decode($cs);
                //$cs_file = $sig_append_c.round(microtime(true)).'.png';
                $cs_file = $sig_append_c.$update_id.'.png';
                file_put_contents("../file_folder/client_signatures/".$cs_file, $cs_img);
                if($obj->update($table,array('cl_sig'=>$cs_file,'cl_sign_date'=>date('Y-m-d h:i:s')),"id=".$update_id)){
                    $json->status="1";
                    $json->msg="Client signature has been marked successfully. Thank you";
                }else{
                    $json->status="0";
                    $json->msg="Failed to signed client signature for this job! Please try again";
                }
            }
            if(isset($_POST['feedback'])){
                $feed_confirm=$obj->insert("interp_assess",array("interpName"=>'id-'.$_POST['ap_user_id'],"orgName"=>$orgName,"professionalism"=>$_POST['professionalism'],"impartiality"=>$_POST['impartiality'],"appearance"=>$_POST['appearance'],"punctuality"=>$_POST['punctuality'],"communication"=>$_POST['communication'],"p_reason"=>$_POST['comment'],"get_feedback"=>'App',"submittedBy"=>'App',"dated"=>date('Y-m-d'),"p_feedbackby"=>$_POST['assignment_incharge'],"order_id"=>$update_id));
                if($feed_confirm){
                    $json->status="1";
                    $json->msg="Feedback has been added successfully. Thank you";
                }else{
                    $json->status="0";
                    $json->msg="Failed to add feedback for this job! Please try again";
                }
            }
            if(isset($_POST['interpreter_signature']) && !empty($_POST['interpreter_signature'])){
                if($table=='interpreter'){
                    //update commit column to 1, change by waseem
                   // $obj->update($table,array('commit'=>1),"id=".$update_id);
                    $sig_append_i="i_";
                }else if($table=='telephone'){
                    $sig_append_i="tp_";
                }else{
                    $sig_append_i="tr_";
                }
                $is = $_POST['interpreter_signature'];
                $old_is_file="../file_folder/interpreter_signatures/".$old_is;
                if(file_exists($old_is_file) && !empty($old_is)){
                    unlink($old_is_file);
                }
                $is_img = base64_decode($is);
                $is_file = $sig_append_i.$update_id.'.png';
                file_put_contents("../file_folder/interpreter_signatures/".$is_file, $is_img);
                if($obj->update($table,array('int_sig'=>$is_file,'int_sign_date'=>date('Y-m-d h:i:s')),"id=".$update_id)){
                    $json->status="1";
                    $json->msg="You have successfully signed and completed this job. Thank you";
                }else{
                    $json->status="0";
                    $json->msg="Failed to signed this job! Please try again";
                }
            }

            // Client Signature Screen Shot saving
            if(isset($_POST['client_signature_shot']) && !empty($_POST['client_signature_shot'])){
                if($table=='interpreter'){
                    $sig_append_c="i_";
                }else if($table=='telephone'){
                    $sig_append_c="tp_";
                }else{
                    $sig_append_c="tr_";
                }
                $cs = $_POST['client_signature_shot'];
                $old_cs_file="../file_folder/client_signature_shots/".$old_cs;
                if(file_exists($old_cs_file) && !empty($old_cs)){
                    unlink($old_cs_file);
                }
                $cs_img = base64_decode($cs);
                //$cs_file = $sig_append_c.round(microtime(true)).'.png';
                $cs_file = $sig_append_c.$update_id.'.png';
                file_put_contents("../file_folder/client_signature_shots/".$cs_file, $cs_img);
                if($obj->update($table,array('client_sign_screen'=>$cs_file,'client_sign_screen_date'=>date('Y-m-d h:i:s')),"id=".$update_id)){
                    $json->status="1";
                    $json->msg="Client signature screen shot has been marked successfully. Thank you";
                }else{
                    $json->status="0";
                    $json->msg="Failed to signed client signature screen shot for this job! Please try again";
                }
            }
            // End of client signature screen shot saving

            //Interpreter Signature screen shot saving
            if(isset($_POST['interpreter_signature_shot']) && !empty($_POST['interpreter_signature_shot'])){
                if($table=='interpreter'){
                    //update commit column to 1, change by waseem
                   // $obj->update($table,array('commit'=>1),"id=".$update_id);
                    $sig_append_i="i_";
                }else if($table=='telephone'){
                    $sig_append_i="tp_";
                }else{
                    $sig_append_i="tr_";
                }
                $is = $_POST['interpreter_signature_shot'];
                $old_is_file="../file_folder/interpreter_signature_shots/".$old_is;
                if(file_exists($old_is_file) && !empty($old_is)){
                    unlink($old_is_file);
                }
                $is_img = base64_decode($is);
                $is_file = $sig_append_i.$update_id.'.png';
                file_put_contents("../file_folder/interpreter_signature_shots/".$is_file, $is_img);
                if($obj->update($table,array('interp_sign_screen'=>$is_file,'interp_sign_screen_date'=>date('Y-m-d h:i:s')),"id=".$update_id)){
                    $json->status="1";
                    $json->msg="Interpreter signature screen shot has been marked successfully. Thank you";
                }else{
                    $json->status="0";
                    $json->msg="Failed to signed this job! Please try again";
                }
            }

            //End of interpreter signature screen saving
    }else{
        $json->msg="not_logged_in";
    }
    header('Content-Type: application/json');
    echo json_encode($json, JSON_UNESCAPED_UNICODE);
}

//get available jobs request
if(isset($_POST['ap_jobs']) && !isset($_POST['ap_tracking']) && !isset($_POST['ap_view'])){
$json=(object) null;
    if(isset($_POST['ap_user_id']) && !empty($_POST['ap_user_id'])){
         $table=@$_POST['ap_value'];
         $get_data=$obj->read_specific("gender,code,email","interpreter_reg","id=".$_POST['ap_user_id']);
         $interp_code=$get_data['code'];
         $gender=$get_data['gender'];
        if($gender=='Female'){
        $gender_req='Male';
        }else{
        $gender_req='Female';
        }
        if($table!='translation'){
            if($table=='interpreter'){
                $put_id="AND interpreter.id=11166";
                $table_name="'Face To Face' as job_type";
                $query_details="$table.assignDate,substr($table.assignTime,1,5) as assignTime,$table.assignDur,CONCAT($table.assignCity,' (',substr($table.postCode,1,3),')') as assignCity,(SELECT interp_cat.ic_title from interp_cat WHERE interp_cat.ic_id=interpreter.interp_cat) as category";
                //echo $query_details;exit;
            }else{
                $put_id="AND telephone.id=3288";
                $table_name="'".ucwords($table)."' as job_type";
                $query_details="$table.assignDate,$table.assignTime,$table.assignDur,(SELECT comunic_types.c_title from comunic_types WHERE comunic_types.c_id=telephone.comunic) as communication_type";
            }
            $put=isset($_POST['ap_value'])?"and $table.gender!= '$gender_req'":" ";
        }else{
            $put_id="AND translation.id=1427";
            $table_name="'".ucwords($table)."' as job_type";
            $query_details="$table.asignDate as assignDate,deliverDate,(SELECT trans_cat.tc_title from trans_cat WHERE trans_cat.tc_id=docType) as document_type,(SELECT GROUP_CONCAT(trans_types.tt_title SEPARATOR ', ') FROM trans_types WHERE trans_types.tt_id IN (translation.transType)) as category,DATE_FORMAT(deliverDate2,'%d-%m-%Y') as delivery_date";
            $put=" ";
        }
        if(isset($_POST['ap_value'])){
            // condition to check if job is F2F or Telephone then get guess_dur
            $guess_dur_cond = '';
            if($_POST['ap_value'] == 'interpreter' || $_POST['ap_value'] == 'telephone'){
                $guess_dur_cond = ','.$table.'.guess_dur';
            }
            $result = $obj->read_all("$table_name,$table.id as job_id,$table.nameRef as job_key $guess_dur_cond,$table.source,$table.target,$query_details","$table,interpreter_reg","$table.deleted_flag=0 and $table.order_cancel_flag=0 and $table.jobStatus= 1 and $table.is_temp= 0 and $table.intrpName= '' ".$put." and $table.jobDisp= 1 and interpreter_reg.code='$interp_code' and 
            interpreter_reg.active='0' AND (interpreter_reg.actnow='Active' OR (interpreter_reg.actnow='Inactive' AND CURRENT_DATE() NOT BETWEEN interpreter_reg.actnow_time AND interpreter_reg.actnow_to)) AND interpreter_reg.deleted_flag=0 AND interpreter_reg.is_temp=0");
        }else{
            $result = $obj->read_all("'Face To Face' as job_type,interpreter.id as job_id,interpreter.nameRef as job_key,interpreter.source,interpreter.target,interpreter.assignDate,substr(interpreter.assignTime,1,5) as assignTime,interpreter.assignDur,CONCAT(interpreter.assignCity,' (',interpreter.postCode,')') as assignCity,'no_display' as document_type,'no_display' as communication_type,(SELECT interp_cat.ic_title from interp_cat WHERE interp_cat.ic_id=interpreter.interp_cat) as category FROM interpreter,interpreter_reg where interpreter.deleted_flag=0 and interpreter.order_cancel_flag=0 and interpreter.jobStatus= 1 and interpreter.is_temp= 0 and interpreter.intrpName= '' and interpreter.gender!= '$gender_req' and interpreter.jobDisp= 1 and interpreter_reg.code='$interp_code' and 
            interpreter_reg.active='0' AND (interpreter_reg.actnow='Active' OR (interpreter_reg.actnow='Inactive' AND CURRENT_DATE() NOT BETWEEN interpreter_reg.actnow_time AND interpreter_reg.actnow_to)) AND interpreter_reg.deleted_flag=0 AND interpreter_reg.is_temp=0 UNION
            SELECT 'Telephone' as job_type,telephone.id as job_id,telephone.nameRef as job_key,telephone.source,telephone.target,telephone.assignDate,telephone.assignTime,telephone.assignDur,'no_display' as assignCity,'no_display' as document_type,(SELECT comunic_types.c_title from comunic_types WHERE comunic_types.c_id=telephone.comunic) as communication_type,'no_display' as category FROM telephone,interpreter_reg where telephone.deleted_flag=0 and telephone.order_cancel_flag=0 and telephone.jobStatus= 1 and telephone.is_temp= 0 and telephone.intrpName= '' and telephone.gender!= '$gender_req' and telephone.jobDisp= 1 and interpreter_reg.code='$interp_code' and 
            interpreter_reg.active='0' AND (interpreter_reg.actnow='Active' OR (interpreter_reg.actnow='Inactive' AND CURRENT_DATE() NOT BETWEEN interpreter_reg.actnow_time AND interpreter_reg.actnow_to)) AND interpreter_reg.deleted_flag=0 AND interpreter_reg.is_temp=0 UNION 
            SELECT 'Translation' as job_type,translation.id as job_id,translation.nameRef as job_key,translation.source,translation.target,translation.asignDate as assignDate,'no_display' as assignTime,'no_display' as assignDur,'no_display' as assignCity,(SELECT trans_cat.tc_title from trans_cat WHERE trans_cat.tc_id=docType) as document_type,docType as communication_type,(SELECT GROUP_CONCAT(trans_types.tt_title SEPARATOR ', ') FROM trans_types WHERE trans_types.tt_id IN (translation.transType)) as category","translation,interpreter_reg","translation.deleted_flag=0 and translation.order_cancel_flag=0 and translation.jobStatus= 1 and translation.is_temp= 0 and translation.intrpName='' and translation.jobDisp= 1 and interpreter_reg.code='$interp_code' and 
            interpreter_reg.active='0' AND (interpreter_reg.actnow='Active' OR (interpreter_reg.actnow='Inactive' AND CURRENT_DATE() NOT BETWEEN interpreter_reg.actnow_time AND interpreter_reg.actnow_to)) AND interpreter_reg.deleted_flag=0 AND interpreter_reg.is_temp=0");
        }
        // echo $result;die;
        $json=array();
        while($row = $result->fetch_assoc()){
            $assignDur=$row['assignDur'];
            if($assignDur>60){
                $hours=$assignDur / 60;
                if(floor($hours)>1){$hr="hours";}else{$hr="hour";}
                    $mins=$assignDur % 60;
                    if($mins==0){
                        $get_dur=sprintf("%2d $hr",$hours);  
                    }else{
                        $get_dur=sprintf("%2d $hr %02d minutes",$hours,$mins);  
                    }
                }else if($assignDur==60){
                    $get_dur="1 Hour";
                }else{
                    $get_dur=$assignDur." minutes";
                }
            $get_dur=$row['job_type']=='Translation'?'':$get_dur;
            $row['assignDur']=$get_dur;
            if($row['source']!=$row['target'] && $row['source']!='English' && $row['target']!='English'){
                $lang_checker=$obj->read_specific("COUNT(DISTINCT interp_lang.lang) as counter","interp_lang","interp_lang.lang IN ('".$row['source']."','".$row['target']."') and interp_lang.code='$interp_code'")['counter'];
                $allow_int=$lang_checker>=2?"yes":"no";
            }else if($row['source']=='English' && $row['target']!='English'){
                $lang_checker=$obj->read_specific("COUNT(DISTINCT interp_lang.lang) as counter","interp_lang","interp_lang.lang='".$row['target']."' and interp_lang.code='$interp_code'")['counter'];
                $allow_int=$lang_checker==1?"yes":"no";
                }else if($row['source']!='English' && $row['target']=='English'){
                    $lang_checker=$obj->read_specific("COUNT(DISTINCT interp_lang.lang) as counter","interp_lang","interp_lang.lang='".$row['source']."' and interp_lang.code='$interp_code'")['counter'];
                    $allow_int=$lang_checker==1?"yes":"no";
                }else if($row['source']==$row['target'] && $row['source']!='English'){
                    $lang_checker=$obj->read_specific("COUNT(DISTINCT interp_lang.lang) as counter","interp_lang","interp_lang.lang='".$row['source']."' and interp_lang.code='$interp_code'")['counter'];
                    $allow_int=$lang_checker>=1?"yes":"no";
                }else{
                    $lang_checker=1;
                    $allow_int="yes";
                }
                if($allow_int=="yes"){
                    $row['assignDate']=$misc->dated($row['assignDate']);
                    array_push($json,$row);
                }
        }//end of while
        if(count($json)==0){
            $json="no_jobs";
        }
    }else{
    $json->msg="not_logged_in";
    }
header('Content-Type: application/json');
echo json_encode($json, JSON_UNESCAPED_UNICODE);
}
//Update Parking Starts
if(isset($_POST['update_parking'])){
    $json=(object) null;
    if(isset($_POST['ap_user_id']) && !empty($_POST['ap_user_id'])){
        $job_id = $_POST['job_id'];
        $is_parking = $_POST['is_parking'];
        $parking_amount = $_POST['parking_amount'];
        $images = [];
        $decoded=$_POST['attachment'];
                $i=0;
                foreach ($decoded as $value){
                    $i++;
                    $file_name = round(microtime(true)).$i.'.png';
                    $file = base64_decode($value);
                    if(file_put_contents("../file_folder/parking_tickets/".$file_name, $file)){
                        array_push($images , $file_name);
                    }
                }
                $obj->update('interpreter',array('otherCost' => $parking_amount,'is_parking'=>1,'parking_tickets'=>json_encode($images)),"id=".$job_id);
    }else{
        $json->msg="not_logged_in";
    }
    header('Content-Type: application/json');
    echo json_encode($json, JSON_UNESCAPED_UNICODE);
    }
// Update Parking Ends
//get active jobs request
if(isset($_POST['ap_active_jobs'])){
    // error_reporting(E_ALL);
$json=(object) null;
    if(isset($_POST['ap_user_id']) && !empty($_POST['ap_user_id'])){
        $table=@$_POST['ap_value'];
        $ap_user_id=$_POST['ap_user_id'];
        if($table!='translation'){
            if($table=='interpreter'){
                $put_id="AND interpreter.id=11166";
                $table_name="'Face To Face' as job_type";
                $query_details="$table.assignDate,substr($table.assignTime,1,5) as assignTime,$table.assignDur,$table.assignCity,$table.postCode,(SELECT interp_cat.ic_title from interp_cat WHERE interp_cat.ic_id=interpreter.interp_cat) as category";
            }else{
                $put_id="AND telephone.id=3288";
                $table_name="'".ucwords($table)."' as job_type";
                $query_details="$table.assignDate,$table.assignTime,$table.assignDur,'' as postCode,(SELECT comunic_types.c_title from comunic_types WHERE comunic_types.c_id=telephone.comunic) as communication_type";
            }
            //$hours_zero="AND $table.hoursWorkd=0";
        }else{
            $put_id="AND translation.id=1427";
            $table_name="'".ucwords($table)."' as job_type";
            $query_details="$table.asignDate as assignDate,(SELECT trans_cat.tc_title from trans_cat WHERE trans_cat.tc_id=docType) as document_type,docType as communication_type,(SELECT GROUP_CONCAT(trans_types.tt_title SEPARATOR ', ') FROM trans_types WHERE trans_types.tt_id IN (translation.transType)) as category,DATE_FORMAT(deliverDate2,'%d-%m-%Y') as delivery_date";
            //$hours_zero="AND $table.numberUnit=0";
        }
        if(isset($_POST['ap_value'])){
            $result = $obj->read_all("$table_name,$table.id as job_id,$table.nameRef as job_key,$table.source,$table.target,$query_details","$table,interpreter_reg","$table.intrpName=interpreter_reg.id AND $table.deleted_flag=0 and $table.order_cancel_flag=0 and $table.jobStatus= 1 and $table.intrpName= '$ap_user_id' and $table.int_sig='' and $table.salary_id=0 and $table.hrsubmited='' ORDER BY assignDate ASC");
        }else{
            $result = $obj->read_all("*",
            "(SELECT 'Face To Face' as job_type,interpreter.id as job_id,interpreter.nameRef as job_key,interpreter.source,interpreter.target,interpreter.assignDate,substr(interpreter.assignTime,1,5) as assignTime,interpreter.assignDur,interpreter.assignCity,interpreter.postCode,'no_display' as document_type,'no_display' as communication_type,(SELECT interp_cat.ic_title from interp_cat WHERE interp_cat.ic_id=interpreter.interp_cat) as category,'no_display' as delivery_date FROM interpreter,interpreter_reg where interpreter.intrpName=interpreter_reg.id AND interpreter.deleted_flag=0 and interpreter.order_cancel_flag=0 and interpreter.jobStatus= 1 and interpreter.intrpName= '$ap_user_id' and interpreter.int_sig='' and interpreter.salary_id=0 and interpreter.hrsubmited='' UNION 
            SELECT 'Telephone' as job_type,telephone.id as job_id,telephone.nameRef as job_key,telephone.source,telephone.target,telephone.assignDate,telephone.assignTime,telephone.assignDur,'no_display' as assignCity,'no_display' as postCode,'no_display' as document_type,(SELECT comunic_types.c_title from comunic_types WHERE comunic_types.c_id=telephone.comunic) as communication_type,'no_display' as category,'no_display' as delivery_date FROM telephone,interpreter_reg where telephone.intrpName=interpreter_reg.id AND telephone.deleted_flag=0 and telephone.order_cancel_flag=0 and telephone.jobStatus= 1 and telephone.intrpName= '$ap_user_id' and telephone.int_sig='' and telephone.salary_id=0 and telephone.hrsubmited='' UNION 
            SELECT 'Translation' as job_type,translation.id as job_id,translation.nameRef as job_key,translation.source,translation.target,translation.asignDate as assignDate,'no_display' as assignTime,'no_display' as assignDur,'no_display' as assignCity,'no_display' as postCode,(SELECT trans_cat.tc_title from trans_cat WHERE trans_cat.tc_id=docType) as document_type,docType as communication_type,(SELECT GROUP_CONCAT(trans_types.tt_title SEPARATOR ', ') FROM trans_types WHERE trans_types.tt_id IN (translation.transType)) as category,DATE_FORMAT(deliverDate2,'%d-%m-%Y') as delivery_date FROM translation,interpreter_reg WHERE translation.intrpName=interpreter_reg.id AND translation.deleted_flag=0 and translation.order_cancel_flag=0 and translation.jobStatus= 1 and translation.intrpName='$ap_user_id' and translation.int_sig='' and translation.salary_id=0 and translation.hrsubmited='') as grp",
            "1 ORDER BY assignDate ASC");
        }
        $json=array();
        while($row = $result->fetch_assoc()){
            $assignDate = $row['assignDate'];
            $assignTime = $row['assignTime'];
            $assignDur=$row['assignDur'];
            if($assignDur>60){
                $hours=$assignDur / 60;
                if(floor($hours)>1){$hr="hours";}else{$hr="hour";}
                    $mins=$assignDur % 60;
                    if($mins==0){
                        $get_dur=sprintf("%2d $hr",$hours);  
                    }else{
                        $get_dur=sprintf("%2d $hr %02d minutes",$hours,$mins);  
                    }
                }else if($assignDur==60){
                    $get_dur="1 Hour";
                }else{
                    $get_dur=$assignDur." minutes";
                }
            $get_dur=$row['job_type']=='Translation'?'no_display':$get_dur;
            if($row['job_type'] == 'Face To Face'){
                $job = $obj->read_specific("is_interpreter_arrived","interpreter","id=".$row['job_id']);
                $row['arrived'] = $job['is_interpreter_arrived'];
                $job = $obj->read_specific("on_the_way","interpreter","id=".$row['job_id']);
                $row['on_the_way'] = $job['on_the_way'];
            }else{
                $row['arrived'] = 0;
                $row['on_the_way'] = 0;
            }
            if($row['job_type'] == 'Telephone'){
                $job = $obj->read_specific("is_interpreter_ready","telephone","id=".$row['job_id']);
                $row['is_ready'] = $job['is_interpreter_ready'];
            }else{
                $row['is_ready'] = 0;
            }
            $expected_start = date($assignDate.' '.substr($assignTime,0,5));
            $expected_end = date("Y-m-d H:i",strtotime("+$assignDur minutes", strtotime($expected_start)));
            $row['expected_start']=$row['job_type']=='Translation'?'no_display':date("d-m-Y H:i", strtotime($expected_start));
            $row['expected_end']=$row['job_type']=='Translation'?'no_display':date("d-m-Y H:i", strtotime($expected_end));
            $row['assignDur']=$get_dur;
            $row['assignDate']=$misc->dated($row['assignDate']);
            $row['assignCity']=$row['assignCity']." (".$row['postCode'].")";
           
            $expected_end = new DateTime($expected_end);
            $current_time = date('d-m-Y H:i');
            $current_time = new DateTime($current_time);

            $diff = $current_time->diff($expected_end);
            $hours = $diff->h;
            $hours = $hours + ($diff->days*24);

            // if($current_time < $expected_end || $hours < 3)
            array_push($json,$row);
        }
    }else{
    $json->msg="not_logged_in";
    }
header('Content-Type: application/json');
echo json_encode($json, JSON_UNESCAPED_UNICODE);
}


//get missing time sheets jobs
if(isset($_POST['ap_missing_timesheets_jobs'])){
    // error_reporting(E_ALL);
$json=(object) null;
    if(isset($_POST['ap_user_id']) && !empty($_POST['ap_user_id'])){
        $table=@$_POST['ap_value'];
        $ap_user_id=$_POST['ap_user_id'];
        if($table!='translation'){
            if($table=='interpreter'){
                $put_id="AND interpreter.id=11166";
                $table_name="'Face To Face' as job_type";
                $query_details="$table.assignDate,substr($table.assignTime,1,5) as assignTime,$table.assignDur,$table.assignCity,$table.postCode,(SELECT interp_cat.ic_title from interp_cat WHERE interp_cat.ic_id=interpreter.interp_cat) as category";
            }else{
                $put_id="AND telephone.id=3288";
                $table_name="'".ucwords($table)."' as job_type";
                $query_details="$table.assignDate,$table.assignTime,$table.assignDur,'' as postCode,(SELECT comunic_types.c_title from comunic_types WHERE comunic_types.c_id=telephone.comunic) as communication_type";
            }
            //$hours_zero="AND $table.hoursWorkd=0";
        }else{
            $put_id="AND translation.id=1427";
            $table_name="'".ucwords($table)."' as job_type";
            $query_details="$table.asignDate as assignDate,(SELECT trans_cat.tc_title from trans_cat WHERE trans_cat.tc_id=docType) as document_type,docType as communication_type,(SELECT GROUP_CONCAT(trans_types.tt_title SEPARATOR ', ') FROM trans_types WHERE trans_types.tt_id IN (translation.transType)) as category,DATE_FORMAT(deliverDate2,'%d-%m-%Y') as delivery_date";
            //$hours_zero="AND $table.numberUnit=0";
        }
        if(isset($_POST['ap_value'])){
            $result = $obj->read_all("$table_name,$table.id as job_id,$table.nameRef as job_key,$table.source,$table.target,$query_details","$table,interpreter_reg","$table.intrpName=interpreter_reg.id AND $table.deleted_flag=0 and $table.order_cancel_flag=0 and $table.jobStatus= 1 and $table.intrpName= '$ap_user_id' and $table.int_sig='' and $table.salary_id=0 and $table.hrsubmited='' ORDER BY assignDate ASC");
        }else{
            $result = $obj->read_all("*",
            "(SELECT 'Face To Face' as job_type,interpreter.id as job_id,interpreter.nameRef as job_key,interpreter.source,interpreter.target,interpreter.assignDate,substr(interpreter.assignTime,1,5) as assignTime,interpreter.assignDur,interpreter.assignCity,interpreter.postCode,'no_display' as document_type,'no_display' as communication_type,(SELECT interp_cat.ic_title from interp_cat WHERE interp_cat.ic_id=interpreter.interp_cat) as category,'no_display' as delivery_date FROM interpreter,interpreter_reg where interpreter.intrpName=interpreter_reg.id AND interpreter.deleted_flag=0 and interpreter.order_cancel_flag=0 and interpreter.jobStatus= 1 and interpreter.intrpName= '$ap_user_id' and interpreter.int_sig='' and interpreter.salary_id=0 and interpreter.hrsubmited='' UNION 
            SELECT 'Telephone' as job_type,telephone.id as job_id,telephone.nameRef as job_key,telephone.source,telephone.target,telephone.assignDate,telephone.assignTime,telephone.assignDur,'no_display' as assignCity,'no_display' as postCode,'no_display' as document_type,(SELECT comunic_types.c_title from comunic_types WHERE comunic_types.c_id=telephone.comunic) as communication_type,'no_display' as category,'no_display' as delivery_date FROM telephone,interpreter_reg where telephone.intrpName=interpreter_reg.id AND telephone.deleted_flag=0 and telephone.order_cancel_flag=0 and telephone.jobStatus= 1 and telephone.intrpName= '$ap_user_id' and telephone.int_sig='' and telephone.salary_id=0 and telephone.hrsubmited='' UNION 
            SELECT 'Translation' as job_type,translation.id as job_id,translation.nameRef as job_key,translation.source,translation.target,translation.asignDate as assignDate,'no_display' as assignTime,'no_display' as assignDur,'no_display' as assignCity,'no_display' as postCode,(SELECT trans_cat.tc_title from trans_cat WHERE trans_cat.tc_id=docType) as document_type,docType as communication_type,(SELECT GROUP_CONCAT(trans_types.tt_title SEPARATOR ', ') FROM trans_types WHERE trans_types.tt_id IN (translation.transType)) as category,DATE_FORMAT(deliverDate2,'%d-%m-%Y') as delivery_date FROM translation,interpreter_reg WHERE translation.intrpName=interpreter_reg.id AND translation.deleted_flag=0 and translation.order_cancel_flag=0 and translation.jobStatus= 1 and translation.intrpName='$ap_user_id' and translation.int_sig='' and translation.salary_id=0 and translation.hrsubmited='') as grp",
            "1 ORDER BY assignDate ASC");
        }
        $json=array();
        while($row = $result->fetch_assoc()){
            $assignDate = $row['assignDate'];
            $assignTime = $row['assignTime'];
            $assignDur=$row['assignDur'];
            if($assignDur>60){
                $hours=$assignDur / 60;
                if(floor($hours)>1){$hr="hours";}else{$hr="hour";}
                    $mins=$assignDur % 60;
                    if($mins==0){
                        $get_dur=sprintf("%2d $hr",$hours);  
                    }else{
                        $get_dur=sprintf("%2d $hr %02d minutes",$hours,$mins);  
                    }
                }else if($assignDur==60){
                    $get_dur="1 Hour";
                }else{
                    $get_dur=$assignDur." minutes";
                }
            $get_dur=$row['job_type']=='Translation'?'no_display':$get_dur;
            if($row['job_type'] == 'Face To Face'){
                $job = $obj->read_specific("is_interpreter_arrived","interpreter","id=".$row['job_id']);
                $row['arrived'] = $job['is_interpreter_arrived'];
                $job = $obj->read_specific("on_the_way","interpreter","id=".$row['job_id']);
                $row['on_the_way'] = $job['on_the_way'];
            }else{
                $row['arrived'] = 0;
                $row['on_the_way'] = 0;
            }
            if($row['job_type'] == 'Telephone'){
                $job = $obj->read_specific("is_interpreter_ready","telephone","id=".$row['job_id']);
                $row['is_ready'] = $job['is_interpreter_ready'];
            }else{
                $row['is_ready'] = 0;
            }
            $expected_start = date($assignDate.' '.substr($assignTime,0,5));
            $expected_end = date("Y-m-d H:i",strtotime("+$assignDur minutes", strtotime($expected_start)));
            $row['expected_start']=$row['job_type']=='Translation'?'no_display':date("d-m-Y H:i", strtotime($expected_start));
            $row['expected_end']=$row['job_type']=='Translation'?'no_display':date("d-m-Y H:i", strtotime($expected_end));
            $row['assignDur']=$get_dur;
            $row['assignDate']=$misc->dated($row['assignDate']);
            $row['assignCity']=$row['assignCity']." (".$row['postCode'].")";
           
            $expected_end = new DateTime($expected_end);
            $current_time = date('d-m-Y H:i');
            $current_time = new DateTime($current_time);

            $diff = $current_time->diff($expected_end);
            $hours = $diff->h;
            $hours = $hours + ($diff->days*24);

            if($current_time > $expected_end && $hours > 3)
                 array_push($json,$row);
        }
    }else{
    $json->msg="not_logged_in";
    }
header('Content-Type: application/json');
echo json_encode($json, JSON_UNESCAPED_UNICODE);
}

/* Marked on the way Start */
if(isset($_POST['on_the_way'])){
    // error_reporting(E_ALL);
    $json=(object) null;
    $table = 'interpreter';
    if(isset($_POST['ap_user_id']) && !empty($_POST['ap_user_id'])){
        $job_id = $_POST['job_id'];
        $json = [];
        $job = $obj->read_specific("on_the_way","interpreter","id=".$job_id);
        array_push($json , [
            'on_the_way' => $job['on_the_way']
        ]);
        $obj->update('interpreter',array('on_the_way' => 1),"id=".$job_id);
        }else{
            $json->msg="not_logged_in";
    }
    header('Content-Type: application/json');
    echo json_encode($json, JSON_UNESCAPED_UNICODE);
}
/* Marked on the way End */

/* Start Job */
if(isset($_POST['ap_start_job'])){
    // error_reporting(E_ALL);
    $json=(object) null;
    $table = 'interpreter';
    if(isset($_POST['ap_user_id']) && !empty($_POST['ap_user_id'])){
        $job_id = $_POST['job_id'];
        $json = [];
        $obj->update('interpreter',array('st_tm' => date('Y-m-d H:i')),"id=".$job_id);
        }else{
            $json->msg="not_logged_in";
    }
    header('Content-Type: application/json');
    echo json_encode($json, JSON_UNESCAPED_UNICODE);
}
/* Start Job end */

/* Marked arrived Start */
if(isset($_POST['marked_arrived'])){
    // error_reporting(E_ALL);
    $json=(object) null;
    $table = 'interpreter';
    if(isset($_POST['ap_user_id']) && !empty($_POST['ap_user_id'])){
        $job_id = $_POST['job_id'];
        $json = [];
        $job = $obj->read_specific("is_interpreter_arrived","interpreter","id=".$job_id);
        array_push($json , [
            'arrived' => $job['is_interpreter_arrived']
        ]);
        $obj->update('interpreter',array('is_interpreter_arrived' => 1,'wt_tm' => date('Y-m-d H:i')),"id=".$job_id);
        }else{
            $json->msg="not_logged_in";
    }
    header('Content-Type: application/json');
    echo json_encode($json, JSON_UNESCAPED_UNICODE);
}

/* Marked arrived End */
/* Marked ready telephone job */
if(isset($_POST['marked_ready'])){
    // error_reporting(E_ALL);
    $json=(object) null;
    $table = 'telephone';
    if(isset($_POST['ap_user_id']) && !empty($_POST['ap_user_id'])){
        $job_id = $_POST['job_id'];
        $json = [];
        $job = $obj->read_specific("is_interpreter_ready","telephone","id=".$job_id);
        array_push($json , [
            'ready' => $job['is_interpreter_ready']
        ]);
        $obj->update('telephone',array('is_interpreter_ready' => 1),"id=".$job_id);
        
        }else{
            $json->msg="not_logged_in";
    }
    header('Content-Type: application/json');
    echo json_encode($json, JSON_UNESCAPED_UNICODE);
}

/* Marked read telephone job End */
/* update interpreter location start */
if(isset($_POST['update_location'])){
    // error_reporting(E_ALL);
    $json=(object) null;
    $table = 'interpreter_reg';
    if(isset($_POST['ap_user_id']) && !empty($_POST['ap_user_id'])){
        $interpreter_id = $_POST['ap_user_id'];
        $lat = $_POST['lat'];
        $lng = $_POST['lng'];
        $json = [];
        $obj->update($table,array('lat' => $lat,'lng' => $lng),"id=".$interpreter_id);
        
        }else{
            $json->msg="not_logged_in";
    }
    header('Content-Type: application/json');
    echo json_encode($json, JSON_UNESCAPED_UNICODE);
}

/* update interpreter location End */
/* get pending jobs start */
if(isset($_POST['pending_jobs'])){
    //  error_reporting(E_ALL);
    $json=(object) null;
    if(isset($_POST['ap_user_id']) && !empty($_POST['ap_user_id'])){
        $interpreter_id = $_POST['ap_user_id'];
    $json = [];
    if($_POST['ap_value'] == 'interpreter'){
    //$query =
    //"SELECT * from (SELECT interpreter.porder,comp_reg.po_req,'Interpreter' as type,interpreter.id,interpreter.st_tm,interpreter.fn_tm,interpreter.target,interpreter_reg.rph,interpreter_reg.rpm,interpreter_reg.rpu,interpreter.travelTimeHour,interpreter.chargeTravelTime,interpreter.travelMile,interpreter.chargeTravel,interpreter.travelCost,interpreter.otherCost,interpreter.intrpName,interpreter.orgName,interpreter_reg.name,interpreter_reg.id as interpreter_id,interpreter.source,interpreter.invoic_date,interpreter.assignDate,interpreter.assignTime,interpreter.orgContact,interpreter.submited, interpreter.aloct_by,interpreter.aloct_date,interpreter.dated,interpreter.hrsubmited,interpreter.comp_hrsubmited,interpreter.interp_hr_date, interpreter.comp_hr_date,interpreter.hoursWorkd,interpreter.C_hoursWorkd,interpreter.total_charges_comp,interpreter.cur_vat,interpreter.C_otherexpns, interpreter.credit_note,interpreter.C_admnchargs,interpreter.rAmount,interpreter.rDate,interpreter.sentemail,interpreter.printed,interpreter.printedby,interpreter.deleted_flag,interpreter.order_cancel_flag,interpreter.nameRef,interpreter.orgRef,interpreter.invoiceNo,interpreter.commit,comp_reg.email,comp_reg.abrv as comp_abrv FROM interpreter,interpreter_reg,comp_reg WHERE interpreter.intrpName=interpreter_reg.id AND interpreter.orgName=comp_reg.abrv AND interpreter.multInv_flag=0 AND interpreter.deleted_flag = 0 and interpreter.order_cancel_flag=0 and interpreter_reg.id = $interpreter_id and interpreter.commit=1 and (round(interpreter.rAmount,2) < round((interpreter.total_charges_comp+(interpreter.total_charges_comp*interpreter.cur_vat)),2) or interpreter.total_charges_comp =0)) as grp ORDER BY CONCAT(assignDate,' ',assignTime)";
    $query =
    "SELECT * from (SELECT interpreter.porder,comp_reg.po_req,'Interpreter' as type,interpreter.id,interpreter.chargInterp,interpreter.st_tm,interpreter.fn_tm,interpreter.target,interpreter_reg.rph,interpreter_reg.rpm,interpreter_reg.rpu,interpreter.travelTimeHour,interpreter.chargeTravelTime,interpreter.travelMile,interpreter.chargeTravel,interpreter.travelCost,interpreter.otherCost,interpreter.intrpName,interpreter.orgName,interpreter_reg.name,interpreter_reg.id as interpreter_id,interpreter.source,interpreter.invoic_date,interpreter.assignDate,interpreter.assignTime,interpreter.orgContact,interpreter.submited, interpreter.aloct_by,interpreter.aloct_date,interpreter.dated,interpreter.hrsubmited,interpreter.comp_hrsubmited,interpreter.interp_hr_date, interpreter.comp_hr_date,interpreter.hoursWorkd,interpreter.C_hoursWorkd,interpreter.total_charges_comp,interpreter.cur_vat,interpreter.C_otherexpns, interpreter.credit_note,interpreter.C_admnchargs,interpreter.rAmount,interpreter.rDate,interpreter.sentemail,interpreter.printed,interpreter.printedby,interpreter.deleted_flag,interpreter.order_cancel_flag,interpreter.nameRef,interpreter.orgRef,interpreter.invoiceNo,interpreter.commit,comp_reg.email,comp_reg.abrv as comp_abrv FROM interpreter,interpreter_reg,comp_reg WHERE interpreter.intrpName=interpreter_reg.id AND interpreter.orgName=comp_reg.abrv AND interpreter.multInv_flag=0 AND interpreter.deleted_flag = 0 and interpreter.order_cancel_flag=0 and interpreter_reg.id = $interpreter_id and interpreter.commit=1 and (round(interpreter.rAmount,2) < round((interpreter.total_charges_comp+(interpreter.total_charges_comp*interpreter.cur_vat)),2) or interpreter.total_charges_comp =0)) as grp ORDER BY CONCAT(assignDate,' ',assignTime)";
    //echo $query;exit;   
    $result = mysqli_query($con, $query);
        while($row = mysqli_fetch_assoc($result)){
            $job_start_time = new DateTime($row['st_tm']);
            $diff = $job_start_time->diff(new DateTime($row['fn_tm']));
            array_push($json , [
                'assignDate' => date('d-m-Y' , strtotime($row['assignDate'])),
                'jobKey' => $row['nameRef'],
                'source' => $row['source'],
                'target' => $row['target'],
                'type' => $row['type'],
                'interpreting_time' => ($diff->h * 60) + $diff->i,
                'interpreter_charges' => $row['chargInterp'],
                'rph' => $row['rph'],
                'rpm' => $row['rpm'],
                'rpu' => $row['rpu'],
                'travel_time' => $row['travelTimeHour'],
                'travel_time_payment' => $row['chargeTravelTime'],
                'travel_mileage' => $row['travelMile'],
                'mileage_payment' => $row['chargeTravel'],
                'travel_cost' => $row['travelCost'],
                'other_cost' => $row['otherCost']
            ]);
        }
    }elseif($_POST['ap_value'] == 'telephone'){
        $query = "SELECT * from (SELECT telephone.porder,comp_reg.po_req,'Telephone' as type,telephone.id,telephone.st_tm,telephone.fn_tm,telephone.target,interpreter_reg.rph,interpreter_reg.rpm,interpreter_reg.rpu,telephone.intrpName,telephone.orgName,interpreter_reg.name,interpreter_reg.id as interpreter_id,telephone.source,telephone.invoic_date,telephone.assignDate,telephone.assignTime,telephone.orgContact,telephone.submited, telephone.aloct_by,telephone.aloct_date,telephone.dated,telephone.hrsubmited,telephone.comp_hrsubmited,telephone.interp_hr_date,telephone.comp_hr_date, telephone.hoursWorkd,telephone.C_hoursWorkd,telephone.total_charges_comp,telephone.cur_vat,0 as C_otherexpns,telephone.credit_note,telephone.C_admnchargs, telephone.rAmount,telephone.rDate,telephone.sentemail,telephone.printed,telephone.printedby,telephone.deleted_flag,telephone.order_cancel_flag,telephone.nameRef,telephone.orgRef,telephone.invoiceNo,telephone.commit,comp_reg.email,comp_reg.abrv as comp_abrv FROM telephone,interpreter_reg,comp_reg WHERE telephone.intrpName=interpreter_reg.id AND telephone.orgName=comp_reg.abrv AND telephone.multInv_flag=0 AND telephone.deleted_flag = 0 and telephone.order_cancel_flag=0 and interpreter_reg.id = $interpreter_id and telephone.commit=1 and (round(telephone.rAmount,2) < round((telephone.total_charges_comp+(telephone.total_charges_comp*telephone.cur_vat)),2) or telephone.total_charges_comp =0)) as grp ORDER BY CONCAT(assignDate,' ',assignTime)";
        // echo $query;exit;
        $result = mysqli_query($con, $query);
        while($row = mysqli_fetch_assoc($result)){
            $job_start_time = new DateTime($row['st_tm']);
            $diff = $job_start_time->diff(new DateTime($row['fn_tm']));
            array_push($json , [
                'assignDate' => date('d-m-Y' , strtotime($row['assignDate'])),
                'jobKey' => $row['nameRef'],
                'source' => $row['source'],
                'target' => $row['target'],
                'type' => $row['type'],
                'interpreting_time' => $diff->i,
                'rph' => $row['rph'],
                'rpm' => $row['rpm'],
                'rpu' => $row['rpu']
            ]);
        }
    }else{
        $query = "SELECT * from (SELECT translation.porder,comp_reg.po_req,'Translation' as type,translation.id,translation.target,interpreter_reg.rph,interpreter_reg.rpm,interpreter_reg.rpu,translation.intrpName,translation.orgName,interpreter_reg.name,interpreter_reg.id as interpreter_id,translation.source,translation.invoic_date,translation.asignDate as assignDate,'00:00:00' as assignTime,translation.orgContact,translation.submited, translation.aloct_by,translation.aloct_date,translation.dated,translation.hrsubmited,translation.comp_hrsubmited,translation.interp_hr_date,translation.comp_hr_date, translation.numberUnit as hoursWorkd,translation.C_numberUnit as C_hoursWorkd,translation.total_charges_comp,translation.cur_vat,0 as C_otherexpns,translation.credit_note,translation.C_admnchargs, translation.rAmount,translation.rDate,translation.sentemail,translation.printed,translation.printedby,translation.deleted_flag,translation.order_cancel_flag,translation.nameRef,translation.orgRef,translation.invoiceNo,translation.commit,comp_reg.email,comp_reg.abrv as comp_abrv FROM translation,interpreter_reg,comp_reg WHERE translation.intrpName=interpreter_reg.id AND translation.orgName=comp_reg.abrv AND translation.multInv_flag=0 and interpreter_reg.id = $interpreter_id AND translation.deleted_flag = 0 and translation.order_cancel_flag=0 and translation.commit=1 and (round(translation.rAmount,2) < round((translation.total_charges_comp+(translation.total_charges_comp*translation.cur_vat)),2) or translation.total_charges_comp =0)) as grp ORDER BY CONCAT(assignDate,' ',assignTime)";
        // echo $query;exit;
        $result = mysqli_query($con, $query);
        while($row = mysqli_fetch_assoc($result)){
            array_push($json , [
                'assignDate' => date('d-m-Y' , strtotime($row['assignDate'])),
                'jobKey' => $row['nameRef'],
                'source' => $row['source'],
                'target' => $row['target'],
                'type' => $row['type'],
                'rph' => $row['rph'],
                'rpm' => $row['rpm'],
                'rpu' => $row['rpu']
            ]);
        }
    }
    }else{
        $json->msg="not_logged_in";
    }
    header('Content-Type: application/json');
    echo json_encode($json, JSON_UNESCAPED_UNICODE);
    }

/* get pending jobs end */

//get bidding request
if(isset($_POST['ap_jobs']) && isset($_POST['ap_view']) && isset($_POST['ap_value'])){
    $json=(object) null;
    if(isset($_POST['ap_user_id']) && !empty($_POST['ap_user_id'])){
        $job_id=trim($_POST['ap_view'],'"');
        $table=@$_POST['ap_value'];
        if($table!='translation'){
            if($table=='interpreter'){
                $table_name="'Face To Face' as job_type";
                $query_details="$table.assignDate,substr($table.assignTime,1,5) as assignTime,$table.assignDur,$table.guess_dur,$table.assignCity,$table.buildingName,$table.street,(SELECT interp_cat.ic_title from interp_cat WHERE interp_cat.ic_id=interpreter.interp_cat) as category,'' as transType,assignIssue as remarks,interp_cat,postCode,intrpName,postcode_data";
            }else{
                $table_name="'".ucwords($table)."' as job_type";
                $query_details="$table.assignDate,$table.assignTime,$table.assignDur,$table.guess_dur,(SELECT telep_cat.tpc_title from telep_cat WHERE telep_cat.tpc_id=telephone.telep_cat) as category,'' as transType,assignIssue as remarks,telep_type as interp_cat";
            }
        }else{
            $table_name="'".ucwords($table)."' as job_type";
            $query_details="$table.asignDate as assignDate,(SELECT trans_cat.tc_title from trans_cat WHERE trans_cat.tc_id=docType) as category,docType as interp_cat,transType,trans_detail,DATE_FORMAT(deliverDate2,'%d-%m-%Y') as delivery_date";
            $put=" ";
        }
        $json=array();
        $row = $obj->read_specific("$table_name,$table.id as job_id,$table.nameRef as job_key,$table.source,$table.target,$query_details,remrks","$table","id=".$job_id);
        if($table=="interpreter"){
            $title_string="it_title";
            $table_string="interp_types";
            $id_string="it_id";
        }else if($table=="telephone"){
            $title_string="tpt_title";
            $table_string="telep_types";
            $id_string="tpt_id";
        }else{
            $title_string="tt_title";
            $table_string="trans_types";
            $id_string="tt_id";
        }
        if($table!="translation"){
            if(($table=="interpreter" && $row['interp_cat']!='12') || ($table=="telephone" && $row['interp_cat']!='11')){
                $row['category_details']=$obj->read_specific("GROUP_CONCAT(CONCAT(".$title_string.")  SEPARATOR ' , ') as title",$table_string,"$id_string IN (".$row['interp_cat'].")")['title'];
            }else{
                $row['category_details']=$row['remarks']?:"Other";
            }
        }else{
            $row['category_details']=$obj->read_specific("GROUP_CONCAT(trans_types.tt_title SEPARATOR ', ') as category","trans_types","trans_types.tt_id IN (".$row['trans_detail'].")")['category']?:$row['remarks'];
        }
        $assignDur=$row['assignDur'];
            if($assignDur>60){
                $hours=$assignDur / 60;
                if(floor($hours)>1){$hr="hours";}else{$hr="hour";}
                $mins=$assignDur % 60;
                if($mins==00){
                    $get_dur=sprintf("%2d $hr",$hours);  
                }else{
                    $get_dur=sprintf("%2d $hr %02d minutes",$hours,$mins);  
                }
            }else if($assignDur==60){
                $get_dur="1 Hour";
            }else{
                $get_dur=$assignDur." minutes";
            }
            $guess_dur=$row['guess_dur'];
            if($assignDur!=$guess_dur && $guess_dur>0){
              if($guess_dur>60){
                  $guess_hours=$guess_dur / 60;
                  if(floor($guess_hours)>1){$guess_hr="hours";}else{$guess_hr="hour";}
                  $guess_mins=$guess_dur % 60;
                  if($guess_mins==0){
                      $get_guess_dur=sprintf("%2d $guess_hr",$guess_hours);  
                  }else{
                      $get_guess_dur=sprintf("%2d $guess_hr %02d minutes",$guess_hours,$guess_mins);  
                  }
              }else if($guess_dur==60){
                  $get_guess_dur="1 Hour";
              }else{
                  $get_guess_dur=$guess_dur." minutes";
              }
            }
            if($assignDur!=$guess_dur && $guess_dur>0){
              $row['html_notes']="This session is booked for ".$get_dur.", however it can take  up to ".$get_guess_dur." or longer.<br>
              Therefore please consider your unrestricted availability before bidding / accepting this job.
              In cases of short notice cancellation, you will be paid the booked time (".$get_dur.").<br>";
              if(!empty($row['remrks'])){
                  $row['html_notes'].=$row['remrks'];
              }
            }else{
                $row['html_notes']=!empty($row['remrks'])?$row['remrks']:"";
            }
            $get_dur=$row['job_type']=='Translation'?'no_display':$get_dur;
            $row['assignDur']=$get_dur;
            $row['bid']=$obj->read_specific("count(*)","bid","interpreter_id=".$_POST['ap_user_id']." AND job=".$job_id." AND tabName='".$table."'")["count(*)"];
            unset($row['interp_cat']);unset($row['remarks']);
            if(!empty($row['intrpName']) && $table=='interpreter'){
                if($row['postCode'] && !empty($row['postcode_data'])){
                $postcode_data=explode(',',$row['postcode_data']);
                $row['latitude']=$postcode_data[0];
                $row['longitude']=$postcode_data[1];
            }else{
                $row['api_key']="JwJX4MXnkEihbeI4wAPTIg14351";
                $row['latitude']="";
                $row['longitude']="";
            }
            }
            $row['assignDate']=$misc->dated($row['assignDate']);
            $row['assignCity']=$table=="interpreter"?$row['assignCity']." (".substr($row['postCode'],0,3).")":"no_display";
            $row['address']=$table=="interpreter"?$row['buildingName'].' '.$row['street'].' '.$row['assignCity']:"no_display";
            array_push($json,$row);
    }else{
        $json->msg="not_logged_in";
    }
header('Content-Type: application/json');
echo json_encode($json, JSON_UNESCAPED_UNICODE);
}
//get bidding request
if(isset($_POST['ap_jobs']) && isset($_POST['ap_tracking']) && isset($_POST['ap_value'])){
    $json=(object) null;
    if(isset($_POST['ap_user_id']) && !empty($_POST['ap_user_id'])){
        $interp_id=$_POST['ap_user_id'];
        $get_data=$obj->read_specific("name,code,contactNo,address","interpreter_reg","id=".$interp_id);
        $data1=$get_data['name'];
        $check_id=trim($_POST['ap_tracking'],'"');
        $val=$_POST['ap_value'];
        $codeid=$get_data['code'];
        $msg_cont_office='';
        $check_res=$obj->read_specific('id','bid','job='.$check_id.' and interpreter_id='.$_POST['ap_user_id'].' and tabName="'.$val.'"');
        $check_bid_booked=$obj->read_specific('id','bid','job='.$check_id.' and allocated=1 and tabName="'.$val.'"');
        $check_booked=$obj->unique_data($val,'intrpName','id',$check_id);
    if($check_res['id']!=''){
        $get_msg_db=$obj->read_specific('message','auto_replies','id=3');
        $json->msg=$get_msg_db['message'];
        $json->status='1';
    }else if($obj->read_specific("id",$val,"deleted_flag=0 AND order_cancel_flag=0 and is_temp= 0 AND id=".$check_id)['id']==''){
        $json->msg="Sorry ! This job is not available for bidding right now. Thanks";
        $json->status='2';
    }else{
        if($check_booked!='' || $check_bid_booked['id']!=''){
            $get_msg_db=$obj->read_specific('message','auto_replies','id=4');
            $json->msg=$get_msg_db['message'];
            $json->status='3';
        }else{
        $row_job = $obj->read_specific("*","$val","id=".$check_id);
        $assignDate=$val=='translation'?$row_job['asignDate']:$row_job['assignDate'];
        $sourceForJob=$row_job['source'];
        $targetForJob=$row_job['target'];
        $orgNameForJob=$row_job['orgName'];
        $query_booked="";
        if($val!='translation'){
            $assignTime=$row_job['assignTime'];
            $assignDur=$row_job['assignDur'];
            $dur_in_hr=$assignDur/60;
            $assignTime_req=substr($assignTime,0,5);
            $replaced_time=str_replace(':','.',$assignTime_req);
            $result_booked=$obj->read_all("id,assignDate,assignTime,assignDur,REPLACE(substr(assignTime,1,5),':','.') as new_time","$val","intrpName='$interp_id' and assignDate='$assignDate' and (REPLACE(substr(assignTime,1,5),':','.')=($replaced_time) OR REPLACE(substr(assignTime,1,5),':','.')=($replaced_time+$dur_in_hr)) AND deleted_flag=0 AND order_cancel_flag=0 and orderCancelatoin=0");
        }else{
            $result_booked=$obj->read_all("id","$val","intrpName='$interp_id' and asignDate='$assignDate' AND deleted_flag=0 AND order_cancel_flag=0 and orderCancelatoin=0");
        }
        if($result_booked->num_rows>0){
                $allot='no';
        }else{
            if($val=='translation'){
                $allot='yes';   
            }else{
            $result_booked=$obj->read_all("id,assignDate,substr(assignTime,1,5) as assignTime,assignDur/60 as assignDur,REPLACE(substr(assignTime,1,5),':','.') as new_time","$val","intrpName='$interp_id' and assignDate='$assignDate' AND deleted_flag=0 AND order_cancel_flag=0 and orderCancelatoin=0");
            if($result_booked->num_rows==0){
                $allot='yes';
            }else{
                
                $allot_array=array();
                while($row_booked = $result_booked->fetch_assoc()){
                    if($replaced_time>$row_booked['new_time']){
                        $get_dur=$replaced_time-($row_booked['new_time']+$row_booked['assignDur']);
                        if($get_dur>=0.30){
                            array_push($allot_array,"yes");
                        }else{
                            array_push($allot_array,"no");
                        }
                    }else{
                        $get_dur=$row_booked['new_time']-($replaced_time+$dur_in_hr);
                        if($get_dur>=0.30){
                            array_push($allot_array,"yes");
                        }else{
                                array_push($allot_array,"no");
                        }
                    }
                }
                if (in_array("no", $allot_array) && !in_array("yes", $allot_array)){
                    $allot= "no";
                }else if (!in_array("no", $allot_array) && in_array("yes", $allot_array)){
                    $allot= "yes";
                }else if (!in_array("no", $allot_array) && !in_array("yes", $allot_array)){
                    $allot= "yes";
                }else if (in_array("no", $allot_array) && in_array("yes", $allot_array)){
                    $allot= "no and yes";
                }else{
                    $allot= "yes";
                    $msg_cont_office='Contact with LSUK office';
                }
            }
        }  
    }
    $check_black=$obj->unique_dataAnd('interp_blacklist','id','interpName',$codeid,"orgName",$orgNameForJob);
    $check_on_hold=$obj->unique_data('interpreter_reg','on_hold','id',$interp_id);
    if($sourceForJob!='English' && $targetForJob!='English'){
    $put_lang="";$query_style='1';
    }else if($sourceForJob=='English' && $targetForJob!='English'){
        $put_lang="AND interp_lang.lang='$targetForJob'";$query_style='2';
    }else if($sourceForJob!='English' && $targetForJob=='English'){
        $put_lang="AND interp_lang.lang='$sourceForJob'";$query_style='2';
    }else{
        $put_lang="";$query_style='3';
    }
    if($query_style=='1'){
    $check_lang=$obj->read_specific("count(interp_lang.id) as counter","interpreter_reg,interp_lang","interpreter_reg.code=interp_lang.code AND (SELECT COUNT(DISTINCT interp_lang.lang) FROM interp_lang WHERE interp_lang.lang IN ('".$sourceForJob."','".$targetForJob."') and interp_lang.code='$codeid')=2");
}else if($query_style=='2'){
    $check_lang=$obj->read_specific("count(*) as counter","interp_lang","code='$codeid' $put_lang");
}else{
    $check_lang=$obj->read_specific("count(*) as counter",$val,"id=".$check_id);
}
$row_feedback = $obj->read_specific("((sum(punctuality) + sum(appearance) + sum(professionalism) + sum(confidentiality) + sum(impartiality) + sum(accuracy) + sum(rapport) + sum(communication))*100) /(COUNT(interp_assess.id)*120) as result","interp_assess","interp_assess.interpName='".$codeid."'");
$today_date=date('Y-m-d');$today_date=date('Y-m-d');
$today_plus_7=date('Y-m-d', strtotime("+7 day"));
$firstday = date('Y-m-d', strtotime("this week"));
$more_jobs=0;
if($val=="translation"){
    $row_count_jobs= $obj->read_specific("count(*) as jobs_done","$val","asignDate BETWEEN '".$firstday."' AND '".$today_plus_7."' and intrpName='".$interp_id."' AND deleted_flag=0 AND order_cancel_flag=0 and orderCancelatoin=0");
}else{
    $row_count_jobs= $obj->read_specific("count(*) as jobs_done","$val","assignDate BETWEEN '".$firstday."' AND '".$today_plus_7."' and intrpName='".$interp_id."' AND deleted_flag=0 AND order_cancel_flag=0 and orderCancelatoin=0");
}
if($row_count_jobs['jobs_done']>=2){
    $more_jobs=1;
}
$row_count_amend = $obj->read_specific("count(*) as amend_counts","amended_records","dated BETWEEN '".$firstday."' AND '".$today_date."' and interpreter_id='$interp_id'");
$empty_doc='No';

//error_reporting(E_ALL);
$row_docs = $obj->read_specific("(CASE WHEN ((interpreter_reg.active='0' AND interpreter_reg.actnow='Active') OR (interpreter_reg.active='0' AND interpreter_reg.actnow='Inactive' AND CURRENT_DATE() NOT BETWEEN interpreter_reg.actnow_time AND interpreter_reg.actnow_to)) THEN 'Yes' ELSE 'No' END) as activeness,applicationForm,agreement,crbDbs,identityDocument,gender","interpreter_reg","id=".$interp_id);

$activeness=$row_docs['activeness'];
//echo 'heresss';
if(empty($row_docs['applicationForm']) || empty($row_docs['agreement']) || empty($row_docs['crbDbs']) || 
          empty($row_docs['identityDocument']) || $row_docs['identityDocument']=='Not Provided' || 
          $row_docs['applicationForm']=='Not Provided' || $row_docs['agreement']=='Not Provided' || 
          $row_docs['crbDbs']=='Not Provided')
        {$empty_doc='Yes';}
$allow_gender=0;
if(!isset($gender_required) || $gender_required=='' || $gender_required=='No Preference'){
    $allow_gender=1;
}else{
    if($row_docs['gender']==$gender_required){
        $allow_gender=1;
    }
}
if($val=='interpreter'){$find_string="Face To Face";$check_col='interp';}else if($val=='telephone'){$find_string="Telephone";$check_col='telep';}else{$find_string="Translation";$check_col='trans';} 
$check_ability=$obj->read_specific("$check_col as check_col","interpreter_reg","id=".$interp_id)['check_col'];
$check_jobdDisp=$row_job['jobDisp'];
$check_jobStatus=$row_job['jobStatus'];

$bid_counter=$obj->read_specific('count(*) as bid_counter','bid',"job=".$check_id." and tabName='".$val."'")['bid_counter'];
//Check to remove auto allocation
// if($bid_counter>0 || $check_on_hold=='Yes'){
if($bid_counter==0 || ($bid_counter>0 || $check_on_hold=='Yes')){
    $get_msg_db=$obj->read_specific('message','auto_replies','id=8');
    $replaced_msg=str_replace("NUMBER",$bid_counter+2,$get_msg_db['message']);
    $json->msg=$replaced_msg;
    $json->status='5';
    $edit_id= $obj->get_id('bid');
    $obj->editFun('bid',$edit_id,'job',$check_id);
    $obj->editFun('bid',$edit_id,'tabName',$val);
    $obj->editFun('bid',$edit_id,'allocated','0');
    $obj->editFun('bid',$edit_id,'interpreter_id',$_POST['ap_user_id']);
}else{
    //echo 'more jobs = '.$more_jobs;exit;
if($allow_gender==1 && $check_ability=='Yes' && $row_count_amend['amend_counts'] <= 2 && $check_jobdDisp=='1' && $check_jobStatus=='1' && $allot=='yes' && $check_black['id']=='' && $check_lang['counter']!='0' && $check_on_hold=='No' && ($row_feedback['result']>=40 || is_null($row_feedback['result'])) && $empty_doc=='No' && $activeness=='Yes' &&  $more_jobs==0){
    // if($allow_gender==1 && $check_ability=='Yes' && $row_count_amend['amend_counts'] <= 2 && $check_jobdDisp=='1' && $check_jobStatus=='1' && $allot=='yes' && $check_black['id']=='' && $check_lang['counter']!='0' && $check_on_hold=='No' && ($row_feedback['result']>=40 || is_null($row_feedback['result'])) && $empty_doc=='No' && $activeness=='Yes'){
    $edit_id= $obj->get_id('bid'); 
    $obj->editFun('bid',$edit_id,'job',$check_id);
    $obj->editFun('bid',$edit_id,'tabName',$val);
    $obj->editFun('bid',$edit_id,'allocated','1');
    $obj->editFun('bid',$edit_id,'interpreter_id',$_POST['ap_user_id']);
    $obj->editFun($val,$check_id,'intrpName',$_POST['ap_user_id']);
    $auto_allocated="Auto Allocated";
    $auto_date=date("Y-m-d");
    $obj->editFun($val,$check_id,'pay_int','1');
    $obj->editFun($val,$check_id,'aloct_by',$auto_allocated);
    $obj->editFun($val,$check_id,'aloct_date',$auto_date);
    $obj->editFun($val,$check_id,'intrpName',$_POST['ap_user_id']);
    $get_msg_db=$obj->read_specific('message','auto_replies','id=5');
    $json->msg=$get_msg_db['message'];
    $json->status='4';
    $get_removals=$obj->read_all("*","app_notifications","title LIKE '%".$check_id."%' and type_key='nj' AND LOCATE('".$find_string."',title)>0");
    if($get_removals->num_rows>0){
        while($row_removals=$get_removals->fetch_assoc()){
          //Update notification counter on APP
          $check_int_id=$obj->read_specific('id','notify_new_doc','interpreter_id='.$row_removals['int_ids'])['id'];
          if(!empty($check_int_id) && $check_int_id>0){
              $existing_notification=$obj->read_specific("new_notification","notify_new_doc","interpreter_id=".$row_removals['int_ids'])['new_notification'];
              $obj->update('notify_new_doc',array("new_notification"=>$existing_notification-1),"interpreter_id=".$row_removals['int_ids']);
          }
          $obj->delete("app_notifications","id=".$row_removals['id']);
        }
    }
    // Send assignment emails code here
    if(isset($_POST['ap_user_id'])){
$mail = new PHPMailer(true);
$table=$val;
$assign_id=$check_id;
$id=$_POST['ap_user_id'];
$email=$get_data['email'];
$int_name=$get_data['name'];
  //get job id details and company        
  $row = $obj->read_specific("$table.*,comp_reg.name as orgzName","$table,comp_reg","$table.orgName=comp_reg.abrv AND $table.id=".$assign_id);
  $source=$row['source'];
  $aloct_by=$row['aloct_by'];
  $target=$row['target'];
  $orgRef=$row['orgRef'];
  $inchEmail=$row['inchEmail'];
  $inchEmail2=$row['inchEmail2'];
  $orgContact=$row['orgContact'];
  $I_Comments=$row['I_Comments'];
  $bookinType=$row['bookinType'];
  $nameRef=$row['nameRef'];
  if($table=='interpreter' || $table=='telephone'){
    $from_add = "info@lsuk.org"; 
    $gender =$row['gender'];
    $inchNo=$row['inchNo'];
    $line1=$row['line1'];
    $inchRoad=$row['inchRoad'];
    $inchCity=$row['inchCity'];
    $inchPcode=$row['inchPcode'];
    $assignDate=$misc->dated($row['assignDate']); 
    $assignTime=substr($row['assignTime'],0,5);
    $db_assignDur=$row['assignDur'];
    if($db_assignDur>60){
        $hours=$db_assignDur / 60;
        if(floor($hours)>1){$hr="hours";}else{$hr="hour";}
        $mins=$db_assignDur % 60;
        if($mins==00){
            $assignDur=sprintf("%2d $hr",$hours);  
        }else{
            $assignDur=sprintf("%2d $hr %02d minutes",$hours,$mins);  
        }
    }else if($db_assignDur==60){
        $assignDur="1 Hour";
    }else{
        $assignDur=$db_assignDur." minutes";
    }
    $orgzName=$row['orgzName'];
    if($table=='interpreter'){
      $dbs_checked=$row['dbs_checked'];
      if($dbs_checked==0){
        $dbs_checked='Yes';
      }else{
        $dbs_checked='No';
      }
      $buildingName=$row['buildingName'];
      $street=$row['street'];
      $assignCity=$row['assignCity'];
      $postCode=$row['postCode'];}
      $assignIssue=$row['assignIssue'];
      $inchPerson=$row['inchPerson'];
      $remrks=$row['remrks'];
      if($table=='telephone'){
        $comunic=$obj->read_specific("c_title","comunic_types","c_id=".$row['comunic'])['c_title'];
        $ClientContact=$row['contactNo'];
        $noClient=$row['noClient'];
      }
    }
  
  if($table=='translation')
  {
    $asignDate=$row['asignDate'];
    $deliveryType=$row['deliveryType'];
    $transType=$row['transType'];
    $deliverDate=$row['deliverDate'];
    $docType=$row['docType'];
    $trans_detail=$row['trans_detail'];
  }


  //to inchEmail,inchEmail2 (client #1): translation
  if($table=='translation'){
$row_format_ack = $obj->read_specific("em_format","email_format","id=6");
//Get format from database
$ack_body = $row_format_ack['em_format'];
$to_add = $inchEmail;
$subject = "Confirmation of ".$source." translation project Reference ID " . $row['reference_no'] . " requested on ".$misc->dated($asignDate);
$append_table = "<table>
<tr>
<td style='border: 1px solid yellowgreen;padding:5px;'>Project Reference Number</td>
<td style='border: 1px solid yellowgreen;padding:5px;'>" . $nameRef . "</td>
</tr>
<tr>
<td style='border: 1px solid yellowgreen;padding:5px;'>Booking Reference Number</td>
<td style='border: 1px solid yellowgreen;padding:5px;'>".$orgRef."</td>
</tr>
<tr>
<td style='border: 1px solid yellowgreen;padding:5px;'>Source Language</td>
<td style='border: 1px solid yellowgreen;padding:5px;'>".$source."</td>
</tr>
<tr>
<td style='border: 1px solid yellowgreen;padding:5px;'>Target Language</td>
<td style='border: 1px solid yellowgreen;padding:5px;'>".$target."</td>
</tr>
<tr>
<td style='border: 1px solid yellowgreen;padding:5px;'>Document Type</td>
<td style='border: 1px solid yellowgreen;padding:5px;'>".$obj->read_specific("tc_title","trans_cat","tc_id=".$docType)['tc_title']."</td>
</tr>
<tr>
<td style='border: 1px solid yellowgreen;padding:5px;'>Translation Category</td>
<td style='border: 1px solid yellowgreen;padding:5px;'>".$obj->read_specific("GROUP_CONCAT(CONCAT(td_title)  SEPARATOR '<br>') as td_title","trans_dropdown","td_id IN (".$transType.")")['td_title']."</td>
</tr>
<tr>
<td style='border: 1px solid yellowgreen;padding:5px;'>Translation Type(s)</td>
<td style='border: 1px solid yellowgreen;padding:5px;'>".$obj->read_specific("GROUP_CONCAT(CONCAT(tt_title)  SEPARATOR '<br>') as tt_title","trans_types","tt_id IN (".$trans_detail.")")['tt_title']."</td>
</tr>
<tr>
<td style='border: 1px solid yellowgreen;padding:5px;'>Delivery Type</td>
<td style='border: 1px solid yellowgreen;padding:5px;'>".$deliveryType."</td>
</tr>
<tr>
<td style='border: 1px solid yellowgreen;padding:5px;'>Delivery Date</td>
<td style='border: 1px solid yellowgreen;padding:5px;'>".$misc->dated($deliverDate)."</td>
</tr>
<tr>
<td style='border: 1px solid yellowgreen;padding:5px;'>Notes (if any) </td>
<td style='border: 1px solid yellowgreen;padding:5px;color:red;'>".$I_Comments."</td>
</tr>
</table>";
$data   = ["[ORGCONTCAT]", "[APPENDTABLE]"];
$to_replace  = ["$orgContact", "$append_table"];
$message=str_replace($data, $to_replace,$ack_body);
//to inchEmail (client #1)
try {
    $mail->SMTPDebug = 0;
    //$mail->isSMTP(); 
    //$mailer->Host = 'smtp.office365.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'info@lsuk.org';
    $mail->Password   = 'LangServ786';
    $mail->SMTPSecure = 'tls';
    $mail->Port       = 587;
    $mail->setFrom($from_add, 'LSUK');
    $mail->addAddress($to_add);
    $mail->addReplyTo($from_add, 'LSUK');
    $mail->isHTML(true);
    $mail->Subject = $subject;
    $mail->Body    = $message;
    if($mail->send()){
        $mail->ClearAllRecipients();
        $mail->addAddress('imran.lsukltd@gmail.com');
        $mail->addReplyTo($from_add, 'LSUK');
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $message;
        $mail->send();
        $mail->ClearAllRecipients();
            if ($inchEmail2!=""){
            $mail->addAddress($inchEmail2);
            $mail->addReplyTo($from_add, 'LSUK');
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = $message;
            $mail->send();
            $mail->ClearAllRecipients();
            }
        //echo "sent";
    }else{
        //echo "not_sent";
    }
} catch (Exception $e) {
    //echo "mail_failed";
}
}

  //to inchEmail,inchEmail2 (client #2): interpreter
  if($table=='interpreter'){
$row_format_ack = $obj->read_specific("em_format","email_format","id=4");
//Get format from database
$ack_body = $row_format_ack['em_format'];
$to_add = $inchEmail;
$subject = "Confirmation of ".$source." Face To Face interpreting project Reference ID " . $row['reference_no'] . " requested on ".$assignDate." at ".$assignTime;
$append_table = "<table>
<tr>
<td style='border: 1px solid yellowgreen;padding:5px;'>Project Reference Number</td>
<td style='border: 1px solid yellowgreen;padding:5px;'>" . $nameRef . "</td>
</tr>
<tr>
<td style='border: 1px solid yellowgreen;padding:5px;'>Assignment Type</td>
<td style='border: 1px solid yellowgreen;padding:5px;'>Face to Face Interpreting Assignment</td>
</tr>
<tr>
<td style='border: 1px solid yellowgreen;padding:5px;'>Case Name or File Reference Number (if any)</td>
<td style='border: 1px solid yellowgreen;padding:5px;'>".$orgRef."</td>
</tr>
<tr>
<td style='border: 1px solid yellowgreen;padding:5px;'>Source Language</td>
<td style='border: 1px solid yellowgreen;padding:5px;'>".$source."</td>
</tr>
<tr>
<td style='border: 1px solid yellowgreen;padding:5px;'>Target Language</td>
<td style='border: 1px solid yellowgreen;padding:5px;'>".$target."</td>
</tr>
<tr>
<td style='border: 1px solid yellowgreen;padding:5px;'>Assignment Date</td>
<td style='border: 1px solid yellowgreen;padding:5px;'>".$assignDate."</td>
</tr>
<tr>
<td style='border: 1px solid yellowgreen;padding:5px;'>Assignment Time</td>
<td style='border: 1px solid yellowgreen;padding:5px;'>".$assignTime."</td>
</tr>
<tr>
<td style='border: 1px solid yellowgreen;padding:5px;'>DBS Interpreter Required ?</td>
<td style='border: 1px solid yellowgreen;padding:5px;'>".$dbs_checked."</td>
</tr>
<tr>
<td style='border: 1px solid yellowgreen;padding:5px;'>Assignment Duration</td>
<td style='border: 1px solid yellowgreen;padding:5px;'>".$assignDur."</td>
</tr>
<tr>
<td style='border: 1px solid yellowgreen;padding:5px;'>Assignment Location</td>
<td style='border: 1px solid yellowgreen;padding:5px;'>".$buildingName.' '.$street.' '.$assignCity.' '.$postCode."</td>
</tr>
<tr>
<td style='border: 1px solid yellowgreen;padding:5px;'>Interpreter Name</td>
<td style='border: 1px solid yellowgreen;padding:5px;'>".$int_name."</td>
</tr>
<tr>
<td style='border: 1px solid yellowgreen;padding:5px;'>Interpreter Gender</td>
<td style='border: 1px solid yellowgreen;padding:5px;'>".$gender."</td>
</tr>
<tr>
<td style='border: 1px solid yellowgreen;padding:5px;'>Interpreter Contact</td>
<td style='border: 1px solid yellowgreen;padding:5px;'>".$orgContact."</td>
</tr>
<tr>
<td style='border: 1px solid yellowgreen;padding:5px;'>Booking Requested By</td>
<td style='border: 1px solid yellowgreen;padding:5px;'>".$inchPerson."</td>
</tr>
<tr>
<td style='border: 1px solid yellowgreen;padding:5px;'>Booking Type</td>
<td style='border: 1px solid yellowgreen;padding:5px;'>".$bookinType."</td>
</tr>
<tr>
<td style='border: 1px solid yellowgreen;padding:5px;'>Notes (if any)</td>
<td style='border: 1px solid yellowgreen;padding:5px;color:red;'>".$I_Comments."</td>
</tr>
</table>";
$data   = ["[INCHPERSON]", "[APPENDTABLE]"];
$to_replace  = ["$inchPerson", "$append_table"];
$message=str_replace($data, $to_replace,$ack_body);
//to inchEmail (client #2)
//php mailer used at top
try {
    $mail->SMTPDebug = 0;
    //$mail->isSMTP(); 
    //$mailer->Host = 'smtp.office365.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'info@lsuk.org';
    $mail->Password   = 'LangServ786';
    $mail->SMTPSecure = 'tls';
    $mail->Port       = 587;
    $mail->setFrom($from_add, 'LSUK');
    $mail->addAddress($to_add);
    $mail->addReplyTo($from_add, 'LSUK');
    $mail->isHTML(true);
    $mail->Subject = $subject;
    $mail->Body    = $message;
    if($mail->send()){
        $mail->ClearAllRecipients();
        $mail->addAddress('imran.lsukltd@gmail.com');
        $mail->addReplyTo($from_add, 'LSUK');
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $message;
        $mail->send();
        $mail->ClearAllRecipients();
            if ($inchEmail2!=""){
            $mail->addAddress($inchEmail2);
            $mail->addReplyTo($from_add, 'LSUK');
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = $message;
            $mail->send();
            $mail->ClearAllRecipients();
            }
        //echo "sent";
    }else{
        //echo "not_sent";
    }
} catch (Exception $e) {
    //echo "mail_failed";
}
}

  //to inchEmail,inchEmail2 (client #3): telephone
  if($table=='telephone'){
    $row_format_ack = $obj->read_specific("em_format","email_format","id=5");
    //Get format from database
    $ack_body = $row_format_ack['em_format'];
    $to_add = $inchEmail;
    $subject = "Confirmation of ".$source." telephone interpreting project Reference ID " . $row['reference_no'] . " requested on ".$assignDate." at ".$assignTime;
    $append_table = "<table>
<tr>
<td style='border: 1px solid yellowgreen;padding:5px;'>Project Reference Number</td>
<td style='border: 1px solid yellowgreen;padding:5px;'>" . $nameRef . "</td>
</tr>
    <tr>
    <td style='border: 1px solid yellowgreen;padding:5px;'>Assignment Type</td>
    <td style='border: 1px solid yellowgreen;padding:5px;'>".$comunic."</td>
    </tr>
    <tr>
    <td style='border: 1px solid yellowgreen;padding:5px;'>Case Name or Reference Number (if any)</td>
    <td style='border: 1px solid yellowgreen;padding:5px;'>".$orgRef."</td>
    </tr>
    <tr>
    <td style='border: 1px solid yellowgreen;padding:5px;'>Source Language</td>
    <td style='border: 1px solid yellowgreen;padding:5px;'>".$source."</td>
    </tr>
    <td style='border: 1px solid yellowgreen;padding:5px;'>Target Language</td>
    <td style='border: 1px solid yellowgreen;padding:5px;'>".$target."</td>
    </tr>
    <tr>
    <td style='border: 1px solid yellowgreen;padding:5px;'>Assignment Date</td>
    <td style='border: 1px solid yellowgreen;padding:5px;'>".$assignDate."</td>
    </tr>
    <tr>
    <td style='border: 1px solid yellowgreen;padding:5px;'>Assignment Time</td>
    <td style='border: 1px solid yellowgreen;padding:5px;'>".$assignTime."</td>
    </tr>
    <tr>
    <td style='border: 1px solid yellowgreen;padding:5px;'>Assignment Duration</td>
    <td style='border: 1px solid yellowgreen;padding:5px;'>".$assignDur."</td>
    </tr>
    <tr>
    <td style='border: 1px solid yellowgreen;padding:5px;'>Booking Requested by</td>
    <td style='border: 1px solid yellowgreen;padding:5px;'>".$inchPerson."</td>
    </tr>
    <tr>
    <td style='border: 1px solid yellowgreen;padding:5px;'>Interpreter Contact</td>
    <td style='border: 1px solid yellowgreen;padding:5px;'>".$orgContact."</td>
    </tr>
    <tr>
    <td style='border: 1px solid yellowgreen;padding:5px;'>Service user Contact Number</td>
    <td style='border: 1px solid yellowgreen;padding:5px;'>".$ClientContact."</td>
    </tr>
    <tr>
    <td style='border: 1px solid yellowgreen;padding:5px;'>Client Contact Number</td>
    <td style='border: 1px solid yellowgreen;padding:5px;'>".$noClient."</td>
    </tr>
    <tr>
    <td style='border: 1px solid yellowgreen;padding:5px;'>Interpreter Name</td>
    <td style='border: 1px solid yellowgreen;padding:5px;'>".$int_name."</td>
    </tr>
    <tr>
    <td style='border: 1px solid yellowgreen;padding:5px;'>Interpreter Gender Requested</td>
    <td style='border: 1px solid yellowgreen;padding:5px;'>".$gender."</td>
    </tr>
    <tr>
    <td style='border: 1px solid yellowgreen;padding:5px;'>Booking Type</td>
    <td style='border: 1px solid yellowgreen;padding:5px;'>".$bookinType."</td>
    </tr>
    <tr>
    <td style='border: 1px solid yellowgreen;padding:5px;'>Notes (if any)</td>
    <td style='border: 1px solid yellowgreen;padding:5px;color:red;'>".$I_Comments."</td>
    </tr>
    </table>";
$data   = ["[ORGCONTCAT]", "[APPENDTABLE]"];
$to_replace  = ["$orgContact", "$append_table"];
$message=str_replace($data, $to_replace,$ack_body);
    //to client #3
        //php mailer used at top
        try {
            $mail->SMTPDebug = 0;
            //$mail->isSMTP(); 
            //$mailer->Host = 'smtp.office365.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'info@lsuk.org';
            $mail->Password   = 'LangServ786';
            $mail->SMTPSecure = 'tls';
            $mail->Port       = 587;
            $mail->setFrom($from_add, 'LSUK');
            $mail->addAddress($to_add);
            $mail->addReplyTo($from_add, 'LSUK');
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = $message;
            if($mail->send()){
                $mail->ClearAllRecipients();
                $mail->addAddress('imran.lsukltd@gmail.com');
                $mail->addReplyTo($from_add, 'LSUK');
                $mail->isHTML(true);
                $mail->Subject = $subject;
                $mail->Body    = $message;
                $mail->send();
                $mail->ClearAllRecipients();
                    if ($inchEmail2!=""){
                    $mail->addAddress($inchEmail2);
                    $mail->addReplyTo($from_add, 'LSUK');
                    $mail->isHTML(true);
                    $mail->Subject = $subject;
                    $mail->Body    = $message;
                    $mail->send();
                    $mail->ClearAllRecipients();
                    }
                //echo "sent";
            }else{
                //echo "not_sent";
            }
        } catch (Exception $e) {
            //echo "mail_failed";
        }
    }
}
    }else{
            $edit_id= $obj->get_id('bid');
            $obj->editFun('bid',$edit_id,'job',$check_id);
            $obj->editFun('bid',$edit_id,'tabName',$val);
            $obj->editFun('bid',$edit_id,'allocated','0');
            $obj->editFun('bid',$edit_id,'interpreter_id',$_POST['ap_user_id']);
            $get_msg_db=$obj->read_specific('message','auto_replies','id=6');
            $json->msg=$get_msg_db['message'].$msg_cont_office;
            $json->status='5';
         }
      }
   }
 }
    }else{
             $json->msg="not_logged_in";
    }
    header('Content-Type: application/json');
    echo json_encode($json, JSON_UNESCAPED_UNICODE);
} 
//View subcription
if(isset($_POST['view_subscription'])){
    $json=(object) null;
    if(isset($_POST['ap_user_id']) && !empty($_POST['ap_user_id'])){
        $row=$obj->read_specific("subscribe","interpreter_reg","id=".$_POST['ap_user_id']);
        $json->status=$row['subscribe'];
    }else{
        $json->msg="not_logged_in";
    }
    header('Content-Type: application/json');
    echo json_encode($json, JSON_UNESCAPED_UNICODE);
}
//Update subcribe for bidding
if(isset($_POST['ap_subscribe'])){
    $json=(object) null;
    if(isset($_POST['ap_user_id']) && !empty($_POST['ap_user_id'])){
        $subscribe=$_POST['ap_subscribe'];
        $upd=$obj->update("interpreter_reg",array("subscribe"=>$subscribe),"id=".$_POST['ap_user_id']);
        if($upd && $_POST['ap_subscribe']==0){
            $json->status="0";
            $json->msg="You have been unsubscribed from jobs notifications!";
        }else{
            $json->status="1";
            $json->msg="You have successfully subscribed to jobs notifications.";
        }
    }else{
        $json->msg="not_logged_in";
    }
    header('Content-Type: application/json');
    echo json_encode($json, JSON_UNESCAPED_UNICODE);
}
if(isset($_POST['ap_logout'])){
    $json=(object) null;
    $json->msg="logged_out";
    header('Content-Type: application/json');
    echo json_encode($json, JSON_UNESCAPED_UNICODE);
}
if(isset($_POST['home_notification'])){
    $json=(object) null;
    if(isset($_POST['ap_user_id']) && !empty($_POST['ap_user_id'])){
        $json->new_notification=$obj->read_specific("new_notification","notify_new_doc","interpreter_id=".$_POST['ap_user_id'])['new_notification'];
        /*if($noty==1){
            $json->new_notification=0;
        }else{
            $json->new_notification=1;
        }*/
    }else{
        $json->msg="not_logged_in";
    }
    header('Content-Type: application/json');
    echo json_encode($json, JSON_UNESCAPED_UNICODE);
}
if(isset($_POST['view_notifications'])){
$json=(object) null;
if(isset($_POST['ap_user_id']) && !empty($_POST['ap_user_id'])){
    
    // $existing_notification=$obj->read_specific("new_notification","notify_new_doc","interpreter_id=".$_POST['ap_user_id'])['new_notification'];
    // if($existing_notification>0){
    //     $obj->update("notify_new_doc",array("new_notification"=>$existing_notification-1),"interpreter_id=".$_POST['ap_user_id']);
    // }
    $query=$obj->read_all("*","app_notifications","1 ORDER BY id DESC");
    $json=array();
    while($row = $query->fetch_assoc()){
        $int_ids=explode(',',$row['int_ids']);
        $read_ids=explode(',',$row['read_ids']);
        if(in_array($_POST['ap_user_id'],$int_ids)){
            if(!in_array($_POST['ap_user_id'],$read_ids)){
                $row['read']='1';
            }else{
                $row['read']='0';
            }
        unset($row['int_ids']);
        unset($row['read_ids']);
        array_push($json,$row);
        }
    }
}else{
    $json->msg="not_logged_in";
}
header('Content-Type: application/json');
echo json_encode($json, JSON_UNESCAPED_UNICODE);
}
if(isset($_POST['notification_id'])){
    if(isset($_POST['ap_user_id']) && !empty($_POST['ap_user_id'])){
        $still_remove=0;
        $get_data=$obj->read_specific("*","app_notifications","id=".$_POST['notification_id']);
        if($get_data['type_key']=="nj" || $get_data['type_key']=="ja" || $get_data['type_key']=="jc"){
            $obj->delete("app_notifications","id=".$_POST['notification_id']);
        }else{
            $read_ids=explode(',',$get_data['read_ids']);
            if(in_array($_POST['ap_user_id'],$read_ids)){
                $still_remove=1;
                $new_read_ids = implode(',',array_diff($read_ids, [$_POST['ap_user_id']]));
                $obj->update("app_notifications",array("read_ids"=>$new_read_ids),"id=".$_POST['notification_id']);
            }
        }
        $existing_notification=$obj->read_specific("new_notification","notify_new_doc","interpreter_id=".$_POST['ap_user_id'])['new_notification'];
        if($existing_notification>0){
          if((!empty($get_data['read_ids']) && $get_data['type_key']!="nd" && $get_data['type_key']!="md") || $still_remove==1){
            $obj->update("notify_new_doc",array("new_notification"=>$existing_notification-1),"interpreter_id=".$_POST['ap_user_id']);
          }
        }
    }else{
        $json->msg="not_logged_in";
    }
}
if(isset($_POST['missing_documents'])){
    $json=(object) null;
    if(isset($_POST['ap_user_id']) && !empty($_POST['ap_user_id'])){
        $get_docs=$obj->read_specific("CONCAT(CASE WHEN (applicationForm='') THEN 'applicationForm,' ELSE '' END ,CASE WHEN (agreement='') THEN 'agreement,' ELSE '' END,CASE WHEN (dbs_file='') THEN 'dbs,' ELSE '' END,CASE WHEN (id_doc_file='') THEN 'id_doc,' ELSE '' END,CASE WHEN (acNo='') THEN 'bank_details,' ELSE '' END ) as missed","interpreter_reg","id=".$_POST['ap_user_id'])['missed'];
        $array=explode(',',$get_docs);
        $items_array=array();
        foreach($array as $item){
            if(!empty($item)){
                array_push($items_array,$item);
            }
        }
        $json=$items_array;
    }else{
        $json->msg="not_logged_in";
    }
    header('Content-Type: application/json');
    echo json_encode($json, JSON_UNESCAPED_UNICODE);
}
if(isset($_POST['update_application'])){
    $json=(object) null;
    $table = 'interpreter_reg';
    echo 'here';
    if(isset($_POST['ap_user_id']) && !empty($_POST['ap_user_id'])){
        //check if uk citizen
        if(isset($_POST['isUkCitizen']) && $_POST['isUkCitizen'] == 1){
            $ap_user_id = $_POST['ap_user_id'];
            //remove previous passport file
            $old_file_column=$obj->read_specific("id_doc_file","$table","id=".$ap_user_id)["$column_name"];
            echo $old_file_column;
            // $old_file="../lsuk_system/file_folder/$document_name/".$old_file_column;
            // if(file_exists($old_file) && !empty($old_file)){
            //     unlink($old_file);
            // }
        }

    }else{
        $json->msg="not_logged_in";
    }
    header('Content-Type: application/json');
    echo json_encode($json, JSON_UNESCAPED_UNICODE);
}
if(isset($_POST['add_missing_document'])){
    $json=(object) null;
    if(isset($_POST['ap_user_id']) && !empty($_POST['ap_user_id'])){
        $table="interpreter_reg";
        $ap_user_id=$_POST['ap_user_id'];
        $document_name=$_POST['document_name'];
        $column_name=$document_name.'_file';
        if($document_name=='applicationForm' || $document_name=='agreement'){
            $label=$document_name=="applicationForm"?"Application Form":"Agreement Form";
            $file_base64 = base64_decode($_POST['file']);
            $file_type = '.'.$_POST['file_type'];
            $old_file_column=$obj->read_specific("$column_name","$table","id=".$ap_user_id)["$column_name"];
            $old_file="../lsuk_system/file_folder/$document_name/".$old_file_column;
            if(file_exists($old_file) && !empty($old_file)){
                unlink($old_file);
            }
            $new_file_name = round(microtime(true)).$file_type;
            if(file_put_contents("../lsuk_system/file_folder/$document_name/".$new_file_name, $file_base64)){
                $obj->editFun("$table",$ap_user_id,"$column_name",$new_file_name);
                $obj->editFun($table,$ap_user_id,$document_name,"Soft Copy");
                $json->status="1";
                $json->msg="Your ".$label." has been added successfully. Thank you";
            }else{
                $json->status="0";
                $json->msg="Failed to add your ".$label.". Try again !";
            }
        }else if($document_name=='bank_details'){
            /*if(isset($_POST['bank_name'])){
                $obj->editFun("$table",$ap_user_id,'bnakName',$_POST['bank_name']);
            }
            if(isset($_POST['account_name'])){
                $obj->editFun("$table",$ap_user_id,'acName',$_POST['account_name']);
            }
            if(isset($_POST['sort_code'])){
                $obj->editFun("$table",$ap_user_id,'acntCode',$_POST['sort_code']);
            }
            if(isset($_POST['account_number'])){
                $obj->editFun("$table",$ap_user_id,'acNo',$_POST['account_number']);
                $json->msg="success";
            }else{
                $json->msg="failed";
            }*/
            $int_name=$obj->read_specific("name","$table","id=".$ap_user_id)["name"];
            $mail = new PHPMailer(true);
            try {
                $from_add="info@lsuk.org";$from_name="LSUK";
                $mail->SMTPDebug = 0;
                //$mail->isSMTP(); 
                //$mailer->Host = 'smtp.office365.com';
                $mail->SMTPAuth   = true;
                $mail->Username   = 'info@lsuk.org';
                $mail->Password   = 'LangServ786';
                $mail->SMTPSecure = 'tls';
                $mail->Port       = 587;
                $mail->setFrom($from_add, $from_name);
                //$mail->addAddress('waqarecp1992@gmail.com');
                $mail->addAddress('payroll@lsuk.org');
                $mail->addReplyTo($from_add, $from_name);
                $mail->isHTML(true);
                $mail->Subject = $int_name.' requested for bank details';
                $mail->Body    = "Dear Admin,<br><b>".$int_name."</b> has requested for updating bank info.<br>
                Kindly update from Admin Portal.<br>
                <u>Updates Requested:</u><br>
                <table>
                <tr>
                <td style='border: 1px solid yellowgreen;padding:5px;'>Bank Name</td>
                <td style='border: 1px solid yellowgreen;padding:5px;'>".$_POST['bank_name']."</td>
                </tr>
                <tr>
                <td style='border: 1px solid yellowgreen;padding:5px;'>Account Title</td>
                <td style='border: 1px solid yellowgreen;padding:5px;'>".$_POST['account_name']."</td>
                </tr>
                <tr>
                <td style='border: 1px solid yellowgreen;padding:5px;'>Sort Code</td>
                <td style='border: 1px solid yellowgreen;padding:5px;'>".$_POST['sort_code']."</td>
                </tr>
                <tr>
                <td style='border: 1px solid yellowgreen;padding:5px;'>Account Number</td>
                <td style='border: 1px solid yellowgreen;padding:5px;'>".$_POST['account_number']."</td>
                </tr>
                </table>";
                if($mail->send()){
                    $mail->ClearAllRecipients();
                    $json->status="1";
                    $json->msg="Request has been sent for your bank details. Thank you";
                }else{
                    $json->status="0";
                    $json->msg="Failed to send request for your bank details. Try again!";
                }
            } catch (Exception $e) {
                //echo "mail_failed";
            }
        }else{
            $col_file_name=$document_name.'_file';
            $col_no_name=$document_name.'_no';
            $col_issue_name=$document_name.'_issue_date';
            $col_expiry_name=$document_name.'_expiry_date';
            $document_number=$_POST['document_number'];
            $obj->editFun($table,$ap_user_id,$col_no_name,$document_number);
            $issue_date=$_POST['issue_date'];
            $obj->editFun($table,$ap_user_id,$col_issue_name,$issue_date);
            $expiry_date=$_POST['expiry_date'];
            $obj->editFun($table,$ap_user_id,$col_expiry_name,$expiry_date);
            if($document_name=="dbs" || $document_name=="id_doc"){
                if($document_name=="dbs"){
                    $col_file_name=="crbDbs";
                    $label="DBS Document";
                }
                if($document_name=="id_doc"){
                    $col_file_name=="identityDocument";
                    $label="Identity Document";
                }
                $obj->editFun($table,$ap_user_id,$col_file_name,"Soft Copy");
            }
            $file_base64 = base64_decode($_POST['file']);
            $file_type = '.'.$_POST['file_type'];
            $old_file_column=$obj->read_specific("$column_name","$table","id=".$ap_user_id)["$column_name"];
            $old_file="../lsuk_system/file_folder/issue_expiry_docs/".$old_file_column;
            if(file_exists($old_file) && !empty($old_file)){
                unlink($old_file);
            }
            $new_file_name = round(microtime(true)).$file_type;
            if(file_put_contents("../lsuk_system/file_folder/issue_expiry_docs/".$new_file_name, $file_base64)){
                $obj->editFun("$table",$ap_user_id,"$column_name",$new_file_name);
                $json->status="1";
                $json->msg="Your ".$label." has been added successfully. Thank you";
            }else{
                $json->status="0";
                $json->msg="Failed to add your ".$label.". Try again !";
            }
        }
    }else{
        $json->status="0";
        $json->msg="You must login to perform this action !";
    }
    header('Content-Type: application/json');
    echo json_encode($json, JSON_UNESCAPED_UNICODE);
}
if(isset($_POST['view_policy'])){
    $json=(object) null;
    if(isset($_POST['policy_id']) && !empty($_POST['policy_id'])){
        $json->policy=$obj->read_specific("html","timesheet_policy","id=".$_POST['policy_id'])['html'];
    }else{
        $json->msg="no_policy_selected";
    }
    header('Content-Type: application/json');
    echo json_encode($json, JSON_UNESCAPED_UNICODE);
}
if(isset($_POST['get_links'])){
    $json=(object) null;
    $json->privacy_policy="https://lsuk.org/lsuk_system/file_folder/lsuk_files/privacy policy 2021.pdf";
    $json->terms_conditions="https://lsuk.org/lsuk_system/file_folder/lsuk_files/Conditions for Linguists.pdf";
    header('Content-Type: application/json');
    echo json_encode($json, JSON_UNESCAPED_UNICODE);
}
if(isset($_POST['test_api'])){
    $json=(object) null;
    $json->result=1;
    header('Content-Type: application/json');
    echo json_encode($json, JSON_UNESCAPED_UNICODE);
}
//Create test device notification
if(isset($_POST['device_notification'])){
    
    $token=$_POST['token'];
    $json=(object) null;
    $json->sent=0;$notification_id=135;
    $subject="Test notification from LSUK IT";
    $sub_title="Greetings! You have got the test notification from LSUK.";
    if(!empty($token)){
        $obj->notify($token,$subject,$sub_title,array("type_key"=>"ja","notification_id"=>$notification_id,"hashCode"=>123456));
        $json->sent=1;
    }
    header('Content-Type: application/json');
    echo json_encode($json, JSON_UNESCAPED_UNICODE);
}
//Create test notification to all devices for 1 interpreter
if(isset($_POST['test_notification'])){
    $json=(object) null;
    $json->sent=0;
    $type_key=$_POST['type_key'];
    $job_type=$_POST['job_type'];
    $notification_id=135;
    $subject="New ".$job_type." Interpreting Project 9545";
    $subject2="Mark your availability for Thursday";
    $sub_title=$job_type." Interpreting job of Arabic language on 12-11-2021 at 12:45:00 is available for you to bid.";
    $array_tokens=explode(',',$obj->read_specific("GROUP_CONCAT( DISTINCT token) as tokens","int_tokens","int_id=874")['tokens']);
    $obj->update("notify_new_doc",array("new_notification"=>1),"interpreter_id=874");
    $availability_note="Good morning!\nCan you mark your presence for the day. This doesn't guarantee a job but makes easy for LSUK to allocate a job.\nThank you";
    if(!empty($array_tokens)){
        foreach($array_tokens as $token){
            if(!empty($token)){
                $obj->notify($token,"🔔 ".$subject2,$availability_note,array("type_key"=>$type_key,"job_type"=>$job_type,"user_id"=>996));
                $json->sent=1;
            }
        }
    }
    header('Content-Type: application/json');
    echo json_encode($json, JSON_UNESCAPED_UNICODE);
}
//Send a ticket
if(isset($_POST['new_ticket'])){
    $json=(object) null;
    if(isset($_POST['ap_user_id']) && !empty($_POST['ap_user_id'])){
        if($obj->insert("tickets",array("interpreter_id"=>$_POST['ap_user_id'],"title"=>$_POST['title'],"details"=>$_POST['details']))){
            $json->msg="Your ticket has been sent. We will respond back soon.";
            $mail = new PHPMailer(true);
            try {
                $interpeter_email=$obj->read_specific("name,email","interpreter_reg","id=".$_POST['ap_user_id']);
                $from_add = "hr@lsuk.org";
                $from_name = "LSUK Admin Team";
                $mail->SMTPDebug = 1;
                //$mail->isSMTP(); 
                //$mailer->Host = 'smtp.office365.com';
                $mail->SMTPAuth   = true;
                $mail->Username   = 'info@lsuk.org';
                $mail->Password   = 'LangServ786';
                $mail->SMTPSecure = 'tls';
                $mail->Port       = 587;
                $mail->setFrom($from_add,$from_name);
                $mail->addAddress($interpeter_email["email"]);
                $mail->addReplyTo($from_add,$from_name);
                $mail->isHTML(true);
                $mail->Subject = "We have received your ticket.";
                $mail->Body = "Hello ".$interpeter_email["name"].",<br>
                We have received your ticket from LSUK App.<br>
                Title: ".$_POST['title']."<br>
                Details: ".$_POST['details']."<br>
                Submitted on: ".date('Y-m-d h:i:s')."<br>
                We will resolve your issue and will respond back soon.<br>
                You can find an update to your ticket status in your tickets screen.<br>
                Thank you.<br>
                Best regards,<br>
                LSUK";
                if($mail->send()){
                    $mail->ClearAllRecipients();
                    $mail->addAddress('hr@lsuk.org');
                    $mail->addReplyTo($interpeter_email["email"], $interpeter_email["name"]);
                    $mail->isHTML(true);
                    $mail->Subject = "New ticket from LSUK App";
                    $mail->Body    = "<b>".$interpeter_email["name"]."</b> has sumitted new ticket,<br>
                    Details are below:<br>
                    Title: ".$_POST['title']."<br>
                    Details: ".$_POST['details']."<br>
                    Submitted on: ".date('Y-m-d h:i:s')."<br>
                    Best regards,<br>
                    LSUK App";
                    $mail->send();
                    $mail->ClearAllRecipients();
                }else{
                    $json->msg="Failed to send email!";
                }
            } catch (Exception $e) {
                $json->mailer="Email Library Error!";
            }
        }else{
            $json->msg="Failed to submit your ticket! Try again later.";
        }
    }else{
        $json->msg="not_logged_in";
    }
    header('Content-Type: application/json');
    echo json_encode($json, JSON_UNESCAPED_UNICODE);
}
//Display all tickets
if(isset($_POST['view_tickets'])){
    $json=(object) null;
    if(isset($_POST['ap_user_id']) && !empty($_POST['ap_user_id'])){
        $query_tickets=$obj->read_all("title,dated,(CASE WHEN status=0 THEN 'Pending' ELSE 'Resolved' END) as status","tickets","interpreter_id=".$_POST['ap_user_id']);
        $json=array();
        while($row = $query_tickets->fetch_assoc()){
            array_push($json,$row);
        }
        if(count($json)==0){
            $json->msg="no_tickets";
        }
    }else{
        $json->msg="not_logged_in";
    }
    header('Content-Type: application/json');
    echo json_encode($json, JSON_UNESCAPED_UNICODE);
}
//Check if app update available
if(isset($_POST['app_update'])){
$json=(object) null;
$row=$obj->read_specific("android_update,ios_update","app_update","id=1");
$json->android_update=$row['android_update'];
$json->ios_update=$row['ios_update'];
header('Content-Type: application/json');
echo json_encode($json, JSON_UNESCAPED_UNICODE);
}
//Update app version
if(isset($_POST['version_update'])){
    $json=(object) null;
    $update_done=0;
    if(isset($_POST['android_version'])){
        $obj->update("app_update",array("android_update"=>$_POST['android_version']),"id=1");
        $update_done=1;
    }
    if(isset($_POST['ios_version'])){
        $obj->update("app_update",array("ios_update"=>$_POST['ios_version']),"id=1");
        $update_done=1;
    }
    if($update_done==1){
        $json->msg="success";
    }else{
        $json->msg="failed";
    }
    header('Content-Type: application/json');
    echo json_encode($json, JSON_UNESCAPED_UNICODE);
}
//Update availability today
if(isset($_POST['save_lat_long'])){
    $json=(object) null;
    $update_done=0;
    if(isset($_POST['job_id'])){
        $latitude=$_POST['latitude'];
        $longitude=$_POST['longitude'];
        $postcode_data=$latitude.",".$longitude;
        $update_array=array("postcode_data"=>$postcode_data);
        $obj->update("interpreter",$update_array,"id=".$_POST['job_id']);
        $update_done=1;
        if($update_done==1){
            $json->status="1";
            $json->msg="Job postcode data has been updated. Thank you";
        }else{
            $json->status="0";
            $json->msg="Failed to update job postcode data. Try again";
        }
    }else{
        $json->status="0";
        $json->msg="Job ID is required to perform this action!";
    }
    header('Content-Type: application/json');
    echo json_encode($json, JSON_UNESCAPED_UNICODE);
}
//Update job rating
if (isset($_POST['update_job_rating'])) {
    $json = (object) null;
    $done = 0;
    if(isset($_POST['job_id']) && isset($_POST['ap_user_id'])){
        $existing_id = $obj->read_specific("id", "job_ratings", "job_type=" . $_POST['job_type'] . " AND job_id=" . $_POST['job_id'])['id'];
        $data_array = array("rating_type" => $_POST['rating_type'], "interpreter_id" => $_POST['ap_user_id'], "job_type" => $_POST['job_type'], "job_id" => $_POST['job_id'], "created_date" => date("Y-m-d H:i:s"));
        if (empty($existing_id)) {
            $obj->insert("job_ratings", $data_array);
            $done = 1;
        } else {
            $obj->update("job_ratings", $data_array, "id=" . $existing_id);
            $done = 1;
        }
        if($done == 1){
            $json->status = "success";
            $json->msg = "Your feedback has been updated successfully. Thank you";
        }else{
            $json->status = "failed";
            $json->msg = "Failed to update your feedback. Please try again";
        }
    } else {
        $json->status = "failed";
        $json->msg = "Job ID and User ID is required to perform this action!";
    }
    header('Content-Type: application/json');
    echo json_encode($json, JSON_UNESCAPED_UNICODE);
}
