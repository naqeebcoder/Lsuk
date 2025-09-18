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

            <h1>Public Sector</h1>

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

      <p>The provision of an accurate and trustworthy language service  is essential in order for public sector organisations to be effective in their  work. We provide professional and efficient interpreting and translation  service to Her Majesty Court Services, Police, Emergency Services and Local Government  organisations. Our services include Face to Face Interpreting, Telephone  Interpreting, Audio and Video Transcription and Documents translations. We  ensure that a qualified interpreter or certified translator is available to  deliver the required service. Rest assured our&nbsp;freelance  interpreters&nbsp;are qualified, fully vetted and categorised according to  their ability and experience.</p>

      <p> </p>

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