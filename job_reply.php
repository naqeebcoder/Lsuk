<?php if(session_id() == '' || !isset($_SESSION)){session_start();} ?> 
<?php include'source/db.php'; include'source/class.php'; if(isset($_POST['submit'])){$table='staff_job';$edit_id= $acttObj->get_id($table);}?>
<!DOCTYPE HTML>
<!--[if IE 8]> <html class="ie8 no-js"> <![endif]-->
<!--[if (gte IE 9)|!(IE)]><!--> <html class="no-js"> <!--<![endif]-->

<!-- Mirrored from ixtendo.com/themes/exquiso-html/about-us.html by HTTrack Website Copier/3.x [XR&CO'2014], Tue, 12 Jan 2016 09:48:33 GMT -->
<head>
<?php include'source/header.php'; ?>
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
            <h1>Staf Job </h1>
          <nav id="breadcrumbs">
               <ul>
                    <li><a href="index.php">Home</a> &rsaquo;</li>
                    <li><a href="<?php echo basename($_SERVER['HTTP_REFERER']);?>"><?php echo ucwords(basename($_SERVER['HTTP_REFERER'], '.php'));?></a> &rsaquo;</li>
                   
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
		

		<div align="center" style=" color:#069; font-size:18px;">Reply</div>
			<form class="sky-form" action="#" method="post">
			 
              <fieldset> <legend style="font-size:14px; color:#069;">Reply to the Applicant </legend>					
			   
					
					<div class="row">
						<section class="col col-6">
							<label class="input">Name *
      <input name="name" type="text" placeholder='' required='' id="name" value="<?php echo $_GET['name']; ?>" readonly/>
							</label>

						</section>
						<section class="col col-6">
                         <label class="input">Subject *
      <input name="sbjct" type="text" placeholder='' required='' id="sbjct"/>
      </label>

                    </section>
			    
					</div>
                    <section>
						<label class="textarea">Message
                        <textarea name="msg" cols="51" rows="5">Dear Applicant,
                        </textarea>
     
						</label>
					</section>
			  </fieldset>
				 
				
				<footer>
					<input type="submit" name="submit" class="button" value="Send Email"/>
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
if((@$_SESSION['email']=="imran@lsuk.org" || $_SESSION['interp_code']=='id-13') && isset($_POST['submit'])){ 
	$from_add = "info@lsuk.org"; 
	$to_add = $_GET['email']; //<-- put your yahoo/gmail email address here
	$subject = $_POST['sbjct'];
	$message = "<p>Dear ".$_POST['name'].",</p>

 
 
 <p>Translation Admin Team<br />
<p>Language Services UK Limited<br />
p>Acredited by Association of Translation Companies (ATC)<br />
  Registered in England &nbsp;and Wales 7760366<br />
  Phone: 01173290610 &nbsp; &nbsp; 07915177068 - 0333 7005785<br />
  Fax: 0333 800 5785<br />
Email: <a href='mailto:INFO@LSUK.ORG'>INFO@LSUK.ORG</a></p>
<p>&nbsp;</p>
<p>LSUK Limited provide professional, accurate and timely Interpreting and  Translation services for many languages. We are&nbsp; bridging the gaps to  connect words / worlds for less. Please contact us for more details. Bookings  can be made Online, over the Phone, via Email or Fax.</p>
The information in this e-mail is confidential. The  contents may not be disclosed or used by anyone other than the addressee. If  you are not the intended recipient, please notify the sender immediately by  reply e-mail and delete this message instantly. LSUK Limited cannot accept any  responsibility for the accuracy or completeness of this message as it has been  transmitted over a public network. For more information on our products and  services please visit <a href='http://www.lsuk.org/'>WWW.LSUK.ORG</a>";

	$headers = "From: $from_add \r\n";
	$headers .= "Reply-To: $from_add \r\n";
	$headers .= "Return-Path: $from_add\r\n";
	$headers .= "X-Mailer: PHP \r\n";
$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";	
if(mail($to_add,$subject,$message,$headers)){echo "<script>alert('Email submited to candidate!');</script>";}else {echo "<script>alert('Email not submited to candidate!');</script>";}}
?>
