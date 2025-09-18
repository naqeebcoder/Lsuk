<?php
//php mailer library
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
require 'phpmailer/vendor/autoload.php';
$mail = new PHPMailer(true);
include 'db.php';
include 'class_new.php';
$from_email = "payroll@lsuk.org";
$dated=date('Y-m-d H:i:s');
$tomorrow = date("Y-m-d", strtotime("+ 1 day"));
$query_reminder="SELECT concat('Face to Face Assignment Reminder on ',interpreter.assignDate,' at ',interpreter.assignTime) as title,interpreter.id as job_id,interpreter.intrpName as interpreter_id,interpreter.assignDate,interpreter.assignTime,interpreter.assignDur,interpreter.postCode,interpreter_reg.name as interpreter_name,interpreter_reg.email as interpreter_email,'day_before' as type,'".$dated."' as inserted_date,'interpreter' as table_type FROM interpreter,interpreter_reg WHERE interpreter.intrpName=interpreter_reg.id and interpreter.deleted_flag=0 and interpreter.orderCancelatoin=0 and interpreter.order_cancel_flag=0 and interpreter.hoursWorkd=0 and interpreter.intrp_salary_comit=0 and interpreter.assignDate = '".$tomorrow."'";
$result_reminder=mysqli_query($con,$query_reminder);
$em_format=$obj_new->read_specific("em_format","email_format","id=32")['em_format'];
while($row_reminder=mysqli_fetch_assoc($result_reminder)){ 
$obj_new->insert('job_reminder',$row_reminder);
$to_address = $row_reminder['interpreter_email'];
$subject = $row_reminder['title'];
if($row_reminder['assignDur']>60){
    $hours=$row_reminder['assignDur'] / 60;
    if(floor($hours)>1){$hr="hours";}else{$hr="hour";}
    $mins=$row_reminder['assignDur'] % 60;
    if($mins==00){
        $get_dur=sprintf("%2d $hr",$hours);  
    }else{
        $get_dur=sprintf("%2d $hr %02d minutes",$hours,$mins);  
    }
}else if($row_reminder['assignDur']==60){
    $get_dur="1 Hour";
}else{
    $get_dur=$row_reminder['assignDur']." minutes";
}
$data_title=" at ".$row_reminder['assignTime']." on ".$row_reminder['assignDate']." on " .$row_reminder['postCode']." which has a duration of ".$get_dur;
$data   = ["[INTERPRETER_NAME]", "[DATA_TITLE]"];
$to_replace  = [$row_reminder['interpreter_name'], $data_title];
$message=str_replace($data, $to_replace,$em_format);
try {
    $mail->SMTPDebug = 0;
    // $mail->isSMTP(); 
    $mail->Host = 'smtp.office365.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'info@lsuk.org';
    $mail->Password   = 'xtxwzcvtdbjpftdj';
    $mail->SMTPSecure = 'tls';
    $mail->Port       = 587;//25 working
    $mail->setFrom($from_email, 'LSUK Job Reminder');
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
} ?>