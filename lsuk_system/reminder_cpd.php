<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
require 'phpmailer/vendor/autoload.php';
$mail = new PHPMailer(true);
include 'actions.php';
/*$result_reminder=$obj->read_all("interpreter_reg.id as interpreter_id,interpreter_reg.name as interpreter_name,interpreter_reg.email as interpreter_email","interpreter_reg","deleted_flag=0 AND is_temp=0 and 
interpreter_reg.active='0' AND (interpreter_reg.actnow='Active' OR (interpreter_reg.actnow='Inactive' AND CURRENT_DATE() NOT BETWEEN interpreter_reg.actnow_time AND interpreter_reg.actnow_to)) AND interpreter_reg.city IN ('Bristol','Bath','Weston Super mare','Swindon','Cardiff','Newport','Somerset','Gloucester','Chippenham','Cheltenham','Bridgewater','Frome','Taunton','Wiltshire','Wells') and id NOT IN 
(SELECT DISTINCT cpd_events.interpreter_id from cpd_events WHERE cpd_events.event_id=1) LIMIT 30");*/
$result_reminder=$obj->read_all("cpd_events.id,cpd_events.reply,cpd_events.attend_type,interpreter_reg.id as interpreter_id,interpreter_reg.name as interpreter_name,interpreter_reg.email as interpreter_email","cpd_events,interpreter_reg","cpd_events.interpreter_id=interpreter_reg.id AND cpd_events.reply IN (0,1) LIMIT 390,30");
$from_email = "hr@lsuk.org";
$subject = 'CPD event will be held remotely via ZOOM';
$em_format="Dear <b>[INTERPRETER_NAME]</b>,<br>
Hope you are well.<br>
The upcoming CPD event on <h3 style='display: inline;'>Friday 21.01.2022</h3> between 09:30 to 12:30 in Bristol will be arranged via <b>ZOOM</b>. This will last approximately 3 hours. We aim to cover the following topics.<br>
1) Working as a Professional Interpreter<br>
2) Working with patients with Mental Health issues<br>
3) Getting the balance back - Interpreters Wellbeing<br>
All attendees will be issued a certificate of the course.<br>
Free to attend<br>
Please confirm your attendance by using below link:<br><br>
<strong><a style='text-decoration: none; border: 1px solid; padding: 4px; border-radius: 4px; color: #002060;cursor: pointer;' title='Event Reply Form' href='[LINK]' target='_blank'>CLICK HERE</a></strong><br><br>
Kind Regards,<br>
LSUK Admin Team";
while($row_reminder=$result_reminder->fetch_assoc()){
  if($row_reminder['interpreter_email']){
    $id=$row_reminder['id'];
    if($row_reminder['reply']==1 && $row_reminder['attend_type']==0){
      $updated_array=array("reply"=>0,"attend_type"=>1);
    }else{
      $updated_array=array("attend_type"=>1);
    }
    $obj->update("cpd_events",$updated_array,"id=".$id);
    $link="https://lsuk.org/cpd_reply.php?id=".base64_encode($id);
    try {
        $mail->SMTPDebug = 0;
        //$mail->isSMTP(); 
        //$mailer->Host = 'lsuk-org.mail.protection.outlook.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'info@lsuk.org';
        $mail->Password   = 'LangServ786';
        $mail->SMTPSecure = 'tls';
        $mail->Port       = 587;//25 working
        $data   = ["[INTERPRETER_NAME]","[LINK]"];
        $to_replace  = [$row_reminder['interpreter_name'],$link];
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