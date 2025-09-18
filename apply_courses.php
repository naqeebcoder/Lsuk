<?php 
//php mailer library
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
require 'lsuk_system/phpmailer/vendor/autoload.php';
$mail = new PHPMailer(true);

include'source/class.php';?>
<!DOCTYPE HTML>
<html class="no-js">
<head>
<?php include'source/header.php'; ?>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" type="text/css" href="lsuk_system/css/bootstrap.css" />
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
            <h1>Apply for Community Interpreting Course</h1>
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
 <?php
if(isset($_POST['g-recaptcha-response']) && $_POST['g-recaptcha-response']){ 
$secret='6LextRoUAAAAAPvBF31eiYCmVP7Ne8a6mSez83zl';
$ip=$_SERVER['REMOTE_ADDR'];
$captcha=$_POST['g-recaptcha-response'];
$rsp=file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=$secret&response=$captcha&remoteip=$ip");
$arr=json_decode($rsp,TRUE);
if($arr['success']){$captcha_flag=1;}else{$captcha_flag=0;}
}
$msg='';
if(isset($_POST['submit']) && $captcha_flag==1){
    if(!empty($_POST['first_name']) && !empty($_POST['last_name']) && !empty($_POST['email']) && !empty($_POST['course_id']) && !empty($_POST['attend_date'])){
        $first_name=$_POST['first_name'];
      $last_name=$_POST['last_name'];
      $email=$_POST['email'];
      $contact=$_POST['contact'];
      $post_code=$_POST['post_code'];
      $city=$_POST['city'];
      $address=$_POST['address'];
      $course_id=$_POST['course_id'];
      $attend_date=(!empty($_POST['attend_date']) && $_POST['attend_date']!='0000-00-00')?$_POST['attend_date']:date('Y-m-d');
      $table='course_applies';
      $insert_id= $acttObj->get_id($table);
      $acttObj->editFun($table,$insert_id,'first_name',$first_name);
      $acttObj->editFun($table,$insert_id,'last_name',$last_name);
      $acttObj->editFun($table,$insert_id,'email',$email);
      $acttObj->editFun($table,$insert_id,'contact',$contact);
      $acttObj->editFun($table,$insert_id,'post_code',$post_code);
      $acttObj->editFun($table,$insert_id,'city',$city);
      $acttObj->editFun($table,$insert_id,'address',$address);
      $acttObj->editFun($table,$insert_id,'dated',$attend_date);
      $acttObj->editFun($table,$insert_id,'course_id',$course_id);
      $course_name = $acttObj->unique_data('courses', 'name', 'id', $course_id);
	$from_add = "hr@lsuk.org";
	$subject =  'LSUK Community Interpreting Course Registration (Acknowledgement)';
	$message ="Hi <b>".ucwords($first_name).' '.ucwords($last_name)."</b>,<br>
	Thanks for sending us your details.<br>
	LSUK will shortly be in touch.<br>
	Thank you<br>
	Best Regards,<br>
	LSUK";
	$subject_lsuk =  'New Website Apply for Interpreting Course';
	$message_lsuk ="Hi <b>Admin</b>,<br>
	New applicant has applied for Community Interpreting Course.<br>
	Below are the details:<br>
	<table>
	<tr>
	<th style='border: 1px solid yellowgreen;padding:5px;'>Name</th>
	<td style='border: 1px solid yellowgreen;padding:5px;'>".ucwords($first_name).' '.ucwords($last_name)."</td>
	</tr>
	<tr>
	<th style='border: 1px solid yellowgreen;padding:5px;'>Email</th>
	<td style='border: 1px solid yellowgreen;padding:5px;'>".$email."</td>
	</tr>
	<tr>
	<th style='border: 1px solid yellowgreen;padding:5px;'>Contact Number</th>
	<td style='border: 1px solid yellowgreen;padding:5px;'>".$contact."</td>
	</tr>
	<tr>
	<th style='border: 1px solid yellowgreen;padding:5px;'>Course Name</th>
	<td style='border: 1px solid yellowgreen;padding:5px;'>".$course_name."</td>
	</tr>
	<th style='border: 1px solid yellowgreen;padding:5px;'>Date to attend from</th>
	<td style='border: 1px solid yellowgreen;padding:5px;'>".$attend_date."</td>
	</tr>
	<tr>
	<th style='border: 1px solid yellowgreen;padding:5px;'>City</th>
	<td style='border: 1px solid yellowgreen;padding:5px;'>".$city."</td>
	</tr>
	<tr>
	<th style='border: 1px solid yellowgreen;padding:5px;'>Post Code</th>
	<td style='border: 1px solid yellowgreen;padding:5px;'>".$post_code."</td>
	</tr>
	<tr>
	<th style='border: 1px solid yellowgreen;padding:5px;'>Address</th>
	<td style='border: 1px solid yellowgreen;padding:5px;'>".$address."</td>
	</tr>
	</table>
	Kindly respond quickly.<br>
	Thank you";
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
    $mail->setFrom($from_add, 'LSUK');
    $mail->addAddress($email);
    $mail->addReplyTo($from_add, 'LSUK');
    $mail->isHTML(true);
    $mail->Subject = $subject;
    $mail->Body    = $message;
    if($mail->send()){
        $mail->ClearAllRecipients();
            $mail->addAddress('hr@lsuk.org');
            $mail->addReplyTo($from_add, 'LSUK');
            $mail->isHTML(true);
            $mail->Subject = $subject_lsuk;
            $mail->Body    = $message_lsuk;
            $mail->send();
            $mail->ClearAllRecipients();
            $msg='<span class="alert alert-success col-md-12 text-center"><h5><b>Thanks for sending us your details.LSUK will shortly be in touch.</b></h5></span><br><br>';
            echo '<script>setTimeout(function(){ window.location.href="apply_courses.php"; }, 3000);</script>';
    }
} catch (Exception $e) { $msg='<span class="alert alert-danger col-md-12 text-center"><h5><b>Message could not be sent. Mailer library error!</b></h5></span><br><br>'; }
    }else{
        $msg='<span class="alert alert-warning col-md-12 text-center"><h5><b>Kindly fill all fields and try again!</b></h5></span><br><br>';
    }
} ?>
    <!-- begin content -->
    <section id="content" class="container clearfix">
  <link rel="stylesheet" type="text/css" href="style_form.css"/>
<div class="col-sm-12">
		     <?php if(isset($msg) && !empty($msg)){echo $msg;} ?><br/>
		     </div>
				<form class="sky-form" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
			  <fieldset>				
				  <div class="row">
                        <section class="col-md-12">
						  <center><img width="400" class="img-thumbnail" src="images/LSUK training qualifications UK.jpeg" alt="Language servivces training qualifications courses UK"/></center>
						</section>
						<section class="col col-6">
						  <label class="input">First Name
							 <input name="first_name" type="text" value="" required />
							</label>
						</section>
                        <section class="col col-6">
                         <label class="input">Last Name
							 <input name="last_name" type="text" value="" required /></label>
      </section>
                        <section class="col col-6">
						  <label class="input">Email Address
							 <input name="email" type="email" value="" required />
							</label>
						</section>
                        <section class="col col-6">
                         <label class="input">Contact Number
							 <input name="contact" type="text" value="" required /></label>
                        </section>
                     <section class="col col-6">
                         <label class="input">Post Code
      <input name="post_code" type="text"  value="" required />
      </label>
                    </section>
                  <section class="col col-6">
                         <label class="select">City
                         <select name="city" required>
      				<option value="" disabled selected>Select your City</option>
      				<optgroup label="England">
                      <option>Bath</option>
                      <option>Birmingham</option>
		      		  <option>Bradford</option>
                      <option>Bridgwater</option>
                      <option>Bristol</option>
                      <option>Buckinghamshire</option>
                      <option>Cambridge</option>
                      <option>Canterbury</option>
                      <option>Carlisle</option>
                      <option>Chippenham</option>
                      <option>Cheltenham</option>
                      <option>Cheshire</option>
                      <option>Coventry</option>
                      <option>Derby</option>
                      <option>Dorset</option>
                      <option>Exeter</option>
                      <option>Frome</option>
                      <option>Gloucester</option>
                      <option>Hereford</option>
                      <option>Leeds</option>
                      <option>Leicester</option>
                      <option>Liverpool</option>
                      <option>London</option>
                      <option>Manchester</option>
                      <option>Newcastle</option>
                      <option>Northampton</option>
                      <option>Norwich</option>
                      <option>Nottingham</option>
                      <option>Oxford</option>
                      <option>Plymouth</option>
                      <option>Pool</option>
                      <option>Portsmouth</option>
                      <option>Salford</option>
                      <option>Shefield</option>
                      <option>Somerset</option>
                      <option>Southampton</option>
                      <option>Swindon</option>
                      <option>Suffolk</option>
                      <option>Surrey</option>
                      <option>Taunton</option>
                      <option>Trowbridge</option>
                      <option>Truro</option>
                      <option>Warwick</option>
                      <option>Wiltshire</option>
                      <option>Winchester</option>
                      <option>Wells</option>
                      <option>Weston Super Mare</option>
                      <option>Worcester</option>
                      <option>Wolverhampton</option>
                      <option>York</option> 
                      <option>Not in List</option>
                    </optgroup>
                    <optgroup label="Scotland">
                      <option>Dundee</option>
                      <option>Edinburgh</option>
                      <option>Glasgow</option>
                       <option>Not in List</option>
                    </optgroup>
                    <optgroup label="Wales">
                      <option>Cardiff</option>
                      <option>Newport</option>
                      <option>Swansea</option>
                      <option>Not in List</option>
                    </optgroup>
                    </select>  
      </label>
                    </section>
                  <section class="col col-6">
                         <label class="select">Course Interested
                         <select name="course_id" required>
      				<option value="" disabled selected>Choose your Course</option>
      				<?php $courses_q=$acttObj->read_all('id,name','courses','status=1');
      				while($row_course=mysqli_fetch_assoc($courses_q)){ ?>
      				<option value="<?php echo $row_course['id']; ?>"><?php echo $row_course['name']; ?></option>
      				<?php } ?>
                    </select>  
      </label>
                    </section>
                        <section class="col col-6">
							<label class="input">Available to attend from     
							 <input name="attend_date" type="date" value="" required /></label>
                    </section>
                        <section class="col col-6">
							<label class="input">Enter your Address     
							 <textarea name="address" class="form-control" required rows="4"/></textarea></label>
                    </section>
                    </div>
			  </fieldset>
                    	<script src='https://www.google.com/recaptcha/api.js'></script>
                        <div class="g-recaptcha" data-sitekey="6LextRoUAAAAAGSGzslurL5xeNDw3lDDVkxM9rZe"></div>
                        <section class="col col-6">
                            <input type="submit" name="submit" class="button" value="Apply Now"/>
                    </section>
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
<script>
window.__lo_site_id = 300741;
	(function() {
		var wa = document.createElement('script'); wa.type = 'text/javascript'; wa.async = true;
		wa.src = 'https://d10lpsik1i8c69.cloudfront.net/w.js';
		var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(wa, s);
	  })();
	</script>
</html>