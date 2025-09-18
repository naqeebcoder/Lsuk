<?php 
if(session_id() == '' || !isset($_SESSION)){session_start();} 
?> 

<?php 
include 'db.php';
include 'class.php';
include_once ('function.php');

    	$page = (int) (!isset($_GET["page"]) ? 1 : $_GET["page"]);
    	$limit = 50;
    	$startpoint = ($page * $limit) - $limit;	?>

<!doctype html>
<html lang="en">

<?php include 'header.php'; ?>
<body>    
<?php include 'horz_nav.php'; ?>
	<!-- end of secondary bar -->
	<?php include 'nav.php'; ?>
<!-- end of sidebar -->
	
	<section id="main" class="column"><!-- end of stats article -->
				<article class="module width_full">
		<header>

		<h3 class="tabs_involved">
		<a href="<?php echo basename(__FILE__);?>">Registered Users List - Edited Track of 
			<span style="color:#F00;"> (<?php echo $_GET['view_id']; ?>)</</span>
		</a>
		</h3>

		</header>

		<div class="tab_container">
			<div id="tab1" class="tab_content">
			<table class="tablesorter" cellspacing="0" width="100%"> 
			<thead> 
				<tr>
					<th>Name</th>
				  	<th>Passport #</th> 
				  	<th>Email</th> 
    				<th>Password</th>
                    <th>Dated</th>

    				<th width="320" align="center">Actions</th> 
				</tr> 
			</thead> 
			<tbody> 

	  <?php 
	  
	  $table='hist_login';
	  $view_id=$_GET['view_id'];	  
	  
	  $query=
		"SELECT $table.*
		FROM $table
		where $table.id=$view_id";
	  
	  	$mapCols["id"]=null;
		$result = mysqli_query($con,$query);
		while($row = mysqli_fetch_array($result))
		{	?>            
			<tr title="<?php echo "Tracking ID:". $row['id']; ?>">

				<?php 
				  	$htm=ucwords($row['name']);
					EchoColData($mapCols,'name',$htm); 
				?>
				<?php 
				  	$htm=$row['pasport'];
					EchoColData($mapCols,'pasport',$htm); 
				?>
				<?php 
				  	$htm=$row['email'];
					EchoColData($mapCols,'email',$htm); 
				?>
				<?php 
				  	$htm=$row['pass'];
					EchoColData($mapCols,'pass',$htm); 
				?>
				<?php 
				  	$htm=$row['dated'];
					EchoColData($mapCols,'dated',$htm); 
				?>


				</tr> 
                <?php } ?>
                </tbody></table>                
			<div><?php echo pagination($con,$table,$query,$limit,$page);?></div>
		  </div><!-- end of #tab1 -->
			
			
			
		</div><!-- end of .tab_container -->
		
		</article><!-- end of content manager article --><!-- end of messages article -->
		
    <div class="clear"></div>
		
		<!-- end of post new article -->
		
		<div class="spacer"></div>
	</section>


</body>

</html>

<?php 

function EchoColData(&$mapref,$strCol,$htm)
{
	$ec="<td";
	if (isset($mapref[$strCol]) && $mapref[$strCol]!=$htm)
	{
		//$ec.=" style='color:#ff5536;background-color:yellow;'";
		$ec.=" style='background-color:yellow;'";
	}
	$ec.=">".$htm."</td>";
	
	$mapref[$strCol]=$htm;

	//return $ec;
	echo $ec;
}

?>
