<?php if(session_id() == '' || !isset($_SESSION)){session_start();} error_reporting(0); ?> 
<?php include 'db.php';include 'class.php';include_once ('function.php'); $org=@$_GET['org'];$view_id=@$_GET['view_id'];
    	$page = (int) (!isset($_GET["page"]) ? 1 : $_GET["page"]);
    	$limit = 100;
    	$startpoint = ($page * $limit) - $limit;	?>
<!doctype html>
<html lang="en">
<script>
function myFunction() {
	 var y = document.getElementById("org").value;if(!y){y="<?php echo $org; ?>";}
	 window.location.href="credit_list_full.php" + '?org=' + y ;
	 
}
</script>
<?php include 'header.php'; ?>
<body>    
<?php include 'horz_nav.php'; ?>
	<!-- end of secondary bar -->
	<?php include 'nav.php'; ?>
<!-- end of sidebar -->
	
	<section id="main" class="column"><!-- end of stats article -->
				<article class="module width_full">
		<header><h3 class="tabs_involved"><a href="<?php echo basename(__FILE__);?>">Credit Full List- Edited Track of <span style="color:#F00;"> (<?php echo $_GET['view_id']; ?>)</</span></a></h3>
   
		</header>
		

		<div class="tab_container">
			<div id="tab1" class="tab_content">
			<table class="tablesorter" cellspacing="0" width="100%"> 
			<thead> 
				<tr>
    				<th>Mode</th> 
				  	<th>Purchase Order #</th>
				  	<th>Invoice No.</th>
				  	<th>Comp Name</th>
				  	<th>Credit</th> 
      				<th>Credit Date</th> 
    				<th>Debit</th> 
   				  	<th>Debit Date</th> 
                  	<th>Dated</th> 
                  	<th>Edited by</th> 
   				</tr> 
			</thead> 
			<tbody> 
      <?php $table='hist_comp_credit';
	   $query="SELECT  $table.id,$table.porder,$table.mode,$table.invoiceNo,$table.orgName,$table.credit,$table.credit_date,$table.debit debit,$table.debit_date,$table.dated, $table.edited_by,$table.edited_date FROM $table
	   LEFT JOIN comp_reg ON $table.orgName=comp_reg.abrv
	   where $table.id=$view_id and $table.orgName like '$org%'
	   
	    LIMIT {$startpoint} , {$limit}";			
			$result = mysqli_query($con,$query);
			while($row = mysqli_fetch_array($result)){$comp_name=$acttObj->unique_data('comp_reg','name','abrv',$row['orgName']);?>            
				<tr>
				  <td><?php echo ucwords($row['mode']); ?></td>
				  <td><?php echo $row['porder']; ?></td>
				  <td><?php echo $row['invoiceNo']; ?></td>
				  <td><?php echo $comp_name; ?></td>
				  <td><?php echo round($row['credit'],2); ?></td> 
   					<td><?php echo $misc->dated($row['credit_date']); ?></td> 
   					<td><?php echo round($row['debit'],2); ?></td> 
    				<td><?php echo $misc->dated($row['debit_date']); ?></td>
    				<td><?php echo $misc->dated($row['dated']); ?></td> 
    				<td align="center"><?php echo $row['edited_by']; ?><br/><?php echo $row['edited_date']; ?>
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