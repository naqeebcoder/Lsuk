<?php
include '../action.php';
if(isset($_POST['ap_login'])){
    $json=(object) null;
    $username=trim($_POST['ap_username']);
    $password=trim($_POST['ap_password']);
    if($username && $password){       
        $row = $obj->read_specific("interpreter_reg.id,interpreter_reg.interp,interpreter_reg.telep,interpreter_reg.trans, interpreter_reg.name, interpreter_reg.is_temp,interpreter_reg.interp_pix as photo,interpreter_reg.email,interpreter_reg.deleted_flag,interpreter_reg.id_doc_no,
        interpreter_reg.id_doc_file,interpreter_reg.id_doc_issue_date,interpreter_reg.id_doc_expiry_date,interpreter_reg.uk_citizen,
        interpreter_reg.work_evid_file,interpreter_reg.work_evid_issue_date,interpreter_reg.work_evid_expiry_date,(CASE 
        WHEN (interpreter_reg.actnow='Active' and (interpreter_reg.actnow_time='1001-01-01' AND interpreter_reg.actnow_to='1001-01-01') AND interpreter_reg.active='0') THEN 'ready'
        WHEN (interpreter_reg.actnow='Active' and (CURRENT_DATE() BETWEEN interpreter_reg.actnow_time AND interpreter_reg.actnow_to) AND interpreter_reg.active='0') THEN 'ready'
        WHEN (interpreter_reg.actnow='Inactive' and (interpreter_reg.actnow_time='1001-01-01' AND interpreter_reg.actnow_to='1001-01-01') AND (interpreter_reg.active='0' OR interpreter_reg.active='1')) THEN 'not ready'
        WHEN (interpreter_reg.actnow='Inactive' and (CURRENT_DATE() NOT BETWEEN interpreter_reg.actnow_time AND interpreter_reg.actnow_to) AND interpreter_reg.active='0') THEN 'ready'
        ELSE 'not ready' END) as status","interpreter_reg","TRIM(email)='".$username."' AND BINARY TRIM(password)='".$password."'");
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
    }else if($status=='not ready' || $row['deleted_flag']=='1'){
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
?>