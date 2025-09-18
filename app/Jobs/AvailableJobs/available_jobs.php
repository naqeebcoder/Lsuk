<?php
include '../../action.php';
//get available jobs request
if(isset($_POST['available_jobs'])){
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
                $append_extra_f2f_check = " AND ((interpreter_reg.uk_citizen=1 AND interpreter_reg.id_doc_expiry_date != '1001-01-01' AND interpreter_reg.id_doc_expiry_date > CURRENT_DATE()) OR (interpreter_reg.uk_citizen=0 AND interpreter_reg.work_evid_expiry_date != '1001-01-01' AND interpreter_reg.work_evid_expiry_date > CURRENT_DATE())) 
                                    AND (interpreter_reg.is_dbs_auto=1 OR (interpreter_reg.is_dbs_auto=0 AND interpreter_reg.dbs_expiry_date != '1001-01-01' AND interpreter_reg.dbs_expiry_date > CURRENT_DATE())) ";
                $wk_type='interp';
                $put_id="AND interpreter.id=11166";
                $table_name="'Face To Face' as job_type";
                $query_details="$table.assignDate,'interpreter' as tbl,substr($table.assignTime,1,5) as assignTime,$table.noty,$table.assignDur,CONCAT($table.assignCity,' (',substr($table.postCode,1,3),')') as assignCity,(SELECT interp_cat.ic_title from interp_cat WHERE interp_cat.ic_id=interpreter.interp_cat) as category";
                //echo $query_details;exit;
            }else{
                $wk_type='telep';
                $put_id="AND telephone.id=3288";
                $table_name="'".ucwords($table)."' as job_type";
                $query_details="$table.assignDate,'telephone' as tbl,$table.assignTime,$table.noty,$table.assignDur,(SELECT comunic_types.c_title from comunic_types WHERE comunic_types.c_id=telephone.comunic) as communication_type";
            }
            $put=isset($_POST['ap_value'])?"and $table.gender!= '$gender_req'":" ";
        }else{
            $wk_type='trans';
            $put_id="AND translation.id=1427";
            $table_name="'".ucwords($table)."' as job_type";
            $query_details="$table.asignDate as assignDate,'translation' as tbl,deliverDate,(SELECT trans_cat.tc_title from trans_cat WHERE trans_cat.tc_id=docType) as document_type,(SELECT GROUP_CONCAT(trans_types.tt_title SEPARATOR ', ') FROM trans_types WHERE trans_types.tt_id IN (translation.transType)) as category,DATE_FORMAT(deliverDate2,'%d-%m-%Y') as delivery_date";
            $put=" ";
        }
        if(isset($_POST['ap_value'])){
            // condition to check if job is F2F or Telephone then get guess_dur
            $guess_dur_cond = '';
            if($_POST['ap_value'] == 'interpreter' || $_POST['ap_value'] == 'telephone'){
                $guess_dur_cond = ','.$table.'.guess_dur';
            }
            $dateCol = $_POST['ap_value'] == 'translation' ? 'asignDate' : 'assignDate';
            $result = $obj->read_all("$table_name,$table.orgName as company_name,$table.id as job_id,$table.nameRef as job_key $guess_dur_cond,$table.source,$table.target,$query_details","$table,interpreter_reg","$table.deleted_flag=0 and $table.order_cancel_flag=0 AND $table.orderCancelatoin=0 and $table.jobStatus= 1 and $table.is_temp= 0 and $table.intrpName= '' ".$put." and $table.jobDisp= 1 and interpreter_reg.code='$interp_code' AND interpreter_reg.$wk_type='Yes' $append_extra_f2f_check AND 
            interpreter_reg.active='0' AND (interpreter_reg.actnow='Active' OR (interpreter_reg.actnow='Inactive' AND $table.$dateCol NOT BETWEEN interpreter_reg.actnow_time AND interpreter_reg.actnow_to)) AND interpreter_reg.deleted_flag=0 AND interpreter_reg.is_temp=0");
        }else{
            $result = $obj->read_all("'Face To Face' as job_type,interpreter.orgName as company_name,interpreter.id as job_id,interpreter.nameRef as job_key,interpreter.source,interpreter.target,interpreter.assignDate,'interpreter' as tbl,substr(interpreter.assignTime,1,5) as assignTime,interpreter.noty,interpreter.assignDur,CONCAT(interpreter.assignCity,' (',interpreter.postCode,')') as assignCity,'no_display' as document_type,'no_display' as communication_type,'none' as communication_image,(SELECT interp_cat.ic_title from interp_cat WHERE interp_cat.ic_id=interpreter.interp_cat) as category FROM interpreter,interpreter_reg where interpreter.deleted_flag=0 and interpreter.order_cancel_flag=0 AND interpreter.orderCancelatoin=0 and interpreter.jobStatus= 1 and interpreter.is_temp= 0 and interpreter.intrpName= '' and interpreter.gender!= '$gender_req' and interpreter.jobDisp= 1 and interpreter_reg.code='$interp_code' AND interpreter_reg.interp='Yes' $append_extra_f2f_check and 
            interpreter_reg.active='0' AND (interpreter_reg.actnow='Active' OR (interpreter_reg.actnow='Inactive' AND interpreter.assignDate NOT BETWEEN interpreter_reg.actnow_time AND interpreter_reg.actnow_to)) AND interpreter_reg.deleted_flag=0 AND interpreter_reg.is_temp=0 UNION
            SELECT 'Telephone' as job_type,telephone.orgName as company_name,telephone.id as job_id,telephone.nameRef as job_key,telephone.source,telephone.target,telephone.assignDate,'telephone' as tbl,telephone.assignTime,telephone.noty,telephone.assignDur,'no_display' as assignCity,'no_display' as document_type,(SELECT comunic_types.c_title from comunic_types WHERE comunic_types.c_id=telephone.comunic) as communication_type,(SELECT CONCAT('https://lsuk.org/lsuk_system/images/comunic_types/', comunic_types.c_image) from comunic_types WHERE comunic_types.c_id=telephone.comunic) as communication_image,'no_display' as category FROM telephone,interpreter_reg where telephone.deleted_flag=0 and telephone.order_cancel_flag=0 AND telephone.orderCancelatoin=0 and telephone.jobStatus= 1 and telephone.is_temp= 0 and telephone.intrpName= '' and telephone.gender!= '$gender_req' and telephone.jobDisp= 1 and interpreter_reg.code='$interp_code' AND interpreter_reg.telep='Yes' and 
            interpreter_reg.active='0' AND (interpreter_reg.actnow='Active' OR (interpreter_reg.actnow='Inactive' AND telephone.assignDate NOT BETWEEN interpreter_reg.actnow_time AND interpreter_reg.actnow_to)) AND interpreter_reg.deleted_flag=0 AND interpreter_reg.is_temp=0 UNION 
            SELECT 'Translation' as job_type,translation.orgName as company_name,translation.id as job_id,translation.nameRef as job_key,translation.source,translation.target,translation.asignDate as assignDate,'translation' as tbl,'no_display' as assignTime,'' as noty,'no_display' as assignDur,'no_display' as assignCity,(SELECT trans_cat.tc_title from trans_cat WHERE trans_cat.tc_id=docType) as document_type,docType as communication_type,'none' as communication_image,(SELECT GROUP_CONCAT(trans_types.tt_title SEPARATOR ', ') FROM trans_types WHERE trans_types.tt_id IN (translation.transType)) as category","translation,interpreter_reg","translation.deleted_flag=0 and translation.order_cancel_flag=0 AND translation.orderCancelatoin=0 and translation.jobStatus= 1 and translation.is_temp= 0 and translation.intrpName='' and translation.jobDisp= 1 and interpreter_reg.code='$interp_code' AND interpreter_reg.trans='Yes' and 
            interpreter_reg.active='0' AND (interpreter_reg.actnow='Active' OR (interpreter_reg.actnow='Inactive' AND translation.asignDate NOT BETWEEN interpreter_reg.actnow_time AND interpreter_reg.actnow_to)) AND interpreter_reg.deleted_flag=0 AND interpreter_reg.is_temp=0");
        }
        // echo $result;die;
        $json=array();
        while($row = $result->fetch_assoc()){
            if(!empty($row['noty'])){
                $noty = explode(",",$row['noty']);
                if(!in_array($_POST['ap_user_id'],$noty)){
                    continue;
                }
            }
            $chk_blk=0;
            $blk=0;
            if(trim(strtolower($row['job_type']))==strtolower('Face to Face')){
                $blk=1;
                $chek_col='interp';
            }if(trim(strtolower($row['job_type']))==strtolower('Telephone')){
                $blk=1;
                $chek_col='telep';
            }else if(trim(strtolower($row['job_type']))==strtolower('Translation')){
                $blk=2;
                $chek_col='trans';
            }
            $chk_blk=$obj->read_specific("COUNT(*) as black_listed","interp_blacklist","interpName='id-".$_POST['ap_user_id']."' AND orgName='".$row['company_name']."' AND deleted_flag=0 AND blocked_for=$blk ")['black_listed'];
            if($chk_blk>0){
                continue;
            }
            $check_bid=$obj->read_specific("*","bid","interpreter_id=".$_POST['ap_user_id']." AND job=".$row['job_id']." AND tabName='".$row['tbl']."'");
            if (!empty($check_bid['id'])) {
                if ($check_bid['bid_type'] == 2) {
                    continue;
                }elseif($check_bid['bid_type'] == 1){
                    $row['bid'] = 1;
                    $row['bid_label'] = "Applied";
                }elseif($check_bid['bid_type'] == 3){
                    $row['bid'] = 3;
                    $row['bid_label'] = "Alternate Availability Given";
                }
            } else {
                $row['bid'] = 0;
            }
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
                $lang_checker=$obj->read_specific("COUNT(DISTINCT interp_lang.lang) as counter","interp_lang","interp_lang.lang IN ('".$row['source']."','".$row['target']."') and interp_lang.level<3 AND interp_lang.type='$chek_col' and interp_lang.code='$interp_code'")['counter'];
                $allow_int=$lang_checker>=2?"yes":"no";
            }else if($row['source']=='English' && $row['target']!='English'){
                $lang_checker=$obj->read_specific("COUNT(DISTINCT interp_lang.lang) as counter","interp_lang","interp_lang.lang='".$row['target']."' and interp_lang.level<3 AND interp_lang.type='$chek_col' and interp_lang.code='$interp_code'")['counter'];
                $allow_int=$lang_checker==1?"yes":"no";
                }else if($row['source']!='English' && $row['target']=='English'){
                    $lang_checker=$obj->read_specific("COUNT(DISTINCT interp_lang.lang) as counter","interp_lang","interp_lang.lang='".$row['source']."' and interp_lang.level<3 AND interp_lang.type='$chek_col' and interp_lang.code='$interp_code'")['counter'];
                    $allow_int=$lang_checker==1?"yes":"no";
                }else if($row['source']==$row['target'] && $row['source']!='English'){
                    $lang_checker=$obj->read_specific("COUNT(DISTINCT interp_lang.lang) as counter","interp_lang","interp_lang.lang='".$row['source']."' and interp_lang.level<3 AND interp_lang.type='$chek_col' and interp_lang.code='$interp_code'")['counter'];
                    $allow_int=$lang_checker>=1?"yes":"no";
                }else{
                    $lang_checker=0;
                    $allow_int="no";
                }
                if($allow_int=="yes"){
                    $row['assignDate']=$misc->dated($row['assignDate']);
                    $row['pf'] = 0;
                    array_push($json,$row);
                }
        }//end of while
        require '../../../lsuk_system/post_format_job.php'; 
        $json= array_merge($jobsArray, $json);
        if(count($json)==0){
            $json="no_jobs";
        }
    }else{
    $json->msg="not_logged_in";
    }
header('Content-Type: application/json');
echo json_encode($json, JSON_UNESCAPED_UNICODE);
}
?>