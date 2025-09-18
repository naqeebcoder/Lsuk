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

            <h1>Corporate Sector</h1>

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

      <p>In an increasingly globalised and competitive business environment, the importance of promoting your business with top quality translations has never been greater. LSUK takes pride in providing its corporate clients with an accurate, efficient, business translation service. We have been working closely with businesses and providing them the service as and when they need.  We supply interpreters who not only masters of their languages but are also experts in the countries where their languages are spoken, they possess appropriate industry skills to meet the requirement of our clients. Our linguists assist businesses through professional interpreting at their vital business meeting with international delegations or by providing written translation service for important financial documents that are required to reach out the consumers and branches in the new markets.</p>

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