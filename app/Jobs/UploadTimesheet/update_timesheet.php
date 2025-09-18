<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
require '../../../lsuk_system/phpmailer/vendor/autoload.php';
include '../../action.php';
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
        if(!isset($_POST['ap_wait_time']) || empty($_POST['ap_wait_time']))
           $json->status = 1;
        if(isset($_POST['ap_wait_time']) && !empty($_POST['ap_wait_time'])){
            $wait_time=$_POST['ap_wait_time']==1?date('Y-m-d H:i'):date("Y-m-d H:i", strtotime($_POST['ap_wait_time']));
               if ($obj->update($table, ["wt_tm" => $wait_time], "id=".$update_id))
                    $json->status = 1;
                else 
                    $json->status = 0;
            $json->msg=date("d-m-Y H:i", strtotime($wait_time));
        }
        if(isset($_POST['ap_start_time']) && !empty($_POST['ap_start_time'])){
            $start_time=$_POST['ap_start_time']==1?date('Y-m-d H:i'):date("Y-m-d H:i", strtotime($_POST['ap_start_time']));
            // if($start_time<$assignment_start_date){
            //    $start_time=$assignment_start_date;
            // }
            if($obj->update("$table",array("st_tm"=>$start_time),"id=".$update_id) && $json->status == 1)
                $json->status = 1;
            else
                $json->status = 0;
            if($table!='translation'){
                $json->msg=date("d-m-Y H:i", strtotime($start_time));
            }
        }
        
        if(isset($_POST['ap_finish_time']) && !empty($_POST['ap_finish_time'])){
            $finish_time=$_POST['ap_finish_time']==1?date('Y-m-d H:i'):date("Y-m-d H:i", strtotime($_POST['ap_finish_time']));
            if($obj->update("$table",array("fn_tm"=>$finish_time),"id=".$update_id) && $json->status == 1)
                $json->status = 1;
            else
                $json->status = 0;
            $get_rec=$obj->read_specific("assignDate,assignTime,assignDur,wt_tm,st_tm,fn_tm","$table","id=".$update_id);
            // $first_time=($get_rec['wt_tm']!='1001-01-01 00:00:00' && $get_rec['wt_tm']!='0000-00-00 00:00:00')?$get_rec['wt_tm']:$get_rec['st_tm'];
            $first_time=$get_rec['st_tm'];
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
            $default_admin_charges = 0.50;
            if ($table!="translation"){
                $_POST['hours_worked']=$obj->read_specific("hoursWorkd","$table","id=".$update_id)['hoursWorkd'];
            }
            $added_via = 1;// Default set to App
            if (isset($_POST['added_via']) && !empty($_POST['added_via'])) {
                if ($_POST['added_via'] == "android") {
                    $added_via = 3;
                }
                if ($_POST['added_via'] == "ios") {
                    $added_via = 4;
                }
            }
            if($table=='interpreter'){
                // $charge_for_interpreting_time = $_POST['charge_for_interpreting_time'];
                // $chargeTravelTime = $_POST['charge_for_travel_time'];
                // $charge_for_travel_cost = $_POST['charge_for_travel_cost'];
                $charge_for_interpreting_time = ($_POST['hours_worked']*$_POST['rate_per_hour']);
                $chargeTravelTime = ($_POST['travel_time_hours'] * $_POST['travel_time_rate_per_hour']);
                $charge_for_travel_cost = ($_POST['travel_mile'] * $_POST['rate_per_mileage']);
                $_POST['deduction'] = $_POST['deduction'] ? $_POST['deduction'] : 0;
                $f2f_total_job_charges = ($charge_for_interpreting_time + $chargeTravelTime + $charge_for_travel_cost + $_POST['travel_cost'] + $_POST['other_cost'] + $default_admin_charges) - $_POST['deduction'];
                
                $data_update=array("hoursWorkd"=>$_POST['hours_worked'],"rateHour"=>$_POST['rate_per_hour'],"chargInterp"=>$charge_for_interpreting_time,
                "travelTimeHour"=>$_POST['travel_time_hours'],"travelTimeRate"=>$_POST['travel_time_rate_per_hour'],"chargeTravelTime"=>$chargeTravelTime,
                "travelMile"=>$_POST['travel_mile'],"rateMile"=>$_POST['rate_per_mileage'],"chargeTravel"=>$charge_for_travel_cost,
                "travelCost"=>$_POST['travel_cost'],"admnchargs"=>$default_admin_charges,"otherCost"=>$_POST['other_cost'],"deduction"=>$_POST['deduction'],
                "total_charges_interp"=>$f2f_total_job_charges,
                "tm_by"=>'i',"added_via"=>$added_via,"cost_type"=>$_POST['cost_type']);
            }else if($table=='telephone'){
                $charge_for_interpreting_time = ($_POST['hours_worked']*$_POST['rate_per_hour']);
                $tp_total_job_charges = ($charge_for_interpreting_time + $_POST['call_charges'] + $_POST['other_charges'] + $default_admin_charges) - $_POST['deduction'];
                
                $data_update=array("hoursWorkd"=>$_POST['hours_worked'],"rateHour"=>$_POST['rate_per_hour'],"chargInterp"=>$charge_for_interpreting_time,"calCharges"=>$_POST['call_charges'],"otherCharges"=>$_POST['other_charges'],"admnchargs"=>$default_admin_charges,"deduction"=>$_POST['deduction'],
                "total_charges_interp"=>$tp_total_job_charges,
                "tm_by"=>'i',"added_via"=>$added_via);
            }else{
                $charge_for_interpreting_time = ($_POST['units']*$_POST['rate_per_unit']);
                $tr_total_job_charges = ($charge_for_interpreting_time + $_POST['any_other_charges']) - $_POST['deduction'];
                
                $data_update=array("numberUnit"=>$_POST['units'],"rpU"=>$_POST['rate_per_unit'],"otherCharg"=>$_POST['any_other_charges'],"admnchargs"=>$default_admin_charges,"deduction"=>$_POST['deduction'],
                "total_charges_interp"=>$tr_total_job_charges,
                "tm_by"=>'i',"added_via"=>$added_via);
            }
            $data_update_details =  array('approved_flag' => 0, 'hrsubmited'=>'Self','interp_hr_date'=>date("Y-m-d"));
            $data_update = array_merge($data_update,$data_update_details);
            if (isset($data_update['total_charges_interp']) && ($data_update['total_charges_interp']-0.5) == 0) {
                $json->status = 0;
                $json->msg = 'Invalid working hours or rate per hour';
                header('Content-Type: application/json');
                echo json_encode($json, JSON_UNESCAPED_UNICODE);
                die();
            }
            $form_option=$obj->update("$table",$data_update,"id=".$update_id);
            //$obj->update($table,array('approved_flag' => 0, 'hrsubmited'=>'Self','interp_hr_date'=>date("Y-m-d")),"id=".$update_id);
            if($form_option){
                $json->status="1";
                $json->msg="Expenses have been updated for this job. Thank you";
            }else{
                $json->status="0";
                $json->msg="Failed to update expenses for this job. Try again";
                header('Content-Type: application/json');
                echo json_encode($json, JSON_UNESCAPED_UNICODE);
                die();
            }
            if ($_POST['ap_user_id'] != 874) {
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
            $decoded=is_object($_POST['attachment'])?json_decode($_POST['attachment']):$_POST['attachment'];
            $i=0;
            $check_existing=$obj->read_all("id,file_name","job_files","tbl='".$table."' AND order_id=".$update_id." AND file_type='timesheet'");
            if($check_existing->num_rows>0){
                $delete_existing_idz=[];
                while($row_existing=$check_existing->fetch_assoc()){
                    $old_other_expenses_file="../../../file_folder/job_files/".$row_existing['file_name'];
                    if(file_exists($old_other_expenses_file) && !empty($old_other_expenses_file)){
                        unlink($old_other_expenses_file);
                        array_push($delete_existing_idz,$row_existing['id']);
                    }
                }
                if(count($delete_existing_idz)>0){
                    $obj->delete("job_files","id IN (".implode(",",$delete_existing_idz).")");
                }
            }
            foreach ($decoded as $value){
                $i++;
                $at_done=1;
                $file_name = $at_append.round(microtime(true)).$i.'.png';
                $file = base64_decode($value);
                if(file_put_contents("../../../file_folder/job_files/".$file_name, $file)){
                    $obj->insert('job_files',array('tbl' => $table,'file_name'=>$file_name,'order_id'=>$update_id,'interpreter_id'=>$_POST['ap_user_id'], 'dated'=>date('Y-m-d H:i:s')));
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
            $old_cs_file="../../../file_folder/client_signatures/".$old_cs;
            if(file_exists($old_cs_file) && !empty($old_cs)){
                unlink($old_cs_file);
            }
            $cs_img = base64_decode($cs);
            //$cs_file = $sig_append_c.round(microtime(true)).'.png';
            $cs_file = $sig_append_c.$update_id.'.png';
            file_put_contents("../../../file_folder/client_signatures/".$cs_file, $cs_img);
            if($obj->update($table,array('cl_sig'=>$cs_file,'cl_sign_date'=>date('Y-m-d H:i:s')),"id=".$update_id)){
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
            $old_is_file="../../../file_folder/interpreter_signatures/".$old_is;
            if(file_exists($old_is_file) && !empty($old_is)){
                unlink($old_is_file);
            }
            $is_img = base64_decode($is);
            $is_file = $sig_append_i.$update_id.'.png';
            file_put_contents("../../../file_folder/interpreter_signatures/".$is_file, $is_img);
            if($obj->update($table,array('int_sig'=>$is_file,'int_sign_date'=>date('Y-m-d H:i:s')),"id=".$update_id)){
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
            $old_cs_file="../../../file_folder/client_signature_shots/".$old_cs;
            if(file_exists($old_cs_file) && !empty($old_cs)){
                unlink($old_cs_file);
            }
            $cs_img = base64_decode($cs);
            //$cs_file = $sig_append_c.round(microtime(true)).'.png';
            $cs_file = $sig_append_c.$update_id.'.png';
            file_put_contents("../../../file_folder/client_signature_shots/".$cs_file, $cs_img);
            if($obj->update($table,array('client_sign_screen'=>$cs_file,'client_sign_screen_date'=>date('Y-m-d H:i:s')),"id=".$update_id)){
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
            $old_is_file="../../../file_folder/interpreter_signature_shots/".$old_is;
            if(file_exists($old_is_file) && !empty($old_is)){
                unlink($old_is_file);
            }
            $is_img = base64_decode($is);
            $is_file = $sig_append_i.$update_id.'.png';
            file_put_contents("../../../file_folder/interpreter_signature_shots/".$is_file, $is_img);
            if($obj->update($table,array('interp_sign_screen'=>$is_file,'interp_sign_screen_date'=>date('Y-m-d H:i:s')),"id=".$update_id)){
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
?>