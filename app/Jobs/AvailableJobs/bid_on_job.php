<?php
include '../../action.php';
//get bidding request
if(isset($_POST['bid_on_job']) && isset($_POST['ap_tracking']) && isset($_POST['ap_value'])){
    $json=(object) null;
    if(isset($_POST['ap_user_id']) && !empty($_POST['ap_user_id'])){
        $bid_via = isset($_POST['bid_via']) && !empty($_POST['bid_via']) ? $_POST['bid_via'] : 2;
        // $bid_via = $_POST['bid_via'] ? $_POST['bid_via'] : 2;
        $array_gender = array("Male" => 1, "Female" => 2, "No Preference" => 3);
        $interp_id=$_POST['ap_user_id'];
        //Code Updated 
        $message=$_POST['message'];
        $alternate_date=$_POST['alternate_date'];
        if (!empty($alternate_date)) {
            $message .= " " . $alternate_date;
        }
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
        }
        else if(isset($_POST['pf']) && $_POST['pf']) {
            $bid_type = $_POST['bid_type'];
            $data = [
                "allocated" => 0,
                "job" => $_POST["ap_tracking"],  
                "dated" => date("Y-m-d"), 
                "interpreter_id" => $_POST["ap_user_id"],  
                "tabName" => $_POST["ap_value"],  
                "bid_type" => $bid_type,
                "bid_via" => 2,
                "message" => ($bid_type == 3) ? $_POST["message"] : null,
                "alternate_date" => ($bid_type == 3) ? $_POST["alternate_date"] : null,
            ];

            $result = $obj->insert("bid", $data);

            if ($result) {
                if ($bid_type == 1) {
                    $json->msg = "Applied successfully";
                } elseif ($bid_type == 2) {
                    $json->msg = "Job declined successfully";
                } elseif ($bid_type == 3) {
                    $json->msg = "Alternative availability given";
                }
                $json->status = '10';
            } else {
                $json->msg = "Error processing request";
                $json->status = '-1';
            }

            header('Content-Type: application/json');
            echo json_encode($json, JSON_UNESCAPED_UNICODE);
            die();
        }

        else{
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
                $gender_required=$row_job['gender'];
                $assignTime=$row_job['assignTime'];
                $assignDur=$row_job['assignDur'];
                $dur_in_hr=$assignDur/60;
                $assignTime_req=substr($assignTime,0,5);
                $replaced_time=str_replace(':','.',$assignTime_req);
                $result_booked=$obj->read_all("id,assignDate,assignTime,assignDur,REPLACE(substr(assignTime,1,5),':','.') as new_time","$val","intrpName='$interp_id' and assignDate='$assignDate' and (REPLACE(substr(assignTime,1,5),':','.')=($replaced_time) OR REPLACE(substr(assignTime,1,5),':','.')=($replaced_time+$dur_in_hr)) AND deleted_flag=0 AND order_cancel_flag=0 and orderCancelatoin=0");
            }else{
                $gender_required='';
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
    $row_docs = $obj->read_specific("(CASE 
    WHEN (actnow='Active' and (actnow_time='1001-01-01' AND actnow_to='1001-01-01') AND active='0') THEN 'Yes'
    WHEN (actnow='Active' and (CURRENT_DATE() BETWEEN actnow_time AND actnow_to) AND active='0') THEN 'Yes'
    WHEN (actnow='Inactive' and (actnow_time='1001-01-01' AND actnow_to='1001-01-01') AND (active='0' OR active='1')) THEN 'No'
    WHEN (actnow='Inactive' and (CURRENT_DATE() NOT BETWEEN actnow_time AND actnow_to) AND active='0') THEN 'Yes'
    ELSE 'No' END) as activeness,applicationForm,agreement,crbDbs,identityDocument,gender","interpreter_reg","id=".$interp_id);

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
        $obj->editFun('bid',$edit_id,'message',$message);
        $obj->editFun('bid',$edit_id,'alternate_date',$alternate_date);
        $obj->editFun('bid',$edit_id,'allocated','0');
        $obj->editFun('bid',$edit_id,'bid_via', $bid_via);
        $obj->editFun('bid',$edit_id,'interpreter_id',$_POST['ap_user_id']);
        if (isset($_POST['bid_type']) && $_POST['bid_type'] == 2) {//Declined bid
            $obj->editFun('bid',$edit_id,'bid_type', 2);
            $json->msg="Your have declined your bid on this job successfully.";
        }
        if (isset($_POST['bid_type']) && $_POST['bid_type'] == 3) {//alternate date bid
            $obj->editFun('bid',$edit_id,'bid_type', 3);
            $json->msg="Your bid for alternate date on this job received successfully.";
        }
        if($val!="translation" && !empty($gender_required)){
            $obj->editFun('bid',$edit_id,'gender_status',$array_gender[$gender_required]);
        }
    }else{
        //echo 'more jobs = '.$more_jobs;exit;
    if($allow_gender==1 && $check_ability=='Yes' && $row_count_amend['amend_counts'] <= 2 && $check_jobdDisp=='1' && $check_jobStatus=='1' && $allot=='yes' && $check_black['id']=='' && $check_lang['counter']!='0' && $check_on_hold=='No' && ($row_feedback['result']>=40 || is_null($row_feedback['result'])) && $empty_doc=='No' && $activeness=='Yes' &&  $more_jobs==0){
        // if($allow_gender==1 && $check_ability=='Yes' && $row_count_amend['amend_counts'] <= 2 && $check_jobdDisp=='1' && $check_jobStatus=='1' && $allot=='yes' && $check_black['id']=='' && $check_lang['counter']!='0' && $check_on_hold=='No' && ($row_feedback['result']>=40 || is_null($row_feedback['result'])) && $empty_doc=='No' && $activeness=='Yes'){
        $get_msg_db=$obj->read_specific('message','auto_replies','id=5');
        $json->msg=$get_msg_db['message'];
        $edit_id= $obj->get_id('bid'); 
        $obj->editFun('bid',$edit_id,'job',$check_id);
        $obj->editFun('bid',$edit_id,'tabName',$val);
        $obj->editFun('bid',$edit_id,'message',$message);
        $obj->editFun('bid',$edit_id,'alternate_date',$alternate_date);
        $obj->editFun('bid',$edit_id,'allocated','1');
        $obj->editFun('bid',$edit_id,'bid_via', $bid_via);
        $obj->editFun('bid',$edit_id,'interpreter_id',$_POST['ap_user_id']);
        if (isset($_POST['bid_type']) && $_POST['bid_type'] == 2) { //Declined bid
            $obj->editFun('bid',$edit_id,'bid_type', 2);
            $json->msg="Your have declined your bid on this job successfully.";
        }
        if (isset($_POST['bid_type']) && $_POST['bid_type'] == 3) { //alternate date bid
            $obj->editFun('bid',$edit_id,'bid_type', 3);
            $json->msg="Your bid for alternate date on this job received successfully.";
        }
        if($val!="translation" && !empty($gender_required)){
            $obj->editFun('bid',$edit_id,'gender_status',$array_gender[$gender_required]);
        }
        $obj->editFun($val,$check_id,'intrpName',$_POST['ap_user_id']);
        $auto_allocated="Auto Allocated";
        $auto_date=date("Y-m-d");
        $obj->editFun($val,$check_id,'pay_int','1');
        $obj->editFun($val,$check_id,'aloct_by',$auto_allocated);
        $obj->editFun($val,$check_id,'aloct_date',$auto_date);
        $obj->editFun($val,$check_id,'intrpName',$_POST['ap_user_id']);
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
    $subject = "Confirmation of ".$source." translation project $assign_id requested on ".$misc->dated($asignDate);
    $append_table = "<table>
    <tr>
    <td style='border: 1px solid yellowgreen;padding:5px;'>Translation Project ID</td>
    <td style='border: 1px solid yellowgreen;padding:5px;'>".$assign_id."</td>
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
    $subject = "Confirmation of ".$source." Face To Face interpreting project $assign_id requested on ".$assignDate." at ".$assignTime;
    $append_table = "<table>
    <tr>
    <td style='border: 1px solid yellowgreen;padding:5px;'>Face To Face Project ID</td>
    <td style='border: 1px solid yellowgreen;padding:5px;'>".$assign_id."</td>
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
        $subject = "Confirmation of ".$source." telephone interpreting project $assign_id requested on ".$assignDate." at ".$assignTime;
        $append_table = "<table>
    <tr>
    <td style='border: 1px solid yellowgreen;padding:5px;'>Telephone Project ID</td>
    <td style='border: 1px solid yellowgreen;padding:5px;'>".$assign_id."</td>
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
                $get_msg_db=$obj->read_specific('message','auto_replies','id=6');
                $json->msg=$get_msg_db['message'].$msg_cont_office;
                $edit_id= $obj->get_id('bid');
                $obj->editFun('bid',$edit_id,'job',$check_id);
                $obj->editFun('bid',$edit_id,'tabName',$val);
                $obj->editFun('bid',$edit_id,'allocated','0');
                $obj->editFun('bid',$edit_id,'bid_via', $bid_via);
                $obj->editFun('bid',$edit_id,'interpreter_id',$_POST['ap_user_id']);
                if (isset($_POST['bid_type']) && $_POST['bid_type'] == 2) {//Declined bid
                    $obj->editFun('bid',$edit_id,'bid_type', 2);
                    $json->msg="Your have declined your bid on this job successfully.";
                }
                if($val!="translation" && !empty($gender_required)){
                    $obj->editFun('bid',$edit_id,'gender_status',$array_gender[$gender_required]);
                }
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
?>