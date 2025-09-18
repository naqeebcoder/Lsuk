<?php session_start(); include 'source/db.php';include 'source/class.php';include_once ('source/function.php');
    	$page = (int) (!isset($_GET["page"]) ? 1 : $_GET["page"]);
    	$limit = 10;
    	$startpoint = ($page * $limit) - $limit;	?>
<!DOCTYPE HTML>
<!--[if IE 8]> <html class="ie8 no-js"> <![endif]-->
<!--[if (gte IE 9)|!(IE)]><!--> <html class="no-js"> <!--<![endif]-->

<!-- Mirrored from ixtendo.com/themes/exquiso-html/about-us.html by HTTrack Website Copier/3.x [XR&CO'2014], Tue, 12 Jan 2016 09:48:33 GMT -->
<head>
<meta name="google-site-verification" content="FD3pfiOXrr6D1lGvNWqseAJrL1PMPj1nguqXAd5mFkY" />
<meta name="google-site-verification" content="FD3pfiOXrr6D1lGvNWqseAJrL1PMPj1nguqXAd5mFkY" />


<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="https://netdna.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">
<script src="prefixfree.min.js"></script>
<script src="source/modernizr.min.js"></script>

<script src="source/jquery-2.1.3.min.js"></script> 
<script>
$(document).ready(function(){ 
	var touch 	= $('#resp-menu');
	var menu 	= $('.menu');
 
	$(touch).on('click', function(e) {
		e.preventDefault();
		menu.slideToggle();
	});
	
	$(window).resize(function(){
		var w = $(window).width();
		if(w > 767 && menu.is(':hidden')) {
			menu.removeAttr('style');
		}
	});
	
});
</script>
<script type="text/javascript">

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-36251023-1']);
  _gaq.push(['_setDomainName', 'jqueryscript.net']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script>
<script type="text/javascript"> 
function disableselect(e){  
return false  
}  

function reEnable(){  
return true  
}  

//if IE4+  
document.onselectstart=new Function ("return false")  
document.oncontextmenu=new Function ("return false")  
//if NS6  
if (window.sidebar){  
document.onmousedown=disableselect  
document.onclick=reEnable  
}
</script>	<!-- begin meta -->
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
				<title>LSUK - Blog Page</title>
						<meta name="robots" content="index, follow" />		<link rel="canonical" href="https://www.lsuk.org" />	
	<!-- end meta -->
	
<link rel="stylesheet" type="text/css" href="css1/B_blue.css" />
<link rel="stylesheet" type="text/css" href="css1/pagination.css" />
	<!-- begin CSS -->
	<link href="style.css" type="text/css" rel="stylesheet" id="main-style">
    
    <!-- REVOLUTION BANNER CSS SETTINGS -->
    <link rel="stylesheet" type="text/css" href="css/revslider.css" media="screen">
    
	<link href="css/responsive.css" type="text/css" rel="stylesheet">
	<!--[if IE]> <link href="css/ie.css" type="text/css" rel="stylesheet"> <![endif]-->
	<link href="css/colors/blue.css" type="text/css" rel="stylesheet" id="color-style">
    <link href="style-switcher/style-switcher.css" type="text/css" rel="stylesheet">
	<!-- end CSS -->
    
    <link href="images/favicon.ico" type="image/x-icon" rel="shortcut icon">
	<meta http-equiv="Content-Language" content="en" />
		<meta name="description" content="LSUK:Blogs" />
        <meta name="keywords" content="blog","Interpretation","interpreting","interpreter","Translator","Translation","Certified Translation","telephone interpreting">
							
                            <meta property="og:title" content="Language Services UK - blog page"/>
					
                    <meta property="og:description" content="language services to help you communicate with global markets and audiences"/>
					
                    <meta property="fb:app_id" content=""/>
					
                    <meta property="og:image" content="http://www.lsuk.org/images/logo.png"/>
					<meta property="og:type" content="website"/>
					<meta property="og:url" content="http://www.lsuk.org"/>
					<meta property="og:site_name" content="lsuk.org"/>
	<!-- begin JS -->
    <script src="js/jquery-1.8.2.min.js" type="text/javascript"></script> <!-- jQuery -->
    <script src="js/ie.js" type="text/javascript"></script> <!-- IE detection -->
    <script src="js/jquery.easing.1.3.js" type="text/javascript"></script> <!-- jQuery easing -->
	<script src="js/modernizr.custom.js" type="text/javascript"></script> <!-- Modernizr -->
    <!--[if IE 8]>
    <script src="js/respond.min.js" type="text/javascript"></script> 
    <script src="js/selectivizr-min.js" type="text/javascript"></script> 
    <![endif]--> 
   <!--  <script src="style-switcher/style-switcher.js" type="text/javascript"></script> style switcher -->
    <script src="js/ddlevelsmenu.js" type="text/javascript"></script> <!-- drop-down menu -->
    <script type="text/javascript"> <!-- drop-down menu -->
        ddlevelsmenu.setup("nav", "topbar");
    </script>
    <script src="js/tinynav.min.js" type="text/javascript"></script> <!-- tiny nav -->
    <script src="js/jquery.validate.min.js" type="text/javascript"></script> <!-- form validation -->
    <script src="js/jquery.flexslider-min.js" type="text/javascript"></script> <!-- slider -->
    <script src="js/jquery.jcarousel.min.js" type="text/javascript"></script> <!-- carousel -->
    <script src="js/jquery.ui.totop.min.js" type="text/javascript"></script> <!-- scroll to top -->
    <script src="js/jquery.fitvids.js" type="text/javascript"></script> <!-- responsive video embeds -->
    <script src="js/jquery.tweet.js" type="text/javascript"></script> <!-- Twitter widget -->
    <script src="js/jquery.tipsy.js" type="text/javascript"></script> <!-- tooltips -->
    <!-- jQuery REVOLUTION Slider  -->
	<script type="text/javascript" src="js/revslider.jquery.themepunch.plugins.min.js"></script> <!-- swipe gestures -->
    <script type="text/javascript" src="js/revslider.jquery.themepunch.revolution.js"></script>
    <!-- REVOLUTION BANNER CSS SETTINGS -->
    <script src="js/jquery.fancybox.pack.js" type="text/javascript"></script> <!-- lightbox -->
    <script src="js/jquery.fancybox-media.js" type="text/javascript"></script> <!-- lightbox -->
    <script src="js/froogaloop.min.js" type="text/javascript"></script> <!-- video manipulation -->
    <script src="js/custom.js" type="text/javascript"></script> <!-- jQuery initialization -->
    <script src="http://maps.googleapis.com/maps/api/js?sensor=false" type="text/javascript"></script> <!-- Google maps -->
    <script src="js/jquery.gmap.min.js" type="text/javascript"></script> <!-- gMap -->
    <!-- end JS -->
	
   <script type="text/javascript">function MM_openBrWindow(theURL,winName,features) {  window.open(theURL,winName,features);}</script>
	<title>LSUK - Blogs Page</title>
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
        
                             
 <?php $table='blog'; $query="SELECT * FROM $table LIMIT {$startpoint} , {$limit}";	

			$result = mysqli_query($con,$query);
			while($row = mysqli_fetch_array($result)){ $id=$row['id'];
				
			$query_rep="SELECT count(*) as rep FROM  blog_rep where rep_id=$id ";	

			$result_rep = mysqli_query($con,$query_rep);
			while($row_rep = mysqli_fetch_array($result_rep)){$rep=$row_rep['rep'];}
				
				 $dated=$row['dated']; $month = date("m",strtotime($dated));$day = date("d",strtotime($dated));?>    	
            
            <article class="entry clearfix">
            	<!--<a class="entry-image link-overlay" href="blog-post-image.html" title="Post Title"><span class="overlay"></span><img src="images/entries/700x240/modern-skyscraper-700x240.png" alt=""></a>-->
            	<div class="entry-date">
                    <div class="entry-day"><?php echo $day; ?></div>
                    <div class="entry-month"><?php  $month=date("F", mktime(0, 0, 0, $month, 10)); echo substr($month,0,3); ?></div>   
                </div>
                <div class="entry-body">
                    <class="entry-title"><a href="#"><?php echo $row['title']; ?> <span class="author"><a href="#"><?php echo $row['name']; ?></a>
                    <div class="entry-content">
                        <p><?php echo $row['comments']; ?> &hellip;</p>
                    </div>
                </div>
            </article>
            
            <?php } ?>
            
            <?php echo pagination($con,$table,$query,$limit,$page);?>
              
            <div class="clear"></div>
            <div class="clear"></div>
            
        </section>
        <hr>
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