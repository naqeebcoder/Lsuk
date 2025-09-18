<?php 
//php mailer library
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
require 'lsuk_system/phpmailer/vendor/autoload.php';
$mail = new PHPMailer(true);
include'source/class.php';
error_reporting(E_ALL);
ini_set("display_errors", 1);
$id = isset($_GET['event_id']) && !empty($_GET['event_id']) ? base64_decode(urldecode($_GET['event_id'])) : 0;
$intrp_id = isset($_GET['id']) && !empty($_GET['id']) ? base64_decode(urldecode($_GET['id'])) : 0;
$interpreter_id=$acttObj->read_specific("interpreter_id","cpd_events","event_id=".$id." and interpreter_id=".$intrp_id)['interpreter_id'];
$get_query=$acttObj->read_specific("interpreter_reg.id,interpreter_reg.name,interpreter_reg.email","interpreter_reg","interpreter_reg.id=".$interpreter_id);
if($id==0 || empty($get_query['name'])){
  echo "<script>window.location.href='index.php';</script>";
}
if(isset($_POST['btn_submit'])){
    if(true){//isset($_POST['g-recaptcha-response']) && !empty($_POST['g-recaptcha-response'])
      $secret='6LextRoUAAAAAPvBF31eiYCmVP7Ne8a6mSez83zl';
      $ip=$_SERVER['REMOTE_ADDR'];
      $captcha=$_POST['g-recaptcha-response'];
      $rsp=file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=$secret&response=$captcha&remoteip=$ip");
      //$arr=json_decode($rsp,TRUE);
      $arr['success']=true;
      if($arr['success']){
        $reply=$_POST['reply'] && $_POST['reply']=="Yes"?1:2;
        //$attend_type=$_POST['attend_type'] && $_POST['attend_type']=="1"?1:0;
        $remarks=$_POST['remarks'];
        $done=$acttObj->update("cpd_events",array("reply"=>$reply,"remarks"=>$remarks,"updated_date"=>date('Y-m-d h:i:s')),array("event_id"=>$id,"interpreter_id"=>$intrp_id));
        if($done){
              $msg='<div class="alert alert-success alert-dismissible col-md-6 col-md-offset-3 text-center">
              <a href="#" class="close" data-dismiss="alert" aria-label="close">&times; </a>
              <i class="glyphicon glyphicon-check"></i> Success: Your feedback has been submitted successfully.<br>
              Thank you
              </div>';
              echo '<script>setTimeout(function(){ window.location="index.php"; }, 3000); </script>';
          }else{
              $msg='<div class="alert alert-danger alert-dismissible col-md-6 col-md-offset-3 text-center">
              <a href="#" class="close" data-dismiss="alert" aria-label="close">&times; </a>
              <i class="glyphicon glyphicon-check"></i> Failed: Failed to submit your feedback !<br>
              Kindly try again.</div>';
          }
      }else{
        echo '<script>alert("Kindly verify your catpahca. Kindly try again.");</script>';
      }
    }else{
      echo '<script>alert("Kindly verify your catpahca. Kindly try again.");window.history.back(-1);</script>';
    }
}
$cpd_feedback=$acttObj->read_specific("count(*) as counter","cpd_events","event_id='".$id."' AND reply!=0 AND interpreter_id=".$intrp_id)['counter']; ?>
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
            <h1>CPD Event Form</h1>
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
         if($cpd_feedback==0){ 
          $acttObj->read_specific("*","cpd_events","event_id=".$id);
          ?>
          <?php $event=$acttObj->read_specific("*","events","id='".$id."'");
           ?>
            <form class="col-md-8 col-md-offset-2" action="#" method="post" enctype="multipart/form-data">
                <table width="100%" align="center" class="table table-bordered">
                <tr>
                    <td colspan="4" align="center"><i class="text-danger"><b><?php echo $event['description'] ?></b></i></td>
                </tr>
                <tr>
                    <td>Interpreter Name</td>
                    <td><b><span class="label label-primary" style="font-size:16px;"><?php echo $get_query['name']; ?></span></b></td>
                    <td>Event Name</td>
                    <td><strong><?php echo $event['title'] ?></strong></td>
                    </tr>
                    <tr>
                    <td>Event Date</td>
                    <td><b><?= date('l d.m.Y', strtotime($event['from_date'])) ?></b></td>
                    <td>Event Time</td>
                    <td><strong><?= date('H:i', strtotime($event['from_date'])) ?> to <?= date('H:i', strtotime($event['to_date'])) ?></strong></td>

                    </tr>
                </table>
                <table width="60%" align="center" class="table table-bordered">
                <tr>
                    <td width="30%">Are you attending this event?</td>
                    <td><label class="btn btn-default"><input type="radio" name="reply" value="Yes"/> Yes</label>&nbsp;&nbsp;&nbsp;
                        <label class="btn btn-default"><input type="radio" name="reply" value="No"/> No</label>
                    </td>
                </tr>
                <tr class="tr_how_attend hidden">
                    <td width="30%">How would you like to attend?</td>
                    <td><label class="btn btn-default"><input type="radio" name="attend_type" value="0"/> In-Person</label>&nbsp;&nbsp;&nbsp;
                        <label class="btn btn-default"><input type="radio" name="attend_type" value="1"/> Remotely</label>
                    </td>
                </tr>
              </table>
              <div class="bg-info col-md-12 form-group"><h4>REMARKS ABOUT THIS EVENT</h4></div>
              <div class="col-md-12 form-group">
                <label>Ask your question (if any)</label>
                <textarea class="form-control" rows="5" name="remarks" placeholder="Write your remarks about this event"></textarea>
              </div>
              <div class="form-group col-md-6 col-sm-6">
                <script src='https://www.google.com/recaptcha/api.js'></script>
                <div class="g-recaptcha" data-sitekey="6LextRoUAAAAAGSGzslurL5xeNDw3lDDVkxM9rZe"></div>
                <br><button class="btn btn-primary" type="submit" name="btn_submit">Submit &raquo;</button>
              </div>
        </form>
        <?php }else{ echo "<center><br><br><br><br><br><br><br><br><h3 class='text-danger'>Feedback for this event has already been recorded.<br>Thank you</h4><br><br><br><br><br><br></center>"; } ?>
         <div class="col-md-12">
            <hr>
        </div>
    </section>
      <!-- begin footer -->
      <?php include'source/footer.php'; ?>
      <!-- end footer -->  
      </div>
      <!-- end container -->
   </body>
</html>