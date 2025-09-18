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

            <h1>Legal Sector</h1>

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

      <p>Global  commerce is on the rise, the need for multilingual contracts, forms, tenders  and other legal documents is rapidly growing.&nbsp;Legal translation&nbsp;is  absolutely mission-critical. We build quality  checks into every major point in the legal translation process to ensure that  the translations we deliver to you are as accurate as you need them to be. Numerous  companies require legal documents to be translated from one language to  another. However,&nbsp;translating legal documents&nbsp;is a very complex task thus these  needs to be accomplished by the right people as minor errors can lead to major  issues.</p>

      <p>Legal system is  heavily regulated and that clear, concise communication with the client is  paramount as well as delivering work quickly and professionally is priority  number one.&nbsp;The legal sector is a vast sector that could require legal  interpreters or legal document translation, but whatever you are looking for we  can assure you that we have got the experience.  <br>

        LSUK core area of specialisation is  Legal Sector. We have been providing translation and interpreting service to a  large number of law firms across south west of England and South Wales for many  years now. We are well aware of the importance of legal meeting and  unprofessionalism can have severe impacts on our clients and the clients of our  clients.  All our operational process is  setup to carefully select a linguist and assigning the job to provide a  consistent high quality service. </p>

We  have been providing appropriately qualified interpreting services to Her  Majesty Court Services and country’s top police forces. Our interpreters are  given trainings to fully understand all relevant sensitivities&nbsp;and legal  concepts to ensure provision of a quality assured linguistic service across the  legal sector.

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