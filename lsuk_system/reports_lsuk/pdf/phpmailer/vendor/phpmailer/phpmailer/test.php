<?php
// Import PHPMailer classes into the global namespace
// These must be at the top of your script, not inside a function
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Load Composer's autoloader
require '../../../vendor/autoload.php';

// Instantiation and passing `true` enables exceptions
$mail = new PHPMailer(true);

try {
    //Server settings
    $mail->SMTPDebug = 0; 
    // $mail->SMTPDebug = SMTP::DEBUG_SERVER;                      // Enable verbose debug output
    $mail->isSMTP();                                            // Send using SMTP
    $mail->Host       = 'c59754.sgvps.net';                    // Set the SMTP server to send through
    $mail->SMTPAuth   = true;                                   // Enable SMTP authentication
    $mail->Username   = '_mainaccount@solworx.co.uk';                     // SMTP username
    $mail->Password   = 'piuMSDarnAaK1tVv48EL@';                               // SMTP password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;         // Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` also accepted
    $mail->Port       = 25;                                    // TCP port to connect to

    //Recipients
    $mail->setFrom('info@lsuk.org', 'LSUK');
    //$mail->addAddress('info@lsuk.org', 'Imran Shah');     // Add a recipient
    $mail->addAddress('waqarecp1992@outlook.com');               // Name is optional
    $mail->addReplyTo('info@lsuk.org', 'LSUK');
    $mail->addCC('waqarecp1992@gmail.com');
    $mail->addBCC('waqarecp1992@gmail.com');

    // Attachments
    $mail->addAttachment('README.md');         // Add attachments
    $mail->addAttachment('README.md', 'Read me File');    // Optional name

    // Content
    $mail->isHTML(true);                                  // Set email format to HTML
    $mail->Subject = 'Here is the subject';
    $mail->Body    = 'This is the HTML message body <b>in bold!</b>';
    $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';
$var=$mail->send();
    if($var){
    echo 'Message has been sent';
    }
} catch (Exception $e) {
    // echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    // echo "Message could not be sent. Mailer Error!";
}