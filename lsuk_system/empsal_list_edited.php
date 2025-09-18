<?php 
if(session_id() == '' || !isset($_SESSION)){session_start();} 
?> 

<?php 
include 'db.php';
include 'class.php';
include_once ('function.php');

/*
$assignDate=@$_GET['assignDate']; 
$interp=@$_GET['interp']; 
$org=@$_GET['org']; 
$job=@$_GET['job'];
$our=@$_GET['our'];
$ur=@$_GET['ur'];
$inov=@$_GET['inov'];
*/

    	$page = (int) (!isset($_GET["page"]) ? 1 : $_GET["page"]);
    	$limit = 50;
    	$startpoint = ($page * $limit) - $limit;	?>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<title>Employee Salary Record History</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<link rel="stylesheet" href="css/bootstrap.css">
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
</head>
<body> 
	<section id="main" class="column"><!-- end of stats article -->
				<article class="module width_full">
		<header><h3 class="tabs_involved"><a href="<?php echo basename(__FILE__);?>">Employee Salary - Edited Track of <span style="color:#F00;"> (<?php echo $_GET['view_id']; ?>)</</span></a></h3>
		</header>
		<div class="tab_container">
			<div id="tab1" class="tab_content" style="overflow-x:scroll">
			<table class="table table-bordered table-hover" cellspacing="0" width="100%"> 
			<thead class="bg-info"> 
				<tr>
    				<th>empId</th> 
    				<th>entry_date</th> 
    				<th>start</th>
    				<th>finish</th>
    				<th>duration</th>
    				<th>salary</th>
    				<th>rph</th>
                    <th>sbmtd_by</th> 
    				<th width="320" align="center">Actions</th> 
				</tr> 
			</thead> 
			<tbody> 

	  <?php 
	  
	  //$table='hist_emp';
	  $table='hist_rolcal';

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
				  	$htm=$row['empId'];
					EchoColData($mapCols,'empId',$htm); 
					//$mapCols["name"]=$htm;
				?>
				<?php 
				  	$htm=$row['entry_date'];
					EchoColData($mapCols,'entry_date',$htm); 
				?>
				<?php 
				  	$htm=$row['start'];
					EchoColData($mapCols,'start',$htm); 
				?>
				<?php 
				  	$htm=$row['finish'];
					EchoColData($mapCols,'finish',$htm); 
				?>
				<?php 
				  	$htm=$row['duration'];
					EchoColData($mapCols,'duration',$htm); 
				?>
				<?php 
				  	$htm=$row['salary'];
					EchoColData($mapCols,'salary',$htm); 
				?>
				<?php 
				  	$htm=$row['rph'];
					EchoColData($mapCols,'rph',$htm); 
				?>
				
				<?php 
				  	$htm=$row['sbmtd_by'].'<br/>'.$row['dated'];
					EchoColData($mapCols,'sbmtd_by',$htm); 
				?>

				<?php
    			/*<td align="center">
					<a href="#" onClick="MM_openBrWindow('interp_view_edited.php?view_id=<?php echo $row['id']; ?>&table=<?php echo $table; ?>&edited_date=<?php echo $row['edited_date']; ?>','_blank','scrollbars=yes,resizable=yes,width=900,height=650')">
						<input type="image" src="images/icn_new_article.png" title="View Edited Record"></a>
				</td> */
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
