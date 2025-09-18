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

            <h1>Executive Board</h1>

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

                   <img src="images/extv/ceo.png" alt="Our Company">

                </div>	

            </div>

            

            <div class="one-half column-last" align="justify">

            	<h2>CEO's Message</h2>

                <p>Imran Shah<br>

                 <u> Chief Executive Officer</u><br><br>

                  The embodiment of true entrepreneurialism, Imran Shah has lead LSUK Limited from a humble start up to become the leading language service provider in the South West of England and South Wales. His enthusiasm for business and the people who work for LSUK Limited is a key driving force behind the emergence of LSUK which is soon to be the first choice for language services.   <br><br>

                 The team at LSUK is hugely passionate about the industry and the customer experience. Our customer service executive is the voice of our clients on the board.  Our CSE ensures to supervise the service we provide and will help to find the right solution to deliver the best service.</p>

<p align="justify">&nbsp;</p>

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