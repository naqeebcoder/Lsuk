<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
require '../../lsuk_system/phpmailer/vendor/autoload.php';
include '../action.php';
if(isset($_POST['add_missing_document'])){
    $json=(object) null;
    if(isset($_POST['ap_user_id']) && !empty($_POST['ap_user_id'])){
        $table="interpreter_reg";
        $ap_user_id=$_POST['ap_user_id'];
        $document_name=$_POST['document_name'];
        $column_name=$document_name.'_file';
        if($document_name=='applicationForm' || $document_name=='agreement'){
            $label=$document_name=="applicationForm"?"Application Form":"Agreement Form";
            $file_base64 = base64_decode($_POST['file']);
            $file_type = '.'.$_POST['file_type'];
            $old_file_column=$obj->read_specific("$column_name","$table","id=".$ap_user_id)["$column_name"];
            $old_file="../../lsuk_system/file_folder/$document_name/".$old_file_column;
            if(file_exists($old_file) && !empty($old_file)){
                unlink($old_file);
            }
            $new_file_name = round(microtime(true)).$file_type;
            if(file_put_contents("../../lsuk_system/file_folder/$document_name/".$new_file_name, $file_base64)){
                $obj->editFun("$table",$ap_user_id,"$column_name",$new_file_name);
                $obj->editFun($table,$ap_user_id,$document_name,"Soft Copy");
                $json->status="1";
                $json->msg="Your ".$label." has been added successfully. Thank you";
            }else{
                $json->status="0";
                $json->msg="Failed to add your ".$label.". Try again !";
            }
        }else if($document_name=='bank_details'){
            /*if(isset($_POST['bank_name'])){
                $obj->editFun("$table",$ap_user_id,'bnakName',$_POST['bank_name']);
            }
            if(isset($_POST['account_name'])){
                $obj->editFun("$table",$ap_user_id,'acName',$_POST['account_name']);
            }
            if(isset($_POST['sort_code'])){
                $obj->editFun("$table",$ap_user_id,'acntCode',$_POST['sort_code']);
            }
            if(isset($_POST['account_number'])){
                $obj->editFun("$table",$ap_user_id,'acNo',$_POST['account_number']);
                $json->msg="success";
            }else{
                $json->msg="failed";
            }*/
            $int_name=$obj->read_specific("name","$table","id=".$ap_user_id)["name"];
            $mail = new PHPMailer(true);
            try {
                $from_add="info@lsuk.org";$from_name="LSUK";
                $mail->SMTPDebug = 0;
                //$mail->isSMTP(); 
                $mailer->Host = 'smtp.office365.com';
                $mail->SMTPAuth   = true;
                $mail->Username   = 'info@lsuk.org';
                $mail->Password   = 'LangServ786';
                $mail->SMTPSecure = 'tls';
                $mail->Port       = 587;
                $mail->setFrom($from_add, $from_name);
                //$mail->addAddress('waqarecp1992@gmail.com');
                $mail->addAddress('hr@lsuk.org');
                $mail->addReplyTo($from_add, $from_name);
                $mail->isHTML(true);
                $mail->Subject = $int_name.' requested for bank details';
                $mail->Body    = "Dear Admin,<br><b>".$int_name."</b> has requested for updating bank info.<br>
                Kindly update from Admin Portal.<br>
                <u>Updates Requested:</u><br>
                <table>
                <tr>
                <td style='border: 1px solid yellowgreen;padding:5px;'>Bank Name</td>
                <td style='border: 1px solid yellowgreen;padding:5px;'>".$_POST['bank_name']."</td>
                </tr>
                <tr>
                <td style='border: 1px solid yellowgreen;padding:5px;'>Account Title</td>
                <td style='border: 1px solid yellowgreen;padding:5px;'>".$_POST['account_name']."</td>
                </tr>
                <tr>
                <td style='border: 1px solid yellowgreen;padding:5px;'>Sort Code</td>
                <td style='border: 1px solid yellowgreen;padding:5px;'>".$_POST['sort_code']."</td>
                </tr>
                <tr>
                <td style='border: 1px solid yellowgreen;padding:5px;'>Account Number</td>
                <td style='border: 1px solid yellowgreen;padding:5px;'>".$_POST['account_number']."</td>
                </tr>
                </table>";
                if($mail->send()){
                    $mail->ClearAllRecipients();
                    $json->status="1";
                    $json->msg="Request has been sent for your bank details. Thank you";
                }else{
                    $json->status="0";
                    $json->msg="Failed to send request for your bank details. Try again!";
                }
            } catch (Exception $e) {
                //echo "mail_failed";
                 $json->status="0";
                    $json->msg="Failed !";
            }
        }else{
            $col_file_name=$document_name.'_file';
            $col_no_name=$document_name.'_no';
            $col_issue_name=$document_name.'_issue_date';
            $col_expiry_name=$document_name.'_expiry_date';
            $document_number=$_POST['document_number'];
            $obj->editFun($table,$ap_user_id,$col_no_name,$document_number);
            $issue_date=$_POST['issue_date'];
            $obj->editFun($table,$ap_user_id,$col_issue_name,$issue_date);
            $expiry_date=$_POST['expiry_date'];
            $obj->editFun($table,$ap_user_id,$col_expiry_name,$expiry_date);
            if($document_name=="dbs" || $document_name=="id_doc"){
                if($document_name=="dbs"){
                    $col_file_name=="crbDbs";
                    $label="DBS Document";
                }
                if($document_name=="id_doc"){
                    $col_file_name=="identityDocument";
                    $label="Identity Document";
                }
                $obj->editFun($table,$ap_user_id,$col_file_name,"Soft Copy");
            }
            $file_base64 = base64_decode($_POST['file']);
            $file_type = '.'.$_POST['file_type'];
            $old_file_column=$obj->read_specific("$column_name","$table","id=".$ap_user_id)["$column_name"];
            $old_file="../../lsuk_system/file_folder/issue_expiry_docs/".$old_file_column;
            if(file_exists($old_file) && !empty($old_file)){
                unlink($old_file);
            }
            $new_file_name = round(microtime(true)).$file_type;
            if(file_put_contents("../../lsuk_system/file_folder/issue_expiry_docs/".$new_file_name, $file_base64)){
                $obj->editFun("$table",$ap_user_id,"$column_name",$new_file_name);
                $json->status="1";
                $json->msg="Your ".$label." has been added successfully. Thank you";
            }else{
                $json->status="0";
                $json->msg="Failed to add your ".$label.". Try again !";
            }
        }
    }else{
        $json->status="0";
        $json->msg="You must login to perform this action !";
    }
    header('Content-Type: application/json');
    echo json_encode($json, JSON_UNESCAPED_UNICODE);
}
?>