<?php
include'source/db.php'; 
include'source/class.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
require 'lsuk_system/phpmailer/vendor/autoload.php';
$mail = new PHPMailer(true);

?>
<!DOCTYPE HTML>
<html class="no-js">
<head>
    <title>LSUK Survey</title>
<meta name="google-site-verification" content="FD3pfiOXrr6D1lGvNWqseAJrL1PMPj1nguqXAd5mFkY" />
<meta name="google-site-verification" content="FD3pfiOXrr6D1lGvNWqseAJrL1PMPj1nguqXAd5mFkY" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" />
    <script src="js/jquery-1.8.2.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.0.3/js/bootstrap.min.js"></script>
	<style>
		.radio-group{
			display: flex;
			justify-content : space-between;
		}
	</style>
</head>
<?php
$id = 0;
// error_reporting(E_ALL);
if(!isset($_POST['submit'])){
if(isset($_GET['token']) && !empty($_GET['token'])){
	$id = base64_decode($_GET['token']);
	if(is_numeric($id)){
	$table = 'survey';
    $row = $acttObj->read_specific("*","$table","id=".$id);
	if(empty($row)){
         echo '<h1>No servey Found. plz click on the link your received in your email</h1>';
		 exit;
	}
}else{
	echo '<h1>No Record found</h1>';exit;
}
}else{
	echo '<h1>Invalide Request</h1>';
	exit;
}
if(!empty($row) && $row['is_submitted'] == 1){
	echo '<h1>You already submitted this survey</h1>';exit;
}
}
if(isset($_POST['submit'])){
	$user_id = $_POST['user_id'];
	$row = $acttObj->read_specific("*","survey","id=".$user_id);
	if(!empty($row) && $row['is_submitted'] == 1){
		echo '<h1>You already submitted this survey</h1>';exit;
	}
	$survey = [
		'a1' => $_POST['a1'],
		'a2' => $_POST['a2'],
		'a3' => $_POST['a3'],
		'a4' => $_POST['a4'],
		'a5' => $_POST['a5'],
		'a6' => $_POST['a6'],
		'a7' => $_POST['a7'],
		'a8' => $_POST['a8'],
		'a9' => $_POST['a9'],
		'a10' => $_POST['a10'],
		'a11' => $_POST['a11'],
		'a12' => $_POST['a12'],
		'a13' => $_POST['a13'],
		'is_submitted' => 1
	];
	$id = $user_id;
    $acttObj->update('survey',$survey,["id" => $user_id]);
	$msg = '<h1 style="background-color:green;color:white;padding:25px">Thank You for your time</h1>';
    $from_email = "hr@lsuk.org";
    $subject = 'Mental Health Survey';
	try {
		$mail->SMTPDebug = 1;
		//$mail->isSMTP(); 
		//$mailer->Host = 'lsuk-org.mail.protection.outlook.com';
		$mail->SMTPAuth   = true;
		$mail->Username   = 'info@lsuk.org';
		$mail->Password   = 'LangServ786';
		$mail->SMTPSecure = 'tls';
		$mail->Port       = 587;//25 working
		//$mail->AddEmbeddedImage('img/logo.png', 'LSUK');
		$message = "Dear Julie Musk".'<br>';
		$message .= "<p>Here is my Survey result</p>";
		$message .= "<p><strong>Q1 :</strong> Do you feel AWP staff are adequately prepared for using an interpreter?<br><strong>Ans 1: ".$_POST['a1']."</strong></p>";
		$message .= "<p><strong>Q2 :</strong>Do AWP staff treat you with respect and professionalism?<br><strong>Ans 2: ".$_POST['a2']."</strong></p>";
		$message .= "<p><strong>Q3 :</strong>When attending an interpreting appointment for AWP- do you feel you have enough information about the situation you are about to interpret in?<br><strong>Ans 3: ".$_POST['a3']."</strong></p>";
		$message .= "<p><strong>Q4 :</strong>Do you feel confident about how to feedback any concerns about your work within AWP?<br><strong>Ans 4: ".$_POST['a4']."</strong></p>";
		$message .= "<p><strong>Q5 :</strong>Do you feel adequately prepared to work in mental health settings?<br><strong>Ans 5: ".$_POST['a5']."</strong></p>";
		$message .= "<p><strong>Q6 :</strong>Do you feel adequately supported when working in mental health (e.g. in terms of training, supervision or emotional support)?<br><strong>Ans 6: ".$_POST['a6']."</strong></p>";
		$message .= "<p><strong>Q7 :</strong>Do you have any specific training needs for working in mental health?<br><strong>Ans 7: ".$_POST['a7']."</strong></p>";
		$message .= "<p><strong>Q8 :</strong>What is your level of interpreting qualification ?<br><strong>Ans 8: ".$_POST['a8']."</strong></p>";
		$message .= "<p><strong>Q9 :</strong>How much experience have you had in working in mental health?<br><strong>Ans 9: ".$_POST['a9']."</strong></p>";
		$message .= "<p><strong>Q10 :</strong>What training have you had in working in mental health?<br><strong>Ans 10: ".$_POST['a10']."</strong></p>";
		$message .= "<p><strong>Q11 :</strong>Do you feel you would benefit from more training?<br><strong>Ans 11: ".$_POST['a11']."</strong></p>";
		$message .= "<p><strong>Q12 :</strong>Any other comments<br><strong>Ans 12: ".$_POST['a12']."</strong></p>";
		$message .= "<p><strong>Q13 :</strong>Would you like us to send you a copy of the survey report ?<br><strong>Ans 13: ".$_POST['a13']."</strong></p>";
		$mail->Sender = $from_email;
		$mail->setFrom($from_email, 'Mental Health Survey');
		// $mail->addAddress('waseemsunktk@gmail.com');
		$mail->addAddress('Julie.musk@nhs.net');

		$mail->addReplyTo($from_email, 'LSUK');
		$mail->isHTML(true);
		$mail->Subject = $subject;
		$mail->Body    = $mail->msgHtml($message);
		$mail->send();
		$mail->ClearAllRecipients();
} catch (Exception $e) {
	echo "Message could not be sent! Mailer library error for: ".$row_reminder['interpreter_id'];
	echo '<pre>';
	print_r($e);exit;
}

}
?>
<body style="margin-top:30px;background: #f2f2f2;">
<form method="post" action="" enctype="multipart/form-data">
<input type='hidden' name='user_id' value='<?php echo $id; ?>'/>
		<div class="container-fluid">
		    <div class="col-md-8 col-md-offset-2" style="background: white;box-shadow: 0 0 16px 1px #d6d8d9;padding:8px;">
		    <div class="bg-info text-center" style="padding: 10px;">
		        <h4>Mental Health Survey for Interpreters for circulation in LSUK</h4>
		    </div>
		     <div class="col-md-8 col-md-offset-2"><br/>
		     <?php if(isset($msg) && !empty($msg)){echo $msg;} ?><br/>
		     </div>
			<div class="col-lg-8 col-lg-offset-2" id="div_login">
            <div class="form-group">
		        <p>Do you feel AWP staff are adequately prepared for using an interpreter?</p>
				<div class='radio-group'>
					<label><input type="radio" name='a1' value='always' required/>Always</label>
					<label><input type="radio" name='a1' value='sometimes'/>SomeTimes</label>
					<label><input type="radio" name='a1' value='never'/>Never</label>
	            </div>
		    </div>
			<div class="form-group">
		        <p>Do AWP staff treat you with respect and professionalism?</p>
				<div class='radio-group'>
					<label><input type="radio" name='a2' value='always' required/>Always</label>
					<label><input type="radio" name='a2' value='sometimes'/>SomeTimes</label>
					<label><input type="radio" name='a2' value='never'/>Never</label>
	            </div>
		    </div>
			<div class="form-group">
		        <p>When attending an interpreting appointment for AWP- do you feel you have enough information about the situation you are about to interpret in?</p>
				<div class='radio-group'>
					<label><input type="radio" name='a3' value='always' required/>Always</label>
					<label><input type="radio" name='a3' value='sometimes'/>SomeTimes</label>
					<label><input type="radio" name='a3' value='never'/>Never</label>
	            </div>
		    </div>
			<div class="form-group">
		        <p>Do you feel confident about how to feedback any concerns about your work within AWP?</p>
				<div class='radio-group'>
					<label><input type="radio" name='a4' value='always' required/>Always</label>
					<label><input type="radio" name='a4' value='sometimes'/>SomeTimes</label>
					<label><input type="radio" name='a4' value='never'/>Never</label>
	            </div>
		    </div>
			<div class="form-group">
		        <p> Do you feel adequately prepared to work in mental health settings?</p>
				<div class='radio-group'>
					<label><input type="radio" name='a5' value='always' required/>Always</label>
					<label><input type="radio" name='a5' value='sometimes'/>SomeTimes</label>
					<label><input type="radio" name='a5' value='never'/>Never</label>
	            </div>
		    </div>
			<div class="form-group">
		        <p>Do you feel adequately supported when working in mental health (e.g. in terms of training, supervision or emotional support)?</p>
				<div class='radio-group'>
					<label><input type="radio" name='a6' value='always' required/>Always</label>
					<label><input type="radio" name='a6' value='sometimes'/>SomeTimes</label>
					<label><input type="radio" name='a6' value='never'/>Never</label>
	            </div>
		    </div>
			<div class="form-group">
		        <p>Do you have any specific training needs for working in mental health?</p>
				<div class='radio-group'>
					<label><input type="radio" name='a7' value='always' required/>Always</label>
					<label><input type="radio" name='a7' value='sometimes'/>SomeTimes</label>
					<label><input type="radio" name='a7' value='never'/>Never</label>
	            </div>
		    </div>
			<div class='form-group'>
			    <h1>About you (optional)</h1>
			</div>
			<div class="form-group">
		        <p>What is your level of interpreting qualification ?</p>
				<div class='radio-group'>
				<input type='text' name='a8' class='form-control'/>
	            </div>
		    </div>
			<div class="form-group">
		        <p>How much experience have you had in working in mental health?</p>
				<div class='radio-group'>
					<label><input type="radio" name='a9' value='always'/>Alot</label>
					<label><input type="radio" name='a9' value='sometimes'/>Some</label>
					<label><input type="radio" name='a9' value='never'/>Only a little</label>
					<label><input type="radio" name='a9' value='never'/>No</label>
	            </div>
		    </div>
			<div class="form-group">
		        <p>What training have you had in working in mental health?</p>
				<div class='radio-group'>
					<input type='text' name='a10' class='form-control'/>
	            </div>
		    </div>
			<div class="form-group">
		        <p>Do you feel you would benefit from more training?</p>
				<div class='radio-group'>
					<label><input type="radio" name='a11' value='always'/>Yes</label>
					<label><input type="radio" name='a11' value='sometimes'/>No</label>
	            </div>
		    </div>
			<div class="form-group">
		        <p>Any other comments</p>
				<div class='radio-group'>
					<textarea rows="4" name='a12' cols="50"></textarea>
	            </div>
		    </div>
			<div class="form-group">
		        <p>Would you like us to send you a copy of the survey report ?</p>
				<div class='radio-group'>
					<label><input type="radio" name='a13' value='always'/>Yes</label>
					<label><input type="radio" name='a13' value='sometimes'/>No</label>
	            </div>
		    </div>
				
				
			</div>
			<div class="form-group"> 
				<input type="submit" class="btn btn-primary col-md-4" name="submit" value="Submit Survey" />
			</div>
			</div>
</div>
</div>
</form>
<!-- end container -->
</body>
<script>
</html>