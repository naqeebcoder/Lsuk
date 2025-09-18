<?php if(session_id() == '' || !isset($_SESSION)){session_start();} error_reporting(0); ?> 
<?php include 'db.php';include 'class.php';include_once ('function.php');$title=@$_GET['title'];$search_2=@$_GET['search_2'];$search_3=@$_GET['search_3'];$view_id=@$_GET['view_id'];
    	$page = (int) (!isset($_GET["page"]) ? 1 : $_GET["page"]);
    	$limit = 20;
    	$startpoint = ($page * $limit) - $limit;	?>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<title>Expenses Edited History</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<link rel="stylesheet" href="css/bootstrap.css">
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
</head>
<body>    
	
	<section id="main" class="column">
				<article class="module width_full">
		<header><h3 class="tabs_involved"><a href="<?php echo basename(__FILE__);?>">Expense list - Edited Track of <span style="color:#F00;"> (<?php echo $_GET['view_id']; ?>)</</span></a></h3>
		</header>
		

		<div class="tab_container">
			<div id="tab1" class="tab_content" style="overflow-x:scroll">
			<table class="table table-bordered table-hover" cellspacing="0" width="100%"> 
			<thead class="bg-info"> 
				<tr>
				  <th>Title</th>
				  <th>Amount </th>
   				  <th>Details </th>
   				  <th>Voucher #</th>
   				  <th>Company</th> 
				  <th>Bill Date</th> 
                  	<th>Edited by</th> 
				</tr> 
			</thead> 
			<tbody> 
      <?php $table='hist_expence';
	   $query="SELECT * FROM $table 
	   where  $table.id=$view_id and $table.deleted_flag=0
	   LIMIT {$startpoint} , {$limit}";
			$result = mysqli_query($con,$query);
			while($row = mysqli_fetch_array($result)){?>            
				<tr>
				  <td><?php echo $row['title']; ?></td>
				  <td><?php echo $row['amoun']; ?></td> 
    				<td><?php echo $row['details']; ?></td>
    				<td><?php echo $row['voucher']; ?></td>
    				<td><?php echo $row['comp']; ?></td> 
				  <td><?php echo $misc->dated($row['billDate']); ?></td> 		
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