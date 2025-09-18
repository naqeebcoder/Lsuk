<?php if(session_id() == '' || !isset($_SESSION)){session_start();} error_reporting(0); ?> 
<?php include 'db.php';include 'class.php';include_once ('function.php');$name=@$_GET['name']; $gender=@$_GET['gender']; $city=@$_GET['city'];$view_id=@$_GET['view_id'];
    	$page = (int) (!isset($_GET["page"]) ? 1 : $_GET["page"]);
    	$limit = 20;
    	$startpoint = ($page * $limit) - $limit;	?>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<title>Employees Edited History</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<link rel="stylesheet" href="css/bootstrap.css">
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
</head>
<body>    
	
	<section id="main" class="column">
				<article class="module width_full">
		<header><h3 class="tabs_involved"><a href="<?php echo basename(__FILE__);?>">Employees list - Edited Track of <span style="color:#F00;"> (<?php echo $_GET['view_id']; ?>)</</span></a></h3>
		</header>
		

		<div class="tab_container">
			<div id="tab1" class="tab_content" style="overflow-x:scroll">
			<table class="table table-bordered table-hover" cellspacing="0" width="100%"> 
			<thead class="bg-info"> 
				<tr>
				  <th>Name</th>
				  <th>Designation</th>
   				  <th>Job Type</th> 
				  <th>Gender</th> 
   				  <th>Contact No</th> 
                    <th>City</th> 
                  	<th>Edited by</th> 
    				<th width="210" align="center">Actions</th> 
				</tr> 
			</thead> 
			<tbody> 
      <?php $table='hist_emp';  $pasport=$_SESSION['pasport'];
	  if($_SESSION['prv']=='Management'){		
	 	$query="SELECT * FROM $table 
	   where $table.id=$view_id and $table.deleted_flag=0  and name like '$name%' and gender like '$gender%' and city like'$city%'
	   LIMIT {$startpoint} , {$limit}";	}
	   else{ $query="SELECT * FROM $table 
	   where $table.id=$view_id and $table.deleted_flag=0  and passp = '$pasport' and name like '$name%' and gender like '$gender%' and city like'$city%'
	   LIMIT {$startpoint} , {$limit}";}
			$result = mysqli_query($con,$query);
			while($row = mysqli_fetch_array($result)){?>            
				<tr>
				  <td><?php echo $row['name']; ?></td>
				  <td><?php echo $row['desig']; ?></td> 
    				<td><?php echo $row['jt']; ?></td> 
				  <td><?php echo $row['gender']; ?></td> 
   					<td><?php echo $row['contact']; ?></td>
                    <td><?php echo $row['city']; ?></td> 		
    				<td align="center"><?php echo $row['edited_by']; ?><br/><?php echo $row['edited_date']; ?>		
    				<td align="center">
                    <?php if($_SESSION['prv']=='Management'){?>
                    <a href="#" onClick="MM_openBrWindow('employee_view_edited.php?view_id=<?php echo $row['id']; ?>&table=<?php echo $table; ?>&edited_date=<?php echo $row['edited_date']; ?>','_blank','scrollbars=yes,resizable=yes,width=900,height=650')">
    				  <input type="image" src="images/icn_new_article.png" title="View Details">
    				  </a>
                    <?php } ?>
                    </td> 
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