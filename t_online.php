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
            <h1>Book On Line</h1>
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
        <section>
        	<div class="one-half">
                <div id="slider-about-us" class="about-us entry-slider">
                    <div id="flexslider-about-us" class="flex-container">
                        <div class="flexslider">
                            <ul class="slides">
                                <li>
                                    <img src="images/entries/full-size/business-woman.jpg" alt="Our Company" title="Pix title">
                                </li>
                                <li>
                                    <img src="images/entries/full-size/business-development.jpg" alt="Our Team" title="Pix title">
                                </li>
                                <li>
                                    <iframe src="http://player.vimeo.com/video/34750078?title=0&amp;byline=0&amp;portrait=0&amp;badge=0&amp;color=ffffff&amp;api=1" width="448" height="252" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>
                                </li>    
                            </ul>
                        </div>
                    </div>
                </div>	
            </div>
            
            <div class="one-half column-last">
            	<h2>Translatio Booking On Line</h2>
                <p align="justify"> Some Text or Detail.</p>
            </div>
            <div class="clear"></div>
        </section>
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