<?php 
//php mailer library
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
require 'lsuk_system/phpmailer/vendor/autoload.php';
$mail = new PHPMailer(true);
include'source/class.php';
$id=isset($_GET['id']) && !empty($_GET['id'])?base64_decode($_GET['id']):0;
$table="int_references";
$get_query=$acttObj->read_specific("interpreter_reg.name,int_references.int_id,int_references.status","interpreter_reg,int_references","interpreter_reg.id=int_references.int_id and int_references.id=".$id);
if($id==0 || empty($get_query['name'])){
  echo "<script>window.location.href='index.php';</script>";
}
if(isset($_POST['btn_submit'])){
  if(!empty($_POST['submit_name']) && !empty($_POST['submit_email'])){
    if(isset($_POST['g-recaptcha-response']) && !empty($_POST['g-recaptcha-response'])){
      $secret='6LextRoUAAAAAPvBF31eiYCmVP7Ne8a6mSez83zl';
      $ip=$_SERVER['REMOTE_ADDR'];
      $captcha=$_POST['g-recaptcha-response'];
      $rsp=file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=$secret&response=$captcha&remoteip=$ip");
      $arr=json_decode($rsp,TRUE);
      if($arr['success']){
        $is_late=$_POST['is_late'];$lateness=isset($is_late) && $is_late=='1'?$_POST['lateness']:0;
        $is_miss=$_POST['is_miss'];$missed=isset($is_miss) && $is_miss=='1'?$_POST['missed']:0;
        $acttObj->update($table,array("punctuality"=>$_POST['punctuality'],"appearance"=>$_POST['appearance'],"professionalism"=>$_POST['professionalism']
        ,"confidentiality"=>$_POST['confidentiality'],"impartiality"=>$_POST['impartiality'],"accuracy"=>$_POST['accuracy'],"rapport"=>$_POST['rapport']
        ,"communication"=>$_POST['communication'],"lateness"=>$_POST['lateness'],"missed"=>$_POST['missed'],"remarks"=>$_POST['remarks'],"submit_name"=>$_POST['submit_name'],"submit_email"=>$_POST['submit_email'],"submit_date"=>date('Y-m-d h:i:s'),"status"=>1),
        array("id"=>$id));
        $message = "Hello Admin,<br>
        <b>".$_POST['submit_name']."</b> has confirmed as a referee for <b>".$get_query['name']."</b><br>
        <b><a style='text-decoration: none;font-size: 16px;border: 1px solid;padding: 4px;border-radius: 4px;background: #618cd6;color: white;' href='https://lsuk.org/lsuk_system/full_view_interpreter.php?view_id=".$get_query['int_id']."'>CLICK HERE</a></b> to view profile of ".$get_query['name'].".<br>
        Kindly verify at LSUK system.<br>Thank you";
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
          $mail->addAddress('hr@lsuk.org');
          $mail->addReplyTo($from_add, $from_name);
          $mail->isHTML(true);
          $mail->Subject = 'New referee confirmation feedback';
          $mail->Body = $message;
          if($mail->send()){
              $mail->ClearAllRecipients();
              $mail->clearAttachments();
              $ref_subject="Referee confirmation of interpreter profile at LSUK";
              if(!empty($_POST['submit_email'])){
                $mail->addAddress($_POST['submit_email']);
                $mail->addReplyTo($from_add, $from_name);
                $mail->isHTML(true);
                $mail->Subject = "LSUK referee confirmation notification";
                $mail->Body    = "Hello ".$_POST['submit_name'].",<br>
                We have received your referee confirmation feedback for ".$get_query['name']." and will review it soon.
                Thank you very much for your quick response.<br>
                Kindest regards,<br>
                LSUK Admin Team<br><br>
                <span style='color: #2f5496;'>Working hours:<br>
                Monday, Tuesday 9AM – 1PM<br>
                Thursday and Friday 9AM - 5PM</span>
                <br><br>
                <span style='color: #002060;'><b><i>Language Services UK Limited<br>
                M/O Association of Translation Companies<br>
                M/O Institute of Translation and Interpreting<br>
                Phone: 01173290610     07915177068 – 0333 7005785<br>
                Fax: 0333 800 5785<br>
                Email: INFO@LSUK.ORG</i></b><br><br></span>
                <small>This message contains confidential information and is intended only for the individual named. If you are not the intended recipient you are notified that disclosing, copying, distributing or taking any action in reliance on the contents of this information is strictly prohibited. If you are not the intended recipient, please notify the sender immediately by reply e-mail and delete this message instantly. Computer viruses can be transmitted via email. he recipient should check this email and any attachments for the presence of viruses. The company accepts no liability for any damage caused by any virus transmitted by this email or for any errors or omissions in the contents of this message, which arise as a result of e-mail transmission. E-mail transmission cannot be guaranteed to be secure or error-free as information could be intercepted, corrupted, lost, destroyed, arrive late or incomplete, or contain viruses. No employee or agent is authorized to conclude any binding agreement on behalf of LanguageServicesUK Limited with another party by email without express written confirmation by Director. Any views or opinions presented in this email are solely those of the author and do not necessarily represent those of the company. Employees of the company are expressly required not to make defamatory statements and not to infringe or authorize any infringement of copyright or any other legal right by email communications. Any such communication is contrary to company policy and outside the scope of the employment of the individual concerned. The company will not accept any liability in respect of such communication, and the employee responsible will be personally liable for any damages or other liability arising. LSUK Limited  or Language Services UK Limited are trading names of LanguageServicesUK Limited – registered in England and Wales (7760366) to provide Interpreting and Translation Services.<small>";
                $mail->send();
                $mail->ClearAllRecipients();
              }
              $msg='<div class="alert alert-success alert-dismissible col-md-6 col-md-offset-3 text-center">
              <a href="#" class="close" data-dismiss="alert" aria-label="close">&times; </a>
              <i class="glyphicon glyphicon-check"></i> Success: Your request has been sent successfully.<br>
              Thank you
              </div>';
              echo '<script>setTimeout(function(){ window.location="index.php"; }, 3000); </script>';
          }else{
              $msg='<div class="alert alert-danger alert-dismissible col-md-6 col-md-offset-3 text-center">
              <a href="#" class="close" data-dismiss="alert" aria-label="close">&times; </a>
              <i class="glyphicon glyphicon-check"></i> Failed: Failed to submit your request !<br>
              Kindly try again.</div>';
          }
        } catch (Exception $e) {
          $msg='<div class="alert alert-danger alert-dismissible col-md-6 col-md-offset-3 text-center">
                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times; </a>
                <i class="glyphicon glyphicon-check"></i> Failed: Mailer library error occured!</div>';
        }
      }else{
        echo '<script>alert("Kindly verify your catpahca. Kindly try again.");</script>';
      }
    }else{
      echo '<script>alert("Kindly verify your catpahca. Kindly try again.");window.history.back(-1);</script>';
    }
  }else{
    echo '<script>alert("Please fill up all the fields with your name and email.Thank you");window.history.back(-1);</script>';
  }
} ?>
<!DOCTYPE HTML>
<html class="no-js">
   <head>
    <?php include'source/header.php'; ?>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" />
    <script src="js/jquery-1.8.2.min.js" type="text/javascript"></script>
    <script src="js/jquery.jcarousel.min.js" type="text/javascript"></script>
    <script src="js/custom.js" type="text/javascript"></script>
    <style>
        .ri {margin-top: 7px !important;}
        .ri .label {font-size: 100% ;padding: .5em 0.6em 0.5em;}
        .mt{margin-top: 2px;}
        td:hover{cursor:pointer;}
      </style>
   </head>
   <body class="boxed">
      <!-- begin container -->
      <div id="wrap">
      <!-- begin header -->
      <?php include'source/top_nav.php'; ?>
      <!-- end header -->
      <!-- begin page title -->
      <section id="page-title">
         <div class="container clearfix">
            <h1>Reference Confirmation Form</h1>
            <nav id="breadcrumbs">
               <ul>
                  <li><a class="btn btn-default" href="index.php">Home</a> &rsaquo;</li>
               </ul>
            </nav>
         </div>
      </section>
         <!-- begin content -->
         <section id="content" class="container-fluid clearfix">
         <?php if(isset($msg) && !empty($msg)){echo $msg;} 
         if(!is_null($get_query['name']) && $get_query['status']==0){ ?>
            <form class="col-md-8 col-md-offset-2" action="#" method="post" enctype="multipart/form-data">
                <div class="bg-info col-xs-12 form-group"><h4>Reference Confirmation for <span class="label label-primary"> <?php echo $get_query['name']; ?></span></h4></div>
                <table width="100%" align="center" class="table table-hover table-bordered">
                <tr class="bg-primary">
                    <td width="25%"><strong>Action</strong></td>
                    <td><strong>Poor</strong></td>
                    <td><strong>Average</strong></td>
                    <td><strong>Fair</strong></td>
                    <td><strong>Good</strong></td>
                    <td><strong>Excellent</strong></td>
                    </tr>
                <tr>
                    <td>Punctuality  * <span class="check_icon pull-right hidden"><i class="fa fa-check-circle text-success"></i></span></td>
                    <td><input type="radio" name="punctuality" id="punctuality" value="-5"  required=''/></td>
                    <td><input type="radio" name="punctuality" id="punctuality" value="1"  required=''/></td>
                    <td><input type="radio" name="punctuality" id="punctuality" value="5"  required=''/></td>
                    <td><input type="radio" name="punctuality" id="punctuality" value="10"  required=''/></td>
                    <td><input type="radio" name="punctuality" id="punctuality" value="15"  required=''/></td>
                    </tr>
                <tr>
                    <td>Appearance  * <span class="check_icon pull-right hidden"><i class="fa fa-check-circle text-success"></i></span></td>
                    <td><input type="radio" name="appearance" id="appearance" value="-5"  required=''/></td>
                    <td><input type="radio" name="appearance" id="appearance" value="1"  required=''/></td>
                    <td><input type="radio" name="appearance" id="appearance" value="5"  required=''/></td>
                    <td><input type="radio" name="appearance" id="appearance" value="10"  required=''/></td>
                    <td><input type="radio" name="appearance" id="appearance" value="15"  required=''/></td>
                    </tr>
                <tr>
                    <td>Professionalism  * <span class="check_icon pull-right hidden"><i class="fa fa-check-circle text-success"></i></span></td>
                    <td><input type="radio" name="professionalism" id="professionalism" value="-5"  required=''/></td>
                    <td><input type="radio" name="professionalism" id="professionalism" value="1"  required=''/></td>
                    <td><input type="radio" name="professionalism" id="professionalism" value="5"  required=''/></td>
                    <td><input type="radio" name="professionalism" id="professionalism" value="10"  required=''/></td>
                    <td><input type="radio" name="professionalism" id="professionalism" value="15"  required=''/></td>
                    </tr>
                <tr>
                    <td>Confidentiality  * <span class="check_icon pull-right hidden"><i class="fa fa-check-circle text-success"></i></span></td>
                    <td><input type="radio" name="confidentiality" id="confidentiality" value="-5"  required=''/></td>
                    <td><input type="radio" name="confidentiality" id="confidentiality" value="1"  required=''/></td>
                    <td><input type="radio" name="confidentiality" id="confidentiality" value="5"  required=''/></td>
                    <td><input type="radio" name="confidentiality" id="confidentiality" value="10"  required=''/></td>
                    <td><input type="radio" name="confidentiality" id="confidentiality" value="15"  required=''/></td>
                    </tr>
                <tr>
                    <td>Impartiality  * <span class="check_icon pull-right hidden"><i class="fa fa-check-circle text-success"></i></span></td>
                    <td><input type="radio" name="impartiality" id="impartiality" value="-5"  required=''/></td>
                    <td><input type="radio" name="impartiality" id="impartiality" value="1"  required=''/></td>
                    <td><input type="radio" name="impartiality" id="impartiality" value="5"  required=''/></td>
                    <td><input type="radio" name="impartiality" id="impartiality" value="10"  required=''/></td>
                    <td><input type="radio" name="impartiality" id="impartiality" value="15"  required=''/></td>
                    </tr>
                <tr>
                    <td>Accuracy  * <span class="check_icon pull-right hidden"><i class="fa fa-check-circle text-success"></i></span></td>
                    <td><input type="radio" name="accuracy" id="accuracy" value="-5"  required=''/></td>
                    <td><input type="radio" name="accuracy" id="accuracy" value="1"  required=''/></td>
                    <td><input type="radio" name="accuracy" id="accuracy" value="5"  required=''/></td>
                    <td><input type="radio" name="accuracy" id="accuracy" value="10"  required=''/></td>
                    <td><input type="radio" name="accuracy" id="accuracy" value="15"  required=''/></td>
                    </tr>
                <tr>
                    <td>Rapport  * <span class="check_icon pull-right hidden"><i class="fa fa-check-circle text-success"></i></span></td>
                    <td><input type="radio" name="rapport" id="rapport" value="-5"  required=''/></td>
                    <td><input type="radio" name="rapport" id="rapport" value="1"  required=''/></td>
                    <td><input type="radio" name="rapport" id="rapport" value="5"  required=''/></td>
                    <td><input type="radio" name="rapport" id="rapport" value="10"  required=''/></td>
                    <td><input type="radio" name="rapport" id="rapport" value="15"  required=''/></td>
                    </tr>
                <tr>
                    <td>Communication  * <span class="check_icon pull-right hidden"><i class="fa fa-check-circle text-success"></i></span></td>
                    <td><input type="radio" name="communication" id="communication" value="-5"  required=''/></td>
                    <td><input type="radio" name="communication" id="communication" value="1"  required=''/></td>
                    <td><input type="radio" name="communication" id="communication" value="5"  required=''/></td>
                    <td><input type="radio" name="communication" id="communication" value="10"  required=''/></td>
                    <td><input type="radio" name="communication" id="communication" value="15"  required=''/></td>
                </tr>
                <tr>
                    <td>Lateness  * </td>
                    <td colspan="5">
                        <div class="radio-inline ri">
                            <label><input type="radio" name="is_late" value="0" checked onclick="func_lateness(this);">
                            <span class="label label-danger">No <i class="fa fa-remove"></i></span></label>
                        </div>
                        <div class="radio-inline ri">
                            <label><input type="radio" name="is_late" value="1" onclick="func_lateness(this);">
                            <span class="label label-success">Yes <i class="fa fa-check-circle"></i></span></label>
                        </div>
                        <input class="form-control hidden lateness" min="0" style="width: 200px;display: inline-block;" type="number" name="lateness" placeholder="Number of ocassions"/>
                    </td>
                </tr>
                <tr>
                    <td>Missed jobs / Non Attendances  * </td>
                    <td colspan="5">
                        <div class="radio-inline ri">
                            <label><input type="radio" name="is_miss" value="0" checked onclick="func_missed(this);">
                            <span class="label label-danger">No <i class="fa fa-remove"></i></span></label>
                        </div>
                        <div class="radio-inline ri">
                            <label><input type="radio" name="is_miss" value="1" onclick="func_missed(this);">
                            <span class="label label-success">Yes <i class="fa fa-check-circle"></i></span></label>
                        </div>
                        <input class="form-control hidden missed" min="0" style="width: 200px;display: inline-block;" type="number" name="missed" placeholder="Number of missed jobs"/>
                    </td>
                </tr>
              </table>
              <div class="bg-info col-md-12 form-group"><h4>PERSON GIVING FEEDBACK</h4></div>
              <div class="col-md-6 form-group">
                <label>Your Name</label>
                <input class="form-control" type="text" name="submit_name" placeholder="Enter your name"/>
              </div>
              <div class="col-md-6 form-group">
                <label>Your Email</label>
                <input class="form-control" type="text" name="submit_email" placeholder="Enter your email"/>
              </div>
              <div class="col-md-12 form-group">
                <label>Enter your remarks</label>
                <textarea class="form-control" rows="5" name="remarks" placeholder="Write your remarks about this interpreter"></textarea>
              </div>
              <div class="form-group col-md-6 col-sm-6">
                <script src='https://www.google.com/recaptcha/api.js'></script>
                <div class="g-recaptcha" data-sitekey="6LextRoUAAAAAGSGzslurL5xeNDw3lDDVkxM9rZe"></div>
                <br><button class="btn btn-primary" type="submit" name="btn_submit">Submit &raquo;</button>
              </div>
        </form>
        <?php }else{ echo "<center><br><br><br><br><br><br><br><br><h3 class='text-danger'>This record has already been confirmed.<br>Thank you</h4><br><br><br><br><br><br></center>"; } ?>
         <div class="col-md-12">
            <hr>
            <?php include'source/our_client.php'; ?>
        </div>
    </section>
      <!-- begin footer -->
      <?php include'source/footer.php'; ?>
      <!-- end footer -->  
      </div>
      <!-- end container -->
   </body>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
<script>
function func_lateness(elem){
    var late=$(elem).val();
    if (late=='1'){
        $('.lateness').removeClass('hidden');
        $('.lateness').attr("required","required");
    }else{
        $('.lateness').addClass('hidden');
        $('.lateness').removeAttr("required");
    }
}
function func_missed(elem){
    var miss=$(elem).val();
    if (miss=='1'){
        $('.missed').removeClass('hidden');
        $('.missed').attr("required","required");
    }else{
        $('.missed').addClass('hidden');
        $('.missed').removeAttr("required");
    }
}
$(document).ready(function()  {
     $('table tr td').click(
        function () {
            $(this).find('input').attr('checked','checked');
            $(this).parents('tr').find('.check_icon').removeClass('hidden');
        }
  );
});
</script>
</html>