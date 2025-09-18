<?php
session_start();
include '../../source/setup_email.php';
// Reject an interpreter
if (isset($_POST['btn_reject_interpreter'])) {
    include '../actions.php';
    $is_int_notified = null;
    $update_array = array("deleted_flag" => 1, "deleted_by" => $_SESSION['UserName'], "deleted_date" => date("Y-m-d H:i:s"));
    $reject_reason = trim($_POST['reject_reason']);
    if (isset($_POST['notify_interpreter']) && $_POST['interpreter_email']) {
        $append_notification = "\nInterpreter notified on email";
    }
    $update_array['reject_reason'] = $reject_reason . $append_notification;
    $done = $obj->update("interpreter_reg", $update_array, "id=" . $_POST['interpreter_id']);
    if ($done) {
        $obj->insert("daily_logs", array("action_id" => 26, "user_id" => $_SESSION['userId'], "details" => "Interpreter ID: " . $_POST['interpreter_id']));
        if (isset($_POST['notify_interpreter']) && $_POST['interpreter_email']) {
            try {
                $row_format_update = $obj->read_specific("em_format", "email_format", "id=47");
                $data_replace   = ["[INTERPRETER]", "[REJECTION_REASON]", "[LSUK_ADMIN_TEAM]"];
                $to_replace  = [ucwords($_POST['interpreter_name']), $reject_reason, $_SESSION['UserName']];
                $message_body = str_replace($data_replace, $to_replace, $row_format_update['em_format']);
                $mail->SMTPDebug = 0;
                $mail->isSMTP();
                $mail->Host = setupEmail::EMAIL_HOST;
                $mail->SMTPAuth   = true;
                $mail->Username   = setupEmail::HR_EMAIL;
                $mail->Password   = setupEmail::HR_PASSWORD;
                $mail->SMTPSecure = setupEmail::SECURE_TYPE;
                $mail->Port       = setupEmail::SENDING_PORT;
                $mail->setFrom(setupEmail::HR_EMAIL, setupEmail::FROM_NAME);
                $mail->addAddress($_POST['interpreter_email']);
                $mail->addReplyTo(setupEmail::HR_EMAIL, setupEmail::FROM_NAME);
                $mail->isHTML(true);
                $mail->Subject = "LSUK update on your registration";
                $mail->Body    = $message_body;
                if ($mail->send()) {
                    $mail->ClearAllRecipients();
                    $is_int_notified = " Email has been sent to interpreter";
                } else {
                    $is_int_notified = " Email could not be sent to interpreter";
                }
            } catch (Exception $e) {
                $is_int_notified = " Email could not be sent to interpreter due to Mailer";
            }
        }
        $_SESSION['returned_message'] = '<center><div class="alert alert-success alert-dismissible show col-md-8 col-md-offset-2" role="alert">
            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
            <strong>Success!</strong> Account status has been updated successfully! ' . $is_int_notified . '
            </div></center>';
    } else {
        $_SESSION['returned_message'] = '<center><div class="alert alert-danger alert-dismissible show col-md-8 col-md-offset-2" role="alert">
            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
            <strong>Sorry!</strong> Failed to update the status of this Account! Please try again
            </div></center>';
    }
    header('Location: ' . $_POST['redirect_url']);
}