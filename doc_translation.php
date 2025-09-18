<?php if(session_id() == '' || !isset($_SESSION)){session_start();} ?> 
<!DOCTYPE HTML>
<!--[if IE 8]> <html class="ie8 no-js"> <![endif]-->
<!--[if (gte IE 9)|!(IE)]><!--> <html class="no-js"> <!--<![endif]-->

<!-- Mirrored from ixtendo.com/themes/exquiso-html/about-us.html by HTTrack Website Copier/3.x [XR&CO'2014], Tue, 12 Jan 2016 09:48:33 GMT -->
<head>
<link rel="stylesheet" href="http://netdna.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">
<link href="new_theme/css/bootstrap.min.css" rel="stylesheet">
<?php include'source/header.php'; ?>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
  <script src="source/jquery-2.1.3.min.js"></script> 
  <script type="text/javascript" src="new_theme/js/bootstrap.min.js"></script>
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
            <h1>Translation</h1>
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
      <p>LSUK provide  a high quality certified translation service in and around Bristol. We provide  complete professional translation services in&nbsp;more  than 200 languages for general, medical, legal and technical documents.&nbsp; Our  translation service include&nbsp;document translation, legal documents translation, medical reports translation, academic certificates translation, identity documents certified translation, technical translation of software and manuals, business documents and financial reports translation, website localization, and translation, audio transcription, video transcription, &nbsp;multilingual desktop publishing, and subtitling. <br>
        We do not  use machines to translate your material. We assign your translation projects to a skilled, certified and professional translator. <br>
        We use  modern technology to meet the standards of design and layout of the actual  document. Estimates  for translation projects for all of our language solutions are always free.&nbsp;Please use the form below to book your  translation project. </p>
      <div>
        <div> <br> <strong>Testimonials:</strong>
       <br>
  
  <br> "We really enjoyed working with LSUK Limited. Not only did the translator complete the project ahead of schedule, but their team was very dedicated to the quality of their translations making sure they we're appropriate for the culture of our target market."</br>
  
  <br>"Many thanks - I was very pleased with the service, efficient and prompt turnaround.We were delighted with the quality of service - it was fast, efficient and accurate."</br>
<br>"We are very happy with the results of the translations.  The setup for each section was perfect and it made implementing the translations much easier for us."</br></div>
      </div>
<p><br/>
      <br/>
      <a href="<?php if(isset($_SESSION['cust_UserName'])){echo 'customer_area_slct_comp.php?interp=order_translation_prem.php';}else{echo 'order_translation.php';} ?>" class="button">Order for Document Translation</a>
    </p>
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