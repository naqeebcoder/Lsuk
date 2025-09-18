<?php if(session_id() == '' || !isset($_SESSION)){session_start();} if(empty($_SESSION['cust_UserName'])){echo '<script type="text/javascript">' . "\n";echo 'window.location="index.php";'; echo '</script>';}?>  
<?php include'source/db.php'; include'source/class.php'; if(isset($_POST['submit'])){$table='staff_job_applicants';$edit_id= $acttObj->get_id($table);}$prvlg = @$_GET['prvlg'];?>
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
            <h1>Set Privileges  </h1>
          <nav id="breadcrumbs">
               <ul>
                    <li><a href="index.php">Home</a> &rsaquo;</li>
                   
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
		

		<div align="center" style=" color:#069; font-size:18px;"> Are you sure you want to <span style="color:#F00; font-weight:bold"><?php if($prvlg==1){echo 'Block';}else{echo 'Un-Block';} ?></span> this user   <?php echo @$_GET['title']; ?></div>
			<form class="sky-form" action="#" method="post">
			 
              				 
				
				<footer>
		<input type="submit" name="yes" value="Yes"  class="button" />&nbsp;&nbsp;<input type="submit" name="no" value="No" onclick="goBack()"  class="button" />
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
<?php if(isset($_POST['yes'])){
 $edit_id = @$_GET['edit_id'];$table = @$_GET['table'];$url = @$_GET['url'];
 
 include 'source/db.php';$acttObj->editFun($table,$edit_id,'prvlg',$prvlg);
 
 echo "<script>window.location.assign('cust_reg_list.php')</script>";}
 if(isset($_POST['no'])){ ?>
 <script>function goBack() {window.history.back();}</script>
 <?php } ?>
