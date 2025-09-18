<?php exit;if(session_id() == '' || !isset($_SESSION)){session_start();} 
//php mailer library
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
require 'lsuk_system/phpmailer/vendor/autoload.php';
$mail = new PHPMailer(true);

if(isset($_POST['g-recaptcha-response']) && $_POST['g-recaptcha-response']){ //var_dump($_POST);

$secret='6LextRoUAAAAAPvBF31eiYCmVP7Ne8a6mSez83zl';

$ip=$_SERVER['REMOTE_ADDR'];

$captcha=$_POST['g-recaptcha-response'];

$rsp=file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=$secret&response=$captcha&remoteip=$ip");

//var_dump($rsp);

$arr=json_decode($rsp,TRUE);

if($arr['success']){$captcha_flag=1;}else{echo 'Spam';}



}

?>



<?php include'source/db.php'; include'source/class.php';$flag=@$_GET['flag']; if(isset($_POST['submit']) && $captcha_flag==1){  if(!empty($flag)){$table='blog_rep';}else{$table='blog';}$edit_id= $acttObj->get_id($table);}else{echo '<script language = "JavaScript" type = "text/JavaScript">alert("Please Proceed Captcha");</script>';}?>

<!DOCTYPE HTML>



<head>

<?php include'source/header.php'; ?>

	<meta name="viewport" content="width=device-width, initial-scale=1.0">

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

            <h1> <?php if(!empty($flag)){ echo "Reply";}else{ echo 'Blog'; }?> </h1>

          <nav id="breadcrumbs">

               <ul>

                    <li><a href="index.php">Home</a> &rsaquo;</li>

                    <li><a href="<?php echo basename($_SERVER['HTTP_REFERER']);?>"><?php echo ucwords(basename($_SERVER['HTTP_REFERER'], '.php'));?></a> &rsaquo;</li>

                    <li><?php echo ucwords(basename($_SERVER['REQUEST_URI'], '.php'));?></li>

            </ul>

          </nav>

        </div>

    </section>

    <!-- begin page title -->

    

    <!-- begin content -->

    <section id="content" class="container clearfix">

    	<!-- begin our company -->

 

    <!-- begin content -->

    <section id="content" class="container clearfix">

  <link rel="stylesheet" type="text/css" href="style_form.css"/>

		



		<div align="center" style=" color:#069; font-size:18px;"> Blog</div>

			<form class="sky-form" action="#" method="post">

			 

              <fieldset> <legend style="font-size:14px; color:#069;">Post a Blog</legend>					

			    <div class="row">

						<section class="col col-6">

						  <label class="input">Full Name *

      <input name="name" type="text" placeholder='' required='' id="name"/>

						  </label>

		 <?php if(isset($_POST['submit'])){$data=$_POST['name']; $acttObj->editFun($table,$edit_id,'name',$data);} ?>   

						</section>

                  <section class="col col-6">

                         <label class="input">Title *

      <input name="title" type="text" placeholder='' required='' id="title"/>

							</label>

		<?php if(isset($_POST['submit'])){$data=$_POST['title']; $acttObj->editFun($table,$edit_id,'title',$data);} ?> 

                    </section>

			    </div>

					

					<div class="row">

						

						<section class="col col-6">

                         <label class="input">Email *

      <input name="email" type="text" placeholder='' required='' id="email"/>

      </label>

     <?php if(isset($_POST['submit'])){$data=$_POST['email']; $acttObj->editFun($table,$edit_id,'email',$data);} ?>

                    </section>

			     <section class="col col-6">

                         <label class="select">Type *

						

						  <select name="type" id="type" required=''>

						 <option value="0">..Select..</option>

                         <?php if(!empty($flag)){ ?>

						 <option>Reply</option>

                         <?php }else{ ?>

						 <option>Blog</option>

						 <option>Query</option>

						 <option>Question</option>

                         <?php } ?>

					      </select></label>

						 <?php if(isset($_POST['submit'])){$data=$_POST['type']; $acttObj->editFun($table,$edit_id,'type',$data);} ?>

                        </section>

					</div>

                    <section>

						<label class="textarea">Text

                        <textarea name="comments" cols="51" rows="5" required=''></textarea>

    <?php if(isset($_POST['submit'])){$data=$_POST['comments']; $acttObj->editFun($table,$edit_id,'comments',$data);} ?>

						</label>

					</section>

                    <script src='https://www.google.com/recaptcha/api.js'></script>

                        <div class="g-recaptcha" data-sitekey="6LextRoUAAAAAGSGzslurL5xeNDw3lDDVkxM9rZe"></div>

			  </fieldset>

				 

				



				<footer>

					<input type="submit" name="submit" class="button" value="Submit"/>

				</footer>

</form>



    </section>

    <!-- end content -->  

    

        <hr>

        

     	<!-- begin clients -->

       <?php include'source/our_client.php'; ?>

        <!-- end clients -->   

    </section>

    <!-- end content -->  

    

    <!-- begin footer -->

	<?php include'source/footer.php'; ?>

	<!-- end footer -->  

</div>

<!-- end container -->

</body>

<!-- Mirrored from ixtendo.com/themes/exquiso-html/about-us.html by HTTrack Website Copier/3.x [XR&CO'2014], Tue, 12 Jan 2016 09:49:13 GMT -->

</html>

<?php

if(isset($_POST['submit']) && $captcha_flag==1){	

	$from_add = "info@lsuk.org";

	$to_add = "infolsuk786@gmail.com"; //<-- put your yahoo/gmail email address here

	$subject = 'Blog Posting Acknowledgement'; //"Order for Translation";

	$message ="<p>Dear LSUK Team</p>



<p>You have received a blog posting acknowledgement</p>

";
//php mailer used at top
try {
    $mail->SMTPDebug = 0;
    //$mail->isSMTP(); 
    //$mailer->Host = 'smtp.office365.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'info@lsuk.org';
    $mail->Password   = 'LangServ786';
    $mail->SMTPSecure = 'tls';
    $mail->Port       = 587;
    $mail->setFrom('info@lsuk.org', 'LSUK Blog Post');
    $mail->addAddress($to_add);
    $mail->addReplyTo($from_add);
    $mail->isHTML(true);
    $mail->Subject = $subject;
    $mail->Body    = $message;
    if($mail->send()){
    $mail->ClearAllRecipients(); ?>
<script>alert("Blog has been posted successfuly.Thanks you.");</script>
<?php }else{?>
<script>alert("Failed to post your blog!");</script>
<?php }
} catch (Exception $e) { ?>
<script>alert("Mailer library error! Contact support");</script>
<?php }	
} 
?>

<?php if(isset($_POST['submit'])){if(!empty($flag)){$acttObj->editFun($table,$edit_id,'rep_id',$flag);}echo "<script>alert('Successful!');</script>";}?>

