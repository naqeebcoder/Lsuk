<?php if(session_id() == '' || !isset($_SESSION)){session_start();} ?> 
<?php include'source/db.php'; include'source/class.php';$update_id= $_GET['update_id']; $table=@$_GET['table'];?>
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
            <h1>Face to Face Job for Approval</h1>
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
		

		<div align="center" style=" color:#069; font-size:18px;"> Approve the job</div><br />
        <div align="center"><img src="file_folder/<?php if($table=='interpreter'){echo 'time_sheet_interp/'.$acttObj->unique_data($table,'time_sheet','id',$update_id);}if($table=='telephone'){echo 'time_sheet_telep/'.$acttObj->unique_data($table,'time_sheet','id',$update_id);}if($table=='translation'){echo 'time_sheet_trans/'.$acttObj->unique_data($table,'time_sheet','id',$update_id);}?>" alt="No Time Sheet Uploaded"/></div><br />
        
			<form class="sky-form" action="#" method="post">
			 
              <fieldset> <legend style="font-size:14px; color:#069;">Are you sure to approve this job (<?php echo $update_id; ?>)*</legend>					
			    <div class="row">
						   <section class="col col-6">
                         <label class="select">
						
						  <select name="aprove_flag" id="aprove_flag" required=''>
						   
						    <option>--Select--</option>
						    <option value="1">Yes</option>
						    <option value="0">No</option>
						   
					      </select></label>
<?php if(isset($_POST['submit'])){$data=$_POST['aprove_flag']; $acttObj->editFun($table,$update_id,'aprove_flag',$data);} ?>
                        </section>
                 <footer>
					<input type="submit" name="submit" class="button" value="Submit"/>
				</footer>
			    </div>

			  </fieldset>
				 
				
				
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
