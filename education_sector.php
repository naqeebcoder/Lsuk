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

            <h1>Education Sector</h1>

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

      <p> Face to face Interpreting and  translation of important material is highly required at educational settings.  Language barriers make it more difficult to assess the level of special help a  child needs. An inability to communicate may not help a child who is already  struggling at school. Equally, it is vital that parents are able to understand  assessment of their child’s special needs and that they are able to contest any  Local Education Authority decisions.  It  helps the children or their parent with limited understanding of English to  understand the system and to stay aware of the progress. </p>

      <p>LSUK maintain close ties with  education sector and works closely with a wide  range of clients from within the education and training sector these include  schools, academies, colleges, universities EFL schools and educational  publishers. Our interpreter or translator will help you deliver the education  services. We provide educational settings with a wide range of translation and  interpreting services. </p>

As a translation agency, we pride ourselves on our robust understanding  of the training and education sector. Our translators are all qualified  professionals with experience translating specific sector terminology, and can  hit the ground running when it comes to higher education or specialist  training. As well as appreciating the nuances of the sector, our linguists are  trained in working with businesses to help meet objectives and are experienced  in interpreting different forms to make sure you get the maximum results for  your translation. </section>

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