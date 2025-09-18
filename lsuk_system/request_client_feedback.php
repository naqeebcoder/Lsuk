<?php //php mailer library
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
require 'phpmailer/vendor/autoload.php';
$mail = new PHPMailer(true);
include'db.php';
include'class.php';
$id=$_GET['order_id'];
$table=$_GET['table'];
$get_query=$acttObj->read_specific("$table.*,interpreter_reg.name","$table,interpreter_reg","$table.intrpName=interpreter_reg.id and $table.deleted_flag=0 and $table.order_cancel_flag=0 and $table.id=".$id);
$feedback=$acttObj->read_specific("count(*) as counter","interp_assess","table_name='".$table."' AND order_id=".$id)['counter'];
$company=$acttObj->read_specific("name","comp_reg","abrv='".$get_query['orgName']."'")['name'];
$feedback_link="https://lsuk.org/feedback_confirmation.php?id=".base64_encode($id)."&tbl=".base64_encode($table);
$jobs_array=array("interpreter"=>"Face To Face","telephone"=>"Telephone","translation"=>"Translation");
$assignDate=$misc->dated($get_query['assignDate']);
$assignment_details = "<table>
<tr>
<td style='border: 1px solid yellowgreen;padding:5px;'>" . $jobs_array[$table] . " Project ID</td>
<td style='border: 1px solid yellowgreen;padding:5px;'>".$id."</td>
</tr>
<tr>
<td style='border: 1px solid yellowgreen;padding:5px;'>Assignment Type</td>
<td style='border: 1px solid yellowgreen;padding:5px;'>".$jobs_array[$table]." Interpreting Assignment</td>
</tr>
<tr>
<td style='border: 1px solid yellowgreen;padding:5px;'>Case Name or File Reference Number (if any)</td>
<td style='border: 1px solid yellowgreen;padding:5px;'>".$get_query['orgRef']."</td>
</tr>
<tr>
<td style='border: 1px solid yellowgreen;padding:5px;'>Source Language</td>
<td style='border: 1px solid yellowgreen;padding:5px;'>".$get_query['source']."</td>
</tr>
<tr>
<td style='border: 1px solid yellowgreen;padding:5px;'>Target Language</td>
<td style='border: 1px solid yellowgreen;padding:5px;'>".$get_query['target']."</td>
</tr>
<tr>
<td style='border: 1px solid yellowgreen;padding:5px;'>Assignment Date</td>
<td style='border: 1px solid yellowgreen;padding:5px;'>".$assignDate."</td>
</tr>
<tr>
<td style='border: 1px solid yellowgreen;padding:5px;'>Assignment Time</td>
<td style='border: 1px solid yellowgreen;padding:5px;'>".$get_query['assignTime']."</td>
</tr>
<tr>
<td style='border: 1px solid yellowgreen;padding:5px;'>Interpreter Name</td>
<td style='border: 1px solid yellowgreen;padding:5px;'>".$get_query['name']."</td>
</tr>
<tr>
<td style='border: 1px solid yellowgreen;padding:5px;'>Interpreter Contact</td>
<td style='border: 1px solid yellowgreen;padding:5px;'>".$get_query['orgContact']."</td>
</tr>
<tr>
<td style='border: 1px solid yellowgreen;padding:5px;'>Booking Requested By</td>
<td style='border: 1px solid yellowgreen;padding:5px;'>".$get_query['inchPerson']."</td>
</tr>
</table>";
//Get format from database
$email_body=$acttObj->read_specific("em_format","email_format","id=42")['em_format'];
$data   = ["[ASSIGNMENT_DETAILS]", "[FEEDBACK_LINK]"];
$to_replace  = ["$assignment_details", "$feedback_link"];
$message=str_replace($data, $to_replace,$email_body);
$subject = "Feedback request for ".$get_query['source']." ".$jobs_array[$table]." interpreting project on ".$assignDate." at ".$get_query['assignTime'];
if(isset($_POST['btn_submit'])){
    if($acttObj->read_specific("count(*) as counter","feedback_requests","order_id=".$id." AND table_name='".$table."'")['counter']==0){
        $acttObj->insert("feedback_requests",array("order_id"=>$id,"table_name"=>$table,"user_id"=>$_SESSION['userId'],"send_by"=>"l"));
    }
    try {
        $mail->SMTPDebug = 0;
        $mail->SMTPAuth   = true;
        $mail->Username   = 'info@lsuk.org';
        $mail->Password   = 'LangServ786';
        $mail->SMTPSecure = 'tls';
        $mail->Port       = 587;
        $from_add='info@lsuk.org';
        $from_name='LSUK';
        $mail->setFrom($from_add, $from_name);
        //$mail->addAddress('waqarecp1992@gmail.com');
        $mail->addAddress($get_query['inchEmail']);
        $mail->addReplyTo($from_add, $from_name);
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $message;
        if($mail->send()){
            $mail->ClearAllRecipients();
            if(!empty($get_query['inchEmail2'])){
                $mail->addAddress($get_query['inchEmail2']);
                $mail->addReplyTo($from_add, $from_name);
                $mail->isHTML(true);
                $mail->Subject = $subject;
                $mail->Body    = $message;
                $mail->send();
                $mail->ClearAllRecipients();
            }
            echo '<script>function refreshParent(){window.opener.location.reload();}
            alert("Feedback has been requested successfully. Thank you");window.onunload = refreshParent;window.close();</script>';
        }else{
            echo '<script>alert("Failed to request client feedback !");window.history.back(-1);</script>';
        }
    } catch (Exception $e) {
        echo '<script>alert("Failed: Mailer library error occured!");window.history.back(-1);</script>';
    }
}
$feedback_counter=$acttObj->read_specific('count(*) as counter','feedback_requests','table_name="'.$table.'" and order_id='.$id);
$feedback=$acttObj->read_specific("count(*) as counter","interp_assess","table_name='".$table."' AND order_id=".$id)['counter'];
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<title>Client Feedback Request Form</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="css/bootstrap.css">
</head>
<body>    
 <div class="container-fluid">
    <h3 class="text-center">Client Feedback Request Form<br>
    <b><span class="label label-primary" style="font-size:16px;"><?php echo $company; ?></span></b></h3>
   <?php if(!is_null($get_query['name']) && $feedback_counter['counter']==0 && $feedback==0){ ?>
        <form class="col-md-8 col-md-offset-2" action="#" method="post" enctype="multipart/form-data">
                <table width="100%" align="center" class="table table-bordered">
                <tr>
                    <td>Job Type</td>
                    <td><?php echo $jobs_array[$table]." Project";?></td>
                    <td>Reference</td>
                    <td><strong><?php echo $get_query['nameRef']; ?></strong></td>
                    </tr>
                    <tr>
                    <td>Interpreter Name</td>
                    <td><b><span class="label label-info" style="font-size:16px;"><?php echo $get_query['name']; ?></span></b></td>
                    <td>Assignment Date</td>
                    <td><strong><?php echo $misc->dated($get_query['assignDate']); ?></strong></td>
                    </tr>
                    <tr>
                    <td>Source Language</td>
                    <td><strong><?php echo $get_query['source']; ?></strong></td>
                    <td>Target Language</td>
                    <td><strong><?php echo $get_query['target']; ?></strong></td>
                    </tr>
                </table>
              <div class="form-group col-md-6 col-sm-6">
                <br><button class="btn btn-primary" type="submit" name="btn_submit">Request Now</button>
              </div>
        </form>
   <?php }else{ echo "<center><br><br><br><br><br><br><br><br><h3 class='text-danger'>Feedback for this job has already been recorded.<br>Thank you</h4><br><br><br><br><br><br></center>"; } ?>
</div>       
</body>
</html>
