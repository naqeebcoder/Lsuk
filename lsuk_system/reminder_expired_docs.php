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
        "interpreter_reg.id, interpreter_reg.name, interpreter_reg.email, interpreter_reg.dbs_expiry_date, interpreter_reg.id_doc_expiry_date, interpreter_reg.work_evid_expiry_date, interpreter_reg.is_dbs_auto, interpreter_reg.dbs_auto_number, interpreter_reg.uk_citizen,interpreter_reg.interp",
        "interpreter_reg",
        "interpreter_reg.deleted_flag=0 AND interpreter_reg.is_temp=0 AND interpreter_reg.isAdhoc=0 AND interpreter_reg.email!='' 
        AND interpreter_reg.active='0' AND (interpreter_reg.actnow='Active' OR (interpreter_reg.actnow='Inactive' AND CURRENT_DATE() NOT BETWEEN interpreter_reg.actnow_time AND interpreter_reg.actnow_to))
        AND (
            (interpreter_reg.interp='Yes' AND interpreter_reg.is_dbs_auto=0 AND interpreter_reg.dbs_expiry_date != '1001-01-01' AND DATEDIFF(interpreter_reg.dbs_expiry_date, CURRENT_DATE()) <= 30)
            OR 
            (interpreter_reg.uk_citizen=1 AND interpreter_reg.id_doc_expiry_date != '1001-01-01' AND DATEDIFF(interpreter_reg.id_doc_expiry_date, CURRENT_DATE()) <= 30)
            OR 
            (interpreter_reg.uk_citizen=0 AND interpreter_reg.work_evid_expiry_date != '1001-01-01' AND DATEDIFF(interpreter_reg.work_evid_expiry_date, CURRENT_DATE()) <= 30)
        ) 
        AND (interpreter_reg.last_expiry_reminder IS NULL OR interpreter_reg.last_expiry_reminder <= DATE_SUB(NOW(), INTERVAL 14 DAY)) 
        LIMIT 20"
    );
$subject = 'Reminder about documents expiry';
$email_body = $obj->read_specific("em_format", "email_format", "id=49")['em_format'];
while ($row_reminder = $get_data->fetch_assoc()) {
        $missed = "";$allowed_to_send = false;
        if ($row_reminder['email']) {
                if ($row_reminder['interp'] == "Yes" && $row_reminder['is_dbs_auto'] == 0 && $row_reminder['dbs_expiry_date'] && $row_reminder['dbs_expiry_date'] != '1001-01-01') {
                        $missed .= getExpiryMessage($row_reminder['dbs_expiry_date'], "DBS", $misc->dated($row_reminder['dbs_expiry_date']));
                        $allowed_to_send = true;
                }
                if ($row_reminder['uk_citizen'] == 1 && $row_reminder['id_doc_expiry_date'] && $row_reminder['id_doc_expiry_date'] != '1001-01-01') {
                        $missed .= getExpiryMessage($row_reminder['id_doc_expiry_date'], "Identity Document", $misc->dated($row_reminder['id_doc_expiry_date']));
                        $allowed_to_send = true;
                }
                if ($row_reminder['uk_citizen'] == 0 && $row_reminder['work_evid_expiry_date'] && $row_reminder['work_evid_expiry_date'] != '1001-01-01') {
                        $missed .= getExpiryMessage($row_reminder['work_evid_expiry_date'], "Right to work document", $misc->dated($row_reminder['work_evid_expiry_date']));
                        $allowed_to_send = true;
                }
                if (!$allowed_to_send || $missed == "") {
                        continue;
                }
                $array_expired_docs = array("interpreter_id" => $row_reminder['id'], "body" => $missed, "created_date" => $datetime);
                try {
                        $data   = ["[INTERPRETER_NAME]", "[DOCUMENTS_DETAILS]"];
                        $to_replace  = [ucwords($row_reminder['name']), $missed];
                        $message = str_replace($data, $to_replace, $email_body);
                        $mail->setFrom(setupEmail::HR_EMAIL, setupEmail::FROM_NAME);
                        $mail->addAddress($row_reminder['email']);
                        $mail->addReplyTo(setupEmail::HR_EMAIL, setupEmail::FROM_NAME);
                        $mail->isHTML(true);
                        $mail->Subject = $subject;
                        $mail->Body    = $mail->msgHtml($message);
                        if ($mail->send()) {
                                $mail->ClearAllRecipients();
                                $array_expired_docs['status'] = 1;
                        } else {
                                $array_expired_docs['status'] = 2;
                        }
                } catch (Exception $e) {
                        $array_expired_docs['status'] = 2;
                }
                $obj->update("interpreter_reg", array("last_expiry_reminder" => $datetime), "id=" . $row_reminder['id']);
                $obj->insert('expired_notifications', $array_expired_docs);
        }
}

function getExpiryMessage($expiryDate, $documentName, $formated_expiry_date)
{
        $message = "";
        // echo "<br>". $documentName ." - " . " - " . $difference . "<br>";
        if ($expiryDate < date("Y-m-d")) {
                $message .= "<b style=color:red>$documentName is expired</b><br>";
        } else {
                $today = new DateTime();
                $expiryDate = new DateTime($expiryDate);
                $difference = $today->diff($expiryDate)->days;
                if ($difference <= 30) {
                        $message .= "<b style=color:red>$documentName is due to expire on " . $formated_expiry_date . "</b><br>";
                }
        }

        return $message;
}
