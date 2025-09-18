<?php 
//php mailer library
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
require 'lsuk_system/phpmailer/vendor/autoload.php';
$mail = new PHPMailer(true);
include'source/class.php';
$id=isset($_GET['id']) && !empty($_GET['id'])?base64_decode($_GET['id']):0;
$table=isset($_GET['tbl']) && !empty($_GET['tbl'])?base64_decode($_GET['tbl']):'interpreter';
$jobs_array=array("interpreter"=>"Face To Face","telephone"=>"Telephone","translation"=>"Translation");
$get_query=$acttObj->read_specific("$table.*,interpreter_reg.name","$table,interpreter_reg","$table.intrpName=interpreter_reg.id and $table.deleted_flag=0 and $table.order_cancel_flag=0 and $table.id=".$id);
$feedback=$acttObj->read_specific("count(*) as counter","interp_assess","table_name='".$table."' AND order_id=".$id)['counter'];
$company=$acttObj->read_specific("name","comp_reg","abrv='".$get_query['orgName']."'")['name'];
if($id==0 || empty($get_query['name'])){
  echo "<script>window.location.href='index.php';</script>";
}
if(isset($_POST['btn_submit'])){
  if(!empty($_POST['submit_name'])){
    if(isset($_POST['g-recaptcha-response']) && !empty($_POST['g-recaptcha-response'])){
      $secret='6LextRoUAAAAAPvBF31eiYCmVP7Ne8a6mSez83zl';
      $ip=$_SERVER['REMOTE_ADDR'];
      $captcha=$_POST['g-recaptcha-response'];
      $rsp=file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=$secret&response=$captcha&remoteip=$ip");
      $arr=json_decode($rsp,TRUE);
      if($arr['success']){
        $appearance=$_POST['appearance']?:15;
        $done=$acttObj->insert("interp_assess",array("punctuality"=>$_POST['punctuality'],"appearance"=>$appearance,"professionalism"=>$_POST['professionalism']
        ,"confidentiality"=>$_POST['confidentiality'],"impartiality"=>$_POST['impartiality'],"accuracy"=>$_POST['accuracy'],"rapport"=>$_POST['rapport']
        ,"communication"=>$_POST['communication'],"get_feedback"=>"Online","p_reason"=>$_POST['remarks'],"submittedBy"=>$_POST['submit_name'],"p_feedbackby"=>$_POST['submit_name'],"order_id"=>$id,"orgName"=>$get_query['orgName'],"interpName"=>"id-".$get_query['intrpName'],"dated"=>date('Y-m-d'),"table_name"=>$table));
        $acttObj->update("feedback_requests",array("status"=>1),array("order_id"=>$id,"table_name"=>$table));
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
  }else{
    echo '<script>alert("Please fill up all the fields with your name.Thank you");window.history.back(-1);</script>';
  }
}
$feedback=$acttObj->read_specific("count(*) as counter","interp_assess","table_name='".$table."' AND order_id=".$id)['counter']; ?>
<!DOCTYPE HTML>
<html class="no-js">
   <head>
    <?php include 'source/header.php'; ?>
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
            <h1>Client Feedback Form</h1>
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
         if(!is_null($get_query['name']) && $feedback==0){ ?>
            <form class="col-md-8 col-md-offset-2" action="#" method="post" enctype="multipart/form-data">
                <table width="100%" align="center" class="table table-bordered">
                <tr>
                    <td>Organization</td>
                    <td><b><span class="label label-primary" style="font-size:16px;"><?php echo $company; ?></span></b></td>
                    <td>Job ID #</td>
                    <td><strong><?php echo $jobs_array[$table]." Project ID (".$get_query['id'].")"; ?></strong></td>
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
                <table width="100%" align="center" class="table table-bordered">
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
                <tr <?php echo $table!="interpreter"?"hidden":""; ?>>
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
              </table>
              <div class="bg-info col-md-12 form-group"><h4>PERSON GIVING FEEDBACK</h4></div>
              <div class="col-md-6 form-group">
                <label>Your Name</label>
                <input class="form-control" type="text" name="submit_name" placeholder="Enter your name"/>
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
        <?php }else{ echo "<center><br><br><br><br><br><br><br><br><h3 class='text-danger'>Feedback for this job has already been recorded.<br>Thank you</h4><br><br><br><br><br><br></center>"; } ?>
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
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
<script>
$(document).ready(function()  {
     $('table tr td').click(
        function () {
            //$(this).parents('tr').find('input').removeAttr('checked');
            $(this).find('input').attr('checked','checked');
            $(this).parents('tr').find('.check_icon').removeClass('hidden');
        }
  );
});
</script>
</html>