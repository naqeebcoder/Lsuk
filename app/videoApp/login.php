<?php
include '../action.php';

if(isset($_POST['login'])){
    $json=(object) null;
    $role=trim($_POST['role']);
    $role=$role ? $role : 1;
    $username=trim($_POST['username']);
    $password=trim($_POST['password']);
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
        if ($row['telep'] == "Yes"){
            $row['has_access'] = "yes";
        } else {
            $row['has_access'] = "no";
        }
    }
    if($row['is_temp']==1){
        $get_msg_db=$obj->read_specific('message','auto_replies','id=7');
        $json->msg=$get_msg_db['message'];
        $json->status="failed";
    }else if(empty($row['id']) || is_null($row['deleted_flag'])){
        $get_msg_db=$obj->read_specific('message','auto_replies','id=1');
        $json->msg=$get_msg_db['message'];
        $json->status="failed";
    }else if($status=='not ready' || $row['deleted_flag']=='1'){
        $get_msg_db=$obj->read_specific('message','auto_replies','id=2');
        $json->msg=$get_msg_db['message'];
        $json->status="failed";
    }else{
        $json->status="success";
        $json->msg='Greetings '.ucwords($row['name']).'! Welcome to LSUK Video App.';
        $json->user_id=$row['id'];
        $json->email=$row['email'];
        $json->name=$row['name'];
        $json->photo=$row['photo'];
        $json->has_access=$row['has_access'];
        $json->role=$role == 1 ? "interpreter" : "client";
    }
    header('Content-Type: application/json');
    echo json_encode($json, JSON_UNESCAPED_UNICODE);
}
?>