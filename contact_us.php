<?php if(session_id() == '' || !isset($_SESSION)){session_start();} 
//php mailer library
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
require 'lsuk_system/phpmailer/vendor/autoload.php';
$mail = new PHPMailer(true);

?> 
<!DOCTYPE HTML>
<html class="no-js"><head>
<meta name="google-site-verification" content="FD3pfiOXrr6D1lGvNWqseAJrL1PMPj1nguqXAd5mFkY" />
<meta name="google-site-verification" content="FD3pfiOXrr6D1lGvNWqseAJrL1PMPj1nguqXAd5mFkY" />
<link rel="stylesheet" href="https://netdna.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">
<script async src="https://www.googletagmanager.com/gtag/js?id=AW-962450822"></script> <script> window.dataLayer = window.dataLayer || []; function gtag(){dataLayer.push(arguments);} gtag('js', new Date()); gtag('config', 'AW-962450822'); </script>
<script> gtag('config', 'AW-962450822/YMYoCLammcoZEIar98oD', { 'phone_conversion_number': '0117-3290610' }); </script>
<script src="prefixfree.min.js"></script>
<script src="source/modernizr.min.js"></script>
<script src="source/jquery-2.1.3.min.js"></script> 
<script>
$(document).ready(function(){ 
	var touch 	= $('#resp-menu');
	var menu 	= $('.menu');
 
	$(touch).on('click', function(e) {
		e.preventDefault();
		menu.slideToggle();
	});
	
	$(window).resize(function(){
		var w = $(window).width();
		if(w > 767 && menu.is(':hidden')) {
			menu.removeAttr('style');
		}
	});
	
});
</script>
<script type="text/javascript">

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-36251023-1']);
  _gaq.push(['_setDomainName', 'jqueryscript.net']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'https://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script>
<script type="text/javascript"> 
function disableselect(e){  
return false  
}  

function reEnable(){  
return true  
}  

document.onselectstart=new Function ("return false")  
if (g_bWantContextMenus)
    document.oncontextmenu=new Function ("return false")  
if (window.sidebar){  
document.onmousedown=disableselect  
document.onclick=reEnable  
}
</script>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
				<title>Language Services UK - LSUK LIMITED </title>
						<meta name="robots" content="index, follow" />		<link rel="canonical" href="https://www.lsuk.org" />	

<link rel="stylesheet" type="text/css" href="css1/B_blue.css" />
<link rel="stylesheet" type="text/css" href="css1/pagination.css" />

	<link href="style.css" type="text/css" rel="stylesheet" id="main-style">
    
    <link rel="stylesheet" type="text/css" href="css/revslider.css" media="screen">
    
	<link href="css/responsive.css" type="text/css" rel="stylesheet">
	<link href="css/colors/blue.css" type="text/css" rel="stylesheet" id="color-style">
    <link href="style-switcher/style-switcher.css" type="text/css" rel="stylesheet">

    <link href="images/favicon.ico" type="image/x-icon" rel="shortcut icon">
	<meta http-equiv="Content-Language" content="en" />
		<meta name="description" content="LSUK: your contact for face to face Interpreting, professional interpreter covering South West of England and South Wales" />
        <meta name="keywords" content="Interpretation","Interpreting","interpreter","Telephone","face to face","certified translation","LSUK","Translation","Translator","Language Services","Bristol",>
                            <meta property="og:title" content="Certified Translation and Professional Interpreting,"/>
					
                    <meta property="og:description" content="LSUK Limited is more than just an interpreting service provider. Our interpreter provide quality language services to help you communicate with global markets and audiences"/>
					
                    <meta property="fb:app_id" content=""/>
					
                    <meta property="og:image" content="https://www.lsuk.org/images/logo.png"/>
					<meta property="og:type" content="website"/>
					<meta property="og:url" content="https://www.lsuk.org"/>
					<meta property="og:site_name" content="lsuk.org"/>
	
    <script src="js/jquery-1.8.2.min.js" type="text/javascript"></script> 
    <script src="js/ie.js" type="text/javascript"></script>
    <script src="js/jquery.easing.1.3.js" type="text/javascript"></script> 
	<script src="js/modernizr.custom.js" type="text/javascript"></script> 
    <script src="js/ddlevelsmenu.js" type="text/javascript"></script> 
    <script type="text/javascript">
        ddlevelsmenu.setup("nav", "topbar");
    </script>
    <script src="js/tinynav.min.js" type="text/javascript"></script>
    <script src="js/jquery.validate.min.js" type="text/javascript"></script> 
    <script src="js/jquery.flexslider-min.js" type="text/javascript"></script> 
    <script src="js/jquery.jcarousel.min.js" type="text/javascript"></script> 
    <script src="js/jquery.ui.totop.min.js" type="text/javascript"></script> 
    <script src="js/jquery.fitvids.js" type="text/javascript"></script> 
    <script src="js/jquery.tweet.js" type="text/javascript"></script> 
    <script src="js/jquery.tipsy.js" type="text/javascript"></script> 
    
    <script src="js/jquery.fancybox.pack.js" type="text/javascript"></script>
    <script src="js/jquery.fancybox-media.js" type="text/javascript"></script> 
    <script src="js/froogaloop.min.js" type="text/javascript"></script>
    <script src="js/custom.js" type="text/javascript"></script>

   <script type="text/javascript">function MM_openBrWindow(theURL,winName,features) {  window.open(theURL,winName,features);}</script>
	<title>Language Services UK - LSUK LIMITED</title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>

<body class="boxed">
<div id="wrap">
<?php include'source/top_nav.php'; ?>
    <section id="page-title">
    	<div class="container clearfix">
            <h1>Contact Us</h1>
          <nav id="breadcrumbs">
                <ul>
                    <li><a href="index.php">Home</a> &rsaquo;</li>
                    <li><a href="<?php echo basename($_SERVER['HTTP_REFERER']);?>"><?php echo ucwords(basename($_SERVER['HTTP_REFERER'], '.php'));?></a> &rsaquo;</li>
                    <li><?php echo ucwords(basename($_SERVER['REQUEST_URI'], '.php'));?></li>
                </ul>
            </nav>
        </div>
    </section>
  
    <section id="content" class="container clearfix">
      
        <section>
            <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2485.0434570345074!2d-2.5377224840728347!3d51.47571662084008!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x48719029b8c3018b%3A0x88a8cb3a7106e838!2sLanguage%20Services%20UK%20Limited!5e0!3m2!1sen!2s!4v1573669146037!5m2!1sen!2s" width="950" height="450" frameborder="0" style="border:0;" allowfullscreen=""></iframe>
        </section>
        
        <section id="main" class="three-fourths">
        
        <h2>Contact Us</h2>
        <p> Language Services UK (LSUK) - Professional Interpreting and Certified Translation Services </p>
         <p>Contact us for Face to Face interpreting - Telephone interpreting and Certified Document Translation</p>
         <p>Professional Interpreter / Translator is a click away </p>
         <p>Please use the form below to drop us a line,a quick message - alternatively you can use the contact details on the right.</P> 
         <!--<p><B>Feedback</B> can be provided <a href="https://www.lsuk.org/feedback.php" class="button">here</a></p>-->
         <p><B>Book your <a href="https://www.lsuk.org/order_interpreter.php">Interpreter</a> and <a href="https://www.lsuk.org/order_translation.php">Translator</a> Online, Over the phone, Via email and Fax.</B></p>  
        <div id="contact-notification-box-success" class="notification-box notification-box-success" style="display: none;">
            <p>Your message has been successfully sent. We will get back to you as soon as possible.</p>
            <a href="#" class="notification-close notification-close-success">x</a>
        </div>
        
        <div id="contact-notification-box-error" class="notification-box notification-box-error " style="display: none;">
            <p>Your message couldn't be sent because a server error occurred. Please try again.</p>
            <a href="#" class="notification-close notification-close-error">x</a>
        </div>
<?php if(isset($_POST['submit'])){
    if(isset($_POST['g-recaptcha-response']) && !empty($_POST['g-recaptcha-response'])){
//$secret='6LextRoUAAAAAPvBF31eiYCmVP7Ne8a6mSez83zl';
$secret='6Lc7tSkcAAAAAP6Rkr4z7MZqEbtuL5tuoq7Tcgmu';
$ip=$_SERVER['REMOTE_ADDR'];
$captcha=$_POST['g-recaptcha-response'];
$rsp=file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=$secret&response=$captcha&remoteip=$ip");
$arr=json_decode($rsp,TRUE);
if($arr['success']){
    $name=$_POST['name'];
    $from_add=$_POST['email'];
    $contact=$_POST['contact'];
    $sub=$_POST['subject'];
    $company=$_POST['company'];
    $message=$_POST['message'];
	$subject = "Query From LSUK Website";

$full_message="Hi <b>Admin</b>

<br>Kindly respond to the query recieved from website.
<br>
Name: ".$name."
<br>
Email: ".$from_add."
<br>
Contact No: ".$contact."
<br>
Company: ".$company."
<br>
Subject: ".$sub."
<br>
Message: ".$message."
<br>";
$noty_message="Hi <b>".$name."</b>
<br>Thank you for your contacting us.
<br>We have just received your query and will be soon in touch with you.
<br>Thanks you.
<br>Kindest Regards,
<br>LSUK Admin Team";

try {
    $mail->SMTPDebug = 0;
    //$mail->isSMTP(); 
    //$mail->Host = 'c59754.sgvps.net';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'info@lsuk.org';
    $mail->Password   = 'LangServ786';
    $mail->SMTPSecure = 'tls';
    $mail->Port       = 587;
    $mail->Sender = 'info@lsuk.org';
    $mail->setFrom('info@lsuk.org', 'LSUK Contact Form');
    //$mail->addAddress("infolsuk786@gmail.com");
    $mail->addAddress("info@lsuk.org");
    $mail->addReplyTo($from_add,$name);
    $mail->isHTML(true);
    $mail->Subject = $subject;
    $mail->Body    = $mail->msgHtml($full_message);
    if($mail->send()){
        $mail->ClearAllRecipients();
        $mail->addAddress($from_add);
        $mail->addReplyTo('info@lsuk.org', 'LSUK');
        $mail->isHTML(true);
        $mail->Subject = 'LSUK has received you query';
        $mail->Body    = $mail->msgHtml($noty_message);
        $mail->send();
        $mail->ClearAllRecipients();
    ?>
<script>alert("Your query has been submitted. We will respond you back within 24 hours. Thank you");</script>
<?php }else{?>
<script>alert("Failed to submit your query!");</script>
<?php }
} catch (Exception $e) { ?>
<script>alert("Mailer library error!");</script>
<?php }

?>

<?php 
}else{?>
<script>alert("Captcha Validation failed. Kindly try again.");</script>
<?php }
        
    }else{?>
    <script>alert("Kindly verify your catpahca. Kindly try again.");
    window.history.back(-1);
    </script>
    <?php }
}  

?>
        <form class="content-form" method="post" action="#">
            <p>
                <label for="name">Name:</label>
                <input id="name" type="text" name="name" required>
            </p>
            
            <p>
                <label for="url">Company Name:</label>
                <input id="company" type="text" name="company" required>
            </p>
            <p>
                <label for="email">Email:</label>
                <input id="email" type="email" name="email" required>
            </p>
            <p>
                <label for="contact">Contact Number *:</label>
                <input id="contact" type="text" name="contact">
            </p>
            <p>
                <label for="subject">Subject:</label>
                <input id="subject" type="text" name="subject" required>
            </p>
            <p>
                <label for="message">Message:</label>
                <textarea id="message" cols="68" rows="8" name="message" required></textarea>
            </p> <script src='https://www.google.com/recaptcha/api.js'></script>
                        <div class="g-recaptcha" data-sitekey="6LextRoUAAAAAGSGzslurL5xeNDw3lDDVkxM9rZe"></div>
            <p>
                <input id="submit" class="button" type="submit" name="submit" value="Send Message">
            </p>
        </form>
       
        </section>
        <aside id="sidebar" class="one-fourth column-last">
            <div class="widget contact-info">
                <h3>Contact Info</h3>
                <p>You can also reach us here:</p>
                <div>
                    <p class="address"><strong>Address:</strong> Language Services UK Limited, Suite 3 Davis House, Lodge Causeway Trading Estate, Lodge Causeway, Fishponds, Bristol, BS16 3JB, UK</p>
                            <p class="phone"><strong>Phone:</strong> (0117) 3290610</p>
                            <p class="fax"><strong>Fax:</strong> 0333 800 5785</p>
                            <p class="email"><strong>Email:</strong> <a href="mailto:INFO@LSUK.ORG">INFO@LSUK.ORG</a></p>
                    <p class="business-hours"><strong>Business Hours:</strong><br>
                    Monday-Friday: 9:00-17:00<br>
                    Saturday, Sunday and Out of Hours - 07915177068
                    
                    </p>
                    <p> Language Services UK Limited is a trading name of LanguageServicesUK Limited Registered in England and Wales - 7760366</p>
                    <p> Corporate Member of Intitute of Translation and Interpreting</p>
                    <p> Member of Translation and Interpreting</p>
                </div>
            </div>
        </aside>
    </section>
	<?php include'source/footer.php'; ?>
</div>
</body>
</html>
<!--<html>
<title>Contact us</title>
<body>
<br><br><br><br><br><br>
<center>
<h2>Page is under maintenance.</h2><br>
<p>We will be right back soon.Thank you</p>
</center>
</body>
</html>-->