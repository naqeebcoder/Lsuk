<?php if(session_id() == '' || !isset($_SESSION)){session_start();} error_reporting(0); ?> 
<?php include 'db.php';include_once ('function.php');$comptype=@$_GET['comptype']; $org=@$_GET['org']; $city=@$_GET['city'];
    	$page = (int) (!isset($_GET["page"]) ? 1 : $_GET["page"]);
    	$limit = 20;
    	$startpoint = ($page * $limit) - $limit;	?>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<title>Company Edited History</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<link rel="stylesheet" href="css/bootstrap.css">
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
</head>
<body>    
	
	<section id="main" class="column">
				<article class="module width_full">
		<header><h3 class="tabs_involved"><a href="<?php echo basename(__FILE__);?>">Registered list of Companies - Edited Track of <span style="color:#F00;"> (<?php echo $_GET['view_id']; ?>)</</span></a></h3>
		</header>
		

		<div class="tab_container">
			<div id="tab1" class="tab_content" style="overflow-x:scroll">
			<table class="table table-bordered table-hover" cellspacing="0" width="100%"> 
			<thead class="bg-info"> 
				<tr>
				    <th>Comp Name</th>
				    <th>Contact Person</th> 
                    <th>Company Type</th> 
    				<th>Phone#</th> 
   				    <th>Email</th> 
                    <th>City</th> 
                    <th>Address</th> 
                    <th>Submitted By</th>
                    <th>Status</th> 
                  	<th>Edited by</th> 
                  	<th>Deleted by</th> 
				</tr> 
			</thead> 
			<tbody> 
      <?php $table='hist_comp_reg';$view_id=@$_GET['view_id'];
	   $query="SELECT  *  FROM $table
	   where $table.id=$view_id 
	    LIMIT {$startpoint} , {$limit}";			
			$result = mysqli_query($con,$query);
			$mapCols["id"]=null;
			while($row = mysqli_fetch_array($result)){?>            
				<tr>

				<?php 
				  	$htm=$row['name'];
					EchoColData($mapCols,'name',$htm); 
					$mapCols["name"]=$htm;
				?>
				<?php 
				  	$htm=$row['contactPerson'];
					EchoColData($mapCols,'contactPerson',$htm); 
					$mapCols["contactPerson"]=$htm;
				?>
				<?php 
				  	$htm=$row['compType'];
					EchoColData($mapCols,'compType',$htm); 
					$mapCols["compType"]=$htm;
				?>
				<?php 
				  	$htm=$row['contactNo1'];
					EchoColData($mapCols,'contactNo1',$htm); 
					$mapCols["contactNo1"]=$htm;
				?>
				<?php 
				  	$htm=$row['email'];
					EchoColData($mapCols,'email',$htm); 
					$mapCols["email"]=$htm;
				?>
				<?php 
				  	$htm=$row['city'];
					EchoColData($mapCols,'city',$htm); 
					$mapCols["city"]=$htm;
				?>
				<?php 
				  	$htm=$row['buildingName'].$row['line1'].$row['streetRoad'];
					EchoColData($mapCols,'aloct_by',$htm); 
					$mapCols["buildingName"]=$htm;
				?>
				<?php 
				  	$htm=$row['sbmtd_by'];
					EchoColData($mapCols,'sbmtd_by',$htm); 
					$mapCols["sbmtd_by"]=$htm;
				?>
				<?php 
				  	$htm=$row['status'];
					EchoColData($mapCols,'status',$htm); 
					$mapCols["status"]=$htm;
				?>
				<?php 
				  	$htm=$row['edited_by'].'<br/>'.$row['edited_date'];
					EchoColData($mapCols,'edited_by',$htm); 
					$mapCols["edited_by"]=$htm;
				?>
				<?php 
				if ($row["deleted_flag"]!=0)
				{
				  	$htm=$row['deleted_by'].'<br/>'.$row['deleted_date'];
					EchoColData($mapCols,'deleted_by',$htm); 
					$mapCols["deleted_by"]=$htm;
				}
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

function EchoColData($mapref,$strCol,$htm)
{
	$ec="<td";
	if (isset($mapref[$strCol]) && $mapref[$strCol]!=$htm)
	{
		//$ec.=" style='color:#ff5536;background-color:yellow;'";
		$ec.=" style='background-color:yellow;'";
	}
	$ec.=">".$htm."</td>";
	//$mapref[$strCol]=$htm;

	//return $ec;
	echo $ec;
}

?>
