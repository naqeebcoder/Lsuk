<?php include 'db.php';include_once ('function.php');$name=@$_GET['name']; $month1=date('m');$to=@$_GET['to'];$from=@$_GET['from'];
    	$page = (int) (!isset($_GET["page"]) ? 1 : $_GET["page"]);
    	$limit = 20;
    	$startpoint = ($page * $limit) - $limit;	?>
<!doctype html>
<html lang="en">

<script>
function myFunction() {
	 var x = document.getElementById("name").value;if(!x){x="<?php echo $name; ?>";}
	 var y = document.getElementById("from").value;if(!y){y="<?php echo $from; ?>";}
	 var z = document.getElementById("to").value;if(!z){z="<?php echo $to; ?>";}
	 window.location.href="trans_work_list.php" + '?name=' + x + '&from=' + y + '&to=' + z;
	 
}
</script>
<body>

	<!--<header id="header">
		<hgroup>
			<h1 class="site_title"><a href="index.html">Website Admin</a></h1>
			<h2 class="section_title">Dashboard</h2><div class="btn_view_site"><a href="http://www.medialoot.com">View Site</a></div>
		</hgroup>
	</header>--> <!-- end of header bar -->
	<?php include 'header.php'; ?>
	<!-- end of secondary bar -->
	<?php include 'nav.php'; ?>
<!-- end of sidebar -->
	
	<section id="main" class="column"><!-- end of stats article -->
				<article class="module width_full">
		<header><h3 class="tabs_involved" style="width:230px;">Salary List(Trans.)</h3>
         <div align="center" style=" width:60%; margin-left:400px;margin-top:10px;">
        <input type="text" name="name" id="name" placeholder='Name' style="border-radius: 5px;" onChange="myFunction()" value="<?php echo $name; ?>" /> |
        <input type="date" name="from" id="from" placeholder='From' style="border-radius: 5px;" onChange="myFunction()" value="<?php echo $from; ?>"/> |
        <input type="date" name="to" id="to" placeholder='To' style="border-radius: 5px;" onChange="myFunction()" value="<?php echo $to; ?>" /></div>

		</header>
		

		<div class="tab_container">
			<div id="tab1" class="tab_content">
			<table class="tablesorter" cellspacing="0" width="100%"> 
			<thead> 
				<tr>
				  <th>Name</th>
				  <th>Gender</th> 
    				<th>Interpreter</th> 
   				  <th>Ph Interp</th> 
   				  <th>Translation</th> 
                    <th>City</th> 
                  <th>Contact No</th>
                    <th>Email</th>
    				<th width="120" align="center">Actions</th> 
				</tr> 
			</thead> 
			<tbody> 
      <?php $table='interpreter_reg';
	   if(!empty($name) || !empty($from)|| !empty($to)){
	 $query="SELECT $table.* FROM $table 
	 JOIN translation ON $table.id=translation.intrpName
	   where (translation.asignDate between '$from' and '$to') and translation.commit=1 and translation.intrp_salary_comit=0 or ($table.name='$name')
	   LIMIT {$startpoint} , {$limit}";	}
	   else{$query="SELECT $table.* FROM $table 
		JOIN translation ON $table.id=translation.intrpName
		where translation.commit=1 and translation.intrp_salary_comit=0  LIMIT {$startpoint} , {$limit}";	}
			$result = mysqli_query($con,$query);
			while($row = mysqli_fetch_array($result)){?>            
				<tr>
				  <td><?php echo $row['name']; ?></td>
				  <td><?php echo $row['gender']; ?></td> 
   					<td><?php echo $row['interp']; ?></td> 
    				<td><?php echo $row['telep']; ?></td> 
    				<td><?php echo $row['trans']; ?></td> 
                    <td><?php echo $row['city']; ?></td> 
    				<td><?php echo $row['contactNo']; ?></td>
    				<td><?php echo $row['email']; ?></td>    				
    				<td align="center">
                    <a href="#" onClick="MM_openBrWindow('interp_data_view.php?view_id=<?php echo $row['id']; ?>&table=<?php echo $table; ?>','_blank','scrollbars=yes,resizable=yes,width=900,height=650')">
    				  <input type="image" src="images/icn_new_article.png" title="View Details">
                     
                     <a href="#" onClick="MM_openBrWindow('salary_query.php?salary_id=<?php echo $row['id']; ?>','_blank','scrollbars=yes,resizable=yes,width=900,height=650')"><input type="image" src="images/paid.png" title="Salary Slip"></a>
                    
                     <a href="#" onClick="MM_openBrWindow('salary_query_paid.php?salary_id=<?php echo $row['id']; ?>','_blank','scrollbars=yes,resizable=yes,width=900,height=650')"><input type="image" src="images/undo.png" title="Undo Salary"></a>
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