<?php if(session_id() == '' || !isset($_SESSION)){session_start();} ?> 
<?php include'source/db.php'; include'source/class.php'; if(isset($_POST['submit'])){$table=@$_GET['table'];$edit_id= $acttObj->get_id('bid');}?>
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
            <h1>Face to Face </h1>
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
  <link rel="stylesheet" type="text/css" href="style_form.css"/>
		

		<div align="center" style=" color:#069; font-size:18px;"> Please Proceed your Bid for  <?php if(@$_GET['table']=='interpreter'){echo 'Face to Face'; } if(@$_GET['table']=='telephone'){echo 'Voice Over'; }if(@$_GET['table']=='translation'){echo 'Translation / Transcription'; }?></div>
			<form class="sky-form" action="#" method="post">
			 
              <fieldset> <legend style="font-size:14px; color:#069;">Post a Bid</legend>					
			    <div class="row">
						<section class="col col-6">
						  <label class="input">Full Name *
      <input name="interp" type="text" placeholder='' required='' id="interp"/>
						  </label>
		 <?php if(isset($_POST['submit'])){$data=$_POST['interp']; $acttObj->editFun('bid',$edit_id,'interp',$data);} ?>   
						</section>
                 <!-- <section class="col col-6">
                         <label class="input">Amount (per hour) &pound; *
      <input name="amount" type="text" placeholder='' required='' id="amount" pattern="[0-9]+([\.|,][0-9]+)?" step="0.01"/>
      </label>
     <?php //if(isset($_POST['submit'])){$data=$_POST['amount']; $acttObj->editFun('bid',$edit_id,'amount',$data);} ?>
                    </section>-->
			    </div>
					
					<div class="row">
						<section class="col col-6">
							<label class="input">Contact No. *
      <input name="contact" type="text" placeholder='' required='' id="contact"/>
							</label>
		<?php if(isset($_POST['submit'])){$data=$_POST['contact']; $acttObj->editFun('bid',$edit_id,'contact',$data);} ?> 
						</section>
						<section class="col col-6">
                         <label class="input">Email *
      <input name="email" type="text" placeholder='' required='' id="email"/>
      </label>
     <?php if(isset($_POST['submit'])){$data=$_POST['email']; $acttObj->editFun('bid',$edit_id,'email',$data);} ?>
                    </section>
			    
					</div>
                    <section>
						<label class="textarea">Your Location
                        <textarea name="location" cols="51" rows="5"></textarea>
    <?php if(isset($_POST['submit'])){$data=$_POST['location']; $acttObj->editFun('bid',$edit_id,'location',$data); $acttObj->editFun('bid',$edit_id,'job',$_GET['tracking']);$acttObj->editFun('bid',$edit_id,'tabName',$_GET['table']);} ?>
						</label>
					</section>
			  </fieldset>
				 
				
				<footer>
					<input type="submit" name="submit" class="button" value="Submit"/>
				</footer>
</form>

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
<?php if(isset($_POST['submit'])){echo "<script>alert('Successful!');</script>";}?>
