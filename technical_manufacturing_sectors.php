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

            <h1>Technical and Manufacturing Sectors</h1>

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

      <p> Leading manufacturing and engineering companies think globally to excel  in an increasingly competitive market. They require multilingual people with  right skills. LSUK has the market expertise to deliver this service.  We assure you that whatever the file size,  whatever the language, we can help with your&nbsp;manufacturing and engineering  translation needs and also whatever your project, we will work  hard to carefully hand pick the most&nbsp;<strong>suitable  linguist for your requirements</strong>&nbsp;and work closely with them to ensure the best result </p>

      <p>We provide Manufacturing and Engineering  translation services for a wide range of high profile organisations  in&nbsp;this sector such as manufacturing, engineering, chemical, automotive  and construction sectors is of critical importance –  we have   no room whatsoever for approximation.   We ensure that highly and suitably skilled technical interpreters are  used for face to face interpreting meetings or technical translators for  software manuals or other technical material.   Attention is paid to the detail and emphasis is on the use of correct  terminology,&nbsp;specific to the manufacturing and engineering sector. In  short we can claim that Interpreter services from LSUK Limited can help you  broaden your supplier or client base, allowing your company to target multiple  regions and markets </p>

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