 <?php //use PHPMailer\PHPMailer\PHPMailer;
// use PHPMailer\PHPMailer\SMTP;
// use PHPMailer\PHPMailer\Exception;
// require '../lsuk_system/phpmailer/vendor/autoload.php';
// $mail = new PHPMailer(true);
// try {
//     $from_add = "info@lsuk.org";
//     $from_name = "LSUK Admin Team";
//     $mail->SMTPDebug = 1;
//     //$mail->isSMTP(); 
//     //$mailer->Host = 'smtp.office365.com';
//     $mail->SMTPAuth   = true;
//     $mail->Username   = 'info@lsuk.org';
//     $mail->Password   = 'LangServ786';
//     $mail->SMTPSecure = 'tls';
//     $mail->Port       = 587;
//     $mail->setFrom($from_add,$from_name);
//     $mail->addAddress("martiksa@icloud.com");
//     $mail->addReplyTo($from_add,$from_name);
//     $mail->isHTML(true);
//     $mail->Subject = "Test email notification";
//     $mail->Body = "Hello <b>Marta</b>,<br>
//     Waqar here,<br>
//     Let me know when you received this notification.<br>
//     Thank you.<br>
//     Best regards,<br>
//     LSUK Technical Team";
//     if($mail->send()){
//         $mail->ClearAllRecipients();
//     }else{
//         echo "Failed to send email!";
//     }
// } catch (Exception $e) {
//     echo "Email Library Error!";
// } ?>