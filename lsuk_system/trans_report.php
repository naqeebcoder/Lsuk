<?php include 'db.php';include_once ('function.php');$org_name=@$_GET["org_name"];	?>
<!doctype html>
<html lang="en">

	<link rel="stylesheet" href="css/layout.css" type="text/css" media="screen" />

<body>

	<!--<header id="header">
		<hgroup>
			<h1 class="site_title"><a href="index.html">Website Admin</a></h1>
			<h2 class="section_title">Dashboard</h2><div class="btn_view_site"><a href="http://www.medialoot.com">View Site</a></div>
		</hgroup>
	</header>--> <!-- end of header bar -->
	<?php //include 'header.php'; ?>
	<!-- end of secondary bar -->
	<?php //include 'nav.php'; ?>
<!-- end of sidebar -->
	
	<section id="main" class="column"><!-- end of stats article -->
				<article class="module width_full">
		<header><h3 class="tabs_involved">Translation Interpreter booking list</h3>
          <ul class="tabs">
          <li><a href="index.php">Home |</a></li>
            <li><a href="interp_report.php">Interpreter</a></li>
            <li><a href="telep_report.php">Telephone</a></li>
            <li><a href="trans_report.php">Translation</a></li>
          </ul>
        </header>
		

		<div class="tab_container">
			<div id="tab1" class="tab_content">
			<table class="tablesorter" cellspacing="0" width="100%"> 
			<thead> 
				<tr>
                	<th>Sr. #</th>
                	<th>Invoice #</th>
				  	<th>Source</th>
    				<th>Company Name</th> 
    				<th>Contact Name</th> 
    				<th>Interpreter Charges</th> 
                    <th>Comp Charges</th>
                </tr> 
			</thead> 
			<tbody> 
            <!--((interpreter.rateHour * interpreter.hoursWorkd)+(interpreter.travelTimeHour * interpreter.travelTimeRate)+(interpreter.travelTimeHour * interpreter.travelTimeRate)+(interpreter.otherCost))* 0.2;-->
      <?php $table='translation'; $i=1;
	   $query="SELECT translation.* FROM translation";			
			$result = mysqli_query($con,$query);
			while($row = mysqli_fetch_array($result)){ ?>   
                     
				<tr>
                	<td><?php echo $i; ?></td>
                	<td><?php echo $row['invoiceNo']; ?></td>
				  	<td><?php echo $row['source']; ?></td>
   					<td><?php echo $row['orgName']; ?></td> 
    				<td><?php echo $row['orgContact']; ?></td> 
    				<td><?php echo $row['total_charges_interp']; ?></td>
    				<td><?php echo $row['total_charges_comp']; ?></td> 
   				</tr> 
                <?php $i++;} ?>
                </tbody></table>                
			
		  </div><!-- end of #tab1 -->
			
			
			
		</div><!-- end of .tab_container -->
		
		</article><!-- end of content manager article --><!-- end of messages article -->
		
    <div class="clear"></div>
		
		<!-- end of post new article -->
		
		<div class="spacer"></div>
	</section>


</body>

</html>