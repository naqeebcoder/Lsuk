<?php set_include_path('/home/customer/www/lsuk.org/public_html/');
function callTMS($url) {
    // $ch = curl_init();
    // curl_setopt($ch, CURLOPT_URL, $url);
    // curl_setopt($ch, CURLOPT_HTTPGET, true);
    // curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    // $combined = curl_exec ($ch);
    // curl_close ($ch);
    // return $combined;
    $options = [
        "http" => [
            "method" => "GET",
            "header" => "User-Agent: PHP\r\n"
        ]
    ];
    $context = stream_context_create($options);
    $response = file_get_contents($url, false, $context);
    return $response;
}
include 'lsuk_system/actions.php';
//Add Email config
include 'source/setup_email.php';
$mail->SMTPDebug = 0;
$mail->isSMTP();
$mail->Host = setupEmail::EMAIL_HOST;
$mail->SMTPAuth   = true;
$mail->SMTPSecure = setupEmail::SECURE_TYPE;
$mail->Port       = setupEmail::SENDING_PORT;
//Add SMS config
include 'source/setup_sms.php';
$setupSMS = new setupSMS;

$updated_date = date('Y-m-d H:i:s');
$updated_array = array("updated_date" => $updated_date);
$order_types = array(1 => "interpreter", 2 => "telephone", 3 => "translation");
$default_limit = 16;
$get_emails_important =$obj->read_all("cron_emails_important.*,'cron_emails_important' as table_name","cron_emails_important","cron_emails_important.status=0 Limit ".$default_limit);
$limit_emails_important = $get_emails_important->num_rows;
if($limit_emails_important==0){
    $get_emails =$obj->read_all("cron_emails.*,'cron_emails' as table_name","cron_emails","cron_emails.status=0 Limit ".$default_limit);
}else{
    $limit=($default_limit-$limit_emails_important);
    $get_emails =$obj->read_all("*","((SELECT cron_emails_important.*,'cron_emails_important' as table_name FROM cron_emails_important WHERE cron_emails_important.status=0 LIMIT ".$limit_emails_important.") UNION (SELECT cron_emails.*,'cron_emails' as table_name FROM cron_emails WHERE cron_emails.status=0 LIMIT ".$limit.")) as grp","1");
}
$counter=1;
if($get_emails->num_rows>0){
    while ($row = $get_emails->fetch_assoc()){
        if(!empty($row['send_to'])){
            $send_from=$row['send_from'];
            if (!empty($row['template_data'])) {
                $template_data=json_decode($row['template_data'],true);
                if ($template_data['specific_interval'] == 1) {
                    $upcoming_assignment_time = new DateTime($template_data['assignment_time']);
                    if ($upcoming_assignment_time->format('Y-m-d H:i:s') > date('Y-m-d H:i:s')) {
                        $upcoming_assignment_time->modify('-1 hour');
                        $time_hour = $upcoming_assignment_time->format('H');
                        if ($time_hour > date('H')) {
                            continue;
                        } else {
                            // Send text message if phone number is added in data
                            if (array_key_exists("send_to_phone", $template_data) && array_key_exists("sms_message_body", $template_data)) {
                                if (setupSMS::IS_ALLOWED == 1) {//SMS is allowed
                                    $send_to_phone = $setupSMS->format_phone($template_data['send_to_phone'], $template_data['send_to_country']);
                                    $obj->insert("job_messages", array("order_id" => $row['order_id'], "order_type" => $row['order_type'], "interpreter_id" => $row['user_id'], "message_category" => 7, "created_by" => 1, "created_date" => $updated_date, "message_body" => $template_data['sms_message_body'], "sent_to" => $send_to_phone));
                                    $inserted_id = $obj->con->insert_id;
                                    if ($inserted_id) {
                                        $sms_response = $setupSMS->send_sms($send_to_phone, $template_data['sms_message_body'] . "\nhttps://lsuk.org/co.php?i=" . $inserted_id);
                                        if ($sms_response['status'] == 0) {
                                            $obj->update("job_messages", array("status" => 0), "id=" . $inserted_id);
                                        }
                                    }
                                }
                            }
                        }
                    } else {
                        $updated_array["status"]=2;
                        $obj->update($row['table_name'], $updated_array, "id=" . $row['id']);
                        continue;
                    }
                    
                }
                $interpreter_email = $template_data['interpreter_email'];
                if($row['template_type'] == 6 && !empty($interpreter_email)){
                    $table = $order_types[$row['order_type']];
                    $tms = "https://lsuk.org/lsuk_system/reports_lsuk/pdf/new_timesheet.php?update_id=" . $row['order_id'] . "&table=$table&down&emailto=" . $interpreter_email . "&send_sms=1&cron_tms=1";
                    $gen_tm = callTMS($tms);
                }
            }
            /*$replacements = array(
                "name"=>$template_data['name'],"assignTime"=>$template_data['assignTime'],"assignDate"=>$template_data['assignDate'],"postCode"=>$template_data['postCode'],
                "department_name"=>$template_data['department_name'],"reference_no"=>$template_data['reference_no'],"total_fee"=>$template_data['total_fee'],"dep_followup"=>$template_data['dep_followup'],"department_updated_date"=>$template_data['department_updated_date'],
                "epr_name"=>$template_data['epr_name'],"epr_cnic"=>$template_data['epr_cnic'],"epr_phn"=>$template_data['epr_phn'],"updated_date"=>$template_data['updated_date'],
                "epr_bank_name"=>$template_data['epr_bank_name'],"transaction_id"=>$template_data['transaction_id'],"bank_deposit_date"=>$template_data['bank_deposit_date'],
                "registration_date"=>$template_data['registration_date'],"generated_password"=>$template_data['generated_password']
            );
            $message_body=str_replace(array_keys($replacements),$replacements,$templates[$row['template_type']]);*/
            try{
                $mail->Username   = $send_from;
                $mail->Password   = $row['send_password'];
                // $mail->Username   = 'info@lsuk.org';
                // $mail->Password   = 'LangServ786';
                $mail->setFrom($send_from, 'LSUK');
                $mail->addAddress($row['send_to']);
                $mail->addReplyTo($send_from, 'LSUK');
                $mail->isHTML(true);
                $mail->Subject = $row['subject'];
                $mail->Body    = $row['template_body'];
                if($mail->send()){
                    if($row['template_type'] == 6){
                        $mail->ClearAllRecipients();
                        $mail->addAddress(setupEmail::LSUK_GMAIL);
                        $mail->addReplyTo($send_from, 'LSUK');
                        $mail->isHTML(true);
                        $mail->Subject = $row['subject'];
                        $mail->Body    = $row['template_body'];
                        $mail->send();
                    }
                    $mail->ClearAllRecipients();
                    $updated_array["status"]=1;
                    $counter++;
                }else{
                    $updated_array["status"]=2;
                }
                //Send notification on APP
                /*$check_id=$obj->read_specific('id','notify_new_doc','interpreter_id='.$template_data['interpreter_id'])['id'];
                if(empty($check_id)){
                    $obj->insert('notify_new_doc',array("interpreter_id"=>$template_data['interpreter_id'],"status"=>'1'));
                }else{
                    $existing_notification=$obj->read_specific("new_notification","notify_new_doc","interpreter_id=".$template_data['interpreter_id'])['new_notification'];
                    $obj->update('notify_new_doc',array("new_notification"=>$existing_notification+1),array("interpreter_id"=>$template_data['interpreter_id']));
                }
                $array_tokens=explode(',',$obj->read_specific("GROUP_CONCAT( DISTINCT token) as tokens","int_tokens","int_id=".$template_data['interpreter_id'])['tokens']);
                if(!empty($array_tokens)){
                    $obj->insert('app_notifications',array("title"=>$template_data['app_title'],"app_sub_title"=>$template_data['app_sub_title'],"dated"=>date('Y-m-d'),"int_ids"=>$template_data['interpreter_id'],"read_ids"=>$template_data['interpreter_id'],"type_key"=>$template_data['type_key']));
                    foreach($array_tokens as $token){
                        if(!empty($token)){
                            $obj->notify($token,$template_data['app_subject'],$template_data['app_sub_title'],array("type_key"=>$template_data['type_key'],"job_type"=>$template_data['job_type']));
                        }
                    }
                }*/
            } catch (Exception $e) {
                $updated_array["status"] = 2;
            }
        }else{
            $updated_array['status'] = 2;
        }
        $obj->update($row['table_name'], $updated_array, "id=" . $row['id']);
    }
}