<?php if(session_id() == '' || !isset($_SESSION)){session_start();} ?> 
<!DOCTYPE HTML>
<!--[if IE 8]> <html class="ie8 no-js"> <![endif]-->
<!--[if (gte IE 9)|!(IE)]><!--> <html class="no-js"> <!--<![endif]-->

<!-- Mirrored from ixtendo.com/themes/exquiso-html/about-us.html by HTTrack Website Copier/3.x [XR&CO'2014], Tue, 12 Jan 2016 09:48:33 GMT -->
<head>
<link href="new_theme/css/bootstrap.min.css" rel="stylesheet">
<script src="source/jquery-2.1.3.min.js"></script> 
<script type="text/javascript" src="new_theme/js/bootstrap.min.js"></script>
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
            <h1>Quality Assurance</h1>
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
      <p>Language Services UK limited is quality  focused. Our interpreting and document translation service is subject to  systematic monitoring for quality assurance to ensure it remains relevant to  the needs of our clients. Information governance and data protection according  to industry laws are the significant feature of our  policy. <br>
        Our quality assurance procedures are in place  to ensure that your work is accomplished unmistakably in the target language  and delivered within its budgets and timescales.<br>
  <strong>Our Interpreters</strong><br>
        Our Interpreters follow the Code of  Conduct for Communication Professionalism, which covers the following: <br>
  <strong>Impartiality:</strong> <br>
        The interpreter  will give equal attention to the service user and service provider. The interpreter  shall be impartial and unbiased. The interpreter is a neutral third party  language conduit and has no role in the decisions taken by the service provider  or client.</p>
      <p><strong>Confidentiality:</strong> <br>
        Anything said in the room is  not to be repeated by the interpreter to any external party. Translators and interpreters  will adhere to data protection regulations and store notes safely on a computer  or on paper to ensure confidentiality. All private information and notes are  deleted after receiving payment and satisfaction from service provider and  client. Language Services UK limited senior management may  ask the information to be disclosed if agreed between the client and senior  manager or if it is felt necessary.<strong> </strong><br>
  <strong>Professional competence:</strong><br>
        The dialogue  or text will be interpreted or translated word for word. Nothing will be added,  deleted or amended. Everything that is said will be interpreted including  conversations between service providers and conversations between service  users. The interpreters and translators will ask for permission from the chair  of the meeting to clarify any issue or misunderstanding where needed. The  interpreter will not offer advice. <br>
  <strong>Our Translation Projects</strong><br>
        To ensure our quality assurance we are  sure to only use professional, highly skilled and appropriate translators.<br>
        Track and manage assignments through a custom  designed CRM<br>
        Use  modern software / technology where needed <br>
        Assign  projects managers to translation projects to ensure that formatting and editing  are as required </p>
      <div>
        <div> </div>
      </div>
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

</html>