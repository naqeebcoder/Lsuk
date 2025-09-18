<?php
/*exit;
file_put_contents('/lsuk.org/public_html/check.txt', $output.'* * * * * NEW_CRON'.PHP_EOL);
echo exec('crontab /tmp/crontab.txt');

exit;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
require 'lsuk_system/phpmailer/vendor/autoload.php';
$mail = new PHPMailer(true);
$from_add='info@lsuk.org';
try {
    $mail->SMTPDebug = 0;
    $mail->SMTPAuth   = true;
    $mail->Username   = 'info@lsuk.org';
    $mail->Password   = 'LangServ786';
    $mail->SMTPSecure = 'tls';
    $mail->Port       = 587;
    $mail->setFrom($from_add, 'LSUK');
    $mail->addAddress('islam.gulshan@gmail.com');
    $mail->addReplyTo($from_add, 'LSUK');
    $mail->isHTML(true);
    $mail->Subject = 'Cron job subject';
    $mail->Body    = 'This is cron job email.<br>Thanks';
    if($mail->send()){
        $mail->ClearAllRecipients();
        echo "sent";
    }
} catch (Exception $e) {
        echo "Library error";
}*/