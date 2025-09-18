<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
require 'phpmailer/vendor/autoload.php';
$mail = new PHPMailer(true);
include 'db.php';
include 'class_new.php';
$dated=date('Y-m-d H:i:s');
//check if cron already executing
$sql = "SELECT * FROM survey_crone WHERE id = 1";
$cron=mysqli_query($con,$sql);
$cron=mysqli_fetch_assoc($cron);
if($cron['start'] == 1){
    exit;
}
$sql = "UPDATE survey_crone SET start = 1 WHERE id = 1";
mysqli_query($con,$sql);

$sql="SELECT * FROM survey WHERE is_sent=0 LIMIT 20";
$users=mysqli_query($con,$sql);
$from_email = "hr@lsuk.org";
$subject = 'Mental Health Survey';
while($user=mysqli_fetch_assoc($users)){
          try {
                $id = base64_encode($user['id']);
                $mail->SMTPDebug = 1;
                //$mail->isSMTP(); 
                //$mailer->Host = 'lsuk-org.mail.protection.outlook.com';
                $mail->SMTPAuth   = true;
                $mail->Username   = 'info@lsuk.org';
                $mail->Password   = 'LangServ786';
                $mail->SMTPSecure = 'tls';
                $mail->Port       = 587;//25 working
                //$mail->AddEmbeddedImage('img/logo.png', 'LSUK');
                $message = "Dear ".$user['name'].'<br>';
                $message .= "<p>My name is Julie Musk and I am responsible for overseeing the contract with LSUK for AWP.</p>";
                $message .= "<p>We are very pleased with the excellent interpreting services you provide and thank you for the support you give our staff and service users.</p>";
                $message .= "<p>We want to find out whether interpreters have the information and support they need to work in mental health settings</p>";
                $message .= "<p>Please click on link below to submit Survey</p>";
                $message .= "<a href='https://lsuk.org/survey.php?token=".$id."'>Survey Link</a>";
                $mail->Sender = $from_email;
                $mail->setFrom($from_email, 'Mental Health Survey');
                $mail->addAddress($user['email']);

                $mail->addReplyTo($from_email, 'LSUK');
                $mail->isHTML(true);
                $mail->Subject = $subject;
                $mail->Body    = $mail->msgHtml($message);
                $mail->send();
                $mail->ClearAllRecipients();
                $sql="UPDATE survey SET is_sent=1 WHERE id = ".$user['id'];
                mysqli_query($con,$sql);
        } catch (Exception $e) {
            echo "Message could not be sent! Mailer library error for: ".$user['id'];
            mysqli_query($con , "INSERT INTO survey_crone_errors (error) VALUES (".$user['id'].")");
            echo '<pre>';
            print_r($e);exit;
        }
}
$sql = "UPDATE survey_crone SET start = 0 WHERE id = 1";
mysqli_query($con,$sql);
?>