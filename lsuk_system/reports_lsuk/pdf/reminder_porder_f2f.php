<?php
//php mailer library
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
require 'phpmailer/vendor/autoload.php';
$mail = new PHPMailer(true);
include 'db.php';
include 'class_new.php';
$from_email = "hr@lsuk.org";
$query_reminder="SELECT * from (SELECT interpreter.porder_email,interpreter.porder,comp_reg.po_req,'Interpreter' as type,interpreter.id,interpreter.intrpName,interpreter.orgName,interpreter.inchEmail,interpreter_reg.name,interpreter.source,interpreter.target,interpreter.invoic_date,interpreter.assignDate,interpreter.assignTime,interpreter.orgContact,interpreter.submited, interpreter.aloct_by,interpreter.aloct_date,interpreter.dated,interpreter.hrsubmited,interpreter.comp_hrsubmited,interpreter.interp_hr_date, interpreter.comp_hr_date,interpreter.hoursWorkd,interpreter.C_hoursWorkd,interpreter.total_charges_comp,interpreter.cur_vat,interpreter.C_otherexpns, interpreter.credit_note,interpreter.C_admnchargs,interpreter.rAmount,interpreter.rDate,interpreter.sentemail,interpreter.printed,interpreter.printedby,interpreter.deleted_flag,interpreter.order_cancel_flag,interpreter.nameRef,interpreter.orgRef,interpreter.invoiceNo,interpreter.commit,comp_reg.email,comp_reg.abrv as comp_abrv FROM interpreter,interpreter_reg,comp_reg WHERE interpreter.intrpName=interpreter_reg.id AND interpreter.orgName=comp_reg.abrv and interpreter.multInv_flag=0 AND interpreter.deleted_flag = 0 and interpreter.order_cancel_flag=0 and interpreter.commit=1 and comp_reg.po_req=1 and (interpreter.porder='' OR interpreter.porder='Nil') and (round(interpreter.rAmount,2) < round((interpreter.total_charges_comp+(interpreter.total_charges_comp*interpreter.cur_vat)),2) or interpreter.total_charges_comp =0) and YEAR(interpreter.assignDate)>'2020'
) as grp";
$result_reminder=mysqli_query($con,$query_reminder);
$em_format=$obj_new->read_specific("em_format","email_format","id=39")['em_format'];
while($row_reminder=mysqli_fetch_assoc($result_reminder)){
$obj_new->insert('po_requested',array("order_id"=>$row_reminder['id'],"order_type"=>"f2f"));
$to_address = $row_reminder['inchEmail'];
$subject = "PO Request for Invoice # ".$row_reminder['invoiceNo'];
$data   = ["[SOURCE]", "[TARGET]", "[ASSIGN_DATE]", "[ASSIGN_TIME]", "[INVOICE_NO]"];
$to_replace  = [$row_reminder['source'],$row_reminder['target'],$row_reminder['assignDate'],$row_reminder['assignTime'],$row_reminder['invoiceNo']];
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
    $mail->setFrom($from_email, 'LSUK PO Request');
    $mail->addAddress($to_address);
    $mail->addReplyTo($from_email, 'LSUK');
    $mail->isHTML(true);
    $mail->Subject = $subject;
    $mail->Body    = $message;
    $mail->send();
    $mail->ClearAllRecipients();
} catch (Exception $e) {
    echo "Message could not be sent! Mailer library error.";
    }
} ?>