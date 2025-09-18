<?php use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
require '../../lsuk_system/phpmailer/vendor/autoload.php';
include '../action.php';
//forgot password
if(isset($_POST['forgot_email'])){
    $json=(object) null;
    $role=trim($_POST['role']);
    $role=$role ? $role : 1;
    if(!empty($_POST['forgot_email'])){
        $forgot_email=trim($_POST['forgot_email']);
        $reg=0;
        $check_data=$obj->read_specific("id,password,deleted_flag,(CASE
        WHEN (interpreter_reg.actnow='Active' and (interpreter_reg.actnow_time='1001-01-01' AND interpreter_reg.actnow_to='1001-01-01') AND interpreter_reg.active='0') THEN 'ready'
        WHEN (interpreter_reg.actnow='Active' and (CURRENT_DATE() BETWEEN interpreter_reg.actnow_time AND interpreter_reg.actnow_to) AND interpreter_reg.active='0') THEN 'ready'
        WHEN (interpreter_reg.actnow='Inactive' and (interpreter_reg.actnow_time='1001-01-01' AND interpreter_reg.actnow_to='1001-01-01') AND (interpreter_reg.active='0' OR interpreter_reg.active='1')) THEN 'not ready'
        WHEN (interpreter_reg.actnow='Inactive' and (CURRENT_DATE() NOT BETWEEN interpreter_reg.actnow_time AND interpreter_reg.actnow_to) AND interpreter_reg.active='0') THEN 'ready'
        ELSE 'not ready' END) as status","interpreter_reg","TRIM(email)='".$forgot_email."'");
        $id=$check_data['id'];
        $password=$check_data['password'];
        $deleted_flag=$check_data['deleted_flag'];
        $status=$check_data['status'];
        if(!empty($id) && $deleted_flag==0 && $status!="not ready"){
            $mail = new PHPMailer(true);
            try {
                    $from_add = "hr@lsuk.org";$from_name = "LSUK Account Security";
                    $em_format=$obj->read_specific("em_format","email_format","id=35")['em_format'];
                    $data   = ["[PASSWORD]"];
                    $to_replace  = [$password];
                    $message=str_replace($data, $to_replace,$em_format);
                    $mail->SMTPDebug = 1;
                    //$mail->isSMTP(); 
                    //$mailer->Host = 'smtp.office365.com';
                    $mail->SMTPAuth   = true;
                    $mail->Username   = 'info@lsuk.org';
                    $mail->Password   = 'LangServ786';
                    $mail->SMTPSecure = 'tls';
                    $mail->Port       = 587;
                    $mail->setFrom($from_add,$from_name);
                    $mail->addAddress($forgot_email);
                    $mail->addReplyTo($from_add,$from_name);
                    $mail->isHTML(true);
                    $mail->Subject = 'Password recovery for LSUK Video App';
                    $mail->Body = $message;
                if($mail->send()){
                    $mail->ClearAllRecipients();
                    $json->status="success";
                    $json->msg="Password sent to your email";
                }else{
                    $json->status="failed";
                    $json->msg="Failed to send password to your email";
                }
            } catch (Exception $e) {$json->mailer="failed";}
        }else{
            $json->status="failed";
            $json->msg="No record found for this email";
        }
    }else{
        $json->status="failed";
        $json->msg="Email should not be empty";
    }
    header('Content-Type: application/json');
    echo json_encode($json, JSON_UNESCAPED_UNICODE);
}
?>