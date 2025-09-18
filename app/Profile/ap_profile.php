<?php
include '../action.php';

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
        $q_lang=$obj->read_all("id,lang,level,type","interp_lang","code='".$json['code']."' ORDER BY lang");
        $array_lang_f2f=array();
        $array_lang_tp=array();
        $array_lang_tr=array();
        while($row_lang=$q_lang->fetch_assoc()){
            if($row_lang['type']=='interp'){
                array_push($array_lang_f2f,['id'=>$row_lang['id'],'language'=>$row_lang['lang'],'level'=>$array_level[$row_lang['level']]]);
            }
            if($row_lang['type']=='telep'){
                array_push($array_lang_tp,['id'=>$row_lang['id'],'language'=>$row_lang['lang'],'level'=>$array_level[$row_lang['level']]]);
            }
            if($row_lang['type']=='trans'){
                array_push($array_lang_tr,['id'=>$row_lang['id'],'language'=>$row_lang['lang'],'level'=>$array_level[$row_lang['level']]]);
            }
            /*if($row_lang['level']>2){
                $json['language_edit']="1";
            }*/
        }
        $json['languages']=$array_lang;
        $json['languages_f2f']=$array_lang_f2f;
        $json['languages_tp']=$array_lang_tp;
        $json['languages_tr']=$array_lang_tr;
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
?>