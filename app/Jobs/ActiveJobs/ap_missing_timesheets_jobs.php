<?php
include '../../action.php';
//get missing time sheets jobs
if(isset($_POST['ap_missing_timesheets_jobs'])){
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
            $append_active_jobs_check=$_POST['ap_value']!='translation'?" AND date_add(CONCAT(assignDate,' ',assignTime), interval (assignDur*2) minute) < NOW() ":"";
            $result = $obj->read_all("$table_name,comp_reg.name as company_name,$table.id as job_id,$table.nameRef as job_key,$table.orderCancelatoin as 'order_cancelled',pay_int,$table.source,$table.target,$query_details","$table,interpreter_reg,comp_reg","$table.intrpName=interpreter_reg.id AND $table.orgName=comp_reg.abrv AND $table.deleted_flag=0 and $table.order_cancel_flag=0 AND (($table.orderCancelatoin=1 and $table.pay_int=1) OR $table.orderCancelatoin=0) and $table.jobStatus= 1 and $table.intrpName= '$ap_user_id' and $table.int_sig='' and $table.salary_id=0 and $table.hrsubmited='' " . $append_active_jobs_check . " ORDER BY assignDate ASC");
        }else{
            $result = $obj->read_all("*",
            "(SELECT 'Face To Face' as job_type,comp_reg.name as company_name,interpreter.id as job_id,interpreter.nameRef as job_key,interpreter.orderCancelatoin as 'order_cancelled',pay_int,interpreter.source,interpreter.target,interpreter.assignDate,substr(interpreter.assignTime,1,5) as assignTime,interpreter.assignDur,interpreter.assignCity,interpreter.postCode,'no_display' as document_type,'no_display' as communication_type,(SELECT interp_cat.ic_title from interp_cat WHERE interp_cat.ic_id=interpreter.interp_cat) as category,'no_display' as delivery_date FROM interpreter,interpreter_reg,comp_reg where interpreter.intrpName=interpreter_reg.id AND interpreter.orgName=comp_reg.abrv AND interpreter.deleted_flag=0 and interpreter.order_cancel_flag=0 AND ((interpreter.orderCancelatoin=1 and interpreter.pay_int=1) OR interpreter.orderCancelatoin=0) and interpreter.jobStatus= 1 and interpreter.intrpName= '$ap_user_id' and interpreter.hoursWorkd=0 and interpreter.salary_id=0 and interpreter.hrsubmited='' AND date_add(CONCAT(assignDate,' ',assignTime), interval (assignDur*2) minute) < NOW() UNION 
            SELECT 'Telephone' as job_type,comp_reg.name as company_name,telephone.id as job_id,telephone.nameRef as job_key,telephone.orderCancelatoin as 'order_cancelled',pay_int,telephone.source,telephone.target,telephone.assignDate,telephone.assignTime,telephone.assignDur,'no_display' as assignCity,'no_display' as postCode,'no_display' as document_type,(SELECT comunic_types.c_title from comunic_types WHERE comunic_types.c_id=telephone.comunic) as communication_type,'no_display' as category,'no_display' as delivery_date FROM telephone,interpreter_reg,comp_reg where telephone.intrpName=interpreter_reg.id AND telephone.orgName=comp_reg.abrv AND telephone.deleted_flag=0 and telephone.order_cancel_flag=0 AND ((telephone.orderCancelatoin=1 and telephone.pay_int=1) OR telephone.orderCancelatoin=0) and telephone.jobStatus= 1 and telephone.intrpName= '$ap_user_id' and telephone.hoursWorkd=0 and telephone.salary_id=0 and telephone.hrsubmited='' AND date_add(CONCAT(assignDate,' ',assignTime), interval (assignDur*5) minute) < NOW() UNION 
            SELECT 'Translation' as job_type,comp_reg.name as company_name,translation.id as job_id,translation.nameRef as job_key,translation.orderCancelatoin as 'order_cancelled',pay_int,translation.source,translation.target,translation.asignDate as assignDate,'no_display' as assignTime,'no_display' as assignDur,'no_display' as assignCity,'no_display' as postCode,(SELECT trans_cat.tc_title from trans_cat WHERE trans_cat.tc_id=docType) as document_type,docType as communication_type,(SELECT GROUP_CONCAT(trans_types.tt_title SEPARATOR ', ') FROM trans_types WHERE trans_types.tt_id IN (translation.transType)) as category,DATE_FORMAT(deliverDate2,'%d-%m-%Y') as delivery_date FROM translation,interpreter_reg,comp_reg WHERE translation.intrpName=interpreter_reg.id AND translation.orgName=comp_reg.abrv AND translation.deleted_flag=0 and translation.order_cancel_flag=0 AND ((translation.orderCancelatoin=1 and translation.pay_int=1) OR translation.orderCancelatoin=0) and translation.jobStatus= 1 and translation.intrpName='$ap_user_id' and translation.salary_id=0 and translation.hrsubmited='') as grp",
            "1 ORDER BY assignDate ASC");
        }
        $data=array();
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
                $row['arrived'] = "0";
                $row['on_the_way'] = "0";
            }
            if($row['job_type'] == 'Telephone'){
                $job = $obj->read_specific("is_interpreter_ready","telephone","id=".$row['job_id']);
                $row['is_ready'] = $job['is_interpreter_ready'];
            }else{
                $row['is_ready'] = "0";
            }
            $expected_start = date($assignDate.' '.substr($assignTime,0,5));
            $expected_end = date("Y-m-d H:i",strtotime("+$assignDur minutes", strtotime($expected_start)));
            $row['expected_start']=$row['job_type']=='Translation'?'no_display':date("d-m-Y H:i", strtotime($expected_start));
            $row['expected_end']=$row['job_type']=='Translation'?'no_display':date("d-m-Y H:i", strtotime($expected_end));
            $row['assignDur']=$get_dur;
            $row['assignDate']=$misc->dated($row['assignDate']);
            $row['assignCity']=$row['assignCity']." (".$row['postCode'].")";
            $row['order_cancelled'] = $row['order_cancelled']==1 && $row['pay_int'] == 1?"1":"0";
            $row['order_cancelled_message']=$row['order_cancelled']==1 && $row['pay_int'] == 1?"This order has been cancelled. You will be paid":"";
            array_push($data,$row);
            $json->missing_timesheet_alert="Please send a paper copy of the timesheet to <span style='color:blue'>payroll@lsuk.org</span> your timesheet (downloadable from web portal) must be signed by yourself ".($row['job_type'] == 'Face To Face'?"and the client":"").". If you don't have signed timesheet then please send your session length and other claim details to the above email address";
        }
        $json->jobs=$data;
        $agreement_validity = $obj->read_specific(
            "*,DATE(created_date) as date_group",
            "audit_logs",
            "table_name='email_format' AND record_id=41 ORDER BY id DESC LIMIT 1"
        )['date_group'] ?? '2010-01-01';
        $missing_docs = $obj->read_specific(
                    "TRIM(BOTH ', ' FROM CONCAT(
                            CASE WHEN ir.agreement = 'Hard Copy' OR ir.signature_date < '$agreement_validity'  THEN 'agreement,'  ELSE '' END,
                            CASE WHEN ir.interp_pix IS NULL OR ir.interp_pix = '' THEN 'photo,' ELSE '' END,
                            CASE WHEN ir.interp = 'Yes' AND (ir.crbDbs IS NULL OR ir.crbDbs = '') THEN 'dbs,' ELSE '' END,
                            CASE WHEN ir.interp = 'Yes' AND ir.dbs_expiry_date IS NOT NULL AND ir.dbs_expiry_date < CURDATE() THEN 'dbs,' ELSE '' END,
                            CASE WHEN ir.uk_citizen = 1 AND (ir.id_doc_no IS NULL OR ir.id_doc_no = '' 
                                    OR ir.id_doc_issue_date IS NULL 
                                    OR ir.id_doc_expiry_date IS NULL) THEN 'identity_document, ' ELSE '' END,
                            CASE WHEN ir.uk_citizen = 1 AND ir.id_doc_expiry_date IS NOT NULL AND ir.id_doc_expiry_date < CURDATE() THEN 'identity_document,' ELSE '' END,
                            CASE 
                                WHEN ir.uk_citizen = 0 AND (
                                    ir.right_to_work_no IS NULL OR ir.right_to_work_no = '' 
                                    OR ir.work_evid_file IS NULL OR ir.work_evid_file = '' 
                                    OR ir.work_evid_issue_date IS NULL 
                                    OR ir.work_evid_expiry_date IS NULL 
                                    OR (ir.work_evid_expiry_date IS NOT NULL AND ir.work_evid_expiry_date < CURDATE())
                                )
                                THEN 'right_to_work,' 
                                ELSE '' 
                            END,
                            CASE WHEN (ir.country_of_origin IS NULL OR ir.country_of_origin = '') THEN 'country_of_origin,' ELSE '' END,
                            CASE WHEN (ir.postCode IS NULL OR ir.postCode = '' OR ir.city = '' OR ir.city IS NULL OR ir.buildingName IS NULL OR ir.buildingName = '') THEN 'address,' ELSE '' END,
                            CASE WHEN (ir.acNo IS NULL OR ir.acNo = '' OR ir.acntCode = '' OR ir.acntCode IS NULL OR ir.acName IS NULL OR ir.acName = '' OR ir.bnakName IS NULL OR ir.bnakName = '') THEN 'bank_details,' ELSE '' END,
                            CASE WHEN (ir.dob = 0000-00-00) THEN 'dob,' ELSE '' END
                        )) AS reasons",
                    "interpreter_reg ir",
                    "(
                        ir.interp_pix IS NULL OR ir.interp_pix = '' OR ir.agreement = ''
                        OR (ir.interp = 'Yes' AND (ir.crbDbs IS NULL OR ir.crbDbs = ''))
                        OR (ir.interp = 'Yes' AND ir.dbs_expiry_date IS NOT NULL AND ir.dbs_expiry_date < CURDATE())
                        OR (ir.uk_citizen = 1 AND (ir.id_doc_no IS NULL OR ir.id_doc_no = '' OR ir.id_doc_issue_date IS NULL OR ir.id_doc_expiry_date IS NULL))
                        OR (ir.uk_citizen = 1 AND ir.id_doc_expiry_date IS NOT NULL AND ir.id_doc_expiry_date < CURDATE())
                        OR (ir.uk_citizen = 0 AND (ir.right_to_work_no IS NULL OR ir.right_to_work_no = ''
                                OR ir.work_evid_file IS NULL OR ir.work_evid_file = ''
                                OR ir.work_evid_issue_date IS NULL
                                OR ir.work_evid_expiry_date IS NULL
                                OR ir.postCode IS NULL OR ir.postCode = '' OR ir.city = '' OR ir.city IS NULL OR ir.buildingName IS NULL OR ir.buildingName = ''
                                OR ir.acNo IS NULL OR ir.acNo = '' OR ir.acntCode = '' OR ir.acntCode IS NULL OR ir.acName IS NULL OR ir.acName = '' OR ir.bnakName IS NULL OR ir.bnakName = ''
                                OR ir.work_evid_expiry_date < CURDATE()
                                OR ir.country_of_origin IS NULL OR ir.country_of_origin = '' OR ir.dob = 0000-00-00))
                    )AND ir.id =".$_POST['ap_user_id']);
        if (!empty(trim($missing_docs['reasons']))) {
        $json->missing_doc = 1;
            $json->missing_doc_list = $missing_docs['reasons'];// remove trailing comma
        } else {
            $json->missing_doc = 0;
            $json->missing_doc_list = "";
        }

        
    }else{
        $json->msg="not_logged_in";
    }
    header('Content-Type: application/json');
    echo json_encode($json, JSON_UNESCAPED_UNICODE);
}
?>