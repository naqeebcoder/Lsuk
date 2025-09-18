<?php exit;session_start(); include 'source/db.php';include 'source/class.php';include_once ('source/function.php');$blog_id=(int) @$_GET["flag"];

    	$page = (int) (!isset($_GET["page"]) ? 1 : $_GET["page"]);
	
    	$startpoint = ($page * $limit) - $limit;	?>

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

            <h1>Blog</h1>

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

    	<!-- begin main content -->

        <section id="main" class="blog-entry-list three-fourths">

        

                             <tbody>

 <?php  
$safe_blog_id = mysqli_real_escape_string($con, $blog_id);
$query="SELECT * FROM blog where id=$safe_blog_id ";	



			$result = mysqli_query($con,$query);

			while($row = mysqli_fetch_array($result)){ $id=$row['id']; $dated=$row['dated']; $month = date("m",strtotime($dated));$day = date("d",strtotime($dated));?>

            

              <article class="entry clearfix">

            	<!--<a class="entry-image link-overlay" href="blog-post-image.html" title="Post Title"><span class="overlay"></span><img src="images/entries/700x240/modern-skyscraper-700x240.png" alt=""></a>-->

            	<div class="entry-date">

                    <div class="entry-day"><?php echo $day; ?></div>

                    <div class="entry-month"><?php  $month=date("F", mktime(0, 0, 0, $month, 10)); echo substr($month,0,3); ?></div>   

                </div>

                <div class="entry-body">

                    <h2 class="entry-title"><a href="#"><?php echo $row['title']; ?></a></h2>

                    <div class="entry-meta">

                    	<span class="author"><a href="#"><?php echo $row['name']; ?></a></span> 

                        <span class="category"><a href="post_blog.php?flag=<?php echo $id; ?>">Reply</a></span>

                    </div>

                    <div class="entry-content">

                        <p><?php echo $row['comments']; ?> &hellip;</p>

                    </div>

                </div>

            </article>

            

				

			<?php 
$safe_id = mysqli_real_escape_string($con, $id);
$safe_startpoint = mysqli_real_escape_string($con, $startpoint);
$safe_limit = mysqli_real_escape_string($con, $limit);
$table='blog_rep';
$query_rep="SELECT * FROM  blog_rep where rep_id=$safe_id LIMIT {$safe_startpoint} , {$safe_limit}";	



			$result_rep = mysqli_query($con,$query_rep);

			while($row_rep = mysqli_fetch_array($result_rep)){

				

				 $dated=$row_rep['dated']; $month = date("m",strtotime($dated));$day = date("d",strtotime($dated));?>    	

            

            <article class="entry clearfix">

            	<!--<a class="entry-image link-overlay" href="blog-post-image.html" title="Post Title"><span class="overlay"></span><img src="images/entries/700x240/modern-skyscraper-700x240.png" alt=""></a>-->

            	<div class="entry-date">

                    <div class="entry-day"><?php echo $day; ?></div>

                    <div class="entry-month"><?php  $month=date("F", mktime(0, 0, 0, $month, 10)); echo substr($month,0,3); ?></div>   

                </div>

                <div class="entry-body">

                    <h2 class="entry-title" style="font-size:14px;"><a href="#"><?php echo $row_rep['title']; ?></a></h2>

                    <div class="entry-meta">

                    	<span class="author"><a href="#"><?php echo $row_rep['name']; ?></a></span> <?php if(@$_SESSION['email']=="imran@lsuk.org" || $_SESSION['interp_code']=='id-13'){ ?><span class="comments"><a href="del.php?del_id=<?php echo $row_rep['id']; ?>&table=<?php echo $table; ?>&url=<?php echo basename($_SERVER['REQUEST_URI']); ?>"> Delete</a></span><?php } ?>

                    </div>

                    <div class="entry-content">

                        <p><?php echo $row_rep['comments']; ?> &hellip;</p>

                    </div> <div class="entry-content">

                        <p><?php echo $row_rep['comments']; ?> &hellip;</p>

                    </div>

                </div>

            </article>

            

            <?php }} ?>

            

            <?php echo pagination($con,$table,$query_rep,$limit,$page);?>

              

            <div class="clear"></div>

            <a href="post_blog.php">Post Blog..........</a>

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