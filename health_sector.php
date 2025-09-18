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

            <h1>Health Sector</h1>

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

      <p> Health care interpreters facilitate communication between  patients with limited English proficiency and their physicians, nurses, lab  technicians and other healthcare providers.&nbsp;  Using interpreters over the phone or for face to face medical  appointment is vital. Interpreters can help reduce inequalities in healthcare  access and quality of care between native and non-native speaking patients.  Professional and skilled interpreters help in improving clinical outcomes, use  of interpreters is feasible given the positive clinical outcomes. </p>

      <p>

LSUK  has been working with major organisations in the health sector to provide  medical interpreting and translation service. Our health care interpreters work in a variety of health care settings, including  hospitals, health surgeries, health clinics, private offices, counselling and  or therapy centres, rehabilitation centres and nursing homes across South west  of England and South Wales. Accuracy and confidentiality is never  compromised at LSUK Limited. Our interpreters know that medical interpreting or  health assessments require immense concentration and focus and are trained to  work in intense emotional environments where required. </p>   </section>

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