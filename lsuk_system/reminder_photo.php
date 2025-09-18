<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
require 'phpmailer/vendor/autoload.php';
$mail = new PHPMailer(true);
include 'actions.php';
$result_reminder=$obj->read_all("interpreter_reg.id as interpreter_id,interpreter_reg.name as interpreter_name,interpreter_reg.email as interpreter_email","interpreter_reg","deleted_flag=0 AND is_temp=0 AND pic_updated=0 and 
interpreter_reg.active='0' AND (interpreter_reg.actnow='Active' OR (interpreter_reg.actnow='Inactive' AND CURRENT_DATE() NOT BETWEEN interpreter_reg.actnow_time AND interpreter_reg.actnow_to)) LIMIT 0,30");
$from_email = "hr@lsuk.org";
$subject = 'Requesting Portfolio Photos';
$em_format=$obj->read_specific("em_format","email_format","id=43")['em_format'];
$sub_title="Please send a camera facing photo.\nDon't cover your face with mask or anything else. Images in sunglasses aren't acceptable too.";
$type_key="up";
while($row_reminder=$result_reminder->fetch_assoc()){
    $array_tokens=explode(',',$obj->read_specific("GROUP_CONCAT( DISTINCT token) as tokens","int_tokens","int_id=".$row_reminder['interpreter_id'])['tokens']);
    if(!empty($array_tokens)){
        foreach($array_tokens as $token){
            if(!empty($token)){
                $obj->notify($token,"📍 ".$subject,"📸 ".$sub_title,array("type_key"=>$type_key));
            }
        }
    }
  if($row_reminder['interpreter_email']){
    try {
        $mail->SMTPDebug = 0;
        //$mail->isSMTP(); 
        //$mailer->Host = 'lsuk-org.mail.protection.outlook.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'info@lsuk.org';
        $mail->Password   = 'LangServ786';
        $mail->SMTPSecure = 'tls';
        $mail->Port       = 587;//25 working
        $data   = ["[INTERPRETER_NAME]"];
        $to_replace  = [$row_reminder['interpreter_name']];
        $message=str_replace($data, $to_replace,$em_format);
        $mail->Sender = $from_email;
        $mail->setFrom($from_email, 'LSUK');
        $mail->addAddress($row_reminder['interpreter_email']);
        $mail->addReplyTo($from_email, 'LSUK');
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $mail->msgHtml($message);
        $mail->send();
        $mail->ClearAllRecipients();
    } catch (Exception $e) {
        echo "Message could not be sent! Mailer library error.".$row_reminder['interpreter_id'];
    }
  }
}
?>