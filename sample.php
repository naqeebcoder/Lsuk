<?php if(session_id() == '' || !isset($_SESSION)){session_start();} ?> 
<!DOCTYPE HTML>
<!--[if IE 8]> <html class="ie8 no-js"> <![endif]-->
<!--[if (gte IE 9)|!(IE)]><!--> <html class="no-js"> <!--<![endif]-->

<!-- Mirrored from ixtendo.com/themes/exquiso-html/about-us.html by HTTrack Website Copier/3.x [XR&CO'2014], Tue, 12 Jan 2016 09:48:33 GMT -->
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
            <h1>About Us</h1>
          <nav id="breadcrumbs">
                <ul>
                    <li><a href="index.php">Home</a> &rsaquo;</li>
                    <li><a href="about_us.php">Message</a> &rsaquo;</li>
                    <li><?php echo ucwords(basename($_SERVER['REQUEST_URI'], '.php'));?></li>
                </ul>
            </nav>
        </div>
    </section>
    <!-- begin page title -->
    
    <!-- begin content -->
  
    <!-- end content -->  
    
    <!-- begin footer -->
	<?php include'source/footer.php'; ?>
	<!-- end footer -->  
</div>
<!-- end container -->
</body>

<!-- Mirrored from ixtendo.com/themes/exquiso-html/about-us.html by HTTrack Website Copier/3.x [XR&CO'2014], Tue, 12 Jan 2016 09:49:13 GMT -->
</html>