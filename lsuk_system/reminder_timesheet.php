<?php
/*php mailer library
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
require 'phpmailer/vendor/autoload.php';
$mail = new PHPMailer(true);
include 'db.php';
include 'class_new.php';
$dated=date('Y-m-d H:i:s');
$dated_format=date('Y-m-d');
$tomorrow = date("Y-m-d", strtotime("+ 1 day"));
$from_email='payroll@lsuk.org';
$query_reminder="SELECT 'Timesheet Reminder for Face To Face ' as title,interpreter.id as job_id,interpreter.intrpName as interpreter_id,interpreter.assignDate,interpreter.assignTime,interpreter.assignDur,interpreter.source,interpreter_reg.name as interpreter_name,interpreter_reg.email as interpreter_email,'".$dated."' as inserted_date,'interpreter' as table_type FROM interpreter,interpreter_reg WHERE interpreter.intrpName=interpreter_reg.id and interpreter.intrpName!='' and interpreter.aloct_by!='' and interpreter.hoursWorkd=0 and interpreter.deleted_flag=0 and interpreter.orderCancelatoin=0 and interpreter.order_cancel_flag=0 and '".$dated_format."' > interpreter.assignDate AND interpreter.assignDate<='".date('Y-m-d',(strtotime ( '-2 day' , strtotime ( $dated) ) ))."' ORDER by interpreter.assignDate";
$result_reminder=mysqli_query($con,$query_reminder);
$em_format=$obj_new->read_specific("em_format","email_format","id=33")['em_format'];
while($row_reminder=mysqli_fetch_assoc($result_reminder)){
$obj_new->insert_array('timesheet_reminder',$row_reminder);
$to_address = trim($row_reminder['interpreter_email']);
$subject = $row_reminder['title'].' assignment on '.$row_reminder['assignDate'].' at '.$row_reminder['assignTime'];
$data_title=$row_reminder['assignDate']." at ".$row_reminder['assignTime'];
$data   = ["[INTERPRETER_NAME]", "[DATA_TITLE]"];
$to_replace  = [$row_reminder['interpreter_name'], $data_title];
$message=str_replace($data, $to_replace,$em_format);
try {
    $mail->SMTPDebug = 0;
    //$mail->isSMTP(); 
    //$mailer->Host = 'lsuk-org.mail.protection.outlook.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'info@lsuk.org';
    $mail->Password   = 'LangServ786';
    $mail->SMTPSecure = 'tls';
    $mail->Port       = 587;//25 working
    $mail->setFrom($from_email, 'LSUK Timesheet Reminder');
    $mail->addAddress($to_address);
    $mail->addReplyTo($from_email, 'LSUK');
    $mail->isHTML(true);
    $mail->Subject = $subject;
    $mail->Body    = $message;
    $mail->send();
    $mail->ClearAllRecipients();
} catch (Exception $e) { ?>
<script>alert("Message could not be sent! Mailer library error.");</script>
<?php }
}*/
?>