<?php if(session_id() == '' || !isset($_SESSION)){session_start();} ?> 
<?php include'source/db.php'; include'source/class.php'; if(isset($_POST['submit'])){$table='staff_job';$edit_id= $acttObj->get_id($table);}?>
<!DOCTYPE HTML>
<!--[if IE 8]> <html class="ie8 no-js"> <![endif]-->
<!--[if (gte IE 9)|!(IE)]><!--> <html class="no-js"> <!--<![endif]-->

<!-- Mirrored from ixtendo.com/themes/exquiso-html/about-us.html by HTTrack Website Copier/3.x [XR&CO'2014], Tue, 12 Jan 2016 09:48:33 GMT -->
<head>
<?php include'source/header.php'; ?>
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
            <h1>Staf Job </h1>
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
		

		<div align="center" style=" color:#069; font-size:18px;">Job Posting</div>
			<form class="sky-form" action="#" method="post">
			 
              <fieldset> <legend style="font-size:14px; color:#069;">Assignment Location </legend>					
			    <div class="row">
						<section class="col col-6">
						  <label class="input">Job Title *
      <input name="title" type="text" placeholder='' required='' id="title"/>
						  </label>
				<?php if(isset($_POST['submit'])){$data=$_POST['title']; $acttObj->editFun($table,$edit_id,'title',$data);} ?> 
						</section>
                  <section class="col col-6">
                         <label class="input">Type of Job * 
      <input name="type" type="text" placeholder='' required='' id="type"/>
      </label>
           <?php if(isset($_POST['submit'])){$data=$_POST['type']; $acttObj->editFun($table,$edit_id,'type',$data);} ?>
                    </section>
			    </div>
					
					<div class="row">
						<section class="col col-6">
							<label class="input">Last Date *
      <input name="last_date" type="date" placeholder='' required='' id="last_date"/>
							</label>
		 <?php if(isset($_POST['submit'])){$data=$_POST['last_date']; $acttObj->editFun($table,$edit_id,'last_date',$data);} ?>
						</section>
						<section class="col col-6">
                         <label class="input">Location *
      <input name="location" type="text" placeholder='' required='' id="location"/>
     <?php if(isset($_POST['submit'])){$data=$_POST['location']; $acttObj->editFun($table,$edit_id,'location',$data);} ?>
      </label>
                    </section>
			    
					</div>
                    <section>
						<label class="textarea">Skills
                        <textarea name="skill" cols="51" rows="5"></textarea>
      <?php if(isset($_POST['submit'])){$data=$_POST['skill']; $acttObj->editFun($table,$edit_id,'skill',$data);} ?>
						</label>
					</section>
                    <section>
						<label class="textarea">Job Description 
                        <textarea name="descrp" cols="51" rows="5"></textarea>
     <?php if(isset($_POST['submit'])){$data=$_POST['descrp']; $acttObj->editFun($table,$edit_id,'descrp',$data);} ?>
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
