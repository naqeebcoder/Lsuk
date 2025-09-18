<?php set_include_path('/home/customer/www/lsuk.org/public_html/');
include 'source/setup_email.php';
include 'lsuk_system/actions.php';
// include '../source/setup_email.php';// Use for local testing
// include 'actions.php';// Use for local testing
$datetime = date('Y-m-d H:i:s');
$mail->SMTPDebug = 0;
$mail->isSMTP();
$mail->Host = setupEmail::EMAIL_HOST;
$mail->SMTPAuth   = true;
$mail->Username   = setupEmail::HR_EMAIL;
$mail->Password   = setupEmail::HR_PASSWORD;
$mail->SMTPSecure = setupEmail::SECURE_TYPE;
$mail->Port       = setupEmail::SENDING_PORT;

$get_data = $obj->read_all(
    "interpreter_reg.id as interpreter_id,interpreter_reg.name,interpreter_reg.email,
    concat(CASE WHEN (applicationForm='') THEN '<b style=color:red>* Application Form</b><br>' ELSE '' END ,
    CASE WHEN (interpreter_reg.country_of_origin='') THEN '<b style=color:red>* Country of Origin</b><br>' ELSE '' END,
    CASE WHEN (interpreter_reg.interp_pix='') THEN '<b style=color:red>* Missing Profile Photo</b><br>' ELSE '' END,
    CASE WHEN (interpreter_reg.agreement='') THEN '<b style=color:red>* Agreement Document</b><br>' ELSE '' END,
    CASE WHEN (interpreter_reg.crbDbs='' AND interpreter_reg.interp='Yes') THEN '<b style=color:red>* DBS Document</b><br>' ELSE '' END,
    CASE WHEN (interpreter_reg.ni='') THEN '<b style=color:red>* National Insurance Document</b><br>' ELSE '' END,
    CASE WHEN (interpreter_reg.identityDocument='' AND interpreter_reg.uk_citizen=1) THEN '<b style=color:red>* Identity Document</b><br>' ELSE '' END,
    CASE WHEN (interpreter_reg.work_evid_file='' AND interpreter_reg.uk_citizen=0) THEN '<b style=color:red>* Right To Work Document</b><br>' ELSE '' END,
    CASE WHEN (interpreter_reg.acNo='') THEN '<b style=color:red>* Bank Details</b><br>' ELSE '' END  ,
    CASE WHEN (interpreter_reg.interp_pix='') THEN '<b style=color:red>* Profle Photo</b><br>' ELSE '' END ) as missed,
    '" . $datetime . "' as inserted_date",
    "interpreter_reg",
    "((interpreter_reg.applicationForm='') OR (interpreter_reg.agreement='') OR (interpreter_reg.interp_pix='') OR (interpreter_reg.country_of_origin='') OR
    (interpreter_reg.crbDbs='' AND interpreter_reg.interp='Yes') OR (interpreter_reg.ni='') OR 
    (interpreter_reg.identityDocument='' AND interpreter_reg.uk_citizen=1) OR 
    (interpreter_reg.acNo='')) AND interpreter_reg.email!='' AND interpreter_reg.deleted_flag=0 AND interpreter_reg.is_temp=0 AND interpreter_reg.isAdhoc=0 and interpreter_reg.active='0' AND (interpreter_reg.actnow='Active' OR (interpreter_reg.actnow='Inactive' AND CURRENT_DATE() NOT BETWEEN interpreter_reg.actnow_time AND interpreter_reg.actnow_to)) and interp='Yes' 
    AND (interpreter_reg.last_reminder_sent IS NULL OR interpreter_reg.last_reminder_sent <= DATE_SUB(NOW(), INTERVAL 14 DAY)) 
    LIMIT 20"
);

$subject = 'Reminder about missing/expired documents!';
$email_body = $obj->read_specific("em_format", "email_format", "id=31")['em_format'];
$sub_title = "We are missing some of your information. Please update as a matter of urgency.";
$app_int_ids = array();
$type_key = "md";
while ($row_reminder = $get_data->fetch_assoc()) {
    //Send notification on APP
    $check_id = $obj->read_specific('id', 'notify_new_doc', 'interpreter_id=' . $row_reminder['interpreter_id'])['id'];
    if (empty($check_id)) {
        $obj->insert('notify_new_doc', array("interpreter_id" => $row_reminder['interpreter_id'], "status" => '1'));
    } else {
        $existing_notification = $obj->read_specific("new_notification", "notify_new_doc", "interpreter_id=" . $row_reminder['interpreter_id'])['new_notification'];
        $obj->update('notify_new_doc', array("new_notification" => $existing_notification + 1), "interpreter_id=" . $row_reminder['interpreter_id']);
    }
    $array_tokens = explode(',', $obj->read_specific("GROUP_CONCAT( DISTINCT token) as tokens", "int_tokens", "int_id=" . $row_reminder['interpreter_id'])['tokens']);
    if (!empty($array_tokens)) {
        array_push($app_int_ids, $row_reminder['interpreter_id']);
        foreach ($array_tokens as $token) {
            if (!empty($token)) {
                $obj->notify($token, "📩 " . $subject, $sub_title, array("type_key" => $type_key));
            }
        }
    }
    $array_missing_docs = array("interpreter_id" => $row_reminder['interpreter_id'], "interpreter_name" => ucwords($row_reminder['name']), "interpreter_email" => $row_reminder['email'], "missed" => $row_reminder['missed'], "inserted_date" => $datetime);
    if ($row_reminder['email']) {
        try {
            $data   = ["[INTERPRETER_NAME]", "[MISSED]"];
            $to_replace  = [ucwords($row_reminder['name']), $row_reminder['missed']];
            $message = str_replace($data, $to_replace, $email_body);
            $mail->setFrom(setupEmail::HR_EMAIL, 'LSUK Documents Reminder');
            $mail->addAddress($row_reminder['email']);
            $mail->addReplyTo(setupEmail::HR_EMAIL, setupEmail::FROM_NAME);
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = $mail->msgHtml($message);
            if ($mail->send()) {
                $mail->ClearAllRecipients();
                $array_missing_docs['status'] = 1;
            } else {
                $array_missing_docs['status'] = 2;
            }
        } catch (Exception $e) {
            $array_missing_docs['status'] = 2;
        }
    }
    $obj->update("interpreter_reg", array("last_reminder_sent" => $datetime), "id=" . $row_reminder['interpreter_id']);
    $obj->insert('docs_reminder', $array_missing_docs);
}
$int_distinct_ids = implode(',', array_unique($app_int_ids));
$obj->insert('app_notifications', array("title" => $subject, "sub_title" => $sub_title, "dated" => date('Y-m-d'), "int_ids" => $int_distinct_ids, "read_ids" => $int_distinct_ids, "type_key" => $type_key));
