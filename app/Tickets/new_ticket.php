<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
require '../../lsuk_system/phpmailer/vendor/autoload.php';
include '../action.php';
//Send a ticket
if(isset($_POST['new_ticket'])){
    $json=(object) null;
    if(isset($_POST['ap_user_id']) && !empty($_POST['ap_user_id'])){
        if($obj->insert("tickets",array("interpreter_id"=>$_POST['ap_user_id'],"title"=>$_POST['title'],"details"=>$_POST['details']))){
            $json->msg="Your ticket has been sent. We will respond back soon.";
            $mail = new PHPMailer(true);
            try {
                $interpeter_email=$obj->read_specific("name,email","interpreter_reg","id=".$_POST['ap_user_id']);
                $from_add = "hr@lsuk.org";
                $from_name = "LSUK Admin Team";
                $mail->SMTPDebug = 1;
                //$mail->isSMTP(); 
                //$mailer->Host = 'smtp.office365.com';
                $mail->SMTPAuth   = true;
                $mail->Username   = 'info@lsuk.org';
                $mail->Password   = 'LangServ786';
                $mail->SMTPSecure = 'tls';
                $mail->Port       = 587;
                $mail->setFrom($from_add,$from_name);
                $mail->addAddress($interpeter_email["email"]);
                $mail->addReplyTo($from_add,$from_name);
                $mail->isHTML(true);
                $mail->Subject = "We have received your ticket.";
                $mail->Body = "Hello ".$interpeter_email["name"].",<br>
                We have received your ticket from LSUK App.<br>
                Title: ".$_POST['title']."<br>
                Details: ".$_POST['details']."<br>
                Submitted on: ".date('Y-m-d H:i:s')."<br>
                We will resolve your issue and will respond back soon.<br>
                You can find an update to your ticket status in your tickets screen.<br>
                Thank you.<br>
                Best regards,<br>
                LSUK";
                if($mail->send()){
                    $mail->ClearAllRecipients();
                    $mail->addAddress('hr@lsuk.org');
                    $mail->addReplyTo($interpeter_email["email"], $interpeter_email["name"]);
                    $mail->isHTML(true);
                    $mail->Subject = "New ticket from LSUK App";
                    $mail->Body    = "<b>".$interpeter_email["name"]."</b> has sumitted new ticket,<br>
                    Details are below:<br>
                    Title: ".$_POST['title']."<br>
                    Details: ".$_POST['details']."<br>
                    Submitted on: ".date('Y-m-d H:i:s')."<br>
                    Best regards,<br>
                    LSUK App";
                    $mail->send();
                    $mail->ClearAllRecipients();
                }else{
                    $json->msg="Failed to send email!";
                }
            } catch (Exception $e) {
                $json->mailer="Email Library Error!";
            }
        }else{
            $json->msg="Failed to submit your ticket! Try again later.";
        }
    }else{
        $json->msg="not_logged_in";
    }
    header('Content-Type: application/json');
    echo json_encode($json, JSON_UNESCAPED_UNICODE);
}
?>