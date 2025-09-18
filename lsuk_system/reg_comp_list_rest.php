<?php if(session_id() == '' || !isset($_SESSION)){session_start();} ?> 
<?php include 'db.php';include 'class.php';include_once ('function.php');$comptype=@$_GET['comptype']; $org=@$_GET['org']; $city=@$_GET['city'];
    	$page = (int) (!isset($_GET["page"]) ? 1 : $_GET["page"]);
    	$limit = 20;
    	$startpoint = ($page * $limit) - $limit;	?>
<!doctype html>
<html lang="en">
<script>
function myFunction() {
	 var x = document.getElementById("comptype").value;if(!x){x="<?php echo $comptype; ?>";}
	 var y = document.getElementById("org").value;if(!y){y="<?php echo $org; ?>";}
	 var z = document.getElementById("city").value;if(!z){z="<?php echo $city; ?>";}
	 window.location.href="<?php echo basename(__FILE__);?>" + '?comptype=' + x + '&org=' + y + '&city=' + z;
	 
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
		<header><h3 class="tabs_involved" style="width:230px;"><a href="<?php echo basename(__FILE__);?>">Registered list of Companies</a></h3>
   <div align="center" style=" width:60%; margin-left:400px;margin-top:10px;">
        
        <select id="org" name="org" onChange="myFunction()" style="width:150px;height:25px;">
                    <?php 			
$sql_opt="SELECT name,abrv FROM comp_reg ORDER BY name ASC";
$result_opt=mysqli_query($con,$sql_opt);
$options="";
while ($row_opt=mysqli_fetch_array($result_opt)) {
    $code=$row_opt["abrv"];
    $name_opt=$row_opt["name"];
    $options.="<OPTION value='$code'>".$name_opt;}
?>
		      <?php if(!empty($org)){ ?>
		      <option><?php echo $org; ?></option>
		      <?php } else{?>
		      <option value="">--Select Org--</option>
		      <?php } ?>
                    <?php echo $options; ?>
                    </option>
                  </select> |
        <select id="comptype" name="comptype" onChange="myFunction()" style="width:150px;height:25px;">
                    <?php 			
$sql_opt="SELECT title FROM comp_type ORDER BY title ASC";
$result_opt=mysqli_query($con,$sql_opt);
$options="";
while ($row_opt=mysqli_fetch_array($result_opt)) {
    $code=$row_opt["title"];
    $name_opt=$row_opt["title"];
    $options.="<OPTION value='$code'>".$name_opt;}
?>
		      <?php if(!empty($comptype)){ ?>
		      <option><?php echo $comptype; ?></option>
		      <?php } else{?>
		      <option value="">--Select Comp Type--</option>
		      <?php } ?>
                    <?php echo $options; ?>
                    </option>
                  </select> |
        
        <select name="city" id="city" onChange="myFunction()" style="width:150px;height:25px;">
        		     
                     <?php 			
$sql_opt="SELECT city FROM cities ORDER BY city ASC";
$result_opt=mysqli_query($con,$sql_opt);
$options="";
while ($row_opt=mysqli_fetch_array($result_opt)) {
    $code=$row_opt["city"];
    $name_opt=$row_opt["city"];
    $options.="<OPTION value='$code'>".$name_opt;}
?>
		      <?php if(!empty($city)){ ?>
		      <option><?php echo $city; ?></option>
		      <?php } else{?>
		      <option value="">--Select City--</option>
		      <?php } ?>
                    <?php echo $options; ?>
                    </option>         
                  </select></div>
		</header>
		

		<div class="tab_container">
			<div id="tab1" class="tab_content">
			<table class="tablesorter" cellspacing="0" width="100%"> 
			<thead> 
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
                    <th style="color:#F00">Deleted by</th> 
    				<th width="80" align="center">Actions</th> 
				</tr> 
			</thead> 
			<tbody> 
      <?php $table='comp_reg';
	   $query="SELECT distinct *  FROM $table
	   where $table.deleted_flag=1 and  compType like '$comptype%' and abrv like '$org%' and  city like '$city%'
	   GROUP BY name
	    LIMIT {$startpoint} , {$limit}";			
			$result = mysqli_query($con,$query);
			while($row = mysqli_fetch_array($result)){?>            
				<tr>
				  <td><?php echo $row['name']; ?></td>
				  <td><?php echo $row['contactPerson']; ?></td> 
   					<td><?php echo $row['compType']; ?></td> 
   					<td><?php echo $row['contactNo1']; ?></td> 
    				<td><?php echo $row['email']; ?></td> 
    				<td><?php echo $row['city']; ?></td> 
    				<td><?php echo $row['buildingName'].$row['line1'].$row['streetRoad']; ?></td>  	
    				<td><?php echo $row['sbmtd_by']; ?></td>
    				<td><?php echo $row['status']; ?></td> 
    				<td  style="color:#F00"><?php echo $row['deleted_by'].'('.$misc->dated($row['deleted_date']).')'; ?></td>  
    				<td align="center">


<?php if($_SESSION['prv']=='Management'){?>
<a href="#" onClick="MM_openBrWindow('trash_restore.php?del_id=<?php echo $row['id']; ?>&table=<?php echo $table; ?>','_blank','scrollbars=yes,resizable=yes,width=500,height=200')"><input type="image" src="images/icn_jump_back.png" title="Restore"></a>

<a href="#" onClick="MM_openBrWindow('del.php?del_id=<?php echo $row['id']; ?>&table=<?php echo $table; ?>','_blank','scrollbars=yes,resizable=yes,width=500,height=200')"><input type="image" src="images/icn_trash.png" title="Delete"></a> 
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