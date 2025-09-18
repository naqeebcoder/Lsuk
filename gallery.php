<?php if(session_id() == '' || !isset($_SESSION)){session_start();} ?> 
<!DOCTYPE HTML>
<!--[if IE 8]> <html class="ie8 no-js"> <![endif]-->
<!--[if (gte IE 9)|!(IE)]><!--> <html class="no-js"> <!--<![endif]-->

<!-- Mirrored from ixtendo.com/themes/exquiso-html/about-us.html by HTTrack Website Copier/3.x [XR&CO'2014], Tue, 12 Jan 2016 09:48:33 GMT -->
<head>
<?php include'source/header.php'; ?>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link href="//maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
<script src="//maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<!------ Include the above in your HEAD tag ---------->

 <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/fancybox/2.1.5/jquery.fancybox.min.css" media="screen">
<script src="//cdnjs.cloudflare.com/ajax/libs/fancybox/2.1.5/jquery.fancybox.min.js"></script>
<style>
#main {
	position:relative;
	width:100%;
	max-width:1200px;
	right:0;
	left:0;
	margin:0 auto auto auto;
	background:white;
	min-height:100%;
	box-shadow: 0 2px 2px 0 rgba(0,0,0,.1);
}

    #pageNav {
	position:relative;
}

#pageNav button {
	color:#fff;
	background:#333;
	padding:8px 12px;
	margin:5px 5px 5px 0;
	border:0;
	font-size:1em;
	cursor:pointer;
	text-decoration:none;
}

.active {
	text-decoration:underline!important;
}

#gallery {
	position:relative;
	margin:40px 0;
}

.img-container {
	padding:1%;
	float:left;
	left:0;
}
.img img {
	width:195%;
	height:auto;
	transform: translate(-25%,-7%) ;
	-moz-transform: translate(-25%,-7%) ;
	-webkit-transform: translate(-25%,-7%) ;
	-ms-transform: translate(-25%,-7%);
}

/*.clearfix {*/
/*	clear:both;*/
/*}*/
.ct a:link,
.ct a:active,
.ct a:hover,
.ct a:visited {
	color:#444;
	text-decoration:none;
}

.ad {
	margin:80px auto 80px 0;
	position:relative;
	width:auto;
	max-width:748px;
}

.ad img {
	width:100%;
	max-width:748px;
}

@media only screen and (max-width: 360px) {
.img-container {
	width:98%;
	padding:2% 1%;
}	
}

@media only screen and (min-width: 360px)and (max-width: 900px) {
.img-container {
	width:48%;
	padding:1%;
}	
}
#demo {
  height:100%;
  position:relative;
  overflow:hidden;
}


.green{
  background-color:#6fb936;
}
        .thumb{
            margin-bottom: 15px;
        }
        
        .page-top{
            margin-top:85px;
        }

   
img.zoom {
    width: 100%;
    height: 200px;
    border-radius:5px;
    object-fit:cover;
    -webkit-transition: all .3s ease-in-out;
    -moz-transition: all .3s ease-in-out;
    -o-transition: all .3s ease-in-out;
    -ms-transition: all .3s ease-in-out;
}
        
 
.transition {
    -webkit-transform: scale(1.2); 
    -moz-transform: scale(1.2);
    -o-transform: scale(1.2);
    transform: scale(1.2);
}
    .modal-header {
   
     border-bottom: none;
}
    .modal-title {
        color:#000;
    }
    .modal-footer{
      display:none;  
    }
    .menu li {
    font-size: 12px;
    }
</style>
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
            <h1>LSUK Gallery</h1>
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
        <section>
        	<div id="pagination"><!-- #pagination start -->

<?php
include_once('source/db.php');//Include the database connection

//////FIRST WE SET UP THE TOTAL images PER PAGE & CALCULATIONS:
$per_page = 12;// Number of images per page, change for a different number of images per page

// Get the page and offset value:
if (isset($_GET['page'])) {
$page = $_GET['page'] - 1;
$offset = $page * $per_page;
}
else {
$page = 0;
$offset = 0;
} 

// Count the total number of images in the table ordering by their id's ascending:
$images = "SELECT count(id) FROM images where status='active' ORDER by id ASC";
$result = mysqli_query($con, $images);
$row = mysqli_fetch_array($result);
$total_images = $row[0];

// Calculate the number of pages:
if ($total_images > $per_page) {//If there is more than one page
$pages_total = ceil($total_images / $per_page);
$page_up = $page + 2;
$page_down = $page;
$display ='';//leave the display variable empty so it doesn't hide anything
} 
else {//Else if there is only one page
$pages = 1;
$pages_total = 1;
$display = ' class="display-none"';//class to hide page count and buttons if only one page
} 
echo '<div id="">';// Gallery start

// DISPLAY THE images:
//Select the images from the table limited as per our $offet and $per_page total:
$result = mysqli_query($con, "SELECT * FROM images where status='active' ORDER by id ASC LIMIT $offset, $per_page");
while($row = mysqli_fetch_array($result)) {//Open the while array loop

//Define the image variable:
$title=$row['title'];
$image=$row['file'];

echo '<div class="col-md-3 col-xs-6 thumb" style="float: left;">';

echo '<a href="gallery/'.$image.'"  class="fancybox" rel="ligthbox">';
echo '<img title="'.$title.'" src="gallery/'.$image.'" class="zoom img-fluid "  alt="'.$title.'">';
echo '</a>';

echo '</div>';// .img-container end
}//Close the while array loop

echo '</div>';// Gallery end

echo '<div class="clearfix"></div>';// Gallery end
////// THEN WE DISPLAY THE PAGE COUNT AND BUTTONS:

echo '<br><h3 style="font-size: 1rem;" '.$display.'>Page '; echo $page + 1 .' of '.$pages_total.'</h3>';//Page out of total pages

$i = 1;//Set the $i counting variable to 1

echo '<div id="pageNav"'.$display.'>';//our $display variable will do nothing if more than one page

// Show the page buttons:
if ($page) {
echo '<a href="gallery.php"><button><<</button></a>';//Button for first page [<<]
echo '<a href="gallery.php?page='.$page_down.'"><button><</button></a>';//Button for previous page [<]
} 

for ($i=1;$i<=$pages_total;$i++) {
if(($i==$page+1)) {
echo '<a href="gallery.php?page='.$i.'"><button class="active">'.$i.'</button></a>';//Button for active page, underlined using 'active' class
}

//In this next if statement, calculate how many buttons you'd like to show. You can remove to show only the active button and first, prev, next and last buttons:
if(($i!=$page+1)&&($i<=$page+3)&&($i>=$page-1)) {//This is set for two below and two above the current page
echo '<a href="gallery.php?page='.$i.'"><button>'.$i.'</button></a>'; }
} 

if (($page + 1) != $pages_total) {
echo '<a href="gallery.php?page='.$page_up.'"><button>></button></a>';//Button for next page [>]
echo '<a href="gallery.php?page='.$pages_total.'"><button>>></button></a>';//Button for last page [>>]
}
echo "</div>";// #pageNav end



?>



</div>
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
<script src="js/jquery-1.8.2.min.js"></script>
<script type="text/javascript">
	$(document).ready(function(){
  $(".fancybox").fancybox({
        openEffect: "none",
        closeEffect: "none"
    });
    
    $(".zoom").hover(function(){
		
		$(this).addClass('transition');
	}, function(){
        
		$(this).removeClass('transition');
	});
});
    
</script>
</html>