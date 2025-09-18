<?php
//php mailer library
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
require 'phpmailer/vendor/autoload.php';
$mail = new PHPMailer(true);
include 'db.php';
include 'class_new.php';
$dated= date('Y-m-d h:i:s');
$query_reminder="SELECT DISTINCT interpreter_reg.id as interpreter_id,interpreter_reg.name, interpreter_reg.email,'".$dated."' as dated FROM interpreter_reg WHERE interpreter_reg.active='0' AND (interpreter_reg.actnow='Active' OR (interpreter_reg.actnow='Inactive' AND CURRENT_DATE() NOT BETWEEN interpreter_reg.actnow_time AND interpreter_reg.actnow_to)) AND interpreter_reg.deleted_flag=0 and interpreter_reg.password=''";
$result_reminder=mysqli_query($con,$query_reminder);
$em_format=$obj_new->read_specific("em_format","email_format","id=34")['em_format'];
$from_email = "hr@lsuk.org";
while($row_reminder=mysqli_fetch_assoc($result_reminder)){
$obj_new->insert_array('password_reminder',$row_reminder);
$to_address = trim($row_reminder['email']);
$new_password='LSUK_'.substr(str_shuffle('0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ') , 0 , 6 ).str_shuffle('!@');
$obj_new->update('interpreter_reg',array('password'=>$new_password),array("id"=>$row_reminder['interpreter_id']));
$subject = "LSUK Password Change Notification!";
$data   = ["[NEW_PASSWORD]"];
$to_replace  = [$new_password];
$message=str_replace($data, $to_replace,$em_format);
if($to_address){
    try {
        $mail->SMTPDebug = 0;
        //$mail->isSMTP(); 
        //$mailer->Host = 'lsuk-org.mail.protection.outlook.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'info@lsuk.org';
        $mail->Password   = 'LangServ786';
        $mail->SMTPSecure = 'tls';
        $mail->Port       = 587;//25 working
        $mail->setFrom($from_email, 'LSUK Password Reminder');
        $mail->addAddress($to_address);
        $mail->addReplyTo($from_email, 'LSUK');
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $message;
        $mail->send();
        $mail->ClearAllRecipients();
        } catch (Exception $e) { 
            echo "Message could not be sent! Mailer library error for: ".$row_reminder['id'];
        }
    }
}
?>
