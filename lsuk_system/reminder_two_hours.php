<?php
die;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
require 'phpmailer/vendor/autoload.php';
$mail = new PHPMailer(true);
include 'db.php';
include 'class_new.php';
$now =  date('Y-m-d H:i:s');
$time   = strtotime($now);
$time   = $time - (-2*60*60); //add 2 hours
$beforeOneHour = date("H:i", $time);
$dated=date('Y-m-d H:i:s');
$today= date("Y-m-d");
$query_reminder="SELECT concat('Face to Face Assignment Reminder for ',interpreter.assignDate,' at ',interpreter.assignTime) as title,interpreter.id as job_id,interpreter.intrpName as interpreter_id,interpreter.assignDate,interpreter.assignTime,interpreter.assignDur,interpreter.postCode,interpreter_reg.name as interpreter_name,interpreter_reg.email as interpreter_email,'2_hours_before' as type,'".$dated."' as inserted_date,'interpreter' as table_type FROM interpreter,interpreter_reg WHERE interpreter.intrpName=interpreter_reg.id and interpreter.deleted_flag=0 and interpreter.orderCancelatoin=0 and interpreter.order_cancel_flag=0 and interpreter.assignDate = '".$today."' and SUBSTR(interpreter.assignTime, 1, 5) ='".$beforeOneHour."'
UNION ALL
SELECT concat('Telephone Assignment Reminder for ',telephone.assignDate,' at ',telephone.assignTime) as title,telephone.id as job_id,telephone.intrpName as interpreter_id,telephone.assignDate,telephone.assignTime,telephone.assignDur,'',interpreter_reg.name as interpreter_name,interpreter_reg.email as interpreter_email,'2_hours_before' as type,'".$dated."' as inserted_date,'telephone' as table_type FROM telephone,interpreter_reg WHERE telephone.intrpName=interpreter_reg.id and telephone.deleted_flag=0 and telephone.orderCancelatoin=0 and telephone.order_cancel_flag=0 and telephone.assignDate = '".$today."' and SUBSTR(telephone.assignTime, 1, 5) ='".$beforeOneHour."'";
$result_reminder=mysqli_query($con,$query_reminder);
if(mysqli_num_rows($result_reminder)>0){
while($row_reminder=mysqli_fetch_assoc($result_reminder)){ 
$obj_new->insert_array('job_reminder',$row_reminder);
$to_address = $row_reminder['interpreter_email'];
$subject = $row_reminder['title'];
$message = "<p>Dear <strong>".$row_reminder['interpreter_name']."</strong>,</p>
          <p>We are writing to let you know that you are attending an assignment at ".$row_reminder['assignTime']." on ".$row_reminder['assignDate']." at ".$row_reminder['postCode']." which has a duration of ".$row_reminder['assignDur']." Minutes / Hours. </p>
          <p>We strongly advice you to use a diary or electronic planner for all your assignments. Don't double book yourself.</p>
          <p>Please make sure you prpare yourself for the topic of the session.</p>
          <p>Don't forget to take your badge and timesheet with you and wear appropriate dresses. Unprofessional appearance can impact on LSUK and your feedback.</p>
          <p>Plan you journey route and time beforehand and leave sufficient travel time to avoid late arrivals. All Latenesses between 5- 30 minutes are fineable however no payment would be made if interpreter's arrival was 30 minutes or more after the start time of the session.</p>
          <p>No attendance and last minutes cancellation aren't acceptable. We will recover 100% of your per hour pay from your previous pay.</p>
          <p>We will pay you 100% of the assignment fee for all jobs cancelled on the day of the session or if cancelled after 6pm the day before. </p>
          <p>We need to know if there are future jobs or any cancellations if any. Don't forget to record your availablity for all furture jobs.</p> 
          <p>Please dont forget you can download your timesheet <a href='https://lsuk.org/login.php'><u>HERE</u></a></p>
          <p>Best regards </p>
          <p>LSUK Admin Team</p>
          <p>Thank you</p>
          ";
try {
    $mail->SMTPDebug = 0;
    // $mail->isSMTP(); 
    $mail->Host = 'smtp.office365.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'info@lsuk.org';
    $mail->Password   = 'xtxwzcvtdbjpftdj';
    $mail->SMTPSecure = 'tls';
    $mail->Port       = 587;//25 working
    $mail->setFrom('imran.lsukltd@gmail.com', 'LSUK Job Reminder');
    $mail->addAddress($to_address);
    $mail->addReplyTo('info@lsuk.org', 'LSUK');
    $mail->isHTML(true);
    $mail->Subject = $subject;
    $mail->Body    = $message;
    $mail->send();
    $mail->ClearAllRecipients();
} catch (Exception $e) { ?>
<script>alert("Message could not be sent! Mailer library error.");</script>
<?php }
}
}
?>