<?php
include '../../action.php';
//get active jobs request
if(isset($_POST['ap_active_jobs'])){
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
        if(isset($table)){
            $exclude_old_jobs = $table == "translation" ? " AND $table.asignDate >= '" . date("Y-m-d") . "'" : " AND $table.assignDate >= '" . date("Y-m-d") . "'";
            $append_active_jobs_check=$_POST['ap_value']!='translation'?" AND date_add(CONCAT(assignDate,' ',assignTime), interval (assignDur*2) minute) > NOW() ":"";
            $result = $obj->read_all("$table_name,comp_reg.name as company_name,$table.id as job_id,$table.nameRef as job_key,$table.orderCancelatoin as 'order_cancelled',pay_int,$table.source,$table.target,$query_details","$table,interpreter_reg,comp_reg","$table.intrpName=interpreter_reg.id AND $table.orgName=comp_reg.abrv AND $table.deleted_flag=0 and $table.order_cancel_flag=0 AND $table.orderCancelatoin=0 and $table.jobStatus= 1 and $table.intrpName= '$ap_user_id' and $table.salary_id=0 and ($table.hrsubmited='' OR ($table.hrsubmited != '' and $table.int_sig='')) " . $append_active_jobs_check . $exclude_old_jobs . " ORDER BY assignDate ASC");
        }else{
            $result = $obj->read_all("*",
            "(SELECT 'Face To Face' as job_type,comp_reg.name as company_name,interpreter.id as job_id,interpreter.nameRef as job_key,interpreter.orderCancelatoin as 'order_cancelled',pay_int,interpreter.source,interpreter.target,interpreter.assignDate,substr(interpreter.assignTime,1,5) as assignTime,interpreter.assignDur,interpreter.assignCity,interpreter.postCode,'no_display' as document_type,'no_display' as communication_type,'none' as communication_image,(SELECT interp_cat.ic_title from interp_cat WHERE interp_cat.ic_id=interpreter.interp_cat) as category,'no_display' as delivery_date FROM interpreter,interpreter_reg,comp_reg where interpreter.intrpName=interpreter_reg.id AND interpreter.orgName=comp_reg.abrv AND interpreter.deleted_flag=0 and interpreter.order_cancel_flag=0 AND interpreter.orderCancelatoin=0 and interpreter.jobStatus= 1 and interpreter.intrpName= '$ap_user_id' and interpreter.salary_id=0 and (interpreter.hrsubmited='' OR (interpreter.hrsubmited != '' and interpreter.int_sig='')) AND date_add(CONCAT(assignDate,' ',assignTime), interval (assignDur*2) minute) > NOW() AND interpreter.assignDate >= '" . date("Y-m-d") . "' UNION 
            SELECT 'Telephone' as job_type,comp_reg.name as company_name,telephone.id as job_id,telephone.nameRef as job_key,telephone.orderCancelatoin as 'order_cancelled',pay_int,telephone.source,telephone.target,telephone.assignDate,telephone.assignTime,telephone.assignDur,'no_display' as assignCity,'no_display' as postCode,'no_display' as document_type,(SELECT comunic_types.c_title from comunic_types WHERE comunic_types.c_id=telephone.comunic) as communication_type,(SELECT CONCAT('https://lsuk.org/lsuk_system/images/comunic_types/', comunic_types.c_image) from comunic_types WHERE comunic_types.c_id=telephone.comunic) as communication_image,'no_display' as category,'no_display' as delivery_date FROM telephone,interpreter_reg,comp_reg where telephone.intrpName=interpreter_reg.id AND telephone.orgName=comp_reg.abrv AND telephone.deleted_flag=0 and telephone.order_cancel_flag=0 AND telephone.orderCancelatoin=0 and telephone.jobStatus= 1 and telephone.intrpName= '$ap_user_id' and telephone.salary_id=0 and (telephone.hrsubmited='' OR (telephone.hrsubmited != '' and telephone.int_sig='')) AND date_add(CONCAT(assignDate,' ',assignTime), interval (assignDur*5) minute) > NOW() AND telephone.assignDate >= '" . date("Y-m-d") . "' UNION 
            SELECT 'Translation' as job_type,comp_reg.name as company_name,translation.id as job_id,translation.nameRef as job_key,translation.orderCancelatoin as 'order_cancelled',pay_int,translation.source,translation.target,translation.asignDate as assignDate,'no_display' as assignTime,'no_display' as assignDur,'no_display' as assignCity,'no_display' as postCode,(SELECT trans_cat.tc_title from trans_cat WHERE trans_cat.tc_id=docType) as document_type,docType as communication_type,'none' as communication_image,(SELECT GROUP_CONCAT(trans_types.tt_title SEPARATOR ', ') FROM trans_types WHERE trans_types.tt_id IN (translation.transType)) as category,DATE_FORMAT(deliverDate2,'%d-%m-%Y') as delivery_date FROM translation,interpreter_reg,comp_reg WHERE translation.intrpName=interpreter_reg.id AND translation.orgName=comp_reg.abrv AND translation.deleted_flag=0 and translation.order_cancel_flag=0 AND translation.orderCancelatoin=0 and translation.jobStatus= 1 and translation.intrpName='$ap_user_id' and translation.salary_id=0 and (translation.hrsubmited='' OR (translation.hrsubmited != '' and translation.int_sig='')) AND translation.asignDate >= '" . date("Y-m-d") . "') as grp",
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
            $row['assignDur']=trim($get_dur);
            $row['assignDate']=$misc->dated($row['assignDate']);
            $row['assignCity']=$row['assignCity']." (".$row['postCode'].")";
            $row['order_cancelled'] = $row['order_cancelled']==1 && $row['pay_int'] == 1?"1":"0";
            $row['order_cancelled_message']=$row['order_cancelled']==1 && $row['pay_int'] == 1?"This order has been cancelled. You will be paid":"";
            $row["hostedBy"]="";// temp changes done as emergency fix
            array_push($json,$row);
        }
    }else{
        $json->msg="not_logged_in";
    }
    header('Content-Type: application/json');
    echo json_encode($json, JSON_UNESCAPED_UNICODE);
}
