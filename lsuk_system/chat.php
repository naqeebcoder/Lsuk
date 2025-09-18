<?php if(session_id() == '' || !isset($_SESSION)){session_start();} ?> 
<?php include 'db.php';include_once ('function.php');
    	$page = (int) (!isset($_GET["page"]) ? 1 : $_GET["page"]);
    	$limit = 10;
    	$startpoint = ($page * $limit) - $limit;	?>
<!doctype html>
<html lang="en"><?php include 'header.php'; ?>
<body>    
<?php include 'horz_nav.php'; ?>
	<!-- end of secondary bar -->
	<?php include 'nav.php'; ?>
<!-- end of sidebar -->
	
	<section id="main" class="column">
		
			
		<!-- end of stats article --><!-- end of content manager article -->
		<?php include 'update.php'; ?>
    <!-- end of messages article -->
		
		<div class="clear"></div>
		
		<!-- end of post new article -->
		
		<div class="spacer"></div>
	</section>


</body>

</html>