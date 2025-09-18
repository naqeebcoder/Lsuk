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

            <h1><?php echo htmlspecialchars(ucfirst(@$_GET['val'])); ?></h1>

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

		<?php  

		switch($_GET['val']){

		case 'strategy':	{echo '<p align="justify">Our core business strategy is to build excellent working relationships with our prestigious clients. The staff at Language Services UK limited and our linguistic experts are work alongside its users to help them achieve their business goals. Our open and supportive policy has led to long term relationships with our clients.  We understand that each client is unique in terms of their culture, needs and vision, therefore our tailor-made services help them in overcoming short and long term challenges.</p>';}break;

		

		case 'interpreting':	{echo '<p align="justify">Language Services UK interpreting service can provide a professional interpreter for various modes of face to face interpreting in the South West of England and South Wales. Our service include Simultaneous interpreting, Consecutive interpreting, Liaison interpreting and Whisper interpreting. Our talented team adds value to each client through impeccable articulation and consistency. Our foreign language interpreters have widespread international experience and a proven track record of delivering clear, clean and consistent "eye-brain-mouth" coordination. Assignments are allocated to approprietly qualified and skilled interpreters based on their knowledge, abilities, skills and experience. We can provide you Community Interpreter, Court Interpreter, Police Interpreter, Medical Interpreter and a Technical Interpreter in Bristol, Bath, Somerset, Gloucester, Swindon, Cardiff, Newport, Plymouth and Exeter.</p>';}break;

		

		case 'phone':	{echo '<p align="justify">Telephone interpreting is used for a brief and short conversation. Your professional Telephone Interpreter is only a call away.  Language Services UK can provide professional telephone interpreting services  using a three way conference calling or through a hotline to the interpreter. do not worry about your call costs , the good news are that we pay in both scenarios. we can connect you on urgent or advanced notices as required.</p>';}break;

		

		case 'translation':	{echo '<p align="justify">Language Services UK is a member of the Association of Translation Companies (ATC). Our certified and professional linguists are fully equipped with relevant skills to translate your material. Whatever the document type or volume is, we will complete each task using a professional translator , within agreed timescale at competitive rates. LSUK will ensure that we always exceed your expectations. We can provide document translation service for legal documents, court documents, academic documents, identity documents, official documetns, technical documents / material, finacial documents, websites and manuals. we can transcript audio and video clips and provide its translations in more than 200 languages. Our project manager supervise each translation project from start to end and ensure that all professional expectations are met. </p>';}break;

		

		default:{echo '<p align="justify">Record Not Found!</p>';}

		}

		

		?>

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