<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
require 'phpmailer/vendor/autoload.php';
$mail = new PHPMailer(true);
include 'actions.php';
$result_reminder=$obj->read_all("interpreter_reg.id as interpreter_id,interpreter_reg.name as interpreter_name,interpreter_reg.email as interpreter_email","interpreter_reg","deleted_flag=0 AND is_temp=0 AND isAdhoc=0 and 
interpreter_reg.active='0' AND (interpreter_reg.actnow='Active' OR (interpreter_reg.actnow='Inactive' AND CURRENT_DATE() NOT BETWEEN interpreter_reg.actnow_time AND interpreter_reg.actnow_to)) and availability_option=1 LIMIT 30,30");
$from_email = "hr@lsuk.org";
$subject = 'Update your availability for '.date("l");
$sub_title="🔔 Good morning!\nCan you mark your presence for the day. This doesn't guarantee a job but makes easy for LSUK to allocate a job.\nThank you";
$type_key="av";
while($row_reminder=$result_reminder->fetch_assoc()){
    $obj->update("interpreter_reg",array("is_marked"=>0),"id=".$row_reminder['interpreter_id']);
    $array_tokens=explode(',',$obj->read_specific("GROUP_CONCAT( DISTINCT token) as tokens","int_tokens","int_id=".$row_reminder['interpreter_id'])['tokens']);
    if(!empty($array_tokens)){
        foreach($array_tokens as $token){
            if(!empty($token)){
				$obj->notify($token,$subject,$sub_title,array("type_key"=>$type_key));
			}
        }
    }
}
?>