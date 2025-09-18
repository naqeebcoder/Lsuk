<?php
//php mailer library
// use PHPMailer\PHPMailer\PHPMailer;
// use PHPMailer\PHPMailer\SMTP;
// use PHPMailer\PHPMailer\Exception;
// require 'lsuk_system/phpmailer/vendor/autoload.php';
// $mail = new PHPMailer(true);
 //include 'source/db.php';
 //include 'source/class.php';
 //$query=$acttObj->read_all("comp_credit.orgName AS company,comp_credit.porder AS porder,round((comp_credit.credit - ifnull(test_sum_of_porder.sum_up,0)),2) AS balance,comp_credit.expired AS expired,comp_credit.dated AS dated","comp_credit left join test_sum_of_porder on (convert(comp_credit.porder using utf8) = convert(test_sum_of_porder.porder using utf8))","comp_credit.porder <> ''");
 //while($row=$query->fetch_assoc()){
     //$acttObj->insert("porder_details",array('company'=>$row['company'],'porder'=>$row['porder'],'balance'=>$row['balance'],'expired'=>$row['expired'],'dated'=>$row['dated']));
 //}
// $from_email = "info@lsuk.org";
// $to_email = "waqarecp1992@gmail.com";
// $subject="A test email from lsuk system";
// $message=$acttObj->read_specific("em_format","email_format","id=7")['em_format'];
// try {
//     $mail->SMTPDebug = 0;
//     //$mail->isSMTP(); 
//     //$mailer->Host = 'lsuk-org.mail.protection.outlook.com';
//     $mail->SMTPAuth   = true;
//     $mail->Username   = 'info@lsuk.org';
//     $mail->Password   = 'LangServ786';
//     $mail->SMTPSecure = 'tls';
//     $mail->Port       = 587;
//     $mail->setFrom($from_email, 'LSUK');
//     $mail->addAddress($to_email);
//     $mail->addReplyTo('hr@lsuk.org', 'LSUK');
//     $mail->isHTML(true);
//     $mail->Subject = $subject;
//     $mail->Body    = $message;
//     if($mail->send()){
//         $mail->ClearAllRecipients();
//         echo '<script>alert("Yes it has been sent.");</script>';
//     }
//  }catch (Exception $e) {
//  echo '<script>alert("Mailer library error!");</script>';
// }