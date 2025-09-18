<?php
die();
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\SMTP;
    use PHPMailer\PHPMailer\Exception;
    require 'phpmailer/vendor/autoload.php';
    $mail = new PHPMailer(true);
    include 'db.php';
    include 'class_new.php';
    $get_pinv = $obj_new->read_all("id,name,invEmail,abrv","comp_reg"," comp_nature IN (1,4) AND deleted_flag=0");

    $r_pinv = mysqli_num_rows($get_pinv);
    if($r_pinv>0){
        while($row = mysqli_fetch_assoc($get_pinv)){
            $comp_abrv = $row['abrv'];
            $comp_name = $row['name'];
            $comp_email = trim($row['invEmail']);
            $attach_file='';
            $attach_file = 'file_folder/pinv_list/'.$comp_abrv.'_pending_invoices.xlsx';
            if(file_exists($attach_file)){
            $to_address = $comp_email;
            $to_address2 = 'accounts@lsuk.org';
            if($to_address){
                    $subject = "Reminder: Outstanding Invoices";
                    $message="<p>Dear Client,<br><br>
                    This is a kind reminder that you need to pay overdue invoices if any. Our agreed payment term for invoices is 21 days.</p>
                    <p>The statement of outstanding invoices is attached for your convenience. Please let us know if you require invoice copies.</p>
                    <p>Please send us the remittance if any of the listed invoices have already been paid.</p>
                    <p>Please make all cheques payable to Language Services UK Limited.</p>
                    <p>For BACS payment:<br>
                    Sort Code: 20-13-34<br>
                    Account Number: 33161234.<br>
                    Company Registration Number: 7760366<br>
                    VAT Number: 198427362.</p>
                    <p>Kind regards,</p>
                    
                    Accounting & Finance Department<br>
                    Language Services UK Limited<br>
                    Tel: 01172445838<br>  

                    <img src='https://lsuk.org/lsuk-logo.png' alt='logo'><br>

                    <small>This message contains confidential information and is intended only for the individual named. If you are not the intended recipient you are notified that disclosing, copying, distributing or taking any action in reliance on the contents of this information is strictly prohibited. If you are not the intended recipient, please notify the sender immediately by reply e-mail and delete this message instantly. Computer viruses can be transmitted via email. The recipient should check this email and any attachments for the presence of viruses. The company accepts no liability for any damage caused by any virus transmitted by this email or for any errors or omissions in the contents of this message, which arise as a result of e-mail transmission. E-mail transmission cannot be guaranteed to be secure or error-free as information could be intercepted, corrupted, lost, destroyed, arrive late or incomplete, or contain viruses. No employee or agent is authorized to conclude any binding agreement on behalf of Language Services UK Limited with another party by email without express written confirmation by Director. Any views or opinions presented in this email are solely those of the author and do not necessarily represent those of the company. Employees of the company are expressly required not to make defamatory statements and not to infringe or authorize any infringement of copyright or any other legal right by email communications. Any such communication is contrary to company policy and outside the scope of the employment of the individual concerned. The company will not accept any liability in respect of such communication, and the employee responsible will be personally liable for any damages or other liability arising.</small>
                    <small>
                    LSUK Limited or Language Services UK Limited are trading names of Language Services UK Limited – registered in England and Wales (7760366) to provide Interpreting and Translation Services.
                    </small>                    
                     ";
                    try {
                        $mail->SMTPDebug = 0;
                        $mail->Host = "smtp.office365.com";
                        $mail->SMTPAuth   = true;
                        $mail->Username   = "accounts@lsuk.org";
                        $mail->Password   = "bfgdpkbrlfvkvfcr";
                        $mail->SMTPSecure = 'tls';
                        $mail->Port       = 587;
                        $mail->setFrom("accounts@lsuk.org", "LSUK");
                        $mail->addAttachment($attach_file); 
                        $mail->addAddress($to_address);
                        $mail->addAddress($to_address2);
                        $mail->addReplyTo("accounts@lsuk.org", "LSUK");
                        $mail->isHTML(true);
                        $mail->Subject = $subject;
                        $mail->Body    = $message;
                        if($mail->send()){
                            echo "comp_name: ".$row['name']." (".$row['abrv'].")  | sent to email: ".$to_address."<br>";
                        }
                    } catch (Exception $e) {
                        echo "Message could not be sent! Mailer library error for: ".$e->getMessage();
                        echo "<br>";
                    }
                    $mail->clearAllRecipients();
                    $mail->clearAttachments();
                }
            }
    }
}

    ?>
    